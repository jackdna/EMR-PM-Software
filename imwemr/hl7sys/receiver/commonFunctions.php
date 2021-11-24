<?php

//to save sent data and received response.
function LogResponse($text)
{
	if( trim($text) == '' )
	{
		return true;
	}

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

	$logDirectory .= DIRECTORY_SEPARATOR.'hl7_receiver';
	
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
	
	$logFile	= strtolower($practiceDirectory).'_'.($GLOBALS['port'] != '' ? $GLOBALS['port'].'_' : '').date('Y_m_d').'.log';
	$logFilePath= $logDirectory.DIRECTORY_SEPARATOR.$logFile;
	
	$data	= date("Y-m-d H:i:s");
	$data	.= " ".trim($text).PHP_EOL;
	
	file_put_contents($logFilePath, $data, FILE_APPEND);
}

/*Copied from R6*/
function do_post_request($data,$for='')
{
	$url  = constant('URL4POST');
	$data = str_replace(array(chr(11),chr(28),'"'),'',$data);
	if($for=='zeiss'){
		$hl7_config_array 	= $GLOBALS['HL7_RECEIVER_ZEISS'];
		$url				= $hl7_config_array['URL4POST'];
	}else{
		if($GLOBALS["LOCAL_SERVER"]!='cec_bsc'){
			$data = base64_encode($data);
		}
	}
	$data = urlencode($data);
	
	$myvars = 'data=' . $data;
	$ch = curl_init( $url ); 
	curl_setopt( $ch, CURLOPT_POST, 1);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt( $ch, CURLOPT_HEADER, 0);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
	if(substr(constant('URL4POST'),0,5)=='https'){
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	}
	$response = curl_exec( $ch );
	if(curl_errno($ch)){
		$response.= 'ERROR: '.curl_error($ch);
	}
	curl_close($ch);
	
/**************Log time of last message received - Only for Unix systems**************/
	$osIdentifier = strtolower( substr(PHP_OS, 0, 3) );
	if( $osIdentifier !== 'win' )
	{
		$flagPath = data_path();
		$flagPath = realPath( $flagPath );
		$flagPath .= DIRECTORY_SEPARATOR.'hl7Flags';
		/*Create Directory if not exists*/
		if( !is_dir($flagPath) )
		{
			mkdir( $flagPath, 0755, true );
			chown( $flagPath, 'apache' );
		}
		
		$receivedTime = exec('date +%s');
		
		$fileName = $flagPath.DIRECTORY_SEPARATOR.'lastReceivedTime.log';
		file_put_contents($fileName, $receivedTime);
	}
/**************End Log time of last message received**************/
	return $response;
}

function writeOnPaper($text){
	if( constant('WRITE_DEBUGGING_LOG') && trim($text)!= '' ){
		$documentRoot = dirname(__FILE__).'/../../../';
		$documentRoot = realpath($documentRoot);
		$osIdentifier = strtolower( substr(PHP_OS, 0, 3) );

		/*Log Directory Path - OS Specific*/
		if( $osIdentifier === 'win' ){
			$logDirectory = $documentRoot.'/../logs';
		}else{
			$logDirectory = '/var/log';
		}

		$logDirectory .= DIRECTORY_SEPARATOR.'hl7_receiver';

		/*Check if Hl7 Log directory Exists - Create if Not*/
		if(!is_dir($logDirectory)){
			mkdir( $logDirectory, 0755, true );
		}

		/*Practice Directory Name*/
		$script = str_replace($documentRoot, '', dirname(__FILE__));
		$script = trim($script, DIRECTORY_SEPARATOR);

		if( strpos($script, DIRECTORY_SEPARATOR) !== false ){
			$practiceDirectory = substr($script, 0, strpos($script, DIRECTORY_SEPARATOR));
		}else{
			$practiceDirectory = $script;
		}

		$logFile	= strtolower($practiceDirectory).'_'.($port != '' ? $port.'_' : '').date('Y_m_d').'.log';
		$logFilePath= $logDirectory.DIRECTORY_SEPARATOR.$logFile;

		$data	= PHP_EOL.date("Y-m-d H:i:s");
		$data	.= " ".$text;

		file_put_contents($logFilePath, $data, FILE_APPEND);
	}
}