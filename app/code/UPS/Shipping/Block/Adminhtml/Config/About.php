<?php
/**
 * About file
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
 * About class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class About extends \Magento\Framework\View\Element\Template
{
    protected $modelLogApi;
    protected $formKey;
    protected $adminSession;
    protected $scopeConfig;

    /**
     * About __construct
     *
     * @param string $context      //The context
     * @param string $modelLogApi  //The modelLogApi
     * @param string $adminSession //The adminSession
     * @param string $formKey      //The formKey
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \UPS\Shipping\Model\LogApi $modelLogApi,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->modelLogApi = $modelLogApi;
        $this->formKey = $formKey;
        $this->scopeConfig = $context->getScopeConfig();
        $this->adminSession = $adminSession;
        parent::__construct($context);
    }

    /**
     * Accessorial getListAccessorial
     *
     * @return array $data
     */
    public function getLocal()
    {
        $countryCodeString = \UPS\Shipping\Helper\Config::SERVICE_UPS_ACCOUNT_LOCATION;
        return $this->scopeConfig->getValue($countryCodeString);
    }

    /**
     * Accessorial getListAccessorial
     *
     * @return array $data
     */
    public function getLogAPIs()
    {
        return $this->modelLogApi->getLogAPIs();
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
     * Account getAdminLanguageCode
     *
     * @return array $data
     */
    public function getAdminLanguageCode()
    {
        $interfaceLocale = \UPS\Shipping\Helper\Config::COUNTRY_US;
        $dataUser = $this->adminSession->getUser()->getData();
        if (isset($dataUser['interface_locale']))
            $interfaceLocale = $dataUser['interface_locale'];
        return $interfaceLocale;
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
