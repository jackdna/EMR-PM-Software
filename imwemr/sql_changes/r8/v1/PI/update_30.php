<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info = array();

$sql1 = "ALTER TABLE  `ccda_docs` CHANGE  `file_path`  `file_path` VARCHAR( 255 )";
imw_query($sql1) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 30 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 30 Success.</b>";
	$color = "green";	
}
?>

<html>
<head>
<title>Update 30</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<h3>Increase data column length for CCDA docs table used in Docs Tab</h3>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>

</body>
</html>