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
$without_pat="yes";  
$title = "Review Payments";
require_once("../accounting/acc_header.php");
require_once("batch_session.php");
$global_date_format = phpDateFormat();
$showCurrencySymbol = show_currency();
$pat_qry=imw_query("select fname,lname from patient_data where id ='$review_patient_id'");
$pat_details=imw_fetch_array($pat_qry);
?>
<div class='purple_bar text-center'>
	<span class="pull-left"><b>Patient Payment Summary</b></span>
    <span><b><?php echo $pat_details['lname'].', '.$pat_details['fname'].' - '.$review_patient_id; ?></b></span>
</div>
<div style="height:<?php echo $_SESSION['wn_height']-360; ?>px; overflow-y:auto;">
    <table class="table table-bordered">
        <tr class="grythead">
            <th rowspan="2">S.No.</th>
            <th rowspan="2">DOS</th>
            <th rowspan="2">EId</th>
            <th rowspan="2">Ins. Case</th>
            <th rowspan="2">C.P.T.</th>
            <th rowspan="2">CoPay</th>
            <th rowspan="2">T.Charges</th>
            <th colspan="4">Payment Details</th>
            <th rowspan="2">Paid By</th>
            <th rowspan="2">New Balance</th>
            <th colspan="2">Date</th>
        </tr>
        <tr class="grythead">
            <th>Allowed</th>
            <th>Deductible </th>
            <th>Deposit </th>
            <th>Paid</th>
            <th>Posted</th>
            <th>Submitted</th>
        </tr>
        <?php
        $getChargeDetailsStr = "SELECT * FROM patient_charge_list
                                WHERE del_status='0' and patient_id='$review_patient_id'
                                ORDER BY date_of_service DESC";
        $getChargeDetailsQry = imw_query($getChargeDetailsStr);
        $countRows = imw_num_rows($getChargeDetailsQry);
        if($countRows<=0){
        ?>
            <tr>
                <td colspan="14" class="text-center lead"><?php echo imw_msg('no_rec');?></td>
            </tr>
            <?php
        }		
        
        while($getChargeDetailsRow = imw_fetch_array($getChargeDetailsQry)){
            $charge_list_id = $getChargeDetailsRow['charge_list_id'];
            $gro_id = $getChargeDetailsRow['gro_id'];
            $submitted = $getChargeDetailsRow['submitted'];
            $postedDate = $getChargeDetailsRow['firstSubmitDate'];
            if($postedDate!='0000-00-00'){
                list($year, $month, $day) = explode("-", $postedDate);
                $postedDate = date(''.$global_date_format.'',mktime(0,0,0,$month,$day,$year));
            }else{
                $postedDate = '-';
            }
            if($submitted=='true'){
                $postedDate=$postedDate;
            }else{
                $postedDate="-";
            }
            $encounter_id = $getChargeDetailsRow['encounter_id'];
            $overPaymentTotal = $getChargeDetailsRow['overPayment'];
    
            $charge_list_id = $getChargeDetailsRow['charge_list_id'];
            $copay = number_format($getChargeDetailsRow['copay'], 2);					
            $copayPaid = $getChargeDetailsRow['copayPaid'];
            $coPayNotRequired = $getChargeDetailsRow['coPayNotRequired'];
            $coPayWriteOff =  $getChargeDetailsRow['coPayWriteOff'];
            $totalAmt = number_format($getChargeDetailsRow['totalAmt'], 2);
            $approvedTotalAmt = $getChargeDetailsRow['approvedTotalAmt'];
            $deductibleTotalAmt = $getChargeDetailsRow['deductibleTotalAmt'];
            $amtCredit = number_format($getChargeDetailsRow['creditAmount'], 2);
            $amtPaid = $getChargeDetailsRow['amtPaid'];
            $newBal = $getChargeDetailsRow['totalBalance'];
            $case_type_id = $getChargeDetailsRow['case_type_id'];
            $primaryInsuranceCoId = $getChargeDetailsRow['primaryInsuranceCoId'];
            $secondaryInsuranceCoId = $getChargeDetailsRow['secondaryInsuranceCoId'];
            $tertiaryInsuranceCoId = $getChargeDetailsRow['tertiaryInsuranceCoId'];
            //------------------- INSURANCE CASE -------- -----------//
            $getInsCaseInfoStr = "SELECT a.*, b.* FROM 
                                    insurance_case a,
                                    insurance_case_types b
                                    WHERE a.ins_caseid='$case_type_id'
                                    AND a.ins_case_type=b.case_id";
            $getInsCaseInfoQry = imw_query($getInsCaseInfoStr);
            $getInsCaseInfoQryRow = imw_fetch_array($getInsCaseInfoQry);
            if($case_type_id>0 && ($primaryInsuranceCoId>0 || $secondaryInsuranceCoId>0 || $tertiaryInsuranceCoId>0)){
                $insCaseTypeNameId = $getInsCaseInfoQryRow['case_name']."-".$case_type_id;
            }else{
                $insCaseTypeNameId = 'Self Pay';
            }	
            //------------------- INSURANCE CASE -------------------//
            
            $date_of_service = $getChargeDetailsRow['date_of_service'];
            list($dosYear, $dosMonth, $dosDay) = explode("-", $date_of_service);
            $date_of_service = date(''.$global_date_format.'',mktime(0,0,0,$dosMonth,$dosDay,$dosYear));
    
        //----------------------- PROCEDURE DETAILS -----------------------//
            
        // Deposit amount
            $getPaymentInfoStr = "SELECT sum(paidForProc) as tot_paidproc FROM 
                                patient_chargesheet_payment_info a,
                                patient_charges_detail_payment_info b
                                WHERE a.encounter_id = '$encounter_id'
                                AND a.payment_id = b.payment_id
                                AND b.deletePayment=0
                                and a.paymentClaims='Deposit'
                                ORDER BY a.payment_id DESC";
            $getPaymentInfoQry = @imw_query($getPaymentInfoStr);
            $totaldepositAmt = 0;
            $getPaymentInfoRows = @imw_fetch_array($getPaymentInfoQry);
            $totaldepositAmt = $getPaymentInfoRows['tot_paidproc'];
            if($getPaymentInfoRows['tot_paidproc']){
                $totaldepositAmt=$getPaymentInfoRows['tot_paidproc'];
            }else{
                $totaldepositAmt='0.00';
            }
            // Deposit amount
                        
            $getProcedureDetailsStr = "SELECT procCode FROM patient_charge_list_details
                                        WHERE del_status='0' and charge_list_id='$charge_list_id'";
            $getProcedureDetailsQry = imw_query($getProcedureDetailsStr);
            $cptPracCode="";
            $count=0;
            while($getProcedureDetailsRows = imw_fetch_array($getProcedureDetailsQry)){
                $cptId = $getProcedureDetailsRows['procCode'];
                    $getProcDetailsStr = "SELECT cpt_prac_code FROM cpt_fee_tbl
                                        WHERE cpt_fee_id='$cptId' AND delete_status = '0'";
                    $getProcDetailsQry = imw_query($getProcDetailsStr);
                    $getProcDetailsRow = imw_fetch_array($getProcDetailsQry);
                        $cptPracCode = $getProcDetailsRow['cpt_prac_code'];
                    ++$count;
            }
            if($count>1){
                $cptPracCode = 'Multi';
            }
            //----------------------- PROCEDURE DETAILS -----------------------//
            
            //----------------------- PAID BY -----------------------//
            $getPaidByStr = "SELECT paid_by,insProviderId FROM patient_chargesheet_payment_info
                            WHERE encounter_id='$encounter_id'
                            AND markPaymentDelete != '1'";
            $getPaidByQry = imw_query($getPaidByStr);
            $c = 0;
            $paid_by = "";
            $insProviderIds=array();
            $paid_by_final="";
            while($getPaidByRows = imw_fetch_array($getPaidByQry)){
                $paid_by = $getPaidByRows['paid_by'];
                $insProviderIds[] = $getPaidByRows['insProviderId'];
                if($c<=0){
                    $tempPaidBy = $paid_by;
                }else{
                    if($tempPaidBy != $paid_by){
                        $paid_by = "Multi";
                        break;
                    }
                }
                ++$c;
            }
            //print_r($insProviderIds);
            if($paid_by == ''){
                $paid_by_final = '';
            }else if(count(array_unique($insProviderIds))>1){
                $paid_by_final="Multi";
            }else if($paid_by == 'Insurance'){
                $insProviderIds=array_unique($insProviderIds);
            }else if($paid_by=='Multi'){
                $paid_by_final="Multi";
            }else if($paid_by=='Patient'){
                $paid_by_final="Patient";
            }
            //----------------------- PAID BY -----------------------//
            ++$seq;
            $groupQry=imw_query("select * from groups_new where gro_id='$gro_id'");
            $groupDetail=imw_fetch_object($groupQry);
            $group_color=$groupDetail->group_color;
            if($group_color){
                if($group_color=='#FFFFFF'){
                    $g_color="#ffffff";
                    $f_class="text_10ab";
                }else{
                    $g_color=$group_color;
                    $f_class="text_10ab_white";
                }
            }else{
                $g_color="#ffffff";
                $f_class="text_10ab";
            }
            
            $trans_allow_amt_total="";
            $sel_tran_allow_amt=imw_query("select sum(trans_amt) as trans_amt_total,proc_allow_amt 
                            from manual_batch_transactions
                            where
                            encounter_id='$encounter_id' and
                            (payment_claims='Allowed')
                            and post_status!=1
                            and del_status=0");
            $fet_tran_allow_amt=imw_fetch_array($sel_tran_allow_amt);				
                
            $trans_allow_amt_total=$fet_tran_allow_amt['trans_amt_total'];
            $proc_allow_amt=$fet_tran_allow_amt['proc_allow_amt'];	
            $chk_diff_allow_amt=0;
            if($approvedTotalAmt>$proc_allow_amt && $proc_allow_amt>0){
                 $chk_diff_allow_amt=$approvedTotalAmt-$proc_allow_amt;
            }
            if($proc_allow_amt>0){
                $approvedTotalAmt=$proc_allow_amt;
            }
            
            if($trans_allow_amt_total>0){
                $chk_allow_amt_trans=$trans_allow_amt_total;
            }
            
            
            $trans_amt_paid_total="";
            $sel_tran_paid_amt=imw_query("select sum(trans_amt) as trans_amt_total 
                            from manual_batch_transactions
                            where
                            encounter_id='$encounter_id'
                            and (payment_claims='Paid' || payment_claims='Deposit'
                                || payment_claims='Interest Payment')
                            and post_status!=1
                            and del_status=0");
            $trans_paid_amt=imw_fetch_array($sel_tran_paid_amt);	
            $trans_amt_paid_total=$trans_paid_amt['trans_amt_total']+$amtPaid;
            
            
            $trans_amt_deposit_total="";
            $sel_tran_dep_amt=imw_query("select sum(trans_amt) as trans_amt_total 
                            from manual_batch_transactions
                            where
                            encounter_id='$encounter_id'
                            and (payment_claims='Deposit')
                            and post_status!=1
                            and del_status=0");
            $trans_deposit_amt=imw_fetch_array($sel_tran_dep_amt);
            $trans_amt_deposit_total=$trans_deposit_amt['trans_amt_total']+$totaldepositAmt;
            
            
            
            $trans_amt_deduct_total="";
            $sel_tran_deduct_amt=imw_query("select sum(trans_amt) as trans_amt_total 
                            from manual_batch_transactions
                            where
                            encounter_id='$encounter_id'
                            and (payment_claims='Deductible')
                            and post_status!=1
                            and del_status=0");
            $trans_deduct_amt=imw_fetch_array($sel_tran_deduct_amt);
            $trans_amt_deduct_total=$trans_deduct_amt['trans_amt_total']+$deductibleTotalAmt;
            
            
            
            $trans_amt_total="";
            $sel_tran_amt=imw_query("select sum(trans_amt) as trans_amt_total 
                            from manual_batch_transactions
                            where
                            encounter_id='$encounter_id' and
                            (payment_claims='Paid' || payment_claims='Deposit'
                             || payment_claims='Write Off'  || payment_claims='Discount')
                             and post_status!=1
                            and del_status=0");
            $fet_tran_amt=imw_fetch_array($sel_tran_amt);
            $trans_amt_total=$fet_tran_amt['trans_amt_total'];	
            $newBal_chk=$newBal-$trans_amt_total;
            
            $trans_amt_write_total="";
            $sel_tran_write_amt=imw_query("select sum(trans_amt) as trans_amt_total 
                            from manual_batch_transactions
                            where
                            encounter_id='$encounter_id' and
                            (payment_claims='Write Off'  || payment_claims='Discount')
                            and post_status!=1
                            and del_status=0");
            $fet_tran_write_amt=imw_fetch_array($sel_tran_write_amt);				
            $trans_amt_write_total=$fet_tran_write_amt['trans_amt_total'];	
            
            
            if($trans_amt_paid_total>$approvedTotalAmt){
                $amtPaid=$approvedTotalAmt-$trans_amt_write_total;
            }else{
                $amtPaid=$trans_amt_paid_total;
            }
            
            $amtPaid=number_format($amtPaid,2);
            $approvedTotalAmt=number_format($approvedTotalAmt,2);
            $totaldepositAmt=number_format($trans_amt_deposit_total,2);
            $deductibleTotalAmt=number_format($trans_amt_deduct_total,2);
            $newBal=$newBal_chk-$chk_diff_allow_amt;
            $newBal=number_format($newBal,2);
            
            $adj_amt_total="";
            $sel_adj_amt="select sum(trans_amt) as trans_amt_total 
                            from manual_batch_transactions
                            where
                            encounter_id='$encounter_id'
                            and (payment_claims='Adjustment' || payment_claims='Over Adjustment')
                            and post_status!=1
                            and del_status=0
                            ";
            $qry_adj_amt=imw_query($sel_adj_amt);
            $adj_paid_amt=imw_fetch_array($qry_adj_amt);
            $adj_amt_total=$adj_paid_amt['trans_amt_total'];
            
            if($newBal<0){
                $overPaymentTotal=$overPaymentTotal+$chk_diff_allow_amt+substr($newBal,1);
                $newBal="0.00";
            }
            
            if($adj_amt_total>0){
                if($overPaymentTotal>0){
                    if($overPaymentTotal>=$adj_amt_total){
                        $overPaymentTotal=$overPaymentTotal-$adj_amt_total;
                        $newBal=0.00;
                    }else{
                        $chk_adj_bal=$adj_amt_total-$overPaymentTotal;
                        $newBal=$chk_adj_bal;
                        $overPaymentTotal=0;
                    }
                }else{
                    $newBal=$adj_amt_total;
                    $overPaymentTotal=0;
                }
            }
            
            $overPaymentTotal=number_format($overPaymentTotal,2);
            
            $sel_trans_paid_arr=imw_query("select insurance_id,trans_by  from manual_batch_transactions
                                            where encounter_id='$encounter_id'
                                            and del_status=0");
            $chk_by_ins=array();								
            while($fet_trans_paid=imw_fetch_array($sel_trans_paid_arr)){
            
                if($fet_trans_paid['trans_by']=='Patient'){
                    $chk_by_pat=$fet_trans_paid['trans_by'];
                }
                
                if($fet_trans_paid['trans_by']=='Insurance'){
                    $chk_by_ins[]=$fet_trans_paid['insurance_id'];
                }
            }	
            $marge_ins_id=array_merge($chk_by_ins,$insProviderIds);
            
            if($paid_by_final=="Multi"){
                $paid_by_final="Multi";
            }else{
                if(($paid_by_final=="Patient" || $chk_by_pat=="Patient") && count(array_unique($marge_ins_id))>0){
                    $paid_by_final="Multi";
                }else if(count(array_unique($marge_ins_id))>1){
                    $paid_by_final="Multi";
                }else if($chk_by_pat=="Patient"){
                    $paid_by_final="Patient";
                }
            }
            $insProviderIds=array_unique($marge_ins_id);
            if(count($insProviderIds)>0 && $paid_by_final!="Multi"){
                foreach($insProviderIds as $insProviderId){		
                //echo $insProviderId;
                    $insCoQry=imw_query("select * from insurance_companies where id='$insProviderId'");
                    $insCoDetails=imw_fetch_object($insCoQry);
                    $paid_by = $insCoDetails->in_house_code;
                
                     if($insCoDetails->in_house_code==''){
                        $paid_by = $insCoDetails->name;
                     }						 
                     if(strlen($paid_by)>10){
                        $paid_by = substr($paid_by, 0, 8).'...';
                     }
                    $paid_by_final.=$paid_by.'<br>'; 
                }
            }
            if($paid_by_final==""){
            $paid_by_final='-';
            }
        ?>
        <tr height="18" style="background-color:<?php echo $g_color; ?>;">
            <td>
                <a class="<?php echo $f_class; ?>" href="batch_transactions.php?b_id=<?php echo $_REQUEST['b_id']; ?>&enc_id=<?php echo $encounter_id; ?>">
                    <?php echo $seq."."; ?>
                </a>
            </td>
            <td class="text-nowrap">
                <a class="<?php echo $f_class; ?>" href="batch_transactions.php?b_id=<?php echo $_REQUEST['b_id']; ?>&enc_id=<?php echo $encounter_id; ?>">						
                    <?php echo $date_of_service; ?>
                </a>
            </td>
            <td>
                <a class="<?php echo $f_class; ?>" href="batch_transactions.php?b_id=<?php echo $_REQUEST['b_id']; ?>&enc_id=<?php echo $encounter_id; ?>">						
                    <?php echo $encounter_id; ?>
                </a>
            </td>
            <td>
                <a class="<?php echo $f_class; ?>" href="batch_transactions.php?b_id=<?php echo $_REQUEST['b_id']; ?>&enc_id=<?php echo $encounter_id; ?>">						
                    <?php echo $insCaseTypeNameId; ?>
                </a>
            </td>
            <td>
                <a class="<?php echo $f_class; ?>" href="batch_transactions.php?b_id=<?php echo $_REQUEST['b_id']; ?>&enc_id=<?php echo $encounter_id; ?>">						
                    <?php echo $cptPracCode; ?>
                </a>
            </td>
            <td>
                <a class="<?php echo $f_class; ?>" href="batch_transactions.php?b_id=<?php echo $_REQUEST['b_id']; ?>&enc_id=<?php echo $encounter_id; ?>">						
                    <?php 
                    if($coPayNotRequired == 0){
                        if($coPayWriteOff!='1'){
                            echo (!empty($copayPaid) || ($copay<=0)) ? "<font color=\"Green\">" : "<font color=\"red\">";
                            echo $showCurrencySymbol.$copay;
                            echo "</font>";
                        }else{
                        ?>
                        <span class="text_10b" style="color:#FF0000;"><?php echo $showCurrencySymbol.$copay; ?></span>
                        <span style="font-family:Arial; font-size:12px; font-weight:bold; color:#0000FF;">W O</span>
                        <?php 
                        }
                    }else{
                        ?>
                        <span class="text_10b" style="color:#FF0000;"><?php echo $showCurrencySymbol.$copay; ?></span>
                        <span style="font-family:Arial; font-size:12px; font-weight:bold; color:#0000FF;">NR</span>
                        <?php 
                    }
                    ?>
                </a>
            </td>
            <td>
                <a class="<?php echo $f_class; ?>" href="batch_transactions.php?b_id=<?php echo $_REQUEST['b_id']; ?>&enc_id=<?php echo $encounter_id; ?>">
                    <?php echo $showCurrencySymbol.$totalAmt; ?>
                </a>
            </td>
            <td>
                <a class="<?php echo $f_class; ?>" href="batch_transactions.php?b_id=<?php echo $_REQUEST['b_id']; ?>&enc_id=<?php echo $encounter_id; ?>">
                    <?php echo $showCurrencySymbol.$approvedTotalAmt; ?>
                </a>
            </td>
            <td>
                <a class="<?php echo $f_class; ?>" href="batch_transactions.php?b_id=<?php echo $_REQUEST['b_id']; ?>&enc_id=<?php echo $encounter_id; ?>">
                    <span style="color:#FFC000;font-weight:bold;">
                        <?php echo $showCurrencySymbol.number_format($deductibleTotalAmt, 2); //$Nowdeduct_amount ?>
                    </span>
                </a>
            </td>
            <td>
                <a class="<?php echo $f_class; ?>" href="batch_transactions.php?b_id=<?php echo $_REQUEST['b_id']; ?>&enc_id=<?php echo $encounter_id; ?>">
                    <?php echo $showCurrencySymbol.$totaldepositAmt; ?>
                </a>
            </td>
            <td>
                <a class="<?php echo $f_class; ?>" href="batch_transactions.php?b_id=<?php echo $_REQUEST['b_id']; ?>&enc_id=<?php echo $encounter_id; ?>">
                    <?php 
                    if($amtPaid<=0){
                        ?>
                        <span style="color:#009900;font-weight:bold;">
                        <?php
                        //echo $amtPaid; 
                        echo $showCurrencySymbol.'0.00';
                        ?>
                        </span>
                        <?php
                    }else{
                        ?>
                        <span style="color:#009900;font-weight:bold;">
                        <?php
                        echo $showCurrencySymbol.$amtPaid; 
                        ?>
                        </span>
                        <?php
                    }
                    ?>
                </a>
            </td>
            <td>
                <a class="<?php echo $f_class; ?>" href="batch_transactions.php?b_id=<?php echo $_REQUEST['b_id']; ?>&enc_id=<?php echo $encounter_id; ?>">
                    <?php if($amtPaid>0) echo $paid_by_final; else echo '-'; ?>
                </a>
            </td>
            <td>
                <a class="<?php echo $f_class; ?>" href="batch_transactions.php?b_id=<?php echo $_REQUEST['b_id']; ?>&enc_id=<?php echo $encounter_id; ?>">
                    <?php 
                    if($overPaymentTotal>0){ 
                        echo '<span style="color:#5D738E;"><b>-'.$showCurrencySymbol.number_format($overPaymentTotal, 2).'</b></span>'; 
                    }else{
                        ?><font color="<?php echo ($newBal <= 0) ? "Green" : "Red";?>"><?php echo $showCurrencySymbol.$newBal; ?></font><?php
                    }
                    ?>
                </a>
            </td>
            <td class="text-nowrap">
                <a class="<?php echo $f_class; ?>" href="batch_transactions.php?b_id=<?php echo $_REQUEST['b_id']; ?>&enc_id=<?php echo $encounter_id; ?>">
                    <?php echo $postedDate; ?>
                </a>
            </td>
            <td class="<?php echo $f_class; ?>">
                <?php
                    $getSubmittedDateStr = "SELECT submited_date FROM submited_record WHERE encounter_id = '$encounter_id' ORDER BY submited_id DESC";
                    $getSubmittedDateQry = imw_query($getSubmittedDateStr);
                    $submittedDateRows = imw_num_rows($getSubmittedDateQry);
                    if($submittedDateRows>0){
                ?>
                <select name="hcfaSubmittedDat" class="selectpicker" data-width="100%">
                    <?php
                            while($getSubmittedDateRow = imw_fetch_array($getSubmittedDateQry)){			
                            $hcfaSubmittedDate = $getSubmittedDateRow['submited_date'];					
                                list($hcfaYear, $hcfaMonth, $hcfaDay) = explode("-", $hcfaSubmittedDate);
                                $hcfaSubmittedDate = date(''.$global_date_format.'',mktime(0,0,0,$hcfaMonth,$hcfaDay,$hcfaYear));
                    ?>
                    <option><?php echo $hcfaSubmittedDate; ?></option>
                    <?php } ?>
                </select>
                <?php }else{ echo '-';}?>
            </td>
        </tr>
        <?php } ?>
    </table>
</div> 
<div class="col-sm-12 text-center">
    <input type="button" class="btn btn-danger" align="bottom" name="close" id="close" onclick="close_fun()" value="Close">	
</div>   
 <?php 
	$pat_id=$review_patient_id;
	$getCollectionAmtStr = "SELECT * FROM patient_charge_list WHERE del_status='0' and patient_id='$pat_id' and collection = 'true'";
	$getCollectionAmtQry = imw_query($getCollectionAmtStr);
	if(imw_num_rows($getCollectionAmtQry)>0){
	?>
	<script type="text/jscript">
		fancyAlert("<font color='#ff0000'><b>Patient Under Collection.</b></font>");
	</script>
	<?php } ?>
</body>
</html>