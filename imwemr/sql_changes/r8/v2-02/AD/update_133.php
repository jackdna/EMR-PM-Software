<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();
$sql1="ALTER TABLE `slot_procedures` ADD `source` VARCHAR( 20 ) NOT NULL COMMENT 'null means this is entered from interface',
ADD `source_uid` VARCHAR( 50 ) NOT NULL";
imw_query($sql1) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 133 run FAILED!</b><br>';
    $color = "red";
}
else
{
	imw_query("update slot_procedures set source='HL7' where user_group=''");
	$affected_rows=imw_affected_rows();
    $msg_info[] = "<br><br><b>Update 133  run successfully!<br/>New cloumn added and $affected_rows records updated</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 133 - Appt Procedure/Reason source field added</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>