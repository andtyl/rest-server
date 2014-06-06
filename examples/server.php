<?php

include "../vendor/autoload.php";

class Controller
{
    protected $request;

    public function __construct(RestServer\Request $request)
    {
        $this->request = $request;
    }

    public function hello($name)
    {
        return array("message" => "Hello $name!");
    }

    public function helloPost()
    {
        return array("message" => "Hello " . $this->request->getParam("name", ""));
    }  
}

$request = new RestServer\Request();

$router = new RestServer\Router($request);

$c = new Controller($request);

$router->auth("123", "456")->url("hello");

$router->post("hello", array($c, "helloPost"));
$router->get("hello/*", array($c, "hello"));

$router->run();
