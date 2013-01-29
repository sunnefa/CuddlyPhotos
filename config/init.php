<?php
/**
 *  An initializer file
 * 
 *  This file includes all the neccessary files such as db.php, settings.php etc.
 * 
 * @filename config/init.php
 * @package Configuration
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * The path definitions 
 */
require_once 'paths.php';

/**
 * The database definitions 
 */
require_once 'db.php';

/**
 * The settings 
 */
require_once 'settings.php';

/**
 * The error handling 
 */
require_once 'errors.php';

/**
 * The autoload 
 */
require_once 'autoload.php';

?>
