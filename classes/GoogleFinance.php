<?php

class GoogleFinance extends Service{

	protected $serviceURL = 'http://rate-exchange.appspot.com/currency?';

	public function __construct(){
		parent::__construct();
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
				$this->serviceURL = $this->serviceURL . 'from=' . $this->from . '&to=' . $this->to;
				$serviceResult = json_decode($this->request($this->serviceURL), true);
				$result = $serviceResult['rate'] * $this->amount;
				return $result;
		endif;
		return false;
	}



}