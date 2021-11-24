<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$s = array();
$s[] = "
CREATE TABLE `proc_amendments` (
  `id` int(10) NOT NULL,
  `id_chart_procedures` int(10) NOT NULL,
  `amndmnt` text NOT NULL,
  `final` int(2) NOT NULL,
  `final_by` int(10) NOT NULL,
  `final_on` datetime NOT NULL,
  `sign` varchar(250) NOT NULL,
  `sign_on` datetime NOT NULL,
  `op_dt` datetime NOT NULL,
  `op_id` int(10) NOT NULL,
  `del_by` int(2) NOT NULL,
  `sign_by` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";

$s[] = "
ALTER TABLE `proc_amendments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_chart_procedures` (`id_chart_procedures`);
";

$s[] = "
ALTER TABLE `proc_amendments`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
";

$s[] = "
ALTER TABLE `proc_amendments`
  ADD CONSTRAINT `id_chart_procedures` FOREIGN KEY (`id_chart_procedures`) REFERENCES `chart_procedures` (`id`);
";

$s[] = "
ALTER TABLE `chart_procedures` ADD `auto_final` INT(2) NOT NULL AFTER `Finalized_date`;
";

foreach($s as $k=>$sql){
	$result = imw_query($sql) or $msg_info[] = imw_error();
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 88</title>
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