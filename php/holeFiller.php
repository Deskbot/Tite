<?php

class HoleFiller {
	private $template, $holes, $holeToPos, $posToHoles, $splitTemplate;
	
    /*
     * creates an array of hole positions to the string for that hole
     * creates an array containing segments of the template
     * separated by values in $this->holes, which are also removed
     */
	public function __construct($templateString, array $holeStrings) {
		$this->template = $templateString;
		$this->holes = $holeStrings;
		$this->splitTemplate = array();
		$this->holeToPos = array();
		$this->posToHoles = array();
		
		foreach ($this->holes as $holeType) {
			$this->holeToPos[$holeType] = array();
			$currentHole =& $this->holeToPos[$holeType];
			
			$lastPos = 0;
			while (($lastPos = strpos($this->template, $holeType, $lastPos)) !== false) {
				$currentHole[] = $lastPos;
				$this->posToHoles[$lastPos] = $holeType;
				$lastPos = $lastPos + strlen($holeType);
			}
		}
		
		ksort($this->posToHoles);
		
		$nextStartPos = 0;
		foreach ($this->posToHoles as $holePosition => $holeType) {
			$this->splitTemplate[] = substr($this->template, $nextStartPos, $holePosition - $nextStartPos);
			$nextStartPos = $holePosition + strlen($holeType);
		}
		$this->splitTemplate[] = substr($this->template, $nextStartPos, strlen($this->template)-1);
	}
	
	/*
     * takes associative array
     * find => replace
     * returns the template with the substrings replaced
     */
	public function insert(array $holesToFillers) {
		$filledString = '';
		$orderedHoles = array_values($this->posToHoles);
		
		for ($i=0; $i < count($this->splitTemplate) - 1; $i++) {
			$holeType = $orderedHoles[$i];
			if (array_key_exists($holeType, $holesToFillers)) {
				$filler = $holesToFillers[$holeType];
				$filledString .= $this->splitTemplate[$i] . $filler;
			}
		}
		
		$filledString .= $this->splitTemplate[$i];
		
		return $filledString;
	}
}

/** In hind sight this class is kind of useless because HoleFiller is designed to be re-used
 * This class is that it accepts a file path instead of the template string itself
 * This class interprets the $holeStrings form the $holesToFillers that parent::insert() uses.
 * The insert method from the parent still exists however
*/
class QuickHoleFiller extends HoleFiller {
    private $holesToFillers;
    
    public function __construct($filepath, $holesToFillers) {
        $this->holesToFillers = $holesToFillers;
        
        $templateString = file_get_contents($filepath);
        $holeStrings = array_keys($holesToFillers);
        
        parent::__construct($templateString, $holesToFillers);
        
        $this->insert($holesToFillers);
    }
    
    public function outsert() {
        return $this->insert($this->holesToFillers);
    }
}