<?php
/**
 * Dimension file
 *
 * @category  UPS_Shipping
 * @Dimension   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Model\ResourceModel;
/**
 * Dimension class
 *
 * @category  UPS_Shipping
 * @Dimension UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Dimension extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Dimension __construct
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
     * Dimension _construct
     *
     * @return array $data
     */
    protected function _construct()
    {
        $this->_init('ups_shipping_product_dimension', 'package_id');
    }

    /**
     * Dimension saveDimension
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function saveDimension($data)
    {
        $this->truncateDimension();
        $countData = count($data);
        if ($countData > 0) {
            foreach ($data as $key=>$value) {
                $dataItem = [
                    'package_name' => $value['package_name'],
                    \UPS\Shipping\Helper\ConstantPackage::WEIGHT => $value['weight'],
                    \UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT => $value['unit_weight'],
                    \UPS\Shipping\Helper\ConstantPackage::LENGTH => $value['length'],
                    \UPS\Shipping\Helper\ConstantPackage::WIDTH => $value['width'],
                    \UPS\Shipping\Helper\ConstantPackage::HEIGHT => $value['height'],
                    \UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION => $value['unit_dimension'],
                ];
                $this->getConnection()->insert($this->getMainTable(), $dataItem);
            }
        }
    }

    /**
     * Dimension getAllListAccessorial
     *
     * @param string $data       //The data
     * @param string $dimension_id //The dimension_id
     *
     * @return array $data
     */
    public function updateDimension($data, $dimension_id)
    {
        return $this->getConnection()->update($this->getMainTable(), $data, ['package_id =?' => $dimension_id]);
    }

    /**
     * Dimension deleteDimension
     *
     * @param string $id //The id
     *
     * @return array $data
     */
    public function deleteDimension($id)
    {
        $getListDimension = $this->getListDimension();
        if (count($getListDimension) > 1) {
            return $this->getConnection()->delete($this->getMainTable(), ['package_id =?' => $id]);
        }
    }

    /**
     * Dimension truncatePackage
     *
     * @return array $data
     */
    public function truncateDimension()
    {
        $connection = $this->getConnection();
        $tableName = $this->getMainTable();
        $connection->truncateTable($tableName);
    }

    /**
     * Dimension getListDimension
     *
     * @return array $data
     */
    public function getListDimension()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->order(['package_id ASC']);
        $packages = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $packages[] = $row;
        }
        return $packages;
    }
}
