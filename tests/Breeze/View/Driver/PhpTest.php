<?php
/**
 * Breeze Framework - PHP view driver test case
 *
 * This file contains the {@link Breeze\View\Driver\Tests\PhpTest} class.
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
     * @see Breeze\View\Driver\Php
     */
    use Breeze\View\Driver\Php;

    /**
     * The test case for the {@link Breeze\View\Driver\Php} class.
     *
     * @package    Breeze
     * @subpackage Tests
     * @author     Jeff Welch <whatthejeff@gmail.com>
     * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
     * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
     * @link       http://breezephp.com/
     */
    class PhpTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * The application stub for testing {@link Breeze\View\Driver\Php}.
         *
         * @param Breeze\Application
         */
        protected $application;
        /**
         * The driver object for testing.
         *
         * @param Breeze\View\Driver\Php
         */
        protected $driver;

        /**
         * Sets up the test case for {@link Breeze\View\Driver\Php}.
         *
         * @return void
         */
        public function setUp()
        {
            $this->application = $this->getMock('Breeze\\Application', array(), array(), '', FALSE);
            $this->driver = new Php($this->application, \Breeze\Tests\FIXTURES_PATH);
        }

        /**
         * Tests {@link Breeze\View\Driver\Php::fetch()} with an invalid template.
         */
        public function testFetchWithInvalidTemplate()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'is not a valid template.');
            $this->driver->fetch('DOES NOT EXIST');
        }

        /**
         * Tests {@link Breeze\View\Driver\Php::fetch()} without variables.
         */
        public function testFetchWithNoVariables()
        {
            $this->assertSame('Hello World', $this->driver->fetch('template.php'));
        }

        /**
         * Tests {@link Breeze\View\Driver\Php::fetch()} with variables.
         */
        public function testFetchWithVariables()
        {
            $this->assertSame('Hello Jeff', $this->driver->fetch('template.php', array('name'=>'Jeff')));
        }
    }
}