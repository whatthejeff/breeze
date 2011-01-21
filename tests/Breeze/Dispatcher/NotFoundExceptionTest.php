<?php
/**
 * Breeze Framework - NotFoundException test case
 *
 * This file contains the {@link Breeze\Dispatcher\Tests\NotFoundExceptionTest} class.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @category   Breeze
 * @package    Dispatcher
 * @subpackage Tests
 * @copyright  Copyright (c) 2010-2011, Breeze Framework
 * @license    New BSD License
 * @version    $Id$
 */

namespace Breeze\Dispatcher\Tests {

    /**
     * @see Breeze\Dispatcher\NotFoundException
     */
    use Breeze\Dispatcher\NotFoundException;

    /**
     * The test case for the {@link Breeze\Dispatcher\NotFoundException} class.
     *
     * @category    Breeze
     * @package     Dispatcher
     * @subpackage  Tests
     */
    class NotFoundExceptionTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Tests that the {@link Breeze\Dispatcher\NotFoundException} has the
         * correct code and message.
         */
        public function testException()
        {
            $this->setExpectedException('Breeze\\Dispatcher\\NotFoundException', 'Page not found', 404);
            throw new NotFoundException('Page not found');
        }
    }

}