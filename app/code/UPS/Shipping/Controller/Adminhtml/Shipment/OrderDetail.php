<?php
/**
 * OrderDetail file
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
 * OrderDetail class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class OrderDetail extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $modelOrder;
    protected $salesOrder;
    protected $timezone;
    protected $countryModel;
    protected $serviceModel;
    protected $modelCurrency;
    protected $regionModel;
    protected $request;
    protected $dateTime;
    /**
     * OrderDetail constructor
     *
     * @param string   $context           //The Context
     * @param string   $resultJsonFactory //The resultJsonFactory
     * @param string   $modelCurrency     //The export Data
     * @param string   $modelOrder        //The model Order
     * @param string   $salesModel        //The sales Model
     * @param string   $timezone          //The time zone
     * @param string   $countryModel      //The country Model
     * @param string   $regionModel       //The region Model
     * @param string   $request           //The request
     * @param DateTime $dateTime          //The datetime
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Directory\Model\Currency $modelCurrency,
        \UPS\Shipping\Model\Order $modelOrder,
        \Magento\Sales\Model\Order $salesModel,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Directory\Model\Country $countryModel,
        \Magento\Directory\Model\RegionFactory $regionModel,
        \Magento\Framework\App\Request\Http $request,
        DateTime $dateTime
    ) {
        $this->countryModel = $countryModel;
        $this->timezone = $timezone;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->modelOrder = $modelOrder;
        $this->salesOrder = $salesModel;
        $this->modelCurrency = $modelCurrency;
        $this->regionModel = $regionModel;
        $this->request = $request;
        $this->dateTime = $dateTime;
        parent::__construct($context);
    }
    /**
     * OrderDetail execute
     *
     * @return null
     */
    public function execute()
    {
        $region = $this->regionModel->create();
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $returnData = [];
            switch ($this->getRequest()->getParam('method')) {
            case 'getDetailOrder':
                $returnData = $this->runDetailOrder($result, $region);
                break;
            default:
                // nothing
                break;
            }
            return $returnData;
        } else {
            return $result->setData(['result' => $result]);
        }
    }

    /**
     * OrderDetail runDetailOrder
     *
     * @param string $result //The result
     * @param string $region //The region
     *
     * @return array $Order
     */
    public function runDetailOrder($result, $region)
    {
        if ($this->request->isPost()) {
            $id = $this->getRequest()->getParam('id');
            if (!is_numeric($id)) {
                $listId = json_decode($id);
                $detailOrder = $this->modelOrder->getMultiDetailOrder($listId);
                $orderValue = 0;
                foreach ($detailOrder as $key => $value) {
                    $order = $this->getOrderData($value);
                    $productName = [];
                    $this->getProductName($order, $productName);
                    // handling service name
                    $this->getServiceName($detailOrder, $key);
                    $detailOrder[$key]['productName']
                        = implode(\UPS\Shipping\Helper\ConstantOrder::STYLE_BR, $productName);

                    $getStreetLine1 = $order->getShippingAddress()->getStreetLine(1);
                    $getStreetLine2 = $order->getShippingAddress()->getStreetLine(2);
                    $getStreetLine3 = $order->getShippingAddress()->getStreetLine(3);
                    $getCreatedAt = $this->dateTime->timestamp($order->getCreatedAt());
                    $createDateTime = strtotime($this->timezone->date($getCreatedAt)->format('m/d/y H:i:s'));
                    $detailOrder[$key]['created_at'] = date('M d, Y, H:i:s', $createDateTime);
                    $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::GRAND_TOTAL]
                        = number_format($detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::GRAND_TOTAL], 2);
                    $baseAmount = $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::BASE_SHIPPING_AMOUNT];
                    $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::BASE_SHIPPING_AMOUNT]
                        = number_format($baseAmount, 2);
                    $detailOrder[$key]['postcode'] = $order->getShippingAddress()->getPostcode();
                    $getCTId = $order->getShippingAddress()->getCountryId();
                    $detailOrder[$key]['CountryAPorADD']=$this->countryModel->loadByCode($getCTId)->getName();
                    $detailOrder[$key]['CountryCode'] = $order->getShippingAddress()->getCountryId();
                    $detailOrder[$key]['ADDAdress1'] = $getStreetLine1;
                    $detailOrder[$key]['ADDAdress2'] = $order->getShippingAddress()->getStreetLine(2);
                    $detailOrder[$key]['ADDAdress3'] = $order->getShippingAddress()->getStreetLine(3);

                    $apAddresstring1 = $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS1];
                    $apAddresstring2 = $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS2];
                    $apAddresstring3 = $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS3];
                    $apAdressAll = $this->addArray([$apAddresstring1, $apAddresstring2, $apAddresstring3]);
                    $detailOrder[$key]['APAdressAll'] = implode(", ", $apAdressAll);
                    $this->getDetailAddress1($detailOrder, $key, $order, $region);
                    $this->getDetailAddress2($detailOrder, $key);
                    $this->getDetailAddress3($detailOrder, $key);
                    $orderValue = $orderValue + $order->getGrandTotal();

                    $addAdressAll = $this->addArray([$getStreetLine1, $getStreetLine2, $getStreetLine3]);
                    $detailOrder[$key]['ADDAdressAll'] = implode(", ", $addAdressAll);
                    $this->translateAccess($detailOrder, $key);
                }
                $orderValueString = (string)number_format($orderValue, 2);
                $detailOrder['orderValue'] =  preg_replace('([^a-zA-Z0-9.])', '', $orderValueString);
            } else {
                $detailOrder = $this->modelOrder->getDetailOrder($id);
                $order = $this->salesOrder->load($detailOrder['order_id_magento']);
                //handling currency symbol
                $currencyCode = $order->getOrderCurrencyCode();
                $currencySymbol = $this->modelCurrency->load($currencyCode)->getCurrencySymbol();
                $this->setCurrencySymbol($currencySymbol, $currencyCode);
                $detailOrder['currency_symbol'] = $currencySymbol;
                $productName = [];
                $this->getProductName($order, $productName);
                $detailOrder['productName']
                    = implode(\UPS\Shipping\Helper\ConstantOrder::STYLE_BR, $productName);
                $serviceNameString = $detailOrder['service_name'];
                // handling service name
                $this->setServiceName($detailOrder, $serviceNameString);
                $timeNew = $this->dateTime->timestamp($order->getCreatedAt());
                $timeAp = strtotime($this->timezone->date($timeNew)->format('m/d/y H:i:s'));
                $detailOrder['created_at'] = date('M d, Y, H:i:s', $timeAp);
                $detailOrder[\UPS\Shipping\Helper\ConstantOrder::GRAND_TOTAL]
                    = number_format($detailOrder[\UPS\Shipping\Helper\ConstantOrder::GRAND_TOTAL], 2);
                $detailOrder[\UPS\Shipping\Helper\ConstantOrder::BASE_SHIPPING_AMOUNT]
                    = number_format($detailOrder[\UPS\Shipping\Helper\ConstantOrder::BASE_SHIPPING_AMOUNT], 2);
                $detailOrder['PaymentStatus'] = $order->getStatus();
                $addressFormat1 = [
                    $order->getShippingAddress()->getStreetLine(1),
                    $order->getShippingAddress()->getStreetLine(2),
                    $order->getShippingAddress()->getStreetLine(3),
                    $order->getShippingAddress()->getCity(),
                    $order->getShippingAddress()->getRegion(),
                    $order->getShippingAddress()->getPostcode()
                ];
                $addressFormat = $this->addArray($addressFormat1);
                $getCountryId = $order->getShippingAddress()->getCountryId();
                $this->setAddressFormat($getCountryId, $addressFormat);
                $detailOrder['address'] = implode('<br/>', $addressFormat);
                $addressFormat_AP = [];
                if ($this->checkData($detailOrder['ap_name']) != "") {
                    $addressFormat_AP[] = $detailOrder['ap_name'];
                }
                $ap_ADDRESS1 = $detailOrder[\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS1];
                $ap_ADDRESS2 = $detailOrder[\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS2];
                $ap_ADDRESS3 = $detailOrder[\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS3];
                $apAdressAll = $this->addArray([$ap_ADDRESS1, $ap_ADDRESS2, $ap_ADDRESS3]);
                $detailOrder['APAdressAll'] = implode(", ", $apAdressAll);
                $getCountryId = $order->getShippingAddress()->getCountryId();
                $this->setAddressAP($addressFormat_AP, $ap_ADDRESS1, $ap_ADDRESS2, $ap_ADDRESS3, $detailOrder, $order, $region);
                $stringStyleBR = \UPS\Shipping\Helper\ConstantOrder::STYLE_BR;
                $getGrandTotalString = (string)number_format($order->getGrandTotal(), 2);
                $detailOrderStr1 = $order->getShippingAddress()->getStreetLine(1);
                $detailOrderStr2 = $order->getShippingAddress()->getStreetLine(2);
                $detailOrderStr3 = $order->getShippingAddress()->getStreetLine(3);
                $detailOrder['address_ap'] = implode($stringStyleBR, $addressFormat_AP);
                $detailOrder['postcode'] = $order->getShippingAddress()->getPostcode();
                $detailOrder['CountryAPorADD'] = $this->countryModel->loadByCode($getCountryId)->getName();
                $detailOrder['CountryCode'] = $getCountryId;
                $detailOrder['orderValue'] = preg_replace('([^a-zA-Z0-9.])', '', $getGrandTotalString);
                $detailOrder['orderValueFM'] = number_format($order->getGrandTotal(), 2);
                $detailOrder['ADDAdress1'] = $detailOrderStr1;
                $detailOrder['ADDAdress2'] = $detailOrderStr2;
                $detailOrder['ADDAdress3'] = $detailOrderStr3;

                $addAdressAll = $this->addArray([$detailOrderStr1, $detailOrderStr2, $detailOrderStr3]);

                $detailOrder['ADDAdressAll'] = implode(", ", $addAdressAll);

                $openOrder = [
                    0 => [
                        'package_id' => 1,
                        'weight' => 0,
                        'unit_weight' => 'kgs',
                        'length' => 0,
                        'width' => 0,
                        'height' => 0,
                        'unit_dimension' => 'cm'
                    ]
                ];
                if (!empty($detailOrder)) {
                    if (!empty($detailOrder['package'])) {
                        $openOrder = json_decode($detailOrder['package'], true);
                    }
                }
                $detailOrder['package'] = $openOrder;

                $this->setDetailOrderFinal($detailOrder, $ap_ADDRESS1, $order, $getCountryId, $region);
            }
            return $result->setData(['result' => $detailOrder]);
        } else {
            http_response_code(404);
        }
    }

    /**
     * OrderDetail getOrderData
     *
     * @param string $value //The value
     *
     * @return array $Order
     */
    public function getOrderData($value)
    {
        return $this->salesOrder->load($value['order_id_magento']);
    }

    /**
     * OrderDetail setDetailOrderFinal
     *
     * @param string $detailOrder  //The addressFormat_AP
     * @param string $ap_ADDRESS1  //The ap_ADDRESS1
     * @param string $order        //The order
     * @param string $getCountryId //The getCountryId
     * @param string $region       //The region
     *
     * @return string
     */
    function setDetailOrderFinal(&$detailOrder, $ap_ADDRESS1, $order, $getCountryId, $region)
    {
        if ($ap_ADDRESS1 == null || $ap_ADDRESS1 == '') {
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::STATE]
                = $order->getShippingAddress()->getRegionCode();
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::STATENAME]
                = $order->getShippingAddress()->getRegion();
        } else {
            if (isset($detailOrder[\UPS\Shipping\Helper\ConstantOrder::STATE])) {
                $constantOrderState = $detailOrder[\UPS\Shipping\Helper\ConstantOrder::STATE];
            } else {
                $constantOrderState = $detailOrder['ap_state'];
            }
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::STATE] = $constantOrderState;
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::STATENAME]
                = $region->loadByCode($constantOrderState, $getCountryId)->getName();
        }
            $accessorial_service_translate = [];
        if (isset($detailOrder[\UPS\Shipping\Helper\ConstantOrder::ACCESSORIAL_SERVICE])
            && !empty($detailOrder[\UPS\Shipping\Helper\ConstantOrder::ACCESSORIAL_SERVICE])
        ) {
            $accessorialService = $detailOrder[\UPS\Shipping\Helper\ConstantOrder::ACCESSORIAL_SERVICE];
            $accessorialArray = json_decode($accessorialService, true);
            foreach ($accessorialArray as $key1 => $value) {
                $accessorial_service_translate[$key1] = __($value);
            }
        }
        $detailOrder['accessorial_service_translate'] = $accessorial_service_translate;
    }

    /**
     * OrderDetail setCurrencySymbol
     *
     * @param array  $addressFormat_AP //The addressFormat_AP
     * @param string $ap_ADDRESS1      //The ap_ADDRESS1
     * @param string $ap_ADDRESS2      //The ap_ADDRESS2
     * @param string $ap_ADDRESS3      //The ap_ADDRESS3
     * @param string $detailOrder      //The detailOrder
     *
     * @return string
     */
    public function setAddressAP(&$addressFormat_AP, $ap_ADDRESS1, $ap_ADDRESS2, $ap_ADDRESS3, $detailOrder, $order, $region)
    {
        if ($ap_ADDRESS1 != null) {
            $apADDRESS1 = htmlentities($ap_ADDRESS1);
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS1]
                = str_replace(\UPS\Shipping\Helper\ConstantOrder::REPLACE, '', $apADDRESS1);
            $addressFormat_AP[] = $this->getAPAddressString($ap_ADDRESS1);
        } else {
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS1] = '';
        }

        if ($ap_ADDRESS2 != null) {
            $apADDRESS2 = htmlentities($ap_ADDRESS2);
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS2]
                = str_replace(\UPS\Shipping\Helper\ConstantOrder::REPLACE, '', $apADDRESS2);
            $addressFormat_AP[] = $this->getAPAddressString($ap_ADDRESS2);
        } else {
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS2] = '';
        }

        if ($ap_ADDRESS3 != null) {
            $apADDRESS3 = htmlentities($ap_ADDRESS3);
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS3]
                = str_replace(\UPS\Shipping\Helper\ConstantOrder::REPLACE, '', $apADDRESS3);
            $addressFormat_AP[] = $this->getAPAddressString($ap_ADDRESS3);
        } else {
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS3] = '';
        }
        $addressFormat_AP[] = $this->getAPAddressString($detailOrder['ap_city']);

        if (isset($detailOrder[\UPS\Shipping\Helper\ConstantOrder::STATE])) {
            $constantOrderState = $detailOrder[\UPS\Shipping\Helper\ConstantOrder::STATE];
        } else {
            $constantOrderState = $detailOrder['ap_state'];
        }
        $getCountryId = $order->getShippingAddress()->getCountryId();
        $detailOrder_string = $region->loadByCode($constantOrderState, $getCountryId)->getName();
        $stringStatePostCode = $this->getAPAddressString($detailOrder['ap_postcode']);
        if (!empty($detailOrder_string)) {
            $stringStatePostCode = $detailOrder_string . ', ' . $stringStatePostCode;
        }
        $addressFormat_AP[] = $stringStatePostCode;
        if ($detailOrder['service_type'] == 'AP') {
            $nameCountryString = $detailOrder[\UPS\Shipping\Helper\ConstantOrder::NAMECOUNTRY];
            if ($nameCountryString != null && $nameCountryString != "") {
                $addressFormat_AP[] = $this->countryModel->loadByCode($nameCountryString)->getName();
            };
        }
    }

    /**
     * OrderDetail setCurrencySymbol
     *
     * @param string $currencySymbol //The currencySymbol
     * @param string $currencyCode   //The currencyCode
     *
     * @return string
     */
    public function setCurrencySymbol(&$currencySymbol, $currencyCode)
    {
        if (empty($currencySymbol)) {
            $currencySymbol = $currencyCode;
        }
    }

    /**
     * OrderDetail setAddressFormat
     *
     * @param string $getCountryId  //The getCountryId
     * @param array  $addressFormat //The addressFormat
     *
     * @return string
     */
    public function setAddressFormat($getCountryId, &$addressFormat)
    {
        if ($getCountryId != '' && $getCountryId != null) {
            $addressFormat[] = $this->countryModel->loadByCode($getCountryId)->getName();
        } else {
            $addressFormat[] = null;
        }
    }

    /**
     * OrderDetail setServiceName
     *
     * @param string $detailOrder       //The detailOrder
     * @param string $serviceNameString //The serviceNameString
     *
     * @return string
     */
    public function setServiceName(&$detailOrder, $serviceNameString)
    {
        if ($detailOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] == '&trade;') {
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO] = 'UPS Access Point&trade; Economy';
        } elseif ($serviceNameString == 'UPS Standard') {
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO]
                = 'UPS'. $detailOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' Standard';
        } elseif ($serviceNameString == 'UPS Express 12:00') {
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO] = 'UPS Express 12:00';
        } elseif ($serviceNameString == 'UPS Ground') {
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO]
                = 'UPS'. $detailOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' Ground';
        } elseif ($serviceNameString == 'UPS Next Day Air Early') {
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO]
                = 'UPS Next Day Air'. $detailOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' Early';
        } elseif ($serviceNameString == 'UPS Standard - Saturday Delivery') {
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO]
                = 'UPS' . $detailOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' Standard - Saturday Delivery';
        } elseif ($serviceNameString == 'UPS Express - Saturday Delivery') {
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO]
                = 'UPS Express' . $detailOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] .' - Saturday Delivery';
        } else {
            $detailOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO]
                = $serviceNameString . $detailOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL];
        }
    }

    /**
     * OrderDetail getAPAddress
     *
     * @param string $ap_ADDRESS1 //The ap_ADDRESS1
     *
     * @return string
     */
    public function getAPAddressString($ap_ADDRESS1)
    {
        $returnData = '';
        if ($this->checkData($ap_ADDRESS1) != "") {
            $returnData = $ap_ADDRESS1;
        }
        return $returnData;
    }

    /**
     * OrderDetail getDetailAddress1
     *
     * @param string $detailOrder //The detail Order
     * @param string $key         //The key
     * @param string $order       //The order
     * @param string $region      //The region
     *
     * @return null
     */
    public function getDetailAddress1(&$detailOrder, $key, $order, $region)
    {
        $apADDRESS1 = htmlentities($detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS1]);
        if ($detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS1] != null) {
            $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS1]
                = str_replace(\UPS\Shipping\Helper\ConstantOrder::REPLACE, '', $apADDRESS1);
        } else {
            $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS1] = '';
        }

        if ($detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS1] == null
            || $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS1] == ''
        ) {
            $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::STATE]
                = $order->getShippingAddress()->getRegionCode();
            $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::STATENAME]
                = $order->getShippingAddress()->getRegion();
        } else {
            if (isset($detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::STATE])) {
                $constantOrderState = $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::STATE];
            } else {
                $constantOrderState = $detailOrder[$key]['ap_state'];
            }
            $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::STATE] = $constantOrderState;
            $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::STATENAME]
                = $region->loadByCode($constantOrderState, $order->getShippingAddress()->getCountryId())->getName();
        }
    }

    /**
     * OrderDetail getDetailAddress2
     *
     * @param string $detailOrder //The detail Order
     * @param string $key         //The key
     *
     * @return null
     */
    public function getDetailAddress2(&$detailOrder, $key)
    {
        $apADDRESS2 = htmlentities($detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS2]);
        if ($detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS2] != null) {
            $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS2]
                = str_replace(\UPS\Shipping\Helper\ConstantOrder::REPLACE, '', apADDRESS2);
        } else {
            $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS2] = '';
        }
    }

    /**
     * OrderDetail getDetailAddress3
     *
     * @param string $detailOrder //The detail Order
     * @param string $key         //The key
     *
     * @return null
     */
    public function getDetailAddress3(&$detailOrder, $key)
    {
        $apADDRESS3 = htmlentities($detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS3]);
        if ($detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS3] != null) {
            $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS3]
                = str_replace(\UPS\Shipping\Helper\ConstantOrder::REPLACE, '', $apADDRESS3);
        } else {
            $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::AP_ADDRESS3] = '';
        }
    }

    /**
     * OrderDetail translateAccess
     *
     * @param string $detailOrder //The detail Order
     * @param string $key         //The key
     *
     * @return null
     */
    public function translateAccess(&$detailOrder, $key)
    {
        $accessorial_service_translate = [];
        if (isset($detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::ACCESSORIAL_SERVICE])
            && !empty($detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::ACCESSORIAL_SERVICE])
        ) {
            $arrAccessorial = json_decode($detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::ACCESSORIAL_SERVICE]);
            foreach ($arrAccessorial as $key1 => $value) {
                $accessorial_service_translate[$key1] = __($value);
            }
        }
        $detailOrder[$key]['accessorial_service_translate'] = $accessorial_service_translate;
    }

    /**
     * OrderDetail getServiceName
     *
     * @param string $detailOrder //The detail Order
     * @param string $key         //The key
     *
     * @return null
     */
    public function getServiceName(&$detailOrder, $key)
    {
        $serviceNameString = $detailOrder[$key]['service_name'];
        if ($detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] == '&trade;') {
            $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO]='UPS Access Point&trade; Economy';
        } elseif ($serviceNameString == 'UPS Standard') {
            $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO]
                = 'UPS'. $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' Standard';
        } elseif ($serviceNameString == 'UPS Express 12:00') {
            $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO] = 'UPS Express 12:00';
        } elseif ($serviceNameString == 'UPS Ground') {
            $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO]
                = 'UPS'. $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' Ground';
        } elseif ($serviceNameString == 'UPS Next Day Air Early') {
            $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO]
                = 'UPS Next Day Air'. $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' Early';
        } else {
            $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO]
                = $serviceNameString . $detailOrder[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL];
        }
    }
    /**
     * OrderDetail getProductName
     *
     * @param string $order       //The order
     * @param array  $productName //The product name
     *
     * @return null
     */
    public function getProductName($order, &$productName)
    {
        foreach ($order->getItemsCollection() as $item) {
            if ($item->getParentItemId() == '' || $item->getParentItemId() == null) {
                $checkParent = $this->modelOrder->checkParentItem($item->getItemId());
                $countCheckParents = $this->countCheckParent($checkParent);
                if ($countCheckParents == 0) {
                    array_push($productName, round($item->getQtyOrdered()) . ' x ' . $item->getProduct()->getName());
                }
            } else {
                array_push($productName, round($item->getQtyOrdered()) . ' x ' . $item->getProduct()->getName());
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
     * OrderDetail addArray
     *
     * @param string $array //The array
     *
     * @return null
     */
    public function addArray($array)
    {
        $arrayReturn = [];
        foreach ($array as $key => $value) {
            if ($value != "" && $value != null) {
                $arrayReturn[] = $value;
            }
        }
        return $arrayReturn;
    }

    /**
     * OrderDetail checkData
     *
     * @param string $data //The data
     *
     * @return null
     */
    public function checkData($data)
    {
        if ($data != null) {
            return $data;
        } else {
            return "";
        }
    }
}
