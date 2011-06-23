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
 * @package    Breeze
 * @subpackage Application
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */

namespace Breeze\View\Driver;

/**
 * @see Breeze\Application
 */
use Breeze\Application;

/**
 * This is the standard template engine interface for the Breeze
 * Framework.  New template engine extensions are required to implement
 * this interface.
 *
 * @package    Breeze
 * @subpackage View
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */
interface DriverInterface
{
    /**
     * Sets up the templates directory path and the extra options for a
     * database engine.  The extra options are to be defined by the
     * specific engines.
     *
     * @param Breeze\Application $application A Breeze Application
     * @param string             $path        The path to the templates
     * directory
     * @param array              $options     Extra options for custom engines
     *
     * @return void
     */
    public function __construct(Application $application, $path = null,
        array $options = array());

    /**
     * Sets the path for the base templates directory.
     *
     * @param string $path The path to the templates directory
     *
     * @return void
     */
    public function setPath($path);

    /**
     * Sets the extra options for the template engine.
     *
     * @param array $options Extra options for setting up custom template
     * engines
     *
     * @return void
     */
    public function setOptions(array $options);

    /**
     * Gets a template engine option.
     *
     * @param string $option  The name of the option to get
     * @param mixed  $default A fallback value if the option hasn't been
     * specified.
     *
     * @return mixed
     */
    public function getOption($option, $default = null);

    /**
     * Updates the template engine if changes to the template-related
     * configurations have changed.
     *
     * @return void
     */
    public function updateConfig();

    /**
     * Gets the full path to the templates directory.
     *
     * @return string The full path to the templates directory.
     */
    public function getPath();

    /**
     * Checks if a template exists.
     *
     * @param string $template The path to the template, excluding the base
     * templates directory.
     *
     * @return boolean If the template exists.
     */
    public function templateExists($template);

    /**
     * Gets the full path to a template, including the base templates
     * directory.
     *
     * @param string $template The path to the template, excluding the base
     * templates directory.
     *
     * @return string The full path to a template.
     */
    public function getTemplatePath($template);

    /**
     * Renders a template using the $variables parameter and returns
     * the contents.
     *
     * @param string $template  The path to the template, excluding the base
     * templates directory.
     * @param array  $variables An associative array of variables to use in the
     * template.
     *
     * @return string The rendered template.
     */
    public function fetch($template, array $variables = array());
}

/**
 * This is the base implementation of the
 * {@link Breeze\View\Driver\DriverInterface} interface.  You should extend
 * this if you're developing a template engine extension as this class does
 * most of the work for you.
 *
 * @package    Breeze
 * @subpackage View
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
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
     * @var string
     */
    protected $_path;

    /**
     * The real path to the base templates directory.
     *
     * @var string
     */
    protected $_realPath;

    /**
     * The extra options for the template engine.
     *
     * @var array
     */
    protected $_options = array();

    /**
     * An instance of the base Breeze Framework class.
     *
     * @var Breeze\Application
     */
    protected $_application;

    /**
     * Sets up the templates directory path and the extra options for a
     * database engine.  The extra options are to be defined by the
     * specific engines.
     *
     * @param Breeze\Application $application A Breeze Application
     * @param string             $path        The path to the templates
     * directory
     * @param array              $options     Extra options for custom engines
     *
     * @return void
     */
    public function __construct(Application $application, $path = null,
        array $options = array())
    {
        $this->setApplication($application);

        if (!empty($options)) {
            $this->setOptions($options);
        }

        if (!empty($path)) {
            $this->setPath($path);
        }

        $this->_config();
    }

    /**
     * Sets the Breeze application.
     *
     * @param Breeze\Application $application A Breeze Application
     *
     * @return void
     */
    public function setApplication(Application $application)
    {
        $this->_application = $application;
    }

    /**
     * Sets the path for the base templates directory.
     *
     * @param string $path The path to the templates directory
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function setPath($path)
    {
        if (!is_readable($path)) {
            throw new \InvalidArgumentException(
                sprintf(self::INVALID_PATH_ERROR, $path)
            );
        }

        $this->_realPath = realpath($path);
        $this->_path = $path;
    }

    /**
     * Sets the extra options for the template engine.
     *
     * @param array $options Extra options for setting up custom template
     * engines
     *
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->_options = $options;
    }

    /**
     * Gets a template engine option.
     *
     * @param string $option  The name of the option to get
     * @param mixed  $default A fallback value if the option hasn't been
     * specified.
     *
     * @return mixed
     */
    public function getOption($option, $default = null)
    {
        return isset($this->_options[$option]) ?
           $this->_options[$option] : $default;
    }

    /**
     * Gets the full path to the templates directory.
     *
     * @return string The full path to the templates directory.
     */
    public function getPath()
    {
        return $this->_realPath;
    }

    /**
     * Checks if a template exists.
     *
     * @param string $template The path to the template, excluding the base
     * templates directory.
     *
     * @return boolean If a template exists.
     */
    public function templateExists($template)
    {
        return is_readable($this->getTemplatePath($template));
    }

    /**
     * Gets the full path to a template, including the base templates
     * directory.
     *
     * @param string $template The path to the template, excluding the base
     * templates directory.
     *
     * @return string The full path to a template.
     */
    public function getTemplatePath($template)
    {
        return $this->getPath() . "/$template";
    }

    /**
     * Renders a template using the $variables parameter and returns
     * the contents.
     *
     * @param string $template  The path to the template, excluding the base
     * templates directory.
     * @param array  $variables An associative array of variables to use in the
     * template.
     *
     * @return string The rendered template.
     * @throws InvalidArgumentException
     */
    public function fetch($template, array $variables = array())
    {
        if (!$this->templateExists($template)) {
            throw new \InvalidArgumentException(
                sprintf(self::INVALID_TEMPLATE_ERROR, $template)
            );
        }

        return $this->_fetchTemplate($template, $variables);
    }

    /**
     * Updates the template engine if changes to the template-related
     * configurations have changed.
     *
     * @return void
     */
    public function updateConfig()
    {
        $path = $this->_application->config('template_directory');
        $options = $this->_application->config('template_options');

        if ($path != $this->_path || $options != $this->_options) {
            $this->_config();
        }
    }

    /**
     * Sets up the internal template engine structures.  This is intended
     * to be where engine specific options are set up.
     *
     * @return void
     */
    abstract protected function _config();

    /**
     * Renders a template using the $variables parameter and returns
     * the contents.  This is sole function engines must implement.
     *
     * @param string $template  The path to the template, excluding the base
     * templates directory.
     * @param array  $variables An associative array of variables to use in the
     * template.
     *
     * @return string The rendered template
     */
    abstract protected function _fetchTemplate($template,
        array $variables = array());
}

