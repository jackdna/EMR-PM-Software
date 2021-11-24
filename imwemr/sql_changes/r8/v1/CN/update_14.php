<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$q = array();
$msg_info = array();
 
$q[] = "

CREATE TABLE `chart_pc_mr` (
  `id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `exam_date` datetime NOT NULL,
  `provider_id` int(10) NOT NULL,
  `ex_type` varchar(20) NOT NULL,
  `ex_number` int(10) NOT NULL,
  `pc_distance` int(2) NOT NULL,
  `pc_near` int(2) NOT NULL,
  `mr_none_given` varchar(50) NOT NULL,
  `mr_cyclopegic` int(2) NOT NULL,
  `mr_pres_date` date NOT NULL,
  `mr_ou_txt_1` varchar(50) NOT NULL,
  `mr_type` varchar(20) NOT NULL,
  `ex_desc` text NOT NULL,
  `prism_desc` text NOT NULL,
  `delete_by` int(10) NOT NULL
) ENGINE=MyISAM;

";

$q[] = "
	ALTER TABLE `chart_pc_mr`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `provider_id` (`provider_id`);

";

$q[] = "
	ALTER TABLE `chart_pc_mr`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
";


$q[] = "
	CREATE TABLE `chart_pc_mr_values` (
  `id` int(10) NOT NULL,
  `chart_pc_mr_id` int(10) NOT NULL,
  `site` varchar(5) NOT NULL,
  `sph` varchar(20) NOT NULL,
  `cyl` varchar(20) NOT NULL,
  `axs` varchar(20) NOT NULL,
  `ad` varchar(20) NOT NULL,
  `prsm_p` varchar(20) NOT NULL,
  `prism` varchar(20) NOT NULL,
  `slash` varchar(20) NOT NULL,
  `sel_1` varchar(20) NOT NULL,
  `sel_2` varchar(20) NOT NULL,
  `ovr_s` varchar(20) NOT NULL,
  `ovr_c` varchar(20) NOT NULL,
  `ovr_v` varchar(20) NOT NULL,
  `ovr_a` varchar(20) NOT NULL,
  `txt_1` varchar(20) NOT NULL,
  `txt_2` varchar(20) NOT NULL,
  `sel2v` varchar(20) NOT NULL
) ENGINE=MyISAM;
";


$q[] = "
ALTER TABLE `chart_pc_mr_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chart_pc_mr_id` (`chart_pc_mr_id`);
";


$q[] = "
	ALTER TABLE `chart_pc_mr_values`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
";

$q[] = " ALTER TABLE `chart_pc_mr` ADD `uid` INT(10) NOT NULL AFTER `prism_desc`; ";

$q[] = " ALTER TABLE `chart_pc_mr` ADD INDEX(`uid`); ";


foreach($q as $qry){
	imw_query($qry) or $msg_info[]=imw_error();
}


if(count($msg_info)>0)
{
	$msg_info[] = '<br><br><b>Update 14  run FAILED!</b><br>';
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 14  run successfully!</b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 14 (CN)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>