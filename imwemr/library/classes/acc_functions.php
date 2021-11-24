<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7,
 Purpose: Accounting Functions
 Access Type: Indirect Access.
*/

//Get current open case for a patient
function getInuranceCaseId_sup($patient_id,$sup_date)
{
	$sql = "select ins_caseid from insurance_case where patient_id='".$patient_id."' and  case_status='Open' order by ins_caseid DESC";
	$rez = imw_query($sql);
	if(imw_num_rows($rez) > 0)
	{
		$arrAllCaseId = array();
		while($row=imw_fetch_array($rez)){
			$arrAllCaseId[] = $row["ins_caseid"];
		}

		if(imw_num_rows($rez) > 1)
		{
			$isMultiCase = true;
			$sql2 = "SELECT case_type_id FROM schedule_appointments
					WHERE sa_patient_id ='".$patient_id."' AND sa_app_start_date= '".$sup_date."' 
					AND sa_patient_app_status_id not in(201,203) order by sa_app_start_date DESC LIMIT 0,1 ";
			$rez2 = imw_query($sql2);
			if(imw_num_rows($rez2) > 0)
			{
				$row2=imw_fetch_array($rez2);
				if(array_search($row2["case_type_id"],$arrAllCaseId) !== false)
				{
					return $row2["case_type_id"];
				}
			}
			else
			{
				return "";
			}
		}
		else
		{
			$isMultiCase = false;
			return $arrAllCaseId[0];
		}
	}
	else
	{
		return "";
	}
}

//Get patient referral detail
function getPatientReffPhy($patientId,$insId,$type){
	if($type == 'primary'){
		$reffType = 1;
	}
	if($type == 'secondary'){
		$reffType = 2;
	}
	if($type == 'tertiary'){
		$reffType = 3;
	}
	
	$qry = "select patient_reff.* from patient_reff join insurance_data
			on insurance_data.id = patient_reff.ins_data_id where insurance_data.type = '$type'
			and insurance_data.pid = '$patientId' and insurance_data.ins_caseid = '$insId'
			and insurance_data.actInsComp = '1' and insurance_data.referal_required = 'Yes'
			and insurance_data.provider > '0' 
			and patient_reff.reff_type = '$reffType'and  ((patient_reff.end_date >= current_date() and 
			patient_reff.effective_date <= current_date())
			or(patient_reff.no_of_reffs > '0'))
			order by patient_reff.end_date desc,patient_reff.reff_id desc,
			insurance_data.actInsComp desc limit 0,1";
	$qryId = imw_query($qry);			
	if(imw_num_rows($qryId)>0){
		$qryRes = imw_fetch_object($qryId);
	}
	return $qryRes;
}

