<?php
/**
 *  Represents a single module
 * 
 *  A single module + CRUD of modules table
 * 
 * @filename classes/core/Module.php
 * @package Core
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * Modules table
 * @since 0.1
 */
class Module {
    
    /**
     * The id of the module
     * @var int 
     */
    public $module_id;
    
    /**
     * The name of the module
     * @var string 
     */
    public $module_name;
    
    /**
     * The path of the module
     * @var string 
     */
    public $module_path;
    
    /**
     * Is the module active or not?
     * @var int 
     */
    public $module_is_active;
    
    /**
     * The name of the modules table
     * @var type 
     */
    private $table_name = 'core__modules';
    
    /**
     * An instance of DBWrapper
     * @var type 
     */
    private $db_wrapper;
    
    /**
     * Constructor, assigns DBWrapper and selects module if applicable
     * @param DBWrapper $db_wrapper
     * @param int $module_id 
     */
    public function __construct(DBWrapper $db_wrapper, $module = null) {
        $this->db_wrapper = $db_wrapper;
        
        if(!is_null($module)) {
            if(is_numeric($module)) {
                $this->select_module_by_id($module);
            } else {
                $this->select_module_by_name($module);
            }
        }
    }
    
    /**
     * Selects a single module based on id
     * @param int $module_id
     * @throws Exception
     * @throws InvalidArgumentException 
     */
    private function select_module_by_id($module_id) {
        
        if(!is_numeric($module_id)) {
            throw new InvalidArgumentException('module_id should be a number');
        } else {
            $results = $this->db_wrapper->select($this->table_name, '*', 'module_id = ' . $module_id);
            
            if($results === false) {
                throw new Exception('No module with id ' . $module_id . ' was found');
            } else {
                $results = Functions::array_flat($results);
                
                $this->module_id = $results['module_id'];
                $this->module_name = $results['module_name'];
                $this->module_path = $results['module_path'];
                $this->module_is_active = $results['module_is_active'];
            }
            
        }
    }
    
    /**
     * Selects gets a module id by name
     * @param string $name
     * @throws NoRecordsFoundException
     * @throws InvalidArgumentException 
     */
    private function select_module_by_name($name) {
        if(!empty($name) && is_string($name)) {
            $module = $this->db_wrapper->select($this->table_name, 'module_id', 'module_name = "' . $name . '"');
            if($module) {
                $this->select_module_by_id($module[0]['module_id']);
            } else {
                throw new ArtNoRecordsFoundException('No module with the name "' . $name . '" was found.', ArtException::NO_RECORDS_FOUND);
            }
        } else {
            throw new ArtInvalidArgumentException('The "select_module_by_name" method in the "Module" class expects a string', ArtException::INVALID_ARGUMENT);
        }
    }
    
    /**
     * Selects all modules based on a specific page id
     * @param int $page_id
     * @return array 
     * @throws ArtNoModulesException
     * @throws ArtInvalidArgumentException
     */
    public function select_modules_by_page_id($page_id) {
        
        if(is_numeric($page_id)) {
            $modules = $this->db_wrapper->select($this->table_name . " AS m", array(
                'm.module_name',
                'm.module_path',
                'm.module_is_active',
                'p.display_order'
            ), "p.page_id = $page_id AND m.module_is_active = 1", null, 'p.display_order', null, $this->db_wrapper->build_joins('core__pages_modules AS p', array('p.module_id', 'm.module_id', 'left')));
            
            if($modules) {
                return $modules;
            } else {
                throw new ArtNoModulesException('The page with the id ' . $page_id . ' has no modules', ArtException::NO_MODULES);
            }
            
        } else {
            throw new ArtInvalidArgumentException('The "select_module_by_page_id" method in the "Module" class expects a number', ArtException::INVALID_ARGUMENT);
        }
    }
    
    /**
     * Select multple modules regardless of page
     * @param int $limit
     * @param int $start
     * @return type 
     * @throws ArtNoRecordsFoundException
     */
    public function select_multiple_modules($limit = 0, $start = 0) {
        
        $limit_string = $start . ', ';
        
        //apparently, this is the official approach when needing offset/start but still unlimited records
        $limit_string .= ($limit == 0) ? '18446744073709551615' : $limit;
        
        $modules = $this->db_wrapper->select($this->table_name, '*', null, $limit_string);
        
        if($modules) {
            return $modules;
        } else {
            throw new ArtNoRecordsFoundException('No records were found in the ' . $this->table_name . ' table.', ArtException::NO_RECORDS_FOUND);
        }
    }
    
    /**
     * Edit a single module based on the module data
     * @param array $module_data
     * @return boolean
     * @throws ArtMySQLException
     * @throws ArtInvalidArgumentException 
     */
    public function edit_module($module_data) {
        if(is_array($module_data)) {
            $update = $this->db_wrapper->update($this->table_name, array('module_name', 'module_path', 'module_is_active'), array($module_data['name'], $module_data['path'], $module_data['is_active']), 'module_id = ' . $module_data['id']);
            if($update) return true;
            else throw new ArtMySQLException('Could not update table ' . $this->table_name, ArtException::MYSQL);
        } else {
            throw new ArtInvalidArgumentException('The edit_module method in the Module class expects parameter $module_data to be an array.', ArtException::INVALID_ARGUMENT);
        }
    }
    
    /**
     * Add a module with the module data to the database
     * @param array $module_data
     * @return boolean
     * @throws ArtMySQLException
     * @throws ArtInvalidArgumentException 
     */
    public function add_module($module_data) {
        if(is_array($module_data)) {
            $add = $this->db_wrapper->insert($this->table_name, array('module_name', 'module_path', 'module_is_active'), array($module_data['name'], $module_data['path'], $module_data['is_active']));
            
            if($add) {
                return true;
            } else {
                throw new ArtMySQLException('Could not insert into table ' . $this->table_name, ArtException::MYSQL);
            }
        } else {
            throw new ArtInvalidArgumentException('The add_module method in the Module class expects parameter $module_data to be an array.', ArtException::INVALID_ARGUMENT);
        }
    }
    
    /**
     * Remove a module from the database
     * 
     * This method should be used very carefully. I think we should not allow anyone 
     * access to it except ourselves, that is we should include some sort of failsafe
     * to this to ensure that the module we are trying to remove is not associated with 
     * a page or multiple pages before this method is invoked.
     * 
     * @param int $module_id
     * @return boolean 
     * @throws ArtMySQLException
     * @throws ArtInvalidIdException
     */
    public function remove_module($module_id) {
        if(is_numeric($module_id)) {
            
            $delete = $this->db_wrapper->delete($this->table_name, 'module_id = ' . $module_id);
            
            if($delete) return true;
            else throw new ArtMySQLException('Could not remove from table ' . $this->table_name, ArtException::MYSQL);
        } else {
            throw new ArtInvalidIdException('Invalid module id passed to remove_module method of Module class', ArtException::INVALID_ID);
        }
    }
}
?>