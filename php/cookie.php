<?php
class Cookie {
    const TEN_YEARS = 315360000;
    const BASE_URL = '/';
    
    //sets a cookie with the information given
    public static function set($name, $value, $duration = self::TEN_YEARS) {
        return setcookie($name, $value, time() + $duration, self::BASE_URL);
    }
    
    //gets the value of the cookie of $name
    public static function get($name) {
        return trim($_COOKIE[$name]);
    }
    
    //removes the cookie of $name
    public static function remove($name) {
        self::set($name, '', -1);
    }
}