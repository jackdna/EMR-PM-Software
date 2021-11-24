<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
/*
File: appt_ac_functions.php
Coded in PHP7
Purpose: Define Class functions for accounting
Access Type: Include
*/
class appt_accounting{
	/*
	Function: show_collection_flag
	Purpose: to check as if collection is due for the patient or not
	Author: AA
	Returns: ARRAY if found, else false
	*/
	function show_collection_flag($pat_id = 0){
		$return = array(false, "");
		if(isset($pat_id) && $pat_id > 0){
			$sql = "select collection, date_format(letter_sent_date,'%m-%d-%Y') as  letter_sent_date from patient_charge_list where del_status='0' and patient_id = '".$pat_id."' and collection = 'true' and totalBalance != '0' order by charge_list_id  desc limit 0,1";
			$res = imw_query($sql);
			if(imw_num_rows($res) > 0){
				$data = imw_fetch_assoc($res);
				$collection_date = "";
				if($data['letter_sent_date'] != "" && $data['letter_sent_date'] != "0000-00-00"){
					$collection_date = $data['letter_sent_date'];
				}
				$return = array(true, $collection_flag);
			}
		}
		return $return;
	}

	function get_pt_acc_dues($pat_id = 0){
		
		/*Purpose: Get patient due*/
	
		$return = array("$0.00", "$0.00", "$0.00", "$0.00", "$0.00");

		$qry = "SELECT IFNULL(SUM( patientDue ),0) as patient_due, IFNULL(SUM( insuranceDue ), 0) as insurance_due, IFNULL(SUM( creditAmount), 0) as credit_amount, IFNULL(SUM( totalBalance ), 0) as total_balance, IFNULL(SUM( overPayment ), 0) as over_payment, IFNULL(SUM( totalAmt ), 0) as total_amt FROM patient_charge_list WHERE del_status='0' and patient_id IN ('".$pat_id."')";
		$res = imw_query($qry);
		if(imw_num_rows($res) > 0){
			$arr = imw_fetch_assoc($res);
			$patient_due_final=$arr["patient_due"];
			$pt_due = numberformat($patient_due_final,2,"yes");
			$ins_due = numberformat($arr["insurance_due"],2,"yes");

			$total_balance_final=($arr["total_balance"]-$arr["over_payment"]);
			$outstanding_bal = (isset($outstanding_bal) && $outstanding_bal > 0) ? "<b style='color:#ff0000'>".numberformat($arr["total_balance"],2,"yes")."</b>" : "".numberformat($total_balance_final,2,"yes");
			$credit_amt = numberformat($arr["credit_amount"],2,"yes");
			$total_amt = numberformat($arr["total_amt"],2,"yes");

			$return = array($pt_due, $ins_due, $outstanding_bal, $credit_amt, $total_amt);
		}
		
		return $return;
	}

	/*
	Function: get_latest_enc_id, accepts dos in Y-m-d format only
	Purpose: to get latest encounter id for a patient on a particular DOS
	Author: AA
	Returns: ARRAY
	*/
	function get_latest_enc_id($pat_id = 0, $dos){
		$return = false;		
		$qry = "SELECT encounterId FROM superbill WHERE patientId=$pat_id and dateOfService = '".$dos."' and del_status='0' ORDER BY timeSuperBill DESC LIMIT 0 , 1";
		$res = imw_query($qry);
		if(imw_num_rows($res) > 0){
			$return = imw_fetch_assoc($res);			
		}
		return $return;
	}

	/*
	Function: get_billing_policies
	Purpose: to get current billing policies
	Author: AA
	Returns: ARRAY
	*/
	function get_billing_policies(){
		$return = false;
		$qry = "select * from copay_policies limit 1";
		$res = imw_query($qry);
		if(imw_num_rows($res) > 0){
			$return = imw_fetch_assoc($res);			
		}
		return $return;
	}

	/*
	Function: get_patient_ins_cases
	Purpose: to get all opened ins cases of this patient
	Author: AA
	Returns: ARRAY
	*/
	function get_patient_ins_cases($pat_id = 0,$case_status = "Open"){
		$return = false;
		if($pat_id > 0){
			$qry = "SELECT ic.ins_caseid,ic.case_status , ict.case_name, ict.vision FROM insurance_case ic INNER JOIN insurance_case_types ict ON ict.case_id = ic.ins_case_type WHERE ic.patient_id = '".$pat_id."'";
			if($case_status != ""){
			$qry .=" AND case_status = '".$case_status."'";
			}
			$res = imw_query($qry);
			if(imw_num_rows($res) > 0){
				while($ret=imw_fetch_assoc($res))
				{
					$return[] = $ret;	
				}
			}
		}
		return $return;
	}
}
?>