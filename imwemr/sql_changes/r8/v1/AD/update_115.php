<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();
$trans_date_time = date('Y-m-d H:i:s');
$sql="update icd10_data set deleted='1',del_operator_id='1',del_date_time='$trans_date_time' where icd10 in ('H50.151') and deleted='0'";

imw_query($sql) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 115 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 115 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 115</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>