<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$error = array();

$sql2 = "ALTER TABLE `real_time_medicare_eligibility` ADD `ins_data_type` VARCHAR( 20 ) NOT NULL COMMENT 'PRIMARY or SECONDARY or TERTIARY' AFTER `ins_data_id`";
imw_query($sql2) or $error[] = imw_error();

if(count($error)>0)
{
	$error[] = "<br><br><b>Update 26 Failed!</b>";
	$color = "red";
}
else
{
	$error[] = "<br><br><b>Update 26 Success.</b>";
	$color = "green";	
}
?>

<html>
<head>
<title>Update 26</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<h3>Adding column for PRIMARY/SECONDARY for RTE request.</h3>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$error));?></font>

</body>
</html>