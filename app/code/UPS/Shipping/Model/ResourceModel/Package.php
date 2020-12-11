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
namespace UPS\Shipping\Model\ResourceModel;
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
class Package extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Package __construct
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
     * Package _construct
     *
     * @return array $data
     */
    protected function _construct()
    {
        $this->_init('ups_shipping_package_default', 'package_id');
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
        $this->truncatePackage();
        $countData = count($data);
        if ($countData > 0) {
            foreach ($data as $key=>$value) {
                $dataItem = [
                    \UPS\Shipping\Helper\ConstantPackage::PACKAGE_NAME => 'package default_' . $key,
                    \UPS\Shipping\Helper\ConstantPackage::WEIGHT => $value['weight'],
                    \UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT => $value['unit_weight'],
                    \UPS\Shipping\Helper\ConstantPackage::LENGTH => $value['length'],
                    \UPS\Shipping\Helper\ConstantPackage::WIDTH => $value['width'],
                    \UPS\Shipping\Helper\ConstantPackage::HEIGHT => $value['height'],
                    \UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION => $value['unit_dimension'],
                    \UPS\Shipping\Helper\ConstantPackage::NUMBERPACKAGE => $value['number_of_item'],
                ];
                $this->getConnection()->insert($this->getMainTable(), $dataItem);
            }
        }
    }

    /**
     * Package getAllListAccessorial
     *
     * @param string $data       //The data
     * @param string $package_id //The package_id
     *
     * @return array $data
     */
    public function updatePackage($data, $package_id)
    {
        return $this->getConnection()->update($this->getMainTable(), $data, ['package_id =?' => $package_id]);
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
        $getListPackage = $this->getListPackage();
        if (count($getListPackage) > 1) {
            return $this->getConnection()->delete($this->getMainTable(), ['package_id =?' => $package_id]);
        }
    }

    /**
     * Package truncatePackage
     *
     * @return array $data
     */
    public function truncatePackage()
    {
        $connection = $this->getConnection();
        $tableName = $this->getMainTable();
        $connection->truncateTable($tableName);
    }

    /**
     * Package getListPackage
     *
     * @return array $data
     */
    public function getListPackage()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->order(['package_id ASC']);
        //return $this->getConnection()->fetchAll($select);
        $packages = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $packages[] = $row;
        }
        return $packages;
    }

    /**
     * Package getAllListAccessorial
     *
     * @param string $package_id //The package_id
     *
     * @return array $data
     */
    public function getOnePackage($package_id)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('package_id = ?', $package_id);
        return $this->getConnection()->fetchRow($select);
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
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), ['COUNT(*)'])
            ->where('package_name = ? ', $name_pkg);
        return $this->getConnection()->fetchOne($select);
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
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), ['package_name'])
            ->where('package_name = ? ', $name_pkg);
        //return $this->getConnection()->fetchAll($select);
        $existNames = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $existNames[] = $row;
        }
        return $existNames;
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
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('package_name = ?', $name_pkgpopup)
            ->where('package_id != ?', $package_id);
        return $this->getConnection()->fetchOne($select);
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
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), ['package_name'])
            ->where('package_name = ?', $name_pkgpopup)
            ->where('package_id != ?', $package_id);
        //return $this->getConnection()->fetchAll($select);
        $existNameExitsPopup = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $existNameExitsPopup[] = $row;
        }
        return $existNameExitsPopup;
    }

    /**
     * Package getListPackageShipment
     *
     * @return array $data
     */
    public function getListPackageShipment()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable());
        //return $this->getConnection()->fetchAll($select);
        $packageShipments = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $packageShipments[] = $row;
        }
        return $packageShipments;
    }

    /**
     * Package getPackageDefault
     *
     * @return array $data
     */
    public function getPackageDefault()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->limitPage(0, 1);
        return $this->getConnection()->fetchRow($select);
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
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('package_id = ?', $IDPackage);
        return $this->getConnection()->fetchRow($select);
    }
}
