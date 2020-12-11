<?php
/**
 * Account file
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
 * Account class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Account extends \Magento\Framework\View\Element\Template
{
    private $_value;
    protected $country;
    protected $accountModel;
    protected $session;
    protected $redirect;
    protected $formKey;
    protected $adminSession;
    protected $scopeConfig;
    protected $apiHandshake;
    protected $apiAccount;
    protected $configWriter;
    protected $cacheTypeList;
    protected $checkoutSession;
    protected $regionColFactory;

    /**
     * Account __construct
     *
     * @param string $context          //The context
     * @param string $country          //The country
     * @param string $accountModel     //The accountModel
     * @param string $redirect         //The redirect
     * @param string $adminSession     //The adminSession
     * @param string $regionColFactory //The regionColFactory
     * @param string $apiHandshake     //The apiHandshake
     * @param string $apiAccount       //The apiAccount
     * @param string $configWriter     //The configWriter
     * @param string $cacheTypeList    //The cacheTypeList
     * @param string $checkoutSession  //The checkoutSession
     * @param string $formKey          //The formKey
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Model\Config\Source\Country $country,
        \UPS\Shipping\Model\Account $accountModel,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Directory\Model\RegionFactory $regionColFactory,
        \UPS\Shipping\API\Handshake $apiHandshake,
        \UPS\Shipping\API\Account $apiAccount,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->session = $context->getSession();
        $this->country = $country;
        $this->accountModel = $accountModel;
        $this->apiHandshake = $apiHandshake;
        $this->apiAccount = $apiAccount;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->checkoutSession = $checkoutSession;
        $this->redirect = $redirect;
        $this->_value = 'value';
        $this->adminSession = $adminSession;
        $this->regionColFactory = $regionColFactory;
        $this->formKey = $formKey;
        parent::__construct($context);
    }

    /**
     * Account checkAccountDefault
     *
     * @return array $data
     */
    public function checkAccountDefault()
    {
        return $this->accountModel->getAccountDefault();
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
     * Account dataFormSession
     *
     * @return array $data
     */
    public function dataFormSession()
    {
        $this->session->start();
        // get message
        $data = $this->session->getMessage();
        $this->session->unsMessage();
        if ($data == "") {
            $dataError = [
                'Title' => '',
                'CustomerName' => '',
                'CompanyName' => '',
                'AddressEmail' => '',
                'PhoneNumber' => '',
                'AddressType' => '',
                'AddressLine1' => '',
                'AddressLine2' => '',
                'AddressLine3' => '',
                'StateProvinceCode' => '',
                'PostalCode' => '',
                'AccountCity' => '',
                'optradio' => '0',
                'AccountName1' => '',
                'AccountNumber1' => '',
                'VATNumber' => '',
                'PromoCode' => '',
                'AccountName' => '',
                'AccountNumber' => '',
                'InvoiceNumber' => '',
                'InvoiceAmount' => '',
                'Currency' => 'EUR',
                'InvoiceDate' => '',
            ];
            $usCountry = strtolower($this->getCountryCode());
            if (\UPS\Shipping\Helper\Config::LOWER_CONFIG_COUNTRY_US == $usCountry) {
                $dataError['ControlId'] = '';
            }
        } else {
            $dataError = $data;
        }
        return $dataError;
    }

    /**
     * Account getListCurrency
     *
     * @return array $data
     */
    public function getListCurrency()
    {
        return \UPS\Shipping\Helper\Config::LIST_CURRENCIES;
    }

    /**
     * Account getListContry
     *
     * @return array $data
     */
    public function getListContry()
    {
        $arrayOptionCountry = $this->country->toOptionArray(true);
        $listCountry = [];

        foreach ($arrayOptionCountry as $country) {
            // create array country UPS
            $arrayCountry = \UPS\Shipping\Helper\Config::LISTEUCOUNTRYS;
            if (in_array($country[$this->_value], $arrayCountry)) {
                $arrayCountry = [
                    'id' => $country[$this->_value],
                    $this->_value => $country['label']
                ];
                // push country to $listCountry
                array_push($listCountry, $arrayCountry);
            }
        }
        return $listCountry;
    }

    /**
     * Account getListStates
     *
     * @return array $data
     */
    public function getListStates()
    {
        $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::PRE_REGISTERED_PLUGIN_TOKEN);
        if (!empty($bearerToken)) {
            $response = $this->apiAccount->getUpsId($bearerToken);
            $response = json_decode($response);
            if (isset($response->error->errorCode) && (intval($response->error->errorCode) == 401)) {
                $this->callAPIHandshake();
            }
        }
        $regions = $this->regionColFactory->create()->getCollection()->addFieldToFilter('country_id', 'US');
        return $regions->getData();
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
}
