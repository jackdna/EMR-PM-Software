<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info = array();

$sql3 = "UPDATE patient_previous_data SET prev_phone_home = REPLACE (prev_phone_home,'-','')
,new_phone_home =REPLACE (new_phone_home,'-','')
, prev_phone_biz =REPLACE (prev_phone_biz,'-','')
, new_phone_biz = REPLACE (new_phone_biz,'-','')
, prev_phone_cell =REPLACE (prev_phone_cell,'-','')
, new_phone_cell =REPLACE (new_phone_cell,'-','');";
imw_query($sql3) or $msg_info[] = imw_error();

$sql3 = "CREATE INDEX patientpreviousdata_prevphonehome ON patient_previous_data(prev_phone_home);";
imw_query($sql3) or $msg_info[] = imw_error();

$sql3 = "CREATE INDEX patientpreviousdata_newphonehome ON patient_previous_data(new_phone_home);";
imw_query($sql3) or $msg_info[] = imw_error();

$sql3 = "CREATE INDEX patientpreviousdata_prevphonebiz ON patient_previous_data(prev_phone_biz);";
imw_query($sql3) or $msg_info[] = imw_error();

$sql3 = "CREATE INDEX patientpreviousdata_newphonebiz ON patient_previous_data(new_phone_biz);";
imw_query($sql3) or $msg_info[] = imw_error();

$sql3 = "CREATE INDEX patientpreviousdata_prevphonecell ON patient_previous_data(prev_phone_cell);";
imw_query($sql3) or $msg_info[] = imw_error();

$sql3 = "CREATE INDEX patientpreviousdata_newphonecell ON patient_previous_data(new_phone_cell);";
imw_query($sql3) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 28 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 28 Success.</b>";
	$color = "green";	
}
?>

<html>
<head>
<title>Update 28</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<h3>Replace hyphen from phone no. columns in patient previous data</h3>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>

</body>
</html>