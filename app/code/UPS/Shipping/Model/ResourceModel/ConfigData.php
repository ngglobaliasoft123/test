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
namespace UPS\Shipping\Model\ResourceModel;
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
class ConfigData extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * License __construct
     * collect registration data
     *
     * @param string $context //The context
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * ConfigData _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('core_config_data', 'id');
    }

    /**
     * ConfigData getConfigData
     *
     * @return array $data
     */
    public function getConfigData()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('path = ?', \UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN);
        return $this->getConnection()->fetchRow($select);
    }

    /**
     * ConfigData updateConfig
     *
     * @param string $value //The value
     *
     * @return array $data
     */
    public function updateConfig($value)
    {
        $dataUpdate = [ "value" => $value];
        return $this->getConnection()->update($this->getMainTable(), $dataUpdate, ['path =?' => \UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN]);
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
        $dataUpdate = ["interface_locale" => $value];
        $condition = ['user_id = ?' => (int)$userAdmin];
        return $this->getConnection()->update('admin_user', $dataUpdate, $condition);
    }
}
