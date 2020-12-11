<?php
/**
 * Accessorial file
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
 * Accessorial class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Accessorial extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Accessorial __construct
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
     * Accessorial _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('ups_shipping_accessorial', 'id');
    }

    /**
     * Accessorial getAllListAccessorial
     *
     * @return array $data
     */
    public function getAllListAccessorial()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable());
        //return $this->getConnection()->fetchAll($select);
        $accessorialAll = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $accessorialAll[] = $row;
        }
        return $accessorialAll;
    }

    /**
     * Accessorial getListAccessorial
     *
     * @return array $data
     */
    public function getListAccessorial()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('show_config = ? ', 1);
        //return $this->getConnection()->fetchAll($select);
        $accessorials = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $accessorials[] = $row;
        }
        return $accessorials;
    }

    /**
     * Accessorial getListAccessorialActive
     *
     * @return array $data
     */
    public function getListAccessorialActive()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('show_config = ? ', 1)
            ->where('show_shipping = ? ', 1);
        //return $this->getConnection()->fetchAll($select);
        $accessorialActives = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $accessorialActives[] = $row;
        }
        return $accessorialActives;
    }

    /**
     * Accessorial getListAccessorialActiveAndSatDeli
     *
     * @return array $data
     */
    public function getListAccessorialActiveAndSatDeli()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('show_config = ? ANd show_shipping = ? ', 1)
            ->orWhere('accessorial_key = ? ', 'UPS_ACSRL_STATURDAY_DELIVERY');

        $accessorialActives = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $accessorialActives[] = $row;
        }
        return $accessorialActives;
    }

    /**
     * Accessorial getAllListAccessorial
     *
     * @param string $id           //The id
     * @param string $showShipping //The showShipping
     *
     * @return array $data
     */
    public function updateAccessorial($id, $showShipping)
    {
        $dataUpdate = ['show_shipping' => $showShipping];
        return $this->getConnection()->update($this->getMainTable(), $dataUpdate, ['id =?' => $id]);
    }
}
