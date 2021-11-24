<?php 
$ignoreAuth = true;
$skip_file="skipthisfile";
/*Set Practice Name - for dynamically including config file*/
if($argv[1]){
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
}
$cron_job="yes";
$skip_file_process="yes";
include(realpath(dirname(__FILE__)."/../../../../config/globals.php"));

//CHECK STORED PROCEDURES. IF EXIST THEN CALL SP RATHER THAN PHP SCRIPT.
$rs=imw_query("SELECT * FROM INFORMATION_SCHEMA.ROUTINES WHERE 
ROUTINE_TYPE='PROCEDURE' AND ROUTINE_SCHEMA='".IMEDIC_IDOC."' AND ROUTINE_NAME='spi_report_closed_day'");
if(imw_num_rows($rs)>0){
	ob_end_flush();
	ob_implicit_flush(true);
	ob_start();

	echo 'Stored Procedures execution is in progress.<br><br>Please wait until it finished inserting recent accounting data.';
	ob_flush();

	$rs1=imw_query("CALL spi_report_closed_day(@StatusCode,@Message);");

	if(!$rs1){
		echo '<br><br><strong>Some issue found in stored procedures.</strong>';
	}else{
		echo '<br><br><strong>Task completed.</strong>';
	}

	ob_end_flush();

}else{
	include(dirname(__FILE__)."/../../../../interface/reports/report_closed_day.php");
}
?>
