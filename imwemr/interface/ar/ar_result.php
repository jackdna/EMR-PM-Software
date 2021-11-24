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
	PURPOSE : AR RESULT PREPRATION
	ACCESS TYPE : INDIRECT
*/

$detail_column_list=($_POST['printable_columns'])?$_POST['printable_columns']:$printableColArr;
$detail_column_list=array_combine($detail_column_list,$detail_column_list);

function show_date_diff($date){
	$days="";
	if(!empty($date)&&($date!="0000-00-00")){
		$date_time = strtotime($date);
		$cur_time = strtotime(date("Y-m-d H:i:s"));
		$days = floor(($cur_time-$date_time)/(60*60*24));
	}
	return $days;
}

$sql = 'SELECT * FROM ar_worksheet';
$resp = imw_query($sql);
if($resp && imw_num_rows($resp)>0){
	$row = imw_fetch_assoc($resp);
	$selected_col=unserialize($row['ar_detail_column']);
	$detail_column_html=array_combine($selected_col,$selected_col);
}

$printFile=false;
$printPdfWidth=1030;

$join_qry=$fet_fields="";
$appt_reason= (sizeof($_POST['appt_reason'])>0) ? implode(',',$_POST['appt_reason']) : '';
if(trim($appt_reason) != ''){
	$join_qry.= " JOIN schedule_appointments ON schedule_appointments.id = patient_charge_list.sch_app_id";
}

$as_of_db=getDateFormatDB($_POST['as_of']);
$aging_by=($_POST['aging_by'])?$_POST['aging_by']:'Date of Service';
##AGING BY FILTER
if($aging_by!='')
{
	if($_POST['aging_by']=="First Claim Date"){
		$fet_fields.='DATEDIFF(NOW(),patient_charge_list.firstSubmitDate) as fsd_date_diff,';
	}else if($_POST['aging_by']=="Last Claim Date"){
		$fet_fields.='DATEDIFF(NOW(),patient_charge_list.Re_submitted_date) as rsd_date_diff,';
	}else{
		$fet_fields.='DATEDIFF(NOW(),patient_charge_list.date_of_service) as dos_date_diff,';
	}
}

##REJECTION CODE FILTER
if(count($_POST['rej_code'])>0 || $_POST['rej_status']!=""){
	$join_qry.= " JOIN deniedpayment ON deniedpayment.charge_list_detail_id = patient_charge_list_details.charge_list_detail_id";
}

##FOLLOW ON FILTER
if($_POST['follow_up_from']!="" || $_POST['follow_up_to']!="" || $_POST['follow_up_opr_id']!="" || $_POST['last_status']!=""){
	$join_qry.= " JOIN tm_assigned_rules ON tm_assigned_rules.encounter_id = patient_charge_list.encounter_id";
}

if($_POST['ord_by_field']=="enc_fac_ord" && $_POST['detail_ins_id']>0){
	//$join_qry.= " LEFT JOIN facility ON facility.id = patient_charge_list.billing_facility_id";
}
if($_POST['ord_by_field']=="enc_pos_fac_ord" && $_POST['detail_ins_id']>0){
	$join_qry.= " LEFT JOIN pos_facilityies_tbl ON pos_facility_id.id = patient_charge_list_details.posFacilityId";
}
if($_POST['ord_by_field']=="enc_prov_ord" && $_POST['detail_ins_id']>0){
	$join_qry.= " LEFT JOIN users ON users.id = patient_charge_list.primaryProviderId";
}
if($_POST['ord_by_field']=="enc_ins_pol_ord" && $_POST['detail_ins_id']>0){
	$join_qry .= " JOIN insurance_data ON insurance_data.ins_caseid = patient_charge_list.case_type_id";
}
if($_POST['ord_by_field']=="enc_ins_case_ord" && $_POST['detail_ins_id']>0){
	$join_qry .= " JOIN insurance_data ON insurance_data.ins_caseid = patient_charge_list.case_type_id
	JOIN insurance_case ON insurance_case.ins_caseid = insurance_data.ins_caseid
	JOIN insurance_case_types ON insurance_case_types.case_id = insurance_case.ins_case_type";
}

$query ="select patient_charge_list.totalBalance, patient_charge_list.case_type_id, patient_charge_list.primaryInsuranceCoId, patient_charge_list.secondaryInsuranceCoId, patient_charge_list.tertiaryInsuranceCoId, patient_charge_list.patient_id, patient_charge_list_details.pri_due, patient_charge_list_details.sec_due, patient_charge_list_details.tri_due, patient_charge_list_details.pat_due, patient_charge_list_details.charge_list_detail_id, patient_charge_list.statement_count, patient_charge_list_details.notes, patient_charge_list.encounter_id, patient_charge_list.date_of_service, patient_charge_list.billing_facility_id, patient_charge_list.primaryProviderId, patient_charge_list.postedDate, patient_charge_list_details.procCode, patient_charge_list_details.totalAmount,patient_charge_list_details.differ_patient_bill,
patient_charge_list.reff_phy_nr,patient_charge_list.reff_phy_id,patient_data.ProviderId,patient_charge_list.charge_list_id,patient_charge_list_details.newBalance,patient_charge_list.vipStatus, $fet_fields
patient_data.sex,patient_data.DOB,patient_data.lname,patient_data.fname, patient_data.mname, patient_data.email, patient_charge_list_details.entered_date, patient_charge_list_details.diagnosis_id1, patient_charge_list_details.diagnosis_id2, patient_charge_list_details.diagnosis_id3, patient_charge_list_details.diagnosis_id4, patient_charge_list_details.diagnosis_id5, patient_charge_list_details.diagnosis_id6, patient_charge_list_details.diagnosis_id7, patient_charge_list_details.diagnosis_id8, patient_charge_list_details.diagnosis_id9, patient_charge_list_details.diagnosis_id10, patient_charge_list_details.diagnosis_id11, patient_charge_list_details.diagnosis_id12, patient_charge_list.firstSubmitDate,patient_charge_list.collection,patient_charge_list_details.claimDenied,patient_data.acc_statement_date,patient_data.hold_statement
,patient_charge_list_details.ar_assign_to,patient_charge_list.claim_status,patient_charge_list_details.posFacilityId
FROM patient_charge_list
JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id
JOIN patient_data ON patient_data.id = patient_charge_list.patient_id
$join_qry
WHERE patient_charge_list_details.del_status='0'
AND patient_charge_list_details.newBalance>0
";
//AND (patient_charge_list_details.pri_due + patient_charge_list_details.sec_due + patient_charge_list_details.tri_due) > 0
##FACILITY FILTER
$sc_name= (sizeof($_POST['facility_id'])>0) ? implode(',',$_POST['facility_id']) : '';
//if($sc_name)$query.=" AND patient_charge_list.billing_facility_id IN($sc_name)";
if($sc_name)$query.=" AND patient_charge_list_details.posFacilityId IN($sc_name)";
##PROVIDER FILTER
$Physician= (sizeof($_POST['filing_provider'])>0) ? implode(',',$_POST['filing_provider']) : '';
if($Physician)$query.=" AND patient_charge_list.primaryProviderId IN($Physician)";
$group_by=($_POST['group_by'])?$_POST['group_by']:'Insurance';

