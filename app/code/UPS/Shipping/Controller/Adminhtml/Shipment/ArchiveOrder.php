<?php
/**
 * ArchiveOrder file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Controller\Adminhtml\Shipment;
/**
 * ArchiveOrder class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class ArchiveOrder extends \Magento\Framework\App\Action\Action
{
    protected $archiveOrder;

    /**
     * Exportlabel unlinkFile
     *
     * @param string $context      //The context
     * @param string $archiveOrder //The archiveOrder
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \UPS\Shipping\Model\Order $archiveOrder
    ) {
        $this->archiveOrder = $archiveOrder;
        parent::__construct($context);
    }

    /**
     * Exportlabel unlinkFile
     *
     * @return null
     */
    public function execute()
    {
        $dataParam = $this->getRequest()->getParams();
        if ($dataParam['updateStatus'] == 'updateArchiveOrder') {
            $listOrderId = json_decode($dataParam['orderIds']);
            $this->archiveOrder->updateStatusArchiveOrder($listOrderId);
            //Redirect to order screen
            $this->_redirect('upsshipping/shipment/order');
        }
    }

}
