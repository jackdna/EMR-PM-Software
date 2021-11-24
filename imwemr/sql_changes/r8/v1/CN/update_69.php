<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$sql = '
CREATE TABLE `chart_ant_chamber` (
  `id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `uid` int(10) NOT NULL,
  `statusElem` varchar(255) NOT NULL,
  `purged` int(2) NOT NULL,
  `purgerId` int(10) NOT NULL,
  `purgeTime` datetime NOT NULL,
  `ut_elem` text NOT NULL,
  `anf_chamber_od` longtext NOT NULL,
  `anf_chamber_od_summary` text NOT NULL,
  `anf_chamber_os` longtext NOT NULL,
  `anf_chamber_os_summary` text NOT NULL,
  `wnlAnt` int(2) NOT NULL,
  `wnlAntOd` int(2) NOT NULL,
  `wnlAntOs` int(2) NOT NULL,
  `posAnt` int(2) NOT NULL,
  `ncAnt` int(2) NOT NULL,
  `ncAnt_od` int(2) NOT NULL,
  `ncAnt_os` int(2) NOT NULL,
  `pen_light` int(2) NOT NULL,
  `modi_note_AntArr` text NOT NULL,
  `last_opr_id` int(10) NOT NULL,
  `wnl_value_Ant` varchar(255) NOT NULL
) ENGINE=InnoDB;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
CREATE TABLE `chart_conjunctiva` (
  `id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `uid` int(10) NOT NULL,
  `statusElem` varchar(255) NOT NULL,
  `purged` int(2) NOT NULL,
  `purgerId` int(10) NOT NULL,
  `purgeTime` datetime NOT NULL,
  `ut_elem` text NOT NULL,
  `conjunctiva_od` longtext NOT NULL,
  `conjunctiva_od_summary` text NOT NULL,
  `conjunctiva_os` longtext NOT NULL,
  `conjunctiva_os_summary` text NOT NULL,
  `wnlConj` int(2) NOT NULL,
  `wnlConjOd` int(2) NOT NULL,
  `wnlConjOs` int(2) NOT NULL,
  `posConj` int(2) NOT NULL,
  `ncConj` int(2) NOT NULL,
  `ncConj_od` int(2) NOT NULL,
  `ncConj_os` int(2) NOT NULL,
  `pen_light` int(2) NOT NULL,
  `modi_note_ConjArr` text NOT NULL,
  `last_opr_id` int(10) NOT NULL,
  `wnl_value_Conjunctiva` varchar(255) NOT NULL
) ENGINE=InnoDB;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
CREATE TABLE `chart_cornea` (
  `id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `uid` int(10) NOT NULL,
  `statusElem` varchar(255) NOT NULL,
  `purged` int(2) NOT NULL,
  `purgerId` int(10) NOT NULL,
  `purgeTime` datetime NOT NULL,
  `ut_elem` text NOT NULL,
  `cornea_od` longtext NOT NULL,
  `cornea_od_summary` text NOT NULL,
  `cornea_os` longtext NOT NULL,
  `cornea_os_summary` text NOT NULL,
  `wnlCorn` int(2) NOT NULL,
  `wnlCornOd` int(2) NOT NULL,
  `wnlCornOs` int(2) NOT NULL,
  `posCorn` int(2) NOT NULL,
  `ncCorn` int(2) NOT NULL,
  `ncCorn_od` int(2) NOT NULL,
  `ncCorn_os` int(2) NOT NULL,
  `pen_light` int(2) NOT NULL,
  `modi_note_CornArr` text NOT NULL,
  `last_opr_id` int(10) NOT NULL,
  `wnl_value_Cornea` varchar(255) NOT NULL
) ENGINE=InnoDB;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
CREATE TABLE `chart_iris` (
  `id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `uid` int(10) NOT NULL,
  `statusElem` varchar(255) NOT NULL,
  `purged` int(2) NOT NULL,
  `purgerId` int(10) NOT NULL,
  `purgeTime` datetime NOT NULL,
  `ut_elem` text NOT NULL,
  `iris_pupil_od` longtext NOT NULL,
  `iris_pupil_od_summary` text NOT NULL,
  `iris_pupil_os` longtext NOT NULL,
  `iris_pupil_os_summary` text NOT NULL,
  `wnlIris` int(2) NOT NULL,
  `wnlIrisOd` int(2) NOT NULL,
  `wnlIrisOs` int(2) NOT NULL,
  `posIris` int(2) NOT NULL,
  `ncIris` int(2) NOT NULL,
  `ncIris_od` int(2) NOT NULL,
  `ncIris_os` int(2) NOT NULL,
  `pen_light` int(2) NOT NULL,
  `modi_note_IrisArr` text NOT NULL,
  `last_opr_id` int(10) NOT NULL,
  `wnl_value_Iris` varchar(255) NOT NULL
) ENGINE=InnoDB;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
CREATE TABLE `chart_lens` (
  `id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `uid` int(10) NOT NULL,
  `statusElem` varchar(255) NOT NULL,
  `purged` int(2) NOT NULL,
  `purgerId` int(10) NOT NULL,
  `purgeTime` datetime NOT NULL,
  `ut_elem` text NOT NULL,
  `lens_od` longtext NOT NULL,
  `lens_od_summary` text NOT NULL,
  `lens_os` longtext NOT NULL,
  `lens_os_summary` text NOT NULL,
  `wnlLens` int(2) NOT NULL,
  `wnlLensOd` int(2) NOT NULL,
  `wnlLensOs` int(2) NOT NULL,
  `posLens` int(2) NOT NULL,
  `ncLens` int(2) NOT NULL,
  `ncLens_od` int(2) NOT NULL,
  `ncLens_os` int(2) NOT NULL,
  `pen_light` int(2) NOT NULL,
  `modi_note_LensArr` text NOT NULL,
  `last_opr_id` int(10) NOT NULL,
  `wnl_value_Lens` varchar(255) NOT NULL
) ENGINE=InnoDB;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_ant_chamber`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD UNIQUE( `form_id`, `purged`),
  ADD KEY `patient_id` (`patient_id`);
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_conjunctiva`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD UNIQUE( `form_id`, `purged`),
  ADD KEY `patient_id` (`patient_id`);
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_cornea`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD UNIQUE( `form_id`, `purged`),
  ADD KEY `patient_id` (`patient_id`);
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_iris`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD UNIQUE( `form_id`, `purged`),
  ADD KEY `patient_id` (`patient_id`);
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_lens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD UNIQUE( `form_id`, `purged`),
  ADD KEY `patient_id` (`patient_id`);
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_ant_chamber`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_conjunctiva`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_cornea`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_iris`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_lens`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';
$result = imw_query($sql) or $msg_info[] = imw_error();


if(!$result)
{
	$msg_info[] = '<br><br><b>Update 69:: Update run FAILED!</b><br><br>';
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 69 :: Update run successfully!<br></b>";
	$color = "green";	
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 69</title>
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