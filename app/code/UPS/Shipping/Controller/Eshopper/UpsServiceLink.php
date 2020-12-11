<?php
/**
 * UpsServiceLink file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Controller\Eshopper;
use Magento\Framework\App\Config\ScopeConfigInterface;
/**
 * UpsServiceLink class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class UpsServiceLink extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $scopeConfig;
    protected $configWriter;
    protected $cacheTypeList;

    /**
     * UpsServiceLink __construct
     *
     * @param string $context           //The context
     * @param string $resultJsonFactory //The resultJsonFactory
     * @param string $configWriter      //The configWriter
     * @param string $scopeConfig       //The scopeConfig
     * @param string $cacheTypeList     //The cacheTypeList
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
        $this->cacheTypeList = $cacheTypeList;
        parent::__construct($context);
    }

    /**
     * UpsServiceLink execute
     *
     * @return boolean
     */
    public function execute()
    {
        $getSecurityToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN);
        $params = $this->getRequest()->getContent();
        if (!empty($params)) {
            $params = json_decode($params);
            if (isset($params->PreRegisteredPluginToken) && isset($params->UpsServiceLinkSecurityToken)
                && isset($params->Command) && ($params->Command == \UPS\Shipping\Helper\Config::PUSH_PRE_REGISTRATION_TOKEN)
            ) {
                $value = $params->PreRegisteredPluginToken;
                $this->configWriter->save(\UPS\Shipping\Helper\Config::PRE_REGISTERED_PLUGIN_TOKEN, $value);
                foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
                    $this->cacheTypeList->cleanType($type);
                }
            }
        }
        return $this->resultJsonFactory->create();
    }
}
