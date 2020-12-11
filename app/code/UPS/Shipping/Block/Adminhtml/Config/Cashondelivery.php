<?php
/**
 * Cashondelivery file
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
 * Cashondelivery class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Cashondelivery extends \Magento\Framework\View\Element\Template
{
    protected $scopeConfig;
    protected $cacheTypeList;
    protected $formKey;

    /**
     * Cashondelivery __construct
     *
     * @param string $context       //The context
     * @param string $cacheTypeList //The cacheTypeList
     * @param string $formKey       //The formKey
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->cacheTypeList = $cacheTypeList;
        $this->formKey = $formKey;
        parent::__construct($context);
    }

    /**
     * Cashondelivery getConfigData
     *
     * @return array $data
     */
    public function getConfigData()
    {
        $this->clearConfigCache();
        return $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CASH_ON_DELIVERY_UPS_SHIPPING_ACTIVE);
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
     * Cashondelivery getConfigDataOption
     *
     * @return array $data
     */
    public function getConfigDataOption()
    {
        $this->clearConfigCache();
        return $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CASH_ON_DELIVERY_UPS_SHIPPING_OPTION_ACTIVE);
    }

    /**
     * Cashondelivery clearConfigCache
     *
     * @return array $data
     */
    public function clearConfigCache()
    {
        $this->cacheTypeList->cleanType('config');
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
