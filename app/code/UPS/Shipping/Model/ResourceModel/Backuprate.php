<?php
/**
 * Backuprate file
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
 * Backuprate class
 *
 * @category  UPS_Shipping
 * @Dimension   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Backuprate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Backuprate __construct
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
     * Backuprate _construct
     *
     * @return array $data
     */
    protected function _construct()
    {
        $this->_init('ups_shipping_fallback_rates', 'id');
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
        $this->truncateBackuprate();
        $countData = count($data);
        if ($countData > 0) {
            foreach ($data as $key=>$value) {
                $dataItem = [
                    'service_type' => 'ADD',
                    'service_id' => $value['service_id'],
                    'fallback_rate' => $value['rate'],
                ];
                $this->getConnection()->insert($this->getMainTable(), $dataItem);
            }
        }
    }

    /**
     * Backuprate updateBackuprate
     *
     * @param string $data          //The data
     * @param string $backuprate_id //The backuprate_id
     *
     * @return array $data
     */
    public function updateBackuprate($data, $backuprate_id)
    {
        return $this->getConnection()->update($this->getMainTable(), $data, ['id =?' => $backuprate_id]);
    }

    /**
     * Backuprate deleteBackuprate
     *
     * @param string $id //The Dimension_id
     *
     * @return array $data
     */
    public function deleteBackuprate($id)
    {
        $getListBackuprate = $this->getListBackuprate();
        if (count($getListBackuprate) > 1) {
            return $this->getConnection()->delete($this->getMainTable(), ['id =?' => $id]);
        }
    }

    /**
     * Backuprate truncatePackage
     *
     * @return array $data
     */
    public function truncateBackuprate()
    {
        $connection = $this->getConnection();
        $tableName = $this->getMainTable();
        $connection->truncateTable($tableName);
    }

    /**
     * Backuprate getListBackuprate
     *
     * @return array $data
     */
    public function getListBackuprate()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->order(['id ASC']);
        //return $this->getConnection()->fetchAll($select);
        $packages = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $packages[] = $row;
        }
        return $packages;
    }
}
