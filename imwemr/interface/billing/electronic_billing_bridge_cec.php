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
File: electronic_billing_bridge_cec.php
Purpose: Communicate with old unchanged 837 creation code (cec specific)
Access Type: Include file 
*/
set_time_limit(600);

$validData = array();
$segmentStart = 1;
$separation_done = false;
$merger_file = false;

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
	//---- GET HQ FACILITY DETAILS ---------------
	$facilityDetails = (object)$this->get_facility_detail(0);

	//GET COPAY POLICIES STATUS
	$policiesDetails			= $this->get_copay_policies();
	
	$navicureFile = false;
	if(trim(strtolower($policiesDetails['Name'])) == "navicure"){
		$navicureFile = true;
	}

	//GETTING DETAILS OF POSTED INSURANCE COMPANIES.
	$ins_detail_qry = "SELECT BatchFile,in_house_code,name,Reciever_id,payer_type 
						FROM insurance_companies 
						WHERE id IN ($Insurance) 
							AND claim_type = '1' 
							AND BatchFile = '1' 
						GROUP BY BatchFile LIMIT 0,1";
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
	if(count($InsuranceComDetails) == 0){			
		$submitterId = $groupDetails['sub_id'];
		$recieverId = $groupDetails['rec_id'];
		$BatchFile = $policiesDetails['Name'];
		$group_Federal_EIN = $groupDetails['group_Federal_EIN'];

		$arr_policiesDetails_Name = explode(" ", $policiesDetails['Name']);
		if(strtoupper($arr_policiesDetails_Name[0]) == "EMDEON"){
			$arr_policiesDetails_Name[0] = "EMD";
		}
		
		$fileNameStart = strtoupper($group_name_str.'_'.$arr_policiesDetails_Name[0]).'_';
		
		$fileNameStart = preg_replace('/ /','_',$fileNameStart);
		$file_type = "I";
		if($createClaims != '' and $chk_institution != $gro_id){
			$file_type = "P";	
		}
		//if(isset($_REQUEST['opt5010']) && $_REQUEST['opt5010']=='yes'){
			$fileNameStart = $_REQUEST["ProductionFile"].$groupDetails['group_State'].$file_type;
		//}
		//--- FILE NAME FOR EMDEON ---
		$fileProcessName = 'ghn_electronic_file_i_5010.php';
		if($createClaims != '' and $chk_institution != $gro_id){
			$fileProcessName = 'emdeon_electronic_file_5010.php';
		}
		
		//--- NAVICURE FILE NAME ----
		if($navicureFile === true){
			if($chk_institution != $gro_id){
				$fileProcessName = 'navicure_electronic_file_5010.php';
			}else if($chk_institution == $gro_id){
				$fileProcessName = 'ghn_electronic_file_i_5010.php';
			}
		}
		$batchFileStatus = true;
	}
	else{	
		$BatchFile = $InsuranceComDetails['name'];
		$batchFileStatus = false;
		
		//--- MEDICARE INSURANCE COMPANY RECEIVER AND SUBMITTER ID ----
		$recieverId = trim($InsuranceComDetails[0]['Reciever_id']);
		$submitterId = trim($InsuranceComDetails[0]['payer_type']);
		
		if(empty($recieverId) === true or empty($submitterId) === true){
			$recieverId = $groupDetails['MedicareReceiverId'];
			$submitterId = $groupDetails['MedicareSubmitterId'];
		}
		
		$ins_name = strtoupper($InsuranceComDetails[0]['in_house_code']);
		
		$arr_policiesDetails_Name = explode(" ", $ins_name);
		if(strtoupper($arr_policiesDetails_Name[0]) == "MEDICARE"){
			$arr_policiesDetails_Name[0] = "MED";
		}
		$fileNameStart = $group_name_str.'_'.$arr_policiesDetails_Name[0].'_';
		
		$fileNameStart = preg_replace('/ /','_',$fileNameStart);
		$file_type = "I";
		if($createClaims != '' and $chk_institution != $gro_id){
			$file_type = "P";	
		}
		//--- FILE NAME FOR MEDICARE ---
		$fileNameStart = $_REQUEST["ProductionFile"].$groupDetails['group_State'].$file_type;
		$fileProcessName = 'medicare_electronic_file_5010.php';
	}

	$validChargeListDetailsId = array();
	//$submitterId=str_pad($submitterId,15);
	//$recieverId=str_pad($recieverId,15);
	$submitterSpaceCount = 15 - strlen($submitterId);
	$submitterSpaceStr = '';
	for($s=1;$s<=$submitterSpaceCount;$s++){
		$submitterSpaceStr .= ' ';
	}
		
	$recieveSpaceCount = 15 - strlen($recieverId);
	$recieveSpaceStr = '';
	for($s=1;$s<=$recieveSpaceCount;$s++){
		$recieveSpaceStr .= ' ';
	}
	
	if($fileProcessName == 'ghn_electronic_file_i_5010.php'){
		require_once('inst_only_check_5010.php');
	}else{
		require_once($fileProcessName);
	}
}

if($separation_done == true){
	$validChargeListId = $arr_valid;
	$invalidChargeListId = $arr_invalid;
	$res = $arr_res;
}

//-- UPDATE ENCOUNTERS AS SUBMITED ----
if(empty($getData) === false){
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
	}
}
?>