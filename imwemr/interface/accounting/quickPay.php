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
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php"); 
$patient_id = $_SESSION['patient'];
$operatorId = $_SESSION['authId'];
$operatorName = $_SESSION['authUser'];
$transactionDate = date('Y-m-d');
$operator_id = $_SESSION['authId'];
$entered_date = date('Y-m-d H:i:s');
//$facility_id=$_SESSION['login_facility'];
$stop_clm_status=1;

$getPatientNameStr = "SELECT * FROM patient_data WHERE id='$patient_id'";
$get_patient_name = imw_query($getPatientNameStr);
	$get_patient_name_row = imw_fetch_array($get_patient_name);
	$patient_fname = $get_patient_name_row['fname'];
	$patient_lname = $get_patient_name_row['lname'];
	$patient_name = $patient_lname. ", " .$patient_fname;
	$ss = $get_patient_name_row['ss'];
	$DOB = $get_patient_name_row['DOB'];
	$today = getdate();
	$today_year = $today['year'];
	$today_mon = $today['mon'];
	$today_day = $today['mday'];
	$date = $today_year. "-" .$today_mon. "-" .$today_day;
		list($year, $month, $day) = explode('-',$DOB);
		$DOB = $month."-".$day."-".$year;
		$dob_year = $year;
		$age = $today_year-$dob_year;
if($today_day<=9){
	$today_day='0'.$today_day;
}
if($today_mon<=9){
	$today_mon='0'.$today_mon;
}
$todate = $today_mon."-".$today_day."-".$today_year;
$postedDate = $today_year."-".$today_mon."-".$today_day;


//-------------------------------- E&M PROCEDURES ARRAY --------------------------------//
$eAndMCptArray = array(99201, 99202, 99203, 99204, 99205, 99211, 99212, 99213, 99214, 99215, 99242, 99243, 99244, 99245);
//-------------------------------- E&M PROCEDURES ARRAY --------------------------------//

$encounter_id = $_REQUEST['eId'];
set_payment_trans($encounter_id,'',$stop_clm_status);
$insProvidersNameArr=array();
$all_prov_ids=array();
$getCopayStr = "SELECT * FROM patient_charge_list
				WHERE del_status='0' and encounter_id='$encounter_id'";
$getCopayQry = imw_query($getCopayStr);
$getCopayRow = imw_fetch_array($getCopayQry);
	$charge_list_id = $getCopayRow['charge_list_id'];
	//-------------------- INSURANCE PROVIDERS --------------------//
		$primaryInsProviderId = $getCopayRow['primaryInsuranceCoId'];
		$secondaryInsProviderId = $getCopayRow['secondaryInsuranceCoId'];
		$tertiaryInsProviderId = $getCopayRow['tertiaryInsuranceCoId'];
			//---------------------- GETTING COMPANIES NAME ----------------------//
				$getPrimaryInsCoNameStr = "SELECT * FROM insurance_companies WHERE id='$primaryInsProviderId'";
				$getPrimaryInsCoNameQry = imw_query($getPrimaryInsCoNameStr);
				$getPrimaryInsCoNameRow = imw_fetch_array($getPrimaryInsCoNameQry);
					$primaryInsCoName = $getPrimaryInsCoNameRow['in_house_code'];
					if($primaryInsCoName==""){
						$primaryInsCoName = $getPrimaryInsCoNameRow['name'];
					}
	
				$getSecondaryInsCoNameStr = "SELECT * FROM insurance_companies WHERE id='$secondaryInsProviderId'";
				$getSecondaryInsCoNameQry = imw_query($getSecondaryInsCoNameStr);
				$getSecondaryInsCoNameRow = imw_fetch_array($getSecondaryInsCoNameQry);
					$secondaryInsCoName = $getSecondaryInsCoNameRow['in_house_code'];
					if($secondaryInsCoName==""){
						$secondaryInsCoName = $getSecondaryInsCoNameRow['name'];
					}
	
				$getTertiaryInsCoNameStr = "SELECT * FROM insurance_companies WHERE id='$tertiaryInsProviderId'";
				$getTertiaryInsCoNameQry = imw_query($getTertiaryInsCoNameStr);
				$getTertiaryInsCoNameRow = imw_fetch_array($getTertiaryInsCoNameQry);
					$tertiaryInsCoName = $getTertiaryInsCoNameRow['in_house_code'];
					if($tertiaryInsCoName==""){
						$tertiaryInsCoName = $getTertiaryInsCoNameRow['name'];
					}
			//---------------------- GETTING COMPANIES NAME ----------------------//		
			
				$insProvidersNameArr[] = $primaryInsCoName;
				$insProvidersNameArr[] = $secondaryInsCoName;
				$insProvidersNameArr[] = $tertiaryInsCoName;
				
				$all_prov_ids[] = $getCopayRow['primaryProviderId'];
				$all_prov_ids[] = $getCopayRow['secondaryProviderId'];
				$all_prov_ids[] = $getCopayRow['tertiaryProviderId'];
	//-------------------- INSURANCE PROVIDERS --------------------//
	
		
	
		$getProcCountStr = "SELECT * FROM patient_charge_list_details WHERE del_status='0' and charge_list_id='$charge_list_id'";
		$getProcCountQry = imw_query($getProcCountStr);
		$getProcCountRows = imw_num_rows($getProcCountQry);
			$getProcCountRow = imw_fetch_array($getProcCountStr);
			$totalProcAmount = $getProcCountRow['totalAmount'];
			

	$copay = number_format($getCopayRow['copay'], 2);
	$pri_copay = number_format($getCopayRow['pri_copay'],2);
	$sec_copay = number_format($getCopayRow['sec_copay'],2);
	$coPayNotRequired = $getCopayRow['coPayNotRequired'];
	$coPayNotRequired2 = $getCopayRow['coPayNotRequired2'];
	$coPayWriteOff = $getCopayRow['coPayWriteOff'];
	$copayPaid = $getCopayRow['copayPaid'];
	$referactionPaid = $getCopayRow['referactionPaid'];
	$totalAmountCharge = $getCopayRow['totalAmt'];
	$amtPaid = number_format($getCopayRow['amtPaid'], 2);
	$amountDue = number_format($getCopayRow['amountDue'], 2);
	$patientAmt = number_format($getCopayRow['patientAmt'], 2);
	$patientPaidAmt = number_format($getCopayRow['patientPaidAmt'], 2);
	$insPaidAmt = number_format($getCopayRow['insPaidAmt'], 2);
	$resPartyPaid = number_format($getCopayRow['resPartyPaid'], 2);
	$insAmt = number_format($getCopayRow['insAmt'], 2);
	$insuranceDue = number_format($getCopayRow['insuranceDue'], 2);
	$patientDue = number_format($getCopayRow['patientDue'], 2);
	$creditAmount = number_format($getCopayRow['creditAmount'], 2);
	$totalBalance = number_format($getCopayRow['totalBalance'], 2);
	$lastPayment = $getCopayRow['lastPayment'];
	$lastPaymentDate = $getCopayRow['lastPaymentDate'];

	$case_type_id = $getCopayRow['case_type_id'];
		$insCaseStr = "SELECT a.*, b.* FROM insurance_case a, 
						insurance_case_types b 
						WHERE 
						a.ins_case_type=b.case_id and
						a.patient_id='$patient_id'
						AND a.ins_caseid='$case_type_id'";
		$insCaseQry = imw_query($insCaseStr);
		$insCaseRow = imw_fetch_array($insCaseQry);
			$insTypeCode = $insCaseRow['case_name']." - ".$insCaseRow['ins_caseid'];

		$getInsProviderStr = "SELECT b.* FROM insurance_data a,
								insurance_companies b
								WHERE a.pid='$patient_id'
								AND (a.type='primary' OR a.type='secondary' OR a.type='tertiary')
								AND a.provider=b.id";
		$getInsProviderQry = imw_query($getInsProviderStr);
		$getInsProviderRow = imw_fetch_array($getInsProviderQry);
			$insProvider = $getInsProviderRow['name'];
	
	
//----------------------------	MAKE PAYMENT SECTION	----------------------------//


