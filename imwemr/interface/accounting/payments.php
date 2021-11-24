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
if(isERPPortalEnabled()) {
	include_once($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");
	$OBJRabbitmqExchange = new Rabbitmq_exchange();
}
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php");
require_once("../../interface/common/assign_new_task.php");

$chk_box_arr = $_POST['chkbx'];
$ar_claim_status_arr = $_POST['ar_claim_status'];
$patient_id = $_POST['patient_id'];
$trans_date = date('Y-m-d');
$trans_time = date('H:i:s'); 
$trans_date_time = date('Y-m-d H:i:s');
$operator_id = $_SESSION['authUserID']; 
$batch_pat_id = $_POST['batch_pat_id'];
$b_id = $_POST['b_id'];
$sel_patient_pre_payments = $_POST['sel_patient_pre_payments'];	
$sel_ci_co_payments = $_POST['sel_ci_co_payments'];	

$qry_pol_main=imw_query("select discount_amount,return_chk_proc,return_chk_amt from copay_policies");
$fet_pol_main=imw_fetch_array($qry_pol_main);

//------------------------ Reason Code ------------------------//
$qry = imw_query("SELECT * FROM cas_reason_code order by cas_code");
while($row = imw_fetch_array($qry)){
	$reason_code_data[$row['cas_id']]=$row;
}
//------------------------ Reason Code ------------------------//

$enc_notes=core_refine_user_input(trim($_REQUEST['enc_notes']));
if($enc_notes!="" && $_REQUEST['enc_id_read']>0){
	$task_assign="1";
	$task_assign_for=$task_assign_by=$task_assign_date="";
	$notes_reminder_date=getDateFormatDB($_POST['notes_reminder_date']);
	$task_assign_for=implode(',',$_REQUEST['assignFor']);
	if($assignFor!=""){
		$task_assign_by=$operator_id;
		$task_assign="2";
		$task_assign_date=$trans_date_time;
	}
	$task_on_reminder=(isset($_REQUEST['task_on_reminder']) && $_REQUEST['task_on_reminder']=='yes') ?'1':'0';
	
	$comment_insert_id = imw_query("INSERT INTO paymentscomment SET patient_id = '$patient_id',encounter_id = '".$_REQUEST['enc_id_read']."',commentsType = '".$_REQUEST['enc_notes_type']."',
	encComments = '$enc_notes',encCommentsDate = '$trans_date',encCommentsTime='$trans_time',encCommentsOperatorId = '$operator_id',reminder_date='$notes_reminder_date',
	task_assign='$task_assign',task_assign_by='$task_assign_by',task_assign_for='$task_assign_for',task_assign_date='$task_assign_date', task_onreminder='".$task_on_reminder."' ");
	if($comment_insert_id){
		$comment_insert_id=imw_insert_id();
        if($_REQUEST['enc_id_read']) {
            $getdos_sql = imw_query("SELECT encounter_id,date_of_service FROM patient_charge_list WHERE patient_id = '$patient_id' and encounter_id='".$_REQUEST['enc_id_read']."' and del_status='0'");
			$getDosRow = imw_fetch_assoc($getdos_sql);
			$notes_pat_dos=$getDosRow['date_of_service'];
        }
		
		$sel_pat=imw_query("select fname,lname from patient_data where id='$patient_id' limit 0,1");
		$fet_pat=imw_fetch_array($sel_pat);
		$pat_lname=$fet_pat['lname'];
		if($fet_pat['fname']){
			$pat_fname=', '.$fet_pat['fname'];
		}
		$tm_patient_name=$pat_lname.$pat_fname;
		
        imw_query("INSERT INTO tm_assigned_rules(section_name,status,changed_value,date_of_service,encounter_id,patientid, patient_name, operatorid, payment_comtId,notes_users,reminder_date,task_on_reminder)
        VALUES('Accounting Notes', '0', '" . $enc_notes . "', '" . $notes_pat_dos . "', '" . $_REQUEST['enc_id_read'] . "','" . $patient_id . "', '" . $tm_patient_name . "',
		'" . $operator_id . "', '" . $comment_insert_id . "', '" . $task_assign_for . "', '" . $notes_reminder_date . "','".$task_on_reminder."') ");
    }
}

$tot_debit_amt_arr=array();
$cc_type_arr=array("American Express"=>"AX","Care Credit"=>"Care Credit","Discover"=>"Dis","Master Card"=>"MC","Visa"=>"Visa","Others"=>"Others");
if(count($chk_box_arr)>0){
	foreach($chk_box_arr as $chk_key=>$chld){
		$cd_payment_method_type=explode('-',$_POST['payment_method_'.$chk_key]);
		$cd_paid_by_arr=array("1"=>"pri","2"=>"sec","3"=>"ter","0"=>"pat","4"=>"resp");
		if(trim($cd_payment_method_type[0])=="Debit"){
			foreach($cd_paid_by_arr as $paid_key=>$paid_val){
				$deb_paid_amt=$_POST[$paid_val.'_paid_'.$chk_key];
				$tot_debit_amt_arr[]=$deb_paid_amt;
			}
		}
	}
	foreach($chk_box_arr as $chk_key=>$chld){
		$check_no=$cc_no=$cc_type="";
		$paid_by_arr=array("1"=>"pri","2"=>"sec","3"=>"ter","0"=>"pat","4"=>"resp");
		$payment_method_type=explode('-',$_POST['payment_method_'.$chk_key]);
		$payment_method=trim($payment_method_type[0]);
		$payment_method=str_replace('/','_',$payment_method);
		$payment_type=trim($payment_method_type[1]);
		$encounter_id=xss_rem($_POST['encounter_id_'.$chk_key], 3);	/* Sanitization to prevent arbitrary values - Security Fix */
		$task_group_id=$_POST['task_group_id_'.$chk_key];
		$chld_id=(int)xss_rem($_POST['chld_id_'.$chk_key], 3);	/* Sanitization to prevent arbitrary values - Security Fix */
		$chl_id=$_POST['chl_id_'.$chk_key];
		$proc_deduct_amt=$_POST['deductibleText'.$chk_key];
		$proc_total_amt=$_POST['total_amt_'.$chk_key];
		$proc_approved_amt=$_POST['approvedText'.$chk_key];
		$proc_approved_amt_old=$_POST['appActualText'.$chk_key];
		$write_off_code_drop=$_REQUEST['write_off_code_'.$chk_key];
		$dos=$_REQUEST['dos_'.$chk_key];
		$over_payment=$_REQUEST['overPayments_chk'.$chk_key];
		$cpt_code=$_REQUEST['cpt_prac_code_'.$chk_key];
		$cpt_code_id=$_REQUEST['cpt_prac_code_id_'.$chk_key];
		$facility_id=$_REQUEST['facility_id_'.$chk_key];
		$enc_patient_name=$_POST['enc_patient_name'];
		$enc_arr[$encounter_id]=$encounter_id;
		if($payment_type=="Check" || $payment_type=="Money Order" || $payment_type=="VEEP" || $payment_type=="EFT" || $payment_method=='Returned Check' || stripos($payment_type,'Check')>0){
			$check_no=$_POST['check_cc_no_'.$chk_key];
		}
		if($payment_type=="Credit Card"){
			$cc_no=$_POST['check_cc_no_'.$chk_key];
			$cc_type=$cc_type_arr[trim($payment_method_type[2])];
		}
		$paid_date=getDateFormatDB($_POST['paid_date_'.$chk_key]);
		$pmt_notes=core_refine_user_input($_REQUEST['pmt_notes_'.$chk_key]);
		$proc_units=$_REQUEST['units_'.$chk_key];
		$up_chrg="";
		if($_REQUEST['total_amt_'.$chk_key]!=$_REQUEST['totalFee'.$chk_key] && $batch_pat_id<=0){
			$procCharges=0;
			if($_REQUEST['totalFee'.$chk_key]>0){
				$procCharges=$_REQUEST['totalFee'.$chk_key]/$proc_units;
			}
			imw_query("insert into tx_charges set patient_id='$patient_id',encounter_id='$encounter_id',charge_list_id='$chl_id',charge_list_detail_id='$chld_id',
			new_charges='".$_REQUEST['totalFee'.$chk_key]."',old_charges='".$_REQUEST['total_amt_'.$chk_key]."',entered_date='$trans_date_time',operator_id='$operator_id'");
			
			$up_chrg=", procCharges='$procCharges',totalAmount='".$_REQUEST['totalFee'.$chk_key]."',approvedAmt='".$proc_approved_amt."'";
		}
		imw_query("update patient_charge_list_details set pmt_notes='$pmt_notes' $up_chrg where charge_list_detail_id='$chld_id'");
		
		if($up_chrg!=''){
			set_payment_trans($encounter_id);
			patient_proc_bal_update($encounter_id);
			patient_bal_update($encounter_id);
		}
		
		$cas_type=$cas_code=$write_off_code_id=$adj_code_id="";
		foreach($write_off_code_drop as $wry_key=>$wrt_val){
			$write_off_code_drop_val=$write_off_code_drop[$wry_key];
			if(strpos($write_off_code_drop_val,'_wrt')>0){
				$write_off_code_id=str_replace('_wrt','',$write_off_code_drop_val);
			}
			if(strpos($write_off_code_drop_val,'_dis')>0){
				$write_off_code_id=str_replace('_dis','',$write_off_code_drop_val);
			}
			if(strpos($write_off_code_drop_val,'_adj')>0){
				$adj_code_id=str_replace('_adj','',$write_off_code_drop_val);
			}
			if(strpos($write_off_code_drop_val,'_cas')>0){
				$cas_code_id=str_replace('_cas','',$write_off_code_drop_val);
				$cas_code_arr=array();
				if($reason_code_data[$cas_code_id]['cas_code']!=""){
					$cas_code_arr=explode(' ',trim($reason_code_data[$cas_code_id]['cas_code']));
					if(count($cas_code_arr)<=1){
						$cas_code_arr=explode('-',trim($reason_code_data[$cas_code_id]['cas_code']));
					}
					if($cas_type!=""){
						$cas_type=$cas_type.','.$cas_code_arr[0];
						$cas_code=$cas_code.','.$cas_code_arr[1];
					}else{
						$cas_type=$cas_code_arr[0];
						$cas_code=$cas_code_arr[1];
					}
				}
			}
		}
		
		if($batch_pat_id>0){
		}else{
			if($payment_method=='Tx Balance' && $patient_id>0){
				//PROCEDURE DUE TRANSACTIONS
				$pri_due = $_REQUEST['pri_paid_'.$chk_key];
				$sec_due = $_REQUEST['sec_paid_'.$chk_key];
				$ter_due = $_REQUEST['ter_paid_'.$chk_key];
				$pat_due = $_REQUEST['pat_paid_'.$chk_key];	
				
				$pri_due_old = $_REQUEST['pri_due_old_'.$chk_key];
				$sec_due_old = $_REQUEST['sec_due_old_'.$chk_key];
				$ter_due_old = $_REQUEST['ter_due_old_'.$chk_key];
				$pat_due_old = $_REQUEST['pat_due_old_'.$chk_key];
					
				imw_query("insert into tx_payments set patient_id='$patient_id',encounter_id='$encounter_id',charge_list_id = '$chl_id',
				charge_list_detail_id='$chld_id',pri_due='$pri_due_old',sec_due='$sec_due_old',tri_due='$ter_due_old',pat_due='$pat_due_old',
				payment_date='$paid_date',entered_date='$trans_date_time',payment_time='$trans_time',operator_id='$operator_id'");
				imw_query("update patient_charge_list_details set pri_due='$pri_due',sec_due='$sec_due',tri_due='$ter_due',pat_due='$pat_due' where charge_list_detail_id='$chld_id'");
			}
			
			if($payment_method=='Returned Check' && $check_no!=""){
				if($check_no!=$old_returned_check){
					$return_chk_proc=$fet_pol_main['return_chk_proc'];
					$return_chk_amt=$fet_pol_main['return_chk_amt'];
					include "del_payment.php";
				}
				$old_returned_check=$check_no;
			}
			
			if($payment_method=='Check In_Out' || $payment_method=='Patient Pre Pmts'){
				include "cico_pmt_payment.php";
			}
		}
		
		if(($payment_method=='Update Allow Amt' || $payment_method == 'Paid')){
			if($proc_approved_amt<>$proc_approved_amt_old){
				$wrt_whr="";
				$defult_wrt_amt=$proc_total_amt-$proc_approved_amt;
				if($defult_wrt_amt>0){
					if($write_off_code_id>0){
						$wrt_whr=" ,write_off_code_id='".$write_off_code_id."'";
					}
				}else{
					$wrt_whr=" ,write_off_code_id=''";
				}

				$paid_val=str_replace('Primary','pri',$payment_type);
				$paid_val=str_replace('Secondary','sec',$paid_val);
				$paid_val=str_replace('Tertiary','ter',$paid_val);
				if($payment_method == 'Paid'){
					foreach($paid_by_arr as $paid_key=>$paid_val){
						if($_POST[$paid_val.'_paid_'.$chk_key]>0){
							if($paid_val=="pri" || $paid_val=="sec" || $paid_val=="ter"){
								$paid_by="Insurance";
								$ins_prov_id=$_POST[$paid_val.'_ins_'.$chk_key];
								$pay_type="ins";
							}else if($paid_val=="resp"){
								$paid_by="Res. Party";
								$ins_prov_id=0;
								$pay_type="pat";
							}else{
								$paid_by="Patient";
								$ins_prov_id=0;
								$pay_type="pat";
							}
						}
					}
				}else{
					if($paid_val=="pri" || $paid_val=="sec" || $paid_val=="ter"){
						$paid_by="Insurance";
						$ins_prov_id=$_POST[$paid_val.'_ins_'.$chk_key];
						$pay_type="ins";
					}else if($paid_val=="resp"){
						$paid_by="Res. Party";
						$ins_prov_id=0;
						$pay_type="pat";
					}else{
						$paid_by="Patient";
						$ins_prov_id=0;
						$pay_type="pat";
					}
				}
				
				$paid_key=0;
				if($paid_val=="pri"){
					$paid_key=1;
				}else if($paid_val=="sec"){
					$paid_key=2;
				}else if($paid_val=="ter"){
					$paid_key=3;
				}
				
				if($batch_pat_id>0){
					$chk_allow_amt=imw_query("select write_off_code_id from manual_batch_transactions 
					where batch_id='$b_id' and charge_list_detaill_id='$chld_id' and payment_claims='Allowed' and del_status='0'");
					$num_allow=imw_num_rows($chk_allow_amt);
					
					if($proc_approved_amt>0 && $num_allow==0){
						$ins_batch_allow="insert into manual_batch_transactions set 
						batch_id='$b_id',patient_id='$batch_pat_id',encounter_id='$encounter_id',charge_list_id='$chl_id',
						charge_list_detaill_id='$chld_id',trans_amt='$defult_wrt_amt',
						insurance_id='$ins_prov_id',ins_selected='$paid_key',
						proc_total_amt='$proc_total_amt',proc_allow_amt='$proc_approved_amt',
						trans_date='$paid_date',operator_id='$operator_id',payment_mode='',
						check_no='$check_no',credit_card_type='$cc_type',credit_card_no='$cc_no',credit_card_exp='',
						payment_claims='Allowed',trans_by='$paid_by',write_off_code_id='$write_off_code_id',adj_code_id='$adj_code_id',
						cas_type='$cas_type',cas_code='$cas_code',facility_id='$facility_id'";
						imw_query($ins_batch_allow);
					}else{
						if($num_allow>0){
							$up_batch_allow="update  manual_batch_transactions set
							trans_amt='$defult_wrt_amt',insurance_id='$ins_prov_id',ins_selected='$paid_key',
							proc_total_amt='$proc_total_amt',proc_allow_amt='$proc_approved_amt',
							trans_date='$paid_date',operator_id='$operator_id',payment_mode='',
							check_no='$check_no',credit_card_type='$cc_type',credit_card_no='$cc_no',credit_card_exp='',
							trans_by='$paid_by',write_off_code_id='$write_off_code_id',
							adj_code_id='$adj_code_id',cas_type='$cas_type',cas_code='$cas_code',facility_id='$facility_id'
							where batch_id='$b_id' and charge_list_detaill_id='$chld_id'
							and trans_amt!=$defult_wrt_amt and payment_claims='Allowed'";
							imw_query($up_batch_allow);
						}
					}
					
				}else{
					if($patient_id>0){
						
						imw_query("insert into defaultwriteoff set patient_id='$patient_id',encounter_id='$encounter_id',charge_list_id='$chl_id',
						charge_list_detail_id='$chld_id',write_off_amount='$defult_wrt_amt',write_off_by='$ins_prov_id',write_off_operator_id='$operator_id',
						write_off_dop='$paid_date',write_off_dot='$trans_date_time',facility_id='$facility_id',cas_type='$cas_type',cas_code='$cas_code' $wrt_whr");
						
						imw_query("update patient_charge_list_details set approvedAmt='$proc_approved_amt',write_off='$defult_wrt_amt',write_off_by='$ins_prov_id',write_off_date='$paid_date',
						write_off_opr_id='$operator_id',write_off_dot='$trans_date' $wrt_whr where charge_list_detail_id='$chld_id'");
						
						imw_query("insert into paymentswriteoff set patient_id='$patient_id',encounter_id='$encounter_id',charge_list_detail_id='$chld_id',
						write_off_by_id='$ins_prov_id',era_amt='$defult_wrt_amt',write_off_operator_id='$operator_id',entered_date='$trans_date_time',
						write_off_date='$paid_date',paymentStatus='',CAS_type='$cas_type',CAS_code='$cas_code',facility_id='$facility_id' $wrt_whr");
					}
				}
			}
		}
		
		$chk_batch_tx="";
		foreach($paid_by_arr as $paid_key=>$paid_val){
			$paid_amt=$_POST[$paid_val.'_paid_'.$chk_key];
			if($paid_val=="pri" || $paid_val=="sec" || $paid_val=="ter"){
				$paid_by="Insurance";
				$ins_prov_id=$_POST[$paid_val.'_ins_'.$chk_key];
				$pay_type="ins";
			}else if($paid_val=="resp"){
				$paid_by="Res. Party";
				$ins_prov_id=0;
				$pay_type="pat";
			}else{
				$paid_by="Patient";
				$ins_prov_id=0;
				$pay_type="pat";
			}
			
			if($payment_method == 'Negative Payment'){
				$paid_amt=str_replace('-','',$paid_amt);
			}
			
			if($paid_amt>0 || (($paid_amt=='0.00' || $paid_amt=='0') && $ins_prov_id>0 && $payment_method == 'Paid')){
				if($batch_pat_id>0){
					$ref_del="";
					if($payment_method=='Refund'){
						$insertRefundOffStr = "insert into manual_batch_creditapplied SET
						patient_id = '$batch_pat_id',batch_id='$b_id',amountApplied='$paid_amt',
						dos='$dos',dateApplied='$paid_date',operatorApplied='$operator_id',
						crAppliedTo='payment',crAppliedToEncId='$encounter_id',charge_list_detail_id='$chld_id',
						type='$paid_by',ins_case='$ins_prov_id',insCompany='$paid_key',credit_note='',
						cpt_code='$cpt_code',cpt_code_id='$cpt_code_id',credit_applied='1',facility_id='$facility_id',
						payment_mode='$payment_type',checkCcNumber='$check_no',creditCardNo='$cc_no',creditCardCo='$cc_type'";
						imw_query($insertRefundOffStr);
						$ins_ref_id=imw_insert_id();
						$paid_amt=0;
						$ref_del=",del_status='$ins_ref_id'";
					}
					if($payment_method!='Update Allow Amt' && $payment_method!=""){
						$copay_chld_id=0;
						if($payment_method == 'CoPay'){
							$encounter_id=$chk_key;
							$chld_id=0;
							$copay_chld_id=$_POST['proc_copay_'.$chk_key];
							$chl_id=$_POST['copay_chl_id_'.$chk_key];
							if($paid_amt>0){
								if(($_POST['tot_copay_paid_'.$chk_key]+$paid_amt)>$_POST['tot_enc_copay_'.$chk_key]){
									$paid_amt=$_POST['tot_enc_copay_'.$chk_key]-$_POST['tot_copay_paid_'.$chk_key];
								}
							}
						}
						if($payment_method=='Tx Balance' && $_REQUEST['bal_chk_for_copay'.$chk_key]!=($_REQUEST['pri_paid_'.$chk_key]+$_REQUEST['sec_paid_'.$chk_key]+$_REQUEST['ter_paid_'.$chk_key]+$_REQUEST['pat_paid_'.$chk_key])){
						}else{
							$payment_method=str_replace("Co Insurance","Co-Insurance",$payment_method);
							$payment_method=str_replace("Co Payment","Co-Payment",$payment_method);
							$ins_batch_allow="insert into manual_batch_transactions set 
							batch_id='$b_id',patient_id='$batch_pat_id',encounter_id='$encounter_id',charge_list_id='$chl_id',
							charge_list_detaill_id='$chld_id',trans_amt='$paid_amt',
							insurance_id='$ins_prov_id',ins_selected='$paid_key',proc_total_amt='$proc_total_amt',proc_allow_amt='$proc_approved_amt',
							trans_date='$paid_date',operator_id='$operator_id',payment_mode='$payment_type',
							check_no='$check_no',credit_card_type='$cc_type',credit_card_no='$cc_no',credit_card_exp='',
							payment_claims='$payment_method',trans_by='$paid_by',copay_charge_list_detaill_id='$copay_chld_id',
							write_off_code_id='$write_off_code_id',adj_code_id='$adj_code_id',cas_type='$cas_type',cas_code='$cas_code',facility_id='$facility_id' $ref_del";
							imw_query($ins_batch_allow);
							$ins_batch_trans_id=imw_insert_id();
						}
					}
					
					if($payment_method=='Tx Balance' && $ins_batch_trans_id>0 && $chk_batch_tx==""){
						//PROCEDURE DUE TRANSACTIONS
						$pri_due = $_REQUEST['pri_paid_'.$chk_key];
						$sec_due = $_REQUEST['sec_paid_'.$chk_key];
						$ter_due = $_REQUEST['ter_paid_'.$chk_key];
						$pat_due = $_REQUEST['pat_paid_'.$chk_key];	
						
						$pri_due_old = $_REQUEST['pri_due_old_'.$chk_key];
						$sec_due_old = $_REQUEST['sec_due_old_'.$chk_key];
						$ter_due_old = $_REQUEST['ter_due_old_'.$chk_key];
						$pat_due_old = $_REQUEST['pat_due_old_'.$chk_key];
						if($_REQUEST['bal_chk_for_copay'.$chk_key]==($pri_due+$sec_due+$ter_due+$pat_due)){		
							imw_query("insert into manual_batch_tx_payments set patient_id='$batch_pat_id',encounter_id='$encounter_id',charge_list_id = '$chl_id',
							charge_list_detail_id='$chld_id',pri_due='$pri_due_old',sec_due='$sec_due_old',tri_due='$ter_due_old',pat_due='$pat_due_old',
							payment_date='$paid_date',entered_date='$trans_date_time',payment_time='$trans_time',operator_id='$operator_id',batch_id='$b_id',batch_trans_id='$ins_batch_trans_id',
							pri_due_new='$pri_due',sec_due_new='$sec_due',tri_due_new='$ter_due',pat_due_new='$pat_due'");
							$chk_batch_tx="1";
						}
					}
				}else{
					
					$task_insert_arr=array();
					$task_insert_arr['patientid']=$patient_id;
					$task_insert_arr['operatorid']=$operator_id;
					$task_insert_arr['section']='reason_code';
                    if($cas_type=="" && $cas_code==""){
                         $task_insert_arr['status_id']='';
                    }else{
                        $task_insert_arr['status_id']=$cas_type.'~~'.$cas_code; 
                    }
                   
					$task_insert_arr['encounter_id']=$encounter_id;
					$task_insert_arr['date_of_service']=$dos;
					$task_insert_arr['cpt_code']=$cpt_code;
					$task_insert_arr['patient_name']=$enc_patient_name;
                    if($payment_method == 'Denied'){
                        $task_insert_arr['task_group']=$task_group_id;
                        $task_insert_arr['task_ins_comp']=$ins_prov_id;
                    }
					assign_acc_task_rules_to($task_insert_arr);
					
					if($payment_method == 'CoPay' || $payment_method == 'Paid' || $payment_method == 'Deposit' || $payment_method == 'Interest Payment' || $payment_method == 'Negative Payment'){
						if($payment_method == 'CoPay'){
							$encounter_id=$chk_key;
							if($paid_amt>0){
								$chld_id=0;
								$payment_method="Paid";
								$copayPaid=1;
								if(($_POST['tot_copay_paid_'.$chk_key]+$paid_amt)>$_POST['tot_enc_copay_'.$chk_key]){
									$paid_amt=$_POST['tot_enc_copay_'.$chk_key]-$_POST['tot_copay_paid_'.$chk_key];
								}
								if(($_POST['tot_copay_paid_'.$chk_key]+$paid_amt)<$_POST['tot_enc_copay_'.$chk_key]){
									$copayPaid=0;
								}
								imw_query("update patient_charge_list set copayPaid='$copayPaid',coPayPaidDate='$paid_date' where encounter_id='$encounter_id'");
								imw_query("update patient_charge_list_details set coPayAdjustedAmount='1',paidStatus='Paid',superBillUpdate='1' where charge_list_detail_id = '".$_POST['proc_copay_'.$chk_key]."'");
							}
						}
						if($ins_prov_id>0){
							$statement_pmt=0;
						}else{
							$statement_pmt=$_POST['statement_pmt'];
						}
						$pcpi_sql=imw_query("insert into patient_chargesheet_payment_info set encounter_id='$encounter_id',paid_by='$paid_by',
						payment_amount='$paid_amt',payment_mode='$payment_type',checkNo='$check_no',creditCardNo='$cc_no',creditCardCo='$cc_type',
						date_of_payment='$paid_date',payment_time='$trans_time',operatorId='$operator_id',insProviderId='$ins_prov_id',insCompany='$paid_key',
						paymentClaims='$payment_method',transaction_date='$trans_date',facility_id='$facility_id',statement_pmt='$statement_pmt'");
						$pcpi_id=imw_insert_id();
						$pcdpi_sql=imw_query("insert into patient_charges_detail_payment_info set payment_id='$pcpi_id',paidBy='$paid_by',
						charge_list_detail_id='$chld_id',paidDate='$paid_date',paid_time='$trans_time',paidForProc='$paid_amt',
						operator_id='$operator_id',entered_date='$trans_date_time',CAS_type='$cas_type',CAS_code='$cas_code'");
						patient_proc_tx_update($chld_id,$paid_amt,$pay_type,$paid_key);
						/*if($check_paid_by=="check_in_out"){
							$pcdpi_sql=imw_query("insert into check_in_out_payment_post set encounter_id='$encounter_id',charge_list_detail_id='$chld_id',patient_id='$patient_id',
							acc_payment_id='$pcpi_id',check_in_out_payment_id='$check_in_out_pay_id',check_in_out_payment_detail_id='$check_in_out_pay_detail_id'");
						}*/
						// UPDATE LAST PAYMENT DATE AND AMOUNT
						imw_query("UPDATE patient_charge_list SET lastPayment = '$paid_amt',lastPaymentDate = '$paid_date' WHERE encounter_id = '$encounter_id'");
					}else if($payment_method == 'Deductible'){
						//DEDUCTIBLE TRANSACTIONS
						$deduct_sql=imw_query("insert into payment_deductible set charge_list_detail_id='$chld_id',deduct_amount='$paid_amt',deduct_date='$paid_date',
						deductible_by='$paid_by',deduct_ins_id='$ins_prov_id',deduct_operator_id='$operator_id',entered_date='$trans_date_time',cas_type='$cas_type',cas_code='$cas_code',facility_id='$facility_id'");
						//imw_query("update patient_charge_list_details set deductAmt=deductAmt+$paid_amt where charge_list_detail_id='$chld_id'");
						patient_proc_tx_update($chld_id,0,$pay_type,$paid_key);
					}else if($payment_method == 'Denied'){
						//DENIED TRANSACTIONS
						
						$denied_whr="";
						$denail_data_arr=array();
						$denail_data_arr['denial_cpt_code']=$cpt_code_id;
						if($cas_code!=""){
							$denail_data_arr['denial_cas_code']=$cas_code;
						}else{
							$denail_data_arr['denial_cas_code']=$cas_type;
						}
						$denial_resp=denial_resp_fun($denail_data_arr);
						if($denial_resp>0){
							$denied_whr=",next_responsible_by = '".$operator_id."'";
						}
						$deduct_sql=imw_query("insert into deniedpayment set patient_id='$patient_id',encounter_id='$encounter_id',
						charge_list_detail_id='$chld_id',deniedBy='$paid_by',deniedById='$ins_prov_id',deniedAmount='$paid_amt',deniedDate='$paid_date',
						denialOperatorId='$operator_id',entered_date='$trans_date_time',CAS_type='$cas_type',CAS_code='$cas_code',facility_id='$facility_id' $denied_whr");
						imw_query("update patient_charge_list_details set claimDenied='1' where charge_list_detail_id='$chld_id'");
						if($denial_resp>0){
							patient_proc_tx_update($chld_id,0,$pay_type,$paid_key);
						}
					}else if($payment_method == 'Write Off' || $payment_method == 'Discount'){
						//WRITE-OFF AND DISCOUNT TRANSACTIONS
						$write_sql = imw_query("insert into paymentswriteoff set patient_id='$patient_id',encounter_id='$encounter_id',
						charge_list_detail_id='$chld_id',write_off_by_id='$ins_prov_id',write_off_amount='$paid_amt',write_off_operator_id='$operator_id',
						entered_date='$trans_date_time',write_off_date='$paid_date',write_off_code_id='$write_off_code_id',paymentStatus='$payment_method',
						CAS_type='$cas_type',CAS_code='$cas_code',facility_id='$facility_id'");
						$dis_id=imw_insert_id();
						if($_POST['cash_dis']=="yes"){
							$chld_cash= " ,cash_discount='".$dis_id."'";
						}
						imw_query("update patient_charge_list_details set newBalance=newBalance-$paid_amt,paidStatus='Paid' $chld_cash where charge_list_detail_id='$chld_id'");
						patient_proc_tx_update($chld_id,$paid_amt,$pay_type,$paid_key);
						if($payment_method == 'Discount'){
							re_calculate_tax($encounter_id);
						}
					}else if($payment_method=='Adjustment' || $payment_method=='Over Adjustment' || $payment_method=='Co Insurance' || $payment_method=='Co Payment'){
						$payment_method=str_replace("Co Insurance","Co-Insurance",$payment_method);
						$payment_method=str_replace("Co Payment","Co-Payment",$payment_method);
						$adj_sql = imw_query("insert into account_payments set patient_id='$patient_id',encounter_id='$encounter_id',charge_list_id='$chl_id',
						charge_list_detail_id='$chld_id',payment_by='$paid_by',payment_method='$payment_type',check_number='$check_no',cc_type='$cc_type',
						cc_number='$cc_no',ins_id='$ins_prov_id',payment_amount='$paid_amt',payment_date='$paid_date',operator_id='$operator_id',
						entered_date='$trans_date_time',payment_type='$payment_method',payment_code_id='$adj_code_id',cas_type='$cas_type',cas_code='$cas_code',facility_id='$facility_id'");
						patient_proc_tx_update($chld_id,$paid_amt,$pay_type,$paid_key);
					}else if($payment_method=='Refund'){
						$ref_sql = imw_query("insert into creditapplied set patient_id='$patient_id',amountApplied='$paid_amt',overpayamount='$over_payment',
						dos='$dos',dateApplied='$paid_date',operatorApplied='$operator_id',entered_date='$trans_date_time',crAppliedTo='payment',
						crAppliedToEncId='$encounter_id',type='$paid_by',ins_case='$ins_prov_id',insCompany='$paid_key',payment_mode='$payment_type',
						checkCcNumber='$check_no',creditCardNo='$cc_no',creditCardCo='$cc_type',cpt_code='$cpt_code',cpt_code_id='$cpt_code_id',
						charge_list_detail_id='$chld_id',credit_applied='1',facility_id='$facility_id'");
						patient_proc_tx_update($chld_id,$paid_amt,$pay_type,$paid_key);
					}else if($payment_method=='Credit' || $payment_method=='Debit'){
						if($_REQUEST['deb_patient_id']>0){
							$deb_amt=$_REQUEST['deb_amt'];
							$deb_chld_id=$_REQUEST['deb_chld_id'];
							$deb_ins_type=$_REQUEST['deb_ins_type'];
							
							$deb_qry=imw_query("select patient_charge_list.encounter_id,patient_charge_list.date_of_service,patient_charge_list.patient_id,
							patient_charge_list_details.procCode,patient_charge_list.case_type_id 
							from patient_charge_list join patient_charge_list_details on patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id 
							where patient_charge_list_details.charge_list_detail_id='$deb_chld_id'");
							$deb_row=imw_fetch_array($deb_qry);
							$deb_dos=$deb_row['date_of_service'];
							$deb_enc=$deb_row['encounter_id'];
							$deb_pat_id=$deb_row['patient_id'];
							$deb_cpt_code_id=$deb_row['procCode'];
							$deb_case_type_id=$deb_row['case_type_id'];
							$deb_paid_by='Patient';
							if($deb_ins_type>0){
								$deb_paid_by='Insurance';
							}
							$cpt_qry =imw_query("SELECT cpt4_code FROM cpt_fee_tbl  WHERE cpt_fee_id = '$deb_cpt_code_id'");
							$cpt_row=imw_fetch_array($cpt_qry);
							$deb_cpt_code=$cpt_row['cpt4_code'];
							
							$ref_sql = imw_query("insert into creditapplied set patient_id_adjust='$patient_id',amountApplied='$paid_amt',dateApplied='$paid_date',
							operatorApplied='$operator_id',entered_date='$trans_date_time',crAppliedTo='adjustment',crAppliedToEncId_adjust='$encounter_id',
							type='$deb_paid_by',ins_case='$deb_case_type_id',insCompany='$deb_ins_type',charge_list_detail_id_adjust='$chld_id',credit_applied='1',
							overpayamount='$deb_amt',dos='$deb_dos',crAppliedToEncId='$deb_enc',charge_list_detail_id='$deb_chld_id',patient_id='$deb_pat_id',
							cpt_code='$deb_cpt_code',cpt_code_id='$deb_cpt_code_id',facility_id='$facility_id'");
							patient_proc_tx_update($chld_id,$paid_amt,$pay_type,$paid_key);
						}else{
							if($payment_method=='Credit' && $paid_amt==array_sum($tot_debit_amt_arr)){
								$ref_sql = imw_query("insert into creditapplied set patient_id_adjust='$patient_id',amountApplied='$paid_amt',dateApplied='$paid_date',
								operatorApplied='$operator_id',entered_date='$trans_date_time',crAppliedTo='adjustment',crAppliedToEncId_adjust='$encounter_id',
								type='$paid_by',ins_case='$ins_prov_id',insCompany='$paid_key',charge_list_detail_id_adjust='$chld_id',credit_applied='1',facility_id='$facility_id' $deb_data_qry");
								$crd_ins_id=imw_insert_id();
								$crd_ins_id_arr[$crd_ins_id]=$crd_ins_id;
								patient_proc_tx_update($chld_id,$paid_amt,$pay_type,$paid_key);
							}else if($payment_method=='Debit'){
								if(count($crd_ins_id_arr)>0){
									$crd_ins_id_imp=implode(',',$crd_ins_id_arr);
									$ref_sql = imw_query("update creditapplied set overpayamount='$over_payment',dos='$dos',crAppliedToEncId='$encounter_id',
									charge_list_detail_id='$chld_id',patient_id='$patient_id',cpt_code='$cpt_code',cpt_code_id='$cpt_code_id'
									where crAppId in($crd_ins_id_imp)");
								}
								$deb_data_qry=",overpayamount='$over_payment',dos='$dos',crAppliedToEncId='$encounter_id',charge_list_detail_id='$chld_id',patient_id='$patient_id',cpt_code='$cpt_code',cpt_code_id='$cpt_code_id'";
								patient_proc_tx_update($chld_id,$paid_amt,$pay_type,$paid_key);
							}
						}
					}
				}
			}
		}
		set_payment_trans($encounter_id);
		if($deb_enc>0){
			set_payment_trans($deb_enc);
		}
		$encounter_id_arr[]=$encounter_id;
	}
}
if(count($ar_claim_status_arr)>0){
	foreach($ar_claim_status_arr as $chk_enc_key=>$chk_enc_val){
		imw_query("UPDATE patient_charge_list SET claim_status = '".$chk_enc_val."' WHERE encounter_id = '".$chk_enc_key."'");
	}
}
if($_REQUEST['batch_pat_id']>0){
	if($_REQUEST['enc_id_read']>0 && $encounter_id<=0){
		$encounter_id=$_REQUEST['enc_id_read'];
	}
	echo "b_id=$b_id&encounter_id=$encounter_id&batch_pat_id=$batch_pat_id";
}else if($_REQUEST['deb_patient_id']>0){
	echo $deb_enc;	
}else{
?>
<script>
	var eId = '<?php echo $_REQUEST['enc_id_read']; ?>';
	var eId_all = '<?php echo implode(',',$encounter_id_arr); ?>';
	if(eId_all<=0){
		eId_all = eId;
	}
	<?php if($apply=='applyRecieptSubmit'){ ?>
		window.open("receipt.php?eId="+eId_all,'','width=1000,height=675,top=10,left=40,scrollbars=yes,resizable=yes');
	<?php } ?>
	top.fmain.location.href="makePayment.php?encounter_id="+eId;
</script>
<?php
}
?>