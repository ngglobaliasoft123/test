<?php
/**
 * Archived file
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

use Magento\Framework\Stdlib\DateTime\DateTime;
/**
 * Archived class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Archived extends \UPS\Shipping\Block\Adminhtml\Shipment\Manage
{
    protected $scopeConfig;
    protected $openorders;
    protected $numberItemOnPage = \UPS\Shipping\Helper\Config::PAGINATION_NUMBER_ITEM_ON_PAGE;
    protected $page;
    protected $salesOrder;
    protected $timezone;
    protected $modelCurrency;
    protected $modelStore;
    protected $dateTime;
    protected $formKey;

    /**
     * Archived __construct
     *
     * @param string   $context       //The context
     * @param string   $openorders    //The openorders
     * @param string   $salesModel    //The salesModel
     * @param string   $modelCurrency //The modelCurrency
     * @param DateTime $dateTime      //The Datatime
     * @param string   $formKey       //The formKey
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \UPS\Shipping\Model\Order $openorders,
        \Magento\Sales\Model\Order $salesModel,
        \Magento\Directory\Model\Currency $modelCurrency,
        DateTime $dateTime,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->openorders = $openorders;
        $this->salesOrder = $salesModel;
        $this->timezone = $context->getLocaleDate();
        $this->modelCurrency = $modelCurrency;
        $this->modelStore = $context->getStoreManager();
        $this->dateTime = $dateTime;
        $this->formKey = $formKey;
        parent::__construct($context);
    }

    /**
     * Archived getListArchivedOrders
     *
     * @return array $data
     */
    public function getListArchivedOrders()
    {
        $this->page = $this->getRequest()->getParam('page_id');
        if (empty($this->page)) {
            $this->page = 1;
        }
        $params = $this->getRequest()->getParams();
        $listOrder = $this->openorders->getListArchivedOrders($this->numberItemOnPage, $this->page, $params);
        foreach ($listOrder as $index => $archivedOrder) {
            // load archived Order
            $order = $this->getOrderData($archivedOrder);
            $newDate = $this->dateTime->date('m/d/y H:i:s', $this->dateTime->timestamp($order->getCreatedAt()));
            $listOrder[$index]['created_at'] = $newDate;
            // hanlding product
            $productName = [];
            foreach ($order->getItemsCollection() as $item) {
                if ($item->getParentItemId() == '' || $item->getParentItemId() == null) {
                    $checkParent = $this->openorders->checkParentItem($item->getItemId());
                    $productNameString = round($item->getQtyOrdered()) . ' x ' . $item->getProduct()->getName();
                    $countCheckParent = $this->countCheckParent($checkParent);
                    if ($countCheckParent == 0) {
                        array_push($productName, $productNameString);
                    }
                } else {
                    array_push($productName, $productNameString);
                }
            }
            $listOrder[$index]['productName'] = implode(', ', $productName);
            $countProductName = $this->countCheckParent($productName);
            if ($countProductName > 3) {
                $productString = trim($productName[0]) . \UPS\Shipping\Helper\ConstantArchived::STYLE_BR
                . trim($productName[1]) . \UPS\Shipping\Helper\ConstantArchived::STYLE_BR
                . trim($productName[2]) . ', ...';
            } else {
                $productString = implode(\UPS\Shipping\Helper\ConstantArchived::STYLE_BR, $productName);
            }
            $newDate = $this->dateTime->date('m/d/y H:i:s', $this->dateTime->timestamp($order->getCreatedAt()));
            $listOrder[$index]['created_at'] = $newDate;
            $listOrder[$index]['listProductName'] = $productString;
            // handling service name
            if (isset($archivedOrder[\UPS\Shipping\Helper\ConstantArchived::SERVICE_NAME])) {
                $serviceTrim = trim($archivedOrder[\UPS\Shipping\Helper\ConstantArchived::SERVICE_NAME]);
                $listOrder[$index]['service_name_info'] = $this->getServiceName($serviceTrim, $archivedOrder);
            }
            // hanlding address
            $addressFormat = [];
            if ($listOrder[$index]['service_type'] == 'AP') {
                $this->handAddress($addressFormat, $listOrder, $index);
            } else {
                $this->handShippingAddress($addressFormat, $listOrder, $index, $order);
            }
            $listOrder[$index]['AddressFormat'] = implode('<br/>', $addressFormat);
            if ($order->getPayment()->getMethod() == \UPS\Shipping\Helper\Config::COD_MAGENTO) {
                $cod = 1;
            } else {
                $cod = 0;
            }
            $listOrder[$index]['cod'] = $cod;
        }
        return $listOrder;
    }

    /**
     * Archived countCheckParent
     *
     * @param string $checkParent //The checkParent
     *
     * @return array $Order
     */
    public function countCheckParent($checkParent)
    {
        return count($checkParent);
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
     * Archived getOrderData
     *
     * @param string $archivedOrder //The archivedOrder
     *
     * @return array $Order
     */
    public function getOrderData($archivedOrder)
    {
        return $this->salesOrder->load($archivedOrder['order_id_magento']);
    }

    /**
     * Archived handAddress
     *
     * @param string $addressFormat //The addressFormat
     * @param string $listOrder     //The listOrder
     * @param string $index         //The index
     *
     * @return array $serviceData
     */
    public function handAddress(&$addressFormat, $listOrder, $index)
    {
        if ($listOrder[$index]['ap_name'] != '') {
            $addressFormat[] = $listOrder[$index]['ap_name'];
        }
        if ($listOrder[$index]['ap_address1'] != '') {
            $addressFormat[] = $listOrder[$index]['ap_address1'];
        }
        if ($listOrder[$index]['ap_address2'] != '') {
            $addressFormat[] = $listOrder[$index]['ap_address2'];
        }
        if ($listOrder[$index]['ap_address3'] != '') {
            $addressFormat[] = $listOrder[$index]['ap_address3'];
        }
        if ($listOrder[$index]['ap_city'] != '') {
            $addressFormat[] = $listOrder[$index]['ap_city'];
        }
    }

    /**
     * Archived handShippingAddress
     *
     * @param string $addressFormat //The addressFormat
     * @param string $listOrder     //The listOrder
     * @param string $index         //The index
     * @param string $order         //The order
     *
     * @return null
     */
    public function handShippingAddress(&$addressFormat, $listOrder, $index, $order)
    {
        if ($order->getShippingAddress()->getStreetLine(1) != '') {
            $addressFormat[] = $order->getShippingAddress()->getStreetLine(1);
        }
        if ($order->getShippingAddress()->getStreetLine(2) != '') {
            $addressFormat[] = $order->getShippingAddress()->getStreetLine(2);
        }
        if ($order->getShippingAddress()->getStreetLine(3) != '') {
            $addressFormat[] = $order->getShippingAddress()->getStreetLine(3);
        }
        if ($listOrder[$index]['city'] != '') {
            $addressFormat[] = $listOrder[$index]['city'];
        }
    }

    /**
     * Archived getServiceName
     *
     * @param string $serviceTrim   //The serviceTrim
     * @param string $archivedOrder //The archivedOrder
     *
     * @return array $serviceData
     */
    public function getServiceName($serviceTrim, $archivedOrder)
    {
        $serviceData = '';
        $symbolService = $archivedOrder['service_symbol'];
        if ($serviceTrim == 'UPS Access Point Economy') {
            $serviceData = 'UPS Access Point' . $symbolService . ' Economy';
        } elseif ($serviceTrim == 'UPS Standard') {
            $serviceData = 'UPS' . $symbolService . ' Standard';
        } elseif ($serviceTrim == 'UPS Express 12:00') {
            $serviceData = 'UPS Express 12:00';
        } elseif ($serviceTrim == 'UPS Ground') {
            $serviceData = 'UPS' . $symbolService . ' Ground';
        } elseif ($serviceTrim == 'UPS Next Day Air Early') {
            $serviceData = 'UPS Next Day Air' . $symbolService . ' Early';
        } elseif ($serviceTrim == 'UPS Standard - Saturday Delivery') {
            $serviceData = 'UPS' . $symbolService . ' Standard - Saturday Delivery';
        } elseif ($serviceTrim == 'UPS Express - Saturday Delivery') {
            $serviceData = 'UPS Express' . $symbolService .' - Saturday Delivery';
        } else {
            $serviceData = $archivedOrder[\UPS\Shipping\Helper\ConstantArchived::SERVICE_NAME]
                . $symbolService;
        }
        return $serviceData;
    }

    /**
     * Archived getPageArchivedOrders
     *
     * @return array $data
     */
    public function getPageArchivedOrders()
    {
        return $this->openorders->getPageArchivedOrders($this->numberItemOnPage);
    }

    /**
     * Archived getCurrentPage
     *
     * @return array $data
     */
    public function getCurrentPage()
    {
        return $this->page;
    }

    /**
     * Archived getColumnHeader
     *
     * @return array $data
     */
    public function getColumnHeader()
    {
        $request = $this->getRequest()->getParams();
        return [
            \UPS\Shipping\Helper\ConstantArchived::ENTITY_ID => [
                'text' => __('Order ID'), 'sort' => (isset($request[\UPS\Shipping\Helper\ConstantArchived::ENTITY_ID])
                && $request[\UPS\Shipping\Helper\ConstantArchived::ENTITY_ID] == 'DESC')
                ? 'ASC' : 'DESC', \UPS\Shipping\Helper\ConstantArchived::WIDTH => '90px'
            ],
            \UPS\Shipping\Helper\ConstantArchived::CREATED_AT_DATE => [
                'text' => __('Order Date'), 'sort' =>
                (isset($request[\UPS\Shipping\Helper\ConstantArchived::CREATED_AT_DATE])
                && $request[\UPS\Shipping\Helper\ConstantArchived::CREATED_AT_DATE] == 'DESC') ? 'ASC' : 'DESC',
                \UPS\Shipping\Helper\ConstantArchived::WIDTH => '90px'
            ],
            \UPS\Shipping\Helper\ConstantArchived::CREATED_AT_TIME => [
                'text' => __('Order Time'), 'sort' =>
                (isset($request[\UPS\Shipping\Helper\ConstantArchived::CREATED_AT_TIME])
                && $request[\UPS\Shipping\Helper\ConstantArchived::CREATED_AT_TIME] == 'DESC') ? 'ASC' : 'DESC',
                \UPS\Shipping\Helper\ConstantArchived::WIDTH => '90px'
            ],
            'product' => [
                'text' => __('Product'), 'sort' => 'NONE', \UPS\Shipping\Helper\ConstantArchived::WIDTH => ''
            ],
            \UPS\Shipping\Helper\ConstantArchived::DELIVERY_ADDRESS => [
                'text' => __('Delivery Address'), 'sort' =>
                (isset($request[\UPS\Shipping\Helper\ConstantArchived::DELIVERY_ADDRESS])
                && $request[\UPS\Shipping\Helper\ConstantArchived::DELIVERY_ADDRESS] == 'DESC') ? 'ASC' : 'DESC',
                \UPS\Shipping\Helper\ConstantArchived::WIDTH => '125px'
            ],
            \UPS\Shipping\Helper\ConstantArchived::SHIPPING_SERVICE => [
                'text' => __('Shipping Service'), 'sort' =>
                (isset($request[\UPS\Shipping\Helper\ConstantArchived::SHIPPING_SERVICE])
                && $request[\UPS\Shipping\Helper\ConstantArchived::SHIPPING_SERVICE] == 'DESC') ? 'ASC' : 'DESC',
                \UPS\Shipping\Helper\ConstantArchived::WIDTH => '120px'
            ],
            'cod' => [
                'text' => __('COD'), 'sort' => (isset($request['cod'])
                && $request['cod'] == 'DESC') ? 'ASC' : 'DESC', \UPS\Shipping\Helper\ConstantArchived::WIDTH => '45px'
            ]
        ];
    }

    /**
     * Archived getSortColumn
     *
     * @return array $data
     */
    public function getSortColumn()
    {
        $request = $this->getRequest()->getParams();
        foreach ($request as $key => $value) {
            if ($key != 'page_id' && $key != 'key') {
                return [$key, $value];
            }
        }
        return [];
    }

    /**
     * Archived getDetailOrder
     *
     * @param string $id //The id
     *
     * @return array $data
     */
    public function getDetailOrder($id)
    {
        return $this->openorders->getDetailOrder($id);
    }

    /**
     * Archived orderPagination
     *
     * @return array $data
     */
    public function orderPagination()
    {
        return $this->pagination($this->getPageArchivedOrders(), $this->getCurrentPage());
    }

    /**
     * Archived getCurrencySymbol
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
     * Archived getFormKey
     *
     * @return array $serviceData
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}
