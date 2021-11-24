<?php 
$ignoreAuth = true;
$skip_file="skipthisfile";
/*Set Practice Name - for dynamically including config file*/
if($argv[1]){
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
}
$cron_job="yes";
include(dirname(__FILE__)."/../interface/fd_closed_day.php");
?>