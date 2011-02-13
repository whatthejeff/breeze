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
 * @package    Breeze
 * @subpackage Demos
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
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