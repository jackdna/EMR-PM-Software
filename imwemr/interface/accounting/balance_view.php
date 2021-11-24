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
require_once("acc_header.php");
if($_REQUEST['patient_id_sch']<>""){
	$patient_id=$_REQUEST['patient_id_sch'];
}else{
	$patient_id=$_SESSION['patient'];
}
$authUser=$_SESSION['authUser'];
$encounter_id = $_REQUEST['eId'];

$get_patient_name = imw_query("SELECT * FROM patient_data WHERE id='$patient_id'");
$get_patient_name_row = imw_fetch_array($get_patient_name);
$patientPhone = $get_patient_name_row['phone_home'];
$patient_fname = $get_patient_name_row['fname'];
$patient_lname = $get_patient_name_row['lname'];
$patient_mname = $get_patient_name_row['mname'];
$patient_name = $patient_lname. ", " .$patient_fname." ".$patient_mname;
	
$DOB = get_date_format($get_patient_name_row['DOB']);
$age = show_age($get_patient_name_row['DOB']);
$TOdate=date('Y-m-d');
$date = $TOdate;

//--------------------- GETTING PATIENT NAME ---------------------//
$getRespPartyDetailsStr = "SELECT * FROM resp_party WHERE patient_id='$patient_id'";
$getRespPartyDetailsQry = imw_query($getRespPartyDetailsStr);
$getRespPartyDetailsRows = imw_fetch_array($getRespPartyDetailsQry);
$respParytFname = $getRespPartyDetailsRows['fname'];
$respParytLname = $getRespPartyDetailsRows['lname'];
$respParytName = $respParytFname." ".$respParytLname;
$resPartyHomePh = $getRespPartyDetailsRows['home_ph'];
if(trim($respParytName) == ''){
	$respParytName=$patient_name;
	$resPartyHomePh = $patientPhone;
}

//--------------------- GETTING PATIENT NAME ---------------------//	

//--------------------- GETTING NEXT APPOINTMENT ---------------------//
$getAppointmentDetailsStr = "SELECT * FROM schedule_appointments 
							WHERE sa_patient_id = '$patient_id'
							AND sa_app_start_date >= '$TOdate'
							ORDER BY sa_app_time ASC";
