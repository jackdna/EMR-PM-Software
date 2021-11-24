<?php
$arrPatIds= array_keys($arrPatientEncIds);

$csv_page_data = $page_data ='';

for($pt=0; $pt<count($arrPatIds); $pt++){
	$insPaidArr12=array();
	$totalPatPaid=0;
	$total_write_off=0;
	$GrandTotalCharge = 0;
	$GrandTotalPayment = 0;
	$GrandTotalCredit=0;
	$GrandTotalprocCharges=0;
	$oldPatId=0;
	$arrAllPriInsComp=$arrAllSecInsComp=$arrAllTriInsComp=array();
	
	$data='';
	$patId=$arrPatIds[$pt];
	$arrEncs=array_values($arrPatientEncIds[$patId]);
	
	for($i=0;$i<count($arrEncs);$i++){
		$encounter_id = $arrEncs[$i];	
		if(count($patientDetailArr[$patId][$encounter_id])>0){
			$detail_arr = $patientDetailArr[$patId][$encounter_id];
			$detailsIdArr = array_keys($detail_arr);
		}	
		$write_off = 0;
		$patient_copay_amount = 0;
		$detail_data = NULL;		
		$SubTotalCharge = 0;
		$SubTotalPayment = 0;
		$SubTotalCredit = 0;
		$SubTotalprocCharges = 0;
		$deduct_amount = 0;
		$copay_cnt=0;
		for($d=0;$d<count($detailsIdArr);$d++){		
			$detailId = $detailsIdArr[$d];
			$date_of_service = $detail_arr[$detailId]['date_of_service'];
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
			
			
			//---  PHYSICIAN INITIAL --------
			$phyName = $detail_arr[$detailId]['userFname'][0];
			$phyName .= $detail_arr[$detailId]['userLname'][0];
			
			$phyName = strtoupper($phyName);
			$encounterId = $detail_arr[$detailId]['encounter_id'];
			$cpt4_code = $detail_arr[$detailId]['cpt4_code'];
			
			$units = $detail_arr[$detailId]['units'];
			$totalAmount = $detail_arr[$detailId]['totalAmount'];
			$SubTotalCharge += preg_replace('/,/','',$totalAmount);
			$GrandTotalCharge += preg_replace('/,/','',$totalAmount);
			$totalAmount = $CLSReports->numberFormat($totalAmount,2);
			$paidForProc = $detail_arr[$detailId]['paidForProc'];
			if($detail_arr[$detailId]['copayPaid']==1 && $copay_cnt==0){
				$paidForProc+= $detail_arr[$detailId]['patient_copay'];
				$copay_cnt=1;
			}
	
			$SubTotalPayment += preg_replace('/,/','',$paidForProc);
			$GrandTotalPayment += preg_replace('/,/','',$paidForProc);
			$paidForProc = $CLSReports->numberFormat($paidForProc,2);
			
			$creditProcAmount = $detail_arr[$detailId]['creditProcAmount'];
			$charge_list_detail_id = $detail_arr[$detailId]['charge_list_detail_id'];
			
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
			
			$SubTotalCredit += preg_replace('/,/','',$creditProcAmount);
			$GrandTotalCredit += preg_replace('/,/','',$creditProcAmount);
			$creditProcAmount = $CLSReports->numberFormat($creditProcAmount,2);
			
			$balForProc = $detail_arr[$detailId]['balForProc'];
			$SubTotalprocCharges += preg_replace('/,/','',$balForProc);
			$GrandTotalprocCharges += preg_replace('/,/','',$balForProc);
			$balForProc = $CLSReports->numberFormat($balForProc,2);
			
			$write_off += preg_replace('/,/','',$detail_arr[$detailId]['write_off']);
			if($d>0){
				$date_of_service = NULL;
				$encounterId = NULL;
				$phyName = NULL;
			}
			
			$detail_data .= <<<DATA
				<tr bgcolor="#FFFFFF">
					<td style="text-align:center" class="text_10">$date_of_service</td>
					<td style="text-align:center" class="text_10">$encounterId</td>
					<td style="text-align:center" class="text_10">$phyName</td>
					<td style="text-align:center; width:150px" class="text_10">$cpt4_code</td>
					<td style="text-align:center" class="text_10">$units</td>
					<td style="text-align:right" class="text_10">$totalAmount</td>
					<td style="text-align:right" class="text_10">$paidForProc</td>
					<td style="text-align:right" class="text_10">$creditProcAmount</td>
					<td style="text-align:right" class="text_10">$balForProc</td>
				</tr>
DATA;
		}
		
		if(count($patient_copay[$encounter_id])>0){
			$patient_copay_amount = $patient_copay[$encounter_id]; 
		}
		if(count($writeOffArr[$encounter_id])>0){
			$write_off += array_sum($writeOffArr[$encounter_id]);
		}
		
		$total_write_off += preg_replace('/,/','',$write_off);
		$write_off = $CLSReports->numberFormat($write_off,2);
		$patient_copay_amount = $CLSReports->numberFormat($patient_copay_amount,2);
		
		//--- GET DEDUCT AMOUNT -------
		$deduct_amount = 0;
		if(count($deductArr[$encounter_id])>0){
			$deduct_amount = array_sum($deductArr[$encounter_id]);
		}
		$deduct_amount = $CLSReports->numberFormat($deduct_amount,2);
		
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
				$paid_amt = $CLSReports->numberFormat($paid_amt,2);
				$insData .= <<<DATA
					<tr>
						<td colspan="5"></td>
						<td colspan="4" height="2px" bgcolor="#009933"></td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td style="text-align:right" colspan="5" class="text_10b">$insCompName - Paid :</td>
						<td style="text-align:left" colspan="4" class="text_10">$paid_amt</td>
					</tr>				
DATA;
			}
		}
		
		//--- GET ALL PAID AMOUNTS DETAILS -----
		$patientPaid = '';
		if(count($paymentArr[$encounter_id]['patient_paid'])>0){
			$patientPaid = array_sum($paymentArr[$encounter_id]['patient_paid']);
		}
		$copayPaid ='';

		$totalPatPaid += preg_replace('/,/','',$patientPaid);
		$patientPaid = $CLSReports->numberFormat($patientPaid,2);
		$SubTotalCharge = $CLSReports->numberFormat($SubTotalCharge,2);
		$SubTotalPayment = $CLSReports->numberFormat($SubTotalPayment,2);
		$SubTotalCredit = $CLSReports->numberFormat($SubTotalCredit,2);
		$SubTotalprocCharges = $CLSReports->numberFormat($SubTotalprocCharges,2);
		$data .= <<<DATA
			<tr>
				<td style="text-align:center; width:80px" class="text_b_w">DOS</td>
				<td style="text-align:center; width:80px" class="text_b_w">E.ID</td>
				<td style="text-align:center; width:125px" class="text_b_w">Phy. Name</td>
				<td style="text-align:center; width:150px" class="text_b_w">CPT</td>
				<td style="text-align:center; width:70px" class="text_b_w">Unit</td>
				<td style="text-align:right; width:112px" class="text_b_w">T.Charges</td>
				<td style="text-align:right; width:112px" class="text_b_w">Payment</td>
				<td style="text-align:right; width:112px" class="text_b_w">Credit</td>
				<td style="text-align:right; width:112px" class="text_b_w">Amount</td>
			</tr>
			$detail_data
			<tr height="7" bgcolor="#FFFFFF"><td colspan="9"></td></tr>
			<tr>
				<td colspan="9" height="2px" bgcolor="#009933"></td>
			</tr>
			<tr bgcolor="#FFFFFF">
				<td colspan="5" style="text-align:right" class="text_10b">Sub Total :</td>
				<td style="text-align:right" class="text_10b">$SubTotalCharge </td>
				<td style="text-align:right" class="text_10b">$SubTotalPayment </td>
				<td style="text-align:right" class="text_10b">$SubTotalCredit </td>
				<td style="text-align:right" class="text_10b">$SubTotalprocCharges </td>
			</tr>
			<tr>
				<td colspan="9" height="2px" bgcolor="#009933"></td>
			</tr>
			<tr bgcolor="#FFFFFF">
				<td style="text-align:right" colspan="5" class="text_10b">Write-off :</td>
				<td style="text-align:left" colspan="4" class="text_10">$write_off</td>
			</tr>
			<tr>
				<td colspan="5"></td>
				<td colspan="4" height="2px" bgcolor="#009933"></td>
			</tr>
			<tr bgcolor="#FFFFFF">
				<td style="text-align:right" colspan="5" class="text_10b">Deductible :</td>
				<td style="text-align:left" colspan="4" class="text_10">$deduct_amount</td>
			</tr>
			<tr>
				<td colspan="5"></td>
				<td colspan="4" height="2px" bgcolor="#009933"></td>
			</tr>
			<tr bgcolor="#FFFFFF">
				<td style="text-align:right" colspan="5" class="text_10b">Copay :</td>
				<td style="text-align:left" colspan="4" class="text_10">$patient_copay_amount</td>
			</tr>
			<tr>
				<td colspan="5"></td>
				<td colspan="4" height="2px" bgcolor="#009933"></td>
			</tr>
			<tr bgcolor="#FFFFFF">
				<td style="text-align:right" colspan="5" class="text_10b">Patient Paid :</td>
				<td style="text-align:left" colspan="4" class="text_10">$patientPaid </td>
			</tr>
			$insData
			<tr>
				<td colspan="5"></td>
				<td colspan="4" height="2px" bgcolor="#009933"></td>
			</tr>
			<tr bgcolor="#FFFFFF"><td colspan="9">&nbsp;</td></tr>
