<?php
/**
 * CommonAPI file
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
 * CommonAPI class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
Class CommonAPI
{
    public $country;
    public $language;
    public $currency;
    public $orderValue;

    /**
     * CommonAPI languageCodeForCountry
     * convert country to language code
     *
     * @param string $languageCode //The languageCode
     *
     * @return boolean
     */
    public static function languageCodeForCountry($languageCode)
    {
        $availabelCode = ['en_AT','de_AT','en_BE','nl_BE','fr_BE','en_CZ','cs_CZ','en_DK','da_DK','en_FI','fi_FI',
        'en_FR','fr_FR','en_DE','de_DE','en_GR','el_GR','en_HU','hu_HU','en_IE','en_IT','it_IT','en_LU','fr_LU',
        'en_NL','nl_NL','en_NO','no_NO','en_PL','pl_PL','en_PT','pt_PT','en_RU','ru_RU','en_SI','en_ES','es_ES',
        'en_SE','sv_SE','en_CH','fr_CH','de_CH','en_GB'];

        if (in_array($languageCode, $availabelCode)) {
            return $languageCode;
        }
        return 'en_GB';
    }
}
