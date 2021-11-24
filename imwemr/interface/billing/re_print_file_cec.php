<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

?><?php
//-------GENERATE CLAIM BATCH FILE ------

$separation_done = false;

if(count($filesName) > 0){
	$fileNameID = $filesName[0];
	$files = false;
	$qry = "select * from batch_file_submitte where batch_file_submitte_id = '$fileNameID' LIMIT 0,1";
	$qryId = imw_query($qry);
	$segmentStart = 1;
	if($qryId && imw_num_rows($qryId)>0){
		imw_query("update batch_file_submitte set status = '0' where batch_file_submitte_id = '$fileNameID'");
		$fileDetail = (object)imw_fetch_assoc($qryId);
		$fileName = $fileDetail->file_name;
		$encounter_id = $fileDetail->encounter_id;
		$pcld_id = $fileDetail->pcld_id;
		$file_data = $fileDetail->file_data;
		$insuranceComp = $fileDetail->ins_company_id;
		$ins_comp = $fileDetail->ins_comp;
		$default_group_id = $fileDetail->group_id;
		$ARR_CHLS_Type_Status = html_entity_decode($fileDetail->clm_type_indicator);
		$ARR_CHLS_Clm_Ctrl_Num = html_entity_decode($fileDetail->claim_ctrl_num);
		if($ins_comp == 'primary'){
			$insComp = 'primaryInsuranceCoId';
			$ins_submit = 'primarySubmit';
		}
		if($ins_comp == 'secondary'){
			$insComp = 'secondaryInsuranceCoId';
			$ins_submit = 'secondarySubmit';
		}
		
		//---- GET SUBMITTER DETAILS ---------------
		$SenderDetails = (object)getRecords('users','id',$fileDetail->sender_id);
		//---- GET FACILITY HQ DETAILS ---------------
		$facilityDetails = (object)getRecords('facility','facility_type',$fileDetail->default_facility_id);
		
		//---- GET COPAY POLICY DETAILS -----
		$policiesDetails = (object)getRecords('copay_policies','policies_id',1);
		$navicureFile = false;
		if(trim(strtolower($policiesDetails->Name)) == "navicure"){
			$navicureFile = true;
		}
		
		//---- GET GROUPS DETAILS -------
		$groupDetails = $this->get_groups_detail($default_group_id);

		//--- GET INSURANCE COMPANY DETAILS ---
		$ins_qry = "select BatchFile from insurance_companies where id in ($insuranceComp) 
				and claim_type = '1' and BatchFile = '1' group by BatchFile";
		$ins_details_res = imw_query($ins_qry);
		$InsuranceComDetails = imw_fetch_assoc($ins_details_res);
		
		$production_code = $fileDetail->production_code;
		if(strtolower($production_code) == 't'){
			$production_code = $ProductionFile;
		}
		if(count($InsuranceComDetails)== 0){
			$emdeon_ins_comp = explode(',',$insuranceComp);
		}
		else{
			$medicare_ins_comp = explode(',',$insuranceComp);
		}
		$get_file_name = explode('MEDICARE',$fileDetail->file_name);
		$change_ins_comp = array();
		if(count($get_file_name) >1){
			$change_ins_comp = $emdeon_ins_comp;
		}
		else{
			$change_ins_comp = $medicare_ins_comp;
		}
		$file_encounter = $fileDetail->encounter_id;
		$fileProcessName = '';
		/*--new logic for selecting regenerate file--*/
		$curr_file_version = $fileDetail->file_format;
		$curr_file_clHouse = $fileDetail->clearing_house;
		if($curr_file_clHouse=='emdeon' && $groupDetails['group_institution'] != 1){
			$fileProcessName = 'reprint_emdeon_electronic_file_5010.php';
		}elseif($curr_file_clHouse=='emdeon' && $groupDetails['group_institution'] == 1){
			$fileProcessName = 'reprint_emdeon_electronic_file_i_5010.php';
		}elseif($curr_file_clHouse=='visionshare'){
			$fileProcessName = 'reprint_medicare_electronic_file_5010.php';
		}elseif($curr_file_clHouse=='navicure'){
			$fileProcessName = 'reprint_navicure_electronic_file_5010.php';
		}elseif($curr_file_clHouse=='ghn'){
			$fileProcessName = 'reprint_ghn_electronic_file_i_5010.php';
		}

		/*--new logic end--*/

		//--- RE-GENERATE FOR EMDEON --OLD LOGIC-
		if(count($InsuranceComDetails)== 0){
			//--- DATA FOR EMDEON --------
			$submitterId = $groupDetails['sub_id'];
			$recieverId = $groupDetails['rec_id'];
			$BatchFile = $policiesDetails->Name;
			$fileNameStart = preg_replace('/ /','_',$fileName);			
			
			if($groupDetails['group_institution'] != 1){
				if($navicureFile == false){
					$fileProcessName = 'reprint_emdeon_electronic_file_5010.php';
				}else if($navicureFile == true){
					$fileProcessName = 'reprint_navicure_electronic_file_5010.php';
				}
			}else{
				//$fileProcessName = 'reprint_emdeon_electronic_file_i.php';
				$fileProcessName = 'reprint_ghn_electronic_file_i_5010.php';
			}
			$batchFileStatus = true;
		}
		else{	
			$insDetails = $objManageData->getRecords('insurance_companies','id',$insuranceComp);
			$BatchFile = $insDetails->name;
			//--- MEDICARE INSURANCE COMPANY RECEIVER AND SUBMITTER ID ----
			$recieverId = trim($insDetails->Reciever_id);
			$submitterId = trim($insDetails->payer_type);

			if(empty($recieverId) === true or empty($submitterId) === true){
				$recieverId = $groupDetails['MedicareReceiverId'];
				$submitterId = $groupDetails['MedicareSubmitterId'];
			}
			
			$batchFileStatus = false;
			$fileNameStart = preg_replace('/ /','_',$fileName);
			if($fileProcessName==''){
			//--- RE-GENERATE CLAIM FILE FOR MEDICARE ---
				$fileProcessName = 'reprint_medicare_electronic_file_5010.php';
			}
		}		
		$validChargeListDetailsId = array();		
		//--- REQUIRE FILE TO RE-GENERATE CLAIM FILE ----
		if($curr_file_version=='5010'){$file_suffix = '_'.$curr_file_version;}else{$file_suffix='';}
		if($fileProcessName == 'reprint_ghn_electronic_file_i_5010.php'){
			require_once('reprint_inst_only_check_5010.php');
		}else{

			$Interchange_controls = $fileDetail->Interchange_control;
			$cont_length = (9 - strlen($Interchange_controls));
			$addSpaces = NULL;
			for($a=0;$a<$cont_length;$a++){
				$addSpaces .= '0';
			}
			$InterchangeControlNumber = $addSpaces.$Interchange_controls;	
			
			$Transaction_set_unique_control = $fileDetail->Transaction_set_unique_control;
			$header_control_identifier = $fileDetail->Transaction_set_unique_control;
			
			$submitterSpaceCount = 15 - strlen($submitterId);
			$submitterSpaceStr = NULL;
			for($s=1;$s<=$submitterSpaceCount;$s++){
				$submitterSpaceStr .= ' ';
			}
			$recieveSpaceCount = 15 - strlen($recieverId);
			$recieveSpaceStr = NULL;
			for($s=1;$s<=$recieveSpaceCount;$s++){
				$recieveSpaceStr .= ' ';
			}

			if($curr_file_version=='5010'){
				$arr_tempFPName = explode('.',$fileProcessName);
				$fileProcessName = $arr_tempFPName[0].'_5010.'.$arr_tempFPName[1];
			}
			require_once($fileProcessName);
		}
		
		if(strtolower($production_code) == 'p'){
			if($separation_done == true){
				if(count($arr_valid) > 0){
					$val_charge_id = join(',',$arr_valid);
					imw_query("update patient_charge_list set $ins_submit = '1',hcfaStatus = '1' where charge_list_id in ($val_charge_id)");
				}
			}else{
				if(count($validChargeListId) > 0){
					$val_charge_id = join(',',$validChargeListId);
					imw_query("update patient_charge_list set $ins_submit = '1',hcfaStatus = '1' where charge_list_id in ($val_charge_id)");
				}
			}
		}
	}
	else{
		$err = 'Claims batch file not exists.';
	}
}

