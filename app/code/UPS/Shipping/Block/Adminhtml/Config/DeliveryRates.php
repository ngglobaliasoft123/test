<?php
/**
 * DeliveryRates file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Block\Adminhtml\Config;
/**
 * DeliveryRates class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class DeliveryRates extends \Magento\Framework\View\Element\Template
{
    protected $scopeConfig;
    protected $delivery;
    protected $service;
    protected $formKey;

    /**
     * DeliveryRates __construct
     *
     * @param string $context  //The context
     * @param string $delivery //The delivery
     * @param string $service  //The service
     * @param string $formKey  //The formKey
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \UPS\Shipping\Model\DeliveryRates $delivery,
        \UPS\Shipping\Model\Service $service,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->delivery = $delivery;
        $this->service = $service;
        $this->formKey = $formKey;

        parent::__construct($context);
    }

    /**
     * DeliveryRates getDataDeliveryRates
     *
     * @param string $service_id //The service_id
     *
     * @return array $data
     */
    public function getDataDeliveryRates($service_id)
    {
        return $this->delivery->getDataDeliveryRates($service_id);
    }

    /**
     * DeliveryRates getConfigData
     *
     * @return array $data
     */
    public function getConfigData()
    {
        $countryCode = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE);
        $configAPString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_ACCESS_POINT;
        $configADDString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_SHIPPING_ADDRESS;
        $result = array();
        if ($this->scopeConfig->getValue($configAPString) == 1) {
            $listServiceAP = $this->service->getListService($countryCode, 'AP');
        } else {
            $listServiceAP = [];
        }
        if ($this->scopeConfig->getValue($configADDString) == 1) {
            $listServiceADD = $this->service->getListService($countryCode, 'ADD');
        } else {
            $listServiceADD = [];
        }

        return ['AP_select_shipping' => $listServiceAP, 'ADD_select_shipping' => $listServiceADD];
    }

    /**
     * Account getCountryCode
     *
     * @return array $data
     */
    public function getCountryCode()
    {
        return $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE);
    }

    /**
     * DeliveryRates getDataDelivery
     *
     * @return array $data
     */
    public function getDataDelivery()
    {
        $listDeliveryRate = $this->delivery->getDataDeliveryRates();
        $rateByService = [];
        foreach ($listDeliveryRate as $row) {
            $rateByService[$row['service_id']][$row['rate_type']][] = [$row['min_order_value'], $row['delivery_rate']];
        }
        return $rateByService;
    }

    /**
     * DeliveryRates getCurrencyData
     *
     * @return array $data
     */
    public function getCurrencyData()
    {
        return $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::DELIVERY_RATES_CURRENCY_DEFAULT);
    }

    /**
     * Country getFormKey
     *
     * @return array $serviceData
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}
