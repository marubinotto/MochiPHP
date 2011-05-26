<?php
// error_reporting(E_ALL | E_STRICT);  // for debug
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

$webrootDir = dirname(__FILE__);

ini_set('default_charset', 'UTF-8');
ini_set('mbstring.internal_encoding', 'UTF-8');	// default encoding for mb_ functions

ini_set('include_path',
	ini_get('include_path') 
		. PATH_SEPARATOR . $webrootDir
		. PATH_SEPARATOR . $webrootDir . '/internals/libs'
);

require_once('mochi/Context.class.php');
require_once('mochi/FrontController.class.php');

$context = new Context($_REQUEST, $_SERVER, $webrootDir);
$controller = new FrontController();
$controller->processRequest($context);
?>
