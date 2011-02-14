<?php
/**
 * Breeze Framework - Global scope shortcuts
 *
 * This file contains code that creates functions in the global scope that delegate
 * to the {@link Breeze\Application} class facade which facilitates the micro-framework
 * feel while maintaining a more modular, extensible design.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @package    Breeze
 * @subpackage Shortcuts
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */

/**
 * @see Breeze\Application
 */
require_once 'Breeze/Application.php';
$breeze = new Breeze\Application();

foreach ($breeze->getHelpers() as $application_helper) {
    if ($application_helper == 'helper') {
        function helper($name, $extension) {
            global $breeze;
            $return = call_user_func_array(array($breeze, 'helper'), func_get_args());
            eval("function $name() {
                global \$breeze;
                return call_user_func_array(array(\$breeze, '$name'), func_get_args());
            }");

            return $return;
        }
    } else {
        eval("function $application_helper() {
            global \$breeze;
            return call_user_func_array(array(\$breeze, '$application_helper'), func_get_args());
        }");
    }
}