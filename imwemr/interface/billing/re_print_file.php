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
/*
File: re_print_file.php
Purpose: To control re-generate operation, and decide in which mode regeneation to be done
Access Type: Include File.
*/
if(count($filesName) > 0){
	$fileNameID = $filesName[0];
	$files = false;
	$qry = "select * from batch_file_submitte where batch_file_submitte_id = '$fileNameID' LIMIT 0,1";
	$qryId = imw_query($qry);
	$segmentStart = 1;
	if(imw_num_rows($qryId)>0){
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
		
		//-----GET ACTIVE CLEARING HOUSE DETAILS------
		$ClearingHouses			= $this->ClearingHouse();
		$ClearingHouse			= $ClearingHouses[0];

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
		$ins_qry = "select BatchFile,in_house_code,name,Reciever_id,payer_type,claim_type,institutional_type,Payer_id,Payer_id_pro from insurance_companies where id in ($insuranceComp) 
				 group by BatchFile";
		$ins_details_res = imw_query($ins_qry);
		$InsuranceComDetails = imw_fetch_assoc($ins_details_res);
		
		$claim_type = '';$Ins_837i=false; $DirectBatch = false;
		$payerSubmitterID = false; $payerReceiverID = false;
		if(count($InsuranceComDetails)>0){
			$claim_type	= $InsuranceComDetails['claim_type'];
			$Ins_837i	= $InsuranceComDetails['institutional_type']=='INST_ONLY' ? true :  false;
			$DirectBatch= $InsuranceComDetails['BatchFile'];
			if($DirectBatch && trim($InsuranceComDetails[0]['payer_type'])!='' && trim($InsuranceComDetails[0]['Reciever_id'])!=''){
				$payerSubmitterID	= $InsuranceComDetails[0]['payer_type'];
				$payerReceiverID	= $InsuranceComDetails[0]['Reciever_id'];
			}
		}
		$production_code = $fileDetail->production_code;
		if(strtolower($production_code) == 't'){
			$production_code = $ProductionFile;
		}
		if(count($InsuranceComDetails)== 0 || intval($claim_type) != '1'){
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
		//$fileProcessName = 'reprint_emdeon_electronic_file_i_5010.php';
		$curr_file_version = $fileDetail->file_format;
		$curr_file_clHouse = $fileDetail->clearing_house;
		if($curr_file_clHouse=='emdeon'){
			$fileProcessName = 'reprint_emdeon_electronic_file_5010.php';
			if($groupDetails['group_institution']=='1' && $Ins_837i){
				$fileProcessName = 'reprint_emdeon_electronic_file_i_5010.php';
			}		
		}else if($curr_file_clHouse=='visionshare'){
			$fileProcessName = 'reprint_medicare_electronic_file_5010.php';
		}
		/*--new logic end--*/

		//--- RE-GENERATE FOR EMDEON --OLD LOGIC-
		$medicare_ins_details = (object)getRecords('insurance_companies','id',$insuranceComp);
		if((count($InsuranceComDetails) == 0 || (count($InsuranceComDetails) > 0 && intval($claim_type) != '1') || (count($InsuranceComDetails) > 0 && intval($claim_type) == '1' && $DirectBatch==false)) || (strtolower($billing_global_server_name)=='gewirtz' && strtoupper($medicare_ins_details->Payer_id)=='SRRGA')){
			//--- DATA FOR EMDEON --------
			$submitterId = $groupDetails['sub_id'];
			$recieverId = $groupDetails['rec_id'];
			if($DirectBatch && $payerSubmitterID && $payerReceiverID){
				$submitterId 	= $payerSubmitterID;
				$recieverId 	= $payerReceiverID;
			}
			$BatchFile = $policiesDetails->Name;
			$fileNameStart = preg_replace('/ /','_',$fileName);			
			if($fileProcessName==''){
				if($groupDetails['group_institution'] != 1 and $navicureFile == false){
					$fileProcessName = 'reprint_emdeon_electronic_file_5010.php';
				}
				else if($navicureFile == true){
					$fileProcessName = 'reprint_navicure_electronic_file_5010.php';
				}
				else{
					$fileProcessName = 'reprint_emdeon_electronic_file_i_5010.php';
				}
			}
			$batchFileStatus = true;
		}
		else{	
			$insDetails = (object)getRecords('insurance_companies','id',$insuranceComp);
			$BatchFile = $insDetails->name;
			//--- MEDICARE INSURANCE COMPANY RECEIVER AND SUBMITTER ID ----
			$recieverId = trim($insDetails->Reciever_id);
			$submitterId = trim($insDetails->payer_type);

			if(empty($recieverId) === true or empty($submitterId) === true || !$DirectBatch){
				$recieverId = $groupDetails['MedicareReceiverId'];
				$submitterId = $groupDetails['MedicareSubmitterId'];
				if((empty($recieverId) === true or empty($submitterId) === true)){
					$recieverId = $groupDetails['rec_id'];
					$submitterId = $groupDetails['sub_id'];
				}
			}
			
			$batchFileStatus = false;
			$fileNameStart = preg_replace('/ /','_',$fileName);
			if($fileProcessName==''){
			//--- RE-GENERATE CLAIM FILE FOR MEDICARE ---
				$fileProcessName = 'reprint_medicare_electronic_file_5010.php';
			}
		}
		
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
	
		if($ClearingHouse && $ClearingHouse['abbr']=='PI' && !empty($ClearingHouse['CL_receiver_id'])){
			$recieverId = $ClearingHouse['CL_receiver_id'];
		}
		
		$recieveSpaceCount = 15 - strlen($recieverId);
		$recieveSpaceStr = NULL;
		for($s=1;$s<=$recieveSpaceCount;$s++){
			$recieveSpaceStr .= ' ';
		}
		
		//--- REQUIRE FILE TO RE-GENERATE CLAIM FILE ----
		$validChargeListDetailsId = array(); $CHLS_Type_Status = $CHLS_Ctrl_Num = array();
		
		/****START OF POS ALT NPI LOGIC*****/
		$encounter_payers_ins_type_q = "SELECT DISTINCT(ic.ins_type) AS ins_types
											FROM insurance_companies ic
											JOIN patient_charge_list pcl ON (pcl.".$insComp." = ic.id) 
											WHERE pcl.encounter_id IN (".$encounter_id.")";
		$encounter_payers_ins_type_res = imw_query($encounter_payers_ins_type_q);
		if($encounter_payers_ins_type_res && imw_num_rows($encounter_payers_ins_type_res)==1){
			$encounter_payers_ins_type_rs	= imw_fetch_assoc($encounter_payers_ins_type_res);
			$encounter_payers_ins_type	= strtoupper($encounter_payers_ins_type_rs['ins_types']);
		
			$overRightPayerWiseNPI = false;
			$curr_file_pcld_ids = $fileDetail->pcld_id;
			if($curr_file_pcld_ids != '' && $encounter_payers_ins_type=='MB'){
				$q_pcld_pos_fac_chk = "SELECT DISTINCT(pcld.posFacilityId) as unique_posFacilityId 
						FROM patient_charge_list_details pcld 
						WHERE pcld.charge_list_detail_id IN ($curr_file_pcld_ids)";
				$res_pcld_pos_fac_chk = imw_query($q_pcld_pos_fac_chk);
				$curr_file_pcld_pos_fac_pkid_arr = array();
				if($res_pcld_pos_fac_chk && imw_num_rows($res_pcld_pos_fac_chk)>0){
					while($rs_pcld_pos_fac_chk = imw_fetch_assoc($res_pcld_pos_fac_chk)){
						$curr_file_pcld_pos_fac_pkid_arr[] = $rs_pcld_pos_fac_chk['unique_posFacilityId'];
					}
					$curr_file_pcld_pos_fac_pkid_str = implode(',',$curr_file_pcld_pos_fac_pkid_arr);
					unset($curr_file_pcld_pos_fac_pkid_arr);
					$q2_pcld_pos_fac_chk = "SELECT DISTINCT(alt_npi_number) as pos_npi FROM pos_facilityies_tbl WHERE pos_facility_id IN (".$curr_file_pcld_pos_fac_pkid_str.")";
					$res2_pcld_pos_fac_chk 	= imw_query($q2_pcld_pos_fac_chk);
					if($res2_pcld_pos_fac_chk && imw_num_rows($res2_pcld_pos_fac_chk)==1){
						$rs2_pcld_pos_fac_chk = imw_fetch_assoc($res2_pcld_pos_fac_chk);
						$overRightPayerWiseNPI = $rs2_pcld_pos_fac_chk['pos_npi'];
					}else $overRightPayerWiseNPI = false;
				}
			}
		}
		/****END OF POS ALT NPI LOGIC*******/
		
		require_once($fileProcessName);
		
		if(strtolower($production_code) == 'p'){
			if(count($validChargeListId) > 0){
				$val_charge_id = join(',',$validChargeListId);
				$qry11 = imw_query("update patient_charge_list set $ins_submit = '1',hcfaStatus = '1' 
						where charge_list_id in ($val_charge_id)");
			}
			if($qry11 && !empty($pcld_ids)){
				$proc_claim_status_qry = "UPDATE patient_charge_list_details SET claim_status='1' WHERE charge_list_detail_id IN ($pcld_ids)";
				$proc_claim_status_res = imw_query($proc_claim_status_qry);
			}
		}
	}
	else{
		$err = 'Claims batch file not exists.';
	}
}
if($inValidChargeListId[0] != ''){
	$errors = join(":",$error);
	$inValidChargeListIds = join(",",$inValidChargeListId);
	$script = 'viewFile('.$fileDetail->Batch_file_submitte_id.',\''.$errors.'\',\''.$inValidChargeListIds.'\');';
}
else{
	$script = 'viewFile(\''.$fileDetail->Batch_file_submitte_id.'\');';
}
?>