/**
 * The PHP-based template engine for the Breeze Framework.
 *
 * @package    Breeze
 * @subpackage View
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */
class Php extends Driver
{
    /**
     * Sets up the internal template engine structures.  This is intended
     * to be where engine specific options are set up.
     *
     * @return void
     */
    protected function _config()
    {
    }

    /**
     * Renders a template using the $variables parameter and returns
     * the contents.
     *
     * @param string $template  The path to the template, excluding the base
     * templates directory.
     * @param array  $variables An associative array of variables to use in the
     * template.
     *
     * @return string The rendered template.
     */
    protected function _fetchTemplate($template, array $variables = array())
    {
        extract($variables);
        ob_start();
        require $this->getTemplatePath($template);
        return ob_get_clean();
    }
}


namespace Breeze\View;

/**
 * @see Breeze\Application
 */
use Breeze\Application;

/**
 * The base view class which wraps an instance of
 * {@link Breeze\View\Driver\DriverInterface} to provide unified access to
 * common template routines regardless of the underlining engine
 * implementation.
 *
 * @package    Breeze
 * @subpackage View
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
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
     * @var Breeze\Application
     */
    protected $_application;
    /**
     * Variables to use in the templates.
     *
     * @var array
     */
    protected $_templateVariables = array();
    /**
     * The current template engine.
     *
     * @var Breeze\View\Driver\DriverInterface
     */
    protected $_engine = null;

    /**
     * Creates a new View.
     *
     * @param Breeze\Application $application A Breeze application
     *
     * @return void
     */
    public function __construct(Application $application)
    {
        $this->setApplication($application);
    }

    /**
     * Sets the Breeze application.
     *
     * @param Breeze\Application $application A Breeze Application
     *
     * @return void
     */
    public function setApplication(Application $application)
    {
        $this->_application = $application;

        if ($this->_engine) {
            $this->_engine->setApplication($application);
        }
    }

    /**
     * Clones the current view, making a deep copy of the current engine.
     *
     * @return void
     */
    public function __clone()
    {
        if ($this->_engine) {
            $this->_engine = clone $this->_engine;
        }
    }

    /**
     * Sets a template variable value.
     *
     * @param string $name  The name of the template variable.
     * @param mixed  $value The value of the template variable.
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->_templateVariables[$name] = $value;
    }

    /**
     * Gets a template variable value.
     *
     * @param string $name The name of the template variable.
     *
     * @return mixed The template variable value.
     */
    public function __get($name)
    {
        return isset($this->_templateVariables[$name]) ?
            $this->_templateVariables[$name] : null;
    }

    /**
     * Checks if a template variable is set.
     *
     * @param string $name The name of the template variable.
     *
     * @return boolean If a template variable is set.
     */
    public function __isset($name)
    {
        return isset($this->_templateVariables[$name]);
    }

    /**
     * Unsets a template variable.
     *
     * @param string $name The name of the template variable.
     *
     * @return void
     */
    public function __unset($name)
    {
        unset($this->_templateVariables[$name]);
    }

    /**
     * Adds template variables using an array.
     *
     * @param array $variables The variables to add to the template.
     *
     * @return void
     */
    public function addVariables(array $variables)
    {
        $this->_templateVariables = array_merge(
            $this->_templateVariables, $variables
        );
    }

    /**
     * Fetches the contents of the template specified by the $template argument
     * using the variables specified in the
     * {@link Breeze\View\View::$_templateVariables} instance variable.
     *
     * @param string $template  The path to the template, excluding the base
     * templates directory and extension.
     * @param array  $variables The variables to add to the template.
     *
     * @return string The rendered template contents.
     */
    public function fetch($template, array $variables = array())
    {
        if (!empty($variables)) {
            $this->addVariables($variables);
        }

        $this->__set(
            $this->_application->config('application_variable'),
            $this->_application
        );

        $contents = $this->getEngine()->fetch(
            $template . $this->_application->config('template_extension'),
            $this->_templateVariables
        );

        return $this->layoutExists() ?
            $this->fetchLayout($contents) : $contents;
    }

    /**
     * Returns the $contents parameter wrapped with the the current layout
     * file.
     *
     * @param string $contents The contents to wrap with a layout
     *
     * @return string The rendered layout with the provided contents.
     */
    public function fetchLayout($contents)
    {
        $layout = $this->_application->config('template_layout') .
            $this->_application->config('template_extension');

        return $this->getEngine()->fetch(
            $layout,
            array_merge(
                $this->_templateVariables,
                array('layout_contents'=>$contents)
            )
        );
    }

    /**
     * Checks to see if a layout is defined and the layout file exists.
     *
     * @return boolean If a layout is defined and exists.
     */
    public function layoutExists()
    {
        $layoutPath = $this->_application->config('template_layout');

        return $layoutPath && $this->getEngine()->templateExists(
            $layoutPath . $this->_application->config('template_extension')
        );
    }

    /**
     * Displays the contents of the template specified by the $template
     * argument using the variables specified in the
     * {@link Breeze\View\View::$_templateVariables} instance variable.
     *
     * @param string $template  The path to the template, excluding the base
     * templates directory and extension.
     * @param array  $variables The variables to add to the template.
     *
     * @return void
     */
    public function display($template, array $variables = array())
    {
        echo $this->fetch($template, $variables);
    }

    /**
     * Fetches the contents of the template specified by the $template argument
     * using the variables specified in the
     * {@link Breeze\View\View::$_templateVariables} instance variable.
     *
     * NOTE: This works just like {@link Breeze\View\View::fetch()} but it
     * ignores the layout preferences.
     *
     * @param string $template  The path to the template, excluding the base
     * templates directory and extension.
     * @param array  $variables The variables to add to the template.
     *
     * @return string The rendered template.
     */
    public function partial($template, array $variables = array())
    {
        $oldLayout = $this->_application->config('template_layout');
        $this->_application->config('template_layout', false);

        $return = $this->fetch($template, $variables);
        $this->_application->config('template_layout', $oldLayout);

        return $return;
    }

    /**
     * Specifies the path to the layouts directory.  Pass the boolean 'FALSE'
     * constant to bypass
     * layouts completely.
     *
     * @param string $layout The path to the layout, excluding the base
     * templates directory.
     *
     * @return void
     */
    public function layout($layout)
    {
        $this->_application->config('template_layout', $layout);
    }

    /**
     * Using the current application configurations, retrieves an instance of
     * the specified template engine.
     *
     * @return Breeze\View\Driver\DriverInterface The template engine.
     * @throws UnexpectedValueException
     */
    public function getEngine()
    {
        $engine = $this->_application->config('template_engine');

        if (is_object($engine)) {
            $this->_setEngineWithObject($engine);
        } else {
            $configClass = __NAMESPACE__ . '\\Driver\\' . $engine;
            $engineClass = get_class((object)$this->_engine);

            if (strtolower($engineClass) != strtolower($configClass)) {
                $this->_getEngineWithString($configClass);
            } else {
                $this->_engine->updateConfig();
            }
        }

        return $this->_engine;
    }

    /**
     * Retrieves the current instance of the specified template engine.
     *
     * @return Breeze\View\Driver\DriverInterface The template engine.
     * @throws UnexpectedValueException
     */
    public function getCurrentEngine()
    {
        return $this->_engine;
    }

    /**
     * Sets the current template engine with an engine object.
     *
     * @param Breeze\View\Driver\DriverInterface $engine The template engine to
     * set.
     *
     * @return void
     */
    protected function _setEngineWithObject(Driver\DriverInterface $engine)
    {
        $this->_engine = $engine;
    }

    /**
     * Sets the current template engine with a engine class name.
     *
     * @param Breeze\View\Driver\DriverInterface $engineClass The template
     * engine to set.
     *
     * @return void
     * @throws UnexpectedValueException
     */
    protected function _getEngineWithString($engineClass)
    {
        $baseInterface = __NAMESPACE__ . '\\Driver\\DriverInterface';
        if (!class_exists($engineClass)
            || !in_array($baseInterface, class_implements($engineClass))
        ) {
            throw new \UnexpectedValueException(
                sprintf(self::INVALID_TEMPLATE_ENGINE_ERROR, $engineClass)
            );
        }

        $this->_engine = new $engineClass(
            $this->_application,
            $this->_application->config('template_directory'),
            $this->_application->config('template_options')
        );
    }
}


