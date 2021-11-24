<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include("../../common/conDb.php");

$msg_info = array();
$sql[] = " ALTER TABLE `surgerycenter` ADD `show_religion` TINYINT( 1 ) NOT NULL ;";
$sql[] = " ALTER TABLE `patient_data_tbl` ADD `religion` VARCHAR( 255 ) NOT NULL ;";
$sql[] = " ALTER TABLE `stub_tbl` ADD `patient_religion` VARCHAR( 255 ) NOT NULL ;";
$sql[] = " ALTER TABLE `surgerycenter` ADD `safety_check_list` TINYINT( 1 ) DEFAULT 1 NOT NULL ;";
$sql[] = " ALTER TABLE `patientconfirmation` ADD `show_checklist` VARCHAR( 2 ) NOT NULL;";
$sql[] = " ALTER TABLE `patient_allergies_tbl` ADD `iolink_pre_op_allergy_id` INT( 11 ) NOT NULL , ADD INDEX iolink_pre_op_allergy_id( iolink_pre_op_allergy_id );";
$sql[] = " ALTER TABLE `patient_prescription_medication_healthquest_tbl` ADD `iolink_prescription_medication_id` INT( 11 ) NOT NULL , ADD INDEX iolink_prescription_medication_id( iolink_prescription_medication_id );";
$sql[] = " ALTER TABLE `patient_anesthesia_medication_tbl` ADD `iolink_prescription_medication_id` INT( 11 ) NOT NULL , ADD INDEX iolink_prescription_medication_id( iolink_prescription_medication_id );";

$sql[] = " CREATE INDEX pa_multicol  ON patient_allergies_tbl(iolink_pre_op_allergy_id,allergy_name,reaction_name);";
$sql[] = " CREATE INDEX ipa_multicol ON iolink_patient_allergy(allergy_name,reaction_name);";

$sql[] = " ALTER TABLE `iolink_patient_prescription_medication` 
			CHANGE `prescription_medication_name` `prescription_medication_name` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
			CHANGE `prescription_medication_desc` `prescription_medication_desc` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
			CHANGE `prescription_medication_sig` `prescription_medication_sig` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL; ";
$sql[] = " CREATE INDEX ippm_multicol  ON iolink_patient_prescription_medication(prescription_medication_name,prescription_medication_desc,prescription_medication_sig);";

$sql[] = " ALTER TABLE `patient_prescription_medication_healthquest_tbl` 
			CHANGE `prescription_medication_name` `prescription_medication_name` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
			CHANGE `prescription_medication_desc` `prescription_medication_desc` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
			CHANGE `prescription_medication_sig` `prescription_medication_sig` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL; ";
$sql[] = " CREATE INDEX ppmh_multicol  ON patient_prescription_medication_healthquest_tbl(iolink_prescription_medication_id,prescription_medication_name,prescription_medication_desc,prescription_medication_sig);";

$sql[] = " ALTER TABLE `patient_anesthesia_medication_tbl` 
			CHANGE `prescription_medication_name` `prescription_medication_name` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
			CHANGE `prescription_medication_desc` `prescription_medication_desc` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
			CHANGE `prescription_medication_sig` `prescription_medication_sig` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL; ";
$sql[] = " CREATE INDEX pamt_multicol  ON patient_anesthesia_medication_tbl(iolink_prescription_medication_id,prescription_medication_name,prescription_medication_desc,prescription_medication_sig);";



foreach($sql as $qry){
	imw_query($qry) or $msg_info[] = imw_error();
}

// update Show_checklist to 1 for previous records
$qryChk = "Select show_checklist From patientconfirmation Where show_checklist <> '' ";
$sqlChk = imw_query($qryChk) or $msg_info[] = imw_error();
$cntChk = imw_num_rows($sqlChk);
if( $cntChk == 0 ) {
	$bkupTableName = "patientconfirmation_".date('Ymd');
	$qry1 = " CREATE TABLE ".$bkupTableName." LIKE patientconfirmation; ";
	imw_query($qry1) or $msg_info[] = imw_error();

	$qry2 = " INSERT ".$bkupTableName." SELECT * FROM patientconfirmation;";
	imw_query($qry2) or $msg_info[] = imw_error();

	$qry3 = " UPDATE patientconfirmation pc Set pc.show_checklist = '1' WHERE pc.show_checklist = '' ";
	imw_query($qry3) or $msg_info[] = imw_error();
}

$qryChk = "Select pre_op_allergy_id From patient_allergies_tbl Where iolink_pre_op_allergy_id <> '0' ";
$sqlChk = imw_query($qryChk) or $msg_info[] = imw_error();
$cntChkAllergy = imw_num_rows($sqlChk);
if( $cntChkAllergy == 0 ) {
	$bkupAllergyTableName = "patient_allergies_tbl_".date('Ymd');
	$qry4 = " CREATE TABLE ".$bkupAllergyTableName." LIKE patient_allergies_tbl; ";
	imw_query($qry4) or $msg_info[] = $qry4.imw_error();

	$qry5 = " INSERT INTO ".$bkupAllergyTableName." (SELECT * FROM patient_allergies_tbl);";
	imw_query($qry5) or $msg_info[] = $qry5.imw_error();

}

