<?php

class APILimiter{

	protected $userIP;
	protected $cacheDir;
	protected $cachePrefix = "LIMITER-";
	protected $cacheExtension = ".cache";

	public function __construct( $minute_limit = 20, $minute = 60){
		//check if caching is enabled, otherwise this class will not work
		if(defined('ENABLECACHING') and ENABLECACHING):
			if (defined('CACHEDIR')):
	            $this->cacheDir = CACHEDIR;
	        else:
	            $this->cacheDir = "/tmp";
	        endif;
			$this->userIP = $this->getRealIPAddress();
			$data = $this->get($this->userIP);
			$last_api_request = 0;
			if(isset($data['time'])):
				$last_api_request = $data['time'];
			endif;
			$last_api_diff = time() - $last_api_request; # in seconds
			$minute_throttle = 0;#$this->get_throttle_minute(); # get from the DB
			if(isset($data['throttle'])):
				$minute_throttle = $data['throttle'];
			endif;
			if ( is_null( $minute_limit ) ):
			    $new_minute_throttle = 0;
			else:
			    $new_minute_throttle = $minute_throttle - $last_api_diff;
			    $new_minute_throttle = $new_minute_throttle < 0 ? 0 : $new_minute_throttle;
			    $new_minute_throttle +=	$minute / $minute_limit;
			    $minute_hits_remaining = floor( ( $minute - $new_minute_throttle ) * $minute_limit / $minute  );
			    $minute_hits_remaining = $minute_hits_remaining >= 0 ? $minute_hits_remaining : 0;
			endif;
			if ( $new_minute_throttle > $minute ):
			    $wait = ceil( $new_minute_throttle - $minute );
				
			    usleep( 250000 );
			    exit ( 'Sorry, The one-minute API limit of ' . $minute_limit . ' requests has been exceeded. Please wait ' . $wait . ' seconds before attempting again.' );
			endif;
			$data_to_save=array("ip" => $this->userIP, "time" => time(), "throttle"=>$new_minute_throttle);
			$this->set($this->userIP, $data_to_save);
		endif;
	}

	private function getRealIPAddress(){
		if (!empty($_SERVER['HTTP_CLIENT_IP'])):
		    return $_SERVER['HTTP_CLIENT_IP'];
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])):
		    return $_SERVER['HTTP_X_FORWARDED_FOR'];
		else:
		    return $_SERVER['REMOTE_ADDR'];
		endif;
	}

	/**
     * Sets Cache file path
     * @access private
     * @param string $key
     * @return string
     */
    private function cacheFile() {
        $cacheFilename = $this->userIP;
        $segments = explode(".", $this->userIP);
        $subdir = $this->cachePrefix.end($segments);
        $this->validateCacheDir($this->cacheDir.$subdir);
        return sprintf("%s/%s", $this->cacheDir.$subdir, $cacheFilename . $this->cacheExtension);
    }

    private function validateCacheDir($dir) {
        if (!is_dir($dir)):
            mkdir($dir, 0777, true);
        endif;
    }


    /**
     * Sets Cache 
     * @param string $key
     * @param mixed $data
     * @return boolean
     */
    public function set($key, $data) {
        $cacheFilePath = $this->cacheFile($key);
        if (!$fp = fopen($cacheFilePath, 'wb')):
            return false;
        endif;
        if (flock($fp, LOCK_EX)):
            fwrite($fp, serialize($data));
            flock($fp, LOCK_UN);
        else:
            return false;
        endif;
        fclose($fp);
        return true;
    }

    /**
     * Get cached file if not expired, removes expired cache files
     * @accss public
     * @param string $key
     * @param int $expiration time in seconds
     * @return boolean
     */
    public function get($key, $expiration = 86400) {
        $cacheFilePath = $this->cacheFile($key);
        if (!@file_exists($cacheFilePath)):
            return false;
        endif;
        if (filemtime($cacheFilePath) < (time() - $expiration)):
            $this->delete($key);
            return false;
        endif;
        if (!$fp = @fopen($cacheFilePath, 'rb')):
            return false;
        endif;
        flock($fp, LOCK_SH);
        $cache = unserialize(fread($fp, filesize($cacheFilePath)));
        flock($fp, LOCK_UN);
        fclose($fp);
        return $cache;
    }

    /**
     * Removes cache file
     * @param string $key
     * @return boolean
     */
    public function delete($key) {
        $cache_path = $this->cacheFile($key);
        if (file_exists($cache_path)):
            unlink($cache_path);
            return true;
        endif;
        return false;
    }
}