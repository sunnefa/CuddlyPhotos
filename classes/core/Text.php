<?php
/**
 *  Represents text table
 * 
 *  CRUD for the text table - represents a single text from the database
 * 
 * @filename classes/core/Text.php
 * @package Core
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * Text table
 * @since 0.1
 */
class Text {
    
    /**
     * The id of the text
     * @var int 
     */
    public $text_id;
    
    /**
     * The name of the text - identifies the text to the user
     * @var string 
     */
    public $text_name;
    
    /**
     * The actual text
     * @var string 
     */
    public $text;
    
    /**
     * The name of the text table
     * @var type 
     */
    private $table_name = 'core__text';
    
    /**
     * Instance of DBWrapper
     * @var type 
     */
    private $db_wrapper;
    
    /**
     * Constructor - assigns DBWrapper and selects a text based on page info
     * @param DBWrapper $db_wrapper
     * @param array $page_data 
     */
    public function __construct(DBWrapper $db_wrapper, $page_data = array()) {
        $this->db_wrapper = $db_wrapper;
        
        if($page_data) {
            $this->select_single_text_by_page($page_data);
        }
    }
    
    /**
     * Selects a single text based on page and display order
     * @param array $page_data
     * @throws OutOfBoundsException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    private function select_single_text_by_page($page_data) {
        
        if(!is_array($page_data)) {
            throw new InvalidArgumentException('page_data must be an array');
        } else {
            if(!isset($page_data['page_id']) || !isset($page_data['display_order'])) {
                throw new OutOfBoundsException('page_data must include both page_id and display_order');
            } else {
                $joins = $this->db_wrapper->build_joins('core__text_pages AS p', array('p.text_id', 't.text_id'), 'left');
                $results = $this->db_wrapper->select($this->table_name . ' AS t', '*', 'p.page_id = ' . $page_data['page_id'] . ' AND p.display_order = ' . $page_data['display_order'], null, null, null, $joins);
                
                if($results === false) {
                    throw new Exception('No text was found for that page with that display order');
                } else {
                    $results = Functions::array_flat($results);
                    $this->text = html_entity_decode($results['text']);
                    $this->text_id = $results['text_id'];
                    $this->text_name = $results['text_name'];
                }
                
            }
        }
    }
    
    /**
     * Selects a single text by text_id
     * @param int $text_id
     * @return array 
     * @throws ArtNoRecordsFoundException
     * @throws ArtInvalidIdException
     */
    public function select_single_text_by_id($text_id) {
        if(is_numeric($text_id)) {
            $text = $this->db_wrapper->select($this->table_name, '*', 'text_id = ' . $text_id);
            
            if($text) return $text;
            else {
                $message = 'No text found with the id ' . $text_id;
                throw new ArtNoRecordsFoundException($message, ArtException::NO_RECORDS_FOUND);
            }
        } else {
            $message = 'Invalid id supplied to select_single_text_by_id method of Text class.';
            throw new ArtInvalidIdException($message, ArtException::INVALID_ID);
        }
    }
    
    /**
     * Selects multiple texts regardless of page
     * @param int $limit
     * @param int $start
     * @return array
     * @throws ArtNoRecordsFoundException
     */
    public function select_multiple_text($limit = 0, $start = 0) {
        
        $limit_string = $start . ', ';
        
        //apparently, this is the official approach when needing offset/start but still unlimited records
        $limit_string .= ($limit == 0) ? '18446744073709551615' : $limit;
        
        $texts = $this->db_wrapper->select($this->table_name, '*', null, $limit_string);
        
        if($texts) return $texts;
        else throw new ArtNoRecordsFoundException('No records were found in the ' . $this->table_name . ' table.', ArtException::NO_RECORDS_FOUND);
    }
    
    /**
     * Edits a text
     * @param array $text_data
     * @return boolean 
     * @throws ArtMySQLException
     * @throws ArtInvalidArgumentException
     */
    public function edit_text($text_data) {
        if(is_array($text_data)) {
            $update = $this->db_wrapper->update($this->table_name, array('text', 'text_name'), array(htmlentities($text_data['text']), $text_data['text_name']), 'text_id = ' . $text_data['id']);
            
            if($update) return true;
            else {
                throw new ArtMySQLException('Could not update table ' . $this->table_name, ArtException::MYSQL);
            }
            
        } else {
            $message = 'The edit_text method of the Text class expects the parameter $text_data to be an array.';
            throw new ArtInvalidArgumentException($message, ArtException::INVALID_ARGUMENT);
        }
    }
    
