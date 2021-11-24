<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$q = array();
$msg_info = array();
 
$q[] = "CREATE TABLE `patient_health_status` (
 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`status_type` VARCHAR( 25 ) NOT NULL COMMENT 'functional,cognitive or any else',
`patient_id` INT NOT NULL ,
`form_id` INT NOT NULL ,
`status_text` VARCHAR( 250 ) NOT NULL ,
`ccd_code` VARCHAR( 25 ) NOT NULL ,
`ccd_code_system` VARCHAR( 25 ) NOT NULL ,
`status_date` DATE NOT NULL ,
`entered_by` INT NOT NULL ,
`entered_date` DATETIME NOT NULL ,
`del_status` SMALLINT NOT NULL DEFAULT '0'
) ENGINE = MYISAM COMMENT = 'to store functional and Cognitive status';";


foreach($q as $qry){
	imw_query($qry) or $msg_info[]=imw_error();
}


if(count($msg_info)>0)
{
	$msg_info[] = '<br><br><b>Update 13  run FAILED!</b><br>';
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 13  run successfully!</b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 13 (CN)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>