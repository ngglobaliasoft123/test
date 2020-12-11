<?php
/**
 * Shipment file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Model\ResourceModel;
/**
 * Shipment class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Shipment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Shipment __construct
     * collect registration data
     *
     * @param string $context //The Context
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Shipment _construct
     *
     * @return array $data
     */
    protected function _construct()
    {
        $this->_init('ups_shipping_shipments', 'id');
    }

    /**
     * Shipment getShipmentDetail
     *
     * @param integer $trackingId //The trackingId
     *
     * @return array $data
     */
    public function getShipmentDetail($trackingId)
    {
        $trackingColumns = [\UPS\Shipping\Helper\ConstantModel::TRACKING_NUMBER,
        \UPS\Shipping\Helper\ConstantModel::SHIPMENT_NUMBER, \UPS\Shipping\Helper\ConstantModel::PACKAGE_DETAIL];
        $shippingColumns =  [ 'ap_name', \UPS\Shipping\Helper\ConstantModel::AP_ADDRESS1,
        \UPS\Shipping\Helper\ConstantModel::AP_ADDRESS2, \UPS\Shipping\Helper\ConstantModel::AP_ADDRESS3,
        \UPS\Shipping\Helper\ConstantModel::AP_CITY, 'ap_postcode', 'ap_country'];
        $arrColumns = ['entity_id', 'increment_id', 'base_currency_code', 'customer_firstname', 'customer_lastname'];
        $saleOrderTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER);
        $shippingOrderTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_ORDERS);
        $orderMagentoId = \UPS\Shipping\Helper\ConstantModel::ORDER_ID_MAGENTO_ENTITY_ID;
        $shippingTrackingTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_TRACKING);
        $select = $this->getConnection()->select()
            ->from(['s' => $this->getMainTable()])
            ->join(['ust' => $shippingTrackingTable], 's.shipment_number = ust.shipment_number', $trackingColumns)
            ->join(['uso' => $shippingOrderTable], 'ust.order_id = uso.id', $shippingColumns)
            ->join(['so' => $saleOrderTable], $orderMagentoId, $arrColumns)
            ->where(\UPS\Shipping\Helper\ConstantModel::ID_SHIPMENT_ID)
            ->where(\UPS\Shipping\Helper\ConstantModel::USO_STATUS)
            ->where('ust.id = ?', $trackingId);
        return $this->getConnection()->fetchRow($select);
    }

    /**
     * Shipment getAllShipments
     *
     * @return array $data
     */
    public function getAllShipments()
    {
        $select = $this->getConnection()->select()->from($this->getMainTable());
        //return $this->getConnection()->fetchAll($select);
        $shipmentAll = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $shipmentAll[] = $row;
        }
        return $shipmentAll;
    }

    /**
     * Shipment getNumberPages
     *
     * @param integer $numberItemOnPage //The numberItemOnPage
     *
     * @return array $data
     */
    public function getNumberPages($numberItemOnPage)
    {
        $shippingOrderTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_ORDERS);
        $saleOrderTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER);
        $shippingTrackingTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_TRACKING);
        $orderMagentoId = \UPS\Shipping\Helper\ConstantModel::ORDER_ID_MAGENTO_ENTITY_ID;
        $select = $this->getConnection()->select()
            ->from(['s' => $this->getMainTable()], ['count(*)'])
            ->joinLeft(['uso' => $shippingOrderTable], \UPS\Shipping\Helper\ConstantModel::ID_SHIPMENT_ID)
            ->joinLeft(['so' => $saleOrderTable], $orderMagentoId)
            ->joinLeft(['ust' => $shippingTrackingTable], 'uso.id = ust.order_id')
            ->where(\UPS\Shipping\Helper\ConstantModel::USO_STATUS);
        $numberRows = $this->getConnection()->fetchOne($select);
        return ceil($numberRows / $numberItemOnPage);
    }

    /**
     * Shipment getListShipments
     *
     * @param integer $numberItemOnPage //The numberItemOnPage
     * @param integer $offset           //The offset
     * @param integer $request          //The request
     *
     * @return array $data
     */
    public function getListShipments($numberItemOnPage, $offset, $request)
    {
        $shippingOrderColumns = [\UPS\Shipping\Helper\ConstantModel::AP_ADDRESS1,
        \UPS\Shipping\Helper\ConstantModel::AP_ADDRESS2, \UPS\Shipping\Helper\ConstantModel::AP_ADDRESS3,
        \UPS\Shipping\Helper\ConstantModel::AP_CITY, 'uso.id as idOrder'];
        $shippingOrderTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_ORDERS);
        $idShipment = \UPS\Shipping\Helper\ConstantModel::ID_SHIPMENT_ID;

        $trackingColumns = ['ust.id as trackingId', \UPS\Shipping\Helper\ConstantModel::TRACKING_NUMBER,
        \UPS\Shipping\Helper\ConstantModel::SHIPMENT_NUMBER, \UPS\Shipping\Helper\ConstantModel::PACKAGE_DETAIL];
        $trackingTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_TRACKING);
        $saleTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER);
        $magentoId = \UPS\Shipping\Helper\ConstantModel::ORDER_ID_MAGENTO_ENTITY_ID;
        $select = $this->getConnection()->select()
            ->from(['s' => $this->getMainTable()])
            ->joinLeft(['uso' => $shippingOrderTable], $idShipment, $shippingOrderColumns)
            ->joinLeft(['so' => $saleTable], $magentoId, ['entity_id', 'base_currency_code', 'increment_id'])
            ->joinLeft(['ust' => $trackingTable], 'uso.id = ust.order_id', $trackingColumns)
            ->where(\UPS\Shipping\Helper\ConstantModel::USO_STATUS)
            ->limit($numberItemOnPage, $offset);
        if (isset($request[\UPS\Shipping\Helper\ConstantModel::SHIPMENT_ID])) {
            $select->order(['s.shipment_number ' . $request[\UPS\Shipping\Helper\ConstantModel::SHIPMENT_ID]]);
        } elseif (isset($request[\UPS\Shipping\Helper\ConstantModel::TRACKING_NUMBER])) {
            $select->order(['ust.tracking_number ' . $request[\UPS\Shipping\Helper\ConstantModel::TRACKING_NUMBER]]);
        } elseif (isset($request[\UPS\Shipping\Helper\ConstantModel::ORDER_ID])) {
            $select->order(['so.increment_id ' . $request[\UPS\Shipping\Helper\ConstantModel::ORDER_ID]]);
        } elseif (isset($request['created_at_date'])) {
            $select->order(['DATE(s.date_created) ' . $request['created_at_date']]);
        } elseif (isset($request['created_at_time'])) {
            $select->order(['s.date_created ' . $request['created_at_time']]);
        } elseif (isset($request[\UPS\Shipping\Helper\ConstantModel::DELIVERY_ADDRESS])) {
            $deliveryRequest = $request[\UPS\Shipping\Helper\ConstantModel::DELIVERY_ADDRESS];
            $addressOrder = [
                's.country ' . $deliveryRequest,
                's.city ' . $deliveryRequest,
                's.address1 ' . $deliveryRequest,
                's.address2 ' . $deliveryRequest,
                's.address3 ' . $deliveryRequest
            ];
            $select->order($addressOrder);
        } elseif (isset($request['shipment_cod'])) {
            $select->order(['s.cod ' . $request['shipment_cod']]);
        } elseif (isset($request['estimated_shipping_fee'])) {
            $select->order(['s.shipping_fee ' . $request['estimated_shipping_fee']]);
        } else {
            $select->order(['s.id asc']);
        }
        //return $this->getConnection()->fetchAll($select);
        $listShipments = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listShipments[] = $row;
        }
        return $listShipments;
    }

    /**
     * Shipment getProductDetail
     *
     * @param integer $shipment_id //The shipment_id
     * @param integer $order_id    //The order_id
     *
     * @return array $data
     */
    public function getProductDetail($shipment_id, $order_id)
    {
        $orderTable = ['item_id', 'parent_item_id', \UPS\Shipping\Helper\ConstantModel::ORDER_ID, 'name', 'qty_ordered'];
        $select = $this->getConnection()->select()
            ->from(['s' => $this->getMainTable()], ['id'])
            ->join(['soi' => $this->getTable('sales_order_item')], '', $orderTable)
            ->where('soi.order_id = ?', $order_id)
            ->where('s.id = ?', $shipment_id);
        //return $this->getConnection()->fetchAll($select);
        $listShipmentDetails = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listShipmentDetails[] = $row;
        }
        return $listShipmentDetails;
    }

    /**
     * Shipment getExportShipmentData
     *
     * @param integer $listTrack //The listTrack
     *
     * @return array $data
     */
    public function getExportShipmentData($listTrack)
    {
        $shipColums = ['country_code', 'service_type', 'service_name', 'service_symbol'];
        $shippingOrderColumns = ['id', 'order_id_magento', 'shipping_service',
        \UPS\Shipping\Helper\ConstantModel::SHIPMENT_ID, 'quote_id',
        \UPS\Shipping\Helper\ConstantModel::STATUS, 'ap_name',
        \UPS\Shipping\Helper\ConstantModel::AP_ADDRESS1, \UPS\Shipping\Helper\ConstantModel::AP_ADDRESS2,
        \UPS\Shipping\Helper\ConstantModel::AP_ADDRESS3, 'ap_state', 'ap_postcode',
        \UPS\Shipping\Helper\ConstantModel::AP_CITY, 'ap_country', 'created_at', 'archived_at'];
        $shippingOrderTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_ORDERS);
        $shippingTrackingTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_TRACKING);
        $select = $this->getConnection()->select()
            ->from(['s' => $this->getMainTable()])
            ->join(['ust' => $shippingTrackingTable], 's.shipment_number = ust.shipment_number')
            ->join(['uso' => $shippingOrderTable], 'ust.order_id = uso.id', $shippingOrderColumns)
            ->join(['sv' => $this->getTable('ups_shipping_services')], 's.shipping_service = sv.id', $shipColums)
            ->where(\UPS\Shipping\Helper\ConstantModel::ID_SHIPMENT_ID)
            ->where(\UPS\Shipping\Helper\ConstantModel::USO_STATUS)
            ->where('ust.id IN(?)', $listTrack);
        //return $this->getConnection()->fetchAll($select);
        $listExportShipments = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listExportShipments[] = $row;
        }
        return $listExportShipments;
    }

    /**
     * Shipment deleteRowShipment
     *
     * @param integer $shipmentNumber //The shipmentNumber
     *
     * @return array $data
     */
    public function deleteRowShipment($shipmentNumber)
    {
        return $this->getConnection()->delete($this->getMainTable(), ['shipment_number = ?' => $shipmentNumber]);
    }

    /**
     * Shipment getListShipmentsOver
     *
     * @param integer $days //The days
     *
     * @return array $data
     */
    public function getListShipmentsOver($days)
    {
        $timestamp = strtotime("-". $days ." days");
        $select = $this->getConnection()->select(\UPS\Shipping\Helper\ConstantModel::SHIPMENT_NUMBER)
            ->from($this->getMainTable())
            ->where('date_created < ?', date('Y-m-d', $timestamp));
        //return $this->getConnection()->fetchAll($select);
        $listShipmentsOvers = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listShipmentsOvers[] = $row;
        }
        return $listShipmentsOvers;
    }

    /**
     * Shipment createShipment
     *
     * @param integer $shipmentNumber  //The shipmentNumber
     * @param integer $shippingService //The shippingService
     * @param integer $accessorial     //The accessorial
     * @param integer $now             //The now
     * @param integer $shippingFee     //The shippingFee
     * @param integer $COD             //The COD
     * @param integer $apId            //The apId
     * @param integer $name            //The name
     * @param integer $state           //The state
     * @param integer $phone           //The phone
     * @param integer $address1        //The address1
     * @param integer $address2        //The address2
     * @param integer $address3        //The address3
     * @param integer $city            //The city
     * @param integer $postcode        //The postcode
     * @param integer $country         //The country
     * @param integer $email           //The email
     * @param integer $OrderValue      //The OrderValue
     *
     * @return array $data
     */
    public function createShipment($shipmentNumber, $shippingService, $accessorial, $now, $shippingFee, $COD, $apId, $name, $state, $phone, $address1, $address2, $address3, $city, $postcode, $country, $email, $OrderValue)
    {
        $data = [
            \UPS\Shipping\Helper\ConstantModel::SHIPMENT_NUMBER => $shipmentNumber,
            'shipping_service' => $shippingService,
            'accessorial_service' => $accessorial,
            'date_created' => $now,
            \UPS\Shipping\Helper\ConstantModel::STATUS => 'Shipped',
            'cod' => $COD,
            'shipping_fee' => $shippingFee,
            'ap_id' => $apId,
            'name' => $name,
            'address1' => $address1,
            'address2' => $address2,
            'address3' => $address3,
            'state' => $state,
            'postcode' => $postcode,
            'city' => $city,
            'country' => $country,
            'phone' => $phone,
            'email' => $email,
            'order_value' => $OrderValue
        ];
        $this->getConnection()->insert($this->getMainTable(), $data);
        return $this->getConnection()->lastInsertId();
    }

    /**
     * Shipment updateSelectedService
     *
     * @param integer $shipmentNumber //The shipmentNumber
     * @param integer $trackingNumber //The trackingNumber
     * @param integer $package        //The package
     * @param integer $orderid        //The orderid
     *
     * @return array $data
     */
    public function createTracking($shipmentNumber, $trackingNumber, $package, $orderid)
    {
        $data = [
            \UPS\Shipping\Helper\ConstantModel::SHIPMENT_NUMBER => $shipmentNumber,
            \UPS\Shipping\Helper\ConstantModel::TRACKING_NUMBER => $trackingNumber,
            \UPS\Shipping\Helper\ConstantModel::STATUS => '1',
            \UPS\Shipping\Helper\ConstantModel::ORDER_ID => $orderid,
            \UPS\Shipping\Helper\ConstantModel::PACKAGE_DETAIL => $package,
        ];
        $dataTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_TRACKING);
        return $this->getConnection()->insert($dataTable, $data);
    }

    /**
     * Shipment getIdShipmentMagentoInsert
     *
     * @return array $data
     */
    public function getIdShipmentMagentoInsert()
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('sales_shipment'))
            ->order(['entity_id DESC'])
            ->limitPage(0, 1);
        return $this->getConnection()->fetchRow($select);
    }

    /**
     * Shipment updateStatus
     *
     * @param integer $shipmentId //The shipmentId
     * @param integer $Status     //The Status
     *
     * @return array $data
     */
    public function updateStatus($shipmentId, $Status)
    {
        $statusString = \UPS\Shipping\Helper\ConstantModel::STATUS;
        $this->getConnection()->update($this->getMainTable(), [$statusString => $Status ], ['id = ?' => $shipmentId]);
    }

    /**
     * Shipment removeShipment
     *
     * @param integer $day //The day
     *
     * @return array $data
     */
    public function removeShipment($day)
    {
        $date = date('Y-m-d H:i:s');
        $date = strtotime($date);
        $date = strtotime("-". $day ." day", $date);
        $date = date('Y-m-d H:i:s', $date);
        $this->getConnection()->delete(
            $this->getMainTable(),
            ['date_created < ?' => $date]
        );
    }
}
