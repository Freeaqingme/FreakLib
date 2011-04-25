<?php
require_once 'Zend/Json.php';
require_once 'Zend/Http/Client.php';

class Freak_ThirdPartyService_SmsCloud
{
    protected $_apiKey;

    // Locks you into a non-changing version of the API (invalid or empty version uses latest version)
    protected $_apiVersion = '1.0';

    // This can be either jsonrpc or xmlrpc
    protected $apiEndPoint = 'jsonrpc';
    //protected $_apiEndpoint = 'xmlrpc';

    // Can also be https, use the useSSL() method to toggle
    protected $useSSL = 'https';
    
    protected $allowMitmAttack = false;

    protected $apiHosts = array('api.smscloud.com',
                                'api-backup.smscloud.com');

    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }
    
    public function setOptions(array $options = array())
    {
        foreach($options as $key => $value) {
            $key = strtolower($key);
            switch($key) {
                case 'apiEndPoint':
                    $this->setApiEndPoint($value);
                    break;
                case 'usessl':
                    $this->useSSL($value);
                    break;
                case 'apikey':
                    $this->setApiKey($value);
                    break;
                case 'apihosts':
                    $this->setApiHosts($value);
                    break;
                case 'allowmitmattacks':
                    $this->allowMitmAttack($value);
                    break;
            }
        }
        
        return $this;
    }
    
    public function setApiHosts(array $hosts) {
        if(count($hosts) == 0) {
            throw new Excpetion('You should specify at least one API host');
        }
        
        $this->apiHosts = $hosts;
        return $this;
    }
    
    public function getApiHosts() {
        return $this->apiHosts;
    }
    
    public function setApiEndPoint($endpoint) {
        $endpoint = strtolower($endpoint);
        
        if($endpoint == 'jsonrpc') {
            if (!function_exists('json_encode')) {
                throw new Exception
                        ('JSON-RPC API was specified, but the JSON PHP extension
                          is not installed. Pick the xml endpoint or install the json extension');
            }
        } elseif($endpoint == 'xmlrpc') {
            if (!function_exists('xmlrpc_encode_request')) {
                throw new Exception('XML-RPC API was specified, but the XML-RPC PHP extension is not installed. See http://www.php.net/manual/en/xmlrpc.installation.php');
            }
        } else {
            throw new Exception('Invalid API endpoint. Valid endpoints are jsonrpc and xmlrpc');
        }

        $this->apiEndPoint = $endpoint;
        return $this;
    }
    
    public function getApiEndPoint() {
        return $this->apiEndPoint;
    }

    public function useSSL($useSSL = null)
    {
        if($useSSL === null) {
            return $this->useSSL == 'https';
        }
        
        $this->useSSL = (bool) $useSSL;
        return $this;
    }
    
    public function allowMitmAttack($allow = null) {
        if($allow === null) {
            return $this->allowMitmAttack;
        }
        
        $this->allowMitmAttack = (bool) $allow;
        return $this;
    }
        
    public function setApiKey($apiKey)
    {
        $this->_apiKey = $apiKey;
        return $this;
    }
    
    public function getApiKey() {
        return $this->_apiKey;
    }
    
    public function getApiVersion() {
        return $this->_apiVersion;
    }

    public function performCarrierLookup($phoneNumbers)
    {
        if(is_array($phoneNumbers)) {
            return $this->request('nvs.carrierLookupBulk', array($phoneNumbers));
        } else {
            return $this->request('nvs.carrierLookup', $phoneNumbers);
        }
    }

    public function sendSMS($fromNumber, $toNumber, $message, $priority = 1)
    {
        $params = array($fromNumber, $toNumber, $message, $priority);
        return $this->request('sms.send', $params);
    }

    protected function request($method, $params)
    {
        $params = (array) $params;
        
        $hosts = $this->getApiHosts();
        $iterator = 0;
        $hostCount = count($hosts);
        foreach($hosts as $host) {
            $iterator++;
            try {
                if($this->getApiEndpoint() == 'jsonrpc') {
                    return $this->jsonRpcRequest($method, $params, $host);
                } else {
                    throw new Exception('xmlrpc not implemented (in this demo)');
                }
            } catch(Exception $e) {
                if($iterator == $hostCount) {
                    throw $e;
                }
            }
        } 
    }

    protected function jsonRpcRequest($method, $params, $host)
    {
        $requestID = uniqid('',true);
        $reqParams = Zend_Json::encode(array('method' => $method,
        									 'params' => $params,
        									 'id'=>$requestID));

        $apiUrl = ($this->useSSL? 'https' : 'http')
                . '://'.$host.'/jsonrpc?key=' . $this->getApiKey()
                . '&apiVersion=' . $this->getApiVersion();
                
        $client = new Zend_Http_Client($apiUrl);
        $client->setRawData($reqParams);
        
        if($this->allowMitmAttack() && $client->getAdapter() instanceof Zend_Http_Adapter_Curl) {
            $client->getAdapter()->setCurlOption(CURLOPT_SSL_VERIFYPEER, 0);
            $client->getAdapter()->setCurlOption(CURLOPT_SSL_VERIFYHOST, 0);
        }
        
        // If this fails, it will throw an exception
        $response = $client->request(Zend_Http_Client::POST); 
        return Zend_Json::decode($response->getBody());
    }
}
