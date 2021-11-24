<?php
//--- UPDATE CREATED BY ---
set_time_limit(0);
$ignoreAuth = true;
include("../../../../config/globals.php");
////

$q="
CREATE TABLE `chart_pt_assessment_plans` (
  `id` int(10) NOT NULL,
  `id_chart_ap` int(10) NOT NULL,
  `id_pt_problem_list` int(10) NOT NULL,
  `not_examin` int(2) NOT NULL,
  `resolve` int(2) NOT NULL,
  `assessment` text NOT NULL,
  `plan` mediumtext NOT NULL,
  `eye` varchar(5) NOT NULL,
  `create_by` int(10) NOT NULL,
  `create_time` datetime NOT NULL,
  `delete_by` int(10) NOT NULL,
  `delete_time` datetime NOT NULL,
  `modify_by` int(10) NOT NULL,
  `modify_time` datetime NOT NULL
) ENGINE=MyISAM; 
";
imw_query($q) or $msg_info[] = imw_error();

$q="
ALTER TABLE `chart_pt_assessment_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_chart_ap` (`id_chart_ap`),
  ADD KEY `id_pt_problem_list` (`id_pt_problem_list`);
 ";
imw_query($q) or $msg_info[] = imw_error();

$q="  
  ALTER TABLE `chart_pt_assessment_plans`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
";
imw_query($q) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 28 run FAILED!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 28 :: Update run successfully!";
	$color = "green";
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 28</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>

</body>
</html>