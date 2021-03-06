<?php 

class RegistrationValidator{

	private $_passed = false,
			$_errors = array(),
			$_db = null;

	public function __construct(){
		$this->_db = DB::getInstance();
	}

	public function check($source,$items = array()){
		foreach ($items as $item => $rules) {
			foreach ($rules as $rule => $rule_value) {

				$value = trim($source[$item]);
				$item = escape($item);

				if($rule === 'required' && empty($value)){
					$this->addError("{$item} is required");
				} else if(!empty($value)) {
					switch ($rule) {
						case 'min':
							if(strlen($value) < $rule_value){
								$this->addError("{$item} must be a minimum of {$rule_value} characters.");
							}
							break;
						case 'max':
							if(strlen($value) > $rule_value){
								$this->addError("{$item} must be a maximum of {$rule_value} characters.");
							}
							break;
						case 'secure':
							if($value === $source[$rule_value] || $value === 'password'){
								$this->addError("{$item} cannot be identical to {$rule_value} or 'password'.");
							}
							break;
						case 'matches':
							if($value != $source[$rule_value]){
								$this->addError("{$item} must match {$rule_value}.");
							}
							break;
						case 'email':
							if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
								$this->addError("invalid email address.");
							}
							break;						
						case 'unique':
    						$this->_db->get($rule_value, array($item, '=', $value));
    						if ($this->_db->count()) { 
        						$this->addError("{$item} already exists.");
    						}
							break;
					}
				}
			}
			
			
		}

		if(empty($this->_errors)){
			$this->_passed = true;
		}

		return $this;
	}

	private function addError($error){
		$this->_errors[] = $error;
	}

	public function errors(){
		return $this->_errors;
	}

	public function passed(){
		return $this->_passed;		
	}
}