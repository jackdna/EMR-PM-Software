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
$title = "Charges Ledger";
require_once("acc_header.php"); 

$dosFrom = $_REQUEST['dosFrom'];
$dosTo = $_REQUEST['dosTo'];
$insCaseTypeId = $_REQUEST['insCaseType'];
$encounter_id = $_REQUEST['encounter_id'];
$active_enc = $_REQUEST['active_enc'];

//----------------- Patient Detail -----------------//
$qry = imw_query("select noBalanceBill,DOB from patient_data where pid = '$patient_id'");
$row = imw_fetch_array($qry);
$noBalanceBill = $row['noBalanceBill'];
$date_of_birth = get_date_format($row['DOB']);

//------------------- DELETE ENCOUNTER DETAILS -------- -----------//
if($_REQUEST['chkbx']){
	foreach($_REQUEST['chkbx'] as $key => $val){
		if($val>0){
			del_enc($val,$_REQUEST["claim_ctrl_pri_".$val]);
            
            $params=array();
            $params['section']='encounter_deleted';
            $params['obj_value']=trim($val);
            $serialized_arr = serialize($params);
            include_once("../../interface/common/assign_new_task.php");
            
		}
	}
}

$pag_limit_to=20;
if($_REQUEST['acc_pag']>1){
	$acc_pag_limit=($_REQUEST['acc_pag']-1)*$pag_limit_to;
}else{
	$acc_pag_limit=0;
}

if($dosFrom){
	$From = getDateFormatDB($dosFrom);
	$To = getDateFormatDB($dosTo);
}
if($insCaseTypeId){
	$insCaseIdPos = strpos($insCaseTypeId, "-");
	$insCaseId = substr($insCaseTypeId, $insCaseIdPos+1);
	$insCaseId = trim($insCaseId);
}
if($encounter_id>0){
	$enc_whr=" and encounter_id='$encounter_id'";
}
if($dosFrom){
	$dat_serv_whr=" and date_of_service >= '$From' and date_of_service <= '$To'";
}
if($insCaseTypeId){
	$case_id_whr=" and case_type_id='$insCaseId'";
}
if(count($_REQUEST['grp_srh'])>0){
	$grp_srh_str=implode("','",$_REQUEST['grp_srh']);
	$grp_id_whr=" and gro_id in('$grp_srh_str')";
}

if($encounter_id=="all"){
	$active_enc_whr="";
	$active_chld_whr="";
}else{
	$active_enc_whr="del_status='0' and ";
	$active_chld_whr="a.del_status='0' and ";
}
if($_REQUEST['unpaid_chrg']){
	$pending_charges_whr=" and totalBalance>0";
}
$i=0;
//----------------- Encounters Detail -----------------//
$getChargesListStr = "SELECT DISTINCT charge_list_id,gro_id,submitted,firstSubmitDate,encounter_id,overPayment,superbillFormId,
	copay,copayPaid,postedAmount,coPayNotRequired,coPayWriteOff,totalAmt,approvedTotalAmt,deductibleTotalAmt,creditAmount,amtPaid,totalBalance,
	case_type_id,primaryInsuranceCoId,secondaryInsuranceCoId,tertiaryInsuranceCoId,primaryProviderId,secondaryProviderId,tertiaryProviderId,date_of_service,
	primary_paid,secondary_paid,tertiary_paid,primarySubmit,secondarySubmit,tertairySubmit,moaQualifier,auth_no,insPaidAmt,del_status,claim_ctrl_pri,claim_status
	FROM patient_charge_list WHERE patient_id='$patient_id' $pending_charges_whr $case_id_whr $grp_id_whr ORDER BY date_of_service DESC,charge_list_id  desc";
