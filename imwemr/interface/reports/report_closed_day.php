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
set_time_limit(0);
function exp_date_time($dat,$ret){
	$dat_exp=explode(' ',$dat);
	if($ret=="date"){
		return $dat_exp[0];
	}else if($ret=="time"){
		return $dat_exp[1];
	}
}
?>
<?php
$start_table="patient_charge_list_details";
$curr_dt=date('Y-m-d H:i:s');
$start_val=0;
$end = 500;

if($_REQUEST['start_table']!=""){
	$start_table=$_REQUEST['start_table'];
}
if($start_table=="patient_charge_list_details"){
	$end = 100;
}

if($_REQUEST['start_val']>0){
	//$start_val = $_REQUEST['start_val'];
}
if($long_end_point<=0){
	$end = 100;
}
if($cron_job!=""){
	$end = 10000;
}
$pol_msg_info="";
if($skip_file_process!=""){
}else{
	if($start_table=="patient_charge_list_details" && $_REQUEST['show_start_val']<=0){
		$pol_qry=imw_query("select rcd_finished from copay_policies");
		$pol_row=imw_fetch_array($pol_qry);
		$rcd_finished_diff=abs(strtotime($curr_dt)-strtotime($pol_row['rcd_finished']))/(60);
		
		$rcd_on_exp=explode(' ',$pol_row['rcd_finished']);
		$rcd_on_date_exp=explode('-',$rcd_on_exp[0]);
		$rcd_on_time_exp=explode(':',$rcd_on_exp[1]);
		$show_chld_modifier_on=$rcd_on_date_exp[1].'-'.$rcd_on_date_exp[2].'-'.$rcd_on_date_exp[0].' '.date("g:i A", strtotime($rcd_on_exp[1]));

		if($pol_row['rcd_finished']=="0000-00-00 00:00:00" || $rcd_finished_diff>10){
			imw_query("update copay_policies set rcd_finished='$curr_dt'");
		}else{
			//$start_table="";
			//$pol_msg_info="Day Close Report is already running on the server since ".$show_chld_modifier_on.".<br> Please wait untill it is finished.";
		}
	}
}

$trans_source=$_SERVER["REQUEST_URI"].'---'.$end.'---'.$_SESSION['authId'];

$row_arr=$row_ap_arr=$rp_row_trans_arr=array();
	
$parent_id=$master_tbl_id=$patient_id=$encounter_id=$charge_list_id=$charge_list_detail_id=$trans_by=$trans_ins_id=$trans_method="";
$check_number=$cc_type=$cc_number=$cc_exp_date=$trans_type=$trans_amount=$trans_code_id=$batch_id=$cap_main_id="";
$trans_dot=$trans_dot_time=$trans_dop=$trans_dop_time=$trans_operator_id=$era_amt=$cas_type=$cas_code=$trans_qry_type="";
$trans_del_date=$trans_del_time=$trans_del_operator_id=$units="";

if($start_table!=""){
	$ins_qry_trans="insert into report_enc_trans (parent_id,master_tbl_id,patient_id,encounter_id,charge_list_id,charge_list_detail_id,
	trans_by,trans_ins_id,trans_method,check_number,cc_type,cc_number,cc_exp_date,trans_type,trans_amount,units,trans_code_id,
	batch_id,cap_main_id,trans_dot,trans_dot_time,trans_dop,trans_dop_time,trans_operator_id,era_amt,cas_type,cas_code,
	trans_qry_type,facility_id,trans_source,trans_del_date,trans_del_time,trans_del_operator_id) value";
	
	$ins_qry_chl="insert into report_enc_detail (report_enc_detail_id,patient_id,encounter_id,charge_list_id,charge_list_detail_id,case_type_id,date_of_service,proc_code_id,pri_prov_id,sec_prov_id,
				facility_id,gro_id,reff_phy_id,pri_ins_id,sec_ins_id,tri_ins_id,units,charges,total_charges,pri_due,sec_due,tri_due,
				pat_due,mod_id1,mod_id2,mod_id3,mod_id4,dx_id1,dx_id2,dx_id3,dx_id4,dx_id5,dx_id6,dx_id7,dx_id8,dx_id9,dx_id10,dx_id11,dx_id12,
				approved_amt,write_off,write_off_code_id,write_off_dop,write_off_dot,write_off_by,write_off_opr_id,proc_balance,superbill_id,
				sb_proc_id,entered_date,operator_id,del_status,del_operator_id,trans_del_date,last_pri_paid_date,last_sec_paid_date,
				last_ter_paid_date,last_pat_paid_date,batch_id,from_sec_due_date,from_ter_due_date,from_pat_due_date,primary_provider_id_for_reports,
				proc_selfpay,first_posted_date,first_posted_opr_id,sch_app_id,over_payment,submitted,lastPayment,lastPaymentDate,statement_status,statement_date,
				letter_sent_date,collection,collectionAmount,collectionDate,collection_sent,letter_sent_id,re_submitted,re_submitted_date,billing_facility_id) value";
}

