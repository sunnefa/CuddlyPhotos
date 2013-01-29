<?php
/**
 *  Controller for get requests
 * 
 *  Loads the correct page and corresponding modules based on the parameters of the get request
 * 
 * @filename controllers/get.php
 * @package Controllers
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

$page_slug = $_GET['page'];

try {
    $page = new Page($sql, $page_slug);
    
    foreach($page->page_modules as $display_order => $module) {
        if($module->module_is_active == 1) {
            include MODULES . $module->module_path;
        }
    }
    
} catch(Exception $e) {
    echo $e->getMessage();
}
?>