$getChargesListQry = imw_query($getChargesListStr);
while($row_chl_data=imw_fetch_array($getChargesListQry)){
	if($_REQUEST['acc_pag']>1){
		$page_start_rec=(($_REQUEST['acc_pag']-1)*$pag_limit_to)+1;
		$page_end_rec = ($page_start_rec+$pag_limit_to)-1;
	}else{
		$page_start_rec=1;
		$page_end_rec = $pag_limit_to;
	}
	
	$show_enc_srh="";
	$show_dos_srh="";
	if($_REQUEST['encounter_id']=="all"){
		$show_enc_srh="yes";
	}else if($_REQUEST['encounter_id']>0){
		if($_REQUEST['encounter_id']==$row_chl_data['encounter_id']){
			$show_enc_srh="yes";
		}
	}else if($row_chl_data['del_status']=='0'){
		$show_enc_srh="yes";
	}
	if(($dosFrom!="" && $row_chl_data['date_of_service']>=$From && $row_chl_data['date_of_service']<=$To) || $dosFrom==""){
		$show_dos_srh="yes";
	}
	if($show_enc_srh=="yes" && $show_dos_srh=="yes"){
		$i=$i+1;
		if($row_chl_data['submitted']=='true'){
			$final_sum_posted=$final_sum_posted+$row_chl_data['postedAmount'];
		}
		
		if($row_chl_data['copayPaid'] == 1){
			$final_sum_paid = $final_sum_paid + $row_chl_data['copay'];
		}
		$final_sum_paid=$final_sum_paid+$row_chl_data['amtPaid'];
		
		if($row_chl_data['overPayment']>0){
			$final_sum_ovrpay=$final_sum_ovrpay+$row_chl_data['overPayment'];
		}else{
			$final_sum_balance=$final_sum_balance+$row_chl_data['totalBalance'];
		}
		
		$chl_enc_arr[]=$row_chl_data['encounter_id'];
		
	}
	if($show_enc_srh=="yes" && $show_dos_srh=="yes" && $i>=$page_start_rec && $i<=$page_end_rec){
		$chl_id_arr[]['charge_list_id']=$row_chl_data['charge_list_id'];
		$group_id_arr[]['gro_id']=$row_chl_data['gro_id'];
		$submitted_dat_arr[]['submitted']=$row_chl_data['submitted'];
		$posted_amt_arr[]['postedAmount']=$row_chl_data['postedAmount'];
		$ovr_pay_arr[]['overPayment']=$row_chl_data['overPayment'];
		$copay_amt_arr[]['copay']=$row_chl_data['copay'];
		$copay_paid_arr[]['copayPaid']=$row_chl_data['copayPaid'];
		$copay_not_req_arr[]['coPayNotRequired']=$row_chl_data['coPayNotRequired'];
		$copay_writeoff_arr[]['coPayWriteOff']=$row_chl_data['coPayWriteOff'];
		$total_amt_arr[]['totalAmt']=$row_chl_data['totalAmt'];
		$approve_amt_arr[]['approvedTotalAmt']=$row_chl_data['approvedTotalAmt'];
		$deduct_amt_arr[]['deductibleTotalAmt']=$row_chl_data['deductibleTotalAmt'];
		$credit_amt_arr[]['creditAmount']=$row_chl_data['creditAmount'];
		$amt_paid_arr[]['amtPaid']=$row_chl_data['amtPaid'];
		$total_balance_arr[]['totalBalance']=$row_chl_data['totalBalance'];
		$case_type_id_arr[]['case_type_id']=$row_chl_data['case_type_id'];
		$pri_ins_id_arr[]['primaryInsuranceCoId']=$row_chl_data['primaryInsuranceCoId'];
		$sec_ins_id_arr[]['secondaryInsuranceCoId']=$row_chl_data['secondaryInsuranceCoId'];
		$tri_ins_id_arr[]['tertiaryInsuranceCoId']=$row_chl_data['tertiaryInsuranceCoId'];
		$primary_paid_arr[]['primary_paid']=$row_chl_data['primary_paid'];
		$secondary_paid_arr[]['secondary_paid']=$row_chl_data['secondary_paid'];
		$tertiary_paid_arr[]['tertiary_paid']=$row_chl_data['tertiary_paid'];
		$primarySubmit_arr[]['primarySubmit']=$row_chl_data['primarySubmit'];
		$secondarySubmit_arr[]['secondarySubmit']=$row_chl_data['secondarySubmit'];
		$tertairySubmit_arr[]['tertairySubmit']=$row_chl_data['tertairySubmit'];
		$moaQualifier_arr[]['moaQualifier']=$row_chl_data['moaQualifier'];
		$auth_no_arr[]['auth_no']=$row_chl_data['auth_no']; 
		$insPaidAmt_arr[]['insPaidAmt']=$row_chl_data['insPaidAmt'];
		$del_status_arr[]['del_status']=$row_chl_data['del_status'];
		$claim_ctrl_pri_arr[]['claim_ctrl_pri']=$row_chl_data['claim_ctrl_pri'];
		$sb_id_arr[]['superbillFormId']=$row_chl_data['superbillFormId'];
		$ar_claim_status_arr[]['claim_status']=$row_chl_data['claim_status'];
		
		if($row_chl_data['primaryInsuranceCoId']){
			$ins_arr[]=$row_chl_data['primaryInsuranceCoId'];
			$ins_chl_arr[$row_chl_data['charge_list_id']]=$row_chl_data['primaryInsuranceCoId'];
		}
		if($row_chl_data['secondaryInsuranceCoId']){
			$ins_arr[]=$row_chl_data['secondaryInsuranceCoId'];
		}
		if($row_chl_data['tertiaryInsuranceCoId']){
			$ins_arr[]=$row_chl_data['tertiaryInsuranceCoId'];
		}
		
		$pri_provider_id_arr[]['primaryProviderId']=$row_chl_data['primaryProviderId'];
		$sec_provider_id_arr[]['secondaryProviderId']=$row_chl_data['secondaryProviderId'];
		$tri_provider_id_arr[]['tertiaryProviderId']=$row_chl_data['tertiaryProviderId'];
		if($row_chl_data['primaryProviderId']){
			$provider_arr[]=$row_chl_data['primaryProviderId'];
		}
		if($row_chl_data['secondaryProviderId']){
			$provider_arr[]=$row_chl_data['secondaryProviderId'];
		}
		if($row_chl_data['tertiaryProviderId']){
			$provider_arr[]=$row_chl_data['tertiaryProviderId'];
		}
		
		if($row_chl_data['firstSubmitDate']!='0000-00-00'){
			$posted_dat_arr[]['firstSubmitDate'] = get_date_format($row_chl_data['firstSubmitDate']);
		}else{
			$posted_dat_arr[]['firstSubmitDate'] = '-';
		}
		
		$getEncountersArr[]['encounter_id']=$row_chl_data['encounter_id'];
		
		
		$chl_arr[]=$row_chl_data['charge_list_id'];
		$enc_arr[]=$row_chl_data['encounter_id'];
		if($row_chl_data['date_of_service']!='0000-00-00'){
			$dateOfService = get_date_format($row_chl_data['date_of_service']);
		}else{
			$dateOfService = '-';
		}
		
		$dosArray[]['date_of_service']=$dateOfService;
		
	}
	
	if($row_chl_data['date_of_service']!='0000-00-00'){
		$dateOfService_srh = get_date_format($row_chl_data['date_of_service']);
	}else{
		$dateOfService_srh = '-';
	}
	
	$dos_arr[] = $dateOfService_srh;
	$srh_enc_arr[$row_chl_data['encounter_id']]=$row_chl_data['encounter_id'];
	$gro_id_arr[$row_chl_data['gro_id']]=$row_chl_data['gro_id'];
	$chk_case_type_id_arr[$row_chl_data['case_type_id']]=$row_chl_data['case_type_id'];
}

