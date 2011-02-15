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

namespace Breeze\View\Tests;

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
     * @param Breeze\Application
     */
    protected $application;
    /**
     * The view object for testing.
     *
     * @param Breeze\View\View
     */
    protected $view;

    /**
     * Configuration values for testsing {@link Breeze\View\View}.
     *
     * @param array
     */
    protected $config = array(
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
     * @return void
     */
    public function setUp()
    {
        $this->application = $this->getMock(
            'Breeze\\Application', array(), array(), '', FALSE
        );
        $this->application->expects($this->any())
                          ->method('config')
                          ->will($this->returnCallback(array($this, 'getConfig')));

        $this->config['template_engine'] = $this->getMock(
            'Breeze\\View\\Driver\\PHP',
            array(),
            array($this->application),
            '',
            FALSE
        );
        $this->view = new View($this->application);
    }

    /**
     * Tests {@link Breeze\View\View::__get()} with a template variable that
     * hasn't been set.
     */
    public function testGetWithUnsetVariable()
    {
        $this->assertNull($this->view->does_not_exist);
    }

    /**
     * Tests {@link Breeze\View\View::__get()} with a template variable that
     * has been set.
     */
    public function testGetWithSetVariable()
    {
        $this->view->addVariables(array('this_is_a' => 'test'));
        $this->assertSame('test', $this->view->this_is_a);
    }

    /**
     * Tests {@link Breeze\View\View::__isset()} with a template variable
     * that has been set.
     */
    public function testIssetWithSetVariable()
    {
        $this->view->addVariables(array('this_is_a' => 'test'));
        $this->assertTrue(isset($this->view->this_is_a));
    }

    /**
     * Tests {@link Breeze\View\View::__isset()} with a template variable
     * that has not been set.
     */
    public function testIssetWithUnsetVariable()
    {
        $this->assertFalse(isset($this->view->does_not_exist));
    }

    /**
     * Tests {@link Breeze\View\View::__unset()} to unset a set template
     * variable.
     */
    public function testUnsetVariable()
    {
        $this->view->addVariables(array('this_is_a' => 'test'));
        $this->assertTrue(isset($this->view->this_is_a));

        unset($this->view->this_is_a);
        $this->assertFalse(isset($this->view->this_is_a));
    }

    /**
     * Tests {@link Breeze\View\View::__set()} to set a template variable.
     */
    public function testSet()
    {
        $this->view->this_is_a = 'test';
        $this->assertSame('test', $this->view->this_is_a);
    }

    /**
     * Tests {@link Breeze\View\View::getEngine()} with an bad template
     * engine.
     */
    public function testGetEngineWithBadEngine()
    {
        $this->setExpectedException(
            '\\UnexpectedValueException', 'is not a valid template engine.'
        );
        $this->config['template_engine'] = 'INVALID';
        $this->view->getEngine();
    }

    /**
     * Tests {@link Breeze\View\View::getEngine()} with a good template
     * engine.
     */
    public function testGetEngineWithGoodEngine()
    {
        $this->assertInstanceOf(
            'Breeze\\View\\Driver\\DriverInterface', $this->view->getEngine()
        );
    }

    /**
     * Tests {@link Breeze\View\View::getEngine()} with a string-based engine.
     */
    public function testGetEngineWithString()
    {
        $this->config['template_engine'] = 'Tests\\Stub';
        $this->assertInstanceOf(
            'Breeze\\View\\Driver\\Tests\\Stub', $this->view->getEngine()
        );
    }

    /**
     * Tests {@link Breeze\View\View::getEngine()} caches the engine if
     * no options have changed.
     */
    public function testGetEngineWithStringCaches()
    {
        $this->config['template_engine'] = 'Tests\\Stub';
        $this->assertSame($this->view->getEngine(), $this->view->getEngine());
    }

    /**
     * Tests {@link Breeze\View\View::layoutExists()} with no set layout.
     */
    public function testLayoutExistsWithNoLayoutSet()
    {
        $this->config['template_layout'] = '';
        $this->assertFalse($this->view->layoutExists());
    }

    /**
     * Tests {@link Breeze\View\View::layoutExists()} with an invalid layout.
     */
    public function testLayoutExistsWithInvalidPath()
    {
        $this->config['template_layout'] = 'DOES NOT EXIST';
        $this->assertFalse($this->view->layoutExists());
    }

    /**
     * Tests {@link Breeze\View\View::layoutExists()} with a valid path.
     */
    public function testLayoutExistsWithValidPath()
    {
        $this->config['template_engine']->expects($this->once())
                                         ->method('templateExists')
                                         ->with($this->equalTo('layout.php'))
                                         ->will($this->returnValue(true));

        $this->assertTrue($this->view->layoutExists());
    }

    /**
     * Tests {@link Breeze\View\View::layout()} calls
     * {@link Breeze\Application::config()} to set the 'template_layout' option.
     */
    public function testLayout()
    {
        $this->application->expects($this->at(0))
                           ->method('config')
                           ->with(
                               $this->equalTo('template_layout'),
                               $this->equalTo('layout')
                             );
        $this->view->layout('layout');
    }

    /**
     * Tests {@link Breeze\View\View::fetchLayout()} to get wrap provided
     * contents in the layout wrapper.
     */
    public function testFetchLayout()
    {
        $this->config['template_engine']->expects($this->once())
                                        ->method('fetch')
                                        ->with(
                                            $this->equalTo('layout.php'),
                                            array('layout_contents'=>'My Contents')
                                          )
                                        ->will($this->returnValue('¡My Contents!'));

        $this->assertSame('¡My Contents!', $this->view->fetchLayout('My Contents'));
    }

    /**
     * Tests {@link Breeze\View\View::fetch()} with no layout set.
     */
    public function testFetchWithoutLayout()
    {
        $this->config['template_layout'] = '';
        $this->mockTemplate();

        $this->assertSame('Hello Jeff', $this->view->fetch(
            'template', array('name'=>'Jeff')
        ));
    }

    /**
     * Tests {@link Breeze\View\View::fetch()} with a layout set.
     */
    public function testFetchWithLayout()
    {
        $this->mockTemplate();
        $this->mockLayout();

        $this->assertSame('¡Hello Jeff!', $this->view->fetch(
            'template', array('name'=>'Jeff')
        ));
    }

    /**
     * Tests {@link Breeze\View\View::display()} with no layout set.
     */
    public function testDisplayWithoutLayout()
    {
        $this->expectOutputString('Hello Jeff');
        $this->config['template_layout'] = '';

        $this->mockTemplate();
        $this->view->display('template', array('name'=>'Jeff'));
    }

    /**
     * Tests {@link Breeze\View\View::display()} with a layout set.
     */
    public function testDisplayWithLayout()
    {
        $this->expectOutputString('¡Hello Jeff!');

        $this->mockTemplate();
        $this->mockLayout();

        $this->view->display('template', array('name'=>'Jeff'));
    }

    /**
     * Tests {@link Breeze\View\View::partial()} doesn't use a layout.
     */
    public function testPartialDoesntUseLayout()
    {
        $this->mockTemplate();
        $this->assertSame('Hello Jeff', $this->view->partial(
            'template', array('name'=>'Jeff')
        ));
    }

    /**
     * Tests {@link Breeze\View\View::partial()} doesn't change the
     * layout preferences.
     */
    public function testPartialDoesntDestoryLayoutPreferences()
    {
        $this->view->partial('template', array('name'=>'Jeff'));
        $this->assertSame('layout', $this->application->config('template_layout'));
    }

    /**
     * Configurations callback to mock {@link Breeze\Configurations}.
     *
     * @param string $key   The name of the configuration value to get/set.
     * @param mixed  $value The config value to set
     *
     * @return mixed
     */
    public function getConfig($key, $value = null)
    {
        if(isset($value)) {
            $this->config[$key] = $value;
        } else {
            return isset($this->config[$key]) ? $this->config[$key] : null;
        }
    }

    /**
     * Mocks an expected {@link Breeze\View\Driver\Driver::fetch()} call
     * to simulate fetching a template with some standard contents.
     *
     * @return void
     */
    protected function mockTemplate()
    {
        $this->config['template_engine']->expects($this->at(0))
                                        ->method('fetch')
                                        ->with(
                                            $this->equalTo('template.php'),
                                            $this->equalTo(array(
                                                'name'=>'Jeff',
                                                'breeze'=>$this->application)
                                            )
                                          )
                                        ->will($this->returnValue('Hello Jeff'));
    }

    /**
     * Mocks an expected {@link Breeze\View\Driver\Driver::templateExists()}
     * and subsequent {@link Breeze\View\Driver\Driver::fetch()} to simulate
     * fetching a layout with some standard contents.
     *
     * @return void
     */
    protected function mockLayout()
    {
        $this->config['template_engine']->expects($this->at(1))
                                        ->method('templateExists')
                                        ->with($this->equalTo('layout.php'))
                                        ->will($this->returnValue(true));
        $this->config['template_engine']->expects($this->at(2))
                                        ->method('fetch')
                                        ->with(
                                            $this->equalTo('layout.php'),
                                            $this->equalTo(array(
                                                'name'=>'Jeff',
                                                'breeze'=>$this->application,
                                                'layout_contents'=>'Hello Jeff')
                                            )
                                          )
                                        ->will($this->returnValue('¡Hello Jeff!'));
    }
}


namespace Breeze\View\Driver\Tests;

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
     * @param Breeze\Application $application A Breeze application
     * @param string             $path        The path to the templates directory
     * @param array              $options     Extra options for custom engines
     *
     * @return void
     */
    public function __construct(Application $application, $path = null,
        array $options = array()
    ) {
    }

    /**
     * Updates the template engine if changes to the template-related
     * configurations have changed.
     *
     * @return void
     */
    public function updateConfig()
    {
    }

    /**
     * Sets up the internal template engine structures.  This is intended
     * to be where engine specific options are set up.
     *
     * @return void
     */
    protected function config()
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
    protected function fetchTemplate($template, array $variables = array())
    {
    }
}