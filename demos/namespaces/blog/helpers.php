<?php
/**
 * Blog - Helpers
 *
 * This file contains a collection of helpers for the blog demo.
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

namespace Breeze\Demos\Blog;

/**
 * Loads a post identified by the $id parameter and assigns it to the
 * template.  If the post can't be loaded, this function throws throws a
 * {@link Breeze\Error\Exception} exception.
 *
 * @param Breeze\Application $breeze An instance of the Breeze application
 * @param integer            $id     The id of the post to load.
 *
 * @return void
 * @throws Breeze\Error\Exception
 */
$breeze->helper('load', function($breeze, $id) {
    if (!($breeze->post = \Doctrine_Core::getTable('Post')->find($id))) {
        $breeze->error(404, 'Post not found');
    }
});

/**
 * Attempts to save an instance of the {@link Post} model.  If validation
 * errors occur, flash vars post, errors, and error are set accordingly.
 *
 * @param Post $post The post to save.
 *
 * @return mixed
 */
$breeze->helper('save', function(\Post $post) use ($breeze) {
    $post->fromArray(array_intersect_key(
        (array)$_POST['post'], array_fill_keys(array('contents','title'), null)
    ));
    try {
        $post->save();
        return true;
    } catch(\Doctrine_Validator_Exception $exception) {
        $breeze->flash('post', $post->toArray());
        $breeze->flash('errors', $post->getErrorStack()->toArray());
        $breeze->flash('error', POST_ERROR_MESSAGE);
    }
});

/**
 * Echos an html escaped version of a string.
 *
 * @param string $string The string to echo.
 *
 * @return void
 */
$breeze->helper('h', function($string) {
    echo htmlentities($string, ENT_QUOTES, 'UTF-8');
});

/**
 * Generates the admin link to a post.
 *
 * @param integer $id  The id of the post to return the link for
 *
 * @return string
 */
$breeze->helper('p', function($id) {
    return "/admin/posts/$id";
});