<?php
/**
 * CommonFunction file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Helper;
/**
 * CommonFunction class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
Class CommonFunction extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;
    protected $apiHandshake;
    protected $configWriter;
    protected $cacheTypeList;
    protected $checkoutSession;

    /**
     * Account __construct
     *
     * @param string $context         //The context
     * @param string $apiHandshake    //The apiHandshake
     * @param string $configWriter    //The configWriter
     * @param string $cacheTypeList   //The cacheTypeList
     * @param string $checkoutSession //The checkoutSession
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \UPS\Shipping\API\Handshake $apiHandshake,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->apiHandshake = $apiHandshake;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    /**
     * TermCondition callAPILicense
     *
     * @return array $data
     */
    public function callAPIHandshake()
    {
        $resultCallHandshakeApi = false;
        $getMerchantKey = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY);
        $websiteMerchant = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_WEBSECUREURL);
        $valueSecurityToken = $this->apiHandshake->generatePass(32);
        $this->checkoutSession->setHandshakeKey($valueSecurityToken);
        if ($this->apiHandshake->callAPIHandshake($websiteMerchant, $getMerchantKey, $valueSecurityToken)) {
            $this->checkoutSession->setSecurityToken($valueSecurityToken);
            $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN,  $valueSecurityToken);
            $this->checkoutSession->setSecurityTokenValue($valueSecurityToken);
            foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
                $this->cacheTypeList->cleanType($type);
            }
            $resultCallHandshakeApi = true;
        }
        return $resultCallHandshakeApi;
    }
}
