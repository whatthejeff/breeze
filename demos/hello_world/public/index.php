<?php
/**
 * Hello World
 *
 * This file contains the Hello World demo.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @category   HelloWorld
 * @package    Core
 * @copyright  Copyright (c) 2010-2011, Breeze Framework
 * @license    New BSD License
 * @version    $Id$
 */

    require_once 'Breeze.php';

    # Hello World!
    get('/', function() {
        display('hello', array('name'=>'World'));
    });

    # Hello $name!
    get(';/(?<name>.+);', function($app, $params) {
        display('hello', array('name'=>urldecode($params['name'])));
    });

    run();