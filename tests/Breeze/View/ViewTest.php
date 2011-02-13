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
 * @package    Breeze
 * @subpackage Tests
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */

namespace Breeze\View\Tests {

    /**
     * @see Breeze\View\View
     */
    use Breeze\View\View;

    /**
     * The test case for the {@link Breeze\View\View} class.
     *
     * @package    Breeze
     * @subpackage Tests
     * @author     Jeff Welch <whatthejeff@gmail.com>
     * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
     * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
     * @link       http://breezephp.com/
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
            'template_engine'       => '',
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

            $this->_config['template_engine'] = $this->getMock('Breeze\\View\\Driver\\PHP', array(), array($this->_application), '', FALSE);
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
            $this->assertInstanceOf('Breeze\\View\\Driver\\DriverInterface', $this->_view->getEngine());
        }

        /**
         * Tests {@link Breeze\View\View::getEngine()} with a string-based engine.
         */
        public function testGetEngineWithString()
        {
            $this->_config['template_engine'] = 'Tests\\Stub';
            $this->assertInstanceOf('Breeze\\View\\Driver\\Tests\\Stub', $this->_view->getEngine());
        }

        /**
         * Tests {@link Breeze\View\View::getEngine()} caches the engine if
         * no options have changed.
         */
        public function testGetEngineWithStringCaches()
        {
            $this->_config['template_engine'] = 'Tests\\Stub';
            $this->assertSame($this->_view->getEngine(), $this->_view->getEngine());
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
            $this->_config['template_engine']->expects($this->once())
                                             ->method('templateExists')
                                             ->with($this->equalTo('layout.php'))
                                             ->will($this->returnValue(true));

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
            $this->_config['template_engine']->expects($this->once())
                                             ->method('fetch')
                                             ->with($this->equalTo('layout.php'), array('layout_contents'=>'My Contents'))
                                             ->will($this->returnValue('¡My Contents!'));

            $this->assertSame('¡My Contents!', $this->_view->fetchLayout('My Contents'));
        }

        /**
         * Tests {@link Breeze\View\View::fetch()} with no layout set.
         */
        public function testFetchWithoutLayout()
        {
            $this->_config['template_layout'] = '';
            $this->_mockTemplate();

            $this->assertSame('Hello Jeff', $this->_view->fetch('template', array('name'=>'Jeff')));
        }

        /**
         * Tests {@link Breeze\View\View::fetch()} with a layout set.
         */
        public function testFetchWithLayout()
        {
            $this->_mockTemplate();
            $this->_mockLayout();

            $this->assertSame('¡Hello Jeff!', $this->_view->fetch('template', array('name'=>'Jeff')));
        }

        /**
         * Tests {@link Breeze\View\View::display()} with no layout set.
         */
        public function testDisplayWithoutLayout()
        {
            $this->expectOutputString('Hello Jeff');
            $this->_config['template_layout'] = '';

            $this->_mockTemplate();
            $this->_view->display('template', array('name'=>'Jeff'));
        }

        /**
         * Tests {@link Breeze\View\View::display()} with a layout set.
         */
        public function testDisplayWithLayout()
        {
            $this->expectOutputString('¡Hello Jeff!');

            $this->_mockTemplate();
            $this->_mockLayout();

            $this->_view->display('template', array('name'=>'Jeff'));
        }

        /**
         * Tests {@link Breeze\View\View::partial()} doesn't use a layout.
         */
        public function testPartialDoesntUseLayout()
        {
            $this->_mockTemplate();
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

        /**
         * Mocks an expected {@link Breeze\View\Driver\Driver::fetch()} call
         * to simulate fetching a template with some standard contents.
         *
         * @access protected
         * @return void
         */
        protected function _mockTemplate()
        {
            $this->_config['template_engine']->expects($this->at(0))
                                             ->method('fetch')
                                             ->with($this->equalTo('template.php'), $this->equalTo(array('name'=>'Jeff', 'breeze'=>$this->_application)))
                                             ->will($this->returnValue('Hello Jeff'));
        }

        /**
         * Mocks an expected {@link Breeze\View\Driver\Driver::templateExists()}
         * and subsequent {@link Breeze\View\Driver\Driver::fetch()} to simulate
         * fetching a layout with some standard contents.
         *
         * @access protected
         * @return void
         */
        protected function _mockLayout()
        {
            $this->_config['template_engine']->expects($this->at(1))
                                             ->method('templateExists')
                                             ->with($this->equalTo('layout.php'))
                                             ->will($this->returnValue(true));
            $this->_config['template_engine']->expects($this->at(2))
                                             ->method('fetch')
                                             ->with($this->equalTo('layout.php'), $this->equalTo(array('name'=>'Jeff', 'breeze'=>$this->_application, 'layout_contents'=>'Hello Jeff')))
                                             ->will($this->returnValue('¡Hello Jeff!'));
        }
    }
}

namespace Breeze\View\Driver\Tests {

    /**
     * @see Breeze\View\Driver\Driver
     */
    use Breeze\View\Driver\Driver;

    /**
     * @see Breeze\Application
     */
    use Breeze\Application;

    /**
     * A stub for testing a string-based engine config.
     *
     * @package    Breeze
     * @subpackage Tests
     * @author     Jeff Welch <whatthejeff@gmail.com>
     * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
     * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
     * @link       http://breezephp.com/
     */
    class Stub extends Driver {

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
        public function __construct(Application $application, $path = null, array $options = array()) {}

        /**
         * Sets up the internal template engine structures.  This is intended
         * to be where engine specific options are set up.
         *
         * @access public
         * @return void
         */
        public function config() {}

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
        protected function _fetch($template, array $variables = array()){}
    }
}