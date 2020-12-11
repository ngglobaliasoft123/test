<?php
/**
 * Service file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Model\ResourceModel;
/**
 * Service class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Service extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $sortedServices;
    /**
     * Service __construct
     * collect registration data
     *
     * @param string $context //The Context
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \UPS\Shipping\Model\SortedServices $sortedServices

    ) {
        $this->sortedServices = $sortedServices;
        parent::__construct($context);
    }

    /**
     * Service _construct
     *
     * @return array $data
     */
    protected function _construct()
    {
        $this->_init('ups_shipping_services', 'id');
    }

    /**
     * Service getAllListService
     *
     * @return array $data
     */
    public function getAllListService()
    {
        $configTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::CORE_CONFIG_DATA);
        $shippingCountry = \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE;
        $select = $this->getConnection()->select()
            ->from(['sv' => $this->getMainTable()])
            ->join(['cf' => $configTable], \UPS\Shipping\Helper\ConstantModel::COUNTRY_CODE_VALUE)
            ->where(\UPS\Shipping\Helper\ConstantModel::CF_PATH, $shippingCountry);
        //return $this->getConnection()->fetchAll($select);
        $serviceAll = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $serviceAll[] = $row;
        }
        return $serviceAll;
    }

    /**
     * Service getListService
     *
     * @param integer $serviceType //The serviceType
     *
     * @return array $data
     */
    public function getListService($countryCode, $serviceType)
    {
        $configTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::CORE_CONFIG_DATA);
        $shippingCountry = \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE;
        $select = $this->getConnection()->select()
            ->from(['sv' => $this->getMainTable()])
            ->join(['cf' => $configTable], \UPS\Shipping\Helper\ConstantModel::COUNTRY_CODE_VALUE)
            ->where(\UPS\Shipping\Helper\ConstantModel::CF_PATH, $shippingCountry)
            ->where(\UPS\Shipping\Helper\ConstantModel::SV_SERVICE_TYPE, $serviceType);
        //return $this->getConnection()->fetchAll($select);
        $listServices = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $listServices[] = $row;
        }

        $sortedServices = $this->sortedServices->getListSortedServicesByCountryCode($countryCode, $serviceType);
        $result = $this->sortedServices->getListSortedServices($sortedServices, $listServices);
        return $result;
    }

    /**
     * Service getServicesById
     * get list selected shipping services by service id
     *
     * @param integer $serviceId //The serviceId
     *
     * @return array $data
     */
    public function getServicesById($serviceId)
    {
        $select = $this->getConnection()->select()
            ->from(['sv' => $this->getMainTable()])
            ->where('sv.id = ?', $serviceId)
            ->where(\UPS\Shipping\Helper\ConstantModel::SV_SERVICE_SELECTED, '1');
        //return $this->getConnection()->fetchAll($select);
        $serviceIds = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $serviceIds[] = $row;
        }
        return $serviceIds;
    }

    /**
     * Service getAllListAccessorial
     * get list selected shipping services by service type
     *
     * @param integer $serviceType //The serviceType
     * @param string $countryCode // Country code
     *
     * @return array $data
     */
    public function getSelectedServices($serviceType, $countryCode = '')
    {
        $select = $this->getConnection()->select()
            ->from(['sv' => $this->getMainTable()])
            ->where(\UPS\Shipping\Helper\ConstantModel::SV_SERVICE_TYPE, $serviceType)
            ->where(\UPS\Shipping\Helper\ConstantModel::SV_SERVICE_SELECTED, '1');
        //return $this->getConnection()->fetchAll($select);
        $selectedServices = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $selectedServices[] = $row;
        }
        if ($countryCode != '') {
            $sortedServices = $this->sortedServices->getListSortedServicesByCountryCode($countryCode, $serviceType);
            $selectedServices = $this->sortedServices->getListSortedServices($sortedServices, $selectedServices);
        }
        return $selectedServices;
    }

    /**
     * Service getSelectedCountryServices
     * get list selected shipping services by service type
     *
     * @param integer $serviceType //The serviceType
     * @param integer $country     //The country
     *
     * @return array $data
     */
    public function getSelectedCountryServices($serviceType, $country)
    {
        $select = $this->getConnection()->select()
            ->from(['sv' => $this->getMainTable()])
            ->where(\UPS\Shipping\Helper\ConstantModel::SV_SERVICE_TYPE, $serviceType)
            ->where(\UPS\Shipping\Helper\ConstantModel::SV_SERVICE_SELECTED, '1')
            ->where('sv.country_code = ?', $country);
        //return $this->getConnection()->fetchAll($select);
        $selectedCountryServices = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $selectedCountryServices[] = $row;
        }
        return $selectedCountryServices;
    }

    /**
     * Service getListNotSelected
     * get list not selected shipping services
     *
     * @param integer $selected //The selected
     *
     * @return array $data
     */
    public function getListNotSelected($selected = '0')
    {
        $configTable = $this->getTable(\UPS\Shipping\Helper\ConstantModel::CORE_CONFIG_DATA);
        $countryCode = \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE;
        $select = $this->getConnection()->select()
            ->from(['sv' => $this->getMainTable()])
            ->where(\UPS\Shipping\Helper\ConstantModel::SV_SERVICE_SELECTED, $selected)
            ->where(\UPS\Shipping\Helper\ConstantModel::CF_PATH, $countryCode)
            ->join(['cf' => $configTable], \UPS\Shipping\Helper\ConstantModel::COUNTRY_CODE_VALUE);
        //return $this->getConnection()->fetchAll($select);
        $notSelectedServices = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $notSelectedServices[] = $row;
        }
        return $notSelectedServices;
    }

    /**
     * Service getShippingServiceById
     * get shipping service by service id
     *
     * @param integer $serviceId //The serviceId
     *
     * @return array $data
     */
    public function getShippingServiceById($serviceId)
    {
        $select = $this->getConnection()->select()
            ->from(['sv' => $this->getMainTable()])
            ->where('sv.id = ?', $serviceId);
        //return $this->getConnection()->fetchAll($select);
        $shippingServiceIds = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $shippingServiceIds[] = $row;
        }
        return $shippingServiceIds;
    }

    /**
     * Service updateSelectedService
     * get list selected shipping services by service type
     *
     * @param integer $serviceId //The selected
     * @param integer $status    //The status
     *
     * @return array $data
     */
    public function updateSelectedService($serviceId, $status)
    {
        $dataUpdate = ['service_selected' => $status];
        return $this->getConnection()->update($this->getMainTable(), $dataUpdate, ['id =?' => $serviceId]);
    }
}
