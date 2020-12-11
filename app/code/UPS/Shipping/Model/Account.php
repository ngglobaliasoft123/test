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
namespace UPS\Shipping\Model;
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
class Account extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = \UPS\Shipping\Helper\Config::UPS_SHIPPING_ACCOUNT;

    protected $cacheTag = \UPS\Shipping\Helper\Config::UPS_SHIPPING_ACCOUNT;
    protected $eventPrefix = \UPS\Shipping\Helper\Config::UPS_SHIPPING_ACCOUNT;

    /**
     * Account _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('UPS\Shipping\Model\ResourceModel\Account');
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
        $this->getResource()->saveAccount($data);
    }

    /**
     * Account getListAccount
     *
     * @return array $data
     */
    public function getListAccount()
    {
        return $this->getResource()->getListAccount();
    }

    /**
     * Account updateSelectedService
     *
     * @return array $data
     */
    public function getAccountDefault()
    {
        return $this->getResource()->getAccountDefault();
    }

    /**
     * Account updateSelectedService
     *
     * @param string $accountId //The accountId
     *
     * @return array $data
     */
    public function deleteAccount($accountId)
    {
        return $this->getResource()->deleteAccount($accountId);
    }

    /**
     * Account updateSelectedService
     *
     * @param string $ups_account_number //The ups_account_number
     *
     * @return array $data
     */
    public function checkAccount($ups_account_number)
    {
        return $this->getResource()->checkAccount($ups_account_number);
    }

    /**
     * Account updateSelectedService
     *
     * @param string $id //The id
     *
     * @return array $data
     */
    public function getInfoAccount($id)
    {
        return $this->getResource()->getInfoAccount($id);
    }
}
