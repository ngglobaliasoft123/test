<?php

/**
 * Locator file
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
 * Locator file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Locator extends ClientAPI
{
    protected $uri = 'https://onlinetools.ups.com/rest/Locator';

    /**
     * Input [
     *      "fullAddress"=> "1042 S Laurel Rd, London, KY 40744",
     *      "countryCode"=> "US",
     *      "Locale" => "en-US",
     *      "UnitOfMeasurement"=> "KM",
     *      "nearby"=> "5"
     * ]
     */

    /**
     * Locator function load address
     *
     * @param string $args //The args
     *
     * @return $request request load address
     */
    public function loadAddress($args)
    {
        $request = (object)[];
        $License = $this->getLicense();
        $request->AccessRequest = [
            "AccessLicenseNumber" => $License["SERVICE_ACCESS_TOKEN"],
            "Username" => $License["USERNAME"],
            "Password" => $License["PASSWORD"]
        ];
        $request->LocatorRequest = [
            "Request" => [
                "RequestAction" => "Locator",
                "RequestOption" => "64",
                "TransactionReference" => ""
            ],
            "OriginAddress" => [
                "PhoneNumber" => "",
                "AddressKeyFormat" => [
                    "SingleLineAddress" => $args['fullAddress'],
                    "CountryCode" => $args['countryCode']
                ]
            ],
            "Translate" => [
                // 'en-US'
                "Locale" => $args['Locale'],
            ],
            "UnitOfMeasurement" => [
                // 'KM'
                "Code" => $args['UnitOfMeasurement']
            ],
            "LocationSearchCriteria" => [
                "MaximumListSize" => $args['MaximumListSize'],
                "SearchRadius" => $args['nearby']
            ]
        ];
        $this->setRequestByObject($request);
        $this->setUri($this->uri);
        return $this->doRequest()->getBody();
    }
}
