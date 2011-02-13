<?php
/**
 * Breeze Framework - Flashhash test case
 *
 * This file contains the {@link Breeze\Plugins\Flashhash\Tests\FlashHashTest} class.
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

namespace Breeze\Plugins\Flashhash\Tests {

    /**
     * @see Breeze\Application
     */
    use Breeze\Application;

    /**
     * @see Breeze\Plugins\Tests\PluginTestCase
     */
    use Breeze\Plugins\Tests\PluginTestCase;

    /**
     * @see Breeze\Plugins\Flashhash\Flashhash
     */
    use Breeze\Plugins\Flashhash\Flashhash;

    /**
     * The test case for the {@link Breeze\Plugins\Flashhash} class.
     *
     * @package    Breeze
     * @subpackage Tests
     * @author     Jeff Welch <whatthejeff@gmail.com>
     * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
     * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
     * @link       http://breezephp.com/
     */
    class FlashHashTest extends PluginTestCase
    {
        /**
         * The path to the plugin file.
         *
         * @param  string
         */
        static protected $_plugin_path = 'Breeze/plugins/Flashhash.php';
        /**
         * The name of the plugin
         *
         * @param string
         */
        static protected $_plugin_name = 'Flashhash';

        /**
         * The flashhash object for testing.
         *
         * @param Breeze\Plugins\Flashhash
         */
        protected $_flashhash;

        /**
         * Sets up the test case for {@link Breeze\Plugins\Flashhash}.
         *
         * @return void
         */
        public function setUp()
        {
            $this->_flashhash = new FlashHash();
        }

        /**
         * Tests {@link Breeze\Plugins\Flashhash::offsetSet()} to set a flashhash
         * value.
         */
        public function testOffsetSet()
        {
            $this->_flashhash['key'] = 'value';
            $this->assertSame('value', $this->_flashhash['key']);
        }

        /**
         * Tests {@link Breeze\Plugins\Flashhash::offsetExists()} when no offset
         * exists.
         */
        public function testOffsetExistsWhenNoOffsetExists()
        {
            $this->assertFalse(isset($this->_flashhash['key']));
        }

        /**
         * Tests {@link Breeze\Plugins\Flashhash::offsetExists()} when offset
         * exists.
         */
        public function testOffsetExistsWhenOffsetExists()
        {
            $this->_flashhash['key'] = 'value';
            $this->assertTrue(isset($this->_flashhash['key']));
        }

        /**
         * Tests {@link Breeze\Plugins\Flashhash::offsetUnset()} to unset an
         * offset.
         */
        public function testOffsetUnset()
        {
            $this->_flashhash['key'] = 'value';
            unset($this->_flashhash['key']);

            $this->assertFalse(isset($this->_flashhash['key']));
        }

        /**
         * Tests {@link Breeze\Plugins\Flashhash::asArray()} to get an entire
         * flashhash as an array.
         */
        public function testAsArray()
        {
            $this->_flashhash['key'] = 'value';
            $this->assertSame(array('key' => 'value'), $this->_flashhash->asArray());
        }

        /**
         * Tests a sesssion with the key 'flashhash' fills all new flashhash
         * instances with no specified key.
         */
        public function testFlashHashWithExistingSessionAndDefaultKey()
        {
            $_SESSION['flashhash'] = array(
                'key' => 'value'
            );

            $this->_flashhash = new FlashHash();
            $this->assertSame(array('key' => 'value'), $this->_flashhash->asArray());
        }

        /**
         * Tests specifying a key for the flashhash works as expected.
         */
        public function testFlashHashWithExistingSessionAndSpecifiedKey()
        {
            $_SESSION['test'] = array(
                'key' => 'value'
            );

            $this->_flashhash = new FlashHash('test');
            $this->assertSame(array('key' => 'value'), $this->_flashhash->asArray());
        }

        /**
         * Tests {@link Breeze\Application::flashnow()} to set a key in the
         * current flashhash.
         */
        public function testFlashNow()
        {
            $this->_setupMockedDependencies();
            $this->_mockPluginSystem();
            $this->_mockApplication();

            $this->_application->flashnow('key', 'value');
            $this->assertSame('value', $this->_application->flash('key'));
        }

        /**
         * Tests {@link Breeze\Application::flash()} sets a value in the current
         * session.
         */
        public function testFlashSet()
        {
            $this->_setupMockedDependencies();
            $this->_mockPluginSystem();
            $this->_mockApplication();

            $this->_application->flash('key', 'value');
            $this->assertSame('value', $_SESSION['flashhash']['key']);
        }
    }
}