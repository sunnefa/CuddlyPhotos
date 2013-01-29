<?php
/**
 *  Static helper methods
 * 
 *  This class includes a few helper methods such as array_flat, n2p and generate_password
 * 
 * @filename classes/utilities/Functions.php
 * @package Utilities
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * Some static helper methods
 * @since 0.1
 * @static
 */
class Functions {
    /**
     * Flattens a multidimensional array to one dimension. Should only used when there is only one array in the
     * second dimension because then the second dimension isn't needed
     * @param array $array
     * @return array 
     */
    static function array_flat($array) {
        $single = array();
        foreach($array as $one) {
            foreach($one as $key => $value) {
                $single[$key] = $value;	
            }
        }
        return $single;
    }
    
    /**
     * Converts the newline character "\n" to p tags in a string
     * @param string $str
     * @return string 
     */
    static function n2p($str) {
        $str = "\t<p>" . preg_replace("(\n|\r)", "</p>$0\t<p>", $str) . "</p>";
        return $str;
    }
    
    /**
     * Removes all p tags from a string
     * @param string $str
     * @return string 
     */
    static function remove_p($str) {
        $str = preg_match("(<p>)", $str) ? str_replace('<p>', '', $str) : $str;
	$str = preg_match("(</p>)", $str) ? str_replace('</p>', '', $str) : $str;
	return $str;
    }
    
    /**
     * Truncates text to the number of words given
     * @param string $string
     * @param int $length
     * @param string $trail
     * @return string 
     */
    static function truncate($string, $length = 10, $trail = "&nbsp;&hellip;") {
        $array = explode(" ", $string);
        if(count($array) > $length) {
            array_splice($array, $length);
            return implode(" ", $array) . $trail;
        }
        return $string;
    }
    
    /**
     * Replaces all spaces in string with underscores
     * @param string $text
     * @return string 
     */
    static function space2under($text) {
        return preg_replace("(\ )", "_", $text);
    }
    
    /**
     * Replaces all underscores with spaces in a string
     * @param string $text
     * @return string 
     */
    static function under2space($text) {
        return preg_replace("(_)", " ", $text);
    }
    
    /**
     * Reloads the page - internal redirects only
     * @param string $where 
     */
    static function reload($where = "") {
        $base = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
        if(!empty($where)) {
            header('Location: ' . $base . $where);
            exit;
        } elseif(!empty($_GET)) {
            $query = implode('/', $_GET);
            header('Location: ' . $base . $query);
            exit;
        } else {
            header('Location: ' . $base);
            exit;
        }
    }
    
    /**
     * Shorthand for header location - external sites only
     * @param type $url 
     */
    static function visit($url) {
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Replaces tokens of the type "{TOKEN}" with the given replacements in a given string
     * @param string $text
     * @param array $tokens_replacements
     * @return string 
     */
    static function replace_tokens($text, $tokens_replacements) {
        foreach($tokens_replacements as $token => $replacement) {
            $text = preg_replace('(\{' . $token . '\})', $replacement, $text);
        }

        return $text;
    }
    
    /**
     * Returns the base url the application resides in, that is from the browsers point of view
     * @return string
     */
    static function get_base_url() {
        return "http://" . $_SERVER['HTTP_HOST'] . preg_replace("#/[^/]*\.php$#simU", "/", $_SERVER["PHP_SELF"]);
    }
    
    /**
     * Implodes an array and surrounds each element with the selected html tag
     * @param array $array
     * @param string $tag
     * @param boolean $use_comma
     * @return string 
     */
    static function implode_with_tag($array, $tag = '<span>', $use_comma = false) {
        $closing_tag = str_replace('<', '</', $tag);
        
        $comma = (!$use_comma) ? '' : ', ';
        
        $glue = $closing_tag . $comma . $tag;
        
        return $tag . implode($glue, $array) . $closing_tag;
        
    }
}

?>