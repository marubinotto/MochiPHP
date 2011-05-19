<?php
require_once('TextField.class.php');

class PasswordField extends TextField
{
	function __construct($name, array $attributes = NULL) {
		parent::__construct($name, $attributes);
	}
	
	function getType() {
		return 'password';
	}
}
?>
