<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php

set_time_limit(0);
include_once("../../common/conDb.php");

$sql[] = "ALTER TABLE `surgerycenter` 
			ADD `suppliesPathFromSftp` VARCHAR( 255 ) NOT NULL ,
			ADD `suppliesPathToSftp` VARCHAR( 255 ) NOT NULL ,
			DROP `suppliesFilePath`;";

$sql[] = "ALTER TABLE `operatingroomrecords_supplies` 
			ADD `predefine_supp_id` INT( 11 ) NOT NULL ,
			ADD INDEX predefine_supp_id( predefine_supp_id );";

$sql[] = "CREATE TABLE `predefine_suppliesused_item_detail` (
			`item_id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`suppliesUsedId` INT( 11 ) NOT NULL ,
			`supply_quick_code` VARCHAR( 255 ) NOT NULL ,
			`stock_id` VARCHAR( 255 ) NOT NULL ,
			`serial_number` VARCHAR( 255 ) NOT NULL ,
			`lot_number` VARCHAR( 255 ) NOT NULL ,
			`expiration_date` DATE NOT NULL ,
			`used_status` TINYINT( 1 ) NOT NULL ,
			INDEX suppliesUsedId( `suppliesUsedId`) , 
			INDEX supply_quick_code(`supply_quick_code`) , 
			INDEX stock_id(`stock_id`) , 
			INDEX serial_number(`serial_number` ),
			INDEX used_status(`used_status` )
			);";

$sql[] = "ALTER TABLE `patientconfirmation` ADD INDEX ascId(ascId);";

$sql[] = "ALTER TABLE predefine_suppliesused CONVERT TO CHARACTER SET latin1 COLLATE 'latin1_swedish_ci';";

$sql[] = "ALTER TABLE predefine_suppliesused_item_detail CONVERT TO CHARACTER SET latin1 COLLATE 'latin1_swedish_ci';";

$sql[] = "ALTER TABLE operatingroomrecords_supplies CONVERT TO CHARACTER SET latin1 COLLATE 'latin1_swedish_ci';";

$sql[] = "ALTER TABLE `operatingroomrecords_supplies` ADD INDEX suppName( suppName ) ;"; 

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = $qry.imw_error();
}

$qry1 = "UPDATE operatingroomrecords_supplies os
		INNER JOIN predefine_suppliesused ps ON (ps.name = os.suppName)
		SET os.predefine_supp_id = ps.suppliesUsedId
		WHERE os.predefine_supp_id ='0';";
imw_query($qry1) or $msg_info[] = $qry1.imw_error();


$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 186 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 186 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 186</title>
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