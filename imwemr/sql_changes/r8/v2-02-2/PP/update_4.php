<?php
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql2= "CREATE TABLE `iportal_communications` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`erp_id` INT NOT NULL ,
	`imw_appt_id` INT NOT NULL ,
	`update_status_to` VARCHAR( 100 ) NOT NULL ,
	`method` VARCHAR( 20 ) NOT NULL ,
	`date_time` DATETIME NOT NULL ,
	`operator_id` INT NOT NULL ,
	`updated_in_schedule` TINYINT NOT NULL ,
	PRIMARY KEY ( `id` )
	) ENGINE = MYISAM ;
	";
imw_query($sql2) or $msg_info[] = imw_error();


//END CODE to ADD RECORD FOR Medical Hx > General Health > dropdown list of family relation

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 4 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 4 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 4</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>
