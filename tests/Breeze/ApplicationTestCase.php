<?php
/**
 * Breeze Framework - Generic Application test case
 *
 * This file contains the {@link Breeze\Tests\ApplicationTestCase} class.
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
     * The generic test case for the {@link Breeze\Application} class.
     *
     * @category   Breeze
     * @package    Application
     * @subpackage Tests
     */
    class ApplicationTestCase extends \PHPUnit_Extensions_OutputTestCase
    {
        /**
         * The application object for testing.
         *
         * @access protected
         * @param  Breeze\Application
         */
        protected $_application;
        /**
         * The application configurations for {@link Breeze\Tests\ApplicationTest::$_application}
         * that will hold the mocked dependencies.
         *
         * @access protected
         * @param  Breeze\Configurations
         */
        protected $_configurations;
        /**
         * The mocked dependencies for {@link Breeze\Tests\ApplicationTest::$_application}.
         *
         * @access public
         * @param  array
         */
        protected $_mocks = array();

        /**
         * Sets up mocks for testing Breeze\Application.
         *
         * @access protected
         * @return void
         */
        protected function _setupMockedDependencies()
        {
            $this->_mocks['view_object'] = $this->getMock('Breeze\\View\\View', array(), array(), '', FALSE);
            $this->_mocks['errors_object'] = $this->getMock('Breeze\\Errors\\Errors', array(), array(), '', FALSE);
            $this->_mocks['dispatcher_object'] = $this->getMock('Breeze\\Dispatcher\\Dispatcher', array(), array(), '', FALSE);
            $this->_mocks['conditions_object'] = $this->getMock('Breeze\\Dispatcher\\Conditions', array(), array(), '', FALSE);
            $this->_mocks['helpers_object'] = $this->getMock('Breeze\\ClosuresCollection', array(), array(), '', FALSE);
            $this->_mocks['before_filters_object'] = $this->getMock('Breeze\\ClosuresCollection', array(), array(), '', FALSE);
            $this->_mocks['after_filters_object'] = $this->getMock('Breeze\\ClosuresCollection', array(), array(), '', FALSE);

            $this->_configurations = $this->getMock('Breeze\\Configurations', array(), array(), '', FALSE);
        }

        /**
         * Get an instance of Breeze\Application with mocked dependencies injected.
         *
         * @access protected
         * @return void
         */
        protected function _mockApplication()
        {
            $i = 0;
            foreach ($this->_mocks as $mock) {
                $this->_configurations->expects($this->at($i++))
                                      ->method('get')
                                      ->will($this->returnValue($mock));
            }
            $this->_application = new Application($this->_configurations);
        }
    }

}