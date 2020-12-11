<?php
/**
 * Service file
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
 * Service class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Service extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = \UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_SERVICE;

    protected $cacheTag = \UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_SERVICE;
    protected $eventPrefix = \UPS\Shipping\Helper\ConstantModel::UPS_SHIPPING_SERVICE;

    /**
     * Service _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('UPS\Shipping\Model\ResourceModel\Service');
    }

    /**
     * Service getAllListService
     * get all list service
     *
     * @return array $data
     */
    public function getAllListService()
    {
        return $this->getResource()->getAllListService();
    }

    /**
     * Service getShippingServiceById
     * get shipping service by ID
     *
     * @param string $serviceId //The serviceId
     *
     * @return array $data
     */
    public function getShippingServiceById($serviceId)
    {
        return $this->getResource()->getShippingServiceById($serviceId);
    }

    /**
     * Service getListService
     * get list service
     *
     * @param string $serviceType //The serviceType
     *
     * @return array $data
     */
    public function getListService($countryCode, $serviceType)
    {
        return $this->getResource()->getListService($countryCode, $serviceType);
    }

    /**
     * Service updateSelectedService
     * get list not selected
     *
     * @param string $serviceId //The serviceId
     * @param string $status    //The status
     *
     * @return array $data
     */
    public function updateSelectedService($serviceId, $status)
    {
        return $this->getResource()->updateSelectedService($serviceId, $status);
    }

    /**
     * Service getListNotSelected
     * get list not selected
     *
     * @param string $selected //The selected
     *
     * @return array $data
     */
    public function getListNotSelected($selected = '0')
    {
        return $this->getResource()->getListNotSelected($selected = '0');
    }

    /**
     * Service getSelectedServices
     * get list not selected
     *
     * @param string $serviceType //The serviceType
     * @param string $countryCode // Country code
     *
     * @return array $data
     */
    public function getSelectedServices($serviceType, $countryCode = '')
    {
        return $this->getResource()->getSelectedServices($serviceType, $countryCode);
    }

    /**
     * Service getSelectedCountryServices
     * get list not selected
     *
     * @param string $serviceType //The serviceType
     * @param string $country     //The country
     *
     * @return array $data
     */
    public function getSelectedCountryServices($serviceType, $country)
    {
        return $this->getResource()->getSelectedCountryServices($serviceType, $country);
    }

    /**
     * Service getServicesById
     * get list not selected
     *
     * @param string $serviceId //The serviceId
     *
     * @return array $data
     */
    public function getServicesById($serviceId)
    {
        return $this->getResource()->getServicesById($serviceId);
    }
}