if($start_table=="patient_charge_list_details"){
	$ins_qry=$ins_qry_trans;
	$acc_qry=imw_query("select pcld.*,pcl.encounter_id,pcl.case_type_id,pcl.facility_id,pcl.primaryInsuranceCoId,pcl.secondaryInsuranceCoId,pcl.
		tertiaryInsuranceCoId,pcl.date_of_service,pcl.first_posted_date,pcl.first_posted_opr_id,pcl.reff_phy_id,pcl.gro_id,pcl.sch_app_id,pcl.Submitted,
		pcl.lastPayment,pcl.lastPaymentDate,pcl.statement_status,pcl.statement_date,pcl.letter_sent_date,pcl.collection,pcl.collectionAmount,pcl.collectionDate,
		pcl.collection_sent,pcl.letter_sent_id,pcl.Re_submitted,pcl.Re_submitted_date,pcl.billing_facility_id
		from patient_charge_list as pcl join patient_charge_list_details as pcld on pcl.charge_list_id=pcld.charge_list_id 
		where (pcld.report_date_timestamp!='0000-00-00 00:00:00' or pcl.report_date_timestamp!='0000-00-00 00:00:00') order by pcld.charge_list_detail_id asc limit $start_val , $end");
	if(imw_num_rows($acc_qry)>0){
		while($acc_row=imw_fetch_array($acc_qry)){
			$row_arr[$acc_row['charge_list_detail_id']]=$acc_row;
			$row_enc_arr[$acc_row['encounter_id']]=$acc_row['encounter_id'];
			$row_chld_arr[$acc_row['charge_list_detail_id']]=$acc_row['charge_list_detail_id'];
		}
		$row_enc_imp=implode("','",$row_enc_arr);
		$chl_mod_qry=imw_query("select enc_id,modifier_on,modifier_by from patient_charge_list_modifiy where enc_id in('$row_enc_imp') order by id asc");
		while($chl_mod_row=imw_fetch_array($chl_mod_qry)){
			$chl_mod_row_arr[$chl_mod_row['enc_id']]=$chl_mod_row;
		}
		
		$row_chld_imp=implode("','",$row_chld_arr);
		$rp_acc_qry=imw_query("select charge_list_detail_id,report_enc_detail_id,charges,units from report_enc_detail where charge_list_detail_id in('$row_chld_imp') order by report_enc_detail_id asc");
		while($rp_acc_row=imw_fetch_array($rp_acc_qry)){
			$rp_row_arr[$rp_acc_row['charge_list_detail_id']]=$rp_acc_row;
		}
		
		$rp_acc_trans_qry=imw_query("select report_trans_id,master_tbl_id,trans_type from report_enc_trans where master_tbl_id in('$row_chld_imp') and master_tbl_id>0 and trans_type='charges' order by report_trans_id asc");
		while($rp_acc_trans_row=imw_fetch_array($rp_acc_trans_qry)){
			$rp_row_trans_arr[$rp_acc_trans_row['master_tbl_id']][$rp_acc_trans_row['trans_type']]=$rp_acc_trans_row;
		}
		
		foreach($row_arr as $row_key=>$row_val){
			$row=$row_arr[$row_key];
			$patient_id=$row['patient_id'];
			$charge_list_id=$row['charge_list_id'];
			$charge_list_detail_id=$row['charge_list_detail_id'];
			$proc_code_id=$row['procCode'];
			$pri_prov_id=$row['primaryProviderId'];
			$sec_prov_id=$row['secondaryProviderId'];
			$units=$row['units'];
			$charges=$row['procCharges'];
			$total_charges=$row['totalAmount'];
			$pri_due=$row['pri_due'];
			$sec_due=$row['sec_due'];
			$tri_due=$row['tri_due'];
			$pat_due=$row['pat_due'];
			$mod_id1=$row['modifier_id1'];
			$mod_id2=$row['modifier_id2'];
			$mod_id3=$row['modifier_id3'];
			$mod_id4=$row['modifier_id4'];
			$dx_id1=$row['diagnosis_id1'];
			$dx_id2=$row['diagnosis_id2'];
			$dx_id3=$row['diagnosis_id3'];
			$dx_id4=$row['diagnosis_id4'];
			$dx_id5=$row['diagnosis_id5'];
			$dx_id6=$row['diagnosis_id6'];
			$dx_id7=$row['diagnosis_id7'];
			$dx_id8=$row['diagnosis_id8'];
			$dx_id9=$row['diagnosis_id9'];
			$dx_id10=$row['diagnosis_id10'];
			$dx_id11=$row['diagnosis_id11'];
			$dx_id12=$row['diagnosis_id12'];
			$approved_amt=$row['approvedAmt'];
			$write_off=$row['write_off'];
			$write_off_code_id=$row['write_off_code_id'];
			$write_off_dop=$row['write_off_date'];
			$write_off_dot=$row['write_off_dot'];
			$write_off_by=$row['write_off_by'];
			$write_off_opr_id=$row['write_off_opr_id'];
			$proc_balance=$row['newBalance'];
			$superbill_id=$row['idSuperBill'];
			$sb_proc_id=$row['procedureInfoId'];
			$entered_date=$row['entered_date'];
			$operator_id=$row['operator_id'];
			$del_status=$row['del_status'];
			$trans_del_date_time=$row['trans_del_date'];
			$del_operator_id=$row['del_operator_id'];
			$last_pri_paid_date=$row['last_pri_paid_date'];
			$last_sec_paid_date=$row['last_sec_paid_date'];
			$last_ter_paid_date=$row['last_ter_paid_date'];
			$last_pat_paid_date=$row['last_pat_paid_date'];
			$batch_id=$row['batch_id'];
			$from_sec_due_date=$row['from_sec_due_date'];
			$from_ter_due_date=$row['from_ter_due_date'];
			$from_pat_due_date=$row['from_pat_due_date'];
			$primary_provider_id_for_reports=$row['primary_provider_id_for_reports'];
			$proc_selfpay=$row['proc_selfpay'];
			$over_payment=$row['overPaymentForProc'];
			
			
			$encounter_id=$row['encounter_id'];
			$case_type_id=$row['case_type_id'];
			$facility_id=$row['facility_id'];
			$pri_ins_id=$row['primaryInsuranceCoId'];
			$sec_ins_id=$row['secondaryInsuranceCoId'];
			$tri_ins_id=$row['tertiaryInsuranceCoId'];
			$date_of_service=$row['date_of_service'];
			$first_posted_date=$row['first_posted_date'];
			$first_posted_opr_id=$row['first_posted_opr_id'];
			$reff_phy_id=$row['reff_phy_id'];
			$gro_id=$row['gro_id'];
			$sch_app_id=$row['sch_app_id'];
			$submitted=$row['Submitted'];
			$lastPayment=$row['lastPayment'];
			$lastPaymentDate=$row['lastPaymentDate'];
			$statement_status=$row['statement_status'];
			$statement_date=$row['statement_date'];
			$letter_sent_date=$row['letter_sent_date'];
			$collection=$row['collection'];
			$collectionAmount=$row['collectionAmount'];
			$collectionDate=$row['collectionDate'];
			$collection_sent=$row['collection_sent'];
			$letter_sent_id=$row['letter_sent_id'];
			$re_submitted=$row['Re_submitted'];
			$re_submitted_date=$row['Re_submitted_date'];
			$billing_facility_id=$row['billing_facility_id'];

			$master_tbl_id=$charge_list_detail_id;
			$master_tbl_id_arr[]=$master_tbl_id;
			$master_chl_id_arr[]=$charge_list_id;
			
			if($rp_row_arr[$charge_list_detail_id]['charge_list_detail_id']>0){
				$qry_in_up="update ";
				$qry_in_up_whr=" where charge_list_detail_id='$charge_list_detail_id'";
				
				imw_query("$qry_in_up report_enc_detail set patient_id='$patient_id',encounter_id='$encounter_id',charge_list_id='$charge_list_id',
				charge_list_detail_id='$charge_list_detail_id',case_type_id='$case_type_id',date_of_service='$date_of_service',proc_code_id='$proc_code_id',pri_prov_id='$pri_prov_id',sec_prov_id='$sec_prov_id',
				facility_id='$facility_id',gro_id='$gro_id',reff_phy_id='$reff_phy_id',pri_ins_id='$pri_ins_id',sec_ins_id='$sec_ins_id',tri_ins_id='$tri_ins_id',units='$units',charges='$charges',total_charges='$total_charges',
				pri_due='$pri_due',sec_due='$sec_due',tri_due='$tri_due',pat_due='$pat_due',mod_id1='$mod_id1',mod_id2='$mod_id2',mod_id3='$mod_id3',mod_id4='$mod_id4',
				dx_id1='$dx_id1',dx_id2='$dx_id2',dx_id3='$dx_id3',dx_id4='$dx_id4',dx_id5='$dx_id5',dx_id6='$dx_id6',dx_id7='$dx_id7',dx_id8='$dx_id8',
				dx_id9='$dx_id9',dx_id10='$dx_id10',dx_id11='$dx_id11',dx_id12='$dx_id12',approved_amt='$approved_amt',write_off='$write_off',write_off_code_id='$write_off_code_id',
				write_off_dop='$write_off_dop',write_off_dot='$write_off_dot',write_off_by='$write_off_by',write_off_opr_id='$write_off_opr_id',
				proc_balance='$proc_balance',superbill_id='$superbill_id',sb_proc_id='$sb_proc_id',entered_date='$entered_date',operator_id='$operator_id',
				del_status='$del_status',del_operator_id='$del_operator_id',trans_del_date='$trans_del_date_time',last_pri_paid_date='$last_pri_paid_date',last_sec_paid_date='$last_sec_paid_date',
				last_ter_paid_date='$last_ter_paid_date',last_pat_paid_date='$last_pat_paid_date',batch_id='$batch_id',from_sec_due_date='$from_sec_due_date',
				from_ter_due_date='$from_ter_due_date',from_pat_due_date='$from_pat_due_date',primary_provider_id_for_reports='$primary_provider_id_for_reports',
				proc_selfpay='$proc_selfpay',first_posted_date='$first_posted_date',first_posted_opr_id='$first_posted_opr_id',sch_app_id='$sch_app_id',
				over_payment='$over_payment',submitted='$submitted',lastPayment='$lastPayment',lastPaymentDate='$lastPaymentDate',statement_status='$statement_status',
				statement_date='$statement_date',letter_sent_date='$letter_sent_date',collection='$collection',collectionAmount='$collectionAmount',collectionDate='$collectionDate',
				collection_sent='$collection_sent',letter_sent_id='$letter_sent_id',re_submitted='$re_submitted',re_submitted_date='$re_submitted_date',billing_facility_id='$billing_facility_id' $qry_in_up_whr");
				
			}else{
				$ins_qry_chl.="('$master_tbl_id','$patient_id','$encounter_id','$charge_list_id','$charge_list_detail_id','$case_type_id','$date_of_service','$proc_code_id','$pri_prov_id','$sec_prov_id',
				'$facility_id','$gro_id','$reff_phy_id','$pri_ins_id','$sec_ins_id','$tri_ins_id','$units','$charges','$total_charges',
				'$pri_due','$sec_due','$tri_due','$pat_due','$mod_id1','$mod_id2','$mod_id3','$mod_id4','$dx_id1','$dx_id2','$dx_id3','$dx_id4','$dx_id5','$dx_id6','$dx_id7','$dx_id8',
				'$dx_id9','$dx_id10','$dx_id11','$dx_id12','$approved_amt','$write_off','$write_off_code_id','$write_off_dop','$write_off_dot','$write_off_by','$write_off_opr_id',
				'$proc_balance','$superbill_id','$sb_proc_id','$entered_date','$operator_id','$del_status','$del_operator_id','$trans_del_date_time','$last_pri_paid_date',
				'$last_sec_paid_date','$last_ter_paid_date','$last_pat_paid_date','$batch_id','$from_sec_due_date','$from_ter_due_date','$from_pat_due_date','$primary_provider_id_for_reports',
				'$proc_selfpay','$first_posted_date','$first_posted_opr_id','$sch_app_id','$over_payment','$submitted','$lastPayment','$lastPaymentDate','$statement_status','$statement_date',
				'$letter_sent_date','$collection','$collectionAmount','$collectionDate','$collection_sent','$letter_sent_id','$re_submitted','$re_submitted_date','$billing_facility_id'),";
			}
			$enc_tran_row=$rp_row_arr[$charge_list_detail_id];
			if($rp_row_arr[$charge_list_detail_id]['charge_list_detail_id']>0){
				$master_tbl_id=$enc_tran_row['report_enc_detail_id'];
				$trans_dop=exp_date_time($chl_mod_row['modifier_on'],'date');
				$trans_dop_time=exp_date_time($chl_mod_row['modifier_on'],'time');
				$trans_dot=exp_date_time($chl_mod_row['modifier_on'],'date');
				$trans_dot_time=exp_date_time($chl_mod_row['modifier_on'],'time');
				$trans_operator_id=$chl_mod_row['modifier_by'];
				$trans_qry_type="update";
			}else{
				//$master_tbl_id=imw_insert_id();
				$trans_dop=exp_date_time($row['entered_date'],'date');
				$trans_dop_time=exp_date_time($row['entered_date'],'time');
				$trans_dot=exp_date_time($row['entered_date'],'date');
				$trans_dot_time=exp_date_time($row['entered_date'],'time');
				$trans_operator_id=$row['operator_id'];
				$trans_qry_type="insert";
			}
			
			$trans_del_date=exp_date_time($row['trans_del_date'],'date');
			$trans_del_time=exp_date_time($row['trans_del_date'],'time');
			$trans_del_operator_id=$row['del_operator_id'];
			
			if($charges!=$enc_tran_row['charges'] || $units!=$enc_tran_row['units'] || $row['del_operator_id']>0 || $master_tbl_id>0){
				$trans_type='charges';
				$trans_amount=$charges;
				$units=$units;
				
				if($rp_row_trans_arr[$master_tbl_id][$trans_type]['report_trans_id']>0){
					$parent_id=$rp_row_trans_arr[$master_tbl_id][$trans_type]['report_trans_id'];
				}else{
					$parent_id=0;
				}
				
				$ins_qry.="('$parent_id','$master_tbl_id','$patient_id','$encounter_id','$charge_list_id','$charge_list_detail_id','$trans_by',
				'$trans_ins_id','$trans_method','$check_number','$cc_type','$cc_number','$cc_exp_date','$trans_type','$trans_amount',$units,
				'$trans_code_id','$batch_id','$cap_main_id','$trans_dot','$trans_dot_time','$trans_dop','$trans_dop_time','$trans_operator_id',
				'$era_amt','$cas_type','$cas_code','$trans_qry_type','$billing_facility_id','$trans_source',";
				if($trans_del_operator_id>0 && $parent_id==0){
					$ins_qry.="'','',''),";
				}else{
					$ins_qry.="'$trans_del_date','$trans_del_time','$trans_del_operator_id'),";
				}
				
				if($trans_del_operator_id>0 && $parent_id==0){
					$trans_qry_type="update";
					$ins_qry.="('$del_parent_id','$master_tbl_id','$patient_id','$encounter_id','$charge_list_id','$charge_list_detail_id','$trans_by',
					'$trans_ins_id','$trans_method','$check_number','$cc_type','$cc_number','$cc_exp_date','$trans_type','$trans_amount',$units,
					'$trans_code_id','$batch_id','$cap_main_id','$trans_dot','$trans_dot_time','$trans_dop','$trans_dop_time','$trans_operator_id',
					'$era_amt','$cas_type','$cas_code','$trans_qry_type','$billing_facility_id','$trans_source','$trans_del_date','$trans_del_time','$trans_del_operator_id'),";
				}
			}
		}
	
		$ins_qry_run_chl=substr($ins_qry_chl,0,-1).';';
		imw_query($ins_qry_run_chl);
		
		$ins_qry_run=substr($ins_qry,0,-1).';';
		imw_query($ins_qry_run);
		
		$master_tbl_id_imp=implode("','",$master_tbl_id_arr);
		$master_chl_id_imp=implode("','",$master_chl_id_arr);
		imw_query("update patient_charge_list_details set report_date_timestamp='' where charge_list_detail_id in('$master_tbl_id_imp')");
		imw_query("update patient_charge_list set report_date_timestamp='' where charge_list_id in('$master_chl_id_imp')");
		$ins_qry="";
		if($cron_job!=""){
			$start_table="account_payments";
		}
	
	}else{
		$start_table="account_payments";
		$start_val=$show_start_val=0;
	}
}
if($start_table=="account_payments"){
	$ins_qry=$ins_qry_trans;
	$row_arr=$row_ap_arr=$rp_row_trans_arr=$master_tbl_id_arr=array();
	$acc_qry=imw_query("select * from account_payments where report_date_timestamp!='0000-00-00 00:00:00' and payment_type!='Co-Insurance' and payment_type!='Co-Payment' order by id asc limit $start_val , $end");
	if(imw_num_rows($acc_qry)>0){
		while($acc_row=imw_fetch_array($acc_qry)){
			$row_arr[$acc_row['id']]=$acc_row;
			$row_ap_arr[$acc_row['id']]=$acc_row['id'];
		}
		
		$row_ap_imp=implode("','",$row_ap_arr);
		$rp_acc_trans_qry=imw_query("select report_trans_id,master_tbl_id,trans_type from report_enc_trans where master_tbl_id in('$row_ap_imp') and trans_type in('Adjustment','Over Adjustment','Returned Check') order by report_trans_id asc");
		while($rp_acc_trans_row=imw_fetch_array($rp_acc_trans_qry)){
			$rp_row_trans_arr[$rp_acc_trans_row['master_tbl_id']][$rp_acc_trans_row['trans_type']]=$rp_acc_trans_row;
		}
	foreach($row_arr as $row_key=>$row_val){
			$row=$row_arr[$row_key];	
			$master_tbl_id_arr[]=$row['id'];
			$master_tbl_id=$row['id'];
			$patient_id=$row['patient_id'];
			$encounter_id=$row['encounter_id'];
			$charge_list_id=$row['charge_list_id'];
			$charge_list_detail_id=$row['charge_list_detail_id'];
			$trans_by=$row['payment_by'];
			$trans_ins_id=$row['ins_id'];
			$trans_method=$row['payment_method'];
			$check_number=$row['check_number'];
			$cc_type=$row['cc_type'];
			$cc_number=$row['cc_number'];
			$cc_exp_date=$row['cc_exp_date'];
			$trans_type=$row['payment_type'];
			$trans_amount=$row['payment_amount'];
			$trans_dop=$row['payment_date'];
			$trans_dot=exp_date_time($row['entered_date'],'date');
			$trans_dot_time=exp_date_time($row['entered_date'],'time');
			$trans_operator_id=$row['operator_id'];
			$trans_code_id=$row['payment_code_id'];
			$batch_id=$row['batch_id'];
			$trans_del_date=exp_date_time($row['del_date_time'],'date');
			$trans_del_time=exp_date_time($row['del_date_time'],'time');
			$trans_del_operator_id=$row['del_operator_id'];
			$facility_id=$row['facility_id'];
			$trans_qry_type="insert";
			if($row['modified_by']>0){
				$trans_qry_type="update";
			}
			
			if($rp_row_trans_arr[$master_tbl_id][$trans_type]['report_trans_id']>0){
				$parent_id=$rp_row_trans_arr[$master_tbl_id][$trans_type]['report_trans_id'];
			}else{
				$parent_id=0;
			}
			
			$ins_qry.="('$parent_id','$master_tbl_id','$patient_id','$encounter_id','$charge_list_id','$charge_list_detail_id','$trans_by',
			'$trans_ins_id','$trans_method','$check_number','$cc_type','$cc_number','$cc_exp_date','$trans_type','$trans_amount','',
			'$trans_code_id','$batch_id','$cap_main_id','$trans_dot','$trans_dot_time','$trans_dop','$trans_dop_time','$trans_operator_id',
			'$era_amt','$cas_type','$cas_code','$trans_qry_type','$facility_id','$trans_source',";
			if($trans_del_operator_id>0 && $parent_id==0){
				$ins_qry.="'','',''),";
			}else{
				$ins_qry.="'$trans_del_date','$trans_del_time','$trans_del_operator_id'),";
			}
			
			if($trans_del_operator_id>0 && $parent_id==0){
				$trans_qry_type="update";
				$ins_qry.="('$del_parent_id','$master_tbl_id','$patient_id','$encounter_id','$charge_list_id','$charge_list_detail_id','$trans_by',
				'$trans_ins_id','$trans_method','$check_number','$cc_type','$cc_number','$cc_exp_date','$trans_type','$trans_amount','',
				'$trans_code_id','$batch_id','$cap_main_id','$trans_dot','$trans_dot_time','$trans_dop','$trans_dop_time','$trans_operator_id',
				'$era_amt','$cas_type','$cas_code','$trans_qry_type','$facility_id','$trans_source','$trans_del_date','$trans_del_time','$trans_del_operator_id'),";
			}
		}
		$ins_qry_run=substr($ins_qry,0,-1).';';
		imw_query($ins_qry_run);
		$master_tbl_id_imp=implode("','",$master_tbl_id_arr);
		imw_query("update account_payments set report_date_timestamp='',date_timestamp=date_timestamp where id in('$master_tbl_id_imp')");
		$ins_qry="";
		if($cron_job!=""){
			$start_table="paymentswriteoff";
		}
		
	}else{
		$start_table="paymentswriteoff";
		$start_val=$show_start_val=0;
	}
}
if($start_table=="paymentswriteoff"){
	$ins_qry=$ins_qry_trans;
	$row_arr=$row_ap_arr=$rp_row_trans_arr=$master_tbl_id_arr=array();
	$acc_qry=imw_query("select * from paymentswriteoff where report_date_timestamp!='0000-00-00 00:00:00' order by write_off_id asc limit $start_val , $end");
	if(imw_num_rows($acc_qry)>0){
		while($acc_row=imw_fetch_array($acc_qry)){
			$row_arr[$acc_row['write_off_id']]=$acc_row;
			$row_ap_arr[$acc_row['write_off_id']]=$acc_row['write_off_id'];
		}
		
		$row_ap_imp=implode("','",$row_ap_arr);
		$rp_acc_trans_qry=imw_query("select report_trans_id,master_tbl_id,trans_type from report_enc_trans where master_tbl_id in('$row_ap_imp') and trans_type in('Write Off','Discount') order by report_trans_id asc");
		while($rp_acc_trans_row=imw_fetch_array($rp_acc_trans_qry)){
			$rp_row_trans_arr[$rp_acc_trans_row['master_tbl_id']][$rp_acc_trans_row['trans_type']]=$rp_acc_trans_row;
		}
		
		foreach($row_arr as $row_key=>$row_val){
			$row=$row_arr[$row_key];	
			$master_tbl_id_arr[]=$row['write_off_id'];
			$master_tbl_id=$row['write_off_id'];
			$patient_id=$row['patient_id'];
			$encounter_id=$row['encounter_id'];
			$charge_list_detail_id=$row['charge_list_detail_id'];
			
			$trans_by='Patient';
			if($row['write_off_by_id']>0){
				$trans_by='Insurance';
			}
			
			$trans_type="Write Off";
			if($row['paymentStatus']!=""){
				$trans_type=$row['paymentStatus'];
			}
			$trans_ins_id=$row['write_off_by_id'];
			$trans_amount=$row['write_off_amount'];
			$trans_dop=$row['write_off_date'];
			$trans_dot=exp_date_time($row['entered_date'],'date');
			$trans_dot_time=exp_date_time($row['entered_date'],'time');
			$trans_operator_id=$row['write_off_operator_id'];
			$trans_code_id=$row['write_off_code_id'];
			$batch_id=$row['batch_id'];
			$cap_main_id=$row['cap_main_id'];
			$era_amt=$row['era_amt'];
			$cas_type=$row['CAS_type'];
			$cas_code=$row['CAS_code'];
			$trans_del_date=$row['write_off_del_date'];
			$trans_del_time=$row['write_off_del_time'];
			$trans_del_operator_id=$row['del_operator_id'];
			$facility_id=$row['facility_id'];
			$trans_qry_type="insert";
			if($row['modified_by']>0){
				$trans_qry_type="update";
			}
			
			$charge_list_id=0;
			if($charge_list_detail_id>0){
				$chl_qry=imw_query("select charge_list_id from patient_charge_list_details where charge_list_detail_id='$charge_list_detail_id'");
				$chl_row=imw_fetch_array($chl_qry);
				$charge_list_id=$chl_row['charge_list_id'];
			}
			
			if($rp_row_trans_arr[$master_tbl_id][$trans_type]['report_trans_id']>0){
				$parent_id=$rp_row_trans_arr[$master_tbl_id][$trans_type]['report_trans_id'];
			}else{
				$parent_id=0;
			}
			
			$ins_qry.="('$parent_id','$master_tbl_id','$patient_id','$encounter_id','$charge_list_id','$charge_list_detail_id','$trans_by',
			'$trans_ins_id','$trans_method','$check_number','$cc_type','$cc_number','$cc_exp_date','$trans_type','$trans_amount','',
			'$trans_code_id','$batch_id','$cap_main_id','$trans_dot','$trans_dot_time','$trans_dop','$trans_dop_time','$trans_operator_id',
			'$era_amt','$cas_type','$cas_code','$trans_qry_type','$facility_id','$trans_source',";
			if($trans_del_operator_id>0 && $parent_id==0){
				$ins_qry.="'','',''),";
			}else{
				$ins_qry.="'$trans_del_date','$trans_del_time','$trans_del_operator_id'),";
			}
			
			if($trans_del_operator_id>0 && $parent_id==0){
				$trans_qry_type="update";
				$ins_qry.="('$del_parent_id','$master_tbl_id','$patient_id','$encounter_id','$charge_list_id','$charge_list_detail_id','$trans_by',
				'$trans_ins_id','$trans_method','$check_number','$cc_type','$cc_number','$cc_exp_date','$trans_type','$trans_amount','',
				'$trans_code_id','$batch_id','$cap_main_id','$trans_dot','$trans_dot_time','$trans_dop','$trans_dop_time','$trans_operator_id',
				'$era_amt','$cas_type','$cas_code','$trans_qry_type','$facility_id','$trans_source','$trans_del_date','$trans_del_time','$trans_del_operator_id'),";
			}
		}
		$ins_qry_run=substr($ins_qry,0,-1).';';
		imw_query($ins_qry_run);
		$master_tbl_id_imp=implode("','",$master_tbl_id_arr);
		imw_query("update paymentswriteoff set report_date_timestamp='',date_timestamp=date_timestamp where write_off_id in('$master_tbl_id_imp')");			
		$ins_qry="";
		if($cron_job!=""){
			$start_table="patient_charges_detail_payment_info";
		}
	
	}else{
		$start_table="patient_charges_detail_payment_info";
		$start_val=$show_start_val=0;
	}
}
if($start_table=="patient_charges_detail_payment_info"){
	$ins_qry=$ins_qry_trans;
	$row_arr=$row_ap_arr=$rp_row_trans_arr=$master_tbl_id_arr=array();
	$acc_qry=imw_query("SELECT pcdpi.*,pcpi.encounter_id,pcpi.payment_mode,pcpi.checkNo,pcpi.creditCardCo,pcpi.creditCardNo,pcpi.expirationDate,
				pcpi.insCompany,pcpi.paymentClaims,pcpi.insProviderId,pcpi.transaction_date,pcpi.facility_id
				FROM  patient_chargesheet_payment_info as pcpi JOIN patient_charges_detail_payment_info as pcdpi 
				on pcpi.payment_id=pcdpi.payment_id where pcdpi.report_date_timestamp!='0000-00-00 00:00:00' order by pcdpi.payment_details_id asc limit $start_val , $end");	
	if(imw_num_rows($acc_qry)>0){
		while($acc_row=imw_fetch_array($acc_qry)){
			$row_arr[$acc_row['payment_details_id']]=$acc_row;
			$row_ap_arr[$acc_row['payment_details_id']]=$acc_row['payment_details_id'];
		}
		
		$row_ap_imp=implode("','",$row_ap_arr);
		$rp_acc_trans_qry=imw_query("select report_trans_id,master_tbl_id,trans_type from report_enc_trans where master_tbl_id in('$row_ap_imp') and trans_type in('copay','Deposit','Interest Payment','Negative Payment','paid') order by report_trans_id asc");
		while($rp_acc_trans_row=imw_fetch_array($rp_acc_trans_qry)){
			$rp_row_trans_arr[$rp_acc_trans_row['master_tbl_id']][$rp_acc_trans_row['trans_type']]=$rp_acc_trans_row;
		}
		
		foreach($row_arr as $row_key=>$row_val){
			$row=$row_arr[$row_key];					
			$master_tbl_id=$row['payment_details_id'];
			$master_tbl_id_arr[]=$master_tbl_id;
			$encounter_id=$row['encounter_id'];
			$charge_list_detail_id=$row['charge_list_detail_id'];
			$trans_by=$row['paidBy'];
			$trans_ins_id=$row['insProviderId'];
			$trans_amount=$row['paidForProc']+$row['overPayment'];
			$trans_dop=$row['paidDate'];
			$trans_dop_time=$row['paid_time'];
			$trans_dot=exp_date_time($row['transaction_date'],'date');
			$trans_dot_time='';
			$operator_id=$row['operator_id'];
			$trans_method=$row['payment_mode'];
			$check_number=$row['checkNo'];
			$cc_type=$row['creditCardCo'];
			$cc_number=$row['creditCardNo'];
			$cc_exp_date=$row['expirationDate'];
			$trans_type=$row['paymentClaims'];
			$cas_type=$row['CAS_type'];
			$cas_code=$row['CAS_code'];
			$batch_id=$row['batch_id'];
			$trans_del_date=$row['deleteDate'];
			$trans_del_time=$row['deleteTime'];
			$trans_del_operator_id=$row['del_operator_id'];
			$facility_id=$row['facility_id'];
			
			$trans_qry_type="insert";
			if($row['modified_by']>0){
				$trans_qry_type="update";
			}
			
			$charge_list_id=0;
			$patient_id=0;
			if($encounter_id>0){
				$chl_qry=imw_query("select charge_list_id,patient_id from patient_charge_list where encounter_id='$encounter_id'");
				$chl_row=imw_fetch_array($chl_qry);
				$charge_list_id=$chl_row['charge_list_id'];
				$patient_id=$chl_row['patient_id'];
				if($charge_list_detail_id==0){
					$chld_qry=imw_query("select charge_list_detail_id from patient_charge_list_details where charge_list_id='$charge_list_id' and coPayAdjustedAmount='1'");
					$chld_row=imw_fetch_array($chld_qry);
					$charge_list_detail_id=$chld_row['charge_list_detail_id'];
					$trans_type="copay-".$trans_type;
				}
			}
			
			if($charge_list_detail_id==0){
				$copay_chld_qry=imw_query("select charge_list_detail_id from patient_charge_list_details where charge_list_id='$charge_list_id' limit 0,1");
				$copay_chld_row=imw_fetch_array($copay_chld_qry);
				$charge_list_detail_id=$copay_chld_row['charge_list_detail_id'];
			}
			if($rp_row_trans_arr[$master_tbl_id][$trans_type]['report_trans_id']>0){
				$parent_id=$rp_row_trans_arr[$master_tbl_id][$trans_type]['report_trans_id'];
			}else{
				$parent_id=0;
			}
			
			$ins_qry.="('$parent_id','$master_tbl_id','$patient_id','$encounter_id','$charge_list_id','$charge_list_detail_id','$trans_by',
			'$trans_ins_id','$trans_method','$check_number','$cc_type','$cc_number','$cc_exp_date','$trans_type','$trans_amount','',
			'$trans_code_id','$batch_id','$cap_main_id','$trans_dot','$trans_dot_time','$trans_dop','$trans_dop_time','$operator_id',
			'$era_amt','$cas_type','$cas_code','$trans_qry_type','$facility_id','$trans_source',";
			
			if($trans_del_operator_id>0 && $parent_id==0){
				$ins_qry.="'','',''),";
			}else{
				$ins_qry.="'$trans_del_date','$trans_del_time','$trans_del_operator_id'),";
			}
			
			if($trans_del_operator_id>0 && $parent_id==0){
				$trans_qry_type="update";
				$ins_qry.="('$del_parent_id','$master_tbl_id','$patient_id','$encounter_id','$charge_list_id','$charge_list_detail_id','$trans_by',
				'$trans_ins_id','$trans_method','$check_number','$cc_type','$cc_number','$cc_exp_date','$trans_type','$trans_amount','',
				'$trans_code_id','$batch_id','$cap_main_id','$trans_dot','$trans_dot_time','$trans_dop','$trans_dop_time','$operator_id',
				'$era_amt','$cas_type','$cas_code','$trans_qry_type','$facility_id','$trans_source','$trans_del_date','$trans_del_time','$trans_del_operator_id'),";
			}
		}
		$ins_qry_run=substr($ins_qry,0,-1).';';
		imw_query($ins_qry_run);
		$master_tbl_id_imp=implode("','",$master_tbl_id_arr);
		imw_query("update patient_charges_detail_payment_info set report_date_timestamp='',date_timestamp=date_timestamp where payment_details_id in('$master_tbl_id_imp')");
		$ins_qry="";
		if($cron_job!=""){
			$start_table="creditapplied";
		}
		
	}else{
		$start_table="creditapplied";
		$start_val=$show_start_val=0;
	}
}
if($start_table=="creditapplied"){
	$ins_qry=$ins_qry_trans;
	$row_arr=$row_ap_arr=$rp_row_trans_arr=$master_tbl_id_arr=array();
	$acc_qry=imw_query("SELECT * FROM  creditapplied  where report_date_timestamp!='0000-00-00 00:00:00' order by crAppId asc limit $start_val , $end");	
	if(imw_num_rows($acc_qry)>0){	
		while($acc_row=imw_fetch_array($acc_qry)){
			$row_arr[$acc_row['crAppId']]=$acc_row;
			$row_ap_arr[$acc_row['crAppId']]=$acc_row['crAppId'];
		}
		
		$row_ap_imp=implode("','",$row_ap_arr);
		$rp_acc_trans_qry=imw_query("select report_trans_id,master_tbl_id,trans_type from report_enc_trans where master_tbl_id in('$row_ap_imp') and trans_type in('refund','credit','debit') order by report_trans_id asc");
		while($rp_acc_trans_row=imw_fetch_array($rp_acc_trans_qry)){
			$rp_row_trans_arr[$rp_acc_trans_row['master_tbl_id']][$rp_acc_trans_row['trans_type']]=$rp_acc_trans_row;
		}
		
		foreach($row_arr as $row_key=>$row_val){
			$row=$row_arr[$row_key];
			$master_tbl_id=$row['crAppId'];
			$master_tbl_id_arr[]=$master_tbl_id;
			$patient_id=$row['patient_id'];
			$encounter_id=$row['crAppliedToEncId'];
			$charge_list_detail_id=$row['charge_list_detail_id'];
			$trans_by=$row['type'];
			$trans_ins_id=$row['insCompany'];
			$trans_amount=$row['amountApplied'];
			$trans_dop=$row['dateApplied'];
			$trans_dot=exp_date_time($row['entered_date'],'date');
			$trans_dot_time=exp_date_time($row['entered_date'],'time');
			$operator_id=$row['operatorApplied'];
			$trans_method=$row['payment_mode'];
			$check_number=$row['checkCcNumber'];
			$cc_type=$row['creditCardCo'];
			$cc_number=$row['creditCardNo'];
			$cc_exp_date=$row['expirationDateCc'];
			$cas_type=$row['CAS_type'];
			$cas_code=$row['CAS_code'];
			$batch_id=$row['batch_id'];
			if(strtolower($row['crAppliedTo'])=='payment'){
				$trans_type="refund";
			}else{
				$trans_type="debit";
			}
			
			$patient_id_adjust=$row['patient_id_adjust'];
			$encounter_id_adjust=$row['crAppliedToEncId_adjust'];
			$charge_list_detail_id_adjust=$row['charge_list_detail_id_adjust'];
			$trans_del_date=exp_date_time($row['del_date_time'],'date');
			$trans_del_time=exp_date_time($row['del_date_time'],'time');
			$trans_del_operator_id=$row['del_operator_id'];
			$facility_id=$row['facility_id'];
			
			$trans_qry_type="insert";
			if($row['modified_by']>0){
				$trans_qry_type="update";
			}
			
			$charge_list_id=0;
			if($encounter_id>0){
				$chl_qry=imw_query("select charge_list_id from patient_charge_list where encounter_id='$encounter_id'");
				$chl_row=imw_fetch_array($chl_qry);
				$charge_list_id=$chl_row['charge_list_id'];
			}
			
			$charge_list_id_adjust=0;
			if($trans_type=="debit"){
				if($encounter_id_adjust>0){
					$chl_qry=imw_query("select charge_list_id from patient_charge_list where encounter_id='$encounter_id_adjust'");
					$chl_row=imw_fetch_array($chl_qry);
					$charge_list_id_adjust=$chl_row['charge_list_id'];
				}
			}	
			
			if($rp_row_trans_arr[$master_tbl_id][$trans_type]['report_trans_id']>0){
				$parent_id=$rp_row_trans_arr[$master_tbl_id][$trans_type]['report_trans_id'];
			}else{
				$parent_id=0;
			}
			
			$ins_qry.="('$parent_id','$master_tbl_id','$patient_id','$encounter_id','$charge_list_id','$charge_list_detail_id','$trans_by',
			'$trans_ins_id','$trans_method','$check_number','$cc_type','$cc_number','$cc_exp_date','$trans_type','$trans_amount','',
			'$trans_code_id','$batch_id','$cap_main_id','$trans_dot','$trans_dot_time','$trans_dop','$trans_dop_time','$operator_id',
			'$era_amt','$cas_type','$cas_code','$trans_qry_type','$facility_id','$trans_source',";
			
			if($trans_del_operator_id>0 && $parent_id==0){
				$ins_qry.="'','',''),";
			}else{
				$ins_qry.="'$trans_del_date','$trans_del_time','$trans_del_operator_id'),";
			}
			
			if($trans_del_operator_id>0 && $parent_id==0){
				$trans_qry_type="update";
				$ins_qry.="('$del_parent_id','$master_tbl_id','$patient_id','$encounter_id','$charge_list_id','$charge_list_detail_id','$trans_by',
				'$trans_ins_id','$trans_method','$check_number','$cc_type','$cc_number','$cc_exp_date','$trans_type','$trans_amount','',
				'$trans_code_id','$batch_id','$cap_main_id','$trans_dot','$trans_dot_time','$trans_dop','$trans_dop_time','$operator_id',
				'$era_amt','$cas_type','$cas_code','$trans_qry_type','$facility_id','$trans_source','$trans_del_date','$trans_del_time','$trans_del_operator_id'),";
			}
			
			
			if($trans_type=="debit"){
				
				if($rp_row_trans_arr[$master_tbl_id]['credit']['report_trans_id']>0){
					$parent_id=$rp_row_trans_arr[$master_tbl_id]['credit']['report_trans_id'];
				}else{
					$parent_id=0;
				}
				
				$ins_qry.="('$parent_id','$master_tbl_id','$patient_id_adjust','$encounter_id_adjust','$charge_list_id_adjust','$charge_list_detail_id_adjust','$trans_by',
				'$trans_ins_id','$trans_method','$check_number','$cc_type','$cc_number','$cc_exp_date','credit','$trans_amount','',
				'$trans_code_id','$batch_id','$cap_main_id','$trans_dot','$trans_dot_time','$trans_dop','$trans_dop_time','$operator_id',
				'$era_amt','$cas_type','$cas_code','$trans_qry_type','$facility_id','$trans_source',";
				
				if($trans_del_operator_id>0 && $parent_id==0){
					$ins_qry.="'','',''),";
				}else{
					$ins_qry.="'$trans_del_date','$trans_del_time','$trans_del_operator_id'),";
				}
				
				if($trans_del_operator_id>0 && $parent_id==0){
					$trans_qry_type="update";
					$ins_qry.="('$del_parent_id','$master_tbl_id','$patient_id_adjust','$encounter_id_adjust','$charge_list_id_adjust','$charge_list_detail_id_adjust','$trans_by',
					'$trans_ins_id','$trans_method','$check_number','$cc_type','$cc_number','$cc_exp_date','credit','$trans_amount','',
					'$trans_code_id','$batch_id','$cap_main_id','$trans_dot','$trans_dot_time','$trans_dop','$trans_dop_time','$operator_id',
					'$era_amt','$cas_type','$cas_code','$trans_qry_type','$facility_id','$trans_source','$trans_del_date','$trans_del_time','$trans_del_operator_id'),";
				}
			}
			
		}
		$ins_qry_run=substr($ins_qry,0,-1).';';
		imw_query($ins_qry_run);
		$master_tbl_id_imp=implode("','",$master_tbl_id_arr);
		imw_query("update creditapplied set report_date_timestamp='',date_timestamp=date_timestamp where crAppId in('$master_tbl_id_imp')");
		$ins_qry="";
		if($cron_job!=""){
			$start_table="defaultwriteoff";
		}
		
	}else{
		$start_table="defaultwriteoff";
		$start_val=$show_start_val=0;
	}
}
if($start_table=="defaultwriteoff"){
	$ins_qry=$ins_qry_trans;
	$row_arr=$row_ap_arr=$rp_row_trans_arr=$master_tbl_id_arr=array();
	$acc_qry=imw_query("SELECT * FROM defaultwriteoff where report_date_timestamp!='0000-00-00 00:00:00' order by write_off_id asc limit $start_val , $end");	
	if(imw_num_rows($acc_qry)>0){	
		while($row=imw_fetch_array($acc_qry)){
			$master_tbl_id_arr[]=$row['write_off_id'];
			$master_tbl_id=$row['write_off_id'];
			$patient_id=$row['patient_id'];
			$encounter_id=$row['encounter_id'];
			$charge_list_id=$row['charge_list_id'];
			$charge_list_detail_id=$row['charge_list_detail_id'];
			
			$trans_by='Patient';
			if($row['write_off_by']>0){
				$trans_by='Insurance';
			}
			$trans_ins_id=$row['write_off_by'];
			$trans_amount=$row['write_off_amount'];
			$trans_code_id=$row['write_off_code_id'];
			
			$trans_dot=exp_date_time($row['write_off_dot'],'date');
			$trans_dot_time=exp_date_time($row['write_off_dot'],'time');
			$trans_dop=$row['write_off_dop'];
			$operator_id=$row['write_off_operator_id'];
			$batch_id=$row['batch_id'];
			$trans_type="default_writeoff";
			
			$trans_del_date=$row['del_date'];
			$trans_del_time=$row['del_time'];
			$trans_del_operator_id=$row['del_operator_id'];
			$facility_id=$row['facility_id'];
			
			$trans_qry_type="insert";
			
			$ins_qry.="('$parent_id','$master_tbl_id','$patient_id','$encounter_id','$charge_list_id','$charge_list_detail_id','$trans_by',
			'$trans_ins_id','$trans_method','$check_number','$cc_type','$cc_number','$cc_exp_date','$trans_type','$trans_amount','',
			'$trans_code_id','$batch_id','$cap_main_id','$trans_dot','$trans_dot_time','$trans_dop','$trans_dop_time','$operator_id',
			'$era_amt','$cas_type','$cas_code','$trans_qry_type','$facility_id','$trans_source','$trans_del_date','$trans_del_time','$trans_del_operator_id'),";
		}
		$ins_qry_run=substr($ins_qry,0,-1).';';
		imw_query($ins_qry_run);
		$master_tbl_id_imp=implode("','",$master_tbl_id_arr);
		imw_query("update defaultwriteoff set report_date_timestamp='' where write_off_id in('$master_tbl_id_imp')");
		$ins_qry="";
		if($cron_job!=""){
			$start_table="setparentid";
		}
		
	}else{
		$start_table="setparentid";
		$start_val=$show_start_val=0;
	}
}
if($start_table=="setparentid"){
	$row_arr=$row_ap_arr=$rp_row_trans_arr=$master_tbl_id_arr=array();
	$acc_qry=imw_query("select report_trans_id,master_tbl_id,parent_id,trans_type FROM report_enc_trans where trans_del_operator_id>0 and parent_id='0' and trans_type!='default_writeoff' order by report_trans_id asc limit $start_val , $end");	
	if(imw_num_rows($acc_qry)>0){	
		while($acc_row=imw_fetch_array($acc_qry)){
			$row_arr[$acc_row['report_trans_id']]=$acc_row;
			$row_ap_arr[$acc_row['master_tbl_id']]=$acc_row['master_tbl_id'];
		}
		
		$row_ap_imp=implode("','",$row_ap_arr);
		$rp_acc_trans_qry=imw_query("select report_trans_id,master_tbl_id,trans_type from report_enc_trans where master_tbl_id in('$row_ap_imp') and trans_del_operator_id='0' order by report_trans_id asc");
		while($rp_acc_trans_row=imw_fetch_array($rp_acc_trans_qry)){
			$rp_row_trans_arr[$rp_acc_trans_row['master_tbl_id']][$rp_acc_trans_row['trans_type']]=$rp_acc_trans_row;
		}	
		
		foreach($row_arr as $row_key=>$row_val){
			$row=$row_arr[$row_key];
			$up_report_trans_id=$row['report_trans_id'];
			$up_master_tbl_id=$row['master_tbl_id'];
			$up_parent_id=$row['parent_id'];
			$up_trans_type=$row['trans_type'];
			if($rp_row_trans_arr[$up_master_tbl_id][$up_trans_type]['report_trans_id']>0){
				$parent_id=$rp_row_trans_arr[$up_master_tbl_id][$up_trans_type]['report_trans_id'];
			}else{
				$parent_id=0;
			}
			imw_query("update report_enc_trans set parent_id='$parent_id' where report_trans_id='$up_report_trans_id' and parent_id='0'");
		}
		
		if($cron_job!=""){
			$start_table="";
		}
	}else{
		$start_table="";
		$start_val=$show_start_val=0;
	}
}
if($start_table=="" && $pol_msg_info==""){
	imw_query("update copay_policies set report_closed_day='$curr_dt',rcd_finished=''");

	$del_rp_acc_trans_qry=imw_query("SELECT report_trans_id FROM `report_enc_trans` WHERE parent_id = '0' GROUP BY trans_type, master_tbl_id, trans_del_operator_id HAVING count( report_trans_id ) >1");
	if(imw_num_rows($del_rp_acc_trans_qry)>0){
		while($del_rp_acc_trans_row=imw_fetch_array($del_rp_acc_trans_qry)){
			$del_rp_acc_trans_arr[$del_rp_acc_trans_row['report_trans_id']]=$del_rp_acc_trans_row['report_trans_id'];
		}
		$del_rp_acc_trans_imp=implode("','",$del_rp_acc_trans_arr);
		imw_query("DELETE FROM `report_enc_trans` WHERE report_trans_id in ('".$del_rp_acc_trans_imp."')");
	}
} 
if($cron_job==""){
	//pre($_REQUEST['start_table']);
	$msg_info = "";
	$close_div = false;
	
	if($start_table=="patient_charge_list_details"){
		$msg_info="Level 1/7 - Charges transactions are being processed. 0 - ".$_REQUEST['show_start_val'];
	}else if($start_table=="account_payments"){
		$msg_info="Level 2/7 - Adjustment, Over Adjustment and Returned Check transactions are being processed. 0 - ".$_REQUEST['show_start_val'];
	}else if($start_table=="paymentswriteoff"){
		$msg_info="Level 3/7 - Write Off and Discount transactions are being processed. 0 - ".$_REQUEST['show_start_val'];
	}else if($start_table=="patient_charges_detail_payment_info"){
		$msg_info="Level 4/7 - Paid, Negative Payment, Interest Payment and Deposit transactions are being processed. 0 - ".$_REQUEST['show_start_val'];
	}else if($start_table=="creditapplied"){
		$msg_info="Level 5/7 - Refund and Credit/Debit transactions are being processed. 0 - ".$_REQUEST['show_start_val'];
	}else if($start_table=="defaultwriteoff"){
		$msg_info="Level 6/7 - Default Write-off transactions are being processed. 0 - ".$_REQUEST['show_start_val'];
	}else if($start_table=="setparentid"){
		$msg_info="Level 7/7 - Final Data merge is being processed. 0 - ".$_REQUEST['show_start_val'];
	}else{
		$msg_info="All transactions processed successfully.";
		$close_div = true;
	}
	if($pol_msg_info!=""){
		$msg_info=$pol_msg_info;
	}
?>
	<form action="" method="get" name="submit_frm" id="submit_frm">
		<input type="hidden" name="start_table" value="<?php echo $start_table; ?>">
		<input type="hidden" name="start_val" value="<?php print $start_val + $end; ?>">
		<input type="hidden" name="show_start_val" value="<?php print $_REQUEST['show_start_val'] + $end; ?>">
        <input type="hidden" name="callfrom" value="<?php print $_REQUEST['callfrom']; ?>">
	</form>
	<script>
		var callfrom='<?php echo $_REQUEST['callfrom'];?>';
		top.fmain.$('#report_create_div .modal-body .alert-info').html('<?php echo $msg_info; ?>');
		<?php
		if($close_div){ ?>
			if(callfrom=='reports'){
				top.fmain.$('#report_create_div').addClass('hide');
				top.show_loading_image('show');
				top.fmain.frm_reports.submit();
			}else{
				top.fmain.$('#report_create_div .modal-body #div_loading_image').addClass('hide');
			}
		<?php		
			}
		?>
	</script>
<?php
	if($start_table!=""){
?>
		<script type="text/javascript">
            document.submit_frm.submit();
        </script>
<?php
	}
}
?>