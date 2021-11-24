<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql="
CREATE TABLE `cl_comments` ( `id` int(11) NOT NULL AUTO_INCREMENT, `cl_sheet_id` int(11) DEFAULT NULL, `comment` text, `created_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `created_by` int(11) DEFAULT NULL, `delete_status` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`id`) ) 
";
imw_query($sql) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 125  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 125  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 125- Add contact lens comments</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>