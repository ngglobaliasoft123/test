<?php

/**
 * Handshake file
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
 * Handshake file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Handshake extends ClientAPI
{
    /**
     * TermCondition callAPILicense
     *
     * @param string $websiteMerchant    //The websiteMerchant
     * @param string $getMerchantKey     //The getMerchantKey
     * @param string $valueSecurityToken //The valueSecurityToken
     *
     * @return array $data
     */
    public function callAPIHandshake($websiteMerchant, $getMerchantKey, $valueSecurityToken)
    {
        $resultCallHandshakeApi = false;
        $arrHandshakeParams = [
            "MerchantKey" => $getMerchantKey,
            "WebstoreUrl" => $websiteMerchant,
            "WebstoreUpsServiceLinkSecurityToken" => $valueSecurityToken,
            "WebstorePlatform" => \UPS\Shipping\Helper\Config::NAME_FLATFORM,
            "WebstorePlatformVersion" => \UPS\Shipping\Helper\Config::VERSION_FLATFORM,
            "UpsReadyPluginName" => \UPS\Shipping\Helper\Config::UPS_SHIPPING_MODULE,
            "UpsReadyPluginVersion" => \UPS\Shipping\Helper\Config::VERSION_PLUGIN,
            "WebstoreUpsServiceLinkUrl" => $websiteMerchant . \UPS\Shipping\Helper\Config::API_URL
        ];
        $response = $this->handleHandshake($arrHandshakeParams);
        // check empty response
        if (!empty($response)) {
            $response = json_decode($response);
            if (isset($response->data) && ($response->data == true)) {
                $resultCallHandshakeApi = true;
            }
        }
        return $resultCallHandshakeApi;
    }

    /**
     * Handshake function load address
     *
     * @param string $args //The args
     *
     * @return $request request load address
     */
    public function handleHandshake($args)
    {
        $request = (object)[];
        $request->WebstoreMetadata = [
            "MerchantKey" => $args['MerchantKey'],
            "WebstoreUrl" => $args['WebstoreUrl'],
            "WebstoreUpsServiceLinkSecurityToken" => $args['WebstoreUpsServiceLinkSecurityToken'],
            "WebstorePlatform" => $args['WebstorePlatform'],
            "WebstorePlatformVersion" => $args['WebstorePlatformVersion'],
            "UpsReadyPluginName" => $args['UpsReadyPluginName'],
            "UpsReadyPluginVersion" => $args['UpsReadyPluginVersion'],
        ];
        $request->VerboseResponseSecurityKey = '';
        $request->WebstoreUpsServiceLinkUrl = $args['WebstoreUpsServiceLinkUrl'];
        $this->setRequestByObject($request);
        $this->setApiUrlByNamePlugin('SecurityService/Handshake');
        return $this->doRequest()->getBody();
    }

    /**
     * Handshake function registeredPluginToken
     *
     * @param string $args //The args
     *
     * @return $request request load address
     */
    public function registeredPluginToken($args)
    {
        $request = (object)[];
        $request->WebstoreMetadata = [
            "MerchantKey" => $args['MerchantKey'],
            "WebstoreUrl" => $args['WebstoreUrl'],
            "WebstoreUpsServiceLinkSecurityToken" => $args['WebstoreUpsServiceLinkSecurityToken'],
            "WebstorePlatform" => $args['WebstorePlatform'],
            "WebstorePlatformVersion" => $args['WebstorePlatformVersion'],
            "UpsReadyPluginName" => $args['UpsReadyPluginName'],
            "UpsReadyPluginVersion" => $args['UpsReadyPluginVersion'],
        ];
        $request->UPSSecurity = [
            "UsernameToken" => [
                "Username" => $args['Username'],
                "Password" => $args['Password']
            ],
            "ServiceAccessToken" => [
                "AccessLicenseNumber" => $args['AccessLicenseNumber']
            ]
        ];
        $this->setRequestByObject($request);
        $this->setApiUrlByNamePlugin('SecurityService/RegisteredPluginToken');
        return $this->doRequestBearer()->getBody();
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
    public function generatePass($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
