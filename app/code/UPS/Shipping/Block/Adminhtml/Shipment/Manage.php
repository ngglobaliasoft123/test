<?php
/**
 * Manage file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Block\Adminhtml\Shipment;
/**
 * Manage class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Manage extends \Magento\Framework\View\Element\Template
{
    /**
     * Manage __construct
     *
     * @param string $context //The context
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Manage pagination
     *
     * @param string $totalPage   //The totalPage
     * @param string $currentPage //The currentPage
     *
     * @return array $data
     */
    public function pagination($totalPage, $currentPage)
    {
        $numberItem = 3;
        $floorItem = floor($numberItem / 2);
        $ceilItem = ceil($numberItem / 2);
        $return = [];
        if ($totalPage <= $numberItem) {
            for ($i = 1; $i <= $totalPage; $i++) {
                $return[] = ['page' => $i, 'text' => $i];
            }
        } else {
            $start = 1;
            $end = $numberItem;
            if ($currentPage == 1) {
                $return[] = ['page' => '', 'text' => \UPS\Shipping\Helper\Config::STYLE_IMG_1
                . $this->getViewFileUrl('UPS_Shipping/images/d-left.png') . '"/><li>'];
                $return[] = ['page' => '', 'text' => \UPS\Shipping\Helper\Config::STYLE_IMG_1
                . $this->getViewFileUrl('UPS_Shipping/images/left.png') . '"/>'];
            } else {
                $return[] = ['page' => 1, 'text' => \UPS\Shipping\Helper\Config::STYLE_IMG_2
                . $this->getViewFileUrl('UPS_Shipping/images/d-left.png') . '"/>'];
                $return[] = ['page' => $currentPage - 1, 'text' => \UPS\Shipping\Helper\Config::STYLE_IMG_2
                . $this->getViewFileUrl('UPS_Shipping/images/left.png') . '"/>'];
            }
            if ($currentPage >= $numberItem) {
                $return[] = ['page' => '', 'text' => '...'];
                $start = $currentPage - $floorItem;
                $end = $currentPage + $floorItem;
            }
            if ($currentPage + $floorItem >= $totalPage) {
                $start = $totalPage - $ceilItem;
                $end = $totalPage;
            }
            for ($i = $start; $i <= $end; $i++) {
                $return[] = ['page' => $i, 'text' => $i];
            }
            if ($end != $totalPage) {
                $return[] = ['page' => '', 'text' => '...'];
            }
            if ($currentPage == $totalPage) {
                $return[] = ['page' => '', 'text' => \UPS\Shipping\Helper\Config::STYLE_IMG_1
                . $this->getViewFileUrl('UPS_Shipping/images/right.png') . '"/>'];
                $return[] = ['page' => '', 'text' => \UPS\Shipping\Helper\Config::STYLE_IMG_1
                . $this->getViewFileUrl('UPS_Shipping/images/d-right.png') . '"/>'];
            } else {
                $return[] = ['page' => $currentPage + 1, 'text' => \UPS\Shipping\Helper\Config::STYLE_IMG_2
                . $this->getViewFileUrl('UPS_Shipping/images/right.png') . '"/>'];
                $return[] = ['page' => $totalPage, 'text' => \UPS\Shipping\Helper\Config::STYLE_IMG_2
                . $this->getViewFileUrl('UPS_Shipping/images/d-right.png') . '"/>'];
            }
        }
        return $return;
    }
}
