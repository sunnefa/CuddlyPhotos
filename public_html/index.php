<?php
/**
 *  Main index file
 *
 *  This is the main index file, the sole access point to this application
 *
 * @filename public_html/index.php
 * @package Controllers
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

session_start();

ob_start();

if(!require_once('../config/init.php')) {
    die("The init file was not found!");
}
try {
    $sql = MySQLWrapper::get_instance();

    $settings = new Settings($sql);

    require_once CONTROLLERS . 'main.php';
} catch(mysqli_sql_exception $e) {
    //todo: implement a prettier fallback for this such as include a file with an error message template
    die('Unable to connect to database. The message returned was: ' . $e->getMessage());
} catch(Exception $e) {
    echo $e->getMessage();
}
ob_end_flush();

?>