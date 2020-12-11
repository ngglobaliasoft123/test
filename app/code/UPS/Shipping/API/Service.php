<?php

/**
 * Service file
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
 * Service file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Service extends ClientAPI
{
    /**
     * Service request cut off time
     *
     * @return $request api
     */
    public function cutOffTime()
    {
        // get Base Request2
        $request = $this->getBaseRequest2();
        //Get request Time In Transit Request
        $request->TimeInTransitRequest = [
            "Request" => [
                "RequestOption" => "TNT",
                "TransactionReference" => [
                    "CustomerContext" => "",
                    "TransactionIdentifier" => ""
                ]
            ],
            //Request ship From
            "ShipFrom" => [
                "Address" => [
                    "StateProvinceCode" => "GA",
                    "CountryCode" => "US",
                    "PostalCode" => "30076"
                ]
            ],
            //Request ship To
            "ShipTo" => [
                "Address" => [
                    "StateProvinceCode" => "GA",
                    "CountryCode" => "US",
                    "PostalCode" => "30076"
                ]
            ],
            //Request Pickup
            "Pickup" => [
                "Date" => "20180910"
            ],
            //Request Shipment Weight
            "ShipmentWeight" => [
                "UnitOfMeasurement" => [
                    "Code" => "KGS",
                    "Description" => "Kilograms"
                ],
                "Weight" => "10"
            ],
            "MaximumListSize" => "1"
        ];
        $this->setRequestByObject($request);
        $this->setApiUrlByName('TimeInTransit');
        return $this->doRequest()->getBody();
    }
}
