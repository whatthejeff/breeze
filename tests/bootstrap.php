<?php
/**
 * Breeze Framework - Breeze Framework tests suite bootstrap.
 *
 * This file contains the bootstrap from the Breeze framework for test suite.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @category   Breeze
 * @package    Tests
 * @copyright  Copyright (c) 2010-2011, Breeze Framework
 * @license    New BSD License
 * @version    $Id$
 */

namespace Breeze\Tests {

    error_reporting(E_ALL | E_STRICT);

    /**
     * The root path to the Breeze Framework's test suite.
     */
    define('Breeze\\Tests\\TESTS_PATH', dirname(__FILE__));
    /**
     * The path to the Breeze Framework's test fixtures.
     */
    define('Breeze\\Tests\\FIXTURES_PATH', TESTS_PATH . '/Breeze/fixtures');

    /**
     * Only test the Dwoo plugin if it's in the include path
     */
    define('Breeze\\Tests\\TEST_DWOO', @include_once('Dwoo/dwooAutoload.php'));
    /**
     * Only test the Smarty plugin if it's in the include path
     */
    define('Breeze\\Tests\\TEST_SMARTY', @include_once('Smarty.class.php'));

    /**
     * @see Breeze\Application
     */
    require_once 'Breeze/Application.php';

    /**
     * @see PHPUnit_Extensions_OutputTestCase
     */
    require_once 'PHPUnit/Extensions/OutputTestCase.php';
    /**
     * @see Breeze\Tests\ApplicationTestCase
     */
    require_once TESTS_PATH . '/Breeze/ApplicationTestCase.php';
    /**
     * @see  Breeze\Plugins\Tests\PluginTestCase
     */
    require_once TESTS_PATH . '/Breeze/plugins/PluginTestCase.php';

}

