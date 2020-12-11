<?php
/**
 * CsrfValidatorSkip file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Plugin;
/**
 * PriceCurrency class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class CsrfValidatorSkip
{
    /**
     * PriceCurrency round
     *
     * @param \Magento\Framework\App\Request\CsrfValidator $subject //The subject
     * @param \Closure                                     $proceed //the proceed
     * @param \Magento\Framework\App\RequestInterface      $request //the request
     * @param \Magento\Framework\App\ActionInterface       $action  // the action
     *
     * @return array $data
     */
    public function aroundValidate(
        $subject,
        \Closure $proceed,
        $request,
        $action
    ) {
        if (in_array($request->getActionName(), \UPS\Shipping\Helper\ConstantEshopper::CSRFACTIONNAME)) {
            return; // Skip CSRF check
        }
        $proceed($request, $action); // Proceed Magento 2 core functionalities
    }
}
