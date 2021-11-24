<?php
$print_file = true;

if(empty($form_submitted) === false){	
	$print_file = false;	
	//-- OPERATOR INITIAL -------
	$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$opInitial = $authProviderNameArr[1][0];
	$opInitial .= $authProviderNameArr[0][0];
	$opInitial = strtoupper($opInitial);
	$curDate = date(phpDateFormat().' h:i A');

	if(trim($Start_date) == '' and trim($End_date) != ''){
		$Start_date = $End_date;
	}
	if(trim($End_date) == '' and trim($Start_date) != ''){
		$End_date = $Start_date;
	}
	
	$grp_id = implode(',',$grp_id);
	
	$qry = "select patient_charge_list.charge_list_id,patient_charge_list.insuranceDue,
			patient_charge_list.encounter_id,patient_charge_list.gro_id,
			patient_charge_list.patient_id,patient_charge_list.patientDue,
			patient_charge_list.copayPaid,
			patient_charge_list.primaryInsuranceCoId, patient_charge_list.secondaryInsuranceCoId,
			patient_charge_list.tertiaryInsuranceCoId, patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId',
			patient_charge_list.pri_copay + patient_charge_list.sec_copay AS patient_copay, 
			date_format(patient_charge_list.date_of_service,'".get_sql_date_format()."') as date_of_service,
			date_format(patient_data.DOB,'".get_sql_date_format()."') as DOB, patient_charge_list_details.totalAmount,
			patient_charge_list_details.paidForProc, patient_charge_list_details.newBalance as balForProc,
			patient_charge_list_details.write_off, patient_charge_list_details.creditProcAmount,
			patient_charge_list_details.charge_list_detail_id, patient_charge_list_details.units,patient_data.title,
			patient_data.lname,patient_data.fname,patient_data.mname,
			patient_data.street,patient_data.street2, patient_data.postal_code,patient_data.city,patient_data.state,
			pos_facilityies_tbl.facilityPracCode,users.lname as userLname, 
			users.fname as userFname,users.mname as usersMname, cpt_fee_tbl.cpt4_code
			from patient_charge_list join patient_charge_list_details on 
			patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id
			join patient_data on patient_data.id = patient_charge_list.patient_id
			join pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = patient_charge_list.facility_id
			join users on users.id = patient_charge_list.primary_provider_id_for_reports
			join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
			where patient_charge_list_details.del_status='0'";

	if(empty($patientId) === false){
		$startLname = '';
		$qry .= " AND patient_charge_list.patient_id in ($patientId)";
	}
	else if(trim($startLname) != ''){
		$qry .= " AND (trim(patient_data.lname) between '$startLname' and '$endLname'  
			or trim(patient_data.lname) like '$endLname%') and patient_data.lname != 'doe'";
	}				
			
	if($Start_date != ''){
		//--- CHANGE DATE FORMAT -------
		$sDate = $Start_date;
		$eDate = $End_date;
		$Start_date = getDateFormatDB($Start_date);
		$End_date = getDateFormatDB($End_date);	

		$qry .= " and patient_charge_list.date_of_service between '$Start_date' and '$End_date'";
	}
	if(trim($grp_id) != ''){
		$qry.= " and patient_charge_list.gro_id IN ($grp_id)";
	}
	$qry .= " order by patient_data.lname,patient_data.fname, patient_charge_list.date_of_service desc ";
	$rs = imw_query($qry)or die(imw_error());
	$qryRes = array();
	while($res = imw_fetch_assoc($rs)){
		$qryRes[] = $res;
	}
	if(count($qryRes)>0){
		$print_file = true;
		$ins_id_arr = array();
		$list_detail_id_arr = array();
		$arrEncounterIds =array();
		$arrPatientEncIds =array();
		$group_id_arr = array();
		$patient_due_arr = array();
		$arrPatsAllIns=array();
		for($i=0;$i<count($qryRes);$i++){
			$pat_id = $qryRes[$i]['patient_id'];
			$encounter_id = $qryRes[$i]['encounter_id'];
			$grp_id = $qryRes[$i]['gro_id'];
			$group_id_arr[$grp_id] = $grp_id;
			$patient_copay[$encounter_id] = $qryRes[$i]['patient_copay'];
			$patient_due_arr[$pat_id]['PATIENT_DUE'][$encounter_id] = $qryRes[$i]['patientDue'];
			$patient_due_arr[$pat_id]['INSURANCE_DUE'][$encounter_id] = $qryRes[$i]['insuranceDue'];
			$list_detail_id = $qryRes[$i]['charge_list_detail_id'];
			$patientDetailArr[$pat_id][$encounter_id][$list_detail_id] = $qryRes[$i];
			$arrEncounterIds[$encounter_id]=$encounter_id;
			$arrPatientEncIds[$pat_id][$encounter_id] = $encounter_id;
			$list_detail_id_arr[] = $list_detail_id;
			
			
			//--- GET UNIQUE INSURANCE COMPANY ID -------
			$primaryInsuranceCoId = $qryRes[$i]['primaryInsuranceCoId'];
			$secondaryInsuranceCoId = $qryRes[$i]['secondaryInsuranceCoId'];
			$tertiaryInsuranceCoId = $qryRes[$i]['tertiaryInsuranceCoId'];
			$ins_id_arr[$pat_id][$primaryInsuranceCoId] = $qryRes[$i]['primaryInsuranceCoId'];
			$ins_id_arr[$pat_id][$secondaryInsuranceCoId] = $qryRes[$i]['secondaryInsuranceCoId'];
			$ins_id_arr[$pat_id][$tertiaryInsuranceCoId] = $qryRes[$i]['tertiaryInsuranceCoId'];
			
			$arrPatsAllIns[$primaryInsuranceCoId] = $primaryInsuranceCoId;
			$arrPatsAllIns[$secondaryInsuranceCoId] = $secondaryInsuranceCoId;
			$arrPatsAllIns[$tertiaryInsuranceCoId] = $tertiaryInsuranceCoId;
		}
		
		$strEncounterIds = join(',', $arrEncounterIds);
		
		$group_id_str = implode(',',$group_id_arr);
		$arrGroupDet=array();
		//--- GET GROUP DETAILS --------
		$insGroupQryRes = imw_query("select gro_id, name ,group_Address1, group_Address2, group_Zip,group_Federal_EIN,group_City,group_State ,group_Telephone from groups_new where gro_id IN ($group_id_str)");
		$groupQryRes = array();
		while($row = imw_fetch_array($insGroupQryRes)) {			
			$groupQryRes[] = $row;	
		}				
		for($i=0; $i<count($groupQryRes); $i++){
			$grpId = $groupQryRes[$i]['gro_id'];
			$groupQryRes[$i]['group_Telephone'] = core_phone_format($groupQryRes[0]['group_Telephone']);
			$arrGroupDet[$grpId] = $groupQryRes[$i];
		}

		//--- GET INSURANCE COMPANY NAME -------
		$ins_id_str = join(',',$arrPatsAllIns);
		$insQryRs = imw_query("select id,name,in_house_code from insurance_companies where id in($ins_id_str)");
		$insQryRes=array();
		$insComName = array();
		while($row = imw_fetch_array($insQryRs)) {			
			$insQryRes[] = $row;	
		}
		for($i=0;$i<count($insQryRes);$i++){
			$id = $insQryRes[$i]['id'];
			$in_house_code = $insQryRes[$i]['in_house_code'];
			if(empty($in_house_code)){
				$in_house_code = $insQryRes[$i]['name'];
			}
			$insComName[$id] = $in_house_code;
		}

		//--- GET WRITE OFF AMOUNT --------
		$qryEc = imw_query("select write_off_amount,encounter_id from paymentswriteoff where encounter_id in($strEncounterIds)and delStatus != '1' and write_off_amount > '0'");
		$writeOffArr = array();
		$writeOffQryRes = array();
		while($row = imw_fetch_array($qryEc)) {			
			$writeOffQryRes[] = $row;	
		}
		for($i=0;$i<count($writeOffQryRes);$i++){
			$write_off_amount = $writeOffQryRes[$i]['write_off_amount'];
			$encounter_id = $writeOffQryRes[$i]['encounter_id'];
			$patientDetailArr[$patientId]['write_off'][] = $write_off_amount;
			$writeOffArr[$encounter_id][] = $write_off_amount;
		}
		
		//--GET COPAY AMOUNT ------------
		
		
		//--- GET PAID AMOUNT ----------
		$list_detail_id_str = join(',',$list_detail_id_arr);
		$pdAmt = imw_query("select patient_chargesheet_payment_info.encounter_id,
				patient_charges_detail_payment_info.paidForProc, patient_charges_detail_payment_info.paidBy,
				patient_charges_detail_payment_info.overPayment, patient_chargesheet_payment_info.insProviderId,
				patient_chargesheet_payment_info.paymentClaims
				from patient_charges_detail_payment_info  join patient_chargesheet_payment_info on
				patient_chargesheet_payment_info.payment_id = patient_charges_detail_payment_info.payment_id
				where encounter_id IN($strEncounterIds) and deletePayment != '1'");
		$paymentArr = array();
		$paymentQryRes = array();
		while($row = imw_fetch_array($pdAmt)) {			
			$paymentQryRes[] = $row;	
		}
		for($i=0;$i<count($paymentQryRes);$i++){
			$encounter_id = $paymentQryRes[$i]['encounter_id'];
			$insProviderId = $paymentQryRes[$i]['insProviderId'];
			$paidForProc = preg_replace('/,/','',$paymentQryRes[$i]['paidForProc']);
			$paidForProc += preg_replace('/,/','',$paymentQryRes[$i]['overPayment']);
			$paidBy = $paymentQryRes[$i]['paidBy'];
			
			//--- ENCOUNTER NEGATIVE PAYMENTS ---
			if($paymentQryRes[$i]['paymentClaims'] == 'Negative Payment'){
				$paidForProc = '-'.$paidForProc;
			}
			
			$paymentArr['ENCOUNTER_ID'][$detailId][] = $paidForProc;
			if($paidBy == 'Insurance'){
				$paymentArr[$encounter_id]['insurance_paid'][$insProviderId][] = $paidForProc;
			}
			else{
				$paymentArr[$encounter_id]['patient_paid'][] = $paidForProc;
			}
		}
		
		//--- GET DEDUCT AMOUNT -----------
		$dedAmt = imw_query("select payment_deductible.deduct_amount,patient_charge_list.encounter_id
				from payment_deductible join patient_charge_list_details on 
				patient_charge_list_details.charge_list_detail_id = payment_deductible.charge_list_detail_id
				join patient_charge_list on patient_charge_list.charge_list_id = patient_charge_list_details.charge_list_id
				where payment_deductible.charge_list_detail_id in ($list_detail_id_str) 
				and payment_deductible.delete_deduct != '1' and payment_deductible.deduct_amount > '0'");
		$deductArr = array();
		$deductQryRes = array();
		while($row = imw_fetch_array($dedAmt)) {			
			$paymentQryRes[] = $row;	
		}
		for($i=0;$i<count($paymentQryRes);$i++){
			$deduct_amount = preg_replace('/,/','',$paymentQryRes[$i]['deduct_amount']);
			$encounter_id = $paymentQryRes[$i]['encounter_id'];
			$deductArr[$encounter_id][] = $deduct_amount;
		}
		
		$date_dos = "( DOS ) From $sDate To $eDate";

		if(empty($eDate)){
			$date_dos = "( DOS ) No Date Selected";
		}


$pageHeader='';
$pageHeader= <<<DATA
<table class="rpt rpt_table rpt_table-bordered rpt_padding"  >
   <tr class="rpt_headers">
       <td class="rptbx1" style="width:350px;">Patient Report ($Process)</td>	
		<td class="rptbx2" style="width:350px; text-align:center">$date_dos</td>			
		<td class="rptbx3" style="width:350px; text-align:right">Created by $opInitial on $curDate&nbsp;</td>
	</tr>
</table>
DATA;
$pdfPageHeader = $pageHeader;
if(strtolower($Process)=='detail'){		
	require_once(dirname(__FILE__).'/patientresultdetail.php');
}else{
	// SUMMARY	
	$arrPatIds= array_keys($arrPatientEncIds);
	
	for($pt=0; $pt<count($arrPatIds); $pt++){	
		$write_off=0;
		$GrandTotalprocCharges=0;
		$patId=$arrPatIds[$pt];
		$arrEncs=array_values($arrPatientEncIds[$patId]);
		$arrAllPriInsComp=$arrAllSecInsComp=$arrAllTriInsComp=array();
		
		for($i=0;$i<count($arrEncs);$i++){
			$encounter_id = $arrEncs[$i];	
			if(count($patientDetailArr[$patId][$encounter_id])>0){
				$detail_arr = $patientDetailArr[$patId][$encounter_id];
				$detailsIdArr = array_keys($detail_arr);
			}	

			for($d=0;$d<count($detailsIdArr);$d++){
				$detailId = $detailsIdArr[$d];
				if($patId != $oldPatId){
					$patient_name_arr = array();
					$patient_name_arr["LAST_NAME"] = $detail_arr[$detailId]['lname'];
					$patient_name_arr["FIRST_NAME"] = $detail_arr[$detailId]['fname'];
					$patient_name_arr["MIDDLE_NAME"] = $detail_arr[$detailId]['mname'];
					$patient_name = changeNameFormat($patient_name_arr);
		
					$DOB = $detail_arr[$detailId]['DOB'];
					$facilityPracCode = $detail_arr[$detailId]['facilityPracCode'];
					$street = $detail_arr[$detailId]['street'];
					$street2 = $detail_arr[$detailId]['street2'];
					$cityAddress = $detail_arr[$detailId]['city'].', ';
					$cityAddress .= $detail_arr[$detailId]['state'].' ';
					$cityAddress .= $detail_arr[$detailId]['postal_code'];
					$cityAddress = ucfirst(trim($cityAddress));
					if($cityAddress[0] == ','){
						$cityAddress = substr($cityAddress,1);
					}
					$oldPatId=$patId;
				}
				$primaryInsuranceCoId = $detail_arr[$detailId]['primaryInsuranceCoId'];
				$secondaryInsuranceCoId = $detail_arr[$detailId]['secondaryInsuranceCoId'];
				$tertiaryInsuranceCoId = $detail_arr[$detailId]['tertiaryInsuranceCoId'];

				if($primaryInsuranceCoId>0){
					$arrAllPriInsComp[$insComName[$primaryInsuranceCoId]]=$insComName[$primaryInsuranceCoId];
				}else if($secondaryInsuranceCoId>0){
					$arrAllSecInsComp[$insComName[$secondaryInsuranceCoId]]=$insComName[$secondaryInsuranceCoId];
				}else if($tertiaryInsuranceCoId>0){
					$arrAllTriInsComp[$insComName[$tertiaryInsuranceCoId]]=$insComName[$tertiaryInsuranceCoId];
				}

				
				$grpId = $detail_arr[$detailId]['gro_id'];

				$charge_list_detail_id = $detail_arr[$detailId]['charge_list_detail_id'];
			
				$GrandTotalCredit=0;
				//--- GET ALL ADJUSTMENT AMOUNT ---
				$adjQry = imw_query("select id,patient_id,encounter_id, charge_list_detail_id,ins_id,
					charge_list_id,payment_by,payment_method,check_number,cc_type,cc_number, 
					replace(payment_amount,',','') as  payment_amount
					from account_payments where charge_list_detail_id in ($charge_list_detail_id) and del_status = '0'");
				$adjQryRes = array();
				while($row = imw_fetch_array($adjQry)) {			
					$adjQryRes[] = $row;	
				}
				$creditProcAmount = 0;
				for($ai=0;$ai<count($adjQryRes);$ai++){
					$creditProcAmount += $adjQryRes[$ai]['payment_amount'];
				}				
				
				$GrandTotalCredit += preg_replace('/,/','',$creditProcAmount);
				$write_off += preg_replace('/,/','',$detail_arr[$detailId]['write_off']);

				$GrandTotalprocCharges += preg_replace('/,/','',$detail_arr[$detailId]['balForProc']);
				
			}

			//--- GET INSURANCE PAID AMOUNT --------
			$insData = NULL;
			if(count($paymentArr[$encounter_id]['insurance_paid'])>0){
				$paidArr = $paymentArr[$encounter_id]['insurance_paid'];
				$insPaidArr = array_keys($paidArr);
				for($in=0;$in<count($insPaidArr);$in++){
					$ins_id = $insPaidArr[$in];
					$paid_amt = array_sum($paidArr[$ins_id]);
	
					$insCompName = $insComName[$ins_id];
					$insPaidArr12[$ins_id][] = $paid_amt;
				}
			}

			$patientPaid = '';
			if(count($paymentArr[$encounter_id]['patient_paid'])>0){
				$patientPaid = array_sum($paymentArr[$encounter_id]['patient_paid']);
			}
	
			$totalPatPaid+= preg_replace('/,/','',$patientPaid);
			
			if(count($writeOffArr[$encounter_id])>0){
				$write_off += array_sum($writeOffArr[$encounter_id]);
			}

		}

		
		$patientDue = $insuranceDue = $priPaid = $secPaid = $terPaid =0;
		//-- GET PATIENT AND INSURANCE DUE AMOUNT --
		$patientDue = array_sum($patient_due_arr[$patId]['PATIENT_DUE']);
		$insuranceDue = array_sum($patient_due_arr[$patId]['INSURANCE_DUE']);
		
		//--- NUMBER FORMAT -------
		$patientDue = $CLSReports->numberFormat($patientDue,2);
		$insuranceDue = $CLSReports->numberFormat($insuranceDue,2);
		
		if(count($insPaidArr12[$primaryInsuranceCoId])>0){
			$priPaid = array_sum($insPaidArr12[$primaryInsuranceCoId]);
		}
		if(count($insPaidArr12[$secondaryInsuranceCoId])>0){
			$secPaid = array_sum($insPaidArr12[$secondaryInsuranceCoId]);
		}
		if(count($insPaidArr12[$tertiaryInsuranceCoId])>0){
			$terPaid = array_sum($insPaidArr12[$tertiaryInsuranceCoId]);
		}
		$priPaid = $CLSReports->numberFormat($priPaid,2);
		$secPaid = $CLSReports->numberFormat($secPaid,2);
		$terPaid = $CLSReports->numberFormat($terPaid,2);
		$totalPatPaid = $CLSReports->numberFormat($totalPatPaid,2);
		$total_write_off = $CLSReports->numberFormat($write_off,2);
		$GrandTotalCredit = $CLSReports->numberFormat($GrandTotalCredit,2);
		$GrandTotalprocCharges = $CLSReports->numberFormat($GrandTotalprocCharges,2);
		
		if(empty($eDate)){
			$date_dos = NULL;
		}
		if(empty($street2) == true){
			$street2 = $cityAddress;
			$cityAddress = '&nbsp;';
		}
		
		$grpName=$arrGroupDet[$grpId]['name'];
		$grpPhone=$arrGroupDet[$grpId]['group_Telephone'];
		$grpAddress=$arrGroupDet[$grpId]['group_Address1'];
		$grpCity=$arrGroupDet[$grpId]['group_City'];
		$grpState=$arrGroupDet[$grpId]['group_State'];
		$grpZip=$arrGroupDet[$grpId]['group_Zip'];
		$grpEIN=$arrGroupDet[$grpId]['group_Federal_EIN'];

		$allPriPaidInsComp=implode(', ', $arrAllPriInsComp);
		$allSecPaidInsComp=implode(', ', $arrAllSecInsComp);
		$allTriPaidInsComp=implode(', ', $arrAllTriInsComp);
		
		
		if($pt>0){ $pageHeader=''; }
		
		//--- CREATE VARIABLE FOR HTML FILE DATA -------
		$page_data_pdf.= <<<DATA
					<table class="rpt rpt_table rpt_table-bordered">
						<tr>
							<td align="left" class="text_b_w" style="width:200px">Group:</td>
							<td align="left" class="text_b_w"  style="width:585px">$grpName</td>
							<td align="left" class="text_b_w"  style="width:100px">Phone:</td>
							<td align="left" class="text_b_w"  style="width:150px">$grpPhone</td>
						</tr>
						<tr><td colspan="4" height="1px"></td></tr>
						<tr>
							<td align="left" class="text_b_w">Address:</td>
							<td style="text-align:left"; class="text_b_w">
								$grpAddress, $grpCity, $grpState $grpZip
							</td>
							<td align="left" class="text_b_w">Tax:</td>
							<td align="left" class="text_b_w">$grpEIN</td>
						</tr>
					</table>				
					<table class="rpt rpt_table rpt_table-bordered">
						<tr style="height:25px">
							<td style="text-align:left; width:531px;" class="text_b_w">Patient Name: $patient_name - $patId</td>
							<td style="text-align:left; width:531px;" class="text_b_w">Facility:&nbsp;$facilityPracCode</td>
						</tr>
					</table>
					<table class="rpt rpt_table rpt_table-bordered" >
					<tr>
						<td align="right" class="text_b" style="width:50px">Name : </td>
						<td class="text_10" style="width:200px">$patient_name</td>
						<td class="text_b" style="width:100px; text-align:right">DOB : </td>
						<td class="text_10" style="width:200px">$DOB</td>
						<td class="text_b" style="width:100px">Payments</td>
						<td class="text_b" style="width:100px; text-align:right">A/C : </td>
						<td class="text_10" style="text-align:right; width:150px">$patId</td>
					</tr>
					<tr>
						<td align="right" class="text_b">Address : </td>
						<td align="left" class="text_10">$street</td>
						<td align="right" class="text_b">Primary Paid Ins. : </td>
						<td align="left" class="text_10">$allPriPaidInsComp</td>
						<td align="left" class="text_10">$priPaid</td>
						<td align="right" class="text_b">Ins Due : </td>
						<td align="right" class="text_10">$insuranceDue</td>
					</tr>
					<tr>
						<td align="right" class="text_b"></td>
						<td align="left" class="text_10">$street2</td>
						<td align="right" class="text_b">Secondary Paid Ins. : </td>
						<td align="left" class="text_10">$allSecPaidInsComp</td>
						<td align="left" class="text_10">$secPaid</td>
						<td align="right" class="text_b">Patient Due : </td>
						<td align="right" class="text_10">$patientDue</td>
					</tr>
					<tr>
						<td align="right" class="text_b"></td>
						<td align="left" class="text_10">$cityAddress</td>
						<td align="right" class="text_b">Tertiary Paid Ins. : </td>
						<td align="left" class="text_10">$allTriPaidInsComp</td>
						<td align="left" class="text_10">$terPaid</td>
						<td align="right" class="text_b">Credit : </td>
						<td align="right" class="text_10">$GrandTotalCredit</td>
					</tr>
					<tr>
						<td align="right" colspan="3" class="text_b">Patient Paid : </td>
						<td align="right" class="text_10">&nbsp;</td>
						<td align="left" class="text_10">$totalPatPaid</td>
						<td align="right" class="text_b">Write Off : </td>
						<td align="right" class="text_10">$total_write_off</td>
					</tr>
					<tr>
						<td align="right" colspan="5" class="text_b">&nbsp;</td>
						<td align="right" class="text_b">Balance : </td>
						<td align="right" class="text_b">$GrandTotalprocCharges</td>
					</tr>
					<tr>
						<td colspan="7" bgcolor="#FFFFFF" height="5"></td>
					</tr>
				</table>
DATA;

		//--- CREATE VARIABLE FOR CSV FILE DATA -------
		$csv_page_data.= <<<DATA
				$pageHeader
				<table class="rpt rpt_table rpt_table-bordered" width="100%">
					<tr>
						<td align="left" class="text_b_w" width="200">Group:</td>
						<td align="left" class="text_b_w" width="550" >$grpName</td>
						<td align="left" width="100" class="text_b_w">Phone:</td>
						<td align="left" width="200" class="text_b_w">$grpPhone</td>
					</tr>
					<tr><td colspan="4" height="1px"></td></tr>
					<tr>
						<td align="left" class="text_b_w">Address:</td>
						<td style="text-align:left"; class="text_b_w">
							$grpAddress, $grpCity, $grpState $grpZip
						</td>
						<td align="left" class="text_b_w">Tax:</td>
						<td align="left" class="text_b_w">$grpEIN</td>
					</tr>
				</table>
			<table class="rpt rpt_table rpt_table-bordered" width="100%">
				<tr style="height:25px">
					<td style="text-align:left; width:450px;" class="text_b_w">Patient Name: $patient_name - $patId</td>
					<td style="text-align:left; width:auto;" class="text_b_w">Facility:&nbsp;$facilityPracCode</td>
				</tr>
			</table>								
			<table class="rpt rpt_table rpt_table-bordered" width="100%" bgcolor="#FFF3E8">
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right" class="text_b">Name : </td>
					<td width="178" class="text_10">$patient_name</td>
					<td style="text-align:right" class="text_b">DOB : </td>
					<td width="120" class="text_10">$DOB</td>
					<td style="text-align:center" class="text_b">Payments</td>
					<td style="text-align:right" class="text_b">A/C : </td>
					<td width="70" class="text_10" style="text-align:right">$patId&nbsp;</td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right" class="text_b">Address : </td>
					<td class="text_10">$street&nbsp;</td>
					<td style="text-align:right" class="text_b">Primary Paid Ins. : </td>
					<td class="text_10">$allPriPaidInsComp&nbsp;</td>
					<td style="text-align:right" class="text_10">$priPaid&nbsp;</td>
					<td style="text-align:right" class="text_b">Ins Due : </td>
					<td style="text-align:right" class="text_10">$insuranceDue&nbsp;</td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td class="text_b">&nbsp;</td>
					<td class="text_10">$street2&nbsp;</td>
					<td style="text-align:right" class="text_b">Secondary Paid Ins. : </td>
					<td class="text_10">$allSecPaidInsComp&nbsp;</td>
					<td style="text-align:right" class="text_10">$secPaid&nbsp;</td>
					<td style="text-align:right" class="text_b">Patient Due : </td>
					<td style="text-align:right" class="text_10">$patientDue&nbsp;</td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td class="text_b">&nbsp;</td>
					<td class="text_10">$cityAddress&nbsp;</td>
					<td style="text-align:right" class="text_b">Tertiary Paid Ins. : </td>
					<td class="text_10">$allTriPaidInsComp&nbsp;</td>
					<td style="text-align:right" class="text_10">$terPaid&nbsp;</td>
					<td style="text-align:right" class="text_b">Credit : </td>
					<td style="text-align:right" class="text_10">$GrandTotalCredit&nbsp;</td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td class="text_10">&nbsp;</td>
					<td class="text_10">&nbsp;</td>
					<td style="text-align:right" class="text_b">Patient Paid : </td>
					<td class="text_10">&nbsp;</td>
					<td style="text-align:right" class="text_10">$totalPatPaid&nbsp;</td>
					<td style="text-align:right" class="text_b">Write Off : </td>
					<td style="text-align:right" class="text_10">$total_write_off&nbsp;</td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td class="text_10">&nbsp;</td>
					<td class="text_10">&nbsp;</td>
					<td class="text_10">&nbsp;</td>
					<td class="text_10">&nbsp;</td>
					<td class="text_b">&nbsp;</td>
					<td style="text-align:right" class="text_b">Balance : </td>
					<td style="text-align:right" class="text_b">$GrandTotalprocCharges</td>
				</tr>
				<tr><td colspan="7" bgcolor="#FFFFFF" height="5"></td></tr>
			</table>
DATA;
	}
}

// PDF DATA
$pdfHTML='<style>'.file_get_contents('css/reports_pdf.css').'</style>';
$page_data.= <<<DATA
	<page backtop="5mm" backbottom="5mm">
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
		<page_header>	
			$pdfPageHeader	
		</page_header>	
			$page_data_pdf
	</page>
DATA;
	
	$strHTML = <<<DATA
		$pdfHTML
		$page_data
DATA;
	}
}
$HTMLCreated = 0;
if($print_file == true and $strHTML != ''){
	$file_location = write_html($strHTML);
	$HTMLCreated = 1;
	echo $csv_page_data;
} else{
	echo '<div class="text-center alert alert-info">No Record Found.</div>';
}
?>