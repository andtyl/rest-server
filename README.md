# Rest API Server

**A simple but yet powerful Rest API Server.**

## Features

* Route HTTP Method + URL's to your callable functions
* Authentication by signed requests. Eg. by using [andtyl/signature](https://github.com/andtyl/signature)

## Basics

Map URL method + URL to your Controller methods or functions using the methods of the Router class `get`, `post`, `put`, `delete`.

Your functions may return a string, array or object. It is automatically encoded to JSON. 

### Example (GET /hello)

```php
$request = new RestServer\Request();
$router = new RestServer\Router($request);
$router->get("hello", function() {
    return array("message" => "hello world");
});
$router->run();
```

Will output

```json
{"message":"hello world"}
```

## URL Parameters

It is possible to specify URL parameters that is passed to your callable.

### Example with URL parameters (POST /hello)

```php
class Foo
{
    function hello($firstname, $lastname)
    {
        return array("Hello $firstname $lastname");
    }
}

$foo = new Foo();

$router = new RestServer\Router();
$router->post("hello/*/*", array($foo, "hello"));
$router->run();
```

## Authentication

It is possible to require authentication. Then every request must be signed. A signed request includes the parameters `auth_key`, `auth_timestamp` and `auth_signature` The signature is a hmac hash of the request data using a `secret`. See `client.php` example file for more defails.

Every auth token (`key` + `secret`) could be set to be allowed access only to specific URL:s.

### Example with authentication

```php
...
//Key: 123 Secret: 456
$router->auth("123", "456")->url("/hello")->url("/foo");
```

**Note:** Allowing URL `/hello` also gives access to all sub-paths like `/hello/foo`

## Examples

See examples folder for more server and client examples.

## Credits

Inspiration from:
* [Respect\Rest](https://github.com/Respect/Rest)
* [Slim Framework](https://github.com/codeguy/Slim)