<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql="UPDATE custom_reports SET template_name = 'Cash Lag Analysis' WHERE report_sub_type ='analytics' AND template_name='Cash Lag Analyses';";
$row=sqlQuery($sql);

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 152  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 152  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 152 -Report name corrected</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>
