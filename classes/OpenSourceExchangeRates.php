<?php

class OpenSourceExchangeRates extends Service{

	public $serviceURL 		= 'http://openexchangerates.org/api/latest.json?';
	public $serviceAPPID 	= '';

	
	public function __construct(){
		parent::__construct();
		if(defined('Opensourceexchangerate_Application_ID')):
			$this->serviceAPPID = Opensourceexchangerate_Application_ID;
		else:
			throw new Exception('openexchangerates.org Application ID is required, please refer to documentation');
		endif;
	}

	public function getRate($amount, $from, $to){
		if(isset($amount)):
			$this->setAmount($amount);
		endif;
		if(isset($from)):
			$this->setFrom($from);
		endif;
		if(isset($to)):
			$this->setTo($to);
		endif;
		if(isset($this->amount) and !empty($this->amount) and 
			isset($this->from) and !empty($this->from) and 
			isset($this->to) and !empty($this->to)):
				$this->serviceURL = $this->serviceURL . 'app_id='.$this->serviceAPPID;
				$serviceResult = json_decode($this->request($this->serviceURL), true);
				if($serviceResult["base"] == $this->from):
					$result = $serviceResult["rates"][$this->to] * $this->amount;
				else:
					$result = ($serviceResult["rates"][$this->to] * (1/$serviceResult["rates"][$this->from])) * $this->amount;
				endif;
				$result = explode(".", $result);
				return $result[0].".".substr($result[1], 0, 2);
		endif;
		return false;
	}
}