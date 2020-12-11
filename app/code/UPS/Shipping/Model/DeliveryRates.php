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
namespace UPS\Shipping\Model;
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
class DeliveryRates extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = \UPS\Shipping\Helper\Config::UPS_SHIPPING_DELIVERY;

    protected $cacheTag = \UPS\Shipping\Helper\Config::UPS_SHIPPING_DELIVERY;
    protected $eventPrefix = \UPS\Shipping\Helper\Config::UPS_SHIPPING_DELIVERY;

    /**
     * DeliveryRates _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('UPS\Shipping\Model\ResourceModel\DeliveryRates');
    }

    /**
     * DeliveryRates updateSelectedService
     * function get list service
     *
     * @param string $service_type //The service_type
     *
     * @return array $data
     */
    public function getListService($service_type)
    {
        return $this->getResource()->getListService($service_type);
    }

    /**
     * DeliveryRates updateSelectedService
     * function get data delivery rates
     *
     * @return array $data
     */
    public function getDataDeliveryRates()
    {
        return $this->getResource()->getDataDeliveryRates();
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
        return $this-> getResource()->getListDeliveryRatesByServiceId($service_id);
    }

    /**
     * DeliveryRates getOneDeliveryRates
     * function get one delivery rates
     *
     * @param string $id //The id
     *
     * @return array $data
     */
    public function getOneDeliveryRates($id)
    {
        return $this-> getResource()->getOneDeliveryRates($id);
    }

    /**
     * DeliveryRates updateSelectedService
     * function remove all delivery rates
     *
     * @return array $data
     */
    public function removeAllDeliveryRates()
    {
        $this->getResource()->removeAllDeliveryRates();
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
        $this->getResource()->saveDeliveryRates($data);
    }

    /**
     * DeliveryRates deleteDeliveryRates
     *
     * @param string $service_id //The service_id
     *
     * @return array $data
     */
    public function deleteDeliveryRates($service_id)
    {
        return $this->getResource()->deleteDeliveryRates($service_id);
    }

    /**
     * DeliveryRates getListCheckedDeliveryRates
     *
     * @return array $data
     */
    public function getListCheckedDeliveryRates()
    {
        return $this->getResource()->getListCheckedDeliveryRates();
    }
}
