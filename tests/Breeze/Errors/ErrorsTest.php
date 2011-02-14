<?php
/**
 * Breeze Framework - Errors test case
 *
 * This file contains the {@link Breeze\Errors\Tests\ErrorsTest} class.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @package    Breeze
 * @subpackage Tests
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */

namespace Breeze\Errors\Tests {

    /**
     * @see Breeze\Errors\Errors
     */
    use Breeze\Errors\Errors;

    /**
     * The test case for the {@link Breeze\Errors\Errors} class.
     *
     * @package    Breeze
     * @subpackage Tests
     * @author     Jeff Welch <whatthejeff@gmail.com>
     * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
     * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
     * @link       http://breezephp.com/
     */
    class ErrorsTest extends \PHPUnit_Extensions_OutputTestCase
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
         * The errors object for testing.
         *
         * @param Breeze\Errors\Errors
         */
        protected $errors;
        /**
         * The application stub for testing {@link Breeze\Errors\Errors}.
         *
         * @param Breeze\Application
         */
        protected $application;
        /**
         * An exception for testing {@link Breeze\Errors\Errors}.
         *
         * @param Exception
         */
        protected $exception;

        /**
         * A sample closure to add to tests.
         *
         * @param Closure
         */
        protected $closure;

        /**
         * Sets up the test case for {@link Breeze\Errors\Errors}.
         *
         * @return void
         */
        public function setUp()
        {
            $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.0';

            $this->application = $this->getMock('Breeze\\Application', array(), array(), '', FALSE);
            $this->exception = new \Exception('test', 403);

            $this->closure = function(){
                echo 'Error Test';
            };

            $this->errors = new Errors($this->application);
            $this->errors->setExit(false);
        }

        /**
         * Tests default error codes with {@link Breeze\Errors\Errors::getErrorForCode()}.
         */
        public function testDefinedErrorCodes()
        {
            foreach (self::getCodes() as $code => $message) {
                $this->assertSame($this->errors->getErrorForCode(substr($code, 5)), $message);
            }
        }

        /**
         * Tests {@link Breeze\Errors\Errors::add()} to add a default error
         * handler.
         */
        public function testSettingDefaultHandler()
        {
            $this->errors->add($this->closure);
            $this->checkErrorOutput();
        }

        /**
         * Tests {@link Breeze\Errors\Errors::add()} to add a handler for an
         * HTTP code.
         */
        public function testAddWithNumberName()
        {
            $this->errors->add('403', $this->closure);
            $this->checkErrorOutput();
        }

        /**
         * Tests {@link Breeze\Errors\Errors::add()} to add a handler for an
         * array of HTTP codes.
         */
        public function testAddWithNumberArray()
        {
            $this->errors->add(range(400,404), $this->closure);
            $this->checkErrorOutput();
        }

        /**
         * Tests {@link Breeze\Errors\Errors::add()} to add a handler for an
         * exception.
         */
        public function testAddWithExceptionName()
        {
            $this->errors->add('Exception', $this->closure);
            $this->checkErrorOutput();
        }

        /**
         * Tests {@link Breeze\Errors\Errors::add()} to add a handler for an
         * array of exceptions.
         */
        public function testAddWithExceptionArray()
        {
            $this->errors->add(array('InvalidArgumentException','Exception'), $this->closure);
            $this->checkErrorOutput();
        }

