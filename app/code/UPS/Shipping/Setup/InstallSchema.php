<?php
/**
 * InstallSchema file
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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
/**
 * InstallSchema class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class InstallSchema implements InstallSchemaInterface
{
    const NULLABLE = 'nullable';
    const IDENTITY = 'identity';
    const PRIMARY = 'primary';
    const UNSIGNED = 'unsigned';
    const NULLABLE_TRUE = 'nullable => true';
    const EMAIL = 'email';
    const COUNTRY = 'Country';
    const COUNTRYSMALL = 'country';
    const STATUS = 'status';

    /**
     * InstallSchema install
     * calculate shipping service
     *
     * @param string $setup   //The setup
     * @param string $context //The context
     *
     * @return null
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if(true){
            $table = $installer->getConnection()->newTable($installer->getTable('ups_shipping_package_default'))
                ->addColumn('package_id', Table::TYPE_INTEGER, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true, self::UNSIGNED => true], 'Package ID')
                ->addColumn('package_name', Table::TYPE_TEXT, 255, ['nullable => false'], 'Package Name')
                ->addColumn('weight', Table::TYPE_FLOAT, '10,2', [], 'Weight')
                ->addColumn('unit_weight', Table::TYPE_TEXT, 30, [], 'Unit weight')
                ->addColumn('length', Table::TYPE_FLOAT, '10,2', [], 'Length')
                ->addColumn('width', Table::TYPE_FLOAT, '10,2', [], 'Width')
                ->addColumn('height', Table::TYPE_FLOAT, '10,2', [], 'Height')
                ->addColumn('unit_dimension', Table::TYPE_TEXT, 30, [], 'Unit dimension')
                ->addColumn('package_number', Table::TYPE_INTEGER, 11, [], 'Package number')
                ->addIndex(
                    $setup->getIdxName('ups_shipping_package_default', ['package_id']),
                    ['package_id']
                );
            $installer->getConnection()->createTable($table);
        }

        if(true){
            $table = $installer->getConnection()->newTable($installer->getTable('ups_shipping_fallback_rates'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true, self::UNSIGNED => true], 'backup rates')
                ->addColumn('service_type', Table::TYPE_TEXT, 20, [], 'service type')
                ->addColumn('service_id', Table::TYPE_INTEGER, 11, [], 'Service ID')
                ->addColumn('fallback_rate', Table::TYPE_FLOAT, '10,2', [], 'Fallback Rate')
                ->addIndex(
                    $setup->getIdxName('ups_shipping_fallback_rates', ['id']),
                    ['id']
                );
            $installer->getConnection()->createTable($table);
        }

        if(true){
            $table = $installer->getConnection()->newTable($installer->getTable('ups_shipping_product_dimension'))
                ->addColumn('package_id', Table::TYPE_INTEGER, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true, self::UNSIGNED => true], 'Package ID')
                ->addColumn('package_name', Table::TYPE_TEXT, 255, ['nullable => false'], 'Package Name')
                ->addColumn('weight', Table::TYPE_FLOAT, '10,2', [], 'Weight')
                ->addColumn('unit_weight', Table::TYPE_TEXT, 30, [], 'Unit weight')
                ->addColumn('length', Table::TYPE_FLOAT, '10,2', [], 'Length')
                ->addColumn('width', Table::TYPE_FLOAT, '10,2', [], 'Width')
                ->addColumn('height', Table::TYPE_FLOAT, '10,2', [], 'Height')
                ->addColumn('unit_dimension', Table::TYPE_TEXT, 30, [], 'Unit dimension')
                ->addIndex(
                    $setup->getIdxName('ups_shipping_product_dimension', ['package_id']),
                    ['package_id']
                );
            $installer->getConnection()->createTable($table);
        }

        if(true){
            $table = $installer->getConnection()->newTable($installer->getTable('ups_shipping_license'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true, self::UNSIGNED => true], 'ID')
                ->addColumn('AccessLicenseText', Table::TYPE_TEXT, '', [self::NULLABLE_TRUE], 'Api Access 1')
                ->addColumn('Username', Table::TYPE_TEXT, '127', [self::NULLABLE_TRUE], 'Api Account 1')
                ->addColumn('Password', Table::TYPE_TEXT, '127', [self::NULLABLE_TRUE], 'Api Account 1')
                ->addColumn('AccessLicenseNumber', Table::TYPE_TEXT, '127', [self::NULLABLE_TRUE], 'Api Access 2')
                ->addIndex(
                    $setup->getIdxName('ups_shipping_license', ['id']),
                    ['id']
                );
            $installer->getConnection()->createTable($table);
        }

        if(true){
            $table = $installer->getConnection()->newTable($installer->getTable('ups_shipping_account'))
                ->addColumn('account_id', Table::TYPE_INTEGER, null, [ self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true, self::UNSIGNED => true], 'Account ID')
                ->addColumn('title', Table::TYPE_TEXT, 10, [self::NULLABLE_TRUE], 'Title')
                ->addColumn('fullname', Table::TYPE_TEXT, 100, [self::NULLABLE_TRUE], 'Fullname')
                ->addColumn('company', Table::TYPE_TEXT, 255, [self::NULLABLE_TRUE], 'Company')
                ->addColumn(self::EMAIL, Table::TYPE_TEXT, 100, [self::NULLABLE_TRUE], self::EMAIL)
                ->addColumn('phone_number', Table::TYPE_TEXT, 100, [self::NULLABLE_TRUE], 'phone_number')
                ->addColumn('address_type', Table::TYPE_TEXT, 255, [self::NULLABLE_TRUE], 'address_type')
                ->addColumn('address_1', Table::TYPE_TEXT, 255, [self::NULLABLE_TRUE], 'Address_1')
                ->addColumn('address_2', Table::TYPE_TEXT, 255, [self::NULLABLE_TRUE], 'Address_2')
                ->addColumn('address_3', Table::TYPE_TEXT, 255, [self::NULLABLE_TRUE], 'Address_3')
                ->addColumn('post_code', Table::TYPE_TEXT, 50, [self::NULLABLE_TRUE], 'Post code')
                ->addColumn('city', Table::TYPE_TEXT, 255, [self::NULLABLE_TRUE], 'City')
                ->addColumn(self::COUNTRYSMALL, Table::TYPE_TEXT, 10, [self::NULLABLE_TRUE], self::COUNTRY)
                ->addColumn('account_type', Table::TYPE_INTEGER, 10, [self::NULLABLE_TRUE], 'Account type(0,1,2)')
                ->addColumn('ups_account_name', Table::TYPE_TEXT, 255, [self::NULLABLE_TRUE], 'UPS account name')
                ->addColumn('ups_account_number', Table::TYPE_TEXT, 255, [self::NULLABLE_TRUE], 'UPS account number')
                ->addColumn('ups_invoice_number', Table::TYPE_TEXT, 255, [self::NULLABLE_TRUE], 'UPS invoice number')
                ->addColumn('ups_account_account', Table::TYPE_TEXT, 255, [self::NULLABLE_TRUE], 'UPS invoice amount')
                ->addColumn('ups_currency', Table::TYPE_TEXT, 255, [self::NULLABLE_TRUE], 'UPS Currency')
                ->addColumn('ups_invoice_date', Table::TYPE_DATETIME, 255, [self::NULLABLE_TRUE], 'UPS invoice date')
                ->addColumn('deviceIdentity', Table::TYPE_TEXT, '', [self::NULLABLE_TRUE], 'Device identity')
                ->addColumn('account_default', Table::TYPE_INTEGER, 11, [self::NULLABLE_TRUE], 'Account default')
                ->addIndex(
                    $setup->getIdxName('ups_shipping_license', ['account_id']),
                    ['account_id']
                );
            $installer->getConnection()->createTable($table);
        }

        if(true){
            $table = $installer->getConnection()->newTable($installer->getTable('ups_shipping_accessorial'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true, self::UNSIGNED => true], 'ID')
                ->addColumn('accessorial_key', Table::TYPE_TEXT, '100', [], 'Key')
                ->addColumn('accessorial_name', Table::TYPE_TEXT, '100', [], 'Name')
                ->addColumn('accessorial_code', Table::TYPE_TEXT, '20', [], 'Code')
                ->addColumn('show_config', Table::TYPE_INTEGER, '1', [], 'Show Config')
                ->addColumn('show_shipping', Table::TYPE_INTEGER, '1', [], 'Show Shipping');
            $installer->getConnection()->createTable($table);
        }

        if(true){
            $table = $installer->getConnection()->newTable($installer->getTable('ups_shipping_logs_api'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true, self::UNSIGNED => true], 'ID')
                ->addColumn('method', Table::TYPE_TEXT, '255', [self::NULLABLE_TRUE], 'Method')
                ->addColumn('full_uri', Table::TYPE_TEXT, '255', [self::NULLABLE_TRUE], 'URI')
                ->addColumn('request', Table::TYPE_TEXT, '', [self::NULLABLE_TRUE], 'Request')
                ->addColumn('response', Table::TYPE_TEXT, '', [self::NULLABLE_TRUE], 'Response')
                ->addColumn('time_request', Table::TYPE_DATETIME, '', [], 'Time request')
                ->addColumn('time_response', Table::TYPE_DATETIME, '', [], 'Time response');
            $installer->getConnection()->createTable($table);
        }

        if(true){
            $table = $installer->getConnection()->newTable($installer->getTable('ups_shipping_retry_api'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true, self::UNSIGNED => true], 'ID')
                ->addColumn('method', Table::TYPE_TEXT, '127', [self::NULLABLE_TRUE], 'Method')
                ->addColumn('datarequest', Table::TYPE_TEXT, '', [self::NULLABLE_TRUE], 'Data request')
                ->addColumn('response', Table::TYPE_TEXT, '', [self::NULLABLE_TRUE], 'Reponse')
                ->addColumn('count_retry', Table::TYPE_INTEGER, '', [self::NULLABLE_TRUE], 'Count number send api')
                ->addColumn('date_created', Table::TYPE_DATETIME, '', [self::NULLABLE_TRUE], 'Date created api');
            $installer->getConnection()->createTable($table);
        }

        if(true){
            $table = $installer->getConnection()->newTable($installer->getTable('ups_shipping_orders'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true, self::UNSIGNED => true], 'ID')
                ->addColumn('order_id_magento', Table::TYPE_INTEGER, '11', [self::NULLABLE_TRUE], 'Order Id Magento')
                ->addColumn('shipping_service', Table::TYPE_INTEGER, '11', [self::NULLABLE_TRUE], 'Service')
                ->addColumn('accessorial_service', Table::TYPE_TEXT, '1023', [self::NULLABLE_TRUE], 'Accessorial')
                ->addColumn('shipment_id', Table::TYPE_INTEGER, '11', [self::NULLABLE_TRUE], 'Shipment Id')
                ->addColumn('quote_id', Table::TYPE_INTEGER, '11', [self::NULLABLE_TRUE], 'Quote Id')
                ->addColumn(self::STATUS, Table::TYPE_INTEGER, '1', [self::NULLABLE_TRUE], 'Order status')
                ->addColumn('ap_id', Table::TYPE_TEXT, '20', [self::NULLABLE_TRUE], 'Access Point Id')
                ->addColumn('ap_name', Table::TYPE_TEXT, '255', [self::NULLABLE_TRUE], 'Access Point Name')
                ->addColumn('ap_address1', Table::TYPE_TEXT, '128', [self::NULLABLE_TRUE], 'Access Point Address 1')
                ->addColumn('ap_address2', Table::TYPE_TEXT, '128', [self::NULLABLE_TRUE], 'Access Point Address 2')
                ->addColumn('ap_address3', Table::TYPE_TEXT, '128', [self::NULLABLE_TRUE], 'Access Point Address 3')
                ->addColumn('ap_state', Table::TYPE_TEXT, '50', [self::NULLABLE_TRUE], 'Access Point state')
                ->addColumn('ap_postcode', Table::TYPE_TEXT, '50', [self::NULLABLE_TRUE], 'Access Point post code')
                ->addColumn('ap_city', Table::TYPE_TEXT, '64', [self::NULLABLE_TRUE], 'Access Point city')
                ->addColumn('ap_country', Table::TYPE_TEXT, '20', [self::NULLABLE_TRUE], 'Access Point country')
                ->addColumn('cod', Table::TYPE_INTEGER, '11', [self::NULLABLE_TRUE], 'COD')
                ->addColumn('created_at', Table::TYPE_DATETIME, '', [self::NULLABLE_TRUE], 'Date create')
                ->addColumn('archived_at', Table::TYPE_DATETIME, '', [self::NULLABLE_TRUE], 'Date archive');
            $installer->getConnection()->createTable($table);
        }

        if(true){
            $table = $installer->getConnection()->newTable($installer->getTable('ups_shipping_services'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true, self::UNSIGNED => true], 'ID')
                ->addColumn('country_code', Table::TYPE_TEXT, '2', [self::NULLABLE_TRUE], self::COUNTRY)
                ->addColumn('service_type', Table::TYPE_TEXT, '20', [self::NULLABLE_TRUE], 'Type(AP-ADD)')
                ->addColumn('service_key', Table::TYPE_TEXT, '100', [self::NULLABLE_TRUE], 'Key')
                ->addColumn('service_key_delivery', Table::TYPE_TEXT, '100', [self::NULLABLE_TRUE], 'Delivery key')
                ->addColumn('service_key_val', Table::TYPE_TEXT, '100', [self::NULLABLE_TRUE], 'Value')
                ->addColumn('service_name', Table::TYPE_TEXT, '100', [self::NULLABLE_TRUE], 'Name')
                ->addColumn('rate_code', Table::TYPE_TEXT, '20', [self::NULLABLE_TRUE], 'Rate code')
                ->addColumn('tin_t_code', Table::TYPE_TEXT, '20', [self::NULLABLE_TRUE], 'Tin T Code')
                ->addColumn('service_selected', Table::TYPE_INTEGER, '1', [self::NULLABLE_TRUE], 'Selected')
                ->addColumn('service_symbol', Table::TYPE_TEXT, '10', [self::NULLABLE_TRUE], 'service_symbol');
            $installer->getConnection()->createTable($table);
        }

        if(true){
            $table = $installer->getConnection()->newTable($installer->getTable('ups_shipping_shipments'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true, self::UNSIGNED => true], 'ID')
                ->addColumn('shipment_number', Table::TYPE_TEXT, '50', [self::NULLABLE_TRUE], 'Shipment number')
                ->addColumn('date_created', Table::TYPE_DATETIME, '', [self::NULLABLE_TRUE], 'Date create')
                ->addColumn(self::STATUS, Table::TYPE_TEXT, '255', [self::NULLABLE_TRUE], 'Shipment status')
                ->addColumn('cod', Table::TYPE_INTEGER, '1', [self::NULLABLE_TRUE], 'COD')
                ->addColumn('shipping_fee', Table::TYPE_TEXT, '30', [self::NULLABLE_TRUE], 'Fee')
                ->addColumn('order_value', Table::TYPE_TEXT, '30', [self::NULLABLE_TRUE], 'Fee Order')
                ->addColumn('accessorial_service', Table::TYPE_TEXT, '1023', [self::NULLABLE_TRUE], 'Accessorial')
                ->addColumn('shipping_service', Table::TYPE_INTEGER, '11', [self::NULLABLE_TRUE], 'Service')
                ->addColumn('ap_id', Table::TYPE_TEXT, '20', [self::NULLABLE_TRUE], 'Access Point Id')
                ->addColumn('name', Table::TYPE_TEXT, '255', [self::NULLABLE_TRUE], 'Name')
                ->addColumn('address1', Table::TYPE_TEXT, '127', [self::NULLABLE_TRUE], 'Address 1')
                ->addColumn('address2', Table::TYPE_TEXT, '127', [self::NULLABLE_TRUE], 'Address 2')
                ->addColumn('address3', Table::TYPE_TEXT, '127', [self::NULLABLE_TRUE], 'Address 3')
                ->addColumn('state', Table::TYPE_TEXT, '15', [self::NULLABLE_TRUE], 'State')
                ->addColumn('postcode', Table::TYPE_TEXT, '15', [self::NULLABLE_TRUE], 'Postcode')
                ->addColumn('city', Table::TYPE_TEXT, '63', [self::NULLABLE_TRUE], 'City')
                ->addColumn(self::COUNTRYSMALL, Table::TYPE_TEXT, '255', [self::NULLABLE_TRUE], self::COUNTRY)
                ->addColumn('phone', Table::TYPE_TEXT, '31', [self::NULLABLE_TRUE], 'Phone')
                ->addColumn(self::EMAIL, Table::TYPE_TEXT, '255', [self::NULLABLE_TRUE], 'Email');
            $installer->getConnection()->createTable($table);
        }
        $this->installAdditional($installer);
        $installer->endSetup();
    }

    /**
     * InstallSchema installAdditional
     *
     * @param string $installer //The installer
     *
     * @return array $data
     */
    public function installAdditional($installer)
    {
        if(true){
            $table = $installer->getConnection()->newTable($installer->getTable('ups_shipping_delivery_rates'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true, self::UNSIGNED => true], 'ID')
                ->addColumn('service_id', Table::TYPE_INTEGER, '11', [self::NULLABLE_TRUE], 'Service ID')
                ->addColumn('rate_type', Table::TYPE_TEXT, '20', [self::NULLABLE_TRUE], 'Rate type')
                ->addColumn('min_order_value', Table::TYPE_FLOAT, '10,2', [self::NULLABLE_TRUE], 'Min order value')
                ->addColumn('delivery_rate', Table::TYPE_FLOAT, '10,2', [self::NULLABLE_TRUE], 'Delivery rate');
            $installer->getConnection()->createTable($table);
        }

        if(true){
            $arrTable = [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true, self::UNSIGNED => true];
            $table = $installer->getConnection()->newTable($installer->getTable('ups_shipping_tracking'))
                ->addColumn('id', Table::TYPE_INTEGER, null, $arrTable, 'ID')
                ->addColumn('tracking_number', Table::TYPE_TEXT, '255', [self::NULLABLE_TRUE], 'Tracking Number')
                ->addColumn('shipment_number', Table::TYPE_TEXT, '255', [self::NULLABLE_TRUE], 'Shipment Number')
                ->addColumn('order_id', Table::TYPE_INTEGER, '11', [self::NULLABLE_TRUE], 'Order Id')
                ->addColumn(self::STATUS, Table::TYPE_INTEGER, '11', [self::NULLABLE_TRUE], 'Status')
                ->addColumn('package_detail', Table::TYPE_TEXT, '255', [self::NULLABLE_TRUE], 'Package Detail');
            $installer->getConnection()->createTable($table);
        }
    }
}
