<?php
/**
 * Package file
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
 * Package class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Package extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = \UPS\Shipping\Helper\Config::UPS_SHIPPING_PACKAGE;

    protected $cacheTag = \UPS\Shipping\Helper\Config::UPS_SHIPPING_PACKAGE;
    protected $eventPrefix = \UPS\Shipping\Helper\Config::UPS_SHIPPING_PACKAGE;

    /**
     * Package _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('UPS\Shipping\Model\ResourceModel\Package');
    }

    /**
     * Package savePackage
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function savePackage($data)
    {
        $this->getResource()->savePackage($data);
    }

    /**
     * Package updatePackage
     *
     * @param string $data       //The data
     * @param string $package_id //The package_id
     *
     * @return array $data
     */
    public function updatePackage($data, $package_id)
    {
        $this->getResource()->updatePackage($data, $package_id);
    }

    /**
     * Package deletePackage
     *
     * @param string $package_id //The package_id
     *
     * @return array $data
     */
    public function deletePackage($package_id)
    {
        $this->getResource()->deletePackage($package_id);
    }

    /**
     * Package getListPackage
     *
     * @return array $data
     */
    public function getListPackage()
    {
        return $this->getResource()->getListPackage();
    }

    /**
     * Package getOnePackage
     *
     * @param string $package_id //The package_id
     *
     * @return array $data
     */
    public function getOnePackage($package_id)
    {
        return $this->getResource()->getOnePackage($package_id);
    }

    /**
     * Package nameExits
     *
     * @param string $name_pkg //The name_pkg
     *
     * @return array $data
     */
    public function nameExits($name_pkg)
    {
        return $this->getResource()->nameExits($name_pkg);
    }

    /**
     * Package getNameExits
     *
     * @param string $name_pkg //The name_pkg
     *
     * @return array $data
     */
    public function getNameExits($name_pkg)
    {
        return $this->getResource()->getNameExits($name_pkg);
    }

    /**
     * Package nameExitsPopup
     *
     * @param string $name_pkgpopup //The name_pkgpopup
     * @param string $package_id    //The package_id
     *
     * @return array $data
     */
    public function nameExitsPopup($name_pkgpopup, $package_id)
    {
        return $this->getResource()->nameExitsPopup($name_pkgpopup, $package_id);
    }

    /**
     * Package getNameExitsPopup
     *
     * @param string $name_pkgpopup //The name_pkgpopup
     * @param string $package_id    //The package_id
     *
     * @return array $data
     */
    public function getNameExitsPopup($name_pkgpopup, $package_id)
    {
        return $this->getResource()->getNameExitsPopup($name_pkgpopup, $package_id);
    }

    /**
     * Package getListPackageShipment
     *
     * @return array $data
     */
    public function getListPackageShipment()
    {
        return $this->getResource()->getListPackageShipment();
    }

    /**
     * Package getPackageDefault
     *
     * @return array $data
     */
    public function getPackageDefault()
    {
        return $this->getResource()->getPackageDefault();
    }

    /**
     * Package getListPackageSelected
     *
     * @param string $IDPackage //The IDPackage
     *
     * @return array $data
     */
    public function getListPackageSelected($IDPackage)
    {
        return $this->getResource()->getListPackageSelected($IDPackage);
    }
}
