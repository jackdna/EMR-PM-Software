<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");


$sql = '
CREATE TABLE `chart_drawings` (
  `id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `drw_od_txt` text NOT NULL,
  `drw_os_txt` text NOT NULL,
  `wnlDraw` int(2) NOT NULL,
  `posDraw` int(2) NOT NULL,
  `ncDraw` int(2) NOT NULL,
  `wnlDrawOd` int(2) NOT NULL,
  `wnlDrawOs` int(2) NOT NULL,
  `uid` int(10) NOT NULL,
  `statusElem` varchar(255) NOT NULL,
  `idoc_drawing_id` varchar(255) NOT NULL,
  `ncDraw_od` int(2) NOT NULL,
  `ncDraw_os` int(2) NOT NULL,
  `ut_elem` text NOT NULL,
  `purged` int(2) NOT NULL,
  `purgerId` int(10) NOT NULL,
  `purgeTime` datetime NOT NULL,
  `last_opr_id` int(10) NOT NULL,
  `exam_name` varchar(100) NOT NULL,
  `exm_drawing` longblob NOT NULL,
  `drawing_insert_update_from` int(1) NOT NULL
) ENGINE=InnoDB;
';
$result = imw_query($sql) or $msg_info[] = imw_error();


$sql = '
CREATE TABLE `chart_lac_sys` (
  `id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `lacrimal_system_summary` longtext NOT NULL,
  `sumLacOs` longtext NOT NULL,
  `lacrimal_od` longtext NOT NULL,
  `lacrimal_os` longtext NOT NULL,
  `wnlLacSys` int(2) NOT NULL,
  `posLacSys` int(2) NOT NULL,
  `ncLacSys` int(2) NOT NULL,
  `wnlLacSysOd` int(2) NOT NULL,
  `wnlLacSysOs` int(2) NOT NULL,
  `uid` int(10) NOT NULL,
  `statusElem` varchar(255) NOT NULL,
  `ncLacSys_od` int(2) NOT NULL,
  `ncLacSys_os` int(2) NOT NULL,
  `ut_elem` text NOT NULL,
  `purged` int(2) NOT NULL,
  `purgerId` int(10) NOT NULL,
  `purgeTime` datetime NOT NULL,
  `modi_note_LacSysArr` text NOT NULL,
  `last_opr_id` int(10) NOT NULL,
  `wnl_value_LacSys` varchar(250) NOT NULL
) ENGINE=InnoDB;
';
$result = imw_query($sql) or $msg_info[] = imw_error();


$sql = '
CREATE TABLE `chart_lesion` (
  `id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `lesion_summary` longtext NOT NULL,
  `sumLesionOs` longtext NOT NULL,
  `lesion_od` longtext NOT NULL,
  `lesion_os` longtext NOT NULL,
  `wnlLesion` int(2) NOT NULL,
  `posLesion` int(2) NOT NULL,
  `ncLesion` int(2) NOT NULL,
  `wnlLesionOd` int(2) NOT NULL,
  `wnlLesionOs` int(2) NOT NULL,
  `uid` int(10) NOT NULL,
  `statusElem` varchar(255) NOT NULL,
  `ncLesion_od` int(2) NOT NULL,
  `ncLesion_os` int(2) NOT NULL,
  `ut_elem` text NOT NULL,
  `purged` int(2) NOT NULL,
  `purgerId` int(10) NOT NULL,
  `purgeTime` datetime NOT NULL,
  `modi_note_LesionArr` text NOT NULL,
  `last_opr_id` int(10) NOT NULL,
  `wnl_value_Lesion` varchar(250) NOT NULL
) ENGINE=InnoDB;
';
$result = imw_query($sql) or $msg_info[] = imw_error();


$sql = '
CREATE TABLE `chart_lids` (
  `id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `lid_od` longtext NOT NULL,
  `lid_os` longtext NOT NULL,
  `wnlLids` int(2) NOT NULL,
  `posLids` int(2) NOT NULL,
  `ncLids` int(2) NOT NULL,
  `lid_conjunctiva_summary` longtext NOT NULL,
  `sumLidsOs` longtext NOT NULL,
  `wnlLidsOd` int(2) NOT NULL,
  `wnlLidsOs` int(2) NOT NULL,
  `uid` int(10) NOT NULL,
  `statusElem` varchar(255) NOT NULL,
  `ncLids_od` int(2) NOT NULL,
  `ncLids_os` int(2) NOT NULL,
  `ut_elem` text NOT NULL,
  `purged` int(2) NOT NULL,
  `purgerId` int(10) NOT NULL,
  `purgeTime` datetime NOT NULL,
  `modi_note_LidsArr` text NOT NULL,
  `last_opr_id` int(10) NOT NULL,
  `wnl_value_Lids` varchar(250) NOT NULL
) ENGINE=InnoDB;
';
$result = imw_query($sql) or $msg_info[] = imw_error();


$sql = '
CREATE TABLE `chart_lid_pos` (
  `id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `lid_deformity_position_summary` longtext NOT NULL,
  `sumLidPosOs` longtext NOT NULL,
  `lidposition_od` longtext NOT NULL,
  `lidposition_os` longtext NOT NULL,
  `wnlLidPos` int(2) NOT NULL,
  `posLidPos` int(2) NOT NULL,
  `ncLidPos` int(2) NOT NULL,
  `wnlLidPosOd` int(2) NOT NULL,
  `wnlLidPosOs` int(2) NOT NULL,
  `uid` int(10) NOT NULL,
  `statusElem` varchar(255) NOT NULL,
  `ncLidPos_od` int(2) NOT NULL,
  `ncLidPos_os` int(2) NOT NULL,
  `ut_elem` text NOT NULL,
  `purged` int(2) NOT NULL,
  `purgerId` int(10) NOT NULL,
  `purgeTime` datetime NOT NULL,
  `modi_note_LidPosArr` text NOT NULL,
  `last_opr_id` int(10) NOT NULL,
  `wnl_value_LidPos` varchar(250) NOT NULL
) ENGINE=InnoDB;
';
$result = imw_query($sql) or $msg_info[] = imw_error();


$sql = '
ALTER TABLE `chart_drawings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD KEY `patient_id` (`patient_id`), 
  ADD UNIQUE( `form_id`, `purged`, `exam_name`),
  ADD KEY `idoc_drawing_id` (`idoc_drawing_id`);
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_lac_sys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD UNIQUE( `form_id`, `purged`),
  ADD KEY `patient_id` (`patient_id`);
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_lesion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD UNIQUE( `form_id`, `purged`),
  ADD KEY `patient_id` (`patient_id`);
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_lids`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD UNIQUE( `form_id`, `purged`),
  ADD KEY `patient_id` (`patient_id`);
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_lid_pos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD UNIQUE( `form_id`, `purged`),
  ADD KEY `patient_id` (`patient_id`);
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_drawings`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_lac_sys`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_lesion`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_lids`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_lid_pos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

if(!$result)
{
	$msg_info[] = '<br><br><b>Update 65 :: Update run FAILED!</b><br><br>';
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 65 :: Update run successfully!<br></b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 65</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
	label{display:inline-block; width:100px; border:0px solid red;}
</style>
</head>
<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>

</body>
</html>