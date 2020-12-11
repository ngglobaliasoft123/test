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
namespace UPS\Shipping\Model\ResourceModel;
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
class LogApi extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * LogApi __construct
     * collect registration data
     *
     * @param string $context //The context
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * LogApi _construct
     *
     * @return array $data
     */
    protected function _construct()
    {
        $this->_init('ups_shipping_logs_api', 'id');
    }

    /**
     * LogApi writeRequest
     *
     * @param string $data //The idOrder
     *
     * @return array $data
     */
    public function writeRequest($data)
    {
        $this->getConnection()->insert($this->getMainTable(), $data);
        return $this->getConnection()->lastInsertId();
    }

    /**
     * LogApi writeResponse
     *
     * @param string $data //The data
     *
     * @return array $data
     */
    public function writeResponse($data)
    {
        return $this->getConnection()->update($this->getMainTable(), $data, ['id =?' => $data['id']]);
    }
}
