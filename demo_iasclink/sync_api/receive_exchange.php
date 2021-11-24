<?php
error_reporting(0);
include __DIR__ . "/sync_api_global.php";
require_once __DIR__ . '/../library/vendor_api/autoload.php';
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPIOException;

include __DIR__ . "/../common/conDb.php";

if(!trim($_SERVER['DOCUMENT_ROOT'])){
	$_SERVER['DOCUMENT_ROOT'] = "/var/www/html";	
}
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
$pdf_dir = $rootServerPath."/".$iolinkDirectoryName."/admin/pdfFiles";	
if(!is_dir($pdf_dir."/sync_api_log")){
	mkdir($pdf_dir."/sync_api_log", 0775);
}
if(!is_dir($pdf_dir."/sync_api_log/error_log")){
	mkdir($pdf_dir."/sync_api_log/error_log", 0775);
}
$error_log_dir = $pdf_dir."/sync_api_log/error_log";

$callback = function ($message)
{
	global $pdf_dir, $error_log_dir;
	$dt_frmt 		= date("Y_m_d");
	$dtm_frmt 		= date('Y_m_d_H_i_s');
	$logFolderPath 	= $pdf_dir."/sync_api_log/date_".$dt_frmt;
	if(!is_dir($logFolderPath)){
		mkdir($logFolderPath, 0775);
	}

	
	$getData = $message->body;
	//echo ' [x] Received ', $getData, "\n";
	$dataArr = json_decode(stripslashes($getData));
	if($dataArr->api_data)
	{
		file_put_contents($logFolderPath.'/api_data_receive_'.$dtm_frmt.'.txt', $getData);
		$dataArrNew = $dataArr->api_data;
		include __DIR__ . '/pdf_endpoint.php';
	}

	if($dataArr->api_allergy_data)
	{
		file_put_contents($logFolderPath.'/api_allergy_data_receive_'.$dtm_frmt.'.txt', $getData);
		$dataArrNew = $dataArr->api_allergy_data;
		include __DIR__ . '/allergies_endpoint.php';
	}

	if($dataArr->api_med_data)
	{
		$logFolderPath.'/api_med_data_receive_'.$dtm_frmt.'.txt'."\n";
		file_put_contents($logFolderPath.'/api_med_data_receive_'.$dtm_frmt.'.txt', $getData);
		$dataArrNew = $dataArr->api_med_data;
		include __DIR__ . '/medications_endpoint.php';
	}

	if($dataArr->api_data_opnote)
	{
		file_put_contents($logFolderPath.'/api_data_opnote_receive_'.$dtm_frmt.'.txt', $getData);
		$dataArrNew = $dataArr->api_data_opnote;
		include __DIR__ . '/opnote_endpoint.php';
	}

	if($dataArr->api_charges_data)
	{
		file_put_contents($logFolderPath.'/api_charges_data_receive_'.$dtm_frmt.'.txt', $getData);
		$dataArrNew = $dataArr->api_charges_data;
		include __DIR__ . '/charges_endpoint.php';
	}

	/** Send Acknowledgement back */
	$messageId = $message->delivery_info['delivery_tag'];
	$message->delivery_info['channel']->basic_ack(
		$messageId
	);
};

do
{
	$isConnected = false;
	try
	{
		$connection = new AMQPSSLConnection($inteHost, $intePort, $inteUser, $intePass, "/", $inteSSLOptions); //[host, port, user, pass]
		$channel = $connection->channel();
		
		/** Declare the queue if it does not exists */
		$channel->queue_declare($inboundQueue, false, $inteDurable, false, false);
		
		echo " [*] Waiting for messages. To exit press CTRL+C\n";
		$isConnected  =true;
		//break(1);
		$channel->basic_qos(null, 1, null);
		$channel->basic_consume($inboundQueue, '', false, false, false, false, $callback);
		
		try
		{
			while( $channel->callbacks )
			{
				$channel->wait();
			}
	
		}
		catch(Exception $exception)
		{
			$isConnected  =false;
			$error = date("Y-m-d H:i:s").' Error3 '.$exception->getMessage();
			echo $error = "\n".trim($error);
			file_put_contents($error_log_dir."/error_".date("Y_m_d").".txt", $error, FILE_APPEND);
			file_put_contents($error_log_dir."/error_".date("Y_m_d").".txt", "\n============================\n", FILE_APPEND);
		}

	}
	catch( AMQPIOException $exception )
	{
		$error = date("Y-m-d H:i:s").' Error1 '.$exception->getMessage();
		echo $error = "\n".trim($error);
		file_put_contents($error_log_dir."/error_".date("Y_m_d").".txt", $error, FILE_APPEND);
		file_put_contents($error_log_dir."/error_".date("Y_m_d").".txt", "\n============================\n", FILE_APPEND);
	}
	catch( Exception $exception )
	{
		$error = date("Y-m-d H:i:s").' Error2 '.$exception->getMessage();
		echo $error = "\n".trim($error);
		file_put_contents($error_log_dir."/error_".date("Y_m_d").".txt", $error, FILE_APPEND);
		file_put_contents($error_log_dir."/error_".date("Y_m_d").".txt", "\n============================\n", FILE_APPEND);
		
	}
	sleep(5);
	
}while( $isConnected === false );


$channel->close();
$connection->close();

