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
namespace UPS\Shipping\Model;
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
class Savelog extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Savelog _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('UPS\Shipping\Model\ResourceModel\Savelog');
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
        $this->getResource()->saveLogAPI($method, $url, $request, $reponse);
    }

    /**
     * Savelog saveLogRetry
     * save log retry
     *
     * @param string $nameFunction //The nameFunction
     * @param string $request      //The request
     * @param string $reponse      //The reponse
     *
     * @return array $data
     */
    public function saveLogRetry($nameFunction, $request, $reponse)
    {
        $this->getResource()->saveLogRetry($nameFunction, $request, $reponse);
    }

    /**
     * Savelog updateCountRetry
     * update count retry
     *
     * @param string $id //The id
     *
     * @return array $data
     */
    public function updateCountRetry($id)
    {
        $this->getResource()->updateCountRetry($id);
    }

    /**
     * Savelog updateSelectedService
     *
     * @param string $id //The id
     *
     * @return array $data
     */
    public function removeRetry($id)
    {
        $this->getResource()->removeRetry($id);
    }

    /**
     * Savelog getListRetry
     *
     * @param string $limit //The limit
     *
     * @return array $data
     */
    public function getListRetry($limit)
    {
        return $this->getResource()->getListRetry($limit);
    }

    /**
     * Savelog updateSelectedService
     *
     * @param string $day //The day
     *
     * @return array $data
     */
    public function removeRetryOutOfdate($day)
    {
        return $this->getResource()->removeRetryOutOfdate($day);
    }
}
