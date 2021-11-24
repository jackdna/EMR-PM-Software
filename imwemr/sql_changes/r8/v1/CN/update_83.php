<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$s = array();
$s[] = "CREATE TABLE `med_proc_dis_setting` (
  `id` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `proc_display` varchar(20) NOT NULL,
  `del_by` int(10) NOT NULL,
  `del_tm` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

$s[] = "ALTER TABLE `med_proc_dis_setting`
  ADD PRIMARY KEY (`id`);";
  
$s[] = "ALTER TABLE `med_proc_dis_setting`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;";  

foreach($s as $k=>$sql){
	$result = imw_query($sql) or $msg_info[] = imw_error();
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 83</title>
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