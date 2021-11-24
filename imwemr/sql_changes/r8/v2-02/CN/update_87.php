<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$s = array();
$s[] = "
CREATE TABLE `chart_draw_inter_report` (
  `id` int(10) NOT NULL,
  `id_chart_draw` int(10) NOT NULL,
  `order_by` int(10) NOT NULL,
  `order_on` datetime NOT NULL,
  `test_type` varchar(30) NOT NULL,
  `assessment` text NOT NULL,
  `dx` text NOT NULL,
  `dxid` int(10) NOT NULL,
  `plan` text NOT NULL,
  `del_by` int(10) NOT NULL,
  `del_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

";


$s[] = "
ALTER TABLE `chart_draw_inter_report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_by` (`order_by`),
  ADD KEY `del_by` (`del_by`),
  ADD KEY `id_chart_draw` (`id_chart_draw`);
";

$s[] = "
ALTER TABLE `chart_draw_inter_report`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
";

foreach($s as $k=>$sql){
	$result = imw_query($sql) or $msg_info[] = imw_error();
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 87</title>
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