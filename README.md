Breeze
======

Breeze is a simple and intuitive micro-framework for PHP 5.3+.  The most basic example of a Breeze application is:

``` php
<?php

require_once 'Breeze.php';

get('/', function(){
    echo 'Hello World!';
});

run();
```

Getting Started
---------------

For help installing and configuring the Breeze framework, see the INSTALL.md file.

### Basic layout

All the instructions in this tutorial will assume that you already have a basic application running.  This means that most of the examples will leave off the following required elements.

``` php
<?php

// plugins go here

require_once 'Breeze.php';

// example code goes here

run();
```

The `require_once` statement is needed to include the framework libraries and `run()` is needed to start the routing process.  Without these elements, most examples will not run.

Routes
------

The routing API involves associating HTTP requests with PHP functions.  For example, the following code would print `hello world` if a GET request were made for `/`.

``` php
<?php

get('/', function(){
    echo 'Hello World!';
});
```

The routing system works with the 4 main HTTP request methods: GET, POST, PUT, DELETE.

``` php
<?php

get('/', function(){
    echo 'you made a GET request';
});

post('/', function(){
    echo 'you made a POST request';
});

put('/', function(){
    echo 'you made a PUT request';
});

delete('/', function(){
    echo 'you made a DELETE request';
});
```

Routes are matched in the order they are declared and once a route is matched no other routes will be matched.  This means you have to be careful when organizing your routes.  For example:

``` php
<?php

get('/', function(){
    echo 'Made it here.';
});

get('/', function(){
    echo 'This will never get echoed.';
});
```

### Pattern matching

Routes can be matched using a basic string or a regular expression.  The one caveat is that regular expressions can not be delimited by `/` as they are indistinguishable from basic strings.  For example, `/my/path/is` could be a basic string or a regular expression.  The Breeze convention is to use the semicolon `;` to delimit regular expressions, but any valid delimiter will due.

``` php
<?php

get('/basic/string/match', function(){
    echo "welcome to http://www.mysite.com/basic/string/match";
});

get(';^/basic/string/\w+$;', function(){
    echo "Wow, regexp matching!";
});
```

Of course, matching regular expressions isn't too useful unless you can capture groups.  Accordingly, Breeze passes any matches from your route as the second argument to your function.

``` php
<?php

# numeric matches
get(';^/hello/(\w+)$;', function($app, $params){
    echo 'Hello ' . $params[1];
});

# named matches
get(';^/hello/(?<name>\w+)$;', function($app, $params){
    echo 'Hello ' . $params['name'];
});
```

### Matching multiple methods

In some cases, it may be helpful to associate one PHP function with multiple request methods.  We will describe a number of uses for this after some more advanced techniques have been introduced, but a simple example is detailed below.

``` php
<?php

any(array('POST','PUT'), '/', function()){
    echo 'HTML 4 and XHTML 1 forms don\'t really support the PUT method. ' .
         'This is one way to handle it.';
});

any('/', function(){
    echo 'This matches all request methods.';
})
```

### Making PUT and DELETE work

The solution described above works, but it's certainly not the most graceful or preferred solution to this problem.  A standard exists in several other frameworks where you can use a hidden form field to indicate the request method you wish to use.  This allows you to keep your controller code concise while addressing the shortcomings of HTML in the HTML.

**Template.php:**

``` html
<form method="post" action="/process_my_form">
<input type="hidden" name="_method" value="PUT" />
...
</form>
```

**Controller.php:**

``` php
<?php

put('/process_my_form', function(){
    echo 'This will be echoed when the form is submitted.';
});
```

Pass
----

Earlier we said once a route is matched no other routes are matched.  This isn't entirely true.  A mechanism exists which allows escaping from an action to continue routing.  This is helpful in a variety of situation.  One common example is to do some pre-processing for a certain set of routes.  Consider the following code:

``` php
<?php

// Show a post
get(';^/posts/(?<id>\d+)$;', function() {
    // load a post and display it
});

// Edit a post
get(';^/posts/(?<id>\d+)/edit$;', function() {
    // load a post and display an edit form
});
put(';^/posts/(?<id>\d+)$;', function() {
    // load a post and update it
});

// Delete a post
get(';^/posts/(?<id>\d+)/delete$;', function() {
    // load a post and display a delete form
});
delete(';^/posts/(?<id>\d+)$;', function() {
    // load a post and delete it
});
```

