<?php
/**
 * Breeze Framework - ClosuresCollection test case
 *
 * This file contains the {@link Breeze\Tests\ClosuresCollectionTest} class.
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
     * @see Breeze\ClosuresCollection
     */
    use Breeze\ClosuresCollection;

    /**
     * The test case for the {@link Breeze\ClosuresCollection} class.
     *
     * @package    Breeze
     * @subpackage Tests
     * @author     Jeff Welch <whatthejeff@gmail.com>
     * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
     * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
     * @link       http://breezephp.com/
     */
    class ClosuresCollectionTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * The collection object for testing.
         *
         * @access protected
         * @param  Breeze\ClosuresCollection
         */
        protected $_collection;

        /**
         * A sample closure to add to tests.
         *
         * @access protected
         * @param  Closure
         */
        protected $_closure;

        /**
         * The list of valid labels for adding to collections with the
         * {@link Breeze\ClosuresCollection::VALIDATE_LABEL} flag set.
         *
         * @access protected
         * @param  array
         */
        protected $_valid_labels = array(
            'thisnameisvalid',
            'thisonehasnumber123',
            'thisonehas_under_scores'
        );

        /**
         * The list of invalid labels that will fail when adding to collections
         * with the {@link Breeze\ClosuresCollection::VALIDATE_LABEL} flag set.
         *
         * @access protected
         * @param  string
         */
        protected $_invalid_labels = array(
            'has a space',
            '1startswithanumber',
            'contains*bad*characters'
        );

        /**
         * The list of valid names for adding to collections with the
         * {@link Breeze\ClosuresCollection::VALIDATE_NAME} flag set.
         *
         * @access protected
         * @param  array
         */
        protected $_valid_names = array(
            'thisisvalid',
            'this is also valid',
            'this*is*also*valid'
        );

        /**
         * Sets up the test case for {@link Breeze\ClosuresCollection}.
         *
         * @return void
         */
        public function setUp()
        {
            $this->_collection = new ClosuresCollection();
            $this->_closure = function(){};
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::has()} with an unset key.
         */
        public function testHasWithUnsetKey()
        {
            $this->assertFalse($this->_collection->has('unset key'));
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::has()} with a set key.
         */
        public function testHasWithSetKey()
        {
            $this->_collection->add('empty closure', $this->_closure);
            $this->assertTrue($this->_collection->has('empty closure'));
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::get()} with an unset key.
         */
        public function testGetWithUnsetKey()
        {
            $this->assertNull($this->_collection->get('unset key'));
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::get()} with a set key.
         */
        public function testGetWithSetKey()
        {
            $this->_collection->add('empty closure', $this->_closure);
            $this->assertSame($this->_closure, $this->_collection->get('empty closure'));
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::add()} with no name.
         */
        public function testAddWithNoName()
        {
            $closures = array(function(){ return 1; },  function(){ return 2; });
            foreach ($closures as $closure) {
                $this->_collection->add($closure);
            }

            $this->assertSame($closures, $this->_collection->all());
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::add()} with a name.
         */
        public function testAddWithName()
        {
            $closures = array('closure1'=>function(){}, 'closure2'=>function(){});
            foreach ($closures as $name => $closure) {
                $this->_collection->add($name, $closure);
            }

            $this->assertSame($closures, $this->_collection->all());
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::add()} to add one closure to multiple
         * names.
         */
        public function testAddOneClosureToMultipleNames()
        {
            $this->_collection->add($this->_valid_names, $this->_closure);
            $this->assertSame(array_fill_keys($this->_valid_names,  $this->_closure), $this->_collection->all());
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::add()} with an invalid closure.
         */
        public function testAddWithInvalidClosure()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'You must provide a callable PHP function.');
            $this->_collection->add('invalid closure', 'INVALID CLOSURE');
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::add()} with an empty name and
         * name validation.
         */
        public function testAddWithStringEmptyNameAndNameValidation()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'You must provide a name.');
            $this->_collection->add('', $this->_closure, ClosuresCollection::VALIDATE_NAME);
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::add()} with an empty array and
         * name validation.
         */
        public function testAddWithArrayEmptyNameAndNameValidation()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'You must provide a name.');
            $this->_collection->add(array(''), $this->_closure, ClosuresCollection::VALIDATE_NAME);
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::add()} with an empty array and
         * name validation.
         */
        public function testAddWithStringValidNameAndNameValidation()
        {
            foreach ($this->_valid_names as $name) {
                $this->_collection->add($name, $this->_closure, ClosuresCollection::VALIDATE_NAME);
            }

            $this->_testClosuresAdded($this->_valid_names, $this->_closure);
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::add()} with an array and name
         * validation.
         */
        public function testAddWithArrayValidNameAndNameValidation()
        {
            $this->_collection->add($this->_valid_names, $this->_closure, ClosuresCollection::VALIDATE_NAME);
            $this->_testClosuresAdded($this->_valid_names, $this->_closure);
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::add()} with string empty and
         * label validation.
         */
        public function testAddWithStringEmptyLabelAndLabelValidation()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'You must provide a name.');
            $this->_collection->add('', $this->_closure, ClosuresCollection::VALIDATE_LABEL);
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::add()} with array empty and
         * label validation.
         */
        public function testAddWithArrayEmptyLabelAndLabelValidation()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'You must provide a name.');
            $this->_collection->add(array(''), $this->_closure, ClosuresCollection::VALIDATE_LABEL);
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::add()} with invalid labels
         * and label validation.
         */
        public function testAddWithStringInvalidLabelAndLabelValidation()
        {
            foreach ($this->_invalid_labels as $label) {
                try {
                    $this->_collection->add($label, $this->_closure, ClosuresCollection::VALIDATE_LABEL);
                    $this->fail("Expected exception \\InvalidArgumentException");
                } catch (\InvalidArgumentException $exception) {
                    $this->assertStringEndsWith('is not a valid PHP function name.', $exception->getMessage());
                }
            }
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::add()} with an array with
         * invalid labels and label validation.
         */
        public function testAddWithArrayInvalidLabelAndLabelValidation()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'is not a valid PHP function name.');
            $this->_collection->add($this->_invalid_labels, $this->_closure, ClosuresCollection::VALIDATE_LABEL);
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::add()} with a string and label
         * validation.
         */
        public function testAddWithStringValidLabelAndLabelValidation()
        {
            foreach ($this->_valid_labels as $label) {
                $this->_collection->add($label, $this->_closure, ClosuresCollection::VALIDATE_LABEL);
            }
            $this->_testClosuresAdded($this->_valid_labels, $this->_closure);
        }

        /**
         * Tests {@link Breeze\ClosuresCollection::add()} with an array and label
         * validation.
         */
        public function testAddWithArrayValidLabelAndLabelValidation()
        {
            $this->_collection->add($this->_valid_labels, $this->_closure, ClosuresCollection::VALIDATE_LABEL);
            $this->_testClosuresAdded($this->_valid_labels, $this->_closure);
        }

        /**
         * Tests that closures were added to {@link Breeze\Tests\ClosuresCollectionTest::$_collection}.
         *
         * @param mixed $keys     The keys that should've been added.
         * @param mixed $closures The closures that should've been added.
         *
         * @return void
         */
        protected function _testClosuresAdded($keys, $closures)
        {
            $keys = (array)$keys;

            if (!is_array($closures)) {
                $closures = array_fill(0, count($keys), $closures);
            }

            foreach ($keys as $index => $key) {
                $this->assertSame($closures[$index], $this->_collection->get($key));
            }
        }
    }

}