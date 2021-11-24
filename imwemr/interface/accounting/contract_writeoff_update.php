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

$oper=$_SESSION['authId'];
$operatorId = $_SESSION['authUserID'];
$cptPracCode_arr = array();
$getCptFeeDetailsStr = "SELECT cpt_prac_code,cpt_fee_id FROM cpt_fee_tbl";
$getCptFeeDetailsQry = imw_query($getCptFeeDetailsStr);
while($getCptFeeDetailsRow = imw_fetch_array($getCptFeeDetailsQry)){
	$cptPracCode_arr[$getCptFeeDetailsRow['cpt_fee_id']] = $getCptFeeDetailsRow['cpt_prac_code'];
}
if($encounter_id){
	$whr_enc=" and encounter_id='$encounter_id'";
}
if($pol_auto_pt_balance>0 && $encounter_id>0){
	$get_chl_detail = imw_query("SELECT charge_list_id,primaryInsuranceCoId,secondaryInsuranceCoId,tertiaryInsuranceCoId,encounter_id FROM patient_charge_list
								WHERE  del_status='0' and patient_id='$patient_id' and totalAmt=totalBalance $whr_enc");
	while($get_chl_rows = imw_fetch_array($get_chl_detail)){
		$charge_list_id=$get_chl_rows['charge_list_id'];
		$primaryInsuranceCoId=$get_chl_rows['primaryInsuranceCoId'];
		$secondaryInsuranceCoId=$get_chl_rows['secondaryInsuranceCoId'];
		$tertiaryInsuranceCoId=$get_chl_rows['tertiaryInsuranceCoId'];
		$encounter_id=$get_chl_rows['encounter_id'];
		$write_off_by=$primaryInsuranceCoId;
		if($post_for == "2"){
			$write_off_by=$secondaryInsuranceCoId;
		}
		if($post_for == "3"){
			$write_off_by=$tertiaryInsuranceCoId;
		}
		$getChldDetailsStr = imw_query("SELECT units,procCharges,totalAmount,paidForProc,balForProc,approvedAmt,newBalance,procCode,charge_list_detail_id
							 			FROM patient_charge_list_details WHERE del_status='0' and charge_list_id='$charge_list_id'");
		while($getChldDetailsRows = imw_fetch_array($getChldDetailsStr)){
			$charge_list_detail_id=$getChldDetailsRows['charge_list_detail_id'];
			$units=$getChldDetailsRows['units'];
			$procCharges=$getChldDetailsRows['procCharges'];
			$totalAmount=$getChldDetailsRows['totalAmount'];
			$paidForProc=$getChldDetailsRows['paidForProc'];
			$balForProc=$getChldDetailsRows['balForProc'];
			$approvedAmt=$getChldDetailsRows['approvedAmt'];
			$newBalance=$getChldDetailsRows['newBalance'];
			$procCode=$getChldDetailsRows['procCode'];
			$cptPracCode=$cptPracCode_arr[$procCode];
	
			$contract_fee=getContractFee($cptPracCode,$primaryInsuranceCoId);
			$contract_fee_unit=$contract_fee*$units;
			if($contract_fee_unit>0){
				if($totalAmount>$contract_fee_unit && $approvedAmt>$contract_fee_unit){
					$write_off_amt=$totalAmount-$contract_fee_unit;
					$write_off_date=date('Y-m-d');
					$write_off_dot=date('Y-m-d');
					
					if($write_off_amt>0){
						$write_off_code_id=$pol_write_off_code;
					}else{
						$write_off_code_id = "";
					}
					if($newBalance>=$write_off_amt){
						$new_balance=$newBalance-$write_off_amt;
						$ovr_amt=0;
					}else{
						$new_balance=0;
						$ovr_amt=$write_off_amt-$newBalance;
					}
					if($ovr_amt>0){
						$paid_proc=$paidForProc-$ovr_amt;
					}else{
						$paid_proc=$paidForProc;
					}
					$chld_update="update patient_charge_list_details set write_off='$write_off_amt',write_off_by='$write_off_by', write_off_date='$write_off_date',
							write_off_dot='$write_off_dot',write_off_code_id='$write_off_code_id',approvedAmt='$contract_fee_unit',newBalance='$new_balance',
							balForProc='$new_balance',write_off_opr_id='$operatorId' where charge_list_detail_id='$charge_list_detail_id'";
							//,paidForProc='$paid_proc',overPaymentForProc=overPaymentForProc+$ovr_amt
					$up_chld=imw_query($chld_update);	
					
					$qry1=imw_query("Insert INTO defaultwriteoff SET patient_id='".$patient_id."',encounter_id='".$encounter_id."',charge_list_id='".$charge_list_id."',
					charge_list_detail_id='".$charge_list_detail_id."',write_off_amount='".$write_off_amt."',write_off_operator_id='".$operatorId."',
					write_off_dop='".$write_off_date."',write_off_dot='".date('Y-m-d H:i:s')."',write_off_code_id='".$write_off_code_id."'");
				}
			}
		}
							
		$encounter_id = $encounter_id;
		//set_due_by_posted($encounter_id,$post_for);
		include"manageEncounterAmounts.php";
	}
}