If you've been paying attention, you probably noticed every method that starts with `/posts/$id` is loading a post.  What sense does it make to repeat this logic 5 times?  Instead, you can do the following:

``` php
<?php

any(';^/posts/(?<id>\d+);', function() {
    // load a post
    pass();
}
```

Now all methods that start with `/post/$id` will match this route and load a post.  The `pass()` function tells the framework to exit out of the current action and continue routing.  This shortcut saves us a lot of keystrokes and removes duplication and complexity from our code which is always helpful.

### redirect()

Don't use the `pass()` function when you really want to send somebody to another URL.  To do this, you can use the `redirect()` function.

``` php
<?php

get('/', function(){
    // sends a 302 response code
    redirect('http://www.mysite.com/some_other_page');
});
```

You can also specify a different response code for redirects.

``` php
<?php

get('/', function(){
    // sends a 301 response code
    redirect('http://www.mysite.com/some_other_page', 301);
});
```

Filters
-------

Sometimes you want an action to be executed no matter what.  Your first instinct might be to use the `pass()` function for this.

``` php
<?php

any(';.*;', function(){
    echo 'This should always be executed';
    pass();
});
```

While this works, it's not ideal.  For one, using a route means you must be cognizant of ordering.  This route will only execute if it's included at the very top of your script.  Also, what happens if you want something to execute after every action?  Putting a route like this at the bottom of a script won't work as expected.  This is where filters come in.  Filters allow us to define functions that always get executed.

``` php
<?php

before(function(){
    echo 'This will be executed first';
});

before(function(){
    echo 'This will be executed second';
});

after(function(){
    echo 'This will be executed next to last';
});

after(function(){
    echo 'This will be executed last';
});
```

Filters are always executed in the order they are defined but you can define them anytime before you call the `run()` function.  This means that the following works as expected.

``` php
<?php

get('/', function(){
    echo " World";
});

before(function(){
    echo "Hello";
});
```

Conditions
----------

Another common use of the `pass()` function is to filter through routes if they don't meet a certain condition.  Imagine you want to do something special on your homepage for iphone users.  One way to do this is:

``` php
<?php

get('/', function(){
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'iphone') === FALSE) {
        pass();
    }
    // do something special for iphones
});

get('/', function(){
    // everyone else will see this
});
```

This works but it's a little bulky.  The conditions API provides a cleaner interface for replicating this functionality.

``` php
<?php

condition('user_agent_contains', function($agent){
    return stristr($_SERVER['HTTP_USER_AGENT'], $agent) !== false;
});

get('/', function(){
    condition('user_agent_contains', 'iphone');
    // do something special for iphones
});

get('/', function(){
    // everyone else will see this
});
```

And since checking a user agent against a known pattern is such a common task, we've predefined a condition in the framework that does just that.  It's called `user_agent_matches`.

``` php
<?php

get('/', function(){
    condition('user_agent_matches', '/iphone/i');
    // do something special for iphones
});

get('/', function(){
    // everyone else will see this
});
```

Another predefined condition is called `host_name_is`.  You may use it in the following way:

``` php
<?php

get('/', function(){
    condition('host_name_is', 'www.mysite.com');
    // do something special for www.mysite.com
});

get('/', function(){
    // everyone else will see this
});
```

Templates
---------

Eventually you'll need a clean way to delineate your views from your controllers.  To address this need, Breeze provides a very simple engine for processing templates.

``` php
<?php

get('/', function(){
    display('index');
});
```

**index.php**:

``` html
<h1>Hi from the template</h1>
```

### Variables

You'll find that you also need a way to pass variables from your controllers to your templates.  For this, Breeze has a `template()` function that can be used to assign and read template variables.

``` php
<?php

get('/jeff', function(){
    template('message', 'Jeff is cool');
    echo template('message'); // prints 'Jeff is cool'

    display('index');
});
```

**index.php**:

``` html
<h1><?php echo $message; ?></h1>
```

You can also make multiple assignments with a single `template()` call.

``` php
<?php

get('/jeff', function(){
    template(array('title'=>'This is a title', 'message'=>'This is a message'));
    display('index');
});
```

**index.php**:

``` html
<h1><?php echo $title; ?></h1>
<p><?php echo $message; ?></p>
```

