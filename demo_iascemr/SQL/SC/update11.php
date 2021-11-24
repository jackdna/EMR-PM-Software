<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("../../common/conDb.php");


$sql = "ALTER TABLE `surgical_check_list` ADD `signNurse1Id` INT(11) NOT NULL, ADD `signNurse1FirstName` VARCHAR(255) NOT NULL, ADD `signNurse1MiddleName` VARCHAR(255) NOT NULL, ADD `signNurse1LastName` VARCHAR(255) NOT NULL, ADD `signNurse1FileStatus` VARCHAR(255) NOT NULL,
		ADD `signNurse1DateTime` DATETIME NOT NULL, ADD `reliefNurse1` VARCHAR(255) NOT NULL, ADD `signNurse2Id` INT(11) NOT NULL, ADD `signNurse2FirstName` VARCHAR(255) NOT NULL, ADD `signNurse2MiddleName` VARCHAR(255) NOT NULL, ADD `signNurse2LastName` VARCHAR(255) 
		NOT NULL, ADD `signNurse2FileStatus` VARCHAR(255) NOT NULL, ADD `signNurse2DateTime` DATETIME NOT NULL, ADD `reliefNurse2` VARCHAR(255) NOT NULL, ADD `signNurse3Id` INT(11) NOT NULL, ADD `signNurse3FirstName` VARCHAR(255) NOT NULL, ADD `signNurse3MiddleName` VARCHAR(255) 
		NOT NULL, ADD `signNurse3LastName` VARCHAR(255) NOT NULL, ADD `signNurse3FileStatus` VARCHAR(255) NOT NULL, ADD `signNurse3DateTime` DATETIME NOT NULL, ADD `reliefNurse3` VARCHAR(255) NOT NULL, ADD `signNurse4Id` INT(11) NOT NULL, ADD `signNurse4FirstName` VARCHAR(255) 
		NOT NULL, ADD `signNurse4MiddleName` VARCHAR(255) NOT NULL, ADD `signNurse4LastName` VARCHAR(255) NOT NULL, ADD `signNurse4FileStatus` VARCHAR(255) NOT NULL, ADD `signNurse4DateTime` DATETIME NOT NULL, ADD `reliefNurse4` VARCHAR(255) NOT NULL;";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `surgical_check_list` DROP `procedure_check_in_nurse_id` ,
		DROP `sign_in_nurse_id` ,
		DROP `time_out_nurse_id` ,
		DROP `sign_out_nurse_id` ;
		";
$row = imw_query($sql) or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 11 run OK";

?>

<html>
<head>
<title>Mysql Updates For Create Table in surgical_check_list</title>
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







