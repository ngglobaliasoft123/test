<?php
/**
 * SaveDeliveryRates file
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
 * SaveDeliveryRates class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class SaveDeliveryRates extends \Magento\Framework\App\Action\Action
{
    protected $pageFactory;
    protected $cacheTypeList;
    protected $delivery;
    protected $dataParam;
    protected $scopeConfig;
    protected $modelService;
    protected $modelDeliveryRates;
    protected $apiManager;
    protected $checkoutSession;
    protected $configWriter;
    protected $licenseModel;
    protected $apiHandshake;
    protected $apiAccount;

    /**
     * SaveDeliveryRates __construct
     *
     * @param string $context            //The context
     * @param string $pageFactory        //The pageFactory
     * @param string $configWriter       //The configWriter
     * @param string $cacheTypeList      //The cacheTypeList
     * @param string $checkoutSession    //The checkoutSession
     * @param string $delivery           //The delivery
     * @param string $modelService       //The modelService
     * @param string $apiManager         //The apiManager
     * @param string $modelDeliveryRates //The modelDeliveryRates
     * @param string $scopeConfig        //The scopeConfig
     * @param string $modelLicense       //modelLicense
     * @param string $apiHandshake       //The apiHandshake
     * @param string $apiAccount         //The apiAccount
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Checkout\Model\Session $checkoutSession,
        \UPS\Shipping\Model\DeliveryRates $delivery,
        \UPS\Shipping\Model\Service $modelService,
        \UPS\Shipping\API\Manager\Index $apiManager,
        \UPS\Shipping\Model\DeliveryRates $modelDeliveryRates,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \UPS\Shipping\Model\License $modelLicense,
        \UPS\Shipping\API\Handshake $apiHandshake,
        \UPS\Shipping\API\Account $apiAccount
    ) {
        $this->pageFactory = $pageFactory;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->delivery = $delivery;
        $this->scopeConfig = $scopeConfig;
        $this->modelService = $modelService;
        $this->modelDeliveryRates = $modelDeliveryRates;
        $this->checkoutSession = $checkoutSession;
        $this->licenseModel = $modelLicense;
        $this->apiHandshake = $apiHandshake;
        $this->apiAccount = $apiAccount;
        $this->apiManager = $apiManager;
        parent::__construct($context);
    }

    /**
     * SaveDeliveryRates execute
     *
     * @return null
     */
    public function execute()
    {
        $this->dataParam = $this->getRequest()->getParams();
        if ($this->_validateForm()) {
            $this->delivery->removeAllDeliveryRates();
            $this->_saveAP();
            $this->_saveADD();
            //offPluginManager 2019-03-18
            $checkMechantExist = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_PLUGIN_MERCHANTINFO_EXIST);
            if ($checkMechantExist == '1') {
                $this->callTransferDeliveryRate();
            }

            if ($this->dataParam['submit'] == 'next') {
                $this->configWriter->save(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_DELIVERY_RATES, 1);
                foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
                    $this->cacheTypeList->cleanType($type);
                }
                $this->_redirect('upsshipping/config/billingpreference');
            } else {
                $this->messageManager->addSuccess(__("Data saved successfully."));
                $this->_redirect('upsshipping/config/deliveryrates');
            }
        } else {
            $this->messageManager->addError(__("Some of the data you entered is not valid. Please check again."));
            $this->_redirect('upsshipping/config/deliveryrates');
        }
    }

    /**
     * SaveDeliveryRates validateForm
     *
     * @return null
     */
    private function _validateForm()
    {
        if (!isset($this->dataParam[\UPS\Shipping\Helper\ConstantDeliveryRates::RATE_TYPEAP])) {
            $this->dataParam[\UPS\Shipping\Helper\ConstantDeliveryRates::RATE_TYPEAP] = [];
        }

        if (!isset($this->dataParam[\UPS\Shipping\Helper\ConstantDeliveryRates::RATE_TYPEADD])) {
            $this->dataParam[\UPS\Shipping\Helper\ConstantDeliveryRates::RATE_TYPEADD] = [];
        }
        return true;
    }

    /**
     * SaveDeliveryRates saveAP
     *
     * @return null
     */
    private function _saveAP()
    {
        $dataForm = $this->dataParam;
        foreach ($dataForm[\UPS\Shipping\Helper\ConstantDeliveryRates::RATE_TYPEAP] as $service_id => $rate_type) {
            $minValue = null;
            $rateValue = null;
            switch (trim($rate_type)) {
            case 'flat_rate':
                if (isset($dataForm['minValueAP'][$service_id])) {
                    foreach ($dataForm['minValueAP'][$service_id] as $key => $minValue) {
                        $data = [
                            \UPS\Shipping\Helper\ConstantDeliveryRates::SERVICE_ID => $service_id,
                            \UPS\Shipping\Helper\ConstantDeliveryRates::RATE_TYPE => $rate_type,
                            \UPS\Shipping\Helper\ConstantDeliveryRates::MIN_ORDER_VALUE => $minValue,
                            \UPS\Shipping\Helper\ConstantDeliveryRates::DELIVERY_RATE
                            => $dataForm['rateValueAP'][$service_id][$key]
                        ];
                        $this->delivery->saveDeliveryRates($data);
                    }
                }
                break;

            case \UPS\Shipping\Helper\ConstantDeliveryRates::REAL_TIME:
                if (isset($dataForm['rateValueRealTimeAP'][$service_id])) {
                    $rateValue = $dataForm['rateValueRealTimeAP'][$service_id];
                    $data = [
                        \UPS\Shipping\Helper\ConstantDeliveryRates::SERVICE_ID => $service_id,
                        \UPS\Shipping\Helper\ConstantDeliveryRates::RATE_TYPE => $rate_type,
                        \UPS\Shipping\Helper\ConstantDeliveryRates::DELIVERY_RATE => $rateValue
                    ];
                    $this->delivery->saveDeliveryRates($data);
                }
                break;

            default:
                break;
            }
        }
    }

    /**
     * SaveDeliveryRates saveADD
     *
     * @return null
     */
    private function _saveADD()
    {
        $dataForm = $this->dataParam;
        foreach ($dataForm[\UPS\Shipping\Helper\ConstantDeliveryRates::RATE_TYPEADD] as $service_id => $rate_type) {
            $minValue = null;
            $rateValue = null;
            switch (trim($rate_type)) {
            case 'flat_rate':
                foreach ($dataForm['minValueADD'][$service_id] as $key => $minValue) {
                    $data = [
                        \UPS\Shipping\Helper\ConstantDeliveryRates::SERVICE_ID => $service_id,
                        \UPS\Shipping\Helper\ConstantDeliveryRates::RATE_TYPE => $rate_type,
                        \UPS\Shipping\Helper\ConstantDeliveryRates::MIN_ORDER_VALUE => $minValue,
                        \UPS\Shipping\Helper\ConstantDeliveryRates::DELIVERY_RATE
                        => $dataForm['rateValueADD'][$service_id][$key]
                    ];
                    $this->delivery->saveDeliveryRates($data);
                }
                break;

            case \UPS\Shipping\Helper\ConstantDeliveryRates::REAL_TIME:
                $rateValue = $dataForm['rateValueRealTimeADD'][$service_id];
                $data = [
                    \UPS\Shipping\Helper\ConstantDeliveryRates::SERVICE_ID => $service_id,
                    \UPS\Shipping\Helper\ConstantDeliveryRates::RATE_TYPE => $rate_type,
                    \UPS\Shipping\Helper\ConstantDeliveryRates::DELIVERY_RATE => $rateValue
                ];
                $this->delivery->saveDeliveryRates($data);
                break;

            default:
                break;
            }
        }
    }

    /**
     * SaveDeliveryRates callTransferDeliveryRates
     *
     * @return string returnDataAPI
     */
    public function callTransferDeliveryRate()
    {
        $arrShippingService = [];
        $shippingDeliveryAP = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_ACCESS_POINT;
        if ($this->scopeConfig->getValue($shippingDeliveryAP) == '1') {
            $arrShippingServiceAP  = $this->modelService->getSelectedServices('AP');
            $arrShippingService  = array_merge($arrShippingService, $arrShippingServiceAP);
        }
        $shippingDeliveryAddress = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_SHIPPING_ADDRESS;
        if ($this->scopeConfig->getValue($shippingDeliveryAddress) == '1') {
            $arrShippingServiceADD = $this->modelService->getSelectedServices('ADD');
            $arrShippingService  = array_merge($arrShippingService, $arrShippingServiceADD);
        }

        $arrDelivaryRatesAp = [];
        if (!empty($arrShippingService)) {
            foreach ($arrShippingService as $service) {
                $stringServiceName = $this->changeServiceNameString($service);
                $deliveryRateService = $this->modelDeliveryRates->getListDeliveryRatesByServiceId($service['id']);
                if (!empty($deliveryRateService)) {
                    foreach ($deliveryRateService as $rateService) {
                        $minimumOrderValue = 0;
                        $deliveryValue = 0;
                        $realtimeValue = 0;
                        $rateType = \UPS\Shipping\Helper\ConstantDeliveryRates::RATE_TYPE;
                        if ($rateService[$rateType] == \UPS\Shipping\Helper\ConstantDeliveryRates::REAL_TIME) {
                            $deliveryType = 20;
                            $realtimeValue = $rateService[\UPS\Shipping\Helper\ConstantDeliveryRates::DELIVERY_RATE];
                        } else {
                            $deliveryType = 10;
                            $minimumOrderValue
                                = (isset($rateService[\UPS\Shipping\Helper\ConstantDeliveryRates::MIN_ORDER_VALUE]))
                            ? $rateService[\UPS\Shipping\Helper\ConstantDeliveryRates::MIN_ORDER_VALUE] : 0;
                            $deliveryValue
                                = (isset($rateService[\UPS\Shipping\Helper\ConstantDeliveryRates::DELIVERY_RATE]))
                            ? $rateService[\UPS\Shipping\Helper\ConstantDeliveryRates::DELIVERY_RATE] : 0;
                        }
                        $arrDelivaryRates[]  = [
                            'service_key_delivery' => $service['service_key_delivery'],
                            \UPS\Shipping\Helper\ConstantDeliveryRates::RATE_TYPE => $deliveryType,
                            'service_type' => ($service['service_type'] == 'AP') ? 10 : 20,
                            'service_name' => $stringServiceName,
                            'rate_code' => $service['rate_code'],
                            \UPS\Shipping\Helper\ConstantDeliveryRates::MIN_ORDER_VALUE => (float)$minimumOrderValue,
                            \UPS\Shipping\Helper\ConstantDeliveryRates::DELIVERY_RATE => (float)$deliveryValue,
                            'realtimeValue' => (float)$realtimeValue
                        ];
                    }
                }
            }
        }
        $this->runTransferDeliveryRates($arrDelivaryRates);
    }


    /**
     * SaveDeliveryRates runTransferDeliveryRates
     *
     * @param string $arrDelivaryRates //The arrDelivaryRates
     *
     * @return string $trackingStatus
     */
    public function runTransferDeliveryRates($arrDelivaryRates)
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
            $dataTransferDeliveryRates = [
                'deliveryRates' => $arrDelivaryRates,
                'merchantKey' => $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY),
                \UPS\Shipping\Helper\ConstantManager::BEARERTOKEN => $bearerToken
            ];
            $responseApi = $this->apiManager->callTransferDeliveryRates($dataTransferDeliveryRates, 1);
            $responseApi = json_decode($responseApi);
            // bearerToken expired
            if (isset($responseApi->error->errorCode) && $responseApi->error->errorCode == '401') {
                if ($this->resetRegisteredToken()) {
                    $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                    $bearerSessionToken = $this->checkoutSession->getBearLongToken();
                    if ($bearerSessionToken != $bearerToken) {
                        $bearerToken = $bearerSessionToken;
                    }
                    $dataTransferDeliveryRates[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN] = $bearerToken;
                    $this->apiManager->callTransferDeliveryRates($dataTransferDeliveryRates, 1);
                }
            }
        }
    }


    /**
     * SaveDeliveryRates changeServiceNameString
     *
     * @param string $service //The service
     *
     * @return string $trackingStatus
     */
    public function changeServiceNameString($service)
    {
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
        return $stringServiceName;
    }

    /**
     * SaveDeliveryRates resetRegisteredToken
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
