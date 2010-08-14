<?php
/**
 * Breeze Framework - View test case
 *
 * This file contains the {@link Breeze\View\Tests\ViewTest} class.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @category   Breeze
 * @package    View
 * @subpackage Tests
 * @copyright  Copyright (c) 2010, Breeze Framework
 * @license    New BSD License
 * @version    $Id$
 */

namespace Breeze\View\Tests {

    /**
     * @see Breeze\View\View
     */
    use Breeze\View\View;

    /**
     * The test case for the {@link Breeze\View\View} class.
     *
     * @category    Breeze
     * @package     View
     * @subpackage  Tests
     */
    class ViewTest extends \PHPUnit_Extensions_OutputTestCase
    {
        /**
         * The application stub for testing {@link Breeze\Application}.
         *
         * @access protected
         * @param  Breeze\Application
         */
        protected $_application;
        /**
         * The view object for testing.
         *
         * @access protected
         * @param  Breeze\View\View
         */
        protected $_view;

        /**
         * Configuration values for testsing {@link Breeze\View\View}.
         *
         * @access protected
         * @param  array
         */
        protected $_config = array(
            'template_engine'       => 'PHP',
            'template_options'      => array(),
            'template_directory'    => \Breeze\Tests\FIXTURES_PATH,
            'template_extension'    => '.php',
            'template_layout'       => 'layout',
            'application_variable'  => 'breeze',
            'errors_backtrace'      => true
        );

        /**
         * Sets up the test case for {@link Breeze\View\View}.
         *
         * @access public
         * @return void
         */
        public function setUp()
        {
            $this->_application = $this->getMock('Breeze\\Application', array(), array(), '', FALSE);
            $this->_application->expects($this->any())
                               ->method('config')
                               ->will($this->returnCallback(array($this, 'getConfig')));
            $this->_view = new View($this->_application);
        }

        /**
         * Tests {@link Breeze\View\View::__get()} with a template variable that
         * hasn't been set.
         */
        public function testGetWithUnsetVariable()
        {
            $this->assertNull($this->_view->does_not_exist);
        }

        /**
         * Tests {@link Breeze\View\View::__get()} with a template variable that
         * has been set.
         */
        public function testGetWithSetVariable()
        {
            $this->_view->addVariables(array('this_is_a' => 'test'));
            $this->assertSame('test', $this->_view->this_is_a);
        }

        /**
         * Tests {@link Breeze\View\View::__isset()} with a template variable
         * that has been set.
         */
        public function testIssetWithSetVariable()
        {
            $this->_view->addVariables(array('this_is_a' => 'test'));
            $this->assertTrue(isset($this->_view->this_is_a));
        }

        /**
         * Tests {@link Breeze\View\View::__isset()} with a template variable
         * that has not been set.
         */
        public function testIssetWithUnsetVariable()
        {
            $this->assertFalse(isset($this->_view->does_not_exist));
        }

        /**
         * Tests {@link Breeze\View\View::__unset()} to unset a set template
         * variable.
         */
        public function testUnsetVariable()
        {
            $this->_view->addVariables(array('this_is_a' => 'test'));
            $this->assertTrue(isset($this->_view->this_is_a));

            unset($this->_view->this_is_a);
            $this->assertFalse(isset($this->_view->this_is_a));
        }

        /**
         * Tests {@link Breeze\View\View::__set()} to set a template variable.
         */
        public function testSet()
        {
            $this->_view->this_is_a = 'test';
            $this->assertSame('test', $this->_view->this_is_a);
        }

        /**
         * Tests {@link Breeze\View\View::getEngine()} with an bad template
         * engine.
         */
        public function testGetEngineWithBadEngine()
        {
            $this->setExpectedException('\\UnexpectedValueException', 'is not a valid template engine.');
            $this->_config['template_engine'] = 'INVALID';
            $this->_view->getEngine();
        }

