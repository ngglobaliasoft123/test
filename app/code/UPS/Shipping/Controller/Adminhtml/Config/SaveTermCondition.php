<?php
/**
 * SaveTermCondition file
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
 * SaveTermCondition class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class SaveTermCondition extends \Magento\Framework\App\Action\Action
{
    protected $pageFactory;
    protected $configWriter;
    protected $cacheTypeList;
    /**
     * SaveTermCondition __construct
     *
     * @param string $context       //The context
     * @param string $pageFactory   //The pageFactory
     * @param string $configWriter  //The configWriter
     * @param string $cacheTypeList //The cacheTypeList
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
    ) {
        $this->pageFactory = $pageFactory;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        parent::__construct($context);
    }
    /**
     * SaveTermCondition execute
     *
     * @return null
     */
    public function execute()
    {
        //Get data from form
        $dataForm = $this->getRequest()->getParams();
        if (isset($dataForm['accept_term_condition'])) {
            $carrierCondition = \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_ACCEPT_TERM_CONDITION;
            $showCondition = \UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_SHOW_TERM_CONDITION;
            $this->configWriter->save($carrierCondition, 1, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
            $this->configWriter->save($showCondition, 0, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
            $types = ['config', 'layout', 'block_html', 'full_page'];
            foreach ($types as $type) {
                $this->cacheTypeList->cleanType($type);
            }
            //Redirect to account screen
            $this->_redirect('upsshipping/config/account');
        } else {
            //Redirect to term condition screen
            $this->_redirect('upsshipping/config/termcondition');
        }
    }
}