if(($_REQUEST['applySubmit']) || ($_REQUEST['applyReceiptSubmit'])){
	$statement_count=1;
	$coPayChk = $_REQUEST['coPayChk'];
	$coPayChk2 = $_REQUEST['coPayChk2'];
	$seqForchk = $_REQUEST['seqForCopay'];
	$chkbxArr = $_REQUEST['chkbx'];
	$paymentMode = $_REQUEST['paymentMode'];
	$checkNo = $_REQUEST['checkNo'];
	$cCNo = $_REQUEST['cCNo'];
	$paidDate = $_REQUEST['paidDate'];
		list($month, $day, $year) = explode("-", $paidDate);
		$paidDate = $year."-".$month."-".$day;
	$expDate = $_REQUEST['expireDate'];
	$creditCardCo = $_REQUEST['creditCardCo'];
	$paidAmount = $_REQUEST['paidAmount']+$_REQUEST['copayAmount'];
	$actualPaidAmount = $_REQUEST['paidAmount'];
	$paymentMadeIs = $_REQUEST['paidAmount'];
	$insSelected = $_REQUEST['insSelected'];
    if(isset($card_details_str_id) && $card_details_str_id!='' && $creditCardCo=='' && $cCNo=='' && $expDate==''){
        $card_details_arr=explode('~~',trim($card_details_str_id));
        $creditCardCo=$card_details_arr[0];
        $cCNo=$card_details_arr[1];
        $expDate=$card_details_arr[2];
    }

	$copay_apply_procedure = $_REQUEST['copay_apply_procedure'];
	$copayAmount = $_REQUEST['copayAmount'];
	$facility_id = $_REQUEST['facility_id'];
	
	for($i=1;$i<=$seqForchk;$i++){
	//echo $i;
		if($_REQUEST['payNew_Amount'.$i]<>"" && $_REQUEST['payNew_Amount'.$i]<>0) {
			$payNew_Amount[] = str_replace(',','',$_REQUEST['payNew_Amount'.$i]);
		}	
	}
	
	if($insSelected == 1 && $paidAmount>0){
		$paymentOf = 'primary_paid';
	}else if($insSelected == 2 && $paidAmount>0){
		$paymentOf = 'secondary_paid';
	}else if($insSelected == 3 && $paidAmount>0){
		$paymentOf = 'tertiary_paid';
	}
	if($paymentOf && $paidAmount>0){
		$updateQry = imw_query("UPDATE patient_charge_list SET $paymentOf = 'true' WHERE encounter_id = '$encounter_id'");
	}
	// CODE OF HEARDOFF INSURANCE PAYMENT	
	
	$paidBy = $_REQUEST['paidBy'];	
	
	//------------- LAST PAYMENT DETAILS -------------------//
	$updateCoPayStr = "UPDATE patient_charge_list SET
							amtPaid = amtPaid+'$paidAmount',
							amountDue = amountDue-'$paidAmount',
							totalBalance = totalBalance-'$paidAmount',
							lastPayment = '$paidAmount',
							lastPaymentDate = '$paidDate'
							WHERE encounter_id = '$encounter_id'";
	$updateCoPayQry = imw_query($updateCoPayStr);
	//------------- LAST PAYMENT DETAILS -------------------//
	
	//-------- GETTING COPAY IS Required OR NOT, PAID OR NOT 
	$getCopayStr = "SELECT * FROM patient_charge_list
					WHERE del_status='0' and encounter_id = '$encounter_id'";
	$getCopayQry = imw_query($getCopayStr);
	$getCopayRow = imw_fetch_array($getCopayQry);
	$charge_list_id = $getCopayRow['charge_list_id'];
	$copay = $getCopayRow['copay'];
	$copayPaid = $getCopayRow['copayPaid'];
	$coPayNotRequired = $getCopayRow['coPayNotRequired'];
	$coPayNotRequired2 = $getCopayRow['coPayNotRequired2'];
	$coPayWriteOff = $getCopayRow['coPayWriteOff'];	
	$patientPaidAmt = $getCopayRow['patientPaidAmt']+$copayAmount;
	
	$getProcCountStr123 = "SELECT newBalance FROM patient_charge_list_details WHERE del_status='0' and charge_list_detail_id='$copay_apply_procedure'";
	$getProcCountQry123 = imw_query($getProcCountStr123);
	if(imw_num_rows($getProcCountQry123)>0){
		$getProcCountRow123 = imw_fetch_array($getProcCountQry123);
		$newBalance_chhk = $getProcCountRow123['newBalance'];
		
	}
	
	//-------- GETTING COPAY IS Required OR NOT, PAID OR NOT
	$getproccode = "SELECT sum(paidForProc) as tot_paidproc FROM 
					patient_chargesheet_payment_info a,
					patient_charges_detail_payment_info b
					WHERE a.encounter_id = '$encounter_id'
					AND a.payment_id = b.payment_id
					AND b.charge_list_detail_id = 0
					AND b.deletePayment=0
					ORDER BY a.payment_id DESC";
	$getproccodeQry = imw_query($getproccode);
	$getproccodeRow = imw_fetch_array($getproccodeQry);
	$paidForProc_chk = $getproccodeRow['tot_paidproc'];
	$tot_paid_chk=$paidForProc_chk+$copayAmount;
	if(($copay>0) && ($coPayNotRequired=='0' || $coPayNotRequired2=='0') && ($coPayWriteOff!='1') && ($copayAmount>0)){
		if($copay_apply_procedure<>"" && $copay_apply_procedure<>0){

			
			if($copayAmount==$copay || $copay==$tot_paid_chk){
				$coPayAdjustedAmount_chk=1;
				$copayPaid_chk=1;
				$coPayAdjusted_chk=1;
			}else{
				$coPayAdjustedAmount_chk=1;
				$copayPaid_chk=0;
				$coPayAdjusted_chk=0;
			}
			$copayApplyOn=$copay_apply_procedure;
			
			
			if($copayApplyOn!=''){
			
				if($copayAmount>$newBalance_chhk){
					$copayAmount_min=$newBalance_chhk;
					$over_pay_bycopay=$copayAmount-$newBalance_chhk;
				}else{
					$copayAmount_min=$copayAmount;
					$over_pay_bycopay=0;
				}
			
				$updateChargeListDetailIdStr = "UPDATE patient_charge_list_details SET
												coPayAdjustedAmount = $coPayAdjustedAmount_chk,
												newBalance = newBalance-'$copayAmount_min',
												overPaymentForProc=overPaymentForProc+$over_pay_bycopay,
												balForProc =balForProc-$copayAmount_min,
												paidStatus = 'Paid',superBillUpdate = '1'
												WHERE charge_list_detail_id = '$copayApplyOn'";
				$updateChargeListDetailIdQry = imw_query($updateChargeListDetailIdStr);
			
			}else{
				$paymentMadeIs = $paymentMadeIs - $copayAmount;
				
				$getProcDetailsStr = "SELECT * FROM 
										patient_charge_list_details a,
										cpt_fee_tbl b
										WHERE a.charge_list_id = '$charge_list_id'
										AND a.procCode = b.cpt_fee_id
										AND b.cpt4_code != '92015'
										AND a.newBalance >= '$copay'";
				$getProcDetailsQry = imw_query($getProcDetailsStr);
				while($getProcDetailsRow = imw_fetch_array($getProcDetailsQry)){
					$cpt4_code = $getProcDetailsRow['cpt4_code'];
					$charge_list_detail_id = $getProcDetailsRow['charge_list_detail_id'];
					/*if(in_array($cpt4_code, $eAndMCptArray)){
						$copayApplyOn = $charge_list_detail_id;
						break;
					}else{
						$copayApplyOn = $charge_list_detail_id;
					}*/
					$copayApplyOn = $charge_list_detail_id;
				}
				$updateChargeListDetailIdStr = "UPDATE patient_charge_list_details SET
												coPayAdjustedAmount = $coPayAdjustedAmount_chk,
												newBalance = newBalance-'$copayAmount',
												balForProc =balForProc-$copayAmount,
												paidStatus = 'Paid',superBillUpdate = '1'
												WHERE charge_list_detail_id = '$copayApplyOn'";
				$updateChargeListDetailIdQry = imw_query($updateChargeListDetailIdStr);
			}
			//------------- UPDATE COPAY STATUS ------------//
				$updateCoPayStr = "UPDATE patient_charge_list SET
										copayPaid = '$copayPaid_chk',
										coPayAdjusted = '$coPayAdjusted_chk',
										coPayPaidDate = '$paidDate',
										coPayAdjustedDate = '$paidDate'
										WHERE encounter_id='$encounter_id'";
				$updateCoPayQry = imw_query($updateCoPayStr);
			//------------- UPDATE COPAY STATUS ------------//
			//---------- GETTING COPAY TO APPLY ON
		}else{											//********************* IF COPAY IS NOT CHECKED
			if($copayPaid==1){
				$copayApplyOn=$copay_apply_procedure;
				
				if($copayApplyOn!=''){
					$getProcDetailsStr = "SELECT * FROM patient_charge_list_details
											WHERE del_status='0' and charge_list_id = '$charge_list_id'
											AND coPayAdjustedAmount = '1'";
					$getProcDetailsQry = imw_query($getProcDetailsStr);
					$getRows = imw_num_rows($getProcDetailsQry);
					if($getRows>0){
						$getProcDetailsRow = imw_fetch_array($getProcDetailsQry);
						$alreadyAppliedOn = $getProcDetailsRow['charge_list_detail_id'];
					}
					if($alreadyAppliedOn!=$copayApplyOn){
						$updateChargeListDetailIdStr = "UPDATE patient_charge_list_details SET
														coPayAdjustedAmount = 1,
														newBalance = newBalance-'$copayAmount',
														balForProc =balForProc-$copayAmount,
														paidStatus = 'Paid',superBillUpdate = '1'
														WHERE charge_list_detail_id = '$copayApplyOn'";
						$updateChargeListDetailIdQry = imw_query($updateChargeListDetailIdStr);
						
						$updateChargeListDetailIdStr = "UPDATE patient_charge_list_details SET
														coPayAdjustedAmount = 0,
														newBalance = newBalance+'$copayAmount'
														WHERE charge_list_detail_id = '$alreadyAppliedOn'";
						$updateChargeListDetailIdQry = imw_query($updateChargeListDetailIdStr);
						//------------- UPDATE COPAY STATUS ------------//
							$updateCoPayStr = "UPDATE patient_charge_list SET
													coPayAdjustedDate = '$paidDate'
													WHERE encounter_id = '$encounter_id'";
							$updateCoPayQry = imw_query($updateCoPayStr);
						//------------- UPDATE COPAY STATUS ------------//
					}
				}
			}
		}
	}

	//------------------------------- MAKING COPAY ADJUSTED -------------------------------//
		if(($coPayChk=='true' || $coPayChk2=='true')&& $copayAmount>0){ 
			if($copayAmount==$copay || $copay==$tot_paid_chk){
				$coPayAdjustedAmount_chk=1;
				$copayPaid_chk=1;
				$coPayAdjusted_chk=1;
			}else{
				$coPayAdjustedAmount_chk=0;
				$copayPaid_chk=0;
				$coPayAdjusted_chk=0;
			}
			$updateCoPayStr1 = "UPDATE patient_charge_list SET
						copayPaid = '$copayPaid_chk',
						coPayPaidDate = '$paidDate'
						WHERE encounter_id = '$encounter_id'";
			$updateCoPayQry1 = imw_query($updateCoPayStr1);
		}
	
	if($chkbxArr!=""){
		
		if($paidBy == 'Insurance'){
			$insProviderName = $_REQUEST['insProviderName'];
			//---------------------- GETTING INSURANCE PROVIDER CO. ID ----------------------//
				$getInsCoIdStr = "SELECT * FROM insurance_companies 
									WHERE in_house_code = '$insProviderName'
									OR name = '$insProviderName'";
				$getInsCoIdQry = imw_query($getInsCoIdStr);
				$getInsCoIdRow = imw_fetch_array($getInsCoIdQry);
					$insCoId = $getInsCoIdRow['id'];
			//---------------------- GETTING INSURANCE PROVIDER CO. ID ----------------------//
		}
		
		$pay_name=0;
		if($chkbxArr!=""){
			//-----------------------     PAYMENT INFO      -----------------------//
				if(($coPayChk == 'true' || $coPayChk2 == 'true') && $copayAmount>0){
					$insertPaymentInfoStr1 = "INSERT INTO patient_chargesheet_payment_info SET
										encounter_id = '$encounter_id', paid_by ='$paidBy',
										payment_amount = '$copayAmount',
										insProviderId = '$insCoId',
										payment_mode='$paymentMode', checkNo='$checkNo',
										creditCardNo ='$cCNo', creditCardCo = '$creditCardCo',
										date_of_payment = '$paidDate', expirationDate = '$expDate',
										balance_amount = '0', operatorId = '$operatorId',
										insCompany = '$insSelected',
										transaction_date = '$transactionDate',facility_id='$facility_id'";
					$insertPaymentInfoQry1 = imw_query($insertPaymentInfoStr1);
					$payment_insert_id_copay = imw_insert_id();
					$insertDetailStr = "INSERT INTO patient_charges_detail_payment_info SET
										payment_id = '$payment_insert_id_copay',
										charge_list_detail_id = '',
										paidBy = '$paidBy',
										paidDate = '$paidDate',
										paidForProc = '$copayAmount',
										operator_id='$operator_id',
										entered_date='$entered_date',
										log_referenceNumber='$log_referenceNumber',
										tsys_transaction_id='$tsys_transaction_id'";
					$insertDetailQry = imw_query($insertDetailStr);				
				}				
			//-----------------------     PAYMENT INFO      -----------------------//
			
			foreach($chkbxArr as $key => $chargeListid){
				if($payNew_Amount){
					foreach($payNew_Amount as $k => $amountPaidTo){
						if($k == $key){
							if($amountPaidTo>0){
								$insertPaymentInfoStr = "INSERT INTO patient_chargesheet_payment_info SET
											encounter_id = '$encounter_id', paid_by ='$paidBy',
											payment_amount = '$amountPaidTo',
											insProviderId = '$insCoId',
											payment_mode='$paymentMode', checkNo='$checkNo',
											creditCardNo ='$cCNo', creditCardCo = '$creditCardCo',
											date_of_payment = '$paidDate', expirationDate = '$expDate',
											balance_amount = '0', operatorId = '$operatorId',
											insCompany = '$insSelected',
											transaction_date = '$transactionDate',facility_id='$facility_id'";
								$insertPaymentInfoQry = imw_query($insertPaymentInfoStr);
								$paymentInsertId = imw_insert_id();
								
								$insertPaymentDetailsInfoStr = "INSERT INTO patient_charges_detail_payment_info SET
																payment_id = '$paymentInsertId', charge_list_detail_id ='$chargeListid',
																paidBy = '$paidBy', paidDate = '$paidDate',
																paidForProc = '$amountPaidTo',
																operator_id='$operator_id',
																entered_date='$entered_date',
                                                                log_referenceNumber='$log_referenceNumber',
                                                                tsys_transaction_id='$tsys_transaction_id'";
								$insertPaymentDetailsInfoQry = imw_query($insertPaymentDetailsInfoStr);
							}
						}
					}
				}	
			}
			//-----------------------  PAYMENT DETAILS INFO 	-----------------------//
		}
		
		foreach($chkbxArr as $chargeListid){
			$getProcDetailsForPaymentStr =  "SELECT * FROM 
											patient_charge_list_details a,
											cpt_fee_tbl b
											WHERE a.charge_list_detail_id = '$chargeListid'
											AND a.procCode = b.cpt_fee_id";
			$getProcDetailsForPaymentQry = imw_query($getProcDetailsForPaymentStr);
			while($getProcDetailsForPaymentRows = imw_fetch_array($getProcDetailsForPaymentQry)){
				//----------------- GETTING DETAILS OF PROCEDURES CPT FEE TABLE.	-----------------//
				$cpt4CodeForPayment = $getProcDetailsForPaymentRows['cpt4_code'];
					$cpt4_code = $getProcDetailsForPaymentRows['cpt4_code'];
					
				$cptPracCodeForPayment = $getProcDetailsForPaymentRows['cpt_prac_code'];
					$cpt_prac_code = $getProcDetailsForPaymentRows['cpt_prac_code'];
					
				if(($cpt4_code==92015) || ($cpt_prac_code==92015)){
						$updatepatientAmtStr = "UPDATE patient_charge_list SET
												referactionPaid = 1
												WHERE encounter_id='$encounter_id'";
						//$updatepatientAmtQry = imw_query($updatepatientAmtStr);
				}
				
				//----------------- GETTING DETAILS OF PROCEDURES CPT FEE TABLE.	-----------------//
			
				//----------------- GETTING DETAILS OF PROCEDURES CHARGE LIST DETAIL TABLE.	-----------------//
				$chargeListIdForPayment = $getProcDetailsForPaymentRows['charge_list_id'];
				$totalAmountForPayment = $getProcDetailsForPaymentRows['totalAmount'];
				$paidForProcForPayment = $getProcDetailsForPaymentRows['paidForProc'];
				$balForProcForPayment = $getProcDetailsForPaymentRows['balForProc'];
				$approvedAmtForPayment = $getProcDetailsForPaymentRows['approvedAmt'];
				$deductAmtForPayment = $getProcDetailsForPaymentRows['deductAmt'];
				$newBalanceForPayment = $getProcDetailsForPaymentRows['newBalance'];
				$overPaymentForProc = $getProcDetailsForPaymentRows['overPaymentForProc'];
				
					$paidForProcAmountArr[] = $newBalanceForPayment;
					
				$creditProcAmount = $getProcDetailsForPaymentRows['creditProcAmount'];
				$coPayAdjustedAmount = $getProcDetailsForPaymentRows['coPayAdjustedAmount'];
				//----------------- GETTING DETAILS OF PROCEDURES CHARGE LIST DETAIL TABLE.	-----------------//
			}
			//----------------- GETTING ENCOUNTERS DETAIL.	-----------------//
			$getEncountersDetailsStr = "SELECT * FROM patient_charge_list 
										WHERE del_status='0' and charge_list_id = '$chargeListIdForPayment'";
			$getEncountersDetailsQry = imw_query($getEncountersDetailsStr);
			$getEncountersDetailsRow = imw_fetch_array($getEncountersDetailsQry);
				$copayForEnc = $getEncountersDetailsRow['copay'];
				$copayPaidForEnc = $getEncountersDetailsRow['copayPaid'];
				$coPayNotRequired = $getEncountersDetailsRow['coPayNotRequired'];
				$coPayNotRequired2 = $getEncountersDetailsRow['coPayNotRequired2'];
				$coPayWriteOff = $getEncountersDetailsRow['coPayWriteOff'];
				$referactionPaidForEnc = $getEncountersDetailsRow['referactionPaid'];
				$amtPaidForEnc = $getEncountersDetailsRow['amtPaid'];
				$amountDueForEnc = $getEncountersDetailsRow['amountDue'];
				$patientAmtForEnc = $getEncountersDetailsRow['patientAmt'];
				$patientPaidAmtForEnc = $getEncountersDetailsRow['patientPaidAmt'];
				$patientDueForEnc = $getEncountersDetailsRow['patientDue'];
				$insPaidAmtForEnc = $getEncountersDetailsRow['insPaidAmt'];
				$insAmtForEnc = $getEncountersDetailsRow['insAmt'];
				$insuranceDueForEnc = $getEncountersDetailsRow['insuranceDue'];
				$totalBalanceForEnc = $getEncountersDetailsRow['totalBalance'];
				$totalCreditForEnc = $getEncountersDetailsRow['creditAmount'];
			//----------------- GETTING ENCOUNTERS DETAIL.	-----------------//
				$getproccode = "SELECT sum(paidForProc) as tot_paidproc FROM 
						patient_chargesheet_payment_info a,
						patient_charges_detail_payment_info b
						WHERE a.encounter_id = '$encounter_id'
						AND a.payment_id = b.payment_id
						AND b.charge_list_detail_id = 0
						AND b.deletePayment=0
						ORDER BY a.payment_id DESC";
				$getproccodeQry = imw_query($getproccode);
				$getproccodeRow = imw_fetch_array($getproccodeQry);
				$paidForProc_chk = $getproccodeRow['tot_paidproc'];
				
				if($payNew_Amount[$pay_name]>$newBalanceForPayment){
					$balForProc=0;
					//$paidForProc=$balForProcForPayment;
					$paidForProc=$approvedAmtForPayment;
					$newOverPay = $payNew_Amount[$pay_name]-$balForProcForPayment;
					$newBalanceNow = 0;
				}else{			
					$newOverPay = 0;		
					$newBalanceNow = $newBalanceForPayment-$payNew_Amount[$pay_name];
					$balForProc = $balForProcForPayment-$payNew_Amount[$pay_name];
					$paidForProc = $totalAmountForPayment-$balForProc;
					if($coPayAdjustedAmount==1){
						$paidForProc=$paidForProc-$paidForProc_chk;
					}
				}
				if($newBalanceNow==0){
					$balForProc=0;
				}
				
				//echo $balForProc;
				
				
				 $updateStr = "UPDATE patient_charge_list_details SET
								paidForProc = $paidForProc,
								balForProc = $balForProc, 
								newBalance = $newBalanceNow,
								overPaymentForProc = overPaymentForProc+'$newOverPay',
								paidStatus = 'Paid',
								superBillUpdate = '1'
								WHERE charge_list_detail_id = '$chargeListid'";
				$updateQry = imw_query($updateStr);
			$pay_name++;	
			if($payNew_Amount){
				foreach($payNew_Amount as $k => $amountPaidTo){
					if($k == $key){
						if($amountPaidTo>0 or $paidBy=="Insurance"){
							if($paidBy=="Insurance" && $insCoId>0){
								$pay_type="ins";
								$ins_type=$insSelected;
							}else{
								$pay_type="pat";
							}
							patient_proc_tx_update($chargeListid,$amountPaidTo,$pay_type,$ins_type);
						}
					}
				}
			}
			
		}
		//-----------------------  PAYMENT DETAILS INFO 	-----------------------//
		
		//-----------------------  PAYMENT DETAILS INFO 	-----------------------//
	}else{
		if($coPayChk == 'true' || $coPayChk2 == 'true'){
			$insertPaymentInfoStr = "INSERT INTO patient_chargesheet_payment_info SET
										encounter_id = '$encounter_id', paid_by ='$paidBy',
										payment_amount = '$copayAmount',
										insProviderId = '$insCoId',
										payment_mode='$paymentMode', checkNo='$checkNo',
										creditCardNo ='$cCNo', creditCardCo = '$creditCardCo',
										date_of_payment = '$paidDate', expirationDate = '$expDate',
										balance_amount = '0', operatorId = '$operatorId',
										insCompany = '$insSelected',
										transaction_date = '$transactionDate',facility_id='$facility_id'";
			$insertPaymentInfoQry = imw_query($insertPaymentInfoStr);
			$payment_insert_id = imw_insert_id();
			$insertDetailStr = "INSERT INTO patient_charges_detail_payment_info SET
								payment_id = '$payment_insert_id',
								charge_list_detail_id = '',
								paidBy = '$paidBy',
								paidDate = '$paidDate',
								paidForProc = '$copayAmount',
								operator_id='$operator_id',
								entered_date='$entered_date',
                                log_referenceNumber='$log_referenceNumber',
                                tsys_transaction_id='$tsys_transaction_id'";
			$insertDetailQry = imw_query($insertDetailStr);				
		}
	}
	
	include("manageEncounterAmounts.php");
	
	//-----------------------  PAYMENT DETAILS INFO 	-----------------------//
	if($_REQUEST['applyReceiptSubmit']<>""){
		set_payment_trans($encounter_id,'',$stop_clm_status);
		if(is_array($chkbxArr)){
			$chkbxArr_exp=implode(',',$chkbxArr);
		}else{
			$chkbxArr_exp=$chkbxArr;
		}
		?>
		<script>
			var eId = '<?php echo $encounter_id; ?>';
			//var ch_id= '<?php echo $chkbxArr_exp; ?>';
			var ch_id= '';
			window.open("receipt.php?eId="+eId+'&ch_id='+ch_id,'_blank','width=1000,height=675,top=10,left=40,scrollbars=yes,resizable=yes');
			//window.close();
			window.opener.top.fmain.location.href="accounting_view.php?encounter_id="+eId;
		</script>
		<?php
	}else{
		?>
		<script>
			var eId = '<?php echo $encounter_id; ?>';
			window.opener.top.fmain.location.href="accounting_view.php?encounter_id="+eId; 
			window.location.href="quickPay.php?eId="+eId;
		</script>
		<?php
	}
}
//----------------------------	MAKE PAYMENT SECTION	----------------------------//	


