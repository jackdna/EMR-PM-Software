<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql = "
ALTER TABLE `stub_tbl` ADD `recentChartSaved` VARCHAR( 255 ) NOT NULL  ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `preophealthquestionnaire` ADD `heartTroubleDesc` TEXT NOT NULL ,
ADD `strokeDesc` TEXT NOT NULL ,
ADD `HighBPDesc` TEXT NOT NULL ,
ADD `anticoagulationTherapyDesc` TEXT NOT NULL ,
ADD `asthmaDesc` TEXT NOT NULL ,
ADD `tuberculosisDesc` TEXT NOT NULL ,
ADD `diabetesDesc` TEXT NOT NULL ,
ADD `epilepsyDesc` TEXT NOT NULL ,
ADD `restlessLegSyndromeDesc` TEXT NOT NULL ,
ADD `hepatitisDesc` TEXT NOT NULL ,
ADD `kidneyDiseaseDesc` TEXT NOT NULL ,
ADD `anesthesiaBadReactionDesc` TEXT NOT NULL ,
ADD `walkerDesc` TEXT NOT NULL ,
ADD `contactLensesDesc` TEXT NOT NULL ,
ADD `autoInternalDefibrillatorDesc` TEXT NOT NULL ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = " ALTER TABLE `postopphysicianorders` ADD `postOpPhyTime` VARCHAR( 255 ) NOT NULL  ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `localanesthesiarecord` ADD `chbx_vss` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_atsf` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_pa` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_nausea` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_vomiting` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_dizziness` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_rd` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_aao` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_ddai` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_pv` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_rtpog` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_pain` VARCHAR( 5 ) NOT NULL  ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `anesthesia_profile_tbl` ADD `chbx_vss` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_atsf` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_pa` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_nausea` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_vomiting` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_dizziness` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_rd` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_aao` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_ddai` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_pv` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_rtpog` VARCHAR( 5 ) NOT NULL ,
ADD `chbx_pain` VARCHAR( 5 ) NOT NULL  ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 25 run OK";

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







