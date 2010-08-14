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
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @category   Blog
 * @package    Core
 * @copyright  Copyright (c) 2010, Breeze Framework
 * @license    New BSD License
 * @version    $Id$
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