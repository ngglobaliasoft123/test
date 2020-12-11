<?php
/**
 * Account file
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
 * Account class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Account extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Account __construct
     * collect registration data
     *
     * @param string $context //The Context
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Account _construct
     *
     * @return array $data
     */
    protected function _construct()
    {
        $this->_init('ups_shipping_account', 'account_id');
    }

    /**
     * Account saveAccount
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function saveAccount($data)
    {
        $data['state_province_code'] = $data['StateProvinceCode'];
        unset($data['StateProvinceCode']);
        if (isset($data['ControlID'])) {
            unset($data['ControlID']);
        }
        $this->getConnection()->insert($this->getMainTable(), $data);
    }

    /**
     * Account getListAccount
     *
     * @return array $data
     */
    public function getListAccount()
    {
        $select = $this->getConnection()->select()->from($this->getMainTable());
        $accounts = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $accounts[] = $row;
        }
        return $accounts;
    }

    /**
     * Account getAccountDefault
     *
     * @return array $data
     */
    public function getAccountDefault()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('account_default = ?', '1');
        return $this->getConnection()->fetchRow($select);
    }

    /**
     * Account deleteAccount
     *
     * @param string $accountId //The accountId
     *
     * @return array $data
     */
    public function deleteAccount($accountId)
    {
        $arrayWhere = ['account_id =?' => $accountId, 'account_default =?' => 0];
        return $this->getConnection()->delete($this->getMainTable(), $arrayWhere);
    }

    /**
     * Account checkAccount
     *
     * @param string $ups_account_number //The ups_account_number
     *
     * @return array $data
     */
    public function checkAccount($ups_account_number)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('ups_account_number = ?', $ups_account_number);
        return $this->getConnection()->fetchRow($select);
    }

    /**
     * Account getInfoAccount
     *
     * @param string $id //The id
     *
     * @return array $data
     */
    public function getInfoAccount($id)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('account_id = ?', $id);
        return $this->getConnection()->fetchRow($select);
    }
}
