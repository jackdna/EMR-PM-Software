<?php
/*
File: auto_read_inbound.php
Coded in PHP 7
Purpose: Read inbound HL7 from from table. 
Access Type: Direct Access 
*/
$ignoreAuth = true;
include("../../../../config/globals.php");
require_once(dirname(__FILE__)."/../../../../hl7sys/old/HL7Reader/".HL7_READER_VERSION.".php");
require_once ("Net/HL7/Message.php");
set_time_limit(0);	
$file_name = 'tysoneye_reprocess_log.txt';
$hl7_start_id = 1;
if(file_exists($file_name) && is_file($file_name)){
	$hl7_start_id = file_get_contents($file_name);
}

$q2 	= "SELECT id, msg FROM `hl7_received` WHERE id > '".$hl7_start_id."' ORDER BY id LIMIT 0,50000";
$hl_res	= imw_query($q2);
if($hl_res && imw_num_rows($hl_res)>0){
	while($hl_rs = imw_fetch_assoc($hl_res)){
		$hl7_data 	= stripslashes($hl_rs['msg']);
		//var_dump(stristr($hl7_data,'MSH|'));
		if(!stristr($hl7_data,'MSH|')) $hl7_data = base64_decode($hl7_data);
	//	continue;
		if(!stristr($hl7_data,'|SIU^S15|')) continue;
		$hl7_id 	= $hl_rs['id'];
		//echo $hl7_id.',';
		if(trim($hl7_data)==''){continue;}
		if($objHL7Reader) unset ($objHL7Reader);
		if($attributes) unset ($attributes);
			
		//CALLING CLASS AND SENDING DATA.
		$attributes = array();
		$attributes['data'] = $hl7_data;
		$attributes['uname'] = constant('USERNAME');
		$attributes['upass'] = constant('PASSWORD');
		$objHL7Reader = new HL7Reader();
		$objHL7Reader->router($attributes['data'], 0,false);
		$objHL7Reader->send_response($hl7_id);
		file_put_contents($file_name,$hl7_id);
	}
}

echo '<hr><br>Once all records checked.';

?>