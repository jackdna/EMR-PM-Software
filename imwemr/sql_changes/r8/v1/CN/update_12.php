<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$q = array();
$msg_info = array();
 
$q[] = "CREATE TABLE `hc_observations` (
	`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
	`form_id` INT(11) NOT NULL COMMENT 'chart note id', 
	`pt_id` INT(11) NOT NULL COMMENT 'Patient Id', 
	`observation_date` DATE NOT NULL, 
	`observation` VARCHAR(255) NOT NULL,
	`status` VARCHAR(20) NOT NULL,
	`entry_date_time` DATETIME NOT NULL, 
	`modified_date_time` DATETIME NOT NULL, 
	`operator_id` INT(11) NOT NULL COMMENT 'IMW Operator ID',
	`del_status` BOOL NOT NULL
) ENGINE = MyISAM;";


$q[] = "CREATE TABLE `hc_concerns` (
	`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
	`concern_date` DATE NOT NULL, 
	`concern` VARCHAR(255) NOT NULL,
	`status` VARCHAR(20) NOT NULL,
	`entry_date_time` DATETIME NOT NULL, 
	`modified_date_time` DATETIME NOT NULL, 
	`operator_id` INT(11) NOT NULL COMMENT 'IMW Operator ID',
	`observation_id` INT(11) NOT NULL COMMENT 'Observation ID', 
	`del_status` BOOL NOT NULL
) ENGINE = MyISAM;";


$q[] = "CREATE TABLE `hc_rel_observations` (
	`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
	`rel_observation_date` DATE NOT NULL, 
	`rel_observation` VARCHAR(255) NOT NULL,
	`entry_date_time` DATETIME NOT NULL, 
	`modified_date_time` DATETIME NOT NULL, 
	`operator_id` INT(11) NOT NULL COMMENT 'IMW Operator ID',
	`observation_id` INT(11) NOT NULL COMMENT 'Observation ID', 
	`del_status` BOOL NOT NULL
) ENGINE = MyISAM;";

$q[] = "ALTER TABLE `hc_observations` ADD `snomed_code` VARCHAR(20) NOT NULL AFTER `pt_id`;";
$q[] = "ALTER TABLE `hc_rel_observations` ADD `snomed_code` VARCHAR(20) NOT NULL AFTER `rel_observation`;";

foreach($q as $qry)
{
	imw_query($qry) or $msg_info[]=imw_error();
}


if(count($msg_info)>0)
{
	$msg_info[] = '<br><br><b>Update 12  run FAILED!</b><br>';
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 12  run successfully!</b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 12 (CN)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>