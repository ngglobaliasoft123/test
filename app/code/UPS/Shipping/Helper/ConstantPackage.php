<?php
/**
 * ConstantPackage file
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
 * ConstantPackage class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class ConstantPackage
{
    //use on block, controller \UPS\Shipping\Helper\Config::carriers_UPS_SHIPPING_COUNTRY_CODE
    const PACKAGE_ID = 'package_id';
    const NAMEPACKAGE = 'namePackage';
    const PACKAGE_NAME = 'package_name';
    const WEIGHT = 'weight';
    const WIDTH = 'width';
    const LENGTH = 'length';
    const HEIGHT = 'height';
    const UNIT_WEIGHT = 'unit_weight';
    const UNIT_DIMENSION = 'unit_dimension';
    const UNIT_DIMENSION_LENGTH = 'lengthunit';
    const UNIT_DIMENSION_CM = 'cm';
    const NAMEPACKAGEPOPUP = 'namePackagePopup';
    const NUMBERPACKAGE = 'package_number';
}
