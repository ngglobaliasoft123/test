<?php
/**
 * ServiceOption file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Block\Frontend\Checkout;
/**
 * ServiceOption class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class ServiceOption extends \Magento\Framework\View\Element\Template
{
    protected $carrierUPS;
    protected $checkoutSession;
    protected $scopeConfig;
    protected $modelLicense;
    protected $apiHandshake;
    protected $apiAccount;
    protected $configWriter;
    protected $cacheTypeList;

    /**
     * ServiceOption __construct
     *
     * @param string $context         //The context
     * @param string $carrierModel    //The carrierModel
     * @param string $checkoutSession //The checkoutSession
     * @param string $modelLicense    //The modelLicense
     * @param string $apiHandshake    //The apiHandshake
     * @param string $apiAccount      //The apiAccount
     * @param string $configWriter    //The configWriter
     * @param string $cacheTypeList   //The cacheTypeList
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \UPS\Shipping\Model\Carrier $carrierModel,
        \Magento\Checkout\Model\Session $checkoutSession,
        \UPS\Shipping\Model\License $modelLicense,
        \UPS\Shipping\API\Handshake $apiHandshake,
        \UPS\Shipping\API\Account $apiAccount,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
    ) {
        parent::__construct($context);
        $this->carrierUPS = $carrierModel;
        $this->checkoutSession = $checkoutSession;
        $this->scopeConfig = $context->getScopeConfig();
        $this->apiHandshake = $apiHandshake;
        $this->apiAccount = $apiAccount;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->licenseModel = $modelLicense;
    }

    /**
     * ServiceOption isActiveUPSShipping
     * function check is Active UPS Shipping
     *
     * @return array $data
     */
    public function isActiveUPSShipping()
    {
        return $this->carrierUPS->isActive();
    }

    /**
     * ServiceOption getFlagShippingFinish
     * function get Flag Shipping Finish
     *
     * @return array $data
     */
    public function getFlagShippingFinish()
    {
        return $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTINFO_EXIST);
    }

    /**
     * ServiceOption getBingMapKey
     * function get the Bing Map Key
     *
     * @return array $data
     */
    public function getBingMapKey()
    {
        $bingmapKey = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_BING_MAPS_KEY);
        if (empty($bingmapKey)) {
            if ($this->callAPIHandshake() && $this->resetRegisteredToken()) {
                $bingmapKey = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_BING_MAPS_KEY);
                $sessionBingMapKey = $this->checkoutSession->getUpsBingMapsKey();
                if ($bingmapKey != $sessionBingMapKey) {
                    $bingmapKey = $sessionBingMapKey;
                }
            }
        }
        return $bingmapKey;
    }

    /**
     * Country isCountryCode
     *
     * @return array $data
     */
    public function isCountryCode()
    {
        return $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE);
    }

    /**
     * AbstractController setRegisteredToken
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
            $valueSecurityToken = $this->checkoutSession->getSecurityTokenValue();
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
                $this->checkoutSession->setUpsBingMapsKey($responseUpsBingMapsKey->data);
            }
            $resetRegisterToken = true;
        }

        foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        return $resetRegisterToken;
    }

    /**
     * TermCondition callAPILicense
     *
     * @return array $data
     */
    public function callAPIHandshake()
    {
        $resultCallHandshakeApi = false;
        $getMerchantKey = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY);
        $websiteMerchant = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_WEBSECUREURL);
        $valueSecurityToken = $this->apiHandshake->generatePass(32);
        $this->checkoutSession->setHandshakeKey($valueSecurityToken);
        if ($this->apiHandshake->callAPIHandshake($websiteMerchant, $getMerchantKey, $valueSecurityToken)) {
            $this->checkoutSession->setSecurityToken($valueSecurityToken);
            $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN, $valueSecurityToken);
            $this->checkoutSession->setSecurityTokenValue($valueSecurityToken);
            foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
                $this->cacheTypeList->cleanType($type);
            }
            $resultCallHandshakeApi = true;
        }
        return $resultCallHandshakeApi;
    }
}
