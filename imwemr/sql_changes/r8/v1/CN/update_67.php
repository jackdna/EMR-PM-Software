<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$sql = '
CREATE TABLE `chart_blood_vessels` (
  `id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `blood_vessels_od` longtext NOT NULL,
  `blood_vessels_od_summary` text NOT NULL,
  `blood_vessels_os` longtext NOT NULL,
  `blood_vessels_os_summary` text NOT NULL,
  `wnlBV` int(2) NOT NULL,
  `posBV` int(2) NOT NULL,
  `ncBV` int(2) NOT NULL,
  `wnlBVOd` int(2) NOT NULL,
  `wnlBVOs` int(2) NOT NULL,
  `uid` int(10) NOT NULL,
  `statusElem` varchar(255) NOT NULL,
  `ncBV_od` int(2) NOT NULL,
  `ncBV_os` int(2) NOT NULL,
  `purged` int(2) NOT NULL,
  `purgerId` int(10) NOT NULL,
  `purgeTime` datetime NOT NULL,
  `ut_elem` text NOT NULL,
  `last_opr_id` int(10) NOT NULL,
  `wnl_value_BV` varchar(250) NOT NULL
) ENGINE=InnoDB;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
CREATE TABLE `chart_macula` (
  `id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `macula_od` longtext NOT NULL,
  `macula_od_summary` text NOT NULL,
  `macula_os` longtext NOT NULL,
  `macula_os_summary` text NOT NULL,
  `wnlMacula` int(2) NOT NULL,
  `posMacula` int(2) NOT NULL,
  `ncMacula` int(2) NOT NULL,
  `wnlMaculaOd` int(2) NOT NULL,
  `wnlMaculaOs` int(2) NOT NULL,
  `uid` int(10) NOT NULL,
  `statusElem` varchar(255) NOT NULL,
  `ncMacula_od` int(2) NOT NULL,
  `ncMacula_os` int(2) NOT NULL,
  `purged` int(2) NOT NULL,
  `purgerId` int(10) NOT NULL,
  `purgeTime` datetime NOT NULL,
  `ut_elem` text NOT NULL,
  `last_opr_id` int(10) NOT NULL,
  `wnl_value_Macula` varchar(250) NOT NULL
) ENGINE=InnoDB;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
CREATE TABLE `chart_periphery` (
  `id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `periphery_od` longtext NOT NULL,
  `periphery_os` longtext NOT NULL,
  `wnlPeri` int(2) NOT NULL,
  `posPeri` int(2) NOT NULL,
  `ncPeri` int(2) NOT NULL,
  `periphery_od_summary` text NOT NULL,
  `periphery_os_summary` text NOT NULL,
  `wnlPeriOd` int(2) NOT NULL,
  `wnlPeriOs` int(2) NOT NULL,
  `uid` int(10) NOT NULL,
  `statusElem` varchar(255) NOT NULL,
  `ncPeri_od` int(2) NOT NULL,
  `ncPeri_os` int(2) NOT NULL,
  `purged` int(2) NOT NULL,
  `purgerId` int(10) NOT NULL,
  `purgeTime` datetime NOT NULL,
  `ut_elem` text NOT NULL,
  `last_opr_id` int(10) NOT NULL,
  `wnl_value_Peri` varchar(250) NOT NULL
) ENGINE=InnoDB;
';
$result = imw_query($sql) or $msg_info[] = imw_error();


$sql = '
CREATE TABLE `chart_retinal_exam` (
  `id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `uid` int(10) NOT NULL,
  `statusElem` varchar(255) NOT NULL,
  `purged` int(2) NOT NULL,
  `purgerId` int(10) NOT NULL,
  `purgeTime` datetime NOT NULL,
  `retinal_od` longtext NOT NULL,
  `retinal_od_summary` text NOT NULL,
  `retinal_os` longtext NOT NULL,
  `retinal_os_summary` text NOT NULL,
  `wnlRetinal` int(2) NOT NULL,
  `ncRetinal` int(2) NOT NULL,
  `posRetinal` int(2) NOT NULL,
  `wnlRetinalOd` int(2) NOT NULL,
  `wnlRetinalOs` int(2) NOT NULL,
  `ncRetinal_od` int(2) NOT NULL,
  `ncRetinal_os` int(2) NOT NULL,
  `periNotExamined` int(2) NOT NULL,
  `peri_ne_eye` varchar(20) NOT NULL,
  `ut_elem` text NOT NULL,
  `modi_note_retinalArr` text NOT NULL,
  `last_opr_id` int(10) NOT NULL,
  `emerstt_lvlSeverityRetFind` varchar(50) NOT NULL,
  `emerstt_macEdFind` varchar(20) NOT NULL,
  `emerstt_comm_p2p` varchar(20) NOT NULL,
  `emerstt_lvlSeverity` varchar(20) NOT NULL,
  `wnl_value_RetinalExam` varchar(255) NOT NULL
) ENGINE=InnoDB;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
CREATE TABLE `chart_vitreous` (
  `id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `vitreous_od` longtext NOT NULL,
  `vitreous_od_summary` text NOT NULL,
  `vitreous_os` longtext NOT NULL,
  `vitreous_os_summary` text NOT NULL,
  `wnlVitreous` int(2) NOT NULL,
  `posVitreous` int(2) NOT NULL,
  `ncVitreous` int(2) NOT NULL,
  `wnlVitreousOd` int(2) NOT NULL,
  `wnlVitreousOs` int(2) NOT NULL,
  `uid` int(10) NOT NULL,
  `statusElem` varchar(255) NOT NULL,
  `ncVitreous_od` int(2) NOT NULL,
  `ncVitreous_os` int(2) NOT NULL,
  `purged` int(2) NOT NULL,
  `purgerId` int(10) NOT NULL,
  `purgeTime` datetime NOT NULL,
  `ut_elem` text NOT NULL,
  `modi_note_vitreousArr` text NOT NULL,
  `last_opr_id` int(10) NOT NULL,
  `wnl_value_Vitreous` varchar(255) NOT NULL
) ENGINE=InnoDB;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_blood_vessels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD UNIQUE( `form_id`, `purged`),
  ADD KEY `patient_id` (`patient_id`);
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_macula`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD UNIQUE( `form_id`, `purged`),
  ADD KEY `patient_id` (`patient_id`);
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_periphery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD UNIQUE( `form_id`, `purged`),
  ADD KEY `patient_id` (`patient_id`);
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_retinal_exam`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD UNIQUE( `form_id`, `purged`),
  ADD KEY `patient_id` (`patient_id`);
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_vitreous`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD UNIQUE( `form_id`, `purged`),
  ADD KEY `patient_id` (`patient_id`);
';
$result = imw_query($sql) or $msg_info[] = imw_error();


$sql = '
ALTER TABLE `chart_blood_vessels`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_macula`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';
$result = imw_query($sql) or $msg_info[] = imw_error();


$sql = '
ALTER TABLE `chart_periphery`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_retinal_exam`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_vitreous`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

if(!$result)
{
	$msg_info[] = '<br><br><b>Update 67:: Update run FAILED!</b><br><br>';
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 67 :: Update run successfully!<br></b>";
	$color = "green";	
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 67</title>
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