<?php
/**
 * ShippingService file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Controller\Eshopper;
/**
 * ShippingService class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class ShippingService extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $checkoutSession;
    protected $countryFactory;
    protected $apiLocator;
    protected $carrier;
    protected $scopeConfig;

    /**
     * ShippingService __construct
     *
     * @param string $context           //The context
     * @param string $resultJsonFactory //The resultJsonFactory
     * @param string $countryFactory    //The countryFactory
     * @param string $checkoutSession   //The checkoutSession
     * @param string $apiLocator        //The apiLocator
     * @param string $carrier           //The carrier
     * @param string $scopeConfig       //The scopeConfig
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \UPS\Shipping\API\Locator $apiLocator,
        \UPS\Shipping\Model\Carrier $carrier,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->checkoutSession = $checkoutSession;
        $this->countryFactory = $countryFactory;
        $this->apiLocator = $apiLocator;
        $this->carrier = $carrier;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * ShippingService execute
     *
     * @return boolean
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $result = $this->resultJsonFactory->create();
            $params = $this->getRequest()->getParams();

            if ($params) {
                switch ($params['method']) {
                case 'loadDefault':
                    $eClear = \UPS\Shipping\Helper\ConstantEshopper::CLEARSESSION;
                    $eCountryCode = \UPS\Shipping\Helper\ConstantEshopper::COUNTRYCODE;
                    // save session address
                    $country = $this->countryFactory->create()->loadByCode($params[$eCountryCode]);
                    $this->setSession($params, $eClear);
                    // address services,
                    $serviceAP = json_decode($this->checkoutSession->getAccessPointServices(), true);
                    // address services,
                    $serviceADD = json_decode($this->checkoutSession->getAddressServices(), true);
                    // get default [AP or Address],
                    $serviceDefault = $this->checkoutSession->getDefaultService();
                    // cheapest fee
                    $cheapestFee = $this->checkoutSession->getCheapestFee();
                    // selected service, id
                    $selectedService = $this->checkoutSession->getSelectedShippingService();
                    // selected service fee, value
                    $selectedServiceFee = $this->checkoutSession->getSelectedFee();
                    $apString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_ACCESS_POINT;
                    $enableServiceAP = $this->scopeConfig->getValue($apString);
                    $addString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_SHIPPING_ADDRESS;
                    $enableServiceADD = $this->scopeConfig->getValue($addString);
                    // service type
                    $listServiceTypes = json_decode($this->checkoutSession->getListServiceTypes(), true);
                    $selectedServiceType = $this->getSelectedServiceType($listServiceTypes, $selectedService);
                    if ($selectedServiceFee > 0 && $params[$eClear] == '0') {
                        $cheapestFee = $selectedServiceFee;
                    }
                    $returnData = [
                        'serviceAP' => $serviceAP,
                        'serviceADD' => $serviceADD,
                        'enableServiceAP' => $enableServiceAP,
                        'enableServiceADD' => $enableServiceADD,
                        'serviceDefault' => $serviceDefault,
                        'serviceFee' => $cheapestFee,
                        'selectedService' => $selectedService,
                        'selectedServiceType' => $selectedServiceType,
                        'countryName' => ($country) ? $country->getName() : ''
                    ];
                    return $result->setData($returnData);

                case 'locatorAPI':
                    $returnData = $this->setLocatorAPI($params);
                    return $result->setData($returnData);

                case 'selectedService':
                    $returnData = $this->setSelectedService($params);
                    return $result->setData($returnData);

                case \UPS\Shipping\Helper\ConstantEshopper::SELECTEDAPADDRESS:
                    $this->setShippingServiceSelected($params);
                    return $result->setData([\UPS\Shipping\Helper\ConstantEshopper::RESULT => []]);

                case 'shipHere':
                    $this->checkoutSession->setClearSession('1');
                    return $result->setData([\UPS\Shipping\Helper\ConstantEshopper::RESULT => []]);

                default:
                    return $result->setData([\UPS\Shipping\Helper\ConstantEshopper::RESULT => []]);
                }
            }
        }
    }

    /**
     * ShippingService setShippingServiceSelected
     *
     * @param string $params //The params
     *
     * @return array
     */
    public function setShippingServiceSelected($params)
    {
        if (isset($params[\UPS\Shipping\Helper\ConstantEshopper::SELECTEDAPADDRESS])) {
            $selectedAddress = $params[\UPS\Shipping\Helper\ConstantEshopper::SELECTEDAPADDRESS];
            $this->checkoutSession->setSelectedAPAddress($selectedAddress);
        }
    }

    /**
     * ShippingService setSelectedService
     *
     * @param string $params //The params
     *
     * @return array
     */
    public function setSelectedService($params)
    {
        $this->checkoutSession->setClearSession('0');
        //selectedShippingService = id
        $eshopperService = $params[\UPS\Shipping\Helper\ConstantEshopper::SELECTEDSHIPPINGSERVICE];
        if (isset($eshopperService)) {
            // listServices = array services fees
            $listServices = json_decode($this->checkoutSession->getListServices(), true);
            //selectedShippingService = id
            $this->checkoutSession->setSelectedShippingService($eshopperService);
            $this->setSessionSelectedFee($listServices, $eshopperService);
            // clear selected AP address
            $this->checkoutSession->setSelectedAPAddress('');
            // list service type
            $listServiceTypes = json_decode($this->checkoutSession->getListServiceTypes(), true);
            $shippingServiceType = $this->getShippingServiceType($listServiceTypes, $eshopperService);
        }
        return [\UPS\Shipping\Helper\ConstantEshopper::RESULT => ['shippingServiceType' => $shippingServiceType]];
    }

    /**
     * ShippingService getSelectedServiceType
     *
     * @param string $listServiceTypes //The listServiceTypes
     * @param string $selectedService  //The selectedService
     *
     * @return array
     */
    public function getSelectedServiceType($listServiceTypes, $selectedService)
    {
        $selectedServiceType = '';
        if (!empty($listServiceTypes) && isset($listServiceTypes[$selectedService])) {
            $selectedServiceType = $listServiceTypes[$selectedService];
        }
        return $selectedServiceType;
    }

    /**
     * ShippingService setLocatorAPI
     *
     * @param string $params //The params
     *
     * @return array
     */
    public function setLocatorAPI($params)
    {
        // locator API
        $inEUCountry = \UPS\Shipping\Helper\Config::LISTEUCOUNTRY;
        $countryCode = $params[\UPS\Shipping\Helper\ConstantEshopper::COUNTRYCODE];
        $satDeliFlg = strpos($params['selectedService'], 'SAT_DELI') !== false ? true : false;
        $avaiString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_NUMBER_OF_ACCESS_POINT_AVAIABLE;
        $countMaximumListSize = $this->scopeConfig->getValue($avaiString);
        $activeString = \UPS\Shipping\Helper\Config::CASH_ON_DELIVERY_UPS_SHIPPING_OPTION_ACTIVE;
        $adultSignature = intval($this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::ADULT_SIGNATURE));
        if ($satDeliFlg === true) {
            $countMaximumListSize *= 2;
        }
        if (($this->scopeConfig->getValue($activeString)=='1' && in_array($countryCode, $inEUCountry)) || 1 == $adultSignature) {
            $countMaximumListSize *= 2;
        }
        $rangeStr = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DISPLAY_ALL_ACCESS_POINT_IN_RANGE;
        $address = [
            'fullAddress' => $params['fullAddress'],
            \UPS\Shipping\Helper\ConstantEshopper::COUNTRYCODE => $countryCode,
            'Locale' => $this->carrier->countrytolocale($countryCode),
            \UPS\Shipping\Helper\ConstantEshopper::UNITOFMEASUREMENT => (('US' == $countryCode) ? 'MI' : 'KM'),
            'MaximumListSize' => (string)$countMaximumListSize,
            'nearby' => $this->scopeConfig->getValue($rangeStr),
        ];
        $response = $this->apiLocator->loadAddress($address);
        $inforLocator = [
            \UPS\Shipping\Helper\ConstantEshopper::ARRAYLOCATORGEOCODE => [],
            \UPS\Shipping\Helper\ConstantEshopper::ARRAYLOCATORINFO => [],
            \UPS\Shipping\Helper\ConstantEshopper::SELECTADDRESS => []
        ];
        $description = '';
        $response = json_decode($response, true);
        $eLocator = \UPS\Shipping\Helper\ConstantEshopper::LOCATORRESPONSE;
        $eResponse = \UPS\Shipping\Helper\ConstantEshopper::RESPONSE;
        $eSearch = \UPS\Shipping\Helper\ConstantEshopper::SEARCHRESULTS;
        if ($response && isset($response[$eLocator])
            && isset($response[$eLocator][$eResponse])
            && isset($response[$eLocator][$eResponse]['ResponseStatusCode'])
            && $response[$eLocator][$eResponse]['ResponseStatusCode'] == "1"
        ) {
            if (isset($response[$eLocator][$eSearch])
                && isset($response[$eLocator][$eSearch]['DropLocation'])
            ) {
                $arrayLocations = $response[$eLocator][$eSearch]['DropLocation'];
            }
            $description = (isset($response[$eLocator][$eResponse]['ResponseStatusDescription'])
            ? $response[$eLocator][$eResponse]['ResponseStatusDescription'] : '');
            $countResponse = count($arrayLocations);
            if (!isset($arrayLocations[1])) {
                $countResponse = 1;
            }
            $numVisible = $this->scopeConfig->getValue($avaiString);
            $maxShow = min($countResponse, $numVisible);
            if (!empty($arrayLocations)) {
                $arrStandardCode = [];
                // in 8 country
                if (in_array($countryCode, $inEUCountry)) {
                    $arrStandardCode = [\UPS\Shipping\Helper\Config::ISVALIDCODE001];
                    if ($this->scopeConfig->getValue($activeString) == '1') {
                        $arrStandardCode[] = \UPS\Shipping\Helper\Config::ISVALIDCODE011;
                    }
                }
                if (1 == $adultSignature) {
                    $arrStandardCode[] = \UPS\Shipping\Helper\Config::ISVALIDCODE013;
                }
                $arrLocators = $this->getArrayLocator($arrayLocations, $countResponse);
                $inforLocator = $this->getSelectAddress($arrLocators, $arrStandardCode, $maxShow, $satDeliFlg);
            }
        }
        return [
            'error' => '',
            'description' => $description,
            'data' => [
                'arrGeoCode' => $inforLocator[\UPS\Shipping\Helper\ConstantEshopper::ARRAYLOCATORGEOCODE],
                'infor' => $inforLocator[\UPS\Shipping\Helper\ConstantEshopper::ARRAYLOCATORINFO],
                'selectAddress' => json_encode($inforLocator[\UPS\Shipping\Helper\ConstantEshopper::SELECTADDRESS])
            ]
        ];
    }

    /**
     * ShippingService getShippingServiceType
     *
     * @param string $listServiceTypes //The listServiceTypes
     * @param string $eshopperService  //The eshopperService
     *
     * @return array
     */
    public function getShippingServiceType($listServiceTypes, $eshopperService)
    {
        $shippingServiceType = '';
        if (!empty($listServiceTypes) && isset($listServiceTypes[$eshopperService])) {
            $shippingServiceType = $listServiceTypes[$eshopperService];
        }
        return $shippingServiceType;
    }
    /**
     * ShippingService getArrayLocator
     *
     * @param string $arrayLocations //The arrayLocations
     * @param string $countResponse  //The countResponse
     *
     * @return null
     */
    public function getArrayLocator($arrayLocations, $countResponse)
    {
        $arrLocators = [];
        if ($countResponse == 1) {
            $arrLocators[] = $arrayLocations;
        } else {
            $arrLocators = $arrayLocations;
        }
        return $arrLocators;
    }

    /**
     * ShippingService setSessionSelectedFee
     * get address for map
     *
     * @param string $listServices    //The listServices
     * @param string $eshopperService //The eshopperService
     *
     * @return null
     */
    public function setSessionSelectedFee($listServices, $eshopperService)
    {
        if (!empty($listServices) && isset($listServices[$eshopperService])) {
            $this->checkoutSession->setSelectedFee($listServices[$eshopperService]);
        }
    }

    /**
     * ShippingService setSession
     * get address for map
     *
     * @param string $params //The params
     * @param string $eClear //The eClear
     *
     * @return null
     */
    public function setSession($params, $eClear)
    {
        if ($params[$eClear] == '1' || $params[$eClear] == 1) {
            $this->checkoutSession->setClearSession('1');
        } else {
            $this->checkoutSession->setClearSession('0');
        }
    }

    /**
     * ShippingService getSelectAddress
     * get address for map
     *
     * @param string $arrLocators     //The arrLocators
     * @param string $arrStandardCode //The arrStandardCode
     * @param string $maxShow         //The maxShow
     * @param boolean $satDeliFlg     // Is saturday delivery shipping service selected
     *
     * @return boolean
     */
    public function getSelectAddress($arrLocators, $arrStandardCode, $maxShow, $satDeliFlg)
    {
        $limitNumberAP = 0;
        $arrayLocatorGeoCode = [];
        $arrayLocatorInfo = [];
        $selectAddress = [];
        foreach ($arrLocators as $key => $locator) {
            $countStandardCode = $this->countItem($arrStandardCode);
            $countMergeServiceCode = 0;
            if ($countStandardCode > 0) {
                $mergeServiceCode = $this->getServiceCode($arrStandardCode, $locator['ServiceOfferingList']['ServiceOffering']);
                $countMergeServiceCode = $this->countItem($mergeServiceCode);
            }
            if ($countStandardCode == 0 || $countMergeServiceCode == $countStandardCode) {
                // only show number of AP in limitation.
                if ($limitNumberAP == $maxShow) {
                    break;
                }
                $formatKey = \UPS\Shipping\Helper\ConstantEshopper::ADDRESSKEYFORMAT;
                $eshopperPolitical1 = \UPS\Shipping\Helper\ConstantEshopper::POLITICALDIVISION1;
                $eshopperPolitical2 = \UPS\Shipping\Helper\ConstantEshopper::POLITICALDIVISION2;
                $ePostalCode = \UPS\Shipping\Helper\ConstantEshopper::POSTCODEPRIMARYLOW;
                $ePostCodeEx = \UPS\Shipping\Helper\ConstantEshopper::POSTCODEEXTENDEDLOW;
                $eADDLine = \UPS\Shipping\Helper\ConstantEshopper::ADDRESSLINE;
                $city = (isset($locator[$formatKey][$eshopperPolitical2]) && $locator[$formatKey][$eshopperPolitical2])
                ? ', ' . $locator[$formatKey][$eshopperPolitical2] : '';
                $state = (isset($locator[$formatKey][$eshopperPolitical1]) && $locator[$formatKey][$eshopperPolitical1])
                ? ', ' . $locator[$formatKey][$eshopperPolitical1] : '';
                $primaryPostCode = (isset($locator[$formatKey][$ePostalCode]) && $locator[$formatKey][$ePostalCode])
                ? ', ' . $locator[$formatKey][$ePostalCode] : '';
                $extendPostCode = (isset($locator[$formatKey][$ePostCodeEx]) && $locator[$formatKey][$ePostCodeEx])
                ? ', ' . $locator[$formatKey][$ePostCodeEx] : '';

                // Get operating hours in day of week
                $openCloseHoursInWeek = $locator['OperatingHours']['StandardHours']['DayOfWeek'];
                $satDeliCloseFlg = false;
                if ($satDeliFlg === true) {
                    foreach ($openCloseHoursInWeek as $openCloseHoursInDay) {
                        if ($openCloseHoursInDay['Day'] == '7' && array_key_exists('ClosedIndicator', $openCloseHoursInDay)) {
                            $satDeliCloseFlg = true;
                            break;
                        }
                    }
                }
                if ($satDeliCloseFlg) {
                    continue;
                }
                $arrayLocatorGeoCode[] = $locator[\UPS\Shipping\Helper\ConstantEshopper::GEOCODE] ['Latitude'] . ', '
                    . $locator[\UPS\Shipping\Helper\ConstantEshopper::GEOCODE]['Longitude'];
                $openCloseString = $this->_openTime($openCloseHoursInWeek);
                $eDistance = \UPS\Shipping\Helper\ConstantEshopper::DISTANCE;
                $eMeasure = \UPS\Shipping\Helper\ConstantEshopper::UNITOFMEASUREMENT;
                $arrayLocatorInfo[] = [
                    'name' => $locator[$formatKey]['ConsigneeName'],
                    'address' => $locator[$formatKey][$eADDLine] . $city . $state . $primaryPostCode . $extendPostCode,
                    \UPS\Shipping\Helper\ConstantEshopper::DISTANCESMALL => $locator[$eDistance]['Value'],
                    'unit' => (strtolower($locator[$eDistance][$eMeasure]['Description']) == 'kilometers') ? 'km' : 'miles',
                    'operatingHours' => $openCloseString,
                    \UPS\Shipping\Helper\ConstantEshopper::STANDARDHOURSOFOPERATION
                        => $locator[\UPS\Shipping\Helper\ConstantEshopper::STANDARDHOURSOFOPERATION]
                ];
                $locator[$formatKey][$eADDLine]= base64_encode($locator[$formatKey][$eADDLine]);
                $locator[$formatKey][\UPS\Shipping\Helper\ConstantEshopper::ACCESSPOINTID]
                    = $locator[\UPS\Shipping\Helper\ConstantEshopper::ACCESSPOINTINFORMATION]
                    [\UPS\Shipping\Helper\ConstantEshopper::PUBLICACCESSPOINTID];
                $selectAddress[] = $locator[$formatKey];
                $limitNumberAP++;
            }
        }
        return [
            \UPS\Shipping\Helper\ConstantEshopper::ARRAYLOCATORGEOCODE => $arrayLocatorGeoCode,
            \UPS\Shipping\Helper\ConstantEshopper::ARRAYLOCATORINFO => $arrayLocatorInfo,
            \UPS\Shipping\Helper\ConstantEshopper::SELECTADDRESS => $selectAddress
        ];
    }

    /**
     * ShippingService getServiceCode
     * get service code
     *
     * @param array $arrStandardCode        //The arrStandardCode
     * @param array $arrServiceOfferingCode //The arrServiceOfferingCode
     *
     * @return boolean
     */
    public function getServiceCode($arrStandardCode, $arrServiceOfferingCode)
    {
        $listServiceOfferingCode = [];
        foreach ($arrServiceOfferingCode as $item) {
            $listServiceOfferingCode[] = $item['Code'];
        }
        return array_intersect($arrStandardCode, $listServiceOfferingCode);
    }

    /**
     * ShippingService openTime
     * get open time
     *
     * @param string $openCloseHours //The openCloseHours
     *
     * @return string openCloseString
     */
    private function _openTime($openCloseHours)
    {
        $dayOfWeek = [
            "2" => __('Monday'),
            "3" => __('Tuesday'),
            "4" => __('Wednesday'),
            "5" => __('Thursday'),
            "6" => __('Friday'),
            "7" => __('Saturday'),
            "1" => __('Sunday'),
        ];
        $openCloseString = '';
        if (!empty($openCloseHours)) {
            $openCloseString = '<table class="addressTable">
            <tr>
                <th></th>
                <th>' . __("Open") . '</th>
                <th>' . __("Close") . '</th>
            </tr>';
            $sunDayOpenCloseString = '';
            foreach ($openCloseHours as $keyDay => $openDay) {
                $this->getTempString($openCloseString, $sunDayOpenCloseString, $openDay, $dayOfWeek);
            }
            $openCloseString .= $sunDayOpenCloseString . '</table>';
        }
        return $openCloseString;
    }

    /**
     * ShippingService getTempString
     * get format time
     *
     * @param string $openCloseString       //The openCloseString
     * @param string $sunDayOpenCloseString //The sunDayOpenCloseString
     * @param string $openDay               //The openDay
     * @param string $dayOfWeek             //The dayOfWeek
     *
     * @return boolean
     */
    public function getTempString(&$openCloseString, &$sunDayOpenCloseString, $openDay, $dayOfWeek)
    {
        $tempString = '<tr><td>' . $dayOfWeek[$openDay['Day']] . \UPS\Shipping\Helper\ConstantEshopper::STYLE_TD;
        if (isset($openDay[\UPS\Shipping\Helper\ConstantEshopper::OPENHOURS])
            && is_array($openDay[\UPS\Shipping\Helper\ConstantEshopper::OPENHOURS])
        ) {
            $arrOpen = $openDay[\UPS\Shipping\Helper\ConstantEshopper::OPENHOURS];
            $arrClose = $openDay[\UPS\Shipping\Helper\ConstantEshopper::CLOSEHOURS];
            if (!empty($arrOpen) && !empty($arrClose)) {
                $tempString .= '<td>' . $this->_formatTime($arrOpen[0])
                . \UPS\Shipping\Helper\ConstantEshopper::STYLE_TD;
                $tempString .= '<td>' . $this->_formatTime($arrClose[0]) . '</td></tr>';
                foreach ($arrOpen as $key => $hours) {
                    if ($key > 0) {
                        $tempString .= '<tr>
                                        <td></td>
                                        <td>' . $this->_formatTime($arrOpen[$key]) . '</td>
                                        <td>' . $this->_formatTime($arrClose[$key]) . '</td>
                                    </tr>';
                    }
                }
            }
        } elseif (isset($openDay[\UPS\Shipping\Helper\ConstantEshopper::OPENHOURS])
            && isset($openDay[\UPS\Shipping\Helper\ConstantEshopper::CLOSEHOURS])
            && !empty($openDay[\UPS\Shipping\Helper\ConstantEshopper::OPENHOURS])
            && !empty($openDay[\UPS\Shipping\Helper\ConstantEshopper::CLOSEHOURS])
        ) {
            $tempString .= '<td>' . $this->_formatTime($openDay[\UPS\Shipping\Helper\ConstantEshopper::OPENHOURS])
            . \UPS\Shipping\Helper\ConstantEshopper::STYLE_TD;
            $tempString .= '<td>'
            . $this->_formatTime($openDay[\UPS\Shipping\Helper\ConstantEshopper::CLOSEHOURS]) . '</td></tr>';
        } else {
            if (isset($openDay[\UPS\Shipping\Helper\ConstantEshopper::OPEN24HOURSINDICATOR])) {
                $tempString .= '<td>' . __("Open 24 hours") . \UPS\Shipping\Helper\ConstantEshopper::STYLE_TD;
                $tempString .= '<td></td></tr>';
            } else {
                $tempString .= '<td>' . __('Closed') . \UPS\Shipping\Helper\ConstantEshopper::STYLE_TD;
                $tempString .= '<td></td></tr>';
            }
        }
        // reomve Sunday to rear
        if ($openDay['Day'] == '1') {
            $sunDayOpenCloseString .= $tempString;
        } else {
            $openCloseString .= $tempString;
        }
    }

    /**
     * ShippingService formatTime
     * get format time
     *
     * @param string $timeString //The timeString
     *
     * @return boolean
     */
    private function _formatTime($timeString)
    {
        $formatedTimeString = '';
        if (!empty($timeString)) {
            $arrayTimeString = str_split($timeString);
            foreach ($arrayTimeString as $key => $value) {
                $countarrayTimeString = $this->countItem($arrayTimeString);
                if (( $key == 2 && $countarrayTimeString == 4) || ( $key == 1 && $countarrayTimeString == 3)) {
                    $formatedTimeString .= ':';
                }
                $formatedTimeString .= $value;
            }
        }
        return $formatedTimeString;
    }

    /**
     * Archived countItem
     *
     * @param string $itemCount //The itemCount
     *
     * @return array $Order
     */
    public function countItem($itemCount)
    {
        return count($itemCount);
    }
}
