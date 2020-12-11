<?php
/**
 * SortedServices file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Model;
/**
 * SortedServices class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class SortedServices extends \Magento\Framework\Model\AbstractModel
{

    /**
     * SortedServices getListSortedServicesByCountryCode
     *
     * @param $countryCode //Country Code
     * @param $serviceType //Service Type
     *
     * @return array $services
     */
    public function getListSortedServicesByCountryCode($countryCode, $serviceType = null)
    {
        $services = array();

        $services['PL']['AP'][] = 'UPS_SP_SERV_PL_AP_AP_ECONOMY';
        $services['PL']['AP'][] = 'UPS_SP_SERV_PL_AP_STANDARD';
        $services['PL']['AP'][] = 'UPS_SP_SERV_PL_AP_STANDARD_SAT_DELI';
        $services['PL']['AP'][] = 'UPS_SP_SERV_PL_AP_EXPEDITED';
        $services['PL']['AP'][] = 'UPS_SP_SERV_PL_AP_EXPRESS_SAVER';
        $services['PL']['AP'][] = 'UPS_SP_SERV_PL_AP_EXPRESS';
        $services['PL']['AP'][] = 'UPS_SP_SERV_PL_AP_EXPRESS_SAT_DELI';
        $services['PL']['AP'][] = 'UPS_SP_SERV_PL_AP_EXPRESS_PLUS';

        $services['PL']['ADD'][] = 'UPS_SP_SERV_PL_ADD_STANDARD';
        $services['PL']['ADD'][] = 'UPS_SP_SERV_PL_ADD_STANDARD_SAT_DELI';
        $services['PL']['ADD'][] = 'UPS_SP_SERV_PL_ADD_EXPEDITED';
        $services['PL']['ADD'][] = 'UPS_SP_SERV_PL_ADD_EXPRESS_SAVER';
        $services['PL']['ADD'][] = 'UPS_SP_SERV_PL_ADD_EXPRESS';
        $services['PL']['ADD'][] = 'UPS_SP_SERV_PL_ADD_EXPRESS_SAT_DELI';
        $services['PL']['ADD'][] = 'UPS_SP_SERV_PL_ADD_EXPRESS_PLUS';

        $services['GB']['AP'][] = 'UPS_SP_SERV_GB_AP_STANDARD';
        $services['GB']['AP'][] = 'UPS_SP_SERV_GB_AP_STANDARD_SAT_DELI';
        $services['GB']['AP'][] = 'UPS_SP_SERV_GB_AP_EXPEDITED';
        $services['GB']['AP'][] = 'UPS_SP_SERV_GB_AP_WW_SAVER';
        $services['GB']['AP'][] = 'UPS_SP_SERV_GB_AP_EXPRESS';
        $services['GB']['AP'][] = 'UPS_SP_SERV_GB_AP_EXPRESS_SAT_DELI';
        $services['GB']['AP'][] = 'UPS_SP_SERV_GB_AP_WW_EXPRESS_PLUS';

        $services['GB']['ADD'][] = 'UPS_SP_SERV_GB_ADD_STANDARD';
        $services['GB']['ADD'][] = 'UPS_SP_SERV_GB_ADD_STANDARD_SAT_DELI';
        $services['GB']['ADD'][] = 'UPS_SP_SERV_GB_ADD_EXPEDITED';
        $services['GB']['ADD'][] = 'UPS_SP_SERV_GB_ADD_WW_SAVER';
        $services['GB']['ADD'][] = 'UPS_SP_SERV_GB_ADD_EXPRESS';
        $services['GB']['ADD'][] = 'UPS_SP_SERV_GB_ADD_EXPRESS_SAT_DELI';
        $services['GB']['ADD'][] = 'UPS_SP_SERV_GB_ADD_WW_EXPRESS_PLUS';

        $services['FR']['AP'][] = 'UPS_SP_SERV_FR_AP_AP_ECONOMY';
        $services['FR']['AP'][] = 'UPS_SP_SERV_FR_AP_STANDARD';
        $services['FR']['AP'][] = 'UPS_SP_SERV_FR_AP_STANDARD_SAT_DELI';
        $services['FR']['AP'][] = 'UPS_SP_SERV_FR_AP_EXPEDITED';
        $services['FR']['AP'][] = 'UPS_SP_SERV_FR_AP_EXPRESS_SAVER';
        $services['FR']['AP'][] = 'UPS_SP_SERV_FR_AP_EXPRESS';
        $services['FR']['AP'][] = 'UPS_SP_SERV_FR_AP_EXPRESS_SAT_DELI';
        $services['FR']['AP'][] = 'UPS_SP_SERV_FR_AP_EXPRESS_PLUS';

        $services['FR']['ADD'][] = 'UPS_SP_SERV_FR_ADD_STANDARD';
        $services['FR']['ADD'][] = 'UPS_SP_SERV_FR_ADD_STANDARD_SAT_DELI';
        $services['FR']['ADD'][] = 'UPS_SP_SERV_FR_ADD_EXPEDITED';
        $services['FR']['ADD'][] = 'UPS_SP_SERV_FR_ADD_EXPRESS_SAVER';
        $services['FR']['ADD'][] = 'UPS_SP_SERV_FR_ADD_EXPRESS';
        $services['FR']['ADD'][] = 'UPS_SP_SERV_FR_ADD_EXPRESS_SAT_DELI';
        $services['FR']['ADD'][] = 'UPS_SP_SERV_FR_ADD_EXPRESS_PLUS';

        $services['DE']['AP'][] = 'UPS_SP_SERV_DE_AP_STANDARD';
        $services['DE']['AP'][] = 'UPS_SP_SERV_DE_AP_STANDARD_SAT_DELI';
        $services['DE']['AP'][] = 'UPS_SP_SERV_DE_AP_EXPEDITED';
        $services['DE']['AP'][] = 'UPS_SP_SERV_DE_AP_EXPRESS_SAVER';
        $services['DE']['AP'][] = 'UPS_SP_SERV_DE_AP_UPS_EXPRESS12';
        $services['DE']['AP'][] = 'UPS_SP_SERV_DE_AP_EXPRESS';
        $services['DE']['AP'][] = 'UPS_SP_SERV_DE_AP_EXPRESS_SAT_DELI';
        $services['DE']['AP'][] = 'UPS_SP_SERV_DE_AP_EXPRESS_PLUS';

        $services['DE']['ADD'][] = 'UPS_SP_SERV_DE_ADD_STANDARD';
        $services['DE']['ADD'][] = 'UPS_SP_SERV_DE_ADD_STANDARD_SAT_DELI';
        $services['DE']['ADD'][] = 'UPS_SP_SERV_DE_ADD_EXPEDITED';
        $services['DE']['ADD'][] = 'UPS_SP_SERV_DE_ADD_EXPRESS_SAVER';
        $services['DE']['ADD'][] = 'UPS_SP_SERV_DE_ADD_EXPRESS12';
        $services['DE']['ADD'][] = 'UPS_SP_SERV_DE_ADD_EXPRESS';
        $services['DE']['ADD'][] = 'UPS_SP_SERV_DE_ADD_EXPRESS_SAT_DELI';
        $services['DE']['ADD'][] = 'UPS_SP_SERV_DE_ADD_EXPRESS_PLUS';

        $services['ES']['AP'][] = 'UPS_SP_SERV_ES_AP_STANDARD';
        $services['ES']['AP'][] = 'UPS_SP_SERV_ES_AP_STANDARD_SAT_DELI';
        $services['ES']['AP'][] = 'UPS_SP_SERV_ES_AP_EXPEDITED';
        $services['ES']['AP'][] = 'UPS_SP_SERV_ES_AP_EXPRESS_SAVER';
        $services['ES']['AP'][] = 'UPS_SP_SERV_ES_AP_EXPRESS';
        $services['ES']['AP'][] = 'UPS_SP_SERV_ES_AP_EXPRESS_SAT_DELI';
        $services['ES']['AP'][] = 'UPS_SP_SERV_ES_AP_EXPRESS_PLUS';

        $services['ES']['ADD'][] = 'UPS_SP_SERV_ES_ADD_STANDARD';
        $services['ES']['ADD'][] = 'UPS_SP_SERV_ES_ADD_STANDARD_SAT_DELI';
        $services['ES']['ADD'][] = 'UPS_SP_SERV_ES_ADD_EXPEDITED';
        $services['ES']['ADD'][] = 'UPS_SP_SERV_ES_ADD_EXPRESS_SAVER';
        $services['ES']['ADD'][] = 'UPS_SP_SERV_ES_ADD_EXPRESS';
        $services['ES']['ADD'][] = 'UPS_SP_SERV_ES_ADD_EXPRESS_SAT_DELI';
        $services['ES']['ADD'][] = 'UPS_SP_SERV_ES_ADD_EXPRESS_PLUS';

        $services['IT']['AP'][] = 'UPS_SP_SERV_IT_AP_STANDARD';
        $services['IT']['AP'][] = 'UPS_SP_SERV_IT_AP_STANDARD_SAT_DELI';
        $services['IT']['AP'][] = 'UPS_SP_SERV_IT_AP_EXPEDITED';
        $services['IT']['AP'][] = 'UPS_SP_SERV_IT_AP_EXPRESS_SAVER';
        $services['IT']['AP'][] = 'UPS_SP_SERV_IT_AP_EXPRESS';
        $services['IT']['AP'][] = 'UPS_SP_SERV_IT_AP_EXPRESS_SAT_DELI';
        $services['IT']['AP'][] = 'UPS_SP_SERV_IT_AP_EXPRESS_PLUS';

        $services['IT']['ADD'][] = 'UPS_SP_SERV_IT_ADD_STANDARD';
        $services['IT']['ADD'][] = 'UPS_SP_SERV_IT_ADD_STANDARD_SAT_DELI';
        $services['IT']['ADD'][] = 'UPS_SP_SERV_IT_ADD_EXPEDITED';
        $services['IT']['ADD'][] = 'UPS_SP_SERV_IT_ADD_EXPRESS_SAVER';
        $services['IT']['ADD'][] = 'UPS_SP_SERV_IT_ADD_EXPRESS';
        $services['IT']['ADD'][] = 'UPS_SP_SERV_IT_ADD_EXPRESS_SAT_DELI';
        $services['IT']['ADD'][] = 'UPS_SP_SERV_IT_ADD_EXPRESS_PLUS';

        $services['NL']['AP'][] = 'UPS_SP_SERV_NL_AP_AP_ECONOMY';
        $services['NL']['AP'][] = 'UPS_SP_SERV_NL_AP_STANDARD';
        $services['NL']['AP'][] = 'UPS_SP_SERV_NL_AP_STANDARD_SAT_DELI';
        $services['NL']['AP'][] = 'UPS_SP_SERV_NL_AP_EXPEDITED';
        $services['NL']['AP'][] = 'UPS_SP_SERV_NL_AP_EXPRESS_SAVER';
        $services['NL']['AP'][] = 'UPS_SP_SERV_NL_AP_EXPRESS';
        $services['NL']['AP'][] = 'UPS_SP_SERV_NL_AP_EXPRESS_SAT_DELI';
        $services['NL']['AP'][] = 'UPS_SP_SERV_NL_AP_EXPRESS_PLUS';

        $services['NL']['ADD'][] = 'UPS_SP_SERV_NL_ADD_STANDARD';
        $services['NL']['ADD'][] = 'UPS_SP_SERV_NL_ADD_STANDARD_SAT_DELI';
        $services['NL']['ADD'][] = 'UPS_SP_SERV_NL_ADD_EXPEDITED';
        $services['NL']['ADD'][] = 'UPS_SP_SERV_NL_ADD_EXPRESS_SAVER';
        $services['NL']['ADD'][] = 'UPS_SP_SERV_NL_ADD_EXPRESS';
        $services['NL']['ADD'][] = 'UPS_SP_SERV_NL_ADD_EXPRESS_SAT_DELI';
        $services['NL']['ADD'][] = 'UPS_SP_SERV_NL_ADD_EXPRESS_PLUS';

        $services['BE']['AP'][] = 'UPS_SP_SERV_BE_AP_AP_ECONOMY';
        $services['BE']['AP'][] = 'UPS_SP_SERV_BE_AP_STANDARD';
        $services['BE']['AP'][] = 'UPS_SP_SERV_BE_AP_STANDARD_SAT_DELI';
        $services['BE']['AP'][] = 'UPS_SP_SERV_BE_AP_EXPEDITED';
        $services['BE']['AP'][] = 'UPS_SP_SERV_BE_AP_EXPRESS_SAVER';
        $services['BE']['AP'][] = 'UPS_SP_SERV_BE_AP_EXPRESS';
        $services['BE']['AP'][] = 'UPS_SP_SERV_BE_AP_EXPRESS_SAT_DELI';
        $services['BE']['AP'][] = 'UPS_SP_SERV_BE_AP_EXPRESS_PLUS';

        $services['BE']['ADD'][] = 'UPS_SP_SERV_BE_ADD_STANDARD';
        $services['BE']['ADD'][] = 'UPS_SP_SERV_BE_ADD_STANDARD_SAT_DELI';
        $services['BE']['ADD'][] = 'UPS_SP_SERV_BE_ADD_EXPEDITED';
        $services['BE']['ADD'][] = 'UPS_SP_SERV_BE_ADD_EXPRESS_SAVER';
        $services['BE']['ADD'][] = 'UPS_SP_SERV_BE_ADD_EXPRESS';
        $services['BE']['ADD'][] = 'UPS_SP_SERV_BE_ADD_EXPRESS_SAT_DELI';
        $services['BE']['ADD'][] = 'UPS_SP_SERV_BE_ADD_EXPRESS_PLUS';

        $services['US']['AP'][] = 'UPS_SP_SERV_US_AP_GROUND';
        $services['US']['AP'][] = 'UPS_SP_SERV_US_AP_3_DAY_SELECT';
        $services['US']['AP'][] = 'UPS_SP_SERV_US_AP_DAY_AIR';
        $services['US']['AP'][] = 'UPS_SP_SERV_US_AP_DAY_AIR_AM';
        $services['US']['AP'][] = 'UPS_SP_SERV_US_AP_AIR_SAVER';
        $services['US']['AP'][] = 'UPS_SP_SERV_US_AP_AIR';
        $services['US']['AP'][] = 'UPS_SP_SERV_US_AP_AIR_EARLY';
        $services['US']['AP'][] = 'UPS_SP_SERV_US_AP_STANDARD';
        $services['US']['AP'][] = 'UPS_SP_SERV_US_AP_WORLDWIDE_EXPEDITED';
        $services['US']['AP'][] = 'UPS_SP_SERV_US_AP_WORLDWIDE_SAVER';
        $services['US']['AP'][] = 'UPS_SP_SERV_US_AP_WORLDWIDE_EXPRESS';
        $services['US']['AP'][] = 'UPS_SP_SERV_US_AP_WORLDWIDE_EXPRESS_PLUS';

        $services['US']['ADD'][] = 'UPS_SP_SERV_US_ADD_GROUND';
        $services['US']['ADD'][] = 'UPS_SP_SERV_US_ADD_3_DAY_SELECT';
        $services['US']['ADD'][] = 'UPS_SP_SERV_US_ADD_DAY_AIR';
        $services['US']['ADD'][] = 'UPS_SP_SERV_US_ADD_DAY_AIR_AM';
        $services['US']['ADD'][] = 'UPS_SP_SERV_US_ADD_AIR_SAVER';
        $services['US']['ADD'][] = 'UPS_SP_SERV_US_ADD_AIR';
        $services['US']['ADD'][] = 'UPS_SP_SERV_US_ADD_AIR_EARLY';
        $services['US']['ADD'][] = 'UPS_SP_SERV_US_ADD_STANDARD';
        $services['US']['ADD'][] = 'UPS_SP_SERV_US_ADD_WORLDWIDE_EXPEDITED';
        $services['US']['ADD'][] = 'UPS_SP_SERV_US_ADD_WORLDWIDE_SAVER';
        $services['US']['ADD'][] = 'UPS_SP_SERV_US_ADD_WORLDWIDE_EXPRESS';
        $services['US']['ADD'][] = 'UPS_SP_SERV_US_ADD_WORLDWIDE_EXPRESS_PLUS';

        if (!empty($countryCode)) {
            if (empty($serviceType)) {
                $result = array();
                $result = array_merge($services[$countryCode]["AP"], $services[$countryCode]["ADD"]);
                return $result;
            }

            return $services[$countryCode][$serviceType];
        }
        return array();
    }

    /**
     * SortedServices getListSortedServices
     *
     * @param array $sortedServices //The list shipping services
     * @param array $listServices //The sorted service
     *
     * @return array $data
     */
    public function getListSortedServices($sortedServices, $listServices)
    {
        $resultServices = array();
        if (is_array($listServices) && !empty($listServices)
            && is_array($sortedServices) && !empty($sortedServices)) {
            foreach ($sortedServices as $service_key) {
                $index = array_search($service_key, array_column($listServices, 'service_key'));
                if (gettype($index) == "integer") {
                    $resultServices[] = $listServices[$index];
                }
            }
        }
        return $resultServices;
    }
}
