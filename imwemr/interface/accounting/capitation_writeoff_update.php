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
if($encounter_id>0){
	$get_chl_detail = imw_query("SELECT charge_list_id,primaryInsuranceCoId,secondaryInsuranceCoId,tertiaryInsuranceCoId,encounter_id,primaryProviderId,
	copay,copayPaid,coPayNotRequired
	FROM patient_charge_list WHERE  del_status='0' and patient_id='$patient_id' and encounter_id='$encounter_id'");
	while($get_chl_rows = imw_fetch_array($get_chl_detail)){
		$charge_list_id=$get_chl_rows['charge_list_id'];
		$primaryInsuranceCoId=$get_chl_rows['primaryInsuranceCoId'];
		$secondaryInsuranceCoId=$get_chl_rows['secondaryInsuranceCoId'];
		$tertiaryInsuranceCoId=$get_chl_rows['tertiaryInsuranceCoId'];
		$primaryProviderId=$get_chl_rows['primaryProviderId'];
		$encounter_id=$get_chl_rows['encounter_id'];
		$copay=$get_chl_rows['copay'];
		$write_off_by=$primaryInsuranceCoId;
		if($post_for == "2"){
			$write_off_by=$secondaryInsuranceCoId;
		}
		if($post_for == "3"){
			$write_off_by=$tertiaryInsuranceCoId;
		}
		
		$ins_qry=imw_query("select id,cap_cpt_code,cap_user,cap_wrt_code,capitation from insurance_companies where id=$write_off_by");
		$ins_row = imw_fetch_array($ins_qry);
		
		$cap_cpt_code_exp=explode(',',$ins_row['cap_cpt_code']);
		$cap_user_exp=explode(',',$ins_row['cap_user']);
		
		if($ins_row['capitation']>0 && !in_array($primaryProviderId,$cap_user_exp)){
			$write_off_code_id=$ins_row['cap_wrt_code'];
			$copay_cont=0;
			$getChldDetailsStr = imw_query("SELECT units,procCharges,totalAmount,paidForProc,balForProc,approvedAmt,newBalance,procCode,charge_list_detail_id
							 			FROM patient_charge_list_details WHERE del_status='0' and charge_list_id='$charge_list_id' and newBalance>0 and proc_selfpay='0'");
			while($getChldDetailsRows = imw_fetch_array($getChldDetailsStr)){
				$charge_list_detail_id=$getChldDetailsRows['charge_list_detail_id'];
				$copay_cont++;
				if(!in_array($getChldDetailsRows['procCode'],$cap_cpt_code_exp)){
					if($get_chl_rows['coPayNotRequired']==0 && $get_chl_rows['copayPaid']==0 && $copay>0){
						if($copay_cont<=imw_num_rows($getChldDetailsStr)){
							if($getChldDetailsRows['newBalance']>=$copay){
								$minus_copay=$copay;
								$copay_cont=imw_num_rows($getChldDetailsStr);
							}else{
								$minus_copay=0;
							}
						}else{
							$minus_copay=0;
						}
					}else{
						$minus_copay=0;
					}
					
					$write_off_amt=$getChldDetailsRows['newBalance']-$minus_copay;
					if($getChldDetailsRows['newBalance']>0 && $write_off_amt>0){
						$chld_update=imw_query("update patient_charge_list_details set write_off='$write_off_amt',write_off_by='$write_off_by', write_off_date='".date('Y-m-d')."',
								write_off_dot='".date('Y-m-d')."',write_off_code_id='$write_off_code_id',newBalance=newBalance-$write_off_amt,
								balForProc=balForProc-$write_off_amt,write_off_opr_id='$operatorId' where charge_list_detail_id='$charge_list_detail_id'");
						
						$insertWriteOffStr = imw_query( "INSERT INTO paymentswriteoff SET patient_id = '$patient_id',encounter_id = '$encounter_id',charge_list_detail_id = '$charge_list_detail_id',
						write_off_by_id='$write_off_by',write_off_amount = '$write_off_amt',write_off_operator_id = '$operatorId',write_off_date = '".date('Y-m-d')."',paymentStatus = 'Write Off',
						write_off_code_id='$write_off_code_id',entered_date='".date('Y-m-d H:i:s')."'");
					}
				}
			}
			$encounter_id = $encounter_id;
			include"manageEncounterAmounts.php";
		}
		
	}
}