##INSURANCE GROUP  and FILTER
if(empty($_POST['ins_carriers'])==false){ $tempInsArr[] = implode(',',$_POST['ins_carriers']); }
if(empty($_POST['insuranceGrp'])==false){ $tempInsArr[] = implode(',',$_POST['insuranceGrp']); }
$tempSelIns = implode(',', $tempInsArr);
$tempInsArr = array();
if(empty($tempSelIns)==false){
	$tempInsArr = explode(',', $tempSelIns);
}
$tempInsArr = array_unique($tempInsArr);
$insuranceName  = implode(',', $tempInsArr);
$arrInsurance=array();
if(sizeof($tempInsArr)>0){
	$arrInsurance = array_combine($tempInsArr,$tempInsArr);
}
unset($tempInsArr);
if( is_array($arrInsurance) && count($arrInsurance) > 0){
	$insCompanies = implode(',',$arrInsurance);
}
##INSRUANCE TYPE FILTER
if($insCompanies)
{
	$ins_type= (sizeof($_POST['ins_types'])>0) ? implode(',',$_POST['ins_types']) : '';
	if(trim($ins_type) == ''){
		$query.=" AND (patient_charge_list.primaryInsuranceCoId IN($insCompanies)
				OR patient_charge_list.secondaryInsuranceCoId IN($insCompanies)
				OR patient_charge_list.tertiaryInsuranceCoId IN($insCompanies))";
	}else{
		$ins_type_arr=explode(',',$ins_type);
		if(trim($ins_type)!='Self Pay'){
			$query.= " and (";
			for($i=0;$i<count($ins_type_arr);$i++){
				$ins_nam=$ins_type_arr[$i];
				if(trim($ins_nam)!='Self Pay')
				{
					$mul_or="";
					if($i>0){
						$mul_or=" or ";
					}
	
					$query.= " $mul_or patient_charge_list.$ins_nam in ($insuranceName)";
				}
			}
			$query.= " )";
		}
	}
}else{
	$ins_type= (sizeof($_POST['ins_types'])>0) ? implode(',',$_POST['ins_types']) : '';
	if(trim($ins_type) != ''){
		$ins_type_arr=explode(',',$ins_type);
		if(trim($ins_type)!='Self Pay'){
			$query.= " and (";
			for($i=0;$i<count($ins_type_arr);$i++){
				$ins_nam=$ins_type_arr[$i];
				if(trim($ins_nam)!='Self Pay')
				{
					$mul_or="";
					if($i>0){
						$mul_or=" or ";
					}
	
					$query.= " $mul_or patient_charge_list.$ins_nam >0";
				}
			}
			$query.= " )";
		}
	}
}

##PATIENT ID FILTER
if($_POST['patientId'] && $_POST['txt_patient_name'])$query.=" AND patient_charge_list.patient_id='$_POST[patientId]'";



##AS OF FILTER

##STATUS FILTER
if($_POST['status'])$query.= " AND patient_charge_list.claim_status in (". implode(',', $_POST['status']) .")";
##INSRUANCE PRIORITY FILTER

##APPT REASON FILTER
if(trim($appt_reason) != '')$query.=" AND schedule_appointments.sa_patient_app_status_id in($appt_reason)";

##FILTER ON FILTER
$filter_on_from=$filter_on_to="";
if($as_of_db!=""){
	if($_POST['aging_by']=="First Claim Date"){
		$query.=" AND patient_charge_list.firstSubmitDate <='$as_of_db'";
	}else if($_POST['aging_by']=="Last Claim Date"){
		$query.=" AND patient_charge_list.Re_submitted_date <='$as_of_db'";
	}else{
		$query.=" AND patient_charge_list.date_of_service <='$as_of_db'";
	}
}else{
	$filter_on=($_POST['filter_on'])?$_POST['filter_on']:'Date of Service';
	if(isset($_POST['filter_on']) && $_POST['filter_on']!='')
	{
		if($_POST['filter_on_from']!="" || $_POST['filter_on_to']!=""){
			if($_POST['filter_on_from'])$filter_on_from=getDateFormatDB($_POST['filter_on_from']);
			if($_POST['filter_on_to'])$filter_on_to=getDateFormatDB($_POST['filter_on_to']);
			switch($filter_on)
			{
				case 'Date of Service':
					if($filter_on_from!="" && $filter_on_to!=""){
						$query.=" AND patient_charge_list.date_of_service BETWEEN '$filter_on_from' AND '$filter_on_to'";
					}else if($filter_on_from!=""){
						$query.=" AND patient_charge_list.date_of_service >='$filter_on_from'";
					}else if($filter_on_to!=""){
						$query.=" AND patient_charge_list.date_of_service <='$filter_on_to'";
					}
					break;
				case 'First Claim Date':
					if($filter_on_from!="" && $filter_on_to!=""){
						$query.=" AND patient_charge_list.firstSubmitDate BETWEEN '$filter_on_from' AND '$filter_on_to'";
					}else if($filter_on_from!=""){
						$query.=" AND patient_charge_list.firstSubmitDate >='$filter_on_from'";
					}else if($filter_on_to!=""){
						$query.=" AND patient_charge_list.firstSubmitDate <='$filter_on_to'";
					}
					break;
				case 'Last Claim Date':
					if($filter_on_from!="" && $filter_on_to!=""){
						$query.=" AND patient_charge_list.Re_submitted_date BETWEEN '$filter_on_from' AND '$filter_on_to'";
					}else if($filter_on_from!=""){
						$query.=" AND patient_charge_list.Re_submitted_date >='$filter_on_from'";
					}else if($filter_on_to!=""){
						$query.=" AND patient_charge_list.Re_submitted_date <='$filter_on_to'";
					}
					break;
			}
		}
	}
}

