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
	//-----GET ACTIVE CLEARING HOUSE DETAILS------
	$ClearingHouses			= $this->ClearingHouse();
	$ClearingHouse			= $ClearingHouses[0];
	
	//---- GET HQ FACILITY DETAILS ---------------
	$facilityDetails = (object)$this->get_facility_detail(0);

	//GET COPAY POLICIES STATUS
	$policiesDetails			= $this->get_copay_policies();
	
	/*//GETTING DETAILS OF POSTED INSURANCE COMPANIES.
	$ins_detail_qry = "SELECT BatchFile,in_house_code,name,Reciever_id,payer_type,claim_type,
						institutional_type,Payer_id,Payer_id_pro 
						FROM insurance_companies
						WHERE id IN ($posted_claim_ins_ids) GROUP BY BatchFile LIMIT 0,1";
	$ins_details_res = imw_query($ins_detail_qry);
	$InsuranceComDetails = imw_fetch_assoc($ins_details_res);
*/
	//--- GET GROUPS DETAILS ---------
	$groupDetails = $this->get_groups_detail($gro_id);
	if($groupDetails['group_institution']=='1'){$chk_institution = $gro_id;}
	$groupNameArr = preg_split('/ /',$groupDetails['name']);
	$group_name_arr = array();
	for($i=0;$i<count($groupNameArr);$i++){
		$group_name_arr[] = $groupNameArr[$i][0];
	}
	$group_name_str = join('',$group_name_arr);
 	
	$file_type = "P";
	
	$submitterId = $groupDetails['sub_id'];
	$recieverId = $groupDetails['rec_id'];
	
	$BatchFile = $ClearingHouse['house_name'];
	$group_Federal_EIN = $groupDetails['group_Federal_EIN'];
	
	$arr_policiesDetails_Name[0] = $ClearingHouse['abbr'];
	$fileProcessName = 'emdeon_electronic_file_5010.php';
	$file_type = "P";
	
	$batchFileStatus = true;
	$fileNameStart = $_REQUEST["ProductionFile"].$groupDetails['group_State'].$file_type;
	
	$submitterSpaceCount = 15 - strlen($submitterId);
	$submitterSpaceStr = '';
	for($s=1;$s<=$submitterSpaceCount;$s++){
		$submitterSpaceStr .= ' ';
	}
	
	if($ClearingHouse && $ClearingHouse['abbr']=='PI' && !empty($ClearingHouse['CL_receiver_id'])){
		$recieverId = $ClearingHouse['CL_receiver_id'];
	}
		
	$recieveSpaceCount = 15 - strlen($recieverId);
	$recieveSpaceStr = '';
	for($s=1;$s<=$recieveSpaceCount;$s++){
		$recieveSpaceStr .= ' ';
	}
	$validChargeListDetailsId = array(); $CHLS_Type_Status = $CHLS_Ctrl_Num = array();
	require($fileProcessName);
}
//}

//-- UPDATE ENCOUNTERS AS SUBMITED ----
if(empty($getData) === false){
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
	}
}
?>