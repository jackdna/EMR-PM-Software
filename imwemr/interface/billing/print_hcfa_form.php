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

include_once(dirname(__FILE__)."/../../config/globals.php");
//include_once(dirname(__FILE__)."/../../library/html_to_pdf/fpdi/fpdi.php");
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php");
include_once(dirname(__FILE__)."/../../library/classes/billing_functions.php");
use setasign\Fpdi\Tcpdf\Fpdi;
$pdf = new Fpdi();
$fpdiCheck = false;
$secondryFlag = false;
$global_date_format = phpDateFormat();

$hide_row[]="24j";
$operator_id=$_SESSION['authId'];
$top_marg=0;
$time_hcfa=time();

// get all file names
$old_hcfa_files = glob(data_path()."UserId_".$operator_id."/tmp/hcfa_form_*");
// loop through files
foreach($old_hcfa_files as $old_hcfa_file_name){
  if(is_file($old_hcfa_file_name) && strpos($old_hcfa_file_name,'hcfa_form_') !== false) {
    // delete file
    @unlink($old_hcfa_file_name);
  }
}

$newfile_hcfa_path=write_html('','hcfa_form_'.$time_hcfa.'.pdf');
if(isset($_REQUEST['chl_chk_box'])){
	if(count($_REQUEST['chl_chk_box'])>0 && $day_charges_chk==""){
		$validChargeListId=$_REQUEST['chl_chk_box'];
	}
}

if($InsComp=="1"){
	$chk_insurance="primarySubmit";
	$insCheck = 'primary';
}else if($InsComp=="2"){
	$chk_insurance="secondarySubmit";
	$insCheck = 'secondary';
}else if($InsComp=="3"){
	$chk_insurance="tertiarySubmit";
	$insCheck = 'tertiary';
}

/*Azar 24J 33B Medicaid Payer IDs*/
$arr_24j_33b_payers = array('SKLA0','27514','54763','27357','88075','56190','128LA','66001');

$eAndMCptArray = array(99201, 99202, 99203, 99204, 99205, 99211, 99212,99213, 99214, 99215, 99242, 99243, 99244, 99245);

