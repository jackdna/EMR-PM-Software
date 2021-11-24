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
$ret_paidDate=$paid_date;
$operator_id = $_SESSION['authUserID'];
$del_time = date('H:i:s');
$ret_entered_date = date('Y-m-d H:i:s');
if($enc_ret_chk_arr){
	$enc_return_imp=implode(',',$enc_ret_chk_arr);
	$whr_enc="a.encounter_id in($enc_return_imp)";
}else{
	$whr_enc="a.encounter_id = '$encounter_id'";	
}
$get_checkno = "SELECT a.checkNo,a.paid_by,b.payment_details_id,
	b.payment_id,b.charge_list_detail_id,a.encounter_id
	 FROM 
	patient_chargesheet_payment_info a,
	patient_charges_detail_payment_info b
	WHERE $whr_enc
	AND a.payment_id = b.payment_id
	AND b.deletePayment='0'
	AND a.checkNo='$check_no'
	ORDER BY a.payment_id DESC";
$get_checkno_qry = imw_query($get_checkno);
if(imw_num_rows($get_checkno_qry)>0){
	while($get_checkno_fet = imw_fetch_array($get_checkno_qry)){
		$charge_list_detail_id=$get_checkno_fet['charge_list_detail_id'];
		$payDetailId=$get_checkno_fet['payment_details_id'];
		$payIdDel=$get_checkno_fet['payment_id'];
		$encounter_id=$get_checkno_fet['encounter_id'];
		$paid_by=$get_checkno_fet['paid_by'];
		
		$deleteDate = date('Y-m-d');
			
		$getPaymentDetails = getRecords('patient_charges_detail_payment_info', 'payment_details_id', $payDetailId);
		if($getPaymentDetails){
			extract($getPaymentDetails);
			$totalPayment = $overPayment + $paidForProc;
			// UPDATE PATIENT CHARGE LIST DETAIL TABLE
			$coPay_update="";
			$copay_chk="";
			if($charge_list_detail_id>0){
				$getChargeDetailsQry = imw_query("select * from patient_charge_list_details where del_status='0' and charge_list_detail_id = '".$charge_list_detail_id."'");
				$getChargeDetails = imw_fetch_object($getChargeDetailsQry);
			}else{
				if($encounter_id>0){
					$sel_chld_enc=imw_query("select b.charge_list_detail_id from
					patient_charge_list as a,
					patient_charge_list_details as b
					where
					b.del_status='0'
					and a.charge_list_id = b.charge_list_id
					and a.encounter_id = '$encounter_id'
					and coPayAdjustedAmount >0");
					$fet_chld_enc=imw_fetch_array($sel_chld_enc);
					$charge_list_detail_id=$fet_chld_enc['charge_list_detail_id'];
					$copay_chk=$charge_list_detail_id;
					$getChargeDetailsQry = imw_query("select * from patient_charge_list_details where del_status='0' and charge_list_detail_id = '".$charge_list_detail_id."'");
					$getChargeDetails = imw_fetch_object($getChargeDetailsQry);
					$coPay_update="coPayAdjustedAmount='0' ,";
					
					$updateChargeListstr= "UPDATE patient_charge_list  SET
											copayPaid = 0, coPayPaidDate = '0000-00-00',
											coPayAdjusted = '', coPayAdjustedDate = ''
											WHERE encounter_id = '$encounter_id'";
					$updateChargeListQry = imw_query($updateChargeListstr);	
				}
			}
			if(count($getChargeDetails)>0){
				$procPaid = $getChargeDetails->paidForProc;
				$procPaid_chk = $getChargeDetails->paidForProc;
				$balForProc = $getChargeDetails->balForProc;
				$newBalance = $getChargeDetails->newBalance;
				$overPaymentForProc = $getChargeDetails->overPaymentForProc;
				$chk_balForProc = $getChargeDetails->balForProc;
				$approvedAmt_chh = $getChargeDetails->approvedAmt;
				$overPayAmt=$getChargeDetails->overPaymentForProc;
				$charge_list_id=$getChargeDetails->charge_list_id;
			}
		
		
			// GETTING total copaypaid amount
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
			$tot_paid_chk1=$paidForProc_chk+$copayAmount;
			
			// GET WRITE OFF FOR UPDATE CHARGE DETAILS
				$getWriteOffDetailsStr = "SELECT sum(write_off_amount) as write_off_amount FROM paymentswriteoff WHERE charge_list_detail_id = '$charge_list_detail_id' and delStatus=0";
				$getWriteOffDetailsQry = imw_query($getWriteOffDetailsStr);
				$getWriteOffDetailsRow = imw_fetch_array($getWriteOffDetailsQry);
				$write_off_amount = $getWriteOffDetailsRow['write_off_amount'];
				// GET WRITE OFF FOR UPDATE CHARGE DETAILS
				
			if($totalPayment>$overPaymentForProc){
				$newOverPay = 0;
				$newBalanceNow = $newBalance + ($totalPayment - $overPaymentForProc);
				$procPaid = $procPaid - ($totalPayment - $overPaymentForProc);
				
				
				if($write_off_amount>0 && $procPaid>0 && $overPayAmt>0){
					$procPaid=$procPaid-$write_off_amount;
				}
				
				$chk_bal_for_proc=$chk_balForProc+$paidForProc;
				if($chk_bal_for_proc>$approvedAmt_chh){
					$balForProc_chk=$approvedAmt_chh;
				}else{
					$balForProc_chk="balForProc + $newBalanceNow";
				}
			}else{			
				$newOverPay = $overPaymentForProc - $totalPayment;
				$newBalanceNow = 0;		
				if($write_off_amount>0 && $overPayAmt>0 && $newOverPay<=0){
					$procPaid=$procPaid-$write_off_amount;
				}	
				$balForProc_chk="0";
			}
			
			if($coPay_update){
				$procPaid=$procPaid_chk;
			}
			
			
			if($newBalanceNow==$approvedAmt_chh){
				$balForProc_chk=$approvedAmt_chh;
			}
			$updateChargeListDetailStr = "UPDATE patient_charge_list_details SET
											paidForProc = $procPaid,
											balForProc = $balForProc_chk,
											newBalance = $newBalanceNow,
											$coPay_update 
											overPaymentForProc = $newOverPay
											WHERE charge_list_detail_id = '$charge_list_detail_id'";
			
			$updateChargeListDetailQry = imw_query($updateChargeListDetailStr);	
			
			$insertAdjStr2 = "INSERT INTO account_payments SET
			patient_id = '$patient_id',
			encounter_id = '$encounter_id',
			charge_list_id = '$charge_list_id',
			charge_list_detail_id = '$charge_list_detail_id',
			payment_by='Patient',
			payment_method='Check',
			cc_type = '$cc_type',
			check_number = '$check_no',
			cc_number = '$cc_no',
			ins_id ='$ins_prov_id',
			payment_amount='$totalPayment',
			payment_date='$ret_paidDate',
			operator_id='$operator_id',
			payment_code_id='$adj_code',
			payment_type='$payment_method',
			copay_chld_id='$copay_chk',
			entered_date='$ret_entered_date',
			facility_id='$facility_id'";
			$insertAdjQry2 = imw_query($insertAdjStr2);	
		}
	}
	$sel_chld_enc5=imw_query("select * from
					patient_charge_list_details
					where
					del_status='0' and 
					charge_list_id='$charge_list_id'
					order by display_order desc");
	$fet_chld_enc5=imw_fetch_array($sel_chld_enc5);
	$dis_ord=$fet_chld_enc5['display_order']+1;
	
	if($return_chk_proc==""){
		$return_chk_proc="rtn-chk";
	}
	$sel_chld_enc6=imw_query("SELECT a.cpt_fee,b.cpt_fee_id FROM cpt_fee_table a,
								cpt_fee_tbl b
								WHERE a.cpt_fee_id = b.cpt_fee_id AND a.fee_table_column_id = '1'
								and (LOWER(b.cpt_prac_code)='".strtolower($return_chk_proc)."') and b.delete_status=0 and b.status='Active'");
	$fet_chld_enc6=imw_fetch_array($sel_chld_enc6);
	
	if($return_chk_proc=="rtn-chk"){
		$ret_cpt_fee=$fet_chld_enc6['cpt_fee'];
		$diagnosis_id1="RTN.CH";
	}else{
		$ret_cpt_fee=$return_chk_amt;
		$diagnosis_id1="RTN.CH";
	}
	
	$sel_chl_qry=imw_query("select all_dx_codes,primary_provider_id_for_reports,primaryInsuranceCoId from patient_charge_list where charge_list_id='$charge_list_id'");
	$sel_chl_row=imw_fetch_array($sel_chl_qry);
	$all_dx_codes=$sel_chl_row['all_dx_codes'];
	$primary_provider_id_for_reports=$sel_chl_row['primary_provider_id_for_reports'];
	$primaryInsuranceCoId=$sel_chl_row['primaryInsuranceCoId'];
	$all_dx_codes_arr=unserialize(html_entity_decode($all_dx_codes));
	if(!in_array($diagnosis_id1,$all_dx_codes_arr)){
		for($d=1;$d<=12;$d++){
			if(!in_array($diagnosis_id1,$all_dx_codes_arr)){
				if($all_dx_codes_arr[$d]==""){
					$all_dx_codes_arr[$d]=$diagnosis_id1;
				}
			}
		}
		$up_all_dx_codes=serialize($all_dx_codes_arr);
		if(count($all_dx_codes_arr)>0){
			imw_query("update patient_charge_list set all_dx_codes='$up_all_dx_codes' where charge_list_id='$charge_list_id'");
		}
	}
	$chr_detail_dx_up="";
	if(in_array($diagnosis_id1,$all_dx_codes_arr)){
		for($d=1;$d<=12;$d++){
			if($diagnosis_id1==$all_dx_codes_arr[$d]){
				$chr_detail_dx_up="diagnosis_id".$d." = '".$diagnosis_id1 ."', ";
			}	
		}
	}	
	$insertAdjStr4 = "INSERT INTO patient_charge_list_details SET
					charge_list_id ='$charge_list_id',patient_id ='$patient_id',procCode ='".$fet_chld_enc6['cpt_fee_id']."',
					start_date='".$fet_chld_enc5['start_date']."',end_date='".$fet_chld_enc5['end_date']."',
					type_of_service ='".$fet_chld_enc5['type_of_service']."',place_of_service='".$fet_chld_enc5['place_of_service']."',
					primaryProviderId='".$fet_chld_enc5['primaryProviderId']."',secondaryProviderId ='".$fet_chld_enc5['secondaryProviderId']."',
					tertiaryProviderId ='".$fet_chld_enc5['tertiaryProviderId']."',units ='1',
					procCharges ='".$ret_cpt_fee."',totalAmount ='".$ret_cpt_fee."',
					paidForProc='0',balForProc='".$ret_cpt_fee."',
					$chr_detail_dx_up approvedAmt='".$ret_cpt_fee."',
					newBalance='".$ret_cpt_fee."',posFacilityId='".$fet_chld_enc5['posFacilityId']."',
					proc_selfpay='".$fet_chld_enc5['proc_selfpay']."',display_order='".$dis_ord."',
					entered_date='".$ret_entered_date."',operator_id='".$operator_id."',
					primary_provider_id_for_reports='".$primary_provider_id_for_reports."'";
					
	$insertAdjQry4 = imw_query($insertAdjStr4);
	$tx_chld=imw_insert_id();

	if($enc_ret_chk_arr){
		$encounter_id_arr[]=$encounter_id;	
	}else{
		$encounter_id=$encounter_id;	
	}
	if(strtolower($paid_by)=="patient" && $primaryInsuranceCoId>0){
		$tx_payment_time = date('H:i:s');
		imw_query("insert into tx_payments set patient_id='$patient_id',encounter_id='$encounter_id',charge_list_id = '$charge_list_id',
		charge_list_detail_id='$tx_chld',pri_due='$ret_cpt_fee',sec_due='0',tri_due='0',pat_due='0',
		entered_date='$ret_entered_date',payment_date='$ret_paidDate',payment_time='$tx_payment_time',operator_id='$operator_id'");
		imw_query("update patient_charge_list_details set pri_due='0',sec_due='0',tri_due='0',pat_due='$ret_cpt_fee' where charge_list_detail_id='$tx_chld'");
	}
	include("manageEncounterAmounts.php");
}
?>