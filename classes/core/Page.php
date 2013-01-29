<?php
/**
 *  Represents the pages table
 * 
 *  Create, retrieve, update, delete methods for the pages table
 * 
 * @filename classes/core/Page.php
 * @package Core
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * Represents a single page from the pages table
 * @since 0.1
 */
class Page {
    
    /**
     * The id of the page
     * @var int 
     */
    public $page_id;
    
    /**
     * The title of the page
     * @var string 
     */
    public $page_title;
    
    /**
     * The description of the page
     * @var string 
     */
    public $page_description;
    
    /**
     * The "slug" or url of the page - this must be unique
     * @var string 
     */
    public $page_slug;
    
    /**
     * The modules in this page
     * @var array 
     */
    public $page_modules;
    
    /**
     * The name of the pages table
     * @var string 
     */
    private $table_name = 'core__pages';
    
    /**
     * An instance of DBWrapper
     * @var DBWrapper 
     */
    private $db_wrapper;
    
    /**
     * Constructor - assigns DBWrapper and if page slug is given, loads that page
     * @param DBWrapper $db_wrapper
     * @param string $page_slug 
     */
    public function __construct(DBWrapper $db_wrapper, $page_slug = 0) {
        $this->db_wrapper = $db_wrapper;
        
        if($page_slug) {
            $this->select_single_page($page_slug);
        }
    }
    
    /**
     * Loads a single page from the database based on the page slug
     * @param string $page_slug      
     * @throws Exception
     * @throw InvalidArgumentException
     */
    private function select_single_page($page_slug) {
        
        if(!is_string($page_slug)) {
            throw new InvalidArgumentException('Page slug should be a string');
        } else {
            $results = $this->db_wrapper->select($this->table_name, '*', "page_slug = '$page_slug'");
            
            if($results === false) {
                throw new Exception('No page matching the slug ' . $page_slug . ' was found');
            } else {
                $results = Functions::array_flat($results);
                $this->page_description = $results['page_description'];
                $this->page_id = $results['page_id'];
                $this->page_slug = $results['page_slug'];
                $this->page_title = $results['page_title'];
                try {
                    $this->page_modules = $this->load_page_modules($results['page_id']);
                } catch(Exception $e) {
                    throw new Exception($e->getMessage());
                } catch(InvalidArgumentException $e) {
                    throw new InvalidArgumentException($e->getMessage());
                }
            }
            
        }
    }
    
    /**
     * Loads multiple pages from the database based on $limit and $start
     * @param int $limit - if limit is 0 all pages will be loaded
     * @param int $start - offset to start from
     * @return array
     * @throws Exception
     */
    public function select_multiple_pages($limit = 0, $start = 0) {
        
        $limit_string = $start . ', ';
        
        //apparently, this is the official approach when needing offset/start but still unlimited records
        $limit_string .= ($limit == 0) ? '18446744073709551615' : $limit;
        
        $results = $this->db_wrapper->select($this->table_name, '*', NULL, $limit_string);
        
        if($results === false) {
            throw new Exception('No pages were found');
        } else {
            return $results;
        }
    }
    
    /**
     * Loads all the modules associated with this page
     * @param int $page_id 
     * @throws ArtNoRecordsFoundException
     * @throws ArtInvalidIdException
     */
    private function load_page_modules($page_id) {
        
        if(!is_numeric($page_id)) {
            throw new InvalidArgumentException('page_id should be a number');
        } else {
            $results = $this->db_wrapper->select('core__pages_modules', 'module_id', 'page_id = ' . $page_id, null, 'display_order');
            
            if($results === false) {
                throw new Exception('No modules found');
            } else {
                $modules = array();
                foreach($results as $module) {
                    $modules[] = new Module($this->db_wrapper, $module['module_id']);
                }
                
                return $modules;
                
            }
            
        }
    }
    
    /**
     * Edits a single page
     * @param array $page_data 
     * @return boolean
     * @throws ArtMySQLException
     * @throws ArtInvalidArgumentException
     */
    public function edit_page($page_data) {
        if(is_array($page_data)) {
            $update = $this->db_wrapper->update($this->table_name, array(
                'page_title',
                'page_slug',
                'page_description'
            ), array(
                $page_data['title'],
                $page_data['slug'],
                $page_data['description']
            ), "page_id = " . $page_data['id']);
            
            if($update) {
                if(isset($page_data['new_modules']) && isset($page_data['old_modules'])) {
                    $difference = Functions::multidimensional_array_diff($page_data['new_modules'], $page_data['old_modules']);
                    foreach($difference as $diff) {
                        //TODO: add try catch clauses here
                        $check = $this->check_page_module_relationship($diff['module_id'], $page_data['id']);
                        if(!$check) {
                            $this->add_page_module_relationship($diff['module_id'], $page_data['id'], $diff['display_order']);
                        } else {
                            $this->update_page_module_relationship($diff['module_id'], $page_data['id'], $diff['display_order']);
                        }
                    }
                }
                return true;
                
            }
            else throw new ArtMySQLException('Could not update table ' . $this->table_name, ArtException::MYSQL);
        } else {
            throw new ArtInvalidArgumentException('The edit_page method of the Page class expects parameter $page_data to be an array', ArtException::INVALID_ARGUMENT);
        }
    }
    
