<?php
/**
 * Blog - Posts Controller
 *
 * This file contains the posts controller for the blog demo.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @category   Blog
 * @package    Controllers
 * @subpackage Posts
 * @copyright  Copyright (c) 2010, Breeze Framework
 * @license    New BSD License
 * @version    $Id$
 */
namespace Breeze\Demos\Blog {
    $breeze->get('/', function($breeze){
        $breeze->redirect('/posts');
    });

    /**
     * View actions
     *
     * GET /admin/posts/
     * GET /admin/posts/:id
     */
    $breeze->get('/posts', function($breeze){
        $breeze->display('posts/index', array('posts'=>\Doctrine_Core::getTable('Post')->findAll()));
    });
    $breeze->get(';^/posts/(?<id>\d+)$;', function($breeze, $params) {
        $breeze->load($breeze, $params['id']);
        $breeze->display('posts/show');
    });
}