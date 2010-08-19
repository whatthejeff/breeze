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
     * @see Breeze\View\Driver\Php
     */
    use Breeze\View\Driver\Php;

    /**
     * The test case for the {@link Breeze\View\Driver\Php} class.
     *
     * @category    Breeze
     * @package     View
     * @subpackage  Tests
     */
    class PhpTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * The application stub for testing {@link Breeze\View\Driver\Php}.
         *
         * @access protected
         * @param  Breeze\Application
         */
        protected $_application;
        /**
         * The driver object for testing.
         *
         * @access protected
         * @param  Breeze\View\Driver\Php
         */
        protected $_driver;

        /**
         * Sets up the test case for {@link Breeze\View\Driver\Php}.
         *
         * @access public
         * @return void
         */
        public function setUp()
        {
            $this->_application = $this->getMock('Breeze\\Application', array(), array(), '', FALSE);
            $this->_driver = new Php($this->_application, \Breeze\Tests\FIXTURES_PATH);
        }

        /**
         * Tests {@link Breeze\View\Driver\Php::fetch()} with an invalid template.
         */
        public function testFetchWithInvalidTemplate()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'is not a valid template.');
            $this->_driver->fetch('DOES NOT EXIST');
        }

        /**
         * Tests {@link Breeze\View\Driver\Php::fetch()} without variables.
         */
        public function testFetchWithNoVariables()
        {
            $this->assertSame('Hello World', $this->_driver->fetch('template.php'));
        }

        /**
         * Tests {@link Breeze\View\Driver\Php::fetch()} with variables.
         */
        public function testFetchWithVariables()
        {
            $this->assertSame('Hello Jeff', $this->_driver->fetch('template.php', array('name'=>'Jeff')));
        }
    }
}