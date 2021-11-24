<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$sql1 = "ALTER TABLE `social_history` 
ADD `use_of_alcohol` TINYINT NOT NULL DEFAULT '0', 
ADD `use_of_drugs` TINYINT NOT NULL DEFAULT '0'";
imw_query($sql1) or $error[] = imw_error();


if(count($error)>0)
{
	$error[] = "<br><br><b>Update 1 Failed!</b>";
	$color = "red";
}
else
{
	$error[] = "<br><br><b>Update 1 Success.</b>";
	$color = "green";	
}

?>

<html>
<head>
<title>Update 1</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$error));?></font>

</body>
</html>