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
set_time_limit(0); 
$without_pat="yes"; 
include_once(dirname(__FILE__)."/acc_header.php");
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/billing_functions.php");
require_once(dirname(__FILE__)."/../../library/classes/common_function.php"); 
require_once(dirname(__FILE__)."/../../library/classes/cls_common_function.php"); 

//$objManageData = new ManageData;
$sup_chk_box_arr=$_REQUEST['sup_chk_box'];
$chl_chk_box_arr = $_REQUEST['chl_chk_box'];
$print_frm=xss_rem($_REQUEST['print_frm'], 1);
$prov_frm_srh=xss_rem($_REQUEST['prov_frm_srh'], 1);
$oper_frm_srh=xss_rem($_REQUEST['oper_frm_srh']);
$prov_frm_type=xss_rem($_REQUEST['prov_frm_type'], 1);
$acc_fac_frm_srh=xss_rem($_REQUEST['acc_fac_frm_srh'], 1);
$acc_ins_frm_srh=xss_rem($_REQUEST['acc_ins_frm_srh'], 1);
$acc_ins_type_frm_srh=xss_rem($_REQUEST['acc_ins_type_frm_srh'], 1);
$dos_to_srh=xss_rem($_REQUEST['dos_to_srh'], 1);
$dos_frm_srh=xss_rem($_REQUEST['dos_frm_srh'], 1);
$post_date=$_REQUEST['post_date'];
$view_frm_srh=xss_rem($_REQUEST['view_frm_srh'], 1);
$ord_by_srh=xss_rem($_REQUEST['ord_by_srh'], 1);
$oper=$_SESSION['authId'];
$srh_tot_poc_fac=xss_rem($_REQUEST['srh_tot_poc_fac']);
$view_frm_chart=xss_rem($_REQUEST['view_frm_chart'], 1);
$validChargeListId=array();
$print_enc_arr=array();

//------------------------	Modifiers	------------------------//
$mod_name_arr=array();
$mod_id_arr=array();
$sql = "select modifiers_id,mod_prac_code from modifiers_tbl WHERE delete_status = '0' order by mod_prac_code ASC";
$rezQry = imw_query($sql);
while($row = imw_fetch_array($rezQry)){
	$mod_name_arr[$row["modifiers_id"]]=$row["mod_prac_code"];
	$mod_id_arr[$row["mod_prac_code"]]=$row["modifiers_id"];
}
//------------------------	Modifiers	------------------------//

//------------------------ TOS Type ------------------------//
$tos_id_arr=array();
$tos_name_arr=array();
$selQry = "select tos_id,tos_prac_cod from tos_tbl order by tos_prac_cod ASC";
$res = imw_query($selQry);
while($row = imw_fetch_array($res)){
	$tos_id_arr[$row['tos_prac_cod']]=$row['tos_id'];
	$tos_name_arr[$row['tos_id']]=$row['tos_prac_cod'];
}
//------------------------ TOS Type ------------------------//

//------------------------ POS Type ------------------------//
$pos_id_arr=array();
$pso_name_arr=array();
$selQry = "select pos_id,pos_prac_code from pos_tbl order by pos_prac_code ASC";
$res = imw_query($selQry);
while($row = imw_fetch_array($res)){
	$pos_id_arr[$row['pos_prac_code']]=$row['pos_id'];
	$pso_name_arr[$row['pos_id']]=$row['pos_prac_code'];
}
//------------------------ POS Type ------------------------//

//------------------------ Groups ------------------------//
$groups_arr=array();
$selQry = "select gro_id,group_institution,group_anesthesia from groups_new";
$res = imw_query($selQry);
while($row = imw_fetch_array($res)){
	if($row['group_institution']=='1'){
		$groups_arr[$row['gro_id']]=$row['gro_id'];
	}
	$grp_detail[$row['gro_id']]=$row;
}
//------------------------ POS Type ------------------------//

//------------------------ Facility ------------------------//
$selQry = "select * from facility order by name ASC";
$res = imw_query($selQry);
while($row = imw_fetch_array($res)){
	$fac_data_arr[$row['id']]=$row;
	$fac_pos_data_arr[$row['fac_prac_code']]=$row['id'];
	$hq_fac_pos_data_arr[$row['facility_type']]=$row['id'];
	$fac_tax_arr[$row['id']]=$row['fac_tax'];
}
//------------------------ Facility ------------------------//