if(count($chl_arr)>0){
	
	$chl_arr_imp=implode(',',$chl_arr);	
	
	//------------------- Policies detail -------- -----------//
	$get_pol=imw_query("SELECT billing_amount FROM copay_policies");
	$row_pol=imw_fetch_array($get_pol);
	$billing_amount=$row_pol['billing_amount'];
	
	//------------------- INSURANCE CASE -------- -----------//
	$getInsCaseInfoStr = "SELECT b.case_name,a.ins_caseid FROM insurance_case a,insurance_case_types b WHERE a.patient_id='$patient_id' AND a.ins_case_type=b.case_id";
	$getInsCaseInfoQry = imw_query($getInsCaseInfoStr);
	while($getInsCaseInfoQryRow = imw_fetch_array($getInsCaseInfoQry)){
		$ins_caseid=$getInsCaseInfoQryRow['ins_caseid'];
		$insCasesArr[] = $getInsCaseInfoQryRow['case_name'].' - '.$getInsCaseInfoQryRow['ins_caseid'];
	}
	
	//----------------- DX code data -----------------//
	$sel_chld_qry="select diagnosis_id1,diagnosis_id2,diagnosis_id3,diagnosis_id4,diagnosis_id5,diagnosis_id6,diagnosis_id7,diagnosis_id8,diagnosis_id9,
				diagnosis_id10,diagnosis_id11,diagnosis_id12,charge_list_id,modifier_id1,modifier_id2,modifier_id3,modifier_id4,procCode
				from patient_charge_list_details where $active_enc_whr charge_list_id in($chl_arr_imp)";
	$sel_chld_run=imw_query($sel_chld_qry);		
	while($sel_chld_row=imw_fetch_array($sel_chld_run)){	
		for($k=1;$k<=12;$k++){
			if($sel_chld_row['diagnosis_id'.$k]!=""){
				$dx_cod_arr[$sel_chld_row['charge_list_id']]['diagnosis_id'.$k][] = $sel_chld_row['diagnosis_id'.$k];
				$all_dx_cod_arr[$sel_chld_row['charge_list_id']][$sel_chld_row['diagnosis_id'.$k]]=$sel_chld_row['diagnosis_id'.$k];
			}
		}
		if($sel_chld_row['modifier_id1']){
			$mod_arr[]=$sel_chld_row['modifier_id1'];
			$mod_cod_arr[$sel_chld_row['charge_list_id']]['mod1'][] = $sel_chld_row['modifier_id1'];
		}
		if($sel_chld_row['modifier_id2']){
			$mod_arr[]=$sel_chld_row['modifier_id2'];
			$mod_cod_arr[$sel_chld_row['charge_list_id']]['mod2'][] = $sel_chld_row['modifier_id2'];
		}
		if($sel_chld_row['modifier_id3']){
			$mod_arr[]=$sel_chld_row['modifier_id3'];
			$mod_cod_arr[$sel_chld_row['charge_list_id']]['mod3'][] = $sel_chld_row['modifier_id3'];
		}
		if($sel_chld_row['modifier_id4']){
			$mod_arr[]=$sel_chld_row['modifier_id4'];
			$mod_cod_arr[$sel_chld_row['charge_list_id']]['mod4'][] = $sel_chld_row['modifier_id4'];
		}
	}
	
	//----------------- insurance data -----------------//
	if(count($ins_arr)>0){
		$ins_arr_imp=implode(',',array_unique($ins_arr));	
		$getinsCo3DetailsStr = "SELECT name,in_house_code,id,FeeTable FROM insurance_companies WHERE id in($ins_arr_imp)";
		$getinsCo3DetailsQry = imw_query($getinsCo3DetailsStr);
		while($getinsCo3DetailsRow = imw_fetch_array($getinsCo3DetailsQry)){
			$id = $getinsCo3DetailsRow['id'];
			$insCo3Name = $getinsCo3DetailsRow['name'];
			$insCo3PracCode = $getinsCo3DetailsRow['in_house_code'];
			if((!$insCo3PracCode) || ($insCo3PracCode=='')){
				$insCo3PracCode = $insCo3Name;
			}
			$len = strlen($insCo3PracCode);
			if($len>8){
				$insCo3PracCode = substr($insCo3PracCode, 0, 8).".. ";
			}		
			$ins_name_arr[$id][]=$insCo3PracCode;
			$fee_table_column_arr[$id]=$getinsCo3DetailsRow['FeeTable'];
		}	
	}				
	
	//----------------- procedure code data -----------------//
	$getProcedureDetailsStr = "SELECT a.*,b.cpt_prac_code,c.cpt_fee,c.fee_table_column_id,b.cpt_desc FROM patient_charge_list_details a 
							   join cpt_fee_tbl b on a.procCode=b.cpt_fee_id
							   join cpt_fee_table c on c.cpt_fee_id=b.cpt_fee_id
							   WHERE $active_chld_whr charge_list_id in ($chl_arr_imp) order by a.charge_list_id,a.display_order";
	$getProcedureDetailsQry = imw_query($getProcedureDetailsStr);
	while($getProcedureDetailsRows = imw_fetch_array($getProcedureDetailsQry)){
		$chl_id_chk=$getProcedureDetailsRows['charge_list_id'];
		$cptPracCode_arr[$chl_id_chk]['cpt_prac_code'][$getProcedureDetailsRows['charge_list_detail_id']] = $getProcedureDetailsRows['cpt_prac_code'];
		$cptPracCode_arr[$chl_id_chk]['cpt_prac_code_desc'][$getProcedureDetailsRows['charge_list_detail_id']] = $getProcedureDetailsRows['cpt_prac_code'].' - '.$getProcedureDetailsRows['cpt_desc'];
		$chld_cpt_arr[$chl_id_chk][$getProcedureDetailsRows['charge_list_detail_id']] = $getProcedureDetailsRows;
		if($billing_amount=='Default'){
			$chk_fee_table_column=$fee_table_column_arr[$ins_chl_arr[$chl_id_chk]];
			if($getProcedureDetailsRows['fee_table_column_id']==$chk_fee_table_column){
				$cpt_contract_price_arr[$getProcedureDetailsRows['charge_list_detail_id']]=$getProcedureDetailsRows['cpt_fee'];
			}
		}
	}
	
	//STATUS ARRAY
	$statusArr = array();
	$qry_claim_status =imw_query("SELECT id,status_name FROM claim_status where del_status='0' ORDER BY status_name ASC");
	while($fet_claim_status=imw_fetch_assoc($qry_claim_status)){
		$statusArr[$fet_claim_status['id']]=$fet_claim_status['status_name'];
	}
	
	//----------------- Provider data -----------------//
	if(count($provider_arr)>0){
		$provider_arr_imp=implode(',',array_unique($provider_arr));	
		$getProviderDetailsStr = "SELECT fname,mname,lname,id FROM users WHERE id in($provider_arr_imp)";
		$getProviderDetailsQry = imw_query($getProviderDetailsStr);
		while($getProviderDetailsRow=imw_fetch_array($getProviderDetailsQry)){
			$providerFname = substr($getProviderDetailsRow['fname'],0,1);
			$providerMname = substr($getProviderDetailsRow['mname'],0,1);
			$providerLname = substr($getProviderDetailsRow['lname'],0,1);
			$id = $getProviderDetailsRow['id'];
			$providerName =$providerFname.$providerMname.$providerLname;
			$provider_name_arr[$id]['provider'][]=$providerName;
		}
	}	
	
	//----------------- Modifier data -----------------//
	if(count($mod_arr)>0){
		$mod_arr_imp=implode(',',array_unique($mod_arr));	
		$getmodDetailsStr = "SELECT mod_prac_code,modifiers_id,mod_description FROM modifiers_tbl WHERE modifiers_id in($mod_arr_imp) AND delete_status = '0'";
		$getmodDetailsQry = imw_query($getmodDetailsStr);
		while($getmodDetailsRow=imw_fetch_array($getmodDetailsQry)){
			$mod_prac_code = $getmodDetailsRow['mod_prac_code'];
			$modifiers_id = $getmodDetailsRow['modifiers_id'];
			if($mod_prac_code==""){
				$mod_prac_code="-";
			}
			$mod_name_arr[$modifiers_id]=$mod_prac_code;
			$mod_desc_arr[$modifiers_id]=$getmodDetailsRow['mod_description'];
		}
	}	
}	

//----------------------- PAID BY -----------------//
if(count($enc_arr)>0){
	$enc_arr_imp=implode(',',$chl_enc_arr);
	$getPaidByStr = "SELECT a.paymentClaims,a.encounter_id,
					 b.paidForProc,b.charge_list_detail_id
					 FROM patient_chargesheet_payment_info as a,
					 patient_charges_detail_payment_info b
					 WHERE 
					 a.encounter_id in($enc_arr_imp)
					 AND a.payment_id = b.payment_id
					 AND b.deletePayment='0'";
	$getPaidByQry = imw_query($getPaidByStr);
	while($getPaidByRows = imw_fetch_array($getPaidByQry)){
		$enc_id=$getPaidByRows['encounter_id'];
		if($getPaidByRows['paymentClaims']=='Deposit'){
			$paid_proc_arr[$enc_id]['paidForProc'][] = $getPaidByRows['paidForProc'];
			$all_paid_proc_arr['paidForProc'][] = $getPaidByRows['paidForProc'];
			$paid_proc_chld_arr[$getPaidByRows['charge_list_detail_id']]['paidForProc'][] = $getPaidByRows['paidForProc'];
		}
	}
}

//----------------------- Get Posted/Submitted Detail -------------------//
if(count($enc_arr)>0){
	$enc_imp=implode(',',$enc_arr);	
	$getPostedDateStr = "SELECT posted_date,encounter_id FROM posted_record WHERE encounter_id in($enc_imp) ORDER BY id DESC";
	$getPostedDateQry = imw_query($getPostedDateStr);
	if(imw_num_rows($getPostedDateQry)>0){
		while($getPostedDaterow = imw_fetch_array($getPostedDateQry)){
			$enc_posted_arr[$getPostedDaterow['encounter_id']][]=$getPostedDaterow['posted_date'];
		}
	}
	
	$getSubmittedDateStr = "SELECT submited_date,encounter_id FROM submited_record WHERE encounter_id in($enc_imp) ORDER BY submited_id DESC";
	$getSubmittedDateQry = imw_query($getSubmittedDateStr);
	if(imw_num_rows($getSubmittedDateQry)>0){
		while($getSubmittedDateRow = imw_fetch_array($getSubmittedDateQry)){
			$enc_submitted_arr[$getSubmittedDateRow['encounter_id']][]=$getSubmittedDateRow['submited_date'];
		}
	}
}

