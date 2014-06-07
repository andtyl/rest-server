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

        $this->assertEquals("GET", $request->getRequestMethod());
        $this->assertEquals("/hello", $request->getUrl());
        $this->assertEquals("bar", $request->getParam("foo"));
        $this->assertEquals(array("foo" => "bar"), $request->getParam());
        $this->assertEquals("BAR", $request->getHeader("X-FOO"));
        $this->assertEquals("IE", $request->getUserAgent());
        $this->assertEquals(true, $request->isSecure());
        $this->assertEquals("8.8.8.8", $request->getIp());
    }

}