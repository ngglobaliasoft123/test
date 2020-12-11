<?php

/**
 * CurlApi file
 *
 * @category UPS_Shipping
 * @package  UPS_Shipping
 * @author   United Parcel Service of America, Inc. <support@ups.com>
 * @license  This work is Licensed under the Academic Free License version 3.0
 * http://opensource.org/licenses/afl-3.0.php
 * @link     https://www.ups.com/us/en/help-center/technology-support/ready-program/e-commerce.page
 */
namespace UPS\Shipping\API;

use Psr\Log\LoggerInterface as PsrLogger;

/**
 * CurlApi file
 *
 * @category UPS_Shipping
 * @package  UPS_Shipping
 * @author   United Parcel Service of America, Inc. <support@ups.com>
 * @license  This work is Licensed under the Academic Free License version 3.0
 * http://opensource.org/licenses/afl-3.0.php
 * @link     https://www.ups.com/us/en/help-center/technology-support/ready-program/e-commerce.page
 */
class CurlApi
{
    const ENV = "PRO";
    const MAXREDIRECTS = 4;
    const TIMEOUT = 30;
    const APIURLMANAGER = [
        'DEV' => "https://plugins-management-server.fsoft.com.vn/api",
        'PRO' => "https://eupluginapi.westeurope.cloudapp.azure.com/api"
    ];

    protected $request;
    protected $response;
    protected $uri;
    protected $token;
    protected $method;
    protected $licenseModel;
    protected $ModelAPI;
    protected $curlClient;
    protected $loggerClient;

    /**
     * CurlApi execute
     *
     * @param string $ModelAPI     //The ModelAPI
     * @param string $modelLicense //The modelLicense
     *
     * @return null
     */
    public function __construct(
        PsrLogger $logger,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \UPS\Shipping\Model\Savelog $ModelAPI,
        \UPS\Shipping\Model\License $modelLicense
    ) {
        $this->ModelAPI = $ModelAPI;
        $this->licenseModel = $modelLicense;
        $this->curlClient = $curl;
        $this->loggerClient = $logger;
    }

    /**
     * CurlApi function set request oject
     *
     * @param string $request //request call api
     *
     * @return null
     */
    public function setRequestByObject($request)
    {
        $this->request = json_encode($request);
    }

    /**
     * CurlApi function set request
     *
     * @param string $request //request call api
     *
     * @return null
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * CurlApi function set api url
     *
     * @param string $name //The name
     *
     * @return null
     */
    public function setApiUrlByName($name)
    {
        $this->method = $name;
        $this->uri = self::APIURLMANAGER[self::ENV] . "/" . $name;
    }

    /**
     * CurlApi set uri
     *
     * @param string $uri /The uri
     *
     * @return null
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * CurlApi set token
     *
     * @param string $token //The token
     *
     * @return null
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * CurlApi do request manager
     *
     * @return $response request data
     */
    public function doRequestManager()
    {
        if (empty($this->uri) || empty($this->request)) {
            return json_encode([]);
        } else {
            $requestData = base64_encode($this->request);
            $this->response = $this->doCurl($this->uri, $this->token, $requestData);
            $this->ModelAPI->saveLogAPI($this->method, $this->uri, $this->request, $this->response);
            return $this->response;
        }
    }

    /**
     * CurlApi do curl
     *
     * @param string $url         //The url
     * @param string $token       //The token
     * @param string $data_base64 //The data_base64
     *
     * @return $webpage
     */
    public function doCurl($url = 'nul', $token = '', $data_base64 = '')
    {
        if ($url != 'nul'){
            $this->_url = $url;
        }
        if ($token != 'nul'){
            $this->_token = $token;
        }
        $curl = curl_init();
        $header = [
            "Authorization: Bearer $token",
            "Cache-Control: no-cache",
            "Content-Type: application/json",
        ];

        if (empty($token)){
            $header = [
                "Cache-Control: no-cache",
                "Content-Type: application/json",
            ];
        }
        $component = [
            //CURLOPT_PORT => MERCHANT_PORT, //Trung lock CURLOPT_URL
            CURLOPT_URL => $this->_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => self::MAXREDIRECTS,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_POSTFIELDS => "{\"data\":\"$data_base64\"}",
        ];
        curl_setopt_array($curl, $component);
        $response = curl_exec($curl);

        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $this->_webpage = $err;
        } else {
            $this->_webpage = $response;
        }
        return $this->_webpage;
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
        $this->ModelAPI->saveLogRetry($method, $request, $response);
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
        $this->ModelAPI->removeRetry($id);
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
        $this->ModelAPI->updateCountRetry($id);
    }

    /**
     * CurlApi get license
     *
     * @return $dataFormat
     */
    public function getLicense()
    {
        $data = $this->licenseModel->checkIsset();
        if (self::ENV == "DEV") {
            $dataFormat = [
                "USERNAME" => "TuChu0103",
                "PASSWORD" => "T!@#052018",
                "SERVICE_ACCESS_TOKEN" => "0D46678E86A9D038",
                "ACCESS_LICENSE_TEXT" => $data["AccessLicenseText"]
            ];
        } else {
            $dataFormat = [
                "USERNAME" => $data["Username"],
                "PASSWORD" => $data["Password"],
                "SERVICE_ACCESS_TOKEN" => $data["AccessLicenseNumber"],
                "ACCESS_LICENSE_TEXT" => $data["AccessLicenseText"],
            ];
        }
        return $dataFormat;
    }

    /**
     * CurlApi get hrrp status
     *
     * @return $_status
     */
    public function getHttpStatus()
    {
        return $this->_status;
    }

    /**
     * CurlApi to string
     *
     * @return $_webpage
     */
    public function __tostring()
    {
        return $this->_webpage;
    }

    /**
     * CurlApi get error
     *
     * @return $_err
     */
    public function getError()
    {
        return $this->_err;
    }
}
