<?php

//to save sent data and received response.
function LogResponse($text)
{	global $port;
	$documentRoot = dirname(__FILE__).'/../../../';
	$documentRoot = realpath($documentRoot);
	
	$osIdentifier = strtolower( substr(PHP_OS, 0, 3) );
	
	/*Log Directory Path - OS Specific*/
	if( $osIdentifier === 'win' )
	{
		$logDirectory = $documentRoot.'/../logs';
	}
	else
	{
		$logDirectory = '/var/log';
	}

	$logDirectory .= DIRECTORY_SEPARATOR.'hl7_sender';
	
	/*Check if Hl7 Log directory Exists - Create if Not*/
	if( !is_dir($logDirectory) )
	{
		mkdir( $logDirectory, 0755, true );
	}
	
	/*Practice Directory Name*/
	$script = trim(PRACTICE_PATH);
	
	if( strpos($script, DIRECTORY_SEPARATOR) !== false )
	{
		$practiceDirectory = substr($script, 0, strpos($script, DIRECTORY_SEPARATOR));
	}
	else
	{
		$practiceDirectory = $script;
	}
	
	$logFile	= strtolower($practiceDirectory).'_'.($port != '' ? $port.'_' : '').date('Y_m_d').'.log';
	$logFilePath= $logDirectory.DIRECTORY_SEPARATOR.$logFile;
	
	$data	= date("Y-m-d H:i:s");
	$data	.= " ".$text.PHP_EOL;
	
	file_put_contents($logFilePath, $data, FILE_APPEND);
}

/*Copied from CLS_makeHL7.php of R6*/
function read_hl7_ACK($hl7msg = '')
{
	$hl7msg = strtoupper($hl7msg);
	$return = array();
	$return['error'] = NULL;
	$return['response'] = NULL;
	if(substr($hl7msg,0,4)=='MSH|'){
		$lines = explode(chr(13),$hl7msg);
		$MSH = explode('|',$lines[0]);
		if(strpos($MSH[8], "^")!=false){
			$ack = explode("^", $MSH[8]);
			if($ack[0]!="ACK")
				$return['error'] = 'NoACK';
		}
		elseif($MSH[8]!="ACK"){
			$return['error'] = 'NoACK';
		}
		$MSA = explode('|',$lines[1]);
		$arr1 = array();
		if($MSA[1]=='AA') {$arr1['status'] = 'Y';}
		else if($MSA[1]=='AE') {$arr1['status'] = 'N';}
		$msa6 = explode('^',$MSA[6]);
		$arr1['status_text'] = $msa6[1];
		$return['response'] = $arr1;
	}else{
		$return['error'] = 'Invalid HL7 message.';
	}
	return $return;
}
