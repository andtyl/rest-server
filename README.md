# Rest API Server

**A simple but yet powerful Rest API Server.**

## Features

* Route HTTP Method + URL's to your callable functions
* All request MUST be authenticated. Eg. by using [andtyl\signature](https://github.com/andtyl/signature)

# Example

```php
$request = new RestServer\Request();
$router = new RestServer\Router($request);
$router->get("hello", array($c, function() {
    return array("message" => "Hello world!"); //Will be encoded to JSON
}));
$router->auth("123", "456")->url("hello");
$router->run();
```

Se examples folder for  more server and client examples.

# TODO

* Manual
* Handle Exceptions in the callables (send 500 Internal Server Error?)

# Credits

Inspiration from:
* [Respect\Rest](https://github.com/Respect/Rest)
* [Slim Framework](https://github.com/codeguy/Slim)