<?php

use RestServer\Request;

class RequestTest extends PHPUnit_Framework_TestCase
{
    public function testRequest()
    {
        $senv = array(
            "HTTP_USER_AGENT" => "IE",
            "REQUEST_URI" => "/sub-foo/server/hello?foo=bar",
            "SCRIPT_NAME" => "/sub-foo/server/index.php",
            "REQUEST_METHOD" => "GET",
            "REMOTE_ADDR" => "8.8.8.8",
            "HTTPS" => "1",
        );
        $params = array("foo" => "bar");
        $headers = array("X-FOO" => "BAR");

        //Mock Request
        $request = new Request($senv, $params, $headers);

        $this->assertEquals($request->getRequestMethod(), "GET");
        $this->assertEquals($request->getUrl(), "/hello");
        $this->assertEquals($request->getParam("foo"), "bar");
        $this->assertEquals($request->getParam(), array("foo" => "bar"));
        $this->assertEquals($request->getHeader("X-FOO"), "BAR");
        $this->assertEquals($request->getUserAgent(), "IE");
        $this->assertEquals($request->isSecure(), true);
        $this->assertEquals($request->getIp(), "8.8.8.8");
    }

}