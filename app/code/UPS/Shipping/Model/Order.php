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
namespace UPS\Shipping\Model;
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
class Order extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = \UPS\Shipping\Helper\Config::UPS_SHIPPING_ORDERS;

    protected $cacheTag = \UPS\Shipping\Helper\Config::UPS_SHIPPING_ORDERS;
    protected $eventPrefix = \UPS\Shipping\Helper\Config::UPS_SHIPPING_ORDERS;

    /**
     * Order _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('UPS\Shipping\Model\ResourceModel\Order');
    }

    /**
     * Order getListOpenOrders
     * calculate shipping service
     *
     * @param string $limit   //The limit
     * @param string $page    //The page
     * @param string $request //The request
     *
     * @return array $data
     */
    public function getListOpenOrders($limit, $page, $request)
    {
        return $this->getResource()->getListOpenOrders($limit, $page, $request);
    }

    /**
     * Order getListArchivedOrders
     * calculate shipping service
     *
     * @param string $limit   //The limit
     * @param string $page    //The page
     * @param string $request //The request
     *
     * @return array $data
     */
    public function getListArchivedOrders($limit, $page, $request)
    {
        return $this->getResource()->getListArchivedOrders($limit, $page, $request);
    }

    /**
     * Order getPageOpenOrders
     * calculate shipping service
     *
     * @param string $numberItemOnPage //The numberItemOnPage
     *
     * @return array $data
     */
    public function getPageOpenOrders($numberItemOnPage)
    {
        return $this->getResource()->getPageOpenOrders($numberItemOnPage);
    }

    /**
     * Order getPageArchivedOrders
     * calculate shipping service
     *
     * @param string $numberItemOnPage //The numberItemOnPage
     *
     * @return array $data
     */
    public function getPageArchivedOrders($numberItemOnPage)
    {
        return $this->getResource()->getPageArchivedOrders($numberItemOnPage);
    }

    /**
     * Order getDetailOrder
     * calculate shipping service
     *
     * @param string $id //The id
     *
     * @return array $data
     */
    public function getDetailOrder($id)
    {
        return $this->getResource()->getDetailOrder($id);
    }

    /**
     * Order getMultiDetailOrder
     * calculate shipping service
     *
     * @param string $listId //The listId
     *
     * @return array $data
     */
    public function getMultiDetailOrder($listId)
    {
        return $this->getResource()->getMultiDetailOrder($listId);
    }

    /**
     * Order insertOpenOrderAfterPlace
     * calculate shipping service
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function insertOpenOrderAfterPlace($data)
    {
        return $this->getResource()->insertOpenOrderAfterPlace($data);
    }

    /**
     * Order getExportOrderData
     * calculate shipping service
     *
     * @param string $listOrderId //The listOrderId
     * @param string $OrderBy     //The OrderBy
     *
     * @return array $data
     */
    public function getExportOrderData($listOrderId, $OrderBy)
    {
        return $this->getResource()->getExportOrderData($listOrderId, $OrderBy);
    }

    /**
     * Order getExportAllOrderData
     * calculate shipping service
     *
     * @param string $OrderBy //The OrderBy
     *
     * @return array $data
     */
    public function getExportAllOrderData($OrderBy)
    {
        return $this->getResource()->getExportAllOrderData($OrderBy);
    }

    /**
     * Order updateOpenOrdersOver
     * calculate shipping service
     *
     * @param string $days //The days
     *
     * @return array $data
     */
    public function updateOpenOrdersOver($days)
    {
        return $this->getResource()->updateOpenOrdersOver($days);
    }

    /**
     * Order deleteArchiveOrdersOver
     * calculate shipping service
     *
     * @param string $days //The days
     *
     * @return array $data
     */
    public function deleteArchiveOrdersOver($days)
    {
        return $this->getResource()->deleteArchiveOrdersOver($days);
    }

    /**
     * Order getListOpenOrdersOver90
     * calculate shipping service
     *
     * @return array $data
     */
    public function getListOpenOrdersOver90()
    {
        return $this->getResource()->getListOpenOrdersOver90();
    }

    /**
     * Order updateStatusUnArchiveOrder
     * calculate shipping service
     *
     * @param string $orderIds //The orderIds
     *
     * @return array $data
     */
    public function updateStatusUnArchiveOrder($orderIds)
    {
        $this->getResource()->updateStatusUnArchiveOrder($orderIds);
    }

    /**
     * Order updateStatusArchiveOrder
     * calculate shipping service
     *
     * @param string $orderIds //The orderIds
     *
     * @return array $data
     */
    public function updateStatusArchiveOrder($orderIds)
    {
        $this->getResource()->updateStatusArchiveOrder($orderIds);
    }

    /**
     * Order getOrderByShipmentId
     * calculate shipping service
     *
     * @param string $shipmentId //The shipmentId
     *
     * @return array $data
     */
    public function getOrderByShipmentId($shipmentId)
    {
        return $this->getResource()->getOrderByShipmentId($shipmentId);
    }

    /**
     * Order updateStatusCancelShipment
     * calculate shipping service
     *
     * @param string $orderId //The orderId
     *
     * @return array $data
     */
    public function updateStatusCancelShipment($orderId)
    {
        return $this->getResource()->updateStatusCancelShipment($orderId);
    }

    /**
     * Order updateStatusOrder
     * calculate shipping service
     *
     * @param string $idOrder    //The idOrder
     * @param string $idshipment //The idshipment
     *
     * @return array $data
     */
    public function updateStatusOrder($idOrder, $idshipment)
    {
        return $this->getResource()->updateStatusOrder($idOrder, $idshipment);
    }

    /**
     * Order CheckParentItem
     * calculate shipping service
     *
     * @param string $itemId //The itemId
     *
     * @return array $data
     */
    public function checkParentItem($itemId)
    {
        return $this->getResource()->checkParentItem($itemId);
    }

    /**
     * Order getStateMagento
     * calculate shipping service
     *
     * @return array $data
     */
    public function getStateMagento()
    {
        return $this->getResource()->getStateMagento();
    }

    /**
     * Order getStateMagentoByStateCode
     * calculate shipping service
     *
     * @param string $stateCode //The stateCode
     *
     * @return array $data
     */
    public function getStateMagentoByStateCode($stateCode)
    {
        return $this->getResource()->getStateMagentoByStateCode($stateCode);
    }

    /**
     * Order getIdMagento
     * calculate shipping service
     *
     * @param string $idOrder //The idOrder
     *
     * @return array $data
     */
    public function getIdMagento($idOrder)
    {
        return $this->getResource()->getIdMagento($idOrder);
    }

    /**
     * Order getCustomerInfor
     *
     * @param string $salesOrderId //The salesOrderId
     *
     * @return array $data
     */
	public function getCustomerInfor($salesOrderId)
	{
		return $this->getResource()->getCustomerInfor($salesOrderId);
	}
}
