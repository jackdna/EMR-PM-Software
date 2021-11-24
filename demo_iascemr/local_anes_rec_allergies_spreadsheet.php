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
//$ascId = $_SESSION['ascId'];
$pConfId = $_SESSION['pConfId'];

if($_REQUEST['saveRecord']=='true'){
	$allergies_name = $_REQUEST['allergiesName'];
	$allergies_reaction = $_REQUEST['allergiesReaction'];	
	$preOpAllergyId = $_REQUEST['preOpAllergyIdList'];
	foreach($allergies_name as $key => $allergies){
		if($allergies){
			if($preOpAllergyId[$key]){
				unset($arrayRecord);
				$arrayRecord['allergy_name'] = $allergies;
				$arrayRecord['reaction_name'] = $allergies_reaction[$key];
				$objManageData->updateRecords($arrayRecord, 'patient_allergies_tbl', 'pre_op_allergy_id', $preOpAllergyId[$key]);
			}else{
				unset($arrayRecord);
				$arrayRecord['allergy_name'] = $allergies;
				$arrayRecord['reaction_name'] = $allergies_reaction[$key];
				$arrayRecord['patient_confirmation_id'] = $pConfId;
				//$arrayRecord['asc_id'] = $ascId;
				$arrayRecord['patient_id'] = $patient_id;
				$objManageData->addRecords($arrayRecord, 'patient_allergies_tbl');
			}
		}else if($preOpAllergyId[$key]){
			$objManageData->delRecord('patient_allergies_tbl', 'pre_op_allergy_id', $preOpAllergyId[$key]);
		}
	}
}

//GETTING PATIENT ALLERGIES
	$patientAllergies = $objManageData->getArrayRecords('patient_allergies_tbl', 'patient_confirmation_id', $pConfId);
	if(count($patientAllergies)>0){
		foreach($patientAllergies as $allergies){
			++$seq;
			$allergiesList[$seq] = $allergies->allergy_name;
			$reactionList[$seq] = $allergies->reaction_name;
			$preOpAllergyIdList[$seq] = $allergies->pre_op_allergy_id;
		}
	}
//GETTING PATIENT ALLERGIES
?>
<form name="frm_localanes_allergies_spreadsheet" method="post" style="margin:0px; " action="local_anes_rec_allergies_spreadsheet.php?saveRecord=true"> 
<table  align="left" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
	<?php 
	for($i_localanes_allerg=1;$i_localanes_allerg<=10;$i_localanes_allerg++){ 
		?> 
		<input type="hidden" name="preOpAllergyIdList[]" value="<?php echo $preOpAllergyIdList[$i_localanes_allerg]; ?>">
		<tr>
			<td height="22">&nbsp;</td>
			<td colspan="5" height="22" class="text_10b" align="left">
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td align="left"><input name="allergiesName[]" type="text" id="Allergies_localanes<?php echo $i_localanes_allerg; ?>" class="field text" style=" border:1px solid #ccccc; width:235px; height:22px; " tabindex="1" value="<?php echo $allergiesList[$i_localanes_allerg]; ?>"></td>
						<td align="left"><input name="allergiesReaction[]" type="text" id="Reaction_localanes<?php echo $i_localanes_allerg; ?>" class="field text" style=" border:1px solid #ccccc; width:235px; height:22px;" tabindex="1" value="<?php echo $reactionList[$i_localanes_allerg]; ?>"/></td>
					</tr>
				</table>
			</td>
		</tr>
		<?php 
	} 
	?>
</table>
</form> 