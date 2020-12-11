<?php
/**
 * Carrier file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Framework\Setup\Lists;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
/**
 * Carrier class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Carrier extends \Magento\Shipping\Model\Carrier\AbstractCarrier
{
    protected $rateRequest;
    protected $checkoutSession;
    protected $apiLocator;
    protected $apiRate;
    protected $modelService;
    protected $modelDeliveryRates;
    protected $quoteFactory;
    protected $modelAccount;
    protected $modelPackage;
    protected $regionFactory;
    protected $lists;
    protected $logger;
    protected $modelAccessorial;
    protected $modelBackupRate;
    protected $timezone;
    protected $storeManager;
    protected $currencyFactory;
    protected $cart;
    protected $priceHelper;
    protected $dateTime;
    protected $packageDimension;
    protected $_code = \UPS\Shipping\Helper\ConstantCarrier::UPS_CODE;

    /**
     * Carrier __construct
     *
     * @param string   $scopeConfig        //The scopeConfig
     * @param string   $rateErrorFactory   //The rateErrorFactory
     * @param string   $logger             //The logger
     * @param string   $rateResultFactory  //The rateResultFactory
     * @param string   $rateMethodFactory  //The rateMethodFactory
     * @param string   $storeManager       //The storeManager
     * @param string   $currencyFactory    //The currencyFactory
     * @param string   $checkoutSession    //The checkoutSession
     * @param string   $apiLocator         //The apiLocator
     * @param string   $apiRate            //The apiRate
     * @param string   $modelService       //The modelService
     * @param string   $modelDeliveryRates //The modelDeliveryRates
     * @param string   $quoteFactory       //The quoteFactory
     * @param string   $modelAccount       //The modelAccount
     * @param string   $modelPackage       //The modelPackage
     * @param string   $regionFactory      //The regionFactory
     * @param string   $modelAccessorial   //The modelAccessorial
     * @param string   $modelBackupRate    //The  modelBackupRate
     * @param string   $packageDimension   //The pacakge dimension
     * @param string   $timezone           //The timezone
     * @param string   $priceCurrency      //The priceCurrency
     * @param string   $priceHelper        //The priceHelper
     * @param string   $lists              //The lists
     * @param DateTime $dateTime           //The dataTime
     * @param string   $data               //The data
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \UPS\Shipping\API\Locator $apiLocator,
        \UPS\Shipping\API\Rate $apiRate,
        \UPS\Shipping\Model\Service $modelService,
        \UPS\Shipping\Model\DeliveryRates $modelDeliveryRates,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \UPS\Shipping\Model\Account $modelAccount,
        \UPS\Shipping\Model\Package $modelPackage,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \UPS\Shipping\Model\Accessorial $modelAccessorial,
        \UPS\Shipping\Model\Backuprate $modelBackupRate,
        \UPS\Shipping\Model\PackageDimension $packageDimension,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        Lists $lists,
        DateTime $dateTime,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->logger = $logger;
        $this->priceHelper = $priceHelper;
        $this->checkoutSession = $checkoutSession;
        $this->apiLocator = $apiLocator;
        $this->apiRate = $apiRate;
        $this->modelService = $modelService;
        $this->modelDeliveryRates = $modelDeliveryRates;
        $this->quoteFactory = $quoteFactory;
        $this->modelAccount = $modelAccount;
        $this->modelPackage = $modelPackage;
        $this->regionFactory = $regionFactory;
        $this->modelAccessorial = $modelAccessorial;
        $this->modelBackupRate = $modelBackupRate;
        $this->packageDimension = $packageDimension;
        $this->priceCurrency = $priceCurrency;
        $this->timezone = $timezone;
        $this->lists = $lists;
        $this->dateTime = $dateTime;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Carrier getAllowedMethods
     *
     * @return array $data
     */
    public function getAllowedMethods()
    {
        return [$this->_code => 'UPS'];
    }

    /**
     * Carrier isActive
     *
     * @return array $data
     */
    public function isActive()
    {
        return $this->getConfigData('active');
    }

    /**
     * Carrier getRateRequest
     *
     * @return array $data
     */
    public function getRateRequest()
    {
        return $this->_rateRequest;
    }

    /**
     * Carrier collectRates
     *
     * @param string $request //The request
     *
     * @return array $data
     */
    public function collectRates(RateRequest $request)
    {
        $this->_rateRequest = $request;
        if (!$this->getConfigData('active')) {
            return false;
        }

        $result = $this->rateResultFactory->create();

        $method = $this->rateMethodFactory->create();

        // save seesion
        $this->_saveSession($this->_rateRequest);

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('name'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('title'));
        $amount = $this->getConfigData('fixed_price');

        $getCheapestFee = $this->checkoutSession->getCheapestFee();
        if ($getCheapestFee > -1) {
            $amount = $getCheapestFee;
        }
        $getSelectedFee = $this->checkoutSession->getSelectedFee();
        if ($getSelectedFee > -1) {
            $amount = $getSelectedFee;
        }
        $currentCurrency = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        $baseCurrency = $this->storeManager->getStore()->getBaseCurrency()->getCode();
        $rateFrom = (float)$this->currencyFactory->create()->load($baseCurrency)->getRate($currentCurrency);
        if ($rateFrom > 0) {
            $amount /= $rateFrom;
        }
        if (($getCheapestFee > -1 || $getSelectedFee > -1)) {
            $method->setPrice($amount);
            $result->append($method);
            return $result;
        } else {
            return null;
        }
    }

    /**
     * Carrier _saveSession
     * save session
     *
     * @param string $request //The request
     *
     * @return array $data
     */
    private function _saveSession($request)
    {
        $cuttime = $this->_scopeConfig->getValue(\UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_CUT_OFF_TIME);
        $cCurency = \UPS\Shipping\Helper\Config::DELIVERY_RATES_CURRENCY_DEFAULT;
        $signDefaultCurrency = $this->_scopeConfig->getValue($cCurency);

        $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();

        $listDeliveryRates = [];
        $cAccessPoint = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_ACCESS_POINT;
        $cAddress = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_SHIPPING_ADDRESS;
        $cDefault = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_SET_DEFAULT;
        $enableServiceAP = $this->_scopeConfig->getValue($cAccessPoint);
        $enableServiceADD = $this->_scopeConfig->getValue($cAddress);
        // select service default
        $serviceDefault = $this->_scopeConfig->getValue($cDefault);
        $sessionEnableAP = $this->checkoutSession->getEnableServiceAP();
        $sessionEnableADD = $this->checkoutSession->getEnableServiceADD();
        $sessionDefaultEnable = $this->checkoutSession->getDefaultService();
        $clearSession = $this->checkoutSession->getClearSession();
        $clearSession = $this->getClearSession($clearSession);
        // reset session when select new address
        if ($enableServiceAP != $sessionEnableAP || $enableServiceADD != $sessionEnableADD || $clearSession == '1') {
            $this->checkoutSession->unsSelectedShippingService();
            $this->checkoutSession->unsSelectedAPAddress();
            $this->checkoutSession->unsSelectedFee();
            $this->checkoutSession->unsCheapestFee();
            $this->checkoutSession->unsListServiceTypes();
            $this->checkoutSession->unsDefaultService();
            $this->checkoutSession->unsEnableServiceAP();
            $this->checkoutSession->unsEnableServiceADD();
            $this->checkoutSession->unsClearSession();
        }
        if ($enableServiceAP == '1' || $enableServiceADD == '1') {
            $listDeliveryRatesFromDatas = $this->modelDeliveryRates->getDataDeliveryRates();
            $listDeliveryRates = $this->_getListDeliveryValue($listDeliveryRatesFromDatas);
        }
        $serviceAP = [];
        $serviceADD = [];
        $cheapestFee = 0;
        $cheapestServiceId = 0;
        // array service
        $arrServiceFees = [];
        $arrServiceTypes = [];
        $params = [
            'city' => $request->getDestCity(),
            'company' => $request->getDestCompany(),
            \UPS\Shipping\Helper\ConstantCarrier::COUNTRY_ID => $request->getDestCountryId(),
            \UPS\Shipping\Helper\ConstantCarrier::FIRSTNAME => $request->getDestFirstname(),
            \UPS\Shipping\Helper\ConstantCarrier::LASTNAME => $request->getDestLastname(),
            \UPS\Shipping\Helper\ConstantCarrier::POSTCODE => $request->getDestPostcode(),
            'region' => $request->getDestRegion(),
            \UPS\Shipping\Helper\ConstantCarrier::REGION_CODE => $request->getDestRegionCode(),
            \UPS\Shipping\Helper\ConstantCarrier::REGION_ID => $request->getDestRegionId(),
            'street' => $request->getDestStreet(),
            'telphone' => $request->getDestTelphone()
        ];
        $regionCode = (!empty($request->getDestRegionCode())) ? $request->getDestRegionCode() : '';
        $fullAddress = $request->getDestStreet() . ', ' . $request->getDestCity() . ', ' . $regionCode . ' '
        . $request->getDestPostcode();
        $params[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS1] = $request->getDestStreet();
        $params[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS2] = '';
        $params[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS3] = '';
        if (!empty($params)) {
            $accessorials = [];
            $listAccessorials = $this->modelAccessorial->getListAccessorialActive();
            $firstLocation = '';
            if (!empty($listAccessorials)) {
                $accessorials = json_decode($listAccessorials, true);
            }
            // package dimension
            $this->checkoutSession->setPackageDimension(json_encode($this->packageDimension->getShippingPackage()));
            if ($enableServiceAP == '1') {
                // locator API
                $address = [
                    'fullAddress' => $fullAddress,
                    'countryCode' => $params[\UPS\Shipping\Helper\ConstantCarrier::COUNTRY_ID],
                    'Locale' => $this->countrytolocale($params[\UPS\Shipping\Helper\ConstantCarrier::COUNTRY_ID]),
                    'UnitOfMeasurement' => 'KM',
                    'MaximumListSize' => '1',
                    'nearby' => '100',
                ];
                if ('us' == strtolower($params[\UPS\Shipping\Helper\ConstantCarrier::COUNTRY_ID])) {
                    $address['UnitOfMeasurement'] = 'MI';
                    //$address['nearby'] = '62.14';
                }
                $response = $this->apiLocator->loadAddress($address);
                $response = json_decode($response, true);
                $cNumberAP = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_CHOOSE_ACCOUNT_NUMBER_AP;
                if ($response && isset($response[\UPS\Shipping\Helper\ConstantCarrier::LOCATORRESPONSE])
                    && isset($response[\UPS\Shipping\Helper\ConstantCarrier::LOCATORRESPONSE][\UPS\Shipping\Helper\ConstantCarrier::RESPONSE])
                    && isset($response[\UPS\Shipping\Helper\ConstantCarrier::LOCATORRESPONSE][\UPS\Shipping\Helper\ConstantCarrier::RESPONSE]['ResponseStatusCode'])
                    && $response[\UPS\Shipping\Helper\ConstantCarrier::LOCATORRESPONSE][\UPS\Shipping\Helper\ConstantCarrier::RESPONSE]['ResponseStatusCode'] == '0'
                ) {
                    $serviceDefault = 0;
                } else {
                    if ($response && isset($response[\UPS\Shipping\Helper\ConstantCarrier::LOCATORRESPONSE])
                        && isset($response[\UPS\Shipping\Helper\ConstantCarrier::LOCATORRESPONSE][\UPS\Shipping\Helper\ConstantCarrier::SEARCHRESULTS])
                        && isset($response[\UPS\Shipping\Helper\ConstantCarrier::LOCATORRESPONSE][\UPS\Shipping\Helper\ConstantCarrier::SEARCHRESULTS][\UPS\Shipping\Helper\ConstantCarrier::DROPLOCATION])
                        && isset($response[\UPS\Shipping\Helper\ConstantCarrier::LOCATORRESPONSE][\UPS\Shipping\Helper\ConstantCarrier::SEARCHRESULTS][\UPS\Shipping\Helper\ConstantCarrier::DROPLOCATION])
                        && isset($response[\UPS\Shipping\Helper\ConstantCarrier::LOCATORRESPONSE][\UPS\Shipping\Helper\ConstantCarrier::SEARCHRESULTS][\UPS\Shipping\Helper\ConstantCarrier::DROPLOCATION]['AddressKeyFormat'])
                    ) {
                        $firstLocation = $response[\UPS\Shipping\Helper\ConstantCarrier::LOCATORRESPONSE]
                        [\UPS\Shipping\Helper\ConstantCarrier::SEARCHRESULTS]
                        [\UPS\Shipping\Helper\ConstantCarrier::DROPLOCATION]['AddressKeyFormat'];
                        $idAccount = $this->_scopeConfig->getValue($cNumberAP);
                        // process valid AP Service
                        $countryCode = $this->_scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE);
                        $listAPServices = $this->modelService->getSelectedServices('AP', $countryCode);
                        $serviceAP = $this->_getShippingService($accessorials, $listAPServices, $params, $cuttime, $listDeliveryRates, $currencyCode, 'AP', $firstLocation, $idAccount);
                    }
                }
            }
            // get list selected shipping services by ADD
            if ($enableServiceADD == '1') {
                $this->setServiceADD($serviceADD, $accessorials, $params, $cuttime, $currencyCode, $listDeliveryRates, $firstLocation);
            }

            $arrCheapest = array_merge($serviceAP, $serviceADD);
            // get list fee and service type of shipping
            $this->setServiceTypeFees($arrCheapest, $arrServiceFees, $arrServiceTypes);
            $countryLower = strtolower($this->_scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE));
            // select cheapest fee
            // select AP default then only AP
            $arrCheapest = $this->getCheapestValue($arrCheapest, $serviceDefault, $enableServiceAP, $serviceAP, $enableServiceADD, $serviceADD, $countryLower);

            // get cheapest fee and response service
            $cheapestFee = -1;
            $this->resetCheapestFee($cheapestFee, $arrCheapest, $cheapestServiceId);
            $cheapestFee = $this->changeCurrencyFormat($cheapestFee);

            // address services,
            $this->checkoutSession->setAccessPointServices(json_encode($serviceAP));
            // address services,
            $this->checkoutSession->setAddressServices(json_encode($serviceADD));
            // set selected default [AP or Address],
            $this->checkoutSession->setDefaultService($serviceDefault);
            // set default [AP Services],
            $this->checkoutSession->setEnableServiceAP($enableServiceAP);
            // set default [AD Services],
            $this->checkoutSession->setEnableServiceADD($enableServiceADD);
            // cheapest fee
            $this->checkoutSession->setCheapestFee($cheapestFee);
            // selected service, id
            $shippingService = $this->checkoutSession->getSelectedShippingService();
            // case arrServiceTypes miss index because change address [Ship Here]
            $this->setSelectedShippingServiceSession($shippingService, $arrServiceTypes, $arrCheapest, $cheapestServiceId);

            $selectedShippingFee = -1;
            $this->setSelectedShippingFee($selectedShippingFee, $arrServiceFees, $shippingService);
            $this->checkoutSession->setSelectedFee($selectedShippingFee);
            // array service fees
            $this->checkoutSession->setListServices(json_encode($arrServiceFees));
            // array service types
            $this->checkoutSession->setListServiceTypes(json_encode($arrServiceTypes));
        }
    }

    /**
     * Carrier setSelectedShippingFee
     *
     * @param string $selectedShippingFee //The selectedShippingFee
     * @param string $arrServiceFees      //The arrServiceFees
     * @param string $shippingService     //The shippingService
     *
     * @return null
     */
    public function setSelectedShippingFee(&$selectedShippingFee, $arrServiceFees, $shippingService)
    {
        if (!empty($arrServiceFees) && isset($arrServiceFees[$shippingService])) {
            $selectedShippingFee = $this->changeCurrencyFormat($arrServiceFees[$shippingService]);
        }
    }

    /**
     * Carrier setSelectedShippingServiceSession
     *
     * @param string $shippingService   //The shippingService
     * @param string $arrServiceTypes   //The arrServiceTypes
     * @param string $arrCheapest       //The arrCheapest
     * @param string $cheapestServiceId //The cheapestServiceId
     *
     * @return null
     */
    public function setSelectedShippingServiceSession($shippingService, $arrServiceTypes, $arrCheapest, $cheapestServiceId)
    {
        if ($shippingService < 1 || (!isset($arrServiceTypes[$shippingService]) && !empty($arrCheapest))) {
            $shippingService = $cheapestServiceId;
            $this->checkoutSession->setSelectedShippingService($shippingService);
        }
    }

    /**
     * Carrier resetCheapestFee
     *
     * @param string $cheapestFee       //The cheapestFee
     * @param string $arrCheapest       //The arrCheapest
     * @param string $cheapestServiceId //The cheapestServiceId
     *
     * @return null
     */
    public function resetCheapestFee(&$cheapestFee, $arrCheapest, &$cheapestServiceId)
    {
        if (!empty($arrCheapest)) {
            $cheapestFee = $arrCheapest[0][\UPS\Shipping\Helper\ConstantCarrier::SHIPPINGFEEVALUE];
            $cheapestServiceId = $arrCheapest[0]['id'];
            foreach ($arrCheapest as $service) {
                if ($service[\UPS\Shipping\Helper\ConstantCarrier::SHIPPINGFEEVALUE] < $cheapestFee) {
                    $cheapestFee = $service[\UPS\Shipping\Helper\ConstantCarrier::SHIPPINGFEEVALUE];
                    $cheapestServiceId = $service['id'];
                }
            }
        }
    }

    /**
     * Carrier setServiceTypeFees
     *
     * @param string $arrCheapest     //The arrCheapest
     * @param string $arrServiceFees  //The arrServiceFees
     * @param string $arrServiceTypes //The arrServiceTypes
     *
     * @return null
     */
    public function setServiceTypeFees($arrCheapest, &$arrServiceFees, &$arrServiceTypes)
    {
        if (!empty($arrCheapest)) {
            foreach ($arrCheapest as $service) {
                $arrServiceFees[$service['id']] = $service[\UPS\Shipping\Helper\ConstantCarrier::SHIPPINGFEEVALUE];
                $arrServiceTypes[$service['id']] = $service['service_type'];
            }
        }
    }

    /**
     * Carrier setServiceADD
     *
     * @param string $serviceADD        //The serviceADD
     * @param string $accessorials      //The accessorials
     * @param string $params            //The params
     * @param string $cuttime           //The cuttime
     * @param string $currencyCode      //The currencyCode
     * @param string $listDeliveryRates //The listDeliveryRates
     * @param string $firstLocation     //The firstLocation
     *
     * @return array $data
     */
    public function setServiceADD(&$serviceADD, $accessorials, $params, $cuttime, $currencyCode, $listDeliveryRates, $firstLocation)
    {
        $cNumberAdd = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_CHOOSE_ACCOUNT_NUMBER_ADD;
        $idAccount = $this->_scopeConfig->getValue($cNumberAdd);
        $countryCode = $this->_scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE);
        $listADDServices = $this->modelService->getSelectedServices('ADD', $countryCode);
        $serviceADD = $this->_getShippingService($accessorials, $listADDServices, $params, $cuttime, $listDeliveryRates, $currencyCode, 'ADD', $firstLocation, $idAccount);
    }

    /**
     * Carrier getCheapestValue
     *
     * @param string $arrCheapest     //The arrCheapest
     * @param string $serviceDefault  //The serviceDefault
     * @param string $enableServiceAP //The enableServiceAP
     * @param string $serviceAP       //The serviceAP
     *
     * @return array $data
     */
    public function getCheapestValue($arrCheapest, $serviceDefault, $enableServiceAP, $serviceAP, $enableServiceADD, $serviceADD, $countryLower)
    {
        $returnData = $arrCheapest;
        if ($serviceDefault == '1' && $enableServiceAP == '1' && !empty($serviceAP)) {
            $returnData = $serviceAP;
        }
        // US and no default AP then cheapest in ADD services
        if ($serviceDefault == '0' && $enableServiceADD == '1' && !empty($serviceADD) && 'us' == $countryLower) {
            $returnData = $serviceADD;
        }
        return $returnData;
    }

    /**
     * Carrier getClearSession
     *
     * @param string $clearSession //The clearSession
     *
     * @return array $data
     */
    public function getClearSession($clearSession)
    {
        $returnData = $clearSession;
        if (empty($clearSession) && ($clearSession != '0' || $clearSession != 0)) {
            $returnData = '1';
        }
        return $returnData;
    }

    /**
     * Carrier _getShOPServices
     *
     * @param string $listUPSServices //The listUPSServices
     *
     * @return array $data
     */
    private function _getShOPServices($listUPSServices)
    {
        $arrUPSServicesTemp = [];
        if (!empty($listUPSServices)) {
            $upsServices = json_decode($listUPSServices, true);
            $arrUPSServicesTemp = [];
            if (isset($upsServices[\UPS\Shipping\Helper\ConstantCarrier::RATERESPONSE][\UPS\Shipping\Helper\ConstantCarrier::RATEDSHIPMENT])
                && !empty($upsServices[\UPS\Shipping\Helper\ConstantCarrier::RATERESPONSE][\UPS\Shipping\Helper\ConstantCarrier::RATEDSHIPMENT])
            ) {
                foreach ($upsServices[\UPS\Shipping\Helper\ConstantCarrier::RATERESPONSE]
                [\UPS\Shipping\Helper\ConstantCarrier::RATEDSHIPMENT] as $serviceTemp) {
                    $arrUPSServicesTemp[$serviceTemp[\UPS\Shipping\Helper\ConstantCarrier::SERVICE]['Code']]
                        = $serviceTemp;
                }
            }
        }
        return $arrUPSServicesTemp;
    }

    /**
     * Carrier _getListDeliveryValue
     * get list delivery value
     *
     * @param string $listDeliveryRatesFromDatas //The listDeliveryRatesFromDatas
     *
     * @return array $data
     */
    private function _getListDeliveryValue($listDeliveryRatesFromDatas)
    {
        $listDeliveryRates = [];
        // change value at here
        if (!empty($listDeliveryRatesFromDatas)) {
            $serviceId = '';
            $countListDeliveryRatesFromDatas = count($listDeliveryRatesFromDatas);
            if ($countListDeliveryRatesFromDatas != 1 && isset($listDeliveryRatesFromDatas[0][\UPS\Shipping\Helper\ConstantCarrier::SERVICE_ID])) {
                $serviceId = $listDeliveryRatesFromDatas[0][\UPS\Shipping\Helper\ConstantCarrier::SERVICE_ID];
            }
            $saveDeliveryRate = $listDeliveryRatesFromDatas[0];
            $orderValues = [];
            $orderRates = [];
            if ($countListDeliveryRatesFromDatas > 1) {
                $listDeliveryRatesFromDatas[] = $listDeliveryRatesFromDatas[0]; // final not excute
            }
            foreach ($listDeliveryRatesFromDatas as $deliveryRate) {
                // differenent service_id
                if ($serviceId != $deliveryRate[\UPS\Shipping\Helper\ConstantCarrier::SERVICE_ID]) {
                    $saveDeliveryRate['orderValues'] = $orderValues;
                    $saveDeliveryRate[\UPS\Shipping\Helper\ConstantCarrier::ORDERRATES] = $orderRates;
                    $orderValues = [];
                    $orderRates = [];
                    if ($countListDeliveryRatesFromDatas == 1 && isset($listDeliveryRatesFromDatas[0][\UPS\Shipping\Helper\ConstantCarrier::SERVICE_ID])) {
                        $serviceId = $listDeliveryRatesFromDatas[0][\UPS\Shipping\Helper\ConstantCarrier::SERVICE_ID];
                    }
                    $listDeliveryRates[$serviceId] = $saveDeliveryRate;
                    // reset
                    $serviceId = $deliveryRate[\UPS\Shipping\Helper\ConstantCarrier::SERVICE_ID];
                    $orderValues[] = $deliveryRate['min_order_value'];
                    $orderRates[] = $deliveryRate['delivery_rate'];
                    $saveDeliveryRate = $deliveryRate;
                } else { // same service_id
                    $orderValues[] = $deliveryRate['min_order_value'];
                    $orderRates[] = $deliveryRate['delivery_rate'];
                }
            }
        }
        return $listDeliveryRates;
    }

    /**
     * Carrier _getShippingService
     * get all shipping service
     *
     * @param string $accessorials      //The accessorials
     * @param string $listShipServices  //The listShipServices
     * @param string $params            //The params
     * @param string $cuttime           //The cuttime
     * @param string $listDeliveryRates //The listDeliveryRates
     * @param string $currencyCode      //The currencyCode
     * @param string $shippingType      //The shippingType
     * @param string $firstLocation     //The firstLocation
     * @param string $idAccount         //The idAccount
     *
     * @return array $data
     */
    private function _getShippingService($accessorials, $listShipServices, $params, $cuttime, $listDeliveryRates, $currencyCode, $shippingType, $firstLocation = [], $idAccount = '')
    {
        $listShippingService = [];
        if (!empty($listShipServices)) {
            $orderTotalPrice = $this->checkoutSession->getQuote()->getSubtotal();
            // change total price to base currency
            $stringRateDate = '';
            $responseRateAPI = $this->_callRateAPI($accessorials, $params, [], $orderTotalPrice, $cuttime, 'ShopTimeInTransit', $shippingType, $currencyCode, \UPS\Shipping\Helper\ConstantCarrier::ESHOPPER, $firstLocation, $idAccount);
            $responseRateAPI = json_decode($responseRateAPI, true);
            if ($shippingType = 'ADD' && $responseRateAPI && isset($responseRateAPI['Fault']['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['Code'])) {
                $rateErrorCode = $responseRateAPI['Fault']['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['Code'];
                $packageSettingType = $this->_scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_PACKAGE_DIMENSIONS);
                if (in_array($rateErrorCode, \UPS\Shipping\Helper\Config::BACKUP_RATE_ERROR_CODE) && (2 == $packageSettingType)) {
                    $listBackupRate = $this->modelBackupRate->getListBackuprate();
                    foreach ($listShipServices as $shippingServiceItem) {
                        foreach ($listBackupRate as $itemBackupRate) {
                            if ($shippingServiceItem['id'] == $itemBackupRate['service_id']) {
                                // calculate Shipping Fee
                                $convertFallbackRate = $this->convertPrice($itemBackupRate['fallback_rate'], $this->getCurrencyCode());
                                $shippingServiceItem['shippingFeeValue'] = $convertFallbackRate;
                                $shippingServiceItem['splitShippingFee'] = $this->formatCurrency($convertFallbackRate);
                                $shippingServiceItem['shippingFeeString'] = '';
                                $listShippingService[] = $shippingServiceItem;
                            }
                        }
                    }
                }
            } elseif ($responseRateAPI && isset($responseRateAPI[\UPS\Shipping\Helper\ConstantCarrier::RATERESPONSE])
                && isset($responseRateAPI[\UPS\Shipping\Helper\ConstantCarrier::RATERESPONSE][\UPS\Shipping\Helper\ConstantCarrier::RESPONSE])
                && isset($responseRateAPI[\UPS\Shipping\Helper\ConstantCarrier::RATERESPONSE][\UPS\Shipping\Helper\ConstantCarrier::RESPONSE][\UPS\Shipping\Helper\ConstantCarrier::RESPONSESTATUS])
                && isset($responseRateAPI[\UPS\Shipping\Helper\ConstantCarrier::RATERESPONSE][\UPS\Shipping\Helper\ConstantCarrier::RESPONSE][\UPS\Shipping\Helper\ConstantCarrier::RESPONSESTATUS]['Code'])
                && $responseRateAPI[\UPS\Shipping\Helper\ConstantCarrier::RATERESPONSE][\UPS\Shipping\Helper\ConstantCarrier::RESPONSE][\UPS\Shipping\Helper\ConstantCarrier::RESPONSESTATUS]['Code'] == '1'
            ) {
                foreach ($listShipServices as $shippingServiceItem) {
                    // get service rates
                    $serviceRate = [];
                    if (!empty($listDeliveryRates) && isset($shippingServiceItem['id'])
                        && isset($listDeliveryRates[$shippingServiceItem['id']])
                    ) {
                        $serviceRate = $listDeliveryRates[$shippingServiceItem['id']];
                    }
                    if (!empty($serviceRate)) {
                        foreach ($responseRateAPI['RateResponse']['RatedShipment'] as $ratedShipment) {
                            $apiRateCode = $ratedShipment['Service']['Code'];
                            $apiSatDeliFlg = $ratedShipment['TimeInTransit']['ServiceSummary']['SaturdayDelivery'];
                            if (isset($ratedShipment['NegotiatedRateCharges']['TotalCharge']['MonetaryValue'])) {
                                $apiMonetaryValue = $ratedShipment['NegotiatedRateCharges']['TotalCharge']['MonetaryValue'];
                            } else {
                                $apiMonetaryValue = $ratedShipment['TotalCharges']['MonetaryValue'];
                            }
                            $apiTimeInTransit = $ratedShipment['TimeInTransit'];

                            if (intval($shippingServiceItem['rate_code']) === intval($apiRateCode)) {
                                if ((strpos($shippingServiceItem['service_key'], 'SAT_DELI') !== false && intval($apiSatDeliFlg) == 1)
                                    || (strpos($shippingServiceItem['service_key'], 'SAT_DELI') === false && intval($apiSatDeliFlg) == 0)
                                ) {
                                    // get time from TinT API and by shipping code
                                    $shippingServiceFee = $this->_getDeliveryRates($serviceRate, $orderTotalPrice, $shippingServiceItem, $apiMonetaryValue);

                                    $this->setTimeInTransit($shippingServiceItem, $stringRateDate, $apiTimeInTransit, $cuttime);
                                    // calculate Shipping Fee
                                    $shippingServiceItem['shippingFeeValue'] = $shippingServiceFee;
                                    $shippingServiceItem['splitShippingFee'] = '';
                                    $shippingServiceItem['shippingFeeString'] = '';

                                    //case shipping fee & Time
                                    $this->setShippingFeeString($listShippingService, $stringRateDate, $cuttime, $shippingServiceFee, $serviceRate, $shippingServiceItem);
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->logger->debug("listShippingService: " . print_r($listShippingService, true));
        return $listShippingService;
    }

    /**
     * Archived getCurrencyData
     *
     * @param string $listShippingService //The listShippingService
     * @param string $stringRateDate      //The stringRateDate
     * @param string $cuttime             //The cuttime
     * @param string $shippingServiceFee  //The shippingServiceFee
     * @param string $serviceRate         //The serviceRate
     * @param string $shippingServiceItem //The shippingServiceItem
     *
     * @return array $Order
     */
    public function setShippingFeeString(&$listShippingService, $stringRateDate, $cuttime, $shippingServiceFee, $serviceRate, $shippingServiceItem)
    {
        if ((!empty($stringRateDate) || ($cuttime == 24))
            && ($shippingServiceFee >= 0 || ($shippingServiceFee == 0
            && isset($serviceRate[\UPS\Shipping\Helper\ConstantCarrier::RATE_TYPE])
            && strtoupper($serviceRate[\UPS\Shipping\Helper\ConstantCarrier::RATE_TYPE]) == 'FLAT_RATE'))
        ) {
            $currentCurrency = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
            $baseCurrency = $this->storeManager->getStore()->getBaseCurrency()->getCode();
            if ($currentCurrency != $baseCurrency) {
                $rateFrom = $this->getCurrencyData($baseCurrency, $currentCurrency);
                if ($rateFrom > 0) {
                    $shippingServiceFee /= $rateFrom;
                }
            }
            $shippingServiceItem['splitShippingFee'] = $this->formatCurrency($shippingServiceFee);
            $shippingServiceItem['shippingFeeString']
                = (!empty($stringRateDate)) ? '(' . $stringRateDate . ')' : ' ';
            $listShippingService[] = $shippingServiceItem;
        }
    }

    /**
     * Archived getCurrencyData
     *
  * @param string $baseCurrency    //The baseCurrency
     * @param string $currentCurrency //The currentCurrency
     *
     * @return array $Order
     */
    public function getCurrencyData($baseCurrency, $currentCurrency)
    {
        return (float)$this->currencyFactory->create()->load($baseCurrency)->getRate($currentCurrency);
    }

    /**
     * Carrier setTimeInTransit
     * get Delivery Rates
     *
     * @param string $shippingServiceItem //The shippingServiceItem
     * @param string $stringRateDate      //The stringRateDate
     * @param string $serviceSummary      //The serviceSummary
     * @param string $cuttime             //The cuttime
     *
     * @return array $data
     */
    public function setTimeInTransit(&$shippingServiceItem, &$stringRateDate, $serviceSummary, $cuttime)
    {
        if (!empty($serviceSummary) && isset($serviceSummary['ServiceSummary'])) {
            $shippingTimeEstimatedArrival = $serviceSummary['ServiceSummary'];
            if ($shippingTimeEstimatedArrival) {
                $date = $shippingTimeEstimatedArrival['EstimatedArrival']['Arrival']['Date'];

                $time = $shippingTimeEstimatedArrival['EstimatedArrival']['Arrival']['Time'];

                if ($date && $time) {
                    $shippingTimeEstimatedArrivalString = $date . $time;
                    $dateTime = strtotime($shippingTimeEstimatedArrivalString);
                    $shippingServiceItem['shippingArrivalDate'] = date('d-m-Y h:i:s A', $dateTime);
                    $timeString = trim(date('H:i', strtotime($time)));
                    if ($timeString == '23:30') {
                        $timeString = __('Delivered by End of Business Day') . ',';
                    } else {
                        $timeString = __('Delivered by') . ' ' . trim(date('h:i A', strtotime($time)));
                    }
                    if ($cuttime != 24) {
                        $stringRateDate = $timeString . ' '
                        . __(date('l', strtotime($shippingServiceItem['shippingArrivalDate'])))
                        . ', ' . date('j', strtotime($date)) . ' '
                        . __(date('F', strtotime($date))) . ' ' . date('Y', strtotime($date));
                    }
                }
            }
        }
    }

    /**
     * Carrier _getDeliveryRates
     * get Delivery Rates
     *
     * @param string $serviceRate               //The serviceRate
     * @param string $orderTotalPrice           //The orderTotalPrice
     * @param string $shippingServiceItem       //The shippingServiceItem
     * @param string $rateShipmentMonetaryValue //The responseRateAPI
     *
     * @return array $data
     */
    private function _getDeliveryRates($serviceRate, $orderTotalPrice, $shippingServiceItem, $rateShipmentMonetaryValue)
    {
        $shippingServiceFee = 0;
        if (strtoupper($serviceRate[\UPS\Shipping\Helper\ConstantCarrier::RATE_TYPE]) == 'FLAT_RATE') {
            if (isset($serviceRate[\UPS\Shipping\Helper\ConstantCarrier::ORDERRATES])) {
                $arrDeliveryRates = $serviceRate[\UPS\Shipping\Helper\ConstantCarrier::ORDERRATES];
                $arrDeliveryValues = $serviceRate['orderValues'];
                $defaultCurrencyCode = $this->storeManager->getStore()->getDefaultCurrencyCode();
                $orderTotalPrice = $this->convertPrice($orderTotalPrice, $defaultCurrencyCode);

                if (count($arrDeliveryRates) == 1 && $arrDeliveryValues[0] == 0) {
                    $shippingServiceFee = $arrDeliveryRates[0];
                } else {
                    array_multisort($arrDeliveryValues, SORT_ASC, $arrDeliveryRates);
                    foreach ($arrDeliveryValues as $key => $value) {
                        if ($orderTotalPrice <= $value) {
                            $shippingServiceFee = $arrDeliveryRates[$key];
                            break;
                        }
                    }
                }
                $shippingServiceFee = $this->convertPrice($shippingServiceFee, $defaultCurrencyCode);
            }
        } else {
            if (isset($shippingServiceItem[\UPS\Shipping\Helper\ConstantCarrier::RATE_CODE])
                && isset($shippingServiceItem['service_name'])
                && $rateShipmentMonetaryValue > 0
            ) {
                $monetaryValues = $rateShipmentMonetaryValue;
                if (isset($serviceRate[\UPS\Shipping\Helper\ConstantCarrier::ORDERRATES])
                    && isset($serviceRate[\UPS\Shipping\Helper\ConstantCarrier::ORDERRATES][0])
                ) {
                    $shippingServiceFee = ($monetaryValues * $serviceRate[\UPS\Shipping\Helper\ConstantCarrier::ORDERRATES][0]) / 100;
                    // change base currency to current currency
                    $baseCurrencyCode = $this->getCurrencyCode();
                    $shippingServiceFee = $this->convertPrice($shippingServiceFee, $baseCurrencyCode);
                }
            }
        }
        return $shippingServiceFee;
    }

    /**
     * Carrier updateSelectedService
     * call Rate API
     *
     * @param string $accessorials    //The accessorials
     * @param string $params          //The params
     * @param string $shippingService //The shippingService
     * @param string $orderTotalPrice //The orderTotalPrice
     * @param string $cuttime         //The cuttime
     * @param string $rateType        //The rateType
     * @param string $shippingType    //The shippingType
     * @param string $currencyCode    //The currencyCode
     * @param string $typeRate        //The typeRate
     * @param string $firstLocation   //The firstLocation
     * @param string $idAccount       //The idAccount
     *
     * @return array $data
     */
    private function _callRateAPI($accessorials, $params, $shippingService = [], $orderTotalPrice = 0, $cuttime = '', $rateType = 'SHOP', $shippingType = 'AP', $currencyCode = 'USD', $typeRate = \UPS\Shipping\Helper\ConstantCarrier::ESHOPPER, $firstLocation = [], $idAccount = '')
    {
        $responseRateAPI = [];
        //default package
        $packageInfo = [];
        $listPackageInfo = json_decode($this->checkoutSession->getPackageDimension(), true);
        $this->logger->debug("listPackageInfo: " . print_r($listPackageInfo, true));
        $arrPackageDimension = [];
        if (!empty($listPackageInfo)) {
            foreach ($listPackageInfo as $package) {
                $arrPackageDimension[] = [
                    'Dimensions' => [
                        \UPS\Shipping\Helper\ConstantCarrier::UNITOFMEASUREMENT => [
                            'Code' => (isset($package['unit_dimension']))
                                ? strtoupper($package['unit_dimension']) : '',
                            \UPS\Shipping\Helper\ConstantCarrier::DESCRIPTION
                            => (isset($package['unit_dimension'])) ?
                        strtoupper($package['unit_dimension']) : ''
                        ],
                        'Length' => (isset($package['length'])) ? $package['length'] : '',
                        'Width' => (isset($package['width'])) ? $package['width'] : '',
                        'Height' => (isset($package['height'])) ? $package['height'] : ''
                    ],
                    'PackageWeight' => [
                        \UPS\Shipping\Helper\ConstantCarrier::UNITOFMEASUREMENT => [
                            'Code' => ($package['unit_weight'])
                            ? strtoupper($package['unit_weight']) : '',
                            \UPS\Shipping\Helper\ConstantCarrier::DESCRIPTION => ($package['unit_weight'])
                            ? strtoupper($package['unit_weight']) : ''
                        ],
                        'Weight' => ($package['weight']) ? $package['weight'] : '0'
                    ],
                    'Packaging' => [
                        'Code' => '02'
                    ],
                    'PackagingType' => [
                        'Code' => '02'
                    ],
                    'PackageServiceOptions' => []
                ];
            }
        } else {
            $arrPackageDimension[] = $listPackageInfo;
        }
        //$listPackageInfo = $this->modelPackage->getListPackage();

        // Get getInfoAccount($id) Info
        if (empty($idAccount)) {
            $accountInfo = $this->modelAccount->getAccountDefault();
        } else {
            $accountInfo = $this->modelAccount->getInfoAccount($idAccount);
        }
        if (!empty($params[\UPS\Shipping\Helper\ConstantCarrier::STATECODE])) {
            $params[\UPS\Shipping\Helper\ConstantCarrier::STATECODE]
                = $this->regionFactory->create()->load($params[\UPS\Shipping\Helper\ConstantCarrier::STATECODE]);
        }
        if (!isset($params[\UPS\Shipping\Helper\ConstantCarrier::STATECODE])
            || $params[\UPS\Shipping\Helper\ConstantCarrier::STATECODE] == ''
        ) {
            $params[\UPS\Shipping\Helper\ConstantCarrier::STATECODE] = 'XX';
        }
        // the euro countries
        if (!isset($accountInfo[\UPS\Shipping\Helper\ConstantCarrier::STATECODE])
            || $accountInfo[\UPS\Shipping\Helper\ConstantCarrier::STATECODE] == ''
        ) {
            $accountInfo[\UPS\Shipping\Helper\ConstantCarrier::STATECODE] = 'XX';
        }
        if (!isset($params[\UPS\Shipping\Helper\ConstantCarrier::REGION_ID])
            || $params[\UPS\Shipping\Helper\ConstantCarrier::REGION_ID] == ''
        ) {
            $params[\UPS\Shipping\Helper\ConstantCarrier::REGION_CODE] = 'XX';
        }

        if (!empty($accountInfo)) {
            $shipToName = trim($params[\UPS\Shipping\Helper\ConstantCarrier::FIRSTNAME] . ' ' . $params[\UPS\Shipping\Helper\ConstantCarrier::LASTNAME]);
            if (empty($shipToName)) {
                $shipToName = 'UPS Shipping';
            }
            $unitWeight = \UPS\Shipping\Helper\ConstantCarrier::UNIT_WEIGHT;
            $unitDimension = \UPS\Shipping\Helper\ConstantCarrier::UNIT_DIMENSION;
            $cCountry = \UPS\Shipping\Helper\ConstantCarrier::COUNTRY;
            $cPostCode = \UPS\Shipping\Helper\ConstantCarrier::POST_CODE;
            $dataRate = [
                'Request' => [
                    'RequestOption' => 'Shoptimeintransit'
                ],
                'Shipper' => [
                    'Name' => (isset($accountInfo[\UPS\Shipping\Helper\ConstantCarrier::BUSINESS_NAME])
                    ? $accountInfo[\UPS\Shipping\Helper\ConstantCarrier::BUSINESS_NAME] : ''),
                    'ShipperNumber' => (isset($accountInfo[\UPS\Shipping\Helper\ConstantCarrier::UPS_ACCOUNT_NUMBER])
                    ? $accountInfo[\UPS\Shipping\Helper\ConstantCarrier::UPS_ACCOUNT_NUMBER] : ''),
                    \UPS\Shipping\Helper\ConstantCarrier::ADDRESS => [
                        \UPS\Shipping\Helper\ConstantCarrier::ADDRESSLINE => [
                            (isset($accountInfo[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS_1])
                            ? $accountInfo[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS_1] : ''),
                            (isset($accountInfo[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS_2])
                            ? $accountInfo[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS_2] : ''),
                            (isset($accountInfo[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS_3])
                            ? $accountInfo[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS_3] : ''),
                        ],
                        'City' => (isset($accountInfo['city']) ? $accountInfo['city'] : ''),
                        \UPS\Shipping\Helper\ConstantCarrier::STATEPROVINCECODE
                        => (isset($accountInfo[\UPS\Shipping\Helper\ConstantCarrier::STATEPROVINCECODENEW])
                        ? $accountInfo[\UPS\Shipping\Helper\ConstantCarrier::STATEPROVINCECODENEW] : 'XX'),
                        \UPS\Shipping\Helper\ConstantCarrier::POSTALCODE => (isset($accountInfo[$cPostCode])
                        ? $accountInfo[$cPostCode] : ''),
                        \UPS\Shipping\Helper\ConstantCarrier::COUNTRYCODE => (isset($accountInfo[$cCountry])
                        ? $accountInfo[$cCountry] : '')
                    ]
                ],
                'ShipTo' => [
                    'Name' => $shipToName,
                    \UPS\Shipping\Helper\ConstantCarrier::ADDRESS => [
                        \UPS\Shipping\Helper\ConstantCarrier::ADDRESSLINE => [
                            (isset($params[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS1])
                            ? $params[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS1] : ''),
                            (isset($params[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS2])
                            ? $params[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS2] : ''),
                            (isset($params[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS3])
                            ? $params[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS3] : '')
                        ],
                        'City' => (isset($params['city']) ? $params['city'] : ''),
                        \UPS\Shipping\Helper\ConstantCarrier::STATEPROVINCECODE
                            => (isset($params[\UPS\Shipping\Helper\ConstantCarrier::REGION_CODE])
                        ? $params[\UPS\Shipping\Helper\ConstantCarrier::REGION_CODE] : ''),
                        \UPS\Shipping\Helper\ConstantCarrier::POSTALCODE
                            => (isset($params[\UPS\Shipping\Helper\ConstantCarrier::POSTCODE])
                        ? $params[\UPS\Shipping\Helper\ConstantCarrier::POSTCODE] : ''),
                        \UPS\Shipping\Helper\ConstantCarrier::COUNTRYCODE
                            => (isset($params[\UPS\Shipping\Helper\ConstantCarrier::COUNTRY_ID])
                        ? $params[\UPS\Shipping\Helper\ConstantCarrier::COUNTRY_ID] : '')
                    ]
                ],
                'ShipFrom' => [
                    'Name' => (isset($accountInfo[\UPS\Shipping\Helper\ConstantCarrier::BUSINESS_NAME])
                    ? $accountInfo[\UPS\Shipping\Helper\ConstantCarrier::BUSINESS_NAME] : ''),
                    \UPS\Shipping\Helper\ConstantCarrier::ADDRESS => [
                        \UPS\Shipping\Helper\ConstantCarrier::ADDRESSLINE => [
                            (isset($accountInfo[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS_1])
                            ? $accountInfo[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS_1] : ''),
                            (isset($accountInfo[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS_2])
                            ? $accountInfo[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS_2] : ''),
                            (isset($accountInfo[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS_3])
                            ? $accountInfo[\UPS\Shipping\Helper\ConstantCarrier::ADDRESS_3] : '')
                        ],
                        'City' => (isset($accountInfo['city']) ? $accountInfo['city'] : ''),
                        \UPS\Shipping\Helper\ConstantCarrier::STATEPROVINCECODE
                            => (isset($accountInfo[\UPS\Shipping\Helper\ConstantCarrier::STATEPROVINCECODENEW])
                        ? $accountInfo[\UPS\Shipping\Helper\ConstantCarrier::STATEPROVINCECODENEW] : 'XX'),
                        \UPS\Shipping\Helper\ConstantCarrier::POSTALCODE => (isset($accountInfo[$cPostCode])
                        ? $accountInfo[$cPostCode] : ''),
                        \UPS\Shipping\Helper\ConstantCarrier::COUNTRYCODE => (isset($accountInfo[$cCountry])
                        ? $accountInfo[$cCountry] : '')
                    ]
                ],
                'ShipmentRatingOptions' => [
                    'NegotiatedRatesIndicator' => ''
                ],
                'Package' => $arrPackageDimension,
                //$accessorials
                'accessorials' => $accessorials,
            ];
            $dataRate['ShippingType'] = $shippingType;
            $dataRate['Typerate'] = $typeRate;
            $dataRate['PaymentDetails']['ShipmentCharge']['BillShipper']['AccountNumber']
                = (isset($accountInfo[\UPS\Shipping\Helper\ConstantCarrier::UPS_ACCOUNT_NUMBER])
            ? $accountInfo[\UPS\Shipping\Helper\ConstantCarrier::UPS_ACCOUNT_NUMBER] : '');

            if ($shippingType == 'AP') {
                $countryCodeStr = \UPS\Shipping\Helper\ConstantCarrier::COUNTRYCODE;
                $cAddress = \UPS\Shipping\Helper\ConstantCarrier::ADDRESS;
                $cAddressLine = \UPS\Shipping\Helper\ConstantCarrier::ADDRESSLINE;
                $cDeliveryAddress = \UPS\Shipping\Helper\ConstantCarrier::ALTERNATEDELIVERYADDRESS;
                // ShipmentIndicationType
                $dataRate['ShipmentIndicationType']['Code'] = '01';
                // AlternateDeliveryAddress
                $dataRate[$cDeliveryAddress]['Name']
                    = (isset($firstLocation['ConsigneeName'])
                ? $firstLocation['ConsigneeName'] : '');
                $dataRate[$cDeliveryAddress]['AttentionName']
                    = $params[\UPS\Shipping\Helper\ConstantCarrier::FIRSTNAME] . ' '
                . $params[\UPS\Shipping\Helper\ConstantCarrier::LASTNAME];
                $dataRate[$cDeliveryAddress][$cAddress][$cAddressLine]
                    = (isset($firstLocation[$cAddressLine]) ? html_entity_decode($firstLocation[$cAddressLine]) : '');
                $dataRate[$cDeliveryAddress][$cAddress]['City']
                    = (isset($firstLocation['PoliticalDivision2']) ? $firstLocation['PoliticalDivision2'] : '');
                $dataRate[$cDeliveryAddress][$cAddress]['StateProvinceCode']
                    = (isset($firstLocation['PoliticalDivision1']) ? $firstLocation['PoliticalDivision1'] : '');
                $dataRate[$cDeliveryAddress][$cAddress]['PostalCode']
                    = (isset($firstLocation['PostcodePrimaryLow']) ? $firstLocation['PostcodePrimaryLow'] : '');
                $dataRate[$cDeliveryAddress][$cAddress][$countryCodeStr]
                    = (isset($firstLocation[$countryCodeStr]) ? $firstLocation[$countryCodeStr] : '');
            }

            if ($rateType != 'SHOP') {
                // calculate time by Cut Time of shipping service screen
                $pickupDate = $this->addCutTimeShippingDate($cuttime);
                $dataRate['DeliveryTimeInformation']['Pickup']['Date'] = $pickupDate;
                $dataRate['InvoiceLineTotal']['CurrencyCode'] = $currencyCode;
                $dataRate['InvoiceLineTotal']['MonetaryValue'] = $orderTotalPrice;
            }
            $responseRateAPI = $this->apiRate->shopTimeInTransit($dataRate);
        }
        return $responseRateAPI;
    }

    /**
     * Carrier updateSelectedService
     * Get shipping Time by Cut Time
     *
     * @param string $cuttime //The cuttime
     *
     * @return array $data
     */
    public function addCutTimeShippingDate($cuttime)
    {
        $cuttime = $cuttime * 1;
        $stringRateDate = date('Ymd');
        // address
        if ($cuttime != 24) {
            // get hour current
            $currentMinute = $this->timezone->date($this->dateTime->timestamp())->format('i') * 1;
            $currentHour = $this->timezone->date($this->dateTime->timestamp())->format('H') * 1;
            if (($currentHour > $cuttime) || ($currentHour == $cuttime && $currentMinute > 0)) {
                // The pickup time ($cuttime) < current time then + 1 day because late pickup
                $date = date_create(date('Y-m-d'));
                date_add($date, date_interval_create_from_date_string('1 days'));
                $stringRateDate = date_format($date, 'Ymd');
            }
        }
        return $stringRateDate;
    }

    /**
     * Carrier updateSelectedService
     * convert country to code
     *
     * @param string $code //The code
     *
     * @return array $data
     */
    public function countrytolocale($code)
    {
        $arrCountryCodes = array_keys($this->lists->getLocaleList());
        $returnCode = [];
        if (!empty($arrCountryCodes)) {
            foreach ($arrCountryCodes as $codeCountry) {
                if (!empty($codeCountry)) {
                    $cutCode = explode('_', $codeCountry);
                    $returnCode[$cutCode[1]] = $codeCountry;
                }
            }
        }

        if ($code == 'UK') {
            $code = 'gb';
        }

        if (array_key_exists($code, $returnCode)) {
            return $returnCode[$code];
        }

        if ($code == 'EU') {
            return 'en_GB';
        } elseif ($code == 'CS') {
            return 'sr_RS';
        } else {
            return \UPS\Shipping\Helper\Config::COUNTRY_US;
        }
    }

    /**
     * Carrier updateSelectedService
     * change cureency format
     *
     * @param string $currency //The currency
     *
     * @return array $data
     */
    public function changeCurrencyFormat($currency)
    {
        if (!empty($currency)) {
            return str_replace(',', '', $currency);
        } else {
            return $currency;
        }
    }

    /**
     * Convert base price value to store price value
     *
     * @param string $amountValue //The amountValue
     * @param string $currencyTo  //The currencyTo
     *
     * @return float
     */
    public function convertPrice($amountValue, $currencyTo)
    {
        $currentCurrency = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        if ($currentCurrency != $currencyTo) {
            $baseCurrency = $this->storeManager->getStore()->getBaseCurrency()->getCode();
            $rateTo = (float)$this->currencyFactory->create()->load($baseCurrency)->getRate($currentCurrency);
            $rateFrom = (float)$this->currencyFactory->create()->load($baseCurrency)->getRate($currencyTo);
            if ($rateFrom > 0) {
                $amountValue = (($amountValue * $rateTo)/$rateFrom);
            }
        }
        return $amountValue;
    }

    /**
     * Index getCurrencyCode
     *
     * @return array $data
     */
    public function getCurrencyCode()
    {
        $countryCode = $this->_scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE);
        $listCurrencys = \UPS\Shipping\Helper\Config::LISTCURRENCYS;
        $currencyCountry = $listCurrencys[$countryCode];
        $currencyCode = 'USD';
        if (!empty($currencyCountry)) {
            $currencyCode = $currencyCountry[1];
        }
        return $currencyCode;
    }

    /**
     * Carrier formatCurrency
     * function format currency
     *
     * @param string $price //The price
     *
     * @return array $data
     */
    public function formatCurrency($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }
}
