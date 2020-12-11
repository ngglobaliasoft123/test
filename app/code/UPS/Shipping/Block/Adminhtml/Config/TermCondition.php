<?php
/**
 * TermCondition file
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
 * TermCondition class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class TermCondition extends \Magento\Framework\View\Element\Template
{
    protected $scopeConfig;
    protected $apiLicense;
    protected $apiHandshake;
    protected $userModel;
    protected $authSession;
    protected $modelLicense;
    protected $formKey;
    protected $configWriter;
    protected $cacheTypeList;
    protected $adminSession;
    protected $checkoutSession;

    /**
     * TermCondition __construct
     *
     * @param string $context         //The context
     * @param string $apiLicense      //The apiLicense
     * @param string $apiHandshake    //The apiHandshake
     * @param string $userModel       //The userModel
     * @param string $authSession     //The authSession
     * @param string $modelLicense    //The modelLicense
     * @param string $configWriter    //The configWriter
     * @param string $formKey         //The formKey
     * @param string $cacheTypeList   //The cacheTypeList
     * @param string $adminSession    //The adminSession
     * @param string $checkoutSession //The checkoutSession
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \UPS\Shipping\API\License $apiLicense,
        \UPS\Shipping\API\Handshake $apiHandshake,
        \Magento\User\Model\User $userModel,
        \Magento\Backend\Model\Auth\Session $authSession,
        \UPS\Shipping\Model\License $modelLicense,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->modelLicense = $modelLicense;
        $this->scopeConfig = $context->getScopeConfig();
        $this->apiLicense = $apiLicense;
        $this->apiHandshake = $apiHandshake;
        $this->userModel = $userModel;
        $this->authSession = $authSession;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->adminSession = $adminSession;
        $this->checkoutSession = $checkoutSession;
        $this->formKey = $formKey;

        parent::__construct($context);
    }

    /**
     * TermCondition isAcceptTermCondition
     *
     * @return array $data
     */
    public function isAcceptTermCondition()
    {
        return $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_TERM_CONDITION);
    }

    /**
     * TermCondition callAPILicense
     *
     * @return array $data
     */
    public function callAPILicense()
    {
        $checkHandshake = false;
        $handshakeToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN);
        if (!empty($handshakeToken)) {
            $checkHandshake = true;
        } else if ($this->callAPIHandshake()) {
            $checkHandshake = true;
        } else {
            $checkHandshake = false;
        }

        if ($checkHandshake) {
            // get user name
            $userName = $this->authSession->getUser()->getUsername();
            $locate = explode('_', $this->userModel->loadByUsername($userName)->getData('interface_locale'));
            // get responce access license
            $countryCodeString = \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE;
            $arrCountryCode = [
                "CountryCode" => $this->scopeConfig->getValue($countryCodeString),
                "LanguageCode" => $locate[0]
            ];
            $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::PRE_REGISTERED_PLUGIN_TOKEN);
            $response = '';
            if (!empty($bearerToken)) {
                $response = $this->apiLicense->access1($arrCountryCode, $bearerToken);
            } else {
                $this->callAPIHandshake();
                $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::PRE_REGISTERED_PLUGIN_TOKEN);
                if (!empty($bearerToken)) {
                    $response = $this->apiLicense->access1($arrCountryCode, $bearerToken);
                }
            }
            // check empty response
            if (empty($response)) {
                return [];
            }
            $response1 = json_decode($response);
            $check = $this->modelLicense->checkIsset();
            $countCheck = count($check);
            if ($countCheck == 0) {
                (isset($response1->AccessLicenseAgreementResponse->AccessLicenseText))
                ? $this->modelLicense ->insertAccessLicenseText($response1->AccessLicenseAgreementResponse->AccessLicenseText)
                : '' ;
            }
            if ($countCheck > 0 ) {
                (isset($response1->AccessLicenseAgreementResponse->AccessLicenseText))
                ? $this->modelLicense->updateAccessLicenseText($response1->AccessLicenseAgreementResponse->AccessLicenseText)
                : '';
            }
            return json_decode(str_replace('\u0026#xD;', '<br>', str_replace('&#xD;', '<br>', $response)));
        } else {
            $returnData = [
                'errorHandshake' => true
            ];
            return json_encode($returnData);
        }
    }

    /**
     * TermCondition callAPIHandshake
     *
     * @return array $data
     */
    public function callAPIHandshake()
    {
        $resultCallHandshakeApi = false;
        $getMerchantKey = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY);
        $websiteMerchant = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_WEBSECUREURL);
        $valueSecurityToken = $this->apiHandshake->generatePass(32);
        $this->checkoutSession->setHandshakeKey($valueSecurityToken);
        if ($this->apiHandshake->callAPIHandshake($websiteMerchant, $getMerchantKey, $valueSecurityToken)) {
            $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN,  $valueSecurityToken);

            foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
                $this->cacheTypeList->cleanType($type);
            }
            $resultCallHandshakeApi = true;
        }
        return $resultCallHandshakeApi;
    }

    /**
     * TermCondition getCurrentUser
     *
     * @return array $data
     */
    public function getCurrentUser()
    {
        return $this->authSession->getUser()->getUsername();
    }

    /**
     * TermCondition getCountryCode
     *
     * @return array $data
     */
    public function getCountryCode()
    {
        return $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE);
    }

    /**
     * TermCondition getAdminLanguageCode
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
