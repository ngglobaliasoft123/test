<?php
/**
 * Country file
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
 * Country class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Country extends \Magento\Framework\View\Element\Template
{
    private $_value = 'value';
    protected $scopeConfig;
    protected $country;
    protected $formKey;
    protected $apiHandshake;
    protected $cacheTypeList;
    protected $checkoutSession;

    /**
     * Country __construct
     *
     * @param string $context         //The context
     * @param string $country         //The country
     * @param string $apiHandshake    //The apiHandshake
     * @param string $configWriter    //The configWriter
     * @param string $cacheTypeList   //The cacheTypeList
     * @param string $formKey         //The formKey
     * @param string $checkoutSession //The checkoutSession
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Model\Config\Source\Country $country,
        \UPS\Shipping\API\Handshake $apiHandshake,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->country = $country;
        $this->formKey = $formKey;
        $this->apiHandshake = $apiHandshake;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->checkoutSession = $checkoutSession;

        parent::__construct($context);
    }

    /**
     * Country isCountryCode
     *
     * @return array $data
     */
    public function isCountryCode()
    {
        return $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE);
    }

    /**
     * Country getListContry
     *
     * @return array $data
     */
    public function getListContry()
    {
        $this->callAPIHandshake();
        $arrayOptionCountry = $this->country->toOptionArray(true);
        $listCountry = [];
        $arrayCountry = \UPS\Shipping\Helper\Config::LISTEUCOUNTRYS;
        $belgium = (string)__('Belgium');
        $netherlands = (string)__('Netherlands');
        $france = (string)__('France');
        $spain = (string)__('Spain');
        $poland = (string)__('Poland');
        $italy = (string)__('Italy');
        $germany = (string)__('Germany');
        $unitedKingdom = (string)__('United Kingdom');
        $unitedStates = (string)__('United States');
        $listCountryNames = ['BE'=>$belgium, 'NL'=>$netherlands,
        'FR'=>$france, 'ES'=>$spain, 'PL'=>$poland,
        'IT'=>$italy, 'DE'=>$germany, 'GB'=>$unitedKingdom, 'US'=>$unitedStates];
        foreach ($arrayOptionCountry as $country) {
            if (in_array($country[$this->_value], $arrayCountry)) {
                $arrayCountrys = [
                    'id' => $country[$this->_value],
                    $this->_value => $listCountryNames[$country[$this->_value]]
                ];
                array_push($listCountry, $arrayCountrys);
            }
        }
        return $listCountry;
    }

    /**
     * TermCondition callAPILicense
     *
     * @return array $data
     */
    public function callAPIHandshake()
    {
        $getMerchantKey = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY);
        $websiteMerchant = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_WEBSECUREURL);
        $valueSecurityToken = $this->apiHandshake->generatePass(32);
        $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN, $valueSecurityToken);

        foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        $this->checkoutSession->setHandshakeKey($valueSecurityToken);
        $this->apiHandshake->callAPIHandshake($websiteMerchant, $getMerchantKey, $valueSecurityToken);
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
