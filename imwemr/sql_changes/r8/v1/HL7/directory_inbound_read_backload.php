<?php
/*
File: auto_read_inbound.php
Coded in PHP 7
Purpose: Read inbound HL7 from pre-defined folder and post it to Athena Directory. 
Access Type: Direct Access (via windows service)
*/
$ignoreAuth = true;
set_time_limit(0);
if($argv[1]){
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST']= $practicePath;
}

include("../../../../config/globals.php");
//error_reporting(-1);
//ini_set("display_errors",-1);
require_once(dirname(__FILE__)."/../../../../hl7sys/old/HL7Reader/".HL7_READER_VERSION."b.php");
require_once ("Net/HL7/Message.php");
set_time_limit(0);

//define('BACK_LOAD_DIR',dirname(__FILE__)."/../../tmp/backload");

/*-READING INBOUND DIRECTORY (DEFINED IN GLOBALS.PHP)-*/
$read_dir = "/tmp/files";
$write_dir = $webserver_root.'/data/'.PRACTICE_PATH.'/HL7_INBOUND/archive2';

if(is_dir($read_dir) && file_exists($read_dir)){
	$files = glob($read_dir.'/*.HL7');
	//SORTING FILE LIST ASCENDING ACCORDING TO DATE MODIFIED
	//usort($files, create_function('$a,$b', 'return filemtime($b)<filemtime($a);')); 	
}
/*-READING FILES (IF FOUND)-*/
/*
for($i=0;$i<5; $i++){
	echo $files[$i].'<br>';
}
die;
*/
foreach($files as $file){
	if(is_file($file) && file_exists($file)){
		$hl7_msg	= file_get_contents($file);
		if(trim($hl7_data)=='') continue;
	//	$hl7_data = 'MSH|^~\&|'.$hl7_data;
		/*if(stristr($hl7_data,"MSH|^~\\\&|MIK")){
			$hl7_data = str_replace("MSH|^~\\\&|MIK","MSH|^~\\&|MIK",$hl7_data);
		}
		if(stristr($hl7_data,"\034")){
			$hl7_data = str_replace("\034","",$hl7_data);
		}*/

		//CALLING CLASS AND SENDING DATA.
		$attributes = array();
		$attributes['data'] = $hl7_data;
		$objHL7Reader = new HL7Reader();
		$objHL7Reader->router($attributes['data'], 0);
		
		$archiveDir		  = $write_dir;
		if(!is_dir($archiveDir)){
			@mkdir($archiveDir,777,true);
			chmod($archiveDir,755);
		}
		if(is_dir($archiveDir)){
			rename($file,$archiveDir.'/'.basename($file));
		}
	}
	
	die('parsed one file');
}

echo '<br>Once all files checked.';
?>