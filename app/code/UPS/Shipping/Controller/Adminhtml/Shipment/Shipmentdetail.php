<?php
/**
 * Shipmentdetail file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\Controller\Adminhtml\Shipment;
/**
 * Shipmentdetail class
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class Shipmentdetail extends \Magento\Framework\App\Action\Action
{
    protected $salesModel;
    protected $modelShipment;
    protected $modelOrder;
    protected $modelTracking;
    protected $modelService;
    protected $jsonResult;
    protected $apiShipment;
    protected $countryFactory;
    protected $modelCurrency;
    protected $apiManager;
    protected $scopeConfig;
    protected $licenseModel;
    protected $apiHandshake;
    protected $apiAccount;
    protected $checkoutSession;

    /**
     * Shipmentdetail __construct
     *
     * @param string $context         //The Context
     * @param string $jsonResult      //The jsonResult
     * @param string $countryFactory  //The countryFactory
     * @param string $modelCurrency   //The modelCurrency
     * @param string $salesModel      //The salesModel
     * @param string $modelShipment   //The modelShipment
     * @param string $modelOrder      //The model Order
     * @param string $modelTracking   //The modelTracking
     * @param string $modelService    //The modelService
     * @param string $apiShipment     //The apiShipment
     * @param string $apiManager      //The apiManager
     * @param string $scopeConfig     //The scopeConfig
     * @param string $configWriter    //The configWriter
     * @param string $cacheTypeList   //The cacheTypeList
     * @param string $modelLicense    //modelLicense
     * @param string $apiHandshake    //The apiHandshake
     * @param string $apiAccount      //The apiAccount
     * @param string $checkoutSession //The checkoutSession
     *
     * @return string $dataRequestShip
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResult,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\Currency $modelCurrency,
        \Magento\Sales\Model\Order $salesModel,
        \UPS\Shipping\Model\Shipment $modelShipment,
        \UPS\Shipping\Model\Order $modelOrder,
        \UPS\Shipping\Model\Tracking $modelTracking,
        \UPS\Shipping\Model\Service $modelService,
        \UPS\Shipping\API\Shipment $apiShipment,
        \UPS\Shipping\API\Manager\Index $apiManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \UPS\Shipping\Model\License $modelLicense,
        \UPS\Shipping\API\Handshake $apiHandshake,
        \UPS\Shipping\API\Account $apiAccount,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->apiManager = $apiManager;
        $this->modelShipment = $modelShipment;
        $this->modelOrder = $modelOrder;
        $this->modelService = $modelService;
        $this->countryFactory = $countryFactory;
        $this->modelTracking = $modelTracking;
        $this->jsonResult = $jsonResult;
        $this->apiShipment = $apiShipment;
        $this->modelCurrency = $modelCurrency;
        $this->salesModel = $salesModel;
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->licenseModel = $modelLicense;
        $this->apiHandshake = $apiHandshake;
        $this->apiAccount = $apiAccount;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    /**
     * Shipmentdetail execute
     *
     * @return null
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $result = $this->jsonResult->create();
            $returnData = [];
            switch ($this->getRequest()->getParam('method')) {
            case 'getShipmentDetail':
                $returnData = $this->runShipmentDetail($result);
                break;
            case 'setCancelShipment':
                $message = $this->cancelShipment();
                $returnData = $result->setData([\UPS\Shipping\Helper\ConstantShipment::SHIPMENT => '','product' => '','message' => $message]);
                break;
            default:
                break;
            }
            return $returnData;
        }
    }

    /**
     * Shipmentdetail runShipmentDetail
     *
     * @param string $result //the result
     *
     * @return null
     */
    public function runShipmentDetail($result)
    {
        $trackingId = $this->getRequest()->getParam('tracking_id');
        $shipmentData = $this->modelShipment->getShipmentDetail($trackingId);
        $entityID = $shipmentData[\UPS\Shipping\Helper\ConstantShipment::ENTITY_ID];
        $shipmentId = $shipmentData['id'];
        $productDetail = $this->modelShipment->getProductDetail($shipmentId, $entityID);
        $trackingNumber = $shipmentData['tracking_number'];
        $packageStatus = $this->getPackageStatus($trackingNumber);
        $orderStatus = strtolower($packageStatus);
        $packageStatus = ucwords(str_replace('_', ' ', $orderStatus));
        $shipmentData['package_status'] = $packageStatus;
        //update package status to sales_order, sales_order_grid in Magento
        $this->salesModel->load($entityID)->setStatus($orderStatus, true)->save();
        $this->modelShipment->updateStatus($shipmentId, $packageStatus);
        $shipmentData['date_created'] = date('M d, Y, H:i:s', strtotime($shipmentData['date_created']));
        //update data to view.
        $varShippingService = $shipmentData[\UPS\Shipping\Helper\ConstantShipment::SHIPPING_SERVICE];
        $shippingService = $this->modelService->getShippingServiceById($varShippingService);
        $shippingServiceName = '';
        $shippingServiceType = '';

        if (!empty($shippingService)) {
            $shippingServiceType = $shippingService[0]['service_type'];
            $shippingServiceName = $shippingService[0]['service_name'];
            $shippingServiceSymbol = $shippingService[0]['service_symbol'];
            // handling service name
            $shippingServiceName = $this->getShippingServiceName($shippingServiceName, $shippingServiceSymbol);
        }
        $shipmentData['service_type'] = $shippingServiceType;
        $shipmentData['access_point_addr'] = "";
        $order = $this->salesModel->load($entityID);
        //handling currency symbol
        $currencyCode = $order->getOrderCurrencyCode();
        $currencySymbol = $this->modelCurrency->load($currencyCode)->getCurrencySymbol();
        $shipmentData['currency_symbol'] = (empty($currencySymbol)) ? $currencyCode : $currencySymbol;
        $shipmentData['customername'] = $this->getCustomerName($order);
        //handling customer name
        $shipmentAddress1 = $shipmentData[\UPS\Shipping\Helper\ConstantShipment::ADDRESS1];
        $shipmentAddress2 = $shipmentData[\UPS\Shipping\Helper\ConstantShipment::ADDRESS2];
        $shipmentAddress3 = $shipmentData[\UPS\Shipping\Helper\ConstantShipment::ADDRESS3];
        $shipmentPostcode = $shipmentData[\UPS\Shipping\Helper\ConstantShipment::POSTCODE];
        $shipmentCountry = $shipmentData[\UPS\Shipping\Helper\ConstantShipment::COUNTRY];

        if ($shippingServiceType == "ADD") {
            $customerAddress = [];
            (!empty($shipmentAddress1)) ? array_push($customerAddress, $shipmentAddress1) : null;
            (!empty($shipmentAddress2)) ? array_push($customerAddress, $shipmentAddress2) : null;
            (!empty($shipmentAddress3)) ? array_push($customerAddress, $shipmentAddress3) : null;
            (!empty($shipmentData['city'])) ? array_push($customerAddress, $shipmentData['city']) : null;
            if (!empty($shipmentData[\UPS\Shipping\Helper\ConstantShipment::STATE])) {
                $stateName = $shipmentData[\UPS\Shipping\Helper\ConstantShipment::STATE];
                array_push($customerAddress, $stateName);
            }
            (!empty($shipmentPostcode)) ? array_push($customerAddress, $shipmentPostcode) : null;
            $countryName = $this->getCountryName($shipmentCountry);
            (!empty($countryName)) ? array_push($customerAddress, $countryName) : null;
            $shipmentData['shipping_address'] = implode(\UPS\Shipping\Helper\ConstantShipment::STYLE_BR, $customerAddress);
            $shipmentData[\UPS\Shipping\Helper\ConstantShipment::SHIPPING_SERVICE]
                = 'To Address (' . $shippingServiceName . ')';
            $accessorialData = json_decode($shipmentData[\UPS\Shipping\Helper\ConstantShipment::ACCESSORIAL_SERVICE]);
            $accessorial_service_translate = $this->getAccessorialService($accessorialData);
            $shipmentData[\UPS\Shipping\Helper\ConstantShipment::ACCESSORIAL_SERVICE]
                = json_encode($accessorial_service_translate);
        } elseif ($shippingServiceType == "AP") {
            $countryId = $order->getShippingAddress()->getCountryId();
            $street = $order->getShippingAddress()->getStreet();
            $city = $order->getShippingAddress()->getCity();
            $state = $order->getShippingAddress()->getRegion();
            $postcode = $order->getShippingAddress()->getPostcode();
            $countryName = $this->getCountryName($countryId);
            $address = $this->getStreetString($street);
            (!empty($state)) ? array_push($address, $state) : null;
            (!empty($city)) ? array_push($address, $city) : null;
            (!empty($postcode)) ? array_push($address, $postcode) : null;
            (!empty($countryName)) ? array_push($address, $countryName) : null;
            $shipmentData['shipping_address']
                = implode(\UPS\Shipping\Helper\ConstantShipment::STYLE_BR, $address);
            $accessPoint = [];
            (!empty($shipmentAddress1)) ? array_push($accessPoint, $shipmentAddress1) : null;
            (!empty($shipmentAddress2)) ? array_push($accessPoint, $shipmentAddress2) : null;
            (!empty($shipmentAddress3)) ? array_push($accessPoint, $shipmentAddress3) : null;
            (!empty($shipmentData['city'])) ? array_push($accessPoint, $shipmentData['city']) : null;
            if (!empty($shipmentData[\UPS\Shipping\Helper\ConstantShipment::STATE])) {
                $stateName = $shipmentData[\UPS\Shipping\Helper\ConstantShipment::STATE];
                array_push($accessPoint, $stateName);
            }
            (!empty($shipmentPostcode)) ? array_push($accessPoint, $shipmentPostcode) : null;
            $countryName1 = $this->getCountryName($shipmentCountry);

            (!empty($shipmentCountry)) ? array_push($accessPoint, $countryName1) : null;
            $shipmentData[\UPS\Shipping\Helper\ConstantShipment::SHIPPING_SERVICE]
                = 'To AP (' . $shippingServiceName . ')';
            $shipmentData['access_point_addr']
                = implode(\UPS\Shipping\Helper\ConstantShipment::STYLE_BR, $accessPoint);
            $this->setAccessorial($shipmentData);
        } else {
            $orderStatus = strtolower($packageStatus);
        }
        $shipmentData['order_value'] = number_format((float)preg_replace('([^a-zA-Z0-9.])', '', $shipmentData['order_value']), 2);
        $shipmentData['shipping_fee'] = number_format((float)$shipmentData['shipping_fee'], 2);
        //offPluginManager 2019-03-18
        $this->runUpdateShipmentsStatus($trackingNumber, $packageStatus);
        return $result->setData([\UPS\Shipping\Helper\ConstantShipment::SHIPMENT => $shipmentData, 'product' => $productDetail, 'message' => '']);
    }

    /**
     * Shipmentdetail runUpdateShipmentsStatus
     *
     * @param string $trackingNumber //The trackingNumber
     * @param string $packageStatus  //The packageStatus
     *
     * @return null
     */
    public function runUpdateShipmentsStatus($trackingNumber, $packageStatus)
    {
        $dataShipmentStatus = [];
        $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
        if (empty($bearerToken)) {
            // re-RegisterToken
            if ($this->resetRegisteredToken()) {
                $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                $bearerSessionToken = $this->checkoutSession->getBearLongToken();
                if ($bearerSessionToken != $bearerToken) {
                    $bearerToken = $bearerSessionToken;
                }
            }
        }
        // call API
        if (!empty($bearerToken)) {
            // update status
            $dataShipmentStatus[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN] = $bearerToken;
            $dataShipmentStatus[\UPS\Shipping\Helper\ConstantShipment::SHIPMENT][] = ["trackingNumber" => $trackingNumber, "shipmentStatus" => $packageStatus];
            $responseApi = $this->apiManager->callUpdateShipmentsStatus($dataShipmentStatus);
            $responseApi = json_decode($responseApi);
            // bearerToken expired
            if (isset($responseApi->error->errorCode) && $responseApi->error->errorCode == '401') {
                if ($this->resetRegisteredToken()) {
                    $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                    $bearerSessionToken = $this->checkoutSession->getBearLongToken();
                    if ($bearerSessionToken != $bearerToken) {
                        $bearerToken = $bearerSessionToken;
                    }
                    $dataShipmentStatus[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN] = $bearerToken;
                    $this->apiManager->callUpdateShipmentsStatus($dataShipmentStatus);
                }
            }
        }
    }

    /**
     * Shipmentdetail setAccessorial
     *
     * @param string $shipmentData //The shipmentData
     *
     * @return null
     */
    public function setAccessorial(&$shipmentData)
    {
        $accessorial_service_translate = [];
        foreach (json_decode($shipmentData[\UPS\Shipping\Helper\ConstantShipment::ACCESSORIAL_SERVICE]) as $key1 => $value) {
            $accessorial_service_translate[$key1] = __($value);
        }
        $shipmentData[\UPS\Shipping\Helper\ConstantShipment::ACCESSORIAL_SERVICE] = json_encode($accessorial_service_translate);
    }

    /**
     * Shipmentdetail cancelShipment
     *
     * @return null
     */
    public function cancelShipment()
    {
        $arrShipmentNumber = [];
        $message = '';
        $dataShipmentStatus = [];
        //$cancelShipmentList: shipment_number, tracking_number.
        $cancelShipmentList = json_decode($this->getRequest()->getParam('cancel_shipment_data'));
        if (!empty($cancelShipmentList) && is_array($cancelShipmentList)) {
            foreach ($cancelShipmentList as $item) {
                    $shipmentNumber = $item->shipment_number;
                if (!in_array($shipmentNumber, $arrShipmentNumber)) {
                    $arrShipmentNumber[] = $shipmentNumber;
                    //get list tracking number of shipment number
                    $listTracking = $this->modelTracking->getListTrackingNumberByShipmentNumber($shipmentNumber);
                    if (!empty($listTracking)) {
                        $checkVoidShipment = $this->voidShipmentByShipmentNumber($shipmentNumber);
                        //update status open order.
                        if ($checkVoidShipment && isset($checkVoidShipment[0]) && $checkVoidShipment[0]) {
                            $this->setSaveCancelShipment($listTracking, $dataShipmentStatus);
                            //remove shipment, tracking number in ups_shipping_shipments
                            $this->modelShipment->deleteRowShipment($shipmentNumber);
                            $this->modelTracking->deleteTracking($shipmentNumber);
                            $message = __("Shipment Canceled");
                        } else {
                            $message = __($checkVoidShipment[1]);
                        }
                    }
                }
            }
            //offPluginManager 2019-03-18
            $this->callMerchantAPI($dataShipmentStatus);
        }
        return $message;
    }

    /**
     * Shipmentdetail setSaveCancelShipment
     *
     * @param string $listTracking       //The listTracking
     * @param string $dataShipmentStatus //The dataShipmentStatus
     *
     * @return null
     */
    public function setSaveCancelShipment($listTracking, &$dataShipmentStatus)
    {
        $arrOrder = [];
        foreach ($listTracking as $items) {
            $stringOrderID = $items[\UPS\Shipping\Helper\ConstantShipment::ORDER_ID];
            if (!in_array($stringOrderID, $arrOrder)) {
                $arrOrder[] = $stringOrderID;
                $this->modelOrder->updateStatusCancelShipment($stringOrderID);
                //update status sales_order, sales_order_grid in Magento
                $orderIdMagento = $this->modelOrder->getIdMagento($stringOrderID);
                $this->saveCancelShipment($orderIdMagento);
            }
            $dataShipmentStatus[\UPS\Shipping\Helper\ConstantShipment::SHIPMENT][] = [
                "trackingNumber" => $items['tracking_number'],
                "shipmentStatus" => "processing_in_progress"
            ];
        }
    }

    /**
     * Shipmentdetail saveCancelShipment
     *
     * @param string $orderIdMagento //The orderIdMagento
     *
     * @return null
     */
    public function saveCancelShipment($orderIdMagento)
    {
        $this->salesModel->load($orderIdMagento['order_id_magento'])->setStatus('processing_in_progress', true)->save();
    }

    /**
     * Shipmentdetail callMerchantAPI
     *
     * @param string $dataShipmentStatus //The dataShipmentStatus
     *
     * @return array $address
     */
    public function callMerchantAPI($dataShipmentStatus)
    {
        if (!empty($dataShipmentStatus)) {
            $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
            if (empty($bearerToken)) {
                // re-RegisterToken
                if ($this->resetRegisteredToken()) {
                    $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                    $bearerSessionToken = $this->checkoutSession->getBearLongToken();
                    if ($bearerSessionToken != $bearerToken) {
                        $bearerToken = $bearerSessionToken;
                    }
                }
            }
            // call API
            if (!empty($bearerToken)) {
                $dataShipmentStatus[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN] = $bearerToken;
                $responseApi = $this->apiManager->callUpdateShipmentsStatus($dataShipmentStatus);
                $responseApi = json_decode($responseApi);
                // bearerToken expired
                if (isset($responseApi->error->errorCode) && $responseApi->error->errorCode == '401') {
                    if ($this->resetRegisteredToken()) {
                        $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
                        $bearerSessionToken = $this->checkoutSession->getBearLongToken();
                        if ($bearerSessionToken != $bearerToken) {
                            $bearerToken = $bearerSessionToken;
                        }
                        $dataShipmentStatus[\UPS\Shipping\Helper\ConstantManager::BEARERTOKEN] = $bearerToken;
                        $this->apiManager->callUpdateShipmentsStatus($dataShipmentStatus);
                    }
                }
            }
        }
    }

    /**
     * Shipmentdetail getAccessorialService
     *
     * @param string $accessorialData //The accessorialData
     *
     * @return array $address
     */
    public function getAccessorialService($accessorialData)
    {
        $accessorial_service_translate = [];
        foreach ($accessorialData as $key => $value) {
            $accessorial_service_translate[$key] = __($value);
        }
        return $accessorial_service_translate;
    }

    /**
     * Shipmentdetail getStreetString
     *
     * @param string $street //The order
     *
     * @return array $address
     */
    public function getStreetString($street)
    {
        $address = [];
        if (!empty($street) && is_array($street)) {
            foreach ($street as $item) {
                if (!empty($item)) {
                    $address[] = $item;
                }
            }
        }
        return $address;
    }

    /**
     * Shipmentdetail getCustomerName
     *
     * @param string $order //The order
     *
     * @return string $trackingStatus
     */
    public function getCustomerName($order)
    {
        if ($order->getCustomerName() != 'Guest') {
            return $order->getShippingAddress()->getFirstname() . ' ' . $order->getShippingAddress()->getLastname();
        } else {
            return $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
        }
    }

    /**
     * Shipmentdetail getShippingServiceName
     *
     * @param string $shippingServiceName   //The shippingServiceName
     * @param string $shippingServiceSymbol //The shippingServiceSymbol
     *
     * @return string $trackingStatus
     */
    public function getShippingServiceName($shippingServiceName, $shippingServiceSymbol)
    {
        if (trim($shippingServiceName) == 'UPS Access Point Economy') {
            return 'UPS Access Point' . $shippingServiceSymbol . ' Economy';
        } elseif (trim($shippingServiceName) == 'UPS Standard') {
            return 'UPS' . $shippingServiceSymbol . ' Standard';
        } elseif (trim($shippingServiceName) == 'UPS Express 12:00') {
            return 'UPS Express 12:00';
        } elseif (trim($shippingServiceName) == 'UPS Ground') {
            return 'UPS' . $shippingServiceSymbol . ' Ground';
        } elseif (trim($shippingServiceName) == 'UPS Next Day Air Early') {
            return 'UPS Next Day Air' . $shippingServiceSymbol . ' Early';
        } elseif (trim($shippingServiceName) == 'UPS Standard - Saturday Delivery') {
            return 'UPS' . $shippingServiceSymbol . ' Standard - Saturday Delivery';
        } elseif (trim($shippingServiceName) == 'UPS Express - Saturday Delivery') {
            return 'UPS Express' . $shippingServiceSymbol .' - Saturday Delivery';
        } else {
            return $shippingServiceName . $shippingServiceSymbol;
        }
    }

    /**
     * Shipmentdetail getCountryName
     *
     * @param string $countryId //The countryId
     *
     * @return string $trackingStatus
     */
    public function getCountryName($countryId)
    {
        $countryName = '';
        if (!empty($countryId)) {
            $country = $this->countryFactory->create()->loadByCode($countryId);
            if ($country) {
                $countryName = $country->getName();
            }
        }
        return $countryName;
    }

    /**
     * Shipmentdetail getPackageStatus
     *
     * @param string $trackingNumber //The trackingNumber
     *
     * @return string $trackingStatus
     */
    public function getPackageStatus($trackingNumber)
    {
        $trackingResponse = json_decode($this->apiShipment->tracking(['InquiryNumber' => $trackingNumber]));
        if ($trackingResponse && !isset($trackingResponse->Fault)) {
            $shipmentPackageActivity = (object)[];
            if (is_array($trackingResponse->TrackResponse->Shipment->Package)) {
                foreach ($trackingResponse->TrackResponse->Shipment->Package as $item) {
                    if ($item->TrackingNumber == $trackingNumber) {
                        $shipmentPackageActivity = $item->Activity;
                        break;
                    }
                }
            } else {
                $shipmentPackageActivity = $trackingResponse->TrackResponse->Shipment->Package->Activity;
            }
            $trackingStatus = $this->getTrackingStatusCode($shipmentPackageActivity);
            if ($trackingStatus != null) {
                return $trackingStatus->Description;
            }
        }
        return '';
    }

    /**
     * Shipmentdetail voidShipmentByShipmentNumber
     *
     * @param string $shipmentNumber //The shipmentNumber
     *
     * @return array $result
     */
    public function voidShipmentByShipmentNumber($shipmentNumber)
    {
        $shipmentResponse = json_decode($this->apiShipment->voidShipment(['VoidShipment'=> ['ShipmentIdentificationNumber' => $shipmentNumber]]));
        $error_message = '';
        if (!isset($shipmentResponse->Fault)) {
            $shipmentStatusCode = $shipmentResponse->VoidShipmentResponse->Response->ResponseStatus->Code;
            if ($shipmentStatusCode == '1') {
                return [true, ''];
            }
        } else {
            if ($shipmentResponse
                && isset($shipmentResponse->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Code)
                && $shipmentResponse->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Code == '190117'
            ) {
                return [true, ''];
            } else {
                if (isset($shipmentResponse->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Code)
                    && $shipmentResponse->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Code == '190118'
                ) {
                    $error_message = __("We are unable to void this shipment at this time. You may attempt to void the shipment later. However you will not be billed for this shipment provided that you do not use the shipping label");
                } else {
                    $error_message = $shipmentResponse->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description;
                }
            }
        }
        return [false, $error_message];
    }

    /**
     * Shipmentdetail getTrackingStatusCode
     *
     * @param string $shipmentPackageActivity //The shipmentPackageActivity
     *
     * @return string $shipmentPackageActivity
     */
    public function getTrackingStatusCode($shipmentPackageActivity)
    {
        if (is_array($shipmentPackageActivity) && isset($shipmentPackageActivity)) {
            $keyRecentestDate = 0;
            $shipmentPackageActivityDateTime
                = strtotime($shipmentPackageActivity[0]->Date . ' ' . $shipmentPackageActivity[0]->Time);
            foreach ($shipmentPackageActivity as $key => $item) {
                $itemTime = strtotime($item->Date . ' ' . $item->Time);
                if ($shipmentPackageActivityDateTime < $itemTime) {
                    $shipmentPackageActivityDateTime = $itemTime;
                    $keyRecentestDate = $key;
                }
            }
            return $shipmentPackageActivity[$keyRecentestDate]->Status;
        } else {
            return $shipmentPackageActivity->Status;
        }
    }

    /**
     * Index setRegisteredToken
     *
     * @return string $trackingStatus
     */
    public function resetRegisteredToken()
    {
        $licenseDefault = $this->licenseModel->getLicenseDefault();
        $resetRegisterToken = false;
        $websiteMerchant = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_WEBSECUREURL);
        $valueSecurityToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN);
        if (empty($valueSecurityToken)) {
            foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
                $this->cacheTypeList->cleanType($type);
            }
            $valueSecurityToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LINK_SECURITY_TOKEN);
        }
        $arrLicenseParams = [
            "MerchantKey" => $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY),
            "WebstoreUrl" => $websiteMerchant,
            "WebstoreUpsServiceLinkSecurityToken" => $valueSecurityToken,
            "WebstorePlatform" => 'Magento',
            "WebstorePlatformVersion" => \UPS\Shipping\Helper\Config::VERSION_FLATFORM,
            "UpsReadyPluginName" => \UPS\Shipping\Helper\Config::UPS_SHIPPING_MODULE,
            "UpsReadyPluginVersion" => \UPS\Shipping\Helper\Config::VERSION_PLUGIN,
            "WebstoreUpsServiceLinkUrl" => $websiteMerchant . \UPS\Shipping\Helper\Config::API_URL,
            "Username" => $licenseDefault['Username'],
            "Password" => $licenseDefault['Password'],
            "AccessLicenseNumber" => $licenseDefault['AccessLicenseNumber']
        ];
        // Long bearer token
        $responseLongToken = $this->apiHandshake->registeredPluginToken($arrLicenseParams);
        if ($responseLongToken) {
            $responseLongToken = json_decode($responseLongToken);
            // save long token
            $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN,  $responseLongToken->data);
            $this->checkoutSession->setBearLongToken($responseLongToken->data);
            // save UPS_BING_MAPS_KEY
            $responseUpsBingMapsKey = $this->apiAccount->getUpsBingMapsKey($responseLongToken->data);
            if ($responseUpsBingMapsKey) {
                $responseUpsBingMapsKey = json_decode($responseUpsBingMapsKey);
                // save long token
                $this->configWriter->save(\UPS\Shipping\Helper\Config::UPS_BING_MAPS_KEY,  $responseUpsBingMapsKey->data);
            }
            $resetRegisterToken = true;
        }

        foreach (\UPS\Shipping\Helper\Config::LIST_CLEAR_CACHE as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        return $resetRegisterToken;
    }
}
