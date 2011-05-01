Function Reference
==================

after()
-------

### - void after( callback $function )

__Synopsis:__

Defines a new filter.

Callback `$function` will be called after routing takes place.

__Note:__

If you call `after()` multiple times, each callback will be called in the order it was defined.

__Example:__


``` php
<?php

after(function(){
// Put your shutdown routines here.
});
```

any()
-----

### - void any( string $route, callback $function )

__Synopsis:__

Defines a new route.

Callback `$function` will be called if any request is made for a URI that matches `$route`.

__Example:__

``` php
<?php

any('/', function(){
display('index');
});
```

### - void any( array $methods, string $route, callback $function )

__Synopsis:__

Defines a new route.

Callback `$function` will be called if a request is made where the request method is defined in `$methods` and the URI matches `$route`

__Example:__

``` php
<?php

any(array('GET','PUT'), '/', function(){
    display('index');
});
```

before()
--------

### - void before( callback $function )

__Synopsis:__

Defines a new filter.

Callback `$function` will be called before routing takes place.

__Note:__

If you call `before()` multiple times, each callback will be called in the order it was defined.

__Example:__

``` php
<?php

before(function(){
    layout('index');
});
```

condition()
-----------

### - void condition( string $name [, mixed $... ] )

__Synopsis:__

Dispatches a condition.

Condition specified by `$name` will be called.  If the condition returns false, `pass()` is called which skips the current route and continues routing.

__Note:__

Additional arguments are sent to the callback function specified by `$name`.

__Example:__

``` php
<?php

get('/', function(){
    condition('user_is_jeff');
    display('jeff');
});

get('/', function(){
    condition('user_agent_matches', '/iphone/i');
    display('iphone');
});
```

### - void condition( string $name, callback $function )

__Synopsis:__

Defines a condition.

The callback `$function` can be dispatched by `$name` after this function has been called.  Remember that the callback should return a boolean which indicates if the condition was met or not.

__Example:__

``` php
<?php

condition('user_is_jeff', function($user){
    return $user-getUserName() == 'jeff';
});

get('/', function(){
    condition('user_is_jeff', $user);
    display('jeff');
});
```

config()
--------

### - mixed config( string $name )

__Synopsis:__

Gets the configuration value identified by `$name`.

__Example:__

``` php
<?php

$template_engine = config('template_engine');
```

### - void config( string $name, mixed $value )

__Synopsis:__

Sets a configuration value.

__Example:__

``` php
<?php

config('template_engine', 'Smarty');
```

### - void config( array $values )

__Synopsis:__

Sets multiple configuration values in a single statement.

__Example:__

``` php
<?php

config(array(
    'template_engine' ='Smarty',
    'template_extension' ='.tpl'
));
```

delete()
--------

### - void delete( string $route, callback $function )

__Synopsis:__

Defines a new route.

Callback `$function` will be called if a DELETE request is made for a URI that matches `$route`.

__Example:__

``` php
<?php

delete('/route', function(){
    display('template');
});
```

display()
---------

### - void display( string $path [, array $variables] )

__Synopsis:__

Renders a template at `$path` and displays it to the end-user.

__Example:__

``` php
<?php

get('/', function(){
    display('index');
});

get('/hello', function(){
    display('hello', array('name'='Jeff'));
});
```

error()
-------

### - void error( string $message )

__Synopsis:__

Dispatches a generic error.

__Example:__

``` php
<?php

get('/', function(){
    if (!$user-logged_in) {
        error('You must be logged in to view this page.');
    }
});
```

### - void error( integer $error_code [, string $message = 'An Error Occurred.'] )

__Synopsis:__

Dispatches an HTTP error.

If the `$error_code` corresponds to a standard HTTP error, an HTTP status header will be dispatched with the appropriate code.

__Note:__

If a standard HTTP `$error_code` is provided with no message, the corresponding error message will be used.

__See:__