##AGING FILTER
$aging_start=($_POST['aging_from']>0)?$_POST['aging_from']:0;
$aging_to=($_POST['aging_to'] && $_POST['aging_to']!='')?$_POST['aging_to']:'181';
$all_due = false;
if($aging_to == '181'){
	$all_due = true;
}
$query .=" AND DATEDIFF(NOW(),patient_charge_list.date_of_service)>=$aging_start";
if($all_due == false){
	$query .= " AND DATEDIFF(NOW(),patient_charge_list.date_of_service)<=$aging_to";
}

##HIDE 30 FILTER
$query.="";
##BALANCE FILTER
//if($_POST['balance_from']>0)$query.=" AND patient_charge_list.totalBalance>=$_POST[balance_from]";
//if($_POST['balance_to']>0)$query.=" AND patient_charge_list.totalBalance<=$_POST[balance_to]";
##GROUP BY FILTER
$query.="";
##FOLLOW ON FILTER
if($_POST['follow_up_from']!="" || $_POST['follow_up_to']!=""){
	if($_POST['follow_up_from'])$follow_up_from=getDateFormatDB($_POST['follow_up_from']);
	if($_POST['follow_up_to'])$follow_up_to=getDateFormatDB($_POST['follow_up_to']);
	if($follow_up_from!="" && $follow_up_to!=""){
		$query.=" AND tm_assigned_rules.reminder_date BETWEEN '$follow_up_from' AND '$follow_up_to'";
	}else if($follow_up_from!=""){
		$query.=" AND tm_assigned_rules.reminder_date >='$follow_up_from'";
	}else if($follow_up_to!=""){
		$query.=" AND tm_assigned_rules.reminder_date <='$follow_up_to'";
	}
}
##OPERATOR FILTER
$follow_up_opr_id= (sizeof($_POST['follow_up_opr_id'])>0) ? implode(',',$_POST['follow_up_opr_id']) : '';
if($follow_up_opr_id)$query.=" AND tm_assigned_rules.operatorid IN($follow_up_opr_id)";
##LAST STATUS FILTER
if($_POST['last_status']!=""){
	$last_status=0;
	if($_POST['last_status']=="Done"){
		$last_status=1;
	}
	$query.=" AND tm_assigned_rules.status ='$last_status'";
}
##REJECTION STATUS FILTER
if($_POST['rej_status']!=""){
	if($_POST['rej_status']=="Pending"){
		$query.=" AND deniedpayment.status='0'";
	}
	if($_POST['rej_status']=="Done"){
		$query.=" AND deniedpayment.status='1'";
	}
}
##REJECTION CODE FILTER
if(count($_POST['rej_code'])>0){
	foreach($_POST['rej_code'] as $r_code){
		$r_code_exp=explode(' ',$r_code);
		$cas_rej_arr[]="(deniedpayment.CAS_type='$r_code_exp[0]' and deniedpayment.CAS_code='$r_code_exp[1]')";
	}
	$query.=" and (".implode(' or ', $cas_rej_arr).")";
}
##SHOW TASK FILTER
##WHAT USER FILTER
if($_POST['show_task']!=""){
	if($_POST['show_task']=="Assigned"){
		//$task_query =" AND paymentscomment.task_assign_for!=''";
	}else if($_POST['show_task']=="Unassigned"){
		//$task_query =" AND paymentscomment.task_assign_for=''";
	}
	/*$qry = "Select patient_id,encounter_id,task_assign_for from paymentscomment where patient_id>0 $task_query";
	$res = imw_query($qry);
	while ($row = imw_fetch_assoc($res)){
		if($row['task_assign_for']!=""){
			$task_assign_for_exp=explode(',',$row['task_assign_for']);
			foreach($task_assign_for_exp as $task_user){
				$comm_task_arr[$row['patient_id']][$row['encounter_id']][$task_user] = $task_user;
			}
		}
	}*/
}

##PRINT STATEMENT FILTER
if($_POST['printStsStatus']!=""){
	if($_POST['printStsStatus']=="Yes"){
		$query.=" AND (patient_data.hold_statement='0' AND patient_charge_list.collection!='true' AND patient_charge_list.vipStatus!='true' and patient_charge_list_details.differ_patient_bill!='true')";
	}else if($_POST['printStsStatus']=="No"){
		$query.=" AND (patient_data.hold_statement='1' or patient_charge_list.collection='true' or patient_charge_list.vipStatus='true' or patient_charge_list_details.differ_patient_bill='true')";
	}
}

##FIRST STATEMENT FILTER
$query.="";
##LAST STATEMENT FILTER
$query.="";
##OVERDUES FILTER
if($_POST['overdue_days']!=""){
	if($_POST['overdue_days']=="Payment Days"){
		if($_POST['overdue_days_from']>0){
			$query .=" AND DATEDIFF(NOW(),patient_charge_list.postedDate)>=".$_POST['overdue_days_from'];
		}
		if($_POST['overdue_days_to']>0){
			$query .= " AND DATEDIFF(NOW(),patient_charge_list.postedDate)<=".$_POST['overdue_days_to'];
		}
		if($_POST['overdue_days_from']>0 || $_POST['overdue_days_to']>0){
			$query .= " AND (patient_charge_list.postedDate>=patient_charge_list.lastPaymentDate)";
		}
	}
	if($_POST['overdue_days']=="Claim Filing Days"){
		if($_POST['overdue_days_from']>0){
			$query .=" AND DATEDIFF(NOW(),patient_charge_list.date_of_service)>=".$_POST['overdue_days_from'];
		}
		if($_POST['overdue_days_to']>0){
			$query .= " AND DATEDIFF(NOW(),patient_charge_list.date_of_service)<=".$_POST['overdue_days_to'];
		}
		if($_POST['overdue_days_from']>0 || $_POST['overdue_days_to']>0){
			$query .= " AND patient_charge_list.postedDate='0000-00-00'";
		}
	}
}
##PRAC CODE FILTER
$cpt_codes= (sizeof($_POST['cpt_code'])>0) ? implode(',',$_POST['cpt_code']) : '';
if($cpt_codes)$query.=" AND patient_charge_list_details.procCode IN($cpt_codes)";
##PATIENT AS FILTER
if($_POST['patient_as']>0)$query.=" AND patient_data.pat_account_status=$_POST[patient_as]";
##STATEMENT COUNT FILTER
if($_POST['statement_count_from']>0)$query.=" AND patient_data.acc_statement_count>=$_POST[statement_count_from]";
if($_POST['statement_count_to']>0)$query.=" AND patient_data.acc_statement_count<=$_POST[statement_count_to]";

