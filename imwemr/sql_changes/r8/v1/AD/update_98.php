<?php 

$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();
$sql="ALTER TABLE  `direct_messages` ADD  `org_sender_id` INT NOT NULL COMMENT  'stores logged in user id'";
imw_query($sql) or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 98  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 98  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 98</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>