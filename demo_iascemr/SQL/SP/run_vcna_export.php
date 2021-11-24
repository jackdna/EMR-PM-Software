<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");
include_once("../../common/commonFunctions.php");
include_once("../../admin/classObjectFunction.php");

$sel_date = (isset($_REQUEST["sel_date"]) && $_REQUEST["sel_date"] <> '0000-00-00') ? urldecode($_REQUEST["sel_date"]) : '';
if(!$sel_date) { $sel_date = date("Y-m-d");	 }

$end_date = (isset($_REQUEST["end_date"]) && $_REQUEST["end_date"] <> '0000-00-00') ? urldecode($_REQUEST["end_date"]) : '';
if(!$end_date ) { $end_date = date("Y-m-d");	 }
	
$show_date = date("m-d-Y",strtotime($sel_date)) .' - ' .date("m-d-Y",strtotime($end_date)) ;

$startTimeStamp = strtotime($sel_date);
$endTimeStamp = strtotime($end_date);

if( $startTimeStamp <= $endTimeStamp ) {
	
	include '../../export_vcna_info.php';
	$next_date = date("Y-m-d",strtotime($sel_date .' + 1Days'));
	echo '<script>window.location.href="run_vcna_export.php?sel_date='.urlencode($next_date).'&end_date='.urlencode($end_date).'";</script>";';
}
else 
	echo '<div style="width:100%;padding:15px; background-color:green; color:white; font-size:18px; font-weight:bold; text-align:center;">Can\'t run export for date greater than '.date("m-d-Y",strtotime($end_date)).'</div>';
die;