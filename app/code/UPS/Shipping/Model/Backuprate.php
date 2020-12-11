<?php
/**
 * Backuprate file
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
 * Backuprate class
 *
 * @category  UPS_Shipping
 * @dimension UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Backuprate extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = \UPS\Shipping\Helper\Config::UPS_SHIPPING_PACKAGE;

    protected $cacheTag = \UPS\Shipping\Helper\Config::UPS_SHIPPING_PACKAGE;
    protected $eventPrefix = \UPS\Shipping\Helper\Config::UPS_SHIPPING_PACKAGE;

    /**
     * Backuprate _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('UPS\Shipping\Model\ResourceModel\Backuprate');
    }

    /**
     * Backuprate saveBackuprate
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function saveBackuprate($data)
    {
        $this->getResource()->saveBackuprate($data);
    }

    /**
     * Backuprate updateBackuprate
     *
     * @param string $data       //The data
     * @param string $package_id //The package_id
     *
     * @return array $data
     */
    public function updateBackuprate($data, $package_id)
    {
        $this->getResource()->updateBackuprate($data, $package_id);
    }

    /**
     * Backuprate deleteBackuprate
     *
     * @param string $package_id //The package_id
     *
     * @return array $data
     */
    public function deleteBackuprate($package_id)
    {
        $this->getResource()->deleteBackuprate($package_id);
    }

    /**
     * Backuprate getListBackuprate
     *
     * @return array $data
     */
    public function getListBackuprate()
    {
        return $this->getResource()->getListBackuprate();
    }
}
