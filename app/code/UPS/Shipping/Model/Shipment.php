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
namespace UPS\Shipping\Model;
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
class Shipment extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = \UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_SHIPMENT;

    protected $cacheTag = \UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_SHIPMENT;
    protected $eventPrefix = \UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_SHIPMENT;

    /**
     * Shipment _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('UPS\Shipping\Model\ResourceModel\Shipment');
    }

    /**
     * Shipment getShipmentDetail
     *
     * @param string $trackingId //The trackingId
     *
     * @return array $data
     */
    public function getShipmentDetail($trackingId)
    {
        return $this->getResource()->getShipmentDetail($trackingId);
    }

    /**
     * Shipment getAllShipments
     *
     * @return array $data
     */
    public function getAllShipments()
    {
        return $this->getResource()->getAllShipments();
    }

    /**
     * Shipment getNumberPages
     *
     * @param string $numberItemOnPage //The numberItemOnPage
     *
     * @return array $data
     */
    public function getNumberPages($numberItemOnPage)
    {
        return $this->getResource()->getNumberPages($numberItemOnPage);
    }

    /**
     * Shipment getListShipments
     *
     * @param string $numberItemOnPage //The numberItemOnPage
     * @param string $offset           //The offset
     * @param string $request          //The request
     *
     * @return array $data
     */
    public function getListShipments($numberItemOnPage, $offset, $request)
    {
        return $this->getResource()->getListShipments($numberItemOnPage, $offset, $request);
    }

    /**
     * Shipment getProductDetail
     *
     * @param string $shipment_id //The shipment_id
     * @param string $order_id    //The order_id
     *
     * @return array $data
     */
    public function getProductDetail($shipment_id, $order_id)
    {
        return $this->getResource()->getProductDetail($shipment_id, $order_id);
    }

    /**
     * Shipment getExportShipmentData
     *
     * @param string $listTrack //The listTrack
     *
     * @return array $data
     */
    public function getExportShipmentData($listTrack)
    {
        return $this->getResource()->getExportShipmentData($listTrack);
    }

    /**
     * Shipment deleteRowShipment
     *
     * @param string $shipmentNumber //The shipmentNumber
     *
     * @return array $data
     */
    public function deleteRowShipment($shipmentNumber)
    {
        return $this->getResource()->deleteRowShipment($shipmentNumber);
    }

    /**
     * Shipment getListShipmentsOver
     *
     * @param string $days //The days
     *
     * @return array $data
     */
    public function getListShipmentsOver($days)
    {
        return $this->getResource()->getListShipmentsOver($days);
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
        return $this->getResource()->createShipment($shipmentNumber, $shippingService, $accessorial, $now, $shippingFee, $COD, $apId, $name, $state, $phone, $address1, $address2, $address3, $city, $postcode, $country, $email, $OrderValue);
    }

    /**
     * Shipment createTracking
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
        return $this->getResource()->createTracking($shipmentNumber, $trackingNumber, $package, $orderid);
    }

    /**
     * Shipment getIdShipmentMagentoInsert
     *
     * @return array $data
     */
    public function getIdShipmentMagentoInsert()
    {
        return $this->getResource()->getIdShipmentMagentoInsert();
    }

    /**
     * Shipment updateStatus
     *
     * @param string $shipmentId //The shipmentId
     * @param string $Status     //The Status
     *
     * @return array $data
     */
    public function updateStatus($shipmentId, $Status)
    {
        return $this->getResource()->updateStatus($shipmentId, $Status);
    }

    /**
     * Shipment removeShipment
     *
     * @param string $day //The day
     *
     * @return array $data
     */
    public function removeShipment($day)
    {
        $this->getResource()->removeShipment($day);
    }
}
