<?php
namespace RestServer;

use RestServer\Exception\RestServerException;
use Signature\Signer;
use Signature\Server;
use Signature\Exception\AuthenticationException;

class Token
{
    protected $key;

    protected $secret;

    protected $valid_url_regexs = array();

    public function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
        return $this;
    }

    public function url($url)
    {
        $url = $url ? (trim($url, "/") . "/") : "";
        $this->valid_url_regexs[] = "~^" . $url . "(.*?)$~";
        return $this;
    }

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

    public function authenticate($method, $path, $params)
    {
        if (!$this->isValidUrl($path)) {
            throw new RestServerException(RestServerException::UNAUTHORIZED, 0, "You are not allowed to access this URL", "");
        }

        try {
            $signature_server = new Server(new Signer());
            $signature_server->authenticate($this->secret, $method, $path, $params);
        } catch (AuthenticationException $e) {
            throw new RestServerException(RestServerException::UNAUTHORIZED, 0, "Invalid request signature: " . $e->getMessage(), "");
        }
    }
}