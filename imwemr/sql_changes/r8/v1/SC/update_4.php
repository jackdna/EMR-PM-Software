<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$qry = "SHOW INDEX FROM schedule_appointments WHERE key_name = 'sa_multiplecol'";
$res=imw_query($qry) or $msg_info[] = imw_error();
if(imw_num_rows($res)==0){
$qry = "CREATE INDEX sa_multiplecol ON schedule_appointments(sa_facility_id,sa_doctor_id,sa_test_id,sa_patient_app_status_id,sa_patient_app_show,sa_app_start_date,sa_app_end_date)";
imw_query($qry) or $msg_info[] = imw_error();
}


if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 4 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 4 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 4</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>