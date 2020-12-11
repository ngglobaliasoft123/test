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
namespace UPS\Shipping\Model;
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
class Tracking extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = \UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_TRACKING;

    protected $cacheTag = \UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_TRACKING;
    protected $eventPrefix = \UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_TRACKING;

    /**
     * Tracking _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('UPS\Shipping\Model\ResourceModel\Tracking');
    }

    /**
     * Tracking deleteTracking
     * delete tracking
     *
     * @param string $shipmentNumber //The shipmentNumber
     *
     * @return array $data
     */
    public function deleteTracking($shipmentNumber)
    {
        return $this->getResource()->deleteTracking($shipmentNumber);
    }

    /**
     * Tracking deleteTrackingByShipmentNumber
     * delete tracking by shipment number
     *
     * @param string $listShipmentNumber //The listShipmentNumber
     *
     * @return array $data
     */
    public function deleteTrackingByShipmentNumber($listShipmentNumber)
    {
        return $this->getResource()->deleteTrackingByShipmentNumber($listShipmentNumber);
    }

    /**
     * Tracking getListTrackingNumberByShipmentNumber
     *
     * @param string $shipmentNumber //The shipmentNumber
     *
     * @return array $data
     */
    public function getListTrackingNumberByShipmentNumber($shipmentNumber)
    {
        return $this->getResource()->getListTrackingNumberByShipmentNumber($shipmentNumber);
    }
}
