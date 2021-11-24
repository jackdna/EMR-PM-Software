<?php
/*
File: auto_read_inbound.php
Purpose: Read inbound HL7 from pre-defined folder and post it to Athena Directory. 
Access Type: Direct Access (via windows service)
*/

$ignoreAuth = true;

/*Set Practice Name - for dynamically including config file*/
$MessageType = false;
if($argv[1]){
	$practicePath = trim($argv[1]);
	if( isset($argv[2]) && trim($argv[2]) != ''){
		$MessageType = strtoupper(trim($argv[2]));
	}
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST']= $practicePath;
}

require_once(dirname(__FILE__)."/../../config/globals.php");

$outbound_dir = '';
if(constant('OUTBOUND_HL7_DIR')){$outbound_dir = constant('OUTBOUND_HL7_DIR');}
if($outbound_dir == '') {$outbound_dir = false;}

if(!$outbound_dir) die('OUTBOUND DIR not defined.');

$q2 = "SELECT * 
		FROM `hl7_sent` 
		WHERE `msg_type` IN ('DFT','Detailed Financial Transaction') 
		AND sent='0' 
		ORDER BY id";
$hl_res = imw_query($q2);
if($hl_res && imw_num_rows($hl_res)>0){
	while($hl_rs 	= imw_fetch_assoc($hl_res)){
		$hl7_data 	= $hl_rs['msg'];
		$hl7_id		= $hl_rs['id'];
		if(trim($hl7_data)=='') continue;
		
		$hl7_file_name = $hl7_id.'.txt';
		if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('okei','keywhitman','mage','marr','gnysx','geas','tysoneye'))){
			$hl7_file_name = 'IDOC_DFT_'.$hl7_id.'.txt';
		}else if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('antigua'))){
			$hl7_file_name = 'IDOC_DFT_'.$hl7_id.'.hl7';
		}else if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('michiganeye', 'monongalia'))){
			$hl7_file_name = 'IDOC_DFT_'.$hl7_id.'.HL7';
		}
		
		
		$fp		= fopen($outbound_dir."/".$hl7_file_name,'a+');
		$fw		= fwrite($fp,$hl7_data);
		fclose($fp);
		if($fw){echo 'File written for msg id: '.$hl7_id.'<br>';} MarkHL7Filed($hl7_id);
	}
}


function MarkHL7Filed($msgId){
	$q = "UPDATE hl7_sent SET sent = 1, sent_on = '".date('Y-m-d H:i:s')."', status_text='Sent to OUTBOUND_HL7_DIR' WHERE id = '".$msgId."'";
	$res = imw_query($q);
}
?>