namespace Breeze\Errors;

/**
 * @see Breeze\Application
 */
use Breeze\Application;
/**
 * @see Breeze\ClosuresCollection
 */
use Breeze\ClosuresCollection;
/**
 * @see Breeze\Dispatcher\EndRequestException
 */
use Breeze\Dispatcher\EndRequestException;

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
 * The base exception class for all other Breeze Framework exceptions to
 * extend.
 *
 * @package    Breeze
 * @subpackage Errors
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */
class Exception extends \Exception
{
}

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
 *
 *     // Echos "Page not found!"
 *     $handler->dispatchError(new Breeze\Dispatcher\NotFoundException());
 * @endcode
 *
 * @package    Breeze
 * @subpackage Errors
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */
class Errors extends ClosuresCollection
{
    /**
     * Error provided if a user tries to dispatch an error that is
     * not a string or an Exception.
     */
    const INVALID_ERROR = 'Errors must be a string or a valid Exception';

    /**
     * A generic error message used if a user doesn't provide a code
     * or message.
     */
    const GENERIC_ERROR = 'An Error Occurred';

    /**
     * The default closure to use for all errors with no defined handler.
     *
     * @var Closure
     */
    protected $_defaultError;
    /**
     * An instance of the base Breeze Framework class for passing into
     * closures.
     *
     * @var Breeze\Application
     */
    protected $_application;

    /**
     * Sets up the {@link Breeze\Errors\Errors::$_defaultError} instance
     * variable for handling errors with no defined handler.
     *
     * @param Breeze\Application $application A Breeze application
     *
     * @return void
     */
    public function __construct(Application $application)
    {
        $this->setApplication($application);
        $this->_defaultError = function(Application $application,
            \Exception $exception)
        {

            $body = sprintf("<h1>%s</h1>", $exception->getMessage());
            if ($application->config('errors_backtrace')) {
                $body .= sprintf(
                    '<pre><code>%s</code></pre>',
                    $exception->getTraceAsString()
                );
            }

            if ($application->layoutExists()) {
                echo $application->fetchLayout($body);
            } else {
                echo '<!DOCTYPE html><html><head>' .
                     '<title>' . Errors::GENERIC_ERROR . '</title>' .
                     '</head><body>' . $body . '</body></html>';
            }

        };
    }

    /**
     * Sets the Breeze application.
     *
     * @param Breeze\Application $application A Breeze Application
     *
     * @return void
     */
    public function setApplication(Application $application)
    {
        $this->_application = $application;
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
     * @param mixed   $names   The codes/exceptions to add errors for.
     * @param Closure $handler The handler for the error.
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function add($names, $handler = null, $validate = null)
    {
        if (!isset($handler) && is_callable($names)) {
            $this->_defaultError = $names;
        } else {
            parent::add($names, $handler, self::VALIDATE_NAME);
        }
    }

    /**
     * Dispatches the closure associated with the $exception argument, falling
     * back to the {@link Breeze\Errors\Errors::$_defaultError} instance
     * variable if no closure can been found.
     *
     * @param mixed $exception The exception to dispatch an error for.
     * @param mixed $code      The error code for the exception if $exception
     * is a message.
     *
     * @return void
     */
    public function dispatchError($exception = null, $code = null)
    {
        if (is_object($exception)) {
            if (!$exception instanceof \Exception) {
                throw new \InvalidArgumentException(self::INVALID_ERROR);
            }

            $this->_setStatus($exception->getCode());
        } else {
            $status = $this->_setStatus($code);
            $exception = $this->_getException($exception, $code, $status);
        }

        if (!($function = $this->get($exception->getCode()))
            && !($function = $this->get(get_class($exception)))
        ) {
            $function = $this->_defaultError;
        }

        $function($this->_application, $exception);
        throw new EndRequestException();
    }

    /**
     * Sets the HTTP status code if the error code corresponds with an HTTP
     * error status code.
     *
     * @param mixed $code The error code.
     *
     * @return string|null The status message if available.
     */
    protected function _setStatus($code)
    {
        if ($code >= 400 && $code <= 417 || $code >= 500 && $code <= 505) {
            return preg_replace(
                '/^.+? (\d+) (.+)$/',
                '$1 - $2',
                $this->_application->status($code)
            );
        }
    }

    /**
     * Gets an Exception given a message and a code.
     *
     * @param string  $message  The exception message
     * @param integer $code     The exception code
     * @param mixed   $fallback A fallback message if $message is not
     * available.
     *
     * @return Exception
     */
    protected function _getException($message, $code, $fallback)
    {
        if (!$message) {
            $message = $fallback ?: self::GENERIC_ERROR;
        }

        return new \Exception($message, $code);
    }
}


namespace Breeze\Dispatcher;

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
 * @package    Breeze
 * @subpackage Dispatcher
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */
class PassException extends Exception
{
}

/**
 * Internal exception used to end a request in lieu of exit().
 *
 * @package    Breeze
 * @subpackage Dispatcher
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */
class EndRequestException extends Exception
{
}

/**
 * The "Not Found" exception used to indicate a HTTP 404 error.
 *
 * @package    Breeze
 * @subpackage Dispatcher
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */
class NotFoundException extends Exception
{
    /**
     * Constructor.
     *
     * @param string $message The exception message.
     *
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
 * @package    Breeze
 * @subpackage Dispatcher
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
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
     * @return void
     */
    public function __construct()
    {
        $this->add(
            'user_agent_matches',
            function($pattern)
            {
                return (bool) preg_match(
                    $pattern, $_SERVER['HTTP_USER_AGENT']
                );
            }
        );

        $this->add(
            'host_name_is',
            function($pattern)
            {
                return $pattern === $_SERVER['HTTP_HOST'];
            }
        );
    }

