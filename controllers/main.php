<?php
/**
 *  Main controller
 * 
 *  Differentiates between get and post requests
 * 
 * @filename controllers/main.php
 * @package Controllers
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        require_once 'get.php';
        break;
    case 'POST':
        require_once 'post.php';
        break;
}
?>
