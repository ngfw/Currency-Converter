<?php

// if date is not set, fix it
if (!ini_get('date.timezone')){
    date_default_timezone_set('America/New_York');
}

//Load configuration, currency array, icons and all classes
$config = include dirname(__FILE__).'/config.php';

define("ENABLECACHING", $config['Enable_caching']);
define("CACHEDIR", $config['CacheDIR']);
define("CACHEEXPIRATIONTIME", $config['CacheExpirationTime']);
if(isset($config['Opensourceexchangerate_Application_ID']) and !empty($config['Opensourceexchangerate_Application_ID'])):
	define("Opensourceexchangerate_Application_ID", $config['Opensourceexchangerate_Application_ID']);
endif;


$currency = include dirname(__FILE__).'/currency.php';
$currencyCodes = array_keys($currency);
$currencyIcons = include dirname(__FILE__).'/currencyIcons.php';

include dirname(__FILE__).'/classes/Filecache.php';
include dirname(__FILE__).'/classes/Httpclient.php';
include dirname(__FILE__).'/classes/Helper.php';
include dirname(__FILE__).'/classes/Service.php';
// see in config which service to use
switch($config['FinanceService']):
	case "Google":
		include dirname(__FILE__).'/classes/GoogleFinance.php';
		$service = new GoogleFinance();
		break;
	case "Yahoo":
		include dirname(__FILE__).'/classes/YahooFinance.php';
		$service = new YahooFinance();
		break;
	case "Opensourceexchangerates":
		include dirname(__FILE__).'/classes/OpenSourceExchangeRates.php';
		$service = new OpenSourceExchangeRates();
		break;
endswitch;
// Set Defaults to avoid php Notice
$amount = $from = $to = false;

// Rate is requested
if(isset($_GET['amount']) and is_numeric($_GET['amount']) and
	isset($_GET['from']) and strlen($_GET['from']) == 3 and in_array($_GET['from'], $currencyCodes) and
	isset($_GET['to']) and strlen($_GET['to']) == 3 and in_array($_GET['from'], $currencyCodes)):
	$amount = $_GET['amount'];
	$from = $_GET['from'];
	$to = $_GET['to'];
	$rate = $service->getRate($amount, $from, $to);
else:
	$amount = 1;
	$from = $config['DefaultFromCurrency'];
	$to = $config['DefaultToCurrency'];
	$rate = $service->getRate($amount, $from, $to);
endif;

?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
<meta charset="utf-8">
<title><?php echo $config['Title']; ?></title>
<meta name="description" content="<?php echo $config['Meta-Description']; ?>">
<meta name="keywords" content="<?php echo $config['Meta-Keywords']; ?>">
<meta property="og:title" content="<?php echo $config['Title']; ?>" />
<meta property="og:type" content="article" />
<meta property="og:url" content="<?php echo Helper::siteURL(); ?>" />
<meta property="og:description" content="<?php echo $config['Meta-Description']; ?>" />

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />
<link href='http://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="Assets/css/bootstrap.min.css">
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
<link href="Assets/css/bootstrap-select.min.css" rel="stylesheet">
<link rel="stylesheet" href="Assets/css/style.css">
</head>
<body>
<div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            
            <a class="navbar-brand" href="/"><?php echo $config['Title']; ?></a>
        </div>
        <div class="navbar-collapse collapse">
            
        </div>
    </div>
