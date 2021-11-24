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

include_once('../../../config/globals.php');
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/acc_functions.php');
include_once($GLOBALS['fileroot'].'/library/classes/cls_common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_cl_functions.php');
include_once($GLOBALS['fileroot'].'/library/classes/class.language.php'); 
//require_once(dirname(__FILE__)."/../../common/functions.inc.php");
//require_once(dirname(__FILE__).'/../../core/core.lang.php');
//require_once("../appt_cl_functions.php");

function get_data_array($qry){
	$query_to_run = imw_query($qry);
	while($row = imw_fetch_array($query_to_run)){
		$return[] = $row;
	}
	return $return;
}

$obj_contactlens = new appt_contactlens();
$objCoreLang = new core_lang;
$operator_id = $_SESSION['authId'];
$operatorDetails = getRecords('users','id',$operator_id);	
//$operatorDetails = $objManageData->mysqlifetchdata();
$operatorName = substr($operatorDetails[0]['fname'],0,1).substr($operatorDetails[0]['lname'],0,1);
$bool_save_payments = true;
if(!core_check_privilege(array('priv_edit_financials'))){
	$prev_payment_qry = "SELECT payment_id FROM check_in_out_payment WHERE del_status=0 AND payment_type='checkout' AND sch_id='".$_REQUEST['sch_id']."' AND patient_id='".$_REQUEST['ci_pid']."' AND total_payment>0";
	$prev_payment_res = imw_query($prev_payment_qry);
	if($prev_payment_res && imw_num_rows($prev_payment_res)>0){
		$bool_save_payments = false;
	}		
}

