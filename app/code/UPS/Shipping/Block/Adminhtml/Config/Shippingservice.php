<?php
/**
 * Shippingservice file
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
 * Shippingservice class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Shippingservice extends \Magento\Framework\View\Element\Template
{
    protected $scopeConfig;
    protected $serviceModel;
    protected $accountModel;
    protected $formKey;

    /**
     * Shippingservice __construct
     *
     * @param string $context      //The context
     * @param string $serviceModel //The serviceModel
     * @param string $accountModel //The accountModel
     * @param string $formKey      //The formKey
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \UPS\Shipping\Model\Service $serviceModel,
        \UPS\Shipping\Model\Account $accountModel,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->serviceModel = $serviceModel;
        $this->accountModel = $accountModel;
        $this->formKey = $formKey;
        parent::__construct($context);
    }

    /**
     * Shippingservice getListService
     *
     * @param string $service_type //The service_type
     *
     * @return array $data
     */
    public function getListService($countryCode, $service_type)
    {
        return $this->serviceModel->getListService($countryCode, $service_type);
    }

    /**
     * Shippingservice getConfigData
     *
     * @return array $data
     */
    public function getConfigData()
    {
        $apString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_ACCESS_POINT;
        $defaultString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_SET_DEFAULT;
        $availableString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_NUMBER_OF_ACCESS_POINT_AVAIABLE;
        $rangeString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DISPLAY_ALL_ACCESS_POINT_IN_RANGE;
        $addressString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_SHIPPING_ADDRESS;
        $cutOfTimeString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_CUT_OFF_TIME;
        $numberAPString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_CHOOSE_ACCOUNT_NUMBER_AP;
        $numberAddString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_CHOOSE_ACCOUNT_NUMBER_ADD;
        $countryCode = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE);
        return [
            'to_ap_delivery' => $this->scopeConfig->getValue($apString),
            'default_shipping' => $this->scopeConfig->getValue($defaultString),
            'chosen_number_of_access' => $this->scopeConfig->getValue($availableString),
            'chosen_display_all' => $this->scopeConfig->getValue($rangeString),
            'to_address_delivery' => $this->scopeConfig->getValue($addressString),
            'cut_off_time' => $this->scopeConfig->getValue($cutOfTimeString),
            'option_chosen_number_of_access' => [3, 4, 5, 6, 7, 8, 9, 10],
            'option_chosen_display_all' => [5, 10, 15, 20, 30, 50],
            'AP_select_shipping' => $this->getListService($countryCode, 'AP'),
            'ADD_select_shipping' => $this->getListService($countryCode, 'ADD'),
            'List_Account' => $this->accountModel->getListAccount(),
            'choose_account_number_ap' => $this->scopeConfig->getValue($numberAPString),
            'choose_account_number_add' => $this->scopeConfig->getValue($numberAddString),
            'option_cut_off_time' => [
                '00' => '12 AM',
                '01' => '1 AM',
                '02' => '2 AM',
                '03' => '3 AM',
                '04' => '4 AM',
                '05' => '5 AM',
                '06' => '6 AM',
                '07' => '7 AM',
                '08' => '8 AM',
                '09' => '9 AM',
                '10' => '10 AM',
                '11' => '11 AM',
                '12' => '12 PM',
                '13' => '1 PM',
                '14' => '2 PM',
                '15' => '3 PM',
                '16' => '4 PM',
                '17' => '5 PM',
                '18' => '6 PM',
                '19' => '7 PM',
                '20' => '8 PM',
                '21' => '9 PM',
                '22' => '10 PM',
                '23' => '11 PM',
                '24' => 'Disable'
            ]
        ];
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
     * Account getAdultSignature
     *
     * @return array $data
     */
    public function getAdultSignature()
    {
        return $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::ADULT_SIGNATURE);
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