if($_POST['detail_ins_id']>0 || $_POST['detail_ins_id']!=""){
	if($group_by=="Facility"){
		$query.=" AND (patient_charge_list.billing_facility_id=".$_POST['detail_ins_id'].")";
	}else if($group_by=="POS Facility"){
		$query.=" AND (patient_charge_list_details.posFacilityId=".$_POST['detail_ins_id'].")";
	}else if($group_by=="Provider"){
		$query.=" AND (patient_charge_list.primaryProviderId=".$_POST['detail_ins_id'].")";
	}else{
		$query.=" AND (patient_charge_list.primaryInsuranceCoId=".$_POST['detail_ins_id']." OR patient_charge_list.secondaryInsuranceCoId=".$_POST['detail_ins_id']." OR patient_charge_list.tertiaryInsuranceCoId=".$_POST['detail_ins_id'].")";
	}
	if($_POST['detail_pat_id']>0){
		$query.=" AND patient_charge_list.patient_id=".$_POST['detail_pat_id'];
	}
}

$ins_prop_ord="";
$ins_priority_arr=array();
if($_POST['ord_by_field']=="enc_ins_type_ord"){
	$_POST['ins_priority']=array('1','2','3');
	if($_POST['ord_by_ascdesc']=="DESC"){
		$_POST['ins_priority']=array('3','2','1');
	}
}
if(sizeof($_POST['ins_priority'])>0){
	foreach($_POST['ins_priority'] as $ins_priority_val)
	{
		if($_POST['detail_ins_id']>0){
			$ins_priority_arr[$ins_priority_val]=$ins_priority_val;
		}
	}
	if($_POST['detail_ins_id']>0){
		for($g=1;$g<=3;$g++){
			if($ins_priority_arr[$g]<=0){
				$ins_priority_arr[$g]=$g;
			}
		}
		$ins_priority_arr[0]=0;
	}
}else{
	$ins_priority_arr[0]=0;
}