DATA;
	}

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
	$total_write_off = $CLSReports->numberFormat($total_write_off,2);

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
	
	if($pt>0){	$pageHeader=''; } 

	// TOTAL OF PATIENT
	$GrandTotalCharge = $CLSReports->numberFormat($GrandTotalCharge,2);
	$GrandTotalPayment = $CLSReports->numberFormat($GrandTotalPayment,2);
	$GrandTotalCredit = $CLSReports->numberFormat($GrandTotalCredit,2);
	$GrandTotalprocCharges = $CLSReports->numberFormat($GrandTotalprocCharges,2);
	
	$dataTotal=<<<DATA
		<tr>
			<td colspan="5"></td>
			<td colspan="4" height="2px" bgcolor="#009933"></td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td colspan="5" style="text-align:right" class="text_10b">Total :</td>
			<td style="text-align:right" class="text_10b">$GrandTotalCharge </td>
			<td style="text-align:right" class="text_10b">$GrandTotalPayment </td>
			<td style="text-align:right" class="text_10b">$GrandTotalCredit </td>
			<td style="text-align:right" class="text_10b">$GrandTotalprocCharges </td>
		</tr>
		<tr>
			<td colspan="5"></td>
			<td colspan="4" height="2px" bgcolor="#009933"></td>
		</tr>
