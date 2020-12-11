<?php

/**
 * Ship file
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
 * Ship file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Ship extends ClientAPI
{
    //Declare variable constand MAX LENGTH
    const MAXLENGTH = 35;
    //Declare variable constand SHIPMENT
    const SHIPMENT = "Shipment";
    //Declare variable constand ADDRESS
    const ADDRESS = 'Address';
    //Declare variable constand ADDRESS LINE
    const ADDRESSLINE = 'AddressLine';
    //Declare variable constand STATE PROVINCE CODE
    const STATEPROVINCECODE = 'StateProvinceCode';
    //Declare variable constand POSTAL CODE
    const POSTALCODE = 'PostalCode';
    //Declare variable constand COUNTRY CODE
    const COUNTRYCODE = 'CountryCode';
    //Declare variable constand SHIPMENT CHARGE
    const SHIPMENTCHARGE = 'ShipmentCharge';
    //Declare variable constand ACCOUNT NUMBER
    const ACCOUNTNUMBER = 'AccountNumber';
    //Declare variable constand PACKAGE
    const PACKAGE = "Package";
    //Declare variable constand PACKAGE WEIGHT
    const PACKAGEWEIGHT = "PackageWeight";
    //Declare variable constand WEIGHT
    const WEIGHT = "Weight";
    //Declare variable constand UNITOFMEASUREMENT
    const UNITOFMEASUREMENT = 'UnitOfMeasurement';
    //Declare variable constand DESCRIPTION
    const DESCRIPTION = 'Description';
    //Declare variable constand ACCESSORIALS
    const ACCESSORIALS = 'accessorials';
    //Declare variable constand PACKAGING TYPE
    const PACKAGINGTYPE = "PackagingType";
    //Declare variable constand DIMENSIONS
    const DIMENSIONS = 'Dimensions';
    //Declare variable constand ALTERNATE DELIVERY ADDRESS
    const ALTERNATEDELIVERYADDRESS = 'AlternateDeliveryAddress';
    const INVOICELINETOTAL = "InvoiceLineTotal";
    //Declare variable constand CURRENCY CODE
    const CURRENCYCODE = 'CurrencyCode';
    //Declare variable constand MONETARY VALUE
    const MONETARYVALUE = 'MonetaryValue';
    //Declare variable constand SHIPMENT SERVICE OPTIONS
    const SHIPMENTSERVICEOPTIONS = "ShipmentServiceOptions";
    //Declare variable constand CHECK
    const CHECK = '([^a-zA-Z0-9.])';
    //Declare variable constand SHIPPER
    const SHIPPER = 'Shipper';
    //Declare variable constand ATTENTION NAME
    const ATTENTIONNAME = 'AttentionName';
    //Declare variable constand PHONE
    const PHONE = 'Phone';
    //Declare variable constand NUMBER
    const NUMBER = 'Number';
    //Declare variable constand SHIP TO
    const SHIPTO = 'ShipTo';
    //Declare variable constand SHIP FROM
    const SHIPFROM = 'ShipFrom';
    //Declare variable constand SERVICE
    const SERVICE = 'Service';
    //Declare variable constand PAYMENT INFORMATION
    const PAYMENTINFORMATION = 'PaymentInformation';
    //Declare variable constand BILL SHIPPER
    const BILLSHIPPER = 'BillShipper';
    //Declare variable constand NOTIFICATION CODE
    const NOTIFICATIONCODE = "NotificationCode";
    //Declare variable constand EMAIL
    const EMAIL = 'Email';
    //Declare variable constand EMAIL_MAIN
    const EMAIL_MAIN = "EMail";
    //Declare variable constand EMAIL ADDRESS
    const EMAILADDRESS = "EMailAddress";
    //Declare variable constand LOCALE
    const LOCALE = "Locale";
    //Declare variable constand LANGUAGE
    const LANGUAGE = "Language";
    //Declare variable constand DIALECT
    const DIALECT = "Dialect";
    //SHIPMENTRATINGOPTIONS
    const SHIPMENTRATINGOPTIONS = "ShipmentRatingOptions";
    const REPLACE = '([^a-zA-Z0-9.])';

    //Declare list variable constand Country Code
    const COUNTRY_EU = [
        "Austria" => "AT",
        "Belgium" => "BE",
        "Bulgaria" => "BG",
        "Croatia" => "HR",
        "Cyprus" => "CY",
        "CzechRepublic" => "CZ",
        "Denmark" => "DK",
        "Estonia" => "EE",
        "Finland" => "FI",
        "France" => "FR",
        "Germany" => "DE",
        "Greece" => "GR",
        "Hungary" => "HU",
        "Ireland" => "IE",
        "Italy" => "IT",
        "Latvia" => "LV",
        "Lithuania" => "LT",
        "Luxembourg" => "LU",
        "Malta" => "MT",
        "Netherlands" => "NL",
        "Poland" => "PL",
        "Portugal" => "PT",
        "Romania" => "RO",
        "Slovakia" => "SK",
        "Slovenia" => "SI",
        "Spain" => "ES",
        "Sweden" => "SE",
        "UnitedKingdom" => "GB",
    ];
    //Declare variable constand EIGHT EU COUNTRY
    const EIGHTEUCOUNTRY = \UPS\Shipping\Helper\Config::LISTEUCOUNTRY;

    /**
     * Ship send request API create shipment
     *
     * @param string $data //from request form.
     *
     * @return null
     */
    public function ship($data)
    {
        $request = $this->getBaseRequest2();
        $request->ShipmentRequest = [];
        $Package = [];
        $Notification = [];
        $fromCountry = $data[self::SHIPFROM][self::ADDRESS][self::COUNTRYCODE];
        $toCountry = $data[self::SHIPTO][self::ADDRESS][self::COUNTRYCODE];
        $packageServiceOptions = [];
        if ($fromCountry == 'US' && $toCountry == 'US') {
            $valueDCISType = 0;
            if (isset($data[self::ACCESSORIALS])) {
                if (array_key_exists('UPS_ACSRL_SIGNATURE_REQUIRED', $data[self::ACCESSORIALS])) {
                    $valueDCISType = 2;
                }

                if (array_key_exists('UPS_ACSRL_ADULT_SIG_REQUIRED', $data[self::ACCESSORIALS])) {
                    $valueDCISType = 3;
                }
            }
            if ($valueDCISType > 0) {
                $packageServiceOptions = [
                    "DeliveryConfirmation" => [
                        "DCISType" => (string)$valueDCISType
                    ]
                ];
            }
        }
        if (isset($data[self::ACCESSORIALS])
            && array_key_exists('UPS_ACSRL_TO_HOME_COD', $data[self::ACCESSORIALS])
            && in_array($toCountry, \UPS\Shipping\Helper\Config::ARRAYCOUNTRYCODE)
        ) {
            $codFundsCode = '0';
            $packageServiceOptions["COD"] = [
                "CODFundsCode" => $codFundsCode,
                "CODAmount" => [
                    self::CURRENCYCODE => $data[self::CURRENCYCODE],
                    self::MONETARYVALUE => preg_replace(self::CHECK, '', (string)$data[self::MONETARYVALUE])
                ]
            ];
        }
        foreach ($data['Package'] as $key => $value) {
            $Package[$key] = [
                "Dimensions" => [
                    self::UNITOFMEASUREMENT => [
                        "Code" => substr(strtoupper($value[self::DIMENSIONS][self::UNITOFMEASUREMENT]['Code']), 0, 2),
                        self::DESCRIPTION => $value[self::DIMENSIONS][self::UNITOFMEASUREMENT][self::DESCRIPTION]
                    ],
                    "Length" => strval($value[self::DIMENSIONS]['Length']),
                    "Width" => strval($value[self::DIMENSIONS]['Width']),
                    "Height" => strval($value[self::DIMENSIONS]['Height'])
                ],
                "PackageWeight" => [
                    self::UNITOFMEASUREMENT => [
                        "Code" => strtoupper($value[self::PACKAGEWEIGHT][self::UNITOFMEASUREMENT]['Code']),
                        self::DESCRIPTION => $value[self::PACKAGEWEIGHT][self::UNITOFMEASUREMENT][self::DESCRIPTION]
                    ],
                    self::WEIGHT => strval($value[self::PACKAGEWEIGHT]['Weight'])
                ],
                'Packaging' => [
                    'Code' => '02'
                ],
                'PackagingType' => [
                    'Code' => '02'
                ],
                'PackageServiceOptions' => $packageServiceOptions
            ];
            if (isset($data[self::ACCESSORIALS])
                && array_key_exists('UPS_ACSRL_ADDITIONAL_HADING', $data[self::ACCESSORIALS])
            ) {
                $Package[$key]["AdditionalHandlingIndicator"] = "";
            }
            if (isset($data[self::ACCESSORIALS])
                && array_key_exists('UPS_ACSRL_DECLARED_VALUE', $data[self::ACCESSORIALS])
            ) {
                $Package[$key]["PackageServiceOptions"]["DeclaredValue"] = [
                    "Type" => [
                        "Code" => "01",
                        "Descripton" => "EVS"
                    ],
                    self::CURRENCYCODE => $data[self::CURRENCYCODE],
                    self::MONETARYVALUE => preg_replace(self::CHECK, '', (string)$data[self::MONETARYVALUE])
                ];
            }
        }
        $dataAdd1 = $data[self::SHIPPER][self::ADDRESS][self::ADDRESSLINE][0];
        $dataAdd2 = $data[self::SHIPPER][self::ADDRESS][self::ADDRESSLINE][1];
        $dataAdd3 = $data[self::SHIPPER][self::ADDRESS][self::ADDRESSLINE][2];
        $dataAddShipTo1 = $data[self::SHIPTO][self::ADDRESS][self::ADDRESSLINE][0];
        $dataAddShipTo2 = $data[self::SHIPTO][self::ADDRESS][self::ADDRESSLINE][1];
        $dataAddShipTo3 = $data[self::SHIPTO][self::ADDRESS][self::ADDRESSLINE][2];
        $dataAddShipFrom1 = $data[self::SHIPFROM][self::ADDRESS][self::ADDRESSLINE][0];
        $dataAddShipFrom2 = $data[self::SHIPFROM][self::ADDRESS][self::ADDRESSLINE][1];
        $dataAddShipFrom3 = $data[self::SHIPFROM][self::ADDRESS][self::ADDRESSLINE][2];

        $stateProvinceShipTo = $data[self::SHIPTO][self::ADDRESS][self::STATEPROVINCECODE];
        if (!in_array($toCountry, \UPS\Shipping\Helper\Config::LISTSTATECOUNTRY) && strlen($stateProvinceShipTo) > 5) {
            $stateProvinceShipTo = substr($stateProvinceShipTo, 0, 5);
        }
        $request->ShipmentRequest = [
            'Request' => [
                'RequestOption' =>  'validate',
                'SubVersion' => '1801'
            ],
            'Shipment' => [
                self::DESCRIPTION => self::DESCRIPTION,
                self::SHIPPER => [
                    'Name' => $data[self::SHIPPER]['Name'],
                    self::ATTENTIONNAME => $data[self::SHIPPER][self::ATTENTIONNAME],
                    'ShipperNumber' => $data[self::SHIPPER]['ShipperNumber'],
                    self::PHONE => [
                        self::NUMBER => $data[self::SHIPPER][self::PHONE][self::NUMBER]
                    ],
                    self::ADDRESS => [
                        self::ADDRESSLINE => [
                            (mb_strlen($dataAdd1)>self::MAXLENGTH)
                            ? mb_strimwidth(strip_tags($dataAdd1), 0, self::MAXLENGTH, "") : $dataAdd1,
                            (mb_strlen($dataAdd2)>self::MAXLENGTH)
                            ? mb_strimwidth(strip_tags($dataAdd2), 0, self::MAXLENGTH, "") : $dataAdd2,
                            (mb_strlen($dataAdd3)>self::MAXLENGTH)
                            ? mb_strimwidth(strip_tags($dataAdd3), 0, self::MAXLENGTH, "") : $dataAdd3
                        ],
                        'City' => $data[self::SHIPPER][self::ADDRESS]['City'],
                        self::STATEPROVINCECODE => $data[self::SHIPPER][self::ADDRESS][self::STATEPROVINCECODE],
                        self::POSTALCODE => $data[self::SHIPPER][self::ADDRESS][self::POSTALCODE],
                        self::COUNTRYCODE => $data[self::SHIPPER][self::ADDRESS][self::COUNTRYCODE]
                    ]
                ],
                self::SHIPTO => [
                    'Name' => $data[self::SHIPTO]['Name'],
                    self::ATTENTIONNAME => $data[self::SHIPTO][self::ATTENTIONNAME],
                    self::PHONE => [
                        self::NUMBER => $data[self::SHIPTO][self::PHONE][self::NUMBER]
                    ],
                    self::ADDRESS => [
                        self::ADDRESSLINE => [
                            (mb_strlen($dataAddShipTo1)>self::MAXLENGTH)
                            ? mb_strimwidth(strip_tags($dataAddShipTo1), 0, self::MAXLENGTH, "") : $dataAddShipTo1,
                            (mb_strlen($dataAddShipTo2)>self::MAXLENGTH)
                            ? mb_strimwidth(strip_tags($dataAddShipTo2), 0, self::MAXLENGTH, "") : $dataAddShipTo2,
                            (mb_strlen($dataAddShipTo3)>self::MAXLENGTH)
                            ? mb_strimwidth(strip_tags($dataAddShipTo3), 0, self::MAXLENGTH, "") : $dataAddShipTo3
                        ],
                        'City' => $data[self::SHIPTO][self::ADDRESS]['City'],
                        self::STATEPROVINCECODE => $stateProvinceShipTo,
                        self::POSTALCODE => $data[self::SHIPTO][self::ADDRESS][self::POSTALCODE],
                        self::COUNTRYCODE => $data[self::SHIPTO][self::ADDRESS][self::COUNTRYCODE]
                    ]
                ],
                self::SHIPFROM => [
                    'Name' => $data[self::SHIPFROM]['Name'],
                    self::ATTENTIONNAME => $data[self::SHIPFROM][self::ATTENTIONNAME],
                    self::PHONE => [
                        self::NUMBER => $data[self::SHIPFROM][self::PHONE][self::NUMBER]
                    ],
                    self::ADDRESS => [
                        self::ADDRESSLINE => [
                            (mb_strlen($dataAddShipFrom1)>self::MAXLENGTH)
                            ? mb_strimwidth(strip_tags($dataAddShipFrom1), 0, self::MAXLENGTH, "") : $dataAddShipFrom1,
                            (mb_strlen($dataAddShipFrom2)>self::MAXLENGTH)
                            ? mb_strimwidth(strip_tags($dataAddShipFrom2), 0, self::MAXLENGTH, "") : $dataAddShipFrom2,
                            (mb_strlen($dataAddShipFrom3)>self::MAXLENGTH)
                            ? mb_strimwidth(strip_tags($dataAddShipFrom3), 0, self::MAXLENGTH, "") : $dataAddShipFrom3
                        ],
                        'City' => $data[self::SHIPFROM][self::ADDRESS]['City'],
                        self::STATEPROVINCECODE => $data[self::SHIPFROM][self::ADDRESS][self::STATEPROVINCECODE],
                        self::POSTALCODE => $data[self::SHIPFROM][self::ADDRESS][self::POSTALCODE],
                        self::COUNTRYCODE => $data[self::SHIPFROM][self::ADDRESS][self::COUNTRYCODE]
                    ]
                ],
                self::SERVICE => [
                    'Code' => trim($data[self::SERVICE]['Code']),
                    self::DESCRIPTION => $data[self::SERVICE][self::DESCRIPTION]
                ],
                self::PAYMENTINFORMATION => [
                    self::SHIPMENTCHARGE => [
                        [
                            'Type' => '01',
                            self::BILLSHIPPER => [
                                self::ACCOUNTNUMBER => $data[self::PAYMENTINFORMATION][self::SHIPMENTCHARGE]
                                [self::BILLSHIPPER][self::ACCOUNTNUMBER]
                            ]
                        ]
                    ]
                ],
                'LabelSpecification' => [
                    'LabelImageFormat' => [
                        'Code' => 'GIF',
                        self::DESCRIPTION => 'GIF'
                    ],
                    'HTTPUserAgent' => 'Mozilla/4.5'
                ],
                self::PACKAGE => $Package
            ]
        ];
        //this is
        /*$request->ShipmentRequest[self::SHIPMENT]["PaymentInformation"][self::SHIPMENTCHARGE][]
            = $this->getPackageData($data);*/

        if (isset($data[self::ACCESSORIALS])) {
            foreach ($data[self::ACCESSORIALS] as $key => $value) {
                if ($key == 'UPS_ACSRL_QV_SHIP_NOTIF' || $key == 'UPS_ACSRL_QV_DLV_NOTIF') {
                    $Notification[] = $this->switchNotification($key, $data);
                } else {
                    $this->setShipmentRequest($request->ShipmentRequest[self::SHIPMENT], $key, $data);
                }
            }
        }
        if ($data['ShippingType'] == 'AP') {
            $countryCode = $data[self::SHIPTO][self::ADDRESS][self::COUNTRYCODE];
            $Notification[] = [
                self::NOTIFICATIONCODE => "012",
                self::EMAIL_MAIN => [
                    self::EMAILADDRESS => $data[self::SHIPTO][self::EMAIL]
                ],
                self::LOCALE => $this->getNotificationLocale($countryCode)
            ];
            $request->ShipmentRequest[self::SHIPMENT]['ShipmentIndicationType'] = [
                "Code" => "01"
            ];
            $DataAddressLine = $data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS][self::ADDRESSLINE];
            $request->ShipmentRequest[self::SHIPMENT][self::ALTERNATEDELIVERYADDRESS] = [
                "Name" => $data[self::ALTERNATEDELIVERYADDRESS]['Name'],
                "AttentionName" => $data[self::ALTERNATEDELIVERYADDRESS][self::ATTENTIONNAME],
                self::ADDRESS => [
                    self::ADDRESSLINE
                    => (mb_strlen($DataAddressLine)
                    > self::MAXLENGTH)
                    ? mb_strimwidth(strip_tags($DataAddressLine), 0, self::MAXLENGTH, "") : $DataAddressLine,
                    "City" => $data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS]['City'],
                    self::STATEPROVINCECODE
                    => $data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS][self::STATEPROVINCECODE],
                    self::POSTALCODE => $data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS][self::POSTALCODE],
                    self::COUNTRYCODE => $data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS][self::COUNTRYCODE]
                ]
            ];
        }
        if (isset($data[self::INVOICELINETOTAL]) && isset($data[self::INVOICELINETOTAL][self::CURRENCYCODE])
            && isset($data[self::INVOICELINETOTAL][self::MONETARYVALUE])
        ) {
            $request->ShipmentRequest[self::SHIPMENT][self::INVOICELINETOTAL] = [ // thông tin tiền tệ
                self::CURRENCYCODE => $data[self::INVOICELINETOTAL][self::CURRENCYCODE],
                self::MONETARYVALUE
                => preg_replace(self::REPLACE, '', (string) $data[self::INVOICELINETOTAL][self::MONETARYVALUE])
            ];
        }
        $request->ShipmentRequest[self::SHIPMENT][self::SHIPMENTSERVICEOPTIONS]["Notification"] = $Notification;
        $request->ShipmentRequest[self::SHIPMENT][self::SHIPMENTRATINGOPTIONS]["NegotiatedRatesIndicator"] = '';
        $this->setRequestByObject($request);
        $this->setApiUrlByName('Ship');
        $doRequestObject = $this->doRequest();
        if (!empty($doRequestObject)) {
            return $doRequestObject->getBody();
        } else {
            return $doRequestObject;
        }
    }

    /**
     * Rate switchNotification
     *
     * @param string $key  //The key
     * @param string $data //The data
     *
     * @return $requestApi
     */
    public function switchNotification($key, $data)
    {
        $notification = [];
        $countryCode = $data[self::SHIPTO][self::ADDRESS][self::COUNTRYCODE];
        switch (trim($key)) {
        case 'UPS_ACSRL_QV_SHIP_NOTIF':
            $notification = [
                self::NOTIFICATIONCODE => "6",
                self::EMAIL_MAIN => [
                    self::EMAILADDRESS => $data[self::SHIPTO][self::EMAIL]
                ],
                self::LOCALE => $this->getNotificationLocale($countryCode)
            ];
            break;
        case 'UPS_ACSRL_QV_DLV_NOTIF':
            $notification = [
                self::NOTIFICATIONCODE => "8",
                self::EMAIL_MAIN => [
                    self::EMAILADDRESS => $data[self::SHIPTO][self::EMAIL]
                ],
                self::LOCALE => $this->getNotificationLocale($countryCode)
            ];
            break;
        }
        return $notification;
    }



    /**
     * Ship getNotificationLocale
     *
     * @param string $countryCode  //The country code
     *
     * @return $requestApi
     */
    public function getNotificationLocale($countryCode)
    {
        $locale = [
            'CZ' => [
                self::LANGUAGE => "CES",
                self::DIALECT => "97"
            ],
            'DK' => [
                self::LANGUAGE => "DAN",
                self::DIALECT => "97"
            ],
            'DE' => [
                self::LANGUAGE => "DEU",
                self::DIALECT => "97"
            ],
            'GR' => [
                self::LANGUAGE => "ELL",
                self::DIALECT => "97"
            ],
            'EN' => [
                self::LANGUAGE => "ENG",
                self::DIALECT => "GB"
            ],
            'FI' => [
                self::LANGUAGE => "FIN",
                self::DIALECT => "97"
            ],
            'FR' => [
                self::LANGUAGE => "FRA",
                self::DIALECT => "97"
            ],
            'BE' => [
                self::LANGUAGE => "FRA",
                self::DIALECT => "97"
            ],
            'HU' => [
                self::LANGUAGE => "HUN",
                self::DIALECT => "97"
            ],
            'IT' => [
                self::LANGUAGE => "ITA",
                self::DIALECT => "97"
            ],
            'NL' => [
                self::LANGUAGE => "NLD",
                self::DIALECT => "97"
            ],
            'NO' => [
                self::LANGUAGE => "NOR",
                self::DIALECT => "97"
            ],
            'PL' => [
                self::LANGUAGE => "POL",
                self::DIALECT => "97"
            ],
            'RO' => [
                self::LANGUAGE => "RON",
                self::DIALECT => "RO"
            ],
            'RU' => [
                self::LANGUAGE => "RUS",
                self::DIALECT => "97"
            ],
            'SK' => [
                self::LANGUAGE => "SLK",
                self::DIALECT => "97"
            ],
            'ES' => [
                self::LANGUAGE => "SPA",
                self::DIALECT => "97"
            ],
            'SE' => [
                self::LANGUAGE => "SWE",
                self::DIALECT => "97"
            ],
            'TR' => [
                self::LANGUAGE => "TUR",
                self::DIALECT => "97"
            ]
        ];
        if (array_key_exists($countryCode, $locale)) {
            return $locale[$countryCode];
        } else {
            return [
                self::LANGUAGE => "ENG",
                self::DIALECT => "GB"
            ];
        }
    }

    /**
     * Rate setShipmentRequest
     *
     * @param string $arrShipmentRequest //The arrShipmentRequest
     * @param string $key                //The key
     * @param string $data               //The data
     *
     * @return $requestApi
     */
    public function setShipmentRequest(&$arrShipmentRequest, $key, $data)
    {
        $fromCountry = $data[self::SHIPFROM][self::ADDRESS][self::COUNTRYCODE];
        $toCountry = $data[self::SHIPTO][self::ADDRESS][self::COUNTRYCODE];
        switch (trim($key)) {
        case 'UPS_ACSRL_RESIDENTIAL_ADDRESS':
            $arrShipmentRequest["ShipTo"][self::ADDRESS]["ResidentialAddressIndicator"] = "1";
            break;
        case 'UPS_ACSRL_STATURDAY_DELIVERY':
            $arrShipmentRequest[self::SHIPMENTSERVICEOPTIONS]["SaturdayDeliveryIndicator"] = "";
            break;
        case 'UPS_ACSRL_CARBON_NEUTRAL':
            $arrShipmentRequest[self::SHIPMENTSERVICEOPTIONS]["UPScarbonneutralIndicator"] = "";
            break;
        case 'UPS_ACSRL_DIRECT_DELIVERY_ONLY':
            $arrShipmentRequest[self::SHIPMENTSERVICEOPTIONS]["DirectDeliveryOnlyIndicator"] = "";
            break;
        case 'UPS_ACSRL_SIGNATURE_REQUIRED':
            if ($fromCountry != 'US' || $toCountry != 'US') {
                $arrShipmentRequest[self::SHIPMENTSERVICEOPTIONS]["DeliveryConfirmation"]["DCISType"] = "1";
            }
            break;
        case 'UPS_ACSRL_ADULT_SIG_REQUIRED':
            if ($fromCountry != 'US' || $toCountry != 'US') {
                $arrShipmentRequest[self::SHIPMENTSERVICEOPTIONS]["DeliveryConfirmation"]["DCISType"] = "2";
            }
            break;
        case 'UPS_ACSRL_ACCESS_POINT_COD':
            $arrShipmentRequest[self::SHIPMENTSERVICEOPTIONS]["AccessPointCOD"] = [
                self::CURRENCYCODE => $data[self::CURRENCYCODE],
                self::MONETARYVALUE => preg_replace(self::CHECK, '', (string)$data[self::MONETARYVALUE])
            ];
            break;
        case 'UPS_ACSRL_TO_HOME_COD':
            $codFundsCode = '1';
            if (!in_array($toCountry, \UPS\Shipping\Helper\Config::ARRAYCOUNTRYCODE)) {
                $arrShipmentRequest[self::SHIPMENTSERVICEOPTIONS]["COD"] = [
                    "CODFundsCode" => $codFundsCode,
                    "CODAmount" => [
                        self::CURRENCYCODE => $data[self::CURRENCYCODE],
                        self::MONETARYVALUE => preg_replace(self::CHECK, '', (string)$data[self::MONETARYVALUE])
                    ]
                ];
            }
            break;
        default:
            break;
        }
    }

    /**
     * Rate getPackageData
     *
     * @param string $data //from request form.
     *
     * @return array $returnData
     */
    public function getPackageData($data)
    {
        $returnData = [];
        if (!in_array($data[self::SHIPTO][self::ADDRESS][self::COUNTRYCODE], self::COUNTRY_EU)
            ||(isset($data[self::ALTERNATEDELIVERYADDRESS])
            && isset($data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS])
            && !in_array($data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS][self::COUNTRYCODE], self::EIGHTEUCOUNTRY)
            && $data['ShippingType'] == 'AP'
            && isset($data[self::ALTERNATEDELIVERYADDRESS]['COD'])
            && $data[self::ALTERNATEDELIVERYADDRESS]['COD'] == '1')
        ) {
            $returnData = [
                'Type' => '02',
                self::BILLSHIPPER => [
                    self::ACCOUNTNUMBER
                    => $data[self::PAYMENTINFORMATION][self::SHIPMENTCHARGE][self::BILLSHIPPER][self::ACCOUNTNUMBER]
                ]
            ];
        }
        return $returnData;
    }
}
