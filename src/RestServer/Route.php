<?php
namespace RestServer;

use RuntimeException;

class Route
{
    /** @var string HTTP Request Method */
    protected $request_method;

    /** @var string URL */
    protected $url;

    /** @var callable A callable (eg function) */
    protected $callable;

    /** @var string The regex URL to match */
    protected $url_regex;

    /** @var int Number of parts in the URL (eg /hello/foo = 2) */
    protected $num_url_parts;

    /** @var int Number of parts in the URL, only fixed (eg /hello/to/* = 2) */
    protected $num_url_parts_fixed;

    /** @var int Number of parts in the URL, only wildcard/parameter (eg /hello/to/* = 1) */
    protected $num_url_parts_parameter;

    /** @var int Position of first URL part parameter */
    protected $first_url_part_parameter_pos;

    /**
     * Constructor
     *
     * @param string $request_method HTTP Request Method
     * @param string $url URL
     * @param callable $callable Callable, eg function
     */
    public function __construct($request_method, $url, $callable)
    {
        $this->request_method = $request_method;

        //Ensure path is absolute
        $this->url = "/" . trim($url, "/");
        
        //Ensure callable is callable
        if (!is_callable($callable)) {
            throw new RuntimeException("\$callable must be a callable");
        }
        $this->callable = $callable;

        $this->parseUrl();
        $this->parseFirstUrlPartParameterPos();
    }

    /**
     * Parse URL, create a regex for the URL, and count number of URL parts
     *
     * @return void
     */
    protected function parseUrl()
    {
        $this->url_regex = "~" . preg_replace("~(/\*)~", "/(.*?)", $this->url, -1, $this->num_url_parts_parameter) . "~";
        $this->num_url_parts = substr_count($this->url, "/");
        $this->num_url_parts_fixed = $this->num_url_parts - $this->num_url_parts_parameter;
    }

    /**
     * Parse first URL part parameter position
     *
     * @return void
     */
    protected function parseFirstUrlPartParameterPos()
    {
        foreach (explode("/", $this->url) as $key => $part) {
            if ($part == "*") {
                $this->first_url_part_parameter_pos = $key;
                return;
            }
        }
        $this->first_url_part_parameter_pos = 0;        
    }

    /**
     * Match this Route against HTTP method and URL
     *
     * @param string $request_method HTTP method
     * @param string $url URL
     * @param array $params Parameters passed by reference (to be accessible from the calling Router)
     * @return bool Do match
     */
    public function match($request_method, $url, &$params = array())
    {
        return $this->matchRequestMethod($request_method) && $this->matchUrl($url, $params);
    }

    /**
     * Match this Routes HTTP method against HTTP method
     *
     * @param string $request_method HTTP metthod
     * @return bool Do match
     */
    protected function matchRequestMethod($request_method)
    {
        return $this->request_method == $request_method;
    }

    /**
     * Match this Routes URL against URL
     *
     * @param string $url URL
     * @param array $params Parameters passed by reference (to be accessible from the calling Router)
     * @return bool Do match
     */
    protected function matchUrl($url, &$params)
    {
        if (preg_match($this->url_regex, $url, $params)) {
           array_shift($params); //First match is the whole regex, remove!
           return true; 
        }
        return false;
    }

    /**
     * Compare this Route to another Route to find out which has "highest dignity"
     * To Be used in PHP comparison function.
     *
     * @param Route $route Instance of a Route
     * @return 1|0|-1
     */
    public function compareTo(Route $route)
    {
        if ($this->getFirstUrlPartParameterPos() > $route->getFirstUrlPartParameterPos()) {
            return 1;
        } elseif ($this->getFirstUrlPartParameterPos() < $route->getFirstUrlPartParameterPos()) {
            return -1;
        }

        if ($this->getNumUrlParts() == $route->getNumUrlParts()) {
            return 0;
        }

        return ($this->getNumUrlParts() > $route->getNumUrlParts()) ? -1 : 1;
    }

    /**
     * Get the callable
     *
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * Get number of URL parts
     *
     * @return int
     */
    public function getNumUrlParts()
    {
        return $this->num_url_parts;
    }

    /**
     * Get number of URL parts, only fixed
     *
     * @return int
     */
    public function getNumUrlPartsFixed()
    {
        return $this->num_url_parts_fixed;
    }

    /**
     * Get number of URL parameters, only parameter
     *
     * @return int
     */
    public function getNumUrlPartsParameter()
    {
        return $this->num_url_parts_parameter;
    }

    /**
     * Get position of the first url part parameter
     *
     * @return int
     */
    public function getFirstUrlPartParameterPos()
    {
        return $this->first_url_part_parameter_pos;
    }    
}