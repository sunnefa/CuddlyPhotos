<?php
/**
 *  Template parser
 * 
 *  Replaces a set of tokens in a template and returns the parsed template
 * 
 * @filename classes/utilities/Template.php
 * @package Utilities
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * Parses template files by loading the source and replacing template tokens with correct values
 * @since 0.1
 */
class Template {
    
    /**
     * The template before parsing
     * @var string 
     */
    private $raw_template;
    
    /**
     * The template after parsing
     * @var string 
     */
    private $parsed_template;
    
    /**
     * An associative array where the keys are the tokens and the values are the values to be replaced
     * @var array
     */
    private $tokens_values;
    
    /**
     * Constructor - takes a template file name and an associative array of tokens and values
     * @param string $template
     * @param array $tokens_values 
     */
    public function __construct($template, $tokens_values) {
        if(file_exists($template)) {
            $this->raw_template = file_get_contents($template);
            
            $this->tokens_values = $tokens_values;
        
            $this->parse_template();
        } else {
            throw new Exception('Template ' . $template . ' was not found');
        }
    }
    
    /**
     * Parses the raw template by replacing the tokens with the values 
     */
    private function parse_template() {
        foreach($this->tokens_values as $token) {
            $this->parsed_template = Functions::replace_tokens($this->raw_template, $this->tokens_values);
        }
    }
    
    /**
     * Returns the parsed template
     * @return string 
     */
    public function return_parsed_template() {
        return $this->parsed_template;
    }
}
?>