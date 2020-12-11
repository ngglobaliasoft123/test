<?php
/**
 * License file
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
 * License class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class License extends \Magento\Framework\Model\AbstractModel
{

    const CACHE_TAG = \UPS\Shipping\Helper\Config::UPS_SHIPPING_LICENSE;

    protected $cacheTag = \UPS\Shipping\Helper\Config::UPS_SHIPPING_LICENSE;
    protected $eventPrefix = \UPS\Shipping\Helper\Config::UPS_SHIPPING_LICENSE;

    /**
     * License _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('UPS\Shipping\Model\ResourceModel\License');
    }

    /**
     * License getLicenseDefault
     *
     * @return array $data
     */
    public function getLicenseDefault()
    {
        return $this->getResource()->getLicenseDefault();
    }

    /**
     * License insertAccessLicenseText
     * function insert access license test
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function insertAccessLicenseText($data)
    {
        return $this->getResource()->insertAccessLicenseText($data);
    }

    /**
     * License updateAccessLicenseText
     * fuction update access license text
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function updateAccessLicenseText($data)
    {
        return $this->getResource()->updateAccessLicenseText($data);
    }

    /**
     * License updateAccount
     *
     * @param string $username //The username
     * @param string $password //The password
     *
     * @return array $data
     */
    public function updateAccount($username, $password)
    {
        return $this->getResource()->updateAccount($username, $password);
    }

    /**
     * License updateLicenseNumber
     *
     * @param string $licenseNumber //The licenseNumber
     *
     * @return array $data
     */
    public function updateLicenseNumber($licenseNumber)
    {
        return $this->getResource()->updateLicenseNumber($licenseNumber);
    }

    /**
     * License checkIsset
     *
     * @return array $data
     */
    public function checkIsset()
    {
        return $this->getResource()->checkIsset();
    }
}
