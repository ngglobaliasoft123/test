<?php
/**
 * Pkgdimension file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Block\Adminhtml\Config;
/**
 * Pkgdimension class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Pkgdimension extends \Magento\Framework\View\Element\Template
{
    protected $scopeConfig;
    protected $modelPackage;
    protected $modelDimension;
    protected $modelBackuprate;
    protected $serviceModel;
    protected $formKey;

    /**
     * Pkgdimension __construct
     *
     * @param string $context         //The context
     * @param string $modelPackage    //The modelPackage
     * @param string $modelDimension  //The modelDimension
     * @param string $modelBackuprate //The modelBackuprate
     * @param string $serviceModel    //The serviceModel
     * @param string $formKey         //The formKey
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \UPS\Shipping\Model\Package $modelPackage,
        \UPS\Shipping\Model\Dimension $modelDimension,
        \UPS\Shipping\Model\Backuprate $modelBackuprate,
        \UPS\Shipping\Model\Service $serviceModel,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->modelPackage = $modelPackage;
        $this->modelDimension = $modelDimension;
        $this->modelBackuprate = $modelBackuprate;
        $this->serviceModel = $serviceModel;
        $this->formKey = $formKey;
        parent::__construct($context);
    }

    /**
     * Pkgdimension getListPackage
     *
     * @return array $data
     */
    public function getListPackage()
    {
        return $this->modelPackage->getListPackage();
    }

    /**
     * Pkgdimension getListDimension
     *
     * @return array $data
     */
    public function getListDimension()
    {
        return $this->modelDimension->getListDimension();
    }

    /**
     * Pkgdimension getListBackuprate
     *
     * @return array $data
     */
    public function getListBackuprate()
    {
        return $this->modelBackuprate->getListBackuprate();
    }

    /**
     * Pkgdimension getAddressServices
     *
     * @return array $data
     */
    public function getAddressServices($countryCode, $service_type = 'ADD')
    {
        $countryCode = strtoupper($countryCode);
        return $this->serviceModel->getListService($countryCode, $service_type);
    }

    /**
     * Pkgdimension getCountryCode
     *
     * @return array $data
     */
    public function getCountryCode()
    {
        return $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE);
    }

    /**
     * Pkgdimension getPackageDimension
     *
     * @return array $data
     */
    public function getPackageDimension()
    {
        return $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_PACKAGE_DIMENSIONS);
    }

    /**
     * Pkgdimension getPackageDimension
     *
     * @return array $data
     */
    public function getIncludeDimension()
    {
        return $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_INCLUDE_DIMENSIONS);
    }

    /**
     * Pkgdimension countItem
     *
     * @param string $itemCount //The itemCount
     *
     * @return array $Order
     */
    public function countItem($itemCount)
    {
        return count($itemCount);
    }

    /**
     * Pkgdimension getFormKey
     *
     * @return array $serviceData
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}
