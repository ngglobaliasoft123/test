<?php
/**
 * LogApi file
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
 * LogApi class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class LogApi extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = \UPS\Shipping\Helper\Config::UPS_SHIPPING_LOGAPI;

    protected $cacheTag = \UPS\Shipping\Helper\Config::UPS_SHIPPING_LOGAPI;
    protected $eventPrefix = \UPS\Shipping\Helper\Config::UPS_SHIPPING_LOGAPI;

    /**
     * LogApi _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('UPS\Shipping\Model\ResourceModel\LogApi');
    }

    /**
     * LogApi writeRequest
     * function write request
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function writeRequest($data)
    {
        return $this->getResource()->writeRequest($data);
    }

    /**
     * LogApi writeResponse
     * function write response
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function writeResponse($data)
    {
        return $this->getResource()->writeResponse($data);
    }
}
