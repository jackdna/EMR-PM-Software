<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
//include("common/linkfile.php");
//include_once("admin/classObjectFunction.php");
$objectMenageData = new manageData;
//if(!$patient_id) {
	$patient_id = $_REQUEST['patient_id'];
//}
//if(!$pConfId) {
	$pConfId = $_REQUEST['pConfId'];
//}
if(!$allergiesHealth) {
	$allergiesHealth =$_REQUEST["allergiesHealth"];
}
if($_REQUEST['submitMe']=='true' || $_REQUEST['saveRecord']=='true'){
	$allergies_quest = $_REQUEST['allergies_quest'];
	$reaction_quest = $_REQUEST['reaction_quest'];
	$allergyId = $_REQUEST['allergyId'];
	if((is_array($allergies_quest)) && (!empty($allergies_quest))){
		foreach($allergies_quest as $Key => $allergiesArrValue){
				$allergiesReactionArr['patient_confirmation_id'] = $pConfId;
				$allergiesReactionArr['patient_id'] = $patient_id;
				$allergiesReactionArr['allergy_name'] = addslashes($allergiesArrValue);
				$allergiesReactionArr['reaction_name'] = addslashes($reaction_quest[$Key]);
				$allergiesReactionArr['operator_name'] = $_SESSION['loginUserName'];
				$allergiesReactionArr['operator_id'] = $_SESSION['loginUserId'];
			if($allergiesArrValue!=''){
				if($allergyId[$Key]){
					$objectMenageData->updateRecords($allergiesReactionArr, 'patient_allergies_tbl', 'pre_op_allergy_id', $allergyId[$Key]);
				}else{
					$objectMenageData->addRecords($allergiesReactionArr, 'patient_allergies_tbl');
				}		
			}else if($allergiesArrValue=='' && $reaction_quest[$Key]=='') {
				$objectMenageData->delRecord('patient_allergies_tbl', 'pre_op_allergy_id', $allergyId[$Key]);
			
			}else if($allergiesArrValue=='') {
				$objectMenageData->updateRecords($allergiesReactionArr, 'patient_allergies_tbl', 'pre_op_allergy_id', $allergyId[$Key]);
			}else if($allergiesArrValue=='No'){
				$objectMenageData->delRecord('patient_allergies_tbl', 'patient_confirmation_id', $pConfId);
			}
		}
	}
}
//GETTING ALLERGIES REACTIONS TO DISPLAY
	$allergiesReactionDetails = $objectMenageData->getArrayRecords('patient_allergies_tbl', 'patient_confirmation_id', $pConfId);
	if(count($allergiesReactionDetails)>0){
		foreach($allergiesReactionDetails as $allergyName){
			++$seq1;
			$pre_op_allergy_id[$seq1] = $allergyName->pre_op_allergy_id;
			$allergy[$seq1] = $allergyName->allergy_name;
			$reaction[$seq1] = $allergyName->reaction_name;		
		}
	}
//GETTING ALLERGIES REACTIONS TO DISPLAY

// GETTING CONFIRMATION DETAILS
	$detailConfirmationAllergies = $objectMenageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
	if($detailConfirmationAllergies) {
		$Confirm_patientHeaderAllergiesNKDA_status = $detailConfirmationAllergies->allergiesNKDA_status;	
	}
// GETTING CONFIRMATION DETAILS

//SET ALLERGIES VALUE IN HEADER
	
	
	$patient_allergies_tblQry = "SELECT * FROM `patient_allergies_tbl` WHERE `patient_confirmation_id` = '$pConfId'";
	$patient_allergies_tblRes = imw_query($patient_allergies_tblQry) or die(imw_error());
	$patient_allergies_tblNumRow = imw_num_rows($patient_allergies_tblRes);
	if($patient_allergies_tblNumRow>0) {
		$allergiesValue = 'Yes';
		while($patient_allergies_tblRow= imw_fetch_array($patient_allergies_tblRes)) {
			$chk_allergy_name = trim($patient_allergies_tblRow['allergy_name']);
			if(strtoupper($chk_allergy_name)=='NKA' && $patient_allergies_tblNumRow==1) {
				$allergiesValue = 'NKA';
			}
		}
	
	}else if($Confirm_patientHeaderAllergiesNKDA_status=="Yes") {
		$allergiesValue = 'NKA';
	}else {
		$allergiesValue = '';
	}