    /**
     * Adds a text
     * @param array $text_data
     * @return boolean
     * @throws ArtMySQLException
     * @throws ArtInvalidArgumentException 
     */
    public function add_text($text_data) {
        if(is_array($text_data)) {
            $add = $this->db_wrapper->insert($this->table_name, array('text', 'text_name'), array(htmlentities($text_data['text']), $text_data['text_name']));
            
            if($add) return true;
            else {
                throw new ArtMySQLException('Could not insert into table ' . $this->table_name, ArtException::MYSQL);
            }
        } else {
            $message = 'The add_text method of the Text class expects the parameter $text_data to be an array.';
            throw new ArtInvalidArgumentException($message, ArtException::INVALID_ARGUMENT);
        }
    }
    
    /**
     * Removes a text from the database
     * TODO: check if the text being deleted has a relationship with a page and delete it
     * @param int $text_id
     * @return boolean
     * @throws ArtMySQLException
     * @throws ArtInvalidIdException 
     */
    public function remove_text($text_id) {
        if(is_numeric($text_id)) {
            $delete = $this->db_wrapper->delete($this->table_name, 'text_id = ' . $text_id);
            
            //here we should make checks to see if the text being deleted has a relationship with a
            //page and remove it if it does
            
            if($delete) return true;
            else {
                throw new ArtMySQLException('Could not delete from table ' . $this->table_name, ArtException::MYSQL);
            }
        } else {
            $message = 'Invalid id supplied to remove_text method of Text class.';
            throw new ArtInvalidIdException($message, ArtException::INVALID_ID);
        }
    }
    
    /**
     * Adds a relationship between text and page
     * @param int $page_id
     * @param int $text_id
     * @param int $display_order
     * @return boolean
     * @throws ArtMySQLException
     * @throws ArtInvalidArgumentException 
     */
    public function add_page_text_relationship($page_id, $text_id, $display_order) {
        if(is_numeric($page_id) && is_numeric($text_id) && is_numeric($display_order)) {
            $add = $this->db_wrapper->insert('core__text_pages', array('page_id', 'text_id', 'display_order'), array($page_id, $text_id, $display_order));
            
            if($add) return true;
            else throw new ArtMySQLException('Could not insert into table core__text_pages', ArtException::MYSQL);
            
        } else {
            $message = 'The add_page_text_relationship method of the Text class expects the parameters $page_id, $text_id and $display_order to be numeric.';
            throw new ArtInvalidArgumentException($message, ArtException::INVALID_ARGUMENT);
        }
    }
    
    /**
     * Checks if a relationship between text and page exists
     * @param int $page_id
     * @param int $text_id
     * @return boolean
     * @throws ArtInvalidArgumentException
     */
    private function check_page_text_relationship($page_id, $text_id) {
        if(is_numeric($page_id) && is_numeric($text_id)) {
            $has_relationship = $this->db_wrapper->select('core__text_pages', '*', 'page_id = ' . $page_id . ' AND text_id = ' . $text_id);
            
            if($has_relationship) return true;
            else return false;
            
        } else {
            $message = 'The check_page_text_relationship method of the Text class expects the parameters $page_id and $text_id to be numeric.';
            throw new ArtInvalidArgumentException($message, ArtException::INVALID_ARGUMENT);
        }
    }
    
    /**
     * Removes a page text relationship
     * @param int $page_id
     * @param int $text_id
     * @return boolean
     * @throws ArtMySQLException
     * @throws ArtInvalidArgumentException 
     */
    public function remove_page_text_relationship($page_id, $text_id) {
        if(is_numeric($page_id) && is_numeric($text_id)) {
            $delete = $this->db_wrapper->delete('core__text_pages', 'page_id = ' . $page_id . ' AND text_id = ' . $text_id);
            
            if($delete) return true;
            else throw new ArtMySQLException('Could not delete from table core__text_pages', ArtException::MYSQL);
        } else {
            $message = 'The remove_page_text_relationship method of the Text class expects the parameters $page_id and $text_id to be numeric.';
            throw new ArtInvalidArgumentException($message, ArtException::INVALID_ARGUMENT);
        }
    }
    
    /**
     * Edits a page text relationship
     * @param int $page_id
     * @param int $text_id
     * @param int $display_order
     * @return boolean
     * @throws ArtMySQLException
     * @throws ArtInvalidArgumentException 
     */
    public function edit_page_text_relationship($page_id, $text_id, $display_order) {
        if(is_numeric($page_id) && is_numeric($text_id) && is_numeric($display_order)) {
            $update = $this->db_wrapper->update('core__text_pages', array('display_order'), array($display_order), 'page_id = ' . $page_id . ' AND text_id = ' . $text_id);
            
            if($update) return true;
            else throw new ArtMySQLException('Could not update table core__text_pages', ArtException::MYSQL);
        } else {
            $message = 'The edit_page_text_relationship method of the Text class expects the parameters $page_id, $text_id and $display_order to be numeric.';
            throw new ArtInvalidArgumentException($message, ArtException::INVALID_ARGUMENT);
        }
    }
}
?>