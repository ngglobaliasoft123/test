<?php

/**
 * Rate file
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
 * Rate file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Rate extends ClientAPI
{
    const REQUEST = "Request";
    const REQUESTOPTION = "RequestOption";
    const SHIPMENT = "Shipment";
    const SHIPPER = 'Shipper';
    const ADDRESS = 'Address';
    const ADDRESSLINE = 'AddressLine';
    const STATEPROVINCECODE = 'StateProvinceCode';
    const POSTALCODE = 'PostalCode';
    const COUNTRYCODE = 'CountryCode';
    const SHIPTO = 'ShipTo';
    const SHIPFROM = 'ShipFrom';
    const PAYMENTDETAILS = 'PaymentDetails';
    const SHIPMENTCHARGE = 'ShipmentCharge';
    const BILLSHIPPER = 'BillShipper';
    const ACCOUNTNUMBER = 'AccountNumber';
    const PACKAGE = 'Package';
    const PACKAGEWEIGHT = 'PackageWeight';
    const WEIGHT = 'Weight';
    const UNITOFMEASUREMENT = 'UnitOfMeasurement';
    const DESCRIPTION = 'Description';
    const ACCESSORIALS = 'accessorials';
    const PACKAGINGTYPE = "PackagingType";
    const DIMENSIONS = 'Dimensions';
    const PACKAGESERVICEOPTIONS = "PackageServiceOptions";
    const ALTERNATEDELIVERYADDRESS = 'AlternateDeliveryAddress';
    const LENGTH = 'Length';
    const WIDTH = 'Width';
    const HEIGHT = 'Height';
    const SERVICE = 'Service';
    const INVOICELINETOTAL = "InvoiceLineTotal";
    const CURRENCYCODE = "CurrencyCode";
    const MONETARYVALUE = "MonetaryValue";
    const SHIPMENTSERVICEOPTIONS = "ShipmentServiceOptions";
    const REPLACE = '([^a-zA-Z0-9.])';

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

    const EIGHTEUCOUNTRY = \UPS\Shipping\Helper\Config::LISTEUCOUNTRY;
    /**
     * Rate shopTimeInTransit
     *
     * @param string $data //The data
     *
     * @return $requestApi
     */
    public function shopTimeInTransit($data)
    {
        $request = $this->getBaseRequest2();
        $request->RateRequest = [
            self::REQUEST => [
                self::REQUESTOPTION => $data['Request']['RequestOption'],
                'TransactionReference' => [
                    'CustomerContext' => ''
                ]
            ],
            self::SHIPMENT => [
                self::SHIPPER => [ //Địa chỉ account
                    "Name" => $data[self::SHIPPER]['Name'],
                    "ShipperNumber" => $data[self::SHIPPER]['ShipperNumber'],
                    self::ADDRESS => [
                        self::ADDRESSLINE => [
                            $data[self::SHIPPER][self::ADDRESS][self::ADDRESSLINE][0],
                            $data[self::SHIPPER][self::ADDRESS][self::ADDRESSLINE][1],
                            $data[self::SHIPPER][self::ADDRESS][self::ADDRESSLINE][2],
                        ],
                        "City" => $data[self::SHIPPER][self::ADDRESS]['City'],
                        self::STATEPROVINCECODE => $data[self::SHIPPER][self::ADDRESS][self::STATEPROVINCECODE],
                        self::POSTALCODE => $data[self::SHIPPER][self::ADDRESS][self::POSTALCODE],
                        self::COUNTRYCODE => $data[self::SHIPPER][self::ADDRESS][self::COUNTRYCODE]
                    ]
                ],
                self::SHIPTO => [ // Địa chỉ e-shopper
                    "Name" => $data[self::SHIPTO]['Name'],
                    self::ADDRESS => [
                        self::ADDRESSLINE => [
                            $data[self::SHIPTO][self::ADDRESS][self::ADDRESSLINE][0],
                            $data[self::SHIPTO][self::ADDRESS][self::ADDRESSLINE][1],
                            $data[self::SHIPTO][self::ADDRESS][self::ADDRESSLINE][2]
                        ],
                        "City" => $data[self::SHIPTO][self::ADDRESS]['City'],
                        self::STATEPROVINCECODE => $data[self::SHIPTO][self::ADDRESS][self::STATEPROVINCECODE],
                        self::POSTALCODE => $data[self::SHIPTO][self::ADDRESS][self::POSTALCODE],
                        self::COUNTRYCODE => $data[self::SHIPTO][self::ADDRESS][self::COUNTRYCODE],
                    ]
                ],
                self::SHIPFROM => [ //Địa chỉ account
                    "Name" => $data[self::SHIPFROM]['Name'],
                    self::ADDRESS => [
                        self::ADDRESSLINE => [
                            $data[self::SHIPFROM][self::ADDRESS][self::ADDRESSLINE][0],
                            $data[self::SHIPFROM][self::ADDRESS][self::ADDRESSLINE][1],
                            $data[self::SHIPFROM][self::ADDRESS][self::ADDRESSLINE][2]
                        ],
                        "City" => $data[self::SHIPFROM][self::ADDRESS]['City'],
                        self::STATEPROVINCECODE => $data[self::SHIPFROM][self::ADDRESS][self::STATEPROVINCECODE],
                        self::POSTALCODE => $data[self::SHIPFROM][self::ADDRESS][self::POSTALCODE],
                        self::COUNTRYCODE => $data[self::SHIPFROM][self::ADDRESS][self::COUNTRYCODE]
                    ]
                ],
                self::PAYMENTDETAILS => [
                    self::SHIPMENTCHARGE => [
                        [
                            "Type" => '01',
                            self::BILLSHIPPER => [
                                self::ACCOUNTNUMBER => $data[self::PAYMENTDETAILS][self::SHIPMENTCHARGE]
                                [self::BILLSHIPPER][self::ACCOUNTNUMBER]
                            ]
                        ]
                    ]
                ],
                "ShipmentRatingOptions" => [
                    "NegotiatedRatesIndicator" => ""
                ]
            ]
        ];

        /*if (!in_array($data[self::SHIPTO][self::ADDRESS][self::COUNTRYCODE], self::COUNTRY_EU)
            ||(isset($data[self::ALTERNATEDELIVERYADDRESS])
            && isset($data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS])
            && !in_array($data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS][self::COUNTRYCODE], self::EIGHTEUCOUNTRY)
            && $data['ShippingType'] == 'AP'
            && isset($data[self::ALTERNATEDELIVERYADDRESS]['COD'])
            && $data[self::ALTERNATEDELIVERYADDRESS]['COD'] == '1')
        ) {
            $request->RateRequest[self::SHIPMENT][self::PAYMENTDETAILS][self::SHIPMENTCHARGE][] = [
                'Type' => '02',
                self::BILLSHIPPER => [
                    self::ACCOUNTNUMBER => $data[self::PAYMENTDETAILS][self::SHIPMENTCHARGE]
                    [self::BILLSHIPPER][self::ACCOUNTNUMBER]
                ]
            ];
        }*/

        $ShipmentTotalWidth = 0;
        $ShipmentTotalCode = "";
        $ShipmentTotalDescription = "";
        //this
        $this->getPackageData($Package, $data, $ShipmentTotalWidth, $ShipmentTotalCode, $ShipmentTotalDescription);
        if ($data['ShippingType'] == 'AP' && isset($data[self::ALTERNATEDELIVERYADDRESS])) {
            $request->RateRequest[self::SHIPMENT]['ShipmentIndicationType'] = [
                "Code" => "01"
            ];
            $request->RateRequest[self::SHIPMENT][self::ALTERNATEDELIVERYADDRESS] = [
                "Name" => $data[self::ALTERNATEDELIVERYADDRESS]['Name'],
                "AttentionName" => $data[self::ALTERNATEDELIVERYADDRESS]['AttentionName'],
                self::ADDRESS => [
                    self::ADDRESSLINE => $data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS][self::ADDRESSLINE],
                    "City" => $data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS]['City'],
                    self::STATEPROVINCECODE
                    => $data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS][self::STATEPROVINCECODE],
                    self::POSTALCODE => $data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS][self::POSTALCODE],
                    self::COUNTRYCODE => $data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS][self::COUNTRYCODE]
                ]
            ];
        }

        if ($data[self::REQUEST][self::REQUESTOPTION] == "Shoptimeintransit") {
            $request->RateRequest[self::REQUEST]["SubVersion"] = "1801";
            $request->RateRequest[self::SHIPMENT]["DeliveryTimeInformation"] = [
                "PackageBillType" => "03",
                "Pickup" => [
                    "Date" => $data['DeliveryTimeInformation']['Pickup']['Date']
                ]
            ];
            if (isset($data['InvoiceLineTotal'])) {
                $request->RateRequest[self::SHIPMENT][self::INVOICELINETOTAL] = [ // thông tin tiền tệ
                    self::CURRENCYCODE => $data[self::INVOICELINETOTAL][self::CURRENCYCODE],
                    self::MONETARYVALUE
                    => preg_replace(self::REPLACE, '', (string) $data[self::INVOICELINETOTAL][self::MONETARYVALUE])
                ];
            }

            $request->RateRequest[self::SHIPMENT]["ShipmentTotalWeight"] = [
                self::UNITOFMEASUREMENT => [
                    "Code" => strtoupper($ShipmentTotalCode),
                    self::DESCRIPTION => $ShipmentTotalDescription
                ],
                self::WEIGHT => $ShipmentTotalWidth
            ];
        }

        if (isset($data[self::ACCESSORIALS])) {
            foreach ($data[self::ACCESSORIALS] as $key => $value) {
                if ($key == 'UPS_ACSRL_DECLARED_VALUE') {
                    foreach ($Package as $key => $value) {
                        $monetaryValue = (string) $data[self::INVOICELINETOTAL][self::MONETARYVALUE];
                        $Package[$key][self::PACKAGESERVICEOPTIONS] = [
                            "DeclaredValue" => [
                                "Type" => [
                                    "Code" => "01",
                                    "Descripton" => "EVS"
                                ],
                                self::CURRENCYCODE => $data[self::INVOICELINETOTAL][self::CURRENCYCODE],
                                self::MONETARYVALUE => preg_replace(self::REPLACE, '', $monetaryValue)
                            ]
                        ];
                    }
                } else {
                    $this->setRateRequest($request->RateRequest[self::SHIPMENT], $key, $data);
                }
            }
        }
        $request->RateRequest[self::SHIPMENT][self::PACKAGE] = $Package;
        $this->setRequestByObject($request);
        $this->setApiUrlByName('Rate');
        return $this->doRequest()->getBody();
    }

    /**
     * Rate Rate
     *
     * @param string $data //The data
     *
     * @return $requestApi
     */
    public function rate($data)
    {
        $request = $this->getBaseRequest2();
        $request->RateRequest = [];
        $request->RateRequest = [
            self::REQUEST => [
                self::REQUESTOPTION => $data['Request']['RequestOption'],
                'TransactionReference' => [
                    'CustomerContext' => ''
                ]
            ],
            self::SHIPMENT => [
                self::SHIPPER => [ //Địa chỉ account
                    "Name" => $data[self::SHIPPER]['Name'],
                    "ShipperNumber" => $data[self::SHIPPER]['ShipperNumber'],
                    self::ADDRESS => [
                        self::ADDRESSLINE => [
                            $data[self::SHIPPER][self::ADDRESS][self::ADDRESSLINE][0],
                            $data[self::SHIPPER][self::ADDRESS][self::ADDRESSLINE][1],
                            $data[self::SHIPPER][self::ADDRESS][self::ADDRESSLINE][2],
                        ],
                        "City" => $data[self::SHIPPER][self::ADDRESS]['City'],
                        self::STATEPROVINCECODE => $data[self::SHIPPER][self::ADDRESS][self::STATEPROVINCECODE],
                        self::POSTALCODE => $data[self::SHIPPER][self::ADDRESS][self::POSTALCODE],
                        self::COUNTRYCODE => $data[self::SHIPPER][self::ADDRESS][self::COUNTRYCODE]
                    ]
                ],
                self::SHIPTO => [ // Địa chỉ e-shopper
                    "Name" => $data[self::SHIPTO]['Name'],
                    self::ADDRESS => [
                        self::ADDRESSLINE => [
                            $data[self::SHIPTO][self::ADDRESS][self::ADDRESSLINE][0],
                            $data[self::SHIPTO][self::ADDRESS][self::ADDRESSLINE][1],
                            $data[self::SHIPTO][self::ADDRESS][self::ADDRESSLINE][2]
                        ],
                        "City" => $data[self::SHIPTO][self::ADDRESS]['City'],
                        self::STATEPROVINCECODE => $data[self::SHIPTO][self::ADDRESS][self::STATEPROVINCECODE],
                        self::POSTALCODE => $data[self::SHIPTO][self::ADDRESS][self::POSTALCODE],
                        self::COUNTRYCODE => $data[self::SHIPTO][self::ADDRESS][self::COUNTRYCODE],
                    ]
                ],
                self::SHIPFROM => [ //Địa chỉ account
                    "Name" => $data[self::SHIPFROM]['Name'],
                    self::ADDRESS => [
                        self::ADDRESSLINE => [
                            $data[self::SHIPFROM][self::ADDRESS][self::ADDRESSLINE][0],
                            $data[self::SHIPFROM][self::ADDRESS][self::ADDRESSLINE][1],
                            $data[self::SHIPFROM][self::ADDRESS][self::ADDRESSLINE][2]
                        ],
                        "City" => $data[self::SHIPFROM][self::ADDRESS]['City'],
                        self::STATEPROVINCECODE => $data[self::SHIPFROM][self::ADDRESS][self::STATEPROVINCECODE],
                        self::POSTALCODE => $data[self::SHIPFROM][self::ADDRESS][self::POSTALCODE],
                        self::COUNTRYCODE => $data[self::SHIPFROM][self::ADDRESS][self::COUNTRYCODE]
                    ]
                ],
                self::PAYMENTDETAILS => [
                    self::SHIPMENTCHARGE => [
                        [
                            "Type" => '01',
                            self::BILLSHIPPER => [
                                self::ACCOUNTNUMBER => $data[self::PAYMENTDETAILS][self::SHIPMENTCHARGE]
                                [self::BILLSHIPPER][self::ACCOUNTNUMBER]
                            ]
                        ]
                    ]
                ],
                "ShipmentRatingOptions" => [
                    "NegotiatedRatesIndicator" => ""
                ]
            ]
        ];

        if (!in_array($data[self::SHIPTO][self::ADDRESS][self::COUNTRYCODE], self::COUNTRY_EU)
            ||(isset($data[self::ALTERNATEDELIVERYADDRESS])
            && isset($data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS])
            && !in_array($data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS][self::COUNTRYCODE], self::EIGHTEUCOUNTRY)
            && $data['ShippingType'] == 'AP'
            && isset($data[self::ALTERNATEDELIVERYADDRESS]['COD'])
            && $data[self::ALTERNATEDELIVERYADDRESS]['COD'] == '1')
        ) {
            $request->RateRequest[self::SHIPMENT][self::PAYMENTDETAILS][self::SHIPMENTCHARGE][] = [
                'Type' => '02',
                self::BILLSHIPPER => [
                    self::ACCOUNTNUMBER => $data[self::PAYMENTDETAILS][self::SHIPMENTCHARGE]
                    [self::BILLSHIPPER][self::ACCOUNTNUMBER]
                ]
            ];
        }

        $ShipmentTotalWidth = 0;
        $ShipmentTotalCode = "";
        $ShipmentTotalDescription = "";
        //this
        $this->getPackageData($Package, $data, $ShipmentTotalWidth, $ShipmentTotalCode, $ShipmentTotalDescription);
        if ($data['ShippingType'] == 'AP' && isset($data[self::ALTERNATEDELIVERYADDRESS])) {
            $request->RateRequest[self::SHIPMENT]['ShipmentIndicationType'] = [
                "Code" => "01"
            ];
            $request->RateRequest[self::SHIPMENT][self::ALTERNATEDELIVERYADDRESS] = [
                "Name" => $data[self::ALTERNATEDELIVERYADDRESS]['Name'],
                "AttentionName" => $data[self::ALTERNATEDELIVERYADDRESS]['AttentionName'],
                self::ADDRESS => [
                    self::ADDRESSLINE => $data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS][self::ADDRESSLINE],
                    "City" => $data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS]['City'],
                    self::STATEPROVINCECODE
                    => $data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS][self::STATEPROVINCECODE],
                    self::POSTALCODE => $data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS][self::POSTALCODE],
                    self::COUNTRYCODE => $data[self::ALTERNATEDELIVERYADDRESS][self::ADDRESS][self::COUNTRYCODE]
                ]
            ];
        }

        if ($data[self::REQUEST][self::REQUESTOPTION] == "RATETIMEINTRANSIT") {
            $request->RateRequest[self::REQUEST]["SubVersion"] = "1801";
            $request->RateRequest[self::SHIPMENT][self::SERVICE] = [
                "Code" => (isset($data[self::SERVICE])) ? trim($data[self::SERVICE]['Code']) : '',
                self::DESCRIPTION => (isset($data[self::SERVICE])) ? $data[self::SERVICE][self::DESCRIPTION] : ''
            ];
            $request->RateRequest[self::SHIPMENT]["DeliveryTimeInformation"] = [
                "PackageBillType" => "03",
                "Pickup" => [
                    "Date" => $data['DeliveryTimeInformation']['Pickup']['Date']
                ]
            ];
            if (isset($data['InvoiceLineTotal'])) {
                $request->RateRequest[self::SHIPMENT][self::INVOICELINETOTAL] = [ // thông tin tiền tệ
                    self::CURRENCYCODE => $data[self::INVOICELINETOTAL][self::CURRENCYCODE],
                    self::MONETARYVALUE
                    => preg_replace(self::REPLACE, '', (string) $data[self::INVOICELINETOTAL][self::MONETARYVALUE])
                ];
            }

            $request->RateRequest[self::SHIPMENT]["ShipmentTotalWeight"] = [
                self::UNITOFMEASUREMENT => [
                    "Code" => strtoupper($ShipmentTotalCode),
                    self::DESCRIPTION => $ShipmentTotalDescription
                ],
                self::WEIGHT => $ShipmentTotalWidth
            ];
        }

        if (isset($data[self::ACCESSORIALS])) {
            foreach ($data[self::ACCESSORIALS] as $key => $value) {
                if ($key == 'UPS_ACSRL_DECLARED_VALUE') {
                    foreach ($Package as $key => $value) {
                        $monetaryValue = (string) $data[self::INVOICELINETOTAL][self::MONETARYVALUE];
                        $Package[$key][self::PACKAGESERVICEOPTIONS]["DeclaredValue"] = [
                            "Type" => [
                                "Code" => "01",
                                "Descripton" => "EVS"
                            ],
                            self::CURRENCYCODE => $data[self::INVOICELINETOTAL][self::CURRENCYCODE],
                            self::MONETARYVALUE => preg_replace(self::REPLACE, '', $monetaryValue)
                        ];
                    }
                } else {
                    $this->setRateRequest($request->RateRequest[self::SHIPMENT], $key, $data);
                }
            }
        }
        $request->RateRequest[self::SHIPMENT][self::PACKAGE] = $Package;
        $this->setRequestByObject($request);
        $this->setApiUrlByName('Rate');
        return $this->doRequest()->getBody();
    }

    /**
     * Rate setRateRequest
     *
     * @param string $arrRateRequest //The arrRateRequest
     * @param string $key            //The key
     * @param string $data           //The data
     *
     * @return $requestApi
     */
    public function setRateRequest(&$arrRateRequest, $key, $data)
    {
        $fromCountry = $data[self::SHIPFROM][self::ADDRESS][self::COUNTRYCODE];
        $toCountry = $data[self::SHIPTO][self::ADDRESS][self::COUNTRYCODE];
        switch ($key) {
        case 'UPS_ACSRL_RESIDENTIAL_ADDRESS':
            $arrRateRequest[self::SHIPTO][self::ADDRESS]["ResidentialAddressIndicator"] = "1";
            break;
        case 'UPS_ACSRL_STATURDAY_DELIVERY':
            $arrRateRequest[self::SHIPMENTSERVICEOPTIONS]["SaturdayDeliveryIndicator"] = "";
            break;
        case 'UPS_ACSRL_CARBON_NEUTRAL':
            $arrRateRequest[self::SHIPMENTSERVICEOPTIONS]["UPScarbonneutralIndicator"] = "";
            break;
        case 'UPS_ACSRL_DIRECT_DELIVERY_ONLY':
            $arrRateRequest[self::SHIPMENTSERVICEOPTIONS]["DirectDeliveryOnlyIndicator"] = "";
            break;
        case 'UPS_ACSRL_SIGNATURE_REQUIRED':
            if ($fromCountry != 'US' || $toCountry != 'US') {
                $arrRateRequest[self::SHIPMENTSERVICEOPTIONS]["DeliveryConfirmation"]["DCISType"] = "1";
            }
            break;
        case 'UPS_ACSRL_ADULT_SIG_REQUIRED':
            if ($fromCountry != 'US' || $toCountry != 'US') {
                $arrRateRequest[self::SHIPMENTSERVICEOPTIONS]["DeliveryConfirmation"]["DCISType"] = "2";
            }
            break;
        case 'UPS_ACSRL_ACCESS_POINT_COD':
            $monetaryValue = $data[self::INVOICELINETOTAL][self::MONETARYVALUE];
            $arrRateRequest[self::SHIPMENTSERVICEOPTIONS]["AccessPointCOD"] = [
                self::CURRENCYCODE => $data[self::INVOICELINETOTAL][self::CURRENCYCODE],
                self::MONETARYVALUE => preg_replace(self::REPLACE, '', (string) $monetaryValue)
            ];
            break;
        case 'UPS_ACSRL_TO_HOME_COD':
            $codFundsCode = '1';
            $monetaryValue = $data[self::INVOICELINETOTAL][self::MONETARYVALUE];
            if (!in_array($toCountry, \UPS\Shipping\Helper\Config::ARRAYCOUNTRYCODE)) {
                $arrRateRequest[self::SHIPMENTSERVICEOPTIONS]["COD"] = [
                    "CODFundsCode" => $codFundsCode,
                    "CODAmount" => [
                        self::CURRENCYCODE => $data[self::INVOICELINETOTAL][self::CURRENCYCODE],
                        self::MONETARYVALUE => preg_replace(self::REPLACE, '', (string) $monetaryValue)
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
     * @param string $Package                  //The Package.
     * @param string $data                     //The data.
     * @param string $ShipmentTotalWidth       //The ShipmentTotalWidth.
     * @param string $ShipmentTotalCode        //The ShipmentTotalCode.
     * @param string $ShipmentTotalDescription //The ShipmentTotalDescription.
     *
     * @return array $returnData
     */
    public function getPackageData(&$Package, $data, &$ShipmentTotalWidth, &$ShipmentTotalCode, &$ShipmentTotalDescription)
    {
        if (isset($data[self::PACKAGE])) {
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
                $monetaryValue = $data[self::INVOICELINETOTAL][self::MONETARYVALUE];
                $packageServiceOptions["COD"] = [
                    "CODFundsCode" => $codFundsCode,
                    "CODAmount" => [
                        self::CURRENCYCODE => $data[self::INVOICELINETOTAL][self::CURRENCYCODE],
                        self::MONETARYVALUE => preg_replace(self::REPLACE, '', (string) $monetaryValue)
                    ]
                ];
            }
            foreach ($data[self::PACKAGE] as $key => $value) {
                $ShipmentTotalWidth = $value[self::PACKAGEWEIGHT][self::WEIGHT];
                $ShipmentTotalCode = $value[self::PACKAGEWEIGHT][self::UNITOFMEASUREMENT]['Code'];
                $ShipmentTotalDescription = $value[self::PACKAGEWEIGHT][self::UNITOFMEASUREMENT][self::DESCRIPTION];
                if (isset($data[self::ACCESSORIALS])
                    && array_key_exists('UPS_ACSRL_ADDITIONAL_HADING', $data[self::ACCESSORIALS])
                ) {
                    $Package[] = [
                        self::PACKAGINGTYPE => [
                            "Code" => "02",
                            self::DESCRIPTION => "Rate"
                        ],
                        self::DIMENSIONS => [
                            self::UNITOFMEASUREMENT => [
                                "Code" => substr(strtoupper($value[self::DIMENSIONS][self::UNITOFMEASUREMENT]['Code']), 0, 2),
                                self::DESCRIPTION => $value[self::DIMENSIONS][self::UNITOFMEASUREMENT]
                                [self::DESCRIPTION]
                            ],
                            self::LENGTH => strval($value[self::DIMENSIONS][self::LENGTH]),
                            self::WIDTH => strval($value[self::DIMENSIONS][self::WIDTH]),
                            self::HEIGHT => strval($value[self::DIMENSIONS][self::HEIGHT])
                        ],
                        self::PACKAGEWEIGHT => [
                            self::UNITOFMEASUREMENT => [
                                "Code" => strtoupper($value[self::PACKAGEWEIGHT][self::UNITOFMEASUREMENT]['Code']),
                                self::DESCRIPTION => $value[self::PACKAGEWEIGHT][self::UNITOFMEASUREMENT]
                                [self::DESCRIPTION]
                            ],
                            self::WEIGHT => strval($value[self::PACKAGEWEIGHT][self::WEIGHT])
                        ],
                        self::PACKAGINGTYPE => [
                            "Code" => "02",
                        ],
                        "AdditionalHandlingIndicator" => "",
                        self::PACKAGESERVICEOPTIONS => $packageServiceOptions
                    ];
                } else {
                    $Package[] = [
                        self::DIMENSIONS => [
                            self::UNITOFMEASUREMENT => [
                                "Code" => substr(strtoupper($value[self::DIMENSIONS][self::UNITOFMEASUREMENT]['Code']), 0, 2),
                                self::DESCRIPTION => $value[self::DIMENSIONS][self::UNITOFMEASUREMENT]
                                [self::DESCRIPTION]
                            ],
                            self::LENGTH => strval($value[self::DIMENSIONS][self::LENGTH]),
                            self::WIDTH => strval($value[self::DIMENSIONS][self::WIDTH]),
                            self::HEIGHT => strval($value[self::DIMENSIONS][self::HEIGHT])
                        ],
                        self::PACKAGEWEIGHT => [
                            self::UNITOFMEASUREMENT => [
                                "Code" => strtoupper($value[self::PACKAGEWEIGHT][self::UNITOFMEASUREMENT]['Code']),
                                self::DESCRIPTION => $value[self::PACKAGEWEIGHT][self::UNITOFMEASUREMENT]
                                [self::DESCRIPTION]
                            ],
                            self::WEIGHT => strval($value[self::PACKAGEWEIGHT][self::WEIGHT])
                        ],
                        self::PACKAGINGTYPE => [
                            "Code" => "02",
                            self::DESCRIPTION => "Rate"
                        ],
                        self::PACKAGESERVICEOPTIONS => $packageServiceOptions
                    ];
                }
            }
        }
    }
}
