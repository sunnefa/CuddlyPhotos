<?php
/**
 *  The settings table
 * 
 *  CRUD of the settings table + represents all settings from the database
 * 
 * @filename classes/core/Settings.php
 * @package Core
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * Settings table CRUD
 * @since 0.1
 */
class Settings {
    
    /**
     * The name of the settings table
     * @var string 
     */
    private $table_name = 'core__settings';
    
    /**
     * Reference to DBWrapper
     * @var DBWrapper 
     */
    private $db_wrapper;

    function __construct(DBWrapper $sql){
        $this->db_wrapper = $sql;
        
        $this->load_settings();
    }
    
    private function load_settings() {
        
        $results = $this->db_wrapper->select($this->table_name, '*');
        
        if($results === false) {
            throw new Exception('No setting records found');
        } else {
            foreach($results as $setting) {
                $this->$setting['setting_name'] = $setting['setting_value'];
            }
        }
        
    }

}

?>