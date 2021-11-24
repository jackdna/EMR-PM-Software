<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("../../common/conDb.php");

$sql="CREATE TABLE iolink_patient_alert_tbl (
 `patient_alert_id` BIGINT( 11)NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `next_gen_alert_id` BIGINT( 11)NOT NULL ,
 `patient_id` INT( 11)NOT NULL ,
 `alert_content` TEXT NOT NULL ,
 `save_date_time` DATETIME NOT NULL ,
 `alert_disabled` VARCHAR( 255)NOT NULL ,
 `alert_disabled_date_time` DATETIME NOT NULL ,
 `alert_disabled_by` VARCHAR( 255)NOT NULL ,
 `iosync_status` VARCHAR( 100)NOT NULL 
) ENGINE=MYISAM COMMENT='Table created to send Sx alerts from iDoc->iOlink->SurgeryCenter';
";
$row = imw_query($sql) or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Updates 10 run OK";

?>

<html>
<head>
<title>Mysql Updates After Launch</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>







