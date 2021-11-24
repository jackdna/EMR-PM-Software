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
$global_date_format_small = str_replace("Y","y",$global_date_format);
$operator_id = $_SESSION['authId'];
if($_REQUEST['collectEid']<>""){
	$encounter_id = $_REQUEST['collectEid'];
}else{
	$encounter_id = $_REQUEST['encounter_id'];
}	

// GETTING AMOUNT DETAILS
$getPaidAmtStr = "SELECT * FROM patient_charge_list WHERE del_status='0' and encounter_id='$encounter_id'";
$getPaidAmtQry = imw_query($getPaidAmtStr);
$getPaidAmtRow = imw_fetch_array($getPaidAmtQry);
	$copay = $getPaidAmtRow['copay'];
	$copayRefractionPaid = $getPaidAmtRow['copayRefractionPaid'];
	$totalPaidAmt = $getPaidAmtRow['amtPaid'];
	$totalEncounterAamount = $getPaidAmtRow['totalAmt'];
	$amtPaid = $getPaidAmtRow['amtPaid'];
	$amountDue = $getPaidAmtRow['amountDue'];
	$patientAmt = $getPaidAmtRow['patientAmt'];
	$patientPaidAmt = $getPaidAmtRow['patientPaidAmt'];
	$insPaidAmt = $getPaidAmtRow['insPaidAmt'];
	$resPartyPaid = $getPaidAmtRow['resPartyPaid'];
	$insAmt = $getPaidAmtRow['insAmt'];
	$insuranceDue = $getPaidAmtRow['insuranceDue'];
	$patientDue = $getPaidAmtRow['patientDue'];
	$creditAmount = $getPaidAmtRow['creditAmount'];
	$totalBalance = $getPaidAmtRow['totalBalance'];
	$lastPayment = $getPaidAmtRow['lastPayment'];
	$lastPaymentDate = $getPaidAmtRow['lastPaymentDate'];
	$moaQualifier = $getPaidAmtRow['moaQualifier'];
	$patient_id = $getPaidAmtRow['patient_id'];
	
	//-------------------- INSURANCE PROVIDERS --------------------//
		$primaryInsProviderId = $getPaidAmtRow['primaryInsuranceCoId'];
		$secondaryInsProviderId = $getPaidAmtRow['secondaryInsuranceCoId'];
		$tertiaryInsProviderId = $getPaidAmtRow['tertiaryInsuranceCoId'];
			
			//---------------------- GETTING COMPANIES NAME ----------------------//
				$getPrimaryInsCoNameStr = "SELECT * FROM insurance_companies WHERE id='$primaryInsProviderId'";
				$getPrimaryInsCoNameQry = imw_query($getPrimaryInsCoNameStr);
				$getPrimaryInsCoNameRow = imw_fetch_array($getPrimaryInsCoNameQry);
					$primaryInsCoId = $getPrimaryInsCoNameRow['id'];
					$primaryInsCoName = $getPrimaryInsCoNameRow['in_house_code'];
					if($primaryInsCoName==""){
						$primaryInsCoName = $getPrimaryInsCoNameRow['name'];
					}
	
				$getSecondaryInsCoNameStr = "SELECT * FROM insurance_companies WHERE id='$secondaryInsProviderId'";
				$getSecondaryInsCoNameQry = imw_query($getSecondaryInsCoNameStr);
				$getSecondaryInsCoNameRow = imw_fetch_array($getSecondaryInsCoNameQry);
					$secondaryInsCoId = $getSecondaryInsCoNameRow['id'];
					$secondaryInsCoName = $getSecondaryInsCoNameRow['in_house_code'];
					if($secondaryInsCoName==""){
						$secondaryInsCoName = $getSecondaryInsCoNameRow['name'];
					}
	
				$getTertiaryInsCoNameStr = "SELECT * FROM insurance_companies WHERE id='$tertiaryInsProviderId'";
				$getTertiaryInsCoNameQry = imw_query($getTertiaryInsCoNameStr);
				$getTertiaryInsCoNameRow = imw_fetch_array($getTertiaryInsCoNameQry);
					$tertiaryInsCoId = $getTertiaryInsCoNameRow['id'];
					$tertiaryInsCoName = $getTertiaryInsCoNameRow['in_house_code'];
					if($tertiaryInsCoName==""){
						$tertiaryInsCoName = $getTertiaryInsCoNameRow['name'];
					}
			//---------------------- GETTING COMPANIES NAME ----------------------//		
			
				$insProvidersNameArr = array($primaryInsCoId => $primaryInsCoName, $secondaryInsCoId => $secondaryInsCoName, $tertiaryInsCoId => $tertiaryInsCoName);
	//-------------------- INSURANCE PROVIDERS --------------------//

		$case_type_id = $getPaidAmtRow['case_type_id'];
		$insCaseStr = "SELECT a.*, b.* FROM insurance_case a, 
						insurance_case_types b 
						WHERE a.patient_id='$patient_id' and
						a.ins_case_type=b.case_id
						AND a.ins_caseid='$case_type_id'";
		$insCaseQry = imw_query($insCaseStr);
		$insCaseRow = imw_fetch_array($insCaseQry);
			if($insCaseRow>0){
				$insTypeCode = $insCaseRow['case_name'];
			}	

		$getInsProviderStr = "SELECT b.* FROM insurance_data a,
								insurance_companies b
								WHERE a.pid='$patient_id'
								AND (a.type='primary' OR a.type='secondary' OR a.type='tertiary')
								AND a.provider=b.id";
		$getInsProviderQry = @imw_query($getInsProviderStr);
		$getInsProviderRow = @imw_fetch_array($getInsProviderQry);
			$insProvider = $getInsProviderRow['name'];
	
	$getPaymentExistsChkStr = "SELECT * FROM patient_chargesheet_payment_info
								WHERE encounter_id='$encounter_id'";
	$getPaymentExistsChkQry = @imw_query($getPaymentExistsChkStr);
	$getPaymentExistsChkRowsCount = @imw_num_rows($getPaymentExistsChkQry);

	//-------------------------- 	GETTING AMOUNT DETAILS	--------------------------//
	$getPaidAmtStr = "SELECT * FROM patient_charge_list WHERE del_status='0' and encounter_id='$encounter_id'";
	$getPaidAmtQry = @imw_query($getPaidAmtStr);
	$getPaidAmtRow = @imw_fetch_array($getPaidAmtQry);
		$copay = $getPaidAmtRow['copay'];
		$copayRefractionPaid = $getPaidAmtRow['copayRefractionPaid'];
		$totalPaidAmt = $getPaidAmtRow['amtPaid'];
		$charge_list_id = $getPaidAmtRow['charge_list_id'];
		
		$totalEncounterAamount = $getPaidAmtRow['totalAmt'];
		$amountDueToPay = $totalEncounterAamount - $totalPaidAmt;
	//-------------------------- 	GETTING AMOUNT DETAILS	--------------------------//

	//-------------------------- GETTING INS. CO. --------------------------//
	$getInsCompanies = @imw_query("SELECT * FROM patient_charge_list 
								WHERE del_status='0' and encounter_id='$encounter_id'");
	$getInsCompaniesRows = @imw_fetch_array($getInsCompanies);
		$provider1 = $getInsCompaniesRows['primaryInsuranceCoId'];
		$provider2 = $getInsCompaniesRows['secondaryInsuranceCoId'];
		$provider3 = $getInsCompaniesRows['tertiaryInsuranceCoId'];

	$getInsCompany1 = @imw_query("SELECT * FROM insurance_companies WHERE id='$provider1'");
	$getInsCompany1Row = @imw_fetch_array($getInsCompany1);
		$insurance1 = $getInsCompany1Row['name'];
		$insProviders[] = $insurance1;

	$getInsCompany2 = @imw_query("SELECT * FROM insurance_companies WHERE id='$provider2'");
	$getInsCompany2Row = @imw_fetch_array($getInsCompany2);
	$insurance2 = $getInsCompany2Row['name'];
	$insProviders[] = $insurance2;

	$getInsCompany3 = @imw_query("SELECT * FROM insurance_companies WHERE id='$provider3'");
	$getInsCompany3Row = @imw_fetch_array($getInsCompany3);
	$insurance3 = $getInsCompany3Row['name'];
	$insProviders[] = $insurance3;	
	//-------------------------- GETTING INS. CO. --------------------------//
	
	$b_id=$_REQUEST['b_id'];
	$sel_manual_file="select * from manual_batch_file where batch_id='$b_id'";
	$sel_manual_file_qry=imw_query($sel_manual_file);
	$sel_manual_file_fet=imw_fetch_array($sel_manual_file_qry);
	$default_write_code=$sel_manual_file_fet['default_write_code'];
	
	$show_cas_code_arr=array();
	// GETTING AMOUNT DETAILS
	$getCASStr = "SELECT * FROM cas_reason_code";
	$getCASQry = imw_query($getCASStr);
	while($getCASRow = imw_fetch_array($getCASQry)){
		$show_cas_code_arr[$getCASRow['cas_code']]=$getCASRow['cas_desc'];
	}
	
	$wrt_id_arr=array();
	$sel_rec=imw_query("select w_id,w_code,w_default from write_off_code");
	while($sel_write=imw_fetch_array($sel_rec)){
		$wrt_id_arr[]=$sel_write['w_id'];
		$wrt_name_arr[$sel_write['w_id']]=$sel_write['w_code'];
		$wrt_def_arr[$sel_write['w_id']]=$sel_write['w_default'];
		$write_off_code_data[$sel_write['w_id']]=$sel_write;
	}
	$wrt_drop='<select name="show_write_code" id="show_write_code"  class="selectpicker" data-width="90%"><option value="">Please Select</option>';
	foreach($wrt_id_arr as $wrt_id){
		$sel_wrt="";	
		if($wrt_def_arr[$wrt_id]=='yes'){ $sel_wrt="selected";}
		$wrt_drop.='<option value="'.$wrt_id.'" '.$sel_wrt.'>'.$wrt_name_arr[$wrt_id].'</option>';
	} 
	$wrt_drop.='</select>'; 
	$wrt_foot='<button type="button" class="btn btn-default" id="wrt_but_id">OK</button>';
	show_modal('write_off_div','Write off Code',$wrt_drop,$wrt_foot);
?>
	<?php
	if($encounter_id){
	$seq = 0;
	?>
	<table class="table table-bordered" style="margin:0px;">
    	<tr class="grythead">
            <th>DOS</th>
            <th>EId</th>
            <th>Ins. Case</th>
            <th>Total CoPay</th>
            <th>Pri Ins.</th>
            <th>Pri CoPay</th>
            <th>Sec Ins.</th>
            <th>Sec CoPay</th>
            <th>Tri Ins.</th>
            <th>Auth#</th>
            <th>Auth Amount</th>
        </tr>
        <?php
		$getEncounterDetailsStr = "SELECT * FROM patient_charge_list
									WHERE del_status='0' and encounter_id='$encounter_id'";
		$getEncounterDetailsQry = @imw_query($getEncounterDetailsStr);
		$getEncounterDetailsRow = @imw_fetch_array($getEncounterDetailsQry);
		$date_of_service = $getEncounterDetailsRow['date_of_service'];
		$dos = $getEncounterDetailsRow['date_of_service'];
			list($year, $month, $day) = explode("-", $date_of_service);
			//$date_of_service = $month."-".$day."-".$year;
			$date_of_service = date(''.phpDateFormat().'',mktime(0,0,0,$month,$day,$year));
		$case_type_id = $getEncounterDetailsRow['case_type_id'];
		$primaryInsuranceCoId = $getEncounterDetailsRow['primaryInsuranceCoId'];
		$secondaryInsuranceCoId = $getEncounterDetailsRow['secondaryInsuranceCoId'];
		$tertiaryInsuranceCoId = $getEncounterDetailsRow['tertiaryInsuranceCoId'];
		if(($case_type_id=='0') ||($primaryInsuranceCoId=='0' && $secondaryInsuranceCoId=='0' && $tertiaryInsuranceCoId=='0')){ 
			$case_type_nam = 'Self Pay';
		}else{
			$case_type_nam = $insTypeCode;
		}
		$copay = $getEncounterDetailsRow['copay'];
		$pri_copay = $getEncounterDetailsRow['pri_copay'];
		$sec_copay = $getEncounterDetailsRow['sec_copay'];
		$auth_no = $getEncounterDetailsRow['auth_no'];
		$auth_amount = $getEncounterDetailsRow['auth_amount'];
		
		 
		
		$getInsCo1DetailsStr = "SELECT * FROM insurance_companies WHERE id='$primaryInsuranceCoId'";
		$getInsCo1DetailsQry = @imw_query($getInsCo1DetailsStr);
		$getInsCo1DetailsRow = @imw_fetch_array($getInsCo1DetailsQry);
		$insCo1NameCode = $getInsCo1DetailsRow['in_house_code'];
		$insCo1NameCode1 = $getInsCo1DetailsRow['in_house_code'];
		if(!$insCo1NameCode){
			$insCo1Name = $getInsCo1DetailsRow['name'];
			$insCo1NameLen = strlen($insCo1Name);
			if($insCo1NameLen>13){
				$insCo1NameCode = $insCo1Name;
				$insCo1NameCode1 = substr($insCo1Name, 0, 13)."..";
			}else{
				$insCo1NameCode=$insCo1Name;
				$insCo1NameCode1=$insCo1Name;
			}
		}	
		
		$getInsCo2DetailsStr = "SELECT * FROM insurance_companies WHERE id='$secondaryInsuranceCoId'";
		$getInsCo2DetailsQry = @imw_query($getInsCo2DetailsStr);
		$getInsCo2DetailsRow = @imw_fetch_array($getInsCo2DetailsQry);
		$insCo2NameCode = $getInsCo2DetailsRow['in_house_code'];
		$insCo2NameCode1 = $getInsCo2DetailsRow['in_house_code'];
		if(!$insCo2NameCode){
			$insCo2Name = $getInsCo2DetailsRow['name'];
			$insCo2NameLen = strlen($insCo2Name);
			if($insCo2NameLen>13){
				$insCo2NameCode = $insCo2Name;
				$insCo2NameCode1 = substr($insCo2Name, 0, 13)."..";
			}else{
				$insCo2NameCode = $insCo2Name;
				$insCo2NameCode1 = $insCo2Name;
			}
		}	
		
		$getInsCo3DetailsStr = "SELECT * FROM insurance_companies WHERE id='$tertiaryInsuranceCoId'";
		$getInsCo3DetailsQry = @imw_query($getInsCo3DetailsStr);
		$getInsCo3DetailsRow = @imw_fetch_array($getInsCo3DetailsQry);
		$insCo3NameCode = $getInsCo3DetailsRow['in_house_code'];
		$insCo3NameCode1 = $getInsCo3DetailsRow['in_house_code'];
		if(!$insCo3NameCode){
			$insCo3Name = $getInsCo3DetailsRow['name'];
			$insCo3NameLen = strlen($insCo3Name);
			if($insCo3NameLen>13){
				$insCo3NameCode = $insCo3Name;
				$insCo3NameCode1 = substr($insCo3Name, 0, 13)."..";
			}else{
				$insCo3NameCode=$insCo3Name;
				$insCo3NameCode1=$insCo3Name;
			}
		}	
		$insCoArray[] = $insCo1NameCode;
		$insCoArray[] = $insCo2NameCode;
		$insCoArray[] = $insCo3NameCode;
		?>
		<tr>
			<th><?php echo $date_of_service; ?></th>
			<th><?php echo $encounter_id; ?></th>
			<th><?php echo $case_type_nam; ?></th>
			<th><?php if($copay>0) echo number_format($copay, 2); else echo '&nbsp;'; ?></th>
			<th><?php if($insCo1NameCode1) echo $insCo1NameCode1; else echo '-'; ?></th>
			<th><?php if($pri_copay>0) echo number_format($pri_copay, 2); else echo '&nbsp;'; ?></th>
			<th><?php if($insCo2NameCode1) echo $insCo2NameCode1; else echo '-'; ?></th>
			<th><?php if($sec_copay>0) echo number_format($sec_copay, 2); else echo '&nbsp;'; ?></th>
			<th><?php if($insCo3NameCode1) echo $insCo3NameCode1; else echo '-'; ?></th>
			<th><?php if($auth_no) echo $auth_no; else echo '&nbsp;'; ?></th>
			<th><?php if($auth_amount>0) echo $auth_amount; else echo '&nbsp;'; ?></th>
		</tr>
	</table>
    <table class="table table-bordered">
        <tr class="grythead">
            <th>Apply</th>
            <th>CPT</th>
            <th class="text-nowrap">Dx Code</th>
            <th class="text-nowrap">
                <table>
                    <tr style="height:38px;">
                        <th style="padding:0px 5px 0px 5px; vertical-align:top;">Total Charges</th>										
                        <th id="contract_fee" style="padding:0px 5px 0px 5px; border-left:1px solid #fff;">Contract Fee</th>
                    </tr>
                </table>
            </th>							
            <th>Allowed</th>
            <th>Deductible</th>
            <th class="text-nowrap">Write Off</th>
            <th>Credit</th>					
            <th>Paid</th>
            <th>Amount</th>
            <th class="text-nowrap">New Balance</th>
            <th class="text-nowrap">Over Paid </th>
            <th>DOT</th>
            <th>Method</th>
            <th class="text-nowrap">CC / Ch.# </th>					
            <th class="text-nowrap">CC Exp. Date</th>
            <th class="text-nowrap">Submitted Date</th>
            <th class="text-nowrap">Write off Code</th>
            <th>Oper</th>
        </tr>
		<?php
        $getChargesDetailsStr = "SELECT * FROM patient_charge_list
                                WHERE del_status='0' and encounter_id='$encounter_id'";
        $getChargesDetailsQry = imw_query($getChargesDetailsStr);
        while($getChargesDetailsRow = imw_fetch_array($getChargesDetailsQry)){
            $chargeListId = $getChargesDetailsRow['charge_list_id'];
            $coPay = $getChargesDetailsRow['copay'];
            $copayPaid = $getChargesDetailsRow['copayPaid'];
            $referactionPaid = $getChargesDetailsRow['referactionPaid'];
            $coPayNotRequired = $getChargesDetailsRow['coPayNotRequired'];
            $coPayWriteOff = $getChargesDetailsRow['coPayWriteOff'];
            
            $amountDue = $getChargesDetailsRow['amountDue'];
            $totalPaidAmt = $getChargesDetailsRow['amtPaid'];
            $totalAmt = $getChargesDetailsRow['totalAmt'];
            $approvedTotalAmt = $getChargesDetailsRow['approvedTotalAmt'];
            $deductibleTotalAmt = $getChargesDetailsRow['deductibleTotalAmt'];
            $totalEncounterBalance = number_format($getChargesDetailsRow['totalBalance'], 2);
            $coPayAdjusted = $getChargesDetailsRow['coPayAdjusted'];
            $creditAmountBalance = $getChargesDetailsRow['creditAmount'];
            $overPayment = $getChargesDetailsRow['overPayment'];
            $operator_id = $getChargesDetailsRow['operator_id'];
            $operatorNamePaid = $usr_alias_name[$operator_id];

            $encCommentsInt = $getChargesDetailsRow['encCommentsInt'];
            $encCommentsExt = $getChargesDetailsRow['encCommentsExt'];
            $encCommentsIntDate = $getChargesDetailsRow['encCommentsIntDate'];
                list($yearComment, $monthComment, $dayComment) = explode("-", $encCommentsIntDate);
                $encCommentsIntDate = $monthComment."-".$dayComment."-".$yearComment;
                
            $encCommentsExtDate = $getChargesDetailsRow['encCommentsExtDate'];
                list($yearComment, $monthComment, $dayComment) = explode("-", $encCommentsExtDate);
                $encCommentsExtDate = $monthComment."-".$dayComment."-".$yearComment;
            $encCommentsIntOperatorId = $getChargesDetailsRow['encCommentsIntOperatorId'];
			$commentsIntOperatorName = $usr_full_name[$encCommentsIntOperatorId];
            
            $encCommentsExtOperatorId = $getChargesDetailsRow['encCommentsExtOperatorId'];
			$commentsExtOperatorName = $usr_full_name[$encCommentsExtOperatorId];
            
            //$postedDate = $getChargesDetailsRow['postedDate'];
                $postedDate = $getChargesDetailsRow['firstSubmitDate'];
                    list($year, $month, $day)=explode("-", $postedDate);
                    $postedDate=$month."-".$day."-".$year;
                
            $totalBalance = $totalAmt - $totalPaidAmt;
            $deductAmt = false;
            $whoPaidAmt = '';
            //-------------------- ASC ORDER BY CPT DESC. --------------------//

            $totalRefractionAmountFor = 0;
            $reflactionAmt = 0;
            $amountToPay = 0;
            $total_write_off_amount = 0;
            if(($copay>0) && ($copayPaid<=0) && ($coPayNotRequired!=1) && ($coPayWriteOff!='1')){
                $amountToPay = $amountToPay + $copay;
            }
            $getproccode1 = "SELECT sum(paidForProc) as tot_paidproc FROM 
                                patient_chargesheet_payment_info a,
                                patient_charges_detail_payment_info b
                                WHERE a.encounter_id = '$encounter_id'
                                AND a.payment_id = b.payment_id
                                AND b.charge_list_detail_id = 0
                                AND b.deletePayment=0
                                ORDER BY a.payment_id DESC";
                $getproccodeQry1 = imw_query($getproccode1);
                $getproccodeRow1 = imw_fetch_array($getproccodeQry1);
                $paidForProc_chk1 = $getproccodeRow1['tot_paidproc'];
                $tot_paid_chk1=$paidForProc_chk1;
        
            $proc_code_imp=get_proc_code($chargeListId);
            $copay_collect_proc=copay_apply_chk($proc_code_imp,'','');
        
                
            $getProcDetailsStr = "SELECT a.* FROM
                                    patient_charge_list_details a,
                                    cpt_fee_tbl b
                                    WHERE a.del_status='0' and charge_list_id='$chargeListId'
                                    AND a.procCode=b.cpt_fee_id
                                    ORDER BY a.charge_list_detail_id ASC";
            //-------------------- ASC ORDER BY CPT DESC. --------------------//
            $getProcDetailsQry = imw_query($getProcDetailsStr);
            $getProcCountRows = imw_num_rows($getProcDetailsQry);
            while($getProcDetailsRows = imw_fetch_array($getProcDetailsQry)){
                $charge_list_detail_id = $getProcDetailsRows['charge_list_detail_id'];
                $procIdForCredit = $getProcDetailsRows['procCode'];
                $dx1 = $getProcDetailsRows['diagnosis_id1'];		
                $dx2 = $getProcDetailsRows['diagnosis_id2'];		
                $dx3 = $getProcDetailsRows['diagnosis_id3'];		
                $dx4 = $getProcDetailsRows['diagnosis_id4'];
                $write_off_code_id = $getProcDetailsRows['write_off_code_id'];
                $write_off_by = $getProcDetailsRows['write_off_by'];
                $del_wrt="1";
                $auto_wrt_amt=$getProcDetailsRows['totalAmount']-$getProcDetailsRows['approvedAmt'];
                if($getProcDetailsRows['totalAmount']>$getProcDetailsRows['approvedAmt']){
                    $del_wrt="";
                }
                auto_writeoff_tran($patient_id,$charge_list_detail_id,$auto_wrt_amt,$write_off_by,$del_wrt);
                
                $w_code_qry = imw_query("SELECT w_code FROM write_off_code 
                                            WHERE w_id = '$write_off_code_id'");							
                $w_code_row = imw_fetch_array($w_code_qry);
                $write_off_code=$w_code_row['w_code'];
                
                $dx_code=$dx1.' '.$dx2.' '.$dx3.' '.$dx4;									
                //----------------------- GETTING PAYMENT DETAILS -----------------------//
                
                $getPaymentDetailEncStr = "SELECT * FROM patient_charges_detail_payment_info 
                                            WHERE charge_list_detail_id = '$charge_list_detail_id'";							
                $getPaymentDetailEncQry = imw_query($getPaymentDetailEncStr);
                $getPaymentRows = imw_num_rows($getPaymentDetailEncQry);
                $getPaymentDetailEncRow = imw_fetch_array($getPaymentDetailEncQry);
                $paymentIdNew = $getPaymentDetailEncRow['payment_id'];							
                $deletePayment = $getPaymentDetailEncRow['deletePayment'];						
                $paidByNew = $getPaymentDetailEncRow['paidBy'];
                    //---------------- PAYMENT MADE BY --------------//
                    $getPaymentDetailsStr = "SELECT * FROM 
                                            patient_chargesheet_payment_info a,
                                            patient_charges_detail_payment_info b
                                            WHERE a.payment_id = b.payment_id
                                            AND b.charge_list_detail_id = '$charge_list_detail_id'";
                    $getPaymentDetailsQry = imw_query($getPaymentDetailsStr);
                    $getRowsCount = imw_num_rows($getPaymentDetailsQry);
                    if($getRowsCount>0){
                        while($getPaymentDetailsRows = imw_fetch_array($getPaymentDetailsQry)){
                            $payment_id = $getPaymentDetailsRows['payment_id'];
                            if($deletePayment!=1){
                                $paidBy = $getPaymentDetailsRows['paidBy'];
                                if($paidBy != $paidByNew){
                                    $paidByNew = 'Multi';
                                }
                            }
                        }
                    }							
                    //---------------- PAYMENT MADE BY --------------//
                $paidDateNew = $getPaymentDetailEncRow['paidDate'];
                if($paidDateNew!=''){
                    list($year, $month, $day)=explode("-", $paidDateNew);
                    $paidDateNew = $month."-".$day."-".$year;
                }else{
                    $paidDateNew = '-';
                }
                $writeOffAmount = 0;
                //----------------------- GETTING PAYMENT DETAILS -----------------------//
                $balForProc = $getProcDetailsRows['balForProc'];
                $procNewBalance = $getProcDetailsRows['newBalance'];
                $approvedAmt = $getProcDetailsRows['approvedAmt'];
                $deductAmt = $getProcDetailsRows['deductAmt'];
                
                $getPaymentExistsChkStr = "SELECT * FROM patient_chargesheet_payment_info
                                            WHERE encounter_id='$encounter_id'
                                            AND payment_id ='$paymentIdNew'";
                $getPaymentExistsChkQry = @imw_query($getPaymentExistsChkStr);
                $getPaymentExistsChkRow = @imw_fetch_array($getPaymentExistsChkQry);
                $paid_by = $getPaymentExistsChkRow['paid_by'];
                $paidDate = $getPaymentExistsChkRow['date_of_payment'];
                    list($year, $month, $day)=explode("-", $paidDate);
                    $paidDate=$month."-".$day."-".$year;
                if($paidDate=="--"){
                    $paidDate="-";
                }
                $payment_mode = $getPaymentExistsChkRow['payment_mode'];
                if($payment_mode==""){
                    $payment_mode="-";
                }
                if($payment_mode=='Check' || $payment_mode=='EFT' || $payment_mode=='Money Order' || $payment_mode=='VEEP'){
                    $checkCCNo = $getPaymentExistsChkRow['checkNo'];
                }else{
                    $checkCCNo = "-";
                }
                if($payment_mode=='Credit Card'){
                    $checkCCNo = $getPaymentExistsChkRow['creditCardNo'];
                }
                if($payment_mode=='Cash'){
                    $checkCCNo='-';
                }
                $expirationDate = $getPaymentExistsChkRow['expirationDate'];
                $operatorId = $getPaymentExistsChkRow['operatorId'];
                $claimDenied = $getProcDetailsRows['claimDenied'];
                $paidStatus = $getProcDetailsRows['paidStatus'];
                $procId = $getProcDetailsRows['procCode'];
                $units = $getProcDetailsRows['units'];
                $procCharges = $getProcDetailsRows['procCharges'];
                $totalAmount = $getProcDetailsRows['totalAmount'];
                $paidForProc = $getProcDetailsRows['paidForProc'];
                $balForProc = $getProcDetailsRows['balForProc'];
                $write_off_Proc = $getProcDetailsRows['write_off'];
                    $writeOffId = $getProcDetailsRows['charge_list_detail_id'];
                if($write_off_Proc){
                    //$writeOffAmount = $write_off_Proc;
                }else{
                    //$writeOffAmount = 0;
                }							
                $getWriteOffAmtStr = "SELECT * FROM paymentswriteoff
                                    WHERE patient_id = '$patient_id'
                                    AND encounter_id = '$encounter_id'
                                    AND charge_list_detail_id = '$charge_list_detail_id'
                                    AND delStatus = 0";
                $getWriteOffAmtQry = imw_query($getWriteOffAmtStr);
                while($getWriteOffAmtRow = imw_fetch_array($getWriteOffAmtQry)){
                    $writeOffAmount = $writeOffAmount+$getWriteOffAmtRow['write_off_amount'];
                }					
                
                $NewBalance = $getProcDetailsRows['newBalance'];
                $coPayAdjustedAmount =  $getProcDetailsRows['coPayAdjustedAmount'];
                $creditProcAmount =  $getProcDetailsRows['creditProcAmount'];
                $overPaymentForProc = $getProcDetailsRows['overPaymentForProc'];
                $getCptFeeDetailsStr = "SELECT * FROM cpt_fee_tbl WHERE cpt_fee_id = '$procId' AND delete_status = '0'";
                $getCptFeeDetailsQry = imw_query($getCptFeeDetailsStr);
                $getCptFeeDetailsRow = imw_fetch_array($getCptFeeDetailsQry);
                    $cptPracCode = $getCptFeeDetailsRow['cpt_prac_code'];
                    $cpt4_code = $getCptFeeDetailsRow['cpt4_code'];
                    $cptDesc = $getCptFeeDetailsRow['cpt_desc'];

                if(($cptPracCode=='92015') || ($cpt4_code=='92015')){
                    $totalRefractionAmountFor = $totalRefractionAmountFor + $NewBalance;
                    $reflactionAmt = $NewBalance;
                    $refractionExists = true;
                    if($paidStatus == 'Paid'){
                        $paid_by = 'Patient';
                    }
                }else{
                    $refractionExists = false;
                }
                
                if(($reflactionAmt>0) && ($referactionPaid<=0)){
                    $amountToPay = $amountToPay + $reflactionAmt;
                }
                
                $writeOff = $totalAmount - $approvedAmt;

                if($cptPracCode=='92015'){ 
                    //$writeOff = '0.00';
                }					
                
                ++$seq;

                $totalBalanceNewDue = $approvedTotalAmt - $creditAmount - $totalPaidAmt - $deductibleTotalAmt;
                if($copay){
                    if(($copayPaid!=1) && ($coPayNotRequired != 0) && ($coPayWriteOff!='1')){
                        $amount = $amount - $copay;
                }
            }
            
            $batch_crd_amt_adust=0;
            $gettot_crd3 = "SELECT sum(amountApplied) as amt_adust  FROM manual_batch_creditapplied WHERE charge_list_detail_id_adjust  = '$charge_list_detail_id'  and delete_credit='0' and credit_applied='1' and post_status='0'";
            $gettot_crdQry3 = imw_query($gettot_crd3);
            $gettot_crdrow3 = imw_fetch_array($gettot_crdQry3);
            $batch_crd_amt_adust = $gettot_crdrow3['amt_adust'];
            
            $batch_deb_amt_adust=0;
            $gettot_crd4 = "SELECT sum(amountApplied) as amt_adust  FROM manual_batch_creditapplied WHERE charge_list_detail_id  = '$charge_list_detail_id'  and delete_credit='0' and credit_applied='1' and post_status='0'";
            $gettot_crdQry4 = imw_query($gettot_crd4);
            $gettot_crdrow4 = imw_fetch_array($gettot_crdQry4);
            $batch_deb_amt_adust = $gettot_crdrow4['amt_adust'];
            
            $trans_amt_total="";
            $sel_tran_amt=imw_query("select sum(trans_amt) as trans_amt_total 
                            from manual_batch_transactions
                            where
                            charge_list_detaill_id='$charge_list_detail_id'
                            and (payment_claims='Paid' || payment_claims='Deposit' 
                                || payment_claims='Interest Payment'
                                || payment_claims='Over Adjustment')
                            and post_status!=1
                            and del_status=0");
            $trans_paid_amt=imw_fetch_array($sel_tran_amt);
            $trans_amt_total=$batch_crd_amt_adust+$trans_paid_amt['trans_amt_total'];
            
            $trans_neg_total="";
            $sel_neg_amt=imw_query("select sum(trans_amt) as trans_amt_total 
                            from manual_batch_transactions
                            where
                            charge_list_detaill_id='$charge_list_detail_id'
                            and (payment_claims='Negative Payment')
                            and post_status!=1
                            and del_status=0");
            $trans_neg_paid_amt=imw_fetch_array($sel_neg_amt);
            $trans_neg_total=$trans_neg_paid_amt['trans_amt_total'];
            
            
        
            
            $trans_deduct_amt_total="";
            $sel_tran_amt_deduct=imw_query("select sum(trans_amt) as trans_amt_total 
                            from manual_batch_transactions
                            where
                            charge_list_detaill_id='$charge_list_detail_id'
                            and (payment_claims='Deductible')
                            and post_status!=1
                            and del_status=0");
            $trans_deduct_amt=imw_fetch_array($sel_tran_amt_deduct);
            $trans_deduct_amt_total=$trans_deduct_amt['trans_amt_total'];
            
            $trans_write_amt_total="";
            $sel_tran_amt_write=imw_query("select sum(trans_amt) as trans_amt_total 
                            from manual_batch_transactions
                            where
                            charge_list_detaill_id='$charge_list_detail_id'
                            and (payment_claims='Write Off' || payment_claims='Discount')
                            and post_status!=1
                            and del_status=0");
            $trans_write_amt=imw_fetch_array($sel_tran_amt_write);
            $trans_write_amt_total=$trans_write_amt['trans_amt_total'];
            
            $writeOffAmount=$writeOffAmount+$trans_write_amt_total;
            
            $trans_allow_write_amt_total="";
            $trans_allow_amt_total="";
            $write_off_code_trans_id="";
            $sel_tran_amt_allow=imw_query("select trans_amt,proc_allow_amt,write_off_code_id 
                            from manual_batch_transactions
                            where
                            charge_list_detaill_id='$charge_list_detail_id'
                            and (payment_claims='Allowed')
                            and post_status!=1
                            and del_status=0");
            $trans_allow_amt=imw_fetch_array($sel_tran_amt_allow);
            $trans_allow_amt_total=$trans_allow_amt['proc_allow_amt'];
            $trans_allow_write_amt_total=$trans_allow_amt['trans_amt'];
            $write_off_code_trans_id=$trans_allow_amt['write_off_code_id'];
            
            if($trans_allow_write_amt_total>0){
                $chk_old_writeoff_amt=$trans_allow_write_amt_total-$writeOff;
                $writeOff=$trans_allow_write_amt_total;
            }else{
                $chk_old_writeoff_amt=$trans_allow_write_amt_total;
            }
                        
            $adj_amt_total="";
            $sel_adj_amt=imw_query("select sum(trans_amt) as trans_amt_total 
                            from manual_batch_transactions
                            where
                            charge_list_detaill_id='$charge_list_detail_id'
                            and (payment_claims='Adjustment')
                            and post_status!=1
                            and del_status=0");
            $adj_paid_amt=imw_fetch_array($sel_adj_amt);
            $adj_amt_total=$adj_paid_amt['trans_amt_total'];
                         
            $total_writ_amt=$trans_write_amt_total+$chk_old_writeoff_amt;
            $NewBalance=$NewBalance-$trans_amt_total-$total_writ_amt+$trans_neg_total;
            
            $overPaymentForProc_trans="";
            if($NewBalance<0){
                $overPaymentForProc_trans=substr($NewBalance,1);
                $overPaymentForProc=$overPaymentForProc+$overPaymentForProc_trans;
            }
            if($adj_amt_total>0){
                if($overPaymentForProc>0){
                    if($overPaymentForProc>=$adj_amt_total){
                        $overPaymentForProc=$overPaymentForProc-$adj_amt_total;
                        $NewBalance=0;
                    }else{
                        $chk_adj_bal=$adj_amt_total-$overPaymentForProc;
                        $NewBalance=$chk_adj_bal;
                        $paidForProc=$paidForProc-$chk_adj_bal;
                        $overPaymentForProc=0;
                    }
                }else{
                    $NewBalance=$NewBalance+$adj_amt_total;
                    $overPaymentForProc=0;
                    $paidForProc=$paidForProc-$adj_amt_total;
                }
            }	
            
            $deductAmt=$deductAmt+$trans_deduct_amt_total;
            //$writeOff=$writeOff+$trans_write_amt_total;
            if($batch_deb_amt_adust>0){
                if($overPaymentForProc>=$batch_deb_amt_adust){
                    $overPaymentForProc = $overPaymentForProc-$batch_deb_amt_adust;
                }else{
                    $chk_ovr_deb_amt=$batch_deb_amt_adust-$overPaymentForProc;
                    $paidForProc = $paidForProc-$chk_ovr_deb_amt;
                    $NewBalance = $NewBalance+$chk_ovr_deb_amt;
                    $overPaymentForProc = 0;
                }
            }
            if($trans_neg_total>0){
                if($overPaymentForProc>=$trans_neg_total){
                    $overPaymentForProc=$overPaymentForProc-$trans_neg_total;
                }else{
                    $neg_paid_trans=$trans_neg_total-$overPaymentForProc;
                    $overPaymentForProc=0;
                }
            }
            $paidForProc=($paidForProc+$trans_amt_total)-($overPaymentForProc_trans+$neg_paid_trans);
            $approvedAmt = $approvedAmt;
            $deductAmt = number_format($deductAmt, 2);
            $paidForProc = $paidForProc;
            $creditProcAmount = number_format($creditProcAmount, 2);
            $totalAmount = number_format($totalAmount, 2);
            $amount = number_format($amount, 2);
            $copay_collect=copay_apply_chk($cpt4_code,'','');
            
            if($trans_allow_amt_total>0){
                if($write_off_code_trans_id>0){
                    $write_off_code_id=$write_off_code_trans_id;
                }
                $w_code_qry = imw_query("SELECT w_code FROM write_off_code 
                                            WHERE w_id = '$write_off_code_id'");							
                $w_code_row = imw_fetch_array($w_code_qry);
                $write_off_code=$w_code_row['w_code'];
                $approvedAmt=$trans_allow_amt_total;
            }
            ?>
            <tr>
                
                <td>
                	<div class="checkbox">
                        <input type="checkbox" value="<?php echo $charge_list_detail_id; ?>" id="chkbx<?php echo $seq; ?>" name="chkbx[]" onClick="return checkPaymentBox('<?php echo $seq; ?>', '<?php echo $writeOffAmount; ?>','<?php echo $getProcCountRows; ?>')" />
                        <label for="chkbx<?php echo $seq; ?>"></label>
                    </div>
                </td>
                 <?php 
                 if($cpt_onetime==0){
                    $cpt_onetime=0;
                 if($copay_collect_proc==true && $copay_collect_proc[0]==true){
                    if($copay_collect[0]==true && $copay_collect[0]==true){
                ?>
                    <input type="hidden" value="<?php echo $charge_list_detail_id; ?>" name="copay_apply_procedure" id="copay_apply_procedure">
                <?php
                        $cpt_onetime++; 
                    }
                    }else{
                        if($balForProc>0){
                ?>
                    <input type="hidden" value="<?php echo $charge_list_detail_id; ?>" name="copay_apply_procedure" id="copay_apply_procedure">
                <?php	
                            $cpt_onetime++;	
                        }
                    }
                    
                }
                $tot_paid_chk2=0;
                if($min_copay_one==0){
                    $min_copay_one=0;
                    $minus_copay_value=0;
                   if($coPayAdjustedAmount==1){									
                      if($coPay==$tot_paid_chk1){
                            $minus_copay_value=$coPay;
                            $min_copay_one++;
                        }else{
                            if($tot_paid_chk1<>0){
                                $minus_copay_value=$coPay-$tot_paid_chk1;
                                $min_copay_one++;
                            }
                        }
                    }
                    if($min_copay_one>0){
                        $tot_paid_chk2=$tot_paid_chk1;
                    }
                }else{
                    $minus_copay_value=0.00;
                }
                
                //echo $tot_paid_chk1;
                ?>
                <td class="text-nowrap" <?php if($cptPracCode==$return_chk_proc){echo "style='background:#F00;color:#FFF;font-weight:bold;'";} ?>>
                    <input type="hidden" value="<?php echo $tot_paid_chk2; ?>" name="copay_paid<?php echo $seq; ?>" id="copay_paid<?php echo $seq; ?>">
                    <input type="hidden" value="<?php echo $minus_copay_value; ?>" name="minus_copay<?php echo $seq; ?>" id="minus_copay<?php echo $seq; ?>">
                    <span><?php if($coPayAdjustedAmount==1){ ?><img src="../../library/images/confirm.gif" style="width:16px; vertical-align:middle;"/><?php } ?></span>
                    <span id="cptIdTd<?php echo $seq; ?>" <?php echo show_tooltip($cptDesc); ?>  style="margin-right:10px;"> <?php echo $cptPracCode; ?> </span>
                </td>
                <td class="text-nowrap"><?php echo $dx_code; ?>&nbsp;</td>
                <td>
                    <table class="table_collapse">
                        <tr style="height:22px;">
                            <td align="left" style="border:none;">
                            	<div class="input-group">
									<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
                                	<input type="text" name="proc_total_amt<?php echo $seq; ?>" id="proc_total_amt<?php echo $seq; ?>" value="<?php echo $totalAmount; ?>" class="form-control" onChange="return EditCharges('<?php echo $seq; ?>');">
								</div>
                                <input type="hidden" name="totalFee<?php echo $seq; ?>" id="totalFee<?php echo $seq; ?>" value="<?php echo $totalAmount; ?>">
                                <input type="hidden" name="proc_unit<?php echo $seq; ?>" id="proc_unit<?php echo $seq; ?>" value="<?php echo $units; ?>">
                            </td>										
                            <?php
                                $contract_fee=getContractFee($cptPracCode,$primaryInsuranceCoId);
                                if($contract_fee){
                                    $display_fee_row = true;
                                    $contract_fee_final=$contract_fee*$units;
                            ?>
                            <td style="width:82px;border-right:none; border-top:none; border-bottom:none;color:#0000FF;" class="text_10"><strong><?php echo $showCurrencySymbol.number_format($contract_fee_final,2); ?></strong></td>
                            <?php } ?>
                        </tr>
                    </table>
                </td>
                <td id="getBalTd<?php echo $seq; ?>" style="display:none;"><?php echo $balForProc; ?></td>
                <td>
                	<div class="input-group">
                        <div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
                   		<input type="text" name="approvedText<?php echo $seq; ?>" id="approvedText<?php echo $seq; ?>" class="form-control" style="width:90px;" value="<?php echo str_replace(',','',number_format($approvedAmt,2)); ?>" onChange="set_write_off_id('<?php echo $seq; ?>',event.y);return checkChkBox(<?php echo $seq; ?>);" onBlur="return approvedBlur('<?php echo $seq; ?>', '<?php echo $writeOffAmount; ?>', this);">
                    </div>
                    <input type="hidden" name="app_amt_chld<?php echo $seq; ?>" id="app_amt_chld<?php echo $seq; ?>" value="<?php echo str_replace(',','',number_format($approvedAmt,2)); ?>">
                    <input type="hidden" name="appActualText<?php echo $seq; ?>" id="appActualText<?php echo $seq; ?>" value="<?php echo str_replace(',','',number_format($approvedAmt,2)); ?>">
                </td>
                <td>
                	<div class="input-group">
                        <div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
                  		<input type="text" style="width:90px; <?php if($deductAmt>0){echo"color:#FFC000;";}?>" name="deductibleText<?php echo $seq; ?>" id="deductibleText<?php echo $seq; ?>" class="form-control" value="<?php echo str_replace(',','',$deductAmt);?>" onChange="return deductChange('<?php echo $seq; ?>'),paymentChange_bydeduct('<?php echo $seq; ?>',this.value,'<?php echo $deductAmt; ?>'),paymentChange('<?php echo $seq; ?>', '<?php echo $writeOffAmount; ?>')">
                    </div>
                </td>
                <td id="writeOffTd<?php echo $seq; ?>">
                <?php 
                if($writeOffAmount>0){
                    $tot_writeOff=$writeOffAmount+$writeOff;
                    echo $showCurrencySymbol.number_format($tot_writeOff, 2); 
                    $show_era_wrt_amt=$tot_writeOff;
                }else{
                    echo $showCurrencySymbol.number_format($writeOff, 2); 
                    $show_era_wrt_amt=$writeOff;
                }
                
                ?>
                </td>
                <input type="hidden" name="write_off_code<?php echo $seq; ?>" id="write_off_code<?php echo $seq; ?>" value="<?php echo $w_code; ?>">

                <!-- Credit Paid Pay -->
                <td id="creditAmtTd<?php echo $seq; ?>" style="color:#009900;font-weight:bold;">
                    <?php 
                        $credit_total=0;
                        $amt_payment=0;
                        $gettot_crd1 = "SELECT sum(amountApplied) as amt_adust  FROM creditapplied WHERE charge_list_detail_id_adjust  = '$charge_list_detail_id'  and delete_credit='0' and credit_applied='1'";
                        $gettot_crdQry1 = imw_query($gettot_crd1);
                        $gettot_crdrow1 = imw_fetch_array($gettot_crdQry1);
                        $amt_adust = $gettot_crdrow1['amt_adust'];
                        
                        $gettot_crd7 = "SELECT  sum(amountApplied) as amt_payment FROM creditapplied WHERE charge_list_detail_id = '$charge_list_detail_id' and crAppliedTo='payment' and delete_credit='0' and credit_applied='1'";
                        $gettot_crdQry7 = imw_query($gettot_crd7);
                        $gettot_crdrow7 = imw_fetch_array($gettot_crdQry7);
                        $amt_payment=$gettot_crdrow7['amt_payment'];
                        $credit_total_final=$amt_adust+$amt_payment+$batch_crd_amt_adust;
                        echo $showCurrencySymbol.number_format($credit_total_final,2);
                        
                        $det_adust_qry=imw_query("select sum(payment_amount) as adj_amt 
                                        from account_payments where  
                                        charge_list_detail_id='$charge_list_detail_id'
                                        and payment_type='Adjustment' and del_status!='1'");
                        $det_adust_rec=imw_fetch_array($det_adust_qry);
                        $adj_amt=$det_adust_rec['adj_amt']+$adj_amt_total;		
                    ?>
                </td>
                <td id="paidAmtPrev<?php echo $seq; ?>"  style="color:#009900;font-weight:bold; white-space:nowrap;">
                <?php echo numberFormat($paidForProc,2,'yes'); ?>
                </td>
                <td>
                	<div class="input-group">
                        <div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
                     	<input type="text" name="payNew<?php echo $seq; ?>" id="payNew<?php echo $seq; ?>" class="form-control" style="width:90px;" value="<?php echo "0.00"; ?>" onBlur="return selectChanges('<?php echo $seq; ?>')" onChange="return paymentChange('<?php echo $seq; ?>', '<?php echo $writeOffAmount; ?>')">
                    </div>
                 </td>
                
                <!--<td width="61" align="left" class="text_10">-->
                    <input type="hidden" name="counterIdArr[]" id="counterIdArr[]" value="<?php echo $seq; ?>">
                    <input type="hidden" name="chargeListDetailIdArr[]" id="chargeListDetailIdArr[]" value="<?php echo $charge_list_detail_id; ?>">
                    <input type="hidden" name="paidAmtText<?php echo $seq; ?>" id="paidAmtText<?php echo $seq; ?>" class="form-control" onChange="return checkChkBox(<?php echo $seq; ?>);" value="<?php echo "0.00"; ?>">
                    <input type="hidden" name="adj_amt<?php echo $seq; ?>" id="adj_amt<?php echo $seq; ?>" value="<?php echo $adj_amt;?>">
                <!--</td>-->
                <!-- Credit Paid Pay -->
                <!-- O/s Charges Paid By -->
                <td  id="newBalanceTd<?php echo $seq; ?>" nowrap>
                    <font color="<?php echo ($overPaymentForProc > 0) ? "Green" : "Red";?>">
                        <?php if($NewBalance<0){ echo '0.00'; }else{ if($overPaymentForProc>0) echo '-'.$showCurrencySymbol.number_format($overPaymentForProc,2); else echo $showCurrencySymbol.number_format($NewBalance,2); } ?>
                    </font>
                    <input type="hidden" value="<?php if($NewBalance<0){ echo '0.00'; }else{ if($overPaymentForProc>0) echo '0.00'; else echo $NewBalance; } ?>" name="bal_chk_for_copay" id="bal_chk_for_copay<?php echo $seq; ?>">
              </td>
                <td id="overPaidPrev<?php echo $seq; ?>" style="color:#5D738E;font-weight:bold;"><?php if($overPaymentForProc>0) echo $showCurrencySymbol.number_format($overPaymentForProc,2); else echo $showCurrencySymbol.number_format($overPaymentForProc,2); ?></td>
                <!--<td width="61" align="right" class="text_10">-->
                    <input type="hidden" readonly size="10" name="overPayment<?php echo $seq; ?>" id="overPayment<?php echo $seq; ?>" class="text_10" value="<?php echo $overPaymentForProc; ?>">
                    <input type="hidden" readonly size="10"  name="overPaymentNow<?php echo $seq; ?>" id="overPaymentNow<?php echo $seq; ?>" class="text_10" value="">
                    <input type="hidden" readonly size="10" name="overPayments_chk<?php echo $seq; ?>" id="overPayments_chk<?php echo $seq; ?>" class="text_10" value="<?php echo $overPaymentForProc; ?>">
                <!--</td>-->
                <!-- <td width="60" align="left" class="text_10b">&nbsp;</td> -->
                <!-- O/s Charges Paid By -->
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>
                    <?php
                    $getSubmittedDateStr = "SELECT * FROM submited_record WHERE encounter_id = '$encounter_id' ORDER BY submited_id DESC";
                    $getSubmittedDateQry = imw_query($getSubmittedDateStr);
                    $submittedDateRows = imw_num_rows($getSubmittedDateQry);
                    if($submittedDateRows>0){
                    ?>
                    <select name="hcfaSubmittedDat" id="hcfaSubmittedDat" class="text_10">
                        <?php
                                while($getSubmittedDateRow = imw_fetch_array($getSubmittedDateQry)){			
                                $hcfaSubmittedDate = $getSubmittedDateRow['submited_date'];	
                                $Ins_type = $getSubmittedDateRow['Ins_type'];				
                                    list($hcfaYear, $hcfaMonth, $hcfaDay) = explode("-", $hcfaSubmittedDate);
                                    $hcfaSubmittedDate = date(''.$global_date_format_small.'',mktime(0,0,0,$hcfaMonth,$hcfaDay,$hcfaYear));
                                    if($Ins_type=="primary" || $Ins_type=="Primary"){
                                        $Ins_type_show="Pri";
                                    }else if($Ins_type=="secondary" || $Ins_type=="Secondary"){
                                        $Ins_type_show="Sec";
                                    }else if($Ins_type=="tertiary" || $Ins_type=="Tertiary"){
                                        $Ins_type_show="Tri";
                                    }
                        ?>
                        <option><?php echo $Ins_type_show.'-'.$hcfaSubmittedDate; ?></option>
                        <?php } ?>
                    </select>
                    <?php }else{ echo '-';}?>
                </td>
                <td nowrap><?php echo $write_off_code; ?>&nbsp;</td>
                <td nowrap><?php echo $operatorNamePaid; ?></td>
            
            </tr>						
            <?php
            $arr_all=array();
            $arr_date=array();
            $arr_all_deduct="";
            $deductibleDetails=imw_query("select * from payment_deductible where charge_list_detail_id in($charge_list_detail_id)");
            while($deductibles=imw_fetch_object($deductibleDetails)){
                $deductible_id = $deductibles->deductible_id;
                $chargeDetailId = $deductibles->charge_list_detail_id;							
                $deduct_amount = $deductibles->deduct_amount;
                $deductible_by = $deductibles->deductible_by;
                $deduct_ins_id = $deductibles->deduct_ins_id;
                
                $getInsuranceDetailsQry=imw_query("select * from insurance_companies where id in($deduct_ins_id)");
                $getInsuranceDetails=imw_fetch_object($getInsuranceDetailsQry);
                $nameInsCo = $getInsuranceDetails->in_house_code;
                $deduct_operator_id = $deductibles->deduct_operator_id;
				$operatorNameDeduct = $usr_alias_name[$deduct_operator_id];
                
                $deduct_date = $deductibles->deduct_date;
                $delete_deduct = $deductibles->delete_deduct;
                $delete_deduct_date = $deductibles->delete_deduct_date;
                $deleteRows = '';
                $show_del_style= '';
                if($delete_deduct==1){
                    $show_del_style='class="hide deleted"';
                    $deleteRows = 'id="deleted_rows_id[]"';
                }else{
                    $show_del_style="";
                }
                $arr_all_deduct ='
                
                <tr  '.$deleteRows.'  '.$show_del_style.'>
                    <td colspan="2" align="left" class="text_10b_purpule">';
                    
                        if($delete_deduct!=1){
                        
                    /*$arr_all_deduct .='	
                        <a class="text_10b_purpule" href="javascript:void(0);" onClick="javascript:editDeductible(\''.$deductible_id.'\',\''.$encounter_id.'\');">&nbsp;<img src="../../library/images/edit.png" alt="Edit" border="0"></a>
                        
                        <a class="text_10b_purpule" href="javascript:delDeductible(\''.$deductible_id.'\',\''.$encounter_id.'\');"><img src="../../library/images/del.png" alt="Del" border="0"></a>
                        
                        ';*/
                        $arr_all_deduct .='&nbsp;'; 
                    }else{
                        $arr_all_deduct .='&nbsp;'; 
                    }
                    $arr_all_deduct .='	
                    </td>
                    <td colspan="3" align="left" class="text_10b" width="205" style="padding-left:2px; ">';
                
                        if($delete_deduct==1){
                            list($year, $month, $day)=explode("-", $delete_deduct_date);
                            $delete_deduct_date = date(''.$global_date_format_small.'',mktime(0,0,0,$month,$day,$year));
                            $delete_deduct_date1 = date('m-d-Y',mktime(0,0,0,$month,$day,$year));
                            //echo "Deductible Deleted Date : ".$delete_deduct_date;
                            $arr_all_deduct .='Deductible Deleted : '; 
                            if($deductible_by=='Insurance'){ 
                            $arr_all_deduct .= $nameInsCo;
                            } else{ 
                                $arr_all_deduct .= $deductible_by; 
                            }
                            $deduct_date = $delete_deduct_date;
                            $deduct_date1 = $delete_deduct_date1;
                        }else{
                            list($year, $month, $day)=explode("-", $deduct_date);
                            $deduct_date = date(''.$global_date_format_small.'',mktime(0,0,0,$month,$day,$year));
                            $deduct_date1 = date('m-d-Y',mktime(0,0,0,$month,$day,$year));
                            //echo 'Deductible Date : '.$deduct_date;
                            $arr_all_deduct .='Deductible : '; 
                            if($deductible_by=='Insurance'){ 
                                $arr_all_deduct .= $nameInsCo;
                            } else{ 
                                $arr_all_deduct .= $deductible_by; 
                            }
                            if(strstr($moaQualifier, 'MA18') || strstr($moaQualifier, 'MA07')){
								$show_moaQualifier="MA18";
								if(strstr($moaQualifier, 'MA07')){
									$show_moaQualifier="MA07";
								}												
                                $arr_all_deduct .='&nbsp;&nbsp;'; 
                                if($insCo1NameCode == $paidByCode){														
                                    if($deletePayment!=1){
                                        $arr_all_deduct .="(".$show_moaQualifier." - Forwarded to ".$insCo2NameCode.")"; 
                                    }
                                }
                            }
                        }
                     $deb_sty="";	
                     if($delete_deduct==1){ $deb_sty="style=text-decoration:line-through;color:#FF0000"; } 
                    $arr_all_deduct .=' </td>
                    <td align="left" class="text_10" id="deductTd'.$deductible_id.'"'.$deb_sty.'>'.$showCurrencySymbol.number_format($deduct_amount, 2).'</td>
                    <td colspan="6">&nbsp;</td>
                    <td align="left" nowrap class="text_10"'.$deb_sty.'>'.$deduct_date.'</td>
                    <td colspan="5">&nbsp;</td>
                    <td align="left" class="text_10" '.$deb_sty.'>'.$operatorNameDeduct.'</td>
                </tr>';
                //echo $deduct_date1;
                //echo $arr_all_deduct;
                $arr_all[$deduct_date1][]=$arr_all_deduct;
                $arr_date[]=$deduct_date1;
                
            }
            //print_r($arr_all);
            $arr_all_writeoff ="";
            $getWriteOffStr = "SELECT * FROM paymentswriteoff
                                WHERE patient_id = '$patient_id'
                                AND encounter_id = '$encounter_id'
                                AND charge_list_detail_id = '$charge_list_detail_id'
                                ORDER BY write_off_id DESC";
            $getWriteOffQry = imw_query($getWriteOffStr);
            $countWriteOffRowsCount = imw_num_rows($getWriteOffQry);
            if($countWriteOffRowsCount>0){
                while($getWriteOffRows = imw_fetch_array($getWriteOffQry)){
                    $arr_all_writeoff="";
                    $write_off_id = $getWriteOffRows['write_off_id'];
                    $write_off_by_id = $getWriteOffRows['write_off_by_id'];								
                        $write_off_by = getData('in_house_code', 'insurance_companies', 'id', $write_off_by_id);
                        if($write_off_by==''){ $write_off_by = getData('name', 'insurance_companies', 'id', $write_off_by_id); }

                    $paymentStatusMode = $getWriteOffRows['paymentStatus'];
                    $paymentwrite_off_code_id = $getWriteOffRows['write_off_code_id'];
                    
                    $w_code_qry1 = imw_query("SELECT w_code FROM write_off_code 
                                            WHERE w_id = '$paymentwrite_off_code_id'");							
                    $w_code_row1 = imw_fetch_array($w_code_qry1);
                    $paymentwrite_off_code=$w_code_row1['w_code'];
                    
                        if($getWriteOffRows['write_off_amount']>0){
                            $write_off_amount = $getWriteOffRows['write_off_amount'];
                        }else{
                            if($getWriteOffRows['era_amt']>0 && $getWriteOffRows['delStatus']=='0' && $show_era_wrt_amt>0){
                                $write_off_amount = $show_era_wrt_amt;
                            }else{
                                $write_off_amount = $getWriteOffRows['era_amt'];
                            }
                            
                        }
                        $total_write_off_amount = $total_write_off_amount + $write_off_amount;
                    
                    $write_off_operator_id = $getWriteOffRows['write_off_operator_id'];
                    $write_off_operator = $usr_alias_name[$write_off_operator_id]; 

                    $write_off_date = $getWriteOffRows['write_off_date'];
                        list($yearWO, $monthWO, $dayWO)=explode("-", $write_off_date);
                        //$write_off_date = $monthWO."-".$dayWO."-".$yearWO;
                        $write_off_date = date(''.$global_date_format_small.'',mktime(0,0,0,$monthWO,$dayWO,$yearWO));
                        $write_off_date1 = date('m-d-Y',mktime(0,0,0,$monthWO,$dayWO,$yearWO));
                    $delStatus = $getWriteOffRows['delStatus'];
                    if($delStatus!=0){
                        $write_off_del_date = $getWriteOffRows['write_off_del_date'];
                            list($yearDelWO, $monthDelWO, $dayDelWO)=explode("-", $write_off_del_date);
                            //$write_off_del_date = $monthDelWO."-".$dayDelWO."-".$yearDelWO;
                            $write_off_del_date = date(''.$global_date_format_small.'',mktime(0,0,0,$monthDelWO,$dayDelWO,$yearDelWO));
                    }
                $deleteRows = '';
                $show_del_style= '';
                if($delStatus==1){
                    $show_del_style='class="hide deleted"';
                    $deleteRows = 'id="deleted_rows_id[]"';
                }else{
                    $show_del_style="";
                }
                $show_cas_code=show_cas_code_fun($getWriteOffRows['CAS_type'],$getWriteOffRows['CAS_code']);
                $arr_all_writeoff .='	
                <tr '.$deleteRows.'   '.$show_del_style.'>
                    <td align="left" colspan="2" class="text_10b_purpule">';
                    
                    if($delStatus==0){
                    /*$arr_all_writeoff .='	
                        <a href="javascript:editWriteOff(\''.$write_off_id.'\',\''.$encounter_id.'\',\''.$charge_list_detail_id.'\');" class="text_10b_purpule">&nbsp;<img src="../../library/images/edit.png" alt="Edit" border="0"></a>
                        
                        <a href="javascript:delWriteOff(\''.$write_off_id.'\',\''.$encounter_id.'\',\''.$charge_list_detail_id.'\');" class="text_10b_purpule"><img src="../../library/images/del.png" alt="Del" border="0"></a>';
                        */
                        $arr_all_writeoff .= '&nbsp;';
                    }else{
                        $arr_all_writeoff .= '&nbsp;';
                    }	
                    $arr_all_writeoff .= '
                    </td>
                    <td colspan="3" width="205">
                        <table cellpadding="0" cellspacing="0" width="205" border="0">
                            <tr>';
                             
                                if(($write_off_del_date!='00-00-0000') && ($write_off_del_date!='') && ($delStatus=='1')){
                                $arr_all_writeoff .= '<td  align="left" style="padding-left:2px; border:none;" class="text_10b">';
                                    if($paymentStatusMode) {
                                        $arr_all_writeoff .= $paymentStatusMode; 
                                     }else{
                                        $arr_all_writeoff .= 'Write Off';
                                     } 
                                     if($write_off_by){ 
                                        $arr_all_writeoff .= " : ".$write_off_by;
                                     }else{
                                         $arr_all_writeoff .= " : Patient"; 
                                      }  
                                      $arr_all_writeoff .='</td>';
                                    
                                }else{
                                    $wri_sty="";
                                     if($delStatus=='1') $wri_sty="text-decoration:line-through;color:#FF0000;"; 
                                     $arr_all_writeoff .='
                                        <td  align="left" style="padding-left:2px;'.$wri_sty.' border:none;" class="text_10b">';
                                         if($paymentStatusMode){ 
                                            $arr_all_writeoff .= $paymentStatusMode; 
                                        }else{ 
                                            $arr_all_writeoff .= 'Write Off';
                                        } 
                                        if($write_off_by){ 
                                            $arr_all_writeoff .= " : ".$write_off_by; 
                                        }else{ 
                                            $arr_all_writeoff .= " : Patient"; 
                                         }
                                    $arr_all_writeoff .=' </td>';
                                }
                                
                            $arr_all_writeoff .='</tr>
                        </table>
                    </td>
                    <td  class="text_10b" >&nbsp;';
                        
                        if(strstr($moaQualifier, 'MA18') || strstr($moaQualifier, 'MA07')){
							$show_moaQualifier="MA18";
							if(strstr($moaQualifier, 'MA07')){
								$show_moaQualifier="MA07";
							}													
                            $arr_all_writeoff .='&nbsp;&nbsp;&nbsp;&nbsp;';
                            if($insCo1NameCode == $paidByCode){														
                                if($deletePayment!=1){
                                    $arr_all_writeoff .='('.$show_moaQualifier.' - Forwarded to '.$insCo2NameCode.')';
                                }
                            }
                        }
                    $wri_sty1="";
                    if($delStatus=='1') $wri_sty1="style=text-decoration:line-through;color:#FF0000;";
                    $arr_all_writeoff .='</td>
                        <td align="left" class="text_10"'.$wri_sty1.'>'.$showCurrencySymbol.number_format($write_off_amount, 2).'</td>
                    <td align="left" colspan="5" class="text_10" '.$wri_sty1.'>'.$show_cas_code.'</td>
                    <td align="left" nowrap class="text_10" '.$wri_sty1.'>'.$write_off_date.'</td>
                    <td align="left" colspan="4" class="text_10" '.$wri_sty1.'>&nbsp;</td>
                    <td align="left" class="text_10" '.$wri_sty1.'>'.$paymentwrite_off_code.'</td>
                    <td align="left" class="text_10" '.$wri_sty1.'>'.$write_off_operator.'</td>
                </tr>';
                $arr_all[$write_off_date1][]=$arr_all_writeoff;
                $arr_date[]=$write_off_date1;
                }
            }
            //print_r($arr_all);
            $arr_all_acc_pay ="";
            $ret_chk_arr=array();
            $getAccPayStr = "SELECT * FROM account_payments
                                WHERE patient_id = '$patient_id'
                                AND encounter_id = '$encounter_id'
                                AND charge_list_detail_id = '$charge_list_detail_id'
                                ORDER BY payment_date DESC";
            $getAccPayQry = imw_query($getAccPayStr);
            $countAccPayRowsCount = imw_num_rows($getAccPayQry);
            if($countAccPayRowsCount>0){
                while($getAccPayRows = imw_fetch_array($getAccPayQry)){
                    $arr_all_acc_pay="";
                    $c_cc_number="";
                    $id = $getAccPayRows['id'];
                    $ins_id = $getAccPayRows['ins_id'];
                    $payment_amount = $getAccPayRows['payment_amount'];
                    $payment_by = $getAccPayRows['payment_by'];
                    $payment_method = $getAccPayRows['payment_method'];
                    $check_number = $getAccPayRows['check_number'];
                    $cc_type = $getAccPayRows['cc_type'];
                    $cc_number = $getAccPayRows['cc_number'];
                    $cc_exp_date = $getAccPayRows['cc_exp_date'];
                    
                    if($payment_method=='Check' || $payment_method=='EFT' || $payment_method=='Money Order' || $payment_method=='VEEP'){
                        $c_cc_number=$check_number;
                        $cc_exp_date="";
                    }
                    if($payment_method=='Credit Card'){
                        $c_cc_number=$cc_number;
                        $payment_method = 'CC';
                    }
                    list($year, $month, $day)=explode("-", $getAccPayRows['payment_date']);
                    $payment_date = date(''.$global_date_format_small.'',mktime(0,0,0,$month,$day,$year));
                    $payment_date_for_arr = date('m-d-Y',mktime(0,0,0,$monthWO,$dayWO,$yearWO));
                    
                    $operator_id = $getAccPayRows['operator_id'];
                    $payment_code_id = $getAccPayRows['payment_code_id'];
                    $payment_type = $getAccPayRows['payment_type'];
                    $del_status = $getAccPayRows['del_status'];
                    
                    if($payment_type=='Returned Check' && $del_status==0){
                        $ret_chk_arr[]=$check_number;
                    }
                                                    
                    $acc_pay_ins = getData('in_house_code', 'insurance_companies', 'id', $ins_id);
                    if($acc_pay_ins==''){ $acc_pay_ins = getData('name', 'insurance_companies', 'id', $ins_id); }
                    
                    $a_code_qry = imw_query("SELECT a_code FROM adj_code 
                                            WHERE a_id = '$payment_code_id'");							
                    $a_code_row = imw_fetch_array($a_code_qry);
                    $payment_code=$a_code_row['a_code'];
                    
					$operator_name = $usr_alias_name[$operator_id]; 

                $deleteRows = '';
                $show_del_style= '';
                if($del_status==1){
                    $show_del_style='class="hide deleted"';
                    //$deleteRows = 'id="deleted_rows_id[]"';
                }else{
                    $show_del_style="";
                }
                
                $arr_all_acc_pay .='	
                <tr '.$deleteRows.' style="height:25px;background:#D8EAFE;" '.$show_del_style.'>
                    <td style="text-align:left;" colspan="2" class="text_10b_purpule">';
                    
                    if($del_status==0){
                        $arr_all_acc_pay .= '&nbsp;';									
                    }else{
                        $arr_all_acc_pay .= '&nbsp;';
                    }	
                    $arr_all_acc_pay .= '
                    </td>
                    <td colspan="3" style="width:205px;">
                        <table style="width:205px;" class="table_collapse">
                            <tr>';
                             
                                if(($payment_date!='00-00-0000') && ($payment_date!='') && ($del_status=='1')){
                                $arr_all_acc_pay .= '<td style="padding-left:2px;text-align:left;border:none;" class="text_10b">';
                                    if($payment_type) {
                                        $arr_all_acc_pay .= $payment_type; 
                                     }
                                     if($acc_pay_ins){ 
                                        $arr_all_acc_pay .= " : ".$acc_pay_ins;
                                     }else{
                                         $arr_all_acc_pay .= " : Patient"; 
                                      }  
                                      $arr_all_acc_pay .='</td>';
                                    
                                }else{
                                    $acc_pay_sty="";
                                     if($del_status=='1') $acc_pay_sty="text-decoration:line-through;color:#FF0000;"; 
                                     $arr_all_acc_pay .='
                                        <td style="padding-left:2px;text-align:left;border:none;'.$acc_pay_sty.'" class="text_10b">';
                                         if($payment_type){ 
                                            $arr_all_acc_pay .= $payment_type; 
                                        }else{ 
                                            $arr_all_acc_pay .= 'Write Off';
                                        } 
                                        if($acc_pay_ins){ 
                                            $arr_all_acc_pay .= " : ".$acc_pay_ins; 
                                        }else{ 
                                            $arr_all_acc_pay .= " : Patient"; 
                                         }
                                    $arr_all_acc_pay .=' </td>';
                                }
                                
                            $arr_all_acc_pay .='</tr>
                        </table>
                    </td>
                    <td colspan="3" class="text_10b">&nbsp;';
                    $acc_pay_sty1="";
                    if($del_status=='1') $acc_pay_sty1="text-decoration:line-through;color:#FF0000;";
                    $arr_all_acc_pay .='</td>
                    <td style="text-align:left;'.$acc_pay_sty1.'" colspan="4" class="text_10">'.$showCurrencySymbol.number_format($payment_amount, 2).'</td>
                    <td style="text-align:left;white-space:nowrap;'.$acc_pay_sty1.'" class="text_10">'.$payment_date.'</td>
                    <td style="text-align:left;'.$acc_pay_sty1.'">'.$payment_method.'</td>
                    <td style="text-align:left;'.$acc_pay_sty1.'">'.$c_cc_number.'</td>
                    <td style="text-align:left;'.$acc_pay_sty1.'">'.$cc_exp_date.'</td>
                    <td style="text-align:left;'.$acc_pay_sty1.'">&nbsp;</td>
                    <td style="text-align:left;'.$acc_pay_sty1.'">&nbsp;</td>
                    <td style="text-align:left;'.$acc_pay_sty1.'">&nbsp;'.$operator_name.'</td>
                </tr>';
                $arr_all[$payment_date_for_arr][]=$arr_all_acc_pay;
                $arr_date[]=$payment_date_for_arr;
                }
            }
            $getDeniedDetailsStr = "SELECT * FROM deniedpayment 
                                    WHERE patient_id = '$patient_id'
                                    AND encounter_id = '$encounter_id'
                                    AND charge_list_detail_id = '$charge_list_detail_id'
                                    ORDER BY deniedId DESC";
            $getDeniedDetailsQry = imw_query($getDeniedDetailsStr);
            $rowsDeniedCount = imw_num_rows($getDeniedDetailsQry);
            if($rowsDeniedCount>0){
                while($getDeniedDetailsRow = imw_fetch_array($getDeniedDetailsQry)){
                    $deniedById="";
                    $arr_all_denied="";
                    $deniedId = $getDeniedDetailsRow['deniedId'];									
                    $deniedBy = $getDeniedDetailsRow['deniedBy'];									
                    if($deniedBy=='Insurance'){	
                        $deniedBy = "Ins.";
                    }
                    $deniedById = $getDeniedDetailsRow['deniedById'];
                        $deniedByName = getData('in_house_code', 'insurance_companies', 'id', $deniedById);
                        if($deniedByName==''){ $deniedByName = getData('name', 'insurance_companies', 'id', $deniedById); }
                        
                    $deniedDate = $getDeniedDetailsRow['deniedDate'];
                        list($yearDenied, $monthDenied, $dayDenied) = explode("-", $deniedDate);
                        //$deniedDate = $monthDenied."-".$dayDenied."-".$yearDenied;
                        $deniedDate = date(''.$global_date_format_small.'',mktime(0,0,0,$monthDenied,$dayDenied,$yearDenied));
                        $deniedDate1 = date('m-d-Y',mktime(0,0,0,$monthDenied,$dayDenied,$yearDenied));
                        
                    $deniedAmount = number_format($getDeniedDetailsRow['deniedAmount'], 2);
                    $denialOperatorId = $getDeniedDetailsRow['denialOperatorId'];
                    $denialOperatorName = $usr_alias_name[$denialOperatorId];
                        
                    $denialDelStatus = $getDeniedDetailsRow['denialDelStatus'];
                    $denialDelDate = $getDeniedDetailsRow['denialDelDate'];
                        list($yearDelDenied, $monthDelDenied, $dayDelDenied) = explode("-", $denialDelDate);
                        //$denialDelDate = $monthDelDenied."-".$dayDelDenied."-".$yearDelDenied;				
                        $denialDelDate = date(''.$global_date_format_small.'',mktime(0,0,0,$monthDelDenied,$dayDelDenied,$yearDelDenied));					
                
                $deleteRows = '';
                $show_del_style= '';
                if($denialDelStatus==1){
                    $show_del_style='class="hide deleted"';
                    $deleteRows = 'id="deleted_rows_id[]"';
                }else{
                    $show_del_style="";
                }
                $show_cas_code=show_cas_code_fun($getDeniedDetailsRow['CAS_type'],$getDeniedDetailsRow['CAS_code']);
                $arr_all_denied .='
                <tr '.$deleteRows.'   '.$show_del_style.'>
                    <td align="left" colspan="2" id="editDel'.$deniedId.'" class="text_10b_purpule">';
                    
                        if($denialDelStatus!='1'){
                            /*$arr_all_denied .='
                            <a href="javascript:EditDenial(\''.$deniedId.'\',\''.$encounter_id.'\');" class="text_10b_purpule">
                                &nbsp;<img src="../../library/images/edit.png" alt="Edit" border="0">
                            </a>
                            
                            <a href="javascript:delDenial(\''.$deniedId.'\',\''.$encounter_id.'\');" class="text_10b_purpule">
                                <img src="../../library/images/del.png" alt="Del" border="0">
                            </a>';*/
                            $arr_all_denied .= '&nbsp;';
                            
                        }else{
                            $arr_all_denied .= '&nbsp;';
                        }
                    $arr_all_denied .='</td>';
                    
                    if(($denialDelStatus!=0) && ($denialDelDate!='') &&  ($denialDelDate!='00-00-0000')){
                    $arr_all_denied .='
                        <td align="left" style="padding-left:2px;" colspan="6" class="text_10b">
                        Claim Denial Deleted : ';
                         if($deniedByName){
                            $arr_all_denied .= $deniedByName; 
                          }else{ 
                            $arr_all_denied .= $deniedBy; 
                          }
						if(strstr($moaQualifier, 'MA18') || strstr($moaQualifier, 'MA07')){
							$show_moaQualifier="MA18";
							if(strstr($moaQualifier, 'MA07')){
								$show_moaQualifier="MA07";
							}												
							$arr_all_denied .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
							if($insCo1NameCode == $paidByCode){														
								if($deletePayment!=1){
									$arr_all_denied .='('.$show_moaQualifier.' - Forwarded to '.$insCo2NameCode.')';
								}
							}
						}
                                                                
                        $arr_all_denied .='</td>';
                    
                    }else{
                    $den_sty="";
                     if($denialDelStatus==1){ $den_sty="text-decoration:line-through;color:#FF0000"; } 
                    $arr_all_denied .='
                        <td align="left" style="padding-left:2px;'.$den_sty.'" colspan="6" class="text_10b"> Claim Denial : ';
                            if($deniedByName){
                                $arr_all_denied .= $deniedByName; 
                            }else{
                                $arr_all_denied .= $deniedBy;
                                } 
                        
                            if(strstr($moaQualifier, 'MA18') || strstr($moaQualifier, 'MA07')){
								$show_moaQualifier="MA18";
								if(strstr($moaQualifier, 'MA07')){
									$show_moaQualifier="MA07";
								}													
                                $arr_all_denied .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                if($insCo1NameCode == $paidByCode){														
                                    if($deletePayment!=1){
                                        $arr_all_denied .= '('.$show_moaQualifier.' - Forwarded to '.$insCo2NameCode.')';
                                    }
                                }
                            }
                                                                    
                        $arr_all_denied .='</td>';
                    
                    } 
                    $den_sty1="";
                    if($denialDelStatus==1){ $den_sty1="style=text-decoration:line-through;color:#FF0000"; } 
                    $arr_all_denied .='
                    <td align="left" id="tdDenialAmt'.$deniedId.'" class="text_10"'.$den_sty1.'>'.$showCurrencySymbol.$deniedAmount.'</td>
                    <td align="left" colspan="3" class="text_10" '.$den_sty1.'>'.$show_cas_code.'</td>
                    <td align="left" class="text_10" '.$den_sty1.'>'.$deniedDate.'</td>
                    <td align="left" colspan="5" class="text_10" '.$den_sty1.'>&nbsp;</td>
                    <td align="left" class="text_10" '.$den_sty1.'> '.$denialOperatorName.'</td>
                </tr>';
                $arr_all[$deniedDate1][]=$arr_all_denied;
                $arr_date[]=$deniedDate1;
                }
            }
            
            //print_r($arr_all);
            $arr_all_crd="";
            $getCreditsStr = "Select * From creditapplied 
                                WHERE (
                                    charge_list_detail_id = '$charge_list_detail_id' 
                                        || 
                                    charge_list_detail_id_adjust = '$charge_list_detail_id'
                                    ) and  credit_applied='1'";
            $getCreditsQry = imw_query($getCreditsStr);
            $getcountCrRows = imw_num_rows($getCreditsQry);
                if($getcountCrRows){
                    while($getCreditsRows = imw_fetch_array($getCreditsQry)){
                        $amountApplied = number_format($getCreditsRows['amountApplied'], 2);
                        $dateApplied = $getCreditsRows['dateApplied'];
                            list($crAppYear, $crAppMonth, $crAppDay) = explode("-", $dateApplied);
                            //$dateApplied = $crAppMonth."-".$crAppDay."-".$crAppYear;
                            $dateApplied = date(''.$global_date_format_small.'',mktime(0,0,0,$crAppMonth,$crAppDay,$crAppYear));
                            $dateApplied1 = date('m-d-Y',mktime(0,0,0,$crAppMonth,$crAppDay,$crAppYear));
                        $operatorApplied = $getCreditsRows['operatorApplied'];
                        $type_credit=$getCreditsRows['type'];
                        $ins_case=$getCreditsRows['ins_case'];
                        $payment_mode_credit=$getCreditsRows['payment_mode'];
                        if($payment_mode_credit=='Check' || $payment_mode_credit=='EFT' || $payment_mode_credit=='Money Order' || $payment_mode_credit=='VEEP'){
                            $checkCcNumber_credit=$getCreditsRows['checkCcNumber'];
                        }
                        if($payment_mode_credit=='Credit Card'){
                            $cc_type=strtoupper(substr($getCreditsRows['creditCardCo'], 0, 2));
                            $checkCcNumber_credit=$cc_type.'-'.$getCreditsRows['creditCardNo'];
                            $expirationDateCc=$getCreditsRows['expirationDateCc'];
                        }
                        $crAppId=$getCreditsRows['crAppId'];
                        $credit_note=$getCreditsRows['credit_note'];
                        $delete_credit=$getCreditsRows['delete_credit'];
                        $crAppliedToEncId=$getCreditsRows['crAppliedToEncId'];
                        $delete_credit=$getCreditsRows['delete_credit'];
                        $modify=$getCreditsRows['modify'];
                        $charge_list_detail_id_adjust=$getCreditsRows['charge_list_detail_id_adjust'];
                        $crAppliedTo=$getCreditsRows['crAppliedTo'];
                        $charge_list_detail_id_chk=$getCreditsRows['charge_list_detail_id'];
                        $patient_id_adjust=$getCreditsRows['patient_id_adjust'];
                        $patient_id_chk=$getCreditsRows['patient_id'];
                        $crAppliedToEncId_adjust=$getCreditsRows['crAppliedToEncId_adjust'];
                        if($type_credit=='Insurance'){
                            //$credit_by = getData('name', 'insurance_companies', 'id', $ins_case);
                            $getInsCoStr = "SELECT * FROM insurance_companies WHERE id = '$ins_case'";
                            $getInsCoQry = imw_query($getInsCoStr);
                            $getInsCoRow = imw_fetch_array($getInsCoQry);
                            $insCoCode = $getInsCoRow['in_house_code'];
                            if($insCoCode<>""){
                                $credit_by=$insCoCode;
                            }else{
                                $credit_by = $getInsCoRow['name'];
                            }
                        }else{
                            $credit_by=$type_credit;
                        }
                        if($credit_by==""){
                            $credit_by="Patient";
                        }
						$crOperName = $usr_alias_name[$operatorApplied];
                        $credit_note1=htmlentities($credit_note);
                        if($crAppliedToEncId_adjust>0){
                            $getpat_to_qry=imw_query("select * from patient_data where pid in($patient_id_adjust)");
                            $getpat_to=imw_fetch_object($getpat_to_qry);
                            $fname_to = $getpat_to->fname;
                            $lname_to = $getpat_to->lname;
                            $mname_to = $getpat_to->mname;
                            $patientName_to = ucwords(trim($lname_to.", ".$fname_to));
                            
                            $getpat_frm_qry=imw_query("select * from patient_data where pid in($patient_id_chk)");
                            $getpat_frm=imw_fetch_object($getpat_frm_qry);
                            $fname_frm = $getpat_frm->fname;
                            $lname_frm = $getpat_frm->lname;
                            $mname_frm = $getpat_frm->mname;
                            $patientName_frm = ucwords(trim($lname_frm.", ".$fname_frm));
                            //echo $patient_id_adjust.'=='.$patient_id_chk;
                            if($patient_id_adjust==$patient_id_chk){
                                if($credit_note1<>""){
                                    $credit_note1=$credit_note1;
                                }
                                $note="Adjustment Credit : $credit_by $credit_note1";
                                $note_debit="Adjustment Debit : $credit_by $credit_note1";
                            }else{
                                $note="Adjustment Credit : $credit_by  from  $patientName_frm - $patient_id_chk  $credit_note1";
                                $note_debit="Adjustment Debit : $credit_by  to $patientName_to – $patient_id_adjust  $credit_note1";
                            }
                        }else{
                            $note="Adjustment Credit : $credit_by $credit_note1";
                            $note_debit="Adjustment Debit : $credit_by $credit_note1";
                        }
                        $deleteRows = '';
                        $show_del_style= '';
                        if($delete_credit==1){
                            $show_del_style='class="hide deleted"';
                            $deleteRows = 'id="deleted_rows_id[]"';
                        }else{
                            $show_del_style="";
                        }
                        if($charge_list_detail_id_chk==$charge_list_detail_id  && $crAppliedTo=='payment'){
                        
                            $arr_all_crd .='
                            <tr '.$deleteRows.'  '.$show_del_style.'>
                              <td colspan="2" align="left" class="text_10b_purpule">';
                                    
                                        if($delete_credit!=1){
                                            /*$arr_all_crd .='<a class="text_10b_purpule" href="javascript:void(0);" onclick="javascript:editcredit(\''.$crAppId.'\',\''.$crAppliedToEncId.'\');" >&nbsp;<img src="../../library/images/edit.png" alt="Edit" border="0"></a>
                                            
                                            <a class="text_10b_purpule" href="javascript:void(0);" onclick="javascript:delcredit(\''.$crAppId.'\',\''.$crAppliedToEncId.'\',\''.$chargeListId.'\');"><img src="../../library/images/del.png" alt="Del" border="0"></a>';
                                            */
                                            $arr_all_crd .='&nbsp;';
                                        }else{
                                            $arr_all_crd .='&nbsp;';
                                        }
                                 $crd_sty="";		
                                 if($delete_credit==1){ $crd_sty="style=text-decoration:line-through;color:#FF0000"; }
                                $arr_all_crd .='</td>
                                <td colspan="5" class="text_10b" align="left" width="325">
                                    Refund : '.$credit_by.'&nbsp;'.htmlentities($credit_note).'
                                </td>
                                <td  class="text_10" align="left" '.$crd_sty.'>'.$showCurrencySymbol.$amountApplied.'</td>
                                <td colspan="4">&nbsp;</td>
                                <td class="text_10" align="left" '.$crd_sty.'>'.$dateApplied.'</td>
                                <td class="text_10" align="left" '.$crd_sty.'>'.$payment_mode_credit.'</td>
                                <td class="text_10" align="left" '.$crd_sty.'>'.$checkCcNumber_credit.'</td>
                                <td class="text_10" align="left" '.$crd_sty.'>'.$expirationDateCc.'</td>
                                <td >&nbsp;</td>
                                <td >&nbsp;</td>
                                <td class="text_10" align="left" '.$crd_sty.'>'.$crOperName.'</td>
                            </tr>';
                            
                        }else if($charge_list_detail_id_adjust==$charge_list_detail_id && $crAppliedTo=='adjustment'){
                        
                            $arr_all_crd .='<tr '.$deleteRows.'   '.$show_del_style.'>
                              <td colspan="2" align="left" class="text_10b_purpule">';
                                    
                                    if($delete_credit!=1){
                                        
                                        /*$arr_all_crd .='<a class="text_10b_purpule" href="javascript:void(0);" onclick="javascript:editcredit(\''.$crAppId.'\',\''.$crAppliedToEncId.'\');" >&nbsp;<img src="../../library/images/edit.png" alt="Edit" border="0"></a>
                                        
                                        <a class="text_10b_purpule" href="javascript:void(0);" onclick="javascript:delcredit(\''.$crAppId.'\',\''.$crAppliedToEncId.'\',\''.$chargeListId.'\');"><img src="../../library/images/del.png" alt="Del" border="0"></a>';
                                        */
                                        $arr_all_crd .= '&nbsp;';
                                    }else{
                                        $arr_all_crd .= '&nbsp;';
                                    }
                                $crd_sty="";
                                 if($delete_credit==1){ $crd_sty="style=text-decoration:line-through;color:#FF0000";}
                                $arr_all_crd .='</td>
                                <td colspan="5" class="text_10b" align="left"  width="325">
                                    '.$note.'
                                </td>
                                <td  class="text_10" align="left"'.$crd_sty.'>'.$showCurrencySymbol.$amountApplied.'</td>
                                <td colspan="4">&nbsp;</td>
                                <td class="text_10" align="left" '.$crd_sty.'>'.$dateApplied.'</td>
                                <td class="text_10" align="left" '.$crd_sty.'>&nbsp;';
                                if($checkCcNumber_credit<>""){
                                    $arr_all_crd .=$payment_mode_credit;
                                }
                                $arr_all_crd .='</td>
                                <td class="text_10" align="left"'.$crd_sty.'>&nbsp;'.$checkCcNumber_credit.'</td>
                                <td class="text_10" align="left" '.$crd_sty.'>'.$expirationDateCc.'</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td class="text_10" align="left"'.$crd_sty.'>'.$crOperName.'</td>
                            </tr>';
                        
                        }
                        
                        if($crAppliedTo=='adjustment' && $charge_list_detail_id_adjust<>$charge_list_detail_id && $charge_list_detail_id_chk==$charge_list_detail_id){
                         $crd_sty1="";
                         if($delete_credit==1){ $crd_sty1="style=text-decoration:line-through;color:#FF0000";}
                        $arr_all_crd .='
                        <tr '.$deleteRows.'   '.$show_del_style.'>
                                <td colspan="2" align="left" class="text_10b">&nbsp;</td>
                                <td colspan="5" class="text_10b" align="left"  width="325">
                                    '.$note_debit.'
                                </td>
                                <td  class="text_10" align="left"'.$crd_sty1.'>'.$showCurrencySymbol.$amountApplied.'</td>
                                <td colspan="4">&nbsp;</td>
                                <td class="text_10" align="left"'.$crd_sty1.'>'.$dateApplied.'</td>
                                <td class="text_10" align="left"'.$crd_sty1.'>'.$payment_mode_credit.'</td>
                                <td class="text_10" align="left"'.$crd_sty1.'>'.$checkCcNumber_credit.'</td>
                                <td class="text_10" align="left" '.$crd_sty1.'>'.$expirationDateCc.'</td>
                                <td >&nbsp;</td>
                                <td>&nbsp;</td>
                                <td class="text_10" align="left"'.$crd_sty1.'>'.$crOperName.'</td>

                            </tr>';
                        
                        }
                        $arr_all[$dateApplied1][]=$arr_all_crd;
                        $arr_date[]=$dateApplied1;
                        $arr_all_crd="";
                        $dateApplied1="";
                    }
                }
                //print_r($arr_date);
                
                // payment paid
                $countPayments = 0;
                $getPaymentDetailsStr = "SELECT * FROM
                                        patient_chargesheet_payment_info a,
                                        patient_charges_detail_payment_info b
                                        WHERE a.payment_id = b.payment_id
                                        AND b.charge_list_detail_id = '$charge_list_detail_id'";
                $getPaymentDetailsQry = imw_query($getPaymentDetailsStr);
                $getRowsCount = imw_num_rows($getPaymentDetailsQry);
                if($getRowsCount>0){
                    while($getPaymentDetailsRows = imw_fetch_array($getPaymentDetailsQry)){
                        $arr_all_pay="";
                        ++$countPayments;																		
                        $payment_id = $getPaymentDetailsRows['payment_id'];
                        $paidForProc = $getPaymentDetailsRows['paidForProc'];
                        $deduct_amount = $getPaymentDetailsRows['deduct_amount'];
                        $paidBy = $getPaymentDetailsRows['paidBy'];
                        $paidOfDateNew = $getPaymentDetailsRows['date_of_payment'];
                        $overPayment = $getPaymentDetailsRows['overPayment'];
                        $paymentClaims = $getPaymentDetailsRows['paymentClaims'];
                        
                        if($paymentClaims=="Deposit"){
                            $paymentClaims_pay="Deposit";
                        }else{
                            $paymentClaims_pay="Payment";
                        }
                            list($yy, $mm, $dd) = explode("-", $paidOfDateNew);
                            
                            //$paidOfDateNew = $mm.'-'.$dd.'-'.$yy;
                             $paidOfDateNew = date(''.$global_date_format_small.'',mktime(0,0,0,$mm,$dd,$yy));
                             $paidOfDateNew1 = date('m-d-Y',mktime(0,0,0,$mm,$dd,$yy));
                        $payment_details_id = $getPaymentDetailsRows['payment_details_id'];
                        $deletePayment = $getPaymentDetailsRows['deletePayment'];
                        $expirationDate = $getPaymentDetailsRows['expirationDate'];
                        $deleteDate = $getPaymentDetailsRows['deleteDate'];
                            if($deleteDate!='0000-00-00'){
                                list($delYear, $delMonth, $delDay) = explode("-", $deleteDate);
                                //$deleteDate = "Deleted Date : ".$delMonth."-".$delDay."-".$delYear;
                                 //$deleteDate = "Deleted Date : ".date('m-d-y',mktime(0,0,0,$delMonth,$delDay,$delYear));
                                 $deleteDate = "Deleted  : ";
                            }else{
                                $deleteDate = "";
                            }
                        
                        $modified_date = $getPaymentDetailsRows['modified_date'];
                        $modified_by = $getPaymentDetailsRows['modified_by'];
                        $modifiedBy = $usr_alias_name[$modified_by];
                        if($modified_date=='0000-00-00'){
                            $modified_date = '';
                        }else{
                            list($modYear, $modMonth, $modDay) = explode("-", $modified_date);
                            $modified_date = '&nbsp;Modified Date - '.$modMonth."-".$modDay."-".$modYear.'&nbsp;By&nbsp;'.$modifiedBy;
                        }
                        //
                        
                        $insProviderId = $getPaymentDetailsRows['insProviderId'];
                            //-------------------- GETTING INS. CO. NAME --------------------//
                            if(($insProviderId!=0) && ($insProviderId!="")){
                                $getInsCoStr = "SELECT * FROM insurance_companies WHERE id = '$insProviderId'";
                                $getInsCoQry = imw_query($getInsCoStr);
                                $getInsCoRow = imw_fetch_array($getInsCoQry);
                                $insCoCode = $getInsCoRow['in_house_code'];
                                $insCoName = $getInsCoRow['name'];
                            }
                            if((!$insCoCode) || ($insCoCode=='')){
                                $insCoCode = $insCoName;
                            }
                            //-------------------- GETTING INS. CO. NAME --------------------//
                        if($paidBy == 'Insurance'){
                            $paidByCode = $insCoCode;
                            if(strlen($paidByCode)>8){
                                //$paidByCode = substr($paidByCode, 0, 8)."..";
                                $paidByCode = $paidByCode;
                            }
                        }
                        if($paidBy == 'Patient'){
                            $paidByCode = 'Patient';
                        }
                        if($paidBy == 'Res. Party'){
                            $paidByCode = 'Res. Party';
                        }
                        $paymentMethod = $getPaymentDetailsRows['payment_mode'];
                        $operatorId = $getPaymentDetailsRows['operatorId'];
						$operatorName = $usr_alias_name[$operatorId];
                        $paidDate = $getPaymentDetailsRows['paidDate'];
                            list($paidYear, $paidMon, $paidDay) = explode("-", $paidDate);
                            //$paidDate = $paidMon.'-'.$paidDay.'-'.$paidYear;
                            $paidDate = date('m-d-y',mktime(0,0,0,$paidMon,$paidDay,$paidYear));
                        if($paymentMethod=='Check' || $paymentMethod=='EFT' || $paymentMethod=='Money Order' || $paymentMethod=='VEEP'){
                            $cCChkNumber = $getPaymentDetailsRows['checkNo'];
                        }
                        $creditCardCo = strtoupper(substr($getPaymentDetailsRows['creditCardCo'], 0, 2));
                        if($paymentMethod=='Credit Card'){
                            $paymentMethod = 'CC';
                            $cCChkNumber = $getPaymentDetailsRows['creditCardNo'];
                            $expDate = $getPaymentDetailsRows['expirationDate'];
                            $ccNoLength = strlen($cCChkNumber);
                            if($ccNoLength>6){
                                $cCChkNumber = strrev($cCChkNumber);
                                $cCChkNumber = substr($cCChkNumber, 0, 4);
                                $cCChkNumber = "xx".strrev($cCChkNumber);
                                $cCChkNumber = $creditCardCo.' - '.$cCChkNumber;
                            }
                            
                            if($deletePayment!=1){
                                $expirationDate = $getPaymentDetailsRows['expirationDate'];
                            }
                        }else{
                            $expirationDate = '';
                        }									
                        ++$seque;
                            if($overPayment){
                                $paidProc = $paidForProc + $overPayment;
                            }else{
                                $paidProc = $paidForProc;
                            }
                            $deleteRows = '';
                            $show_del_style= '';
                            if($deletePayment==1){
                                $show_del_style='class="hide deleted"';
                                $deleteRows = 'id="deleted_rows_id[]"';
                            }else{
                                $show_del_style="";
                            }
                            $show_cas_code=show_cas_code_fun($getPaymentDetailsRows['CAS_type'],$getPaymentDetailsRows['CAS_code']);
                            $arr_all_pay .='
                            <tr '.$deleteRows.'   '.$show_del_style.'>
                                <td colspan="2" align="left" class="text_10b">
                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tr>';
                                            if($deletePayment!=1){ 
                                            /*$arr_all_pay .='
                                            <td class="text_10b_purpule" nowrap>
                                                <a href="javascript:editPaymentFn(\''.$payment_details_id.'\',\''.$payment_id.'\',\''.$encounter_id.'\');" class="text_10b_purpule">
                                                    <img src="../../library/images/edit.png" alt="Edit" border="0">
                                                </a>
                                                
                                                <a href="javascript:delPaymentId(\''.$payment_id.'\',\''.$encounter_id.'\',\''.$paidForProc.'\',\''.$payment_details_id.'\',\''.$overPaymentForProc.'\');" class="text_10b_purpule">
                                                    <img src="../../library/images/del.png" alt="Del" border="0">
                                                </a>
                                            </td>';*/
                                             } 
                                        $arr_all_pay .='</tr>
                                    </table>
                                </td>
                                <td colspan="6" class="text_10b">'.$paymentClaims_pay.'';
                                
                                    if($deleteDate){
                                        $arr_all_pay .= '&nbsp;'.$deleteDate.$paidByCode;
                                    }else{
                                        if(($deduct_amount!=0) && ($deletePayment!=1)){ 
                                            //echo 'Date : '.$paidDate.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deduct Amount:'.number_format($deduct_amount, 2); 
                                            $arr_all_pay .=' : '.$paidByCode.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deduct Amount:'.$showCurrencySymbol.number_format($deduct_amount, 2); 
                                        }else if($modified_date){ 
                                            $arr_all_pay .= ' : '.$paidByCode. ' - '. $modified_date; 
                                        }else{ 
                                            //echo 'Date : '.$paidDate; 
                                            $arr_all_pay .= ' : '.$paidByCode; 
                                        } 
                                    }
                                    if(strstr($moaQualifier, 'MA18') || strstr($moaQualifier, 'MA07')){
										$show_moaQualifier="MA18";
										if(strstr($moaQualifier, 'MA07')){
											$show_moaQualifier="MA07";
										}												
                                        $arr_all_pay .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                        if($insCo1NameCode == $paidByCode){														
                                            if($deletePayment!=1){
                                                $arr_all_pay .= '('.$show_moaQualifier.' - Forwarded to '.$insCo2NameCode.')';
                                            }
                                        }
                                    }
                                $pay_sty="";
                                if($deletePayment==1){ $pay_sty="style=text-decoration:line-through;color:#FF0000;"; } 
                                $arr_all_pay .='</td>
                                <td align="left" id="paymentTD'.$seque.'" class="text_10"'.$pay_sty.' >'.$showCurrencySymbol.number_format($paidProc, 2).'</td>
                                <td align="left" colspan="3" class="text_10" '.$pay_sty.'>'.$show_cas_code.'</td>
                                <td align="left" nowrap class="text_10" '.$pay_sty.'>'.$paidOfDateNew.'</td>
                                <td align="left" class="text_10" '.$pay_sty.'>'.$paymentMethod.'</td>
                                <td align="left" class="text_10" '.$pay_sty.'>';
                                    
                                        if($paymentMethod!='Cash'){
                                            $arr_all_pay .= $cCChkNumber; 
                                        }else{
                                            $arr_all_pay .= "-";
                                        }
                                    
                                $arr_all_pay .='</td>
                                <td class="text_10" align="left"'.$pay_sty.'>';
                                if($expirationDate){
                                    $arr_all_pay .= $expirationDate;
                                 }else{ 
                                     $arr_all_pay .= '&nbsp;';
                                 }
                                 $arr_all_pay .='</td>
                                <td align="left" class="text_10"'.$pay_sty.'>&nbsp;</td>
                                <td align="left" class="text_10"'.$pay_sty.'>&nbsp;</td>
                                <td align="left" class="text_10"'.$pay_sty.'>'.$operatorName.'</td>
                            </tr>';
                        $arr_all[$paidOfDateNew1][]=$arr_all_pay;
                        $arr_date[]=$paidOfDateNew1;
                    }
                }
                
                //print_r($arr_all);
            $arr_all_crd="";
            $getCreditsStr = "Select * From manual_batch_creditapplied 
                                WHERE (
                                    charge_list_detail_id = '$charge_list_detail_id' 
                                        || 
                                    charge_list_detail_id_adjust = '$charge_list_detail_id'
                                    ) and  credit_applied='1'
                                    and post_status='0'";
            $getCreditsQry = imw_query($getCreditsStr);
            $getcountCrRows = imw_num_rows($getCreditsQry);
                if($getcountCrRows){
                    while($getCreditsRows = imw_fetch_array($getCreditsQry)){
                        $amountApplied = number_format($getCreditsRows['amountApplied'], 2);
                        $dateApplied = $getCreditsRows['dateApplied'];
                            list($crAppYear, $crAppMonth, $crAppDay) = explode("-", $dateApplied);
                            //$dateApplied = $crAppMonth."-".$crAppDay."-".$crAppYear;
                            $dateApplied = date(''.$global_date_format_small.'',mktime(0,0,0,$crAppMonth,$crAppDay,$crAppYear));
                            $dateApplied1 = date('m-d-Y',mktime(0,0,0,$crAppMonth,$crAppDay,$crAppYear));
                        $operatorApplied = $getCreditsRows['operatorApplied'];
                        $type_credit=$getCreditsRows['type'];
                        $ins_case=$getCreditsRows['ins_case'];
                        $payment_mode_credit=$getCreditsRows['payment_mode'];
                        if($payment_mode_credit=='Check' || $payment_mode_credit=='EFT' || $payment_mode_credit=='Money Order' || $payment_mode_credit=='VEEP'){
                            $checkCcNumber_credit=$getCreditsRows['checkCcNumber'];
                        }
                        if($payment_mode_credit=='Credit Card'){
                            $cc_type=strtoupper(substr($getCreditsRows['creditCardCo'], 0, 2));
                            $checkCcNumber_credit=$cc_type.'-'.$getCreditsRows['creditCardNo'];
                            $expirationDateCc=$getCreditsRows['expirationDateCc'];
                        }
                        $crAppId=$getCreditsRows['crAppId'];
                        $credit_note=$getCreditsRows['credit_note'];
                        $delete_credit=$getCreditsRows['delete_credit'];
                        $crAppliedToEncId=$getCreditsRows['crAppliedToEncId'];
                        $delete_credit=$getCreditsRows['delete_credit'];
                        $modify=$getCreditsRows['modify'];
                        $charge_list_detail_id_adjust=$getCreditsRows['charge_list_detail_id_adjust'];
                        $crAppliedTo=$getCreditsRows['crAppliedTo'];
                        $charge_list_detail_id_chk=$getCreditsRows['charge_list_detail_id'];
                        $patient_id_adjust=$getCreditsRows['patient_id_adjust'];
                        $patient_id_chk=$getCreditsRows['patient_id'];
                        $crAppliedToEncId_adjust=$getCreditsRows['crAppliedToEncId_adjust'];
                        $b_id=$getCreditsRows['batch_id'];
                        if($type_credit=='Insurance'){
                            //$credit_by = getData('name', 'insurance_companies', 'id', $ins_case);
                            $getInsCoStr = "SELECT * FROM insurance_companies WHERE id = '$ins_case'";
                            $getInsCoQry = imw_query($getInsCoStr);
                            $getInsCoRow = imw_fetch_array($getInsCoQry);
                            $insCoCode = $getInsCoRow['in_house_code'];
                            if($insCoCode<>""){
                                $credit_by=$insCoCode;
                            }else{
                                $credit_by = $getInsCoRow['name'];
                            }
                        }else{
                            $credit_by=$type_credit;
                        }
                        if($credit_by==""){
                            $credit_by="Patient";
                        }
						$crOperName = $usr_alias_name[$operatorApplied];
                        $credit_note1=htmlentities($credit_note);
                        if($crAppliedToEncId_adjust>0){
                        
                            $getpat_to_qry=imw_query("select * from patient_data where pid in($patient_id_adjust)");
                            $getpat_to=imw_fetch_object($getpat_to_qry);
                            $fname_to = $getpat_to->fname;
                            $lname_to = $getpat_to->lname;
                            $mname_to = $getpat_to->mname;
                            $patientName_to = ucwords(trim($lname_to.", ".$fname_to));
                            
                            $getpat_frm_qry=imw_query("select * from patient_data where pid in($patient_id_chk)");
                            $getpat_frm=imw_fetch_object($getpat_frm_qry);
                            $fname_frm = $getpat_frm->fname;
                            $lname_frm = $getpat_frm->lname;
                            $mname_frm = $getpat_frm->mname;
                            $patientName_frm = ucwords(trim($lname_frm.", ".$fname_frm));
                            //echo $patient_id_adjust.'=='.$patient_id_chk;
                            if($patient_id_adjust==$patient_id_chk){
                                if($credit_note1<>""){
                                    $credit_note1=$credit_note1;
                                }
                                $note="Adjustment Credit : $credit_by $credit_note1";
                                $note_debit="Adjustment Debit : $credit_by $credit_note1";
                            }else{
                                $note="Adjustment Credit : $credit_by  from  $patientName_frm - $patient_id_chk  $credit_note1";
                                $note_debit="Adjustment Debit : $credit_by  to $patientName_to – $patient_id_adjust  $credit_note1";
                            }
                        }else{
                            $note="Adjustment Credit : $credit_by $credit_note1";
                            $note_debit="Adjustment Debit : $credit_by $credit_note1";
                        }
                        $deleteRows = '';
                        $show_del_style= '';
                        if($delete_credit==1){
                            $show_del_style='class="hide deleted"';
                            $deleteRows = 'id="deleted_rows_id[]"';
                        }else{
                            $show_del_style="";
                        }
                        if($charge_list_detail_id_chk==$charge_list_detail_id  && $crAppliedTo=='payment'){
                        
                            $arr_all_crd .='
                            <tr '.$deleteRows.'   style="'.$show_del_style.'">
                              <td colspan="2" align="left" class="text_10b_purpule">';
                                    
                                        if($delete_credit!=1){
                                            $arr_all_crd .='<a class="text_10b_purpule" href="javascript:editTrans_crd(\''.$b_id.'\',\''.$crAppId.'\');" >&nbsp;<img src="../../library/images/edit.png" alt="Edit" border="0"></a>
                                        
                                                <a class="text_10b_purpule" href="javascript:delTransId_crd(\''.$b_id.'\',\''.$send_enc.'\',\''.$crAppId.'\');"><img src="../../library/images/del.png" alt="Del" border="0"></a>';
                                            
                                            $arr_all_crd .='&nbsp;';
                                        }else{
                                            $arr_all_crd .='&nbsp;';
                                        }
                                 $crd_sty="";		
                                 if($delete_credit==1){ $crd_sty="style=text-decoration:line-through;color:#FF0000"; }
                                $arr_all_crd .='</td>
                                <td colspan="5" class="text_10b" align="left" width="325">
                                    Refund : '.$credit_by.'&nbsp;'.htmlentities($credit_note).'
                                </td>
                                <td  class="text_10" align="left" '.$crd_sty.'>'.$showCurrencySymbol.$amountApplied.'</td>
                                <td colspan="4">&nbsp;</td>
                                <td class="text_10" align="left" '.$crd_sty.'>'.$dateApplied.'</td>
                                <td class="text_10" align="left" '.$crd_sty.'>'.$payment_mode_credit.'</td>
                                <td class="text_10" align="left" '.$crd_sty.'>'.$checkCcNumber_credit.'</td>
                                <td class="text_10" align="left" '.$crd_sty.'>'.$expirationDateCc.'</td>
                                <td >&nbsp;</td>
                                <td >&nbsp;</td>
                                <td class="text_10" align="left" '.$crd_sty.'>'.$crOperName.'</td>
                            </tr>';
                            
                        }else if($charge_list_detail_id_adjust==$charge_list_detail_id && $crAppliedTo=='adjustment'){
                        
                            $arr_all_crd .='<tr '.$deleteRows.'   '.$show_del_style.'>
                              <td colspan="2" align="left" class="text_10b_purpule">';
                                    $send_enc=0;
                                    if($crAppliedToEncId_adjust>0){
                                        $send_enc=$crAppliedToEncId_adjust;
                                    }else{
                                        $send_enc=$crAppliedToEncId;
                                    }
                                    if($delete_credit!=1){
                                        
                                        $arr_all_crd .='<a class="text_10b_purpule" href="javascript:editTrans_crd(\''.$b_id.'\',\''.$crAppId.'\');" >&nbsp;<img src="../../library/images/edit.png" alt="Edit" border="0"></a>
                                        
                                        <a class="text_10b_purpule" href="javascript:delTransId_crd(\''.$b_id.'\',\''.$send_enc.'\',\''.$crAppId.'\');"><img src="../../library/images/del.png" alt="Del" border="0"></a>';
                                        
                                        $arr_all_crd .= '&nbsp;';
                                    }else{
                                        $arr_all_crd .= '&nbsp;';
                                    }
                                $crd_sty="";
                                 if($delete_credit==1){ $crd_sty="style=text-decoration:line-through;color:#FF0000";}
                                $arr_all_crd .='</td>
                                <td colspan="5" class="text_10b" align="left"  width="325">
                                    '.$note.'
                                </td>
                                <td  class="text_10" align="left"'.$crd_sty.'>'.$showCurrencySymbol.$amountApplied.'</td>
                                <td colspan="4">&nbsp;</td>
                                <td class="text_10" align="left" '.$crd_sty.'>'.$dateApplied.'</td>
                                <td class="text_10" align="left" '.$crd_sty.'>&nbsp;';
                                if($checkCcNumber_credit<>""){
                                    $arr_all_crd .=$payment_mode_credit;
                                }
                                $arr_all_crd .='</td>
                                <td class="text_10" align="left"'.$crd_sty.'>&nbsp;'.$checkCcNumber_credit.'</td>
                                <td class="text_10" align="left" '.$crd_sty.'>'.$expirationDateCc.'</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td class="text_10" align="left"'.$crd_sty.'>'.$crOperName.'</td>
                            </tr>';
                        
                        }
                        
                        if($crAppliedTo=='adjustment' && $charge_list_detail_id_adjust<>$charge_list_detail_id && $charge_list_detail_id_chk==$charge_list_detail_id){
                         $crd_sty1="";
                         if($delete_credit==1){ $crd_sty1="style=text-decoration:line-through;color:#FF0000";}
                        $arr_all_crd .='
                        <tr '.$deleteRows.'   '.$show_del_style.'>
                                <td colspan="2" align="left" class="text_10b">&nbsp;</td>
                                <td colspan="5" class="text_10b" align="left"  width="325">
                                    '.$note_debit.'
                                </td>
                                <td  class="text_10" align="left"'.$crd_sty1.'>'.$showCurrencySymbol.$amountApplied.'</td>
                                <td colspan="4">&nbsp;</td>
                                <td class="text_10" align="left"'.$crd_sty1.'>'.$dateApplied.'</td>
                                <td class="text_10" align="left"'.$crd_sty1.'>'.$payment_mode_credit.'</td>
                                <td class="text_10" align="left"'.$crd_sty1.'>'.$checkCcNumber_credit.'</td>
                                <td class="text_10" align="left" '.$crd_sty1.'>'.$expirationDateCc.'</td>
                                <td >&nbsp;</td>
                                <td>&nbsp;</td>
                                <td class="text_10" align="left"'.$crd_sty1.'>'.$crOperName.'</td>

                            </tr>';
                        
                        }
                        $arr_all[$dateApplied1][]=$arr_all_crd;
                        $arr_date[]=$dateApplied1;
                        $arr_all_crd="";
                        $dateApplied1="";
                    }
                }
                //batch file payment transaction
                $deletePayment="";
                $getPayment_batch = "SELECT * FROM
                                        manual_batch_transactions
                                        WHERE batch_id = '$b_id'
                                        and charge_list_detaill_id='$charge_list_detail_id'
                                        and payment_claims !='Allowed'
                                        and batch_id='$b_id'
                                        and post_status!=1
                                        and del_status=0";
                $getPayment_batchQry = imw_query($getPayment_batch);
                $getRows_batch_Count = imw_num_rows($getPayment_batchQry);
                if($getRows_batch_Count>0){
                    while($getPaymentBatchRows = imw_fetch_array($getPayment_batchQry)){
                        $arr_all_pay_tran="";
                        $modified_date="";
                        $trans_id = $getPaymentBatchRows['trans_id'];
                        $trans_amt = $getPaymentBatchRows['trans_amt'];
                        $trans_payment_claims = $getPaymentBatchRows['payment_claims'];
                        $trans_payment_mode = $getPaymentBatchRows['payment_mode'];
                        $trans_date = $getPaymentBatchRows['trans_date'];
                        $trans_operator_id = $getPaymentBatchRows['operator_id'];
                        $trans_insurance_id = $getPaymentBatchRows['insurance_id'];
                        $trans_by = $getPaymentBatchRows['trans_by'];
                        $creditCardType= $getPaymentBatchRows['credit_card_type'];
                        $write_off_code_id= $getPaymentBatchRows['write_off_code_id'];
                        $enc_id= $getPaymentBatchRows['encounter_id'];
                        $cas_type= $getPaymentBatchRows['cas_type'];
                        $cas_code= $getPaymentBatchRows['cas_code'];
                        
                        
                        $write_off_code_trans="";
                        $w_code_qry_tran = imw_query("SELECT w_code FROM write_off_code 
                                            WHERE w_id = '$write_off_code_id'");							
                        $w_code_row_tran = imw_fetch_array($w_code_qry_tran);
                        $write_off_code_trans=$w_code_row_tran['w_code'];
                        
                        
                        $show_cas_code=show_cas_code_fun($cas_type,$cas_code);
                        
                        list($yy, $mm, $dd) = explode("-", $trans_date);
                            
                        //$paidOfDateNew = $mm.'-'.$dd.'-'.$yy;
                            
                         $paidOfDateNew1 = date('m-d-Y',mktime(0,0,0,$mm,$dd,$yy));
                         
                        if($trans_payment_claims=="Deposit"){
                            $trans_payment_claims="Deposit";
                        }else if($trans_payment_claims=="Interest Payment"){
                            $trans_payment_claims="Interest Payment";
                        }else if($trans_payment_claims=="Paid"){
                            $trans_payment_claims="Payment";
                        }else if($trans_payment_claims=="Write Off" || $trans_payment_claims=="Discount" || $trans_payment_claims=="Denied" || $trans_payment_claims=="Deductible"){
                            $trans_payment_mode="";
                        }
                        
                        //-------------------- GETTING INS. CO. NAME --------------------//
                        if(($trans_insurance_id!=0) && ($trans_insurance_id!="")){
                            $getInsCoStr = "SELECT in_house_code,name FROM insurance_companies WHERE id = '$trans_insurance_id'";
                            $getInsCoQry = imw_query($getInsCoStr);
                            $getInsCoRow = imw_fetch_array($getInsCoQry);
                            $insCoCode = $getInsCoRow['in_house_code'];
                            $insCoName = $getInsCoRow['name'];
                        }
                        if((!$insCoCode) || ($insCoCode=='')){
                            $insCoCode = $insCoName;
                        }
                        //-------------------- GETTING INS. CO. NAME --------------------//
                        
                        if($trans_by == 'Insurance'){
                            $paidByCode = $insCoCode;
                            if(strlen($paidByCode)>8){
                                $paidByCode = $paidByCode;
                            }
                        }
                        
                        if($trans_by == 'Patient'){
                            $paidByCode = 'Patient';
                        }
                        
                        if($trans_by == 'Res. Party'){
                            $paidByCode = 'Res. Party';
                        }
						$operatorName_tran = $usr_alias_name[$trans_operator_id];
                        list($tranYear, $tranMon, $tranDay) = explode("-", $trans_date);
                        $tranDate = date(''.$global_date_format_small .'',mktime(0,0,0,$tranMon,$tranDay,$tranYear));
                        
                        if($trans_payment_mode=='Check' || $trans_payment_mode=='EFT' || $trans_payment_mode=='Money Order' || $trans_payment_mode=='VEEP'){
                            $chk_number_trans = $getPaymentBatchRows['check_no'];
                        }
                        
                        $creditCardNoTrans = strtoupper(substr($getPaymentBatchRows['credit_card_no'], 0, 2));
                        if($trans_payment_mode=='Credit Card'){
                            $trans_payment_mode = 'CC';
                            $creditCardNoTrans = $getPaymentBatchRows['credit_card_no'];
                            $expDate_cc_tran = $getPaymentBatchRows['credit_card_exp'];
                            $ccNoLength_tran = strlen($creditCardNoTrans);
                            if($ccNoLength_tran>6){
                                $creditCardNoTrans = strrev($creditCardNoTrans);
                                $creditCardNoTrans = substr($creditCardNoTrans, 0, 4);
                                $creditCardNoTrans = "xx".strrev($creditCardNoTrans);
                                $chk_number_trans = $creditCardType.' - '.$creditCardNoTrans;
                            }
                        }else{
                            $expDate_cc_tran = '';
                        }
                                                            
                        ++$seque;
                            $deleteRows_tran = '';
                            $show_del_style= '';
                            $deleteDate="";
                            if($deleteRows_tran==1){
                                $show_del_style='class="hide deleted"';
                                $deleteRows = 'id="deleted_rows_id[]"';
                            }else{
                                $show_del_style="";
                            }
                            $arr_all_pay_tran .='
                            <tr '.$deleteRows_tran.'   '.$show_del_style.'>
                                <td colspan="2" align="left" class="text_10b">
                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tr>';
                                            if($deletePayment!=1){ 
                                            $arr_all_pay_tran .='
                                            <td class="text_10b_purpule" nowrap>
                                                <a href="javascript:editTrans(\''.$b_id.'\',\''.$trans_id.'\');" class="text_10b_purpule">
                                                    <img src="../../library/images/edit.png" alt="Edit" border="0">
                                                </a>
                                                <a href="javascript:delTransId(\''.$b_id.'\',\''.$enc_id.'\',\''.$trans_id.'\');" class="text_10b_purpule">
                                                    <img src="../../library/images/del.png" alt="Del" border="0">
                                                </a>
                                            </td>';
                                             } 
                                        $arr_all_pay_tran .='</tr>
                                    </table>
                                </td>
                                <td colspan="6" class="text_10b">'.$trans_payment_claims.'';
                                
                                    if($deleteDate){
                                        $arr_all_pay_tran .= '&nbsp;'.$deleteDate.$paidByCode;
                                    }else{
                                        /*if(($deduct_amount!=0) && ($deletePayment!=1)){ 
                                            //echo 'Date : '.$paidDate.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deduct Amount:'.number_format($deduct_amount, 2); 
                                            $arr_all_pay_tran .=' : '.$paidByCode.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deduct Amount:'."$".number_format($deduct_amount, 2); 
                                        }else */
                                        if($modified_date){ 
                                            $arr_all_pay_tran .= ' : '.$paidByCode. ' - '. $modified_date; 
                                        }else{ 
                                            //echo 'Date : '.$paidDate; 
                                            $arr_all_pay_tran .= ' : '.$paidByCode; 
                                        } 
                                    }
                                $pay_sty="";
                                //if($deletePayment==1){ $pay_sty="style=text-decoration:line-through;color:#FF0000;"; } 
                                $arr_all_pay_tran .='</td>
                                <td align="left" id="paymentTD'.$seque.'" class="text_10"'.$pay_sty.' >'.$showCurrencySymbol.number_format($trans_amt, 2).'</td>
                                <td align="left" colspan="3" class="text_10" '.$pay_sty.'>'.$show_cas_code.'</td>
                                <td align="left" nowrap class="text_10" '.$pay_sty.'>'.$tranDate.'</td>
                                <td align="left" class="text_10" '.$pay_sty.'>'.$trans_payment_mode.'</td>
                                <td align="left" class="text_10" '.$pay_sty.'>';
                                        
                                if($trans_payment_mode!='Cash'){
                                    $arr_all_pay_tran .= $chk_number_trans; 
                                }else{
                                    $arr_all_pay_tran .= "-";
                                }
                                    
                                $arr_all_pay_tran .='</td>
                                <td class="text_10" align="left"'.$pay_sty.'>';
                                if($expDate_cc_tran){
                                    $arr_all_pay_tran .= $expDate_cc_tran;
                                 }else{ 
                                     $arr_all_pay_tran .= '&nbsp;';
                                 }
                                 $arr_all_pay_tran .='</td>
                                <td align="left" class="text_10"'.$pay_sty.'>&nbsp;</td>
                                <td align="left" class="text_10"'.$pay_sty.'>'.$write_off_code_trans.'</td>
                                <td align="left" class="text_10"'.$pay_sty.'>'.$operatorName_tran.'</td>
                            </tr>';
                        $arr_all[$paidOfDateNew1][]=$arr_all_pay_tran;
                        $arr_date[]=$paidOfDateNew1;
                    }
                }
                
                //print_r($arr_date);
                $arr_date1 = array_values(array_unique($arr_date));
                //print_r($arr_date1);
                array_walk($arr_date1,"ymd2ts");
                //print_r($arr_date1);
                sort($arr_date1);
                array_walk($arr_date1,"ts2ymd");
                //print_r($arr_date1);
                //$arr_date1=array_reverse($arr_date1);
                //print '<pre>';
                //print_r($arr_all);
                for($k=0;$k<count($arr_date1);$k++){
                    $date = $arr_date1[$k];
                    for($p=0;$p<count($arr_all[$date]);$p++){
                        if($arr_all[$date][$p]){
                            print $arr_all[$date][$p];
                        }
                    }
                    //print $arr_all[$date];
                }
            }
        }
        
        if($copay>0){
            $getCoPayWriteOffIDStr = "SELECT * FROM paymentswriteoff
                                        WHERE patient_id = '$patient_id'
                                        AND encounter_id = '$encounter_id'
                                        AND charge_list_detail_id = 0
                                        AND delStatus = 0";
            $getCoPayWriteOffIDQry = imw_query($getCoPayWriteOffIDStr);
            $coPayWriteOffCount = imw_num_rows($getCoPayWriteOffIDQry);
            if($coPayWriteOffCount>0){
                $getCoPayWriteOffIDRow = imw_fetch_array($getCoPayWriteOffIDQry);
                    $coPay_write_off_id = $getCoPayWriteOffIDRow['write_off_id'];
            }
            $paymentStatusType = $getCoPayWriteOffIDRow['paymentStatus'];
            $write_off_code_copay = $getCoPayWriteOffIDRow['write_off_code_id'];
            $w_code_qry2 = imw_query("SELECT w_code FROM write_off_code 
                                            WHERE w_id = '$write_off_code_copay'");							
            $w_code_row2 = imw_fetch_array($w_code_qry2);
            $write_off_code_id_copay=$w_code_row2['w_code'];
            if(($coPayNotRequired == 0) && ($coPayWriteOff!='1')){
                if($copayPaid<=0){
                    ?>
                    <tr >
                        <td align="left" class="text_10b">
                            <input disabled type="checkbox" name="coPayChk" id="coPayChk" value="true" class="text_10" onClick="return changeAmt(<?php echo $copay; ?>,<?php echo $getProcCountRows; ?>);" /> 
                        </td>
                        <td align="left" class="text_10b">CoPay</td>
                    </tr>
                    <?php
                }else{
                    ?>
                    <tr >									
                        <td colspan="2" align="left" class="text_10b">&nbsp;&nbsp;<img src="../../library/images/confirm.gif" width="16px" class="text_10b" /></td>
                        <td colspan="20" align="left" class="text_10b">CoPay</td>
                    </tr>
                    <?php
                }
            }else{
                ?>
                <tr >
                    <td colspan="18" align="left" class="text_10b">
                    <?php
                    if($coPayWriteOff!=0){
                        /*?>
                        <span style="padding-left:30px;width:105px;" class="text_10b">
                            <a href="javascript:delCoPayWOFn('<?php echo $encounter_id; ?>', '<?php echo $coPay_write_off_id; ?>');" class="text_10b_purpule">
                                <img src="../../library/images/del.png" alt="Del" border="0">
                            </a>
                        </span>
                        <?php*/
                    }
                    ?>
                    CoPay:
                    <span class="text_10b" style="color:#FF0000;"><?php echo $showCurrencySymbol.number_format($copay, 2); ?></span>
                    <?php 
                    if($coPayWriteOff!='1'){ 
                        ?>
                        <span style="font-family:Arial; font-size:12px; font-weight:bold;color:#0000FF;">NR</span>
                        <?php 
                    }else{ 
                        ?>
                        <span style="font-family:Arial; font-size:12px; font-weight:bold;color:#0000FF;"><?php if($paymentStatusType) echo $paymentStatusType; else echo 'Write Off'; ?></span>
                        <?php
                    } 
                    ?>
                    </td>
                    <td align="left" class="text_10">
                        <?php echo $write_off_code_id_copay; ?>
                    </td>
                </tr>
                <?php
            }	
        }
        $getCoPayPaidInfoStr = "SELECT * FROM 
                                patient_chargesheet_payment_info a,
                                patient_charges_detail_payment_info b
                                WHERE a.encounter_id = '$encounter_id'
                                AND a.payment_id = b.payment_id
                                AND b.charge_list_detail_id = 0
                                ORDER BY a.payment_id DESC";
        $getCoPayPaidInfoQry = imw_query($getCoPayPaidInfoStr);
        if(imw_num_rows($getCoPayPaidInfoQry)>0){
            while($getCoPayPaidInfoRows = imw_fetch_array($getCoPayPaidInfoQry)){
                $payment_details_id_coPay = $getCoPayPaidInfoRows['payment_details_id'];
                $payment_id_coPay = $getCoPayPaidInfoRows['payment_id'];
                $operatorId_coPay = $getCoPayPaidInfoRows['operatorId'];
                $coPayPaymentMode = $getCoPayPaidInfoRows['payment_mode'];
                $coPaycheckNo = $getCoPayPaidInfoRows['checkNo'];
                $coPaycreditCardCo = $getCoPayPaidInfoRows['creditCardCo'];
                $coPaycreditCardNo = $getCoPayPaidInfoRows['creditCardNo'];
                $paymentClaims = $getCoPayPaidInfoRows['paymentClaims'];
				$operatorCoPay = $usr_alias_name[$operatorId_coPay];	
                $paidBy_coPay = $getCoPayPaidInfoRows['paidBy'];
                $paidDate_coPay = $getCoPayPaidInfoRows['paidDate'];
                    list($coPayYear, $coPayMon, $coPayDay) = explode("-", $paidDate_coPay);
                    //$paidDate_coPay = $coPayMon.'-'.$coPayDay.'-'.$coPayYear;
                    $paidDate_coPay = date(''.$global_date_format_small.'',mktime(0,0,0,$coPayMon,$coPayDay,$coPayYear));

                $paidForProc_coPay = $getCoPayPaidInfoRows['paidForProc'];
                $deleteDate_coPay = $getCoPayPaidInfoRows['deleteDate'];
                    list($deleteDate_coPayYear, $deleteDate_coPayMon, $deleteDate_coPayDay) = explode("-", $deleteDate_coPay);
                    //$deleteDate_coPay = $deleteDate_coPayMon.'-'.$deleteDate_coPayDay.'-'.$deleteDate_coPayYear;
                    $deleteDate_coPay = date('m-d-y',mktime(0,0,0,$deleteDate_coPayMon,$deleteDate_coPayDay,$deleteDate_coPayYear));
                $deletePayment = $getCoPayPaidInfoRows['deletePayment'];
                
                        $insProviderId = $getCoPayPaidInfoRows['insProviderId'];
                            //-------------------- GETTING INS. CO. NAME --------------------//
                            if(($insProviderId!=0) && ($insProviderId!="")){
                                $getInsCoStr = "SELECT * FROM insurance_companies WHERE id = '$insProviderId'";
                                $getInsCoQry = imw_query($getInsCoStr);
                                $getInsCoRow = imw_fetch_array($getInsCoQry);
                                $insCoCode = $getInsCoRow['in_house_code'];
                                $insCoName = $getInsCoRow['name'];
                            }
                            if((!$insCoCode) || ($insCoCode=='')){
                                $insCoCode = $insCoName;
                            }
                            //-------------------- GETTING INS. CO. NAME --------------------//
                        if($paidBy_coPay == 'Insurance'){
                            $paidBy_coPay = $insCoCode;
                            if(strlen($paidBy_coPay)>8){
                                //$paidBy_coPay = substr($paidBy_coPay, 0, 8)."..";
                                $paidBy_coPay = $paidBy_coPay;
                            }
                        }else{
                            $paidBy_coPay=$paidBy_coPay;
                        }
                        
                    $deleteRows = '';
                    $show_del_style= '';
                    if($deletePayment==1){
                        $show_del_style='class="hide deleted"';
                        $deleteRows = 'id="deleted_rows_id[]"';
                    }else{
                        $show_del_style="";
                    }
                ?>
                <tr <?php echo $deleteRows ; ?> <?php echo $show_del_style; ?> bgcolor="#EAF0F7" >
                    <td colspan="2" align="left">&nbsp;
                    <?php
                    if($deletePayment==0){
                        /*?>
                        <a href="javascript:delCoPayPaymentFn('<?php echo $encounter_id; ?>', '<?php echo $payment_details_id_coPay; ?>', '<?php echo $payment_id_coPay; ?>', '<?php echo $paidForProc_coPay; ?>');" class="text_10b_purpule">
                            <img src="../../library/images/del.png" alt="Del" border="0">
                        </a>
                        <?php*/
                    }
                    ?>
                    </td>
                    
                    <td align="left" colspan="6" class="text_10b">
                        <?php
                        if($deletePayment=='1'){
                            //echo "CoPay Payment Deleted Date : ".$deleteDate_coPay;
                            echo "CoPay Payment Deleted : ".$paidBy_coPay;
                        }else{
                            echo "CoPay ".$paymentClaims." : ".$paidBy_coPay;
                        }
                        ?>									
                    </td>								
                    <td align="left" colspan="4" class="text_10" <?php if($deletePayment=='1') echo 'style=text-decoration:line-through;color:#FF0000;'; ?>><?php echo $showCurrencySymbol.number_format($paidForProc_coPay, 2); ?></td>
                    <!-- <td align="left" class="text_10" <?php //if($deletePayment=='1') echo 'style=text-decoration:line-through;color:#FF0000;'; ?>><?php //echo $paidBy_coPay; ?></td> -->
                    <td align="left" nowrap class="text_10" <?php if($deletePayment=='1') echo 'style=text-decoration:line-through;color:#FF0000;'; ?>><?php echo $paidDate_coPay; ?></td>
                    <td class="text_10" align="left" <?php if($deletePayment=='1') echo 'style=text-decoration:line-through;color:#FF0000;'; ?>><?php echo $coPayPaymentMode; ?></td>
                    <?php if($coPayPaymentMode<>'Cash'){?>
                    <td class="text_10" align="left" <?php if($deletePayment=='1') echo 'style=text-decoration:line-through;color:#FF0000;'; ?>><?php if($coPayPaymentMode=='Check' || $coPayPaymentMode=='EFT' || $coPayPaymentMode=='Money Order' || $coPayPaymentMode=='VEEP'){echo $coPaycheckNo;}else{ echo $coPaycreditCardNo;} ?></td>
                    <td colspan="3" class="text_10" align="left">&nbsp;</td>
                    <?php } else{?>
                    <td colspan="4" class="text_10" align="left">&nbsp;</td>
                    <?php } ?>
                    <td  nowrap align="left" n class="text_10" <?php if($deletePayment=='1') echo 'style=text-decoration:line-through;color:#FF0000;'; ?>><?php echo $operatorCoPay; ?></td>
                </tr>
                <?php
            }
        }
        //--------------------- COPAY WROTEOFF LIST ---------------------
        $getCoPayWriteOffListStr = "SELECT * FROM paymentswriteoff
                                    WHERE patient_id = '$patient_id'
                                    AND encounter_id = '$encounter_id'
                                    AND charge_list_detail_id = 0
                                    AND delStatus = '1'";
        $getCoPayWriteOffListQry = imw_query($getCoPayWriteOffListStr);
        while($getCoPayWriteOffListRows = imw_fetch_array($getCoPayWriteOffListQry)){
            $coPay_write_off_by_id = $getCoPayWriteOffListRows['write_off_by_id'];
                $coPay_write_off_by = getData('name', 'insurance_companies', 'id', $coPay_write_off_by_id);
            $coPay_write_off_amount = number_format($getCoPayWriteOffListRows['write_off_amount'], 2);
            $coPay_write_off_operator_id = $getCoPayWriteOffListRows['write_off_operator_id'];
            $coPay_write_off_operatorName = $usr_alias_name[$coPay_write_off_operator_id];	    
            $coPay_write_off_date = $getCoPayWriteOffListRows['write_off_date'];
            $coPay_delStatus = $getCoPayWriteOffListRows['delStatus'];
            $coPay_write_off_del_date = $getCoPayWriteOffListRows['write_off_del_date'];
            $coPay_write_off_code = $getCoPayWriteOffListRows['write_off_code_id'];
            $w_code_qry3 = imw_query("SELECT w_code FROM write_off_code 
                                            WHERE w_id = '$coPay_write_off_code'");							
            $w_code_row3 = imw_fetch_array($w_code_qry3);
            $coPay_write_off_code_id=$w_code_row3['w_code'];
                list($coPayWOYear, $coPayWOMonth, $coPayWODay) = explode("-", $coPay_write_off_del_date);
                //$coPay_write_off_del_date = $coPayWOMonth."-".$coPayWODay."-".$coPayWOYear;		
                $coPay_write_off_del_date = date(''.$global_date_format_small.'',mktime(0,0,0,$coPayWOMonth,$coPayWODay,$coPayWOYear));						
            $deleteRows = '';
            $show_del_style= '';
            if($coPay_delStatus==1){
                $show_del_style='class="hide deleted"';
                $deleteRows = 'id="deleted_rows_id[]"';
            }else{
                $show_del_style="";
            }
            ?>
            <tr <?php echo $deleteRows ; ?> <?php echo $show_del_style; ?> bgcolor="#EAF0F7" >
                <td colspan="2" align="left" class="text_10">&nbsp;</td>
                <td colspan="6" class="text_10b">Write Off CoPay Deleted Date : <?php echo $coPay_write_off_del_date; ?></td>
                <td colspan="4" align="left" class="text_10" style="text-decoration:line-through;color:#FF0000;"><?php echo $showCurrencySymbol.$coPay_write_off_amount; ?></td>
                <td colspan="5" align="left" class="text_10" style="text-decoration:line-through;color:#FF0000;"><?php if($coPay_write_off_by) echo $coPay_write_off_by; else echo '&nbsp;'; ?></td>
                <td style="text-decoration:line-through;color:#FF0000;"><?php echo $coPay_write_off_code_id; ?></td>
                <td style="text-decoration:line-through;color:#FF0000;"><?php echo $coPay_write_off_operatorName; ?></td>
            </tr>
            <?php							
        }
        //--------------------- COPAY WROTEOFF LIST ---------------------					
        ?>
        <input type="hidden" name="amountDue" id="amountDue" value="<?php echo $amountDue; ?>" />
        <input type="hidden" name="copaySt" id="copaySt" value="<?php echo $getRowValidation; ?>" />
        <input type="hidden" name="totalBalance" id="totalBalance" value="<?php echo $totalBalanceNewDue; ?>">
        <input type="hidden" name="charge_list_detail_id" id="charge_list_detail_id" value="<?php echo $charge_list_detail_id; ?>">
        <input type="hidden" value="<?php echo $totalRefractionAmountFor; ?>" name="totalRefractionAmountFor" id="totalRefractionAmountFor">
        <input type="hidden" value="<?php echo $seq; ?>" name="sequence" id="sequence">
  </table>
<table class="table table-bordered">
    <tr class="grythead">
        <th>Int. / Ext.</th>
        <th>Comment Date</th>
        <th>Comments</th>
        <th>Operator</th>
        <th>Function</th>
    </tr>
    <?php
        $getCommentsStr = "SELECT * FROM paymentscomment WHERE patient_id = '$patient_id' AND encounter_id = '$encounter_id'";
        $getCommentsQry = imw_query($getCommentsStr);
        while($getCommentsRows = imw_fetch_array($getCommentsQry)){
			$commentId = $getCommentsRows['commentId'];
			$commentsType = $getCommentsRows['commentsType'];
			$encCommentsDate = $getCommentsRows['encCommentsDate'];
			$c_type = $getCommentsRows['c_type'];
			
			list($commentsYear, $commentsMonth, $commentsDay) = explode("-", $encCommentsDate);
			$encCommentsDate = date(''.$global_date_format_small.'',mktime(0,0,0,$commentsMonth,$commentsDay,$commentsYear));
			
			$encComments = $getCommentsRows['encComments'];
			$encCommentsOperatorId = $getCommentsRows['encCommentsOperatorId'];
			$operatorName = $usr_alias_name[$encCommentsOperatorId];	
    ?>
    <tr>
        <td id="editType<?php echo $commentId; ?>"><?php echo $commentsType; ?></td>
        <td id="commentDateTd<?php echo $commentId; ?>"><?php echo $encCommentsDate; ?></td>
        <td id="commentTd<?php echo $commentId; ?>"><?php echo $encComments; ?></td>
        <td id="operName<?php echo $commentId; ?>"><?php echo $operatorName; ?></td>
       <?php if($c_type=='batch'){?>
        <td>
        	<table>
                <tr>
                    <td id="editTd<?php echo $commentId; ?>"><a href="javascript:void(0);" onClick="editComment('<?php echo $commentId; ?>', '<?php echo $commentsType; ?>', '<?php echo $b_id; ?>');"><img src="../../library/images/edit.png" alt="Edit" style="border:none;"></a></td>
                    <td><a href="javascript:void(0);" onClick="javascript:delComment_batch('<?php echo $commentId; ?>', '<?php echo $encounter_id; ?>',  '<?php echo $b_id; ?>');"><img src="../../library/images/del.png" alt="Del" style="border:none; padding-left:10px;"></a></td>
                </tr>
            </table>
        </td>
        <?php }else{echo "<td>&nbsp;</td>";} ?>
    </tr>
    <?php
    }
    ?>
</table>
 
<?php
}
?>
<script type="text/javascript">
	var display_fee_row = '<?php print $display_fee_row; ?>';
	var dis = 'none';
	if(display_fee_row == true){
		dis = 'table-cell';
	}
	document.getElementById('contract_fee').style.display=dis;
</script>