//show invalid claims in files if any
if($separation_done == true){
	$script = "";
	if(count($arr_invalid1) > 0){ 
		$errors = join(":", $error_msg1);
		$inValidChargeListIds = join(",", $arr_invalid1);
		//$script .= 'viewFile('.$created_file_id1.',\''.$errors.'\',\''.$inValidChargeListIds.'\');';
	}else{
		//$script .= 'viewFile(\''.$created_file_id1.'\');';
	}
	if(count($arr_invalid2) > 0){ echo "there";
		$errors = join(":", $error_msg2);
		$inValidChargeListIds = join(",", $arr_invalid2);
		//$script .= 'viewFile('.$created_file_id2.',\''.$errors.'\',\''.$inValidChargeListIds.'\');';
	}else{ 
		//$script .= 'viewFile(\''.$created_file_id2.'\');';
	}
}else{
	if($inValidChargeListId[0] != ''){
		$errors = join(":",$error);
		$inValidChargeListIds = join(",",$inValidChargeListId);
		$script = 'viewFile('.$fileDetail->Batch_file_submitte_id.',\''.$errors.'\',\''.$inValidChargeListIds.'\');';
	}
	else{
		$script = 'viewFile(\''.$fileDetail->Batch_file_submitte_id.'\');';
	}
}
?>