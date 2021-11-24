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
$title = "CI/CO Prepayments";
require_once("acc_header.php");
?>
<?php
$manuall_time = date('h:i A');
if($_POST['post_action']=="del"){
	if($_REQUEST['chk_in_out_arr']){
		//----------------- Delete CI/CO Payment -----------------//
		foreach($_REQUEST['chk_in_out_arr'] as $key => $del_detail_id){
			if($del_detail_id>0){
				$del_pay_id=$cio_pay_id_arr[$del_detail_id];
				imw_query("update check_in_out_payment_details set status='1', delete_date='$curr_date', delete_time='$curr_time', delete_operator_id='$operator_id' where id='$del_detail_id'");
				imw_query("update check_in_out_payment_post set status = '1' where check_in_out_payment_detail_id='$del_detail_id'");
				imw_query("update ci_pmt_ref set del_status = '1',del_by='$operator_id',del_date='$curr_date',del_time='$curr_time' where ci_co_id = '$del_detail_id' and patient_id='$patient_id' and del_status='0'");
				
				$chk_qry=imw_query("select sum(item_charges) as item_charges_sum,sum(item_payment) as item_payment_sum from check_in_out_payment_details where payment_id='$del_pay_id' and status='0'");
				$chk_row=imw_fetch_array($chk_qry);
				$item_charges_sum=$chk_row['item_charges_sum'];
				$item_payment_sum=$chk_row['item_payment_sum'];
				
				imw_query("update check_in_out_payment set total_charges='$item_charges_sum',total_payment='$item_payment_sum' where payment_id='$del_pay_id'");
			}
		}
	}
	
	if($_REQUEST['pt_pre_payment_chk']){
		//----------------- Delete Patient Pre Payment -----------------//
		foreach($_REQUEST['pt_pre_payment_chk'] as $key => $del_pre_pay_id){
			if($del_pre_pay_id>0){
				imw_query("update patient_pre_payment set del_status = '1',del_operator_id='$operator_id',trans_del_date='$curr_date_time' where id = '$del_pre_pay_id' and patient_id='$patient_id'");
				imw_query("update ci_pmt_ref set del_status = '1',del_by='$operator_id',del_date='$curr_date',del_time='$curr_time' where pmt_id = '$del_pre_pay_id' and patient_id='$patient_id'");
			}
		}
	}
}
if($_POST['post_action']=="manual"){
	//----------------- Insert CI/CO Manual Payment -----------------//
	foreach($_REQUEST['chk_in_out_arr'] as $key => $pay_detail_id){
		if($pay_detail_id>0){
			$pay_amt=$chk_in_out_pay_arr[$pay_detail_id];
			$cio_pay_id=$cio_pay_id_arr[$pay_detail_id];
			imw_query("insert into check_in_out_payment_post set manually_payment='$pay_amt',patient_id='$patient_id',check_in_out_payment_id='$cio_pay_id',
			check_in_out_payment_detail_id='$pay_detail_id',manually_date='$curr_date',manually_time='$manuall_time',manually_by='$operator_id'");
		}
	}
	
	//----------------- Insert Patient Pre Payment Manual Payment -----------------//
	foreach($_REQUEST['pt_pre_payment_chk'] as $key => $pt_pre_payment_id){
		if($pt_pre_payment_id>0){
			$pt_pre_pending_amt=$_POST['pt_pre_pending_amt_chk'][$pt_pre_payment_id];
			imw_query("update patient_pre_payment set apply_payment_type='manually',apply_amount='$pt_pre_pending_amt',apply_payment_by='$operator_id',
			apply_payment_date='$curr_date',apply_payment_time='$curr_time' where id='$pt_pre_payment_id'");
		}
	}
}
?>
<?php
if($_REQUEST['del_pcpi_pay_id']>0){
	//----------------- Unapply CI/CO Payment -----------------//
	$del_pcpi_pay_id=$_REQUEST['del_pcpi_pay_id'];
	$trans_del_date=date('Y-m-d');
	$trans_del_time=date('H:i:s');
	$trans_date_time=date('Y-m-d H:i:s');
	$operator_id = $_SESSION['authId'];	
	if($_REQUEST['del_by']=='ci'){
		imw_query("update check_in_out_payment_post set status = '1' where acc_payment_id='$del_pcpi_pay_id'");
	}
	imw_query("update patient_chargesheet_payment_info set markPaymentDelete='1',unapply='1',unapply_by='$operator_id',unapply_date='$trans_date_time',unapply_type='".$_REQUEST['del_by']."' where payment_id='$del_pcpi_pay_id'");
	imw_query("update patient_charges_detail_payment_info set deletePayment='1',deleteDate='$trans_del_date',deleteTime='$trans_del_time',del_operator_id='$operator_id',unapply='1',unapply_by='$operator_id',unapply_date='$trans_date_time',unapply_type='".$_REQUEST['del_by']."' where payment_id='$del_pcpi_pay_id'");		
	
	$pcpi_qry=imw_query("select * from patient_chargesheet_payment_info where payment_id='$del_pcpi_pay_id'");
	$pcpi_row=imw_fetch_array($pcpi_qry);
	/*imw_query("insert into patient_chargesheet_payment_info set encounter_id='".$pcpi_row['encounter_id']."',paid_by='".$pcpi_row['paid_by']."',
	payment_amount='".$pcpi_row['payment_amount']."',payment_mode='".$pcpi_row['payment_mode']."',checkNo='".$pcpi_row['checkNo']."',
	creditCardNo='".$pcpi_row['creditCardNo']."',creditCardCo='".$pcpi_row['creditCardCo']."',date_of_payment='".$pcpi_row['date_of_payment']."',
	expirationDate='".$pcpi_row['expirationDate']."',operatorId='".$operator_id."',insProviderId='".$pcpi_row['insProviderId']."',
	insCompany='".$pcpi_row['insCompany']."',paymentClaims='Negative Payment',transaction_date='".$trans_del_date."',unapply_type='".$_REQUEST['del_by']."'");
	$pcpi_ins=imw_insert_id();*/
	
	$pcpid_qry=imw_query("select * from patient_charges_detail_payment_info where payment_id='$del_pcpi_pay_id'");
	$pcpid_row=imw_fetch_array($pcpid_qry);
	/*imw_query("insert into patient_charges_detail_payment_info set payment_id='".$pcpi_ins."',charge_list_detail_id='".$pcpid_row['charge_list_detail_id']."',
	paidBy='".$pcpid_row['paidBy']."',paidDate='".$pcpid_row['paidDate']."',paidForProc='".$pcpid_row['paidForProc']."',
	operator_id='".$operator_id."',entered_date='".$trans_del_date."',unapply_type='".$_REQUEST['del_by']."'");*/
	
	if($pcpid_row['charge_list_detail_id']==0){
		$get_copay_chld_qry = imw_query("SELECT charge_list_detail_id FROM patient_charge_list join patient_charge_list_details 
		on patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id
		WHERE patient_charge_list_details.del_status='0' and patient_charge_list.encounter_id='".$pcpi_row['encounter_id']."'
		and patient_charge_list_details.coPayAdjustedAmount='1'");
		$get_copay_chld_row = imw_fetch_array($get_copay_chld_qry);
		$copay_charge_list_detail_id = $get_copay_chld_row['charge_list_detail_id'];
		imw_query("UPDATE patient_charge_list SET copayPaid='0',coPayPaidDate='0000-00-00',coPayAdjusted='',coPayAdjustedDate='' WHERE encounter_id='".$pcpi_row['encounter_id']."'");
		imw_query("UPDATE patient_charge_list_details SET coPayAdjustedAmount = '0' WHERE charge_list_detail_id = '$copay_charge_list_detail_id'");
	}
	
	$encounter_id=$pcpi_row['encounter_id'];
	set_payment_trans($encounter_id);
}
if($_REQUEST['del_pcpi_pay_id']>0 || $_POST['post_action']!=""){
?>
	<script language="javascript">
        location.href="check_in_out_acc.php?Check_inout_chk=Pre_payments";
    </script>
<?php } ?>
<div id="pending_payment_div_content" class="div_popcontent" style="display:none">
	<table class="table table-bordered table-hover table-striped">
		<tr>
			<td id="click_rec" class="purple_bar" colspan="5" style="text-align:left;">
			</td>
			<form action="#" name="frm_hidden"  method="post">
				<input type="hidden" name="click_payment_id" id="click_payment_id" value="">
				<input type="hidden" name="click_payment_detail_id" id="click_payment_detail_id" value="">
				<input type="hidden" name="click_payment_type_id" id="click_payment_type_id" value="">
			</form>
		</tr>
		<tr class="grythead">
			<th>
				DOS
			</th>
			<th>
				Cpt Code
			</th>
            <th>
				Pat Balance
			</th>
            <th>
				Ins Balance
			</th>
			<th>
				Total Balance
			</th>
		</tr>
		
		<?php
		  $sel_qry="select patient_charge_list.date_of_service,
					cpt_fee_tbl.cpt_prac_code,
					patient_charge_list_details.pat_due,
					patient_charge_list_details.newBalance,
					patient_charge_list_details.charge_list_detail_id,
					patient_charge_list.encounter_id,
					patient_charge_list.copay,
					patient_charge_list.copayPaid,
					patient_charge_list.charge_list_id
						from 
					patient_charge_list join patient_charge_list_details on
					patient_charge_list.charge_list_id = patient_charge_list_details.charge_list_id 
					join cpt_fee_tbl on
					cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode 
						where 
					patient_charge_list_details.del_status='0'
					and patient_charge_list_details.newBalance>0
					and patient_charge_list_details.patient_id='$patient_id'
					order by patient_charge_list.date_of_service desc,
					patient_charge_list.encounter_id,
					cpt_fee_tbl.cpt_prac_code
					";
		  $run_qry=imw_query($sel_qry);	
		  while($fet_rec=imw_fetch_array($run_qry)){	
		  $copay_show[$fet_rec['charge_list_id']][]=$fet_rec;
		?>
		<tr class="text-center">
			<td nowrap>
				<a href="javascript:void(0);" onClick="process_payment('<?php echo $fet_rec['charge_list_detail_id']; ?>','<?php echo $fet_rec['encounter_id']; ?>','<?php echo $fet_rec['newBalance']; ?>','');">
					<?php 
						echo get_date_format($fet_rec['date_of_service']);
					?>
				</a>
			</td>
			<td>
				<a href="javascript:void(0);" onClick="process_payment('<?php echo $fet_rec['charge_list_detail_id']; ?>','<?php echo $fet_rec['encounter_id']; ?>','<?php echo $fet_rec['newBalance']; ?>','');">
					<?php 
						echo $fet_rec['cpt_prac_code'];
					?>
				</a>
			</td>
            <td>
				<a href="javascript:void(0);" onClick="process_payment('<?php echo $fet_rec['charge_list_detail_id']; ?>','<?php echo $fet_rec['encounter_id']; ?>','<?php echo $fet_rec['pat_due']; ?>','');">
					<?php 
						echo '$'.number_format($fet_rec['pat_due'],2);
					?>
				</a>
			</td>
            <td>
				<a href="javascript:void(0);" onClick="process_payment('<?php echo $fet_rec['charge_list_detail_id']; ?>','<?php echo $fet_rec['encounter_id']; ?>','<?php echo $fet_rec['newBalance']-$fet_rec['pat_due']; ?>','');">
					<?php 
						echo '$'.number_format(($fet_rec['newBalance']-$fet_rec['pat_due']),2);
					?>
				</a>
			</td>
			<td>
				<a href="javascript:void(0);" onClick="process_payment('<?php echo $fet_rec['charge_list_detail_id']; ?>','<?php echo $fet_rec['encounter_id']; ?>','<?php echo $fet_rec['newBalance']; ?>','');">
					<?php 
						echo '$'.number_format($fet_rec['newBalance'],2);
					?>
				</a>
			</td>
		</tr>
		<?php } ?>
		<?php  
			$copay_chl_arr=array_keys($copay_show);
			for($j=0;$j<count($copay_chl_arr);$j++){
				$copay_chl_id=$copay_chl_arr[$j];
				$cpt_onetime=0;
				for($f=0;$f<count($copay_show[$copay_chl_id]);$f++){
					$copay_detail=$copay_show[$copay_chl_id][$f];
					if($copay_detail['copayPaid']==0 && $copay_detail['copay']>0 && $copay_detail['newBalance']>=$copay_detail['copay']){
						$charge_list_id = $copay_detail['charge_list_id']; 
						$encounter_id = $copay_detail['encounter_id']; 
						$proc_code_imp=get_proc_code($charge_list_id);
						$copay_collect_proc=copay_apply_chk($proc_code_imp,'','');
						$copay_collect=copay_apply_chk($copay_detail['cpt_prac_code'],'','');
						 if($cpt_onetime==0){
							if($copay_collect_proc==true && $copay_collect_proc[0]==true){
								if($copay_collect[0]==true && $copay_collect[0]==true){
									$cpt_onetime++;	
									$getproccodeQry = imw_query("SELECT sum(paidForProc) as tot_paidproc FROM 
													patient_chargesheet_payment_info a,
													patient_charges_detail_payment_info b
													WHERE a.encounter_id = '$encounter_id'
													AND a.payment_id = b.payment_id
													AND b.charge_list_detail_id = 0
													AND b.deletePayment=0
													and a.paymentClaims!='Negative Payment'
													ORDER BY a.payment_id DESC");
									$getproccodeRow = imw_fetch_array($getproccodeQry);
									$copay_amt_final=$copay_detail['copay']-$getproccodeRow['tot_paidproc'];
									
									$getproccodeQry = imw_query("SELECT sum(paidForProc) as tot_paidproc FROM 
													patient_chargesheet_payment_info a,
													patient_charges_detail_payment_info b
													WHERE a.encounter_id = '$encounter_id'
													AND a.payment_id = b.payment_id
													AND b.charge_list_detail_id = 0
													AND b.deletePayment=0
													and a.paymentClaims='Negative Payment'
													ORDER BY a.payment_id DESC");
									$getproccodeRow = imw_fetch_array($getproccodeQry);
									$copay_amt_final=$copay_amt_final+$getproccodeRow['tot_paidproc'];
						?>
									<tr class="text-center">
										<td nowrap>
											<a href="javascript:void(0);" onClick="process_payment('<?php echo $copay_detail['charge_list_detail_id']; ?>','<?php echo $copay_detail['encounter_id']; ?>','<?php echo $copay_amt_final; ?>','copay');">
												<?php 
													echo get_date_format($copay_detail['date_of_service']);
												?>
											</a>
										</td>
										<td>
											<a href="javascript:void(0);" onClick="process_payment('<?php echo $copay_detail['charge_list_detail_id']; ?>','<?php echo $copay_detail['encounter_id']; ?>','<?php echo $copay_amt_final; ?>','copay');">
												<?php 
													echo $copay_detail['cpt_prac_code'] .' - Copay';
												?>
											</a>
										</td>
                                        <td>
											<a href="javascript:void(0);" onClick="process_payment('<?php echo $copay_detail['charge_list_detail_id']; ?>','<?php echo $copay_detail['encounter_id']; ?>','<?php echo $copay_amt_final; ?>','copay');">
												<?php 
													echo '$'.number_format($copay_amt_final,2);
												?>
											</a>
										</td>
                                        <td>
											<a href="javascript:void(0);" onClick="process_payment('<?php echo $copay_detail['charge_list_detail_id']; ?>','<?php echo $copay_detail['encounter_id']; ?>','<?php echo $copay_amt_final; ?>','copay');">
												<?php 
													echo '$ 0.00';
												?>
											</a>
										</td>
										<td>
											<a href="javascript:void(0);" onClick="process_payment('<?php echo $copay_detail['charge_list_detail_id']; ?>','<?php echo $copay_detail['encounter_id']; ?>','<?php echo $copay_amt_final; ?>','copay');">
												<?php 
													echo '$'.number_format($copay_amt_final,2);
												?>
											</a>
										</td>
									</tr>
		<?php 
								}
							}
						}	
					} 
				}	
			}	
		?>
	</table>
	<table class="table" id="click_rec_comm_td">
		<tr>
			<td>
				<strong>Comment :</strong> <span id="click_rec_comm"></span>
			<td>
		</tr>
	</table>
</div>
<?php
$sess_height=$_SESSION['wn_height']-310;
$sess_height_div=$sess_height/2;
$sess_height_div=$sess_height_div-55;
$ptInfoOnHeader = core_get_patient_name($patient_id);
$sel_row=imw_query("select id,name from facility");
while($fet_row=imw_fetch_array($sel_row)){
	$facility_arr[$fet_row['id']]=$fet_row['name'];
}
?>
<div style="height:<?php echo $sess_height; ?>px; overflow:hidden;" class="table-responsive"> 
	<form name="manual_frm" action="check_in_out_acc.php" method="post">
    <input type="hidden" name="post_action" id="post_action" value="" />
    <input type="hidden" name="valueBtn" id="valueBtn" value="" />
		<table class="table table-hover table-striped" style="margin:0px;">
			<tr class="purple_bar">
				<td>CI/CO Payments</td>
				<td  class="col-sm-6"><?php echo $ptInfoOnHeader[4]; ?></td>
				<td class="col-sm-1">
					<select name="ci_del_status" onChange="document.manual_frm.submit();" class="selectpicker" data-width="100%">
						<option value="0" <?php if($ci_del_status!="1"){echo"selected";} ?>>Active</option>
						<option value="1" <?php if($ci_del_status=="1"){echo"selected";} ?>>All</option>
					</select>
				</td>
			</tr>
		</table>
		<div class="table-responsive" id="check_in_out_form" style="height:<?php echo $sess_height_div; ?>px; overflow-y:auto; overflow-x:hidden;">
			<table class="table table-bordered table-hover table-striped">
				<tr class="grythead">
					<th>Apply</th>
					<?php
					$get_prov=array();
					$getPhysicianNameStr="SELECT fname,lname,mname,id FROM users";
					$getPhysicianNameQry=imw_query($getPhysicianNameStr);
					while($getPhysicianNameRow=imw_fetch_array($getPhysicianNameQry)){
						$phy_arr['FIRST_NAME']=$getPhysicianNameRow['fname'];
						$phy_arr['LAST_NAME']=$getPhysicianNameRow['lname'];
						$phy_arr['MIDDLE_NAME']=$getPhysicianNameRow['mname'];
						$get_prov[$getPhysicianNameRow['id']]=changeNameFormat($phy_arr);
					}
					$item_ids_arr=array();
					$item_names_arr=array();
					$sel_row=imw_query("select * from check_in_out_fields");
					while($fet_row=imw_fetch_array($sel_row)){
						$item_ids_arr[]=$fet_row['id'];
						$item_names_arr[$fet_row['id']]=$fet_row['item_name'];
					} ?>
					<th>DOS</th>
					<th>Encounter</th>
					<th>Field Name/Proc Code</th>
					<th class="text-nowrap">Total/Balance</th>
					<th>Payment Method</th>
                    <th>Facility</th>
					<th>CC / Ch. <?php getHashOrNo();?></th>
					<th>Date Of Transaction</th>
				</tr>
				<?php
					$payment_id_arr=array();
					$total_payment_arr=array();
					$payment_method_arr=array();
					$payment_check_cc_arr=array();
					$payment_cc_date_arr=array();
					$created_on_arr=array();
					$created_by_arr=array();
					$modified_by_arr=array();
					$modified_on_arr=array();
					$payment_type=array();
					$data_str_arr=array();
					$ci_co_sch_arr=array();
					$del_whr=" and del_status='0'";
					if($ci_del_status>0){
						$del_whr="";
					}
					//----------------- CI/CO Payment Detail -----------------//
					$qry_payment_main=imw_query("select * from  check_in_out_payment 
										where patient_id='$patient_id' $del_whr
										order by payment_id desc");
					while($fet_payment_main=imw_fetch_array($qry_payment_main)){
                        $cicodata_str='';
                        if($fet_payment_main['log_referenceNumber']!=''){
                            $cicoSql='select id,scheduID,encounter_id,laneId,TransactionNumber,transactionAmount,is_api from tsys_possale_transaction where log_referenceNumber="'.$fet_payment_main['log_referenceNumber'].'" and patient_id="'.$patient_id.'" ';
                            $cicoRs=imw_query($cicoSql);
                            $cicoRow=imw_fetch_assoc($cicoRs);
                            $cicoTransactionNumber=$cicoRow['TransactionNumber'];
                            $cicolaneID=$cicoRow['laneId'];
                            $cicoscheduID=$cicoRow['scheduID'];
                            $cicoencounter_id=$cicoRow['encounter_id'];
                            $cicotransactionAmount=$cicoRow['transactionAmount'];
                            $cicois_api=$cicoRow['is_api'];
                            $cicoreturnid=$fet_payment_main['payment_id'];
                            $payment_method=$fet_payment_main['payment_method'];

                            $cicodata_str.=' data-laneid_'.$cicoreturnid.'="'.$cicolaneID.'"  ';
                            $cicodata_str.=' data-transactionnumber_'.$cicoreturnid.'="'.$cicoTransactionNumber.'"  ';
                            $cicodata_str.=' data-scheduid_'.$cicoreturnid.'="'.$cicoscheduID.'"  ';
                            $cicodata_str.=' data-encounterid_'.$cicoreturnid.'="'.$cicoencounter_id.'"  ';
                            $cicodata_str.=' data-returnid_'.$cicoreturnid.'="'.$cicoreturnid.'"  ';
                            $cicodata_str.=' data-payment_method_'.$cicoreturnid.'="'.$payment_method.'"  ';
                            $cicodata_str.=' data-transactionamount_'.$cicoreturnid.'="'.$cicotransactionAmount.'"  ';
                            $cicodata_str.=' data-is_api_'.$cicoreturnid.'="'.$cicois_api.'"  ';
                        }
						$payment_id_arr[]=$fet_payment_main['payment_id'];
						$total_payment_arr[$fet_payment_main['payment_id']]=$fet_payment_main['total_payment'];
						$data_str_arr[$fet_payment_main['payment_id']]=$cicodata_str;
						$payment_method_arr[$fet_payment_main['payment_id']]=$fet_payment_main['payment_method'];
						$payment_status_arr[$fet_payment_main['payment_id']]=$fet_payment_main['del_status'];
						if($fet_payment_main['payment_method']=='Check' or $fet_payment_main['payment_method']=='EFT' or $fet_payment_main['payment_method']=='Money Order' or $fet_payment_main['payment_method']=='VEEP'){
							$payment_check_cc_arr[$fet_payment_main['payment_id']]=$fet_payment_main['check_no'];
						}else if($fet_payment_main['payment_method']=='Credit Card'){
							$cc_no=substr($fet_payment_main['cc_no'],-4);
							$payment_check_cc_arr[$fet_payment_main['payment_id']]=$fet_payment_main['cc_type'] .' - '. $cc_no;
							$payment_cc_date_arr[$fet_payment_main['payment_id']]=$fet_payment_main['cc_expire_date'];
						}
						$created_on_arr[$fet_payment_main['payment_id']]=get_date_format($fet_payment_main['created_on'],'','',2).' '.$fet_payment_main['created_time'];
						$created_by_arr[$fet_payment_main['payment_id']]=$fet_payment_main['created_by'];
						if($fet_payment_main['modified_on']!="0000-00-00"){
							$modified_on_arr[$fet_payment_main['payment_id']]=get_date_format($fet_payment_main['modified_on'],'','',2).' '.$fet_payment_main['modified_time'];
							$modified_by_arr[$fet_payment_main['payment_id']]=$fet_payment_main['modified_by'];
							$created_on_arr[$fet_payment_main['payment_id']]=get_date_format($fet_payment_main['modified_on'],'','',2).' '.$fet_payment_main['modified_time'];
							$created_by_arr[$fet_payment_main['payment_id']]=$fet_payment_main['modified_by'];
						}else{
							$modified_on_arr[$fet_payment_main['payment_id']]=' - ';
							$modified_by_arr[$fet_payment_main['payment_id']]=' - ';
						}
						
						if($fet_payment_main['delete_date']!="0000-00-00"){
							$deleted_on_arr[$fet_payment_main['payment_id']]=get_date_format($fet_payment_main['delete_date'],'','',2).' '.date("h:i A",strtotime($fet_payment_main['delete_time']));
							$deleted_by_arr[$fet_payment_main['payment_id']]=$fet_payment_main['delete_operator_id'];
						}else{
							$deleted_on_arr[$fet_payment_main['payment_id']]=' - ';
							$deleted_by_arr[$fet_payment_main['payment_id']]=' - ';
						}
						$payment_type[$fet_payment_main['payment_id']]=$fet_payment_main['payment_type'];
						if($fet_payment_main['payment_type']=="checkout"){
							$payment_comment[$fet_payment_main['payment_id']]=$fet_payment_main['co_comment'];
							$payment_sch_id[$fet_payment_main['payment_id']]=$fet_payment_main['sch_id'];


							$payment_id_by_sch[$fet_payment_main['sch_id']]=$fet_payment_main['payment_id'];
						}
						$ci_co_sch_arr[$fet_payment_main['payment_id']]=$fet_payment_main['sch_id'];
					}	
					
					
					$post_check_in_out=array();
					$post_charge_list_id_arr=array();
					$past_payment_chk_arr=array();
					if(imw_num_rows($qry_payment_main)>0){
						$query = "SELECT a.unapply,a.payment_id,a.encounter_id, a.payment_amount, a.payment_mode, a.checkNo, a.creditCardNo,
								a.creditCardCo, a.expirationDate, a.date_of_payment,a.operatorId, b.check_in_out_payment_id,
								b.check_in_out_payment_detail_id, b.charge_list_detail_id,b.status as ciopp_del_status,c.date_of_service,
								patient_charges_detail_payment_info.paidForProc, patient_charges_detail_payment_info.overPayment,
								patient_charges_detail_payment_info.entered_date 
								FROM patient_chargesheet_payment_info as a
								JOIN check_in_out_payment_post as b ON b.acc_payment_id = a.payment_id 
								JOIN patient_charge_list as c ON c.encounter_id = a.encounter_id 
								JOIN patient_charges_detail_payment_info ON patient_charges_detail_payment_info.payment_id = a.payment_id 
								WHERE 
								c.del_status='0' and b.patient_id = '".$patient_id."' AND c.patient_id = '".$patient_id."' 
								AND patient_charges_detail_payment_info.deletePayment = '0' ORDER BY a.date_of_payment";
						$qry_check_in_out_post = imw_query($query);
						while($get_check_in_out_post = imw_fetch_array($qry_check_in_out_post)){
							$post_pay_id = $get_check_in_out_post['check_in_out_payment_id'];
							$post_pay_detail_id = $get_check_in_out_post['check_in_out_payment_detail_id'];
							$post_check_in_out[$post_pay_detail_id][]=$get_check_in_out_post;
							$post_charge_list_id_arr[]=$get_check_in_out_post['charge_list_detail_id'];
							if($get_check_in_out_post['ciopp_del_status']==0 && $get_check_in_out_post['unapply']==0){
								$past_payment_chk_arr[$post_pay_detail_id][]=$get_check_in_out_post['paidForProc']+$get_check_in_out_post['overPayment'];
								$main_past_payment_chk_arr[$post_pay_id][]=$get_check_in_out_post['paidForProc']+$get_check_in_out_post['overPayment'];
							}
						}
					}
					
					$post_charge_list_id_imp=implode("','",$post_charge_list_id_arr);
					$get_proc_code=imw_query("select cft.cpt_prac_code,pcld.charge_list_detail_id
						from 
							patient_charge_list_details  as pcld,
							cpt_fee_tbl as cft
						where 
						pcld.del_status='0'
						and pcld.procCode=cft.cpt_fee_id 
						and pcld.charge_list_detail_id in('$post_charge_list_id_imp')");
					while($fet_proc_code=imw_fetch_array($get_proc_code)){
							$cpt_prac_code=$fet_proc_code['cpt_prac_code'];
							$proc_arr[$fet_proc_code['charge_list_detail_id']]=$cpt_prac_code;
					}
					
					$del_whr=" and status='0'";
					if($ci_del_status>0){
						$del_whr="";
					}
					$item_ids_imp=implode("','",$item_ids_arr);	
					$payment_id_imp=implode("','",$payment_id_arr);	
					$payment_detail=array();
					$chk_ci_by_cdi=array();				
					$qry_payment="select  * from check_in_out_payment_details where payment_id in('$payment_id_imp') $del_whr";
					$sel_payment=imw_query($qry_payment);
					$seq=1;
					while($fet_payment=imw_fetch_array($sel_payment)){
						$pay_id=$fet_payment['payment_id'];
						$payment_detail[$pay_id][]=$fet_payment;
						$chk_ci_by_cdi[$fet_payment['id']]=$pay_id;
					}
					
					$ci_co_sch_imp=implode("','",$ci_co_sch_arr);	
					$ci_co_facility_arr=array();	
					$qry_payment="select id,sa_facility_id from schedule_appointments where id in('$ci_co_sch_imp')";
					$sel_payment=imw_query($qry_payment);
					while($fet_sch=imw_fetch_array($sel_payment)){
						$ci_co_facility_arr[$fet_sch['id']]=$fet_sch['sa_facility_id'];
					}
					
					//----------------- Posted CI/CO Payment Detail -----------------//
					$post_check_in_manual_out=array();
					$manual_qry_payment="select  * from check_in_out_payment_post where patient_id  ='$patient_id' and status='0'";
					$manual_sel_payment=imw_query($manual_qry_payment);
					while($manual_fet_payment=imw_fetch_array($manual_sel_payment)){
						$pay_detail_id=$manual_fet_payment['check_in_out_payment_detail_id'];
						$post_check_in_manual_out[$pay_detail_id][]=$manual_fet_payment;
						$post_check_in_manual_pay[$pay_detail_id][]=$manual_fet_payment['manually_payment'];
						$main_post_check_in_manual_pay[$manual_fet_payment['check_in_out_payment_id']][]=$manual_fet_payment['manually_payment'];
					}
					
					//----------------- Refund CI/CO Payment Detail -----------------//
					$sel_ref_qry=imw_query("select * from ci_pmt_ref where patient_id='$patient_id' and del_status='0'");
					while($sel_ref_row=imw_fetch_array($sel_ref_qry)){
						if($sel_ref_row['ci_co_id']>0){
							$ci_ref_detail[$sel_ref_row['ci_co_id']][]=$sel_ref_row;
							$past_payment_chk_arr[$sel_ref_row['ci_co_id']][]=$sel_ref_row['ref_amt'];
							$main_past_payment_chk_arr[$chk_ci_by_cdi[$sel_ref_row['ci_co_id']]][]=$sel_ref_row['ref_amt'];
						}
						if($sel_ref_row['pmt_id']>0){
							$pmt_ref_detail[$sel_ref_row['pmt_id']][]=$sel_ref_row;
							$pmt_past_payment_chk_arr[$sel_ref_row['pmt_id']][]=$sel_ref_row['ref_amt'];
						}
					}
					
					for($p=0;$p<count($payment_id_arr);$p++){
						$seq=$p+1;
						$pay_id=$payment_id_arr[$p];
						$pay_sch_id=$payment_sch_id[$pay_id];
						$pay_detail=$payment_detail[$pay_id];
						for($d=0;$d<count($pay_detail);$d++){
							$item_id=$pay_detail[$d]['item_id'];
							$tran_payment_detail_id=$pay_detail[$d]['id'];
							if($pay_detail[$d]['item_payment']>0){
								$cico_link_cls=$cico_del_cls=$pop_var=$edit_pop_var=$show_deleted_date="";
								$chk_post_check_in_manual_pay=str_replace(',','',number_format(array_sum($post_check_in_manual_pay[$tran_payment_detail_id]),2));
								$chk_past_payment_chk_arr=str_replace(',','',number_format(array_sum($past_payment_chk_arr[$tran_payment_detail_id]),2));
								
								$stop_del="";
								if(array_sum($past_payment_chk_arr[$tran_payment_detail_id])>0 || array_sum($post_check_in_manual_pay[$tran_payment_detail_id])>0 || $pay_detail[$d]['status']>0){
									$stop_del=1;
								}
								
								if($pay_detail[$d]['item_payment']>$chk_post_check_in_manual_pay && $pay_detail[$d]['item_payment']>$chk_past_payment_chk_arr){
									$pop_var="show_right_div('".$pay_id."','".$pay_detail[$d]['id']."','".$item_names_arr[$item_id]."','".str_replace(',','',number_format($pay_detail[$d]['item_payment'],2))."');";
									$edit_pop_var="edit_acc_pay('".$pay_id."','".$pay_detail[$d]['id']."','".$stop_del."');";
									$cico_link_cls="text_purple";
								}
								if($pay_detail[$d]['status']>0){
									$pop_var="";
									$cico_link_cls="";
									$cico_del_cls="text-danger";
									$edit_pop_var="";
									if($pay_detail[$d]['delete_date']!='0000-00-00'){
										$show_deleted_date = ' - '.date("m-d-y",strtotime($pay_detail[$d]['delete_date'])).' '. date("h:i A",strtotime($pay_detail[$d]['delete_time'])); 
									}
									if($pay_detail[$d]['delete_operator_id']>0){
										$del_opr_name= show_opr_init($get_prov[$pay_detail[$d]['delete_operator_id']]);
										$show_deleted_date.=' '.$del_opr_name;
									}
								}
								
								$pt_ci_co_pending_amt=$pay_detail[$d]['item_payment']-($chk_post_check_in_manual_pay+$chk_past_payment_chk_arr);
								$ci_co_sch_id=$ci_co_sch_arr[$pay_id];
					?>
						<tr style="background-color:#eeeecd;" class="details_payment_<?php echo $pay_id; ?> text-center">
							<td>
								<div class="checkbox">
									<input type="hidden" name="cio_pay_id_arr[<?php echo $tran_payment_detail_id; ?>]" value="<?php echo $pay_id; ?>">
									<input type="hidden" name="chk_in_out_pay_arr[<?php echo $tran_payment_detail_id; ?>]" value="<?php echo $pay_detail[$d]['item_payment'];  ?>">
									<input type="checkbox" <?php echo $data_str_arr[$pay_id];?> name="chk_in_out_arr[]" id="chk_in_out_arr_<?php echo $tran_payment_detail_id; ?>" class="chk_box_css" value="<?php echo $tran_payment_detail_id;?>" <?php if($stop_del==1){echo "disabled";} ?>>
									<label for="chk_in_out_arr_<?php echo $tran_payment_detail_id;?>"></label>
								</div>
							</td>
							<td></td>
							<td></td>
							<td class="<?php echo $cico_link_cls; ?> text-left pointer <?php echo $cico_del_cls; ?>" onclick="<?php echo $edit_pop_var; ?>" onMouseDown="<?php echo $pop_var; ?>"><?php echo $item_names_arr[$item_id]; ?></td>
							<td class="<?php echo $cico_link_cls; ?> text-right pointer <?php echo $cico_del_cls; ?> payment_amt_<?php echo $tran_payment_detail_id;?>" data-total_amt_<?php echo $pay_id;?>="<?php echo number_format($pay_detail[$d]['item_payment'],2); ?>" onclick="<?php echo $edit_pop_var; ?>" onMouseDown="<?php echo $pop_var; ?>">
                            	<?php echo '$'.number_format($pay_detail[$d]['item_payment'],2).'<span style="color:#F00">/'.numberformat($pt_ci_co_pending_amt,2,'yes').'</span>'; ?>
                            </td>
							<td class="<?php echo $cico_del_cls; ?>"><?php echo $payment_method_arr[$pay_id]; ?></td>
                            <td class="<?php echo $cico_del_cls; ?>"><?php echo $facility_arr[$ci_co_facility_arr[$ci_co_sch_id]]; ?></td>
							<td class="<?php echo $cico_del_cls; ?>"><?php echo $payment_check_cc_arr[$pay_id]; ?></td>
							<td class="text-left">
							<?php 
								$opr_name_exp= show_opr_init($get_prov[$created_by_arr[$pay_id]]);
								echo '<span class="'.$cico_del_cls.'">'.$created_on_arr[$pay_id].' '.$opr_name_exp.'</span><span class="text-red">'.$show_deleted_date.'</span>';
							?>
							</td>
						</tr>
						<?php 
							$post_details=$post_check_in_out[$tran_payment_detail_id];
							for($k=0;$k<count($post_details);$k++){
								if($post_details[$k]['paidForProc']>0){
								$paid_amt=$post_details[$k]['paidForProc']+$post_details[$k]['overPayment'];
								$cico_link_cls="";
								$delClass="";
								if($post_details[$k]['ciopp_del_status']>0 && $post_details[$k]['unapply']>0){
									$cico_link_cls="text_10 red_color";
									$delClass=' text-danger';
								}
						?>
							<tr style="background:#fdfde5;" class="<?php echo $delClass; ?> details_payment_<?php echo $pay_id; ?> text-center">
								<td></td>
								<td class="text-nowrap">	
									<?php
										echo get_date_format($post_details[$k]['date_of_service'],'','',2); 
								  ?>
								</td>
								<td><?php echo $post_details[$k]['encounter_id']; ?></td>
								<td class="text-left"><?php echo $proc_arr[$post_details[$k]['charge_list_detail_id']]; ?></td>
								<td class="text-right"><?php echo "$".number_format($paid_amt,2); ?></td>
								<td></td>
								<td></td>
                                <td></td>
								<td class="text-left">
									<div class="row">
										<div class="col-sm-6">
											<?php 
												$opr_pay_arr=array();
												$opr_name_exp="";
												$opr_name_exp= show_opr_init($get_prov[$post_details[$k]['operatorId']]);
												echo get_date_format($post_details[$k]['entered_date']).' '.$opr_name_exp;
											 ?>
										</div>
										<div class="text-right col-sm-6">
											<?php if($post_details[$k]['ciopp_del_status']==0 && $post_details[$k]['unapply']==0){?>
												<input type="button" class="btn btn-success" id="Unapply_cico_<?php echo $post_details[$k]['payment_id']; ?>" name="Unapply" value="Unapply" onClick="unapply_fun('<?php echo $post_details[$k]['payment_id']; ?>','ci');"/>
											<?php } ?>
										</div> 
									</div>    	
								 </td>
							</tr>
						<?php	
								}		
							}
						?>
						<?php 
							$manual_post_details=$post_check_in_manual_out[$tran_payment_detail_id];
							for($k=0;$k<count($manual_post_details);$k++){
								if($manual_post_details[$k]['manually_payment']>0){
								$manually_pay=$manual_post_details[$k]['manually_payment'];
						?>
							<tr style="background:#fdfde5;" class="details_payment_<?php echo $pay_id; ?> text-center">
								<td></td>
								<td></td>
								<td></td>
								<td class="text-left">Manual Payment</td>
								<td class="text-right"><?php echo "$".number_format($manually_pay,2); ?></td>
								<td></td>
								<td></td>
                                <td></td>	
								<td class="text-left">
								 <?php 
									$opr_pay_arr=array();
									$opr_name_exp="";
									$opr_name_exp= show_opr_init($get_prov[$manual_post_details[$k]['manually_by']]);
									echo get_date_format($manual_post_details[$k]['manually_date']).' '.$opr_name_exp;
								 ?>		
								</td>
							</tr>
						<?php	
								}		
							 }
						?>
						
						<?php 
							$ci_ref_post_detail=$ci_ref_detail[$tran_payment_detail_id];
							for($k=0;$k<count($ci_ref_post_detail);$k++){
								if($ci_ref_post_detail[$k]['ref_amt']>0){
								$refund_pay=$ci_ref_post_detail[$k]['ref_amt'];
								$ref_check_cc_detail=$cc_no="";
								if($ci_ref_post_detail[$k]['payment_method']=='Check' || $ci_ref_post_detail[$k]['payment_method']=='EFT' || $ci_ref_post_detail[$k]['payment_method']=='Money Order' || $ci_ref_post_detail[$k]['payment_method']=='VEEP'){
									$ref_check_cc_detail=$ci_ref_post_detail[$k]['check_no'];
								}else if($ci_ref_post_detail[$k]['payment_method']=='Credit Card'){
									$cc_no=substr($ci_ref_post_detail[$k]['cc_no'],-4);
									$ref_check_cc_detail=$ci_ref_post_detail[$k]['cc_co'] .' - '. $cc_no;
								}
						?>
							<tr style="background:#ff9933;" class="details_payment_<?php echo $pay_id; ?> text-center">
								<td></td>
								<td></td>
								<td></td>
								<td class="text-left">Refund</td>
								<td class="text-right"><?php echo "$".number_format($refund_pay,2); ?></td>
								<td><?php echo $ci_ref_post_detail[$k]['payment_method']; ?></td>
                                <td></td>
								<td><?php echo $ref_check_cc_detail; ?></td>
								<td class="text-left">
								 <?php 
									$opr_pay_arr=array();
									$opr_name_exp="";
									$opr_name_exp= show_opr_init($get_prov[$ci_ref_post_detail[$k]['entered_by']]);
									echo get_date_format($ci_ref_post_detail[$k]['entered_date']).' '.$opr_name_exp;
								 ?>		
								</td>
							</tr>
						<?php	
								}		
							 }
						  }		
					   }
					?>
					 <?php 
						if($payment_comment[$pay_id]!="" && $payment_id_by_sch[$pay_sch_id]==$pay_id){
					?>
                    <tr>
						<td colspan="9">
							<strong>Check Out Comment :-</strong> &nbsp; <?php echo $payment_comment[$pay_id]; ?>
						</td>
					</tr>
					<?php } ?>		
					<?php		
						}
					?>
					 <?php if(count($payment_id_arr)==0){?>
						<tr>
							<td colspan="9" class="text-center lead"><?php echo imw_msg('no_rec'); ?></td>
						</tr>
					<?php } ?>
				</table>
			</div>
            <div style="padding-top:10px;"></div>
			<table class="table table-hover table-striped" style="margin:0px;">
				<tr class="purple_bar">
					<td>Prepayments</td>
					<td class="col-sm-1">
						<select name="pmt_del_status" onChange="document.manual_frm.submit();" class="selectpicker" data-width="100%">
							<option value="0" <?php if($pmt_del_status!="1"){echo"selected";} ?>>Active</option>
							<option value="1" <?php if($pmt_del_status=="1"){echo"selected";} ?>>All</option>
						</select>
					</td>
				</tr>
			</table>
			<div id="patient_pre_payment_form" style="height:<?php echo $sess_height_div; ?>px; overflow-y:auto; overflow-x:hidden;">
				<table class="table table-bordered table-hover table-striped">
					<tr class="grythead">
						<th>Apply</th>
						<th>DOS</th>
						<th>Encounter</th>
						<th>Field Name/Proc Code</th>
						<th nowrap>Total/Balance</th>
						<th>Payment Method</th>
                        <th>Facility</th>
						<th>CC / Ch. <?php getHashOrNo();?></th>
						<th>Comment</th>
						<th>Date Of Transaction</th>
					</tr>
					<?php
					$del_whr=" and del_status='0'";
					if($pmt_del_status>0){
						$del_whr="";
					}
					//----------------- Patient Pre Payment Detail -----------------//
					$depo_qry = " select * from patient_pre_payment where patient_id='$patient_id' $del_whr order by entered_date desc, entered_time desc";
					$depo_mysql = imw_query($depo_qry);
					if(imw_num_rows($depo_mysql)>0){
						$post_pay_id="";
						$post_check_in_out=array();
						$post_charge_list_id_arr=array();
						$past_payment_chk_arr=array();
						$query = "SELECT patient_charge_list.encounter_id,patient_chargesheet_payment_info.unapply,patient_chargesheet_payment_info.payment_mode, patient_chargesheet_payment_info.checkNo,
								patient_chargesheet_payment_info.creditCardNo,patient_chargesheet_payment_info.payment_id,
								patient_chargesheet_payment_info.creditCardCo, patient_chargesheet_payment_info.expirationDate, patient_chargesheet_payment_info.date_of_payment,
								patient_chargesheet_payment_info.operatorId,patient_charges_detail_payment_info.patient_pre_payment_id,patient_charges_detail_payment_info.entered_date, 
								patient_charges_detail_payment_info.charge_list_detail_id, patient_charge_list.date_of_service, 
								patient_charges_detail_payment_info.paidForProc, patient_charges_detail_payment_info.overPayment,
								patient_charges_detail_payment_info.payment_details_id,patient_charges_detail_payment_info.paid_time,
								patient_charge_list_details.coPayAdjustedAmount,patient_charge_list_details.charge_list_detail_id as chld_charge_list_detail_id
								FROM 
								patient_charge_list 
								JOIN patient_charge_list_details on patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id
								JOIN patient_chargesheet_payment_info on patient_chargesheet_payment_info.encounter_id=patient_charge_list.encounter_id
								JOIN patient_charges_detail_payment_info on patient_charges_detail_payment_info.payment_id=patient_chargesheet_payment_info.payment_id 
								JOIN patient_pre_payment on patient_pre_payment.id=patient_charges_detail_payment_info.patient_pre_payment_id
								WHERE 
								patient_charges_detail_payment_info.deletePayment='0' and patient_pre_payment.del_status = '0' 
								and patient_pre_payment.patient_id = '".$patient_id."' and patient_charge_list.patient_id = '".$patient_id."' 
								and patient_charges_detail_payment_info.patient_pre_payment_id>0 ORDER BY patient_chargesheet_payment_info.date_of_payment";
						$qry_check_in_out_post = imw_query($query);
						while($get_check_in_out_post = imw_fetch_array($qry_check_in_out_post)){
							if($get_check_in_out_post['charge_list_detail_id']==0 && $get_check_in_out_post['coPayAdjustedAmount']>0){
								$charge_list_detail_id=$get_check_in_out_post['chld_charge_list_detail_id'];
							}else{
								$charge_list_detail_id=$get_check_in_out_post['charge_list_detail_id'];
							}
							$post_pay_id = $get_check_in_out_post['patient_pre_payment_id'];
							$post_check_in_out[$post_pay_id][$get_check_in_out_post['payment_details_id']]=$get_check_in_out_post;
							$post_charge_list_id_arr[]=$charge_list_detail_id;
							if($get_check_in_out_post['unapply']==0){
								$past_payment_chk_arr[$post_pay_id][$get_check_in_out_post['payment_details_id']]=$get_check_in_out_post['paidForProc']+$get_check_in_out_post['overPayment'];
							}
						}
						$proc_arr=array();
						$post_charge_list_id_imp=implode("','",$post_charge_list_id_arr);
						$get_proc_code=imw_query("select cft.cpt_prac_code,pcld.charge_list_detail_id
												 from patient_charge_list_details  as pcld,cpt_fee_tbl as cft
												 where pcld.del_status='0' and pcld.procCode=cft.cpt_fee_id 
												 and pcld.charge_list_detail_id in('$post_charge_list_id_imp')");
						while($fet_proc_code=imw_fetch_array($get_proc_code)){
								$cpt_prac_code=$fet_proc_code['cpt_prac_code'];
								$proc_arr[$fet_proc_code['charge_list_detail_id']]=$cpt_prac_code;
						}
					}
						
					$i=0;
					$pre_payment_count=0;
					while($dpRows = imw_fetch_array($depo_mysql)) 
					{
						$i++;
						$pre_payment_count=$i;
						$delClass=$pop_var=$edit_pop_var=$show_deleted_date='';	
						$apply_pre_pay=0;
						$apply_pre_pay=array_sum($past_payment_chk_arr[$dpRows['id']]);
						$apply_pre_pay=$apply_pre_pay+array_sum($pmt_past_payment_chk_arr[$dpRows['id']]);
						$link_cls="text_purple";
						
						$stop_del="";
						if($dpRows['apply_payment_type']=='manually' || $apply_pre_pay>=$dpRows['paid_amount'] || $dpRows['del_status']>0){
							$link_cls="";
							$stop_del=1;
						}
						
						if($dpRows['apply_payment_type']!='manually' && $apply_pre_pay<$dpRows['paid_amount']){	
							$pop_var="show_ppp_right_div('". $dpRows['id']."','". $dpRows['paid_amount']."','". trim(addslashes(str_replace(array("\r\n", "\n", "\r",'"')," ",$dpRows['comment'])))."');";
							$edit_pop_var="edit_pre_pay('". $dpRows['id']."','".$stop_del."');";
						}
						$pt_pre_pending_amt=0;
						$chk_pt_pre_paid_amt=0;
						$chk_post_details=$post_check_in_out[$dpRows['id']];
						foreach($chk_post_details as $key_post => $val_post){	
							if($chk_post_details[$key_post]['paidForProc']>0 && $chk_post_details[$key_post]['unapply']<=0){
								$chk_pt_pre_paid_amt=$chk_pt_pre_paid_amt+$chk_post_details[$key_post]['paidForProc']+$chk_post_details[$key_post]['overPayment'];
							}
						}
						
						$chk_pt_pre_paid_amt=$chk_pt_pre_paid_amt+array_sum($pmt_past_payment_chk_arr[$dpRows['id']])+$dpRows['apply_amount'];
						$pt_pre_pending_amt=$dpRows['paid_amount']-$chk_pt_pre_paid_amt;
						
						if($dpRows['del_status']=='1'){ 
							$pop_var='';
							$delClass=' text-danger';
							if($dpRows['trans_del_date']!='0000-00-00' && $dpRows['trans_del_date']!=""){
								$show_deleted_date = ' - '.date("m-d-y",strtotime($dpRows['trans_del_date'])).' '. date("h:i A",strtotime($dpRows['trans_del_date'])); 
							}
							if($dpRows['del_operator_id']>0){
								$del_opr_name= show_opr_init($get_prov[$dpRows['del_operator_id']]);
								$show_deleted_date.=' '.$del_opr_name;
							}
						}
                        
                        $pmtdata_str='';
                        if($dpRows['log_referenceNumber']!=''){
                            $pmtSql='select id,scheduID,encounter_id,laneId,TransactionNumber,transactionAmount,is_api from tsys_possale_transaction where log_referenceNumber="'.$dpRows['log_referenceNumber'].'" and patient_id="'.$patient_id.'" ';
                            $pmtRs=imw_query($pmtSql);
                            $pmtRow=imw_fetch_assoc($pmtRs);
                            $pmtTransactionNumber=$pmtRow['TransactionNumber'];
                            $pmtlaneID=$pmtRow['laneId'];
                            $pmtscheduID=$pmtRow['scheduID'];
                            $pmtencounter_id=$pmtRow['encounter_id'];
                            $pmttransactionAmount=$pmtRow['transactionAmount'];
                            $pmtis_api=$pmtRow['is_api'];
                            $pmtreturnid=$dpRows['id'];
                            $payment_method=$dpRows['payment_mode'];
                            $pmt_pay_amt=number_format($dpRows['paid_amount'],2);
                            
                            $pmtdata_str.=' data-laneid_'.$dpRows['id'].'="'.$pmtlaneID.'"  ';
                            $pmtdata_str.=' data-transactionnumber_'.$dpRows['id'].'="'.$pmtTransactionNumber.'"  ';
                            $pmtdata_str.=' data-scheduid_'.$dpRows['id'].'="'.$pmtscheduID.'"  ';
                            $pmtdata_str.=' data-encounterid_'.$dpRows['id'].'="'.$pmtencounter_id.'"  ';
                            $pmtdata_str.=' data-returnid_'.$dpRows['id'].'="'.$pmtreturnid.'"  ';
                            $pmtdata_str.=' data-payment_method_'.$dpRows['id'].'="'.$payment_method.'"  ';
                            $pmtdata_str.=' data-payment_amt_'.$dpRows['id'].'="'.$pmt_pay_amt.'"  ';
                            $pmtdata_str.=' data-transactionamount_'.$dpRows['id'].'="'.$pmttransactionAmount.'"  ';
                            $pmtdata_str.=' data-is_api_'.$dpRows['id'].'="'.$pmtis_api.'"  ';
                        }
					?>
					<tr style="background-color:#eeeecd;" class="text-center">
						<td>
                        	<div class="checkbox">
								<input type="hidden" name="pt_pre_pending_amt_chk[<?php echo $dpRows['id']; ?>]" value="<?php echo $pt_pre_pending_amt; ?>">
								<input type="checkbox" <?php echo $pmtdata_str; ?> name="pt_pre_payment_chk[]" id="pt_pre_payment_chk_<?php echo $dpRows['id'];  ?>" class="chk_box_css" value="<?php echo $dpRows['id']; ?>" <?php if($stop_del==1){echo "disabled";} ?>>
								<label for="pt_pre_payment_chk_<?php echo $dpRows['id'];  ?>"></label>
							</div>
						</td>
						<td></td>
						<td></td>
						<td></td>
						<td class="text-right <?php echo $link_cls; ?> pointer <?php echo $delClass; ?>" onMouseDown="<?php echo $pop_var; ?>" onClick="<?php echo $edit_pop_var; ?>">
							<?php echo '$'.number_format($dpRows['paid_amount'],2).'<span style="color:#F00">/'.numberformat($pt_pre_pending_amt,2,'yes').'</span>'; ?>
						</td>
						<td class="<?php echo $delClass; ?>">
							<?php echo $dpRows['payment_mode']; ?>
						</td>
                        <td class="<?php echo $delClass; ?>">
							<?php echo $facility_arr[$dpRows['facility_id']]; ?>
						</td>
						<td class="<?php echo $delClass; ?>">
							<?php 
							if($dpRows['payment_mode']=='Check' || $dpRows['payment_mode']=='EFT' || $dpRows['payment_mode']=='Money Order' || $dpRows['payment_mode']=='VEEP'){
								echo $dpRows['check_no'];
							}else if($dpRows['payment_mode']=='Credit Card'){
								$cc_no=substr($dpRows['cc_no'],-4);
								$credit_card_company="";
								if($dpRows['credit_card_co']=="AX"){
									$credit_card_company="American Express";
								}
								if($dpRows['credit_card_co']=="Dis"){
									$credit_card_company="Discover";
								}
								if($dpRows['credit_card_co']=="MC"){
									$credit_card_company="Master Card";
								}
								if($dpRows['credit_card_co']=="Visa"){
									$credit_card_company="Visa";
								}
								if($dpRows['credit_card_co']=="Care Credit"){
									$credit_card_company="Care Credit";
								}
								if($dpRows['credit_card_co']=="Other"){
									$credit_card_company="Other";
								}
								echo $credit_card_company.' - '.$cc_no;
							}
							 ?>
						</td>
                        <td class="text-left <?php echo $delClass; ?>">
                        	<?php echo $dpRows['comment']; ?>
                        </td>
						<td class="text-left">
							<?php 
								$show_entered_date="";
								$opr_name_exp="";
								if($dpRows['entered_date']!='0000-00-00' && $dpRows['entered_date']!=""){
									 $opr_name_exp= show_opr_init($get_prov[$dpRows['entered_by']]);
									$show_entered_date = date("m-d-y",strtotime($dpRows['entered_date'])).' '. date("h:i A",strtotime($dpRows['entered_time'])).' '.$opr_name_exp; 
								}
								echo '<span class="'.$delClass.'">'.$show_entered_date.'</span><span class="text-red">'.$show_deleted_date.'</span>';
							?>
						</td>                                
					</tr>
					<?php if($dpRows['apply_payment_type']=='manually'){?>
						<tr style="background:#fdfde5;" class="text-center">
							<td> </td>
							<td></td>
							<td>&nbsp; </td>
							<td class="text-left">Manual Payment</td>
							<td class="text-right">
								<?php echo '$'.number_format($dpRows['apply_amount'],2); ?>
							</td>
							<td></td>
							<td></td>
                            <td></td>
                            <td></td>
							<td class="text-left">
								<?php 
									$show_entered_date="";
									$opr_name_exp="";
									if($dpRows['apply_payment_date']!='0000-00-00' && $dpRows['apply_payment_date']!=""){
										 $opr_name_exp="";
										 $opr_name_exp= show_opr_init($get_prov[$dpRows['apply_payment_by']]);
										$show_entered_date = date("m-d-y",strtotime($dpRows['apply_payment_date'])).' '. date("h:i A",strtotime($dpRows['apply_payment_time'])).' '.$opr_name_exp; 
									}
									echo $show_entered_date;
								?>
							</td>
						</tr>
					<?php } ?>
					<?php 
					$post_details=$post_check_in_out[$dpRows['id']];
					foreach($post_details as $key_post => $val_post){	
						if($post_details[$key_post]['paidForProc']>0){
						$paid_amt=$post_details[$key_post]['paidForProc']+$post_details[$key_post]['overPayment'];
						
						 $delClass="";
						 if($post_details[$key_post]['unapply']>0){
							 $delClass=' text-danger';
						 }
					?>
							<tr style="background:#fdfde5;" class="<?php echo $delClass; ?> text-center">
								<td></td>
								<td class="text-nowrap">	
									<?php
										echo get_date_format($post_details[$key_post]['date_of_service'],'','',2); 
									?>
								</td>
								<td><?php echo $post_details[$key_post]['encounter_id']; ?></td>
								<td class="text-left">
								<?php 
									if($post_details[$key_post]['charge_list_detail_id']>0){
										echo $proc_arr[$post_details[$key_post]['charge_list_detail_id']]; 
									}else{
										echo "Copay";
									}
								?>
								</td>
								<td class="text-right"><?php echo "$".number_format($paid_amt,2); ?></td>
								<td></td>
								<td></td>
                                <td></td>
                                <td></td>
								<td class="text-left">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <?php 
                                                $show_entered_date="";
                                                $opr_name_exp="";
                                                $enter_date_exp="";
                                                if($post_details[$key_post]['entered_date']!='0000-00-00 00:00:00' && $post_details[$key_post]['entered_date']!=""){
                                                    $opr_name_exp="";
                                                    $enter_date_exp=explode(' ',$post_details[$key_post]['entered_date']);
                                                    $opr_name_exp= show_opr_init($get_prov[$post_details[$key_post]['operatorId']]);
                                                    $show_entered_date = date("m-d-y",strtotime($enter_date_exp[0])).' '. date("h:i A",strtotime($enter_date_exp[1])).' '.$opr_name_exp; 
                                                }
                                                echo $show_entered_date;
                                             ?>	
                                        </div>
                                        <div class="text-right col-sm-6">
                                            <?php if($post_details[$key_post]['unapply']==0){?>
                                                <input type="button" class="btn btn-success" id="Unapply_pp_<?php echo $post_details[$key_post]['payment_id']; ?>" name="Unapply" value="Unapply" onClick="unapply_fun('<?php echo $post_details[$key_post]['payment_id']; ?>','pp');"/>
                                            <?php } ?>
                                        </div> 
                                    </div>  
								</td>
							</tr>
				<?php	
						}		
					}
				?>
						
				<?php 
					$pmt_ref_post_detail=$pmt_ref_detail[$dpRows['id']];
					for($k=0;$k<count($pmt_ref_post_detail);$k++){
						if($pmt_ref_post_detail[$k]['ref_amt']>0){
						$refund_pay=$pmt_ref_post_detail[$k]['ref_amt'];
						$ref_check_cc_detail=$cc_no="";
						if($pmt_ref_post_detail[$k]['payment_method']=='Check' || $pmt_ref_post_detail[$k]['payment_method']=='EFT' || $pmt_ref_post_detail[$k]['payment_method']=='Money Order' || $pmt_ref_post_detail[$k]['payment_method']=='VEEP'){
							$ref_check_cc_detail=$pmt_ref_post_detail[$k]['check_no'];
						}else if($pmt_ref_post_detail[$k]['payment_method']=='Credit Card'){
							$cc_no=substr($pmt_ref_post_detail[$k]['cc_no'],-4);
							$ref_check_cc_detail=$pmt_ref_post_detail[$k]['cc_co'] .' - '. $cc_no;
						}
				?>
					<tr style="background:#ff9933;" class="text-center">
						<td></td>
						<td></td>
						<td></td>
						<td class="text-left">Refund</td>
						<td class="text-right"><?php echo "$".number_format($refund_pay,2); ?></td>
						<td><?php echo $pmt_ref_post_detail[$k]['payment_method']; ?></td>
                        <td></td>
						<td><?php echo $ref_check_cc_detail; ?></td>
                        <td></td>
						<td class="text-left">
                        <?php 
                            $opr_pay_arr=array();
                            $opr_name_exp="";
                            $opr_name_exp= show_opr_init($get_prov[$pmt_ref_post_detail[$k]['entered_by']]);
                            echo date("m-d-y",strtotime($pmt_ref_post_detail[$k]['entered_date'])).' '. date("h:i A",strtotime($pmt_ref_post_detail[$k]['entered_time'])).' '.$opr_name_exp;
                        ?>		
						</td>
					</tr>
				<?php	
					}		
				}
			}
			?>
			<?php if($pre_payment_count==0){?>
				<tr>
					<td colspan="10" class="text-center lead"><?php echo imw_msg('no_rec'); ?></td>
				</tr>
			<?php } ?>
		</table>
		</div>
	</form>
</div>

<?php
$login_facility=$_SESSION['login_facility'];
$operator_id=$_SESSION['authId'];
/* code for pos device in hidden*/
$cookieName="imedicwareposdevice_".$operator_id;
if(isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName]!='') {
    $poscookie=json_decode($_COOKIE[$cookieName],true);
    if(empty($poscookie)==false){
        if($poscookie['login_facility']==$login_facility) {
            if($poscookie['user_id']==$operator_id) {
                if($poscookie['expire_time']<=time()) {
                    unset($_COOKIE[$cookieName]);
                } else {
                    $defaultDevice=$poscookie['device_id'];
                }
            }
        }
    }
}

$pos_device=false;
$devicesArr=array();
$devices_sql="Select *, tsys_device_details.id as d_id from tsys_device_details 
              JOIN tsys_merchant ON tsys_merchant.id= tsys_device_details.merchant_id 
              WHERE device_status=0 
              AND tsys_device_details.facility_id='".$login_facility."' 
              AND merchant_status=0 
              ";
$resp = imw_query($devices_sql);
$devices_option = "";
$counter=0;
if ($resp && imw_num_rows($resp) > 0) {
    $pos_device=true;
    while ($row = imw_fetch_assoc($resp)) {
        $counter++;
        $ipAddress=$row['ipAddress'];
        $port=$row['port'];
        $device_url=$phpHTTPProtocol.$ipAddress.':'.$port;
        $selected='';
        if(!$defaultDevice && $counter==1) {
            $selected='selected="selected" ';
        } else {
            $selected=($row['d_id']==$defaultDevice)?'selected="selected" ':'';
        }
        $devices_option .= "<option ".$selected." data-device_ip='".$ipAddress."' data-device_url='".$device_url."' value='" . $row['d_id'] . "'>" . $row['deviceName'] . "</option>";
    }
}

?>

<?php if($pos_device) { ?>
    <div class="clearfix"></div>
    <div class="hide <?php //echo $cc_class;?>">
        <div class="">
            <select name="tsys_device_url" id="tsys_device_url" class="form-control minimal" onchange="setDefaultDevice(this);">
                <option value="no_pos_device">No POS</option>
                <?php echo $devices_option; ?>
            </select>
        </div>
        <input type="hidden" name="laneId" id="laneId" value="<?php //echo $laneID;?>" />
        <input type="hidden" name="referenceNumber" id="referenceNumber" value="" />
        <input type="hidden" name="tsys_payment_type_log_id" id="tsys_payment_type_log_id" value="" />
        <input type="hidden" name="pos_counter" id="pos_counter" value="0" />

        <input type="hidden" name="log_referenceNumber" id="log_referenceNumber" value="" />
        <input type="hidden" name="tsys_transaction_id" id="tsys_transaction_id" value="" />
        <input type="hidden" name="tsys_void_id" id="tsys_void_id" value="" />
        <input type="hidden" name="tsys_last_status" id="tsys_last_status" value="" />
        
        <input type="hidden" name="card_details_str_id" id="card_details_str_id" value="" />
    </div>

    <div id="div_loading_image" class="text-center" style="z-index:9999;display:none;">
        <div class="loading_container">
            <div class="process_loader"></div>
            <div id="div_loading_text" class="text-info"></div>
        </div>
    </div>

<?php } ?>

<script>
    var pos_device='<?php echo $pos_device; ?>';
    var pos_patient_id='<?php echo $patient_id;?>';
</script>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/pos/jquery.base64.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/pos/pos.js"></script>

<script type="text/javascript">
    var no_pos_device=false;
    if($('#tsys_device_url').val()=='no_pos_device') {
        no_pos_device=true;
    }
    
    if(pos_device && no_pos_device==false){
        var arr=["void_ci_co_pmt","Void","top.fmain.void_records();"]
    } else {
        var arr=["void_ci_co_pmt","Void","top.fmain.del_records('manual_frm');"]
    }
var ar = [["save_chk_in_out","Manual Payment","top.fmain.save_chk_in_out();"],
		  ["print_post","Print","top.fmain.print_post();"],
		  ["refund_ci_co_pmt","Refund","top.fmain.refund_ci_co_pmt();"],
          arr
          ];
	top.btn_show("ACCOUNT",ar);

$(document).ready(function()
{
	$('#pending_payment_div').draggable({'handle':'#pending_payment_div_head'});
});
<?php if($_REQUEST['show_pay_trans_id']){ ?>
	show_payments_div('<?php echo $_REQUEST['show_pay_trans_id']; ?>','show');
<?php } ?>
	if(parent.document.getElementById('coll_date')){	
		parent.document.getElementById('coll_date').style.display='none';	
	}
</script>

<script type="text/javascript">
var totalAmtArr={};
var laneIDArr={};
var referenceNumberArr={};
var transNumberArr=[];
var scheduidArr={};
var encounteridArr={};
var returnidArr={};
var transactionamountArr={};
var is_apiArr={};
function void_records() {
    $('[id^=chk_in_out_arr_]:checked').each(function(key, elem){
        var pmtAmtVal=0;
        var elemid=elem.value;
        var payid=$("input[name='cio_pay_id_arr["+elemid+"]']").val();
        
        var TransactionNumber=$(elem).data('transactionnumber_'+payid);
        var laneID=$(elem).data('laneid_'+payid);
        //var referenceNumber=$(elem).data('laneid_'+payid);
        var scheduid=$(elem).data('scheduid_'+payid);
        var encounterid=$(elem).data('encounterid_'+payid);
        var returnid=$(elem).data('returnid_'+payid);
        var payment_method=$(elem).data('payment_method_'+payid);
        var transactionamount=$(elem).data('transactionamount_'+payid);
        var is_api=$(elem).data('is_api_'+payid);
        
        pmtAmtVal=$('.payment_amt_'+elemid).data('total_amt_'+payid);

        pmtAmtVal=parseFloat(pmtAmtVal);
        if(TransactionNumber && payment_method=='Credit Card') {
            if (typeof(totalAmtArr[TransactionNumber])!= "undefined" && totalAmtArr[TransactionNumber]) {
                pmtAmtVal=parseFloat(totalAmtArr[TransactionNumber])+pmtAmtVal;
            }
            totalAmtArr[TransactionNumber]=pmtAmtVal;
            transNumberArr.push(TransactionNumber);
            if(laneID)
                laneIDArr[TransactionNumber]=laneID;
            //if(referenceNumber)
                //referenceNumberArr[TransactionNumber]=referenceNumber;
            if(scheduid)
                scheduidArr[TransactionNumber]=scheduid;
            if(encounterid)
                encounteridArr[TransactionNumber]=encounterid;
            if(returnid)
                returnidArr[TransactionNumber]=returnid;
            if(transactionamount)
                transactionamountArr[TransactionNumber]=transactionamount;
            if(is_api)
                is_apiArr[TransactionNumber]=is_api;
        }

    });
    


    $('[id^=pt_pre_payment_chk_]:checked').each(function(id, obj){
        var pmtAmtVal=0;
        var objid=obj.value;
        
        var TransactionNumber=$(obj).data('transactionnumber_'+objid);
        var laneID=$(obj).data('laneid_'+objid);
        //var referenceNumber=$(obj).data('laneid_'+payid);
        var scheduid=$(obj).data('scheduid_'+objid);
        var encounterid=$(obj).data('encounterid_'+objid);
        var returnid=$(obj).data('returnid_'+objid);
        var payment_method=$(obj).data('payment_method_'+objid);
        pmtAmtVal=$(obj).data('payment_amt_'+objid);
        var transactionamount=$(obj).data('transactionamount_'+objid);
        var is_api=$(obj).data('is_api_'+objid);
        
        pmtAmtVal=parseFloat(pmtAmtVal);
        if(TransactionNumber && payment_method=='Credit Card') {
            if (typeof(totalAmtArr[TransactionNumber])!= "undefined" && totalAmtArr[TransactionNumber]) {
                pmtAmtVal=parseFloat(totalAmtArr[TransactionNumber])+pmtAmtVal;
            }
            
            totalAmtArr[TransactionNumber]=pmtAmtVal;
            transNumberArr.push(TransactionNumber);
            if(laneID)
                laneIDArr[TransactionNumber]=laneID;
            //if(referenceNumber)
                //referenceNumberArr[TransactionNumber]=referenceNumber;
            if(scheduid)
                scheduidArr[TransactionNumber]=scheduid;
            if(encounterid)
                encounteridArr[TransactionNumber]=encounterid;
            if(returnid)
                returnidArr[TransactionNumber]=returnid;
            if(transactionamount)
                transactionamountArr[TransactionNumber]=parseFloat(transactionamount);
            if(is_api)
                is_apiArr[TransactionNumber]=is_api;
        }
        
    });

    transNumberArr=$.unique(transNumberArr);
    if($.isEmptyObject(totalAmtArr) && $.isEmptyObject(referenceNumberArr) && $.isEmptyObject(transNumberArr)){
        pos_submit_frm();
        return false;
    }
    
    var voidTrans=false;
    $.each(totalAmtArr, function(tranN, Amt) {
        if(transactionamountArr[tranN]!=Amt) {
            voidTrans=true;
        }
    });
        
    if(voidTrans) {
        blankallArray();
        top.fAlert('Please select all or choose Refund button.');
    } else {
        func_recursive();
    }
}


function func_recursive() {
    var i=$('#pos_counter').val();
    if(transNumberArr.length>0 && transNumberArr[i]) {
        var transactionNumber=transNumberArr[i];
        var totalAmt=totalAmtArr[transactionNumber];
        var laneID=laneIDArr[transactionNumber];
        var scheduID=scheduidArr[transactionNumber];
        var encounter_id=encounteridArr[transactionNumber];
        var voidid=returnidArr[transactionNumber];
        var transactionamount=transactionamountArr[transactionNumber];
        var is_api=is_apiArr[transactionNumber];
        var transactionType='16';

        $('.btn').prop('disabled', true);
                
        main_void_transaction(transactionNumber,totalAmt,transactionType,voidid,laneID,scheduID,encounter_id,is_api);
    }
}


function main_void_transaction(transactionNumber,totalAmt,transactionType,voidid,laneID,scheduID,encounter_id,is_api) {
    var r=confirm("Are you sure to void this transaction?");
    if (r==false)
    {
        blankallArray();
        return false;
    }

    $('#tsys_void_id').val(voidid);

    if(totalAmt) {
        totalAmt=parseFloat(totalAmt);
        totalAmt=Math.round(totalAmt*100);
    } else {
        totalAmt=false;  
    }

    $('#tsys_last_status').val('VOIDED');

    if(!transactionNumber) {
        top.fAlert('Invalid Transaction.');
        return false;
    }

    var posMachine='PRESENT';
    /*Create referenceNumber using ajax Log table entry */
    createReferenceNumber(posMachine);
    var referenceNumber=$('#log_referenceNumber').val();
    if(!referenceNumber) {
        console.log('referenceNumber does not exists.');
        return false;
    }

    show_cc_loading_image('show','', 'Please Wait...');
    if(is_api==1) {
        var tsysOrderNumber={};
        tsysOrderNumber.transactionNumber=transactionNumber;
        tsysOrderNumber.scheduID=scheduID;
        tsysOrderNumber.encounter_id=encounter_id;
        tsysOrderNumber.acc_sec='acc_sec';
        pos_api_payment(totalAmt,referenceNumber,tsysOrderNumber,'void');
    } else {
        totalAmt=false;
        chargeAmount( totalAmt, laneID, scheduID, encounter_id, transactionType, transactionNumber, referenceNumber, voidid ,'acc_void' );
    }

}

function blankallArray() {
    totalAmtArr={};
    laneIDArr={};
    referenceNumberArr={};
    transNumberArr=[];
    scheduidArr={};
    encounteridArr={};
    returnidArr={};
    transactionamountArr={};
    is_apiArr={};
}

function pos_submit_frm() {
    top.fmain.del_records('manual_frm');
}

    
</script>
<?php require_once("acc_footer.php");?>	