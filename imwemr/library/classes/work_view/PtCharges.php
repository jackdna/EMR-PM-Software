<?php
class PtCharges{
	private $pid;
	public function __construct($pid){		
		$this->pid=$pid;
	}

function getPatientDues()
{
	$patientId = $this->pid;
	$sql = "SELECT
			patient_charge_list.encounter_id,
			patient_charge_list.patientDue,
			patient_charge_list.insuranceDue,
			patient_charge_list.lastPayment,
			patient_charge_list.lastPaymentDate,
			patient_charge_list_details.pri_due,
			patient_charge_list_details.sec_due,
			patient_charge_list_details.tri_due,
			patient_charge_list_details.pat_due
			FROM patient_charge_list join patient_charge_list_details
			on patient_charge_list_details.charge_list_id=patient_charge_list.charge_list_id
			WHERE patient_charge_list.del_status='0' and patient_charge_list.patient_id ='".$patientId."'
			and patient_charge_list_details.del_status='0'
			ORDER BY patient_charge_list.lastPaymentDate ";
	$rez = sqlStatement($sql);
	$tAmt = $tInsDue = $tCrBal = $tBal = $lastPayment = 0;
	for($i=1;$row=sqlFetchArray($rez);$i++)
	{
		$tAmt += $row["pat_due"];
		$tInsDue += $row["pri_due"]+$row["sec_due"]+$row["tri_due"];
		if(!empty($row["amount_cr"]) && ($row["crApplied"] != $row["amount_cr"])){
			$tCrBal +=  (!empty($row["crAvailable"])) ? $row["crAvailable"] : $row["amount_cr"];
		}
		if(!empty($row["lastPayment"])){
			$lastPayment = $row["lastPayment"];
			$lastPaymentDate = $row["lastPaymentDate"];
		}
	}

	// Get Patinet Credit
	$tCrBal = $this->getPatientCredit();

	// Balance
	$tBal = $tAmt + $tInsDue - $tCrBal;
	$tBal = ($tBal < 0) ? "0" : $tBal;

	$arrDues = array($tAmt,$tInsDue,$tCrBal,$tBal,$lastPayment,$lastPaymentDate);
	return $arrDues;
}

function getPatientCredit()
{
	$patientId = $this->pid;
	$credit = 0;
	$sql = "SELECT creditAmount FROM patientcredit WHERE patientId = '".$patient_id."' ";
	$row = sqlQuery($sql);
	if($row != false)
	{
		$credit = $row["creditAmount"];
	}
	return $credit;
}

function getLastPaidInfo(){
	$patient_id = $this->pid;
	$qry = "select encounter_id from patient_charge_list 
			where del_status='0' and patient_id = '$patient_id'";
	$qryRes = sqlStatement($qry);
	$ent_id_arr = array();
	for($e=0;$row = sqlFetchArray($qryRes);$e++){
		$ent_id_arr[] = $row['encounter_id'];
	}
	if(count($ent_id_arr)>0){
	$ent_id = implode(',',$ent_id_arr);
	if(!empty($ent_id)){
	$paid_amt_arr=array();
	$qry = "select patient_chargesheet_payment_info.date_of_payment,
			patient_chargesheet_payment_info.encounter_id,patient_chargesheet_payment_info.paid_by,
			patient_charges_detail_payment_info.paidForProc,
			patient_charges_detail_payment_info.overPayment from 
			patient_chargesheet_payment_info join
			patient_charges_detail_payment_info on 
			patient_charges_detail_payment_info.payment_id = 
			patient_chargesheet_payment_info.payment_id
			where patient_charges_detail_payment_info.deletePayment != 1
			and patient_chargesheet_payment_info.encounter_id in ($ent_id)
			order by patient_chargesheet_payment_info.payment_id asc";
	
	$qryRes = sqlStatement($qry);		
	for($i=0;$row=sqlFetchArray($qryRes);$i++){
		$last_paid_dop=$row['date_of_payment'];
		$paid_amt_arr[$row['date_of_payment']][]=$row['paidForProc']+$row['overPayment'];
	}
	$paymentAmountLast = array_sum($paid_amt_arr[$last_paid_dop]);
	}
	}	
	
	if($last_paid_dop){
		$PaidLastDate = wv_formatDate($last_paid_dop);
		/*list($yearp, $monthp, $dayp) = split("-", $last_paid_dop);
		if($monthp<>""){
			$PaidLastDate = $monthp.'-'.$dayp.'-'.$yearp;
		}*/
	}else{
		$PaidLastDate = '-';
	}
	$paymentAmountLast = number_format($paymentAmountLast, 2);
	return array($paymentAmountLast, $PaidLastDate);

}

function getTodayPaidInfo($sch_app_dos){
	$today_pay_method_arr=array();
	$today_pay_method_check_arr=array();
	$today_pay_method_cc_arr=array();
	$today_pay_method_cc_type_arr=array();
	
	$patient_id = $this->pid;	
	$ci_co_qry=sqlStatement("select total_payment,payment_method,check_no,cc_no,cc_type from check_in_out_payment 
				where patient_id='$patient_id' and created_on='$sch_app_dos' and total_payment>'0' and del_status='0'");
	for($i=1;$ci_co_row=sqlFetchArray($ci_co_qry);$i++){
		$today_total_pay_arr[]=$ci_co_row['total_payment'];
		if($ci_co_row['payment_method']=="Credit Card"){
			$ci_co_row['payment_method']="CC";
		}
		$today_pay_method_arr[$ci_co_row['payment_method']]=$ci_co_row['payment_method'];
		if($ci_co_row['cc_no']!=""){
			$today_pay_method_cc_arr[$ci_co_row['cc_no']]=$ci_co_row['cc_no'];
			$today_pay_method_cc_type_arr[$ci_co_row['cc_type']]=$ci_co_row['cc_type'];
		}
		if($ci_co_row['check_no']!=""){
			$today_pay_method_check_arr[$ci_co_row['check_no']]=$ci_co_row['check_no'];
		}
	}
	if(count($today_total_pay_arr)>0){
	$today_total_pay= array_sum($today_total_pay_arr);
	}
	
	$today_pay_method = implode(', ',$today_pay_method_arr);	
	$today_pay_method_check = implode(', ',$today_pay_method_check_arr);
	$today_pay_method_cc = implode(', ',$today_pay_method_cc_arr);
	$today_pay_method_cc_type = implode(', ',$today_pay_method_cc_type_arr);
	return array($today_total_pay, $today_pay_method, $today_pay_method_check, $today_pay_method_cc, $today_pay_method_cc_type);
}

}

?>