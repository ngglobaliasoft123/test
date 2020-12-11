<?php
/**
 * Account API file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\API;

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
class Account extends ClientAPI
{
    const COMPANYNAME = 'CompanyName';
    const CUSTOMERNAME = 'CustomerName';
    const TITLE = 'Title';
    const ADDRESS = 'Address';
    const ADDRESSLINE = 'AddressLine';
    const POSTALCODE = 'PostalCode';
    const POST_CODE = 'post_code';
    const COUNTRYCODE = 'CountryCode';
    const COUNTRYSTRING = 'country';
    const ACCOUNT_TYPE = 'account_type';
    const PHONENUMBER = 'PhoneNumber';
    const EMAILADDRESS = 'EmailAddress';
    const DEVICEIDENTITY = 'DeviceIdentity';
    const STATECODE = 'StateProvinceCode';
    const SHIPPERACCOUNT = 'ShipperAccount';
    const ACCOUNTNAME = 'AccountName';
    const ACCOUNTNUMBER = 'AccountNumber';
    const INVOICEINFO = 'InvoiceInfo';
    const INVOICENUMBER = 'InvoiceNumber';
    const INVOICEDATE = 'InvoiceDate';
    const CURRENCYCODE = 'CurrencyCode';
    const INVOICEAMOUNT = 'InvoiceAmount';
    const REQUESTSTRING = 'Request';
    const TRANSACTIONREFERENCE = 'TransactionReference';
    const CUSTOMERCONTEXT = 'CustomerContext';
    const ENDUSERINFORMATION = 'EndUserInformation';
    const BILLINGADDRESS = 'BillingAddress';
    const STREETADDRESS = 'StreetAddress';
    const PHONESTRING = 'Phone';
    const NUMBERSTRING = 'Number';
    const PICKUPADDRESS = 'PickupAddress';
    const DEVICEIDENTITYSMALL = 'deviceIdentity';
    const CONTACTNAME = 'ContactName';
    const USERNAME = 'Username';
    const LOCALE = 'Locale';
    const USER_NAME = 'username';
    const PROMOCODE = 'PromoCode';
    const LANGUAGECODE = 'LanguageCode';
    const CONSTPASS = 'password';
    const USERNAMESMALL = "Username";

    /**
     * Function reformatData
     *
     * @param string $data //data form input
     *
     * @author UPS <noreply@ups.com>
     *
     * @return $dataFormat
     */
    public function reformatData($data)
    {
        //Infor account
        $dataFormat = [
            self::COMPANYNAME => $data['company'],
            self::CUSTOMERNAME => $data['fullname'],
            self::TITLE => $data['title'],
            self::ADDRESS => [
                self::ADDRESSLINE => [
                    $data['address_1'],
                    $data['address_2'],
                    $data['address_3']
                ],
                'City' => $data['city'],
                self::STATECODE => (isset($data[self::STATECODE]) ? $data[self::STATECODE] : 'XX'),
                self::POSTALCODE => str_replace('-', '', $data[self::POST_CODE]),
                self::COUNTRYCODE => $data[self::COUNTRYSTRING]
            ],
            self::ACCOUNT_TYPE => $data[self::ACCOUNT_TYPE],
            self::PHONENUMBER => $data['phone_number'],
            self::EMAILADDRESS => $data['email'],
            'NotificationCode' => '01',
            self::DEVICEIDENTITY => (isset($data[self::DEVICEIDENTITYSMALL]) ? $data[self::DEVICEIDENTITYSMALL] : ''),
            'SuggestUsernameIndicator' => 'N',
        ];
        switch ($data[self::ACCOUNT_TYPE]) {
            //UPS Account Number WITH an invoice occurred in the last 45 days
            //(90 days if your account is in the US or Canada)
        case 1:
            //Infor account
            $dataFormat[self::SHIPPERACCOUNT][1] = [
                self::ACCOUNTNAME => $data['ups_account_name'],
                self::ACCOUNTNUMBER => $data['ups_account_number'],
                self::COUNTRYCODE => $data[self::COUNTRYSTRING],
                self::POSTALCODE => str_replace('-', '', $data[self::POST_CODE]),
                self::INVOICEINFO => [
                    self::INVOICENUMBER => $data['ups_invoice_number'],
                    self::INVOICEDATE => $data['ups_invoice_date'],
                    self::CURRENCYCODE => $data['ups_currency'],
                    self::INVOICEAMOUNT => $data['ups_account_account']
                ]
            ];
            if (!empty($data['ControlID'])) {
                $dataFormat[self::SHIPPERACCOUNT][1][self::INVOICEINFO]['ControlID'] = $data['ControlID'];
            }
            break;
            //UPS Account Number WITHOUT an invoice occurred in the last 45 days
            //(90 days if your account is in the US or Canada)
        case 2:
            //Infor account
            $dataFormat[self::SHIPPERACCOUNT][2] = [
                self::ACCOUNTNAME => $data['ups_account_name'],
                self::ACCOUNTNUMBER => $data['ups_account_number'],
                self::COUNTRYCODE => $data[self::COUNTRYSTRING],
                self::POSTALCODE => str_replace('-', '', $data[self::POST_CODE]),
            ];
            break;
            //I don't have a UPS Account Number
        default:
            break;
        }
        return $dataFormat;
    }
    /**
     * Generate Ups password
     *
     * @param string $length //length
     *
     * @author UPS <noreply@ups.com>
     *
     * @return $randomString
     *  */
    private function _generatePass($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Account Registration
     *
     * @param string $data        //The data
     * @param string $bearerToken //The bearerToken
     *
     * @return string $responseAPI
     */
    public function registration($data, $bearerToken)
    {
        //Convert data input to Ascii
        $this->convertToAscii($data);
        switch ($data[self::ACCOUNT_TYPE]) {
        //UPS Account Number WITH an invoice occurred in the last 45 days
        //(90 days if your account is in the US or Canada)
        case 1:
            $format_date = explode('-', $data[self::SHIPPERACCOUNT][1][self::INVOICEINFO][self::INVOICEDATE]);
            $date = '';
            $countDate = count($format_date);
            for ($i = 0; $i < $countDate; $i++) {
                $date.=$format_date[$i];
            }
            //Infor shipper account
            $ShipperAccount = [
                self::ACCOUNTNAME => $data[self::SHIPPERACCOUNT][1][self::ACCOUNTNAME],
                self::ACCOUNTNUMBER => $data[self::SHIPPERACCOUNT][1][self::ACCOUNTNUMBER],
                self::COUNTRYCODE => $data[self::ADDRESS][self::COUNTRYCODE],
                self::POSTALCODE => $data[self::ADDRESS][self::POSTALCODE],
                self::INVOICEINFO => [
                    self::INVOICENUMBER => $data[self::SHIPPERACCOUNT][1][self::INVOICEINFO][self::INVOICENUMBER],
                    self::INVOICEDATE => $date,
                    self::CURRENCYCODE => $data[self::SHIPPERACCOUNT][1][self::INVOICEINFO][self::CURRENCYCODE],
                    self::INVOICEAMOUNT => $data[self::SHIPPERACCOUNT][1][self::INVOICEINFO][self::INVOICEAMOUNT]
                ]
            ];
            if (!empty($data[self::SHIPPERACCOUNT][1][self::INVOICEINFO]['ControlID'])) {
                $ShipperAccount[self::INVOICEINFO]['ControlID'] = $data[self::SHIPPERACCOUNT][1][self::INVOICEINFO]['ControlID'];
            }
            break;
        //UPS Account Number WITHOUT an invoice occurred in the last 45 days
        //(90 days if your account is in the US or Canada)
        case 2:
            //Infor shipper account
            $ShipperAccount = [
                self::ACCOUNTNAME => $data[self::SHIPPERACCOUNT][2][self::ACCOUNTNAME],
                self::ACCOUNTNUMBER => $data[self::SHIPPERACCOUNT][2][self::ACCOUNTNUMBER],
                self::COUNTRYCODE => $data[self::ADDRESS][self::COUNTRYCODE],
                self::POSTALCODE => $data[self::ADDRESS][self::POSTALCODE],
            ];
            break;
        //I don't have a UPS Account Number
        default:
            $ShipperAccount = [];
        }
        $request = $this->getBaseRequestBearer();
        $address = [];
        foreach ($data[self::ADDRESS][self::ADDRESSLINE] as $key => $value) {
            if ($value != "") {
                array_push($address, $value);
            }
        }

        $Username = $this->getUserName($bearerToken);
        if (!empty($Username[0])) {
            $Password = $this->_generatePass(26);
            $request->RegisterRequest = [
                self::REQUESTSTRING => [
                    'RequestOption' => 'N',
                    self::TRANSACTIONREFERENCE => [
                        self::CUSTOMERCONTEXT => self::CUSTOMERCONTEXT
                    ]
                ],
                self::USERNAME => $Username[0],
                'Password' => $Password,
                self::COMPANYNAME => $data[self::COMPANYNAME],
                self::CUSTOMERNAME => $data[self::CUSTOMERNAME],
                'EndUserIPAddress' => $this->getClientIP(),
                self::TITLE => $data[self::TITLE],
                self::ADDRESS => [
                    self::ADDRESSLINE => $address,
                    'City' => $data[self::ADDRESS]['City'],
                    'StateProvinceCode' => (isset($data[self::ADDRESS][self::STATECODE]) ? $data[self::ADDRESS][self::STATECODE] : 'XX'),
                    self::POSTALCODE => $data[self::ADDRESS][self::POSTALCODE],
                    self::COUNTRYCODE => $data[self::ADDRESS][self::COUNTRYCODE]
                ],
                self::PHONENUMBER => $data[self::PHONENUMBER],
                self::EMAILADDRESS => $data[self::EMAILADDRESS],
                'NotificationCode' => '01',
                self::DEVICEIDENTITY => (isset($data[self::DEVICEIDENTITY]) ? $data[self::DEVICEIDENTITY] : ''),
                'SuggestUsernameIndicator' => 'N'
            ];
            //Check empty Shipper Account
            if (!empty($ShipperAccount)) {
                $request->RegisterRequest[self::SHIPPERACCOUNT] = $ShipperAccount;
            }
            $request->UPSSecurity = (object)[];
            $this->bearerToken = $bearerToken;
            $this->setRequestByObject($request);
            $this->setApiUrlByNamePlugin('UpsReadyProvider/Registration');
            $response = $this->doRequestBearer()->getBody();
            return [
                $response,
                $Username[0],
                $Password
            ];
        } else {
            return [
                json_encode([]),
                $Username[1],
                ''
            ];
        }
    }

    /**
     * Account RegistrationSuccess
     *
     * @param string $data //The data
     *
     * @return string $responseAPI
     */
    public function registrationSuccess($data)
    {
        //Convert data input to Ascii
        $this->convertToAscii($data);
        switch ($data[self::ACCOUNT_TYPE]) {
        //UPS Account Number WITH an invoice occurred in the last 45 days
        //(90 days if your account is in the US or Canada)
        case 1:
            $format_date = explode('-', $data[self::SHIPPERACCOUNT][1][self::INVOICEINFO][self::INVOICEDATE]);
            $date = '';
            $countData = count($format_date);
            for ($i = 0; $i < $countData; $i++) {
                $date.=$format_date[$i];
            }
            //Infor Shipper Account
            $ShipperAccount = [
                self::ACCOUNTNAME => $data[self::SHIPPERACCOUNT][1][self::ACCOUNTNAME],
                self::ACCOUNTNUMBER => $data[self::SHIPPERACCOUNT][1][self::ACCOUNTNUMBER],
                self::COUNTRYCODE => $data[self::ADDRESS][self::COUNTRYCODE],
                self::POSTALCODE => $data[self::ADDRESS][self::POSTALCODE],
                self::INVOICEINFO => [
                    self::INVOICENUMBER => $data[self::SHIPPERACCOUNT][1][self::INVOICEINFO][self::INVOICENUMBER],
                    self::INVOICEDATE => $date,
                    self::CURRENCYCODE => $data[self::SHIPPERACCOUNT][1][self::INVOICEINFO][self::CURRENCYCODE],
                    self::INVOICEAMOUNT => $data[self::SHIPPERACCOUNT][1][self::INVOICEINFO][self::INVOICEAMOUNT]
                ]
            ];

            if (!empty($data[self::SHIPPERACCOUNT][1][self::INVOICEINFO]['ControlID'])) {
                $ShipperAccount[self::INVOICEINFO]['ControlID'] = $data[self::SHIPPERACCOUNT][1][self::INVOICEINFO]['ControlID'];
            }
            break;
        //UPS Account Number WITHOUT an invoice occurred in the last 45 days
        //(90 days if your account is in the US or Canada)
        case 2:
            //Infor Shipper Account
            $ShipperAccount = [
                self::ACCOUNTNAME => $data[self::SHIPPERACCOUNT][2][self::ACCOUNTNAME],
                self::ACCOUNTNUMBER => $data[self::SHIPPERACCOUNT][2][self::ACCOUNTNUMBER],
                self::COUNTRYCODE => $data[self::ADDRESS][self::COUNTRYCODE],
                self::POSTALCODE => $data[self::ADDRESS][self::POSTALCODE],
            ];
            break;
        //I don't have a UPS Account Number
        default:
            $ShipperAccount = [];
            break;
        }
        $request = $this->getBaseRequest2();
        $License = $this->getLicense();
        $request->ManageAccountRequest = [
            self::REQUESTSTRING => [
                self::TRANSACTIONREFERENCE => [
                    self::CUSTOMERCONTEXT => self::CUSTOMERCONTEXT
                ]
            ],
            self::USERNAME => $License['USERNAME'],
            'Password' => $License['PASSWORD'],
            self::SHIPPERACCOUNT => $ShipperAccount
        ];
        $this->setRequestByObject($request);
        $this->setApiUrlByName('Registration');
        return $this->doRequest()->getBody();
    }

    /**
     * Shipmentdetail OpenAccount
     *
     * @param string $data        //The data
     * @param string $bearerToken //The bearerToken
     *
     * @return string $responseAPI
     */
    public function openAccount($data, $bearerToken)
    {
        //Convert data input to Ascii
        $this->convertToAscii($data);
        $request = $this->getBaseRequestNoAccountBearer();
        $localLanguage = $data[self::LOCALE];
        $customerServiceCode = '02';
        $countryLocal = strtolower($data[self::PICKUPADDRESS][self::COUNTRYCODE]);
        if ('us' == $countryLocal) {
            $localLanguage = \UPS\Shipping\Helper\Config::COUNTRY_US;
            $customerServiceCode = '01';
        }
        $request->OpenAccountRequest = [
            self::LOCALE => $localLanguage,
            "CustomerServiceCode" => $customerServiceCode,
            self::REQUESTSTRING => [
                self::TRANSACTIONREFERENCE => [
                    self::CUSTOMERCONTEXT => '',
                    'TransactionIdentifier' => ''
                ]
            ],
            'AccountCharacteristics' => [
                'CustomerClassification' => [
                    'Code' => '01'
                ]
            ],
            self::ENDUSERINFORMATION => [
                'EndUserIPAddress' => $this->getClientIP(),
                'EndUserEmail' => substr($data[self::PICKUPADDRESS][self::EMAILADDRESS], 0, 50),
                'EndUserMyUPSID' => [
                    self::USERNAME => $data[self::ENDUSERINFORMATION][self::USER_NAME]
                ],
                'VatTaxID' => $data[self::ENDUSERINFORMATION]['VatTaxID'],
                self::DEVICEIDENTITY => $data[self::ENDUSERINFORMATION][self::DEVICEIDENTITY]
            ],
            self::BILLINGADDRESS => [
                self::CONTACTNAME => substr($data[self::BILLINGADDRESS][self::CONTACTNAME], 0, 20),
                self::COMPANYNAME => substr($data[self::BILLINGADDRESS][self::COMPANYNAME], 0, 30),
                self::STREETADDRESS => substr($data[self::BILLINGADDRESS][self::STREETADDRESS], 0, 30),
                'City' => $data[self::BILLINGADDRESS]['City'],
                self::COUNTRYCODE => $data[self::BILLINGADDRESS][self::COUNTRYCODE],
                self::STATECODE => (isset($data[self::BILLINGADDRESS][self::STATECODE])) ? trim($data[self::BILLINGADDRESS][self::STATECODE]) : 'XX',
                self::POSTALCODE => str_replace('-', '', $data[self::BILLINGADDRESS][self::POSTALCODE]),
                self::PHONESTRING => [
                self::NUMBERSTRING => $data[self::BILLINGADDRESS][self::PHONESTRING][self::NUMBERSTRING],
                ]
            ],
            self::PICKUPADDRESS => [
                self::CONTACTNAME => substr($data[self::PICKUPADDRESS][self::CONTACTNAME], 0, 20),
                self::COMPANYNAME => substr($data[self::PICKUPADDRESS][self::COMPANYNAME], 0, 30),
                self::STREETADDRESS => substr($data[self::PICKUPADDRESS][self::STREETADDRESS], 0, 30),
                'City' => $data[self::PICKUPADDRESS]['City'],
                self::COUNTRYCODE => $data[self::PICKUPADDRESS][self::COUNTRYCODE],
                self::STATECODE => (isset($data[self::PICKUPADDRESS][self::STATECODE])) ? trim($data[self::PICKUPADDRESS][self::STATECODE]) : 'XX',
                self::POSTALCODE => str_replace('-', '', $data[self::PICKUPADDRESS][self::POSTALCODE]),
                self::PHONESTRING => [
                self::NUMBERSTRING => $data[self::PICKUPADDRESS][self::PHONESTRING][self::NUMBERSTRING],
                ],
                self::EMAILADDRESS => $data[self::PICKUPADDRESS][self::EMAILADDRESS]
            ],
            'PickupInformation' => [
                'PickupOption' => [
                    'Code' => '08'
                ]
            ]
        ];
        $request->UPSSecurity = (object)[];
        $this->bearerToken = $bearerToken;
        $this->setRequestByObject($request);
        $this->setApiUrlByNamePlugin('UpsReadyProvider/OpenAccount');
        return $this->doRequestBearer()->getBody();
    }

    /**
     * Account getUserName
     *
     *  @param string $bearerToken //The bearerToken
     *
     * @return string $trackingStatus
     */
    public function getUserName($bearerToken)
    {
        $responseUpsId = $this->getUpsId($bearerToken);
        $responseUpsId = json_decode($responseUpsId);
        $Username = [];
        if (isset($responseUpsId->data) && !empty($responseUpsId->data)) {
            $Username[0] = $responseUpsId->data;
            $Username[1] = '';
        } else {
            $Username[0] = '';
            $Username[1] = $responseUpsId->error->message;
        }
        return $Username;
    }

    /**
     * Account getUpsBingMapsKey
     *
     *  @param string $bearerToken //The bearerToken
     *
     * @return string $trackingStatus
     */
    public function getUpsBingMapsKey($bearerToken)
    {
        $request = $this->getBaseRequestNoAccountBearer();
        $this->bearerToken = $bearerToken;
        $this->setRequestByObject($request);
        $this->setApiUrlByNamePlugin('SecurityService/UpsBingMapsKey');
        return $this->doRequestBearer()->getBody();
    }

    /**
     * Account getUpsId
     *
     *  @param string $bearerToken //The bearerToken
     *
     * @return string $trackingStatus
     */
    public function getUpsId($bearerToken)
    {
        $request = $this->getBaseRequestNoAccountBearer();
        $this->bearerToken = $bearerToken;
        $this->setRequestByObject($request);
        $this->setApiUrlByNamePlugin('SecurityService/MyUpsID');
        return $this->doRequestBearer()->getBody();
    }

    /**
     * Account PromoDiscountAgreement
     *
     * @param string $data //The data
     *
     * @return string $trackingStatus
     */
    public function promoDiscountAgreement($data)
    {
        //Convert data input to Ascii
        $this->convertToAscii($data);
        $request = $this->getBaseRequestNoAccount();
        $request->UPSSecurity = [
            "UsernameToken" => [
                self::USERNAMESMALL => $data[self::USER_NAME],
                'Password' => $data[self::CONSTPASS]
            ],
            "ServiceAccessToken" => [
                self::ACCESSLICENSENUMBER => $data['accessLicense']
            ]
        ];
        $request->PromoDiscountAgreementRequest = [
            self::PROMOCODE => $data[self::PROMOCODE],
            self::LOCALE => [
                self::LANGUAGECODE => $data[self::LANGUAGECODE],
                self::COUNTRYCODE => $data[self::COUNTRYCODE],
            ]
        ];
        $this->setRequestByObject($request);
        $this->setApiUrlByName('PromoDiscountAgreeMent');
        return $this->doRequest()->getBody();
    }

    /**
     * Account promoDiscount
     *
     * @param string $data //The data
     *
     * @return string $trackingStatus
     */
    public function promoDiscount($data)
    {
        //Convert data input to Ascii
        $this->convertToAscii($data);
        $request = (object)[];
        $request->UPSSecurity = [
            "UsernameToken" => [
                self::USERNAMESMALL => $data[self::USER_NAME],
                'Password' => $data[self::CONSTPASS]
            ],
            "ServiceAccessToken" => [
                self::ACCESSLICENSENUMBER => $data['accessLicense']
            ]
        ];
        $request->PromoDiscountRequest = [
            'AgreementAcceptanceCode' => $data['AgreementAcceptanceCode'],
            self::PROMOCODE => $data[self::PROMOCODE],
            self::LOCALE => [
                self::LANGUAGECODE => $data[self::LANGUAGECODE],
                self::COUNTRYCODE => $data[self::COUNTRYCODE],
            ],
            'AccountInfo' => [
                self::ACCOUNTNUMBER => $data[self::ACCOUNTNUMBER]
            ]
        ];
        $this->setRequestByObject($request);
        $this->setApiUrlByName('PromoDiscount');
        return $this->doRequest()->getBody();
    }
}
