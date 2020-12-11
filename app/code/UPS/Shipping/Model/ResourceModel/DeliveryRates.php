<?php
/**
 * DeliveryRates file
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
 * DeliveryRates class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class DeliveryRates extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * DeliveryRates __construct
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
     * DeliveryRates _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('ups_shipping_delivery_rates', 'id');
    }

    /**
     * DeliveryRates getDataDeliveryRates
     *
     * @return array $data
     */
    public function getDataDeliveryRates()
    {
        $select = $this->getConnection()->select()->from($this->getMainTable());
        //return $this->getConnection()->fetchAll($select);
        $listDeliveryRates = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listDeliveryRates[] = $row;
        }
        return $listDeliveryRates;
    }

    /**
     * DeliveryRates getOneDeliveryRates
     *
     * @param string $id //The id
     *
     * @return array $data
     */
    public function getOneDeliveryRates($id)
    {
        $select = $this->getConnection()->select()->from($this->getMainTable())->where('id = ?', $id);
        return $this->getConnection()->fetchRow($select);
    }

    /**
     * DeliveryRates getListDeliveryRatesByServiceId
     *
     * @param string $service_id //The service_id
     *
     * @return array $data
     */
    public function getListDeliveryRatesByServiceId($service_id)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('service_id = ?', $service_id);
        //return $this->getConnection()->fetchAll($select);
        $listDeliveryRateIds = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listDeliveryRateIds[] = $row;
        }
        return $listDeliveryRateIds;
    }

    /**
     * DeliveryRates saveDeliveryRates
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function saveDeliveryRates($data)
    {
        $this->getConnection()->insert($this->getMainTable(), $data);
    }

    /**
     * DeliveryRates removeAllDeliveryRates
     *
     * @return array $data
     */
    public function removeAllDeliveryRates()
    {
        $this->getConnection()->delete($this->getMainTable());
    }

    /**
     * DeliveryRates deleteDeliveryRates
     *
     * @param string $service_id //The service_id
     *
     * @return boolean
     */
    public function deleteDeliveryRates($service_id)
    {
        return $this->getConnection()->delete($this->getMainTable(), ['service_id = ?' => $service_id]);
    }

    /**
     * DeliveryRates getListCheckedDeliveryRates
     *
     * @return array $data
     */
    public function getListCheckedDeliveryRates()
    {
        $shippingTable = $this->getTable('ups_shipping_services');
        $select = $this->getConnection()->select()
            ->from(['dr' => $this->getMainTable()])
            ->join(['ss' => $shippingTable], 'dr.service_id = ss.id', ['id as idservice'])
            ->where('ss.service_selected = 1');
        //return $this->getConnection()->fetchAll($select);
        $listCheckedDeliveryRates = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listCheckedDeliveryRates[] = $row;
        }
        return $listCheckedDeliveryRates;
    }
}
