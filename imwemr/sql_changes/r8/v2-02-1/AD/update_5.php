<?php 
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql2= "ALTER TABLE `patient_data` ADD `erp_patient_id` VARCHAR(200) NOT null";
imw_query($sql2) or $msg_info[] = imw_error();
$sql3= "ALTER TABLE `patient_data` ADD `erp_username` VARCHAR(200) NOT null";
imw_query($sql3) or $msg_info[] = imw_error();
$sql4= "ALTER TABLE `patient_data` ADD `erp_password` VARCHAR(200) NOT null";
imw_query($sql4) or $msg_info[] = imw_error();
$sql5= "ALTER TABLE `patient_data` ADD `erp_pt_contact_id` VARCHAR(200) NOT null";
imw_query($sql5) or $msg_info[] = imw_error();
$sql6= "ALTER TABLE `patient_data` ADD `erp_pt_email_id` VARCHAR(200) NOT null";
imw_query($sql6) or $msg_info[] = imw_error();
$sql7= "ALTER TABLE `patient_data` ADD `erp_pt_cellph_id` VARCHAR(200) NOT null";
imw_query($sql7) or $msg_info[] = imw_error();
$sql8= "ALTER TABLE `patient_data` ADD `erp_pt_bizph_id` VARCHAR(200) NOT null";
imw_query($sql8) or $msg_info[] = imw_error();
$sql9= "ALTER TABLE `patient_data` ADD `erp_pt_homeph_id` VARCHAR(200) NOT null";
imw_query($sql9) or $msg_info[] = imw_error();
$sql10= "ALTER TABLE `patient_data` ADD `erp_pt_postaladd_id` VARCHAR(200) NOT null";
imw_query($sql10) or $msg_info[] = imw_error();
$sql11= "ALTER TABLE `patient_data` ADD `erp_pt_comm_category_id` VARCHAR(200) NOT null";
imw_query($sql11) or $msg_info[] = imw_error();

$sql12= "ALTER TABLE `resp_party` ADD `erp_resp_username` VARCHAR(200) NOT null";
imw_query($sql12) or $msg_info[] = imw_error();
$sql13= "ALTER TABLE `resp_party` ADD `erp_resp_password` VARCHAR(200) NOT null";
imw_query($sql13) or $msg_info[] = imw_error();

$sql14= "ALTER TABLE `patient_multi_address` ADD `erp_pt_multiadd_id` VARCHAR(200) NOT null";
imw_query($sql14) or $msg_info[] = imw_error();



if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 5 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 5 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 5</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>