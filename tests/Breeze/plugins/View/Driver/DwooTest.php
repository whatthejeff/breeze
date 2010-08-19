<?php
/**
 * Breeze Framework - Dwoo view driver test case
 *
 * This file contains the {@link Breeze\View\Driver\Tests\DwooTest} class.
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

namespace Breeze\View\Driver\Tests {

    /**
     * @see Breeze\Application
     */
    use Breeze\Application;

    /**
     * @see Breeze\View\Driver\Dwoo
     */
    use Breeze\View\Driver\Dwoo;

    /**
     * @see Breeze\Tests\ApplicationTestCase
     */
    use Breeze\Tests\ApplicationTestCase;

    /**
     * The test case for the {@link Breeze\View\Driver\Dwoo} class.
     *
     * @category    Breeze
     * @package     View
     * @subpackage  Tests
     */
    class DwooTest extends ApplicationTestCase
    {
        /**
         * The driver object for testing.
         *
         * @access protected
         * @param  Breeze\View\Driver\Dwoo
         */
        protected $_driver;

        /**
         * Sets up the test case for {@link Breeze\View\Driver\Dwoo}.
         *
         * @access public
         * @return void
         */
        public function setUp()
        {
            /**
             * @see Breeze\View\Driver\Dwoo
             */
            require_once 'Breeze/plugins/breeze.dwoo.php';
            Application::clearPlugins(array('Dwoo'));

            $this->_application = $this->getMock('Breeze\\Application', array(), array(), '', FALSE);
            $this->_driver = new Dwoo($this->_application, \Breeze\Tests\FIXTURES_PATH . '/Dwoo');
        }

        /**
         * Tests {@link Breeze\View\Driver\Dwoo::fetch()} with an invalid template.
         */
        public function testFetchWithInvalidTemplate()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'is not a valid template.');
            $this->_driver->fetch('DOES NOT EXIST');
        }

        /**
         * Tests {@link Breeze\View\Driver\Dwoo::fetch()} without variables.
         */
        public function testFetchWithNoVariables()
        {
            $this->assertSame('Hello World', $this->_driver->fetch('template.tpl'));
        }

        /**
         * Tests {@link Breeze\View\Driver\Dwoo::fetch()} with variables.
         */
        public function testFetchWithVariables()
        {
            $this->assertSame('Hello Jeff', $this->_driver->fetch('template.tpl', array('name'=>'Jeff')));
        }

        /**
         * Tests that the plugin was loaded correctly.
         */
        public function testPluginLoaded()
        {
            $config = array(
                'template_engine'    => 'Dwoo',
                'template_extension' => '.tpl',
                'template_options'   => array(
                    'compile_dir' => 'compiled',
                    'cache_dir'   => 'cache')
            );

            $this->_setupMockedDependencies();
            $this->_configurations->expects($this->once())
                                  ->method('set')
                                  ->with($this->equalTo($config));
            $this->_mockApplication();
        }
    }
}