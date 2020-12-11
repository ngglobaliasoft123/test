<?php
/**
 * Order file
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
 * Order class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Order extends \UPS\Shipping\Block\Adminhtml\Shipment\Manage
{
    protected $scopeConfig;
    protected $openorders;
    protected $numberItemOnPage = \UPS\Shipping\Helper\Config::PAGINATION_NUMBER_ITEM_ON_PAGE;
    protected $page;
    protected $salesOrder;
    protected $listPackageName;
    protected $modelAccount;
    protected $timezone;
    protected $countryModel;
    protected $serviceModel;
    protected $sortedServices;
    protected $accessorialModel;
    protected $country;
    protected $modelCurrency;
    protected $adminSession;
    protected $modelStore;
    protected $regionModel;
    protected $configWriter;
    protected $cacheTypeList;
    protected $dateTime;
    protected $formKey;

    /**
     * Order __construct
     *
     * @param string   $context          //The context
     * @param string   $country          //The country
     * @param string   $openorders       //The openorders
     * @param string   $salesModel       //The salesModel
     * @param string   $modelCurrency    //The modelCurrency
     * @param string   $adminSession     //The adminSession
     * @param string   $listPackageName  //The listPackageName
     * @param string   $modelaccount     //The modelaccount
     * @param string   $serviceModel     //The serviceModel
     * @param string   $accessorialModel //The accessorialModel
     * @param string   $countryModel     //The countryModel
     * @param string   $regionModel      //The regionModel
     * @param string   $configWriter      //The configWriter
     * @param string   $scopeConfig       //The scopeConfig
     * @param string   $cacheTypeList     //The cacheTypeList
     * @param DateTime $dateTime         //The date
     * @param string   $formKey          //The formKey
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Model\Config\Source\Country $country,
        \UPS\Shipping\Model\Order $openorders,
        \Magento\Sales\Model\Order $salesModel,
        \Magento\Directory\Model\Currency $modelCurrency,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \UPS\Shipping\Model\Package $listPackageName,
        \UPS\Shipping\Model\Account $modelaccount,
        \UPS\Shipping\Model\Service $serviceModel,
        \UPS\Shipping\Model\SortedServices $sortedServices,
        \UPS\Shipping\Model\Accessorial $accessorialModel,
        \Magento\Directory\Model\Country $countryModel,
        \Magento\Directory\Model\RegionFactory $regionModel,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        DateTime $dateTime,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->countryModel = $countryModel;
        $this->timezone = $context->getLocaleDate();
        $this->modelAccount = $modelaccount;
        $this->scopeConfig = $scopeConfig;
        $this->openorders = $openorders;
        $this->salesOrder = $salesModel;
        $this->serviceModel = $serviceModel;
        $this->sortedServices = $sortedServices;
        $this->accessorialModel = $accessorialModel;
        $this->listPackageName = $listPackageName;
        $this->country = $country;
        $this->modelCurrency = $modelCurrency;
        $this->adminSession = $adminSession;
        $this->modelStore = $context->getStoreManager();
        $this->regionModel = $regionModel;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->dateTime = $dateTime;
        $this->formKey = $formKey;
        parent::__construct($context);
    }

    /**
     * Order getListState
     *
     * @return array $data
     */
    public function getListState()
    {
        $state = [];
        $getState = $this->openorders->getStateMagento();
        foreach ($getState as $item => $value) {
            $arrayState = ['country_code' => $value['country_id'],
            'state_name' => $value['default_name'], 'state_code' => $value['code']];
            array_push($state, $arrayState);
        }
        return $state;
    }

    /**
     * Order getListCountry
     *
     * @return array $data
     */
    public function getListCountry()
    {
        $arrayOptionCountry = $this->country->toOptionArray(true);
        return $arrayOptionCountry;
    }

    /**
     * Order getListAccount
     *
     * @return array $data
     */
    public function getListAccount()
    {
        return $this->modelAccount->getListAccount();
    }

    /**
     * Order getAllListService
     *
     * @return array $data
     */
    public function getAllListService()
    {
        $service = $this->serviceModel->getAllListService();
        if (!empty($service) && is_array($service)) {
            foreach ($service as $key => $item) {
                if (trim($item[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME]) == 'UPS Access Point Economy') {
                    $service[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO] = 'UPS Access Point'
                    . $item[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' Economy';
                } elseif (trim($item[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME]) == 'UPS Standard') {
                    $service[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO] = 'UPS'
                    . $item[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' Standard';
                } elseif (trim($item[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME]) == 'UPS Express 12:00') {
                    $service[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO] = 'UPS Express 12:00';
                } elseif (trim($item[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME]) == 'UPS Ground') {
                    $service[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO] = 'UPS'
                    . $item[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' Ground';
                } elseif (trim($item[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME]) == 'UPS Next Day Air Early') {
                    $service[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO] = 'UPS Next Day Air'
                    . $item[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' Early';
                } elseif (trim($item[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME]) == 'UPS Standard - Saturday Delivery') {
                    $service[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO] = 'UPS'
                    . $item[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' Standard - Saturday Delivery';
                } elseif (trim($item[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME]) == 'UPS Express - Saturday Delivery') {
                    $service[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO] = 'UPS Express'
                    . $item[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' - Saturday Delivery';
                } else {
                    $service[$key][\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO]
                        = $item[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME]
                        . $item[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL];
                }
            }
        }
        // get country code
        $countryCode = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE);
        $sortedServices = $this->sortedServices->getListSortedServicesByCountryCode($countryCode);
        $result = $this->sortedServices->getListSortedServices($sortedServices, $service);
        return $result;
    }

    /**
     * Order getAllListAccessorial
     *
     * @return array $data
     */
    public function getAllListAccessorial()
    {
        return $this->accessorialModel->getAllListAccessorial();
    }

    /**
     * Order getListOpenOrders
     *
     * @return array $data
     */
    public function getListOpenOrders()
    {
        $this->page = $this->getRequest()->getParam('page_id');
        if (empty($this->page)) {
            $this->page = 1;
        }
        $params = $this->getRequest()->getParams();
        $listOrder = $this->openorders->getListOpenOrders($this->numberItemOnPage, $this->page, $params);
        foreach ($listOrder as $index => $archivedOrder) {
            $order = $this->getOrderData($archivedOrder);
            // hanlding product
            $productName = [];
            foreach ($order->getItemsCollection() as $item) {
                $productNameString = round($item->getQtyOrdered()) . ' x ' . $item->getProduct()->getName();
                if ($item->getParentItemId() == '' || $item->getParentItemId() == null) {
                    $checkParent = $this->openorders->checkParentItem($item->getItemId());
                    $countCheckParent = $this->countCheckParent($checkParent);
                    if ($countCheckParent == 0) {
                        array_push($productName, $productNameString);
                    }
                } else {
                    array_push($productName, $productNameString);
                }
            }
            $listOrder[$index]['productName'] = implode(', ', $productName);
            $countproductName = $this->countCheckParent($productName);
            if ($countproductName > 3) {
                $productString = trim($productName[0]) . \UPS\Shipping\Helper\ConstantOrder::STYLE_BR
                . trim($productName[1]) . \UPS\Shipping\Helper\ConstantOrder::STYLE_BR
                . trim($productName[2]) . ', ...';
            } else {
                $productString = implode(\UPS\Shipping\Helper\ConstantOrder::STYLE_BR, $productName);
            }
            // $newDate = new \DateTime($order->getCreatedAt());
            // $listOrder[$index]['created_at'] = $this->timezone->date($newDate)->format('m/d/y H:i:s');
            $listOrder[$index]['created_at']
                = $this->dateTime->date('m/d/y H:i:s', $this->dateTime->timestamp($order->getCreatedAt()));
            $listOrder[$index]['listProductName'] = $productString;
            // handling service name
            if (isset($archivedOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME])) {
                $orderTrim = trim($archivedOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME]);
                $listOrder[$index][\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME_INFO]
                    = $this->getServiceName($orderTrim, $archivedOrder);
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
     * @param string $orderTrim     //The orderTrim
     * @param string $archivedOrder //The archivedOrder
     *
     * @return array $serviceData
     */
    public function getServiceName($orderTrim, $archivedOrder)
    {
        $serviceData = '';
        if ($orderTrim == 'UPS Access Point Economy') {
            $serviceData = 'UPS Access Point'
            . $archivedOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' Economy';
        } elseif ($orderTrim == 'UPS Standard') {
            $serviceData = 'UPS' . $archivedOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' Standard';
        } elseif ($orderTrim == 'UPS Express 12:00') {
            $serviceData = 'UPS Express 12:00';
        } elseif ($orderTrim == 'UPS Ground') {
            $serviceData = 'UPS' . $archivedOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' Ground';
        } elseif ($orderTrim == 'UPS Next Day Air Early') {
            $serviceData = 'UPS Next Day Air' . $archivedOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' Early';
        } elseif ($orderTrim == 'UPS Standard - Saturday Delivery') {
            $serviceData = 'UPS' . $archivedOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' Standard - Saturday Delivery';
        } elseif ($orderTrim == 'UPS Express - Saturday Delivery') {
            $serviceData = 'UPS Express' . $archivedOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL] . ' - Saturday Delivery';
        } else {
            $serviceData = $archivedOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_NAME]
                . $archivedOrder[\UPS\Shipping\Helper\ConstantOrder::SERVICE_SYMBOL];
        }

        return $serviceData;
    }

    /**
     * Order getPageOpenOrders
     *
     * @return array $data
     */
    public function getPageOpenOrders()
    {
        return $this->openorders->getPageOpenOrders($this->numberItemOnPage);
    }

    /**
     * Order getCurrentPage
     *
     * @return array $data
     */
    public function getCurrentPage()
    {
        return $this->page;
    }

    /**
     * Order exportSortColumn
     *
     * @return array $data
     */
    public function exportSortColumn()
    {
        $request = $this->getRequest()->getParams();
        if (isset($request[\UPS\Shipping\Helper\ConstantOrder::ENTITY_ID])
            && $request[\UPS\Shipping\Helper\ConstantOrder::ENTITY_ID] == 'ASC'
        ) {
            $OrderBy = 'DESC';
        } else {
            $OrderBy = 'ASC';
        }
        return $OrderBy;
    }

    /**
     * Order getColumnHeader
     *
     * @return array $data
     */
    public function getColumnHeader()
    {
        $request = $this->getRequest()->getParams();
        return [
            \UPS\Shipping\Helper\ConstantOrder::ENTITY_ID => [
                'text' => __('Order ID'), 'sort' => (isset($request[\UPS\Shipping\Helper\ConstantOrder::ENTITY_ID])
                && $request[\UPS\Shipping\Helper\ConstantOrder::ENTITY_ID] == 'DESC')
                ? 'ASC' : 'DESC', \UPS\Shipping\Helper\ConstantOrder::WIDTH => '90px'
            ],
            \UPS\Shipping\Helper\ConstantOrder::CREATED_AT_DATE => [
                'text' => __('Order Date'), 'sort'
                => (isset($request[\UPS\Shipping\Helper\ConstantOrder::CREATED_AT_DATE])
                && $request[\UPS\Shipping\Helper\ConstantOrder::CREATED_AT_DATE] == 'DESC') ?
                 'ASC' : 'DESC', \UPS\Shipping\Helper\ConstantOrder::WIDTH => '90px'
            ],
            \UPS\Shipping\Helper\ConstantOrder::CREATED_AT_TIME => [
                'text' => __('Order Time'), 'sort'
                => (isset($request[\UPS\Shipping\Helper\ConstantOrder::CREATED_AT_TIME])
                && $request[\UPS\Shipping\Helper\ConstantOrder::CREATED_AT_TIME] == 'DESC') ?
                 'ASC' : 'DESC', \UPS\Shipping\Helper\ConstantOrder::WIDTH => '90px'
            ],
            'product' => [
                'text' => __('Product'), 'sort' => 'NONE', \UPS\Shipping\Helper\ConstantOrder::WIDTH => ''
            ],
            \UPS\Shipping\Helper\ConstantOrder::DELIVERY_ADDRESS => [
                'text' => __('Delivery Address'), 'sort'
                => (isset($request[\UPS\Shipping\Helper\ConstantOrder::DELIVERY_ADDRESS])
                && $request[\UPS\Shipping\Helper\ConstantOrder::DELIVERY_ADDRESS] == 'DESC') ? 'ASC' : 'DESC',
                \UPS\Shipping\Helper\ConstantOrder::WIDTH => '125px'
            ],
            \UPS\Shipping\Helper\ConstantOrder::SHIPPING_SERVICE => [
                'text' => __('Shipping Service'), 'sort'
                => (isset($request[\UPS\Shipping\Helper\ConstantOrder::SHIPPING_SERVICE])
                && $request[\UPS\Shipping\Helper\ConstantOrder::SHIPPING_SERVICE] == 'DESC') ? 'ASC' : 'DESC',
                \UPS\Shipping\Helper\ConstantOrder::WIDTH => '120px'
            ],
            'cod' => [
                'text' => __('COD'), 'sort' => (isset($request['cod'])
                && $request['cod'] == 'DESC') ? 'ASC' : 'DESC', \UPS\Shipping\Helper\ConstantOrder::WIDTH => '45px'
            ]
        ];
    }

    /**
     * Order getSortColumn
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
     * Order getDetailOrder
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
     * Order orderPagination
     *
     * @return array $data
     */
    public function orderPagination()
    {
        return $this->pagination($this->getPageOpenOrders(), $this->getCurrentPage());
    }

    /**
     * Order getListPackageShipment
     *
     * @return array $data
     */
    public function getListPackageShipment()
    {
        return $this->listPackageName->getListPackageShipment();
    }
    /**
     * Order getListPackageShipment
     *
     * @return array $data
     */
    public function getSelectedPackageShipment()
    {
        return [
            0 => [
                'package_id' => 1,
                'weight' => 0,
                'unit_weight' => 'kgs',
                'length' => 0,
                'width' => 0,
                'height' => 0,
                'unit_dimension' => 'cm'
            ]
        ];
    }

    /**
     * Order getAccountDefault
     *
     * @return array $data
     */
    public function getAccountDefault()
    {
        $apString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_CHOOSE_ACCOUNT_NUMBER_AP;
        $addString = \UPS\Shipping\Helper\Config::SERVICE_UPS_SHIPPING_CHOOSE_ACCOUNT_NUMBER_ADD;
        return [
            'AP' => $this->scopeConfig->getValue($apString),
            'ADD' => $this->scopeConfig->getValue($addString)
        ];
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
     * Order getCurrencySymbol
     *
     * @return array $data
     */
    public function getCurrencySymbol()
    {
        $currencyCode = $this->modelStore->getStore()->getCurrentCurrencyCode();
        $currencySymbol = $this->modelCurrency->load($currencyCode)->getCurrencySymbol();
        if (empty($currencySymbol)) {
            $currencySymbol = $currencyCode;
        }
        return $currencySymbol;
    }

    /**
     * Order getFormKey
     *
     * @return array $serviceData
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}
