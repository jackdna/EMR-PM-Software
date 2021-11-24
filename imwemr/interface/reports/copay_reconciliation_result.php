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
FILE : COPAY_RECONCILLATION_RESULT.PHP
PURPOSE :  COPAY RECONSILLITION REPORT RESULT
ACCESS TYPE : DIRECT
*/

$without_pat = "yes";
require_once("reports_header.php");
$dateFormat= get_sql_date_format();
$phpDateFormat = phpDateFormat();
$curDate = date($phpDateFormat.' h:i A');		
$FCName= $_SESSION['authId'];
$page_data = NULL;
$pdf_data = NULL;
$curDate = date($phpDateFormat);
if($Start_date == ""){
	$Start_date = $curDate;
	$End_date = $curDate;
}

$sel_opr = $CLSReports->report_display_selected(implode(',',$operator_id),'operator',1,$opr_cnt);
	
if($_POST['form_submitted']){
	
	//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
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
	//---------------------
	
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$createdBy = ucfirst(trim($op_name_arr[1][0]));
	$createdBy .= ucfirst(trim($op_name_arr[0][0]));

	//operator_id
//	$rqArrOprId = $_REQUEST['operator_id'];
//	$operator = join(',',$rqArrOprId);
}

$arrFacilitySel=array();
$arrDoctorSel=array();

