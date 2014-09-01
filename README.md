# Maverick - PHP Framework
Maverick is a general purpose PHP framework, it is meant to be as light-weight as possible while providing a firm foundation for development.

## Starting Your Application
An application written with Maverick starts out like this:

```php
<?php

require './vendor/autoload.php';

define('ROOT', __DIR__ . '/');

$app = new Maverick\Application();
$app->start();
```

This will create the application and all necessary services. The default services include `request`, `session`, `router`, `response` and `exception.controller`. The names should be pretty self explanatory. The nice part about the service manager built into Maverick is that all services can be overwritten after they are registered. The only exception is that you cannot override a service after it has been instantiated.

### Adding Routes
To begin adding routes to your application, you may define one by using the following code (it assumes the previous code is included):

```php
$app->router->get('/hello/{#([a-z]+)#i}', function($name) {
    return 'Hello ' $name . '!';
});
```

This route will be matched when using the HTTP method `GET`, `http` or `https` and when the URL is something like: `/hello/world` or `/hello/maverick`. The portion of the route between the curly braces is a standard regular expression which will only match uppercase and lowercase letters.

It is important to note that you do not have to flag a route as dynamic, simply surround a regular expression within curly braces and the router will figure it out.

For more information regarding regular expressions, I suggest looking at this [cheat sheet](http://www.cheatography.com/davechild/cheat-sheets/regular-expressions/).

If you need to be more specific about this route, you can specify restrictions such as the HTTP communication standard (HTTP or HTTPS) like so:

```php
$app->router->get('/hello/{#([a-z]+)#i}', function($name) {
    return 'Hello ' $name . '!';
}, ['https' => true]);
```

Unlike the previous route, this route will only match when using `https`.

To be continued...
