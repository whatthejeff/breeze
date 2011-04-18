<?php
/**
 * Blog - Setup
 *
 * This file contains the common setup configurations for the blog example.
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

/**
 * @see Breeze\Application
 */
use Breeze\Application;

define('BREEZE_APPLICATION', realpath(dirname(__FILE__)));

// The admin form messages
const POST_ERROR_MESSAGE = 'Oh no, something went wrong:';
const POST_CREATED_MESSAGE = 'Your post was successfully created.';
const POST_UPDATED_MESSAGE = 'Your post was successfully updated.';
const POST_DELETED_MESSAGE = 'Your post was successfully deleted.';

// Uncomment to use an alternate template engine
// require_once 'Breeze/plugins/Dwoo.php';
// require_once 'Breeze/plugins/Smarty.php';

// Setup the breeze framework components
require_once 'Breeze/plugins/Flashhash.php';
require_once 'Breeze/Application.php';
$breeze = new Application();
require_once 'helpers.php';

// Configure the breeze framework
$breeze->config('template_directory', sprintf('%s/views/%s',
    BREEZE_APPLICATION, strtolower($breeze->config('template_engine'))
));
$breeze->config('errors_backtrace', false);
$breeze->error('404', function($breeze){ $breeze->display('errors/404'); });
$breeze->error(function($breeze){ $breeze->display('errors/500'); });

// Setup the Doctrine model components
require_once('Doctrine/lib/Doctrine.php');
\spl_autoload_register(array('Doctrine', 'autoload'));
\spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));

$manager = \Doctrine_Manager::getInstance();
$manager->setAttribute(
    \Doctrine_Core::ATTR_VALIDATE, \Doctrine_Core::VALIDATE_ALL
);
$manager->setAttribute(
    \Doctrine_Core::ATTR_EXPORT, \Doctrine_Core::EXPORT_ALL
);
$manager->setAttribute(
    \Doctrine_Core::ATTR_MODEL_LOADING,
    \Doctrine_Core::MODEL_LOADING_CONSERVATIVE
);

\Doctrine_Manager::connection(
    'sqlite:///' . BREEZE_APPLICATION . '/databases/blog.db?mode=0666', 'blog'
);
\Doctrine_Core::loadModels(BREEZE_APPLICATION . '/models');