// CHECK REFRACTION IS TO COLLECTOR NOT
	$getRefChkStr = "Select refraction FROM copay_policies WHERE policies_id = '1'";
	$getRefChkQry = imw_query($getRefChkStr);
	$getRefChkRow = imw_fetch_assoc($getRefChkQry);
	$refractionChk = $getRefChkRow['refraction'];
	$all_prov_imp_ids=implode(',',$all_prov_ids);
	if($refractionChk=='No'){
		$getRefChkStr = imw_query("Select id FROM users 
							WHERE id in($all_prov_imp_ids) 
							and collect_refraction ='1'");
		if(imw_num_rows($getRefChkStr)>0){
			$refractionChk='yes';
		}
	}
// CHECK REFRACTION IS TO COLLECTOR NOT
$getproccode1 = "SELECT sum(paidForProc) as tot_paidproc FROM 
				patient_chargesheet_payment_info a,
				patient_charges_detail_payment_info b
				WHERE a.encounter_id = '$encounter_id'
				AND a.payment_id = b.payment_id
				AND b.charge_list_detail_id = 0
				AND b.deletePayment=0
				ORDER BY a.payment_id DESC";
$getproccodeQry1 = imw_query($getproccode1);
$getproccodeRow1 = imw_fetch_array($getproccodeQry1);
$paidForProc_chk1 = $getproccodeRow1['tot_paidproc'];
$tot_paid_chk1=$paidForProc_chk1;

$qry = imw_query("select pm_id,pm_name from payment_methods where del_status='0' order by default_method desc, pm_name");
while($row = imw_fetch_array($qry)){ 
	$payment_method_arr[$row['pm_id']]=$row['pm_name'];
}

$login_facility=$_SESSION['login_facility'];
$pos_device=false;
$devices_sql="Select *, tsys_device_details.id as d_id from tsys_device_details 
              JOIN tsys_merchant ON tsys_merchant.id= tsys_device_details.merchant_id 
              WHERE device_status=0 
              AND tsys_device_details.facility_id='".$login_facility."' 
              AND merchant_status=0
              ";
$resp = imw_query($devices_sql);
if($resp && imw_num_rows($resp)>0){
    $pos_device=true;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<title>Quick Payment</title>
<link href="../../library/css/jquery-ui.min.css" type="text/css" rel="stylesheet">
<link href="../../library/css/bootstrap.min.css" type="text/css" rel="stylesheet">
<link href="../../library/css/bootstrap-select.css" type="text/css" rel="stylesheet">
<link href="../../library/css/common.css" type="text/css" rel="stylesheet">
<link href="../../library/css/core.css" type="text/css" rel="stylesheet">
<link href="../../library/css/accounting.css" type="text/css" rel="stylesheet">
<link href="../../library/css/jquery.datetimepicker.min.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/messi/messi.css">

<script type="text/javascript" src="../../library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="../../library/js/jquery-ui.min.1.11.2.js"></script>
<script type="text/javascript" src="../../library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../library/js/bootstrap-typeahead.js"></script>
<script type="text/javascript" src="../../library/js/bootstrap-select.js"></script>
<script type="text/javascript" src="../../library/js/jquery.datetimepicker.full.min.js"></script>
<script type="text/javascript" src="../../library/js/common.js"></script>
<script type="text/javascript" src="../../library/js/icd10_autocomplete.js"></script> 
<script type="text/javascript" src="../../library/js/acc_common.js"></script> 
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js"></script>

<script type="text/javascript">
var pos_device='<?php echo $pos_device; ?>';
var pos_patient_id='<?php echo $patient_id;?>';
var today = new Date();
var day = today.getDate();
var month = today.getMonth()
var year = y2k(today.getYear());
var mon=month+1;
if(mon<=9){
	mon='0'+mon;
}
var todaydate=mon+'-'+day+'-'+year;

function removeCommas(val){
	if(val.indexOf(",") != -1){
		do{
			var val = val.replace(",", "");
		}while(val.indexOf(",") != -1)
	}
	if(val.indexOf("$") != -1){
		do{
			var val = val.replace("$", "");
		}while(val.indexOf("$") != -1)
	}
	return val;
}
function y2k(number){
	return (number < 1000)? number+1900 : number;
}
function newWindow(q){
	mywindow=open('../common/mycal1.php?md='+q,'','width=200,height=250,top=200,left=300');
	mywindow.location.href = '../common/mycal1.php?md='+q;
	if(mywindow.opener == null)
		mywindow.opener = self;
}
function restart(q){
	var fillDate = ''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year;
	if(q==8){
		if(todaydate<fillDate){
			alert("Date Of Service can not be a future date")
			return false;
		}
	}
	document.getElementById("date"+q).value=''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year;
	mywindow.close();
}
function padout(number){
	return (number < 10) ? '0' + number : number;
}
function applieStatus(s, cop){
	var chkbx = document.getElementById("chkbx"+s).checked;
	var amt = parseFloat(document.getElementById("payAmt").innerHTML);
	var fee = parseFloat(document.getElementById("totalFee"+s).innerHTML);
	if(chkbx==true){
		var payAmount = amt+fee;
		document.getElementById("payAmt").innerHTML=payAmount;
		document.getElementById("paidAmount").value=payAmount;
	}else{
		var payAmount = amt-fee;
		document.getElementById("payAmt").innerHTML=payAmount;
		document.getElementById("paidAmount").value=payAmount;
	}
}
function check(tAmt, amt, cop, rows, btn, btn2, valBtn){
	var procTotalAmt=0;
	var paymentIsMade = document.getElementById("paidAmount").value;
	var paymentDate = document.getElementById("date1").value;
	var enter_copayamt=document.getElementById("copayAmount").value;
	var pay_copayamt=document.getElementById("copayAmount_hid").value;
	var arr_apply_copay=new Array(99212,99213,99214,99215,99201,99202,99203,99204,99205,99241,99242,99243,99244,99245,92012,92013,92014,92002,92003,92004);
	var copay_chk_box=false;
	
	if(paymentDate == ''){
		alert('Please enter Payment Date.');
		return false;
	}

	if(enter_copayamt>0){
		if(document.getElementById('coPayChk')){
			if(document.getElementById('coPayChk').checked==true){
				copay_chk_box=true;
			}
		}
		if(document.getElementById('coPayChk2')){
			if(document.getElementById('coPayChk2').checked==true){
				copay_chk_box=true;
			}
		}
	}
	
	if(parseInt(enter_copayamt)>parseInt(pay_copayamt)){
		alert("Copay amount should not be greater than "+pay_copayamt);
		return false;
	}
	if(paymentIsMade>amt){

	}

	for(var i=1; i<=rows; i++){
		var procAmt = document.getElementById("totalFee"+i).innerHTML;
		do{
			if(procAmt.indexOf(",")!=-1){
				do{
					var procAmt = procAmt.replace(",", "");
				}while(procAmt.indexOf(",")!=-1)
			}
		}while(procAmt.indexOf(",")!=-1)
		var procTotalAmt = parseFloat(procTotalAmt)+parseFloat(procAmt);
	}
	
	var paidAmt = document.getElementById("paidAmount").value;
	var comAmt = amt+cop;

	if(comAmt<paidAmt){
		
	}

	if(amt=='0'&& pay_copayamt=='0'){
		alert("No Due Amount to pay.")
		return false;
	}
	var f1 = document.getElementById("paymentMode").value;
	var flag = 0;
	var msg = "Please Enter following information\n";
	if(f1=="Check" || f1=="EFT" || f1=="Money Order" || f1=="VEEP"){
		var chkNo = document.getElementById("checkNo").value;
		var date1 = document.getElementById("date1").value;
		var paidBy = document.getElementById("paidBy").value;
		var paidAmt = document.getElementById("paidAmount").value;

		if(chkNo==""){ msg+="-Check No\n";	++flag; }
		if(date1==""){ msg+="-Paid Date\n";	++flag; }
		if(paidBy==""){ msg+="-Paid By\n";	++flag; }
		//if((paidAmt=="") || (paidAmt==0)){ msg+="-Amount\n";	++flag; }
		if((paidAmt=="") || (paidAmt=='NaN')){ msg+="-Amount\n";	++flag; }
		if(flag>0){
			alert(msg)
			return false;
		}
	}
	if(document.getElementById("paidBy").value=="Insurance"){
		var insProv = document.getElementById("insProviderName").value;
		if((insProv=='') || (insProv=='N/A')){
				alert("Please select Any insurance company. if selected may be N/A.")
				return false;
		}
		var insCoIS = document.getElementById("insProviderName").selectedIndex;		
		document.getElementById("insSelected").value = insCoIS;
	}
	
    var no_pos_device=false;
    if($('#tsys_device_url').val()=='no_pos_device') {
        no_pos_device=true;
    }
	
	if(f1=="Credit Card" && (!pos_device || no_pos_device==true) ){
		var creditCNo = document.getElementById("cCNo").value;
		var creditCardCo = document.getElementById("creditCardCo").value;
		var date1 = document.getElementById("date1").value;
		var expDate = document.getElementById("date2").value;
		var paidBy = document.getElementById("paidBy").value;
		var paidAmt = document.getElementById("paidAmount").value;

		if(creditCNo==""){ msg+="-Credit Card No\n";	++flag; }
		if(creditCardCo==""){ msg+="-Credit Card Co.\n";	++flag; }
		//if(expDate==""){ msg+="-Expiration Date\n";	++flag; }
		if(paidBy==""){ msg+="-Paid By\n";	++flag; }
		if(date1==""){ msg+="-Paid Date\n";	++flag; }
		//if((paidAmt=="") || (paidAmt==0)){ msg+="-Amount\n";	++flag; }
		if((paidAmt=="") || (paidAmt=='NaN')){ msg+="-Amount\n";	++flag; }
		if(flag>0){
			alert(msg)
			return false;
		}

	}
	if(f1=="Cash"){
		var paidBy = document.getElementById("paidBy").value;
		var date1 = document.getElementById("date1").value;
		var paidAmt = document.getElementById("paidAmount").value;
		if(date1==""){ msg+="-Paid Date\n";	++flag; }
		if(paidBy==""){ msg+="-Paid By\n";	++flag; }
		//if((paidAmt=="") || (paidAmt==0)){ msg+="-Amount\n";	++flag; }
		if((paidAmt=="") || (paidAmt=='NaN')){ msg+="-Amount\n";	++flag; }
		if(flag>0){
			alert(msg)
			return false;
		}

	}
    
    $("#valueBtn").val(valBtn);
	
    if($('#paymentMode').val()=='Credit Card' && pos_device && no_pos_device==false && typeof(make_cccard_payment)!='undefined'){
        make_cccard_payment();            
    }else {
        pos_submit_frm();
    }
	
    
}
    
function pos_submit_frm() {
    var valBtn=$("#valueBtn").val();
    switch(valBtn) {
        case 'applySubmit':
            var btn='applySubmitBtn';
            var btn2='applyReceiptSubmitBtn';
            break;
        case 'applyReceiptSubmit':
            var btn='applyReceiptSubmitBtn';
            var btn2='applySubmitBtn';
            break;
    }
    
    document.getElementById(btn).disabled = "true";
	document.getElementById(btn2).disabled = "true";
	document.getElementById(valBtn).value='true';
	document.pamentFrm.submit();   
	var eId = <?php echo $encounter_id; ?>;
    
}
function showRow(){
	var show = document.getElementById("paymentMode").value;
	if(show=="Cash"){
		document.getElementById("checkRow").style.display="none";
		document.getElementById("creditCardRow").style.display="none";
	}
	if(show=="Check" || show=="EFT" || show=="Money Order" || show=="VEEP"){
		document.getElementById("checkRow").style.display="block";
		document.getElementById("creditCardRow").style.display="none";
	}
	if(show=="Credit Card"){
		document.getElementById("creditCardRow").style.display="block";
		document.getElementById("checkRow").style.display="none";
	}
}
function payAmtChk(s, p ,tot){
	var refractionChk = '<?php echo $refractionChk; ?>';
	if(p == '92015' && refractionChk == 'No'){
		alert("Refraction can not be collected.")
		return false;
	}	
	var tot= tot;
	var globalCPTId_false='';
	var payAmt = document.getElementById("payAmt").innerHTML;
	var paidAmount = parseFloat(document.getElementById("paidAmount").value);
	var chkStatus = document.getElementById("chkbx"+s).checked;
	var duesAmt = document.getElementById("amtDueTr").innerHTML;
	//alert(payAmt+'-'+paidAmount+'-'+chkStatus+'-'+duesAmt)
	
	if(duesAmt.indexOf(",") != -1){
		do{		
			var duesAmt = duesAmt.replace(",", "");
		}while(duesAmt.indexOf(",") != -1)
	}
	var min_amt=document.getElementById("payNew_Amount"+s).value;
	if(parseInt(min_amt)>0){
		var val = document.getElementById("payNew_Amount"+s).value;
	}else{
		var val = document.getElementById("procActualBal"+s).innerHTML;
	}
	var payAmt = parseFloat(document.getElementById("payAmt").innerHTML);
	
	
	if(val.indexOf(",") != -1){
		do{
			var val = val.replace(",", "");
		}while(val.indexOf(",") != -1)
	}

	if(chkStatus==true){
		if(tot){
			for(i=1;i<=tot;i++){
				if(document.getElementById('chkbx'+i)){
					if(document.getElementById('chkbx'+i).checked==true){
						globalCPTId_false += document.getElementById('cptIdTd'+i).value+",";
					}
				}	
			}
			document.getElementById('pro_arr').value = globalCPTId_false;
		}	
		var newPaidAmt = parseFloat(paidAmount) + parseFloat(val);
		var newAmtDue = duesAmt - val;
		var newPaidAmt =  (newPaidAmt.toFixed(2));
		var newAmtDue =  (newAmtDue.toFixed(2));
		document.getElementById("paidAmount").value = newPaidAmt;
		//document.getElementById("amtDueTr").innerHTML = newAmtDue;
		if(document.getElementById("chkbx"+s).checked==true){
			document.getElementById("payNew_Amount"+s).value = val;
		}
		copay_minus_procedure(tot);
		//alert(newPaidAmt+'-'+newAmtDue+'-'+paidAmount+'-'+val)
	}else{
		//alert(val);
			if(document.getElementById("payNew_Amount"+s).value>0){
				var val = document.getElementById("payNew_Amount"+s).value;
			}else{
				var val = document.getElementById("payNew_Amount"+s).value;
			}
		if(document.getElementById("coPayApplied"+s)){
			if(document.getElementById("coPayApplied"+s).checked==true){
				alert("CoPay Applied. Can't unchecked")
				return false;
			}
		}
		
		var newPaidAmt = parseFloat(paidAmount) - parseFloat(val);
		var newAmtDue = parseFloat(duesAmt) + parseFloat(val);		
		var newPaidAmt =  (newPaidAmt.toFixed(2));
		var newAmtDue =  (newAmtDue.toFixed(2));
		document.getElementById("paidAmount").value = newPaidAmt;
		//document.getElementById("amtDueTr").innerHTML = newAmtDue;
		
		if(document.getElementById("chkbx"+s).checked==false){
			document.getElementById("payNew_Amount"+s).value = '0.00';
		}
	}	
}
function printReceipt(eId){
	window.open("receipt.php?eId="+eId,'','width=1000,height=675,top=10,left=40,scrollbars=yes,resizable=yes');
	//window.close();
}
function cCCompany(){
	if(document.getElementById("creditCardCo").value=="Others"){
		document.getElementById("creditCardCoTd").innerHTML="";
		var ccCo = '<input name="creditCardCo" type="text" class="text_10" size="12" >';
		document.getElementById("creditCardCoTd").innerHTML=ccCo;
	}
}
function changeAmt(copayAmt){
	var copay_chk_box=false;
	if(document.getElementById('coPayChk')){
		if(document.getElementById('coPayChk').checked==true){
			copay_chk_box=true;
		}
	}
	if(document.getElementById('coPayChk2')){
		if(document.getElementById('coPayChk2').checked==true){
			copay_chk_box=true;
		}
	}
	if(copay_chk_box==true){
		document.getElementById('copayAmount').readOnly=false;
	}else{
		document.getElementById('copayAmount').readOnly=true;
	}
	var eleLength = document.getElementById("pamentFrm").elements.length;
	var pay = parseFloat(document.getElementById("payAmt").innerHTML);
	var paidAmount = parseFloat(document.getElementById("paidAmount").value);
	if(document.getElementById("coPayChk") && document.getElementById("coPayChk2")){
		if(document.getElementById("coPayChk").checked==false && document.getElementById("coPayChk2").checked==false){
		//var	newPay = paidAmount - copayAmt;
		var newPay=0;
		var newPay = (newPay.toFixed(2));
		document.getElementById("copayAmount").value = newPay;
		
		}else if(document.getElementById("coPayChk").checked==true && document.getElementById("coPayChk2").checked==true){
			//var	newPay = paidAmount + copayAmt;
			var	newPay = document.getElementById("copayAmount_hid").value;
			//var newPay = (newPay.toFixed(2));
			document.getElementById("copayAmount").value = newPay;
			if(document.getElementById('chk_count_for_copay')){
				var tot=document.getElementById('chk_count_for_copay').value;
			}
			copay_minus_procedure(tot);
		}
		else if(document.getElementById("coPayChk").checked==true && document.getElementById("coPayChk2").checked==false){
			//var	newPay = paidAmount + copayAmt;
			if(document.getElementById("pri_copay_chk").value>document.getElementById("copayAmount_hid").value){
				var	newPay = document.getElementById("copayAmount_hid").value;
			}else{
				var	newPay = document.getElementById("pri_copay_chk").value;
			}
			document.getElementById("copayAmount").value = newPay;
			if(document.getElementById('chk_count_for_copay')){
				var tot=document.getElementById('chk_count_for_copay').value;
			}
			copay_minus_procedure(tot);
		}
		else if(document.getElementById("coPayChk").checked==false && document.getElementById("coPayChk2").checked==true){
			//var	newPay = paidAmount + copayAmt;
			if(document.getElementById("sec_copay_chk").value>document.getElementById("copayAmount_hid").value){
				var	newPay = document.getElementById("copayAmount_hid").value;
			}else{
				var	newPay = document.getElementById("sec_copay_chk").value;
			}
			document.getElementById("copayAmount").value = newPay;
			if(document.getElementById('chk_count_for_copay')){
				var tot=document.getElementById('chk_count_for_copay').value;
			}
			copay_minus_procedure(tot);
		}
	}else if(document.getElementById("coPayChk")){
		if(document.getElementById("coPayChk").checked==false){
		//var	newPay = paidAmount - copayAmt;
		var newPay=0;
		var newPay = (newPay.toFixed(2));
		document.getElementById("copayAmount").value = newPay;
		}else if(document.getElementById("coPayChk").checked==true){
			//var	newPay = paidAmount + copayAmt;
			var	newPay = document.getElementById("copayAmount_hid").value;
			//var newPay = (newPay.toFixed(2));
			document.getElementById("copayAmount").value = newPay;
			if(document.getElementById('chk_count_for_copay')){
				var tot=document.getElementById('chk_count_for_copay').value;
			}
			copay_minus_procedure(tot);
		}
	}else if(document.getElementById("coPayChk2")){
			if(document.getElementById("coPayChk2").checked==false){
				//var	newPay = paidAmount - copayAmt;
				var newPay=0;
				var newPay = (newPay.toFixed(2));
				document.getElementById("copayAmount").value = newPay;
			
			}else if(document.getElementById("coPayChk2").checked==true){
				//var	newPay = paidAmount + copayAmt;
				if(document.getElementById("sec_copay_chk").value>document.getElementById("copayAmount_hid").value){
					var	newPay = document.getElementById("copayAmount_hid").value;
				}else{
					var	newPay = document.getElementById("sec_copay_chk").value;
				}
				document.getElementById("copayAmount").value = newPay;
				if(document.getElementById('chk_count_for_copay')){
					var tot=document.getElementById('chk_count_for_copay').value;
				}
				copay_minus_procedure(tot);
			}
		}	
	}

function showInsCoList(){
	var whoPaid = document.getElementById("paidBy").value;
	if(whoPaid=='Insurance'){
		document.getElementById("insCoNames").style.display="block";
	}else{
		document.getElementById("insCoNames").style.display="none";
	}
}
function closeMe(){
	window.close();
}
</script>
<script>
function coPayApplyFn(s, procBal, c, coPayPaid, totBal){
	if(procBal.indexOf(",")!=-1){
		do{
			var procBal = procBal.replace(",", "");
		}while(procBal.indexOf(",")!=-1)
	}
	var coPayApplyStatus = document.getElementById("coPayApplied"+s).checked;
	if(coPayApplyStatus==true){
		<!--  -->
		
		//var procBalIn = document.getElementById("procActualBal"+s).innerHTML;
		var procBalIn = document.getElementById("payNew_Amount"+s).value;

		if(procBalIn.indexOf(",")!=-1){
			do{
				var procBalIn = procBalIn.replace(",", "");
			}while(procBalIn.indexOf(",")!=-1)
		}
		if(parseFloat(procBalIn)<parseFloat(c)){
			return false;
		}
		var procBalIn = procBalIn - c;
		var procBalIn = (procBalIn.toFixed(2));
		//document.getElementById("procActualBal"+s).innerHTML = procBalIn;
		document.getElementById("payNew_Amount"+s).value = procBalIn;
		document.getElementById("totalFee"+s).innerHTML = procBalIn;
		<!--  -->

		<!--  -->
		var eleLength = document.getElementById("pamentFrm").elements.length;
		for(var i=0; i<eleLength; i++){
			var eleName = document.getElementById("pamentFrm").elements[i].name;
			if(eleName.indexOf("coPayApplied") != -1 ){
				if(document.getElementById("pamentFrm").elements[i].checked==true){
					if(document.getElementById("pamentFrm").elements[i].name!='coPayApplied'+s){
						var str = document.getElementById("pamentFrm").elements[i].name;
						var val = str.substr(12);
						//var procActBalIs = document.getElementById("procActualBal"+val).innerHTML;
						var procActBalIs = document.getElementById("payNew_Amount"+val).value;
						if(procActBalIs.indexOf(",")!=-1){
							do{
								var procActBalIs = procActBalIs.replace(",", "");
							}while(procActBalIs.indexOf(",")!=-1)
						}
						var procActBalIsNow = parseFloat(procActBalIs) + parseFloat(c);
						var procActBalIsNow = procActBalIsNow.toFixed(2);
						//document.getElementById("procActualBal"+val).innerHTML = procActBalIsNow;
						document.getElementById("payNew_Amount"+val).value = procActBalIsNow;
						
						document.getElementById("totalFee"+s).innerHTML = procActBalIsNow;
					}
				}
				document.getElementById("pamentFrm").elements[i].checked = false;
			}
		}
		document.getElementById("coPayApplied"+s).checked = true;
		<!--  -->
		var eleLength = document.getElementById("pamentFrm").elements.length;
		var amtToPayIs = 0;
		for(var i=0; i<eleLength; i++){
			var eleName = document.getElementById("pamentFrm").elements[i].name;
			if(eleName.indexOf("chkbx") != -1 ){
				if(document.getElementById("pamentFrm").elements[i].checked==true){
					var presentVal = document.getElementById("pamentFrm").elements[i].id;
					var presentVal = presentVal.substr(5);
					//var amtForBal = document.getElementById("procActualBal"+presentVal).innerHTML;
					var amtForBal = document.getElementById("payNew_Amount"+presentVal).value;
					if(amtForBal.indexOf(",")!=-1){
						do{
							var amtForBal = amtForBal.replace(",", "");
						}while(amtForBal.indexOf(",")!=-1)
					}
					var amtToPayIs = parseFloat(amtToPayIs) + parseFloat(amtForBal);
				}
			}
		}

		if(document.getElementById("coPayChk")){
			if(document.getElementById("coPayChk").checked==true){
				var amtToPayIs = parseFloat(amtToPayIs)+parseFloat(c);
			}
		}
		
		var amtToPayIs = (amtToPayIs.toFixed(2));
		document.getElementById("paidAmount").value = amtToPayIs;

		var nowDueAmt = totBal - amtToPayIs;
		if(document.getElementById("coPayChk")){
			if(document.getElementById("coPayChk").checked==true){
//				var nowDueAmt = nowDueAmt - c;
//				var amtToPayIs = amtToPayIs+c;
			}
		}
		var nowDueAmt = (nowDueAmt.toFixed(2));
		//document.getElementById("amtDueTr").innerHTML = nowDueAmt;		
	}else{
		document.getElementById("coPayApplied"+s).checked = true;
		return false;
	}
}	
function expDate(id){
    id=id?id:'date2';
	var expireDate = document.getElementById(id).value;
	if(expireDate!=''){
		var strLen = expireDate.length;
		var posSlash = expireDate.indexOf("/");
		var posDash = expireDate.indexOf("-");
		if((posSlash == 2) || (posDash == 2)){
			var formatExp = true;
		}else{
			if((posSlash == 1) || (posDash == 1)){
				var expireDate = '0'+expireDate;
				
			}else{
				var formatExp = false;
			}
		}
		var mm = expireDate.substr(0,2);
		var yy = expireDate.substr(3);
		if((strLen>5) || (strLen<5) || (formatExp==false) || (mm>12) || (mm<=0) || (yy<=0)){
			if(strLen==7){
				if(formatExp==false){
					alert("Please enter date in forrmat mm/yy")
					document.getElementById(id).value = '';
				}else{
					yySess = expireDate.substr(3, 2);
					if(yySess!=20){
						alert("Please enter year correctly.")
						document.getElementById(id).value = '';
					}
				}
			}else{
				alert("Please enter date in forrmat mm/yy")
				document.getElementById(id).value = '';
			}
		}
	}
}
function selectChanges(s){
	var cptId = document.getElementById('cptIdTd'+s).value;
	var refractionChk = '<?php echo $refractionChk; ?>';
	if(cptId == '92015' && refractionChk == 'No'){	
	alert("Refraction can not be collected.")
		return false;
	}	
	document.getElementById('chkbx'+s).checked = true;
}
function paymentChange(s, wAmt){
	var cptId = document.getElementById('cptIdTd'+s).value;
	var refractionChk = '<?php echo $refractionChk; ?>';


	if(cptId == '92015' && refractionChk == 'No'){
	}else{
		var copay = 0;
		var TotalPayAmount = 0;	
		var frmEle = document.pamentFrm.elements.length;
		var frmObj = document.pamentFrm;
		for(i=0;i<frmEle;i++){
			var eleName = frmObj.elements[i].name;
			if(eleName.indexOf('payNew_Amount') != -1){
				var amtToPay = removeCommas(frmObj.elements[i].value);
				if((amtToPay=='-0.00') || (amtToPay<0)){
					frmObj.elements[i].value = '0.00';
				}
				var TotalPayAmount = TotalPayAmount + parseFloat(amtToPay);
			}
				
		}
		
		document.getElementById("paidAmount").value = TotalPayAmount.toFixed(2);
	}
}
function copay_minus_procedure(tot2){
	var arr_apply_copay_chk=new Array(99212,99213,99214,99215,99201,99202,99203,99204,99205,99241,99242,99243,99244,99245,92012,92013,92014,92002,92003,92004);
	var s=1;
	var tot2=tot2;
	if(tot2){
		for(s=1;s<=tot2;s++){
			if(document.getElementById('coPayChk')){
				if(document.getElementById('chkbx'+s)){
					if((document.getElementById('copayAmount').value>0) && (document.getElementById('chkbx'+s).checked==true) && (document.getElementById('coPayChk').checked==true || document.getElementById('coPayChk2').checked==true) && (document.getElementById('chkbx'+s).value==document.getElementById('copay_apply_procedure').value)){
						for(c=0;c<arr_apply_copay_chk.length;c++){
						//alert(document.getElementById('procActualBal'+s).innerHTML+'=='+document.getElementById('copayAmount_hid').value);
							if(document.getElementById('cptIdTd'+s).value==arr_apply_copay_chk[c]){
								if(parseFloat(document.getElementById('procActualBal'+s).innerHTML.replace(/\,/g,""))>=parseFloat(document.getElementById('copayAmount').value)){
									var new_minus_copay=parseFloat(document.getElementById('procActualBal'+s).innerHTML.replace(/\,/g,""))-parseFloat(document.getElementById('copayAmount_hid').value);
									if(document.getElementById('payNew_Amount'+s).value!=new_minus_copay){
										document.getElementById('payNew_Amount'+s).value=new_minus_copay.toFixed(2);
										var pad_amts=parseFloat(document.getElementById('paidAmount').value)-parseFloat(document.getElementById('copayAmount_hid').value);
										document.getElementById('paidAmount').value=pad_amts.toFixed(2);
									}
								}else{
									var pad_amts_less=document.getElementById('paidAmount').value-document.getElementById('payNew_Amount'+s).value;
									document.getElementById('paidAmount').value=pad_amts_less.toFixed(2);
									document.getElementById('payNew_Amount'+s).value='0.00';
									
								}	
							}else{
								if(document.getElementById('cptIdTd'+s).value==document.getElementById('copay_apply_procedure_code').value){
									if(parseFloat(document.getElementById('procActualBal'+s).innerHTML.replace(/\,/g,""))>=parseFloat(document.getElementById('copayAmount').value)){
										var new_minus_copay=parseFloat(document.getElementById('procActualBal'+s).innerHTML.replace(/\,/g,""))-parseFloat(document.getElementById('copayAmount_hid').value);
										if(document.getElementById('payNew_Amount'+s).value!=new_minus_copay){
											document.getElementById('payNew_Amount'+s).value=new_minus_copay.toFixed(2);
											var pad_amts=parseFloat(document.getElementById('paidAmount').value)-parseFloat(document.getElementById('copayAmount_hid').value);
											document.getElementById('paidAmount').value=pad_amts.toFixed(2);
										}
									}else{
										var pad_amts_less=document.getElementById('paidAmount').value-document.getElementById('payNew_Amount'+s).value;
										document.getElementById('paidAmount').value=pad_amts_less.toFixed(2);
										document.getElementById('payNew_Amount'+s).value='0.00';
										
									}	
								}	
							}	
						}
					}
				}	
			}	
		}	
	}	
}
function copay_hide(){
	if(document.getElementById('coPayChk') && document.getElementById('coPayChk')){
	}else{
		document.getElementById('copayAmount').value='0.00';
	}
}
$(document).ready(function(){
	$('.date-pick').datetimepicker({
		timepicker:false,
		format:window.opener.global_date_format, //'m-d-Y',
		formatDate:'Y-m-d'
	});
});
</script>
</head>
<body onLoad="copay_hide();">
<div class="mainwhtbox">
	<div class="row">
		<form name="pamentFrm" id="pamentFrm" action="quickPay.php" method="post">
            
            <input type="hidden" name="log_referenceNumber" id="log_referenceNumber" value="" />
            <input type="hidden" name="tsys_transaction_id" id="tsys_transaction_id" value="" />
            <input type="hidden" name="tsys_void_id" id="tsys_void_id" value="" />
            <input type="hidden" name="tsys_last_status" id="tsys_last_status" value="" />
            <input type="hidden" name="valueBtn" id="valueBtn" value="" />
            <input type="hidden" name="card_details_str_id" id="card_details_str_id" value="" />

			<input type="hidden"  name="pro_arr" id="pro_arr">	
			<div class="col-sm-12 purple_bar">
				<label>Payment</label>	
			</div>	
			<div class="col-sm-12" id="postedSuperbill">
				<div class="row pt10">
					<div class="col-sm-4">
						<label>Patient Name : </label>	<span><?php echo $patient_name; ?></span>
					</div>	
					
					<div class="col-sm-4">
						<label>Patient Id :</label>	<span><?php echo $patient_id; ?></span>
					</div>	
					
					<div class="col-sm-4">
						<label>Operator :</label> <span><?php echo $operatorName; ?></span>	
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-4">
						<label>Social Security # :</label>	<span><?php echo $ss; ?></span>
					</div>
					
					<div class="col-sm-4">
						<label>DOB :</label>	<span><?php echo date(phpDateFormat(), strtotime(str_replace('-', '/', $DOB)))." (".$age." Yrs.)"; ?></span>
					</div>
					
					<div class="col-sm-4">
						<label>Pri CoPay :</label>	<span><?php echo "$".$pri_copay; ?></span>
						<input type="hidden" id="pri_copay_chk" name="pri_copay_chk" value="<?php echo $pri_copay; ?>">	
						<?php
							$img_show=$pri_copay-$tot_paid_chk1;
							if($copay>0){
								if($coPayNotRequired == 0){
									if($copayPaid<=0 && $img_show>0){
										if($coPayWriteOff!='1'){
										?>
											<div class="checkbox checkbox-inline">		
												<input type="checkbox" id="coPayChk" name="coPayChk" value="true" checked="checked" onClick="return changeAmt(<?php echo $pri_copay; ?>);" />
												<label for="coPayChk"></label>
											</div>
										<?php
										}else{
										?>
											<div class="checkbox checkbox-inline">		
												<input type="checkbox" id="coPayChk" name="coPayChk" value="true" class="text_10" checked="checked" style="visibility:hidden;"/>
												<label for="coPayChk">Write Off</label>
											</div>	
								<?php
										}
									}else{
								?>
								<div class="checkbox checkbox-inline">
									<img src="../../library/images/confirm.gif" width="16px" /> 
									<input type="checkbox" id="coPayChk" name="coPayChk" value="true" class="text_10" checked="checked" style="visibility:hidden;"/>
									<label for="coPayChk"></label>	
								</div>	
							   
								<?php
									}
								}else{
									?>
									<div class="checkbox checkbox-inline">
										<input type="checkbox" id="coPayChk" name="coPayChk" value="true" class="text_10" checked="checked" style="visibility:hidden;"/>
										<label for="coPayChk">NR</label>	
									</div>
								<?php
								}
							}
						?>
					</div>	
				</div>	
			</div>	
			<div class="col-sm-12">
				<div class="row">
					<div class="col-sm-4">
						<label>In. Case :</label>	<span><?php echo $insTypeCode; ?></span>
					</div>	
					
					<div class="col-sm-4">
						<label>Ins. Provider :</label>	<span><?php echo $insProvider; ?></span>		
					</div>	

					<div class="col-sm-4">
						<label>Sec CoPay :</label> <span><?php echo "$".$sec_copay; ?></span>
						<input type="hidden" id="sec_copay_chk" name="sec_copay_chk" value="<?php echo $sec_copay; ?>">
						<?php
							if($sec_copay>0){
								if($coPayNotRequired2 == 0){
									if($copayPaid<=0){
										if($coPayWriteOff!='1'){
										?>
										<div class="checkbox checkbox-inline">
											<input type="checkbox" id="coPayChk2" name="coPayChk2" value="true" class="text_10" checked="checked" onClick="return changeAmt(<?php echo $sec_copay; ?>);" />
											<label for="coPayChk2"></label>
										</div>
								<?php
										}else{
										?>
										<span style="font-size:12px; font-weight:bold; color:#0000FF;">Write Off</span>
								<?php
										}
									}else{
										?>
										<img src="../../library/images/confirm.gif" width="16px" />
								<?php
									}
								}else{
									?>
								<span style="font-size:12px; font-weight:bold; color:#0000FF;">NR</span>
								<?php
								}
							}
						?>
					</div>		
				</div>	
			</div>
			<div class="row pt10">
				<table class="table table-bordered table-striped">
					<thead>
						<tr class="grythead">
							<th class="text-center" >Apply</th>
							<th class="text-center" >Procedure</th>
							<th class="text-center" >Description</th>
							<th class="text-center" >Units</th>
							<th class="text-center" >Cost</th>
							<th class="text-center text-nowrap" >Total Charge</th>
							<th class="text-center" >Amount</th>
							<th class="text-center text-nowrap" >New Balance</th>
						</tr>
					</thead>	
					<tbody>
						<?php
							$totalRefractionAmountFor = '0';
							$reflactionUnitsAmt = '0';
							$reflactionAmt = '0';
							
							$getChargesDetailsStr = "SELECT * FROM patient_charge_list
													WHERE del_status='0' and encounter_id='$encounter_id'";
							$getChargesDetailsQry = imw_query($getChargesDetailsStr);
							while($getChargesDetailsRow = imw_fetch_array($getChargesDetailsQry)){
								$chargeListId = $getChargesDetailsRow['charge_list_id'];
								$coPay = $getChargesDetailsRow['copay'];
								$coPayNotRequired = $getChargesDetailsRow['coPayNotRequired'];
								$coPayNotRequired2 = $getChargesDetailsRow['coPayNotRequired2'];
								
								if($coPayNotRequired==0){
									$pri_copay = $getCopayRow['pri_copay'];
								}else{
									$pri_copay=0;
								}
								if($coPayNotRequired2==0){
									$sec_copay = $getCopayRow['sec_copay'];
								}else{
									$sec_copay=0;
								}
								
								$coPay_chk=$pri_copay+$sec_copay;
								
								$coPayAmount = number_format($getChargesDetailsRow['copay'], 2);
								
								$coPayWriteOff = $getChargesDetailsRow['coPayWriteOff'];
								$copayPaid = $getChargesDetailsRow['copayPaid'];
								$coPayAdjusted = $getChargesDetailsRow['coPayAdjusted'];
								$refractionPaid = $getChargesDetailsRow['refractionPaid'];
								$approvedTotalAmt = number_format($getChargesDetailsRow['approvedTotalAmt'], 2);
								$deductibleTotalAmt = number_format($getChargesDetailsRow['deductibleTotalAmt'], 2);
								$totalPaidAmt = number_format($getChargesDetailsRow['amtPaid'], 2);
								$amountDue = $getChargesDetailsRow['amountDue'];
								$newAmountDue = number_format($getChargesDetailsRow['amountDue'], 2);
								$totalBalance = $getChargesDetailsRow['totalBalance'];
								$totalBalanceAmount = $getChargesDetailsRow['totalBalance'];
								$totalCreditAmounts = number_format($getChargesDetailsRow['creditAmount'], 2);
								$deductAmt = false;
								
								$proc_code_imp=get_proc_code($chargeListId);
								$copay_collect_proc=copay_apply_chk($proc_code_imp,'','');

								//------------- ASC ORDER BY CPT DESC. -------------//
								$getProcDetailsStr = "SELECT a.* FROM
													patient_charge_list_details a,
													cpt_fee_tbl b
													WHERE charge_list_id = '$chargeListId'
													AND a.procCode = b.cpt_fee_id and a.del_status='0'
													ORDER BY a.display_order,a.charge_list_detail_id asc,b.cpt_desc ASC";
								//------------- ASC ORDER BY CPT DESC. -------------//
								$getProcDetailsQry = imw_query($getProcDetailsStr);
								$getProcCountRows = imw_num_rows($getProcDetailsQry);
								while($getProcDetailsRows = imw_fetch_array($getProcDetailsQry)){
									$charge_list_detail_id = $getProcDetailsRows['charge_list_detail_id'];
									$paidStatus = $getProcDetailsRows['paidStatus'];
									$procId = $getProcDetailsRows['procCode'];
									$units = $getProcDetailsRows['units'];
									$procCharges = number_format($getProcDetailsRows['procCharges'], 2);
									$totalAmount = number_format($getProcDetailsRows['totalAmount'], 2);
									$paidForProc = number_format($getProcDetailsRows['paidForProc'], 2);
									$balForProc = number_format($getProcDetailsRows['balForProc'], 2);
									$newBalance = number_format($getProcDetailsRows['newBalance'], 2);
									$procActualBalance = number_format($getProcDetailsRows['newBalance'], 2);
									$coPayAdjustedAmount = $getProcDetailsRows['coPayAdjustedAmount'];
									
									$approvedAmt = $getProcDetailsRows['approvedAmt'];
									$deductAmt = $getProcDetailsRows['deductAmt'];
									$creditAmount = $getProcDetailsRows['creditProcAmount'];
										
									$getCptFeeDetailsStr = "SELECT * FROM cpt_fee_tbl WHERE cpt_fee_id='$procId'";
									$getCptFeeDetailsQry = imw_query($getCptFeeDetailsStr);
									$getCptFeeDetailsRow = imw_fetch_array($getCptFeeDetailsQry);
									$cptPracCode = $getCptFeeDetailsRow['cpt_prac_code'];
									$cpt4_code = $getCptFeeDetailsRow['cpt4_code'];
									
									$copay_collect_chk=copay_apply_chk($cpt4_code,'','');
									//-------------------- TESTING CPT CODE IS E&M CODE OR NOT --------------------//
									if(($coPay>0) && ($copayPaid==1) && ($coPayAdjusted==0)){
										if(($newBalance>0)){
											if($newBalance>$copay){
												//$newBalance = $newBalance - $copay;
												$copay = 0;
											}else{
												$copay = $copay - $newBalance;
												//$newBalance = 0;
												$copay = 0;
											}
											$newBalance = number_format($newBalance, 2);
											$totalBalance = $totalBalanceAmount - $coPayAmount;
										}
									}
									if($procActualBalance!=$newBalance){
										$coPayApplyChk = true;
									}else{
										$coPayApplyChk = false;
									}
									if(($coPay>0) && ($copayPaid==0)){
										$coPayApplyChk = true;
									}
									//-------------------- TESTING CPT CODE IS E&M CODE OR NOT --------------------//
										
									$cptDesc = $getCptFeeDetailsRow['cpt_desc'];
									$totalNetBalance = $totalBalance;

									if($cptPracCode == '92015' && $refractionChk != 'No'){
										$totalRefractionAmountFor = $totalRefractionAmountFor + $approvedAmt;
										$reflactionUnitsAmt = $reflactionUnitsAmt + $approvedAmt;
										$reflactionAmt = $reflactionAmt + $newBalance;
									}

									$newBalanceAmt = $balForProc;
									if($balForProc>0){
										$balanceAmount = $newBalance;
									}else{
										$balanceAmount = 0;
									}
									++$seq;
									++$seqForCopay;
									?>
							<tr>
								<td class="text-center">
									<?php 
									 if($cpt_onetime==0){
										$cpt_onetime=0;
										 $copay_collect=copay_apply_chk($cptPracCode,'','');
										 if($copay_collect_proc[0]==true && $copay_collect_proc[1]==true){
											if($copay_collect[0]==true && $copay_collect[1]==true){
									?>
											<input type="hidden" value="<?php echo $charge_list_detail_id; ?>" name="copay_apply_procedure" id="copay_apply_procedure">
											<input type="hidden" value="<?php echo $cpt4_code; ?>" name="copay_apply_procedure_code" id="copay_apply_procedure_code">
									<?php 
											$cpt_onetime++;
											}
										}else{
											if($balForProc>0){
									?>	
											<input type="hidden" value="<?php echo $charge_list_detail_id; ?>" name="copay_apply_procedure" id="copay_apply_procedure">
											<input type="hidden" value="<?php echo $cpt4_code; ?>" name="copay_apply_procedure_code" id="copay_apply_procedure_code">
									<?php	
												$cpt_onetime++;
												}
											}
											
										}
									?>
									<?php if($procActualBalance>0) {?>
									<input type="hidden" name="chk_count_for_copay" id="chk_count_for_copay" value="<?php echo $getProcCountRows; ?>">
									<div class="checkbox checkbox-inline">
										<input type="checkbox" <?php if($cptPracCode=="92015" && $refractionChk == 'yes') echo "Checked"; ?> value="<?php echo $charge_list_detail_id; ?>" id="chkbx<?php echo $seq; ?>" name="chkbx[]" onClick="return payAmtChk(<?php echo $seq; ?>, '<?php echo $cptPracCode; ?>', '<?php echo $getProcCountRows; ?>');" />	
										<label for="chkbx<?php echo $seq; ?>"></label>
									</div>										
								  <?php }else{ ?><img src="../../library/images/confirm.gif" width="16px" /><?php } ?></td>
								<td class="text-left">
									<table>
										<tr>
											<td style="border:none;">
												 <?php if($coPayAdjustedAmount==1){ ?><img src="../../library/images/confirm.gif" width="16px" align="middle"/><?php } ?>
											</td>
											<td class="text-left" >
												<?php echo $cptPracCode; ?>
												<input type="hidden" value="<?php echo $cptPracCode; ?>" id="cptIdTd<?php echo $seq; ?>" name="cptIdTd<?php echo $seq; ?>">
											</td>

										</tr>
									</table>
								</td>
								<td class="text-left"><?php echo $cptDesc; ?></td>
								<td class="text-center"><?php echo $units; ?></td>
								<td class="text-right"><?php echo "$".$procCharges; ?></td>
								<td class="text-right"><?php echo "$".$totalAmount; ?>
									<span style=" vertical-align:middle; display:none;" class="text-right" id="totalFee<?php echo $seq; ?>"><?php echo $newBalance; ?></span>
								</td>
								<td class="text-right"> 
									
									<div class="input-group">
										<div class="input-group-addon"><span class="">$</span></div>	
										<input  name="payNew_Amount<?php echo $seq; ?>" id="payNew_Amount<?php echo $seq; ?>" type="text" value="<?php echo "0.00"; ?>" class="form-control" <?php if($newBalance==0){ echo "readonly"; }else{ ?>  onBlur="return selectChanges('<?php echo $seq; ?>')" onChange="return paymentChange('<?php echo $seq; ?>', this.value)" <?php } ?> style="width:90px;" />
									</div>
								</td>
								<td class="text-right">
                                	<span>$</span><span id="procActualBal<?php echo $seq; ?>" class=""><?php echo $newBalance; ?></span>
								</td>
							</tr>
								<?php
								}
							}

							//========================= =================================//
							if($copay>0){
								if(($coPayNotRequired==0 || $coPayNotRequired2==0)&& ($coPayWriteOff!='1')){
									if($copayPaid<=0){
										$amount = $copay;
									}
								}
							}
							if($reflactionAmt>0){
								$amount = $amount + $reflactionAmt;
								$totalBalance = $totalBalance - $reflactionAmt;
							}
							$amount = number_format($amount, 2);
							$totalBalance = number_format($totalBalance, 2);
							//========================= =================================//
							?>
							<tr style="display:none;">
								<th  colspan="7" class="text-right">
									<input type="hidden" value="<?php echo $totalRefractionAmountFor; ?>" name="totalRefractionAmountFor" id="totalRefractionAmountFor">
									<input type="hidden" value="<?php echo $seqForCopay; ?>" name="seqForCopay" id="seqForCopay">
									 Total Cost of  Procedures:
								</th>
								<td style="vertical-align:middle;" class="text-right"><?php echo "$".$totalAmountCharge; ?></td>
							</tr>
							<tr>
							  <th  colspan="7" class="text-right">Total Approved Charges:</th>
							  <td style=" vertical-align:middle;"  class="text-right"><?php echo "$".$approvedTotalAmt; ?></td>
						  </tr>
							<?php
							if($deductibleTotalAmt>0){
								?>
								<tr>
								  <th  colspan="7" class="text-right">Total Deductible Charges:</th>
								  <td style=" vertical-align:middle;"class="text-right"><?php echo "$".$deductibleTotalAmt; ?></td>
								</tr>
								<?php
							}
							if($totalCreditAmounts>0){
								?>
								<tr>
									<th  colspan="7" class="text-right">Credit Amoumt: </th>
									<td style="vertical-align:middle;" class="text-right"><?php echo "$".$totalCreditAmounts; ?></td>
								</tr>
								<?php
							}
							?>
							<tr>
								<th colspan="7" class="text-right">Total Procedures Paid Previously:</th>
								<td style="vertical-align:middle;" class="text-right" id="totalAmt"><?php if($totalPaidAmt=="") echo "$"."0.00"; else echo "$".$totalPaidAmt; ?></td>
							</tr>
							<?php
							if($coPayPaid!=1){
							?>
							<tr id="copayAppliedRow" style="display:none;">
								<th colspan="7" class="text-right">CoPay Applied amount:</th>
								<td style=" vertical-align:middle;" class="text-right"><?php echo "$".$copay; ?></td>
							</tr>
							<?php
							}
							?>
							<tr>
								<th colspan="7" class="text-right">New Balance:</th>
								<td style="vertical-align:middle;" class="text-right" id="payAmt"><?php echo "$".number_format($totalBalanceAmount, 2); ?></td>
							</tr>
					</tbody>	
				</table>	
			</div>
			<div class="row">
					<input type="hidden" name="amountDue" id="amountDue" value="<?php echo $amountDue; ?>" />
					<input type="hidden" name="copaySt" id="copaySt" value="<?php echo $getRowValidation; ?>" />
					<input type="hidden" name="eId" id="eId" value="<?php echo $encounter_id; ?>" />
					<div class="col-sm-2">
						<label>Amount Due:</label>
						<div class="input-group">
							<div class="input-group-addon"><span>$</span></div>	
							<span id="amtDueTr" class="form-control"><?php echo $totalBalance; ?></span>
						</div>	
					</div>	
					
					<div class="col-sm-2">
						<label>Amount :</label>
						<div class="input-group">
							<div class="input-group-addon"><span>$</span></div>
							<input readonly name="paidAmount" id="paidAmount" type="text" value="<?php echo '0.00'; ?>" class="form-control" />
						</div>	
					</div>	
					
					<div class="col-sm-2">
						<label>Copay :</label>
						<div class="input-group">
							<input  name="copayAmount_hid" id="copayAmount_hid" type="hidden" value="<?php echo number_format(($coPay_chk-$tot_paid_chk1),2); ?>" />
							<div class="input-group-addon"><span>$</span></div>
							<input <?php if($coPayAdjustedAmount==1){ echo "readonly";} ?> name="copayAmount" id="copayAmount" type="text" value="<?php echo number_format(($coPay_chk-$tot_paid_chk1),2); ?>" class="form-control" />	
						</div>	
					</div>	
					
					<div class="col-sm-2">
						<label>Paid Date :</label>
						<div class="input-group">
							<input id="date1" type="text"  name="paidDate" onChange="checkdate(this);" value="<?php echo date(phpDateFormat(), strtotime(str_replace('-', '/', $todate))); ?>" size='13' maxlength="10" class="date-pick form-control" />
							<label class="input-group-addon" for="date1"><span class="glyphicon glyphicon-calendar"></span></label>	
						</div>	
					</div>
                    
                    <div class="col-sm-2">
						<label>Billing Facility :</label>
						<div class="input-group">
							<select name="facility_id" id="facility_id" class="selectpicker" data-width="155px">
                                <option value="">Billing Facility</option>
                                <?php
                                    $selQry = "select * from facility order by name ASC";
									$res = imw_query($selQry);
									while($row = imw_fetch_array($res)){
										$id = $row['id'];
										$sel = $_SESSION['login_facility'] == $id ? 'selected="selected"': '';
										print '<option '.$sel.' value="'.$id.'">'.$row['name'].'</option>';
									}
                                ?>
                            </select>
						</div>	
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-2 pt10">
						<label>Who Paid :</label>
						<select name="paidBy" id="paidBy" class="selectpicker show-menu-arrow" data-width="100%" onChange="return showInsCoList();">
							<option value="Patient">Patient</option>
							<option value="Res. Party">Res. Party</option>
							<?php
							if(($primaryInsProviderId) || ($secondaryInsProviderId) || ($tertiaryInsProviderId)){

								?>
								<option value="Insurance">Insurance</option>
								<?php
							}
							?>
						</select>	
					</div>	
					
					<div id="insCoNames" class="col-sm-2 pt10" style="display:none;">
						<label>Ins. Pr. : <input type="hidden" name="insSelected" id="insSelected" value=""></label>
						<select name="insProviderName" id="insProviderName" class="selectpicker show-menu-arrow" data-width="100%">
							<option value=""></option>
							<?php
							foreach($insProvidersNameArr as $key => $insCoName){
								if($key==0) $value = ' (Primary)';
								if($key==1) $value = ' (Secondary)';
								if($key==2) $value = ' (Tertiary)';
								if($insCoName=='') $insCoName = 'N/A';
									?>
									<option value="<?php echo $insCoName; ?>"><?php echo $insCoName." $value"; ?></option>
									<?php
							}
							?>
						</select>
					</div>

					<div class="col-sm-2 pt10">
						<label>Method :</label>
						<select name="paymentMode" id="paymentMode" class="selectpicker show-menu-arrow" data-width="100%" onChange="return showRow();">
							<?php foreach($payment_method_arr as $method_key=>$method_val){?>
								<option value="<?php echo $method_val; ?>" <?php if(strtolower($method_val)=="cash") echo 'SELECTED'; ?>><?php echo $method_val; ?></option>
							<?php }?>
						</select>
					</div>
                   
					
					<div class="col-sm-6 pt10">
						<div class="row">
							<div id="checkRow" style="display:none;" class="col-sm-5">
								<label>Check # :</label>
								<input name="checkNo" id="checkNo" type="text" class="form-control" value="" />								
							</div>	
							<div class="col-sm-12" id="creditCardRow" style="display:none;">
								<div class="row">
									<div class="col-sm-5" >
										<label>CC Type.:</label>
										<span id="creditCardCoTd">
											<select name="creditCardCo" id="creditCardCo" class="selectpicker show-menu-arrow" data-width="100%"  onChange="return cCCompany();">
												<option value=""></option>
												<option value="AX">American Express</option>
												<option value="Care Credit">Care Credit</option>
												<option value="Dis">Discover</option>
												<option value="MC">Master Card</option>
												<option value="Visa">Visa</option>
												<option value="Others">Others</option>
											</select>
										</span>
									</div>	
									<div class="col-sm-4">
										<label>CC #:</label>
										<input name="cCNo" id="cCNo" type="text" class="form-control" value="" />	
									</div>
										
									<div class="col-sm-3">
										<label>Exp. Date:</label>	
										<input id="date2" type="text"  name="expireDate" onBlur="return expDate();" value="" maxlength="10" class="form-control" />	
									</div>	
								</div>	
							</div>	
						</div>	
					</div>
                    
                     <?php if($pos_device) { ?>
                        <div class="row">
                            <div class="col-sm-8">
                            <?php 
                                $laneID='10000004';
                                $cc_class='acc_payment';
                                $target_ids='paymentMode';
                                include 'pos/include_cc_payment.php';
                            ?>
                            </div>
                        </div>
                    <?php } ?>
                    
				</div>
			<div class="col-sm-12 pt10 text-center" id="module_buttons">
				<input type="hidden" name="applySubmit" id="applySubmit" value="">
				<input type="hidden" name="applyReceiptSubmit" id="applyReceiptSubmit" value="">
				<input type="button" name="applySubmitBtn" value="Done" class="btn btn-success" id="applySubmitBtn" onClick="return check(<?php echo $totalAmountCharge; ?>, <?php echo $amountDue; ?>, <?php echo $copay; ?>, '<?php echo $getProcCountRows; ?>', 'applySubmitBtn', 'applyReceiptSubmitBtn', 'applySubmit')" />
				<input type="button" name="applyReceiptSubmitBtn" value="Done & Print Receipt" class="btn btn-success" id="applyReceiptSubmitBtn" onClick="return check(<?php echo $totalAmountCharge; ?>, <?php echo $amountDue; ?>, <?php echo $copay; ?>, <?php echo $getProcCountRows; ?>, 'applyReceiptSubmitBtn', 'applySubmitBtn', 'applyReceiptSubmit')" />
				<input type="button" name="printReceiptSubmit" value="Print Receipt" class="btn btn-success" id="printReceiptSubmit"  onClick="return printReceipt(<?php echo $encounter_id; ?>);" />
                <?php if($pos_device) { ?>
                    <button type="button" name="pos_log" value="pos_log" class="btn btn-primary" onClick="window.show_transaction_popup();">POS Log</button>
                <?php } ?>
				<input type="button" name="cancel" value="Cancel" class="btn btn-danger" id="cancel"  onClick="return closeMe();" />	
			</div>	
		</form>	
	</div>
</div>
        
        
<div id="div_loading_image" class="text-center" style="z-index:9999;display:none;">
    <div class="loading_container">
        <div class="process_loader"></div>
        <div id="div_loading_text" class="text-info"></div>
    </div>
</div>
        
</body>
</html>