<?php
/**
 * SaveAccount file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Controller\Adminhtml\Config;

/**
 * SaveAccount class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class SaveAccount extends \Magento\Framework\App\Action\Action
{
    protected $messageManager;
    protected $resultPageFactory;
    protected $modelAccount;
    protected $checkoutSession;
    protected $regex;
    protected $data;
    protected $apiLicense;
    protected $accountModel;
    protected $scopeConfig;
    protected $userModel;
    protected $authSession;
    protected $configWriter;
    protected $session;
    protected $cacheTypeList;
    protected $modelLicense;
    protected $carrier;
    protected $apiHandshake;
    protected $apiAccount;
    protected $licenseModel;
    protected $upgradePlugin;
    /**
     * SaveAccount __construct
     *
     * @param string $context           //The context
     * @param string $resultPageFactory //The resultPageFactory
     * @param string $modelAccount      //The modelAccount
     * @param string $checkoutSession   //The checkoutSession
     * @param string $apiLicense        //The apiLicense
     * @param string $accountModel      //The accountModel
     * @param string $licenseModel      //The licenseModel
     * @param string $configWriter      //The configWriter
     * @param string $scopeConfig       //The scopeConfig
     * @param string $userModel         //The userModel
     * @param string $authSession       //The authSession
     * @param string $cacheTypeList     //The cacheTypeList
     * @param string $session           //The session
     * @param string $apiHandshake      //The apiHandshake
     * @param string $apiAccount        //The apiAccount
     * @param string $carrier           //The carrier
     * @param string $apiManager        //The apiManager
     * @param string $modelLicense      //The modelLicense
     *
     * @return null
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \UPS\Shipping\Model\Account $modelAccount,
        \Magento\Checkout\Model\Session $checkoutSession,
        \UPS\Shipping\API\License $apiLicense,
        \UPS\Shipping\Model\Account $accountModel,
        \UPS\Shipping\Model\License $licenseModel,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\User\Model\User $userModel,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \UPS\Shipping\API\Handshake $apiHandshake,
        \UPS\Shipping\API\Account $apiAccount,
        \UPS\Shipping\Model\Carrier $carrier,
        \UPS\Shipping\API\Manager\Index $apiManager,
        \UPS\Shipping\Model\License $modelLicense
    ) {
        $this->licenseModel = $licenseModel;
        $this->modelLicense = $modelLicense;
        $this->session = $session;
        $this->scopeConfig = $scopeConfig;
        $this->cacheTypeList = $cacheTypeList;
        $this->accountModel = $accountModel;
        $this->checkoutSession = $checkoutSession;
        $this->apiLicense = $apiLicense;
        $this->configWriter = $configWriter;
        $this->resultPageFactory = $resultPageFactory;
        $this->modelAccount = $modelAccount;
        $this->messageManager = $context->getMessageManager();
        $this->userModel = $userModel;
        $this->apiHandshake = $apiHandshake;
        $this->apiAccount = $apiAccount;
        $this->authSession = $authSession;
        $this->upgradePlugin = $apiManager;
        $this->carrier = $carrier;

        parent::__construct($context);
    }

    /**
     * SaveAccount apiOpenAccount
     *
     * @param string $username //The username
     *
     * @return string $trackingStatus
     */
    public function apiOpenAccount($username)
    {
        $this->data = $this->getRequest()->getParams();
        $userNameLocal = $this->authSession->getUser()->getUsername();
        $address = $this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSLINE1];
        if ($this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSLINE2] != "") {
            $address = $address . ', ' . $this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSLINE2];
        };
        if ($this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSLINE3] != "") {
            $address = $address . ', ' . $this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSLINE3];
        };

        $locate = explode('_', $this->userModel->loadByUsername($userNameLocal)->getData('interface_locale'));
        $languageCountry = $locate[0] . '_' . $this ->data[\UPS\Shipping\Helper\ConstantAccount::COUNTRYCODE];
        $dataOpenAccount = [
            "Locale" => \UPS\Shipping\Helper\CommonAPI::languageCodeForCountry($languageCountry),
            "EndUserInformation" => [
                \UPS\Shipping\Helper\ConstantAccount::USERNAME => $username,
                "VatTaxID" => (!empty($this->data['VATNumber'])) ? $this->data['VATNumber'] : '',
                "DeviceIdentity" => $this->data['ioBlackBox']
            ],
            "BillingAddress" => [
                "ContactName" => $this->data[\UPS\Shipping\Helper\ConstantAccount::CUSTOMERNAME],
                \UPS\Shipping\Helper\ConstantAccount::COMPANYNAME
                => $this->data[\UPS\Shipping\Helper\ConstantAccount::COMPANYNAME],
                "StreetAddress" => $address,
                "City" => $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTCITY],
                \UPS\Shipping\Helper\ConstantAccount::COUNTRYCODE
                => $this->data[\UPS\Shipping\Helper\ConstantAccount::COUNTRYCODE],
                \UPS\Shipping\Helper\ConstantAccount::STATEPROVINCECODE
                => (isset($this->data[\UPS\Shipping\Helper\ConstantAccount::STATEPROVINCECODE])) ? trim($this->data[\UPS\Shipping\Helper\ConstantAccount::STATEPROVINCECODE]) : '',
                \UPS\Shipping\Helper\ConstantAccount::POSTALCODE
                => $this->getPostalCode($this->data[\UPS\Shipping\Helper\ConstantAccount::POSTALCODE]),
                "Phone" => [
                    "Number" => $this->getPhones($this->data[\UPS\Shipping\Helper\ConstantAccount::PHONENUMBER])
                ]
            ],
            "PickupAddress" => [
                "ContactName" => $this->data[\UPS\Shipping\Helper\ConstantAccount::CUSTOMERNAME],
                \UPS\Shipping\Helper\ConstantAccount::COMPANYNAME
                => $this->data[\UPS\Shipping\Helper\ConstantAccount::COMPANYNAME],
                "StreetAddress" => $address,
                "City" => $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTCITY],
                \UPS\Shipping\Helper\ConstantAccount::COUNTRYCODE
                => $this->data[\UPS\Shipping\Helper\ConstantAccount::COUNTRYCODE],
                \UPS\Shipping\Helper\ConstantAccount::STATEPROVINCECODE
                => (isset($this->data[\UPS\Shipping\Helper\ConstantAccount::STATEPROVINCECODE])) ? trim($this->data[\UPS\Shipping\Helper\ConstantAccount::STATEPROVINCECODE]) : '',
                \UPS\Shipping\Helper\ConstantAccount::POSTALCODE
                => $this->getPostalCode($this->data[\UPS\Shipping\Helper\ConstantAccount::POSTALCODE]),
                "Phone" => [
                    "Number" => $this->getPhones($this->data[\UPS\Shipping\Helper\ConstantAccount::PHONENUMBER])
                ],
                \UPS\Shipping\Helper\ConstantAccount::EMAILADDRESS
                => $this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSEMAIL]
            ]
        ];
        $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::PRE_REGISTERED_PLUGIN_TOKEN);
        return json_decode($this->apiAccount->openAccount($dataOpenAccount, $bearerToken));
    }

    /**
     * SaveAccount license
     *
     * @param string $username //The username
     * @param string $password //The password
     * @param string $data     //The data
     *
     * @return string $trackingStatus
     */
    public function license($username, $password, $data)
    {
        $this->licenseModel->updateAccount($username, $password);
        $userName = $this->authSession->getUser()->getUsername();
        $interfaceLocale = \UPS\Shipping\Helper\ConstantAccount::INTERFACE_LOCALE;
        $locate = explode('_', $this->userModel->loadByUsername($userName)->getData($interfaceLocale));
        $shippingCountryCode = \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE;
        $request = [
            \UPS\Shipping\Helper\ConstantAccount::COMPANYNAME
            => $data[\UPS\Shipping\Helper\ConstantAccount::COMPANYNAME],
            "Address" => [
                "AddressLine1" => $data[\UPS\Shipping\Helper\ConstantAccount::ADDRESS]
                [\UPS\Shipping\Helper\ConstantAccount::ADDRESSLINE][0],
                "AddressLine2" => $data[\UPS\Shipping\Helper\ConstantAccount::ADDRESS]
                [\UPS\Shipping\Helper\ConstantAccount::ADDRESSLINE][1],
                "AddressLine3" => $data[\UPS\Shipping\Helper\ConstantAccount::ADDRESS]
                [\UPS\Shipping\Helper\ConstantAccount::ADDRESSLINE][2],
                "City" => $data[\UPS\Shipping\Helper\ConstantAccount::ADDRESS]['City'],
                "StateProvinceCode" => $data[\UPS\Shipping\Helper\ConstantAccount::ADDRESS][\UPS\Shipping\Helper\ConstantAccount::STATEPROVINCECODE],
                \UPS\Shipping\Helper\ConstantAccount::POSTALCODE
                => $this->getPostalCode($this->data[\UPS\Shipping\Helper\ConstantAccount::POSTALCODE]),
                \UPS\Shipping\Helper\ConstantAccount::COUNTRYCODE
                => $data[\UPS\Shipping\Helper\ConstantAccount::ADDRESS]
                [\UPS\Shipping\Helper\ConstantAccount::COUNTRYCODE],
            ],
            "PrimaryContact" => [
                "Name" => $data[\UPS\Shipping\Helper\ConstantAccount::CUSTOMERNAME],
                \UPS\Shipping\Helper\ConstantAccount::TITLE => $data[\UPS\Shipping\Helper\ConstantAccount::TITLE],
                \UPS\Shipping\Helper\ConstantAccount::EMAIL
                => $data[\UPS\Shipping\Helper\ConstantAccount::EMAILADDRESS],
                \UPS\Shipping\Helper\ConstantAccount::PHONENUMBER
                => $this->getPhones($data[\UPS\Shipping\Helper\ConstantAccount::PHONENUMBER]),
                \UPS\Shipping\Helper\ConstantAccount::FAXNUMBER => "",
            ],
            "SecondaryContact" => [
                "Name" => $data[\UPS\Shipping\Helper\ConstantAccount::CUSTOMERNAME],
                \UPS\Shipping\Helper\ConstantAccount::TITLE => $data[\UPS\Shipping\Helper\ConstantAccount::TITLE],
                \UPS\Shipping\Helper\ConstantAccount::EMAIL
                => $data[\UPS\Shipping\Helper\ConstantAccount::EMAILADDRESS],
                \UPS\Shipping\Helper\ConstantAccount::PHONENUMBER
                => $this->getPhones($data[\UPS\Shipping\Helper\ConstantAccount::PHONENUMBER]),
                \UPS\Shipping\Helper\ConstantAccount::FAXNUMBER => "",
            ],
            "AccessLicenseProfile" => [
                \UPS\Shipping\Helper\ConstantAccount::COUNTRYCODE => $this->scopeConfig->getValue($shippingCountryCode),
                \UPS\Shipping\Helper\ConstantAccount::LANGUAGECODE => $locate[0],
            ]
        ];
        $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::PRE_REGISTERED_PLUGIN_TOKEN);
        $license = json_decode($this->apiLicense->access2($request, $bearerToken));

        if (isset($license) && isset($license->AccessLicenseResponse)
            && isset($license->AccessLicenseResponse->AccessLicenseNumber)
        ) {
            $this->licenseModel->updateLicenseNumber($license->AccessLicenseResponse->AccessLicenseNumber);
            $this->setRegisteredToken($license->AccessLicenseResponse->AccessLicenseNumber, $username, $password);
        }
        return $license;
    }

    /**
     * SaveAccount apiLicense
     *
     * @param string $username //The username
     * @param string $password //The password
     *
     * @return string $trackingStatus
     */
    public function apiLicense($username, $password)
    {
        $this->licenseModel->updateAccount($username, $password);
        $this->data = $this->getRequest()->getParams();
        $userName = $this->authSession->getUser()->getUsername();
        $interfaceLocale = \UPS\Shipping\Helper\ConstantAccount::INTERFACE_LOCALE;
        $locate = explode('_', $this->userModel->loadByUsername($userName)->getData($interfaceLocale));
        $shippingCountryCode = \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE;
        $request = [
            \UPS\Shipping\Helper\ConstantAccount::COMPANYNAME
            => $this->data[\UPS\Shipping\Helper\ConstantAccount::COMPANYNAME],
            "Address" => [
                "AddressLine1" => $this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSLINE1],
                "AddressLine2" => $this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSLINE2],
                "AddressLine3" => $this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSLINE3],
                "City" => $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTCITY],
                \UPS\Shipping\Helper\ConstantAccount::STATEPROVINCECODE
                => (isset($this->data[\UPS\Shipping\Helper\ConstantAccount::STATEPROVINCECODE])) ? trim($this->data[\UPS\Shipping\Helper\ConstantAccount::STATEPROVINCECODE]) : '',
                \UPS\Shipping\Helper\ConstantAccount::POSTALCODE
                => $this->getPostalCode($this->data[\UPS\Shipping\Helper\ConstantAccount::POSTALCODE]),
                \UPS\Shipping\Helper\ConstantAccount::COUNTRYCODE
                => $this->data[\UPS\Shipping\Helper\ConstantAccount::COUNTRYCODE],
            ],
            "PrimaryContact" => [
                "Name" => $this->data[\UPS\Shipping\Helper\ConstantAccount::CUSTOMERNAME],
                \UPS\Shipping\Helper\ConstantAccount::TITLE
                => $this->data[\UPS\Shipping\Helper\ConstantAccount::TITLE],
                \UPS\Shipping\Helper\ConstantAccount::EMAIL
                => $this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSEMAIL],
                \UPS\Shipping\Helper\ConstantAccount::PHONENUMBER
                => $this->getPhones($this->data[\UPS\Shipping\Helper\ConstantAccount::PHONENUMBER]),
                \UPS\Shipping\Helper\ConstantAccount::FAXNUMBER => "",
            ],
            "SecondaryContact" => [
                "Name" => $this->data[\UPS\Shipping\Helper\ConstantAccount::CUSTOMERNAME],
                \UPS\Shipping\Helper\ConstantAccount::TITLE
                => $this->data[\UPS\Shipping\Helper\ConstantAccount::TITLE],
                \UPS\Shipping\Helper\ConstantAccount::EMAIL
                => $this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSEMAIL],
                \UPS\Shipping\Helper\ConstantAccount::PHONENUMBER
                => $this->getPhones($this->data[\UPS\Shipping\Helper\ConstantAccount::PHONENUMBER]),
                \UPS\Shipping\Helper\ConstantAccount::FAXNUMBER => "",
            ],
            "AccessLicenseProfile" => [
                \UPS\Shipping\Helper\ConstantAccount::COUNTRYCODE => $this->scopeConfig->getValue($shippingCountryCode),
                \UPS\Shipping\Helper\ConstantAccount::LANGUAGECODE => $locate[0],
            ]
        ];
        $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::PRE_REGISTERED_PLUGIN_TOKEN);
        $license = json_decode($this->apiLicense->access2($request, $bearerToken));
        if (isset($license) && isset($license->AccessLicenseResponse)
            && isset($license->AccessLicenseResponse->AccessLicenseNumber)
        ) {
            $this->licenseModel->updateLicenseNumber($license->AccessLicenseResponse->AccessLicenseNumber);
            $this->setRegisteredToken($license->AccessLicenseResponse->AccessLicenseNumber, $username, $password);
        }
        return $license;
    }

    /**
     * SaveAccount promoDiscountAgreement
     *
     * @param string $promoCodeData //The promoCodeData
     *
     * @return string $trackingStatus
     */
    public function promoDiscountAgreement($promoCodeData)
    {
        $userName = $this->authSession->getUser()->getUsername();
        $interfaceLocale = \UPS\Shipping\Helper\ConstantAccount::INTERFACE_LOCALE;
        $locate = explode('_', $this->userModel->loadByUsername($userName)->getData($interfaceLocale));
        $shippingCountryCode = \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE;
        $dataPromoAgreement = [
            \UPS\Shipping\Helper\ConstantAccount::USERNAME => $promoCodeData['username'],
            \UPS\Shipping\Helper\ConstantAccount::CONSTPASSSTRING => $promoCodeData['password'],
            \UPS\Shipping\Helper\ConstantAccount::ACCESSLICENSE => $promoCodeData['accessLicense'],
            \UPS\Shipping\Helper\ConstantAccount::PROMOCODE
            => $promoCodeData[\UPS\Shipping\Helper\ConstantAccount::PROMOCODE],
            \UPS\Shipping\Helper\ConstantAccount::COUNTRYCODE => $this->scopeConfig->getValue($shippingCountryCode),
            \UPS\Shipping\Helper\ConstantAccount::LANGUAGECODE => strtoupper($locate[0]),
        ];
        return json_decode($this->apiAccount->promoDiscountAgreement($dataPromoAgreement));
    }

    /**
     * SaveAccount promoDiscount
     *
     * @param string $promoData //The promoData
     *
     * @return string $trackingStatus
     */
    public function promoDiscount($promoData)
    {
        $userName = $this->authSession->getUser()->getUsername();
        $interfaceLocale = \UPS\Shipping\Helper\ConstantAccount::INTERFACE_LOCALE;
        $locate = explode('_', $this->userModel->loadByUsername($userName)->getData($interfaceLocale));
        $shippingCountryCode = \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE;
        $dataPromo = [
            \UPS\Shipping\Helper\ConstantAccount::USERNAME => $promoData['username'],
            \UPS\Shipping\Helper\ConstantAccount::CONSTPASSSTRING => $promoData['password'],
            \UPS\Shipping\Helper\ConstantAccount::ACCESSLICENSE => $promoData['accessLicense'],
            \UPS\Shipping\Helper\ConstantAccount::AGREEMENTACCEPTANCECODE => $promoData['AgreementAcceptanceCode'],
            "AccountNumber" => $promoData[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNUMBER],
            \UPS\Shipping\Helper\ConstantAccount::PROMOCODE
            => $promoData[\UPS\Shipping\Helper\ConstantAccount::PROMOCODE],
            \UPS\Shipping\Helper\ConstantAccount::COUNTRYCODE => $this->scopeConfig->getValue($shippingCountryCode),
            \UPS\Shipping\Helper\ConstantAccount::LANGUAGECODE => strtoupper($locate[0]),
        ];

        return json_decode($this->apiAccount->promoDiscount($dataPromo));
    }

    /**
     * SaveAccount getPackageStatus
     * only get number for Phone
     *
     * @param string $phoneNumber //The phoneNumber
     *
     * @return string $trackingStatus
     */
    public function getPhones($phoneNumber)
    {
        preg_match_all('/\d+/', $phoneNumber, $matches);
        return implode("", $matches[0]);
    }

    /**
     * SaveAccount getPackageStatus
     * only get number and characters
     *
     * @param string $postalCode //The postalCode
     *
     * @return string $trackingStatus
     */
    public function getPostalCode($postalCode)
    {
        return trim($postalCode);
    }

    /**
     * SaveAccount execute
     *
     * @return null
     */
    public function execute()
    {
        $result = $this->resultPageFactory->create();
        $this->data = $this->getRequest()->getParams();
        $message = __('Some of the data you entered is not valid. Please check again.');
        if ($this->checkValidate()) {
            if (!$this->checkValidateOption()) {
                $this->session->start();
                $this->session->setMessage($this->data);
                $this->messageManager->addErrorMessage($message);
                $this->_redirect(\UPS\Shipping\Helper\ConstantAccount::LINKCOUNT);
            } else {
                $stateProvice = (isset($this->data[\UPS\Shipping\Helper\ConstantAccount::STATEPROVINCECODE]) ? $this->data[\UPS\Shipping\Helper\ConstantAccount::STATEPROVINCECODE] : 'XX');
                $dataAccount = [
                    'title' => $this->data[\UPS\Shipping\Helper\ConstantAccount::TITLE],
                    'fullname' => $this->data[\UPS\Shipping\Helper\ConstantAccount::CUSTOMERNAME],
                    'company' => $this->data[\UPS\Shipping\Helper\ConstantAccount::COMPANYNAME],
                    'email' => $this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSEMAIL],
                    'phone_number' => $this->getPhones($this->data[\UPS\Shipping\Helper\ConstantAccount::PHONENUMBER]),
                    'address_type' => $this->data['AddressType'],
                    'address_1' => $this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSLINE1],
                    'address_2' => $this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSLINE2],
                    'address_3' => $this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSLINE3],
                    'StateProvinceCode' => $this->getPostalCode($stateProvice),
                    'post_code' => $this->getPostalCode($this->data[\UPS\Shipping\Helper\ConstantAccount::POSTALCODE]),
                    'city' => $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTCITY],
                    'country' => $this->data[\UPS\Shipping\Helper\ConstantAccount::COUNTRYCODE],
                    'account_type' => $this->data[\UPS\Shipping\Helper\ConstantAccount::OPTRADIO],
                    'deviceIdentity' => $this->data['ioBlackBox'],
                    'account_default' => 1
                ];

                $errorMessage = '';
                $errorCode = false;

                if ($this->data[\UPS\Shipping\Helper\ConstantAccount::OPTRADIO] == 1) {
                    $this->selectOptionOne($dataAccount, $errorCode, $errorMessage);
                } elseif ($this->data[\UPS\Shipping\Helper\ConstantAccount::OPTRADIO] == 2) {
                    $this->selectOptionTwo($dataAccount, $errorCode, $errorMessage);
                } elseif ($this->data[\UPS\Shipping\Helper\ConstantAccount::OPTRADIO] == 0) {
                    $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::PRE_REGISTERED_PLUGIN_TOKEN);
                    $dataFormat = $this->apiAccount->reformatData($dataAccount);
                    $responseData = $this->apiAccount->registration($dataFormat, $bearerToken);
                    $responseData[0] = json_decode($responseData[0]);
                    if (empty($responseData[0])) {
                        $this->reGetToken($responseData, $dataFormat);
                    }
                    $promoCodeInput = (isset($this->data[\UPS\Shipping\Helper\ConstantAccount::PROMOCODE])) ? trim($this->data[\UPS\Shipping\Helper\ConstantAccount::PROMOCODE]) : '';
                    $usCountry = strtolower($this->data[\UPS\Shipping\Helper\ConstantAccount::COUNTRYCODE]);
                    if ('us' == $usCountry) {
                        $promoCodeInput = \UPS\Shipping\Helper\ConstantAccount::PROMO_CODE_STRING;
                    }
                    if (isset($responseData[0]->RegisterResponse->Response->ResponseStatus->Code)
                        && $responseData[0]->RegisterResponse->Response->ResponseStatus->Code == 1
                    ) {
                        //call  Open Account API
                        $responseOpenAccount = $this->apiOpenAccount($responseData[1]);
                        $this->programPromoAPI($responseOpenAccount, $dataAccount, $promoCodeInput, $responseData, $errorCode, $errorMessage);
                    } else {
                        if (isset($responseData[0]) && isset($responseData[0]->Fault)) {
                            $errorMessage
                                = $responseData[0]->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description;
                        } else {
                            $errorMessage = $responseData[1];
                        }
                    }
                }
                if ($errorCode == false) {
                    $this->session->start();
                    $this->session->setMessage($this->data);
                    $this->messageManager->addErrorMessage($errorMessage);
                    $this->_redirect(\UPS\Shipping\Helper\ConstantAccount::LINKCOUNT);
                }
            }
        } else {
            $this->session->start();
            $this->session->setMessage($this->data);
            $this->messageManager->addErrorMessage($message);
            $this->_redirect(\UPS\Shipping\Helper\ConstantAccount::LINKCOUNT);
        }
    }

    /**
     * SaveAccount programPromoAPI
     *
     * @param array  $responseOpenAccount //The dataAccount
     * @param string $dataAccount         //The dataAccount
     * @param string $promoCodeInput      //The promoCodeInput
     * @param string $responseData        //The responseData
     * @param string $errorCode           //The errorCode
     * @param string $errorMessage        //The errorMessage
     *
     * @return null
     */
    public function programPromoAPI($responseOpenAccount, $dataAccount, $promoCodeInput, $responseData, &$errorCode, &$errorMessage)
    {
        $promo = $this->runPromoAPI($responseOpenAccount, $dataAccount, $promoCodeInput, $responseData);
        $errorCode = true;
        if ($promo && !$promo[\UPS\Shipping\Helper\ConstantAccount::ERRORCODE]) {
            $errorCode = false;
            $errorMessage = $promo[\UPS\Shipping\Helper\ConstantAccount::ERRORMESSAGE];
        }
    }

    /**
     * SaveAccount selectOptionOne
     *
     * @param array  $dataAccount  //The dataAccount
     * @param string $errorCode    //The errorCode
     * @param string $errorMessage //The errorMessage
     *
     * @return null
     */
    public function selectOptionOne($dataAccount, &$errorCode, &$errorMessage)
    {
        $date = date('Y-m-d', strtotime(str_replace('-', '/', $this->data['InvoiceDate'])));
        $dataOption = [
            'ups_account_name' => $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNAME],
            \UPS\Shipping\Helper\ConstantAccount::UPSACCOUNTNUMBER
            => $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNUMBER],
            'ups_invoice_number' => $this->data['InvoiceNumber'],
            'ups_account_account' => $this->data['InvoiceAmount'],
            'ups_currency' => $this->data['Currency'],
            'ups_invoice_date' => $date,
        ];
        $accountNumber = $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNUMBER];
        $existAccount = $this->accountModel->checkAccount($accountNumber);
        if (empty($existAccount)) {
            $dataAccount = array_merge($dataAccount, $dataOption);
            $usCountry = strtolower($this->data[\UPS\Shipping\Helper\ConstantAccount::COUNTRYCODE]);
            if (\UPS\Shipping\Helper\Config::LOWER_CONFIG_COUNTRY_US == $usCountry && !empty($this->data['ControlId'])) {
                $dataAccount['ControlID'] = $this->data['ControlId'];
            }
            $dataFormat = $this->apiAccount->reformatData($dataAccount);
            $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::PRE_REGISTERED_PLUGIN_TOKEN);
            $responseData = $this->apiAccount->registration($dataFormat, $bearerToken);
            $responseData[0] = json_decode($responseData[0]);
            if (empty($responseData[0])) {
                $this->reGetToken($responseData, $dataFormat);
            }
            if (\UPS\Shipping\Helper\Config::LOWER_CONFIG_COUNTRY_US == $usCountry && isset($dataFormat['ControlID'])) {
                unset($dataFormat['ControlID']);
            }
            $responseOptionOne = $this->saveAccountOption($responseData, $dataFormat, $dataAccount);
            $errorCode = true;
            if ($responseOptionOne && !$responseOptionOne[\UPS\Shipping\Helper\ConstantAccount::ERRORCODE]
            ) {
                $errorCode = false;
                $errorMessage = $responseOptionOne[\UPS\Shipping\Helper\ConstantAccount::ERRORMESSAGE];
            }
        }
    }

    /**
     * SaveAccount selectOptionTwo
     *
     * @param array  $dataAccount  //The dataAccount
     * @param string $errorCode    //The errorCode
     * @param string $errorMessage //The errorMessage
     *
     * @return null
     */
    public function selectOptionTwo($dataAccount, &$errorCode, &$errorMessage)
    {
        $dataOption = [
            'ups_account_name' => $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNAME1],
            \UPS\Shipping\Helper\ConstantAccount::UPSACCOUNTNUMBER
            => $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNUMBER1],
        ];
        $accountNumber1 = $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNUMBER1];
        $existAccount = $this->accountModel->checkAccount($accountNumber1);
        if (empty($existAccount)) {
            $dataAccount = array_merge($dataAccount, $dataOption);
            $dataFormat = $this->apiAccount->reformatData($dataAccount);
            $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::PRE_REGISTERED_PLUGIN_TOKEN);
            $responseData = $this->apiAccount->registration($dataFormat, $bearerToken);
            $responseData[0] = json_decode($responseData[0]);
            if (empty($responseData[0])) {
                $this->reGetToken($responseData, $dataFormat);
            }
            $responseOptionTwo = $this->saveAccountOption($responseData, $dataFormat, $dataAccount);
            $errorCode = true;
            if ($responseOptionTwo && !$responseOptionTwo[\UPS\Shipping\Helper\ConstantAccount::ERRORCODE]
            ) {
                $errorCode = false;
                $errorMessage = $responseOptionTwo[\UPS\Shipping\Helper\ConstantAccount::ERRORMESSAGE];
            }
        }
    }


    /**
     * SaveAccount reGetToken
     *
     * @param string $responseData //The responseData
     * @param array  $dataFormat   //The dataFormat
     *
     * @return null
     */
    public function reGetToken(&$responseData, $dataFormat)
    {
        $this->callAPIHandshake();
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
        $responseData = $this->apiAccount->registration($dataFormat, $bearerToken);
        $responseData[0] = json_decode($responseData[0]);
    }


    /**
     * SaveAccount runPromoAPI
     *
     * @param string $responseOpenAccount //The responseOpenAccount
     * @param string $dataAccount         //The dataAccount
     * @param string $promoCodeInput      //The promoCodeInput
     * @param string $responseData        //The responseData
     *
     * @return null
     */
    public function runPromoAPI($responseOpenAccount, $dataAccount, $promoCodeInput, $responseData)
    {
        $errorCode = false;
        $errorMessage = '';
        if (isset($responseOpenAccount->OpenAccountResponse->Response->ResponseStatus->Code)
            && $responseOpenAccount->OpenAccountResponse->Response->ResponseStatus->Code == 1
            && !isset($responseOpenAccount->OpenAccountResponse->PickupAddressCandidate)
        ) {
            // call Access License API
            $responseLicense = $this->apiLicense($responseData[1], $responseData[2]);
            if (isset($responseLicense->AccessLicenseResponse->Response->ResponseStatus->Code)
                && $responseLicense->AccessLicenseResponse->Response->ResponseStatus->Code == 1
            ) {
                $errorCode = true;
                if (isset($responseOpenAccount->OpenAccountResponse->ShipperNumber)) {
                    $dataAccount[\UPS\Shipping\Helper\ConstantAccount::UPSACCOUNTNUMBER]
                        = $responseOpenAccount->OpenAccountResponse->ShipperNumber;
                }
                $this->modelAccount->saveAccount($dataAccount);
                $this->configWriter->save(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_ACCOUNT, 1);
                foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
                    $this->cacheTypeList->cleanType($type);
                }
                if (!empty($promoCodeInput)) {
                    // call Promo Discount Agreement API
                    $promoCodeAgreement = [
                        \UPS\Shipping\Helper\ConstantAccount::USERNAME => $responseData[1],
                        \UPS\Shipping\Helper\ConstantAccount::CONSTPASSSTRING => $responseData[2],
                        // "accessLicense" => '6D54DCFD5EBA7608',
                        \UPS\Shipping\Helper\ConstantAccount::ACCESSLICENSE
                        => $responseLicense->AccessLicenseResponse->AccessLicenseNumber,
                        \UPS\Shipping\Helper\ConstantAccount::PROMOCODE => $promoCodeInput
                    ];
                    $responsePromoAgreement = $this->promoDiscountAgreement($promoCodeAgreement);
                    if (isset($responsePromoAgreement->PromoDiscountAgreementResponse->Response->ResponseStatus->Code)
                        && $responsePromoAgreement->PromoDiscountAgreementResponse->Response->ResponseStatus->Code == 1
                    ) {
                        // call  Promo Discount API
                        $promoCodeDataPrepare = [
                            \UPS\Shipping\Helper\ConstantAccount::USERNAME => $responseData[1],
                            \UPS\Shipping\Helper\ConstantAccount::CONSTPASSSTRING => $responseData[2],
                            // "accessLicense" => '6D54DCFD5EBA7608',
                                \UPS\Shipping\Helper\ConstantAccount::ACCESSLICENSE
                                => $responseLicense->AccessLicenseResponse->AccessLicenseNumber,
                            // "AgreementAcceptanceCode" => 'C63_01_17_2018-12-31',
                            \UPS\Shipping\Helper\ConstantAccount::AGREEMENTACCEPTANCECODE
                            => $responsePromoAgreement->PromoDiscountAgreementResponse->PromoAgreement->AcceptanceCode,
                            // "AccountNumber" => '63V3F1',
                            "AccountNumber" => $responseOpenAccount->OpenAccountResponse->ShipperNumber,
                            \UPS\Shipping\Helper\ConstantAccount::PROMOCODE => $promoCodeInput
                        ];
                        $responsePromoDiscount = $this->promoDiscount($promoCodeDataPrepare);
                        if (!empty($responsePromoDiscount->PromoDiscountResponse->Response->ResponseStatus->Code)
                            && $responsePromoDiscount->PromoDiscountResponse->Response->ResponseStatus->Code == 1
                        ) {
                            $successMessage = \UPS\Shipping\Helper\ConstantAccount::PROMO_DISCOUNT_STRING . ' ' . $responsePromoDiscount->PromoDiscountResponse->Response->ResponseStatus->Description;
                            $this->session->setSuccessMessage($successMessage);
                        } else {
                            $errorMessage = $responsePromoDiscount->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description;
                            $this->session->setErrorMessage($errorMessage);
                        }
                    } else {
                        $errorMessage = $responsePromoAgreement->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description;
                        $this->session->setErrorMessage($errorMessage);
                    }
                }
                $this->_redirect(\UPS\Shipping\Helper\ConstantAccount::LINKACCOUNTSUCCESS);
            } else {
                $errorMessage = $responseLicense->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description;
            }
        } else {
            if (isset($responseOpenAccount->Fault)) {
                $errorMessage = $responseOpenAccount->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description;
            } else {
                $errorMessage = __("Your request to create a UPS Shipping Account was not successful. Please check the 'City' and 'Postal code' values that you entered. Here are some suggestions on the City and Postal code that are a valid combination - ");
                if ($responseOpenAccount && isset($responseOpenAccount->OpenAccountResponse)
                    && isset($responseOpenAccount->OpenAccountResponse->PickupAddressCandidate)
                ) {
                    $arrPickupAddressCandidate = [];
                    $itemPickupAddressCandidate = $responseOpenAccount->OpenAccountResponse->PickupAddressCandidate;
                    $arrPickupAddressCandidate = $this->getArrayPickupAddress($itemPickupAddressCandidate);
                    foreach ($arrPickupAddressCandidate as $item) {
                        $errorMessage .= '"' . $item->City . ', ' . $item->PostalCode . '";';
                    }
                    $errorMessage = rtrim($errorMessage, ";");
                    $errorMessage .= '. ';
                }
                $errorMessage .= __("Please update the city and postal code values with one of these selections and click Get Started");
                $this->data['submit_account'] = 'PickupAddressCandidate';
            }
        }
        return [
            \UPS\Shipping\Helper\ConstantAccount::ERRORCODE => $errorCode,
            \UPS\Shipping\Helper\ConstantAccount::ERRORMESSAGE => $errorMessage
        ];
    }

    /**
     * SaveAccount saveAccountOption
     * select option
     *
     * @param string $itemPickupAddressCandidate //The itemPickupAddressCandidate
     *
     * @return string error
     */
    public function getArrayPickupAddress($itemPickupAddressCandidate)
    {
        $arrPickupAddressCandidate = [];
        if ($itemPickupAddressCandidate && !is_array($itemPickupAddressCandidate)) {
            $arrPickupAddressCandidate[] = $itemPickupAddressCandidate;
        } else {
            $arrPickupAddressCandidate = $itemPickupAddressCandidate;
        }
        return $arrPickupAddressCandidate;
    }

    /**
     * SaveAccount saveAccountOption
     * select option
     *
     * @param string $responseData //The responseData
     * @param string $dataFormat   //The dataFormat
     * @param string $dataAccount  //The dataAccount
     *
     * @return string error
     */
    public function saveAccountOption($responseData, $dataFormat, $dataAccount)
    {
        $errorCode = false;
        $errorMessage = '';
        if (isset($responseData[0]->RegisterResponse->Response->ResponseStatus->Code)
            && $responseData[0]->RegisterResponse->Response->ResponseStatus->Code == 1
        ) {
            $checkAPI = $this->checkSuccessAPI($responseData[0]->RegisterResponse->ShipperAccountStatus);
            if (isset($checkAPI[0]) && $checkAPI[0] == true) {
                $responseLicense = $this->license($responseData[1], $responseData[2], $dataFormat);
                if (isset($responseLicense->AccessLicenseResponse->Response->ResponseStatus->Code)
                    && $responseLicense->AccessLicenseResponse->Response->ResponseStatus->Code == 1
                ) {
                    $errorCode = true;
                    $this->modelAccount->saveAccount($dataAccount);
                    $this->configWriter->save(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_ACCOUNT, 1);
                    foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
                        $this->cacheTypeList->cleanType($type);
                    }
                    $this->_redirect(\UPS\Shipping\Helper\ConstantAccount::LINKACCOUNTSUCCESS);
                } else {
                    if (isset($responseLicense->Fault)) {
                        $errorMessage = $responseLicense->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description;
                    } else {
                        $errorMessage = __('Cannot create new Account Number');
                    }
                }
            } else {
                $errorMessage = $checkAPI[1];
            }
        } else {
            if (isset($responseData[0]) && isset($responseData[0]->Fault)) {
                $errorMessage = $responseData[0]->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description;
            } else {
                $errorMessage = $responseData[1];
            }
        }
        return [
            \UPS\Shipping\Helper\ConstantAccount::ERRORCODE => $errorCode,
            \UPS\Shipping\Helper\ConstantAccount::ERRORMESSAGE => $errorMessage
        ];
    }

    /**
     * SaveAccount checkSuccessAPI
     * select option
     *
     * @param string $responseData //The responseData
     *
     * @return boolean
     */
    public function checkSuccessAPI($responseData)
    {
        $checkAPI = [];
        if (is_array($responseData)) {
            foreach ($responseData as $item) {
                if (isset($item->Code)) {
                    $checkAPI[] = [
                        "Code" => $item->Code,
                        "Description" => $item->Description,
                    ];
                    $checkAPI = json_decode(json_encode($checkAPI));
                } else {
                    $checkAPI = $item;
                };
            }
        } else {
            if (isset($responseData->Code)) {
                $checkAPI[] = [
                    "Code" => $responseData->Code,
                    "Description" => $responseData->Description,
                ];
                $checkAPI = json_decode(json_encode($checkAPI));
            } else {
                $checkAPI = $responseData;
            };
        }
        $errorMessage = [];
        $successAPI = 0;
        $codeSuccess = ["010", "012", "040", "42"];
        foreach ($checkAPI as $key => $value) {
            if (in_array((string)$value->Code, $codeSuccess)) {
                $successAPI++;
            } else {
                $errorMessage[] = $value->Description;
            }
        }
        if ($successAPI > 0) {
            $check = true;
            $errorMessage = '';
        } else {
            $check = false;
            $errorMessage = implode(", ", $errorMessage);
        }
        return [$check, $errorMessage];
    }

    /**
     * SaveAccount checkValidate
     * required validate
     *
     * @return boolean
     */
    public function checkValidate()
    {
        $customerName = $this->data[\UPS\Shipping\Helper\ConstantAccount::CUSTOMERNAME];
        $postCode = $this->data[\UPS\Shipping\Helper\ConstantAccount::POSTALCODE];
        $companyName = $this->data[\UPS\Shipping\Helper\ConstantAccount::COMPANYNAME];
        $addressLine1 = $this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSLINE1];
        $accountCity = $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTCITY];
        if ($this->data && (!empty($customerName) || $customerName == '0')
            && filter_var($this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSEMAIL], FILTER_VALIDATE_EMAIL)
            && (!empty($postCode) || $postCode == '0') && (!empty($companyName) || $companyName == '0')
            && (!empty($addressLine1) || $addressLine1 == '0') && (!empty($accountCity) || $accountCity == '0')
        ) {
            return true;
        }
        return false;
    }

    /**
     * SaveAccount checkValidateOption
     * required validate
     *
     * @return boolean
     */
    public function checkValidateOption()
    {
        $this->regex = \UPS\Shipping\Helper\Config::REGEX_VALIDATE;
        $conditionAccountNumber = $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNUMBER];
        $conditionAccountNumber1 = $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNUMBER1];
        $accountName = $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNAME];
        $accountName1 = $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNAME1];
        if ($this->data[\UPS\Shipping\Helper\ConstantAccount::OPTRADIO] == 2) {
            if (!$this->validateAccount($conditionAccountNumber1, $this->regex['accountNumber'])
                || (empty($accountName1) & $accountName1 != '0')
            ) {
                return false;
            }
        } elseif ($this->data[\UPS\Shipping\Helper\ConstantAccount::OPTRADIO] == 1
            && (!$this->validateAccount($conditionAccountNumber, $this->regex['accountNumber'])
            || (empty($accountName) && $accountName !== '0')
            || !$this->validateAccount($this->data['InvoiceNumber'], $this->regex['invoiceNumber'])
            || !$this->validateAccount($this->data['InvoiceAmount'], $this->regex['invoiceAmount'])
            || !$this->validateAccount($this->data['InvoiceDate'], $this->regex['date']))
        ) {
            return false;
        } else {
            return true;
        }
        return true;
    }

    /**
     * SaveAccount validateAccount
     * required validate
     *
     * @param string $inputAccount //The inputAccount
     * @param string $validate     //The validate
     *
     * @return boolean
     */
    public function validateAccount($inputAccount, $validate)
    {
        if (preg_match($validate, $inputAccount)) {
            return true;
        }
        return false;
    }

    /**
     * SaveAccount setRegisteredToken
     *
     * @param string $license  //The license
     * @param string $username //The username
     * @param string $password //The password
     *
     * @return string $trackingStatus
     */
    public function setRegisteredToken($license, $username, $password)
    {
        $websiteMerchant = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_WEBSECUREURL);
        $valueSecurityToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN);
        $merchantKey = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY);
        $arrHandshakeParams = [
            "MerchantKey" => $merchantKey,
            "WebstoreUrl" => $websiteMerchant,
            "WebstoreUpsServiceLinkSecurityToken" => $valueSecurityToken,
            "WebstorePlatform" => 'Magento',
            "WebstorePlatformVersion" => \UPS\Shipping\Helper\Config::VERSION_FLATFORM,
            "UpsReadyPluginName" => \UPS\Shipping\Helper\Config::UPS_SHIPPING_MODULE,
            "UpsReadyPluginVersion" => \UPS\Shipping\Helper\Config::VERSION_PLUGIN,
            "WebstoreUpsServiceLinkUrl" => $websiteMerchant . \UPS\Shipping\Helper\Config::API_URL,
            "Username" => $username,
            "Password" => $password,
            "AccessLicenseNumber" => $license
        ];
        // Long bearer token
        $responseLongToken = $this->apiHandshake->registeredPluginToken($arrHandshakeParams);
        if ($responseLongToken) {
            $responseLongToken = json_decode($responseLongToken);
            // save long token
            $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN,  $responseLongToken->data);

            // save UPS_BING_MAPS_KEY
            $responseUpsBingMapsKey = $this->apiAccount->getUpsBingMapsKey($responseLongToken->data);
            if ($responseUpsBingMapsKey) {
                $responseUpsBingMapsKey = json_decode($responseUpsBingMapsKey);
                // save long token
                $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_BING_MAPS_KEY,  $responseUpsBingMapsKey->data);
            }
            //callUpgradePluginVersion
            $data = [
                \UPS\Shipping\Helper\ConstantManager::BEARERTOKEN => $responseLongToken->data,
                'merchantKey' => $merchantKey,
            ];
            $this->upgradePlugin->callUpgradePluginVersion($data);
        }

        foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
            $this->cacheTypeList->cleanType($type);
        }
    }

    /**
     * TermCondition callAPILicense
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
}