//----------------------- Get Auth Detail -------------------//
if(count($chk_case_type_id_arr)>0){
	$chk_case_type_id_imp=implode(',',$chk_case_type_id_arr);
	$auth_qry=imw_query("select auth_name,ins_case_id from patient_auth where patient_id='$patient_id' and auth_status='0' and ins_case_id in($chk_case_type_id_imp) and no_of_reffs > 0");
	while($auth_row=imw_fetch_array($auth_qry)){
		$auth_detail[$auth_row['ins_case_id']]=$auth_row['auth_name'];	
	}
}


//----------------------- Get Group Detail -------------------//
if(count($gro_id_arr)>0){
	$gro_id_imp=implode(',',$gro_id_arr);						
	$grp_qry=imw_query("select group_color,gro_id,name from groups_new");
	while($grp_row=imw_fetch_array($grp_qry)){
		$grp_detail[$grp_row['gro_id']]=$grp_row;	
	}
}
?>
<div class="purple_bar">
	<form name="dateOfServiceFrm" method="post" action="review_charges.php">
    <table width="100%">
    	<tr>
        	<td>
              	<div class="checkbox checkbox-inline">
                    <input type="checkbox" name="all_chrg" id="all_chrg" <?php if((!$_REQUEST['unpaid_chrg']) && (!$dosFrom) && (!$insCaseTypeId) && (!$encounter_id || $encounter_id=='active')) { ?>checked="checked" <?php } ?> onClick="checkCreteria('all');"/>
                    <label for="all_chrg">All Charges</label>
                </div>
            </td>
            <td>
              	 <div class="checkbox checkbox-inline">
                    <input type="checkbox" name="unpaid_chrg" id="unpaid_chrg" <?php if($_REQUEST['unpaid_chrg']!="") { ?>checked="checked" <?php } ?> onClick="checkCreteria('unpaid');"/>
                    <label for="unpaid_chrg">Unpaid Charges</label>
                </div>
            </td>
            <td>
            	<span>Group:</span>
                <select name="grp_srh[]" id="grp_srh" class="selectpicker" data-width="180" multiple data-actions-box="true" data-title="Select Group" data-size="10">
					 <?php
						foreach($grp_detail as $g_key => $g_id){
							if(in_array($g_key,$_REQUEST['grp_srh'])){
								$sel = 'selected="selected"';
							}else{
								$sel = '';
							}
					?>
							<option value="<?php echo $g_key; ?>" <?php echo $sel; ?>><?php echo ucfirst($grp_detail[$g_key]['name']); ?></option>
							<?php
						}
                    ?>
                </select>
            </td>
            <td>
            	<span>DOS From:</span>
                <select id="dosFromDate" name="dosFrom" class="selectpicker" data-width="auto" data-size="10">
                    <option value="">All</option>
                    <?php
                        $dos=array_values(array_unique($dos_arr));
                        for($i=0;$i<count($dos);$i++){
                    ?>
                        <option value="<?php echo $dos[$i]; ?>" <?php if($dosFrom==$dos[$i]) echo "SELECTED"; ?>><?php echo $dos[$i]; ?></option>
                    <?php
                    }
                    ?>	
                </select>	
            </td>
            <td>
            	<span>DOS To:</span>
                <select id="dosToDate" name="dosTo" class="selectpicker" data-width="auto" data-size="10">
                    <option value="">All</option>
                    <?php
                        for($j=0;$j<count($dos);$j++){
                    ?>
                        <option value="<?php echo $dos[$j]; ?>" <?php if($dosTo==$dos[$j]) echo "SELECTED"; ?>><?php echo $dos[$j]; ?></option>
                    <?php
                    }
                    ?>	
              </select>
            </td>
            <td>
            	<span>Ins. Case:</span>
                <select id="inscase" name="insCaseType" class="selectpicker" data-width="auto" data-size="10">
                    <option value="">Select Case</option>
                    <?php
                    foreach($insCasesArr as $key => $value)	{
                        ?>
                        <option value="<?php echo $value; ?>" <?php if($value==$insCaseTypeId) echo "SELECTED"; ?>><?php echo $value; ?></option>
                        <?php
                    }
                    ?>
                </select>	
            </td>
            <td>
            	<span>E.Id:</span>
                <select name="encounter_id" id="eId" class="selectpicker" onChange="checkCreteria();" data-width="auto" data-size="10">
                    <option value="all" <?php if($encounter_id=="all") echo "SELECTED"; ?>>All</option>
                    <option value="active" <?php if($encounter_id=="active" || $encounter_id=="") echo "SELECTED"; ?>>Active</option>
                    <?php
                    foreach($srh_enc_arr as $key => $e_id){
                        ?>
                        <option value="<?php echo $e_id; ?>"<?php if($e_id==$encounter_id) echo "SELECTED"; ?>><?php echo $e_id; ?></option>
                        <?php
                    }
                    ?>
                </select>	
            </td>
            <td>
            	<span>Page <?php getHashOrNo();?>:</span>
				 <?php
                 $pag_qry = imw_query("SELECT charge_list_id FROM patient_charge_list WHERE $active_enc_whr patient_id='$patient_id' $enc_whr $dat_serv_whr $case_id_whr $grp_id_whr ORDER BY date_of_service DESC,charge_list_id  desc");
                 $pag_divide=ceil(imw_num_rows($pag_qry)/$pag_limit_to);
                ?>
                <select name="acc_pag" class="selectpicker" onChange="checkCreteria();" data-width="auto" data-size="10">
                    <?php for($n=1;$n<=$pag_divide;$n++){?>
                    <option value="<?php echo $n; ?>" <?php if($_REQUEST['acc_pag']==$n){ echo "selected";} ?>><?php echo $n; ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
    </table>
   </form>
</div>
<div class="clearfix"></div>
<div class="table-responsive" style="margin:0px; height:<?php echo $_SESSION['wn_height']-355;?>px; overflow-x:auto; width:100%;" >
	<form name="charge_list_frm" action="review_charges.php" method="post" style="margin:0px;">
    <input type="hidden" name="proc_details" id="proc_details" value="">
		<table class="table table-bordered table-hover">
			<tr class='grythead'>
            	<th rowspan="2" onClick="show_multi_proc();" class="text_purple" style="cursor:pointer;">S.No.</th>
				<th rowspan="2">
					<div class="checkbox">
						<input type="checkbox" name="chkbx_all" id="chkbx_all" onClick="return chk_all();"/>
						<label for="chkbx_all"></label>
					</div>
				</th>
				<th rowspan="2">DOS</th>
				<th rowspan="2" colspan="2">E. Id</th>
				<th rowspan="2">CPT</th>
				<th rowspan="2">Auth#</th>
				<th colspan="2">Providers</th>
				<th rowspan="2">Dx. Codes</th>
				<th colspan="4">Modifiers</th>
				<th colspan="3">Insurance</th>
				<th colspan="4">Charges</th>
				<th colspan="2">Date</th>
                <th rowspan="2">AR Status</th>
			</tr>
			<tr class='grythead'>
				<th>I</th>
				<th>II</th>
				<th>I</th>
				<th>II</th>
				<th>III</th>
                <th>IV</th>
				<th>Primary</th>
				<th>Sec.</th>
				<th>Tertiary</th>
				<th>Posted</th>
				<th>Deposit</th>
				<th>Paid</th>
				<th>Balance</th>
				<th>Posted</th>
				<th>Submitted</th>
			</tr>
			<?php
			if(count($getEncountersArr)<=0){
			?>
			<tr>
				<td colspan="24" class="text-center lead"><?php echo imw_msg('no_rec'); ?></td>
			</tr>
			<?php
			}
			$del_hide=false;
			$sum_posted=0;
			$sum_deposit=0;
			$sum_paid=0;
			$sum_balance=0;
			for($l=0;$l<count($getEncountersArr);$l++){
				$charge_list_id = $chl_id_arr[$l]['charge_list_id'];
				$gro_id = $group_id_arr[$l]['gro_id'];
				$submitted = $submitted_dat_arr[$l]['submitted'];
				$postedAmount = $posted_amt_arr[$l]['postedAmount'];
				$postedDate = $posted_dat_arr[$l]['firstSubmitDate'];
			
			
				if($submitted=='true'){
					$postedDate=$postedDate;
					$postedAmount =$postedAmount;
					$posted_opt_false="";
				}else{
					$postedDate="-";
					$postedAmount="0";
					$posted_opt_false="<option>-</option>";
				}

				$sum_posted=$sum_posted+$postedAmount;
				$encounter_id = $getEncountersArr[$l]['encounter_id'];
				$overPaymentTotal = $ovr_pay_arr[$l]['overPayment'];
				$copay = numberformat($copay_amt_arr[$l]['copay'], 2);					
				$copayPaid = $copay_paid_arr[$l]['copayPaid'];
				$coPayNotRequired = $copay_not_req_arr[$l]['coPayNotRequired'];
				$coPayWriteOff =  $copay_writeoff_arr[$l]['coPayWriteOff'];
				$totalAmt = numberformat($total_amt_arr[$l]['totalAmt'], 2);
				$approvedTotalAmt = numberformat($approve_amt_arr[$l]['approvedTotalAmt'], 2);
				$deductibleTotalAmt = numberformat($deduct_amt_arr[$l]['deductibleTotalAmt'], 2);
				$amtCredit = numberformat($credit_amt_arr[$l]['creditAmount'], 2);
				$amtPaid = $amt_paid_arr[$l]['amtPaid'];
				$newBal = numberformat($total_balance_arr[$l]['totalBalance'], 2);
				$case_type_id = $case_type_id_arr[$l]['case_type_id'];
				$primaryInsuranceCoId = $pri_ins_id_arr[$l]['primaryInsuranceCoId'];
				$secondaryInsuranceCoId = $sec_ins_id_arr[$l]['secondaryInsuranceCoId'];
				$tertiaryInsuranceCoId = $tri_ins_id_arr[$l]['tertiaryInsuranceCoId'];
				$primary_paid = $primary_paid_arr[$l]['primary_paid'];
				$secondary_paid = $secondary_paid_arr[$l]['secondary_paid'];
				$tertiary_paid  = $tertiary_paid_arr[$l]['tertiary_paid'];
				$primarySubmit = $primarySubmit_arr[$l]['primarySubmit'];
				$secondarySubmit = $secondarySubmit_arr[$l]['secondarySubmit'];
				$tertairySubmit = $tertairySubmit_arr[$l]['tertairySubmit'];
				$moaQualifier = $moaQualifier_arr[$l]['moaQualifier'];
				$claim_ctrl_pri = $claim_ctrl_pri_arr[$l]['claim_ctrl_pri'];
				$superbillFormId = $sb_id_arr[$l]['superbillFormId'];
				$ar_claim_status_id = $ar_claim_status_arr[$l]['claim_status'];
				if($auth_no_arr[$l]['auth_no']){
					$auth_no_final=$auth_no_arr[$l]['auth_no'];
				}else{
					$auth_no_final='';
				}
				$mod_sec=false;
				if(strstr($moaQualifier, 'MA18') || strstr($moaQualifier, 'MA07')){
					$mod_sec=true;
				}
				if($copayPaid == 1){
					$amtPaid = $amtPaid + $copay;
				}
				$sum_paid=$sum_paid+$amtPaid;

				if($ovr_pay_arr[$l]['overPayment']>0){
					$sum_ovrpay=$sum_ovrpay+$ovr_pay_arr[$l]['overPayment'];
				}else{
					$sum_balance=$sum_balance+$total_balance_arr[$l]['totalBalance'];
				}

				if($primaryInsuranceCoId>0){
					$pri_ins=$ins_name_arr[$primaryInsuranceCoId][0];
					if($primary_paid=='false' && $primarySubmit=='1'){
						$pri_paid_color="background:#E79015";
					}else{
						$pri_paid_color="";
					}
				}else{
					$pri_ins='-';
					$pri_paid_color="";
				}
				if($secondaryInsuranceCoId>0){
					$sec_ins=$ins_name_arr[$secondaryInsuranceCoId][0];
					if($primary_paid=='true' && $secondary_paid=='false' && $newBal>0){
						$sec_paid_color="background:#E79015";
					}else{
						$sec_paid_color="";
					}
				}else{
					$sec_ins='-';
					$sec_paid_color="";
				}
				if($tertiaryInsuranceCoId>0){
					$tri_ins=$ins_name_arr[$tertiaryInsuranceCoId][0];
					if($primary_paid=='true' && $secondary_paid=='true' && $tertiary_paid=='false' && $newBal>0){
						$tri_paid_color="background:#E79015";
					}else{
						$tri_paid_color="";
					}
				}else{
					$tri_ins='-';
					$tri_paid_color="";
				}

				$submittedDateRows = count($enc_submitted_arr[$encounter_id]);
				
				if($submittedDateRows==0){
					$pri_paid_color="";
					$sec_paid_color="";
					$tri_paid_color="";
				}

				$primaryProviderId=$pri_provider_id_arr[$l]['primaryProviderId'];
				$secondaryProviderId=$sec_provider_id_arr[$l]['secondaryProviderId'];
				$tertiaryProviderId=$tri_provider_id_arr[$l]['tertiaryProviderId'];

				if($primaryProviderId>0){
					$primaryProviderName=$provider_name_arr[$primaryProviderId]['provider'][0];
				}else{
					$primaryProviderName="-";
				}
				if($secondaryProviderId>0){
					$secondaryProviderName=$provider_name_arr[$secondaryProviderId]['provider'][0];;
				}else{
					$secondaryProviderName="-";
				}
				if($tertiaryProviderId>0){
					$tertiaryProviderName=$provider_name_arr[$tertiaryProviderId]['provider'][0];
				}else{
					$tertiaryProviderName="-";
				}

				$dateOfService=$dosArray[$l]['date_of_service'];
				$multi_dx1="";
				$multi_dx2="";
				$multi_dx3="";
				$multi_dx4="";
				if(count($dx_cod_arr[$charge_list_id]['diagnosis_id1'])>1){
					$diag1="Multi";
					$multi_dx1=implode('<br>',$dx_cod_arr[$charge_list_id]['diagnosis_id1']);
				}else{
					$diag1=$dx_cod_arr[$charge_list_id]['diagnosis_id1'][0];
				}
				if(count($dx_cod_arr[$charge_list_id]['diagnosis_id2'])>1){
					$diag2="Multi";
					$multi_dx2=implode('<br>',$dx_cod_arr[$charge_list_id]['diagnosis_id2']);
				}else{
					$diag2=$dx_cod_arr[$charge_list_id]['diagnosis_id2'][0];
				}
				if(count($dx_cod_arr[$charge_list_id]['diagnosis_id3'])>1){
					$diag3="Multi";
				$multi_dx3=implode('<br>',$dx_cod_arr[$charge_list_id]['diagnosis_id3']);
				}else{
					$diag3=$dx_cod_arr[$charge_list_id]['diagnosis_id3'][0];
				}
				if(count($dx_cod_arr[$charge_list_id]['diagnosis_id4'])>1){
					$diag4="Multi";
				$multi_dx4=implode('<br>',$dx_cod_arr[$charge_list_id]['diagnosis_id4']);
				}else{
					$diag4=$dx_cod_arr[$charge_list_id]['diagnosis_id4'][0];
				}
				if($diag1==""){
					$diag1=" - ";
				}	
				if($diag2==""){
					$diag2=" - ";
				}	
				if($diag3==""){
					$diag3=" - ";
				}	
				if($diag4==""){
					$diag4=" - ";
				}	
				$all_dx_cod_imp=implode(', ',$all_dx_cod_arr[$charge_list_id]);
				$multi_mod1="";
				$multi_mod2="";
				$multi_mod3="";
				$multi_mod4="";
				if(count($mod_cod_arr[$charge_list_id]['mod1'])>1){
					$mod1="Multi";
					$multi_mod1_array=array();
					for($h=0;$h<count($mod_cod_arr[$charge_list_id]['mod1']);$h++){
						$multi_mod1_array[]=$mod_name_arr[$mod_cod_arr[$charge_list_id]['mod1'][$h]].' - '.$mod_desc_arr[$mod_cod_arr[$charge_list_id]['mod1'][$h]];
					}
					$multi_mod1=implode('<br>',$multi_mod1_array);
				}else{
					$mod_id1=$mod_cod_arr[$charge_list_id]['mod1'][0];
					$mod1=$mod_name_arr[$mod_id1];
					$multi_mod1=$mod1.' - '.$mod_desc_arr[$mod_id1];
				}
				if(count($mod_cod_arr[$charge_list_id]['mod2'])>1){
					$mod2="Multi";
					$multi_mod2_array=array();
					for($d=0;$d<count($mod_cod_arr[$charge_list_id]['mod2']);$d++){
						$multi_mod2_array[]=$mod_name_arr[$mod_cod_arr[$charge_list_id]['mod2'][$d]].' - '.$mod_desc_arr[$mod_cod_arr[$charge_list_id]['mod2'][$d]];
					}
					$multi_mod2=implode('<br>',$multi_mod2_array);
				}else{
					$mod_id2=$mod_cod_arr[$charge_list_id]['mod2'][0];
					$mod2=$mod_name_arr[$mod_id2];
					$multi_mod2=$mod2.' - '.$mod_desc_arr[$mod_id2];
				}
				if(count($mod_cod_arr[$charge_list_id]['mod3'])>1){
					$mod3="Multi";
					$multi_mod3_array=array();
					for($k=0;$k<count($mod_cod_arr[$charge_list_id]['mod3']);$k++){
						$multi_mod3_array[]=$mod_name_arr[$mod_cod_arr[$charge_list_id]['mod3'][$k]].' - '.$mod_desc_arr[$mod_cod_arr[$charge_list_id]['mod3'][$k]];
					}
					$multi_mod3=implode('<br>',$multi_mod3_array);
				}else{
					$mod_id3=$mod_cod_arr[$charge_list_id]['mod3'][0];
					$mod3=$mod_name_arr[$mod_id3];
					$multi_mod3=$mod3.' - '.$mod_desc_arr[$mod_id3];
				}
				if(count($mod_cod_arr[$charge_list_id]['mod4'])>1){
					$mod4="Multi";
					$multi_mod4_array=array();
					for($k=0;$k<count($mod_cod_arr[$charge_list_id]['mod4']);$k++){
						$multi_mod4_array[]=$mod_name_arr[$mod_cod_arr[$charge_list_id]['mod4'][$k]].' - '.$mod_desc_arr[$mod_cod_arr[$charge_list_id]['mod4'][$k]];
					}
					$multi_mod4=implode('<br>',$multi_mod4_array);
				}else{
					$mod_id4=$mod_cod_arr[$charge_list_id]['mod4'][0];
					$mod4=$mod_name_arr[$mod_id4];
					$multi_mod4=$mod4.' - '.$mod_desc_arr[$mod_id4];
				}
				if($mod1==""){
					$mod1=" - ";
				}	
				if($mod2==""){
					$mod2=" - ";
				}	
				if($mod3==""){
					$mod3=" - ";
				}	
				if($mod4==""){
					$mod4=" - ";
				}

				$procedureNameCode="";
				//----------------------- PROCEDURE DETAILS -----------------------//
				if(count($cptPracCode_arr[$charge_list_id]['cpt_prac_code'])==1){
					foreach($cptPracCode_arr[$charge_list_id]['cpt_prac_code'] as $cpt_key=>$cpt_val){
						$procedureNameCode=$cpt_val;
					}
				}	
				$multi_cpt="";
				if(count($cptPracCode_arr[$charge_list_id]['cpt_prac_code'])>1){
					$procedureNameCode='Multi';
					$multi_cpt=implode('<br>',$cptPracCode_arr[$charge_list_id]['cpt_prac_code_desc']);
				}else{
					$multi_cpt=implode('<br>',$cptPracCode_arr[$charge_list_id]['cpt_prac_code_desc']);
				}
				if($procedureNameCode==""){
					$procedureNameCode='-';
				}
				
				//----------------------- Deposit Amount -----------------------//
				$totaldepositAmt=0;
				if($paid_proc_arr[$encounter_id]['paidForProc']){
					$totaldepositAmt=array_sum($paid_proc_arr[$encounter_id]['paidForProc']);
				}else{
					$totaldepositAmt=0;
				}
				$sum_deposit=$sum_deposit+$totaldepositAmt;

				$group_color=$grp_detail[$gro_id]['group_color'];
				if($group_color){
					if($group_color=='#FFFFFF'){
						$g_color="#ffffff";
					}else{
						$g_color=$group_color;
					}
				}else{
					$g_color="#ffffff";
				}
				$contract_fee=0;
				$pri_contract_flag=0;
				$pri_ins_paid=0;
				$contract_flag=$sb_flag="";
				if($primaryInsuranceCoId>0 && $secondaryInsuranceCoId==0){
					foreach($cptPracCode_arr[$charge_list_id]['cpt_prac_code'] as $cpt_key=>$cpt_val){
						$contract_fee=$contract_fee+$cpt_contract_price_arr[$cpt_key];
					}
					$pri_ins_paid=$insPaidAmt_arr[$l]['insPaidAmt'];
					if($pri_ins_paid>0 && $contract_fee>$pri_ins_paid){
						$pri_contract_flag=1;
					}
				}
				$icon_wd=7;
				if($pri_contract_flag>0){
					$contract_flag='<img src="../../library/images/flag_orange_n.gif" style="width:14px; height:16px;">';
					$icon_wd=$icon_wd+14;
				}
				if($superbillFormId>0){
					$sb_flag='<img src="../../library/images/sb.png" '.show_tooltip("Superbill").' style="width:16px; height:16px;">';
					$icon_wd=$icon_wd+16;
				}

				$del_charge_list_id="0";
				$sb_class="";
				if($del_status_arr[$l]['del_status']>0){
					$del_charge_list_id=$charge_list_id;
					$sb_class="text-danger";
				}
				
				$page_link='href="accounting_view.php?encounter_id='.$encounter_id.'&del_charge_list_id='.$del_charge_list_id.'&tabvalue=Enter_Charges&show_load=yes"';
				++$seq;
				$chld_id_show_arr=array();
				foreach($chld_cpt_arr[$charge_list_id] as $cpt_key=>$cpt_val){	
					$chld_id_show_arr[]=$chld_cpt_arr[$charge_list_id][$cpt_key]['charge_list_detail_id'];
				}
				$chld_id_show_str=implode(',',$chld_id_show_arr);
?>
				<tr class="text-center">
                	<td <?php echo show_gro_color($group_color); ?>>
                        <a href="javascript:show_enc_proc('<?php echo $chld_id_show_str;?>','<?php echo $encounter_id;?>');" class="text_purple">
                            <?php echo $seq; ?>
                        </a>
                    </td>
					<td>
						<input type="hidden" value="<?php echo $submitted; ?>" name="submittedText[]">
						<?php
						$del_hide=true;	
						if($del_status_arr[$l]['del_status']==0){
							if($claim_ctrl_pri=="" && count($enc_submitted_arr[$encounter_id])>0){
								$claim_ctrl_pri=billing_global_get_clm_control_num($patient_id,$encounter_id,0,'primary');
								if($claim_ctrl_pri!=""){
									imw_query("update patient_charge_list set claim_ctrl_pri='$claim_ctrl_pri' where charge_list_id='$charge_list_id'");
								}
							}
						?>
							<div class="checkbox">
								<input name="chkbx[]" type="checkbox" id="chkbx<?php echo $charge_list_id; ?>" class="chk_box_css" value="<?php echo $charge_list_id; ?>"/>
								<label for="chkbx<?php echo $charge_list_id; ?>"></label>
							</div>
							<input style="display:none;" class="chk_box_ccn_css"  type="checkbox" id="claim_ctrl_pri_<?php echo $charge_list_id; ?>" name="claim_ctrl_pri_<?php echo $charge_list_id; ?>" value="<?php echo $claim_ctrl_pri; ?>">
							<?php if($totalAmt>$newBal){ ?>
								<input type="hidden" id="chkbx_del_<?php echo $charge_list_id; ?>" name="chkbx_del_<?php echo $charge_list_id; ?>" value="no">
								<input type="hidden" id="chkbx_enc_del_<?php echo $charge_list_id; ?>" name="chkbx_enc_del_<?php echo $charge_list_id; ?>" value="<?php echo $encounter_id;?>">
						<?php 	} 
							}
						?>
					</td>
					<td class="text-nowrap">
						<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>>
							<?php echo $dateOfService; ?>
						</a>
					</td>
                    <td style="border-right-color:white;">
                      	<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>>
							<?php echo $encounter_id; ?>
                        </a>
                    </td>
                    <td>
                        <span class="pull-right" style="width:<?php echo $icon_wd.'px;';?>">
                           <?php echo $contract_flag; ?>
                           <?php echo $sb_flag; ?>
                        </span>
                    </td>
					<td>
						<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?> <?php echo show_tooltip($multi_cpt); ?>>
							<?php echo $procedureNameCode; ?>
						</a>
					</td>
					<td>
						<table style="width:100%;">
							<tr>
								<td>
									<?php echo $auth_no_final; ?>
								</td>
								<td class="pull-right">
									<?php if(count($auth_detail[$case_type_id])>0){?>
										<span title="More" style="cursor:pointer;" class="glyphicon glyphicon-circle-arrow-down" onClick="show_auth_info('<?php echo $case_type_id; ?>','<?php echo $auth_no_final; ?>');"></span>
									<?php }?>
								</td>
							</tr>
						</table>
					</td>
					<td>
						<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>>
							<?php echo strtoupper($primaryProviderName); ?>
						</a>
					</td>
					<td>
						<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>>
							<?php echo strtoupper($secondaryProviderName); ?>
						</a>
					</td>
					<td class="text-left">
						<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>>
							<?php echo $all_dx_cod_imp; ?>
						</a>
					</td>
					<td>
						<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?> <?php echo show_tooltip($multi_mod1); ?>>
							<?php echo $mod1; ?>
						</a>
					</td>
					<td>
						<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?> <?php echo show_tooltip($multi_mod2); ?>>
							<?php echo $mod2; ?>
						</a>
					</td>
					<td>
						<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?> <?php echo show_tooltip($multi_mod3); ?>>
							<?php echo $mod3; ?>
						</a>
					</td>
                    <td>
						<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?> <?php echo show_tooltip($multi_mod4); ?>>
							<?php echo $mod4; ?>
						</a>
					</td>
					<td style=" <?php echo $pri_paid_color; ?>">
						<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>>
							<?php echo $pri_ins; ?>
						</a>
					</td>
					<td style=" <?php echo $sec_paid_color; ?>">
						<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>>
							<?php  echo $sec_ins; ?>
						</a>
					</td>
					<td style=" <?php echo $tri_paid_color; ?>">
						<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>>
							<?php echo $tri_ins; ?>
						</a>
					</td>
					<td class="text-right">
						<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>>
							<?php echo numberformat($postedAmount,2); ?>
						</a>
					</td>
					<td class="text-right">
						<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>>
							<?php echo numberformat($totaldepositAmt,2); ?>
						</a>
					</td>
					<td class="text-right">
						<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>>
							<span style="color:Green;"><?php echo numberformat($amtPaid,2,"yes"); ?></span>
						</a>
					</td>
					<td class="text-right">
						<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>>
							<?php
							if($overPaymentTotal>0){ 
								echo '<span style="color:#5D738E;"><b>'."-".numberformat($overPaymentTotal, 2).'</b></span>'; 
							}else{ 
							?>
							<?php if(str_replace('$','',$newBal)>0) echo '<span style="color:red"><b>'.$newBal.'</b></span>'; else echo '<span style="color:black"><b>'.$newBal.'</b></span>'; ?>
							<?php } ?>
						</a>
					</td>
					<td>
						<?php 
						$enc_posted_detail=array();
						$enc_posted_detail=$enc_posted_arr[$encounter_id];
						if(count($enc_posted_detail)>0){
						?>
							<select name="postedDate" class="selectpicker" data-width="100%" data-size="5">
							<?php
							echo $posted_opt_false;
							foreach($enc_posted_detail as $val){	
								$postedDate = $val;					
								$postedDateFinal = get_date_format($postedDate);
								$pdf_for_posted_date[]=$postedDateFinal;
							?>				
								<option><?php echo $postedDateFinal; ?></option>
							<?php }?>         
							</select>
						<?php  
						}else{ 
							echo "-";
						}
						?>
					</td>
					<td>
						<?php
							$enc_submitted_detail=array();
							$enc_submitted_detail=$enc_submitted_arr[$encounter_id];
							if(count($enc_submitted_detail)>0){
						?>
							<select name="hcfaSubmittedDat" class="selectpicker" data-width="100%" data-size="5">
						<?php
							foreach($enc_submitted_detail as $val){		
								$hcfaSubmittedDate = $val;					
								$hcfaSubmittedDate = get_date_format($hcfaSubmittedDate);
						?>
									<option><?php echo $hcfaSubmittedDate; ?></option>
						<?php } ?>
							</select>
						<?php }else{ echo '-';}?>
					</td>
                    <td class="text-left">
						<a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>>
							<?php echo $statusArr[$ar_claim_status_id]; ?>
						</a>
					</td>
				</tr>
                <?php
                $proc_det_arr = $chld_cpt_arr[$charge_list_id];
				if(count($proc_det_arr)>1){
					foreach($proc_det_arr as $cpt_key=>$cpt_val){
						$chld_all_dx_cod_arr=array();
						$proc_name = $proc_det_arr[$cpt_key]['cpt_prac_code'];
						$proc_cpt_desc = $proc_det_arr[$cpt_key]['cpt_desc'];
						for($k=1;$k<=12;$k++){
							if($proc_det_arr[$cpt_key]['diagnosis_id'.$k]!=""){
								$chld_all_dx_cod_arr[$proc_det_arr[$cpt_key]['diagnosis_id'.$k]]=$proc_det_arr[$cpt_key]['diagnosis_id'.$k];
							}
						}
						$chld_pri_ins=$pri_ins;
						$chld_sec_ins=$sec_ins;
						$chld_tri_ins=$tri_ins;
						if($proc_det_arr[$cpt_key]['proc_selfpay']>0){
							$chld_pri_ins=$chld_sec_ins=$chld_tri_ins=" - ";
						}
						$amt_var_col="Red";
						if($proc_newBalance <= 0){
							$amt_var_col="Green";
						}
						if($_REQUEST['details']==""){$det_dis="display:none;";}
						?>
                        <tr class="text-center" id="td_proc_details_row_<?php echo $cpt_key;?>" style=" <?php echo $det_dis;?>">
                            <td><input type="hidden" name="proc_details_row[]" value="<?php echo $cpt_key;?>"></td>
                            <td colspan="4" class="text-left"><a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>><?php echo $proc_cpt_desc; ?></a></td>
                            <td><a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?> <?php echo show_tooltip($proc_name.' - '.$proc_cpt_desc); ?>><?php echo $proc_name; ?></a></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-left"><a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>><?php echo implode(', ',$chld_all_dx_cod_arr); ?></a></td>
                            <td><a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?> <?php echo show_tooltip($mod_name_arr[$proc_det_arr[$cpt_key]['modifier_id1']].' - '.$mod_desc_arr[$proc_det_arr[$cpt_key]['modifier_id1']]); ?>><?php echo $mod_name_arr[$proc_det_arr[$cpt_key]['modifier_id1']]; ?></a></td>
                            <td><a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?> <?php echo show_tooltip($mod_name_arr[$proc_det_arr[$cpt_key]['modifier_id2']].' - '.$mod_desc_arr[$proc_det_arr[$cpt_key]['modifier_id2']]); ?>><?php echo $mod_name_arr[$proc_det_arr[$cpt_key]['modifier_id2']]; ?></a></td>
                            <td><a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?> <?php echo show_tooltip($mod_name_arr[$proc_det_arr[$cpt_key]['modifier_id3']].' - '.$mod_desc_arr[$proc_det_arr[$cpt_key]['modifier_id3']]); ?>><?php echo $mod_name_arr[$proc_det_arr[$cpt_key]['modifier_id3']]; ?></a></td>
                            <td><a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?> <?php echo show_tooltip($mod_name_arr[$proc_det_arr[$cpt_key]['modifier_id4']].' - '.$mod_desc_arr[$proc_det_arr[$cpt_key]['modifier_id4']]); ?>><?php echo $mod_name_arr[$proc_det_arr[$cpt_key]['modifier_id4']]; ?></a></td>
                            <td><a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>><?php echo $chld_pri_ins; ?></a></td>
                            <td><a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>><?php echo $chld_sec_ins; ?></a></td>
                            <td><a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>><?php echo $chld_tri_ins; ?></a></td>
                            <td class="text-right"><a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>><?php if($proc_det_arr[$cpt_key]['posted_status']>0) echo numberformat($proc_det_arr[$cpt_key]['totalAmount'],2);?></a></td>
                            <td class="text-right"><a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>><?php echo numberformat(array_sum($paid_proc_chld_arr[$cpt_key]['paidForProc']),2);?></a></td>
                            <td class="text-right"><a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>><?php echo numberformat(($proc_det_arr[$cpt_key]['paidForProc']),2,"yes"); ?></a></td>
                            <td class="text-right">
                            <a class="<?php echo $sb_class; ?>" <?php echo $page_link; ?>>
							<?php
							if($proc_det_arr[$cpt_key]['overPaymentForProc']>0){ 
								echo '<span style="color:#5D738E;"><b>'."-".numberformat($proc_det_arr[$cpt_key]['overPaymentForProc'], 2).'</b></span>'; 
							}else{ 
								if(str_replace('$','',$proc_det_arr[$cpt_key]['newBalance'])>0) echo '<span style="color:red"><b>'.numberformat($proc_det_arr[$cpt_key]['newBalance'],2).'</b></span>'; else echo '<span style="color:black"><b>'.numberformat($proc_det_arr[$cpt_key]['newBalance'],2).'</b></span>';
							} ?></a>
                            </td>	
                            <td></td>	
                            <td></td>
                            <td></td>			
						</tr>
                        <?php
					}
				}
			}
			?>
			<?php 
			if(count($getEncountersArr)>0){
				if($sum_ovrpay>$sum_balance){
					$sum_balance=$sum_ovrpay-$sum_balance;
					if($sum_balance>0){
						$tot_credit_sign='-';
					}
				}else{
					$sum_balance=$sum_balance-$sum_ovrpay;
				}
				
				if($final_sum_ovrpay>$final_sum_balance){
					$final_sum_balance=$final_sum_ovrpay-$final_sum_balance;
				}else{
					$final_sum_balance=$final_sum_balance-$final_sum_ovrpay;
				}
				?>
				<tr class="purple_bar">
					<td colspan="15" class="white_bar"></td>
					<td colspan="2" class="text-right">Total:</td>
					<td class="text-right"><?php echo numberformat($sum_posted,2); ?></td>
					<td class="text-right"><?php echo numberformat($sum_deposit,2); ?></td>
					<td class="text-right"><?php echo numberformat($sum_paid,2,"yes"); ?></td>
					<td class="text-right"><?php echo $tot_credit_sign.numberformat($sum_balance,2); ?></td>
					<td></td>
					<td></td>
                    <td></td>
				</tr>
				<tr class="purple_bar">
					<td colspan="15" class="white_bar"></td>
					<td colspan="2" class="text-right">Final Total:</td>
					<td class="text-right"><?php echo numberformat($final_sum_posted,2); ?></td>
					<td class="text-right"><?php echo numberformat(array_sum($all_paid_proc_arr['paidForProc']),2); ?></td>
					<td class="text-right"><?php echo numberformat($final_sum_paid,2,"yes"); ?></td>
					<td class="text-right"><?php echo $tot_credit_sign.numberformat($final_sum_balance,2); ?></td>
					<td></td>
					<td></td>
                    <td></td>
				</tr>
			<?php } ?>
	</table>
</form>
</div>
<script type="text/javascript">
	 <?php if(count($getEncountersArr)>0 && $del_hide==true){ ?>
		var ar = [["a_r_bal","Balance View","top.fmain.OpenBalWin('');"],
				  ["search_btn","Search","top.fmain.checkCreteria();"],
				  ["delete_fun","Void","top.fmain.chkSelection();"]];
		top.btn_show("ACCOUNT",ar);
	<?php }else{ ?>
		top.btn_show();
	<?php }?>
</script>	
<?php require_once("acc_footer.php");?>		