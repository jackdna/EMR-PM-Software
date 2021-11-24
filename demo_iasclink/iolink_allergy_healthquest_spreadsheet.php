<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php

include_once("common/conDb.php");
//include("common/linkfile.php");
//include_once("admin/classObjectFunction.php");
$objectMenageData = new manageData;

$patient_id = $_REQUEST['patient_id'];
$patient_in_waiting_id = $_REQUEST['patient_in_waiting_id'];
$pConfId = $_REQUEST['pConfId'];

if(!$allergiesHealth) {
	$allergiesHealth =$_REQUEST["allergiesHealth"];
}
if($_REQUEST['submitMe']=='true' || $_REQUEST['saveRecord']=='true'){
	$allergies_quest = $_REQUEST['allergies_quest'];
	$reaction_quest = $_REQUEST['reaction_quest'];
	$allergyId = $_REQUEST['allergyId'];
	if((is_array($allergies_quest)) && (!empty($allergies_quest))){
		foreach($allergies_quest as $Key => $allergiesArrValue){
				//$allergiesReactionArr['patient_confirmation_id'] = $pConfId;
				$allergiesReactionArr['patient_id'] = $patient_id;
				$allergiesReactionArr['patient_in_waiting_id'] = $patient_in_waiting_id;
				$allergiesReactionArr['allergy_name'] = addslashes($allergiesArrValue);
				$allergiesReactionArr['reaction_name'] = addslashes($reaction_quest[$Key]);
				$allergiesReactionArr['operator_name'] = $_SESSION['iolink_loginUserName'];
				$allergiesReactionArr['operator_id'] = $_SESSION['iolink_loginUserId'];
			if($allergiesArrValue!=''){
				if($allergyId[$Key]){
					$objectMenageData->updateRecords($allergiesReactionArr, 'iolink_patient_allergy', 'pre_op_allergy_id', $allergyId[$Key]);
				}else{
					$objectMenageData->addRecords($allergiesReactionArr, 'iolink_patient_allergy');
				}		
			}else if($allergiesArrValue=='' && $reaction_quest[$Key]=='') {
				$objectMenageData->delRecord('iolink_patient_allergy', 'pre_op_allergy_id', $allergyId[$Key]);
			
			}else if($allergiesArrValue=='') {
				$objectMenageData->updateRecords($allergiesReactionArr, 'iolink_patient_allergy', 'pre_op_allergy_id', $allergyId[$Key]);
			}else if($allergiesArrValue=='No'){
				$objectMenageData->delRecord('iolink_patient_allergy', 'patient_in_waiting_id', $patient_in_waiting_id);
			}
		}
	}

	//START CODE FOR HEALTH QUESTIONNAIRE
	if($_REQUEST['chbx_drug_react']=='Yes') {
		 imw_query("delete from iolink_patient_allergy where patient_in_waiting_id = '$patient_in_waiting_id'");
	}	
	//END CODE FOR HEALTH QUESTIONNAIRE

}


//GETTING ALLERGIES REACTIONS TO DISPLAY
	$allergiesReactionDetails = $objectMenageData->getArrayRecords('iolink_patient_allergy', 'patient_in_waiting_id', $patient_in_waiting_id);
	if(count($allergiesReactionDetails)>0){
		foreach($allergiesReactionDetails as $allergyName){
			++$seq1;
			$pre_op_allergy_id[$seq1] = $allergyName->pre_op_allergy_id;
			$allergy[$seq1] = $allergyName->allergy_name;
			$reaction[$seq1] = $allergyName->reaction_name;		
		}
	}
//GETTING ALLERGIES REACTIONS TO DISPLAY

//SET ALLERGIES VALUE IN HEADER
	// GETTING CONFIRMATION DETAILS
		$detailConfirmationAllergies = $objectMenageData->getRowRecord('patientconfirmation', 'patient_in_waiting_id ', $patient_in_waiting_id);
		if(detailConfirmationAllergies) {
			$Confirm_patientHeaderAllergiesNKDA_status = $detailConfirmationAllergies->allergiesNKDA_status;	
		}
	// GETTING CONFIRMATION DETAILS
	
	$patient_allergies_tblQry = "SELECT * FROM `iolink_patient_allergy` WHERE `patient_in_waiting_id` = '$patient_in_waiting_id'";
	$patient_allergies_tblRes = imw_query($patient_allergies_tblQry) or die(imw_error());
	$patient_allergies_tblNumRow = imw_num_rows($patient_allergies_tblRes);
	if($patient_allergies_tblNumRow>0) {
		$allergiesValue = 'Yes';
		while($patient_allergies_tblRow= imw_fetch_array($patient_allergies_tblRes)) {
			$chk_allergy_name = trim($patient_allergies_tblRow['allergy_name']);
			if($chk_allergy_name=='NKA' && $patient_allergies_tblNumRow==1) {
				$allergiesValue = 'NKA';
			}
		}
	
	}else if($Confirm_patientHeaderAllergiesNKDA_status=="Yes") {
		$allergiesValue = 'NKA';
	}else {
		$allergiesValue = '';
	}
