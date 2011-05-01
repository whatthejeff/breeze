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

namespace Breeze\Errors\Tests;

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
class ErrorsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The errors object for testing.
     *
     * @param Breeze\Errors\Errors
     */
    protected $_errors;
    /**
     * The application stub for testing {@link Breeze\Errors\Errors}.
     *
     * @param Breeze\Application
     */
    protected $_application;
    /**
     * An exception for testing {@link Breeze\Errors\Errors}.
     *
     * @param Exception
     */
    protected $_exception;

    /**
     * A sample closure to add to tests.
     *
     * @param Closure
     */
    protected $_closure;

    /**
     * Sets up the test case for {@link Breeze\Errors\Errors}.
     *
     * @return void
     */
    public function setUp()
    {
        $this->_application = $this->getMock(
            'Breeze\\Application', array(), array(), '', FALSE
        );
        $this->_exception = new \Exception('test', 403);

        $this->_closure = function(){
            echo 'Error Test';
        };

        $this->_errors = new Errors($this->_application);
    }

    /**
     * Tests {@link Breeze\Errors\Errors::add()} to add a default error
     * handler.
     */
    public function testSettingDefaultHandler()
    {
        $this->_errors->add($this->_closure);
        $this->_checkErrorOutput();
    }

    /**
     * Tests {@link Breeze\Errors\Errors::add()} to add a handler for an
     * HTTP code.
     */
    public function testAddWithNumberName()
    {
        $this->_errors->add('403', $this->_closure);
        $this->_checkErrorOutput();
    }

    /**
     * Tests {@link Breeze\Errors\Errors::add()} to add a handler for an
     * array of HTTP codes.
     */
    public function testAddWithNumberArray()
    {
        $this->_errors->add(range(400,404), $this->_closure);
        $this->_checkErrorOutput();
    }

    /**
     * Tests {@link Breeze\Errors\Errors::add()} to add a handler for an
     * exception.
     */
    public function testAddWithExceptionName()
    {
        $this->_errors->add('Exception', $this->_closure);
        $this->_checkErrorOutput();
    }

    /**
     * Tests {@link Breeze\Errors\Errors::add()} to add a handler for an
     * array of exceptions.
     */
    public function testAddWithExceptionArray()
    {
        $this->_errors->add(
            array('InvalidArgumentException','Exception'), $this->_closure
        );
        $this->_checkErrorOutput();
    }

    /**
     * Tests {@link Breeze\Errors\Errors::add()} with an empty name.
     */
    public function testAddWithStringEmptyName()
    {
        $this->setExpectedException(
            '\\InvalidArgumentException', 'You must provide a name.'
        );
        $this->_errors->add('', $this->_closure);
    }

    /**
     * Tests {@link Breeze\Errors\Errors::add()} with an empty array of
     * names.
     */
    public function testAddWithArrayEmptyName()
    {
        $this->setExpectedException(
            '\\InvalidArgumentException', 'You must provide a name.'
        );
        $this->_errors->add(array(''), $this->_closure);
    }

    /**
     * Tests {@link Breeze\Errors\Errors::dispatchError()} with an invalid
     * error.
     */
    public function testDispatchWithInvalidError()
    {
        $this->setExpectedException(
            '\\InvalidArgumentException',
            'Errors must be a string or a valid Exception'
        );
        $this->_errors->dispatchError(new \StdClass());
    }

    /**
     * Tests {@link Breeze\Errors\Errors::dispatchError()} to dispatch an
     * error with a string.
     */
    public function testDispatchWithString()
    {
        $this->setExpectedException('Breeze\\Dispatcher\\EndRequestException');
        $this->expectOutputString('Error Test');

        $this->_errors->add($this->_closure);
        $this->_errors->dispatchError('test', 403);
    }

    /**
     * Tests {@link Breeze\Errors\Errors::dispatchError()} to dispatch an
     * error without a string and a code that corresponds to an HTTP error
     * status code.
     */
    public function testDispatchWithoutStringAndHttpStatusCode()
    {
        $this->setExpectedException('Breeze\\Dispatcher\\EndRequestException');
        $this->expectOutputString('403 - Forbidden');
        $this->_application->expects($this->once())
                           ->method('status')
                           ->with($this->equalTo(403))
                           ->will($this->returnValue(
                               'HTTP/1.1 403 Forbidden'
                             ));

        $this->_errors->add(function($app, $exception){
            echo $exception->getMessage();
        });
        $this->_errors->dispatchError('', 403);
    }

    /**
     * Tests {@link Breeze\Errors\Errors::dispatchError()} to dispatch an
     * error without a string and a code that does not correspond to an HTTP
     * error status code.
     */
    public function testDispatchWithoutStringAndNonHttpStatusCode()
    {
        $this->setExpectedException('Breeze\\Dispatcher\\EndRequestException');
        $this->expectOutputString('An Error Occurred');
        $this->_application->expects($this->never())
                           ->method('status');

        $this->_errors->add(function($app, $exception){
            echo $exception->getMessage();
        });
        $this->_errors->dispatchError('', 800);
    }

    /**
     * Tests {@link Breeze\Errors\Errors::dispatchError()} to dispatch a
     * default handler with no layout and no backtrace.
     */
    public function testDefaultHandlerWithNoLayoutAndNoBacktrace()
    {
        $this->_checkErrorOutput(
            '<!DOCTYPE html><html><head><title>An Error Occurred</title>' .
            '</head><body><h1>test</h1></body></html>'
        );
    }

    /**
     * Tests {@link Breeze\Errors\Errors::dispatchError()} to dispatch a
     * default handler with no layout and a backtrace.
     */
    public function testDefaultHandlerWithNoLayoutAndBacktrace()
    {
        $this->_application->expects($this->once())
                           ->method('config')
                           ->will($this->returnValue(true));
        $this->_checkErrorOutput(sprintf(
            '<!DOCTYPE html><html><head><title>An Error Occurred</title>' .
            '</head><body><h1>test</h1><pre><code>%s</code></pre></body>' .
            '</html>',
            $this->_exception->getTraceAsString()
        ));
    }

    /**
     * Tests {@link Breeze\Errors\Errors::dispatchError()} to dispatch a
     * default handler with a layout and no backtrace.
     */
    public function testDefaultHandlerWithLayoutAndNoBacktrace()
    {
        $this->_application->expects($this->at(2))
                           ->method('__call')
                           ->with($this->equalTo('layoutExists'))
                           ->will($this->returnValue(true));
        $this->_application->expects($this->at(3))
                           ->method('__call')
                           ->with(
                               $this->equalTo('fetchLayout'),
                               $this->equalTo(array('<h1>test</h1>'))
                             )
                           ->will($this->returnValue(
                               "<mylayout><h1>test</h1></mylayout>"
                             ));
        $this->_checkErrorOutput('<mylayout><h1>test</h1></mylayout>');
    }

    /**
     * Tests {@link Breeze\Errors\Errors::dispatchError()} to dispatch a
     * default handler with a layout and a backtrace.
     */
    public function testDefaultHandlerWithLayoutAndBacktrace()
    {
        $contents = sprintf(
            '<h1>test</h1><pre><code>%s</code></pre>',
            $this->_exception->getTraceAsString()
        );

        $this->_application->expects($this->once())
                           ->method('config')
                           ->will($this->returnValue(true));
        $this->_application->expects($this->at(2))
                           ->method('__call')
                           ->with($this->equalTo('layoutExists'))
                           ->will($this->returnValue(true));
        $this->_application->expects($this->at(3))
                           ->method('__call')
                           ->with(
                               $this->equalTo('fetchLayout'),
                               $this->equalTo(array($contents))
                             )
                           ->will(
                               $this->returnValue(
                                   "<mylayout>$contents</mylayout>"
                               )
                             );

        $this->_checkErrorOutput("<mylayout>$contents</mylayout>");
    }

    /**
     * Tests dispatching an error with a code that corresponds to an HTTP error
     * status code sends the corresponding HTTP status header.
     */
    public function testErrorCodeCorrespondingToHttpStatusCode()
    {
        $this->setExpectedException('Breeze\\Dispatcher\\EndRequestException');

        $this->_application->expects($this->once())
                           ->method('status')
                           ->with($this->equalTo(403));
        $this->_errors->dispatchError($this->_exception);
    }

    /**
     * Tests dispatching an error with a code that does not correspond to an
     * HTTP error status code does not send an HTTP status header.
     */
    public function testNotErrorCodeCorrespondingToHttpStatusCode()
    {
        $this->setExpectedException('Breeze\\Dispatcher\\EndRequestException');

        $this->_exception = new \Exception('test', 800);
        $this->_application->expects($this->never())->method('status');
        $this->_errors->dispatchError($this->_exception);
    }

    /**
     * Tests that the output from {@link Breeze\Errors\Errors::dispatchError()}
     * matches an expected string.
     *
     * @return array
     */
    protected function _checkErrorOutput($expected = 'Error Test')
    {
        $this->setExpectedException('Breeze\\Dispatcher\\EndRequestException');
        $this->expectOutputString($expected);
        $this->_errors->dispatchError($this->_exception);
    }
}