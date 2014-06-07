<?php

use RestServer\Request;
use RestServer\Router;

class RouterTest extends PHPUnit_Framework_TestCase
{
    public function testRouter()
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

        /*
        $router->get("hello", function() {
            return 1;
        });
        $router->get("*", function() {
            return 2;
        });
        $router->get("ḧello/to", function() {
            return 3;
        });
        $router->get("ḧello/*", function() {
            return 4;
        });               
        $callable = $router->matchRoute("GET", "/hello", $p);
        $this->assertEquals(2, $callable());

        $callable = $router->matchRoute("GET", "/hello/andy", $p);
        $this->assertEquals(4, $callable());
        */
    }

}