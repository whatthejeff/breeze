<?php
/**
 * Breeze Framework - Base classes
 *
 * This file contains the base classes for the Breeze Framework.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @category   Breeze
 * @package    Application
 * @copyright  Copyright (c) 2010, Breeze Framework
 * @license    New BSD License
 * @version    $Id$
 */


namespace Breeze\View\Driver {

    /**
     * @see Breeze\Application
     */
    use Breeze\Application;

    /**
     * This is the standard template engine interface for the Breeze
     * Framework.  New template engine extensions are required to implement
     * this interface.
     *
     * @category    Breeze
     * @package     View
     * @subpackage  Driver
     */
    interface DriverInterface
    {
        /**
         * Sets up the templates directory path and the extra options for a
         * database engine.  The extra options are to be defined by the
         * specific engines.
         *
         * @access public
         * @param  Breeze\Application $application   An instance of the base Breeze Framework class
         * @param  string $path                      The path to the templates directory
         * @param  array $options                    Extra options for setting up custom template engines
         * @return void
         */
        public function __construct(Application $application, $path = null, array $options = array());

        /**
         * Sets the path for the base templates directory.
         *
         * @access public
         * @param  string $path  The path to the templates directory
         * @return void
         */
        public function setPath($path);

        /**
         * Sets the extra options for the template engine.
         *
         * @access public
         * @param  array $options  Extra options for setting up custom template engines
         * @return void
         */
        public function setOptions(array $options);

        /**
         * Gets a template engine option.
         *
         * @access public
         * @param  string $option  The name of the option to get
         * @param  mixed $default  A fallback value if the option hasn't been specified.
         * @return mixed
         */
        public function getOption($option, $default = null);

        /**
         * Sets up the internal template engine structures.  This is intended
         * to be where engine specific options are set up.
         *
         * @access public
         * @return void
         */
        public function config();

        /**
         * Gets the full path to the templates directory.
         *
         * @access public
         * @return string  The full path to the templates directory.
         */
        public function getPath();

        /**
         * Checks if a template exists.
         *
         * @access public
         * @param  string $template  The path to the template, excluding the base templates directory.
         * @return boolean  If the template exists.
         */
        public function templateExists($template);

        /**
         * Gets the full path to a template, including the base templates
         * directory.
         *
         * @access public
         * @param  string $template  The path to the template, excluding the base templates directory.
         * @return string  The full path to a template.
         */
        public function getTemplatePath($template);

        /**
         * Renders a template using the $variables parameter and returns
         * the contents.
         *
         * @access public
         * @param  string $template  The path to the template, excluding the base templates directory.
         * @param  array $variables  An associative array of variables to use in the template.
         * @return string  The rendered template.
         */
        public function fetch($template, array $variables = array());
    }

    /**
     * This is the base implementation of the {@link Breeze\View\Driver\DriverInterface}
     * interface.  You should extend this if you're developing a template engine
     * extension as this class does most of the work for you.
     *
     * @category    Breeze
     * @package     View
     * @subpackage  Driver
     */
    abstract class Driver implements DriverInterface
    {
        /**
         * The message provided when a user provides a base template path
         * that is not readable.
         */
        const INVALID_PATH_ERROR = '%s is not a valid template path.';
        /**
         * The message provided when a user provides a template path
         * that is not readable.
         */
        const INVALID_TEMPLATE_ERROR = '%s is not a valid template.';

        /**
         * The base templates directory.
         *
         * @access  protected
         * @var     string
         */
        protected $_path;

        /**
         * The real path to the base templates directory.
         *
         * @access  protected
         * @var     string
         */
        protected $_real_path;

        /**
         * The extra options for the template engine.
         *
         * @access  protected
         * @var     array
         */
        protected $_options = array();

        /**
         * An instance of the base Breeze Framework class.
         *
         * @access  protected
         * @var     Breeze\Application
         */
        protected $_application;

        /**
         * Sets up the templates directory path and the extra options for a
         * database engine.  The extra options are to be defined by the
         * specific engines.
         *
         * @access public
         * @param  Breeze\Application $application   An instance of the base Breeze Framework class.
         * @param  string $path                      The path to the templates directory
         * @param  array $options                    Extra options for setting up custom template engines
         * @return void
         */
        public function __construct(Application $application, $path = null, array $options = array())
        {
            $this->_application = $application;

            if (!empty($options)) {
                $this->setOptions($options);
            }

            if (!empty($path)) {
                $this->setPath($path);
            }

            $this->_config();
        }

        /**
         * Sets the path for the base templates directory.
         *
         * @access public
         * @param  string $path  The path to the templates directory
         * @return void
         * @throws InvalidArgumentException
         */
        public function setPath($path)
        {
            if (!is_readable($path)) {
                throw new \InvalidArgumentException(sprintf(self::INVALID_PATH_ERROR, $path));
            }

            $this->_real_path = realpath($path);
            $this->_path = $path;
        }

        /**
         * Sets the extra options for the template engine.
         *
         * @access public
         * @param  array $options  Extra options for setting up custom template engines
         * @return void
         */
        public function setOptions(array $options)
        {
            $this->_options = $options;
        }

        /**
         * Gets a template engine option.
         *
         * @access public
         * @param  string $option  The name of the option to get
         * @param  mixed $default  A fallback value if the option hasn't been specified.
         * @return mixed
         */
        public function getOption($option, $default = null)
        {
            return isset($this->_options[$option]) ? $this->_options[$option] : $default;
        }

        /**
         * Gets the full path to the templates directory.
         *
         * @access public
         * @return string  The full path to the templates directory.
         */
        public function getPath()
        {
            return $this->_real_path;
        }

        /**
         * Checks if a template exists.
         *
         * @access public
         * @param  string $template  The path to the template, excluding the base templates directory.
         * @return boolean  If a template exists.
         */
        public function templateExists($template)
        {
            return is_readable($this->getTemplatePath($template));
        }

        /**
         * Gets the full path to a template, including the base templates
         * directory.
         *
         * @access public
         * @param  string $template  The path to the template, excluding the base templates directory.
         * @return string  The full path to a template.
         */
        public function getTemplatePath($template)
        {
            return $this->getPath() . "/$template";
        }

        /**
         * Renders a template using the $variables parameter and returns
         * the contents.
         *
         * @access public
         * @param  string $template  The path to the template, excluding the base templates directory.
         * @param  array $variables  An associative array of variables to use in the template.
         * @return string  The rendered template.
         * @throws InvalidArgumentException
         */
        public function fetch($template, array $variables = array())
        {
            if (!$this->templateExists($template)) {
                throw new \InvalidArgumentException(sprintf(self::INVALID_TEMPLATE_ERROR, $template));
            }

            return $this->_fetch($template, $variables);
        }

