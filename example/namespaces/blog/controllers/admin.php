<?php
/**
 * Blog - Admin Controller
 *
 * This file contains the admin controller for the blog example.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @package    Breeze
 * @subpackage Examples
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
 */

namespace Breeze\Examples\Blog;

$breeze->get('/admin', function($breeze){
    $breeze->redirect('/admin/posts');
});

/**
 * Pre-processor for loading posts
 */
$breeze->any(';^/admin/posts/(?<id>\d+);', function($breeze, $params){
    $breeze->load($breeze, $params['id']);
    $breeze->pass();
});

/**
 * View actions
 *
 * GET /admin/posts/
 * GET /admin/posts/:id
 */
$breeze->get('/admin/posts', function($breeze){
    $breeze->display('admin/posts/index', array(
        'posts'=>\Doctrine_Core::getTable('Post')->findAll())
    );
});
$breeze->get(';^/admin/posts/(?<id>\d+)$;', function($breeze, $params) {
    $breeze->display('admin/posts/show');
});

/**
 * New actions
 *
 * GET  /admin/posts/new
 * POST /admin/posts
 */
$breeze->get('/admin/posts/new', function($breeze){
    $breeze->display('admin/posts/new');
});
$breeze->post('/admin/posts', function($breeze){
    $post = new \Post();
    if ($breeze->save($post)) {
        $breeze->flash('notice', POST_CREATED_MESSAGE);
        $breeze->redirect($breeze->p($post['id']));
    }
    $breeze->redirect('/admin/posts/new');
});

/**
 * Edit actions
 *
 * GET /admin/posts/:id/edit
 * PUT /admin/posts/:id
 */
$breeze->get(';^/admin/posts/(?<id>\d+)/edit$;', function($breeze, $params) {
    $breeze->display('admin/posts/edit');
});
$breeze->put(';^/admin/posts/(?<id>\d+)$;', function($breeze, $params) {
    if ($breeze->save($breeze->post)) {
        $breeze->flash('notice', POST_UPDATED_MESSAGE);
        $breeze->redirect($breeze->p($params['id']));
    }
    $breeze->redirect($breeze->p($params['id']) . '/edit');
});

/**
 * Delete actions
 *
 * GET    /admin/posts/:id/delete
 * DELETE /admin/posts/:id
 */
$breeze->get(';^/admin/posts/(?<id>\d+)/delete$;', function($breeze, $params) {
    $breeze->display('admin/posts/delete');
});
$breeze->delete(';^/admin/posts/(?<id>\d+)$;', function($breeze, $params) {
    $breeze->post->delete();
    $breeze->flash('notice', POST_DELETED_MESSAGE);
    $breeze->redirect('/admin/posts');
});