    /**
     * Adds a new condition.
     *
     * @param string|array $names     A list of names to associate with the
     * provided closure
     * @param Closure      $condition The condition to add.
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function add($names, $condition = null, $validate = null)
    {
        parent::add($names, $condition, self::VALIDATE_NAME);
    }

    /**
     * Checks a condition, throwing a Breeze\Dispatcher\PassException if the
     * condition is not met.
     *
     * @param string $name The condition to dispatch
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws PassException
     */
    public function dispatchCondition($name)
    {
        if (!($condition = $this->get($name))) {
            throw new \InvalidArgumentException(
                sprintf(self::INVALID_CONDITION_ERROR, $name)
            );
        } elseif (!call_user_func_array(
            $condition,
            array_slice(func_get_args(), 1)
        )) {
            throw new PassException();
        }
    }
}

/**
 * HTTP status wrapper for building and dispatching the HTTP status header.
 *
 * @code
 *     // header('HTTP/1.1 404 Not Found');
 *     $status = new Breeze\Dispatcher\Dispatcher(404);
 *     $status->send();
 *
 *     // header('HTTP/1.0 200 OK');
 *     $status = new Breeze\Dispatcher\Dispatcher(200, '1.0');
 *     $status->send();
 * @endcode
 *
 * @package    Breeze
 * @subpackage Dispatcher
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */
class Status
{
    /**
     * Standard HTTP response codes
     */
    const HTTP_100 = 'Continue';
    const HTTP_101 = 'Switching Protocols';
    const HTTP_200 = 'OK';
    const HTTP_201 = 'Created';
    const HTTP_202 = 'Accepted';
    const HTTP_203 = 'Non-Authoritative Information';
    const HTTP_204 = 'No Content';
    const HTTP_205 = 'Reset Content';
    const HTTP_206 = 'Partial Content';
    const HTTP_300 = 'Multiple Choices';
    const HTTP_301 = 'Moved Permanently';
    const HTTP_302 = 'Found';
    const HTTP_303 = 'See Other';
    const HTTP_304 = 'Not Modified';
    const HTTP_305 = 'Use Proxy';
    const HTTP_307 = 'Temporary Redirect';
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
     * The status code for the HTTP response.
     *
     * @var integer
     */
    protected $_statusCode = 200;
    /**
     * The status message for the HTTP response.
     *
     * @var string
     */
    protected $_statusMessage = 'OK';
    /**
     * The protocol version for the HTTP response.
     *
     * @var string
     */
    protected $_httpVersion = '1.1';

    /**
     * Creates a new HTTP Status.
     *
     * @param integer $statusCode  The status code for the HTTP response.
     * @param string  $httpVersion The protocol version for the HTTP response.
     *
     * @return void
     */
    public function __construct($statusCode = null, $httpVersion = null)
    {
        if ($statusCode) {
            $this->set($statusCode, $httpVersion);
        }
    }

    /**
     * Sets a new HTTP status.
     *
     * @param integer $statusCode  The status code for the HTTP response.
     * @param string  $httpVersion The protocol version for the HTTP response.
     *
     * @return void
     */
    public function set($statusCode, $httpVersion = null)
    {
        if ($httpVersion) {
            $this->_httpVersion = $httpVersion;
        }

        $constant = 'self::HTTP_' . $statusCode;
        if (defined($constant)) {
            $this->_statusCode = $statusCode;
            $this->_statusMessage = constant($constant);
        }
    }

    /**
     * Sends an HTTP Status header.
     *
     * @return void
     */
    public function send()
    {
        if (!headers_sent()) {
            header($this);
        }
    }

    /**
     * Gets the string representation of the current status.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            'HTTP/%s %s %s',
            $this->_httpVersion,
            $this->_statusCode,
            $this->_statusMessage
        );
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
 * @package    Breeze
 * @subpackage Dispatcher
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
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
     * @var array
     */
    protected static $_supportedMethods = array('GET','POST','PUT','DELETE');

    /**
     * An instance of the base Breeze Framework class for querying
     * configurations.
     *
     * @var Breeze\Application
     */
    protected $_application;
    /**
     * The URI to use for routing.
     *
     * @var string
     */
    protected $_requestUri;
    /**
     * The HTTP method to use for routing.
     *
     * @var string
     */
    protected $_requestMethod = 'GET';
    /**
     * A collection of user-defined request handlers.
     *
     * @var array
     */
    protected $_routes;

    /**
     * Creates a new dispatcher for routing end-user requests.
     *
     * @param Breeze\Application $application A Breeze application
     *
     * @return void
     */
    public function __construct(Application $application)
    {
        $this->setApplication($application);

        foreach (self::$_supportedMethods as $method) {
            $this->_routes[$method] = array();
        }
    }

    /**
     * Sets the Breeze application.
     *
     * @param Breeze\Application $application A Breeze Application
     *
     * @return void
     */
    public function setApplication(Application $application)
    {
        $this->_application = $application;
    }