//END SET ALLERGIES VALUE IN HEADER

?>
<!-- <form name="frm_health_quest_spreadsheet" method="post" style="margin:0px;" action="health_quest_spreadsheet.php?submitMe=true"> -->
	<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="patient_in_waiting_id" value="<?php echo $patient_in_waiting_id; ?>">
	<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">	
	<input type="hidden" name="hidd_allergiesValue" id="hidd_allergiesValueId" value="<?php echo $allergiesValue; ?>">	
	<table class="table_collapse alignLeft" style="background-color:#F1F4F0;" >
    
		<?php 
		for($i_healthquest_allerg=1;$i_healthquest_allerg<=20;$i_healthquest_allerg++) { 
			
			if(!$allgNameWidth) {
				$allgNameWidth = $_REQUEST["allgNameWidth"];
			}	
			if($allgNameWidth=="") { $allgNameWidth = 210; }
			
			if(!$allgReactionWidth) {
				$allgReactionWidth = $_REQUEST["allgReactionWidth"];
			}	
			if($allgReactionWidth=="") { $allgReactionWidth = 210; }
			
			if(trim($allergy[$i_healthquest_allerg])=="") {$txtBorderAllrg="0px";} else { $txtBorderAllrg="1px"; }
			if(trim($reaction[$i_healthquest_allerg])=="") {$txtBorderReact="0px"; } else { $txtBorderReact="1px"; }
			?> 
			<tr style="padding-left:0px; background-color:#FFFFFF; "> <!--  bgcolor="#F1F4F0"-->
				<td style="height:22px; padding-left:4px;"><input type="hidden" name="allergyId[]" value="<?php echo $pre_op_allergy_id[$i_healthquest_allerg]; ?>"></td>
			  	<td class="text_10b alignLeft" style="height:22px;">
					<table style="border:none; border-collapse:collapse;">
						<tr>
							<td class="text_10b"><input type="text"  name="allergies_quest[]" id="Allergies_quest<?php echo $i_healthquest_allerg;?>" class="field text" style=" border:<?php echo $txtBorderAllrg;?>; border-color:#CCCCCC; border-style:solid; width:<?php echo $allgNameWidth;?>px; height:22px; " tabindex="1" value="<?php echo stripslashes($allergy[$i_healthquest_allerg]);?>" /></td>
							<td class="text_10b"><input type="text" name="reaction_quest[]" id="Reaction_quest<?php echo $i_healthquest_allerg;?>" class="field text" style=" border:<?php echo $txtBorderReact;?>; border-color:#CCCCCC; border-style:solid; width:<?php echo $allgReactionWidth;?>px;; height:22px;" tabindex="1" value="<?php echo stripslashes($reaction[$i_healthquest_allerg]);?>" /></td>
						</tr>
					</table>
				</td>
			</tr>
			<?php 
		} 
		?>
	</table>
<!-- </form> -->
<?php
	if($allergiesHealth=='Yes'){
		?>
		<script>
			
			obj = document.getElementsByName('allergies_quest[]');
			obj1 = document.getElementsByName('reaction_quest[]');
			var len = obj.length;
			for(i=0;i<len;i++){
				obj[i].disabled = true;
				obj[i].value='';
				obj1[i].disabled = true;
				obj1[i].value='';
			}
		</script>
		<?php
	}
?>
<script>
	var allergiesValueId = document.getElementById('hidd_allergiesValueId').value;
	if(allergiesValueId==''){
		//DO NOTHING
	}else if(allergiesValueId=='NKA') {
		//DO NOTHING
	}else {
		allergiesValueId = '<img src="images/Interface_red_image003.gif" width="17" height="15" align="middle" onclick="showAllergiesPopUpFn();">';
	}
	if(top.allergiesHeaderId) {
		top.allergiesHeaderId.innerHTML = allergiesValueId;
	}	
</script>