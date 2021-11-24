<?php
require_once('../../../../config/globals.php');

$time_mor_from_AP = $_REQUEST['fromAP'];
$time_mor_from_hour = $_REQUEST['fromHr'];
$time_mor_from_mins = $_REQUEST['fromMn'];
$time_mor_to_AP = $_REQUEST['toAP'];
$time_mor_to_hour = $_REQUEST['toHr'];
$time_mor_to_mins = $_REQUEST['toMn'];
$temp_id = $_REQUEST['temp_id'];

if($time_mor_from_AP == "PM"){
    if($time_mor_from_hour < 12){
        $time_mor_from_hour += 12;
    }
}    
if($time_mor_from_AP == "AM"){
    if($time_mor_from_hour == 12){
        $time_mor_from_hour = "00";
    }
}

$time_mor_from_hour = (strlen($time_mor_from_hour) == 1) ? "0".$time_mor_from_hour : $time_mor_from_hour;

if($time_mor_to_AP == "PM"){
    if($time_mor_to_hour < 12){
        $time_mor_to_hour += 12;
    }
}    
if($time_mor_to_AP == "AM"){
    if($time_mor_to_hour == 12){
        $time_mor_to_hour = "00";
    }
}

$time_mor_to_hour = (strlen($time_mor_to_hour) == 1) ? "0".$time_mor_to_hour : $time_mor_to_hour;

$start_time = trim($time_mor_from_hour).':'.trim($time_mor_from_mins).':00';
$end_time = trim($time_mor_to_hour).':'.trim($time_mor_to_mins).':00';

$qry = "SELECT id FROM schedule_templates WHERE id=".$temp_id." and ((('".$start_time."' between SUBTIME(fldLunchStTm,'00:10:00') and fldLunchEdTm) or ('".$end_time."' between SUBTIME(fldLunchStTm,'00:10:00') and fldLunchEdTm) or ('".$start_time."' < fldLunchStTm and '".$end_time."' > fldLunchEdTm)) or (fldLunchStTm='00:00:00' and fldLunchEdTm='00:00:00') )";
$result_obj = imw_query($qry);
$result_rows = imw_num_rows($result_obj);
if($result_rows == 0)
{
	echo 'no';	
}
else
{
	echo 'yes';	
}
?>