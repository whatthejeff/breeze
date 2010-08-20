<?php
/**
 * Hello World (namespaced)
 *
 * This file contains the Hello World demo (namespaced).
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
 * @copyright  Copyright (c) 2010, Breeze Framework
 * @license    New BSD License
 * @version    $Id$
 */

namespace Breeze\Demos\HelloWorld {

    /**
     * @see Breeze\Application
     */
    use Breeze\Application;

    /**
     * @see Breeze\Application
     */
    require_once 'Breeze/Application.php';

    $breeze = new Application();

    # Hello World!
    $breeze->get('/', function($app) {
        $app->display('hello', array('name'=>'World'));
    });

    # Hello $name!
    $breeze->get(';/(?<name>.+);', function($app, $params) {
        $app->display('hello', array('name'=>urldecode($params['name'])));
    });

    $breeze->run();
}