<?php
/**
 * Breeze Framework - Application test case
 *
 * This file contains the {@link Breeze\Tests\ApplicationTest} class.
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

namespace Breeze\Tests {

    /**
     * @see Breeze\Application
     */
    use Breeze\Application;

    /**
     * The test case for the {@link Breeze\Application} class.
     *
     * @package    Breeze
     * @subpackage Tests
     * @author     Jeff Welch <whatthejeff@gmail.com>
     * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
     * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
     * @link       http://breezephp.com/
     */
    class ApplicationTest extends ApplicationTestCase
    {
        /**
         * Sets up the test case for {@link Breeze\Application}.
         *
         * @return void
         */
        public function setUp()
        {
            $this->_setupMockedDependencies();
            $this->_mockApplication();
        }

        /**
         * Tests {@link Breeze\Application::pass()} throws {@link Breeze\Dispatcher\PassException}.
         */
        public function testPass()
        {
            $this->setExpectedException('Breeze\\Dispatcher\\PassException');
            $this->application->pass();
        }

        /**
         * Tests {@link Breeze\Application::redirect()} throws a location header.
         */
        public function testRedirect()
        {
            $this->application->redirect('http://www.breezephp.com/', null, false);
            $this->assertSame(xdebug_get_headers(), array('Location: http://www.breezephp.com/'));
        }

        /**
         * Tests {@link Breeze\Application::redirect()} with a custom status code.
         */
        public function testRedirectWithDifferentCode()
        {
            $this->markTestSkipped(
              "At the moment it's not possible to test HTTP status codes.  Xdebug offers xdebug_get_headers, but it doesn't check status codes.  See: http://bugs.xdebug.org/view.php?id=601"
            );
        }

        /**
         * Tests {@link Breeze\Application::config()} to set a single value.
         */
        public function testConfigSetWithSingleValue()
        {
            $this->configurations->expects($this->once())
                                 ->method('set')
                                 ->with($this->equalTo('this is a'), $this->equalTo('test'));
            $this->application->config('this is a', 'test');
        }

        /**
         * Tests {@link Breeze\Application::config()} to set values with an array.
         */
        public function testConfigSetWithArray()
        {
            $config = array(
                'test1' => 'value1',
                'test2' => 'value2'
            );

            $this->configurations->expects($this->once())
                                 ->method('set')
                                 ->with($this->equalTo($config));
            $this->application->config($config);
        }

        /**
         * Tests {@link Breeze\Application::config()} to retrieve a value.
         */
        public function testConfigGet()
        {
            $this->configurations->expects($this->at(0))
                                 ->method('get')
                                 ->with($this->equalTo('this is a'))
                                 ->will($this->returnValue('test'));
            $this->assertSame('test', $this->application->config('this is a'));
        }

        /**
         * Tests {@link Breeze\Application::condition()} to retrieve a condition without parameters.
         */
        public function testConditionGetWithoutParameters()
        {
           $this->mocks['conditions_object']->expects($this->once())
                                            ->method('dispatchCondition')
                                            ->with($this->equalTo('test'));
           $this->application->condition('test');
        }

        /**
         * Tests {@link Breeze\Application::condition()} to retrieve a condition with parameters.
         */
        public function testConditionGetWithParameters()
        {
           $this->mocks['conditions_object']->expects($this->once())
                                            ->method('dispatchCondition')
                                            ->with($this->equalTo('test'), $this->equalTo('param1'), $this->equalTo('param2'));
           $this->application->condition('test', 'param1', 'param2');
        }

        /**
         * Tests {@link Breeze\Application::condition()} to set a condition.
         */
        public function testConditionSet()
        {
           $closure = function(){};
           $this->mocks['conditions_object']->expects($this->once())
                                            ->method('add')
                                            ->with($this->equalTo('test'), $this->equalTo($closure));
           $this->application->condition('test', $closure);
        }

        /**
         * Tests {@link Breeze\Application::template()} to get a template variable.
         */
        public function testTemplateGet()
        {
            $this->mocks['view_object']->expects($this->once())
                                       ->method('__get')
                                       ->with($this->equalTo('this_is_a'))
                                       ->will($this->returnValue('test'));
            $this->assertSame('test', $this->application->template('this_is_a'));
        }

        /**
         * Tests {@link Breeze\Application::template()} to set a single template variable.
         */
        public function testTemplateSetSingle()
        {
            $this->mocks['view_object']->expects($this->once())
                                       ->method('__set')
                                       ->with($this->equalTo('this_is_a'), $this->equalTo('value'));
            $this->application->template('this_is_a', 'value');
        }

        /**
         * Tests {@link Breeze\Application::template()} to set multiple template variables
         * with an array.
         */
        public function testTemplateSetArray()
        {
            $template_variables = array(
                'test1' => 'value1',
                'test2' => 'value2'
            );

            $this->mocks['view_object']->expects($this->once())
                                       ->method('addVariables')
                                       ->with($this->equalTo($template_variables));
            $this->application->template($template_variables);
        }

        /**
         * Tests {@link Breeze\Application::error()} to dispatch an error using a standard
         * HTTP error code.
         */
        public function testErrorDispatchDefaultHttpErrorCode()
        {
            $this->mocks['errors_object']->expects($this->once())
                                         ->method('getErrorForCode')
                                         ->with($this->equalTo(404))
                                         ->will($this->returnValue('Page Not Found'));
            $this->mocks['errors_object']->expects($this->once())
                                         ->method('dispatchError')
                                         ->with($this->equalTo('404 - Page Not Found'), $this->equalTo('404'));
            $this->application->error(404);
        }

        /**
         * Tests {@link Breeze\Application::error()} to dispatch an error using a non-standard
         * HTTP error code.
         */
        public function testErrorDispatchGenericMessageWithNonHttpErrorCode()
        {
            $this->mocks['errors_object']->expects($this->once())
                                         ->method('getErrorForCode')
                                         ->with($this->equalTo(600))
                                         ->will($this->returnValue(null));
            $this->mocks['errors_object']->expects($this->once())
                                         ->method('dispatchError')
                                         ->with($this->equalTo('An Error Occurred.'), $this->equalTo('600'));
            $this->application->error(600);
        }

        /**
         * Tests {@link Breeze\Application::error()} to dispatch a custom error message.
         */
        public function testErrorDispatchCustomMessage()
        {
            $this->mocks['errors_object']->expects($this->once())
                                         ->method('dispatchError')
                                         ->with($this->equalTo('this is a message'), $this->equalTo('0'));
            $this->application->error("this is a message");
        }

        /**
         * Tests {@link Breeze\Application::error()} to add a default error.
         */
        public function testErrorAddDefault()
        {
            $closure = function(){};
            $this->mocks['errors_object']->expects($this->once())
                                         ->method('add')
                                         ->with($this->equalTo($closure));
            $this->application->error($closure);
        }

        /**
         * Tests {@link Breeze\Application::error()} to add an error using a code.
         */
        public function testErrorAddCode()
        {
            $closure = function(){};
            $this->mocks['errors_object']->expects($this->once())
                                         ->method('add')
                                         ->with($this->equalTo(403), $this->equalTo($closure));
            $this->application->error(403, $closure);
        }

        /**
         * Tests {@link Breeze\Application::error()} to add an error using an exception.
         */
        public function testErrorAddException()
        {
            $closure = function(){};
            $this->mocks['errors_object']->expects($this->once())
                                         ->method('add')
                                         ->with($this->equalTo('Exception'), $this->equalTo($closure));
            $this->application->error('Exception', $closure);
        }

        /**
         * Tests {@link Breeze\Application::error()} to add errors using an array.
         */
        public function testErrorAddArray()
        {
            $closure = function(){};
            $range = range(400, 500);
            $this->mocks['errors_object']->expects($this->once())
                                         ->method('add')
                                         ->with($this->equalTo($range), $this->equalTo($closure));
            $this->application->error($range, $closure);
        }

        /**
         * Tests calling an unknown method triggers an undefined function method.
         */
        public function testCallUnkownMethod()
        {
            $this->setExpectedException('\\PHPUnit_Framework_Error', 'Call to undefined function:');
            $this->application->this_will_fail();
        }

        /**
         * Tests {@link Breeze\Application::display()} to display a template with no variables.
         */
        public function testViewDisplay()
        {
            $this->mocks['view_object']->expects($this->once())
                                       ->method('display')
                                       ->with($this->equalTo('template'));
            $this->application->display('template');
        }

        /**
         * Tests {@link Breeze\Application::display()} to display a template with variables.
         */
        public function testViewDisplayWithVariables()
        {
            $template_variables = array('this is a', 'test');
            $this->mocks['view_object']->expects($this->once())
                                       ->method('display')
                                       ->with($this->equalTo('template'), $this->equalTo($template_variables));
            $this->application->display('template', $template_variables);
        }

        /**
         * Tests {@link Breeze\Application::fetch()} to get the contents of a template.
         */
        public function testFetch()
        {
            $this->mocks['view_object']->expects($this->once())
                                       ->method('fetch')
                                       ->with($this->equalTo('template'))
                                       ->will($this->returnValue('template contents'));
            $this->assertSame('template contents', $this->application->fetch('template'));
        }

        /**
         * Tests {@link Breeze\Application::fetch()} to get the contents of a template with variables.
         */
        public function testViewFetchWithVariables()
        {
            $template_variables = array('this is a', 'test');
            $this->mocks['view_object']->expects($this->once())
                                       ->method('fetch')
                                       ->with($this->equalTo('template'), $this->equalTo($template_variables))
                                       ->will($this->returnValue('template contents'));
            $this->assertSame('template contents', $this->application->fetch('template', $template_variables));
        }

        /**
         * Tests {@link Breeze\Application::layout()} to set the layout file.
         */
        public function testViewLayout()
        {
            $this->mocks['view_object']->expects($this->once())
                                       ->method('layout')
                                       ->with($this->equalTo('layout'));
            $this->application->layout('layout');
        }

        /**
         * Tests {@link Breeze\Application::partial()} to fetch the contents of a partial.
         */
        public function testPartial()
        {
            $this->mocks['view_object']->expects($this->once())
                                       ->method('partial')
                                       ->with($this->equalTo('template'))
                                       ->will($this->returnValue('template contents'));
            $this->assertSame('template contents', $this->application->partial('template'));
        }

        /**
         * Tests {@link Breeze\Application::partial()} to fetch the contents of a partial.
         * with variables.
         */
        public function testViewPartialWithVariables()
        {
            $template_variables = array('this is a', 'test');
            $this->mocks['view_object']->expects($this->once())
                                       ->method('partial')
                                       ->with($this->equalTo('template'), $this->equalTo($template_variables))
                                       ->will($this->returnValue('template contents'));
            $this->assertSame('template contents', $this->application->partial('template', $template_variables));
        }

        /**
         * Tests {@link Breeze\Application::layoutExists()} to see if a layout exists.
         */
        public function testViewLayoutExists()
        {
            $this->mocks['view_object']->expects($this->once())
                                       ->method('layoutExists')
                                       ->will($this->returnValue(true));
            $this->assertTrue($this->application->layoutExists());
        }

        /**
         * Tests {@link Breeze\Application::fetchLayout()} to wrap contents in a layout file.
         */
        public function testViewFetchLayout()
        {
            $this->mocks['view_object']->expects($this->once())
                                       ->method('fetchLayout')
                                       ->with($this->equalTo('contents'))
                                       ->will($this->returnValue('<layout>contents</layout>'));
            $this->assertSame('<layout>contents</layout>', $this->application->fetchLayout('contents'));
        }

        /**
         * Tests {@link Breeze\Application::get()}, {@link Breeze\Application::post()},
         * {@link Breeze\Application::put()}, {@link Breeze\Application::delete()} to dispatch
         * synthetic requests.
         */
        public function testDispatcherSyntheticRequest()
        {
            foreach (array('get','post','put','delete') as $method) {
                $this->mocks['dispatcher_object']->expects($this->at(0))
                                                 ->method('dispatch')
                                                 ->with($this->equalTo($method), $this->equalTo('/my/page'));
                $this->application->$method('/my/page');
            }
        }

        /**
         * Tests {@link Breeze\Application::get()}, {@link Breeze\Application::post()},
         * {@link Breeze\Application::put()}, {@link Breeze\Application::delete()} to add
         * new routes.
         */
        public function testDispatcherAdd()
        {
            $closure = function(){};
            foreach (array('get','post','put','delete') as $method) {
                $this->mocks['dispatcher_object']->expects($this->at(0))
                                                 ->method('__call')
                                                 ->with($this->equalTo($method), $this->equalTo(array('/my/page', $closure)));
                $this->application->$method('/my/page', $closure);
            }
        }

        /**
         * Tests {@link Breeze\Application::__set()} to set a template variable.
         */
        public function testSet()
        {
            $this->mocks['view_object']->expects($this->once())
                                       ->method('__set')
                                       ->with($this->equalTo('this_is_a'), $this->equalTo('test'));
            $this->application->this_is_a = 'test';
        }

        /**
         * Tests {@link Breeze\Application::__get()} to get a template variable.
         */
        public function testGet()
        {
            $this->mocks['view_object']->expects($this->once())
                                       ->method('__get')
                                       ->with($this->equalTo('this_is_a'))
                                       ->will($this->returnValue('test'));
            $this->assertSame('test', $this->application->this_is_a);
        }

        /**
         * Tests {@link Breeze\Application::__isset()} to check if a template variable is set.
         */
        public function testIsset()
        {
            $this->mocks['view_object']->expects($this->once())
                                       ->method('__isset')
                                       ->with($this->equalTo('this_is_a'))
                                       ->will($this->returnValue(false));
            $this->assertFalse(isset($this->application->this_is_a));
        }

        /**
         * Tests {@link Breeze\Application::__unset()} to unset a template variable.
         */
        public function testUnset()
        {
            $this->mocks['view_object']->expects($this->once())
                                       ->method('__unset')
                                       ->with($this->equalTo('this_is_a'));
            unset($this->application->this_is_a);
        }

        /**
         * Tests {@link Breeze\Application::helper()} to add a helper.
         */
        public function testAddHelper()
        {
            $closure = function(){};
            $this->mocks['helpers_object']->expects($this->once())
                                          ->method('add')
                                          ->with($this->equalTo('name'), $this->equalTo($closure), 'label');
            $this->application->helper('name', $closure);
        }

        /**
         * Tests {@link Breeze\Application::helper()} to add a helper.
         */
        public function testRunHelper()
        {
            $this->mocks['helpers_object']->expects($this->once())
                                          ->method('has')
                                          ->with('test_helper')
                                          ->will($this->returnValue(true));
            $this->mocks['helpers_object']->expects($this->once())
                                          ->method('get')
                                          ->with('test_helper')
                                          ->will($this->returnValue(function($name){ return "hello $name"; }));
            $this->assertSame('hello test', $this->application->test_helper('test'));
        }

        /**
         * Tests {@link Breeze\Application::getHelpers()} to get a list of defined helpers.
         */
        public function testGetHelpers()
        {
            $helpers = array(
                'test1','test2','get','delete','put','post','any','before','after','config',
                'template','display','fetch','pass','helper','run','error','condition','redirect',
                'partial'
            );
            $this->mocks['helpers_object']->expects($this->once())
                                          ->method('all')
                                          ->will($this->returnValue(array('test1'=>function(){}, 'test2'=>function(){})));
            $this->assertSame($helpers, $this->application->getHelpers());
        }

        /**
         * Tests {@link Breeze\Application::before()} to add a before filter.
         */
        public function testAddBeforeFilter()
        {
            $closure = function(){};
            $this->mocks['before_filters_object']->expects($this->once())
                                                 ->method('add')
                                                 ->with($this->equalTo($closure));
            $this->application->before($closure);
        }

        /**
         * Tests {@link Breeze\Application::after()} to add an after filter.
         */
        public function testAddAfterFilter()
        {
            $closure = function(){};
            $this->mocks['after_filters_object']->expects($this->once())
                                                ->method('add')
                                                ->with($this->equalTo($closure));
            $this->application->after($closure);
        }

        /**
         * Tests {@link Breeze\Application::filter()} with a bad filter type throws an
         * InvalidArgumentException.
         */
        public function testFilterWithBadType()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'is not a valid filter type.');
            $this->application->filter('DOES NOT EXIST');
        }

        /**
         * Tests {@link Breeze\Application::filter()} to run before filters.
         */
        public function testFilterBefore()
        {
            $this->expectOutputString('test1test2');
            $this->mocks['before_filters_object']->expects($this->once())
                                                 ->method('all')
                                                 ->will($this->returnValue(array('test1'=>function(){ echo "test1"; }, 'test2'=>function(){ echo "test2"; })));
            $this->application->filter('before');
        }

        /**
         * Tests {@link Breeze\Application::filter()} to run after filters.
         */
        public function testFilterAfter()
        {
            $this->expectOutputString('test1test2');
            $this->mocks['after_filters_object']->expects($this->once())
                                                ->method('all')
                                                ->will($this->returnValue(array('test1'=>function(){ echo "test1"; }, 'test2'=>function(){ echo "test2"; })));
            $this->application->filter('after');
        }

        /**
         * Tests {@link Breeze\Application::run()} to dispatch exceptions and filters.
         */
        public function testRun()
        {
            $this->expectOutputString('before1before2contentsafter1after2');
            $this->mocks['before_filters_object']->expects($this->once())
                                                 ->method('all')
                                                 ->will($this->returnValue(array('test1'=>function(){ echo "before1"; }, 'test2'=>function(){ echo "before2"; })));
            $this->mocks['after_filters_object']->expects($this->once())
                                                ->method('all')
                                                ->will($this->returnValue(array('test1'=>function(){ echo "after1"; }, 'test2'=>function(){ echo "after2"; })));
            $this->mocks['dispatcher_object']->expects($this->once())
                                             ->method('dispatch')
                                             ->will($this->returnCallback(function(){
                                                 echo "contents";
                                               }));
            $this->application->run();
        }

        /**
         * Tests exceptions throw from {@link Breeze\Dispatcher\Dispatcher::dispatch()} in
         * {@link Breeze\Application::run()} will be dispatched using {@link Breeze\\Errors\\Errors}
         */
        public function testRunWithExceptions()
        {
            $exception = new \Exception('Something bad happened');
            $this->mocks['dispatcher_object']->expects($this->once())
                                             ->method('dispatch')
                                             ->will($this->throwException($exception));
            $this->mocks['errors_object']->expects($this->once())
                                         ->method('dispatchError')
                                         ->with($this->equalTo($exception));
            $this->application->run();
        }

        /**
         * Tests injecting a bad dependency while constructing {@link Breeze\Application} throws
         * an {@link UnexpectedValueException}.
         */
        public function testInjectBadDependency()
        {
            $this->setExpectedException('\\UnexpectedValueException', 'stdClass is not an instance of Breeze\\View\\View.');
            $this->configurations = $this->getMock('Breeze\\Configurations', array(), array(), '', FALSE);
            $this->configurations->expects($this->any())
                                 ->method('get')
                                 ->will($this->returnValue(new \stdClass()));

            new Application($this->configurations);
        }

        /**
         * Tests {@link Breeze\Application::register()} with an invalid closure as a plugin
         * throws an {@link UnexpectedValueException}.
         */
        public function testRegisterInvalidPlugin()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'You must provide a callable PHP function.');
            Application::register('bad plugin', 'this will not work');
        }

        /**
         * Tests {@link Breeze\Application::register()} with an invalid plugin name throws
         * an {@link UnexpectedValueException}.
         */
        public function testRegisterPluginWithInvalidName()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'You must provide a name.');
            Application::register('', function(){});
        }

        /**
         * Tests {@link Breeze\Application::register()} to register a plugin.
         */
        public function testRegisterPlugin()
        {
            $this->expectOutputString('testing');
            Application::register('test_plugin', function(){ echo "testing"; });
            $this->_mockApplication();

            Application::unregister('test_plugin');
        }

        /**
         * Tests {@link Breeze\Application::register()} to register a plugin which registers a helper.
         */
        public function testRegisterPluginWithHelper()
        {
            $closure = function(){};
            $this->mocks['helpers_object']->expects($this->once())
                                          ->method('add')
                                          ->with($this->equalTo('name'), $this->equalTo($closure), 'label');
            Application::register('test_plugin', function($app) use ($closure){
                $app->helper('name', $closure);
            });
            $this->_mockApplication();

            Application::unregister('test_plugin');
        }
    }

}