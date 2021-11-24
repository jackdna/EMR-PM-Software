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
$title = "Service Charges";
require_once("acc_header.php");
include_once(dirname(__FILE__)."/../../library/classes/class.mpay.php");
require_once(dirname(__FILE__)."/../../library/classes/cls_common_function.php");
require_once(dirname(__FILE__)."/../../library/classes/work_view/wv_functions.php");
require_once(dirname(__FILE__)."/../../library/classes/work_view/Poe.php");
require_once(dirname(__FILE__)."/../../library/classes/work_view/CPT.php");
require_once(dirname(__FILE__)."/../../library/classes/work_view/Facility.php");
$objMpay = new mPay;
$objCLSCommonFunction = new CLSCommonFunction;
$oFacilityfun = new Facility();
$postButton = 'Post Charges';
$stop_clm_status=1;
$sm_unique_id=1;
if(!$encounter_id){
	if(isset($_SESSION['encounter_id'])&&!empty($_SESSION['encounter_id'])){
		if(!outstandingSBExists($_SESSION['encounter_id'])){
			$encounter_id = $_SESSION['encounter_id'];
			$_SESSION['encounter_id'] = NULL;
		}
	}
}

//--------------------------------copay Apply Array start---------------------------------------
$copay_apply_array=array("99212","99213","99214","99215","99201","99202","99203","99204","99205","99241","99242","99243","99244","99245","92012","92013","92014","92002","92003","92004");
$visit_code_arr=array("92001","92011","92002","92012","92004","92014","99201","99202","99203","99204","99205","99241","99242","99243","99244","99245","99211","99212","99213","99214","99215");						 
//--------------------------------copay Apply Array end------------------------------------------
$patient_id = $_SESSION['patient'];
$authUser = $_SESSION['authUser'];
$facilityId = $_SESSION['login_facility'];
$operator_id=$_SESSION['authId'];
$del_time = date('H:i:s');

//------------------------------------- Default Patient Data ---------------------------------------------
$qry = imw_query("select fname,mname,lname,default_facility,providerID,vip,primary_care_id,pat_account_status,noBalanceBill,DOB from patient_data where id = '$patient_id'");
$patientDetail = imw_fetch_object($qry);
$patientName = ucwords(trim($patientDetail->lname.", ".$patientDetail->fname." ".$patientDetail->mname));
$default_facility = $patientDetail->default_facility;
$default_providerID  = $patientDetail->providerID;
$default_vip = $patientDetail->vip;
$noBalanceBill = $patientDetail->noBalanceBill;
$date_of_birth = get_date_format($patientDetail->DOB);
//------------------------------------- Default Patient Data ---------------------------------------------

