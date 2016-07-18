<?php

// if date is not set, fix it
if (!ini_get('date.timezone')){
    date_default_timezone_set('America/New_York');
}
$config = include dirname(__FILE__).'/config.php';
include dirname(__FILE__).'/classes/Helper.php';
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
<meta charset="utf-8">
<title><?php echo $config['Title']; ?> Disclaimers</title>
<meta name="description" content="<?php echo $config['Meta-Description']; ?>">
<meta name="keywords" content="<?php echo $config['Meta-Keywords']; ?>">
<meta property="og:title" content="<?php echo $config['Title']; ?> Disclaimers" />
<meta property="og:type" content="article" />
<meta property="og:url" content="<?php echo Helper::siteURL(); ?>" />
<meta property="og:description" content="Disclaimers <?php echo $config['Meta-Description']; ?>" />

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />
<link href='http://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="Assets/css/bootstrap.min.css">
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

<link rel="stylesheet" href="Assets/css/style.css">
</head>
<body>
<div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            
            <a class="navbar-brand" href="/"><?php echo $config['Title']; ?> </a>
        </div>
        <div class="navbar-collapse collapse">
            
        </div>
    </div>
</div>
<div class="container">
    
    <h2>Disclaimers</h2>

Data is provided by different financial exchanges and may be delayed as specified by our data providers. <?php echo $config['Title']; ?> does not verify any data and disclaims any obligation to do so.
<br />
<?php echo $config['Title']; ?>, its data or content providers.<br />
<br />
You agree not to copy, modify, reformat, download, store, reproduce, reprocess, transmit or redistribute any data or information found herein or use any such data or information in a commercial enterprise without obtaining prior written consent. All data and information is provided “as is” for personal informational purposes only, and is not intended for trading purposes or advice. Please consult your broker or financial representative to verify pricing before executing any trade.
<br />
Exchange rates are provided for informational purposes only, and do not constitute financial advice of any kind. Although every attempt is made to ensure quality, NO guarantees are given whatsoever of accuracy, validity, availability, or fitness for any purpose - please use at your own risk. All usage is subject of your acceptance .
</div>
<footer class="footer">
    &copy; <?php echo date("Y");?> |
    <a href="/">Home</a> |
    <a href="api.php">API</a> |
    <a href="javascript:void(0)">Disclaimers</a> 

</footer>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

</body> 
</html>
