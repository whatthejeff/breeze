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
 * @copyright  Copyright (c) 2010, Breeze Framework
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