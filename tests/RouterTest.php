<?php

use RestServer\Request;
use RestServer\Router;

class RouterTest extends PHPUnit_Framework_TestCase
{
    private function createRouter()
    {
        $senv = array(
            "REQUEST_URI" => "",
            "SCRIPT_NAME" => "",
            "REQUEST_METHOD" => "GET",
        );
        $params = array();
        $headers = array();

        $request = new Request($senv, $params, $headers);
        $router = new Router($request);
        return $router;       
    }

    public function testRouterSort()
    {
        $router = $this->createRouter();

        $router->get("/hello", function() {});
        $router->get("/*", function() {});
        $router->get("/hello/foo", function() {});
        $router->get("/hello/*", function() {});        
        $router->get("/hello/foo/bar", function() {});
        $router->get("/hello/*/*", function() {});  
        $router->get("/*/*/hello", function() {});  

        $router->sortRoutes();

        $correct_order_string = "GET /hello/foo/bar\nGET /hello/foo\nGET /hello\nGET /hello/*/*\nGET /hello/*\nGET /*/*/hello\nGET /*\n";

        $order_string = "";
        foreach ($router->getRoutes() as $route) {
            $order_string .= $route . "\n";
        }

        $this->assertEquals($correct_order_string, $order_string);
    }

    public function testRouterMatch()
    {
        $router = $this->createRouter();
        $router->get("/hello", function() { return 1; });
        $router->get("/*", function() { return 2; });
        $router->get("/hello/foo", function() { return 3; }); 
        $router->get("/*/*/hello", function() { return 4; });

        $c = $router->matchRoute("GET", "/hello", $params);
        $this->assertEquals(1, $c());

        $c = $router->matchRoute("GET", "/hello/", $params);
        $this->assertEquals(1, $c());

        $c = $router->matchRoute("GET", "/foo", $params);
        $this->assertEquals(2, $c());

        $c = $router->matchRoute("GET", "/hello/foo", $params);
        $this->assertEquals(3, $c());

        $c = $router->matchRoute("GET", "/foo/bar/hello", $params);
        $this->assertEquals(4, $c());                

        $c = $router->matchRoute("GET", "/hello/foo/hello", $params);
        $this->assertEquals(4, $c());

        $c = $router->matchRoute("GET", "/hello/foo/hello/", $params);
        $this->assertEquals(4, $c());

        $this->setExpectedException('RestServer\Exception\RestServerException');
        $c = $router->matchRoute("GET", "/hello/foo/hello/foo", $params);
    }


}