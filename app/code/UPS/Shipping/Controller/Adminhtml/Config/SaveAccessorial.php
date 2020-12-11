<?php
/**
 * SaveAccessorial file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Controller\Adminhtml\Config;

/**
 * SaveAccessorial class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class SaveAccessorial extends \Magento\Framework\App\Action\Action
{
    protected $configWriter;
    protected $cacheTypeList;
    protected $modelAccessorial;
    protected $checkoutSession;
    protected $apiManager;
    protected $scopeConfig;
    protected $licenseModel;
    protected $apiHandshake;
    protected $apiAccount;
    /**
     * SaveAccessorial __construct
     *
     * @param string $context          //The context
     * @param string $configWriter     //The configWriter
     * @param string $modelAccessorial //The modelAccessorial
     * @param string $checkoutSession  //The checkoutSession
     * @param string $apiManager       //The apiManager
     * @param string $cacheTypeList    //The cacheTypeList
     * @param string $scopeConfig      //The scopeConfig
     * @param string $modelLicense     //modelLicense
     * @param string $apiHandshake     //The apiHandshake
     * @param string $apiAccount       //The apiAccount
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \UPS\Shipping\Model\Accessorial $modelAccessorial,
        \Magento\Checkout\Model\Session $checkoutSession,
        \UPS\Shipping\API\Manager\Index $apiManager,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \UPS\Shipping\Model\License $modelLicense,
        \UPS\Shipping\API\Handshake $apiHandshake,
        \UPS\Shipping\API\Account $apiAccount
    ) {
        $this->configWriter = $configWriter;
        $this->modelAccessorial = $modelAccessorial;
        $this->cacheTypeList = $cacheTypeList;
        $this->checkoutSession = $checkoutSession;
        $this->scopeConfig = $scopeConfig;
        $this->apiManager = $apiManager;
        $this->licenseModel = $modelLicense;
        $this->apiHandshake = $apiHandshake;
        $this->apiAccount = $apiAccount;
        parent::__construct($context);
    }
    /**
     * SaveAccessorial execute
     *
     * @return null
     */
    public function execute()
    {
        $dataForm = $this->getRequest()->getParams();

        $listAssessorial = $this->modelAccessorial->getListAccessorial();
        foreach ($listAssessorial as $row) {
            $showShipping = (isset($dataForm[$row['accessorial_key']])) ? 1 : 0;
            $this->modelAccessorial->updateAccessorial($row['id'], $showShipping);
        }
        // offPluginManager 2019-03-18
        // call plugin manager
        $checkTransferExist = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_PLUGIN_MERCHANTINFO_EXIST);
        if ($checkTransferExist == '1') {
            $this->callTransferAccessorial();
        }
        //end call plugin manager
        $this->configWriter->save(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_ACCESSORIAL, 1);
        foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        $this->_redirect('upsshipping/config/pkgdimension');
    }

    /**
     * Shipmentdetail getPackageStatus
     *
     * @return string $returnDataAPI
     */
    public function callTransferAccessorial()
    {
        $arrReturnAccessorial = $this->modelAccessorial->getListAccessorialActive();
        $arrReturnAccessorial = json_decode($arrReturnAccessorial, true);
        // Accessorial
        $arrAccessorial = [];
        $arrayAccessorialKey = array_keys($arrReturnAccessorial);
        if (!empty($arrayAccessorialKey)) {
            foreach ($arrayAccessorialKey as $key) {
                $arrAccessorial[] = [
                    'accessorial_key' => $key,
                    'accessorial_name' => $arrReturnAccessorial[$key]
                ];
            }
            $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
            if (empty($bearerToken)) {
                // re-RegisterToken
                if ($this->resetRegisteredToken()) {
                    $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                    $bearerSessionToken = $this->checkoutSession->getBearLongToken();
                    if ($bearerSessionToken != $bearerToken) {
                        $bearerToken = $bearerSessionToken;
                    }
                }
            }
            // call API
            if (!empty($bearerToken)) {
                $dataTransferAccessorial = [
                    'dataAccessorial' => $arrAccessorial,
                    'merchantKey' => $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY),
                    \UPS\Shipping\Helper\ConstantManager::BEARERTOKEN => $bearerToken
                ];
                $responseApi = $this->apiManager->callTransferAccessorials($dataTransferAccessorial, 1);
                $responseApi = json_decode($responseApi);
                // bearerToken expired
                if (isset($responseApi->error->errorCode) && $responseApi->error->errorCode == '401') {
                    if ($this->resetRegisteredToken()) {
                        $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                        $bearerSessionToken = $this->checkoutSession->getBearLongToken();
                        if ($bearerSessionToken != $bearerToken) {
                            $bearerToken = $bearerSessionToken;
                        }
                        $dataTransferAccessorial[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN] = $bearerToken;
                        $this->apiManager->callTransferAccessorials($dataTransferAccessorial, 1);
                    }
                }
            }
        }
    }

    /**
     * Index setRegisteredToken
     *
     * @return string $trackingStatus
     */
    public function resetRegisteredToken()
    {
        $licenseDefault = $this->licenseModel->getLicenseDefault();
        $resetRegisterToken = false;
        $websiteMerchant = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_WEBSECUREURL);
        $valueSecurityToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN);
        if (empty($valueSecurityToken)) {
            foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
                $this->cacheTypeList->cleanType($type);
            }
            $valueSecurityToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN);
        }
        $arrLicenseParams = [
            "MerchantKey" => $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY),
            "WebstoreUrl" => $websiteMerchant,
            "WebstoreUpsServiceLinkSecurityToken" => $valueSecurityToken,
            "WebstorePlatform" => 'Magento',
            "WebstorePlatformVersion" => \UPS\Shipping\Helper\Config::VERSION_FLATFORM,
            "UpsReadyPluginName" => \UPS\Shipping\Helper\Config::UPS_SHIPPING_MODULE,
            "UpsReadyPluginVersion" => \UPS\Shipping\Helper\Config::VERSION_PLUGIN,
            "WebstoreUpsServiceLinkUrl" => $websiteMerchant . \UPS\Shipping\Helper\Config::API_URL,
            "Username" => $licenseDefault['Username'],
            "Password" => $licenseDefault['Password'],
            "AccessLicenseNumber" => $licenseDefault['AccessLicenseNumber']
        ];
        // Long bearer token
        $responseLongToken = $this->apiHandshake->registeredPluginToken($arrLicenseParams);
        if ($responseLongToken) {
            $responseLongToken = json_decode($responseLongToken);
            // save long token
            $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN,  $responseLongToken->data);
            $this->checkoutSession->setBearLongToken($responseLongToken->data);
            // save UPS_BING_MAPS_KEY
            $responseUpsBingMapsKey = $this->apiAccount->getUpsBingMapsKey($responseLongToken->data);
            if ($responseUpsBingMapsKey) {
                $responseUpsBingMapsKey = json_decode($responseUpsBingMapsKey);
                // save long token
                $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_BING_MAPS_KEY,  $responseUpsBingMapsKey->data);
            }
            $resetRegisterToken = true;
        }

        foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        return $resetRegisterToken;
    }
}
