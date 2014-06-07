<?php
namespace RestServer;

use RestServer\Exception\RestServerException;
use Signature\Signer;
use Signature\Server;
use Signature\Exception\AuthenticationException;

class Token
{
    /** @var string API key */
    protected $key;

    /** @var string API secret */
    protected $secret;

    /** @var array Valid URL regex expression */
    protected $valid_url_regexs = array();

    /**
     * Constructor
     *
     * @param string $key API key
     * @param string $secret API secret
     */
    public function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
        return $this;
    }

    /**
     * Add a URL for this Token
     *
     * @param string $url URL
     * @return self Returns self for method chaining
     */
    public function url($url)
    {
        $url = $url ? (trim($url, "/") . "/") : "";
        $this->valid_url_regexs[] = "~^" . $url . "(.*?)$~";
        return $this;
    }

    /**
     * Is URL valid for thos Token (A URL is valid as long it starts with it, eg "/hello" is valid for "/hello/foo")
     *
     * @param string $url URL
     * @return bool
     */
    protected function isValidUrl($url)
    {
        $url = trim($url, "/") . "/";
        foreach ($this->valid_url_regexs as $re) {
            if (preg_match($re, $url)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Authenticate Token, chgek URL and signature parameters
     *
     * @param string $method HTTP method
     * @param string $url URL
     * @param array $params Parameters
     * @return void
     * @throws RestServerException If not authenticated
     */
    public function authenticate($method, $url, $params)
    {
        if (!$this->isValidUrl($url)) {
            throw new RestServerException(RestServerException::UNAUTHORIZED, 0, "You are not allowed to access this URL", "");
        }

        try {
            $signature_server = new Server(new Signer());
            $signature_server->authenticate($this->secret, $method, $url, $params);
        } catch (AuthenticationException $e) {
            throw new RestServerException(RestServerException::UNAUTHORIZED, 0, "Invalid request signature: " . $e->getMessage(), "");
        }
    }
}