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
$arrDateRange= $CLSCommonFunction->changeDateSelection();
if($dayReport=='Daily'){
	$Start_date = $End_date= date($phpDateFormat);
}else if($dayReport=='Weekly'){
	$Start_date = $arrDateRange['WEEK_DATE'];
	$End_date= date($phpDateFormat);
}else if($dayReport=='Monthly'){
	$Start_date = $arrDateRange['MONTH_DATE'];
	$End_date= date($phpDateFormat);
}else if($dayReport=='Quarterly'){
	$Start_date = $arrDateRange['QUARTER_DATE_START'];
	$End_date = $arrDateRange['QUARTER_DATE_END'];
}

$Process = strtolower($summary_detail);

$globalDateFormat = phpDateFormat();
$page_data = '';	$printFile= true;
if(empty($Submit) === false){
	$printFile = false;
	$curDate = date($globalDateFormat.' H:i A');
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = $op_name_arr[1][0];
	$op_name .= $op_name_arr[0][0];

	$st_date = getDateFormatDB($Start_date);
	$en_date = getDateFormatDB($End_date);

	$primaryProviderId = implode(',',$phyId);
	$facility_name_str = implode(',',$facility_name);
	$opr_name_str = implode(',',$operator_id);
	$grp_id = implode(',',$groups);
	
	// GET ALL USERS
	$rs=imw_query("Select id,fname,lname FROM users");
	while($res=imw_fetch_array($rs)){
		$usrNameArr=array();
		$usrNameArr["LAST_NAME"] = $res['lname'];
		$usrNameArr["FIRST_NAME"] = $res['fname'];
		$usrName = changeNameFormat($usrNameArr);
		$arrAllUsers[$res['id']]=$usrName;
	}
	
	//getting report generator name
	$report_generator_name = NULL;
	if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
		$arr_report_generator_name = explode(" ", $_SESSION["authProviderName"]);
		$report_generator_name = substr($arr_report_generator_name[1], 0, 1).substr($arr_report_generator_name[0], 0, 1);
		$report_generator_name = strtoupper($report_generator_name);
	}
	if(empty($facility_name_str)==false){ 
		$schfacPart = " WHERE id IN($facility_name_str)"; 
		$posfacPart = " WHERE pos_facility_id IN($facility_name_str)"; 
	}	
	// GET SELECTED SCHEDULER FACILITIES
	$sch_fac_id_str='';
	$fac_query = imw_query("Select id,name,fac_prac_code from facility $schfacPart");												
	$fac_query_res = array();
	while($row = imw_fetch_assoc($fac_query)){
		$fac_query_res[] = $row;
	}
	$sch_fac_id_arr = array();
	$arr_sch_facilities=array();
	for($i=0;$i<count($fac_query_res);$i++){
		$fac_id = $fac_query_res[$i]['id'];
		$pos_fac_id = $fac_query_res[$i]['fac_prac_code'];
		$sch_fac_id_arr[$fac_id] = $fac_id;
		$sch_pos_fac_arr[$fac_id] = $pos_fac_id;
		$sch_fac_arr[$pos_fac_id] = $fac_id;
		$arr_sch_facilities[$fac_id]=$fac_query_res[$i]['name'];
	}
	$sch_fac_id_str = implode(',',$sch_fac_id_arr);
	// GET ALL FACILITIES
	$allFacArr=array();
	$qry = "select pos_facilityies_tbl.facilityPracCode as name,
				pos_facilityies_tbl.pos_facility_id as id,
				pos_tbl.pos_prac_code
				from pos_facilityies_tbl 
				LEFT JOIN pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id 
				$posfacPart";
	$qryRs = imw_query($qry);
	while($qryRes = imw_fetch_array($qryRs)){
		$allFacArr[$qryRes['id']] = $qryRes['name'].' - '.$qryRes['pos_prac_code'];
	}
	
	// CI/CO FIELDS
	$rs=imw_query("Select * FROM check_in_out_fields");
	while($res=imw_fetch_array($rs)){
		$cioFields[$res['id']] = $res['item_name'];	
	}
	// CI/CO AMOUNTS
	$schFacId=0;
	$arr_sc_name=explode(',', $facility_name_str);
	$arrSchFacId=array();
	for($i=0; $i<sizeof($arr_sc_name); $i++){
		if($arr_sc_name[$i]>0){
			$id=$sch_fac_arr[$arr_sc_name[$i]];
			if($id>0 && $id!=''){
				$arrSchFacId[$id] = $id;
			}
		}
	}
	$arrSchFacId=array_unique($arrSchFacId);
	$schFacId = implode(',', $arrSchFacId);
	$arrAppliedAmt = array();
	$qry="SELECT sa.sa_patient_id, 
		sa.sa_facility_id, 
		sa.sa_doctor_id, 
		cioPay.payment_id, 
		cioPay.sch_id, 
		DATE_FORMAT(cioPay.created_on, '".get_sql_date_format()."') as created_on, 
		cioPay.created_by,
		cioPayDet.id as 'cioPayDetId', 
		cioPayDet.item_id, 
		cioPayDet.item_payment, 
		cioPayDet.payment_type,
		cioPayDet.id AS check_in_out_detail_id,
		cioPayDet.status,
		cioPayDet.delete_operator_id,
		DATE_FORMAT(cioPayDet.delete_date,'".get_sql_date_format()."') as delete_date,
		pd.fname, 
		pd.mname, 
		pd.lname    
		FROM schedule_appointments sa 
		JOIN check_in_out_payment cioPay ON cioPay.sch_id  = sa.id 
		JOIN check_in_out_payment_details cioPayDet ON cioPayDet.payment_id = cioPay.payment_id 
		LEFT JOIN users ON users.id = sa.sa_doctor_id  
		LEFT JOIN patient_data pd ON pd.id = sa.sa_patient_id 
		LEFT JOIN facility fac on fac.id=sa.sa_facility_id 
		WHERE cioPay.total_payment>0 
		AND (cioPay.created_on BETWEEN '".$st_date."' AND '".$en_date."') AND cioPayDet.status='0'";
	if(empty($facility_name_str) === false){
		$qry .= " and sa.sa_facility_id in($facility_name_str)";
	}
	if(empty($primaryProviderId) === false){
		$qry .= " and sa.sa_doctor_id in($primaryProviderId)";
	}
	if(empty($opr_name_str) === false){
		$qry .= " and cioPay.created_by in($opr_name_str)";
	}
	if(empty($grp_id) === false){
		$qry .= " and fac.default_group in($grp_id)";
	}
	$qry.= " ORDER BY users.lname, users.fname, pd.lname, pd.fname";
	//echo $qry."<br>";
	
	$rs=imw_query($qry);
	$arrPayments = array();
	$arrDelPayments = array();
	while($res=imw_fetch_array($rs)){
	
		//query to get refund detail for current ci/co payments if any
		$refundAmt=0;
		$qryRef=imw_query("Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND ci_co_id = '".$res['cioPayDetId']."' AND (entered_date BETWEEN '".$st_date."' AND '".$en_date."')")or die(imw_error().'_471');
		while($rsRef=imw_fetch_array($qryRef)){
			$refundAmt+=$rsRef['ref_amt'];
		}
		//$qryRef.close;
		$created_by = $res['created_by'];
		$del_by = $res['delete_operator_id'];
		$pat_id = $res['sa_patient_id'];
		$payment_id = $res['payment_id'];
		$pay_det_id = $res['cioPayDetId'];
		$created_on = $res['created_on'];
		$doc_id = $res['sa_doctor_id'];
		
		$patNameArr=array();
		$patNameArr["LAST_NAME"] = $res['lname'];
		$patNameArr["FIRST_NAME"] = $res['fname'];
		$patNameArr["MIDDLE_NAME"] = $res['mname'];
		$patName = changeNameFormat($patNameArr);
		
		if($res['status'] == 0){
			
			$tempData[$pay_det_id]['payment_type'] = $res['payment_type'];
			//FOR DETAIL
			$tempCIOArr[$pay_det_id]['pat_id']= $res['sa_patient_id'];
			$tempCIOArr[$pay_det_id]['pat_name']= $patName;
			$tempCIOArr[$pay_det_id]['payment']+= $res['item_payment'];
			$tempCIOArr[$pay_det_id]['payment_date']= $res['created_on'];
			$tempCIOArr[$pay_det_id]['created_by']= $created_by;
			$tempCIOArr[$pay_det_id]['doc_name']= $arrAllUsers[$doc_id];
			$tempCIOArr[$pay_det_id]['cio_field_id']= $res['item_id'];
			$tempCIOArr[$pay_det_id]['refund']+= $refundAmt;
			$tempCIOIdArr[$payment_id] = $payment_id;

		}else{
			if($Process == "summary"){
				$arrPayments[$created_by]['del_payment'] += $res['item_payment']-$refundAmt;
				$arrPayments[$created_by]['del_payment_ref'] += $refundAmt;
			}
			
			//----BEGIN MAKE ARRAY FOR DELETED PAYMENTS--------------
			$arrDelPayDetail = array();
			$arrDelPayDetail['del_date'] = $res['delete_date'];
			$arrDelPayDetail['cio_field_id'] = $res['item_id'];
			$arrDelPayDetail['cio_payment'] = $res['item_payment']-$refundAmt;
			$arrDelPayDetail['cio_payment_ref'] = $refundAmt;
			$arrDelPayDetail['payment_id'] = $res['payment_id'];
			$arrDelPayDetail['check_in_out_detail_id'] = $res['check_in_out_detail_id'];
			$arrDelPayments[$del_by]['patient'][$pat_id]['pat_detail'] = $patName;
			$arrDelPayments[$del_by]['patient'][$pat_id]['payments'][$created_on]['cio_payment'][] = $arrDelPayDetail;
			$arrDelPayments[$del_by]['cio_payment_id'][] = $res['check_in_out_detail_id'];
			//----END MAKE ARRAY FOR DELETED PAYMENTS--------------
		}
	}

	$splitted_encounters=array();
	if(sizeof($tempCIOArr)>0){
		$arrAllPayDetIds= array_keys($tempCIOArr);
		$splitted_encounters = array_chunk($arrAllPayDetIds,4000);
		$tempCIOPaid=array();
		foreach($splitted_encounters as $arrSplittedPayDetIds){
			$str_splitted_encs 	 = implode(',',$arrSplittedPayDetIds);
			$arr_acc_payment_id=array();
			$temp_acc_payment_id=array();
			$arrCioAccId = array();

			$qry="SELECT cioPost.check_in_out_payment_detail_id, cioPost.patient_id, 
			 cioPost.manually_payment, 
			 cioPost.acc_payment_id, 
			 cioPost.manually_date,
			 cioPost.check_in_out_payment_id 
			 FROM check_in_out_payment_post cioPost 
			 WHERE cioPost.check_in_out_payment_detail_id IN(".$str_splitted_encs.") 
			 AND cioPost.status='0'";
			$rs=imw_query($qry);
			while($res=imw_fetch_array($rs)){
				$pay_det_id = $res['check_in_out_payment_detail_id'];
				$check_in_out_payment_detail_id = $res['check_in_out_payment_detail_id'];
				$acc_payment_id = $res['acc_payment_id'];

				if($res['manually_payment']>0){
					$tempCIOArr[$pay_det_id]['applied']+= $res['manually_payment'];
				}
				
				if($res['acc_payment_id']>0){ 
					$arr_acc_payment_id[$acc_payment_id]=$acc_payment_id;
					$temp_acc_payment_id[$acc_payment_id] = $pay_det_id;
					$arrCioAccId[$acc_payment_id] = $check_in_out_payment_detail_id;
				}
			}

			$arrPaymentsApplied = array();
			if(sizeof($arr_acc_payment_id)>0){
				$str_acc_payment_id = implode(',', $arr_acc_payment_id);
				
				$qry="SELECT patPay.payment_id, 
					  patPayDet.paidForProc 
					  FROM patient_chargesheet_payment_info patPay 
					  LEFT JOIN patient_charges_detail_payment_info patPayDet ON patPayDet.payment_id = patPay.payment_id 
					  WHERE patPay.payment_id IN(".$str_acc_payment_id.") 
					  AND patPayDet.deletePayment='0' 
					  AND patPayDet.unapply='0'";

				$rs=imw_query($qry);
				while($res=imw_fetch_array($rs)){
					$pay_det_id = $arrCioAccId[$res['payment_id']];
					$opr_id = $tempPayData[$pay_det_id];
					$arrPaymentsApplied[$pay_det_id] += $res['paidForProc'];
					$tempCIOArr[$pay_det_id]['applied']+= $res['paidForProc'];
				}
			}

			$i=0;
			//FINAL ARRAY
			foreach($arrSplittedPayDetIds as $pay_det_id){
				$cioData = $tempCIOArr[$pay_det_id];

				$cioPayment = $cioData['payment'];
				$created_by = $cioData['created_by'];
				$patId = $cioData['pat_id'];
				$created_on = $cioData['payment_date'];

				if($cioData['applied']>0){
					$cioPayment-= trim($cioData['applied']);
				}
				$cioPayment-= trim($cioData['refund']);
				
				if($cioPayment!=0 && $cioPayment!=''){

					if($Process == "summary"){
						$payType = $tempData[$pay_det_id]['payment_type'];
						
						if($payType=='checkin'){
							$arrPayments[$created_by]['ci_payment']+= $cioData['payment'];
							$arrPayments[$created_by]['ci_payment_ref']+= $cioData['refund'];
						}else{
							$arrPayments[$created_by]['co_payment']+= $cioData['payment'];
							$arrPayments[$created_by]['co_payment_ref']+= $cioData['refund'];
						}
						$arrPayments[$created_by]['un_applied']+= $cioPayment;
						
					}else{
						$arrPayments[$created_by]['patient'][$patId]['pat_detail'] = $cioData['pat_name'];
						$arrPayments[$created_by]['patient'][$patId]['payments'][$created_on]['cio_payment'][$i]['payment']= $cioData['payment'];
						$arrPayments[$created_by]['patient'][$patId]['payments'][$created_on]['cio_payment'][$i]['un_applied']= $cioPayment;
						$arrPayments[$created_by]['patient'][$patId]['payments'][$created_on]['cio_payment'][$i]['payment_date'] = $cioData['payment_date'];
						$arrPayments[$created_by]['patient'][$patId]['payments'][$created_on]['cio_payment'][$i]['cio_field_id'] = $cioData['cio_field_id'];
						$arrPayments[$created_by]['patient'][$patId]['payments'][$created_on]['cio_payment'][$i]['refund'] = $cioData['refund'];
						$i++;
					}
				}								
			}
		}
	}


	// GET PATIENT PRE PAYMENTS
	$q = "SELECT pDep.id, 
		pDep.patient_id, 
		pDep.paid_amount, 
		DATE_FORMAT(pDep.paid_date, '".get_sql_date_format()."') as 'paidDate', 
		pDep.apply_payment_date, pDep.provider_id,
		pData.default_facility, 
		pDep.entered_by, 
		pDep.apply_payment_type, 
		pDep.apply_amount, 
		pDep.del_status, 
		pDep.del_operator_id, 
		pDep.facility_id, 
		DATE_FORMAT(pDep.trans_del_date, '".get_sql_date_format()."') as 'delDate',
		pData.fname as 'pfname', 
		pData.mname as 'pmname', 
		pData.lname as 'plname' 
		FROM patient_pre_payment pDep 
		LEFT JOIN patient_data pData ON pData.id = pDep.patient_id
		LEFT JOIN facility ON facility.id = pDep.facility_id	
		WHERE (pDep.paid_date between '$st_date' and '$en_date') AND pDep.del_status='0'";
	if(empty($facility_name_str) === false){
		$q.= " and pDep.facility_id in($facility_name_str)";
	}
	if(empty($primaryProviderId) === false){
		$q .= " and pDep.provider_id in($primaryProviderId)";
	}
	if(empty($opr_name_str) === false){
		$q .= " AND pDep.entered_by in($opr_name_str)";
	}
	if(empty($grp_id) === false){
		$q .= " AND facility.default_group in($grp_id)";
	}
 	$q .= " ORDER BY pDep.paid_date";
	$patQryRes = array();
	$qry = imw_query($q);
	while($rowq = imw_fetch_assoc($qry)){
		$patQryRes[] = $rowq;
	}
	$arrPrePayIds=array();
	$arrPrePayments = array();
	$tempDetailArr = array();
	for($i=0;$i<count($patQryRes);$i++){
		$refundAmt=0;
		//query to get refund detail for current pre payments if any
		$qryRef=imw_query("Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND pmt_id = '".$patQryRes[$i]['id']."' AND (entered_date BETWEEN '".$st_date."' AND '".$en_date."')")or die(imw_error().'_656');
		while($rsRef=imw_fetch_array($qryRef))
		{
			$refundAmt+=$rsRef['ref_amt'];
		}
	//	$qryRef.close;	

		$patId = $patQryRes[$i]['patient_id'];
		$oprId = $patQryRes[$i]['entered_by'];
		$id = $patQryRes[$i]['id'];
		$paid_date = $patQryRes[$i]['paidDate'];
		$balance_amount=$patQryRes[$i]['paid_amount'];
		$arrPrePayIds[$id] = $id;
		$patNameArr["LAST_NAME"] = $patQryRes[$i]['plname'];
		$patNameArr["FIRST_NAME"] = $patQryRes[$i]['pfname'];
		$patNameArr["MIDDLE_NAME"] = $patQryRes[$i]['pmname'];
		$patName = changeNameFormat($patNameArr);
		
		if($patQryRes[$i]['del_status']=='0'){
			if($patQryRes[$i]['apply_payment_type']=='manually'){
				$arrTempPrePay[$id]['applied']+= $patQryRes[$i]['apply_amount'];
			}
			$arrTempPrePay[$id]['pat_id'] = $patId;
			$arrTempPrePay[$id]['pat_name'] = $patName;
			$arrTempPrePay[$id]['payment_date'] = $patQryRes[$i]['paidDate'];
			$arrTempPrePay[$id]['pre_payment'] = $balance_amount;
			$arrTempPrePay[$id]['opr_id'] = $oprId;
			$arrTempPrePay[$id]['refund'] = $refundAmt;
			$arrAllIds[$id]=$id;
		}
		if($patQryRes[$i]['del_status']=='1'){
			if($Process == "summary"){
				$arrPayments[$oprId]['del_payment'] += $balance_amount;
				$arrPayments[$oprId]['del_payment_ref'] += $refundAmt;
			}
			
			$del_by = $patQryRes[$i]['del_operator_id'];
			$del_date = $patQryRes[$i]['delDate'];
			$arrDelPrePayDetail = array();
			$arrDelPrePayDetail['del_date'] = $patQryRes[$i]['delDate'];
			$arrDelPrePayDetail['payment_id'] = $patQryRes[$i]['id'];
			$arrDelPrePayDetail['pre_payment'] = $balance_amount;
			$arrDelPrePayDetail['pre_payment_ref'] = $refundAmt;
			
			$arrDelPayments[$del_by]['patient'][$patId]['pat_detail'] = $patName;
			$arrDelPayments[$del_by]['patient'][$patId]['payments'][$paid_date]['pre_payment'][] = $arrDelPrePayDetail;
		}
		
		$tempDetailArr[$id]['pat_id'] = $patId;
		$tempDetailArr[$id]['pat_name'] = $patName;
		$tempDetailArr[$id]['payment_date'] = $patQryRes[$i]['paidDate'];
		$tempDetailArr[$id]['opr_id'] = $oprId;
	}

	// GET PRE PAT ENCOUNTER APPLIED AMTS
	if(count($arrPrePayIds)>0){
		$strPrePayIds = implode(',', $arrPrePayIds);
		$q = "SELECT patient_pre_payment_id, 
		   paidForProc 
		   FROM patient_charges_detail_payment_info  
		   WHERE patient_charges_detail_payment_info.patient_pre_payment_id IN($strPrePayIds) 
		   AND deletePayment='0' 
		   AND patient_charges_detail_payment_info.unapply='0'";
		$res = imw_query($q);
		while($row = imw_fetch_array($res)){
			$id = $row['patient_pre_payment_id'];
			$opr_id=$arrTempPrePay[$id]['opr_id'];
			$arrTempPrePay[$id]['applied']+= $row['paidForProc'];
		}
	}
	
	$i=0;
	//FINAL PRE-PAYMENT ARRAY
	foreach($arrTempPrePay as $preId => $preData){
		$unappliedAmt=0;
		$tempArr = $tempDetailArr[$preId];
		$oprId= $preData['opr_id'];
		if($oprId == ""){
			$oprId = $tempArr['opr_id'];
		}
		$pt_name = $preData['pat_name'];
		if($pt_name == ""){
			$pt_name = $tempArr['pat_name'];
		}
		$patId = $preData['pat_id'];
		if($patId == ""){
			$patId = $tempArr['pat_id'];
		}
		
		$paid_date = $preData['payment_date'];
		if($paid_date == ""){
			$paid_date = $tempArr['payment_date'];
		}
		$preData['pre_payment'] = trim($preData['pre_payment']);
		$preData['applied']= trim($preData['applied']);
		$preData['refund']= trim($preData['refund']);
		$unappliedAmt = trim($preData['pre_payment']) - trim(($preData['applied'] + $preData['refund']));
		
		if($unappliedAmt!=0 && $unappliedAmt!=''){
			if($Process=='summary'){
				$arrPayments[$oprId]['pre_payment']+= $preData['pre_payment'];
				$arrPayments[$oprId]['pre_payment_ref']+= $preData['refund'];
				$arrPayments[$oprId]['pre_un_applied']+= $unappliedAmt;
			}else{
				$arrPayments[$oprId]['patient'][$patId]['pat_detail'] = $pt_name;
				$arrPayments[$oprId]['patient'][$patId]['payments'][$paid_date]['pre_payment'][$i]['payment_date']= $paid_date;
				$arrPayments[$oprId]['patient'][$patId]['payments'][$paid_date]['pre_payment'][$i]['pre_payment']= $preData['pre_payment'];
				$arrPayments[$oprId]['patient'][$patId]['payments'][$paid_date]['pre_payment'][$i]['un_applied']= $unappliedAmt;
				$arrPayments[$oprId]['patient'][$patId]['payments'][$paid_date]['pre_payment'][$i]['refund']= $preData['refund'];
				$i++;
			}
		}
	}
	//END FETCHING DATA
	
	if(count($arrPayments)>0){
		$arrProviderID=explode(',', $providerID);
		$arrFacilityName=explode(',', $facility_name);
		$arrOprName=explode(',', $opr_name);

		$sel_grp = $CLSReports->report_display_selected(implode(',',$grp_id),'group',1,$grp_cnt);
		$sel_fac = $CLSReports->report_display_selected(implode(',',$facility_name),'facility_tbl',1,$fac_cnt);
		$sel_phy = $CLSReports->report_display_selected(implode(',',$phyId),'physician',1,$phy_cnt);
		$sel_opr = $CLSReports->report_display_selected(implode(',',$operator_id),'operator',1,$opr_cnt);
		
		$arrPhyTotals=array();
		$arrFacTotals=array();
		$arrGrandTotals=array();
		$report_tit="Unapplied Payments Report (Summary)";
		if($Process!='summary'){ $report_tit=	"Unapplied Payments Report (Detail)";	}
		
		if($Process == "summary"){
			include_once("unapplied_payment_summary.php");
		}else{
			include_once("unapplied_payment_detail.php");
		}
	} // outermost IF	
}

	$tooltip='Red coloured CI/CO and Pre-Payments represents that there is refund amount deducted from these payments.';
	
	if($pdf_data){
	$pdf_data .= '<table width="100%" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8" >
		<tr><td style="height:10px; background-color:#FFFFFF;" colspan="3"></td></tr>
		<tr><td style="width:20px;" class="info" style="background-color:#FFFFFF;">&nbsp;</td>
		<td style="width:4px;" height="5px;" bgcolor="#FF0000"></td>
		<td class="info" style="padding-left:20px; ; background-color:#FFFFFF;">
		'.$tooltip.'
		</td>
		</tr>
		</table>';
	}

	if($page_data){
	$page_data .= '<table width="100%" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8" >
			<tr><td style="height:10px; background-color:#FFFFFF;" colspan="3"></td></tr>
			<tr><td style="width:20px;" style="background-color:#FFFFFF;">&nbsp;</td>
			<td style="width:4px;" height="5px;" bgcolor="#FF0000"></td>
			<td class="info" style="padding-left:20px; ; background-color:#FFFFFF;">
			'.$tooltip.'<br/>Refund amount can be view by mouse over on red coloured amount.
			</td>
			</tr>
			</table>';
	}
	
