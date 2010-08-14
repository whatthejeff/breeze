<?php
/**
 * Breeze Framework - View Driver test case.
 *
 * This file contains the {@link Breeze\View\Driver\Tests\DriverTest} class.
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
     * @see Breeze\View\Driver\Driver
     */
    use Breeze\View\Driver\Driver;

    /**
     * The test case for the {@link Breeze\View\Driver\Driver} class.
     *
     * @category    Breeze
     * @package     View
     * @subpackage  Tests
     */
    class DriverTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * The driver object for testing.
         *
         * @access protected
         * @param  Breeze\View\Driver\Driver
         */
        protected $_driver;
        /**
         * The application stub for testing {@link Breeze\View\Driver\Driver}.
         *
         * @access protected
         * @param  Breeze\Application
         */
        protected $_application;
        /**
         * Options to use for testing the setting of options with
         * {@link Breeze\View\Driver\Driver}.
         *
         * @access protected
         * @param  Breeze\Application
         * @static
         */
        protected static $_options = array('option1'=>'value1', 'option2'=>'value2');

        /**
         * Sets up the test case for {@link Breeze\View\Driver\Driver}.
         *
         * @access public
         * @return void
         */
        public function setUp()
        {
            $this->_application = $this->getMock('Breeze\\Application', array(), array(), '', FALSE);
            $this->_driver = $this->getMockForAbstractClass('Breeze\\View\\Driver\\Driver', array($this->_application));
        }

        /**
         * Tests {@link Breeze\View\Driver\Driver::setPath()} with an invalid path.
         */
        public function testSetPathWithInvalidPath()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'is not a valid template path.');
            $this->_driver->setPath('DOES NOT EXIST');
        }

        /**
         * Tests {@link Breeze\View\Driver\Driver::setPath()} with a valid path.
         */
        public function testSetPathWithValidPath()
        {
            $this->_driver->setPath(\Breeze\Tests\FIXTURES_PATH);
            $this->assertSame(\Breeze\Tests\FIXTURES_PATH, $this->_driver->getPath());
        }

        /**
         * Tests {@link Breeze\View\Driver\Driver::__construct()} with an invalid path.
         */
        public function testSetPathWithInvalidPathWithConstructor()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'is not a valid template path.');
            $this->_driver = $this->getMockForAbstractClass('Breeze\\View\\Driver\\Driver', array($this->_application, 'DOES NOT EXIST'));
        }

        /**
         * Tests {@link Breeze\View\Driver\Driver::__construct()} with a valid path.
         */
        public function testSetPathWithValidPathWithConstructor()
        {
            $this->_driver = $this->getMockForAbstractClass('Breeze\\View\\Driver\\Driver', array($this->_application, \Breeze\Tests\FIXTURES_PATH));
            $this->assertSame(\Breeze\Tests\FIXTURES_PATH, $this->_driver->getPath());
        }

        /**
         * Tests {@link Breeze\View\Driver\Driver::templateExists()} with an invalid path.
         */
        public function testTemplateExistsWithInvalidTemplate()
        {
            $this->_driver->setPath(\Breeze\Tests\FIXTURES_PATH);
            $this->assertFalse($this->_driver->templateExists('DOES NOT EXIST'));
        }

        /**
         * Tests {@link Breeze\View\Driver\Driver::templateExists()} with a valid path.
         */
        public function testTemplateExistsWithValidTemplate()
        {
            $this->_driver->setPath(\Breeze\Tests\FIXTURES_PATH);
            $this->assertTrue($this->_driver->templateExists('template.php'));
        }

        /**
         * Tests {@link Breeze\View\Driver\Driver::getTemplatePath()} to get the path
         * to a template.
         */
        public function testGetTemplatePath()
        {
            $this->_driver->setPath(\Breeze\Tests\FIXTURES_PATH);
            $this->assertSame(\Breeze\Tests\FIXTURES_PATH . "/template.php", $this->_driver->getTemplatePath('template.php'));
        }

        /**
         * Tests {@link Breeze\View\Driver\Driver::setOptions()} to set options for
         * the current driver.
         */
        public function testSetOptions()
        {
            $this->_driver->setOptions(self::$_options);

            foreach (self::$_options as $option => $value) {
                $this->assertSame($value, $this->_driver->getOption($option));
            }
        }

        /**
         * Tests {@link Breeze\View\Driver\Driver::__construct()} to set options for
         * the current driver.
         */
        public function testSetOptionsWithConstructor()
        {
            $this->_driver = $this->getMockForAbstractClass('Breeze\\View\\Driver\\Driver', array($this->_application, null, self::$_options));

            foreach (self::$_options as $option => $value) {
                $this->assertSame($value, $this->_driver->getOption($option));
            }
        }

        /**
         * Tests {@link Breeze\View\Driver\Driver::getOption()} to get the specified
         * default option for the current driver.
         */
        public function testGetOptionWithDefault()
        {
            $this->assertSame('default', $this->_driver->getOption('DOES NOT EXIST', 'default'));
        }

        /**
         * Tests {@link Breeze\View\Driver\Driver::getOption()} to get an unset option
         * with no default specified.
         */
        public function testGetUnsetOption()
        {
            $this->assertNull($this->_driver->getOption('DOES NOT EXIST'));
        }

        /**
         * Tests {@link Breeze\View\Driver\Driver::fetch()} with an invalid template path.
         */
        public function testFetchWithInvalidTemplate()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'is not a valid template.');
            $this->_driver->setPath(\Breeze\Tests\FIXTURES_PATH);
            $this->_driver->fetch('DOES NOT EXIST');
        }

        /**
         * Tests {@link Breeze\View\Driver\Driver::fetch()} with a valid template.
         */
        public function testFetchWithValidTemplate()
        {
            $this->_driver->expects($this->once())
                          ->method('_fetch')
                          ->will($this->returnValue('contents'));
            $this->_driver->setPath(\Breeze\Tests\FIXTURES_PATH);
            $this->assertSame('contents', $this->_driver->fetch('template.php'));
        }

        /**
         * Tests {@link Breeze\View\Driver\Driver::__construct()} calls
         * {@link Breeze\View\Driver\Driver::_config()}.
         */
        public function testConstructorCallsConfig()
        {
            $this->_driver->expects($this->once())
                          ->method('_config');
            $this->_driver->__construct($this->_application);
        }

        /**
         * Tests {@link Breeze\View\Driver\Driver::config()} with options that have
         * changed will call {@link Breeze\View\Driver\Driver::_config()}.
         */
        public function testConfigWithDirtyOptions()
        {
            $this->_application->expects($this->once())
                               ->method('config')
                               ->with($this->equalTo('template_directory'))
                               ->will($this->returnValue(\Breeze\Tests\FIXTURES_PATH));

            $this->_driver->expects($this->once())
                          ->method('_config');
            $this->_driver->config();
        }

        /**
         * Tests {@link Breeze\View\Driver\Driver::config()} with options that have
         * not changed will not call {@link Breeze\View\Driver\Driver::_config()}.
         */
        public function testConfigWithCleanOptions()
        {
            $this->_application->expects($this->at(0))
                               ->method('config')
                               ->with($this->equalTo('template_directory'))
                               ->will($this->returnValue(\Breeze\Tests\FIXTURES_PATH));
            $this->_application->expects($this->at(1))
                               ->method('config')
                               ->with($this->equalTo('template_options'))
                               ->will($this->returnValue(array()));

            $this->_driver->expects($this->never())
                          ->method('_config');
            $this->_driver->setPath(\Breeze\Tests\FIXTURES_PATH);
            $this->_driver->config();
        }
    }
}