        /**
         * Sets up the internal template engine structures.  This is intended
         * to be where engine specific options are set up.
         *
         * @access public
         * @return void
         */
        public function config()
        {
            if ($this->_application->config('template_directory') != $this->_path || $this->_application->config('template_options') != $this->_options) {
                $this->_config();
            }
        }

        /**
         * Sets up the internal template engine structures.  This is intended
         * to be where engine specific options are set up.
         *
         * @access protected
         * @return void
         */
        abstract protected function _config();

        /**
         * Renders a template using the $variables parameter and returns
         * the contents.  This is sole function engines must implement.
         *
         * @access protected
         * @param  string $template  The path to the template, excluding the base templates directory.
         * @param  array $variables  An associative array of variables to use in the template.
         * @return string  The rendered template
         */
        abstract protected function _fetch($template, array $variables = array());
    }

    /**
     * The PHP-based template engine for the Breeze Framework.
     *
     * @category    Breeze
     * @package     View
     * @subpackage  Driver
     */
    class Php extends Driver
    {
        /**
         * Sets up the internal template engine structures.  This is intended
         * to be where engine specific options are set up.
         *
         * @access protected
         * @return void
         */
        protected function _config(){}

        /**
         * Renders a template using the $variables parameter and returns
         * the contents.
         *
         * @access protected
         * @param  string $template  The path to the template, excluding the base templates directory.
         * @param  array $variables  An associative array of variables to use in the template.
         * @return string  The rendered template.
         */
        protected function _fetch($template, array $variables = array())
        {
            extract($variables);
            ob_start();
            require $this->getTemplatePath($template);
            return ob_get_clean();
        }
    }
}

namespace Breeze\View {

    /**
     * @see Breeze\Application
     */
    use Breeze\Application;

    /**
     * The base view class which wraps an instance of {@link Breeze\View\Driver\DriverInterface}
     * to provide unified access to common template routines regardless of the underlining
     * engine implementation.
     *
     * @category    Breeze
     * @package     View
     */
    class View
    {
        /**
         * The message provided when a user configures the this app to use
         * a template engine that hasn't been included.
         */
        const INVALID_TEMPLATE_ENGINE_ERROR = '%s is not a valid template engine.';

        /**
         * An instance of the base Breeze Framework class.
         *
         * @access  protected
         * @var     Breeze\Application
         */
        protected $_application;
        /**
         * Variables to use in the templates.
         *
         * @access  protected
         * @var     array
         */
        protected $_template_variables = array();
        /**
         * The current template engine.
         *
         * @access  protected
         * @var     Breeze\View\Driver\DriverInterface
         */
        protected $_engine = null;

        /**
         * Creates a new View.
         *
         * @access public
         * @param  Breeze\Application $application  an instance of the base Breeze Framework class.
         * @return void
         */
        public function __construct(Application $application)
        {
            $this->_application = $application;
        }

        /**
         * Sets a template variable value.
         *
         * @access public
         * @param  string $name  The name of the template variable.
         * @param  mixed $value  The value of the template variable.
         * @return void
         */
        public function __set($name, $value)
        {
            $this->_template_variables[$name] = $value;
        }

        /**
         * Gets a template variable value.
         *
         * @access public
         * @param  string $name  The name of the template variable.
         * @return mixed  The template variable value.
         */
        public function __get($name)
        {
            return isset($this->_template_variables[$name]) ? $this->_template_variables[$name] : null;
        }

        /**
         * Checks if a template variable is set.
         *
         * @access public
         * @param  string $name  The name of the template variable.
         * @return boolean  If a template variable is set.
         */
        public function __isset($name)
        {
            return isset($this->_template_variables[$name]);
        }

        /**
         * Unsets a template variable.
         *
         * @access public
         * @param  string $name  The name of the template variable.
         * @return void
         */
        public function __unset($name)
        {
            unset($this->_template_variables[$name]);
        }

        /**
         * Adds template variables using an array.
         *
         * @access public
         * @param  array $variables  The variables to add to the template.
         * @return void
         */
        public function addVariables(array $variables)
        {
            $this->_template_variables = array_merge($this->_template_variables, $variables);
        }

        /**
         * Fetches the contents of the template specified by the $template argument using the
         * variables specified in the {@link Breeze\View\View::$_template_variables} instance
         * variable.
         *
         * @access public
         * @param  string $template  The path to the template, excluding the base templates directory and extension.
         * @param  array $variables  The variables to add to the template.
         * @return string  The rendered template contents.
         */
        public function fetch($template, array $variables = array())
        {
            if (!empty($variables)) {
                $this->addVariables($variables);
            }

            $this->__set($this->_application->config('application_variable'), $this->_application);

            $contents = $this->getEngine()->fetch($template . $this->_application->config('template_extension'), $this->_template_variables);
            return $this->layoutExists() ? $this->fetchLayout($contents) : $contents;
        }

        /**
         * Returns the $contents parameter wrapped with the the current layout
         * file.
         *
         * @access public
         * @param  string $contents  The contents to wrap with a layout
         * @return string  The rendered layout with the provided contents.
         */
        public function fetchLayout($contents)
        {
            return $this->getEngine()->fetch($this->_application->config('template_layout') . $this->_application->config('template_extension'), array_merge($this->_template_variables, array('layout_contents'=>$contents)));
        }

        /**
         * Checks to see if a layout is defined and the layout file exists.
         *
         * @access public
         * @return boolean  If a layout is defined and exists.
         */
        public function layoutExists()
        {
            $layout_path = $this->_application->config('template_layout');
            return $layout_path && $this->getEngine()->templateExists($layout_path . $this->_application->config('template_extension'));
        }

        /**
         * Displays the contents of the template specified by the $template argument using the
         * variables specified in the {@link Breeze\View\View::$_template_variables} instance
         * variable.
         *
         * @access public
         * @param  string $template  The path to the template, excluding the base templates directory and extension.
         * @param  array $variables  The variables to add to the template.
         * @return void
         */
        public function display($template, array $variables = array())
        {
            echo $this->fetch($template, $variables);
        }

        /**
         * Fetches the contents of the template specified by the $template argument using the
         * variables specified in the {@link Breeze\View\View::$_template_variables} instance
         * variable.
         *
         * NOTE: This works just like {@link Breeze\View\View::fetch()} but it ignores the
         * layout preferences.
         *
         * @access public
         * @param  string $template  The path to the template, excluding the base templates directory and extension.
         * @param  array $variables  The variables to add to the template.
         * @return string  The rendered template.
         */
        public function partial($template, array $variables = array())
        {
            $old_layout = $this->_application->config('template_layout');
            $this->_application->config('template_layout', false);

            $return = $this->fetch($template, $variables);
            $this->_application->config('template_layout', $old_layout);

            return $return;
        }

