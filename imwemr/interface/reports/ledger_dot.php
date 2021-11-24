

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
?>
<?php
/*
FILE : productivity_physcian_payments.php
PURPOSE : PRODUCTIVITY PAYMENTS FOR PHYSICIAN
ACCESS TYPE : DIRECT
*/
//require_once(dirname(__FILE__).'/../common/functions.inc.php');

//MAKING QUERY PART ------------------

if($chk_src_date == ""){
	$chk_src_date =  getDateFormatDB(date($phpDateFormat));
}

$qryBigPart='';
if(empty($grp_id) == false){
	$qryBigPart .= " and main.gro_id IN ($grp_id)";
}
if(empty($Physician)===false){
	$qryBigPart.= " and main.primary_provider_id_for_reports IN ($Physician)";
}
if(empty($credit_physician) === false){
	$qryBigPart.= " and main.sec_prov_id IN ($credit_physician)";
}
if($chksamebillingcredittingproviders==1){
	$qryBigPart.= " and main.primary_provider_id_for_reports!=main.sec_prov_id";							
}	

if(trim($insuranceName) != ''){
	if(trim($ins_type) == ''){
		$qryBigPart .= " and (main.pri_ins_id in ($insuranceName)
		or main.sec_ins_id in ($insuranceName)
		or main.tri_ins_id in ($insuranceName))";
	}
	else{
		$ins_type_arr=explode(',',$ins_type);
		$qryBigPart .= " and (";
		for($i=0;$i<count($ins_type_arr);$i++){
			$ins_nam=$ins_type_arr[$i];
			if(trim($ins_nam)!='Self Pay')
			{
				$mul_or="";
				if($i>0){
					$mul_or=" or ";
				}
				$qryBigPart .= " $mul_or main.$ins_nam in ($insuranceName)";
			}
		}
		$qryBigPart .= " )";
	}
}
//------------------

