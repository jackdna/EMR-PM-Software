<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql[] = "ALTER TABLE `surgery_consent_form` ADD INDEX confirmation_id ( `confirmation_id` );";
$sql[] = "ALTER TABLE `patient_prescription_medication_healthquest_tbl` ADD INDEX confirmation_id( `confirmation_id` ) ;";
$sql[] = "ALTER TABLE `healthquestionadmin` ADD INDEX confirmation_id( `confirmation_id` );"; 
$sql[] = "ALTER TABLE `patient_postop_nurse_checklist` ADD INDEX confirmation_id( `confirmation_id` );";
$sql[] = "ALTER TABLE `qualitymeasures` ADD INDEX confirmation_id( `confirmation_id` );";
$sql[] = "ALTER TABLE `patient_data_tbl` ADD INDEX asc_id( `asc_id` );";
$sql[] = "ALTER TABLE `stub_tbl` ADD INDEX grp_stub( dos,patient_first_name,patient_middle_name,patient_last_name,patient_dob,surgery_time );";
$sql[] = "ALTER TABLE `surgeonprofile` ADD INDEX surgeonId( `surgeonId` );";
$sql[] = "ALTER TABLE `surgeonprofileprocedure` ADD INDEX profileId( `profileId` );";
$sql[] = "ALTER TABLE `eposted` ADD INDEX grp_epost( table_name, patient_conf_id);";
$sql[] = "ALTER TABLE `users` ADD INDEX user_type( `user_type` );";
$sql[] = "ALTER TABLE `genanesthesianursesnewnotes` ADD INDEX confirmation_id( `confirmation_id` );";
$sql[] = "ALTER TABLE `patient_prescription_medication_tbl` ADD INDEX confirmation_id( `confirmation_id` );";
$sql[] = "ALTER TABLE `superbill_tbl` ADD INDEX confirmation_id( `confirmation_id` );";
$sql[] = "ALTER TABLE `superbill_tbl` ADD INDEX deleted( `deleted` );";
$sql[] = "ALTER TABLE `superbill_tbl` ADD INDEX bill_user_type( `bill_user_type` );";
$sql[] = "ALTER TABLE `procedurescategory` ADD INDEX name( `name` );";
$sql[] = "ALTER TABLE `procedurescategory` ADD INDEX del_status( `del_status` );";
$sql[] = "ALTER TABLE `surgeonprofile` ADD INDEX defaultProfile( `defaultProfile` );";
$sql[] = "ALTER TABLE `surgeonprofile` ADD INDEX del_status( `del_status` );";
$sql[] = "ALTER TABLE `tblprogress_report` ADD INDEX confirmation_id( `confirmation_id` );";
$sql[] = "ALTER TABLE `tblprogress_report` ADD INDEX usersId( `usersId` );";
$sql[] = "ALTER TABLE `dischargesummarysheet` ADD INDEX confirmation_id( `confirmation_id` );";
$sql[] = "ALTER TABLE `operatingroomrecords` ADD INDEX patient_id( `patient_id` );";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error().'<br>'.$qry;
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 157 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 157 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 157</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
@imw_close();
}
?> 
</body>
</html>