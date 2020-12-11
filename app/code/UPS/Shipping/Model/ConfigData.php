<?php
/**
 * ConfigData file
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
 * ConfigData class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class ConfigData extends \Magento\Framework\Model\AbstractModel
{
    /**
     * ConfigData _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('UPS\Shipping\Model\ResourceModel\ConfigData');
    }

    /**
     * ConfigData getConfigData
     *
     * @return array $data
     */
    public function getConfigData()
    {
        return $this->getResource()->getConfigData();
    }

    /**
     * ConfigData saveConfig
     *
     * @param string $value //The value
     *
     * @return array $data
     */
    public function updateConfig($value)
    {
        $this->getResource()->updateConfig($value);
    }

    /**
     * ConfigData updateInterfaceLocale
     *
     * @param string $value     //The value
     * @param string $userAdmin //The userAdmin
     *
     * @return array $data
     */
    public function updateInterfaceLocale($value, $userAdmin)
    {
        $this->getResource()->updateInterfaceLocale($value, $userAdmin);
    }
}
