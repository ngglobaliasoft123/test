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

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
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
class UpgradeSchema implements UpgradeSchemaInterface
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
    const SYMBOL_REG = '&reg;';
    const UPS_EXPEDITED = 'UPS Expedited';
    const UPS_EXPRESS_SAVER = 'UPS Express Saver';
    const UPS_EXPRESS = 'UPS Express';
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

    const IDENTITY = 'identity';
    const PRIMARY = 'primary';
    const UNSIGNED = 'unsigned';
    const NULLABLE_TRUE = 'nullable => true';
    const EMAIL = 'email';
    const COUNTRY = 'Country';
    const COUNTRYSMALL = 'country';

    /**
     * UpgradeSchema upgrade
     * calculate shipping service
     *
     * @param string $setup   //The setup
     * @param string $context //The context
     *
     * @return array $data
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.1.5') < 0) {
            if (!$installer->tableExists('ups_shipping_product_dimension')) {
                $table = $installer->getConnection()->newTable($installer->getTable('ups_shipping_product_dimension'))
                    ->addColumn('package_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true, self::UNSIGNED => true], 'Package ID')
                    ->addColumn('package_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable => false'], 'Package Name')
                    ->addColumn('weight', \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT, '10,2', ['length' => '10,2'], 'Weight')
                    ->addColumn('unit_weight', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 30, [], 'Unit weight')
                    ->addColumn('length', \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT, '10,2', ['length' => '10,2'], 'Length')
                    ->addColumn('width', \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT, '10,2', ['length' => '10,2'], 'Width')
                    ->addColumn('height', \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT, '10,2', ['length' => '10,2'], 'Height')
                    ->addColumn('unit_dimension', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 30, [], 'Unit dimension')
                    ->addIndex(
                        $setup->getIdxName('ups_shipping_product_dimension', ['package_id']),
                        ['package_id']
                    );
                $installer->getConnection()->createTable($table);
            }
            if (!$installer->tableExists('ups_shipping_fallback_rates')) {
                $table = $installer->getConnection()->newTable($installer->getTable('ups_shipping_fallback_rates'))
                ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true, self::UNSIGNED => true], 'backuprate ID')
                    ->addColumn('service_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 20, [], 'service type')
                    ->addColumn('service_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [], 'Service ID')
                    ->addColumn('fallback_rate', \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT, '10,2', ['length' => '10,2'], 'Fallback Rate')
                    ->addIndex(
                        $setup->getIdxName('ups_shipping_fallback_rates', ['id']),
                        ['id']
                    );
                $installer->getConnection()->createTable($table);
            }

            if ($installer->getConnection()->tableColumnExists('ups_shipping_package_default', 'weight')) {
                $definition = [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    'length' => '10,2',
                    'comment' => 'Weight'
                ];
                $installer->getConnection()->modifyColumn(
                    $setup->getTable('ups_shipping_package_default'),
                    'weight',
                    $definition
                );
            }

            if ($installer->getConnection()->tableColumnExists('ups_shipping_package_default', 'length')) {
                $definition = [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    'length' => '10,2',
                    'comment' => 'Length'
                ];
                $installer->getConnection()->modifyColumn(
                    $setup->getTable('ups_shipping_package_default'),
                    'length',
                    $definition
                );
            }

            if ($installer->getConnection()->tableColumnExists('ups_shipping_package_default', 'width')) {
                $definition = [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    'length' => '10,2',
                    'comment' => 'Width'
                ];
                $installer->getConnection()->modifyColumn(
                    $setup->getTable('ups_shipping_package_default'),
                    'width',
                    $definition
                );
            }

            if ($installer->getConnection()->tableColumnExists('ups_shipping_package_default', 'height')) {
                $definition = [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    'length' => '10,2',
                    'comment' => 'Height'
                ];
                $installer->getConnection()->modifyColumn(
                    $setup->getTable('ups_shipping_package_default'),
                    'height',
                    $definition
                );
            }

            if ($installer->getConnection()->tableColumnExists('ups_shipping_product_dimension', 'weight')) {
                $definition = [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    'length' => '10,2',
                    'comment' => 'Weight'
                ];
                $installer->getConnection()->modifyColumn(
                    $setup->getTable('ups_shipping_product_dimension'),
                    'weight',
                    $definition
                );
            }

            if ($installer->getConnection()->tableColumnExists('ups_shipping_product_dimension', 'length')) {
                $definition = [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    'length' => '10,2',
                    'comment' => 'Length'
                ];
                $installer->getConnection()->modifyColumn(
                    $setup->getTable('ups_shipping_product_dimension'),
                    'length',
                    $definition
                );
            }

            if ($installer->getConnection()->tableColumnExists('ups_shipping_product_dimension', 'width')) {
                $definition = [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    'length' => '10,2',
                    'comment' => 'Width'
                ];
                $installer->getConnection()->modifyColumn(
                    $setup->getTable('ups_shipping_product_dimension'),
                    'width',
                    $definition
                );
            }

            if ($installer->getConnection()->tableColumnExists('ups_shipping_product_dimension', 'height')) {
                $definition = [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    'length' => '10,2',
                    'comment' => 'Height'
                ];
                $installer->getConnection()->modifyColumn(
                    $setup->getTable('ups_shipping_product_dimension'),
                    'height',
                    $definition
                );
            }

            if ($installer->getConnection()->tableColumnExists('ups_shipping_fallback_rates', 'fallback_rate')) {
                $definition = [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    'length' => '10,2',
                    'comment' => 'Fallback rate'
                ];
                $installer->getConnection()->modifyColumn(
                    $setup->getTable('ups_shipping_fallback_rates'),
                    'fallback_rate',
                    $definition
                );
            }
        }

        //add column business_name
        $tableName = $setup->getTable('ups_shipping_package_default');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $columns = [
                'package_number' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    self::NULLABLE => true,
                    self::COMMENT => 'Number Package',
                ],
            ];

            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }
        //add column business_name
        $tableName = $setup->getTable('ups_shipping_orders');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $columns = [
                'ap_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    20,
                    self::NULLABLE => true,
                    self::COMMENT => 'Access Point Id',
                ],
                'ap_country' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    20,
                    self::NULLABLE => true,
                    self::COMMENT => 'Access Point country',
                ],
                'cod' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    self::NULLABLE => true,
                    self::COMMENT => 'COD',
                ],
                'quote_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    self::NULLABLE => true,
                    self::COMMENT => 'Quote Id',
                ],
                'accessorial_service' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    1023,
                    self::NULLABLE => true,
                    self::COMMENT => 'Accessorial',
                ],
                'package' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    1023,
                    self::NULLABLE => true,
                    self::COMMENT => 'Shipping Package',
                ],
            ];

            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
                //see other function at: vendor\magento\framework\DB\Adapter\Pdo\Mysql.php
            }
        }
        //add column quote_id, app_country, cod
        $tableName = $setup->getTable('ups_shipping_orders');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $columns = [
                'quote_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    self::NULLABLE => false,
                    self::COMMENT => 'Quote Id',
                ],
                'ap_country' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    10,
                    self::NULLABLE => true,
                    self::COMMENT => 'Access Point country',
                ],
                'cod' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    1,
                    self::NULLABLE => true,
                    self::COMMENT => 'cod',
                ],
                'shipping_service' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    self::NULLABLE => true,
                    self::COMMENT => 'Service',
                ],
                'ap_address3' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    128,
                    self::NULLABLE => true,
                    self::COMMENT => 'Access Point Address 3',
                ],
                'created_at' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    '',
                    self::NULLABLE => true,
                    self::COMMENT => 'Date Order',
                ],
                'archived_at' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    '',
                    self::NULLABLE => true,
                    self::COMMENT => 'Date Archive',
                ]
            ];

            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
                //see other function at: vendor\magento\framework\DB\Adapter\Pdo\Mysql.php
            }
        }
        //add column package_detail
        $tableName = $setup->getTable('ups_shipping_tracking');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $columns = [
                'package_detail' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    self::NULLABLE => true,
                    self::COMMENT => 'Package Detail',
                ]
            ];

            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
                //see other function at: vendor\magento\framework\DB\Adapter\Pdo\Mysql.php
            }
        }

        //add column ups_shipping_shipments
        $tableName = $setup->getTable('ups_shipping_shipments');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $columns = [
                'order_value' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    10,
                    self::NULLABLE => true,
                    self::COMMENT => 'Fee Order',
                ],
                'accessorial_service' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    1023,
                    self::NULLABLE => true,
                    self::COMMENT => 'Accessorial',
                ],
                'shipping_service' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    self::NULLABLE => true,
                    self::COMMENT => '    Service',
                ],
                'name' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    self::NULLABLE => true,
                    self::COMMENT => 'Name',
                ],
                'address1' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    127,
                    self::NULLABLE => true,
                    self::COMMENT => 'Address 1',
                ],
                'address2' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    127,
                    self::NULLABLE => true,
                    self::COMMENT => 'Address 2',
                ],
                'address3' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    127,
                    self::NULLABLE => true,
                    self::COMMENT => 'Address 3',
                ],
                'state' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    15,
                    self::NULLABLE => true,
                    self::COMMENT => 'State',
                ],
                'postcode' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    15,
                    self::NULLABLE => true,
                    self::COMMENT => 'Postcode',
                ],
                'city' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    63,
                    self::NULLABLE => true,
                    self::COMMENT => 'City',
                ],
                'country' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    self::NULLABLE => true,
                    self::COMMENT => 'Country',
                ],
                'phone' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    31,
                    self::NULLABLE => true,
                    self::COMMENT => 'Phone',
                ],
                'email' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    self::NULLABLE => true,
                    self::COMMENT => 'Email',
                ],
                'status' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    self::NULLABLE => true,
                    self::COMMENT => 'Shipment status',
                ],
                'shipping_fee' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    30,
                    self::NULLABLE => true,
                    self::COMMENT => 'Fee',
                ],
                'order_value' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    30,
                    self::NULLABLE => true,
                    self::COMMENT => 'Fee Order',
                ],
                'date_created' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    '',
                    self::NULLABLE => true,
                    self::COMMENT => 'Date Shipment',
                ]
            ];

            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
                //see other function at: vendor\magento\framework\DB\Adapter\Pdo\Mysql.php
            }
        }

        $this->upgradeAdditional($setup);

        $setup->endSetup();
    }

    /**
     * UpgradeSchema upgrade
     *
     * @param string $setup //The setup
     *
     * @return null
     */
    public function upgradeAdditional($setup)
    {
        // add column symbol
        $tableName = $setup->getTable('ups_shipping_services');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $columns = [
                'service_symbol' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    10,
                    self::NULLABLE => false,
                    self::COMMENT => 'Package Detail',
                    'default' => ''
                ]
            ];
            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
                //see other function at: vendor\magento\framework\DB\Adapter\Pdo\Mysql.php
            }
        }

        // add order id
        $tableName = $setup->getTable('ups_shipping_tracking');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $columns = [
                'order_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    self::NULLABLE => false,
                    self::COMMENT => 'Order Id'
                ]
            ];
            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
                //see other function at: vendor\magento\framework\DB\Adapter\Pdo\Mysql.php
            }
        }

        // add device identity and state province code
        $tableName = $setup->getTable('ups_shipping_account');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $columns = [
                'state_province_code' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    15,
                    self::NULLABLE => true,
                    self::COMMENT => 'State Province Code'
                ],
                'deviceIdentity' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '',
                    self::NULLABLE => true,
                    self::COMMENT => 'Device identity'
                ]
            ];
            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }
    }
}
