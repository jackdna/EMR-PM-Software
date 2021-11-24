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
$patient_id = $_SESSION['patient'];
$operatorName = $_SESSION['authUser'];

include_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");

$patientAmount = $approvedTotalAmt = $totalEncounterAmt = $deductTotalAmt = $paidTotal = 0;
$amountTotalDue = $patientPaidAmount = $patientDues = $overPaymentForProc = 0;
$insAmount = $insPaidAmount = $insDues = $newTotalBalance = $referactionPaid = $crdamt= 0;

// ADJUSTING ALL AMOUNTS
if($encounter_id_arr<>""){
	$encounter_id_arr_imp = implode(',',$encounter_id_arr);	
	$enc_whr = "encounter_id in($encounter_id_arr_imp)";
	$enc_blank = "1";
}else{
	$enc_whr = "encounter_id = '$encounter_id'";
}
$getEncountersDetailsStr = "SELECT * FROM patient_charge_list
									WHERE del_status='0' and $enc_whr";
$getEncountersDetailsQry = imw_query($getEncountersDetailsStr);

while($getEncountersDetailsRow = imw_fetch_array($getEncountersDetailsQry)){

	$patientAmount = $approvedTotalAmt = $totalEncounterAmt = $deductTotalAmt = $paidTotal = 0;
	$amountTotalDue = $patientPaidAmount = $patientDues = $overPaymentForProc = 0;
	$insAmount = $insPaidAmount = $insDues = $newTotalBalance = $referactionPaid = $crdamt= 0;

	$charge_list_id = $getEncountersDetailsRow['charge_list_id'];
	$encounter_id = $getEncountersDetailsRow['encounter_id'];
	$copay = $getEncountersDetailsRow['copay'];
	$copayPaid = $getEncountersDetailsRow['copayPaid'];
	$coPayNotRequired = $getEncountersDetailsRow['coPayNotRequired'];
	$coPayWriteOff = $getEncountersDetailsRow['coPayWriteOff'];
	$coPayAdjusted = $getEncountersDetailsRow['coPayAdjusted'];
	$primaryInsuranceCoId = $getEncountersDetailsRow['primaryInsuranceCoId'];
	$secondaryInsuranceCoId = $getEncountersDetailsRow['secondaryInsuranceCoId'];
	$tertiaryInsuranceCoId = $getEncountersDetailsRow['tertiaryInsuranceCoId'];
	$statement_count_chk = $getEncountersDetailsRow['statement_count'];
	$patientDues=0;
	$insDues=0;
	$getProcDetailsStr =  "SELECT * FROM patient_charge_list_details
								WHERE del_status='0' and charge_list_id = '$charge_list_id'";
	$getProcDetailsQry = imw_query($getProcDetailsStr);
	while($getProcDetailsRows = imw_fetch_array($getProcDetailsQry)){				
		$charge_list_detail_id = $getProcDetailsRows['charge_list_detail_id'];
		$procCode = $getProcDetailsRows['procCode'];
		
		$patientDues = $patientDues+$getProcDetailsRows['pat_due'];
		$insDues = $insDues+$getProcDetailsRows['pri_due']+$getProcDetailsRows['sec_due']+$getProcDetailsRows['tri_due'];	
		
			//GET REFRACTION OR NOT
			$gettingProcRefractionStr = "SELECT * FROM cpt_fee_tbl WHERE cpt_fee_id = '$procCode' AND delete_status = '0'";
			$gettingProcRefractionQry = imw_query($gettingProcRefractionStr);
			$gettingProcRefractionRow = imw_fetch_array($gettingProcRefractionQry);
			$cpt4_code = $gettingProcRefractionRow['cpt4_code'];
			//GET REFRACTION OR NOT
		$totalAmount = $getProcDetailsRows['totalAmount'];
			$totalEncounterAmt = $totalEncounterAmt + $totalAmount;
		$paidForProc = $getProcDetailsRows['paidForProc'];
			$paidTotal = $paidTotal + $paidForProc;
		$balForProc = $getProcDetailsRows['balForProc'];
		$approvedAmt = $getProcDetailsRows['approvedAmt'];
			$approvedTotalAmt = $approvedTotalAmt + $approvedAmt;
		$deductAmt = $getProcDetailsRows['deductAmt'];
			$deductTotalAmt = $deductTotalAmt + $deductAmt;
		$newBalance = $getProcDetailsRows['newBalance'];
			$newTotalBalance = $newTotalBalance + $newBalance;
			$amountTotalDue = $amountTotalDue + $newBalance;
		$creditProcAmount = $getProcDetailsRows['creditProcAmount'];
		$crdamt = $creditProcAmount+$crdamt;
		$coPayAdjustedAmount = $getProcDetailsRows['coPayAdjustedAmount'];
		$overPaymentForProc = $overPaymentForProc + $getProcDetailsRows['overPaymentForProc'];
		if($overPaymentForProc>$amountTotalDue){
			$overPaymentForProc_final=$overPaymentForProc-$amountTotalDue;
			$newTotalBalance_final=0;
		}else{
			$overPaymentForProc_final=0;
			$newTotalBalance_final=$newTotalBalance-$overPaymentForProc;
		}
		
		if($primaryInsuranceCoId==0){
			$patientAmount = $patientAmount + $approvedAmt;
			$patientPaidAmount = $patientPaidAmount + $paidForProc;
			//$patientDues = $patientDues + $newBalance;					
		}else{
			if($cpt4_code==92015){
				$patientAmount = $patientAmount + $approvedAmt;
				if($newBalance<=0) $referactionPaid = 1;
				//$patientDues = $patientDues + $newBalance;
			}else{
				$insAmount = $insAmount + $approvedAmt;
				//$insDues = $insDues + $newBalance;						
			}
		}
	}
	
	$patientPaidAmount = $patientAmount - $patientDues;
	//if($_REQUEST['paidBy']=='Insurance'){
		$insPaidAmount = $insAmount - $insDues;
	//}
	if($copay){
		if(($coPayNotRequired==0) && ($coPayWriteOff!=1)){
			if($copayPaid==1){
				$patientAmount = $patientAmount + $copay;
				$patientPaidAmount = $patientPaidAmount + $copay;						
			}else{
				$patientAmount = $patientAmount + $copay;
				//$patientDues = $patientDues + $copay;
			}
		}
	}
	
	$updatepatientAmtStr = "UPDATE patient_charge_list SET
							referactionPaid = '$referactionPaid',
							totalAmt = '$totalEncounterAmt',
							approvedTotalAmt = '$approvedTotalAmt',
							deductibleTotalAmt = '$deductTotalAmt',
							amtPaid = '$paidTotal',
							amountDue = '$amountTotalDue',
							patientAmt = '$patientAmount',
							patientPaidAmt = '$patientPaidAmount',
							patientDue = '$patientDues',
							insAmt = '$insAmount',
							insPaidAmt = '$insPaidAmount',
							insuranceDue = '$insDues',
							totalBalance = '$newTotalBalance_final',
							creditAmount ='$crdamt',
							overPayment = '$overPaymentForProc_final'";									
	if($statement_count>0 && $statement_count_chk>0){
		$updatepatientAmtStr .= ", statement_count = '$statement_count'";
	}										
	$updatepatientAmtStr .= " WHERE encounter_id = '$encounter_id'";
	$updatepatientAmtQry = imw_query($updatepatientAmtStr);
	patient_bal_update($encounter_id);
	patient_proc_bal_update($encounter_id);
	updateEncounterFlags($encounter_id,$stop_clm_status);
}	
if($enc_blank<>""){
	$encounter_id="";
}
//------------------- ADJUSTING ALL AMOUNTS -------------------//
?>