//IF CHECKVIEW SELECTED
if($reportType=='checkView'){
	//TRANSACTIONS TABLE
	$qry="Select trans.report_trans_id, trans.patient_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, 
	trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_qry_type, trans.trans_method, trans.check_number, trans.parent_id,
	trans.cc_type, trans.cc_number, trans.facility_id,
	DATE_FORMAT(trans.trans_dop, '$dateFormat') as 'trans_dop', trans.trans_del_operator_id  
	FROM report_enc_trans trans WHERE trans.trans_type!='charges' AND (trans.trans_del_operator_id='0' || (trans.trans_del_operator_id>0 && (trans.trans_del_date<='$chk_src_date'))) 
	AND LOWER(trans.trans_method) IN('check','eft','money order') $chAmtQryCriteria";
	if(empty($Start_date)==false && empty($End_date)==false){
		if($DateRangeFor=='transaction_date'){
			$qry.=" AND (trans_dot BETWEEN '$Start_date' and '$End_date')";
		}else{
			$qry.=" AND (trans_dop BETWEEN '$Start_date' and '$End_date')";	
		}
	}
	if(trim($insuranceName) != ''){
		$qry.= " AND trans.trans_ins_id in($operatorName)";
	}
	if(empty($operatorName)==false){
		$qry.= " AND trans.trans_operator_id in($operatorName)";
	}
	if(empty($sc_name)==false && $pay_location=='1'){
		$qry.=" AND trans.facility_id IN(".$sc_name.")";	
	}
	if(empty($strBatchIds)==false){
		$qry.=" AND trans.batch_id IN(".$strBatchIds.")";
	}
	
	$qry.=" ORDER BY trans.trans_dot, trans.trans_dot_time, trans.report_trans_id";
	$rs=imw_query($qry);
	while($res = imw_fetch_assoc($rs)){
		$report_trans_id=$res['report_trans_id'];
		$encounter_id= $res['encounter_id'];
		$chgDetId= $res['charge_list_detail_id'];
		$insCompId = $res['trans_ins_id'];
		$encounterIdArr[$res['encounter_id']] = $res['encounter_id'];
		$trans_type= strtolower($res['trans_type']);
		$trans_by= strtolower($res['trans_by']);
		$check_number= (empty($res['check_number'])==false)? strtolower(trim($res['check_number'])) : '0';
		$res['trans_method']= strtolower($res['trans_method']);			
		$payloc=$res['facility_id'];
		$tempRecordData[$report_trans_id]=$res['trans_amount'];
		$tempRecordDetail[$report_trans_id]['check_no']=$check_number;
		$tempRecordDetail[$report_trans_id]['pay_loc']=$payloc;
		$tempRecordDetail[$report_trans_id]['trans_amt']=$res['trans_amount'];
		
		
		$arrCheckNos[$chgDetId][$check_number] = $check_number;

		//RECORD FIRST RECORD ID TO SOLVE PROBLEM EVEN IF RECORD IS MODIFIED MORE THAN TWO TIME.
		//THIS IS DONE SO THAT RECORD WILL DISPLAY ONLY LAST MODIFED PAYMENT METHOD IN RESULT.
		//BUT THERE ARE DIFFERENT OPERATORS MODIFIED SAME PAYMENT AND REPORT IS SEARCHED FOR PARTICULAR OPERATOR THEN THIS LOGIC MAY FAIL.
		$record_id=($res['parent_id']>0)? $res['parent_id']: $report_trans_id;
		if($res['parent_id']>0){
			if($tempRecording[$res['parent_id']]>0){
				$tempArrFirstRecordid[$report_trans_id]=$tempArrFirstRecordid[$res['parent_id']];
			}else{
				$tempArrFirstRecordid[$report_trans_id]=$res['parent_id'];
			}
			$tempRecording[$report_trans_id]=$report_trans_id;
		}
		$record_id=($tempArrFirstRecordid[$report_trans_id]>0)? $tempArrFirstRecordid[$report_trans_id]: $record_id;
		//--------------------------------------------
		

		switch($trans_type){
			case 'paid':
			case 'copay-paid':
			case 'deposit':
			case 'interest payment':
			case 'negative payment':
			case 'copay-negative payment':
				$paidForProc=$res['trans_amount'];
				if($trans_type=='negative payment' || $trans_type=='copay-negative payment' || $res['trans_del_operator_id']>0)$paidForProc="-".$res['trans_amount'];
				if(($trans_type=='negative payment' || $trans_type=='copay-negative payment') && $res['trans_del_operator_id']>0)$paidForProc=$res['trans_amount'];
	
				//IF parent_id >0 THEN IT MEANS RECORD IS UPDATED. THEN REMOVE PREVIOUS FETCHED AMOUNT.
				$prevFetchedAmt=0;
				if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
					$prevFetchedAmt = ($trans_type=='negative payment' || $trans_type=='copay-negative payment') ? $tempRecordData[$res['parent_id']] : "-".$tempRecordData[$res['parent_id']];
					
					//IF CHECK NUMBER/PAY LOCATION CHANGED THEN REMOVE AMT FROM OLD CHECK NUMBER
					$old_check_no=$tempRecordDetail[$res['parent_id']]['check_no'];
					$old_pay_loc=$tempRecordDetail[$res['parent_id']]['pay_loc'];
					$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
					if($old_check_no!=$check_number || $old_pay_loc!=$payloc){
						if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
							if($trans_by == 'patient' || $trans_by == 'res. party'){
								$patPayCheckDetArr[$old_pay_loc][$old_check_no][$chgDetId]['patPaid']-=$old_trans_amt;
							}else{
								$patPayCheckDetArr[$old_pay_loc][$old_check_no][$chgDetId]['insPaid']-=$old_trans_amt;
							}
							$mainEncounterCheckPayArr[$old_pay_loc][$old_check_no][$chgDetId]-= $old_trans_amt;
							$patPayCheckDetArr[$old_pay_loc][$old_check_no][$chgDetId]['paid'][$record_id]-=$old_trans_amt;
						}else{
							if($trans_by == 'patient' || $trans_by == 'res. party'){
								$patPayCheckDetArr[$old_check_no][$chgDetId]['patPaid']-=$old_trans_amt;
							}else{
								$patPayCheckDetArr[$old_check_no][$chgDetId]['insPaid']-=$old_trans_amt;
							}
							$mainEncounterCheckPayArr[$old_check_no][$chgDetId]-= $old_trans_amt;
							$patPayCheckDetArr[$old_check_no][$chgDetId]['paid'][$record_id]-=$old_trans_amt;
						}
						$prevFetchedAmt=0;
					}
				}
				
				if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
					if($trans_by == 'patient' || $trans_by == 'res. party'){
						$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['patPaid']+= $paidForProc + $prevFetchedAmt;
					}else if($trans_by == 'insurance'){
						if(empty($insuranceName)===true || (empty($insuranceName)===false && $arrInsurance[$insCompId])){	
	
							$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['insPaid']+= $paidForProc + $prevFetchedAmt;
							$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['insComp']= $insCompId;
							$tempArrInsPay[$encounter_id][$chgDetId][$insCompId]+= $paidForProc + $prevFetchedAmt;
						}else{
							$paidForProc=0;
						}						
					}
					$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['paid'][$record_id]+= $paidForProc + $prevFetchedAmt;
					$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['method'][$record_id]= $res['trans_method'];
					$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['cc_type'][$record_id]= $res['cc_type'];
					$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['cc_number'][$record_id]= $res['cc_number'];
					$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['paid_date'][$record_id] = $res['trans_dop'];
					$tempTransPayLoc[$chgDetId][$check_number][$payloc]=$payloc;

					$mainEncounterCheckPayArr[$payloc][$check_number][$chgDetId]+= $paidForProc + $prevFetchedAmt;
				}else{
					
					if($trans_by == 'patient' || $trans_by == 'res. party'){
						$patPayCheckDetArr[$check_number][$chgDetId]['patPaid']+= $paidForProc + $prevFetchedAmt;
					}else if($trans_by == 'insurance'){
						if(empty($insuranceName)===true || (empty($insuranceName)===false && $arrInsurance[$insCompId])){	
	
							$patPayCheckDetArr[$check_number][$chgDetId]['insPaid']+= $paidForProc + $prevFetchedAmt;
							$patPayCheckDetArr[$check_number][$chgDetId]['insComp']= $insCompId;
							$tempArrInsPay[$encounter_id][$chgDetId][$insCompId]+= $paidForProc + $prevFetchedAmt;
						}else{
							$paidForProc=0;
						}						
					}
			
					$patPayCheckDetArr[$check_number][$chgDetId]['paid'][$record_id]+= $paidForProc + $prevFetchedAmt;
					$patPayCheckDetArr[$check_number][$chgDetId]['method'][$record_id]= $res['trans_method'];
					$patPayCheckDetArr[$check_number][$chgDetId]['cc_type'][$record_id]= $res['cc_type'];
					$patPayCheckDetArr[$check_number][$chgDetId]['cc_number'][$record_id]= $res['cc_number'];
					$patPayCheckDetArr[$check_number][$chgDetId]['paid_date'][$record_id] = $res['trans_dop'];					

					$mainEncounterCheckPayArr[$check_number][$chgDetId]+= $paidForProc + $prevFetchedAmt;
				}
				
			break;
	
			case 'credit':
			case 'debit':
				$crddbtamt=$res['trans_amount'];
				if($trans_type=='credit'){ 
					$crddbtamt= ($res['trans_del_operator_id']>0) ? "-".$res['trans_amount'] : $res['trans_amount'];							
				}else{  //debit
					$crddbtamt= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];				
				}
	
				$prevFetchedAmt=0;
				if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
					$prevFetchedAmt = ($trans_type=='credit') ? "-".$tempRecordData[$res['parent_id']] : $tempRecordData[$res['parent_id']];

					//IF CHECK NUMBER/PAY LOCATION CHANGED THEN REMOVE AMT FROM OLD CHECK NUMBER
					$old_check_no=$tempRecordDetail[$res['parent_id']]['check_no'];
					$old_pay_loc=$tempRecordDetail[$res['parent_id']]['pay_loc'];
					$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
					if($old_check_no!=$check_number || $old_pay_loc!=$payloc){
						if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
							if($trans_by=='insurance'){
								$patPayCheckDetArr[$old_pay_loc][$old_check_no][$chgDetId]['insPaid']-=$old_trans_amt;
							}else{
								$patPayCheckDetArr[$old_pay_loc][$old_check_no][$chgDetId]['patPaid']-=$old_trans_amt;
							}
							$mainEncounterCheckPayArr[$old_pay_loc][$old_check_no][$chgDetId]-= $old_trans_amt;
							$patPayCheckDetArr[$old_pay_loc][$old_check_no][$chgDetId]['paid']-=$old_trans_amt;
						}else{
							if($trans_by=='insurance'){
								$patPayCheckDetArr[$old_check_no][$chgDetId]['insPaid']-=$old_trans_amt;
							}else{
								$patPayCheckDetArr[$old_check_no][$chgDetId]['patPaid']-=$old_trans_amt;
							}
							$mainEncounterCheckPayArr[$old_check_no][$chgDetId]-= $old_trans_amt;
							$patPayCheckDetArr[$old_check_no][$chgDetId]['paid']-=$old_trans_amt;
						}
						$prevFetchedAmt=0;
					}
				}
				
				if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
					if($trans_by=='insurance'){
						$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['insPaid']+= $crddbtamt+$prevFetchedAmt;
						if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['paid_date'][] = $res['trans_dop'];
					}else{
						$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['patPaid']+= $crddbtamt+$prevFetchedAmt;
						if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['paid_date'][] = $res['trans_dop'];					
					}
					$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['paid'][$record_id]+= $crddbtamt + $prevFetchedAmt;
					$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['method'][$record_id]= $res['trans_method'];
					$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['cc_type'][$record_id]= $res['cc_type'];
					$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['cc_number'][$record_id]= $res['cc_number'];
					$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['paid_date'][$record_id] = $res['trans_dop'];
					$tempTransPayLoc[$chgDetId][$check_number][$payloc]=$payloc;

					$mainEncounterCheckPayArr[$payloc][$check_number][$chgDetId]+= $crddbtamt + $prevFetchedAmt;
				}else{
					if($trans_by=='insurance'){
						$patPayCheckDetArr[$check_number][$chgDetId]['insPaid']+= $crddbtamt+$prevFetchedAmt;
						if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$check_number][$chgDetId]['paid_date'][] = $res['trans_dop'];					
					}else{
						$patPayCheckDetArr[$check_number][$chgDetId]['patPaid']+= $crddbtamt+$prevFetchedAmt;				
						if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$check_number][$chgDetId]['paid_date'][] = $res['trans_dop'];					
					}
					$patPayCheckDetArr[$check_number][$chgDetId]['paid'][$record_id]+= $crddbtamt + $prevFetchedAmt;
					$patPayCheckDetArr[$check_number][$chgDetId]['method'][$record_id]= $res['trans_method'];
					$patPayCheckDetArr[$check_number][$chgDetId]['cc_type'][$record_id]= $res['cc_type'];
					$patPayCheckDetArr[$check_number][$chgDetId]['cc_number'][$record_id]= $res['cc_number'];
					$patPayCheckDetArr[$check_number][$chgDetId]['paid_date'][$record_id] = $res['trans_dop'];						

					$mainEncounterCheckPayArr[$check_number][$chgDetId]+= $crddbtamt + $prevFetchedAmt;
				}
			break;

			case 'default_writeoff':
				if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
					$normalWriteOffCheckAmt[$payloc][$check_number][$chgDetId]= $res['trans_amount'];
					if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['paid_date'][] = $res['trans_dop'];
					$tempTransPayLoc[$chgDetId][$check_number][$payloc]=$payloc;
				}else{
					$normalWriteOffCheckAmt[$check_number][$chgDetId]= $res['trans_amount'];
					if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$check_number][$chgDetId]['paid_date'][] = $res['trans_dop'];
				}
			break;
			case 'write off':
			case 'discount':
				if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];
	
				$prevFetchedAmt=0;
				if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
					$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];

					//IF CHECK NUMBER/PAY LOCATION CHANGED THEN REMOVE AMT FROM OLD CHECK NUMBER
					$old_check_no=$tempRecordDetail[$res['parent_id']]['check_no'];
					$old_pay_loc=$tempRecordDetail[$res['parent_id']]['pay_loc'];
					$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
					if($old_check_no!=$check_number || $old_pay_loc!=$payloc){
						if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
							$writte_off_check_arr[$old_pay_loc][$old_check_no][$chgDetId]-=$old_trans_amt;
						}else{
							$writte_off_check_arr[$old_check_no][$chgDetId]-=$old_trans_amt;
						}
						$prevFetchedAmt=0;
					}					
				}
				
				if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
					$writte_off_check_arr[$payloc][$check_number][$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
					if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['paid_date'][] = $res['trans_dop'];
					$tempTransPayLoc[$chgDetId][$check_number][$payloc]=$payloc;
				}else{
					$writte_off_check_arr[$check_number][$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
					if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$check_number][$chgDetId]['paid_date'][] = $res['trans_dop'];
				}
			break;
			case 'over adjustment':
				if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];
	
				$prevFetchedAmt=0;
				if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
					$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];

					//IF CHECK NUMBER/PAY LOCATION CHANGED THEN REMOVE AMT FROM OLD CHECK NUMBER
					$old_check_no=$tempRecordDetail[$res['parent_id']]['check_no'];
					$old_pay_loc=$tempRecordDetail[$res['parent_id']]['pay_loc'];
					$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
					if($old_check_no!=$check_number || $old_pay_loc!=$payloc){
						if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
							$arrCheckAdjustmentAmt[$old_pay_loc][$old_check_no][$chgDetId]-=$old_trans_amt;
						}else{
							$arrCheckAdjustmentAmt[$old_check_no][$chgDetId]-=$old_trans_amt;
						}
						$prevFetchedAmt=0;
					}										
				}
						
				if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
					$arrCheckAdjustmentAmt[$payloc][$check_number][$chgDetId]+= $res['trans_amount']+ $prevFetchedAmt;
					if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['paid_date'][] = $res['trans_dop'];
					$tempTransPayLoc[$chgDetId][$check_number][$payloc]=$payloc;
				}else{
					$arrCheckAdjustmentAmt[$check_number][$chgDetId]+= $res['trans_amount']+ $prevFetchedAmt;
					if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$check_number][$chgDetId]['paid_date'][] = $res['trans_dop'];
				}
			break;
			case 'adjustment':
				$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
	
				$prevFetchedAmt=0;
				if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
					$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];

					//IF CHECK NUMBER/PAY LOCATION CHANGED THEN REMOVE AMT FROM OLD CHECK NUMBER
					$old_check_no=$tempRecordDetail[$res['parent_id']]['check_no'];
					$old_pay_loc=$tempRecordDetail[$res['parent_id']]['pay_loc'];
					$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
					if($old_check_no!=$check_number || $old_pay_loc!=$payloc){
						if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
							$arrCheckAdjustmentAmt[$old_pay_loc][$old_check_no][$chgDetId]-=$old_trans_amt;
						}else{
							$arrCheckAdjustmentAmt[$old_check_no][$chgDetId]-=$old_trans_amt;
						}
						$prevFetchedAmt=0;
					}						
				}

				if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
					$arrCheckAdjustmentAmt[$payloc][$check_number][$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
					if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['paid_date'][] = $res['trans_dop'];
					$tempTransPayLoc[$chgDetId][$check_number][$payloc]=$payloc;
				}else{
					$arrCheckAdjustmentAmt[$check_number][$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
					if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$check_number][$chgDetId]['paid_date'][] = $res['trans_dop'];
				}
			break;
			case 'returned check':
				$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
	
				$prevFetchedAmt=0;
				if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
					$prevFetchedAmt = $tempRecordData[$res['parent_id']];

					//IF CHECK NUMBER/PAY LOCATION CHANGED THEN REMOVE AMT FROM OLD CHECK NUMBER
					$old_check_no=$tempRecordDetail[$res['parent_id']]['check_no'];
					$old_pay_loc=$tempRecordDetail[$res['parent_id']]['pay_loc'];
					$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
					if($old_check_no!=$check_number || $old_pay_loc!=$payloc){
						if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
							$arrCheckAdjustmentAmt[$old_pay_loc][$old_check_no][$chgDetId]-=$old_trans_amt;
						}else{
							$arrCheckAdjustmentAmt[$old_check_no][$chgDetId]-=$old_trans_amt;
						}
						$prevFetchedAmt=0;
					}						
				}
				
				if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
					$arrCheckAdjustmentAmt[$payloc][$check_number][$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
					if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['paid_date'][] = $res['trans_dop'];
					$tempTransPayLoc[$chgDetId][$check_number][$payloc]=$payloc;
				}else{
					$arrCheckAdjustmentAmt[$check_number][$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
					if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$check_number][$chgDetId]['paid_date'][] = $res['trans_dop'];					
				}
			break;
			case 'refund':
				$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];		
	
				$prevFetchedAmt=0;
				if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
					$prevFetchedAmt = $tempRecordData[$res['parent_id']];

					//IF CHECK NUMBER/PAY LOCATION CHANGED THEN REMOVE AMT FROM OLD CHECK NUMBER
					$old_check_no=$tempRecordDetail[$res['parent_id']]['check_no'];
					$old_pay_loc=$tempRecordDetail[$res['parent_id']]['pay_loc'];
					$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
					if($old_check_no!=$check_number || $old_pay_loc!=$payloc){
						if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
							$arrCheckAdjustmentAmt[$old_pay_loc][$old_check_no][$chgDetId]-=$old_trans_amt;
						}else{
							$arrCheckAdjustmentAmt[$old_check_no][$chgDetId]-=$old_trans_amt;
						}
						$prevFetchedAmt=0;
					}						
				}
				
				if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
					$arrCheckAdjustmentAmt[$payloc][$check_number][$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
					if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$payloc][$check_number][$chgDetId]['paid_date'][] = $res['trans_dop'];
					$tempTransPayLoc[$chgDetId][$check_number][$payloc]=$payloc;
				}else{
					$arrCheckAdjustmentAmt[$check_number][$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
					if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$check_number][$chgDetId]['paid_date'][] = $res['trans_dop'];
				}
			break;
		}
	}
	unset($tempRecording);
	unset($tempArrFirstRecordid);

	//GET FIRST POSTED ENCOUTNERS FETCHED SEPARATELY DUE TO OPERATOR SELECTION
	$firstPostedEnc=array();
	if(empty($strChSearchEnc)==true){
		$qry="Select main.encounter_id FROM report_enc_detail main
		JOIN patient_data on patient_data.id = main.patient_id 
		WHERE (main.first_posted_date BETWEEN '$Start_date' AND '$End_date') 
		AND (main.del_status='0' OR (main.del_status='1' AND DATE_FORMAT(main.trans_del_date, '%Y-%m-%d')>'$End_date')) ".$qryBigPart;
		if(empty($operatorName)==false){
			$qry.= " AND main.operator_id in($operatorName)";
		}
		if(empty($sc_name) == false){
			if($pay_location=='1' || $billing_location=='1'){
				$qry.= " and main.billing_facility_id IN ($sc_name)";
			}else{
				$qry.= " and main.facility_id IN ($sc_name)";	
			}
		}
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$firstPostedEnc[$res['encounter_id']]=$res['encounter_id'];
		}unset($rs);
		//------------------------------
	}
	
	//MERGE WITH FIRST POSTED ENCOUNTERS
	$encounterIdArr=array_merge($encounterIdArr, $firstPostedEnc);
	$encounterIdArr = array_unique($encounterIdArr);
	$encounterIdStr = join(',',$encounterIdArr);
	
	//MAIN QUERY FOR CHECK VIEW
	if(sizeof($encounterIdArr)>0){
		$qry="Select main.charge_list_id, main.encounter_id, main.charge_list_detail_id, main.pri_ins_id, main.sec_ins_id, main.tri_ins_id,main.operator_id,
		(main.charges * main.units) as totalAmt, main.units, date_format(main.date_of_service,'".$dateFormat."') as date_of_service, main.date_of_service as 'dos', 
		date_format(main.entered_date,'".$dateFormat."') as entered_date,
		DATE_FORMAT(main.trans_del_date, '%Y-%m-%d') as trans_del_date,
		main.primary_provider_id_for_reports  as 'primaryProviderId', main.sec_prov_id,	
		main.proc_code_id, main.dx_id1, main.dx_id2, main.dx_id3, main.dx_id4, main.dx_id5, main.dx_id6, main.dx_id7, main.dx_id8, main.dx_id9,
		main.dx_id10, main.dx_id11, main.dx_id12, main.mod_id1, main.mod_id2, main.mod_id3, main.mod_id4, main.billing_facility_id, 
		main.proc_balance,	main.pri_due, main.sec_due, main.tri_due, main.pat_due, main.del_status, main.first_posted_date, 
		date_format(main.first_posted_date,'".$dateFormat."') as 'first_posted_date_formatted', main.over_payment,
		main.submitted, main.over_payment, main.re_submitted, main.re_submitted_date, 
		users.lname as physicianLname,users.fname as physicianFname, users.mname as physicianMname,
		pos_tbl.pos_prac_code as facilityPracCode,
		pos_facilityies_tbl.pos_facility_id, patient_data.id as patient_id,
		patient_data.lname,	patient_data.fname, patient_data.mname, patient_data.default_facility,
		main.write_off, cpt_fee_tbl.cpt_prac_code, cpt_fee_tbl.cpt4_code, cpt_fee_tbl.departmentId,
		cpt_fee_tbl.cpt_desc	
		FROM report_enc_detail main 
		JOIN patient_data on patient_data.id = main.patient_id 
		JOIN users on users.id = main.primary_provider_id_for_reports 
		LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = main.facility_id
		LEFT JOIN pos_tbl ON pos_tbl.pos_id = pos_facilityies_tbl.pos_id 
		JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = main.proc_code_id
		LEFT JOIN department_tbl on department_tbl.departmentId = cpt_fee_tbl.departmentId 		
		WHERE (main.del_status='0' OR (main.del_status='1' AND DATE_FORMAT(main.trans_del_date, '%Y-%m-%d')>'$chk_src_date'))";
		if(empty($departmentIds) == false){
			$qry.= " and department_tbl.departmentId IN ($departmentIds)";	
		}
		
		if($pay_location!='1'){	 // No need to pass facility here if pay location selected.
			if(empty($sc_name) == false){
				if($billing_location=='1'){
					$qry.= " and main.billing_facility_id IN ($sc_name)";
				}else{
					$qry.= " and main.facility_id IN ($sc_name)";	
				}
			}		
		}
		$qry.= $qryBigPart;
		if($pureSelfPay==1)$qry.= " and patient_charge_list_details.proc_selfpay=1";	
		
		if(sizeof($encounterIdArr)>0){
			$qry.= " AND main.encounter_id in ($encounterIdStr)";		
		}
		$qry .= " order by users.lname,users.fname, pos_facilityies_tbl.facilityPracCode, 
		patient_data.lname,patient_data.fname,main.date_of_service,main.encounter_id";
	
		
		$res=imw_query($qry);
		//--- PRIMARY PROVIDER ID ARRAY ----
		$arr_selected_phy = array();
		if(empty($Physician) === false){
			$arr_selected_phy = preg_split("/,/", $Physician);
		}
		
		$main_encounter_id_arr = array();
		$facilityNameArr = array();
		$physician_initial_arr = array();
		$arrPatNoFacility=array();
		while($rs = imw_fetch_assoc($res)){
			$encounter_id = $rs['encounter_id'];
			
			$charge_list_detail_id = $rs['charge_list_detail_id'];
			$doctor_id = $rs['primaryProviderId'];
			$secondaryProviderId = $rs['sec_prov_id'];
		
			//---- GET POS FACILITY NAME ---
			$pos_facility_id = ($pay_location=='1' || $billing_location=='1') ? $rs['billing_facility_id'] : $rs['pos_facility_id'];	
			$pos_facility_id = (empty($pos_facility_id)==true) ? '0' : $pos_facility_id;
			$facilityPracCode = $rs['facilityPracCode'];
			$facilityNameArr[$pos_facility_id] = $facilityPracCode;	
			$first_posted_date= $rs["first_posted_date"];
			$oprId=$rs['operator_id'];
			$deptId= $arrDeptOfCptCodes[$rs["proc_code_id"]];
			$submitted= $rs["submitted"];
			$re_submitted=$rs["re_submitted"];
			$re_submitted_date=$rs["re_submitted_date"];
			
			//IF NOT DELETED OR DELETED AFTER FIRST POSTED DATE ONLY THEN FETCH THE RECORD
			if($rs['del_status']=='0' 
			|| ($rs['del_status']=='1' && $first_posted_date=='0000-00-00' && $rs['trans_del_date'] > $rs['dos'])
			|| ($rs['del_status']=='1' && $first_posted_date!='0000-00-00' && $rs['trans_del_date'] >= $first_posted_date)){
	
				if(!($submitted=='true' && $first_posted_date>=$Start_date && $first_posted_date<=$chk_src_date && (sizeof($arrSelOprators)<=0 || $arrSelOprators[$oprId]))){
					$rs['totalAmt']='0';
				}
				
				if(empty($Physician) == true && empty($credit_physician) === false){
					$doctor_id=$secondaryProviderId;
				}								
			
				$firstGrpBy= $doctor_id;
				$secGrpBy = $pos_facility_id;
				$facGrpNo=0;
				if($viewBy=='facility'){
					$firstGrpBy= $pos_facility_id;
					$secGrpBy = $doctor_id;
					$facGrpNo=1;
				}
	
				$displayInResub  =false;
				$displayInSub =false;
				if($DateRangeFor!='date_of_service' && $re_submitted == 'true' && $submitted == 'true' && ($re_submitted_date>= $Start_date && $re_submitted_date <= $chk_src_date)){
					$displayInResub = true;
				}else if($DateRangeFor!='date_of_service' && $submitted == 'true'){
					$displayInSub = true;
				}			
			
				if($processReport == "Summary"){
					
					if(($re_submitted == 'true' && $submitted == 'true' && $DateRangeFor=='date_of_service')
					|| ($DateRangeFor!='date_of_service' && $displayInResub==true)){ 
						foreach($arrCheckNos[$charge_list_detail_id] as $check_number){
							$mainResCheckArr['re_posted_charges'][$check_number][$encounter_id][] = $rs;
						}						
					
					}else if($submitted == 'false' || $submitted == false){ //not-posted
						foreach($arrCheckNos[$charge_list_detail_id] as $check_number){
							$mainResCheckArr['not_posted_charges'][$check_number][$encounter_id][] = $rs;
						}						
		
					}else if(($submitted == 'true' && $re_submitted == 'false' && $DateRangeFor=='date_of_service')
					|| ($DateRangeFor!='date_of_service' && $submitted == 'true' && $re_submitted == 'false' && !in_array($encounter_id, $arrAllResubEncs)) 
					|| ($DateRangeFor!='date_of_service' && $submitted == 'true' && $re_submitted == 'true' && $displayInSub==true)){//re-submitted 
						foreach($arrCheckNos[$charge_list_detail_id] as $check_number){
							$mainResCheckArr['posted_charges'][$check_number][$encounter_id][] = $rs;
						}
					}
					
				}else{
					if(($re_submitted == 'true' && $submitted == 'true' && $DateRangeFor=='date_of_service')
					|| ($DateRangeFor!='date_of_service' && $displayInResub==true)){ 

						if($pay_location=='1' && sizeof($tempTransPayLoc[$charge_list_detail_id])>0 && $facGrpNo>0){
							foreach($tempTransPayLoc[$charge_list_detail_id] as $check_number => $checkData){
								foreach($checkData as $payloc){
									$mainResCheckArr['re_posted_charges'][$payloc][$check_number][$encounter_id][] = $rs;
								}
							}
						}else{
							foreach($arrCheckNos[$charge_list_detail_id] as $check_number){
								$mainResCheckArr['re_posted_charges'][$firstGrpBy][$check_number][$encounter_id][] = $rs;
							}
						}
					
					}else if($submitted == 'false' || $submitted == false){ //not-posted
						
						if($pay_location=='1' && sizeof($tempTransPayLoc[$charge_list_detail_id])>0 && $facGrpNo>0){
							foreach($tempTransPayLoc[$charge_list_detail_id] as $check_number => $checkData){
								foreach($checkData as $payloc){
									$mainResCheckArr['not_posted_charges'][$payloc][$check_number][$encounter_id][] = $rs;
								}
							}
						}else{						
							foreach($arrCheckNos[$charge_list_detail_id] as $check_number){
								$mainResCheckArr['not_posted_charges'][$firstGrpBy][$check_number][$encounter_id][] = $rs;
							}
						}
						
					}else if(($submitted == 'true' && $re_submitted == 'false' && $DateRangeFor=='date_of_service')
					|| ($DateRangeFor!='date_of_service' && $submitted == 'true' && $re_submitted == 'false' && !in_array($encounter_id, $arrAllResubEncs)) 
					|| ($DateRangeFor!='date_of_service' && $submitted == 'true' && $re_submitted == 'true' && $displayInSub==true)){

						if($pay_location=='1' && sizeof($tempTransPayLoc[$charge_list_detail_id])>0 && $facGrpNo>0){
							foreach($tempTransPayLoc[$charge_list_detail_id] as $check_number => $checkData){
								foreach($checkData as $payloc){
									$mainResCheckArr['posted_charges'][$payloc][$check_number][$encounter_id][] = $rs;
								}
							}
						}else{						
							foreach($arrCheckNos[$charge_list_detail_id] as $check_number){
								$mainResCheckArr['posted_charges'][$firstGrpBy][$check_number][$encounter_id][] = $rs;
							}
						}
					}
				}
			}
		}
	}
	
	//SORT BY FACILITY ID IF SEARCH IS DONE BY GROUP BY FACILITY
