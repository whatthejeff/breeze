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
 * @package    Breeze
 * @subpackage Tests
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
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
     * @package    Breeze
     * @subpackage Tests
     * @author     Jeff Welch <whatthejeff@gmail.com>
     * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
     * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
     * @link       http://breezephp.com/
     */
    class PluginTestCase extends ApplicationTestCase
    {
        /**
         * The path to the plugin file.
         *
         * @param string
         */
        static protected $plugin_path = '';
        /**
         * The name of the plugin
         *
         * @param string
         */
        static protected $plugin_name = '';

        /**
         * Includes the plugin for testing.
         *
         * @return void
         */
        public static function setUpBeforeClass()
        {
            $constant = '\\Breeze\\Tests\\TEST_' . strtoupper(static::$plugin_name);
            if (!defined($constant) || constant($constant)) {
                require_once static::$plugin_path;
            }
        }

        /**
         * Removes the plugin so it doesn't mess with other tests.
         *
         * @return void
         */
        public static function tearDownAfterClass()
        {
            Application::unregister(static::$plugin_name);
        }

        /**
         * Sets it up so that calling a plugin will work.
         *
         * @return void
         */
        protected function _mockPluginSystem()
        {
            $test = $this;
            $this->mocks['helpers_object']->expects($this->any())
                                          ->method('add')
                                          ->will($this->returnCallback(function($name, $value) use ($test){
                                              $test->helpers[$name] = $value;
                                            }));
            $this->mocks['helpers_object']->expects($this->any())
                                          ->method('has')
                                          ->will($this->returnCallback(function($name) use ($test){
                                              return isset($test->helpers[$name]);
                                            }));
            $this->mocks['helpers_object']->expects($this->any())
                                          ->method('get')
                                          ->will($this->returnCallback(function($name) use ($test){
                                              return $test->helpers[$name];
                                            }));

            $this->mocks['view_object']->expects($this->any())
                                       ->method('__set')
                                       ->will($this->returnCallback(function($name, $value) use ($test){
                                           $test->views[$name] = $value;
                                         }));
            $this->mocks['view_object']->expects($this->any())
                                       ->method('__get')
                                       ->will($this->returnCallback(function($name) use ($test){
                                           return $test->views[$name];
                                         }));
        }
    }

}