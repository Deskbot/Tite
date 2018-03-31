<?php
class Post {
	protected static $method;
	
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
    
    public static function get($key) {
        return trim($_POST[$key]);
    }
    
    public static function exists($key) {
        return isset($_POST[$key]);
    }
    
    public static function hasData($key) {
		return isset($_POST[$key]) && !empty($_POST[$key]) && trim($_POST[$key]) !== '';
    }
}