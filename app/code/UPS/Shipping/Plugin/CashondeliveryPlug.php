<?php
/**
 * PluginCashondeliveryPlug file
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
 * CashondeliveryPlug class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class CashondeliveryPlug
{
    protected $checkoutSession;
    protected $modelService;
    /**
     * Place __construct
     *
     * @param string $checkoutSession   //The checkoutSession
     * @param string $modelService      //The modelService
     *
     * @return null
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \UPS\Shipping\Model\Service $modelService
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->modelService = $modelService;
    }

    /**
     * CashondeliveryPlug afterIsAvailable
     *
     * @param \Magento\OfflinePayments\Model\Cashondelivery $subject //The subject
     * @param string                                        $result  //the proceed
     *
     * @return array $data
     */
    public function afterIsAvailable(\Magento\OfflinePayments\Model\Cashondelivery $subject, $result)
    {
        $quote = $this->checkoutSession->getQuote();
        $usCountry = strtolower($quote->getShippingAddress()->getCountry());

        $shippingService = $this->checkoutSession->getSelectedShippingService();
        $listServices = $this->modelService->getServicesById($shippingService);
        if (!empty($listServices[0]['service_type'])
            && $listServices[0]['service_type'] == 'AP'
            && \UPS\Shipping\Helper\Config::LOWER_CONFIG_COUNTRY_US == $usCountry
        ) {
            return false;
        }
        return $result;
    }
}
