<?php
/**
 * Breeze Framework - Smarty view driver test case
 *
 * This file contains the {@link Breeze\View\Driver\Tests\SmartyTest} class.
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

namespace Breeze\View\Driver\Tests {

    /**
     * @see Breeze\Application
     */
    use Breeze\Application;

    /**
     * @see Breeze\View\Driver\Smarty
     */
    use Breeze\View\Driver\Smarty;

    /**
     * @see Breeze\Plugins\Tests\PluginTestCase
     */
    use Breeze\Plugins\Tests\PluginTestCase;

    /**
     * The test case for the {@link Breeze\View\Driver\Smarty} class.
     *
     * @category    Breeze
     * @package     View
     * @subpackage  Tests
     */
    class SmartyTest extends PluginTestCase
    {
        /**
         * The path to the plugin file.
         *
         * @access protected
         * @param  string
         */
        static protected $_plugin_path = 'Breeze/plugins/Smarty.php';
        /**
         * The name of the plugin
         *
         * @access protected
         * @param  string
         */
        static protected $_plugin_name = 'Smarty';

        /**
         * The driver object for testing.
         *
         * @access protected
         * @param  Breeze\View\Driver\Smarty
         */
        protected $_driver;

        /**
         * Sets up the test case for {@link Breeze\View\Driver\Smarty}.
         *
         * @access public
         * @return void
         */
        public function setUp()
        {
            if (!\Breeze\Tests\TEST_SMARTY) {
                $this->markTestSkipped('Smarty is not available for testing');
            }

            $this->_application = $this->getMock('Breeze\\Application', array(), array(), '', FALSE);
            $this->_driver = new Smarty($this->_application, \Breeze\Tests\FIXTURES_PATH . '/Smarty');
        }

        /**
         * Tests {@link Breeze\View\Driver\Smarty::fetch()} with an invalid template.
         */
        public function testFetchWithInvalidTemplate()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'is not a valid template.');
            $this->_driver->fetch('DOES NOT EXIST');
        }

        /**
         * Tests {@link Breeze\View\Driver\Smarty::fetch()} without variables.
         */
        public function testFetchWithNoVariables()
        {
            $this->assertSame('Hello World', $this->_driver->fetch('template.tpl'));
        }

        /**
         * Tests {@link Breeze\View\Driver\Smarty::fetch()} with variables.
         */
        public function testFetchWithVariables()
        {
            $this->assertSame('Hello Jeff', $this->_driver->fetch('template.tpl', array('name'=>'Jeff')));
        }

        /**
         * Tests {@link Breeze\View\Driver\Smarty::partial()} without a specified file.
         */
        public function testPartialWithoutFile()
        {
            $this->setExpectedException('\\PHPUnit_Framework_Error', 'Smarty error: [partial] missing parameter \'file\'');
            $this->_driver->partial(array(), $this->getMock('Smarty'));
        }

        /**
         * Tests {@link Breeze\View\Driver\Smarty::partial()} without variables.
         */
        public function testPartialWithoutVariables()
        {
            $this->_application->expects($this->once())
                               ->method('__call')
                               ->with($this->equalTo('partial'), $this->equalTo(array('template.tpl', array())))
                               ->will($this->returnValue('Hello World'));
            $this->assertSame('Hello World', $this->_driver->partial(array('file'=>'template.tpl'), $this->getMock('Smarty')));
        }

        /**
         * Tests {@link Breeze\View\Driver\Smarty::partial()} with variables.
         */
        public function testPartialWithVariables()
        {
            $this->_application->expects($this->once())
                               ->method('__call')
                               ->with($this->equalTo('partial'), $this->equalTo(array('template.tpl', array('name'=>'Jeff'))))
                               ->will($this->returnValue('Hello Jeff'));
            $this->assertSame('Hello Jeff', $this->_driver->partial(array('file'=>'template.tpl', 'name'=>'Jeff'), $this->getMock('Smarty')));
        }

        /**
         * Tests that the plugin was loaded correctly.
         */
        public function testPluginLoaded()
        {
            $config = array(
                'template_engine'    => 'Smarty',
                'template_extension' => '.tpl',
                'template_options'   => array(
                    'compile_dir' => 'compiled',
                    'cache_dir'   => 'cache',
                    'config_dir'  => 'config')
            );

            $this->_setupMockedDependencies();
            $this->_configurations->expects($this->once())
                                  ->method('set')
                                  ->with($this->equalTo($config));
            $this->_mockApplication();
        }
    }
}