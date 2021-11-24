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
$title = "Unprocessed Superbills";
require_once(dirname(__FILE__)."/acc_header.php");

if($_REQUEST["post_action"] == "del"){
	//----------------- Delete Superbill -----------------//
	if($_REQUEST['chkbx']){
		foreach($_REQUEST['chkbx'] as $key => $val){
			if($val>0){
				$qry = "update superbill set del_status='".$val."' where idSuperBill='".$val."'";
				imw_query($qry);
			}
		}
	}
}

//----------------- Copay Policies Data -----------------//
$get_pol=imw_query("SELECT billing_amount,vip_ref_not_collect,anes_time_divisor FROM copay_policies");
$row_pol=imw_fetch_assoc($get_pol);
$vip_ref_not_collect=$row_pol['vip_ref_not_collect'];
$billing_amount=$row_pol['billing_amount'];
$anes_time_divisor=$row_pol['anes_time_divisor'];

if($_REQUEST['post_action']>0){
	
	//------------------------ Facility ------------------------//
	$selQry = "select * from facility order by name ASC";
	$res = imw_query($selQry);
	while($row = imw_fetch_array($res)){
		$fac_pos_data_arr[$row['fac_prac_code']]=$row['id'];
		$hq_fac_pos_data_arr[$row['facility_type']]=$row['id'];
		$fac_tax_arr[$row['id']]=$row['fac_tax'];
	}
	//------------------------ Facility ------------------------//
	
	//------------------------ Groups ------------------------//
	$selQry = "select group_color,gro_id,name,group_institution,group_anesthesia from groups_new";
	$res = imw_query($selQry);
	while($row = imw_fetch_array($res)){
		$grp_detail[$row['gro_id']]=$row;
	}
	//------------------------ POS Type ------------------------//
	
	$superBillId = $_REQUEST['post_action'];
	$chk_sup_qry = imw_query("select * from superbill WHERE idSuperBill='$superBillId' and patientId='$patient_id' and postedStatus='0'");
	if(imw_num_rows($chk_sup_qry)>0){
		$getSuperbillPostedStatusRow = imw_fetch_array($chk_sup_qry);
		$insCaseId = $getSuperbillPostedStatusRow['insuranceCaseId'];	
		$idSuperBill = $getSuperbillPostedStatusRow['idSuperBill'];
		$encounterId = $getSuperbillPostedStatusRow['encounterId'];
		$primaryPhysicianId = $getSuperbillPostedStatusRow['physicianId'];
		$patientId = $getSuperbillPostedStatusRow['patientId'];
		$todaysCharges = $getSuperbillPostedStatusRow['todaysCharges'];
		$todaysPayment = $getSuperbillPostedStatusRow['todaysPayment'];
			
		$chk_chl_qry = imw_query("select charge_list_id from patient_charge_list WHERE encounter_id='$encounterId'");
		if(imw_num_rows($chk_chl_qry)==0){
			
			//----------------- POS Facility Detail -----------------//
			$pos_fac_arr=array();
			$poc_fac_all_arr=array();
			$qry_pos_fac=imw_query("select pos_facilityies_tbl.facilityPracCode as name,pos_facilityies_tbl.pos_facility_id as id,
									pos_tbl.pos_prac_code,pos_tbl.pos_id,pos_facilityies_tbl.pos_id as pos_id_fac
									from pos_facilityies_tbl left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
									order by pos_facilityies_tbl.facilityPracCode");	
			while($fet_pos_fac=imw_fetch_array($qry_pos_fac)){
				$poc_fac_all_arr[$fet_pos_fac['id']]=$fet_pos_fac['pos_id'];
				if(strtolower($fet_pos_fac['pos_prac_code'])=='o' && $fet_pos_fac['pos_id']==$fet_pos_fac['pos_id_fac'] && $facility_id==$fet_pos_fac['id']){
					$posFacilityId = $fet_pos_fac['id'];
				}
			}
			
			if($insCaseId == '' || $insCaseId==0){
				$insCaseId = $_REQUEST['insCaseSelect'.$superBillId];
				$ins_case_id_arr = explode('-',$insCaseId);
				$insCaseId = end($ins_case_id_arr);
			}
			
			//----------------- ChartNote Detail -----------------//
			$getPhysicianQry = imw_query("SELECT finalizerId,enc_icd10 FROM chart_master_table WHERE patient_id='$patient_id' AND encounterId='$encounterId'");
			$getPhysicianRow = imw_fetch_array($getPhysicianQry);
			if($primaryPhysicianId=='0'){
				$primaryPhysicianId = $getPhysicianRow['finalizerId'];
			}
	
			//----------------- Insurance Detail -----------------//
			$getStartEndDate = imw_query("SELECT * FROM insurance_case WHERE ins_caseid='$insCaseId'");
			$getStartEndDateRow = imw_fetch_array($getStartEndDate);
			$ins_case_type = $getStartEndDateRow['ins_case_type'];
			$effective_date = $getStartEndDateRow['start_date'];
			$expiration_date = $getStartEndDateRow['end_date'];
						
			$getPrimaryInsCoDetails = imw_query("SELECT * FROM insurance_data WHERE ins_caseid='$insCaseId' AND pid='$patient_id' AND actInsComp='1'");
			while($getPrimaryInsCoDetailsRow = imw_fetch_array($getPrimaryInsCoDetails)){
				if($getPrimaryInsCoDetailsRow['type']=="primary"){
					$primaryInsCoId = $getPrimaryInsCoDetailsRow['provider'];
					$chk_pri_copay=$getPrimaryInsCoDetailsRow['copay'];
					$referal_req_pri=$getPrimaryInsCoDetailsRow['referal_required'];
					$auth_req_pri = $getPrimaryInsCoDetailsRow['auth_required'];
				}
				if($getPrimaryInsCoDetailsRow['type']=="secondary"){
					$secondaryInsCoId = $getPrimaryInsCoDetailsRow['provider'];
					$chk_sec_copay=$getPrimaryInsCoDetailsRow['copay'];
					$referal_req_sec=$getPrimaryInsCoDetailsRow['referal_required'];
					$auth_req_sec = $getPrimaryInsCoDetailsRow['auth_required'];
				}
				if($getPrimaryInsCoDetailsRow['type']=="tertiary"){
					$tertiaryInsCoId = $getPrimaryInsCoDetailsRow['provider'];
					$chk_tri_copay=$getPrimaryInsCoDetailsRow['copay'];
					$referal_req_ter=$getPrimaryInsCoDetailsRow['referal_required'];
					$auth_req_ter = $getPrimaryInsCoDetailsRow['auth_required'];
				}
			}
							
			$formId = $getSuperbillPostedStatusRow['formId'];
			$dateOfService = $getSuperbillPostedStatusRow['dateOfService'];
			$refferingPhysician = $getSuperbillPostedStatusRow['refferingPhysician'];
			$vipSuperBill = $getSuperbillPostedStatusRow['vipSuperBill'];
			$gro_id = $getSuperbillPostedStatusRow['gro_id'];
			$sch_app_id = $getSuperbillPostedStatusRow['sch_app_id'];
			$arr_dx_codes = remove_spec_dx($getSuperbillPostedStatusRow['arr_dx_codes']);
			$primary_provider_id_for_reports = $getSuperbillPostedStatusRow['primary_provider_id_for_reports'];
			if($getPhysicianRow['enc_icd10']=="1" || $getSuperbillPostedStatusRow['sup_icd10']=="1"){
				$enc_icd10 = 1;
			}else{
				$enc_icd10 = 0;
			}
			$enc_icd10 = 1;//will always in r8
			if($vipSuperBill == 1){
				$vipSuperBill = "true";
			}else{
				$vipSuperBill = "false";
			}
				
			$notesSuperBill = $getSuperbillPostedStatusRow['notesSuperBill'];
			
			$posCode = $getSuperbillPostedStatusRow['pos'];
			if(($posCode != 'O') && (($posCode != 'o'))){
				$posFacilityId = 0;
			}
			
			//----------------- POS Detail -----------------//
			$getPosIdQry = imw_query("SELECT * FROM pos_tbl WHERE pos_prac_code='$posCode'");
			$getPosIdRow = imw_fetch_array($getPosIdQry);
			$posId = $getPosIdRow['pos_id'];
			$chk_pos_facility  = $getPosIdRow['facility'];
			
			//----------------- Apoointments Detail -----------------//		
			$chk_sc_fac=$chk_sc_id="";
			if($posCode=="SC"){
				$chk_sc_fac=" and schedule_appointments.sa_facility_id='$chk_pos_facility'";
			}
			if($sch_app_id>0){
				$chk_sc_id=" and schedule_appointments.id='$sch_app_id'";
			}
			$sel_appQry=imw_query("select facility.fac_prac_code,pos_facilityies_tbl.pos_id from 
					schedule_appointments  as schedule_appointments,
					facility as facility,
					pos_facilityies_tbl as pos_facilityies_tbl
						where 
					schedule_appointments.sa_facility_id=facility.id
					and pos_facilityies_tbl.pos_facility_id =facility.fac_prac_code
					and schedule_appointments.sa_app_start_date='$dateOfService'
					and schedule_appointments.sa_patient_id='$patient_id'
					and schedule_appointments.sa_patient_app_status_id not in(3,18,201,203) $chk_sc_fac $chk_sc_id");
			$sel_appRow = imw_fetch_array($sel_appQry);
			$posFacilityId=$sel_appRow['fac_prac_code'];	
			if($posFacilityId>0){
				$posId=$sel_appRow['pos_id'];
			}else{
				$sel_pat_fac=imw_query("select id,default_facility from patient_data where id in('$patient_id')");
				$fet_pat_fac=imw_fetch_array($sel_pat_fac);
				$default_facility=$fet_pat_fac['default_facility'];
				if($fet_pat_fac['default_facility']>0){
					$posFacilityId=$fet_pat_fac['default_facility'];
					$posId=$poc_fac_all_arr[$fet_pat_fac['default_facility']];
				}
			}
					
			//----------------- TOS Detail -----------------//				
			$tosCode = $getSuperbillPostedStatusRow['tos'];
			$getTosIdQry = imw_query("SELECT * FROM tos_tbl WHERE tos_prac_cod='$tosCode' OR tos_description='$tosCode' OR tos_code='$tosCode'");
			$getTosIdRow = imw_fetch_array($getTosIdQry);
			$tosId = $getTosIdRow['tos_id'];
			
			//----------------- Referring Physician Detail -----------------//	
			$getReffPhyId = getPatientReffPhy($patient_id,$insCaseId,'primary');
			if($refferingPhysician>0){
				$reff_phy_id = $refferingPhysician;
			}else{
				if($getReffPhyId->reff_phy_id){
					$reff_phy_id = $getReffPhyId->reff_phy_id;
				}
				else{
					$pat_qry = imw_query("SELECT * FROM patient_data WHERE id='$patient_id'");
					$pat_row = imw_fetch_assoc($pat_qry);
					$reff_phy_id = $pat_row['primary_care_id'];
					$default_facility=$pat_row['default_facility'];
				}
			}
			$billing_type=3;
			if($grp_detail[$gro_id]['group_anesthesia']>0){
				$billing_type=1;
			}else if($grp_detail[$gro_id]['group_institution']>0){
				$get_ins_type = imw_query("SELECT * FROM insurance_companies WHERE id='$primaryInsCoId'");
				$get_ins_type_row = imw_fetch_array($get_ins_type);
				if($get_ins_type_row['institutional_type']=="INST_PROF"){
					$billing_type=3;
				}else{
					$billing_type=2;
				}
			}
			
			if(strtolower($bl_pt_home_facility)=="yes" && $default_facility>0){
				$main_facility_id=$fac_pos_data_arr[$default_facility];
			}else if($_SESSION['login_facility']>0){
				$main_facility_id=$_SESSION['login_facility'];
			}else if($hq_fac_pos_data_arr[1]>0){
				$main_facility_id=$hq_fac_pos_data_arr[1];
			}else{
				$main_facility_id=$fac_pos_data_arr[$posFacilityId];
			}
			
			$admit_date=$disch_date=$acc_anes_time=$acc_anes_unit="";
			if($getSuperbillPostedStatusRow['anes_start_time']!="00:00:00"){
				$admit_date=$dateOfService.' '.$getSuperbillPostedStatusRow['anes_start_time'];
			}
			if($getSuperbillPostedStatusRow['anes_stop_time']!="00:00:00"){
				$disch_date=$dateOfService.' '.$getSuperbillPostedStatusRow['anes_stop_time'];
				
			}
			if($admit_date!=""){
				$anes_start_time_exp=explode(":",$getSuperbillPostedStatusRow['anes_start_time']);
				$anes_stop_time_exp=explode(":",$getSuperbillPostedStatusRow['anes_stop_time']);
				$acc_anes_time = ((($anes_stop_time_exp[0]-$anes_start_time_exp[0])*60)+($anes_stop_time_exp[1]-$anes_start_time_exp[1]));
				$acc_anes_unit=number_format(($acc_anes_time/$anes_time_divisor),2);
			}
			
			//----------------- Insert Data in Accounting -----------------//	
			$insertChargesStr = "INSERT INTO patient_charge_list SET entered_date='".date('Y-m-d')."',entered_time='".date('H:i:s')."',
								operator_id='".$_SESSION['authId']."',vipStatus='$vipSuperBill',encounter_id='$encounterId',
								patient_id='$patient_id',case_type_id='$insCaseId',facility_id='$posFacilityId',billing_facility_id='$main_facility_id',
								primaryInsuranceCoId='$primaryInsCoId',secondaryInsuranceCoId='$secondaryInsCoId',tertiaryInsuranceCoId='$tertiaryInsCoId',
								date_of_service='$dateOfService',reff_phy_id = '$reff_phy_id',submitted='false',superbillFormId='$idSuperBill',
								payment_status='Pending',gro_id='$gro_id',sch_app_id='$sch_app_id',superbillPosted='true',
								all_dx_codes='$arr_dx_codes',enc_icd10='$enc_icd10',billing_type='$billing_type',
								admit_date='$admit_date',disch_date='$disch_date',acc_anes_time='$acc_anes_time',acc_anes_unit='$acc_anes_unit'";
			$insertChargesStr = imw_query($insertChargesStr);
			$chargeListId = imw_insert_id();
			
			$phy_qry = imw_query("SELECT * FROM users WHERE id='$primaryPhysicianId'");
			$phy_detail = imw_fetch_assoc($phy_qry);
			
			if(in_array($phy_detail['user_type'],$GLOBALS['arrValidCNPhy'])){
				$primaryPhysicianId_cn=$primaryPhysicianId;
			}
			if($reff_phy_id>0){
				$reff_phy_id_cn=$reff_phy_id;
			}else{
				if($phy_detail['lname'] && $phy_detail['fname']){
					$refPhySicianLName = $phy_detail['lname'];
					$refPhySicianFName = $phy_detail['fname'];
				}
				
				$ref_phy_qry = imw_query("select physician_Reffer_id,FirstName,LastName  from refferphysician where FirstName = '".addslashes($refPhySicianFName)."' and LastName = '".addslashes($refPhySicianLName)."' and delete_status ='0' order by FirstName asc limit 0,1");
				$ref_phy_res_id = imw_fetch_assoc($ref_phy_qry);
				$reff_phy_id_cn=$ref_phy_res_id['physician_Reffer_id'];
			}
			$totalFeeAmt=0;
			$totalUnits=0;
			$copayAmt=0;
			$refractionAmt=0;
					
			$referral_arr=array();
			if($referal_req_pri=='Yes' || $referal_req_sec=='Yes' || $referal_req_ter=='Yes'){
				$chk_reff_date=$dateOfService;
				$reff_qry=imw_query("select reffral_no,reff_type from patient_reff where insCaseid='$insCaseId' 
				and patient_id='$patient_id' and reff_phy_id='$reff_phy_id_cn'
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
				
			$group_ins=imw_query("select gro_id from groups_new where gro_id='$gro_id' and group_institution='1'");
			$group_num=imw_num_rows($group_ins);
			
			$getProcedureInfoStr = "SELECT * FROM procedureinfo WHERE idSuperBill='$superBillId' AND delete_status ='0' order by porder,id";
			$getProcedureInfoQry = imw_query($getProcedureInfoStr);
			$cpt_prac_code_arr=array();
			$display_order=0;
			while($getProcedureInfoRows = imw_fetch_array($getProcedureInfoQry)){
				$display_order++;
				$id = $getProcedureInfoRows['id'];
				$units = $getProcedureInfoRows['units'];
				$cptCode = $getProcedureInfoRows['cptCode'];
				$cpt_prac_code_arr[]=$cptCode;
				
				$vip_ref_not_collect=$row_pol['vip_ref_not_collect'];
				if($billing_amount=='Default'){
					$fee_table_column_id="1";
				}else{
					if($primaryInsCoId>0){
						$qryId = imw_query("select FeeTable from insurance_companies where id = '$primaryInsCoId'");
						$feeColRow = imw_fetch_array($qryId);
						$fee_table_column_id=$feeColRow['FeeTable'];
					}else{
						$fee_table_column_id="1";
					}
				}
				
				//----------------- CPT Detail -----------------//	
				$getCPTPriceQry = imw_query("SELECT a.cpt_fee,b.cpt_fee_id,b.cpt_prac_code,b.cpt_comments,b.not_covered,
											b.cpt_prac_code,b.rev_code,b.cpt_tax FROM cpt_fee_table a,cpt_fee_tbl b
											WHERE b.cpt_prac_code='$cptCode' AND a.cpt_fee_id = b.cpt_fee_id
											AND a.fee_table_column_id = '$fee_table_column_id' 
											and b.delete_status = '0' order by b.status asc");
				$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
				$fee=$getCPTPriceRow['cpt_fee'];
				$cptId = $getCPTPriceRow['cpt_fee_id'];
				$cptPracCode = $getCPTPriceRow['cpt_prac_code'];
				$cpt_comments = $getCPTPriceRow['cpt_comments'];
				$cpt_not_covered = $getCPTPriceRow['not_covered'];
				$str = $getCPTPriceRow['cpt_prac_code'];
				$rev_code = $getCPTPriceRow['rev_code'];
				$cpt_tax = $getCPTPriceRow['cpt_tax'];
				
				if(imw_num_rows($getCPTPriceQry)==0){
					$getCPTPriceQry = imw_query("SELECT a.cpt_fee,b.cpt_fee_id,b.cpt_prac_code,b.cpt_comments,b.not_covered,
												b.cpt_prac_code,b.rev_code,b.cpt_tax FROM cpt_fee_table a,cpt_fee_tbl b
												WHERE b.cpt4_code='$cptCode' AND a.cpt_fee_id = b.cpt_fee_id
												AND a.fee_table_column_id = '$fee_table_column_id' 
												and b.delete_status = '0' order by b.status asc");
					$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
					$fee=$getCPTPriceRow['cpt_fee'];
					$cptId = $getCPTPriceRow['cpt_fee_id'];
					$cptPracCode = $getCPTPriceRow['cpt_prac_code'];
					$cpt_comments = $getCPTPriceRow['cpt_comments'];
					$cpt_not_covered = $getCPTPriceRow['not_covered'];
					$str = $getCPTPriceRow['cpt_prac_code'];
					$rev_code = $getCPTPriceRow['rev_code'];
					$cpt_tax = $getCPTPriceRow['cpt_tax'];
				}
				
				if($vip_ref_not_collect>0 && $vipSuperBill=="true"){	
					if($str=="92015" || $str=="Refraction" || $str=="refraction"){
						$fee=0;
					}
				}
				$totalUnitsFeeAmt = $units*$fee;
				$totalFeeAmt = $totalFeeAmt+$totalUnitsFeeAmt;
				$totalUnits = $totalUnits+$units;
				if(($cptPracCode==92015) || ($cptCode==92015)){
				}else{
					$deductAmt = '0.00';
				}
				$dx1 = $getProcedureInfoRows['dx1'];
				$dx2 = $getProcedureInfoRows['dx2'];
				$dx3 = $getProcedureInfoRows['dx3'];
				$dx4 = $getProcedureInfoRows['dx4'];
				$dx5 = $getProcedureInfoRows['dx5'];
				$dx6 = $getProcedureInfoRows['dx6'];
				$dx7 = $getProcedureInfoRows['dx7'];
				$dx8 = $getProcedureInfoRows['dx8'];
				$dx9 = $getProcedureInfoRows['dx9'];
				$dx10 = $getProcedureInfoRows['dx10'];
				$dx11 = $getProcedureInfoRows['dx11'];
				$dx12 = $getProcedureInfoRows['dx12'];
				
				//----------------- Modifier Detail -----------------//	
				$modifier1=$modifier2=$modifier3=$modifier4="";
				$cpt_txt_val=array();
				$getMod1IdStr = "SELECT mod_prac_code,modifiers_id FROM modifiers_tbl WHERE (mod_prac_code='".$getProcedureInfoRows['modifier1']."'
				or mod_prac_code='".$getProcedureInfoRows['modifier2']."' or mod_prac_code='".$getProcedureInfoRows['modifier3']."' or mod_prac_code='".$getProcedureInfoRows['modifier4']."') AND delete_status = '0'";
				$getMod1IdQry = imw_query($getMod1IdStr);
				while($getMod1IdRow = imw_fetch_array($getMod1IdQry)){
					if($getMod1IdRow['mod_prac_code']==$getProcedureInfoRows['modifier1']){
						$modifier1 = $getMod1IdRow['modifiers_id'];
					}
					if($getMod1IdRow['mod_prac_code']==$getProcedureInfoRows['modifier2']){
						$modifier2 = $getMod1IdRow['modifiers_id'];
					}
					if($getMod1IdRow['mod_prac_code']==$getProcedureInfoRows['modifier3']){
						$modifier3 = $getMod1IdRow['modifiers_id'];
					}
					if($getMod1IdRow['mod_prac_code']==$getProcedureInfoRows['modifier4']){
						$modifier4 = $getMod1IdRow['modifiers_id'];
					}
				}
				
				if($chargeListId>0){	
					if($primary_provider_id_for_reports==0){
						$primary_provider_id_for_reports=$primaryPhysicianId_cn;
					}
					$proc_selfpay_cond="";
					if($cpt_not_covered>0){
						$proc_selfpay_cond=",proc_selfpay='1'";
					}
					$rev_rate="";
					if($group_num>0){
						if(strtolower($billing_global_server_name)=='sheepshead'){
							$rev_rate="24/1408";
						}else if(strtolower($billing_global_server_name)=='shoreline'){
							$rev_rate="A3/".$totalUnitsFeeAmt;
						}
					}
					$insertChargesDetailStr = "INSERT INTO patient_charge_list_details SET
						charge_list_id = '$chargeListId',patient_id = '$patient_id',procCode = '$cptId',
						start_date = '$dateOfService',primaryProviderId = '$primaryPhysicianId_cn',secondaryProviderId = '$primaryPhysicianId_cn',
						notes = '$cpt_comments',units = '$units',procCharges = '$fee',totalAmount = '$totalUnitsFeeAmt',paidForProc = '0.00',
						balForProc = '$totalUnitsFeeAmt',approvedAmt = '$totalUnitsFeeAmt',deductAmt = '$deductAmt',
						newBalance = '$totalUnitsFeeAmt',type_of_service = '$tosId',place_of_service = '$posId',
						posFacilityId = '$posFacilityId',modifier_id1 = '$modifier1',modifier_id2 = '$modifier2',
						modifier_id3 = '$modifier3',modifier_id4 = '$modifier4',diagnosis_id1 = '$dx1',diagnosis_id2 = '$dx2',diagnosis_id3 = '$dx3',
						diagnosis_id4 = '$dx4',diagnosis_id5 = '$dx5',diagnosis_id6 = '$dx6',diagnosis_id7 = '$dx7',
						diagnosis_id8 = '$dx8',diagnosis_id9 = '$dx9',diagnosis_id10 = '$dx10',diagnosis_id11 = '$dx11',
						diagnosis_id12 = '$dx12',referral = '$referral',optional_referral = '$optional_referral',
						credits = 'false',idSuperBill = '$superBillId',procedureInfoId = '$id',display_order = '$display_order',
						creditLessBalance = '$totalUnitsFeeAmt',entered_date='".date('Y-m-d H:i:s')."',operator_id='".$_SESSION['authId']."',
						rev_code='$rev_code',primary_provider_id_for_reports='$primary_provider_id_for_reports',rev_rate='$rev_rate' $proc_selfpay_cond";
					$insertChargesDetailStr = imw_query($insertChargesDetailStr);
					if($cpt_tax>0){
						$cpt_txt_val_arr[]=$totalUnitsFeeAmt;
					}
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
				charge_list_id = '$chargeListId',patient_id = '$patient_id',procCode = '$cpt_tax_id',
				start_date = '$dateOfService',primaryProviderId = '$primaryPhysicianId_cn',secondaryProviderId = '$primaryPhysicianId_cn',
				units = '1',procCharges = '$cpt_txt_val',totalAmount = '$cpt_txt_val',
				balForProc = '$cpt_txt_val',approvedAmt = '$cpt_txt_val',newBalance = '$cpt_txt_val',
				type_of_service = '$tosId',place_of_service = '$posId',posFacilityId = '$posFacilityId',
				idSuperBill = '$superBillId',procedureInfoId = '',display_order = '$tax_display_order',
				entered_date='".date('Y-m-d H:i:s')."',operator_id='".$_SESSION['authId']."',
				primary_provider_id_for_reports='$primary_provider_id_for_reports',proc_selfpay='1'";
				$insertChargesDetailStr = imw_query($insertChargesDetailStr);
			}
				
			$cpt_prac_code_imp=implode(',',$cpt_prac_code_arr);
			$copay_collect_chk=copay_apply_chk($cpt_prac_code_imp,$primaryInsCoId,$secondaryInsCoId);
			
			$copay_policies = ChkSecCopay_collect($primaryInsId);
			$secCopay_collect_chk=$copay_policies;	
			
			if($copay_collect_chk[0]==true && $copay_collect_chk[1]==true){
				$pri_copay=$chk_pri_copay;
				if($secCopay_collect_chk=='Yes'){
					$sec_copay=$chk_sec_copay;
				}
				$copay=$pri_copay+$sec_copay;
			}else if($copay_collect_chk[0]==true){
				$pri_copay=$chk_pri_copay;
				$sec_copay=0;
				$copay=$pri_copay+$sec_copay;
			}else if($copay_collect_chk[1]==true && $secCopay_collect_chk=='Yes'){
				$pri_copay=0;
				$sec_copay=$chk_sec_copay;
				$copay=$pri_copay+$sec_copay;
			}
			
			$coPayNotRequired=0;
			$coPayNotRequired2=0;
			
			if($vipSuperBill=="true" && $vip_copay_not_collect>0){
				if($pri_copay>0){
					$coPayNotRequired = 1;
				}
				if($sec_copay>0){
					$coPayNotRequired2 = 1;
				}
			}	
			$patientAmtToPay = $refractionAmt+$copay;		
			
			if($primary_provider_id_for_reports==0){
				$primary_provider_id_for_reports=$primaryPhysicianId_cn;
			}
			
			if($auth_req_pri=='Yes' || $auth_req_sec=='Yes' || $auth_req_ter=='Yes'){
				$auth_id=$auth_no=$auth_amount="";
				$auth_whr=" and (auth_provider = '".$primaryPhysicianId_cn."' or auth_provider='0')";
				$auth_qry=imw_query("select patient_auth.auth_name,patient_auth.a_id,patient_auth.AuthAmount,patient_auth.auth_cpt_codes,patient_auth.ins_type,
							insurance_data.auth_required 
							from patient_auth join insurance_data on insurance_data.id = patient_auth.ins_data_id
							where insurance_data.pid = '$patient_id' and insurance_data.ins_caseid = '$insCaseId'
							and (patient_auth.end_date='0000-00-00' or patient_auth.end_date >= '$dateOfService') 
							and patient_auth.auth_date <= '$dateOfService' and patient_auth.no_of_reffs > '0' and patient_auth.auth_status='0' $auth_whr
							order by patient_auth.end_date desc,patient_auth.a_id desc");
				while($auth_row=imw_fetch_array($auth_qry)){
					$auth_cpt_codes_exp=array();
					if(trim($auth_row['auth_cpt_codes'])!=""){
						$auth_cpt_codes_exp=explode(';',$auth_row['auth_cpt_codes']);
					}
					if(array_intersect($cpt_prac_code_arr,$auth_cpt_codes_exp) || count($auth_cpt_codes_exp)==0){
						if($auth_no==""){
							$auth_id=$auth_row['a_id'];
							$auth_no=$auth_row['auth_name'];
							$auth_amount=$auth_row['AuthAmount'];
						}
					}
				}
			}
			
			$auth_up="";
			if($auth_no!=""){
				$auth_up=",auth_id='$auth_id',auth_no='$auth_no',auth_amount='$auth_amount'";
			}
				
			$updateEncounterAmountStr = "UPDATE patient_charge_list SET primaryProviderId = '$primaryPhysicianId_cn',secondaryProviderId = '$primaryPhysicianId_cn',
										reff_phy_id = '$reff_phy_id_cn',referral = '$referral',optional_referral = '$optional_referral',
										totalAmt = '$totalFeeAmt',copay='$copay',pri_copay='$pri_copay',sec_copay='$sec_copay',
										coPayNotRequired='$coPayNotRequired',coPayNotRequired2='$coPayNotRequired2',
										primary_provider_id_for_reports='$primary_provider_id_for_reports' $auth_up
										WHERE charge_list_id='$chargeListId'";
			$updateEncounterAmountQry = imw_query($updateEncounterAmountStr);
			$encounter_id = $encounterId;
			include('manageEncounterAmounts.php');
		
			if($copay){
				$refractionAmt = $refractionAmt+$copay;
			}
			$todaysCharges = $totalFeeAmt;
		
			if($ins_case_type == 1){
				$todaysPayment = $patientAmtToPay;
			}else{
				$todaysPayment = $todaysCharges;
			}
			
			//----------------- Update Superbill -----------------//	
			imw_query("UPDATE superbill SET postedStatus='1',postedDate='".date('Y-m-d')."',insuranceCaseId='$insCaseId',todaysCharges='$todaysCharges',todaysPayment='$todaysPayment' WHERE idSuperBill='$superBillId'");
			
			echo"<script type='text/javascript'>window.location.href='accounting_view.php?encounter_id=".$encounterId."&uniqueurl=".$encounterId."'</script>";
			exit();
		}
	}
}

$sb_data_arr=array();
if($_REQUEST["list_type"] == "1"){
	$sb_whr = "";	
}else{
	$sb_whr=" and sb.del_status='0'";
}

//----------------- Superbill Detail -----------------//
$qry =imw_query("SELECT sb.idSuperBill,sb.gro_id,sb.primary_provider_id_for_reports,sb.insuranceCaseId,sb.encounterId,sb.dateOfService,
				sb.del_status,sb.vipSuperBill,sb.pri_ins_id,sb.todaysPayment,sb.ascId,sb.postedStatus,sb.sch_app_id,
				proc_info.cptCode,proc_info.units,proc_info.delete_status
 				FROM superbill as sb left join procedureinfo as proc_info on sb.idSuperBill=proc_info.idSuperBill
				WHERE sb.patientId = '$patient_id' AND sb.postedStatus='0' AND sb.merged_with='0' and proc_info.delete_status='0' 
				$sb_whr order by sb.encounterId desc");
if(imw_num_rows($qry)>0){
	while($row = imw_fetch_assoc($qry)){
		$sb_data_arr[$row['idSuperBill']]['idSuperBill']=$row['idSuperBill'];
		$sb_data_arr[$row['idSuperBill']]['gro_id']=$row['gro_id'];
		$sb_data_arr[$row['idSuperBill']]['primary_provider_id_for_reports']=$row['primary_provider_id_for_reports'];
		$sb_data_arr[$row['idSuperBill']]['insuranceCaseId']=$row['insuranceCaseId'];
		$sb_data_arr[$row['idSuperBill']]['encounterId']=$row['encounterId'];
		$sb_data_arr[$row['idSuperBill']]['dateOfService']=$row['dateOfService'];
		$sb_data_arr[$row['idSuperBill']]['del_status']=$row['del_status'];
		$sb_data_arr[$row['idSuperBill']]['vipSuperBill']=$row['vipSuperBill'];
		$sb_data_arr[$row['idSuperBill']]['pri_ins_id']=$row['pri_ins_id'];
		$sb_data_arr[$row['idSuperBill']]['todaysPayment']=$row['todaysPayment'];
		$sb_data_arr[$row['idSuperBill']]['ascId']=$row['ascId'];
		$sb_data_arr[$row['idSuperBill']]['postedStatus']=$row['postedStatus'];
		$sb_data_arr[$row['idSuperBill']]['sch_app_id']=$row['sch_app_id'];
		$sb_data_arr[$row['idSuperBill']]['cptCode'][]=$row['cptCode'];
		$sb_data_arr[$row['idSuperBill']]['units'][]=$row['units'];
		$sb_data_arr[$row['idSuperBill']]['delete_status'][]=$row['delete_status'];
		$sb_proc_arr[$row['cptCode']]=$row['cptCode'];
		$sb_prov_arr[$row['primary_provider_id_for_reports']]=$row['primary_provider_id_for_reports'];
		if($row['gro_id']>0){
			$sb_gro_arr[$row['gro_id']]=$row['gro_id'];
		}
	}
}

if(count($sb_data_arr)>0){
	$act_ins_caseid_arr=$act_ins_comp_arr=$ins_comp_fee_arr=$usr_arr=$app_arr=array();
	
	//----------------- Group Detail -----------------//
	if(count($sb_gro_arr)>0){
		$sb_data_imp=implode(',',$sb_gro_arr);
		$grp_qry=imw_query("select group_color,gro_id,name,group_institution,group_anesthesia from groups_new where gro_id in($sb_data_imp)");
		while($grp_row=imw_fetch_assoc($grp_qry)){
			$grp_detail[$grp_row['gro_id']]=$grp_row;	
			$group_name[$grp_row['gro_id']]=ucfirst($grp_row['name']);
		}
	}
	
	//----------------- Insurance Detail -----------------//
	$getInsPriCoQry=imw_query("SELECT ins_caseid,provider FROM insurance_data WHERE pid='$patient_id' AND actInsComp = '1' AND provider!=''");
	while($getInsPriCoRow=imw_fetch_assoc($getInsPriCoQry)){
		$act_ins_caseid_arr[$getInsPriCoRow['ins_caseid']]=$getInsPriCoRow['ins_caseid'];
		$act_ins_comp_arr[$getInsPriCoRow['provider']]=$getInsPriCoRow['provider'];
	}
	
	if(count($act_ins_comp_arr)>0){
		$act_ins_comp_imp=implode("','",$act_ins_comp_arr);
		$get_ins_comp = imw_query("select id,FeeTable from insurance_companies where id in('$act_ins_comp_imp')");
		while($get_ins_comp_row=imw_fetch_assoc($get_ins_comp)){
			$ins_comp_fee_arr[$get_ins_comp_row['id']]=$get_ins_comp_row['FeeTable'];
		}
	}
	
	if(count($act_ins_caseid_arr)>0){
		$act_ins_caseid_imp=implode(',',$act_ins_caseid_arr);
		$getInsCase=imw_query("SELECT insurance_case_types.case_name,insurance_case_types.normal,insurance_case.ins_caseid 
						FROM insurance_case join insurance_case_types on insurance_case.ins_case_type=insurance_case_types.case_id
						WHERE insurance_case.patient_id='$patient_id' and insurance_case.case_status='Open' 
						and insurance_case.ins_caseid in($act_ins_caseid_imp)");				
		while($getInsCaseRow=imw_fetch_assoc($getInsCase)){
			$openInsCaseName=$getInsCaseRow['case_name'];
			$openInsCaseId=$getInsCaseRow['ins_caseid'];
			$openInsCaseNameID=$openInsCaseName." - ".$openInsCaseId;
			$caseType = $openInsCaseName." - ".$openInsCaseId;
			$openInsCaseNameIDArray[]= array($caseType);
			$openInsCaseIdArray[]=$openInsCaseId;
			$insCasesArr[]=$caseType;
			$act_ins_case_data_arr[$openInsCaseId]=$getInsCaseRow;
			if($getInsCaseRow['normal']=='1'){
				$normal_ins_case_id=$openInsCaseId;
			}
			$act_ins_case_id_arr[]=$openInsCaseId;
		}	
	}
	
	//----------------- CPT Detail -----------------//
	if(count($sb_proc_arr)>0){
		$sb_proc_imp=implode("','",$sb_proc_arr);
		$qry = imw_query("select cpt_fee_tbl.cpt_prac_code,cpt_fee_table.fee_table_column_id,cpt_fee_table.cpt_fee
				from cpt_fee_tbl join cpt_fee_table on cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id
				where (cpt_fee_tbl.cpt_prac_code in('$sb_proc_imp')) AND cpt_fee_tbl.delete_status = '0'");
		while($get_row=imw_fetch_assoc($qry)){
			$cpt_fee_arr[$get_row['cpt_prac_code']][$get_row['fee_table_column_id']]=$get_row['cpt_fee'];
		}
	}
	
	//----------------- User Detail -----------------//
	if(count($sb_prov_arr)>0){
		$sb_prov_imp=implode(',',$sb_prov_arr);
		$getPhysicianNameQry=imw_query("SELECT fname,lname,mname,id FROM users where id in($sb_prov_imp)");
		while($getPhysicianNameRow=imw_fetch_assoc($getPhysicianNameQry)){
			$phy_arr['FIRST_NAME']=$getPhysicianNameRow['fname'];
			$phy_arr['LAST_NAME']=$getPhysicianNameRow['lname'];
			$phy_arr['MIDDLE_NAME']=$getPhysicianNameRow['mname'];
			$usr_arr[$getPhysicianNameRow['id']]=changeNameFormat($phy_arr);
		}
	}
	
	//----------------- Appointments Detail -----------------//
	$qry_case_id = imw_query("select id,sa_app_start_date,case_type_id,sa_facility_id from schedule_appointments 
				   where sa_patient_id='$patient_id' and sa_patient_app_status_id not in(3,18,201,203)");												
	while($fet_case_list=imw_fetch_assoc($qry_case_id)){
		$app_arr[$fet_case_list['id']]=$fet_case_list;
	}
}

$group_name_final=@implode(', ',$group_name);
?>
<div class="table-responsive" style="height:<?php echo $_SESSION['wn_height']-310;?>px; overflow-x:auto; width:100%;">
	<form name="superbill_frm" id="superbill_frm" action="superbill_charges.php" method="post">
    	<input type="hidden" name="post_action" id="post_action" value="" />
        <table class="table table-bordered table-hover table-striped table-condensed">
            <thead>
                <tr class='grythead'>
                    <th>
                        <div class="checkbox">
                            <input type="checkbox" name="chkbx_all" id="chkbx_all" onClick="return chk_all();"/>
                            <label for="chkbx_all"></label>
                        </div>
                    </th>
                    <th>
                    	<span class="pull-right btn btn-info" style="width:80px;" onclick="open_superbill('<?php echo $_REQUEST['list_type']; ?>');">
                        	<?php if($_REQUEST['list_type']==1){echo"All";}else{echo "Active";} ?>
                        </span>
                    	<span>DOS</span>
                    </th>
                    <th>E. Id</th>
                    <th>Insurance Case</th>
                    <th>Physician</th>
                    <th>Procedure</th>
                    <th>Units</th>
                    <th>Total Charges</th>
                    <th>Balance</th>
                    <th>PDF</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(count($sb_data_arr)>0){
                $seq=0;
                foreach($sb_data_arr as $sb_key=>$sb_val){
                    $seq++;
                    $sb_data=$sb_data_arr[$sb_key];
                    $sb_dos = get_date_format($sb_data['dateOfService']);
                    $encounterId=$sb_data['encounterId'];
                    $postedStatus=$sb_data['postedStatus'];
                    $insuranceCaseId=$sb_data['insuranceCaseId'];
                    $sup_date=$sb_data['dateOfService'];
                    $curCaseId=$sa_facility_id=$insurance_case_id=0;
                    $case_name=$case_name_id=$phy_name=$show_cpt_code=$total_charges=$total_unit_charges=$bg_color=$sb_class=$multi_cpt="";
                    
                    foreach($app_arr as $app_key=>$app_val){
                        if($app_arr[$app_key]['id']==$sb_data['sch_app_id']){
                            if($app_arr[$app_key]['case_type_id']>0){
                                $curCaseId=$app_arr[$app_key]['case_type_id'];
                            }
                            if($app_arr[$app_key]['sa_facility_id']>0){
                                $sa_facility_id=$app_arr[$app_key]['sa_facility_id'];
                            }
                        }elseif($app_arr[$app_key]['sa_app_start_date']==$sup_date){
                            if($app_arr[$app_key]['case_type_id']>0){
                                $curCaseId=$app_arr[$app_key]['case_type_id'];
                            }
                            if($app_arr[$app_key]['sa_facility_id']>0){
                                $sa_facility_id=$app_arr[$app_key]['sa_facility_id'];
                            }
                        }
                    }
                    
                    if($sb_data['insuranceCaseId']==0){
                        if($curCaseId<=0){
                            $insurance_case_id=$act_ins_case_id_arr[0];
                        }else{
                            $insurance_case_id=$curCaseId;
                        }
                        if($insurance_case_id>0){
                            $sb_data['insuranceCaseId']=$insurance_case_id;
                        }else{
                            $sb_data['insuranceCaseId']=$normal_ins_case_id;
                        }
                        $up_case=imw_query("update superbill set insuranceCaseId='".$sb_data['insuranceCaseId']."' where del_status='0' and postedStatus='0' and insuranceCaseId='0' and idSuperBill = '".$sb_data['idSuperBill']."'");
                    }
                    
                    if($sb_data['insuranceCaseId']>0){
                        $case_name=$act_ins_case_data_arr[$sb_data['insuranceCaseId']]['case_name'];
                        $case_name_id=$case_name."-".$sb_data['insuranceCaseId'];
                    }
                    
                    $phy_name=$usr_arr[$sb_data['primary_provider_id_for_reports']];
                    if(count($sb_data['cptCode'])>1){
                        $show_cpt_code="Multi";
                        $multi_cpt=implode('<br>',$sb_data['cptCode']);
                    }else{
                        $show_cpt_code=$sb_data['cptCode'][0];
                    }
                    $total_units=array_sum($sb_data['units']);
                    
                    $vipSuperBill=$sb_data['vipSuperBill'];
                    if($sb_data['insuranceCaseId']=='0' || $sb_data['pri_ins_id']==0 || $billing_amount=='Default'){
                        $fee_table = 1;
                    }else{
                        $fee_table=$ins_comp_fee_arr[$sb_data['pri_ins_id']];
                    }
                    foreach($sb_data['cptCode'] as $proc_key=>$proc_val){
                        $str=$sb_data['cptCode'][$proc_key];	
                        $str_units=$sb_data['units'][$proc_key];
                        $fee=$cpt_fee_arr[$str][$fee_table];
                        if($vip_ref_not_collect>0 && $sb_data['vipSuperBill']>0){
                            if($str=="92015" || strtolower($str)=="refraction"){
                                $fee=0;
                            }
                        }
                        $total_unit_charges=$str_units*$fee;
                        $total_charges=$total_charges+$total_unit_charges;
                    }
                    $balance=$total_charges-$sb_data['todaysPayment'];
                    
                    $sum_unit=$sum_unit+$total_units;
                    $sum_charges=$sum_charges+$total_charges;
                    $sum_balance=$sum_balance+$balance;
                    
                    $group_color=$grp_detail[$sb_data['gro_id']]['group_color'];
                    if($group_color!="" && $group_color!="#FFFFFF"){
                        $bg_color="background-color: ".$group_color;
                    }else{
                        $bg_color="background-color: #FFFFFF";
                    }
                    
                    if($sb_data['del_status']!='0'){
                        $sb_class="text-danger";
                    }
                    
                    $billType	=	'Practice';
                    if($grp_detail[$gro_id]['group_institution'])
                    {
                        $billType	=	'Facility';
                    }
                    elseif($grp_detail[$gro_id]['group_anesthesia'])
                    {
                        $billType	=	'Anesthesia';
                    }
                    $sup_link="";	
                    if($sb_data['del_status']=='0'){$sup_link='href="javascript:add_charge_list(\''.$sb_data['idSuperBill'].'\');"';}
                ?>
                <tr <?php echo show_gro_color($group_color); ?> class="text-center">
                    <td>
                        <?php if($sb_data['del_status']=='0'){?>
                            <div class="checkbox">
                                <input name="chkbx[]" type="checkbox" id="chkbx<?php echo $sb_data['idSuperBill']; ?>" class="chk_box_css" value="<?php echo $sb_data['idSuperBill']; ?>"/>
                                <label for="chkbx<?php echo $sb_data['idSuperBill']; ?>"></label>
                            </div>
                        <?php } ?>
                    </td>
                    <td>
                        <a <?php echo $sup_link; ?> class="<?php echo $sb_class; ?>">
                            <?php if($sb_dos=="") echo "-"; else echo $sb_dos; ?>
                        </a>
                    </td>
                    <td>
                        <a <?php echo $sup_link; ?> class="<?php echo $sb_class; ?>">	
                            <?php echo $encounterId; ?>
                        </a>
                    </td>
                    <td id="insCaseTd<?php echo $sb_data['idSuperBill']; ?>">
                        <a <?php echo $sup_link; ?> class="<?php echo $sb_class; ?>">
                            <?php if(count($insCasesArr)==0){ echo "No insurance company selected.";}else{ echo $case_name_id; }?>
                        </a>
                    </td>
                    <td>
                        <a <?php echo $sup_link; ?> class="<?php echo $sb_class; ?>">
                            <?php echo $phy_name; ?>
                        </a>
                    </td>
                    <td>
                        <a <?php echo $sup_link; ?> class="<?php echo $sb_class; ?>" <?php echo show_tooltip($multi_cpt); ?>>
                            <?php echo $show_cpt_code; ?>
                        </a>
                    </td>
                    <td class="text-right">
                        <a <?php echo $sup_link; ?> class="<?php echo $sb_class; ?>">
                            <?php echo $total_units; ?>
                        </a>
                    </td>
                    <td class="text-right">
                        <a <?php echo $sup_link; ?> class="<?php echo $sb_class; ?>">
                            <?php echo numberformat($total_charges,2); ?>
                        </a>
                    </td>
                    <td class="text-right">
                        <a <?php echo $sup_link; ?> class="<?php echo $sb_class; ?>"> 
                            <?php echo numberformat($balance,2); ?>
                        </a>
                    </td>
                    <td>
                        <?php if($sb_data['ascId']>0){?>
                            <a href="javascript:void(0);" onClick="window.open('discharge_summary_sheet_pdf.php?ascId=<?php echo $sb_data['ascId']; ?>&sch_facility_id=<?php echo $sa_facility_id; ?>&superBillId=<?php echo $sb_data['idSuperBill']; ?>&billType=<?php echo $billType; ?>','PDF','menubar=0,resizable=yes');">
                                <span class="glyphicon glyphicon-file"></span>
                            </a>
                        <?php } else{ echo "-"; }?>
                    </td>
                    <td>
                        <?php
                            if($sb_data['del_status'] == '0'){
                        ?>	
                            <a href="javascript:edit_superbill(<?php echo $sb_data['idSuperBill']; ?>,<?php echo $encounterId; ?>);">
                                <span class="glyphicon glyphicon-pencil"></span>
                            </a>
                        <?php
                            }
                        ?>
                    </td>
                </tr>
                <?php    
                }
            ?>
                <tr class="purple_bar">
                    <td colspan="5" class="white_bar"></td>
                    <td class="text-right">Total</td>
                    <td class="text-right"><?php echo $sum_unit; ?></td>
                    <td class="text-right"><?php echo numberformat($sum_charges,2); ?></td>
                    <td class="text-right"><?php echo numberformat($sum_balance,2); ?></td>
                    <td colspan="2"></td>
                </tr>
            <?php	
            }else{
            ?>
                <tr>
                    <td colspan="11" class="text-center lead"><?php echo imw_msg('no_rec'); ?></td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
     </form>   
</div>
<script type="text/javascript">
	 <?php if(count($sb_data_arr)>0){ ?>
		var ar = [["new_superbill","New SuperBill","top.fmain.ajax_chart_dos_fun('chart_note_dos');"],
				  ["delete_fun","Void","top.fmain.del_records('superbill_frm');"]];
		top.btn_show("ACCOUNT",ar);
	<?php }else{ ?>
		var ar = [["new_superbill","New SuperBill","top.fmain.ajax_chart_dos_fun('chart_note_dos');"]];
		top.btn_show("ACCOUNT",ar);		  
	<?php }?>
</script>
<?php require_once("acc_footer.php");?>