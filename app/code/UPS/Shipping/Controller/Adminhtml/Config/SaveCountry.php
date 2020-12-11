<?php
/**
 * SaveCountry file
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
 * SaveCountry class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class SaveCountry extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $configWriter;
    protected $cacheTypeList;
    protected $authAdminUserSession;
    protected $configDataModel;

    /**
     * SaveCountry __construct
     *
     * @param string $context           //The context
     * @param string $resultPageFactory //The resultPageFactory
     * @param string $configWriter      //The configWriter
     * @param string $cacheTypeList     //The cacheTypeList
     * @param string $authSession       //The authSession
     * @param string $ConfigData        //The ConfigData
     *
     * @return null
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Backend\Model\Auth\Session $authSession,
        \UPS\Shipping\Model\ConfigData $ConfigData
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->authAdminUserSession = $authSession;
        $this->configDataModel = $ConfigData;
        parent::__construct($context);
    }
    /**
     * SaveCountry execute
     *
     * @return null
     */
    public function execute()
    {
        // get data form
        $dataForm = $this->getRequest()->getParams();
        // check id country
        if (isset($dataForm['country_id'])) {
            $countryCode = \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE;
            $this->configWriter->save($countryCode, $dataForm['country_id'], ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
            if (\UPS\Shipping\Helper\Config::CONFIG_COUNTRY_US == $dataForm['country_id']) {
                $userAdminId = $this->authAdminUserSession->getUser()->getUserId();
                $value = \UPS\Shipping\Helper\Config::COUNTRY_US;
                $this->configDataModel->updateInterfaceLocale($value, $userAdminId);
                //us: the default service/upsshipping/set_default is 0
                $this->configWriter->save(\UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_SET_DEFAULT, 0);
            } else {
                $this->configWriter->save(\UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_DELIVERY_SET_DEFAULT, 1);
            }
            $this->cacheTypeList->cleanType('config');
            // redirect to TermCondition
            $this->_redirect('upsshipping/config/termcondition');
        } else {
            // redirect to country
            $this->_redirect('upsshipping/config/country');
        }
    }
}