For your convenience, Breeze allows you to display a template and assign the template variables in a single statement.

``` php
<?php

get('/breeze', function(){
    display('index', array('message'=>'Breeze is very cool'));
});
```

**index.php**:

``` html
<h1><?php echo $message; ?></h1>
```

Breeze actually has one more way to define template variables.  This method exists for the plugin architecture which we'll discuss in more detail later, but it can be helpful when working with multiple template variables at once.

``` php
<?php

get('/special', function($app){
    $app->message = 'This is another way of defining template variables. ' .
                    'This mostly exists for working with the plugin architecture ' .
                    'but can be useful when working with actions that declare a ' .
                    'large number of template variables.';
    display('index');
});
```

**index.php**:

``` html
<h1><?php echo $message; ?></h1>
```

And of course, though it's not encouraged, you can use these three different methods interchangeably.

``` php
<?php

get('/mixed', function($app){
    template('message', 'Jeff is cool');
    $app->message = 'Now the message has changed';

    display('index', array('message'=>'Now the message has changed again'));
});
```

**index.php**:

``` html
<h1><?php echo $message; ?></h1>
```

### Layouts

Most websites have a common header and footer.  Breeze introduces what is known as a "layout file" which allows for defining a header and footer in a single file.  The default location for this is a file called `layout` in the root of your views directory.  If this file doesn't exist, Breeze will simply render your pages without a layout.

``` php
<?php

get('/', function(){
    display('index');
});
```

**layout.php:**

``` html
<html>
    <head><title>Layout Example</title></head>
    <body>
        <?php echo $layout_contents; ?>
    </body>
</html>
```

**index.php:**

``` html
<h1>Hello from the index</h1>
```

As applications get larger they necessitate more than one layout.  To change the layout from within an action, you can use the `layout()` function.

``` php
<?php

get('/', function(){
    layout('secondary_layout');
});
```

Having to call the `layout()` function in every action isn't very concise.  A possible improvement is to use the `before()` filter or the `pass()` shortcuts we discussed earlier.

``` php
<?php

// All urls that start with /posts/ will use the posts/layout file
any(';^/posts/;', function()) {
    layout('posts/layout');
    pass();
});
```

### fetch() and partial()

Sometimes you don't want to display a template immediately.  To render the contents of a template without displaying it, you can use one of the following functions.

``` php
<?php

get('/', function(){
    template('title', 'This is a title')

    $with_layout    = fetch('index', array('message'=>'This returns the template with the layout'));
    $without_layout = partial('index', array('message'=>'This returns the template without the layout'));
});
```

**index.php:**

``` html
<h1><?php echo $title; ?></h1>
<p><?php echo $message; ?></p>
```

Since `partial()` returns templates without the layout, it is ideal for using inside templates.

**index.php:**

``` html
<h1>My Posts</h1>
<div id="posts">
<?
    foreach($posts as $post):
        echo partial('show_post', array('post'=>$post));
    endforeach;
?>
</div>
```

**show_post.php:**

``` html
<h2><?php echo $post['title']; ?></h2>
<p><?php echo $post['body']; ?></p>
<hr />
```

### Using other template engines

With Breeze, you are free to use any template engine you choose.  The default distribution of Breeze includes plugins for the Smarty and Dwoo template engines.  Creating your own plugins is relatively trivial and described in the plugins section.

To use an alternate template engine, simply require the plugin file before requiring the `Breeze.php` file.


**controller.php:**

``` php
<?php

require_once 'Breeze/plugins/Smarty.php';
require_once 'Breeze.php';

get('/', function(){
    template('message', 'Hello from Smarty!');
    display('index');
});
```

**index.php:**

``` html
<h1>{$message}</h1>
```

### The application variable

One downside of some 3rd-party template engines is they have their own way of defining functions.  This means that some of our functions aren't available.  Consider the following example:

**index.php:**

``` html
<h1>Hello</h1>
<?
    echo partial('message');
?>
```

**message.php:**

``` html
<p>World</p>
```

Try to do this same thing in Smarty and you'll get a syntax error:

**index.tpl:**

``` html
<h1>Hello</h1>
{partial('message')}
```

**message.tpl:**

``` html
<p>World</p>
```

