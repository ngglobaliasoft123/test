<?php
/**
 * SaveCashOnDelivery file
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
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * SaveCashOnDelivery class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class SaveCashOnDelivery extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $configWriter;
    protected $cacheTypeList;
    //variable constan config Ap
    const CONFIGAP = 'configAP';
    /**
     * SaveCashOnDelivery __construct
     *
     * @param string $context           //The context
     * @param string $resultPageFactory //The resultPageFactory
     * @param string $configWriter      //The configWriter
     * @param string $cacheTypeList     //The cacheTypeList
     *
     * @return null
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        parent::__construct($context);
    }
    /**
     * SaveCashOnDelivery execute
     *
     * @return null
     */
    public function execute()
    {
        //get data form
        $dataForm = $this->getRequest()->getParams();
        $cashOnActive = \UPS\Shipping\Helper\Config::CASH_ON_DELIVERY_UPS_SHIPPING_OPTION_ACTIVE;
        if (!isset($dataForm[self::CONFIGAP][$cashOnActive])) {
            $dataForm[self::CONFIGAP][$cashOnActive] = 0;
        } else {
            $dataForm[self::CONFIGAP][$cashOnActive] = 1;
        }
        //hanlding ap
        foreach ($dataForm[self::CONFIGAP] as $key => $value) {
            $this->saveHanldingAp($key, $value);
        }
        //save COD = 1 into database
        $this->configWriter->save(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_CASH_ON_DELIVERY, 1);
        foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        //redirect to package dimension
        $this->_redirect('upsshipping/config/pkgdimension');
    }

    /**
     * SaveCashOnDelivery saveHanldingAp
     *
     * @param string $key   //The key
     * @param string $value //The value
     *
     * @return null
     */
    public function saveHanldingAp($key, $value)
    {
        $this->configWriter->save($key, $value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
    }
}
