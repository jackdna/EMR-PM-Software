<?php
$ignoreAuth = true;
include_once(dirname(__FILE__)."/../../../../config/globals.php");

$msg_info=array();

$s = array();

$s[] = "
CREATE TABLE `iportal_app_reqs` (
  `id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `appt_req_id` varchar(255) NOT NULL,
  `vis_ins_car` varchar(255) NOT NULL,
  `vis_grp_num` varchar(255) NOT NULL,
  `vis_pol_num` varchar(255) NOT NULL,
  `med_ins_car` varchar(255) NOT NULL,
  `med_grp_num` varchar(255) NOT NULL,
  `med_pol_num` varchar(255) NOT NULL,
  `req_date` varchar(255) NOT NULL,
  `comments` tinytext NOT NULL,
  `pt_email` varchar(255) NOT NULL,
  `phone_num_type` varchar(255) NOT NULL,
  `phone_num` varchar(255) NOT NULL,
  `app_status` varchar(255) NOT NULL,
  `app_req_rsn_Id` varchar(255) NOT NULL,
  `country_id` varchar(255) NOT NULL,
  `app_ext_id` varchar(255) NOT NULL,
  `doc_ext_id` varchar(255) NOT NULL,
  `loc_ext_id` varchar(255) NOT NULL,
  `valid_pt` int(2) NOT NULL,
  `aprv_dec` int(2) NOT NULL,
  `app_can_req_id` varchar(255) NOT NULL,
  `can_reason` tinytext NOT NULL,
  `operator_id` int(10) NOT NULL,
  `action_date_time` datetime NOT NULL,
  `created_on` datetime NOT NULL,
  `pt_ext_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

 ";

$s[] = "
ALTER TABLE `iportal_app_reqs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `app_ext_id` (`app_ext_id`),
  ADD KEY `app_can_req_id` (`app_can_req_id`),
  ADD KEY `appt_req_id` (`appt_req_id`) USING BTREE;
	";

$s[] = "
ALTER TABLE `iportal_app_reqs`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
";

foreach($s as $k=>$sql){
	$result = imw_query($sql) or $msg_info[] = imw_error();
}

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 1  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 1  run successfully!</b>";
    $color = "green";
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 3</title>
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