To solve this problem, every template receives an instance of the current app.  This variable is called `$breeze` by default, but you can configure the name using methods described in the next section.  Using this information, we can fix the syntax error we received in the last example.

**index.tpl:**

``` html
<h1>Hello</h1>
{$breeze->partial('message')}
```

**message.tpl:**

``` html
<p>World</p>
```

Unfortunately, Smarty has another shortcoming.  Unlike Dwoo, Smarty has no easy way to create an array inline.  For example, the following will give you another syntax error:

**index.tpl:**

``` html
<h1>Hello</h1>
{$breeze->partial('message', array('message'=>'World'))}
```

**message.tpl:**

``` html
<p>{$message}</p>
```

To address this problem, we've created a custom plugin for the Smarty engine called `partial`.  You can use it in the following way:

**index.tpl:**

``` html
<h1>Hello</h1>
{partial file='message' message='World'}
```

**message.tpl:**

``` html
<p>{$message}</p>
```

For more information on how the `$breeze` variable works, be sure to read the plugins section.

Configurations
--------------

The Breeze template API works well out of the box, but you may find yourself wanting to change the default template location or the default template extension.  To do this, you will need to use the `config()` function.

``` php
<?php

config('template_directory', '/path/to/my/views');
config('template_extension', '.tpl');
echo config('template_extension'); // echoes '.tpl'
```

It is also possible to assign multiple configurations in a single statement.

``` php
<?php

config(array(
    'template_directory' => '/path/to/my/views',
    'template_extension' => '.tpl'
));
echo config('template_extension'); // echoes '.tpl'
```

The default configuration values are:

``` php
<?php

array(
    'template_engine'       => 'PHP',      /* The template engine */
    'template_options'      => array(),    /* The extra options for the template engine */
    'template_directory'    => '../views', /* The base directory for all templates */
    'template_extension'    => '.php',     /* The common extension for all templates */
    'template_layout'       => 'layout',   /* The default path to the layout file */
    'application_variable'  => 'breeze',   /* The template variable that holds the current application object */
    'errors_backtrace'      => true        /* If error templates should include backtraces */
);
```

Errors
------

Unfortunately, things don't always go as expected.  Eventually a resource will become unreachable, a user will provide some funky input, or a bug will be introduced into your production environment.  Whatever the case, your application needs to be equipped to handle such events.

Without configurations, Breeze will trap any uncaught exceptions that bubble up or any errors that are dispatched from within the framework.  If a layout is defined, Breeze will use it for displaying errors, otherwise it will use a minimal layout.

### Dispatching errors

The simplest way to test the default behavior of the Breeze errors framework is to dispatch some errors and see what happens.  We said earlier that the Breeze Framework will trap any uncaught exceptions that bubble up.  Let's test this out.

``` php
<?php

get('/', function(){
   throw new Exception('Oh no, what happened?');
});
```

Using the default configurations, you should see your error message (wrapped in your layout if you have one defined) and a stack backtrace to help you find out where the error occurred.  Stack backtraces can be useful when developing, but you almost certainly want to disable them in your production environment.  To do this, you can use the following code:

``` php
<?php

config('errors_backtrace', false);
```

Sometimes you want exceptions to be interpreted as specific HTTP errors.  By specifying an exception code that coincides with an HTTP error code, the appropriate header will be sent automatically.

``` php
<?php

get('/', function(){
   throw new Exception("Oops, I think I misplaced that page.", 404); // 404 header is sent
});
```

And since throwing exceptions can be a bit verbose at times, we've defined a more concise syntax for dispatching errors.

``` php
<?php

get('/', function(){
   error(404);
});

get('/another/url', function(){
   error('This is a message');
});

get('/even/another/url', function(){
   error(404, 'This is a message');
});
```

The `error()` function, when used in this way, is essentially just a shortcut for throwing a generic exception. This is however the preferred method, so we recommended you use it in your controllers unless you have a compelling reason not to.

### Handling errors

Dispatching errors is only half the story.  The default error handling, while helpful during development, is not exactly appropriate for a production environment.  You'll want to define error handlers to take the appropriate actions in your production environments.  By design, error handlers work almost identically to the routing system.

``` php
<?php

// Handle 404's
error(404, function(){
    template('title', 'Page Not Found');
    display('404');
});

// Handle all codes 400-505
error(range(400,505), function(){
    display('generic', array('title'=>'Oh no, something bad happened'));
});
```

