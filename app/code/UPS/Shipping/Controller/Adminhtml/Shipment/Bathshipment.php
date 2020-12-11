<?php
/**
 * Bathshipment file
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
 * Bathshipment class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Bathshipment extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $modelOrder;
    protected $modelShipment;
    protected $salesOrder;
    protected $modelPackage;
    protected $apiShip;
    protected $timezone;
    protected $modelStore;
    protected $apiManager;
    protected $scopeConfig;
    protected $regionModel;
    protected $dateTime;
    protected $licenseModel;
    protected $apiHandshake;
    protected $apiAccount;
    protected $configWriter;
    protected $cacheTypeList;
    protected $checkoutSession;

    /**
     * Bathshipment __construct
     *
     * @param string   $scopeConfig       //The scopeConfig
     * @param string   $context           //The context
     * @param string   $resultJsonFactory //The resultJsonFactory
     * @param string   $modelOrder        //The modelOrder
     * @param string   $modelShipment     //The modelShipment
     * @param string   $modelPackage      //The modelPackage
     * @param string   $salesModel        //The salesModel
     * @param string   $apiShip           //The apiShip
     * @param string   $timezone          //The timezone
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
     * @return null
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \UPS\Shipping\Model\Order $modelOrder,
        \UPS\Shipping\Model\Shipment $modelShipment,
        \UPS\Shipping\Model\Package $modelPackage,
        \Magento\Sales\Model\Order $salesModel,
        \UPS\Shipping\API\Ship $apiShip,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
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
        $this->timezone = $timezone;
        $this->modelPackage = $modelPackage;
        $this->salesOrder = $salesModel;
        $this->apiShip = $apiShip;
        $this->modelShipment = $modelShipment;
        $this->modelOrder = $modelOrder;
        $this->resultJsonFactory = $resultJsonFactory;
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
     * Bathshipment execute
     *
     * @return null
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $shipFrom = json_decode($this->getRequest()->getParam('shipfrom'));
        $package_0 = [
            'package_id' => 1,
            'weight' => 0,
            'unit_weight' => 'kgs',
            'length' => 0,
            'width' => 0,
            'height' => 0,
            'unit_dimension' => 'cm'
        ];
        $packages[] = $package_0;
        $listOrder = json_decode($this->getRequest()->getParam('idorder'));
        $listDetailOrder = $this->modelOrder->getMultiDetailOrder($listOrder);
        $listError = [];
        $listErrorHTML = [];
        if (!empty($listDetailOrder)) {
            foreach ($listDetailOrder as $value) {
                if (!empty($value['package'])) {
                    $packages = json_decode($value['package'], true);
                }
                $dataRequestShip = [];
                $order = $this->getOrderData($value);
                $getBaseAmountOrdered = (string)number_format($order->getPayment()->getBaseAmountOrdered(), 2);
                $orderValue = preg_replace('([^a-zA-Z0-9.])', '', $getBaseAmountOrdered);
                $dataRequestShip = $this->getDataRequestShip($value, $orderValue, $shipFrom, $order);
                if ($value[\UPS\Shipping\Helper\ConstantShipment::SERVICE_TYPE] == 'AP') {
                    if (empty($dataRequestShip['ShipTo']['Phone']['Number'])) {
                        $dataRequestShip['ShipTo']['Phone']['Number'] = '0000000000';
                    }
                    $stateProvinceCodeEU = $value['ap_state'];
                    $countryCodeValue = $order->getShippingAddress()->getCountryId();
                    // US and Canada, UPSEUPUAT-911
                    if (!in_array($countryCodeValue, ['US', 'CA', 'IE'])) {
                        $stateProvinceCodeEU = 'XX';
                    }
                    $dataRequestShip["AlternateDeliveryAddress"] = [
                        "Name" => $value[\UPS\Shipping\Helper\ConstantShipment::AP_NAME],
                        \UPS\Shipping\Helper\ConstantShipment::ATTENTIONNAME
                        => $value[\UPS\Shipping\Helper\ConstantShipment::AP_NAME],
                        \UPS\Shipping\Helper\ConstantShipment::ADDRESS => [
                            \UPS\Shipping\Helper\ConstantShipment::ADDRESSLINE
                            => str_replace('&#xD;', ' ', $value[\UPS\Shipping\Helper\ConstantShipment::AP_ADDRESS1]),
                            "City" => $value[\UPS\Shipping\Helper\ConstantShipment::AP_CITY],
                            \UPS\Shipping\Helper\ConstantShipment::STATEPROVINCECODE => $stateProvinceCodeEU,
                            \UPS\Shipping\Helper\ConstantShipment::POSTALCODE
                            => implode("", explode("-", $value[\UPS\Shipping\Helper\ConstantShipment::AP_POSTCODE])),
                            \UPS\Shipping\Helper\ConstantShipment::COUNTRYCODE
                            => $countryCodeValue
                        ]
                    ];
                }
                if (!empty($packages)) {
                    foreach ($packages as $package) {
                        $dataRequestShip["Package"][] = [
                            "Dimensions" => [
                                "UnitOfMeasurement" => [
                                    "Code" => $package['unit_dimension'],
                                    \UPS\Shipping\Helper\ConstantShipment::DESCRIPTION => ('IN' == strtoupper($package['unit_dimension'])) ? "inches" : "centimeter"
                                ],
                                "Length" => strval($package['length']),
                                "Width" => strval($package['width']),
                                "Height" => strval($package['height'])
                            ],
                            "PackageWeight" => [
                                "UnitOfMeasurement" => [
                                    "Code" => $package['unit_weight'],
                                    \UPS\Shipping\Helper\ConstantShipment::DESCRIPTION => ('LBS' == strtoupper($package['unit_weight'])) ? "Pounds" : "kilograms"
                                ],
                                "Weight" => strval($package['weight'])
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
                $accessorial = [];
                // get request accessorial
                $this->requestAccessorial($value, $accessorial, $dataRequestShip);

                // get request  cod
                $this->requestCod($order, $value, $dataRequestShip);

                $messege = "";
                // US and Canada, UPSEUPUAT-911
                if (in_array($dataRequestShip['ShipTo'][\UPS\Shipping\Helper\ConstantShipment::ADDRESS][\UPS\Shipping\Helper\ConstantShipment::COUNTRYCODE], ['US', 'CA', 'IE'])) {
                    $regionId = $dataRequestShip['ShipTo'][\UPS\Shipping\Helper\ConstantShipment::ADDRESS][\UPS\Shipping\Helper\ConstantShipment::STATEPROVINCECODE];
                    $region = $this->regionModel->create()->load($regionId);
                    $arrRegion = $region->getData();
                    $dataRequestShip['ShipTo'][\UPS\Shipping\Helper\ConstantShipment::ADDRESS][\UPS\Shipping\Helper\ConstantShipment::STATEPROVINCECODE] = $arrRegion['code'];
                } else {
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
                    // check tracking number
                    $this->checkTrackingNumber($dataResponseAPI, $trackingNumber);
                    $date = $this->dateTime->gmtDate('Y-m-d H:i:s');
                    $acces = $value[\UPS\Shipping\Helper\ConstantShipment::ACCESSORIAL_SERVICE];
                    // check cod
                    $this->checkCod($order, $cod);
                    if ($value[\UPS\Shipping\Helper\ConstantShipment::SERVICE_TYPE] == 'AP') {
                        $orderId = $value['id'];
                        $shipment_id_magento = 0;
                        $stateName = $this->regionModel->create()->loadByCode($value['ap_state'], $order->getShippingAddress()->getCountryId())->getName();
                        $idShipment = $this->modelShipment->createShipment($shipmentNumber, $value['idservice'], $acces, $date, $shippingFee, $cod, $value['ap_id'], $value[\UPS\Shipping\Helper\ConstantShipment::AP_NAME], $stateName, $value['telephone'], $value[\UPS\Shipping\Helper\ConstantShipment::AP_ADDRESS1], $value['ap_address2'], $value['ap_address3'], $value[\UPS\Shipping\Helper\ConstantShipment::AP_CITY], $value[\UPS\Shipping\Helper\ConstantShipment::AP_POSTCODE], $order->getShippingAddress()->getCountryId(), $value['email'], number_format($value['grand_total'], 2), $shipment_id_magento);
                        $addresss = [$value[\UPS\Shipping\Helper\ConstantShipment::AP_ADDRESS1],
                        $value['ap_address2'], $value['ap_address3']];
                        $addressManager = implode(', ', $addresss);
                        $postCodeManager = $value[\UPS\Shipping\Helper\ConstantShipment::AP_POSTCODE];
                        $cityManager = $value[\UPS\Shipping\Helper\ConstantShipment::AP_CITY];
                        $countryManager = $order->getShippingAddress()->getCountryId();
                    } else {
                        $apId = '';
                        $orderId = $value['id'];
                        $shipment_id_magento = 0;
                        $addName = $value[\UPS\Shipping\Helper\ConstantShipment::FIRSTNAME] . ' '
                        . $value[\UPS\Shipping\Helper\ConstantShipment::LASTNAME];

                        $postCodeManager = $order->getShippingAddress()->getPostcode();
                        $cityManager = $order->getShippingAddress()->getCity();
                        $countryManager = $order->getShippingAddress()->getCountryId();
                        $getStreetLine1 = $order->getShippingAddress()->getStreetLine(1);
                        $getStreetLine2 = $order->getShippingAddress()->getStreetLine(2);
                        $getStreetLine3 = $order->getShippingAddress()->getStreetLine(3);
                        $idShipment = $this->modelShipment->createShipment($shipmentNumber, $value['idservice'], $acces, $date, $shippingFee, $cod, $apId, $addName, $order->getShippingAddress()->getRegion(), $value['telephone'], $getStreetLine1, $getStreetLine2, $getStreetLine3, $cityManager, $postCodeManager, $countryManager, $value['email'], number_format($value['grand_total'], 2), $shipment_id_magento);
                        $addresss = [$getStreetLine1, $getStreetLine2, $getStreetLine3];
                        $addressManager = implode(', ', $addresss);
                    }
                    $this->saveOrderData($value);
                    $this->modelOrder->updateStatusOrder($value['id'], $idShipment);
                    $products = [];
                    $this->productDetail($order, $products);
                    $packageShipment = [];
                    if (!empty($packages[0]['length'])) {
                        foreach ($packages as $key => $package) {
                            // check unit package
                            $this->checkPackage($package, $unit_dimension, $unit_weight);
                            $package['trackingnumber'] = $trackingNumber;
                            if (!empty($dataResponseAPI)
                                && !empty($dataResponseAPI->PackageResults)
                            ) {
                                if (!empty($dataResponseAPI->PackageResults->TrackingNumber)) {
                                    $package['trackingnumber'] =  $dataResponseAPI->PackageResults->TrackingNumber;
                                    $trackingNumber =  $dataResponseAPI->PackageResults->TrackingNumber;
                                } else {
                                    $package['trackingnumber'] =  $dataResponseAPI->PackageResults[$key]->TrackingNumber;
                                    $trackingNumber =  $dataResponseAPI->PackageResults[$key]->TrackingNumber;
                                }
                            }
                            $detailPackage = $package['length'] . 'x' . $package['width'] . 'x' . $package['height'] . ' '
                            . $unit_dimension . ', ' . $package['weight'] . $unit_weight;
                            $this->modelShipment->createTracking($shipmentNumber, $trackingNumber, $detailPackage, $value['id']);
                            $packageShipment[] = $package;
                        }
                    }
                    $dataShipmentManager = [];
                    $merchantKey = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY);
                    //offPluginManager 2019-03-18
                    $this->runTransferShipments($merchantKey, $shipFrom, $shipmentNumber, $shippingFee, $orderValue, $addressManager, $postCodeManager, $cityManager, $countryManager, $value, $cod, $products, $accessorial, $packageShipment);
                    $listError[] = [$value['increment_id'], $value['id'], 'true'];
                } else {
                    $messageError = '';
                    if (!empty($dataresponseShip->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description)) {
                        $messageError = $dataresponseShip->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description;
                    }
                    $listError[] = [$value['increment_id'], $value['id'], 'false', $messageError];
                }
            }
        }
        return $result->setData(['listOrderFalse' => $listError]);
    }

    /**
     * Bathshipment runTransferShipments
     *
     * @param string $merchantKey     //The merchantKey
     * @param string $shipFrom        //The shipFrom
     * @param string $shipmentNumber  //The shipmentNumber
     * @param string $shippingFee     //The shippingFee
     * @param string $orderValue      //The orderValue
     * @param string $addressManager  //The addressManager
     * @param string $postCodeManager //The postCodeManager
     * @param string $cityManager     //The cityManager
     * @param string $countryManager  //The countryManager
     * @param string $value           //The value
     * @param string $cod             //The cod
     * @param string $products        //The products
     * @param string $accessorial     //The accessorial
     * @param string $package         //The package
     *
     * @return null
     */
    public function runTransferShipments($merchantKey, $shipFrom, $shipmentNumber, $shippingFee, $orderValue, $addressManager, $postCodeManager, $cityManager, $countryManager, $value, $cod, $products, $accessorial, $package)
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
                'revenue'           => number_format($orderValue, 2),
                'address'           => str_replace('&#xD;', ' ', $addressManager),
                'postalCode'        => $this->getPostalCode($postCodeManager),
                'city'              => $cityManager,
                'country'           => $countryManager,
                'serviceType'       => $value[\UPS\Shipping\Helper\ConstantShipment::SERVICE_TYPE],
                'serviceCode'       => $value['rate_code'],
                'serviceName'       => $value['service_name'],
                'isCashOnDelivery'  => $cod,
                'products'          => $products
            ];
            $dataShipmentManager['accessorials'] = $accessorial;
            $dataShipmentManager['packages'] = $package;
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
     * Bathshipment productDetail
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
     * Bathshipment countCheckParent
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
     * Bathshipment saveOrderData
     *
     * @param string $value //The value
     *
     * @return null
     */
    public function saveOrderData($value)
    {
        $this->salesOrder->load($value['order_id_magento'])->setStatus('shipped', true)->save();
    }

    /**
     * Bathshipment getOrderData
     *
     * @param string $archivedOrder //The archivedOrder
     *
     * @return array $Order
     */
    public function getOrderData($archivedOrder)
    {
        return $this->salesOrder->load($archivedOrder['order_id_magento']);
    }

    /**
     * Bathshipment checkTrackingNumber
     *
     * @param string $dataResponseAPI //The dataResponseAPI
     * @param string $trackingNumber  //The trackingNumber
     *
     * @return null
     */
    public function checkTrackingNumber($dataResponseAPI, &$trackingNumber)
    {
        if (isset($dataResponseAPI->PackageResults->TrackingNumber)) {
            $trackingNumber = $dataResponseAPI->PackageResults->TrackingNumber;
        } else {
            $trackingNumber = $dataResponseAPI->PackageResults[0]->TrackingNumber;
        }
    }

    /**
     * Bathshipment checkCod
     *
     * @param string $order //The order
     * @param string $cod   //The cod
     *
     * @return null
     */
    public function checkCod($order, &$cod)
    {
        if ($order->getPayment()->getMethod() == 'cashondelivery') {
            $cod = '1';
        } else {
            $cod = '0';
        }
    }

    /**
     * Bathshipment checkPackage
     *
     * @param string $package        //The package
     * @param string $unit_dimension //The unit_dimension
     * @param string $unit_weight    //The unit_weight
     *
     * @return null
     */
    public function checkPackage($package, &$unit_dimension, &$unit_weight)
    {
        if ($package['unit_dimension'] == 'cm') {
            $unit_dimension = 'cm';
        } else {
            $unit_dimension = 'inch';
        }
        if ($package['unit_weight'] == 'kgs') {
            $unit_weight = 'Kg';
        } else {
            $unit_weight = 'Pounds';
        }
    }

    /**
     * Bathshipment requestAccessorial
     *
     * @param string $value           //The value
     * @param string $accessorial     //The accessorial
     * @param string $dataRequestShip //The value
     *
     * @return null
     */
    public function requestAccessorial($value, &$accessorial, &$dataRequestShip)
    {
        if (isset($value[\UPS\Shipping\Helper\ConstantShipment::ACCESSORIAL_SERVICE])
            && !empty($value[\UPS\Shipping\Helper\ConstantShipment::ACCESSORIAL_SERVICE])) {
            $accessorial = json_decode($value[\UPS\Shipping\Helper\ConstantShipment::ACCESSORIAL_SERVICE]);
            foreach ($accessorial as $key => $value1) {
                $dataRequestShip[\UPS\Shipping\Helper\ConstantShipment::ACCESSORIALS][$key] = [];
            }
        }
    }

    /**
     * Bathshipment requestCod
     *
     * @param string $order           //The order
     * @param string $value           //The value
     * @param string $dataRequestShip //The dataRequestShip
     *
     * @return string $dataRequestShip
     */
    public function requestCod($order, $value, &$dataRequestShip)
    {
        if ($order->getPayment()->getMethod() == 'cashondelivery') {
            // COD for Ship
            $dataRequestShip["AlternateDeliveryAddress"]['COD'] = '1';
            if ($value[\UPS\Shipping\Helper\ConstantShipment::SERVICE_TYPE] == 'AP') {
                $dataRequestShip[\UPS\Shipping\Helper\ConstantShipment::ACCESSORIALS]
                ["UPS_ACSRL_ACCESS_POINT_COD"] = [];
            } else {
                $dataRequestShip[\UPS\Shipping\Helper\ConstantShipment::ACCESSORIALS]
                ["UPS_ACSRL_TO_HOME_COD"] = [];
            }
        }
    }

    /**
     * Bathshipment getDataRequestShip
     *
     * @param string $value      //The value
     * @param string $orderValue //The orderValue
     * @param string $shipFrom   //The shipFrom
     * @param string $order      //The order
     *
     * @return string $dataRequestShip
     */
    public function getDataRequestShip($value, $orderValue, $shipFrom, $order)
    {
        $dataRequestShip = [
            "ShippingType" => $value[\UPS\Shipping\Helper\ConstantShipment::SERVICE_TYPE],
            "CurrencyCode" => $order->getOrderCurrencyCode(),
            "MonetaryValue" => preg_replace('([^a-zA-Z0-9.])', '', (string)number_format($orderValue, 2)),
            "Shipper" => [
                "Name" => $shipFrom[0],
                \UPS\Shipping\Helper\ConstantShipment::ATTENTIONNAME => $shipFrom[1],
                "ShipperNumber" => $shipFrom[2],
                \UPS\Shipping\Helper\ConstantShipment::PHONE => [
                    \UPS\Shipping\Helper\ConstantShipment::NUMBER => (isset($shipFrom[3]) ? $shipFrom[3] : '')
                ],
                \UPS\Shipping\Helper\ConstantShipment::ADDRESS => [
                    \UPS\Shipping\Helper\ConstantShipment::ADDRESSLINE => [
                        (isset($shipFrom[4]) ? $shipFrom[4] : ''),
                        (isset($shipFrom[5]) ? $shipFrom[5] : ''),
                        (isset($shipFrom[6]) ? $shipFrom[6] : '')
                    ],
                    "City" => (isset($shipFrom[7]) ? $shipFrom[7] : ''),
                    \UPS\Shipping\Helper\ConstantShipment::STATEPROVINCECODE => (isset($shipFrom[10]) && !empty($shipFrom[10])) ? $shipFrom[10] : "XX",
                    \UPS\Shipping\Helper\ConstantShipment::POSTALCODE
                    => $this->getPostalCode($shipFrom[8]),
                    \UPS\Shipping\Helper\ConstantShipment::COUNTRYCODE => $shipFrom[9]
                ]
            ],
            "ShipTo" => [
                "Name" => $value[\UPS\Shipping\Helper\ConstantShipment::FIRSTNAME]
                . ' ' . $value[\UPS\Shipping\Helper\ConstantShipment::LASTNAME],
                \UPS\Shipping\Helper\ConstantShipment::ATTENTIONNAME
                => $value[\UPS\Shipping\Helper\ConstantShipment::FIRSTNAME]
                . ' ' . $value[\UPS\Shipping\Helper\ConstantShipment::LASTNAME],
                \UPS\Shipping\Helper\ConstantShipment::PHONE => [
                    "Number" => $order->getShippingAddress()->getTelephone(),
                ],
                \UPS\Shipping\Helper\ConstantShipment::ADDRESS => [
                    \UPS\Shipping\Helper\ConstantShipment::ADDRESSLINE => [
                        $order->getShippingAddress()->getStreetLine(1),
                        $order->getShippingAddress()->getStreetLine(2),
                        $order->getShippingAddress()->getStreetLine(3)
                    ],
                    "City" => $order->getShippingAddress()->getCity(),
                    \UPS\Shipping\Helper\ConstantShipment::STATEPROVINCECODE
                    => $order->getShippingAddress()->getRegionId(),
                    \UPS\Shipping\Helper\ConstantShipment::POSTALCODE
                    => $this->getPostalCode($order->getShippingAddress()->getPostcode()),
                    \UPS\Shipping\Helper\ConstantShipment::COUNTRYCODE
                    => $order->getShippingAddress()->getCountryId(),
                ],
                "Email" => $order->getShippingAddress()->getEmail(),
            ],
            "ShipFrom" => [
                "Name" => $shipFrom[0],
                \UPS\Shipping\Helper\ConstantShipment::ATTENTIONNAME => $shipFrom[1],
                "ShipperNumber" => $shipFrom[2],
                'Phone' => [
                    \UPS\Shipping\Helper\ConstantShipment::NUMBER
                    => (isset($shipFrom[3]) ? $shipFrom[3] : '')
                ],
                \UPS\Shipping\Helper\ConstantShipment::ADDRESS => [
                    \UPS\Shipping\Helper\ConstantShipment::ADDRESSLINE => [
                        (isset($shipFrom[4]) ? $shipFrom[4] : ''),
                        (isset($shipFrom[5]) ? $shipFrom[5] : ''),
                        (isset($shipFrom[6]) ? $shipFrom[6] : '')
                    ],
                    "City" => (isset($shipFrom[7]) ? $shipFrom[7] : ''),
                    \UPS\Shipping\Helper\ConstantShipment::STATEPROVINCECODE => (isset($shipFrom[10]) && !empty($shipFrom[10])) ? $shipFrom[10] : "XX",
                    \UPS\Shipping\Helper\ConstantShipment::POSTALCODE
                    => $this->getPostalCode($shipFrom[8]),
                    \UPS\Shipping\Helper\ConstantShipment::COUNTRYCODE => $shipFrom[9]
                ]
            ],
            'InvoiceLineTotal' => [
                "CurrencyCode" => $order->getOrderCurrencyCode(),
                "MonetaryValue" => preg_replace('([^a-zA-Z0-9.])', '', (string)number_format($orderValue, 2)),
            ],
            "Service" => [
                "Code" => $value['rate_code'],
                \UPS\Shipping\Helper\ConstantShipment::DESCRIPTION => $value['service_name']
            ],
            "PaymentInformation" => [
                "ShipmentCharge" => [
                    "Type" => "01",
                    "BillShipper" => [
                        "AccountNumber" => $shipFrom[2]// number account
                    ]
                ]
            ],
            "ShipmentRatingOptions" => [
                "NegotiatedRatesIndicator" => ""
            ],
        ];
        return $dataRequestShip;
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
     * Bathshipment resetRegisteredToken
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
