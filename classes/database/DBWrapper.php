<?php
/**
 *  Abstract database wrapper
 * 
 *  Abstract database abstraction layer, all child classes must implement all methods
 * 
 * @filename DBWrapper.php
 * @package 
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * Abstract database abstraction layer.
 * 
 * All child classes extending this class must implement all methods.
 *
 * @since 0.1
 * @abstract
 */
abstract class DBWrapper {
    
    /**
     * The database username
     * @var string 
     */
    protected $db_user;
    
    /**
     * The database host name
     * @var string
     */
    protected $db_host;
    
    /**
     * The database name
     * @var string 
     */
    protected $db_name;
    
    /**
     * The database password
     * @var string
     */
    protected $db_pass;
    
    /**
     * The database connection
     * @var link identifier 
     */
    protected $db_conn;

    abstract protected function connect();
    
    abstract protected function close();
    
    abstract public function select($table, $fields, $where = null, $limit = null, $order = null, $group = null, $join = null);
    
    abstract public function insert($table, $fields, $values);
    
    abstract protected function execute_query($query);
    
    abstract public function update($table, $fields, $values, $where);
    
    abstract public function delete($table, $where);
}
?>