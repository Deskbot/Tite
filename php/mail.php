<?php
class Mail {
    const LINE_END = '\r\n';
    
    private static $mailer;
    
    private $headers;
    
    //static methods
    public static function get() {
        if (self::$mailer === null) {
            self::$mailer = new self();
        }
        return self::$mailer;
    }
    
    //non-static methods
    public function __construct() {
        $this->headers = 'Content-Type: text/html; characterset=ISO-8859-1' . self::LINE_END;
    }
    
    public function send($to, $subject, $message) {
        return mail($to, $subject, $message, trim($this->headers));
    }
    
    public function addHeader($input) {
        $lineEndLength = strlen(self::LINE_END);
        $inputLength = strlen($input);
        
        //add line ending if it's not there
        if (substr($input, $inputLength - $lineEndLength, $lineEndLength) !== self::LINE_END)
            $input .= self::LINE_END;
        
        $this->headers .= $input;
    }
    
    public function getHeaders() {
        return $this->headers;
    }
}