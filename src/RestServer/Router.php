<?php
namespace RestServer;

use RestServer\Exception\RestServerException;
use RuntimeException;

class Router
{
    /** @var Request Instance of Request */
    protected $request;

    /** @var array Array of Routes */
    protected $routes = array();

    /** @var boolean Should every request be authenticated */
    protected $authenticate = false;

    /** @var array Array of Tokens for authentication */
    protected $tokens = array();

    /**
     * Constructor
     *
     * @param Request|null $request Instance of Request or null
     */
    public function __construct(Request $request = null)
    {
        if ($request === null) {
            $request = new Request();
        }
        $this->request = $request;
    }

    /**
     * "Run" the Router
     *
     * @return void
     */
    public function run()
    {
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

    /**
     * Try to match a Route against the HTTP method and URL
     *
     * @param string $method HTTP method
     * @param string $url URL
     * @param array $params Params (passed by reference because the Route sets the URL parameters to this)
     * @return Route Instance of a Route
     * @throws RestServerException If no Route is matched
     */
    public function matchRoute($method, $url, &$params)
    {
        $this->sortRoutes();

        foreach ($this->routes as $route) {
            if ($route->match($method, $url, $params)) {
                return $route->getCallable();
            }
        }
        
        throw new RestServerException(RestServerException::NOT_FOUND, 0, "Not found", "");
    }

    /**
     * Sort the routes so that the Route with "highest precedence" is matched first
     *
     * @return void
     */
    public function sortRoutes()
    {
        usort($this->routes, function($a, $b) {
            return $a->compareTo($b);
        });       
    }

    /**
     * Get Routes
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Add Route
     *
     * @param Route $route Instance of Route
     */
    public function addRoute(Route $route)
    {
        $this->routes[] = $route;
    }

    /**
     * Generate JSON of a string, object or array
     *
     * @param mixed $data Can be anything but a resource
     * @return string
     */
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

    /**
     * Authenticate, check that the api key and signature of the request is OK
     *
     * @return void
     * @throws RestServerException On authentication failure
     */
    protected function authenticate()
    {
        if (!$this->authenticate) {
            return;
        }

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

    /**
     * Create a GET route
     *
     * @param string $url URL
     * @param callable $callable Callable
     * @return void
     */
    public function get($url, $callable)
    {
        $this->routes[] = new Route(Request::METHOD_GET, $url, $callable);
    }

    /**
     * Create a POST route
     *
     * @param string $url URL
     * @param callable $callable Callable
     * @return void
     */
    public function post($url, $callable)
    {
        $this->routes[] = new Route(Request::METHOD_POST, $url, $callable);
    }

    /**
     * Create a PUT route
     *
     * @param string $url URL
     * @param callable $callable Callable
     * @return void
     */
    public function put($url, $callable)
    {
        $this->routes[] = new Route(Request::METHOD_PUT, $url, $callable);
    }

    /**
     * Create a DELETE route
     *
     * @param string $url URL
     * @param callable $callable Callable
     * @return void
     */
    public function delete($url, $callable)
    {
        $this->routes[] = new Route(Request::METHOD_DELETE, $url, $callable);
    }

    /**
     * Set authentication, if so, every request need authentication
     *
     * @param [type] $authenticate
     */
    public function setAuthentication($authenticate)
    {
        $this->authenticate = (boolean)$authenticate;
    }

    /**
     * Create a authentication token
     *
     * @param string $key API Key
     * @param string $secret API Secret
     * @return Token The Token
     */
    public function auth($key, $secret)
    {
        $this->setAuthentication(true);
        return $this->tokens[$key] = new Token($key, $secret);
    }
}