        /**
         * Tests {@link Breeze\View\View::getEngine()} with a good template
         * engine.
         */
        public function testGetEngineWithGoodEngine()
        {
            $this->assertType('Breeze\\View\\Driver\\DriverInterface', $this->_view->getEngine());
        }

        /**
         * Tests {@link Breeze\View\View::getEngine()} caches the engine if
         * no options have changed.
         */
        public function testGetEngineCaches()
        {
            $engine = $this->_view->getEngine();
            $this->assertSame($engine, $this->_view->getEngine());
        }

        /**
         * Tests {@link Breeze\View\View::layoutExists()} with no set layout.
         */
        public function testLayoutExistsWithNoLayoutSet()
        {
            $this->_config['template_layout'] = '';
            $this->assertFalse($this->_view->layoutExists());
        }

        /**
         * Tests {@link Breeze\View\View::layoutExists()} with an invalid layout.
         */
        public function testLayoutExistsWithInvalidPath()
        {
            $this->_config['template_layout'] = 'DOES NOT EXIST';
            $this->assertFalse($this->_view->layoutExists());
        }

        /**
         * Tests {@link Breeze\View\View::layoutExists()} with a valid path.
         */
        public function testLayoutExistsWithValidPath()
        {
            $this->assertTrue($this->_view->layoutExists());
        }

        /**
         * Tests {@link Breeze\View\View::layout()} calls {@link Breeze\Application::config()}
         * to set the 'template_layout' option.
         */
        public function testLayout()
        {
            $this->_application->expects($this->at(0))
                               ->method('config')
                               ->with($this->equalTo('template_layout'), $this->equalTo('layout'));
            $this->_view->layout('layout');
        }

        /**
         * Tests {@link Breeze\View\View::fetchLayout()} to get wrap provided
         * contents in the layout wrapper.
         */
        public function testFetchLayout()
        {
            $this->assertSame('¡My Contents!', $this->_view->fetchLayout('My Contents'));
        }

        /**
         * Tests {@link Breeze\View\View::fetch()} with no layout set.
         */
        public function testFetchWithoutLayout()
        {
            $this->_config['template_layout'] = '';
            $this->assertSame('Hello Jeff', $this->_view->fetch('template', array('name'=>'Jeff')));
        }

        /**
         * Tests {@link Breeze\View\View::fetch()} with a layout set.
         */
        public function testFetchWithLayout()
        {
            $this->assertSame('¡Hello Jeff!', $this->_view->fetch('template', array('name'=>'Jeff')));
        }

        /**
         * Tests {@link Breeze\View\View::display()} with no layout set.
         */
        public function testDisplayWithoutLayout()
        {
            $this->expectOutputString('Hello Jeff');

            $this->_config['template_layout'] = '';
            $this->_view->display('template', array('name'=>'Jeff'));
        }

        /**
         * Tests {@link Breeze\View\View::display()} with a layout set.
         */
        public function testDisplayWithLayout()
        {
            $this->expectOutputString('¡Hello Jeff!');
            $this->_view->display('template', array('name'=>'Jeff'));
        }

        /**
         * Tests {@link Breeze\View\View::partial()} doesn't use a layout.
         */
        public function testPartialDoesntUseLayout()
        {
            $this->assertSame('Hello Jeff', $this->_view->partial('template', array('name'=>'Jeff')));
        }

        /**
         * Tests {@link Breeze\View\View::partial()} doesn't change the
         * layout preferences.
         */
        public function testPartialDoesntDestoryLayoutPreferences()
        {
            $this->_view->partial('template', array('name'=>'Jeff'));
            $this->assertSame('layout', $this->_application->config('template_layout'));
        }

        /**
         * Configurations callback to mock {@link Breeze\Configurations}.
         *
         * @access public
         * @param  string $key   The name of the configuration value to get/set.
         * @param  mixed $value  The config value to set
         * @return mixed
         */
        public function getConfig($key, $value = null)
        {
            if(isset($value)) {
                $this->_config[$key] = $value;
            } else {
                return isset($this->_config[$key]) ? $this->_config[$key] : null;
            }
        }
    }
}