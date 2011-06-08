<?php
/**
 * Breeze Framework - Global scope shortcuts
 *
 * This file contains code that creates functions in the global scope that delegate
 * to the {@link Breeze\Application} class facade which facilitates the micro-framework
 * feel while maintaining a more modular, extensible design.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @package    Breeze
 * @subpackage Shortcuts
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */

/**
 * @see Breeze\Application
 */
require_once 'Breeze/Application.php';

/**
 * @see Breeze\Application
 */
use Breeze\Application;

/**
 * Defines a new filter.
 *
 * Closure `$filter` will be invoked after routing takes place.
 *
 * NOTE: If you call `after()` multiple times, each callback will be invoked
 * in the order it was defined.
 *
 * @code
 *     after(function(){
 *         layout('index');
 *     });
 * @endcode
 *
 * @param Closure $filter The filter to add.
 *
 * @return void
 */
function after()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'after'), func_get_args()
    );
}

/**
 * Defines a new route.
 *
 * Closure `$handler` will be invoked if a request is made for a URI where the
 * request method is defined in `$methods` and the URI matches `$pattern`.
 *
 * @code
 *     any(array('GET', 'POST'), '/route', function(){
 *         display('template');
 *     });
 * @endcode
 *
 * This function can also be used without the `$methods` parameter in which
 * case Closure `$handler` will be invoked if any request matches `$pattern`.
 *
 * @code
 *     any('/route', function(){
 *         display('template');
 *     });
 * @endcode
 *
 * @param array   $methods The HTTP method that must match
 * @param string  $pattern The pattern the requested URI must match
 * @param Closure $handler The handler for the matching request
 *
 * @return void
 * @throws InvalidArgumentException
 */
function any()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'any'), func_get_args()
    );
}

/**
 * Defines a new filter.
 *
 * Closure `$filter` will be invoked before routing takes place.
 *
 * NOTE: If you call `before()` multiple times, each callback will be invoked
 * in the order it was defined.
 *
 * @code
 *     before(function(){
 *         layout('index');
 *     });
 * @endcode
 *
 * @param Closure $filter The filter to add.
 *
 * @return void
 */
function before()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'before'), func_get_args()
    );
}

/**
 * Defines a new route filter.
 *
 * Closure `$filter` will be invoked on each route pattern before it's
 * evaluated against the current request.
 *
 * NOTE: If you call `route()` multiple times, each callback will be invoked
 * in the order it was defined.
 *
 * @code
 *     route(function(&$pattern){
 *         $pattern = '/subdir' . $pattern;
 *     });
 * @endcode
 *
 * @param Closure $filter The filter to add.
 *
 * @return void
 */
function route()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'route'), func_get_args()
    );
}

/**
 * Defines or dispatches a condition.
 *
 * @code
 *     // Define a new condition
 *     condition('user_is_jeff', function($user){
 *         return $user-getUserName() == 'jeff';
 *     });
 *
 *     get('/', function(){
 *         // Calls the condition
 *         condition('user_is_jeff', $user);
 *         display('jeff');
 *     });
 * @endcode
 *
 * @param string $name    The name of the condition to set/dispatch
 * @param mixed  $handler The condition handler
 *
 * @return void
 */
function condition()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'condition'), func_get_args()
    );
}

/**
 * Sets or gets a configuration value.
 *
 * @code;
 *     // Sets a configuration value
 *     config('jeff', 'is cool');
 *
 *     // Gets a configuration value
 *     echo config('jeff'); // Prints "is cool"
 * @endcode
 *
 * @param string $name  The name of the config value to set/get
 * @param mixed  $value The config value to set
 *
 * @return mixed
 */
function config()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'config'), func_get_args()
    );
}

/**
 * Defines a new DELETE route.
 *
 * Closure `$handler` will be invoked if a DELETE request is made for a URI
 * that matches `$pattern`.
 *
 * @code
 *     delete('/route', function(){
 *         display('template');
 *     });
 * @endcode
 *
 * @param string  $pattern The pattern the requested URI must match
 * @param Closure $handler The handler for the matching request
 *
 * @return void
 * @throws InvalidArgumentException
 */
