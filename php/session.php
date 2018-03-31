<?php
class Session {
    public static function start() {
        if (!self::isStarted()) return session_start();
    }
    
    public static function isStarted() {
        return session_id() !== '';
    }
    
    public static function get($key) {
        return $_SESSION[$key];
    }
    
    public static function set($key, $val) {
        $_SESSION[$key] = $val;
    }
    
    public static function remove($key) {
        unset($_SESSION[$key]);
    }
    
    public static function hasData($key) {
        return !empty($_SESSION[$key]);
    }
}