        /**
         * Specifies the path to the layouts directory.  Pass the boolean 'FALSE' constant to bypass
         * layouts completely.
         *
         * @access public
         * @param  string $layout  The path to the layout, excluding the base templates directory.
         * @return void
         */
        public function layout($layout)
        {
            $this->_application->config('template_layout', $layout);
        }

        /**
         * Using the current application configurations, retrieves an instance of the specified
         * template engine.
         *
         * @access public
         * @return Breeze\View\Driver\DriverInterface  The template engine.
         * @throws UnexpectedValueException
         */
        public function getEngine()
        {
            $engine_class = __NAMESPACE__ . '\\Driver\\' . $this->_application->config('template_engine');

            if (strtolower(get_class((object)$this->_engine)) != strtolower($engine_class)) {
                if (class_exists($engine_class) && in_array(__NAMESPACE__ . '\\Driver\\DriverInterface', class_implements($engine_class))) {
                    $this->_engine = new $engine_class($this->_application, $this->_application->config('template_directory'), $this->_application->config('template_options'));
                } else {
                    throw new \UnexpectedValueException(sprintf(self::INVALID_TEMPLATE_ENGINE_ERROR, $engine_class));
                }
            } else {
                $this->_engine->config();
            }

            return $this->_engine;
        }
    }
}

namespace Breeze\Errors {

    /**
     * @see Breeze\Application
     */
    use Breeze\Application;
    /**
     * @see Breeze\ClosuresCollection
     */
    use Breeze\ClosuresCollection;

    /**
     * Standard "undefined function" error message to use in __call() functions.
     */
    const UNDEFINED_FUNCTION = 'Call to undefined function: %s::%s().';
    /**
     * Error provided if a programmer neglects to provide a callable
     * function as the closure argument.
     */
    const INVALID_CLOSURE_ERROR = 'You must provide a callable PHP function.';

    /**
     * The base exception class for all other Breeze Framework exceptions to extend.
     *
     * @category    Breeze
     * @package     Errors
     */
    class Exception extends \Exception {}

    /**
     * The base error handler for the Breeze Framework.  This class is used to
     * define closures that are associated with HTTP errors and exceptions that
     * might be thrown during the Breeze Framework lifecycle.
     *
     * @code
     *     $handler = new Breeze\Errors\Errors($app);
     *     $handler->add('404', function(){
     *        echo "Page not found!";
     *     });
     *     $handler->dispatchError(new Breeze\Dispatcher\NotFoundException()); // Echos "Page not found!"
     * @endcode
     *
     * @category    Breeze
     * @package     Errors
     */
    class Errors extends ClosuresCollection
    {
        /**
         * The standard HTTP error responses.
         */
        const HTTP_400 = 'Bad Request';
        const HTTP_401 = 'Unauthorized';
        const HTTP_402 = 'Payment Required';
        const HTTP_403 = 'Forbidden';
        const HTTP_404 = 'Not Found';
        const HTTP_405 = 'Method Not Allowed';
        const HTTP_406 = 'Not Acceptable';
        const HTTP_407 = 'Proxy Authentication Required';
        const HTTP_408 = 'Request Timeout';
        const HTTP_409 = 'Conflict';
        const HTTP_410 = 'Gone';
        const HTTP_411 = 'Length Required';
        const HTTP_412 = 'Precondition Failed';
        const HTTP_413 = 'Request Entity Too Large';
        const HTTP_414 = 'Request-URI Too Long';
        const HTTP_415 = 'Unsupported Media Type';
        const HTTP_416 = 'Requested Range Not Satisfiable';
        const HTTP_417 = 'Expectation Failed';
        const HTTP_500 = 'Internal Server Error';
        const HTTP_501 = 'Not Implemented';
        const HTTP_502 = 'Bad Gateway';
        const HTTP_503 = 'Service Unavailable';
        const HTTP_504 = 'Gateway Timeout';
        const HTTP_505 = 'HTTP Version Not Supported';

        /**
         * Error provided if a user tries to dispatch an error that is
         * not a string or an Exception.
         */
        const INVALID_ERROR = 'Errors must be a string or a valid Exception';

        /**
         * The default closure to use for all errors with no defined handler.
         *
         * @access  protected
         * @var     Closure
         */
        protected $_default_error;
        /**
         * An instance of the base Breeze Framework class for passing into closures.
         *
         * @access  protected
         * @var     Breeze\Application
         */
        protected $_application;
        /**
         * If dispatching an error should cause the application to exit.
         *
         * @access  protected
         * @var     boolean
         */
        protected $_exit = true;

        /**
         * Sets up the {@link Breeze\Errors\Errors::$_default_error} instance variable
         * for handling errors with no defined handler.
         *
         * @access public
         * @param  Breeze\Application $application  an instance of the base Breeze Framework class for passing into closures.
         * @return void
         */
        public function __construct(Application $application)
        {
            $this->_application = $application;
            $this->_default_error = function(Application $application, \Exception $exception) use ($application) {
                $body = sprintf("<h1>%s</h1>", $exception->getMessage());
                if ($application->config('errors_backtrace')) {
                    $body .= sprintf('<pre><code>%s</code></pre>', $exception->getTraceAsString());
                }

                if ($application->layoutExists()) {
                    echo $application->fetchLayout($body);
                } else {
                    echo "<!DOCTYPE html><html><head><title>An error occurred</title></head><body>{$body}</body></html>";
                }
            };
        }

        /**
         * Sets if dispatching errors should cause the application to exit.
         *
         * @access public
         * @return boolean  If dispatching errors should cause the application to exit.
         */
        public function getExit()
        {
            return $this->_exit;
        }

        /**
         * Sets if dispatching errors should cause the application to exit.
         *
         * @access public
         * @param  boolean  If dispatching errors should cause the application to exit.
         * @return void
         */
        public function setExit($exit)
        {
            $this->_exit = $exit;
        }

        /**
         * Gets the HTTP error string that corresponds to an HTTP status code.
         *
         * @access public
         * @param  integer $code  The code to get the error for
         * @return string  The error code.
         */
        public function getErrorForCode($code)
        {
            return defined("self::HTTP_$code") ? constant("self::HTTP_$code") : null;
        }

