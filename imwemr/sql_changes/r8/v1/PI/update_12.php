<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
 
$q = "CREATE TABLE `uniptidentify` (
	 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`ptId` INT NOT NULL,
	`uniqueTag` VARCHAR(250) NOT NULL,
	`fname` VARCHAR(250) NOT NULL,
	`lname` VARCHAR(250) NOT NULL,
	`delStatus` TINYINT NOT NULL
	) ENGINE = MYISAM";
$r = imw_query($q) or $msg_info[]=imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = '<br><br><b>Update 12  run FAILED!</b><br>'.imw_error();
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 12 run successfully!</b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 12 (PI)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<h3></h3>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>