//END SET ALLERGIES VALUE IN HEADER
$cntHlt = count($allergy)+20;
?>
<!-- <form name="frm_health_quest_spreadsheet" method="post" style="margin:0px;" action="health_quest_spreadsheet.php?submitMe=true"> -->
	<input type="hidden" name="patient_id"  value="<?php echo $patient_id; ?>">
	<input type="hidden" name="pConfId"  value="<?php echo $pConfId; ?>">
	<input type="hidden" name="ascId"  value="<?php echo $ascId; ?>">	
	<input type="hidden" name="hidd_allergiesValue" id="hidd_allergiesValueId" value="<?php echo $allergiesValue; ?>">	
	<input type="hidden" name="hidd_count_rows_in_table" id="hidd_count_rows_in_table" value="<?php echo $cntHlt; ?>">	
   
	<table id="hlthQstSpreadTableId" class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped" style="background-color:#F1F4F0;" >
		<tbody>
		<?php 
		
		for($i_healthquest_allerg=1;$i_healthquest_allerg<=$cntHlt;$i_healthquest_allerg++) { 
			
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
				<input type="hidden" name="allergyId[]" value="<?php echo $pre_op_allergy_id[$i_healthquest_allerg]; ?>">
			  	<td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">
					<input type="text"  name="allergies_quest[]" id="Allergies_quest<?php echo $i_healthquest_allerg;?>" class="form-control" tabindex="1" value="<?php echo stripslashes($allergy[$i_healthquest_allerg]);?>" />
				</td>
				<td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">
					<input type="text" name="reaction_quest[]" id="Reaction_quest<?php echo $i_healthquest_allerg;?>" class="form-control" tabindex="1" value="<?php echo stripslashes($reaction[$i_healthquest_allerg]);?>" />
				</td>
			</tr>
			<?php 
		} 
		?>
		</tbody>
	</table>
<!-- </form> -->
<?php
	if($allergiesNKDA_patientconfirmation_status=='Yes'){
		?>
		<script>
			
			obj = document.getElementsByName('allergies_quest[]');
			obj1 = document.getElementsByName('reaction_quest[]');
			var len = obj.length;
			for(i=0;i<20;i++){
				obj[i].disabled = true;
				obj[i].value='';
				obj1[i].disabled = true;
				obj1[i].value='';
			}
		</script>
		<?php
	}
	//start print emr
	$healthHeight='10';
	if($sect=="print_emr") {
		if(count($allergy)>0) {
			$healthHeight=(count($allergy)*24);
        }?>
		<script>
            var healthHeight = '<?php echo $healthHeight;?>';
            if(document.getElementById('iframe_health_quest')) {
                document.getElementById('iframe_health_quest').style.height=healthHeight+'px';
            }
			if(document.getElementById('iframe_history_physical')) {
                document.getElementById('iframe_history_physical').style.height=healthHeight+'px';
            }
			if(document.getElementById('iframe_allergies_pre_op_nurse_rec')) {
                document.getElementById('iframe_allergies_pre_op_nurse_rec').style.height=healthHeight+'px';
				
				if(document.getElementById('frmAction')) {
					if(document.getElementById('frmAction').value=="laser_procedure.php") {
						document.getElementById('iframe_allergies_pre_op_nurse_rec').style.width='350px';
						document.getElementById('iframe_allergies_pre_op_nurse_rec').style.overflowX='hidden';
					}
				}
            }
			if(document.getElementById('iframe_allergies_genanes_nurse_notes')) {
                document.getElementById('iframe_allergies_genanes_nurse_notes').style.height=healthHeight+'px';
            }
			if(document.getElementById('iframe_allergies_pre_op_gen_anes')) {
                document.getElementById('iframe_allergies_pre_op_gen_anes').style.height=healthHeight+'px';
            }
			if(document.getElementById('iframe_allergies_local_anes_rec')) {
                document.getElementById('iframe_allergies_local_anes_rec').style.height=healthHeight+'px';
				document.getElementById('iframe_allergies_local_anes_rec').style.width='250px';
				document.getElementById('alrNmeLabel').style.width='100px';
				document.getElementById('alrNmeLabel').style.paddingLeft='2px';
				document.getElementById('alrRecLabel').style.width='100px';
				document.getElementById('alrRecLabel').style.paddingLeft='2px';				
            }
			if(document.getElementById('iframe_allergies_gen_anes_rec')) {
                document.getElementById('iframe_allergies_gen_anes_rec').style.height=healthHeight+'px';
            }
			if(document.getElementById('iframe_allergies_oproom_rec')) {
                document.getElementById('iframe_allergies_oproom_rec').style.height=healthHeight+'px';
				document.getElementById('iframe_allergies_oproom_rec').style.width='300px';
            }
        </script>
	<?php	
	}
	//end print emr	
?>
<script>
	var allergiesValueId = document.getElementById('hidd_allergiesValueId').value;
	if(allergiesValueId==''){
		//DO NOTHING
	}else if(allergiesValueId=='NKA') {
		//DO NOTHING
	}else {
		allergiesValueId = '<img src="images/Interface_red_image003.gif" style="width:17px; height:15px;" class="valignMiddle" onclick="showAllergiesPopUpFn(<?php echo $pConfId; ?>);">';
	}
	if(top.allergiesHeaderId) {
		top.allergiesHeaderId.innerHTML = allergiesValueId;
	}	
</script>