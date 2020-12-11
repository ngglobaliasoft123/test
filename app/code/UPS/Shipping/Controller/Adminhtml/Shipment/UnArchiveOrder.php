<?php
/**
 * UnArchiveOrder file
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
 * UnArchiveOrder class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class UnArchiveOrder extends \Magento\Framework\App\Action\Action
{
    protected $UnArchiveOrder;

    /**
     * Exportlabel unlinkFile
     *
     * @param string $context        //The context
     * @param string $UnArchiveOrder //The UnArchiveOrder
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \UPS\Shipping\Model\Order $UnArchiveOrder
    ) {
        $this->UnArchiveOrder = $UnArchiveOrder;
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
        if ($dataParam['updateUnArchivedStatus'] == 'updateUnArchiveOrder') {
            $listOrderId = json_decode($dataParam['orderIds']);
            $this->UnArchiveOrder->updateStatusUnArchiveOrder($listOrderId);
            //Redirect to order screen
            $this->_redirect('upsshipping/shipment/archived');
        }
    }

}
