<?php
/**
 * Breeze Framework - Conditions test case
 *
 * This file contains the {@link Breeze\Dispatcher\Tests\ConditionsTest} class.
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

namespace Breeze\Dispatcher\Tests {

    /**
     * @see Breeze\Dispatcher\Dispatcher
     */
    use Breeze\Dispatcher\Dispatcher;
    /**
     * @see Breeze\Dispatcher\NotFoundException
     */
    use Breeze\Dispatcher\NotFoundException;

    /**
     * The test case for the {@link Breeze\Dispatcher\Dispatcher} class.
     *
     * @package    Breeze
     * @subpackage Tests
     * @author     Jeff Welch <whatthejeff@gmail.com>
     * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
     * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
     * @link       http://breezephp.com/
     */
    class DispatcherTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * The application stub for testing {@link Breeze\Dispatcher\Dispatcher}.
         *
         * @param Breeze\Application
         */
        protected $application;
        /**
         * The dispatcher object for testing.
         *
         * @param Breeze\Dispatcher\Dispatcher
         */
        protected $dispatcher;

        /**
         * The valid request methods.
         *
         * @param array
         */
        protected static $supported_methods = array('GET','POST','PUT','DELETE');
        /**
         * A mapping of original URIs and the expected final result after the
         * dispatcher normalizes them.
         *
         * @param array
         */
        protected static $uris = array(

            # Empty url, means homepage
            array(
                'original' => '',
                'final'    => '/'
            ),

            # Homepage
            array(
                'original' => '/',
                'final'    => '/'
            ),

            # Standard path without forward slash
            array(
                'original' => '/path',
                'final'    => '/path'
            ),

            # Standard path with forward slash
            array(
                'original' => '/path/',
                'final'    => '/path'
            ),

            # Querystring
            array(
                'original' => '/path/with/querystring?this=neat&this=cool',
                'final'    => '/path/with/querystring'
            ),

            # Querystring with forward slash
            array(
                'original' => '/path/with/querystring/?this=neat&this=cool',
                'final'    => '/path/with/querystring'
            ),

            # Hash
            array(
                'original' => '/path/with/hash#neat',
                'final'    => '/path/with/hash'
            ),

            # Hash with forward slash
            array(
                'original' => '/path/with/hash/#neat',
                'final'    => '/path/with/hash'
            ),

            # Query string, slash, and hash
            array(
                'original' => '/path/with/hash/and/querystring/?this=neat#neat',
                'final'    => '/path/with/hash/and/querystring'
            )
        );

        /**
         * Sets up the test case for {@link Breeze\Dispatcher\Dispatcher}.
         *
         * @return void
         */
        public function setUp()
        {
            $this->application = $this->getMock('Breeze\\Application', array(), array(), '', FALSE);
            $this->dispatcher = new Dispatcher($this->application);
        }

        /**
         * Tests an invalid request method triggers an error.
         */
        public function testInvalidRequestMethod()
        {
            $this->setExpectedException('PHPUnit_Framework_Error', 'Call to undefined function:');
            $this->dispatcher->invalid('/something', function(){});
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::setRequestMethod()} with
         * HEAD uses GET.
         */
        public function testSetRequestMethodWithHeadUsesGet()
        {
            $this->dispatcher->setRequestMethod('HEAD');
            $this->assertSame('GET', $this->dispatcher->getRequestMethod());
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::setRequestMethod()} to
         * specify a request method.
         */
        public function testSetRequestMethodWithParameter()
        {
            $dispatcher = $this->dispatcher;
            $this->_testSetRequestMethod(function($method) use ($dispatcher){
                $dispatcher->setRequestMethod($method);
            });
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::setRequestMethod()} to
         * get the request method from the $_SERVER superglobal.
         */
        public function testSetRequestMethodWithRequestMethod()
        {
            $this->_testSetRequestMethod(function($method){
                $_SERVER['REQUEST_METHOD'] = $method;
            });
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::setRequestMethod()} to
         * get the request method from the $_ENV superglobal.
         */
        public function testSetRequestMethodWithEnv()
        {
            $this->_testSetRequestMethod(function($method){
                $_ENV['HTTP_X_HTTP_METHOD_OVERRIDE'] = $method;
            });
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::setRequestMethod()} to
         * get the request method from a form field.
         */
        public function testSetRequestMethodWithFormField()
        {
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $this->_testSetRequestMethod(function($method){
                $_POST['_method'] = $method;
            });
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::dispatch()} to
         * set the request method.
         */
        public function testSetRequesMethodFromDispatch()
        {
            foreach (self::$supported_methods as $method) {
                try {
                    $this->dispatcher->dispatch($method);
                    $this->fail("Expected exception Breeze\\Dispatcher\\NotFoundException");
                } catch (NotFoundException $exception) {
                    $this->assertSame($method, $this->dispatcher->getRequestMethod());
                }
            }
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::setRequestUri()} to
         * set the requested URI.
         */
        public function testSetRequestUriWithParameter()
        {
            $dispatcher = $this->dispatcher;
            $this->_testSetRequestUri(function($uri) use ($dispatcher){
                $dispatcher->setRequestUri($uri['original']);
            });
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::setRequestUri()} to
         * set the requested URI from the $_SERVER['REQUEST_URI'] superglobal.
         */
        public function testSetRequestUriWithRequestUri()
        {
            $this->_testSetRequestUri(function($uri){
                $_SERVER['REQUEST_URI'] = $uri['original'];
            });
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::setRequestUri()} to
         * set the requested URI from the $_SERVER['HTTP_X_REWRITE_URL'].
         */
        public function testSetRequestUriWithXRewriteUrl()
        {
            $this->_testSetRequestUri(function($uri){
                $_SERVER['HTTP_X_REWRITE_URL'] = $uri['original'];
            });
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::setRequestUri()} to
         * set the requested URI from the $_SERVER['UNENCODED_URL'].
         */
        public function testSetRequestUriWithUnencodedUrl()
        {
            $_SERVER['IIS_WasUrlRewritten'] = '1';
            $this->_testSetRequestUri(function($uri){
                $_SERVER['UNENCODED_URL'] = $uri['original'];
            });
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::setRequestUri()} to
         * set the requested URI from the $_SERVER['ORIG_PATH_INFO'].
         */
        public function testSetRequestUriWithOrigPathInfo()
        {
            $this->_testSetRequestUri(function($uri){
                $_SERVER['ORIG_PATH_INFO'] = $uri['original'];
            });
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::dispatch()} to
         * set the request URI.
         */
        public function testSetRequestUriFromDispatch()
        {
            foreach (self::$uris as $uri) {
                try {
                    $this->dispatcher->dispatch(null, $uri['original']);
                    $this->fail("Expected exception Breeze\\Dispatcher\\NotFoundException");
                } catch (NotFoundException $exception) {
                    $this->assertSame($uri['final'], $this->dispatcher->getRequestUri());
                }
            }
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::get()} to add route with
         * no pattern protion.
         */
        public function testAddRouteWithNoPattern()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'You must provide a non-empty pattern');
            $this->dispatcher->get(null, function(){});
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::get()} to add route with
         * invalid closure.
         */
        public function testAddRouteWithNoCallback()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'You must provide a callable PHP function.');
            $this->dispatcher->get('invalid closure', 'INVALID CLOSURE');
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::dispatch()} where the URI
         * matches an added route but the request method does not.
         */
        public function testDispatchWithMatchingUriAndNotMatchingMethod()
        {
            $this->setExpectedException('Breeze\\Dispatcher\\NotFoundException', 'No Matching Routes Found');
            $this->_addUris();
            $this->dispatcher->dispatch('POST', '/path/with/querystring?this=neat&this=cool');
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::dispatch()} where the request
         * method matches an added route but the request URI does not.
         */
        public function testDispatchWithNotMatchingUriAndMatchingMethod()
        {
            $this->setExpectedException('Breeze\\Dispatcher\\NotFoundException', 'No Matching Routes Found');
            $this->_addUris();
            $this->dispatcher->dispatch('GET', '/abc');
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::any()} can be used to access
         * a route from any request method.
         */
        public function testDispatchWithAny()
        {
            $this->_addUris('any');
            $this->_testAllUrisWithAllMethods();
        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::dispatch()} with string
         * matching.
         */
        public function testDispatchWithStringMatch()
        {
            $this->_addUris(self::$supported_methods);
            $this->_testAllUrisWithAllMethods();

        }

        /**
         * Tests {@link Breeze\Dispatcher\Dispatcher::dispatch()} with regexp
         * matching.
         */
        public function testDispatchWithRegexpMatch()
        {
            $this->dispatcher->any(';wont match;', function() {
                $this->fail('Pattern ;wont match; should not match.');
            });

            $this->dispatcher->any(';(.+);', function($app, $matches) {
                echo "Hi from {$matches[1]}";
            });

            $this->_testAllUrisWithAllMethods();
        }

        /**
         * Tests setting the request method works as expected.
         *
         * @param Closure $callback    A callback for setting the request method
         * @param boolean $call_setter If {@link Breeze\Dispatcher\Dispatcher::setRequestMethod()} should be called.
         *
         * @return void
         */
        protected function _testSetRequestMethod($callback, $call_setter = true)
        {
            foreach (self::$supported_methods as $method) {
                $callback($method);

                if ($call_setter) {
                    $this->dispatcher->setRequestMethod();
                }

                $this->assertSame($method, $this->dispatcher->getRequestMethod());
            }
        }

        /**
         * Tests setting the request URI works as expected.
         *
         * @param Closure $callback    A callback for setting the request URI
         * @param boolean $call_setter If {@link Breeze\Dispatcher\Dispatcher::setRequestUri()} should be called.
         *
         * @return void
         */
        protected function _testSetRequestUri($callback, $call_setter = true)
        {
            foreach (self::$uris as $uri) {
                $callback($uri);

                if ($call_setter) {
                    $this->dispatcher->setRequestUri();
                }

                $this->assertSame($uri['final'], $this->dispatcher->getRequestUri());
            }
        }

        /**
         * Tests hitting all uris in {@link Breeze\Dispatcher\Tests\DispatcherTest::$uris} on
         * all methods defined in {@link Breeze\Dispatcher\Tests\DispatcherTest::$method}.
         *
         * @param Closure $callback    A callback for setting the request URI
         * @param boolean $call_setter If {@link Breeze\Dispatcher\Dispatcher::setRequestUri()} should be called.
         *
         * @return void
         */
        protected function _testAllUrisWithAllMethods()
        {
            foreach (self::$supported_methods as $method) {
                foreach (self::$uris as $uri) {
                    ob_start();
                    $this->dispatcher->dispatch($method, $uri['original']);
                    $this->assertSame("Hi from " . $uri['final'], ob_get_clean());
                }
            }
        }

        /**
         * Add routes for the uris in {@link Breeze\Dispatcher\Tests\DispatcherTest::$uris}.
         *
         * @param string|array $methods Request methods to add the uris for.
         *
         * @return void
         */
        protected function _addUris($methods = 'GET')
        {
            foreach ((array)$methods as $method) {
                foreach (self::$uris as $uri) {
                    $this->dispatcher->$method($uri['final'], function() use ($uri) {
                        echo "Hi from {$uri['final']}";
                    });
                }
            }
        }
    }
}