/*	if($viewBy=='facility' && $pay_location=='1'){
		if(sizeof($mainResCheckArr)>0){
			$tempArr=$mainResCheckArr;
			unset($mainResCheckArr);
			
			foreach($arrSchFacilites as $id =>$name){
				if($tempArr['re_posted_charges'][$id]){
					$mainResCheckArr['re_posted_charges'][$id]=$tempArr['posted_charges'][$id];
				}
				if($tempArr['not_posted_charges'][$id]){
					$mainResCheckArr['not_posted_charges'][$id]=$tempArr['not_posted_charges'][$id];
				}
				if($tempArr['posted_charges'][$id]){
					$mainResCheckArr['posted_charges'][$id]=$tempArr['posted_charges'][$id];
				}
			}
			unset($tempArr);
		}
	}*/
		
}

//NORMAL VIEW
$tempRecordData=array();
$encounterIdArr=array();
//TRANSACTIONS TABLE
$qry="Select trans.report_trans_id, trans.patient_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, 
trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_qry_type, trans.trans_method, trans.check_number, trans.parent_id,
trans.cc_type, trans.cc_number, trans.facility_id, 
DATE_FORMAT(trans.trans_dop, '$dateFormat') as 'trans_dop', trans.trans_del_operator_id  
FROM report_enc_trans trans 
WHERE trans.trans_type!='charges' AND (trans.trans_del_operator_id='0' || (trans.trans_del_operator_id>0 && (trans.trans_del_date<='$chk_src_date'))) 
$chAmtQryCriteria ";
if($reportType=='checkView'){
	$qry.=" AND LOWER(trans.trans_method) NOT IN('check','eft','money order')";	
}
if(empty($Start_date)==false && empty($End_date)==false){
	if($DateRangeFor=='transaction_date'){
		$qry.=" AND (trans_dot BETWEEN '$Start_date' and '$End_date')";
	}else{
		$qry.=" AND (trans_dop BETWEEN '$Start_date' and '$End_date')";	
	}
}
if(trim($insuranceName) != ''){
	$qry.= " AND trans.trans_ins_id in($operatorName)";
}
if(empty($operatorName)==false){
	$qry.= " AND trans.trans_operator_id in($operatorName)";
}
if(empty($strBatchIds)==false){
	$qry.=" AND trans.batch_id IN(".$strBatchIds.")";
}
if(empty($sc_name)==false && $pay_location=='1'){
	$qry.=" AND trans.facility_id IN(".$sc_name.")";	
}
$qry.=" ORDER BY trans.master_tbl_id, trans.report_trans_id, trans.trans_dot, trans.report_trans_id";
//$qry.=" ORDER BY trans.trans_dot, trans.trans_dot_time";
$rs=imw_query($qry);
while($res = imw_fetch_assoc($rs)){
	$report_trans_id=$res['report_trans_id'];
	$encounter_id= $res['encounter_id'];
	$chgDetId= $res['charge_list_detail_id'];
	$insCompId = $res['trans_ins_id'];
	$encounterIdArr[$res['encounter_id']] = $res['encounter_id'];
	$trans_type= strtolower($res['trans_type']);
	$trans_by= strtolower($res['trans_by']);			
	$res['trans_method']= strtolower($res['trans_method']);			
	$payloc=$res['facility_id'];
	$tempRecordData[$report_trans_id]=$res['trans_amount'];
	$tempRecordDetail[$report_trans_id]['pay_loc']=$payloc;
	$tempRecordDetail[$report_trans_id]['trans_amt']=$res['trans_amount'];
	
	//RECORD FIRST RECORD ID TO SOLVE PROBLEM EVEN IF RECORD IS MODIFIED MORE THAN TWO TIME.
	//THIS IS DONE SO THAT RECORD WILL DISPLAY ONLY LAST MODIFED PAYMENT METHOD IN RESULT.
	//BUT THERE ARE DIFFERENT OPERATORS MODIFIED SAME PAYMENT AND REPORT IS SEARCHED FOR PARTICULAR OPERATOR THEN THIS LOGIC MAY FAIL.
	$record_id=($res['parent_id']>0)? $res['parent_id']: $report_trans_id;
	if($res['parent_id']>0){
		if($tempRecording[$res['parent_id']]>0){
			$tempArrFirstRecordid[$report_trans_id]=$tempArrFirstRecordid[$res['parent_id']];
		}else{
			$tempArrFirstRecordid[$report_trans_id]=$res['parent_id'];
		}
		$tempRecording[$report_trans_id]=$report_trans_id;
	}
	$record_id=($tempArrFirstRecordid[$report_trans_id]>0)? $tempArrFirstRecordid[$report_trans_id]: $record_id;
	//--------------------------------------------	
	
	switch($trans_type){
		case 'paid':
		case 'copay-paid':
		case 'deposit':
		case 'interest payment':
		case 'negative payment':
		case 'copay-negative payment':
			$paidForProc=$res['trans_amount'];
			if($trans_type=='negative payment' || $trans_type=='copay-negative payment' || $res['trans_del_operator_id']>0)$paidForProc="-".$res['trans_amount'];
			if(($trans_type=='negative payment' || $trans_type=='copay-negative payment') && $res['trans_del_operator_id']>0)$paidForProc=$res['trans_amount'];

			//IF parent_id >0 THEN IT MEANS RECORD IS UPDATED. THEN REMOVE PREVIOUS FETCHED AMOUNT.
			$prevFetchedAmt=0;
			if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
				$prevFetchedAmt = ($trans_type=='negative payment' || $trans_type=='copay-negative payment') ? $tempRecordData[$res['parent_id']] : "-".$tempRecordData[$res['parent_id']];

				//IF PAY LOCATION CHANGED THEN REMOVE AMT FROM OLD PAY LOCATION
				$old_pay_loc=$tempRecordDetail[$res['parent_id']]['pay_loc'];
				$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
				if($old_pay_loc!=$payloc){
					if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
						if($trans_by == 'patient' || $trans_by == 'res. party'){
							$patPayDetArr[$old_pay_loc][$chgDetId]['patPaid']-=$old_trans_amt;
						}else{
							$patPayDetArr[$old_pay_loc][$chgDetId]['insPaid']-=$old_trans_amt;
						}
						$patPayDetArr[$old_pay_loc][$chgDetId]['paid'][$record_id]-=$old_trans_amt;
						$mainEncounterPayArr[$old_pay_loc][$chgDetId]-=$old_trans_amt;						
					}else{
						if($trans_by == 'patient' || $trans_by == 'res. party'){
							$patPayDetArr[$chgDetId]['patPaid']-=$old_trans_amt;
						}else{
							$patPayDetArr[$chgDetId]['insPaid']-=$old_trans_amt;
						}
						$patPayDetArr[$chgDetId]['paid'][$record_id]-=$old_trans_amt;
						$mainEncounterPayArr[$chgDetId]-=$old_trans_amt;
					}
					$prevFetchedAmt=0;
				}
			}
			
			if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
				if($trans_by == 'patient' || $trans_by == 'res. party'){
					$patPayDetArr[$payloc][$chgDetId]['patPaid']+= $paidForProc + $prevFetchedAmt;
				}else if($trans_by == 'insurance'){
					if(empty($insuranceName)===true || (empty($insuranceName)===false && $arrInsurance[$insCompId])){	
						$patPayDetArr[$payloc][$chgDetId]['insPaid']+= $paidForProc + $prevFetchedAmt;
						$tempArrInsPay[$encounter_id][$chgDetId][$insCompId]+= $paidForProc + $prevFetchedAmt;
					}else{
						$paidForProc=0;
					}						
				}

				$patPayDetArr[$payloc][$chgDetId]['paid'][$record_id]+= $paidForProc + $prevFetchedAmt;
				$patPayDetArr[$payloc][$chgDetId]['method'][$record_id]= $res['trans_method'];
				$patPayDetArr[$payloc][$chgDetId]['check_num'][$record_id]= trim($res['check_number']);
				$patPayDetArr[$payloc][$chgDetId]['cc_type'][$record_id]= $res['cc_type'];
				$patPayDetArr[$payloc][$chgDetId]['cc_number'][$record_id]= $res['cc_number'];
				$patPayDetArr[$payloc][$chgDetId]['paid_date'][$record_id] = $res['trans_dop'];
	
				$mainEncounterPayArr[$payloc][$chgDetId]+= $paidForProc + $prevFetchedAmt;
				$tempTransPayLoc[$chgDetId][$payloc]=$payloc;
			}else{
				if($trans_by == 'patient' || $trans_by == 'res. party'){
					$patPayDetArr[$chgDetId]['patPaid']+= $paidForProc + $prevFetchedAmt;
				}else if($trans_by == 'insurance'){
					if(empty($insuranceName)===true || (empty($insuranceName)===false && $arrInsurance[$insCompId])){	
						$patPayDetArr[$chgDetId]['insPaid']+= $paidForProc + $prevFetchedAmt;
						$tempArrInsPay[$encounter_id][$chgDetId][$insCompId]+= $paidForProc + $prevFetchedAmt;
					}else{
						$paidForProc=0;
					}						
				}
				
				$patPayDetArr[$chgDetId]['paid'][$record_id]+= $paidForProc + $prevFetchedAmt;
				$patPayDetArr[$chgDetId]['method'][$record_id]= $res['trans_method'];
				$patPayDetArr[$chgDetId]['check_num'][$record_id]= trim($res['check_number']);
				$patPayDetArr[$chgDetId]['cc_type'][$record_id]= $res['cc_type'];
				$patPayDetArr[$chgDetId]['cc_number'][$record_id]= $res['cc_number'];
				$patPayDetArr[$chgDetId]['paid_date'][$record_id] = $res['trans_dop'];
	
				$mainEncounterPayArr[$chgDetId]+= $paidForProc + $prevFetchedAmt;
			}
		break;

		case 'credit':
		case 'debit':
			$crddbtamt=$res['trans_amount'];
			if($trans_type=='credit'){ 
				$crddbtamt= ($res['trans_del_operator_id']>0) ? "-".$res['trans_amount'] : $res['trans_amount'];							
			}else{  //debit
				$crddbtamt= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];				
			}

			$prevFetchedAmt=0;
			if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
				$prevFetchedAmt = ($trans_type=='credit') ? "-".$tempRecordData[$res['parent_id']] : $tempRecordData[$res['parent_id']];

				//IF PAY LOCATION CHANGED THEN REMOVE AMT FROM OLD PAY LOCATION
				$old_pay_loc=$tempRecordDetail[$res['parent_id']]['pay_loc'];
				$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
				if($old_pay_loc!=$payloc){
					if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
						if($trans_by=='insurance'){
							$pay_crd_deb_arr[$old_pay_loc][$chgDetId]['Insurance']-=$old_trans_amt;
						}else{
							$pay_crd_deb_arr[$old_pay_loc][$chgDetId]['Patient']-=$old_trans_amt;
						}
						$mainEncounterPayArr[$old_pay_loc][$chgDetId]-=$old_trans_amt;
						$patPayDetArr[$old_pay_loc][$chgDetId]['paid'][$record_id]-=$old_trans_amt;
					}else{
						if($trans_by=='insurance'){
							$patPayDetArr[$chgDetId]['insPaid']-=$old_trans_amt;
						}else{
							$patPayDetArr[$chgDetId]['patPaid']-=$old_trans_amt;
						}
						$mainEncounterPayArr[$chgDetId]-=$old_trans_amt;
						$patPayDetArr[$chgDetId]['paid'][$record_id]-=$old_trans_amt;
					}
					$prevFetchedAmt=0;
				}				
			}
			
			if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
				if($trans_by=='insurance'){
					$pay_crd_deb_arr[$payloc][$chgDetId]['Insurance']+= $crddbtamt+$prevFetchedAmt;
					if($res['trans_del_operator_id']<=0)$patPayDetArr[$payloc][$chgDetId]['paid_date'][] = $res['trans_dop'];					
				}else{
					$pay_crd_deb_arr[$payloc][$chgDetId]['Patient']+= $crddbtamt+$prevFetchedAmt;				
					if($res['trans_del_operator_id']<=0)$patPayDetArr[$payloc][$chgDetId]['paid_date'][] = $res['trans_dop'];					
				}
				$tempTransPayLoc[$chgDetId][$payloc]=$payloc;
				$mainEncounterPayArr[$payloc][$chgDetId]+= $crddbtamt + $prevFetchedAmt;
				
				$patPayDetArr[$payloc][$chgDetId]['paid'][$record_id]+= $crddbtamt + $prevFetchedAmt;
				$patPayDetArr[$payloc][$chgDetId]['method'][$record_id]= $res['trans_method'];
				$patPayDetArr[$payloc][$chgDetId]['check_num'][$record_id]= trim($res['check_number']);
				$patPayDetArr[$payloc][$chgDetId]['cc_type'][$record_id]= $res['cc_type'];
				$patPayDetArr[$payloc][$chgDetId]['cc_number'][$record_id]= $res['cc_number'];
				$patPayDetArr[$payloc][$chgDetId]['paid_date'][$record_id] = $res['trans_dop'];				

			}else{
				if($trans_by=='insurance'){
					$patPayDetArr[$chgDetId]['insPaid']+= $crddbtamt+$prevFetchedAmt;
					if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['paid_date'][] = $res['trans_dop'];					
				}else{
					$patPayDetArr[$chgDetId]['patPaid']+= $crddbtamt+$prevFetchedAmt;				
					if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['paid_date'][] = $res['trans_dop'];					
				}
				$mainEncounterPayArr[$chgDetId]+= $crddbtamt + $prevFetchedAmt;
				
				$patPayDetArr[$chgDetId]['paid'][$record_id]+= $crddbtamt + $prevFetchedAmt;
				$patPayDetArr[$chgDetId]['method'][$record_id]= $res['trans_method'];
				$patPayDetArr[$chgDetId]['check_num'][$record_id]= trim($res['check_number']);
				$patPayDetArr[$chgDetId]['cc_type'][$record_id]= $res['cc_type'];
				$patPayDetArr[$chgDetId]['cc_number'][$record_id]= $res['cc_number'];
				$patPayDetArr[$chgDetId]['paid_date'][$record_id] = $res['trans_dop'];				
			}
		break;

		case 'default_writeoff':
			if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
				$normalWriteOffAmt[$payloc][$chgDetId]= $res['trans_amount'];
				if($res['trans_del_operator_id']<=0)$patPayDetArr[$payloc][$chgDetId]['paid_date'][] = $res['trans_dop'];
				$tempTransPayLoc[$chgDetId][$payloc]=$payloc;
			}else{
				$normalWriteOffAmt[$chgDetId]= $res['trans_amount'];
				if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['paid_date'][] = $res['trans_dop'];
			}
		break;
		case 'write off':
		case 'discount':
			if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];

			$prevFetchedAmt=0;
			if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
				$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];

				//IF PAY LOCATION CHANGED THEN REMOVE AMT FROM OLD PAY LOCATION
				$old_pay_loc=$tempRecordDetail[$res['parent_id']]['pay_loc'];
				$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
				if($old_pay_loc!=$payloc){
					if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
						$writte_off_arr[$old_pay_loc][$chgDetId]-=$old_trans_amt;
					}else{
						$writte_off_arr[$chgDetId]-=$old_trans_amt;
					}
					$prevFetchedAmt=0;
				}					
			}
			
			if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
				$writte_off_arr[$payloc][$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
				if($res['trans_del_operator_id']<=0)$patPayDetArr[$payloc][$chgDetId]['paid_date'][] = $res['trans_dop'];
				$tempTransPayLoc[$chgDetId][$payloc]=$payloc;
			}else{
				$writte_off_arr[$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
				if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['paid_date'][] = $res['trans_dop'];
			}
		break;
		case 'over adjustment':
			if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];

			$prevFetchedAmt=0;
			if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
				$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];

				//IF PAY LOCATION CHANGED THEN REMOVE AMT FROM OLD PAY LOCATION
				$old_pay_loc=$tempRecordDetail[$res['parent_id']]['pay_loc'];
				$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
				if($old_pay_loc!=$payloc){
					if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
						$arrAdjustmentAmt[$old_pay_loc][$chgDetId]-=$old_trans_amt;
					}else{
						$arrAdjustmentAmt[$chgDetId]-=$old_trans_amt;
					}
					$prevFetchedAmt=0;
				}					
			}
					
			if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
				$arrAdjustmentAmt[$payloc][$chgDetId]+= $res['trans_amount']+ $prevFetchedAmt;
				if($res['trans_del_operator_id']<=0)$patPayDetArr[$payloc][$chgDetId]['paid_date'][] = $res['trans_dop'];
				$tempTransPayLoc[$chgDetId][$payloc]=$payloc;
			}else{
				$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount']+ $prevFetchedAmt;
				if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['paid_date'][] = $res['trans_dop'];
			}
		break;
		case 'adjustment':
			$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

			$prevFetchedAmt=0;
			if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
				$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];

				//IF PAY LOCATION CHANGED THEN REMOVE AMT FROM OLD PAY LOCATION
				$old_pay_loc=$tempRecordDetail[$res['parent_id']]['pay_loc'];
				$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
				if($old_pay_loc!=$payloc){
					if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
						$arrAdjustmentAmt[$old_pay_loc][$chgDetId]-=$old_trans_amt;
					}else{
						$arrAdjustmentAmt[$chgDetId]-=$old_trans_amt;
					}
					$prevFetchedAmt=0;
				}				
			}
			
			if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
				$arrAdjustmentAmt[$payloc][$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
				if($res['trans_del_operator_id']<=0)$patPayDetArr[$payloc][$chgDetId]['paid_date'][] = $res['trans_dop'];
				$tempTransPayLoc[$chgDetId][$payloc]=$payloc;
			}else{
				$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
				if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['paid_date'][] = $res['trans_dop'];
			}
		break;
		case 'returned check':
			$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

			$prevFetchedAmt=0;
			if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
				$prevFetchedAmt = $tempRecordData[$res['parent_id']];

				//IF PAY LOCATION CHANGED THEN REMOVE AMT FROM OLD PAY LOCATION
				$old_pay_loc=$tempRecordDetail[$res['parent_id']]['pay_loc'];
				$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
				if($old_pay_loc!=$payloc){
					if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
						$arrAdjustmentAmt[$old_pay_loc][$chgDetId]-=$old_trans_amt;
					}else{
						$arrAdjustmentAmt[$chgDetId]-=$old_trans_amt;
					}
					$prevFetchedAmt=0;
				}				
			}
			
			if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
				$arrAdjustmentAmt[$payloc][$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
				if($res['trans_del_operator_id']<=0)$patPayDetArr[$payloc][$chgDetId]['paid_date'][] = $res['trans_dop'];
				$tempTransPayLoc[$chgDetId][$payloc]=$payloc;
			}else{
				$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
				if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['paid_date'][] = $res['trans_dop'];
			}
		break;
		case 'refund':
			$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];		

			$prevFetchedAmt=0;
			if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
				$prevFetchedAmt = $tempRecordData[$res['parent_id']];

				//IF PAY LOCATION CHANGED THEN REMOVE AMT FROM OLD PAY LOCATION
				$old_pay_loc=$tempRecordDetail[$res['parent_id']]['pay_loc'];
				$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
				if($old_pay_loc!=$payloc){
					if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
						$arrAdjustmentAmt[$old_pay_loc][$chgDetId]-=$old_trans_amt;
					}else{
						$arrAdjustmentAmt[$chgDetId]-=$old_trans_amt;
					}
					$prevFetchedAmt=0;
				}					
			}
			
			if($pay_location=='1' && $viewBy=='facility' && $processReport == "Detail"){
				$arrAdjustmentAmt[$payloc][$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
				if($res['trans_del_operator_id']<=0)$patPayDetArr[$payloc][$chgDetId]['paid_date'][] = $res['trans_dop'];
				$tempTransPayLoc[$chgDetId][$payloc]=$payloc;
			}else{
				$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
				if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['paid_date'][] = $res['trans_dop'];
			}
		break;
	}
}

