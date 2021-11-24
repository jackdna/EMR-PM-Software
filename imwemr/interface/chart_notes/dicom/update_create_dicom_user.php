<?php
set_time_limit(0);
$ignoreAuth = true;
require_once(dirname(__FILE__).'/../../../config/globals.php');

$sql = "select fname from users where fname = 'DICOM' and lname = 'DICOM' ";
$row = sqlQuery($sql);
if($row!=false && !empty($row["fname"])){
	//
}else{

$sql = "
INSERT INTO `users` (`id`, `user_group_id`, `username`, `password`, `authorized`, `info`, `source`, `fname`, `mname`, `lname`, `federaltaxid`, `federaldrugid`, `upin`, `facility`, `see_auth`, `provider_color`, `licence`, `additional_info`, `user_type`, `gro_id`, `default_group`, `access_pri`, `default_facility`, `max_appoint`, `user_npi`, `TaxonomyId`, `MedicareId`, `MedicaidId`, `BlueShieldId`, `TaxId`, `schedule_warnings`, `superuser`, `locked`, `passwordChanged`, `loginAttempts`, `passCreatedOn`, `HIPPA_STATUS`, `hippa_date`, `SLA`, `sla_date`, `eRx_user_name`, `erx_password`, `pro_title`, `pro_suffix`, `eRx_facility_id`, `Enable_Scheduler`, `athenaID`, `StopOverBooking`, `sign`, `optical_enable`, `passwordReset`, `session_timeout`, `created_on`, `created_by`, `modified_on`, `modified_by`, `delete_status`, `collect_refraction`, `sch_facilities`, `max_day`, `max_per`, `initials`, `department`, `external_id`, `sign_sigplus_path`) VALUES
(NULL, 5, 'imdicom', 'd422b1e9d8b03d2fda97e2e4655ae13ec4fd58b8b104bfc364f29c89c22db496', NULL, NULL, NULL, 'DICOM', '', 'DICOM', NULL, '', '', NULL, 1, '0', '', '', '3', '', '', 'a:35:{s:17:&quot;priv_cl_work_view&quot;;i:1;s:13:&quot;priv_cl_tests&quot;;i:1;s:18:&quot;priv_cl_medical_hx&quot;;i:1;s:15:&quot;priv_Front_Desk&quot;;i:0;s:12:&quot;priv_Billing&quot;;i:0;s:15:&quot;priv_Accounting&quot;;i:1;s:12:&quot;priv_Acc_all&quot;;i:0;s:14:&quot;priv_Acc_vonly&quot;;i:0;s:13:&quot;priv_Security&quot;;i:0;s:17:&quot;priv_sc_scheduler&quot;;i:0;s:19:&quot;priv_sc_house_calls&quot;;i:0;s:26:&quot;priv_sc_recall_fulfillment&quot;;i:0;s:18:&quot;priv_bi_front_desk&quot;;i:0;s:14:&quot;priv_bi_ledger&quot;;i:0;s:20:&quot;priv_bi_prod_payroll&quot;;i:0;s:10:&quot;priv_bi_ar&quot;;i:0;s:18:&quot;priv_bi_statements&quot;;i:0;s:18:&quot;priv_bi_end_of_day&quot;;i:0;s:16:&quot;priv_cl_clinical&quot;;i:0;s:14:&quot;priv_cl_visits&quot;;i:0;s:11:&quot;priv_cl_ccd&quot;;i:0;s:17:&quot;priv_cl_order_set&quot;;i:0;s:16:&quot;priv_vo_clinical&quot;;i:0;s:15:&quot;priv_vo_pt_info&quot;;i:0;s:11:&quot;priv_vo_acc&quot;;i:0;s:15:&quot;priv_vo_charges&quot;;i:0;s:15:&quot;priv_vo_payment&quot;;i:0;s:17:&quot;priv_Sch_Override&quot;;i:0;s:16:&quot;priv_pt_Override&quot;;i:0;s:10:&quot;priv_admin&quot;;i:0;s:12:&quot;priv_Optical&quot;;i:0;s:11:&quot;priv_iOLink&quot;;i:0;s:16:&quot;priv_break_glass&quot;;i:0;s:20:&quot;priv_edit_financials&quot;;i:0;s:15:&quot;priv_no_reports&quot;;i:0;}', '', '', '', '', '', '', '', '', 'disallow', 'no', 0, 0, 0, CURDATE(), 'no', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '', '', '', '', '', 0, 0, 'No', '0-0-0:;', '', 0, '30MI', CURDATE(), 1, CURDATE(), 1, 0, 0, '1;32;33;34;35;36;37;38;39;40;41;42;43;44', '', '', '', '', '', '');

";
$result = sqlQuery($sql);

}

if(!$result)
{
	$msg_info[] = '<br><br><b>Update create dicom user :: Update run FAILED!</b><br><br>';
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update create dicom user :: Update run successfully!<br></b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update create dicom user :: </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(implode("<br>",$msg_info));?>
</font>
</body>
</html>