<?php
/**
 * ClientAPI file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
namespace UPS\Shipping\API;

/**
 * ClientAPI file
 *
 * @category  UPS_Shipping
 * @package   UPS_Shipping
 * @author    United Parcel Service of America, Inc. <noreply@ups.com>
 * @copyright 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */
class ClientAPI
{
    const ENV = "PRO";
    const APIURL = [
        'DEV' => "https://wwwcie.ups.com/rest",
        'UAT' => "https://onlinetools.ups.com/rest",
        'PRO' => "https://onlinetools.ups.com/rest"
    ];
    const APIURLPLUGIN = [
        'DEV' => "https://fa-ecptools-dev.azurewebsites.net/api",
        'UAT' => "https://fa-ecptools-uat.azurewebsites.net/api",
        'PRO' => "https://fa-ecptools-prd.azurewebsites.net/api"
    ];

    const APIURLPLUGINMANAGER = [
        'DEV' => "https://fa-ecpanalytics-dev.azurewebsites.net/api",
        'UAT' => "https://fa-ecpanalytics-uat.azurewebsites.net/api",
        'PRO' => "https://fa-ecpanalytics-prd.azurewebsites.net/api"
    ];

    const DEVELOPER_LICENSE_NUMBER = "ED466785DB641E6C";
    const USERNAME = "USERNAME";
    const CONSTPASSSTRING = "PASSWORD";
    const SERVICE_ACCESS_TOKEN = "SERVICE_ACCESS_TOKEN";
    const USERNAMESMALL = "Username";
    const CONSTPASSSMALLSTRING = "Password";
    const ACCESSLICENSENUMBER = "AccessLicenseNumber";
    const REMOTE_ADDR = 'REMOTE_ADDR';

    protected $httpClient;
    protected $request;
    protected $response;
    protected $uri;
    protected $logApi;
    protected $scopeConfig;
    protected $configWriter;
    protected $method;
    protected $licenseModel;
    protected $convertToAscii;
    protected $requestClient;
    protected $bearerToken;
    protected $modelAPI;
    protected $cacheTypeList;
    protected $storeManager;

    /**
     * ClientAPI __construct
     *
     * @param string $requestClient     //The requestClient
     * @param string $httpClientFactory //httpClientFactory
     * @param string $configWriter      //The configWriter
     * @param string $scopeConfig       //The scopeConfig
     * @param string $cacheTypeList     //The cacheTypeList
     * @param string $logApiModel       //logApiModel
     * @param string $modelLicense      //modelLicense
     * @param string $modelAPI          //The modelAPI
     * @param string $storeManager      //The storeManager
     * @param string $convertToAscii    //convertToAscii
     *
     * @return null
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $requestClient,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \UPS\Shipping\Model\LogApi $logApiModel,
        \UPS\Shipping\Model\License $modelLicense,
        \UPS\Shipping\Model\Savelog $modelAPI,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \UPS\Shipping\API\ConvertToASCII $convertToAscii
    ) {
        $this->requestClient = $requestClient;
        $this->licenseModel = $modelLicense;
        $this->httpClient = $httpClientFactory;
        $this->scopeConfig = $scopeConfig;
        $this->cacheTypeList = $cacheTypeList;
        $this->configWriter = $configWriter;
        $this->modelAPI = $modelAPI;
        $this->logApi = $logApiModel;
        $this->storeManager = $storeManager;
        $this->convertToAscii = $convertToAscii;
    }

    /**
     * ClientAPI convertToAscii
     *
     * @param string $data //The data
     *
     * @return $ipaddress
     */
    public function convertToAscii(&$data)
    {
        $this->convertToAscii->transliterator($data);
    }

    /**
     * ClientAPI getLicense
     *
     * @author UPS <noreply@ups.com>
     *
     * @return $ipaddress
     */
    public function getLicense()
    {
        // get license data
        $data = $this->licenseModel->checkIsset();
        if (self::ENV == "DEV") {
            $dataFormat = [
                self::USERNAME => "TuChu0103",
                self::CONSTPASSSTRING => "T!@#052018",
                self::SERVICE_ACCESS_TOKEN => "0D46678E86A9D038",
                "ACCESS_LICENSE_TEXT" => $data["AccessLicenseText"]
            ];
        } else {
            $dataFormat = [
                self::USERNAME => $data[self::USERNAMESMALL],
                self::CONSTPASSSTRING => $data[self::CONSTPASSSMALLSTRING],
                self::SERVICE_ACCESS_TOKEN => $data[self::ACCESSLICENSENUMBER],
                "ACCESS_LICENSE_TEXT" => $data["AccessLicenseText"],
            ];
        }
        return $dataFormat;
    }

