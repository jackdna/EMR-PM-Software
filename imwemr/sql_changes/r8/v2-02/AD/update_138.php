<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql="CREATE TABLE `imonitor_settings` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `setting_name` VARCHAR(50) NOT NULL, `default_value` VARCHAR(250) NOT NULL, `practice_value` VARCHAR(250) NOT NULL, `last_operator` INT NOT NULL, `update_datetime` DATETIME NOT NULL)";

$res = imw_query($sql);

if($res){
    $def_dilation_timer_val 	= constant('DEFAULT_DILATION_TIMER') ? constant('DEFAULT_DILATION_TIMER') : 15;
	$def_refresh_interval_val 	= constant('DEFAULT_REFRESH_INTERVAL') ? constant('DEFAULT_REFRESH_INTERVAL') : 10000;
	$def_always_refresh_val 	= constant('IMM_ALWAYS_REFRESH') ? constant('IMM_ALWAYS_REFRESH') : 'NO';
	$q = array();
	$q[] = "INSERT INTO imonitor_settings SET id='1', setting_name='show_noshow_patients', default_value='1', practice_value='1', update_datetime='".date('Y-m-d H:i:s')."'";
	$q[] = "INSERT INTO imonitor_settings SET id='2', setting_name='dilation_time', default_value='15', practice_value='".$def_dilation_timer_val."', update_datetime='".date('Y-m-d H:i:s')."'";
	$q[] = "INSERT INTO imonitor_settings SET id='3', setting_name='auto_refresh', default_value='1', practice_value='1', update_datetime='".date('Y-m-d H:i:s')."'";
	$q[] = "INSERT INTO imonitor_settings SET id='4', setting_name='refresh_in_background', default_value='NO', practice_value='".$def_always_refresh_val."', update_datetime='".date('Y-m-d H:i:s')."'";
	$q[] = "INSERT INTO imonitor_settings SET id='5', setting_name='refresh_interval', default_value='10000', practice_value='".$def_refresh_interval_val."', update_datetime='".date('Y-m-d H:i:s')."'";
	foreach($q as $q1){
		$res1 = imw_query($q1);
	}
	
	$msg_info[] = '<br><br><b>Table Created with name "imonitor_settings".</b><br>';
    $color = "green";
}
else
{
    $msg_info[] = "<br><br><b>Table Already created. ".imw_error()."</b>";
    $color = "red";
}
?>
<html>
<head>
<title>Update 138</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>