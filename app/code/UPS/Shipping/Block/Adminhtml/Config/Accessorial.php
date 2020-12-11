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
namespace UPS\Shipping\Block\Adminhtml\Config;
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
class Accessorial extends \Magento\Framework\View\Element\Template
{
    protected $scopeConfig;
    protected $modelAccessorial;
    protected $formKey;

    /**
     * Accessorial __construct
     *
     * @param string $context          //The context
     * @param string $modelAccessorial //The modelAccessorial
     * @param string $formKey          //The formKey
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \UPS\Shipping\Model\Accessorial $modelAccessorial,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->modelAccessorial = $modelAccessorial;
        $this->formKey = $formKey;
        parent::__construct($context);
    }

    /**
     * Accessorial getListAccessorial
     *
     * @return array $data
     */
    public function getListAccessorial()
    {
        return $this->modelAccessorial->getListAccessorial();
    }

    /**
     * Country getFormKey
     *
     * @return array $serviceData
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}