        /**
         * Adds a handler for the errors specified by the $names argument.
         *
         * @code
         *     // Examples
         *     $handler->add('403', function(){});
         *     $handler->add('Exception', function(){});
         *     $handler->add(array('404','405'), function(){});
         *     $handler->add(range(400,417), function(){});
         *     $handler->add(function(){}); // Catchall
         * @endcode
         *
         * @access public
         * @param  mixed $names      The codes/exceptions to add errors for.
         * @param  Closure $handler  The handler for the error.
         * @return void
         * @throws InvalidArgumentException
         */
        public function add($names, $handler = null, $validate = null)
        {
            if (!isset($handler) && is_callable($names)) {
                $this->_default_error = $names;
            } else {
                parent::add($names, $handler, self::VALIDATE_NAME);
            }
        }

        /**
         * Dispatches the closure associated with the $exception argument, falling
         * back to the {@link Breeze\Errors\Errors::$_default_error} instance variable
         * if no closure can been found.
         *
         * @access public
         * @param  mixed $exception  The exception to dispatch an error for.
         * @param  mixed $code       The error code for the exception if $exception is a message.
         * @return void
         */
        public function dispatchError($exception, $code = null)
        {
            if (is_string($exception)) {
                $exception = new \Exception($exception, $code);
            } elseif (!(is_object($exception) && $exception instanceof \Exception)) {
                throw new \InvalidArgumentException(self::INVALID_ERROR);
            }

            $number = $exception->getCode();
            $http_message = $this->getErrorForCode($number);

            if (!($function = $this->get($number)) && !($function = $this->get(get_class($exception)))) {
                $function = $this->_default_error;
            }

            if (!headers_sent() && $http_message && isset($_SERVER['SERVER_PROTOCOL'])) {
                header("{$_SERVER['SERVER_PROTOCOL']} $number $http_message");
            }

            $function($this->_application, $exception);

            if ($this->getExit()) {
                exit;
            }
        }
    }
}

namespace Breeze\Dispatcher {

    /**
     * @see Breeze\Application
     */
    use Breeze\Application;
    /**
     * @see Breeze\ClosuresCollection
     */
    use Breeze\ClosuresCollection;
    /**
     * @see Breeze\Errors\Exception
     */
    use Breeze\Errors\Exception;

    /**
     * The "Pass" exception which is used to skip to the next
     * matching route.
     *
     * @code
     *     $app->get('/hello', function(){
     *        throw new PassException();
     *     });
     *
     *     $app->get(';.+;', function(){
     *        echo "Made it to this route because you used the PassException"
     *     })
     * @endcode
     *
     * @category    Breeze
     * @package     Dispatcher
     */
    class PassException extends Exception {}

    /**
     * The "Not Found" exception used to indicate a HTTP 404 error.
     *
     * @category    Breeze
     * @package     Dispatcher
     */
    class NotFoundException extends Exception
    {
        /**
         * Constructor.
         *
         * @access public
         * @param  string $message  The exception message.
         * @return void
         */
        public function __construct($message = null)
        {
            parent::__construct($message, 404);
        }
    }

    /**
     * The base conditions manager for forwarding requests that don't
     * meet certain conditions.
     *
     * @category    Breeze
     * @package     Dispatcher
     */
    class Conditions extends ClosuresCollection
    {
        /**
         * Error message provided if the programmer attempts to call a condition
         * that doesn't exist.
         */
        const INVALID_CONDITION_ERROR = '%s is not a valid condition.';

        /**
         * Sets up some default conditions.
         *
         * @access public
         * @return void
         */
        public function __construct() {
            $this->add('user_agent_matches', function($pattern) {
                return (bool) preg_match($pattern, $_SERVER['HTTP_USER_AGENT']);
            });

            $this->add('host_name_is', function($pattern) {
               return $pattern === $_SERVER['HTTP_HOST'];
            });
        }

        /**
         * Adds a new condition.
         *
         * @access public
         * @param  string|array $names  A list of names to associate with the provided closure
         * @param  Closure $condition   The condition to add.
         * @return void
         * @throws InvalidArgumentException
         */
        public function add($names, $condition = null, $validate = null)
        {
            parent::add($names, $condition, self::VALIDATE_NAME);
        }

        /**
         * Checks a condition, throwing a Breeze\Dispatcher\PassException if the condition
         * is not met.
         *
         * @access public
         * @param  string $name  The condition to dispatch
         * @return void
         * @throws InvalidArgumentException
         * @throws PassException
         */
        public function dispatchCondition($name)
        {
            if (!($condition = $this->get($name))) {
                throw new \InvalidArgumentException(sprintf(self::INVALID_CONDITION_ERROR, $name));
            } elseif (!call_user_func_array($condition, array_slice(func_get_args(), 1))) {
                throw new PassException();
            }
        }
    }

    /**
     * The base URL dispatcher for the Breeze Framework.  This class is used to
     * define closures that are associated with HTTP requests.
     *
     * @code
     *     $handler = new Breeze\Dispatcher\Dispatcher($app);
     *     $handler->get('/', function(){
     *        echo "Hello World!";
     *     });
     *     $handler->dispatch('GET', '/') // echos hello world
     * @endcode
     *
     * @category    Breeze
     * @package     Dispatcher
     */
    class Dispatcher
    {
        /**
         * Message to inform the end-user that no routes match the current request
         */
        const NO_ROUTES_ERROR = 'No Matching Routes Found';
        /**
         * Error message provided if the programmer attempts to associate a closure
         * with an empty pattern.
         */
        const NO_PATTERN_ERROR = 'You must provide a non-empty pattern';

        /**
         * The supported HTTP methods.
         *
         * @access  protected
         * @var     array
         * @static
         */
        protected static $_supported_methods = array('GET','POST','PUT','DELETE');

        /**
         * An instance of the base Breeze Framework class for querying configurations.
         *
         * @access  protected
         * @var     Breeze\Application
         */
        protected $_application;
        /**
         * The URI to use for routing.
         *
         * @access  protected
         * @var     string
         */
        protected $_request_uri;
        /**
         * The HTTP method to use for routing.
         *
         * @access  protected
         * @var     string
         */
        protected $_request_method = 'GET';
        /**
         * A collection of user-defined request handlers.
         *
         * @access  protected
         * @var     array
         */
        protected $_routes;

        /**
         * Creates a new dispatcher for routing end-user requests.
         *
         * @access public
         * @param  Breeze\Application $application  An instance of the base Breeze Framework class for passing into closures.
         * @return void
         */
        public function __construct(Application $application)
        {
            $this->_application = $application;

            foreach (self::$_supported_methods as $method) {
                $this->_routes[$method] = array();
            }
        }

