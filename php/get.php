<?php
class Get {
    public static function notSet(array $arr) {
        $errors = array();
        
        foreach ($arr as $key) {
            if (!self::exists($key)) $errors[] = $key;
        }
        
        return $errors;
    }
    
    public static function noData() {
        $errors = array();
        
        foreach ($arr as $key) {
            if (!self::hasData($key)) $errors[] = $key;
        }
        
        return $errors;
    }
	
    public static function find($key) { //was supposed to be called 'get' but that gets confused with a constructor
        return trim($_GET[$key]);
    }
    
    public static function exists($key) {
        return isset($_GET[$key]);
    }
    
    public static function hasData($key) {
        return !empty($_GET[$key]) && trim($_GET[$key]) !== '';
    }
}