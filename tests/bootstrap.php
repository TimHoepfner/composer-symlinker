<?php
error_reporting(-1);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

/**
 * Path trickery ensures test suite will always run, standalone or within
 * another composer package. Designed to find composer autoloader and require
 */
$vendorPos = strpos(__DIR__, 'vendor/piwi/composer-symlinker');
if($vendorPos !== false) {
	// Package has been cloned within another composer package, resolve path to autoloader
	$vendorDir = substr(__DIR__, 0, $vendorPos) . 'vendor/';
	$loader = require $vendorDir . 'autoload.php';
} else {
	// Package itself (cloned standalone)
	$loader = require __DIR__.'/../vendor/autoload.php';
}