        /**
         * Provides access to the get(), post(), etc. methods that are
         * essentially just aliases of the {@link Breeze\Dispatcher\Dispatcher::route()}
         * method.
         *
         * @access public
         * @param  string $name      The name of the method
         * @param  array $arguments  The method arguments
         * @return mixed
         */
        public function __call($name, $arguments)
        {
            $name = strtoupper($name);

            if (in_array($name, self::$_supported_methods)) {
                return call_user_func_array(array($this, '_addRoute'), array_merge(array($name), (array)$arguments));
            } elseif ($name == 'ANY') {
                if (isset($arguments[1]) && is_callable($arguments[1])) {
                    $arguments = array_merge(array(self::$_supported_methods), (array)$arguments);
                }

                return call_user_func_array(array($this, '_addRoute'), $arguments);
            }

            trigger_error(sprintf(\Breeze\Errors\UNDEFINED_FUNCTION, get_class($this), $name), E_USER_ERROR);
        }

        /**
         * Adds a route that will be checked when routing requests using the
         * {@link Breeze\Dispatcher\Dispatcher::dispatch()} method.
         *
         * @access protected
         * @param  string|array $methods  The HTTP method that must match
         * @param  string $pattern        The pattern the requested URI must match
         * @param  Closure $handler       The handler for the matching request
         * @return void
         * @throws InvalidArgumentException
         */
        protected function _addRoute($methods, $pattern, $handler)
        {
            foreach ((array)$methods as $method) {
                if (!is_callable($handler)) {
                    throw new \InvalidArgumentException(\Breeze\Errors\INVALID_CLOSURE_ERROR);
                } elseif (!$pattern) {
                    throw new \InvalidArgumentException(self::NO_PATTERN_ERROR);
                }

                $this->_routes[$method][] = array('pattern'=>$pattern, 'handler'=>$handler);
            }
        }

        /**
         * Checks all routes added with {@link Breeze\Dispatcher\Dispatcher::route()}
         * and calls the corresponding handler if a matching request is found.
         * Otherwise a {@link Breeze\Dispatcher\NotFoundException} exception is thrown.
         *
         * @access public
         * @param  string $request_method  An optional request method to spoof the incoming request method.
         * @param  string $request_uri     An optional URI to spoof the incoming request URI.
         * @return void
         * @throws Breeze\Dispatcher\NotFoundException
         */
        public function dispatch($request_method = null, $request_uri = null)
        {
            $this->setRequestUri($request_uri);
            $this->setRequestMethod($request_method);

            if (isset($this->_routes[$this->_request_method])) {
                foreach ($this->_routes[$this->_request_method] as $route) {
                    try {
                        if ($route['pattern']{0} != '/') {
                            $this->_processRegexpRoute($route['pattern'], $route['handler']);
                        } else {
                            $this->_processRoute($route['pattern'], $route['handler']);
                        }
                    } catch (PassException $exception) {
                        continue;
                    }
                    return;
                }
            }

            throw new NotFoundException(self::NO_ROUTES_ERROR);
        }

        /**
         * Invokes the $handler if the current value of  {@link Breeze\Dispatcher\Dispatcher::$_request_uri}
         * matches the $pattern.  Otherwise a {@link Breeze\Dispatcher\PassException}
         * exception is thrown.
         *
         * @access protected
         * @param  string $pattern   The pattern the requested URI must match
         * @param  Closure $handler  The handler for the matching request
         * @return void
         * @throws Breeze\Dispatcher\PassException
         */
        protected function _processRoute($pattern, $handler)
        {
            if ($this->_request_uri != $pattern) {
                throw new PassException();
            }

            $handler($this->_application);
        }

        /**
         * Invokes the $handler if the current value of  {@link Breeze\Dispatcher\Dispatcher::$_request_uri}
         * matches the regexp $pattern.  Otherwise a {@link Breeze\Dispatcher\PassException}
         * exception is thrown.
         *
         * @access protected
         * @param  string $pattern   The pattern the requested URI must match
         * @param  Closure $handler  The handler for the matching request
         * @return void
         * @throws Breeze\Dispatcher\PassException
         */
        protected function _processRegexpRoute($pattern, $handler)
        {
            if (!preg_match($pattern, $this->_request_uri, $matches)) {
                throw new PassException();
            }

            $handler($this->_application, $matches);
        }

        /**
         * Sets (and normalizes) the request URI.  $_SERVER['REQUEST_URI'] is used
         * by default if $request_uri is not provided.
         *
         * @access public
         * @param  string $request_uri  The request URI to use.
         * @return void
         */
        public function setRequestUri($request_uri = null)
        {
            if ($request_uri === null) {
                if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
                    $this->_request_uri = $_SERVER['HTTP_X_REWRITE_URL'];
                } elseif (isset($_SERVER['IIS_WasUrlRewritten']) && $_SERVER['IIS_WasUrlRewritten'] == '1'
                    && isset($_SERVER['UNENCODED_URL']) && $_SERVER['UNENCODED_URL'] != '') {
                    $this->_request_uri = $_SERVER['UNENCODED_URL'];
                } elseif (isset($_SERVER['REQUEST_URI'])) {
                    $this->_request_uri = $_SERVER['REQUEST_URI'];
                } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
                    $this->_request_uri = $_SERVER['ORIG_PATH_INFO'];
                }
            } else {
                $this->_request_uri = $request_uri;
            }

            $this->_request_uri = parse_url($this->_request_uri, PHP_URL_PATH);
            if ($this->_request_uri == '') {
                $this->_request_uri = '/';
            }
            if ($this->_request_uri != '/' && substr($this->_request_uri, -1) == '/') {
                $this->_request_uri = substr($this->_request_uri, 0, -1);
            }
        }

        /**
         * Gets the currently set request URI.
         *
         * @access public
         * @return string
         */
        public function getRequestUri()
        {
            return $this->_request_uri;
        }

        /**
         * Sets (and normalizes) the request method.  $_SERVER['REQUEST_METHOD'] is
         * used by default if $request_method is not provided.
         *
         * @access public
         * @param  string $request_method  The request method to use.
         * @return void
         */
        public function setRequestMethod($request_method = null)
        {
            if ($request_method === null) {
                if (isset($_ENV['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
                    $this->_request_method = $_ENV['HTTP_X_HTTP_METHOD_OVERRIDE'];
                } elseif (isset($_SERVER['REQUEST_METHOD'])) {
                    $this->_request_method = $_SERVER['REQUEST_METHOD'];
                }
            } else {
                $this->_request_method = $request_method;
            }

            $this->_request_method = strtoupper($this->_request_method);
            if ($this->_request_method == 'POST' && isset($_POST['_method'])) {
                $this->_request_method = strtoupper($_POST['_method']);
            } elseif ($this->_request_method == 'HEAD') {
                $this->_request_method = 'GET';
            }
        }

        /**
         * Gets the currently set request method.
         *
         * @access public
         * @return string
         */
        public function getRequestMethod()
        {
            return $this->_request_method;
        }
    }
}

