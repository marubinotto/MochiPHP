<?php
// error_reporting(E_ALL | E_STRICT);  // for debug
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

require_once('mochi/Context.class.php');
require_once('mochi/FrontController.class.php');

$context = new Context($_REQUEST, $_SERVER, dirname(__FILE__));
$controller = new FrontController();
$controller->processRequest($context);
?>
