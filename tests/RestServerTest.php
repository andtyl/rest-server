<?php

use RestServer\Request;
use RestServer\Router;
use Signature\Client;
use Signature\Signer;

class RestServerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testRestServer()
    {
        $URL = "/hello/Hi";
        $METHOD = "POST";
        $KEY = "123";
        $SECRET = "456";

        //_SERVER
        $senv = array(
            "HTTP_USER_AGENT" => "",
            "REQUEST_URI" => $URL,
            "SCRIPT_NAME" => "/index.php",
            "REQUEST_METHOD" => $METHOD,
            "REMOTE_ADDR" => "",
            "HTTPS" => "",
        );
        
        //Params
        $client_signer = new Client(new Signer());
        $params = $client_signer->getSignedRequestParams($KEY, $SECRET, $METHOD, $URL, array("name" => "Foo"));

        $request = new Request($senv, $params);
        $router = new Router($request);

        $router->post("hello/*", function($greeting) use ($request) {
            return array("message" => $greeting . " " . $request->getParam('name'));
        });

        $router->auth($KEY, $SECRET)->url("hello");

        $this->expectOutputString(json_encode(array("message" => "Hi Foo")));

        $router->run();
    }
}