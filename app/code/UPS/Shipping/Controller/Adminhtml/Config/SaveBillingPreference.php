<?php
/**
 * Savebillingpreference file
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
 * Savebillingpreference class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class SaveBillingPreference extends \Magento\Framework\App\Action\Action
{
    protected $configWriter;
    protected $cacheTypeList;
    protected $scopeConfig;
    protected $modelAccessorial;
    protected $modelAccount;
    protected $modelService;
    protected $modelPackage;
    protected $modelBackuprate;
    protected $modelDeliveryRates;
    protected $apiManager;
    protected $checkoutSession;
    protected $storeManager;
    protected $licenseModel;
    protected $apiHandshake;
    protected $apiAccount;
    protected $setup;
    protected $apiLicense;
    protected $userModel;
    protected $authSession;

    /**
     * Savebillingpreference __construct
     *
     * @param string $context            //The context
     * @param string $configWriter       //The configWriter
     * @param string $cacheTypeList      //The cacheTypeList
     * @param string $checkoutSession    //The checkoutSession
     * @param string $scopeConfig        //The scopeConfig
     * @param string $modelAccessorial   //The modelAccessorial
     * @param string $modelPackage       //The modelPackage
     * @param string $modelBackuprate    //The modelBackuprate
     * @param string $modelAccount       //The modelAccount
     * @param string $modelService       //The modelService
     * @param string $modelDeliveryRates //The modelDeliveryRates
     * @param string $apiManager         //The apiManager
     * @param string $storeManager       //The storeManager
     * @param string $modelLicense       //modelLicense
     * @param string $apiHandshake       //The apiHandshake
     * @param string $apiAccount         //The apiAccount
     * @param string $setup              //The setup
     * @param string $apiLicense         //The apiLicense
     * @param string $userModel          //The userModel
     * @param string $authSession        //The authSession
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \UPS\Shipping\Model\Accessorial $modelAccessorial,
        \UPS\Shipping\Model\Package $modelPackage,
        \UPS\Shipping\Model\Backuprate $modelBackuprate,
        \UPS\Shipping\Model\Account $modelAccount,
        \UPS\Shipping\Model\Service $modelService,
        \UPS\Shipping\Model\DeliveryRates $modelDeliveryRates,
        \UPS\Shipping\API\Manager\Index $apiManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \UPS\Shipping\Model\License $modelLicense,
        \UPS\Shipping\API\Handshake $apiHandshake,
        \UPS\Shipping\API\Account $apiAccount,
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \UPS\Shipping\API\License $apiLicense,
        \Magento\User\Model\User $userModel,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->storeManager = $storeManager;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->scopeConfig = $scopeConfig;
        $this->modelAccount = $modelAccount;
        $this->modelPackage = $modelPackage;
        $this->modelBackuprate = $modelBackuprate;
        $this->modelService = $modelService;
        $this->modelAccessorial = $modelAccessorial;
        $this->modelDeliveryRates = $modelDeliveryRates;
        $this->apiManager = $apiManager;
        $this->checkoutSession = $checkoutSession;
        $this->licenseModel = $modelLicense;
        $this->apiHandshake = $apiHandshake;
        $this->apiAccount = $apiAccount;
        $this->apiLicense = $apiLicense;
        $this->userModel = $userModel;
        $this->authSession = $authSession;
        $this->setup = $setup;
        parent::__construct($context);
    }

    /**
     * Savebillingpreference execute
     *
     * @return null
     */
    public function execute()
    {
        $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_MERCHANTINFO_EXIST, 1);
        $this->configWriter->save(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_BILLING_PREFERENCE, 1);
        //offPluginManager 2019-03-18
        $checkTransferExist = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_PLUGIN_MERCHANTINFO_EXIST);
        if ($checkTransferExist == 0 || empty($checkTransferExist) || 1) {
            if (empty($checkTransferExist)) {
                //save merchant/plugin/exist
                $dataPlugin = [
                    'scope' => 'default',
                    'scope_id' => 0,
                    'path' => \UPS\Shipping\Helper\Config::UPS_PLUGIN_MERCHANTINFO_EXIST,
                    'value' => 0,
                ];
                $this->setup->getConnection()->insertOnDuplicate($this->setup->getTable('core_config_data'), $dataPlugin);
                $handshakeToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN);
                if (empty($handshakeToken)) {
                    $this->callAPIHandshake();
                    // get user name
                    $userName = $this->authSession->getUser()->getUsername();
                    $locate = explode('_', $this->userModel->loadByUsername($userName)->getData('interface_locale'));
                    // get responce access license
                    $countryCodeString = \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE;
                    $arrCountryCode = [
                        "CountryCode" => $this->scopeConfig->getValue($countryCodeString),
                        "LanguageCode" => $locate[0]
                    ];
                    $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::PRE_REGISTERED_PLUGIN_TOKEN);
                    if (!empty($bearerToken)) {
                        $this->apiLicense->access1($arrCountryCode, $bearerToken);
                    } else {
                        $this->callAPIHandshake();
                        $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::PRE_REGISTERED_PLUGIN_TOKEN);
                        if (!empty($bearerToken)) {
                            $this->apiLicense->access1($arrCountryCode, $bearerToken);
                        }
                    }
                }
            }
            $response = $this->callTransferMerchantInfo(0);
            if ($response) {
                $this->callTransferMerchantInfo(1);
            }
            $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_PLUGIN_MERCHANTINFO_EXIST, 1);
        }
        foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        $this->_redirect('upsshipping/shipment/order');
    }

    /**
     * Function TransferMerchantInfoAPI
     *
     * @param string $default //The default
     *
     * @return boolean
     */
    public function callTransferMerchantInfo($default = 0) //default = 0 account default , = 1 account success
    {
        $merchantKey = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY);
        $websiteMerchant = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_WEBSECUREURL);
        $websiteMerchant = str_replace('https://', '', str_replace('http://', '', $websiteMerchant));
        $version = \UPS\Shipping\Helper\Config::VERSION_PLUGIN;
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();

        $defaultPackageName = 'default package';
        $weight = '0';
        $weightUnit = 'kgs';
        $length = '0';
        $width = '0';
        $height = '0';
        $dimensionUnit = 'cm';
        $packageItem = '0';
        $serviceKey = '';
        $rate = '0';

        // Get Account information
        $accountNumberInfo = [];
        $runAPI = true;
        // Accessorial
        $arrReturnAccessorial = $this->modelAccessorial->getListAccessorialActive();
        $arrReturnAccessorial = json_decode($arrReturnAccessorial, true);
        $arrAccessorial = [];
        $arrayAccessorialKey = array_keys($arrReturnAccessorial);
        if (!empty($arrayAccessorialKey)) {
            foreach ($arrayAccessorialKey as $key) {
                $arrAccessorial[] = [
                    'accessorial_key' => $key,
                    'accessorial_name' => $arrReturnAccessorial[$key]
                ];
            }
        }
        $optionPackage = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_PACKAGE_DIMENSIONS);

        $optionIncludeDimension = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_INCLUDE_DIMENSIONS);
        $arrShippingService = $this->getServiceShippingArray();
        // Shipping Services and Delivery Rates
        $listShippingServices = [];
        $arrDelivaryRates = [];
        $this->getRatesServices($arrShippingService, $listShippingServices, $arrDelivaryRates);
        // Default package
        $jsonDefaultPackage = $this->modelPackage->getListPackage();
        $jsonBackuprate = $this->modelBackuprate->getListBackuprate();
        if (!empty($jsonDefaultPackage)) {
            $defaultPackage = (isset($jsonDefaultPackage[0])) ? $jsonDefaultPackage[0] : [];
            if (!empty($defaultPackage) && $optionPackage < 2) {
                $defaultPackageName = $defaultPackage[\UPS\Shipping\Helper\ConstantPackage::PACKAGE_NAME];
                $weight             = $defaultPackage[\UPS\Shipping\Helper\ConstantPackage::WEIGHT];
                $weightUnit
                    = (strtoupper($defaultPackage[\UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT]) == 'LBS')
                ? 'Pounds' : 'Kg';
                $length             = $defaultPackage[\UPS\Shipping\Helper\ConstantPackage::LENGTH];
                $width              = $defaultPackage[\UPS\Shipping\Helper\ConstantPackage::WIDTH];
                $height             = $defaultPackage[\UPS\Shipping\Helper\ConstantPackage::HEIGHT];
                $dimensionUnit
                    = (strtoupper($defaultPackage[\UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION]) == 'CM')
                ? 'Cm' : 'Inch';
                $packageItem = $defaultPackage['package_number'];
            }
        }

        if (!empty($jsonBackuprate)) {
            $defaultBackuprate = (isset($jsonBackuprate[0])) ? $jsonBackuprate[0] : [];
            if (!empty($defaultBackuprate)) {
                $serviceInformation = $this->modelService->getShippingServiceById($defaultBackuprate['service_id']);
                if (!empty($serviceInformation[0]['service_key'])) {
                    $serviceKey = $serviceInformation[0]['service_key'];
                }
                $rate = $defaultBackuprate['fallback_rate'];
            }
        }
        $defaultPackages = [
            'option' => $optionPackage,
            \UPS\Shipping\Helper\ConstantPackage::PACKAGE_NAME => $defaultPackageName,
            \UPS\Shipping\Helper\ConstantPackage::WEIGHT => $weight,
            \UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT => $weightUnit,
            \UPS\Shipping\Helper\ConstantPackage::LENGTH => $length,
            \UPS\Shipping\Helper\ConstantPackage::WIDTH => $width,
            \UPS\Shipping\Helper\ConstantPackage::HEIGHT => $height,
            \UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION => $dimensionUnit,
            'packageItem' => $packageItem,
            'includeDimensionsInRating' => $optionIncludeDimension,
            'serviceKey' => $serviceKey,
            'rate' => $rate
        ];
        if ($default == 0) { // default Account
            $accountDefault = $this->modelAccount->getAccountDefault();
            // this is
            $accountNumberInfo = $this->getOneNumberInfo($accountDefault, $merchantKey, $websiteMerchant, $currencyCode, $version);
        } else { // list account
            $listAccount = $this->modelAccount->getListAccount();
            // this is
            $accountNumberInfo = $this->getMoreNumberInfo($listAccount, $merchantKey, $websiteMerchant, $currencyCode, $version, $defaultPackageName, $weight, $weightUnit, $length, $width, $height, $dimensionUnit);
            $arrAccessorial = [];
            $listShippingServices = [];
            $arrDelivaryRates = [];
        }
        $dataPluginVersion = [
            'scope' => 'default',
            'scope_id' => 0,
            'path' => \UPS\Shipping\Helper\Config::UPS_READY_PLUGIN_VERSION,
            'value' => \UPS\Shipping\Helper\Config::VERSION_PLUGIN,
        ];
        $this->setup->getConnection()->insertOnDuplicate($this->setup->getTable('core_config_data'), $dataPluginVersion);
        $this->runTransferMerchantInfo($accountNumberInfo, $defaultPackages, $arrAccessorial, $listShippingServices, $arrDelivaryRates);
        return true;
    }

    /**
     * Function runTransferMerchantInfo
     *
     * @param string $accountNumberInfo    //The accountNumberInfo
     * @param string $defaultPackages      //The defaultPackages
     * @param string $arrAccessorial       //The arrAccessorial
     * @param string $listShippingServices //The listShippingServices
     * @param string $arrDelivaryRates     //The arrDelivaryRates
     *
     * @return string
     */
    public function runTransferMerchantInfo($accountNumberInfo, $defaultPackages, $arrAccessorial, $listShippingServices, $arrDelivaryRates)
    {
        // UPS_SERVICE_LONG_SECURITY_TOKEN
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
        $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_PLUGIN_MERCHANTINFO_EXIST, 1);
        if (!empty($bearerToken) && !empty($accountNumberInfo)) {
            $dataTransferMerchantInfo = [
                'accountNumberInfo' => $accountNumberInfo,
                'defaultPackages' => $defaultPackages,
                'accessorials' => $arrAccessorial,
                'shippingServices' => $listShippingServices,
                'deliveryRates' => $arrDelivaryRates,
                \UPS\Shipping\Helper\ConstantManager::BEARERTOKEN => $bearerToken
            ];
            $returnDataAPI = $this->apiManager->callTransferMerchantInfo($dataTransferMerchantInfo);
            $returnDataAPI = json_decode($returnDataAPI);
            // bearerToken expired
            if (isset($returnDataAPI->error->errorCode) && $returnDataAPI->error->errorCode == '401') {
                if ($this->resetRegisteredToken()) {
                    $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                    $bearerSessionToken = $this->checkoutSession->getBearLongToken();
                    if ($bearerSessionToken != $bearerToken) {
                        $bearerToken = $bearerSessionToken;
                    }
                    $dataTransferMerchantInfo[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN] = $bearerToken;
                    $returnDataAPI = $this->apiManager->callTransferMerchantInfo($dataTransferMerchantInfo);
                }
            }
            if (isset($returnDataAPI->data) && $returnDataAPI->data == 'true') {
                return true;
            }
        }
    }

    /**
     * Function getServiceNameString
     *
     * @param string $service //The service
     *
     * @return string
     */
    public function getServiceNameString($service)
    {
        $stringServiceName = '';
        if ($service['service_symbol'] == '&trade;') {
            $stringServiceName = __("UPS Access Point™ Economy");
        } elseif ($service[\UPS\Shipping\Helper\ConstantBilling::SERVICE_NAME] == 'UPS Standard') {
            $stringServiceName = __('UPS® Standard');
        } elseif ($service[\UPS\Shipping\Helper\ConstantBilling::SERVICE_NAME] == 'UPS Express 12:00') {
            $stringServiceName = __('UPS Express 12:00');
        } elseif ($service[\UPS\Shipping\Helper\ConstantBilling::SERVICE_NAME] == 'UPS Ground') {
            $stringServiceName = __('UPS® Ground');
        } elseif ($service[\UPS\Shipping\Helper\ConstantBilling::SERVICE_NAME] == 'UPS Next Day Air Early') {
            $stringServiceName = __('UPS Next Day Air® Early');
        } elseif ($service[\UPS\Shipping\Helper\ConstantBilling::SERVICE_NAME] == 'UPS Standard - Saturday Delivery') {
            $stringServiceName = __('UPS® Standard - Saturday Delivery');
        } elseif ($service[\UPS\Shipping\Helper\ConstantBilling::SERVICE_NAME] == 'UPS Express - Saturday Delivery') {
            $stringServiceName = __('UPS Express® - Saturday Delivery');
        } else {
            $stringServiceName =  __($service[\UPS\Shipping\Helper\ConstantBilling::SERVICE_NAME]). '®';
        }
        return $stringServiceName;
    }

    /**
     * Function getRatesServices
     *
     * @param array $arrShippingService   //The arrShippingService
     * @param array $listShippingServices //The listShippingServices
     * @param array $arrDelivaryRates     //The arrDelivaryRates
     *
     * @return null
     */
    public function getRatesServices($arrShippingService, &$listShippingServices, &$arrDelivaryRates)
    {
        // Shipping Services and Delivery Rates
        if (!empty($arrShippingService)) {
            foreach ($arrShippingService as $service) {
                $deliveryRateService = $this->modelDeliveryRates->getListDeliveryRatesByServiceId($service['id']);
                $stringServiceName = $this->getServiceNameString($service);
                $listShippingServices[] = [
                    \UPS\Shipping\Helper\ConstantBilling::SERVICE_KEY_DELIVERY
                        => $service[\UPS\Shipping\Helper\ConstantBilling::SERVICE_KEY_DELIVERY],
                    \UPS\Shipping\Helper\ConstantBilling::SERVICE_TYPE
                        => ($service[\UPS\Shipping\Helper\ConstantBilling::SERVICE_TYPE] == 'AP') ? 10 : 20,
                    \UPS\Shipping\Helper\ConstantBilling::SERVICE_NAME => $stringServiceName,
                    \UPS\Shipping\Helper\ConstantBilling::RATE_CODE
                        => $service[\UPS\Shipping\Helper\ConstantBilling::RATE_CODE]
                ];
                if (!empty($deliveryRateService)) {
                    foreach ($deliveryRateService as $rateService) {
                        $minimumOrderValue = 0;
                        $deliveryValue = 0;
                        $realtimeValue = 0;
                        if ($rateService['rate_type'] == 'real_time') {
                            $deliveryType = 20;
                            $realtimeValue = $rateService[\UPS\Shipping\Helper\ConstantBilling::DELIVERY_RATE];
                        } else {
                            $deliveryType = 10;
                            $minimumOrderValue
                                = isset($rateService[\UPS\Shipping\Helper\ConstantBilling::MIN_ORDER_VALUE])
                                ? $rateService[\UPS\Shipping\Helper\ConstantBilling::MIN_ORDER_VALUE] : 0;
                            $deliveryValue = (isset($rateService[\UPS\Shipping\Helper\ConstantBilling::DELIVERY_RATE]))
                            ? $rateService[\UPS\Shipping\Helper\ConstantBilling::DELIVERY_RATE] : 0;
                        }
                        $arrDelivaryRates[]  = [
                            \UPS\Shipping\Helper\ConstantBilling::SERVICE_KEY_DELIVERY
                                => $service[\UPS\Shipping\Helper\ConstantBilling::SERVICE_KEY_DELIVERY],
                            'rate_type' => $deliveryType,
                            \UPS\Shipping\Helper\ConstantBilling::SERVICE_TYPE
                                => ($service[\UPS\Shipping\Helper\ConstantBilling::SERVICE_TYPE] == 'AP') ? 10 : 20,
                            \UPS\Shipping\Helper\ConstantBilling::SERVICE_NAME => $stringServiceName,
                            \UPS\Shipping\Helper\ConstantBilling::RATE_CODE
                                => $service[\UPS\Shipping\Helper\ConstantBilling::RATE_CODE],
                            \UPS\Shipping\Helper\ConstantBilling::MIN_ORDER_VALUE => (float)$minimumOrderValue,
                            \UPS\Shipping\Helper\ConstantBilling::DELIVERY_RATE => (float)$deliveryValue,
                            'realtimeValue' => (float)$realtimeValue,
                        ];
                    }
                }
            }
        }
    }

    /**
     * Function getServiceShippingArray
     *
     * @return string
     */
    public function getServiceShippingArray()
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
        return $arrShippingService;
    }

    /**
     * Function getMoreNumberInfo
     *
     * @param string $listAccount        //The accountDefault
     * @param string $merchantKey        //The accountDefault
     * @param string $websiteMerchant    //The accountDefault
     * @param string $currencyCode       //The currencyCode
     * @param string $version            //The version
     * @param string $defaultPackageName //The defaultPackageName
     * @param string $weight             //The weight
     * @param string $weightUnit         //The weightUnit
     * @param string $length             //The length
     * @param string $width              //The width
     * @param string $height             //The height
     * @param string $dimensionUnit      //The dimensionUnit
     *
     * @return array
     */
    public function getMoreNumberInfo($listAccount, $merchantKey, $websiteMerchant, $currencyCode, $version, $defaultPackageName, $weight, $weightUnit, $length, $width, $height, $dimensionUnit)
    {
        $accountNumberInfo = [];
        foreach ($listAccount as $value) {
            if (isset($value['account_default']) && $value['account_default'] != '1') {
                $address = (isset($value[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_1])
                ? $value[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_1] : '');
                if (isset($value[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_2])
                    && !empty($value[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_2])
                ) {
                    $address .= ', ' .  $value[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_2];
                }
                if (isset($value[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_3])
                    && !empty($value[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_3])
                ) {
                    $address .= ', ' . $value[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_3];
                }
                $accountNumberItem = [
                    'merchantKey'   => $merchantKey,
                    'accountNumber' => (isset($value[\UPS\Shipping\Helper\ConstantAccount::UPS_ACCOUNT_NUMBER])
                    ? $value[\UPS\Shipping\Helper\ConstantAccount::UPS_ACCOUNT_NUMBER] : ''),
                    'companyName'   => $value[\UPS\Shipping\Helper\ConstantAccount::COMPANY],
                    'joiningDate'   => date('d/m/Y'),
                    'website'       => $websiteMerchant,
                    'currencyCode'  => $currencyCode,
                    'status'        => 10,
                    'platform'      => 30,
                    'version'       => $version,
                    'address'       => $address,
                    'postalCode'    => $value[\UPS\Shipping\Helper\ConstantAccount::POST_CODE],
                    'city'          => $value['city'],
                    \UPS\Shipping\Helper\ConstantAccount::COUNTRY
                    => $value[\UPS\Shipping\Helper\ConstantAccount::COUNTRY],
                    \UPS\Shipping\Helper\ConstantPackage::PACKAGE_NAME  => $defaultPackageName,
                    \UPS\Shipping\Helper\ConstantPackage::WEIGHT        => $weight,
                    \UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT   => $weightUnit,
                    \UPS\Shipping\Helper\ConstantPackage::LENGTH        => $length,
                    \UPS\Shipping\Helper\ConstantPackage::WIDTH         => $width,
                    \UPS\Shipping\Helper\ConstantPackage::HEIGHT        => $height,
                    \UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION => $dimensionUnit,
                    'isFirstAccount'=> 0
                ];
                $accountNumberInfo[] = $accountNumberItem;
            }
        }
        return $accountNumberInfo;
    }

    /**
     * Function getOneNumberInfo
     *
     * @param string $accountDefault  //The accountDefault
     * @param string $merchantKey     //The merchantKey
     * @param string $websiteMerchant //The websiteMerchant
     * @param string $currencyCode    //The currencyCode
     * @param string $version         //The version
     *
     * @return array
     */
    public function getOneNumberInfo($accountDefault, $merchantKey, $websiteMerchant, $currencyCode, $version)
    {
        $accountNumberInfo = [];
        if (!empty($accountDefault)) {
            $address = (isset($accountDefault[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_1])
            ? $accountDefault[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_1] : '');
            if (isset($accountDefault[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_2])
                && !empty($accountDefault[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_2])
            ) {
                $address .= ', ' . $accountDefault[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_2];
            }
            if (isset($accountDefault[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_3])
                && !empty($accountDefault[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_3])
            ) {
                $address .= ', ' . $accountDefault[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_3];
            }
            $accountNumberInfo = [
                'merchantKey'   => $merchantKey,
                'accountNumber' => (isset($accountDefault[\UPS\Shipping\Helper\ConstantAccount::UPS_ACCOUNT_NUMBER])
                ? $accountDefault[\UPS\Shipping\Helper\ConstantAccount::UPS_ACCOUNT_NUMBER] : ''),
                'companyName'   => (isset($accountDefault[\UPS\Shipping\Helper\ConstantAccount::COMPANY])
                ? $accountDefault[\UPS\Shipping\Helper\ConstantAccount::COMPANY] : ''),
                'joiningDate'   => date('d/m/Y'),
                'website'       => str_replace('https://', '', str_replace('http://', '', $websiteMerchant)),
                'currencyCode'  => $currencyCode,
                'status'        => 10,
                'platform'      => 30, // magento
                'version'       => $version,
                'address'       => $address,
                'postalCode'    => (isset($accountDefault[\UPS\Shipping\Helper\ConstantAccount::POST_CODE]) ? $accountDefault[\UPS\Shipping\Helper\ConstantAccount::POST_CODE] : ''),
                'city'          => (isset($accountDefault['city']) ? $accountDefault['city'] : ''),
                \UPS\Shipping\Helper\ConstantAccount::COUNTRY
                => (isset($accountDefault[\UPS\Shipping\Helper\ConstantAccount::COUNTRY])
                ? $accountDefault[\UPS\Shipping\Helper\ConstantAccount::COUNTRY] : ''),
                'isFirstAccount'=> 1
            ];
        }
        return $accountNumberInfo;
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
            $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN,  $valueSecurityToken);

            foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
                $this->cacheTypeList->cleanType($type);
            }
            $resultCallHandshakeApi = true;
        }
        return $resultCallHandshakeApi;
    }
}
