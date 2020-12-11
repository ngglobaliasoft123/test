<?php
/**
 * Order file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Controller\Adminhtml\Shipment;
/**
 * Order class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Order extends \UPS\Shipping\Controller\Adminhtml\AbstractController
{
    /**
     * Order execute
     *
     * @return null
     */
    public function execute()
    {
        $this->startExcute();
        if ($this->getRequest()->isAjax()) {
            $result = $this->resultJsonFactory->create();
            switch ($this->getRequest()->getParam('method')) {
            case 'getStates':
                if ($this->request->isPost()) {
                    $state = [];
                    $getState = $this->modelOrder->getStateMagento();
                    foreach ($getState as $item => $value) {
                        $arrayState = ['country_code' => $value['country_id'],
                        'state_name' => $value['default_name'], 'state_code' => $value['code']];
                        array_push($state, $arrayState);
                    }
                    return $result->setData(['states' => $state]);
                } else {
                    http_response_code(404);
                }
                break;
            default:
                break;
            }
        } else {
            $currentVersion = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_READY_PLUGIN_VERSION);
            if (empty($currentVersion) || version_compare($currentVersion, \UPS\Shipping\Helper\Config::VERSION_PLUGIN) < 0) {
                if (empty($currentVersion)) {
                    //save merchant/plugin/exist
                    $dataPluginVersion = [
                        'scope' => 'default',
                        'scope_id' => 0,
                        'path' => \UPS\Shipping\Helper\Config::UPS_READY_PLUGIN_VERSION,
                        'value' => \UPS\Shipping\Helper\Config::VERSION_PLUGIN,
                    ];
                    $this->setup->getConnection()->insertOnDuplicate($this->setup->getTable('core_config_data'), $dataPluginVersion);
                }
                $value['method'] = 'callUpgradePluginVersion';
                $value['id'] = 0;
                $dataVersion[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN] = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                $dataVersion[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY] = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY);
                $this->runCallManager($value, $dataVersion);
            }

            //offPluginManager 2019-03-18
            $checkTransferExist = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_PLUGIN_MERCHANTINFO_EXIST);
            if ($checkTransferExist == 0 || empty($checkTransferExist)) {
                if (empty($checkTransferExist)) {
                    //save merchant/plugin/exist
                    $dataPlugin = [
                        'scope' => 'default',
                        'scope_id' => 0,
                        'path' => \UPS\Shipping\Helper\Config::UPS_PLUGIN_MERCHANTINFO_EXIST,
                        'value' => 0,
                    ];
                    $this->setup->getConnection()->insertOnDuplicate($this->setup->getTable('core_config_data'), $dataPlugin);
                    $this->runHandshake();
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
            return $this->pageFactory->create();
        }
    }

    /**
     * Order runHandshake
     *
     * @return string
     */
    public function runHandshake()
    {
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

    /**
     * Order TransferMerchantInfoAPI
     *
     * @param string $default //The default
     *
     * @return boolean
     */
    public function callTransferMerchantInfo($default = 0) //default = 0 account defalu , = 1 account success
    {
        $merchantKey = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY);
        $websiteMerchant = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_WEBSECUREURL);
        $websiteMerchant = str_replace('https://', '', str_replace('http://', '', $websiteMerchant));
        $version = \UPS\Shipping\Helper\Config::VERSION_PLUGIN;
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();

        $defaultPackageName = 'default package';
        $weight             = '0';
        $weightUnit         = 'kgs';
        $length             = '0';
        $width              = '0';
        $height             = '0';
        $dimensionUnit      = 'cm';

        // Get Account information
        $accountNumberInfo = [];
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
        $arrShippingService = $this->getServiceShippingArray();
        // Shipping Services and Delivery Rates
        $listShippingServices = [];
        $arrDelivaryRates = [];
        $this->getRatesServices($arrShippingService, $listShippingServices, $arrDelivaryRates);
        // Default package
        $jsonDefaultPackage = $this->modelPackage->getListPackage();

        if (!empty($jsonDefaultPackage)) {
            $defaultPackage = (isset($jsonDefaultPackage[0])) ? $jsonDefaultPackage[0] : [];
            if (!empty($defaultPackage)) {
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
            }
        }
        $defaultPackages = [
            \UPS\Shipping\Helper\ConstantPackage::PACKAGE_NAME => $defaultPackageName,
            \UPS\Shipping\Helper\ConstantPackage::WEIGHT => $weight,
            \UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT => $weightUnit,
            \UPS\Shipping\Helper\ConstantPackage::LENGTH => $length,
            \UPS\Shipping\Helper\ConstantPackage::WIDTH => $width,
            \UPS\Shipping\Helper\ConstantPackage::HEIGHT => $height,
            \UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION => $dimensionUnit,
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
        // UPS_SERVICE_LONG_SECURITY_TOKEN
        $this->runTransferMerchantInfo($accountNumberInfo, $defaultPackages, $arrAccessorial, $listShippingServices, $arrDelivaryRates);
        return true;
    }

    /**
     * Order runTransferMerchantInfo
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
     * Order getRatesServices
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
     * Order getServiceNameString
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
     * Order getServiceShippingArray
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
     * Order getMoreNumberInfo
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
                    'joiningDate'   => date('m/d/Y'),
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
     * Order getOneNumberInfo
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
                'joiningDate'   => date('m/d/Y'),
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
     * Order callAPILicense
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
            $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN,  $valueSecurityToken);
            $this->checkoutSession->setSecurityTokenValue($valueSecurityToken);
            foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
                $this->cacheTypeList->cleanType($type);
            }
            $resultCallHandshakeApi = true;
        }
        return $resultCallHandshakeApi;
    }
}
