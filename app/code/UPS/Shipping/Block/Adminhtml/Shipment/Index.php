<?php
/**
 * Index file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Block\Adminhtml\Shipment;
/**
 * Index class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Index extends \UPS\Shipping\Block\Adminhtml\Shipment\Manage
{
    protected $shipmentModel;
    protected $numberItemOnPage;
    protected $modelCurrency;
    protected $modelStore;
    protected $formKey;
    protected $scopeConfig;

    /**
     * Index __construct
     *
     * @param string $context       //The context
     * @param string $modelCurrency //The modelCurrency
     * @param string $shipmentModel //The shipmentModel
     * @param string $formKey       //The formKey
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Model\Currency $modelCurrency,
        \UPS\Shipping\Model\Shipment $shipmentModel,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->shipmentModel = $shipmentModel;
        $this->modelCurrency = $modelCurrency;
        $this->modelStore = $context->getStoreManager();
        $this->scopeConfig = $context->getScopeConfig();
        $this->formKey = $formKey;
        parent::__construct($context);
    }

    /**
     * Index getAllShipments
     *
     * @return array $data
     */
    public function getAllShipments()
    {
        return $this->shipmentModel->getAllShipments();
    }

    /**
     * Account getCountryCode
     *
     * @return array $data
     */
    public function getCountryCode()
    {
        return $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE);
    }

    /**
     * Index getNumberPages
     *
     * @return array $data
     */
    public function getNumberPages()
    {
        $this->numberItemOnPage = \UPS\Shipping\Helper\Config::PAGINATION_NUMBER_ITEM_ON_PAGE;
        return $this->shipmentModel->getNumberPages($this->numberItemOnPage);
    }

    /**
     * Index getListShipments
     *
     * @return array $data
     */
    public function getListShipments()
    {
        $this->numberItemOnPage = \UPS\Shipping\Helper\Config::PAGINATION_NUMBER_ITEM_ON_PAGE;
        $request = $this->getRequest()->getParams();
        if (isset($request[\UPS\Shipping\Helper\ConstantShipment::PAGE_ID])) {
            $offset = ($request[\UPS\Shipping\Helper\ConstantShipment::PAGE_ID] - 1) * $this->numberItemOnPage;
        } else {
            $offset = 0;
        }
        return $this->shipmentModel->getListShipments($this->numberItemOnPage, $offset, $request);
    }

    /**
     * Index getCurrentPage
     *
     * @return array $data
     */
    public function getCurrentPage()
    {
        $pageId = $this->getRequest()->getParam(\UPS\Shipping\Helper\ConstantShipment::PAGE_ID);
        if (empty($pageId)) {
            $pageId = 1;
        }
        return $pageId;
    }

    /**
     * Index getShipmentTableHeader
     *
     * @return array $data
     */
    public function getShipmentTableHeader()
    {
        $request = $this->getRequest()->getParams();
        return [
            \UPS\Shipping\Helper\ConstantShipment::SHIPMENT_ID => [
                'text' => __('ID Shipment'),
                'sort' => (isset($request[\UPS\Shipping\Helper\ConstantShipment::SHIPMENT_ID])
                && $request[\UPS\Shipping\Helper\ConstantShipment::SHIPMENT_ID] == 'DESC') ? 'ASC' : 'DESC',
                \UPS\Shipping\Helper\ConstantShipment::MIN_WIDTH => ''
            ],
            \UPS\Shipping\Helper\ConstantShipment::TRACKING_NUMBER => [
                'text' => __('Tracking Number'),
                'sort' => (isset($request[\UPS\Shipping\Helper\ConstantShipment::TRACKING_NUMBER])
                && $request[\UPS\Shipping\Helper\ConstantShipment::TRACKING_NUMBER] == 'DESC') ? 'ASC' : 'DESC',
                \UPS\Shipping\Helper\ConstantShipment::MIN_WIDTH => ''
            ],
            \UPS\Shipping\Helper\ConstantShipment::ORDER_ID => [
                'text' => __('Order ID'),
                'sort' => (isset($request[\UPS\Shipping\Helper\ConstantShipment::ORDER_ID])
                && $request[\UPS\Shipping\Helper\ConstantShipment::ORDER_ID] == 'DESC') ? 'ASC' : 'DESC',
                \UPS\Shipping\Helper\ConstantShipment::MIN_WIDTH => ''
            ],
            \UPS\Shipping\Helper\ConstantShipment::CREATED_AT_DATE => [
                'text' => __('Date'),
                'sort' => (isset($request[\UPS\Shipping\Helper\ConstantShipment::CREATED_AT_DATE])
                && $request[\UPS\Shipping\Helper\ConstantShipment::CREATED_AT_DATE] == 'DESC') ? 'ASC' : 'DESC',
                \UPS\Shipping\Helper\ConstantShipment::MIN_WIDTH => ''
            ],
            \UPS\Shipping\Helper\ConstantShipment::CREATED_AT_TIME => [
                'text' => __('Time'),
                'sort' => (isset($request[\UPS\Shipping\Helper\ConstantShipment::CREATED_AT_TIME])
                && $request[\UPS\Shipping\Helper\ConstantShipment::CREATED_AT_TIME] == 'DESC') ? 'ASC' : 'DESC',
                \UPS\Shipping\Helper\ConstantShipment::MIN_WIDTH => ''
            ],
            \UPS\Shipping\Helper\ConstantShipment::DELIVERY_ADDRESS => [
                'text' => __('Delivery Address'),
                'sort' => (isset($request[\UPS\Shipping\Helper\ConstantShipment::DELIVERY_ADDRESS])
                && $request[\UPS\Shipping\Helper\ConstantShipment::DELIVERY_ADDRESS] == 'DESC') ? 'ASC' : 'DESC',
                \UPS\Shipping\Helper\ConstantShipment::MIN_WIDTH => ''
            ],
            \UPS\Shipping\Helper\ConstantShipment::ESTIMATED_SHIPPING_FEE => [
                'text' => __('Estimated Shipping Fee'),
                'sort' => (isset($request[\UPS\Shipping\Helper\ConstantShipment::ESTIMATED_SHIPPING_FEE])
                && $request[\UPS\Shipping\Helper\ConstantShipment::ESTIMATED_SHIPPING_FEE] == 'DESC') ? 'ASC' : 'DESC',
                \UPS\Shipping\Helper\ConstantShipment::MIN_WIDTH => ''
            ],
        ];
    }

    /**
     * Index getSortColumn
     *
     * @return array $data
     */
    public function getSortColumn()
    {
        $request = $this->getRequest()->getParams();
        foreach ($request as $key => $value) {
            if ($key != \UPS\Shipping\Helper\ConstantShipment::PAGE_ID && $key != 'key') {
                return [$key, $value];
            }
        }
        return [];
    }

    /**
     * Index shipmentPagination
     *
     * @return array $data
     */
    public function shipmentPagination()
    {
        return $this->pagination($this->getNumberPages(), $this->getCurrentPage());
    }

    /**
     * Index getDeliveryAddress
     *
     * @param string $shipment //The shipment
     *
     * @return array $data
     */
    public function getDeliveryAddress($shipment)
    {
        $address = [];
        $deliveryAddress = '';
        (!empty($shipment['address1'])) ? array_push($address, $shipment['address1']) : null;
        (!empty($shipment['address2'])) ? array_push($address, $shipment['address2']) : null;
        (!empty($shipment['address3'])) ? array_push($address, $shipment['address3']) : null;
        (!empty($shipment['city'])) ? array_push($address, $shipment['city']) : null;
        $deliveryAddress = implode('<br/>', $address);
        return $deliveryAddress;
    }

    /**
     * Index getCurrencySymbol
     *
     * @return array $data
     */
    public function getCurrencySymbol()
    {
        // get Current Currency Code
        $currencyCode = $this->modelStore->getStore()->getCurrentCurrencyCode();
        // get Currency Symbol
        $currencySymbol = $this->modelCurrency->load($currencyCode)->getCurrencySymbol();
        if (empty($currencySymbol)) {
            $currencySymbol = $currencyCode;
        }
        return $currencySymbol;
    }

    /**
     * Index getCurrencyCode
     *
     * @return array $data
     */
    public function getCurrencyCode()
    {
        $countryCode = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE);
        $listCurrencys = \UPS\Shipping\Helper\Config::LISTCURRENCYS;
        $currencyCountry = $listCurrencys[$countryCode];
        $currencyCode = 'USD';
        if (!empty($currencyCountry)) {
            $currencyCode = $currencyCountry[1];
        }
        return $currencyCode;
    }

    /**
     * Index getFormKey
     *
     * @return array $serviceData
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}
