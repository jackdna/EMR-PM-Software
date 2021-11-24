<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include("../../common/conDb.php");

$sql[] = "ALTER TABLE `patientpreopmedication_tbl` ADD INDEX patient_confirmation_id (patient_confirmation_id);";
$sql[] = "ALTER TABLE `patientpreopmedication_tbl` ADD INDEX preOpPhyOrderId(preOpPhyOrderId);";
$sql[] = "ALTER TABLE `patientpreopmedication_tbl` ADD INDEX medicationName(medicationName);";
$sql[] = "ALTER TABLE `patientpreopmedication_tbl` ADD INDEX strength(strength);";
$sql[] = "ALTER TABLE `patientpreopmedication_tbl` ADD INDEX direction(direction);";
$sql[] = "ALTER TABLE `patientpreopmedication_tbl` ADD INDEX sourcePage(sourcePage);";
$sql[] = "ALTER TABLE `procedureprofile` ADD INDEX procedureId(procedureId);";
$sql[] = "ALTER TABLE `laser_procedure_template` ADD INDEX laser_procedureID(laser_procedureID);";
$sql[] = "ALTER TABLE `patient_physician_orders` ADD INDEX confirmation_id(confirmation_id);";
$sql[] = "ALTER TABLE `patient_physician_orders` ADD INDEX chartName(chartName);";
$sql[] = "ALTER TABLE `patient_physician_orders` ADD INDEX physician_order_name(physician_order_name);";
$sql[] = "ALTER TABLE `stub_tbl` ADD INDEX surgeon_fname(surgeon_fname);";
$sql[] = "ALTER TABLE `stub_tbl` ADD INDEX surgeon_lname(surgeon_lname);";
$sql[] = "ALTER TABLE `stub_tbl` ADD INDEX patient_status(patient_status);";
$sql[] = "ALTER TABLE `patientconfirmation` ADD INDEX prim_proc_is_misc(prim_proc_is_misc);";
$sql[] = "ALTER TABLE `stub_tbl` ADD INDEX iasc_facility_id(iasc_facility_id);";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 170 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 170 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 170</title>
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