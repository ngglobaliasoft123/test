<?php

/**
 * Index file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\API\Manager;

/**
 * Index file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */

class Index extends \UPS\Shipping\API\ClientAPI
{
    const TYPE_PACKAGE_DIMENSION = 2;
    /**
     * Index call Update Merchant Status
     *
     * @param string $dataUpdateMerchantStatus //The dataUpdateMerchantStatus
     * @param int    $id                       //The id
     *
     * @return null
     */
    public function callUpdateMerchantStatus($dataUpdateMerchantStatus, $id = 0)
    {
        $url = 'Merchant/UpdateMerchantStatus';
        $dataAPIFormat = [
            \UPS\Shipping\Helper\ConstantManager::MERCHANTKEY
            => $dataUpdateMerchantStatus[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY],
            \UPS\Shipping\Helper\ConstantManager::ACCOUNTNUMBER
            => $dataUpdateMerchantStatus[\UPS\Shipping\Helper\ConstantManager::ACCOUNTNUMBER],
            \UPS\Shipping\Helper\ConstantManager::STATUS
            => $dataUpdateMerchantStatus[\UPS\Shipping\Helper\ConstantManager::STATUS]
        ];
        $bearerToken = $dataUpdateMerchantStatus[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN];
        $responManager = $this->createPluginManagerAPI($dataAPIFormat, $url, $bearerToken);
        $responManagerDecode = json_decode($responManager, true);
        if (!isset($responManagerDecode['data'])
            || (isset($responManagerDecode['data']) && $responManagerDecode['data'] != '1')
        ) {
            if ($id == 0) {
                $this->saveLogManager(
                    "callUpdateMerchantStatus", json_encode($dataUpdateMerchantStatus), $responManager
                );
            } else {
                $this->updateCountRetry($id);
            }
        } else {
            if ($id != 0) {
                $this->removeRetry($id);
            }
        }
        return $responManager;
    }

    /**
     * Index call callTransferAccessorials
     *
     * @param string $dataTransferAccessioral //The dataTransferAccessioral
     * @param int    $id                      //The id
     *
     * @return null
     */
    public function callTransferAccessorials($dataTransferAccessioral, $id = 0)
    {
        $url = 'Merchant/TransferAccessorials';
        $dataAccessorial = $dataTransferAccessioral['dataAccessorial'];
        $bearerToken = $dataTransferAccessioral[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN];

        $dataAPIFormat[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY]
            = $dataTransferAccessioral[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY];
        // accessorial
        foreach ($dataAccessorial as $value) {
            $dataAPIFormat[\UPS\Shipping\Helper\ConstantManager::ACCESSORIALS][] = [
                'key' => (isset($value[\UPS\Shipping\Helper\ConstantManager::ACCESSORIAL_KEY]) ?
                $value[\UPS\Shipping\Helper\ConstantManager::ACCESSORIAL_KEY] : ''),
                'name' => (isset($value[\UPS\Shipping\Helper\ConstantManager::ACCESSORIAL_NAME]) ?
                $value[\UPS\Shipping\Helper\ConstantManager::ACCESSORIAL_NAME] : '')
            ];
        }
        $responManager = $this->createPluginManagerAPI($dataAPIFormat, $url, $bearerToken);
        $responManagerDecode = json_decode($responManager, true);
        if (!isset($responManagerDecode['data'])
            || (isset($responManagerDecode['data']) && $responManagerDecode['data'] != '1')
        ) {
            if ($id == 0) {
                $this->saveLogManager(
                    "callTransferAccessorials", json_encode($dataTransferAccessioral), $responManager
                );
            } else {
                $this->updateCountRetry($id);
            }
        } else {
            if ($id != 0) {
                $this->removeRetry($id);
            }
        }
        return $responManager;
    }

