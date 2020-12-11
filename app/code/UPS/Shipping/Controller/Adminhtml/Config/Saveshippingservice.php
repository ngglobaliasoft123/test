<?php
/**
 * Saveshippingservice file
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

use Magento\Framework\App\Config\ScopeConfigInterface;
/**
 * Saveshippingservice class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Saveshippingservice extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $configWriter;
    protected $cacheTypeList;
    protected $serviceModel;
    protected $deliveryModel;
    protected $apiManager;
    protected $scopeConfig;
    protected $checkoutSession;
    protected $licenseModel;
    protected $apiHandshake;
    protected $apiAccount;

    /**
     * Saveshippingservice __construct
     *
     * @param string $context           //The context
     * @param string $resultPageFactory //The resultPageFactory
     * @param string $configWriter      //The configWriter
     * @param string $cacheTypeList     //The cacheTypeList
     * @param string $serviceModel      //The serviceModel
     * @param string $checkoutSession   //The checkoutSession
     * @param string $deliveryModel     //The deliveryModel
     * @param string $apiManager        //The apiManager
     * @param string $scopeConfig       //The scopeConfig
     * @param string $modelLicense      //modelLicense
     * @param string $apiHandshake      //The apiHandshake
     * @param string $apiAccount        //The apiAccount
     *
     * @return null
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \UPS\Shipping\Model\Service $serviceModel,
        \Magento\Checkout\Model\Session $checkoutSession,
        \UPS\Shipping\Model\DeliveryRates $deliveryModel,
        \UPS\Shipping\API\Manager\Index $apiManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \UPS\Shipping\Model\License $modelLicense,
        \UPS\Shipping\API\Handshake $apiHandshake,
        \UPS\Shipping\API\Account $apiAccount
    ) {
        $this->serviceModel = $serviceModel;
        $this->deliveryModel = $deliveryModel;
        $this->resultPageFactory = $resultPageFactory;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->scopeConfig = $scopeConfig;
        $this->apiManager = $apiManager;
        $this->licenseModel = $modelLicense;
        $this->apiHandshake = $apiHandshake;
        $this->apiAccount = $apiAccount;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    /**
     * Function validate data form
     *
     * @param string $dataForm //The dataForm
     *
     * @return boolean
     */
    public function validate($dataForm)
    {
        $validateForm = 0;
        $shippingDeliveryAP = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_ACCESS_POINT;
        $shippingDeliveryAddress = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_SHIPPING_ADDRESS;
        $configAP = \UPS\Shipping\Helper\Config::CONFIGAP;
        $configServiceAP = \UPS\Shipping\Helper\Config::SERVICEAP;
        $configADD = \UPS\Shipping\Helper\Config::CONFIGADD;
        $configServiceADD = \UPS\Shipping\Helper\Config::SERVICEADD;

        if ((isset($dataForm[$configAP][$shippingDeliveryAP]) && !isset($dataForm[$configServiceAP]))
            || (isset($dataForm[$configADD][$shippingDeliveryAddress]) && !isset($dataForm[$configServiceADD]))
            || (!isset($dataForm[$configAP][$shippingDeliveryAP]) && !isset($dataForm[$configADD][$shippingDeliveryAddress]))
        ) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * Saveshippingservice execute
     *
     * @return null
     */
    public function execute()
    {
        $dataForm = $this->getRequest()->getParams();
        $validate = $this->validate($dataForm);
        if ($validate) {
            $adultSignature = \UPS\Shipping\Helper\Config::ADULT_SIGNATURE;
            if (!isset($dataForm[\UPS\Shipping\Helper\Config::CONFIGAP][$adultSignature])) {
                $adultSignatureValue = 0;
            } else {
                $adultSignatureValue = 1;
            }
            $defaultString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_SET_DEFAULT;
            if (!isset($dataForm[\UPS\Shipping\Helper\Config::CONFIGAP][$defaultString])) {
                $dataForm[\UPS\Shipping\Helper\Config::CONFIGAP][$defaultString] = 0;
            } else {
                $dataForm[\UPS\Shipping\Helper\Config::CONFIGAP][$defaultString] = 1;
            }
            $apString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_ACCESS_POINT;
            if (!isset($dataForm[\UPS\Shipping\Helper\Config::CONFIGAP][$apString])) {
                $dataForm[\UPS\Shipping\Helper\Config::CONFIGAP][$apString] = 0;
            } else {
                $dataForm[\UPS\Shipping\Helper\Config::CONFIGAP]
                [\UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_ACCESS_POINT] = 1;
            }
            $addString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_SHIPPING_ADDRESS;
            if (!isset($dataForm[\UPS\Shipping\Helper\Config::CONFIGADD][$addString])) {
                $dataForm[\UPS\Shipping\Helper\Config::CONFIGADD]
                [\UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_SHIPPING_ADDRESS] = 0;
            } else {
                $dataForm[\UPS\Shipping\Helper\Config::CONFIGADD]
                [\UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_SHIPPING_ADDRESS] = 1;
            }
            $countryCode = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE);
            $arrAPService = $this->serviceModel->getListService($countryCode, 'AP');
            $arrADDService = $this->serviceModel->getListService($countryCode, 'ADD');
            //hanlding ap
            $this->hanldingAp($arrAPService, $dataForm);
            //hanlding add
            $this->hanldingAdd($arrADDService, $dataForm);
            //hanlding deleteDeliveryRates
            $listNoSelected = $this->serviceModel->getListNotSelected();
            foreach ($listNoSelected as $key => $value) {
                $this->deliveryModel->deleteDeliveryRates($value['id']);
            }
            $this->configWriter->save(\UPS\Shipping\Helper\Config::ADULT_SIGNATURE, $adultSignatureValue);
            //hanlding saveDeliveryRates
            $this->saveDelivery($dataForm);
            //offPluginManager 2019-03-18
            // call plugin manager
            $checkTransferExist = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_PLUGIN_MERCHANTINFO_EXIST);
            if ($checkTransferExist == '1') {
                $apString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_ACCESS_POINT;
                $addString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_SHIPPING_ADDRESS;
                $shippingDeliveryAP = $dataForm[\UPS\Shipping\Helper\Config::CONFIGAP][$apString];
                $shippingDeliveryADD = $dataForm[\UPS\Shipping\Helper\Config::CONFIGADD][$addString];
                $this->callTransferShippingService($shippingDeliveryAP, $shippingDeliveryADD);
            }
            //end call plugin manager
            $this->callPluginManager($dataForm);
        } else {
            $this->messageManager->addError(__("Please select at least one shipping service."));
            $this->_redirect('upsshipping/config/shippingservice');
        }
    }

    /**
     * Saveshippingservice call plugin manager
     *
     * @param string $dataForm //The dataForm
     *
     * @return boolean
     */
    public function callPluginManager($dataForm)
    {
        if (isset($dataForm['save'])) {
            $this->cacheTypeList->cleanType('config');
            $this->_redirect('upsshipping/config/shippingservice');
        } else {
            $this->configWriter->save(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_SHIPPING_SERVICE, 1);
            foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
                $this->cacheTypeList->cleanType($type);
            }
            $this->_redirect('upsshipping/config/cashondelivery');
        }
    }
    /**
     * Saveshippingservice hanldingAp
     *
     * @param array  $apService //The apService
     * @param string $dataForm  //The dataForm
     *
     * @return boolean
     */
    public function hanldingAp($apService, $dataForm)
    {
        foreach ($dataForm[\UPS\Shipping\Helper\Config::CONFIGAP] as $key => $value) {
            $this->saveConfigWriter($key, $value);
        }
        $shippingDeliveryAP = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_ACCESS_POINT;
        if ($dataForm[\UPS\Shipping\Helper\Config::CONFIGAP][$shippingDeliveryAP] == 1) {
            foreach ($apService as $data) {
                if (isset($dataForm[\UPS\Shipping\Helper\Config::SERVICEAP])
                    && in_array($data['id'], $dataForm[\UPS\Shipping\Helper\Config::SERVICEAP])
                ) {
                    $checked = 1;
                } else {
                    $checked = 0;
                }
                $this->serviceModel->updateSelectedService($data['id'], $checked);
            }
        }
    }

    /**
     * Saveshippingservice HanldingAdd
     *
     * @param array  $addService //The addService
     * @param string $dataForm   //The dataForm
     *
     * @return boolean
     */
    public function hanldingAdd($addService, $dataForm)
    {
        foreach ($dataForm[\UPS\Shipping\Helper\Config::CONFIGADD] as $key => $value) {
            $this->saveConfigWriter($key, $value);
        }
        $shippingDeliveryADD = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_SHIPPING_ADDRESS;
        if ($dataForm[\UPS\Shipping\Helper\Config::CONFIGADD][$shippingDeliveryADD] == 1) {
            foreach ($addService as $data) {
                if (isset($dataForm[\UPS\Shipping\Helper\Config::SERVICEADD])
                    && in_array($data['id'], $dataForm[\UPS\Shipping\Helper\Config::SERVICEADD])
                ) {
                    $checked = 1;
                } else {
                    $checked = 0;
                }
                $this->serviceModel->updateSelectedService($data['id'], $checked);
            }
        }
    }

    /**
     * Saveshippingservice saveConfigWriter
     *
     * @param string $key   //The key
     * @param string $value //The value
     *
     * @return boolean
     */
    public function saveConfigWriter($key, $value)
    {
        $this->configWriter->save($key, $value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
    }

    /**
     * Saveshippingservice saveDeliveryRates
     *
     * @param string $dataForm //The dataForm
     *
     * @return boolean
     */
    public function saveDelivery($dataForm)
    {
        $ListSelected = $this->deliveryModel->getListCheckedDeliveryRates();
        $serviceArray = [];
        foreach ($ListSelected as $key => $value) {
            $serviceArray[] = $value['idservice'];
        }
        $configServiceAdd = \UPS\Shipping\Helper\Config::SERVICEADD;
        if (isset($dataForm[\UPS\Shipping\Helper\Config::SERVICEAP])
            && isset($dataForm[\UPS\Shipping\Helper\Config::SERVICEADD])
        ) {
            $megeArray = array_merge($dataForm[\UPS\Shipping\Helper\Config::SERVICEAP], $dataForm[$configServiceAdd]);
        } elseif (isset($dataForm[\UPS\Shipping\Helper\Config::SERVICEAP])) {
            $megeArray = $dataForm[\UPS\Shipping\Helper\Config::SERVICEAP];
        } elseif (isset($dataForm[\UPS\Shipping\Helper\Config::SERVICEADD])) {
            $megeArray = $dataForm[\UPS\Shipping\Helper\Config::SERVICEADD];
        } else {
            $megeArray = '';
        }
        $countryCode = strtolower($this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE));
        $rateTypeValue = 'flat_rate';
        $minOrderValue = 0;
        $deliveryRate = 0;
        if ('us' == $countryCode) {
            $rateTypeValue = 'real_time';
            $minOrderValue = null;
            $deliveryRate = 100;
        }
        foreach ($megeArray as $key => $value) {
            if (!in_array($value, $serviceArray)) {
                $dataInsertDefault = [
                    'service_id' => $value,
                    'rate_type' => $rateTypeValue,
                    'min_order_value' => $minOrderValue,
                    'delivery_rate' => $deliveryRate
                ];
                $this->deliveryModel->saveDeliveryRates($dataInsertDefault);
            }
        }
    }

    /**
     * Saveshippingservice call transfer shipping service
     *
     * @param string $flagAP  //The flagAP
     * @param string $flagADD //The flagADD
     *
     * @return string $returnDataAPI
     */
    public function callTransferShippingService($flagAP, $flagADD)
    {
        $arrShippingService = [];
        if ($flagAP == '1') {
            $arrShippingServiceAP  = $this->serviceModel->getSelectedServices('AP');
            $arrShippingService  = array_merge($arrShippingService, $arrShippingServiceAP);
        }
        if ($flagADD == '1') {
            $arrShippingServiceADD = $this->serviceModel->getSelectedServices('ADD');
            $arrShippingService  = array_merge($arrShippingService, $arrShippingServiceADD);
        }
        $listShippingServices = [];
        if (!empty($arrShippingService)) {
            foreach ($arrShippingService as $service) {
                $stringServiceName = '';
                $serviceNameString = $service['service_name'];
                if ($service['service_symbol'] == '&trade;') {
                    $stringServiceName = __("UPS Access Point™ Economy");
                } elseif ($serviceNameString == 'UPS Standard') {
                    $stringServiceName = __('UPS® Standard');
                } elseif ($serviceNameString == 'UPS Express 12:00') {
                    $stringServiceName = __('UPS Express 12:00');
                } elseif ($serviceNameString == 'UPS Ground') {
                    $stringServiceName = __('UPS® Ground');
                } elseif ($serviceNameString == 'UPS Next Day Air Early') {
                    $stringServiceName = __('UPS Next Day Air® Early');
                } elseif ($serviceNameString == 'UPS Standard - Saturday Delivery') {
                    $stringServiceName = __('UPS® Standard - Saturday Delivery');
                } elseif ($serviceNameString == 'UPS Express - Saturday Delivery') {
                    $stringServiceName = __('UPS Express® - Saturday Delivery');
                } else {
                    $stringServiceName =  __($serviceNameString). '®';
                }
                $listShippingServices[] = [
                    'service_key' => $service['service_key_delivery'],
                    'service_type' => ($service['service_type'] == 'AP') ? 10 : 20,
                    'service_name' => $stringServiceName,
                    'rate_code' => $service['rate_code']
                ];
            }
            $this->runTransferShippingServices($listShippingServices);
        }
    }

    /**
     * Saveshippingservice runTransferShippingServices
     *
     * @param string $listShippingServices //The listShippingServices
     *
     * @return string $trackingStatus
     */
    public function runTransferShippingServices($listShippingServices)
    {
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
            $dataTransferShippingServices = [
                'dataShippingServices' => $listShippingServices,
                'merchantKey' => $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY),
                \UPS\Shipping\Helper\ConstantManager::BEARERTOKEN => $bearerToken
            ];
            $returnDataAPI = $this->apiManager->callTransferShippingServices($dataTransferShippingServices, 1);
            $returnDataAPI = json_decode($returnDataAPI);
            // bearerToken expired
            if (isset($returnDataAPI->error->errorCode) && $returnDataAPI->error->errorCode == '401') {
                if ($this->resetRegisteredToken()) {
                    $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                    $bearerSessionToken = $this->checkoutSession->getBearLongToken();
                    if ($bearerSessionToken != $bearerToken) {
                        $bearerToken = $bearerSessionToken;
                    }
                    $dataTransferShippingServices[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN] = $bearerToken;
                    $this->apiManager->callTransferShippingServices($dataTransferShippingServices, 1);
                }
            }
        }
    }

    /**
     * Saveshippingservice resetRegisteredToken
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
