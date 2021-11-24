<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
//UPTO update94.php AMENDMENTS IN DATABASE
set_time_limit(700);
include_once("common/conDb.php");

$oldPatientId = '3133';
//$newPatientId='3318';

if($oldPatientId && $newPatientId) {
	
	//START CODE TO REPLACE NEW PATIENT-ID WITH OLD ONE
	$updateTableArr = array('amendment','chartnotes_change_audit_tbl','consent_sub_docs','eposted',
						    'genanesthesianursesnewnotes','genanesthesianursesnotes','genanesthesiarecord',
							'gen_nursenotes_intake_room','gen_nursenotes_recovery_meds','healthquestionadmin',
							'heparin_lockout_time','insurance_data','iolink_consent_filled_form',
							'iolink_consent_form_signature','iolink_healthquestionadmin','iolink_iol_manufacturer',
							'iolink_patient_allergy','iolink_patient_prescription_medication','iolink_preophealthquestionnaire',
							'iolink_scan_consent','laser_procedure_patient_table','left_navigation_forms','localanesthesiarecord',
							'medication_time','msg_tbl','operatingroomrecords','operativereport','patientconfirmation',
							'patient_allergies_tbl','patient_anesthesia_medication_tbl','patient_in_waiting_tbl',
							'patient_medication_tbl','patient_prescription_medication_healthquest_tbl','patient_prescription_medication_tbl',
							'patient_previous_operation_tbl','postopnursingrecord','post_operative_site_time',
							'preopgenanesthesiarecord','preopnursequestionadmin','preopnursing_vitalsign_tbl',
							'preopphysicianorders','scan_documents','scan_log_tbl','scan_upload_tbl','vitalsign_tbl'
						   );
	foreach($updateTableArr as $key=>$tableName){
		$patient_id_field = 'patient_id';
		if($tableName == 'operativereport' || $tableName == 'patientconfirmation') {
			$patient_id_field = 'patientId';
		}
		$updatePatientIdQry="UPDATE $tableName SET $patient_id_field='".$newPatientId."'
					WHERE $patient_id_field='".$oldPatientId."'";
		imw_query($updatePatientIdQry) or die(imw_error().$updatePatientIdQry);
	}
	//END CODE TO REPLACE NEW PATIENT-ID WITH OLD ONE
}


$msg_info[] = "<br><br><b>All patient id updated successfully</b>";
?>
<html>
<head>
<title>Mysql Updates After Launch </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
<?php
}
@imw_close();
?> 

</body>
</html>
