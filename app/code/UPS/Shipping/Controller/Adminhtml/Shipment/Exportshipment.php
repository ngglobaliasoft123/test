<?php
/**
 * Exportshipment file
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
 * Exportshipment class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Exportshipment extends \Magento\Framework\App\Action\Action
{

    protected $exportData;
    protected $pageFactory;
    protected $salesOrder;
    protected $timezone;
    protected $countryModel;
    protected $modelOrder;
    protected $dateTime;
    /**
     * Exportshipment constructor
     *
     * @param string   $context      //The Context
     * @param string   $pageFactory  //The Api
     * @param string   $exportData   //The export Data
     * @param string   $modelOrder   //The model Order
     * @param string   $salesModel   //The sales Model
     * @param string   $timezone     //The time zone
     * @param string   $countryModel //The country Model
     * @param DateTime $dateTime     //The Datetime
     *
     * @return null
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \UPS\Shipping\Model\Shipment $exportData,
        \UPS\Shipping\Model\Order $modelOrder,
        \Magento\Sales\Model\Order $salesModel,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Directory\Model\Country $countryModel,
        DateTime $dateTime
    ) {
        $this->modelOrder = $modelOrder;
        $this->countryModel = $countryModel;
        $this->timezone = $timezone;
        $this->exportData = $exportData;
        $this->salesOrder = $salesModel;
        $this->pageFactory = $pageFactory;
        $this->dateTime = $dateTime;
        parent::__construct($context);
    }
    /**
     * Exportshipment execute
     *
     * @return null
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        if (isset($data["statusExport"])) {
            $listOrderId = json_decode($data['listChecked']);
            $listOrderExport = $this->exportData->getExportShipmentData($listOrderId);

            $dataExport = [
                ['Shipment ID', 'Date', 'Time', 'Tracking number', 'deliveryStatus', 'COD', 'CODAmount', 'CODCurrency',
                'Estimated shipping fee', 'Shipping service', 'Accessorials', 'Order ID', 'Order date', 'Order value',
                'Shipping fee', 'Package details', 'Product details', 'Customer name', 'Customer Address line 1',
                'Customer Address line 2', 'Customer Address line 3', 'Customer PostalCode', 'Customer Phone no',
                'Customer City', 'Customer StateOrProvince', 'Customer Country', 'Customer Email',
                'AlternateDeliveryAddressIndicator', 'UPSAccessPointID', 'Access Point Address line 1',
                'Access Point Address line 2', 'Access Point Address line 3', 'Access Point City',
                'Access Point StateOrProvince', 'Access Point PostalCode', 'Access Point Country']
            ];
            foreach ($listOrderExport as $key => $shipment) {
                $order = $this->getOrderData($shipment);
                $productName = [];
                foreach ($order->getItemsCollection() as $item) {
                    $productNameString = round($item->getQtyOrdered()) . ' x '
                    . html_entity_decode($item->getProduct()->getName());
                    if ($item->getParentItemId() == '' || $item->getParentItemId() == null) {
                        $checkParent = $this->modelOrder->checkParentItem($item->getItemId());
                        $countCheckParents = $this->countCheckParent($checkParent);
                        if ($countCheckParents == 0) {
                            array_push($productName, $productNameString);
                        }
                    } else {
                        array_push($productName, $productNameString);
                    }
                }
                $accessorial = json_decode($shipment['accessorial_service']);
                $arrayAccessorialShipment = [];
                foreach ($accessorial as $key => $value) {
                    $arrayAccessorialShipment[] = trim(__($value));
                }
                if ($shipment['cod'] == '1') {
                    if ($shipment['service_type'] == "AP") {
                        $arrayAccessorialShipment[] = __(\UPS\Shipping\Helper\Config::SHIP_TO_SERVICE_AP);
                    } else {
                        $arrayAccessorialShipment[] = __(\UPS\Shipping\Helper\Config::SHIP_TO_SERVICE_ADD);
                    }
                    $method = "Yes";
                } else {
                    $method = "No";
                }

                $country = $this->getCountryString($order);
                $name = $this->getNameString($order);
                $rowExport = [
                    'Shipment_ID' => $shipment['shipment_number'],
                    'Date' => date('Y-m-d', strtotime($shipment['date_created'])),
                    'Time' => date('H:i:s', strtotime($shipment['date_created'])),
                    'Tracking_number' => $shipment['tracking_number'],
                    'deliveryStatus' => $order->getStatus(),
                    'COD' => __($method),
                    'CODAmount' => ($method != 'No') ? '"' . number_format((float)preg_replace(\UPS\Shipping\Helper\ConstantExport::FORMAT_REPLACE, '', $shipment['order_value']), 2) .'"':'',
                    'CODCurrency' => ($method != 'No') ? $order->getOrderCurrencyCode() : '',
                    'Estimated_shipping_fee' => '"' . number_format((float)preg_replace(\UPS\Shipping\Helper\ConstantExport::FORMAT_REPLACE, '', $shipment['shipping_fee']), 2) .'"',
                    'Shipping_service' => '"' . $this->getShippingServiceName($shipment['service_name'], $shipment['service_symbol']) .'"',
                    'Accessorials' => '"' . implode(', ', $arrayAccessorialShipment) .'"',
                    'Order_ID' => (string)$order->getIncrementId(),
                    'Order_date' => $this->dateTime->date('Y-m-d', $this->dateTime->timestamp($order->getCreatedAt())),
                    'Order_value' => '"' . number_format((float)preg_replace(\UPS\Shipping\Helper\ConstantExport::FORMAT_REPLACE, '', $shipment['order_value']), 2) .'"',
                    'Shipping_fee' => '"' . number_format($order->getPayment()->getShippingAmount(), 2) .'"',
                    'Package_details' => '"' . $shipment['package_detail'] .'"',
                    'Product_details' => '"' . implode(', ', $productName) .'"',
                    'Customer_name' => html_entity_decode($name),
                    'Customer_Address_line_1' => '"' . html_entity_decode($order->getShippingAddress()->getStreetLine(1)) .'"',
                    'Customer_Address_line_2' => '"' . html_entity_decode($order->getShippingAddress()->getStreetLine(2)) .'"',
                    'Customer_Address_line_3' => '"' . html_entity_decode($order->getShippingAddress()->getStreetLine(3)) .'"',
                    'Customer_PostalCode' => $order->getShippingAddress()->getPostcode(),
                    'Customer_Phone' => $order->getShippingAddress()->getTelephone(),
                    'Customer_City' => html_entity_decode($order->getShippingAddress()->getCity()),
                    'Customer_StateOrProvince' => $order->getShippingAddress()->getRegion(),
                    'Customer_Country' => $country,
                    'Customer_Email' => $order->getShippingAddress()->getEmail(),
                ];
                if ($shipment['service_type'] == "AP") {
                    $rowExport['AlternaetDeliveryAddressIndicator'] = 1;
                    $rowExport['UPSAcessPointID'] = (isset($shipment['ap_id']) ? $shipment['ap_id'] : '');
                    $rowExport['Access_Point_Address_line_1'] = '"' . html_entity_decode($shipment['name']) .'"';
                    $stringAddress1 = htmlentities($shipment['address1']);
                    $stringAddress1 = html_entity_decode($stringAddress1);
                    $rowExport['Access_Point_Address_line_2'] = '"' . str_replace(\UPS\Shipping\Helper\ConstantExport::REPLACE, ['', ''], $stringAddress1) .'"';
                    $stringAddress2 = htmlentities($shipment['address2']);
                    $stringAddress2 = html_entity_decode($stringAddress2);
                    $rowExport['Access_Point_Address_line_3'] = '"' . str_replace(\UPS\Shipping\Helper\ConstantExport::REPLACE, ['', ''], $stringAddress2) .'"';

                    $rowExport['Access_Point_City'] = '"' . html_entity_decode($shipment['city']) .'"';
                    $rowExport['Access_Point_StateOrProvince'] = $shipment['state'];
                    $rowExport['Access_Point_PostalCode'] = $shipment['postcode'];
                    $rowExport['Access_Point_Country'] = $this->countryModel->loadByCode($shipment['country'])->getName();
                } else {
                    $rowExport['Customer_name'] = html_entity_decode($shipment['name']);
                    $stringAddress1 = htmlentities($shipment['address1']);
                    $stringAddress1 = html_entity_decode($stringAddress1);
                    $rowExport['Customer_Address_line_1'] = '"' . str_replace(\UPS\Shipping\Helper\ConstantExport::REPLACE, ['', ''], $stringAddress1) .'"';
                    $stringAddress2 = htmlentities($shipment['address2']);
                    $stringAddress2 = html_entity_decode($stringAddress2);
                    $rowExport['Customer_Address_line_2'] = '"' . str_replace(\UPS\Shipping\Helper\ConstantExport::REPLACE, ['', ''], $stringAddress2) .'"';
                    $stringAddress3 = htmlentities($shipment['address3']);
                    $stringAddress3 = html_entity_decode($stringAddress3);
                    $rowExport['Customer_Address_line_3'] = '"' . str_replace(\UPS\Shipping\Helper\ConstantExport::REPLACE, ['', ''], $stringAddress3) .'"';

                    $rowExport['Customer_PostalCode'] = $shipment['postcode'];
                    $rowExport['Customer_Phone'] = $shipment['phone'];
                    $rowExport['Customer_City'] = html_entity_decode($shipment['city']);
                    $rowExport['Customer_StateOrProvince'] = $shipment['state'];
                    $rowExport['Customer_Country'] = $this->countryModel->loadByCode($shipment['country'])->getName();
                    $rowExport['Customer_Email'] = $shipment['email'];
                    $rowExport['AlternaetDeliveryAddressIndicator'] = 0;
                    $rowExport['UPSAcessPointID'] = '';
                    $rowExport['Access_Point_Address_line_1'] = '';
                    $rowExport['Access_Point_Address_line_2'] = '';
                    $rowExport['Access_Point_Address_line_3'] = '';
                    $rowExport['Access_Point_City'] = '';
                    $rowExport['Access_Point_StateOrProvince'] = '';
                    $rowExport['Access_Point_PostalCode'] = '';
                    $rowExport['Access_Point_Country'] = '';
                }
                array_push($dataExport, $rowExport);
            }
            $filename = "Shipments_data_" . date('dmy') . ".csv";
            header('Content-type: text/csv');
            header('Content-Type: application/force-download; charset=UTF-8');
            header('Cache-Control: no-store, no-cache');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            $this->exportCSVFile($dataExport);
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
     * Archived getOrderData
     *
     * @param string $shipment //The shipment
     *
     * @return array $Order
     */
    public function getOrderData($shipment)
    {
        return $this->salesOrder->load($shipment['order_id_magento']);
    }

    /**
     * Exportshipment getCountryString
     *
     * @param string $records //order
     *
     * @return string
     */
    public function getCountryString($order)
    {
        $country = '';
        if ($order->getShippingAddress()->getCountryId() != ''
            && $order->getShippingAddress()->getCountryId() != null
        ) {
            $country = $this->countryModel->loadByCode($order->getShippingAddress()->getCountryId())->getName();
        }
        return $country;
    }

    /**
     * Exportshipment getNameString
     *
     * @param string $order //The order
     *
     * @return string
     */
    public function getNameString($order)
    {
        $nameString = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
        if ($order->getCustomerName() != 'Guest') {
            $nameString = $order->getShippingAddress()->getFirstname() . ' '
            . $order->getShippingAddress()->getLastname();
        }
        return $nameString;
    }

    /**
     * Exportshipment exportCSVFile
     *
     * @param string $records //The PDF file link
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
