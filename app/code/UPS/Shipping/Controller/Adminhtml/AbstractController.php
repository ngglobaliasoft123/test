<?php
/**
 * AbstractController file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Controller\Adminhtml;
/**
 * AbstractController class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
abstract class AbstractController extends \Magento\Framework\App\Action\Action
{
    protected $pageFactory;
    protected $cacheManager;
    protected $eventManager;
    protected $authSession;
    protected $url;
    protected $modelTracking;
    protected $modelOrder;
    protected $accountModel;
    protected $apiManager;
    protected $resultJsonFactory;
    protected $modelAPI;
    protected $modelShipment;
    protected $request;
    protected $formKey;
    protected $apiHandshake;
    protected $apiAccount;
    protected $scopeConfig;
    protected $configWriter;
    protected $cacheTypeList;
    protected $licenseModel;
    protected $setup;
    protected $userModel;
    protected $apiLicense;
    protected $storeManager;
    protected $modelAccessorial;
    protected $modelPackage;
    protected $modelAccount;
    protected $modelService;
    protected $modelDeliveryRates;
    protected $checkoutSession;
    protected $versionModule;

    const UPS_SHIPPING_RESOURCE = "UPS_Shipping::shipping";

    /**
     * AbstractController __construct
     *
     * @param string $context            //The context
     * @param string $pageFactory        //The pageFactory
     * @param string $resultJsonFactory  //The resultJsonFactory
     * @param string $authSession        //The authSession
     * @param string $modelShipment      //The modelShipment
     * @param string $modelOrder         //The modelOrder
     * @param string $apiManager         //The apiManager
     * @param string $modelAPI           //The modelAPI
     * @param string $trackingModel      //The trackingModel
     * @param string $accountModel       //The accountModel
     * @param string $formKey            //The formKey
     * @param string $scopeConfig        //The scopeConfig
     * @param string $apiHandshake       //The apiHandshake
     * @param string $apiAccount         //The apiAccount
     * @param string $request            //The request
     * @param string $configWriter       //The configWriter
     * @param string $cacheTypeList      //The cacheTypeList
     * @param string $modelLicense       //modelLicense
     * @param string $setup              //The setup
     * @param string $userModel          //The userModel
     * @param string $apiLicense         //The apiLicense
     * @param string $storeManager       //The storeManager
     * @param string $modelAccessorial   //The modelAccessorial
     * @param string $modelPackage       //The modelPackage
     * @param string $modelAccount       //The modelAccount
     * @param string $modelService       //The modelService
     * @param string $modelDeliveryRates //The modelDeliveryRates
     * @param string $checkoutSession    //The checkoutSession
     *
     * @return string $dataRequestShip
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \UPS\Shipping\Model\Shipment $modelShipment,
        \UPS\Shipping\Model\Order $modelOrder,
        \UPS\Shipping\API\Manager\Index $apiManager,
        \UPS\Shipping\Model\Savelog $modelAPI,
        \UPS\Shipping\Model\Tracking $trackingModel,
        \UPS\Shipping\Model\Account $accountModel,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \UPS\Shipping\API\Handshake $apiHandshake,
        \UPS\Shipping\API\Account $apiAccount,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \UPS\Shipping\Model\License $modelLicense,
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\User\Model\User $userModel,
        \UPS\Shipping\API\License $apiLicense,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \UPS\Shipping\Model\Accessorial $modelAccessorial,
        \UPS\Shipping\Model\Package $modelPackage,
        \UPS\Shipping\Model\Account $modelAccount,
        \UPS\Shipping\Model\Service $modelService,
        \UPS\Shipping\Model\DeliveryRates $modelDeliveryRates,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->modelTracking = $trackingModel;
        $this->modelOrder = $modelOrder;
        $this->authSession = $authSession;
        $this->pageFactory = $pageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->modelAPI = $modelAPI;
        $this->url = $context->getUrl();
        $this->modelShipment = $modelShipment;
        $this->apiManager = $apiManager;
        $this->accountModel = $accountModel;
        $this->request = $request;
        $this->formKey = $formKey;
        $this->apiHandshake = $apiHandshake;
        $this->apiAccount = $apiAccount;
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->licenseModel = $modelLicense;
        $this->setup = $setup;
        $this->userModel = $userModel;
        $this->apiLicense = $apiLicense;
        $this->storeManager = $storeManager;
        $this->modelAccessorial = $modelAccessorial;
        $this->modelPackage = $modelPackage;
        $this->modelAccount = $modelAccount;
        $this->modelService = $modelService;
        $this->modelDeliveryRates = $modelDeliveryRates;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    /**
     * AbstractController runCallManager
     *
     * @return array $serviceData
     */
    public function startExcute()
    {
        $this->request->setParam('form_key', $this->formKey->getFormKey());
        if (!$this->authSession->isAllowed(static::UPS_SHIPPING_RESOURCE)) {
            header('Location: ' . $this->url->getUrl('admin/index/index'));
        }
        // over 90 days which not return order yet then delete
        $this->modelOrder->deleteArchiveOrdersOver(\UPS\Shipping\Helper\Config::DAY_REMOVE_ORDER);

        // over 90 days which not return order yet then delete
        $this->modelOrder->updateOpenOrdersOver(\UPS\Shipping\Helper\Config::DAY_ARCHIVE_ORDER);

        // remove retry
        if ($this->modelAPI) {
            $this->modelAPI->removeRetryOutOfdate(\UPS\Shipping\Helper\Config::DAY_REMOVE_RETRY);
        }

        // reload retry
        $data = $this->modelAPI->getListRetry(\UPS\Shipping\Helper\Config::LIMIT_RETRY);
        $countCheck = count($data);
        if ($countCheck > 0 ) {
            $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
            $this->goResetRegisteredToken($bearerToken);
            // call API
            if (!empty($bearerToken)) {
                foreach ($data as $key => $value) {
                    $data = json_decode($value['datarequest'], true);
                    $data[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN] = $bearerToken;
                    if ($value['method'] == 'callUpdateMerchantStatus') {
                        $responseApi = $this->apiManager->callUpdateMerchantStatus($data, $value['id']);
                        $this->runExpiredToken($data, $responseApi, $value['id'], 'callUpdateMerchantStatus');
                    } else {
                        $this->runCallManager($value, $data);
                    }
                }
            }
        }

        // remove tracking number before shipment
        $arrShipmentNumbers = $this->modelShipment->getListShipmentsOver(\UPS\Shipping\Helper\Config::DAY_REMOVE_SHIPMENT);
        $listShipmentNumbers = [];
        if (!empty($arrShipmentNumbers)) {
            foreach ($arrShipmentNumbers as $item) {
                $listShipmentNumbers[] = $item['shipment_number'];
            }
        }

        if (!empty($listShipmentNumbers)) {
            // remove shipment > 90day
            return $this->modelTracking->deleteTrackingByShipmentNumber($listShipmentNumbers)
                && $this->modelShipment->removeShipment(\UPS\Shipping\Helper\Config::DAY_REMOVE_SHIPMENT);
        } else {
            return true;
        }
    }

    /**
     * AbstractController goResetRegisteredToken
     *
     * @param string $bearerToken //The bearerToken
     *
     * @return string $bearerToken
     */
    public function goResetRegisteredToken(&$bearerToken)
    {
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
    }

    /**
     * AbstractController runCallManager
     *
     * @param string $value //The value
     * @param string $data  //The data
     *
     * @return array $serviceData
     */
    public function runCallManager($value, $data)
    {
        switch ($value['method']) {
        case 'callTransferAccessorials':
            $responseApi = $this->apiManager->callTransferAccessorials($data, $value['id']);
            $this->runExpiredToken($data, $responseApi, $value['id'], 'callTransferAccessorials');
            break;
        case 'callTransferDefaultPackage':
            $responseApi = $this->apiManager->callTransferDefaultPackage($data, $value['id']);
            $this->runExpiredToken($data, $responseApi, $value['id'], 'callTransferDefaultPackage');
            break;
        case 'callTransferDeliveryRates':
            $responseApi = $this->apiManager->callTransferDeliveryRates($data, $value['id']);
            $this->runExpiredToken($data, $responseApi, $value['id'], 'callTransferDeliveryRates');
            break;
        case 'callTransferMerchantInfo':
            $responseApi = $this->apiManager->callTransferMerchantInfo($data, $value['id']);
            $this->runExpiredToken($data, $responseApi, $value['id'], 'callTransferMerchantInfo');
            break;
        case 'callTransferShippingServices':
            $responseApi = $this->apiManager->callTransferShippingServices($data, $value['id']);
            $this->runExpiredToken($data, $responseApi, $value['id'], 'callTransferShippingServices');
            break;
        case 'callTransferShipments':
            $responseApi = $this->apiManager->callTransferShipments($data, $value['id']);
            $this->runExpiredToken($data, $responseApi, $value['id'], 'callTransferShipments');
            break;
        case 'callUpdateShipmentsStatus':
            $responseApi = $this->apiManager->callUpdateShipmentsStatus($data, $value['id']);
            $this->runExpiredToken($data, $responseApi, $value['id'], 'callUpdateShipmentsStatus');
            break;
        case 'callUpgradePluginVersion':
            $responseApi = $this->apiManager->callUpgradePluginVersion($data, $value['id']);
            $this->runExpiredToken($data, $responseApi, $value['id'], 'callUpgradePluginVersion');
            break;
        default:
            break;
        }
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
            }
            $resetRegisterToken = true;
        }

        foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        return $resetRegisterToken;
    }

    /**
     * AbstractController setRegisteredToken
     *
     * @param string $data         //The data
     * @param string $responseApi  //The responseApi
     * @param string $tokenId      //The tokenId
     * @param string $functionName //The functionName
     *
     * @return string $trackingStatus
     */
    public function runExpiredToken($data, $responseApi, $tokenId, $functionName)
    {
        $responseApi = json_decode($responseApi);
        // bearerToken expired
        if (isset($responseApi->error->errorCode) && $responseApi->error->errorCode == '401') {
            if ($this->resetRegisteredToken()) {
                $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                $bearerSessionToken = $this->checkoutSession->getBearLongToken();
                if ($bearerSessionToken != $bearerToken) {
                    $bearerToken = $bearerSessionToken;
                }
                $data[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN] = $bearerToken;
                $this->apiManager->$functionName($data, $tokenId);
            }
        }
    }
}
