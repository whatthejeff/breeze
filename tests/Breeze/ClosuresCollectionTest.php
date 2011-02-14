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

namespace Breeze\Tests;

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
     * @param Breeze\ClosuresCollection
     */
    protected $collection;

    /**
     * A sample closure to add to tests.
     *
     * @param Closure
     */
    protected $closure;

    /**
     * The list of valid labels for adding to collections with the
     * {@link Breeze\ClosuresCollection::VALIDATE_LABEL} flag set.
     *
     * @param array
     */
    protected $valid_labels = array(
        'thisnameisvalid',
        'thisonehasnumber123',
        'thisonehas_under_scores'
    );

    /**
     * The list of invalid labels that will fail when adding to collections
     * with the {@link Breeze\ClosuresCollection::VALIDATE_LABEL} flag set.
     *
     * @param string
     */
    protected $invalid_labels = array(
        'has a space',
        '1startswithanumber',
        'contains*bad*characters'
    );

    /**
     * The list of valid names for adding to collections with the
     * {@link Breeze\ClosuresCollection::VALIDATE_NAME} flag set.
     *
     * @param array
     */
    protected $valid_names = array(
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
        $this->collection = new ClosuresCollection();
        $this->closure = function(){};
    }

    /**
     * Tests {@link Breeze\ClosuresCollection::has()} with an unset key.
     */
    public function testHasWithUnsetKey()
    {
        $this->assertFalse($this->collection->has('unset key'));
    }

    /**
     * Tests {@link Breeze\ClosuresCollection::has()} with a set key.
     */
    public function testHasWithSetKey()
    {
        $this->collection->add('empty closure', $this->closure);
        $this->assertTrue($this->collection->has('empty closure'));
    }

    /**
     * Tests {@link Breeze\ClosuresCollection::get()} with an unset key.
     */
    public function testGetWithUnsetKey()
    {
        $this->assertNull($this->collection->get('unset key'));
    }

    /**
     * Tests {@link Breeze\ClosuresCollection::get()} with a set key.
     */
    public function testGetWithSetKey()
    {
        $this->collection->add('empty closure', $this->closure);
        $this->assertSame($this->closure, $this->collection->get('empty closure'));
    }

    /**
     * Tests {@link Breeze\ClosuresCollection::add()} with no name.
     */
    public function testAddWithNoName()
    {
        $closures = array(function(){ return 1; },  function(){ return 2; });
        foreach ($closures as $closure) {
            $this->collection->add($closure);
        }

        $this->assertSame($closures, $this->collection->all());
    }

    /**
     * Tests {@link Breeze\ClosuresCollection::add()} with a name.
     */
    public function testAddWithName()
    {
        $closures = array('closure1'=>function(){}, 'closure2'=>function(){});
        foreach ($closures as $name => $closure) {
            $this->collection->add($name, $closure);
        }

        $this->assertSame($closures, $this->collection->all());
    }

    /**
     * Tests {@link Breeze\ClosuresCollection::add()} to add one closure to multiple
     * names.
     */
    public function testAddOneClosureToMultipleNames()
    {
        $this->collection->add($this->valid_names, $this->closure);
        $this->assertSame(array_fill_keys($this->valid_names,  $this->closure), $this->collection->all());
    }

    /**
     * Tests {@link Breeze\ClosuresCollection::add()} with an invalid closure.
     */
    public function testAddWithInvalidClosure()
    {
        $this->setExpectedException('\\InvalidArgumentException', 'You must provide a callable PHP function.');
        $this->collection->add('invalid closure', 'INVALID CLOSURE');
    }

    /**
     * Tests {@link Breeze\ClosuresCollection::add()} with an empty name and
     * name validation.
     */
    public function testAddWithStringEmptyNameAndNameValidation()
    {
        $this->setExpectedException('\\InvalidArgumentException', 'You must provide a name.');
        $this->collection->add('', $this->closure, ClosuresCollection::VALIDATE_NAME);
    }

    /**
     * Tests {@link Breeze\ClosuresCollection::add()} with an empty array and
     * name validation.
     */
    public function testAddWithArrayEmptyNameAndNameValidation()
    {
        $this->setExpectedException('\\InvalidArgumentException', 'You must provide a name.');
        $this->collection->add(array(''), $this->closure, ClosuresCollection::VALIDATE_NAME);
    }

    /**
     * Tests {@link Breeze\ClosuresCollection::add()} with an empty array and
     * name validation.
     */
    public function testAddWithStringValidNameAndNameValidation()
    {
        foreach ($this->valid_names as $name) {
            $this->collection->add($name, $this->closure, ClosuresCollection::VALIDATE_NAME);
        }

        $this->checkClosuresAdded($this->valid_names, $this->closure);
    }

    /**
     * Tests {@link Breeze\ClosuresCollection::add()} with an array and name
     * validation.
     */
    public function testAddWithArrayValidNameAndNameValidation()
    {
        $this->collection->add($this->valid_names, $this->closure, ClosuresCollection::VALIDATE_NAME);
        $this->checkClosuresAdded($this->valid_names, $this->closure);
    }

    /**
     * Tests {@link Breeze\ClosuresCollection::add()} with string empty and
     * label validation.
     */
    public function testAddWithStringEmptyLabelAndLabelValidation()
    {
        $this->setExpectedException('\\InvalidArgumentException', 'You must provide a name.');
        $this->collection->add('', $this->closure, ClosuresCollection::VALIDATE_LABEL);
    }

    /**
     * Tests {@link Breeze\ClosuresCollection::add()} with array empty and
     * label validation.
     */
    public function testAddWithArrayEmptyLabelAndLabelValidation()
    {
        $this->setExpectedException('\\InvalidArgumentException', 'You must provide a name.');
        $this->collection->add(array(''), $this->closure, ClosuresCollection::VALIDATE_LABEL);
    }

    /**
     * Tests {@link Breeze\ClosuresCollection::add()} with invalid labels
     * and label validation.
     */
    public function testAddWithStringInvalidLabelAndLabelValidation()
    {
        foreach ($this->invalid_labels as $label) {
            try {
                $this->collection->add($label, $this->closure, ClosuresCollection::VALIDATE_LABEL);
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
        $this->collection->add($this->invalid_labels, $this->closure, ClosuresCollection::VALIDATE_LABEL);
    }

    /**
     * Tests {@link Breeze\ClosuresCollection::add()} with a string and label
     * validation.
     */
    public function testAddWithStringValidLabelAndLabelValidation()
    {
        foreach ($this->valid_labels as $label) {
            $this->collection->add($label, $this->closure, ClosuresCollection::VALIDATE_LABEL);
        }
        $this->checkClosuresAdded($this->valid_labels, $this->closure);
    }

    /**
     * Tests {@link Breeze\ClosuresCollection::add()} with an array and label
     * validation.
     */
    public function testAddWithArrayValidLabelAndLabelValidation()
    {
        $this->collection->add($this->valid_labels, $this->closure, ClosuresCollection::VALIDATE_LABEL);
        $this->checkClosuresAdded($this->valid_labels, $this->closure);
    }

    /**
     * Tests that closures were added to {@link Breeze\Tests\ClosuresCollectionTest::$collection}.
     *
     * @param mixed $keys     The keys that should've been added.
     * @param mixed $closures The closures that should've been added.
     *
     * @return void
     */
    protected function checkClosuresAdded($keys, $closures)
    {
        $keys = (array)$keys;

        if (!is_array($closures)) {
            $closures = array_fill(0, count($keys), $closures);
        }

        foreach ($keys as $index => $key) {
            $this->assertSame($closures[$index], $this->collection->get($key));
        }
    }
}