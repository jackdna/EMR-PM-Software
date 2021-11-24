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

$qry = imw_query("select patient_id from patient_charge_list where collection = 'true' and del_status='0'");
$collectionPatientIdArr = array();
while($collectionQryRes=imw_fetch_array($qry)){	
	$collectionPatientIdArr[] = $collectionQryRes['patient_id'];
}
$collectionPatientIdStr = join(',',$collectionPatientIdArr);

//--- Get Charge_list_id -------
$qry = "select encounter_id,charge_list_id,primary_paid,primaryInsuranceCoId,secondaryInsuranceCoId,
		tertiaryInsuranceCoId,secondary_paid,tertiary_paid,totalBalance,overPayment from patient_charge_list
		where del_status='0' and patient_id in($patientId)";
if($vip_bill_not_pat>0){
	$qry .= " and vipStatus='false'";
}
if($fully_paid == 0){
	$qry .= " and (patient_charge_list.totalBalance > 0 or patient_charge_list.overPayment>0)";
}
if(empty($collectionPatientIdStr) == false){
	$qry .= " and patient_id not in($collectionPatientIdStr)";
}
if($startLname != '' && $rePrint == ''){
	$qry .= " and (statement_status = 0 or statement_date < '$Statement_Elapsed_date')";
}
$patientChargeListQry=imw_query($qry);
while($patientChargeListRow=imw_fetch_array($patientChargeListQry)){
	set_payment_trans($patientChargeListRow['encounter_id'],'',1);
	$patientChargeListRes2[] = $patientChargeListRow;
}

//--- get Self pay Company Id -----
$qry = imw_query("select id from insurance_companies where in_house_code = 'self pay'");
$selfPayArr = array();
while($insQryRes=imw_fetch_array($qry)){		
	$selfPayArr[] = $insQryRes['id'];
}

//--- Get Ins Diff For all Charges --------
$chk_total_balance = array();
for($i=0;$i<count($patientChargeListRes2);$i++){
	$chk_total_balance[] = $patientChargeListRes2[$i]['totalBalance'];
}
$charge_list_id_arr = array();
if(array_sum($chk_total_balance)>0){
	for($i=0;$i<count($patientChargeListRes2);$i++){
		$charge_list_id_arr[] = $patientChargeListRes2[$i]['charge_list_id'];
	}
}