//$query .=" and patient_charge_list.patient_id in(6042,28022,8748)";
if($_POST['ord_by_field']=="enc_dos_ord" && $_POST['detail_ins_id']>0){
	$query .= " ORDER BY patient_charge_list.date_of_service ".$_POST['ord_by_ascdesc'].",patient_data.lname".$pat_ord_by.",patient_data.fname".$pat_ord_by;
}else if($_POST['ord_by_field']=="enc_doc_ord" && $_POST['detail_ins_id']>0){
	$query .= " ORDER BY patient_charge_list_details.entered_date ".$_POST['ord_by_ascdesc'].",patient_data.lname".$pat_ord_by.",patient_data.fname".$pat_ord_by;
}else if($_POST['ord_by_field']=="pat_dob_ord" && $_POST['detail_ins_id']>0){
	$query .= " ORDER BY patient_data.DOB ".$_POST['ord_by_ascdesc'].",patient_data.lname".$pat_ord_by.",patient_data.fname".$pat_ord_by;
}else if($_POST['ord_by_field']=="enc_fac_ord" && $_POST['detail_ins_id']>0){
	$query .= " ORDER BY facility.name ".$_POST['ord_by_ascdesc'].",patient_data.lname".$pat_ord_by.",patient_data.fname".$pat_ord_by;
}else if($_POST['ord_by_field']=="enc_pos_fac_ord" && $_POST['detail_ins_id']>0){
	$query .= " ORDER BY pos_facilityies_tbl.facilityPracCode ".$_POST['ord_by_ascdesc'].",patient_data.lname".$pat_ord_by.",patient_data.fname".$pat_ord_by;
}else if($_POST['ord_by_field']=="enc_prov_ord" && $_POST['detail_ins_id']>0){
	$query .= " ORDER BY users.fname ".$_POST['ord_by_ascdesc'].",users.lname ".$_POST['ord_by_ascdesc'].",patient_data.lname".$pat_ord_by.",patient_data.fname".$pat_ord_by;
}else if($_POST['ord_by_field']=="enc_ins_pol_ord" && $_POST['detail_ins_id']>0){
	$query .= " ORDER BY insurance_data.policy_number ".$_POST['ord_by_ascdesc'].",patient_data.lname".$pat_ord_by.",patient_data.fname".$pat_ord_by;
}else if($_POST['ord_by_field']=="enc_ins_case_ord" && $_POST['detail_ins_id']>0){
	$query .= " ORDER BY insurance_case_types.case_name ".$_POST['ord_by_ascdesc'].",patient_data.lname".$pat_ord_by.",patient_data.fname".$pat_ord_by;
}else if($_POST['ord_by_field']=="enc_claim_ord" && $_POST['detail_ins_id']>0){
	$query .= " ORDER BY patient_charge_list.firstSubmitDate ".$_POST['ord_by_ascdesc'].",patient_data.lname".$pat_ord_by.",patient_data.fname".$pat_ord_by;
}else if($_POST['ord_by_field']=="enc_pt_st_ord"){
	if($_POST['ord_by_ascdesc']=="DESC"){
		$query .= " ORDER BY patient_data.hold_statement asc,patient_charge_list.vipStatus asc,patient_charge_list.collection asc,patient_charge_list_details.differ_patient_bill asc,patient_data.lname".$pat_ord_by.",patient_data.fname".$pat_ord_by;
	}else{
		$query .= " ORDER BY patient_data.hold_statement desc,patient_charge_list.vipStatus desc,patient_charge_list.collection desc,patient_charge_list_details.differ_patient_bill desc,patient_data.lname".$pat_ord_by.",patient_data.fname".$pat_ord_by;
	}
}else{
	$query .= " ORDER BY patient_data.lname".$pat_ord_by.",patient_data.fname".$pat_ord_by.",patient_charge_list.date_of_service".$dos_ord_by;
}
$row = imw_query($query);
if(imw_num_rows($row)>0){
	while($res_row=imw_fetch_assoc($row)){
		$res_arr[$res_row['charge_list_detail_id']]=$res_row;
		$main_chld_arr[$res_row['charge_list_detail_id']]=$res_row['charge_list_detail_id'];
		$main_enc_arr[$res_row['encounter_id']]=$res_row['encounter_id'];
		$arr_assign_to_exp=explode(',',$res_row['ar_assign_to']);
		if(count($arr_assign_to_exp)>0){
			foreach($arr_assign_to_exp as $arr_assign_to_exp_key => $arr_assign_to_exp_val){
				if($arr_assign_to_exp_val>0){
					$comm_task_arr[$res_row['patient_id']][$res_row['charge_list_detail_id']][$arr_assign_to_exp_val] = $arr_assign_to_exp_val;
				}
			}
		}
	}

	if($as_of_db!=""){
		$main_chld_imp=implode(',',$main_chld_arr);
		$chld_row=imw_query("select * from report_enc_trans where charge_list_detail_id in($main_chld_imp) and trans_type!='charges' and trans_dot<='$as_of_db' order by report_trans_id asc");
		while($chld_res=imw_fetch_assoc($chld_row)){
			$index_parent_id=$chld_res['report_trans_id'];
			if($chld_res['parent_id']>0){
				$index_parent_id=$chld_res['parent_id'];
			}
			$chld_res_arr[$chld_res['charge_list_detail_id']][$index_parent_id]=$chld_res;
		}

		$getProcedureDetailsQry = imw_query("SELECT cpt_prac_code,cpt_fee_id FROM cpt_fee_tbl WHERE not_covered=1");
		while($getProcedureDetailsRows = imw_fetch_assoc($getProcedureDetailsQry)){
			$chld_cpt_ref_arr[$getProcedureDetailsRows['cpt_fee_id']]=$getProcedureDetailsRows['cpt_prac_code'];
		}

		$getDeductDetailsQry = imw_query("SELECT deduct_ins_id,deduct_amount,charge_list_detail_id FROM payment_deductible WHERE deductible_by='Insurance' and charge_list_detail_id in($main_chld_imp) and delete_deduct='0'");
		while($getDeductDetailsRows = imw_fetch_assoc($getDeductDetailsQry)){
			$insCompany=0;
			if($getDeductDetailsRows['deduct_ins_id']>0){
				if($getDeductDetailsRows['deduct_ins_id']==$res_arr[$getDeductDetailsRows['charge_list_detail_id']]['primaryInsuranceCoId']){
					$insCompany=1;
				}else if($getDeductDetailsRows['deduct_ins_id']==$res_arr[$getDeductDetailsRows['charge_list_detail_id']]['secondaryInsuranceCoId']){
					$insCompany=2;
				}else if($getDeductDetailsRows['deduct_ins_id']==$res_arr[$getDeductDetailsRows['charge_list_detail_id']]['tertiaryInsuranceCoId']){
					$insCompany=3;
				}
			}
			$chld_deduct_arr[$getDeductDetailsRows['charge_list_detail_id']][$insCompany][]=$getDeductDetailsRows['deduct_amount'];
		}
	}
	if(count($main_chld_arr)>0){
		$main_chld_imp=implode(',',$main_chld_arr);
		$getDeniedDetailsQry = imw_query("SELECT deniedById,deniedAmount,charge_list_detail_id,patient_id,next_responsible_by,CAS_type,CAS_code,deniedDate FROM deniedpayment WHERE deniedBy='Insurance' and charge_list_detail_id in($main_chld_imp) and denialDelStatus='0'");
		while($getDeniedDetailsRows = imw_fetch_assoc($getDeniedDetailsQry)){
			$insCompany=0;
			if($getDeniedDetailsRows['deniedById']>0){
				if($getDeniedDetailsRows['deniedById']==$res_arr[$getDeniedDetailsRows['charge_list_detail_id']]['primaryInsuranceCoId']){
					$insCompany=1;
				}else if($getDeniedDetailsRows['deniedById']==$res_arr[$getDeniedDetailsRows['charge_list_detail_id']]['secondaryInsuranceCoId']){
					$insCompany=2;
				}else if($getDeniedDetailsRows['deniedById']==$res_arr[$getDeniedDetailsRows['charge_list_detail_id']]['tertiaryInsuranceCoId']){
					$insCompany=3;
				}
			}
			if($getDeniedDetailsRows['next_responsible_by']>0){
				$chld_denied_arr[$getDeniedDetailsRows['charge_list_detail_id']][$insCompany][]=$getDeniedDetailsRows['deniedAmount'];
			}
			$chld_rejected_arr[$getDeniedDetailsRows['charge_list_detail_id']]['denied_date']=$getDeniedDetailsRows['deniedDate'];
			$cas_code=$getDeniedDetailsRows['CAS_type'];
			if($getDeniedDetailsRows['CAS_code']!=""){
				$cas_code=$cas_code.' - '.$getDeniedDetailsRows['CAS_code'];
			}
			$chld_rejected_arr[$getDeniedDetailsRows['charge_list_detail_id']]['cas_code']=$cas_code;
		}
	}
	
	if(count($main_enc_arr)>0){
		$main_enc_imp=implode(',',$main_enc_arr);
		$getSubmittedDetailsQry = imw_query("SELECT encounter_id,submited_date FROM submited_record WHERE encounter_id in($main_enc_imp) order by submited_id asc");
		while($getSubmittedDetailsRows = imw_fetch_assoc($getSubmittedDetailsQry)){
			$enc_submitted_arr[$getSubmittedDetailsRows['encounter_id']]=$getSubmittedDetailsRows['submited_date'];
		}
	}

	#CLEANING UP OLD FILES IF ANY
	$filebasepath =data_path().'UserId_'.$_SESSION['authId'].'/tmp/ar/';
	if( !is_dir($filebasepath) ){
		mkdir($filebasepath, 0755, true);
		chown($filebasepath, 'apache');
	}
	#HTML
	foreach(glob($filebasepath."/*.html") as $html_file_names){
		if($html_file_names){unlink($html_file_names);}
	}
	#CSV
	foreach(glob($filebasepath."/*.csv") as $csv_file_names){
		if($csv_file_names){unlink($csv_file_names);}
	}
	//CSV FILE NAME
	$file_name="/ar/ar_worksheet_".time().".csv";
	$csv_file_name= write_html("", $file_name);
	if(file_exists($csv_file_name)){
		unlink($csv_file_name);
	}
	$fp = fopen ($csv_file_name, 'a+');
}
foreach($res_arr as $res){
	$stm_status="no";
	$last_stm_count=count($patientStmArr[$res['patient_id']])-1;
	if(($patientStmArr[$res['patient_id']][0]>=$f_sts_from || $f_sts_from=="") && ($patientStmArr[$res['patient_id']][0]<=$f_sts_to || $f_sts_to=="")){
		//Condition true
	} else continue;

	if(($patientStmArr[$res['patient_id']][$last_stm_count]>=$l_sts_from || $l_sts_from=="") && ($patientStmArr[$res['patient_id']][$last_stm_count]<=$l_sts_to || $l_sts_to=="")){
		//Condition true
	} else continue;

	if(($patientStmArr[$res['patient_id']][$last_stm_count]>=$l_sts_from || $l_sts_from=="") && ($patientStmArr[$res['patient_id']][$last_stm_count]<=$l_sts_to || $l_sts_to=="")){
		//Condition true
	} else continue;

	if($_POST['show_task']=="Assigned"){
		if(count($comm_task_arr[$res['patient_id']][$res['charge_list_detail_id']])<=0){
			continue;
		}
		if(count($_POST['what_user'])>0){
			$task_assign_chk=array();
			foreach($comm_task_arr[$res['patient_id']][$res['charge_list_detail_id']] as $task_user)
			{
				if($what_user_sel[$task_user]>0){
					//echo $what_user_sel[$task_user].'-'.$task_user;
					$task_assign_chk[$task_user]=$what_user_sel[$task_user];
				}
			}
			if(count($task_assign_chk)<=0)continue;
		}
	}

	if($_POST['show_task']=="Unassigned"){
		if(count($comm_task_arr[$res['patient_id']][$res['charge_list_detail_id']])>0){
			continue;
		}
	}


	$csv_data="AR WorkSheet$pfx \n";
	$printFile=true;
	$ins_ar_heading_data=$csv_ins_ar_heading_data="";
	$ar_cols=0;
	$dos_date_diff=$res['dos_date_diff'];
	if($_POST['aging_by']=="First Claim Date"){
		$dos_date_diff=$res['fsd_date_diff'];
	}else if($_POST['aging_by']=="Last Claim Date"){
		$dos_date_diff=$res['rsd_date_diff'];
	}

	$ins_type=0;
	if((sizeof($_POST['ins_priority'])>0 || $_POST['ord_by_field']=="enc_ins_type_ord") && $_POST['detail_ins_id']>0){
		if($res['primaryInsuranceCoId']==$_POST['detail_ins_id']){
			$ins_type=1;
		}else if($res['secondaryInsuranceCoId']==$_POST['detail_ins_id']){
			$ins_type=2;
		}else if($res['tertiaryInsuranceCoId']==$_POST['detail_ins_id']){
			$ins_type=3;
		}
	}

	//Start calculate
	if($as_of_db!=""){
		$chld_trans_arr=$tot_chld_trans_amt_arr=array();
		foreach($chld_res_arr[$res['charge_list_detail_id']] as $chld_res_key => $chld_res_val){
			$chld_trans_arr=$chld_res_arr[$res['charge_list_detail_id']][$chld_res_key];
			$insCompany=0;
			$trans_type=strtolower($chld_trans_arr['trans_type']);
			if($chld_trans_arr['trans_ins_id']>0){
				if($chld_trans_arr['trans_ins_id']==$res['primaryInsuranceCoId']){
					$insCompany=1;
				}else if($chld_trans_arr['trans_ins_id']==$res['secondaryInsuranceCoId']){
					$insCompany=2;
				}else if($chld_trans_arr['trans_ins_id']==$res['tertiaryInsuranceCoId']){
					$insCompany=3;
				}
			}
			if($trans_type=='negative payment' || $trans_type=='copay-negative payment'){
				$tot_chld_trans_amt_arr[$chld_trans_arr['charge_list_detail_id']][$insCompany][]="-".$chld_trans_arr['trans_amount'];
			}
			if($trans_type=='copay-paid' || $trans_type=='paid' || $trans_type=='interest payment' || $trans_type=='deposit'){
				$tot_chld_trans_amt_arr[$chld_trans_arr['charge_list_detail_id']][$insCompany][]=$chld_trans_arr['trans_amount'];
			}
			if($trans_type=='co-insurance' || $trans_type=='co-payment'){
				$tot_chld_coins_trans_amt_arr[$chld_trans_arr['charge_list_detail_id']][$insCompany][]=$chld_trans_arr['trans_amount'];
				$tot_chld_trans_amt_arr[$chld_trans_arr['charge_list_detail_id']][$insCompany][]=0;
			}
			if($trans_type=='credit'){
				$tot_chld_trans_amt_arr[$chld_trans_arr['charge_list_detail_id']][$insCompany][]=$chld_trans_arr['trans_amount'];
			}
			if($trans_type=='debit'){
				$tot_chld_trans_amt_arr[$chld_trans_arr['charge_list_detail_id']][$insCompany][]="-".$chld_trans_arr['trans_amount'];
			}
			if($trans_type=='default_writeoff'){
				$tot_chld_trans_amt_arr[$chld_trans_arr['charge_list_detail_id']][$insCompany][]=$chld_trans_arr['trans_amount'];
			}
			if($trans_type=='write off' || $trans_type=='discount' || $trans_type=='over adjustment'){
				$tot_chld_trans_amt_arr[$chld_trans_arr['charge_list_detail_id']][$insCompany][]=$chld_trans_arr['trans_amount'];
			}
			if($trans_type=='adjustment' || $trans_type=='returned check'){
				$tot_chld_trans_amt_arr[$chld_trans_arr['charge_list_detail_id']][$insCompany][]="-".$chld_trans_arr['trans_amount'];
			}
			if($trans_type=='refund'){
				$tot_chld_trans_amt_arr[$chld_trans_arr['charge_list_detail_id']][$insCompany][]=$chld_trans_arr['trans_amount'];
			}
		}
		$pri_paid=array_sum($tot_chld_trans_amt_arr[$res['charge_list_detail_id']][1]);
		$sec_paid=array_sum($tot_chld_trans_amt_arr[$res['charge_list_detail_id']][2]);
		$tri_paid=array_sum($tot_chld_trans_amt_arr[$res['charge_list_detail_id']][3]);
		$pat_paid=array_sum($tot_chld_trans_amt_arr[$res['charge_list_detail_id']][0]);
		$newBalance=$res['totalAmount']-($pri_paid+$sec_paid+$tri_paid+$pat_paid);

		$pri_due=$sec_due=$tri_due=$pat_due=0;
		$pat_heard_ins="false";
		$pri_heard_ins=$sec_heard_ins=$tri_heard_ins="true";
		if(count($tot_chld_trans_amt_arr[$res['charge_list_detail_id']][1])==0 && $res['primaryInsuranceCoId']>0){
			$pri_heard_ins="false";
			$pri_due=$newBalance;
		}else if(count($tot_chld_trans_amt_arr[$res['charge_list_detail_id']][2])==0 && $res['secondaryInsuranceCoId']>0){
			$sec_heard_ins="false";
			$sec_due=$newBalance;
		}else if(count($tot_chld_trans_amt_arr[$res['charge_list_detail_id']][3])==0 && $res['tertiaryInsuranceCoId']>0){
			$tri_heard_ins="false";
			$tri_due=$newBalance;
		}else{
			$pat_heard_ins="true";
			$pat_due=$newBalance;
		}
	}else{
		$newBalance=$res['newBalance'];
		$pri_due=$res['pri_due'];
		$sec_due=$res['sec_due'];
		$tri_due=$res['tri_due'];
		$pat_due=$res['pat_due'];
	}

	for($a=$aging_start;$a<=$aging_to;$a++){
		$start = $a;
		$a = $a > 0 ? $a - 1 : $a;
		$end = ($a) + $aggingCycle;

		//Array Index Format (1=Insurance, 2=Facility, 3=Provider, 4=Insurance Type)
		if($dos_date_diff >= $start && ($dos_date_diff <= $end || $start>=181)){
			if($group_by=="POS Facility"){
				$ins_res_arr[0][$res['posFacilityId']][0][$start][]=$newBalance;
				$ins_chld_res_arr[0][$res['posFacilityId']][0][$res['charge_list_detail_id']][$start][]=$newBalance;
				$ins_pat_res_arr[0][$res['posFacilityId']][0][$res['patient_id']][$start][]=$newBalance;
				$ar_data_arr[0][$res['posFacilityId']][0][$ins_type][]=$res;
			}else if($group_by=="Provider"){
				$ins_res_arr[0][0][$res['primaryProviderId']][$start][]=$newBalance;
				$ins_chld_res_arr[0][0][$res['primaryProviderId']][$res['charge_list_detail_id']][$start][]=$newBalance;
				$ins_pat_res_arr[0][0][$res['primaryProviderId']][$res['patient_id']][$start][]=$newBalance;
				$ar_data_arr[0][0][$res['primaryProviderId']][$ins_type][]=$res;
			}else{
				if($pri_due>0 && $res['primaryInsuranceCoId']>0 && (count($arrInsurance)==0 || in_array($res['primaryInsuranceCoId'],$arrInsurance)) && ($_POST['detail_ins_id']<=0 || $res['primaryInsuranceCoId']==$_POST['detail_ins_id']) && (count($ins_type_arr)==0 || in_array('primaryInsuranceCoId',$ins_type_arr))){
					$ins_res_arr[$res['primaryInsuranceCoId']][0][0][$start][]=$pri_due;
					$ins_chld_res_arr[$res['primaryInsuranceCoId']][0][0][$res['charge_list_detail_id']][$start][]=$pri_due;
					$ins_pat_res_arr[$res['primaryInsuranceCoId']][0][0][$res['patient_id']][$start][]=$pri_due;
					$ar_data_arr[$res['primaryInsuranceCoId']][0][0][$ins_type][]=$res;
				}
				if($sec_due>0 && $res['secondaryInsuranceCoId']>0 && (count($arrInsurance)==0 || in_array($res['secondaryInsuranceCoId'],$arrInsurance)) && ($_POST['detail_ins_id']<=0 || $res['secondaryInsuranceCoId']==$_POST['detail_ins_id']) && (count($ins_type_arr)==0 || in_array('secondaryInsuranceCoId',$ins_type_arr))){
					$ins_res_arr[$res['secondaryInsuranceCoId']][0][0][$start][]=$sec_due;
					$ins_chld_res_arr[$res['secondaryInsuranceCoId']][0][0][$res['charge_list_detail_id']][$start][]=$sec_due;
					$ins_pat_res_arr[$res['secondaryInsuranceCoId']][0][0][$res['patient_id']][$start][]=$sec_due;
					$ar_data_arr[$res['secondaryInsuranceCoId']][0][0][$ins_type][]=$res;
				}
				if($tri_due>0 && $res['tertiaryInsuranceCoId']>0 && (count($arrInsurance)==0 || in_array($res['tertiaryInsuranceCoId'],$arrInsurance)) && ($_POST['detail_ins_id']<=0 || $res['tertiaryInsuranceCoId']==$_POST['detail_ins_id']) && (count($ins_type_arr)==0 || in_array('tertiaryInsuranceCoId',$ins_type_arr))){
					$ins_res_arr[$res['tertiaryInsuranceCoId']][0][0][$start][]=$tri_due;
					$ins_chld_res_arr[$res['tertiaryInsuranceCoId']][0][0][$res['charge_list_detail_id']][$start][]=$tri_due;
					$ins_pat_res_arr[$res['tertiaryInsuranceCoId']][0][0][$res['patient_id']][$start][]=$tri_due;
					$ar_data_arr[$res['tertiaryInsuranceCoId']][0][0][$ins_type][]=$res;
				}
				if($pat_due>0 && count($arrInsurance)==0 && (count($ins_type_arr)==0 || in_array('Self Pay',$ins_type_arr))){
					$ins_res_arr[0][0][0][$start][]=$pat_due;
					$ins_chld_res_arr[0][0][0][$res['charge_list_detail_id']][$start][]=$pat_due;
					$ins_pat_res_arr[0][0][0][$res['patient_id']][$start][]=$pat_due;
					$ar_data_arr[0][0][0][0][]=$res;
				}
			}
		}
		
		$show_ord_sign=array();
		if($_POST['ord_by_field']!=""){
			if($_POST['ord_by_ascdesc']=="DESC"){
				$show_ord_sign[$_POST['ord_by_field']]='<span class="glyphicon glyphicon-chevron-down pull-right"></span>';
			}else{
				$show_ord_sign[$_POST['ord_by_field']]='<span class="glyphicon glyphicon-chevron-up pull-right"></span>';
			}
		}
		
		if($start==181){
			$csv_ins_ar_heading_data.="181+$pfx";
			if($_POST['detail_pat_id']>0){
				$ins_ar_heading_data .='<td class="text_b_w txt_r text-nowrap">181+</td>';
			}else{
				$ins_ar_heading_data .='<td class="text_b_w txt_r text-nowrap" onClick="ar_ord_by(\'aging_181\')">181+'.$show_ord_sign['aging_181'].'</td>';
			}
		}else{
			if($_POST['detail_pat_id']>0){
				$ins_ar_heading_data .='<td class="text_b_w txt_r text-nowrap">'.$start.' - '.$end.'</td>';
			}else{
				$ins_ar_heading_data .='<td class="text_b_w txt_r text-nowrap" onClick="ar_ord_by(\'aging_'.$start.'\')">'.$start.' - '.$end.''.$show_ord_sign['aging_'.$start].'</td>';
			}
			$csv_ins_ar_heading_data.="\"$start - $end\"".$pfx;
		}
		$a += $aggingCycle;
		$ar_cols=$ar_cols+1;
	}
	$ar_pat_id[$res['patient_id']]=$res['patient_id'];
	if($res['collection']=='true' || $res['hold_statement']==1 || $res['vipStatus']=='true' || $res['differ_patient_bill']=='true'){
		$collection_pat_id[$res['patient_id']][$res['encounter_id']] = $res['patient_id'];
	}
}
if(count($ar_pat_id)>0){
	$ar_pat_id_str=implode(',',$ar_pat_id);
	$qry=imw_query("Select type,pid,provider,policy_number,ins_caseid FROM insurance_data WHERE pid IN(".$ar_pat_id_str.") ORDER BY id");
	while($res=imw_fetch_array($qry)){
		$pat_ins_pol_data[$res['pid']][$res['ins_caseid']][$res['provider']][$res['type']] = $res['policy_number'];
		$ins_case_id_arr[$res['ins_caseid']]=$res['ins_caseid'];
	}

	$ins_case_id_str=implode(',',$ins_case_id_arr);
	$qry=imw_query("Select ins_caseid,ins_case_type FROM insurance_case WHERE ins_caseid IN(".$ins_case_id_str.") ORDER BY ins_caseid");
	while($res=imw_fetch_array($qry)){
		$ins_case_type_id[$res['ins_caseid']] = $res['ins_case_type'];
	}
	
	$qry=imw_query("Select encounter_id,encComments,reminder_date FROM paymentscomment WHERE patient_id IN(".$ar_pat_id_str.") ORDER BY commentId asc");
	while($res=imw_fetch_array($qry)){
		$pat_enc_comments[$res['encounter_id']]=$res;
	}
}

