<?php
/**
 *  Constants for file paths
 * 
 *  This file defines some constants for file paths such as classes, modules and templates
 * 
 * @filename config/paths.php
 * @package Configuration
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */
/**
 * Shorthand for directory separator 
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * The root folder 
 */
define('ROOT', dirname(dirname(__FILE__)) . DS);

/**
 * The classes folder 
 */
define('CLASSES', ROOT . 'classes' . DS);

/**
 * The config folder 
 */
define('CONFIG', ROOT, 'config' . DS);

/**
 * The controllers folder 
 */
define('CONTROLLERS', ROOT . 'controllers' . DS);

/**
 * The modules folder 
 */
define('MODULES', ROOT . 'modules' . DS);

/**
 * The public_html folder 
 */
define('PUBLIC_HTML', ROOT . 'public_html' . DS);

/**
 * The templates folder 
 */
define('TEMPLATES', ROOT . 'templates' . DS);
?>
