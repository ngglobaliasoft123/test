<?php
/**
 * SaveAccountSuccess file
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
 * SaveAccountSuccess class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class SaveAccountSuccess extends \Magento\Framework\App\Action\Action
{
    protected $messageManager;
    protected $resultPageFactory;
    protected $resultJsonFactory;
    protected $modelAccount;
    protected $regex;
    protected $data;
    protected $apiAccount;
    protected $session;
    protected $modelService;
    protected $scopeConfig;
    protected $configWriter;
    protected $cacheTypeList;
    protected $modelPackage;
    protected $apiManager;
    protected $modelAccessorial;
    protected $modelDeliveryRates;
    protected $checkoutSession;
    protected $storeManager;
    protected $apiHandshake;
    protected $licenseModel;
    /**
     * SaveAccountSuccess execute
     *
     * @param string $context            //The context
     * @param string $resultPageFactory  //The resultPageFactory
     * @param string $resultJsonFactory  //The resultJsonFactory
     * @param string $scopeConfig        //The scopeConfig
     * @param string $configWriter       //The configWriter
     * @param string $modelAccount       //The modelAccount
     * @param string $modelPackage       //The modelPackage
     * @param string $apiAccount         //The apiAccount
     * @param string $modelService       //The modelService
     * @param string $modelAccessorial   //The modelAccessorial
     * @param string $checkoutSession    //The checkoutSession
     * @param string $modelDeliveryRates //The modelDeliveryRates
     * @param string $apiManager         //The apiManager
     * @param string $session            //The session
     * @param string $cacheTypeList      //The cacheTypeList
     * @param string $storeManager       //The storeManager
     * @param string $apiHandshake       //The apiHandshake
     * @param string $modelLicense       //modelLicense
     *
     * @return null
     */
    public function __construct(\Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \UPS\Shipping\Model\Account $modelAccount,
        \UPS\Shipping\Model\Package $modelPackage,
        \UPS\Shipping\API\Account $apiAccount,
        \UPS\Shipping\Model\Service $modelService,
        \UPS\Shipping\Model\Accessorial $modelAccessorial,
        \Magento\Checkout\Model\Session $checkoutSession,
        \UPS\Shipping\Model\DeliveryRates $modelDeliveryRates,
        \UPS\Shipping\API\Manager\Index $apiManager,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \UPS\Shipping\API\Handshake $apiHandshake,
        \UPS\Shipping\Model\License $modelLicense
    ) {
        $this->storeManager = $storeManager;
        $this->modelPackage = $modelPackage;
        $this->cacheTypeList = $cacheTypeList;
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
        $this->modelService = $modelService;
        $this->session = $session;
        $this->modelDeliveryRates = $modelDeliveryRates;
        $this->apiAccount = $apiAccount;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->modelAccount = $modelAccount;
        $this->messageManager = $context->getMessageManager();
        $this->checkoutSession = $checkoutSession;
        $this->modelAccessorial = $modelAccessorial;
        $this->apiManager = $apiManager;
        $this->apiHandshake = $apiHandshake;
        $this->licenseModel = $modelLicense;

        parent::__construct($context);
    }
    /**
     * SaveAccountSuccess execute
     *
     * @return null
     */
    public function execute()
    {
        $this->data = $this->getRequest()->getParams();
        $message = __('Some of the data you entered is not valid. Please check again.');
        $accountnumber = $this->getAccountNumber();
        if ($this->getRequest()->isAjax()) {
            $result = $this->resultJsonFactory->create();
            $methodAjax = $this->getRequest()->getParam('method');
            if ($methodAjax == 'deleteAccount') {
                $idAccount = $this->getRequest()->getParam('account_id');
                // call Plugin manager
                // offPluginManager 2019-03-18
                $this->callPluginManager($idAccount);
                // end call API
                $this->endCallApi($idAccount);
                return $result->setData(['result' => $this->_deleteAccount($idAccount)]);
            } else {
                return null;
            }
        } else {
            if ($this->modelAccount->checkAccount($accountnumber) == false) {
                $result = $this->resultPageFactory->create();
                if ($this->checkValidate()) {
                    if (!$this->checkValidateOption()) {
                        $this->session->start();
                        $this->session->setMessage($this->data);
                        $this->messageManager->addErrorMessage($message);
                        $this->_redirect(\UPS\Shipping\Helper\ConstantAccount::LINKACCOUNTSUCCESS);
                    } else {
                        //get 4 columns of account default
                        $defaultAccount = $this->modelAccount->getAccountDefault();
                        $phoneNumberString = $this->data[\UPS\Shipping\Helper\ConstantAccount::PHONENUMBER];
                        $addressLineString1 = \UPS\Shipping\Helper\ConstantAccount::ADDRESSLINE1;
                        $stateProviceCode = (isset($this->data[\UPS\Shipping\Helper\ConstantAccount::STATEPROVINCECODE])? $this->getPostalCode($this->data[\UPS\Shipping\Helper\ConstantAccount::STATEPROVINCECODE]) : 'XX');
                        $dataAccount = [
                            'title' => $defaultAccount['title'],
                            'fullname' => $defaultAccount['fullname'],
                            \UPS\Shipping\Helper\ConstantAccount::COMPANY
                            => $defaultAccount[\UPS\Shipping\Helper\ConstantAccount::COMPANY],
                            'email' => $defaultAccount['email'],
                            'address_type' => $this->data['AddressType'],
                            'ups_account_name' => $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNAME],
                            \UPS\Shipping\Helper\ConstantAccount::ADDRESS_1 => $this->data[$addressLineString1],
                            \UPS\Shipping\Helper\ConstantAccount::ADDRESS_2 => $this->data['AddressLine2'],
                            \UPS\Shipping\Helper\ConstantAccount::ADDRESS_3 => $this->data['AddressLine3'],
                            \UPS\Shipping\Helper\ConstantAccount::STATEPROVINCECODE => $stateProviceCode,
                            \UPS\Shipping\Helper\ConstantAccount::POST_CODE
                            => $this->getPostalCode($this->data[\UPS\Shipping\Helper\ConstantAccount::POSTALCODE]),
                            'city' => $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTCITY],
                            \UPS\Shipping\Helper\ConstantAccount::COUNTRY => $this->data['CountryCode'],
                            'phone_number' => $this->getPhones($phoneNumberString),
                            'account_type' => $this->data[\UPS\Shipping\Helper\ConstantAccount::OPTRADIO],
                            \UPS\Shipping\Helper\ConstantAccount::ACCOUNT_DEFAULT => 0
                        ];
                        // get account default
                        $dataDefault = $this->modelAccount->getAccountDefault();
                        $errorCode = false;
                        if ($this->data[\UPS\Shipping\Helper\ConstantAccount::OPTRADIO] == 1) {
                            $date = date('Y-m-d', strtotime(str_replace('-', '/', $this->data['InvoiceDate'])));
                            $dataOption = [
                                \UPS\Shipping\Helper\ConstantAccount::UPS_ACCOUNT_NUMBER
                                => $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNUMBER],
                                'ups_invoice_number' => $this->data['InvoiceNumber'],
                                'ups_account_account' => $this->data['InvoiceAmount'],
                                'ups_currency' => $this->data['Currency'],
                                'ups_invoice_date' => $date,
                            ];
                            $dataAccount = array_merge($dataAccount, $dataOption);
                            $usCountry = strtolower($this->data[\UPS\Shipping\Helper\ConstantAccount::COUNTRYCODE]);
                            if (\UPS\Shipping\Helper\Config::LOWER_CONFIG_COUNTRY_US == $usCountry && !empty($this->data['ControlId'])) {
                                $dataAccount['ControlID'] = $this->data['ControlId'];
                            }
                            // check option account
                            $this->checkOption($errorCode, $dataAccount, $errorMessage);
                        }

                        if ($this->data[\UPS\Shipping\Helper\ConstantAccount::OPTRADIO] == 2) {
                            $dataOption = [
                                \UPS\Shipping\Helper\ConstantAccount::UPS_ACCOUNT_NUMBER
                                => $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNUMBER1],
                            ];
                            $dataAccount = array_merge($dataAccount, $dataOption);
                            // check option account
                            $this->checkOption($errorCode, $dataAccount, $errorMessage);
                        }
                        if ($errorCode == false) {
                            $this->session->start();
                            $this->data[\UPS\Shipping\Helper\ConstantAccount::BORDERACCOUNTRED] = '';
                            $this->session->setMessage($this->data);
                            $this->messageManager->addErrorMessage($errorMessage);
                            $this->_redirect(\UPS\Shipping\Helper\ConstantAccount::LINKACCOUNTSUCCESS);
                        }
                    }
                } else {
                    $this->session->start();
                    $this->data[\UPS\Shipping\Helper\ConstantAccount::BORDERACCOUNTRED] = '';
                    $this->session->setMessage($this->data);
                    $this->messageManager->addErrorMessage($message);
                    $this->_redirect(\UPS\Shipping\Helper\ConstantAccount::LINKACCOUNTSUCCESS);
                }
            } else {
                $this->session->start();
                $this->data[\UPS\Shipping\Helper\ConstantAccount::BORDERACCOUNTRED] = 'admin__field-error';
                $this->session->setMessage($this->data);
                $messageExist = __("Account number already exists in 'Your Payment Account'");
                $this->messageManager->addErrorMessage($messageExist);
                $this->_redirect(\UPS\Shipping\Helper\ConstantAccount::LINKACCOUNTSUCCESS);
            }
        }
    }


    /**
     * Function getAccountNumber
     *
     * @return boolean
     */
    public function getAccountNumber()
    {
        $accountnumber = '';
        if (isset($this->data[\UPS\Shipping\Helper\ConstantAccount::OPTRADIO])
            && $this->data[\UPS\Shipping\Helper\ConstantAccount::OPTRADIO] == 2
        ) {
            $accountnumber = $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNUMBER1];
        } elseif (isset($this->data[\UPS\Shipping\Helper\ConstantAccount::OPTRADIO])
            && $this->data[\UPS\Shipping\Helper\ConstantAccount::OPTRADIO] == 1
        ) {
            $accountnumber = $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNUMBER];
        } else {
            $accountnumber = '';
        }
        return $accountnumber;
    }

    /**
     * Function checkOption
     * check option
     *
     * @param string $errorCode    //The errorCode
     * @param string $dataAccount  //The dataAccount
     * @param string $errorMessage //The errorMessage
     *
     * @return boolean
     */
    public function checkOption(&$errorCode, $dataAccount, &$errorMessage)
    {
        $dataFormat = $this->apiAccount->reformatData($dataAccount);
        $responseData = json_decode($this->apiAccount->registrationSuccess($dataFormat));
        if (isset($responseData->ManageAccountResponse->Response->ResponseStatus->Code)
            && $responseData->ManageAccountResponse->Response->ResponseStatus->Code == 1
        ) {
            $checkAPI = $this->checkSuccessAPI($responseData->ManageAccountResponse->ShipperAccountStatus);
            $usCountry = strtolower($dataAccount[\UPS\Shipping\Helper\ConstantAccount::COUNTRY]);
            if ($checkAPI[0] == true) {
                if (\UPS\Shipping\Helper\Config::LOWER_CONFIG_COUNTRY_US == $usCountry && isset($dataAccount['ControlID'])) {
                    unset($dataAccount['ControlID']);
                }
                $errorCode = true;
                $this->modelAccount->saveAccount($dataAccount);
                //offPluginManager 2019-03-18
                //call plugin API
                $checkTransferExist = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_PLUGIN_MERCHANTINFO_EXIST);
                if ($checkTransferExist == '1') {
                    $this->callMerchantInfoAddAccount($dataAccount);
                }
                //end call plugin API
                $this->messageManager->addSuccessMessage(__("Your UPS account has been successfull registered"));
                $this->_redirect(\UPS\Shipping\Helper\ConstantAccount::LINKACCOUNTSUCCESS);
            } else {
                $errorMessage = $checkAPI[1];
            }
        } else {
            $this->data[\UPS\Shipping\Helper\ConstantAccount::BORDERACCOUNTRED] = '';
            $errorMessage = $responseData->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description;
        }
    }

    /**
     * Function callPluginManager
     * select option
     *
     * @param string $idAccount //The idAccount
     *
     * @return boolean
     */
    public function callPluginManager($idAccount)
    {
        $checkTransferExist = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_PLUGIN_MERCHANTINFO_EXIST);
        if ($checkTransferExist == '1') {
            $accountInfo = $this->modelAccount->getInfoAccount($idAccount);
            if (isset($accountInfo[\UPS\Shipping\Helper\ConstantAccount::UPS_ACCOUNT_NUMBER])) {
                $this->callUpdateMerchantStatus($accountInfo[\UPS\Shipping\Helper\ConstantAccount::UPS_ACCOUNT_NUMBER]);
            }
        }
    }

    /**
     * Function endCallApi
     * select option
     *
     * @param string $idAccount //The idAccount
     *
     * @return boolean
     */
    public function endCallApi($idAccount)
    {
        $appNumber = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_CHOOSE_ACCOUNT_NUMBER_AP;
        $addNumber = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_CHOOSE_ACCOUNT_NUMBER_ADD;
        $getAccountDefaultAP = $this->scopeConfig->getValue($appNumber);
        $getAccountDefaultADD = $this->scopeConfig->getValue($addNumber);
        if ($idAccount == $getAccountDefaultAP) {
            $this->configWriter->save($appNumber, 1);
        };
        if ($idAccount == $getAccountDefaultADD) {
            $this->configWriter->save($addNumber, 1);
        }
        foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
            $this->cacheTypeList->cleanType($type);
        }
    }

    /**
     * Function deleteAccount
     * select option
     *
     * @param string $accountId //The accountId
     *
     * @return boolean
     */
    private function _deleteAccount($accountId)
    {
        return $this->modelAccount->deleteAccount($accountId);
    }

    /**
     * Function checkSuccessAPI
     * select option
     *
     * @param string $responseData //The responseData
     *
     * @return boolean
     */
    public function checkSuccessAPI($responseData)
    {
        $checkAPI = [];
        if (isset($responseData->Code)) {
            $checkAPI[] = [
                "Code" => $responseData->Code,
                "Description" => $responseData->Description,
            ];
            $checkAPI = json_decode(json_encode($checkAPI));
        } else {
            $checkAPI = $responseData;
        };
        $errorMessage = [];
        $successAPI = 0;
        $codeSuccess = ["010", "012", "040", "42"];//code success
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
     * Function checkValidate
     * select option
     *
     * @return boolean
     */
    public function checkValidate()
    {
        $validate = 0;
        $accountName = $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNAME];
        if (!empty($accountName) || $accountName == '0') {
            $validate++;
        }
        $postCode = $this->data[\UPS\Shipping\Helper\ConstantAccount::POSTALCODE];
        if (!empty($postCode) || $postCode == '0') {
            $validate++;
        }
        $addressLine1 = $this->data[\UPS\Shipping\Helper\ConstantAccount::ADDRESSLINE1];
        if (!empty($addressLine1) || $addressLine1 == '0') {
            $validate++;
        }
        $phoneNumber = $this->data[\UPS\Shipping\Helper\ConstantAccount::PHONENUMBER];
        if (!empty($phoneNumber) || $phoneNumber == '0') {
            $validate++;
        }
        $city = $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTCITY];
        if (!empty($city) || $city == '0') {
            $validate++;
        }
        if ($this->data && ($validate == 5)) {
            return true;
        }
        return false;
    }

    /**
     * Function checkValidateOption
     * select option
     *
     * @return boolean
     */
    public function checkValidateOption()
    {
        $this->regex = \UPS\Shipping\Helper\Config::REGEX_VALIDATE;
        $accountNumber1 = $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNUMBER1];
        $accountNumber = $this->data[\UPS\Shipping\Helper\ConstantAccount::ACCOUNTNUMBER];
        $accountNumberSmall = \UPS\Shipping\Helper\ConstantAccount::ACCOUNTNUMBERSMALL;
        if ($this->data[\UPS\Shipping\Helper\ConstantAccount::OPTRADIO] == 2) {
            if (!$this->validateAccount($accountNumber1, $this->regex[$accountNumberSmall])) {
                return false;
            }
        } elseif ($this->data[\UPS\Shipping\Helper\ConstantAccount::OPTRADIO] == 1
            && (!$this->validateAccount($accountNumber, $this->regex[$accountNumberSmall])
            || ($this->checkValidateAccount() == true))
        ) {
                return false;
        } else {
            return true;
        }
        return true;
    }

    /**
     * Function checkValidateAccount
     * select option
     *
     * @return boolean
     */
    public function checkValidateAccount()
    {
        $this->regex = \UPS\Shipping\Helper\Config::REGEX_VALIDATE;
        if (!$this->validateAccount($this->data['InvoiceNumber'], $this->regex['invoiceNumber'])
            || !$this->validateAccount($this->data['InvoiceAmount'], $this->regex['invoiceAmount'])
            || !$this->validateAccount($this->data['InvoiceDate'], $this->regex['date'])
        ) {
            return true;
        }
        return false;
    }

    /**
     * Function validateAccount
     * select option
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
     * Function callMerchantInfoAddAccount
     * call Merchant TransferInfo API
     *
     * @param string $dataAccount //The dataAccount
     *
     * @return boolean
     */
    public function callMerchantInfoAddAccount($dataAccount)
    {
        $merchantKey = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY);
        $websiteMerchant = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_WEBSECUREURL);
        $websiteMerchant = str_replace('https://', '', str_replace('http://', '', $websiteMerchant));
        $version = \UPS\Shipping\Helper\Config::VERSION_PLUGIN;
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        // Accessorial
        $arrReturnAccessorial = $this->modelAccessorial->getListAccessorialActive();
        $arrReturnAccessorial = json_decode($arrReturnAccessorial, true);
        $arrAccessorial = [];
        $arrayAccessorialKey = array_keys($arrReturnAccessorial);
        if (!empty($arrayAccessorialKey)) {
            foreach ($arrayAccessorialKey as $key) {
                $arrAccessorial[] = [
                    'accessorial_key' => $key,
                    'accessorial_name' => $arrReturnAccessorial[$key]
                ];
            }
        }
        $arrShippingService = [];
        $shippingDeliveryAP = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_ACCESS_POINT;
        if ($this->scopeConfig->getValue($shippingDeliveryAP) == '1') {
            $arrShippingServiceAP  = $this->modelService->getSelectedServices('AP');
            $arrShippingService  = array_merge($arrShippingService, $arrShippingServiceAP);
        }
        $shippingDeliveryAddress = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_TO_SHIPPING_ADDRESS;
        if ($this->scopeConfig->getValue($shippingDeliveryAddress) == '1') {
            $arrShippingServiceADD = $this->modelService->getSelectedServices('ADD');
            $arrShippingService  = array_merge($arrShippingService, $arrShippingServiceADD);
        }
        // Shipping Services and Delivery Rates
        $this->checkShipping($arrShippingService, $listShippingServices, $arrDelivaryRates);
        // Default package
        $this->defaultPackage($defaultPackage, $defaultPackageName, $weight, $weightUnit, $length, $width, $height, $dimensionUnit);

        $defaultPackages = [
            \UPS\Shipping\Helper\ConstantAccount::PACKAGE_NAME => $defaultPackageName,
            \UPS\Shipping\Helper\ConstantPackage::WEIGHT => $weight,
            \UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT => $weightUnit,
            \UPS\Shipping\Helper\ConstantPackage::LENGTH => $length,
            \UPS\Shipping\Helper\ConstantPackage::WIDTH => $width,
            \UPS\Shipping\Helper\ConstantPackage::HEIGHT => $height,
            \UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION => $dimensionUnit,
        ];
        $accountNumberInfo = [];
        if (isset($dataAccount[\UPS\Shipping\Helper\ConstantAccount::ACCOUNT_DEFAULT])
            && $dataAccount[\UPS\Shipping\Helper\ConstantAccount::ACCOUNT_DEFAULT] != '1'
        ) {
            $accountNumber = $dataAccount[\UPS\Shipping\Helper\ConstantAccount::UPS_ACCOUNT_NUMBER];
            $address = (isset($dataAccount[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_1])
            ? $dataAccount[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_1] : '');
            // account number infor
            if (isset($dataAccount[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_2])
                && !empty($dataAccount[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_2])
            ) {
                $address .= ', ' .  $dataAccount[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_2];
            }
            if (isset($dataAccount[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_3])
                && !empty($dataAccount[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_3])
            ) {
                $address .= ', ' . $dataAccount[\UPS\Shipping\Helper\ConstantAccount::ADDRESS_3];
            }
            $accountNumberItem = [
                'merchantKey'   => $merchantKey,
                \UPS\Shipping\Helper\ConstantAccount::ACCOUNTNUMBERSMALL
                => (isset($dataAccount[\UPS\Shipping\Helper\ConstantAccount::UPS_ACCOUNT_NUMBER])
                ? $dataAccount[\UPS\Shipping\Helper\ConstantAccount::UPS_ACCOUNT_NUMBER] : ''),
                'companyName'   => $dataAccount[\UPS\Shipping\Helper\ConstantAccount::COMPANY],
                'joiningDate'   => date('d/m/Y'),
                'website'       => $websiteMerchant,
                'currencyCode'  => $currencyCode,
                'status'        => 10,
                'platform'      => 30,
                'version'       => $version,
                'address'       => $address,
                'postalCode'    => (isset($dataAccount[\UPS\Shipping\Helper\ConstantAccount::POST_CODE])
                ? $this->getPostalCode($dataAccount[\UPS\Shipping\Helper\ConstantAccount::POST_CODE]) : ''),
                'city'          => (isset($dataAccount['city']) ? $dataAccount['city'] : ''),
                \UPS\Shipping\Helper\ConstantAccount::COUNTRY
                => (isset($dataAccount[\UPS\Shipping\Helper\ConstantAccount::COUNTRY])
                ? $dataAccount[\UPS\Shipping\Helper\ConstantAccount::COUNTRY] : ''),

                \UPS\Shipping\Helper\ConstantAccount::PACKAGE_NAME => $defaultPackageName,
                \UPS\Shipping\Helper\ConstantPackage::WEIGHT        => $weight,
                \UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT    => $weightUnit,
                \UPS\Shipping\Helper\ConstantPackage::LENGTH        => $length,
                \UPS\Shipping\Helper\ConstantPackage::WIDTH         => $width,
                \UPS\Shipping\Helper\ConstantPackage::HEIGHT        => $height,
                \UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION => $dimensionUnit,
                'isFirstAccount'=> 0,
            ];
            $accountNumberInfo = $accountNumberItem;
        }
        $this->runTransferMerchantInfo($accountNumberInfo, $defaultPackages);
    }

    /**
     * Function runTransferMerchantInfo
     * get Default Package
     *
     * @param string $accountNumberInfo //The accountNumberInfo
     * @param string $defaultPackages   //The defaultPackages
     *
     * @return boolean
     */
    public function runTransferMerchantInfo($accountNumberInfo, $defaultPackages)
    {
        $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
        if (empty($bearerToken)) {
            // re-RegisterToken
            if ($this->resetRegisteredToken()) {
                $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                $bearerSessionToken = $this->checkoutSession->getBearLongToken();
                if ($bearerSessionToken != $bearerToken) {
                    $bearerToken = $bearerSessionToken;
                }
            }
        }
        // call API
        if (!empty($bearerToken)) {
            $dataTransferMerchantInfo = [
                'accountNumberInfo' => $accountNumberInfo,
                'defaultPackages' => $defaultPackages,
                'accessorials' => [],
                'shippingServices' => [],
                'deliveryRates' => [],
                \UPS\Shipping\Helper\ConstantManager::BEARERTOKEN => $bearerToken
            ];
            $responseApi = $this->apiManager->callTransferMerchantInfo($dataTransferMerchantInfo);
            $responseApi = json_decode($responseApi);
            // bearerToken expired
            if (isset($responseApi->error->errorCode) && $responseApi->error->errorCode == '401') {
                if ($this->resetRegisteredToken()) {
                    $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                    $bearerSessionToken = $this->checkoutSession->getBearLongToken();
                    if ($bearerSessionToken != $bearerToken) {
                        $bearerToken = $bearerSessionToken;
                    }
                    $dataTransferMerchantInfo[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN] = $bearerToken;
                    $this->apiManager->callTransferMerchantInfo($dataTransferMerchantInfo);
                }
            }
        }
    }

    /**
     * Function defaultPackage
     * get Default Package
     *
     * @param string $defaultPackage     //The defaultPackage
     * @param string $defaultPackageName //The defaultPackageName
     * @param string $weight             //The weight
     * @param string $weightUnit         //The weightUnit
     * @param string $length             //The length
     * @param string $width              //The width
     * @param string $height             //The height
     * @param string $dimensionUnit      //The dimensionUnit
     *
     * @return boolean
     */
    public function defaultPackage(&$defaultPackage, &$defaultPackageName, &$weight, &$weightUnit, &$length, &$width, &$height, &$dimensionUnit)
    {
        $jsonDefaultPackage = $this->modelPackage->getListPackage();
        if (!empty($jsonDefaultPackage)) {
            $defaultPackage = (isset($jsonDefaultPackage[0])) ? $jsonDefaultPackage[0] : [];
            if (!empty($defaultPackage)) {
                $unitDimension = $defaultPackage[\UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION];
                $unitWeight = $defaultPackage[\UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT];
                $defaultPackageName = $defaultPackage[\UPS\Shipping\Helper\ConstantAccount::PACKAGE_NAME];
                $weight             = $defaultPackage[\UPS\Shipping\Helper\ConstantPackage::WEIGHT];
                $weightUnit         = ((strtoupper($unitWeight) == 'LBS') ? 'Pounds' : 'Kg');
                $length             = $defaultPackage[\UPS\Shipping\Helper\ConstantPackage::LENGTH];
                $width              = $defaultPackage[\UPS\Shipping\Helper\ConstantPackage::WIDTH];
                $height             = $defaultPackage[\UPS\Shipping\Helper\ConstantPackage::HEIGHT];
                $dimensionUnit      = ((strtoupper($unitDimension) == 'CM') ? 'Cm' : 'Inch');
            }
        }
    }

    /**
     * Function checkShipping
     * select option
     *
     * @param string $arrShippingService   //The arrShippingService
     * @param string $listShippingServices //The listShippingServices
     * @param string $arrDelivaryRates     //The arrDelivaryRates
     *
     * @return boolean
     */
    public function checkShipping($arrShippingService, &$listShippingServices, &$arrDelivaryRates)
    {
        $listShippingServices = [];
        $arrDelivaryRates = [];
        if (!empty($arrShippingService)) {
            foreach ($arrShippingService as $service) {
                $deliveryRateService = $this->modelDeliveryRates->getListDeliveryRatesByServiceId($service['id']);
                $stringServiceName = '';
                if ($service['service_symbol'] == '&trade;') {
                    $stringServiceName = __("UPS Access Point™ Economy");
                } elseif ($service[\UPS\Shipping\Helper\ConstantAccount::SERVICE_NAME] == 'UPS Standard') {
                    $stringServiceName = __('UPS® Standard');
                } elseif ($service[\UPS\Shipping\Helper\ConstantAccount::SERVICE_NAME] == 'UPS Express 12:00') {
                    $stringServiceName = __('UPS Express 12:00');
                } elseif ($service[\UPS\Shipping\Helper\ConstantAccount::SERVICE_NAME] == 'UPS Ground') {
                    $stringServiceName = __('UPS® Ground');
                } elseif ($service[\UPS\Shipping\Helper\ConstantAccount::SERVICE_NAME] == 'UPS Next Day Air Early') {
                    $stringServiceName = __('UPS Next Day Air® Early');
                } elseif ($service[\UPS\Shipping\Helper\ConstantBilling::SERVICE_NAME] == 'UPS Standard - Saturday Delivery') {
                    $stringServiceName = __('UPS® Standard - Saturday Delivery');
                } elseif ($service[\UPS\Shipping\Helper\ConstantBilling::SERVICE_NAME] == 'UPS Express - Saturday Delivery') {
                    $stringServiceName = __('UPS Express® - Saturday Delivery');
                } else {
                    $stringServiceName =  __($service[\UPS\Shipping\Helper\ConstantAccount::SERVICE_NAME]). '®';
                }

                $listShippingServices[] = [
                    \UPS\Shipping\Helper\ConstantAccount::SERVICE_KEY_DELIVERY
                    => $service[\UPS\Shipping\Helper\ConstantAccount::SERVICE_KEY_DELIVERY],
                    \UPS\Shipping\Helper\ConstantAccount::SERVICE_TYPE
                    => ($service[\UPS\Shipping\Helper\ConstantAccount::SERVICE_TYPE] == 'AP') ? 10 : 20,
                    \UPS\Shipping\Helper\ConstantAccount::SERVICE_NAME => $stringServiceName,
                    \UPS\Shipping\Helper\ConstantAccount::RATE_CODE
                    => $service[\UPS\Shipping\Helper\ConstantAccount::RATE_CODE]
                ];
                if (!empty($deliveryRateService)) {
                    foreach ($deliveryRateService as $rateService) {
                        $minimumOrderValue = 0;
                        $deliveryValue = 0;
                        $realtimeValue = 0;
                        if ($rateService['rate_type'] == 'real_time') {
                            $deliveryType = 20;
                            $realtimeValue = $rateService[\UPS\Shipping\Helper\ConstantAccount::DELIVERY_RATE];
                        } else {
                            $deliveryType = 10;
                            $minOrderValue = $rateService[\UPS\Shipping\Helper\ConstantAccount::MIN_ORDER_VALUE];
                            $deliveryRateValue = $rateService[\UPS\Shipping\Helper\ConstantAccount::DELIVERY_RATE];
                            $minimumOrderValue = (isset($minOrderValue)) ? $minOrderValue : 0;
                            $deliveryValue = (isset($deliveryRateValue)) ? $deliveryRateValue : 0;
                        }
                        $arrDelivaryRates[] = [
                            \UPS\Shipping\Helper\ConstantAccount::SERVICE_KEY_DELIVERY
                            => $service[\UPS\Shipping\Helper\ConstantAccount::SERVICE_KEY_DELIVERY],
                            'rate_type' => $deliveryType,
                            \UPS\Shipping\Helper\ConstantAccount::SERVICE_TYPE
                            => ($service[\UPS\Shipping\Helper\ConstantAccount::SERVICE_TYPE] == 'AP') ? 10 : 20,
                            \UPS\Shipping\Helper\ConstantAccount::SERVICE_NAME => $stringServiceName,
                            \UPS\Shipping\Helper\ConstantAccount::RATE_CODE
                            => $service[\UPS\Shipping\Helper\ConstantAccount::RATE_CODE],
                            \UPS\Shipping\Helper\ConstantAccount::MIN_ORDER_VALUE => (float)$minimumOrderValue,
                            \UPS\Shipping\Helper\ConstantAccount::DELIVERY_RATE => (float)$deliveryValue,
                            'realtimeValue' => (float)$realtimeValue,
                        ];
                    }
                }
            }
        }
    }

    /**
     * Function callUpdateMerchantStatus
     * call api to update account's status
     *
     * @param string $accountNumber //The accountNumber
     *
     * @return boolean
     */
    public function callUpdateMerchantStatus($accountNumber)
    {
        $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
        if (empty($bearerToken)) {
            // re-RegisterToken
            if ($this->resetRegisteredToken()) {
                $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                $bearerSessionToken = $this->checkoutSession->getBearLongToken();
                if ($bearerSessionToken != $bearerToken) {
                    $bearerToken = $bearerSessionToken;
                }
            }
        }
        // call API
        if (!empty($bearerToken)) {
            $dataUpdateMerchantStatus = [
                'merchantKey'  => $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY),
                \UPS\Shipping\Helper\ConstantAccount::ACCOUNTNUMBERSMALL=> $accountNumber,
                'status'       => 20,
                \UPS\Shipping\Helper\ConstantManager::BEARERTOKEN => $bearerToken
            ];
            $responseApi = $this->apiManager->callUpdateMerchantStatus($dataUpdateMerchantStatus);
            $responseApi = json_decode($responseApi);
            // bearerToken expired
            if (isset($responseApi->error->errorCode) && $responseApi->error->errorCode == '401') {
                if ($this->resetRegisteredToken()) {
                    $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                    $bearerSessionToken = $this->checkoutSession->getBearLongToken();
                    if ($bearerSessionToken != $bearerToken) {
                        $bearerToken = $bearerSessionToken;
                    }
                    $dataUpdateMerchantStatus[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN] = $bearerToken;
                    $this->apiManager->callUpdateMerchantStatus($dataUpdateMerchantStatus);
                }
            }
        }
    }

    /**
     * Function getPhones
     * only get number for Phone
     *
     * @param string $phoneNumber //The phoneNumber
     *
     * @return boolean
     */
    public function getPhones($phoneNumber)
    {
        preg_match_all('/\d+/', $phoneNumber, $matches);
        return implode("", $matches[0]);
    }

    /**
     * Function getPostalCode
     * only get number and characters
     *
     * @param string $postalCode //The postalCode
     *
     * @return boolean
     */
    public function getPostalCode($postalCode)
    {
        return trim($postalCode);
    }

    /**
     * Index setRegisteredToken
     *
     * @return string $trackingStatus
     */
    public function resetRegisteredToken()
    {
        $licenseDefault = $this->licenseModel->getLicenseDefault();
        $resetRegisterToken = false;
        $websiteMerchant = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_WEBSECUREURL);
        $valueSecurityToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN);
        if (empty($valueSecurityToken)) {
            foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
                $this->cacheTypeList->cleanType($type);
            }
            $valueSecurityToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN);
        }
        $arrLicenseParams = [
            "MerchantKey" => $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY),
            "WebstoreUrl" => $websiteMerchant,
            "WebstoreUpsServiceLinkSecurityToken" => $valueSecurityToken,
            "WebstorePlatform" => 'Magento',
            "WebstorePlatformVersion" => \UPS\Shipping\Helper\Config::VERSION_FLATFORM,
            "UpsReadyPluginName" => \UPS\Shipping\Helper\Config::UPS_SHIPPING_MODULE,
            "UpsReadyPluginVersion" => \UPS\Shipping\Helper\Config::VERSION_PLUGIN,
            "WebstoreUpsServiceLinkUrl" => $websiteMerchant . \UPS\Shipping\Helper\Config::API_URL,
            "Username" => $licenseDefault['Username'],
            "Password" => $licenseDefault['Password'],
            "AccessLicenseNumber" => $licenseDefault['AccessLicenseNumber']
        ];
        // Long bearer token
        $responseLongToken = $this->apiHandshake->registeredPluginToken($arrLicenseParams);
        if ($responseLongToken) {
            $responseLongToken = json_decode($responseLongToken);
            // save long token
            $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN,  $responseLongToken->data);
            $this->checkoutSession->setBearLongToken($responseLongToken->data);
            // save UPS_BING_MAPS_KEY
            $responseUpsBingMapsKey = $this->apiAccount->getUpsBingMapsKey($responseLongToken->data);
            if ($responseUpsBingMapsKey) {
                $responseUpsBingMapsKey = json_decode($responseUpsBingMapsKey);
                // save long token
                $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_BING_MAPS_KEY,  $responseUpsBingMapsKey->data);
            }
            $resetRegisterToken = true;
        }

        foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        return $resetRegisterToken;
    }
}
