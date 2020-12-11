<?php
/**
 * Account file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Controller\Adminhtml\Config;
/**
 * Account class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Account extends \UPS\Shipping\Controller\Adminhtml\AbstractController
{
    /**
     * Account execute
     *
     * @return null
     */
    public function execute()
    {
        $this->startExcute();
        if (!$this->accountModel->getAccountDefault()) {
            return $this->pageFactory->create();
        } else {
            return $this->goRedirect();
        }
    }

    /**
     * Account goRedirect
     *
     * @return resultRedirect
     */
    public function goRedirect()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('upsshipping/config/accountsuccess');
        return $resultRedirect;
    }
}
