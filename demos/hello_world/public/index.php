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
 * @package    Breeze
 * @subpackage Demos
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
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