//SET & GET AMOUNTS FOR GIVEN ENCOUNTER ID
function patient_proc_bal_update($encounter){
	if(empty($encounter) === false){
		$chld_cpt_self_arr=array();
		$getProcedureDetailsStr = "SELECT b.cpt_prac_code,b.cpt_desc,b.not_covered,b.cpt_fee_id
									FROM cpt_fee_tbl as b
									WHERE b.not_covered=1";
		$getProcedureDetailsQry = imw_query($getProcedureDetailsStr);
		while($getProcedureDetailsRows = imw_fetch_array($getProcedureDetailsQry)){
			$chld_cpt_ref_arr[$getProcedureDetailsRows['cpt_fee_id']]=$getProcedureDetailsRows['cpt_prac_code'];
		}
		
		$chl_qry = imw_query("select * from patient_charge_list where del_status='0' and encounter_id='$encounter'");
		$enc_id_arr=array();
		while($qryRes = imw_fetch_array($chl_qry)){	
			$patient_id = $qryRes['patient_id'];
			$encounter_id = $qryRes['encounter_id'];
			$charge_list_id = $qryRes['charge_list_id'];
			$primary_paid = $qryRes['primary_paid'];
			$secondary_paid = $qryRes['secondary_paid'];
			$tertiary_paid = $qryRes['tertiary_paid'];
			$primaryInsuranceCoId = $qryRes['primaryInsuranceCoId'];
			$secondaryInsuranceCoId = $qryRes['secondaryInsuranceCoId'];
			$tertiaryInsuranceCoId = $qryRes['tertiaryInsuranceCoId'];
			$copay = $qryRes['copay'];
			
			$getPaidByStr = "SELECT a.paid_by,a.insProviderId,
							 a.insCompany,b.overPayment,
							 a.paymentClaims,a.encounter_id,
							 b.paidForProc,b.charge_list_detail_id
							 FROM patient_chargesheet_payment_info as a,
							 patient_charges_detail_payment_info b
							 WHERE 
							 a.encounter_id in($encounter_id)
							 AND a.payment_id = b.payment_id
							 AND b.deletePayment='0'";
			$getPaidByQry = imw_query($getPaidByStr);
			while($getPaidByRows = imw_fetch_array($getPaidByQry)){
				$enc_id=$getPaidByRows['encounter_id'];
				if($getPaidByRows['paymentClaims']=='Negative Payment'){
					$tot_enc_neg_paid_amt_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
					$tot_enc_neg_paid_amt_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
					
					$tot_chld_neg_paid_amt_arr[$getPaidByRows['charge_list_detail_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
					$tot_chld_neg_paid_amt_arr[$getPaidByRows['charge_list_detail_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
					
					$final_tot_neg_paid_amt_arr[$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
					$final_tot_neg_paid_amt_arr[$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
				}else{
					$tot_enc_paid_amt_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
					$tot_enc_paid_amt_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
					
					$tot_chld_paid_amt_arr[$getPaidByRows['charge_list_detail_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
					$tot_chld_paid_amt_arr[$getPaidByRows['charge_list_detail_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
					
					if($getPaidByRows['charge_list_detail_id']==0){
						$tot_chld_paid_amt_copay_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
						$tot_chld_paid_amt_copay_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
					}
					$final_tot_paid_amt_arr[$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
					$final_tot_paid_amt_arr[$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
				}
				
				if($getPaidByRows['paymentClaims']=='Deposit'){
					$paid_proc_arr[$enc_id]['paidForProc'][] = $getPaidByRows['paidForProc'];
					$deposit_proc_arr[$enc_id][$getPaidByRows['charge_list_detail_id']][] = $getPaidByRows['paidForProc'];
				}
			}
			
			$pri_bor_paid_col="";
			$sec_bor_paid_col="";
			$tri_bor_paid_col="";
			$pat_bor_paid_col="";
			$pat_heard_ins="false";
			if($primary_paid=="false" && $primaryInsuranceCoId>0){
				$pri_bor_paid_col="border";
			}else if($secondary_paid=="false" && $secondaryInsuranceCoId>0){
				$sec_bor_paid_col="border";
			}else if($tertiary_paid=="false" && $tertiaryInsuranceCoId>0){
				$tri_bor_paid_col="border";
			}else{
				if($newBal>0){
					$pat_bor_paid_col="border";
				}
				$pat_heard_ins="true";
			}
			$qry=imw_query("select * from patient_charge_list_details where del_status='0' and charge_list_id='$charge_list_id'");
			while($row=imw_fetch_array($qry)){
				$proc_approvedAmt=$row['approvedAmt'];
				$charge_list_detail_id=$row['charge_list_detail_id'];
				$chld_id=$row['charge_list_detail_id'];
				$proc_newBalance=$row['newBalance'];
				$proc_coPayAdjustedAmount = $row['coPayAdjustedAmount'];
				$proc_selfpay = $row['proc_selfpay'];
				$proc_deductAmt = $row['deductAmt'];
				$procCode  = $row['procCode'];
				
				$chld_deduct_arr=array();
				$getDeductDetailsStr = "SELECT deduct_ins_id,deduct_amount FROM payment_deductible WHERE delete_deduct=0 and
										deductible_by='Insurance' and charge_list_detail_id='$charge_list_detail_id'";
				$getDeductDetailsQry = imw_query($getDeductDetailsStr);
				while($getDeductDetailsRows = imw_fetch_array($getDeductDetailsQry)){
					$chld_deduct_arr[$getDeductDetailsRows['deduct_ins_id']][]=$getDeductDetailsRows['deduct_amount'];
				}
				$pri_deduct=0;
				$sec_deduct=0;
				$tri_deduct=0;
				$pri_deduct=array_sum($chld_deduct_arr[$primaryInsuranceCoId]);
				$sec_deduct=array_sum($chld_deduct_arr[$secondaryInsuranceCoId]);
				$tri_deduct=array_sum($chld_deduct_arr[$tertiaryInsuranceCoId]);
				$for_pri_deduct=true;
				$for_sec_deduct=false;
				$for_tri_deduct=false;
				$for_pat_deduct=false;
				
				$chld_denied_arr=array();
				$getDeniedDetailsStr = "SELECT deniedById,deniedAmount FROM deniedpayment WHERE denialDelStatus=0 and
										deniedBy='Insurance' and charge_list_detail_id='$charge_list_detail_id' and next_responsible_by>0";
				$getDeniedDetailsQry = imw_query($getDeniedDetailsStr);
				while($getDeniedDetailsRows = imw_fetch_array($getDeniedDetailsQry)){
					$chld_denied_arr[$getDeniedDetailsRows['deniedById']][]=$getDeniedDetailsRows['deniedAmount'];
				}
				$pri_denied=0;
				$sec_denied=0;
				$tri_denied=0;
				$pri_denied=array_sum($chld_denied_arr[$primaryInsuranceCoId]);
				$sec_denied=array_sum($chld_denied_arr[$secondaryInsuranceCoId]);
				$tri_denied=array_sum($chld_denied_arr[$tertiaryInsuranceCoId]);
				
				$chld_acc_pay_arr=array();
				$getAccPayDetailsStr = "SELECT ins_id,payment_amount FROM account_payments WHERE del_status=0 and payment_by='Insurance' 
										and charge_list_detail_id='$charge_list_detail_id' and (payment_type='Co-Insurance' or payment_type='Co-Payment')";
				$getAccPayDetailsQry = imw_query($getAccPayDetailsStr);
				while($getAccPayDetailsRows = imw_fetch_array($getAccPayDetailsQry)){
					$chld_acc_pay_arr[$getAccPayDetailsRows['ins_id']][]=$getAccPayDetailsRows['payment_amount'];
				}
				$pri_co_ins=0;
				$sec_co_ins=0;
				$tri_co_ins=0;
				$pri_co_ins=array_sum($chld_acc_pay_arr[$primaryInsuranceCoId]);
				$sec_co_ins=array_sum($chld_acc_pay_arr[$secondaryInsuranceCoId]);
				$tri_co_ins=array_sum($chld_acc_pay_arr[$tertiaryInsuranceCoId]);
				
				$tot_chld_pri_paid_amt_all_num=count($tot_chld_paid_amt_arr[$chld_id][1]);
				$tot_chld_sec_paid_amt_all_num=count($tot_chld_paid_amt_arr[$chld_id][2]);
				$tot_chld_tri_paid_amt_all_num=count($tot_chld_paid_amt_arr[$chld_id][3]);
				$tot_chld_pat_paid_amt_all_num=count($tot_chld_paid_amt_arr[$chld_id][0]);
				
				$tot_chld_pri_paid_amt_all=array_sum($tot_chld_paid_amt_arr[$chld_id][1])-array_sum($tot_chld_neg_paid_amt_arr[$chld_id][1]);	
				$tot_chld_sec_paid_amt_all=array_sum($tot_chld_paid_amt_arr[$chld_id][2])-array_sum($tot_chld_neg_paid_amt_arr[$chld_id][2]);
				$tot_chld_tri_paid_amt_all=array_sum($tot_chld_paid_amt_arr[$chld_id][3])-array_sum($tot_chld_neg_paid_amt_arr[$chld_id][3]);
				$tot_chld_pat_paid_amt_all=array_sum($tot_chld_paid_amt_arr[$chld_id][0])-array_sum($tot_chld_neg_paid_amt_arr[$chld_id][0]);
				
				if($proc_coPayAdjustedAmount>0){
					$tot_chld_pat_paid_amt_all=$tot_chld_pat_paid_amt_all+array_sum($tot_chld_paid_amt_copay_arr[$encounter_id][0]);
				}
				if($primary_paid=="true"){
					$for_pri_deduct=false;	
				}
				if($secondary_paid=="true"){
					$for_sec_deduct=false;	
				}
				if($secondaryInsuranceCoId>0 && ($primaryInsuranceCoId==0 || $pri_deduct>0 || $primary_paid=="true" || $tot_chld_pri_paid_amt_all>0 || $tot_chld_pri_paid_amt_all_num>0) && $sec_deduct<=0 && $secondary_paid=="false"){
					$for_sec_deduct=true;
					$for_pri_deduct=false;
				}
				if($secondaryInsuranceCoId>0 && ($primaryInsuranceCoId==0 || $pri_denied>0 || $primary_paid=="true" || $tot_chld_pri_paid_amt_all>0 || $tot_chld_pri_paid_amt_all_num>0) && $sec_denied<=0  && $secondary_paid=="false"){
					$for_sec_deduct=true;
					$for_pri_deduct=false;
				}
				if($secondaryInsuranceCoId>0 && ($primaryInsuranceCoId==0 || $pri_co_ins>0 || $primary_paid=="true" || $tot_chld_pri_paid_amt_all>0 || $tot_chld_pri_paid_amt_all_num>0) && $sec_co_ins<=0  && $secondary_paid=="false"){
					$for_sec_deduct=true;
					$for_pri_deduct=false;
				}
				
				if($for_sec_deduct==true && ($secondaryInsuranceCoId==0 || $sec_deduct>0 || $sec_denied>0 || $sec_co_ins>0 || $tot_chld_sec_paid_amt_all>0 || $tot_chld_sec_paid_amt_all_num>0) && ($primaryInsuranceCoId==0 || $pri_denied>0 || $pri_deduct>0 || $pri_co_ins>0 || $tot_chld_pri_paid_amt_all>0 || $tot_chld_pri_paid_amt_all_num>0)){
					$for_sec_deduct=false;
					$for_pri_deduct=false;
					$for_tri_deduct=true;
				}
				if($for_pri_deduct==true && ($pri_deduct>0 || $pri_denied>0 || $pri_co_ins>0 || $primaryInsuranceCoId==0 || $tot_chld_pri_paid_amt_all>0 || $tot_chld_pri_paid_amt_all_num>0)){
					$for_pri_deduct=false;
				}
				if($for_pri_deduct==false && $for_sec_deduct==false){
					$for_tri_deduct=true;
				}
				if($for_tri_deduct==true && ($tri_deduct>0 || $tri_denied>0 || $tri_co_ins>0 || $tertiaryInsuranceCoId==0 || $tot_chld_tri_paid_amt_all>0 || $tot_chld_tri_paid_amt_all_num>0)){
					$for_tri_deduct=false;
				}
				
				if(($for_sec_deduct==false || $sec_deduct>0 || $sec_denied>0 || $sec_co_ins>0 || $secondaryInsuranceCoId==0 || $tot_chld_sec_paid_amt_all>0 || $tot_chld_sec_paid_amt_all_num>0) 
				&& ($for_tri_deduct==false || $tri_deduct>0 || $tri_denied>0 || $tri_co_ins>0 || $tertiaryInsuranceCoId==0 || $tot_chld_tri_paid_amt_all>0 || $tot_chld_tri_paid_amt_all_num>0) 
				&& $for_pri_deduct==false){
					$for_pat_deduct=true;
				}

				$ref_amt="";
				$ref_amt=$chld_cpt_ref_arr[$procCode];
				$tot_pt_balance_proc=0;
				$pri_due=0;
				$sec_due=0;
				$tri_due=0;
				if($pat_heard_ins == "true"){
					$pri_due = 0;
					$sec_due = 0;
					$tri_due = 0;
					$tot_pt_balance_proc=$proc_newBalance;
				}else{	
					if($pat_bor_paid_col!=""){
						$tot_pt_balance_proc=$proc_newBalance;
						$pri_due = 0;
						$sec_due = 0;
						$tri_due = 0;
					}else{
						if($proc_coPayAdjustedAmount>0){	
							if($proc_selfpay>0){
								$tot_pt_balance_proc=$proc_newBalance;
							}else{
								if($for_pri_deduct == true){
									$pri_due=$proc_newBalance;
								}else{
									if($for_sec_deduct == true || $for_tri_deduct == true){
										//$tot_pt_balance_proc=$copay;
									}else{
										$tot_pt_balance_proc=$proc_newBalance;
									}
								}
							}
						}else{
							if($for_pri_deduct == true){
								$pri_due=$proc_newBalance;
							}else{
								if($for_sec_deduct == true || $for_tri_deduct == true){
								}else{
									$tot_pt_balance_proc=$proc_newBalance;
								}
							}
						}
						if($proc_selfpay>0 || $ref_amt>0){
							$tot_pt_balance_proc=$proc_newBalance;
						}
					}
				}
				if($tot_pt_balance_proc<0){ $tot_pt_balance_proc=0;}
				
				if($for_sec_deduct == true){
					$sec_due=$proc_deductAmt;
				}
				if($for_sec_deduct == false && $for_tri_deduct == true){
					$tri_due=$proc_deductAmt;
				}
				if($for_pat_deduct == true){
					$tot_pt_balance_proc=$proc_newBalance;
				}
				if(($tot_chld_pri_paid_amt_all>0 || $tot_chld_pri_paid_amt_all_num>0) && $primaryInsuranceCoId>0){
					if(($tot_chld_sec_paid_amt_all>0 || $tot_chld_sec_paid_amt_all_num>0) && $secondaryInsuranceCoId>0){
						if(($tot_chld_tri_paid_amt_all>0 || $tot_chld_tri_paid_amt_all_num>0) && $tertiaryInsuranceCoId>0){
							$tot_pt_balance_proc=$proc_newBalance;
						}else{
							if($tertiaryInsuranceCoId>0 && $for_tri_deduct==true){
								$tri_due=$proc_newBalance-$tot_pt_balance_proc;
							}else{
								$tot_pt_balance_proc=$proc_newBalance;
							}
						}
					}else{
						if($secondaryInsuranceCoId>0 && $for_sec_deduct==true){
							$sec_due=$proc_newBalance-$tot_pt_balance_proc;
						}else{
							if(($tot_chld_tri_paid_amt_all>0 || $tot_chld_tri_paid_amt_all_num>0) && $tertiaryInsuranceCoId>0){
								$tot_pt_balance_proc=$proc_newBalance;
							}else{
								if($tertiaryInsuranceCoId>0 && $for_tri_deduct==true){
									$tri_due=$proc_newBalance-$tot_pt_balance_proc;
								}else{
									$tot_pt_balance_proc=$proc_newBalance;
								}
							}
						}
					}
				}else{
					if($primaryInsuranceCoId>0){
						$pri_due=$proc_newBalance-$tot_pt_balance_proc;
					}
					if($for_sec_deduct == true){
						$pri_due=0;
						$sec_due=$proc_newBalance;
					}
					if($for_sec_deduct == false && $for_tri_deduct == true){
						$pri_due=0;
						$sec_due=0;
						$tri_due=$proc_newBalance;
					}
					if($for_pat_deduct == true){
						$tot_pt_balance_proc=$proc_newBalance;
						$pri_due=0;
						$sec_due=0;
						$tri_due=0;
					}
					
				}
				if($proc_selfpay>0 || $ref_amt>0){
					$tot_pt_balance_proc=$proc_newBalance;
					$pri_due=0;
					$sec_due=0;
					$tri_due=0;
				}
				if($pri_due<0){ $pri_due=0;}
				if($sec_due<0){ $sec_due=0;}
				if($tri_due<0){ $tri_due=0;}
				if($proc_newBalance<=0){ $tot_pt_balance_proc=0;}
				
				if($proc_newBalance>0){
					$tot_due_amt=$pri_due+$sec_due+$tri_due+$tot_pt_balance_proc;
					$diff_due_amt=$tot_due_amt-$proc_newBalance;
					if($diff_due_amt!=0){
						if($pri_due>0){
							$pri_due=$pri_due-$diff_due_amt;
						}else if($sec_due>0){
							$sec_due=$sec_due-$diff_due_amt;
						}else if($tri_due>0){
							$tri_due=$tri_due-$diff_due_amt;
						}else if($tot_pt_balance_proc>0){
							$tot_pt_balance_proc=$tot_pt_balance_proc-$diff_due_amt;
						}
						if($pri_due<0){$pri_due=0;}
						if($sec_due<0){$sec_due=0;}
						if($tri_due<0){$tri_due=0;}
						if($tot_pt_balance_proc<0){$tot_pt_balance_proc=0;}
						if($pri_due==0 && $sec_due==0 && $tri_due==0){
							$tot_pt_balance_proc=$proc_newBalance;	
						}
					}else{
						if($tot_pt_balance_proc>$proc_newBalance){
							$tot_pt_balance_proc=$proc_newBalance;
						}
						if($pri_due==0 && $sec_due==0 && $tri_due==0){
							$tot_pt_balance_proc=$proc_newBalance;
						}
					}
				}else{
					$pri_due=0;
					$sec_due=0;
					$tri_due=0;
					$tot_pt_balance_proc=0;
				}

				$len_tx_qry=imw_query("select charge_list_detail_id from tx_payments where charge_list_detail_id='$charge_list_detail_id' and del_status='0' limit 0,1");
				if(imw_num_rows($len_tx_qry)==0){
					imw_query("update patient_charge_list_details set pri_due=$pri_due,sec_due=$sec_due,tri_due=$tri_due,pat_due=$tot_pt_balance_proc where charge_list_detail_id ='$charge_list_detail_id'");
				}
				//echo $patient_id.'-'.$encounter_id.'<br>';
			}
		}
	}
}

//Set patient and insurance due for a encounter
function patient_bal_update($encounter){
	if(empty($encounter) === false){
		//--- FETCH DATA FOR SINGLE ENCOUNTER ------
		$qry = imw_query("select encounter_id,approvedTotalAmt,primaryInsuranceCoId,
				secondaryInsuranceCoId,copay,pri_copay,sec_copay,copayPaid,coPayNotRequired,
				tertiaryInsuranceCoId,insPaidAmt,charge_list_id,
				insAmt,insuranceDue,patientDue,totalBalance, 
				primary_paid,secondary_paid,tertiary_paid,deductibleTotalAmt
				from patient_charge_list where del_status='0' and encounter_id = '$encounter'");
		$chargeQryRes = imw_fetch_array($qry);
		
		$charge_list_id = $chargeQryRes['charge_list_id'];
		$encounter_id = $chargeQryRes['encounter_id'];
		$primaryInsuranceCoId = $chargeQryRes['primaryInsuranceCoId'];
		$secondaryInsuranceCoId = $chargeQryRes['secondaryInsuranceCoId'];
		$tertiaryInsuranceCoId = $chargeQryRes['tertiaryInsuranceCoId'];
		$totalBalance = preg_replace('/,/','',$chargeQryRes['totalBalance']);
		$coPayNotRequired = $chargeQryRes['coPayNotRequired'];
		$coPayNotRequired2 = $chargeQryRes['coPayNotRequired2'];
		$copay = preg_replace('/,/','',$chargeQryRes['copay']);
		$pri_copay = preg_replace('/,/','',$chargeQryRes['pri_copay']);
		$sec_copay = preg_replace('/,/','',$chargeQryRes['sec_copay']);
		$totalAmt = preg_replace('/,/','',$chargeQryRes['approvedTotalAmt']);
		$deductibleTotalAmt = $chargeQryRes['deductibleTotalAmt'];
		
		$primary_paid = $chargeQryRes['primary_paid'];
		$secondary_paid = $chargeQryRes['secondary_paid'];
		$tertiary_paid = $chargeQryRes['tertiary_paid'];
		
		$primaryPaid = false;
		$secondaryPaid = false;
		$tertiaryPaid = false;
		if($chargeQryRes['primaryInsuranceCoId'] == 0 || $primary_paid=='true'){
			$primaryPaid = true;
		}
		if($chargeQryRes['secondaryInsuranceCoId'] == 0 || $secondary_paid=='true'){
			$secondaryPaid = true;
		}
		if($chargeQryRes['tertiaryInsuranceCoId'] == 0 || $tertiary_paid=='true'){
			$tertiaryPaid = true;
		}
		
		$qry = imw_query("select sum(approvedAmt) as approvedAmt_chld from patient_charge_list_details where del_status='0' and charge_list_id = '$charge_list_id' and proc_selfpay='1'");
		$chargeQryResDet = imw_fetch_array($qry);
		
		$self_pay_chld = $chargeQryResDet['approvedAmt_chld'];
		
		//--- GET WRITE OFF AMOUNT -----
		$qry = imw_query("select write_off_amount from paymentswriteoff where encounter_id = '$encounter' and delStatus != 1");
		$write_off_amount_arr = array();
		while($writeOffQryRes = imw_fetch_array($qry)){
			$write_off_amount_arr[] = preg_replace('/,/','',$writeOffQryRes['write_off_amount']);		
		}
		$write_off_amount = array_sum($write_off_amount_arr);
		$totalAmt = $totalAmt - $write_off_amount;
		$patientAmt = 0;
		$patientAmt_copay = 0;
		$patientAmt_sec = 0;
		if($coPayNotRequired != 1){
			$patientAmt_copay = $pri_copay;
		}
		if($coPayNotRequired2 != 1){
			$patientAmt_sec = $sec_copay;
		}
		if($self_pay_chld>0){
			$patientAmt = $deductibleTotalAmt + $self_pay_chld;
		}else{
			if($primaryPaid==true && $secondaryPaid==true && $tertiaryPaid==true){
				$deductibleTotalAmt=$deductibleTotalAmt;
			}else{
				$deductibleTotalAmt=0;
			}
			$patientAmt = $patientAmt_copay + $patientAmt_sec + $deductibleTotalAmt + $self_pay_chld;
		}
		
		//---- GET REFRACTION PROCEDURE ID -------
		$qry = imw_query("select refraction from copay_policies where policies_id = '1'");
		$refQryRes = imw_fetch_array($qry);
		
		if($refQryRes['refraction'] == 'Yes'){
			$qry = imw_query("select cpt_fee_id from cpt_fee_tbl where cpt4_code = '92015' and delete_status = '0' and not_covered='1'");
			$cptQryRes = imw_fetch_array($qry);
			$cpt_fee_id = $cptQryRes['cpt_fee_id'];		
		}
		
		//--- GET ALL CHARGE LIST DETAIL ID -----
		$qry = imw_query("select charge_list_detail_id,totalAmount,procCode,pri_due,sec_due,tri_due,pat_due
				from patient_charge_list_details where del_status='0' and charge_list_id = '$charge_list_id'");
		$charge_list_detail_id_arr = array();
		$patientDue_arr=array();
		$insuranceDue_arr=array();
		while($detailQryRes = imw_fetch_array($qry)){	
			$procCode = $detailQryRes['procCode'];		
			$charge_list_detail_id_arr[] = $detailQryRes['charge_list_detail_id'];
			//--- IF PROCEDURE CODE IS REFRACTION --------
			if($cpt_fee_id == $procCode){
				$patientAmt += preg_replace('/,/','',$detailQryRes['totalAmount']);
			}
			$patientDue_arr[]=$detailQryRes['pat_due'];
			$insuranceDue_arr[]=$detailQryRes['pri_due']+$detailQryRes['sec_due']+$detailQryRes['tri_due'];
		}
		$charge_list_detail_id_str = join(',',$charge_list_detail_id_arr);
		
		//---- GET ALL PAYMENTS DETAILS FOR AN ENCOUNTER --------
		if(empty($encounter_id) === false){

			$qry = imw_query("select patient_charges_detail_payment_info.paidForProc + 
					patient_charges_detail_payment_info.overPayment as paidForProc,
					patient_charges_detail_payment_info.paidBy,				
					patient_chargesheet_payment_info.insProviderId,
					patient_chargesheet_payment_info.paymentClaims  
					from patient_charges_detail_payment_info join 
					patient_chargesheet_payment_info on
					patient_chargesheet_payment_info.payment_id = 
					patient_charges_detail_payment_info.payment_id
					where patient_chargesheet_payment_info.encounter_id = '$encounter_id'
					and patient_charges_detail_payment_info.deletePayment != '1'");
			
			$insPaidAmtArr = array();
			$patientPaidAmtArr = array();
			$resPaidAmtArr = array();				
			$insCompanyPaidIdArr = array();
			while($paymentQryRes = imw_fetch_array($qry)){
				$paidForProc = preg_replace('/,/','',$paymentQryRes['paidForProc']);
				$overPayment = preg_replace('/,/','',$paymentQryRes['overPayment']);
				$paidBy = ucfirst($paymentQryRes['paidBy']);
				$paymentClaims = $paymentQryRes['paymentClaims'];
				if($paidBy == 'Insurance'){
					if($paymentClaims == 'Negative Payment'){
						$insPaidAmtArr[] = '-'.$paidForProc;
						$insPaidAmtArr[] = '-'.$overPayment;
					}else{
						$insPaidAmtArr[] = $paidForProc;
						$insPaidAmtArr[] = $overPayment;
					}
				}
				else if($paidBy == 'Patient'){
					if($paymentClaims == 'Negative Payment'){
						$patientPaidAmtArr[] = '-'.$paidForProc;
						$patientPaidAmtArr[] = '-'.$overPayment;
					}else{
						$patientPaidAmtArr[] = $paidForProc;
						$patientPaidAmtArr[] = $overPayment;
					}
				}
				else{
					if($paymentClaims == 'Negative Payment'){						
						$resPaidAmtArr[] = '-'.$paidForProc;
						$resPaidAmtArr[] = '-'.$overPayment;
					}else{
						$resPaidAmtArr[] = $paidForProc;
						$resPaidAmtArr[] = $overPayment;
					}
				}
			}
			$insPaidAmt = array_sum($insPaidAmtArr);
			$patientPaidAmt = array_sum($patientPaidAmtArr);
			$resPaidAmt = array_sum($resPaidAmtArr);
		}
		
		//---- GET ALL CREDIT DETAILS FOR AN ENCOUNTER --------
		if(empty($encounter_id) === false){
			$len=0;
			$qry = imw_query("select type,ins_case,amountApplied from creditapplied
					where crAppliedTo = 'adjustment' and crAppliedToEncId_adjust = '$encounter_id'
					and credit_applied = '1' and delete_credit ='0'");
			while($creditdebitQryRes = imw_fetch_array($qry)){	
				$amountApplied = preg_replace('/,/','',$creditdebitQryRes['amountApplied']);
				$type = ucfirst($creditdebitQryRes['type']);
				$insComp = $creditdebitQryRes['ins_case'];	
				if($type == 'Insurance'){
					$insPaidAmt += $amountApplied;
				}
				else if($type == 'Patient'){
					$patientPaidAmt += $amountApplied;
				}
				else{
					$resPaidAmt += $amountApplied;

				}
			}
		}
					
		if(empty($encounter_id) === false){
			$len=0;
			$qry = imw_query("select payment_type,payment_amount,payment_by from account_payments 
					where encounter_id = '$encounter_id' and del_status ='0'");
			while($adjQryRes = imw_fetch_array($qry)){	
				$amountApplied = preg_replace('/,/','',$adjQryRes['payment_amount']);
				$payment_by = ucfirst($adjQryRes['payment_by']);
				$payment_type = ucfirst($adjQryRes['payment_type']);
				if($payment_type=="Adjustment"){
					if($payment_by == 'Insurance'){
						$insPaidAmt += -$amountApplied;
					}
					else if($payment_by == 'Patient'){
						$patientPaidAmt += -$amountApplied;
					}
					else{
						$resPaidAmt += -$amountApplied;
					}
				}
				if($payment_type=="Over Adjustment"){
					if($payment_by == 'Insurance'){
						$insPaidAmt += $amountApplied;
					}
					else if($payment_by == 'Patient'){
						$patientPaidAmt += $amountApplied;
					}
					else{
						$resPaidAmt += $amountApplied;
					}
				}
			}
		}
		
		$insAmt = $totalAmt - $patientAmt;
		$patientDue = 0;
		$insuranceDue = 0;
		//--- HEARD OFF CHECK FROM ALL INSURANCE COMPANIES --------
		if($primaryPaid == true and $secondaryPaid == true and $tertiaryPaid == true){
			$insuranceDue = 0;
			$patientDue=0;
			//$patientDue = $totalBalance; 
			$patientDueRes = $totalAmt-$insPaidAmt;
			if($patientDueRes<0){
				$insuranceDue=$patientDueRes;
				$patientDue=$patientDue-($patientPaidAmt + $resPaidAmt);
			}else{
				//$patientDue=$patientDueRes-($patientPaidAmt + $resPaidAmt);
				$patientDue=$totalBalance;
			}
		}
		else{
			$paidAmt = $insPaidAmt + $patientPaidAmt + $resPaidAmt;	
			
			/*$patientDue = $patientAmt - ($patientPaidAmt + $resPaidAmt);
			$insuranceDue = $insAmt - $insPaidAmt;*/
			
			//---- IF PATIENT PAID AMOUNT GRATER THAN PATIENT AMOUNT -------
			if($patientAmt <= ($patientPaidAmt + $resPaidAmt)){
				$patientDue = 0;
				$patientOverPaid = ($patientPaidAmt + $resPaidAmt) - $patientAmt;
			}	
			else{			
				$patientDue = $patientAmt - ($patientPaidAmt + $resPaidAmt);
				$patientOverPaid = 0;
			}	
			//---- IF INSURANCE PAID AMOUNT GRATER THAN INSURANCE AMOUNT -------
			if($insAmt <= $insPaidAmt){
				$insuranceOverPaid = $insPaidAmt - $insAmt;
				$insuranceDue = 0;
			}	
			else{
				$insuranceDue = $insAmt - ($insPaidAmt + $patientOverPaid);
				$insuranceOverPaid = 0;
			}			
			//--- OVER PAYMENT CHECK ---------
			if($patientDue >= $insuranceOverPaid){
				$patientDue = $patientDue - $insuranceOverPaid;
			}
			else{
				$insuranceDue=-($insuranceOverPaid - $patientDue);
				$patientDue = 0;
			}	
		}

		//--- UPDATE MAIN CHARGE LIST TABLE ------
		$patientDue=array_sum($patientDue_arr);
		$insuranceDue=array_sum($insuranceDue_arr);
		if($patientDue > $totalBalance){
			$patientDue = $totalBalance;
		}
		if($insuranceDue > $totalBalance){
			$insuranceDue = $totalBalance;
		}
		imw_query("update patient_charge_list set insAmt = '$insAmt',patientDue = '$patientDue',
				insuranceDue = '$insuranceDue',insPaidAmt = '$insPaidAmt',
				patientPaidAmt = '$patientPaidAmt',resPartyPaid = '$resPaidAmt'
				where encounter_id = '$encounter'");
	}

	$erp_error=array();
	if(isERPPortalEnabled()) {
		try {
			include_once($GLOBALS['srcdir']."/erp_portal/patients_balance.php");
			$patient_id = $_SESSION['patient'];
			$obj_patients = new Patients_balance;
			$obj_patients->patientResponsibleBalance($patient_id);
		} catch(Exception $e) {
			$erp_error[]='Unable to connect to ERP Portal';
		}
		
	}
	
}
	
//Set encounter payment flags	
function updateEncounterFlags($encounter_id,$stop_clm_status=0){
	$qry = imw_query("select patient_charge_list.primaryInsuranceCoId,
			patient_charge_list.secondaryInsuranceCoId, patient_charge_list.tertiaryInsuranceCoId ,
			patient_charge_list.patient_id,patient_charge_list.charge_list_id,
			patient_charge_list_details.charge_list_detail_id,
			patient_charge_list_details.newBalance
			from patient_charge_list join patient_charge_list_details on 
			patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id
			join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
			where patient_charge_list_details.del_status='0' and patient_charge_list.encounter_id = '$encounter_id'
			and patient_charge_list.primaryInsuranceCoId > '0'
			and patient_charge_list_details.proc_selfpay = '0'
			and patient_charge_list_details.procCharges > '0'
			and cpt_fee_tbl.not_covered = '0'");
	
	$primary_paid = 'false';
	$secondary_paid = 'false';
	$tertiary_paid = 'false';
	if(imw_num_rows($qry) > 0){		

		//--- GET ALL CHARGE LIST DETAIL ID ----
		$insurance_arr = array();
		$charge_list_detail_id_arr = array();
		while($insQryRes = imw_fetch_array($qry)){
			$charge_list_detail_id_arr[] = $insQryRes["charge_list_detail_id"];
			$insurance_arr['PRIMARY'] = $insQryRes['primaryInsuranceCoId'];
			$insurance_arr['SECONDARY'] = $insQryRes['secondaryInsuranceCoId'];
			$insurance_arr['TERTIARY'] = $insQryRes['tertiaryInsuranceCoId'];
		}
		$charge_list_detail_id_str = join(',',$charge_list_detail_id_arr);
		
		//--- GET ALL PAYMENTS OF AN SINGLE ENCOUNTER -------
		$qry = imw_query("select patient_chargesheet_payment_info.insProviderId,
				patient_chargesheet_payment_info.insCompany,
				patient_charges_detail_payment_info.charge_list_detail_id
				from patient_chargesheet_payment_info join patient_charges_detail_payment_info on
				patient_charges_detail_payment_info.payment_id = patient_chargesheet_payment_info.payment_id
				where patient_charges_detail_payment_info.deletePayment = '0'
				and patient_chargesheet_payment_info.encounter_id = '$encounter_id'
				and patient_chargesheet_payment_info.insProviderId  > '0'
				and patient_chargesheet_payment_info.insCompany > '0'
				and patient_charges_detail_payment_info.charge_list_detail_id in ($charge_list_detail_id_str)
				and patient_chargesheet_payment_info.paymentClaims !='Negative Payment'
				group by patient_chargesheet_payment_info.insCompany,
				patient_chargesheet_payment_info.insProviderId,					
				patient_charges_detail_payment_info.charge_list_detail_id");
		$ins_heard_flag_arr = array();
		while($payQryRes = imw_fetch_array($qry)){	
			$insProviderId = $payQryRes['insProviderId'];
			$insCompany = $payQryRes['insCompany'];
			$charge_list_detail_id = $payQryRes['charge_list_detail_id'];
			if($insurance_arr['PRIMARY'] == $insProviderId and $insCompany == 1){
				$ins_heard_flag_arr["PRIMARY"][$charge_list_detail_id] = true;
			}
			else if($insurance_arr['SECONDARY'] == $insProviderId and $insCompany == 2){
				$ins_heard_flag_arr["SECONDARY"][$charge_list_detail_id] = true;
			}
			else if($insurance_arr['TERTIARY'] == $insProviderId and $insCompany == 3){
				$ins_heard_flag_arr["TERTIARY"][$charge_list_detail_id] = true;
			}
		}
		//--- GET WRITE OFF AMOUNT FOR SINGLE ENCOUNTER BY INSURANCE COMPANY ----		
		$qry = imw_query("select write_off_by_id, charge_list_detail_id from paymentswriteoff 
				where encounter_id = '$encounter_id' and write_off_by_id > 0 
				and delStatus = '0' and charge_list_detail_id in ($charge_list_detail_id_str)
				group by write_off_by_id, charge_list_detail_id");
		while($writeQryRes = imw_fetch_array($qry)){		
			$write_off_by_id = $writeQryRes['write_off_by_id'];
			$charge_list_detail_id = $writeQryRes['charge_list_detail_id'];
			if($write_off_by_id == $insurance_arr['PRIMARY']){
				$ins_heard_flag_arr["PRIMARY"][$charge_list_detail_id] = true;
			}
			else if($write_off_by_id == $insurance_arr['SECONDARY']){
				$ins_heard_flag_arr["SECONDARY"][$charge_list_detail_id] = true;
			}
			else if($write_off_by_id == $insurance_arr['TERTIARY']){
				$ins_heard_flag_arr["TERTIARY"][$charge_list_detail_id] = true;
			}
		}
		
		
		//---- GET PAYMENT DENIEL AMOUNT ----
		$qry = imw_query("select deniedById, charge_list_detail_id from deniedpayment 
				where encounter_id = '$encounter_id' and denialDelStatus = '0' 
				and deniedById > 0 and charge_list_detail_id in ($charge_list_detail_id_str)
				and next_responsible_by>0 group by deniedById, charge_list_detail_id");
		while($deniedQryRes = imw_fetch_array($qry)){	
			$deniedById = $deniedQryRes['deniedById'];
			$charge_list_detail_id = $deniedQryRes['charge_list_detail_id'];
			if($deniedById == $insurance_arr['PRIMARY']){
				$ins_heard_flag_arr["PRIMARY"][$charge_list_detail_id] = true;
			}
			else if($deniedById == $insurance_arr['SECONDARY']){
				$ins_heard_flag_arr["SECONDARY"][$charge_list_detail_id] = true;
			}
			else if($deniedById == $insurance_arr['TERTIARY']){
				$ins_heard_flag_arr["TERTIARY"][$charge_list_detail_id] = true;
			}
		}
		
		//---- GET PAYMENT Co-Ins AMOUNT ----
		$chld_acc_pay_arr=array();
		$getAccPayDetailsStr = "SELECT ins_id,charge_list_detail_id FROM account_payments WHERE del_status=0 
								and encounter_id='$encounter_id' and (payment_type='Co-Insurance' or payment_type='Co-Payment')";
		$getAccPayDetailsQry = imw_query($getAccPayDetailsStr);
		while($getAccPayDetailsRows = imw_fetch_array($getAccPayDetailsQry)){
			$co_ins_comp_id = $getAccPayDetailsRows['ins_id'];
			$charge_list_detail_id = $getAccPayDetailsRows['charge_list_detail_id'];
			if($co_ins_comp_id == $insurance_arr['PRIMARY']){
				$ins_heard_flag_arr["PRIMARY"][$charge_list_detail_id] = true;
			}
			else if($co_ins_comp_id == $insurance_arr['SECONDARY']){
				$ins_heard_flag_arr["SECONDARY"][$charge_list_detail_id] = true;
			}
			else if($co_ins_comp_id == $insurance_arr['TERTIARY']){
				$ins_heard_flag_arr["TERTIARY"][$charge_list_detail_id] = true;
			}
		}
		
		//--- GET DEDUCT AMOUNT FOR SINGLE ENCOUNTER ------		
		$qry = imw_query("select deduct_ins_id, charge_list_detail_id from payment_deductible 
				where charge_list_detail_id in ($charge_list_detail_id_str)
				and delete_deduct = '0' and deduct_ins_id > 0 
				group by deduct_ins_id, charge_list_detail_id");
		while($deniedQryRes = imw_fetch_array($qry)){
			$deduct_ins_id = $deniedQryRes['deduct_ins_id'];
			$charge_list_detail_id = $deniedQryRes['charge_list_detail_id'];
			if($deduct_ins_id == $insurance_arr['PRIMARY']){
				$ins_heard_flag_arr["PRIMARY"][$charge_list_detail_id] = true;
			}
			else if($deduct_ins_id == $insurance_arr['SECONDARY']){
				$ins_heard_flag_arr["SECONDARY"][$charge_list_detail_id] = true;
			}
			else if($deduct_ins_id == $insurance_arr['TERTIARY']){
				$ins_heard_flag_arr["TERTIARY"][$charge_list_detail_id] = true;
			}
		}
		
		//--- SET INSURANCE COMPANY HEARD FLAG ----					
		if(count($charge_list_detail_id_arr) == count($ins_heard_flag_arr["PRIMARY"])){
			$primary_paid = 'true';
		}
		if(count($charge_list_detail_id_arr) == count($ins_heard_flag_arr["SECONDARY"])){
			$secondary_paid = 'true';
		}
		if(count($charge_list_detail_id_arr) == count($ins_heard_flag_arr["TERTIARY"])){
			$tertiary_paid = 'true';
		}
		if($stop_clm_status<=0){
			if($insurance_arr['SECONDARY']>0){
				foreach($ins_heard_flag_arr["PRIMARY"] as $key => $val){
					if($ins_heard_flag_arr["PRIMARY"][$key]==true && $ins_heard_flag_arr["SECONDARY"][$key]!=true){
						imw_query("update patient_charge_list_details set claim_status='0' where charge_list_detail_id='$key'");
					}
				}
			}
			if($insurance_arr['TERTIARY']>0){
				foreach($ins_heard_flag_arr["PRIMARY"] as $key => $val){
					if($ins_heard_flag_arr["PRIMARY"][$key]==true && $ins_heard_flag_arr["SECONDARY"][$key]==true && $ins_heard_flag_arr["TERTIARY"][$key]!=true){
						imw_query("update patient_charge_list_details set claim_status='0' where charge_list_detail_id='$key'");
					}
				}
			}
			
			//--- UPDATE INSURANCE COMPANY PAID FLAG ----
			imw_query("update patient_charge_list set primary_paid = '$primary_paid',
			secondary_paid = '$secondary_paid',tertiary_paid = '$tertiary_paid'
			where encounter_id = '$encounter_id'");
		}
	}
	$dataArr = array();
	$dataArr['affected'] = imw_affected_rows();
	return $dataArr;		
}

//Get copay collect procedure
function copay_apply_chk($proc_code='',$pri_ins='',$sec_ins=''){
	$copay_collect=array();
	$copay_collect[0]=false;
	$copay_collect[1]=false;
	if($pri_ins){
		$pri_copay_collect=getInsuranceDetails($pri_ins);
		if($pri_copay_collect['collect_copay']==1){
			$copay_collect[0]=true;
		}
	}
	if($sec_ins){
		$sec_copay_collect=getInsuranceDetails($sec_ins);
		if($sec_copay_collect['collect_copay']==1){
			$copay_collect[1]=true;
		}
	}
	
	if($proc_code){
		$proc_code_arr=explode(',',$proc_code);
		$copay_apply_arr=array("99212","99213","99214","99215","99201","99202","99203","99204","99205","99241",
							 "99242","99243","99244","99245","92012","92013","92014","92002","92003","92004");
		$copay_apply_proc_code=array_intersect($copay_apply_arr,$proc_code_arr);
		if(count($copay_apply_proc_code)>0){
			$copay_apply_proc_imp=implode(',',$copay_apply_proc_code);
			$copay_collect[0]=true;
			$copay_collect[1]=true;
		}
	}
	return $copay_collect;
	
}

//Get insurance company detail
function getInsuranceDetails($id){
	$qry = imw_query("select * from insurance_companies where id = '$id'");
	if(imw_num_rows($qry)>0){
		$qryRes = imw_fetch_assoc($qry);
	}
	return $qryRes;
}

//Check copay for seconday insurance company
function ChkSecCopay_collect($pri_ins_id){
	$qry = imw_query("SELECT * FROM copay_policies WHERE policies_id='1'");
	$policyQryRes = imw_fetch_assoc($qry);
	
	$sec_copay_for_ins = $policyQryRes['sec_copay_for_ins'];
	$secondary_copay = $policyQryRes['secondary_copay'];
	if($secondary_copay=='Yes' && $sec_copay_for_ins==''){
		$sec_copay_collect="Yes";
	}else if($sec_copay_for_ins=='Medicare as Primary'){
		$qry = imw_query("SELECT id FROM insurance_companies WHERE 
				(in_house_code = 'medicare' or name like '%medicare%')
				and id='$pri_ins_id'");
		if(imw_num_rows($qry)>0){
			$sec_copay_collect="Yes";
		}
	}
	return $sec_copay_collect;
}

//Set encounter balance and overpaid
function set_payment_trans($enc_id,$arr='',$stop_clm_status=0){
	$curr_date_time = date('Y-m-d H:i:s');
	if($arr=="multi"){
		$enc_arr_id=implode(',',$enc_id);
		$encounter_id_arr=$enc_id;
	}else{
		$enc_arr_id=$enc_id;
		$encounter_id_arr[]=$enc_id;
	}
	if($enc_arr_id){
		$chl_data_qry = "SELECT patient_charge_list.charge_list_id,
					 patient_charge_list.encounter_id,
					 patient_charge_list.pri_copay,
					 patient_charge_list.sec_copay,
					 patient_charge_list.copayPaid,
					 patient_charge_list_details.pat_due,
					 patient_charge_list_details.pri_due,
					 patient_charge_list_details.sec_due,
					 patient_charge_list_details.tri_due,
					 patient_charge_list_details.charge_list_detail_id,
					 patient_charge_list_details.totalAmount,
					 patient_charge_list_details.approvedAmt,
					 patient_charge_list_details.coPayAdjustedAmount,
					 patient_charge_list.primaryInsuranceCoId,
					 patient_charge_list.secondaryInsuranceCoId,
					 patient_charge_list.tertiaryInsuranceCoId,
					 patient_charge_list_details.proc_selfpay,
					 patient_charge_list.date_of_service,
					 patient_charge_list_details.last_pri_paid_date,
					 patient_charge_list_details.last_sec_paid_date,
					 patient_charge_list_details.last_ter_paid_date,
					 patient_charge_list_details.last_pat_paid_date
					  FROM 
					 patient_charge_list join patient_charge_list_details
					 on patient_charge_list.charge_list_id = patient_charge_list_details.charge_list_id
					 where 
					 patient_charge_list_details.del_status='0' and 
					 patient_charge_list.encounter_id in($enc_arr_id)
					 order by patient_charge_list.encounter_id,patient_charge_list_details.charge_list_detail_id";
		$chl_data_run = imw_query($chl_data_qry);
		while($chl_data_row = imw_fetch_array($chl_data_run)){
			$charge_list_detail_id = $chl_data_row['charge_list_detail_id'];
			$encounter_id = $chl_data_row['encounter_id'];
			$chld_id_arr[] = $chl_data_row['charge_list_detail_id'];
			$charge_list_id = $chl_data_row['charge_list_id'];
			$totalAmount = $chl_data_row['totalAmount'];
			$approvedAmt = $chl_data_row['approvedAmt'];
			$copayPaid = $chl_data_row['copayPaid'];
			$coPayAdjustedAmount = $chl_data_row['coPayAdjustedAmount'];
			$total_pri_sec_copay = $chl_data_row['pri_copay']+$chl_data_row['sec_copay'];
			$pat_due = $chl_data_row['pat_due'];
			$pri_due = $chl_data_row['pri_due'];
			$sec_due = $chl_data_row['sec_due'];
			$tri_due = $chl_data_row['tri_due'];
			$chld_id_imp=implode(',',$chld_id_arr);
			
			if($chl_data_row['proc_selfpay']>0){
				imw_query("update tx_payments set del_status='1',del_operator_id='".$_SESSION['authId']."',del_date_time='$curr_date_time' where charge_list_detail_id='$charge_list_detail_id' and del_status='0'");
			}
			
			$ins_paid_date_arr=array();
			$pat_paid_date_arr=array();
			$deduct_total_amt_arr=array();
			$denied_total_amt_arr=array();
			$wd_total_amt_arr=array();
			$copay_total_amt_arr=array();
			//------------ deduct amount ----------------------
			$deduct_data_qry = "select deduct_amount,deduct_ins_id,deductible_by,deduct_date from 
								payment_deductible where 
								charge_list_detail_id in($charge_list_detail_id) and delete_deduct='0' order by deduct_date asc";
			$deduct_data_run = imw_query($deduct_data_qry);
			while($deduct_data_row = imw_fetch_array($deduct_data_run)){
				$deduct_total_amt_arr[] = $deduct_data_row['deduct_amount'];
				if($deduct_data_row['deductible_by']=="Insurance"){
					$ins_paid_date_arr[$deduct_data_row['deduct_ins_id']][]=$deduct_data_row['deduct_date'];
				}
			}
			$deduct_total_amt=array_sum($deduct_total_amt_arr);
			//------------ deduct amount -----------------------
			
			//------------ denied amount -----------------------
			$denied_data_qry = "select deniedAmount,deniedById,deniedBy,deniedDate from 
								deniedpayment where 
								charge_list_detail_id in($charge_list_detail_id) and denialDelStatus='0' order by deniedDate asc";
			$denied_data_run = imw_query($denied_data_qry);
			while($denied_data_row = imw_fetch_array($denied_data_run)){
				$denied_total_amt_arr[]= $denied_data_row['deniedAmount'];
				if($denied_data_row['deniedBy']=="Insurance"){
					$ins_paid_date_arr[$denied_data_row['deniedById']][]=$denied_data_row['deniedDate'];
				}
			}
			$denied_total_amt = array_sum($denied_total_amt_arr);
			$claimDenied="";
			if($denied_total_amt>0){
				 $claimDenied='1';
			}
			//------------ denied amount ---------------------
			$neg_paid_total_amt_arr=array();
			$paid_total_amt_arr=array();
			$paid_amt_qry = "select patient_charges_detail_payment_info.paidForProc,
							 patient_charges_detail_payment_info.overPayment,
							 patient_chargesheet_payment_info.paymentClaims,
							 patient_chargesheet_payment_info.encounter_id,
							 patient_chargesheet_payment_info.payment_id,
							 patient_chargesheet_payment_info.paid_by,
							 patient_chargesheet_payment_info.insProviderId,
							 patient_chargesheet_payment_info.date_of_payment
							 from patient_chargesheet_payment_info join 
							 patient_charges_detail_payment_info on
							 patient_chargesheet_payment_info.payment_id = patient_charges_detail_payment_info.payment_id
							 where patient_charges_detail_payment_info.charge_list_detail_id  in($charge_list_detail_id) 
							 and patient_charges_detail_payment_info.deletePayment='0'";
			$paid_amt_run = imw_query($paid_amt_qry);
			while($paid_data_row = imw_fetch_array($paid_amt_run)){
				if($paid_data_row['paymentClaims']=="Negative Payment"){
					$neg_paid_total_amt_arr[]=$paid_data_row['paidForProc']+$paid_data_row['overPayment'];	 
				}else{
					$paid_total_amt_arr[]=$paid_data_row['paidForProc']+$paid_data_row['overPayment']; 
				}
				if($paid_data_row['encounter_id']==0){
					$chk_payment_id=$paid_data_row['payment_id'];
					imw_query("update patient_chargesheet_payment_info set encounter_id='$encounter_id' 
					where payment_id=$chk_payment_id and encounter_id=0");
				}
				if($paid_data_row['paid_by']=="Insurance"){
					$ins_paid_date_arr[$paid_data_row['insProviderId']][]=$paid_data_row['date_of_payment'];
				}else{
					$pat_paid_date_arr[0][]=$paid_data_row['date_of_payment'];
				}
			}
			
			//------------ Paid and deposit and interest payment amount ---------------------
			$paid_total_amt=array_sum($paid_total_amt_arr);		 
			//------------ Paid and deposit and interest payment amount ---------------------
			
			//------------ Negative payment amount ---------------------
			$neg_paid_total_amt=array_sum($neg_paid_total_amt_arr);	 	 
			//------------ Negative payment amount ---------------------
			
			//----------------------- copay amount ---------------------
			$copay_total_amt=0;
			if($total_pri_sec_copay>0){
				$copay_amt_qry = "SELECT patient_charges_detail_payment_info.paidForProc,
								patient_chargesheet_payment_info.paid_by,
							 	patient_chargesheet_payment_info.insProviderId,
								patient_chargesheet_payment_info.date_of_payment
							 	FROM 
								patient_chargesheet_payment_info join 
								patient_charges_detail_payment_info on
								patient_chargesheet_payment_info.payment_id = patient_charges_detail_payment_info.payment_id
								WHERE patient_chargesheet_payment_info.encounter_id = '$encounter_id'
								AND patient_charges_detail_payment_info.charge_list_detail_id = 0
								and patient_charges_detail_payment_info.deletePayment='0'
								and patient_chargesheet_payment_info.unapply='0'
								and patient_chargesheet_payment_info.paymentClaims!='Negative Payment'
								ORDER BY patient_chargesheet_payment_info.payment_id DESC";
				$copay_amt_run = imw_query($copay_amt_qry);
				if($coPayAdjustedAmount>0){
					while($copay_data_row = imw_fetch_array($copay_amt_run)){
						$copay_total_amt_arr[]=$copay_data_row['paidForProc'];
						if($copay_data_row['paid_by']=="Insurance"){
							$ins_paid_date_arr[$copay_data_row['insProviderId']][]=$copay_data_row['date_of_payment'];
						}else{
							$pat_paid_date_arr[0][]=$copay_data_row['date_of_payment'];
						}
					}
					$copay_total_amt=array_sum($copay_total_amt_arr);; 	
					if($copayPaid==0 && $copay_total_amt>=$total_pri_sec_copay){
						imw_query("update patient_charge_list set copayPaid='1' where encounter_id=$encounter_id and copayPaid='0'");
					}
					
					if(array_sum($copay_total_amt_arr)<=0){
						imw_query("update patient_charge_list set copayPaid='0' where encounter_id=$encounter_id and copayPaid='1'");
						imw_query("update patient_charge_list_details set coPayAdjustedAmount='0' where charge_list_id='$charge_list_id' and coPayAdjustedAmount='1'");
					}
				}
			}
			//---------------------- copay amount ---------------------
			
			//------------ Writeoff and Discount amount ----------------------
			$wd_data_qry = "select write_off_amount,write_off_by_id,write_off_date from 
								paymentswriteoff where 
								charge_list_detail_id in($charge_list_detail_id) and delStatus='0'";
			$wd_data_run = imw_query($wd_data_qry);
			while($wd_data_row = imw_fetch_array($wd_data_run)){
				if($wd_data_row['write_off_by_id']>0){
					$ins_paid_date_arr[$wd_data_row['write_off_by_id']][]=$wd_data_row['write_off_date'];
				}else{
					$pat_paid_date_arr[0][]=$wd_data_row['write_off_date'];
				}
				$wd_total_amt_arr[]=$wd_data_row['write_off_amount'];
			}
			$wd_total_amt = array_sum($wd_total_amt_arr);
			//------------ Writeoff and Discount amount ----------------------
			
			$adj_total_amt_arr=array();
			$ovr_adj_total_amt_arr=array();
			$retchk_total_amt_arr=array();
			$adj_data_qry = "select payment_amount,payment_type,copay_chld_id from 
								account_payments where 
								charge_list_detail_id in($charge_list_detail_id)
								and del_status='0'";
			$adj_data_run = imw_query($adj_data_qry);
			while($adj_data_row = imw_fetch_array($adj_data_run)){
				if($adj_data_row['payment_type']=="Adjustment"){
					$adj_total_amt_arr[] = $adj_data_row['payment_amount'];
				}
				if($adj_data_row['payment_type']=="Over Adjustment"){
					$ovr_adj_total_amt_arr[] = $adj_data_row['payment_amount'];
				}
				if($adj_data_row['payment_type']=="Returned Check" && $adj_data_row['copay_chld_id']==0){
					$retchk_total_amt_arr[] = $adj_data_row['payment_amount'];
				}
			}
			
			//------------ adustment amount ----------------------
			$adj_total_amt = array_sum($adj_total_amt_arr);
			//------------ adustment amount ----------------------
			
			//------------ over adustment amount ----------------------
			$ovr_adj_total_amt = array_sum($ovr_adj_total_amt_arr);
			//------------ over adustment amount ----------------------
			
			//------------ return check amount ------------------------
			$retchk_total_amt = array_sum($retchk_total_amt_arr);
			//-------------------- return check amount ---------------------
		
			$ref_total_amt_arr=array();
			$deb_total_amt_arr=array();
			$cr_total_amt_arr=array();
			$ref_data_qry = "select amountApplied,crAppliedTo,charge_list_detail_id_adjust from 
								creditapplied where 
								charge_list_detail_id in($charge_list_detail_id)
								and delete_credit='0'";
			$ref_data_run = imw_query($ref_data_qry);
			while($ref_data_row = imw_fetch_array($ref_data_run)){
				if($ref_data_row['crAppliedTo']=="payment"){
					$ref_total_amt_arr[] = $ref_data_row['amountApplied'];
				}
				if($ref_data_row['crAppliedTo']=="adjustment"){
					$deb_total_amt_arr[] = $ref_data_row['amountApplied'];
				}
				if($ref_data_row['crAppliedTo']=="adjustment" && $ref_data_row['charge_list_detail_id_adjust']>0){
					//$cr_total_amt_arr[] = $ref_data_row['amountApplied'];
				}
			}
			//------------------- refund amount ----------------------------
			//$ref_total_amt=array_sum($ref_total_amt_arr);
			$ref_total_amt_new=array_sum($ref_total_amt_arr);
			//---------------------- refund amount -------------------------
			
			//---------------------- debit amount --------------------------
			$deb_total_amt=array_sum($deb_total_amt_arr);
			//--------------------- debit amount ---------------------------
			
			//-------------------- credit amount ---------------------------
			//$cr_total_amt=array_sum($cr_total_amt_arr);
			$cr_data_qry = "select sum(amountApplied) as amountApplied from 
								creditapplied where 
								charge_list_detail_id_adjust in($charge_list_detail_id)
								and crAppliedTo ='adjustment' 
								and charge_list_detail_id_adjust>0
								and delete_credit='0'";
			$cr_data_run = imw_query($cr_data_qry);
			$cr_data_row = imw_fetch_array($cr_data_run);
			$cr_total_amt = $cr_data_row['amountApplied'];
			//-------------------- credit amount ---------------------------
			
			$tot_writeoff_amt=$wd_total_amt;
			$tot_writeoff_amt_allow="";
			$final_ovr_amt=0;
			if($totalAmount>$approvedAmt){
				$allow_writeoff=$totalAmount-$approvedAmt;
				$tot_writeoff_amt=$allow_writeoff+$tot_writeoff_amt;
				$tot_writeoff_amt_allow=$allow_writeoff;
			}
			$tot_plus_amt_chk=$paid_total_amt+$ovr_adj_total_amt+$cr_total_amt;
			$tot_minus_amt_chk=$copay_total_amt+$wd_total_amt;
			$tot_minus_plus_chk=$ref_total_amt;
			$tot_minus_over_payment = $deb_total_amt+$neg_paid_total_amt+$adj_total_amt+$retchk_total_amt;
			
			if($tot_minus_amt_chk>$approvedAmt && $coPayAdjustedAmount>0){
				$tot_bal_amt_chk=0;
				$copay_ovr_pay=$tot_minus_amt_chk-$approvedAmt;
			}else{
				$tot_bal_amt_chk=$approvedAmt-$tot_minus_amt_chk;
			}
			
			if($tot_bal_amt_chk>=$tot_plus_amt_chk){
				$final_paid_amt=$tot_plus_amt_chk-$tot_minus_plus_chk;
				$final_bal_amt=$tot_bal_amt_chk-$final_paid_amt;
				$final_ovr_amt=0;
			}else{
				$tot_paid_amt_chk=$tot_plus_amt_chk-$tot_minus_plus_chk;
				if($tot_bal_amt_chk>$tot_paid_amt_chk){
					$final_paid_amt = $tot_paid_amt_chk;
					$final_bal_amt=$tot_bal_amt_chk-$final_paid_amt;
				}else{
					$final_paid_amt=$tot_bal_amt_chk-$tot_minus_plus_chk;
					$final_bal_amt=0;
				}
				if($tot_paid_amt_chk>$final_paid_amt){
					$final_ovr_amt=$tot_paid_amt_chk-$final_paid_amt;
				}else{
					$final_ovr_amt=0;
				}
			}
			if($final_ovr_amt>$ref_total_amt_new){
				$final_ovr_amt=$final_ovr_amt-$ref_total_amt_new;
			}else{
				$ref_total_amt_new_minus=$ref_total_amt_new-$final_ovr_amt;
				$final_paid_amt=$final_paid_amt-$ref_total_amt_new_minus;
				$final_bal_amt=$final_bal_amt+$ref_total_amt_new_minus;
				$final_ovr_amt=0;
			}
			if($tot_minus_over_payment>0){
				if($final_ovr_amt>=$tot_minus_over_payment){
					$final_ovr_amt=$final_ovr_amt-$tot_minus_over_payment;
				}else{
					$chk_deb_minus_ovr=$tot_minus_over_payment-$final_ovr_amt;
					$final_paid_amt=$final_paid_amt-$chk_deb_minus_ovr;
					$final_bal_amt=$final_bal_amt+$chk_deb_minus_ovr;
					$final_ovr_amt=0;
				}
			}
			if($final_ovr_amt>0){
				$tot_writeoff_copay_amt=0;
				if($coPayAdjustedAmount>0){
					if($copay_ovr_pay>0){
						$tot_writeoff_copay_amt=0;
					}else{
						if($copay_total_amt>0){
							$tot_writeoff_copay_amt=$copay_total_amt;
						}
					}
				}
				$chk_total_wrt_bal_amt=$totalAmount-($tot_writeoff_amt+$tot_writeoff_copay_amt);
				$chk_total_paid_amt=$final_paid_amt+$final_ovr_amt;
				$chk_total_paid_amt = str_replace(',','',number_format($chk_total_paid_amt,2));
				$chk_total_wrt_bal_amt = str_replace(',','',number_format($chk_total_wrt_bal_amt,2));
				if($chk_total_paid_amt>=$chk_total_wrt_bal_amt){
					$final_paid_amt=$chk_total_wrt_bal_amt;
					$final_ovr_amt=$chk_total_paid_amt-$chk_total_wrt_bal_amt;
					if($coPayAdjustedAmount>0){
						if($copay_ovr_pay>0){
							$final_ovr_amt=$chk_total_paid_amt+$copay_ovr_pay;
						}else{
							if($copay_total_amt>0){
								$final_ovr_amt=$final_ovr_amt+($copay_total_amt-$tot_writeoff_copay_amt);
							}
						}
					}
				}else{
					if($coPayAdjustedAmount>0){
						if($copay_ovr_pay>0){
							$final_paid_amt=0;
							$final_ovr_amt=$chk_total_paid_amt+$copay_ovr_pay;
						}
					}
				}
			}else{
				if($coPayAdjustedAmount>0){
					if($copay_ovr_pay>0){
						$final_ovr_amt=$final_paid_amt+$copay_ovr_pay;
						$final_paid_amt=0;
						$final_bal_amt=0;
					}
				}
			}
			$credit_amt=$ref_total_amt+$cr_total_amt;
			//$neg_paid_total_amt<=0
			if($final_paid_amt<0 && $ref_total_amt_new<=0){
				$final_paid_amt=0;
			}
			if($final_ovr_amt>0){
				$final_bal_amt=0;
			}
			$whr_tx="";
			if($final_bal_amt>0){
				$tot_due_amt=$pri_due+$sec_due+$tri_due+$pat_due;
				$diff_due_amt=$tot_due_amt-$final_bal_amt;
				//echo $newBalance.'-'.$tot_due_amt.'-'.$pri_due.'-'.$sec_due.'-'.$pat_due;
				if($diff_due_amt!=0){
					if($pri_due>0){
						$pri_due=$pri_due-$diff_due_amt;
					}else if($sec_due>0){
						$sec_due=$sec_due-$diff_due_amt;
					}else if($tri_due>0){
						$tri_due=$tri_due-$diff_due_amt;
					}else if($pat_due>0){
						$pat_due=$pat_due-$diff_due_amt;
					}
					if($pri_due<0){$pri_due=0;}
					if($sec_due<0){$sec_due=0;}
					if($tri_due<0){$tri_due=0;}
					if($pat_due<0){$pat_due=0;}
					if($pri_due==0 && $sec_due==0 && $tri_due==0){
						$pat_due=$final_bal_amt;	
					}
					$whr_tx=",pri_due=$pri_due,sec_due=$sec_due,tri_due=$tri_due,pat_due=$pat_due";
				}else{
					if($pat_due>$final_bal_amt){
						$whr_tx=",pat_due=$final_bal_amt";
					}
					if($pri_due==0 && $sec_due==0 && $tri_due==0){
						$whr_tx=",pat_due=$final_bal_amt";
					}
				}
			}else{
				$pri_due=0;
				$sec_due=0;
				$tri_due=0;
				$pat_due=0;
				$whr_tx=",pri_due=$pri_due,sec_due=$sec_due,tri_due=$tri_due,pat_due=$pat_due";
			}
			$last_pri_paid_date="";
			$last_sec_paid_date="";
			$last_tri_paid_date="";
			$last_pat_paid_date="";
			$last_sec_paid_date2="";
			$last_tri_paid_date2="";
			$from_sec_due_date="";
			$from_ter_due_date="";
			$from_pat_due_date="";
			if($chl_data_row['primaryInsuranceCoId']>0){
				$first_pri_paid_date=$ins_paid_date_arr[$chl_data_row['primaryInsuranceCoId']][0];
				rsort($ins_paid_date_arr[$chl_data_row['primaryInsuranceCoId']]);
				if($ins_paid_date_arr[$chl_data_row['primaryInsuranceCoId']][0]>$chl_data_row['last_pri_paid_date']){
					$last_pri_paid_date=$ins_paid_date_arr[$chl_data_row['primaryInsuranceCoId']][0];
				}
			}
			if($chl_data_row['secondaryInsuranceCoId']>0){
				$first_sec_paid_date=$ins_paid_date_arr[$chl_data_row['secondaryInsuranceCoId']][0];
				rsort($ins_paid_date_arr[$chl_data_row['secondaryInsuranceCoId']]);
				if($last_pri_paid_date>$chl_data_row['last_sec_paid_date'] || $ins_paid_date_arr[$chl_data_row['secondaryInsuranceCoId']][0]>$chl_data_row['last_sec_paid_date']){
					if($ins_paid_date_arr[$chl_data_row['secondaryInsuranceCoId']][0]>$last_pri_paid_date){
						$last_sec_paid_date=$ins_paid_date_arr[$chl_data_row['secondaryInsuranceCoId']][0];
						$last_sec_paid_date2=$ins_paid_date_arr[$chl_data_row['secondaryInsuranceCoId']][0];
					}else{
						$last_sec_paid_date=$last_pri_paid_date;
						$last_sec_paid_date2=$ins_paid_date_arr[$chl_data_row['secondaryInsuranceCoId']][0];
					}
				}
			}
			if($chl_data_row['tertiaryInsuranceCoId']>0){
				$first_tri_paid_date=$ins_paid_date_arr[$chl_data_row['tertiaryInsuranceCoId']][0];
				rsort($ins_paid_date_arr[$chl_data_row['tertiaryInsuranceCoId']]);
				if($last_sec_paid_date2>$chl_data_row['last_ter_paid_date'] || $ins_paid_date_arr[$chl_data_row['tertiaryInsuranceCoId']][0]>$chl_data_row['last_ter_paid_date']){
					if($ins_paid_date_arr[$chl_data_row['tertiaryInsuranceCoId']][0]>$last_sec_paid_date2){
						$last_tri_paid_date=$ins_paid_date_arr[$chl_data_row['tertiaryInsuranceCoId']][0];
						$last_tri_paid_date2=$ins_paid_date_arr[$chl_data_row['tertiaryInsuranceCoId']][0];
					}else{
						$last_tri_paid_date=$last_sec_paid_date2;
						$last_tri_paid_date2=$ins_paid_date_arr[$chl_data_row['tertiaryInsuranceCoId']][0];
					}
				}
			}
			$first_pat_paid_date=$pat_paid_date_arr[0][0];
			
			if($chl_data_row['secondaryInsuranceCoId']>0){
				if($chl_data_row['primaryInsuranceCoId']>0){
					$from_sec_due_date=$first_pri_paid_date;
				}else{
					$from_sec_due_date=$chl_data_row['date_of_service'];
				}
			} 
			
			if($chl_data_row['tertiaryInsuranceCoId']>0){
				if($chl_data_row['secondaryInsuranceCoId']>0){
					$from_ter_due_date=$first_sec_paid_date;
				}else if($chl_data_row['primaryInsuranceCoId']>0){
					$from_ter_due_date=$first_pri_paid_date;
				}else{
					$from_ter_due_date=$chl_data_row['date_of_service'];
				}
			}
			
			if($chl_data_row['tertiaryInsuranceCoId']>0){
				$from_pat_due_date=$first_tri_paid_date;
			}else if($chl_data_row['secondaryInsuranceCoId']>0){
				$from_pat_due_date=$first_sec_paid_date;
			}else if($chl_data_row['primaryInsuranceCoId']>0){
				$from_pat_due_date=$first_pri_paid_date;
			}else{
				$from_pat_due_date=$chl_data_row['date_of_service'];
			}
			
			rsort($pat_paid_date_arr[0]);
			if($pat_paid_date_arr[0][0]>$chl_data_row['last_pat_paid_date']){
				$last_pat_paid_date=$pat_paid_date_arr[0][0];
			}
			if($last_tri_paid_date2!="" || $chl_data_row['tertiaryInsuranceCoId']>0){
				if($last_tri_paid_date2!="" && $last_tri_paid_date2>$pat_paid_date_arr[0][0]){
					$last_pat_paid_date=$last_tri_paid_date2;
				}
			}else if($last_sec_paid_date2!="" || $chl_data_row['secondaryInsuranceCoId']>0){
				if($last_sec_paid_date2!="" && $last_sec_paid_date2>$pat_paid_date_arr[0][0]){
					$last_pat_paid_date=$last_sec_paid_date2;
				}
			}else if($last_pri_paid_date!="" || $chl_data_row['primaryInsuranceCoId']>0){
				if($last_pri_paid_date!="" && $last_pri_paid_date>$pat_paid_date_arr[0][0]){
					$last_pat_paid_date=$last_pri_paid_date;
				}
			}
			
			if($chl_data_row['proc_selfpay']>0 && $last_pat_paid_date=="" && $chl_data_row['last_pat_paid_date']==""){
				$last_pat_paid_date=$chl_data_row['date_of_service'];
			}
			if($chl_data_row['primaryInsuranceCoId']>0 && $last_pri_paid_date=="" && $chl_data_row['last_pri_paid_date']==""){
				$last_pri_paid_date=$chl_data_row['date_of_service'];
			}
			
			$tx_qry= imw_query("select payment_date,pri_due,sec_due,tri_due,pat_due from tx_payments where charge_list_detail_id='$charge_list_detail_id' and del_status='0' order by payment_date desc limit 0,1");
			$tx_run=imw_fetch_array($tx_qry);
			$tx_pay_date=$tx_run['payment_date'];
			if($tx_pay_date>$last_pri_paid_date && $tx_pay_date>$last_sec_paid_date && $tx_pay_date>$last_tri_paid_date && $tx_pay_date>$last_pat_paid_date){
				if($pri_due!=$tx_run['pri_due']){
					$last_pri_paid_date=$tx_pay_date;
				}
				if($sec_due!=$tx_run['sec_due']){
					$last_sec_paid_date=$tx_pay_date;
				}
				if($tri_due!=$tx_run['tri_due']){
					$last_ter_paid_date=$tx_pay_date;
				}
				if($pat_due!=$tx_run['pat_due']){
					$last_pat_paid_date=$tx_pay_date;
				}
			}
			$whr_last_pri_paid_date="";
			$whr_last_sec_paid_date="";
			$whr_last_ter_paid_date="";
			$whr_last_pat_paid_date="";
			if($last_pri_paid_date!=""){
				$whr_last_pri_paid_date=",last_pri_paid_date='$last_pri_paid_date'";
			}
			if($last_sec_paid_date!=""){
				$whr_last_sec_paid_date=",last_sec_paid_date='$last_sec_paid_date'";
			}
			if($last_tri_paid_date!=""){
				$whr_last_ter_paid_date=",last_ter_paid_date='$last_tri_paid_date'";
			}
			if($last_pat_paid_date!=""){
				$whr_last_pat_paid_date=",last_pat_paid_date='$last_pat_paid_date'";
			}
			
			$tx_qry= imw_query("select payment_date,pri_due,sec_due,tri_due,pat_due from tx_payments where charge_list_detail_id='$charge_list_detail_id' and del_status='0'");
			while($tx_run=imw_fetch_array($tx_qry)){
				$tx_pay_date=$tx_run['payment_date'];
				if($from_pat_due_date>$tx_pay_date  || $from_pat_due_date==""){
					if($pat_due!=$tx_run['pat_due'] && $pat_due>0){
						$from_pat_due_date=$tx_pay_date;
					}
				}
				if($from_ter_due_date>$tx_pay_date  || $from_ter_due_date==""){
					if($tri_due!=$tx_run['tri_due'] && $tri_due>0){
						$from_ter_due_date=$tx_pay_date;
					}
				}
				if($from_sec_due_date>$tx_pay_date || $from_sec_due_date==""){
					if($sec_due!=$tx_run['sec_due'] && $sec_due>0){
						$from_sec_due_date=$tx_pay_date;
					}
				}
			}
			
			$whr_from_sec_due_date="";
			$whr_from_ter_due_date="";
			$whr_from_pat_due_date="";
			if($from_sec_due_date!=""){
				$whr_from_sec_due_date=",from_sec_due_date='$from_sec_due_date'";
			}
			if($from_ter_due_date!=""){
				$whr_from_ter_due_date=",from_ter_due_date='$from_ter_due_date'";
			}
			if($from_pat_due_date!=""){
				$whr_from_pat_due_date=",from_pat_due_date='$from_pat_due_date'";
			}
			$chld_up="update patient_charge_list_details set 
					  write_off='$tot_writeoff_amt_allow',
					  deductAmt='$deduct_total_amt',
					  paidForProc='$final_paid_amt',
					  balForProc='$final_bal_amt',
					  newBalance='$final_bal_amt',
					  overPaymentForProc='$final_ovr_amt',
					  creditProcAmount='$credit_amt',
					  claimDenied='$claimDenied'
					  $whr_last_pri_paid_date $whr_last_sec_paid_date $whr_last_ter_paid_date $whr_last_pat_paid_date
					  $whr_from_sec_due_date $whr_from_ter_due_date $whr_from_pat_due_date
					  $whr_tx
					  where charge_list_detail_id = '$charge_list_detail_id'";
			imw_query($chld_up);	  
		}
		include(dirname(__FILE__)."/../../interface/accounting/manageEncounterAmounts.php");
	}

}

//DELETE ENCOUNTER AND PROCEDURE DETAILS
function del_enc($chl_id,$claim_ctrl_pri_send){
	$operator_id = $_SESSION['authId'];
	$curr_time = date('H:i:s');
	$curr_date = date('Y-m-d');
	$curr_date_time = date('Y-m-d H:i:s');
	$chld_arr=array();
	$pmt_arr=array();
	if($chl_id>0){
		
		$voided_encounter="";
		if($claim_ctrl_pri_send!=""){
			$voided_encounter=",void_notify='1'";
		}
		
		$qry = imw_query("select encounter_id,patient_id FROM patient_charge_list WHERE charge_list_id='$chl_id'");
		$chl_row = imw_fetch_assoc($qry);
		$encounter_id=$chl_row['encounter_id'];
		
		$copay_chld=0;
		$qry_chld=imw_query("SELECT charge_list_detail_id,coPayAdjustedAmount,patient_id FROM patient_charge_list_details WHERE charge_list_id='$chl_id'");
		while($chld_row=imw_fetch_assoc($qry_chld)){
			$chld_arr[$chld_row['charge_list_detail_id']]=$chld_row['charge_list_detail_id'];
			if($chld_row['coPayAdjustedAmount']>0){
				$copay_chld=$chld_row['charge_list_detail_id'];
			}
			$pat_id=$chld_row['patient_id'];
			$sel_def_wrt=imw_query("select write_off_amount,facility_id from defaultwriteoff where patient_id='$pat_id' and charge_list_detail_id='".$chld_row['charge_list_detail_id']."' and del_status='0' order by write_off_id desc limit 0,1");
			$row_def_wrt=imw_fetch_array($sel_def_wrt);
			if($row_def_wrt['write_off_amount']>0){
				imw_query("insert into defaultwriteoff set patient_id='".$pat_id."',encounter_id='".$encounter_id."',charge_list_id='".$chl_id."',charge_list_detail_id='".$chld_row['charge_list_detail_id']."',
				write_off_amount='0',write_off_operator_id='".$operator_id."',write_off_dop='".$curr_date."',write_off_dot='".$curr_date_time."',facility_id='".$row_def_wrt['facility_id']."'");
			}
		}
		$chld_imp=implode("','",$chld_arr);
		
		$qry_pmt=imw_query("SELECT payment_id FROM patient_chargesheet_payment_info WHERE encounter_id='$encounter_id'");
		while($pmt_row=imw_fetch_assoc($qry_pmt)){
			$pmt_arr[$pmt_row['payment_id']]=$pmt_row['payment_id'];
		}
		$pmt_imp=implode("','",$pmt_arr);
		
		$del_qry = imw_query("update account_payments set del_status='1',del_date_time='$curr_date_time',del_operator_id='$operator_id' WHERE charge_list_id = '$chl_id' and del_status='0'");
		$del_qry = imw_query("update check_in_out_payment_post set status='1',del_date_time='$curr_date_time',del_operator_id='$operator_id' WHERE encounter_id = '$encounter_id' and status='0'");
		$del_qry = imw_query("update creditapplied set delete_credit='1',del_date_time='$curr_date_time',del_operator_id='$operator_id' WHERE crAppliedToEncId = '$encounter_id' and crAppliedToEncId>0 and delete_credit='0'");
		$del_qry = imw_query("update creditapplied set delete_credit='1',del_date_time='$curr_date_time',del_operator_id='$operator_id' WHERE crAppliedToEncId_adjust = '$encounter_id' and crAppliedToEncId_adjust>0 and delete_credit='0'");
		$del_qry = imw_query("update deniedpayment set denialDelStatus='1',denialDelDate='$curr_date',denialDelTime='$curr_time',del_operator_id='$operator_id' WHERE encounter_id = '$encounter_id' and denialDelStatus='0'");
		$del_qry = imw_query("update payment_deductible set delete_deduct='1',delete_deduct_date='$curr_date',delete_deduct_time='$curr_time',delete_operator_id='$operator_id' WHERE charge_list_detail_id in('$chld_imp') and delete_deduct='0'");
		$del_qry = imw_query("update patient_chargesheet_payment_info set markPaymentDelete='1' WHERE encounter_id='$encounter_id' and markPaymentDelete='0'");
		$del_qry = imw_query("update patient_charges_detail_payment_info set deletePayment='1',deleteDate='$curr_date',deleteTime='$curr_time',del_operator_id='$operator_id' WHERE payment_id in('$pmt_imp') and deletePayment='0'");
		$del_qry = imw_query("update paymentswriteoff set delStatus='1',write_off_del_date='$curr_date',write_off_del_time='$curr_time',del_operator_id='$operator_id' WHERE encounter_id = '$encounter_id' and delStatus='0'");
		$del_qry = imw_query("UPDATE patient_charges_detail_payment_info SET charge_list_detail_id='$copay_chld' WHERE payment_id in('$pmt_imp') and charge_list_detail_id='0'");
		if($copay_chld>0){
			imw_query("UPDATE report_enc_trans SET trans_type='paid' WHERE charge_list_id ='$chl_id' and trans_type='copay-paid'");
		}	
		$sel_crd=imw_query("select crAppliedToEncId,crAppliedToEncId_adjust from creditapplied where crAppliedToEncId = '$encounter_id' or crAppliedToEncId_adjust = '$encounter_id'");
		if(imw_num_rows($sel_crd)>0){
			while($row_crd=imw_fetch_assoc($sel_crd)){
				$crd_encounter_id1=$row_crd['crAppliedToEncId'];
				$crd_encounter_id2=$row_crd['crAppliedToEncId_adjust'];
				if($crd_encounter_id1!=$encounter_id && $crd_encounter_id1>0){
					set_payment_trans($crd_encounter_id1);
				}
				if($crd_encounter_id2!=$encounter_id && $crd_encounter_id2>0){
					set_payment_trans($crd_encounter_id2);
				}
			}
		}
		
		set_payment_trans($encounter_id);	
		
		$del_qry = imw_query("update patient_charge_list set del_status='1',del_operator_id='$operator_id',trans_del_date='$curr_date_time',copayPaid='0',coPayPaidDate='0000-00-00',coPayAdjusted='',coPayAdjustedDate='0000-00-00' $voided_encounter WHERE charge_list_id = '$chl_id' and del_status='0'");
		$del_qry = imw_query("update patient_charge_list_details set del_status='1',del_operator_id='$operator_id',trans_del_date='$curr_date_time',paidForProc='0',balForProc=totalAmount,pri_due='0',sec_due='0',tri_due='0',pat_due=totalAmount,write_off='0',total_write_off='0',approvedAmt=totalAmount,
							  deductAmt='0',newBalance=totalAmount,coPayAdjustedAmount='0',overPaymentForProc='0',creditProcAmount='0' WHERE charge_list_id = '$chl_id' and del_status='0'");
	
	}
}

//Get procedure contract fee
function getContractFee($proc,$pri_ins,$reports=''){
	$contract_price="";
	$qry = imw_query("select billing_amount from copay_policies");
	$qryRes=imw_fetch_assoc($qry);
	if($qryRes['billing_amount']=='Default'){
		$contract_price=0;
		if($pri_ins>0){
			$qry_ins = imw_query("select FeeTable from insurance_companies where id = '$pri_ins'");
			$qry_feeRes = imw_fetch_assoc($qry_ins);
			
			$FeeTable = (int)$qry_feeRes['FeeTable'];
			if($FeeTable == 0 and empty($reports) === false){
				$FeeTable = 1;
			}
			if($FeeTable>0){
				$qry_cpt = imw_query("select cpt_fee_table.cpt_fee from cpt_fee_tbl
					join cpt_fee_table on cpt_fee_table.fee_table_column_id = '$FeeTable'
					where cpt_fee_tbl.cpt_prac_code='$proc'
					and cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id and cpt_fee_tbl.delete_status = '0'");
				$qry_feeRes1 = imw_fetch_assoc($qry_cpt);
				$contract_price = $qry_feeRes1['cpt_fee'];
				if(imw_num_rows($qry_cpt)==0){
					$qry_cpt = imw_query("select cpt_fee_table.cpt_fee from cpt_fee_tbl
					join cpt_fee_table on cpt_fee_table.fee_table_column_id = '$FeeTable'
					where (cpt_fee_tbl.cpt4_code='$proc' OR cpt_fee_tbl.cpt_desc='$proc')
					and cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id and cpt_fee_tbl.delete_status = '0'");
					$qry_feeRes1 = imw_fetch_assoc($qry_cpt);
					$contract_price = $qry_feeRes1['cpt_fee'];
				}
			}
		}	
	}
	return $contract_price;
}

//Get procedure code
function get_proc_code($chargeListId){
	$qry=imw_query("SELECT b.cpt4_code FROM patient_charge_list_details a, cpt_fee_tbl b
		WHERE a.del_status='0' and charge_list_id='$chargeListId' AND a.procCode=b.cpt_fee_id");
	$proc_Arr = array();
	while($qryRes=imw_fetch_assoc($qry)){
		$proc_Arr[] =$qryRes['cpt4_code'];
	}
	$proc_Arr_imp=implode(',',$proc_Arr);
	return $proc_Arr_imp;
}

function show_opr_init($val){
	$opr_name_exp=explode(', ',$val);
	$opr_name_mid_exp=explode(' ',$opr_name_exp[1]);
    $opr_init_name= substr($opr_name_exp[1],0,1).substr($opr_name_mid_exp[1],0,1).substr($opr_name_exp[0],0,1);
	return strtoupper($opr_init_name);
}

function patient_proc_tx_update($chld,$pay_amt,$pay_type,$ins_type){
	if(empty($chld) === false){
		$qry=imw_query("select * from patient_charge_list_details where del_status='0' and charge_list_detail_id='$chld'");
		$row=imw_fetch_array($qry);
		$procCode=$row['procCode'];
		$newBalance=$row['newBalance'];
		$chld_pri_due=$row['pri_due'];
		$chld_sec_due=$row['sec_due'];
		$chld_tri_due=$row['tri_due'];
		$chld_pat_due=$row['pat_due'];
		$proc_selfpay=$row['proc_selfpay'];
		$charge_list_id=$row['charge_list_id'];
		
		$qry_chl=imw_query("select * from patient_charge_list where del_status='0' and charge_list_id='$charge_list_id'");
		$row_chl=imw_fetch_array($qry_chl);
		$primaryInsuranceCoId=$row_chl['primaryInsuranceCoId'];
		$secondaryInsuranceCoId=$row_chl['secondaryInsuranceCoId'];
		$tertiaryInsuranceCoId=$row_chl['tertiaryInsuranceCoId'];
		
		$getProcedureDetailsStr = "SELECT b.cpt_prac_code,b.cpt_desc,b.not_covered,b.cpt_fee_id
									FROM cpt_fee_tbl as b
									WHERE b.not_covered=1
									and b.cpt_fee_id='$procCode'";
		$getProcedureDetailsQry = imw_query($getProcedureDetailsStr);
		if(imw_num_rows($getProcedureDetailsStr)>0 or $proc_selfpay>0){
			$pri_due=0;
			$sec_due=0;
			$tri_due=0;
			$pat_due=$newBalance;
		}else{
			if($pay_type=="ins" && $ins_type>0){
				
				if($ins_type==1){
					$pri_due=0;
					$add_amt=0;
					$add_amt=$chld_pri_due-$pay_amt;
					if($add_amt<0){
						$add_amt=-$add_amt;
					}
					$sec_due=$chld_sec_due;
					$tri_due=$chld_tri_due;
					$pat_due=$chld_pat_due;
					
					if($add_amt>0 && $secondaryInsuranceCoId>0){
						$sec_due=$sec_due+$add_amt;
					}else if($add_amt>0 && $tertiaryInsuranceCoId>0){
						$tri_due=$tri_due+$add_amt;
					}else{
						if($add_amt>0){
							$pat_due=$pat_due+$add_amt;
						}
					}
				}else if($ins_type==2){
					$sec_due=0;
					$add_amt=0;
					$pri_due=$chld_pri_due;
					$add_amt=$chld_sec_due-$pay_amt;
					if($add_amt<0){
						$add_amt=-$add_amt;
					}
					$tri_due=$chld_tri_due;
					$pat_due=$chld_pat_due;
					if($add_amt>0 && $tertiaryInsuranceCoId>0){
						$tri_due=$tri_due+$add_amt;
					}else{
						if($add_amt>0){
							$pat_due=$pat_due+$add_amt;
						}
					}
				}else if($ins_type==3){
					$tri_due=0;
					$add_amt=0;
					$pri_due=$chld_pri_due;
					$sec_due=$chld_sec_due;
					$add_amt=$chld_tri_due-$pay_amt;
					if($add_amt<0){
						$add_amt=-$add_amt;
					}
					$pat_due=$chld_pat_due;
					if($add_amt>0){
						$pat_due=$pat_due+$add_amt;
					}
				}
			}else{
				$pri_due=$chld_pri_due;
				$sec_due=$chld_sec_due;
				$tri_due=$chld_tri_due;
				$pat_due=$chld_pat_due-$pay_amt;
			}
		}
		if($pri_due<0){$pri_due=0;}
		if($sec_due<0){$sec_due=0;}
		if($tri_due<0){$tri_due=0;}
		if($pat_due<0){$pat_due=0;}
		if($newBalance<=0){
			$pri_due=0;
			$sec_due=0;
			$tri_due=0;
			$pat_due=0;
		}else{
			$tot_due_amt=$pri_due+$sec_due+$tri_due+$pat_due;
			$diff_due_amt=$tot_due_amt-$newBalance;
			//echo $newBalance.'-'.$tot_due_amt.'-'.$pri_due.'-'.$sec_due.'-'.$pat_due;
			if($diff_due_amt!=0){
				if($pri_due>0){
					$pri_due=$pri_due-$diff_due_amt;
				}else if($sec_due>0){
					$sec_due=$sec_due-$diff_due_amt;
				}else if($tri_due>0){
					$tri_due=$tri_due-$diff_due_amt;
				}else if($pat_due>0){
					$pat_due=$pat_due-$diff_due_amt;
				}
				if($pri_due<0){$pri_due=0;}
				if($sec_due<0){$sec_due=0;}
				if($tri_due<0){$tri_due=0;}
				if($pat_due<0){$pat_due=0;}
				if($pri_due==0 && $sec_due==0 && $tri_due==0){
					$pat_due=$newBalance;	
				}
			}
		}
		$up_qry="update patient_charge_list_details set pri_due=$pri_due,sec_due=$sec_due,tri_due=$tri_due,pat_due=$pat_due where charge_list_detail_id ='$chld'";
		imw_query($up_qry);
	}
}
function outstandingSBExists($eId){
	$sql="SELECT encounterId FROM superbill WHERE encounterId='".$eId."' and patientId='".$_SESSION['patient']."' and postedStatus ='0' and del_status='0' ";
	$row=imw_query($sql);
	$ret=0;
	if($row!=false){
		$ret=1;
	}
	if($ret==0){
		$sql2=imw_query("SELECT encounter_id FROM patient_charge_list WHERE encounter_id='".$eId."' and patient_id='".$_SESSION['patient']."' and del_status ='0'");
		$row2=imw_num_rows($sql2);
		if($row2==0){
			$ret=1;
		}
	}
	return $ret;
}

function set_app_id_chl($patient_id,$dos,$tbl_cond){
	$sch_qry=imw_query("select id,sa_app_start_date,sa_doctor_id from schedule_appointments where sa_app_start_date='$dos' and sa_patient_id='$patient_id' and sa_patient_app_status_id NOT IN (203,201,18,19,20) order by sa_app_start_date,sa_app_starttime asc");
	if(imw_num_rows($sch_qry)>0){
		if(imw_num_rows($sch_qry)==1){
			$sch_row=imw_fetch_array($sch_qry);
			$sch_app_id=$sch_row['id'];
			$sa_app_start_date=$sch_row['sa_app_start_date'];
			if($tbl_cond=="sup"){
				imw_query("update superbill set sch_app_id='$sch_app_id' where del_status='0' and dateOfService='$dos' and patientId='$patient_id' and sch_app_id='0'");
			}else{
				imw_query("update patient_charge_list set sch_app_id='$sch_app_id' where del_status='0' and date_of_service='$dos' and patient_id='$patient_id' and sch_app_id='0'");
			}
		}else{
			$qry="Select id, user_type FROM users";
			$rs = imw_query($qry);
			while($res=imw_fetch_array($rs)){
				$allUsers[$res['id']] = $res['user_type'];
			}
			
			$sch_app_arr=array();
			while($sch_row=imw_fetch_array($sch_qry)){
				if($allUsers[$sch_row['sa_doctor_id']]=='1'){
					$sch_app_arr[$sch_row['id']]=$sch_row['sa_app_start_date'];
					$sch_app_dos_arr[$sch_row['sa_app_start_date']]=$sch_row['id'];
				}else{
					$sch_app_other_arr[$sch_row['id']]=$sch_row['sa_app_start_date'];
					$sch_app_dos_other_arr[$sch_row['sa_app_start_date']]=$sch_row['id'];
				}
			}
			foreach($sch_app_other_arr as $app_key => $app_value){
				$sch_app_arr[$app_key]=$app_value;
				$sch_app_dos_arr[$app_value]=$app_key;
			}
			
			if($tbl_cond=="sup"){
				$chl_qry=imw_query("select sch_app_id,encounterId as encounter_id,dateOfService as date_of_service from superbill where del_status='0' and dateOfService='$dos' and patientId='$patient_id'");
			}else{
				$chl_qry=imw_query("select sch_app_id,encounter_id,date_of_service from patient_charge_list where del_status='0' and date_of_service='$dos' and patient_id='$patient_id'");
			}
			$chl_app_arr=array();
			$chl_app_dos_arr=array();
			while($chl_row=imw_fetch_array($chl_qry)){
				$chl_app_arr[$chl_row['encounter_id']]=$chl_row['sch_app_id'];
				$chl_app_dos_arr[$chl_row['encounter_id']]=$chl_row['date_of_service'];
			}
			$last_sch_id_arr=array();
			$up_sch_id="";
			foreach($chl_app_arr as $sch_enc_id => $sch_id){
				$chl_app_dos_val="";
				$chl_app_dos_val=$chl_app_dos_arr[$sch_enc_id];
				foreach($sch_app_arr as $sch_app_id => $sch_app_dos){
					if(!in_array($sch_app_id,$last_sch_id_arr) && $chl_app_dos_val==$sch_app_dos){
						$up_sch_id=$sch_app_id;
						break;
					}
				}
				if($sch_app_arr[$sch_id]=="" && $up_sch_id>0){
					if($tbl_cond=="sup"){
						imw_query("update superbill set sch_app_id='$up_sch_id' where del_status='0' and encounterId='$sch_enc_id' and patientId='$patient_id' and sch_app_id='0' and encounterId>0");
					}else{
						imw_query("update patient_charge_list set sch_app_id='$up_sch_id' where del_status='0' and encounter_id='$sch_enc_id' and patient_id='$patient_id' and sch_app_id='0' and encounter_id>0");
					}
					$last_sch_id=$up_sch_id;
					$last_sch_id_arr[]=$up_sch_id;
				}else{
					$last_sch_id=$sch_id;
					$last_sch_id_arr[]=$sch_id;
				}
			}
			if($tbl_cond=="sup"){
				imw_query("update superbill set sch_app_id='$last_sch_id' where del_status='0' and  dateOfService='$dos' and patientId='$patient_id' and sch_app_id='0'");
			}else{
				imw_query("update patient_charge_list set sch_app_id='$last_sch_id' where del_status='0' and date_of_service='$dos' and patient_id='$patient_id' and sch_app_id='0'");
			}
		}
	}
}

function getActInsDetail($patientId,$caseId,$type,$enc_dos,$old_id){
	if($old_id>0){
		$old_id_whr="provider='$old_id'";
	}else{
		$new_id_whr="(date_format(effective_date,'%Y-%m-%d')<='$enc_dos')
			and (expiration_date = '0000-00-00 00:00:00' 
			or date_format(expiration_date,'%Y-%m-%d') >= '$enc_dos')";
	}
	$qry = imw_query("select * from insurance_data where pid = '$patientId'
			and ins_caseid = '$caseId' and provider > '0'
			and type = '$type' 
			and $old_id_whr $new_id_whr 
			order by actInsComp desc limit 0,1");		
	if(imw_num_rows($qry)>0){
		$qryRes = imw_fetch_object($qry);
	}
	return $qryRes;
}

function getFacilityNameRow($id){
	$qry = "select * from pos_facilityies_tbl";
	if($id){
		$patientDetails = getPatientData($id);
		$qry .= " where pos_facility_id = '".$patientDetails->default_facility."'";
	}
	$qry .= " order by headquarter desc limit 0,1";
	
	$qry_exc = imw_query($qry);			
	if(imw_num_rows($qry_exc)>0){
		$qryRes = imw_fetch_object($qry_exc);
	}
	return $qryRes;		
}

function getPatientData($id){	
	$qry = imw_query("select * from patient_data where id in ($id)");
	if(imw_num_rows($qry)>0){
		$qryRes = imw_fetch_object($qry);
	}
	return $qryRes;
}

function posFacilityDetails($id,$ord_by="facility_name"){
	$qry = imw_query("select a.* from pos_facilityies_tbl a,pos_tbl b
				 where b.pos_prac_code = '$id' and b.pos_id = a.pos_id order by a.".$ord_by);
	if(imw_num_rows($qry)>0){
		while($row = imw_fetch_object($qry)){
			$return[] = $row;
		};
	}
	return $return;
}
function posFacilityDetail($id){
	$qry = imw_query("select * from pos_facilityies_tbl where pos_facility_id in ($id)");
	if(imw_num_rows($qry)>0){
		$return = imw_fetch_object($qry);
	}
	return $return;
}
function set_due_by_posted($enc_id,$chld_ids,$posted_by,$ae_val='0'){
	if($chld_ids!=""){
		$whr_chld_ids=" and patient_charge_list_details.charge_list_detail_id in($chld_ids)";
	}
	$operatorId = $_SESSION['authUserID'];
	$paidDate = date('Y-m-d');
	$entered_date = date('Y-m-d H:i:s');
	$chl_data_qry = "SELECT patient_charge_list.charge_list_id,
					 patient_charge_list.patient_id,
					 patient_charge_list_details.pat_due,
					 patient_charge_list_details.pri_due,
					 patient_charge_list_details.sec_due,
					 patient_charge_list_details.tri_due,
					 patient_charge_list_details.charge_list_detail_id,
					 patient_charge_list_details.newBalance
					  FROM 
					 patient_charge_list join patient_charge_list_details
					 on patient_charge_list.charge_list_id = patient_charge_list_details.charge_list_id
					 join cpt_fee_tbl
					 on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
					 where 
					 patient_charge_list_details.del_status='0' and 
					 patient_charge_list.encounter_id=$enc_id 
					 $whr_chld_ids
					 and patient_charge_list_details.newBalance>0
					 and (patient_charge_list.primaryInsuranceCoId>0 or
					 patient_charge_list.secondaryInsuranceCoId>0 or
					 patient_charge_list.tertiaryInsuranceCoId>0)
					 and ((patient_charge_list_details.proc_selfpay!='1' and cpt_fee_tbl.not_covered = '0') or 
					 (patient_charge_list_details.pri_due>0 or patient_charge_list_details.sec_due>0 or patient_charge_list_details.tri_due>0))
					 and patient_charge_list.hl7_order_id='' and patient_charge_list_details.hl7_sub_order_id='' and patient_charge_list.opt_order_id<=0";
		$chl_data_run = imw_query($chl_data_qry);
		while($chl_data_row = imw_fetch_array($chl_data_run)){
			$pat_due_old=$chl_data_row['pat_due'];
			$pri_due_old=$chl_data_row['pri_due'];
			$sec_due_old=$chl_data_row['sec_due'];
			$tri_due_old=$chl_data_row['tri_due'];
			$charge_list_id=$chl_data_row['charge_list_id'];
			$post_patient_id=$chl_data_row['patient_id'];
			$charge_list_detail_id=$chl_data_row['charge_list_detail_id'];
			$newBalance=$chl_data_row['newBalance'];
			$chk_post=true;
			if($posted_by==3){
				if($newBalance==$tri_due_old){
					$chk_post=false;
				}
			}else if($posted_by==2){
				if($newBalance==$sec_due_old){
					$chk_post=false;
				}
			}else{
				if($newBalance==$pri_due_old){
					$chk_post=false;
				}
				if($ae_val>0){
					if($newBalance>$pat_due_old){
						$chk_post=true;
					}else{
						$chk_post=false;
					}
				}
			}
			if($chk_post==true){
				$insertAdjStr3 = "INSERT INTO tx_payments SET
									patient_id = '$post_patient_id',
									encounter_id = '$enc_id',
									charge_list_id = '$charge_list_id',
									charge_list_detail_id = '$charge_list_detail_id',
									pri_due='$pri_due_old',
									sec_due='$sec_due_old',
									tri_due='$tri_due_old',
									pat_due='$pat_due_old',
									payment_date='$paidDate',
									operator_id='$operatorId',
									entered_date='$entered_date'";
				$insertAdjQry3 = imw_query($insertAdjStr3);
				if($posted_by==3){
					$insertAdjStr4 = "update patient_charge_list_details SET
									pri_due = '0',
									sec_due = '0',
									tri_due = '$newBalance',
									pat_due = '0'
									WHERE charge_list_detail_id='$charge_list_detail_id'
									";
				}else if($posted_by==2){
					$insertAdjStr4 = "update patient_charge_list_details SET
									pri_due = '0',
									sec_due = '$newBalance',
									tri_due = '0',
									pat_due = '0'
									WHERE charge_list_detail_id='$charge_list_detail_id'
									";
				}else{
					if($ae_val>0){
						$insertAdjStr4 = "update patient_charge_list_details SET
									pri_due = '0',
									sec_due = '0',
									tri_due = '0',
									pat_due = '$newBalance'
									WHERE charge_list_detail_id='$charge_list_detail_id'";
					}else{
						$insertAdjStr4 = "update patient_charge_list_details SET
									pri_due = '$newBalance',
									sec_due = '0',
									tri_due = '0',
									pat_due = '0'
									WHERE charge_list_detail_id='$charge_list_detail_id'
									";
					}
				}
				$insertAdjQry4 = imw_query($insertAdjStr4);
			}
		}
}
function ShowValidChargeList($charge_list_id,$type){
	if($type == 'primary'){
		$SecondType = 'secondary';
	}
	if($type == 'secondary'){
		$SecondType = 'primary';
	}
	if(count($charge_list_id)>0){
		foreach($charge_list_id as $chargeId){
			$qry = imw_query("select * from patient_charge_list where del_status='0' and charge_list_id = '$chargeId'");
			$chargeListDetail = imw_fetch_object($qry);
			$reffPhyscianId = $chargeListDetail->reff_phy_id;
			$ins_caseid = $chargeListDetail->case_type_id;
			$patient_id = $chargeListDetail->patient_id;
			$date_of_service = $chargeListDetail->date_of_service;
			$reff_phy_nr = $chargeListDetail->reff_phy_nr;
			if($chargeListDetail->primaryInsuranceCoId>0 || $chargeListDetail->secondaryInsuranceCoId>0 || $chargeListDetail->tertiaryInsuranceCoId>0){
				$qry = imw_query("select * from patient_data where id = '".$chargeListDetail->patient_id."'");
				$patientDetail = imw_fetch_object($qry);
				if($reffPhyscianId == 0 || $reffPhyscianId == ''){
					$reffPhyscianId = $patientDetail->providerID;
					$qry = imw_query("select * from users where id = '$reffPhyscianId'");
					$reffDetail = imw_fetch_object($qry);
					$reffPhysicianLname = $reffDetail->lname;
					$reffPhysicianFname = $reffDetail->fname;
					$reffPhysicianMname = $reffDetail->mname;					
					$npiNumber = $reffDetail->user_npi;
					$Texonomy = $reffDetail->TaxonomyId;
				}
				else{
					$qry = imw_query("select * from refferphysician where physician_Reffer_id = '$reffPhyscianId'");
					$reffDetail = imw_fetch_object($qry);
					$reffPhysicianLname = $reffDetail->LastName;
					$reffPhysicianFname = $reffDetail->FirstName;
					$reffPhysicianMname = $reffDetail->MiddleName;
					$npiNumber = $reffDetail->NPI;
					$Texonomy = $reffDetail->Texonomy;
				}
				$patient_name = ucwords(trim($patientDetail->patientLname.", ".$patientDetail->patientFname." ".substr($patientDetail->patientMname,0,1)));
				
				$qry = imw_query("select * from users where id = '".$chargeListDetail->primaryProviderId."'");
				$renderingPhyDetail = imw_fetch_object($qry);
				if($chargeListDetail->primaryInsuranceCoId==0){
					$all_error[$chargeId][] = 'Patient Primary Infomation is Required.';
				}
				if($patientDetail->sex == ''){
					$all_error[$chargeId][] = 'Patient Gender Infomation is Required.';
				}
				if($renderingPhyDetail->user_npi == ''){
					$all_error[$chargeId][] = 'Rendering Physician NPI # is Required.';
				}
				if($renderingPhyDetail->TaxonomyId == ''){
					$all_error[$chargeId][] = 'Rendering Physician Taxonomy # is Required.';
				}
				if($reff_phy_nr==0){
					if($reffPhyscianId<=0 && $type=="elect"){
						$all_error[$chargeId][] = 'Referring Physician is Required.';
					}
					if($npiNumber == ''){
						$all_error[$chargeId][] = 'Referring Physician NPI # is Required.';
					}
				}
				
				if($type == 'tertiary'){
				}else{
					//--CORRECTING SUBSCRIBER GENDER IF MISSING----
					imw_query("UPDATE insurance_data, patient_data SET insurance_data.subscriber_sex=patient_data.sex WHERE patient_data.id=insurance_data.pid AND patient_data.sex != '' AND (insurance_data.subscriber_sex = '' || insurance_data.subscriber_sex is NULL) AND insurance_data.subscriber_relationship = 'self' AND insurance_data.pid='$patient_id'");
					$subscriberDetails = getInsGroupNumber($ins_caseid,$patient_id,$type,$date_of_service,'1');			
					//---- Patient Validate Check -------
					
					$qry = imw_query("select * from insurance_companies where id = '".$subscriberDetails->provider."'");
					$otherInsDetail = imw_fetch_object($qry);
					//---- INSURANCE COMPANY NAME FOR DISPLAY ---
					
					$payer_id=trim($otherInsDetail->Payer_id);
					$payment_method = trim($otherInsDetail->Insurance_payment);
					if($type == 'secondary'){
						$payment_method = trim($otherInsDetail->secondary_payment_method);
					}
					if($otherInsDetail->institutional_type=="INST_PROF"){
						$payer_id=trim($otherInsDetail->Payer_id_pro);
					}
					
					$show_type=ucfirst($type);
					
					if($payer_id == '' && $payment_method == "Electronics"){
						$all_error[$chargeId][] = "$show_type Insurance Carrier Payer Id is Required.";
					}
					else if(strlen($payer_id) < 3 && $payment_method == "Electronics"){
						$all_error[$chargeId][] = "$show_type Insurance Carrier Payer Id minimum length violation.";
					}
					if($payer_id == 'SPRNT' && $payment_method == "Electronics"){
						if($otherInsDetail->contact_address == '' || $otherInsDetail->City == '' || $otherInsDetail->State == '' || $otherInsDetail->Zip ==''){
							$all_error[$chargeId][] = '$show_type Insurance Carrier Address information is required.';
						}
					}
					if(trim($subscriberDetails->policy_number) == '' && $payment_method == "Electronics"){
						$all_error[$chargeId][] = "$show_type Insurance Carrier Policy # is Required.";
					}
					if($subscriberDetails->subscriber_street == '0'){ 
						$subscriberDetails->subscriber_street = '';
					}
					if(trim($subscriberDetails->subscriber_street) == '' && $payment_method == "Electronics"){
						$all_error[$chargeId][] = "$show_type Subscriber Address is Required.";
					}
					if(trim($subscriberDetails->subscriber_postal_code) == '' && $payment_method == "Electronics"){
						$all_error[$chargeId][] = "$show_type Subscriber Postal Code is Required.";
					}
					if(trim($subscriberDetails->subscriber_state) == '' && $payment_method == "Electronics"){
						$all_error[$chargeId][] = "$show_type Subscriber State is Required.";
					}
					if(trim($subscriberDetails->subscriber_city) == '' && $payment_method == "Electronics"){
						$all_error[$chargeId][] = "$show_type Subscriber City is Required.";
					}
					if(trim($subscriberDetails->subscriber_lname) == '' && $payment_method == "Electronics"){
						$all_error[$chargeId][] = "$show_type Subscriber Last Name is Required.";
					}
					if(trim($subscriberDetails->subscriber_fname) == '' && $payment_method == "Electronics"){
						$all_error[$chargeId][] = "$show_type Subscriber First Name is Required.";
					}
					if(trim($subscriberDetails->subscriber_sex) == '' && $payment_method == "Electronics"){
						$all_error[$chargeId][] = "$show_type Subscriber Gender Information is Required.";
					}
					if(trim($subscriberDetails->subscriber_DOB) == '0000-00-00' && $payment_method == "Electronics"){
						$all_error[$chargeId][] = "$patient_name $show_type Subscriber Date of Birth is Required.";
					}
					
					
					$subscriberDetails2 = getInsGroupNumber($ins_caseid,$patient_id,$SecondType,$date_of_service,'1');
					
					$qry = imw_query("select * from insurance_companies where id = '".$subscriberDetails2->provider."'");
					$otherInsDetail2 = imw_fetch_object($qry);
					
					$show_SecondType=ucfirst($SecondType);
					
					$payer_id2=trim($otherInsDetail2->Payer_id);
					if($otherInsDetail2->institutional_type=="INST_PROF"){
						$payer_id2=trim($otherInsDetail2->Payer_id_pro);
					}
					if($SecondType == 'primary'){
						if(trim($subscriberDetails2->policy_number) == '' && $payment_method == "Electronics"){
							$all_error[$chargeId][] = "$show_SecondType Insurance Carrier Policy # is Required.";
						}
						
						if($payer_id2 == '' && $payment_method == "Electronics"){
							$all_error[$chargeId][] = "$show_SecondType Insurance Carrier Payer Id is Required.";
						}
						if($payer_id2 == 'SPRNT' && $payment_method == "Electronics"){
							if($otherInsDetail2->contact_address == '' || $otherInsDetail2->City == '' || $otherInsDetail2->State == '' || $otherInsDetail2->Zip ==''){
								$all_error[$chargeId][] = '$show_SecondType Insurance Carrier Address information is required.';
							}
						}
						if($subscriberDetails2->subscriber_street == '0') 
							$subscriberDetails2->subscriber_street = '';
						if(trim($subscriberDetails2->subscriber_street) == '' && $payment_method == "Electronics"){
							$all_error[$chargeId][] = "$show_SecondType Subscriber Address is Required.";
						}
						if(trim($subscriberDetails2->subscriber_postal_code) == '' && $payment_method == "Electronics"){
							$all_error[$chargeId][] = "$show_SecondType Subscriber Postal Code is Required.";
						}
						if(trim($subscriberDetails2->subscriber_state) == '' && $payment_method == "Electronics"){
							$all_error[$chargeId][] = "$show_SecondType Subscriber State is Required.";
						}
						if(trim($subscriberDetails2->subscriber_city) == '' && $payment_method == "Electronics"){
							$all_error[$chargeId][] = "$show_SecondType Subscriber City is Required.";
						}
						if(trim($subscriberDetails2->subscriber_lname) == '' && $payment_method == "Electronics"){
							$all_error[$chargeId][] = "$show_SecondType Subscriber Last Name is Required.";
						}
						if(trim($subscriberDetails2->subscriber_fname) == '' && $payment_method == "Electronics"){
							$all_error[$chargeId][] = "$show_SecondType Subscriber First Name is Required.";
						}
						if(trim($subscriberDetails2->subscriber_sex) == '' && $payment_method == "Electronics"){
							$all_error[$chargeId][] = "$show_SecondType Subscriber Gender Information is Required.";
						}
						if(trim($subscriberDetails2->subscriber_DOB) == '0000-00-00' && $payment_method == "Electronics"){
							$all_error[$chargeId][] = "$patient_name $show_SecondType Subscriber Date of Birth is Required.";
						}
					}
				}
			}
		}
	}
	$return['all_error'] = $all_error;
	return $return;
}

// GETTING INSURANCE COMPANY DETAILS
function getInsGroupNumber($id,$pid,$type,$date_of_service,$all_ins_comp=""){
	if($all_ins_comp==""){
		$ins_del_chk= " and insurance_companies.ins_del_status  = '0'";
	}
	$qry = imw_query("select insurance_data.* from insurance_data join insurance_companies
			on insurance_companies.id = insurance_data.provider
			where insurance_data.ins_caseid = '$id' and insurance_data.pid = '$pid'
			and insurance_data.type = '$type' and insurance_data.provider > '0'
			$ins_del_chk
			and date_format(insurance_data.effective_date,'%Y-%m-%d') <= '$date_of_service' 
			and (insurance_data.expiration_date = '0000-00-00 00:00:00' 
			or date_format(insurance_data.expiration_date,'%Y-%m-%d') >= '$date_of_service') 
			order by insurance_data.actInsComp desc limit 0,1");
	if(imw_num_rows($qry)>0){
		$qryRes = imw_fetch_object($qry);
	}
	return $qryRes;
}

function normalise($string) {
	$string = str_replace("\r"," ", $string);
	$string = str_replace("\n"," ", $string);
	return trim(addslashes($string));
}

function set_write_off_trans_vip($enc_arr_id){
	$wrt_vip_ins_id=array();
	if($enc_arr_id){
		$operatorId = $_SESSION['authUserID'];
		$paidDate = date('Y-m-d');
		$entered_date=date('Y-m-d H:i:s');
		$pol_qry = imw_query("select vip_bill_not_pat,vip_write_off_code from copay_policies where policies_id = '1' and vip_bill_not_pat>0 limit 0,1");
		$pol_qry_res = imw_fetch_array($pol_qry);
		if($pol_qry_res['vip_bill_not_pat']>0){
			$chl_data_qry = "SELECT patient_charge_list.charge_list_id,
						 patient_charge_list.encounter_id,
						 patient_charge_list.vipStatus,patient_charge_list.primaryInsuranceCoId,
						 patient_charge_list.secondaryInsuranceCoId,patient_charge_list.tertiaryInsuranceCoId,
						 patient_charge_list.primary_paid,patient_charge_list.secondary_paid,patient_charge_list.tertiary_paid,
						 patient_charge_list_details.patient_id,
						 patient_charge_list_details.charge_list_detail_id,
						 patient_charge_list_details.totalAmount,
						 patient_charge_list_details.approvedAmt,
						 patient_charge_list_details.newBalance,
						 patient_charge_list_details.pat_due,
						 patient_charge_list_details.pri_due,
						 patient_charge_list_details.sec_due,
						 patient_charge_list_details.tri_due
						  
						  FROM 
						 patient_charge_list join patient_charge_list_details
						 on patient_charge_list.charge_list_id = patient_charge_list_details.charge_list_id
						 where 
						 patient_charge_list_details.del_status='0' and 
						 patient_charge_list.encounter_id in($enc_arr_id)
						 and patient_charge_list_details.newBalance>0
						 and patient_charge_list_details.pat_due>0
						 and patient_charge_list.vipStatus='true'";
			$chl_data_run = imw_query($chl_data_qry);
			while($chl_data_row = imw_fetch_array($chl_data_run)){
				$patient_id = $chl_data_row['patient_id'];
				$charge_list_detail_id = $chl_data_row['charge_list_detail_id'];
				$encounter_id = $chl_data_row['encounter_id'];
				$totalAmount = $chl_data_row['totalAmount'];
				$approvedAmt = $chl_data_row['approvedAmt'];
				$newBalance = $chl_data_row['newBalance'];
				$pat_due = $chl_data_row['pat_due'];
				$pri_due= $chl_data_row['pri_due'];
				$sec_due = $chl_data_row['sec_due'];
				$tri_due = $chl_data_row['tri_due'];
				$ins_paid_status=true;
				if($pri_due>0 || $sec_due>0 || $tri_due>0){
					$ins_paid_status=false;
				}
				if($ins_paid_status=="true" && $pat_due>0 && $pol_qry_res['vip_bill_not_pat'] >0 && $newBalance>0){
					$write_off_code_id=$pol_qry_res['vip_write_off_code'];
					$insertWriteOffStr = "INSERT INTO paymentswriteoff SET
											patient_id = '$patient_id',
											encounter_id = '$encounter_id',
											charge_list_detail_id = '$charge_list_detail_id',
											write_off_by_id = '0',
											write_off_amount = '$pat_due',
											write_off_operator_id = '$operatorId',
											write_off_date = '$paidDate',
											paymentStatus = 'Write Off',
											write_off_code_id='$write_off_code_id',
											entered_date='$entered_date'
											";
					$insertWriteOffQry = imw_query($insertWriteOffStr);
					
					$wrt_vip_ins_id[]=imw_insert_id();
				}
			}
		}
	}
	return $wrt_vip_ins_id;
}
function mmddyy_date($val){
	$dat_exp1=explode(' ',$val);
	$dat_exp2=explode('-',$dat_exp1[0]);
	$dat_make=$dat_exp2[1].'-'.$dat_exp2[2].'-'.substr($dat_exp2[0],2);
	if($dat_make=="00-00-00"){
		$send_date="-";
	}else{
		$send_date=$dat_make;	
	}
	return $send_date;
}
function auto_writeoff_tran($pat_id,$chld_id,$wrt_amt,$write_off_by,$del_wrt){
	$writeOffDelDate=date('Y-m-d');
	$del_time = date('H:i:s');
	$operator_id = $_SESSION['authId'];
	$modified_date=date('Y-m-d H:i:s');
	if($del_wrt>0){
		imw_query("update paymentswriteoff set delStatus='1',del_operator_id='$operator_id',write_off_del_date='$writeOffDelDate',write_off_del_time='$del_time' where charge_list_detail_id='$chld_id' and write_off_amount='0' and delStatus='0'");
	}else{
		$sel_wrt=imw_query("select write_off_id,era_amt,write_off_date,entered_date from paymentswriteoff where patient_id='$pat_id' and charge_list_detail_id='$chld_id' and era_amt>0 and write_off_amount='0' and delStatus='0' order by write_off_date asc");
		if(imw_num_rows($sel_wrt)>0){
			$wrt_cont=imw_num_rows($sel_wrt);
			while($row_wrt=imw_fetch_array($sel_wrt)){
				$write_off_id=$row_wrt['write_off_id'];
				$era_amt=$row_wrt['era_amt'];
				$chld_write_off_date=$row_wrt['write_off_date'];
				$chld_wrt_entered_date=explode(' ',$row_wrt['entered_date']);
				$chld_write_off_dot=$chld_wrt_entered_date[0];
				if($wrt_cont>1){
					imw_query("update paymentswriteoff set delStatus='1',del_operator_id='$operator_id',write_off_del_date='$writeOffDelDate',write_off_del_time='$del_time' where write_off_id='$write_off_id'");
				}else{
					if($era_amt!=$wrt_amt){
						imw_query("update paymentswriteoff set era_amt='$wrt_amt',write_off_by_id='$write_off_by',modified_date='$modified_date',modified_by='$operator_id' where write_off_id='$write_off_id'");
					}
					imw_query("update patient_charge_list_details set write_off_date='$chld_write_off_date',write_off_dot='$chld_write_off_dot' where charge_list_detail_id='$chld_id'");
				}
				$wrt_cont=$wrt_cont-1;
			}
		}
	}
	$sel_def_wrt=imw_query("select encounter_id,charge_list_id,facility_id,write_off_amount,write_off_by from defaultwriteoff where patient_id='$pat_id' and charge_list_detail_id='$chld_id' and del_status='0' order by write_off_id desc limit 0,1");
	if(imw_num_rows($sel_def_wrt)>0){
		$row_def_wrt=imw_fetch_array($sel_def_wrt);
		if(str_replace(',','',number_format($row_def_wrt['write_off_amount'],2))!=str_replace(',','',number_format($wrt_amt,2))){
			imw_query("insert into defaultwriteoff set patient_id='".$pat_id."',encounter_id='".$row_def_wrt['encounter_id']."',charge_list_id='".$row_def_wrt['charge_list_id']."',charge_list_detail_id='".$chld_id."',
			write_off_amount='".$wrt_amt."',write_off_by='".$row_def_wrt['write_off_by']."',write_off_operator_id='".$operator_id."',write_off_dop='".$writeOffDelDate."',write_off_dot='".$modified_date."',facility_id='".$row_def_wrt['facility_id']."'");
		}
	}else{
		if($wrt_amt>0){
			$sel_def_wrt=imw_query("select patient_charge_list.encounter_id,patient_charge_list.charge_list_id,patient_charge_list.billing_facility_id 
			from patient_charge_list join patient_charge_list_details on patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id
			where patient_charge_list_details.charge_list_detail_id='$chld_id' and patient_charge_list_details.del_status='0'");
			if(imw_num_rows($sel_def_wrt)>0){
				$row_def_wrt=imw_fetch_array($sel_def_wrt);
				imw_query("insert into defaultwriteoff set patient_id='".$pat_id."',encounter_id='".$row_def_wrt['encounter_id']."',charge_list_id='".$row_def_wrt['charge_list_id']."',charge_list_detail_id='".$chld_id."',
				write_off_amount='".$wrt_amt."',write_off_by='".$write_off_by."',write_off_operator_id='".$operator_id."',write_off_dop='".$writeOffDelDate."',write_off_dot='".$modified_date."',facility_id='".$row_def_wrt['billing_facility_id']."'");
			}
		}
	}
}
function ymd2ts(&$mdy){
	if($mdy<>""){
		list($m,$d,$y)=explode('-',$mdy);
		$mdy=$y.$m.$d;
	}	
}
function ts2ymd(&$ts){
	if($ts<>""){
		$y=substr($ts,0,4);
		$m=substr($ts,4,2);
		$d=substr($ts,6,2);
		$ts= date('m-d-Y',mktime(0,0,0,$m,$d,$y));
	}
}
function show_cas_code_fun($CAS_type,$CAS_code){
	global $show_cas_code_arr;
	
	$ret_show_cas_code_arr=array();
	$CAS_type_arr=explode(',',$CAS_type);
	$CAS_code_arr=explode(',',$CAS_code);
	foreach($CAS_type_arr as $key => $val){
		$show_cas_code_chk = $CAS_type_arr[$key].' '.$CAS_code_arr[$key];
		$show_cas_code_chk1 = $CAS_type_arr[$key].$CAS_code_arr[$key];
		$show_cas_code_chk2 = $CAS_code_arr[$key];
		$show_cas_code_chk3 = $CAS_type_arr[$key];
		$show_cas_code_chk4 = $CAS_type_arr[$key].'-'.$CAS_code_arr[$key];
		
		if($show_cas_code_arr[$show_cas_code_chk]!=""){
			$ret_show_cas_code_arr[$show_cas_code_chk]=$show_cas_code_chk.' - '.$show_cas_code_arr[$show_cas_code_chk];
		}else if($show_cas_code_arr[$show_cas_code_chk1]!=""){
			$ret_show_cas_code_arr[$show_cas_code_chk1]=$show_cas_code_chk.' - '.$show_cas_code_arr[$show_cas_code_chk1];
		}else if($show_cas_code_arr[$show_cas_code_chk2]!=""){
			$ret_show_cas_code_arr[$show_cas_code_chk2]=$show_cas_code_chk.' - '.$show_cas_code_arr[$show_cas_code_chk2];
		}else if($show_cas_code_arr[$show_cas_code_chk3]!=""){
			$ret_show_cas_code_arr[$show_cas_code_chk3]=$show_cas_code_chk.' - '.$show_cas_code_arr[$show_cas_code_chk3];
		}else if($show_cas_code_arr[$show_cas_code_chk4]!=""){
			$ret_show_cas_code_arr[$show_cas_code_chk4]=$show_cas_code_chk.' - '.$show_cas_code_arr[$show_cas_code_chk4];
		}
	}
	/*
	if(strpos($CAS_code, '237') || strpos($CAS_code, '59')){
		if(stristr($CAS_type,'CO,CO') && stristr($CAS_code,'45,45') ){
			$CAS_type=str_replace('CO,CO','CO',$CAS_type);
			$CAS_code=str_replace('45,45','45',$CAS_code);
		}
		$CASTypeArray = explode(",", $CAS_type);
		$CASReasonCodeArr = explode(",", $CAS_code);
		$show_cas_code = $CASTypeArray[0].' '.$CASReasonCodeArr[0].' - '.$show_cas_code_arr[$CASReasonCodeArr[0]].'<br>';
		if($CASReasonCodeArr[1]=='237'){
			$show_cas_code.= $CASTypeArray[1].' '.$CASReasonCodeArr[1].' - '.'ERx Penalty';
		}
		if($CASReasonCodeArr[1]=='59'){
			$show_cas_code.= $CASTypeArray[1].' '.$CASReasonCodeArr[1].' - '.$show_cas_code_arr[$CASReasonCodeArr[1]];
		}
		return $show_cas_code;
	}else{
		if($CAS_code == '0'){
			$CAS_code="";
		}
		if(stristr($CAS_type,'CO,CO') && stristr($CAS_code,'45,45') ){
			$CAS_type="CO";
			$CAS_code="45";
		}
		$show_cas_code_chk = $CAS_type.' '.$CAS_code;
		$show_cas_code_chk1 = $CAS_type.$CAS_code;
		$show_cas_code="";
		if($show_cas_code_arr[$show_cas_code_chk]!=""){
			$show_cas_code=$show_cas_code_arr[$show_cas_code_chk];
		}else if($show_cas_code_arr[$show_cas_code_chk1]!=""){
			$show_cas_code=$show_cas_code_arr[$show_cas_code_chk1];
		}else if($show_cas_code_arr[$CAS_code]!=""){
			$show_cas_code=$show_cas_code_arr[$CAS_code];
		}else if($show_cas_code_arr[$CAS_type]!=""){
			$show_cas_code=$show_cas_code_arr[$CAS_type];
		}
		if($show_cas_code!=""){
			if($CAS_code!=""){
				$show_cas_code=$CAS_type.' '.$CAS_code .' - '.$show_cas_code;
			}else{
				$show_cas_code=$CAS_type.' - '.$show_cas_code;
			}
		}else{
			if($CAS_code!=""){
				$show_cas_code=$CAS_type.' '.$CAS_code;
			}else if($CAS_type!=""){
				$show_cas_code=$CAS_type;
			}
		}
		return $show_cas_code;
	}*/
	return implode('<br>',$ret_show_cas_code_arr);
}
function approvedAmtText($approvedAmtText){
	$formatedValue = str_replace(",", "", $approvedAmtText);
	$formatedValue = str_replace("-", "", $formatedValue);
	return $formatedValue;
}
function show_gro_color($g_color){
	$show_style="";
	if($g_color!=""){
		$show_style="style='border-left:10px solid $g_color;'";
	}
	return $show_style;
}
function show_ins_tooltip($ins_data)
{
	$data=array();
	$tooltip_content="";
	if($ins_data)
	{
		if($ins_data['policy_number']){	
			$data['policy'] = $ins_data['policy_number'];
		}
		if($ins_data['City']!=''){
			$city = $ins_data['City'].', '.$ins_data['State'].' '.$ins_data['Zip'];
		}
	
		$data['name'] = $ins_data['in_house_code'].' - '.$ins_data['name'];
	
		if($ins_data['contact_address']!=""){
			$address = $ins_data['contact_address'];
		}
		$address .= ($city) ? ' - '.$city : '';
		
		$data['address'] = $address;
		$data['phone'] = ($ins_data['phone'] != '') ? $ins_data['phone'] : '';
	}
	foreach($data as $key => $val)
	{
		$tooltip_content .= '<b>'.ucfirst($key).': </b>'.$val.'<br>';
	}
	return show_tooltip($tooltip_content);
}

function getPatientCharList_era($id,$process,$era_print=""){		
	$qry = imw_query("select id from insurance_companies where in_house_code = 'medicare'");
	$medId=array();
	while($medQryRes = imw_fetch_array($qry)){		
		$medId[] = $medQryRes['id'];
	}
	$medId_imp=implode(',',$medId);
	//and group_institution != '1'
	$in_house_ord="";
	if($era_print==""){
		$in_house_ord="ic.in_house_code,";
	}
	if($process == 'hcfa'){
		$qry = "select a.*,date_format(a.postedDate,'%m-%d-%y') as postedDate,
				date_format(a.date_of_service,'%m-%d-%y') as date_of_service
				from patient_charge_list as a,groups_new as b, 
				patient_data as p,
				insurance_companies as ic
				where 
				a.del_status='0' and 
				p.id=a.patient_id
				and ic.id=a.secondaryInsuranceCoId
				and a.encounter_id in($id)
				and a.primaryInsuranceCoId in($medId_imp)
				and a.secondaryInsuranceCoId > '0'
				and b.gro_id = a.gro_id
				order by $in_house_ord p.lname,p.fname";
	}
	else{
		$qry = "select a.*,date_format(a.postedDate,'%m-%d-%y') as postedDate,
				date_format(a.date_of_service,'%m-%d-%y') as date_of_service
				from patient_charge_list a,insurance_companies b,
				patient_data as p,
				insurance_companies as ic
				where 
				a.del_status='0' and 
				p.id=a.patient_id
				and ic.id=a.secondaryInsuranceCoId
				and b.Insurance_payment = 'Electronics' 
				and a.secondaryInsuranceCoId = b.id
				and a.primaryInsuranceCoId in($medId_imp)
				and b.name != 'SELF PAY'
				and a.primary_paid = 'true' and a.secondary_paid = 'false'
				and a.encounter_id in($id)
				and a.totalBalance > '0' and a.submitted = 'true' and a.secondarySubmit = '0'
				group by a.charge_list_id
				order by $in_house_ord p.lname,p.fname";
	}
	
	$qryRes = imw_query($qry);
	while($qryRow = imw_fetch_array($qryRes)){	
		$return[] = changeFormat($qryRow);
	}
	return $return;
}
function changeFormat($datArr){
	$returnArr = array();
	if(count($datArr) > 0){
		foreach($datArr as $key => $val){
			$returnArr[$key] = trim(html_entity_decode(stripslashes($val)));
		}
	}
	return $returnArr;
}
function getSubmitedDate($enc_id,$pat_id){
	$qry = imw_query("select submited_date from submited_record where encounter_id in ($enc_id)
			and patient_id in ($pat_id) order by submited_id desc ");
	$qryRes = imw_query($qry);
	while($qryRow = imw_fetch_array($qryRes)){	
		$qryRes = $this->getObject($qryId);
	}
	return $qryRes;	
}
function denial_resp_fun($data_arr){
	$ret_val=0;
	$den_qry = imw_query("select * from denial_resp where denial_resp_id='1'");
	$den_row=imw_fetch_array($den_qry);
	$ins_cpt_code_arr=explode(',',$den_row['cpt_code_resp']);	
	$ins_cas_code_arr=explode(',',$den_row['cas_code_resp']);	
	if($den_row['denial_resp_all']>0){
		$ret_val=1;
	}
	if(count($ins_cpt_code_arr)>0 && $ret_val==0){
		if(in_array($data_arr['denial_cpt_code'],$ins_cpt_code_arr)){
			$ret_val=1;
		}
	}
	if(count($ins_cas_code_arr)>0  && $ret_val==0){
		foreach($ins_cas_code_arr as $key => $val){
			$ins_cas_code_arr_sec=explode(' ',$ins_cas_code_arr[$key]);
			$ins_cas_code_arr_thr=explode('-',$ins_cas_code_arr[$key]);
			if(in_array($data_arr['denial_cas_code'],$ins_cas_code_arr_sec)){
				$ret_val=1;
			}
			if(in_array($data_arr['denial_cas_code'],$ins_cas_code_arr_thr)){
				$ret_val=1;
			}
		}
	}
	return $ret_val;
}
function re_calculate_tax($enc_id){
	$tax_proc_id="";
	if($enc_id>0){
		$pcl_qry = imw_query("select charge_list_id,billing_facility_id from patient_charge_list where encounter_id='$enc_id' and del_status='0'");
		$pcl_row = imw_fetch_array($pcl_qry);
		if($pcl_row['billing_facility_id']>0){
			$fac_qry = imw_query("select fac_tax from facility where id='".$pcl_row['billing_facility_id']."'");
			$fac_row = imw_fetch_array($fac_qry);
			if($fac_row['fac_tax']>0){
				$pcld_qry =  imw_query("select charge_list_detail_id,totalAmount,procCode from patient_charge_list_details where del_status='0' and charge_list_id = '".$pcl_row['charge_list_id']."'");
				while($pcld_row = imw_fetch_array($pcld_qry)){
					$cpt_qry = imw_query("select cpt_tax,cpt_prac_code from cpt_fee_tbl where cpt_fee_id='".$pcld_row['procCode']."' and delete_status = '0'");
					$cpt_row = imw_fetch_array($cpt_qry);
					if($cpt_row['cpt_tax']>0){
						$cpt_tax_amt[]=$pcld_row['totalAmount'];
						$cpt_tax_chld_id[$pcld_row['charge_list_detail_id']]=$pcld_row['charge_list_detail_id'];
					}
					if(strtolower($cpt_row['cpt_prac_code'])=="tax"){
						$tax_proc_id=$pcld_row['charge_list_detail_id'];
					}
				}
				if($tax_proc_id>0){
					$cpt_tax_chld_id_imp=implode("','",$cpt_tax_chld_id);
					$dis_qry = imw_query("select sum(write_off_amount) as wrt_amt from paymentswriteoff where charge_list_detail_id in ('".$cpt_tax_chld_id_imp."') and delStatus='0' and paymentStatus='Discount'");
					$dis_row = imw_fetch_array($dis_qry);
					$tax_proc_amt=(((array_sum($cpt_tax_amt)-$dis_row['wrt_amt'])*$fac_row['fac_tax'])/100);
					imw_query("update patient_charge_list_details set procCharges='".$tax_proc_amt."',totalAmount='".$tax_proc_amt."',approvedAmt='".$tax_proc_amt."' where charge_list_detail_id='".$tax_proc_id."'");
				}
			}
		}
	}
}
function remove_spec_dx($arr_dx_codes){
	$arr_dx_codes = unserialize(html_entity_decode($arr_dx_codes));
	foreach($arr_dx_codes as $dx_key=>$dx_val){
		$arr_dx_code_exp=explode('@*@',$arr_dx_codes[$dx_key]);
		$arr_dx_codes_arr[$dx_key]=$arr_dx_code_exp[0];
	}
	$arr_dx_codes = serialize($arr_dx_codes_arr);
	return $arr_dx_codes;
}
?>