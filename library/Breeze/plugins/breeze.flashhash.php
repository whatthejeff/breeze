<?php
/**
 * Breeze Framework - Flashhash Plugin
 *
 * This file contains the Flashhash plugin for the Breeze Framework.  Flashhash is
 * a utility that can be used to pass arbitrary data between actions.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @author      Jeff Welch <whatthejeff@gmail.com>
 * @category    Breeze
 * @package     Plugins
 * @subpackage  Flashhash
 * @copyright   Copyright (c) 2010, Breeze Framework
 * @license     New BSD License
 * @version     $Id$
 */

namespace Breeze\Plugins\Flashhash {

    /**
     * @see Breeze\Application
     */
    use Breeze\Application;

    /**
     * @see Breeze\Application
     */
    require_once 'Breeze/Application.php';

    /**
     * The base Flashhash class which can be used to pass arbitrary data between
     * actions.
     *
     * @category    Breeze
     * @package     Plugins
     * @subpackage  Flashhash
     */
    class Flashhash implements \ArrayAccess
    {
        /**
         * Variables currently in the flashhash.
         *
         * @access  protected
         * @var     array
         */
        protected $_variables = array();

        /**
         * Starts a new flashhash instance using the $key parameter as the index
         * where the flashhash data will be stored in the current session.
         *
         * NOTE: This method will call session_start().
         *
         * @access public
         * @param string $key  The key where the flashhash is stored in the session.
         * @return void
         */
        public function __construct($key = 'flashhash')
        {
            @session_start();

            if (isset($_SESSION[$key])) {
                $this->_variables = $_SESSION[$key];
                unset($_SESSION[$key]);
            }
        }

        /**
         * Sets a new value in the flashhash.
         *
         * @access public
         * @param string $offset  The offset where the value should be set.
         * @param mixed $value    The value to set.
         * @return void
         */
        public function offsetSet($offset, $value)
        {
            $this->_variables[$offset] = $value;
        }

        /**
         * Checks if a value exists in the flashhash
         *
         * @access public
         * @param string $offset  The offset where the value should be set.
         * @return boolean  If the value exists.
         */
        public function offsetExists($offset)
        {
            return isset($this->_variables[$offset]);
        }

        /**
         * Removes a value from the flashhash.
         *
         * @access public
         * @param string $offset  The offset of the value that should be removed.
         * @return void
         */
        public function offsetUnset($offset)
        {
            unset($this->_variables[$offset]);
        }

        /**
         * Gets a value from the flashhash.
         *
         * @access public
         * @param string $offset  The offset where the value should be set.
         * @return mixed  The value from the flashhash.
         */
        public function offsetGet($offset)
        {
            return isset($this->_variables[$offset]) ? $this->_variables[$offset] : null;
        }

        /**
         * Returns the flashhash as an actual array.
         *
         * Hack for the fact that some template engines, namely Smarty, don't
         * support the \ArrayAccess interface for some of the more important
         * constructs (like foreach).
         *
         * @access public
         * @return array
         */
        public function asArray()
        {
            return $this->_variables;
        }
    }

    Application::register('Flashhash', function($app){
        // Make the flashhash available to templates
        $app->flash = new Flashhash();

        /**
         *  @code
         *      get('/', function(){
         *          flash('name', 'This is a value');
         *          redirect('/getflash');
         *      });
         *
         *      get('/getflash', function(){
         *          echo flash('name'); // This is a value
         *          display('getflash');
         *      });
         *
         *      // getflash.php
         *      <p id="flash"><?php echo $flash['name']; ?></p>
         *  @endcode
         */
        $app->helper('flash', function($name, $value = null) use ($app) {
            $num_args = func_num_args();

            if ($num_args == 1) {
                return $app->flash[$name];
            } else {
                $_SESSION['flashhash'][$name] = $value;
            }
        });

        /**
         *  @code
         *      get('/', function(){
         *          flashnow('name', 'This is a value');
         *          echo flash('name'); // This is a value
         *          display('getflash');
         *      });
         *
         *      // getflash.php
         *      <p id="flash"><?php echo $flash['name']; ?></p>
         *  @endcode
         */
        $app->helper('flashnow', function($name, $value = null) use ($app) {
            $app->flash[$name] = $value;
        });
    });
}