function delete()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'delete'), func_get_args()
    );
}

/**
 * Displays the contents of the template specified by `$template` using the
 * variables specified by `$variables`.
 *
 * @code
 *     get('/', function(){
 *         display('index');
 *     });
 *
 *     get('/hello', function(){
 *         display('hello', array('name'='Jeff'));
 *     });
 * @endcode
 *
 * @param string $template  The path to the template, excluding the base
 * templates directory and extension.
 * @param array  $variables The variables to add to the template.
 *
 * @return void
 */
function display()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'display'), func_get_args()
    );
}

/**
 * Defines or dispatches an error.
 *
 * @code
 *     // Defining error handlers
 *     error(function(){ echo "default handler"; });
 *     error(403, function(){ echo 'permission denied'; });
 *     error('JeffsException', function(){
 *         echo "Jeff's Exception";
 *     });
 *
 *     // Dispatching errors
 *     error(403)
 *     error(403, "Permission Denied");
 *     error("Permission Denied");
 * @endcode
 *
 * @param mixed $var1 A numeric code, a message, or a closure
 * @param mixed $var2 A message, or a closure
 *
 * @return void
 */
function error()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'error'), func_get_args()
    );
}

/**
 * Returns the contents of the template specified by `$template` using the
 * variables specified by `$variables`.
 *
 * @code
 *     get('/', function(){
 *         echo fetch('hello');
 *     });
 *
 *     get('/hello', function(){
 *         echo fetch('hello', array('name'='Jeff'));
 *     });
 * @endcode
 *
 * @param string $template  The path to the template, excluding the base
 * templates directory and extension.
 * @param array  $variables The variables to add to the template.
 *
 * @return string
 */
function fetch()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'fetch'), func_get_args()
    );
}

/**
 * Defines a new GET route.
 *
 * Closure `$handler` will be invoked if a GET request is made for a URI that
 * matches `$pattern`.
 *
 * @code
 *     get('/route', function(){
 *         display('template');
 *     });
 * @endcode
 *
 * @param string  $pattern The pattern the requested URI must match
 * @param Closure $handler The handler for the matching request
 *
 * @return void
 * @throws InvalidArgumentException
 */
function get()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'get'), func_get_args()
    );
}

/**
 * Defines a new helper.
 *
 * Closure `$helper` will be declared as a standard PHP function identified by
 * `$name`.
 *
 * @code
 *     helper('add_numbers', function($num1, $num2){
 *         return $num1 + $num2;
 *     });
 *
 *     echo add_numbers(5, 10); // 15
 * @endcode
 *
 * @param string  $name   The name of the helper to add.
 * @param Closure $helper The helper to add.
 *
 * @return void
 */
function helper($name, $extension)
{
    call_user_func_array(
        array(Application::getInstance('breeze'), 'helper'), func_get_args()
    );

    eval(
        "function $name()
         {
             return call_user_func_array(
                 array(Breeze\Application::getInstance('breeze'), '$name'),
                 func_get_args()
             );
         }"
    );
}

/**
 * Returns the contents of the template specified by `$template` using the
 * variables specified in `$variables`.
 *
 * NOTE: This works just like {@link fetch()} but it ignores the layout
 * preferences.
 *
 * @code
 *     get('/', function(){
 *         echo fetch('hello');
 *     });
 *
 *     get('/hello', function(){
 *         echo fetch('hello', array('name'='Jeff'));
 *     });
 * @endcode
 *
 * @param string $template  The path to the template, excluding the base
 * templates directory and extension.
 * @param array  $variables The variables to add to the template.
 *
 * @return string
 */
function partial()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'partial'), func_get_args()
    );
}

