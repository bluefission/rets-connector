<?php
ini_set('display_errors', 1);
set_time_limit(30000);
ini_set('memory_limit', '4048M');

function exceptionErrorHandler($errNumber, $errStr, $errFile, $errLine ) {
    throw new ErrorException($errStr, 0, $errNumber, $errFile, $errLine);
}
// set_error_handler('exceptionErrorHandler');

date_default_timezone_set('America/New_York');
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
// error_reporting(0);
// require_once('../../../../../turborets/includes/phrets.class.php');
$autoloader = require 'vendor/autoload.php';

require_once("RetsConnector.php");

$connector = new RetsConnector();

$connector->connect($url, $username, $password);
$listings = $connector->properties();

var_dump($listings);