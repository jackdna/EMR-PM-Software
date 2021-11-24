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
File: electronic_billing_bridge.php
Purpose: To communicate with un-changed old 837 EDI creation code.
Access Type: Include file 
*/

if(strtolower($billing_global_server_name)=='cec'){
	include_once(dirname(__FILE__)."/electronic_billing_bridge_cec.php");
	exit;
}
set_time_limit(600);


$validData = array();
$segmentStart = 1;

if($InsComp == 'primaryInsuranceCoId'){
	$setField = 'primarySubmit';
	$setFields = 'Primary';
}
else{
	$setField = 'secondarySubmit';
	$setFields = 'Secondary';
}

$Insurance = $post['instype'];
if(empty($main_charge_list_id) === false){
	$overwrite_reciever_id = true;
	//-----GET ACTIVE CLEARING HOUSE DETAILS------
	$ClearingHouses			= $this->ClearingHouse();
	$ClearingHouse			= $ClearingHouses[0];
	
	//---- GET HQ FACILITY DETAILS ---------------
	$facilityDetails = (object)$this->get_facility_detail(0);

	//GET COPAY POLICIES STATUS
	$policiesDetails			= $this->get_copay_policies();
	
	$navicureFile = false;
	if(trim(strtolower($policiesDetails['Name'])) == "navicure"){
		$navicureFile = true;
	}
	
	if(trim($posted_claim_ins_ids)=='' || $posted_claim_ins_ids==0){die('Internal Processing Error!');}
	
	//GETTING DETAILS OF POSTED INSURANCE COMPANIES.
	$ins_detail_qry = "SELECT BatchFile,in_house_code,name,Reciever_id,payer_type,claim_type,
						institutional_type,Payer_id,Payer_id_pro 
						FROM insurance_companies
						WHERE id IN ($posted_claim_ins_ids) GROUP BY BatchFile LIMIT 0,1";
	$ins_details_res = imw_query($ins_detail_qry);
	$InsuranceComDetails = imw_fetch_assoc($ins_details_res);

	//--- GET GROUPS DETAILS ---------
	$groupDetails = $this->get_groups_detail($gro_id);
	if($groupDetails['group_institution']=='1'){$chk_institution = $gro_id;}
	$groupNameArr = preg_split('/ /',$groupDetails['name']);
	$group_name_arr = array();
	for($i=0;$i<count($groupNameArr);$i++){
		$group_name_arr[] = $groupNameArr[$i][0];
	}
	$group_name_str = join('',$group_name_arr);
 	
	$claim_type = '';$Ins_837i=false; $DirectBatch = false;
	$payerSubmitterID = false; $payerReceiverID = false;
	if(count($InsuranceComDetails)>0){
		$claim_type	= $InsuranceComDetails['claim_type'];
		$Ins_837i	= $InsuranceComDetails['institutional_type']=='INST_ONLY' ? true :  false;
		$DirectBatch= $InsuranceComDetails['BatchFile'];
		if($DirectBatch && trim($InsuranceComDetails['payer_type'])!='' && trim($InsuranceComDetails['Reciever_id'])!=''){
			$payerSubmitterID	= $InsuranceComDetails['payer_type'];
			$payerReceiverID	= $InsuranceComDetails['Reciever_id'];
		}
	}
	
	$file_type = "I";
	
	if(count($InsuranceComDetails) == 0 || (count($InsuranceComDetails) > 0 && intval($claim_type) != '1') || (count($InsuranceComDetails) > 0 && intval($claim_type) == '1' && $DirectBatch==false)){
		$submitterId = $groupDetails['sub_id'];
		$recieverId = $groupDetails['rec_id'];
		if($DirectBatch && $payerSubmitterID && $payerReceiverID){
			$submitterId 	= $payerSubmitterID;
			$recieverId 	= $payerReceiverID;
			$overwrite_reciever_id = false;
		}
		
		$BatchFile = $ClearingHouse['house_name'];
		$group_Federal_EIN = $groupDetails['group_Federal_EIN'];

		$arr_policiesDetails_Name[0] = $ClearingHouse['abbr'];
		
		
		//--- FILE NAME FOR EMDEON ---
		$fileProcessName = 'emdeon_electronic_file_i_5010.php';
		if($chk_institution != $gro_id || (count($InsuranceComDetails) > 0 && !$Ins_837i)){
			$fileProcessName = 'emdeon_electronic_file_5010.php';
			$file_type = "P";
		}else if($chk_institution == $gro_id || (count($InsuranceComDetails) > 0 && $Ins_837i)){
			$fileProcessName = 'emdeon_electronic_file_i_5010.php';
			$file_type = "I";
		}

		if($grouped_ins_override=='INST_PROF'){
			$file_type = "P";
			$fileProcessName = 'emdeon_electronic_file_5010.php';
		}else if($grouped_ins_override=='INST_ONLY'){
			$file_type = "I";
			$fileProcessName = 'emdeon_electronic_file_i_5010.php';
		}

		$batchFileStatus = true;
	}else{	
		$BatchFile = $InsuranceComDetails['name'];
		$batchFileStatus = false;
		
		//--- MEDICARE INSURANCE COMPANY RECEIVER AND SUBMITTER ID ----
		$recieverId = trim($InsuranceComDetails['Reciever_id']);
		$submitterId = trim($InsuranceComDetails['payer_type']);
		
		if((empty($recieverId) === true or empty($submitterId) === true) || !$DirectBatch){
			$recieverId = $groupDetails['MedicareReceiverId'];
			$submitterId = $groupDetails['MedicareSubmitterId'];
			if((empty($recieverId) === true or empty($submitterId) === true)){
				$recieverId = $groupDetails['rec_id'];
				$submitterId = $groupDetails['sub_id'];
			}
		}

		
		$ins_name = strtoupper($InsuranceComDetails['in_house_code']);
		
		$arr_policiesDetails_Name = explode(" ", $ins_name);
		if(strtoupper($arr_policiesDetails_Name[0]) == "MEDICARE"){
			$arr_policiesDetails_Name[0] = "MED";
		}
		$file_type = "P";
		$fileProcessName = 'medicare_electronic_file_5010.php';
	}
	$fileNameStart = $_REQUEST["ProductionFile"].$groupDetails['group_State'].$file_type;
	
	$submitterSpaceCount = 15 - strlen($submitterId);
	$submitterSpaceStr = '';
	for($s=1;$s<=$submitterSpaceCount;$s++){
		$submitterSpaceStr .= ' ';
	}
	
	if($overwrite_reciever_id && $ClearingHouse && $ClearingHouse['abbr']=='PI' && !empty($ClearingHouse['CL_receiver_id'])){
		$recieverId = $ClearingHouse['CL_receiver_id'];
	}
		
	$recieveSpaceCount = 15 - strlen($recieverId);
	$recieveSpaceStr = '';
	for($s=1;$s<=$recieveSpaceCount;$s++){
		$recieveSpaceStr .= ' ';
	}
	$validChargeListDetailsId = $CHLS_Type_Status = $CHLS_Ctrl_Num = $validChargeListId = array(); $getData = false;
	$pcld_ids = '';
	//die($fileProcessName);
	require($fileProcessName);
}
//}

//-- UPDATE ENCOUNTERS AS SUBMITED ----
if($getData){
	$_SESSION['interchangecontrolnumber']++;
	if($ProductionFile != 'T'){
		if(count($validChargeListId)>0){
			$validChargeListIdStr = implode(',',$validChargeListId);
		}
		$update_qry = "update patient_charge_list set `$setField` = 1,hcfaStatus = 1 
						where charge_list_id in ($validChargeListIdStr)";
		$qry = imw_query($update_qry);
		if($qry && !empty($pcld_ids)){
			$proc_claim_status_qry = "UPDATE patient_charge_list_details SET claim_status='1' WHERE charge_list_detail_id IN ($pcld_ids)";
			$proc_claim_status_res = imw_query($proc_claim_status_qry);
		}
		
		/******UPDATE void_notify_date for created claim******/
		$q = "UPDATE patient_charge_list SET void_claim_date = '".date('Y-m-d H:i:s')."' WHERE charge_list_id in ($validChargeListIdStr) AND void_notify='1' AND (void_claim_date='' or void_claim_date='0000-00-00 00:00:00')";
		$res = imw_query($q);
	}
}
?>