<?php

use RestServer\Token;

class TokenTest extends PHPUnit_Framework_TestCase
{
    public function testTokenUrl()
    {
        $token = new Token("", "");

        $token->url("foo");
        $this->assertTrue($token->isValidUrl("foo"));
        $this->assertTrue($token->isValidUrl("foo/"));
        $this->assertTrue($token->isValidUrl("foo/bar"));
        $this->assertFalse($token->isValidUrl("fooo"));
        $this->assertFalse($token->isValidUrl("fo"));
        $this->assertFalse($token->isValidUrl("bar"));
    }

}