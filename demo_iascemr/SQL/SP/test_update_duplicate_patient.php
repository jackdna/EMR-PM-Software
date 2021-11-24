<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(900);
	include_once("../../common/conDb.php");  //MYSQL CONNECTION

function displayDateFormat($selectDt) {
	$setDate='';
	if($selectDt && $selectDt!='0000-00-00') {
		list($Yr,$Mnt,$Dy) = explode('-',$selectDt);
		if($Yr && $Mnt && $Dy) {
			$setDate = date('m-d-Y',mktime(0,0,0,$Mnt,$Dy,$Yr));
		}	
	}
	return $setDate;
}

function mergePatientIdFun($oldPatientId,$newPatientId){
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
								'preopphysicianorders','scan_documents','scan_log_tbl','scan_upload_tbl','surgical_check_list','vitalsign_tbl'
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
		
		//START CODE TO DELETE OLD PATIENT-INFO
			$delPatientQry=  "DELETE FROM patient_data_tbl WHERE patient_id='".$oldPatientId."'";
			//$delPatientRes = imw_query($delPatientQry) or die(imw_error());
		//END CODE TO DELETE OLD PATIENT-INFO
	}
}
?>

<html>
<head>
<title>Mysql Updates After Launch </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<table align="left" border="0" cellpadding="2" cellspacing="2" width="80%">
	<tr>
		<th colspan="8">List of duplicate patients</th>
	</tr>
	<tr bgcolor="#80AFEF" align="left" valign="top">
		<th>SNo.</th>
		<th nowrap>Patient-ID</th>
		<th nowrap>Confimation ID - DOS</th>
		<th nowrap>First Name</th>
		<th nowrap>Midle Name</th>
		<th nowrap>Last Name</th>
		<th>DOB</th>
		<th nowrap>IMW Patient-ID</th>
	</tr>
<?php	
	$patientDataTblQry 		= "SELECT patient_fname,patient_lname,date_of_birth FROM patient_data_tbl ORDER BY patient_fname ASC";
	$patientDataTblRes 		= imw_query($patientDataTblQry);
	$patientDataTblNumRow 	= imw_num_rows($patientDataTblRes);
	$c=0;
	$cntrColor=0;
	if($patientDataTblNumRow>0) {
		while($patientDataTblRow 	 = imw_fetch_array($patientDataTblRes)) {
			$patientDuplicateDataQry = "SELECT * FROM patient_data_tbl 
											WHERE patient_fname='".addslashes($patientDataTblRow['patient_fname'])."'
											  AND patient_lname='".addslashes($patientDataTblRow['patient_lname'])."'
											  AND date_of_birth='".$patientDataTblRow['date_of_birth']."'
											ORDER BY patient_fname ASC, patient_id ASC";
			$patientDuplicateDataRes 		= imw_query($patientDuplicateDataQry) or die(imw_error());
			$patientDuplicateDataNumRow 	= imw_num_rows($patientDuplicateDataRes);
			if($patientDuplicateDataNumRow>1) {
				$showDuplicate = true;
				$oldNewPtId=array();
				$duplicatePaitentMnameArr = array();
				$imwPatientIdArr = array();
				while($patientDuplicateDataRow = imw_fetch_array($patientDuplicateDataRes)) {
					$confirmIdAndDos='';
					if(!in_array($patientDuplicateDataRow['patient_id'],$duplicatePaitentIdArr)) {
						$cntrColor++;
						if($showDuplicate == true) {
							$c++;
						}
						
						$showDuplicate=false;
						$trColor="#FFFFFF";
						if($cntrColor%2==0) { $trColor = "#EAF4FD";}
						
						$oldNewPtId[] = $patientDuplicateDataRow['patient_id'];
						$duplicatePaitentMnameArr[]= $patientDuplicateDataRow['patient_mname'];
						$imwPatientIdArr[] = stripslashes($patientDuplicateDataRow['imwPatientId']);
						$chkConfirmIdQry = "SELECT patientConfirmationId,dos FROM `patientconfirmation` WHERE patientId='".$patientDuplicateDataRow['patient_id']."' ";
						$chkConfirmIdRes 		= imw_query($chkConfirmIdQry);
						if(imw_num_rows($chkConfirmIdRes)>0) {
							while($chkConfirmIdRow = imw_fetch_array($chkConfirmIdRes)) {
								$confirmIdAndDos .= $chkConfirmIdRow['patientConfirmationId'].' - '.displayDateFormat($chkConfirmIdRow['dos']).'<br>';
							}	
						}	
						$notDuplicate='';
						if(trim($duplicatePaitentMnameArr[0]) && trim($duplicatePaitentMnameArr[1]) && trim($duplicatePaitentMnameArr[0]) != trim($duplicatePaitentMnameArr[1])) {
							$notDuplicate = '<br><font color="#FF0000"><b>Not Duplicate</b></font>';
							$trColor='#FFFF00';
						}else {//CHECK FOR DUPLICATE PATIENT
							
						}	
						
		?>
							<tr align="left" valign="top" bgcolor="<?php echo $trColor;?>"> 
								<td><?php echo $c;?></td>
								<td><?php echo $patientDuplicateDataRow['patient_id'];?></td>
								<td nowrap><?php echo $confirmIdAndDos;?></td>
								<td><?php echo $patientDuplicateDataRow['patient_fname'];?></td>
								<td nowrap><?php echo $patientDuplicateDataRow['patient_mname'].$notDuplicate;?></td>
								<td><?php echo $patientDuplicateDataRow['patient_lname'];?></td>
								<td nowrap><?php echo displayDateFormat($patientDuplicateDataRow['date_of_birth']);?></th>
								<td nowrap><?php echo $patientDuplicateDataRow['imwPatientId'];?></th>
							</tr>
		
		<?php			
					}
					$duplicatePaitentIdArr[]= $patientDuplicateDataRow['patient_id'];
				}
				if($oldNewPtId) {
					if($oldNewPtId[0] && $oldNewPtId[1]) {
						$blMrgPt =true;
						if($duplicatePaitentMnameArr) {
							if(trim($duplicatePaitentMnameArr[0]) && trim($duplicatePaitentMnameArr[1]) && trim($duplicatePaitentMnameArr[0]) != trim($duplicatePaitentMnameArr[1])) {
								$blMrgPt =false;
							}
						}
						
						$oldPtId = $oldNewPtId[1]; //DEFAULT SETTING OF OLD PATIENT
						$newPtId = $oldNewPtId[0]; //DEFAULT SETTING OF NEW PATIENT
						
						//START  SET OLD-NEW PATIENT ACC TO IMW-ID
						if($imwPatientIdArr) {
							if(!trim($imwPatientIdArr[0]) && (trim($imwPatientIdArr[1]))) {
								$oldPtId = $oldNewPtId[0];
								$newPtId = $oldNewPtId[1];
							}
						}
						//END SET OLD-NEW PATIENT ACC TO IMW-ID
						
						//START CODE TO MERGE PATIENT
						if($blMrgPt==true) {
							//echo '<br>'.$newPtId.' - '.$oldPtId;
							/*
							if($newPtId=='228') {
								mergePatientIdFun($oldPtId,$newPtId);
							}*/
						}
						//END CODE TO MERGE PATIENT  
					}
				}
				if($showDuplicate == false) {
	?>	
					<tr>
						<td colspan="8"><hr></td>
					</tr>
	<?php			
				}
			}
		}
	}

?>

</table>


<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
<?php
}
@imw_close();
?> 

</body>
</html>