$printFile=true;
//pre($_REQUEST);
unset($_REQUEST['operator']);
if(trim($_REQUEST['Submit']) != ''){
	$printFile = false;
    
    //--- CHANGE DATE FORMAT ----
	$startDate = getDateFormatDB($Start_date);
	$endDate   = getDateFormatDB($End_date);
	
	
	$str_exclude_procedures=implode(',',$exclude_procedures);
	$str_exclude_status=implode(',',$exclude_status);

    //$operatorIds = $selOperId;
	//GET ID OF TEST TYPE USER
	$master_test_type_id=0;
	$rs=imw_query("Select user_type_id FROM user_type WHERE TRIM(LOWER(user_type_name))='test'");	
	$res=imw_fetch_array($rs);
	$master_test_type_id=$res['user_type_id'];
	

	//GET ALL USERS
	$arrUsersType=array();
	$providerNameArr[0] = 'No Provider';
	//$providerNameArr['No'] = 'No Check In/Out';
	$rs=imw_query("Select id, fname, mname, lname, username, user_type FROM users");	
	while($res=imw_fetch_array($rs)){
		$id = $res['id'];
		$pro_name_arr = array();
		$pro_name_arr["LAST_NAME"] = $res['lname'];
		$pro_name_arr["FIRST_NAME"] = $res['fname'];
		$pro_name_arr["MIDDLE_NAME"] = $res['mname'];
		$pro_name = changeNameFormat($pro_name_arr);
		//$providerNameArr[$id] = $pro_name;
		$providerNameArr[$id] = strtoupper($res['fname'][0].$res['mname'][0].$res['lname'][0]);
		$allUsersByName[$res['username']] = $id;
		$allUsernameById[$id] = $res['username'];
		$arrUsersType[$id][$res['user_type']]=$res['user_type'];
	}

	// GET CI/CO FILEDS NAMES
	$cicoFields=array();
	$rs=imw_query("Select id,item_name FROM check_in_out_fields");
	while($res=imw_fetch_array($rs)){
		$cicoFields[$res['id']] = $res['item_name'];
	}
	
	
	$selOprNames='';
	if(empty($operator_id)===false){ 
		$arrOperatorSel = $operator_id; 
		$tempOpr=array();
		foreach($arrOperatorSel as $oprId){
			$tempOpr[$oprId] = $allUsernameById[$oprId];
		}
		if(sizeof($tempOpr)>0){
			$selOprNames = "'".implode("','", $tempOpr)."'";
		}
	}
	
	//FUNCTION TO GET COPAY AMOUNT WITH SOME CONDITIONS
	function getCopayAmt($insType, $arrInsCaseCopay, $arrCopayType, $arrCopayCollect, $arrUsersType, $docId=0, $master_test_type_id=0, $caseId=0){
		$copayAmt=0;

		if($arrCopayType[$caseId][$insType]=='0'){ //means "Practice" type
			$copayAmt=0;
			
		}else if($arrCopayType[$caseId][$insType]=='1'){ //means "Dilated/Un-Dilated" type
			$arrCopay=explode("/", $arrInsCaseCopay[$caseId][$insType]);
			if($arrCopay[1]){
				$copayAmt=$arrCopay[1];
			}else{
				$copayAmt=0;
			}

		}else if($arrCopayType[$caseId][$insType]=='2'){ //means "Office/Test" type

			//CHECK IF USER IS "TEST" TYPE
			if($arrUsersType[$docId][$master_test_type_id]){
				//CHECK IF INSURANCE COMPANY HAS SELECTED "COPAY COLLECT" AS YES
				if($arrCopayCollect[$caseId][$insType]=='1'){ 
					$arrCopay=explode("/", $arrInsCaseCopay[$caseId][$insType]);
					if($arrCopay[1]){
						$copayAmt=$arrCopay[1];
					}else{
						$copayAmt=0;
					}
				}else{
					$copayAmt=0;	
				}
			}else{
				$copayAmt=0;	
			}
		}

		return $copayAmt;
	}
	

	//--- GET RECORDS
	
	//AND previous_status.status IN(11,13,3,18) 
	$qry = "Select sa.id, sa.sa_patient_id, sa.sa_doctor_id, sa.case_type_id, sa.status_update_operator_id, sa.sa_app_start_date, DATE_FORMAT(sa.sa_app_start_date , '%Y-%m-%d') as 'appt_date',
	DATE_FORMAT(sa.sa_app_start_date , '".get_sql_date_format()."') as 'created_date',sa.sa_app_starttime, 
	sa.sa_patient_app_status_id, previous_status.status,previous_status.status_time,
	previous_status.statusChangedBy, pd.fname, pd.mname, pd.lname 
	FROM schedule_appointments sa 
	LEFT JOIN previous_status ON previous_status.sch_id = sa.id 
	LEFT JOIN patient_data pd ON pd.id = sa.sa_patient_id 
	WHERE (sa.sa_app_start_date BETWEEN '$startDate' AND '$endDate') AND sa.sa_patient_app_status_id NOT IN(19,201,203) 
    AND LOWER(pd.lname)!='doe'";
	if(empty($str_exclude_procedures)===false){
		$qry.= " AND sa.procedureid NOT IN(".$str_exclude_procedures.")";
	}
	if(empty($str_exclude_status)===false){
		$qry.= " AND sa.sa_patient_app_status_id NOT IN(".$str_exclude_status.")";
	}	
	if(empty($selOprNames) === false){
		$qry.= " AND previous_status.statusChangedBy in(".$selOprNames.")";
	}
	$qry.="  ORDER BY pd.lname, pd.fname, sa.sa_app_start_date, sa.sa_app_starttime, previous_status.status DESC";
    $rs = imw_query($qry);
    $grpoperator_ids = array();
	while($res = imw_fetch_array($rs)){
		$printFile=true;
		$pid = $res['sa_patient_id'];
		$schId = $res['id'];
		$arrSchIds[$schId] = $schId;
		$sch_sa_status  = $res['sa_patient_app_status_id'];
		$sch_status  = $res['status'];
		$status_time  = $res['status_time'];
        $sel_opr_id = $res['statusChangedBy'];
        
		$arrPatData[$pid]['pat_name'] = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
        $oprId_rs = imw_query("select id from users where username='$sel_opr_id'");
        $oprId_res = imw_fetch_assoc($oprId_rs);
        $oprId = $oprId_res['id'];
        if($grpby_block=='grpby_operators'){
            $firstGrpBy= $oprId;
		}
	 
		if(($sch_sa_status=='0' || $sch_status=='3' || $sch_status=='18') && ($sch_status!='11' || $sch_status!='13') && ($res['case_type_id']<=0 || $sch_status=='3' || $sch_status=='18')){ // NO CHECIN/OUT or CANCELLED or NO SHOW
			if($sch_sa_status=='0'){
				$status='No Check In/Out';
			}else if($sch_sa_status=='3'){
				$status = 'No Show';
			}else if($sch_sa_status=='18'){
				$status = 'Cancelled';
			}
			$arrDataOthers[$schId][$pid]['pat_name']= $arrPatData[$pid]['pat_name'];
			$arrDataOthers[$schId][$pid]['created_date'] = $res['created_date'];
			$arrDataOthers[$schId][$pid]['sa_app_starttime'] = $res['sa_app_starttime'];
			$arrDataOthers[$schId][$pid]['status'] = $status;

		}else if(!$arrDataOthers[$schId]){
			
			if($sch_status=='13'){ 
				$tempCIO[$schId]['lbl']='checkin';
				$tempCIO[$schId]['opr_id'] = $allUsersByName[$res['statusChangedBy']]; 
				$tempCIO[$schId]['ci_opr_id'] = $allUsersByName[$res['statusChangedBy']];
				$tempCIO[$schId]['ci_status_time'] = $status_time;
			}else if($sch_status=='11'){ 
				$tempCIO[$schId]['lbl']='checkout';
				$tempCIO[$schId]['opr_id'] = $allUsersByName[$res['statusChangedBy']];
				$tempCIO[$schId]['co_opr_id'] = $allUsersByName[$res['statusChangedBy']];
				$tempCIO[$schId]['co_status_time'] = $status_time;
				
			}

			$res['payment_type'] = $tempCIO[$schId]['lbl'];
			$res['new_operator'] = $tempCIO[$schId]['opr_id'];
			$res['ci_new_operator'] = $tempCIO[$schId]['ci_opr_id'];
			$res['co_new_operator'] = $tempCIO[$schId]['co_opr_id'];
			$res['ci_created_time'] = $tempCIO[$schId]['ci_status_time'];
			$res['co_created_time'] = $tempCIO[$schId]['co_status_time'];
			
			$tempArrCIO[$pid][$schId]=$res;
	
			$arrInsCaseIds[$res['case_type_id']]['id'] = $res['case_type_id'];
			$arrInsCaseIds[$res['case_type_id']]['schdule_date'] = $res['appt_date'];
			$arrInsCaseIds[$res['case_type_id']]['created_date'] = $res['created_date'];
			$arrInsCaseIds[$res['case_type_id']]['sa_app_starttime'] = $res['sa_app_starttime'];

			unset($arrDataOthers[$schId]);
		}
		if($res['case_type_id']>0){
			$tempPatIds[$pid] = $pid;
			$tempCaseIds[$res['case_type_id']] = $res['case_type_id'];
		}
		$tempOrderByName1[$schId][$pid] = $pid;
		$tempOrderByName[$firstGrpBy][$schId][$pid] = $pid;
        
        $grpoperator_ids[] = $firstGrpBy;
	}unset($rs);	

	// -- GET CI/CO DETAILS
	if(sizeof($tempCIO)>0){
		$arrSchIds = array_keys($tempCIO);
		$strSchIds  =implode(',', $arrSchIds);
		$data = array();
        $where='';
        if($pay_method) {
            $where=" AND cioPay.payment_method='$pay_method' ";
        }
		
		$qry = "Select cioPay.sch_id, cioPay.payment_method, cioPay.patient_id, cioPayDet.id, cioPayDet.item_id, cioPayDet.item_payment,cioPay.payment_type,cioPay.created_time
		FROM check_in_out_payment cioPay 
		LEFT JOIN check_in_out_payment_details cioPayDet ON cioPayDet.payment_id = cioPay.payment_id 
		LEFT JOIN check_in_out_fields cioFields ON cioFields.id =  cioPayDet.item_id 
		WHERE cioPay.sch_id IN(".$strSchIds.") $where AND LOWER(item_name) LIKE '%copay%' AND cioPayDet.status='0'";
		$rs = imw_query($qry);
        if(imw_num_rows($rs)>0) {
            while($res = imw_fetch_array($rs)){
                
                $printFile=true;
                $pid = $res['patient_id'];
                $schId = $res['sch_id'];

                $tot_ci_copay_pay[$pid][$schId][]=$res['item_payment'];
                /*if($res['payment_type']=="checkin"){
                    $data['ci_created_time'] = $res['created_time'];
                }
                if($res['payment_type']=="checkout"){
                    $data['co_created_time'] = $res['created_time'];
                }*/
				$tempArrCIO[$pid][$schId]['item_payment']=$res['item_payment'];
				$tempArrCIO[$pid][$schId]['copay_field']=$schId;
				$tempArrCIO[$pid][$schId]['payment_method']=$res['payment_method'];
                $cicoDetIdsForRefund[$res['id']] = $schId;			
            }
        }unset($rs);
	}

	// GET INS COPAY AMT
	if(sizeof($tempPatIds)>0){
		
		$strPatIds = implode(',', $tempPatIds);
		$caseIds = implode(',', $tempCaseIds);
		$qry = "Select insData.id,insData.type,insData.provider, insData.copay, insData.pid, insData.ins_caseid, insData.self_pay_provider, 
		insData.copay_type, 
		DATE_FORMAT(insData.effective_date, '%Y-%m-%d') as 'effective_date', DATE_FORMAT(insData.expiration_date, '%Y-%m-%d') as 'expiration_date', 
		insData.actInsComp,insurance_companies.in_house_code, insurance_companies.collect_copay 
		FROM insurance_data insData 
		left join insurance_companies on insurance_companies.id=insData.provider
		WHERE insData.pid IN(".$strPatIds.") AND insData.ins_caseid IN(".$caseIds.") ORDER BY insData.actInsComp DESC";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$type= $res['type'];
			$pid = $res['pid'];
			$caseId = $res['ins_caseid'];
		
			$tempAllInsData[$pid] = $pid;
			
			if($res['self_pay_provider']=='1'){
				$tempAllDataSelfPay[$pid] = $pid;
			}else{
				if($arrInsCaseIds[$caseId]['id'] && $res['actInsComp']=='1' && (($res['effective_date']<=$arrInsCaseIds[$caseId]['schdule_date'] && $res['expiration_date']=='0000-00-00') || $res['expiration_date']>=$arrInsCaseIds[$caseId]['schdule_date']) && !$arrTempCheck[$caseId][$type]){
					if($res['copay']>0){
						$arrInsCaseCopay[$caseId][$res['type']]=  $res['copay'];
						if($res['in_house_code']!=""){
							$arrInsCaseCopay_id[$caseId][$res['type']]=  substr($res['in_house_code'],0,6).'/';
						}
						$arrTempCheck[$caseId][$type] = 1;
						$arrAllInsCopay[$pid] = $pid;
						$arrCopayType[$caseId][$res['type']]= $res['copay_type'];
						$arrCopayCollect[$caseId][$res['type']]= $res['collect_copay'];
					}else{
						$arrNoCopay[$pid] = $pid;
					}
					$arrTempChkIns[$caseId] = $caseId;
				}else{
					if($res['copay']>0 && !$arrTempChkIns[$caseId]){
						$arrInsCopay[$pid][$res['type']]=  $res['copay'];
						if($res['in_house_code']!=""){
							$arrInsCopay_id[$pid][$res['type']]=  substr($res['in_house_code'],0,6).'/';
						}
						$arrAllInsCopay[$pid] = $pid;
						$arrCopayType[$caseId][$res['type']]= $res['copay_type'];
						$arrCopayCollect[$caseId][$res['type']]= $res['collect_copay'];
					}
					if($res['copay']<=0 && !$arrTempChkIns[$caseId]){
						$arrNoCopay[$pid] = $pid;
					}
				}
			}
		}
		unset($rs);			

		// FINAL ARRAY
		foreach($tempPatIds as $pid){
			
			foreach($tempArrCIO[$pid] as $schId => $cioData){
				$printFile = true;
				$payType='';
				$copay_amt=0;
				
				$payType = $cioData['payment_type']; // checkin/checkout
				$caseId = $cioData['case_type_id'];
				$itemId = $cioData['item_id'];
				$docId = $cioData['sa_doctor_id'];

				if($arrNoCopay[$pid] && !$tempAllDataSelfPay[$pid]){
					// RE-APPLY OLD VARIABLE VALUES
					$tempAllDataNoCopay[$schId][$pid]['pat_name']= $arrPatData[$pid]['pat_name'];
					$tempAllDataNoCopay[$schId][$pid]['created_date'] = $cioData['created_date'];
					$tempAllDataNoCopay[$schId][$pid]['sa_app_starttime'] = $cioData['sa_app_starttime'];
					$tempAllDataNoCopay[$schId][$pid]['checkin_by'] = $cioData['ci_new_operator'];
					$tempAllDataNoCopay[$schId][$pid]['checkout_by'] = $cioData['co_new_operator'];
					$tempAllDataNoCopay[$schId][$pid]['status'] = 'No Copay';
					
				}else if(!$tempAllDataSelfPay[$pid]){

					// RE-APPLY OLD VARIABLE VALUES
					if($tempAllData[$schId][$pid]['case_id']){
						$caseId = $tempAllData[$schId][$pid]['case_id'];
					}// -----------------------

					if($cioData['copay_field']){
						if($cioData['item_payment']>0){
							$tempAllData[$schId][$pid]['copay_collected']+= array_sum($tot_ci_copay_pay[$pid][$schId]);
							$tempAllData[$schId][$pid]['pat_name']= $arrPatData[$pid]['pat_name'];
							$tempAllData[$schId][$pid]['created_date'] = $cioData['created_date'];
							$tempAllData[$schId][$pid]['sa_app_starttime'] = $cioData['sa_app_starttime'];
							//$tempAllData[$schId][$pid][$payType.'_by'] = $cioData['new_operator'];
							$tempAllData[$schId][$pid]['checkin_by'] = $cioData['ci_new_operator'];
							$tempAllData[$schId][$pid]['checkout_by'] = $cioData['co_new_operator'];
							$tempAllData[$schId][$pid]['ci_created_time'] = $cioData['ci_created_time'];
							$tempAllData[$schId][$pid]['co_created_time'] = $cioData['co_created_time'];
							$tempAllData[$schId][$pid]['case_id'] = $caseId;
							$tempAllData[$schId][$pid]['cico_det_id'] = $cioData['cico_det_id'];
							$tempAllData[$schId][$pid]['payment_method'] = $cioData['payment_method'];

							if($arrInsCaseCopay[$caseId]['primary']){

								//IF SECOND APPOINTMENT ON SAME DAY THEN CONDITIONS APPLY ON COPAY AMT
								if($tempCheckMoreThanOneApptPaid[$pid][$cioData['sa_app_start_date']]){
									$copay_amt = getCopayAmt('primary', $arrInsCaseCopay, $arrCopayType, $arrCopayCollect, $arrUsersType, $docId, $master_test_type_id, $caseId);
									$arrInsCaseCopay[$caseId]['primary'] = $copay_amt;
								}
								
								$tempAllData[$schId][$pid]['pri_ins_copay'] = $arrInsCaseCopay[$caseId]['primary'];
								$tempAllData[$schId][$pid]['pri_ins_comp'] = $arrInsCaseCopay_id[$caseId]['primary'];
							}else{
								$tempAllData[$schId][$pid]['pri_ins_copay'] = $arrInsCopay[$pid]['primary'];
								$tempAllData[$schId][$pid]['pri_ins_comp'] = $arrInsCopay_id[$pid]['primary'];
							}
							
							if($arrInsCaseCopay[$caseId]['secondary']){
								//IF SECOND APPOINTMENT ON SAME DAY THEN CONDITIONS APPLY ON COPAY AMT
								if($tempCheckMoreThanOneApptPaid[$pid][$cioData['sa_app_start_date']]){
									$copay_amt = getCopayAmt('secondary', $arrInsCaseCopay, $arrCopayType, $arrCopayCollect, $arrUsersType, $docId, $master_test_type_id, $caseId);
									$arrInsCaseCopay[$caseId]['secondary'] = $copay_amt;
								}
																
								$tempAllData[$schId][$pid]['sec_ins_copay'] = $arrInsCaseCopay[$caseId]['secondary'];
								$tempAllData[$schId][$pid]['sec_ins_comp'] = $arrInsCaseCopay_id[$caseId]['secondary'];
							}else{
								$tempAllData[$schId][$pid]['sec_ins_comp'] = $arrInsCopay[$pid]['secondary'];
								$tempAllData[$schId][$pid]['sec_ins_comp'] = $arrInsCopay_id[$pid]['secondary'];
							}

							$tempCheckMoreThanOneApptPaid[$pid][$cioData['sa_app_start_date']]=1;						

						}else{ 
							//if(empty($payType)===true){ $payType='checkin'; $cioData['new_operator']='No';}
							$tempAllDataNotCollected[$schId][$pid]['copay_collected']= 0;
							$tempAllDataNotCollected[$schId][$pid]['pat_name']= $arrPatData[$pid]['pat_name'];
							$tempAllDataNotCollected[$schId][$pid]['created_date'] = $cioData['created_date'];
							$tempAllDataNotCollected[$schId][$pid]['sa_app_starttime'] = $cioData['sa_app_starttime'];
							//$tempAllDataNotCollected[$schId][$pid][$payType.'_by'] = $cioData['new_operator'];
							$tempAllDataNotCollected[$schId][$pid]['checkin_by'] = $cioData['ci_new_operator'];
							$tempAllDataNotCollected[$schId][$pid]['checkout_by'] = $cioData['co_new_operator'];
							$tempAllDataNotCollected[$schId][$pid]['ci_created_time'] = $cioData['ci_created_time'];
							$tempAllDataNotCollected[$schId][$pid]['co_created_time'] = $cioData['co_created_time'];
							$tempAllDataNotCollected[$schId][$pid]['case_id'] = $caseId;
                            
							if($arrInsCaseCopay[$caseId]['primary']){

								//IF SECOND APPOINTMENT ON SAME DAY THEN CONDITIONS APPLY ON COPAY AMT
								if($tempCheckMoreThanOneApptPaid[$pid][$cioData['sa_app_start_date']]){
									$copay_amt = getCopayAmt('primary', $arrInsCaseCopay, $arrCopayType, $arrCopayCollect, $arrUsersType, $docId, $master_test_type_id, $caseId);
									$arrInsCaseCopay[$caseId]['primary'] = $copay_amt;
								}

								$tempAllDataNotCollected[$schId][$pid]['pri_ins_copay'] = $arrInsCaseCopay[$caseId]['primary'];
								$tempAllDataNotCollected[$schId][$pid]['pri_ins_comp'] = $arrInsCaseCopay_id[$caseId]['primary'];
							}else{
								$tempAllDataNotCollected[$schId][$pid]['pri_ins_copay'] = $arrInsCopay[$pid]['primary'];
								$tempAllDataNotCollected[$schId][$pid]['pri_ins_comp'] = $arrInsCopay_id[$pid]['primary'];
							}
							
							if($arrInsCaseCopay[$caseId]['secondary']){

								//IF SECOND APPOINTMENT ON SAME DAY THEN CONDITIONS APPLY ON COPAY AMT
								if($tempCheckMoreThanOneApptPaid[$pid][$cioData['sa_app_start_date']]){
									$copay_amt = getCopayAmt('secondary', $arrInsCaseCopay, $arrCopayType, $arrCopayCollect, $arrUsersType, $docId, $master_test_type_id, $caseId);
									$arrInsCaseCopay[$caseId]['secondary'] = $copay_amt;
								}
								
								$tempAllDataNotCollected[$schId][$pid]['sec_ins_copay'] = $arrInsCaseCopay[$caseId]['secondary'];
								$tempAllDataNotCollected[$schId][$pid]['sec_ins_comp'] = $arrInsCaseCopay_id[$caseId]['secondary'];
							}else{
								$tempAllDataNotCollected[$schId][$pid]['sec_ins_copay'] = $arrInsCopay[$pid]['secondary'];
								$tempAllDataNotCollected[$schId][$pid]['sec_ins_comp'] = $arrInsCopay_id[$pid]['secondary'];
							}
							
							$tempCheckMoreThanOneAppt[$pid][$cioData['sa_app_start_date']]=1;
						}
					}else{
						
						//if(empty($payType)===true){ $payType='checkin'; $cioData['new_operator']='No';}
						$tempAllDataNotCollected[$schId][$pid]['copay_collected']= 0;
						$tempAllDataNotCollected[$schId][$pid]['pat_name']= $arrPatData[$pid]['pat_name'];
						$tempAllDataNotCollected[$schId][$pid]['created_date'] = $cioData['created_date'];
						$tempAllDataNotCollected[$schId][$pid]['sa_app_starttime'] = $cioData['sa_app_starttime'];
						//$tempAllDataNotCollected[$schId][$pid][$payType.'_by'] = $cioData['new_operator'];
						$tempAllDataNotCollected[$schId][$pid]['checkin_by'] = $cioData['ci_new_operator'];
						$tempAllDataNotCollected[$schId][$pid]['checkout_by'] = $cioData['co_new_operator'];
						$tempAllDataNotCollected[$schId][$pid]['ci_created_time'] = $cioData['ci_created_time'];
						$tempAllDataNotCollected[$schId][$pid]['co_created_time'] = $cioData['co_created_time'];
						$tempAllDataNotCollected[$schId][$pid]['case_id'] = $caseId;
                        
						if($arrInsCaseCopay[$caseId]['primary']){

							//IF SECOND APPOINTMENT ON SAME DAY THEN CONDITIONS APPLY ON COPAY AMT
							if($tempCheckMoreThanOneAppt[$pid][$cioData['sa_app_start_date']]){
								$copay_amt = getCopayAmt('primary', $arrInsCaseCopay, $arrCopayType, $arrCopayCollect, $arrUsersType, $docId, $master_test_type_id, $caseId);
								$arrInsCaseCopay[$caseId]['primary'] = $copay_amt;
							}
							
							$tempAllDataNotCollected[$schId][$pid]['pri_ins_copay'] = $arrInsCaseCopay[$caseId]['primary'];
							$tempAllDataNotCollected[$schId][$pid]['pri_ins_comp'] = $arrInsCaseCopay_id[$caseId]['primary'];
						}else{
							$tempAllDataNotCollected[$schId][$pid]['pri_ins_copay'] = $arrInsCopay[$pid]['primary'];
							$tempAllDataNotCollected[$schId][$pid]['pri_ins_comp'] = $arrInsCopay_id[$pid]['primary'];
						}
						
						if($arrInsCaseCopay[$caseId]['secondary']){
							
							//IF SECOND APPOINTMENT ON SAME DAY THEN CONDITIONS APPLY ON COPAY AMT
							if($tempCheckMoreThanOneAppt[$pid][$cioData['sa_app_start_date']]){
								$copay_amt = getCopayAmt('secondary', $arrInsCaseCopay, $arrCopayType, $arrCopayCollect, $arrUsersType, $docId=0, $master_test_type_id=0, $caseId=0);
								$arrInsCaseCopay[$caseId]['secondary'] = $copay_amt;
							}
							
							$tempAllDataNotCollected[$schId][$pid]['sec_ins_copay'] = $arrInsCaseCopay[$caseId]['secondary'];
							$tempAllDataNotCollected[$schId][$pid]['sec_ins_comp'] = $arrInsCaseCopay_id[$caseId]['secondary'];
						}else{
							$tempAllDataNotCollected[$schId][$pid]['sec_ins_copay'] = $arrInsCopay[$pid]['secondary'];
							$tempAllDataNotCollected[$schId][$pid]['sec_ins_comp'] = $arrInsCopay_id[$pid]['secondary'];
						}

						$tempCheckMoreThanOneAppt[$pid][$cioData['sa_app_start_date']]=1;
					}
				}
			
				//SELF PAY DATA
				if($tempAllDataSelfPay[$pid]){

					$tempAllDataSelfPay1[$schId][$pid]['pat_name']= $arrPatData[$pid]['pat_name'];
					$tempAllDataSelfPay1[$schId][$pid]['created_date'] = $cioData['created_date'];
					$tempAllDataSelfPay1[$schId][$pid]['sa_app_starttime'] = $cioData['sa_app_starttime'];
					//$tempAllDataSelfPay1[$schId][$pid][$payType.'_by'] = $cioData['new_operator'];
					$tempAllDataSelfPay1[$schId][$pid]['checkin_by'] = $cioData['ci_new_operator'];
					$tempAllDataSelfPay1[$schId][$pid]['checkout_by'] = $cioData['co_new_operator'];
					$tempAllDataSelfPay1[$schId][$pid]['ci_created_time'] = $cioData['ci_created_time'];
					$tempAllDataSelfPay1[$schId][$pid]['co_created_time'] = $cioData['co_created_time'];
					$tempAllDataSelfPay1[$schId][$pid]['status'] = 'Self Pay';
				}
			}
		}

		// SORT ALL DATA (ORDER BY NAME)
		foreach($tempOrderByName as $operator => $schData){
			foreach($schData as $schId => $pid){
				// COLLECTED DATA
				if($tempAllData[$schId]){
					$arrFinalData[$operator][$schId] = $tempAllData[$schId];
				}
				// NOT COLLECTED DATA
				if($tempAllDataNotCollected[$schId]){
					$arrFinalDataNotCollected[$operator][$schId] = $tempAllDataNotCollected[$schId];
				}
				
				// NO COPAY, SELF PAY, NO CHECK IN/OUT, CANCELLED, NO SHOW
				if($tempAllDataNoCopay[$schId]){  // no copay
					$arrFinalOtherData[$operator][$schId] =  $tempAllDataNoCopay[$schId];
				}
				if($tempAllDataSelfPay1[$schId]){ // self pay
					$arrFinalOtherData[$operator][$schId] =  $tempAllDataSelfPay1[$schId];
				}
				if($arrDataOthers[$schId]){ // no checkin/out, cancelled, no show
					$arrFinalOtherData[$operator][$schId] =  $arrDataOthers[$schId];
				}
			}
		}
	}

	#############################################################
	#query to get refund detail for current ci/co payments if any
	#############################################################
	if(sizeof($cicoDetIdsForRefund)>0){
		
		$strCicoDetIdsForRefund = implode(',', array_keys($cicoDetIdsForRefund));
		$qry="Select ci_co_id, ref_amt FROM ci_pmt_ref WHERE ci_co_id IN(".$strCicoDetIdsForRefund.") AND del_status='0'";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$schId = $cicoDetIdsForRefund[$res['ci_co_id']];
			
			$arrCicoRefund[$schId]+= $res['ref_amt'];
		}unset($rs);

	}
	//-- END FETCHING DATA


    if($printFile==true){
		$page_content='';

		require_once(dirname(__FILE__)."/copay_reconcilliation_details.php");
		
		if($printFile == true and trim($page_content) != ''){
			
			//--- PAGE HEADER DATA ---
			$curDate = date(phpDateFormat().' H:i A');
			$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
			$op_name = $op_name_arr[1][0];
			$op_name .= $op_name_arr[0][0];

			$OperatorSelected=$sel_opr;

			$tooltip='Red coloured CI/CO represents that there is refund amount deducted from these payments.';
			
			$html_page_content = <<<DATA
				<page backtop="5mm" backbottom="10mm">			
				<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				<page_header>
                    <table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
                        <tr class="rpt_headers">
                            <td class="rptbx1" style="width:260px;">&nbsp;Copay Reconciliation Report</td>
                            <td class="rptbx2" style="width:250px;">&nbsp;Appt Date ($Start_date To $End_date)</td>
                            <td class="rptbx3" style="width:260px;">&nbsp;Operator: $OperatorSelected</td>
                            <td class="rptbx1" style="width:250px;">&nbsp;Created by $op_name on $curDate&nbsp;</td>
                        </tr>
                    </table>
				</page_header>
				$page_content
				<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
					<tr><td style="height:10px; background-color:#FFFFFF;" colspan="3"></td></tr>
					<tr><td class="info" style="background-color:#FFFFFF;"></td>
						<td style="width:2px;" height="5px;" bgcolor="#FF0000"></td>
						<td class="info" style="padding-left:20px; ; background-color:#FFFFFF;">
						$tooltip
						</td>
					</tr>
				</table>		
				</page>
DATA;
			//--- CREATE HTML FILE FOR PDF PRINTING ---
            
//			if($callFrom != 'scheduled'){
//			$html_file_name = get_pdf_name($_SESSION['authId'],'dr_total');
//			file_put_contents("new_html2pdf/$html_file_name.html",$html_page_content);
//			}
			
			//--- CSV FILE DATA --
			$page_content = <<<DATA
				<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
                    <tr class="rpt_headers">
                        <td class="rptbx1" style="width:260px;">&nbsp;Copay Reconciliation Report</td>
                        <td class="rptbx2" style="width:250px;">&nbsp;Appt Date ($Start_date To $End_date)</td>
                        <td class="rptbx3" style="width:260px;">&nbsp;Operator: $OperatorSelected</td>
                        <td class="rptbx1" style="width:250px;">&nbsp;Created by $op_name on $curDate&nbsp;</td>
                    </tr>
                </table>
				$page_content
				<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
					<tr><td style="height:10px; background-color:#FFFFFF;" colspan="3"></td></tr>
					<tr><td class="info" style="background-color:#FFFFFF;"></td>
						<td style="width:2px;" height="5px;" bgcolor="#FF0000"></td>
						<td class="info" style="padding-left:20px; ; background-color:#FFFFFF;">
						$tooltip
						<br>Refund amount can be view by mouse over on red coloured amount.
						</td>
					</tr>
				</table>		
DATA;
			$conditionChk = true;
		}
	}
    
}

if($page_content){
    $op='l';
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$page_content;

    $stylePDF = '<style>' . file_get_contents('css/reports_pdf.css') . '</style>';
    $strHTML = $stylePDF . $html_page_content;

    $file_location = write_html($strHTML,'copay_reconcilliation.html');
    
    
    $HTMLCreated=0;
	if($callFrom!='scheduled'){	
		if($printFile==true){
			$HTMLCreated=1;
			if($output_option=='view' || $output_option=='output_csv'){
				echo $csv_file_data;
			} else {
				echo '<div class="text-center alert alert-info">Please Check PDF.</div>';
			}
		}
	}
}else{
	if($callFrom!='scheduled'){	
		echo '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}



?>
