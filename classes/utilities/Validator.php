<?php
/**
 *  Validates data
 * 
 *  Validates some data formatted like type[data]
 * Currently the validator can only validate data if it's in an array...
 * 
 * @filename classes/utilities/Validator.php
 * @package Utilities
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * Validates data formatted like type[data]
 * @since 0.1
 */
class Validator {
    
    /**
     * Supported data types
     * When creating forms it might be a good idea to echo out one of these instead of writing the string
     * to make sure there are no typos
     * @var array
     */
    public static $data_types = array(
        'first_name',
        'last_name',
        'email',
        'password',
        'text',
        'url',
        'number',
        'postcode',
        'creditcard',
        'html_text'
    );
    
    /**
     * The error messages for each type
     * @var array 
     */
    private $error_messages = array(
        'first_name' => 'Please write your first name only. Names must only contain English alphabetic characters, no spaces or punctuation marks.',
        'last_name' => 'Please write your last name only. Names must only contain English alphabetic characters, no spaces or punctuation marks.',
        'email' => 'That is not a valid email address.',
        'password' => 'Passwords must be 8 or more characters and contain at least 1 lowercase and 1 uppercase character.',
        'text' => 'Text must not contain any HTML.',
        'url' => 'That is not a valid URL.',
        'number' => 'That is not a valid number.',
        'html_text' => 'That is not valid text.'
    );
    
    /**
     * An array holding the unvalidated data
     * @var array 
     */
    private $raw_data;
    
    /**
     * An array holding the validated data
     * @var array 
     */
    private $validated_data;
    
    /**
     * An array holding the error messages for the data that didn't validate
     * @var array 
     */
    private $validation_errors;
    
    /**
     * An array holding the data that didn't validate
     * @var array 
     */
    private $invalids;
    
    /**
     * Constructor, accepts an array of data to be validated
     * @param array $raw_data 
     */
    public function __construct($raw_data){
        $this->raw_data = $raw_data;

        $this->validate();
    }
    
    /**
     * Validates the data passed to the constructor
     * @return mixed 
     * @throws ArtInvalidDataTypeException
     */
    private function validate() {
        foreach($this->raw_data as $type => $data) {
            $function = 'is_valid_' . $type;
            if(method_exists($this, $function)) {
                if($valid = $this->$function($data)) {
                    $this->validated_data[$type] = $valid;
                } else {
                    $this->invalids[$type] = $valid;
                }
            } else {
                throw new ArtInvalidDataTypeException('The data type "' . $type . '" is not supported by the Validator class.', ArtException::INVALID_DATA_TYPE);
            }
        }
    }
    
    /**
     * Returns the validated data or appropiate error message
     * @return array 
     */
    public function return_valid_data() {
        if(count($this->invalids) != 0) {
            $this->get_validation_error();
            $this->validation_errors['valid'] = 'false';
            return $this->validation_errors;
        } else {
            $this->validated_data['valid'] = 'true';
            return $this->validated_data;
        }
    }
    
    /**
     * Adds the right error message to the validation errors array 
     */
    private function get_validation_error() {
        foreach($this->invalids as $type => $data) {
            $this->validation_errors[$type] = $this->error_messages[$type];
        }
    }
    
    /**
     * Checks a piece of data is a valid first name
     * @param string $data
     * @return boolean 
     */
    private function is_valid_first_name($data) {
        $data = trim($data);
        if(preg_match('(/^[A-Z]+$/i)', $data)) {
            return false;
        } else {
            return $data;
        } 
    }
    
    /**
     * Checks a piece of data is a valid last name
     * @param string $data
     * @return boolean 
     */
    private function is_valid_last_name($data) {
        $data = trim($data);
        if(preg_match('(/^[A-Z]+$/i)', $data)) {
            return false;
        } else {
            return $data;
        } 
    }
    
    /**
     * Checks a piece of data is a valid email address
     * @param string $data
     * @return boolean 
     */
    private function is_valid_email($data) {
        $data = trim($data);
        if(preg_match('(^.*[a-zA-Z0-9-_\.]+@.*[a-zA-Z0-9-_\.]+[\.]+.*[a-z\.]{2,6})', $data)) {
            return $data;
        } else {
            return false;
        }
    }
    
    /**
     * Checks a piece of data is a strong password
     * @param string $data
     * @return boolean 
     */
    private function is_valid_password($data) {
        $data = trim($data);
        if(preg_match('(^.*(?=.{8})(?=.*[a-z])(?=.*[A-Z]).*$)', $data)) {
            return $data;
        } else {
            return false;
        }
    }
    
    /**
     * Checks a piece of data is valid text - without HTML
     * @param string $data
     * @return boolean 
     */
    private function is_valid_text($data) {
        $data = trim($data);
        if(strlen($data) != strlen(strip_tags($data))) {
            return false;
        } else {
            return $data;
        }
    }
    
    /**
     * Checks a piece of data is a valid URL
     * @param string $data
     * @return boolean 
     */
    private function is_valid_url($data) {
        $data = trim($data);
        if(preg_match("/\b(?:(?:https?):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $data)) {
            return $data;
        } else {
            return false;
        }
    }
    
    /**
     * Checks a piece of data is a valid number
     * @param string/int $data
     * @return boolean 
     */
    private function is_valid_number($data) {
        $data = trim($data);
        if(is_numeric($data)) {
            return $data;
        } else {
            return false;
        }
    }
    
    /**
     * Trims and converts HTML text to html entities
     * @param string $data
     * @return string 
     */
    private function is_valid_html_text($data) {
        $data = trim(htmlentities($data));
        
        return $data;
    }
}
?>