namespace Breeze {

    /**
     * @see Breeze\Dispatcher\PassException
     */
    use Breeze\Dispatcher\PassException;

    /**
     * The current version of the Breeze Framework.
     */
    const VERSION = '1.0';

    /**
     * Error provided if a programmer attempts to add a closure with
     * not name.
     */
    const NO_NAME_ERROR = 'You must provide a name.';

    /**
     * General purpose class for aggregating closures.
     *
     * @category   Breeze
     * @package    Application
     */
    class ClosuresCollection
    {
        /**
         * Flag to indicate that a closure must be associated with a name.
         */
        const VALIDATE_NAME = 'name';
        /**
         * Flag to indicate that a closure must be associated with a valid
         * PHP label.
         */
        const VALIDATE_LABEL = 'label';
        /**
         * Regexp for validating a PHP label.
         *
         * @see http://www.php.net/manual/en/functions.user-defined.php
         */
        const VALID_LABEL = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';
        /**
         * Error provided if a programmer attempts to use an invalid name
         * for a closure.
         */
        const INVALID_LABEL_ERROR = '%s is not a valid PHP function name.';

        /**
         * Collection of named closures.
         *
         * @access  protected
         * @var     array
         */
        protected $_named_closures = array();
        /**
         * Collection of unnamed closures.
         *
         * @access  protected
         * @var     array
         */
        protected $_closures = array();

        /**
         * Associates a closure with a single name or a group of names.
         *
         * @access public
         * @param  string|array $names  A list of names to associate with the provided closure
         * @param  Closure $closure     The closure to add
         * @param  string $validate     A flag to indicate what type of validation to do on closure names.
         * @return void
         * @throws InvalidArgumentException
         */
        public function add($names, $closure = null, $validate = null)
        {
            if (!isset($closure)) {
                $closure = $names;
                $names = '';
            }

            if (!is_callable($closure)) {
                throw new \InvalidArgumentException(\Breeze\Errors\INVALID_CLOSURE_ERROR);
            }

            foreach ((array)$names as $name) {
                if (empty($name)) {
                    if (!empty($validate)) {
                        throw new \InvalidArgumentException(NO_NAME_ERROR);
                    }

                    $this->_closures[] = $closure;
                } else {
                    if ($validate == self::VALIDATE_LABEL && !preg_match(self::VALID_LABEL, $name)) {
                        throw new \InvalidArgumentException(sprintf(self::INVALID_LABEL_ERROR, $name));
                    }

                    $this->_named_closures[$name] = $closure;
                }
            }
        }

        /**
         * Checks if a $name has any closures associated with it.
         *
         * @access public
         * @param  string $name  The name to check.
         * @return boolean  If $name has any closures associated with it.
         */
        public function has($name)
        {
            return isset($this->_named_closures[$name]);
        }

        /**
         * Gets the closure associated with a specified $name.
         *
         * @access public
         * @param  string $name  The name to to get the closure for.
         * @return mixed  The closure associated with $name.
         */
        public function get($name)
        {
            return $this->has($name) ? $this->_named_closures[$name] : null;
        }

        /**
         * Returns all defined closures.
         *
         * @access public
         * @return array  All defined closures.
         */
        public function all()
        {
            return array_merge($this->_closures, $this->_named_closures);
        }
    }

    /**
     * The base configurations handler for the Breeze Framework.  This class is used
     * to manage global application settings.
     *
     * @category   Breeze
     * @package    Application
     */
    class Configurations
    {
        /**
         * The application configurations.
         *
         * @access  protected
         * @var     array
         */
        protected $_configurations = array(
            'template_engine'       => 'PHP',      /* The template engine */
            'template_options'      => array(),    /* The extra options for the template engine */
            'template_directory'    => '../views', /* The base directory for all templates */
            'template_extension'    => '.php',     /* The common extension for all templates */
            'template_layout'       => 'layout',   /* The default path to the layout file */
            'application_variable'  => 'breeze',   /* The template variable that holds the current application object */
            'errors_backtrace'      => true        /* If error templates should include backtraces */
        );

        /**
         * Initializes the configurations using $options.
         *
         * @access public
         * @param  array $options  The default options to set
         * @return void
         */
        public function __construct(array $options = array())
        {
            $this->_configurations = array_merge($this->_configurations, $options);
        }

        /**
         * Sets a configuration value(s).
         *
         * @code
         *     $config = new Breeze\Configurations();
         *     $config->set('jeff', 'is cool');
         *     $config->set(array('jeff'=>'is cool'));
         * @endcode
         *
         * @access public
         * @param  string|array $name  The name of the configuration value to set or an array of values to set.
         * @param  mixed $value        The value to set.
         * @return void
         */
        public function set($name, $value = null)
        {
            if (is_array($name)) {
                $this->_configurations = array_merge($this->_configurations, $name);
            } else {
                $this->_configurations[$name] = $value;
            }
        }

        /**
         * Gets a configuration value.
         *
         * @access public
         * @param  string $name  The name of the configuration value to get.
         * @return mixed
         */
        public function get($name)
        {
            return isset($this->_configurations[$name]) ? $this->_configurations[$name] : null;
        }
    }

    /**
     * Base Breeze application class.  This class serves as a facade for the rest
     * of the framework's classes to facilitate the "micro framework" feel while
     * maintaining a more modular, extensible design.
     *
     * @category   Breeze
     * @package    Application
     */
    class Application
    {
        /**
         * Error provided if a programmer attempts to trigger a filter that
         * is not a valid filter type.
         */
        const INVALID_FILTER_ERROR = '%s is not a valid filter type.';
        /**
         * Error provided if a programmer attempts to add a dependency that
         * does not follow the proper protocol.
         */
        const INVALID_DEPENDENCY_ERROR = '%s is not an instance of %s.';
        /**
         * A generic error message used if a user doesn't provide a code
         * or message to {@link Breeze\Application::error()}.
         */
        const GENERIC_ERROR = 'An Error Occurred.';
        /**
         * Message format for associating a code with an error message.
         * Example: 404 - Not Found
         */
        const ERROR_CODE_MESSAGE = '%s - %s';

        /**
         * Key for before filters.
         */
        const BEFORE = 'before';
        /**
         * Key for after filters.
         */
        const AFTER = 'after';