    /**
     * Provides access to the get(), post(), etc. methods that are
     * essentially just aliases of the
     * {@link Breeze\Dispatcher\Dispatcher::route()} method.
     *
     * @param string $name      The name of the method
     * @param array  $arguments The method arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $name = strtoupper($name);

        if (in_array($name, self::$_supportedMethods)) {
            return call_user_func_array(
                array($this, '_addRoute'),
                array_merge(array($name), (array)$arguments)
            );
        } elseif ($name == 'ANY') {
            if (isset($arguments[1]) && is_callable($arguments[1])) {
                $arguments = array_merge(
                    array(self::$_supportedMethods), (array)$arguments
                );
            }

            return call_user_func_array(array($this, '_addRoute'), $arguments);
        }

        throw new \BadMethodCallException(
            sprintf(\Breeze\Errors\UNDEFINED_FUNCTION, get_class($this), $name)
        );
    }

    /**
     * Adds a route that will be checked when routing requests using the
     * {@link Breeze\Dispatcher\Dispatcher::dispatch()} method.
     *
     * @param string|array $methods The HTTP method that must match
     * @param string       $pattern The pattern the requested URI must match
     * @param Closure      $handler The handler for the matching request
     *
     * @return void
     * @throws InvalidArgumentException
     */
    protected function _addRoute($methods, $pattern, $handler)
    {
        foreach ((array)$methods as $method) {
            if (!is_callable($handler)) {
                throw new \InvalidArgumentException(
                    \Breeze\Errors\INVALID_CLOSURE_ERROR
                );
            } elseif (!$pattern) {
                throw new \InvalidArgumentException(self::NO_PATTERN_ERROR);
            }

            $this->_routes[$method][] = array(
                'pattern'=>$pattern,
                'handler'=>$handler
            );
        }
    }