</div>
<div class="container">
    
    <div class="col-md-8">
    	<h2 class="headerText"> Currency Converter </h2>
    	<div class="well">
			<form class="form-inline" role="form" method="get" action="/">
			  <div class="form-group amountWrapper">
			    <label class="sr-only" for="amount">Amount</label>
			    <input type="text" class="form-control amountWrapper" name="amount" id="amount" placeholder="Enter amount" <?php if(isset($amount)): echo "value='".$amount."'"; endif;?>>
			  </div>
			  <div class="form-group">
			    <select class="selectpicker show-tick show-menu-arrow" name="from" id="from" data-live-search="true">
			    	<?php 
			    	if(isset($from)):
			    		// override default selection
						$config['DefaultFromCurrency'] = $from;
					endif;
			    	foreach($currency as $code => $country): ?>
			     	<option data-subtext="<?php  if(isset($currencyIcons[$code])): echo $currencyIcons[$code].' '; endif; echo $country; ?>" <?php if(isset($config['DefaultFromCurrency']) and $config['DefaultFromCurrency'] == $code): echo "selected='selected'"; endif;?>><?php echo $code;?></option>
			     	<?php endforeach; ?>
			  	</select>
			  </div>
			  <div class="form-group">
			  	&nbsp;
			  	<a href="javascript:void(0);" id="swapCurrency"><i class="fa fa-arrows-h"></i></a>
			  	&nbsp;
			  </div>
			  <div class="form-group">
			    <select class="selectpicker show-tick show-menu-arrow" name="to" id="to" data-live-search="true">
			     	<?php 
			     	if(isset($to)):
			    		// override default selection
						$config['DefaultToCurrency'] = $to;
					endif;
			     	foreach($currency as $code => $country): ?>
			     	<option data-subtext="<?php  if(isset($currencyIcons[$code])): echo $currencyIcons[$code].' '; endif; echo $country; ?>" <?php if(isset($config['DefaultToCurrency']) and $config['DefaultToCurrency'] == $code): echo "selected='selected'"; endif;?>><?php echo $code;?></option>
			     	<?php endforeach; ?>
			  	</select>
			  </div>
			 
			  <button type="submit" class="btn btn-primary">Go!</button>
			</form>

		</div>
		<div class="panel panel-default">
			<div class="panel-body text-center">
				<h2><?php echo $amount;?> <small><?php echo $from;?></small> <i class="fa fa-ellipsis-h"></i> <?php echo $rate;?> <small><?php echo $to;?></small></h2>
			</div>
		</div>
		<?php include(dirname(__FILE__)."/728x90.html"); ?>
		<br />
		<h2 class="headerText"> Spread the word </h2>
		<div class="well">
			 <ul class="rrssb-buttons">
                    <li class="email">
                        <a href="mailto:?subject=<?php echo urlencode($config['Title']); ?>&amp;body=<?php echo urlencode(Helper::siteURL()."/?".$_SERVER['QUERY_STRING']); ?>">
                            <span class="icon">
                                <i class="fa fa-envelope-o"></i>
                            </span>
                            <span class="text">email</span>
                        </a>
                    </li>
                    <li class="facebook">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(Helper::siteURL()."/?".$_SERVER['QUERY_STRING']); ?>" class="popup">
                            <span class="icon">
                                <i class="fa fa-facebook"></i>
                            </span>
                            <span class="text">facebook</span>
                        </a>
                    </li>
                    <li class="linkedin">
                        <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo urlencode(Helper::siteURL()."/?".$_SERVER['QUERY_STRING']); ?>&amp;title=<?php echo urlencode($config['Title']); ?>&amp;summary=<?php echo urlencode($config['Meta-Description']); ?>" class="popup">
                            <span class="icon">
                                <i class="fa fa-linkedin"></i>
                            </span>
                            <span class="text">linkedin</span>
                        </a>
                    </li>
                    <li class="twitter">
                        <a href="http://twitter.com/home?status=<?php echo urlencode($config['Title']." ".Helper::siteURL()."/?".$_SERVER['QUERY_STRING']); ?>" class="popup">
                            <span class="icon">
                                <i class="fa fa-twitter"></i>
                           </span>
                            <span class="text">twitter</span>
                        </a>
                    </li>
                    <li class="googleplus">
                        <a href="https://plus.google.com/share?url=<?php echo urlencode($config['Title']." ".Helper::siteURL()."/?".$_SERVER['QUERY_STRING']); ?>" class="popup">
                            <span class="icon">
                                <i class="fa fa-google-plus"></i>
                            </span>
                            <span class="text">google+</span>
                        </a>
                    </li>
                    <li class="pinterest">
                        <a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(Helper::siteURL()."/?".$_SERVER['QUERY_STRING']); ?>&amp;media=&amp;description=<?php echo urlencode($config['Title']." ".Helper::siteURL()."/?".$_SERVER['QUERY_STRING']); ?>">
                            <span class="icon">
                                <i class="fa fa-pinterest"></i>
                            </span>
                            <span class="text">pinterest</span>
                        </a>
                    </li>
                    
                </ul>
		</div>
	</div>

	<div class="col-md-4 rightSideAd">
		<?php include(dirname(__FILE__)."/300x600.html"); ?>
		
	</div>
 	<div class="clear-fix"></div>



   
</div>
<footer class="footer">
    &copy; <?php echo date("Y");?> |
    <a href="api.php">API</a> |
    <a href="disclaimers.php">Disclaimers</a> 
</footer>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<script src="Assets/js/bootstrap-select.js"></script>
<script src="Assets/js/script.js"></script>
</body> 
</html>
