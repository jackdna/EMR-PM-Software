<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql="
CREATE TABLE `chart_vis_ext_mr` (
	`id` int(11) NOT NULL,
	`patient_id` bigint(20) NOT NULL,
	`ext_dos` date NOT NULL,
	`ext_mr_od_s` varchar(50) NOT NULL,
	`ext_mr_od_c` varchar(50) NOT NULL,
	`ext_mr_od_a` varchar(50) NOT NULL,
	`ext_mr_od_txt1` varchar(50) NOT NULL,
	`ext_mr_od_add` varchar(255) NOT NULL,
	`ext_mr_od_txt2` varchar(50) NOT NULL,
	`ext_mr_od_gl_ph` varchar(50) NOT NULL,
	`ext_mr_od_gl_ph_txt` varchar(50) NOT NULL,
	`ext_mr_od_p` varchar(50) NOT NULL,
	`ext_mr_od_sel_1` varchar(50) NOT NULL,
	`ext_mr_od_slash` varchar(50) NOT NULL,
	`ext_mr_od_prism` varchar(50) NOT NULL,
	`ext_mr_os_s` varchar(50) NOT NULL,
	`ext_mr_os_c` varchar(50) NOT NULL,
	`ext_mr_os_a` varchar(50) NOT NULL,
	`ext_mr_os_txt1` varchar(50) NOT NULL,
	`ext_mr_os_add` varchar(255) NOT NULL,
	`ext_mr_os_txt2` varchar(50) NOT NULL,
	`ext_mr_os_gl_ph` varchar(50) NOT NULL,
	`ext_mr_os_gl_ph_txt` varchar(50) NOT NULL,
	`ext_mr_os_p` varchar(50) NOT NULL,
	`ext_mr_os_sel_1` varchar(50) NOT NULL,
	`ext_mr_os_slash` varchar(50) NOT NULL,
	`ext_mr_os_prism` varchar(50) NOT NULL,
	`ext_mr_desc` text NOT NULL,
	`ext_mr_prism_desc` text NOT NULL,
	`entered_date_time` datetime NOT NULL,
	`prescribed_by` varchar(255) NOT NULL,
	`del_status` int(2) NOT NULL DEFAULT 0 COMMENT '0=Active, 1=Inactive',
	`del_date_time` datetime NOT NULL,
	`del_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";
imw_query($sql) or $msg_info[] = imw_error();

$sql="
ALTER TABLE `chart_vis_ext_mr`
  ADD PRIMARY KEY (`id`);
";
imw_query($sql) or $msg_info[] = imw_error();

$sql="
ALTER TABLE `chart_vis_ext_mr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
";
imw_query($sql) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 124  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 124  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 124- Add External Vision</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>