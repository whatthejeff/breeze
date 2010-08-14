<?php
/**
 * Breeze Framework - Dwoo Plugin
 *
 * This file contains the Dwoo plugin for the Breeze Framework.  For more information
 * on Dwoo, visit {@link http://dwoo.org/}.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @author      Jeff Welch <whatthejeff@gmail.com>
 * @category    Breeze
 * @package     View
 * @subpackage  Driver
 * @copyright   Copyright (c) 2010, Breeze Framework
 * @license     New BSD License
 * @version     $Id$
 */

namespace Breeze\View\Driver {

    /**
     * @see Breeze\Application
     */
    use Breeze\Application;

    /**
     * @see Breeze\Application
     */
    require_once 'Breeze/Application.php';
    /**
     * @see Dwoo
     */
    require_once 'Dwoo/dwooAutoload.php';

    /**
     * The Dwoo-based template engine for the Breeze Framework.
     *
     * @category    Breeze
     * @package     View
     * @subpackage  Driver
     */
    class Dwoo extends Driver
    {
        /**
         * An instance of Dwoo
         *
         * @access  protected
         * @var     Dwoo
         * @see     http://dwoo.org/
         */
        protected $_dwoo;

        /**
         * The default directory for compiled templates.
         */
        const DEFAULT_COMPILE_DIR = 'compiled';
        /**
         * The default directory for cached templates.
         */
        const DEFAULT_CACHE_DIR = 'cache';

        /**
         * Sets up the templates directory path and the extra options for a
         * database engine.  The extra options are to be defined by the
         * specific engines.
         *
         * @access public
         * @param  Breeze\Application $application  An instance of the base Breeze Framework class.
         * @param  string $path                     The path to the templates directory
         * @param  array $options                   Extra options for setting up custom template engines
         * @return void
         */
        public function __construct(Application $application, $path = null, array $options = null)
        {
            $this->_dwoo = new \Dwoo();
            parent::__construct($application, $path, $options);
        }

        /**
         * Sets up the internal template engine structures.  This is intended
         * to be where engine specific options are set up.
         *
         * @access protected
         * @return void
         */
        protected function _config()
        {
            $path = $this->getPath();
            $this->_dwoo->setCompileDir($path . '/' . $this->getOption('compile_dir', self::DEFAULT_COMPILE_DIR));
            $this->_dwoo->setCacheDir($path . '/' . $this->getOption('cache_dir', self::DEFAULT_CACHE_DIR));
        }

        /**
         * Renders a template using the $variables parameter and returns
         * the contents.
         *
         * @access protected
         * @param  string $template  The path to the template, excluding the base templates directory.
         * @param  array $variables  An associative array of variables to use in the template.
         * @return string  The rendered template.
         */
        protected function _fetch($template, array $variables = null)
        {
            return $this->_dwoo->get($this->getTemplatePath($template), $variables);
        }
    }

    Application::register('Dwoo', function($app){

        // Sets up some default Dwoo configurations
        $app->config(array(
            'template_engine'    => 'Dwoo',
            'template_extension' => '.tpl',
            'template_options'   => array(
                'compile_dir' => Dwoo::DEFAULT_COMPILE_DIR,
                'cache_dir'   => Dwoo::DEFAULT_CACHE_DIR)
        ));

    });
}