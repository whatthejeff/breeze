<?php
/**
 * Blog - Admin Controller
 *
 * This file contains the admin controller for the blog demo.
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
 * @subpackage Admin
 * @copyright  Copyright (c) 2010, Breeze Framework
 * @license    New BSD License
 * @version    $Id$
 */

    get('/admin', function(){
        redirect('/admin/posts');
    });

    /**
     * Pre-processor for loading posts
     */
    any(';^/admin/posts/(?<id>\d+);', function($app, $params){
        load($app, $params['id']);
        pass();
    });

    /**
     * View actions
     *
     * GET /admin/posts/
     * GET /admin/posts/:id
     */
    get('/admin/posts', function(){
        display('admin/posts/index', array('posts'=>Doctrine_Core::getTable('Post')->findAll()));
    });
    get(';^/admin/posts/(?<id>\d+)$;', function($app, $params) {
        display('admin/posts/show');
    });

    /**
     * New actions
     *
     * GET  /admin/posts/new
     * POST /admin/posts
     */
    get('/admin/posts/new', function(){
        display('admin/posts/new');
    });
    post('/admin/posts', function(){
        if (save(new Post())) {
            flash('notice', POST_CREATED_MESSAGE);
            redirect(p($post['id']));
        }
        redirect('/admin/posts/new');
    });

    /**
     * Edit actions
     *
     * GET /admin/posts/:id/edit
     * PUT /admin/posts/:id
     */
    get(';^/admin/posts/(?<id>\d+)/edit$;', function($app, $params) {
        display('admin/posts/edit');
    });
    put(';^/admin/posts/(?<id>\d+)$;', function($app, $params) {
        if (save($app->post)) {
            flash('notice', POST_UPDATED_MESSAGE);
            redirect(p($params['id']));
        }
        redirect(p($params['id']) . '/edit');
    });

    /**
     * Delete actions
     *
     * GET    /admin/posts/:id/delete
     * DELETE /admin/posts/:id
     */
    get(';^/admin/posts/(?<id>\d+)/delete$;', function($app, $params) {
        display('admin/posts/delete');
    });
    delete(';^/admin/posts/(?<id>\d+)$;', function($app, $params) {
        $app->post->delete();
        flash('notice', POST_DELETED_MESSAGE);
        redirect('/admin/posts');
    });