<?php
/**
 * Billingpreference file
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
 * Billingpreference class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Billingpreference extends \Magento\Framework\View\Element\Template
{
    protected $adminSession;
    protected $formKey;
    protected $scopeConfig;
    /**
     * Billingpreference __construct
     *
     * @param string $context      //The context
     * @param string $adminSession //The adminSession
     * @param string $formKey      //The formKey
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->adminSession = $adminSession;
        $this->formKey = $formKey;
        parent::__construct($context);
    }

    /**
     * Billingpreference getUrlFile
     *
     * @return array $data
     */
    public function getUrlFile()
    {
        return $this->_assetRepo->getUrl("UPS_Shipping::file/CODAndSheduledPUForm.pdf");
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
     * Billingpreference getUrlCODFile
     *
     * @param string $language //The language
     *
     * @return array $data
     */
    public function getUrlCODFile($language)
    {
        return $this->_assetRepo->getUrl("UPS_Shipping::file/CODRegistration_" . $language . ".pdf");
    }

    /**
     * Billingpreference getUrlRegisterFile
     *
     * @param string $language //The language
     *
     * @return array $data
     */
    public function getUrlRegisterFile($language)
    {
        return $this->_assetRepo->getUrl("UPS_Shipping::file/PickUpRegistration_" . $language . ".pdf");
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