    /**
     * Adds a single page to the database
     * @param array $page_data
     * @return boolean
     * @throws ArtMySQLException
     * @throws ArtInvalidArgumentException 
     */
    public function add_page($page_data) {
        if(is_array($page_data)) {
            $added = $this->db_wrapper->insert($this->table_name, array(
                'page_title',
                'page_slug',
                'page_description'
            ), array(
                $page_data['title'],
                $page_data['slug'],
                $page_data['description']
            ));
            
            if($added) {
                if(isset($page_data['modules'])) {
                    foreach($page_data['modules'] as $mod) {
                        //TODO: put try catch clause here
                        $this->add_page_module_relationship($mod['module_id'], $page_data['page_id'], $mod['display_order']);
                    }
                }
                return true;
            }
            else throw new ArtMySQLException('Could not insert into table ' . $this->table_name, ArtException::MYSQL);
            
        } else {
            throw new ArtInvalidArgumentException('The add_page method of the Page class expects parameter $page_data to be an array', ArtException::INVALID_ARGUMENT);
        }
    }
    
    /**
     * Deletes a single page from the database
     * @param int $page_id
     * @return boolean
     * @throws ArtMySQLException
     * @throws ArtInvalidIdException
     */
    public function delete_page($page_id) {
        if(is_numeric($page_id)) {
            $deleted = $this->db_wrapper->delete($this->table_name, 'page_id = ' . $page_id);
            
            if($deleted) {
                //TODO: add try catch clause here
                $this->remove_page_module_relationship_by_page($page_id);
                return true;
            }
            else throw new ArtMySQLException('Could not delete from table ' . $this->table_name, ArtException::MYSQL);
        } else {
            throw new ArtInvalidIdException('Invalid id passes to delete_page method of Page class.', ArtException::INVALID_ID);
        }
    }
    
    /**
     * Updates the relationship between a single page and single module
     * @param int $module_id
     * @param int $page_id
     * @param int $display_order
     * @return boolean
     * @throws ArtInvalidArgumentException
     */
    private function update_page_module_relationship($module_id, $page_id, $display_order) {
        if(is_numeric($module_id) && is_numeric($display_order) && is_numeric($page_id)) {
            $update = $this->db_wrapper->update('core__pages_modules', array('display_order'), array($display_order), 'page_id = ' . $page_id . ' AND module_id = ' . $module_id);
            
            if($update) return true;
            else return false;
        } else {
            throw new ArtInvalidArgumentException('The update_page_module_relationship method of the Page class expects the parameters $module_id, $page_id and $display_order to be numeric.', ArtException::INVALID_ARGUMENT);
        }
    }
    
    /**
     * Checks if a module has a relationship with a page
     * @param int $module_id
     * @param int $page_id
     * @return boolean 
     * @throws ArtInvalidArgumentException
     */
    private function check_page_module_relationship($module_id, $page_id) {
        if(is_numeric($page_id) && is_numeric($module_id)) {
            $has_relationship = $this->db_wrapper->select('core__pages_modules', '*', 'page_id = ' . $page_id . ' AND module_id = ' . $module_id);
            
            if($has_relationship) return true;
            else return false;
            
        } else {
            throw new ArtInvalidArgumentException('The update_page_module_relationship method of the Page class expects the parameters $module_id and $page_id to be numeric.', ArtException::INVALID_ARGUMENT);
        }
    }
    
    /**
     * Removes the relationship between a page and a module
     * @param int $module_id
     * @param int $page_id
     * @return boolean 
     * @throws ArtInvalidArgumentException
     */
    private function remove_page_module_relationship($module_id, $page_id) {
        if(is_numeric($page_id) && is_numeric($module_id)) {
            $deleted = $this->db_wrapper->delete('core__pages_modules', 'page_id = ' . $page_id . ' AND module_id = ' . $module_id);
            
            if($deleted) return true;
            else return false;
            
        } else {
            throw new ArtInvalidArgumentException('The update_page_module_relationship method of the Page class expects the parameters $module_id and $page_id to be numeric.', ArtException::INVALID_ARGUMENT);
        }
    }
    
    /**
     * Adds the relationship between a page and a module
     * @param int $module_id
     * @param int $page_id
     * @param int $display_order
     * @return boolean 
     * @throws ArtInvalidArgumentException
     */
    private function add_page_module_relationship($module_id, $page_id, $display_order) {
        if(is_numeric($display_order) && is_numeric($module_id) && is_numeric($page_id)) {
            $added = $this->db_wrapper->insert('core__pages_modules', array('module_id', 'page_id', 'display_order'), array($module_id, $page_id, $display_order));
            
            if($added) return true;
            else return false;
            
        } else {
            throw new ArtInvalidArgumentException('The update_page_module_relationship method of the Page class expects the parameters $module_id, $page_id and $display_order to be numeric.', ArtException::INVALID_ARGUMENT);
        }
    }
    
    /**
     * Removes all page-module relationships by page id
     * @param int $page_id
     * @return boolean 
     * @throws ArtInvalidArgumentException
     */
    private function remove_page_module_relationship_by_page($page_id) {
        if(is_numeric($page_id)) {
            $deleted = $this->db_wrapper->delete('core__pages_modules', 'page_id = ' . $page_id);
            
            if($deleted) return true;
            else return false;
        } else {
            throw new ArtInvalidArgumentException('The update_page_module_relationship method of the Page class expects the parameter $page_id to be numeric.', ArtException::INVALID_ARGUMENT);
        }
    }
}
?>