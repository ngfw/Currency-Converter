<?php


abstract class Service{

	protected $amount;
	protected $from;
	protected $to;
	protected $httpclient;
	protected $Filecache;
	protected $requestURL;

	public function __construct(){
		$this->httpclient = new Httpclient;
		if(ENABLECACHING):
			$this->Filecache = new Filecache;
		endif;
		$this->setAmount(1);
	}

	public function setAmount($amount){
		if(isset($amount) AND is_numeric($amount)):
			$this->amount = $amount;
		else:
			$this->amount = 1;
		endif;
		return true;
	}

	public function setFrom($from){
		if(isset($from) AND strlen($from) == 3):
			$this->from = $from;
			return true;
		endif;
		return false;
	}

	public function setTo($to){
		if(isset($to) AND strlen($to) == 3):
			$this->to = $to;
			return true;
		endif;
		return false;
	}

	public function request($url){
		if(isset($url) and !empty($url)):
			$result = false;
			if(ENABLECACHING):
				$result = $this->Filecache->get($url, CACHEEXPIRATIONTIME);
			endif;
			if(!$result):
				$this->httpclient->setUri($url);
				$result = $this->httpclient->request();
				if(ENABLECACHING):
					$this->Filecache->set($url, $result['content']);
				endif;
				return $result['content'];
			endif;
			return $result;
		endif;
		return false;
	}

}