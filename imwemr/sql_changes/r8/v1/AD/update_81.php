<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();
$elec_num=imw_query("select code from elect_report_type where code='03'");
if(imw_num_rows($elec_num)==0){
	imw_query("TRUNCATE TABLE elect_report_type");
	imw_query("insert into elect_report_type (code,code_desc) values
	('03','Report Justifying Treatment Beyond Utilization Guidelines'),
	('04','Drugs Administered'),
	('05','Treatment Diagnosis'),
	('06','Initial Assessment'),
	('07','Functional Goals'),
	('08','Plan of Treatment'),
	('09','Progress Report'),
	('10','Continued Treatment'),
	('11','Chemical Analysis'),
	('13','Certified Test Report'),
	('15','Justification for Admission'),
	('21','Recovery Plan'),
	('48','Social Security Benefit Letter'),
	('55','Rental Agreement'),
	('59','Benefit Letter'),
	('77','Support Data for Verification'),
	('A3','Allergies or Sensitivities Document'),
	('A4','Autopsy Report'),
	('AM','Ambulance Certification'),
	('AS','Admission Summary'),
	('AT','Purchase Order Attachment'),
	('B2','Prescription'),
	('B3','Physician Order'),
	('B4','Referral Form'),
	('BR','Benchmark Testing Results'),
	('BS','Baseline'),
	('BT','Blanket Test Results'),
	('CB','Chiropractic Justification'),
	('CK','Consent Form'),
	('CT','Certification'),
	('D2','Drug Profile Document'),
	('DA','Dental Models'),
	('DB','Durable Medical Equipment Prescription'),
	('DG','Diagnostic Report'),
	('DJ','Discharge Monitoring Report'),
	('DS','Discharge Summary'),
	('EB','Explanation of Benefits Coordination of Benefits or Medicare Secondary Payor'),
	('FM','Family Medical History Document'),
	('HC','Health Certificate'),
	('HR','Health Clinic Records'),
	('15','Immunization Record'),
	('IR','State School Immunization Records'),
	('LA','Laboratory Results'),
	('M1','Medical Record Attachment'),
	('MT','Models'),
	('NN','Nursing Notes'),
	('OB','Operative Note'),
	('OC','Oxygen Content Averaging Report'),
	('OD','Orders and Treatments Document'),
	('OE','Objective Physical Examination including vital signs Document'),
	('OX','Oxygen Therapy Certification'),
	('OZ','Support Data for Claim'),
	('P4','Pathology Report'),
	('P5','Patient Medical History Document'),
	('P6','Periodontal Charts'),
	('P7','Periodontal Reports'),
	('PE','Parenteral or Enteral Certification'),
	('PN','Physical Therapy Notes'),
	('PO','Prosthetics or Orthotic Certification'),
	('PQ','Paramedical Results'),
	('PY','Physician Report'),
	('PZ','Physical Therapy Certification'),
	('QC','Cause and Corrective Action Report'),
	('QR','Quality Report'),
	('RB','Radiology Films'),
	('RR','Radiology Reports'),
	('RT','Report of Tests and Analysis Report'),
	('RX','Renewable Oxygen Content Averaging Report'),
	('SG','Symptoms Document'),
	('V5','Death Notification'),
	('XP','Photographs')") or $msg_info[] = imw_error();
}


/*************************************/
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 81 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 81 completed successfully.</b>";
	$color = "green";
}


?>
<html>
<head>
<title>Update 81</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>