$qry = imw_query("SELECT vip_copay_not_collect,anes_time_divisor
			FROM 
		copay_policies WHERE policies_id='1'");
while($row = imw_fetch_array($qry)){
	$policyQryRes[] = $row;	
}		
//$policyQryRes = $objManageData->mysqlifetchData($qry);
$vip_copay_not_collect = $policyQryRes[0]['vip_copay_not_collect'];
$anes_time_divisor = $policyQryRes[0]['anes_time_divisor'];

if(count($chl_chk_box_arr) > 0){
	for($i=0;$i<count($chl_chk_box_arr);$i++){
		$chl_info=array();
		$chl_id = $chl_chk_box_arr[$i];
		$cpt_code_arr = $cpt_codes_arr[0][$chl_id];
		$chlListDetailIdArr = array_keys($cpt_code_arr);
		$encounterId = $enc_id[$chl_id];
		$dc_patient_id = $pat_id[$chl_id];
		$post_p_id = $pat_id[$chl_id];
		$insCaseId = $caseTypeText[$chl_id];
		$pri_id_ins = $pri_ins_id[$chl_id];
		$sec_id_ins = $sec_ins_id[$chl_id];
		$pos_fac_code_id = $posFacilityCode[$chl_id];
		$superBillIds = $chl_superbill_id[$chl_id];
		$hcfa_chk_box=$hcfa_chk_box_arr[$chl_id];
		$print_enc_arr[]=$encounterId;
		//--------------------- TOS CODE.	---------------------//
		$tosText = $tos[$chl_id];
		$tosID = $tos_id_arr[$tosText];
		//--------------------- TOS CODE.	---------------------//			
		
		//--------------------- POS CODE.	---------------------//
		$posText = $pos[$chl_id];
		$posId = $pos_id_arr[$posText];
		//--------------------- POS CODE.	---------------------//
		
		$patientDetails = getRecords('patient_data','id',$dc_patient_id);
		$reff_phy_id = $patientDetails['primary_care_id'];
		if($pos_fac_code_id>0){
			$facility_id= $pos_fac_code_id;
		}else{
			$facility_id = $patientDetails['default_facility'];
		}
		
		if(strtolower($bl_pt_home_facility)=="yes" && $patientDetails['default_facility']>0){
			$main_facility_id=$fac_pos_data_arr[$patientDetails['default_facility']];
		}else if($_SESSION['login_facility']>0){
			$main_facility_id=$_SESSION['login_facility'];
		}else if($hq_fac_pos_data_arr[1]>0){
			$main_facility_id=$hq_fac_pos_data_arr[1];
		}else{
			$main_facility_id=$fac_pos_data_arr[$pos_fac_code_id];
		}
		
		$tday=date('Y-m-d');
		$chl_info['case_type_id']=$insCaseId;
		$chl_info['facility_id']=$facility_id;
		if($main_fac_id[$chl_id]<=0){
			$chl_info['billing_facility_id']=$main_facility_id;
		}
		$chl_info['primaryInsuranceCoId']=$pri_id_ins;
		$chl_info['secondaryInsuranceCoId']=$sec_id_ins;
		$up_chl = UpdateRecords($chl_id,'charge_list_id',$chl_info,'patient_charge_list');

		$cpt_prac_code_arr=array();
		$totalFeeAmt=0;
		$refractionAmt=0;
		
		if($superBillIds>0){
			$cur_postedDate = date('Y-m-d');
			imw_query("update superbill set postedStatus='1',postedDate='$cur_postedDate' where idSuperBill in($superBillIds) and postedStatus='0'");
		}
		
		$chld_ids_arr=array();
		for($j=0;$j<count($chlListDetailIdArr);$j++){
			$chld_info=array();
			$detailsId = $chlListDetailIdArr[$j];
			//--------------------- Procedure code id ---------------------//
			$proc_code_ins =$cpt_codes_arr[0][$chl_id][$detailsId];
			$units_ins = $units_arr[$chl_id][$detailsId];
			$proc_charges_ins= str_replace(',','',$proc_charges[$chl_id][$detailsId]);
			$total_amt_ins= str_replace(',','',$total_amt[$chl_id][$detailsId]);
			$cpt_prac_code_arr[]=$proc_code_ins;
			$total_copay_ins= str_replace(',','',$copay_amt[$chl_id][$detailsId]);
			$check_in_out_paid = $check_in_out_paid_arr[$chl_id];
			$cico_payment_id = $cico_payment_id_arr[$chl_id];
			$pt_pmt_paid = $pt_pmt_paid_arr[$chl_id];
			$pt_pmt_id = $pt_pmt_id_arr[$chl_id];
			if($self_pay_chk_box_arr[$chl_id][$detailsId]=="yes"){
				$proc_self_pay = 1;
			}else{
				$proc_self_pay = 0;
			}
			
			$getProcIdStr = "SELECT cpt_fee_id,not_covered FROM cpt_fee_tbl WHERE cpt_prac_code='$proc_code_ins' AND delete_status = '0' order by status asc";
			$getProcIdQry = imw_query($getProcIdStr);
			$getProcIdRow = imw_fetch_array($getProcIdQry);
			$procedureId = $getProcIdRow['cpt_fee_id'];
			$cpt_not_covered = $getProcIdRow['not_covered'];
			//--------------------- Procedure code id ---------------------//
			
			//--------------------- MODIFIERS ID.	---------------------//
			$mod1Text = $mod1[$chl_id][$detailsId];
			$mod1Id = $mod_id_arr[$mod1Text];
				
			$mod2Text = $mod2[$chl_id][$detailsId];
			$mod2Id = $mod_id_arr[$mod2Text];
	
			$mod3Text = $mod3[$chl_id][$detailsId];
			$mod3Id = $mod_id_arr[$mod3Text];
			
			$mod4Text = $mod4[$chl_id][$detailsId];
			$mod4Id = $mod_id_arr[$mod4Text];
			//--------------------- MODIFIERS ID.	---------------------//

			$chld_detail = getRecords('patient_charge_list',"del_status='0' and charge_list_id",$chl_id);
			$chk_totalAmt  = $chld_detail['totalAmt'];
			$chk_totalBalance   = $chld_detail['totalBalance'];
			//$cico_chl_copay_amt   = $chld_detail->copay;
			$coPayNotRequired   = $chld_detail['coPayNotRequired'];
			$coPayNotRequired2   = $chld_detail['coPayNotRequired2'];
			$cico_chl_copay_amt=0;
			if($coPayNotRequired==0){
				$cico_chl_copay_amt=$cico_chl_copay_amt+$chld_detail['pri_copay'];
			}
			if($coPayNotRequired2==0){
				$cico_chl_copay_amt=$cico_chl_copay_amt+$chld_detail['sec_copay'];
			}
			$chld_ids_arr[$detailsId]=$detailsId;
			$chld_detail_cico_cond = getRecords('patient_charge_list_details',"del_status='0' and charge_list_detail_id",$detailsId);
			$cico_chld_newBalance_cond  = $chld_detail_cico_cond['newBalance'];
			$cico_chld_approve_cond  = $chld_detail_cico_cond['approvedAmt'];
			$cico_chld_totalAmount_cond  = $chld_detail_cico_cond['totalAmount'];
			if($insCaseId>0){
				$chld_info['proc_selfpay'] = $proc_self_pay;
			}else{
				$chld_info['proc_selfpay'] = 1;
			}
			if($cpt_not_covered>0){
				$chld_info['proc_selfpay'] = 1;
			}
			$chld_info['procCode'] = $procedureId;
			if((float)$cico_chld_totalAmount_cond==(float)$cico_chld_approve_cond){
				$chld_info['units'] = $units_ins;
				$chld_info['procCharges'] = $proc_charges_ins;
				$chld_info['totalAmount'] = $total_amt_ins;
				$chld_info['approvedAmt'] = $total_amt_ins;
			}
			
			$chld_info['type_of_service'] = $tosID;
			$chld_info['place_of_service'] = $posId;
			$chld_info['posFacilityId'] = $facility_id;
			$chld_info['modifier_id1'] = $mod1Id;
			$chld_info['modifier_id2'] = $mod2Id;
			$chld_info['modifier_id3'] = $mod3Id;
			$chld_info['modifier_id4'] = $mod4Id;
			/*$chld_info['diagnosis_id1'] = $dx1[$chl_id][$detailsId];
			$chld_info['diagnosis_id2'] = $dx2[$chl_id][$detailsId];
			$chld_info['diagnosis_id3'] = $dx3[$chl_id][$detailsId];
			$chld_info['diagnosis_id4'] = $dx4[$chl_id][$detailsId];*/
			for($g=1;$g<=12;$g++){
				//$chld_info['diagnosis_id'.$g] = "";
			}
			for($g=0;$g<12;$g++){
				$diagText_all_exp=array();
				$diagText_all_exp=explode('**',$diagText_all[$chl_id][$detailsId][$g]);
				if($diagText_all_exp[1]>0){
					//$chld_info['diagnosis_id'.$diagText_all_exp[1]] = $diagText_all_exp[0];
				}
			}
			//print_r($chld_info);
			//exit();
			$totalFeeAmt = $totalFeeAmt+$total_amt_ins;
			$up_chld = UpdateRecords($detailsId,'charge_list_detail_id',$chld_info,'patient_charge_list_details');
			$chld_detail_cico = getRecords('patient_charge_list_details',"del_status='0' and charge_list_detail_id",$detailsId);
			$cico_chld_newBalance  = $chld_detail_cico['newBalance'];
			$cico_chld_id = $chld_detail_cico['charge_list_detail_id'];
			$cico_enc_id = $chld_detail['encounter_id'];
			$cico_paidForProc = $chld_detail_cico['paidForProc'];
			if($cico_paidForProc<0){
				$now_proc_paid_tot = str_replace(',','',$total_paid_amt[$chl_id][$detailsId]);
			}else{
				$now_proc_paid_tot = str_replace(',','',$total_paid_amt[$chl_id][$detailsId])-$cico_paidForProc;
			}
			$chk_enc_tot_paid_amt= str_replace(',','',$total_paid_amt[$chl_id][$detailsId]);
			set_payment_trans($cico_enc_id);
			if($print_frm=="payment" && ($check_in_out_paid>0 || $pt_pmt_paid>0) && $cico_chld_newBalance>0){
				include"cico_day_charges_payment.php";
			}
		}
		
		$chrg_mod_opr_data=array();
		$chrg_mod_opr_data['enc_id'] = $encounterId;
		$chrg_mod_opr_data['modifier_by'] = $oper;
		$chrg_mod_opr_data['modifier_on'] = date('Y-m-d h:i:s a');
		$insertedChargemodId = AddRecords($chrg_mod_opr_data,'patient_charge_list_modifiy');
		
		$encounter_id = $encounterId;
		patient_proc_bal_update($encounter_id);
		include"manageEncounterAmounts.php";
		
		$encounter_id=$encounterId;
		$submitToIns='true';
		$post_pat_id=$post_p_id;
		$day_charges_chk='yes';
		if($print_frm=="charges"){
			$status="";
			if($hcfa_chk_box){
				$status=1;
			}
			$chld_day_ids=implode(',',$chld_ids_arr);
			$chk_day_charges="yes";
			include"postCharges.php";
			if($status){
				$chargeListDetail = getRecords('patient_charge_list',"del_status='0' and charge_list_id",$chl_id);
				$reffPhyscianId = $chargeListDetail['reff_phy_id'];
				$patientDetail = getRecords('patient_data','id',$chargeListDetail['patient_id']);
				if($reffPhyscianId == 0 || $reffPhyscianId == ''){
					$reffPhyscianId = $patientDetail['providerID'];
					$reffDetail = getRecords('users','id',$reffPhyscianId);
					$reffPhysicianLname = $reffDetail['lname'];
					$reffPhysicianFname = $reffDetail['fname'];
					$reffPhysicianMname = $reffDetail['mname'];					
					$npiNumber = $reffDetail['user_npi'];
					$Texonomy = $reffDetail['TaxonomyId'];
				}
				else{
					$reffDetail = getRecords('refferphysician','physician_Reffer_id',$reffPhyscianId);
					$reffPhysicianLname = $reffDetail['LastName'];
					$reffPhysicianFname = $reffDetail['FirstName'];
					$reffPhysicianMname = $reffDetail['MiddleName'];
					$npiNumber = $reffDetail['NPI'];
					$Texonomy = $reffDetail['Texonomy'];
				}
				$renderingPhyDetail = getRecords('users','id',$chargeListDetail['primaryProviderId']);
				//---- Patient Validate Check -------
				$validation = false;
				if($chargeListDetail['primaryInsuranceCoId']==0 && $validation == false){
					$validation = true;
				}
				if($patientDetail['sex']== '' && $validation == false){
					$validation = true;
				}
				if($chargeListDetail['reff_phy_nr']==0){
					if($npiNumber == '' && $validation == false){
						$validation = true;
					}
					if($Texonomy == '' && $validation == false){
						//$validation = true;
					}
				}
				if($renderingPhyDetail['user_npi'] == '' && $validation == false){
					$validation = true;
				}
				if($renderingPhyDetail['TaxonomyId'] == '' && $validation == false){
					$validation = true;
				}
				if($validation == true){
					$invalidChargeListId[0] = $chl_id;
				}
				else{
					if($pri_id_ins>0){
						$validChargeListId[] = $chl_id;
					}
				}
			}
		}

	}
}
if(count($sup_chk_box_arr) > 0){
	for($i=0;$i<count($sup_chk_box_arr);$i++){
		$chl_info=array();
		$superBillId = 'sup_'.$sup_chk_box_arr[$i];
		$org_superBillId = $sup_chk_box_arr[$i];
		$superBillIds = $sup_chk_box_arr[$i];
		$cpt_code_arr = $cpt_codes_arr[1][$superBillId];
		$superListDetailIdArr = array_keys($cpt_code_arr);
		$encounterId = $enc_id[$superBillId];
		$dc_patient_id = $pat_id[$superBillId];
		$post_p_id = $pat_id[$superBillId];
		$insCaseId = $caseTypeText[$superBillId];
		$pri_id_ins = $pri_ins_id[$superBillId];
		$sec_id_ins = $sec_ins_id[$superBillId];
		$ter_id_ins = $ter_ins_id[$superBillId];
		$pos_fac_code_id = $posFacilityCode[$superBillId];
		$hcfa_chk_box=$hcfa_chk_box_arr[$superBillId];
		$reff_phy_id=$reff_phy_id_arr[$superBillId];
		$ref_auth=$ref_auth_arr[$superBillId];
		$ref_auth_chk=$ref_auth_chk_arr[$superBillId];
		$auth_name=$auth_name_arr[$superBillId];
		$a_id=$a_id_arr[$superBillId];
		$AuthAmount=$AuthAmount_arr[$superBillId];
		$ref_no_chk=$ref_no_chk_arr[$superBillId];
		$print_enc_arr[]=$encounterId;
		$getEncounterMatchStr = "SELECT encounter_id FROM patient_charge_list WHERE del_status='0' and encounter_id='$encounterId'";
		$getEncounterMatchQry = imw_query($getEncounterMatchStr);
		$getEncounterMatchRows = imw_num_rows($getEncounterMatchQry);
		if($getEncounterMatchRows==0){
			//--------------------- TOS CODE.	---------------------//
			$tosText = $tos[$superBillId];
			$tosID = $tos_id_arr[$tosText];
			//--------------------- TOS CODE.	---------------------//			
			
			//--------------------- POS CODE.	---------------------//
			$posText = $pos[$superBillId];
			$posId = $pos_id_arr[$posText];
			//--------------------- POS CODE.	---------------------//
			
			$getSuperbillPostedStatusStr = "SELECT * FROM superbill
										WHERE idSuperBill='$org_superBillId' and del_status='0'";
			$getSuperbillPostedStatusQry = imw_query($getSuperbillPostedStatusStr);
			$getSuperbillPostedStatusRow = imw_fetch_array($getSuperbillPostedStatusQry);
			
			if($getSuperbillPostedStatusRow['vipSuperBill'] == 1){
				$vipSuperBill = "true";
			}else{
				$vipSuperBill = "false";
			}
			
			$superBillData=array();
			$superBillData['postedStatus'] = '1';
			$superBillData['postedDate'] = date('Y-m-d');	
			$superBill_id_rec = UpdateRecords($org_superBillId,'idSuperBill',$superBillData,'superbill');
			
			$patientDetails = getRecords('patient_data','id',$dc_patient_id);
			if($reff_phy_id<=0){
				$reff_phy_id = $patientDetails['primary_care_id'];
			}
			if($pos_fac_code_id>0){
				$facility_id= $pos_fac_code_id;
			}else{
				$facility_id = $patientDetails['default_facility'];
			}
			
			if(strtolower($bl_pt_home_facility)=="yes" && $patientDetails['default_facility']>0){
				$main_facility_id=$fac_pos_data_arr[$patientDetails['default_facility']];
			}else if($_SESSION['login_facility']>0){
				$main_facility_id=$_SESSION['login_facility'];
			}else if($hq_fac_pos_data_arr[1]>0){
				$main_facility_id=$hq_fac_pos_data_arr[1];
			}else{
				$main_facility_id=$fac_pos_data_arr[$pos_fac_code_id];
			}
			
			// GETTING REFERALS
			/*$getReferralNoStr = "SELECT * FROM patient_reff WHERE patient_id = '$dc_patient_id' AND reff_type = '1'";
			$getReferralNoQry = imw_query($getReferralNoStr);
			$getReferralNoRow = imw_fetch_array($getReferralNoQry);
			$refNo1 = $getReferralNoRow['reffral_no'];
			
			$getReferralNoStr = "SELECT * FROM patient_reff WHERE patient_id = '$dc_patient_id' AND reff_type = '2'";
			$getReferralNoQry = imw_query($getReferralNoStr);
			$getReferralNoRow = imw_fetch_array($getReferralNoQry);
			$refNo2 = $getReferralNoRow['reffral_no'];*/
			// GETTING Ins. Case Id
			$pri_provider_id =$getSuperbillPostedStatusRow['physicianId'];
			$notesSuperBill=$getSuperbillPostedStatusRow['notesSuperBill'];
			$primary_provider_id_for_reports =$getSuperbillPostedStatusRow['primary_provider_id_for_reports'];
			if($primary_provider_id_for_reports==0){
				$primary_provider_id_for_reports=$pri_provider_id;
			}
			
			if($insCaseId>0){
				if($pri_id_ins==0 || $pri_id_ins==""){
					$sup_dos_date=$getSuperbillPostedStatusRow['dateOfService'];
					$getPrimaryInsCoDetails = imw_query("SELECT provider FROM insurance_data WHERE
											ins_caseid='$insCaseId'
											AND pid='$dc_patient_id'
											AND type='primary'
											and provider > 0
											and (
												date_format(effective_date,'%Y-%m-%d')<='$sup_dos_date' 
													and (
														date_format(expiration_date,'%Y-%m-%d')>='$sup_dos_date' 
														or  date_format(expiration_date,'%Y-%m-%d')='0000-00-00'
													)
												)
											order by actInsComp desc, effective_date desc, id desc");
					$getPrimaryInsCoRow = @imw_fetch_array($getPrimaryInsCoDetails);
					$pri_id_ins = $getPrimaryInsCoRow['provider'];
				}
				if($sec_id_ins==0 || $sec_id_ins==""){
					$sup_dos_date=$getSuperbillPostedStatusRow['dateOfService'];
					$getSecondaryInsCoDetails = imw_query("SELECT provider FROM insurance_data WHERE
											ins_caseid='$insCaseId'
											AND pid='$dc_patient_id'
											AND type='secondary'
											and provider > 0
											and (
												date_format(effective_date,'%Y-%m-%d')<='$sup_dos_date' 
													and (
														date_format(expiration_date,'%Y-%m-%d')>='$sup_dos_date' 
														or  date_format(expiration_date,'%Y-%m-%d')='0000-00-00'
													)
												)
											order by actInsComp desc, effective_date desc, id desc");
					$getSecondaryInsCoRow = @imw_fetch_array($getSecondaryInsCoDetails);
					$sec_id_ins = $getSecondaryInsCoRow['provider'];
				}
				if($ter_id_ins==0 || $ter_id_ins==""){
					$sup_dos_date=$getSuperbillPostedStatusRow['dateOfService'];
					$getSecondaryInsCoDetails = imw_query("SELECT provider FROM insurance_data WHERE
											ins_caseid='$insCaseId'
											AND pid='$dc_patient_id'
											AND type='tertiary'
											and provider > 0
											and (
												date_format(effective_date,'%Y-%m-%d')<='$sup_dos_date' 
													and (
														date_format(expiration_date,'%Y-%m-%d')>='$sup_dos_date' 
														or  date_format(expiration_date,'%Y-%m-%d')='0000-00-00'
													)
												)
											order by actInsComp desc, effective_date desc, id desc");
					$getSecondaryInsCoRow = @imw_fetch_array($getSecondaryInsCoDetails);
					$ter_id_ins = $getSecondaryInsCoRow['provider'];
				}
			}
			
			$getPhysicianStr = "SELECT * FROM chart_master_table 
								WHERE patient_id='$dc_patient_id'
								AND encounterId='$encounterId'";
			$getPhysicianQry = imw_query($getPhysicianStr);
			$getPhysicianRow = imw_fetch_array($getPhysicianQry);
			
			$tday=date('Y-m-d');
			$chl_info['entered_date']=$tday;
			$chl_info['vipStatus']=$vipSuperBill;
			$chl_info['encounter_id']=$encounterId;
			$chl_info['patient_id']=$dc_patient_id;
			$chl_info['case_type_id']=$insCaseId;
			$chl_info['facility_id']=$facility_id;
			$chl_info['billing_facility_id']=$main_facility_id;
			$chl_info['primaryInsuranceCoId']=$pri_id_ins;
			$chl_info['secondaryInsuranceCoId']=$sec_id_ins;
			$chl_info['tertiaryInsuranceCoId']=$ter_id_ins;
			$chl_info['primaryProviderId']=$pri_provider_id;
			$chl_info['secondaryProviderId']=$pri_provider_id;
			$chl_info['date_of_service']=$getSuperbillPostedStatusRow['dateOfService'];
			$chl_info['reff_phy_id']=$reff_phy_id;
			$chl_info['submitted']='false';
			$chl_info['superbillFormId']=$org_superBillId;
			$chl_info['payment_status']='Pending';
			$chl_info['operator_id']=$oper;
			$chl_info['gro_id']=$getSuperbillPostedStatusRow['gro_id'];
			$chl_info['sch_app_id']=$getSuperbillPostedStatusRow['sch_app_id'];
			$chl_info['superbillPosted']='true';
			$chl_info['entered_time'] = date('H:i:s');
			$chl_info['all_dx_codes'] = remove_spec_dx($getSuperbillPostedStatusRow['arr_dx_codes']);
			if($getPhysicianRow['enc_icd10']=="1" || $getSuperbillPostedStatusRow['sup_icd10']=="1"){
				$chl_info['enc_icd10'] = 1;
			}else{
				$chl_info['enc_icd10'] = 0;
			}
			$chl_info['enc_icd10'] = 1;//will always in r8
			$chl_info['primary_provider_id_for_reports']=$primary_provider_id_for_reports;
			
			$billing_type=3;
			if($grp_detail[$getSuperbillPostedStatusRow['gro_id']]['group_anesthesia']>0){
				$billing_type=1;
			}else if($grp_detail[$getSuperbillPostedStatusRow['gro_id']]['group_institution']>0){
				$get_ins_type = imw_query("SELECT * FROM insurance_companies WHERE id='$pri_id_ins'");
				$get_ins_type_row = imw_fetch_array($get_ins_type);
				if($get_ins_type_row['institutional_type']=="INST_PROF"){
					$billing_type=3;
				}else{
					$billing_type=2;
				}
			}
			$chl_info['billing_type']=$billing_type;
			
			$admit_date=$disch_date=$acc_anes_time=$acc_anes_unit="";
			if($getSuperbillPostedStatusRow['anes_start_time']!="00:00:00"){
				$admit_date=$getSuperbillPostedStatusRow['dateOfService'].' '.$getSuperbillPostedStatusRow['anes_start_time'];
			}
			if($getSuperbillPostedStatusRow['anes_stop_time']!="00:00:00"){
				$disch_date=$getSuperbillPostedStatusRow['dateOfService'].' '.$getSuperbillPostedStatusRow['anes_stop_time'];
				
			}
			if($admit_date!=""){
				$anes_start_time_exp=explode(":",$getSuperbillPostedStatusRow['anes_start_time']);
				$anes_stop_time_exp=explode(":",$getSuperbillPostedStatusRow['anes_stop_time']);
				$acc_anes_time = ((($anes_stop_time_exp[0]-$anes_start_time_exp[0])*60)+($anes_stop_time_exp[1]-$anes_start_time_exp[1]));
				$acc_anes_unit=number_format(($acc_anes_time/$anes_time_divisor),2);
			}
			$chl_info['admit_date']=$admit_date;
			$chl_info['disch_date']=$disch_date;
			$chl_info['acc_anes_time']=$acc_anes_time;
			$chl_info['acc_anes_unit']=$acc_anes_unit;
			
			$ins_chl = AddRecords($chl_info,'patient_charge_list');
			$cpt_prac_code_arr=array();
			$totalFeeAmt=0;
			$refractionAmt=0;
			$display_order=0;
			$copay_proc_code_arr=array();
			for($t=0;$t<count($superListDetailIdArr);$t++){
				$copay_detailsId = $superListDetailIdArr[$t];
				//--------------------- Procedure code id ---------------------//
				$copay_proc_code_arr[] =$cpt_codes_arr[1][$superBillId][$copay_detailsId];
			}
			// PRIMARY
			$getPrimaryInsCoDetails = imw_query("SELECT copay,referal_required FROM insurance_data 
													WHERE ins_caseid='$insCaseId'
													AND pid='$dc_patient_id'
													AND type='primary'
													AND actInsComp='1'");
			$getPrimaryInsCoDetailsRow = imw_fetch_array($getPrimaryInsCoDetails);
			$referal_req_pri=$getPrimaryInsCoDetailsRow['referal_required'];
			// SECONDARY
			$getSecondaryInsCoDetails = imw_query("SELECT copay,referal_required FROM insurance_data 
													WHERE ins_caseid='$insCaseId'
													AND pid='$dc_patient_id'
													AND type='secondary'
													AND actInsComp='1'");
			$getSecondaryInsCoRow = imw_fetch_array($getSecondaryInsCoDetails);
			$referal_req_sec=$getSecondaryInsCoRow['referal_required'];
			// TERTIARY
			$copay=0;
			$pri_copay=0;
			$sec_copay=0;
			$cpt_prac_code_imp=implode(',',$copay_proc_code_arr);
			$copay_collect_chk=copay_apply_chk($cpt_prac_code_imp,$pri_id_ins,$sec_id_ins);
			
			$copay_policies = ChkSecCopay_collect($pri_id_ins);
			$secCopay_collect_chk=$copay_policies;	
			
			if($copay_collect_chk[0]==true && $copay_collect_chk[1]==true){
				$pri_copay=$getPrimaryInsCoDetailsRow['copay'];
				if($secCopay_collect_chk=='Yes'){
					$sec_copay=$getSecondaryInsCoRow['copay'];
				}
				$copay=$pri_copay+$sec_copay;
			}else if($copay_collect_chk[0]==true){
				$pri_copay=$getPrimaryInsCoDetailsRow['copay'];
				$sec_copay=0;
				$copay=$pri_copay+$sec_copay;
			}else if($copay_collect_chk[1]==true && $secCopay_collect_chk=='Yes'){
				$pri_copay=0;
				$sec_copay=$getSecondaryInsCoRow['copay'];
				$copay=$pri_copay+$sec_copay;
			}
			
			$coPayNotRequired=0;
			$coPayNotRequired2=0;
			if($vipSuperBill=="true" && $vip_copay_not_collect>0){
				if($pri_copay>0){
					$coPayNotRequired =1;
				}
				if($sec_copay>0){
					$coPayNotRequired2 =1;
				}
			}	
			
			$referral_arr=array();
			$referral="";
			if($ref_auth=="" && $ref_auth_chk==""){
				if($referal_req_pri=='Yes' || $referal_req_sec=='Yes' || $referal_req_ter=='Yes'){
					$chk_reff_date=$getSuperbillPostedStatusRow['dateOfService'];
					$reff_qry=imw_query("select reffral_no,reff_type from patient_reff where insCaseid='$insCaseId' 
					and patient_id='$dc_patient_id' and reff_phy_id='$reff_phy_id'
					and (end_date='0000-00-00' or end_date >= '$chk_reff_date') and effective_date <= '$chk_reff_date'
					and no_of_reffs > '0' and del_status='0' order by end_date asc,reff_id asc");
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
							$optional_referral=$reff_row['reffral_no'];
						}
						if($referal_req_ter=='Yes' && $reff_row['reff_type']=='3'){
							$referral_arr[]=array($reff_row['reffral_no'],$xyz, $reff_row['reffral_no']);
							if($referral==""){
								$referral=$reff_row['reffral_no'];
							}
						}
					}
				}
			}
			
			if($ref_auth_chk=="" && $ref_no_chk!=""){
				$referral=$ref_auth;
			}
			
			$auth_up="";
			if($auth_name!=""){
				$auth_up=",auth_id='$a_id',auth_no='$auth_name',auth_amount='$AuthAmount'";
			}
			
			$patientAmtToPay = $refractionAmt+$copay;		
			$updateEncounterAmountStr = "UPDATE patient_charge_list SET 
											copay='$copay',
											pri_copay='$pri_copay',
											sec_copay='$sec_copay',
											coPayNotRequired='$coPayNotRequired',
											coPayNotRequired2='$coPayNotRequired2',
											referral = '$referral',
											optional_referral = '$optional_referral' $auth_up
											WHERE charge_list_id='$ins_chl'";
			$updateEncounterAmountQry = imw_query($updateEncounterAmountStr);
			$chld_ids_arr=array();
			for($j=0;$j<count($superListDetailIdArr);$j++){
				$display_order++;
				$chld_info=array();
				$detailsId = $superListDetailIdArr[$j];
				//--------------------- Procedure code id ---------------------//
				$proc_code_ins =$cpt_codes_arr[1][$superBillId][$detailsId];
				$units_ins = $units_arr[$superBillId][$detailsId];
				$proc_charges_ins= str_replace(',','',$proc_charges[$superBillId][$detailsId]);
				$total_amt_ins= str_replace(',','',$total_amt[$superBillId][$detailsId]);
				$check_in_out_paid = $check_in_out_paid_arr[$superBillId];
				$cico_payment_id = $cico_payment_id_arr[$superBillId];
				$pt_pmt_paid = $pt_pmt_paid_arr[$superBillId];
				$pt_pmt_id = $pt_pmt_id_arr[$superBillId];
				$cpt_prac_code_arr[]=$proc_code_ins;
				
				if($self_pay_chk_box_arr[$superBillId][$detailsId]=="yes"){
					$proc_self_pay = 1;
				}else{
					$proc_self_pay = 0;
				}
				
				$getProcIdStr = "SELECT cpt_fee_id,rev_code,cpt_comments,not_covered,cpt_tax FROM cpt_fee_tbl WHERE cpt_prac_code='$proc_code_ins' AND delete_status = '0' order by status asc";
				$getProcIdQry = imw_query($getProcIdStr);
				$getProcIdRow = imw_fetch_array($getProcIdQry);
				$procedureId = $getProcIdRow['cpt_fee_id'];
				$rev_code = $getProcIdRow['rev_code'];
				$cpt_comments = $getProcIdRow['cpt_comments'];
				$cpt_not_covered = $getProcIdRow['not_covered'];
				$cpt_tax = $getProcIdRow['cpt_tax'];
				//--------------------- Procedure code id ---------------------//
				
				//-------------------- insurance details -----------------------
				$getStartEndDate = "SELECT * FROM insurance_case WHERE ins_caseid='$insCaseId'";
				$getStartEndDate = imw_query($getStartEndDate);
				$getStartEndDateRow = imw_fetch_array($getStartEndDate);
				$ins_case_type = $getStartEndDateRow['ins_case_type'];
				$effective_date = $getStartEndDateRow['start_date'];
				
				list($date, $time)=explode(" ", $effective_date);
				$start_date=$date;
						
				$expiration_date = $getStartEndDateRow['end_date'];
				list($date, $time)=explode(" ", $expiration_date);
				$end_date = $date;
				//-------------------- insurance details -----------------------
				
				
				//--------------------- MODIFIERS ID.	---------------------//
				$mod1Text = $mod1[$superBillId][$detailsId];
				$mod1Id = $mod_id_arr[$mod1Text];
					
				$mod2Text = $mod2[$superBillId][$detailsId];
				$mod2Id = $mod_id_arr[$mod2Text];
		
				$mod3Text = $mod3[$superBillId][$detailsId];
				$mod3Id = $mod_id_arr[$mod3Text];
				
				$mod4Text = $mod4[$superBillId][$detailsId];
				$mod4Id = $mod_id_arr[$mod4Text];
				//--------------------- MODIFIERS ID.	---------------------//
				
				$chld_info['charge_list_id'] = $ins_chl;
				$chld_info['patient_id'] = $dc_patient_id;
				$chld_info['procCode'] = $procedureId;
				$chld_info['start_date'] = $getSuperbillPostedStatusRow['dateOfService'];
				//$chld_info['end_date'] = $end_date;
				$chld_info['primaryProviderId'] = $pri_provider_id;
				$chld_info['secondaryProviderId'] = $pri_provider_id;
				//$chld_info['notes'] = $notesSuperBill;
				$chld_info['units'] = $units_ins;
				$chld_info['procCharges'] = $proc_charges_ins;
				$chld_info['totalAmount'] = $total_amt_ins;
				$chld_info['paidForProc'] = '0';
				$chld_info['balForProc'] = $total_amt_ins;
				$chld_info['approvedAmt'] = $total_amt_ins;
				$chld_info['deductAmt'] = '0';
				$chld_info['newBalance'] = $total_amt_ins;
				$chld_info['type_of_service'] = $tosID;
				$chld_info['place_of_service'] = $posId;
				$chld_info['posFacilityId'] = $facility_id;
				$chld_info['modifier_id1'] = $mod1Id;
				$chld_info['modifier_id2'] = $mod2Id;
				$chld_info['modifier_id3'] = $mod3Id;
				$chld_info['modifier_id4'] = $mod4Id;
				$chld_info['primary_provider_id_for_reports'] = $primary_provider_id_for_reports;
				if($insCaseId>0){
					$chld_info['proc_selfpay'] = $proc_self_pay;
				}else{
					$chld_info['proc_selfpay'] = 1;
				}
				
				if($cpt_not_covered>0){
					$chld_info['proc_selfpay'] = 1;
				}
				
				/*$chld_info['diagnosis_id1'] = $dx1[$superBillId][$detailsId];
				$chld_info['diagnosis_id2'] = $dx2[$superBillId][$detailsId];
				$chld_info['diagnosis_id3'] = $dx3[$superBillId][$detailsId];
				$chld_info['diagnosis_id4'] = $dx4[$superBillId][$detailsId];*/
				
				for($g=1;$g<=12;$g++){
					//$chld_info['diagnosis_id'.$g] = "";
				}
				for($g=0;$g<12;$g++){
					
					/*$diagText_all_exp=array();
					$diagText_all_exp=explode('**',$diagText_all[$superBillId][$detailsId][$g]);
					if($diagText_all_exp[1]>0){
						//$chld_info['diagnosis_id'.$diagText_all_exp[1]] = $diagText_all_exp[0];
					}*/
					
				}
				if($detailsId>0){
					$get_sup_proc_qry=imw_query("select dx1,dx2,dx3,dx4,dx5,dx6,dx7,dx8,dx9,dx10,dx11,dx12 from procedureinfo where id='$detailsId'");
					$get_sup_proc_row = imw_fetch_array($get_sup_proc_qry);
					for($g=1;$g<=12;$g++){
						$chld_info['diagnosis_id'.$g] = $get_sup_proc_row['dx'.$g];
					}
				}
				
				$chld_info['referral'] = $referral;
				$chld_info['optional_referral'] = $optional_referral;
				$chld_info['credits'] = 'false';
				$chld_info['idSuperBill'] = $org_superBillId;
				$chld_info['procedureInfoId'] = $detailsId;
				$chld_info['creditLessBalance'] = $total_amt_ins;
				$chld_info['display_order'] = $display_order;
				$chld_info['entered_date'] = date('Y-m-d H:i:s');	
				$chld_info['operator_id'] = $oper;
				$chld_info['rev_code'] = $rev_code;
				$chld_info['notes'] = $cpt_comments;
				
				$totalFeeAmt = $totalFeeAmt+$total_amt_ins;
				
				if($groups_arr[$getSuperbillPostedStatusRow['gro_id']]>0){
					if(strtolower($billing_global_server_name)=='sheepshead'){
						$chld_info['rev_rate'] = "24/1408";
					}else if(strtolower($billing_global_server_name)=='shoreline'){
						$chld_info['rev_rate'] = "A3/".$total_amt_ins;
					}
				}
				$ins_chld = AddRecords($chld_info,'patient_charge_list_details');
				$chld_ids_arr[$ins_chld]=$ins_chld;
				
				$chld_detail_cico = getRecords('patient_charge_list_details',"del_status='0' and charge_list_detail_id",$ins_chld);
				$cico_chld_newBalance  = $chld_detail_cico['newBalance'];
				$cico_chld_id = $chld_detail_cico['charge_list_detail_id'];
				$cico_chl_id = $chld_detail_cico['charge_list_id'];
				$cico_paidForProc = $chld_detail_cico['paidForProc'];
				$cico_enc_id = $encounterId;
								
				$chld_detail = getRecords('patient_charge_list',"del_status='0' and charge_list_id",$cico_chl_id);
				//$cico_chl_copay_amt   = $chld_detail->copay;
				$coPayNotRequired   = $chld_detail['coPayNotRequired'];
				$coPayNotRequired2   = $chld_detail['coPayNotRequired2'];
				$cico_chl_copay_amt=0;
				if($coPayNotRequired==0){
					$cico_chl_copay_amt=$cico_chl_copay_amt+$chld_detail['pri_copay'];
				}
				if($coPayNotRequired2==0){
					$cico_chl_copay_amt=$cico_chl_copay_amt+$chld_detail['sec_copay'];
				}
			
				$now_proc_paid_tot = str_replace(',','',$total_paid_amt[$superBillId][$detailsId])-$cico_paidForProc;
				$chk_enc_tot_paid_amt= str_replace(',','',$total_paid_amt[$superBillId][$detailsId]);
				if($print_frm=="payment" && ($check_in_out_paid>0 || $pt_pmt_paid>0) && $cico_chld_newBalance>0){
					include"cico_day_charges_payment.php";
				}
				if($cpt_tax>0){
					$cpt_txt_val_arr[]=$total_amt_ins;
				}
			}

			// Add Procedure Tax
			$fac_tax=0;
			if($main_facility_id>0){
				$fac_tax = $fac_tax_arr[$main_facility_id];
			}else if($hq_fac_pos_data_arr[1]>0){
				$fac_tax = $fac_tax_arr[$hq_fac_pos_data_arr[1]];
			}
			
			if($fac_tax>0 && array_sum($cpt_txt_val_arr)>0){
				$cpt_txt_val=0;	
				$cpt_qry = imw_query("select cpt_fee_id from cpt_fee_tbl where cpt_prac_code='tax' or cpt4_code='tax' order by delete_status asc limit 0,1");
				$cpt_res = imw_fetch_array($cpt_qry);
				
				$cpt_txt_val=(array_sum($cpt_txt_val_arr)*$fac_tax)/100;
				$cpt_txt_val=str_replace(',','',number_format($cpt_txt_val,2));
				$tax_display_order=$display_order+1;
				$cpt_tax_id=$cpt_res['cpt_fee_id'];
				
				$insertChargesDetailStr = "INSERT INTO patient_charge_list_details SET
				charge_list_id = '$ins_chl',patient_id = '$dc_patient_id',procCode = '$cpt_tax_id',
				start_date = '".$getSuperbillPostedStatusRow['dateOfService']."',primaryProviderId = '$pri_provider_id',
				secondaryProviderId = '$pri_provider_id',units = '1',procCharges = '$cpt_txt_val',totalAmount = '$cpt_txt_val',
				balForProc = '$cpt_txt_val',approvedAmt = '$cpt_txt_val',newBalance = '$cpt_txt_val',
				type_of_service = '$tosID',place_of_service = '$posId',posFacilityId = '$facility_id',
				idSuperBill = '$superBillId',procedureInfoId = '',display_order = '$tax_display_order',
				entered_date='".date('Y-m-d H:i:s')."',operator_id='".$_SESSION['authId']."',
				primary_provider_id_for_reports='$primary_provider_id_for_reports',proc_selfpay='1'";
				$insertChargesDetailStr = imw_query($insertChargesDetailStr);
			}
			
			$encounter_id = $encounterId;
			patient_proc_bal_update($encounter_id);
			include "manageEncounterAmounts.php";
			
			if($patientDetails['pat_account_status']>0){
				$coll_qry=imw_query("select patient_data.id from patient_data 
						join account_status on account_status.id=patient_data.pat_account_status 
						where patient_data.pat_account_status>0 and
						account_status.status_type='collection' and patient_data.id='$dc_patient_id'");
				$today_date = date('Y-m-d');
				if(imw_num_rows($coll_qry)>0){
					$today_date = date('Y-m-d');
					$updateStr = "UPDATE patient_charge_list SET
								  collection = 'true',
								  collectionAmount = totalBalance,
								  collectionDate = '".$today_date."' 
								  WHERE patient_id ='$dc_patient_id' and totalBalance>0  and collection !='true'";
					$updateRs = imw_query($updateStr);
				}else{
					$updateStr = "UPDATE patient_charge_list SET
								  collection = 'false',
								  collectionDate = '' 
								  WHERE patient_id ='$dc_patient_id' and collection ='true'";
					$updateRs = imw_query($updateStr);
				}
			}else{
					$updateStr = "UPDATE patient_charge_list SET
								  collection = 'false',
								  collectionDate = '' 
								  WHERE patient_id ='$dc_patient_id' and collection ='true'";
					$updateRs = imw_query($updateStr);
			}
			
			$encounter_id=$encounterId;
			$submitToIns='true';
			$post_pat_id=$post_p_id;
			$day_charges_chk='yes';
			if($print_frm=="charges"){
				$status="";
				if($hcfa_chk_box){
					$status=1;
				}
				$chld_day_ids=implode(',',$chld_ids_arr);
				$chk_day_charges="yes";
				include"postCharges.php";
				if($status){
					$chargeListDetail = getRecords('patient_charge_list',"del_status='0' and charge_list_id",$ins_chl);
					$reffPhyscianId = $chargeListDetail['reff_phy_id'];
					$patientDetail = getRecords('patient_data','id',$chargeListDetail['patient_id']);
					if($reffPhyscianId == 0 || $reffPhyscianId == ''){
						$reffPhyscianId = $patientDetail['providerID'];
						$reffDetail = getRecords('users','id',$reffPhyscianId);
						$reffPhysicianLname = $reffDetail['lname'];
						$reffPhysicianFname = $reffDetail['fname'];
						$reffPhysicianMname = $reffDetail['mname'];					
						$npiNumber = $reffDetail['user_npi'];
						$Texonomy = $reffDetail['TaxonomyId'];
					}
					else{
						$reffDetail = getRecords('refferphysician','physician_Reffer_id',$reffPhyscianId);
						$reffPhysicianLname = $reffDetail['LastName'];
						$reffPhysicianFname = $reffDetail['FirstName'];
						$reffPhysicianMname = $reffDetail['MiddleName'];
						$npiNumber = $reffDetail['NPI'];
						$Texonomy = $reffDetail['Texonomy'];
					}
					$renderingPhyDetail = getRecords('users','id',$chargeListDetail['primaryProviderId']);
					//---- Patient Validate Check -------
					$validation = false;
					if($chargeListDetail['primaryInsuranceCoId']==0 && $validation == false){
						$validation = true;
					}
					if($patientDetail['sex'] == '' && $validation == false){
						$validation = true;
					}
					if($chargeListDetail['reff_phy_nr']==0){
						if($npiNumber == '' && $validation == false){
							$validation = true;
						}
						if($Texonomy == '' && $validation == false){
							//$validation = true;
						}
					}
					if($renderingPhyDetail['user_npi'] == '' && $validation == false){
						$validation = true;
					}
					if($renderingPhyDetail['TaxonomyId'] == '' && $validation == false){
						$validation = true;
					}
					if($validation == true){
						$invalidChargeListId[0] = $ins_chl;
					}
					else{
						if($pri_id_ins>0){
							$validChargeListId[] = $ins_chl;
						}
					}
				}
			}
		}	
	}
}
if(count($validChargeListId)>0){
	$InsComp=1;
	$validChargeListId=array_unique($validChargeListId);
	$chld_ids="";
	require_once"../billing/print_hcfa_form.php";
}
?>
<form action="day_charges_list.php" name="post_charge_update" id="post_charge_update" method="post">
	<input type="hidden" name="print_frm" value="<?php echo $print_frm; ?>">
    <input type="hidden" name="provider_type" value="<?php echo $prov_frm_type; ?>">
    <input type="hidden" name="provider_srh_str" value="<?php echo $prov_frm_srh; ?>">
    <input type="hidden" name="operator_srh_str" value="<?php echo $oper_frm_srh; ?>">
    <input type="hidden" name="dos_frm" value="<?php echo $dos_frm_srh; ?>">
    <input type="hidden" name="dos_to" value="<?php echo $dos_to_srh; ?>">
    <input type="hidden" name="view_srh" value="<?php echo $view_frm_srh; ?>">
    <input type="hidden" name="view_chart" value="<?php echo $view_frm_chart; ?>">
    <input type="hidden" name="ord_by" value="<?php echo $ord_by_srh; ?>">
    <input type="hidden" name="acc_fac_str" value="<?php echo $acc_fac_frm_srh; ?>">
    <input type="hidden" name="acc_ins_str" value="<?php echo $acc_ins_frm_srh; ?>">
    <input type="hidden" name="inscasetype_str" value="<?php echo $acc_ins_type_frm_srh; ?>">
    <input type="hidden" name="srh_tot_poc_fac" value="<?php echo $srh_tot_poc_fac; ?>">
</form>
<script>document.post_charge_update.submit();</script>