<?php

// configuration file
return array(

	// Page Title, Also Used in header
	'Title' => "Currency converter",

	// Default Meta Keywords and Description
	"Meta-Description" => "Convert Currency instantly",
	"Meta-Keywords" => "Convert,Currency,instantly",


	"DefaultFromCurrency" => "USD",
	"DefaultToCurrency" => "EUR",

	// System configuration
	
	/**
	 * Available services:
	 * Please choose one 
	 * Google , Yahoo, Opensourceexchangerates
	 */
	"FinanceService" => "Yahoo",

	/**
	 * This is not Required Unless you use Opensourceexchangerates Service
	 */
	"Opensourceexchangerate_Application_ID" => "8adf749ddbea47edbb718c726eead536",

	// Enable Caching
	// boolean: true or false
	"Enable_caching" => true,
	// Cache Directory
	// Please don't forget Trailing slash
	"CacheDIR" => dirname(__FILE__)."/cache/",
	"CacheExpirationTime" => 3600,


	// API Configuration
	// Enable API?
	// boolean: true or false
	"EnableAPI" => true,
	// Num of allowed Request per Minute
	"AllowedNumberOfRequests" => 20,
);