        /**
         * Collection of plugins that are attached to the Base application
         * class at runtime.
         *
         * @access  protected
         * @var     array
         * @static
         */
        protected static $_plugins = array();
        /**
         * Collection of pre-defined helpers.
         *
         * @access  protected
         * @var     array
         * @static
         */
        protected static $_core_helpers = array(
            'get','delete','put','post','any','before','after',
            'config','template','display','fetch','pass','helper',
            'run','error','condition','redirect','partial'
        );

        /**
         * Collection of user configurations.
         *
         * @access  protected
         * @var     Breeze\Configurations
         */
        protected $_configurations;
        /**
         * Core view class for delegating to different template engines.
         *
         * @access  protected
         * @var     Breeze\View\View
         */
        protected $_view;
        /**
         * Core dispatcher for routing end-user requests to pre-defined
         * actions.
         *
         * @access  protected
         * @var     Breeze\Dispatcher\Dispatcher
         */
        protected $_dispatcher;
        /**
         * Core conditions manager for forwarding requests that don't
         * meet certain conditions.
         *
         * @access  protected
         * @var     Breeze\Dispatcher\Conditions
         */
        protected $_conditions;
        /**
         * Core error handler for managing errors which are invoked during
         * routing.
         *
         * @access  protected
         * @var     Breeze\Errors\Errors
         */
        protected $_error_handler;

        /**
         * User-defined helpers.
         *
         * @access  protected
         * @var     Breeze\ClosuresCollection
         */
        protected $_user_helpers;
        /**
         * User-defined filters.
         *
         * @access  protected
         * @var     Breeze\ClosuresCollection
         */
        protected $_filters = array();

        /**
         * Initializes defined plugins and allows for overriding of default
         * application configurations.
         *
         * @access public
         * @param  Breeze\Configurations $configurations  Override default application configurations.
         * @return void
         */
        public function __construct(Configurations $configurations = null)
        {
            $this->_configurations = isset($configurations) ? $configurations : new Configurations();

            $this->_view = $this->_getDependency('view_object', 'Breeze\\View\\View');
            $this->_error_handler = $this->_getDependency('errors_object', 'Breeze\\Errors\\Errors');
            $this->_dispatcher = $this->_getDependency('dispatcher_object', 'Breeze\\Dispatcher\\Dispatcher');
            $this->_conditions = $this->_getDependency('conditions_object', 'Breeze\\Dispatcher\\Conditions');

            $this->_user_helpers = $this->_getDependency('helpers_object', 'Breeze\\ClosuresCollection');
            $this->_filters = array(
                self::BEFORE=>$this->_getDependency('before_filters_object', 'Breeze\\ClosuresCollection'),
                self::AFTER=>$this->_getDependency('after_filters_object', 'Breeze\\ClosuresCollection')
            );

            foreach (self::$_plugins as $plugin) {
                $plugin($this);
            }
        }

        /**
         * Injects a dependency.  This is mostly intended to help with testing.
         *
         * @access public
         * @param  string $key   The configuration key where the dependency is stored.
         * @param  string $name  The class name for the dependency.
         * @return void
         */
        protected function _getDependency($key, $name)
        {
            if ($dependency = $this->_configurations->get($key)) {
                if (!is_a($dependency, $name)) {
                    throw new \UnexpectedValueException(sprintf(self::INVALID_DEPENDENCY_ERROR, get_class($dependency), $name));
                }

                return $dependency;
            }

            return in_array($key, array('view_object','errors_object','dispatcher_object')) ? new $name($this) : new $name();
        }

        /**
         * Throws a {@link Breeze\Dispatcher\PassException}.  When used in the
         * context of request handlers, it passes control to the next matching
         * request.
         *
         * @code
         *     $app = new Breeze\Application();
         *     $app->get('/hello', function($app){
         *        $app->pass();
         *     });
         *
         *     $app->get(';.+;', function(){
         *        echo "Made it to this route because you used Breeze\Application::pass()"
         *     })
         * @endcode
         *
         * @access public
         * @return void
         * @throws Breeze\Dispatcher\PassException
         */
        public function pass()
        {
            throw new PassException();
        }

        /**
         * Shortcut for making a redirect.
         *
         * @access public
         * @param  string $url    The url to redirect to.
         * @param  integer $code  The status code for the redirect.
         * @param  boolean $exit  If the program should exit after header is sent.
         * @return void
         */
        public function redirect($url, $code = 302, $exit = false)
        {
            header("Location: $url", true, $code);

            if ($exit) {
                exit;
            }
        }

        /**
         * Shortcut for getting and setting configuration values.
         *
         * @code
         *     $app = new Breeze\Application();
         *     $app->config('jeff', 'is cool');
         *     echo $app->config('jeff'); // Prints "is cool"
         * @endcode
         *
         * @access public
         * @param  string $name  The name of the config value to set/get
         * @param  mixed $value  The config value to set
         * @return void
         */
        public function config($name, $value = null)
        {
            if (func_num_args() > 1 || is_array($name)) {
                return $this->_configurations->set($name, $value);
            }

            return $this->_configurations->get($name);
        }

        /**
         * Shortcut for setting and dispatching conditions.
         *
         * @code
         *     $app = new Breeze\Application();
         *     $app->condition('is cool', function($name) { return $name == 'jeff' });
         *     $app->condition('is cool', 'someone else'); // Throws Breeze\Dispatcher\PassException
         * @endcode
         *
         * @access public
         * @param  string $name    The name of the condition to set/dispatch
         * @param  mixed $handler  The condition handler
         * @return void
         * @throws Breeze\Dispatcher\PassException
         */
        public function condition($name, $handler = null)
        {
            if (is_callable($handler)) {
                return $this->_conditions->add($name, $handler);
            }

            return call_user_func_array(array($this->_conditions, 'dispatchCondition'), func_get_args());
        }

        /**
         * Shortcut for getting and setting template values.
         *
         * @code
         *     $app = new Breeze\Application();
         *     $app->template('jeff', 'is cool');
         *     echo $app->template('jeff'); // Prints "is cool"
         * @endcode
         *
         * @access public
         * @param  string $name  The name of the template value to set/get
         * @param  mixed $value  The template value to set
         * @return void
         */
        public function template($name, $value = null)
        {
            if (func_num_args() > 1) {
                return $this->_view->__set($name, $value);
            } elseif (is_array($name)) {
                return $this->_view->addVariables($name);
            }

            return $this->_view->__get($name);
        }

