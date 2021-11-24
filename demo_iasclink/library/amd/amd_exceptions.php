<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019

/*
 * File: amd_exceptions.php
 * Coded in PHP7
 * Purpose: This file contains custom exception class and custom error handler function.
 * Access Type: Include
*/

if( !isset($callFromSC) || !$callFromSC)
	include_once(dirname(__FILE__).'/../../common/conDb.php');

class amdException extends Exception
{
	/*Error Type*/
	protected $type;
	public $date;
	
	public function __construct($type, $message, $code = 0, Exception $previous = null)
	{
		$this->type = $type;
		$this->date = date( 'mdYHis' );
		
		parent::__construct($message, $code, $previous);
	}
	
	public function showMessage()
	{
		$response = array();
		$response['type'] = $this->type;
		$response['message'] = "Error: ".($this->getMessage()).'<br />Reference No.: '.$this->date.'<br />Please Contact Support.';
		return $response;
	}
	
	public function getErrorText()
	{
		return $this->message;
	}
	
	public function getErrorType()
	{
		return $this->type;
	}
}

/* error handler function */
function amdErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }
	try{
		throw new amdException('Error', '['.$errno.'] '.$errstr);
	}
	catch( amdException $e)
	{
		$response = $e->showMessage();
		
		/*Log Error text to file*/
		$errData = $e->date."\n".($e->getErrorText())."\n".$e->getTraceAsString()."\n";
		file_put_contents( dirname(__FILE__).'/data/error.txt', $errData, FILE_APPEND );
		
		if( isset($rethrow) && $rethrow )
			echo json_encode( $response ) . PHP_EOL ;
		else
			throw $e;
	}
	
    /* Don't execute PHP internal error handler */
    return true;
}
