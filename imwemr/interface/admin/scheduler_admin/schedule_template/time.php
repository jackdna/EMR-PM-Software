<?php
/*
File: time.php
Purpose: get schedule template
Access Type: Direct
*/
require_once('../../../../config/globals.php');

$sch_tmp_id = $_GET["sch_tmp_id"];
$returnString="";
if($sch_tmp_id<>""){
    $qry = "select * from schedule_templates where id = '$sch_tmp_id'";
    $qryRes = imw_fetch_array(imw_query($qry));
    $mor_start_time = $qryRes['morning_start_time'];
    $mor_end_time = $qryRes['morning_end_time'];    
    $schedule_name1 = $qryRes['schedule_name'];
    $date_status = $qryRes['date_status'];
    $returnString=$mor_start_time."---".$mor_end_time."---".$date_status;
}    
print $returnString;
?>