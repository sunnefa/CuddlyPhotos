<?php

/**
 *  Sets some settings
 * 
 *  Sets settings for error reporting, error handling and exception handling
 * 
 * @filename config/settings.php
 * @package Configuration
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * Turn on all error reporting
 * This must be changed the the site goes live 
 */
error_reporting(E_ALL|E_STRICT);

/**
 * Set the error handler to the error handler function 
 */
//set_error_handler('handle_errors');

/**
 * Set the exception handler to the exception handler function 
 */
//set_exception_handler('handle_exceptions');

/**
 * Register a shutdown function for handling fatal errors 
 */
//register_shutdown_function('shutdown');

?>
