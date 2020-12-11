<?php
/**
 * Rateshipment file
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

use Magento\Framework\Stdlib\DateTime\DateTime;
/**
 * Rateshipment class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Rateshipment extends \Magento\Framework\App\Action\Action
{
    protected $salesOrder;
    protected $apiRate;
    protected $scopeConfig;
    protected $modelPackage;
    protected $timezone;
    protected $modelStore;
    protected $modelOrder;
    protected $resultJsonFactory;
    protected $dateTime;

    /**
     * Rateshipment __construct
     *
     * @param string   $context           //The Context
     * @param string   $resultJsonFactory //The resultJsonFactory
     * @param string   $modelPackage      //The modelPackage
     * @param string   $salesModel        //The sales Model
     * @param string   $apiRate           //The apiRate
     * @param string   $scopeConfig       //The scopeConfig
     * @param string   $timezone          //The time zone
     * @param string   $modelStore        //The modelStore
     * @param string   $modelOrder        //The model Order
     * @param DateTime $dateTime          //The time
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \UPS\Shipping\Model\Package $modelPackage,
        \Magento\Sales\Model\Order $salesModel,
        \UPS\Shipping\API\Rate $apiRate,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Store\Model\StoreManagerInterface $modelStore,
        \UPS\Shipping\Model\Order $modelOrder,
        DateTime $dateTime
    ) {
        $this->modelStore = $modelStore;
        $this->timezone = $timezone;
        $this->modelPackage = $modelPackage;
        $this->scopeConfig = $scopeConfig;
        $this->salesOrder = $salesModel;
        $this->apiRate = $apiRate;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->modelOrder = $modelOrder;
        $this->dateTime = $dateTime;
        parent::__construct($context);
    }

    /**
     * Rateshipment execute
     *
     * @return null
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $shipFrom = json_decode($this->getRequest()->getParam('shipfrom'));
        $shipTo = json_decode($this->getRequest()->getParam('shipto'));
        $shippingType = json_decode($this->getRequest()->getParam('ShippingType'));
        $packageData = json_decode($this->getRequest()->getParam('Package'));
        $accessorialService = json_decode($this->getRequest()->getParam('AccessorialService'));
        $cod = $this->getRequest()->getParam('COD');
        $OrderIdMagento = $this->getRequest()->getParam('OrderIdMagento');
        $OrderValue = $this->getRequest()->getParam('OrderValue');
        $order = $this->salesOrder->load($OrderIdMagento);
        //hadling date delivery
        $cutOfTime = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_CUT_OFF_TIME;
        $timeService = (int) $this->scopeConfig->getValue($cutOfTime) . '00';
        $timeNow = (int) implode('', explode(":", $this->timezone->date($this->dateTime->timestamp())->format('H:i')));
        if ($timeNow < $timeService || $timeNow == $timeService) {
            $dateAPI = $this->timezone->date($this->dateTime->timestamp())->format('Ymd');
        } else {
            $date = sprintf('%02d', (int) $this->timezone->date($this->dateTime->timestamp())->format('d') + 1);
            $dateAPI = $this->timezone->date($this->dateTime->timestamp())->format('Ym') . $date;
        }
        $checkEdit = $this->getRequest()->getParam('editshipment');
        // create data Request
        $name = $order->getShippingAddress()->getFirstname() . ' ' . $order->getShippingAddress()->getLastname();
        if ($shippingType['0'] == 'ADD' && $checkEdit == 1) {
            $shiptoFormat[] = $shipTo[0];
            $shiptoFormat[] = $shipTo[0];
            $shiptoFormat[] = $shipTo[2];
            $shiptoFormat[] = $shipTo[3];
            $shiptoFormat[] = $shipTo[4];
            $shiptoFormat[] = $shipTo[5];
            $shiptoFormat[] = $shipTo[6];
            $shiptoFormat[] = $shipTo[1];
            $shiptoFormat[] = implode("", explode(" ", $shipTo[7]));
            $shiptoFormat[] = $shipTo[8];
            $shiptoFormat[] = $shipTo[9];
        } else {
            if ($order->getCustomerName() != 'Guest') {
                $name = $order->getShippingAddress()->getFirstname() . ' '. $order->getShippingAddress()->getLastname();
            } else {
                $name = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
            }
            $shiptoFormat[] = $name;
            $shiptoFormat[] = $name;
            $shiptoFormat[] = $order->getShippingAddress()->getTelephone();
            $shiptoFormat[] = $order->getShippingAddress()->getStreetLine(1);
            $shiptoFormat[] = $order->getShippingAddress()->getStreetLine(2);
            $shiptoFormat[] = $order->getShippingAddress()->getStreetLine(3);
            $shiptoFormat[] = $order->getShippingAddress()->getCity();
            $shiptoFormat[] = $order->getShippingAddress()->getRegionCode();
            $shiptoFormat[] = implode("", explode(" ", $order->getShippingAddress()->getPostcode()));
            $shiptoFormat[] = $order->getShippingAddress()->getCountryId();
            $shiptoFormat[] = $order->getShippingAddress()->getEmail();
        }
        $dataRequestRate = [
            "ShippingType" => $shippingType['0'],
            "Typerate" => "createshipment",
            "Request" => [
                "RequestOption" => "RATETIMEINTRANSIT"
            ],
            "DeliveryTimeInformation" => [
                "PackageBillType" => "03",
                "Pickup" => [
                    "Date" => $dateAPI
                ]
            ],
            "Shipper" => [
                "Name" => $shipFrom[0],
                "ShipperNumber" => $shipFrom[2],
                \UPS\Shipping\Helper\ConstantShipment::ADDRESS => [
                    \UPS\Shipping\Helper\ConstantShipment::ADDRESSLINE => [$shipFrom[4], $shipFrom[5], $shipFrom[6]],
                    "City" => $shipFrom[7],
                    \UPS\Shipping\Helper\ConstantShipment::STATEPROVINCECODE => (isset($shipFrom[10]) && !empty($shipFrom[10])) ? $shipFrom[10] : "XX",
                    \UPS\Shipping\Helper\ConstantShipment::POSTALCODE => implode("", explode(" ", $shipFrom[8])),
                    \UPS\Shipping\Helper\ConstantShipment::COUNTRYCODE => $shipFrom[9]
                ]
            ],
            "ShipTo" => [
                "Name" => $shiptoFormat[0],
                \UPS\Shipping\Helper\ConstantShipment::ADDRESS => [
                    \UPS\Shipping\Helper\ConstantShipment::ADDRESSLINE => [$shiptoFormat[3], $shiptoFormat[4], $shiptoFormat[5]],
                    "City" => $shiptoFormat[6],
                    \UPS\Shipping\Helper\ConstantShipment::STATEPROVINCECODE => $shiptoFormat[7],
                    \UPS\Shipping\Helper\ConstantShipment::POSTALCODE => $shiptoFormat[8],
                    \UPS\Shipping\Helper\ConstantShipment::COUNTRYCODE => $shiptoFormat[9]
                ]
            ],
            "ShipFrom" => [
                "Name" => $shipFrom[0],
                "ShipperNumber" => $shipFrom[2],
                \UPS\Shipping\Helper\ConstantShipment::ADDRESS => [
                    \UPS\Shipping\Helper\ConstantShipment::ADDRESSLINE => [$shipFrom[4], $shipFrom[5], $shipFrom[6]],
                    "City" => $shipFrom[7],
                    \UPS\Shipping\Helper\ConstantShipment::STATEPROVINCECODE => (isset($shipFrom[10]) && !empty($shipFrom[10])) ? $shipFrom[10] : "XX",
                    \UPS\Shipping\Helper\ConstantShipment::POSTALCODE => implode("", explode(" ", $shipFrom[8])),
                    \UPS\Shipping\Helper\ConstantShipment::COUNTRYCODE => $shipFrom[9]
                ]
            ],
            "PaymentDetails" => [
                "ShipmentCharge" => [
                    "BillShipper" => [
                        "AccountNumber" => $shipFrom[2]
                    ]
                ]
            ],
            "Service" => [
                "Code" => $shippingType['1'],
                \UPS\Shipping\Helper\ConstantShipment::DESCRIPTION => $shippingType['3']
            ],
            "InvoiceLineTotal" => [
                "CurrencyCode" => $order->getOrderCurrencyCode(),
                "MonetaryValue" => $OrderValue
            ],
            "ShipmentRatingOptions" => [
                "NegotiatedRatesIndicator" => ""
            ]
        ];
        if ($shippingType['0'] == 'AP') {
            $dataRequestRate['AlternateDeliveryAddress'] = [
                "Name" => $shipTo[0],
                "AttentionName" => $shipTo[0],
                \UPS\Shipping\Helper\ConstantShipment::ADDRESS => [
                    \UPS\Shipping\Helper\ConstantShipment::ADDRESSLINE => [$shipTo[3], $shipTo[4], $shipTo[5]],
                    "City" => $shipTo[6],
                    \UPS\Shipping\Helper\ConstantShipment::STATEPROVINCECODE => $shipTo[1],
                    \UPS\Shipping\Helper\ConstantShipment::POSTALCODE => implode("", explode(" ", $shipTo[7])),
                    \UPS\Shipping\Helper\ConstantShipment::COUNTRYCODE => $shipTo[8]
                ]
            ];
        }
        $this->setDataRequestRate($dataRequestRate, $accessorialService, $cod, $shippingType, $packageData);
        //call API
        $check = true;
        $resultJson = [];
        $messege = "";
        $dataresponseRate = json_decode($this->apiRate->rate($dataRequestRate));
        if (isset($dataresponseRate->RateResponse->Response->ResponseStatus->Code)
            && $dataresponseRate->RateResponse->Response->ResponseStatus->Code == 1
        ) {
            $responseRate = $dataresponseRate->RateResponse->RatedShipment;
            $resultJson['CurrencyCode'] = $responseRate->TotalCharges->CurrencyCode;
            $resultJson['MonetaryValue'] = $responseRate->TotalCharges->MonetaryValue;
            if (isset($responseRate->NegotiatedRateCharges)) {
                $resultJson['CurrencyCode'] = $responseRate->NegotiatedRateCharges->TotalCharge->CurrencyCode;
                $resultJson['MonetaryValue'] = $responseRate->NegotiatedRateCharges->TotalCharge->MonetaryValue;
            }
            $date1 = $responseRate->TimeInTransit->ServiceSummary->EstimatedArrival->Arrival->Date;
            $time1 = $responseRate->TimeInTransit->ServiceSummary->EstimatedArrival->Arrival->Time;
            $date2 = $responseRate->TimeInTransit->ServiceSummary->EstimatedArrival->Pickup->Date;
            $time2 = $responseRate->TimeInTransit->ServiceSummary->EstimatedArrival->Pickup->Time;
            if ((int) $date1 > (int) $date2) {
                $date = $date1;
                $time = $time1;
            } else {
                $date = $date2;
                $time = $time2;
            }
            $resultJson['TimeInTransit'] = substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2)
            . ' ' . substr($time, 0, 2) . ':' . substr($time, 2, 2) . ':' . substr($time, 4, 2);
            $messege = "";
        } else {
            $check = false;
            $messege = $dataresponseRate->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description;
        }
        return $result->setData(['check' => $check, 'message' => $messege, 'result' => $resultJson]);
    }

    /**
     * Rateshipment setDataRequestRate
     *
     * @param string $dataRequestRate    //The dataRequestRate
     * @param string $accessorialService //The accessorialService
     * @param string $cod                //The cod
     * @param string $shippingType       //The shippingType
     * @param string $packageData        //The packageData
     *
     * @return null
     */
    public function setDataRequestRate(&$dataRequestRate, $accessorialService, $cod, $shippingType, $packageData)
    {
        // create data request accessorials
        foreach ($accessorialService as $key => $value) {
            $dataRequestRate[\UPS\Shipping\Helper\ConstantShipment::ACCESSORIALS][$key] = [];
        }
        if ($cod == 1) {
            // COD for Ship
            $dataRequestRate["AlternateDeliveryAddress"]['COD'] = '1';
            if ($shippingType['0'] == 'AP') {
                $dataRequestRate[\UPS\Shipping\Helper\ConstantShipment::ACCESSORIALS]["UPS_ACSRL_ACCESS_POINT_COD"] =[];
            } else {
                $dataRequestRate[\UPS\Shipping\Helper\ConstantShipment::ACCESSORIALS]["UPS_ACSRL_TO_HOME_COD"] = [];
            }
        }
        $packageAPI = [];
        if (!empty($packageData) && isset($packageData[0])) {
            $selectedOpenOrder = $this->modelOrder->getDetailOrder($packageData[0]);
            $openOrder = [];
            if (!empty($selectedOpenOrder)) {
                if (!empty($selectedOpenOrder['package'])) {
                    $openOrder = json_decode($selectedOpenOrder['package'], true);
                }
            }
            // create data request Package
            foreach ($packageData as $key => $value) {
                if (is_numeric($value) && isset($openOrder[0])) {
                    $packageAPI[] = $openOrder[0];
                } else {
                    $packageDecode = json_decode($value);
                    $tmpArray = [];
                    if (!empty($packageDecode) && (is_array($packageDecode) || is_object($packageDecode))) {
                        foreach ($packageDecode as $key => $value) {
                            $tmpArray[$key] = $value;
                        }
                    } else {
                        $tmpArray = [
                            'length' => 0,
                            'width' => 0,
                            'height' => 0,
                            'unit_dimension' => 'cm',
                            'weight' => 0,
                            'unit_weight' => 'kgs'
                        ];
                    }
                    $packageAPI[] = $tmpArray;
                }
            }
        }
        if (!empty($packageAPI)) {
            foreach ($packageAPI as $key => $value) {
                $dataRequestRate["Package"][] = [
                    "Dimensions" => [
                        "UnitOfMeasurement" => [
                            "Code" => $value['unit_dimension'],
                            \UPS\Shipping\Helper\ConstantShipment::DESCRIPTION => ('IN' == strtoupper($value['unit_dimension'])) ? "inches" : "centimeter"
                        ],
                        "Length" => $value['length'],
                        "Width" => $value['width'],
                        "Height" => $value['height']
                    ],
                    "PackageWeight" => [
                        "UnitOfMeasurement" => [
                            "Code" => $value['unit_weight'],
                            \UPS\Shipping\Helper\ConstantShipment::DESCRIPTION =>  ('LBS' == strtoupper($value['unit_weight'])) ? "Pounds" : "kilograms"
                        ],
                        "Weight" => $value['weight']
                    ],
                    "Packaging" => [
                        "Code" => "02"
                    ],
                    "PackagingType" => [
                        "Code" => "02"
                    ],
                ];
            }
        }
    }
}
