<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$patient_id = $_SESSION['patient_id'];
$ascId = $_SESSION['ascId'];
$pConfId = $_SESSION['pConfId'];

if($_REQUEST['saveMe']=='true'){
	$medication_names = $_REQUEST['medicationName'];
	$medication_reactions = $_REQUEST['medicationReactions'];
	$medication_ids = $_REQUEST['medicationIds'];
	foreach($medication_names as $key => $mediNames){		
		if($mediNames){
			unset($arrayRecord);
			if($medication_ids[$key]){
				$arrayRecord['prescription_medication_name'] = $mediNames;
				$arrayRecord['prescription_medication_desc'] = $medication_reactions[$key];
				$objManageData->updateRecords($arrayRecord, 'patient_medication_tbl', 'medication_id', $medication_ids[$key]);
			}else{
				$arrayRecord['prescription_medication_name'] = $mediNames;
				$arrayRecord['prescription_medication_desc'] = $medication_reactions[$key];
				$arrayRecord['patient_confirmation_id'] = $pConfId;
				//$arrayRecord['asc_id'] = $ascId;
				$arrayRecord['patient_id'] = $patient_id;
				$objManageData->addRecords($arrayRecord, 'patient_medication_tbl');
			}
		}else if($medication_ids[$key]){
			$objManageData->delRecord('patient_medication_tbl', 'medication_id', $medication_ids[$key]);
		}
	}
}

//GETTING PATIENT MEDICATIONS
	$patientMedications = $objManageData->getArrayRecords('patient_medication_tbl', 'patient_confirmation_id', $pConfId);
	if(count($patientMedications)>0){
		foreach($patientMedications as $medications){
			++$seq;
			$medicationsNameList[$seq] = $medications->prescription_medication_name;
			$medicationsDescList[$seq] = $medications->prescription_medication_desc;
			$medicationId[$seq] = $medications->medication_id;
		}
	}
//GETTING PATIENT MEDICATIONS
?>
<form name="frm_localanes_allergies_spreadsheet" method="post" style="margin:0px;" action="local_anes_rec_medication_spreadsheet.php?saveMe=true">
	<table  align="left" width="90%" border="0" cellpadding="0" cellspacing="0" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
		<?php 
		for($i_localanes_med=1;$i_localanes_med<=10;$i_localanes_med++){ 
			?> 
				<input type="hidden" name="medicationIds[]" value="<?php echo $medicationId[$i_localanes_med]; ?>">
				<tr style="padding-left:4; ">
					<td height="22">&nbsp;</td>
					<td colspan="5" height="22" class="text_10b" align="left">
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td align="left"><input type="text" name="medicationName[]" id="medication_localanes_name<?php echo $i_localanes_med; ?>" class="field text" style=" border:1px solid #ccccc;width:215px; height:22px; " tabindex="1" value="<?php echo $medicationsNameList[$i_localanes_med]; ?>"/></td>
								<td align="left"><input type="text" name="medicationReactions[]" id="medication_localanes_reaction<?php echo $i_localanes_med; ?>" class="field text" style=" border:1px solid #ccccc;width:215px; height:22px;" tabindex="1" value="<?php echo $medicationsDescList[$i_localanes_med]; ?>"  /></td>
							</tr>
						</table>
					</td>
				</tr>
			<?php 
			} 
		?>
	</table>
</form>	
