<?php
include_once("../../../../config/globals.php");
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');

set_time_limit(180);
//scheduler object
$obj_scheduler = new appt_scheduler();

$sel_date_val = "";
if($_REQUEST["sel_date_val"] == ""){
	$sel_date_val = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d"), date("Y")));
	//$sel_date_val = "2011-03-11";
}else if($_REQUEST["sel_date_val"] != ""){
	$sel_date_val = $_REQUEST["sel_date_val"];
}
if(defined('SCHEDULER_CACHE_RANGE') && constant('SCHEDULER_CACHE_RANGE')>0)$cache_days=constant('SCHEDULER_CACHE_RANGE');
else $cache_days=365;
$LAST_date = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") +$cache_days, date("Y")));

if(strtotime($LAST_date) > strtotime($sel_date_val)){
	
	if($sel_date_val != ""){
		$arr_prov = $obj_scheduler->load_providers();
		$arr_sel_prov = array();
		for($f = 0; $f < count($arr_prov); $f++){
			$arr_sel_prov[] = $arr_prov[$f]["id"];
		}
		$obj_scheduler->cache_prov_working_hrs($sel_date_val, $arr_sel_prov, $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common", true);
	}

	list($y, $m, $d) = explode('-', $sel_date_val);
	$send_date_val = date('Y-m-d', mktime(0, 0, 0, $m, $d+1, $y));
	$show_date_val = date('Y-m-d', mktime(0, 0, 0, $m, $d+1, $y));
	print($send_date_val.'~'.get_date_format($show_date_val));
}else{
	print("Completed");
}
?>