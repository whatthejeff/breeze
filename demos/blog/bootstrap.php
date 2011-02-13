<?php
/**
 * Blog - Bootstrap
 *
 * This file contains the bootstrap routines for the blog demo.
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

    /**
     * Sets up the blog environment.
     */
    require_once 'setup.php';

    /**
     * The public posts controller
     */
    require_once 'controllers/posts.php';
    /**
     * The public admin controller
     */
    require_once 'controllers/admin.php';

    run();