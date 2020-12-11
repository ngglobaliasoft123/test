<?php
/**
 * Export file
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
 * Export class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Export extends \Magento\Framework\App\Action\Action
{
    protected $exportData;
    protected $timezone;
    protected $salesOrder;
    protected $countryModel;
    protected $regionModel;
    protected $dateTime;
    protected $modelOrder;

    /**
     * Export __construct
     *
     * @param string   $context      //The context
     * @param string   $exportData   //The exportData
     * @param string   $salesModel   //The salesModel
     * @param string   $timezone     //The timezone
     * @param string   $countryModel //The countryModel
     * @param string   $regionModel  //The regionModel
     * @param string   $modelOrder   //The modelOrder
     * @param DateTime $dateTime     //The Datatime
     *
     * @return string $dataRequestShip
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \UPS\Shipping\Model\Order $exportData,
        \Magento\Sales\Model\Order $salesModel,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Directory\Model\Country $countryModel,
        \Magento\Directory\Model\RegionFactory $regionModel,
        \UPS\Shipping\Model\Order $modelOrder,
        DateTime $dateTime
    ) {
        $this->countryModel = $countryModel;
        $this->exportData = $exportData;
        $this->salesOrder = $salesModel;
        $this->timezone = $timezone;
        $this->regionModel = $regionModel;
        $this->dateTime = $dateTime;
        $this->modelOrder = $modelOrder;
        parent::__construct($context);
    }

    /**
     * Export execute
     *
     * @return string $dataRequestShip
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        if (isset($data["ExportType"])) {
            $this->getlistOrderExport($data, $listOrderExport);
            $dataExport = [
                ['Order ID', 'Order Date', 'Order Time', 'COD', 'COD Amount', 'COD Currency', 'Currency State',
                'Total Paid', 'Total Products', 'Shipping Service',
                    'Accessorials Service', 'Product Name', 'Merchant UPSaccount Number', 'Customer Last Name',
                    'Customer First Name', 'Customer Address line 1',
                    'Customer Address line 2', 'Customer Address line 3', 'Customer PostalCode', 'Customer Phone',
                    'Customer City', 'Customer StateOrProvince',
                    'Customer Country', 'Customer Email', 'AlternateDeliveryAddressIndicator', 'UPSAccessPointID',
                    'Access Point Address line 1', 'Access Point Address line 2',
                    'Access Point Address line 3', 'Access Point City', 'Access Point StateOrProvince',
                    'Access Point PostalCode', 'Access Point Country']
            ];
            foreach ($listOrderExport as $key => $openOrder) {
                $order = $this->getOrderData($openOrder);
                $productName = [];
                $totalPrice = 0;
                $this->getTotalPrice($order, $totalPrice, $productName);
                $accessorialShipment = [];

                $this->getAccessorial($accessorialShipment, $openOrder, $order);

                $cod = '';
                $codAmount = '';
                $codCurrency = '';
                $this->getCod($order, $cod, $codAmount, $codCurrency);
                $this->getAlternaet($openOrder, $alternaet_delivery);
                $this->getCountry($order, $country);
                $this->getCountryAp($openOrder, $order, $country_ap);
                $exportAPId = $openOrder[\UPS\Shipping\Helper\ConstantExport::AP_ID];
                $exportAPName = $openOrder[\UPS\Shipping\Helper\ConstantExport::AP_NAME];
                $exportAPAddress1 = $openOrder[\UPS\Shipping\Helper\ConstantExport::AP_ADDRESS1];
                $exportAPCity = $openOrder[\UPS\Shipping\Helper\ConstantExport::AP_CITY];
                $exportAPState = $openOrder[\UPS\Shipping\Helper\ConstantExport::AP_STATE];
                $stateName = $this->regionModel->create()->loadByCode($exportAPState, $order->getShippingAddress()->getCountryId())->getName();
                $exportAPPostcode = $openOrder[\UPS\Shipping\Helper\ConstantExport::AP_POSTCODE];

                $exportDate = $this->dateTime->date('m/d/y H:i:s', $this->dateTime->timestamp($order->getCreatedAt()));
                $exportReplaceString = \UPS\Shipping\Helper\ConstantExport::REPLACE;
                $exportgetStreetLine1 = $order->getShippingAddress()->getStreetLine(1);
                $exportgetStreetLine2 = $order->getShippingAddress()->getStreetLine(2);
                $exportgetStreetLine3 = $order->getShippingAddress()->getStreetLine(3);
                $customerInfor = $this->exportData->getCustomerInfor($openOrder['order_id_magento']);
                if (!isset($customerInfor['lastname']) || empty($customerInfor['lastname'])) {
                    $customerLastName = ($order->getCustomerName() == 'Guest')
                    ? $order->getShippingAddress()->getLastname() : $order->getCustomerLastname();
                    $customerLastName = html_entity_decode($customerLastName);
                } else {
                    $customerLastName = $customerInfor['lastname'];
                }
                if (!isset($customerInfor['firstname']) || empty($customerInfor['firstname'])) {
                    $customerFirstName = ($order->getCustomerName() == 'Guest')
                    ? $order->getShippingAddress()->getFirstname() : $order->getCustomerFirstname();
                    $customerFirstName = html_entity_decode($customerFirstName);
                } else {
                    $customerFirstName = $customerInfor['firstname'];
                }
                $customerLastName = html_entity_decode($customerLastName);
                $customerFirstName = html_entity_decode($customerFirstName);
                $rowExport = [
                    'order_id' => $order->getIncrementId(),
                    'order_date' => date('Y-m-d', strtotime($exportDate)),
                    'order_time' => date('H:i:s', strtotime($exportDate)),
                    'cod' => __($cod),
                    'cod_amount' => '"' . $codAmount .'"',
                    'cod_currency' => '"' . $codCurrency .'"',
                    'currency_state' => $order->getStatusLabel(),
                    'total_paid' => '"' . number_format($order->getGrandTotal(), 2) .'"',
                    'total_product' => '"' . number_format($totalPrice, 2) .'"',
                    'shipping_service' => '"' . $this->getShippingServiceName($openOrder['service_name'], $openOrder['service_symbol']) .'"',
                    'accessorial_service' => '"' . implode(', ', $accessorialShipment) .'"',
                    'product_name' => '"' . str_replace('&trade;', '™', implode(', ', $productName)) .'"',
                    'merchant_ups_account_number' => '',
                    'customer_last_name' => $customerLastName,
                    'customer_first_name' => $customerFirstName,
                    'customer_address_1' => '"' . str_replace($exportReplaceString, ['',''], $exportgetStreetLine1) .'"',
                    'customer_address_2' => '"' . str_replace($exportReplaceString, ['',''], $exportgetStreetLine2) .'"',
                    'customer_address_3' => '"' . str_replace($exportReplaceString, ['',''], $exportgetStreetLine3) .'"',
                    'customer_postal_code' => $order->getShippingAddress()->getPostcode(),
                    'customer_phone' => $order->getShippingAddress()->getTelephone(),
                    'customer_city' => $order->getShippingAddress()->getCity(),
                    'customer_state_or_province' => $order->getShippingAddress()->getRegion(),
                    'customer_country' => $country,
                    'customer_email' => $order->getShippingAddress()->getEmail(),
                    'alternaet_delivery_address_indicator' => $alternaet_delivery,
                    'UPSAcessPointID' => ($exportAPId != '' || $exportAPId != null) ? $exportAPId : '',
                    'Access_Point_Address_line_1' => ($exportAPName != '' || $exportAPName != null)
                    ? $exportAPName : '',
                    'Access_Point_Address_line_2' => ($exportAPAddress1 != '' || $exportAPAddress1 != null)
                    ? str_replace('&#xD;', '', $exportAPAddress1) : '',
                    'Access_Point_Address_line_3' => '',
                    'Access_Point_City' => ($exportAPCity != '' || $exportAPCity != null) ? $exportAPCity : '',
                    'Access_Point_StateOrProvince' => ($exportAPState != '' || $exportAPState != null)
                    ? $stateName : '',
                    'Access_Point_PostalCode' => ($exportAPPostcode != '' || $exportAPPostcode != null)
                    ? $exportAPPostcode : '',
                    'Access_Point_Country' => $country_ap,
                ];
                array_push($dataExport, $rowExport);
            }
            $filename = "orders_data_" . date("dmy") . ".csv";
            header('Content-type: text/csv');
            header('Content-Type: application/force-download; charset=UTF-8');
            header('Cache-Control: no-store, no-cache');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            $this->exportCSVFile($dataExport);
        }
    }

    /**
     * Archived getOrderData
     *
     * @param string $openOrder //The openOrder
     *
     * @return array $Order
     */
    public function getOrderData($openOrder)
    {
        return $this->salesOrder->load($openOrder['order_id_magento']);
    }

    /**
     * Function getlistOrderExport
     *
     * @param string $data            //The data
     * @param string $listOrderExport //The listOrderExport
     *
     * @return $listOrderExport
     */
    public function getlistOrderExport($data, &$listOrderExport)
    {
        $OrderBy = $data['OrderBy'];
        if ($data['orderIds'] != '') {
            $listOrderId = json_decode($data['orderIds']);
            $listOrderExport = $this->exportData->getExportOrderData($listOrderId, $OrderBy);
        } else {
            $listOrderExport = $this->exportData->getExportAllOrderData($OrderBy);
        }
    }

    /**
     * Function getAccessorial
     *
     * @param string $accessorialShipment //The accessorialShipment
     * @param string $openOrder           //The openOrder
     * @param string $order               //The order
     *
     * @return $accessorialShipment
     */
    public function getAccessorial(&$accessorialShipment, $openOrder, $order)
    {
        $accessorial = json_decode($openOrder['accessorial_service']);
        foreach ($accessorial as $key => $value) {
            $accessorialShipment[] = __($value);
        }
        if ($openOrder[\UPS\Shipping\Helper\ConstantExport::SERVICE_TYPE] == 'AP'
            && $order->getPayment()->getMethod() == \UPS\Shipping\Helper\ConstantExport::CASHONDELIVERY
        ) {
            $accessorialShipment[] = __(\UPS\Shipping\Helper\Config::SHIP_TO_SERVICE_AP);
        }
        if ($openOrder[\UPS\Shipping\Helper\ConstantExport::SERVICE_TYPE] == 'ADD'
            && $order->getPayment()->getMethod() == \UPS\Shipping\Helper\ConstantExport::CASHONDELIVERY
        ) {
            $accessorialShipment[] = __(\UPS\Shipping\Helper\Config::SHIP_TO_SERVICE_ADD);
        }
    }

    /**
     * Function getCountryAp
     *
     * @param string $openOrder  //The openOrder
     * @param string $order      //The order
     * @param string $country_ap //The country_ap
     *
     * @return $country_ap
     */
    public function getCountryAp($openOrder, $order, &$country_ap)
    {
        if ($openOrder[\UPS\Shipping\Helper\ConstantExport::SERVICE_TYPE] == 'AP'
            && $order->getShippingAddress()->getCountryId() != ''
        ) {
            $country_ap = $this->countryModel->loadByCode($order->getShippingAddress()->getCountryId())->getName();
        } else {
            $country_ap = '';
        }
    }

    /**
     * Function getCountry
     *
     * @param string $order   //The order
     * @param string $country //The country
     *
     * @return $country
     */
    public function getCountry($order, &$country)
    {
        if ($this->countryModel->loadByCode($order->getShippingAddress()->getCountryId())->hasData()) {
            $country = $this->countryModel->loadByCode($order->getShippingAddress()->getCountryId())->getName();
        } else {
            $country = '';
        }
    }

    /**
     * Function getCod
     *
     * @param string $order       //The order
     * @param string $cod         //The cod
     * @param string $codAmount   //The codAmount
     * @param string $codCurrency //The codCurrency
     *
     * @return $codAmount
     */
    public function getCod($order, &$cod, &$codAmount, &$codCurrency)
    {
        if ($order->getPayment()->getMethod() == \UPS\Shipping\Helper\ConstantExport::CASHONDELIVERY) {
            $cod = 'Yes';
            $codAmount = number_format($order->getGrandTotal(), 2);
            $codCurrency = $order->getOrderCurrencyCode();
        } else {
            $cod = 'No';
            $codAmount = '';
            $codCurrency = '';
        }
    }

    /**
     * Function getAlternaet
     *
     * @param string $openOrder          //The openOrder
     * @param string $alternaet_delivery //The alternaet_delivery
     *
     * @return $alternaet_delivery
     */
    public function getAlternaet($openOrder, &$alternaet_delivery)
    {
        if ($openOrder[\UPS\Shipping\Helper\ConstantExport::SERVICE_TYPE] == 'AP') {
            $alternaet_delivery = 1;
        } else {
            $alternaet_delivery = 0;
        }
    }

    /**
     * Function getTotalPrice
     *
     * @param string $order       //The order
     * @param string $totalPrice  //The totalPrice
     * @param string $productName //The productName
     *
     * @return $totalPrice
     */
    public function getTotalPrice($order, &$totalPrice, &$productName)
    {
        foreach ($order->getItemsCollection() as $item) {
            $valueFloat = (float)preg_replace('([^a-zA-Z0-9.])', '', (string)number_format($item->getPrice(), 2));
            $productNameString = html_entity_decode($item->getProduct()->getName());
            $totalPrice = $totalPrice + $valueFloat * (int)$item->getQtyOrdered();
            if ($item->getParentItemId() == '' || $item->getParentItemId() == null) {
                $checkParent = $this->exportData->checkParentItem($item->getItemId());
                $countCheckParents = $this->countCheckParent($checkParent);
                if ($countCheckParents == 0) {
                    array_push($productName, round($item->getQtyOrdered()) . ' x ' . $productNameString);
                }
            } else {
                array_push($productName, round($item->getQtyOrdered()) . ' x ' . $productNameString);
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
     * Function exportCSVFile
     *
     * @param string $records //The records
     *
     * @return null
     */
    public function exportCSVFile($records)
    {
        $fh = fopen('php://output', 'w');
        fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));
        $heading = false;
        if (!empty($records)) {
            foreach ($records as $row) {
                if (!$heading) {
                    fwrite($fh, implode(",", $row));
                    $heading = true;
                } else {
                    fwrite($fh, "\n" . implode(",", $row));
                }
            }
            fclose($fh);
        }
    }

    /**
     * Shipmentdetail getShippingServiceName
     *
     * @param string $shippingServiceName   //The shippingServiceName
     * @param string $shippingServiceSymbol //The shippingServiceSymbol
     *
     * @return string $trackingStatus
     */
    public function getShippingServiceName($shippingServiceName, $shippingServiceSymbol)
    {
        $shippingServiceSymbol = html_entity_decode($shippingServiceSymbol);
        if (trim($shippingServiceName) == 'UPS Access Point Economy') {
            return 'UPS Access Point' . $shippingServiceSymbol . ' Economy';
        } elseif (trim($shippingServiceName) == 'UPS Standard') {
            return 'UPS' . $shippingServiceSymbol . ' Standard';
        } elseif (trim($shippingServiceName) == 'UPS Express 12:00') {
            return 'UPS Express 12:00';
        } elseif (trim($shippingServiceName) == 'UPS Ground') {
            return 'UPS' . $shippingServiceSymbol . ' Ground';
        } elseif (trim($shippingServiceName) == 'UPS Next Day Air Early') {
            return 'UPS Next Day Air' . $shippingServiceSymbol . ' Early';
        } elseif (trim($shippingServiceName) == 'UPS Standard - Saturday Delivery') {
            return 'UPS' . $shippingServiceSymbol . ' Standard - Saturday Delivery';
        } elseif (trim($shippingServiceName) == 'UPS Express - Saturday Delivery') {
            return 'UPS Express' . $shippingServiceSymbol .' - Saturday Delivery';
        } else {
            return $shippingServiceName . $shippingServiceSymbol;
        }
    }
}
