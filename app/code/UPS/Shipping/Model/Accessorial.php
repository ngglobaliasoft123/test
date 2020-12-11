<?php
/**
 * Accessorial file
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
 * Accessorial class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Accessorial extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = \UPS\Shipping\Helper\Config::UPS_SHIPPING_ACCESSORIAL;

    protected $cacheTag = \UPS\Shipping\Helper\Config::UPS_SHIPPING_ACCESSORIAL;
    protected $eventPrefix = \UPS\Shipping\Helper\Config::UPS_SHIPPING_ACCESSORIAL;

    /**
     * Accessorial _construct
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('UPS\Shipping\Model\ResourceModel\Accessorial');
    }

    /**
     * Accessorial getAllListAccessorial
     *
     * @return array $data
     */
    public function getAllListAccessorial()
    {
        return $this->getResource()->getAllListAccessorial();
    }

    /**
     * Accessorial getListAccessorial
     *
     * @return array $data
     */
    public function getListAccessorial()
    {
        return $this->getResource()->getListAccessorial();
    }

    /**
     * Accessorial getListAccessorialActive
     *
     * @param bool $satDeliFlg
     * @return array $data
     */
    public function getListAccessorialActive($satDeliFlg = false)
    {
        $arrListAccessorials = [];
        if ($satDeliFlg === true) {
            $listAccessorials = $this->getResource()->getListAccessorialActiveAndSatDeli();
        } else {
            $listAccessorials = $this->getResource()->getListAccessorialActive();
        }
        if (!empty($listAccessorials)) {
            foreach ($listAccessorials as $accessorial) {
                $arrListAccessorials[$accessorial['accessorial_key']] = $accessorial['accessorial_name'];
            }
        }
        return json_encode($arrListAccessorials);
    }

    /**
     * Accessorial updateAccessorial
     *
     * @param string $id           //The id
     * @param string $showShipping //The showShipping
     *
     * @return array $data
     */
    public function updateAccessorial($id, $showShipping)
    {
        return $this->getResource()->updateAccessorial($id, $showShipping);
    }
}
