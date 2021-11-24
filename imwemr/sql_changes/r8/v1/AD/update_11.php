<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = "
CREATE TABLE `chart_hpi` (
  `id` int(10) NOT NULL,
  `hpi` varchar(250) NOT NULL,
  `cat` int(2) NOT NULL,
  `op_by` int(10) NOT NULL,
  `op_time` datetime NOT NULL,
  `del` int(2) NOT NULL
) ENGINE=MyISAM
";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "
INSERT INTO `chart_hpi` (`id`, `hpi`, `cat`, `op_by`, `op_time`, `del`) VALUES
(1, 'Vision Problem', 0, 1, CURRENT_TIME(), 0),
(2, 'Irritation', 0, 1, CURRENT_TIME(), 0),
(3, 'Post Segment', 0, 1, CURRENT_TIME(), 0),
(4, 'Neuro', 0, 1, CURRENT_TIME(), 0),
(5, 'Follow-up', 0, 1, CURRENT_TIME(), 0),
(6, 'Distance', 1, 1, CURRENT_TIME(), 0),
(7, 'Near', 1, 1, CURRENT_TIME(), 0),
(8, 'Glare', 1, 1, CURRENT_TIME(), 0),
(9, 'Mid Distance', 1, 1, CURRENT_TIME(), 0),
(10, 'Other', 1, 1, CURRENT_TIME(), 0),
(11, 'Lids - External', 2, 1, CURRENT_TIME(), 0),
(12, 'Ocular', 2, 1, CURRENT_TIME(), 0),
(13, 'Flashing Lights', 3, 1, CURRENT_TIME(), 0),
(14, 'Floaters', 3, 1, CURRENT_TIME(), 0),
(15, 'Amsler Grid', 3, 1, CURRENT_TIME(), 0),
(16, 'Double Vision', 4, 1, CURRENT_TIME(), 0),
(17, 'Temporal Arteritis Symptoms', 4, 1, CURRENT_TIME(), 0),
(18, 'Headaches', 4, 1, CURRENT_TIME(), 0),
(19, 'Migraine Headaches', 4, 1, CURRENT_TIME(), 0),
(20, 'Loss of Vision', 4, 1, CURRENT_TIME(), 0),
(21, 'Post-op', 5, 1, CURRENT_TIME(), 0),
(22, 'Follow-up', 5, 1, CURRENT_TIME(), 0);
";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "
ALTER TABLE `chart_hpi`
  ADD PRIMARY KEY (`id`);
";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "
ALTER TABLE `chart_hpi`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
";
imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 11 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 11 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 11</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>