        /**
         * Shortcut for adding or dispatching an error.
         *
         * @code
         *     $app = new Breeze\Application();
         *
         *     // Defining error handlers
         *     $app->error(function(){ echo "default handler"; });
         *     $app->error(403, function(){ echo 'permission denied'; });
         *     $app->error('JeffsException', function(){ echo "Jeff's Exception"; });
         *
         *     // Dispatching errors
         *     $app->error(403)
         *     $app->error(403, "Permission Denied");
         *     $app->error("Permission Denied");
         * @endcode
         *
         * @access public
         * @param  mixed $var1  A numeric code, a message, or a closure
         * @param  mixed $var2  A message, or a closure
         * @return void
         */
        public function error($var1 = '', $var2 = '')
        {
            if (is_callable($var2) || is_callable($var1)) {
                return call_user_func_array(array($this->_error_handler, 'add'), func_get_args());
            }

            if (!is_numeric($var1)) {
                $var2 = $var1;
                $var1 = 0;
            }

            if (!$var2) {
                if ($var2 = $this->_error_handler->getErrorForCode($var1)) {
                    $var2 = sprintf(self::ERROR_CODE_MESSAGE, $var1, $var2);
                } else {
                    $var2 = self::GENERIC_ERROR;
                }
            }

            return $this->_error_handler->dispatchError($var2, $var1);
        }

        /**
         * Delegates methods to their corresponding classes and handles
         * user-defined helpers.
         *
         * @access public
         * @param  string $name      The name of the method
         * @param  array $arguments  The method arguments
         * @return void
         */
        public function __call($name, $arguments)
        {
            if (in_array($name, array('display','fetch','layout','partial','layoutExists','fetchLayout'))) {
                return call_user_func_array(array($this->_view, $name), $arguments);
            }

            if (in_array($name, array('get','delete','put','post','any'))) {
                if ($name != 'any' && (count($arguments) < 2 || !is_callable($arguments[1]))) {
                   return call_user_func_array(array($this, 'run'), array_merge(array($name), $arguments));
                }
                return call_user_func_array(array($this->_dispatcher, $name), $arguments);
            }

            if ($this->_user_helpers->has($name)) {
                return call_user_func_array($this->_user_helpers->get($name), $arguments);
            }

            trigger_error(sprintf(\Breeze\Errors\UNDEFINED_FUNCTION, get_class($this), $name), E_USER_ERROR);
        }

        /**
         * Shortcut for setting a template value.
         *
         * @access public
         * @param  string $name  The name of the template value to set
         * @param  mixed $value  The template value to set
         * @return void
         */
        public function __set($name, $value)
        {
            $this->_view->__set($name, $value);
        }

        /**
         * Shortcut for getting a template value
         *
         * @access public
         * @param  string $name  The name of the template value to get
         * @return mixed  THe template value
         */
        public function __get($name)
        {
            return $this->_view->__get($name);
        }

        /**
         * Shortcut for checking if a template value is set.
         *
         * @access public
         * @param  string $name  The name of the template value to check.
         * @return boolean  If the variable is set
         */
        public function __isset($name)
        {
            return $this->_view->__isset($name);
        }

        /**
         * Shortcut for unsetting a template value.
         *
         * @access public
         * @param  string $name  The name of the template value to unset.
         * @return void
         */
        public function __unset($name)
        {
            $this->_view->__unset($name);
        }

        /**
         * Registers a new plugin.
         *
         * @access public
         * @param  string $name     The name of the plugin to register.
         * @param  Closure $plugin  The plugin to register
         * @return void
         * @throws InvalidArgumentException
         */
        public static function register($name, $plugin)
        {
            if (!is_callable($plugin)) {
                throw new \InvalidArgumentException(\Breeze\Errors\INVALID_CLOSURE_ERROR);
            } elseif (!$name) {
                throw new \InvalidArgumentException(NO_NAME_ERROR);
            }

            self::$_plugins[$name] = $plugin;
        }

        /**
         * Removes a registered plugin.
         *
         * @access public
         * @param  string $name  The name of the plugin to unregister.
         * @return void
         */
        public static function unregister($name)
        {
            if (isset(self::$_plugins[$name])) {
                unset(self::$_plugins[$name]);
            }
        }

        /**
         * Clears all plugins except the ones specified by the $whitelist
         * parameter.
         *
         * @access public
         * @param  array $whitelist  The plugins that should not be cleared
         * @return void
         */
        public static function clearPlugins(array $whitelist = array())
        {
            self::$_plugins = array_intersect_key(self::$_plugins, array_fill_keys($whitelist, null));
        }

        /**
         * Adds a user-defined helper.
         *
         * @access public
         * @param  string $name     The name of the helper to add.
         * @param  Closure $helper  The helper to add.
         * @return void
         */
        public function helper($name, $helper)
        {
            $this->_user_helpers->add($name, $helper, ClosuresCollection::VALIDATE_LABEL);
        }

        /**
         * Gets a list of all defined helpers.
         *
         * @access public
         * @return array  All defined helpers.
         */
        public function getHelpers()
        {
            return array_merge(array_keys($this->_user_helpers->all()), self::$_core_helpers);
        }

        /**
         * Adds a filter to be executed before the request is routed.
         *
         * @access public
         * @param  Closure $filter  The filter to add.
         * @return void
         */
        public function before($filter)
        {
            $this->_filters[self::BEFORE]->add($filter);
        }

        /**
         * Adds a filter to be executed after the request is routed.
         *
         * @access public
         * @param  Closure $filter  The filter to add.
         * @return void
         */
        public function after($filter)
        {
            $this->_filters[self::AFTER]->add($filter);
        }

        /**
         * Executes the filters specified by the $type argument.
         *
         * @access public
         * @param  string $type  The type of filter to execute.
         * @return void
         * @throws InvalidArgumentException
         */
        public function filter($type)
        {
            if (!in_array($type, array(self::BEFORE,self::AFTER))) {
                throw new \InvalidArgumentException(sprintf(self::INVALID_FILTER_ERROR, $type));
            }

            foreach ((array)$this->_filters[$type]->all() as $filter) {
                $filter($this);
            }
        }

        /**
         * Process the incoming request using the programmer-defined routes.
         *
         * @access public
         * @param  string $request_method  An optional request method to spoof the incoming request method.
         * @param  string $request_uri     An optional URI to spoof the incoming request URI.
         * @return void
         */
        public function run($request_method = null, $request_uri = null)
        {
            $this->filter(self::BEFORE);
            try {
                $this->_dispatcher->dispatch($request_method, $request_uri);
            } catch (\Exception $exception) {
                $this->_error_handler->dispatchError($exception);
            }
            $this->filter(self::AFTER);
        }
    }
}