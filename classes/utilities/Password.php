<?php
/**
 *  Password hashing
 * 
 *  Hashes a password using a random salt and the sha512 algorithm
 * 
 * @filename classes/utilities/Password.php
 * @package Utilities
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * Password hashing
 * @since 0.1
 */
class Password {
    
    /**
     * The method of encryption - sha512
     * @var string 
     */
    private $method = '$6$';
    
    /**
     * The number of times the crypt method should encrypt
     * @var int 
     */
    private $iterations;
    
    /**
     * A dictionary of characters used for salting
     * @var array 
     */
    private $dictionary = array(
            'lowercase' => 'abcdefghijklmnopqrstuvwxyz',
            'uppercase' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'numeric' => '1234567890',
            'symbols' => '!#$%&/()=-_}][{^@:;><'
        );
    
    /**
     * The length of the salt to generate
     * @var int 
     */
    private $salt_length;
    
    /**
     * The types of letters to use in the salt
     * @var string
     */
    private $salt_types;
    
    /**
     * The salt string
     * @var type 
     */
    private $salt;
    
    /**
     * Constructor, sets default values and generates the salt
     * @param int $salt_length
     * @param string $salt_types
     * @param int $iterations 
     */
    public function __construct($salt_length = 10, $salt_types = 'all', $iterations = 10000) {
        $this->salt_length = $salt_length;
        
        $this->salt_types = $salt_types;
        
        $this->iterations = $iterations;
        
        $this->salt = $this->generate_salt();
    }
    
    /**
     * Generates a random salt
     * @return string 
     */
    private function generate_salt() {
        $salt = "";
        
        $chars = $this->return_dictionary_part();
        
        
        $dictionary_size = strlen($chars);
        $arr = str_split($chars);
        
	for($i = 0; $i < $this->salt_length; $i++) {
            $salt .= $arr[mt_rand(0, $dictionary_size - 1)];
	}
	return $salt;
    }
    
    /**
     * Creates a hash from the given password using the number of iterations and the salt generated
     * @param string $password
     * @return string 
     */
    public function create_hash($password) {
        
        $salty = $this->method . 'rounds=' . $this->iterations . '$' . $this->salt . '$';
        
        return crypt($password, $salty);
        
    }
    
    /**
     * Compares a plain text password to a hash
     * @param string $password
     * @param string $hash
     * @return boolean 
     */
    public function compare($password, $hash) {
        if(crypt($password, $hash) === $hash) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Returns the correct part of the dictionary based on the salt_types variable
     * @return string 
     */
    private function return_dictionary_part() {
        $chars = '';
        
        if(is_string($this->salt_types)) {
            switch($this->salt_types) {
                case 'all':
                default:
                    $chars = implode('', $this->dictionary);
                    break;
                case 'lowercase':
                    $chars = $this->dictionary['lowercase'];
                    break;
                case 'uppercase':
                    $chars = $this->dictionary['uppercase'];
                    break;
                case 'numeric':
                    $chars = $this->dictionary['numeric'];
                    break;
                case 'symbols':
                    $chars = $this->dictionary['symbols'];
                    break;
            }
        } elseif(is_array($this->salt_types)) {
            foreach($this->salt_types as $type) {
                if(preg_match("(lowercase|uppercase|numeric|symbols)", $type)) {
                    $chars .= $this->dictionary[$type];
                } else {
                    $chars = implode('', $this->dictionary);
                }
            }
        }
        
        return $chars;
    }
    
}
?>