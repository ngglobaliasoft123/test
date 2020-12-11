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
namespace UPS\Shipping\Model\ResourceModel;
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
class License extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
     * License _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('ups_shipping_license', 'id');
    }

    /**
     * Account getLicenseDefault
     *
     * @return array $data
     */
    public function getLicenseDefault()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('id = ?', '1');
        return $this->getConnection()->fetchRow($select);
    }

    /**
     * License insertAccessLicenseText
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function insertAccessLicenseText($data)
    {
        $dataInsert = ["AccessLicenseText" => $data];
        return $this->getConnection()->insert($this->getMainTable(), $dataInsert);
    }

    /**
     * License updateAccessLicenseText
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function updateAccessLicenseText($data)
    {
        $dataUpdate = ["AccessLicenseText" => $data];
        return $this->getConnection()->update($this->getMainTable(), $dataUpdate, ['id = 1']);
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
        $dataUpdate = [ "Username" => $username, "Password" => $password];
        return $this->getConnection()->update($this->getMainTable(), $dataUpdate, ['id = 1']);
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
        $dataUpdate = [ "AccessLicenseNumber" => $licenseNumber ];
        return $this->getConnection()->update($this->getMainTable(), $dataUpdate, ['id = ?' => 1]);
    }

    /**
     * License checkIsset
     *
     * @return array $data
     */
    public function checkIsset()
    {
        $select = $this->getConnection()->select() ->from($this->getMainTable());
        return $this->getConnection()->fetchRow($select);
    }
}
