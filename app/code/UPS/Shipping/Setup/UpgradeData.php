<?php
/**
 * UpgradeSchema file
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

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
/**
 * UpgradeSchema class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class UpgradeData implements UpgradeDataInterface
{
    const NULLABLE = 'nullable';
    const COMMENT = 'comment';
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
    const UPS_STANDARD_SAT_DELI = 'UPS Standard - Saturday Delivery';
    const SYMBOL_REG = '&reg;';
    const UPS_EXPEDITED = 'UPS Expedited';
    const UPS_EXPRESS_SAVER = 'UPS Express Saver';
    const UPS_EXPRESS = 'UPS Express';
    const UPS_EXPRESS_SAT_DELI = 'UPS Express - Saturday Delivery';
    const UPS_EXPRESS12 = 'UPS Express 12:00';
    const UPS_EXPRESS_PLUS = 'UPS Express Plus';
    const TIME_HI_AND_VERSION = 'time_hi_and_version';
    const CLOCK_SEQ = 'clock_seq';
    const UPS_DAY_AIR = 'UPS 2nd Day Air';
    const UPS_DAY_AIR_AM = 'UPS 2nd Day Air A.M.';
    const UPS_3_DAY_SELECT = 'UPS 3 Day Select';
    const UPS_GROUND = 'UPS Ground';
    const UPS_AIR_EARLY = 'UPS Next Day Air Early';
    const UPS_AIR_SAVER = 'UPS Next Day Air Saver';
    const UPS_AIR = 'UPS Next Day Air';
    const UPS_WORLDWIDE_EXPEDITED = 'UPS Worldwide Expedited';
    const UPS_WORLDWIDE_EXPRESS_PLUS = 'UPS Worldwide Express Plus';
    const UPS_WORLDWIDE_EXPRESS = 'UPS Worldwide Express';
    const UPS_WORLDWIDE_SAVER = 'UPS Worldwide Saver';

    /**
     * UpgradeSchema upgrade
     * calculate shipping service
     *
     * @param string $setup   //The setup
     * @param string $context //The context
     *
     * @return array $data
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.1.0') < 0) {
            // Get tutorial_simplenews table
            $tableName = $setup->getTable('ups_shipping_services');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $sqlServices = "SELECT id FROM $tableName WHERE `id` = 22";
                $serviceAll = [];
                $query = $setup->getConnection()->query($sqlServices);
                while ($row = $query->fetch()) {
                    $serviceAll[] = $row;
                }
                if (empty($serviceAll)) {
                    // Declare data
                    $dataDEServices = [
                        ['id' => 23, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_DE_AP_EXPEDITED', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_AP_EXPEDITED', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_AP_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 26, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_DE_AP_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_AP_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_AP_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 27, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_DE_AP_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_AP_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_AP_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 24, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_DE_AP_EXPRESS_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_AP_EXPRESS_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_AP_EXPRESS_SAVER_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 22, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_DE_AP_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_AP_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_AP_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 25, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_DE_AP_UPS_EXPRESS12', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_AP_UPS_EXPRESS12', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_AP_UPS_EXPRESS12_VAL', self::SERVICE_NAME => self::UPS_EXPRESS12, self::RATE_CODE => '74', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => null],
                        ['id' => 29, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_DE_ADD_EXPEDITED', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_ADD_EXPEDITED', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_ADD_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 32, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_DE_ADD_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_ADD_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_ADD_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 33, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_DE_ADD_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_ADD_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_ADD_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 30, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_DE_ADD_EXPRESS_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_ADD_EXPRESS_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_ADD_EXPRESS_SAVER_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 28, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_DE_ADD_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_ADD_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_ADD_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 31, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_DE_ADD_EXPRESS12', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_ADD_EXPRESS12', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_ADD_EXPRESS12_VAL', self::SERVICE_NAME => self::UPS_EXPRESS12, self::RATE_CODE => '74', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 34, self::COUNTRY_CODE => 'FR', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_FR_AP_AP_ECONOMY', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_FR_AP_AP_ECONOMY', self::SERVICE_KEY_VAL => 'UPS_DELI_FR_AP_AP_ECONOMY_VAL', self::SERVICE_NAME => 'UPS Access Point Economy', self::RATE_CODE => '70', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => '&trade;'],
                        ['id' => 36, self::COUNTRY_CODE => 'FR', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_FR_AP_EXPEDITED', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_FR_AP_EXPEDITED', self::SERVICE_KEY_VAL => 'UPS_DELI_FR_AP_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 38, self::COUNTRY_CODE => 'FR', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_FR_AP_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_FR_AP_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_FR_AP_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 39, self::COUNTRY_CODE => 'FR', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_FR_AP_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_FR_AP_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_FR_AP_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 37, self::COUNTRY_CODE => 'FR', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_FR_AP_EXPRESS_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_FR_AP_EXPRESS_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_FR_AP_EXPRESS_SAVER_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 35, self::COUNTRY_CODE => 'FR', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_FR_AP_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_FR_AP_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_FR_AP_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 41, self::COUNTRY_CODE => 'FR', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_FR_ADD_EXPEDITED', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_FR_ADD_EXPEDITED', self::SERVICE_KEY_VAL => 'UPS_DELI_FR_ADD_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 43, self::COUNTRY_CODE => 'FR', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_FR_ADD_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_FR_ADD_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_FR_ADD_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 44, self::COUNTRY_CODE => 'FR', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_FR_ADD_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_FR_ADD_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_FR_ADD_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 42, self::COUNTRY_CODE => 'FR', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_FR_ADD_EXPRESS_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_FR_ADD_EXPRESS_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_FR_ADD_EXPRESS_SAVER_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 40, self::COUNTRY_CODE => 'FR', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_FR_ADD_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_FR_ADD_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_FR_ADD_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 46, self::COUNTRY_CODE => 'ES', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_ES_AP_EXPEDITED', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_ES_AP_EXPEDITED', self::SERVICE_KEY_VAL => 'UPS_DELI_ES_AP_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 48, self::COUNTRY_CODE => 'ES', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_ES_AP_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_ES_AP_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_ES_AP_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 49, self::COUNTRY_CODE => 'ES', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_ES_AP_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_ES_AP_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_ES_AP_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 47, self::COUNTRY_CODE => 'ES', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_ES_AP_EXPRESS_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_ES_AP_EXPRESS_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_ES_AP_EXPRESS_SAVER_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 45, self::COUNTRY_CODE => 'ES', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_ES_AP_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_ES_AP_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_ES_AP_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 51, self::COUNTRY_CODE => 'ES', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_ES_ADD_EXPEDITED', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_ES_ADD_EXPEDITED', self::SERVICE_KEY_VAL => 'UPS_DELI_ES_ADD_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 53, self::COUNTRY_CODE => 'ES', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_ES_ADD_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_ES_ADD_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_ES_ADD_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 54, self::COUNTRY_CODE => 'ES', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_ES_ADD_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_ES_ADD_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_ES_ADD_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 52, self::COUNTRY_CODE => 'ES', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_ES_ADD_EXPRESS_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_ES_ADD_EXPRESS_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_ES_ADD_EXPRESS_SAVER_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 50, self::COUNTRY_CODE => 'ES', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_ES_ADD_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_ES_ADD_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_ES_ADD_STANDARD_VAL', self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 56, self::COUNTRY_CODE => 'IT', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_IT_AP_EXPEDITED', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_IT_AP_EXPEDITED', self::SERVICE_KEY_VAL => 'UPS_DELI_IT_AP_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 58, self::COUNTRY_CODE => 'IT', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_IT_AP_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_IT_AP_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_IT_AP_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 59, self::COUNTRY_CODE => 'IT', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_IT_AP_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_IT_AP_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_IT_AP_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 57, self::COUNTRY_CODE => 'IT', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_IT_AP_EXPRESS_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_IT_AP_EXPRESS_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_IT_AP_EXPRESS_SAVER_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 55, self::COUNTRY_CODE => 'IT', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_IT_AP_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_IT_AP_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_IT_AP_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 61, self::COUNTRY_CODE => 'IT', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_IT_ADD_EXPEDITED', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_IT_ADD_EXPEDITED', self::SERVICE_KEY_VAL => 'UPS_DELI_IT_ADD_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 63, self::COUNTRY_CODE => 'IT', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_IT_ADD_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_IT_ADD_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_IT_ADD_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 64, self::COUNTRY_CODE => 'IT', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_IT_ADD_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_IT_ADD_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_IT_ADD_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 62, self::COUNTRY_CODE => 'IT', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_IT_ADD_EXPRESS_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_IT_ADD_EXPRESS_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_IT_ADD_EXPRESS_SAVER_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 60, self::COUNTRY_CODE => 'IT', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_IT_ADD_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_IT_ADD_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_IT_ADD_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 65, self::COUNTRY_CODE => 'NL', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_NL_AP_AP_ECONOMY', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_NL_AP_AP_ECONOMY', self::SERVICE_KEY_VAL => 'UPS_DELI_NL_AP_AP_ECONOMY_VAL', self::SERVICE_NAME => 'UPS Access Point Economy', self::RATE_CODE => '70', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => '&trade;'],
                        ['id' => 67, self::COUNTRY_CODE => 'NL', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_NL_AP_EXPEDITED', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_NL_AP_EXPEDITED', self::SERVICE_KEY_VAL => 'UPS_DELI_NL_AP_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 69, self::COUNTRY_CODE => 'NL', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_NL_AP_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_NL_AP_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_NL_AP_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 70, self::COUNTRY_CODE => 'NL', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_NL_AP_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_NL_AP_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_NL_AP_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 68, self::COUNTRY_CODE => 'NL', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_NL_AP_EXPRESS_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_NL_AP_EXPRESS_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_NL_AP_EXPRESS_SAVER_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 66, self::COUNTRY_CODE => 'NL', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_NL_AP_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_NL_AP_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_NL_AP_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 72, self::COUNTRY_CODE => 'NL', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_NL_ADD_EXPEDITED', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_NL_ADD_EXPEDITED', self::SERVICE_KEY_VAL => 'UPS_DELI_NL_ADD_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 74, self::COUNTRY_CODE => 'NL', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_NL_ADD_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_NL_ADD_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_NL_ADD_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 75, self::COUNTRY_CODE => 'NL', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_NL_ADD_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_NL_ADD_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_NL_ADD_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 73, self::COUNTRY_CODE => 'NL', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_NL_ADD_EXPRESS_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_NL_ADD_EXPRESS_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_NL_ADD_EXPRESS_SAVER_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 71, self::COUNTRY_CODE => 'NL', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_NL_ADD_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_NL_ADD_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_NL_ADD_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 76, self::COUNTRY_CODE => 'BE', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_BE_AP_AP_ECONOMY', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_BE_AP_AP_ECONOMY', self::SERVICE_KEY_VAL => 'UPS_DELI_BE_AP_AP_ECONOMY_VAL', self::SERVICE_NAME => 'UPS Access Point Economy', self::RATE_CODE => '70', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => '&trade;'],
                        ['id' => 78, self::COUNTRY_CODE => 'BE', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_BE_AP_EXPEDITED', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_BE_AP_EXPEDITED', self::SERVICE_KEY_VAL => 'UPS_DELI_BE_AP_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 80, self::COUNTRY_CODE => 'BE', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_BE_AP_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_BE_AP_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_BE_AP_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 81, self::COUNTRY_CODE => 'BE', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_BE_AP_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_BE_AP_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_BE_AP_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 79, self::COUNTRY_CODE => 'BE', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_BE_AP_EXPRESS_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_BE_AP_EXPRESS_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_BE_AP_EXPRESS_SAVER_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 77, self::COUNTRY_CODE => 'BE', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_BE_AP_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_BE_AP_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_BE_AP_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 83, self::COUNTRY_CODE => 'BE', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_BE_ADD_EXPEDITED', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_BE_ADD_EXPEDITED', self::SERVICE_KEY_VAL => 'UPS_DELI_BE_ADD_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 85, self::COUNTRY_CODE => 'BE', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_BE_ADD_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_BE_ADD_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_BE_ADD_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 86, self::COUNTRY_CODE => 'BE', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_BE_ADD_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_BE_ADD_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_BE_ADD_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 84, self::COUNTRY_CODE => 'BE', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_BE_ADD_EXPRESS_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_BE_ADD_EXPRESS_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_BE_ADD_EXPRESS_SAVER_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 82, self::COUNTRY_CODE => 'BE', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_BE_ADD_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_BE_ADD_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_BE_ADD_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG]
                    ];

                    foreach ($dataDEServices as $data) {
                        $setup->getConnection()->insertOnDuplicate($setup->getTable('ups_shipping_services'), $data);
                    }
                }

                $sqlServicesUS = "SELECT id FROM $tableName WHERE `id` = 87";
                $serviceUS = [];
                $queryUS = $setup->getConnection()->query($sqlServicesUS);
                while ($row = $queryUS->fetch()) {
                    $serviceUS[] = $row;
                }
                if (empty($serviceUS)) {
                    // Declare data
                    $dataUSServices = [
                        ['id' => 87, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_GROUND', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_GROUND', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_GROUND_VAL', self::SERVICE_NAME => self::UPS_GROUND, self::RATE_CODE => '03', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 88, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_3_DAY_SELECT', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_3_DAY_SELECT', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_3_DAY_SELECT_VAL', self::SERVICE_NAME => self::UPS_3_DAY_SELECT, self::RATE_CODE => '12', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 89,self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_DAY_AIR', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_DAY_AIR', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_DAY_AIR_VAL', self::SERVICE_NAME => self::UPS_DAY_AIR, self::RATE_CODE => '02', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 90, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_DAY_AIR_AM', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_DAY_AIR_AM', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_DAY_AIR_AM_VAL', self::SERVICE_NAME => self::UPS_DAY_AIR_AM, self::RATE_CODE => '59', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 91, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_AIR_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_AIR_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_AIR_SAVER_VAL', self::SERVICE_NAME => self::UPS_AIR_SAVER, self::RATE_CODE => '13', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 92, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_AIR', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_AIR', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_AIR_VAL', self::SERVICE_NAME => self::UPS_AIR, self::RATE_CODE => '01', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 93, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_AIR_EARLY', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_AIR_EARLY', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_AIR_EARLY_VAL', self::SERVICE_NAME => self::UPS_AIR_EARLY, self::RATE_CODE => '14', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 94, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 95, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_WORLDWIDE_EXPEDITED', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_WORLDWIDE_EXPEDITED', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_WORLDWIDE_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_WORLDWIDE_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 96, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_WORLDWIDE_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_WORLDWIDE_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_WORLDWIDE_SAVER_VAL', self::SERVICE_NAME => self::UPS_WORLDWIDE_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 97, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_WORLDWIDE_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_WORLDWIDE_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_WORLDWIDE_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_WORLDWIDE_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 98, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_WORLDWIDE_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_WORLDWIDE_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_WORLDWIDE_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_WORLDWIDE_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 99, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_GROUND', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_GROUND', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_GROUND_VAL', self::SERVICE_NAME => self::UPS_GROUND, self::RATE_CODE => '03', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 100, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_3_DAY_SELECT', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_3_DAY_SELECT', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_3_DAY_SELECT_VAL', self::SERVICE_NAME => self::UPS_3_DAY_SELECT, self::RATE_CODE => '12', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 101, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_DAY_AIR', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_DAY_AIR', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_DAY_AIR_VAL', self::SERVICE_NAME => self::UPS_DAY_AIR, self::RATE_CODE => '02', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 102, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_DAY_AIR_AM', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_DAY_AIR_AM', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_DAY_AIR_AM_VAL', self::SERVICE_NAME => self::UPS_DAY_AIR_AM, self::RATE_CODE => '59', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 103, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_AIR_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_AIR_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_AIR_SAVER_VAL', self::SERVICE_NAME => self::UPS_AIR_SAVER, self::RATE_CODE => '13', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 104, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_AIR', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_AIR', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_AIR_VAL', self::SERVICE_NAME => self::UPS_AIR, self::RATE_CODE => '01', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 105, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_AIR_EARLY', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_AIR_EARLY', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_AIR_EARLY_VAL', self::SERVICE_NAME => self::UPS_AIR_EARLY, self::RATE_CODE => '14', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 106, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 107, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_WORLDWIDE_EXPEDITED', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_WORLDWIDE_EXPEDITED', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_WORLDWIDE_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_WORLDWIDE_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 108, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_WORLDWIDE_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_WORLDWIDE_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_WORLDWIDE_SAVER_VAL', self::SERVICE_NAME => self::UPS_WORLDWIDE_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 109, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_WORLDWIDE_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_WORLDWIDE_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_WORLDWIDE_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_WORLDWIDE_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 110, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_WORLDWIDE_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_WORLDWIDE_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_WORLDWIDE_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_WORLDWIDE_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG]
                    ];

                    foreach ($dataUSServices as $data) {
                        $setup->getConnection()->insertOnDuplicate($setup->getTable('ups_shipping_services'), $data);
                    }
                }

                // Additional the ups_shipping_services
                $dataServices = [
                    ['id' => 21, self::COUNTRY_CODE => 'GB', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_GB_ADD_WW_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_GB_ADD_WW_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_GB_ADD_WW_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => '21', self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 25, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_DE_AP_UPS_EXPRESS12', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_AP_UPS_EXPRESS12', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_AP_UPS_EXPRESS12_VAL', self::SERVICE_NAME => self::UPS_EXPRESS12, self::RATE_CODE => '74', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => null],
                    ['id' => 26, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_DE_AP_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_AP_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_AP_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 27, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_DE_AP_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_AP_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_AP_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 31, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_DE_ADD_EXPRESS12', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_ADD_EXPRESS12', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_ADD_EXPRESS12_VAL', self::SERVICE_NAME => self::UPS_EXPRESS12, self::RATE_CODE => '74', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 32, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_DE_ADD_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_ADD_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_ADD_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 33, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_DE_ADD_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_ADD_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_DE_ADD_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 45, self::COUNTRY_CODE => 'ES', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_ES_AP_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_ES_AP_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_ES_AP_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 50, self::COUNTRY_CODE => 'ES', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_ES_ADD_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_ES_ADD_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_ES_ADD_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 62, self::COUNTRY_CODE => 'IT', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_IT_ADD_EXPRESS_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_IT_ADD_EXPRESS_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_IT_ADD_EXPRESS_SAVER_VAL', self::SERVICE_NAME => self::UPS_EXPRESS_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0 , self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 87, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_GROUND', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_GROUND', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_GROUND_VAL', self::SERVICE_NAME => self::UPS_GROUND, self::RATE_CODE => '03', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 88, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_3_DAY_SELECT', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_3_DAY_SELECT', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_3_DAY_SELECT_VAL', self::SERVICE_NAME => self::UPS_3_DAY_SELECT, self::RATE_CODE => '12', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 89,self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_DAY_AIR', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_DAY_AIR', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_DAY_AIR_VAL', self::SERVICE_NAME => self::UPS_DAY_AIR, self::RATE_CODE => '02', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 90, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_DAY_AIR_AM', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_DAY_AIR_AM', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_DAY_AIR_AM_VAL', self::SERVICE_NAME => self::UPS_DAY_AIR_AM, self::RATE_CODE => '59', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 91, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_AIR_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_AIR_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_AIR_SAVER_VAL', self::SERVICE_NAME => self::UPS_AIR_SAVER, self::RATE_CODE => '13', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 92, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_AIR', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_AIR', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_AIR_VAL', self::SERVICE_NAME => self::UPS_AIR, self::RATE_CODE => '01', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 93, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_AIR_EARLY', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_AIR_EARLY', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_AIR_EARLY_VAL', self::SERVICE_NAME => self::UPS_AIR_EARLY, self::RATE_CODE => '14', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 94, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 95, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_WORLDWIDE_EXPEDITED', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_WORLDWIDE_EXPEDITED', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_WORLDWIDE_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_WORLDWIDE_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 96, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_WORLDWIDE_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_WORLDWIDE_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_WORLDWIDE_SAVER_VAL', self::SERVICE_NAME => self::UPS_WORLDWIDE_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 97, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_WORLDWIDE_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_WORLDWIDE_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_WORLDWIDE_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_WORLDWIDE_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 98, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'AP', self::SERVICE_KEY => 'UPS_SP_SERV_US_AP_WORLDWIDE_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_AP_WORLDWIDE_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_US_AP_WORLDWIDE_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_WORLDWIDE_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 99, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_GROUND', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_GROUND', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_GROUND_VAL', self::SERVICE_NAME => self::UPS_GROUND, self::RATE_CODE => '03', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 100, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_3_DAY_SELECT', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_3_DAY_SELECT', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_3_DAY_SELECT_VAL', self::SERVICE_NAME => self::UPS_3_DAY_SELECT, self::RATE_CODE => '12', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 101, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_DAY_AIR', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_DAY_AIR', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_DAY_AIR_VAL', self::SERVICE_NAME => self::UPS_DAY_AIR, self::RATE_CODE => '02', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 102, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_DAY_AIR_AM', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_DAY_AIR_AM', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_DAY_AIR_AM_VAL', self::SERVICE_NAME => self::UPS_DAY_AIR_AM, self::RATE_CODE => '59', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 103, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_AIR_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_AIR_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_AIR_SAVER_VAL', self::SERVICE_NAME => self::UPS_AIR_SAVER, self::RATE_CODE => '13', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 104, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_AIR', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_AIR', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_AIR_VAL', self::SERVICE_NAME => self::UPS_AIR, self::RATE_CODE => '01', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 105, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_AIR_EARLY', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_AIR_EARLY', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_AIR_EARLY_VAL', self::SERVICE_NAME => self::UPS_AIR_EARLY, self::RATE_CODE => '14', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 106, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_STANDARD', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_STANDARD', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_STANDARD_VAL', self::SERVICE_NAME => self::UPS_STANDARD, self::RATE_CODE => '11', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 107, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_WORLDWIDE_EXPEDITED', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_WORLDWIDE_EXPEDITED', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_WORLDWIDE_EXPEDITED_VAL', self::SERVICE_NAME => self::UPS_WORLDWIDE_EXPEDITED, self::RATE_CODE => '08', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 108, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_WORLDWIDE_SAVER', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_WORLDWIDE_SAVER', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_WORLDWIDE_SAVER_VAL', self::SERVICE_NAME => self::UPS_WORLDWIDE_SAVER, self::RATE_CODE => '65', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 109, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_WORLDWIDE_EXPRESS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_WORLDWIDE_EXPRESS', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_WORLDWIDE_EXPRESS_VAL', self::SERVICE_NAME => self::UPS_WORLDWIDE_EXPRESS, self::RATE_CODE => '07', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG],
                    ['id' => 110, self::COUNTRY_CODE => 'US', self::SERVICE_TYPE => 'ADD', self::SERVICE_KEY => 'UPS_SP_SERV_US_ADD_WORLDWIDE_EXPRESS_PLUS', self::SERVICE_KEY_DELIVERY => 'UPS_DELI_US_ADD_WORLDWIDE_EXPRESS_PLUS', self::SERVICE_KEY_VAL => 'UPS_DELI_US_ADD_WORLDWIDE_EXPRESS_PLUS_VAL', self::SERVICE_NAME => self::UPS_WORLDWIDE_EXPRESS_PLUS, self::RATE_CODE => '54', self::TIN_T_CODE => null, self::SERVICE_SELECTED => 0, self::SERVICE_SYMBOL => self::SYMBOL_REG]
                ];

                $dataCountry = [
                    ['country_id' => 'PR', 'iso2_code' => 'PR', 'iso3_code' => 'PRI']
                ];
                foreach ($dataCountry as $item) {
                    $setup->getConnection()->insertOnDuplicate($setup->getTable('directory_country'), $item);
                }

                foreach ($dataServices as $data) {
                    $setup->getConnection()->insertOnDuplicate($setup->getTable('ups_shipping_services'), $data);
                }
            }
        }
        // Add saturday delivery shipping service
        if (version_compare($context->getVersion(), '2.1.1') < 0) {
            // Get tutorial_simplenews table
            $tableName = $setup->getTable('ups_shipping_services');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $sqlSatDeliveryService = "SELECT id FROM $tableName WHERE `id` = 111";
                $serviceSatDelivery = [];
                $querySatDelivery = $setup->getConnection()->query($sqlSatDeliveryService);
                while ($row = $querySatDelivery->fetch()) {
                    $serviceSatDelivery[] = $row;
                }
                if (empty($serviceSatDelivery)) {
                    $saturdayDeliveryServices = [
                        //PL
                        ['id' => 111, self::COUNTRY_CODE => 'PL', self::SERVICE_TYPE => 'AP',
                            self::SERVICE_KEY => 'UPS_SP_SERV_PL_AP_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_PL_AP_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_PL_AP_STANDARD_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_STANDARD_SAT_DELI,
                            self::RATE_CODE => '11', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 112, self::COUNTRY_CODE => 'PL', self::SERVICE_TYPE => 'AP',
                            self::SERVICE_KEY => 'UPS_SP_SERV_PL_AP_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_PL_AP_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_PL_AP_EXPRESS_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_EXPRESS_SAT_DELI,
                            self::RATE_CODE => '07', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 113, self::COUNTRY_CODE => 'PL', self::SERVICE_TYPE => 'ADD',
                            self::SERVICE_KEY => 'UPS_SP_SERV_PL_ADD_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_PL_ADD_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_PL_ADD_STANDARD_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_STANDARD_SAT_DELI,
                            self::RATE_CODE => '11', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 114, self::COUNTRY_CODE => 'PL', self::SERVICE_TYPE => 'ADD',
                            self::SERVICE_KEY => 'UPS_SP_SERV_PL_ADD_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_PL_ADD_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_PL_ADD_EXPRESS_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_EXPRESS_SAT_DELI,
                            self::RATE_CODE => '07', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        //GB
                        ['id' => 115, self::COUNTRY_CODE => 'GB', self::SERVICE_TYPE => 'AP',
                            self::SERVICE_KEY => 'UPS_SP_SERV_GB_AP_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_GB_AP_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_GB_AP_STANDARD_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_STANDARD_SAT_DELI,
                            self::RATE_CODE => '11', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 116, self::COUNTRY_CODE => 'GB', self::SERVICE_TYPE => 'AP',
                            self::SERVICE_KEY => 'UPS_SP_SERV_GB_AP_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_GB_AP_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_GB_AP_EXPRESS_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_EXPRESS_SAT_DELI,
                            self::RATE_CODE => '07', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 117, self::COUNTRY_CODE => 'GB', self::SERVICE_TYPE => 'ADD',
                            self::SERVICE_KEY => 'UPS_SP_SERV_GB_ADD_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_GB_ADD_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_GB_ADD_STANDARD_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_STANDARD_SAT_DELI,
                            self::RATE_CODE => '11', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 118, self::COUNTRY_CODE => 'GB', self::SERVICE_TYPE => 'ADD',
                            self::SERVICE_KEY => 'UPS_SP_SERV_GB_ADD_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_GB_ADD_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_GB_ADD_EXPRESS_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_EXPRESS_SAT_DELI,
                            self::RATE_CODE => '07', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        //DE
                        ['id' => 119, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'AP',
                            self::SERVICE_KEY => 'UPS_SP_SERV_DE_AP_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_AP_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_DE_AP_STANDARD_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_STANDARD_SAT_DELI,
                            self::RATE_CODE => '11', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 120, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'AP',
                            self::SERVICE_KEY => 'UPS_SP_SERV_DE_AP_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_AP_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_DE_AP_EXPRESS_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_EXPRESS_SAT_DELI,
                            self::RATE_CODE => '07', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 121, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'ADD',
                            self::SERVICE_KEY => 'UPS_SP_SERV_DE_ADD_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_ADD_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_DE_ADD_STANDARD_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_STANDARD_SAT_DELI,
                            self::RATE_CODE => '11', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 122, self::COUNTRY_CODE => 'DE', self::SERVICE_TYPE => 'ADD',
                            self::SERVICE_KEY => 'UPS_SP_SERV_DE_ADD_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_DE_ADD_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_DE_ADD_EXPRESS_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_EXPRESS_SAT_DELI,
                            self::RATE_CODE => '07', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        //FR
                        ['id' => 123, self::COUNTRY_CODE => 'FR', self::SERVICE_TYPE => 'AP',
                            self::SERVICE_KEY => 'UPS_SP_SERV_FR_AP_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_FR_AP_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_FR_AP_STANDARD_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_STANDARD_SAT_DELI,
                            self::RATE_CODE => '11', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 124, self::COUNTRY_CODE => 'FR', self::SERVICE_TYPE => 'AP',
                            self::SERVICE_KEY => 'UPS_SP_SERV_FR_AP_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_FR_AP_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_FR_AP_EXPRESS_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_EXPRESS_SAT_DELI,
                            self::RATE_CODE => '07', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 125, self::COUNTRY_CODE => 'FR', self::SERVICE_TYPE => 'ADD',
                            self::SERVICE_KEY => 'UPS_SP_SERV_FR_ADD_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_FR_ADD_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_FR_ADD_STANDARD_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_STANDARD_SAT_DELI,
                            self::RATE_CODE => '11', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 126, self::COUNTRY_CODE => 'FR', self::SERVICE_TYPE => 'ADD',
                            self::SERVICE_KEY => 'UPS_SP_SERV_FR_ADD_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_FR_ADD_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_FR_ADD_EXPRESS_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_EXPRESS_SAT_DELI,
                            self::RATE_CODE => '07', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        //ES
                        ['id' => 127, self::COUNTRY_CODE => 'ES', self::SERVICE_TYPE => 'AP',
                            self::SERVICE_KEY => 'UPS_SP_SERV_ES_AP_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_ES_AP_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_ES_AP_STANDARD_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_STANDARD_SAT_DELI,
                            self::RATE_CODE => '11', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 128, self::COUNTRY_CODE => 'ES', self::SERVICE_TYPE => 'AP',
                            self::SERVICE_KEY => 'UPS_SP_SERV_ES_AP_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_ES_AP_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_ES_AP_EXPRESS_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_EXPRESS_SAT_DELI,
                            self::RATE_CODE => '07', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 129, self::COUNTRY_CODE => 'ES', self::SERVICE_TYPE => 'ADD',
                            self::SERVICE_KEY => 'UPS_SP_SERV_ES_ADD_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_ES_ADD_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_ES_ADD_STANDARD_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_STANDARD_SAT_DELI,
                            self::RATE_CODE => '11', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 130, self::COUNTRY_CODE => 'ES', self::SERVICE_TYPE => 'ADD',
                            self::SERVICE_KEY => 'UPS_SP_SERV_ES_ADD_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_ES_ADD_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_ES_ADD_EXPRESS_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_EXPRESS_SAT_DELI,
                            self::RATE_CODE => '07', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        //IT
                        ['id' => 131, self::COUNTRY_CODE => 'IT', self::SERVICE_TYPE => 'AP',
                            self::SERVICE_KEY => 'UPS_SP_SERV_IT_AP_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_IT_AP_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_IT_AP_STANDARD_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_STANDARD_SAT_DELI,
                            self::RATE_CODE => '11', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 132, self::COUNTRY_CODE => 'IT', self::SERVICE_TYPE => 'AP',
                            self::SERVICE_KEY => 'UPS_SP_SERV_IT_AP_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_IT_AP_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_IT_AP_EXPRESS_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_EXPRESS_SAT_DELI,
                            self::RATE_CODE => '07', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 133, self::COUNTRY_CODE => 'IT', self::SERVICE_TYPE => 'ADD',
                            self::SERVICE_KEY => 'UPS_SP_SERV_IT_ADD_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_IT_ADD_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_IT_ADD_STANDARD_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_STANDARD_SAT_DELI,
                            self::RATE_CODE => '11', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 134, self::COUNTRY_CODE => 'IT', self::SERVICE_TYPE => 'ADD',
                            self::SERVICE_KEY => 'UPS_SP_SERV_IT_ADD_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_IT_ADD_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_IT_ADD_EXPRESS_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_EXPRESS_SAT_DELI,
                            self::RATE_CODE => '07', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        //NL
                        ['id' => 135, self::COUNTRY_CODE => 'NL', self::SERVICE_TYPE => 'AP',
                            self::SERVICE_KEY => 'UPS_SP_SERV_NL_AP_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_NL_AP_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_NL_AP_STANDARD_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_STANDARD_SAT_DELI,
                            self::RATE_CODE => '11', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 136, self::COUNTRY_CODE => 'NL', self::SERVICE_TYPE => 'AP',
                            self::SERVICE_KEY => 'UPS_SP_SERV_NL_AP_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_NL_AP_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_NL_AP_EXPRESS_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_EXPRESS_SAT_DELI,
                            self::RATE_CODE => '07', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 137, self::COUNTRY_CODE => 'NL', self::SERVICE_TYPE => 'ADD',
                            self::SERVICE_KEY => 'UPS_SP_SERV_NL_ADD_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_NL_ADD_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_NL_ADD_STANDARD_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_STANDARD_SAT_DELI,
                            self::RATE_CODE => '11', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 138, self::COUNTRY_CODE => 'NL', self::SERVICE_TYPE => 'ADD',
                            self::SERVICE_KEY => 'UPS_SP_SERV_NL_ADD_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_NL_ADD_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_NL_ADD_EXPRESS_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_EXPRESS_SAT_DELI,
                            self::RATE_CODE => '07', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        //BE
                        ['id' => 139, self::COUNTRY_CODE => 'BE', self::SERVICE_TYPE => 'AP',
                            self::SERVICE_KEY => 'UPS_SP_SERV_BE_AP_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_BE_AP_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_BE_AP_STANDARD_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_STANDARD_SAT_DELI,
                            self::RATE_CODE => '11', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 140, self::COUNTRY_CODE => 'BE', self::SERVICE_TYPE => 'AP',
                            self::SERVICE_KEY => 'UPS_SP_SERV_BE_AP_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_BE_AP_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_BE_AP_EXPRESS_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_EXPRESS_SAT_DELI,
                            self::RATE_CODE => '07', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 141, self::COUNTRY_CODE => 'BE', self::SERVICE_TYPE => 'ADD',
                            self::SERVICE_KEY => 'UPS_SP_SERV_BE_ADD_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_BE_ADD_STANDARD_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_BE_ADD_STANDARD_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_STANDARD_SAT_DELI,
                            self::RATE_CODE => '11', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG],
                        ['id' => 142, self::COUNTRY_CODE => 'BE', self::SERVICE_TYPE => 'ADD',
                            self::SERVICE_KEY => 'UPS_SP_SERV_BE_ADD_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_DELIVERY => 'UPS_DELI_BE_ADD_EXPRESS_SAT_DELI',
                            self::SERVICE_KEY_VAL => 'UPS_DELI_BE_ADD_EXPRESS_SAT_DELI_VAL',
                            self::SERVICE_NAME => self::UPS_EXPRESS_SAT_DELI,
                            self::RATE_CODE => '07', self::TIN_T_CODE => '0', self::SERVICE_SELECTED => 0,
                            self::SERVICE_SYMBOL => self::SYMBOL_REG]
                    ];
                    foreach ($saturdayDeliveryServices as $data) {
                        $setup->getConnection()->insertOnDuplicate($tableName, $data);
                    }
                }
            }
        }

        $setup->endSetup();
    }
}
