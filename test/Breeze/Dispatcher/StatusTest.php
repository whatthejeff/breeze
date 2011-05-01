<?php
/**
 * Breeze Framework - Status test case
 *
 * This file contains the {@link Breeze\Dispatcher\Tests\StatusTest} class.
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

namespace Breeze\Dispatcher\Tests;

/**
 * @see Breeze\Dispatcher\Status
 */
use Breeze\Dispatcher\Status;

/**
 * The test case for the {@link Breeze\Dispatcher\Status} class.
 *
 * @package    Breeze
 * @subpackage Tests
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */
class StatusTest extends \PHPUnit_Framework_TestCase
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
     * The status object for testing.
     *
     * @param Breeze\Dispatcher\Status
     */
    protected $_status;

    /**
     * Sets up the test case for {@link Breeze\Dispatcher\Status}.
     *
     * @return void
     */
    public function setUp()
    {
        $this->_status = new Status();
    }

    /**
     * Tests default status.
     */
    public function testDefaultStatus()
    {
        $this->assertSame('HTTP/1.1 200 OK', (string) $this->_status);
    }

    /**
     * Tests {@link Breeze\Dispatcher\Status::set()} with an invalid HTTP code
     * yields a default 200 status.
     */
    public function testSetWithInvalidCode()
    {
        $this->_status->set(800);
        $this->assertSame('HTTP/1.1 200 OK', (string) $this->_status);
    }

    /**
     * Tests {@link Breeze\Dispatcher\Status::__construct()} with an invalid
     * HTTP code yields a default 200 status.
     */
    public function testConstrutorSetWithInvalidCode()
    {
        $this->_status = new Status(800);
        $this->assertSame('HTTP/1.1 200 OK', (string) $this->_status);
    }

    /**
     * Tests {@link Breeze\Dispatcher\Status::set()} with valid HTTP codes
     * generates the appropriate message.
     */
    public function testSetWithDefaultVersionString()
    {
        foreach (self::_getCodes() as $code => $message) {
            $statusCode = substr($code, 5);
            $statusMessage = sprintf(
                'HTTP/1.1 %s %s', $statusCode, constant("self::$code")
            );

            $this->_status->set($statusCode);
            $this->assertSame($statusMessage, (string) $this->_status);
        }
    }

    /**
     * Tests {@link Breeze\Dispatcher\Status::set()} with valid HTTP codes
     * and custom version string generates the appropriate message.
     */
    public function testSetWithExplicitVersionString()
    {
        foreach (self::_getCodes() as $code => $message) {
            $statusCode = substr($code, 5);
            $statusMessage = sprintf(
                'HTTP/1.0 %s %s', $statusCode, constant("self::$code")
            );

            $this->_status->set($statusCode, '1.0');
            $this->assertSame($statusMessage, (string) $this->_status);
        }
    }

    /**
     * Tests {@link Breeze\Dispatcher\Status::__constrct()} with valid HTTP
     * codes generates the appropriate message.
     */
    public function testConstructorSetWithDefaultVersionString()
    {
        foreach (self::_getCodes() as $code => $message) {
            $statusCode = substr($code, 5);
            $statusMessage = sprintf(
                'HTTP/1.1 %s %s', $statusCode, constant("self::$code")
            );

            $this->_status = new Status($statusCode);
            $this->assertSame($statusMessage, (string) $this->_status);
        }
    }

    /**
     * Tests {@link Breeze\Dispatcher\Status::__construct()} with valid HTTP
     * codes and custom version string generates the appropriate message.
     */
    public function testConstructorSetWithExplicitVersionString()
    {
        foreach (self::_getCodes() as $code => $message) {
            $statusCode = substr($code, 5);
            $statusMessage = sprintf(
                'HTTP/1.0 %s %s', $statusCode, constant("self::$code")
            );

            $this->_status = new Status($statusCode, '1.0');
            $this->assertSame($statusMessage, (string) $this->_status);
        }
    }

    /**
     * Gets an array of the defined HTTP constants.
     *
     * @return array
     */
    protected static function _getCodes() {
        $codes = array();

        $class = new \ReflectionClass(get_class());
        foreach ($class->getConstants() as $name => $constant) {
            if (substr($name, 0, 4) == 'HTTP') {
                $codes[$name] = $constant;
            }
        }

        return $codes;
    }
}