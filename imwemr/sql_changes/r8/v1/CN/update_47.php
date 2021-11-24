<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);

$q = array();
$q[] = '
	CREATE TABLE `chart_vis_lasik` (
  `id` int(10) NOT NULL,
  `method` varchar(50) NOT NULL,
  `date_lasik` date NOT NULL,
  `time_lasik` varchar(10) NOT NULL,
  `intervention` varchar(200) NOT NULL,
  `microkeratome` varchar(255) NOT NULL,
  `laser_excimer` varchar(200) NOT NULL,
  `laser_mode` varchar(200) NOT NULL,
  `laser_optical_zone` varchar(100) NOT NULL,
  `target` text NOT NULL,
  `laser` text NOT NULL,
  `user_id` int(10) NOT NULL,
  `date_op` date NOT NULL,
  `form_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL
) ENGINE=MyISAM;
';

$q[] = '
	ALTER TABLE `chart_vis_lasik`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);
';

$q[] = '
	ALTER TABLE `chart_vis_lasik`
	MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';

$q[] = '
	CREATE TABLE `chart_vis_lasik_options` (
  `id` int(10) NOT NULL,
  `op_name` varchar(200) NOT NULL,
  `op_type` text NOT NULL,
  `del_by` int(10) NOT NULL
) ENGINE=MyISAM;
';

$q[] = '
	ALTER TABLE `chart_vis_lasik_options`
  ADD PRIMARY KEY (`id`);
';

$q[] = '
	ALTER TABLE `chart_vis_lasik_options`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';

foreach($q as $qry)
    imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 47 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 47 completed successfully. </b>";
	$color = "green";	
	
}
?>
<html>
<head>
<title>Update 47</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>