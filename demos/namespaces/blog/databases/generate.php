<?php
/**
 * Blog - Database Generator
 *
 * This file contains a script for generating the database for the blog demo.
 * Running this script from command line as follows will generate a new database:
 *
 * @code
 *     php generate.php
 * @endcode
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @category   Blog
 * @package    Databases
 * @copyright  Copyright (c) 2010, Breeze Framework
 * @license    New BSD License
 * @version    $Id$
 */

    define('BREEZE_APPLICATION', realpath(dirname(__FILE__) . '/..'));

    require_once('Doctrine/lib/Doctrine.php');
    spl_autoload_register(array('Doctrine', 'autoload'));
    spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));

    $manager = Doctrine_Manager::getInstance();
    Doctrine_Manager::connection('sqlite:///' . BREEZE_APPLICATION . '/databases/blog.db?mode=0666', 'blog');

    Doctrine_Core::dropDatabases();
    Doctrine_Core::createDatabases();
    Doctrine_Core::createTablesFromModels(BREEZE_APPLICATION . '/models');