You can also define a catchall error handler that will catch any errors that have no associated handlers.

``` php
<?php

// If no other handlers catch your error, this will
error(function(){
    display('error', array('title'=>'Oh no, something bad happened'));
});
```

Additionally, you can define error handlers based on specific exception types.

``` php
<?php

error('MyException', function(){
    display('myexception', array('title'=>'Why does this exception have such a generic name?'));
});

error(array('MyException','MyOtherException), function(){
    display('exceptions', array('title'=>'Even more exceptions, yay!'));
});
```

Just like the routing system, handlers receive and instance of the current app as their first argument.  More importantly, the second argument is an instance of the exception that was thrown that ultimately triggered the error.

``` php
<?php

error(function($app, $exception){
    echo $exception->getMessage(); // Prints 'Page Not Found'
});

get('/', function(){
    error(404, 'Page Not Found');
});
```

Status
------

One of the more useful features of Breeze errors is that they provide a nice shorthand syntax for dispatching HTTP error statuses:

``` php
<?php

error(404); // header('HTTP/1.1 404 Not Found');
```

Breeze provides a `status()` function for accomplishing the same functionality for other HTTP statuses:

``` php
<?php

status(204); // header('HTTP/1.1 204 No Content');
status(204, '1.0'); // header('HTTP/1.0 204 No Content');
```

You can also use `status()` to inspect the status message that is going to be sent for the current request:

``` php
<?php

status(204); // header('HTTP/1.1 204 No Content');
echo status(); // HTTP/1.1 204 No Content
```

Plugins
-------

Sometimes you'll find yourself repeating the same routes, configurations, conditions, templates, error handlers, etc. in most of your applications.  Breeze has a very logical solution for this.  Any code that you write using the Breeze framework can be ported to a plugin that can be used by other applications.  Additionally, Breeze offers the possibility of creating your own Breeze functions meaning you can extend the framework's core functionality until your heart's content.

The best way to learn the plugin system is through example.  For starters, you might want to take a look at the plugins that are included with the Breeze Framework.  When in doubt, use one of these plugins as a skeleton for the plugin you are trying to write.

The most basic example of a plugin would be:

**Helloworld.php:**

``` php
<?php

namespace Breeze\Plugins\HelloWorld;

use Breeze\Application;
require_once 'Breeze/Application.php';

Application::register('HelloWorld', function($app){
    $app->get('/', function(){
        echo 'Hello World';
    });
});
```

Now what's going on here?  It's actually rather simple, but it requires a little bit of insight into how the Breeze framework actually works.

### Under the hood

A bit of magic is happening behind the scenes when you call `require_once 'Breeze.php';`.  The truth is, while all of this code has seemed pretty procedural up to this point, it's really just a trick.  Every function call you are making is actually delegating the work to a system of objects that are working in the background.  `get()`, `post()`, `template()`, `config()`, et al. are all just shortcuts for a more verbose object-oriented interface.  In fact, it's possible to turn off these shortcuts so that we don't pollute the global scope, but we won't dig into that until the next section.

### Breaking it down

With that knowledge in hand, we can start to decipher this plugin code.  `Breeze\Application` is the class which all of your functions like `get()` and `post()` are delegated to.  When we call the static `Breeze\Application::register()` method, we are adding new functionality to this class which will be available when new instances are created.

The first argument for the `Breeze\Application::register()` function is the name you want to give your plugin.  This is helpful as it gives us a way to remove this plugin if we decide we no longer want it later on.  You can remove a plugin using the following syntax:

``` php
<?php

Breeze\Application::unregister('hello');
```

The second argument is a function that will be called when a new instance of `Breeze\Application` is created.  This is what enables you to define routes, configurations, templates, etc.  When this function is called, we get an instance of the current application which we can use to call all of our familiar functions.

**NOTE**: You must use the `$app->method()` syntax instead of the `method()` shortcuts when defining plugins.

**Helloworld.php:**