[List of HTTP status codes](http://en.wikipedia.org/wiki/List_of_HTTP_status_codes).

__Example:__

``` php
<?php

get('/', function(){
    if (!$user-logged_in) {
        error(403); // 403 Forbidden
    }
});

get('/', function(){
    if (!$user-logged_in) {
        error(403, 'Forbidden'); // 403 Forbidden
    }
});
```

### - void error( callback $function )

__Synopsis:__

Adds a default error handler.

Callback `$function` gets called for errors that have no defined handler.

__Example:__

``` php
<?php

error(function(){
    display('default_error');
});
```

### - void error( mixed $error, callback $function )

__Synopsis:__

Adds an error handler.

Callback `$function` gets called for errors that are associated with the provided `$error`.

* If `$error` is an integer, the callback is associated with the corresponding error code.
* If `$error` is a string, the callback is associated with the corresponding exception.
* If `$error` is an array, the callback is associated with all exceptions and codes defined in the array.

__Example:__

``` php
<?php

error(500, function(){
    display('server_error');
});

error('ForbiddenException', function(){
    display('forbidden');
});

error(array(403,404), function(){
    display('http_error');
});
```


fetch()
---------

### - string fetch( string $path [, array $variables] )

__Synopsis:__

Renders a template at `$path` and returns it.

__Example:__

``` php
<?php

$contents = fetch('path/to/template');
$contents = fetch('path/to/template', array('name'='Jeff'));
```

get()
-----

### - void get( string $route, callback $function )

__Synopsis:__

Defines a new route.

Callback `$function` will be called if a GET request is made for a URI that matches `$route`.

__Example:__

``` php
<?php

get('/route', function(){
    display('template');
});
```

helper()
--------

### - void helper( string $name, callback $function )

__Synopsis:__

Defines a new helper.

Callback `$function` will be declared as a standard PHP function identified by `$name`.

__Example:__

``` php
<?php

helper('add_numbers', function($num1, $num2){
    return $num1 + $num2;
});

echo add_numbers(5, 10); // 15
```

layout()
--------

### - void layout( string $path )

__Synopsis:__

Sets the layout.

__Example:__

``` php
<?php

layout('path/to/layout');
```

partial()
---------

### - string partial( string $path [, array $variables] )

__Synopsis:__

Renders a template at `$path` without a layout and returns it.

__Example:__

``` php
<?php

$contents = partial('path/to/template');
$contents = partial('path/to/template', array('name'='Jeff'));
```

pass()
------

### - void pass( void )

__Synopsis:__

Exits out of the current route and continues routing.

__Example:__

``` php
<?php

get(';/posts.*;', function(){
    layout('posts');
    pass();
});

get('/posts', function(){
    display('list_posts');
});

get(';/posts/(\d+);', function(){
    display('view_post');
});
```

post()
-----

### - void post( string $route, callback $function )

__Synopsis:__

Defines a new route.

Callback `$function` will be called if a POST request is made for a URI that matches `$route`.

__Example:__

``` php
<?php

post('/route', function(){
    display('template');
});
```

put()
-----

### - void put( string $route, callback $function )

__Synopsis:__

Defines a new route.

Callback `$function` will be called if a PUT request is made for a URI that matches `$route`.

__Example:__

``` php
<?php

put('/route', function(){
    display('template');
});
```

redirect()
----------

### - void redirect( string $url [, integer $status_code = 302 [, boolean $exit = true]] )

__Synopsis:__

Redirects the end-user to a new url.

__Note:__

If `$exit` is set to false, the application will not terminate after the location header is sent.  This is only appropriate for testing.

__Example:__

``` php
<?php

redirect('http://www.breezephp.com/');
redirect('http://www.breezephp.com/', 301); // Issues a 301 status header
```

register()
----------

### - void Breeze\Application::register( string $name, callback $function )

__Synopsis:__

Registers a new plugin.

Callback `$function` will be called each time `Breeze\Application` is instantiated.

__Example:__

``` php
<?php

namespace Breeze\Plugins\Auth {
    use Breeze\Application;
    require_once 'Breeze/Application.php';

    Application::register('Auth', function($app){
        $app-helper('auth', function($user, $redirect_url) use ($app){
            if($user-name != 'admin') {
                $app-redirect($redirect_url);
            }
        });
    });
}
```

run()
-----

### - void run( [string $request_method [, string $uri]] )

__Synopsis:__

Starts the routing process.

__Note:__

You may coerce the request method and request URI by providing the `$request_method` and `$uri` arguments respectively.  This is useful for testing.

__Example:__

``` php
<?php

run('GET', '/posts/2');
```

status()
-----

### - string status( void )

__Synopsis:__

Gets the current HTTP status.

__Example:__

``` php
<?php

status() // HTTP/1.1 200 OK
```

### - string status( integer $statusCode [, string $httpVersion] )

__Synopsis:__

Sets the current HTTP status.

__Example:__

``` php
<?php

status(204); // header('HTTP/1.1 204 Not Content');
status(204, '1.0'); // header('HTTP/1.0 204 Not Content');
```

template()
----------

### - mixed template( string $name )

__Synopsis:__

Gets the template value identified by `$name`.

__Example:__

``` php
<?php

$title = template('page_title');
```

### - void template( string $name, mixed $value )

__Synopsis:__

Sets a template value.

__Example:__

``` php
<?php

template('page_title', 'Welcome to my website');
```

### - void template( array $values )

__Synopsis:__

Sets multiple template values in a single statement.

__Example:__

``` php
<?php

template(array(
    'page_title' ='Welcome to my website',
    'user_name' ='Jeff'
));