    /**
     * Index call callTransferDefaultPackage
     *
     * @param string $dataTransferDefaultPackage //The dataTransferDefaultPackage
     * @param int    $id                         //The id
     * @param int    $option                     //The option
     *
     * @return null
     */
    public function callTransferDefaultPackage($dataTransferDefaultPackage, $id = 0, $option = 1)
    {
        $url = 'Merchant/TransferDefaultPackage';

        $defaultPackages = $dataTransferDefaultPackage['dataPackageDimension'];
        $bearerToken = $dataTransferDefaultPackage[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN];

        $dataAPIFormat = [
            \UPS\Shipping\Helper\ConstantManager::MERCHANTKEY
            => (isset($defaultPackages[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY]) ?
            $defaultPackages[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY] : ''),
            'name'       => (isset($defaultPackages[\UPS\Shipping\Helper\ConstantManager::PACKAGE_NAME]) ?
            $defaultPackages[\UPS\Shipping\Helper\ConstantManager::PACKAGE_NAME] : ''),
            \UPS\Shipping\Helper\ConstantManager::WEIGHT
            => (isset($defaultPackages[\UPS\Shipping\Helper\ConstantManager::WEIGHT]) ?
            strval($defaultPackages[\UPS\Shipping\Helper\ConstantManager::WEIGHT]) : '0'),
            \UPS\Shipping\Helper\ConstantManager::WEIGHTUNIT
            => (isset($defaultPackages[\UPS\Shipping\Helper\ConstantManager::UNIT_WEIGHT]) ?
            $defaultPackages[\UPS\Shipping\Helper\ConstantManager::UNIT_WEIGHT] : 'kgs'),
            \UPS\Shipping\Helper\ConstantManager::LENGTH
            => (isset($defaultPackages[\UPS\Shipping\Helper\ConstantManager::LENGTH]) ?
            strval($defaultPackages[\UPS\Shipping\Helper\ConstantManager::LENGTH]) : '0'),
            \UPS\Shipping\Helper\ConstantManager::WIDTH
            => (isset($defaultPackages[\UPS\Shipping\Helper\ConstantManager::WIDTH]) ?
            strval($defaultPackages[\UPS\Shipping\Helper\ConstantManager::WIDTH]) : '0'),
            \UPS\Shipping\Helper\ConstantManager::HEIGHT
            => (isset($defaultPackages[\UPS\Shipping\Helper\ConstantManager::HEIGHT]) ?
            strval($defaultPackages[\UPS\Shipping\Helper\ConstantManager::HEIGHT]) : '0'),
            \UPS\Shipping\Helper\ConstantManager::DIMENSIONUNIT
            => (isset($defaultPackages[\UPS\Shipping\Helper\ConstantManager::UNIT_DIMENSION]) ?
            $defaultPackages[\UPS\Shipping\Helper\ConstantManager::UNIT_DIMENSION] : 'cm'),
            'packageItem' => (isset($defaultPackages['packageItem']) ?
            $defaultPackages['packageItem'] : '0'),
        ];

        $packageSettingType = $option;
        if ($packageSettingType == self::TYPE_PACKAGE_DIMENSION) {
            $url = 'Merchant/TransferDefaultPackageRate';
            $dataAPIFormat = [
                \UPS\Shipping\Helper\ConstantManager::MERCHANTKEY
                => (isset($defaultPackages[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY]) ?
                $defaultPackages[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY] : ''),
                'includeDimensionsInRating'       => (isset($defaultPackages['includeDimensionsInRating']) ?
                $defaultPackages['includeDimensionsInRating'] : ''),
            ];
            $dataAPIFormat['backupRate'][] = [
                'serviceKey' => (isset($defaultPackages['backupRate']['serviceKey']) ?
                $defaultPackages['backupRate']['serviceKey'] : ''),
                'rate' => (isset($defaultPackages['backupRate']['rate']) ?
                $defaultPackages['backupRate']['rate'] : '0'),
            ];
        }
        $responManager = $this->createPluginManagerAPI($dataAPIFormat, $url, $bearerToken);
        $responManagerDecode = json_decode($responManager, true);
        if (!isset($responManagerDecode['data'])
            || (isset($responManagerDecode['data']) && $responManagerDecode['data'] != '1')
        ) {
            if ($id == 0) {
                $this->saveLogManager(
                    "callTransferDefaultPackage", json_encode($dataTransferDefaultPackage), $responManager
                );
            } else {
                $this->updateCountRetry($id);
            }
        } else {
            if ($id != 0) {
                $this->removeRetry($id);
            }
        }
        return $responManager;
    }

    /**
     * Index callTransferDeliveryRates
     *
     * @param string $dataDeliveryRates //The dataDeliveryRates
     * @param int    $id                //The id
     *
     * @return null
     */
    public function callTransferDeliveryRates($dataDeliveryRates, $id = 0)
    {
        $deliveryRates = $dataDeliveryRates[\UPS\Shipping\Helper\ConstantManager::DELIVERYRATES];
        $bearerToken = $dataDeliveryRates[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN];
        $url = 'Merchant/TransferDeliveryRates';
        $dataAPIFormat[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY]
            = (isset($dataDeliveryRates[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY]) ?
        $dataDeliveryRates[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY] : '');
        foreach ($deliveryRates as $value) {
            $dataAPIFormat[\UPS\Shipping\Helper\ConstantManager::DELIVERYRATES][] = [
                'key'           => (isset($value[\UPS\Shipping\Helper\ConstantManager::SERVICE_KEY_DELIVERY]) ?
                $value[\UPS\Shipping\Helper\ConstantManager::SERVICE_KEY_DELIVERY] : ''),
                'deliveryType'  => (isset($value[\UPS\Shipping\Helper\ConstantManager::RATE_TYPE]) ?
                $value[\UPS\Shipping\Helper\ConstantManager::RATE_TYPE] : ''),
                \UPS\Shipping\Helper\ConstantManager::SERVICETYPE
                => (isset($value[\UPS\Shipping\Helper\ConstantManager::SERVICE_TYPE]) ?
                $value[\UPS\Shipping\Helper\ConstantManager::SERVICE_TYPE] : ''),
                \UPS\Shipping\Helper\ConstantManager::SERVICENAME
                => (isset($value[\UPS\Shipping\Helper\ConstantManager::SERVICE_NAME]) ?
                $value[\UPS\Shipping\Helper\ConstantManager::SERVICE_NAME] : ''),
                \UPS\Shipping\Helper\ConstantManager::SERVICECODE
                => (isset($value[\UPS\Shipping\Helper\ConstantManager::RATE_CODE]) ?
                $value[\UPS\Shipping\Helper\ConstantManager::RATE_CODE] : ''),
                'minimumOrderValue' => (isset($value[\UPS\Shipping\Helper\ConstantManager::MIN_ORDER_VALUE]) ?
                $value[\UPS\Shipping\Helper\ConstantManager::MIN_ORDER_VALUE] : 0),
                'deliveryValue' => (isset($value[\UPS\Shipping\Helper\ConstantManager::DELIVERY_RATE]) ?
                $value[\UPS\Shipping\Helper\ConstantManager::DELIVERY_RATE] : 0),
                \UPS\Shipping\Helper\ConstantManager::REALTIMEVALUE
                => (isset($value[\UPS\Shipping\Helper\ConstantManager::REALTIMEVALUE]) ?
                $value[\UPS\Shipping\Helper\ConstantManager::REALTIMEVALUE] : 0)
            ];
        }
        $responManager = $this->createPluginManagerAPI($dataAPIFormat, $url, $bearerToken);
        $responManagerDecode = json_decode($responManager, true);
        if (!isset($responManagerDecode['data'])
            || (isset($responManagerDecode['data']) && $responManagerDecode['data'] != '1')
        ) {
            if ($id == 0) {
                $this->saveLogManager("callTransferDeliveryRates", json_encode($dataDeliveryRates), $responManager);
            } else {
                $this->updateCountRetry($id);
            }
        } else {
            if ($id != 0) {
                $this->removeRetry($id);
            }
        }
        return $responManager;
    }

