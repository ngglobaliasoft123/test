<?php
/**
 * Package file
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
use Magento\Eav\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use stdClass;

/**
 * Package class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class PackageDimension
{
    public $pkgLength = 0;
    public $pkgWidth  = 0;
    public $pkgHeight = 0;
    public $dimensionUnit = 'cm';
    public $pkgWeight = 0;
    public $numberOfPackage = 1;
    public $weightUnit = 'kgs';
    public $description = '';

    private $numberOfCartItem = 0;
    private $isIncludeDimension = false;
    private $isCompatibleWeight = false;
    private $isCompatibleDimension = false;
    private $cartPackage;
    private $smallestBox;
    private $heaviestBox;
    private $cartProducts = [];
    private $listDefaultPackage = [];
    private $listProductDimension = [];

    const TYPE_PACKAGE_DEFAULT = 1;
    const TYPE_PRODUCT_DIMENSION = 2;

    protected $modelPackageDefault;
    protected $modelProductDimension;
    protected $scopeConfig;
    protected $checkoutSession;
    protected $storeManager;
    protected $_logger;

    /**
     * Place __construct
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param Package $modelPackageDefault //The openOrderModel
     * @param Package $modelProductDimension //The checkoutSession
     * @param ScopeConfigInterface $scopeConfig //The scopeConfig
     * @param CartInterface $cart //The modelService
     * @param Config $config //The modelAccessorial
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \UPS\Shipping\Model\Package $modelPackageDefault,
        \UPS\Shipping\Model\Dimension $modelProductDimension,
        ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_logger = $logger;
        $this->modelPackageDefault = $modelPackageDefault;
        $this->modelProductDimension = $modelProductDimension;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
    }


    /**
     * PackageDimension getShippingPackage
     *
     * @return array $data
     */
    public function getShippingPackage()
    {
        $this->weightUnit = $this->storeManager->getStore()->getConfig('general/locale/weight_unit');
        if ($this->weightUnit == 'lbs') {
            $this->dimensionUnit = 'inch';
        }
        $packageSettingType = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_PACKAGE_DIMENSIONS);
        $this->_logger->debug("packageSettingType: " . $packageSettingType);
        $includeDimension = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_INCLUDE_DIMENSIONS) == 1 ? true : false;
        $this->_logger->debug("includeDimension: " . print_r($includeDimension, true));

        if ($packageSettingType == false || $packageSettingType == self::TYPE_PACKAGE_DEFAULT) {
            // Get list default package
            $listDefaultPackage = $this->modelPackageDefault->getListPackage();
            $numberOfCartItem = $this->checkoutSession->getQuote()->getItemsQty();

            // Set package dimension (case package default)
            $this->setDefaultPackageDimension($listDefaultPackage, $numberOfCartItem);
        } else if ($packageSettingType == self::TYPE_PRODUCT_DIMENSION) {
            $isIncludeDimension = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_INCLUDE_DIMENSIONS) == 1 ? true : false;
            $listProductDimension = $this->modelProductDimension->getListDimension();
            $this->_logger->debug("includeDimension: " . print_r($isIncludeDimension, true));
            $this->_logger->debug("getListPackage: " . print_r($listProductDimension, true));

            // Set cart package base on cart order item
            $this->setCartPackage();
            // Set package dimension (case product dimension)
            $this->setProductPackageDimension($isIncludeDimension, $listProductDimension);
        }
        $listPackages = [];
        for ($i = 1; $i <= $this->numberOfPackage; $i++) {
            $package = [];
            $package['length'] = strval($this->pkgLength);
            $package['width'] = strval($this->pkgWidth);
            $package['height'] = strval($this->pkgHeight);
            $package['unit_dimension'] = $this->dimensionUnit;
            $package['weight'] = strval($this->pkgWeight);
            $package['unit_weight'] = $this->weightUnit;
            $listPackages[] = $package;
        }
        $this->_logger->debug("listPackages: " . print_r($listPackages, true));

        return $listPackages;
    }

    /**
     * setDefaultPackageDimension
     *
     * @param array $listDefaultPackage : list default package
     * @param int $numberOfCartItem : number of item in cart
     */
    public function setDefaultPackageDimension($listDefaultPackage, $numberOfCartItem) {
        // Set variable
        $this->listDefaultPackage = $listDefaultPackage;
        $this->numberOfCartItem = $numberOfCartItem;
        // Check empty array data
        if (empty($this->listDefaultPackage)) {
            return;
        }
        $packageIndex = 0;
        $numberOfItem = 1;
        // Set package index (get max setting number < cart number)
        foreach ($this->listDefaultPackage as $key => $defaultPackage) {
            if ($defaultPackage['unit_dimension'] != $this->dimensionUnit || $defaultPackage['unit_weight'] != $this->weightUnit) {
                continue;
            }
            if ($defaultPackage['package_number'] <= $numberOfCartItem && $numberOfItem <= $defaultPackage['package_number']) {
                $packageIndex = $key;
                $numberOfItem = $defaultPackage['package_number'];
            }
        }
        // Set package dimension
        $this->pkgLength = $this->listDefaultPackage[$packageIndex]['length'];
        $this->pkgWidth  = $this->listDefaultPackage[$packageIndex]['width'];
        $this->pkgHeight = $this->listDefaultPackage[$packageIndex]['height'];
        $this->pkgWeight = $this->listDefaultPackage[$packageIndex]['weight'];
        $this->description = 'Get default package dimension';
    }

    /**
     * setProductPackageDimension
     *
     * @param boolean $isIncludeDimension : is include dimension
     * @param array   $listProductDimension : list product dimension
     */
    public function setProductPackageDimension($isIncludeDimension, $listProductDimension) {
        // Set variable
        $this->isIncludeDimension = $isIncludeDimension;
        $this->listProductDimension = $listProductDimension;
        // Check empty array data
        if (empty($this->listProductDimension)) {
            return;
        }
        // Determine smallest package size to largest of user defined package
        $maxWeightIndex = 0;
        $maxWeight = 0;
        $smallestVolume = 0;
        $smallestVolumeWeight = 0;
        $isBoxSelected = false;
        foreach ($this->listProductDimension as $key => $productDimension) {
            if ($productDimension['weight'] > $maxWeight) {
                $maxWeight = $productDimension['weight'];
                $maxWeightIndex = $key;
            }
            $compatibleWeightFlg = false;
            $compatibleDimensionFlg = false;
            $volume = $productDimension['length'] * $productDimension['width'] * $productDimension['height'];
            list ($minSide, $medianSide, $maxSide) = $this->getPackageSide([
                $productDimension['length'],
                $productDimension['width'],
                $productDimension['height']
            ]);
            // Check compatible weight
            if ($this->cartPackage->weight <= $productDimension['weight']) {
                $compatibleWeightFlg = true;
            }
            // Check compatible dimension
            if ($this->isIncludeDimension) {
                if ($this->cartPackage->minSide <= $minSide && $this->cartPackage->medianSide <= $medianSide
                    && $this->cartPackage->maxSide <= $maxSide && $this->cartPackage->volume <= $volume
                ) {
                    $compatibleDimensionFlg = true;
                }
            }
            // Set package weight
            if ($compatibleWeightFlg) {
                // If compatible dimension, get smallest box
                if (!$this->isIncludeDimension || $compatibleDimensionFlg) {
                    // Get smallest box
                    if ($volume < $smallestVolumeWeight || $smallestVolumeWeight == 0) {
                        $smallestVolumeWeight = $volume;
                        // Set package dimension
                        $this->pkgLength  = $productDimension['length'];
                        $this->pkgWidth  = $productDimension['width'];
                        $this->pkgHeight  = $productDimension['height'];
                        $this->numberOfPackage = 1;
                        $this->pkgWeight = $this->cartPackage->weight;
                        $this->smallestBox = $productDimension;
                        $this->smallestBox['volume'] = $productDimension['length'] * $productDimension['width'] * $productDimension['height'];
                    }
                    $this->description = "Weight is compatible, dimension is compatible or not include (get smallest box)";
                    $isBoxSelected = true;
                }
            } else if ($this->isIncludeDimension && $compatibleDimensionFlg) {
                // Get smallest box
                if ($volume < $smallestVolume || $smallestVolume == 0) {
                    $smallestVolume = $volume;
                    // Set package dimension
                    $this->pkgLength  = $productDimension['length'];
                    $this->pkgWidth  = $productDimension['width'];
                    $this->pkgHeight  = $productDimension['height'];
                    if (isset($productDimension['weight']) && intval($productDimension['weight']) > 0) {
                        $this->numberOfPackage = ceil($this->cartPackage->weight / $productDimension['weight']);
                    }
                    if ($this->numberOfPackage > 0) {
                        $this->pkgWeight = round($this->cartPackage->weight / $this->numberOfPackage, 2);
                    }
                    $this->smallestBox = $productDimension;
                    $this->smallestBox['volume'] = $productDimension['length'] * $productDimension['width'] * $productDimension['height'];
                }
                $this->description = "Weight is not compatible, dimension is compatible (get smallest box with multi package)";
                $isBoxSelected = true;
            }

            if ($compatibleWeightFlg) {
                $this->isCompatibleWeight = true;
            }
            if ($compatibleDimensionFlg) {
                $this->isCompatibleDimension = true;
            }
        }
        if (!$isBoxSelected) {
            // Weight is compatible, dimension is not compatible
            if ($this->isCompatibleWeight) {
                $this->pkgLength = $this->cartPackage->maxSide;
                $this->pkgWidth = $this->cartPackage->medianSide;
                $this->pkgHeight = $this->cartPackage->minSide;
                $this->pkgWeight = $this->cartPackage->weight;
                $this->numberOfPackage = 1;
                $this->description = "Weight is compatible, dimension is not compatible (get custom dimension)";
            } else {
                $packageHeaviest = $this->listProductDimension[$maxWeightIndex];
                // Weight is not compatible and not include
                if (!$this->isIncludeDimension) {
                    $this->pkgLength = $packageHeaviest['length'];
                    $this->pkgWidth = $packageHeaviest['width'];
                    $this->pkgHeight = $packageHeaviest['height'];
                    $this->description = "Weight is not compatible, not include dimension (get heaviest box)";
                } else if (!$this->isCompatibleDimension) {
                    // Weight is not compatible and dimension is not compatible
                    $this->pkgLength = $this->cartPackage->maxSide;
                    $this->pkgWidth = $this->cartPackage->medianSide;
                    $this->pkgHeight = $this->cartPackage->minSide;
                    $this->description = "Weight and dimension are not compatible (get custom dimension with multi package which one package is compatible with heaviest box)";
                }
                if (isset($packageHeaviest['weight']) && intval($packageHeaviest['weight']) > 0) {
                    $this->numberOfPackage = ceil($this->cartPackage->weight / $packageHeaviest['weight']);
                    if ($this->numberOfPackage > 0) {
                        $this->pkgWeight = round($this->cartPackage->weight / $this->numberOfPackage, 2);
                    }
                }
                $this->heaviestBox = $packageHeaviest;
            }
        }

        $this->_logger->debug("description: " . print_r($this->description, true));
    }

    /**
     * setCartPackage
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function setCartPackage()
    {
        $cartProducts = $this->checkoutSession->getQuote()->getAllItems();

        $minSide = 0;
        $medianSide = 0;
        $maxSide = 0;
        $volume = 0;
        $weight = 0;
        // Get product item in cart
        foreach ($cartProducts as $product) {
            // Get product info
            $productItem = [
                'id_product' => $product->getProductId(),
                'quantity' => $product->getQty(),
                'length' => $product->getProduct()->getData('ups_dimensions_length'),
                'width' => $product->getProduct()->getData('ups_dimensions_width'),
                'height' => $product->getProduct()->getData('ups_dimensions_height'),
                'weight' => ($product->getIsVirtual() == 1 || $product->getProduct()->getTypeId() != 'simple') ? 0 : $product->getWeight(),
            ];

            $this->_logger->debug("getTypeId: " . print_r($product->getTypeId(), true));
            $this->_logger->debug("getTypeIdssss: " . print_r($product->getProduct()->getTypeId(), true));
            $this->_logger->debug("isVirtual: " . print_r($product->getIsVirtual(), true));
            $this->_logger->debug("cartProducts: " . print_r($product->debug(), true));

            $this->cartProducts[] = $productItem;
            // Get product volume
            $productVolume = $productItem['length'] * $productItem['width'] * $productItem['height'];
            // Get package side (min, median, max)
            list ($productMinSide, $productMedianSide, $productMaxSide) = $this->getPackageSide([
                $productItem['length'],
                $productItem['width'],
                $productItem['height']
            ]);
            if ($minSide < $productMinSide) {
                $minSide = $productMinSide;
            }
            if ($medianSide < $productMedianSide) {
                $medianSide = $productMedianSide;
            }
            if ($maxSide < $productMaxSide) {
                $maxSide = $productMaxSide;
            }
            if ($productItem['quantity'] > 1) {
                $volume += $productItem['quantity'] * $productVolume;
                $weight += $productItem['quantity'] * $productItem['weight'];
            } else {
                $volume += $productVolume;
                $weight += $productItem['weight'];
            }
        }
        $cartPackage = new \stdClass();
        $cartPackage->minSide = round($minSide, 2);
        $cartPackage->medianSide = round($medianSide, 2);
        $cartPackage->maxSide = round($maxSide, 2);
        $cartPackage->volume = round($volume, 2);
        $cartPackage->weight = round($weight, 2);


        $this->_logger->debug("cartPackage: " . print_r($cartPackage, true));
        $this->cartPackage = $cartPackage;
    }

    /**
     * getPackageSide
     *
     * @param array $sides
     * @return array
     */
    private function getPackageSide($sides) {
        asort($sides);
        $minSide  = array_shift($sides);
        $medianSide = array_shift($sides);
        $maxSide = array_shift($sides);
        return [
            $minSide,
            $medianSide,
            $maxSide
        ];
    }
}
