<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$s = array();

$s[] = "
CREATE TABLE `admn_lens_used` (
  `id` int(10) NOT NULL,
  `name` varchar(200) NOT NULL,
  `del_by` int(10) NOT NULL,
  `del_tm` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";

$s[] = "
ALTER TABLE `admn_lens_used`
  ADD PRIMARY KEY (`id`),
  ADD KEY `del_by` (`del_by`);
";

$s[] = "
ALTER TABLE `admn_lens_used`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
";

$s[] = "ALTER TABLE `chart_retinal_exam` ADD `lens_used` VARCHAR(100) NOT NULL AFTER `wnl_value_RetinalExam`;";
if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
$s[] = "ALTER TABLE `chart_retinal_exam_archive` ADD `lens_used` VARCHAR(100) NOT NULL AFTER `wnl_value_RetinalExam`;";
}

foreach($s as $k=>$sql){
	$result = imw_query($sql) or $msg_info[] = imw_error();
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 86</title>
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