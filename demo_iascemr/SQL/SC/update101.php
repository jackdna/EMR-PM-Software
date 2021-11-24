<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql1="CREATE TABLE `intra_op_post_op_order` (`intraOpId` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL, `deleted` tinyint(1) NOT NULL, `isDefault` tinyint(1) NOT NULL, PRIMARY KEY (`intraOpId`) ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;"; 
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `surgeonprofile` ADD  `intraOpPostOpOrder` text NOT NULL AFTER  `defaultProfile` "; 
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `procedureprofile` ADD  `intraOpPostOpOrder` TEXT NOT NULL AFTER  `preOpOrders` "; 
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `operatingroomrecords` ADD  `intraOpPostOpOrder` TEXT NOT NULL AFTER  `postOpDropsOther` ";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `medications` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `evaluation` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `fooddrinkslist` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `preopcomments` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `site` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `recoverycomments` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `patient2takehome` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `postopdrops` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `oproomnursenotes` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `postopevaluation` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `model` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `laserpredefine_hx_present_illness_tbl` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `laserpredefine_past_medical_hx_tbl` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `laserpredefine_sle_tbl` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `laserpredefine_fundus_exam_tbl` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `laserpredefine_mentalstate_tbl` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `laserpredefine_postprogressnotes_tbl` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `laserpredefine_postoperativestatus_tbl` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `laserpredefine_chiefcomplaint_tbl` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `laserpredefine_spot_size_tbl` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `laserpredefine_power_tbl` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `laserpredefine_shots_tbl` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `laserpredefine_total_energy_tbl` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `laserpredefine_degree_opening_tbl` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `laserpredefine_count_tbl` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `laserpredefine_exposure_tbl` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `predefine_suppliesused` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

$sql1="ALTER TABLE `laserpredefine_spot_duration_tbl` ADD `isDefault` INT NOT NULL;";
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

//Add supply costs
$sql1="ALTER TABLE `predefine_suppliesused` ADD `supplies_cost` DECIMAL( 10, 2 ) NOT NULL"; 
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();

//Add supply costs
$sql1="ALTER TABLE `procedures` ADD `labor_cost` DECIMAL( 10, 2 ) NOT NULL"; 
imw_query(imw_real_escape_string($sql1))or $msg_info[] = imw_error();



if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 101 Failed! </b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 101 Success.</b><br>".$message;
	$color = "green";			
}

?>

<html>
<head>
<title>Update 101</title>
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