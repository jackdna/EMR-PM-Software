<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$error = array();


$sql1 = "ALTER TABLE users 
		ADD updox_sync_status INT(1) NOT NULL, 
		ADD updox_sync_date_time datetime NOT NULL,
		ADD INDEX updox_sync_status(updox_sync_status);";
imw_query($sql1) or $error[] = imw_error();




if(count($error)>0)
{
	$error[] = "<br><br><b>Update 5 Failed!</b>";
	$color = "red";
}
else
{
	$error[] = "<br><br><b>Update 5 Success.</b>";
	$color = "green";	
}



?>

<html>
<head>
<title>Update 5</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$error));?></font>

</body>
</html>