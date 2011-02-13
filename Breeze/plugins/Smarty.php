<?php
/**
 * Breeze Framework - Smarty Plugin
 *
 * This file contains the Smarty plugin for the Breeze Framework.  For more information
 * on Smarty, visit {@link http://www.smarty.net/}.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @package    Breeze
 * @subpackage View
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
 * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
 * @link       http://breezephp.com/
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
     * @see Smarty
     */
    @include_once 'Smarty.class.php';

    /**
     * The Smarty-based template engine for the Breeze Framework.
     *
     * @package    Breeze
     * @subpackage View
     * @author     Jeff Welch <whatthejeff@gmail.com>
     * @copyright  2010-2011 Jeff Welch <whatthejeff@gmail.com>
     * @license    https://github.com/whatthejeff/breeze/blob/master/LICENSE New BSD License
     * @link       http://breezephp.com/
     */
    class Smarty extends Driver
    {
        /**
         * An instance of Smarty
         *
         * @var Smarty
         * @see http://www.smarty.net/
         */
        protected $_smarty;

        /**
         * The default directory for compiled templates.
         */
        const DEFAULT_COMPILE_DIR = 'compiled';
        /**
         * The default directory for cached templates.
         */
        const DEFAULT_CACHE_DIR = 'cache';
        /**
         * The default directory for configurations.
         */
        const DEFAULT_CONFIG_DIR = 'config';

        /**
         * Sets up the templates directory path and the extra options for a
         * database engine.  The extra options are to be defined by the
         * specific engines.
         *
         * @param Breeze\Application $application An instance of the base Breeze Framework class.
         * @param string             $path        The path to the templates directory
         * @param array              $options     Extra options for setting up custom template engines
         *
         * @return void
         */
        public function __construct(Application $application, $path = null, array $options = array())
        {
            $this->_smarty = new \Smarty();
            parent::__construct($application, $path, $options);

            $this->_smarty->registerPlugin('function', 'partial', array($this, 'partial'));
        }

        /**
         * Sets up the internal template engine structures.  This is intended
         * to be where engine specific options are set up.
         *
         * @return void
         */
        protected function _config()
        {
            $path = $this->getPath();

            $this->_smarty->template_dir = $path;
            $this->_smarty->compile_dir  = $path . '/' . $this->getOption('compile_dir', self::DEFAULT_COMPILE_DIR);
            $this->_smarty->cache_dir    = $path . '/' . $this->getOption('cache_dir', self::DEFAULT_CACHE_DIR);
            $this->_smarty->config_dir   = $path . '/' . $this->getOption('config_dir', self::DEFAULT_CONFIG_DIR);
        }

        /**
         * Renders a template using the $variables parameter and returns
         * the contents.
         *
         * @param string $template  The path to the template, excluding the base templates directory.
         * @param array  $variables An associative array of variables to use in the template.
         *
         * @return string
         */
        protected function _fetch($template, array $variables = array())
        {
            $this->_smarty->assign($variables);
            return $this->_smarty->fetch($template);
        }

        /**
         * Smarty wrapper for the {@link Breeze\View\View::partial()} function.
         *
         * @param array  $params An associative array of parameters for the smarty function.
         * @param Smarty $smarty An instance of the Smarty object.
         *
         * @return string
         */
        public function partial($params, \Smarty $smarty)
        {
            if (!array_key_exists('file', $params)) {
               trigger_error("Smarty error: [partial] missing parameter 'file'", E_USER_ERROR);
            }

            $file = $params['file'];
            unset($params['file']);

            return $this->_application->partial($file, $params);
        }
    }

    Application::register('Smarty', function($app){

        // Sets up some default Smarty configurations
        $app->config(array(
            'template_engine'    => 'Smarty',
            'template_extension' => '.tpl',
            'template_options'   => array(
                'compile_dir' => Smarty::DEFAULT_COMPILE_DIR,
                'cache_dir'   => Smarty::DEFAULT_CACHE_DIR,
                'config_dir'  => Smarty::DEFAULT_CONFIG_DIR)
        ));

    });
}