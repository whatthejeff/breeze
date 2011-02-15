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

namespace Breeze\Tests;

/**
 * @see Breeze\Application
 */
use Breeze\Application;

/**
 * The generic test case for the {@link Breeze\Application} class.
 *
 * @package    Breeze
 * @subpackage Tests
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */
class ApplicationTestCase extends \PHPUnit_Extensions_OutputTestCase
{
    /**
     * The application object for testing.
     *
     * @param Breeze\Application
     */
    protected $application;
    /**
     * The application configurations for
     * {@link Breeze\Tests\ApplicationTest::$application} that will hold the mocked
     * dependencies.
     *
     * @param Breeze\Configurations
     */
    protected $configurations;
    /**
     * The mocked dependencies for
     * {@link Breeze\Tests\ApplicationTest::$application}.
     *
     * @param array
     */
    protected $mocks = array();

    /**
     * Sets up mocks for testing Breeze\Application.
     *
     * @return void
     */
    protected function setupMockedDependencies()
    {
        $this->mocks['view_object'] = $this->getMock(
            'Breeze\\View\\View', array(), array(), '', FALSE
        );
        $this->mocks['errors_object'] = $this->getMock(
            'Breeze\\Errors\\Errors', array(), array(), '', FALSE
        );
        $this->mocks['dispatcher_object'] = $this->getMock(
            'Breeze\\Dispatcher\\Dispatcher', array(), array(), '', FALSE
        );
        $this->mocks['conditions_object'] = $this->getMock(
            'Breeze\\Dispatcher\\Conditions', array(), array(), '', FALSE
        );
        $this->mocks['helpers_object'] = $this->getMock(
            'Breeze\\ClosuresCollection', array(), array(), '', FALSE
        );
        $this->mocks['before_filters_object'] = $this->getMock(
            'Breeze\\ClosuresCollection', array(), array(), '', FALSE
        );
        $this->mocks['after_filters_object'] = $this->getMock(
            'Breeze\\ClosuresCollection', array(), array(), '', FALSE
        );

        $this->configurations = $this->getMock(
            'Breeze\\Configurations', array(), array(), '', FALSE
        );
    }

    /**
     * Get an instance of Breeze\Application with mocked dependencies injected.
     *
     * @return void
     */
    protected function mockApplication()
    {
        $i = 0;
        foreach ($this->mocks as $mock) {
            $this->configurations->expects($this->at($i++))
                                 ->method('get')
                                 ->will($this->returnValue($mock));
        }
        $this->application = new Application($this->configurations);
    }
}