<?php
/**
 * Usage: set_error_handler(array('PhpErrorException', 'throwException'));
 * 
 * The following error types cannot be handled with a user defined function: 
 * E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, 
 * E_COMPILE_WARNING, and most of E_STRICT raised in the file where set_error_handler() is called.
 * http://php.net/manual/ja/function.set-error-handler.php
 * 
 * ErrorException (PHP 5 >= 5.1.0)
 * http://www.php.net/manual/ja/class.errorexception.php
 * 
 * Fatal errors cannot be trapped in userland because the engine may not be in a stable state.
 * http://marc.info/?l=php-internals&m=97673386418430&w=2
 * 
 * Basically, even an E_NOTICE will halt your entire application 
 * if your error_reporting settings do not suppress them.
 * I often have third party libraries involved, and then if they don't use exceptions 
 * you end up with 3-4 different approaches to the problem! Zend uses exceptions. 
 * CakePHP uses a custom error handler, and most PEAR libraries use the PEAR::Error object. 
 * http://stackoverflow.com/questions/60607/in-php5-should-i-use-exceptions-or-trigger-error-set-error-handler
 * 
 * PHP Trick: Catching fatal errors (E_ERROR) with a custom error handler:
 * http://insomanic.me.uk/post/229851073/php-trick-catching-fatal-errors-e-error-with-a
 * 
 * PHP の「エラー処理ハンドラ」「シャットダウンハンドラ」「例外処理ハンドラ」の挙動:
 * http://keicode.com/cgi/php-error-handling.php
 * 
 * PHPのset_erorr_handlerとregister_shutdown_functionとob関数について ( エラーを整形出力したい ):
 * http://havelog.ayumusato.com/develop/php/e191-php-error-handling.html
 */
class PhpErrorException extends Exception
{
	protected static $ERRNOS = array(
		E_ERROR	 => "Error",
		E_WARNING => "Warning",
		E_PARSE => "Parse Error",
		E_NOTICE => "Notice",
		E_CORE_ERROR => "Core Error",
		E_CORE_WARNING => "Core Warning",
		E_COMPILE_ERROR => "Compile Error",
		E_COMPILE_WARNING => "Compile Warning",
		E_USER_ERROR => "User Error",
		E_USER_WARNING => "User Warning",
		E_USER_NOTICE => "User Notice",
		E_STRICT => "Runtime Notice"
	);
	
	static function throwException($errno, $errstr, $errfile, $errline) {
		$message = '';
		if (isset(self::$ERRNOS[$errno])) {
			$message .= (self::$ERRNOS[$errno] . ': ');
		}
		$message .= $errstr;
		
		$exception = new PhpErrorException($message, $errno);
		$exception->file = $errfile;
		$exception->line = $errline;
		throw $exception;
	}
}
?>