``` php
<?php

namespace Breeze\Plugins\HelloWorld;

use Breeze\Application;
require_once 'Breeze/Application.php';

Application::register('HelloWorld', function($app /* <-- this is an instance of Breeze\Application, ie your current app */){
    // This is the same as calling get() after your application has started
    $app->get('/', function(){
        echo 'Hello World';
    });

    // This is the same as calling config() after your application has started
    $app->config('this', 'is cool');

    // Shorthand for assigning a template variable.
    $app->something = 'cool';

    // The longhand versions also work
    $app->template('something', 'cool');
});
```

**Controller.php:**

``` php
<?php

require_once 'Breeze/plugins/Helloworld.php'; // This sets up the plugin
require_once 'Breeze.php'; // This creates an instance of Breeze\Application, and runs your plugin code
```

### Namespaces

If you haven't used namespaces in PHP yet, you might be confused by the backslashes in the class names.  This is simply how you express namespaces in PHP.  We encourage people that are writing plugins to use the appropriate namespaces.  In the next section we demonstrate how to use the Breeze Framework while not polluting the global scope with all the shortcut functions.  If you don't namespace your plugin appropriately, you will undermine this aspect of the framework and people will be less likely to use your plugins.

The Breeze standard for plugin namespaces is template engines should use the `Breeze\View\Driver` namespace and all other plugins should use `Breeze\Plugins\YourPluginNameHere` namespace.

### Templates

As we mentioned earlier, it's possible to create plugins that include templates.  When doing so, you must keep a couple of things in mind.  First, you should always use the default PHP template engine.  Never rely on a user to have a certain template engine available.  Second, you should always use the `$breeze` application variable discussed in the templates section when calling Breeze functions.  This assures that your application works even when developers using your plugin have the Breeze shortcuts disabled.

### Helpers

So far we've only showed you how to use existing framework components to bundle bits of functionality together that you can share with other apps.  What if you want to create your own framework elements?  To do this, you can use the `helper()` function.

``` php
<?php

helper('add_numbers', function($num1, $num2) {
    return $num1 + $num2;
});

get('/', function()) {
    echo add_numbers($_GET['num1'], $_GET['num2']);
}
```

Okay, this isn't exactly useful.  You could've have just created a normal function right?

``` php
<?php

function add_numbers($num1, $num2) {
    return $num1 + $num2;
}

get('/', function(){
    echo add_numbers($_GET['num1'], $_GET['num2']);
});
```

These two examples may look similar, but there's actually a very subtle difference.  The first example creates a function that is actually attached to the `Breeze\Application` class we described earlier.  We are just using the same trick to make it seem like a normal function.  This means that this function doesn't pollute the global scope if a user turns off shortcuts.  And since this method protects the global scope, it's safe to use when defining plugins.

**Auth.php:**

``` php
<?php

namespace Breeze\Plugins\Auth;

use Breeze\Application;
require_once 'Breeze/Application.php';

Application::register('Auth', function($app){
    $app->helper('auth', function($user, $redirect_url) use ($app){
        if($user->name != 'admin') {
            $app->redirect($redirect_url);
        }
    });
});
```

**Controller.php:**

``` php
<?php

require_once 'Breeze/plugins/Auth.php';
require_once 'Breeze.php';

get('/', function(){
    auth($_SESSION['user'], '/login');
});
```

The global scope
----------------

Let's be honest, if you're using PHP, your global scope is already a bit compromised.  With the introduction of namespaces though, PHP has finally given us some tools to help combat this problem.  And while having functions like `get()` and `display()` in your global scope may save you some keystrokes, it isn't exactly the ideal situation, especially for larger applications.  Fortunately, Breeze allows you to turn off these shortcut functions and use a slightly more verbose syntax to protect your scope.

**Controller.php:**

``` php
<?php

namespace Breeze\Examples\Scope;

use Breeze\Application;
require_once 'Breeze/Application.php';

$app = new Application();
$app->get('/', function($app){
    $app->display('index');
});

$app->run();
```

### Templates

Once you've disabled the Breeze shortcuts, you will need to use the `$breeze` application variable discussed in the templates section when calling Breeze functions.

**index.php:**

``` html
<h1>Hi there!</h1>
<p>I am using the <?php echo $breeze->config('template_engine'); ?> template engine.</p>
```

### Examples

Be sure to checkout the `namespaces` examples for a more complete treatment on protecting the global scope.

That's all folks
----------------

If you read this entire tutorial, you should be well on your way to creating Breeze applications.  Be sure to check out the included examples and plugins for more examples of Breeze in action.
