<?php
/**
 * AccountSuccess file
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
 * AccountSuccess class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class AccountSuccess extends \Magento\Framework\View\Element\Template
{
    protected $formKey;
    private $_value = 'value';
    private $_countryString = 'country';
    private $_countryName = 'countryName';

    protected $country;
    protected $modelAccount;
    protected $session;
    protected $adminSession;
    protected $countryModel;
    protected $scopeConfig;
    protected $regionColFactory;

    /**
     * AccountSuccess __construct
     *
     * @param string $context          //The context
     * @param string $country          //The country
     * @param string $modelAccount     //The modelAccount
     * @param string $countryModel     //The countryModel
     * @param string $adminSession     //The adminSession
     * @param string $regionColFactory //The regionColFactory
     * @param string $formKey          //The formKey
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Model\Config\Source\Country $country,
        \UPS\Shipping\Model\Account $modelAccount,
        \Magento\Directory\Model\Country $countryModel,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Directory\Model\RegionFactory $regionColFactory,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->countryModel = $countryModel;
        $this->session = $context->getSession();
        $this->country = $country;
        $this->formKey = $formKey;
        $this->adminSession = $adminSession;
        $this->regionColFactory = $regionColFactory;
        $this->modelAccount = $modelAccount;
        parent::__construct($context);
    }

    /**
     * AccountSuccess getCountryCode
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
     * AccountSuccess dataFormSession
     *
     * @return array $data
     */
    public function dataFormSession()
    {
        $this->session->start();
        $data = $this->session->getMessage();
        $this->session->unsMessage();
        if ($data == "") {
            $dataError = [
                'AddressType' => '',
                'AccountName' => '',
                'AddressLine1' => '',
                'AddressLine2' => '',
                'AddressLine3' => '',
                'StateProvinceCode' =>'',
                'PostalCode' => '',
                'AccountCity' => '',
                'optradio' => '2',
                'PhoneNumber' => '',
                'AccountNumber' => '',
                'InvoiceNumber' => '',
                'InvoiceAmount' => '',
                'Currency' => 'EUR',
                'InvoiceDate' => '',
                'AccountNumber1' => '',
                'BorderAccountRed' => ''
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
     * AccountSuccess dataErrorSession
     *
     * @return array $data
     */
    public function dataErrorSession()
    {
        $this->session->start();
        $data = $this->session->getErrorMessage();
        $this->session->unsErrorMessage();
        return $data;
    }

    /**
     * AccountSuccess dataSuccessSession
     *
     * @return array $data
     */
    public function dataSuccessSession()
    {
        $this->session->start();
        $data = $this->session->getSuccessMessage();
        $this->session->unsSuccessMessage();
        return $data;
    }

    /**
     * AccountSuccess getListCurrency
     *
     * @return array $data
     */
    public function getListCurrency()
    {
        return \UPS\Shipping\Helper\Config::LIST_CURRENCIES;
    }

    /**
     * AccountSuccess getListContry
     *
     * @return array $data
     */
    public function getListContry()
    {
        // get list country
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
     * AccountSuccess getListAccount
     *
     * @return array $data
     */
    public function getListAccount()
    {
        // get list account
        $data = $this->modelAccount->getListAccount();
        foreach ($data as $key => $value) {
            if ($this->countryModel->loadByCode($value[$this->_countryString])->hasData()) {
                $data[$key][$this->_countryName] = $this->countryModel->loadByCode($value[$this->_countryString])->getName();
            } else {
                $data[$key][$this->_countryName] = '';
            }
        }
        return $data;
    }

    /**
     * AccountSuccess getAccountDefault
     *
     * @return array $data
     */
    public function getAccountDefault()
    {
        // get data account default
        $data = $this->modelAccount->getAccountDefault();
        if ($this->countryModel->loadByCode($data[$this->_countryString])->hasData()) {
            $data[$this->_countryName] = $this->countryModel->loadByCode($data[$this->_countryString])->getName();
        } else {
            $data[$this->_countryName] = '';
        }
        return $data;
    }

    /**
     * Account getListStates
     *
     * @return array $data
     */
    public function getListStates()
    {
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
}