$getAppointmentDetailsQry = imw_query($getAppointmentDetailsStr);
$rowsCount = imw_num_rows($getAppointmentDetailsQry);
if($rowsCount>0){
	$getAppointmentDetailsRow = imw_fetch_array($getAppointmentDetailsQry);
	
	$sa_app_starttime_exp=explode(":",$getAppointmentDetailsRow['sa_app_starttime']);
	$sa_app_starttime = $sa_app_starttime_exp[0].":".$sa_app_starttime_exp[1];
	
	$sa_app_endtime_exp=explode(":",$getAppointmentDetailsRow['sa_app_endtime']);
	$sa_app_endtime = $sa_app_endtime_exp[0].":".$sa_app_endtime_exp[1];
	
	$sa_app_time = $sa_app_starttime ." - ". $sa_app_endtime;
		
	//---------------------- APPOINTMENT DATE AND TIME ----------------------//
	$appointMentDate=get_date_format($getAppointmentDetailsRow['sa_app_start_date']);
	$appointMent=$appointMentDate.' '.$sa_app_time;
		
	$procedureid = $getAppointmentDetailsRow['procedureid'];
	$physicianId = $getAppointmentDetailsRow['sa_doctor_id'];
		
	//-------------------------	GETTING PROCEDURE WILL TO PERFORM  -------------------------//
		$getProcCodeStr = "SELECT proc FROM slot_procedures WHERE id = '$procedureid'";
		$getProcCodeQry = imw_query($getProcCodeStr);
		$getProcCodeRow = imw_fetch_array($getProcCodeQry);
		$cpt_prac_code = $getProcCodeRow['proc'];
		//$cpt_desc = $getProcCodeRow['cpt_desc'];
	//-------------------------	GETTING PROCEDURE WILL TO PERFORM  -------------------------//
		
	//------------------------- GETTING PHYCICIAN NAME -------------------------//
		$getPhysicianNameStr = "SELECT * FROM users WHERE id = '$physicianId' AND user_type = '1'";
		$getPhysicianNameQry = imw_query($getPhysicianNameStr);
		$getPhysicianNameRow = imw_fetch_array($getPhysicianNameQry);
		$fName = $getPhysicianNameRow['fname'];
		$lName = $getPhysicianNameRow['lname'];
		$mName = $getPhysicianNameRow['mname'];
		$physicianName = $fName." ".$mName." ".$lName;
	//------------------------- GETTING PHYCICIAN NAME -------------------------//
		
	//------------------------- GETTING FACILITY NAME -------------------------//
		$getfacilityNameStr = "SELECT * FROM facility WHERE id = '$physicianId'";
		$getfacilityNameQry = imw_query($getfacilityNameStr);
		$getfacilityNameRow = imw_fetch_array($getfacilityNameQry);
		$facilityName = $getfacilityNameRow['name'];
	//------------------------- GETTING FACILITY NAME -------------------------//
		
	//$Procedure = $cpt_prac_code." - ".$cpt_desc;
	$Procedure = $cpt_prac_code;

}else{
	$appointMent = '-';
	$Procedure = '-';
}
//--------------------- GETTING NEXT APPOINTMENT ---------------------//
?>
<div class="table-responsive" style="overflow-x:hidden;">
	<div class="row purple_bar">
		<div class="col-sm-4">Balance View</div>
		<div class="col-sm-5"><?php echo $patient_name." - ".$patient_id; ?></div>
		<div class="col-sm-3">Phone <?php getHashOrNo();?>: <?php echo core_phone_format($patientPhone); ?></div>
	</div>
	<?php
	$qry_pol=imw_query("select elem_arCycle from copay_policies");
	$row_pol=imw_fetch_array($qry_pol);
	$elem_arCycle=$row_pol['elem_arCycle'];
	
	if($encounter_id<>""){
		$enc_whr="and encounter_id=$encounter_id";
		$creditlist_whr="and a.enc_id=$encounter_id";
		$creditapply_whr="and b.crAppliedToEncId=$encounter_id";
	}
	$getPatientEncountersStr = "SELECT * FROM patient_charge_list WHERE del_status='0' and patient_id = '$patient_id' $enc_whr";
	$getPatientEncountersQry = imw_query($getPatientEncountersStr);
	while($getPatientEncountersRow = imw_fetch_array($getPatientEncountersQry)){
		$encounter_id = $getPatientEncountersRow['encounter_id'];
		$todayAmountApplied = $getPatientEncountersRow['creditAmount']; 
			$charge_list_id = $getPatientEncountersRow['charge_list_id'];
			$encounter_id = $getPatientEncountersRow['encounter_id'];
			$collectionAmount = $getPatientEncountersRow['collectionAmount'];
				$collectionAmountTotal = $collectionAmountTotal + $collectionAmount;
				
			$copay = $getPatientEncountersRow['copay'];
			$copayPaid = $getPatientEncountersRow['copayPaid'];
			$coPayNotRequired = $getPatientEncountersRow['coPayNotRequired'];
			if(($copay>0) && ($copayPaid!=1) && ($coPayNotRequired!=1)){
				$unPaidCoPay = $unPaidCoPay + $copay;
			}
			
			$patientDue = $getPatientEncountersRow['patientDue'];
			$insuranceDue = $getPatientEncountersRow['insuranceDue'];
			$amtPaid = $getPatientEncountersRow['amtPaid'];
			$date_of_service = $getPatientEncountersRow['date_of_service'];
			$creditAmount = $getPatientEncountersRow['creditAmount'];
			$totalBalance = $getPatientEncountersRow['totalBalance'];
		
			if($date_of_service == $TOdate){
				$todayPatientDue = $todayPatientDue + $patientDue;
				$todayInsuranceDue = $todayInsuranceDue + $insuranceDue;
				$todayCreditAmount = $todayCreditAmount + $creditAmount;
			}
		$totalpatientDue = $totalpatientDue + $patientDue;
		$totalInsuranceDue = $totalInsuranceDue + $insuranceDue;
		$totalAmtPaid = $totalAmtPaid + $amtPaid;
		$totalCreditAmount = $totalCreditAmount + $creditAmount;
		$totalBalanceAmount = $totalBalanceAmount + $totalBalance;
		$chargeListId[] = $getPatientEncountersRow['charge_list_id'];
	}
	
	$chargeListIds = implode(",",$chargeListId);
	//--- Get Balance Of Patient Of 0-30 Days --------
	$firstPatientMonthDue = getARBalMonthDue($chargeListIds,$patient_id,'pat_due','0',$elem_arCycle);
	//--- Get Balance Of Patient Of 30-60 Days --------
	$secondPatientMonthDue = getARBalMonthDue($chargeListIds,$patient_id,'pat_due',$elem_arCycle+1,$elem_arCycle*2);
	//--- Get Balance Of Patient Of 60-90 Days --------
	$thirdPatientMonthDue = getARBalMonthDue($chargeListIds,$patient_id,'pat_due',($elem_arCycle*2)+1,$elem_arCycle*3);
	//--- Get Balance Of Patient Of 90-120 Days --------
	$fourthPatientMonthDue = getARBalMonthDue($chargeListIds,$patient_id,'pat_due',($elem_arCycle*3)+1,$elem_arCycle*4);
	//--- Get Balance Of Patient Of 120+ Days --------
	$fifthPatientMonthDue = getARBalMonthDue($chargeListIds,$patient_id,'pat_due',$elem_arCycle*4);
	//--- Get Toatl Balance Of Patient --------
	$totalPatientAggingDue = $firstPatientMonthDue[0] + $secondPatientMonthDue[0];
	$totalPatientAggingDue += $thirdPatientMonthDue[0] + $fourthPatientMonthDue[0];
	$totalPatientAggingDue += $fifthPatientMonthDue[0]; 
	
	//--- Get Balance Of Insurance Of 0-30 Days --------
	$firstInsMonthDue = getARBalMonthDue($chargeListIds,$patient_id,'insuranceDue','0',$elem_arCycle);
	//--- Get Balance Of Insurance Of 30-60 Days --------
	$secondInsMonthDue = getARBalMonthDue($chargeListIds,$patient_id,'insuranceDue',$elem_arCycle+1,$elem_arCycle*2);
	//--- Get Balance Of Insurance Of 60-90 Days --------
	$thirdInsMonthDue = getARBalMonthDue($chargeListIds,$patient_id,'insuranceDue',($elem_arCycle*2)+1,$elem_arCycle*3);
	//--- Get Balance Of Insurance Of 90-120 Days --------
	$fourthInsMonthDue = getARBalMonthDue($chargeListIds,$patient_id,'insuranceDue',($elem_arCycle*3)+1,$elem_arCycle*4);
	//--- Get Balance Of Insurance Of 120+ Days --------
	$fifthInsMonthDue = getARBalMonthDue($chargeListIds,$patient_id,'insuranceDue',$elem_arCycle*4);
	//--- Get Total Balance Of Insurance --------
	$totalInsAggingDue = $firstInsMonthDue[0] + $secondInsMonthDue[0];
	$totalInsAggingDue += $thirdInsMonthDue[0] + $fourthInsMonthDue[0];
	$totalInsAggingDue += $fifthInsMonthDue[0]; 
	
	//--- Get Toatl Balance Of 0-30 Days --------
	$firstMonthTotalDue = $firstPatientMonthDue[0]+$firstInsMonthDue[0];
	//--- Get Toatl Balance  Of 30-60 Days --------
	$secondMonthTotalDue= $secondPatientMonthDue[0]+$secondInsMonthDue[0];
	//--- Get Toatl Balance  Of 60-90 Days --------
	$thirdMonthTotalDue= $thirdPatientMonthDue[0]+$thirdInsMonthDue[0];
	//--- Get Toatl Balance  Of 90-120 Days --------
	$fourthMonthTotalDue = $fourthPatientMonthDue[0]+$fourthInsMonthDue[0];
	//--- Get Toatl Balance  Of 120+ Days --------
	$fifthMonthTotalDue = $fifthPatientMonthDue[0]+$fifthInsMonthDue[0];
	//--- Get Total Balance --------
	$totalMonthAggingDue = $firstMonthTotalDue + $secondMonthTotalDue;
	$totalMonthAggingDue += $thirdMonthTotalDue + $fourthMonthTotalDue;
	$totalMonthAggingDue += $fifthMonthTotalDue;
	$totalTodays = $todayPatientDue + $todayInsuranceDue;
	$totalTotals = $totalpatientDue + $totalInsuranceDue;
	
	$totalpatientDue = numberformat($totalpatientDue, 2);
	$todayPatientDue = numberformat($todayPatientDue, 2);
	$totalInsuranceDue = numberformat($totalInsuranceDue, 2);
	$todayInsuranceDue = numberformat($todayInsuranceDue, 2);
	$totalTodays = numberformat($totalTodays, 2);
	$totalTotals = numberformat($totalTotals, 2);
	$totalCreditAmount = numberformat($totalCreditAmount, 2);
	$todayCreditAmount = numberformat($todayAmountApplied, 2);
	$totalBalanceAmount = numberformat($totalBalanceAmount, 2);
	$totalAmtPaid = numberformat($totalAmtPaid, 2);
	$todayAmtPaid = numberformat($todayAmtPaid, 2);
	$unPaidCoPay =  numberformat($unPaidCoPay, 2);
	
	$monthThisTotalDues = $monthThisPatDue + $monthThisInsDue;
	$month1TotalDues = $month1PatDue + $month1InsDue;
	$month2TotalDues = $month2PatDue + $month2InsDue;
	$month3TotalDues = $month3PatDue + $month3InsDue;
	$month4TotalDues = $month4PatDue + $month4InsDue;

	//-------------------- GETTING TODAY'S COLLECTION AMOUNT --------------------//
		$getPaimentdetailsStr = "SELECT * FROM 
								patient_charge_list
								WHERE del_status='0' and patient_id = '$patient_id' $enc_whr";
		$getPaimentdetailsQry = imw_query($getPaimentdetailsStr);
		while($getPaimentdetailsRow = imw_fetch_array($getPaimentdetailsQry)){
			$collectionAmount = $getPaimentdetailsRow['collectionAmount'];
			$collectionDate = $getPaimentdetailsRow['collectionDate'];
			if($collectionDate == $TOdate){
				$todayCollection = $collectionAmount;
			}
			$totalCollectionAmount = $totalCollectionAmount + $collectionAmount;
			$availableCreditAmount=$availableCreditAmount + $getPaimentdetailsRow['creditAmount'];
		}
		$availableCreditAmount=numberformat($availableCreditAmount,2);
		$totalCollectionAmount = numberformat($totalCollectionAmount, 2);
		$todayCollection = numberformat($todayCollection, 2);
	//-------------------- GETTING TODAY'S COLLECTION AMOUNT --------------------//

	?>
	<table class="table table-bordered table-condensed">
		<tr class='grythead'>
			<th colspan="6">Description</th>
			<th colspan="2">Today</th>
			<th>Total</th>
		</tr>
		<tr>
		  <th>Res. party Name:</th>
		  <td><?php echo $respParytName; ?></td>
		  <th>DOB:</th>
		  <td><?php if($DOB!='00-00-0000') echo $DOB; ?></td>
		  <th>Age:</th>
		  <td><?php echo $age; ?></td>
		  <th>Insurance:</th>
		  <td class="text-right"><?php echo $todayInsuranceDue; ?></td>
		  <th class="text-right"><?php echo numberformat($totalInsAggingDue,2); ?></th>
		</tr>
		<tr>
		  <th>Res. party Phone <?php getHashOrNo();?>:</th>
		  <td><?php echo core_phone_format($resPartyHomePh); ?></td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <th>Patient:</th>
		  <td class="text-right"><?php echo $todayPatientDue; ?></td>
		  <th class="text-right"><?php echo numberformat($totalPatientAggingDue,2); ?></th>
		</tr>
		<tr>
		  <th>Next Appointment:</th>
		  <td><?php echo $appointMent; ?></td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <th>Total:</th>
		  <td class="text-right"><?php echo $totalTodays; ?></td>
		  <th class="text-right"><?php echo numberformat($totalMonthAggingDue,2); ?></th>
		</tr>
		<tr>
		  <th>Procedure:</th>
		  <td><?php echo $Procedure; ?></td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <th>Credit:</th>
		  <td class="text-right"><?php echo $todayCreditAmount; ?></td>
		  <th class="text-right"><?php echo $totalCreditAmount; ?></th>
		</tr>
		<tr>
		  <th>Physician:</th>
		  <td colspan="2"><?php echo $physicianName;?></td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <th>Collection:</th>
		  <td class="text-right"><?php echo $todayCollection; ?></td>
		  <th class="text-right"><?php echo $totalCollectionAmount; ?></th>
		</tr>
	</table>
	<div class="col-lg-10 col-lg-offset-1">
		<table class="table table-bordered table-condensed">
			<tr class='grythead'>
				<th>Description</th>
				<th>Not Billed</th>
				<th><?php print '0 - '.$elem_arCycle.'' ?></th>
				<th><?php print ''.($elem_arCycle+1).' - '.($elem_arCycle*2).'' ?></th>
				<th><?php print ''.($elem_arCycle*2+1).' - '.($elem_arCycle*3).'' ?></th>
				<th><?php print ''.($elem_arCycle*3+1).' - '.($elem_arCycle*4).'' ?></th>
				<th><?php print ''.($elem_arCycle*4).' +' ?></th>
				<th>Total</th>
			</tr>
			<tr>
				<th>Patient Due</th>
				<td class="text-right"><?php echo '$0.00' ?></td>
				<td class="text-right"><?php echo numberformat($firstPatientMonthDue[0], 2); ?></td>
				<td class="text-right"><?php echo numberformat($secondPatientMonthDue[0], 2); ?></td>
				<td class="text-right"><?php echo numberformat($thirdPatientMonthDue[0], 2); ?></td>
				<td class="text-right"><?php echo numberformat($fourthPatientMonthDue[0], 2); ?></td>
				<td class="text-right"><?php echo numberformat($fifthPatientMonthDue[0], 2); ?></td>
				<th class="text-right"><?php echo numberformat($totalPatientAggingDue, 2); ?></th>
			</tr>
			<tr>
				<th>Insurance Due</th>
				<td class="text-right"><?php echo '$0.00' ?></td>
				<td class="text-right"><?php echo numberformat($firstInsMonthDue[0], 2); ?></td>
				<td class="text-right"><?php echo numberformat($secondInsMonthDue[0], 2); ?></td>
				<td class="text-right"><?php echo numberformat($thirdInsMonthDue[0], 2); ?></td>
				<td class="text-right"><?php echo numberformat($fourthInsMonthDue[0], 2); ?></td>
				<td class="text-right"><?php echo numberformat($fifthInsMonthDue[0], 2); ?></td>
				<th class="text-right"><?php echo numberformat($totalInsAggingDue, 2); ?></th>
			</tr>
			<tr>
				<th>Total Due</th>
				<td class="text-right"><?php echo '$0.00' ?></td>
				<td class="text-right"><?php echo numberformat($firstMonthTotalDue, 2); ?></td>
				<td class="text-right"><?php echo numberformat($secondMonthTotalDue, 2); ?></td>
				<td class="text-right"><?php echo numberformat($thirdMonthTotalDue, 2); ?></td>
				<td class="text-right"><?php echo numberformat($fourthMonthTotalDue, 2); ?></td>
				<td class="text-right"><?php echo numberformat($fifthMonthTotalDue, 2); ?></td>
				<th class="text-right"><?php echo numberformat($totalMonthAggingDue, 2); ?></th>
			</tr>
		</table>		
			
		<?php
		//----------------- GETTING LAST VISIT -----------------//
			$getLastVisitDetailsStr = "SELECT * FROM patient_charge_list
										WHERE del_status='0' and patient_id = '$patient_id' 
										$enc_whr
										ORDER BY date_of_service ASC";
			$getLastVisitDetailsQry = imw_query($getLastVisitDetailsStr);
			$overPayment=0;
			while($getLastVisitDetailsRow = imw_fetch_array($getLastVisitDetailsQry)){
				$overPayment+= $getLastVisitDetailsRow['overPayment'];
				$lastVisitDate = get_date_format($getLastVisitDetailsRow['date_of_service']);
				$ent_id_arr[] = $getLastVisitDetailsRow['encounter_id'];
			}	
		//----------------- GETTING LAST VISIT -----------------//
		
		
		//----------------- LAST PATIENT PAID INFRMATION -----------------//
			$ent_id = implode(',',$ent_id_arr);
			$paid_amt_arr=array();
			$qry = imw_query("select patient_chargesheet_payment_info.date_of_payment,
					patient_chargesheet_payment_info.encounter_id,patient_chargesheet_payment_info.paid_by,
					patient_charges_detail_payment_info.paidForProc,
					patient_charges_detail_payment_info.overPayment from 
					patient_chargesheet_payment_info join
					patient_charges_detail_payment_info on 
					patient_charges_detail_payment_info.payment_id = 
					patient_chargesheet_payment_info.payment_id
					where patient_charges_detail_payment_info.deletePayment != 1
					and patient_chargesheet_payment_info.encounter_id in ($ent_id)
					order by patient_chargesheet_payment_info.payment_id asc");
			while($row=imw_fetch_array($qry)){
				if($row['paid_by']=='Insurance'){
					$last_ins_dop=$row['date_of_payment'];
				}else{
					$last_pat_dop=$row['date_of_payment'];
				}
				if($_REQUEST['eId']>0){
					$paid_amt_arr[$row['paid_by']][$row['date_of_payment']][$row['encounter_id']][]=$row['paidForProc']+$row['overPayment'];
				}else{
					$paid_amt_arr[$row['paid_by']][$row['date_of_payment']][]=$row['paidForProc']+$row['overPayment'];
				}
			}
			if($_REQUEST['eId']>0){
				$paymentAmountPatLast = array_sum($paid_amt_arr['Patient'][$last_pat_dop][$_REQUEST['eId']]);
				$paymentAmountInsLast = array_sum($paid_amt_arr['Insurance'][$last_ins_dop][$_REQUEST['eId']]);
			}else{
				$paymentAmountPatLast = array_sum($paid_amt_arr['Patient'][$last_pat_dop]);
				$paymentAmountInsLast = array_sum($paid_amt_arr['Insurance'][$last_ins_dop]);
			}
			
			if($last_pat_dop){
				$patientPaidLastDate = get_date_format($last_pat_dop);
			}else{
				$patientPaidLastDate = '-';
			}
			
			if($last_ins_dop){	
				$insurancePaidLastDate = get_date_format($last_ins_dop);
			}else{
				$insurancePaidLastDate = '-';
			}
			
			$paymentAmountPatLast = number_format($paymentAmountPatLast, 2);
			$paymentAmountInsLast = number_format($paymentAmountInsLast, 2);
		//----------------- LAST INSURANCE PAID INFRMATION -----------------//
		
		$qry = imw_query("select * from patient_charge_list where del_status='0' and encounter_id = '$encounter_id'");
		$getLastPaymentDetailsRow = imw_fetch_array($qry);
		$copay = $getLastPaymentDetailsRow['copay'];
		$copayPaid = $getLastPaymentDetailsRow['copayPaid'];
		$coPayNotRequired = $getLastPaymentDetailsRow['coPayNotRequired'];
		
		$totalBal = $totalTotals + $totalCreditAmount;
		$totalBal = number_format($totalBal, 2);
																
		?>
		<table class="table table-bordered table-condensed">
			<tr>
				<th>Total Balance:</th>
				<td class="text-right"><?php echo $totalTotals; //$totalBalanceAmount; //$totalBal; ?></td>
				<th>Last Visit:</th>
				<td class="text-right"><?php echo $lastVisitDate; ?></td>
				<th>Last Patient Paid:</th>
				<td class="text-right"><?php echo $paymentAmountPatLast; ?></td>
			</tr>
			<tr>
				<th>Available Credit:</th>
				<td class="text-right"><?php echo $availableCreditAmount; ?></td>
				<th>Last Bill:</th>
				<td>&nbsp;</td>
				<th>Last Pt. Paid Date:</th>
				<td class="text-right"><?php echo $patientPaidLastDate; ?></td>
			</tr>
			<tr>
				<th>Over Payment:</th>
				<td class="text-right"><?php echo numberformat($overPayment, 2); ?></td>
				<th>Finance Amt:</th>
				<td class="text-right"><?php echo '$'.'0.00'; ?></td>
				<th>Last Ins. Paid:</th>
				<td class="text-right"><?php echo $paymentAmountInsLast; ?></td>
			</tr>
			<tr>
				<th>New Balance:</th>
				<td class="text-right"><?php echo $totalTotals; ?></td>
				<th>Budget Amt:</th>
				<td class="text-right"><?php echo '$'.'0.00'; ?></td>
				<th>Last Ins. Paid Date:</th>
				<td class="text-right"><?php echo $insurancePaidLastDate; ?></td>
			</tr>
			<tr>
				<th>Unpaid CoPay:</th>
				<td class="text-right"><?php echo $unPaidCoPay; ?></td>
				<th>Collection:</th>
				<td class="text-right"><?php echo numberformat($collectionAmountTotal, 2); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</div>	
</div>	
</body>
</html>