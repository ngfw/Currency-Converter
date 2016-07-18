<?php

class YahooFinance extends Service{

	protected $serviceURL = 'http://query.yahooapis.com/v1/public/yql?q=select+%2A+from+yahoo.finance.xchange+where+pair+in+%28%22[CURRENCY]%22%29&format=json&env=store://datatables.org/alltableswithkeys';

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
				$this->serviceURL = str_replace("[CURRENCY]", $this->from . $this->to, $this->serviceURL);
				$serviceResult = json_decode($this->request($this->serviceURL), true);
				$rate = $serviceResult['query']['results']['rate']['Rate'];
				$result = $rate * $this->amount;
				return $result;
		endif;
		return false;
	}



}