<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql = "
CREATE TABLE `narcotics_log_tbl` (
`log_id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`patient_id` INT( 11 ) NOT NULL ,
`patient_confirmation_id` INT( 11 ) NOT NULL ,
`patient_name` VARCHAR( 255 ) NOT NULL ,
`date_of_service` DATE  NOT NULL ,
`user_id`  INT( 11 ) NOT NULL ,
`save_date_time` DATETIME NOT NULL ,
`narotics_medication_id` INT( 11 ) NOT NULL ,
`narcotics_medication_descripation` VARCHAR( 255 ) NOT NULL ,
`narotics_med_other` VARCHAR( 255 ) NOT NULL ,
`md_crna` VARCHAR( 255 ) NOT NULL ) ENGINE = MYISAM;
";
$row = imw_query($sql) or $msg_info[] = imw_error();


$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 17 run OK";

//line 12323
?>

<html>
<head>
<title>Mysql Updates For Query Optimization</title>
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







