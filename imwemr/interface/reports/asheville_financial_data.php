<?php
if(isset($_POST['form_submitted']) && $_POST['form_submitted']=='1'){
	$csv_string = '';
	$dtfrom = getDateFormatDB($Start_date);
	$dtupto = getDateFormatDB($End_date);
	
	/******GETTING TOP PAYERS, WHO PAID MOST IN THE DURATION******/
	$top_payer_ids = false;
	$top_insurers_q = "SELECT pcpi.insProviderId, SUM(pcdpi.paidForProc+pcdpi.overPayment) AS ins_paid_amt 
						FROM `patient_charges_detail_payment_info` pcdpi 
						JOIN patient_chargesheet_payment_info pcpi ON (pcpi.payment_id=pcdpi.payment_id AND pcpi.insProviderId>0 AND (pcpi.date_of_payment BETWEEN '$dtfrom' AND '$dtupto')) 
						GROUP BY (pcpi.insProviderId) 
						ORDER BY ins_paid_amt DESC 
						LIMIT 0,5";
	$top_insurers_res = imw_query($top_insurers_q);
	if($top_insurers_res && imw_num_rows($top_insurers_res)>0){
		$top_payer_ids = array();
		while($top_insurers_rs = imw_fetch_assoc($top_insurers_res)){
			$top_payer_ids[] = $top_insurers_rs['insProviderId'];
		}
	}
	
	/*******GET PROCEDURE id OF PREDEFINED CPTs********/
	$cpt_ids_ar = false;
	$cpt_ids_res = imw_query("SELECT cpt_fee_id,cpt4_code FROM cpt_fee_tbl WHERE cpt_prac_code IN ('66821F','66982F','66984F') AND delete_status='0' ORDER BY cpt4_code");
	if($cpt_ids_res && imw_num_rows($cpt_ids_res)==3){
		$cpt_ids_ar = array();
		while($cpt_ids_rs = imw_fetch_assoc($cpt_ids_res)){
			$cpt_ids_ar[$cpt_ids_rs['cpt_fee_id']] = $cpt_ids_rs['cpt4_code'];
		}
	}
//	$cpt_ids_str = implode(',',$cpt_ids_ar);
	//unset($cpt_ids_ar);

	if(!$top_payer_ids || count($top_payer_ids)!=5){
		echo '<div class="text-center alert alert-info">Top 5 payers not found.</div>';
	}else if(!$cpt_ids_ar || count($cpt_ids_ar)!=3){
		echo '<div class="text-center alert alert-info">Predefined procedures not found in system.</div>';
	}else{
		/*******GETTING MEDICAID PAYER******/
		$medicaid_payer_id = false;
		$res_medicaid_payer = imw_query("SELECT id FROM insurance_companies WHERE ((name LIKE '%medicaid%' OR in_house_code LIKE 'medicaid') OR ins_type='MC') AND ins_del_status='0'");
		if($res_medicaid_payer && imw_num_rows($res_medicaid_payer)==1){//IF ONE PAYER is set as medicare.
			$rs_medicaid_payer = imw_fetch_assoc($res_medicaid_payer);
			$medicaid_payer_id = $rs_medicaid_payer['id'];
		}else if($res_medicaid_payer && imw_num_rows($res_medicaid_payer)>1){// if multiple payers are set as medicare
			while($rs_medicaid_payer = imw_fetch_assoc($res_medicaid_payer)){
				$medicaid_payer_id[] = $rs_medicaid_payer['id'];
			}
			$medicaid_payer_id = implode(',',$medicaid_payer_id);
		}

		/*******Getting MEDICARE payer****/
		$medicare_payer_id = false;
		$res_medicare_payer = imw_query("SELECT id FROM insurance_companies WHERE ((claim_type ='1' AND (name LIKE '%medicare%' OR in_house_code LIKE 'medicare')) OR ins_type='MB') AND ins_del_status='0'");
		if($res_medicare_payer && imw_num_rows($res_medicare_payer)==1){//IF ONE PAYER is set as medicare.
			$rs_medicare_payer = imw_fetch_assoc($res_medicare_payer);
			$medicare_payer_id = $rs_medicare_payer['id'];
		}else if($res_medicare_payer && imw_num_rows($res_medicare_payer)>1){// if multiple payers are set as medicare
			while($rs_medicare_payer = imw_fetch_assoc($res_medicare_payer)){
				$medicare_payer_id[] = $rs_medicare_payer['id'];
			}
			$medicare_payer_id = implode(',',$medicare_payer_id);
		}
		
		foreach($cpt_ids_ar as $cpt=>$dispCPT){
			$csv_string .= 'SPX,'.$dispCPT;//3rd and 4th logic pending.
			
			/******Getting average charges, where paid to zero******/
			$third_col_val = '0';
			$q_third_col = "SELECT pcld.procCode,ROUND(AVG(pcld.procCharges),0) AS average_charge 
			FROM patient_charge_list_details pcld 
			JOIN patient_charges_detail_payment_info pcdpi ON (pcdpi.charge_list_detail_id=pcld.charge_list_detail_id) 
			WHERE pcdpi.deletePayment='0' 
				  AND pcld.del_status='0' 
				  AND (pcdpi.paidDate BETWEEN '$dtfrom' AND '$dtupto') 
				  AND pcld.procCode = '$cpt' 
				  AND pcld.newBalance='0.00' 
				  GROUP BY pcld.procCode";// AND LOWER(pcdpi.paidBy) = 'insurnace' 
			$res_third_col = imw_query($q_third_col);
			if($res_third_col && imw_num_rows($res_third_col)==1){
				$rs_third_col = imw_fetch_assoc($res_third_col);
				$third_col_val = $rs_third_col['average_charge'];
			}
			
			/******Getting average writeoff, where paid to zero******/
			$forth_col_val = '0';
			$q_forth_col = "SELECT pcld.procCode,ROUND(AVG(pw.write_off_amount),0) AS average_writeoff 
			FROM patient_charge_list_details pcld 
			JOIN paymentswriteoff pw ON (pw.charge_list_detail_id=pcld.charge_list_detail_id) 
			WHERE pw.delStatus='0' 
				  AND pcld.del_status='0' 
				  AND LOWER(pw.paymentStatus) = 'write off' 
				  AND (pw.write_off_date BETWEEN '$dtfrom' AND '$dtupto') 
				  AND pcld.procCode = '$cpt' 
				  AND pcld.newBalance='0.00' 
				  GROUP BY pcld.procCode";
			$res_forth_col = imw_query($q_forth_col);
			if($res_forth_col && imw_num_rows($res_forth_col)==1){
				$rs_forth_col = imw_fetch_assoc($res_forth_col);
				$forth_col_val = $rs_forth_col['average_writeoff'];
			}
			
			$csv_string .= ','.$third_col_val.','.$forth_col_val;
			
			/******Getting the payment done by Medicaid (MC) for given CPTs******/
			$fifth_col_val = '0';
			$q_medicaid_amount = "SELECT pcld.procCode,ROUND(SUM(pcpi.payment_amount),0) AS medicaid_amount 
			FROM patient_charge_list_details pcld 
			JOIN patient_charges_detail_payment_info pcdpi ON (pcdpi.charge_list_detail_id=pcld.charge_list_detail_id) 
			JOIN patient_chargesheet_payment_info pcpi ON (pcpi.payment_id=pcdpi.payment_id) 
			WHERE pcdpi.deletePayment='0' 
				  AND pcld.del_status='0' 
				  AND pcpi.insProviderId IN ($medicaid_payer_id) 
				  AND (pcpi.date_of_payment BETWEEN '$dtfrom' AND '$dtupto') 
				  AND pcld.procCode = '$cpt' 
				  GROUP BY pcld.procCode";
			$res_medicaid_amount = imw_query($q_medicaid_amount);
			if($res_medicaid_amount && imw_num_rows($res_medicaid_amount)==1){
				$rs_medicaid_amount = imw_fetch_assoc($res_medicaid_amount);
				$fifth_col_val = $rs_medicaid_amount['medicaid_amount'];
			}
			
			/******Getting the payment done by Medicare (MB) for given CPTs******/
			$sixth_col_val = '0';
			$q_medicare_amount = "SELECT pcld.procCode,ROUND(SUM(pcpi.payment_amount),0) AS medicare_amount 
			FROM patient_charge_list_details pcld 
			JOIN patient_charges_detail_payment_info pcdpi ON (pcdpi.charge_list_detail_id=pcld.charge_list_detail_id) 
			JOIN patient_chargesheet_payment_info pcpi ON (pcpi.payment_id=pcdpi.payment_id) 
			WHERE pcdpi.deletePayment='0' 
				  AND pcld.del_status='0' 
				  AND pcpi.insProviderId IN ($medicare_payer_id) 
				  AND (pcpi.date_of_payment BETWEEN '$dtfrom' AND '$dtupto') 
				  AND pcld.procCode = '$cpt' 
				  GROUP BY pcld.procCode";
			$res_medicare_amount = imw_query($q_medicare_amount);
			if($res_medicare_amount && imw_num_rows($res_medicare_amount)==1){
				$rs_medicare_amount = imw_fetch_assoc($res_medicare_amount);
				$sixth_col_val = $rs_medicare_amount['medicare_amount'];
			}
			
			$csv_string .= ','.$fifth_col_val.','.$sixth_col_val;
			/***GETTING lowest, Average & Largest payment for this procedure by payer from top 5****/
			$col7thTo21st = array();
			foreach($top_payer_ids as $topPayer){
				/******Getting the LOWEST,AVERGE,Largest AMOUNT PAID BY LARGEST PAYER given CPTs******/
				$q_amount_largest_payer = "SELECT pcld.procCode, ROUND(MIN(pcpi.payment_amount),0) AS lowest_amount, ROUND(AVG(pcpi.payment_amount),0) AS average_amount, ROUND(MAX(pcpi.payment_amount),0) AS max_amount FROM patient_charge_list_details pcld 
				JOIN patient_charges_detail_payment_info pcdpi ON (pcdpi.charge_list_detail_id=pcld.charge_list_detail_id) 
				JOIN patient_chargesheet_payment_info pcpi ON (pcpi.payment_id=pcdpi.payment_id) 
				WHERE pcdpi.deletePayment='0' 
					  AND pcld.del_status='0' 
					  AND pcpi.insProviderId = '$topPayer' 
					  AND (pcpi.date_of_payment BETWEEN '$dtfrom' AND '$dtupto') 
					  AND pcld.procCode = '$cpt' 
					  GROUP BY pcld.procCode";
				$res_amount_largest_payer = imw_query($q_amount_largest_payer);
				if($res_amount_largest_payer && imw_num_rows($res_amount_largest_payer)==1){
					$rs_amount_largest_payer = imw_fetch_assoc($res_amount_largest_payer);
					$col7thTo21st[] = $rs_amount_largest_payer['lowest_amount'];
					$col7thTo21st[] = $rs_amount_largest_payer['average_amount'];
					$col7thTo21st[] = $rs_amount_largest_payer['max_amount'];
				}else{
					$col7thTo21st[] = 0;
					$col7thTo21st[] = 0;
					$col7thTo21st[] = 0;
				}
			}
			
			$col7thTo21st = implode(',',$col7thTo21st);
			$csv_string .= ','.$col7thTo21st.',0,0,0'.chr(13);
			
				
		}
		if(!empty($csv_string)){
			echo '<div class="text-center alert alert-info">Report Generated. Please download CSV by clicking button below.</div>';
		}
	}

	
}
?>

</body>
</html>