$qry6 = " 
UPDATE patient_allergies_tbl pa
INNER JOIN iolink_patient_allergy ipa ON pa.allergy_name = ipa.allergy_name
AND pa.reaction_name = ipa.reaction_name
AND pa.iolink_pre_op_allergy_id = '0'
INNER JOIN patientconfirmation pc ON pc.patientConfirmationId = pa.patient_confirmation_id
INNER JOIN stub_tbl st ON st.patient_confirmation_id = pc.patientConfirmationId
AND st.dos = pc.dos
AND ipa.patient_in_waiting_id = st.iolink_patient_in_waiting_id
INNER JOIN patient_in_waiting_tbl wt ON wt.patient_in_waiting_id = st.iolink_patient_in_waiting_id 
SET pa.iolink_pre_op_allergy_id = ipa.pre_op_allergy_id; ";
imw_query($qry6) or $msg_info[] = $qry6.imw_error();


$qryChkMedication = "Select prescription_medication_id From patient_prescription_medication_healthquest_tbl Where iolink_prescription_medication_id <> '0' ";
$sqlChkMedication = imw_query($qryChkMedication) or $msg_info[] = imw_error();
$cntChkMedication = imw_num_rows($sqlChkMedication);
if( $cntChkMedication == 0 ) {
	$bkupMedicationTableName = "patient_prescription_medication_healthquest_tbl_".date('Ymd');
	$qry7 = " CREATE TABLE ".$bkupMedicationTableName." LIKE patient_prescription_medication_healthquest_tbl; ";
	imw_query($qry7) or $msg_info[] = $qry7.imw_error();

	$qry8 = " INSERT INTO ".$bkupMedicationTableName." (SELECT * FROM patient_prescription_medication_healthquest_tbl);";
	imw_query($qry8) or $msg_info[] = $qry8.imw_error();
}

$qry9 = " 
UPDATE patient_prescription_medication_healthquest_tbl pp
INNER JOIN iolink_patient_prescription_medication ipp ON pp.prescription_medication_name = ipp.prescription_medication_name
AND pp.prescription_medication_desc = ipp.prescription_medication_desc
AND pp.prescription_medication_sig = ipp.prescription_medication_sig
AND pp.iolink_prescription_medication_id = '0'
INNER JOIN patientconfirmation pc ON pc.patientConfirmationId = pp.confirmation_id
INNER JOIN stub_tbl st ON st.patient_confirmation_id = pc.patientConfirmationId
AND st.dos = pc.dos
AND ipp.patient_in_waiting_id = st.iolink_patient_in_waiting_id
INNER JOIN patient_in_waiting_tbl wt ON wt.patient_in_waiting_id = st.iolink_patient_in_waiting_id 
SET pp.iolink_prescription_medication_id = ipp.prescription_medication_id";
imw_query($qry9) or $msg_info[] = $qry9.imw_error();


$qryChkMedicationHp = "Select prescription_medication_id From patient_anesthesia_medication_tbl Where iolink_prescription_medication_id <> '0' ";
$sqlChkMedicationHp = imw_query($qryChkMedicationHp) or $msg_info[] = imw_error();
$cntChkMedicationHp = imw_num_rows($sqlChkMedicationHp);
if( $cntChkMedicationHp == 0 ) {
	$bkupMedicationTableNameHp = "patient_anesthesia_medication_tbl_".date('Ymd');
	$qry10 = " CREATE TABLE ".$bkupMedicationTableNameHp." LIKE patient_anesthesia_medication_tbl; ";
	imw_query($qry10) or $msg_info[] = $qry10.imw_error();

	$qry11 = " INSERT INTO ".$bkupMedicationTableNameHp." (SELECT * FROM patient_anesthesia_medication_tbl);";
	imw_query($qry11) or $msg_info[] = $qry11.imw_error();
}

$qry12 = " 
UPDATE patient_anesthesia_medication_tbl pp 
INNER JOIN iolink_patient_prescription_medication ipp ON pp.prescription_medication_name = ipp.prescription_medication_name
AND pp.prescription_medication_desc = ipp.prescription_medication_desc
AND pp.prescription_medication_sig = ipp.prescription_medication_sig
AND pp.iolink_prescription_medication_id = '0'
INNER JOIN patientconfirmation pc ON pc.patientConfirmationId = pp.confirmation_id
INNER JOIN stub_tbl st ON st.patient_confirmation_id = pc.patientConfirmationId
AND st.dos = pc.dos
AND ipp.patient_in_waiting_id = st.iolink_patient_in_waiting_id
INNER JOIN patient_in_waiting_tbl wt ON wt.patient_in_waiting_id = st.iolink_patient_in_waiting_id 
SET pp.iolink_prescription_medication_id = ipp.prescription_medication_id; ";
imw_query($qry12) or $msg_info[] = $qry12.imw_error();


$qry6dropindex1 = " ALTER TABLE patient_allergies_tbl DROP INDEX pa_multicol; ";
imw_query($qry6dropindex1) or $msg_info[] = imw_error();

$qry6dropindex2 = " ALTER TABLE iolink_patient_allergy DROP INDEX ipa_multicol; ";
imw_query($qry6dropindex2) or $msg_info[] = imw_error();


$qry9dropindex1 = " ALTER TABLE iolink_patient_prescription_medication DROP INDEX ippm_multicol; ";
imw_query($qry9dropindex1) or $msg_info[] = imw_error();

$qry9dropindex2 = " ALTER TABLE patient_prescription_medication_healthquest_tbl DROP INDEX ppmh_multicol; ";
imw_query($qry9dropindex2) or $msg_info[] = imw_error();

$qry12dropindex2 = " ALTER TABLE patient_anesthesia_medication_tbl DROP INDEX pamt_multicol; ";
imw_query($qry12dropindex2) or $msg_info[] = imw_error();

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 175 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 175 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 175</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
imw_close();
}
?> 
</body>
</html>