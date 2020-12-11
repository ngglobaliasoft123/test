<?php
/**
 * SavePkgdimension file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Controller\Adminhtml\Config;

/**
 * SavePkgdimension class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class SavePkgdimension extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $configWriter;
    protected $cacheTypeList;
    protected $modelService;
    protected $modelPackage;
    protected $modelDimension;
    protected $apiManager;
    protected $checkoutSession;
    protected $scopeConfig;
    protected $licenseModel;
    protected $apiHandshake;
    protected $apiAccount;

    /**
     * SavePkgdimension __construct
     *
     * @param string $context           //The context
     * @param string $resultJsonFactory //The resultJsonFactory
     * @param string $configWriter      //The configWriter
     * @param string $cacheTypeList     //The cacheTypeList
     * @param string $checkoutSession   //The checkoutSession
     * @param string $modelPackage      //The modelPackage
     * @param string $modelDimension    //The modelDimension
     * @param string $modelBackuprate   //The modelBackuprate
     * @param string $apiManager        //The apiManager
     * @param string $scopeConfig       //The scopeConfig
     * @param string $modelLicense      //modelLicense
     * @param string $apiHandshake      //The apiHandshake
     * @param string $modelService      //The modelService
     * @param string $apiAccount        //The apiAccount
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Checkout\Model\Session $checkoutSession,
        \UPS\Shipping\Model\Package $modelPackage,
        \UPS\Shipping\Model\Dimension $modelDimension,
        \UPS\Shipping\Model\Backuprate $modelBackuprate,
        \UPS\Shipping\API\Manager\Index $apiManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \UPS\Shipping\Model\License $modelLicense,
        \UPS\Shipping\API\Handshake $apiHandshake,
        \UPS\Shipping\Model\Service $modelService,
        \UPS\Shipping\API\Account $apiAccount
    ) {
        $this->configWriter = $configWriter;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->modelPackage = $modelPackage;
        $this->modelDimension = $modelDimension;
        $this->modelBackuprate = $modelBackuprate;
        $this->cacheTypeList = $cacheTypeList;
        $this->scopeConfig = $scopeConfig;
        $this->apiManager = $apiManager;
        $this->licenseModel = $modelLicense;
        $this->apiHandshake = $apiHandshake;
        $this->apiAccount = $apiAccount;
        $this->checkoutSession = $checkoutSession;
        $this->modelService = $modelService;
        parent::__construct($context);
    }

    /**
     * SavePkgdimension execute
     *
     * @return null
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $result = $this->resultJsonFactory->create();
            $returnData = [];
            switch ($this->getRequest()->getParam('method')) {
            case 'getOnePackage':
                $package_id = $this->getRequest()->getParam(\UPS\Shipping\Helper\ConstantPackage::PACKAGE_ID);
                $returnData = $result->setData(['result' => $this->modelPackage->getOnePackage($package_id)]);
                break;

            case 'deletePackage':
                $returnData = $result->setData(['result' => $this->_deletePackageDimension()]);
                break;

            default:
                break;
            }
            return $returnData;
        } else {
            $method = $this->getRequest()->getParam('method');
            switch ($method) {
                case 'addPackageDimension':
                    $this->_savePackageDimension();
                    break;

                case 'updatePackage':
                    $this->_updatePackageDimension();
                    break;

                default:
                    break;
            }
            $this->submitForm($method);
        }
    }

    /**
     * SavePkgdimension submitForm
     *
     * @param string $method //The method
     *
     * @return null
     */
    public function submitForm($method)
    {
        $method_click = $this->getRequest()->getParam('btn_package');
        if ('next' == $method_click) {
            $this->_redirect('upsshipping/config/deliveryrates');
        } else {
            $this->_redirect('upsshipping/config/pkgdimension');
        }
    }

    /**
     * SavePkgdimension validate add package name
     *
     * @param string $inputPkg //The inputPkg
     *
     * @return boolean
     */
    private static function _validateAddPkgName($inputPkg)
    {
        $inputPkgStrlen = mb_strlen($inputPkg);
        if (($inputPkgStrlen >= 1) && ($inputPkgStrlen <= 50)
        ) {
            return true;
        }
        return false;
    }

    /**
     * SavePkgdimension validate add package unit
     *
     * @param string $inputPkgs //The inputPkgs
     *
     * @return boolean
     */
    private static function _validateAddPkgUnit($inputPkgs)
    {
        if (preg_match('/^\d+(\.\d{1,2})?$/', $inputPkgs) && $inputPkgs >= 0.01 && $inputPkgs <= 9999.99) {
            return true;
        }
        return false;
    }

    /**
     * SavePkgdimension validate unit
     *
     * @param string $data //The data
     *
     * @return boolean
     */
    public function validateUnit($data)
    {
        if (!$this->_validateAddPkgUnit($data[\UPS\Shipping\Helper\ConstantPackage::WEIGHT])
            || !$this->_validateAddPkgUnit($data[\UPS\Shipping\Helper\ConstantPackage::WIDTH])
            || !$this->_validateAddPkgUnit($data[\UPS\Shipping\Helper\ConstantPackage::HEIGHT])
        ) {
            return true;
        }
        return false;
    }

    /**
     * SavePkgdimension save package
     *
     * @author UPS <noreply@ups.com>
     *
     * @return string $errorMessage
     */
    private function _savePackageDimension()
    {
        $data = $this->getRequest()->getParams();
        $saveData = [];
        $packageDimensionString = \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_PACKAGE_DIMENSIONS;
        $this->configWriter->save($packageDimensionString, $data['package_setting_option']);
        foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        if ('1' == $data['package_setting_option']) {
            // default_package
            if (!empty($data['default_package'])) {
                $saveData = $data['default_package'];
                $this->modelPackage->savePackage($saveData);
                $this->messageManager->addSuccess(__("Data saved successfully."));
            }
        } else {
            $packageIncludeDimension = \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_INCLUDE_DIMENSIONS;
            $dataIncludeString = '';
            if (!empty($data['config']['service/upsshipping/item_level_rating_include_dimensions'])) {
                $dataIncludeString = trim($data['config']['service/upsshipping/item_level_rating_include_dimensions']);
            }
            $dataIncludeData = (('on' == $dataIncludeString) ? 1 : 0);
            $this->configWriter->save($packageIncludeDimension, $dataIncludeData);
            foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
                $this->cacheTypeList->cleanType($type);
            }
            $saveData = $data["product_dimension"];
            $this->modelDimension->saveDimension($saveData);
            //modelBackuprate
            $saveBackup = $data["backup_rate"];
            $this->modelBackuprate->saveBackuprate($saveBackup);
            $this->messageManager->addSuccess(__("Data saved successfully."));
            // product_dimension
        }
        $checkTransferExist = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_PLUGIN_MERCHANTINFO_EXIST);
        // call plugin manager
        if ($checkTransferExist == '1') {
            foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
                $this->cacheTypeList->cleanType($type);
            }
            $this->callTransferPackageDimension($data['package_setting_option']);
        }
        /*$packageNameString = $data[\UPS\Shipping\Helper\ConstantPackage::NAMEPACKAGE];
        $listSamePackageNames = $this->modelPackage->getNameExits($packageNameString);
        $selectedCountry = strtolower($this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE));
        $arrPackageName = [];
        if (!empty($listSamePackageNames)) {
            foreach ($listSamePackageNames as $item) {
                $arrPackageName[] = $item[\UPS\Shipping\Helper\ConstantPackage::PACKAGE_NAME];
            }
        }
        if (!$this->_validateAddPkgName($packageNameString)
            || !$this->_validateAddPkgUnit($data[\UPS\Shipping\Helper\ConstantPackage::LENGTH])
            || ($this->validateUnit($data)) == true
        ) {
            $this->messageManager->addError(__("Some of the data you entered is not valid. Please check again."));
        } elseif ($this->modelPackage->nameExits($packageNameString)
            && in_array($packageNameString, $arrPackageName)
        ) {
            $this->messageManager->addError(__("The name Package is exist."));
        } elseif (!$this->validateWeight($data, $selectedCountry)) {
            if ('us' == $selectedCountry) {
                $this->messageManager->addError(__("Error! Maximum allowable weight per package weight is 150lbs."));
            } else {
                $this->messageManager->addError(__("Error! Maximum allowable per package weight is 70.00 kgs or 154.32 lbs."));
            }
        } elseif (!$this->validateLength($data, $selectedCountry)) {
            if ('us' == $selectedCountry) {
                $this->messageManager->addError(__("Error! Maximum allowable package length is 108 inches."));
            }
        } elseif (!$this->validateDimension($data, $selectedCountry)) {
            if ('us' == $selectedCountry) {
                $this->messageManager->addError(__("Error! Package exceeds the maximum allowable size of 165 inches (the maximum allowable size calculation = length + 2 * width + 2 * height)."));
            } else {
                $this->messageManager->addError(__("Error! Package exceeds the maximum allowable size of 400 cm or 157.48 inches (the maximum allowable size calculation = length + 2 * width + 2 * height)."));
            }
        } else {*/

        //}
    }

    /**
     * SavePkgdimension validate dimension
     *
     * @param string $data            //The data
     * @param string $selectedCountry //The selectedCountry
     *
     * @return boolean
     */
    public function validateWeight($data, $selectedCountry)
    {
        if ('kgs' == $data['weightunit']) {
            if ($data[\UPS\Shipping\Helper\ConstantPackage::WEIGHT] <= 70) {
                return true;
            }
        } else {
            $changeUnit = (154.32)*1;
            if ('us' == $selectedCountry) {
                $changeUnit = 150;
            }
            if ($data[\UPS\Shipping\Helper\ConstantPackage::WEIGHT] <= $changeUnit) {
                return true;
            }
        }
        return false;
    }

    /**
     * SavePkgdimension validate length
     *
     * @param string $data            //The data
     * @param string $selectedCountry //The selectedCountry
     *
     * @return boolean
     */
    public function validateLength($data, $selectedCountry)
    {
        if ('us' != $selectedCountry ) {
            return true;
        }
        if ('inch' == $data[\UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION_LENGTH] && 'us' == $selectedCountry) {
            $changeUnit = 108;
            if ($data[\UPS\Shipping\Helper\ConstantPackage::LENGTH] <= $changeUnit && $data[\UPS\Shipping\Helper\ConstantPackage::WIDTH] <= $changeUnit && $data[\UPS\Shipping\Helper\ConstantPackage::HEIGHT] <= $changeUnit) {
                return true;
            }
        }
        return false;
    }

    /**
     * SavePkgdimension validate dimension
     *
     * @param string $data            //The data
     * @param string $selectedCountry //The selectedCountry
     *
     * @return boolean
     */
    public function validateDimension($data, $selectedCountry)
    {
        $dimensionFormula = $data[\UPS\Shipping\Helper\ConstantPackage::LENGTH] + 2 * $data[\UPS\Shipping\Helper\ConstantPackage::WIDTH] + 2 * $data[\UPS\Shipping\Helper\ConstantPackage::HEIGHT];
        if (\UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION_CM == $data[\UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION_LENGTH]) {
            if ($dimensionFormula <= 400) {
                return true;
            }
        } else {
            $changeUnit = (157.48)*1;
            if ('us' == $selectedCountry) {
                $changeUnit = 165;
            }
            if ($dimensionFormula <= $changeUnit) {
                return true;
            }
        }
        return false;
    }

    /**
     * SavePkgdimension validate update package
     *
     * @param string $data //The data
     *
     * @return boolean
     */
    public function validateUpdatePackage($data)
    {
        if (!$this->_validateAddPkgUnit($data['lengthPopup'])
            || !$this->_validateAddPkgUnit($data['widthPopup'])
            || !$this->_validateAddPkgUnit($data['heightPopup'])
        ) {
            return true;
        }
        return false;
    }

    /**
     * SavePkgdimension update package
     *
     * @return boolean
     */
    private function _updatePackageDimension()
    {
        $data = $this->getRequest()->getParams();
        $dataPopupPackageName = $data[\UPS\Shipping\Helper\ConstantPackage::NAMEPACKAGEPOPUP];
        $packageDefault = $this->modelPackage->getPackageDefault();
        $packageId = \UPS\Shipping\Helper\ConstantPackage::PACKAGE_ID;
        $listSamePackageNames
            = $this->modelPackage->getNameExitsPopup($dataPopupPackageName, $this->getRequest()->getParam($packageId));
        $selectedCountry = strtolower($this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE));
        $arrPackageName = [];
        if (!empty($listSamePackageNames)) {
            foreach ($listSamePackageNames as $item) {
                $arrPackageName[] = $item[\UPS\Shipping\Helper\ConstantPackage::PACKAGE_NAME];
            }
        }
        if (!$this->_validateAddPkgName($dataPopupPackageName)
            || !$this->_validateAddPkgUnit($data['weightPopup'])
            || ($this->validateUpdatePackage($data) == true)
        ) {
            return [
                'error' => 1,
                'message' => __('Some of the data you entered is not valid. Please check again.')
            ];
        } elseif ($this->modelPackage->nameExitsPopup($dataPopupPackageName, $this->getRequest()->getParam($packageId))
            && in_array($dataPopupPackageName, $arrPackageName)
        ) {
            $this->messageManager->addError(__("The name Package is exist."));
        } elseif (!$this->validateEditWeight($data, $selectedCountry)) {
            if ('us' == $selectedCountry) {
                $this->messageManager->addError(__("Error! Maximum allowable weight per package weight is 150lbs."));
            } else {
                $this->messageManager->addError(__("Error! Maximum allowable per package weight is 70.00 kgs or 154.32 lbs."));
            }
        } elseif (!$this->validateEditLength($data, $selectedCountry)) {
            if ('us' == $selectedCountry) {
                $this->messageManager->addError(__("Error! Maximum allowable package length is 108 inches."));
            }
        } elseif (!$this->validateEditDimension($data, $selectedCountry)) {
            if ('us' == $selectedCountry) {
                $this->messageManager->addError(__("Error! Package exceeds the maximum allowable size of 165 inches (the maximum allowable size calculation = length + 2 * width + 2 * height)."));
            } else {
                $this->messageManager->addError(__("Error! Package exceeds the maximum allowable size of 400 cm or 157.48 inches (the maximum allowable size calculation = length + 2 * width + 2 * height)."));
            }
        } else {
            $this->messageManager->addSuccess(__("Data saved successfully."));
            $packageId = \UPS\Shipping\Helper\ConstantPackage::PACKAGE_ID;
            $updateData = [
                \UPS\Shipping\Helper\ConstantPackage::PACKAGE_NAME => $dataPopupPackageName,
                \UPS\Shipping\Helper\ConstantPackage::WEIGHT => $data['weightPopup'],
                \UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT => $data['weightunitPopup'],
                \UPS\Shipping\Helper\ConstantPackage::LENGTH => $data['lengthPopup'],
                \UPS\Shipping\Helper\ConstantPackage::WIDTH => $data['widthPopup'],
                \UPS\Shipping\Helper\ConstantPackage::HEIGHT => $data['heightPopup'],
                \UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION => $data['lengthUnitPopup']
            ];
            $this->modelPackage->updatePackage($updateData, $data[$packageId]);
            $updateMessage = __("Data saved successfully.");

            if ($packageDefault[$packageId] == $this->getRequest()->getParam($packageId)) {
                $checkTransferExist = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_PLUGIN_MERCHANTINFO_EXIST);
                // call plugin manager
                if ($checkTransferExist == '1') {
                    //$this->callTransferPackageDimension();
                }
                // end call
            }
            return ['error' => 0, 'message' => $updateMessage];
        }
    }

    /**
     * SavePkgdimension validateEditWeight
     *
     * @param string $data            //The data
     * @param string $selectedCountry //The selectedCountry
     *
     * @return boolean
     */
    public function validateEditWeight($data, $selectedCountry)
    {
        if ('kgs' == $data['weightunitPopup']) {
            if ($data['weightPopup'] <= 70) {
                return true;
            }
        } else {
            $changeUnit = (154.32)*1;
            if ('us' == $selectedCountry) {
                $changeUnit = 150;
            }
            if ($data['weightPopup'] <= $changeUnit) {
                return true;
            }
        }
        return false;
    }

    /**
     * SavePkgdimension validateEditLength
     *
     * @param string $data            //The data
     * @param string $selectedCountry //The selectedCountry
     *
     * @return boolean
     */
    public function validateEditLength($data, $selectedCountry)
    {
        if ('us' != $selectedCountry ) {
            return true;
        }
        if ('inch' == $data['lengthUnitPopup'] && 'us' == $selectedCountry) {
            $changeUnit = 108;
            if ($data['lengthPopup'] <= $changeUnit && $data['widthPopup'] <= $changeUnit && $data['heightPopup'] <= $changeUnit) {
                return true;
            }
        }
        return false;
    }

    /**
     * SavePkgdimension validate dimension
     *
     * @param string $data            //The data
     * @param string $selectedCountry //The selectedCountry
     *
     * @return boolean
     */
    public function validateEditDimension($data, $selectedCountry)
    {
        $dimensionFormula = $data['lengthPopup'] + 2 * $data['widthPopup'] + 2 * $data['heightPopup'];
        if (\UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION_CM == $data['lengthUnitPopup']) {
            if ($dimensionFormula <= 400) {
                return true;
            }
        } else {
            $changeUnit = (157.48)*1;
            if ('us' == $selectedCountry) {
                $changeUnit = 165;
            }
            if ($dimensionFormula <= $changeUnit) {
                return true;
            }
        }
        return false;
    }

    /**
     * SavePkgdimension delete package
     *
     * @return boolean
     */
    private function _deletePackageDimension()
    {
        $packageId = $this->getRequest()->getParam(\UPS\Shipping\Helper\ConstantPackage::PACKAGE_ID);
        return $this->modelPackage->deletePackage($packageId);
    }

    /**
     * SavePkgdimension call transfer package
     *
     * @return boolean
     */
    public function callTransferPackageDimension($option)
    {
        // default package
        $jsonDefaultPackage = $this->modelPackage->getListPackage();
        $jsonBackuprate = $this->modelBackuprate->getListBackuprate();
        $defaultPackageName = 'default package';
        $weight             = '0';
        $weightUnit         = 'kgs';
        $length             = '0';
        $width              = '0';
        $height             = '0';
        $dimensionUnit      = 'cm';
        $packageItem = '0';
        $serviceKey = '';
        $rate = '0';

        if (!empty($jsonDefaultPackage)) {
            $defaultPackage = (isset($jsonDefaultPackage[0])) ? $jsonDefaultPackage[0] : [];

            if (!empty($defaultPackage)) {
                $unit_weight = strtoupper($defaultPackage[\UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT]);
                $dimension_unit = strtoupper($defaultPackage[\UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION]);
                $defaultPackageName = $defaultPackage[\UPS\Shipping\Helper\ConstantPackage::PACKAGE_NAME];
                $weight             = $defaultPackage[\UPS\Shipping\Helper\ConstantPackage::WEIGHT];
                if ($unit_weight == 'LBS') {
                    $weightUnit = 'Pounds';
                } else {
                    $weightUnit = 'Kg';
                }
                $length             = $defaultPackage[\UPS\Shipping\Helper\ConstantPackage::LENGTH];
                $width              = $defaultPackage[\UPS\Shipping\Helper\ConstantPackage::WIDTH];
                $height             = $defaultPackage[\UPS\Shipping\Helper\ConstantPackage::HEIGHT];
                if ($dimension_unit == 'CM') {
                    $dimensionUnit = 'Cm';
                } else {
                    $dimensionUnit = 'Inch';
                }
                $packageItem = $defaultPackage['package_number'];
            }
        }

        if (!empty($jsonBackuprate)) {
            $defaultBackuprate = (isset($jsonBackuprate[0])) ? $jsonBackuprate[0] : [];
            if (!empty($defaultBackuprate)) {
                $serviceInformation = $this->modelService->getShippingServiceById($defaultBackuprate['service_id']);
                if (!empty($serviceInformation[0]['service_key'])) {
                    $serviceKey = $serviceInformation[0]['service_key'];
                }
                $rate = $defaultBackuprate['fallback_rate'];
            }
        }
        if (1 == $option) {
            $arrPackageDimension = [
                'merchantKey' => $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY),
                'name' => $defaultPackageName,
                \UPS\Shipping\Helper\ConstantPackage::WEIGHT => $weight,
                \UPS\Shipping\Helper\ConstantPackage::UNIT_WEIGHT => $weightUnit,
                \UPS\Shipping\Helper\ConstantPackage::LENGTH => $length,
                \UPS\Shipping\Helper\ConstantPackage::WIDTH => $width,
                \UPS\Shipping\Helper\ConstantPackage::HEIGHT => $height,
                \UPS\Shipping\Helper\ConstantPackage::UNIT_DIMENSION => $dimensionUnit,
                'packageItem' => $packageItem,
            ];
        } else {
            $optionIncludeDimension = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_INCLUDE_DIMENSIONS);
            $arrPackageDimension = [
                'merchantKey' => $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY),
                'includeDimensionsInRating' => $optionIncludeDimension,
                'backupRate' => [
                    'serviceKey' => $serviceKey,
                    'rate' => $rate,
                ],
            ];
        }
        $this->runTransferDefaultPackage($arrPackageDimension, $option);
    }

    /**
     * SavePkgdimension runTransferDefaultPackage
     *
     * @param string $arrPackageDimension //The arrPackageDimension
     *
     * @return string $trackingStatus
     */
    public function runTransferDefaultPackage($arrPackageDimension, $option)
    {
        $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
        if (empty($bearerToken)) {
            // re-RegisterToken
            if ($this->resetRegisteredToken()) {
                $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                $bearerSessionToken = $this->checkoutSession->getBearLongToken();
                if ($bearerSessionToken != $bearerToken) {
                    $bearerToken = $bearerSessionToken;
                }
            }
        }
        // call API
        if (!empty($bearerToken)) {
            $dataTransferPackageDimension = [
                'dataPackageDimension' => $arrPackageDimension,
                \UPS\Shipping\Helper\ConstantManager::BEARERTOKEN => $bearerToken
            ];
            $returnDataAPI = $this->apiManager->callTransferDefaultPackage($dataTransferPackageDimension, 1, $option);
            $returnDataAPI = json_decode($returnDataAPI);
            // bearerToken expired
            if (isset($returnDataAPI->error->errorCode) && $returnDataAPI->error->errorCode == '401') {
                if ($this->resetRegisteredToken()) {
                    $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                    $bearerSessionToken = $this->checkoutSession->getBearLongToken();
                    if ($bearerSessionToken != $bearerToken) {
                        $bearerToken = $bearerSessionToken;
                    }
                    $dataTransferPackageDimension[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN] = $bearerToken;
                    $this->apiManager->callTransferDefaultPackage($dataTransferPackageDimension, 1, $option);
                }
            }
        }
    }

    /**
     * SavePkgdimension resetRegisteredToken
     *
     * @return string $trackingStatus
     */
    public function resetRegisteredToken()
    {
        $licenseDefault = $this->licenseModel->getLicenseDefault();
        $resetRegisterToken = false;
        $websiteMerchant = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_WEBSECUREURL);
        $valueSecurityToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN);
        if (empty($valueSecurityToken)) {
            foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
                $this->cacheTypeList->cleanType($type);
            }
            $valueSecurityToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN);
        }
        $arrLicenseParams = [
            "MerchantKey" => $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY),
            "WebstoreUrl" => $websiteMerchant,
            "WebstoreUpsServiceLinkSecurityToken" => $valueSecurityToken,
            "WebstorePlatform" => 'Magento',
            "WebstorePlatformVersion" => \UPS\Shipping\Helper\Config::VERSION_FLATFORM,
            "UpsReadyPluginName" => \UPS\Shipping\Helper\Config::UPS_SHIPPING_MODULE,
            "UpsReadyPluginVersion" => \UPS\Shipping\Helper\Config::VERSION_PLUGIN,
            "WebstoreUpsServiceLinkUrl" => $websiteMerchant . \UPS\Shipping\Helper\Config::API_URL,
            "Username" => $licenseDefault['Username'],
            "Password" => $licenseDefault['Password'],
            "AccessLicenseNumber" => $licenseDefault['AccessLicenseNumber']
        ];
        // Long bearer token
        $responseLongToken = $this->apiHandshake->registeredPluginToken($arrLicenseParams);
        if ($responseLongToken) {
            $responseLongToken = json_decode($responseLongToken);
            // save long token
            $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN,  $responseLongToken->data);
            $this->checkoutSession->setBearLongToken($responseLongToken->data);
            // save UPS_BING_MAPS_KEY
            $responseUpsBingMapsKey = $this->apiAccount->getUpsBingMapsKey($responseLongToken->data);
            if ($responseUpsBingMapsKey) {
                $responseUpsBingMapsKey = json_decode($responseUpsBingMapsKey);
                // save long token
                $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_BING_MAPS_KEY,  $responseUpsBingMapsKey->data);
            }
            $resetRegisterToken = true;
        }

        foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        return $resetRegisterToken;
    }
}
