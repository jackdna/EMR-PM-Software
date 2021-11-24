<?php 
/*update to set serial for scheduler buttons as it was in R7*/
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = "select icon_order from `schedule_icon_list_order` where icon_name='Add Appt'";
$sql_sts=imw_query($qry) or $msg_info[] = imw_error();
$sql_dta=imw_fetch_object($sql_sts);
if($sql_dta->icon_order==19){
	$sql[]="update `schedule_icon_list_order` set `icon_order`=1 where `icon_name`='Cancel'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=2 where `icon_name`='Check In'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=3 where `icon_name`='Check Out'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=4 where `icon_name`='Reschedule'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=5 where `icon_name`='Save'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=6 where `icon_name`='Add Appt'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=7 where `icon_name`='Appt. Hx'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=8 where `icon_name`='A&P'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=9 where `icon_name`='CL-Sply'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=10 where `icon_name`='CL-Disp'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=11 where `icon_name`='Confirm'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=12 where `icon_name`='eRx'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=13 where `icon_name`='Facesheet'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=14 where `icon_name`='First Available'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=15 where `icon_name`='Make Appointment'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=16 where `icon_name`='New Patient'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=17 where `icon_name`='PMT'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=18 where `icon_name`='Recall'; ";
	$sql[]="update `schedule_icon_list_order` set `icon_order`=19 where `icon_name`='Super Bill'";
	foreach($sql as $q){
		imw_query($q) or $msg_info[] = imw_error();
	}
}

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 10 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 10 completed successfully.</b>`";
	$color = "green";
}
?>
<html>
<head>
<title>Update 10</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>