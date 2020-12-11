<?php
/**
 * Tracking file
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
 * Tracking class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Tracking extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Tracking __construct
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
     * Tracking _construct
     *
     * @return array $data
     */
    protected function _construct()
    {
        $this->_init('ups_shipping_tracking', 'id');
    }

    /**
     * Tracking deleteTracking
     *
     * @param string $shipmentNumber //The shipmentNumber
     *
     * @return array $data
     */
    public function deleteTracking($shipmentNumber)
    {
        return $this->getConnection()->delete($this->getMainTable(), ['shipment_number = ?' => $shipmentNumber]);
    }

    /**
     * Tracking deleteTrackingByShipmentNumber
     *
     * @param string $listShipmentNumber //The listShipmentNumber
     *
     * @return array $data
     */
    public function deleteTrackingByShipmentNumber($listShipmentNumber)
    {
        return $this->getConnection()->delete($this->getMainTable(), ['shipment_number IN(?)' => $listShipmentNumber]);
    }

    /**
     * Tracking getListTrackingNumberByShipmentNumber
     *
     * @param string $shipmentNumber //The shipmentNumber

     * @return array $data
     */
    public function getListTrackingNumberByShipmentNumber($shipmentNumber)
    {
        $select = $this->getConnection()->select()
            ->from(['s' => $this->getMainTable()])
            ->where('s.shipment_number = ?', $shipmentNumber);
        //return $this->getConnection()->fetchAll($select);
        $listTrackings = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listTrackings[] = $row;
        }
        return $listTrackings;
    }
}
