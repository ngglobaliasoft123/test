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
namespace UPS\Shipping\Model;
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
class RetryApi extends \Magento\Framework\Model\AbstractModel
{
    /**
     * RetryApi _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('UPS\Shipping\Model\ResourceModel\RetryApi');
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
        return $this->getResource()->getRetryAPIByKey($key_api);
    }

    /**
     * RetryApi updateReTryAPIFail
     * update retry API Fail
     *
     * @param string $dataUpdate //The dataUpdate
     *
     * @return array $data
     */
    public function updateReTryAPIFail($dataUpdate)
    {
        return $this->getResource()->updateReTryAPIFail($dataUpdate);
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
        $retryApi = $this->getRetryAPIByKey($data['key_api']);
        if (!empty($retryApi)) {
            if (isset($retryApi[0][\UPS\Shipping\Helper\ConstantModel::COUNT_RETRY])
                && $retryApi[0][\UPS\Shipping\Helper\ConstantModel::COUNT_RETRY] > 3
            ) {
                return $this->deleteReTryAPIFail($data);
            } else {
                $dataUpdate['id_retry'] = $retryApi[0]['id_retry'];
                $dataUpdate[\UPS\Shipping\Helper\ConstantModel::COUNT_RETRY]
                    = $retryApi[0][\UPS\Shipping\Helper\ConstantModel::COUNT_RETRY];
                return $this->updateReTryAPIFail($dataUpdate);
            }
        } else {
            return $this->getResource()->saveReTryAPIFail($data);
        }
    }

    /**
     * RetryApi deleteReTryAPIFail
     * delete retry API Fail
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function deleteReTryAPIFail($data)
    {
        return $this->getResource()->deleteReTryAPIFail($data);
    }
}