    /**
     * ClientAPI setRequestByObject
     *
     * @param string $request //The request
     *
     * @return $ipaddress
     */
    public function setRequestByObject($request)
    {
        $this->request = json_encode($request);
    }

    /**
     * ClientAPI setRequest
     *
     * @param string $request //The request
     *
     * @return $ipaddress
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * ClientAPI setApiUrlByName
     *
     * @param string $name //The name
     *
     * @return $ipaddress
     */
    public function setApiUrlByName($name)
    {
        $this->method = $name;
        // get uri
        $this->uri = self::APIURL[self::ENV] . "/" . $name;
        if ($name == 'PromoDiscountAgreeMent') {
            $this->uri = self::APIURL[self::ENV] . "/" . 'PromoDiscount';
        }
    }

    /**
     * ClientAPI setApiUrlByName
     *
     * @param string $name //The name
     *
     * @return $ipaddress
     */
    public function setApiUrlByNamePlugin($name)
    {
        $this->method = $name;
        $baseRequestUrl = $this->storeManager->getStore()->getBaseUrl();
        $linkPluginManager = 'PRO';
        if (strpos($baseRequestUrl, \UPS\Shipping\Helper\Config::UAT_STRING) !== false) {
            $linkPluginManager = 'UAT';
        }
        // get uri
        $this->uri = self::APIURLPLUGIN[$linkPluginManager] . "/" . $name;
    }

    /**
     * ClientAPI setApiUrlByPluginManager
     *
     * @param string $name //The name
     *
     * @return $ipaddress
     */
    public function setApiUrlByPluginManager($name)
    {
        $this->method = $name;
        $baseRequestUrl = $this->storeManager->getStore()->getBaseUrl();
        $linkPluginManager = 'PRO';
        if (strpos($baseRequestUrl, \UPS\Shipping\Helper\Config::UAT_STRING) !== false) {
            $linkPluginManager = 'UAT';
        }
        // get uri
        $this->uri = self::APIURLPLUGINMANAGER[$linkPluginManager] . "/" . $name;
    }

    /**
     * ClientAPI setUri
     *
     * @param string $uri //The uri
     *
     * @return $ipaddress
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * ClientAPI getBaseRequest2
     *
     * @author UPS <noreply@ups.com>
     *
     * @return $ipaddress
     */
    public function getBaseRequest2()
    {
        $request = (object)[];
        // get license
        $License = $this->getLicense();
        $request->UPSSecurity = [
            "UsernameToken" => [
                self::USERNAMESMALL => $License[self::USERNAME],
                self::CONSTPASSSMALLSTRING => $License[self::CONSTPASSSTRING]
            ],
            "ServiceAccessToken" => [
                self::ACCESSLICENSENUMBER => $License[self::SERVICE_ACCESS_TOKEN]
            ]
        ];
        return $request;
    }


    /**
     * ClientAPI getBaseRequest2
     *
     * @author UPS <noreply@ups.com>
     *
     * @return $ipaddress
     */
    public function getBaseRequestBearer()
    {
        $request = (object)[];
        // get license
        $License = $this->getLicense();
        return $request;
    }

    /**
     * ClientAPI getBaseRequestNoAccountBearer
     *
     * @author UPS <noreply@ups.com>
     *
     * @return $ipaddress
     */
    public function getBaseRequestNoAccountBearer()
    {
        $request = (object)[];
        return $request;
    }

    /**
     * ClientAPI getBaseRequestNoAccount
     *
     * @author UPS <noreply@ups.com>
     *
     * @return $ipaddress
     */
    public function getBaseRequestNoAccount()
    {
        $request = (object)[];
        $request->UPSSecurity = [
            "UsernameToken" => [
                self::USERNAMESMALL => "TuChu0103",
                self::CONSTPASSSMALLSTRING => "T!@#052018"
            ],
            "ServiceAccessToken" => [
                self::ACCESSLICENSENUMBER => "0D46678E86A9D038"
            ]
        ];
        return $request;
    }

    /**
     * ClientAPI getMethodName
     *
     * @author UPS <noreply@ups.com>
     *
     * @return $ipaddress
     */
    private function _getMethodName()
    {
        if (!empty($this->method)) {
            return $this->method;
        } else {
            $a_Uri = explode('/', $this->uri);
            return $a_Uri[count($a_Uri) - 1];
        }
    }

