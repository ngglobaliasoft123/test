<?php
/**
 * RetryApi file
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
 * RetryApi class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class RetryApi extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * RetryApi __construct
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
     * RetryApi _construct
     *
     * @return array $data
     */
    protected function _construct()
    {
        $this->_init('ups_shipping_retry_api', 'id');
    }

    /**
     * RetryApi getRetryAPIAll
     *
     * @return array $data
     */
    public function getRetryAPIAll()
    {
        $select = $this->getConnection()->select()->from($this->getMainTable());
        //return $this->getConnection()->fetchAll($select);
        $retryAll = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $retryAll[] = $row;
        }
        return $retryAll;
    }

    /**
     * RetryApi getRetryAPIByKey
     *
     * @param string $key_api //The key_api
     *
     * @return array $data
     */
    public function getRetryAPIByKey($key_api)
    {
        $select = $this->getConnection()->select()->from($this->getMainTable())->where('key_api = ?', $key_api);
        //return $this->getConnection()->fetchAll($select);
        $retryAPIs = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $retryAPIs[] = $row;
        }
        return $retryAPIs;
    }

    /**
     * RetryApi saveReTryAPIFail
     * save retry api fail
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function saveReTryAPIFail($data)
    {
        $this->getConnection()->insert($this->getMainTable(), $data);
    }

    /**
     * RetryApi updateReTryAPIFail
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function updateReTryAPIFail($data)
    {
        $dataUpdate = [
            'count_retry' => $data['count_retry'] + 1
        ];
        return $this->getConnection()->update($this->getMainTable(), $dataUpdate, ['id_retry =?'=>$data['id_retry']]);
    }

    /**
     * RetryApi getAllListAccessorial
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function deleteReTryAPIFail($data)
    {
        $this->getConnection()->delete($this->getMainTable());
    }
}
