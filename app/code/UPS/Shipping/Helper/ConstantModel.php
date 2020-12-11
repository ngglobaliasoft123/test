<?php
/**
 * ConstantModel file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Helper;
/**
 * ConstantModel class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class ConstantModel
{
    const SALES_ORDER = 'sales_order';
    const ID_MAGENTO_ENTITY_ID = 'op.order_id_magento = so.entity_id';
    const CREATED_AT = 'created_at';
    const INCREMENT_ID = 'increment_id';
    const ENTITY_ID = 'entity_id';
    const SALES_ORDER_ADDRESS = 'sales_order_address';
    const SHIPPING_ADDRESS_ID_ENTITY_ID =  'so.shipping_address_id = ad.entity_id';
    const STREET = 'street';
    const COUNTRY_ID_AS_NAMECOUNTRY = 'country_id as nameCountry';
    const UPS_SHIPPING_SERVICES = 'ups_shipping_services';
    const SERVICE_TYPE = 'service_type';
    const SERVICE_KEY = 'service_key';
    const SERVICE_SYMBOL = 'service_symbol';
    const COUNTRY_CODE = 'country_code';
    const SERVICE_NAME = 'service_name';
    const RATE_CODE = 'rate_code';
    const SERVICE_ID = 'id';
    const SALES_ORDER_PAYMENT = 'sales_order_payment';
    const METHOD = 'method';
    const SHIPPING_METHOD = 'so.shipping_method = ?';
    const CREATED_AT_DATE = 'created_at_date';
    const CREATED_AT_TIME = 'created_at_time';
    const SHIPPING_SERVICE = 'shipping_service';
    const DELIVERY_ADDRESS = 'delivery_address';
    const STATUS = 'status';
    const Y_M_D = "Y-m-d";
    // service
    const COUNTRY_CODE_VALUE = 'sv.country_code = cf.value';
    const CORE_CONFIG_DATA = 'core_config_data';
    const CF_PATH = 'cf.path = ?';
    const SV_SERVICE_TYPE = 'sv.service_type = ?';
    const SV_SERVICE_SELECTED = 'sv.service_selected = ?';
    // model shipment
    const UPS_SHIPPING_TRACKING = 'ups_shipping_tracking';
    const SHIPMENT_NUMBER = 'shipment_number';
    const TRACKING_NUMBER = 'tracking_number';
    const PACKAGE_DETAIL = 'package_detail';
    const UPS_SHIPPING_ORDERS = 'ups_shipping_orders';
    const AP_ADDRESS1 = 'ap_address1';
    const AP_ADDRESS2 = 'ap_address2';
    const AP_ADDRESS3 = 'ap_address3';
    const AP_CITY = 'ap_city';
    const ORDER_ID_MAGENTO_ENTITY_ID = 'uso.order_id_magento = so.entity_id';
    const ID_SHIPMENT_ID = 's.id = uso.shipment_id';
    const USO_STATUS = 'uso.status = 2';
    const SHIPMENT_ID = 'shipment_id';
    const ORDER_ID = 'order_id';
    // retry API
    const COUNT_RETRY = 'count_retry';
    // service
    const UPS_SHIPPING_SERVICE = 'ups_shipping_service';
    // shipment
    const UPS_SHIPPING_SHIPMENT = 'ups_shipping_shipment';
}