$charge_list_id_str = join(',',$charge_list_id_arr);
$qry = imw_query("select charge_list_id,differ_insurance_bill,proc_selfpay from `patient_charge_list_details` 
		where del_status='0' and charge_list_id in($charge_list_id_str)");
$charge_list_id_arr = array();
$self_proc_all = array();
while($detailQryRes=imw_fetch_array($qry)){	
	$differ_insurance_bill = $detailQryRes['differ_insurance_bill'];
	$charge_list_id = $detailQryRes['charge_list_id'];
	if($differ_insurance_bill == 'true'){
		$charge_list_id_arr[$charge_list_id] = $charge_list_id;
	}
	$self_proc_all[$charge_list_id][] = $detailQryRes['proc_selfpay'];
}

$diff_charge_list_id_str = join(',',$charge_list_id_arr);

$get_charge_list_id = array();
if(array_sum($chk_total_balance)>0){
	for($i=0;$i<count($patientChargeListRes2);$i++){
		$encounter_id = $patientChargeListRes2[$i]['encounter_id'];
		$ins_comp_paid = false;
		$charge_list_id = $patientChargeListRes2[$i]['charge_list_id'];
		$primaryInsuranceCoId = $patientChargeListRes2[$i]['primaryInsuranceCoId'];	
		$primary_paid = $patientChargeListRes2[$i]['primary_paid'];
		$secondaryInsuranceCoId = $patientChargeListRes2[$i]['secondaryInsuranceCoId'];
		$secondary_paid = $patientChargeListRes2[$i]['secondary_paid'];
		$tertiaryInsuranceCoId = $patientChargeListRes2[$i]['tertiaryInsuranceCoId'];
		$tertiary_paid = $patientChargeListRes2[$i]['tertiary_paid'];
		$differ_id = $charge_list_id_arr[$charge_list_id];
		if($differ_id){
			$ins_comp_paid = true;	
		}
		$all_proc_self_chk=0;
		if(array_sum($self_proc_all[$charge_list_id])>0){
			//echo array_sum($self_proc_all[$charge_list_id])."==".count($self_proc_all[$charge_list_id]);
			if(array_sum($self_proc_all[$charge_list_id])>=count($self_proc_all[$charge_list_id])){
				$all_proc_self_chk=1;
			}
		}
		
		preg_match("/$charge_list_id/",$diff_charge_list_id_str,$insDiffChkArr);	
		if($primaryInsuranceCoId > 0 && count($insDiffChkArr) == 0 && $force_cond!='yes' && $all_proc_self_chk==0 && $full_enc==0){
			$priSel = $selfPayArr[$primaryInsuranceCoId];
			if($priSel != '' || $primary_paid == 'true'){
				$ins_comp_paid = true;
			}
			else{
				$ins_comp_paid = false;
			}
			if($secondaryInsuranceCoId > 0 && $ins_comp_paid == true){			
				$secSel = $selfPayArr[$secondaryInsuranceCoId];			
				if($secSel != '' || $secondary_paid == 'true'){
					$ins_comp_paid = true;
				}
				else{
					$ins_comp_paid = false;
				}
			}
			if($tertiaryInsuranceCoId > 0 && $ins_comp_paid == true){
				$terSel = $selfPayArr[$tertiaryInsuranceCoId];
				if($terSel != '' || $tertiary_paid == 'true'){
					$ins_comp_paid = true;
				}
				else{
					$ins_comp_paid = false;
				}
			}
		}
		else{
			$ins_comp_paid = true;
		}
		//if($ins_comp_paid == true){
			$get_charge_list_id[$charge_list_id] = $charge_list_id;
		//}
	}
}

$get_charge_list_id = array_values($get_charge_list_id);
$get_charge_listId = implode(',',$get_charge_list_id);
if($get_charge_listId){
	if($imp_g_code_proc!=""){
		$imp_g_code_proc="'".$imp_g_code_proc."'";
		$exclude_g_code_proc=" and cpt_fee_tbl.cpt_prac_code not in($imp_g_code_proc)";
	}
	$cptQry = imw_query("select cpt_fee_tbl.cpt_fee_id from cpt_category_tbl join cpt_fee_tbl
			on cpt_fee_tbl.cpt_cat_id = cpt_category_tbl.cpt_cat_id
			where (cpt_category_tbl.cpt_category like 'PQRI%' 
			or cpt_fee_tbl.cpt_prac_code like 'G%') $exclude_g_code_proc");
	//or lower(cpt_fee_tbl.cpt_desc) like '%comanage%'		
	$cptCodeArr = array();
	while($cptQryRes=imw_fetch_array($cptQry)){		
		$cptCodeArr[] = $cptQryRes['cpt_fee_id'];
	}
	$cptCodeStr = join(',',$cptCodeArr);
	
	$full_enc_cond="";
	$full_enc_chl_arr=array();
	if($force_cond!='yes'){
		if($full_enc>0){
			$chk_full_enc_qry=imw_query("select charge_list_id from patient_charge_list_details where charge_list_id in ($get_charge_listId) and pat_due>0 and del_status='0'");
			while($chk_full_enc_row=imw_fetch_array($chk_full_enc_qry)){
				$full_enc_chl_arr[]=$chk_full_enc_row['charge_list_id'];
			}
			if(count($full_enc_chl_arr)>0){
				$full_enc_chl_exp=implode(',',$full_enc_chl_arr);
				$full_enc_cond=" or patient_charge_list.charge_list_id in($full_enc_chl_exp)";
			}
		}
		$whr_pt_due=" and (patient_charge_list_details.pat_due>0 $full_enc_cond)";
	}
	if(empty($cptCodeStr) === false){
		$whr_gcode .= " and cpt_fee_tbl.cpt_fee_id not in ($cptCodeStr)";
	}
	//---- Main query to fetch the data --------
	$qry = "select patient_charge_list.charge_list_id,patient_charge_list.patient_id,
			date_format(patient_charge_list.date_of_service, '%m-%d-%y') as date_of_service,
			patient_charge_list.totalBalance,patient_charge_list.gro_id,
			patient_charge_list_details.charge_list_detail_id,
			patient_charge_list_details.diagnosis_id1,
			patient_charge_list_details.diagnosis_id2,
			patient_charge_list_details.diagnosis_id3,
			patient_charge_list_details.diagnosis_id4,
			patient_charge_list_details.diagnosis_id5,
			patient_charge_list_details.diagnosis_id6,
			patient_charge_list_details.diagnosis_id7,
			patient_charge_list_details.diagnosis_id8,
			patient_charge_list_details.diagnosis_id9,
			patient_charge_list_details.diagnosis_id10,
			patient_charge_list_details.diagnosis_id11,
			patient_charge_list_details.diagnosis_id12,
			patient_charge_list_details.modifier_id1,
			patient_charge_list_details.modifier_id2,
			patient_charge_list_details.modifier_id3,
			patient_data.lname,patient_data.fname,patient_data.mname,
			cpt_fee_tbl.cpt4_code,
			patient_charge_list_details.newBalance,
			patient_charge_list_details.pat_due,
			patient_charge_list_details.overPaymentForProc
			from patient_charge_list
			join patient_data on patient_data.id = patient_charge_list.patient_id
			join patient_charge_list_details on 
			patient_charge_list_details.charge_list_id = 
			patient_charge_list.charge_list_id
			join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = 
			patient_charge_list_details.procCode
			where patient_charge_list_details.del_status='0' and patient_charge_list.charge_list_id in ($get_charge_listId) 
			and patient_charge_list_details.differ_patient_bill != 'true'
			and patient_data.hold_statement='0'
			$whr_pt_due $whr_gcode
			order by patient_data.lname,patient_data.fname,
			patient_charge_list.date_of_service asc";
	$patientChargeList_Qry = imw_query($qry);
}

// --- Get modifiers_tbl Data ----
$qry = imw_query("select modifiers_id,mod_prac_code from modifiers_tbl WHERE delete_status = '0'");
$modifier_arr = array();
while($modifierQryRes=imw_fetch_array($qry)){	
	$modifiers_id = $modifierQryRes['modifiers_id'];
	$mod_prac_code = $modifierQryRes['mod_prac_code'];
	$modifier_arr[$modifiers_id] = $mod_prac_code;
}
$mainResult = array();
while($patientChargeListRes=imw_fetch_array($patientChargeList_Qry)){	
	$charge_list_detail_id = $patientChargeListRes['charge_list_detail_id'];
	$charge_list_id = $patientChargeListRes['charge_list_id'];
	$mainResult['charge_list_id'][$charge_list_id] = $patientChargeListRes;	
	$mainResult[$charge_list_id]['charge_list_detail_id'][] = $charge_list_detail_id;
	$mainResult[$charge_list_id][$charge_list_detail_id] = $patientChargeListRes;
}
$force_cond_chk="";
if($force_cond=='yes'){
	$force_cond_chk="checked";
}
$data = '
	<tr class="purple_bar">
		<td colspan="4">Statements</td>
		<td colspan="3">
			<div class="checkbox checkbox-inline">
				<input type="checkbox" name="force_print" id="force_print" '.$force_cond_chk.' onClick="getStatement();"/>
				<label for="force_print">Forcefully Print</label>
			</div>&nbsp;&nbsp;
			<div class="checkbox checkbox-inline">
				<input type="checkbox" name="text_print" id="text_print" value="1"/>
				<label for="text_print">Text File</label>
			</div>
		</td>
	</tr>
	<tr class="grythead">
		<th>
			<div class="checkbox">
				<input type="checkbox" name="selectAll" id="selectAll" onclick="selAll(this.checked);"/>
				<label for="selectAll"></label>
			</div>
		</th>
		<th>Patient - ID</th>
		<th>Dos</th>
		<th>CPT</th>
		<th>DX</th>
		<th>MOD</th>
		<th>Balance</th>
	</tr>';
if(count($mainResult)>0){
	$charge_list_id_arr = $mainResult['charge_list_id'];
	if(count($charge_list_id_arr)>0){
		foreach($charge_list_id_arr as $chargeListId => $detailArr){
			$pat_id=$detailArr['patient_id'];
			$patientName = $detailArr['lname'].', ';
			$patientName .= $detailArr['fname'].' ';
			$patientName .= $detailArr['mname'];
			$patientName = ucfirst(trim($patientName));
			$patientName .= ' - '.$detailArr['patient_id'];
			if(!empty($detailArr['date_of_service']) && $detailArr['date_of_service'] != '00-00-00') {
				$date_of_service = date(phpDateFormat(), strtotime(str_replace('-', '/', $detailArr['date_of_service'])));
			} else {
				$date_of_service = $detailArr['date_of_service'];
			}
			$totalBalance = $detailArr['totalBalance'];
			$detail_arr = $mainResult[$chargeListId]['charge_list_detail_id'];
			$cpt4_code_arr = array();
			$diagnosisIdArr = array();
			$modifierIdArr = array();
			$tot_pat_due = array();
			$tot_balance = array();
			$tot_pat_ovr_pay = array();
			for($d=0;$d<count($detail_arr);$d++){
				$detailId = $detail_arr[$d];
				$cpt4_code = $mainResult[$chargeListId][$detailId]['cpt4_code'];
				$cpt4_code_arr[$cpt4_code] = $cpt4_code;
				$diagnosis_id1 = $mainResult[$chargeListId][$detailId]['diagnosis_id1'];
				$diagnosis_id2 = $mainResult[$chargeListId][$detailId]['diagnosis_id2'];
				$diagnosis_id3 = $mainResult[$chargeListId][$detailId]['diagnosis_id3'];
				$diagnosis_id4 = $mainResult[$chargeListId][$detailId]['diagnosis_id4'];
				$diagnosis_id5 = $mainResult[$chargeListId][$detailId]['diagnosis_id5'];
				$diagnosis_id6 = $mainResult[$chargeListId][$detailId]['diagnosis_id6'];
				$diagnosis_id7 = $mainResult[$chargeListId][$detailId]['diagnosis_id7'];
				$diagnosis_id8 = $mainResult[$chargeListId][$detailId]['diagnosis_id8'];
				$diagnosis_id9 = $mainResult[$chargeListId][$detailId]['diagnosis_id9'];
				$diagnosis_id10 = $mainResult[$chargeListId][$detailId]['diagnosis_id10'];
				$diagnosis_id11 = $mainResult[$chargeListId][$detailId]['diagnosis_id11'];
				$diagnosis_id12 = $mainResult[$chargeListId][$detailId]['diagnosis_id12'];
				
				$diagnosisIdArr[$diagnosis_id1] = $diagnosis_id1;
				$diagnosisIdArr[$diagnosis_id2] = $diagnosis_id2;
				$diagnosisIdArr[$diagnosis_id3] = $diagnosis_id3;
				$diagnosisIdArr[$diagnosis_id4] = $diagnosis_id4;
				$diagnosisIdArr[$diagnosis_id5] = $diagnosis_id5;
				$diagnosisIdArr[$diagnosis_id6] = $diagnosis_id6;
				$diagnosisIdArr[$diagnosis_id7] = $diagnosis_id7;
				$diagnosisIdArr[$diagnosis_id8] = $diagnosis_id8;
				$diagnosisIdArr[$diagnosis_id9] = $diagnosis_id9;
				$diagnosisIdArr[$diagnosis_id10] = $diagnosis_id10;
				$diagnosisIdArr[$diagnosis_id11] = $diagnosis_id11;
				$diagnosisIdArr[$diagnosis_id12] = $diagnosis_id12;
				
				$modifier_id1 = $mainResult[$chargeListId][$detailId]['modifier_id1'];
				$modifier_id2 = $mainResult[$chargeListId][$detailId]['modifier_id2'];
				$modifier_id3 = $mainResult[$chargeListId][$detailId]['modifier_id3'];
				$modifierIdArr[$modifier_id1] = $modifier_arr[$modifier_id1];
				$modifierIdArr[$modifier_id2] = $modifier_arr[$modifier_id2];
				$modifierIdArr[$modifier_id3] = $modifier_arr[$modifier_id3];
				$tot_pat_due[$detailId]=$mainResult[$chargeListId][$detailId]['pat_due'];
				$tot_pat_ovr_pay[$detailId]=$mainResult[$chargeListId][$detailId]['overPaymentForProc'];
				if($force_cond!='yes' && $full_enc==0){
					$tot_balance[$detailId]=$tot_pat_due[$detailId]-$tot_pat_ovr_pay[$detailId];
				}else{
					$tot_balance[$detailId]=$mainResult[$chargeListId][$detailId]['newBalance']-$tot_pat_ovr_pay[$detailId];
				}
			}
			
			$cpt4_code = join(', ',array_unique($cpt4_code_arr));
			$diagnosisIdArr = array_unique($diagnosisIdArr);
			sort($diagnosisIdArr);
			if($diagnosisIdArr[0] == ''){
				array_shift($diagnosisIdArr);
			}
			$diagnosisId = join(', ',$diagnosisIdArr);
			$modifierIdArr = array_unique($modifierIdArr);
			sort($modifierIdArr);
			if($modifierIdArr[0] == ''){
				array_shift($modifierIdArr);
			}
			$modifierId = join(', ',$modifierIdArr);
			
			$gro_id=$mainResult[$chargeListId][$detailId]['gro_id'];
			
			$groupDetail = getRecords('groups_new','gro_id',$gro_id);
			$group_color=$groupDetail->group_color;
			if($group_color){
				if($group_color=='#FFFFFF'){
					$g_color="#ffffff";
					$f_class="text_10ab";
				}else{
					$g_color=$group_color;
					$f_class="text_10ab_white";
				}
			}else{
				$g_color="#ffffff";
				$f_class="text_10ab";
			}
			$TotPatDue=number_format(array_sum($tot_pat_due),2);
			$TotBalance='$'.number_format(array_sum($tot_balance),2);
			$TotBalance=str_replace('$-','-$',$TotBalance);
			$data .= '<tr>
					<td bgcolor="'.$g_color.'">
						<div class="checkbox">
							<input type="checkbox" name="chargeList[]" id="id_'.$chargeListId.'" value="'.$chargeListId.'"/>
							<label for="id_'.$chargeListId.'"></label>
						</div>
					</td>
					<td bgcolor="'.$g_color.'" class="'.$f_class.'" nowrap>'.$patientName.'</td>
					<td bgcolor="'.$g_color.'" class="'.$f_class.'" nowrap>'.$date_of_service.'</td>
					<td bgcolor="'.$g_color.'" class="'.$f_class.'">'.$cpt4_code.'</td>
					<td bgcolor="'.$g_color.'" class="'.$f_class.'">'.$diagnosisId.'</td>
					<td bgcolor="'.$g_color.'" class="'.$f_class.'">'.$modifierId.'</td>
					<td bgcolor="'.$g_color.'" class="'.$f_class.'">'.$TotBalance.'</td>
				<tr>';
		}
	}
	$buttons='<input type="button" id="printStatements" class="btn btn-success" value="Print Statement" onClick="checkRes();" name="printStatements">
		  <input type="button" id="emailStatements" class="btn btn-success" value="Print Statement and Send Email" name="emailStatements" onClick="send_email1();">
		  <input type="button" id="prevStatements" class="btn btn-danger" value="Cancel" name="prevStatements" onClick="window.location.href=\'accountingStatementsResult.php?Submit=Get Report&pevious=yes&patientId='.$pat_id.'\'">';
}
else{
	$data.= "<tr><td class='text-center lead' colspan='7'>".imw_msg('no_rec')."</td></tr>";
	$buttons='<input type="button" id="close" class="btn btn-danger" value="Close" name="close" onClick="window.close();">';
}
?>