<?php
namespace RestServer;

class Response
{
    /** @var array HTTP status codes and messages */
    protected $http_status_code_texts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );
    
    /** @var array HTTP headers */
    protected $headers = array();   

    /** @var int HTTP code */
    protected $code;

    /** @var string HTTP body */
    protected $body;    
    
    /**
     * Constructor
     *
     * @param int $code HTTP code
     * @param string $body HTTP body
     * @param array $headers HTTP headers
     */
    public function __construct($code = 200, $body = "", $headers = array())
    {
        $this->setCode($code);
        $this->setBody($body);
        $this->setHeaders($headers);
    }

    /**
     * Get HTTP headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    
    /**
     * Set HTTP headers
     *
     * @param array $headers HTTP headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * Add HTTP header
     *
     * @param string $header Header (eg "Header-name: Value")
     */
    public function addHeader($header)
    {
        $this->headers[] = $header;
    }   

    /**
     * Get HTTP code
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set HTTP code
     *
     * @param int $code HTTP code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }   
    
    /**
     * Get HTTP body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set HTTP body
     *
     * @param string $body HTTP body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Send Response to browser
     *
     * @return void
     */
    public function send()
    {
        header_remove("X-Powered-By");
        header("HTTP/1.1 ".$this->getCode()." ".$this->getHttpStatusCodeText());
        foreach ($this->getHeaders() as $header) {
            header($header);
        }
        echo $this->getBody();
    }

    /**
     * Get HTTP status code text
     *
     * @param int $code HTTP staus code
     * @return string
     */
    protected function getHttpStatusCodeText($code = null)
    {
        $code = $code ?: $this->getCode();
        return isset($this->http_status_code_texts[$code]) ? $this->http_status_code_texts[$code] : "";
    }
}