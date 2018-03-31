<?php
class Template {
	protected $filepath;
	protected $variables = array();
	
	const FILE_SUFFIX = '.php';
	
	public function __construct($fileName) {
		$this->filepath = TEMPLATE_PATH . $fileName . self::FILE_SUFFIX;
	}
	
	public function addVar($key, $value) {
		$this->variables[$key] = $value;
	}
	
	public function addVars(array $arr) {
		$this->variables = array_merge($this->variables, $arr);
	}
	
	//outputs the template
	public function html() {
		ob_start(); //starts buffering the output
		
		extract($this->variables); //turns array into variables in this scope
		
		require $this->filepath; //uses the variables from extract
		
		return ob_get_clean(); //returns buffer contents and clears it
	}
}

class EmailTemplate extends Template {
	public function __construct($filepath) {
		$this->filepath = EMAIL_TEMPLATE_PATH . $filepath . self::FILE_SUFFIX;
	}
}