        /**
         * Tests {@link Breeze\Errors\Errors::add()} with an empty name.
         */
        public function testAddWithStringEmptyName()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'You must provide a name.');
            $this->errors->add('', $this->closure);
        }

        /**
         * Tests {@link Breeze\Errors\Errors::add()} with an empty array of
         * names.
         */
        public function testAddWithArrayEmptyName()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'You must provide a name.');
            $this->errors->add(array(''), $this->closure);
        }

        /**
         * Tests {@link Breeze\Errors\Errors::dispatchError()} with an invalid
         * error.
         */
        public function testDispatchWithInvalidError()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'Errors must be a string or a valid Exception');
            $this->errors->dispatchError(new \StdClass());
        }

        /**
         * Tests {@link Breeze\Errors\Errors::dispatchError()} to dispatch an
         * error with a string.
         */
        public function testDispatchWithString()
        {
            $this->expectOutputString('Error Test');
            $this->errors->add($this->closure);
            $this->errors->dispatchError('test', 403);
        }

        /**
         * Tests {@link Breeze\Errors\Errors::dispatchError()} to dispatch a
         * default handler with no layout and no backtrace.
         */
        public function testDefaultHandlerWithNoLayoutAndNoBacktrace()
        {
            $this->expectOutputString('<!DOCTYPE html><html><head><title>An error occurred</title></head><body><h1>test</h1></body></html>');
            $this->errors->dispatchError($this->exception);
        }

        /**
         * Tests {@link Breeze\Errors\Errors::dispatchError()} to dispatch a
         * default handler with no layout and a backtrace.
         */
        public function testDefaultHandlerWithNoLayoutAndBacktrace()
        {
            $this->application->expects($this->once())
                              ->method('config')
                              ->will($this->returnValue(true));
            $this->checkErrorOutput(sprintf('<!DOCTYPE html><html><head><title>An error occurred</title></head><body><h1>test</h1><pre><code>%s</code></pre></body></html>', $this->exception->getTraceAsString()));
        }

        /**
         * Tests {@link Breeze\Errors\Errors::dispatchError()} to dispatch a
         * default handler with a layout and no backtrace.
         */
        public function testDefaultHandlerWithLayoutAndNoBacktrace()
        {
            $this->application->expects($this->at(1))
                              ->method('__call')
                              ->with($this->equalTo('layoutExists'))
                              ->will($this->returnValue(true));
            $this->application->expects($this->at(2))
                              ->method('__call')
                              ->with($this->equalTo('fetchLayout'), $this->equalTo(array('<h1>test</h1>')))
                              ->will($this->returnValue("<mylayout><h1>test</h1></mylayout>"));
            $this->checkErrorOutput('<mylayout><h1>test</h1></mylayout>');
        }

        /**
         * Tests {@link Breeze\Errors\Errors::dispatchError()} to dispatch a
         * default handler with a layout and a backtrace.
         */
        public function testDefaultHandlerWithLayoutAndBacktrace()
        {
            $contents = sprintf('<h1>test</h1><pre><code>%s</code></pre>', $this->exception->getTraceAsString());

            $this->application->expects($this->once())
                              ->method('config')
                              ->will($this->returnValue(true));
            $this->application->expects($this->at(1))
                              ->method('__call')
                              ->with($this->equalTo('layoutExists'))
                              ->will($this->returnValue(true));
            $this->application->expects($this->at(2))
                              ->method('__call')
                              ->with($this->equalTo('fetchLayout'), $this->equalTo(array($contents)))
                              ->will($this->returnValue("<mylayout>$contents</mylayout>"));

            $this->checkErrorOutput("<mylayout>$contents</mylayout>");
        }

        /**
         * Tests {@link Breeze\Errors\Errors::setExit()} changes the exit
         * option.
         */
        public function testExit()
        {
            $this->assertFalse($this->errors->getExit());
            $this->errors->setExit(true);
            $this->assertTrue($this->errors->getExit());
        }

        /**
         * Tests errors issue the correct status headers.
         */
        public function testErrorsIssueCorrectStatusHeader()
        {
            $this->markTestSkipped(
              "At the moment it's not possible to test HTTP status codes.  Xdebug offers xdebug_get_headers, but it doesn't check status codes.  See: http://bugs.xdebug.org/view.php?id=601"
            );
        }

        /**
         * Gets an array of the defined HTTP error constants.
         *
         * @return array
         */
        protected static function getCodes() {
            $codes = array();

            $class = new \ReflectionClass(get_class());
            foreach ($class->getConstants() as $name => $constant) {
                if (substr($name, 0, 4) == 'HTTP') {
                    $codes[$name] = $constant;
                }
            }

            return $codes;
        }

        /**
         * Tests that the output from {@link Breeze\Errors\Errors::dispatchError()}
         * matches an expected string.
         *
         * @return array
         */
        protected function checkErrorOutput($expected = 'Error Test')
        {
            $this->expectOutputString($expected);
            $this->errors->dispatchError($this->exception);
        }
    }
}