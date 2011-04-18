<?php
/**
 * Blog - Posts Controller
 *
 * This file contains the posts controller for the blog example.
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

get('/', function(){
    redirect('/posts');
});

/**
 * View actions
 *
 * GET /admin/posts/
 * GET /admin/posts/:id
 */
get('/posts', function(){
    display('posts/index', array(
        'posts'=>Doctrine_Core::getTable('Post')->findAll())
    );
});
get(';^/posts/(?<id>\d+)$;', function($app, $params) {
    load($app, $params['id']);
    display('posts/show');
});
