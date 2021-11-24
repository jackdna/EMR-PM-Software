<?php
set_time_limit(0);
$ignoreAuth = true;
include(dirname(__FILE__)."/../../../../config/globals.php");

$q[] = "
CREATE TABLE `chart_vis_master` (
  `id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `status_elements` text NOT NULL,
  `ut_elem` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";

$q[] = "
	ALTER TABLE `chart_vis_master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `form_id` (`form_id`);
";

$q[] = "
ALTER TABLE `chart_vis_master`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
";

$q[] = "
CREATE TABLE `chart_acuity` (
  `id` int(10) NOT NULL,
  `id_chart_vis_master` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `uid` int(10) NOT NULL,
  `sec_name` varchar(250) NOT NULL,
  `sec_indx` int(2) NOT NULL,
  `snellen` varchar(200) NOT NULL,
  `ex_desc` tinytext NOT NULL,
  `sel_od` varchar(50) NOT NULL,
  `txt_od` varchar(50) NOT NULL,
  `sel_os` varchar(50) NOT NULL,
  `txt_os` varchar(50) NOT NULL,
  `sel_ou` varchar(50) NOT NULL,
  `txt_ou` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";

$q[]="
ALTER TABLE `chart_acuity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `acuity_vis_id` (`id_chart_vis_master`);
";


$q[] = "
ALTER TABLE `chart_acuity`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
";

$q[] = "
ALTER TABLE `chart_acuity`
  ADD CONSTRAINT `acuity_vis_id` FOREIGN KEY (`id_chart_vis_master`) REFERENCES `chart_vis_master` (`id`);
";

$q[] = "
CREATE TABLE `chart_ak` (
  `id` int(10) NOT NULL,
  `id_chart_vis_master` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `uid` int(10) NOT NULL,
  `k_od` varchar(50) NOT NULL,
  `slash_od` varchar(50) NOT NULL,
  `x_od` varchar(50) NOT NULL,
  `k_os` varchar(50) NOT NULL,
  `slash_os` varchar(50) NOT NULL,
  `x_os` varchar(50) NOT NULL,
  `k_type` varchar(50) NOT NULL,
  `ex_desc` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";

$q[] = "
ALTER TABLE `chart_ak`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ak_vis_id` (`id_chart_vis_master`);";
  
$q[] = "  
  ALTER TABLE `chart_ak`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;";
  
$q[] = "  
  ALTER TABLE `chart_ak`
  ADD CONSTRAINT `ak_vis_id` FOREIGN KEY (`id_chart_vis_master`) REFERENCES `chart_vis_master` (`id`);";

$q[] = "
CREATE TABLE `chart_sca` (
  `id` int(10) NOT NULL,
  `id_chart_vis_master` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `uid` int(10) NOT NULL,
  `sec_name` varchar(50) NOT NULL,
  `s_od` varchar(50) NOT NULL,
  `c_od` varchar(50) NOT NULL,
  `a_od` varchar(50) NOT NULL,
  `sel_od` varchar(50) NOT NULL,
  `s_os` varchar(50) NOT NULL,
  `c_os` varchar(50) NOT NULL,
  `a_os` varchar(50) NOT NULL,
  `sel_os` varchar(50) NOT NULL,
  `ex_desc` tinytext NOT NULL,
  `ar_ref_place` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

$q[] = "
ALTER TABLE `chart_sca`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sca_vis_id` (`id_chart_vis_master`);";
  
$q[] = "  
  ALTER TABLE `chart_sca`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;";
  
 $q[] = " 
  ALTER TABLE `chart_sca`
  ADD CONSTRAINT `sca_vis_id` FOREIGN KEY (`id_chart_vis_master`) REFERENCES `chart_vis_master` (`id`);";
  
 $q[] = " 
  CREATE TABLE `chart_exo` (
  `id` int(10) NOT NULL,
  `id_chart_vis_master` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `uid` int(10) NOT NULL,
  `pd` varchar(50) NOT NULL,
  `pd_od` varchar(50) NOT NULL,
  `pd_os` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

$q[] = "
ALTER TABLE `chart_exo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exo_vis_id` (`id_chart_vis_master`);";
  
 $q[] = " 
  ALTER TABLE `chart_exo`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;";
  
 $q[] = " 
  ALTER TABLE `chart_exo`
  ADD CONSTRAINT `exo_vis_id` FOREIGN KEY (`id_chart_vis_master`) REFERENCES `chart_vis_master` (`id`);";
  
 $q[] = " 
  CREATE TABLE `chart_bat` (
  `id` int(10) NOT NULL,
  `id_chart_vis_master` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `uid` int(10) NOT NULL,
  `nl_od` varchar(50) NOT NULL,
  `l_od` varchar(50) NOT NULL,
  `m_od` varchar(50) NOT NULL,
  `h_od` varchar(50) NOT NULL,
  `nl_os` varchar(50) NOT NULL,
  `l_os` varchar(50) NOT NULL,
  `m_os` varchar(50) NOT NULL,
  `h_os` varchar(50) NOT NULL,
  `nl_ou` varchar(50) NOT NULL,
  `l_ou` varchar(50) NOT NULL,
  `m_ou` varchar(50) NOT NULL,
  `h_ou` varchar(50) NOT NULL,
  `ex_desc` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

$q[] = "
ALTER TABLE `chart_bat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bat_vis_id` (`id_chart_vis_master`);";
  
 $q[] = " 
  ALTER TABLE `chart_bat`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;";
  
  $q[] = "
  ALTER TABLE `chart_bat`
  ADD CONSTRAINT `bat_vis_id` FOREIGN KEY (`id_chart_vis_master`) REFERENCES `chart_vis_master` (`id`);";
  
  
  $q[] = "
  CREATE TABLE `chart_pam` (
  `id` int(10) NOT NULL,
  `id_chart_vis_master` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `uid` int(10) NOT NULL,
  `txt1_od` varchar(50) NOT NULL,
  `txt2_od` varchar(50) NOT NULL,
  `txt1_os` varchar(50) NOT NULL,
  `txt2_os` varchar(50) NOT NULL,
  `txt1_ou` varchar(50) NOT NULL,
  `txt2_ou` varchar(50) NOT NULL,
  `sel1` varchar(50) NOT NULL,
  `sel2` varchar(50) NOT NULL,
  `ex_desc` tinytext NOT NULL,
  `pam` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

$q[] = "
ALTER TABLE `chart_pam`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pam_vis_id` (`id_chart_vis_master`);";
  
 $q[] = " 
  ALTER TABLE `chart_pam`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;";
  
  $q[] = "
  ALTER TABLE `chart_pam`
  ADD CONSTRAINT `pam_vis_id` FOREIGN KEY (`id_chart_vis_master`) REFERENCES `chart_vis_master` (`id`);";

$q[] = " ALTER TABLE `chart_pc_mr` ADD id_chart_vis_master int(10) NOT NULL ";

$q[] = "
ALTER TABLE `chart_pc_mr`  
  ADD INDEX `id_chart_vis_master` (`id_chart_vis_master`);";
  
$q[] = "ALTER TABLE `chart_acuity` ADD INDEX `sec_name` (`sec_name`);";

$q[] = "ALTER TABLE `chart_acuity` ADD INDEX `sec_indx` (`sec_indx`);";

$q[] = "ALTER TABLE `chart_sca` ADD INDEX `sec_name` (`sec_name`);";

$q[] = "ALTER TABLE `chart_pc_mr` ADD INDEX `ex_type` (`ex_type`);";

$q[] = "ALTER TABLE `chart_pc_mr` ADD INDEX `ex_number` (`ex_number`);";


foreach($q as $k => $v){
	$result = imw_query($v) or $msg_info[] = imw_error();
}

if(!$result)
{
	$msg_info[] = '<br><br><b>Update 58 :: Update run FAILED!</b><br><br>';
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 58 :: Update run successfully!<br></b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 58 </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(implode("<br>",$msg_info));?>
</font>
</body>
</html>