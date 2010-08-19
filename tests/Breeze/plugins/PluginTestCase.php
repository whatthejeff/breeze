<?php
/**
 * Breeze Framework - Generic Plugin test case
 *
 * This file contains the {@link Breeze\Plugins\Tests\PluginTestCase} class.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @category   Breeze
 * @package    Plugins
 * @subpackage Tests
 * @copyright  Copyright (c) 2010, Breeze Framework
 * @license    New BSD License
 * @version    $Id$
 */

namespace Breeze\Plugins\Tests {

    /**
     * @see Breeze\Application
     */
    use Breeze\Application;

    /**
     * @see Breeze\Tests\ApplicationTestCase
     */
    use Breeze\Tests\ApplicationTestCase;

    /**
     * The generic plugin test case.
     *
     * @category   Breeze
     * @package    Plugins
     * @subpackage Tests
     */
    class PluginTestCase extends ApplicationTestCase
    {
        /**
         * The path to the plugin file.
         *
         * @access protected
         * @param  string
         */
        static protected $_plugin_path = '';
        /**
         * The name of the plugin
         *
         * @access protected
         * @param  string
         */
        static protected $_plugin_name = '';

        /**
         * Includes the plugin for testing.
         *
         * @access public
         * @return void
         */
        public static function setUpBeforeClass()
        {
            require_once static::$_plugin_path;
        }

        /**
         * Removes the plugin so it doesn't mess with other tests.
         *
         * @access public
         * @return void
         */
        public static function tearDownAfterClass()
        {
            Application::unregister(static::$_plugin_name);
        }

        /**
         * Sets it up so that calling a plugin will work.
         *
         * @access protected
         * @return void
         */
        protected function _mockPluginSystem()
        {
            $test = $this;
            $this->_mocks['helpers_object']->expects($this->any())
                                           ->method('add')
                                           ->will($this->returnCallback(function($name, $value) use ($test){
                                                 $test->helpers[$name] = $value;
                                             }));
            $this->_mocks['helpers_object']->expects($this->any())
                                           ->method('has')
                                           ->will($this->returnCallback(function($name) use ($test){
                                                 return isset($test->helpers[$name]);
                                             }));
            $this->_mocks['helpers_object']->expects($this->any())
                                           ->method('get')
                                           ->will($this->returnCallback(function($name) use ($test){
                                                 return $test->helpers[$name];
                                             }));

            $this->_mocks['view_object']->expects($this->any())
                                        ->method('__set')
                                        ->will($this->returnCallback(function($name, $value) use ($test){
                                            $test->views[$name] = $value;
                                          }));
            $this->_mocks['view_object']->expects($this->any())
                                        ->method('__get')
                                        ->will($this->returnCallback(function($name) use ($test){
                                            return $test->views[$name];
                                          }));
        }
    }

}