<?php
/**
 * Createshipment file
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
 * Createshipment class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Createshipment extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $modelAccount;
    protected $modelOrder;
    protected $modelShipment;
    protected $salesOrder;
    protected $modelPackage;
    protected $apiShip;
    protected $timezone;
    protected $countryModel;
    protected $modelStore;
    protected $apiManager;
    protected $scopeConfig;
    protected $regionModel;
    protected $dateTime;
    protected $licenseModel;
    protected $apiHandshake;
    protected $apiAccount;
    protected $checkoutSession;

    /**
     * Createshipment __construct
     *
     * @param string   $scopeConfig       //The scopeConfig
     * @param string   $context           //The context
     * @param string   $resultJsonFactory //The resultJsonFactory
     * @param string   $modelAccount      //The modelAccount
     * @param string   $modelOrder        //The modelOrder
     * @param string   $modelShipment     //The modelShipment
     * @param string   $modelPackage      //The modelPackage
     * @param string   $salesModel        //The salesModel
     * @param string   $apiShip           //The apiShip
     * @param string   $timezone          //The timezone
     * @param string   $countryModel      //The countryModel
     * @param string   $modelStore        //The modelStore
     * @param string   $apiManager        //The apiManager
     * @param string   $regionModel       //The regionModel
     * @param string   $configWriter      //The configWriter
     * @param string   $cacheTypeList     //The cacheTypeList
     * @param string   $modelLicense      //modelLicense
     * @param string   $apiHandshake      //The apiHandshake
     * @param string   $apiAccount        //The apiAccount
     * @param string   $checkoutSession   //The checkoutSession
     * @param DateTime $dateTime          //The dateTime
     *
     * @return string $dataRequestShip
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \UPS\Shipping\Model\Account $modelAccount,
        \UPS\Shipping\Model\Order $modelOrder,
        \UPS\Shipping\Model\Shipment $modelShipment,
        \UPS\Shipping\Model\Package $modelPackage,
        \Magento\Sales\Model\Order $salesModel,
        \UPS\Shipping\API\Ship $apiShip,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Directory\Model\Country $countryModel,
        \Magento\Store\Model\StoreManagerInterface $modelStore,
        \UPS\Shipping\API\Manager\Index $apiManager,
        \Magento\Directory\Model\RegionFactory $regionModel,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \UPS\Shipping\Model\License $modelLicense,
        \UPS\Shipping\API\Handshake $apiHandshake,
        \UPS\Shipping\API\Account $apiAccount,
        \Magento\Checkout\Model\Session $checkoutSession,
        DateTime $dateTime
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->apiManager = $apiManager;
        $this->modelStore = $modelStore;
        $this->countryModel = $countryModel;
        $this->timezone = $timezone;
        $this->modelPackage = $modelPackage;
        $this->salesOrder = $salesModel;
        $this->apiShip = $apiShip;
        $this->modelShipment = $modelShipment;
        $this->modelOrder = $modelOrder;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->modelAccount = $modelAccount;
        $this->regionModel = $regionModel;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->licenseModel = $modelLicense;
        $this->apiHandshake = $apiHandshake;
        $this->apiAccount = $apiAccount;
        $this->dateTime = $dateTime;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    /**
     * Createshipment execute
     *
     * @return string $dataRequestShip
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            switch ($this->getRequest()->getParam('method')) {
            case 'getInfoAccount':
                    $idAccount = $this->getRequest()->getParam('id');
                    $detailAccount = $this->modelAccount->getInfoAccount($idAccount);
                    $this->stateNameDetail($detailAccount);
                    $this->countryDetail($detailAccount);
                    $this->addressDetail($detailAccount);
                return $result->setData(['result' => $detailAccount]);
            case 'CreateAPI':
                    $check = true;
                    $messege = "";
                    $this->setCreateAPI($check, $messege);
                return $result->setData(['check' => $check, 'message' => $messege]);
            default:
                break;
            }
        } else {
            return $result->setData(['check' => false, 'message' => '']);
        }
    }

    /**
     * Createshipment setCreateAPI
     *
     * @param string $check   //The check
     * @param string $messege //The messege
     *
     * @return null
     */
    public function setCreateAPI(&$check, &$messege)
    {
        // data ajax
        $shipFrom = json_decode($this->getRequest()->getParam('shipfrom'));
        $shipTo = json_decode($this->getRequest()->getParam('shipto'));
        $shippingType = json_decode($this->getRequest()->getParam('ShippingType'));
        $AccessorialService = json_decode($this->getRequest()->getParam('AccessorialService'));
        $listOrder = json_decode($this->getRequest()->getParam('idorder'));
        $cod = $this->getRequest()->getParam('COD');
        $OrderValue = $this->getRequest()->getParam('OrderValue');
        $OrderIdMagento = $this->getRequest()->getParam('OrderIdMagento');
        $order = $this->salesOrder->load($OrderIdMagento);
        $packageAPI = [];
        $checkEdit = $this->getRequest()->getParam('editshipment');
        $shiptoFormat = [];
        $shippingTypeName = '';

        $this->setShiptoFormat($shippingType, $checkEdit, $shipTo, $shiptoFormat, $order);
        // request api
        $dataRequestShip = [
            "ShippingType" => $shippingType['0'],
            "CurrencyCode" => $order->getOrderCurrencyCode(),
            "MonetaryValue" => $OrderValue,
            "Shipper" => [
                "Name" => $shipFrom[0],
                \UPS\Shipping\Helper\ConstantShipment::ATTENTIONNAME => $shipFrom[1],
                "ShipperNumber" => $shipFrom[2],
                \UPS\Shipping\Helper\ConstantShipment::PHONE => [
                    \UPS\Shipping\Helper\ConstantShipment::NUMBER => $shipFrom[3]
                ],
                \UPS\Shipping\Helper\ConstantShipment::ADDRESS => [
                    \UPS\Shipping\Helper\ConstantShipment::ADDRESSLINE => [
                        $shipFrom[4],
                        $shipFrom[5],
                        $shipFrom[6]
                    ],
                    "City" => $shipFrom[7],
                    \UPS\Shipping\Helper\ConstantShipment::STATEPROVINCECODE => (isset($shipFrom[10]) && !empty($shipFrom[10])) ? $shipFrom[10] : "XX",
                    \UPS\Shipping\Helper\ConstantShipment::POSTALCODE
                    => $this->getPostalCode($shipFrom[8]),
                    \UPS\Shipping\Helper\ConstantShipment::COUNTRYCODE => $shipFrom[9]
                ]
            ],
            "ShipTo" => [
                "Name" => $shiptoFormat[0],
                \UPS\Shipping\Helper\ConstantShipment::ATTENTIONNAME => $shiptoFormat[1],
                \UPS\Shipping\Helper\ConstantShipment::PHONE => [
                    \UPS\Shipping\Helper\ConstantShipment::NUMBER => $shiptoFormat[2],
                ],
                \UPS\Shipping\Helper\ConstantShipment::ADDRESS => [
                    \UPS\Shipping\Helper\ConstantShipment::ADDRESSLINE => [
                        $shiptoFormat[3],
                        $shiptoFormat[4],
                        $shiptoFormat[5]
                    ],
                    "City" => $shiptoFormat[6],
                    \UPS\Shipping\Helper\ConstantShipment::STATEPROVINCECODE => $shiptoFormat[7],
                    \UPS\Shipping\Helper\ConstantShipment::POSTALCODE => $this->getPostalCode($shiptoFormat[8]),
                    \UPS\Shipping\Helper\ConstantShipment::COUNTRYCODE => $shiptoFormat[9],
                ],
                "Email" => $shiptoFormat[10],
            ],
            "ShipFrom" => [
                "Name" => $shipFrom[0],
                \UPS\Shipping\Helper\ConstantShipment::ATTENTIONNAME => $shipFrom[1],
                \UPS\Shipping\Helper\ConstantShipment::PHONE => [
                    \UPS\Shipping\Helper\ConstantShipment::NUMBER => $shipFrom[3]
                ],
                \UPS\Shipping\Helper\ConstantShipment::ADDRESS => [
                    \UPS\Shipping\Helper\ConstantShipment::ADDRESSLINE => [
                        $shipFrom[4],
                        $shipFrom[5],
                        $shipFrom[6]
                    ],
                    "City" => $shipFrom[7],
                    \UPS\Shipping\Helper\ConstantShipment::STATEPROVINCECODE => (isset($shipFrom[10]) && !empty($shipFrom[10])) ? $shipFrom[10] : "XX",
                    \UPS\Shipping\Helper\ConstantShipment::POSTALCODE
                    => $this->getPostalCode($shipFrom[8]),
                    \UPS\Shipping\Helper\ConstantShipment::COUNTRYCODE => $shipFrom[9]
                ]
            ],
            'InvoiceLineTotal' => [
                "CurrencyCode" => $order->getOrderCurrencyCode(),
                "MonetaryValue" => $OrderValue,
            ],
            "Service" => [
                "Code" => $shippingType['1'],
                \UPS\Shipping\Helper\ConstantShipment::DESCRIPTION => $shippingType['3']
            ],
            "PaymentInformation" => [
                "ShipmentCharge" => [
                    "Type" => "01",
                    "BillShipper" => [
                        "AccountNumber" => $shipFrom[2] // number account
                    ]
                ]
            ],
            "ShipmentRatingOptions" => [
                "NegotiatedRatesIndicator" => ""
            ]
        ];

        if ($shippingType['0'] == 'AP') {
            if (empty($dataRequestShip['ShipTo']['Phone']['Number'])) {
                $dataRequestShip['ShipTo']['Phone']['Number'] = '0000000000';
            }
            $stateProvinceCodeEU = $shipTo[1];
            // US and Canada, UPSEUPUAT-911
            if (!in_array($shipTo[8], ['US', 'CA', 'IE'])) {
                $stateProvinceCodeEU = 'XX';
            }
            $dataRequestShip["AlternateDeliveryAddress"] = [
                "Name" => $shipTo[0],
                \UPS\Shipping\Helper\ConstantShipment::ATTENTIONNAME => $shipTo[1],
                \UPS\Shipping\Helper\ConstantShipment::ADDRESS => [
                    \UPS\Shipping\Helper\ConstantShipment::ADDRESSLINE
                    => str_replace('&#xD;', ' ', $shipTo[3]),
                    "City" => $shipTo[6],
                    \UPS\Shipping\Helper\ConstantShipment::STATEPROVINCECODE => $stateProvinceCodeEU,
                    \UPS\Shipping\Helper\ConstantShipment::POSTALCODE
                    => $this->getPostalCode($shipTo[7]),
                    \UPS\Shipping\Helper\ConstantShipment::COUNTRYCODE => $shipTo[8]
                ]
            ];
        }

        foreach ($AccessorialService as $key => $value) {
            $dataRequestShip[\UPS\Shipping\Helper\ConstantShipment::ACCESSORIALS][$key] = [];
        }
        $this->requestCod($cod, $shippingType, $dataRequestShip);
        $this->requestPackage($packageAPI);

        $this->setPackageAPI($packageAPI, $dataRequestShip);
        //call API
        // US and Canada, UPSEUPUAT-911
        if (!in_array($shipTo[8], ['US', 'CA', 'IE'])) {
            $dataRequestShip['ShipTo'][\UPS\Shipping\Helper\ConstantShipment::ADDRESS][\UPS\Shipping\Helper\ConstantShipment::STATEPROVINCECODE] = 'XX';
        }
        $dataresponseShip = json_decode($this->apiShip->ship($dataRequestShip));
        if (isset($dataresponseShip->ShipmentResponse->Response->ResponseStatus->Code)
            && $dataresponseShip->ShipmentResponse->Response->ResponseStatus->Code == 1
        ) {
            $dataResponseAPI = $dataresponseShip->ShipmentResponse->ShipmentResults;
            $shippingFee = $dataResponseAPI->ShipmentCharges->TotalCharges->MonetaryValue;
            if (isset($dataResponseAPI->NegotiatedRateCharges)) {
                $shippingFee = $dataResponseAPI->NegotiatedRateCharges->TotalCharge->MonetaryValue;
            }
            $shipmentNumber = $dataResponseAPI->ShipmentIdentificationNumber;
            $date = $this->dateTime->gmtDate('Y-m-d H:i:s');
            $accessorial = $this->getRequest()->getParam('AccessorialService');
            $products = [];
            //here
            foreach ($listOrder as $key => $value) {
                foreach ($packageAPI as $key1 => $value1) {
                        $this->getTrackingNumber($dataResponseAPI, $trackingNumber, $key1);
                        $this->unitPackage($value1);
                        $detailPackage = $value1['length'] . 'x' . $value1['width'] . 'x' . $value1['height']
                        . ' ' . $value1[\UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION] . ', '
                        . $value1['weight'] . $value1[\UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT];
                        $this->modelShipment->createTracking($shipmentNumber, $trackingNumber, $detailPackage, $value);
                        $packageAPI[$key1]['trackingnumber'] = $trackingNumber;
                }
                    $getIdOrderMagento = $this->modelOrder->getIdMagento($value);
                    $orderIdMagento = $getIdOrderMagento['order_id_magento'];
                    $this->saveOrderData($orderIdMagento);
                    $order = $this->getOrderData($getIdOrderMagento);
                    $this->productDetail($order, $products);
            }
            $apId = (isset($shipTo[10]) ? $shipTo[10] : '');
            $stateName = $this->regionModel->create()->loadByCode($shipTo[1], $order->getShippingAddress()->getCountryId())->getName();

            $idShipment = $this->modelShipment->createShipment($shipmentNumber, $shippingType[2], $accessorial, $date, $shippingFee, $cod, $apId, $shipTo[0], $stateName, $shipTo[2], $shipTo[3], $shipTo[4], $shipTo[5], $shipTo[6], $shipTo[7], $shipTo[8], $shipTo[9], $OrderValue);
            $this->setAllProperties($listOrder, $idShipment, $shippingType, $shippingTypeName, $packageAPI);
            $dataShipmentManager = [];
            $merchantKey = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY);
            //offPluginManager 2019-03-18
            $this->runTransferShipments($merchantKey, $shipFrom, $shipmentNumber, $shippingFee, $OrderValue, $shipTo, $shippingType, $shippingTypeName, $cod, $products, $AccessorialService, $packageAPI);
            $check = true;
            $messege = "";
        } else {
                $check = false;
                $messege = $dataresponseShip->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description;
        }
    }

    /**
     * Bathshipment runTransferShipments
     *
     * @param string $merchantKey        //The merchantKey
     * @param string $shipFrom           //The shipFrom
     * @param string $shipmentNumber     //The shipmentNumber
     * @param string $shippingFee        //The shippingFee
     * @param string $OrderValue         //The OrderValue
     * @param string $shipTo             //The shipTo
     * @param string $shippingType       //The shippingType
     * @param string $shippingTypeName   //The shippingTypeName
     * @param string $cod                //The cod
     * @param string $products           //The products
     * @param string $AccessorialService //The AccessorialService
     * @param string $packageAPI         //The packageAPI
     *
     * @return null
     */
    public function runTransferShipments($merchantKey, $shipFrom, $shipmentNumber, $shippingFee, $OrderValue, $shipTo, $shippingType, $shippingTypeName, $cod, $products, $AccessorialService, $packageAPI)
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
            $dataShipmentManager[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN] = $bearerToken;
            $dataShipmentManager['shipment'] = [
                'merchantKey'       => $merchantKey,
                'accountNumber'     => $shipFrom[2],
                'shipmentId'        => $shipmentNumber,
                'fee'               => $shippingFee,
                'revenue'           => $OrderValue,
                'address'           => str_replace('&#xD;', ' ', $shipTo[3]),
                'postalCode'        => $this->getPostalCode($shipTo[7]),
                'city'              => $shipTo[6],
                \UPS\Shipping\Helper\ConstantShipment::COUNTRY => $shipTo[8],
                'serviceType'       => $shippingType['0'],
                'serviceCode'       => $shippingType['1'],
                'serviceName'       => $shippingTypeName,
                'isCashOnDelivery'  => $cod,
                'products'          => $products
            ];
            $dataShipmentManager['accessorials'] = $AccessorialService;
            $dataShipmentManager['packages'] = $packageAPI;
            $responseApi = $this->apiManager->callTransferShipments($dataShipmentManager);
            $responseApi = json_decode($responseApi);
            // bearerToken expired
            if (isset($responseApi->error->errorCode) && $responseApi->error->errorCode == '401') {
                if ($this->resetRegisteredToken()) {
                    $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                    $bearerSessionToken = $this->checkoutSession->getBearLongToken();
                    if ($bearerSessionToken != $bearerToken) {
                        $bearerToken = $bearerSessionToken;
                    }
                    $dataShipmentManager[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN] = $bearerToken;
                    $this->apiManager->callTransferShipments($dataShipmentManager);
                }
            }
        }
    }

    /**
     * Archived getOrderData
     *
     * @param string $orderIdMagento //The orderIdMagento
     *
     * @return array $Order
     */
    public function getOrderData($orderIdMagento)
    {
        return $this->salesOrder->load($orderIdMagento['order_id_magento']);
    }

    /**
     * Archived saveOrderData
     *
     * @param string $orderIdMagento //The orderIdMagento
     *
     * @return array $Order
     */
    public function saveOrderData($orderIdMagento)
    {
        $this->salesOrder->load($orderIdMagento)->setStatus('shipped', true)->save();
    }

    /**
     * Createshipment setAllProperties
     *
     * @param string $listOrder        //The listOrder
     * @param string $idShipment       //The idShipment
     * @param string $shippingType     //The shippingType
     * @param string $shippingTypeName //The shippingTypeName
     * @param string $packageAPI       //The packageAPI
     *
     * @return null
     */
    public function setAllProperties($listOrder, $idShipment, $shippingType, &$shippingTypeName, &$packageAPI)
    {
        foreach ($listOrder as $key => $value) {
            $this->modelOrder->updateStatusOrder($value, $idShipment);
        }
        $serviceName = trim($shippingType['3']);
        if ($serviceName == "UPS Access Point Economy" ) {
            $shippingTypeName = __("UPS Access Point™ Economy");
        } elseif ($serviceName == 'UPS Standard') {
            $shippingTypeName = __('UPS® Standard');
        } elseif ($serviceName == 'UPS Express 12:00') {
            $shippingTypeName = __('UPS Express 12:00');
        } elseif ($serviceName == 'UPS Ground') {
            $shippingTypeName = __('UPS® Ground');
        } elseif ($serviceName == 'UPS Next Day Air Early') {
            $shippingTypeName = __('UPS Next Day Air® Early');
        } elseif ($serviceName == 'UPS Standard - Saturday Delivery') {
            $shippingTypeName = __('UPS® Standard - Saturday Delivery');
        } elseif ($serviceName == 'UPS Express - Saturday Delivery') {
            $shippingTypeName = __('UPS Express® - Saturday Delivery');
        } else {
            $shippingTypeName = $serviceName.'®';
        }
        foreach ($packageAPI as $key1 => $value1) {
            $packageAPI[$key1][\UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT]
                = ($value1[\UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT] == 'kgs') ? 'Kg' : 'Pounds';
        }
    }

    /**
     * Createshipment setPackageAPI
     *
     * @param string $packageAPI      //The packageAPI
     * @param string $dataRequestShip //The dataRequestShip
     *
     * @return null
     */
    public function setPackageAPI($packageAPI, &$dataRequestShip)
    {
        if (!empty($packageAPI) && is_array($packageAPI)) {
            foreach ($packageAPI as $key => $value) {
                $dataRequestShip["Package"][] = [
                    "Dimensions" => [
                        "UnitOfMeasurement" => [
                            "Code" => $value[\UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION],
                            \UPS\Shipping\Helper\ConstantShipment::DESCRIPTION =>  ('IN' == strtoupper($value[\UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION])) ? "inches" : "centimeter"
                        ],
                        "Length" => $value['length'],
                        "Width" => $value['width'],
                        "Height" => $value['height']
                    ],
                    "PackageWeight" => [
                        "UnitOfMeasurement" => [
                            "Code" => $value[\UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT],
                            \UPS\Shipping\Helper\ConstantShipment::DESCRIPTION =>  ('LBS' == strtoupper($value[\UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT])) ? "Pounds" : "kilograms"
                        ],
                        "Weight" => $value['weight']
                    ],
                    "Packaging" => [
                        "Code" => "02"
                    ],
                    "PackagingType" => [
                        "Code" => "02"
                    ]
                ];
            }
        }
    }

    /**
     * Createshipment countryDetail
     *
     * @param string $shippingType //The shippingType
     * @param string $checkEdit    //The checkEdit
     * @param string $shipTo       //The shipTo
     * @param string $shiptoFormat //The shiptoFormat
     * @param string $order        //The order
     *
     * @return null
     */
    public function setShiptoFormat($shippingType, $checkEdit, $shipTo, &$shiptoFormat, $order)
    {
        if ($shippingType['0'] == 'ADD' && $checkEdit == 1) {
            $shiptoFormat[] = $shipTo[0];
            $shiptoFormat[] = $shipTo[0];
            $shiptoFormat[] = $shipTo[2];
            $shiptoFormat[] = $shipTo[3];
            $shiptoFormat[] = $shipTo[4];
            $shiptoFormat[] = $shipTo[5];
            $shiptoFormat[] = $shipTo[6];
            $shiptoFormat[] = $shipTo[1];
            $shiptoFormat[] = $this->getPostalCode($shipTo[7]);
            $shiptoFormat[] = $shipTo[8];
            $shiptoFormat[] = $shipTo[9];
        } else {
                $name = $order->getShippingAddress()->getFirstname() . ' '
                . $order->getShippingAddress()->getLastname();
                $shiptoFormat[] = $name;
                $shiptoFormat[] = $name;
                $shiptoFormat[] = $order->getShippingAddress()->getTelephone();
                $shiptoFormat[] = $order->getShippingAddress()->getStreetLine(1);
                $shiptoFormat[] = $order->getShippingAddress()->getStreetLine(2);
                $shiptoFormat[] = $order->getShippingAddress()->getStreetLine(3);
                $shiptoFormat[] = $order->getShippingAddress()->getCity();
                $shiptoFormat[] = $order->getShippingAddress()->getRegionCode();
                $shiptoFormat[] = $this->getPostalCode($order->getShippingAddress()->getPostcode());
                $shiptoFormat[] = $order->getShippingAddress()->getCountryId();
                $shiptoFormat[] = $order->getShippingAddress()->getEmail();
        }
    }

    /**
     * Createshipment countryDetail
     *
     * @param string $detailAccount //The detailAccount
     *
     * @return null
     */
    public function countryDetail(&$detailAccount)
    {
        $shipmentCountry = $detailAccount[\UPS\Shipping\Helper\ConstantShipment::COUNTRY];
        if ($shipmentCountry != '' && $shipmentCountry != null) {
            $country = $this->countryModel->loadByCode($shipmentCountry)->getName();
            $detailAccount['Macountry'] = $shipmentCountry;
        } else {
            $country = '';
            $detailAccount['Macountry'] = '';
        }
        $detailAccount[\UPS\Shipping\Helper\ConstantShipment::COUNTRY] = $country;
    }

    /**
     * Createshipment stateNameDetail
     *
     * @param string $detailAccount //The detailAccount
     *
     * @return null
     */
    public function stateNameDetail(&$detailAccount)
    {
        $detailAccount['stateNameDetail'] = '';
        if (!empty($detailAccount['state_province_code']) && $detailAccount['state_province_code'] != 'XX') {
            $detailAccount['stateNameDetail'] = $this->regionModel->create()->loadByCode($detailAccount['state_province_code'], $detailAccount['country'])->getName();
        }
    }

    /**
     * Createshipment addressDetail
     *
     * @param string $detailAccount //The detailAccount
     *
     * @return null
     */
    public function addressDetail(&$detailAccount)
    {
        $Address = [];
        if ($detailAccount[\UPS\Shipping\Helper\ConstantShipment::ADDRESS_1] != ""
            && $detailAccount[\UPS\Shipping\Helper\ConstantShipment::ADDRESS_1] != null
        ) {
            $Address[] = $detailAccount[\UPS\Shipping\Helper\ConstantShipment::ADDRESS_1];
        }
        if ($detailAccount[\UPS\Shipping\Helper\ConstantShipment::ADDRESS_2] != ""
            && $detailAccount[\UPS\Shipping\Helper\ConstantShipment::ADDRESS_2] != null
        ) {
            $Address[] = $detailAccount[\UPS\Shipping\Helper\ConstantShipment::ADDRESS_2];
        }
        if ($detailAccount[\UPS\Shipping\Helper\ConstantShipment::ADDRESS_3] != ""
            && $detailAccount[\UPS\Shipping\Helper\ConstantShipment::ADDRESS_3] != null
        ) {
            $Address[] = $detailAccount[\UPS\Shipping\Helper\ConstantShipment::ADDRESS_3];
        }
        $detailAccount['MergeAddress'] = implode(", ", $Address);
    }

    /**
     * Createshipment requestCod
     *
     * @param string $cod             //The cod
     * @param string $shippingType    //The shippingType
     * @param string $dataRequestShip //The dataRequestShip
     *
     * @return string $dataRequestShip
     */
    public function requestCod($cod, $shippingType, &$dataRequestShip)
    {
        if ($cod == 1) {
            // COD for Ship
            $dataRequestShip["AlternateDeliveryAddress"]['COD'] = '1';
            if ($shippingType['0'] == 'AP') {
                $dataRequestShip[\UPS\Shipping\Helper\ConstantShipment::ACCESSORIALS]
                ["UPS_ACSRL_ACCESS_POINT_COD"] = [];
            } else {
                $dataRequestShip[\UPS\Shipping\Helper\ConstantShipment::ACCESSORIALS]
                ["UPS_ACSRL_TO_HOME_COD"] = [];
            }
        }
    }

    /**
     * Createshipment unitPackage
     *
     * @param string $value1 //The value1
     *
     * @return string $dataRequestShip
     */
    public function unitPackage(&$value1)
    {
        if ($value1[\UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION] == 'cm') {
            $value1[\UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION] = 'cm';
        } else {
            $value1[\UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION] = 'inch';
        }
        if ($value1[\UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT] == 'kgs') {
            $value1[\UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT] = 'Kg';
        } else {
            $value1[\UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT] = 'Pounds';
        }
    }

    /**
     * Createshipment productDetail
     *
     * @param string $order    //The order
     * @param string $products //The products
     *
     * @return null
     */
    public function productDetail($order, &$products)
    {
        foreach ($order->getItemsCollection() as $item) {
            if ($item->getParentItemId() == '' || $item->getParentItemId() == null) {
                $checkParent = $this->modelOrder->checkParentItem($item->getItemId());
                $countCheckParents = $this->countCheckParent($checkParent);
                if ($countCheckParents == 0) {
                    array_push($products, round($item->getQtyOrdered()) . ' x ' . $item->getProduct()->getName());
                }
            } else {
                array_push($products, round($item->getQtyOrdered()) . ' x ' . $item->getProduct()->getName());
            }
        }
    }

    /**
     * Archived countCheckParent
     *
     * @param string $checkParent //The checkParent
     *
     * @return array $Order
     */
    public function countCheckParent($checkParent)
    {
        return count($checkParent);
    }

    /**
     * Shipmentdetail getPackageStatus
     * only get number and characters
     *
     * @param string $postalCode //The postalCode
     *
     * @return string $trackingStatus
     */
    public function getPostalCode($postalCode)
    {
        $country = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE);
        $returnPostCode = preg_replace("/[^a-zA-Z0-9]+/", "", $postalCode);
        return trim($returnPostCode);
    }

    /**
     * Createshipment getTrackingNumber
     *
     * @param string $dataResponseAPI //The dataResponseAPI
     * @param string $trackingNumber  //The trackingNumber
     * @param string $key1            //The key1
     *
     * @return null
     */
    public function getTrackingNumber($dataResponseAPI, &$trackingNumber, $key1)
    {
        if (isset($dataResponseAPI->PackageResults->TrackingNumber)) {
            $trackingNumber = $dataResponseAPI->PackageResults->TrackingNumber;
        } else {
            $trackingNumber = $dataResponseAPI->PackageResults[$key1]->TrackingNumber;
        }
    }

    /**
     * Createshipment unlinkFile
     *
     * @param string $packageAPI //The packageAPI
     *
     * @return string $dataRequestShip
     */
    public function requestPackage(&$packageAPI)
    {
        $package = json_decode($this->getRequest()->getParam('Package'));

        if (!empty($package) && isset($package[0])) {
            $selectedOpenOrder = $this->modelOrder->getDetailOrder($package[0]);
            $openOrder = [];
            if (!empty($selectedOpenOrder)) {
                if (!empty($selectedOpenOrder['package'])) {
                    $openOrder = json_decode($selectedOpenOrder['package'], true);
                }
            }
            foreach ($package as $key => $value) {
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
        } else {
            $packageAPI[] = [
                'length' => 0,
                'width' => 0,
                'height' => 0,
                'unit_dimension' => 'cm',
                'weight' => 0,
                'unit_weight' => 'kgs'
            ];
        }
        if (empty($packageAPI)) {
            $packageAPI[] = [
                'length' => 0,
                'width' => 0,
                'height' => 0,
                'unit_dimension' => 'cm',
                'weight' => 0,
                'unit_weight' => 'kgs'
            ];
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