    /**
     * Index callTransferMerchantInfo
     *
     * @param string $dataMerchantInfo //The dataMerchantInfo
     * @param int    $id               //The id
     *
     * @return null
     */
    public function callTransferMerchantInfo($dataMerchantInfo, $id = 0)
    {
        if (!empty($dataMerchantInfo) && isset($dataMerchantInfo['accountNumberInfo'])) {
            $accountNumberInfo = $dataMerchantInfo['accountNumberInfo'];
            $defaultPackages = $dataMerchantInfo['defaultPackages'];
            $accessorials = $dataMerchantInfo[\UPS\Shipping\Helper\ConstantManager::ACCESSORIALS];
            $shippingServices = $dataMerchantInfo[\UPS\Shipping\Helper\ConstantManager::SHIPPINGSERVICES];
            $deliveryRates = $dataMerchantInfo[\UPS\Shipping\Helper\ConstantManager::DELIVERYRATES];
            $bearerToken = $dataMerchantInfo[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN];

            $url = 'Merchant/TransferMerchantInfo';
            $notArray = true;
            if (isset($accountNumberInfo[0]) && is_array($accountNumberInfo[0])) { // case many account
                $notArray = false;
                foreach ($accountNumberInfo as $item) {
                    $dataAPIFormat[] = [
                        \UPS\Shipping\Helper\ConstantManager::MERCHANTKEY
                        => (isset($item[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY]) ?
                        $item[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY] : ''),
                        \UPS\Shipping\Helper\ConstantManager::ACCOUNTNUMBER
                        => (isset($item[\UPS\Shipping\Helper\ConstantManager::ACCOUNTNUMBER]) ?
                        $item[\UPS\Shipping\Helper\ConstantManager::ACCOUNTNUMBER] : ''),
                        'joiningDate'   => date(\UPS\Shipping\Helper\ConstantManager::MDY),
                        \UPS\Shipping\Helper\ConstantManager::WEBSITE
                        => (isset($item[\UPS\Shipping\Helper\ConstantManager::WEBSITE]) ?
                        $item[\UPS\Shipping\Helper\ConstantManager::WEBSITE] : ''),
                        \UPS\Shipping\Helper\ConstantManager::CURRENCYCODE
                        => (isset($item[\UPS\Shipping\Helper\ConstantManager::CURRENCYCODE]) ?
                        $item[\UPS\Shipping\Helper\ConstantManager::CURRENCYCODE] : ''),
                        \UPS\Shipping\Helper\ConstantManager::STATUS
                        => (isset($item[\UPS\Shipping\Helper\ConstantManager::STATUS]) ?
                        $item[\UPS\Shipping\Helper\ConstantManager::STATUS] : ''),
                        \UPS\Shipping\Helper\ConstantManager::PLATFORM
                        => (isset($item[\UPS\Shipping\Helper\ConstantManager::PLATFORM]) ?
                        $item[\UPS\Shipping\Helper\ConstantManager::PLATFORM] : ''),
                        \UPS\Shipping\Helper\ConstantManager::VERSION
                        => (isset($item[\UPS\Shipping\Helper\ConstantManager::VERSION]) ?
                        $item[\UPS\Shipping\Helper\ConstantManager::VERSION] : ''),
                        \UPS\Shipping\Helper\ConstantManager::POSTALCODE
                        => (isset($item[\UPS\Shipping\Helper\ConstantManager::POSTALCODE]) ?
                        $item[\UPS\Shipping\Helper\ConstantManager::POSTALCODE]: ''),
                        \UPS\Shipping\Helper\ConstantManager::CITY
                        => (isset($item[\UPS\Shipping\Helper\ConstantManager::CITY]) ?
                        $item[\UPS\Shipping\Helper\ConstantManager::CITY] : ''),
                        \UPS\Shipping\Helper\ConstantManager::COUNTRY
                        => (isset($item[\UPS\Shipping\Helper\ConstantManager::COUNTRY]) ?
                        $item[\UPS\Shipping\Helper\ConstantManager::COUNTRY] : ''),
                        \UPS\Shipping\Helper\ConstantManager::ISFIRSTACCOUNT
                        => (isset($item[\UPS\Shipping\Helper\ConstantManager::ISFIRSTACCOUNT])
                        && $item[\UPS\Shipping\Helper\ConstantManager::ISFIRSTACCOUNT] == 1) ? true : false
                    ];
                    $dataAPIFormat['packageDimension'][] = [
                        'option' => (isset($defaultPackages['option']) ? $defaultPackages['option'] : ''),
                        'name' => (isset($item[\UPS\Shipping\Helper\ConstantManager::PACKAGE_NAME]) ?
                        $item[\UPS\Shipping\Helper\ConstantManager::PACKAGE_NAME] : ''),
                        \UPS\Shipping\Helper\ConstantManager::WEIGHT
                        => (isset($item[\UPS\Shipping\Helper\ConstantManager::WEIGHT]) ?
                        strval($item[\UPS\Shipping\Helper\ConstantManager::WEIGHT]) : '0'),
                        \UPS\Shipping\Helper\ConstantManager::WEIGHTUNIT
                        => (isset($item[\UPS\Shipping\Helper\ConstantManager::UNIT_WEIGHT]) ?
                        $item[\UPS\Shipping\Helper\ConstantManager::UNIT_WEIGHT] : 'kgs'),
                        \UPS\Shipping\Helper\ConstantManager::LENGTH
                        => (isset($item[\UPS\Shipping\Helper\ConstantManager::LENGTH]) ?
                        strval($item[\UPS\Shipping\Helper\ConstantManager::LENGTH]) : '0'),
                        \UPS\Shipping\Helper\ConstantManager::WIDTH
                        => (isset($item[\UPS\Shipping\Helper\ConstantManager::WIDTH]) ?
                        strval($item[\UPS\Shipping\Helper\ConstantManager::WIDTH]) : '0'),
                        \UPS\Shipping\Helper\ConstantManager::HEIGHT
                        => (isset($item[\UPS\Shipping\Helper\ConstantManager::HEIGHT]) ?
                        strval($item[\UPS\Shipping\Helper\ConstantManager::HEIGHT]) : '0'),
                        \UPS\Shipping\Helper\ConstantManager::DIMENSIONUNIT
                        => (isset($item[\UPS\Shipping\Helper\ConstantManager::UNIT_DIMENSION]) ?
                        $item[\UPS\Shipping\Helper\ConstantManager::UNIT_DIMENSION] : 'cm'),
                        'packageItem' => (isset($defaultPackages['packageItem']) ? $defaultPackages['packageItem'] : '0'),
                        'includeDimensionsInRating' => (isset($defaultPackages['includeDimensionsInRating']) ? $defaultPackages['includeDimensionsInRating'] : ''),
                        'serviceKey' => (isset($defaultPackages['serviceKey']) ? $defaultPackages['serviceKey'] : ''),
                        'rate' => (isset($defaultPackages['rate']) ? $defaultPackages['rate'] : '0'),
                    ];
                }
            } else {
                // accessorial
                $arrAccessorials = [];
                if (!empty($accessorials)) {
                    foreach ($accessorials as $key => $value) {
                        $arrAccessorials[] = [
                            'key' => (isset($value[\UPS\Shipping\Helper\ConstantManager::ACCESSORIAL_KEY]) ?
                            $value[\UPS\Shipping\Helper\ConstantManager::ACCESSORIAL_KEY] : ''),
                            'name' => (isset($value[\UPS\Shipping\Helper\ConstantManager::ACCESSORIAL_NAME]) ?
                            $value[\UPS\Shipping\Helper\ConstantManager::ACCESSORIAL_NAME] : '')
                        ];
                    }
                }
                // shippingServices
                $arrShippingServices = [];
                if (!empty($shippingServices)) {
                    foreach ($shippingServices as $key => $value) {
                        $arrShippingServices[] = [
                            'key'           => (isset($value[\UPS\Shipping\Helper\ConstantManager::SERVICE_KEY_DELIVERY])
                            ? $value[\UPS\Shipping\Helper\ConstantManager::SERVICE_KEY_DELIVERY] : ''),
                            \UPS\Shipping\Helper\ConstantManager::SERVICETYPE
                            => (isset($value[\UPS\Shipping\Helper\ConstantManager::SERVICE_TYPE]) ?
                            $value[\UPS\Shipping\Helper\ConstantManager::SERVICE_TYPE] : ''),
                            'name'          => (isset($value[\UPS\Shipping\Helper\ConstantManager::SERVICE_NAME]) ?
                            $value[\UPS\Shipping\Helper\ConstantManager::SERVICE_NAME] : ''),
                            'code'          => (isset($value[\UPS\Shipping\Helper\ConstantManager::RATE_CODE]) ?
                            $value[\UPS\Shipping\Helper\ConstantManager::RATE_CODE] : '')
                        ];
                    }
                }
                // deliveryRates
                $arrDeliveryRates = [];
                if (!empty($deliveryRates)) {
                    foreach ($deliveryRates as $key => $value) {
                        $arrDeliveryRates[] = [
                            'key'           => (isset($value[\UPS\Shipping\Helper\ConstantManager::SERVICE_KEY_DELIVERY]) ?
                            $value[\UPS\Shipping\Helper\ConstantManager::SERVICE_KEY_DELIVERY] : ''),
                            'deliveryType'  => (isset($value[\UPS\Shipping\Helper\ConstantManager::RATE_TYPE]) ?
                            $value[\UPS\Shipping\Helper\ConstantManager::RATE_TYPE] : ''),
                            \UPS\Shipping\Helper\ConstantManager::SERVICETYPE
                            => (isset($value[\UPS\Shipping\Helper\ConstantManager::SERVICE_TYPE]) ?
                            $value[\UPS\Shipping\Helper\ConstantManager::SERVICE_TYPE] : ''),
                            \UPS\Shipping\Helper\ConstantManager::SERVICENAME
                            => (isset($value[\UPS\Shipping\Helper\ConstantManager::SERVICE_NAME]) ?
                            $value[\UPS\Shipping\Helper\ConstantManager::SERVICE_NAME] : ''),
                            \UPS\Shipping\Helper\ConstantManager::SERVICECODE
                            => (isset($value[\UPS\Shipping\Helper\ConstantManager::RATE_CODE]) ?
                            $value[\UPS\Shipping\Helper\ConstantManager::RATE_CODE] : ''),
                            'minimumOrderValue' => (isset($value[\UPS\Shipping\Helper\ConstantManager::MIN_ORDER_VALUE]) ?
                            $value[\UPS\Shipping\Helper\ConstantManager::MIN_ORDER_VALUE] : 0),
                            'deliveryValue' => (isset($value[\UPS\Shipping\Helper\ConstantManager::DELIVERY_RATE]) ?
                            $value[\UPS\Shipping\Helper\ConstantManager::DELIVERY_RATE] : 0),
                            \UPS\Shipping\Helper\ConstantManager::REALTIMEVALUE
                            => (isset($value[\UPS\Shipping\Helper\ConstantManager::REALTIMEVALUE]) ?
                            $value[\UPS\Shipping\Helper\ConstantManager::REALTIMEVALUE] : 0)
                        ];
                    }
                }
                $dataAPIFormat = [
                    \UPS\Shipping\Helper\ConstantManager::MERCHANTKEY
                    => (isset($accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY]) ?
                    $accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY] : ''),
                    \UPS\Shipping\Helper\ConstantManager::ACCOUNTNUMBER
                    => (isset($accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::ACCOUNTNUMBER]) ?
                    $accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::ACCOUNTNUMBER] : ''),
                    'joiningDate'   => date(\UPS\Shipping\Helper\ConstantManager::MDY),
                    \UPS\Shipping\Helper\ConstantManager::WEBSITE
                    => (isset($accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::WEBSITE]) ?
                    $accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::WEBSITE] : ''),
                    \UPS\Shipping\Helper\ConstantManager::CURRENCYCODE
                    => (isset($accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::CURRENCYCODE]) ?
                    $accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::CURRENCYCODE] : ''),
                    \UPS\Shipping\Helper\ConstantManager::STATUS
                    => (isset($accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::STATUS]) ?
                    $accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::STATUS] : ''),
                    \UPS\Shipping\Helper\ConstantManager::PLATFORM
                    => (isset($accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::PLATFORM]) ?
                    $accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::PLATFORM] : ''),
                    \UPS\Shipping\Helper\ConstantManager::VERSION
                    => (isset($accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::VERSION]) ?
                    $accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::VERSION] : ''),
                    \UPS\Shipping\Helper\ConstantManager::POSTALCODE
                    => (isset($accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::POSTALCODE]) ?
                    $accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::POSTALCODE]: ''),
                    \UPS\Shipping\Helper\ConstantManager::CITY
                    => (isset($accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::CITY]) ?
                    $accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::CITY] : ''),
                    \UPS\Shipping\Helper\ConstantManager::COUNTRY
                    => (isset($accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::COUNTRY]) ?
                    $accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::COUNTRY] : ''),

                    \UPS\Shipping\Helper\ConstantManager::ISFIRSTACCOUNT
                    => (isset($accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::ISFIRSTACCOUNT])
                    && $accountNumberInfo[\UPS\Shipping\Helper\ConstantManager::ISFIRSTACCOUNT] == 1) ? true : false
                ];
                if (empty($defaultPackages['weight'])) {
                    $defaultPackages['weight'] = '0';
                }
                if (empty($defaultPackages['unit_weight'])) {
                    $defaultPackages['unit_weight'] = 'kgs';
                }
                if (empty($defaultPackages['length'])) {
                    $defaultPackages['length'] = '0';
                }
                if (empty($defaultPackages['width'])) {
                    $defaultPackages['width'] = '0';
                }
                if (empty($defaultPackages['height'])) {
                    $defaultPackages['height'] = '0';
                }
                if (empty($defaultPackages['unit_dimension'])) {
                    $defaultPackages['unit_dimension'] = 'cm';
                }
                if (empty($defaultPackages['packageItem'])) {
                    $defaultPackages['packageItem'] = '0';
                }
                $dataAPIFormat['packageDimension'][] = [
                    'option' => (isset($defaultPackages['option']) ? $defaultPackages['option'] : ''),
                    'name' => (isset($defaultPackages[\UPS\Shipping\Helper\ConstantManager::PACKAGE_NAME]) ?
                    $defaultPackages[\UPS\Shipping\Helper\ConstantManager::PACKAGE_NAME] : ''),
                    \UPS\Shipping\Helper\ConstantManager::WEIGHT
                    => (isset($defaultPackages[\UPS\Shipping\Helper\ConstantManager::WEIGHT]) ?
                    strval($defaultPackages[\UPS\Shipping\Helper\ConstantManager::WEIGHT]) : '0'),
                    \UPS\Shipping\Helper\ConstantManager::WEIGHTUNIT
                    => (isset($defaultPackages[\UPS\Shipping\Helper\ConstantManager::UNIT_WEIGHT]) ?
                    $defaultPackages[\UPS\Shipping\Helper\ConstantManager::UNIT_WEIGHT] : 'kgs'),
                    \UPS\Shipping\Helper\ConstantManager::LENGTH
                    => (isset($defaultPackages[\UPS\Shipping\Helper\ConstantManager::LENGTH]) ?
                    strval($defaultPackages[\UPS\Shipping\Helper\ConstantManager::LENGTH]) : '0'),
                    \UPS\Shipping\Helper\ConstantManager::WIDTH
                    => (isset($defaultPackages[\UPS\Shipping\Helper\ConstantManager::WIDTH]) ?
                    strval($defaultPackages[\UPS\Shipping\Helper\ConstantManager::WIDTH]) : '0'),
                    \UPS\Shipping\Helper\ConstantManager::HEIGHT
                    => (isset($defaultPackages[\UPS\Shipping\Helper\ConstantManager::HEIGHT]) ?
                    strval($defaultPackages[\UPS\Shipping\Helper\ConstantManager::HEIGHT]) : '0'),
                    \UPS\Shipping\Helper\ConstantManager::DIMENSIONUNIT
                    => (isset($defaultPackages[\UPS\Shipping\Helper\ConstantManager::UNIT_DIMENSION]) ?
                    $defaultPackages[\UPS\Shipping\Helper\ConstantManager::UNIT_DIMENSION] : 'cm'),
                    'packageItem' => (isset($defaultPackages['packageItem']) ? $defaultPackages['packageItem'] : '0'),
                    'includeDimensionsInRating' => (isset($defaultPackages['includeDimensionsInRating']) ? $defaultPackages['includeDimensionsInRating'] : ''),
                    'serviceKey' => (isset($defaultPackages['serviceKey']) ? $defaultPackages['serviceKey'] : ''),
                    'rate' => (isset($defaultPackages['rate']) ? $defaultPackages['rate'] : '0'),
                ];
                $this->setDataAPIFormat($dataAPIFormat, $arrAccessorials, $arrShippingServices, $arrDeliveryRates);
            }
            $responManager = $this->createPluginManagerAPI($dataAPIFormat, $url, $bearerToken, $notArray);
            return $this->getResponManagerDecode($responManager, $dataMerchantInfo, $id);
        }
    }

    /**
     * Index getResponManagerDecode
     *
     * @param string $dataAPIFormat       //The dataAPIFormat
     * @param string $arrAccessorials     //The arrAccessorials
     * @param int    $arrShippingServices //The arrShippingServices
     * @param int    $arrDeliveryRates    //The arrDeliveryRates
     *
     * @return null
     */
    public function setDataAPIFormat(&$dataAPIFormat, $arrAccessorials, $arrShippingServices, $arrDeliveryRates)
    {
        if (!empty($arrAccessorials)) {
            $dataAPIFormat[\UPS\Shipping\Helper\ConstantManager::ACCESSORIALS] = $arrAccessorials;
        }
        if (!empty($arrShippingServices)) {
            $dataAPIFormat[\UPS\Shipping\Helper\ConstantManager::SHIPPINGSERVICES] = $arrShippingServices;
        }
        if (!empty($arrDeliveryRates)) {
            $dataAPIFormat[\UPS\Shipping\Helper\ConstantManager::DELIVERYRATES] = $arrDeliveryRates;
        }
    }

    /**
     * Index getResponManagerDecode
     *
     * @param string $responManager    //The responManager
     * @param string $dataMerchantInfo //The dataMerchantInfo
     * @param int    $id               //The id
     *
     * @return null
     */
    public function getResponManagerDecode($responManager, $dataMerchantInfo, $id)
    {
        $responManagerDecode = json_decode($responManager, true);
        if (!isset($responManagerDecode['data'])
            || (isset($responManagerDecode['data']) && $responManagerDecode['data'] != '1')
        ) {
            if ($id == 0) {
                $this->saveLogManager("callTransferMerchantInfo", json_encode($dataMerchantInfo), $responManager);
            } else {
                $this->updateCountRetry($id);
            }
        } else {
            if ($id != 0) {
                $this->removeRetry($id);
            }
        }
        return $responManager;
    }

    /**
     * Index callTransferShippingServices
     *
     * @param string $dataTransferShippingServices //The dataTransferShippingServices
     * @param int    $id                           //The id
     *
     * @return null
     */
    public function callTransferShippingServices($dataTransferShippingServices, $id = 0)
    {
        $dataShippingServices = $dataTransferShippingServices['dataShippingServices'];
        $bearerToken = $dataTransferShippingServices[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN];
        $url = 'Merchant/TransferShippingServices';
        $dataAPIFormat[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY]
            = $dataTransferShippingServices[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY];
        // shipping Services
        foreach ($dataShippingServices as $value) {
            $dataAPIFormat[\UPS\Shipping\Helper\ConstantManager::SHIPPINGSERVICES][] = [
                'key' => (isset($value['service_key']) ? $value['service_key'] : ''),
                \UPS\Shipping\Helper\ConstantManager::SERVICETYPE
                => (isset($value[\UPS\Shipping\Helper\ConstantManager::SERVICE_TYPE]) ?
                $value[\UPS\Shipping\Helper\ConstantManager::SERVICE_TYPE] : ''),
                'code' => (isset($value[\UPS\Shipping\Helper\ConstantManager::RATE_CODE]) ?
                $value[\UPS\Shipping\Helper\ConstantManager::RATE_CODE] : ''),
                'name' => (isset($value[\UPS\Shipping\Helper\ConstantManager::SERVICE_NAME]) ?
                $value[\UPS\Shipping\Helper\ConstantManager::SERVICE_NAME] : '')
            ];
        }
        $responManager = $this->createPluginManagerAPI($dataAPIFormat, $url, $bearerToken);
        $responManagerDecode = json_decode($responManager, true);
        if (!isset($responManagerDecode['data'])
            || (isset($responManagerDecode['data']) && $responManagerDecode['data'] != '1')
        ) {
            if ($id == 0) {
                $dataTransfer = json_encode($dataTransferShippingServices);
                $this->saveLogManager("callTransferShippingServices", $dataTransfer, $responManager);
            } else {
                $this->updateCountRetry($id);
            }
        } else {
            if ($id != 0) {
                $this->removeRetry($id);
            }
        }
        return $responManager;
    }

    /**
     * Index callTransferShipments
     *
     * @param string $dataShipments //The dataShipments
     * @param int    $id            //The id
     *
     * @return null
     */
    public function callTransferShipments($dataShipments, $id = 0)
    {
        $url = 'Shipment/TransferShipments';
        $bearerToken = $dataShipments[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN];
        $dataShipment = $dataShipments['shipment'];
        $accessorials = $dataShipments[\UPS\Shipping\Helper\ConstantManager::ACCESSORIALS];
        $packages = $dataShipments['packages'];
        if (isset($dataShipment[\UPS\Shipping\Helper\ConstantManager::SERVICETYPE])) {
            if ($dataShipment[\UPS\Shipping\Helper\ConstantManager::SERVICETYPE] == "AP") {
                $serviceType = 10;
            } else {
                $serviceType = 20;
            }
        } else {
            $serviceType = "";
        }
        $dataShipmentProduct = $dataShipment[\UPS\Shipping\Helper\ConstantManager::PRODUCTS];
        $dataAPIFormat = [
            \UPS\Shipping\Helper\ConstantManager::MERCHANTKEY
            => (isset($dataShipment[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY]) ?
            $dataShipment[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY] : ''),
            \UPS\Shipping\Helper\ConstantManager::ACCOUNTNUMBER
            => (isset($dataShipment[\UPS\Shipping\Helper\ConstantManager::ACCOUNTNUMBER]) ?
            $dataShipment[\UPS\Shipping\Helper\ConstantManager::ACCOUNTNUMBER] : ''),
            \UPS\Shipping\Helper\ConstantManager::SHIPMENTID
            => (isset($dataShipment[\UPS\Shipping\Helper\ConstantManager::SHIPMENTID]) ?
            $dataShipment[\UPS\Shipping\Helper\ConstantManager::SHIPMENTID] : ''),
            'fee'               => (isset($dataShipment['fee']) ? strval($dataShipment['fee']) : ''),
            'revenue'           => (isset($dataShipment['fee']) ? strval($dataShipment['fee']) : ''),
            // 'revenue'           => (isset($dataShipment['revenue']) ? (float)$dataShipment['revenue'] : ''),
            'orderDate'         => date(\UPS\Shipping\Helper\ConstantManager::MDY),
            \UPS\Shipping\Helper\ConstantManager::ADDRESS
            => (isset($dataShipment[\UPS\Shipping\Helper\ConstantManager::ADDRESS]) ?
            $dataShipment[\UPS\Shipping\Helper\ConstantManager::ADDRESS] : ''),
            \UPS\Shipping\Helper\ConstantManager::POSTALCODE
            => (isset($dataShipment[\UPS\Shipping\Helper\ConstantManager::POSTALCODE]) ?
            $dataShipment[\UPS\Shipping\Helper\ConstantManager::POSTALCODE] : ''),
            \UPS\Shipping\Helper\ConstantManager::CITY
            => (isset($dataShipment[\UPS\Shipping\Helper\ConstantManager::CITY]) ?
            $dataShipment[\UPS\Shipping\Helper\ConstantManager::CITY] : ''),
            \UPS\Shipping\Helper\ConstantManager::COUNTRY
            => (isset($dataShipment[\UPS\Shipping\Helper\ConstantManager::COUNTRY]) ?
            $dataShipment[\UPS\Shipping\Helper\ConstantManager::COUNTRY] : ''),
            \UPS\Shipping\Helper\ConstantManager::SERVICETYPE       => $serviceType,
            \UPS\Shipping\Helper\ConstantManager::SERVICECODE
            => (isset($dataShipment[\UPS\Shipping\Helper\ConstantManager::SERVICECODE]) ?
            $dataShipment[\UPS\Shipping\Helper\ConstantManager::SERVICECODE] : ''),
            \UPS\Shipping\Helper\ConstantManager::SERVICENAME
            => (isset($dataShipment[\UPS\Shipping\Helper\ConstantManager::SERVICENAME]) ?
            $dataShipment[\UPS\Shipping\Helper\ConstantManager::SERVICENAME] : ''),
            \UPS\Shipping\Helper\ConstantManager::ISCASHONDELIVERY
            => (isset($dataShipment[\UPS\Shipping\Helper\ConstantManager::ISCASHONDELIVERY]) ?
            (integer)$dataShipment[\UPS\Shipping\Helper\ConstantManager::ISCASHONDELIVERY] : 0),
            \UPS\Shipping\Helper\ConstantManager::PRODUCTS
            => (isset($dataShipment[\UPS\Shipping\Helper\ConstantManager::PRODUCTS]) ?
            str_replace(['™','&trade'], ['\u2122','\u2122'], $dataShipmentProduct) : ''),
        ];
        foreach ($accessorials as $key => $value) {
            $dataAPIFormat[\UPS\Shipping\Helper\ConstantManager::ACCESSORIALS][] = [
                'name' => $value
            ];
        }
        foreach ($packages as $key => $value) {
            $dataAPIFormat['packages'][] = [
                \UPS\Shipping\Helper\ConstantManager::TRACKINGNUMBER => $value['trackingnumber'],
                \UPS\Shipping\Helper\ConstantManager::SHIPMENTSTATUS
                => \UPS\Shipping\Helper\Config::CREATE_SHIPMENT_STATUS,
                \UPS\Shipping\Helper\ConstantManager::WEIGHT
                => strval($value[\UPS\Shipping\Helper\ConstantManager::WEIGHT]),
                \UPS\Shipping\Helper\ConstantManager::WEIGHTUNIT
                => $value[\UPS\Shipping\Helper\ConstantManager::UNIT_WEIGHT],
                \UPS\Shipping\Helper\ConstantManager::LENGTH
                => strval($value[\UPS\Shipping\Helper\ConstantManager::LENGTH]),
                \UPS\Shipping\Helper\ConstantManager::WIDTH
                => strval($value[\UPS\Shipping\Helper\ConstantManager::WIDTH]),
                \UPS\Shipping\Helper\ConstantManager::HEIGHT
                => strval($value[\UPS\Shipping\Helper\ConstantManager::HEIGHT]),
                \UPS\Shipping\Helper\ConstantManager::DIMENSIONUNIT
                => $value[\UPS\Shipping\Helper\ConstantManager::UNIT_DIMENSION],
            ];
        }
        $responManager = $this->createPluginManagerAPI($dataAPIFormat, $url, $bearerToken);
        $responManagerDecode = json_decode($responManager, true);
        if (!isset($responManagerDecode['data'])
            || (isset($responManagerDecode['data']) && $responManagerDecode['data'] != '1')
        ) {
            if ($id == 0) {
                $this->saveLogManager("callTransferShipments", json_encode($dataShipments), $responManager);
            } else {
                $this->updateCountRetry($id);
            }
        } else {
            if ($id != 0) {
                $this->removeRetry($id);
            }
        }
        return $responManager;
    }

    /**
     * Index callUpdateShipmentsStatus
     *
     * @param string  $datashipments //The datashipments
     * @param integer $id            //The id
     *
     * @return null
     */
    public function callUpdateShipmentsStatus($datashipments, $id = 0)
    {
        // status truyền tham số api tracking vào
        $url = 'Shipment/UpdateShipmentStatus';
        $bearerToken = $datashipments[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN];
        $dataAPIFormat = [];
        foreach ($datashipments['shipment'] as $key => $value) {
            $dataAPIFormat[] = [
                \UPS\Shipping\Helper\ConstantManager::TRACKINGNUMBER
                => $value[\UPS\Shipping\Helper\ConstantManager::TRACKINGNUMBER],
                \UPS\Shipping\Helper\ConstantManager::SHIPMENTSTATUS
                => (string)$value[\UPS\Shipping\Helper\ConstantManager::SHIPMENTSTATUS]
            ];
        }
        $responManager = $this->createPluginManagerAPI($dataAPIFormat, $url, $bearerToken);
        $responManagerDecode = json_decode($responManager, true);
        if (!isset($responManagerDecode['data'])
            || (isset($responManagerDecode['data']) && $responManagerDecode['data'] != '1')
        ) {
            if ($id == 0) {
                $this->saveLogManager("UpdateShipmentStatus", json_encode($datashipments), $responManager);
            } else {
                $this->updateCountRetry($id);
            }
        } else {
            if ($id != 0) {
                $this->removeRetry($id);
            }
        }
        return $responManager;
    }

    /**
     * Index callUpgradePluginVersion
     *
     * @param string $data //The data
     * @param int    $id   //The id
     *
     * @return null
     */
    public function callUpgradePluginVersion($data, $id = 0)
    {
        $url = 'Merchant/UpgradePluginVersion';
        $bearerToken = $data[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN];
        $dataAPIFormat = [
            \UPS\Shipping\Helper\ConstantManager::MERCHANTKEY
            => $data[\UPS\Shipping\Helper\ConstantManager::MERCHANTKEY],
            \UPS\Shipping\Helper\ConstantManager::VERSION => \UPS\Shipping\Helper\Config::VERSION_PLUGIN
        ];
        $responManager = $this->createPluginManagerAPI($dataAPIFormat, $url, $bearerToken);
        $responManagerDecode = json_decode($responManager, true);
        if (!isset($responManagerDecode['data']) || (isset($responManagerDecode['data']) && $responManagerDecode['data'] != '1')) {
            if ($id == 0) {
                $this->saveLogManager("callUpgradePluginVersion", json_encode($data), $responManager);
            } else {
                $this->updateCountRetry($id);
            }
        } else {
            if ($id != 0) {
                $this->removeRetry($id);
            }
        }
        return $responManager;
    }

    /**
     * Index createPluginManagerAPI
     *
     * @param string $dataApi     //The dataApi
     * @param string $url         //The url
     * @param string $bearerToken //The bearerToken
     * @param string $notArray    //The notArray
     *
     * @return $requestApi
     */
    public function createPluginManagerAPI($dataApi, $url, $bearerToken='', $notArray = true)
    {
        if ($url != 'Merchant/VerifyMerchant' && $url != 'Shipment/UpdateShipmentStatus' && $notArray) {
            $dataApi = [$dataApi];
        }
        $this->bearerToken = $bearerToken;
        $this->setRequest(json_encode($dataApi));
        $this->setApiUrlByPluginManager($url);
        return $this->doRequestPluginManager()->getBody();
    }

    /**
     * Index createCommonAPI
     *
     * @param string $dataApi     //The dataApi
     * @param string $url         //The url
     * @param string $bearerToken //The bearerToken
     * @param string $notArray    //The notArray
     *
     * @return $requestApi
     */
    public function createCommonAPI($dataApi, $url, $bearerToken='', $notArray = true)
    {
        $this->setApiUrlByName($url);
        if (!empty($bearerToken)) {
            $this->setToken($bearerToken);
        }

        if ($url != 'Merchant/VerifyMerchant' && $url != 'Shipment/UpdateShipmentStatus' && $notArray) {
            $dataApi = [$dataApi];
        }

        $this->setRequest(json_encode($dataApi));
        return $this->doRequestManager();
    }
}