    /**
     * ClientAPI doRequest
     *
     * @author UPS <noreply@ups.com>
     *
     * @return $ipaddress
     */
    public function doRequest()
    {
        if (empty($this->uri) || empty($this->request)) {
            return json_encode([]);
        } else {
            $client = $this->httpClient->create();
            $client->setUri($this->uri);
            $dataConfig = [
                'maxredirects' => 0,
                'timeout'      => 600
            ];
            $client->setConfig($dataConfig);
            $arrayDataRequest = [
                'method' => $this->_getMethodName(), 'full_uri' => $this->uri,
                'request' => $this->request, 'time_request' => \date('Y-m-d H:i:s')
            ];
            $logId = $this->logApi->writeRequest($arrayDataRequest);
            // get response from post request
            $this->response = $client->setRawData($this->request, 'application/json')->request('POST');
            // write response log api
            $arrayLogApi = ['id' => $logId, 'response' => $this->response->getBody(), 'time_response' => \date('Y-m-d H:i:s')];
            $this->logApi->writeResponse($arrayLogApi);
            // Set response to return, this may be overwrite when call save log api error
            $res = $this->response;
            if (is_object(json_decode($res->getBody())) && property_exists(json_decode($res->getBody()), 'Fault')) {
                $this->SaveLogApiError();
            }
            return $res;
        }
    }

    /**
     * ClientAPI doRequestBearer
     *
     * @author UPS <noreply@ups.com>
     *
     * @return $ipaddress
     */
    public function doRequestBearer()
    {
        if (empty($this->uri) || empty($this->request)) {
            return json_encode([]);
        } else {
            $client = $this->httpClient->create();
            $headers = [
                'Authorization' => 'Bearer ' . $this->bearerToken,
                'Accept'        => 'application/json',
            ];
            $client->setHeaders($headers);
            $client->setUri($this->uri);
            $dataConfig = [
                'maxredirects' => 0,
                'timeout'      => 600
            ];
            $client->setConfig($dataConfig);
            $arrayDataRequest = [
                'method' => $this->_getMethodName(), 'full_uri' => $this->uri,
                'request' => $this->request, 'time_request' => \date('Y-m-d H:i:s')
            ];
            $logId = $this->logApi->writeRequest($arrayDataRequest);
            // get response from post request
            $this->response = $client->setRawData($this->request, 'application/json')->request('POST');
            // write response log api
            $arrayLogApi = ['id' => $logId, 'response' => $this->response->getBody(), 'time_response' => \date('Y-m-d H:i:s')];
            $this->logApi->writeResponse($arrayLogApi);
            // Set response to return, this may be overwrite when call save log api error
            $res = $this->response;
            $resBody = json_decode($res->getBody());
            if (is_object($resBody) && property_exists($resBody, 'error') && !empty($resBody->error)) {
                $this->SaveLogApiError();
            }
            return $res;
        }
    }

    /**
     * ClientAPI doRequestPluginManager
     *
     * @author UPS <noreply@ups.com>
     *
     * @return $ipaddress
     */
    public function doRequestPluginManager()
    {
        if (empty($this->uri) || empty($this->request)) {
            return json_encode([]);
        } else {
            $client = $this->httpClient->create();
            $headers = [
                'Authorization' => 'Bearer ' . $this->bearerToken,
                'Accept'        => 'application/json',
            ];
            $client->setHeaders($headers);
            $client->setUri($this->uri);
            $dataConfig = [
                'maxredirects' => 0,
                'timeout'      => 600
            ];
            $client->setConfig($dataConfig);
            $arrayDataRequest = [
                'method' => $this->_getMethodName(), 'full_uri' => $this->uri,
                'request' => $this->request, 'time_request' => \date('Y-m-d H:i:s')
            ];
            $logId = $this->logApi->writeRequest($arrayDataRequest);
            //$client->setMethod(\Zend_Http_Client::POST);
            //$client->setRawData($this->request, 'application/json');
            // get response from post request
            $this->response = $client->setRawData($this->request, 'application/json')->request('POST');
            // write response log api
            $arrayLogApi = ['id' => $logId, 'response' => $this->response->getBody(), 'time_response' => \date('Y-m-d H:i:s')];
            $this->logApi->writeResponse($arrayLogApi);
            // Set response to return, this may be overwrite when call save log api error
            $res = $this->response;
            $resBody = json_decode($res->getBody());
            if (is_object($resBody) && property_exists($resBody, 'error') && !empty($resBody->error)) {
                $this->SaveLogApiError();
            }
            return $res;
        }
    }

