<?php 
/**
 * EXPECTED ARRIVAL TIME COLUMN ADDED FOR PROCEDURES
 */ 
 
//------ENABLE TO ACCESS FILE------
$ignoreAuth = true; 

//------GLOBAL FILE INCLUSION------
include("../../../../config/globals.php"); 

//------ADD COLUMN QUERY-----------
$sql="SHOW INDEX FROM `provider_schedule_tmp` WHERE key_name = 'provider' ";
$row=imw_query($sql);
if($row == false){
	$sql1[]="ALTER TABLE `provider_schedule_tmp` ADD INDEX( `provider`);";
}

$sql="SHOW INDEX FROM schedule_label_tbl WHERE key_name = 'schedulelabeltbl_schtempid ' ";
$row=imw_query($sql);
if($row == false){
	$sql1[]="CREATE INDEX schedulelabeltbl_schtempid ON schedule_label_tbl (sch_template_id);";
}

$sql1[]="ALTER TABLE `provider_schedule_tmp` CHANGE `provider` `provider` INT NOT NULL,
CHANGE `facility` `facility` INT NOT NULL,
CHANGE `sch_tmp_id` `sch_tmp_id` INT NOT NULL,
CHANGE `week1` `week1` SMALLINT NOT NULL,
CHANGE `week2` `week2` SMALLINT NOT NULL,
CHANGE `week3` `week3` SMALLINT NOT NULL,
CHANGE `week4` `week4` SMALLINT NOT NULL,
CHANGE `week5` `week5` SMALLINT NOT NULL,
CHANGE `week6` `week6` SMALLINT NOT NULL";

foreach($sql1 as $q)
{
	imw_query($q) or $msg_info[] = imw_error();
}

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 20 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 20 completed successfully.</b>`";
	$color = "green";
}
?>
<html>
<head>
<title>Update 20 - Create index and change fields data type.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>