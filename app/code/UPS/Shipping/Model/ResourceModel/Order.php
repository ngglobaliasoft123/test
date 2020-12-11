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
namespace UPS\Shipping\Model\ResourceModel;
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
class Order extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Order __construct
     * collect registration data
     *
     * @param string $context //The context
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Order _construct
     *
     * @return array $data
     */
    protected function _construct()
    {
        $this->_init('ups_shipping_orders', 'id');
    }

    /**
     * Order getListOpenOrders
     *
     * @param string $limit   //The limit
     * @param string $page    //The page
     * @param string $request //The request
     *
     * @return array $data
     */
    public function getListOpenOrders($limit, $page, $request)
    {
        $shippingMethod = \UPS\Shipping\Helper\Config::SALES_ORDER_SHIPPING_METHOD;
        $saleTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER);
        $saleId = \UPS\Shipping\Helper\ConstantModel::ID_MAGENTO_ENTITY_ID;
        $saleCols = [\UPS\Shipping\Helper\ConstantModel::ENTITY_ID, \UPS\Shipping\Helper\ConstantModel::INCREMENT_ID,
        \UPS\Shipping\Helper\ConstantModel::CREATED_AT, 'updated_at'];
        $orderTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER_ADDRESS);
        $entityId = \UPS\Shipping\Helper\ConstantModel::SHIPPING_ADDRESS_ID_ENTITY_ID;
        $orderCols = [\UPS\Shipping\Helper\ConstantModel::STREET, 'city',
        \UPS\Shipping\Helper\ConstantModel::COUNTRY_ID_AS_NAMECOUNTRY];
        $serviceTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_SERVICES);
        $serviceCols = [\UPS\Shipping\Helper\ConstantModel::COUNTRY_CODE,
        \UPS\Shipping\Helper\ConstantModel::SERVICE_TYPE,
        \UPS\Shipping\Helper\ConstantModel::SERVICE_NAME, \UPS\Shipping\Helper\ConstantModel::SERVICE_SYMBOL];
        $payCols = [\UPS\Shipping\Helper\ConstantModel::METHOD];
        $payTable =  $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER_PAYMENT);
        $select = $this->getConnection()->select()
            ->from(['op' => $this->getMainTable()])
            ->join(['so' => $saleTable], $saleId, $saleCols)
            ->join(['ad' => $orderTable], $entityId, $orderCols)
            ->join(['sv' => $serviceTable], 'op.shipping_service = sv.id', $serviceCols)
            ->join(['pay' => $payTable], 'op.order_id_magento = pay.parent_id', $payCols)
            ->where(\UPS\Shipping\Helper\ConstantModel::SHIPPING_METHOD, $shippingMethod)
            ->where('op.status = 1')
            ->where('so.status IN(?)', \UPS\Shipping\Helper\Config::SALES_ORDER_SHIPPING_STATUS)
            ->limitPage($page, $limit);

        if (isset($request[\UPS\Shipping\Helper\ConstantModel::ENTITY_ID])) {
            $select->order(['so.increment_id ' . $request[\UPS\Shipping\Helper\ConstantModel::ENTITY_ID]]);
        } elseif (isset($request[\UPS\Shipping\Helper\ConstantModel::CREATED_AT_DATE])) {
            $select->order(['DATE(so.created_at) ' . $request[\UPS\Shipping\Helper\ConstantModel::CREATED_AT_DATE]]);
        } elseif (isset($request[\UPS\Shipping\Helper\ConstantModel::CREATED_AT_TIME])) {
            $select->order(['TIME(so.created_at) ' . $request[\UPS\Shipping\Helper\ConstantModel::CREATED_AT_TIME]]);
        } elseif (isset($request[\UPS\Shipping\Helper\ConstantModel::SHIPPING_SERVICE])) {
            $select->order(['op.shipping_service ' . $request[\UPS\Shipping\Helper\ConstantModel::SHIPPING_SERVICE]]);
        } elseif (isset($request[\UPS\Shipping\Helper\ConstantModel::DELIVERY_ADDRESS])) {
            $orderRequest = $request[\UPS\Shipping\Helper\ConstantModel::DELIVERY_ADDRESS];
            $saleData = [
                'op.ap_country ' . $orderRequest,
                'op.ap_city ' . $orderRequest,
                'op.ap_address1 ' . $orderRequest,
                'op.ap_address2 ' . $orderRequest,
                'op.ap_address3 ' . $orderRequest
            ];
            $select->order($saleData);
        } elseif (isset($request['cod'])) {
            $select->order(['pay.method ' . $request['cod']]);
        } else {
            $select->order(['so.increment_id asc']);
        }
        //return $this->getConnection()->fetchAll($select);
        $listOpenOrders = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listOpenOrders[] = $row;
        }
        return $listOpenOrders;
    }

    /**
     * Order getListArchivedOrders
     *
     * @param string $limit   //The limit
     * @param string $page    //The page
     * @param string $request //The request
     *
     * @return array $data
     */
    public function getListArchivedOrders($limit, $page, $request)
    {
        $saleTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER);
        $saleId = \UPS\Shipping\Helper\ConstantModel::ID_MAGENTO_ENTITY_ID;
        $saleCols = [\UPS\Shipping\Helper\ConstantModel::ENTITY_ID, \UPS\Shipping\Helper\ConstantModel::INCREMENT_ID,
        \UPS\Shipping\Helper\ConstantModel::CREATED_AT];
        $orderTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER_ADDRESS);
        $entityId = \UPS\Shipping\Helper\ConstantModel::SHIPPING_ADDRESS_ID_ENTITY_ID;
        $orderCols = [\UPS\Shipping\Helper\ConstantModel::STREET, 'city',
        \UPS\Shipping\Helper\ConstantModel::COUNTRY_ID_AS_NAMECOUNTRY];
        $serviceTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_SERVICES);
        $serviceCols = [\UPS\Shipping\Helper\ConstantModel::COUNTRY_CODE, \UPS\Shipping\Helper\ConstantModel::SERVICE_TYPE,
        \UPS\Shipping\Helper\ConstantModel::SERVICE_NAME, \UPS\Shipping\Helper\ConstantModel::SERVICE_SYMBOL];
        $payCols = [\UPS\Shipping\Helper\ConstantModel::METHOD];
        $payTable =  $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER_PAYMENT);
        $shippingMethod = \UPS\Shipping\Helper\Config::SALES_ORDER_SHIPPING_METHOD;

        $select = $this->getConnection()->select()
            ->from(['op' => $this->getMainTable()])
            ->join(['so' => $saleTable], $saleId, $saleCols)
            ->join(['ad' => $orderTable], $entityId, $orderCols)
            ->join(['sv' => $serviceTable], 'op.shipping_service = sv.id', $serviceCols)
            ->join(['pay' =>$payTable], 'op.order_id_magento = pay.parent_id', $payCols)
            ->where(\UPS\Shipping\Helper\ConstantModel::SHIPPING_METHOD, $shippingMethod)
            ->where('op.status = 3')
            ->limitPage($page, $limit);

        if (isset($request[\UPS\Shipping\Helper\ConstantModel::ENTITY_ID])) {
            $select->order(['so.increment_id ' . $request[\UPS\Shipping\Helper\ConstantModel::ENTITY_ID]]);
        } elseif (isset($request[\UPS\Shipping\Helper\ConstantModel::CREATED_AT_DATE])) {
            $select->order(['DATE(so.created_at) ' . $request[\UPS\Shipping\Helper\ConstantModel::CREATED_AT_DATE]]);
        } elseif (isset($request[\UPS\Shipping\Helper\ConstantModel::CREATED_AT_TIME])) {
            $select->order(['TIME(so.created_at) ' . $request[\UPS\Shipping\Helper\ConstantModel::CREATED_AT_TIME]]);
        } elseif (isset($request[\UPS\Shipping\Helper\ConstantModel::SHIPPING_SERVICE])) {
            $select->order(['op.shipping_service ' . $request[\UPS\Shipping\Helper\ConstantModel::SHIPPING_SERVICE]]);
        } elseif (isset($request[\UPS\Shipping\Helper\ConstantModel::DELIVERY_ADDRESS])) {
            $orderRequest = $request[\UPS\Shipping\Helper\ConstantModel::DELIVERY_ADDRESS];
            $orderData = [
                'op.ap_country ' . $orderRequest,
                'op.ap_city ' . $orderRequest,
                'op.ap_address1 ' . $orderRequest,
                'op.ap_address2 ' . $orderRequest,
                'op.ap_address3 ' . $orderRequest
            ];
            $select->order($orderData);
        } elseif (isset($request['cod'])) {
            $select->order(['pay.method ' . $request['cod']]);
        } else {
            $select->order(['so.increment_id asc']);
        }
        //return $this->getConnection()->fetchAll($select);
        $listArchivedOrders = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listArchivedOrders[] = $row;
        }
        return $listArchivedOrders;
    }

    /**
     * Order getPageOpenOrders
     *
     * @param string $numberItemOnPage //The numberItemOnPage
     *
     * @return array $data
     */
    public function getPageOpenOrders($numberItemOnPage)
    {
        $shippingMethod = \UPS\Shipping\Helper\Config::SALES_ORDER_SHIPPING_METHOD;
        $saleOrderTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER);
        $entityId = \UPS\Shipping\Helper\ConstantModel::ID_MAGENTO_ENTITY_ID;
        $orderAddTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER_ADDRESS);
        $orderMagentoId = \UPS\Shipping\Helper\ConstantModel::SHIPPING_ADDRESS_ID_ENTITY_ID;
        $select = $this->getConnection()->select()
            ->from(['op' => $this->getMainTable()], ['count(*)'])
            ->join(['so' => $saleOrderTable], $entityId)
            ->join(['ad' => $orderAddTable], $orderMagentoId)
            ->where('op.status = 1')
            ->where(\UPS\Shipping\Helper\ConstantModel::SHIPPING_METHOD, $shippingMethod);
        $numberRows = $this->getConnection()->fetchOne($select);
        return ceil($numberRows / $numberItemOnPage);
    }

    /**
     * Order getPageArchivedOrders
     *
     * @param string $numberItemOnPage //The numberItemOnPage
     *
     * @return array $data
     */
    public function getPageArchivedOrders($numberItemOnPage)
    {
        $shippingMethod = \UPS\Shipping\Helper\Config::SALES_ORDER_SHIPPING_METHOD;
        $saleOrderTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER);
        $entityId = \UPS\Shipping\Helper\ConstantModel::ID_MAGENTO_ENTITY_ID;
        $orderAddTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER_ADDRESS);
        $orderMagentoId = \UPS\Shipping\Helper\ConstantModel::SHIPPING_ADDRESS_ID_ENTITY_ID;
        $select = $this->getConnection()->select()
            ->from(['op' => $this->getMainTable()], ['count(*)'])
            ->join(['so' => $saleOrderTable], $entityId)
            ->join(['ad' => $orderAddTable], $orderMagentoId)
            ->where('op.status = 3')
            ->where(\UPS\Shipping\Helper\ConstantModel::SHIPPING_METHOD, $shippingMethod);
        $numberRows = $this->getConnection()->fetchOne($select);
        return ceil($numberRows / $numberItemOnPage);
    }

    /**
     * Order getDetailOrder
     *
     * @param string $id //The id
     *
     * @return array $data
     */
    public function getDetailOrder($id)
    {
        $saleOrderTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER);
        $entityId = \UPS\Shipping\Helper\ConstantModel::ID_MAGENTO_ENTITY_ID;
        $saleOrderCols = [\UPS\Shipping\Helper\ConstantModel::ENTITY_ID,
        \UPS\Shipping\Helper\ConstantModel::INCREMENT_ID,
        \UPS\Shipping\Helper\ConstantModel::CREATED_AT, 'base_shipping_amount', 'grand_total'];
        $orderAddTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER_ADDRESS);
        $orderMagentoId = \UPS\Shipping\Helper\ConstantModel::SHIPPING_ADDRESS_ID_ENTITY_ID;
        $orderAddCols = [\UPS\Shipping\Helper\ConstantModel::STREET, 'city', 'lastname', 'firstname', 'email',
        'telephone', \UPS\Shipping\Helper\ConstantModel::COUNTRY_ID_AS_NAMECOUNTRY];
        $payTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER_PAYMENT);
        $payCols = [\UPS\Shipping\Helper\ConstantModel::METHOD];
        $shippingServiceTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_SERVICES);
        $serviceCols = [\UPS\Shipping\Helper\ConstantModel::COUNTRY_CODE,
        \UPS\Shipping\Helper\ConstantModel::SERVICE_TYPE,
        \UPS\Shipping\Helper\ConstantModel::SERVICE_KEY,
        \UPS\Shipping\Helper\ConstantModel::SERVICE_NAME,
        \UPS\Shipping\Helper\ConstantModel::RATE_CODE,
        \UPS\Shipping\Helper\ConstantModel::SERVICE_ID . ' as idservice',
        \UPS\Shipping\Helper\ConstantModel::SERVICE_SYMBOL];
        $arrayOrderTable = [\UPS\Shipping\Helper\ConstantModel::STATUS => $this->getTable('sales_order_status')];
        $shippingMethod = \UPS\Shipping\Helper\Config::SALES_ORDER_SHIPPING_METHOD;
        $select = $this->getConnection()->select()
            ->from(['op' => $this->getMainTable()])
            ->join(['so' => $saleOrderTable], $entityId, $saleOrderCols)
            ->join(['ad' => $orderAddTable], $orderMagentoId, $orderAddCols)
            ->join(['pay' => $payTable], 'pay.parent_id = op.order_id_magento', $payCols)
            ->join(['sv' => $shippingServiceTable], 'sv.id = op.shipping_service', $serviceCols)
            ->join($arrayOrderTable, 'status.status = so.status', ['label'])
            ->where(\UPS\Shipping\Helper\ConstantModel::SHIPPING_METHOD, $shippingMethod)
            ->where('op.id = ?', $id);
        return $this->getConnection()->fetchRow($select);
    }

    /**
     * Order getMultiDetailOrder
     *
     * @param string $listId //The listId
     *
     * @return array $data
     */
    public function getMultiDetailOrder($listId)
    {
        $saleOrderTb = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER);
        $saleOrdCol = [\UPS\Shipping\Helper\ConstantModel::ENTITY_ID, \UPS\Shipping\Helper\ConstantModel::INCREMENT_ID,
        \UPS\Shipping\Helper\ConstantModel::CREATED_AT, 'base_shipping_amount','grand_total'];
        $saleAddTb = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER_ADDRESS);
        $entityId = \UPS\Shipping\Helper\ConstantModel::SHIPPING_ADDRESS_ID_ENTITY_ID;
        $saleAddCols = [\UPS\Shipping\Helper\ConstantModel::STREET, 'city', 'lastname', 'firstname', 'email',
        'telephone', \UPS\Shipping\Helper\ConstantModel::COUNTRY_ID_AS_NAMECOUNTRY];
        $payTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER_PAYMENT);
        $payCol = [\UPS\Shipping\Helper\ConstantModel::METHOD];
        $shippingServiceTb = $this->getTable(\UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_SERVICES);
        $shippingServiceCol = [\UPS\Shipping\Helper\ConstantModel::COUNTRY_CODE,
        \UPS\Shipping\Helper\ConstantModel::SERVICE_TYPE,
        \UPS\Shipping\Helper\ConstantModel::SERVICE_NAME, 'rate_code', 'id as idservice',
        \UPS\Shipping\Helper\ConstantModel::SERVICE_SYMBOL];
        $shippingMethod = \UPS\Shipping\Helper\Config::SALES_ORDER_SHIPPING_METHOD;
        $select = $this->getConnection()->select()
            ->from(['op' => $this->getMainTable()])
            ->join(['so' => $saleOrderTb], \UPS\Shipping\Helper\ConstantModel::ID_MAGENTO_ENTITY_ID, $saleOrdCol)
            ->join(['ad' => $saleAddTb], $entityId, $saleAddCols)
            ->join(['pay' => $payTable], 'pay.parent_id = op.order_id_magento', $payCol)
            ->join(['sv' => $shippingServiceTb], 'sv.id = op.shipping_service', $shippingServiceCol)
            ->where(\UPS\Shipping\Helper\ConstantModel::SHIPPING_METHOD, $shippingMethod)
            ->where('op.id IN(?)', $listId);
        //return $this->getConnection()->fetchAll($select);
        $listDetailOrders = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listDetailOrders[] = $row;
        }
        return $listDetailOrders;
    }

    /**
     * Order insertOpenOrderAfterPlace
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function insertOpenOrderAfterPlace($data)
    {
        $this->getConnection()->insert($this->getMainTable(), $data);
        return $this->getConnection()->lastInsertId();
    }

    /**
     * Order getExportOrderData
     *
     * @param string $listOrderId //The listOrderId
     * @param string $OrderBy     //The OrderBy
     *
     * @return array $data
     */
    public function getExportOrderData($listOrderId, $OrderBy)
    {
        $shippingTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_SERVICES);
        $select = $this->getConnection()->select()
            ->from(['od' => $this->getMainTable()])
            ->where('od.id IN(?)', $listOrderId)
            ->join(['sv' => $shippingTable], 'sv.id = od.shipping_service')
            ->where('od.status = 1');
        $select->order(['od.id ' . $OrderBy]);
        //return $this->getConnection()->fetchAll($select);
        $listExportOrders = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listExportOrders[] = $row;
        }
        return $listExportOrders;
    }

    /**
     * Order getExportAllOrderData
     *
     * @param string $OrderBy //The OrderBy
     *
     * @return array $data
     */
    public function getExportAllOrderData($OrderBy)
    {
        $shippingTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_SERVICES);
        $select = $this->getConnection()->select()
            ->from(['od' => $this->getMainTable()])
            ->where('od.status = 1')
            ->join(['sv' => $shippingTable], 'sv.id = od.shipping_service');
        $select->order(['od.id ' . $OrderBy]);
        //return $this->getConnection()->fetchAll($select);
        $listExportOrderAll = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listExportOrderAll[] = $row;
        }
        return $listExportOrderAll;
    }

    /**
     * Order updateOpenOrdersOver
     *
     * @param string $days //The days
     *
     * @return array $data
     */
    public function updateOpenOrdersOver($days)
    {
        $arrSet = [\UPS\Shipping\Helper\ConstantModel::STATUS => 3,
        'archived_at' => date(\UPS\Shipping\Helper\ConstantModel::Y_M_D)];
        $timestamp = strtotime("-". $days ." day");
        $arrWhere = [\UPS\Shipping\Helper\ConstantModel::STATUS => 1, 'created_at < ?' => date('Y-m-d', $timestamp)];
        return $this->getConnection()->update($this->getMainTable(), $arrSet, $arrWhere);
    }

    /**
     * Order deleteArchiveOrdersOver
     *
     * @param string $days //The days
     *
     * @return array $data
     */
    public function deleteArchiveOrdersOver($days)
    {
        $date = date('Y-m-d H:i:s');
        $date = strtotime($date);
        $date = strtotime("-". $days ." day", $date);
        $date = date('Y-m-d H:i:s', $date);
        $this->getConnection()->delete(
            $this->getMainTable(),
            ['archived_at < ?' => $date, 'status =?' => 3]
        );
    }

    /**
     * Order updateStatusUnArchiveOrder
     *
     * @param string $orderIds //The orderIds
     *
     * @return array $data
     */
    public function updateStatusUnArchiveOrder($orderIds)
    {
        $arrArchiveOrder =  [\UPS\Shipping\Helper\ConstantModel::STATUS => 1,
        'archived_at' => date(\UPS\Shipping\Helper\ConstantModel::Y_M_D)];
        $this->getConnection()->update($this->getMainTable(), $arrArchiveOrder, ['id IN(?)' => $orderIds]);
    }

    /**
     * Order updateStatusArchiveOrder
     *
     * @param string $orderIds //The orderIds
     *
     * @return array $data
     */
    public function updateStatusArchiveOrder($orderIds)
    {
        $arrArchiveOrder =  [\UPS\Shipping\Helper\ConstantModel::STATUS => 3,
        'archived_at' => date(\UPS\Shipping\Helper\ConstantModel::Y_M_D)];
        $this->getConnection()->update($this->getMainTable(), $arrArchiveOrder, ['id IN(?)' => $orderIds]);
    }

    /**
     * Order getOrderByShipmentId
     *
     * @param string $shipmentId //The shipmentId
     *
     * @return array $data
     */
    public function getOrderByShipmentId($shipmentId)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('shipment_id = ?', $shipmentId);
        //return $this->getConnection()->fetchAll($select);
        $listShipmentOrders = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listShipmentOrders[] = $row;
        }
        return $listShipmentOrders;
    }

    /**
     * Order updateStatusCancelShipment
     *
     * @param string $orderId //The orderId
     *
     * @return array $data
     */
    public function updateStatusCancelShipment($orderId)
    {
        $arrUpdate = [\UPS\Shipping\Helper\ConstantModel::STATUS => 1];
        return $this->getConnection()->update($this->getMainTable(), $arrUpdate, ['id IN(?)' => $orderId]);
    }

    /**
     * Order updateStatusOrder
     *
     * @param string $idOrder    //The idOrder
     * @param string $idshipment //The idshipment
     *
     * @return array $data
     */
    public function updateStatusOrder($idOrder, $idshipment)
    {
        $arrStatus = [\UPS\Shipping\Helper\ConstantModel::STATUS => 2,'shipment_id' => $idshipment];
        $this->getConnection()->update($this->getMainTable(), $arrStatus, ['id = ?' => $idOrder]);
    }

    /**
     * Order checkParentItem
     *
     * @param string $itemId //The itemId
     *
     * @return array $data
     */
    public function checkParentItem($itemId)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('sales_order_item'))
            ->where('parent_item_id = ?', $itemId);
        //return $this->getConnection()->fetchAll($select);
        $listParentItems = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listParentItems[] = $row;
        }
        return $listParentItems;
    }

    /**
     * Order getStateMagento
     *
     * @return array $data
     */
    public function getStateMagento()
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('directory_country_region'));
        //return $this->getConnection()->fetchAll($select);
        $listStateMagentoes = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listStateMagentoes[] = $row;
        }
        return $listStateMagentoes;
    }

    /**
     * Order getStateMagentoByStateCode
     *
     * @param string $stateCode //The stateCode
     *
     * @return array $data
     */
    public function getStateMagentoByStateCode($stateCode)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('directory_country_region'))
            ->where('code = ?', $stateCode);
        return $this->getConnection()->fetchRow($select);
    }

    /**
     * Order getIdMagento
     *
     * @param string $idOrder //The idOrder
     *
     * @return array $data
     */
    public function getIdMagento($idOrder)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('id = ?', $idOrder);
        return $this->getConnection()->fetchRow($select);
    }

    /**
     * Customer name by sales_order_id
     *
     * @param string $salesOrderId //The salesOrderId
     *
     * @return array $data
     */
    public function getCustomerInfor($salesOrderId)
    {
        $saleOrderTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER);
        $entityId = \UPS\Shipping\Helper\ConstantModel::ID_MAGENTO_ENTITY_ID;
        $saleOrderCols = [\UPS\Shipping\Helper\ConstantModel::ENTITY_ID,
        \UPS\Shipping\Helper\ConstantModel::INCREMENT_ID,
        \UPS\Shipping\Helper\ConstantModel::CREATED_AT, 'base_shipping_amount', 'grand_total'];
        $orderAddTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER_ADDRESS);
        $orderMagentoId = \UPS\Shipping\Helper\ConstantModel::SHIPPING_ADDRESS_ID_ENTITY_ID;
        $orderAddCols = [\UPS\Shipping\Helper\ConstantModel::STREET, 'city', 'lastname', 'firstname', 'email',
        'telephone', \UPS\Shipping\Helper\ConstantModel::COUNTRY_ID_AS_NAMECOUNTRY];
        $payTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::SALES_ORDER_PAYMENT);
        $payCols = [\UPS\Shipping\Helper\ConstantModel::METHOD];
        $shippingServiceTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_SERVICES);
        $serviceCols = [\UPS\Shipping\Helper\ConstantModel::COUNTRY_CODE,
        \UPS\Shipping\Helper\ConstantModel::SERVICE_TYPE,
        \UPS\Shipping\Helper\ConstantModel::SERVICE_NAME, 'rate_code', 'id as idservice',
        \UPS\Shipping\Helper\ConstantModel::SERVICE_SYMBOL];
        $arrayOrderTable = [\UPS\Shipping\Helper\ConstantModel::STATUS => $this->getTable('sales_order_status')];
        $shippingMethod = \UPS\Shipping\Helper\Config::SALES_ORDER_SHIPPING_METHOD;
        $select = $this->getConnection()->select()
            ->from(['op' => $this->getMainTable()])
            ->join(['so' => $saleOrderTable], $entityId, $saleOrderCols)
            ->join(['ad' => $orderAddTable], $orderMagentoId, $orderAddCols)
            ->join(['pay' => $payTable], 'pay.parent_id = op.order_id_magento', $payCols)
            ->join(['sv' => $shippingServiceTable], 'sv.id = op.shipping_service', $serviceCols)
            ->join($arrayOrderTable, 'status.status = so.status', ['label'])
            ->where(\UPS\Shipping\Helper\ConstantModel::SHIPPING_METHOD, $shippingMethod)
            ->where('so.entity_id = ?', $salesOrderId);
        return $this->getConnection()->fetchRow($select);
    }
}