$op = 'l';
$HTMLCreated=0;
if($printFile == true and $page_data != ''){
	$HTMLCreated=1;
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$page_data;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$pdf_data;

	$file_location = write_html($strHTML);
}else{
	$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
}

if($output_option=='view' || $output_option=='output_csv'){
	echo $csv_file_data;	
}

/*

	$reports_html_var = '<style type="text/css">'.file_get_contents("css/reports_html.css").'</style>';	
	if($page_data){
		$page_data = $pdf_header.$page_data;
		echo $reports_html_var.$page_data;
	} else {
		echo $pdf_header;
		echo '<div class="text-center alert alert-info">No Record exists.</div>';;
	}
		
	if($printFile == true and $page_data != ''){
		$reports_pdf_var = '<style type="text/css">'.file_get_contents("css/reports_pdf.css").'</style>';
		$op='l';
		$file_location = write_html($pdf_data);
		$conditionChk = true;
		
		
		
		$reports_pdf_var = '<style type="text/css">'.file_get_contents("css/reports_pdf.css").'</style>';
		$strHTML1 = $reports_pdf_var;
		$strHTML1.=<<<DATA
		<page backtop="9mm" backbottom="5mm">
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
		<page_header>
		$pdf_header
		<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff" style="width:100%">
		</table>
		</page_header>
		$pdf_data
		</page>
DATA;
		$op='l';
		$file_location = write_html($strHTML1);
		$conditionChk = true;
	}	*/
?>