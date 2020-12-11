<?php
/**
 * Savelog file
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
 * Savelog class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Savelog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const COUNT_RETRY = 'count_retry';

    /**
     * Savelog __construct
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
     * Savelog _construct
     *
     * @return array $data
     */
    protected function _construct()
    {
        $this->_init('ups_shipping_retry_api', 'id');
    }

    /**
     * Savelog saveLogAPI
     *
     * @param string $method  //The method
     * @param string $url     //The url
     * @param string $request //The request
     * @param string $reponse //The reponse
     *
     * @return array $data
     */
    public function saveLogAPI($method, $url, $request, $reponse)
    {
        $data = [
            'method' => $method,
            'full_uri' => $url,
            'request' => $request,
            'response' => $reponse,
            'time_request' => date("Y-m-d H:i:s")
        ];
        $this->getConnection()->insert($this->getTable('ups_shipping_logs_api'), $data);
        return $this->getConnection()->lastInsertId();
    }

    /**
     * Savelog saveLogRetry
     *
     * @param string $method   //The method
     * @param string $request  //The request
     * @param string $response //The response
     *
     * @return array $data
     */
    public function saveLogRetry($method, $request, $response)
    {
        $data = [
            'method' => $method,
            'datarequest' => $request,
            'response' => $response,
            self::COUNT_RETRY => 0,
            'date_created' => date("Y-m-d H:i:s")
        ];
        $this->getConnection()->insert($this->getMainTable(), $data);
        return $this->getConnection()->lastInsertId();
    }

    /**
     * Savelog updateCountRetry
     *
     * @param integer $id //The id
     *
     * @return array $data
     */
    public function updateCountRetry($id)
    {
        $count_retry = $this->getRetryById($id);
        if (!empty($count_retry)) {
            $this->getConnection()->update($this->getMainTable(), [self::COUNT_RETRY => 1 + $count_retry[0][self::COUNT_RETRY]], ['id = ?' => $id]);
        }
    }

    /**
     * Savelog removeRetry
     *
     * @param integer $id //The id
     *
     * @return array $data
     */
    public function removeRetry($id)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            ['id = ?' => $id]
        );
    }

    /**
     * Savelog getListRetry
     *
     * @param integer $limit //The limit
     *
     * @return array $data
     */
    public function getListRetry($limit)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('ups_shipping_retry_api'))
            ->order(['count_retry ASC'])
            ->limitPage(0, $limit);
        //return $this->getConnection()->fetchAll($select);
        $listRetrys = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listRetrys[] = $row;
        }
        return $listRetrys;
    }

    /**
     * Savelog getRetryById
     *
     * @param integer $id //The id
     *
     * @return array $data
     */
    public function getRetryById($id)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('ups_shipping_retry_api'))
            ->where('id = ?', $id);
        //return $this->getConnection()->fetchAll($select);
        $listRetrys = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listRetrys[] = $row;
        }
        return $listRetrys;
    }

    /**
     * Savelog removeRetryOutOfdate
     *
     * @param integer $day //The day
     *
     * @return array $data
     */
    public function removeRetryOutOfdate($day)
    {
        $date = date("Y-m-d H:i:s");
        $date = strtotime($date);
        $date = strtotime("-". $day ." day", $date);
        $date = date('Y-m-d H:i:s', $date);
        $this->getConnection()->delete(
            $this->getMainTable(),
            ['date_created < ?' => $date]
        );
    }
}