//--- SAVE PATIENT CHECK OUT PAYMENT ----
if(trim($btn_submit) != ''){
	//--- CHECK OUT PAYMENTS ---
	if((count($check_in_out_pay) > 0 || $co_comment!="" || $edit_payment_tbl_id_cash>0 || $edit_payment_tbl_id_check>0 || $edit_payment_tbl_id_eft>0 || $edit_payment_tbl_id_mo>0 || $edit_payment_tbl_id_card>0) && $bool_save_payments){
		$payment_data_arr = array();
		$payment_types = array('Cash'=>'tot_cash_payment', 'Check'=>'tot_check_payment', 'Credit Card'=>'tot_card_payment','EFT'=>'tot_eft_payment','Money Order'=>'tot_mo_payment');
		$payment_data_arr['patient_id'] = $patient_id;
		$payment_data_arr['sch_id'] = $sch_id;
		$payment_data_arr['total_charges'] = $total_charges_txt;
//		$payment_data_arr['total_payment'] = $tot_payment_txt-$tot_CI_payment;
//		$payment_data_arr['payment_method'] = $payment_method;
		$payment_data_arr['check_no'] = $checkNo;
		$payment_data_arr['cc_type'] = $creditCardCo;
		$payment_data_arr['cc_no'] = $cCNo;
		$payment_data_arr['cc_expire_date'] = $expireDate;
		$payment_data_arr['del_status'] = 0;
		$payment_data_arr['payment_type'] = 'checkout';
		$payment_data_arr['co_comment'] = $co_comment;
		foreach($payment_types as $key=>$val){
			$payment_data_arr['payment_method'] = $key;
			$payment_data_arr['total_payment'] = $$val;
			if($key == 'Cash'){
				$payment_data_arr['check_no'] = '';
				$payment_data_arr['cc_type'] = '';
				$payment_data_arr['cc_no'] = '';
				$payment_data_arr['cc_expire_date'] = '';
                $payment_data_arr['log_referenceNumber'] = '';
                $payment_data_arr['tsys_transaction_id'] = '';
				if($edit_payment_tbl_id_cash == ''){
					$payment_data_arr["created_on"] = date("Y-m-d");
					$payment_data_arr["created_time"] = date('h:i A');
					$payment_data_arr["created_by"] = $_SESSION['authId'];
					$edit_payment_tbl_id_cash = Addrecords($payment_data_arr,'check_in_out_payment');
				}else{
					$payment_data_arr["modified_on"] = date("Y-m-d");
					$payment_data_arr["modified_by"] = $_SESSION['authId'];
					Updaterecords($edit_payment_tbl_id_cash,'payment_id',$payment_data_arr,'check_in_out_payment');
				}
			}
			if($key == 'Check' or $key == 'EFT' or $key == 'Money Order'){
				$payment_data_arr['check_no'] = $checkNo;
				$payment_data_arr['cc_type'] = '';
				$payment_data_arr['cc_no'] = '';
				$payment_data_arr['cc_expire_date'] = '';
                $payment_data_arr['log_referenceNumber'] = '';
                $payment_data_arr['tsys_transaction_id'] = '';
				if(trim($edit_payment_tbl_id_check)=='' || trim($edit_payment_tbl_id_eft)=='' || trim($edit_payment_tbl_id_mo)==''){
					$payment_data_arr["created_on"] = date("Y-m-d");
					$payment_data_arr["created_time"] = date('h:i A');
					$payment_data_arr["created_by"] = $_SESSION['authId'];
					if($key == 'Check'){
						$edit_payment_tbl_id_check = Addrecords($payment_data_arr,'check_in_out_payment');
					}
					else if($key == 'EFT'){
						$edit_payment_tbl_id_eft = Addrecords($payment_data_arr,'check_in_out_payment');
					}else if($key == 'Money Order'){
						$edit_payment_tbl_id_mo = Addrecords($payment_data_arr,'check_in_out_payment');
					}
				}else{
					$payment_data_arr["modified_on"] = date("Y-m-d");
					$payment_data_arr["modified_by"] = $_SESSION['authId'];
					if($key == 'Check'){
						Updaterecords($edit_payment_tbl_id_check,'payment_id',$payment_data_arr,'check_in_out_payment');
					}
					else if($key == 'EFT'){
						Updaterecords($edit_payment_tbl_id_eft,'payment_id',$payment_data_arr,'check_in_out_payment');
					}
					else if($key == 'Money Order'){
						Updaterecords($edit_payment_tbl_id_mo,'payment_id',$payment_data_arr,'check_in_out_payment');
					}
				}
			}
			if($key == 'Credit Card'){
				$payment_data_arr['cc_type'] = $creditCardCo;
				$payment_data_arr['cc_no'] = $cCNo;
				$payment_data_arr['cc_expire_date'] = $expireDate;
				$payment_data_arr['check_no'] = '';
                $payment_data_arr['log_referenceNumber'] = $log_referenceNumber;
                $payment_data_arr['tsys_transaction_id'] = $tsys_transaction_id;
                if(isset($card_details_str_id) && $card_details_str_id!=''){
                    $card_details_arr=explode('~~',trim($card_details_str_id));
                    $payment_data_arr['cc_type']=$card_details_arr[0];
                    $payment_data_arr['cc_no']=$card_details_arr[1];
                    $payment_data_arr['cc_expire_date']=$card_details_arr[2];
                }
				if($edit_payment_tbl_id_card == ''){
					$payment_data_arr["created_on"] = date("Y-m-d");
					$payment_data_arr["created_time"] = date('h:i A');
					$payment_data_arr["created_by"] = $_SESSION['authId'];
					$edit_payment_tbl_id_card = Addrecords($payment_data_arr,'check_in_out_payment');
				}else{
					$payment_data_arr["modified_on"] = date("Y-m-d");
					$payment_data_arr["modified_by"] = $_SESSION['authId'];
					Updaterecords($edit_payment_tbl_id_card,'payment_id',$payment_data_arr,'check_in_out_payment');
				}
			}
//			$edit_payment_tbl_id = Addrecords($payment_data_arr,'check_in_out_payment');
		}
		if($edit_payment_tbl_id_cash > 0 || $edit_payment_tbl_id_check > 0 || $edit_payment_tbl_id_card > 0 || $edit_payment_tbl_id_eft > 0 || $edit_payment_tbl_id_mo > 0){
			

			
			//--- PROCEDURE PAYMENT DETAILS TABLES ----
			$ietm_id_arr = array_keys($chk_payment_detail_arr);
			for($i=0;$i<count($ietm_id_arr);$i++){
				$item_id = $ietm_id_arr[$i];
				$req_pay_method = 'pay_method_'.($item_id);
//				echo $req_pay_method;
				$selct_pay_method = $_REQUEST[$req_pay_method];
				$pay_detail_arr = array();
				if($selct_pay_method=='Cash'){
					$pay_detail_arr["payment_id"] = $edit_payment_tbl_id_cash;
				}
				if($selct_pay_method=='Check' or $selct_pay_method == 'EFT' or $selct_pay_method == 'Money Order'){
					$pay_detail_arr["payment_id"] = $edit_payment_tbl_id_check;
				}
				if($selct_pay_method=='Credit Card'){
					$pay_detail_arr["payment_id"] = $edit_payment_tbl_id_card;
				}
				if($selct_pay_method=='EFT'){
					$pay_detail_arr["payment_id"] = $edit_payment_tbl_id_eft;
				}
				if($selct_pay_method=='Money Order'){
					$pay_detail_arr["payment_id"] = $edit_payment_tbl_id_mo;
				}
				$pay_detail_arr["item_id"] = $item_id;
				if($_POST['copay_dilated_'.$item_id]){
					$copay_type=1;
				}else if($_POST['copay_non_dilated_'.$item_id]){
					$copay_type=2;
				}else if($_POST['copay_test_dilated_'.$item_id]){
					$copay_type=1;
				}else if($_POST['copay_test_non_dilated_'.$item_id]){
					$copay_type=2;
				}else{
					$copay_type=0;
				}
				
				$pay_detail_arr["item_charges"] = preg_replace('/[\$,]/','',$_POST["item_charges_".$item_id]);
				$pay_detail_arr["item_payment"] = preg_replace('/[\$,]/','',$_POST["item_pay_".$item_id]);
				$pay_detail_arr["payment_type"] = 'checkout';
				//--- GET ALREADY EXISTS PAYMENT DETAILS --
				$payment_detail_id = $chk_payment_detail_arr[$item_id];
				if(in_array($item_id,$check_in_out_pay) === true){
					if($payment_detail_id == ''){
						$payment_detail_id = Addrecords($pay_detail_arr,'check_in_out_payment_details');
					}
					else{
						$item_charges = preg_replace('/[\$,]/','',$_POST["item_charges_".$item_id]);
						$item_payment = preg_replace('/[\$,]/','',$_POST["item_pay_".$item_id]);
						
						$up_checkIn=imw_query("update check_in_out_payment_details set 
									payment_id='".$pay_detail_arr["payment_id"]."',item_id='$item_id', 
									item_charges='$item_charges',item_payment='$item_payment', 
									copay_type='$copay_type'	
										where 
									id='$payment_detail_id' and payment_type='checkout'");
						//Updaterecords($payment_detail_id,'id',$pay_detail_arr,'check_in_out_payment_details');
					}
				}
				else{
					/*$delQry = "update check_in_out_payment_details set status='1',delete_date='".date("Y-m-d")."',delete_time='".date('H:i:s')."',delete_operator_id='".$_SESSION['authId']."' where id = '$payment_detail_id'";
					imw_query($delQry);*/
				}
			}
		}
	}
	?>
    <script type="text/javascript">
		window.opener.top.fmain.pre_load_front_desk('<?php echo $patient_id;?>','<?php echo $sch_id;?>'); 
	</script>
    <?php
	//--- PRINT RECIEPT ---	
	if(trim($btn_submit_print) != ''){
		$filePath = "check_in_out_payment.php?search=true&frm_status=show_check_out&ci_pid=$patient_id&sch_id=$sch_id&vip_check=".$_REQUEST['vip_alert_post'];
		?>
        <script type="text/javascript">
			var edit_id = "<?php print $edit_payment_tbl_id; ?>";
			var patient_id = "<?php print $patient_id; ?>";
			var sch_id = "<?php print $sch_id; ?>";
			window.open("payment_receipt.php?id="+sch_id+"&pid="+patient_id,'print_receipt','width=800,height=550,top=10,left=40,scrollbars=yes,resizable=yes');  
			var filePath = "<?php print $filePath ?>";
			window.location.href = filePath;
		</script>
        <?php
	}
	else{	
		?>
        	<script type="text/javascript">
				var patient_id = "<?php print $patient_id; ?>";
				var sch_id = "<?php print $sch_id?>";
				var vip_check="<?php echo($_REQUEST['vip_alert_post']); ?>";
				var file_path = "check_in_out_payment.php?search=true&frm_status=show_check_out&ci_pid="+patient_id+"&sch_id="+sch_id+"&vip_check="+vip_check;
				window.location.href = file_path;
			</script>
        <?php
//		header("location: check_in_out_payment.php?search=true&frm_status=show_check_out&ci_pid=$patient_id&sch_id=$sch_id");
	}
}

$patient_id = isset($_REQUEST['ci_pid']) ? $_REQUEST['ci_pid'] : $_REQUEST['patient_id'];
if(!$bool_save_payments){$visit_payment_readonly=' disabled';}else{$visit_payment_readonly='';}
//$objManageData->Smarty->assign("visit_payment_readonly",$visit_payment_readonly);
//--- SET IMAGE FOLDER PATH ---
//$objManageData->Smarty->assign("webroot",$webroot);

//$objManageData->Smarty->assign("sch_id",$sch_id);
//$objManageData->Smarty->assign("patient_id",$patient_id);

//--- CHECK OUT DATA ----

//--- GET CURRENT CASE ID AND DATE OF SERVICE FROM APPOITMENT TABLE ---
if(trim($sch_id) != ""){
	$schedule_query = "select schedule_appointments.sa_app_start_date, schedule_appointments.case_type_id,schedule_appointments.rte_id
						from schedule_appointments where schedule_appointments.id = '$sch_id'";
	$schQryRes = get_data_array($schedule_query);
	$sa_app_start_date = $schQryRes[0]['sa_app_start_date'];
	$current_caseid = $schQryRes[0]['case_type_id'];
	$sch_rte_id = $schQryRes[0]['rte_id'];
}

//---- SET PATIENT NAME ----
$pat_query = "select lname,fname,mname,vip from patient_data where id = '$patient_id'";
$patQryRes = get_data_array($pat_query);
$patient_name_arr = array();
$patient_name_arr["LAST_NAME"] = $patQryRes[0]["lname"];
$patient_name_arr["FIRST_NAME"] = $patQryRes[0]["fname"];
$patient_name_arr["MIDDLE_NAME"] = $patQryRes[0]["mname"];
$patient_vip=$patQryRes[0]["vip"];
$patient_name = changeNameFormat($patient_name_arr);
$patient_name .= ' - '.$patient_id;
//$objManageData->Smarty->assign("patient_name",$patient_name);
//$objManageData->Smarty->assign("vip_status",$patient_vip);

//--- PATIENT TOTAL DUE AMOUNT ----
$patDueQry = "select sum(patientDue) as patientDue,sum(totalBalance) as pat_totalBalance,sum(overPayment) as pat_overPayment from patient_charge_list where del_status='0' and patient_id = '$patient_id' and date_of_service<'".$sa_app_start_date."'";
$patDueQryRes = get_data_array($patDueQry);
$pat_totalBalance=$patDueQryRes[0]['pat_totalBalance'];
$pat_overPayment=$patDueQryRes[0]['pat_overPayment'];
$pat_totalBalance_final=$patDueQryRes[0]['pat_totalBalance']-$patDueQryRes[0]['pat_overPayment'];

//--- PATIENT Today Paid Amount ----
$pat_tot_today_pay_arr=array();
$pat_tot_today_pay_copay_arr=array();
$pat_tot_today_pay_ref_arr=array();

$patPaidQry = "select patient_charge_list_details.procCode,patient_charge_list.encounter_id,patient_charge_list_details.charge_list_detail_id
				from patient_charge_list 
				join patient_charge_list_details on patient_charge_list_details.charge_list_id=patient_charge_list.charge_list_id 
				where patient_charge_list_details.del_status='0' and patient_charge_list.patient_id = '$patient_id' and patient_charge_list.date_of_service='".$sa_app_start_date."'";
$patPaidRes = get_data_array($patPaidQry);
for($i=0;$i<count($patPaidRes);$i++){
	$pat_enc_arr[$patPaidRes[$i]['encounter_id']]=$patPaidRes[$i]['encounter_id'];
	$pat_enc_procCode_arr[$patPaidRes[$i]['charge_list_detail_id']]=$patPaidRes[$i]['procCode'];
}
$pat_enc_imp=implode(',',$pat_enc_arr);
$patPaidQry = "select patient_charges_detail_payment_info.paidForProc,patient_charges_detail_payment_info.charge_list_detail_id
				from patient_chargesheet_payment_info 
				join patient_charges_detail_payment_info on patient_charges_detail_payment_info.payment_id=patient_chargesheet_payment_info.payment_id
				where patient_charges_detail_payment_info.deletePayment='0' and patient_chargesheet_payment_info.paid_by='Patient'
				and patient_chargesheet_payment_info.encounter_id in($pat_enc_imp)";
$patPaidRes = get_data_array($patPaidQry);
for($i=0;$i<count($patPaidRes);$i++){
	$pat_tot_today_pay_copay_arr[$patPaidRes[$i]['charge_list_detail_id']][]=$patPaidRes[$i]['paidForProc'];
	if($patPaidRes[$i]['charge_list_detail_id']>0){
		$pat_tot_today_pay_ref_arr[$pat_enc_procCode_arr[$patPaidRes[$i]['charge_list_detail_id']]][]=$patPaidRes[$i]['paidForProc'];
	}
	$pat_tot_today_pay_arr[]=$patPaidRes[$i]['paidForProc'];
}
$pat_tot_today_pay=array_sum($pat_tot_today_pay_arr);
$pat_tot_today_copay_pay=array_sum($pat_tot_today_pay_copay_arr[0]);
// REFRACTION CHECK
$polcies_query = "select count(show_check_in) as rowCount,refraction,secondary_copay,sec_copay_collect_amt,sec_copay_for_ins, medicare_co_ins,billing_amount  from copay_policies";
$polciesQryRes = get_data_array($polcies_query);
$refractionChk = $polciesQryRes[0]['refraction'];
$sec_copay_collect_amt = $polciesQryRes[0]['sec_copay_collect_amt'];
$sec_copay_for_ins = $polciesQryRes[0]['sec_copay_for_ins'];
$medicare_co_ins = $polciesQryRes[0]['medicare_co_ins'];
$billing_amount = $polciesQryRes[0]['billing_amount'];
$sup_bill_qry="select superbill.physicianId,procedureinfo.cptCode
			from superbill join procedureinfo on procedureinfo.idSuperBill=superbill.idSuperBill
			where superbill.del_status ='0' AND procedureinfo.delete_status ='0' AND superbill.dateOfService ='$sa_app_start_date'";
$supDueQryRes = get_data_array($sup_bill_qry);
for($i=0;$i<count($supDueQryRes);$i++){
	if($supDueQryRes[$i]['cptCode']=='92015'){
		$ref_proc=$supDueQryRes[$i]['cptCode'];
	}
	$sup_phy=$supDueQryRes[$i]['physicianId'];
}
$getRefChkStr = imw_query("Select id,user_type,collect_refraction  FROM users 
						WHERE id ='$sup_phy'");
$usr_detail=imw_fetch_array($getRefChkStr);
$user_type=$usr_detail['user_type'];
$collect_refraction=$usr_detail['collect_refraction'];
if($refractionChk=='No'){
	if($collect_refraction>0){
		$refractionChk='yes';
	}
}


//--- GET CHECK IN FIELDS ----
$check_in_fields_qry = "select * from check_in_out_fields where item_name != '' and item_show > 0";
$checkInFieldsQryRes = get_data_array($check_in_fields_qry);
$itemNameArr = array();
for($i=0;$i<count($checkInFieldsQryRes);$i++){
	$itemNameArr[] = "'".$checkInFieldsQryRes[$i]['item_name']."'";
	$itemIdNameArr[$checkInFieldsQryRes[$i]['id']] = $checkInFieldsQryRes[$i]['item_name'];
	$itemNameIdArr[$checkInFieldsQryRes[$i]['item_name']] = $checkInFieldsQryRes[$i]['id'];
}
$itemNameStr = join(',',$itemNameArr);

$procAmtArr = array();

$chk_sel_dilated = 0;
$sel_dilation=imw_query("SELECT dia_id FROM chart_dialation WHERE patient_id='$patient_id' limit 0,1");
if(imw_num_rows($sel_dilation)>0){
	$chk_sel_dilated=1;
}

//--- GET INSURANCE COMPANY DETAILS ---
$ins_com_query = "select insurance_data.id as insurance_dataId, insurance_companies.in_house_code, insurance_companies.name, insurance_data.copay, insurance_companies.collect_copay,
					insurance_data.copay_type,insurance_data.type,insurance_companies.id as ins_comp_id,insurance_data.co_ins,insurance_companies.FeeTable
			from insurance_data left join insurance_companies on insurance_companies.id = insurance_data.provider
			where insurance_data.ins_caseid = '$current_caseid' and insurance_data.pid = '$patient_id'
			and insurance_data.actInsComp = '1' and insurance_data.type != 'tertiary' and insurance_data.provider > 0";
$insQryRes = get_data_array($ins_com_query);
$insurance_dataId = "";
$insDataArr = array();
$copayAmtArr = array();
$collect_copay_test = false;
$collect_copay_test_amt = array();
$copayAmtDilatedArr = array();
$copayAmtNonDilatedArr = array();
$collect_copay_dilated_test_amt = array();
$collect_copay_non_dilated_test_amt = array();
$real_time_data = "";
for($i=0;$i<count($insQryRes);$i++){
	$co_ins_pat = $insQryRes[$i]['co_ins'];
	
	$ins_name = trim($insQryRes[$i]['in_house_code']);
	if($ins_name == ''){
		$ins_name = $insQryRes[$i]['name'];
	}
	$insDataArr[$insQryRes[$i]['type']]['FeeTable'] = $insQryRes[$i]['FeeTable'];
	//$copayAmtArr[] = $insQryRes[$i]['copay'];
	if(strtolower($insQryRes[$i]['type']) == 'primary'){
		$primaryInsurnaceName = $ins_name;
		$primaryInsuranceCoId=$insQryRes[$i]['ins_comp_id'];
		$rte_ins_copay[]=$insQryRes[$i]['copay'];
		$insurance_dataId = $insQryRes[$i]['insurance_dataId'];
	}
	if(strtolower($insQryRes[$i]['type']) == 'secondary'){
		$SecondaryInsurnaceName = $ins_name;
		$secondaryInsuranceCoId=$insQryRes[$i]['ins_comp_id'];
	}
	if(strtolower($insQryRes[$i]['type']) == 'primary'){
		if($insQryRes[$i]['copay_type']>0){
			if($insQryRes[$i]['copay_type']==2){
				if($user_type==1 ||$user_type==12){
					$copayAmtArr = explode('/',$insQryRes[$i]['copay']);	
					$copayAmtDilatedArr[] = $copayAmtArr[0];	
					//$copayAmtNonDilatedArr[] = $copayAmtArr[1];
				}
			}else{
				$copayAmtArr = explode('/',$insQryRes[$i]['copay']);	
				$copayAmtDilatedArr[] = $copayAmtArr[0];	
				$copayAmtNonDilatedArr[] = $copayAmtArr[1];
			}
		}else{
			$copayAmtArr = explode('/',$insQryRes[$i]['copay']);	
			$copayAmtDilatedArr[] = $copayAmtArr[0];
		}
	}
	if(strtolower($insQryRes[$i]['type']) == 'secondary'){
		$secCopay=ChkSecCopay_collect($primaryInsuranceCoId);
		if($secCopay == 'Yes'){
			if($sec_copay_collect_amt>=$insQryRes[$i]['copay'] || $sec_copay_for_ins==''){
				$rte_ins_copay[]=$insQryRes[$i]['copay'];
				if($insQryRes[$i]['copay_type']>0){
					if($insQryRes[$i]['copay_type']==2){
						if($user_type==1 || $user_type==12){
							$copayAmtArr = explode('/',$insQryRes[$i]['copay']);	
							$copayAmtDilatedArr[] = $copayAmtArr[0];	
							//$copayAmtNonDilatedArr[] = $copayAmtArr[1];
						}
					}else{
						$copayAmtArr = explode('/',$insQryRes[$i]['copay']);	
						$copayAmtDilatedArr[] = $copayAmtArr[0];	
						$copayAmtNonDilatedArr[] = $copayAmtArr[1];
					}
				}else{
					$copayAmtArr = explode('/',$insQryRes[$i]['copay']);	
					$copayAmtDilatedArr[] = $copayAmtArr[0];
				}
			}
		}
	}
	if($insQryRes[$i]['collect_copay'] == 1){
		if($insQryRes[$i]['copay_type']>0){
			if($insQryRes[$i]['copay_type']==2){
				if($user_type==5 || $chk_sel_dilated>0){
					$collect_copay_test = true;
					//$collect_copay_test_amt[] = $insQryRes[$i]['copay'];
					$collect_copay_test_amt = explode('/',$insQryRes[$i]['copay']);	
					$collect_copay_non_dilated_test_amt[] = $collect_copay_test_amt[1];
				}
			}else{
				$collect_copay_test = true;
				//$collect_copay_test_amt[] = $insQryRes[$i]['copay'];
				$collect_copay_test_amt = explode('/',$insQryRes[$i]['copay']);	
				$collect_copay_dilated_test_amt[] = $collect_copay_test_amt[0];	
				$collect_copay_non_dilated_test_amt[] = $collect_copay_test_amt[1];
			}
		}else{
			$collect_copay_test = true;
			//$collect_copay_test_amt[] = $insQryRes[$i]['copay'];
			$collect_copay_test_amt = explode('/',$insQryRes[$i]['copay']);	
			$collect_copay_non_dilated_test_amt[] = $collect_copay_test_amt[0];	
		}
	}
$real_time_data="YES";
}
//$objManageData->Smarty->assign('SecondaryInsurnaceName', $SecondaryInsurnaceName);
//$objManageData->Smarty->assign('primaryInsurnaceName', $primaryInsurnaceName);
$totalCopayDilatedAmt = array_sum($copayAmtDilatedArr);
$totalCopayNonDilatedAmt = array_sum($copayAmtNonDilatedArr);

$totalCopayTestDilatedAmt = array_sum($collect_copay_dilated_test_amt);
$totalCopayTestNonDilatedAmt = array_sum($collect_copay_non_dilated_test_amt);

//$totalCopayAmt = array_sum($copayAmtArr);




// RTE DATA 
	if(constant("ENABLE_REAL_ELIGILIBILITY") == "YES" && $real_time_data=="YES"){
		$vsStatus = $vsTran = "";
		$qryGetRTMEid = "select rte_id, sa_app_start_date from schedule_appointments where id = '".$sch_id."' LIMIT 1";
		$resGetRTMEid = imw_query($qryGetRTMEid);
		if(imw_num_rows($resGetRTMEid) > 0){
							$valGetRTMEid = imw_fetch_array($resGetRTMEid);
							$intRTMEid = $valGetRTMEid[0];
							$strAppDate = $valGetRTMEid[1];
							$qryGetRealTimeData = "select DATE_FORMAT(rtme.responce_date_time, '%m-%d-%Y %I:%i %p') as vs270RespDate, response_deductible, response_copay, response_co_insurance, DATE_FORMAT(rtme.responce_date_time, '%m-%d-%Y') as vsRespDate, 
													rtme.transection_error as vsTransectionError, 
													rtme.EB_responce as vsEBLoopResp, CONCAT_WS('',SUBSTRING(us.fname,1,1),SUBSTRING(us.mname,1,1), SUBSTRING(us.lname,1,1)) as elOpName
													from real_time_medicare_eligibility rtme LEFT JOIN users us on us.id = rtme.request_operator
													where rtme.id = '".$intRTMEid."' order by responce_date_time desc limit 1
												";
							$rsGetRealTimeData = imw_query($qryGetRealTimeData);
							if($rsGetRealTimeData){
								if(imw_num_rows($rsGetRealTimeData)>0){
									$rowGetRealTimeData = imw_fetch_object($rsGetRealTimeData);
									$dbRespDDC = $rowGetRealTimeData->response_deductible;
									$arrRespDDC = explode("-", $dbRespDDC);
									$intDDCAmt = (int)$arrRespDDC[4];
									
									$dbRespCopay = $rowGetRealTimeData->response_copay;
									$arrRespCopay = explode("-", $dbRespCopay);
									$intCopayAmt = (float)$arrRespCopay[4];
									
									$dbRespCoIns = $rowGetRealTimeData->response_co_insurance;
									$arrRespCoIns = explode("-", $dbRespCoIns);
									$strCoInsAmt = $arrRespCoIns[6];
								}
							}
							$vsToolTip = $vsStatusDate = $strEBResponce = $imgRealTimeEli = "";																
							if(($rowGetRealTimeData->vs270RespDate != "00-00-0000") && ($rowGetRealTimeData->vs270RespDate != "")){
								$vsToolTip = "Last Transaction on: ".$rowGetRealTimeData->vs270RespDate."\n";
								$vsStatusDate = $rowGetRealTimeData->vs270RespDate;
							}
							else{
								$imgRealTimeEli = "<a id=\"anchorEligibility\" href=\"javascript:void(0);\" class=\"text_10b_purpule\" title=\"$vsToolTip\" onclick=\"getRealTimeEligibility('$insurance_dataId');\">";
								$imgRealTimeEli .= "<img id=\"imgEligibility\" src=\"../../../library/images/eligibility_blue.png\" border=\"0\" title=\"Realtime Medicare Eligibility Request\" />";
								$imgRealTimeEli .= "</a>&nbsp;";			
							}	
												
							if($rowGetRealTimeData->vsTransectionError != ""){
								$vsToolTip .= $rowGetRealTimeData->vsTransectionError;
								$vsStatus = "Error: ".$vsStatusDate."<br>BY: ".$rowGetRealTimeData->elOpName;
								$vsTran = "error";
								$imgRealTimeEli = "<a id=\"anchorEligibility\" href=\"javascript:void(0);\" class=\"text_10b_purpule\" title=\"$vsToolTip\" onclick=\"getRealTimeEligibility('$insurance_dataId');\">";
								$imgRealTimeEli .= "<img id=\"imgEligibility\" src=\"../../../library/images/eligibility_red.png\" border=\"0\"/>";
								$imgRealTimeEli .= "</a>&nbsp;";
							}
							elseif($rowGetRealTimeData->vsEBLoopResp != ""){									
								$strEBResponce = $objCoreLang->get_vocabulary("vision_share_271", "EB", (string)trim($rowGetRealTimeData->vsEBLoopResp));
								$vsToolTip .= "Status: ".$strEBResponce;
								$vsStatus = $strEBResponce.": ".$vsStatusDate."<br>BY: ".$rowGetRealTimeData->elOpName;
								$vsTran = "sucss";
								if(($rowGetRealTimeData->vsEBLoopResp != "6") && ($rowGetRealTimeData->vsEBLoopResp != "7") && ($rowGetRealTimeData->vsEBLoopResp != "8") && ($rowGetRealTimeData->vsEBLoopResp != "V")){
									$imgRealTimeEli = "<a id=\"anchorEligibility\" href=\"javascript:void(0);\" class=\"text_10b_purpule\" title=\"$vsToolTip\" onclick=\"getRealTimeEligibility('$insurance_dataId');\">";
									$imgRealTimeEli .= "<img id=\"imgEligibility\" src=\"../../../library/images/eligibility_green.png\" border=\"0\"/>";
									$imgRealTimeEli .= "</a>&nbsp;";
								}
								else{
									$imgRealTimeEli = "<a id=\"anchorEligibility\" href=\"javascript:void(0);\" class=\"text_10b_purpule\" title=\"$vsToolTip\" onclick=\"getRealTimeEligibility('$insurance_dataId');\">";
									$imgRealTimeEli .= "<img id=\"imgEligibility\" src=\"../../../library/images/eligibility_red.png\" border=\"0\"/>";
									$imgRealTimeEli .= "</a>&nbsp;";
								}
							}
							
							
							// Get detail of RTE deductible and Co-Insurance
							$daysBetween = 0;
							$intDDCAmtPre = $intCopayAmtPre = "";
							$strCoInsAmtPre = "";
							$rtme_id = $rte_days = "";
							$qryGetRTEAmountPrevious = "select rtme.id,rtme.response_deductible, rtme.response_copay, rtme.response_co_insurance,DATE_FORMAT(rtme.responce_date_time, '%Y-%m-%d') as respDate
													from real_time_medicare_eligibility rtme LEFT JOIN users us on us.id = rtme.request_operator
													where rtme.patient_id = '".$patient_id."'
													and rtme.ins_data_id = '".$insurance_dataId."' 
													and rtme.EB_responce != '' and rtme.transection_error = ''
													order by rtme.responce_date_time desc ";
							if($intRTMEid == 0){
								$qryGetRTEAmountPrevious .= "limit 1";
							}
							else{
								$qryGetRTEAmountPrevious .= "limit 0, 1";
							}
							$qryGetRTEAmountPrevious;
							$rsGetRTEAmountPrevious = imw_query($qryGetRTEAmountPrevious);
							
							if($rsGetRTEAmountPrevious){
								if(imw_num_rows($rsGetRTEAmountPrevious)>0){
									$rowGetRTEAmountPrevious = imw_fetch_object($rsGetRTEAmountPrevious);	
									if(empty($rowGetRTEAmountPrevious->id) == false){
										$rtme_id = $rowGetRTEAmountPrevious->id;
									}
									if(empty($rowGetRTEAmountPrevious->response_deductible) == false){			
										$dbRespDDCPre = $rowGetRTEAmountPrevious->response_deductible;
										$arrRespDDCPre = explode("-", $dbRespDDCPre);
										$intDDCAmtPre = (int)$arrRespDDCPre[4];
									}
									if(empty($rowGetRTEAmountPrevious->response_copay) == false){			
										$dbRespCopayPre = $rowGetRTEAmountPrevious->response_copay;
										$arrRespCopayPre = explode("-", $dbRespCopayPre);
										$intCopayAmtPre = (float)$arrRespCopayPre[4];
									}
									if(empty($rowGetRTEAmountPrevious->response_co_insurance) == false){			
										$dbRespCoInsPre = $rowGetRTEAmountPrevious->response_co_insurance;
										$arrRespCoInsPre = explode("-", $dbRespCoInsPre);
										$strCoInsAmtPre = $arrRespCoInsPre[6];
										/*if(substr($strCoInsAmtPre,0,1) == "."){
											$strCoInsAmtPre = str_replace(".","",$strCoInsAmtPre);
										} */
										$strCoInsAmtPre = (float)$strCoInsAmtPre * 100;
									}
									if((empty($rowGetRTEAmountPrevious->respDate) == false) && ((empty($intDDCAmtPre) == false) || (empty($intCopayAmtPre) == false) || (empty($strCoInsAmtPre) == false))){
										$startDate = strtotime($rowGetRTEAmountPrevious->respDate);
										$endDate = strtotime($strAppDate);
										$daysBetween = ceil(abs($endDate - $startDate) / 86400);
									}
									if((empty($rowGetRTEAmountPrevious->respDate) == false)){
										$startDate = strtotime($rowGetRTEAmountPrevious->respDate);
										$endDate = strtotime(date('Y-m-d'));
										$rte_days = ceil(abs($endDate - $startDate) / 86400);
									}
								}
							}
							
							
							// Get Rte Valid days Range
							$query = "select RTEValidDays from copay_policies";
							$result = imw_query($query) or die(imw_error);
							while($row = imw_fetch_array($result)){
								$valid_days  = $row["RTEValidDays"];
							}
							if($rte_days <= $valid_days){
								$date_remain = "YES";
							}else{
								$date_remain = "NO";
							}
							//End Of Code
							//$objManageData->Smarty->assign("DAY_REMAIN", $date_remain);
							//$objManageData->Smarty->assign("IMGREALTIMEELI", $imgRealTimeEli);
							//$objManageData->Smarty->assign("RTME_ID", $rtme_id);
							//$objManageData->Smarty->assign("VSSTATUS", $vsStatus);
							//$objManageData->Smarty->assign("VSTRAN", $vsTran);
							//$objManageData->Smarty->assign("VSRESPDATECOMP", $rowGetRealTimeData->vsRespDate);
							//$objManageData->Smarty->assign("VSTOOLTIP", $vsToolTip);
							//$objManageData->Smarty->assign("REPORT_WINDOW", $_SESSION['wn_height'] - 140);
							//$objManageData->Smarty->assign("RTE_COPAY_AMT_PRE", $intCopayAmtPre);
							//$objManageData->Smarty->assign("RTE_DDC_AMT_PRE", $intDDCAmtPre);
							//$objManageData->Smarty->assign("RTE_COINS_AMT_PRE", $strCoInsAmtPre);
							
							##
			}
	}

//END OF RTE DATA



$fee_table_column_id=1;
if($insDataArr["primary"]["FeeTable"]>1){
	$fee_table_column_id=$insDataArr["primary"]["FeeTable"];
}


//--- GET REFRACTION AMOUNT ----
$ref_query = "select cpt_fee_table.cpt_fee,cpt_fee_table.cpt_fee_id from cpt_fee_tbl 
			join cpt_fee_table on cpt_fee_tbl.cpt_fee_id = cpt_fee_table.cpt_fee_id
			where cpt_fee_table.fee_table_column_id = '$fee_table_column_id'
			and cpt_fee_tbl.cpt4_code = '92015' AND cpt_fee_tbl.delete_status = '0'";
$refQryRes = get_data_array($ref_query);
$procAmtArr["Refraction"] = $refQryRes[0]['cpt_fee'];
if($refQryRes[0]['cpt_fee_id']>0){
	$pat_tot_today_pay_ref=array_sum($pat_tot_today_pay_ref_arr[$refQryRes[0]['cpt_fee_id']]);
}

$clws_id=0;
//--- GET Contact  lens ----
$GetCLDataQuery= "SELECT clws_id,clws_type,cpt_evaluation_fit_refit 
				FROM contactlensmaster WHERE 
				DATE_FORMAT(`clws_savedatetime`,'%Y-%m-%d') ='$sa_app_start_date'
				and patient_id='$patient_id' and 
				(clws_type LIKE '%Fit%' OR clws_type LIKE '%Refit%' OR clws_type LIKE '%Evaluation%') limit 0,1";
				$GetCLDataRes = imw_query($GetCLDataQuery) or die(imw_error()); 
				$GetCLDataNumRow = imw_num_rows($GetCLDataRes);
				$GetCLDataRow=@imw_fetch_assoc($GetCLDataRes);
				$procAmtArr["Contact lens"] = str_replace($GLOBALS['currency'],"",$GetCLDataRow["cpt_evaluation_fit_refit"]);
				$procAmtArr["Contact Lens Supply"] = str_replace($GLOBALS['currency'],"",$GetCLDataRow["cpt_evaluation_fit_refit"]);
				$clws_id=$GetCLDataRow["clws_id"];

//---- GET CONTACT LENS SUPPLY AMOUNT
$cl_supply_amt=0;
if($clws_id>0){
	$qry="Select clSupply FROM clprintorder_master WHERE clws_id='".$clws_id."' ORDER BY print_order_id DESC LIMIT 0,1";
	$rs=imw_query($qry);
	$res=imw_fetch_assoc($rs);
	$cl_supply_amt=$res['clSupply'];
	unset($rs);
}

//--- GET PROCEDURE AMOUNT ---
$procQuery = "select cpt_fee_table.cpt_fee, cpt_fee_tbl.cpt_prac_code, cpt_fee_tbl.cpt_desc, cpt_fee_table.cpt_fee_id,cpt_fee_tbl.not_covered
			 from cpt_fee_tbl join cpt_fee_table on cpt_fee_tbl.cpt_fee_id = cpt_fee_table.cpt_fee_id
			 where cpt_fee_table.fee_table_column_id = '$fee_table_column_id' and cpt_fee_tbl.cpt_prac_code != '92015'
			 and (cpt_fee_tbl.cpt_desc in($itemNameStr) or cpt_fee_tbl.cpt_prac_code in($itemNameStr) or cpt_fee_tbl.not_covered='1') 
 			AND cpt_fee_tbl.delete_status = '0'";
$procQryRes = get_data_array($procQuery);

for($i=0;$i<count($procQryRes);$i++){
	$cpt_desc = "'".$procQryRes[$i]['cpt_desc']."'";
	if(in_array($cpt_desc,$itemNameArr) === true){
		$itemName = preg_replace('/\'/','',$cpt_desc);
	}
	else{
		$itemName = $procQryRes[$i]['cpt_prac_code'];
	}
	$procAmtArr[$itemName] = $procQryRes[$i]['cpt_fee'];
	if($procQryRes[$i]['not_covered']>0){
		$NCprocAmtArr[$procQryRes[$i]['cpt_prac_code']] = $procQryRes[$i]['not_covered'];
	}
}

//--- SET TOTAL PAYMENTS ---
$pay_query = "select check_in_out_payment.payment_id as main_pay_id, check_in_out_payment.total_payment, 
			check_in_out_payment.payment_method,check_in_out_payment.check_no,check_in_out_payment.co_comment,
			check_in_out_payment.cc_type,check_in_out_payment.cc_no , check_in_out_payment.cc_expire_date, 
			check_in_out_payment_details.item_id, check_in_out_payment_details.item_payment,
			check_in_out_payment_details.payment_type,check_in_out_payment_details.id as pay_detail_id,
			check_in_out_payment_details.copay_type as copay_type
			from check_in_out_payment join check_in_out_payment_details on 
			check_in_out_payment_details.payment_id = check_in_out_payment.payment_id
			where check_in_out_payment.patient_id = '$patient_id' and check_in_out_payment.sch_id = '$sch_id'
			and check_in_out_payment.del_status = '0'
			and check_in_out_payment_details.status = '0'
			and check_in_out_payment_details.payment_type='checkout'";
$paymentQryRes = get_data_array($pay_query);

$pay_query_ci = "select check_in_out_payment.payment_id as main_pay_id, check_in_out_payment.total_payment, 
			check_in_out_payment.payment_method,check_in_out_payment.check_no,check_in_out_payment.created_on,
			check_in_out_payment.created_time,check_in_out_payment.created_by,
			check_in_out_payment.cc_type,check_in_out_payment.cc_no , check_in_out_payment.cc_expire_date, 
			check_in_out_payment_details.item_id, check_in_out_payment_details.item_payment,
			check_in_out_payment_details.payment_type,check_in_out_payment_details.id as pay_detail_id,
			check_in_out_payment_details.copay_type as copay_type
			from check_in_out_payment join check_in_out_payment_details on 
			check_in_out_payment_details.payment_id = check_in_out_payment.payment_id
			where check_in_out_payment.patient_id = '$patient_id' and check_in_out_payment.sch_id = '$sch_id'
			and check_in_out_payment.del_status = '0'
			and check_in_out_payment_details.status = '0'
			and check_in_out_payment_details.payment_type='checkin'";
$paymentQryRes_ci = get_data_array($pay_query_ci);

$printRecieptBtn = 'none';
if(count($paymentQryRes) > 0){
	$printRecieptBtn = 'inline';
}
//$objManageData->Smarty->assign('printRecieptBtn',$printRecieptBtn);

$total_payment_arr = array(0.00);
$total_payment_ci_arr = array(0.00);
$total_payment_co_arr = array(0.00);
$checkInPayArr = array();



$payment_select = $paymentQryRes[0]["payment_method"];
$main_pay_id = $paymentQryRes[0]["main_pay_id"];
$payment_chk_number = $paymentQryRes[0]["check_no"];
$co_comment = $paymentQryRes[0]["co_comment"];

if(count($paymentQryRes)==0){
	$comm_qry=imw_query("select co_comment from check_in_out_payment where patient_id = '$patient_id' and sch_id = '$sch_id' and payment_type='checkout' limit 0,1");
	$comm_row=imw_fetch_array($comm_qry);
	$co_comment = $comm_row['co_comment'];
}

//$objManageData->Smarty->assign("edit_payment_tbl_id",$main_pay_id);
//$objManageData->Smarty->assign("check_out_comment",$co_comment);

//--- SET CREDIT CARD DROP DOWN ---
$cr_name_arr = array(""=>"");
$cr_name_arr["AX"] = "American Express";
$cr_name_arr["Care Credit"] = "Care Credit";
$cr_name_arr["Dis"] = "Discover";
$cr_name_arr["MC"] = "Master Card";
$cr_name_arr["Visa"] = "Visa";
$cr_name_arr["Others"] = "Others";
//$objManageData->Smarty->assign("cr_options",$cr_name_arr);

for($i=0;$i<count($paymentQryRes_ci);$i++){
	$item_id = $paymentQryRes_ci[$i]['item_id'];
	$main_pay_id = $paymentQryRes_ci[$i]['main_pay_id'];
	$total_payment_arr[$main_pay_id] = $paymentQryRes_ci[$i]["total_payment"];
	$total_payment_ci_arr[] = $paymentQryRes_ci[$i]["item_payment"];
	$checkInPayArr[$item_id]['checkin'] = $paymentQryRes_ci[$i];
	$checkInPayArr_acc[$itemIdNameArr[$item_id]][] = $paymentQryRes_ci[$i]["item_payment"];
	
}
if(array_sum($checkInPayArr_acc['Copay-visit'])>0){
	$pat_tot_today_pay = $pat_tot_today_pay-$pat_tot_today_copay_pay;
}else{
	$totalCopayDilatedAmt = $totalCopayDilatedAmt-$pat_tot_today_copay_pay;
}
if(array_sum($checkInPayArr_acc['Refraction'])>0){
	$pat_tot_today_pay = $pat_tot_today_pay-$pat_tot_today_pay_ref;
}

$selected_pay_method = array();
for($i=0;$i<count($paymentQryRes);$i++){
	$item_id = $paymentQryRes[$i]['item_id'];
	$main_pay_id = $paymentQryRes[$i]['main_pay_id'];
	$total_payment_arr[$main_pay_id] = $paymentQryRes[$i]["total_payment"];
	if($paymentQryRes[$i]['payment_type']=='checkout'){
		$total_payment_co_arr[] = $paymentQryRes[$i]["item_payment"];
	}else{
		$total_payment_ci_arr[] = $paymentQryRes[$i]["item_payment"];
	}
	$checkInPayArr[$item_id][$paymentQryRes[$i]['payment_type']] = $paymentQryRes[$i];
	$selected_pay_method[$paymentQryRes[$i]["item_id"]] = $paymentQryRes[$i]["payment_method"];
}//pre($selected_pay_method,1);

/*----*/
$checkRow = "none";
$creditCardRow = "none";

$query_get_saved_pay_types = "SELECT payment_id, total_payment, payment_method, check_no, cc_type, cc_no, cc_expire_date 
					FROM check_in_out_payment 
					WHERE patient_id='$patient_id' and sch_id='$sch_id' 
					AND del_status = '0' 
					AND payment_type='checkout'";
$res_get_saved_pay_types = imw_query($query_get_saved_pay_types);
if($res_get_saved_pay_types && imw_num_rows($res_get_saved_pay_types)>0){
	while($rs_get_saved_pay_types = imw_fetch_array($res_get_saved_pay_types)){
		if($rs_get_saved_pay_types['payment_method']=='Cash'){
			
			$edit_payment_tbl_id_cash = $rs_get_saved_pay_types['payment_id'];
		}else if($rs_get_saved_pay_types['payment_method']=='Check'){
			
			$payment_chk_number = $rs_get_saved_pay_types['check_no'];
			$edit_payment_tbl_id_check = $rs_get_saved_pay_types['payment_id'];
			if($rs_get_saved_pay_types['total_payment'] != '' && $rs_get_saved_pay_types['total_payment'] != '0.00'){$checkRow = "inline-block";}
		
		}else if($rs_get_saved_pay_types['payment_method']=='Credit Card'){
			$creditCardNumber = $rs_get_saved_pay_types['cc_no'];
			$creditCardDate = $rs_get_saved_pay_types["cc_expire_date"];
			$cr_selected = $rs_get_saved_pay_types["cc_type"];
			$edit_payment_tbl_id_card = $rs_get_saved_pay_types['payment_id'];
			if($rs_get_saved_pay_types['total_payment'] != '' && $rs_get_saved_pay_types['total_payment'] != '0.00'){$creditCardRow = "inline-table";}
		
		}else if($rs_get_saved_pay_types['payment_method']=='EFT'){
			$payment_chk_number = $rs_get_saved_pay_types['check_no'];
			$edit_payment_tbl_id_eft = $rs_get_saved_pay_types['payment_id'];
			if($rs_get_saved_pay_types['total_payment'] != '' && $rs_get_saved_pay_types['total_payment'] != '0.00'){$checkRow = "inline-block";}
		}else if($rs_get_saved_pay_types['payment_method']=='Money Order'){
			$payment_chk_number = $rs_get_saved_pay_types['check_no'];
			$edit_payment_tbl_id_mo = $rs_get_saved_pay_types['payment_id'];
			if($rs_get_saved_pay_types['total_payment'] != '' && $rs_get_saved_pay_types['total_payment'] != '0.00'){$checkRow = "inline-block";}
		}
	}
}
//$objManageData->Smarty->assign("checkRow",$checkRow);
//$objManageData->Smarty->assign("creditCardRow",$creditCardRow);
/*----*/
//--- SET TOTAL CHARGES ---
$total_today_deduct='0';
$total_today_copay = '0';
$today_charges = $not_covered_charges = array();	
$supBillQuery = "SELECT gro_id,idSuperBill,insuranceCaseId,procOrder, todaysCharges as supBillCharges,pri_copay,sec_copay FROM superbill WHERE dateOfService='".$sa_app_start_date."' AND patientId='".$patient_id."' AND del_status='0' AND postedStatus = '0'";
$supBillResult = imw_query($supBillQuery);
$totalVisitCharges = '0.00';
//$supBillRs = imw_fetch_array($supBillResult);
$total_group = imw_num_rows($supBillResult);
if($supBillResult && imw_num_rows($supBillResult)>0){
	
	while($supBillRow = imw_fetch_array($supBillResult)){
		if($supBillRow['supBillCharges'] != NULL){
			
					// Calculate Total Allowable Charges From SuperBill
					$getPrimaryInsCoStr = "SELECT provider FROM insurance_data										
				  							WHERE pid = '$patient_id'
											AND type = 'primary'
											AND ins_caseid = '".$supBillRow["insuranceCaseId"]."'";
					$getPrimaryInsCoQry = imw_query($getPrimaryInsCoStr);
					$getPrimaryInsCoRow = imw_fetch_assoc($getPrimaryInsCoQry);
					$pInsId = $getPrimaryInsCoRow['provider'];			

				$procedurePracCode = $supBillRow["procOrder"];
				$idSuperBill = $supBillRow["idSuperBill"];
				$arr_procedurePracCode = explode(',', $procedurePracCode);
				$pracCodeCharges = "0";
				$qry_unit = "select id,cptCode,units from procedureinfo where idSuperBill='$idSuperBill' AND delete_status='0' ";
				$sql_unit = imw_query($qry_unit) or die(imw_error());
				while($row_unit = imw_fetch_array($sql_unit)){
					$CptUnit = $row_unit["units"];
					$SupCptCode = $row_unit["cptCode"];
					$pracCodePrize = getContractFee($SupCptCode,$pInsId);
					$pracCodeCharges +=  ($pracCodePrize*$CptUnit);
					if($NCprocAmtArr[$SupCptCode]>0){
						$contract_price=$pracCodePrize;
						if($billing_amount=='Default'){
							$qry_cpt = imw_query("select cpt_fee_table.cpt_fee from cpt_fee_tbl
							join cpt_fee_table on cpt_fee_table.fee_table_column_id = '1'
							where cpt_fee_tbl.cpt_prac_code='$SupCptCode'
							and cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id and cpt_fee_tbl.delete_status = '0'");
							$qry_feeRes1 = imw_fetch_assoc($qry_cpt);
							$contract_price = $qry_feeRes1['cpt_fee'];
							if(imw_num_rows($qry_cpt)==0){
								$qry_cpt = imw_query("select cpt_fee_table.cpt_fee from cpt_fee_tbl
								join cpt_fee_table on cpt_fee_table.fee_table_column_id = '1'
								where (cpt_fee_tbl.cpt4_code='$SupCptCode' OR cpt_fee_tbl.cpt_desc='$SupCptCode')
								and cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id and cpt_fee_tbl.delete_status = '0'");
								$qry_feeRes1 = imw_fetch_assoc($qry_cpt);
								$contract_price = $qry_feeRes1['cpt_fee'];
							}
						}else{	
							$qry_ins = imw_query("select FeeTable from insurance_companies where id = '$pInsId'");
							$qry_feeRes = imw_fetch_assoc($qry_ins);
							$FeeTable = $qry_feeRes['FeeTable'];
							if($FeeTable<=0){
								$FeeTable=1;
							}
							$qry_cpt = imw_query("select cpt_fee_table.cpt_fee from cpt_fee_tbl
								join cpt_fee_table on cpt_fee_table.fee_table_column_id = '$FeeTable'
								where cpt_fee_tbl.cpt_prac_code='$SupCptCode'
								and cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id and cpt_fee_tbl.delete_status = '0'");
							$qry_feeRes1 = imw_fetch_assoc($qry_cpt);
							$contract_price = $qry_feeRes1['cpt_fee'];
							if(imw_num_rows($qry_cpt)==0){
								$qry_cpt = imw_query("select cpt_fee_table.cpt_fee from cpt_fee_tbl
								join cpt_fee_table on cpt_fee_table.fee_table_column_id = '$FeeTable'
								where (cpt_fee_tbl.cpt4_code='$SupCptCode' OR cpt_fee_tbl.cpt_desc='$SupCptCode')
								and cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id and cpt_fee_tbl.delete_status = '0'");
								$qry_feeRes1 = imw_fetch_assoc($qry_cpt);
								$contract_price = $qry_feeRes1['cpt_fee'];
							}
						}
						$not_covered_charges[$row_unit["id"]]=$contract_price*$CptUnit;
					}
				}
				
				//End Of Code To Calculate Allowable Charges 
				
				$today_charges["allowable_charges"][] = $pracCodeCharges;			
				$today_charges["gro_id"][] = $supBillRow["gro_id"];
				
				$totalVisitCharges += $pracCodeCharges;
				$total_allowable_charges += $pracCodeCharges;
				$total_today_copay +=  $supBillRow['pri_copay']+$supBillRow['sec_copay'];
			//	$secondaryInsurance_chld =  $secondaryInsuranceCoId;
		}
	
	}
}else{  
	
	/*$pclBillQuery = "SELECT gro_id, SUM(totalAmt) as pclBillCharges, sum(insuranceDue) as Insurance_Due, SUM(approvedTotalAmt) as allowable_charges,
					SUM(patientDue) as today_patientDue,sum(copay) as today_copay,
					sum(deductibleTotalAmt) as today_deduct,primaryInsuranceCoId,secondaryInsuranceCoId
					FROM patient_charge_list WHERE date_of_service='".$sa_app_start_date."' AND patient_id='".$patient_id."' group by gro_id";
	$pclBillResult = imw_query($pclBillQuery); 
	$total_group = imw_num_rows($pclBillResult);
	if($pclBillResult && imw_num_rows($pclBillResult)>0){
		while($pclBillRow = imw_fetch_array($pclBillResult)){
			$today_charges["Patient_Due"][] = $pclBillRow["today_patientDue"];
			$today_charges["Insurance_Due"][] = $pclBillRow["Insurance_Due"];
			$today_charges["allowable_charges"][] = $pclBillRow["allowable_charges"];
			$today_charges["gro_id"] = $pclBillRow["gro_id"];
				
		//	$pclBillRs = imw_fetch_array($pclBillResult);
			$totalVisitCharges +=  $pclBillRow['pclBillCharges'];
			$total_allowable_charges +=  $pclBillRow['allowable_charges'];
			//$total_today_patientDue =  $pclBillRs['today_patientDue'];
			$total_today_copay +=  $pclBillRow['today_copay'];
			//$total_today_deduct =  $pclBillRs['today_deduct'];
			$primaryInsurance_chld =  $pclBillRow['primaryInsuranceCoId'];
			$secondaryInsurance_chld =  $pclBillRow['secondaryInsuranceCoId'];	
		
		}
	}*/
	$pclBillQuery = "SELECT gro_id,charge_list_id,totalAmt as pclBillCharges, insuranceDue as Insurance_Due, approvedTotalAmt as allowable_charges,
					patientDue as today_patientDue,copay as today_copay,
					deductibleTotalAmt as today_deduct,primaryInsuranceCoId,secondaryInsuranceCoId
					FROM patient_charge_list WHERE del_status='0' and date_of_service='".$sa_app_start_date."' AND patient_id='".$patient_id."'";
	$pclBillResult = imw_query($pclBillQuery); 
	$total_group = imw_num_rows($pclBillResult);
	if($pclBillResult && imw_num_rows($pclBillResult)>0){
		while($pclBillRow = imw_fetch_array($pclBillResult)){
			$today_charges["Patient_Due"][] = $pclBillRow["today_patientDue"];
			$today_charges["Insurance_Due"][] = $pclBillRow["Insurance_Due"];
			$today_charges["gro_id"][] = $pclBillRow["gro_id"];
			
			$primaryInsurance_chld =  $pclBillRow['primaryInsuranceCoId'];
			$secondaryInsurance_chld =  $pclBillRow['secondaryInsuranceCoId'];	
			$charge_list_id =  $pclBillRow['charge_list_id'];	
			$pracCodeCharges=0;
			$chld_qry=imw_query("select procCode,units from patient_charge_list_details where del_status='0' and charge_list_id='$charge_list_id'");
			while($chld_row=imw_fetch_array($chld_qry)){
				$procCode =  $chld_row['procCode'];	
				$units =  $chld_row['units'];	
				$getCptFeeDetailsStr = "SELECT cpt_prac_code FROM cpt_fee_tbl WHERE cpt_fee_id = '$procCode' AND delete_status = '0'";
				$getCptFeeDetailsQry = imw_query($getCptFeeDetailsStr);
				$getCptFeeDetailsRow = imw_fetch_array($getCptFeeDetailsQry);
				$cptPracCode = $getCptFeeDetailsRow['cpt_prac_code'];
				
				$pracCodePrize = getContractFee($cptPracCode,$primaryInsurance_chld);
				$pracCodeCharges += $pracCodePrize*$units;
			}
			$today_charges["allowable_charges"][] = $pracCodeCharges;
			//$pclBillRs = imw_fetch_array($pclBillResult);
			$totalVisitCharges +=  $pracCodeCharges;
			$total_allowable_charges +=  $pracCodeCharges;
			//$total_today_patientDue =  $pclBillRs['today_patientDue'];
			$total_today_copay +=  $pclBillRow['today_copay'];
			//$total_today_deduct =  $pclBillRs['today_deduct'];
		}
	}
}
// TO Make an array of an Group Name 
$group_name = array();
$qry_group = "select gro_id , name from groups_new where del_status='0'";
$res_group = imw_query($qry_group) or die(imw_error());
while($row_group = imw_fetch_array($res_group)){
		$disp_name = $row_group["name"];
		$disp_name_arr = explode(' ', $disp_name);
		$disp_name="";
		for($i=0;$i<count($disp_name_arr);$i++){
			$disp_name .= substr($disp_name_arr[$i],0,1);
		}
		$group_name[$row_group["gro_id"]] = $disp_name;
}
//$objManageData->Smarty->assign("group_name", $group_name);

// Code For Previous Charges of Patient  
/*
$previous_data = array();
$supBillPreQuery = "SELECT sum(todaysCharges) as grp_todaysCharges,gro_id FROM superbill WHERE dateOfService<'".$sa_app_start_date."' AND patientId='".$patient_id."' AND postedStatus = '0' group by gro_id";
$supBillPreResult = imw_query($supBillPreQuery) or die(imw_error());
while($supBillPreRow = imw_fetch_array($supBillPreResult)){
	$previous_data[$supBillPreRow["gro_id"]][] = $supBillPreRow["grp_todaysCharges"];
}
*/


// Code For Previous Charges of Patient 
$previous_data = array();
$pclBillQuery = "SELECT sum(totalBalance) as grp_totalBalance,sum(patientDue) as grp_patientDue, sum(insuranceDue) as grp_insuranceDue, sum(overPayment) as grp_overPayment,gro_id
					FROM patient_charge_list WHERE del_status='0' and date_of_service<'".$sa_app_start_date."' AND patient_id='".$patient_id."'
					group by gro_id";
	$pclBillResult = imw_query($pclBillQuery);
	while($pclBillRs = imw_fetch_array($pclBillResult)){
		$previous_data[$pclBillRs["gro_id"]]["Patient_Due"][] = $pclBillRs["grp_patientDue"];
		$previous_data[$pclBillRs["gro_id"]]["Insurance_Due"][] = $pclBillRs["grp_insuranceDue"];
		$patient_total_due +=  $pclBillRs["grp_patientDue"];
		$total_credit_arr[]= $pclBillRs["grp_totalBalance"]-$pclBillRs["grp_overPayment"];
	}

$pclBillQuery = "SELECT SUM(patientDue) as prev_patientDue FROM patient_charge_list WHERE del_status='0' and date_of_service<'".$sa_app_start_date."' AND patient_id='".$patient_id."'";
$pclBillResult = imw_query($pclBillQuery); 
if($pclBillResult && imw_num_rows($pclBillResult)>0){
	$pclBillRs = imw_fetch_array($pclBillResult);
	$total_prev_patientDue =  $pclBillRs['prev_patientDue'];
}
if($date_remain != "YES"){
	$total_today_copay="0";
}
else{
	if($intCopayAmtPre>0){
		$total_today_copay =$intCopayAmtPre;
	}
}
if($total_today_copay>0){
	$total_today_copay=$total_today_copay;
}else{
	$total_today_copay=array_sum($rte_ins_copay);
}
if($date_remain != "YES"){
		$total_today_deduct="0";
	}
	else{
		if($intDDCAmtPre > $total_allowable_charges){
			$total_today_deduct=$total_allowable_charges;
		}
		else{
			$total_today_deduct=$intDDCAmtPre;
		}
	}
$copay_deduct_total=$total_today_copay+$total_today_deduct;
if($date_remain != "YES"){
		$co_ins_pat_exp=explode('/',$co_ins_pat);
		$visit_co_ins=$co_ins_pat_exp[1];
}
else{
	if($strCoInsAmtPre>0){
		$show_pt_co_ins = "show";
	//	if(substr($strCoInsAmtPre,0,1) == "."){
	//		$strCoInsAmtPre = str_replace(".","",$strCoInsAmtPre);
	//	}
		$visit_co_ins=$strCoInsAmtPre;
	}else{
		$show_pt_co_ins = "show";
		if($co_ins_pat != ""){	
			$co_ins_pat_exp=explode('/',$co_ins_pat);
			$visit_co_ins=$co_ins_pat_exp[1];
		}
		else{
			$show_pt_co_ins = "hide";
			//$co_ins_pat_exp=explode('/',$medicare_co_ins);
			//$visit_co_ins=$co_ins_pat_exp[1];
		}
	}
}

$pat_total_today_patientDue = 0;
$pat_total_today_patientDue_show = 0;
	for($i=0;$i<count($total_group);$i++){
		$grp_allowable_charges =  $today_charges["allowable_charges"][$i];
		$grp_group_id =  $today_charges["gro_id"][$i];
		$grp_patient_due =  $today_charges["Insurance_Due"][$i];
		$grp_insurance_due = $today_charges["Patient_Due"][$i];
		if($copay_deduct_total >= $grp_allowable_charges){
			$pat_total_today_patientDue += $grp_allowable_charges;
		//	$previous_data[$grp_group_id]["Patient_Due"][] += $grp_allowable_charges;
		}else{
			if($secondaryInsurance_chld>0){
				$pat_total_today_patientDue += $copay_deduct_total;
			//	$previous_data[$grp_group_id]["Patient_Due"][] += $copay_deduct_total;
			//	$previous_data[$grp_group_id]["Insurance_Due"][] += $grp_allowable_charges - $copay_deduct_total;
			}else{
				$pat_total_today_patientDue += $copay_deduct_total+(($grp_allowable_charges-$copay_deduct_total)*($visit_co_ins/100));
			//	$previous_data[$grp_group_id]["Patient_Due"][] += $pat_total_today_patientDue;
			//	$previous_data[$grp_group_id]["Insurance_Due"][] += $grp_allowable_charges - $pat_total_today_patientDue;
			}
		}
	}
$Previous_Data = $previous_data;	
$show_co_ins = ($total_allowable_charges-$total_today_copay)*($visit_co_ins/100);
if($show_co_ins == 0){
	$show_pt_co_ins = "hide";
}
else{
	$show_pt_co_ins = "show";
}
$ins_co_ins = $show_co_ins;
if($pat_tot_today_pay>0){
	if($pat_tot_today_pay>$pat_total_today_patientDue){
		$pat_total_today_patientDue=0;
		$pat_total_today_patientDue_show=0;
	}else{
		$pat_total_today_patientDue=$pat_total_today_patientDue-$pat_tot_today_pay;
		$pat_total_today_patientDue_show=$pat_total_today_patientDue-array_sum($total_payment_ci_arr);
	}
}else{
	$pat_total_today_patientDue_show=$pat_total_today_patientDue-array_sum($total_payment_ci_arr);
}
$pat_due_payment=($pat_total_today_patientDue+$total_prev_patientDue)-(array_sum($total_payment_ci_arr));
$ins_co_ins = numberformat($ins_co_ins,2,"yes");
$total_today_copay = number_format($total_today_copay,2);
//$objManageData->Smarty->assign("show_pt_co_ins", $show_pt_co_ins);
for($i=0;$i<count($checkInFieldsQryRes);$i++){
	$item_name = strtolower(trim($checkInFieldsQryRes[$i]['item_name']));
	$item_id = $checkInFieldsQryRes[$i]['id'];
	
	switch($item_name){
		case "copay-visit":
			$visitCopayId = "item_pay_".$item_id;
			$total_amt_arr[] = $totalCopayDilatedAmt;
			$totalCopayAmt_final = str_replace(',','',number_format($totalCopayDilatedAmt,2));
			$totalCopayAmt_non_dilated_final = str_replace(',','',number_format($totalCopayNonDilatedAmt,2));
			$checkInFieldsQryRes[$i]['item_amt']['dilated'] = $totalCopayAmt_final;
			$checkInFieldsQryRes[$i]['item_amt']['nondilated'] = $totalCopayAmt_non_dilated_final;
			$checkInFieldsQryRes[$i]['item_name'] = ucfirst($item_name);
		break;
		case "copay":
			$visitCopayId = "item_pay_".$item_id;
			$total_amt_arr[] = $totalCopayDilatedAmt;
			$totalCopayAmt_final = str_replace(',','',number_format($totalCopayDilatedAmt,2));
			$totalCopayAmt_non_dilated_final = str_replace(',','',number_format($totalCopayNonDilatedAmt,2));
			$checkInFieldsQryRes[$i]['item_amt']['dilated'] = $totalCopayAmt_final;
			$checkInFieldsQryRes[$i]['item_amt']['nondilated'] = $totalCopayAmt_non_dilated_final;
			$checkInFieldsQryRes[$i]['item_name'] = ucfirst($item_name);
		break;
		case "prv. t.bal":
			$prevBalId = "item_pay_".$item_id;
			$totalCopayAmt_final = str_replace(',','',numberformat($pat_totalBalance_final,2,"yes"));
			$total_amt_arr[] = $pat_totalBalance_final;
			$checkInFieldsQryRes[$i]['item_amt'] = $totalCopayAmt_final;
		break;
		case "deposit":
			$checkInFieldsQryRes[$i]['item_amt'] = '-';
		break;
		case "copay-test":
				$testCopayId = "item_pay_".$item_id;
			if($collect_copay_test == true){
				$total_amt_arr[] = $totalCopayTestDilatedAmt;
				$total_collect_dilated_amt = str_replace(',','',number_format($totalCopayTestDilatedAmt,2));
				$total_collect_non_dilated_amt = str_replace(',','',number_format($totalCopayTestNonDilatedAmt,2));
				$checkInFieldsQryRes[$i]['item_amt']['test_dilated'] = $total_collect_dilated_amt;
				$checkInFieldsQryRes[$i]['item_amt']['test_nondilated'] = $total_collect_non_dilated_amt;
				$checkInFieldsQryRes[$i]['item_name'] = ucfirst($item_name);
			}
			else{
				$checkInFieldsQryRes[$i]['item_amt'] = '-';
				$checkInFieldsQryRes[$i]['item_name'] = ucfirst($item_name);
			}
		break;
		case "refraction":			
			$ref_query = "SELECT GROUP_CONCAT(c3.mr_none_given) AS vis_mr_none_given
					FROM chart_master_table c1 
					INNER JOIN chart_vis_master c2 ON c2.form_id = c1.id
					INNER JOIN chart_pc_mr c3 ON c2.id = c3.id_chart_vis_master
					WHERE c1.patient_id = '".$patient_id."' and c1.date_of_service = '".$sa_app_start_date."' 					
					AND LOCATE(CONCAT('elem_mrNoneGiven',c3.ex_number,'=1'), c2.status_elements) > 0 
					GROUP BY c1.id 
					ORDER BY c1.id DESC LIMIT 0,1";			
			$refQryRes = get_data_array($ref_query);
			$checkInFieldsQryRes[$i]['item_amt'] = '-';
			$vis_mr_none_given = trim($refQryRes[0]["vis_mr_none_given"]);
			if($refractionChk=='Yes'){
				if($ref_proc=='92015'){
					if($procAmtArr["Refraction"]>0){
						if(array_sum($checkInPayArr_acc['Refraction'])>0){
						}else{
							$procAmtArr["Refraction"] = $procAmtArr["Refraction"]-$pat_tot_today_pay_ref;
						}
					}
					$refAmt = str_replace(',','',numberformat($procAmtArr["Refraction"],2));
					$checkInFieldsQryRes[$i]['item_amt'] = $refAmt;
				}else{
					if(empty($vis_mr_none_given) == false){
						preg_match("/None/",$vis_mr_none_given,$mrGivenArr);
						if(count($mrGivenArr) == 0){
							if($procAmtArr["Refraction"]>0){
								if(array_sum($checkInPayArr_acc['Refraction'])>0){
								}else{
									$procAmtArr["Refraction"] = $procAmtArr["Refraction"]-$pat_tot_today_pay_ref;
								}
							}
							$refAmt = str_replace(',','',numberformat($procAmtArr["Refraction"],2));
							$checkInFieldsQryRes[$i]['item_amt'] = $refAmt;
						}
					}
				}
			}
		break;
		
		case "deductible":
			$deductableId = "item_pay_".$item_id;
			$item_deductable_amt = str_replace(',','',numberFormat($total_today_deduct,2,"yes"));
			$total_amt_arr[] = $total_today_deduct;
			$checkInFieldsQryRes[$i]['item_amt'] = $item_deductable_amt;
		break; 
		
		case "pt co-ins":
			$co_insId = "item_pay_".$item_id;
			$item_co_ins = str_replace(',','',numberFormat($show_co_ins,2,"yes"));
			$total_amt_arr[] = $show_co_ins;
			$checkInFieldsQryRes[$i]['item_amt'] = $item_co_ins;
		break;  
		
		case "pt balance":
			$pt_balanceId = "item_pay_".$item_id;
			$item_pt_balance_amt = str_replace(',','',numberFormat($pat_due_payment,2,"yes"));
			$total_amt_arr[] = $pat_due_payment;
			$checkInFieldsQryRes[$i]['item_amt'] = $item_pt_balance_amt;
		break; 
		
		case "self pay":
			$self_pay_id = "item_pay_".$item_id;
			$not_covered_charges_amt = str_replace(',','',numberFormat(array_sum($not_covered_charges),2,"yes"));
			$total_amt_arr[] = array_sum($not_covered_charges);
			$checkInFieldsQryRes[$i]['item_amt'] = $not_covered_charges_amt;
		break; 

		case "contact lens":
			//$pt_balanceId = "item_pay_".$item_id;
			$cl_amt = str_replace(',','',numberFormat($procAmtArr["Contact lens"],2,"yes"));
			$total_amt_arr[] = $procAmtArr["Contact lens"];
			$checkInFieldsQryRes[$i]['item_amt'] = $cl_amt;
		break;  

		case "contact lens supply":
			//$pt_balanceId = "item_pay_".$item_id;
			$clsupplyamt = str_replace(',','',numberFormat($cl_supply_amt,2,"yes"));
			$total_amt_arr[] = $cl_supply_amt;
			$checkInFieldsQryRes[$i]['item_amt'] = $clsupplyamt;
		break;  
		
		default:
			$item_default_amt = '-';
			$item_name = $checkInFieldsQryRes[$i]['item_name'];
			if($procAmtArr[$item_name] > 0){
				$item_default_amt = str_replace(',','',numberFormat($procAmtArr[$item_name],2,"yes"));
			}
			$total_amt_arr[] = $procAmtArr[$item_name];
			$checkInFieldsQryRes[$i]['item_amt'] = $item_default_amt;
		break;
	}
	
	//--- CHECK IN / OUT ITEMS PAYMENT DETAILS ---
	$item_id = strtolower($checkInFieldsQryRes[$i]['id']);
	$chk_pay_det_arr = $checkInPayArr[$item_id]['checkout'];
	if(count($chk_pay_det_arr) > 0){
		$item_payment = str_replace(',','',number_format($chk_pay_det_arr['item_payment'],2));	
		$CICO_type=$chk_pay_det_arr['payment_type'];	
		$checkInFieldsQryRes[$i][$CICO_type]['item_payment'] = $item_payment;
		$checkInFieldsQryRes[$i][$CICO_type]['pay_detail_id'] = $chk_pay_det_arr['pay_detail_id'];
		$checkInFieldsQryRes[$i][$CICO_type]['item_checked'] = 'checked';
		$checkInFieldsQryRes[$i][$CICO_type]['copay_type'] = $chk_pay_det_arr['copay_type'];
		/*if($item_name == 'copay' and $item_payment == 0){
			$checkInFieldsQryRes[$i][$CICO_type]['nr_checked'] = 'checked';
			$checkInFieldsQryRes[$i][$CICO_type]['nr_enabled'] = 'disabled';
		}*/
	}	
	$chk_in_pay_det_arr = $checkInPayArr[$item_id]['checkin'];
	if(count($chk_in_pay_det_arr) > 0){
		$item_payment = str_replace(',','',number_format($chk_in_pay_det_arr['item_payment'],2));	
		$CICO_type=$chk_in_pay_det_arr['payment_type'];	
		$checkInFieldsQryRes[$i][$CICO_type]['item_payment'] = $item_payment;
		$checkInFieldsQryRes[$i][$CICO_type]['pay_detail_id'] = $chk_in_pay_det_arr['pay_detail_id'];
		$checkInFieldsQryRes[$i][$CICO_type]['item_checked'] = 'checked';
		$checkInFieldsQryRes[$i][$CICO_type]['copay_type'] = $chk_pay_det_arr['copay_type'];
		$total_ci_payment[]=$item_payment;
		/*if($item_name == 'copay' and $item_payment == 0){
			$checkInFieldsQryRes[$i][$CICO_type]['nr_checked'] = 'checked';
			$checkInFieldsQryRes[$i][$CICO_type]['nr_enabled'] = 'disabled';
		}*/
	}	
}
list($cy,$cm,$cd)=explode('-',$paymentQryRes_ci[0]["created_on"]);
$CI_created_on = $cm.'-'.$cd.'-'.$cy;
$CI_created_by = $paymentQryRes_ci[0]["created_by"];
$CI_created_time = $paymentQryRes_ci[0]["created_time"];

if($CI_created_by>0){
	$qry = "select fname,lname from users where id='$CI_created_by'";		
	$operatorDetails = get_data_array($qry);
	$CI_operatorName = substr($operatorDetails[0]['fname'],0,1).substr($operatorDetails[0]['lname'],0,1);
	
	$top_ci_header='CI-'.$CI_created_on.' '.$CI_created_time.' '.$CI_operatorName;
	//$objManageData->Smarty->assign("top_ci_header",$top_ci_header);
}
$superbill_vip=0;
$qry_superbill_vip="SELECT sb.vipSuperBill as super_bill_vip,cmt.date_of_service as dos from superbill as sb INNER JOIN chart_master_table as cmt on(cmt.id=sb.formId AND cmt.patient_id=sb.patientId) where sb.vipSuperBill=1 and cmt.date_of_service='".trim($schQryRes[0]['sa_app_start_date'])."' and cmt.patient_id='".$patient_id."' limit 0,1";
$res_superbill_vip=imw_query($qry_superbill_vip);
if(imw_num_rows($res_superbill_vip)>0){
	$row_superbill_vip=imw_fetch_assoc($res_superbill_vip);
	if($row_superbill_vip['super_bill_vip']==1 && (!$_REQUEST['vip_check'])){
		$superbill_vip=1;	
	}
}
if(verify_payment_method("MPAY")){
/*--GETTING due amount from superbill BASEd On pt_id and encounter id.--*/
	$mpay_query7 = "SELECT encounterId FROM chart_master_table WHERE patient_id='".$patient_id."' AND 
					date_format(create_dt,'%Y-%m-%d') = '".$schQryRes[0]['sa_app_start_date']."' ORDER BY id desc LIMIT 0,1";
	$mpay_result7 = get_data_array($mpay_query7);
	if($mpay_result7){
		$mpay_encounterID 	= $mpay_result7[0]['encounterId'];
	}
	
	$mpay_query6 = "SELECT todaysCharges, todaysPayment FROM superbill WHERE patientId='".$patient_id."' 
					AND encounterId = '".$mpay_encounterID."' ORDER BY idSuperBill desc LIMIT 0,1";
	$mpay_result6 = get_data_array($mpay_query6);
	if($mpay_result6){
		$superbilldue 	= intval($mpay_result6[0]['todaysCharges'])-intval($mpay_result6[0]['todaysPayment']);
	}
	/*--SETTING JS VARIABLES TO CARRY FIELD NAMES FOR COPAY AND PREVIOUS BALANCE FIELDS--*/
	//$objManageData->Smarty->assign("item_deductable_amt", $item_deductable_amt);
	//$objManageData->Smarty->assign("item_co_ins", $item_co_ins);
	//$objManageData->Smarty->assign("deductableId",$deductableId);
	//$objManageData->Smarty->assign("co_insId",$co_insId);
	//$objManageData->Smarty->assign("pt_balanceId",$pt_balanceId);
	//$objManageData->Smarty->assign("visitCopayId",$visitCopayId);
	//$objManageData->Smarty->assign("testCopayId",$testCopayId);
	//$objManageData->Smarty->assign("prevBalId",$prevBalId);
	$superbilldue = str_replace(',','',number_format($superbilldue,2));
}
	$isMpay = verify_payment_method("MPAY");


$top_co_header='Check Out-'.get_date_format(date('Y-m-d')).' '.date('h:i A').' '.$operatorName;
//$objManageData->Smarty->assign("top_co_header",$top_co_header);

//$objManageData->Smarty->assign("chk_sel_dilated",$chk_sel_dilated);

$totalCharges = str_replace(',','',number_format($totalVisitCharges,2));
$total_allowable_charges = str_replace(',','',number_format($total_allowable_charges,2));
$pat_total_today_patientDue = numberformat($pat_total_today_patientDue_show,2,1);
$total_prev_patientDue = str_replace(',','',number_format($total_prev_patientDue,2));
//--- SET TOTAL PAYMENTS ---
$pat_due_payment=($pat_total_today_patientDue+$total_prev_patientDue)-(array_sum($total_payment_ci_arr));
$total_payment = str_replace(',','',number_format(array_sum($total_payment_arr),2));
$total_ci_payment = str_replace(',','',number_format(array_sum($total_payment_ci_arr),2));
$total_co_payment = str_replace(',','',number_format(array_sum($total_payment_co_arr),2));
$total_pat_due_payment = numberformat($pat_due_payment,2,1);
$total_credit = numberformat(array_sum($total_credit_arr),2,1);
$total_credit_chk = str_replace(',','',number_format(array_sum($total_credit_arr),2));
/*print"<pre>";
print_r($total_payment_arr);
print_r($total_payment_co_arr); */
//--- SET PAYMENT METHOD ---
$pay_method = array("Cash"=>"Cash","Check"=>"Check","Credit Card"=>"Credit Card","EFT"=>"EFT","Money Order"=>"Money Order");

//$objManageData->Smarty->assign("payment_method",$pay_method);
//$objManageData->Smarty->assign("payment_select",$payment_select);
//$objManageData->Smarty->assign("sa_app_start_date",$sa_app_start_date);

//print "<pre>";
//print_r($checkInFieldsQryRes);
//--- SET CHECK IN / OUT FILEDS HTML ---
$check_out_data = $checkInFieldsQryRes;

//--- GET PATIENT INFO POPUP TEMPLATE -----



// 
/*	$ins_name = trim($insQryRes[$i]['in_house_code']);
	if($ins_name == ''){
		$ins_name = $insQryRes[$i]['name'];
	} */
//End Of Code
	$last_final_rx_id = "";
	$arr_last_final_rx = $obj_contactlens->get_last_final_rx($_SESSION["patient"],$sa_app_start_date);
	if($arr_last_final_rx !== false){
		$last_final_rx_id = $arr_last_final_rx[0]["clws_id"];
		$final_cl_rx_status = $arr_last_final_rx["final_status"];
	}
	
	//$objManageData->Smarty->assign("last_final_rx_id",$last_final_rx_id);
	//$objManageData->Smarty->assign("final_cl_rx_status",$final_cl_rx_status);
	$cl_rx = $last_final_rx_id;
	//$objManageData->Smarty->assign("cl_rx",$cl_rx);

	$vis_mr_none_given = "";
	$arr_last_glasses_rx = $obj_contactlens->get_last_glasses_rx($_SESSION["patient"],$sa_app_start_date);
	if($arr_last_glasses_rx !== false){
		$vis_mr_none_given = ($arr_last_glasses_rx["vis_mr_none_given"] == "None Given" || $arr_last_glasses_rx["vis_mr_none_given"] == "None") ? "" : $arr_last_glasses_rx["vis_mr_none_given"];
	}
	
	//$objManageData->Smarty->assign("vis_mr_none_given",$vis_mr_none_given);	
	$gl_rx = $vis_mr_none_given;
	//$objManageData->Smarty->assign("gl_rx",$gl_rx);
	
	$result_pc = $obj_contactlens->pc_data_existence($sa_app_start_date, $_SESSION["patient"]);
	if(trim($result_pc) != "")
	{
		$pc_rx = $result_pc;	
	}
	else
	{
		$pc_rx = 0;	
	}
    
$login_facility=$_SESSION['login_facility'];
$pos_device=false;
$devices_sql="Select tsys_device_details.id from tsys_device_details
              JOIN tsys_merchant ON tsys_merchant.id= tsys_device_details.merchant_id
              WHERE device_status=0
              AND tsys_device_details.facility_id='".$login_facility."' 
              AND merchant_status=0
              ";
$resp = imw_query($devices_sql);
if($resp && imw_num_rows($resp)>0){
    $pos_device=true;
}

//$objManageData->Smarty->assign("rootdir", $GLOBALS['rootdir']);
//$objManageData->Smarty->assign("form_height", "550");
$default_currency = htmlspecialchars_decode(show_currency());
//$objManageData->Smarty->assign("superbill_vip",$superbill_vip);
//$objManageData->Smarty->display(dirname(__FILE__)."/check_in_out_payment_html.php");
include_once(dirname(__FILE__)."/check_in_out_payment_html.php");
?>