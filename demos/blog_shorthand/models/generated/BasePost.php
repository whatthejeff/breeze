<?php
/**
 * Blog - Base Post Model
 *
 * This file contains the auto-generated Base Post model for the blog demo.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @category   Blog
 * @package    Models
 * @subpackage Post
 * @copyright  Copyright (c) 2010, Breeze Framework
 * @license    New BSD License
 * @version    $Id$
 */

/**
 * BasePost
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property text $title
 * @property text $contents
 *
 * @category   Blog
 * @package    Models
 * @subpackage Post
 */
abstract class BasePost extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('post');
        $this->hasColumn('title', 'text', null, array(
             'type' => 'text',
             'notnull' => true,
             'primary' => false,
             'autoincrement' => false,
             'notblank' => true,
             'length' => '',
             ));
        $this->hasColumn('contents', 'text', null, array(
             'type' => 'text',
             'notnull' => true,
             'primary' => false,
             'autoincrement' => false,
             'notblank' => true,
             'length' => '',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}