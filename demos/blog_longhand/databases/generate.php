<?php
/**
 * Blog - Database Generator
 *
 * This file contains a script for generating the database for the blog demo.
 * Running this script from command line as follows will generate a new database:
 *
 * <code>
 * php generate.php
 * </code>
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
    require_once BREEZE_APPLICATION . '/models/Doctrine.php';

    Doctrine_Core::dropDatabases();
    Doctrine_Core::createDatabases();
    Doctrine_Core::generateModelsFromYaml(BREEZE_APPLICATION . '/databases/schema.yml', BREEZE_APPLICATION . '/models');
    Doctrine_Core::createTablesFromModels(BREEZE_APPLICATION . '/models');