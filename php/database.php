<?php
class Database {
    const DB_FILE = DB_FILE;
    
    private static $db;
    
    private $con;
    
    //static methods
    public static function get() {
        if (self::$db === null) {
            self::$db = new self();
        }
        return self::$db;
    }
    
    //non-static methods
    public function __construct() {
        $this->con = new SQLite3(self::DB_FILE);
    }
    
    /*
     * performs a query with a prepared statement
     * variables can be associative or numeric
     * order of integer keys is taken into account
     * returns false if execution fails
     */
    public function preparedQuery($query, array $variables) {
        $stmt = $this->con->prepare($query) or die(var_dump(debug_backtrace())); //or die($query);
        $tokenNum = 1;
        
        foreach ($variables as $key => $value) {
            if (is_int($key))  $key = $tokenNum;
            else               $key = $key[0] === ':' ? $key : ':' . $key; //prepend colon if it's not there already
            // keys can only be integer or string <http://php.net/manual/en/language.types.array.php>
            
            $stmt->bindValue($key, $value);// or die(var_dump(debug_backtrace()));
            $tokenNum++;
        }
		
        return $stmt->execute();// or die(var_dump(debug_backtrace())); //gives false if execution fails
    }
    
    public function lastId() {
        return $this->con->lastInsertRowID();
    }
	
	public function lastError() {
		return $this->con->lastErrorCode();
	}
	
	public function lastErrorString() {
		return $this->con->lastErrorMsg();
	}
}

/*
class Db_Result {
	private $stmt, $rowNum, $row;
	
	function __construct($stmt) {
		$this->stmt = $stmt;
		$this->rowNum = 0;
		$this->stmt->store_result();
		
		$metadata = $this->stmt->result_metadata();
		$this->params = array();
		$this->row = array();
		
		while ($field = $metadata->fetch_field()) {
			$params[] =& $this->row[$field->name];
		}
		
		call_user_func_array( array($this->stmt, 'bind_result'), $params );
	}
	
	//private functions
	function fetch_assoc() {
		if (++$this->rowNum > $this->stmt->num_rows) {
			return null;
		}
		
		$this->stmt->fetch();
		
		return $this->row;
	}
}
*/