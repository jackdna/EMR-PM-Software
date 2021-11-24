<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);

$q = array();
$q[] = '
CREATE TABLE `chart_proc_lasik` (
  `id` int(10) NOT NULL,
  `chart_proc_id` int(10) NOT NULL,
  `pre_op_tech` int(10) NOT NULL,
  `near_eye` varchar(10) NOT NULL,
  `allergies` text NOT NULL,
  `xanax` varchar(50) NOT NULL,
  `pre_op_checks` text NOT NULL,
  `lasik_modifier` text NOT NULL,
  `lasik_eye` varchar(10) NOT NULL,
  `dos_mr_od` varchar(255) NOT NULL,
  `dos_mr_os` varchar(255) NOT NULL,
  `post_op_target_od` varchar(255) NOT NULL,
  `post_op_target_os` varchar(255) NOT NULL,
  `avg_k_axis_od` varchar(255) NOT NULL,
  `avg_k_axis_os` varchar(255) NOT NULL,
  `treatment1_od` varchar(255) NOT NULL,
  `treatment1_os` varchar(255) NOT NULL,
  `pachy_od` varchar(255) NOT NULL,
  `pachy_os` varchar(255) NOT NULL,
  `flap_thick_od` varchar(255) NOT NULL,
  `flap_thick_os` varchar(255) NOT NULL,
  `stromal_bed_od` varchar(255) NOT NULL,
  `stromal_bed_os` varchar(255) NOT NULL,
  `keratome_od` varchar(255) NOT NULL,
  `keratome_os` varchar(255) NOT NULL,
  `risks_benefits` int(1) NOT NULL,
  `surgeon_sign` varchar(255) NOT NULL,
  `surgeon_sign_dos` datetime NOT NULL,
  `abrasion` varchar(10) NOT NULL,
  `bcl` varchar(10) NOT NULL,
  `post_op_type` varchar(255) NOT NULL,
  `drops` text NOT NULL,
  `temperature` varchar(20) NOT NULL,
  `humidity` varchar(20) NOT NULL,
  `keratome_tech` int(10) NOT NULL,
  `laser_operator` int(10) NOT NULL,
  `post_op_surgeon` int(10) NOT NULL,
  `cornea_check_od` varchar(255) NOT NULL,
  `cornea_check_os` varchar(255) NOT NULL,
  `plugs_inserted` varchar(255) NOT NULL,
  `plugs_inserted_eye` varchar(10) NOT NULL,
  `plugs_inserted_size` varchar(50) NOT NULL,
  `post_op_kit_given` int(1) NOT NULL,
  `post_op_tech` int(10) NOT NULL,
  `comments` text NOT NULL,
  `del_by` int(10) NOT NULL,
  `del_time` datetime NOT NULL
) ENGINE=MyISAM;
	
';

$q[] = 'ALTER TABLE `chart_proc_lasik`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chart_proc_id` (`chart_proc_id`),
  ADD KEY `keratome_tech` (`keratome_tech`),
  ADD KEY `laser_operator` (`laser_operator`),
  ADD KEY `post_op_surgeon` (`post_op_surgeon`),
  ADD KEY `post_op_tech` (`post_op_tech`);';
  
 $q[] = 'ALTER TABLE `chart_proc_lasik`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;'; 
  
 $q[] = '
	CREATE TABLE `chart_lasik_pt_verify` (
  `id` int(10) NOT NULL,
  `id_chart_proc_lasik` int(10) NOT NULL,
  `item` varchar(255) NOT NULL,
  `surgeon` int(10) NOT NULL,
  `tech` int(10) NOT NULL,
  `veri_time` varchar(10) NOT NULL
) ENGINE=MyISAM
 '; 
 
 $q[] = '
	ALTER TABLE `chart_lasik_pt_verify`
  ADD PRIMARY KEY (`id`),
  ADD KEY `surgeon` (`surgeon`),
  ADD KEY `tech` (`tech`);
 ';
 
 $q[] = '
	ALTER TABLE `chart_lasik_pt_verify`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
 ';
 
foreach($q as $qry)
    imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 43 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 43 completed successfully. </b>";
	$color = "green";
	
	
}
?>
<html>
<head>
<title>Update 43</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>