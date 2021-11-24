<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include("../../common/conDb.php");

$msg_info = array();
$sql[] = " ALTER TABLE  `laser_procedure_patient_table` ADD  `discharge_home` TINYINT( 1 ) NOT NULL;";
$sql[] = " ALTER TABLE  `laser_procedure_patient_table` ADD  `patients_relation` VARCHAR( 64 ) NOT NULL;";
$sql[] = " ALTER TABLE  `laser_procedure_patient_table` ADD  `patients_relation_other` VARCHAR( 255 ) NOT NULL;";
$sql[] = " ALTER TABLE  `laser_procedure_patient_table` ADD  `patient_transfer` TINYINT( 1 ) NOT NULL;";
$sql[] = " ALTER TABLE  `laser_procedure_patient_table` ADD  `discharge_time` TIME NOT NULL;";

$sql[] = " ALTER TABLE `dischargesummarysheet`    
			ADD `cpt_inte_sync_status` INT(11) NOT NULL,  
			ADD `cpt_inte_sync_date_time` DATETIME NOT NULL,
			ADD INDEX cpt_inte_sync_status(cpt_inte_sync_status);"; 

$sql[] = " ALTER TABLE `dischargesummarysheet`    
			ADD `cpt_inte_sync_flag` INT(11) NOT NULL,  
			ADD INDEX cpt_inte_sync_flag(cpt_inte_sync_flag);"; 

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 174 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 174 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 174</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
@imw_close();
}
?> 
</body>
</html>