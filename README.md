# Rest API Server

**A simple but yet powerful Rest API Server.**

## Features

* Route HTTP Method + URL's to your callable functions
* Authentication by signed requests. Eg. by using [andtyl/signature](https://github.com/andtyl/signature)

# Example (GET /hello)

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

# URL Parameters

It is possible to specify URL parameters that is passed to your callable.

# Example with URL parameters (POST /hello/*/*)

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

# Example with authentication

It is possible to require authentication. Then every request must be signed. A signed request includes the parameters `auth_key`, `auth_timestamp` and `auth_signature` The signature is a hmac hash of the request data using a `secret`. See client.php example file for more info.

Every auth token (key + secret) could be set to allow access only to specific URL:s.

```php
...
$router->auth("123", "456")->url("/hello")->url("/foo");
```

**Note:** Allowing URL `/hello` also gives access to all sub-paths like `/hello/foo`

See examples folder for more server and client examples.

# Credits

Inspiration from:
* [Respect\Rest](https://github.com/Respect/Rest)
* [Slim Framework](https://github.com/codeguy/Slim)