    /**
     * CurlApi save log manager
     *
     * @param string $method   //The method
     * @param string $request  //The request
     * @param string $response //The response
     *
     * @return null
     */
    public function saveLogManager($method, $request , $response)
    {
        $this->modelAPI->saveLogRetry($method, $request, $response);
    }

    /**
     * CurlApi remove retry
     *
     * @param string $id // The id
     *
     * @return null
     */
    public function removeRetry($id)
    {
        $this->modelAPI->removeRetry($id);
    }

    /**
     * CurlApi update retry
     *
     * @param string $id //The id
     *
     * @return null
     */
    public function updateCountRetry($id)
    {
        $this->modelAPI->updateCountRetry($id);
    }

    /**
     * ClientAPI getClientIP
     *
     * @author UPS <noreply@ups.com>
     *
     * @return $ipaddress
     */
    public function getClientIP()
    {
        $ipaddress = $this->requestClient->getServer(self::REMOTE_ADDR);
        $clientIP = $this->requestClient->getServer('HTTP_CLIENT_IP');
        $forwardedXFor = $this->requestClient->getServer('HTTP_X_FORWARDED_FOR');
        $forwardedIP = $this->requestClient->getServer('HTTP_X_FORWARDED');
        $forwardedFor = $this->requestClient->getServer('HTTP_FORWARDED_FOR');
        $forwardedNo = $this->requestClient->getServer('HTTP_FORWARDED');
        $remoteAddr = $this->requestClient->getServer(self::REMOTE_ADDR);
        if (isset($clientIP)) {
            $ipaddress = $clientIP;
        } elseif (isset($forwardedXFor)) {
            $ipaddress = $forwardedXFor;
        } elseif (isset($forwardedIP)) {
            $ipaddress = $forwardedIP;
        } elseif (isset($forwardedFor)) {
            $ipaddress = $forwardedFor;
        } elseif (isset($forwardedNo)) {
            $ipaddress = $forwardedNo;
        } elseif (isset($remoteAddr)) {
            $ipaddress = $remoteAddr;
        } else {
            $ipaddress = 'UNKNOWN';
        }

        if (filter_var($ipaddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $ipaddress = '127.0.0.1';
        }
        $lastIndex = strpos($ipaddress, ",");
        if ($lastIndex > -1) {
            $ipArray = explode(",", $ipaddress);
            $countIP = count($ipArray);
            if ($countIP > 0) {
                $ipaddress = trim($ipArray[0]);
            }
        }
        return $ipaddress;
    }

    /**
     * Save log error api
     *
     */
    public function SaveLogApiError()
    {
        $request = (object)[];
        $request->Platform = '30';
        $request->CountryCode = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::CARRIER_UPS_SHIPPING_COUNTRY_CODE);
        $request->MerchantUrl = $this->storeManager->getStore()->getCurrentUrl();
        $request->MerchantKey = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_MERCHANTKEY);
        $request->LogApiUrl = $this->uri;
        $request->LogApiRequest = $this->request;
        $request->LogApiResponse = $this->response->getBody();
        // Set new request and url
        $this->setRequestByObject([$request]);
        $this->setApiUrlByPluginManager('Merchant/WriteLogger');
        // Write request log api
        $arrayDataRequest = [
            'method' => $this->_getMethodName(), 'full_uri' => $this->uri,
            'request' => $this->request, 'time_request' => \date('Y-m-d H:i:s')
        ];
        $logId = $this->logApi->writeRequest($arrayDataRequest);
        // Create http client
        $client = $this->httpClient->create();
        if (!empty($this->bearerToken)) {
            $bearerToken = $this->bearerToken;
        } else {
            $bearerToken = $this->scopeConfig->getValue(\UPS\Shipping\Helper\Config::UPS_SERVICE_LONG_SECURITY_TOKEN);
        }
        $headers = [
            'Authorization' => 'Bearer ' . $bearerToken,
            'Accept'        => 'application/json',
        ];
        $client->setHeaders($headers);
        $client->setUri($this->uri);
        $dataConfig = [
            'maxredirects' => 0,
            'timeout'      => 600
        ];
        $client->setConfig($dataConfig);
        // get response from post request
        $this->response = $client->setRawData($this->request, 'application/json')->request('POST');
        // write response log api
        $arrayLogApi = [
            'id' => $logId,
            'response' => $this->response->getBody(),
            'time_response' => \date('Y-m-d H:i:s')
        ];
        $this->logApi->writeResponse($arrayLogApi);
    }
}