DATA;
	// ------------	

	//--- CREATE VARIABLE FOR HTML FILE DATA -------
	$page_data.= <<<DATA
		<page backtop="17mm" backbottom="5mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>		
				$pdfPageHeader
				<table class="rpt rpt_table rpt_table-bordered" >
				<tr>
					<td align="left" class="text_b_w" style="width:200px">Group:</td>
					<td align="left" class="text_b_w"  style="width:585px">$grpName</td>
					<td align="left" class="text_b_w"  style="width:100px">Phone:</td>
					<td align="left" class="text_b_w"  style="width:150px">$grpPhone</td>
				</tr>					
					<tr><td colspan="4" height="2px"></td></tr>
					<tr>
						<td align="left" class="text_b_w">Address:</td>
						<td style="text-align:left"; class="text_b_w">
							$grpAddress, $grpCity, $grpState $grpZip
						</td>
						<td align="left" class="text_b_w">Tax:</td>
						<td align="left" class="text_b_w">$grpEIN</td>
					</tr>
				</table>				
			</page_header>
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
			<table class="rpt rpt_table rpt_table-bordered" >
				$data
			</table>
		</page>
DATA;

	//--- CREATE VARIABLE FOR CSV FILE DATA -------
	$csv_page_data.= <<<DATA
		$pageHeader
		<table class="rpt rpt_table rpt_table-bordered" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td align="left" class="text_b_w" width="100">Group:</td>
				<td align="left" class="text_b_w" width="472" >$grpName</td>
				<td align="left" width="50" class="text_b_w">Phone:</td>
				<td align="left" width="120" class="text_b_w">$grpPhone</td>
			</tr>
			<tr><td colspan="4" height="2px"></td></tr>
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
				<td style="text-align:center" class="text_b">Due Amounts</td>
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
		<table class="rpt rpt_table rpt_table-bordered" cellpadding="0" cellspacing="0" bgcolor="#FFF3E8">
			$data
			$dataTotal
		</table>
DATA;
	

}
//--- GET TOTAL AMOUNT -------
?>