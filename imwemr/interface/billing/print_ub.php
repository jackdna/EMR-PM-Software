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
$fpdiCheck=false;
$time_ub=time();
$operator_id=$_SESSION['authId'];
$current_date=date('Y-m-d');
$newfile_ub_path=write_html('','ub_form.pdf');
if(isset($_REQUEST['chl_chk_box'])){
	if(count($_REQUEST['chl_chk_box'])>0){
		$validChargeListId=$_REQUEST['chl_chk_box'];
	}
}
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
		$chl_usr_arr[$row->primary_provider_id_for_reports]=$row->primary_provider_id_for_reports;
		
		$chl_pat_arr[$row->patient_id]=$row->patient_id;
		$chl_gro_id_arr[$row->gro_id]=$row->gro_id;
		$chl_enc_arr[$row->encounter_id]=$row->encounter_id;
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
	$qry=imw_query("select patient_charge_list_details.* from patient_charge_list_details 
					join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
					where patient_charge_list_details.del_status='0' and patient_charge_list_details.proc_selfpay!='1' 
					and cpt_fee_tbl.not_covered = '0' and patient_charge_list_details.charge_list_id in ($chl_id_imp) $chld_whr
					order by patient_charge_list_details.display_order,patient_charge_list_details.charge_list_detail_id");
	while($row=imw_fetch_array($qry)){
		$chld_arr[$row['charge_list_id']][$row['charge_list_detail_id']]=$row;
		$chld_procCode_arr[$row['procCode']]=$row['procCode'];
		$chld_rev_code_arr[$row['rev_code']]=$row['rev_code'];
		$chld_proc_code_essi_arr[$row['proc_code_essi']]=$row['proc_code_essi'];
		$chld_modifier_id_arr[$row['modifier_id1']]=$row['modifier_id1'];
		$chld_modifier_id_arr[$row['modifier_id2']]=$row['modifier_id2'];
		$chld_modifier_id_arr[$row['modifier_id3']]=$row['modifier_id3'];
		$chld_modifier_id_arr[$row['modifier_id4']]=$row['modifier_id4'];
	}
	//------------------------ Encounter Procedures Detail ------------------------//
	
	$chl_pat_imp=implode(',',$chl_pat_arr);
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
	//------------------------ Insurance Company Detail ------------------------//
	
	//------------------------ User Detail ------------------------//
	$chl_usr_imp=implode(',',$chl_usr_arr);
	$sql = imw_query("select * from users WHERE id in($chl_usr_imp)");
	while($row=imw_fetch_object($sql)){			
		$usr_data[$row->id]=$row;
	}		
	//------------------------	User Detail	------------------------//
	
	//------------------------ Patient Detail ------------------------//
	$sql = imw_query("select * from patient_data WHERE id in($chl_pat_imp)");
	while($row=imw_fetch_array($sql)){			
		$pat_data[$row['id']]=$row;
	}		
	//------------------------	Patient Detail	------------------------//
	
	//------------------------ Responsible Party Detail ------------------------//
	$sql = imw_query("select * from resp_party WHERE patient_id in($chl_pat_imp) and  fname<>''");
	while($row=imw_fetch_array($sql)){			
		$resp_data[$row['patient_id']]=$row;
	}		
	//------------------------	Responsible Party  Detail	------------------------//
	
	//------------------------ Group Detail ------------------------//
	$chl_gro_id_imp=implode(',',$chl_gro_id_arr);
	$sql = imw_query("select * from groups_new WHERE gro_id in($chl_gro_id_imp)");
	while($row=imw_fetch_array($sql)){			
		$group_data[$row['gro_id']]=$row;
	}		
	//------------------------	Group Detail	------------------------//
	
	//------------------------ Policies Detail ------------------------//
	$pol_info=imw_query("select Address1,Telephone,Zip,City,State,phone_ext,zip_ext from copay_policies");
	$pol_row=imw_fetch_array($pol_info);
	//------------------------ Policies Detail ------------------------//
	
	//------------------------ UB04 Margin Detail ------------------------//
	$group_margin_qry=imw_query("select top_margin,left_margin,top_line_margin from create_margins where margin_type='UB04'");
	$group_margin=imw_fetch_array($group_margin_qry);
	//------------------------ UB04 Margin Detail ------------------------//
	
	//------------------------ Payment Detail ------------------------//
	$chl_enc_imp=implode(',',$chl_enc_arr);
	$qry = imw_query("select patient_charges_detail_payment_info.paidForProc + patient_charges_detail_payment_info.overPayment as paidForProc,
					   patient_charges_detail_payment_info.paidDate,patient_chargesheet_payment_info.paymentClaims,patient_chargesheet_payment_info.encounter_id,
					   patient_charges_detail_payment_info.charge_list_detail_id,patient_chargesheet_payment_info.insProviderId,patient_chargesheet_payment_info.insCompany 
					   from patient_charges_detail_payment_info 
					   join patient_chargesheet_payment_info on patient_chargesheet_payment_info.payment_id = patient_charges_detail_payment_info.payment_id
					   where patient_chargesheet_payment_info.paid_by = 'Insurance'
					   and patient_chargesheet_payment_info.encounter_id in($chl_enc_imp) and patient_charges_detail_payment_info.deletePayment = '0'");
	while($row=imw_fetch_array($qry)){			
		$payment_data[$row['encounter_id']][$row['insProviderId']][]=$row;
	}				   
	//------------------------ Payment Detail ------------------------//
		
	//------------------------ CPT Detail ------------------------//
	$chld_procCode_imp=implode(',',$chld_procCode_arr);
	$sql = imw_query("select * from cpt_fee_tbl WHERE cpt_fee_id in($chld_procCode_imp)");
	while($row=imw_fetch_array($sql)){			
		$cpt_fee_data[$row['cpt_fee_id']]=$row;
	}		
	//------------------------	CPT Detail	------------------------//
	
	//------------------------ Rev Code Detail ------------------------//
	if(count($chld_rev_code_arr)>0){
		$chld_rev_code_imp=implode(',',$chld_rev_code_arr);
		$sql = imw_query("select * from revenue_code WHERE r_id in($chld_rev_code_imp)");
		while($row=imw_fetch_array($sql)){			
			$revenue_code_data[$row['r_id']]=$row;
		}	
	}
	//------------------------	Rev Code Detail	------------------------//
	
	//------------------------ Proc Code Detail ------------------------//
	if(count($chld_proc_code_essi_arr)>0){
		$chld_proc_code_essi_imp=implode(',',$chld_proc_code_essi_arr);
		$sql = imw_query("select * from proc_code_tbl WHERE proc_code_id in($chld_proc_code_essi_imp)");
		while($row=imw_fetch_array($sql)){			
			$proc_code_data[$row['proc_code_id']]=$row;
		}
	}
	//------------------------	Proc Code Detail	------------------------//
	
	//------------------------ Modifier Code Detail ------------------------//
	if(count($chld_modifier_id_arr)>0){
		$chld_modifier_id_imp=implode(',',$chld_modifier_id_arr);
		$sql = imw_query("select * from modifiers_tbl WHERE modifiers_id in($chld_modifier_id_imp)");
		while($row=imw_fetch_array($sql)){			
			$modifier_data[$row['modifiers_id']]=$row;
		}
	}
	//------------------------ Modifier Code Detail	------------------------//
	
	//------------------------ POS Facility ------------------------//
	$selQry = "select * from pos_facilityies_tbl order by pos_id ASC";
	$res = imw_query($selQry);
	while($row = imw_fetch_object($res)){
		$pos_fac_data_arr[$row->pos_facility_id]=$row;
	}
	//------------------------ POS Facility ------------------------//
	
	//------------------------ Facility Detail ------------------------//
	$selQry = "select * from facility where facility_type  = '1'";
	$res = imw_query($selQry);
	while($row = imw_fetch_object($res)){
		$fac_data_arr[]=$row;
	}
	//------------------------ Facility Detail ------------------------//
	
	foreach($validChargeListId as $charg_id){
		if($charg_id != ""){	
			$fpdiCheck=true;
			//--- Start get patient chare list ----	
			$patientListData = $chl_arr[$charg_id];
			$p_id=$patientListData->patient_id;
			$e_id=$patientListData->encounter_id;
			$charge_list_dos1=explode('-',$patientListData->date_of_service);
			$charge_list_dos=$charge_list_dos1[1].$charge_list_dos1[2].substr($charge_list_dos1[0],2);
			if($chk_insurance == ''){
				$chk_insurance = 'primarySubmit';
			}
			$enc_dos=$patientListData->date_of_service;
			$enc_icd10=$patientListData->enc_icd10;
			if($enc_icd10>0){
				$enc_icd10_ind="0";
			}else{
				$enc_icd10_ind="9";
			}
			
			$type_of_bill="831";	
			if(count($bill_type[$charg_id])>0){
				$type_of_bill=$bill_type[$charg_id];
			}else{
				if($post_bill_type!=""){
					$type_of_bill=$post_bill_type;
				}
			}
			
			$objpriInsuranceCoData = '';
			$objpriinsData = '';
			$claim_ctrl_no='';
			$claim_ctrl_up='';
			if($InsComp=="1"){
				$chk_insurance="primarySubmit";
				$claim_ctrl_no=$claim_ctrl[$charg_id];
				$insCheck="Primary";
				$claim_ctrl_up=" claim_ctrl_pri='".$claim_ctrl_no."'";
			}else if($InsComp=="2"){
				$chk_insurance="secondarySubmit";
				$claim_ctrl_no=$claim_ctrl[$charg_id];
				$insCheck="secondary";
				$claim_ctrl_up=" claim_ctrl_sec='".$claim_ctrl_no."'";
			}else if($InsComp=="3"){
				$chk_insurance="tertiarySubmit";
				$claim_ctrl_no=$claim_ctrl[$charg_id];
				$insCheck="tertiary";
				$claim_ctrl_up=" claim_ctrl_ter='".$claim_ctrl_no."'";
			}
			if($claim_ctrl_up!="" && $newFile==""){
				imw_query("update patient_charge_list set $claim_ctrl_up where charge_list_id='$charg_id'");
			}else{
				if($InsComp=="1"){
					$claim_ctrl_no=$patientListData->claim_ctrl_pri;
				}else if($InsComp=="2"){
					$claim_ctrl_no=$patientListData->claim_ctrl_sec;
				}else if($InsComp=="3"){
					$claim_ctrl_no=$patientListData->claim_ctrl_ter;
				}
			}
			if($type_of_bill=="831"){
				$claim_ctrl_no="";
			}
			if($chk_insurance == 'primarySubmit'){
				if($patientListData->primaryInsuranceCoId){
					$objpriInsuranceCoData_rmark = $ins_comp_data[$patientListData->primaryInsuranceCoId];
					$Ins_company_id_chk=$patientListData->primaryInsuranceCoId;
				}
			}
			else if($chk_insurance == 'secondarySubmit'){
				if($patientListData->secondaryInsuranceCoId){
					$objpriInsuranceCoData_rmark = $ins_comp_data[$patientListData->secondaryInsuranceCoId];
					$Ins_company_id_chk=$patientListData->secondaryInsuranceCoId;
				}
			}
			else{
				if($patientListData->tertiaryInsuranceCoId){
					$objpriInsuranceCoData_rmark = $ins_comp_data[$patientListData->tertiaryInsuranceCoId];
					$Ins_company_id_chk=$patientListData->tertiaryInsuranceCoId;
				}
			}

			if($patientListData->primaryInsuranceCoId){
				$objpriInsuranceCoData = $ins_comp_data[$patientListData->primaryInsuranceCoId];
				foreach($ins_case_data_arr[$patientListData->case_type_id] as $icd_key=>$icd_val){
					$icd_data=$ins_case_data_arr[$patientListData->case_type_id][$icd_key];
					if($icd_data->pid==$p_id && $icd_data->provider==$patientListData->primaryInsuranceCoId && $icd_data->type=='primary'){
						$effective_date=getDateFormatDB($icd_data->effective_date);
						$expiration_date=getDateFormatDB($icd_data->expiration_date);
						if($effective_date<=$enc_dos && ($icd_data->expiration_date=="0000-00-00 00:00:00" || $expiration_date>=$enc_dos)){
							$objpriinsData = $icd_data;
						}
					}
				}
			}
				
			if($patientListData->copayPaid == 0){
				$balAmt = $patientListData->totalBalance - $patientListData->copay;
			}
			else{
				$balAmt = $patientListData->totalBalance;
			}
			
			$post = array();
			$post['encounter_id'] = $patientListData->encounter_id;
			$post['patient_id'] = $patientListData->patient_id;
			$post['Ins_type'] = $insCheck;
			$post['Ins_company_id'] = $Ins_company_id_chk;
			$post['posted_amount'] = $balAmt;
			$post['operator_id'] = $_SESSION['authUserID'];
			$post['submited_date'] = date('Y-m-d');	
			if($only_show==""){	
				$insertId = AddRecords($post,'submited_record');
			}
			if($patientListData->enc_accept_assignment==2 && $chk_insurance == 'primarySubmit'){
				$fpdiCheck = false;
			}else{
				$insPaidAmountArr=array();
				$insPaidAmount="";
				if($chk_insurance=="secondarySubmit"){
					$pri_ins_pay=$patientListData->primaryInsuranceCoId;
					$piadRes = $payment_data[$e_id][$pri_ins_pay];
					$insPaidAmountArr = array();
					$priInsPaidAmountByCLDidArr = array();
					for($r=0;$r<count($piadRes);$r++){
						$paidForProc = $piadRes[$r]['paidForProc'];	
						if($piadRes[$r]['paymentClaims'] == 'Negative Payment'){
							$paidForProc = '-'.$paidForProc;
						}										
						$insPaidAmountArr[] = $paidForProc;
					}
					$insPaidAmount = array_sum($insPaidAmountArr);	
				}
					
				
				$pri_ins_rmark=$objpriInsuranceCoData_rmark->name;
				$pri_ins_add_rmark=$objpriInsuranceCoData_rmark->contact_address;
				$pri_cit_comp_remark=$objpriInsuranceCoData_rmark->City;
				$pri_stat_comp_rmark=$objpriInsuranceCoData_rmark->State;
				$pri_zip_comp_rmark1=$objpriInsuranceCoData_rmark->Zip;
				if($pri_zip_comp_rmark1){
					$pri_zip_comp_remark=', '.$pri_zip_comp_rmark1;
				}
				$pri_csz_comp_rmark=$pri_cit_comp_remark.' '.$pri_stat_comp_rmark.$pri_zip_comp_remark;
				$pri_ins=$objpriInsuranceCoData->name;
				$pri_group_id=$objpriInsuranceCoData->institutional_Code_id;
				$pri_cit_comp=$objpriInsuranceCoData->City;
				$pri_stat_comp=$objpriInsuranceCoData->State;
				$pri_zip_comp1=$objpriInsuranceCoData->Zip;
				if($pri_zip_comp1){
					$pri_zip_comp=', '.$pri_zip_comp1;
				}
				$pri_add_comp=$objpriInsuranceCoData->contact_address;
				$pri_csz_comp=$pri_cit_comp.' '.$pri_stat_comp.$pri_zip_comp;
				if($pri_ins){
					$pri_y='y';
				}
				$ins_dat_lname_pri=$objpriinsData->subscriber_lname;
				$ins_dat_mname_pri1=$objpriinsData->subscriber_mname;
				$ins_dat_fname_pri=$objpriinsData->subscriber_fname;
				$ins_dat_suffix_pri=$objpriinsData->subscriber_suffix;
				$ins_dat_rel_pri=$objpriinsData->subscriber_relationship;
				$ins_dat_pol_pri=preg_replace("/[^A-Za-z0-9]/","",$objpriinsData->policy_number);
				$ins_dat_plan_pri=$objpriinsData->plan_name;
				$ins_dat_group_pri=$objpriinsData->group_number;
				if($ins_dat_mname_pri1){
					$ins_dat_mname_pri=', '.$ins_dat_mname_pri1;
				}
				$ins_person_pri=trim(ucfirst($ins_dat_lname_pri).', '.ucfirst($ins_dat_fname_pri).' '.ucfirst($ins_dat_mname_pri1));
				
				$objsecInsuranceCoData = '';
				$objsecinsData = '';
				if($patientListData->secondaryInsuranceCoId){
					$objsecInsuranceCoData = $ins_comp_data[$patientListData->secondaryInsuranceCoId];
					foreach($ins_case_data_arr[$patientListData->case_type_id] as $icd_key=>$icd_val){
						$icd_data=$ins_case_data_arr[$patientListData->case_type_id][$icd_key];
						if($icd_data->pid==$p_id && $icd_data->provider==$patientListData->secondaryInsuranceCoId && $icd_data->type=='secondary'){
							$effective_date=getDateFormatDB($icd_data->effective_date);
							$expiration_date=getDateFormatDB($icd_data->expiration_date);
							if($effective_date<=$enc_dos && ($icd_data->expiration_date=="0000-00-00 00:00:00" || $expiration_date>=$enc_dos)){
								$objsecinsData = $icd_data;
							}
						}
					}
				}
				$sec_ins=$objsecInsuranceCoData->name;
				$sec_group_id=$objsecInsuranceCoData->institutional_Code_id;
				
				$ins_dat_lname_sec=$objsecinsData->subscriber_lname;
				
				$ins_dat_mname_sec1=$objsecinsData->subscriber_mname;
				$ins_dat_fname_sec=$objsecinsData->subscriber_fname;
				$ins_dat_suffix_sec=$objsecinsData->subscriber_suffix;
				$ins_dat_rel_sec=$objsecinsData->subscriber_relationship;
				$ins_dat_pol_sec=preg_replace("/[^A-Za-z0-9]/","",$objsecinsData->policy_number);
				$ins_dat_plan_sec=$objsecinsData->plan_name;
				$ins_dat_group_sec=$objsecinsData->group_number;
				if($ins_dat_mname_sec1){
					$ins_dat_mname_sec=', '.$ins_dat_mname_sec1;
				}
				$ins_person_sec="";
				if($sec_ins){
					$ins_person_sec=trim(ucfirst($ins_dat_lname_sec).', '.ucfirst($ins_dat_fname_sec).' '.ucfirst($ins_dat_mname_sec1));
				}
				$sec_y="";
				if($sec_ins){
					$sec_y='y';
				}
				$objterInsuranceCoData = '';
				$objterinsData='';
				if($patientListData->tertiaryInsuranceCoId){
					$objterInsuranceCoData = $ins_comp_data[$patientListData->tertiaryInsuranceCoId];
					foreach($ins_case_data_arr[$patientListData->case_type_id] as $icd_key=>$icd_val){
						$icd_data=$ins_case_data_arr[$patientListData->case_type_id][$icd_key];
						if($icd_data->pid==$p_id && $icd_data->provider==$patientListData->tertiaryInsuranceCoId && $icd_data->type=='tertiary'){
							$effective_date=getDateFormatDB($icd_data->effective_date);
							$expiration_date=getDateFormatDB($icd_data->expiration_date);
							if($effective_date<=$enc_dos && ($icd_data->expiration_date=="0000-00-00 00:00:00" || $expiration_date>=$enc_dos)){
								$objterinsData = $icd_data;
							}
						}
					}
				}
				$ter_ins=$objterInsuranceCoData->name;
				$ter_group_id=$objterInsuranceCoData->institutional_Code_id;
				if($InsComp=="1"){
					$group_insut_57=$pri_group_id;
				}else if($InsComp=="2"){
					$group_insut_57=$sec_group_id;
				}else{
					$group_insut_57=$ter_group_id;
				}
				
				$ins_dat_lname_ter=$objterinsData->subscriber_lname;
				$ins_dat_mname_ter1=$objterinsData->subscriber_mname;
				$ins_dat_fname_ter=$objterinsData->subscriber_fname;
				$ins_dat_suffix_ter=$objterinsData->subscriber_suffix;
				$ins_dat_rel_ter=$objterinsData->subscriber_relationship;
				$ins_dat_pol_ter=preg_replace("/[^A-Za-z0-9]/","",$objterinsData->policy_number);
				$ins_dat_plan_ter=$objterinsData->plan_name;
				$ins_dat_group_ter=$objterinsData->group_number;
				
				if($ins_dat_mname_ter1){
					$ins_dat_mname_ter=', '.$ins_dat_mname_ter1;
				}
				$ins_person_ter="";
				if($ter_ins){
					$ins_person_ter=trim(ucfirst($ins_dat_lname_ter).', '.ucfirst($ins_dat_fname_ter).' '.ucfirst($ins_dat_mname_ter1));
				}
				$ter_y="";
				if($ter_ins){
					$ter_y='y';
				}
				
				$rel_arr=array("Spouse"=>'01',"Self"=>'18',"Son"=>'19',"Daughter"=>'19',"Mother"=>'32',"Father"=>'33',"Guardian"=>'09',"POA"=>'G8',"Employee"=>20,"Other Relationship"=>'G8');
				
				 foreach($rel_arr as $key=>$val) {
					if(ucfirst($ins_dat_rel_pri)==$key){
						$ins_rel_final_pri=$val;
					}
				 }
				 $ins_rel_final_sec="";
				 foreach($rel_arr as $key1=>$val1) {
					if(ucfirst($ins_dat_rel_sec)==$key1){
						$ins_rel_final_sec=$val1;
					}
				 }
				$ins_rel_final_ter="";
				 foreach($rel_arr as $key2=>$val2) {
					if(ucfirst($ins_dat_rel_ter)==$key2){
						$ins_rel_final_ter=$val2;
					}
				 }
				 
				 //--- Start get provider details ----	
				 
				$objpriprovider='';
				if($patientListData->primaryProviderId){
					$objpriprovider = $usr_data[$patientListData->primaryProviderId];
					$pro_pri_fnam=$objpriprovider->fname;
					$pro_pri_lnam=$objpriprovider->lname;
					$pro_pri_npi=$objpriprovider->user_npi;
					$pro_pri_upin=$objpriprovider->upin;
					$pro_pri_taxaonomy=$objpriprovider->TaxonomyId;
					$pro_sx_physician=$objpriprovider->sx_physician;
				}
				
				$objsecprovider='';
				if($patientListData->secondaryProviderId){
					$objsecprovider = $usr_data[$patientListData->secondaryProviderId];
				}
				if(($patientListData->secondaryProviderId)=='0'){
					$pro_sec_fnam=$objpriprovider->fname;
					$pro_sec_lnam=$objpriprovider->lname;
					$pro_sec_npi=$objpriprovider->user_npi;
					$pro_sec_upin=$objpriprovider->upin;
				}else{
					$pro_sec_fnam=$objsecprovider->fname;
					$pro_sec_lnam=$objsecprovider->lname;
					$pro_sec_npi=$objsecprovider->user_npi;
					$pro_sec_upin=$objsecprovider->upin;
				}
				
				$objtriprovider='';
				if($patientListData->tertiaryProviderId){
					$objtriprovider = $usr_data[$patientListData->tertiaryProviderId];
					$pro_tri_fnam=$objtriprovider->fname;
					$pro_tri_lnam=$objtriprovider->lname;
					$pro_tri_npi=$objtriprovider->user_npi;
					$pro_tri_upin=$objtriprovider->upin;
				}
				
				if($patientListData->reff_phy_nr==0 && $patientListData->primaryProviderId!=$patientListData->primary_provider_id_for_reports && $patientListData->primary_provider_id_for_reports>0){
					$objpriprovider = $usr_data[$patientListData->primary_provider_id_for_reports];
					$pro_pri_fnam=$objpriprovider->fname;
					$pro_pri_lnam=$objpriprovider->lname;
					$pro_pri_npi=$objpriprovider->user_npi;
					$pro_pri_upin=$objpriprovider->upin;
					$pro_pri_taxaonomy=$objpriprovider->TaxonomyId;
					
					$pro_sec_fnam=$objpriprovider->fname;
					$pro_sec_lnam=$objpriprovider->lname;
					$pro_sec_npi=$objpriprovider->user_npi;
					$pro_sec_upin=$objpriprovider->upin;
					
				}
				 //--- Start get provider details ----	
				$cur_dat=date(''.phpDateFormat().'');
				$cur_dat=str_replace('2016','16',$cur_dat);
				list($dy,$dm,$dd)=explode('-',$patientListData->date_of_service);
				
				if(phpDateFormat() == "d-m-Y")
				{
					$dateofservice=$dd.$dm.substr($dy,2);
				}
				else
				{
					$dateofservice=$dm.$dd.substr($dy,2);
				}
				
				//--- Start get patient data details ----				
				$imw_row1=$pat_data[$p_id];
				$pat_nam=ucfirst($imw_row1['lname']).', '.ucfirst($imw_row1['fname']).' '.ucfirst($imw_row1['mname']);
				$pat_add1=$imw_row1['street'];
				$pat_add2=$imw_row1['street2'];
				$pat_city=$imw_row1['city'];
				$pat_state=$imw_row1['state'];
				$pat_zip=$imw_row1['postal_code'];
				list($y,$m,$d)=explode('-',$imw_row1['DOB']);
				
				if(phpDateFormat() == "d-m-Y")
				{
					$pat_dat=$d.$m.$y;
				}
				else
				{
					$pat_dat=$m.$d.$y;
				}
				
				$pat_sex=substr($imw_row1['sex'],0,1);
				
				 //--- Start get relation party data details ----	
						 
				$imw_info3=$resp_data[$p_id];
				if($imw_row3['fname']!=""){
					$imw_row3=imw_fetch_array($imw_info3);
					$res_nam=ucfirst($imw_row3['lname']).', '.ucfirst($imw_row3['fname']).' '.ucfirst($imw_row3['mname']);
					$res_add1=$imw_row3['address'];
					$res_add2=$imw_row3['address2'];
					if($imw_row3['zip']){
						$zipp=' '.$imw_row3['zip'];
					}
					$res_csz=$imw_row3['city'].', '.$imw_row3['state'].$zipp;
				}else{
					$res_nam=ucfirst($imw_row1['lname']).', '.ucfirst($imw_row1['fname']).' '.ucfirst($imw_row1['mname']);
					$res_add1=$imw_row1['street'];
					$res_add2=$imw_row1['street2'];
					if($imw_row1['postal_code']){
						$postt=' '.$imw_row1['postal_code'];
					}
					$res_csz=$imw_row1['city'].', '.$imw_row1['state'].$postt;
				}	
				
				$pol_add=$pol_row['Address1'];
				if($pol_row['phone_ext']<>"" && $pol_row['phone_ext']<>"0"){
					$phone_ext='( '.$pol_row['phone_ext'].' )';
				}
				$pol_Telephone=preg_replace('/[^0-9]/','',$pol_row['Telephone']);
				$pol_phone1 = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/","$1-$2-$3",$pol_Telephone);
				
				$pol_phone=$pol_phone1.' '.$phone_ext;
				
				if($pol_row['Zip']){
					$pol_zip=', '.$pol_row['Zip'];
				}
				if($pol_row['zip_ext']){
					$pol_zip_ext='-'.$pol_row['zip_ext'];
				}
				$pol_csz=$pol_row['City'].' '.$pol_row['State'].$pol_zip.$pol_zip_ext;
				
				$gro_id = $patientListData->gro_id;
				
				$group_row=$group_data[$gro_id];
				$grup_first_arr = explode(' ',$group_row['name']);
				$grup_first = $grup_first_arr[0];
				array_shift($grup_first_arr);
				$grup_last = join(' ',$grup_first_arr);
				$sc_group_sec_id=$group_row['sec_id'];
				$sc_eni=$group_row['group_Federal_EIN'];
				$sc_npi=$group_row['group_NPI'];
				$sc_nam=$group_row['name'];
				$pol_nam=$sc_nam;
				$sc_address=$group_row['group_Address1'];
				$group_Telephone=preg_replace('/[^0-9]/','',$group_row['group_Telephone']);
				$sc_phone = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/","$1-$2-$3",$group_Telephone);
				if($group_row['group_Zip']){
					$s_zip=', '.$group_row['group_Zip'];
				}
				if($group_row['zip_ext']){
					$zip_ext='-'.$group_row['zip_ext'];
				}
				$sc_csz=$group_row['group_City'].' '.$group_row['group_State'].$s_zip.$zip_ext;
				$chk_rem_grp_no="";
				if(in_array(strtolower($billing_global_server_name), array('clarisvision'))){
					$posFacilityId_arr = array();
					foreach($chld_arr[$charg_id] as $chld_key=>$chld_val){
						$posFacilityId_arr[] = $chld_arr[$charg_id][$chld_key]['posFacilityId'];
					}
					$pos_fac_id = array_unique($posFacilityId_arr);
					if($pos_fac_id[0]>0){
						$objfacility = $fac_data_arr[0];
						$chk_pos_home=$pos_fac_data_arr[$pos_fac_id[0]];
						$pol_nam=substr($chk_pos_home->facility_name,0,35);
						$pol_add = $chk_pos_home->pos_facility_address;
						$pol_csz = $chk_pos_home->pos_facility_city.', '.$chk_pos_home->pos_facility_state;
						$pol_csz .= ' '.$chk_pos_home->pos_facility_zip.'-'.$chk_pos_home->zip_ext;
						$pol_phone = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/","$1-$2-$3",$objfacility->phone);
					}
					$chk_rem_grp_no="yes";
				}
				if(in_array(strtolower($billing_global_server_name), array('patel'))){
					$posFacilityId_arr = array();
					foreach($chld_arr[$charg_id] as $chld_key=>$chld_val){
						$posFacilityId_arr[] = $chld_arr[$charg_id][$chld_key]['posFacilityId'];
					}
					$pos_fac_id = array_unique($posFacilityId_arr);
					if($pos_fac_id[0]>0){
						$objfacility = $fac_data_arr[0];
						$chk_pos_home=$pos_fac_data_arr[$pos_fac_id[0]];
						$sc_address = $chk_pos_home->pos_facility_address;
						$sc_csz = $chk_pos_home->pos_facility_city.', '.$chk_pos_home->pos_facility_state;
						$sc_csz .= ' '.$chk_pos_home->pos_facility_zip;
						if($chk_pos_home->zip_ext!=""){
							$sc_csz .= '-'.$chk_pos_home->zip_ext;
						}
						$sc_phone = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/","$1-$2-$3",$chk_pos_home->phone);
					}
				}else{
					if($group_row['rem_address1']!="" && ($group_row['rem_telephone']!="" || $chk_rem_grp_no!="") && $group_row['rem_zip']!="" && $group_row['rem_state']!=""){
						$sc_address=$group_row['rem_address1'];
						if($group_row['rem_telephone']!=""){
							$rem_group_Telephone=preg_replace('/[^0-9]/','',$group_row['rem_telephone']);
							$sc_phone = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/","$1-$2-$3",$rem_group_Telephone);
						}
						if($group_row['rem_zip']){
							$s_zip=', '.$group_row['rem_zip'];
						}
						if($group_row['rem_zip_ext']){
							$zip_ext='-'.$group_row['rem_zip_ext'];
						}
						$sc_csz=$group_row['rem_city'].' '.$group_row['rem_state'].$s_zip.$zip_ext;
					}
				}
				
				if($patientListData->tertiaryProviderId=='0'){
					$pro_tri_lnam_sc=$grup_last;
					$pro_tri_fnam_sc=$grup_first;
					$pro_tri_npi_sc=$sc_npi;
				}else{
					$pro_tri_fnam_sc=$objtriprovider->fname;
					$pro_tri_lnam_sc=$objtriprovider->lname;
					$pro_tri_npi_sc=$objtriprovider->user_npi;
					$pro_tri_upin_sc=$objtriprovider->upin;
					
					$lnam_sc=$grup_last;
					$fnam_sc=$grup_first;
					$npi_sc=$sc_npi;
				}
				$prev_ub_arr=array();
				if($print_paper_type=='WithoutPrintub'){
					$top_line_margin_arr=json_decode(html_entity_decode($group_margin['top_line_margin']),true);
					foreach ($top_line_margin_arr as $top_key => $top_value) {
						$top_line_margin_arr[$top_key]=(int)$top_value;
					}
					$pagecount = $pdf->setSourceFile("../../library/html_to_pdf/predefined_forms/ub_form_wo.pdf");
					$top_margin=0.1;
					if($group_margin['left_margin']>0){
						$left_margin=$group_margin['left_margin']-0.1;
					}else if($group_margin['left_margin']<0){
						$left_margin=$group_margin['left_margin'];
					}else{
						$left_margin=0.1;
					}
				}else{
					if(constant("global_ub_print_red")=="yes"){
						$pagecount = $pdf->setSourceFile("../../library/html_to_pdf/predefined_forms/ub_form_red.pdf");
						$top_margin=-9;
						$left_margin=-6.5;
						$wo_page_margion=0;
						$wo_page_margion1=1;
						$wo_page_margion2=0;
						$wo_page_margion3=0;
						$wo_page_margion5=0;
					}else{
						$pagecount = $pdf->setSourceFile("../../library/html_to_pdf/predefined_forms/ub_form.pdf");
						$top_margin=-5;
						$left_margin=-5;
						$wo_page_margion=0;
						$wo_page_margion1=0;
						$wo_page_margion2=0;
						$wo_page_margion3=0;
						$wo_page_margion5=0;
					}
				}
				$value_code=array();
				$value_price=array();
				$value_cent=array();
				foreach($chld_arr[$charg_id] as $chld_key=>$chld_val){
					$imw_row4=$chld_arr[$charg_id][$chld_key];
					if($imw_row4['rev_rate']!=""){
						$rev_rate=explode('/',$imw_row4['rev_rate']);
						$value_code[] = trim($rev_rate[0]);
						$chk_dot=explode('.',$rev_rate[1]);
						if($chk_dot[1]!=""){
							$value_price[] = trim($chk_dot[0]);
							if(str_replace('.','',$chk_dot[1])=="00"){
								$value_cent[] =  '';
							}else{
								$value_cent[] =  str_replace('.','',$chk_dot[1]);
							}
						}else{
							if($chk_dot[0]>0){
								$value_price[] = trim($chk_dot[0]);
								$value_cent[] =  ""; 
							}
						}
					}
				}
				
				//--- PATIENT ADMIN DATE -------
				$admitDate_exp = explode(" ",$patientListData->admit_date);
				$admitDate_exp_final = explode("-",$admitDate_exp[0]);
				$admitDate = $admitDate_exp_final[1].'-'.$admitDate_exp_final[2].'-'.substr($admitDate_exp_final[0],2);				
				if($admitDate == "00-00-00"){
					$admitDate = '';
				}
				$admitTime="";
				if(trim($admitDate_exp[1]) != "00:00:00"){
					$admitTime = substr(trim($admitDate_exp[1]),0,5);
				}
	
				//--- PATIENT DISPATCH DATE -------
				$dischDate_exp = explode(" ",$patientListData->disch_date);
				$dischDate_exp_final = explode("-",$dischDate_exp[0]);
				$dischDate = $dischDate_exp_final[1].'-'.$dischDate_exp_final[2].'-'.substr($dischDate_exp_final[0],2);
				if($dischDate == "00-00-00"){
					$dischDate = '';
				}
				$dischTime="";
				if(trim($dischDate_exp[1]) != "00:00:00"){
					$dischTime = substr(trim($dischDate_exp[1]),0,5);
				}
				$pdf->SetAutoPageBreak(false,0);
				$pdf->setPrintHeader(false);
				$tplidx = $pdf->importPage(1);
				$pdf->AddPage();
				$pdf->useTemplate($tplidx,0,0,210,295);
				$pdf->SetFont('helvetica','',8);
				$pdf->Ln($top_margin);
				//BOX NO. 1,2,3a
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_1_1']);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(0.1);
				$pdf->Cell(62.9,5,$sc_nam,0,0,'');
				$prev_ub_arr['ub_1a']=$sc_nam;
				if(in_array(strtolower($billing_global_server_name), array('palisades')) && strtolower($pri_ins)=="maryland medicaid"){
					$pdf->Cell(68,5,'',0,0,'');
					$prev_ub_arr['ub_2a']='';
				}else{
					$pdf->Cell(68,5,$pol_nam,0,0,'');
					$prev_ub_arr['ub_2a']=$pol_nam;
				}
				$pdf->Cell(0,5,$p_id,8,0,'');
				$prev_ub_arr['ub_3a']=$p_id;
				//BOX NO. 1,2,3b,4
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_1_2']);
				}else{
					$pdf->Ln(4);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(63,5,$sc_address,0,0,'');
				$prev_ub_arr['ub_1b']=$sc_address;
				if(in_array(strtolower($billing_global_server_name), array('palisades')) && strtolower($pri_ins)=="maryland medicaid"){
					$pdf->Cell(68,5,'',0,0,'');
				}else{
					$pdf->Cell(68,5,$pol_add,0,0,'');
					$prev_ub_arr['ub_2b']=$pol_add;
				}
				$pdf->Cell(60,5,$e_id,0,0,'');
				$prev_ub_arr['ub_3b']=$e_id;
				if(in_array(strtolower($billing_global_server_name), array('palisades')) && strtolower($pri_ins)=="maryland medicaid"){
					$pdf->Cell(0,5,'083',0,0,'');
					$prev_ub_arr['ub_4']='083';
				}else{
					if($type_of_bill=="837" && (in_array($objpriInsuranceCoData_rmark->Payer_id,array('11315')) || in_array($objpriInsuranceCoData_rmark->Payer_id_pro,array('11315')))){	
						$type_of_bill="0XX7";
					}
					$pdf->Cell(0,5,$type_of_bill,0,0,'');
					$prev_ub_arr['ub_4']=$type_of_bill;
				}
				//BOX NO. 1,2
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_1_3']);
				}else{
					$pdf->Ln(4);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(63,5,$sc_csz,0,0,'');
				$prev_ub_arr['ub_1c']=$sc_csz;
				if(in_array(strtolower($billing_global_server_name), array('palisades')) && strtolower($pri_ins)=="maryland medicaid"){
					$pdf->Cell(0,5,'',0,0,'');
				}else{
					$pdf->Cell(0,5,$pol_csz,0,0,'');
					$prev_ub_arr['ub_2c']=$pol_csz;
				}
				//BOX NO. 1,2,5,6
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_1_4']);
				}else{
					$pdf->Ln(5);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(63,5,$sc_phone,0,0,'');
				$prev_ub_arr['ub_1d']=$sc_phone;
				if(in_array(strtolower($billing_global_server_name), array('palisades')) && strtolower($pri_ins)=="maryland medicaid"){
					$pdf->Cell(61,5,'',0,0,'');
				}else{
					$pdf->Cell(61,5,$pol_phone,0,0,'');
					$prev_ub_arr['ub_2d']=$pol_phone;
				}
				$pdf->Cell(24,5,$sc_eni,0,0,'');
				$prev_ub_arr['ub_5']=$sc_eni;
				$pdf->Cell(17,5,$charge_list_dos,0,0,'');
				$prev_ub_arr['ub_6a']=$charge_list_dos;
				$pdf->Cell(0,5,$charge_list_dos,0,0,'');
				$prev_ub_arr['ub_6b']=$charge_list_dos;
				//BOX NO. 8a,9a
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_8_1']);
				}else{
					$pdf->Ln(5);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(103,5,'',0,0,'');
				$pdf->Cell(130,5,$pat_add1.' '.$pat_add2,0,0,'');
				$prev_ub_arr['ub_9a']=$pat_add1.' '.$pat_add2;
				$pat_before_margin=5+$wo_page_margion5;
				//BOX NO. 8b,9b,9c,9d,9e
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_8_2']);
				}else{
					$pdf->Ln($pat_before_margin);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(2,2,'',0,0,'');
				$pdf->Cell(73,2,$pat_nam,0,0,'');
				$prev_ub_arr['ub_8b']=$pat_nam;
				$pdf->Cell(83,2,$pat_city,0,0,'');
				$prev_ub_arr['ub_9b']=$pat_city;
				$pdf->Cell(10,2,$pat_state,0,0,'');
				$prev_ub_arr['ub_9c']=$pat_state;
				$pdf->Cell(30,2,$pat_zip,0,0,'');
				$prev_ub_arr['ub_9d']=$pat_zip;
				//BOX NO. 10 to 30
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_10_1']);
				}else{
					$pdf->Ln(9);
				}
				$box_14="3";
				if(in_array(strtolower($billing_global_server_name), array('essi','ocean'))){
					$admitTime=$dischTime=" 99";
				}
				$box_15="1";
				if(in_array(strtolower($billing_global_server_name), array('summiteye'))){
					$box_15="2";
				}
				if(in_array(strtolower($billing_global_server_name), array('austineeye'))){
					$box_14="9";
					if(!in_array($objpriInsuranceCoData_rmark->Payer_id,array('66006'))){
						$box_15="9";
					}
				}
				if($patientListData->acc_anes_unit>0){
					if(in_array($objpriInsuranceCoData_rmark->Payer_id,array('14163')) && in_array(strtolower($billing_global_server_name), array('patel'))){
						$admitDate=$admitTime=$box_14=$box_15=$dischTime="";
					}
					$pdf->Cell($left_margin);
					$pdf->Cell(22,1,$pat_dat,0,0,'');
					$pdf->Cell(6,1,$pat_sex,0,0,'');
					$pdf->Cell(14.2,1,$admitDate,0,0,'');
					$pdf->Cell(10,1,$admitTime,0,0,'');
					$pdf->Cell(7,1,$box_14,0,0,'');
					$pdf->Cell(5.1,1,$box_15,0,0,'');
					$pdf->Cell(9,1,$dischTime,0,0,'');
					$pdf->Cell(30,1,'01',0,0,'');
				}else{
					if($admitDate==""){
						$admitDate=$charge_list_dos1[1].'-'.$charge_list_dos1[2].'-'.substr($charge_list_dos1[0],2);
					}
					if($group_row['group_institution']>0 && in_array(strtolower($billing_global_server_name), array('swagelwootton'))){
						$admitDate=$admitTime=$box_14=$dischTime="";
					}
					if(in_array($objpriInsuranceCoData_rmark->Payer_id,array('14163')) && in_array(strtolower($billing_global_server_name), array('patel'))){
						$admitDate=$admitTime=$box_14=$box_15=$dischTime="";
					}
					$pdf->Cell($left_margin);
					$pdf->Cell(22,1,$pat_dat,0,0,'');
					$pdf->Cell(6,1,$pat_sex,0,0,'');
					$pdf->Cell(14.2,1,$admitDate,0,0,'');
					$pdf->Cell(10,1,$admitTime,0,0,'');
					$pdf->Cell(7,1,$box_14,0,0,'');
					$pdf->Cell(5.1,1,$box_15,0,0,'');
					$pdf->Cell(9,1,$dischTime,0,0,'');
					$pdf->Cell(30,1,'01',0,0,'');
				}
				$prev_ub_arr['ub_10']=$pat_dat;
				$prev_ub_arr['ub_11']=$pat_sex;
				$prev_ub_arr['ub_12']=$admitDate;
				$prev_ub_arr['ub_13']=$admitTime;
				$prev_ub_arr['ub_14']=$box_14;
				$prev_ub_arr['ub_15']=$box_15;
				$prev_ub_arr['ub_16']=$dischTime;
				$prev_ub_arr['ub_17']='01';
				//BOX NO. 38 to 41
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_38_1']);
				}else{
					$pdf->Ln(22);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(2,1,'',0,0,'');
				if(in_array(strtolower($billing_global_server_name), array('essi', 'sheepshead','mackool','palisades','azar','domi'))){
					$pdf->Cell(105,1,$pri_ins_rmark,0,0,'');
					$prev_ub_arr['ub_38a']=$pri_ins_rmark;
				}else{
					$pdf->Cell(105,1,$res_nam,0,0,'');
					$prev_ub_arr['ub_38a']=$res_nam;
				}
				$pdf->Cell(13,1,$value_code[0],0,0,'');
				$prev_ub_arr['ub_39a1']=$value_code[0];
				$strlen="";
				$spaces="";
				$strlen = strlen($value_price[0]);
				$spaces = 10 - $strlen;
				$stSp = '';
				for($s=0;$s<$spaces;$s++){
					$stSp .= ' ';
				}
				$pdf->Cell(12,1,$stSp.$value_price[0],0,0,'');
				$pdf->Cell(50,1,$value_cent[0],0,0,'');
				$prev_ub_arr['ub_39a2']=$stSp.$value_price[0];
				$prev_ub_arr['ub_39a3']=$value_cent[0];
				
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_38_2']);
				}else{
					$pdf->Ln(5);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(2,1,'',0,0,'');
				if(in_array(strtolower($billing_global_server_name), array('essi', 'sheepshead','mackool','palisades','azar','domi'))){
					$pdf->Cell(105,1,$pri_ins_add_rmark,0,0,'');
					$prev_ub_arr['ub_38b']=$pri_ins_add_rmark;
				}else{
					$pdf->Cell(105,1,$res_add1.' '.$res_add2,0,0,'');
					$prev_ub_arr['ub_38b']=$res_add1.' '.$res_add2;
				}
				$pdf->Cell(13,1,$value_code[1],0,0,'');
				$prev_ub_arr['ub_39b1']=$value_code[1];
				$strlen="";
				$spaces="";
				$strlen = strlen($value_price[1]);
				$spaces = 10 - $strlen;
				$stSp = '';
				for($s=0;$s<$spaces;$s++){
					$stSp .= ' ';
				}
				$pdf->Cell(12,1,$stSp.$value_price[1],0,0,'');
				$prev_ub_arr['ub_39b2']=$stSp.$value_price[1];
				$pdf->Cell(50,1,$value_cent[1],0,0,'');
				$prev_ub_arr['ub_39b3']=$value_cent[1];
				
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_38_3']);
				}else{
					$pdf->Ln(4);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(2,1,'',0,0,'');
				if(in_array(strtolower($billing_global_server_name), array('essi', 'sheepshead','mackool','palisades','azar','domi'))){
					$pdf->Cell(105,1,$pri_csz_comp_rmark,0,0,'');
					$prev_ub_arr['ub_38c']=$pri_csz_comp_rmark;
				}else{
					$pdf->Cell(105,1,$res_csz,0,0,'');
					$prev_ub_arr['ub_38c']=$res_csz;
				}
				$pdf->Cell(13,1,$value_code[2],0,0,'');
				$prev_ub_arr['ub_39c1']=$value_code[2];
				$strlen="";
				$spaces="";
				$strlen = strlen($value_price[2]);
				$spaces = 10 - $strlen;
				$stSp = '';
				for($s=0;$s<$spaces;$s++){
					$stSp .= ' ';
				}
				$pdf->Cell(12,1,$stSp.$value_price[2],0,0,'');
				$prev_ub_arr['ub_39c2']=$stSp.$value_price[2];
				$pdf->Cell(50,1,$value_cent[2],0,0,'');
				$prev_ub_arr['ub_39c3']=$value_cent[2];
				
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_38_4']);
				}else{
					$pdf->Ln(4);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(2,1,'',0,0,'');
				$pdf->Cell(105,1,"",0,0,'');
				$pdf->Cell(14,1,$value_code[3],0,0,'');
				$prev_ub_arr['ub_39d1']=$value_code[3];
				$strlen="";
				$spaces="";
				$strlen = strlen($value_price[3]);
				$spaces = 10 - $strlen;
				$stSp = '';
				for($s=0;$s<$spaces;$s++){
					$stSp .= ' ';
				}
				$pdf->Cell(12,1,$stSp.$value_price[3],0,0,'');
				$prev_ub_arr['ub_39d2']=$stSp.$value_price[3];
				$pdf->Cell(50,1,$value_cent[3],0,0,'');
				$prev_ub_arr['ub_39d3']=$value_cent[3];
				
				$cod_len=6+$wo_page_margion;
				$pdf->Ln($cod_len);
				//BOX NO. 42 to 49
				//--- Start get all charge list details ----	
				$cpt4_code_arr=array();
				$notes_arr=array();
				$count_pro=0;
				$tot_amt=0;
				$dignosis = array();
				$proc_code_essi_arr = array();
				$num_charge_details=count($chld_arr[$charg_id]);
				$top_name_var=1;
				foreach($chld_arr[$charg_id] as $chld_key=>$chld_val){
					$imw_row4=$chld_arr[$charg_id][$chld_key];	
					$proc_code_essi="";
					$rev_cod=$imw_row4['rev_code'];
					$proc_cod=$imw_row4['procCode'];
					$proc_code_essi=$imw_row4['proc_code_essi'];
					$modifier_id=$imw_row4['modifier_id1'];
					$modifier_id2=$imw_row4['modifier_id2'];
					$modifier_id3=$imw_row4['modifier_id3'];
					$modifier_id4=$imw_row4['modifier_id4'];
					$totalAmount=$imw_row4['totalAmount'];
					$sec_due_arr[$charg_id][]=$imw_row4['sec_due'];
					
					if($imw_row4['posFacilityId']>0){
						$posFacilityId=$imw_row4['posFacilityId'];
					}
					
					for($f=1;$f<=12;$f++){
						if($imw_row4['diagnosis_id'.$f]){
							$dignosis[]=$imw_row4['diagnosis_id'.$f];
						}
					}
					
					if($imw_row4['notes']!=""){
						$notes_arr[]=$imw_row4['notes'];
					}
					
					$cpt4_code=$cpt_fee_data[$proc_cod]['cpt4_code'];
					$cpt_desc1=$cpt_fee_data[$proc_cod]['cpt_desc'];
					$only_ndc_num=explode("/",$cpt_fee_data[$proc_cod]['cpt_comments']);
					if(strlen($cpt_desc1)>35){
						$cpt_desc=substr($cpt_desc1,0,32).'...';
					}else{
						$cpt_desc=$cpt_desc1;
					}	
					
					$r_code=$revenue_code_data[$rev_cod]['r_code'];
					$proc_code_new = $proc_code_data[$proc_code_essi]['proc_code'];
					
					if($proc_code_new){
						$proc_code_essi_arr[]=$proc_code_new;		
						$dateofservice_arr[]=$dateofservice;
					}	
					$modifier_code_arr=array();
					$modifier_code_arr[$modifier_data[$modifier_id]['modifier_code']]=$modifier_data[$modifier_id]['modifier_code'];
					$modifier_code_arr[$modifier_data[$modifier_id2]['modifier_code']]=$modifier_data[$modifier_id2]['modifier_code'];
					$modifier_code_arr[$modifier_data[$modifier_id3]['modifier_code']]=$modifier_data[$modifier_id3]['modifier_code'];
					$modifier_code_arr[$modifier_data[$modifier_id4]['modifier_code']]=$modifier_data[$modifier_id4]['modifier_code'];
					$modifier_code=implode(',',$modifier_code_arr);
					
					if($count_pro<22){
						if($print_paper_type=='WithoutPrintub'){
							$pdf->Ln($top_line_margin_arr['top_42_'.$top_name_var]);
						}else{
							$pdf->Ln(4);
						}
						if($only_ndc_num[0]!=""){
							if(strlen($cpt_desc)>22){
								$cpt_desc=substr($cpt_desc,0,20).'...';
							}
							if($cpt_fee_data[$proc_cod]['unit_of_measure']!="" && $cpt_fee_data[$proc_cod]['measurement']!=""){
								$cpt_desc="N4".$only_ndc_num[0].' '.$cpt_fee_data[$proc_cod]['unit_of_measure'].$cpt_fee_data[$proc_cod]['measurement'];
							}else{
								$cpt_desc="N4".$only_ndc_num[0].' '.'UN'.unit_format($imw_row4['units']);
							}
						}
						$pdf->Cell($left_margin);
						$pdf->Cell(10,1,$r_code,0,0,'');
						$pdf->Cell(65,1,$cpt_desc,0,0,'');
						$pdf->Cell(38,1,$cpt4_code.' '.$modifier_code,0,0,'');
						$pdf->Cell(18,1,$dateofservice,0,0,'');
						$pdf->Cell(20,1,$imw_row4['units'],0,0,'');
						$arr = preg_split('/./',$totalAmount);
						$strlen = strlen(substr($totalAmount,0,-3));
						$spaces = 10 - $strlen;
						$stSp = '';
						for($s=0;$s<$spaces;$s++){
							$stSp .= ' ';
						}
						$pdf->Cell(15,1,$stSp.substr($totalAmount,0,-3),0,0,'');
						$pdf->Cell(25,1,substr($totalAmount,-2),0,0,'');
						$tot_amt+=$totalAmount;
						$count_pro++;
						$prev_ub_arr['ub_42a'.$top_name_var]=$r_code;
						$prev_ub_arr['ub_43a'.$top_name_var]=$cpt_desc;
						$prev_ub_arr['ub_44a'.$top_name_var]=$cpt4_code.' '.$modifier_code;
						$prev_ub_arr['ub_45a'.$top_name_var]=$dateofservice;
						$prev_ub_arr['ub_46a'.$top_name_var]=$imw_row4['units'];
						$prev_ub_arr['ub_47a'.$top_name_var]=$stSp.substr($totalAmount,0,-3);
						$prev_ub_arr['ub_47b'.$top_name_var]=substr($totalAmount,-2);
						$top_name_var=$top_name_var+1;
					}	
					
				}
				for($i=$num_charge_details;$i<23;$i++){
					$pdf->Ln(4);
					$pdf->Cell(176,1,'',0,0,'');
				}
				$start_page=1;
				$end_page=1;
				$before_tot_price_len=10+$wo_page_margion1;
				//BOX NO. 42(23) to 49(23)
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_42_23']);
				}else{
					$pdf->Ln($before_tot_price_len);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(22,1,'001',0,0,'');
				$pdf->Cell(15,1,$start_page,0,0,'');
				$pdf->Cell(73,1,$end_page,0,0,'');
				$pdf->Cell(36,1,$cur_dat,0,0,'');
				$strlen = strlen(substr(number_format($tot_amt,2),0,-3));
				$spaces = (14 - $strlen);
				$stSp = '';
				for($s=0;$s<$spaces;$s++){
					$stSp .= ' ';
				}
				$pdf->Cell(19,1,$stSp.substr(number_format($tot_amt,2),0,-3),0,0,'');
				$pdf->Cell(15,1,substr(number_format($tot_amt,2),-2),0,0,'');
				$pdf->Cell(15,1,'0',0,0,'');
				$prev_ub_arr['ub_42a23']='001';
				$prev_ub_arr['ub_43a23']=$start_page;
				$prev_ub_arr['ub_42b23']=$end_page;
				$prev_ub_arr['ub_45a23']=$cur_dat;
				$prev_ub_arr['ub_47a23']=$stSp.substr(number_format($tot_amt,2),0,-3);
				$prev_ub_arr['ub_47b23']=substr(number_format($tot_amt,2),-2);
				
				//BOX NO. 56
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_56_1']);
				}else{
					$pdf->Ln(4);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(170,1,'',0,0,'');
				$pdf->Cell(30,2,$sc_npi,0,0,'');
				$prev_ub_arr['ub_56']=$sc_npi;
				$after_ins_len=6+$wo_page_margion2;
				//BOX NO. 50a to 57a
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_50_1']);
				}else{
					$pdf->Ln($after_ins_len);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(60,1,$pri_ins,0,0,'');
				$prev_ub_arr['ub_50a']=$pri_ins;
				if(in_array(strtolower($billing_global_server_name), array('mackool'))){
					$pdf->Cell(34,1,'',0,0,'');
				}else{
					$pdf->Cell(34,1,$pri_group_id,0,0,'');
					$prev_ub_arr['ub_51a']=$pri_group_id;
				}
				$pdf->Cell(8,-1,$pri_y,0,0,'');
				$prev_ub_arr['ub_52a']=$pri_y;
				if($chk_insurance=="secondarySubmit"){
					$pdf->Cell(2,-1,$pri_y,0,0,'');
					$prev_ub_arr['ub_53a']=$pri_y;
					$strlen = strlen(substr(number_format($insPaidAmount,2),0,-3));
					$spaces = (18 - $strlen);
					$stSp = '';
					for($s=0;$s<$spaces;$s++){
						$stSp .= ' ';
					}
					$pdf->Cell(21,-1,$stSp.substr(number_format($insPaidAmount,2),0,-3),0,0,'');
					$pdf->Cell(44,-1,substr(number_format($insPaidAmount,2),-2),0,0,'');
					$prev_ub_arr['ub_54a1']=$stSp.substr(number_format($insPaidAmount,2),0,-3);
					$prev_ub_arr['ub_54a2']=substr(number_format($insPaidAmount,2),-2);
				}else{
					$pdf->Cell(65,-1,$pri_y,0,0,'');
					$prev_ub_arr['ub_53a']=$pri_y;
				}
				$pdf->Cell(10,-1,$group_insut_57,0,0,'');
				$prev_ub_arr['ub_57a']=$group_insut_57;
				//BOX NO. 50b to 57b
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_50_2']);
				}else{
					$pdf->Ln(4);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(60,1,$sec_ins,0,0,'');
				$prev_ub_arr['ub_50b']=$sec_ins;
				if(in_array(strtolower($billing_global_server_name), array('mackool'))){
					$pdf->Cell(34,1,'',0,0,'');
				}else{
					$pdf->Cell(34,1,$sec_group_id,0,0,'');
					$prev_ub_arr['ub_51b']=$sec_group_id;
				}
				$pdf->Cell(8,1,$sec_y,0,0,'');
				$pdf->Cell(29,1,$sec_y,0,0,'');
				$prev_ub_arr['ub_52b']=$sec_y;
				$prev_ub_arr['ub_53b']=$sec_y;
				if($chk_insurance=="secondarySubmit" && in_array(strtolower($billing_global_server_name), array('seaside'))){
					$strlen = strlen(substr(number_format(array_sum($sec_due_arr[$charg_id]),2),0,-3));
					$spaces = (18 - $strlen);
					$stSp = '';
					for($s=0;$s<$spaces;$s++){
						$stSp .= ' ';
					}
					$pdf->Cell(21,-1,$stSp.substr(number_format(array_sum($sec_due_arr[$charg_id]),2),0,-3),0,0,'');
					$pdf->Cell(44,-1,substr(number_format(array_sum($sec_due_arr[$charg_id]),2),-2),0,0,'');
					$prev_ub_arr['ub_54b1']=$stSp.substr(number_format(array_sum($sec_due_arr[$charg_id]),2),0,-3);
					$prev_ub_arr['ub_54b2']=substr(number_format(array_sum($sec_due_arr[$charg_id]),2),-2);
				}
				//BOX NO. 50c to 57c
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_50_3']);
				}else{
					$pdf->Ln(4);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(60,1,$ter_ins,0,0,'');
				$prev_ub_arr['ub_50c']=$ter_ins;
				if(in_array(strtolower($billing_global_server_name), array('mackool'))){
					$pdf->Cell(34,1,'',0,0,'');
				}else{
					$pdf->Cell(34,1,$ter_group_id,0,0,'');
					$prev_ub_arr['ub_51c']=$ter_group_id;
				}
				$pdf->Cell(8,1,$ter_y,0,0,'');
				$pdf->Cell(8,1,$ter_y,0,0,'');
				$prev_ub_arr['ub_52c']=$ter_y;
				$prev_ub_arr['ub_53c']=$ter_y;
				//BOX NO. 58a to 62a
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_58_1']);
				}else{
					$pdf->Ln(9);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(65,1,$ins_person_pri,0,0,'');
				$pdf->Cell(8,1,$ins_rel_final_pri,0,0,'');
				$pdf->Cell(50,1,$ins_dat_pol_pri,0,0,'');
				$pdf->Cell(40,1,$ins_dat_plan_pri,0,0,'');
				$pdf->Cell(30,1,$ins_dat_group_pri,0,0,'');
				$prev_ub_arr['ub_58a']=$ins_person_pri;
				$prev_ub_arr['ub_59a']=$ins_rel_final_pri;
				$prev_ub_arr['ub_60a']=$ins_dat_pol_pri;
				$prev_ub_arr['ub_61a']=$ins_dat_plan_pri;
				$prev_ub_arr['ub_62a']=$ins_dat_group_pri;
				//BOX NO. 58b to 62b
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_58_2']);
				}else{
					$pdf->Ln(4);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(65,1,$ins_person_sec,0,0,'');
				$pdf->Cell(8,1,$ins_rel_final_sec,0,0,'');
				$pdf->Cell(50,1,$ins_dat_pol_sec,0,0,'');
				$pdf->Cell(40,1,$ins_dat_plan_sec,0,0,'');
				$pdf->Cell(30,1,$ins_dat_group_sec,0,0,'');
				$prev_ub_arr['ub_58b']=$ins_person_sec;
				$prev_ub_arr['ub_59b']=$ins_rel_final_sec;
				$prev_ub_arr['ub_60b']=$ins_dat_pol_sec;
				$prev_ub_arr['ub_61b']=$ins_dat_plan_sec;
				$prev_ub_arr['ub_62b']=$ins_dat_group_sec;
				//BOX NO. 58c to 62c
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_58_3']);
				}else{
					$pdf->Ln(4);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(65,1,$ins_person_ter,0,0,'');
				$pdf->Cell(8,1,$ins_rel_final_ter,0,0,'');
				$pdf->Cell(50,1,$ins_dat_pol_ter,0,0,'');
				$pdf->Cell(40,1,$ins_dat_plan_ter,0,0,'');
				$pdf->Cell(30,1,$ins_dat_group_ter,0,0,'');
				$prev_ub_arr['ub_58c']=$ins_person_ter;
				$prev_ub_arr['ub_59c']=$ins_rel_final_ter;
				$prev_ub_arr['ub_60c']=$ins_dat_pol_ter;
				$prev_ub_arr['ub_61c']=$ins_dat_plan_ter;
				$prev_ub_arr['ub_62c']=$ins_dat_group_ter;
				//BOX NO. 63a
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_63_1']);
				}else{
					$pdf->Ln(10);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(80,1,$patientListData->auth_no,0,0,'');
				$pdf->Cell(65,1,$claim_ctrl_no,0,0,'');
				$prev_ub_arr['ub_63a']=$patientListData->auth_no;
				$prev_ub_arr['ub_64a']=$claim_ctrl_no;
				//BOX NO. 63b
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_63_2']);
				}else{
					$pdf->Ln(4);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(80,1,'',0,0,'');
				//BOX NO. 63c
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_63_3']);
				}else{
					$pdf->Ln(4);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(80,1,'',0,0,'');
				//BOX NO. 66 to 68
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_66_1']);
				}else{
					$pdf->Ln(5);
				}
				$pdf->Cell($left_margin);
				//--- Start Dx ---
				if(in_array(strtolower($billing_global_server_name), array('lodenvision','azar'))){
					$dignosis = array_values(array_unique($dignosis));
				}
				$pdf->Cell(3,1,'',0,0,'');
				
				$pdf->Cell(20,1,$dignosis[0],0,0,'');
				$pdf->Cell(20,1,$dignosis[1],0,0,'');
				$pdf->Cell(20,1,$dignosis[2],0,0,'');
				$pdf->Cell(20,1,$dignosis[3],0,0,'');
				$pdf->Cell(20,1,$dignosis[4],0,0,'');
				$pdf->Cell(20,1,$dignosis[5],0,0,'');
				$pdf->Cell(20,1,$dignosis[6],0,0,'');
				$pdf->Cell(20,1,$dignosis[7],0,0,'');
				$pdf->Cell(20,1,$dignosis[8],0,0,'');
				
				$prev_ub_arr['ub_67']=$dignosis[0];
				$prev_ub_arr['ub_67a']=$dignosis[1];
				$prev_ub_arr['ub_67b']=$dignosis[2];
				$prev_ub_arr['ub_67c']=$dignosis[3];
				$prev_ub_arr['ub_67d']=$dignosis[4];
				$prev_ub_arr['ub_67e']=$dignosis[5];
				$prev_ub_arr['ub_67f']=$dignosis[6];
				$prev_ub_arr['ub_67g']=$dignosis[7];
				$prev_ub_arr['ub_67h']=$dignosis[8];
				
				//BOX NO. 66 to 68
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_66_2']);
				}else{
					$pdf->Ln(4);
				}
				$pdf->Cell($left_margin);
				if($print_paper_type=='WithoutPrintub' && in_array(strtolower($billing_global_server_name), array('patel','seaside'))){
					$pdf->Cell(-4);
				}else{
					$pdf->Cell(-2);
				}
				$pdf->Cell(3,1,$enc_icd10_ind,0,0,'');
				$pdf->Cell(2,1,'',0,0,'');
				$pdf->Cell(20,1,$dignosis[9],0,0,'');
				$pdf->Cell(20,1,$dignosis[10],0,0,'');
				$pdf->Cell(20,1,$dignosis[11],0,0,'');
				$pdf->Cell(20,1,$dignosis[12],0,0,'');
				$pdf->Cell(20,1,$dignosis[13],0,0,'');
				$pdf->Cell(20,1,$dignosis[14],0,0,'');
				$pdf->Cell(20,1,$dignosis[15],0,0,'');
				$pdf->Cell(20,1,$dignosis[16],0,0,'');
				$pdf->Cell(20,1,$dignosis[17],0,0,'');
				$prev_ub_arr['ub_66']=$enc_icd10_ind;
				$prev_ub_arr['ub_67i']=$dignosis[9];
				$prev_ub_arr['ub_67j']=$dignosis[10];
				$prev_ub_arr['ub_67k']=$dignosis[11];
				$prev_ub_arr['ub_67l']=$dignosis[12];
				$prev_ub_arr['ub_67m']=$dignosis[13];
				$prev_ub_arr['ub_67n']=$dignosis[14];
				$prev_ub_arr['ub_67o']=$dignosis[15];
				$prev_ub_arr['ub_67p']=$dignosis[16];
				$prev_ub_arr['ub_67q']=$dignosis[17];
				//--- End Dx ---
				$pos_facility_name="";
				if($show_facility_in_ub04=="1" && $group_row['group_institution']>0){
					if($posFacilityId == '' || $posFacilityId == '0'){
						$posFacilityId = $imw_row1['default_facility'];
					}
					$posFacilityDetail = $pos_fac_data_arr[$posFacilityId];
					$pos_facility_name = $posFacilityDetail->facility_name;
					$pos_facility_npi = $posFacilityDetail->npiNumber;
					
					$fac_first_arr = explode(' ',$pos_facility_name);
					$fac_first_nam = $fac_first_arr[0];
					array_shift($fac_first_arr);
					$fac_last_nam = join(' ',$fac_first_arr);
				}
				if($pos_facility_name!="" && !in_array(strtolower($billing_global_server_name), array('mackool','utaheye','sheepshead'))){
					$box_76_npi=$pos_facility_npi;
					$box_76_upin='';
					$box_76_lname=substr(ucwords(strtolower($fac_last_nam)),0,26);
					$box_76_fname=substr(ucwords(strtolower($fac_first_nam)),0,20);
					
					$box_77_npi=$pro_pri_npi;
					$box_77_upin=$pro_pri_upin;
					$box_77_lname=substr(ucwords(strtolower($pro_pri_lnam)),0,26);
					$box_77_fname=substr(ucwords(strtolower($pro_pri_fnam)),0,20);
					
					$box_78_npi=$pro_sec_npi;
					$box_78_upin=$pro_sec_upin;
					$box_78_lname=substr(ucwords(strtolower($pro_sec_lnam)),0,26);
					$box_78_fname=substr(ucwords(strtolower($pro_sec_fnam)),0,20);
					
					if($patientListData->tertiaryProviderId=='0'){
						$box_79_npi=$pro_tri_npi_sc;
						$box_79_upin='';
						$box_79_lname=substr(ucwords(strtolower($pro_tri_lnam_sc)),0,26);
						$box_79_fname=substr(ucwords(strtolower($pro_tri_fnam_sc)),0,20);
					}else{
						$box_79_npi=$npi_sc;
						$box_79_upin='';
						$box_79_lname=substr(ucwords(strtolower($lnam_sc)),0,26);
						$box_79_fname=substr(ucwords(strtolower($fnam_sc)),0,20);
					}
				}else{
					$box_76_npi=$pro_pri_npi;
					$box_76_upin=$pro_pri_upin;
					$box_76_lname=substr(ucwords(strtolower($pro_pri_lnam)),0,26);
					$box_76_fname=substr(ucwords(strtolower($pro_pri_fnam)),0,20);
					
					$box_77_npi=$pro_sec_npi;
					$box_77_upin=$pro_sec_upin;
					$box_77_lname=substr(ucwords(strtolower($pro_sec_lnam)),0,26);
					$box_77_fname=substr(ucwords(strtolower($pro_sec_fnam)),0,20);
					
					$box_78_npi=$pro_tri_npi_sc;
					$box_78_upin=$pro_tri_upin_sc;
					$box_78_lname=substr(ucwords(strtolower($pro_tri_lnam_sc)),0,26);
					$box_78_fname=substr(ucwords(strtolower($pro_tri_fnam_sc)),0,20);
					
					$box_79_npi=$npi_sc;
					$box_79_upin='';
					$box_79_lname=substr(ucwords(strtolower($lnam_sc)),0,26);
					$box_79_fname=substr(ucwords(strtolower($fnam_sc)),0,20);
					
				}
				
				//BOX NO.  76
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_74_1']);
				}else{
					$pdf->Ln(10);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(143,1,'',0,0,'');
				
				$pdf->Cell(38,1,$box_76_npi,0,0,'');
				$pdf->Cell(30,1,$box_76_upin,0,0,'');
				$prev_ub_arr['ub_76a']=$box_76_npi;
				$prev_ub_arr['ub_76c']=$box_76_upin;
				
				//BOX NO. 74 to 76
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_74_2']);
				}else{
					$pdf->Ln(4);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(20,1,$proc_code_essi_arr[0],0,0,'');
				$pdf->Cell(17,1,$dateofservice_arr[0],0,0,'');
				$pdf->Cell(20,1,$proc_code_essi_arr[1],0,'');
				$pdf->Cell(17,1,$dateofservice_arr[1],0,0,'');
				$pdf->Cell(20,1,$proc_code_essi_arr[2],0,0,'');
				$pdf->Cell(36,1,$dateofservice_arr[2],0,0,'');
				
				$prev_ub_arr['ub_74f1']=$proc_code_essi_arr[0];
				$prev_ub_arr['ub_74f2']=$dateofservice_arr[0];
				$prev_ub_arr['ub_74a1']=$proc_code_essi_arr[1];
				$prev_ub_arr['ub_74a2']=$dateofservice_arr[1];
				$prev_ub_arr['ub_74b1']=$proc_code_essi_arr[2];
				$prev_ub_arr['ub_74b2']=$dateofservice_arr[2];
				
				$pdf->Cell(45,1,$box_76_lname,0,0,'');
				$pdf->Cell(30,1,$box_76_fname,0,0,'');
				$prev_ub_arr['ub_76d']=$box_76_lname;
				$prev_ub_arr['ub_76e']=$box_76_lname;
				
				//BOX NO. 77
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_74_3']);
				}else{
					$pdf->Ln(5);
				}
				$show_dn="";
				if(in_array(strtolower($billing_global_server_name), array('sheepshead','patel'))){
					$show_dn="DN";
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(143,1,'',0,0,'');
				$pdf->Cell(30.5,1,$box_77_npi,0,0,'');
				$pdf->Cell(7.5,1,$show_dn,0,0,'');
				$pdf->Cell(30,1,$box_77_upin,0,0,'');
				$prev_ub_arr['ub_77a']=$box_77_npi;
				$prev_ub_arr['ub_77b']=$show_dn;
				$prev_ub_arr['ub_77c']=$box_77_upin;
			
				//BOX NO. 74 to 77
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_74_4']);
				}else{
					$pdf->Ln(4);
				}
				$pdf->Cell($left_margin);
				$pdf->Cell(20,1,$proc_code_essi_arr[3],0,0,'');
				$pdf->Cell(17,1,$dateofservice_arr[3],0,0,'');
				$pdf->Cell(20,1,$proc_code_essi_arr[4],0,0,'');
				$pdf->Cell(17,1,$dateofservice_arr[4],0,0,'');
				$pdf->Cell(20,1,$proc_code_essi_arr[5],0,0,'');
				$pdf->Cell(36,1,$dateofservice_arr[5],0,0,'');
				$prev_ub_arr['ub_74c1']=$proc_code_essi_arr[3];
				$prev_ub_arr['ub_74c2']=$dateofservice_arr[3];
				$prev_ub_arr['ub_74d1']=$proc_code_essi_arr[4];
				$prev_ub_arr['ub_74d2']=$dateofservice_arr[4];
				$prev_ub_arr['ub_74e1']=$proc_code_essi_arr[5];
				$prev_ub_arr['ub_74e2']=$dateofservice_arr[5];
				
				$pdf->Cell(45,1,$box_77_lname,0,0,'');
				$pdf->Cell(30,1,$box_77_fname,0,0,'');
				$prev_ub_arr['ub_77d']=$box_77_lname;
				$prev_ub_arr['ub_77e']=$box_77_lname;
				$before_80_box_magin=5;
				
				//BOX NO. 78
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_80_1']);
				}else{
					$pdf->Ln($before_80_box_magin);
				}
				$pdf->Cell($left_margin);
				if(in_array(strtolower($billing_global_server_name), array('hammad_iasc','sheepshead'))){
					$notes_str=substr(implode(', ',$notes_arr),0,30);
				}else{
					$notes_str='';
				}
				$pdf->Cell(12,1,'',0,0,'');
				$pdf->Cell(57,1,$notes_str,0,0,'');
				$prev_ub_arr['ub_80']=$notes_str;
				if(stristr($group_row['name'],'Papillion') && in_array($objpriInsuranceCoData_rmark->Payer_id_pro,$arr_NE_Medicaid_payers) && $chk_insurance == 'primarySubmit'){
					$pdf->Cell(74,1,'261QA1903X',0,0,'');
					$prev_ub_arr['ub_81a2']='261QA1903X';
				}else if(in_array(strtolower($billing_global_server_name), array('swagelwootton')) && $group_row['group_institution']>0){
					$pdf->Cell(74,1,'261QA1903X',0,0,'');
					$prev_ub_arr['ub_81a2']='261QA1903X';
				}else{
					if((in_array($objpriInsuranceCoData_rmark->Payer_id,array('12B38')) || in_array($objpriInsuranceCoData_rmark->Payer_id_pro,array('SB806')))){
						$pdf->Cell(74,1,$pro_pri_taxaonomy,0,0,'');
						$prev_ub_arr['ub_81a2']=$pro_pri_taxaonomy;
					}else if(in_array(strtolower($billing_global_server_name), array('hammad_iasc','tutela'))){
						$pdf->Cell(-6);
						$pdf->Cell(6,1,"B3",0,0,'');
						$pdf->Cell(74,1,$pro_pri_taxaonomy,0,0,'');
						$prev_ub_arr['ub_81a1']='B3';
						$prev_ub_arr['ub_81a2']=$pro_pri_taxaonomy;
					}else{
						$pdf->Cell(74,1,'',0,0,'');
					}
				}
				
				$show_dn="";
				if(in_array(strtolower($billing_global_server_name), array('essi','ocean'))){
					$show_dn="DN";
				}
				
				if(in_array(strtolower($billing_global_server_name), array('sheepshead'))){
					if($box_77_npi==$box_78_npi){
						$box_78_lname=$box_79_lname;
						$box_78_fname=$box_79_fname;
						$box_78_npi=$box_79_npi;
						$box_78_upin=$box_79_upin;
						$show_dn="";
					}
					$box_79_lname="";
					$box_79_fname="";
					$box_79_npi="";
				}else if((in_array($objpriInsuranceCoData_rmark->Payer_id,array('14163','12B53','12B45','SB890','SB891','SKTN2','12K46')) || in_array($objpriInsuranceCoData_rmark->Payer_id_pro,array('14163','12B53','12B45','SB890','SB891','SKTN2','12K46')))){
					$box_78_npi=$show_dn=$box_78_upin=$box_78_lname=$box_78_fname="";
					$box_79_npi=$show_dn=$box_79_upin=$box_79_lname=$box_79_fname="";
				}
				$pdf->Cell(30.5,1,$box_78_npi,0,0,'');
				$pdf->Cell(7.5,1,$show_dn,0,0,'');
				$pdf->Cell(30,1,$box_78_upin,0,0,'');
				$prev_ub_arr['ub_78a']=$box_78_npi;
				$prev_ub_arr['ub_78b']=$show_dn;
				$prev_ub_arr['ub_78c']=$box_78_upin;
				
				//BOX NO. 80 to 78
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_80_2']);
				}else{
					$pdf->Ln(4);
				}
				$pdf->Cell($left_margin);
				if(in_array(strtolower($billing_global_server_name), array('essi', 'sheepshead','mackool','palisades','azar','domi'))){
					$pdf->Cell(69,1,'',0,0,'');
				}else{
					$pdf->Cell(69,1,$pri_ins_rmark,0,0,'');
					$prev_ub_arr['ub_80a']=$pri_ins_rmark;
				}
				if(stristr($group_row['name'],'Papillion') && in_array($objpriInsuranceCoData_rmark->Payer_id_pro,$arr_NE_Medicaid_payers) && $chk_insurance == 'primarySubmit'){
					$pdf->Cell(62,1,$group_row['group_Zip'].'-'.$group_row['zip_ext'],0,0,'');
					$prev_ub_arr['ub_81b2']=$group_row['group_Zip'].'-'.$group_row['zip_ext'];
				}else{
					$pdf->Cell(62,1,'',0,0,'');
				}
				$pdf->Cell(45,1,$box_78_lname,0,0,'');
				$pdf->Cell(30,1,$box_78_fname,0,0,'');
				$prev_ub_arr['ub_78d']=$box_78_lname;
				$prev_ub_arr['ub_78e']=$box_78_fname;
				
				//BOX NO. 80 to 79
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_80_3']);
				}else{
					$pdf->Ln(5);
				}
				$pdf->Cell($left_margin);
				if(in_array(strtolower($billing_global_server_name), array('essi', 'sheepshead','mackool','palisades','azar','domi'))){
					$pdf->Cell(143,1,'',0,0,'');
				}else{
					$pdf->Cell(143,1,$pri_ins_add_rmark,0,0,'');
					$prev_ub_arr['ub_80b']=$pri_ins_add_rmark;
				}
				$pdf->Cell(38,1,$box_79_npi,0,0,'');
				$prev_ub_arr['ub_79a']=$box_79_npi;
				$pdf->Cell(30,1,'',0,0,'');
				//BOX NO. 80 to 79
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_80_4']);
				}else{
					$pdf->Ln(4);
				}
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Cell($left_margin);
					if(in_array(strtolower($billing_global_server_name), array('essi', 'sheepshead','mackool','palisades','azar','domi'))){
						$pdf->Cell(131,1,'',0,0,'');
					}else{
						$pdf->Cell(131,1,$pri_csz_comp_rmark,0,0,'');
						$prev_ub_arr['ub_80c']=$pri_csz_comp_rmark;
					}
					$pdf->Cell(45,1,$box_79_lname,0,0,'');
					$pdf->Cell(30,1,$box_79_fname,0,0,'');
					$prev_ub_arr['ub_79d']=$box_79_lname;
					$prev_ub_arr['ub_79e']=$box_79_fname;
				}else{
					$pdf->Cell($left_margin);
					if(in_array(strtolower($billing_global_server_name), array('essi', 'sheepshead','mackool','palisades','azar','domi'))){
						$pdf->Cell(131,0,'',0,0,'');
					}else{
						$pdf->Cell(131,0,$pri_csz_comp_rmark,0,0,'');
						$prev_ub_arr['ub_80c']=$pri_csz_comp_rmark;
					}
					$pdf->Cell(45,0,$box_79_lname,0,0,'');
					$pdf->Cell(30,0,$box_79_fname,0,0,'');
					$prev_ub_arr['ub_79d']=$box_79_lname;
					$prev_ub_arr['ub_79e']=$box_79_fname;
				}
			}
		}
		$ub_save_data=serialize($prev_ub_arr);
		$prev_hcfa_qry=imw_query("insert into previous_ub set operator_id='$operator_id',patient_id='".$patientListData->patient_id."',enc_id='".$patientListData->encounter_id."',
					created_date='$current_date',ub_data='$ub_save_data',enc_balance='$tot_amt'");
	}
	if($fpdiCheck == true){
		$pdf->Output($newfile_ub_path,"F");
		if($only_show==""){	
			//--- Start query To change The Staus For Every Patient --------
			$chk_insurance=str_replace('tertiarySubmit','tertairySubmit',$chk_insurance);
			imw_query("update patient_charge_list set $chk_insurance = '1',hcfaStatus = '1' where charge_list_id in($chl_id_imp)");
			
			if($chld_ids!=""){
				$chld_whr_new=" charge_list_detail_id in($chld_ids)";
			}else{
				$chld_whr_new=" charge_list_id in($chl_id_imp)";
			}
			
			imw_query("update patient_charge_list_details set claim_status='1' where $chld_whr_new");
			
			//--- End query To change The Staus For Every Patient --------	
		}
		$final_path=str_replace($GLOBALS['fileroot'],$GLOBALS['webroot'],$newfile_ub_path);
		if($ar_ajax==""){
			if($newFile!=""){
				print '<script type="text/javascript">window.open(\''.$final_path.'\',"printUB","resizable=1,width=650,height=450");</script>';
			}else{
				echo $final_path;
			}
		}
		$msg = 'UB Form Successfully Printed.';
	}
}
?>
