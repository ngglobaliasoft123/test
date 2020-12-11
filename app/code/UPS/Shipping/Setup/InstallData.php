<?php
/**
 * InstallData file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
/**
 * InstallData class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class InstallData implements InstallDataInterface
{
    const VAL_DEFAULT = 'default';
    const SCOPE = 'scope';
    const SCOPE_ID = 'scope_id';
    const VALUE = 'value';
    const STATUS = 'status';
    const LABEL = 'label';
    const ACCESSORIAL_NAME = 'accessorial_name';
    const ACCESSORIAL_KEY = 'accessorial_key';
    const SHOW_SHIPPING = 'show_shipping';
    const SHOW_CONFIG = 'show_config';
    const ACCESSORIAL_CODE = 'accessorial_code';
    const SERVICE_KEY_DELIVERY = 'service_key_delivery';
    const SERVICE_KEY = 'service_key';
    const SERVICE_TYPE = 'service_type';
    const SERVICE_KEY_VAL = 'service_key_val';
    const COUNTRY_CODE = 'country_code';
    const RATE_CODE = 'rate_code';
    const SERVICE_NAME = 'service_name';
    const TIN_T_CODE = 'tin_t_code';
    const SERVICE_SYMBOL = 'service_symbol';
    const SERVICE_SELECTED = 'service_selected';
    const UPS_STANDARD = 'UPS Standard';
    const SYMBOL_REG = '&reg;';
    const UPS_EXPEDITED = 'UPS Expedited';
    const UPS_EXPRESS_SAVER = 'UPS Express Saver';
    const UPS_EXPRESS = 'UPS Express';
    const UPS_EXPRESS12 = 'UPS Express 12:00';
    const UPS_EXPRESS_PLUS = 'UPS Express Plus';
    const TIME_HI_AND_VERSION = 'time_hi_and_version';
    const CLOCK_SEQ = 'clock_seq';

    /**
     * InstallData install
     * install shipping plugin
     *
     * @param string $setup   //The setup
     * @param string $context //The context
     *
     * @return array $data
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $merchantKey = $this->generateMerchantKey();
        $dataConfigs = [
            [ self::SCOPE => self::VAL_DEFAULT, self::SCOPE_ID => 0,
            'path' => \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_TERM_CONDITION, self::VALUE => '0'],
            [ self::SCOPE => self::VAL_DEFAULT, self::SCOPE_ID => 0,
            'path' => \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_SHOW_TERM_CONDITION, self::VALUE => '1'],
            [ self::SCOPE => self::VAL_DEFAULT, self::SCOPE_ID => 0,
            'path' => \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_ACCOUNT, self::VALUE => '0'],
            [ self::SCOPE => self::VAL_DEFAULT, self::SCOPE_ID => 0,
            'path' => \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_SHIPPING_SERVICE, self::VALUE => '0'],
            [ self::SCOPE => self::VAL_DEFAULT, self::SCOPE_ID => 0,
            'path' => \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_CASH_ON_DELIVERY, self::VALUE => '0'],
            [ self::SCOPE => self::VAL_DEFAULT, self::SCOPE_ID => 0,
            'path' => \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_ACCESSORIAL, self::VALUE => '0'],
            [ self::SCOPE => self::VAL_DEFAULT, self::SCOPE_ID => 0,
            'path' => \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_PACKAGE_DIMENSIONS, self::VALUE => '0'],
            [ self::SCOPE => self::VAL_DEFAULT, self::SCOPE_ID => 0,
            'path' => \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_DELIVERY_RATES, self::VALUE => '0'],
            [ self::SCOPE => self::VAL_DEFAULT, self::SCOPE_ID => 0,
            'path' => \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_BILLING_PREFERENCE, self::VALUE => '0'],
            [ self::SCOPE => self::VAL_DEFAULT, self::SCOPE_ID => 0,
            'path' => \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE, self::VALUE => 'PL'],
            [ self::SCOPE => self::VAL_DEFAULT, self::SCOPE_ID => 0,
            'path' => \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_SET_DEFAULT, self::VALUE => '1'],
            // config keys
            [ self::SCOPE => self::VAL_DEFAULT, self::SCOPE_ID => 0,
            'path' => \UPS\Shipping\Helper\Config::UPS_MERCHANTINFO_EXIST, self::VALUE => '0'],

            [ self::SCOPE => self::VAL_DEFAULT, self::SCOPE_ID => 0,
            'path' => \UPS\Shipping\Helper\Config::UPS_DELIVERYRATES_EXIST, self::VALUE => '0'],
            // Merchant key
            [ self::SCOPE => self::VAL_DEFAULT, self::SCOPE_ID => 0,
            'path' => \UPS\Shipping\Helper\Config::UPS_MERCHANTKEY, self::VALUE => $merchantKey],
            // Ups COD Option
            [ self::SCOPE => self::VAL_DEFAULT, self::SCOPE_ID => 0,
            'path' => \UPS\Shipping\Helper\Config::CASH_ON_DELIVERY_UPS_SHIPPING_OPTION_ACTIVE, self::VALUE => '0']
        ];

        $statusShipment = [
            [ self::STATUS => 'shipped', self::LABEL => 'Shipped'],
            [ self::STATUS => 'delivered', self::LABEL => 'Delivered'],
            [ self::STATUS => 'order_canceled', self::LABEL => 'Order Canceled'],
            [ self::STATUS => 'refunded', self::LABEL => 'Refunded'],
            [ self::STATUS => 'processing_in_progress', self::LABEL => 'Processing In Progress']
        ];
        foreach ($statusShipment as $data) {
            $setup->getConnection()->insertOnDuplicate($setup->getTable('sales_order_status'), $data);
        }

        //insert data License
        $insertData = [
            'id' => '1',
            'AccessLicenseText' => '',
            'Username' => 'TuChu0103',
            'Password' => 'T!@#052018',
            'AccessLicenseNumber' => '0D46678E86A9D038'
        ];
        $setup->getConnection()->insertOnDuplicate($setup->getTable('ups_shipping_license'), $insertData);

        //accessorial
        $dataAccessorial = [
            ['id' => 1, self::ACCESSORIAL_KEY => 'UPS_ACSRL_ADDITIONAL_HADING', self::ACCESSORIAL_NAME
            => 'Additional handling',
             self::ACCESSORIAL_CODE => '100', self::SHOW_CONFIG => 1, self::SHOW_SHIPPING => 0],
            ['id' => 2, self::ACCESSORIAL_KEY => 'UPS_ACSRL_QV_SHIP_NOTIF',
            self::ACCESSORIAL_NAME => 'Quantum View Ship Notification', self::ACCESSORIAL_CODE => '6',
            self::SHOW_CONFIG => 1, self::SHOW_SHIPPING => 0],
            ['id' => 3, self::ACCESSORIAL_KEY => 'UPS_ACSRL_QV_DLV_NOTIF',
            self::ACCESSORIAL_NAME => 'Quantum View Delivery Notification', self::ACCESSORIAL_CODE => '372',
            self::SHOW_CONFIG => 1, self::SHOW_SHIPPING => 0],
            ['id' => 4, self::ACCESSORIAL_KEY => 'UPS_ACSRL_RESIDENTIAL_ADDRESS',
            self::ACCESSORIAL_NAME => 'Residential Address', self::ACCESSORIAL_CODE => '270',
            self::SHOW_CONFIG => 0, self::SHOW_SHIPPING => 0],
            ['id' => 5, self::ACCESSORIAL_KEY => 'UPS_ACSRL_STATURDAY_DELIVERY',
            self::ACCESSORIAL_NAME => 'Saturday Delivery', self::ACCESSORIAL_CODE => '300', self::SHOW_CONFIG => 0,
            self::SHOW_SHIPPING => 0],
            ['id' => 6, self::ACCESSORIAL_KEY => 'UPS_ACSRL_CARBON_NEUTRAL', self::ACCESSORIAL_NAME => 'Carbon Neutral',
            self::ACCESSORIAL_CODE => '441', self::SHOW_CONFIG => 1, self::SHOW_SHIPPING => 0],
            ['id' => 7, self::ACCESSORIAL_KEY => 'UPS_ACSRL_DIRECT_DELIVERY_ONLY',
            self::ACCESSORIAL_NAME => 'Direct Delivery Only', self::ACCESSORIAL_CODE => '541', self::SHOW_CONFIG => 0,
            self::SHOW_SHIPPING => 0],
            ['id' => 8, self::ACCESSORIAL_KEY => 'UPS_ACSRL_DECLARED_VALUE', self::ACCESSORIAL_NAME => 'Declared value',
            self::ACCESSORIAL_CODE => '5', self::SHOW_CONFIG => 0, self::SHOW_SHIPPING => 0],
            ['id' => 9, self::ACCESSORIAL_KEY => 'UPS_ACSRL_SIGNATURE_REQUIRED', self::ACCESSORIAL_NAME
            => 'Signature Required',
             self::ACCESSORIAL_CODE => '2', self::SHOW_CONFIG => 1, self::SHOW_SHIPPING => 0],
            ['id' => 10, self::ACCESSORIAL_KEY => 'UPS_ACSRL_ADULT_SIG_REQUIRED',
            self::ACCESSORIAL_NAME => 'Adult Signature Required', self::ACCESSORIAL_CODE => '3', self::SHOW_CONFIG => 1,
            self::SHOW_SHIPPING => 0],
            ['id' => 11, self::ACCESSORIAL_KEY => 'UPS_ACSRL_ACCESS_POINT_COD', self::ACCESSORIAL_NAME
            => 'To Access Point COD',
             self::ACCESSORIAL_CODE => '4', self::SHOW_CONFIG => 0, self::SHOW_SHIPPING => 0],
            ['id' => 12, self::ACCESSORIAL_KEY => 'UPS_ACSRL_TO_HOME_COD', self::ACCESSORIAL_NAME => 'To Home COD',
            self::ACCESSORIAL_CODE => '500', self::SHOW_CONFIG => 0, self::SHOW_SHIPPING => 0]
        ];
        foreach ($dataAccessorial as $data) {
            $setup->getConnection()->insertOnDuplicate($setup->getTable('ups_shipping_accessorial'), $data);
        }

        //services
        $dataServices = [
            ['id' => 1, self::COUNTRY_CODE => 'PL', self::SERVICE_TYPE => 'AP',
            self::SERVICE_KEY => 'UPS_SP_SERV_PL_AP_AP_ECONOMY',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_PL_AP_AP_ECONOMY',
            self::SERVICE_KEY_VAL => 'UPS_DELI_PL_AP_AP_ECONOMY_VAL',
            self::SERVICE_NAME => 'UPS Access Point Economy', self::RATE_CODE => '70', self::TIN_T_CODE => '39',
            self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => '&trade;'],
            ['id' => 2, self::COUNTRY_CODE => 'PL', self::SERVICE_TYPE => 'AP',
            self::SERVICE_KEY => 'UPS_SP_SERV_PL_AP_STANDARD',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_PL_AP_STANDARD',
            self::SERVICE_KEY_VAL => 'UPS_DELI_PL_AP_STANDARD_VAL',
            self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => '25',
            self::SERVICE_SELECTED => 0,
            self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 3, self::COUNTRY_CODE => 'PL', self::SERVICE_TYPE => 'AP',
            self::SERVICE_KEY => 'UPS_SP_SERV_PL_AP_EXPEDITED',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_PL_AP_EXPEDITED',
            self::SERVICE_KEY_VAL => 'UPS_DELI_PL_AP_EXPEDITED_VAL',
            self::SERVICE_NAME => self::UPS_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => '05',
            self::SERVICE_SELECTED => 0,
            self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 4, self::COUNTRY_CODE => 'PL', self::SERVICE_TYPE => 'AP',
            self::SERVICE_KEY => 'UPS_SP_SERV_PL_AP_EXPRESS_SAVER',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_PL_AP_EXPRESS_SAVER',
            self::SERVICE_KEY_VAL => 'UPS_DELI_PL_AP_EXPRESS_SAVER_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_SAVER,
            self::RATE_CODE => '65', self::TIN_T_CODE => '26', self::SERVICE_SELECTED => 0,
            self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 5, self::COUNTRY_CODE => 'PL', self::SERVICE_TYPE => 'AP',
            self::SERVICE_KEY => 'UPS_SP_SERV_PL_AP_EXPRESS',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_PL_AP_EXPRESS',
            self::SERVICE_KEY_VAL => 'UPS_DELI_PL_AP_EXPRESS_VAL',
            self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => '24',
            self::SERVICE_SELECTED => 0,
            self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 6, self::COUNTRY_CODE => 'PL', self::SERVICE_TYPE => 'AP',
            self::SERVICE_KEY => 'UPS_SP_SERV_PL_AP_EXPRESS_PLUS',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_PL_AP_EXPRESS_PLUS',
            self::SERVICE_KEY_VAL => 'UPS_DELI_PL_AP_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS,
            self::RATE_CODE => '54', self::TIN_T_CODE => '23', self::SERVICE_SELECTED => 0,
            self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 7, self::COUNTRY_CODE => 'PL', self::SERVICE_TYPE => 'ADD',
            self::SERVICE_KEY => 'UPS_SP_SERV_PL_ADD_STANDARD',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_PL_ADD_STANDARD',
            self::SERVICE_KEY_VAL => 'UPS_DELI_PL_ADD_STANDARD_VAL',
            self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => '25',
            self::SERVICE_SELECTED => 0,
            self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 8, self::COUNTRY_CODE => 'PL', self::SERVICE_TYPE => 'ADD',
            self::SERVICE_KEY => 'UPS_SP_SERV_PL_ADD_EXPEDITED',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_PL_ADD_EXPEDITED',
            self::SERVICE_KEY_VAL => 'UPS_DELI_PL_ADD_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_EXPEDITED,
            self::RATE_CODE => '08', self::TIN_T_CODE => '05', self::SERVICE_SELECTED => 0,
            self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 9, self::COUNTRY_CODE => 'PL', self::SERVICE_TYPE => 'ADD',
            self::SERVICE_KEY => 'UPS_SP_SERV_PL_ADD_EXPRESS_SAVER',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_PL_ADD_EXPRESS_SAVER',
            self::SERVICE_KEY_VAL => 'UPS_DELI_PL_ADD_EXPRESS_SAVER_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_SAVER,
            self::RATE_CODE => '65', self::TIN_T_CODE => '26', self::SERVICE_SELECTED => 0,
            self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 10, self::COUNTRY_CODE => 'PL', self::SERVICE_TYPE => 'ADD',
            self::SERVICE_KEY => 'UPS_SP_SERV_PL_ADD_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_PL_ADD_EXPRESS',
            self::SERVICE_KEY_VAL => 'UPS_DELI_PL_ADD_EXPRESS_VAL',
            self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => '24',
            self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 11, self::COUNTRY_CODE => 'PL', self::SERVICE_TYPE => 'ADD',
            self::SERVICE_KEY => 'UPS_SP_SERV_PL_ADD_EXPRESS_PLUS',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_PL_ADD_EXPRESS_PLUS',
            self::SERVICE_KEY_VAL => 'UPS_DELI_PL_ADD_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS,
            self::RATE_CODE => '54', self::TIN_T_CODE => '23', self::SERVICE_SELECTED => 0,
            self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 12, self::COUNTRY_CODE => 'GB', self::SERVICE_TYPE => 'AP',
            self::SERVICE_KEY => 'UPS_SP_SERV_GB_AP_STANDARD',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_GB_AP_STANDARD',
            self::SERVICE_KEY_VAL => 'UPS_DELI_GB_AP_STANDARD_VAL',
            self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => '68',
            self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 13, self::COUNTRY_CODE => 'GB', self::SERVICE_TYPE => 'AP',
            self::SERVICE_KEY => 'UPS_SP_SERV_GB_AP_EXPEDITED',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_GB_AP_EXPEDITED',
            self::SERVICE_KEY_VAL => 'UPS_DELI_GB_AP_EXPEDITED_VAL',
            self::SERVICE_NAME => self::UPS_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => '05',
            self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 14, self::COUNTRY_CODE => 'GB', self::SERVICE_TYPE => 'AP',
            self::SERVICE_KEY => 'UPS_SP_SERV_GB_AP_WW_SAVER',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_GB_AP_WW_SAVER',
            self::SERVICE_KEY_VAL => 'UPS_DELI_GB_AP_WW_SAVER_VAL',
            self::SERVICE_NAME => self::UPS_EXPRESS_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null,
            self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 15, self::COUNTRY_CODE => 'GB', self::SERVICE_TYPE => 'AP',
            self::SERVICE_KEY => 'UPS_SP_SERV_GB_AP_EXPRESS',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_GB_AP_EXPRESS',
            self::SERVICE_KEY_VAL => 'UPS_DELI_GB_AP_EXPRESS_VAL',
            self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null,
            self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 16, self::COUNTRY_CODE => 'GB', self::SERVICE_TYPE => 'AP',
            self::SERVICE_KEY => 'UPS_SP_SERV_GB_AP_WW_EXPRESS_PLUS',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_GB_AP_WW_EXPRESS_PLUS',
            self::SERVICE_KEY_VAL => 'UPS_DELI_GB_AP_WW_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS,
            self::RATE_CODE => '54', self::TIN_T_CODE => '21', self::SERVICE_SELECTED => 0,
            self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 17, self::COUNTRY_CODE => 'GB', self::SERVICE_TYPE => 'ADD',
            self::SERVICE_KEY => 'UPS_SP_SERV_GB_ADD_STANDARD',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_GB_ADD_STANDARD',
            self::SERVICE_KEY_VAL => 'UPS_DELI_GB_ADD_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD,
            self::RATE_CODE => '11', self::TIN_T_CODE => '68', self::SERVICE_SELECTED => 0,
            self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 18, self::COUNTRY_CODE => 'GB', self::SERVICE_TYPE => 'ADD',
            self::SERVICE_KEY => 'UPS_SP_SERV_GB_ADD_EXPEDITED',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_GB_ADD_EXPEDITED',
            self::SERVICE_KEY_VAL => 'UPS_DELI_GB_ADD_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_EXPEDITED,
            self::RATE_CODE => '08', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0,
            self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 19, self::COUNTRY_CODE => 'GB', self::SERVICE_TYPE => 'ADD',
            self::SERVICE_KEY => 'UPS_SP_SERV_GB_ADD_WW_SAVER',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_GB_ADD_WW_SAVER',
            self::SERVICE_KEY_VAL => 'UPS_DELI_GB_ADD_WW_SAVER_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_SAVER,
            self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0,
            self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 20, self::COUNTRY_CODE => 'GB', self::SERVICE_TYPE => 'ADD',
            self::SERVICE_KEY => 'UPS_SP_SERV_GB_ADD_EXPRESS',
            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_GB_ADD_EXPRESS',
            self::SERVICE_KEY_VAL => 'UPS_DELI_GB_ADD_EXPRESS_VAL',
            self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
            ['id' => 21, self::COUNTRY_CODE => 'GB', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_GB_ADD_WW_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_GB_ADD_WW_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_GB_ADD_WW_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => '21', self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG]
        ];
        foreach ($dataServices as $data) {
            $setup->getConnection()->insertOnDuplicate($setup->getTable('ups_shipping_services'), $data);
        }

        // install data core_config_data
        foreach ($dataConfigs as $data) {
            $setup->getConnection()->insertOnDuplicate($setup->getTable('core_config_data'), $data);
        }
    }

    /**
     * InstallData generateMerchantKey
     * calculate shipping service
     *
     * @return array $data
     */
    public function generateMerchantKey()
    {
        $randmax_bits = strlen(base_convert(mt_getrandmax(), 10, 2));
        $x = '';

        while (strlen($x) < 128) {
            $maxbits = (128 - strlen($x) < $randmax_bits) ? 128 - strlen($x) :  $randmax_bits;
            $x .= str_pad(base_convert(random_int(9999999, 999999999), 10, 2), $maxbits, "0", STR_PAD_LEFT);
        }

        $x .= str_pad(base_convert(random_int(9999999, 999999999), 10, 2), $maxbits, "0", STR_PAD_LEFT);
        $a = [];
        $a['time_low_part'] = substr($x, 0, 32);
        $a['time_mid'] = substr($x, 32, 16);
        $a[self::TIME_HI_AND_VERSION] = substr($x, 48, 16);
        $a[self::CLOCK_SEQ] = substr($x, 64, 16);
        $a['node_part'] =  substr($x, 80, 48);

        $a[self::TIME_HI_AND_VERSION] = substr_replace($a[self::TIME_HI_AND_VERSION], '0100', 0, 4);
        $a[self::CLOCK_SEQ] = substr_replace($a[self::CLOCK_SEQ], '10', 0, 2);

        $timeLowPart = str_pad(base_convert($a['time_low_part'], 2, 16), 8, "0", STR_PAD_LEFT);
        $timeMid = str_pad(base_convert($a['time_mid'], 2, 16), 4, "0", STR_PAD_LEFT);
        $timeHiVersion = str_pad(base_convert($a[self::TIME_HI_AND_VERSION], 2, 16), 4, "0", STR_PAD_LEFT);
        $clockSeq = str_pad(base_convert($a[self::CLOCK_SEQ], 2, 16), 4, "0", STR_PAD_LEFT);
        $noPart = str_pad(base_convert($a['node_part'], 2, 16), 12, "0", STR_PAD_LEFT);
        return sprintf('%s-%s-%s-%s-%s', $timeLowPart, $timeMid, $timeHiVersion, $clockSeq, $noPart);
    }
}