    /**
     * Checks all routes added with
     * {@link Breeze\Dispatcher\Dispatcher::route()} and calls the
     * corresponding handler if a matching request is found. Otherwise a
     * {@link Breeze\Dispatcher\NotFoundException} exception is thrown.
     *
     * @param string $requestMethod An optional request method to spoof the
     * incoming request method.
     * @param string $requestUri    An optional URI to spoof the incoming
     * request URI.
     *
     * @return void
     * @throws Breeze\Dispatcher\NotFoundException
     */
    public function dispatch($requestMethod = null, $requestUri = null)
    {
        $this->setRequestUri($requestUri);
        $this->setRequestMethod($requestMethod);

        if (isset($this->_routes[$this->_requestMethod])) {
            $filters = $this->_application->getRouteFilters();
            foreach ($this->_routes[$this->_requestMethod] as $route) {
                $pattern = $route['pattern'];
                foreach ($filters as $filter) {
                    $pattern = $filter($pattern);
                }
                try {
                    if ($pattern{0} != '/') {
                        $this->_processRegexpRoute(
                            $pattern,
                            $route['handler']
                        );
                    } else {
                        $this->_processRoute(
                            $pattern, $route['handler']
                        );
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
     * Invokes the $handler if the current value of
     * {@link Breeze\Dispatcher\Dispatcher::$_requestUri} matches the $pattern.
     * Otherwise a {@link Breeze\Dispatcher\PassException} exception is thrown.
     *
     * @param string  $pattern The pattern the requested URI must match
     * @param Closure $handler The handler for the matching request
     *
     * @return void
     * @throws Breeze\Dispatcher\PassException
     */
    protected function _processRoute($pattern, $handler)
    {
        if ($this->_requestUri != $pattern) {
            throw new PassException();
        }

        $handler($this->_application);
    }

    /**
     * Invokes the $handler if the current value of
     * {@link Breeze\Dispatcher\Dispatcher::$_requestUri} matches the regexp
     * $pattern.  Otherwise a {@link Breeze\Dispatcher\PassException} exception
     * is thrown.
     *
     * @param string  $pattern The pattern the requested URI must match
     * @param Closure $handler The handler for the matching request
     *
     * @return void
     * @throws Breeze\Dispatcher\PassException
     */
    protected function _processRegexpRoute($pattern, $handler)
    {
        if (!preg_match($pattern, $this->_requestUri, $matches)) {
            throw new PassException();
        }

        $handler($this->_application, $matches);
    }

    /**
     * Sets (and normalizes) the request URI.  $_SERVER['REQUEST_URI'] is used
     * by default if $requestUri is not provided.
     *
     * @param string $requestUri The request URI to use.
     *
     * @return void
     */
    public function setRequestUri($requestUri = null)
    {
        if ($requestUri === null) {
            if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
                $this->_requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
            } elseif (isset($_SERVER['IIS_WasUrlRewritten'])
                && $_SERVER['IIS_WasUrlRewritten'] == '1'
                && isset($_SERVER['UNENCODED_URL'])
                && $_SERVER['UNENCODED_URL'] != ''
            ) {
                $this->_requestUri = $_SERVER['UNENCODED_URL'];
            } elseif (isset($_SERVER['REQUEST_URI'])) {
                $this->_requestUri = $_SERVER['REQUEST_URI'];
            } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
                $this->_requestUri = $_SERVER['ORIG_PATH_INFO'];
            }
        } else {
            $this->_requestUri = $requestUri;
        }

        $this->_requestUri = parse_url($this->_requestUri, PHP_URL_PATH);
        if ($this->_requestUri == '') {
            $this->_requestUri = '/';
        }
        if ($this->_requestUri != '/'
            && substr($this->_requestUri, -1) == '/') {
            $this->_requestUri = substr($this->_requestUri, 0, -1);
        }
    }

    /**
     * Gets the currently set request URI.
     *
     * @return string
     */
    public function getRequestUri()
    {
        return $this->_requestUri;
    }

    /**
     * Sets (and normalizes) the request method.  $_SERVER['REQUEST_METHOD'] is
     * used by default if $requestMethod is not provided.
     *
     * @param string $requestMethod The request method to use.
     *
     * @return void
     */
    public function setRequestMethod($requestMethod = null)
    {
        if ($requestMethod === null) {
            if (isset($_ENV['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
                $this->_requestMethod = $_ENV['HTTP_X_HTTP_METHOD_OVERRIDE'];
            } elseif (isset($_SERVER['REQUEST_METHOD'])) {
                $this->_requestMethod = $_SERVER['REQUEST_METHOD'];
            }
        } else {
            $this->_requestMethod = $requestMethod;
        }

        $this->_requestMethod = strtoupper($this->_requestMethod);
        if ($this->_requestMethod == 'POST' && isset($_POST['_method'])) {
            $this->_requestMethod = strtoupper($_POST['_method']);
        } elseif ($this->_requestMethod == 'HEAD') {
            $this->_requestMethod = 'GET';
        }
    }

    /**
     * Gets the currently set request method.
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->_requestMethod;
    }
}


namespace Breeze;

/**
 * @see Breeze\Dispatcher\PassException
 */
use Breeze\Dispatcher\PassException;
/**
 * @see Breeze\Dispatcher\EndRequestException
 */
use Breeze\Dispatcher\EndRequestException;

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
 * @package    Breeze
 * @subpackage Application
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
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
     * @var array
     */
    protected $_namedClosures = array();
    /**
     * Collection of unnamed closures.
     *
     * @var array
     */
    protected $_closures = array();

    /**
     * Associates a closure with a single name or a group of names.
     *
     * @param string|array $names    A list of names to associate with the
     * provided closure
     * @param Closure      $closure  The closure to add
     * @param string       $validate A flag to indicate what type of validation
     * to do on closure names.
     *
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
            throw new \InvalidArgumentException(
                \Breeze\Errors\INVALID_CLOSURE_ERROR
            );
        }

        foreach ((array)$names as $name) {
            if (empty($name)) {
                if (!empty($validate)) {
                    throw new \InvalidArgumentException(NO_NAME_ERROR);
                }

                $this->_closures[] = $closure;
            } else {
                if ($validate == self::VALIDATE_LABEL
                    && !preg_match(self::VALID_LABEL, $name)
                ) {
                    throw new \InvalidArgumentException(
                        sprintf(self::INVALID_LABEL_ERROR, $name)
                    );
                }

                $this->_namedClosures[$name] = $closure;
            }
        }
    }

    /**
     * Checks if a $name has any closures associated with it.
     *
     * @param string $name The name to check.
     *
     * @return boolean If $name has any closures associated with it.
     */
    public function has($name)
    {
        return isset($this->_namedClosures[$name]);
    }

    /**
     * Gets the closure associated with a specified $name.
     *
     * @param string $name The name to to get the closure for.
     *
     * @return mixed The closure associated with $name.
     */
    public function get($name)
    {
        return $this->has($name) ? $this->_namedClosures[$name] : null;
    }

    /**
     * Returns all defined closures.
     *
     * @return array All defined closures.
     */
    public function all()
    {
        return array_merge($this->_closures, $this->_namedClosures);
    }
}

/**
 * The base configurations handler for the Breeze Framework.  This class is
 * used to manage global application settings.
 *
 * @package    Breeze
 * @subpackage Application
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */
class Configurations
{
    /**
     * The application configurations.
     *
     * @var array
     */
    protected $_configurations = array(
        'template_engine'       => 'PHP',
        'template_options'      => array(),
        'template_directory'    => '../views',
        'template_extension'    => '.php',
        'template_layout'       => 'layout',
        'application_variable'  => 'breeze',
        'errors_backtrace'      => true
    );

    /**
     * Initializes the configurations using $options.
     *
     * @param array $options The default options to set
     *
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
     * @param string|array $name  The name of the configuration value to set or
     * an array of values to set.
     * @param mixed        $value The value to set.
     *
     * @return void
     */
    public function set($name, $value = null)
    {
        if (is_array($name)) {
            $this->_configurations = array_merge(
                $this->_configurations, $name
            );
        } else {
            $this->_configurations[$name] = $value;
        }
    }

    /**
     * Gets a configuration value.
     *
     * @param string $name The name of the configuration value to get.
     *
     * @return mixed
     */
    public function get($name)
    {
        return isset($this->_configurations[$name]) ?
            $this->_configurations[$name] : null;
    }
}

/**
 * Base Breeze application class.  This class serves as a facade for the rest
 * of the framework's classes to facilitate the "micro framework" feel while
 * maintaining a more modular, extensible design.
 *
 * @package    Breeze
 * @subpackage Application
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
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
     * Key for before filters.
     */
    const BEFORE = 'before';
    /**
     * Key for after filters.
     */
    const AFTER = 'after';

    /**
     * Collection of instances.
     *
     * IMPORTANT: Currently this is only used to facilitate the shorthand
     * notation and should not be used for any other purpose.
     *
     * @var array
     */
    protected static $_instances = array();
    /**
     * Collection of plugins that are attached to the Base application
     * class at runtime.
     *
     * @var array
     */
    protected static $_plugins = array();
    /**
     * Collection of pre-defined helpers.
     *
     * @var array
     */
    protected static $_coreHelpers = array(
        'get','delete','put','post','any','before','after','config','template',
        'display','fetch','pass','helper','run','error','condition','redirect',
        'partial','status'
    );

    /**
     * Collection of user configurations.
     *
     * @var Breeze\Configurations
     */
    protected $_configurations;
    /**
     * Core view class for delegating to different template engines.
     *
     * @var Breeze\View\View
     */
    protected $_view;
    /**
     * Core dispatcher for routing end-user requests to pre-defined
     * actions.
     *
     * @var Breeze\Dispatcher\Dispatcher
     */
    protected $_dispatcher;
    /**
     * Core conditions manager for forwarding requests that don't
     * meet certain conditions.
     *
     * @var Breeze\Dispatcher\Conditions
     */
    protected $_conditions;
    /**
     * Core error handler for managing errors which are invoked during
     * routing.
     *
     * @var Breeze\Errors\Errors
     */
    protected $_errorHandler;
    /**
     * HTTP status wrapper for managing the HTTP status header for the current
     * request.
     *
     * @var Breeze\Dispatcher\Status
     */
    protected $_status;

    /**
     * User-defined helpers.
     *
     * @var Breeze\ClosuresCollection
     */
    protected $_userHelpers;
    /**
     * User-defined filters.
     *
     * @var Breeze\ClosuresCollection
     */
    protected $_filters = array();
    /**
     * User-defined route filters.
     *
     * @var Breeze\ClosuresCollection
     */
    protected $_routeFilters;

    /**
     * Initializes defined plugins and allows for overriding of default
     * application configurations.
     *
     * @param Breeze\Configurations $configurations Overrides default
     * application configurations.
     *
     * @return void
     */
    public function __construct(Configurations $configurations = null)
    {
        $this->_configurations = isset($configurations) ?
            $configurations : new Configurations();

        $this->_view = $this->_getDependency(
            'view_object', 'Breeze\\View\\View'
        );

        $this->_errorHandler = $this->_getDependency(
            'errors_object', 'Breeze\\Errors\\Errors'
        );
        $this->_dispatcher = $this->_getDependency(
            'dispatcher_object', 'Breeze\\Dispatcher\\Dispatcher'
        );
        $this->_conditions = $this->_getDependency(
            'conditions_object', 'Breeze\\Dispatcher\\Conditions'
        );
        $this->_status = $this->_getDependency(
            'status_object', 'Breeze\\Dispatcher\\Status'
        );

        $this->_userHelpers = $this->_getDependency(
            'helpers_object', 'Breeze\\ClosuresCollection'
        );
        $this->_filters = array(
            self::BEFORE=>$this->_getDependency(
                'before_filters_object', 'Breeze\\ClosuresCollection'
            ),
            self::AFTER=>$this->_getDependency(
                'after_filters_object', 'Breeze\\ClosuresCollection'
            )
        );
        $this->_routeFilters = $this->_getDependency(
            'route_filters_object', 'Breeze\\ClosuresCollection'
        );

        foreach (self::$_plugins as $plugin) {
            $plugin($this);
        }
    }

    /**
     * Gets an instance of the {@link Breeze\Application} object by name.
     *
     * IMPORTANT: Currently this is only used to facilitate the shorthand
     * notation and should not be used for any other purpose.
     *
     * @param string                $name           The name the object is
     * stored under.
     * @param Breeze\Configurations $configurations Overrides default
     * application configurations.
     *
     * @return Breeze\Application
     */
    public function getInstance($name, Configurations $configurations = null)
    {
        if (!isset(self::$_instances[$name])) {
            self::$_instances[$name] = new self($configurations);
        }

        return self::$_instances[$name];
    }

    /**
     * Sets an instance of the {@link Breeze\Application} object by name.
     *
     * IMPORTANT: Currently this is only used to facilitate testing and should
     * not be used for any other purpose.
     *
     * @param string             $name        The name the object is stored
     * under.
     * @param Breeze\Application $application The Breeze application.
     *
     * @return Breeze\Application
     */
    public function setInstance($name, Application $application)
    {
        self::$_instances[$name] = $application;
    }

    /**
     * Clones the Breeze application, making deep copies of all object
     * properties.
     *
     * @return void
     */
    public function __clone()
    {
        $this->_configurations = clone $this->_configurations;
        $this->_view = clone $this->_view;
        $this->_errorHandler = clone $this->_errorHandler;
        $this->_dispatcher = clone $this->_dispatcher;
        $this->_conditions = clone $this->_conditions;
        $this->_status = clone $this->_status;
        $this->_userHelpers = clone $this->_userHelpers;
        $this->_routeFilters = clone $this->_routeFilters;

        $this->_filters[self::BEFORE] = clone $this->_filters[self::BEFORE];
        $this->_filters[self::AFTER] = clone $this->_filters[self::AFTER];
    }

    /**
     * Wrapper for cloning an application object.
     *
     * NOTE: This was created to get around some well-known limitations with
     * the PHPUnit mocking interface.  I will probably fix the issue in PHPUnit
     * sometime soon, so this method's availability is not to be relied upon.
     *
     * @see https://github.com/sebastianbergmann/phpunit-mock-objects/pull/25
     * @see https://github.com/sebastianbergmann/phpunit-mock-objects/issues/47
     *
     * @return Breeze\Application
     */
    public function cloneApplication()
    {
        $clone = clone $this;

        $clone->_errorHandler->setApplication($clone);
        $clone->_dispatcher->setApplication($clone);
        $clone->_view->setApplication($clone);

        return $clone;
    }

    /**
     * Removes an instance of the {@link Breeze\Application} object by name.
     *
     * IMPORTANT: Currently this is only used to facilitate testing and should
     * not be used for any other purpose.
     *
     * @param string $name The name the object is stored under.
     *
     * @return Breeze\Application
     */
    public function removeInstance($name)
    {
        unset(self::$_instances[$name]);
    }

    /**
     * Injects a dependency.  This is mostly intended to help with testing.
     *
     * @param string $key  The configuration key where the dependency is
     * stored.
     * @param string $name The class name for the dependency.
     *
     * @return void
     */
    protected function _getDependency($key, $name)
    {
        if ($dependency = $this->_configurations->get($key)) {
            if (!is_a($dependency, $name)) {
                throw new \UnexpectedValueException(
                    sprintf(
                        self::INVALID_DEPENDENCY_ERROR,
                        get_class($dependency),
                        $name
                    )
                );
            }

            return $dependency;
        }

        $appObjects = array('view_object','errors_object','dispatcher_object');
        return in_array($key, $appObjects) ? new $name($this) : new $name();
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
     *        echo "Made it here because you used Breeze\Application::pass()"
     *     })
     * @endcode
     *
     * @return void
     * @throws Breeze\Dispatcher\PassException
     */
    public function pass()
    {
        throw new PassException();
    }

    /**
     * Shortcut for getting and setting HTTP statuses.
     *
     * @param integer $statusCode  The status code for the HTTP response.
     * @param string  $httpVersion The protocol version for the HTTP response.
     *
     * @return string the current status message
     */
    public function status($statusCode = null, $httpVersion = null)
    {
        if ($statusCode) {
            $this->_status->set($statusCode, $httpVersion);
            $this->_status->send();
        }

        return (string) $this->_status;
    }

    /**
     * Shortcut for making a redirect.
     *
     * @param string  $url  The url to redirect to.
     * @param integer $code The status code for the redirect.
     *
     * @return void
     */
    public function redirect($url, $code = 302)
    {
        header("Location: $url", true, $code);
        $this->_status->set($code);

        throw new EndRequestException();
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
     * @param string $name  The name of the config value to set/get
     * @param mixed  $value The config value to set
     *
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
     *     $app->condition('is cool', function($name) {
     *         return $name == 'jeff'
     *     });
     *
     *     // Throws Breeze\Dispatcher\PassException
     *     $app->condition('is cool', 'someone else');
     * @endcode
     *
     * @param string $name    The name of the condition to set/dispatch
     * @param mixed  $handler The condition handler
     *
     * @return void
     * @throws Breeze\Dispatcher\PassException
     */
    public function condition($name, $handler = null)
    {
        if (is_callable($handler)) {
            return $this->_conditions->add($name, $handler);
        }

        return call_user_func_array(
            array($this->_conditions, 'dispatchCondition'), func_get_args()
        );
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
     * @param string $name  The name of the template value to set/get
     * @param mixed  $value The template value to set
     *
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
     *     $app->error('JeffsException', function(){
     *         echo "Jeff's Exception";
     *     });
     *
     *     // Dispatching errors
     *     $app->error(403)
     *     $app->error(403, "Permission Denied");
     *     $app->error("Permission Denied");
     * @endcode
     *
     * @param mixed $var1 A numeric code, a message, or a closure
     * @param mixed $var2 A message, or a closure
     *
     * @return void
     */
    public function error($code = '', $message = '')
    {
        if (is_callable($code) || is_callable($message)) {
            return call_user_func_array(
                array($this->_errorHandler, 'add'), func_get_args()
            );
        }

        if (!is_numeric($code)) {
            list($message, $code) = array($code, (int) $message);
        }

        return $this->_errorHandler->dispatchError($message, $code);
    }

    /**
     * Delegates methods to their corresponding classes and handles
     * user-defined helpers.
     *
     * @param string $name      The name of the method
     * @param array  $arguments The method arguments
     *
     * @return void
     */
    public function __call($name, $arguments)
    {
        if (in_array(
            $name,
            array('display','fetch','layout','partial',
                  'layoutExists','fetchLayout')
        )) {
            return call_user_func_array(
                array($this->_view, $name), $arguments
            );
        }

        if (in_array($name, array('get','delete','put','post','any'))) {
            if ($name != 'any' && (count($arguments) < 2
                || !is_callable($arguments[1]))
            ) {
                return call_user_func_array(
                    array($this, 'run'), array_merge(array($name), $arguments)
                );
            }
            return call_user_func_array(
                array($this->_dispatcher, $name), $arguments
            );
        }

        if ($this->_userHelpers->has($name)) {
            return call_user_func_array(
                $this->_userHelpers->get($name), $arguments
            );
        }

        throw new \BadMethodCallException(
            sprintf(\Breeze\Errors\UNDEFINED_FUNCTION, get_class($this), $name)
        );
    }

    /**
     * Shortcut for setting a template value.
     *
     * @param string $name  The name of the template value to set
     * @param mixed  $value The template value to set
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->_view->__set($name, $value);
    }

    /**
     * Shortcut for getting a template value
     *
     * @param string $name The name of the template value to get
     *
     * @return mixed The template value
     */
    public function __get($name)
    {
        return $this->_view->__get($name);
    }

    /**
     * Shortcut for checking if a template value is set.
     *
     * @param string $name The name of the template value to check.
     *
     * @return boolean If the variable is set
     */
    public function __isset($name)
    {
        return $this->_view->__isset($name);
    }

    /**
     * Shortcut for unsetting a template value.
     *
     * @param string $name The name of the template value to unset.
     *
     * @return void
     */
    public function __unset($name)
    {
        $this->_view->__unset($name);
    }

    /**
     * Registers a new plugin.
     *
     * @param string  $name   The name of the plugin to register.
     * @param Closure $plugin The plugin to register
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public static function register($name, $plugin)
    {
        if (!is_callable($plugin)) {
            throw new \InvalidArgumentException(
                \Breeze\Errors\INVALID_CLOSURE_ERROR
            );
        } elseif (!$name) {
            throw new \InvalidArgumentException(NO_NAME_ERROR);
        }

        self::$_plugins[$name] = $plugin;
    }

    /**
     * Removes a registered plugin.
     *
     * @param string $name The name of the plugin to unregister.
     *
     * @return void
     */
    public static function unregister($name)
    {
        unset(self::$_plugins[$name]);
    }

    /**
     * Adds a user-defined helper.
     *
     * @param string  $name   The name of the helper to add.
     * @param Closure $helper The helper to add.
     *
     * @return void
     */
    public function helper($name, $helper)
    {
        $this->_userHelpers->add(
            $name, $helper, ClosuresCollection::VALIDATE_LABEL
        );
    }

    /**
     * Gets a list of all defined helpers.
     *
     * @return array All defined helpers.
     */
    public function getHelpers()
    {
        return array_merge($this->getUserHelpers(), self::$_coreHelpers);
    }

    /**
     * Gets a list of all user-defined helpers.
     *
     * @return array All user-defined helpers.
     */
    public function getUserHelpers()
    {
        return array_keys($this->_userHelpers->all());
    }

    /**
     * Gets a list of all user-defined route filters.
     *
     * @return array All user-defined route filters.
     */
    public function getRouteFilters()
    {
        return $this->_routeFilters->all();
    }

    /**
     * Adds a filter to be executed before the request is routed.
     *
     * @param Closure $filter The filter to add.
     *
     * @return void
     */
    public function before($filter)
    {
        $this->_filters[self::BEFORE]->add($filter);
    }

    /**
     * Adds a filter to be executed on each route before evaluating it
     * against the current request.
     *
     * @param Closure $filter The filter to add.
     *
     * @return void
     */
    public function route($filter)
    {
        $this->_routeFilters->add($filter);
    }

    /**
     * Adds a filter to be executed after the request is routed.
     *
     * @param Closure $filter The filter to add.
     *
     * @return void
     */
    public function after($filter)
    {
        $this->_filters[self::AFTER]->add($filter);
    }

    /**
     * Executes the filters specified by the $type argument.
     *
     * @param string $type The type of filter to execute.
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function filter($type)
    {
        if (!in_array($type, array(self::BEFORE,self::AFTER))) {
            throw new \InvalidArgumentException(
                sprintf(self::INVALID_FILTER_ERROR, $type)
            );
        }

        foreach ((array)$this->_filters[$type]->all() as $filter) {
            $filter($this);
        }
    }

    /**
     * Process the incoming request using the programmer-defined routes.
     *
     * @param string $requestMethod An optional request method to spoof the
     * incoming request method.
     * @param string $requestUri    An optional URI to spoof the incoming
     * request URI.
     *
     * @return void
     */
    public function run($requestMethod = null, $requestUri = null)
    {
        try {
            $this->filter(self::BEFORE);
            try {
                $this->_dispatcher->dispatch($requestMethod, $requestUri);
            } catch (\Exception $exception) {
                if ($exception instanceof EndRequestException) {
                    throw $exception;
                }

                $this->_errorHandler->dispatchError($exception);
            }
            $this->filter(self::AFTER);
        } catch (EndRequestException $exception) {
            // Breeze\Dispatcher\EndRequestException is just a shortcut to skip
            // to the end of a Breeze request
        }
    }
}