$associatedeye_25_box_arr = array("41"=>"32458181000","42"=>"29444408600","43"=>"28692102700","44"=>"30150877400","45"=>"28572301100","46"=>"38578158900","52"=>"50598769100");
if(count($validChargeListId) > 0){
	
	//------------------------ Encounter Detail ------------------------//
	$chl_id_imp=implode(',',$validChargeListId);
	$qry = imw_query("select * from patient_charge_list where del_status='0' and charge_list_id in ($chl_id_imp)");
	while($row=imw_fetch_object($qry)){
		$chl_arr[$row->charge_list_id]=$row;
		$chl_ins_arr[$row->primaryInsuranceCoId]=$row->primaryInsuranceCoId;
		$chl_ins_arr[$row->secondaryInsuranceCoId]=$row->secondaryInsuranceCoId;
		$chl_ins_arr[$row->tertiaryInsuranceCoId]=$row->tertiaryInsuranceCoId;
		
		$chl_usr_arr[$row->primaryProviderId]=$row->primaryProviderId;
		$chl_usr_arr[$row->secondaryProviderId]=$row->secondaryProviderId;
		$chl_usr_arr[$row->tertiaryProviderId]=$row->tertiaryProviderId;
		
		$chl_pat_arr[$row->patient_id]=$row->patient_id;
		$chl_gro_id_arr[$row->gro_id]=$row->gro_id;
		$chl_enc_arr[$row->encounter_id]=$row->encounter_id;
		$chl_case_id_arr[$row->case_type_id]=$row->case_type_id;
	}
	//------------------------ Encounter Detail ------------------------//
	
	//------------------------ Encounter Procedures Detail ------------------------//
	if($chld_ids!=""){
		$chld_whr=" and patient_charge_list_details.charge_list_detail_id in($chld_ids)";
	}else{
		if($newFile!=""){
			$chld_whr=" and patient_charge_list_details.posted_status='1'";
		}else{
			$chld_whr=" and patient_charge_list_details.posted_status='1' and patient_charge_list_details.claim_status='0'";
		}
	}
	$qry = imw_query("select patient_charge_list_details.*,cpt_fee_tbl.cpt4_code from patient_charge_list_details 
			join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
			where patient_charge_list_details.del_status='0' and patient_charge_list_details.charge_list_id in ($chl_id_imp) 
			and patient_charge_list_details.proc_selfpay!='1' and cpt_fee_tbl.not_covered = '0' $chld_whr  
			order by patient_charge_list_details.display_order,patient_charge_list_details.charge_list_detail_id");
	while($row=imw_fetch_array($qry)){
		$chld_arr[$row['charge_list_id']][$row['charge_list_detail_id']]=$row;
		$chld_procCode_arr[$row['procCode']]=$row['procCode'];
		$chld_rev_code_arr[$row['rev_code']]=$row['rev_code'];
		$chld_proc_code_essi_arr[$row['proc_code_essi']]=$row['proc_code_essi'];
		$chld_place_of_service_arr[$row['place_of_service']]=$row['place_of_service'];
		$chld_type_of_service_arr[$row['type_of_service']]=$row['type_of_service'];
		$chld_modifier_id_arr[$row['modifier_id1']]=$row['modifier_id1'];
		$chld_modifier_id_arr[$row['modifier_id2']]=$row['modifier_id2'];
		$chld_modifier_id_arr[$row['modifier_id3']]=$row['modifier_id3'];
		$chld_modifier_id_arr[$row['modifier_id4']]=$row['modifier_id4'];
		
		for($f=1;$f<=12;$f++){
			if($row['diagnosis_id'.$f]!=''){
				$chld_diagnosis_id_arr[$row['diagnosis_id'.$f]]=$row['diagnosis_id'.$f];
			}
		}
	}
	//------------------------ Encounter Procedures Detail ------------------------//
	
	//------------------------ Insurance Payments Detail ------------------------//
	$ins_paid_arr=array();
	$chl_enc_imp=implode(',',$chl_enc_arr);
	if(in_array(strtolower($billing_global_server_name), array('bennett','seaside','nco','clarityeye','creekside','chamg'))){
		$pay_qry = imw_query("select patient_chargesheet_payment_info.encounter_id,patient_charges_detail_payment_info.paidForProc,patient_charges_detail_payment_info.overPayment
						from patient_charges_detail_payment_info
						join patient_chargesheet_payment_info 
						on patient_chargesheet_payment_info.payment_id = patient_charges_detail_payment_info.payment_id
						where patient_chargesheet_payment_info.insProviderId >0
						and patient_charges_detail_payment_info.paidBy = 'Insurance'
						and patient_chargesheet_payment_info.encounter_id in($chl_enc_imp)							
						and patient_charges_detail_payment_info.deletePayment = '0'
						and patient_chargesheet_payment_info.paymentClaims!='Negative Payment'
						and patient_charges_detail_payment_info.charge_list_detail_id>0");
		while($paid_res = imw_fetch_array($pay_qry)){
			$ins_paid_arr[$paid_res['encounter_id']][] = $paid_res['paidForProc']+$paid_res['overPayment'];
		}
	}
	
	//------------------------ Patient Detail ------------------------//
	$chl_pat_imp=implode(',',$chl_pat_arr);
	$sql = imw_query("select * from patient_data WHERE id in($chl_pat_imp)");
	while($row=imw_fetch_object($sql)){			
		$pat_data[$row->id]=$row;
	}		
	//------------------------	Patient Detail	------------------------//
	
	//------------------------ Group Detail ------------------------//
	$chl_gro_id_imp=implode(',',$chl_gro_id_arr);
	$sql = imw_query("select * from groups_new WHERE gro_id in($chl_gro_id_imp) or group_institution='1'");
	while($row=imw_fetch_array($sql)){			
		$group_data[$row['gro_id']]=$row;
		if($row['group_institution']=='1' and $row['del_status']=='0'){
			$sc_npi=$row['group_NPI'];
			$sc_nam=$row['name'];
			$chk_institution=$row['gro_id'];
		}
	}		
	//------------------------	Group Detail	------------------------//
	
	//------------------------ Insurance Company Detail------------------------//
	$chl_ins_imp=implode(',',$chl_ins_arr);
	$qry = imw_query("select * from insurance_companies where id in($chl_ins_imp)");
	while($row = imw_fetch_object($qry)){
		$ins_comp_data[$row->id]=$row;
	}
	
	$qry = imw_query("select * from insurance_data where provider in($chl_ins_imp) and provider>0 and pid in($chl_pat_imp)");
	while($row = imw_fetch_object($qry)){
		$ins_case_data_arr[$row->ins_caseid][]=$row;
	}
	
	$chl_case_id_imp=implode(',',$chl_case_id_arr);
	$qry = imw_query("SELECT a.ins_caseid,b.case_name FROM insurance_case a join insurance_case_types b on a.ins_case_type=b.case_id where a.ins_caseid in($chl_case_id_imp)");
	while($row = imw_fetch_array($qry)){
		$ins_case_name_arr[$row['ins_caseid']]= $row['case_name'];
	}
	//------------------------ Insurance Company Detail ------------------------//
	
	//------------------------ User Detail ------------------------//
	$chl_usr_imp=implode(',',$chl_usr_arr);
	$sql = imw_query("select * from users WHERE id in($chl_usr_imp)");
	while($row=imw_fetch_object($sql)){			
		$usr_data[$row->id]=$row;
	}		
	//------------------------	User Detail	------------------------//
	
	//------------------------ Diagnosis Code Detail------------------------//
	$chld_diagnosis_id_imp="'".implode("','",$chld_diagnosis_id_arr)."'";
	$qry = imw_query("select * from diagnosis_code_tbl where dx_code in($chld_diagnosis_id_imp)");
	while($row = imw_fetch_object($qry)){
		$dx_desc_data[$row->dx_code]=$row;
	}
	//------------------------ Diagnosis Code Detail------------------------//
	
	//------------------------ Modifier Code Detail ------------------------//
	if(count($chld_modifier_id_arr)>0){
		$chld_modifier_id_imp=implode(',',$chld_modifier_id_arr);
		$sql = imw_query("select * from modifiers_tbl WHERE modifiers_id in($chld_modifier_id_imp)");
		while($row=imw_fetch_object($sql)){			
			$modifier_data[$row->modifiers_id]=$row;
		}
	}
	//------------------------ Modifier Code Detail	------------------------//
	
	//------------------------ Facility Detail ------------------------//
	$selQry = "select * from facility where facility_type  = '1'";
	$res = imw_query($selQry);
	while($row = imw_fetch_object($res)){
		$fac_data_arr[]=$row;
	}
	//------------------------ Facility Detail ------------------------//

	//------------------------ POS Detail ------------------------//
	$chld_place_of_service_imp=implode(',',$chld_place_of_service_arr);
	$selQry = "select pos_id,pos_code from pos_tbl where pos_id in($chld_place_of_service_imp)";
	$res = imw_query($selQry);
	while($row = imw_fetch_object($res)){
		$pos_data_arr[$row->pos_id]=$row;
	}
	//------------------------ POS Detail ------------------------//
	
	//------------------------ TOS Detail ------------------------//
	$chld_type_of_service_imp=implode(',',$chld_type_of_service_arr);
	$selQry = "select tos_id,tos_code from tos_tbl where tos_id in($chld_type_of_service_imp)";
	$res = imw_query($selQry);
	while($row = imw_fetch_object($res)){
		$tos_data_arr[$row->tos_id]=$row;
	}
	//------------------------ TOS Detail ------------------------//
	
	//------------------------ POS Facility  Detail ------------------------//
	$selQry = "select * from pos_facilityies_tbl order by facilityPracCode";
	$res = imw_query($selQry);
	while($row = imw_fetch_object($res)){
		$pos_fac_data_arr[$row->pos_facility_id]=$row;
	}
	//------------------------ POS Facility Detail ------------------------//
	
	//------------------------ HCFA Margin Detail ------------------------//
	$group_margin_qry=imw_query("select top_margin,left_margin,top_line_margin from create_margins where margin_type='HCFA'");
	$group_margin=imw_fetch_array($group_margin_qry);
	//------------------------ HCFA Margin Detail ------------------------//
	
	foreach($validChargeListId as $val){
		if($val != ""){
			
			$patientListData = $chl_arr[$val];
			
			if(empty($gro_id) == true){
				$gro_id_chl = $patientListData->gro_id;				
			}else{
				$gro_id_chl=$gro_id;
			}
			$enc_icd10_db=$patientListData->enc_icd10;
			$enc_icd10=1;
			if($enc_icd10_db>0){
				$enc_icd10_ind=0;
			}else{
				$enc_icd10_ind=9;
			}
			
			$groupDetails = $group_data[$gro_id_chl];
			
			if($groupDetails['rem_address1']!="" && $groupDetails['rem_zip']!="" && $groupDetails['rem_state']!=""){
				$groupDetails['group_Address1']=$groupDetails['rem_address1'];
				$groupDetails['group_Zip']=$groupDetails['rem_zip'];
				$groupDetails['zip_ext']=$groupDetails['rem_zip_ext'];
				$groupDetails['group_State']=$groupDetails['rem_state'];
				$groupDetails['group_City']=$groupDetails['rem_city'];
			}
			
			$objInsuranceCoData = $claim_ctrl_no = $claim_ctrl_up = '';
			if($chk_insurance == 'primarySubmit'){
				if($patientListData->primaryInsuranceCoId){
					$objpriInsuranceCoData_rmark = $ins_comp_data[$patientListData->primaryInsuranceCoId];
					$Ins_company_id_chk=$patientListData->primaryInsuranceCoId;
					$objInsuranceCoData = $ins_comp_data[$patientListData->primaryInsuranceCoId];
				}
				$claim_ctrl_no = $claim_ctrl[$val];
				$claim_ctrl_up=" claim_ctrl_pri='".$claim_ctrl_no."'";
			}else if($chk_insurance == 'secondarySubmit'){
				if($patientListData->secondaryInsuranceCoId){
					$objpriInsuranceCoData_rmark = $ins_comp_data[$patientListData->secondaryInsuranceCoId];
					$Ins_company_id_chk=$patientListData->secondaryInsuranceCoId;
					$objInsuranceCoData = $ins_comp_data[$patientListData->secondaryInsuranceCoId];
					$objSecInsuranceCoData = $ins_comp_data[$patientListData->secondaryInsuranceCoId];
				}
				$claim_ctrl_no = $claim_ctrl[$val];
				$claim_ctrl_up=" claim_ctrl_sec='".$claim_ctrl_no."'";
			}else if($chk_insurance == 'tertiarySubmit'){
				if($patientListData->tertiaryInsuranceCoId){
					$objpriInsuranceCoData_rmark = $ins_comp_data[$patientListData->tertiaryInsuranceCoId];
					$Ins_company_id_chk=$patientListData->tertiaryInsuranceCoId;
					$objInsuranceCoData = $ins_comp_data[$patientListData->tertiaryInsuranceCoId];
					$objSecInsuranceCoData = $ins_comp_data[$patientListData->tertiaryInsuranceCoId];
				}
				$claim_ctrl_no = $claim_ctrl[$val];
				$claim_ctrl_up=" claim_ctrl_ter='".$claim_ctrl_no."'";
			}

			if($claim_ctrl_up!="" && $newFile==""){
				imw_query("update patient_charge_list set $claim_ctrl_up where charge_list_id='$val'");
			}else{
				if($chk_insurance == 'primarySubmit'){
					$claim_ctrl_no=$patientListData->claim_ctrl_pri;
				}else if($chk_insurance == 'secondarySubmit'){
					$claim_ctrl_no=$patientListData->claim_ctrl_sec;
				}else if($chk_insurance == 'tertiarySubmit'){
					$claim_ctrl_no=$patientListData->claim_ctrl_ter;
				}
			}
			
			$chk_work_val="";
			$chk_auto_val="";
			$chk_auto_st="";
			if($patientListData->case_type_id > 0){
				$case_name=$ins_case_name_arr[$patientListData->case_type_id];
				if(strtolower($case_name)=="workman comp"){
					$chk_work_val="yes";
				}
				if(strtolower($case_name)=="auto"){
					$chk_auto_val="yes";
					$chk_auto_st=$patientListData->auto_state;
				}
			}
	
			//--- GET PATIENT DETAILS AND RESPONSIBLE PARTY DETAILS AND INSURANCE GROUP NUMBER ----	
			$patientDetails = '';
			$objInsGroupNumber = '';
			if($patientListData->patient_id){
				$patientDetails = $pat_data[$patientListData->patient_id];
				if($patientListData->case_type_id){
					foreach($ins_case_data_arr[$patientListData->case_type_id] as $icd_key=>$icd_val){
						$icd_data=$ins_case_data_arr[$patientListData->case_type_id][$icd_key];
						if($icd_data->pid==$patientListData->patient_id && $icd_data->provider==$Ins_company_id_chk && $icd_data->type==$insCheck){
							$effective_date=getDateFormatDB($icd_data->effective_date);
							$expiration_date=getDateFormatDB($icd_data->expiration_date);
							if($effective_date<=$patientListData->date_of_service && ($icd_data->expiration_date=="0000-00-00 00:00:00" || $expiration_date>=$patientListData->date_of_service)){
								$objInsGroupNumber = $icd_data;
							}
						}
						if($insCheck!='primary'){
							if($insCheck=='tertiary'){
								if($icd_data->pid==$patientListData->patient_id && $icd_data->provider==$patientListData->secondaryInsuranceCoId && $icd_data->type=='secondary'){
									$effective_date=getDateFormatDB($icd_data->effective_date);
									$expiration_date=getDateFormatDB($icd_data->expiration_date);
									if($effective_date<=$patientListData->date_of_service && ($icd_data->expiration_date=="0000-00-00 00:00:00" || $expiration_date>=$patientListData->date_of_service)){
										$objSecondaryInsGroupNumber = $icd_data;
										$secondryFlag = true;
									}
								}
							}else{
								if($icd_data->pid==$patientListData->patient_id && $icd_data->provider==$patientListData->primaryInsuranceCoId && $icd_data->type=='primary'){
									$effective_date=getDateFormatDB($icd_data->effective_date);
									$expiration_date=getDateFormatDB($icd_data->expiration_date);
									if($effective_date<=$patientListData->date_of_service && ($icd_data->expiration_date=="0000-00-00 00:00:00" || $expiration_date>=$patientListData->date_of_service)){
										$objSecondaryInsGroupNumber = $icd_data;
										$secondryFlag = true;
									}
								}
							}
						}else{
							if($icd_data->pid==$patientListData->patient_id && $icd_data->provider==$patientListData->secondaryInsuranceCoId && $icd_data->type=='secondary'){
								$effective_date=getDateFormatDB($icd_data->effective_date);
								$expiration_date=getDateFormatDB($icd_data->expiration_date);
								if($effective_date<=$patientListData->date_of_service && ($icd_data->expiration_date=="0000-00-00 00:00:00" || $expiration_date>=$patientListData->date_of_service)){
									$objSecondaryInsGroupNumber = $icd_data;
									$secondryFlag = true;
								}
							}elseif($secondryFlag == false && $icd_data->pid==$patientListData->patient_id && $icd_data->provider==$patientListData->tertiaryInsuranceCoId && $icd_data->type=='tertiary'){
								$effective_date=getDateFormatDB($icd_data->effective_date);
								$expiration_date=getDateFormatDB($icd_data->expiration_date);
								if($effective_date<=$patientListData->date_of_service && ($icd_data->expiration_date=="0000-00-00 00:00:00" || $expiration_date>=$patientListData->date_of_service)){
									$objSecondaryInsGroupNumber = $icd_data;
								}
							}else{
								$objSecondaryInsGroupNumber = '';
							}
						}
					}
				}
			}
			$sec_ins_name=strtoupper($objSecInsuranceCoData->name);
			$sec_ins_house_code=strtoupper($objSecInsuranceCoData->in_house_code);
			//---- GET FACILITY DETAILS ------------
			$objfacility = $fac_data_arr[0];
			
			//--- GET PROVIDER DETAILS --------
			$physicianDetails = '';
			if($patientListData->primaryProviderId){
				$physicianDetails = $usr_data[$patientListData->primaryProviderId];										
			}

			//----- GET REFFERING PHYSICIAN DETAILS ----------		
			if($patientListData->reff_phy_id){
				$refferPhysicianId = $patientListData->reff_phy_id;
			}
			else{
				$refferPhysicianId = $patientDetails->primary_care_id;
			}		
			if($patientListData->reff_phy_nr==1){
				$refferPhysicianId=0;
			}
			$refferPhysician = (object)getRecords('refferphysician','physician_Reffer_id',$refferPhysicianId);
		
			$pdf_status = true;
			if($chk_insurance == 'secondarySubmit'){
				if($objSecondaryInsGroupNumber->Sec_HCFA == 1){
					$pdf_status = false;
				}
			}
			if($pdf_status == true){
				$fpdiCheck = true;
				if($patientListData->copayPaid == 0){
					$balAmt = $patientListData->totalBalance - $patientListData->copay;
				}
				else{
					$balAmt = $patientListData->totalBalance;
				}
				//---- SAVE RECORD FOR SUBMITTED ENCOUNTER -----				
				$post = array();
				$post['encounter_id'] = $patientListData->encounter_id;
				$post['patient_id'] = $patientListData->patient_id;
				$post['Ins_type'] = $insCheck;
				$post['Ins_company_id'] = $Ins_company_id_chk;
				$post['posted_amount'] = $balAmt;
				$post['operator_id'] = $_SESSION['authUserID'];
				$post['submited_date'] = date('Y-m-d');	
				
				$encounter_id = $patientListData->encounter_id;	
				$clm_control_num_type="7";
				if($claim_ctrl_no!=""){
					$clm_control_num=$claim_ctrl_no;
				}else{
					$clm_control_num=billing_global_get_clm_control_num($patientListData->patient_id,$patientListData->encounter_id,0,$insCheck);
				}
				if($clm_control_num=="" || $bill_type[$val]=="831"){
					$clm_control_num_type="";
					$clm_control_num="";
				}
				
				if($only_show==""){
					$insertId = AddRecords($post,'submited_record');
				}
	
				//----- RESPOSIBILTY PARTY CHECK ----------
				$subscriber_relationship = $objInsGroupNumber->subscriber_relationship;
				if($subscriber_relationship != "self" && $subscriber_relationship != "select"){
					$respPartyName = trim($objInsGroupNumber->subscriber_lname);
					$objInsGroupNumber->subscriber_fname;
					if($objInsGroupNumber->subscriber_fname){
						$respPartyName .= ', '.trim($objInsGroupNumber->subscriber_fname);
					}
					$respPartyName .= ' '.trim($objInsGroupNumber->subscriber_mname);
					$respPartyName .= ' '.trim($objInsGroupNumber->subscriber_suffix);
					$respPartyDOB = explode("-",$objInsGroupNumber->subscriber_DOB);
					$respPartyDOb = $respPartyDOB[1].' '.$respPartyDOB[2].' '.$respPartyDOB[0];
					if($objInsGroupNumber->subscriber_sex == "Male"){
						$Sx = 12;
						$S2x = 2;
					}
					if($objInsGroupNumber->subscriber_sex == "Female"){
						$Sx = 0;
						$S2x = 20;
					}
					$respPartyAddress = $objInsGroupNumber->subscriber_street;
					$respPartyCity = $objInsGroupNumber->subscriber_city;
					$respPartyState = $objInsGroupNumber->subscriber_state;
					if($patientDetails->status == "married"){
						$Mx = -26;
					}
					elseif($patientDetails->status == "single"){
						$Mx=-42;
					}
					else{
						$Mx=-11;
					}
					$respPartyPostalCode = $objInsGroupNumber->subscriber_postal_code;
					$respPartyHomePhone = preg_replace('/[^0-9]/','',$objInsGroupNumber->subscriber_phone);
					$respPartyareaCode = substr($respPartyHomePhone,0,3);
					$respPartyHomePhone = substr($respPartyHomePhone,3);					
					$respPartySs = $objInsGroupNumber->subscriber_street;
				}
				else{				
					$respPartyDetails = $pat_data[$patientListData->patient_id];
					$respPartyName = $respPartyDetails->lname;
					if($patientDetails->fname){
						$respPartyName .= ', '.ucfirst($respPartyDetails->fname);
					}
					if($patientDetails->mname){
						$respPartyName .= ' '.$respPartyDetails->mname;
					}
					if($patientDetails->suffix){
						$respPartyName .= ' '.$respPartyDetails->suffix;
					}
					$respPartyDOB = explode("-",$patientDetails->DOB);
					$respPartyDOb = $respPartyDOB[1].' '.$respPartyDOB[2].' '.$respPartyDOB[0];
					if($respPartyDetails->sex == "Male"){
						$Sx = 12;
						$S2x = 2;
					}
					if($respPartyDetails->sex == "Female"){
						$Sx = 0;
						$S2x = 20;
					}
					if($respPartyDetails->street2){
						$respPartyAddress = $respPartyDetails->street.', '.$respPartyDetails->street2;
					}
					else{
						$respPartyAddress = $respPartyDetails->street;
					}
					$respPartyCity = $respPartyDetails->city;
					$respPartyState = $respPartyDetails->state;
					if($patientDetails->status == "married"){
						$Mx=-26;
					}
					elseif($patientDetails->status == "single"){
						$Mx=-42;
					}
					else{
						$Mx=-11;
					}
					$respPartyPostalCode = $respPartyDetails->postal_code;
					$respPartyHomePhone = preg_replace('/[^0-9]/','',$respPartyDetails->phone_home);
					$respPartyareaCode = substr($respPartyHomePhone,0,3);
					$respPartyHomePhone = substr($respPartyHomePhone,3);
					$respPartySs = $respPartyDetails->ss;
				}
				
				//--- GET PATIENT DETAILS --------
				$patientDetails = $pat_data[$patientListData->patient_id];
				$patientName = $patientDetails->lname;
				if($patientDetails->fname){
					$patientName .= ', '.ucfirst($patientDetails->fname);
				}
				if($patientDetails->mname){
					$patientName .= ' '.$patientDetails->mname;
				}
				$PatientDOB = explode("-",$patientDetails->DOB);
				$PatientDOB = $PatientDOB[1].' '.$PatientDOB[2].' '.$PatientDOB[0];
				if($patientDetails->sex == "Male"){
					$Sx = 12;
				}
				else{
					$Sx = 0;
				}
				if($patientDetails->street2){
					$patientAddress = $patientDetails->street.', '.$patientDetails->street2;
				}
				else{
					$patientAddress = $patientDetails->street;
				}
				
				$patientCity = $patientDetails->city;
				$patientState = $patientDetails->state;
				if($patientDetails->status == "married"){
					$Mx=-26;
				}
				elseif($patientDetails->status == "single"){
					$Mx=-42;
				}
				else{
					$Mx=-11;
				}
				$patientPostalCode = $patientDetails->postal_code;
				$patientHomePhone = preg_replace('/[^0-9]/','',$patientDetails->phone_home);
				$areaCode = substr($patientHomePhone,0,3);
				$patientPhoneNumber = substr($patientHomePhone,3);				
				$patientSs = $patientDetails->ss;
				
				//--- PROCEDURE LOOP FOR SINGLE ENCOUNTER -----
				$page_count = 1; 
				$page_limit = 6;
				
				$total_count = ceil(count($chld_arr[$val]) / $page_limit);
				if($patientListData->enc_accept_assignment==2){
					$fpdiCheck = false;
				}else{
					if($total_count>0){
						for($p=0;$p<$total_count;$p++){
							$prev_hcfa_arr=array();
							$start_limit = ($page_count - 1) * $page_limit;
							$chld_page_arr=array();
							//---- GET ENCOUNTER DETAILS ------------
							$qry = imw_query("select patient_charge_list_details.*,cpt_fee_tbl.units as admin_cpt_unit,
									cpt_fee_tbl.cpt_comments as admin_ndc,cpt_fee_tbl.cpt_desc as admin_cpt_desc,cpt_fee_tbl.cpt4_code,
									cpt_fee_tbl.unit_of_measure,cpt_fee_tbl.measurement 
									from patient_charge_list_details join cpt_fee_tbl
									on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
										where 
									patient_charge_list_details.del_status='0' and patient_charge_list_details.charge_list_id = '$val'
									and patient_charge_list_details.proc_selfpay!='1' and cpt_fee_tbl.not_covered = '0' $chld_whr
									order by patient_charge_list_details.display_order,
									patient_charge_list_details.charge_list_detail_id limit $start_limit,$page_limit");
							while($row_chld=imw_fetch_array($qry)){
								$chld_page_arr[$row_chld['charge_list_id']][]=$row_chld;
							}
							$arrpatientChargeDetails = $chld_page_arr[$val];
			
							//--- FILL HCFA FORM FIELDS --------
							if($print_paper_type=='PrintCms_white'){
								$left_marg = - 10;
								$top_marg = ($group_margin['top_margin'] - 5);
								$left_marg = ($group_margin['left_margin'] + $left_marg);
								$left_marg = $left_marg == 0 ? 1 : $left_marg;
								$pagecount = $pdf->setSourceFile("../../library/html_to_pdf/predefined_forms/HCFA_FORM_WO.pdf");
							}else{
								if($enc_icd10>0){
									$pagecount = $pdf->setSourceFile("../../library/html_to_pdf/predefined_forms/HCFA_FORM_ICD10.pdf");
								}else{
									$pagecount = $pdf->setSourceFile("../../library/html_to_pdf/predefined_forms/HCFA_FORM.pdf");
								}
								$left_marg=0.1;
							}
							$pdf->setPrintHeader(false);
							$tplidx = $pdf->importPage(1);
							$pdf->AddPage();
							$pdf->useTemplate($tplidx,0,0);			
							$pdf->SetFont('Courier','',10);
							$page_count++;
							if($print_paper_type=='PrintCms_white'){
								$pdf->Ln($top_marg);
							}else{
								$pdf->Ln(8);
							}
							$minus_box_zero=0;
							if(in_array(strtolower($billing_global_server_name), array('brandoneye'))){
								$minus_box_zero=35;
							}
							//BOX NO. 0
							$pdf->Cell(110-$minus_box_zero);				
							$pdf->Cell(115,1,$objInsuranceCoData->name,12,0,'');
							$prev_hcfa_arr[]=$objInsuranceCoData->name.' HCFA 0 --~~';
							$pdf->Ln(5);
							$pdf->Cell(110-$minus_box_zero);
							$pdf->Cell(115,1,$objInsuranceCoData->contact_address,0,0,'');
							$prev_hcfa_arr[]=$objInsuranceCoData->contact_address.' HCFA 0a --~~';
							$pdf->Ln(5);
							$pdf->Cell(110-$minus_box_zero);
							if($objInsuranceCoData->City){
								$ins_zip_ext="";
								if($objInsuranceCoData->zip_ext!=""){
									$ins_zip_ext="-".$objInsuranceCoData->zip_ext;
								}
								$pdf->Cell(115,1,$objInsuranceCoData->City.', '.$objInsuranceCoData->State.' '.$objInsuranceCoData->Zip.$ins_zip_ext,0,0,'');
								$prev_hcfa_arr[]=$objInsuranceCoData->City.', '.$objInsuranceCoData->State.' '.$objInsuranceCoData->Zip.$ins_zip_ext.' HCFA 0b --~~';
							}
							$insName = strtoupper($objInsuranceCoData->name);
							$insHouseCode = strtoupper($objInsuranceCoData->in_house_code);
							preg_match('/MEDICAID/',strtoupper($objInsuranceCoData->in_house_code),$ins_house_code);
							$prev_hcfa_arr[]=$insHouseCode.' HCFA 0c --~~';
							$prev_hcfa_arr[]=$objInsuranceCoData->Payer_id_pro.' HCFA 0d --~~';
							$prev_hcfa_arr[]=$objInsuranceCoData->Payer_id.' HCFA 0e --~~';
							$pdf->Ln(7);
							
							//BOX NO. 1,1a
							if($insName == "MEDICARE" || $insHouseCode == "MEDICARE" || in_array($objInsuranceCoData->Payer_id_pro,$arr_RRM_payers) || in_array($objInsuranceCoData->Payer_id,$arr_RRM_payers)){
								$InsComImgWidth = -3;
							}else if($insName == "MEDICAID" || $insHouseCode == "MEDICAID" || count($ins_house_code)>0){
								$InsComImgWidth = 14;
							}else if($insName == "MEDICAID/UNISYS" || $insHouseCode == "MEDICAID/UNISYS"){
								$InsComImgWidth = 14;
							}else if($insName == "TRICARE CHAMPUS" || $insHouseCode == "TRICARE CHAMPUS"){
								$InsComImgWidth = 32;
							}else if($insName == "CHAMPVA" || $insHouseCode == "CHAMPVA"){
								$InsComImgWidth = 55;
							}else if($insName == "GROUP HEALTH PLAN" || $insHouseCode == "GROUP HEALTH PLAN"){
								$InsComImgWidth = 73;
							}else if($insName == "FECA BLKLUNG" || $insHouseCode == "FECA BLKLUNG"){
								$InsComImgWidth = 93;
							}else{
								if($insName == "Work comp" || $insHouseCode == "Work comp"){
									$InsComImgWidth = 108;
								}else{
									$InsComImgWidth = 108;
								}
							}
								
							//--- INSURANCE COMPANY NAME IMAGE ----
							$arrDiagnosisId = array();
							if(in_array(strtolower($billing_global_server_name), array('hartman'))){
								$arrDiagnosisId=unserialize(html_entity_decode($patientListData->all_dx_codes));
							}else{
								for($d = 0;$d< count($arrpatientChargeDetails);$d++){
									for($f=1;$f<=12;$f++){
										if($arrpatientChargeDetails[$d]['diagnosis_id'.$f]){
											$arrDiagnosisId[] = $arrpatientChargeDetails[$d]['diagnosis_id'.$f];
										}
									}
								}	
							}
							$diagnosisId1 = array();
							$diagnosisId1 = array_unique($arrDiagnosisId);
							$diagnosis_id = array_slice($diagnosisId1,0,12);
							$PlaceOfServiceId = explode(",",$PlaceOfService);
							$physician_name = $physicianDetails->fname.' ';
							$physician_name .= $physicianDetails->lname;
							
							$pdf->Cell($left_marg);
							$pdf->Cell($InsComImgWidth,10,'',0,0,'');
							$pdf->Cell(125-$InsComImgWidth,10,'X',0,0,'');
							$prev_hcfa_arr[]=$insName.' HCFA 1 --~~';
							$objInsGroupNumber->policy_number = preg_replace("/[^A-Za-z0-9]/","",$objInsGroupNumber->policy_number);
							$pdf->Cell(115,10,$objInsGroupNumber->policy_number,0,0,'');
							$prev_hcfa_arr[]=$objInsGroupNumber->policy_number.' HCFA 1a --~~';
							$pdf->Ln(9);
							
							//BOX NO. 2,3,4
							$pdf->Cell($left_marg);
							$pdf->Cell(75,9,$patientName,0,0,'');
							$prev_hcfa_arr[]=$patientName.' HCFA 2 --~~';
							$pdf->Cell(38-$Sx,9,$PatientDOB,0,0,'');
							$prev_hcfa_arr[]=$PatientDOB.' HCFA 3a --~~';
							
							//--- INSURED GENDER INFORMATION IMAGE ---
							$pdf->Cell(10+$Sx,9,'X',0,0,'');
							$prev_hcfa_arr[]=$patientDetails->sex.' HCFA 3b --~~';
							
							$pdf->Cell(70,9,$respPartyName,0,0,'');
							$prev_hcfa_arr[]=$respPartyName.' HCFA 4 --~~';
							$pdf->Ln(9);
							
							//BOX NO. 5a,6,7
							$pdf->Cell($left_marg);
							$relationship = $objInsGroupNumber->subscriber_relationship;
							if($relationship == "self"){
								$sRx = 1;
							}
							elseif($relationship == "Spouse"){
								$sRx = 14;
							}
							elseif($relationship == "Father" || $relationship == "Mother"){
								$sRx = 24;
							}
							else{
								$sRx = 37;
							}
							
							$pdf->Cell(77+$sRx,9,$patientAddress,0,0,'');
							$prev_hcfa_arr[]=$patientAddress.' HCFA 5a --~~';
							//--- SUBSCRIBER INSURED RELATIONSHIP IMAGE -----
							$pdf->Cell(47-$sRx,8,'X',0,0,'');
							$prev_hcfa_arr[]=$relationship.' HCFA 6 --~~';
							$pdf->Cell(10,9,$respPartyAddress,0,0,'');
							$prev_hcfa_arr[]=$respPartyAddress.' HCFA 7a --~~';
							$pdf->Ln(8);
							
							//BOX NO. 5b,5c,8,7b,7c
							$pdf->Cell($left_marg);
							$pdf->Cell(61,9,$patientCity,0,0,'');
							$prev_hcfa_arr[]=$patientCity.' HCFA 5b --~~';
							$pdf->Cell(64,9,$patientState,0,0,'');
							$prev_hcfa_arr[]=$patientState.' HCFA 5c --~~';
							
							//--- INSURED MARITAL STATUS IMAGE -------
							if($enc_icd10>0){
							}else{
								$pdf->Cell($Mx,9,'',0,0,'');
								$pdf->Cell(1,9,'X',0,0,'');
								$pdf->Cell(-$Mx,9,'',0,0,'');
								$prev_hcfa_arr[]=$patientDetails->status.' HCFA 8 --~~';
							}
							$pdf->Cell(57,9,$respPartyCity,0,0,'');
							$prev_hcfa_arr[]=$respPartyCity.' HCFA 7b --~~';
							$pdf->Cell(10,9,$respPartyState,0,0,'');
							$prev_hcfa_arr[]=$respPartyState.' HCFA 7c --~~';
							$pdf->Ln(8);
							
							//BOX NO. 5d,5e,5f,7d,7e,7f
							$pdf->Cell($left_marg);
							$pdf->Cell(34,9,$patientPostalCode,0,0,'');
							$prev_hcfa_arr[]=$patientPostalCode.' HCFA 5d --~~';
							$pdf->Cell(11,9,$areaCode,0,0,'');
							$prev_hcfa_arr[]=$areaCode.' HCFA 5e --~~';
							$pdf->Cell(82,9,$patientPhoneNumber,0,0,'');
							$prev_hcfa_arr[]=$patientPhoneNumber.' HCFA 5f --~~';
							
							//---- PATIENT STATUS IMAGE ----
							$WD = $left_marg + 94;
							$HG = $top_marg + 62;
							$pdf->Cell(33,9,$respPartyPostalCode,0,0,'');
							$prev_hcfa_arr[]=$respPartyPostalCode.' HCFA 7d --~~';
							$pdf->Cell(11,9,$respPartyareaCode,0,0,'');
							$prev_hcfa_arr[]=$respPartyareaCode.' HCFA 7e --~~';
							$pdf->Cell(10,9,$respPartyHomePhone,0,0,'');
							$prev_hcfa_arr[]=$respPartyHomePhone.' HCFA 7f --~~';
							$pdf->Ln(7);
							
							
							//BOX NO. 9,11
							$pdf->Cell($left_marg);
							$insuredName = trim($objSecondaryInsGroupNumber->subscriber_lname);
							if($objSecondaryInsGroupNumber->subscriber_fname){
								$insuredName .= ', '.trim($objSecondaryInsGroupNumber->subscriber_fname);
							}		
							$insuredDOB = $objSecondaryInsGroupNumber->subscriber_DOB;
							$insuredDOB_exp = explode("-",$insuredDOB);
							$ChangeDOB = $insuredDOB_exp[0].' '.$insuredDOB_exp[1].' '.$insuredDOB_exp[2];
							//--- SECONDARY INSURANCE GENDER INFORMATION CHECK -------
							$insuredSex = $objSecondaryInsGroupNumber->subscriber_sex;
							if($insuredSex == "Female"){
								$imgWidth = -43;
							}
							else{
								$imgWidth = -58;
							}
							if($objSecondaryInsGroupNumber == ""){
								$insuredName = 'None';
							}	
							if(($sec_ins_name == "MEDICARE" || $sec_ins_house_code == "MEDICARE") && in_array(strtolower($billing_global_server_name), array('north central'))){
								$pdf->Cell(77,11,'',0,0,'');	
							}else{
								$pdf->Cell(77,11,$insuredName,0,0,'');	
								$prev_hcfa_arr[]=$insuredName.' HCFA 9 --~~';	
							}
							$pdf->Cell(48,10,'',0,0,'');
							$show_box11_val=$objInsGroupNumber->policy_number;
							if(in_array(strtolower($billing_global_server_name), array('manahan','heca')) && $objInsGroupNumber->group_number!=""){
								$show_box11_val=$objInsGroupNumber->group_number;
							}
							if(in_array(strtolower($billing_global_server_name), array('lie','liesc','liasc','domi','swagelwootton','pilkintoneye','dso','lehigh','lodenvision','clarisvision','samo','greenwich','cecc','associatedeye','clarityeye','brian','eyephysiciansofaustin','creekside','raleighophthalmology','shoreline','kung'))){
								$show_box11_val=$objInsGroupNumber->group_number;
							}
							if(($objInsuranceCoData->Payer_id_pro=='59274' || $objInsuranceCoData->Payer_id=='59274') && in_array(strtolower($billing_global_server_name), array('sakowitz'))){
								$show_box11_val=$objInsGroupNumber->group_number;
							}
							if(strtolower($insHouseCode) == "medso" && in_array(strtolower($billing_global_server_name), array('witlin'))){
								$pdf->Cell(10,11,'',0,0,'');
							}else if((strtolower($insHouseCode) == "affinity health plan" || strtolower($insName) == "affinity health plan" || strtolower($objInsuranceCoData->emdeon_payer_eligibility)=='afnty') && in_array(strtolower($billing_global_server_name), array('northshore'))){
								$pdf->Cell(10,11,'',0,0,'');
							}else if($insCheck=='secondary' && (in_array($objInsuranceCoData->Payer_id_pro,$arr_Medicare_payers) || in_array($objInsuranceCoData->Payer_id,$arr_Medicare_payers)) && in_array(strtolower($billing_global_server_name), array('mackool','north central','silverman'))){
								$pdf->Cell(10,11,$objSecondaryInsGroupNumber->policy_number,0,0,'');
								$prev_hcfa_arr[]=$objSecondaryInsGroupNumber->policy_number.' HCFA 11 --~~';
							}else if((in_array($objInsuranceCoData->Payer_id_pro,$arr_Medicare_payers) || in_array($objInsuranceCoData->Payer_id,$arr_Medicare_payers)) && in_array(strtolower($billing_global_server_name), array('gewirtz','kung','desert','shoreline'))){
								$pdf->Cell(10,11,'None',0,0,'');
								$prev_hcfa_arr[]='None'.' HCFA 11 --~~';
							}else if((in_array($objInsuranceCoData->Payer_id_pro,$arr_Medicare_payers) || in_array($objInsuranceCoData->Payer_id,$arr_Medicare_payers) || in_array($objInsuranceCoData->Payer_id_pro,$arr_DME_payers) || in_array($objInsuranceCoData->Payer_id,$arr_DME_payers)) && in_array(strtolower($billing_global_server_name), array('brian'))){
								$pdf->Cell(10,11,'None',0,0,'');
								$prev_hcfa_arr[]='None'.' HCFA 11 --~~';
							}else if(in_array(strtolower($billing_global_server_name), array('leps','pilkintoneye','shabatian'))){
								$pdf->Cell(10,11,'',0,0,'');
							}else if(in_array(strtolower($billing_global_server_name), array('clearvue'))){
								$pdf->Cell(10,11,'None',0,0,'');
								$prev_hcfa_arr[]='None'.' HCFA 11 --~~';
							}else{
								if($show_box11_val==""){	
									$pdf->Cell(10,11,'None',0,0,'');
									$prev_hcfa_arr[]='None'.' HCFA 11 --~~';
								}else{
									$pdf->Cell(10,11,$show_box11_val,0,0,'');
									$prev_hcfa_arr[]=$show_box11_val.' HCFA 11 --~~';
								}
							}
							$pdf->Ln(8);
							
							//--- IMAGE FOR PATIENT CONDITIONS ---------
							if($chk_work_val!=""){
								$final_width=1;
							}else{
								$final_width=16;
							}
							if($chk_auto_val!=""){
								$final_width_auto=1;
							}else{
								$final_width_auto=16;
							}
							//BOX NO. 9a,10a,10b,10c,11a,11a1
							$pdf->Cell($left_marg);
							$objSecondaryInsGroupNumber->policy_number = preg_replace("/[^A-Za-z0-9]/","",$objSecondaryInsGroupNumber->policy_number);
							
							if($insCheck=='secondary' && (in_array($objInsuranceCoData->Payer_id_pro,$arr_Medicare_payers) || in_array($objInsuranceCoData->Payer_id,$arr_Medicare_payers)) && in_array(strtolower($billing_global_server_name), array('mackool','north central','silverman'))){
								$pdf->Cell(82+$final_width,12,$show_box11_val,0,0,'');
								$prev_hcfa_arr[]=$show_box11_val.' HCFA 9a --~~';
							}else if(($sec_ins_name == "MEDICARE" || $sec_ins_house_code == "MEDICARE") && in_array(strtolower($billing_global_server_name), array('north central'))){
								$pdf->Cell(82+$final_width,12,'',0,0,'');
							}else{
								$pdf->Cell(82+$final_width,12,$objSecondaryInsGroupNumber->policy_number,0,0,'');
								$prev_hcfa_arr[]=$objSecondaryInsGroupNumber->policy_number.' HCFA 9a --~~';
							}
							$prev_hcfa_arr[]=$chk_work_val.' HCFA 10a --~~';
							$pdf->Cell(46-$final_width,14,'X',0,0,'');
							$pdf->Cell(2);
							
							if((in_array($objInsuranceCoData->Payer_id_pro,$arr_Medicare_payers) || in_array($objInsuranceCoData->Payer_id,$arr_Medicare_payers) || in_array($objInsuranceCoData->Payer_id_pro,$arr_DME_payers) || in_array($objInsuranceCoData->Payer_id,$arr_DME_payers)) && in_array(strtolower($billing_global_server_name), array('brian'))){
								$pdf->Cell(35+$S2x,15,'',0,0,'');
							}else{
								$pdf->Cell(35+$S2x,15,$respPartyDOb,0,0,'');	
								$prev_hcfa_arr[]=$respPartyDOb.' HCFA 11a --~~';
												
								//--- PRIMARY INSUREAD GENDER INFORMATION IMAGE FOR 11.A FIELD -----------
								if($S2x>1){
									$pdf->Cell(2,14,'X',0,0,'');
									$prev_hcfa_arr[]=$objInsGroupNumber->subscriber_sex.' HCFA 11a1 --~~';
								}
							}
							$pdf->Ln(8);
							
							//BOX NO. 9b,11b
							
							$pdf->Cell($left_marg);
							if($enc_icd10>0){
								$pdf->Cell(82+$final_width_auto,15,'',0,0,'');
							}else{
								if(($sec_ins_name == "MEDICARE" || $sec_ins_house_code == "MEDICARE") && in_array(strtolower($billing_global_server_name), array('north central'))){
									$pdf->Cell(82+$final_width_auto,15,'',0,0,'');
								}else{
									$pdf->Cell(82+$final_width_auto,15,$ChangeDOB,0,0,'');
									$prev_hcfa_arr[]=$ChangeDOB.' HCFA 9b --~~';
								}
							}
							if($enc_icd10>0){
							}else{	
								if(($sec_ins_name == "MEDICARE" || $sec_ins_house_code == "MEDICARE") && in_array(strtolower($billing_global_server_name), array('north central'))){
								}else{
									if($objSecondaryInsGroupNumber != ""){
										//--- SECONDARY INSURED GENDER INFORMATION IMAGE FOR 9.B FIELD -----------
										$pdf->Cell($imgWidth,9,'',0,0,'');
										$pdf->Cell(0.1,15,'X',0,0,'');
										$pdf->Cell(-$imgWidth,9,'',0,0,'');
										$prev_hcfa_arr[]=$insuredSex.' HCFA 9b1 --~~';					
									}
								}
							}
							$pdf->Cell(27-$final_width_auto,15,'X',0,0,'');
							$pdf->Cell(19,14,$chk_auto_st,0,0,'');
							$prev_hcfa_arr[]=$chk_auto_val.' HCFA 10b --~~';
							$prev_hcfa_arr[]=$chk_auto_st.' HCFA 10b2 --~~';
							if((in_array($objInsuranceCoData->Payer_id_pro,$arr_Medicare_payers) || in_array($objInsuranceCoData->Payer_id,$arr_Medicare_payers) || in_array($objInsuranceCoData->Payer_id_pro,$arr_DME_payers) || in_array($objInsuranceCoData->Payer_id,$arr_DME_payers)) && in_array(strtolower($billing_global_server_name), array('brian'))){
								$pdf->Cell(10,14,'',0,0,'');
							}else if(in_array(strtolower($billing_global_server_name), array('tutela'))){
								$pdf->Cell(10,14,'',0,0,'');
							}else{
								$pdf->Cell(10,14,$patientDetails->occupation,0,0,'');
								$prev_hcfa_arr[]=$patientDetails->occupation.' HCFA 11b --~~';
							}
							$pdf->Ln(9);
							
							$primaryInsDetails =  $ins_comp_data[$objSecondaryInsGroupNumber->provider];
							//BOX NO. 9c,11c
							$pdf->Cell($left_marg);
							$pdf->Cell(98,10,'',0,0,'');
							$pdf->Cell(28,14,'X',0,0,'');
							$prev_hcfa_arr[]='no HCFA 10c --~~';
							if((in_array($objInsuranceCoData->Payer_id_pro,$arr_Medicare_payers) || in_array($objInsuranceCoData->Payer_id,$arr_Medicare_payers) || in_array($objInsuranceCoData->Payer_id_pro,$arr_DME_payers) || in_array($objInsuranceCoData->Payer_id,$arr_DME_payers)) && in_array(strtolower($billing_global_server_name), array('brian'))){
								$pdf->Cell(6,12,'',0,0,'');
							}else if((in_array($objInsuranceCoData->Payer_id_pro,$arr_DME_payers) || in_array($objInsuranceCoData->Payer_id,$arr_DME_payers)) && in_array(strtolower($billing_global_server_name), array('gewirtz'))){
								$pdf->Cell(6,12,'DMER-NHIC',0,0,'');
								$prev_hcfa_arr[]='DMER-NHIC'.' HCFA 11c --~~';
							}else{
								if($insCheck=='secondary' && (in_array($objInsuranceCoData->Payer_id_pro,$arr_Medicare_payers) || in_array($objInsuranceCoData->Payer_id,$arr_Medicare_payers)) && in_array(strtolower($billing_global_server_name), array('mackool','north central','silverman'))){
									$pdf->Cell(6,12,$primaryInsDetails->name,0,0,'');
									$prev_hcfa_arr[]=$primaryInsDetails->name.' HCFA 11c --~~';
								}else{
									$pdf->Cell(6,12,$objInsGroupNumber->plan_name,0,0,'');
									$prev_hcfa_arr[]=$objInsGroupNumber->plan_name.' HCFA 11c --~~';
								}
							}
							$pdf->Ln(9);
							
							if($insuredName != 'None' && !in_array(strtolower($billing_global_server_name), array('clearvue'))){
								$health= 1;
							}
							else{
								$health= 14;
							}
							
							//BOX NO. 9d,10d,11d
							$pdf->Cell($left_marg);
							//--- GET SECONDARY INSURANCE COMPANY NAME ----------
							if($insCheck=='secondary' && (in_array($objInsuranceCoData->Payer_id_pro,$arr_Medicare_payers) || in_array($objInsuranceCoData->Payer_id,$arr_Medicare_payers)) && in_array(strtolower($billing_global_server_name), array('mackool','north central','silverman'))){
								$pdf->Cell(125+$health,12,$objInsuranceCoData->name,0,0,'');	
								$prev_hcfa_arr[]=$objInsuranceCoData->name.' HCFA 9d --~~';
							}else if(($sec_ins_name == "MEDICARE" || $sec_ins_house_code == "MEDICARE") && in_array(strtolower($billing_global_server_name), array('north central'))){
								$pdf->Cell(125+$health,12,'',0,0,'');
							}else{
								$pdf->Cell(125+$health,12,$primaryInsDetails->name,0,0,'');	
								$prev_hcfa_arr[]=$primaryInsDetails->name.' HCFA 9d --~~';
							}
							
							//--- PATINET SECONDARY INSURANCE COMPANY CHECK ----------
							if($insCheck=='secondary' && (in_array($objInsuranceCoData->Payer_id_pro,$arr_Medicare_payers) || in_array($objInsuranceCoData->Payer_id,$arr_Medicare_payers)) && in_array(strtolower($billing_global_server_name), array('mackool','north central','silverman'))){
								$pdf->Cell(2,13,'',0,0,'');
							}else{
								$pdf->Cell(2,13,'X',0,0,'');
								$prev_hcfa_arr[]=$insuredName.' HCFA 11d --~~';
							}
							$pdf->Ln(18);
							
							//BOX NO. 12,13
							$cur_date = date(''.$global_date_format.'');
							$pdf->Cell($left_marg);
							$pdf->Cell(15,10,'',0,0,'');
							$pdf->Cell(72,8,'Signature On File',0,0,'');				
							$pdf->Cell(60,8,$cur_date,0,0,'');
							$pdf->Cell(60,8,'Signature On File',0,0,'');
							$prev_hcfa_arr[]=$cur_date.' HCFA 12b --~~';
							
							$onset_date="";
							$onset_date_tm_exp=explode(' ',$patientListData->admit_date);
							if($onset_date_tm_exp[0]!="0000-00-00"){
								$onset_date_exp=explode('-',$onset_date_tm_exp[0]);
								$onset_date = $onset_date_exp['1'].' '.$onset_date_exp['2'].' '.$onset_date_exp['0'];
							}
							$pdf->Ln(10);
							if(in_array(strtolower($billing_global_server_name), array('tutela'))){
								$onset_date="";
							}
							//BOX NO. 14,15,16
							$pdf->Cell($left_marg);
							$pdf->Cell(85,8,$onset_date,0,0,'');
							$prev_hcfa_arr[]=$onset_date.' HCFA 14 --~~';
							$pdf->Ln(3);
							
							//BOX NO. 17a
							$pdf->Cell($left_marg);
							//--- REFFERING PHYSICIAN CHECK ----------				
							$pdf->Cell(85,8,'',0,0,'');
							if(in_array(strtolower($billing_global_server_name), array('bennett'))){
								$pdf->Cell(85,11,'OTH000',0,0,'');
								$prev_hcfa_arr[]='OTH000'.' HCFA 17a --~~';
							}else if(in_array(strtolower($billing_global_server_name), array('patel'))){
								$ref_tax_zz="";
								if($refferPhysician->Texonomy!=""){
									$ref_tax_zz="ZZ";
								}
								$pdf->Cell(85,11,$ref_tax_zz.$refferPhysician->Texonomy,0,0,'');
								$prev_hcfa_arr[]=$ref_tax_zz.$refferPhysician->Texonomy.' HCFA 17a --~~';
							}else if(in_array($objInsuranceCoData->Payer_id_pro,$arr_DME_payers) || in_array($objInsuranceCoData->Payer_id,$arr_DME_payers)){
								$pdf->Cell(85,11,'',0,0,'');
							}else if($groupDetails['group_Federal_EIN']=="200067898" && ($objInsuranceCoData->Payer_id_pro=='MBNC2' || $objInsuranceCoData->Payer_id=='MBNC2') && in_array(strtolower($billing_global_server_name), array('asheville'))){
								$pdf->Cell(85,11,'',0,0,'');
							}else{
								if(count($ins_house_code)>0){
									$pdf->Cell(85,11,$refferPhysician->MDCD,0,0,'');
									$prev_hcfa_arr[]=$refferPhysician->MDCD.' HCFA 17a --~~';
								}else{
									$pdf->Cell(85,11,$refferPhysician->MDCR,0,0,'');
									$prev_hcfa_arr[]=$refferPhysician->MDCR.' HCFA 17a --~~';
								}
							}
							$pdf->Ln(5);
							
							//BOX NO. 17,17b,18a,18b
							$pdf->Cell(-2);
							$pdf->Cell($left_marg);
							if($enc_icd10>0){
								if(in_array(strtolower($billing_global_server_name), array('brian'))){
									$physicianName = 'DK  ';
								}else{
									$physicianName = 'DN  ';
								}
							}else{
								$physicianName = $refferPhysician->Title.' ';
							}
							if($refferPhysician->LastName=="" && $refferPhysician->FirstName==""){
								$physicianName = '  ';
							}
							$physicianName .= $refferPhysician->LastName.', ';
							$physicianName .= $refferPhysician->FirstName.' ';
							$physicianName .= $refferPhysician->MiddleName;
							$physicianName = ucwords(trim($physicianName));
							if(empty($refferPhysician->LastName)){
								$physicianName = preg_replace('/,/','',$physicianName);
							} 
							
							if($groupDetails['group_Federal_EIN']=="200067898" && ($objInsuranceCoData->Payer_id_pro=='MBNC2' || $objInsuranceCoData->Payer_id=='MBNC2') && in_array(strtolower($billing_global_server_name), array('asheville'))){
								$pdf->Cell(85,8,'',0,0,'');
								$pdf->Cell(50,8,'',0,0,'');	
							}else{
								$pdf->Cell(85,8,$physicianName,0,0,'');
								$pdf->Cell(50,8,$refferPhysician->NPI,0,0,'');	
								
								$prev_hcfa_arr[]=$physicianName.' HCFA 17 --~~';
								$prev_hcfa_arr[]=$refferPhysician->NPI.' HCFA 17b --~~';
							}
							
							//--- PATIENT ADMIN DATE -------
								$admitDate_exp = explode(" ",$patientListData->admit_date);
							$admitDate_exp_final = explode("-",$admitDate_exp[0]);
							$admitDate = $admitDate_exp_final[1].' '.$admitDate_exp_final[2].' '.$admitDate_exp_final[0];				
							if($admitDate_exp_final[0] == 0000 || in_array(strtolower($billing_global_server_name), array('asheville','summiteye','cfei'))){
								$admitDate = '';
							}
							$pdf->Cell(35,9,$admitDate,0,0,'');
							$prev_hcfa_arr[]=$admitDate.' HCFA 18a --~~';
		
							//--- PATIENT DISPATCH DATE -------
								$dischDate_exp = explode(" ",$patientListData->disch_date);
							$dischDate_exp_final = explode("-",$dischDate_exp[0]);
							$dischDate = $dischDate_exp_final[1].' '.$dischDate_exp_final[2].' '.$dischDate_exp_final[0];
							if($dischDate_exp_final[0] == 0000 || in_array(strtolower($billing_global_server_name), array('asheville','tec','summiteye','lehigh','cfei'))){
								$dischDate = '';
							}
							$pdf->Cell(10,9,$dischDate,0,0,'');	
							$prev_hcfa_arr[]=$dischDate.' HCFA 18b --~~';	
							
							//BOX NO. 19,20
							$d = 0;
							$h = 0;
							for($f=1;$f<=12;$f++){
								${'diagnosis_id'.$f} = '';
							}
							$diagnose_arr = array();
							foreach($diagnosis_id as $key => $vals){
								if($vals != ""){
									$objDiagnosisDetails = $dx_desc_data[$vals];
									$diagnose_arr[$vals] = $d+1;
									$h = $d+1;
									if(in_array(strtolower($billing_global_server_name), array('manahan'))){
										$objDiagnosisDetails->diag_description="";
									}
									
									if($vals!=""){
										if($enc_icd10>0){
											$diagnose_arr[$vals] = chr($diagnose_arr[$vals]+64);
											${'diagnosis_id'.$h} = $vals;
										}else{
											${'diagnosis_id'.$h} = $vals.'         '.substr($objDiagnosisDetails->diag_description,0,30);
										}
									}
									$d++;
								}
							}
						
							//----- MAKE PROCEDURE CODE IN ASC ORDER ---------	
							$arrProc = array();
							$arrProc1 = array();
							$arrProc2 = array();
							$arr_admin_cpt_units = array();
							$CLIA_Procedure	 = false;
							$ndc_str_arr=array();
							for($d = 0;$d< count($arrpatientChargeDetails);$d++){
								$id = $arrpatientChargeDetails[$d]['procCode'];
								$charge_list_detail_id = $arrpatientChargeDetails[$d]['charge_list_detail_id'];
								$cpt4_code = $arrpatientChargeDetails[$d]['cpt4_code'];
								$procArrayRes = array_search($cpt4_code,$eAndMCptArray);
								if($procArrayRes !== false){
									$arrProc1[] = $charge_list_detail_id;
								}
								else{
									$arrProc2[] = $charge_list_detail_id;
								}
								if(trim($cpt4_code) != 'V2785'){
									$arr_admin_cpt_units[$charge_list_detail_id]=$arrpatientChargeDetails[$d]['admin_cpt_unit'];
									$arr_admin_cpt_ndc[$charge_list_detail_id]=$arrpatientChargeDetails[$d]['admin_ndc'];
									$arr_admin_cpt_desc[$charge_list_detail_id]=$arrpatientChargeDetails[$d]['admin_cpt_desc'];
									$arr_admin_cpt_unit_of_measure[$charge_list_detail_id]=$arrpatientChargeDetails[$d]['unit_of_measure'];
									$arr_admin_cpt_measurement[$charge_list_detail_id]=$arrpatientChargeDetails[$d]['measurement'];
								}
								
								if(trim($cpt4_code) == '83861' || trim($cpt4_code) == '83516' || trim($cpt4_code) == '87809'){
									$CLIA_Procedure = true;
								}
								if(trim($cpt4_code) == 'V2785'){
									$ndc_str_arr[$arrpatientChargeDetails[$d]['admin_ndc']]=$arrpatientChargeDetails[$d]['admin_ndc'];
								}else if($arrpatientChargeDetails[$d]['admin_ndc']!="" && count($ndc_str_arr)<2){
									if($arrpatientChargeDetails[$d]['unit_of_measure']!="" && $arrpatientChargeDetails[$d]['measurement']!=""){
										$show_n4_un=' '.$arrpatientChargeDetails[$d]['unit_of_measure'].$arrpatientChargeDetails[$d]['measurement'];
									}else{
										$show_n4_un=' UN'.unit_format($arrpatientChargeDetails[$d]['units']);
									}
									if(in_array(strtolower($billing_global_server_name), array('eyeclinicsmichigan','shoreline'))){
										$show_n4_un="";
									}
									if(in_array(strtolower($billing_global_server_name), array('tyson'))){
										$ndc_str_arr[$arrpatientChargeDetails[$d]['admin_ndc']]="N4".$arrpatientChargeDetails[$d]['admin_ndc'];
									}else{
										$ndc_str_arr[$arrpatientChargeDetails[$d]['admin_ndc']]="N4".$arrpatientChargeDetails[$d]['admin_ndc'].$show_n4_un;
									}
								}
								
							}
							$arrProc = array_merge($arrProc1,$arrProc2);
							
							$pdf->Ln(7);
							$pdf->Cell($left_marg);
							$notes_arr=array();
							for($d = 0;$d< count($arrpatientChargeDetails);$d++){
								if($arrpatientChargeDetails[$d]['notes']!=""){
									$notes_arr[] = $arrpatientChargeDetails[$d]['notes'];
								}
							}
							if(count($ndc_str_arr)>0 && !in_array(strtolower($billing_global_server_name), array('tutela','kirkeye'))){
								$notes_str=substr(implode(', ',$ndc_str_arr),0,65);
							}else{
								$notes_str=substr(implode(', ',$notes_arr),0,65);
							}
							if(in_array(strtolower($billing_global_server_name), array('bennett')) && (in_array($objInsuranceCoData->Payer_id_pro,array('61129')) || in_array($objInsuranceCoData->Payer_id,array('61129')))){
								if($physicianDetails->TaxonomyId!=""){
									$notes_str='zz'.$physicianDetails->TaxonomyId;
								}
							}
							$pdf->Cell(139,10,$notes_str,0,0,'');
							$prev_hcfa_arr[]=$notes_str.' HCFA 19 --~~';
							$pdf->Cell(2,11,'X',0,0,'');
							$prev_hcfa_arr[]='no HCFA 20 --~~';
							$pdf->Ln(6);
							
							//BOX NO. 21a
							$pdf->Cell($left_marg);
							$pdf->Cell(102);
							$pdf->Cell(2,10,$enc_icd10_ind,0,0,'');	
							$prev_hcfa_arr[]=$enc_icd10_ind.' HCFA 21a --~~';
							$pdf->Ln(3);
							
							//BOX NO. 21a,21b,21c,21d,21e,21f,21g,21h,21i,21j,21k,21l,22a,22b,23
							$pdf->Cell($left_marg);
							$pdf->Cell(3,10,'',0,0,'');	
							if($enc_icd10>0){
								
								$pdf->Cell(35,10,$diagnosis_id1,0,0,'');
								$pdf->Cell(33,10,$diagnosis_id2,0,0,'');
								$pdf->Cell(32,10,$diagnosis_id3,0,0,'');
								$pdf->Cell(23,10,$diagnosis_id4,0,0,'');
								$pdf->Cell(28,10,$clm_control_num_type,0,0,'');
								$pdf->Cell(25,10,$clm_control_num,0,0,'');
								
								$pdf->Ln(4.4);
								$pdf->Cell($left_marg);
								$pdf->Cell(3,10,'',0,0,'');
								$pdf->Cell(35,10,$diagnosis_id5,0,0,'');
								$pdf->Cell(33,10,$diagnosis_id6,0,0,'');
								$pdf->Cell(32,10,$diagnosis_id7,0,0,'');
								$pdf->Cell(35,10,$diagnosis_id8,0,0,'');
								
								$pdf->Ln(4.4);
								$pdf->Cell($left_marg);
								$pdf->Cell(3,10,'',0,0,'');
								$pdf->Cell(35,10,$diagnosis_id9,0,0,'');
								$pdf->Cell(33,10,$diagnosis_id10,0,0,'');
								$pdf->Cell(32,10,$diagnosis_id11,0,0,'');
								$pdf->Cell(35,10,$diagnosis_id12,0,0,'');
								
								$prev_hcfa_arr[]=$diagnosis_id1.' HCFA 21a1 --~~';
								$prev_hcfa_arr[]=$diagnosis_id2.' HCFA 21b1 --~~';
								$prev_hcfa_arr[]=$diagnosis_id3.' HCFA 21c1 --~~';
								$prev_hcfa_arr[]=$diagnosis_id4.' HCFA 21d1 --~~';
								$prev_hcfa_arr[]=$clm_control_num_type.' HCFA 22a --~~';
								$prev_hcfa_arr[]=$clm_control_num.' HCFA 22b --~~';
								
								$prev_hcfa_arr[]=$diagnosis_id5.' HCFA 21e1 --~~';
								$prev_hcfa_arr[]=$diagnosis_id6.' HCFA 21f1 --~~';
								$prev_hcfa_arr[]=$diagnosis_id7.' HCFA 21g1 --~~';
								$prev_hcfa_arr[]=$diagnosis_id8.' HCFA 21h1 --~~';
								
								$prev_hcfa_arr[]=$diagnosis_id9.' HCFA 21i1 --~~';
								$prev_hcfa_arr[]=$diagnosis_id10.' HCFA 21j1 --~~';
								$prev_hcfa_arr[]=$diagnosis_id11.' HCFA 21k1 --~~';
								$prev_hcfa_arr[]=$diagnosis_id12.' HCFA 21l1 --~~';
									
							}else{
								//--- SEPRATE DIAGNOSE CODE FROM DECIMAL ------					
								$diagnosis_id1_arr = preg_split('/\./',$diagnosis_id1);
								$diagnos_pointer1 = $diagnosis_id1_arr[0];
								$diagnos_desc1 = $diagnosis_id1_arr[1];
								$diagnosis_id3_arr = preg_split('/\./',$diagnosis_id3);
								$diagnos_pointer3 = $diagnosis_id3_arr[0];
								$diagnos_desc3 = $diagnosis_id3_arr[1];
			
								//--- DIAGNOSE CODE LENGTH CHECK --------
								if(strlen($diagnos_desc3) > 31){
									$diagnos_desc3 = substr($diagnos_desc3,0,31);
								}
								$pdf->Cell(10,10,$diagnos_pointer1,0,0,'');
								$pdf->Cell(59,10,$diagnos_desc1,0,0,'');
								$pdf->Cell(10,10,$diagnos_pointer3,0,0,'');
								$pdf->Cell(59,10,$diagnos_desc3,0,0,'');
								$pdf->Ln(8.8);
								$pdf->Cell($left_marg);
								$pdf->Cell(3,10,'',0,0,'');
								//--- SEPRATE DIAGNOSE CODE FROM DECIMAL ------
								$diagnosis_id2_arr = preg_split('/\./',$diagnosis_id2);
								$diagnos_pointer2 = $diagnosis_id2_arr[0];
								$diagnos_desc2 = $diagnosis_id2_arr[1];
								$diagnosis_id4_arr = preg_split('/\./',$diagnosis_id4);
								$diagnos_pointer4 = $diagnosis_id4_arr[0];
								$diagnos_desc4 = $diagnosis_id4_arr[1];
			
								//--- DIAGNOSE CODE LENGTH CHECK --------
								if(strlen($diagnos_desc4) > 31){
									$diagnos_desc4 = substr($diagnos_desc4,0,31);
								}
								$pdf->Cell(10,10,$diagnos_pointer2,0,0,'');
								$pdf->Cell(59,10,$diagnos_desc2,0,0,'');
								$pdf->Cell(9,10,$diagnos_pointer4,0,0,'');
								$pdf->Cell(59,10,$diagnos_desc4,0,0,'');
								
								$prev_hcfa_arr[]=$diagnos_pointer1.' HCFA 21a1 --~~';
								$prev_hcfa_arr[]=$diagnos_desc1.' HCFA 21a2 --~~';
								
								$prev_hcfa_arr[]=$diagnos_pointer2.' HCFA 21b1 --~~';
								$prev_hcfa_arr[]=$diagnos_desc2.' HCFA 21b2 --~~';
								
								$prev_hcfa_arr[]=$diagnos_pointer3.' HCFA 21c1 --~~';
								$prev_hcfa_arr[]=$diagnos_desc3.' HCFA 21c2 --~~';
								
								$prev_hcfa_arr[]=$diagnos_pointer4.' HCFA 21d1 --~~';
								$prev_hcfa_arr[]=$diagnos_desc4.' HCFA 21d2 --~~';
							}
							$approval1 = array();
							$referral = array();
							for($d = 0;$d< count($arrpatientChargeDetails);$d++){
								$approval1[] = $arrpatientChargeDetails[$d]['approval1'];
								$referral[] = $arrpatientChargeDetails[$d]['referral'];
							}
							
							$billing_global_clia_num = "";
							if($CLIA_Procedure && $patientListData->facility_id > 0){
								$billing_global_clia_num = get_CLIA_by_facility_id($patientListData->facility_id);
							}
							
							if(in_array(strtolower($billing_global_server_name), array('revision_eye')) && $CLIA_Procedure){
								$approval = '36D2020283';
							}else if(in_array(strtolower($billing_global_server_name), array('heca')) && $CLIA_Procedure){
								$approval = '33D2123087';
							}else if(in_array(strtolower($billing_global_server_name), array('creekside')) && $CLIA_Procedure){
								$approval = '23D2050592';
							}else if(in_array(strtolower($billing_global_server_name), array('farbowitz')) && $CLIA_Procedure){
								$approval = '31D2058603';
							}else if($billing_global_clia_num!="" && in_array(strtolower($billing_global_server_name), array('arizonaeye','lie','lifestyleeye','northshore'))){
								$approval = $billing_global_clia_num;
							}else if($referral[0] != ""){
								$approval = $referral[0]; 
							}else if($patientListData->auth_no!=""){
								$approval = $patientListData->auth_no;
							}else{
								$approval = ''; 
							}
							
							$pdf->Cell(50,9,$approval,0,0,'');
							$prev_hcfa_arr[]=$approval.' HCFA 23 --~~';
							$pdf->Ln(9);
							
							//BOX NO. 24a,24b,24c,24d,24e,24f,24g,24h,24i,24j
							$pdf->Cell($left_marg);	
							//---- START PROCEDURE DISPLAY -----------------
							$arrProcDetail = array();
							$sub_total_proc_charges = 0;
							$sub_total_proc_charges_cent = 0;
							$sub_proc_paid_charges = 0;
							$proc_balance = 0;
							$proc_writeOff=0;
							$npi_24j=$pos_fac_data_row="";
							for($d = 0;$d< count($arrProc);$d++){
								$arrProcDetail = $chld_arr[$val][$arrProc[$d]];
								$sub_proc_paid_charges += preg_replace('/,/','',$arrProcDetail['paidForProc']);
								$proc_balance += preg_replace('/,/','',$arrProcDetail['newBalance']);
								
								//--- Get Write-off---------
								$proc_writeOff=$proc_writeOff+$arrProcDetail['write_off'];
								$getWriteOffAmtStr = "SELECT write_off_amount FROM paymentswriteoff WHERE patient_id = '".$patientListData->patient_id."' 
												AND encounter_id = '".$patientListData->encounter_id."' AND charge_list_detail_id = '".$arrProc[$d]."' 
												AND delStatus = 0";
								$getWriteOffAmtQry = imw_query($getWriteOffAmtStr);
								while($getWriteOffAmtRow = imw_fetch_array($getWriteOffAmtQry)){
									$proc_writeOff = $proc_writeOff+$getWriteOffAmtRow['write_off_amount'];
								}
								//--- Get Write-off---------
								
								$diagnosisPointer = array();
								$modifier_id1 = array();
								for($f=1;$f<=12;$f++){
									$diagnosisPointer[] = $arrProcDetail['diagnosis_id'.$f];
								}					
								//--- GET MODIFIER CODE ---------
								$modifier_id1 = $modifier_data[$arrProcDetail['modifier_id1']];
								$modifier_id2 = $modifier_data[$arrProcDetail['modifier_id2']];
								$modifier_id3 = $modifier_data[$arrProcDetail['modifier_id3']];	
								$modifier_id4 = $modifier_data[$arrProcDetail['modifier_id4']];						
								$Pointer = "";
								$dateOfService = $patientListData->date_of_service;
								$getDate = explode("-",$dateOfService);
								if(in_array(strtolower($billing_global_server_name), array('shnayder'))){
									$dateOfService = $getDate[1].' '.$getDate[2].' '.$getDate[0];
								}else{
									$dateOfService = $getDate[1].' '.$getDate[2].' '.substr($getDate[0],2,4);
								}
								//--- GET DIAGNOSIS POINTER -------
								$diagnosis = array_unique($diagnosisPointer);
								$diagnosisPointerArr = array();
								foreach($diagnosis as $dia_val){
									if(empty($dia_val) == false){
										$diagnosisPointerArr[] = $diagnose_arr[$dia_val];
									}
								}	
								if(in_array(strtolower($billing_global_server_name), array('manahan','centerforsight'))){
									$Pointer = $diagnosisPointerArr[0];
								}else{
									if((in_array($objInsuranceCoData->Payer_id_pro,array('SKMI0')) || in_array($objInsuranceCoData->Payer_id,array('SKMI0'))) && in_array(strtolower($billing_global_server_name), array('shoreline'))){
										$Pointer = join('',array_slice($diagnosisPointerArr,0,4));
									}else{
										$Pointer = join(',',array_slice($diagnosisPointerArr,0,4));
									}
								}
								//--- GET PLACE OF SERVICE DETAILS ----------
								if($arrProcDetail['place_of_service']){
									$getposPracCode = $pos_data_arr[$arrProcDetail['place_of_service']];
								}
								//--- GET PROCEDURE CODE ----------					
								if($arrProcDetail['procCode']){
									$getProcCode = $arrProcDetail['cpt4_code'];
								}
								if(count($ins_house_code)>0 || in_array($objInsuranceCoData->ins_type,array('MC')) || in_array($objInsuranceCoData->Payer_id_pro,array('HPRNT')) || in_array($objInsuranceCoData->Payer_id,array('HPRNT')) || in_array($objInsuranceCoData->Payer_id,$arr_24j_33b_payers) || in_array($objInsuranceCoData->Payer_id_pro,$arr_24j_33b_payers)){
									if($patientListData->acc_anes_unit>0){
										$pdf->Cell($left_marg);
										$pdf->Ln(3.3);
									}else if($arr_admin_cpt_ndc[$arrProc[$d]]!=""){
										$pdf->Cell($left_marg);
										$pdf->Ln(3.3);
									}else{
										$pdf->Ln(3);
										$pdf->Cell($left_marg);
										$pdf->Cell(159);
										$show_taxonomy_no=0;
										if(in_array($objInsuranceCoData->Payer_id_pro,$arr_NE_Medicaid_payers) && in_array(strtolower($billing_global_server_name), array('manahan','clarityeye'))){
										}else if($groupDetails['group_Federal_EIN']!="721410176" && (in_array($objInsuranceCoData->ins_type,array('MC')) || in_array($objInsuranceCoData->Payer_id,$arr_24j_33b_payers) || in_array($objInsuranceCoData->Payer_id_pro,$arr_24j_33b_payers)) && in_array(strtolower($billing_global_server_name), array('azar'))){
										}else if(in_array(strtolower($billing_global_server_name), array('bennett','hattiesburg','empireeye','clarityeye','eyephysicianssouthwestvirginia','desert','keystone'))){
											if(in_array($objInsuranceCoData->ins_type,array('MC'))){
												$pdf->Cell(10,12,'ZZ',0,0,'');
												$prev_hcfa_arr[]='ZZ'.' HCFA 24i --~~';
											}else{
												$pdf->Cell(10,12,'',0,0,'');
											}
											$pdf->Cell(23,12,$physicianDetails->TaxonomyId,0,0,'');
											$prev_hcfa_arr[]=$physicianDetails->TaxonomyId.' HCFA 24j --~~';
										}else if(in_array($objInsuranceCoData->ins_type,array('MC')) && in_array(strtolower($billing_global_server_name), array('ues','lehigh','westfall','tutela','dso')) && $show_taxonomy_no==0){
											if(in_array(strtolower($billing_global_server_name), array('westfall'))){
											}else{
												$pdf->Cell(-10);
												$pdf->Cell(10,12,'ZZ',0,0,'');
												$prev_hcfa_arr[]='ZZ'.' HCFA 24i --~~';
												$pdf->Cell(23,12,$physicianDetails->TaxonomyId,0,0,'');
												$prev_hcfa_arr[]=$physicianDetails->TaxonomyId.' HCFA 24j --~~';
											}
										}else if(in_array(strtolower($billing_global_server_name), array('patel','icaresanantonio','pei'))){
											if($physicianDetails->TaxonomyId!=""){
												$pdf->Cell(10,12,'ZZ',0,0,'');
												$prev_hcfa_arr[]='ZZ'.' HCFA 24i --~~';
											}else{
												$pdf->Cell(10,12,'',0,0,'');
											}
											$pdf->Cell(23,12,$physicianDetails->TaxonomyId,0,0,'');
											$prev_hcfa_arr[]=$physicianDetails->TaxonomyId.' HCFA 24j --~~';
										}else if(count($ins_house_code)>0){
											$pdf->Cell(10,12,'',0,0,'');
											$pdf->Cell(33,12,$physicianDetails->MedicaidId,0,0,'');
											$show_taxonomy_no=1;
											$prev_hcfa_arr[]=$physicianDetails->MedicaidId.' HCFA 24j --~~';
										}
										
										$pdf->Ln(5.3);
									}
								}else{
									if($patientListData->acc_anes_unit>0){
										$pdf->Ln(3.3);
									}else if($arr_admin_cpt_ndc[$arrProc[$d]]!=""){
										$pdf->Ln(3.3);
									}else{
										if(in_array($objInsuranceCoData->Payer_id_pro,$arr_NE_Medicaid_payers) && in_array(strtolower($billing_global_server_name), array('clarityeye'))){
											$pdf->Ln(8.3);
										}else if(in_array(strtolower($billing_global_server_name), array('bennett','hattiesburg','empireeye','clarityeye','eyephysicianssouthwestvirginia','desert'))){
											$pdf->Ln(3);
											$pdf->Cell(159);
											if(in_array($objInsuranceCoData->ins_type,array('MC'))){
												$pdf->Cell(10,12,'ZZ',0,0,'');
												$prev_hcfa_arr[]='ZZ'.' HCFA 24i --~~';
											}else{
												$pdf->Cell(10,12,'',0,0,'');
											}
											$pdf->Cell(23,12,$physicianDetails->TaxonomyId,0,0,'');
											$prev_hcfa_arr[]=$physicianDetails->TaxonomyId.' HCFA 24j --~~';
											$pdf->Ln(5.3);
										}else if(in_array($objInsuranceCoData->ins_type,array('MC')) && in_array(strtolower($billing_global_server_name), array('ues','lehigh','westfall','tutela','dso'))){
											if(in_array(strtolower($billing_global_server_name), array('westfall'))){
												$pdf->Ln(8.3);
											}else{
												$pdf->Ln(3);
												$pdf->Cell(159);
												$pdf->Cell(10,12,'ZZ',0,0,'');
												$prev_hcfa_arr[]='ZZ'.' HCFA 24i --~~';
												$pdf->Cell(23,12,$physicianDetails->TaxonomyId,0,0,'');
												$prev_hcfa_arr[]=$physicianDetails->TaxonomyId.' HCFA 24j --~~';
												$pdf->Ln(5.3);
											}
										}else if(in_array(strtolower($billing_global_server_name), array('patel','icaresanantonio','pei'))){
											$pdf->Ln(3);
											$pdf->Cell(159);
											$pdf->Cell(10,12,'ZZ',0,0,'');
											$prev_hcfa_arr[]='ZZ'.' HCFA 24i --~~';
											$pdf->Cell(23,12,$physicianDetails->TaxonomyId,0,0,'');
											$prev_hcfa_arr[]=$physicianDetails->TaxonomyId.' HCFA 24j --~~';
											$pdf->Ln(5.3);
										}else if((in_array($objInsuranceCoData->Payer_id_pro,array('MCDIL','9999')) || in_array($objInsuranceCoData->Payer_id,array('MCDIL','9999'))) && in_array(strtolower($billing_global_server_name), array('kirkeye','henry'))){
											$pdf->Ln(3);
											$pdf->Cell(159);
											$pdf->Cell(10,12,'ZZ',0,0,'');
											$prev_hcfa_arr[]='ZZ'.' HCFA 24i --~~';
											$pdf->Cell(23,12,$physicianDetails->TaxonomyId,0,0,'');
											$prev_hcfa_arr[]=$physicianDetails->TaxonomyId.' HCFA 24j --~~';
											$pdf->Ln(5.3);
										}else{
											$pdf->Ln(8.3);
										}
									}	
								}	
								
								if($patientListData->acc_anes_unit>0){
									$start_time_exp = explode(" ",$patientListData->admit_date);
									$start_time = explode(":",$start_time_exp[1]);
									$end_time_exp = explode(" ",$patientListData->disch_date);
									$end_time = explode(":",$end_time_exp[1]);
									$total_unit=$arr_admin_cpt_units[$arrProc[$d]]+number_format($patientListData->acc_anes_unit,2);
									$pdf->Cell($left_marg);	
									$pdf->Cell(-2);
									$pdf->Cell(13,10,'START',0,0,'');
									$pdf->Cell(11,10,$start_time[0].':'.$start_time[1],0,0,'');
									$pdf->Cell(9,10,'END',0,0,'');
									$pdf->Cell(11,10,$end_time[0].':'.$end_time[1],0,0,'');
									$pdf->Cell(10,10,'TIME',0,0,'');
									$pdf->Cell(13,10,'UNITS',0,0,'');
									$pdf->Cell(11,10,number_format($patientListData->acc_anes_unit,2),0,0,'');
									$pdf->Cell(23,10,'MOD  UNITS',0,0,'');
									$pdf->Cell(8,10,'0.0',0,0,'');
									$pdf->Cell(13,10,'RVS = ',0,0,'');
									$pdf->Cell(8,10,$arr_admin_cpt_units[$arrProc[$d]],0,0,'');
									$pdf->Cell(17,10,'TOTAL = ',0,0,'');
									$pdf->Cell(14,10,$total_unit,0,0,'');
									
									$prev_hcfa_arr[]=$start_time[0].':'.$start_time[1].' HCFA 24a1-1'.$d.' --~~';
									$prev_hcfa_arr[]=$end_time[0].':'.$end_time[1].' HCFA 24a2-1'.$d.' --~~';
									$prev_hcfa_arr[]=number_format($patientListData->acc_anes_unit,2).' HCFA 24d-1'.$d.' --~~';
									$prev_hcfa_arr[]=$arr_admin_cpt_units[$arrProc[$d]].' HCFA 24f-1'.$d.' --~~';
									$prev_hcfa_arr[]=$total_unit.' HCFA 24g-1'.$d.' --~~';
								
									$show_taxonomy_no=0;
									if(count($ins_house_code)>0 || in_array($objInsuranceCoData->ins_type,array('MC')) || in_array($objInsuranceCoData->Payer_id_pro,array('HPRNT')) || in_array($objInsuranceCoData->Payer_id,array('HPRNT')) || in_array($objInsuranceCoData->Payer_id,$arr_24j_33b_payers) || in_array($objInsuranceCoData->Payer_id_pro,$arr_24j_33b_payers)){
										if(in_array($objInsuranceCoData->Payer_id_pro,$arr_NE_Medicaid_payers) && in_array(strtolower($billing_global_server_name), array('manahan','clarityeye'))){
										}else if($groupDetails['group_Federal_EIN']!="721410176" && (in_array($objInsuranceCoData->ins_type,array('MC')) || in_array($objInsuranceCoData->Payer_id,$arr_24j_33b_payers) || in_array($objInsuranceCoData->Payer_id_pro,$arr_24j_33b_payers)) && in_array(strtolower($billing_global_server_name), array('azar'))){
										}else if(count($ins_house_code)>0){
											$pdf->Cell(23,10,$physicianDetails->MedicaidId,0,0,'');
											$prev_hcfa_arr[]=$physicianDetails->MedicaidId.' HCFA 24j-1'.$d.' --~~';
											$show_taxonomy_no=1;
										}
									}
									if(in_array($objInsuranceCoData->Payer_id_pro,$arr_NE_Medicaid_payers) && in_array(strtolower($billing_global_server_name), array('clarityeye'))){
									}else if(in_array(strtolower($billing_global_server_name), array('bennett','hattiesburg','patel','empireeye','clarityeye','eyephysicianssouthwestvirginia','desert'))){
										$pdf->Cell(10,10,'ZZ',0,0,'');
										$prev_hcfa_arr[]='ZZ'.' HCFA 24i-1'.$d.' --~~';
										$pdf->Cell(23,10,$physicianDetails->TaxonomyId,0,0,'');
										$prev_hcfa_arr[]=$physicianDetails->TaxonomyId.' HCFA 24j-1'.$d.' --~~';
									}else if((in_array($objInsuranceCoData->Payer_id_pro,array('SB804','SX091','22248')) || in_array($objInsuranceCoData->Payer_id,array('SB804','SX091','22248'))) && in_array(strtolower($billing_global_server_name), array('ues','lehigh','westfall')) && $show_taxonomy_no==0){
										$pdf->Cell(10,10,'',0,0,'');
										$pdf->Cell(33,10,$physicianDetails->TaxonomyId,0,0,'');
										$prev_hcfa_arr[]=$physicianDetails->TaxonomyId.' HCFA 24j-1'.$d.' --~~';
									}else if((in_array($objInsuranceCoData->Payer_id_pro,array('MCDIL','9999')) || in_array($objInsuranceCoData->Payer_id,array('MCDIL','9999'))) && in_array(strtolower($billing_global_server_name), array('kirkeye','henry'))){
										$pdf->Cell(10,10,'ZZ',0,0,'');
										$prev_hcfa_arr[]='ZZ'.' HCFA 24i-1'.$d.' --~~';
										$pdf->Cell(23,10,$physicianDetails->TaxonomyId,0,0,'');
										$prev_hcfa_arr[]=$physicianDetails->TaxonomyId.' HCFA 24j-1'.$d.' --~~';
									}
									$pdf->Ln(5);
								}else{
									if($arr_admin_cpt_ndc[$arrProc[$d]]!=""){
										$pdf->Cell($left_marg);	
										$pdf->Cell(-2);
										$pdf->Cell(4,10,'N4',0,0,'');
										$prev_hcfa_arr[]='N4'.' HCFA 24a1-1'.$d.' --~~';
										$only_ndc_num=explode("/",$arr_admin_cpt_ndc[$arrProc[$d]]);
										//$ndc_space_cont=13+strlen($only_ndc_num[0]);
										if($arr_admin_cpt_unit_of_measure[$arrProc[$d]]!="" && $arr_admin_cpt_measurement[$arrProc[$d]]!=""){
											$show_n4_un=' '.$arr_admin_cpt_unit_of_measure[$arrProc[$d]].$arr_admin_cpt_measurement[$arrProc[$d]];
										}else{
											$show_n4_un=' UN'.unit_format($arrProcDetail['units']);
										}
										$ndc_space_cont=158;
										$pdf->Cell($ndc_space_cont,10,$only_ndc_num[0].$show_n4_un,0,0,'');
										$prev_hcfa_arr[]=$only_ndc_num[0].$show_n4_un.' HCFA 24a2-1'.$d.' --~~';
										//$pdf->Cell(32,10,$show_n4_un,0,0,'');
										//$prev_hcfa_arr[]=$show_n4_un.' HCFA 24d-1'.$d.' --~~';
										$prev_hcfa_arr[]=' HCFA 24d-1'.$d.' --~~';
										//$pdf->Cell(19,10,'',0,0,'');
										if(in_array($objInsuranceCoData->Payer_id_pro,$arr_NE_Medicaid_payers) && in_array(strtolower($billing_global_server_name), array('clarityeye'))){
										}else if(in_array(strtolower($billing_global_server_name), array('bennett','hattiesburg','patel','empireeye','clarityeye','eyephysicianssouthwestvirginia','desert'))){
											//$pdf->Cell(102,10,'',0,0,'');
											$pdf->Cell(10,10,'ZZ',0,0,'');
											$prev_hcfa_arr[]='ZZ'.' HCFA 24i-1'.$d.' --~~';
											$pdf->Cell(23,10,$physicianDetails->TaxonomyId,0,0,'');
											$prev_hcfa_arr[]=$physicianDetails->TaxonomyId.' HCFA 24j-1'.$d.' --~~';
										}else if((in_array($objInsuranceCoData->Payer_id_pro,array('SB804','SX091','22248')) || in_array($objInsuranceCoData->Payer_id,array('SB804','SX091','22248'))) && in_array(strtolower($billing_global_server_name), array('ues','lehigh','westfall'))){
											//$pdf->Cell(112,10,'',0,0,'');
											$pdf->Cell(10,10,'',0,0,'');
											$pdf->Cell(23,10,$physicianDetails->TaxonomyId,0,0,'');
											$prev_hcfa_arr[]=$physicianDetails->TaxonomyId.' HCFA 24j-1'.$d.' --~~';
										}else if((in_array($objInsuranceCoData->Payer_id_pro,array('MCDIL','9999')) || in_array($objInsuranceCoData->Payer_id,array('MCDIL','9999'))) && in_array(strtolower($billing_global_server_name), array('kirkeye','henry'))){
											//$pdf->Cell(102,10,'',0,0,'');
											$pdf->Cell(10,10,'ZZ',0,0,'');
											$prev_hcfa_arr[]='ZZ'.' HCFA 24i-1'.$d.' --~~';
											$pdf->Cell(23,10,$physicianDetails->TaxonomyId,0,0,'');
											$prev_hcfa_arr[]=$physicianDetails->TaxonomyId.' HCFA 24j-1'.$d.' --~~';
										}
										$pdf->Ln(5);
									}
								}
								$pdf->Cell($left_marg);	
								$pdf->Cell(-2.5);
								$pdf->Cell(23,8,$dateOfService,0,0,'');
								$pdf->Cell(17,8,$dateOfService,0,0,'');
								$prev_hcfa_arr[]=$dateOfService.' HCFA 24a1_'.$d.' --~~';
								$prev_hcfa_arr[]=$dateOfService.' HCFA 24a2_'.$d.' --~~';
								if($getposPracCode->pos_code == 11){
									$posCode = '  '.$getposPracCode->pos_code;
								}
								else{
									$posCode = '  '.$getposPracCode->pos_code;
								}
								$pdf->Cell(13,8,$posCode,0,0,'');
								$prev_hcfa_arr[]=$posCode.' HCFA 24b_'.$d.' --~~';
								$type_of_service = $arrProcDetail['type_of_service'];
								if($type_of_service){
									$objTOSDetail = $tos_data_arr[$type_of_service];
								}
								$sub_total_proc_charges += str_replace(',','',$arrProcDetail['procCharges'] * $arrProcDetail['units']);
								
								$totalcharges = numberFormat($arrProcDetail['procCharges']* $arrProcDetail['units'],2);						
								$paidCharges = substr($totalcharges,0,-3);
								$paidCharges = str_replace(',','',$paidCharges);
								$paidCharges=str_replace('$','',$paidCharges);
								$paidChargesCent = substr($totalcharges,-2);
								$sub_total_proc_charges_cent += $paidChargesCent;
								
								$chrg_space = 13;
								$chrg_cent_space = 15;
								if(strlen($paidCharges)<7){
									$chrg_space = $chrg_space+((7-strlen($paidCharges))*2);
									$chrg_cent_space=$chrg_cent_space-((7-strlen($paidCharges))*2);
								}
								$show_emg="";
								if((in_array($objInsuranceCoData->Payer_id_pro,array('MCDAZ')) || in_array($objInsuranceCoData->Payer_id,array('MCDAZ'))) && in_array(strtolower($billing_global_server_name), array('arizonaeye'))){	
									$show_emg="Y";
								}
								$pdf->Cell(8,8,$show_emg,0,0,'');
								$pdf->Cell(18,8,$getProcCode,0,0,'');
								$pdf->Cell(8,8,$modifier_id1->modifier_code,0,0,'');
								$pdf->Cell(8,8,$modifier_id2->modifier_code,0,0,'');
								$pdf->Cell(8,8,$modifier_id3->modifier_code,0,0,'');
								$pdf->Cell(8,8,$modifier_id4->modifier_code,0,0,'');
								$pdf->Cell($chrg_space,8,$Pointer,0,0,'');
								$pdf->Cell($chrg_cent_space,8,$paidCharges,0,0,'');
								$pdf->Cell(8,8,$paidChargesCent,0,0,'');
								$pdf->Cell(25,8,unit_format($arrProcDetail['units']),0,0,'');
								
								$prev_hcfa_arr[]=$getProcCode.' HCFA 24d1_'.$d.' --~~';
								$prev_hcfa_arr[]=$modifier_id1->modifier_code.' HCFA 24d2_'.$d.' --~~';
								$prev_hcfa_arr[]=$modifier_id2->modifier_code.' HCFA 24d3_'.$d.' --~~';
								$prev_hcfa_arr[]=$modifier_id3->modifier_code.' HCFA 24d4_'.$d.' --~~';
								$prev_hcfa_arr[]=$modifier_id4->modifier_code.' HCFA 24d5_'.$d.' --~~';
								$prev_hcfa_arr[]=$Pointer.' HCFA 24e_'.$d.' --~~';
								$prev_hcfa_arr[]=$paidCharges.' HCFA 24f_'.$d.' --~~';
								$prev_hcfa_arr[]=$paidChargesCent.' HCFA 24f2_'.$d.' --~~';
								$prev_hcfa_arr[]=unit_format($arrProcDetail['units']).' HCFA 24g_'.$d.' --~~';
								
								if($npi_24j==""){
									$pos_npi_24j = $pos_fac_data_arr[$arrProcDetail['posFacilityId']];
									$npi_24j=$pos_npi_24j->npiNumber;
								}

								if(in_array(strtolower($billing_global_server_name), array('hattiesburg'))){
								}else if(in_array('24j',$hide_row) and strtoupper($objInsuranceCoData->name)=="NHIC CORP"){
								}else if((in_array($objInsuranceCoData->Payer_id_pro,array('65054','EMRC')) || in_array($objInsuranceCoData->Payer_id,array('65054','EMRC'))) && in_array(strtolower($billing_global_server_name), array('azar'))){
									$pdf->Cell(33,8,$npi_24j,0,0,'');
									$prev_hcfa_arr[]=$npi_24j.' HCFA 24j_'.$d.' --~~';
								}else if($groupDetails['group_Federal_EIN']!="721410176" && (in_array($objInsuranceCoData->ins_type,array('MC')) || in_array($objInsuranceCoData->Payer_id,$arr_24j_33b_payers) || in_array($objInsuranceCoData->Payer_id_pro,$arr_24j_33b_payers)) && in_array(strtolower($billing_global_server_name), array('azar'))){
								}else{
									if(in_array($objInsuranceCoData->Payer_id_pro,array('HPRNT')) || in_array($objInsuranceCoData->Payer_id,array('HPRNT')) || in_array($objInsuranceCoData->Payer_id_pro,$arr_NE_Medicaid_payers) || in_array($objInsuranceCoData->Payer_id,$arr_NE_Medicaid_payers)){
										$pdf->Cell(33,8,$physicianDetails->user_npi,0,0,'');
										$prev_hcfa_arr[]=$physicianDetails->user_npi.' HCFA 24j_'.$d.' --~~';
									}else{
										preg_match('/MEDICARE/',strtoupper($insName),$med_ins_house_code);
										//if(in_array(strtolower($billing_global_server_name), array('millmanderr')) && empty($chk_institution) == false && $chk_institution == $gro_id_chl && (count($med_ins_house_code)>0 || $insName == "MEDICARE" || $insHouseCode == "MEDICARE"))
										if($patientListData->billing_type==2){
											$pdf->Cell(33,8,$sc_npi,0,0,'');
											$prev_hcfa_arr[]=$sc_npi.' HCFA 24j_'.$d.' --~~';
										}else{
											$pdf->Cell(33,8,$physicianDetails->user_npi,0,0,'');
											$prev_hcfa_arr[]=$physicianDetails->user_npi.' HCFA 24j_'.$d.' --~~';
										}
									}
								}
								if($arrProcDetail['posFacilityId']>0){
									$pos_fac_data_row=$pos_fac_data_arr[$arrProcDetail['posFacilityId']];
								}
							}
							//--- LINE BREAK CHECK AFTER PROCEDURE DISPLAY ----
							if($d == 1){
								$ln = 50;
								if($print_paper_type=='PrintCms_white' && $top_marg=="15"){
									$ln=$ln-0.3;
								}
							}
							else if($d == 2){
								$ln = 42;
								if($print_paper_type=='PrintCms_white' && $top_marg=="15"){
									$ln=$ln-0.5;
								}
							}
							else if($d == 3){
								$ln = 33;
							}
							else if($d == 4){
								$ln = 24;
							}
							else if($d == 5){
								$ln = 16;
							}
							else{
								$ln = 8;
							}
							$pdf->Ln($ln);
							
							//BOX NO. 25,26,27,28,29,30
							$pdf->Cell($left_marg);
							if($associatedeye_25_box_arr[$patientListData->primaryProviderId]!="" && $chk_work_val!="" && in_array(strtolower($billing_global_server_name), array('associatedeye'))){
								$pdf->Cell(43,10,$associatedeye_25_box_arr[$patientListData->primaryProviderId],0,0,'');
								$prev_hcfa_arr[]=$associatedeye_25_box_arr[$patientListData->primaryProviderId].' HCFA 25a1 --~~';
							}else{
								$pdf->Cell(43,10,$groupDetails['group_Federal_EIN'],0,0,'');
								$prev_hcfa_arr[]=$groupDetails['group_Federal_EIN'].' HCFA 25a1 --~~';
							}
							
							//----- FEDERAL TAX EIN IMAGE -------
							$pdf->Cell(12,10,'X',0,0,'');
							$prev_hcfa_arr[]='EIN HCFA 25a2 --~~';
							$encounterId = $patientListData->encounter_id;
							
							//-- TOTAL CHARGES OF A SINGLE ENCOUNTER ------
							$sub_total_proc_charges = number_format($sub_total_proc_charges,2);
							$sub_total_proc_charges_arr = preg_split('/\./',$sub_total_proc_charges);
							$totalAmt = $sub_total_proc_charges_arr[0];
							$totalAmt = str_replace(',','',$totalAmt);
							$totalCent = $sub_total_proc_charges_arr[1];
							//-- TOTAL CHARGES OF A SINGLE ENCOUNTER ------
							
							//--- GET ALL PAID AMOUNTS OF SINGLE ENCOUNTER -------
							if($patientListData->copayPaid > 0){
								$sub_proc_paid_charges += $patientListData->copay;
							}
							if($insCheck!='primary' && $proc_writeOff>0 && !in_array(strtolower($billing_global_server_name), array('manahan'))){
								//$sub_proc_paid_charges += $proc_writeOff;
							}
							if(in_array(strtolower($billing_global_server_name), array('bennett','seaside','nco','clarityeye','creekside','tyson','chamg'))){
								$sub_proc_paid_charges="";
								$sub_proc_paid_charges = array_sum($ins_paid_arr[$encounter_id]);
							}	
							$sub_proc_paid_charges = number_format($sub_proc_paid_charges,2);
							$sub_proc_paid_charges_arr = preg_split('/\./',$sub_proc_paid_charges);
							$amtPaid = $sub_proc_paid_charges_arr[0];
							$amtPaid = str_replace(',','',$amtPaid);
							$amtPaidCent = $sub_proc_paid_charges_arr[1];
							if((in_array($objInsuranceCoData->Payer_id_pro,$arr_Medicare_payers) || in_array($objInsuranceCoData->Payer_id,$arr_Medicare_payers)) && in_array(strtolower($billing_global_server_name), array('farbowitz','mackool'))){
								$amtPaid = '';
								$amtPaidCent = '';
							}else if(in_array(strtolower($billing_global_server_name), array('summiteye'))){
								$amtPaid = '';
								$amtPaidCent = '';
							}
							//--- GET ALL PAID AMOUNTS OF SINGLE ENCOUNTER -------
							
							//--- GET TOTAL BALANCE OF A SINGLE ENCOUNTER -------
							$balance_proc_charges = number_format($proc_balance,2);
							$balance_proc_charges_arr = preg_split('/\./',$balance_proc_charges);
							$totalBalance = $balance_proc_charges_arr[0];
							$totalBalance = str_replace(',','',$totalBalance);
							$totalBalanceCent = $balance_proc_charges_arr[1];
							//--- GET TOTAL BALANCE OF A SINGLE ENCOUNTER -------
							
							$chrg_space = 34;
							$chrg_cent_space = 17;
							if(strlen($totalAmt)<8){
								$chrg_space = $chrg_space+((8-strlen($totalAmt))*2);
								$chrg_cent_space=$chrg_cent_space-((8-strlen($totalAmt))*2);
							}
							
							$paid_space = 10;
							$paid_cent_space = 15;
							if(strlen($amtPaid)<7){
								$paid_space = $paid_space+((7-strlen($amtPaid))*2);
								$paid_cent_space=$paid_cent_space-((7-strlen($amtPaid))*2);
							}
							
							$bal_space = 6;
							$bal_cent_space = 17;
							if(strlen($totalBalance)<8){
								$bal_space = $bal_space+((8-strlen($totalBalance))*2);
								$bal_cent_space=$bal_cent_space-((8-strlen($totalBalance))*2);
							}
							
							$pdf->Cell(36,10,$patientListData->patient_id,0,0,'');				
							$prev_hcfa_arr[]=$patientListData->patient_id.' HCFA 26 --~~';
							$pdf->Cell($chrg_space,10,'X',0,0,'');
							$prev_hcfa_arr[]='yes HCFA 27 --~~';
							
							$pdf->Cell($chrg_cent_space,10,$totalAmt,0,0,'');
							$pdf->Cell($paid_space,10,$totalCent,0,0,'');
							$prev_hcfa_arr[]=$totalAmt.' HCFA 28a1 --~~';
							$prev_hcfa_arr[]=$totalCent.' HCFA 28a2 --~~';
							
							$pdf->Cell($paid_cent_space,10,$amtPaid,0,0,'');
							$pdf->Cell($bal_space,10,$amtPaidCent,0,0,'');
							$prev_hcfa_arr[]=$amtPaid.' HCFA 29a1 --~~';
							$prev_hcfa_arr[]=$amtPaidCent.' HCFA 29a2 --~~';
							
							if($enc_icd10>0){
							}else{
								$pdf->Cell($bal_cent_space,10,$totalBalance,0,0,'');
								$pdf->Cell(21,10,$totalBalanceCent,0,0,'');	
								
								$prev_hcfa_arr[]=$totalBalance.' HCFA 30a1 --~~';
								$prev_hcfa_arr[]=$totalBalanceCent.' HCFA 30a2 --~~';
							}
									
							//--- GET GROUP DETAILS ----------
							$facilityPh = preg_replace('/[^0-9]/','',$objfacility->phone);
							//---- FACILITY CHECK FOR ENCOUNTER -----------
							if($arrpatientChargeDetails[0]['posFacilityId']){
								$posFacilityId = array();
								for($d = 0;$d< count($arrpatientChargeDetails);$d++){
									$posFacilityId[] = $arrpatientChargeDetails[$d]['posFacilityId'];
								}
								$posFacilityId = array_unique($posFacilityId);
								$chk_pos_home = $pos_fac_data_arr[$posFacilityId[0]];
								if($chk_pos_home->facility_name != 'Home'){
									$posFacilityDetail = $pos_fac_data_arr[$posFacilityId[0]];
									$facility_name = $posFacilityDetail->facility_name;
									$pos_facility_address = $posFacilityDetail->pos_facility_address;
									$posfacilityCity = $posFacilityDetail->pos_facility_city.', '.$posFacilityDetail->pos_facility_state;
									$posfacilityCity .= ' '.$posFacilityDetail->pos_facility_zip.'-'.$posFacilityDetail->zip_ext;
								}else{
									if(in_array(strtolower($billing_global_server_name), array('creekside'))){
										$facility_name = "Patient's Home";
										$pos_facility_address = $patientDetails->street.' '.$patientDetails->street2;
										$posfacilityCity = $patientDetails->city.', '.$patientDetails->state;
										$posfacilityCity .= ' '.$patientDetails->postal_code;
										if($patientDetails->zip_ext!=""){
											$posfacilityCity .= '-'.$patientDetails->zip_ext;
										}
									}else{
										$facility_name = "Patient's Home";
										$pos_facility_address="";
										$posfacilityCity="";
									}
								}
								$npiNumber = $chk_pos_home->npiNumber;
								$alt_npi_number = $chk_pos_home->alt_npi_number;
								$facilityPhone = '';
							}else{					
								$facility_name = $groupDetails['name'];
								$pos_facility_address = $objfacility->street;
								$posfacilityCity = $objfacility->city.', '.$objfacility->state;
								$posfacilityCity .= ' '.$objfacility->postal_code.'-'.$objfacility->zip_ext;
								$npiNumber = $objfacility->fac_npi;
								$facilityPhone = preg_replace('/[^0-9]/','',$objfacility->phone);
								$alt_npi_number="";
							}
							
							$pdf->Ln(2);
							
							if($patientListData->billing_type==1 && in_array(strtolower($billing_global_server_name), array('cfei'))){
								$facilityPh=str_replace('-','',$refferPhysician->physician_phone);
								$groupDetails['name'] = $refferPhysician->LastName.', '.$refferPhysician->FirstName.' '.$refferPhysician->MiddleName;
								$groupDetails['name'] = trim($groupDetails['name']).' '.$refferPhysician->Title;
								$groupDetails['group_Address1'] = $refferPhysician->Address1;
								$groupDetails['group_City'] = $refferPhysician->City;
								$groupDetails['group_State'] = $refferPhysician->State;
								$groupDetails['group_Zip'] = $refferPhysician->ZipCode;
								$groupDetails['zip_ext'] = $refferPhysician->zip_ext;
								$groupDetails['group_NPI'] = $refferPhysician->NPI;
							}
							
							//BOX NO. 31,32,33
							$pdf->Cell($left_marg);
							$pdf->Cell(97,9,'',0,0,'');
							$pdf->Cell(65,22,$facilityPhone,0,0,'');
							$prev_hcfa_arr[]=$facilityPhone.' HCFA 32_2b --~~';
							
							$facilityPhCode = substr($facilityPh,0,3);
							$facilityPh = substr($facilityPh,3);
							$pdf->Cell(11,15,$facilityPhCode,0,0,'');
							$pdf->Cell(65,15,$facilityPh,0,0,'');
							
							$prev_hcfa_arr[]=$facilityPhCode.' HCFA 33_1a --~~';
							$prev_hcfa_arr[]=$facilityPh.' HCFA 33_1b --~~';
							
							$pdf->Ln(5);
							$pdf->Cell($left_marg);
							if(strlen($facility_name)>30 || strlen($groupDetails['name'])>35){
								$pdf->SetFont('Courier','',8);
							}
							
							$facility_name=substr($facility_name,0,35);
							$groupDetails[0]['name']=substr($groupDetails[0]['name'],0,35);
							
							$pdf->Cell(55,10,'',0,0,'');							
							$pdf->Cell(68,12,$facility_name,0,0,'');									
							$pdf->Cell(35,12,$groupDetails['name'],0,0,'');
							$prev_hcfa_arr[]=$facility_name.' HCFA 32_2 --~~';
							$prev_hcfa_arr[]=$groupDetails['name'].' HCFA 33_2 --~~';
						
							$pdf->SetFont('Courier','',10);
							$pdf->Ln(5);
							$pdf->Cell($left_marg);
							$pdf->Cell(55,10,'',0,0,'');
							$pdf->Cell(68,10,$pos_facility_address,0,0,'');
							$pdf->Cell(65,10,$groupDetails['group_Address1'],0,0,'');
							$prev_hcfa_arr[]=$pos_facility_address.' HCFA 32_3 --~~';
							$prev_hcfa_arr[]=$groupDetails['group_Address1'].' HCFA 33_3 --~~';
						
							$pdf->Ln(3);
							$pdf->Cell($left_marg);
							$physicianDetail = $physicianDetails->lname.', '.$physicianDetails->fname.' '.$physicianDetails->mname;
							$physicianDetail_FLM = $physicianDetails->fname.' '.$physicianDetails->lname.' ';					
							$pro_title=$physicianDetails->pro_title;
							$sc_nam=substr($sc_nam,0,23);
							if($patientListData->billing_type==2){
								$pdf->Cell(26,10,$sc_nam,0,0,'');
								$prev_hcfa_arr[]=$sc_nam.' HCFA 31_4 --~~';
							}else if(in_array(strtolower($billing_global_server_name), array('hartman'))){
								$pdf->Cell(26,10,$physicianDetail_FLM.$pro_title,0,0,'');
								$prev_hcfa_arr[]=$physicianDetail_FLM.$pro_title.' HCFA 31_4 --~~';
							}else{
								$pdf->Cell(26,10,$physicianDetail.$pro_title,0,0,'');
								$prev_hcfa_arr[]=$physicianDetail.$pro_title.' HCFA 31_4 --~~';
							}
							
							$cur_date = date(''.$global_date_format.'');
							$pdf->Cell(29,16,$cur_date,0,0,'');
							$prev_hcfa_arr[]=$cur_date.' HCFA 31_6 --~~';
							$facilityCity = $objfacility->city.', '.$objfacility->state.' '.$objfacility->postal_code.'-'.$objfacility->zip_ext;	
							$groupCity = $groupDetails['group_City'].', '.$groupDetails['group_State'].' '.$groupDetails['group_Zip'].'-'.$groupDetails['zip_ext'];					

							if(in_array(strtolower($billing_global_server_name), array('berkeleyeye'))){
								if(in_array($objInsuranceCoData->Payer_id,array('12K64'))){
									$groupDetails['group_Address1']=$pos_facility_address;
									$groupCity=$posfacilityCity;
								}else if(in_array($objInsuranceCoData->Payer_id,array('87726')) && $groupDetails[0]['name']=="Caplan Surgery Center"){
									$groupDetails['group_Address1']='P O BOX 4220';
									$groupCity='Houston, TX 77210-4220';
								}
							}
							
							if(in_array(strtolower($billing_global_server_name), array('gcny'))){
								$posfacilityCity=str_replace('-','',$posfacilityCity);
								$groupCity=str_replace('-','',$groupCity);
							}
							
							$pdf->Cell(68,12,$posfacilityCity,0,0,'');
							$pdf->Cell(63,12,$groupCity,0,0,'');
							$prev_hcfa_arr[]=$posfacilityCity.' HCFA 32_4 --~~';
							$prev_hcfa_arr[]=$groupCity.' HCFA 33_4 --~~';
							
							$pdf->Ln(5);
							$pdf->Cell($left_marg);
							if(in_array($objInsuranceCoData->Payer_id_pro,$arr_DME_payers) || in_array($objInsuranceCoData->Payer_id,$arr_DME_payers)){
								$pdf->Cell(6,12,'',0,0,'');
								$pdf->Cell(50,12,'Signature On File',0,0,'');
								$prev_hcfa_arr[]='Signature On File'.' HCFA 31_5 --~~';
							}else{
								$pdf->Cell(56,12,'',0,0,'');
							}
							if($insName != "MEDICARE" && !in_array($objInsuranceCoData->Payer_id_pro,$arr_RRM_payers) && !in_array($objInsuranceCoData->Payer_id,$arr_RRM_payers)){
								$pdf->Cell(30,12,$npiNumber,0,0,'');
								$prev_hcfa_arr[]=$npiNumber.' HCFA 32a --~~';
								if(in_array($objInsuranceCoData->Payer_id_pro,$arr_NE_Medicaid_payers) && in_array(strtolower($billing_global_server_name), array('clarityeye'))){
									$pdf->Cell(38,12,'',0,0,'');
								}else if(in_array($objInsuranceCoData->Payer_id_pro,$arr_DME_payers) || in_array($objInsuranceCoData->Payer_id,$arr_DME_payers) || stristr($groupDetails['name'],'Manahan')){
									$pdf->Cell(38,12,'',0,0,'');
								}else{
									$pdf->Cell(38,12,$chk_pos_home->taxId,0,0,'');
									$prev_hcfa_arr[]=$chk_pos_home->taxId.' HCFA 32b --~~';
								}
							}else{
								if($getposPracCode->pos_code == 11 && !in_array(strtolower($billing_global_server_name), array('desert','seaside','shoreline','betz'))){
									$pdf->Cell(30,12,'',0,0,'');
								}else{
									$pdf->Cell(30,12,$npiNumber,0,0,'');
									$prev_hcfa_arr[]=$npiNumber.' HCFA 32a --~~';
								}
								if(in_array(strtolower($billing_global_server_name), array('shoreline','betz'))){
									$pdf->Cell(38,12,$chk_pos_home->taxId,0,0,'');
									$prev_hcfa_arr[]=$chk_pos_home->taxId.' HCFA 32b --~~';
								}else{
									$pdf->Cell(38,12,'',0,0,'');
								}
							}
							$show_npi_33a=$groupDetails['group_NPI'];
							if($alt_npi_number!="" && in_array($objInsuranceCoData->ins_type,array('MB'))){
								$show_npi_33a=$alt_npi_number;
							}
							
							if((in_array($objInsuranceCoData->Payer_id_pro,array('60054','62308','61101')) || in_array($objInsuranceCoData->Payer_id,array('60054','62308','61101'))) && in_array(strtolower($billing_global_server_name), array('kirkeye'))){
								$show_npi_33a=$physicianDetails->user_npi;
							}
							
							if($patientListData->billing_type==1 && in_array(strtolower($billing_global_server_name), array('kirkeye'))){
								$show_npi_33a=$groupDetails['group_NPI'];
							}
							
							$pdf->Cell(28,12,$show_npi_33a,0,0,'');
							$prev_hcfa_arr[]=$show_npi_33a.' HCFA 33a --~~';
							$insurance_Practice_Code_id = '';
							$group_institution = $groupDetails['group_institution'];
							if($patientListData->billing_type == 2 && $objInsuranceCoData->institutional_Code_id != ''){
								$insurance_Practice_Code_id = $objInsuranceCoData->institutional_Code_id;
							}
							if($patientListData->billing_type != 2 && $objInsuranceCoData->insurance_Practice_Code_id != ''){
								$insurance_Practice_Code_id = $objInsuranceCoData->insurance_Practice_Code_id;
							}
							if($patientListData->billing_type==1 && in_array(strtolower($billing_global_server_name), array('cfei'))){
								$insurance_Practice_Code_id = $refferPhysician->Texonomy;
							}
							if(in_array($objInsuranceCoData->Payer_id_pro,$arr_NE_Medicaid_payers) && stristr($groupDetails['name'],'Manahan')){
								$pdf->Cell(76,12,'193400000X',0,0,'');
								$prev_hcfa_arr[]='193400000X'.' HCFA 33b --~~';
							}else if(in_array($objInsuranceCoData->Payer_id_pro,$arr_NE_Medicaid_payers) && stristr($groupDetails['name'],'Papillion')){
								$pdf->Cell(76,12,'261QA1903X',0,0,'');
								$prev_hcfa_arr[]='261QA1903X'.' HCFA 33b --~~';
							}else if(in_array(strtolower($billing_global_server_name), array('manahan','cep'))){
								$pdf->Cell(76,12,$groupDetails['group_Federal_EIN'],0,0,'');
								$prev_hcfa_arr[]=$groupDetails['group_Federal_EIN'].' HCFA 33b --~~';
							}else if($chk_work_val!="" && in_array(strtolower($billing_global_server_name), array('associatedeye'))){
								$pdf->Cell(76,12,$groupDetails['group_Federal_EIN'].'00',0,0,'');
								$prev_hcfa_arr[]=$groupDetails['group_Federal_EIN'].'00'.' HCFA 33b --~~';
							}else if($groupDetails['group_Federal_EIN']=="721288671" && (in_array($objInsuranceCoData->ins_type,array('MC')) || in_array($objInsuranceCoData->Payer_id,$arr_24j_33b_payers) || in_array($objInsuranceCoData->Payer_id_pro,$arr_24j_33b_payers)) && in_array(strtolower($billing_global_server_name), array('azar'))){
								$pdf->Cell(76,12,'1660060',0,0,'');
								$prev_hcfa_arr[]='1660060'.' HCFA 33b --~~';
							}else if($groupDetails['group_Federal_EIN']=="721410176" && (in_array($objInsuranceCoData->Payer_id,$arr_24j_33b_payers) || in_array($objInsuranceCoData->Payer_id_pro,$arr_24j_33b_payers)) && in_array(strtolower($billing_global_server_name), array('azar'))){
								$pdf->Cell(76,12,'1796298',0,0,'');
								$prev_hcfa_arr[]='1796298'.' HCFA 33b --~~';
							}else if(!in_array($objInsuranceCoData->ins_type,array('MC')) && (in_array($objInsuranceCoData->Payer_id_pro,array('HPRNT')) || in_array($objInsuranceCoData->Payer_id,array('HPRNT'))) && in_array(strtolower($billing_global_server_name), array('azar'))){
								$pdf->Cell(76,12,'216586',0,0,'');
								$prev_hcfa_arr[]='216586'.' HCFA 33b --~~';
							}else if(in_array($objInsuranceCoData->ins_type,array('MC')) && in_array(strtolower($billing_global_server_name), array('dso'))){	
								$pdf->Cell(76,12,'ZZ193400000X',0,0,'');
								$prev_hcfa_arr[]='ZZ193400000X'.' HCFA 33b --~~';
							}else if(in_array($objInsuranceCoData->ins_type,array('MC')) && in_array(strtolower($billing_global_server_name), array('westfall','tutela','dso'))){
								if($physicianDetails->TaxonomyId!="" && !in_array(strtolower($billing_global_server_name), array('westfall'))){
									$pdf->Cell(76,12,"ZZ".$physicianDetails->TaxonomyId,0,0,'');
									$prev_hcfa_arr[]="ZZ".$physicianDetails->TaxonomyId.' HCFA 33b --~~';
								}else{
									$pdf->Cell(76,12,$physicianDetails->TaxonomyId,0,0,'');
									$prev_hcfa_arr[]=$physicianDetails->TaxonomyId.' HCFA 33b --~~';
								}
							}else if((in_array($objInsuranceCoData->Payer_id_pro,array('27215')) || in_array($objInsuranceCoData->Payer_id,array('27215'))) && in_array(strtolower($billing_global_server_name), array('bennett'))){
								$pdf->Cell(76,12,'261QA1903X',0,0,'');
								$prev_hcfa_arr[]='261QA1903X'.' HCFA 33b --~~';
							}else if(in_array(strtolower($billing_global_server_name), array('tyson','swagelwootton','bennett','patel','empireeye','clarityeye','kirkeye','dso','domi','pei'))){
								if($physicianDetails->TaxonomyId!="" && !in_array(strtolower($billing_global_server_name), array('kirkeye','dso'))){
									$pdf->Cell(76,12,"ZZ".$physicianDetails->TaxonomyId,0,0,'');
									$prev_hcfa_arr[]="ZZ".$physicianDetails->TaxonomyId.' HCFA 33b --~~';
								}else{
									$pdf->Cell(76,12,$physicianDetails->TaxonomyId,0,0,'');
									$prev_hcfa_arr[]=$physicianDetails->TaxonomyId.' HCFA 33b --~~';
								}
								
							}else if(in_array(strtolower($billing_global_server_name), array('shoreline')) && (in_array($objInsuranceCoData->Payer_id_pro,$arr_BL_payers) || in_array($objInsuranceCoData->Payer_id,$arr_BL_payers))){
								$pdf->Cell(76,12,$physicianDetails->upin,0,0,'');
								$prev_hcfa_arr[]=$physicianDetails->upin.' HCFA 33b --~~';
							}else if(in_array(strtolower($billing_global_server_name), array('shoreline')) && (in_array($objInsuranceCoData->Payer_id,array('87726')) || in_array($objInsuranceCoData->Payer_id,array('87726')))){
								$pdf->Cell(76,12,$pos_fac_data_row->taxId,0,0,'');
								$prev_hcfa_arr[]=$pos_fac_data_row->taxId.' HCFA 33b --~~';
							}else{
								if(in_array(strtolower($billing_global_server_name), array('bennett','hattiesburg','henry'))){
									if((in_array($objInsuranceCoData->Payer_id_pro,array('9999')) || in_array($objInsuranceCoData->Payer_id,array('9999'))) && in_array(strtolower($billing_global_server_name), array('henry'))){
										if($physicianDetails->TaxonomyId!=""){
											$pdf->Cell(76,12,"ZZ".$physicianDetails->TaxonomyId,0,0,'');
											$prev_hcfa_arr[]="ZZ".$physicianDetails->TaxonomyId.' HCFA 33b --~~';
										}else{
											$pdf->Cell(76,12,$physicianDetails->TaxonomyId,0,0,'');
											$prev_hcfa_arr[]=$physicianDetails->TaxonomyId.' HCFA 33b --~~';
										}
										
									}else{
										$pdf->Cell(76,12,$insurance_Practice_Code_id,0,0,'');
										$prev_hcfa_arr[]=$insurance_Practice_Code_id.' HCFA 33b --~~';
									}
								}else if(in_array($objInsuranceCoData->Payer_id_pro,$arr_RRM_payers) || in_array($objInsuranceCoData->Payer_id,$arr_RRM_payers) || in_array($objInsuranceCoData->Payer_id_pro,$arr_DME_payers) || in_array($objInsuranceCoData->Payer_id,$arr_DME_payers)){
									$pdf->Cell(76,12,'',0,0,'');
								}else{
									$pdf->Cell(76,12,$insurance_Practice_Code_id,0,0,'');
									$prev_hcfa_arr[]=$insurance_Practice_Code_id.' HCFA 33b --~~';
								}
							}
							include "previous_hcfa.php";
						}
					}else{
						$dateOfService2="";
						$encounter_id="";
						$patient_id="";
						if($patientListData->patient_id>0){
							$dateOfService2 = $patientListData->date_of_service;
							$encounter_id = $patientListData->encounter_id;
							$patient_id = $patientListData->patient_id;
						}else{
							$qry = imw_query("select * from patient_charge_list where charge_list_id in ($val)");
							$chl_row = imw_fetch_array($qry);
							$dateOfService2 = $chl_row['date_of_service'];
							$encounter_id = $chl_row['encounter_id'];
							$patient_id = $chl_row['patient_id'];
							
							$patientDetails = $pat_data[$patient_id];
							$patientName = $patientDetails->lname;
							if($patientDetails->fname){
								$patientName .= ', '.ucfirst($patientDetails->fname);
							}
							if($patientDetails->mname){
								$patientName .= ' '.$patientDetails->mname;
							}
						}
						$getDate2 = explode("-",$dateOfService2);
						
						if($global_date_format == "d-m-Y")
						{ $dateOfService2 = $getDate2[2].'-'.$getDate2[1].'-'.$getDate2[0]; }else{
							$dateOfService2 = $getDate2[1].'-'.$getDate2[2].'-'.$getDate2[0];
						}
						
						$pagecount = $pdf->setSourceFile("../../library/html_to_pdf/predefined_forms/HCFA_FORM_WO.pdf");
						$pdf->setPrintHeader(false);
						$tplidx = $pdf->importPage(1);
						$pdf->AddPage();
						$pdf->useTemplate($tplidx,0,0);			
						$pdf->SetFont('Courier','',10);
						$page_count++;
						$pdf->Ln($top_marg);
						$pdf->Cell(5);		
						$pdf->Cell(30,1,"Patient Name:",12,0,'');
						$pdf->Cell(50,1,$patientName.'-'.$patient_id,12,0,'');
						$pdf->Ln(5);
						$pdf->Cell(5);		
						$pdf->Cell(30,1,"DOS:",12,0,'');
						$pdf->Cell(50,1,$dateOfService2,12,0,'');
						$pdf->Ln(5);
						$pdf->Cell(5);		
						$pdf->Cell(30,1,"Encounter:",12,0,'');
						$pdf->Cell(50,1,$encounter_id,12,0,'');
						$pdf->Ln(5);
						$pdf->Cell(5);		
						$pdf->Cell(50,1,"This encounter has been voided.",12,0,'');
						$pdf->Ln(5);
					}
				}
			}
		}
	}
	//---- CREATE PDF FILE FOR PRINTING --------
	if($fpdiCheck == true){
		$pdf->Output($newfile_hcfa_path,"F");
		if($only_show==""){	
			$chk_insurance=str_replace('tertiarySubmit','tertairySubmit',$chk_insurance);
			//--- Start query To change The Staus For Every Patient --------
			imw_query("update patient_charge_list set $chk_insurance = '1',hcfaStatus = '1' where charge_list_id in($chl_id_imp)");
			
			if($chld_ids!=""){
				$chld_whr_new=" charge_list_detail_id in($chld_ids)";
			}else{
				$chld_whr_new=" charge_list_id in($chl_id_imp)";
			}
			
			imw_query("update patient_charge_list_details set claim_status='1' where $chld_whr_new");
			
			//--- End query To change The Staus For Every Patient --------	
		}
		$final_path=str_replace($GLOBALS['fileroot'],$GLOBALS['webroot'],$newfile_hcfa_path);
		if($ar_ajax==""){
			if($newFile!=""){
				print '<script type="text/javascript">window.open(\''.$final_path.'\',"printHCFA","resizable=1,width=650,height=450");</script>';
			}else{
				echo $final_path;
			}
		}
		$msg = 'HCFA Form Successfully Printed.';		
	}
}
?>
