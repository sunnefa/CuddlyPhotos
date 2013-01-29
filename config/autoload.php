<?php
/**
 *  Autoload function
 * 
 *  This function autoloads classes for inclusion
 * 
 * @filename config/autoload.php
 * @package Configuration
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * Autoload magic function
 * @param string $classname 
 */
function __autoload($classname) {
    $class_folders = array('blog', 'core', 'database', 'goodies', 'portfolio', 'utilities');
    
    foreach($class_folders as $folder) {
        $file = CLASSES . $folder . DS . $classname . '.php';
        if(file_exists($file)) {
            require_once $file;
        }
    }
}
?>
