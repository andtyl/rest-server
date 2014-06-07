<?php
namespace RestServer;

use RestServer\Exception\RestServerException;

class Request
{
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const METHOD_PUT = "PUT";
    const METHOD_DELETE = "DELETE";

    /** @var array Server environment variables ($_SERVER) */ 
    protected $senv; 

    /** @var array Parameters */ 
    protected $params;  

    /** @var array Headers */
    protected $headers; 

    /** @var string The URL */
    protected $url;

    /**
     * Constructor
     */
    public function __construct(array $senv = null, array $params = null, array $headers = null)
    {
        //Set server environment variables
        $this->senv = ($senv !== null) ? array_merge($_SERVER, $senv) : $_SERVER;

        //Set params
        ($params !== null) ? $this->params = $params : $this->parseParams();

        //Set headers
        ($headers !== null) ? $this->headers = $headers : $this->parseHeaders();
        
        //Parse the requested URL
        $this->parseUrl();
    }   

    /**
     * Parse the URL
     *
     * @return void
     */
    protected function parseUrl()
    {
        $url_path = "";
        if (isset($this->senv['REQUEST_URI']) && $this->senv['REQUEST_URI']) {
            $url_path = $this->senv['REQUEST_URI'];
            $str_pos = strpos($url_path, "?");
            if ($str_pos !== false) {
                $url_path = substr($url_path, 0, $str_pos);
            }
        }

        //Remove sub dir path from url path
        if (isset($this->senv['SCRIPT_NAME']) && $this->senv['SCRIPT_NAME']) {
            $sub_dir_path = dirname($this->senv['SCRIPT_NAME']);
            $url_path = substr($url_path, strlen($sub_dir_path)); 
        }

        $url_path = rtrim($url_path, "/");

        if (empty($url_path)) {
            $url_path = "/";
        }
        
        $this->url = $url_path;
    }

    /**
     * Parse the params
     *
     * @return void
     */
    protected function parseParams()
    {
        switch ($this->getRequestMethod()) {
            case self::METHOD_GET:
                $this->params = $_GET;
                break;
            case self::METHOD_POST:
                $this->params = $_POST;
                break;
            case self::METHOD_PUT:
                parse_str(file_get_contents("php://input"), $this->params);
                break;
            case self::METHOD_DELETE:
                $this->params = array();
                break;
            default:
                throw new RestServerException(RestServerException::METHOD_NOT_ALLOWED, 0, "", "");
        }       
    }
    
    /**
     * Parse the HTTP Headers
     *
     * @return void
     */
    protected function parseHeaders()
    {
        if (function_exists("apache_request_headers")) {
            $this->headers = apache_request_headers(); 
        }
    }

    /**
     * Get the HTTP Method
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return isset($this->senv['REQUEST_METHOD']) ? $this->senv['REQUEST_METHOD'] : "";
    }

    /**
     * Get the HTTP User Agent
     * 
     * @return string
     */
    public function getUserAgent()
    {
        return isset($this->senv['HTTP_USER_AGENT']) ? $this->senv['HTTP_USER_AGENT'] : ""; 
    }
    
    /**
     * Is the request made over HTTPS (TLS)
     *
     * @return boolean
     */
    public function isSecure()
    {
        return isset($this->senv['HTTPS']) && $this->senv['HTTPS'] ? true : false;
    }

    /**
     * Get the IP of the remote
     *
     * @return string
     */
    public function getIp()
    {
        return isset($this->senv['REMOTE_ADDR']) ? $this->senv['REMOTE_ADDR'] : "";
    }

    /**
     * Get the URL
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get a parameter
     *
     * @param string|null $key Parameter name or null to get alla parameters in array
     * @return string|array
     */
    public function getParam($key = null)
    {
        if ($key !== null) {
            return isset($this->params[$key]) ? $this->params[$key] : "";
        } else {
            return $this->params;
        }
    }

    /**
     * The HTTP Header
     *
     * @param string $header Header name
     * @return string
     */
    public function getHeader($header)
    {
        return isset($this->headers[$header]) ? $this->headers[$header] : "";
    }   
}