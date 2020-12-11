<?php
/**
 * Dimension file
 *
 * @category  UPS_Shipping
 * @dimension UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Model;
/**
 * Dimension class
 *
 * @category  UPS_Shipping
 * @dimension UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Dimension extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = \UPS\Shipping\Helper\Config::UPS_SHIPPING_PACKAGE;

    protected $cacheTag = \UPS\Shipping\Helper\Config::UPS_SHIPPING_PACKAGE;
    protected $eventPrefix = \UPS\Shipping\Helper\Config::UPS_SHIPPING_PACKAGE;

    /**
     * Dimension _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('UPS\Shipping\Model\ResourceModel\Dimension');
    }

    /**
     * Dimension saveDimension
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function saveDimension($data)
    {
        $this->getResource()->saveDimension($data);
    }

    /**
     * Dimension updateDimension
     *
     * @param string $data       //The data
     * @param string $package_id //The package_id
     *
     * @return array $data
     */
    public function updateDimension($data, $package_id)
    {
        $this->getResource()->updateDimension($data, $package_id);
    }

    /**
     * Dimension deleteDimension
     *
     * @param string $package_id //The package_id
     *
     * @return array $data
     */
    public function deleteDimension($package_id)
    {
        $this->getResource()->deleteDimension($package_id);
    }

    /**
     * Dimension getListDimension
     *
     * @return array $data
     */
    public function getListDimension()
    {
        return $this->getResource()->getListDimension();
    }
}
