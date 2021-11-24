<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = array();

$qry[] = "
CREATE TABLE `ccda_export_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_id` text NOT NULL,
  `provider_id` text NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `schedule_type` varchar(255) NOT NULL,
  `schedule_date_time` datetime NOT NULL,
  `reoccurring_time_period` varchar(255) NOT NULL,
  `reoccurring_day_num` int(11) NOT NULL,
  `reoccurring_day_week` varchar(255) NOT NULL,
  `reoccurring_time` time NOT NULL,
  `operator_id` int(11) NOT NULL,
  `operator_date_time` datetime NOT NULL,
  `delete_status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ;
";

foreach($qry as $q){imw_query($q) or $msg_info[] = imw_error();}



if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 18 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 18 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 18</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>