if($_POST['summary_detail']=='summary' || $_POST['summary_detail']==""){
	if(imw_num_rows($row)>0){
		$ins_comp_arr=$fac_comp_arr=$prov_comp_arr=array();
		if($group_by=="Facility"){
			$ins_comp_arr[0]=$prov_comp_arr[0]="";
			$fac_comp_arr=$facArr;
			$fac_comp_arr[0]='Other';
		}else if($group_by=="POS Facility"){
			$ins_comp_arr[0]=$prov_comp_arr[0]="";
			$fac_comp_arr=$PosFacArr;
			$fac_comp_arr[0]='Other';
		}else if($group_by=="Provider"){
			$ins_comp_arr[0]=$fac_comp_arr[0]="";
			$prov_comp_arr=$usersArr;
			$prov_comp_arr[0]['name']='Other';
		}else{
			$fac_comp_arr[0]=$prov_comp_arr[0]="";
			$ins_comp_arr=$insCompArr;
		}
		if(($_POST['detail_ins_id']>0 || $_POST['detail_ins_id']!="") && $_POST['detail_pat_id']>0){
			require_once('ar_result_detail.php');
		}else if($_POST['detail_ins_id']>0 || $_POST['detail_ins_id']!=""){
			require_once('ar_pat_result_detail.php');
		}else{
			require_once('ar_result_summary.php');
		}
	}
}
$HTMLCreated=0;
if($printFile == true){
	$HTMLCreated=1;
	fwrite($fp,$csv_data);
	fclose($fp);

	$pdf_content=$style;
	if($_POST['summary_detail']=='summary' || $_POST['summary_detail']=="")
	{
		if($_POST['detail_ins_id']>0){
			$pdf_content.='<page backbottom="4mm">';
		}else{
			$pdf_content.='<page backtop="6mm" backbottom="4mm">';
			$pdf_content.='<page_header>'.$pdf_header.'</page_header>';
		}


	}
	$pdf_content.=$pdf_body;
	$pdf_content.='<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>';
	$pdf_content.='</page>';

	$pdfName = '/ar/ar_'.$_SESSION['authId'].'_'.date('Y_m_d_h_i_s').'.html';
	$file_location = write_html($pdf_content,$pdfName);

}
echo ($html_body)?$html_body:'<div id="html_data_div" class="row"><div class="text-center alert alert-info">No Record Found.</div></div>';
?>