/**
 * Exits out of the current route and continues routing.
 *
 * @code
 *     get(';/posts.*;', function(){
 *         layout('posts');
 *         pass();
 *     });
 *
 *     get('/posts', function(){
 *         display('list_posts');
 *     });
 *
 *     get(';/posts/(\d+);', function(){
 *         display('view_post');
 *     });
 * @endcode
 *
 * @return void
 */
function pass()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'pass'), func_get_args()
    );
}

/**
 * Defines a new POST route.
 *
 * Closure `$handler` will be invoked if a POST request is made for a URI that
 * matches `$pattern`.
 *
 * @code
 *     post('/route', function(){
 *         display('template');
 *     });
 * @endcode
 *
 * @param string  $pattern The pattern the requested URI must match
 * @param Closure $handler The handler for the matching request
 *
 * @return void
 * @throws InvalidArgumentException
 */
function post()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'post'), func_get_args()
    );
}

/**
 * Defines a new PUT route.
 *
 * Closure `$handler` will be invoked if a PUT request is made for a URI that
 * matches `$pattern`.
 *
 * @code
 *     put('/route', function(){
 *         display('template');
 *     });
 * @endcode
 *
 * @param string  $pattern The pattern the requested URI must match
 * @param Closure $handler The handler for the matching request
 *
 * @return void
 * @throws InvalidArgumentException
 */
function put()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'put'), func_get_args()
    );
}

/**
 * Redirects the end-user to a new url.
 *
 * @code
 *     redirect('http://www.breezephp.com/');
 *     redirect('http://www.breezephp.com/', 301); // Issues a 301 status header
 * @endcode
 *
 * @param string  $url  The url to redirect to.
 * @param integer $code The status code for the redirect.
 *
 * @return void
 */
function redirect()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'redirect'), func_get_args()
    );
}

/**
 * Starts the routing process.
 *
 * NOTE: You may coerce the request method and request URI by providing the
 * `$requestMethod` and `$requestUri` arguments respectively.  This is useful
 * for testing.
 *
 * @code
 *     // Default
 *     run()
 *
 *     // Coerce request
 *     run('GET', '/posts/2');
 * @endcode
 *
 * @param string $requestMethod An optional request method to spoof the
 * incoming request method.
 * @param string $requestUri    An optional URI to spoof the incoming
 * request URI.
 *
 * @return void
 */
function run()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'run'), func_get_args()
    );
}

/**
 * Sets or gets an HTTP status.
 *
 * @code
 *     // header('HTTP/1.1 404 Not Found');
 *     status(404)
 *
 *     // header('HTTP/1.0 404 Not Found');
 *     status(404, '1.0');
 *
 *     // Gets the current status header
 *     echo status(); // HTTP/1.0 404 Not Found
 * @endcode
 *
 * @param integer $statusCode  The status code for the HTTP response.
 * @param string  $httpVersion The protocol version for the HTTP response.
 *
 * @return string
 */
function status()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'status'), func_get_args()
    );
}

/**
 * Sets or gets a template variable.
 *
 * @code
 *     // Sets a template variable
 *     template('jeff', 'is cool');
 *
 *     // Gets a template variable
 *     echo template('jeff'); // Prints "is cool"
 * @endcode
 *
 * @param string $name  The name of the template variable to set/get
 * @param mixed  $value The value of the template variable
 *
 * @return mixed
 */
function template()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'template'), func_get_args()
    );
}

/**
 * Sets the layout to use.
 *
 * @code
 *     // Path resolves to views/path/to/layout.php
 *     layout('path/to/layout');
 * @endcode
 *
 * @param string $path The path to the layout file
 *
 * @return void
 */
function layout()
{
    return call_user_func_array(
        array(Application::getInstance('breeze'), 'layout'), func_get_args()
    );
}

// Moves all user-defined helpers to the global scope
foreach (Application::getInstance('breeze')->getUserHelpers() as $helper) {
    eval(
        "function $helper()
         {
             return call_user_func_array(
                 array(Breeze\Application::getInstance('breeze'), '$helper'),
                 func_get_args()
             );
         }"
    );
}
