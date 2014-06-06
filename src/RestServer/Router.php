<?php
namespace RestServer;

use RestServer\Exception\RestServerException;
use RuntimeException;

class Router
{
    protected $request;

    protected $routes = array();

    protected $tokens = array();

    public function __construct(Request $request = null)
    {
        if ($request === null) {
            $request = new Request();
        }
        $this->request = $request;
    }

    public function run()
    {
        $this->sortRoutes();

        try {
            $params = array();
            $callable = $this->matchRoute($this->request->getRequestMethod(), $this->request->getUrl(), $params);
            $this->authenticate();
            $result = call_user_func_array($callable, $params); 
        } catch (RestServerException $e) {
            $result = $e->asArray();
        }

        $body = $this->json($result);
        $response = new Response(200, $body, array("Content-type: application/json"));
        $response->send();
    }

    protected function matchRoute($method, $url, &$params)
    {
        foreach ($this->routes as $route) {
            if ($route->match($method, $url, $params)) {
                return $route->getCallable();
            }
        }
        
        throw new RestServerException("404");
    }

    protected function sortRoutes()
    {
        usort($this->routes, function($a, $b) {
            return $a->compareTo($b);
        });
    }

    protected function json($data)
    {
        $json = json_encode($data);
        
        if (json_last_error() != JSON_ERROR_NONE) {
            switch (json_last_error()) {
                case JSON_ERROR_DEPTH:
                    $json_error = 'The maximum stack depth has been exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $json_error =  'Invalid or malformed JSON';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $json_error =  'Control character error, possibly incorrectly encoded';
                    break;
                case JSON_ERROR_SYNTAX:
                    $json_error =  'Syntax error';
                    break;
                case JSON_ERROR_UTF8:
                    $json_error =  'Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                default:
                    $json_error =  'Unknown error';
                    break;
            }
            throw new RuntimeException("JSON encode error: ".$json_error."");
        }
        
        return $json;
    }

    protected function authenticate()
    {
        $auth_key = $this->request->getParam("auth_key", null);

        if ($auth_key === null) {
            throw new RestServerException(RestServerException::UNAUTHORIZED, 0, "Missing auth key", "");
        }

        if (!isset($this->tokens[$auth_key])) {
            throw new RestServerException(RestServerException::UNAUTHORIZED, 0, "Invalid auth key", "");
        }

        $token = $this->tokens[$auth_key];

        $token->authenticate($this->request->getRequestMethod(), $this->request->getUrl(), $this->request->getParam());
    }

    public function get($url, $callable)
    {
        $this->routes[] = new Route(Request::METHOD_GET, $url, $callable);
    }

    public function post($url, $callable)
    {
        $this->routes[] = new Route(Request::METHOD_POST, $url, $callable);
    }

    public function put($url, $callable)
    {
        $this->routes[] = new Route(Request::METHOD_PUT, $url, $callable);
    }

    public function delete($url, $callable)
    {
        $this->routes[] = new Route(Request::METHOD_DELETE, $url, $callable);
    }

    public function auth($key, $secret)
    {
        return $this->tokens[$key] = new Token($key, $secret);
    }
}