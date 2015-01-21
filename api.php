<?php

// if date is not set, fix it
if (!ini_get('date.timezone')){
    date_default_timezone_set('America/New_York');
}

//Load configuration, currency array, icons and all classes
$config = include dirname(__FILE__).'/config.php';

if(!$config['EnableAPI']):
	header("Status: 404 Not Found");
	header('HTTP/1.0 404 Not Found');
	exit("Sorry, API is not available");
endif;
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
include dirname(__FILE__).'/classes/APILimiter.php';
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
$data = $amount = $from = $to = $format = false;

// Rate is requested
if(isset($_GET['amount']) and is_numeric($_GET['amount']) and
	isset($_GET['from']) and strlen($_GET['from']) == 3 and in_array(strtoupper($_GET['from']), $currencyCodes) and
	isset($_GET['to']) and strlen($_GET['to']) == 3 and in_array(strtoupper($_GET['from']), $currencyCodes)):
	$amount = $_GET['amount'];
	$from = strtoupper($_GET['from']);
	$to = strtoupper($_GET['to']);
	$rate = $service->getRate($amount, $from, $to);
	$data = array(
		"From" => $from,
		"To" => $to,
		"Amount" => $amount,
		"Result" => (string)$rate
	);
endif;


if(isset($_GET['format']) and in_array($_GET['format'], array("json", "xml")) and isset($data) and !empty($data)):
	new APILimiter($config['AllowedNumberOfRequests']);
	switch($_GET['format']):
		case "json":
			header('Content-Type: application/json');
			echo json_encode($data);
			exit();
		break;
		case "xml":
			header('Content-Type: application/xml; charset=utf-8');
			$xml = new SimpleXMLElement('<root/>');
			$data = array_flip($data);
			array_walk($data, array ($xml, 'addChild'));
			echo $xml->asXML();
			exit();
		break;
	endswitch;
endif;

// Print Documentation 
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
<meta charset="utf-8">
<title><?php echo $config['Title']; ?></title>
<meta name="description" content="api <?php echo $config['Meta-Description']; ?>">
<meta name="keywords" content="api <?php echo $config['Meta-Keywords']; ?>">
<meta property="og:title" content="<?php echo $config['Title']; ?> API" />
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
<style>
.highlight {
padding: 9px 14px;
margin-bottom: 14px;
background-color: #f7f7f9;
border: 1px solid #e1e1e8;
border-radius: 4px;
}
</style>
</head>
<body>
<div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            
            <a class="navbar-brand" href="/"><?php echo $config['Title']; ?> API</a>
        </div>
        <div class="navbar-collapse collapse">
            
        </div>
    </div>
</div>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-info" role="alert">Please note, There is a <?php echo $config['AllowedNumberOfRequests'];?> request limit per minute.</div>
		</div>
		<div class="col-md-6"><strong>Required Parameters:</strong><br />
			<strong>format</strong> = json | xml <br />
			<strong>amount</strong> = numeric value <br />
			<strong>from</strong> = currency code <br />
			<strong>to</strong> = currency code <br />
			<strong>Exmple URL:</strong> <a href="<?php echo Helper::siteURL(); ?>/api.php?format=json&amount=100&from=usd&to=eur"; target="_blank"><?php echo Helper::siteURL(); ?>/api.php?format=json&amount=100&from=usd&to=eur</a>
		</div>
		<div class="col-md-6">
			<div class="highlight"><?php echo Helper::siteURL(); ?>/api.php
			<br />?format=###
			<br />&amount=###
			<br />&from=###
			<br />&to=###
			</div>
		</div>
	</div>
	<div class="col-md-12">
		Expected output:
		<div class="highlight">
			{<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#000">From:</span> <span style="color:green">"USD"</span>,<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#000">To:</span> <span style="color:green">"EUR"</span>,<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#000">Amount:</span> <span style="color:green">"100"</span>,<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#000">Result:</span> <span style="color:green">"74.452"</span><br />
			}<br />
		</div>
	</div>
</div>
<footer class="footer">
    &copy; <?php echo date("Y");?> |
    <a href="/">Home</a> |
    <a href="javascript:void(0);">API</a> |
    <a href="disclaimers.php">Disclaimers</a> 

</footer>
</body>
</html>
