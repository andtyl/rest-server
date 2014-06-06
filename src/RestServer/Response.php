<?php
namespace RestServer;

class Response
{
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

    protected $headers = array();   

    protected $code;

    protected $body;    
    
    public function __construct($code = 200, $body = "", $headers = array())
    {
        $this->setCode($code);
        $this->setBody($body);
        $this->setHeaders($headers);
    }
    
    public function addHeader($header)
    {
        $this->headers[] = $header;
    }   

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }   
    
    public function getHeaders()
    {
        return $this->headers;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }   
    
    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function send()
    {
        header_remove("X-Powered-By");
        header("HTTP/1.1 ".$this->getCode()." ".$this->getHttpStatusCodeText());
        foreach ($this->getHeaders() as $header) {
            header($header);
        }
        echo $this->getBody();
    }

    protected function getHttpStatusCodeText($code = null)
    {
        $code = $code ?: $this->getCode();
        return isset($this->http_status_code_texts[$code]) ? $this->http_status_code_texts[$code] : "";
    }
}