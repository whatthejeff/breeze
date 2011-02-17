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
 * @package    Breeze
 * @subpackage Tests
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */

namespace Breeze\View\Driver\Tests;

/**
 * @see Breeze\View\Driver\Driver
 */
use Breeze\View\Driver\Driver;

/**
 * The test case for the {@link Breeze\View\Driver\Driver} class.
 *
 * @package    Breeze
 * @subpackage Tests
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */
class DriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The driver object for testing.
     *
     * @param Breeze\View\Driver\Driver
     */
    protected $driver;
    /**
     * The application stub for testing {@link Breeze\View\Driver\Driver}.
     *
     * @param Breeze\Application
     */
    protected $application;
    /**
     * Options to use for testing the setting of options with
     * {@link Breeze\View\Driver\Driver}.
     *
     * @param Breeze\Application
     */
    protected static $options = array('option1'=>'value1', 'option2'=>'value2');

    /**
     * Sets up the test case for {@link Breeze\View\Driver\Driver}.
     *
     * @return void
     */
    public function setUp()
    {
        $this->application = $this->getMock(
            'Breeze\\Application', array(), array(), '', FALSE
        );
        $this->driver = $this->getMockForAbstractClass(
            'Breeze\\View\\Driver\\Driver', array($this->application)
        );
    }

    /**
     * Tests {@link Breeze\View\Driver\Driver::setPath()} with an invalid path.
     */
    public function testSetPathWithInvalidPath()
    {
        $this->setExpectedException(
            '\\InvalidArgumentException', 'is not a valid template path.'
        );
        $this->driver->setPath('DOES NOT EXIST');
    }

    /**
     * Tests {@link Breeze\View\Driver\Driver::setPath()} with a valid path.
     */
    public function testSetPathWithValidPath()
    {
        $this->driver->setPath(\Breeze\Tests\FIXTURES_PATH);
        $this->assertSame(\Breeze\Tests\FIXTURES_PATH, $this->driver->getPath());
    }

    /**
     * Tests {@link Breeze\View\Driver\Driver::__construct()} with an invalid path.
     */
    public function testSetPathWithInvalidPathWithConstructor()
    {
        $this->setExpectedException(
            '\\InvalidArgumentException', 'is not a valid template path.'
        );
        $this->driver = $this->getMockForAbstractClass(
            'Breeze\\View\\Driver\\Driver',
            array($this->application, 'DOES NOT EXIST')
        );
    }

    /**
     * Tests {@link Breeze\View\Driver\Driver::__construct()} with a valid path.
     */
    public function testSetPathWithValidPathWithConstructor()
    {
        $this->driver = $this->getMockForAbstractClass(
            'Breeze\\View\\Driver\\Driver',
            array($this->application, \Breeze\Tests\FIXTURES_PATH)
        );
        $this->assertSame(\Breeze\Tests\FIXTURES_PATH, $this->driver->getPath());
    }

    /**
     * Tests {@link Breeze\View\Driver\Driver::templateExists()} with an invalid
     * path.
     */
    public function testTemplateExistsWithInvalidTemplate()
    {
        $this->driver->setPath(\Breeze\Tests\FIXTURES_PATH);
        $this->assertFalse($this->driver->templateExists('DOES NOT EXIST'));
    }

    /**
     * Tests {@link Breeze\View\Driver\Driver::templateExists()} with a valid path.
     */
    public function testTemplateExistsWithValidTemplate()
    {
        $this->driver->setPath(\Breeze\Tests\FIXTURES_PATH);
        $this->assertTrue($this->driver->templateExists('template.php'));
    }

    /**
     * Tests {@link Breeze\View\Driver\Driver::getTemplatePath()} to get the path
     * to a template.
     */
    public function testGetTemplatePath()
    {
        $this->driver->setPath(\Breeze\Tests\FIXTURES_PATH);
        $this->assertSame(
            \Breeze\Tests\FIXTURES_PATH . "/template.php",
            $this->driver->getTemplatePath('template.php')
        );
    }

    /**
     * Tests {@link Breeze\View\Driver\Driver::setOptions()} to set options for
     * the current driver.
     */
    public function testSetOptions()
    {
        $this->driver->setOptions(self::$options);

        foreach (self::$options as $option => $value) {
            $this->assertSame($value, $this->driver->getOption($option));
        }
    }

    /**
     * Tests {@link Breeze\View\Driver\Driver::__construct()} to set options for
     * the current driver.
     */
    public function testSetOptionsWithConstructor()
    {
        $this->driver = $this->getMockForAbstractClass(
            'Breeze\\View\\Driver\\Driver',
            array($this->application, null, self::$options)
        );

        foreach (self::$options as $option => $value) {
            $this->assertSame($value, $this->driver->getOption($option));
        }
    }

    /**
     * Tests {@link Breeze\View\Driver\Driver::getOption()} to get the specified
     * default option for the current driver.
     */
    public function testGetOptionWithDefault()
    {
        $this->assertSame(
            'default', $this->driver->getOption('DOES NOT EXIST', 'default')
        );
    }

    /**
     * Tests {@link Breeze\View\Driver\Driver::getOption()} to get an unset option
     * with no default specified.
     */
    public function testGetUnsetOption()
    {
        $this->assertNull($this->driver->getOption('DOES NOT EXIST'));
    }

    /**
     * Tests {@link Breeze\View\Driver\Driver::fetch()} with an invalid template
     * path.
     */
    public function testFetchWithInvalidTemplate()
    {
        $this->setExpectedException(
            '\\InvalidArgumentException', 'is not a valid template.'
        );
        $this->driver->setPath(\Breeze\Tests\FIXTURES_PATH);
        $this->driver->fetch('DOES NOT EXIST');
    }

    /**
     * Tests {@link Breeze\View\Driver\Driver::fetch()} with a valid template.
     */
    public function testFetchWithValidTemplate()
    {
        $this->driver->expects($this->once())
                     ->method('_fetchTemplate')
                     ->will($this->returnValue('contents'));
        $this->driver->setPath(\Breeze\Tests\FIXTURES_PATH);
        $this->assertSame('contents', $this->driver->fetch('template.php'));
    }

    /**
     * Tests {@link Breeze\View\Driver\Driver::__construct()} calls
     * {@link Breeze\View\Driver\Driver::_config()}.
     */
    public function testConstructorCallsConfig()
    {
        $this->driver->expects($this->once())
                     ->method('_config');
        $this->driver->__construct($this->application);
    }

    /**
     * Tests {@link Breeze\View\Driver\Driver::updateConfig()} with options that
     * have changed will call {@link Breeze\View\Driver\Driver::_config()}.
     */
    public function testConfigWithDirtyOptions()
    {
        $this->application->expects($this->at(0))
                          ->method('config')
                          ->with($this->equalTo('template_directory'))
                          ->will($this->returnValue(\Breeze\Tests\FIXTURES_PATH));
        $this->application->expects($this->at(1))
                          ->method('config')
                          ->with($this->equalTo('template_options'))
                          ->will($this->returnValue(array()));

        $this->driver->expects($this->once())
                     ->method('_config');
        $this->driver->updateConfig();
    }

    /**
     * Tests {@link Breeze\View\Driver\Driver::updateConfig()} with options that
     * have not changed will not call {@link Breeze\View\Driver\Driver::_config()}.
     */
    public function testConfigWithCleanOptions()
    {
        $this->application->expects($this->at(0))
                          ->method('config')
                          ->with($this->equalTo('template_directory'))
                          ->will($this->returnValue(\Breeze\Tests\FIXTURES_PATH));
        $this->application->expects($this->at(1))
                          ->method('config')
                          ->with($this->equalTo('template_options'))
                          ->will($this->returnValue(array()));

        $this->driver->expects($this->never())
                     ->method('_config');
        $this->driver->setPath(\Breeze\Tests\FIXTURES_PATH);
        $this->driver->updateConfig();
    }
}