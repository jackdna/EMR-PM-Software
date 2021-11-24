<?php
$ignoreAuth = true;
include("../../../../config/globals.php"); 
$date_time=date("Y-m-d H:i:s");

imw_query("update patient_charge_list set report_date_timestamp=''");

imw_query("update patient_charge_list set report_date_timestamp='$date_time' where charge_list_id in (select charge_list_id from patient_charge_list_details where report_date_timestamp!='0000-00-00 00:00:00')") or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>Update 3 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>Update 3 Success.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Release 8 Updates 3 (RP)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>
