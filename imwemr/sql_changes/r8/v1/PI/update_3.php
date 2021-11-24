<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql1="ALTER TABLE `patient_data` 
	ADD `race_code` VARCHAR(255) NOT NULL, 
	ADD `ethnicity_code` VARCHAR(255) NOT NULL, 
	ADD `sor_txt` VARCHAR(255) NOT NULL COMMENT 'sor - sexual orientation', 
	ADD `other_sor` VARCHAR(255) NOT NULL, 
	ADD `sor_code` VARCHAR(255) NOT NULL,
	ADD `gi_txt` VARCHAR(255) NOT NULL COMMENT 'gi - gender identity', 
	ADD `other_gi` VARCHAR(255) NOT NULL,
	ADD `gi_code` VARCHAR(255) NOT NULL;";
imw_query($sql1) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>PI &gt; Update 3 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>PI &gt; Update 3 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 3 (PI)</title>
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