//GET FIRST POSTED ENCOUTNERS FETCHED SEPARATELY DUE TO OPERATOR SELECTION
$firstPostedEnc=array();
if(empty($strChSearchEnc)==true){
	$qry="Select main.encounter_id FROM report_enc_detail main
	JOIN patient_data on patient_data.id = main.patient_id 
	WHERE (main.first_posted_date BETWEEN '$Start_date' AND '$End_date') 
	AND (main.del_status='0' OR (main.del_status='1' AND DATE_FORMAT(main.trans_del_date, '%Y-%m-%d')>'$End_date')) ".$qryBigPart;
	if(empty($operatorName)==false){
		$qry.= " AND main.operator_id in($operatorName)";
	}
	if(empty($sc_name) == false){
		if($pay_location=='1' || $billing_location=='1'){
			$qry.= " and main.billing_facility_id IN ($sc_name)";
		}else{
			$qry.= " and main.facility_id IN ($sc_name)";	
		}
	}
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$firstPostedEnc[$res['encounter_id']]=$res['encounter_id'];
	}unset($rs);
	//------------------------------
}

//MERGE WITH FIRST POSTED ENCOUNTERS
$encounterIdArr=array_merge($encounterIdArr, $firstPostedEnc);
$encounterIdArr = array_unique($encounterIdArr);
$encounterIdStr = join(',',$encounterIdArr);