//------------------------------------- Policiy detail ---------------------------------------------
$qry = imw_query("SELECT sec_copay_collect_amt,sec_copay_for_ins,vip_copay_not_collect,vip_ref_not_collect,vip_bill_not_pat,
				vip_write_off_code,anes_time_divisor,return_chk_proc,icd_code FROM copay_policies WHERE policies_id='1'");
$policyQryRes = imw_fetch_array($qry);
$return_chk_proc = $policyQryRes['return_chk_proc'];
$policies_icd_code = $policyQryRes['icd_code'];
if($return_chk_proc==""){
	$return_chk_proc="rtn-chk";
}
$sec_copay_collect_amt = $policyQryRes['sec_copay_collect_amt'];
$sec_copay_for_ins = $policyQryRes['sec_copay_for_ins'];
if($policyQryRes['anes_time_divisor']!="" && $policyQryRes['anes_time_divisor']!=0){
	$anes_time_divisor = $policyQryRes['anes_time_divisor'];
}else{
	$anes_time_divisor = 1;
}	
$default_vip_chk=false;
if($default_vip>0 && $encounter_id==""){
	$vip_copay_not_collect = $policyQryRes['vip_copay_not_collect'];
	$vip_ref_not_collect = $policyQryRes['vip_ref_not_collect'];
	$vip_bill_not_pat = $policyQryRes['vip_bill_not_pat'];
	$vip_write_off_code = $policyQryRes['vip_write_off_code'];
	$default_vip_chk=true;
}
//------------------------------------- Policiy detail ---------------------------------------------

//-------- Start To Get Data From Encounter ---------
if($_REQUEST['del_charge_list_id']>0){
	$del_charge_list_id=$_REQUEST['del_charge_list_id'];
	$whr_del_charge_list_id="charge_list_id='$del_charge_list_id' and ";
	$whr_del_chld="";
}else{
	$whr_del_charge_list_id=" del_status='0' and ";
	$whr_del_chld=" del_status='0' and ";
}

if($encounter_id){
	if($_REQUEST['uniqueurl']>0){
		set_payment_trans($encounter_id,'',$stop_clm_status);
		patient_proc_bal_update($encounter_id);
	}
	patient_bal_update($encounter_id);
	$qry = imw_query("select * from patient_charge_list where $whr_del_charge_list_id encounter_id = '$encounter_id' and patient_id='$patient_id'");
	$chargeDetails = imw_fetch_object($qry);
	$curCaseId = $chargeDetails->case_type_id;
	$charge_list_id = $chargeDetails->charge_list_id;
	$del_status = $chargeDetails->del_status;
	$priInsComId_chk = $chargeDetails->primaryInsuranceCoId;
	$secInsComId_chk = $chargeDetails->secondaryInsuranceCoId;
	$terInsComId_chk = $chargeDetails->tertiaryInsuranceCoId;
	$referral = $chargeDetails->referral;
	$optional_referral = $chargeDetails->optional_referral;
	$reff_phy_id = $chargeDetails->reff_phy_id;
	$copay = $chargeDetails->copay;
	$copay_chk = $chargeDetails->copay;
	$copayPaid = $chargeDetails->copayPaid;
	$preAmtPaid = $chargeDetails->amtPaid;
	$submitted = $chargeDetails->submitted;
	$Re_submitted = $chargeDetails->Re_submitted;
	$enc_dos = $chargeDetails->date_of_service;
	$totalAmt_chk = $chargeDetails->totalAmt;
	$totalBalance_chk  = $chargeDetails->totalBalance;
	$primary_paid_chk  = $chargeDetails->primary_paid;
	$secondary_paid_chk  = $chargeDetails->secondary_paid;
	$tertiary_paid_chk  = $chargeDetails->tertiary_paid;
	$approvedTotalAmt_chk  = $chargeDetails->approvedTotalAmt;
	$letter_sent_date  = $chargeDetails->letter_sent_date;
	$chk_self_readonly=false;
	if($primary_paid_chk=='true' || $secondary_paid_chk=='true' || $tertiary_paid_chk=='true'){
		$chk_self_readonly=true;
	}
	
	if($chargeDetails->sch_app_id==0){
		set_app_id_chl($patient_id,$enc_dos,'chl');
	}
	
	if($chargeDetails->primaryInsuranceCoId){
		$ins_arr[]=$chargeDetails->primaryInsuranceCoId;
	}
	if($chargeDetails->secondaryInsuranceCoId){
		$ins_arr[]=$chargeDetails->secondaryInsuranceCoId;
	}
	if($chargeDetails->tertiaryInsuranceCoId){
		$ins_arr[]=$chargeDetails->tertiaryInsuranceCoId;
	}
	
	$enc_providers_arr[$chargeDetails->primaryProviderId]=$chargeDetails->primaryProviderId;
	$enc_providers_arr[$chargeDetails->secondaryProviderId]=$chargeDetails->secondaryProviderId;
	$enc_providers_arr[$chargeDetails->tertiaryProviderId]=$chargeDetails->tertiaryProviderId;
}

if(constant('ACC_DFT_GENERATION')==true){
	$hl7_qry = imw_query("SELECT account_num FROM `hl7_received_accno` WHERE `patient_id` ='$patient_id' AND `dt_of_visit` = '$enc_dos' ORDER BY `entered_dt` DESC LIMIT 0,1");
	$hl7_row = imw_fetch_array($hl7_qry);
	$hl7_account_num=$hl7_row['account_num'];
}
if((float)$totalAmt_chk==(float)$totalBalance_chk){
	$chg_proc_amt_chk='no';
}else{
	$chg_proc_amt_chk='yes';
}
if($enc_dos==""){
	$enc_dos=date('Y-m-d');
}
if(!$encounter_id){
	$curCaseId = $_SESSION['currentCaseid'];
}else{
	$curCaseId = $chargeDetails->case_type_id;
}
//-------- End To Get Data From Encounter ---------

//------------------------------------- ReSubmit or post charges Checking ---------------------------------------------
if($encounter_id){
	$reSubmitDetails = imw_query("select submited_id from submited_record where encounter_id = '$encounter_id'");
}
if(imw_num_rows($reSubmitDetails)>0){
	$postButton = 'Re-submit';
}
//------------------------------------- ReSubmit or post charges Checking ---------------------------------------------


//-------------------- Provider/Case Type/Facility By Default Scheduler appointment ------------------
if($encounter_id==""){
	$qry_case_id = "select case_type_id,auth_pri_id,auth_sec_id,auth_ter_id,sa_doctor_id,sa_facility_id from schedule_appointments,users 
					where users.id=schedule_appointments.sa_doctor_id and users.user_type=1 and 
					sa_doctor_id>0 and sa_app_start_date='$enc_dos' and sa_patient_id='$patient_id'
					and sa_patient_app_status_id NOT IN (203,201,18,19,20) order by sa_app_starttime desc limit 0,1";												
	$run_case_id = imw_query($qry_case_id);	
	$fet_case_list=imw_fetch_array($run_case_id);
	$sch_auth_pri_id = $fet_case_list['auth_pri_id'];
	$sch_auth_sec_id = $fet_case_list['auth_sec_id'];
	$sch_auth_ter_id = $fet_case_list['auth_ter_id'];
	if($fet_case_list['case_type_id']>0){
		$curCaseId=$fet_case_list['case_type_id'];
	}
	$sa_doctor_id =$fet_case_list['sa_doctor_id'];
	$sa_facility_id =$fet_case_list['sa_facility_id'];
}
//--------------------  Provider/Case Type/Facility By Default Scheduler appointment  ------------------

if($encounter_id==""){
	if($_SESSION['acc_usr_data']['caseTypeText'][$patient_id]){
		$curCaseId=$_SESSION['acc_usr_data']['caseTypeText'][$patient_id];
	}
}
$qry = imw_query("SELECT insct.case_name, insct.vision, insct.normal, insc.ins_caseid, insc.ins_case_type,insc.start_date,insc.end_date 
		FROM insurance_case_types insct 
		JOIN insurance_case insc ON (insc.ins_case_type=insct.case_id AND insc.case_status ='Open') 
		JOIN insurance_data insd ON (insd.ins_caseid=insc.ins_caseid AND insd.provider >0) 
		JOIN insurance_companies inscomp ON (inscomp.id=insd.provider AND inscomp.in_house_code !='n/a') 
		WHERE insc.patient_id='$patient_id'
		GROUP BY insc.ins_caseid 
		ORDER BY insc.ins_case_type");
$first_case=$openInsCaseId=0;
while($caseRes=imw_fetch_array($qry)){	
	$ins_case_type = $caseRes['ins_case_type'];
	$vision= $caseRes['vision'];
	$caseName = $caseRes['case_name'];
	$ins_caseid = $caseRes['ins_caseid'];
	$insCasesArr = $caseName.'-'.$ins_caseid;
	$select = false;
	if($curCaseId == $ins_caseid){
		$select = true;
		$sel = 'selected="selected"';
		$paidInsCase = $insCasesArr;
		$vision_chk=$vision;
		$chk_ins_case_type=$ins_case_type;
	}
	else{
		$select = false;
		$sel = '';
	}
	//$openInsCaseId=0;
	if($select == true || $first_case == 0){
		$openInsCaseId = $ins_caseid;
		$case_type_normal = $caseRes['normal'];
	}
	$insCasesOption .= '
		<option value="'.$insCasesArr.'" '.$sel.'>'.$insCasesArr.'</option>
	';
	$first_case++;
	if($openInsCaseId>0){
		$effective_date=$caseRes['start_date'];
		$expiration_date=$caseRes['end_date'];
		$st_date=get_date_format($effective_date);
		$end_date=get_date_format($expiration_date);
	}
}
			
if($effective_date=="0000-00-00"){
	$st_date="";
}
if($expiration_date=="0000-00-00"){
	$end_date="";
}
$alrt_ins_status=0;

//------ Start Get Primary Ins Company Id Without Encounter Id ------------

//------------------------ Auth number ------------------------//
$qry = "select auth_name,a_id,AuthAmount,ins_data_id,ins_type,ins_case_id,ins_provider,auth_date,end_date,auth_provider,auth_cpt_codes from patient_auth where patient_id='$patient_id' and ins_case_id='$openInsCaseId' and auth_status='0' and no_of_reffs>0 order by a_id desc";
$res = imw_query($qry);
while($row = imw_fetch_array($res)){
	$auth_data[$row['ins_data_id']][$row['ins_type']][]=$row;
}
//------------------------ Auth number ------------------------//

//------------------------ Insurance Data ------------------------//
$qry = "select * from insurance_data where pid='$patient_id' and ins_caseid='$openInsCaseId' and provider > '0' order by actInsComp desc";
$res = imw_query($qry);
while($row = imw_fetch_array($res)){
	$ins_data[$row['id']]=$row;
	$ins_arr[$row['provider']]=$row['provider'];
}

//------------------------ Insurance Company ------------------------//
if(count($ins_arr)>0){
	$ins_arr_imp=implode(',',array_unique($ins_arr));	
	$qry = "select * from insurance_companies WHERE id in($ins_arr_imp) order by name ASC";
	$res = imw_query($qry);
	while($row = imw_fetch_array($res)){
		$ins_comp_data[$row['id']]=$row;
		$ins_in_house_code_id[$row['in_house_code']]=$row['id'];
	}
}
//------------------------ Insurance Company ------------------------//
$priInsComId=$secInsComId=$terInsComId=0;

//STATUS ARRAY
$statusArr = array();
$qry_claim_status =imw_query("SELECT id,status_name FROM claim_status where del_status='0' ORDER BY status_name ASC");
while($fet_claim_status=imw_fetch_assoc($qry_claim_status)){
	$statusArr[$fet_claim_status['id']]=$fet_claim_status['status_name'];
}

foreach($ins_data as $key=>$val){
	$ins_detail=$ins_data[$key];
	$effective_date_exp=explode(' ',$ins_detail['effective_date']);
	$expiration_date_exp=explode(' ',$ins_detail['expiration_date']);
	
	if($priInsComId<=0 && $ins_detail['type']=="primary" && $effective_date_exp[0]<=$enc_dos && ($ins_detail['expiration_date']=='0000-00-00 00:00:00' || $expiration_date_exp[0]>=$enc_dos)){
		$priInsComId = $ins_detail['provider'];
		$copay = $ins_detail['copay'];
		$insur_chk_copay1 = explode('/',$ins_detail['copay']);
		$insur_chk_copay = $insur_chk_copay1[0];
		$insur_id_pri = $ins_detail['id'];
		$auth_req_pri = $ins_detail['auth_required'];
		$referal_req_pri  = $ins_detail['referal_required'];
		$self_pay_provider = $ins_detail['self_pay_provider'];
		
		if($priInsComId_chk!=$priInsComId && $encounter_id>0){
			$alrt_ins_status=1;
		}
		
		$priInsComDetails=(object)$ins_comp_data[$priInsComId];
		$insurance_co1 = $priInsComDetails->in_house_code;
		$Payer_id_pro = $priInsComDetails->Payer_id_pro;
		$Payer_id = $priInsComDetails->Payer_id;
		$ins_icd_code = $priInsComDetails->icd_code;
		$institutional_type = $priInsComDetails->institutional_type;
		
		$auth_arr=array();
		if($auth_req_pri=='Yes'){
			foreach($auth_data[$insur_id_pri][1] as $key=>$val){
				$authQryRes=$auth_data[$insur_id_pri][1][$key];
				if($authQryRes['auth_provider']=='0' && $authQryRes['auth_cpt_codes']=="" && $authQryRes['auth_date']<=$enc_dos && ($authQryRes['end_date']=="0000-00-00" || $authQryRes['end_date']>=$enc_dos)){
					if($sch_auth_pri_id>0){
						if($sch_auth_pri_id==$authQryRes['a_id']){
							$authDataArr[1]['auth_name'][] = $authQryRes['auth_name'];
							$authDataArr[1]['AuthAmount'][] = $authQryRes['AuthAmount'];
							$authDataArr[1]['a_id'][] = $authQryRes['a_id'];
						}
					}else{
						$authDataArr[1]['auth_name'][] = $authQryRes['auth_name'];
						$authDataArr[1]['AuthAmount'][] = $authQryRes['AuthAmount'];
						$authDataArr[1]['a_id'][] = $authQryRes['a_id'];
					}
					$auth_arr[]=array($authQryRes['auth_name'],$xyz, $authQryRes['auth_name']);
					$stringAllAuth.="'".str_replace("'","",$authQryRes['auth_name'])."',";
				}
			}
		}	
		$stringAllAuth = substr($stringAllAuth,0,-1);
	}
	if($secInsComId<=0 && $ins_detail['type']=="secondary" && $effective_date_exp[0]<=$enc_dos && ($ins_detail['expiration_date']=='0000-00-00 00:00:00' || $expiration_date_exp[0]>=$enc_dos)){
		$secInsComId = $ins_detail['provider'];
		$copay_sec = $ins_detail['copay'];
		$insur_chk_copay_sec1 = explode('/',$ins_detail['copay']);
		$insur_chk_copay_sec = $insur_chk_copay_sec1[0];
		$insur_id_sec = $ins_detail['id'];
		$auth_req_sec = $ins_detail['auth_required'];
		$referal_req_sec  = $ins_detail['referal_required'];
		
		if($secInsComId_chk!=$secInsComId && $encounter_id>0){
			$alrt_ins_status=1;
		}
		
		$secInsComDetails=(object)$ins_comp_data[$secInsComId];
		$insurance_co2 = $secInsComDetails->in_house_code;
		
		if($auth_req_sec=='Yes'){
			foreach($auth_data[$insur_id_sec][2] as $key=>$val){
				$authQryRes=$auth_data[$insur_id_sec][2][$key];	
				if($authQryRes['auth_provider']=='0' && $authQryRes['auth_cpt_codes']=="" && $authQryRes['auth_date']<=$enc_dos && ($authQryRes['end_date']=="0000-00-00" || $authQryRes['end_date']>=$enc_dos)){	
					if($sch_auth_sec_id>0){
						if($sch_auth_sec_id==$authQryRes['a_id']){
							$authDataArr[2]['auth_name'][] = $authQryRes['auth_name'];
							$authDataArr[2]['AuthAmount'][] = $authQryRes['AuthAmount'];
							$authDataArr[2]['a_id'][] = $authQryRes['a_id'];
						}
					}else{
						$authDataArr[2]['auth_name'][] = $authQryRes['auth_name'];
						$authDataArr[2]['AuthAmount'][] = $authQryRes['AuthAmount'];
						$authDataArr[2]['a_id'][] = $authQryRes['a_id'];
					}
					$auth_arr_sec[]=array($authQryRes['auth_name'],$xyz, $authQryRes['auth_name']);
					$stringAllAuth_sec.="'".str_replace("'","",$authQryRes['auth_name'])."',";
				}
			}
		}	
		$stringAllAuth_sec = substr($stringAllAuth_sec,0,-1);
	}
	
	if($terInsComId<=0 && $ins_detail['type']=="tertiary" && $effective_date_exp[0]<=$enc_dos && ($ins_detail['expiration_date']=='0000-00-00 00:00:00' || $expiration_date_exp[0]>=$enc_dos)){
		$terInsComId = $ins_detail['provider'];
		$copay_ter = $ins_detail['copay'];
		$insur_chk_copay_ter = $ins_detail['copay'];
		$insur_id_tri = $ins_detail['id'];
		$auth_req_tri = $ins_detail['auth_required'];
		$referal_req_ter  = $ins_detail['referal_required'];
		
		if($terInsComId_chk!=$terInsComId && $encounter_id>0){
			$alrt_ins_status=1;
		}

		$terInsComDetails=(object)$ins_comp_data[$terInsComId];
		$insurance_co3 = $terInsComDetails->in_house_code;
		
		if($auth_req_tri=='Yes'){
			foreach($auth_data[$insur_id_tri][3] as $key=>$val){
				$authQryRes=$auth_data[$insur_id_tri][3][$key];		
				if($authQryRes['auth_provider']=='0' && $authQryRes['auth_cpt_codes']=="" && $authQryRes['auth_date']<=$enc_dos && ($authQryRes['end_date']=="0000-00-00" || $authQryRes['end_date']>=$enc_dos)){
					if($sch_auth_ter_id>0){
						if($sch_auth_ter_id==$authQryRes['a_id']){
							$authDataArr[3]['auth_name'][] = $authQryRes['auth_name'];
							$authDataArr[3]['AuthAmount'][] = $authQryRes['AuthAmount'];
							$authDataArr[3]['a_id'][] = $authQryRes['a_id'];
						}
					}else{
						$authDataArr[3]['auth_name'][] = $authQryRes['auth_name'];
						$authDataArr[3]['AuthAmount'][] = $authQryRes['AuthAmount'];
						$authDataArr[3]['a_id'][] = $authQryRes['a_id'];
					}
					$auth_arr_tri[]=array($authQryRes['auth_name'],$xyz, $authQryRes['auth_name']);
					$stringAllAuth_tri.="'".str_replace("'","",$authQryRes['auth_name'])."',";
				}
			}
		}	
		$stringAllAuth_tri = substr($stringAllAuth_tri,0,-1);
	}
	if($encounter_id>0){
		if($ins_detail['type']=="primary" && $priInsComId_chk!=$priInsComId){
			$alrt_ins_status=1;
		}
		if($ins_detail['type']=="secondary" && $secInsComId_chk!=$secInsComId){
			$alrt_ins_status=1;
		}
		if($ins_detail['type']=="tertiary" && $terInsComId_chk!=$terInsComId){
			$alrt_ins_status=1;
		}
	}
}
	
if(count($authDataArr[1]['auth_name'])>0){
	$auth_no=$authDataArr[1]['auth_name'][0];
	$auth_id=$authDataArr[1]['a_id'][0];
	$auth_amount=$authDataArr[1]['AuthAmount'][0];
	$auth_arr=$auth_arr;
	$stringAllAuth=$stringAllAuth;
}else if(count($authDataArr[2]['auth_name'])>0){
	$auth_no=$authDataArr[2]['auth_name'][0];
	$auth_id=$authDataArr[2]['a_id'][0];
	$auth_amount=$authDataArr[2]['AuthAmount'][0];
	$auth_arr=$auth_arr_sec;
	$stringAllAuth=$stringAllAuth_sec;
}else if(count($authDataArr[3]['auth_name'])>0){
	$auth_no=$authDataArr[3]['auth_name'][0];
	$auth_id=$authDataArr[3]['a_id'][0];
	$auth_amount=$authDataArr[3]['AuthAmount'][0];
	$auth_arr=$auth_arr_tri;
	$stringAllAuth=$stringAllAuth_tri;
}

//------------------------ Insurance Data ------------------------//

//------------------------	START GETTING DATA FOR MENUS TO Provider	------------------------//
$phy_id_cn=$GLOBALS['arrValidCNPhy'];
$sql = imw_query("select id,fname,lname,mname,sx_physician,user_type,Enable_Scheduler,delete_status,default_group from users order by lname ASC");
while($row=imw_fetch_array($sql)){
	if($row['delete_status']==0){			
		$cat_id = $row["id"];		
		$fname=$row["fname"];
		$lname=$row["lname"];
		$mname="";
		if($row["mname"]!=""){
			$mname=" ".trim($row["mname"]).'.';
		}
		$name=$lname.", ".$fname.$mname;
		if($row["Enable_Scheduler"]=='1' || in_array($row["user_type"],$phy_id_cn) || in_array($row["id"],$enc_providers_arr)){
			$arrProviderCodes[] = array($name,$xyz, $name);
			$stringAllProvider.="'".addslashes($name)."',";
			if($row["sx_physician"]>0){
				$sx_physician_arr[$row["id"]]=$row["id"];
			}
		}
	}
	$usr_name_arr[$row["id"]]=$row;
}		
$stringAllProvider = substr($stringAllProvider,0,-1);
//------------------------	END GETTING DATA FOR MENUS TO Provider	------------------------//

//----- Start To Get Refferal Detail Without Encounter Id ------

$reffDetail = getPatientReffPhy($patient_id,$openInsCaseId,'primary');
if(!$optional_referral){
	//----- ReffDetail For Secondary Ins Company -------
	$reffDetails = getPatientReffPhy($patient_id,$openInsCaseId,'secondary');
	$optional_referral = $reffDetails->reffral_no;
}

//---- Get Reffering Physician Id -------------
if($_REQUEST['refferingPhysician']!=""){
	$reff_phy_id=$_REQUEST['refferingPhysician'];
}else{
	if($_REQUEST['Reffer_physician']<>""){
		$Reffer_physician_chk=$_REQUEST['Reffer_physician'];
		list($reff_phy_id,$reff_phy_name) = $objCLSCommonFunction->chk_create_ref_phy($Reffer_physician_chk,7);
	}else{
		if($_REQUEST['refferingPhysician']!=""){
			$reff_phy_id=$_REQUEST['refferingPhysician'];
		}else{
			if(!$reff_phy_id){
				if($reffDetail){
					$reff_phy_id = $reffDetail->reff_phy_id;
				}
				else{
					$reff_phy_id = $patientDetail->primary_care_id;	
				}
			}
		}
	}	
}	

if($reff_phy_id){
	if($_REQUEST['Reffer_physician']!=""){
		$qry = imw_query("update patient_charge_list set reff_phy_id = '$reff_phy_id' where encounter_id = '$encounter_id' and patient_id='$patient_id'");
	}
	$refPhySicianName=$objCLSCommonFunction->get_ref_phy_name($reff_phy_id);
}
else{
	$patientPrimaryCareDetails=$usr_name_arr[$patientDetail->providerID];
	if($patientPrimaryCareDetails['lname'] && $patientPrimaryCareDetails['fname']){
		$refPhySicianName = $patientPrimaryCareDetails['lname'].', ';
		$refPhySicianName .= $patientPrimaryCareDetails['fname'];
		if($patientPrimaryCareDetails['mname']){
			$refPhySicianName .= " ".$patientPrimaryCareDetails['mname'];
		}
	}
}

//----- End To Get Refferal Detail Without Encounter Id ------

//------------------- START GET OPERATOR INITIALS-----------------------
$operatorDetails=$usr_name_arr[$operator_id];
$operatorName_mod = substr($operatorDetails['fname'],0,1).substr($operatorDetails['mname'],0,1).substr($operatorDetails['lname'],0,1);
$operatorName_mod_id=$operator_id;
//-------------------END GET OPERATOR INITIALS-----------------------
	
//------------------------	START GETTING DATA FOR MENUS TO Procedure CATEGORY-----------------------//
$visit_code_arr_id=array();
			
$sql = imw_query("select * from cpt_fee_tbl WHERE cpt_cat_id>0 order by cpt_prac_code asc, delete_status desc");
while($row=imw_fetch_array($sql)){
	$cpt_fee_data[$row['cpt_fee_id']]=$row;
	$cpt_prac_data_arr[$row['cpt_prac_code']]=$row;
	$cpt_4code_data_arr[$row['cpt4_code']]=$row;
	$cpt_desc_data_arr[$row['cpt_desc']]=$row;
	$cpt_tax_data[$row['cpt_fee_id']]=$row['cpt_tax'];
	if(strtolower($row['status'])=='active' && $row['delete_status']=='0'){
		$cpt_fee_cat_data[$row['cpt_cat_id']][]=$row;
	}
}

$sql = imw_query("select cpt_cat_id,cpt_category from cpt_category_tbl order by cpt_category ASC");
while($row=imw_fetch_array($sql)){
	if($row['cpt_cat_id']>0){
		$arrSubOptions=array();
		foreach($cpt_fee_cat_data[$row['cpt_cat_id']] as $key=>$val){
			$rowCodes=$cpt_fee_cat_data[$row['cpt_cat_id']][$key];
			//$arrSubOptions[] = array($rowCodes["cpt_prac_code"]."-".$rowCodes["cpt_desc"],$xyz, $rowCodes["cpt_prac_code"]);
			$arrCptCodesAndDesc[] = $rowCodes["cpt_fee_id"];
			$arrCptCodesAndDesc[] = $rowCodes["cpt_prac_code"];
			$arrCptCodesAndDesc[] = $rowCodes["cpt_desc"]; 	
			
			$code = $rowCodes["cpt_prac_code"];
			$cpt_desc = $rowCodes["cpt_desc"];
			$stringAllProcedures.="'".str_replace("'","",$code)."',";	
			$stringAllProcedures.="'".str_replace("'","",$cpt_desc)."',";
			
			$adm_dx_codes_arr[$rowCodes["cpt_fee_id"]]=$rowCodes["dx_codes"];
			
			if(in_array($rowCodes["cpt_prac_code"],$visit_code_arr)){
				$visit_code_arr_id[$rowCodes["cpt_fee_id"]]=$rowCodes["cpt_fee_id"];
			}
		}
	}
}
$stringAllProcedures = substr($stringAllProcedures,0,-1);
$visit_code_id_imp = implode(',',$visit_code_arr_id);
$visit_code_arr_imp = implode(',',$visit_code_arr);
//------------------------	END GETTING DATA FOR MENUS TO CATEGORY OF Procedures	------------------------//

//------------------------	START GETTING DATA FOR MENUS TO Modifiers	------------------------//
$sql = imw_query("select * from modifiers_tbl WHERE delete_status = '0' order by mod_prac_code ASC");
while($row=imw_fetch_array($sql)){	
	$code=$row["mod_prac_code"];
	$mod_description=$row["mod_description"];
	if(in_array(strtolower($billing_global_server_name), array('cheemaeye'))){
		$stringAllModifiers.="'".str_replace("'","",$code.' - '.$mod_description)."',";
	}else{
		$stringAllModifiers.="'".str_replace("'","",$code)."',";
	}
	$stringAllModifiers.="'".str_replace("'","",$mod_description)."',";
	$str_arr_proc_desc_code[]=str_replace("'","",$code.' - '.$row["mod_description"]);
	$str_arr_desc_code[]=str_replace("'","",$row["mod_description"]);
	$str_arr_proc_code[]=$row["mod_prac_code"];
	$mod_code_arr[$row["modifiers_id"]]=$row["mod_prac_code"];
	$mod_prac_code_id[strtolower($row["mod_prac_code"])]=$row["modifiers_id"];
	$mod_desc_proc[$row["mod_prac_code"]]=$row["mod_description"];
}
$stringAllModifiers = substr($stringAllModifiers,0,-1);
//------------------------	END GETTING DATA FOR MENUS TO Modifiers	------------------------//

//------------------------ START GETTING DATA FOR MENUS TO CATEGORY OF DIAGNOSIS-----------------//

$sql = imw_query("select * from diagnosis_code_tbl WHERE diag_cat_id>0 order by dx_code,diag_description");
while($row=imw_fetch_array($sql)){
	$dx_cat_code_data[$row['diag_cat_id']][]=$row;
}

$sql = imw_query("SELECT * FROM diagnosis_category order by category");
while($row=imw_fetch_array($sql)){	
	$arrSubOptions = array();
	if($row["diag_cat_id"] > 0){
		foreach($dx_cat_code_data[$row['diag_cat_id']] as $key=>$val){
			$rowCodes=$dx_cat_code_data[$row['diag_cat_id']][$key];
			$arrSubOptions[] = array($rowCodes["dx_code"]."-".$rowCodes["diag_description"],$xyz, $rowCodes["dx_code"]);
			$arrDxCodechld[$rowCodes["dx_code"]]=$rowCodes["diag_description"];
			
			$d_prac_code = $rowCodes['d_prac_code'];	
			$dx_code = $rowCodes['dx_code'];	
			
			$stringAllDiag.="'".str_replace("'","",$d_prac_code)."',";
			if($d_prac_code!=$dx_code){
				$stringAllDiag.="'".str_replace("'","",$dx_code)."',";
			}
			
			$diag_description = $rowCodes['diag_description'];
			$stringAllDiag.="'".str_replace("'","",$diag_description)."',";
		}
		$arrDxCodes[] = array($row["category"],$arrSubOptions);
	}		
}
$stringAllDiag = substr($stringAllDiag,0,-1);
	
$icd10_desc_arr=array();
$icd10_sql_qry=imw_query("select icd10,icd10_desc from icd10_data where deleted='0'");
while($icd10_sql_row=imw_fetch_array($icd10_sql_qry)){
	$icd10_dx=str_replace('-','',$icd10_sql_row['icd10']);
	$icd10_desc_arr[$icd10_dx]=$icd10_sql_row['icd10_desc'];
}
//------------------------ End GETTING DATA FOR MENUS TO CATEGORY OF DIAGNOSIS	------------------------//

//------------------------ TOS Type ------------------------//
$selQry = "select * from tos_tbl order by tos_prac_cod ASC";
$res = imw_query($selQry);
while($row = imw_fetch_array($res)){
	$id = $row['tos_id'];
	$code = $row['tos_prac_cod'];
	$tosArray[] = array($row["tos_description"]."-".$row["tos_prac_cod"],$xyz, $row["tos_prac_cod"]);
	$stringAllTos.="'".str_replace("'","",$code)."',";
	$tos_data_arr[$row['tos_id']]=$row;
	$tos_prac_code_id[$row['tos_prac_cod']]=$row['tos_id'];
	
}
$stringAllTos = substr($stringAllTos,0,-1);
//------------------------ TOS Type ------------------------//

//------------------------ POS Type ------------------------//
$selQry = "select * from pos_tbl order by pos_prac_code ASC";
$res = imw_query($selQry);
while($row = imw_fetch_array($res)){
	$id = $row['pos_id'];
	$code = $row['pos_prac_code'];
	$posArray[] = array($row["pos_code"]."-".$row["pos_prac_code"],$xyz, $row["pos_prac_code"]);
	$stringAllPos.="'".str_replace("'","",$code)."',";
	$pos_data_arr[$row['pos_id']]=$row;
	$pos_prac_code_id[$row['pos_prac_code']]=$row['pos_id'];
}
$stringAllPos = substr($stringAllPos,0,-1);
//------------------------ POS Type ------------------------//

//------------------------ POS Facility ------------------------//
$selQry = "select * from pos_facilityies_tbl order by pos_id ASC";
$res = imw_query($selQry);
while($row = imw_fetch_array($res)){
	$pos_fac_data_arr[$row['pos_facility_id']]=$row;
	$pos_id_fac_data_arr[$row['pos_id']][]=$row;
}
//------------------------ POS Facility ------------------------//

//------------------------ Facility ------------------------//
$fac_tax_arr=array();
$selQry = "select * from facility order by name ASC";
$res = imw_query($selQry);
while($row = imw_fetch_array($res)){
	$fac_data_arr[$row['id']]=$row;
	$fac_pos_data_arr[$row['fac_prac_code']]=$row['id'];
	$fac_tax_arr[$row['id']]=$row['fac_tax'];
}
//------------------------ Facility ------------------------//

//------------------------ Revenue Code ------------------------//
$selQry1 = "select * from revenue_code ORDER BY r_code";
$res1 = imw_query($selQry1);
$providerRows1 = imw_num_rows($res1);
while($row1 = imw_fetch_array($res1)){
	$r_id = $row1['r_id'];
	$r_code = $row1['r_code'];
	$stringAllcode.="'".str_replace("'","",$r_code)."',";
	$rev_data_arr[$row1['r_id']]=$row1;
	$rev_code_id_arr[$row1['r_code']]=$row1['r_id'];
}
$stringAllrev = substr($stringAllcode,0,-1);
//------------------------ Revenue Code ------------------------//	

//------------------------ proc Code ------------------------//
$selQry8 = "select * from proc_code_tbl  ORDER BY proc_code";
$res8 = imw_query($selQry8);
$providerRows8 = imw_num_rows($res8);
while($row8 = imw_fetch_array($res8)){
	$proc_code_id1 = $row8['proc_code_id'];
	$proc_code = $row8['proc_code'];
	
	$stringAllproccode.="'".str_replace("'","",$proc_code)."',";
	$proc_data_arr[$row8['proc_code_id']]=$row8;
	$proc_code_id_arr[$row8['proc_code']]=$row8['proc_code_id'];
}
$stringAllproc = substr($stringAllproccode,0,-1);
//------------------------ proc Code ------------------------//
if($default_facility>0){
	$facility_name=(object)$pos_fac_data_arr[$default_facility];
}

if($_REQUEST['encounterIdText']){
	$qry = imw_query("select * from patient_charge_list where $whr_del_charge_list_id encounter_id = '".$_REQUEST['encounterIdText']."'");
	$encounterDetails = imw_fetch_object($qry);
	$insertedChargeId = $encounterDetails->charge_list_id;
	$case_type_id = $encounterDetails->case_type_id;
	$superBillIds = $encounterDetails->superbillFormId;
	$entered_date = $encounterDetails->entered_date;
	$primary_provider_id_for_reports = $encounterDetails->primary_provider_id_for_reports;
}	
	
//----------------------------------------------START (NEXT, SAVE, NEW PROCEDURES.)---------------------------------------------//
if($_REQUEST['form_submit']=='yes'){
	$encounterIdText_chk = xss_rem($_REQUEST['encounterIdText']);
	$chk_chl_qry = imw_query("select encounter_id,auth_id,auth_no from patient_charge_list WHERE encounter_id='$encounterIdText_chk' and patient_id='$patient_id'");
	if(imw_num_rows($chk_chl_qry)>0 || $encounterIdText_chk<=0){
		$last_cnt=$_REQUEST['last_cnt'];
		$chk_chl_row=imw_fetch_array($chk_chl_qry);
		$acc_usr_data=array();
		if($_REQUEST['enc_id_read']==""){
			$acc_usr_data['dos']=$_REQUEST['dos'];
		}
		$groups_exp=explode('_',$_REQUEST['groups']);
		$groups=$groups_exp[0];
		$acc_usr_data['tosText']=$_REQUEST['tosText'];
		$acc_usr_data['posText']=$_REQUEST['posText'];
		$acc_usr_data['posFacilityCode']=$_REQUEST['posFacilityCode'];
		$acc_usr_data['primaryProviderText'][$patient_id]=$_REQUEST['primaryProviderText'];
		$acc_usr_data['secondaryProviderText'][$patient_id]=$_REQUEST['secondaryProviderText'];
		$acc_usr_data['tertiaryProviderText'][$patient_id]=$_REQUEST['tertiaryProviderText'];
		$acc_usr_data['Reffer_physician'][$patient_id]=$_REQUEST['Reffer_physician'];
		$acc_usr_data['refferingPhysician'][$patient_id]=$_REQUEST['refferingPhysician'];
		$acc_usr_data['referral'][$patient_id]=$_REQUEST['referral'];
		$acc_usr_data['groups'][$patient_id]=$groups;
		$acc_usr_data['caseTypeText'][$patient_id]=$_REQUEST['caseTypeText'];
		$acc_usr_data['primaryInsText'][$patient_id]=$_REQUEST['primaryInsText'];
		$acc_usr_data['pay_copay_chg'][$patient_id]=$_REQUEST['pay_copay_chg'];
		$acc_usr_data['secondaryInsText'][$patient_id]=$_REQUEST['secondaryInsText'];
		$acc_usr_data['pay_copay_chg2'][$patient_id]=$_REQUEST['pay_copay_chg2'];
		$acc_usr_data['tertiaryInsText'][$patient_id]=$_REQUEST['tertiaryInsText'];
		$acc_usr_data['ins_defer'][$patient_id]=$_REQUEST['ins_defer'];
		$acc_usr_data['pat_defer'][$patient_id]=$_REQUEST['pat_defer'];
		$acc_usr_data['vipPatient'][$patient_id]=$_REQUEST['vipPatient'];
		$acc_usr_data['auth_no'][$patient_id]=$_REQUEST['auth_no'];
		$acc_usr_data['auth_amount'][$patient_id]=$_REQUEST['auth_amount'];
		//$acc_usr_data['comment'][$patient_id]=$_REQUEST['comment'];
		
		$_SESSION['acc_usr_data']=$acc_usr_data;
		
		$encounterIdText = xss_rem($_REQUEST['encounterIdText']);
		if($encounterIdText==""){			
			// CHECH IF EXISTS
			do{
				$encounterIdText = $oFacilityfun->getEncounterId();
				$qry=imw_query("SELECT * FROM patient_charge_list WHERE del_status='0' and encounter_id='$encounterIdText'");
				$getMatchFound=imw_fetch_object($qry);
			}while($getMatchFound);
		}
	
		$encounter_id = $encounterIdText;
		
		
		if($_REQUEST['chrg_mod_opr_id']){
			$chrg_mod_opr_data['enc_id'] = $encounter_id;
			$chrg_mod_opr_data['modifier_by'] = $_REQUEST['chrg_mod_opr_id'];
			$chrg_mod_opr_data['modifier_on'] = date('Y-m-d h:i:s a');
			$insertedChargemodId = AddRecords($chrg_mod_opr_data,'patient_charge_list_modifiy');
		}
		
		//POE Object
		
		$oPoe = new Poe($patient_id,$encounter_id);
		//$flgPoe = false;
		
		$dos = $_REQUEST['dos'];
		list($month, $day, $year)=explode("-", $dos);
		$dos = $year."-".$month."-".$day;
		$dateOFService = $dos;
		
		//--------------------- PROVIDERS ID.	---------------------//	
		$primaryProviderId=get_provider_id($_REQUEST['primaryProviderText']);
		
		$secondaryProviderText = addslashes($_REQUEST['secondaryProviderText']);
		if($secondaryProviderText){
			$secondaryProviderId=get_provider_id($_REQUEST['secondaryProviderText']);
		}
		$tertiaryProviderText = addslashes($_REQUEST['tertiaryProviderText']);
		if($tertiaryProviderText){
			$tertiaryProviderId=get_provider_id($_REQUEST['tertiaryProviderText']);
		}
		//--------------------- PROVIDERS ID.	---------------------//		
		
		
		$st_date = $_REQUEST['st_date'];
		list($month, $day, $year) = explode('-',$st_date);
		$st_date = $year."-".$month."-".$day;
		
		$end_date = $_REQUEST['end_date'];
		list($month, $day, $year) = explode('-',$end_date);
		$end_date = $year."-".$month."-".$day;		
		
		$admit_date=$_REQUEST['admit_date'];
		$admit_time=$_REQUEST['admit_time'];
		if($admit_time){
			$admit_timeSplit=explode(":",$admit_time);
			$admit_time_hour=trim($admit_timeSplit[0]);
			$admit_time_minute=trim($admit_timeSplit[1]);
			if(strlen($admit_time_hour)==1){
				$admit_time_hour="0".$admit_time_hour;
			}
			if(strlen($admit_time_minute)==1){
				$admit_time_minute="0".$admit_time_minute;
			}
			$admit_time=$admit_time_hour.":".$admit_time_minute;
		}	
		list($month, $day, $year) = explode('-',$admit_date);
		$admit_date=$year."-".$month."-".$day;
		if($_REQUEST['admit_date']!=""){
			$admit_date=$admit_date." ".$admit_time;
		}
		$disch_date=$_REQUEST['admit_date'];
		$disch_time=$_REQUEST['disch_time'];
		if($disch_time){
			$disch_timeSplit=explode(":",$disch_time);
			$disch_time_hour=trim($disch_timeSplit[0]);
			$disch_time_minute=trim($disch_timeSplit[1]);
			if(strlen($disch_time_hour)==1){
				$disch_time_hour="0".$disch_time_hour;
			}
			if(strlen($disch_time_minute)==1){
				$disch_time_minute="0".$disch_time_minute;
			}
			$disch_time=$disch_time_hour.":".$disch_time_minute;
		}	
		list($month, $day, $year) = explode('-',$disch_date);
		$disch_date=$year."-".$month."-".$day;
		if($_REQUEST['admit_date']!=""){
			$disch_date=$disch_date." ".$disch_time;
		}
		
		$auth_no=$_REQUEST['auth_no'];
		$auth_id=$_REQUEST['auth_id'];
		$auth_amount=$_REQUEST['auth_amount'];
		$referral=$_REQUEST['referral'];
		$optionalReferral=$_REQUEST['optionalReferral'];
		$chk_self=$_REQUEST['chk_self'];
		if($_REQUEST['reff_phy_nr'] == true){
			$reff_phy_nr = 1;
		}else{
			$reff_phy_nr = 0;
		}
		if($_REQUEST['comment']=='Comment'){
			$comment="";
		}else{
			$comment=$_REQUEST['comment'];
		}
		
		
		//--------------------- INSURANCE Case Type.	---------------------//
		if($caseTypeText!='Self'){
			$caseTypeText = $_REQUEST['caseTypeText'];
				$caseTypeText_arr=explode('-',$caseTypeText);
				$caseTypeText_arr_cont=count($caseTypeText_arr);
				$caseTypeText = $caseTypeText_arr[$caseTypeText_arr_cont-1];
		}else{
			$caseTypeText = 0;
		}
		//--------------------- INSURANCE Case Type.	---------------------//
		
		//--------------------- INSURANCE COMPAMIES ID.	---------------------//
		
		if($_REQUEST['getPriInsId']){
			$primaryInsId=$_REQUEST['getPriInsId'];
		}else{
			$primaryInsText = $_REQUEST['primaryInsText'];
			if(empty($primaryInsText)==false){
				$primaryInsId =  $ins_in_house_code_id[$primaryInsText];
			}
		}	
		if($_REQUEST['getSecInsId']){
			$secondaryInsId=$_REQUEST['getSecInsId'];
		}else{
			$secondaryInsText=$_REQUEST['secondaryInsText'];
			if(empty($secondaryInsText)==false){
				$secondaryInsId =  $ins_in_house_code_id[$secondaryInsText];
			}
		}
		if($_REQUEST['getTriInsId']){
			$tertiaryInsId=$_REQUEST['getTriInsId'];
		}else{
			$tertiaryInsText=$_REQUEST['tertiaryInsText'];
			if(empty($tertiaryInsText)==false){
				$tertiaryInsId =  $ins_in_house_code_id[$tertiaryInsText];
			}
		}
		$pay_copay_chg=$_REQUEST['pay_copay_chg'];
		$pay_copay_chg2=$_REQUEST['pay_copay_chg2'];
		$proc_copay=$pay_copay_chg+$pay_copay_chg2;
		if($chk_self<>""){
			//$caseTypeText = 0;
			$primaryInsId=0;
			$secondaryInsId=0;
			$tertiaryInsId=0;
			$pay_copay_chg=0;
			$pay_copay_chg2=0;
			$proc_copay=0;
		}
		
		//--------------------- INSURANCE COMPAMIES ID.	---------------------//			
	
		//--------------------- TOS CODE.	---------------------//
		$tosText = $_REQUEST['tosText'];
		$tosID = $tos_prac_code_id[$tosText];
		//--------------------- TOS CODE.	---------------------//			
		
		//--------------------- POS CODE.	---------------------//
		$posText = $_REQUEST['posText'];
		$posId = $pos_prac_code_id[$posText];
		$posFacilityId = $_REQUEST['posFacilityCode'];
		//--------------------- POS CODE.	---------------------//
		
		
		$ins_defer=$_REQUEST['ins_defer'];
		if($ins_defer=="on"){
			$ins_defer="true";
		}else{
			$ins_defer="false";
		}	
		$pat_defer=$_REQUEST['pat_defer'];
		if($pat_defer=="on"){
			$pat_defer="true";
		}else{
			$pat_defer="false";
		}	
		
		$vipPatient = $_REQUEST['vipPatient'];
		if(!$vipPatient){
			$vipPatient = "false";
		}
		
		$coPayNotRequired = $_REQUEST['coPayNotRequired'];
		$coPayNotRequired2 = $_REQUEST['coPayNotRequired2'];
		if($coPayNotRequired == true){
			$coPayNotRequired = 1;
		}else{
			$coPayNotRequired = 0;
		}
		if($coPayNotRequired2 == true){
			$coPayNotRequired2 = 1;
		}else{
			$coPayNotRequired2 = 0;
		}
		
		if($vipPatient=="true" && $policyQryRes['vip_copay_not_collect']>0 && $_REQUEST['copayPaid_chk']!='yes'){
			$coPayNotRequired = 1;
			$coPayNotRequired2 = 1;
		}	
		$acc_anes_time=$_REQUEST['acc_anes_time'];
		$acc_anes_unit=$_REQUEST['acc_anes_unit'];
		if($_REQUEST['acc_anesthesia']=="on"){
			$acc_anesthesia=1;
		}else{
			$acc_anesthesia=0;
		}
		if($_REQUEST['Reffer_physician']!=""){
			$reff_phy_id=$reff_phy_id;
		}else{
			if($_REQUEST['refferingPhysician']!=""){
				$reff_phy_id=$_REQUEST['refferingPhysician'];
			}else{
				$primaryProviderText = $_REQUEST['primaryProviderText'];
				list($primaryProviderlname, $primaryProviderfname) = explode(', ', $primaryProviderText);
				$qry=imw_query("SELECT physician_Reffer_id,FirstName,LastName FROM refferphysician WHERE FirstName='".addslashes($primaryProviderfname)."' and LastName='".addslashes($primaryProviderlname)."' and delete_status ='0' order by FirstName asc");
				$ref_phy_res_id=imw_fetch_array($qry);	
				$reff_phy_id=$ref_phy_res_id['physician_Reffer_id'];
			}
		}
		
		if($sx_physician_arr[$primaryProviderId]>0 && $groups_exp[2]>0){
			$chk_ref_prov=imw_query("select FirstName,LastName,MiddleName from refferphysician where physician_Reffer_id='$reff_phy_id'");
			$chk_ref_prov_row=imw_fetch_array($chk_ref_prov);
			if(trim($chk_ref_prov_row['MiddleName'])!=""){
				$final_ref_data=$chk_ref_prov_row['LastName'].', '.$chk_ref_prov_row['FirstName'].' '.$chk_ref_prov_row['MiddleName'].'.';
			}else{
				$final_ref_data=$chk_ref_prov_row['LastName'].', '.$chk_ref_prov_row['FirstName'];
			}
			if($chk_ref_prov_row['FirstName']!="" && $chk_ref_prov_row['LastName']!=""){
				$primary_provider_id_for_reports_chk=get_provider_id($final_ref_data);
			}
			if($primary_provider_id_for_reports_chk>0){
				$primary_provider_id_for_reports=$primary_provider_id_for_reports_chk;
			}else{
				$primary_provider_id_for_reports=$primaryProviderId;
			}
		}else{
			$primary_provider_id_for_reports=$primaryProviderId;
		}
		
		$chargeListData['vipStatus'] = $vipPatient;
		$chargeListData['encounter_id'] = $encounterIdText;
		$chargeListData['patient_id'] = $patient_id;
		$chargeListData['case_type_id'] = $caseTypeText;
		if($facility_id>0){
			$chargeListData['facility_id'] = $facility_id;
		}else{
			$chargeListData['facility_id'] = $posFacilityId;
		}
		if($_REQUEST['billing_facility_id']>0){
			$chargeListData['billing_facility_id'] = $_REQUEST['billing_facility_id'];
		}else{
			$chargeListData['billing_facility_id'] = $fac_pos_data_arr[$posFacilityId];
		}
		
		$chargeListData['primaryInsuranceCoId'] = $primaryInsId;
		$chargeListData['secondaryInsuranceCoId'] = $secondaryInsId;
		$chargeListData['tertiaryInsuranceCoId'] = $tertiaryInsId;
		$chargeListData['primaryProviderId'] = $primaryProviderId;
		$chargeListData['secondaryProviderId'] = $secondaryProviderId;
		$chargeListData['tertiaryProviderId'] = $tertiaryProviderId;
		$chargeListData['admit_date'] = $admit_date;
		$chargeListData['disch_date'] = $disch_date;
		$chargeListData['date_of_service'] = $dos;
		$chargeListData['payment_status'] = 'Pending';
		$chargeListData['patientPaidAmt'] = '0';
		$chargeListData['insPaidAmt'] = '0';
		$chargeListData['resPartyPaid'] = '0';
		$chargeListData['coPayNotRequired'] = $coPayNotRequired;
		$chargeListData['coPayNotRequired2'] = $coPayNotRequired2;
		$chargeListData['reff_phy_id'] = $reff_phy_id;
		$chargeListData['referral'] = $referral;
		$chargeListData['optional_referral'] = $optionalReferral;
		$chargeListData['operator_id'] = $_REQUEST['operator_read_id'];
		$chargeListData['auth_id'] = $auth_id;
		$chargeListData['auth_no'] = $auth_no;
		$chargeListData['auth_amount'] = $auth_amount;
		$chargeListData['gro_id'] = $groups;
		//$chargeListData['comment'] = $comment;
		$chargeListData['acc_anesthesia'] = $acc_anesthesia;
		$chargeListData['acc_anes_time'] = $acc_anes_time;
		$chargeListData['acc_anes_unit'] = $acc_anes_unit;
		$chargeListData['report_type_code'] =  $_REQUEST['report_type_code'];
		$chargeListData['transmission_code'] =  $_REQUEST['transmission_code'];
		$chargeListData['control_no'] = $_REQUEST['control_no'];
		$chargeListData['primary_provider_id_for_reports'] = $primary_provider_id_for_reports;
		$chargeListData['reff_phy_nr'] = $reff_phy_nr;

		if($auth_no!=$chk_chl_row['auth_no'] || ($auth_id>0 && $auth_id!=$chk_chl_row['auth_id'])){
			$chargeListData['auth_status'] = 'false';
		}
		
		//copay apply 
		for($j=1;$j<=$last_cnt;$j++){
			$procedureText_arr[] = $_REQUEST['procedureText_'.$j];
		}
		$procedureText_imp=implode(',',$procedureText_arr);
		$copay_collect=copay_apply_chk($procedureText_imp,$primaryInsId,$secondaryInsId);
		if($_REQUEST['copayPaid_chk']!='yes'){
			if($copay_collect[0]==true || $copay_collect[1]==true){
			
				$copay_policies = ChkSecCopay_collect($primaryInsId);
				$secCopay=$copay_policies;
				
				if($_REQUEST['copayPaid_chk']!='yes'){
					if($chk_self<>""){
						$chargeListData['pri_copay']=0;
						$chargeListData['sec_copay']=0;
						$chargeListData['copay']=0;
					}else{
						if($copay_collect[0]==true){
							$chargeListData['pri_copay']=$pay_copay_chg;
						}else{
							$pay_copay_chg=0;
							$chargeListData['pri_copay']=0;
						}
						if($copay_collect[1]==true && $secCopay=='Yes'){
							if($sec_copay_collect_amt>=$pay_copay_chg2 || $sec_copay_for_ins==''){
								$chargeListData['sec_copay']=$pay_copay_chg2;
							}else{
								$chargeListData['sec_copay']=0;
							}
						}else{
							$pay_copay_chg2=0;
							$chargeListData['sec_copay']=0;
						}
						$chargeListData['copay']=$pay_copay_chg+$pay_copay_chg2;
					}
				}	
			}else{
				$chargeListData['pri_copay']=0;
				$chargeListData['sec_copay']=0;
				$chargeListData['copay']=0;
			}
		}
		$chl_diag_arr=array();
		for($g=1;$g<=12;$g++){
			$chl_diag_arr[$g]=$_REQUEST['diagText_'.$g];
		}
		$chargeListData['all_dx_codes'] = serialize($chl_diag_arr);
		if($_REQUEST['enc_icd10']<=0){
			if($dos>="2015-10-01"){
				$_REQUEST['enc_icd10']=1;//will always in r8
			}
		}
		$chargeListData['enc_icd10'] = $_REQUEST['enc_icd10'];
		$chargeListData['auto_state'] = $_REQUEST['auto_state'];
		$chargeListData['billing_type'] = $_REQUEST['billing_type'];
		$chargeListData['claim_status'] = $_REQUEST['ar_claim_status'];
		//copay apply 
		if($insertedChargeId){
			$chargeListData['entered_date'] = $entered_date;
			if($_REQUEST['chk_frm_sub']=='yes'){
			}
			//------- Update Patient Charge List Table ------------
			$insertedCharge = UpdateRecords($insertedChargeId,'charge_list_id',$chargeListData,'patient_charge_list');
		}else{
			$chargeListData['submitted'] = 'false';
			$chargeListData['collection'] = 'false';
			$chargeListData['entered_date'] = date('Y-m-d');
			$chargeListData['entered_time'] = $del_time;
			//------- End Patient Charge List Table ------------
			$insertedChargeId = AddRecords($chargeListData,'patient_charge_list');
		}
		
		for($f=1;$f<=$last_cnt;$f++){
			if($_REQUEST['chkbx_'.$f]){
				if($_REQUEST['procedureText_'.$f]){
				
					$charge_list_detail_id = $_REQUEST['chld_id_'.$f];
					$procedureText = $_REQUEST['procedureText_'.$f];
					$proc_selfpay = $_REQUEST['proc_selfpay_'.$f];
					$display_order_id = $_REQUEST['display_order_'.$f];	
				
					if($proc_selfpay<>""){
						$proc_selfpay="1";
					}else{
						$proc_selfpay="0";
					}	
					
					//--------------------- MODIFIERS ID.	---------------------//
					$mod1Text = $_REQUEST['mod1Text_'.$f];
					$mod2Text = $_REQUEST['mod2Text_'.$f];
					$mod3Text = $_REQUEST['mod3Text_'.$f];
					$mod4Text = $_REQUEST['mod4Text_'.$f];
					$mod1Id = $mod_prac_code_id[strtolower($mod1Text)];
					$mod2Id = $mod_prac_code_id[strtolower($mod2Text)];
					$mod3Id = $mod_prac_code_id[strtolower($mod3Text)];
					$mod4Id = $mod_prac_code_id[strtolower($mod4Text)];
					//--------------------- MODIFIERS ID.	---------------------//
					
					$proc_copay = $_REQUEST['proc_copay_'.$f];
					$units = $_REQUEST['units_'.$f];
					$charges = str_replace(',','',$_REQUEST['charges_'.$f]);
					$netAmt = str_replace(',','',$_REQUEST['netAmt_'.$f]);
					
					$app1 = $_REQUEST['app1_'.$f];
					$app1_date = $_REQUEST['app1_date_'.$f];
					list($month, $day, $year) = explode('-',$app1_date);
					$app1_date = $year."-".$month."-".$day;
					
					$app2 = $_REQUEST['app2_'.$f];
					$app2_date = $_REQUEST['app2_date_'.$f];
					list($month, $day, $year) = explode('-',$app2_date);
					$app2_date = $year."-".$month."-".$day;
					
					$onsettypeText=$_REQUEST['onsettypeText_'.$f];
					$onset_date=$_REQUEST['onset_date_'.$f];
					list($month, $day, $year) = explode('-',$onset_date);
					$onset_date=$year."-".$month."-".$day;
					
					$rev_rate=$_REQUEST['rev_rate_'.$f];
					$rev_code=$_REQUEST['revcode_'.$f];
					$proc_code=$_REQUEST['proccode_'.$f];
					
					$rev_code_id="";
					if($rev_code_id_arr[$rev_code]>0){
						$rev_code_id = $rev_code_id_arr[$rev_code];
					}
					$proc_code_id="";
					if($proc_code_id_arr[$proc_code]>0){
						$proc_code_id = $proc_code_id_arr[$proc_code];
					}
					$notes=""; $front_acc_comment=""; $acc_comment="";
					if($_REQUEST['notes_'.$f]!="Comment"){
						$notes = $_REQUEST['notes_'.$f];
						if($_REQUEST['comment_front_'.$f]){
							$front_acc_comment=$_REQUEST['comment_front_'.$f];
						}
						if($_REQUEST['acc_comment_'.$f]){
							$acc_comment=$_REQUEST['acc_comment_'.$f];
						}
					}
					
					$getProcIdRow=$cpt_prac_data_arr[$procedureText];
					$procedureId = $getProcIdRow['cpt_fee_id'];
					$cpt4_code = $getProcIdRow['cpt4_code'];
					$cpt_prac_code = $getProcIdRow['cpt_prac_code'];
					$cpt_not_covered = $getProcIdRow['not_covered'];
					if($getProcIdRow['cpt_fee_id']<=0){
						$getProcIdRow=$cpt_4code_data_arr[$procedureText];
						if($getProcIdRow['cpt_fee_id']<=0){
							$getProcIdRow=$cpt_desc_data_arr[$procedureText];
						}
						$procedureId = $getProcIdRow['cpt_fee_id'];
						$cpt4_code = $getProcIdRow['cpt4_code'];
						$cpt_prac_code = $getProcIdRow['cpt_prac_code'];
						$cpt_not_covered = $getProcIdRow['not_covered'];
					}
					$oPoe->isPoeCode($cpt_prac_code);
					
					if($cpt_not_covered>0 || strtolower($procedureText)=="tax"){
						$proc_selfpay=1;
						$_REQUEST['proc_selfpay_'.$f]=$_REQUEST['chld_id_'.$f];
					}
					$approvedAmt = $netAmt;
					$deductAmt = 0;
					// Getting Proc Code Is Refraction Or Not
					$chargeListDetail=array();	
					$chargeListDetail['charge_list_id'] = $insertedChargeId;	
					$chargeListDetail['proc_selfpay'] = $proc_selfpay;
					$chargeListDetail['patient_id'] = $patient_id;	
					$chargeListDetail['procCode'] = $procedureId;	
					$chargeListDetail['approval1'] = $app1;
					$chargeListDetail['approval1_date'] = $app1_date;
					$chargeListDetail['approval2'] = $app2;
					$chargeListDetail['approval2_date'] = $app2_date;
					$chargeListDetail['primaryProviderId'] = $primaryProviderId;
					$chargeListDetail['secondaryProviderId'] = $secondaryProviderId;
					$chargeListDetail['tertiaryProviderId'] = $tertiaryProviderId;
					$chargeListDetail['notes'] = $notes;
					//$chargeListDetail['front_acc_comment'] = $front_acc_comment;
					//$chargeListDetail['acc_comment'] = $acc_comment;
					$chargeListDetail['rev_code'] = $rev_code_id;
					$chargeListDetail['proc_code_essi'] = $proc_code_id;
					$chargeListDetail['display_order'] = $display_order_id;
					$chargeListDetail['primary_provider_id_for_reports'] = $primary_provider_id_for_reports;
					
					$paidForProc = 0;
					$balForProc = $netAmt;
					$newBalance = $netAmt;
					// IF SOME OF FULL IS PAID THEN MANAGE AMOUNTS
					
					$qry=imw_query("SELECT * FROM patient_charge_list_details WHERE del_status='0' and charge_list_detail_id='$charge_list_detail_id'");
					$getOldChargeDetails=imw_fetch_object($qry);
					if($charge_list_detail_id>0){
						$chk_approve_amount_changed = $getOldChargeDetails->approvedAmt;
						$chk_totalAmount_amount_changed = $getOldChargeDetails->totalAmount;
						$chk_allow_write_off_amount = $chk_totalAmount_amount_changed-$chk_approve_amount_changed;
						$chk_newBalance_amount_changed = $getOldChargeDetails->newBalance;
						$chk_paidForProc_amount_changed = $getOldChargeDetails->paidForProc;
						$chk_units_changed = $getOldChargeDetails->units;
						$chk_overPaymentForProc_changed = $getOldChargeDetails->overPaymentForProc;
						$coPayAdjustedAmount_changed = $getOldChargeDetails->coPayAdjustedAmount;
						
						
						if($chk_totalAmount_amount_changed==$netAmt){
						}else{
							$tot_proc_paid=0;
							$qry=imw_query("SELECT * FROM patient_charges_detail_payment_info WHERE charge_list_detail_id='$charge_list_detail_id'");
							$get_paid_chld=imw_fetch_object($qry);
							if($get_paid_chld){
								foreach($get_paid_chld as $get_paid_chld_data){
									if($get_paid_chld_data->deletePayment==0){
										$get_paidForProc=$get_paid_chld_data->paidForProc;
										$get_overPayment=$get_paid_chld_data->overPayment;
										$tot_proc_paid=$tot_proc_paid+$get_paidForProc+$get_overPayment;
									}
								}
							}	
							
							$paidForProc_copay_chk=0;
							$getcopaycode = "SELECT sum(paidForProc) as tot_paidproc FROM 
										patient_chargesheet_payment_info a,
										patient_charges_detail_payment_info b
										WHERE a.encounter_id = '$encounter_id'
										AND a.payment_id = b.payment_id
										AND b.charge_list_detail_id = 0
										AND b.deletePayment=0
										ORDER BY a.payment_id DESC";
							$getcopaycodeQry = imw_query($getcopaycode);
							$getcopycodeRow = imw_fetch_array($getcopaycodeQry);
							if($coPayAdjustedAmount_changed==1){
								$paidForProc_copay_chk = $getcopycodeRow['tot_paidproc'];
							}else{
								$paidForProc_copay_chk=0;
							}
							
							$tot_writeoff_amt_chk=0;
							$qry=imw_query("SELECT * FROM paymentswriteoff WHERE charge_list_detail_id='$charge_list_detail_id'");
							$get_write_chld=imw_fetch_object($qry);
							if($get_write_chld){
								foreach($get_write_chld as $get_write_chld_data){
									if($get_write_chld_data->delStatus==0){
										$write_off_amount_tab=$get_write_chld_data->write_off_amount;
										$tot_writeoff_amt_chk=$tot_writeoff_amt_chk+$write_off_amount_tab;
									}
								}
							}	
							
							$tot_refund_amt_chk=0;
							$getrefcode = "SELECT sum(amountApplied) as amountApplied FROM 
										creditapplied
										WHERE 
										crAppliedTo  = 'payment'
										AND charge_list_detail_id = '$charge_list_detail_id'
										AND delete_credit=0";
							$getrefcodeQry = imw_query($getrefcode);
							$getrefcodeRow = imw_fetch_array($getrefcodeQry);
							$tot_refund_amt_chk = $getrefcodeRow['amountApplied'];
							
							$tot_adj_ovr_amt_chk=0;
							$getadj_ovrcode = "SELECT sum(payment_amount) as payment_amount FROM 
										account_payments
										WHERE 
										(payment_type = 'Adjustment' or payment_type = 'Over Adjustment')
										AND charge_list_detail_id = '$charge_list_detail_id'
										AND del_status=0";
							$getadj_ovrcodeQry = imw_query($getadj_ovrcode);
							$getadj_ovrcodeRow = imw_fetch_array($getadj_ovrcodeQry);
							$tot_adj_ovr_amt_chk = $getadj_ovrcodeRow['payment_amount'];
							
							
							$now_allow_write_amt=0;
							$now_allow_write_amt=$netAmt-$chk_approve_amount_changed;
							if($chk_allow_write_off_amount>0){
								$chargeListDetail['write_off']=$now_allow_write_amt;
							}else{
								$chk_balance_write_amt=0;
								if($tot_writeoff_amt_chk>0){
									$chk_balance_write_amt=$netAmt-$tot_writeoff_amt_chk-$paidForProc_copay_chk;
								}else{
									$chk_balance_write_amt=$netAmt-$paidForProc_copay_chk;
								}
								if($tot_refund_amt_chk>0){
									$chk_balance_write_amt=$chk_balance_write_amt+$tot_refund_amt_chk;
								}
								if($tot_adj_ovr_amt_chk>0){
									$chk_balance_write_amt=$chk_balance_write_amt+$tot_adj_ovr_amt_chk;
								}
								
								if($chk_balance_write_amt>$tot_proc_paid){
									$now_bal_amt=$chk_balance_write_amt-$tot_proc_paid;
									$tot_proc_paid=$tot_proc_paid-$tot_adj_ovr_amt_chk;
									$tot_proc_paid=$tot_proc_paid-$tot_refund_amt_chk;
									
									$chargeListDetail['paidForProc']=$tot_proc_paid;
									$chargeListDetail['balForProc'] = $now_bal_amt;
									$chargeListDetail['newBalance'] = $now_bal_amt;
									$chargeListDetail['overPaymentForProc']=0;
									$chargeListDetail['approvedAmt'] = $netAmt;
								}else{
									$now_ovr_paid_amt=$tot_proc_paid-$chk_balance_write_amt;
									$now_paid_amt=$chk_balance_write_amt;
									$now_paid_amt=$now_paid_amt-$tot_refund_amt_chk;
									$now_paid_amt=$now_paid_amt-$tot_adj_ovr_amt_chk;
									$chargeListDetail['paidForProc']=$now_paid_amt;
									$chargeListDetail['balForProc'] = 0;
									$chargeListDetail['newBalance'] = 0;
									$chargeListDetail['overPaymentForProc']=$now_ovr_paid_amt;
									$chargeListDetail['approvedAmt'] = $netAmt;
								}
							}
						}
					}else{
						$chargeListDetail['balForProc'] = $netAmt;
						$chargeListDetail['newBalance'] = $netAmt;
						$chargeListDetail['approvedAmt'] = $netAmt;
					}
					if($chk_totalAmount_amount_changed==$chk_newBalance_amount_changed){
						$chargeListDetail['balForProc'] = $netAmt;
						$chargeListDetail['newBalance'] = $netAmt;
						$chargeListDetail['approvedAmt'] = $netAmt;
					}
					$chargeListDetail['units'] = $units;
					$chargeListDetail['procCharges'] = $charges;
					$chargeListDetail['totalAmount'] = $netAmt;	
					$chargeListDetail['modifier_id1'] = $mod1Id;
					$chargeListDetail['modifier_id2'] = $mod2Id;
					$chargeListDetail['modifier_id3'] = $mod3Id;
					$chargeListDetail['modifier_id4'] = $mod4Id;
					$chargeListDetail['referral'] = $referral;
					$chargeListDetail['optional_referral'] = $optionalReferral;
					//$chargeListDetail['onset_type'] = $onsettypeText;
					//$chargeListDetail['onset_date'] =  $onset_date;
					$chargeListDetail['rev_rate'] = $rev_rate;	
					for($g=1;$g<=12;$g++){
						$chargeListDetail['diagnosis_id'.$g] = "";
					}
					for($g=0;$g<12;$g++){
						$diagText_all_exp=array();
						$diagText_all_exp=explode('**',$_REQUEST['diagText_all_'.$f][$g]);
						if($diagText_all_exp[1]>0){
							$chargeListDetail['diagnosis_id'.$diagText_all_exp[1]] = $diagText_all_exp[0];
						}
					}
							
					if($charge_list_detail_id){
						//------- Update Patient Charge List Details Table ------------
						$insertedCharge = UpdateRecords($charge_list_detail_id,'charge_list_detail_id',$chargeListDetail,'patient_charge_list_details');
						$chld_chkbox_for_post_arr[]=$charge_list_detail_id;
					// ----------------------------------END UPDATE PROCEDURE DETAILS----------------------------------					
					}else{
					//------- End Patient Charge List Table ------------
						$chargeListDetail['entered_date'] = date('Y-m-d H:i:s');	
						$chargeListDetail['operator_id'] = $operator_id;	
						$insertedChargeListDetailId = AddRecords($chargeListDetail,'patient_charge_list_details');
						$chld_chkbox_for_post_arr[]=$insertedChargeListDetailId;
					// ----------------------------------END INSERT NEW PROCEDURE DETAILS----------------------------						
					}
					
				}
			}
		}
		if($insertedChargeId){
			if($st_date=="0000-00-00" || str_replace('-','',$st_date)==""){
				$st_date="";
			}
			if($end_date=="0000-00-00" || str_replace('-','',$end_date)==""){
				$end_date="";
			}
			$chargeListDetail_all['start_date'] = $st_date;
			$chargeListDetail_all['end_date'] = $end_date;
			$chargeListDetail_all['type_of_service'] = $tosID;
			$chargeListDetail_all['place_of_service'] = $posId;
			$chargeListDetail_all['posFacilityId'] = $posFacilityId;
			$chargeListDetail_all['primaryProviderId'] = $primaryProviderId;
			$chargeListDetail_all['secondaryProviderId'] = $secondaryProviderId;
			$chargeListDetail_all['tertiaryProviderId'] = $tertiaryProviderId;
			$chargeListDetail_all['differ_insurance_bill'] = $ins_defer;
			$chargeListDetail_all['differ_patient_bill'] = $pat_defer;	
			$chargeListDetail_all['referral'] = $referral;
			$chargeListDetail_all['optional_referral'] = $optionalReferral;	
			$chk_gro_id_arr=explode(',',$_REQUEST['chk_gro_id']);	
			if($_REQUEST['billing_type']==2){
			}else{
				$chargeListDetail_all['rev_code'] = '';
				$chargeListDetail_all['proc_code_essi'] = '';
			}
			if($chk_self<>""){
				$chargeListDetail_all['proc_selfpay'] = '1';			
			}else{
				//$chargeListDetail_all['proc_selfpay'] = '0';	
			}
			$chargeListDetail_all['primary_provider_id_for_reports'] = $primary_provider_id_for_reports;
			$up_charge_details = UpdateRecords($insertedChargeId,'charge_list_id',$chargeListDetail_all,'patient_charge_list_details');
		}
		$self_chld=array();
		$qry_chld=imw_query("select charge_list_detail_id,display_order from patient_charge_list_details where del_status='0' and charge_list_id='$insertedChargeId' order by display_order");
		while($detailsData_chld=imw_fetch_array($qry_chld)){
			$self_chld[]=$detailsData_chld['charge_list_detail_id'];
			$max_display_order=$detailsData_chld['display_order'];
		}
		if($chk_self==""){
			for($g=1;$g<=$last_cnt;$g++){
				if(in_array($_REQUEST['proc_selfpay_'.$g],$self_chld)){
					$up_self_case=$_REQUEST['proc_selfpay_'.$g];
					$chargeListDetail_self_pay['proc_selfpay'] = '1';
					$up_charge_details_self_pay = UpdateRecords($up_self_case,'charge_list_detail_id',$chargeListDetail_self_pay,'patient_charge_list_details');
				}else{
					$up_self_case=$_REQUEST['proc_selfpay_chld_'.$g];
					$chargeListDetail_self_pay['proc_selfpay'] = '0';
					$up_charge_details_self_pay = UpdateRecords($up_self_case,'charge_list_detail_id',$chargeListDetail_self_pay,'patient_charge_list_details');
				}
			}
		}
		for($f=1;$f<=$last_cnt;$f++){
			if($_REQUEST['chld_id_'.$f]>0){
				$charge_list_detail_id = $_REQUEST['chld_id_'.$f];
				$display_order_id = $_REQUEST['display_order_'.$f];
				if(strtolower($_REQUEST['procedureText_'.$f])=="tax"){
					$display_order_id=$max_display_order+1;
				}
				imw_query("update patient_charge_list_details set display_order=$display_order_id where charge_list_detail_id = $charge_list_detail_id");
			}
		}
		//if($flgPoe == true){
			$oPoe->setPoeEnId();		
		//}
		$post_chrg_chk=$_REQUEST['post_chrg_chk'];
		$chkbox_for_post=implode(',',$chld_chkbox_for_post_arr);
		set_payment_trans($encounter_id,'',$stop_clm_status);
		patient_proc_bal_update($encounter_id);
		include("manageEncounterAmounts.php");	
		
		//HL7 DFT MESSAGE SAVING  21-Feb-2015
		if(constant('ACC_DFT_GENERATION')==true){
			require_once(dirname(__FILE__)."/../../hl7sys/old/CLS_makeHL7.php");
			$makeHL7		= new makeHL7;
			$makeHL7->log_HL7_message($encounter_id,'Detailed Financial Transaction','ACC_DFT');
			//die('done');
		}
		echo "<script>
				if(typeof(top.get_pt_edu_alert)!='undefined'){
					top.get_pt_edu_alert();
				}
			 </script>";
		echo "<script>window.location.href='accounting_view.php?encounter_id=".$encounter_id."&tabvalue=Enter_Charges&post_chrg_chk=".$post_chrg_chk."&chkbox_for_post=".$chkbox_for_post."';</script>";	 
	}
}

if($encounter_id){
	if($patientDetail->pat_account_status>0){
		$coll_qry=imw_query("select patient_data.id from patient_data 
				join account_status on account_status.id=patient_data.pat_account_status 
				where patient_data.pat_account_status>0 and
				account_status.status_type='collection' and patient_data.id='$patient_id'");
		$today_date = date('Y-m-d');
		if(imw_num_rows($coll_qry)>0){
			$today_date = date('Y-m-d');
			$updateStr = "UPDATE patient_charge_list SET
						  collection = 'true',
						  collectionAmount = totalBalance,
						  collectionDate = '".$today_date."' 
						  WHERE patient_id ='$patient_id' and totalBalance>0 and collection !='true'";
			$updateRs = imw_query($updateStr);
		}else{
			$updateStr = "UPDATE patient_charge_list SET
					  collection = 'false',
					  collectionDate = '' 
					  WHERE patient_id ='$patient_id' and collection ='true'";
			$updateRs = imw_query($updateStr);
		}
	}else{
		$updateStr = "UPDATE patient_charge_list SET collection = 'false',collectionDate = '' WHERE patient_id ='$patient_id' and collection ='true'";
		$updateRs = imw_query($updateStr);
	}
	
	$getCaseTypeStr = "SELECT *,date_format(date_of_service,'%m-%d-%Y') as dateOfService FROM patient_charge_list WHERE $whr_del_charge_list_id encounter_id = '$encounter_id' AND patient_id = '$patient_id'";
	$getCaseTypeQry = imw_query($getCaseTypeStr);
	$getCaseTypeRow = imw_fetch_array($getCaseTypeQry);
	$charge_list_id = $getCaseTypeRow['charge_list_id'];
	$copay = $getCaseTypeRow['copay'];
	$copayPaid = $getCaseTypeRow['copayPaid'];
	$case_type_id = $getCaseTypeRow['case_type_id'];
	$pri_copay = $getCaseTypeRow['pri_copay'];
	$sec_copay = $getCaseTypeRow['sec_copay'];
	$auth_id_enc = $getCaseTypeRow['auth_id'];
	$auth_no_enc = $getCaseTypeRow['auth_no'];
	$auth_amount_enc = $getCaseTypeRow['auth_amount'];
	$chlist_gro_id = $getCaseTypeRow['gro_id'];
	$chk_chld_pri=$getCaseTypeRow['primaryInsuranceCoId']; 
	$chk_chld_sec=$getCaseTypeRow['secondaryInsuranceCoId']; 
	$chk_chld_tri=$getCaseTypeRow['tertiaryInsuranceCoId']; 
	$date_of_service=$getCaseTypeRow['dateOfService']; 
	$comment=$getCaseTypeRow['comment']; 
	$chk_referral=$getCaseTypeRow['referral']; 
	$chk_totalAmt=$getCaseTypeRow['totalAmt']; 
	$chk_totalBalance=$getCaseTypeRow['totalBalance']; 
	$chk_reff_phy_id=$getCaseTypeRow['reff_phy_id'];
	$chk_chl_opr_id=$getCaseTypeRow['operator_id'];
	$acc_anesthesia=$getCaseTypeRow['acc_anesthesia'];
	$acc_anes_time=$getCaseTypeRow['acc_anes_time'];
	$acc_anes_unit=$getCaseTypeRow['acc_anes_unit'];
	$all_dx_codes = $getCaseTypeRow['all_dx_codes'];
	$enc_icd10 = $getCaseTypeRow['enc_icd10'];
	$report_type_code = $getCaseTypeRow['report_type_code'];
	$transmission_code = $getCaseTypeRow['transmission_code'];
	$control_no = $getCaseTypeRow['control_no'];
	$auto_state = $getCaseTypeRow['auto_state'];
	$enc_accept_assignment = $getCaseTypeRow['enc_accept_assignment'];
	$reff_phy_nr = $getCaseTypeRow['reff_phy_nr'];
	$facility_id = $getCaseTypeRow['facility_id'];
	$billing_type = $getCaseTypeRow['billing_type'];
	$billing_facility_id = $getCaseTypeRow['billing_facility_id'];
	$ar_claim_status_id = $getCaseTypeRow['claim_status'];
	
	if($auth_no_enc!="" || in_array(strtolower($billing_global_server_name), array('austineeye','tec'))){
		$auth_no=$auth_no_enc;
		$auth_id=$auth_id_enc;
		$auth_amount=$auth_amount_enc;
	}
	
	$get_case_type = imw_query("SELECT a.*, b.* FROM insurance_case a,insurance_case_types b WHERE a.ins_caseid='$case_type_id' AND b.case_id=a.ins_case_type");
	$get_case_type_row = imw_fetch_array($get_case_type);
	$insCaseType = $get_case_type_row['case_name'];
	$openInsCaseNameID = $insCaseType." - ".$case_type_id;
	$effective_date=$get_case_type_row['start_date'];
	$expiration_date=$get_case_type_row['end_date'];

	$st_date=get_date_format($effective_date);
	$end_date=get_date_format($expiration_date);
	if($effective_date=="0000-00-00"){
		$st_date="";
	}
	if($expiration_date=="0000-00-00"){
		$end_date="";
	}
	
	$vipPatient = $getCaseTypeRow['vipStatus'];
	$copayPaid = $getCaseTypeRow['copayPaid'];
	$iscoPayNotRequired = $getCaseTypeRow['coPayNotRequired'];
	$iscoPayNotRequired2 = $getCaseTypeRow['coPayNotRequired2'];
	$coPayWriteOff = $getCaseTypeRow['coPayWriteOff'];
	$reff_phy_ids = $getCaseTypeRow['reff_phy_id'];
	$submitted = $getCaseTypeRow['submitted'];
	
	//----------------- POSTED OR NOT
	$primarySubmit = $getCaseTypeRow['primarySubmit'];
	$secondarySubmit = $getCaseTypeRow['secondarySubmit'];
	$TertiarySubmit = $getCaseTypeRow['tertairySubmit'];
	if($submitted=='true'){
		if(($primarySubmit==1) || ($secondarySubmit==1) || ($TertiarySubmit==1)){
			$chargesPosted = "true";
		}else{
			$chargesPosted = "false";
		}
	}
	//----------------- POSTED OR NOT
	
	//-------------- HCFA PRINTER ONCE OR NOT
	if($postButton == 'Re-submit'){
		$initialPrinted = "true";
	}else{
		$initialPrinted = "false";
	}
	//-------------- HCFA PRINTER ONCE OR NOT
	
	$encounterCaseTypeId = $getCaseTypeRow['case_type_id'];	
	$admit_date = $getCaseTypeRow['admit_date'];
	$admit_date_time = explode(" ",$admit_date);
	$admit_date = $admit_date_time[0];
	$admit_time = $admit_date_time[1];
	
	$admit_date = get_date_format($admit_date);
	if($admit_date=="00-00-0000"){
		$admit_date = "";
		$admit_time = "";
	}
	if($admit_time=="00:00:00"){
		$admit_time="";
	}else{
		$admit_timeSplit=explode(":",$admit_time);
		$admit_time=$admit_timeSplit[0].":".$admit_timeSplit[1];
	}
	if($admit_time==":"){$admit_time="";}
	$disch_date = $getCaseTypeRow['disch_date'];
	$disch_date_time = explode(" ",$disch_date);
	$disch_date = $disch_date_time[0];
	$disch_time = $disch_date_time[1];
	$disch_date = get_date_format($disch_date);
	if($disch_date == "00-00-0000"){
		$disch_date="";
		$disch_time="";
	}
	if($disch_time=="00:00:00"){
		$disch_time="";
	}else{
		$disch_timeSplit=explode(":",$disch_time);
		$disch_time=$disch_timeSplit[0].":".$disch_timeSplit[1];
	}
	if($disch_time==":"){$disch_time="";}
	
	//----------------------- (PHYSICIAN PROVIDERS) -----------------------//
	$getPhysicianIdStr = "SELECT * FROM patient_charge_list_details WHERE $whr_del_chld charge_list_id='$charge_list_id' AND patient_id='$patient_id'";
	$getPhysicianIdQry = imw_query($getPhysicianIdStr);
	$getPhysicianIdRow = imw_fetch_array($getPhysicianIdQry);
	if($getPhysicianIdRow['newBalance']!=($getPhysicianIdRow['pri_due']+$getPhysicianIdRow['sec_due']+$getPhysicianIdRow['tri_due']+$getPhysicianIdRow['pat_due'])){
		set_payment_trans($encounter_id,'',$stop_clm_status);
	}
	$rev_rate = $getPhysicianIdRow['rev_rate'];
	$differ_insurance_bill = $getPhysicianIdRow['differ_insurance_bill'];
	$differ_patient_bill = $getPhysicianIdRow['differ_patient_bill'];
	$place_of_service_id = $getPhysicianIdRow['place_of_service'];
	$type_of_service_id = $getPhysicianIdRow['type_of_service'];

	$type_of_service = $tos_data_arr[$type_of_service_id]['tos_prac_cod'];
	
	$posFacilityId = $getPhysicianIdRow['posFacilityId'];
	$start_date  = $getPhysicianIdRow['start_date'];
	$end_dat = $getPhysicianIdRow['end_date'];
	
	
	if($start_date!='0000-00-00'){
		$st_date = get_date_format($start_date);
	}
	if($end_dat!='0000-00-00'){
		$end_date = get_date_format($end_dat);
	}
	
	//================== Getting POS Prac. Code ==================//
	
	$place_of_service = $pos_data_arr[$place_of_service_id]['pos_prac_code'];
		
	//================== Getting POS Prac. Code	==================//
	
	//================== Getting Payment ==================//
	
	$sql = imw_query("select pcdpi.paidForProc,pcdpi.charge_list_detail_id,pcpi.paymentClaims,pcpi.paid_by from patient_chargesheet_payment_info pcpi join patient_charges_detail_payment_info pcdpi
					 on pcpi.payment_id=pcdpi.payment_id WHERE pcpi.encounter_id='$encounter_id' and pcdpi.deletePayment='0'");
	while($row=imw_fetch_array($sql)){
		$payment_chld_data[$row['charge_list_detail_id']][]=$row['paidForProc'];
		$payment_claim_chld_data[$row['charge_list_detail_id']][$row['paymentClaims']][]=$row['paidForProc'];
		$payment_type_chld_data[$row['charge_list_detail_id']][$row['paid_by']][]=$row['paidForProc'];
	}
	
	$tot_paid_chk1=array_sum($payment_chld_data[0]);
	if($tot_paid_chk1>0){
		$copayPaid_chk='yes';
	}
	//================== Getting Payment ==================//	
}
if($sa_doctor_id>0){
	$primaryProviderId=$sa_doctor_id;
	$sa_facility_id=$sa_facility_id;
	$fac_prac_code=$fac_data_arr[$sa_facility_id]['fac_prac_code'];

	$sc_pos_fec_details1=(object)$pos_fac_data_arr[$fac_prac_code];
	$pos_id_sc=$sc_pos_fec_details1->pos_id;
}else{
	if($getCaseTypeRow['primaryProviderId']){
		$primaryProviderId=$getCaseTypeRow['primaryProviderId'];
	}else{
		$primaryProviderId=$default_providerID;
	}
}	
	
$phy_detail = $usr_name_arr[$primaryProviderId];
if(in_array($phy_detail['user_type'],$GLOBALS['arrValidCNPhy']) || $phy_detail['Enable_Scheduler']=='1'  || in_array($primaryProviderId,$enc_providers_arr)){
	$primaryProviderId=$primaryProviderId;
}else{
	$primaryProviderId=0;
}
	
if($primaryProviderId==0){
	$chart_qry=imw_query("select finalizerId  from chart_master_table where encounterId='$encounter_id'");
	$chart_row=imw_fetch_array($chart_qry);
	$finalizerId=$chart_row['finalizerId'];
	
	$phy_detail = $usr_name_arr[$finalizerId];
	if(in_array($phy_detail['user_type'],$GLOBALS['arrValidCNPhy'])){
		$primaryProviderId=$finalizerId;
	}else{
		$primaryProviderId=0;
	}
}
	
//-------- Primary Physician ----------
$priProvider = (object)$usr_name_arr[$primaryProviderId];
if($priProvider->lname){
	$pro1=", ";
}
$pro1_mname="";
if($priProvider->mname){
	$pro1_mname=" ".trim($priProvider->mname).'.';
}
$provider1_name = $priProvider->lname.$pro1.$priProvider->fname.$pro1_mname;
$provider1_g_id  = $priProvider->default_group;

//-------- Secondary Physician ----------
$secProvider = (object)$usr_name_arr[$getCaseTypeRow['secondaryProviderId']];
if($secProvider->lname){
	$pro2=", ";
}
$pro2_mname="";
if($secProvider->mname){
	$pro2_mname=" ".trim($secProvider->mname).'.';
}
$provider2_name = $secProvider->lname.$pro2.$secProvider->fname.$pro2_mname;

//-------- Tertiary Physician ----------
$terProvider = (object)$usr_name_arr[$getCaseTypeRow['tertiaryProviderId']];
if($terProvider->lname){
	$pro3=", ";
}
$pro3_mname="";
if($terProvider->mname){
	$pro3_mname=" ".trim($terProvider->mname).'.';
}
$provider3_name = $terProvider->lname.$pro3.$terProvider->fname.$pro3_mname;

//-------------------- TOS By Default HQ ------------------
if($type_of_service==''){
	foreach($tos_data_arr as $key=>$val){
		if($tos_data_arr[$key]['headquarter']=='1'){
			$type_of_service=$tos_data_arr[$key]['tos_prac_cod'];
		}
	}
}
//-------------------- TOS By Default HQ ------------------
	
//-------------------- POS By patient info and scheduler ------------------

if(empty($pos_id_sc) == true){
	$pos_id = $facility_name->pos_id;
	$main_pos_name=$facility_name->facilityPracCode;
}else{
	$pos_id=$pos_id_sc;
	$main_pos_name=$sc_pos_fec_details1->facilityPracCode;
	$posFacilityId=$sc_pos_fec_details1->pos_facility_id;
}

if($pos_id != '' && $place_of_service==''){		
	$place_of_service = $pos_data_arr[$pos_id]['pos_prac_code'];
}
//-------------------- POS By patient info scheduler ------------------
	
//-------------------- POS By Default HQ ------------------
if($place_of_service==''){
	foreach($pos_data_arr as $key=>$val){
		foreach($pos_id_fac_data_arr[$key] as $pf_key=>$pf_val){
			if($pos_id_fac_data_arr[$key][$pf_key]['headquarter']=='1'){
				$place_of_service=$pos_data_arr[$key]['pos_prac_code'];
				$posFacilityId=$pos_id_fac_data_arr[$key][$pf_key]['pos_facility_id'];
			}
		}
	}
}
//-------------------- POS By Default HQ ------------------
	
//--------------------------- OTHER DETAILS OF ENCOUNTER ---------------------------//
if(in_array(strtolower($billing_global_server_name), array('hattiesburg','keystone','gewirtz','millmanderr'))){
	$grp_ord=" desc";
}
$fet_groups=imw_query("select * from groups_new order by name $grp_ord");
while($row_groups=imw_fetch_array($fet_groups)){
	if($row_groups['group_institution']==1){
		$chk_ins_gro_arr[]=$row_groups['gro_id'];
	}
	$gro_data[]=$row_groups;
}

foreach($ins_data as $key=>$val){
	$row_groups4=$ins_data[$key];
	$referal_required[$row_groups4['type']]=$row_groups4['referal_required'];
	$insurance_dataId=$row_groups4['id'];
	$ins_type=$row_groups4['type'];
	if(($ins_type=="primary" && $chk_chld_pri==$row_groups4['provider']) || ($ins_type=="secondary" && $chk_chld_sec==$row_groups4['provider']) || ($ins_type=="tertiary" && $chk_chld_tri==$row_groups4['provider'])){
		//--- SCAN DOCUMENTS -----
		$scan_img_wid = '';
		$scan_img_src = '';
		if($insDataArr[$ins_type]['scan_card']==""){
			if(trim($row_groups4['scan_card']) != ''){
				$firstScanImage = data_path().$row_groups4['scan_card'];
				if(realpath($firstScanImage) != ''){
					$scan_img_wid = newImageResize($firstScanImage,20,20);
					$scan_img_src = "<img style=\"cursor:pointer\" onClick=\"show_scanned(this,'$insurance_dataId',1,'$ins_type')\" src=\"".data_path(1).$row_groups4['scan_card']."\" data-src=\"".data_path(1).$row_groups4['scan_card']."\" $scan_img_wid>";
				}
			}
			$insDataArr[$ins_type]['scan_card'] = $scan_img_src;
		}
		
		//--- SECOND SCAN DOCUMENTS -----
		$scan2_img_wid = '';
		$scan2_img_src = '';
		if($insDataArr[$ins_type]['scan_card2']==""){
			if(trim($row_groups4['scan_card2']) != '' && $insDataArr[$ins_type]['scan_card2']==""){
				$secondScanImage = data_path().$row_groups4['scan_card2'];
				if(realpath($secondScanImage) != ''){
					$scan2_img_wid = newImageResize($secondScanImage,20,20);
					$scan2_img_src = "<img style=\"cursor:pointer\" onClick=\"show_scanned(this,'$insurance_dataId',2,'$ins_type')\" src=\"".data_path(1).$row_groups4['scan_card2']."\" data-src=\"".data_path(1).$row_groups4['scan_card2']."\" $scan2_img_wid>";
				}
			}
			$insDataArr[$ins_type]['scan_card2'] = $scan2_img_src;
		}
	}
}
	
//-------- Primary Physician ----------
if($referal_required['primary']!='Yes' && $chk_reff_phy_id==0){
	if($priProvider->lname && $priProvider->fname){
		$refPhySicianName = $priProvider->lname.', ';
		$refPhySicianName .= $priProvider->fname;
		if($priProvider->mname){
			$refPhySicianName .=" ".$priProvider->mname;
		}
		$reff_phy_id="";
	}
}
if($chk_chl_opr_id>0){
	$operatorDetails = (object)$usr_name_arr[$chk_chl_opr_id];
	$operatorName = substr($operatorDetails->fname,0,1).substr($operatorDetails->mname,0,1).substr($operatorDetails->lname,0,1);
	$operator_read_id=$chk_chl_opr_id;
}else{
	$operatorName=$operatorName_mod;
	$operator_read_id=$operatorName_mod_id;
}
	
//create array for report type
$fet_type_code=imw_query("select * from elect_report_type order by id asc");

while($row_type_code=imw_fetch_object($fet_type_code))
{
	$row_type_code_arr[$row_type_code->code]=$row_type_code->code_desc;
}
//create array for transmission code
$fet_trans_code=imw_query("select * from elect_report_trans order by id asc");
while($row_trans_code=imw_fetch_object($fet_trans_code))
{
	$row_trans_code_arr[$row_trans_code->code]=$row_trans_code->code_desc;
}

//Get Adjustment Amount
$qry = imw_query("SELECT amountApplied,charge_list_detail_id,charge_list_detail_id_adjust FROM creditapplied WHERE crAppliedTo='adjustment' 
				  AND delete_credit='0' AND (patient_id = '$patient_id' or patient_id_adjust = '$patient_id')");
while($row=imw_fetch_array($qry)){
	if($row['charge_list_detail_id']>0){
		$crd_chld_data_arr[$row['charge_list_detail_id']][]=$row['amountApplied'];
	}
	if($row['charge_list_detail_id_adjust']>0){
		$crd_chld_data_arr[$row['charge_list_detail_id_adjust']][]=$row['amountApplied'];
	}
}									
?>
<?php
	$bgcolor_top = '#F4F9EE';
	$bgcolor_top1 = '#FFFFFF';
	if($encounter_id==""){
		if($_SESSION['acc_usr_data']['dos']){
			//$date_of_service=$_SESSION['acc_usr_data']['dos'];
		}
		if($_SESSION['acc_usr_data']['tosText']){
			$type_of_service=$_SESSION['acc_usr_data']['tosText'];
		}
		if($_SESSION['acc_usr_data']['posText']){
			$place_of_service=$_SESSION['acc_usr_data']['posText'];
		}
		if($_SESSION['acc_usr_data']['posFacilityCode']){
			$posFacilityId=$_SESSION['acc_usr_data']['posFacilityCode'];
			$main_pos_name="";
		}
		if($_SESSION['acc_usr_data']['primaryProviderText'][$patient_id]){
			$provider1_name=$_SESSION['acc_usr_data']['primaryProviderText'][$patient_id];
		}
		if($_SESSION['acc_usr_data']['secondaryProviderText'][$patient_id]){
			$provider2_name=$_SESSION['acc_usr_data']['secondaryProviderText'][$patient_id];
		}
		if($_SESSION['acc_usr_data']['tertiaryProviderText'][$patient_id]){
			$provider3_name=$_SESSION['acc_usr_data']['tertiaryProviderText'][$patient_id];
		}
		if($_SESSION['acc_usr_data']['Reffer_physician'][$patient_id]){
			$refPhySicianName=$_SESSION['acc_usr_data']['Reffer_physician'][$patient_id];
		}
		if($_SESSION['acc_usr_data']['refferingPhysician'][$patient_id]){
			$reff_phy_id=$_SESSION['acc_usr_data']['refferingPhysician'][$patient_id];
		}
		if($_SESSION['acc_usr_data']['referral'][$patient_id]){
			$referral=$_SESSION['acc_usr_data']['referral'][$patient_id];
		}
		if($_SESSION['acc_usr_data']['groups'][$patient_id]){
			$provider1_g_id=$_SESSION['acc_usr_data']['groups'][$patient_id];
		}
		if($_SESSION['acc_usr_data']['ins_defer'][$patient_id]){
			if($_SESSION['acc_usr_data']['ins_defer'][$patient_id]=="on"){
				$differ_insurance_bill="true";
			}
		} 
		if($_SESSION['acc_usr_data']['pat_defer'][$patient_id]){
			if($_SESSION['acc_usr_data']['pat_defer'][$patient_id]=="on"){
				$differ_patient_bill="true";
			}
		} 
		if($_SESSION['acc_usr_data']['vipPatient'][$patient_id]){
			$vipPatient=$_SESSION['acc_usr_data']['vipPatient'][$patient_id];
		} 
		if($_SESSION['acc_usr_data']['auth_no'][$patient_id]){
			//$auth_no=$_SESSION['acc_usr_data']['auth_no'][$patient_id];
		} 
		if($_SESSION['acc_usr_data']['auth_amount'][$patient_id]){
			//$auth_amount=$_SESSION['acc_usr_data']['auth_amount'][$patient_id];
		}
		if($_SESSION['acc_usr_data']['comment'][$patient_id]){
			//$comment=$_SESSION['acc_usr_data']['comment'][$patient_id];
		}
		
		$st_date = date('m-d-Y');
		$end_dat="";
	}
	if($encounter_id<=0 && $provider1_name!="" && strtolower($billing_global_server_name)=="lehigh" && in_array($priInsComDetails->Payer_id_pro,$arr_Medicare_payers) && in_array($priInsComDetails->Payer_id,$arr_Medicare_payers)){
		if(stristr($provider1_name,'.')){
			$Reffer_physician_chk_med=substr($provider1_name,0,-1);
		}else{
			$Reffer_physician_chk_med=$provider1_name;
		}
		list($reff_phy_id,$reff_phy_name) = $objCLSCommonFunction->chk_create_ref_phy($Reffer_physician_chk_med,7);
		$reff_phy_id=$reff_phy_id; 
		$refPhySicianName=$reff_phy_name; 
	}
	$referral_arr=array();
	if($referal_req_pri=='Yes' || $referal_req_sec=='Yes' || $referal_req_ter=='Yes'){
		$chk_reff_date=$enc_dos;
		$reff_qry=imw_query("select reffral_no,reff_type from patient_reff where insCaseid='$openInsCaseId' 
		and patient_id='$patient_id' and reff_phy_id='$reff_phy_id'
		and (end_date='0000-00-00' or end_date >= '$chk_reff_date') and effective_date <= '$chk_reff_date'
		and no_of_reffs > '0' and del_status='0' order by end_date desc,reff_id desc");
		while($reff_row=imw_fetch_array($reff_qry)){
			if($referal_req_pri=='Yes' && $reff_row['reff_type']=='1'){
				$referral_arr[]=array($reff_row['reffral_no'],$xyz, $reff_row['reffral_no']);
				if($referral==""){
					$referral=$reff_row['reffral_no'];
				}
			}
			if($referal_req_sec=='Yes' && $reff_row['reff_type']=='2'){
				$referral_arr[]=array($reff_row['reffral_no'],$xyz, $reff_row['reffral_no']);
				if($referral==""){
					$referral=$reff_row['reffral_no'];
				}
			}
			if($referal_req_ter=='Yes' && $reff_row['reff_type']=='3'){
				$referral_arr[]=array($reff_row['reffral_no'],$xyz, $reff_row['reffral_no']);
				if($referral==""){
					$referral=$reff_row['reffral_no'];
				}
			}
		}
	}
    
    
//-------------------- get POS facility from Users POS facility group ------------------
//if POS Facilty group exists and selected in logged in user
$user_pos_fac_arr=array();
if(isPosFacGroupEnabled() ){
    $u_sql_res=imw_query("Select id,posfacilitygroup_id from users where id='".$_SESSION['authId']."' and posfacilitygroup_id!='' ");
    $user_row=imw_fetch_assoc($u_sql_res);
    $user_pos_id_fac_data_arr=array();
    if(empty($user_row)==false && isset($user_row['posfacilitygroup_id']) && $user_row['posfacilitygroup_id']!='') {
        $posfacilitygroup_ids_arr=json_decode(html_entity_decode($user_row['posfacilitygroup_id']), true);
        $posfacgroup_ids_str=(empty($posfacilitygroup_ids_arr)==false)? implode(',',$posfacilitygroup_ids_arr): '';
        $selQry1 = "select * from pos_facilityies_tbl where posfacilitygroup_id IN(".$posfacgroup_ids_str.") order by pos_id ASC";
        $res1 = imw_query($selQry1);
        while($row1 = imw_fetch_array($res1)){
            $user_pos_id_fac_data_arr[$row1['pos_id']][]=$row1;
        }
    }
    if(empty($user_pos_id_fac_data_arr)==false) {
        foreach($pos_data_arr as $key=>$val){
            if($pos_data_arr[$key]['pos_prac_code']==$place_of_service){
                foreach($user_pos_id_fac_data_arr[$key] as $pf_key=>$pf_val){
                    $user_pos_fac_arr[]=$user_pos_id_fac_data_arr[$key][$pf_key]['pos_facility_id'];				
                }
            }
        }

    }
}

//-------------------- get POS facility from Users POS facility group ------------------

?>
<script>
var fac_tax_arr = JSON.parse('<?php echo json_encode($fac_tax_arr); ?>');
var return_chk_proc = '<?php echo $return_chk_proc; ?>';
var visit_code = '<?php echo $visit_code_id_imp; ?>';
var chk_visit_code_arr ='<?php echo $visit_code_arr_imp;?>';
var str_arr_desc_code_val ='<?php echo join('-~-~-',remLineBrk($str_arr_desc_code));?>';
var str_arr_desc_code = str_arr_desc_code_val.split('-~-~-');
var str_arr_proc_code_val ='<?php echo join('-~-~-',$str_arr_proc_code);?>';
var str_arr_proc_code = str_arr_proc_code_val.split('-~-~-');
var str_arr_proc_desc_code_val ='<?php echo join('-~-~-',remLineBrk($str_arr_proc_desc_code));?>';
var str_arr_proc_desc_code = str_arr_proc_desc_code_val.split('-~-~-');
var customarrayrev;
var customarrayTos;
var customarrayPos;
strPracCodeArray =new Array(<?php echo remLineBrk($strPracCodeArray);?>);
<?php if($stringAllProvider!=""){?>
	var customarrayProvider= new Array(<?php echo remLineBrk($stringAllProvider); ?>);
<?php }if($stringAllInsComp!=""){?>
	var customarrayInsComp= new Array(<?php echo remLineBrk($stringAllInsComp); ?>);
<?php }if($stringAllProcedures!=""){?>
	var customarrayProcedure= new Array(<?php echo remLineBrk($stringAllProcedures); ?>);
<?php }if($stringAllModifiers!=""){?>
	var customarrayModifiers= new Array(<?php echo remLineBrk($stringAllModifiers); ?>);
<?php }if($stringAllDiag!=""){?>
	var customarrayDiag= new Array(<?php echo remLineBrk($stringAllDiag); ?>);
<?php }if($stringAllInsCaseType!=""){?>
	var customarrayCaseType= new Array(<?php echo remLineBrk($stringAllInsCaseType); ?>);
<?php }if($stringAllTos!=""){?>
	var customarrayTos= new Array(<?php echo remLineBrk($stringAllTos); ?>);
<?php }if($stringAllPos!=""){?>
	var customarrayPos= new Array(<?php echo remLineBrk($stringAllPos); ?>);
<?php }if($stringAllrev!=""){?>
	var customarrayrev= new Array(<?php echo remLineBrk($stringAllrev); ?>);
<?php }if($stringAllproc!=""){?>
	var customarrayproc= new Array(<?php echo remLineBrk($stringAllproc); ?>);
<?php }else{?>
	var customarrayproc;
<?php }if($stringAllAuth!=""){?>
	var customarrayauth= new Array(<?php echo remLineBrk($stringAllAuth); ?>);
<?php }else{?>
	var customarrayauth;
<?php }	?>	
</script>
<?php
//--- SELF PAY CHECK ---------
$copay_show_self_pay=false;
if($charge_list_id>0){
	if($chk_chld_pri==0 && $chk_chld_sec==0 && $chk_chld_tri==0){
		$copay_show_self_pay=true;
	}
}else{
	if($self_pay_provider=='1'){
		$copay_show_self_pay=true;
	}
}
if($copay_show_self_pay==true){
	$priInsComId=$insurance_co1=$secInsComId=$insurance_co2=$terInsComId=$insurance_co3="";
	$alrt_ins_status=0;
	$chk_self_readonly=false;
}
?>
<form name="enter_charges" id="enter_charges" action="accounting_view.php" method="post">
<input type="hidden" name="anes_time_divisor" id="anes_time_divisor" value="<?php echo $anes_time_divisor;?>">
<input type="hidden" name="elem_procOrder" id="elem_procOrder">
<input type="hidden" name="pqri_pop_row" id="pqri_pop_row" value="">
<input type="hidden" name="form_submit" id="form_submit" value="yes">
<input type="hidden" id="encounterIdText" name="encounterIdText" value="<?php echo xss_rem($encounter_id); ?>">
<input type="hidden" name="detail_id" id="detail_id" value="<?php echo $charge_list_detail_id; ?>">
<input type="hidden" name="case" id="case" value="<?php echo xss_rem($case); ?>">
<input type="hidden" name="superBillId" id="superBillId" value="<?php echo xss_rem($superBillId); ?>">
<input type="hidden" name="superbillProcId" id="superbillProcId" value="<?php echo xss_rem($superbillProcId); ?>">
<input type="hidden" name="copayPaid_chk" id="copayPaid_chk" value="<?php echo $copayPaid_chk; ?>">
<input type="hidden" value="<?php echo $chargesPosted12; ?>" id="chargesPosted" name="chargesPosted">
<input type="hidden" value="<?php echo $initialPrinted; ?>" id="initialPrinted" name="initialPrinted">
<input type="hidden" value="<?php echo implode(',',$chk_ins_gro_arr); ?>" name="chk_gro_id" id="chk_gro_id">
<input type="hidden" value="" name="post_chrg_chk" id="post_chrg_chk">
<input type="hidden"  name="chk_frm_sub" id="chk_frm_sub" value="yes">
<input type="hidden" name="chkbox_for_post" id="chkbox_for_post" value="<?php echo $_REQUEST['chkbox_for_post']; ?>">
<input type="hidden" name="enc_icd10" id="enc_icd10" value="<?php echo $enc_icd10; ?>">
<input type="hidden" name="Payer_id_pro" id="Payer_id_pro" value="<?php echo $priInsComDetails->Payer_id_pro; ?>">
<input type="hidden" name="Payer_id" id="Payer_id" value="<?php echo $priInsComDetails->Payer_id; ?>">
<input type="hidden" name="vip_ref_not_collect" id="vip_ref_not_collect" value="<?php echo $policyQryRes['vip_ref_not_collect'];?>" />
<div class="row form-group">
	<div class="col-sm-2">
    	<div class="col-sm-6">
        	<label class="label_fw" for="">Groups</label>
            <?php 
				if($charge_list_id==""){
					$chlist_gro_id=$provider1_g_id;
				}
			?>
            <input type="hidden" name="optionalReferral" id="optionalReferral" value="<?php echo $optional_referral; ?>" onChange="set_frm_post();">
            <select name="groups" id="groups" class="selectpicker" data-width="100%" onChange="anes_fun();show_rev_div();set_frm_post();">
				<?php
                    foreach($gro_data as $key=>$val){
                        $row_groups=$gro_data[$key];
                        if($getCaseTypeRow['gro_id']==$row_groups['gro_id'] || $row_groups['del_status']=='0'){
                        $row_groups=$gro_data[$key];	
                        $txt_color = ($row_groups['del_status'])?'style="color:red;"':'';
                ?>
                        <option <?php echo $txt_color; ?> value="<?php echo $row_groups['gro_id'].'_'.$row_groups['group_anesthesia'].'_'.$row_groups['group_institution'];?>" <?php if($chlist_gro_id==$row_groups['gro_id']){echo "selected";} ?>><?php echo stripslashes($row_groups['name']);?></option>
                <?php }} ?>
			</select>
        </div>
        <div class="col-sm-6">
        	<label class="label_fw" for="">Claim type</label>
            <select name="billing_type" id="billing_type" class="selectpicker" data-width="100%" onChange="anes_fun(this.value);show_rev_div();">
				<option value="0" <?php if($billing_type==0){echo "selected";} ?>>Claim Type</option>
                <option value="3" <?php if($billing_type==3){echo "selected";} ?>>Professional</option>
                <option value="1" <?php if($billing_type==1){echo "selected";} ?>>Anesthesia</option>
                <option value="2" <?php if($billing_type==2){echo "selected";} ?>>Institution</option>
			</select>
        </div>
	</div>
	<div class="col-sm-2">
		<label class="label_fw">DOS<div id="coll_date" style="font-weight:bold;" class="pull-right"></div></label>
		<?php if(!$date_of_service){$date_of_service = date(phpDateFormat());} ?>
		<div class="input-group">
			<input type="text" name="dos" id="dos_id" value="<?php echo $date_of_service; ?>" class="form-control date-pick" onBlur="fillDates(this);" onChange="loadCaseInfo('<?php echo $encounter_id; ?>');set_icd10('dos');<?php if($encounter_id==""){?>case_type_id_sch('<?php echo $patient_id; ?>',this.value);<?php } ?>set_frm_post();generateControleNo('<?php echo $patient_id; ?>');">
			<label class="input-group-addon pointer" for="dos_id"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
		</div>
	</div>
	<div class="col-sm-2">
		<label for="enc_id_read">Encounter <?php if($hl7_account_num!=""){ echo "(A/C# ".$hl7_account_num.")";}?></label>
		<input type="text" name="enc_id_read" id="enc_id_read" value="<?php echo $encounter_id; ?>" class="form-control" readonly onChange="set_frm_post();">
	</div>
     <div class="col-sm-6 pt4">
         <div class="col-sm-3">
            <label for="" class="text_purple" style="cursor:pointer;" onClick="top.change_main_Selection(top.document.getElementById('Insurance'));">Ins. Case</label>
            <select name="caseTypeText" id="caseTypeText" class="selectpicker" data-width="100%" onChange="return loadCaseInfo('<?php echo $encounter_id; ?>');set_frm_post();">
                <?php print $insCasesOption; ?>
                <?php
                if(!$insCasesOption){
                    ?>
                    <option value="Self">Self Pay</option><?php 
                }
                ?>
            </select>
        </div>
        <div class="col-sm-2">
			<label for="ar_claim_status">AR Status</label>
            <select name="ar_claim_status" id="ar_claim_status" class="selectpicker" data-width="100%" onChange="return loadCaseInfo('<?php echo $encounter_id; ?>');set_frm_post();">
                <option value="">Please Select</option>
				<?php
                foreach($statusArr as $key=>$name)
                {
					$sel = '';
					if ($key==$ar_claim_status_id){
						$sel = 'SELECTED';
					}
                	echo "<option value='$key' $sel>$name</option>";
                }
                ?>
            </select>
        </div>
      	<div class="checkbox text_purple checkbox-inline">
            <input type="checkbox" name="chk_self" id="chk_self" value="Self" onClick="loadCaseInfo('<?php echo $encounter_id; ?>'); set_frm_post(); chk_all_self_proc();" <?php if($copay_show_self_pay==true){echo "checked ";} if($chk_self_readonly==true){echo "disabled";}?> >
            <label for="chk_self">Self Pay</label>
        </div>
        <div class="checkbox text_purple checkbox-inline">
            <input type="checkbox" <?php if($vipPatient=="true" || $default_vip_chk=="true") echo "CHECKED"; ?> value="true" id="vipPatient" name="vipPatient" onClick="set_frm_post();refractionChkFun('','','');">
            <label for="vipPatient">VIP</label>
        </div>  
        <div class="checkbox text_purple checkbox-inline">
            <input type="checkbox" <?php if($differ_insurance_bill=="true") echo "CHECKED" ?> id="ins_defer" name="ins_defer" onClick="set_frm_post();">
            <label for="ins_defer">Defer Ins. Bill</label>
        </div>
        <div class="checkbox text_purple checkbox-inline">
            <input type="checkbox" <?php if($differ_patient_bill=="true") echo "CHECKED" ?> name="pat_defer" id="pat_defer"  onClick="set_frm_post();">
            <label for="pat_defer">Defer Pt. Bill</label>
        </div>   
    </div>
</div>
<div class="row form-group">
    <div class="col-sm-2">
		<label for="primary_provider_id">Billing Provider</label>
		<div class="input-group">
			<input type="text" name="primaryProviderText" id="primary_provider_id" value="<?php echo $provider1_name; ?>" class="form-control" onChange="set_ref_phy();set_frm_post();set_auth_drop('<?php echo $encounter_id; ?>');setTimeout(function(){set_credited_prov();},500);">
			<?php echo get_simple_menu($arrProviderCodes,"primary_provider_id_menu","primary_provider_id","300","0",$sm_unique_id++);?>
		</div>
	</div>
    <?php if($provider2_name=="" && $encounter_id<=0){$provider2_name=$provider1_name;}?>
	<div class="col-sm-2">
		<label for="secondary_provider_id">Credited Provider</label>
		<div class="input-group">
			<input type="text" name="secondaryProviderText" id="secondary_provider_id" value="<?php echo $provider2_name; ?>" class="form-control" onChange="set_frm_post();">
			<?php echo get_simple_menu($arrProviderCodes,"secondary_provider_id_menu","secondary_provider_id","300","0",$sm_unique_id++);?>
		</div>	
	</div>
	<div class="col-sm-2">
    	<input type="hidden" name="refferingPhysician" id="refferingPhysician" value="<?php print $reff_phy_id; ?>" />
    	<label class="label_fw" for="">
        	Ref. Physician
           <div class="checkbox text_purple checkbox-inline pull-right">	
               <input type="checkbox" name="reff_phy_nr" id="reff_phy_nr" <?php if($reff_phy_nr=='1') echo "CHECKED"; ?>  onChange="set_frm_post();">
               <label for="reff_phy_nr">NR</label>
           </div>
    	</label>
        <div>
            <input type="text" name="Reffer_physician" id="reffer_physician"  value="<?php echo  $refPhySicianName; ?>" class="form-control" onChange="set_frm_post();set_reff_drop();" onKeyUp="top.loadPhysicians(this,'refferingPhysician');">
        </div>
	</div>
	<div class="col-sm-2">
    	<div class="col-sm-6">
			<?php if($charge_list_id>0){ $referral=$chk_referral;}?>
            <label for="referral">Referral <?php getHashOrNo();?></label>
            <div class="input-group" id="reff_rec_id">
                <input type="text" name="referral" id="referral" value="<?php echo $referral; ?>" class="form-control" onChange="set_frm_post();">
                <?php echo get_simple_menu($referral_arr,"referral_menu","referral","300","0",$sm_unique_id++); ?>
            </div>
        </div>
        <div class="col-sm-6">
        	<label for="control_no">Control <?php getHashOrNo();?></label>
			<input type="text" name="control_no" id="control_no" value="<?php echo $control_no; ?>" class="form-control">
        </div>    
	</div>
	<div class="col-sm-2">
    	<div class="col-sm-6">
            <label for="auth_no">Auth <?php getHashOrNo();?></label>
            <input type="hidden" name="auth_id" id="auth_id" value="<?php echo $auth_id; ?>" >
            <div class="input-group" id="auth_rec_id">
                <input type="text" name="auth_no" id="auth_no" value="<?php echo $auth_no; ?>" class="form-control" onChange="set_frm_post();set_auth_info(this);">
                <?php echo get_simple_menu($auth_arr,"auth_no_menu","auth_no","300","0",$sm_unique_id++); ?>
            </div>
        </div>
        <div class="col-sm-6">
        	<label for="auth_amount">Auth Amount</label>
			<div class="input-group">
				<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
				<input type="text" name="auth_amount" id="auth_amount" value="<?php echo number_format($auth_amount,2); ?>" class="form-control" onChange="set_frm_post();">
			</div>
        </div>     
	</div>
	<div class="col-sm-2">
		<div class="col-sm-6" title="<?php echo $row_type_code_arr[$report_type_code]; ?>" id="report_type_code_td">
			<label for="report_type_code">R. Type</label>
			 <select name="report_type_code" id="report_type_code" onChange="getTdTitle('report_type_code','report_type_code_td')" class="selectpicker" data-width="100%">
				<option value=""><?php echo imw_msg('drop_sel'); ?></option>
				  <?php
					foreach($row_type_code_arr as $key=>$val){
						$report_type_code_sel="";
						if($key==$report_type_code){$report_type_code_sel="selected";}
						echo"<option value='$key' data-header='$val' $report_type_code_sel>$key</option>";
					}
				?>
			 </select>
		</div>
        <div class="col-sm-6">
			<label for="transmission_code">Transmission</label>
			 <select name="transmission_code" id="transmission_code" onChange="getTdTitle('transmission_code','transmission_code_td')" class="selectpicker" data-width="100%">
				<option value=""><?php echo imw_msg('drop_sel'); ?></option>
				   <?php
					foreach($row_trans_code_arr as $key=>$val){
						$transmission_code_sel="";
						if($key==$transmission_code){$transmission_code_sel="selected";}
						echo"<option value='$key' data-header='$val' $transmission_code_sel>$key</option>";
					}
				  ?>
			 </select>
		</div>
	</div>
</div>
<div class="row form-group">
	<div class="col-sm-2">
		<label for="primaryInsId">Pri. Ins</label>
		<div class="form-inline">
        	<input type="hidden" name="pri_institutional_type" id="pri_institutional_type" value="<?php echo $institutional_type; ?>">
			<input type="hidden" name="getPriInsId" id="getPriInsId" value="<?php if($priInsComId){ echo $priInsComId;}else{ echo '0';} ?>">
			<input type="text" name="primaryInsText" id="primaryInsId" value="<?php print $insurance_co1; ?>" class="form-control" readonly onChange="set_frm_post();" style="width:80%;">				
			<span id="pri_scan_card">
				<?php 
					if($insurance_co1){
						if($insDataArr['primary']['scan_card']){
							echo $insDataArr['primary']['scan_card'];
						}
					}	
				 ?>
			 </span>
			 <span id="pri_scan_card2">
				<?php 
					if($insurance_co1){
						if($insDataArr['primary']['scan_card2']){
							echo $insDataArr['primary']['scan_card2'];
						}
					}	
				 ?>
			 </span>
		 </div>
	</div>
    <div class="col-sm-2">
		<label for="secondaryInsId">Sec. ins</label>
		<div class="form-inline">
			<input type="hidden" name="getSecInsId" id="getSecInsId" value="<?php if($secInsComId){ echo $secInsComId;}else{ echo '0';} ?>">
			<input type="text" name="secondaryInsText" id="secondaryInsId" value="<?php print $insurance_co2; ?>" class="form-control" readonly onChange="set_frm_post();"  style="width:80%;">
			<span id="sec_scan_card">
				<?php 
					if($insurance_co2){
						if($insDataArr['secondary']['scan_card']){
							echo $insDataArr['secondary']['scan_card'];
						}
					}	
				 ?>
			 </span>
			 <span id="sec_scan_card2">
				<?php 
					if($insurance_co2){
						if($insDataArr['secondary']['scan_card2']){
							echo $insDataArr['secondary']['scan_card2'];
						}
					}	
				 ?>
			 </span>
		 </div>
	</div>
    <div class="col-sm-2">
		<label for="tertiaryInsId">Ter. Ins</label>
        <div class="form-inline">
            <input type="hidden" name="pay_copay_chg3" id="pay_copay_chg3" value="<?php echo number_format($insur_chk_copay_ter,2);?>" >
            <input type="hidden" name="getTriInsId" id="getTriInsId" value="<?php if($terInsComId){ echo $terInsComId;}else{ echo '0';} ?>">
            <input type="text" name="tertiaryInsText" id="tertiaryInsId" value="<?php print $insurance_co3; ?>" class="form-control" readonly onChange="set_frm_post();" style="width:80%;">				
            <span id="ter_scan_card">
                <?php 
                    if($insurance_co3){
                        if($insDataArr['tertiary']['scan_card']){
                            echo $insDataArr['tertiary']['scan_card'];
                        }
                    }
                 ?>
             </span>
             <span id="ter_scan_card2">
                <?php 
                    if($insurance_co3){
                        if($insDataArr['tertiary']['scan_card2']){
                            echo $insDataArr['tertiary']['scan_card2'];
                        }
                    }
                 ?>
             </span>
        </div>  	 
	</div>
	<div class="col-sm-3" id="copayTd">
    	<?php 
		$getCopay = false;
		if(($copay_chk>0 || $insur_chk_copay>0) && $alrt_ins_status==0){
			$getCopay = true;
			if($copay_chk>0){
				$copay_chk=$copay_chk;
			}else{
				$copay_chk=$insur_chk_copay;
			}
		}	
		if(($pri_copay>0 || $charge_list_id>0) && $alrt_ins_status==0){
			$insur_chk_copay=$pri_copay;
		}
		if(($sec_copay>0 || $charge_list_id>0) && $alrt_ins_status==0){
			$insur_chk_copay_sec=$sec_copay;
		}
		?>	
		<?php
			if($copay_show_self_pay==true){
				$insur_chk_copay="";
			}
		?>
    	<div class="col-sm-6">
            <label class="label_fw" for="">
                Pri. CoPay
               <div class="checkbox text_purple checkbox-inline pull-right">	
				<?php	
                    if($getCopay == true){
                        if($copayPaid==1 && $insur_chk_copay>0){
                            ?>
                            <img src="../../library/images/confirm.gif" width="16px">
                            <?php 
                        }else{
                            if($copayPaid_chk!='yes'){
                                if($coPayWriteOff!='1'){
                                ?>
                                <div class="checkbox text_purple">
                                    <input type="checkbox" name="coPayNotRequired" id="coPayNotRequired" <?php if($iscoPayNotRequired == 1 || $vip_copay_not_collect==1) echo "CHECKED"; ?>  onChange="set_frm_post();">
                                    <label for="coPayNotRequired">NR</label>
                                </div>
                                <?php
                                }else{
                                ?>
                                <span style="font-family:Arial; font-size:12px; font-weight:bold; color:#0000FF;">Write Off</span>
                                <?php
                                }	
                            }	
                        }
                    }				
                ?>
               </div>
            </label>
            <div class="input-group">
                <div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
                <input type="hidden" name="copay_pri_hid" id="copay_pri_hid" value="<?php echo number_format($insur_chk_copay,2);?>">
                <input type="text" name="pay_copay_chg" id="pay_copay_chg" value="<?php echo number_format($insur_chk_copay,2);?>" class="form-control" <?php if($copayPaid_chk=='yes' || $copayPaid==1 || $encounter_id==""){ ?>readonly="readonly"<?php } ?>  onChange=" <?php if($acc_view_chr_only == 0){ ?> edit_copay_ins(); <?php } ?> set_frm_post();">
            </div>
        </div>
        <div class="col-sm-6">
        	<?php 
                $getCopay = false;
                if(($copay_chk>0 || $insur_chk_copay_sec) && $alrt_ins_status==0){
                    $getCopay = true;
                    if($copay_chk>0){
                        $copay_chk_sec=$copay_chk;
                    }else{
                        $copay_chk_sec=$insur_chk_copay_sec;
                    }
                }
            ?>
            <label class="label_fw" for="">
            	Sec. Copay
                <div class="checkbox text_purple checkbox-inline pull-right">	
                    <?php	
                        if($getCopay == true){
                            if($copayPaid==1 && $insur_chk_copay_sec>0){
                                ?>
                                <img src="../../library/images/confirm.gif" width="16px">
                                <?php 
                            }else{
                                if($copayPaid_chk!='yes'){
                                    if($coPayWriteOff!='1'){
                                    ?>
                                    <div class="checkbox text_purple">
                                        <input type="checkbox" name="coPayNotRequired2" id="coPayNotRequired2" <?php if($iscoPayNotRequired2 == 1 || $vip_copay_not_collect==1) echo "CHECKED"; ?>  onChange="set_frm_post();">
                                        <label for="coPayNotRequired2">NR</label>
                                    </div>
                                    <?php
                                    }else{
                                    ?>
                                    <span style="font-family:Arial; font-size:12px; font-weight:bold; color:#0000FF;">Write Off</span>
                                    <?php
                                    }
                                }		
                            }
                        }			
                    ?>
                </div>
            </label>
            <div class="input-group">
                <div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
                <input type="hidden" name="copay_sec_hid" id="copay_sec_hid" value="<?php echo number_format($insur_chk_copay_sec,2);?>">
                <input type="text" name="pay_copay_chg2" id="pay_copay_chg2" value="<?php echo number_format($insur_chk_copay_sec,2);?>" class="form-control" <?php if($copayPaid==1 || $encounter_id=="" || $copayPaid_chk=='yes'){ ?>readonly="readonly"<?php } ?> onChange="<?php if($acc_view_chr_only == 0){ ?> edit_copay_ins(); <?php } ?> set_frm_post();">
            </div>
        </div>
	</div>
   	<div class="col-sm-1">
       <label for="tosText_id">TOS</label>
        <div class="input-group">
            <input type="text" name="tosText" id="tosText_id" value="<?php echo $type_of_service; ?>" class="form-control" onChange="set_frm_post();">
            <?php echo get_simple_menu($tosArray,"tosText_menu","tosText_id","300","0",$sm_unique_id++);?>
        </div>	
    </div>
    <div class="col-sm-2">
        <div class="col-sm-4">
            <label for="posText">POS</label>
            <div class="input-group">
                <input type="text" name="posText" id="posText" value="<?php echo $place_of_service; ?>" class="form-control" onChange="set_frm_post(); return checkPosFn();">
                <?php echo get_simple_menu($posArray,"posText_menu","posText","300","0",$sm_unique_id++);?>
            </div>	
        </div>
        <div class="col-sm-8">
            <label for="posFacilityCode">POS Facility</label>
            <?php
                if($posFacilityId==0 || $posFacilityId==""){
                    $posFacilityId=$facility_name->pos_facility_id;
                }
            ?>
            <select name="posFacilityCode" id="posFacilityCode" class="form-control minimal" data-width="100%" onChange="set_frm_post();">
                <?php
                foreach($pos_data_arr as $key=>$val){
                    if($pos_data_arr[$key]['pos_prac_code']==$place_of_service){
                        foreach($pos_id_fac_data_arr[$key] as $pf_key=>$pf_val){
                            $posFacilityDetails=$pos_id_fac_data_arr[$key][$pf_key];				
                            $id = $posFacilityDetails['pos_facility_id'];
                            $facilityPracCode = $posFacilityDetails['facilityPracCode'];
                            $sel = $posFacilityId == $id ? 'selected="selected"': '';
                            if(empty($user_pos_fac_arr)==false && (!in_array($id,$user_pos_fac_arr)) ) {
                                if($posFacilityId == $id && $encounter_id!="")
                                    { /*do not skip already selected pos facility which does not exists in users pos facility group */ }
                                else 
                                    { /*skip pos facility those does not exists in users pos facility group */
                                    continue; }
                            }
                            if(isPosFacGroupEnabled() ){
                                if( isset($posFacilityDetails['posfacilitygroup_id']) && $posFacilityDetails['posfacilitygroup_id']==0 && ($posFacilityId != $id || $encounter_id=="") ) continue;
                            }
                            print '<option '.$sel.' value="'.$id.'">'.$facilityPracCode.'</option>';
                        }
                    }
                }
                ?>
            </select>
            <?php
                if($main_pos_name==""){
                    if($posFacilityId==0 || $posFacilityId==""){
                        $posFacilityId=$facility_name->pos_facility_id;
                    }
                    $main_pos_name=$pos_fac_data_arr[$posFacilityId]['facilityPracCode'];
                }
            ?>
            <input type="hidden" name="fac_name_read" id="fac_name_read" value="<?php echo $main_pos_name; ?>" class="form-control" readonly onChange="set_frm_post();">
        </div>
   </div>   
</div>
<div class="row form-group">
	<div class="col-sm-2">
    	<label for="admit_date">Onset Date</label>
        <div class="input-group">
            <input type="text" name="admit_date" id="admit_date" value="<?php echo $admit_date; ?>" class="form-control date-pick" onBlur="checkdate(this);" onChange="set_frm_post();">
            <label class="input-group-addon pointer" for="admit_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
        </div>
    </div>
	<div class="col-sm-2">
		<div class="row">
			<div class="col-sm-6">
				<label for="admit_time">Start Time</label>
				<input type="text" name="admit_time" id="admit_time" value="<?php echo $admit_time; ?>" class="form-control" onChange="anes_fun($('#billing_type').val());">
			</div>
			<div class="col-sm-6">
                <label for="disch_time">End Time</label>
                <input type="hidden" name="disch_date" id="disch_date" value="<?php echo $disch_date; ?>" class="form-control date-pick" onBlur="checkdate(this);" onChange="set_frm_post();">
                <input type="text" name="disch_time" id="disch_time" value="<?php echo $disch_time; ?>" class="form-control" onChange="anes_fun($('#billing_type').val());">
			</div>
		</div>	
	</div>
    <div class="col-sm-2">
        <div class="col-sm-6">
        	<label for="acc_anes_time">Duration (min)</label>
			<input type="text" name="acc_anes_time" id="acc_anes_time" value="<?php echo $acc_anes_time; ?>" class="form-control">
        </div>
        <div class="col-sm-6">
        	<label for="acc_anes_unit">Units</label>
			<input type="text" name="acc_anes_unit" id="acc_anes_unit" value="<?php echo $acc_anes_unit; ?>" class="form-control">
		</div>   
	</div>
	<div class="col-sm-1">
    	<label for="read_tot_charges">T.Charges</label>
        <div class="input-group">
            <div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
            <input type="text" name="read_tot_charges" id="read_tot_charges" value="0.00" readonly class="form-control">
        </div>  
	</div>
    <div class="col-sm-1">
        <label for="operator_read">Operator</label>
        <input type="text" name="operator_read" id="operator_read" value="<?php echo $operatorName; ?>" class="form-control" onChange="set_frm_post();">
        <input type="hidden" name="operator_read_id" id="operator_read_id" value="<?php echo $operator_read_id; ?>" onChange="set_frm_post();">
    </div>
    <div class="col-sm-2">
        <label for="chrg_mod_opr_show">Mod Operator</label>
        <select name="chrg_mod_opr_show" id="chrg_mod_opr_show"  class="selectpicker" data-width="100%" data-title="" onChange="set_frm_post();">
        <?php
        $fet_chl_mod=imw_query("select modifier_by,modifier_on from patient_charge_list_modifiy where enc_id='$encounter_id' and enc_id>0 order by id desc");
        while($row_chl_mod=imw_fetch_array($fet_chl_mod)){
            if($row_chl_mod['modifier_by']>0){
                $show_operatorName_mod_id=$row_chl_mod['modifier_by'];
                $operatorDetails_show = (object)$usr_name_arr[$show_operatorName_mod_id];
                //$show_operatorName_mod = substr($operatorDetails_show->fname,0,1).substr($operatorDetails_show->mname,0,1).substr($operatorDetails_show->lname,0,1);
				$show_operatorName_mod = $operatorDetails_show->lname.', '.$operatorDetails_show->fname;
				if($operatorDetails_show->mname!=""){
					$show_operatorName_mod.=" ".trim($operatorDetails_show->mname);
				}
                $modifier_on_exp=explode(' ',$row_chl_mod['modifier_on']);
                $modifier_on_date_exp=explode('-',$modifier_on_exp[0]);
                $modifier_on_time_exp=explode(':',$modifier_on_exp[1]);
                $show_chld_modifier_on=$modifier_on_date_exp[1].'-'.$modifier_on_date_exp[2].'-'.$modifier_on_date_exp[0].' '.$modifier_on_time_exp[0].':'.$modifier_on_time_exp[1].' '.strtoupper($modifier_on_exp[2]);
        ?>
                <option value=""><?php echo $show_chld_modifier_on.' - '.$show_operatorName_mod?></option>
        <?php	
            }
        }
        ?>
        </select>	
        <input type="hidden" name="chrg_mod_opr" id="chrg_mod_opr" value="<?php echo $operatorName_mod; ?>" onChange="set_frm_post();">
        <input type="hidden" name="chrg_mod_opr_id" id="chrg_mod_opr_id" value="<?php echo $operatorName_mod_id; ?>" onChange="set_frm_post();">
    </div>
     <div class="col-sm-1">
        <label for="Billing">Billing Facility</label>
        <?php
			if($facility_id==0 || $facility_id==""){
				//$facility_id=$facilityId;
			}
		?>
        <select name="billing_facility_id" id="billing_facility_id" class="selectpicker" data-width="100%" onChange="set_frm_post();addTax();">
            <option value="">Please Select</option>
			<?php
				$sel_chk="";
           		foreach($fac_data_arr as $fac_key=>$fac_val){
					$FacilityDetails=$fac_data_arr[$fac_key];				
					$id = $FacilityDetails['id'];
					$sel="";
					if($sel_chk==""){
						if($billing_facility_id==$id){
							$sel=$sel_chk='selected="selected"';
						}else if(strtolower($bl_pt_home_facility)=="yes" && $billing_facility_id<=0 && $default_facility==$FacilityDetails['fac_prac_code'] && $encounter_id<=0 && $FacilityDetails['fac_prac_code']>0){
							$sel=$sel_chk='selected="selected"';
						}else if($facilityId>0 && $facilityId==$id && $encounter_id<=0){
							$sel=$sel_chk='selected="selected"';
						}
						/*else if($billing_facility_id<=0 && $FacilityDetails['facility_type']=="1" && $encounter_id<=0){
							$sel=$sel_chk='selected="selected"';
						}*/
					}
					print '<option '.$sel.' value="'.$id.'">'.$FacilityDetails['name'].'</option>';
				}
            ?>
        </select>
    </div>
    <div class="auto_state_css col-sm-1">
        <label for="auto_state">State</label>
        <input type="text" name="auto_state" id="auto_state" value="<?php print $auto_state; ?>" onChange="set_frm_post();" class="form-control" title="Place of Accident">				
     </div>
</div>
<div class="row form-group">
	<?php
		$all_dx_codes_arr=unserialize(html_entity_decode($all_dx_codes));
		$dx_code_title_arr=array();
		if($enc_icd10>0){
			if(count($all_dx_codes_arr)>0){
				$dx_code_title_arr=get_icd10_desc($all_dx_codes_arr,0);
			}
		}
	?>
	<?php 
		for($d=1;$d<=12;$d++){
			
		if($enc_icd10>0){
			$dx_val2=$all_dx_codes_arr[$d];
			$icd10_desc_arr[$all_dx_codes_arr[$d]]=$dx_code_title_arr[$dx_val2];
		}else{
			if($all_dx_codes_arr[$d]!="" && $arrDxCodechld[$all_dx_codes_arr[$d]]!=""){
				$dx_desc_arr[$all_dx_codes_arr[$d]]=$all_dx_codes_arr[$d].' - '.$arrDxCodechld[$all_dx_codes_arr[$d]];
			}
		}	
		$dx_code_title="";
		$dx_val2="";
		if($enc_icd10>0){
			if($all_dx_codes_arr[$d]!=""){
				$dx_code_title=$dx_code_title_arr[$all_dx_codes_arr[$d]];
			}
		}else{
			if($all_dx_codes_arr[$d]!="" && $arrDxCodechld[$all_dx_codes_arr[$d]]!=""){
				$dx_code_title=$all_dx_codes_arr[$d].' - '.$arrDxCodechld[$all_dx_codes_arr[$d]];
			}
		}
	?>
	<div class="col-sm-1" id="diagText_span_<?php echo $d; ?>">
		<label for="diagText_<?php echo $d; ?>"><?php echo "DX".$d; ?></label>
		<input type="text" name="diagText_<?php echo $d; ?>" id="diagText_<?php echo $d; ?>" value="<?php echo $all_dx_codes_arr[$d]; ?>" <?php echo show_tooltip($dx_code_title,'top'); ?> class="form-control dx_box_12" onChange="checkDB4Code_fun(this); return chkValidation('diagText_<?php echo $d; ?>');">
		<input type="hidden" name="old_diagText_<?php echo $d; ?>" id="old_diagText_<?php echo $d; ?>" value="" class="old_dx_box_12">
		<input type="hidden" name="lit_diagText_<?php echo $d; ?>" id="lit_diagText_<?php echo $d; ?>" value="" class="lit_dx_box_12">
	</div>
	 <?php }?>
</div>
<?php
	if($control_no==""){
		echo "<script type='application/javascript'>generateControleNo(".$patient_id.");</script>";
	}
?>
<div class="clearfix"></div>
<div id="divWorkView" style="width:100%; height:<?php echo $_SESSION['wn_height']-600; ?>px; overflow:scroll;" class="table-responsive">
    <div id="dxdrop" style="position:absolute;top:0px;"></div>
	<table class="table table-bordered" id="acc_main_tbl">
		<tr id="top_row_id" class="grythead">
        	<th></th>
			<th>
				<div class="checkbox">
					<input type="checkbox" name="chkbx_all" id="chkbx_all" onClick="return chk_all();" onChange="set_frm_post();">
					<label for="chkbx_all"></label>
				</div>
			</th>
			<th class="text-nowrap">Self Pay</th>
			<th>Procedure</th>
			<th id="rev_code_txt" class="text-nowrap">Rev Code</th>
			<th id="proc_code_txt" class="text-nowrap">Proc Code</th>
			<th id="dx_code_head">Dx Codes</th>
			<th>Mod1</th>
			<th>Mod2</th>
			<th>Mod3</th>
            <th>Mod4</th>
			<th style="min-width:70px;">Unit</th>
			<th>Charges</th>
			<th>Net Amt</th>
            <th>App1</th>
			<th>App1 Date</th>
			<th>App2</th>
			<th>App2 Date</th>
			<th>Rev/Rate</th>
			<th>Comment</th>
		</tr>
		<tbody id="sortable" style="cursor:move;">
		<?php
			$chkbox_for_post_arr=array();
			$chkbox_for_post_arr=explode(",",$_REQUEST['chkbox_for_post']);
			$getProcedureDetailsStr = "SELECT * FROM patient_charge_list_details WHERE  $whr_del_chld charge_list_id='$charge_list_id' order by display_order,charge_list_detail_id";					
			$getProcedureDetailsQry = imw_query($getProcedureDetailsStr);
			$pro_cont=0;
			if($charge_list_id>0){
				while($getProcedureDetailsRows = imw_fetch_array($getProcedureDetailsQry)){
					$charge_list_detail_id = $getProcedureDetailsRows['charge_list_detail_id'];
					$procId = $getProcedureDetailsRows['procCode'];
					$paidStatus = $getProcedureDetailsRows['paidStatus'];
					$start_date = $getProcedureDetailsRows['start_date'];
					$end_date = $getProcedureDetailsRows['end_date'];
					$units = $getProcedureDetailsRows['units'];
					$procCharges = $getProcedureDetailsRows['procCharges'];
					$totalFee = $getProcedureDetailsRows['totalAmount'];
					$paidForProc = $getProcedureDetailsRows['paidForProc'];
					$overPaymentForProc = $getProcedureDetailsRows['overPaymentForProc'];
					$balForProc = $getProcedureDetailsRows['balForProc'];
					$approvedAmt_chk = $getProcedureDetailsRows['approvedAmt'];
					$newBalanceToAlter = $getProcedureDetailsRows['newBalance'];
					$modifier_id1=$getProcedureDetailsRows['modifier_id1'];
					$modifier_id2=$getProcedureDetailsRows['modifier_id2'];
					$modifier_id3=$getProcedureDetailsRows['modifier_id3'];
					$modifier_id4=$getProcedureDetailsRows['modifier_id4'];
					$approval1=$getProcedureDetailsRows['approval1'];
					$approval1_date1=$getProcedureDetailsRows['approval1_date'];
					$notes=$getProcedureDetailsRows['notes'];
					$front_acc_comment=$getProcedureDetailsRows['front_acc_comment'];
					$acc_comment=$getProcedureDetailsRows['acc_comment'];
					$rev_codeid=$getProcedureDetailsRows['rev_code'];
					$proc_codeid=$getProcedureDetailsRows['proc_code_essi'];
					$proc_selfpay=$getProcedureDetailsRows['proc_selfpay'];
					$display_order=$getProcedureDetailsRows['display_order'];
					$chld_del_status=$getProcedureDetailsRows['del_status'];
					$write_off_by = $getProcedureDetailsRows['write_off_by'];
					$rev_code_final=$proc_code_final="";
					if($rev_data_arr[$rev_codeid]['r_code']!=""){
						$rev_code_final = $rev_data_arr[$rev_codeid]['r_code'];
					}
					if($proc_data_arr[$proc_codeid]['proc_code']!=""){
						$proc_code_final = $proc_data_arr[$proc_codeid]['proc_code'];
					}	
			
					if($paidStatus=='Paid' && $totalFee<>$newBalanceToAlter){
						$alter = "";
					}else{
						$alter = "";
					}
					
					$approval1_date = get_date_format($approval1_date1);
					if($approval1_date=="00-00-0000"){
						$approval1_date="";
					}
					
					$approval2=$getProcedureDetailsRows['approval2'];
					$approval2_date2=$getProcedureDetailsRows['approval2_date'];
					
					$approval2_date = get_date_format($approval2_date2);
					if($approval2_date=="00-00-0000"){
						$approval2_date="";
					}
					
					$onset_type=$getProcedureDetailsRows['onset_type'];
					$onset_date1=$getProcedureDetailsRows['onset_date'];
					
					$onset_dates = get_date_format($onset_date1);
					if($onset_dates=="00-00-0000"){
						$onset_dates="";
					}
					
					$rev_rate=$getProcedureDetailsRows['rev_rate'];
					
					// Deposit amount
					$totaldepositAmt = array_sum($payment_claim_chld_data[$charge_list_detail_id]['Deposit']);
					if($totaldepositAmt>0){
						$totaldepositAmt=number_format($totaldepositAmt,2);
					}else{
						$totaldepositAmt='0.00';
					}
					// Deposit amount
					
					$cpt_prac_code = $cpt_fee_data[$procId]['cpt_prac_code'];
					$cpt_desc = addslashes($cpt_fee_data[$procId]['cpt_desc']);
					$modPracCode1=$mod_code_arr[$modifier_id1];
					$modPracCode2=$mod_code_arr[$modifier_id2];
					$modPracCode3=$mod_code_arr[$modifier_id3];
					$modPracCode4=$mod_code_arr[$modifier_id4];
					
				$pro_cont++;
				$show_copay="";
				$copay_collect_chk=copay_apply_chk($cpt_prac_code,'','');
				
				if($copay_collect_chk[0]==true && $copay_collect_chk[1]==true){
					$show_copay=$copay;
				}else{
					$show_copay='0.00';
				}
				$horg_pos1=20;
				$vert_pos1=22*$pro_cont;
				$menu_pos1=25*$pro_cont;
				$div_hg1=476-$menu_pos1;
				$bgcolor = $pro_cont%2 == 0 ? '#F4F9EE' : '#FFFFFF';
				$del_wrt="1";
				$auto_wrt_amt=$getProcedureDetailsRows['totalAmount']-$getProcedureDetailsRows['approvedAmt'];
				if($getProcedureDetailsRows['totalAmount']>$getProcedureDetailsRows['approvedAmt']){
					$del_wrt="";
				}
				auto_writeoff_tran($patient_id,$charge_list_detail_id,$auto_wrt_amt,$write_off_by,$del_wrt);
			?>
				<tr id="<?php echo $pro_cont; ?>" class="text-center">
                	<td><span class="glyphicon glyphicon-remove pointer" alt="Delete Row" onclick="del_chld('<?php echo $pro_cont; ?>');"></span></td>
                    <td>
                    	<input type="hidden" name="cpt_tax_<?php echo $pro_cont; ?>" id="cpt_tax_<?php echo $pro_cont; ?>" value="<?php echo $cpt_tax_data[$procId]; ?>">
                        <input type="hidden" name="display_order_<?php echo $pro_cont; ?>" id="display_order_<?php echo $pro_cont; ?>" class="display_order_cls" value="<?php echo $display_order; ?>">
						<input type="hidden" name="chld_id_<?php echo $pro_cont; ?>" id="chld_id_<?php echo $pro_cont; ?>" value="<?php echo $charge_list_detail_id; ?>">
						<?php
							if($chld_del_status==0){
								$proc_chk_box="";
								if(count($chkbox_for_post_arr)>0 && $post_chrg_chk!=""){
									if(in_array($charge_list_detail_id,$chkbox_for_post_arr)){
										$proc_chk_box="checked";
									}
								}else{
									$proc_chk_box="checked";
								}
								if($proc_chk_box!=""){
									echo "<script type='text/javascript'>document.getElementById('chkbx_all').checked=true</script>";
								}
						?>
							<div class="checkbox">
								<input type="checkbox" name="chkbx_<?php echo $pro_cont; ?>" id="chkbx_<?php echo $pro_cont; ?>" value="<?php echo $charge_list_detail_id; ?>" class="chk_box_css" <?php echo $proc_chk_box; ?>>	
								<label for="chkbx_<?php echo $pro_cont; ?>"></label>
							</div>
						<?php
							}
						?>
                        <?php	
							if($newBalanceToAlter != $totalFee || $paidForProc>0 ||  $overPaymentForProc>0){
						?>
							<input type="hidden" name="chkbx_del_<?php echo $pro_cont; ?>" id="chkbx_del_<?php echo $pro_cont; ?>" value="no">
						<?php 	
							}
							$proc_self_disable=0;
							$tot_paid_chk3=array_sum($payment_type_chld_data[$charge_list_detail_id]['Insurance']);
							if($tot_paid_chk3>0){
								$proc_self_disable='1';
							}
						?>
					</td>
					<td>
						<div class="checkbox">
							<input type="checkbox" name="proc_selfpay_<?php echo $pro_cont; ?>" id="proc_selfpay_<?php echo $pro_cont; ?>" value="<?php echo $charge_list_detail_id; ?>" class="proc_selfpay"  <?php if($proc_selfpay=='1'){echo "checked";} if($proc_self_disable=='1'){echo"disabled";} ?> onClick="show_div('<?php echo $pro_cont; ?>');">
							<label for="proc_selfpay_<?php echo $pro_cont; ?>"></label>
						</div>
						<input type="hidden" name="proc_selfpay_chld_<?php echo $pro_cont; ?>" id="proc_selfpay_chld_<?php echo $pro_cont; ?>" value="<?php echo $charge_list_detail_id; ?>">
                    </td>
					<td>
                      <input type="text" name="procedureText_<?php echo $pro_cont; ?>" id="procedureText_<?php echo $pro_cont; ?>" data-sort="contain" value="<?php echo $cpt_prac_code; ?>" class="form-control" style="width:110px;" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="rev_ajaxfunction('<?php echo $pro_cont; ?>'); hidecopayprice(this.value,'<?php echo $pro_cont; ?>'); show_div('<?php echo $pro_cont; ?>');set_rev_rate('<?php echo $pro_cont; ?>');" onChange="ajaxFunction1('<?php echo $pro_cont; ?>','<?php echo $chg_proc_amt_chk; ?>');set_frm_post();tos_ajaxfunction('<?php echo $pro_cont; ?>');chk_visit_code();set_rev_rate('<?php echo $pro_cont; ?>');set_auth_drop('<?php echo $encounter_id; ?>');" <?php echo show_tooltip($cpt_desc); ?>>
                    </td>
					<td id="rev_code_td_<?php echo $pro_cont; ?>">
						<input type="text" name="revcode_<?php echo $pro_cont; ?>" id="revcode_<?php echo $pro_cont; ?>" value="<?php echo $rev_code_final; ?>" class="form-control" onkeypress="{if (event.keyCode==13)return focus_div(this);}"  onBlur="show_div('<?php echo $pro_cont; ?>');" onChange="set_frm_post();">
					</td>
					<td id="proc_code_td_<?php echo $pro_cont; ?>">
						<input type="text" name="proccode_<?php echo $pro_cont; ?>" id="proccode_<?php echo $pro_cont; ?>" value="<?php echo $proc_code_final; ?>" class="form-control" onkeypress="{if (event.keyCode==13)return focus_div(this);}"  onBlur="show_div('<?php echo $pro_cont; ?>');" onChange="set_frm_post();">
					</td>
                    <?php 
					$dx_desc_arr=array();
					$dx_desc="";
					for($f=1;$f<=12;$f++){
						$dx_val=$getProcedureDetailsRows['diagnosis_id'.$f];
						if($enc_icd10>0){
							$dx_val2=$dx_val;
							if(strlen($dx_val)>6){
								$dx_val2=substr($dx_val,0,6);
							}
							if($dx_val!="" && $icd10_desc_arr[$dx_val2]!=""){
								$dx_desc_arr[$dx_val]=$dx_val.' - '.$icd10_desc_arr[$dx_val2];
							}else{
								if($dx_val!="" && $icd10_desc_arr[$dx_val]!=""){
									$dx_desc_arr[$dx_val]=$dx_val.' - '.$icd10_desc_arr[$dx_val];
								}
							}
						}else{
							if($dx_val!="" && $arrDxCodechld[$dx_val]!=""){
								$dx_desc_arr[$dx_val]=$dx_val.' - '.$arrDxCodechld[$dx_val];
							}
						}
					} 
					$dx_desc=addslashes(implode($dx_desc_arr,'<br>'));
				   ?>
					<td class="text-left text-nowrap">
                    	  <span <?php echo show_tooltip($dx_desc); ?>>
                        	<input type="hidden" name="app_proc_dx_code_<?php echo $pro_cont; ?>" id="app_proc_dx_code_<?php echo $pro_cont; ?>" value="<?php echo str_replace(',','~~~',$adm_dx_codes_arr[$procId]); ?>">
                             <select name="diagText_all_<?php echo $pro_cont; ?>[]" id="diagText_all_<?php echo $pro_cont; ?>" class="diagText_all_css selectpicker" data-title="Select Dx Codes" data-actions-box="true" data-container="#dxdrop" multiple="multiple" onChange="show_div('<?php echo $pro_cont; ?>'); set_frm_post(); chk_adm_dx('<?php echo $pro_cont; ?>'); ">
                               <?php for($f=1;$f<=12;$f++){
                                    $dx_val=$getProcedureDetailsRows['diagnosis_id'.$f];
                                    if($dx_val!=""){
                                        $dx_send_val=$dx_val.'**'.$f;
                                        $dx_sel="selected";
                                 ?>
                                    <option value="<?php echo $dx_send_val; ?>" <?php echo $dx_sel; ?>><?php echo $dx_val; ?></option>
                               <?php }} ?>
                              </select>
                          </span>
						  <span>
						 	 <?php if($pro_cont==1){?>
								<a title="Copy Dx Codes" class="glyphicon glyphicon-copy" style="color: green;" onclick="sb_copy_dx_codes();" href="javascript:void(0);"></a>
							 <?php } ?>
						  </span>
                    </td>
					<td>
						<input type="text" name="mod1Text_<?php echo $pro_cont; ?>" id="mod1Text_<?php echo $pro_cont; ?>" value="<?php echo $modPracCode1; ?>" class="form-control" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('<?php echo $pro_cont; ?>'); getmodeDescJs(this);" onChange="set_frm_post();" <?php echo show_tooltip($mod_desc_proc[$modPracCode1]); ?>>
					</td>
					<td>
						<input type="text" name="mod2Text_<?php echo $pro_cont; ?>" id="mod2Text_<?php echo $pro_cont; ?>" value="<?php echo $modPracCode2; ?>" class="form-control" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('<?php echo $pro_cont; ?>'); getmodeDescJs(this);" onChange="set_frm_post();" <?php echo show_tooltip($mod_desc_proc[$modPracCode2]); ?>>
					</td>
					<td>
						<input type="text" name="mod3Text_<?php echo $pro_cont; ?>" id="mod3Text_<?php echo $pro_cont; ?>" value="<?php echo $modPracCode3; ?>" class="form-control" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('<?php echo $pro_cont; ?>'); getmodeDescJs(this);" onChange="set_frm_post();" <?php echo show_tooltip($mod_desc_proc[$modPracCode3]); ?>>
					</td>
                    <td>
						<input type="text" name="mod4Text_<?php echo $pro_cont; ?>" id="mod4Text_<?php echo $pro_cont; ?>" value="<?php echo $modPracCode4; ?>" class="form-control" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('<?php echo $pro_cont; ?>'); getmodeDescJs(this);" onChange="set_frm_post();" <?php echo show_tooltip($mod_desc_proc[$modPracCode4]); ?>>
					</td>
                    <?php if($acc_edit_financials<=0){$charg_read_only="readonly"; $charg_read_only_alrt="You do not have permission to perform this action."; }?>
					<td <?php echo show_tooltip($charg_read_only_alrt); ?>>
                   		<input type="hidden" name="proc_copay_<?php echo $pro_cont; ?>" id="proc_copay_<?php echo $pro_cont; ?>" value="<?php echo $show_copay; ?>" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('<?php echo $pro_cont; ?>');">				
						<input <?php echo $charg_read_only; ?> type="text" name="units_<?php echo $pro_cont; ?>" id="units_<?php echo $pro_cont; ?>" <?php echo $alter; ?> value="<?php if(!$units) echo "1"; else echo unit_format($units); ?>" class="form-control" onChange="set_frm_post(); return totalAmt('<?php echo $pro_cont; ?>');" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('<?php echo $pro_cont; ?>');">				
					</td>
					<td <?php echo show_tooltip($charg_read_only_alrt); ?>>
						<div class="input-group">
							<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
							<?php 
							if($totalFee>$approvedAmt_chk){
							?>
								<input type="hidden" name="chk_old_app_amt_<?php echo $pro_cont; ?>" id="chk_old_app_amt_<?php echo $pro_cont; ?>" value="<?php echo $approvedAmt_chk; ?>">
							<?php } ?>
							<?php $procCharges=str_replace(',','',number_format($procCharges,2)); ?>
								<input <?php echo $charg_read_only; ?> type="text" name="charges_<?php echo $pro_cont; ?>" id="charges_<?php echo $pro_cont; ?>" <?php echo $alter; ?> value="<?php echo $procCharges; ?>" class="form-control" style="width:80px;" onChange="set_frm_post(); return totalAmt('<?php echo $pro_cont; ?>');" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('<?php echo $pro_cont; ?>');">				
								<input type="hidden" name="netAmt_old_<?php echo $pro_cont; ?>" id="netAmt_old_<?php echo $pro_cont; ?>" value="<?php echo $totalFee; ?>">
							<?php 
							$tot_adj_amt_chk=0;
							$tot_adj_amt_chk = array_sum($crd_chld_data_arr[$charge_list_detail_id]);
							if($tot_adj_amt_chk>0){
							?>
								<input type="hidden" name="chk_de_cr_<?php echo $pro_cont; ?>" id="chk_de_cr_<?php echo $pro_cont; ?>" value="<?php echo $cpt_prac_code; ?>">
							<?php 
							}
							?>
						</div>
					</td>
					
					<td id="netChargesTd_<?php echo $pro_cont; ?>" <?php echo show_tooltip($charg_read_only_alrt); ?>>
						<div class="input-group">
							<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
							<input <?php echo $charg_read_only; ?> type="text" name="netAmt_<?php echo $pro_cont; ?>" id="netAmt_<?php echo $pro_cont; ?>" <?php echo $alter; ?> value="<?php if($totalFee) echo $totalFee; else echo "0.00"; ?>" class="form-control" style="width:80px;" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="return totalAmt('<?php echo $pro_cont; ?>'); show_div('<?php echo $pro_cont; ?>');" onChange="set_frm_post();">
						</div>
					</td>
					<td>
						<input type="text" name="app1_<?php echo $pro_cont; ?>" id="app1_<?php echo $pro_cont; ?>" value="<?php echo $approval1; ?>" class="form-control" style="width:80px;" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('<?php echo $pro_cont; ?>');" onChange="set_frm_post();">				
					</td>
					<td>
						<div class="input-group">
							<input type="text" name="app1_date_<?php echo $pro_cont; ?>" id="date_<?php echo $pro_cont; ?>" value="<?php echo $approval1_date; ?>" class="form-control date-pick" style="width:90px;" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="checkdate(this); show_div('<?php echo $pro_cont; ?>');" onChange="set_frm_post();">
							<label class="input-group-addon pointer" for="date_<?php echo $pro_cont; ?>"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
						</div>
					</td>
					<td>
						<input type="text" name="app2_<?php echo $pro_cont; ?>" id="app2_<?php echo $pro_cont; ?>" value="<?php echo $approval2; ?>" class="form-control" style="width:80px;" onBlur="show_div('<?php echo $pro_cont; ?>');" onChange="set_frm_post();">				
					</td>
					<td>
						<div class="input-group">
							<input type="text" name="app2_date_<?php echo $pro_cont; ?>" id="app2_date_<?php echo $pro_cont; ?>" value="<?php echo $approval2_date; ?>" class="form-control date-pick" style="width:90px;" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="checkdate(this); show_div('<?php echo $pro_cont; ?>');" onChange="set_frm_post();">
							<label class="input-group-addon pointer" for="app2_date_<?php echo $pro_cont; ?>"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
						</div>
					</td>
					<td>
						<input type="text" name="rev_rate_<?php echo $pro_cont; ?>" id="rev_rate_<?php echo $pro_cont; ?>" value="<?php echo $rev_rate; ?>" class="form-control" style="width:70px;" onBlur="show_div('<?php echo $pro_cont; ?>');" onChange="set_frm_post();">				
					</td>
					<td>
						<textarea name="notes_<?php echo $pro_cont; ?>" id="notes_<?php echo $pro_cont; ?>" class="form-control" rows="1" style="width:180px;" onClick="return emptyNotes(this);" onBlur="show_div('<?php echo $pro_cont; ?>');" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onChange="set_frm_post();"><?php if(!$notes) echo ""; else echo $notes; ?></textarea>
					</td>
				</tr>
			<?php	
				}
			}
		?>
        <tr>
        	<td colspan="26" style="height:0px; padding:0px;">
            	<input type="hidden" id="proc_copay_chk" name="proc_copay_chk" value="<?php echo $proc_copay_chk; ?>" onChange="set_frm_post();">
				<input type="hidden" id="old_rec" name="old_rec" value="<?php echo $pro_cont;?>" onChange="set_frm_post();">
                <input type="hidden" id="last_cnt" name="last_cnt" value="<?php echo $pro_cont;?>" onChange="set_frm_post();">
            </td>
        </tr>
        </tbody>
	</table>
</div>
</form>	
<script type="text/javascript">
	$(document).ready(function(){
		tot_charges();addNewRow('');anes_fun(<?php echo $billing_type; ?>);show_rev_div();
		$(".dx_box_12").blur(crt_dx_dropdown);
		$( "#sortable" ).sortable();
		$( "#sortable" ).disableSelection();
		set_auth_drop('show_only_drop');
	});
</script>
<script type="text/javascript">
	var sb_multi_vst_cd_noalert = "<?php echo $GLOBALS['MULTIPLE_VISIT_CODES_NOALERT'];?>";
	var last_cnt=$("#last_cnt").val();
	for(var j=1;j<=last_cnt;j++){
		var obj7 = $('#procedureText_'+j).typeahead({source:customarrayProcedure,scrollBar:true});
		var obj8 = $('#mod1Text_'+j).typeahead({source:customarrayModifiers});
		var obj9 = $('#mod2Text_'+j).typeahead({source:customarrayModifiers});
		var obj10 = $('#mod3Text_'+j).typeahead({source:customarrayModifiers});
		var obj11 = $('#mod4Text_'+j).typeahead({source:customarrayModifiers});
		if(document.getElementById('revcode_'+j)){
			var obj19 =$('#revcode_'+j).typeahead({source:customarrayrev});
		}
		if(document.getElementById('revcode_'+j)){
			var obj20 = $('#proccode_'+j).typeahead({source:customarrayproc});
		}
	}
	set_dx_typeahead('');
	set_auto_state();
	var obj4 = $('#primary_provider_id').typeahead({source:customarrayProvider});
	var obj5 = $('#secondary_provider_id').typeahead({source:customarrayProvider});
	var obj16 = $('#tosText_id').typeahead({source:customarrayTos});
	var obj17 = $('#posText').typeahead({source:customarrayPos});
</script>
<?php
	if($letter_sent_date!="0000-00-00" && $encounter_id>0 && $letter_sent_date!="" && $letter_sent_date!="00-00-00"){
		list($y,$m,$d)=explode('-',$letter_sent_date);
		$coll_date=$m.'-'.$d.'-'.substr($y,2);
?>
	<script type="text/javascript">
		$('#coll_date').html('<?php echo "Collection Date : ".$coll_date; ?>');
	</script>
<?php		
	}
	if($_REQUEST['del_charge_list_id']>0){
?>
<script type="text/javascript">
	top.btn_show();
</script>
<?php }else{?>
<script type="text/javascript">
var server_name='<?php echo $billing_global_server_name;?>';

	var ar = new Array();
	ar[0] = new Array("balance","Balance View","top.fmain.OpenBalWin('<?php echo $encounter_id; ?>');");
	ar[1] = new Array("only_save","Done","top.fmain.check('new', '');");
	ar[2] = new Array("save","Done & <?php print $postButton; ?>","top.fmain.check('new','yes');");
	ar[3] = new Array("payment","Payment","top.fmain.OpenPaymentWin();");
	ar[4] = new Array("delete","Void","top.fmain.del_chld();");
	ar[5] = new Array("close","Close","top.fmain.reload_frm('AccountingEC');");
	if(server_name=='cfei'){
		ar[6] = new Array("asc_state","ASC State","top.fmain.OpenAscStateWin();");
	}
	
	top.btn_show("ACCOUNT",ar);
</script>
<?php }
	if($copayReflect == 'false'){
?>
	<script type="text/jscript">
		top.fAlert('CoPay Amount is changed from insurance.<br>CoPay Amount has already been paid for encounter.<br>To reflect CoPay for the encounter please delete copay payment transaction first.')
	</script>
<?php
	}
	if($_REQUEST['post_chrg_chk']=='yes'){
		echo "<script>postCharges();</script>";
	}
	if($alrt_ins_status==1){
		echo "<script>top.fAlert('Insurance has changed, please save encounter.');</script>";
	}
	if($encounter_id<=0){
		if($ins_icd_code=="ICD-10"){
			echo "<script>icd10_fun();</script>";
		}else if($policies_icd_code=="ICD-10" && $ins_icd_code!="ICD-9"){
			echo "<script>icd10_fun();</script>";
		}
	}else{
		$acc_enc="yes";
	}
	
?>
<?php require_once("acc_footer.php");?>	
