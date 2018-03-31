<?php
class Response {
    private $data;
	private $feedbacks = array();
	protected static $type = 'response';
    
    public function test($positive, $message) {
        if ($positive) $this->addOne($message);
        return $positive;
    }

    public function addOne($message) {
        $this->feedbacks[] = $message;
    }
    
    public function addMany(array $messageArr) {
        $this->feedbacks = array_merge($messageArr, $this->feedbacks);
    }
	
	public function addData($key, $val) {
		$this->data[$key] = $val;
	}
	
	public function handleRedirect($feedbackType, $url) {
		if ($this->exist()) {
			if (Get::hasData('ajax') && Get::find('ajax') === '1') {
				
				$obj = array(static::$type => array($feedbackType => $this->toReadable()));
				
				echo json_encode($obj);
				
				die;
			} else {
				Session::set($feedbackType, $this);
				redirect($url);
			}
		}
	}
	
	public function handleStop($handler) {
		if ($this->exist()) {
			if (!empty($handler)) $handler();
			die;
		}
	}
    
    public function total() {
        return count($this->feedbacks);
    }
    
    public function exist() {
        return $this->total() > 0;
    }
    
    public function none() {
        return $this->total() === 0;
    }
    
    public function toQueryString() {
        return '?' . http_build_query(array('feedbacks' => urlencode($this->toJSON())));
    }
    
    public function toJSON() {
        return json_encode($this->feedbacks);
    }
    
    public function toString() {
        return implode(', ', $this->feedbacks);
    }
	
	public function toReadable() {
		return implode(' ', $this->feedbacks);
	}
}

class Errors extends Response {
	protected static $type = 'errors';
	
	public function test($success, $message) {
		parent::test(!$success, $message);
	}
}