<?php

/**
 * License file
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
 * License file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class License extends ClientAPI
{
    //Declare variable constand Access License Profile
    const ACCESSLICENSEPROFILE = 'AccessLicenseProfile';
    //Declare variable constand Address
    const ADDRESS = 'Address';
    //Declare variable constand Country Code
    const COUNTRYCODE = 'CountryCode';
    //Declare variable constand Primary Contact
    const PRIMARYCONTACT = 'PrimaryContact';
    //Declare variable constand Title
    const TITLE = 'Title';
    const EMAILADDRESS = 'EMailAddress';
    const PHONENUMBER = 'PhoneNumber';
    const FAXNUMBER = 'FaxNumber';
    //Declare variable constand Secondary Contact
    const SECONDARYCONTACT = 'SecondaryContact';
    //Declare variable constand State Province Code
    const STATEPROVINCECODE = 'StateProvinceCode';
    /**
     * License function access 1
     *
     * @param string $data        //The data
     * @param string $bearerToken //The bearerToken
     *
     * @return $request access
     */
    public function access1($data, $bearerToken)
    {
        //get Base Request No Account
        $request = $this->getBaseRequestNoAccountBearer();
        //get request Access License Agreement Request
        $request->UPSSecurity = (object)[];
        $request->AccessLicenseAgreementRequest = [
            "Request" => [
                "RequestOption" => "",
                "TransactionReference" => [
                    "CustomerContext" => "CutomerContext",
                    "TransactionIdentifier" => ""
                ]
            ],
            "DeveloperLicenseNumber" => self::DEVELOPER_LICENSE_NUMBER,
            self::ACCESSLICENSEPROFILE => $data
        ];
        $this->bearerToken = $bearerToken;
        $this->setRequestByObject($request);
        $this->setApiUrlByNamePlugin('UpsReadyProvider/License');
        return $this->doRequestBearer()->getBody();
    }

    /**
     * License function access 2
     *
     * @param string $data        //The data
     * @param string $bearerToken //The bearerToken
     *
     * @return $request access
     */
    public function access2($data, $bearerToken)
    {
        //Convert data input to Ascii
        $this->convertToAscii($data);
        // get base requset
        //$request = $this->getBaseRequest2();
        $request = $this->getBaseRequestBearer();
        // get license
        $license = $this->getLicense();
        $request->UPSSecurity = (object)[];
        //get request Access License Request
        $request->AccessLicenseRequest = [
            "Request" => [
                "RequestOption" => "",
                "TransactionReference" => [
                    "CustomerContext" => "CutomerContext",
                    "TransactionIdentifier" => ""
                ]
            ],
            "CompanyName" => $data['CompanyName'],
            self::ADDRESS => [
                "AddressLine1" => $data[self::ADDRESS]['AddressLine1'],
                "AddressLine2" => $data[self::ADDRESS]['AddressLine2'],
                "AddressLine3" => $data[self::ADDRESS]['AddressLine3'],
                "City" => $data[self::ADDRESS]['City'],
                self::STATEPROVINCECODE => (isset($data[self::ADDRESS][self::STATEPROVINCECODE])
                ? $data[self::ADDRESS][self::STATEPROVINCECODE] : ''),
                "PostalCode" => str_replace('-', '', $data[self::ADDRESS]['PostalCode']),
                self::COUNTRYCODE => $data[self::ADDRESS][self::COUNTRYCODE],
            ],
            self::PRIMARYCONTACT => [
                "Name" => $data[self::PRIMARYCONTACT]['Name'],
                self::TITLE => $data[self::PRIMARYCONTACT]['Title'],
                self::EMAILADDRESS => $data[self::PRIMARYCONTACT]['EMailAddress'],
                self::PHONENUMBER => $data[self::PRIMARYCONTACT]['PhoneNumber'],
                self::FAXNUMBER => $data[self::PRIMARYCONTACT]['FaxNumber'],
            ],
            self::SECONDARYCONTACT => [
                "Name" => 'Test',
                self::TITLE => 'Mr',
                self::EMAILADDRESS => 'admin@mail.com',
                self::PHONENUMBER => '090909090'
            ],
            "CompanyURL" => $this->requestClient->getServer('SERVER_NAME'),
            "DeveloperLicenseNumber" => self::DEVELOPER_LICENSE_NUMBER,
            self::ACCESSLICENSEPROFILE => [
                self::COUNTRYCODE => $data[self::ACCESSLICENSEPROFILE][self::COUNTRYCODE],
                "LanguageCode" => strtoupper($data[self::ACCESSLICENSEPROFILE]['LanguageCode']),
                "AccessLicenseText" => $license["ACCESS_LICENSE_TEXT"]
            ],
            "ClientSoftwareProfile" => [
                "SoftwareInstaller" => "Magento",
                "SoftwareProductName" => "Magento Module",
                "SoftwareProvider" => "Magento",
                "SoftwareVersionNumber" => "2.2.5"
            ]
        ];
        $this->bearerToken = $bearerToken;
        $this->setRequestByObject($request);
        $this->setApiUrlByNamePlugin('UpsReadyProvider/License');
        return $this->doRequestBearer()->getBody();
    }
}