//MAIN QUERY
if(sizeof($encounterIdArr)>0){
	$qry="Select main.charge_list_id, main.encounter_id, main.charge_list_detail_id, main.pri_ins_id, main.sec_ins_id, main.tri_ins_id,main.operator_id,
	(main.charges * main.units) as totalAmt, main.units, date_format(main.date_of_service,'".$dateFormat."') as date_of_service, main.date_of_service as 'dos',
	date_format(main.entered_date,'".$dateFormat."') as entered_date,
	DATE_FORMAT(main.trans_del_date, '%Y-%m-%d') as trans_del_date,
	main.primary_provider_id_for_reports  as 'primaryProviderId', main.sec_prov_id,	
	main.proc_code_id, main.dx_id1, main.dx_id2, main.dx_id3, main.dx_id4, main.dx_id5, main.dx_id6, main.dx_id7, main.dx_id8, main.dx_id9,
	main.dx_id10, main.dx_id11, main.dx_id12, main.mod_id1, main.mod_id2, main.mod_id3, main.mod_id4, main.billing_facility_id,
	main.proc_balance,	main.pri_due, main.sec_due, main.tri_due, main.pat_due, main.del_status, main.first_posted_date, 
	date_format(main.first_posted_date,'".$dateFormat."') as 'first_posted_date_formatted', main.over_payment,
	main.submitted, main.over_payment, main.re_submitted, main.re_submitted_date, 
	users.lname as physicianLname,users.fname as physicianFname, users.mname as physicianMname,
	pos_tbl.pos_prac_code as facilityPracCode,
	pos_facilityies_tbl.pos_facility_id, patient_data.id as patient_id,
	patient_data.lname,	patient_data.fname, patient_data.mname, patient_data.default_facility,
	main.write_off, cpt_fee_tbl.cpt_prac_code, cpt_fee_tbl.cpt4_code, cpt_fee_tbl.departmentId,
	cpt_fee_tbl.cpt_desc
	FROM report_enc_detail main 
	JOIN patient_data on patient_data.id = main.patient_id 
	JOIN users on users.id = main.primary_provider_id_for_reports 
	LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = main.facility_id
	LEFT JOIN pos_tbl ON pos_tbl.pos_id = pos_facilityies_tbl.pos_id 
	JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = main.proc_code_id
	LEFT JOIN department_tbl on department_tbl.departmentId = cpt_fee_tbl.departmentId 		
	WHERE ((main.del_status='0') OR (main.del_status='1' AND DATE_FORMAT(main.trans_del_date, '%Y-%m-%d')>'$chk_src_date'))";
	if(empty($departmentIds) == false){
		$qry.= " and department_tbl.departmentId IN ($departmentIds)";	
	}
	
	if($pay_location!='1'){	 // No need to pass facility here if pay location selected.
		if(empty($sc_name) == false){
			if($billing_location=='1'){
				$qry.= " and main.billing_facility_id IN ($sc_name)";
			}else{
				$qry.= " and main.facility_id IN ($sc_name)";	
			}
		}
	}
	$qry.= $qryBigPart;
	if($pureSelfPay==1)$qry.= " and patient_charge_list_details.proc_selfpay=1";	
	
	if(sizeof($encounterIdArr)>0){
		$qry.= " AND main.encounter_id in ($encounterIdStr)";		
	}
	$qry .= " order by users.lname,users.fname, pos_facilityies_tbl.facilityPracCode, 
	patient_data.lname,patient_data.fname,main.date_of_service,main.encounter_id";

	
	$res=imw_query($qry);
	//--- PRIMARY PROVIDER ID ARRAY ----
	$arr_selected_phy = array();
	if(empty($Physician) === false){
		$arr_selected_phy = preg_split("/,/", $Physician);
	}
	
	$main_encounter_id_arr = array();
	$facilityNameArr = array();
	$physician_initial_arr = array();
	$arrPatNoFacility=array();

	while($rs = imw_fetch_assoc($res)){
		$encounter_id = $rs['encounter_id'];
		
		$charge_list_detail_id = $rs['charge_list_detail_id'];
		$doctor_id = $rs['primaryProviderId'];
		$secondaryProviderId = $rs['sec_prov_id'];
	
		//---- GET POS FACILITY NAME ---
		$pos_facility_id = ($pay_location=='1' || $billing_location=='1') ? $rs['billing_facility_id'] : $rs['pos_facility_id'];	
		$pos_facility_id = (empty($pos_facility_id)==true) ? '0' : $pos_facility_id;
		$facilityPracCode = $rs['facilityPracCode'];
		$facilityNameArr[$pos_facility_id] = $facilityPracCode;	
		$first_posted_date= $rs["first_posted_date"];
		$oprId=$rs['operator_id'];
		$deptId= $arrDeptOfCptCodes[$rs["proc_code_id"]];
		$submitted= $rs["submitted"];
		$re_submitted=$rs["re_submitted"];
		$re_submitted_date=$rs["re_submitted_date"];
	
		
		//IF NOT DELETED OR DELETED AFTER FIRST POSTED DATE ONLY THEN FETCH THE RECORD
		if($rs['del_status']=='0' 
		|| ($rs['del_status']=='1' && $first_posted_date=='0000-00-00' && $rs['trans_del_date'] > $rs['dos'])
		|| ($rs['del_status']=='1' && $first_posted_date!='0000-00-00' && $rs['trans_del_date'] > $first_posted_date)){

			if(!($submitted=='true' && $first_posted_date>=$Start_date && $first_posted_date<=$chk_src_date && (sizeof($arrSelOprators)<=0 || $arrSelOprators[$oprId]))){
				$rs['totalAmt']='0';
			}

			if(empty($Physician) == true && empty($credit_physician) === false){
				$doctor_id=$secondaryProviderId;
			}							
		
			$firstGrpBy= $doctor_id;
			$secGrpBy = $pos_facility_id;
			$facGrpNo=0;
			if($viewBy=='facility'){
				$firstGrpBy= $pos_facility_id;
				$secGrpBy = $doctor_id;
				$facGrpNo=1;
			}

			$displayInResub  =false;
			$displayInSub =false;
			if($DateRangeFor!='date_of_service' && $re_submitted == 'true' && $submitted == 'true' && ($re_submitted_date>= $Start_date && $re_submitted_date <= $chk_src_date)){
				$displayInResub = true;
			}else if($DateRangeFor!='date_of_service' && $submitted == 'true'){
				$displayInSub = true;
			}			
		
			if($processReport == "Summary"){
				
				if(($re_submitted == 'true' && $submitted == 'true' && $DateRangeFor=='date_of_service')
				|| ($DateRangeFor!='date_of_service' && $displayInResub==true)){ 
				
					$mainResArr['re_posted_charges'][$firstGrpBy][$encounter_id][] = $rs;
				
				}else if($submitted == 'false' || $submitted == false){ //not-posted
					$mainResArr['not_posted_charges'][$firstGrpBy][$encounter_id][] = $rs;
				
				}else if(($submitted == 'true' && $re_submitted == 'false' && $DateRangeFor=='date_of_service')
				|| ($DateRangeFor!='date_of_service' && $submitted == 'true' && $re_submitted == 'false' && !in_array($encounter_id, $arrAllResubEncs)) 
				|| ($DateRangeFor!='date_of_service' && $submitted == 'true' && $re_submitted == 'true' && $displayInSub==true)){//re-submitted 
				
					$mainResArr['posted_charges'][$firstGrpBy][$encounter_id][] = $rs;
				}
				
			}else{
				if(($re_submitted == 'true' && $submitted == 'true' && $DateRangeFor=='date_of_service')
				|| ($DateRangeFor!='date_of_service' && $displayInResub==true)){ 
					
					if($pay_location=='1' && sizeof($tempTransPayLoc[$charge_list_detail_id])>0 && $facGrpNo>0){
						foreach($tempTransPayLoc[$charge_list_detail_id] as $payloc){
							$mainResArr['re_posted_charges'][$payloc][$encounter_id][] = $rs;
						}
					}else{
						$mainResArr['re_posted_charges'][$firstGrpBy][$encounter_id][] = $rs;
					}
				
				}else if($submitted == 'false' || $submitted == false){ //not-posted

					if($pay_location=='1' && sizeof($tempTransPayLoc[$charge_list_detail_id])>0 && $facGrpNo>0){
						foreach($tempTransPayLoc[$charge_list_detail_id] as $payloc){
							$mainResArr['not_posted_charges'][$payloc][$encounter_id][] = $rs;
						}
					}else{
						$mainResArr['not_posted_charges'][$firstGrpBy][$encounter_id][] = $rs;
					}

				}else if(($submitted == 'true' && $re_submitted == 'false' && $DateRangeFor=='date_of_service')
				|| ($DateRangeFor!='date_of_service' && $submitted == 'true' && $re_submitted == 'false' && !in_array($encounter_id, $arrAllResubEncs)) 
				|| ($DateRangeFor!='date_of_service' && $submitted == 'true' && $re_submitted == 'true' && $displayInSub==true)){
				
					if($pay_location=='1' && sizeof($tempTransPayLoc[$charge_list_detail_id])>0 && $facGrpNo>0){
						foreach($tempTransPayLoc[$charge_list_detail_id] as $payloc){
							$mainResArr['posted_charges'][$payloc][$encounter_id][] = $rs;
						}
					}else{
						$mainResArr['posted_charges'][$firstGrpBy][$encounter_id][] = $rs;
					}
				}
			}
		}
	}

	//SORT BY FACILITY ID IF SEARCH IS DONE BY GROUP BY FACILITY
	if($viewBy=='facility' && ($pay_location=='1' || $billing_location=='1')){
		if(sizeof($mainResArr)>0){
			$tempArr=$mainResArr;
			unset($mainResArr);
			
			//ADDING IDS IN $tempSchFac VARIABLE WHICH ARE RECEIVED FROM MAIN ARRAY BUT NOT HAVE IN $tempSchFac SO THAT RECORDS SHOULD MISSED.
			$tempSchFac=$arrSchFacilites;
			$arrFetchedFacIds=array_unique(array_merge(array_keys($tempArr['posted_charges']), array_keys($tempArr['not_posted_charges']), array_keys($tempArr['re_posted_charges'])));
			foreach($arrFetchedFacIds as $id){
				if(!$tempSchFac[$id]){
					$tempSchFac[$id]=($pay_location=='1')? 'No Pay Location': 'No Billing Location';
				}
			}

			foreach($tempSchFac as $id =>$name){
				if($tempArr['re_posted_charges'][$id]){
					$mainResArr['re_posted_charges'][$id]=$tempArr['re_posted_charges'][$id];
				}
				if($tempArr['not_posted_charges'][$id]){
					$mainResArr['not_posted_charges'][$id]=$tempArr['not_posted_charges'][$id];
				}
				if($tempArr['posted_charges'][$id]){
					$mainResArr['posted_charges'][$id]=$tempArr['posted_charges'][$id];
				}
			}
			unset($tempArr);
		}
	}	
}
unset($tempRecording);
unset($tempArrFirstRecordid);
?>