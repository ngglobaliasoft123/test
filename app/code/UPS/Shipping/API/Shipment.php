<?php

/**
 * Shipment file
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
 * Shipment file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Shipment extends ClientAPI
{
    // ['InquiryNumber' => '1Z12345E0205271688']
    /**
     * Shipment request create tracking
     *
     * @param string $data //The data
     *
     * @return $request api
     */
    public function tracking($data)
    {
        if (self::ENV == 'DEV') {
            $trackingNumber = '1Z12345E0205271688';
        } else {
            $trackingNumber = $data['InquiryNumber'];
        }
        // get Base Request2
        $request = $this->getBaseRequest2();
        $request->TrackRequest = [
            "Request" => [
                "RequestOption" => "",
                "TransactionReference" => [
                    "CustomerContext" => "",
                ]
            ],
            "InquiryNumber" => $trackingNumber
        ];
        $this->setRequestByObject($request);
        $this->setApiUrlByName('Track');
        return $this->doRequest()->getBody();
    }

    // ['VoidShipment' => ['ShipmentIdentificationNumber' => 1ZISDE016691676846']]
    /**
     * Shipment VoidShipment
     *
     * @param string $data //The data
     *
     * @return $request api
     */
    public function voidShipment($data)
    {
        if (self::ENV == 'DEV') {
            $shipmentNumber = '1ZISDE016691676846';
        } else {
            $shipmentNumber = $data['VoidShipment']['ShipmentIdentificationNumber'];
        }
        // get Base Request2
        $request = $this->getBaseRequest2();
        $request->VoidShipmentRequest = [
            'Request' => [
                'TransactionReference' => [
                    'CustomerContext' => "",
                ]
            ],
            'VoidShipment' => [
                'ShipmentIdentificationNumber' => $shipmentNumber,
            ]
        ];
        $this->setRequestByObject($request);
        $this->setApiUrlByName('Void');
        return $this->doRequest()->getBody();
    }

    // ['TrackingNumber' => '1Z12345E8791315509']
    /**
     * Shipment LabelRecovery
     *
     * @param string $data //The data
     *
     * @return $request api
     */
    public function labelRecovery($data)
    {
        if (self::ENV == 'DEV') {
            $trackingNumber = '1Z12345E8791315509';
        } else {
            $trackingNumber = (!empty($data['TrackingNumber']) ? trim($data['TrackingNumber']) : '');
        }
        // get Base Request2
        $request = $this->getBaseRequest2();
        $request->LabelRecoveryRequest = [
            'RequestOption' => [
                'SubVersion' => '1701'
            ],
            'LabelSpecification' => [
                'LabelImageFormat' => [
                    'Code' => (!empty($data['LabelFormat']) ? trim($data['LabelFormat']) : ''),
                ],
                'HTTPUserAgent' => 'Mozilla/4.5',
            ],
            'Translate' => [
                'LanguageCode' => 'eng',
                'DialectCode' => 'GB',
                'Code' => '01',
            ],
            'TrackingNumber' => $trackingNumber
        ];
        // set Request By Object
        $this->setRequestByObject($request);
        $this->setApiUrlByName('LBRecovery');
        return $this->doRequest()->getBody();
    }

}
