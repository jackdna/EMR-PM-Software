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

$blExcludePageTags = false; //default setting for summary report
$printFile = true;
$totMissingEnc = 0;
$arrCheckPatEnc =array();
$arrCheckEncounters =array();
$arrPatEncDOS = array();

// GET AND SET CHARGES STATUS
$enctStatusArr = $enctStatus;
$enctStatus  = implode(',', $enctStatus);
$billStatus = '';
$chargesStatus ='';
$submittedStatus='';

if(sizeof($enctStatusArr) > 0)
{
	if(in_array("noSuperBill", $enctStatusArr)) { $billStatus = "noSuperBill"; }
	if(in_array("chargesNotEntered", $enctStatusArr)) { $enteredStatus = "chargesNotEntered"; }
	if(in_array("chargesNotPosted", $enctStatusArr)) { $postedStatus = "chargesNotPosted"; }
	if(in_array("chargesNotSubmitted", $enctStatusArr)) { $submittedStatus = "chargesNotSubmitted"; }
}

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

$reportProcess = strtolower($summary_detail);

if($_POST['form_submitted']){
	$conditionChk = false;
	$printFile = false;
	$core_drop_groups_exp = $grp_cnt; 
    $facilityName_exp = $fac_cnt;
    $PHY_NAME_exp = $opr_cnt;

	$startDate = getDateFormatDB($Start_date);
	$endDate   = getDateFormatDB($End_date);

	$sel_pro1 = $sel_pro_arr = $phyId;
	
	//--- GET PHYSICIAN NAME ---------
	$physicianNameArr = $CLSCommonFunction->drop_down_providers('','','','Array');
	if(count($sel_pro_arr) == 0){
		if(count($physicianNameArr)>0){
			$sel_pro_arr = array_keys($physicianNameArr);
		}
	}
	
	$sel_pro = join(',',$sel_pro_arr);
	$facilityId = join(',',$facility_name);

	if(empty($facilityId) === false){
		$fac_qry = "select fac_prac_code,name,id from facility where id  in($facilityId)";
		$fac_qry_res = imw_query($fac_qry);
		$pos_fac_id_arr=array();
		while($fac_row = imw_fetch_array($fac_qry_res)){
			if(empty($fac_row['fac_prac_code']) === false){
				$pos_fac_id_arr[] = $fac_row['fac_prac_code'];
			}
		}
		
		$pos_fac_id_str = join(',',array_unique($pos_fac_id_arr));
		unset($pos_fac_id_arr);
	}

	if($ExRescheduleAppts){	
		$varReschedule = ',202';
	}
		
	//---- GET APPOINTMENT STATUS OF SELECTED DATE -----------
	$app_query = "select sa_patient_id, DATE_FORMAT(sa_app_start_date,  '%m-%d-%Y') as 'appt_date' from schedule_appointments 
			WHERE (sa_app_start_date BETWEEN '$startDate' AND '$endDate') AND sa_patient_app_status_id NOT IN(201,18,3$varReschedule)";
	if(empty($facilityId) === false){
		$app_query .= " AND sa_facility_id in($facilityId)";
	}
	if(empty($sel_pro) === false){
		$app_query .= " AND sa_doctor_id IN($sel_pro)";
	}
	$appQryRes1 = array();
	$apquery = imw_query($app_query);
	while($rowapp = imw_fetch_assoc($apquery)){
		$appQryRes1[] = $rowapp;
	}

	$patientIdArr = array();
	$patientDateArr =array();
	for($i=0;$i<count($appQryRes1);$i++){
		$patientIdArr[$appQryRes1[$i]['sa_patient_id']] = $appQryRes1[$i]['sa_patient_id'];
		$patientDateArr[$appQryRes1[$i]['appt_date']] = $appQryRes1[$i]['appt_date'];
	}
	$patientIdStr = implode(",", $patientIdArr);

	// GET SUBMITTED RECORDS AND DATES
	$subRs=imw_query("Select encounter_id, patient_id, DATE_FORMAT(subRec.submited_date, '".get_sql_date_format()."') as 'submitted_date' 
	FROM submited_record subRec WHERE subRec.submited_date>= '$startDate'");
	while($subQryRes = imw_fetch_array($subRs)){
		$pid = $subQryRes['patient_id'];
		$eid =  $subQryRes['encounter_id'];
		$submittedDate = $subQryRes['submitted_date'];
		$tempSubmittedDate[$eid] = $submittedDate;
	}

	// PATIENT CHARGES AND SUBMITTED RECORDS	
	$arrSuBillPids  =array();
	$pt_chg = "select ptChg.totalAmt, ptChg.submitted, ptChg.encounter_id, ptChg.patient_id, DATE_FORMAT(ptChg.postedDate, '".get_sql_date_format()."') as 'postedDate',
	DATE_FORMAT(ptChg.date_of_service, '".get_sql_date_format()."') as 'date_of_service',
	spBill.encounterId, spBill.ascId, spBill.todaysCharges 
	from patient_charge_list ptChg  
	LEFT JOIN superbill spBill ON spBill.encounterId = ptChg.encounter_id 
	WHERE ((ptChg.postedDate BETWEEN '$startDate' AND '$endDate') 
	OR (ptChg.entered_date BETWEEN '$startDate' AND '$endDate') 
	OR (ptChg.Re_submitted_date BETWEEN '$startDate' AND '$endDate') 
	OR (ptChg.date_of_service BETWEEN '$startDate' AND '$endDate'))";

	if(empty($core_grp_id) === false){
		$strGrpIds = join(',',(array)$core_grp_id);
		$pt_chg .= " and ptChg.gro_id in ($strGrpIds)";
	}
	if($billStatus =='noSuperBill'){
		//$objManageData->QUERY_STRING.= " AND spBill.encounterId IS NULL";
	}
	if($enteredStatus =='chargesNotEntered'){
		//$objManageData->QUERY_STRING.= " AND ptChg.encounter_id IS NULL";
	}
	if($postedStatus =='chargesNotPosted'){
		//$objManageData->QUERY_STRING.= " AND ptChg.postedDate ='00-00-0000'";
	}
	if(empty($pos_fac_id_str) === false){
		$pt_chg .= " AND ptChg.facility_id in($pos_fac_id_str)";
	}
	if(empty($sel_pro) === false){
		$pt_chg .= " AND ptChg.primary_provider_id_for_reports IN($sel_pro)";
	}
	$pt_chg .= " ORDER BY ptChg.charge_list_id";
	
	$chargesQryRes = array();
	$ptchg = imw_query($pt_chg);
	while($rowPt = imw_fetch_assoc($ptchg)){
		$chargesQryRes[] = $rowPt;
	}
	$patientChargesDataArr = array();
	$encounterIdArr = array();
	$tempArrPids  =array();
	$arrSuBillEnc =array();
	$tempSBillPostArr = array();
	$totMissingEncounters = array();
	$tempSBillArr = array();

	for($i=0;$i<count($chargesQryRes);$i++){
		$enterInBlock =1;
		$makeArr=1;
		$patient_id = $chargesQryRes[$i]['patient_id'];
		$submitted = $chargesQryRes[$i]['submitted'];
		$totalAmt = $chargesQryRes[$i]['totalAmt'];
		$superBillAmt = $chargesQryRes[$i]['todaysCharges'];
		$date_of_service  = $chargesQryRes[$i]['date_of_service'];
		$enc_ID = $chargesQryRes[$i]['encounter_id'];


		if($billStatus=='noSuperBill' && $chargesQryRes[$i]['encounterId']!=NULL){ $enterInBlock=0; }
		if($postedStatus=='chargesNotPosted' && $chargesQryRes[$i]['postedDate']!='00-00-0000'){ $enterInBlock=0; }

		//CHECKING SUPERBILL
		if($superBillAmt>0 || $chargesQryRes[$i]['ascId']>0){
			$arrSuBillEnc[$enc_ID] = $enc_ID;
			$tempSBillArr[$patient_id]=$patient_id;
			//FOR SUMMARY
			$superbillCreated[$patient_id][$date_of_service] = $patient_id;
		}
		if($chargesQryRes[$i]['encounterId']==NULL){
			$summaryNoSBills[$patient_id][]=$patient_id;
		}
		
		
		if($enterInBlock=='1'){
			//SUBMITTED REOCRDS ARRAYS
			if($tempSubmittedDate[$enc_ID]){
				$submittedEncIds[] = $enc_ID;
				$arrCheckSubEnc[$enc_ID] = $patient_id;
			}
	
			$encounterIdArr[$enc_ID] = $enc_ID;
	
			if($submittedStatus=='chargesNotSubmitted' && $tempSubmittedDate[$enc_ID]!=''){ $makeArr=0; } 
			if($makeArr=='1'){
				$patientChargesDataArr[$patient_id][$date_of_service]['encounter_id_summary'] = $chargesQryRes[$i]['encounter_id'];
				$patientChargesDataArr[$patient_id][$date_of_service]['encounter_id'][] = $chargesQryRes[$i]['encounter_id'];
				$patientChargesDataArr[$patient_id][$date_of_service]['super_bill_encounter'][] = $chargesQryRes[$i]['encounterId'];					
				$patientChargesDataArr[$patient_id][$date_of_service]['super_bill_discharge'][] = $chargesQryRes[$i]['ascId'];					
				$patientChargesDataArr[$patient_id][$date_of_service]['super_bill_amount'][] = $superBillAmt;
			}
		
			if($chargesQryRes[$i]['encounterId']){
				$tempSBillPostArr[$patient_id] = $patient_id;
			}
			if($chargesQryRes[$i]['postedDate']!='00-00-0000'){
				$postedAmount[$enc_ID]=$totalAmt;
			}
			
			// NOT SUBMITTED AMOUNTS
			if($tempSubmittedDate[$enc_ID]==''){
				$patientNotSubArr[$patient_id][$date_of_service][$enc_ID] = $totalAmt;
			}
	
			$tempChargesArr[$patient_id] = $patient_id;
			$arrCheckPatEnc[$enc_ID] = $patient_id;
			$arrCheckEncounters[$patient_id] = $enc_ID;
			$arrCheckForSubRec[$enc_ID] = $patient_id;
			
			$arrEncounters[$enc_ID] = $date_of_service;
			$arrPatEncDOS[$patient_id][] = $date_of_service;
			$arrPatEncSize[$patient_id][$date_of_service][]=$enc_ID;
		}
		$arrPatEntered[$patient_id][] = $date_of_service;
	}
	
	//--- SUPER BILL (CHECK IF SUPER BILL EXISTS)
	$strSuBillEnc  = implode(",",$arrSuBillEnc);
 	$sb_qry = "Select patientId, encounterId, todaysCharges, patientId, ascId, DATE_FORMAT(dateOfService,'".get_sql_date_format()."') as 'dateOfService', DATE_FORMAT(patient_charge_list.date_of_service, '".get_sql_date_format()."') as 'date_of_service' 
	FROM superbill LEFT JOIN patient_charge_list ON patient_charge_list.encounter_id = superbill.encounterId 
	where (dateOfService BETWEEN '$startDate' AND '$endDate') 
	AND patient_charge_list.encounter_id IS NULL";
	if(sizeof($arrSuBillEnc)>0){
		$sb_qry.=" AND encounterId NOT IN(".$strSuBillEnc.")";
	}
	$sb_qry.=" ORDER BY superbill.idSuperBill";
	
	$superbillQryRes = array();
	$sbqry = imw_query($sb_qry);
	while($rowSB = imw_fetch_assoc($sbqry)){
		$superbillQryRes[] = $rowSB;
	}
	
	$superbillDataArr = array();
	$superbillEncArr = array();
	for($i=0;$i<count($superbillQryRes);$i++){
		$pid = $superbillQryRes[$i]['patientId'];
		$enc_ID = $superbillQryRes[$i]['encounterId'];
		$dateOfService = $superbillQryRes[$i]['dateOfService'];
		$date_of_service = $superbillQryRes[$i]['date_of_service'];

		if($billStatus !='noSuperBill'){
			$superbillEncArr[$pid][$dateOfService][] = $superbillQryRes[$i]['encounterId'];
			$superbillDataArr[$pid][$dateOfService][] = $superbillQryRes[$i]['todaysCharges'];
			$superbillDisArr[$pid][$dateOfService][] = $superbillQryRes[$i]['ascId'];
			$arrPatSBSize[$pid][$dateOfService][]=$pid;
		}
		
		//FOR SUMMARY
		$superbillCreated[$pid][$dateOfService] = $pid;
		$superbillNotProcessed[$pid][$dateOfService][] = $pid;
		
		$tempSBillArr[$pid] = $pid;
		$arrCheckPatEnc[$enc_ID]=$pid;
		$arrCheckEncounters[$pid] =$end_ID;

		$arrPatEncDOS[$pid][] = $dateOfService;
	}

	// REMOVE PATIENTS FROM ARRAY
	if(sizeof($tempChargesArr)>0){
		if($submittedStatus =='chargesNotSubmitted' || $postedStatus =='chargesNotPosted'){
			if($enteredStatus==""){
				$patientIdArr = array_intersect($patientIdArr, $tempChargesArr);	//SET ARRAY WITH NOT ARRAY 
				$patientIdArr = array_merge($patientIdArr, $tempSBillArr);	//SET ARRAY WITH NOT ARRAY 
				$patientIdArr = array_unique($patientIdArr);
			}
		}

		if($billStatus=='noSuperBill' && $enteredStatus!="chargesNotEntered" && $submittedStatus!='chargesNotSubmitted' && $postedStatus!='chargesNotPosted'){
			if(sizeof($tempChargesArr)>0){
				$tempArr = array_diff($tempChargesArr, $tempSBillArr);
			}else{
				$patientIdArr = array_diff($patientIdArr, $tempSBillArr);	//SET ARRAY WITH NOT ARRAY 
			}
		}
	}

	// REMOVE IF NOT SUBMITTED
	if(($submittedStatus =='chargesNotSubmitted' && sizeof($arrCheckSubEnc)>0) && ($enteredStatus!="chargesNotEntered" && $postedStatus!='chargesNotPosted')){
		$arrCheckForSubRec = array_diff_key($arrCheckForSubRec, $arrCheckSubEnc);
		$ss_keys= array_values($arrCheckForSubRec);
		array_unique($ss_keys);
		$patientIdArr=  array();
		for($j=0;$j<sizeof($ss_keys);$j++){
			$pid = $ss_keys[$j];
			if($pid!=''){
				$patientIdArr[$pid] =$pid; 
			}
		}
	}
	

	//GET DEFERRED INS, VIP, SELF PAY INFORATION OF ENCOUNTERS
	$arrDeferredInfo = array();
	if(sizeof($encounterIdArr)>0){
		$strEncIds=implode(',',$encounterIdArr);
		
		$qry = "SELECT patchg.encounter_id, patchg.vipStatus, patchg.primaryInsuranceCoId, patchg.secondaryInsuranceCoId, patchg.tertiaryInsuranceCoId, patchgdet.differ_insurance_bill 
		FROM patient_charge_list patchg 
		JOIN patient_charge_list_details patchgdet ON patchgdet.charge_list_id=patchg.charge_list_id 
		WHERE patchg.encounter_id IN(".$strEncIds.")";
		$rs = imw_query($qry);
		while($res = imw_fetch_assoc($rs)){

			if($res['vipStatus']=='true'){
				$arrDeferredInfo[$res['encounter_id']]['vip']='VIP';
			}
			if($res['differ_insurance_bill']=='true'){
				$arrDeferredInfo[$res['encounter_id']]['deferred_ins']='Deferred Ins.';
			}
			if($res['primaryInsuranceCoId']<=0 && $res['secondaryInsuranceCoId']<=0 && $res['tertiaryInsuranceCoId']<=0){
				$arrDeferredInfo[$res['encounter_id']]['self_pay']='Self Pay';
			}
		}	

	}
	//--- GET ALL PAYMENTS FOR SUMMARY-------
	if($reportProcess=='summary'){
		$encounterIdStr = implode(",", $encounterIdArr);
		$pymt_qry = "select patient_charges_detail_payment_info.paidForProc + patient_charges_detail_payment_info.overPayment
		as paidForProc, patient_chargesheet_payment_info.payment_mode,
		patient_chargesheet_payment_info.encounter_id
		from patient_charges_detail_payment_info join
		patient_chargesheet_payment_info on
		patient_chargesheet_payment_info.payment_id = patient_charges_detail_payment_info.payment_id 
		where (patient_charges_detail_payment_info.paidDate BETWEEN '$startDate' AND '$endDate') 
		and patient_chargesheet_payment_info.encounter_id IN($encounterIdStr) 
		and patient_charges_detail_payment_info.deletePayment != 1";
		$paymentQryRes = array();
		$pymtqry = imw_query($pymt_qry);
		while($rowpymt = imw_fetch_assoc($pymtqry)){
			$paymentQryRes[] = $rowpymt;
		}
		$paymentDataArr = array();
		for($i=0;$i<count($paymentQryRes);$i++){
			$encounter_id = $paymentQryRes[$i]['encounter_id'];
			$payment_mode = strtolower($paymentQryRes[$i]['payment_mode']);
			$paidForProc = $paymentQryRes[$i]['paidForProc'];
			$paymentDataArr[]  = $paidForProc;
		}
		$totPaidAmt = array_sum($paymentDataArr);
	}

	//GET ALL SUBMITTED ELECTRONIC FILES RELATED WITH ENCOUNTERS
	$arrSubmittedFiles = array();
	$arrCheckFiles=array();

	for($i=0; $i<sizeof($submittedEncIds); $i++){
		//$qrySubFile="Select Interchange_control, DATE_FORMAT(create_date, '".get_sql_date_format()."') as 'create_date' FROM batch_file_submitte WHERE FIND_IN_SET('".$submittedEncIds[$i]."', encounter_id) ORDER BY Batch_file_submitte_id";
		$qrySubFile="Select encounter_id, Interchange_control, DATE_FORMAT(create_date, '".get_sql_date_format()."') as 'create_date' FROM batch_file_submitte WHERE encounter_id LIKE '%".$submittedEncIds[$i]."%' ORDER BY Batch_file_submitte_id";
		$rsSubFile = imw_query($qrySubFile);
		while($resSubFile = imw_fetch_array($rsSubFile)){
			
			//RE-CHECKING EXACT ENCOUNTER_ID
			if(preg_match("~\b".$submittedEncIds[$i]."\b~", $resSubFile['encounter_id']) ){
			
			$InterchangeControlNumber = $Interchange_controls = '';
				$addSpaces = NULL;
				$Interchange_controls = $resSubFile['Interchange_control'];
				$cont_length = (4 - strlen($Interchange_controls));
				for($a=0;$a<$cont_length;$a++){
					$addSpaces .= '0';
				}
				$InterchangeControlNumber = $addSpaces.$Interchange_controls;	
				if(array_key_exists($submittedEncIds[$i], $arrSubmittedFiles)){
					if(!in_array($Interchange_controls, $arrCheckFiles[$submittedEncIds[$i]])){
						$arrCheckFiles[$submittedEncIds[$i]][] = $Interchange_controls;
						$arrSubmittedFiles[$submittedEncIds[$i]].= ", ".$InterchangeControlNumber.' - '.$resSubFile['create_date'];
					}
				}else{
					$arrSubmittedFiles[$submittedEncIds[$i]]= $InterchangeControlNumber.' - '.$resSubFile['create_date'];
					$arrCheckFiles[$submittedEncIds[$i]][] = $Interchange_controls;			
				}
			
			}
		}
	}

	$patientIdStr = implode(",", $patientIdArr);
	$qryPart='';
	if($patientIdStr!=''){
		$qryPart =" AND schedule_appointments.sa_patient_id IN(".$patientIdStr.")";
	}
	//---- GET SCHEDULED APPOINTMENTS OF SELECTED DATE -----------
	$app_qry = "select schedule_appointments.sa_patient_app_status_id, 
	schedule_appointments.id, schedule_appointments.sa_facility_id, facility.name, 
	schedule_appointments.sa_patient_id, DATE_FORMAT(schedule_appointments.sa_app_start_date,'".get_sql_date_format()."') as 'sa_app_startdate', schedule_appointments.sa_app_starttime, 
	schedule_appointments.sa_app_endtime , schedule_appointments.sa_app_duration,
	schedule_appointments.sa_doctor_id, patient_data.lname,patient_data.fname,patient_data.mname, patient_data.phone_home,
	slot_procedures.proc 
	FROM schedule_appointments left join slot_procedures on
	slot_procedures.id = schedule_appointments.procedureId
	LEFT JOIN patient_data on schedule_appointments.sa_patient_id = patient_data.id 
	LEFT JOIN facility on schedule_appointments.sa_facility_id = facility.id 
	JOIN users on users.id = schedule_appointments.sa_doctor_id 
	WHERE sa_patient_app_status_id NOT IN(201,18,3$varReschedule) 
	AND ((schedule_appointments.sa_app_start_date BETWEEN '$startDate' AND '$endDate') $qryPart)";
		$app_qry .= " ";
	if(empty($facilityId) === false){
		$app_qry .= " AND schedule_appointments.sa_facility_id in($facilityId)";
	}
	if(empty($sel_pro) === false){
		$app_qry .= " AND schedule_appointments.sa_doctor_id IN($sel_pro)";
	}	
    $app_qry .= " ORDER BY users.lname, facility.name, schedule_appointments.sa_app_start_date, schedule_appointments.sa_app_starttime,
				patient_data.lname,patient_data.fname";
	$appQryRes = array();
	$appqry = imw_query($app_qry);
	while($rowapp = imw_fetch_assoc($appqry)){
		$appQryRes[] = $rowapp;
	}
	
	$appointmentDataArr = array();
	$sidArr = array();
	if($totMissingEnc ==0) {
		$totMissingEnc = count($appQryRes);
	}
	$tempChkNS=array();

	for($i=0;$i<count($appQryRes);$i++){
		$isEncOrSB=0;
		$sa_doctor_id = $appQryRes[$i]['sa_doctor_id'];
		$sa_facility_id = $appQryRes[$i]['sa_facility_id'];
		$pat_id =$appQryRes[$i]['sa_patient_id'];
		$sa_app_startdate_tr = $appQryRes[$i]['sa_app_startdate'];
		$arrFacilityNames[$sa_facility_id] = $appQryRes[$i]['name'];
		
		if($reportProcess=='detail'){
			$encStatusStr = implode(',', $enctStatusArr);
			if(empty($encStatusStr)===true){	
				$arrDD[$sa_doctor_id]=$sa_doctor_id;
				$appointmentDataArr[$sa_doctor_id][$sa_facility_id][] = $appQryRes[$i];
				$sidArr[] = $appQryRes[$i]['id'];
			}else{
				if($enteredStatus == ""){
					if(in_array($sa_app_startdate_tr, $arrPatEncDOS[$pat_id])){
						$sa_doctor_id = $appQryRes[$i]['sa_doctor_id'];
						$appointmentDataArr[$sa_doctor_id][$sa_facility_id][] = $appQryRes[$i];
						$sidArr[] = $appQryRes[$i]['id'];
					}
				}else{

					$makeArr=0;
					if($enteredStatus=="chargesNotEntered" && !in_array($sa_app_startdate_tr, $arrPatEntered[$pat_id])){
						$makeArr=1;
					}
					if($postedStatus=="chargesNotPosted" && in_array($sa_app_startdate_tr, $arrPatEncDOS[$pat_id])){
						if($enteredStatus=="chargesNotEntered"){
							if(!in_array($sa_app_startdate_tr, $arrPatEntered[$pat_id])){
								$makeArr=1;
							}
						}else{
							$makeArr=1;
						}
					}
					if($submittedStatus=="chargesNotSubmitted" && in_array($sa_app_startdate_tr, $arrPatEncDOS[$pat_id])){
						if($enteredStatus=="chargesNotEntered"){
							if(!in_array($sa_app_startdate_tr, $arrPatEntered[$pat_id])){
								$makeArr=1;
							}
						}else{
							$makeArr=1;
						}
					}
					if($billStatus=='noSuperBill'){
						if($tempSBillArr[$pat_id]){
							$makeArr=0;
						}
						if(in_array($pat_id,$summaryNoSBills[$pat_id])){
							if($enteredStatus=="chargesNotEntered"){
								if(!in_array($sa_app_startdate_tr, $arrPatEntered[$pat_id])){
									$makeArr=1;
								}
							}else{
								$makeArr=1;
							}
						}
					}
					
					
					if($makeArr=='1'){
						$sa_doctor_id = $appQryRes[$i]['sa_doctor_id'];
						$appointmentDataArr[$sa_doctor_id][$sa_facility_id][] = $appQryRes[$i];
						$sidArr[] = $appQryRes[$i]['id'];
					}
				}
			}
			$arrPatApptSize[$pat_id][$sa_app_startdate_tr][]=$pat_id;
		}else{

			$appointmentDataArr[$sa_doctor_id][$sa_facility_id] = $sa_facility_id;

			//SUPERBILLS CREATED
			if($superbillCreated[$pat_id][$sa_app_startdate_tr]){
				$superbillCreate[$sa_doctor_id][$sa_facility_id][]=$pat_id;
				
				$superbillCreated_temp[$appQryRes[$i]['id']] = $appQryRes[$i]['id'];
				unset($superbillCreated[$pat_id][$sa_app_startdate_tr]);
			}
			//SUPERBILLS NOT PROCESSED
			if($superbillNotProcessed[$pat_id][$sa_app_startdate_tr]){
				$superbillNotProc[$sa_doctor_id][$sa_facility_id][]=count($superbillNotProcessed[$pat_id][$sa_app_startdate_tr]);
			}

			$sidArr[] = $appQryRes[$i]['id'];
			$tempSchData[$appQryRes[$i]['id']]['doctor'] = $sa_doctor_id;
			$tempSchData[$appQryRes[$i]['id']]['facility'] = $sa_facility_id;
		}
	}

 	$summaryTotEnc = count($arrSummaryTotEnc);
	$sidStr = join(',',$sidArr);
	
	//GET CI TIME
	$arrCheckInData=array();
	if(empty($sidStr)==false){
		//$qry="Select sch_id,created_time FROM check_in_out_payment WHERE sch_id IN(".$sidStr.")";
		$qry="Select patient_id, sch_id, status, status_time, DATE_FORMAT(status_date,'".get_sql_date_format()."') as 'status_date' 
		 FROM previous_status WHERE sch_id IN(".$sidStr.") AND status IN(11,13) ORDER BY status DESC";
		$rs=imw_query($qry);
		while($res = imw_fetch_array($rs)){
			if($res['status']==13){
				$arrCheckInData[$res['sch_id']]['check_in'] = $res['status_time'];
			}else{
				$arrCheckInData[$res['sch_id']]['check_out'] = $res['status_time'];
			}
			
			//TOTAL PATIENTS SEEN
			$sa_doctor_id = $tempSchData[$res['sch_id']]['doctor'];
			$sa_facility_id = $tempSchData[$res['sch_id']]['facility'];
			$arrSummarySeen[$sa_doctor_id][$sa_facility_id][$res['sch_id']]= $res['sch_id'];		
			
			//SUPER BILL NOT CREATED
			if(!$superbillCreated_temp[$res['sch_id']]){
				$superbillNotCreate[$sa_doctor_id][$sa_facility_id][]=$res['patient_id'];
				$superbillCreated_temp[$res['sch_id']]=$res['sch_id'];
			}
			
		}unset($rs);
	}
	
	if(count($appointmentDataArr) > 0){
		$printFile = true;
		$php_date_format=phpDateFormat();	
		$curDate = date($php_date_format.' H:i A');
		$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
		$op_name = $op_name_arr[1][0];
		$op_name .= $op_name_arr[0][0];
		
		$stDate = explode("-", $startDate);
		$startDate1 = date($php_date_format,mktime(0,0,0,$stDate[1],$stDate[2],$stDate[0]));//$stDate[1].'-'.$stDate[2].'-'.$stDate[0];	
		
		$enDate = explode("-", $endDate);
		$endDate1 = date($php_date_format,mktime(0,0,0,$enDate[1],$enDate[2],$enDate[0]));//$enDate[1].'-'.$enDate[2].'-'.$enDate[0];	
		
		
		$selGrp = 'All';
		if(empty($core_grp_id)==false){ $arrGrp=explode(',',$core_grp_id);}
		if(count($arrGrp)==$core_drop_groups_exp){
			$selGrp = 'All';
		}else if(count($arrGrp)>1){
			$selGrp="Multi";
		}else if(count($arrGrp)>0){
			$rs=imw_query("Select name FROM groups_new WHERE gro_id='".$arrGrp[0]."'");
			$res=imw_fetch_array($rs);
			$selGrp = $res['name'];
		}

		$selPhy = 'All';
		if(count($sel_pro1)==$PHY_NAME_exp){
			$selPhy = 'All';
		}else if(count($sel_pro1)>1){
			$selPhy="Multi";
		}else if(count($sel_pro1)>0 && $sel_pro1[0]!=''){
			$selPhy = ucfirst(trim($physicianNameArr[$sel_pro1[0]]));
		}
		
		$selFac = 'All';
		if(empty($facilityId)==false){ $arrFac=explode(',',$facilityId);}
		if(count($arrFac)==$facilityName_exp){
			$selFac = 'All';
		}else if(count($arrFac)>1){
			$selFac="Multi";
		}else if(count($arrFac)>0){
			$selFac = $arrFacilityNames[$arrFac[0]];
		}
		$enctStatus_exp=explode(',',$enctStatus);
		$sel_enc_status="All";
		if(count($enctStatus_exp)==4){
			$sel_enc_status="All";
		}else if(count($enctStatus_exp)>1){
			$sel_enc_status="Multi";
		}else if(count($enctStatus_exp)>0 && $enctStatus_exp[0]!=""){
			if($enctStatus_exp[0]=="noSuperBill"){
				$sel_enc_status = "No Superbill";
			}
			if($enctStatus_exp[0]=="chargesNotEntered"){
				$sel_enc_status = "Charges Not Entered";
			}
			if($enctStatus_exp[0]=="chargesNotPosted"){
				$sel_enc_status = "Charges Not Posted";
			}
			if($enctStatus_exp[0]=="chargesNotSubmitted"){
				$sel_enc_status = "Charges Not Submitted";
			}
		}
		$curDate = date(phpDateFormat().' H:i A');
		$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
		$op_name = $op_name_arr[1][0];
		$op_name .= $op_name_arr[0][0];
		
		//MAKING OUTPUT DATA
		$file_name='unapplied_superbills.csv';
		$csv_file_name= write_html("", $file_name);
		if(file_exists($csv_file_name)){
			unlink($csv_file_name);
		}
		$fp = fopen ($csv_file_name, 'a+');
		
		$arr=array();	
		$arr[]='Unapplied Superbills :'.$reportProcess;
		$arr[]='Selected Date : '.$startDate1.' To '.$endDate1;
		$arr[]='Created by: '.$op_name.' on '.$curDate;
		fputcsv($fp,$arr, ",","\"");
		$arr=array();	
		$arr[]='Selected Group : '.$selGrp;
		$arr[]='Selected Facility : '.$selFac;
		$arr[]='Selected Physician : '.$selPhy;
		$arr[]='Encounter Status : '.$sel_enc_status;
		fputcsv($fp,$arr, ",","\"");
		require_once('missing_encounters_'.$reportProcess.'.php');
		
	}
}

$op = 'l';
$HTMLCreated=0;
if($printFile == true and $csv_data != ''){
	$HTMLCreated=1;
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$csv_data;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$strHTML;

	$file_location = write_html($strHTML, 'unapplied_superbills.html');
}else{
	$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
}

if($output_option=='view' || $output_option=='output_csv'){
	if($callFrom!='scheduled'){	
		echo $csv_file_data;	
	}
}
?>