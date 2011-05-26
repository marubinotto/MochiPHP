<?php
// error_reporting(E_ALL | E_STRICT);  // for debug
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

define('WEBROOT_DIR', dirname(__FILE__));

ini_set('include_path',
	ini_get('include_path') 
		. PATH_SEPARATOR . WEBROOT_DIR
		. PATH_SEPARATOR . WEBROOT_DIR . '/internals/libs'
);

require_once('mochi/Context.class.php');
require_once('mochi/FrontController.class.php');

$context = new Context($_REQUEST, $_SERVER, WEBROOT_DIR);
$controller = new FrontController();
$controller->processRequest($context);
?>
