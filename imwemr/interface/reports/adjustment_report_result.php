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
//require_once('../../library/classes/class.reports.php');
//$CLSReports = new CLSReports;
ini_set('memory_limit', '592M'); 
$without_pat = "yes";
require_once("reports_header.php");
$dateFormat = get_sql_date_format();
$phpDateFormat = phpDateFormat();
$curDate = date($phpDateFormat . ' h:i A');
$FCName = $_SESSION['authId'];
$page_data = NULL;
$pdf_data = NULL;
$curDate = date($phpDateFormat);
if ($Start_date == "") {
    $Start_date = $curDate;
    $End_date = $curDate;
}

$operater_name = array();
$sel_opr_rs = imw_query("select fname,lname,id from users");
while ($row_opr = imw_fetch_array($sel_opr_rs)) {
    $id = $row_opr['id'];
    if ($row_opr['lname'] == "" && $row_opr['fname'] == "") {
        $operater_name[$row_opr['id']] = "Not Defined";
    } else {
        $operater_name[$row_opr['id']] = core_name_format($row_opr['lname'], $row_opr['fname'], $row_opr['mname']);
    }
}

$op_name_arr = preg_split('/, /', strtoupper($_SESSION['authProviderName']));
$op_name = $op_name_arr[1][0];
$op_name .= $op_name_arr[0][0];
//$op_name = $_SESSION['authUser'];
$reptByForFun = 'dot';

$curDate = date($phpDateFormat . ' h:i A');

$printFile = true;
$processReport = strtolower($summary_detail);
if (empty($Submit) === false) {
    $printFile = false;

    //DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
    $arrDateRange = $CLSCommonFunction->changeDateSelection();

    if ($dayReport == 'Daily') {
        $Start_date = $End_date = date($phpDateFormat);
    } else if ($dayReport == 'Weekly') {
        $Start_date = $arrDateRange['WEEK_DATE'];
        $End_date = date($phpDateFormat);
    } else if ($dayReport == 'Monthly') {
        $Start_date = $arrDateRange['MONTH_DATE'];
        $End_date = date($phpDateFormat);
    } else if ($dayReport == 'Quarterly') {
        $Start_date = $arrDateRange['QUARTER_DATE_START'];
        $End_date = $arrDateRange['QUARTER_DATE_END'];
    }

    //--- CHANGE DATE FORMAT ----
    $StartDate = getDateFormatDB($Start_date);
    $EndDate = getDateFormatDB($End_date);

    if (empty($batchFiles) == false) {
        $StartDate = $EndDate = '';
    }

    $grp_id = (sizeof($groups) > 0) ? implode(',', $groups) : '';
    $sc_name = (sizeof($facility_name) > 0) ? implode(',', $facility_name) : '';
    $phyId = (sizeof($phyId) > 0) ? implode(',', $phyId) : '';
    $operator = (sizeof($operator_id) > 0) ? implode(',', $operator_id) : '';

    $selGrpDisp = $CLSReports->report_display_selected($groups, 'group', 1, $grp_cnt);
    $selFacDisp = $CLSReports->report_display_selected($sc_name, 'facility', 1, $posfac_cnt);
    $selPhyDisp = $CLSReports->report_display_selected($phyId, 'physician', 1, $phy_cnt);
	$sel_opr = $CLSReports->report_display_selected($operator, 'operator', 1, $opr_cnt);
    $arrOprTemp = explode(",", $operator);
    if (sizeof($arrOpr) > 0) {
        $arrOpr = array_combine($arrOprTemp, $arrOprTemp);
    }
    unset($arrOprTemp);

    //GET ALL USERS
    $qry = "Select id,lname,fname FROM users";
    $rs = imw_query($qry);
    $userNameTwoCharArr = array();
    while ($res = imw_fetch_array($rs)) {
        $id = $res['id'];
        $operatorInitial = '';

        $operatorInitial = substr($res['fname'], 0, 1);
        $operatorInitial .= substr($res['lname'], 0, 1);
        $userNameTwoCharArr[$id] = strtoupper($operatorInitial);
    }
	
	//GET SCHEDULE FACILITIES
	$arrPosFacOfSchFac=array();
	$arrSchFacilites=array();
	$arrSchFacilites[0]='No Pay Location';
	$rs=imw_query("Select id, name, fac_prac_code FROM facility");	
	while($res=imw_fetch_assoc($rs)){
		$arrPosFacOfSchFac[$res['id']] = $res['fac_prac_code'];
		$arrSchFacilites[$res['id']] = $res['name'];
	}unset($rs);	

    $str_wrt_code = implode(',',$wrt_code);
    if (trim($wrt_code) != "") {
        $wrt_code = explode(',', $wrt_code);
    } 
    $w_code = array();
    $d_code = array();
    $a_code = array();
    $r_code = array();
    for ($i = 0; $i < count($wrt_code); $i++) {
        $value = substr($wrt_code[$i], 0, 3);
        if ($value == "WC_") {
            $w_code[] = str_replace('WC_', '', $wrt_code[$i]);
        } else if ($value == "DC_") {
            $d_code[] = str_replace('DC_', '', $wrt_code[$i]);
        } else if ($value == "AC_") {
            $a_code[] = str_replace('AC_', '', $wrt_code[$i]);
        } else if ($value == "RC_") {
            $r_code[] = str_replace('RC_', '', $wrt_code[$i]);
        }
    }

    if (sizeof($w_code) > 0) {
        $w_code = array_combine($w_code, $w_code);
    }
    $write_off = implode(',', $w_code);
    $discount = implode(',', $d_code);
    $adjustment = implode(',', $a_code);
    $reason = implode(',', $r_code);
    $disOrAdjSel = (sizeof($d_code) > 0 || sizeof($a_code) > 0) ? true : false;

    $dtRangeFor = $DateRangeFor;
    $qryRange = 'transaction_date';
    if ($dtRangeFor == 'dos') {
        $qryRange = 'date_of_service';
    }

    $chld_arr = array();
    $arrEncounters = array();

    //	GET DOE PATIENT ID's
    $arrDoePats = array();
    $strDoePats = '';
    $qryDoe = "Select id from patient_data WHERE trim(lname)='doe'";
    $rsDoe = imw_query($qryDoe);
    while ($resDoe = imw_fetch_array($rsDoe)) {
        $arrDoePats[$resDoe['id']] = $resDoe['id'];
    }
    $strDoePats = implode(',', $arrDoePats);


    //--- WRITE OFF CODE ---- 
    $write_off_qry = "select w_id, w_code from write_off_code";
    if (count($w_code) > 0) {
        $write_off_qry .= " where w_id in ($write_off)";
    }
    $write_off_code_rs = imw_query($write_off_qry);
    $write_off_code_arr = array();
    if (imw_num_rows($write_off_code_rs) > 0) {
        while ($write_off_code_res = imw_fetch_array($write_off_code_rs)) {
            $w_id = $write_off_code_res['w_id'];
            $write_off_code_arr[$w_id] = $write_off_code_res['w_code'];
        }
    }
    //--- Discount CODE ---- 
    $dis_off_qry = "select d_id,d_code from discount_code";
    if (count($d_code) > 0) {
        $dis_off_qry .= " where d_id in ($discount)";
    }
    $dis_off_code_rs = imw_query($dis_off_qry);
    $dis_off_code_arr = array();
    if (imw_num_rows($dis_off_code_rs) > 0) {
        while ($dis_off_code_res = imw_fetch_array($dis_off_code_rs)) {
            $d_id = $dis_off_code_res['d_id'];
            $dis_off_code_arr[$d_id] = $dis_off_code_res['d_code'];
        }
    }
    //--- Adj OFF CODE ---- 
    $adj_off_qry = "select a_id,a_code from adj_code";
    if (count($a_code) > 0) {
        $adj_off_qry .= " where a_id in ($adjustment)";
    }
    $adj_off_code_rs = imw_query($adj_off_qry);
    $adj_off_code_arr = array();
    if (imw_num_rows($adj_off_code_rs) > 0) {
        while ($adj_off_code_res = imw_fetch_array($adj_off_code_rs)) {
            $a_id = $adj_off_code_res['a_id'];
            $adj_off_code_arr[$a_id] = $adj_off_code_res['a_code'];
        }
    }
	
	//--- Reason OFF CODE ---- 
    $reas_off_qry = "select cas_id,cas_code from cas_reason_code";
    if (count($r_code) > 0) {
        $reas_off_qry .= " where cas_id in ($reason)";
    }
    $reas_off_code_rs = imw_query($reas_off_qry);
    $reason_off_code_arr = $reas_off_code_arr = array();
    if (imw_num_rows($reas_off_code_rs) > 0) {
        while ($reas_off_code_res = imw_fetch_array($reas_off_code_rs)) {
            $cas_id = $reas_off_code_res['cas_id'];
            $reason_off_code_arr['res_'.$cas_id] = $reas_off_code_res['cas_code'];
            $reas_off_code_arr[$cas_id] = $reas_off_code_res['cas_code'];
        }
    }
	
	// IF SEARCH BY BATCH FILES
    $arrBatchIds = array();
    $batchQryEnc = '';
    if (empty($batchFiles) == false) {
        $arrTrackingNos = explode(',', $batchFiles);
        foreach ($arrTrackingNos as $val) {
            $trackNo = "'" . trim($val) . "'";
            $tempArr[$trackNo] = $trackNo;
        }
        $batchFilesSearch = implode(',', $tempArr);
        $qry = "Select batch_id FROM manual_batch_file WHERE tracking IN(" . $batchFilesSearch . ") AND del_status='0' AND post_status='1'";
        $rs = imw_query($qry);
        while ($res = imw_fetch_array($rs)) {
            $arrBatchIds[$res['batch_id']] = $res['batch_id'];
        }
        $strBatchIds = implode(',', $arrBatchIds);
    }

    $arrDoePats = array();
    $arrPatientId = $arrFacilityId = $arrProviderId = $arrOprId = $arrProcedureId = $arrChld = $arrEncounters = array();
    $arrFinalWriteOffCode = array();

	// (JOIN OF PATIENT DATA TABLE ADDED SO THAT PATIENT SHOULD NOT FETCHED THAT HAS NOT EXIST IN PATIENT DATA TABLE.)
	
    // WRITE-OFF CODE
    if (count($w_code) > 0 || count($wrt_code) == 0) {
        $sel_write_qry = "SELECT write_off_amount, 
								paymentswriteoff.encounter_id,
								paymentswriteoff.patient_id, 
								paymentswriteoff.charge_list_detail_id, 
								paymentswriteoff.write_off_code_id, 
								patient_charge_list.facility_id, 
								patient_charge_list.billing_facility_id, 
								patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId', 
								paymentswriteoff.write_off_operator_id, 
								date_format(paymentswriteoff.entered_date, '" . $dateFormat . "') as write_off_dot,
								paymentswriteoff.paymentStatus, patient_charge_list.del_status  
						FROM paymentswriteoff 
						JOIN patient_charge_list ON paymentswriteoff.encounter_id = patient_charge_list.encounter_id 
						JOIN patient_data pd ON pd.id=patient_charge_list.patient_id
						WHERE paymentswriteoff.write_off_amount > '0' 
							AND LOWER(paymentswriteoff.paymentStatus) != 'discount'
  						    AND paymentswriteoff.charge_list_detail_id != 0 
						";

        if ($dtRangeFor == "dos") {
            $sel_write_qry .= " AND paymentswriteoff.delStatus ='0'";
        } else {
            $sel_write_qry .= " AND ((paymentswriteoff.delStatus='0') OR (paymentswriteoff.delStatus='1' AND paymentswriteoff.write_off_del_date >'$EndDate')) 
								AND (patient_charge_list.del_status='0' OR (patient_charge_list.del_status='1' AND DATE_FORMAT(patient_charge_list.trans_del_date, '%Y-%m-%d')>'$EndDate'))";			
        }
        if (empty($strDoePats) == false) {
            //$sel_write_qry.=" AND patient_charge_list.patient_id NOT IN(".$strDoePats.")";
        }
        if ($StartDate != '' && $EndDate != '') {
            if ($dtRangeFor == "dos") {
                $sel_write_qry .= " and patient_charge_list.date_of_service between '$StartDate' and '$EndDate'";
            } else {
                $sel_write_qry .= " and (DATE_FORMAT(paymentswriteoff.entered_date, '%Y-%m-%d') between '$StartDate' and '$EndDate')";
            }
        }
        if (sizeof($arrBatchIds) > 0) {
            $sel_write_qry .= " and paymentswriteoff.batch_id IN ($strBatchIds)";
        }
        if (empty($write_off) == false) {
            $sel_write_qry .= " and paymentswriteoff.write_off_code_id IN ($write_off)";
        }
        if (empty($grp_id) === false) {
            $sel_write_qry .= " and patient_charge_list.gro_id IN ($grp_id)";
        }
        if (empty($sc_name) === false) {
			if($billing_location=='1'){
				$sel_write_qry.= " and patient_charge_list.billing_facility_id IN ($sc_name)";
			}else{	
				$sel_write_qry .= " and patient_charge_list.facility_id IN ($sc_name)";
			}
        }
        if (empty($phyId) === false) {
            $sel_write_qry .= " and patient_charge_list.primary_provider_id_for_reports IN ($phyId)";
        }
        if (empty($operator) === false) {
            $sel_write_qry .= " and  paymentswriteoff.write_off_operator_id IN ($operator) ORDER BY patient_charge_list.del_status ASC";
        }
        		
        $write_off_res = imw_query($sel_write_qry);
        $arrWriteOffCode = $arrCopayEnc = array();
        $tempArrEncSts = array();
        while ($write_off_res_new = imw_fetch_array($write_off_res)) {
            $encId = $write_off_res_new['encounter_id'];

            if (!($tempArrEncSts[$encId]) || $tempArrEncSts[$encId] == $write_off_res_new['del_status']) { //CHECK ADDED TO AVOID DUPLICATE ENCOUNTER IN CASE ONE IS DELETED.			
                $tempArrEncSts[$encId] = $write_off_res_new['del_status'];

                $writeOperatorId = $write_off_res_new['write_off_operator_id'];
                $writeCodeId = $write_off_res_new['write_off_code_id'];
                $providerId = $write_off_res_new['primaryProviderId'];
                $write_off_dot = $write_off_res_new['write_off_dot'];
                $facilityId = ($billing_location=='1') ? $write_off_res_new['billing_facility_id'] : $write_off_res_new['facility_id'];	
                $patientId = $write_off_res_new['patient_id'];

                $chld = $write_off_res_new['charge_list_detail_id'];

                /* if($chld == 0){
                  $arrCopayEnc[$encId]['spl'][] = $write_off_res_new;
                  $arrCopayEnc[$encId]['providerId'] = $providerId;
                  $arrCopayEnc[$encId]['facilityId'] = $facilityId;
                  $arrCopayEnc[$encId]['patientId'] = $patientId;
                  $arrCopayEnc[$encId]['writeCodeId'] = $writeCodeId;
                  }else{ */
                $arrWriteOffCode[$writeCodeId][$writeOperatorId][$providerId][$facilityId][$patientId][$encId][$chld]['spl'][] = $write_off_res_new;
                //}
                $arrWriteOffCode[$writeCodeId][$writeOperatorId][$providerId][$facilityId][$patientId][$encId]['write_off'] += $write_off_res_new['write_off_amount'];
                $arrWriteOffCode[$writeCodeId][$writeOperatorId][$providerId][$facilityId][$patientId][$encId]['write_off_dot'] = $write_off_res_new['write_off_dot'];
                $arrWriteOffCode[$writeCodeId][$writeOperatorId][$providerId][$facilityId]['write_off'] += $write_off_res_new['write_off_amount'];
                 $arrWriteOffCode['code'][$writeCodeId] = $writeCodeId;
                $arrWriteOffCode['woff_operator'][$writeOperatorId] = $writeOperatorId;
				$arrWriteOffCode['opr_id'][$chld][$writeCodeId]=$writeOperatorId;
				
				
                //if($chld != 0){
                $arrWriteOffCode['charge_list_detail_id'][$chld]['code'][$writeCodeId] = $writeCodeId;
                //}
                $arrEncounters[$encId] = $encId;
                $arrPatientId[$patientId] = $patientId;
                $arrFacilityId[$facilityId] = $facilityId;
                $arrProviderId[] = $providerId;
                $arrOprId[$write_off_res_new['write_off_operator_id']] = $write_off_res_new['write_off_operator_id'];
                //if($chld != 0)
                $arrChld[$chld] = $chld;
            }
        }
    }unset($write_off_res);

    //--------BEGIN GET CHLD ID FOR COPAY APPLIED ENCOUNTERS-------------
    /* $sql = "SELECT pcld.charge_list_detail_id, 
      pcl.encounter_id
      FROM patient_charge_list pcl
      JOIN patient_charge_list_details pcld ON pcld.charge_list_id = pcl.charge_list_id
      WHERE pcl.encounter_id IN (".implode(",",$arrCopayEnc).")
      AND pcld.coPayAdjustedAmount = 1
      ";
      $copay_enc_res = $objManageData->getQryRes($sql);
      for($i=0;$i<count($copay_enc_res);$i++){
      $chld = $copay_enc_res[$i]['charge_list_detail_id'];
      $encId = $copay_enc_res[$i]['encounter_id'];
      $writeCodeId = $arrCopayEnc[$encId]['writeCodeId'];
      $providerId = $arrCopayEnc[$encId]['providerId'];
      $facilityId = $arrCopayEnc[$encId]['facilityId'];
      $patientId = $arrCopayEnc[$encId]['patientId'];
      $spl = $arrCopayEnc[$encId]['spl'];
      $detailArr = array();
      foreach($spl as $arr){
      $arr['charge_list_detail_id'] = $chld;
      $detailArr[] = $arr;
      }

      $arrWriteOffCode[$writeCodeId][$providerId][$facilityId][$patientId][$encId][$chld]['spl'][] = $detailArr;
      $arrWriteOffCode['charge_list_detail_id'][$chld]['code'][$writeCodeId] = $writeCodeId;
      $arrChld[$chld] = $chld;
      } */
    //--------ENDGET CHLD ID FOR COPAY APPLIED ENCOUNTERS-------------
    //DISCOUNT CODE
    if (count($d_code) > 0 || count($wrt_code) == 0) {
        $sel_write_qry = "SELECT paymentswriteoff.write_off_amount, 
								paymentswriteoff.encounter_id,
								paymentswriteoff.patient_id, 
								paymentswriteoff.charge_list_detail_id, 
								paymentswriteoff.write_off_code_id, 
								patient_charge_list.facility_id, 
								patient_charge_list.billing_facility_id, 
								patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId', 
								paymentswriteoff.write_off_operator_id, 
								date_format(paymentswriteoff.entered_date, '" . $dateFormat . "') as write_off_dot,
								paymentswriteoff.paymentStatus, patient_charge_list.del_status  		
							FROM paymentswriteoff 
							JOIN patient_charge_list on paymentswriteoff.encounter_id = patient_charge_list.encounter_id
							JOIN patient_data pd ON pd.id=patient_charge_list.patient_id
							WHERE paymentswriteoff. write_off_amount > '0' 
								AND LOWER(paymentStatus)='discount'";
        if ($dtRangeFor == "dos") {
            $sel_write_qry .= " AND paymentswriteoff.delStatus ='0'";
        } else {
            $sel_write_qry .= " AND ((paymentswriteoff.delStatus='0') OR (paymentswriteoff.delStatus='1' AND paymentswriteoff.write_off_del_date >'$EndDate'))
								AND (patient_charge_list.del_status='0' OR (patient_charge_list.del_status='1' AND DATE_FORMAT(patient_charge_list.trans_del_date, '%Y-%m-%d')>'$EndDate'))";
        }
        if (empty($strDoePats) == false) {
            //$sel_write_qry.=" AND patient_charge_list.patient_id NOT IN(".$strDoePats.")";
        }
        if ($StartDate != '' && $EndDate != '') {
            if ($dtRangeFor == "dos") {
                $sel_write_qry .= " and patient_charge_list.date_of_service between '$StartDate' and '$EndDate'";
            } else {
                $sel_write_qry .= " and (DATE_FORMAT(paymentswriteoff.entered_date, '%Y-%m-%d') between '$StartDate' and '$EndDate')";
            }
        }
        if (sizeof($arrBatchIds) > 0) {
            $sel_write_qry .= " and paymentswriteoff.batch_id IN ($strBatchIds)";
        }
        if (empty($discount) == false) {
            $sel_write_qry .= " and paymentswriteoff.write_off_code_id IN ($discount)";
        }
        if (empty($grp_id) === false) {
            $sel_write_qry .= " and patient_charge_list.gro_id IN ($grp_id)";
        }
        if (empty($sc_name) === false) {
			if($billing_location=='1'){
				$sel_write_qry.= " and patient_charge_list.billing_facility_id IN ($sc_name)";
			}else{	
				$sel_write_qry .= " and patient_charge_list.facility_id IN ($sc_name)";
			}
        }
        if (empty($phyId) === false) {
            $sel_write_qry .= " and patient_charge_list.primary_provider_id_for_reports IN ($phyId)";
        }
        if (empty($operator) === false) {
            $sel_write_qry .= " and  paymentswriteoff.write_off_operator_id IN ($operator) ORDER BY patient_charge_list.del_status ASC";
        }
        //echo $sel_write_qry ;
        $write_off_res = imw_query($sel_write_qry);
        $arrDisData = array();
        $tempArrEncSts = array();
        while ($write_off_res_new = imw_fetch_array($write_off_res)) {
            $encId = $write_off_res_new['encounter_id'];

            if (!($tempArrEncSts[$encId]) || $tempArrEncSts[$encId] == $write_off_res_new['del_status']) { //CHECK ADDED TO AVOID DUPLICATE ENCOUNTER IN CASE ONE IS DELETED.			
                $tempArrEncSts[$encId] = $write_off_res_new['del_status'];

                $writeOperatorId = $write_off_res_new['write_off_operator_id'];
                $writeCodeId = $write_off_res_new['write_off_code_id'];
                $providerId = $write_off_res_new['primaryProviderId'];
                $facilityId = ($billing_location=='1') ? $write_off_res_new['billing_facility_id'] : $write_off_res_new['facility_id'];	
                $patientId = $write_off_res_new['patient_id'];
                $write_off_dot = $write_off_res_new['write_off_dot'];
                $chld = $write_off_res_new['charge_list_detail_id'];

                $arrDisData[$writeCodeId][$writeOperatorId][$providerId][$facilityId][$patientId][$encId][$chld]['apl'][] = $write_off_res_new;
                $arrDisData[$writeCodeId][$writeOperatorId][$providerId][$facilityId][$patientId][$encId]['dis'] += $write_off_res_new['write_off_amount'];
                $arrDisData[$writeCodeId][$writeOperatorId][$providerId][$facilityId][$patientId][$encId]['write_off_dot']= $write_off_res_new['write_off_dot'];
                $arrDisData[$writeCodeId][$writeOperatorId][$providerId][$facilityId]['dis'] += $write_off_res_new['write_off_amount'];
                $arrDisData['code'][$writeCodeId] = $writeCodeId;
                $arrDisData['woff_operator'][$writeOperatorId] = $writeOperatorId;
                $arrDisData['charge_list_detail_id'][$chld]['code'][$writeCodeId] = $writeCodeId;
				$arrDisData['opr_info'][$chld][$writeCodeId]=$writeOperatorId;

                $arrEncounters[$encId] = $encId;
                $arrPatientId[$patientId] = $patientId;
                $arrFacilityId[$facilityId] = $facilityId;
                $arrProviderId[] = $providerId;
                $arrOprId[$write_off_res_new['write_off_operator_id']] = $write_off_res_new['write_off_operator_id'];
                $arrChld[$chld] = $chld;
            }
        }
    }unset($write_off_res);

//							account_payments.check_number,	
    // ADJUSTMENT CODE	
    if (count($a_code) > 0 || count($wrt_code) == 0 || count($r_code) > 0) {
        $sel_adj_qry = "SELECT 
							account_payments.encounter_id, 
							account_payments.payment_amount,
							account_payments.charge_list_detail_id,
							account_payments.payment_code_id,
							account_payments.payment_type,
							account_payments.patient_id,
							account_payments.operator_id,						
							patient_charge_list.facility_id,
							patient_charge_list.billing_facility_id, 
							account_payments.check_number,
							account_payments.cas_type,
							account_payments.cas_code,
							date_format(account_payments.entered_date, '" . $dateFormat . "') as write_off_dot,
							patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId', patient_charge_list.del_status
					FROM account_payments join patient_charge_list ON account_payments.encounter_id = patient_charge_list.encounter_id
					JOIN patient_data pd ON pd.id=patient_charge_list.patient_id
					WHERE account_payments.payment_amount  > '0' 
						AND (account_payments.payment_type = 'Adjustment' or account_payments.payment_type = 'Over Adjustment'
						 or account_payments.payment_type = 'Returned Check')";
        if ($dtRangeFor == "dos") {
            $sel_adj_qry .= " AND account_payments.del_status ='0'";
        } else {
            $sel_adj_qry .= " AND (account_payments.del_status='0' OR (account_payments.del_status='1' AND DATE_FORMAT(account_payments.del_date_time, '%Y-%m-%d') >'$EndDate'))
  							  AND (patient_charge_list.del_status='0' OR (patient_charge_list.del_status='1' AND DATE_FORMAT(patient_charge_list.trans_del_date, '%Y-%m-%d')>'$EndDate'))";
        }
        if (empty($strDoePats) == false) {
            //$sel_adj_qry.=" AND patient_charge_list.patient_id NOT IN(".$strDoePats.")";
        }
        if ($StartDate != '' && $EndDate != '') {
            if ($dtRangeFor == "dos") {
                $sel_adj_qry .= " and (patient_charge_list.date_of_service between '$StartDate' and '$EndDate')";
            } else {
                $sel_adj_qry .= " and (DATE_FORMAT(account_payments.entered_date, '%Y-%m-%d') between '$StartDate' and '$EndDate')";
            }
        }
        if (sizeof($arrBatchIds) > 0) {
            $sel_adj_qry .= " and account_payments.batch_id IN ($strBatchIds)";
        }
        if (empty($adjustment) == false) {
            $sel_adj_qry .= " and account_payments.payment_code_id IN ($adjustment)";
        }
		$reason_adj_qry="";
		if (empty($reason) == false) {
			foreach ($r_code as $r_id => $r_val) {
				if($reas_off_code_arr[$r_val]!=""){
					if($reason_adj_qry!=""){
						$reason_adj_qry .= " or ";
					}
					$reason_adj_qry .= " account_payments.cas_type ='".$reas_off_code_arr[$r_val]."'";
				}
			}
			if($reason_adj_qry!=""){
				$sel_adj_qry .= " and ($reason_adj_qry)";
			}
        }
        if (empty($grp_id) === false) {
            $sel_adj_qry .= " and patient_charge_list.gro_id IN ($grp_id)";
        }
        if (empty($sc_name) === false) {
			if($billing_location=='1'){
				$sel_adj_qry.= " and patient_charge_list.billing_facility_id IN ($sc_name)";
			}else{				
				$sel_adj_qry .= " and patient_charge_list.facility_id IN ($sc_name)";
			}
        }
        if (empty($phyId) === false) {
            $sel_adj_qry .= " and patient_charge_list.primary_provider_id_for_reports IN ($phyId)";
        }
        if (empty($operator) === false) {
            $sel_adj_qry .= " and  account_payments.operator_id IN ($operator)";
        }
		$sel_adj_qry .= " ORDER BY patient_charge_list.del_status ASC";
        $adj_off_res = imw_query($sel_adj_qry);
        $arrAdjData = array();
        $tempArrEncSts = array();
        $tempResCodeArr = array();
        while ($adj_off_res_new = imw_fetch_assoc($adj_off_res)) {
            $encId = $adj_off_res_new['encounter_id'];

            if (!($tempArrEncSts[$encId]) || $tempArrEncSts[$encId] == $adj_off_res_new['del_status']) { //CHECK ADDED TO AVOID DUPLICATE ENCOUNTER IN CASE ONE IS DELETED.						
                $tempArrEncSts[$encId] = $adj_off_res_new['del_status'];

                $writeOperatorId = $adj_off_res_new['operator_id'];
                $paymentCodeId = $adj_off_res_new['payment_code_id'];
                $providerId = $adj_off_res_new['primaryProviderId'];
                $facilityId = ($billing_location=='1') ? $adj_off_res_new['billing_facility_id'] : $adj_off_res_new['facility_id'];					
                $patientId = $adj_off_res_new['patient_id'];
                $chld = $adj_off_res_new['charge_list_detail_id'];
				if($adj_off_res_new['cas_type']!=""){
					$tempResCodeArr[$paymentCodeId] = $adj_off_res_new['cas_type'];
				}
                $arrAdjData[$paymentCodeId][$writeOperatorId][$providerId][$facilityId][$patientId][$encId][$chld]['spl'][] = $adj_off_res_new;

                if ($adj_off_res_new['payment_type'] == 'Returned Check' || $adj_off_res_new['payment_type'] == 'Adjustment') {
                    $arrAdjData[$paymentCodeId][$writeOperatorId][$providerId][$facilityId][$patientId][$encId]['adj'] += "-" . $adj_off_res_new['payment_amount'];
                    $arrAdjData[$paymentCodeId][$writeOperatorId][$providerId][$facilityId]['adj'] += "-" . $adj_off_res_new['payment_amount'];
                } else {
                    $arrAdjData[$paymentCodeId][$writeOperatorId][$providerId][$facilityId][$patientId][$encId]['adj'] += $adj_off_res_new['payment_amount'];
                    $arrAdjData[$paymentCodeId][$writeOperatorId][$providerId][$facilityId]['adj'] += $adj_off_res_new['payment_amount'];
                }
				
				$arrAdjData[$paymentCodeId][$writeOperatorId][$providerId][$facilityId][$patientId][$encId]['write_off_dot'] = $adj_off_res_new['write_off_dot'];
				
				
                $arrAdjData['code'][$paymentCodeId] = $paymentCodeId;
                $arrAdjData['woff_operator'][$writeOperatorId] = $writeOperatorId;
                $arrAdjData['charge_list_detail_id'][$chld]['code'][$paymentCodeId] = $paymentCodeId;
				$arrAdjData['opr_info'][$chld][$paymentCodeId] = $writeOperatorId;

                $arrEncounters[$encId] = $encId;
                $arrPatientId[$patientId] = $patientId;
                $arrFacilityId[$facilityId] = $facilityId;
                $arrProviderId[] = $providerId;
                $arrOprId[$adj_off_res_new['operator_id']] = $adj_off_res_new['operator_id'];
                $arrChld[$chld] = $chld;
            }
        }
    }unset($adj_off_res);

    //GET DEFAULT WRITE-OFF
   // if ($dtRangeFor == "dot") {
	if (count($w_code) > 0 || count($wrt_code) == 0) { 
        $arrTemp = array();
        $arrTemp1 = array();
        $tempArrEncSts = array();
		$writeoff_qry_part='';
		
        if (empty($strDoePats) == false) {
            //$writeoff_qry_part.=" AND def.patient_id NOT IN(".$strDoePats.")";
        }
        if (sizeof($arrBatchIds) > 0) {
            $writeoff_qry_part .= " and def.batch_id IN ($strBatchIds)";
        }
        if (empty($write_off) == false) {
            $writeoff_qry_part .= " and def.write_off_code_id IN ($write_off)";
        }
        if (empty($grp_id) === false) {
            $writeoff_qry_part .= " and patChg.gro_id IN ($grp_id)";
        }
        if (empty($sc_name) === false) {
			if($billing_location=='1'){
				$writeoff_qry_part.= " and patChg.billing_facility_id IN ($sc_name)";
			}else{	
			    $writeoff_qry_part.= " and patChg.facility_id IN ($sc_name)";
			}
        }
        if (empty($phyId) === false) {
            $writeoff_qry_part .= " and patChg.primary_provider_id_for_reports IN ($phyId)";
        }
        if (empty($operator) === false) {
            $writeoff_qry_part .= " and  def.write_off_operator_id IN ($operator)";
        }
		
        $normalWriteQry = "Select def.patient_id, def.encounter_id, def.charge_list_detail_id, def.write_off_amount, def.write_off_operator_id,
		date_format(def.write_off_dot, '" . $dateFormat . "') as write_off_dot,
		patChg.primaryProviderId, patChg.facility_id, patChg.billing_facility_id, patChg.del_status, write_off_code_id  
		FROM defaultwriteoff def JOIN patient_charge_list patChg ON patChg.charge_list_id=def.charge_list_id 
		JOIN patient_data pd ON pd.id=patChg.patient_id
		WHERE 1=1";
		if($dtRangeFor=="dos"){
			$normalWriteQry.= " and (patChg.date_of_service between '$StartDate' and '$EndDate') 
			AND def.del_status='0' AND patChg.del_status='0'";
		}else{
			$normalWriteQry.=" AND (def.del_status='0' OR (def.del_status='1' AND def.del_date> '" . $EndDate . "')) 
			AND (patChg.del_status='0' OR (patChg.del_status='1' AND DATE_FORMAT(patChg.trans_del_date, '%Y-%m-%d')>'$EndDate'))
			AND (DATE_FORMAT(def.write_off_dot, '%Y-%m-%d') BETWEEN '$StartDate' AND '$EndDate')";
		}
		$normalWriteQry.=$writeoff_qry_part;
		$normalWriteQry .= " ORDER BY patChg.del_status ASC, def.encounter_id ASC, def.charge_list_detail_id ASC, def.write_off_dot ASC, def.write_off_id ASC"; //TO RECORD ONLY LAST WRITE-OFF		
        
		$normalWriteRs = imw_query($normalWriteQry);
        while ($write_off_res_new = imw_fetch_array($normalWriteRs)) {
            $encId = $write_off_res_new['encounter_id'];

            if (!($tempArrEncSts[$encId]) || $tempArrEncSts[$encId] == $write_off_res_new['del_status']) { //CHECK ADDED TO AVOID DUPLICATE ENCOUNTER IN CASE ONE IS DELETED.						
                $tempArrEncSts[$encId] = $write_off_res_new['del_status'];

                $writeOperatorId = $write_off_res_new['write_off_operator_id'];
                $writeCodeId = $write_off_res_new['write_off_code_id'];
                $providerId = $write_off_res_new['primaryProviderId'];
                $write_off_dot = $write_off_res_new['write_off_dot'];
                $facilityId = ($billing_location=='1') ? $write_off_res_new['billing_facility_id'] : $write_off_res_new['facility_id'];					
                $patientId = $write_off_res_new['patient_id'];

                $chId = $write_off_res_new['charge_list_detail_id'];

                $arrTemp[$providerId][$facilityId][$patientId][$encId][$chId] = $write_off_res_new['write_off_amount'];
                $arrTemp1[$chId]['operator_id'] = $write_off_res_new['write_off_operator_id'];
				$arrTemp1[$chId]['code_id'] = $writeCodeId;
				$arrTemp1[$chId]['write_off_dot'] = $write_off_dot;
            }
        }
		foreach ($arrTemp as $providerId => $facData) {
			foreach ($facData as $facilityId => $patData) {
				foreach ($patData as $patientId => $encData) {
					foreach ($encData as $encId => $chgData) {
						foreach ($chgData as $chId => $writeAmt) {
							$writeCodeId=$arrTemp1[$chId]['code_id'];
							$writeOprId=$arrTemp1[$chId]['operator_id'];
							$write_off_dot=$arrTemp1[$chId]['write_off_dot'];
							
							$arrWriteOffCode[$writeCodeId][$writeOprId][$providerId][$facilityId][$patientId][$encId]['write_off']+= $writeAmt;
							$arrWriteOffCode[$writeCodeId][$writeOprId][$providerId][$facilityId][$patientId][$encId]['write_off_dot']= $write_off_dot;
							$arrWriteOffCode[$writeCodeId][$writeOprId][$providerId][$facilityId]['write_off']+= $writeAmt;
							$arrWriteOffCode[$writeCodeId][$writeOprId][$providerId][$facilityId]['write_off_dot']= $write_off_dot;
							$arrWriteOffCode['code'][$writeCodeId] = $writeCodeId;
							$arrWriteOffCode['charge_list_detail_id'][$chId]['code'][$writeCodeId] = $writeCodeId;
							$arrWriteOffCode['woff_operator'][$writeOprId] = $writeOprId;
							$arrDefaultWriteOff[$chId]['amt'] += $writeAmt;
							$arrDefaultWriteOff[$chId]['code'] = $arrTemp1[$chId]['code_id'];
							$arrDefaultWriteOff['opr_info'][$chId][$writeCodeId]=$writeOprId;

							$arrEncounters[$encId] = $encId;
							$arrPatientId[$patientId] = $patientId;
							$arrFacilityId[$facilityId] = $facilityId;
							$arrProviderId[] = $providerId;
							$arrOprId[$arrTemp1[$chId]] = $writeOprId;
							$arrChld[$chId] = $chId;
						}
					}
				}
			}
		}
  }

    //	GET REFUND AMOUNT
    if ($dtRangeFor == 'dot') {
        //	GET DEBIT AMOUNT
        $debit_qry_rs = $CLSReports->__getDebitappliedData('', $mode = 'ENCOUNTERID', $StartDate, $EndDate, $strBatchIds, $operator, '', '', '', '', '', $checkDel = 'yes');
        while($debit_qry_res = imw_fetch_assoc($debit_qry_rs)){
			$eid = $debit_qry_res['crAppliedToEncId'];
            $charge_id = $debit_qry_res['charge_list_detail_id'];
			$opr_id= $debit_qry_res['operatorApplied'];
			$write_off_dot= $debit_qry_res['entered_date'];
            if ($debit_qry_res['crAppliedTo'] == "payment") { //REFUND
                $arrAdjData['charge_list_detail_id'][$charge_id]['code']['extra_adj_refund'] = 'extra_adj_refund';
				$arrAdjData['opr_info'][$charge_id][0] = $opr_id;
                $arrOtherAdj[$charge_id][$opr_id] += $debit_qry_res['amountApplied'];
                $arrEncounters[$eid] = $eid;
				$arrAdjData['write_off_dot'][$charge_id] = $write_off_dot;
            }
        }
    } else {
        $refundQry = "Select crd.patient_id, crd.crAppliedToEncId, crd.charge_list_detail_id, crd.amountApplied, patChg.del_status,
		crd.operatorApplied  
		FROM patient_charge_list patChg JOIN creditapplied crd ON crd.crAppliedToEncId=patChg.encounter_id  
		WHERE (patChg.date_of_service BETWEEN '$StartDate' AND '$EndDate') AND patChg.del_status='0' AND crAppliedTo='payment' 
		AND crd.delete_credit='0'";
        if (empty($grp_id) === false) {
            $refundQry .= " and patChg.gro_id IN ($grp_id)";
        }
        if (empty($sc_name) === false) {
			if($billing_location=='1'){
				$refundQry.= " and patChg.billing_facility_id IN ($sc_name)";
			}else{	
				$refundQry.= " and patChg.facility_id IN ($sc_name)";
			}
        }
        if (empty($phyId) === false) {
            $refundQry .= " and patChg.primary_provider_id_for_reports IN ($phyId)";
        }
        if (empty($operator) === false) {
            $refundQry .= " and  crd.operatorApplied IN ($operator)";
        }
        $debit_qry_rs = imw_query($refundQry);
        while ($debit_qry_res = imw_fetch_assoc($debit_qry_rs)) {
            $eid = $debit_qry_res['crAppliedToEncId'];
            $charge_id = $debit_qry_res['charge_list_detail_id'];
			$opr_id= $debit_qry_res['operatorApplied'];

            $arrAdjData['charge_list_detail_id'][$charge_id]['code']['extra_adj_refund'] = 'extra_adj_refund';
			$arrAdjData['opr_info'][$charge_id][0] = $opr_id;
            $arrOtherAdj[$charge_id][$opr_id] += $debit_qry_res['amountApplied'];
            $arrEncounters[$eid] = $eid;
        }
    }


    $strEncounters = implode(',', $arrEncounters);
    // GET MAIN CHARGES AMOUNT
    $flag_data_exist = 0;
    $chld_imp = implode(',', $chld_arr);
    $tempArrEncSts = array();
    $sel_qry = "SELECT 
	patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId', patient_charge_list.del_status,
	date_format(patient_charge_list.date_of_service, '" . $dateFormat . "') as date_of_service,
	patient_charge_list.charge_list_id,
	patient_charge_list.encounter_id, 
	patient_charge_list.gro_id, 
	patient_charge_list.facility_id,
	patient_charge_list.billing_facility_id,
	
	patient_charge_list_details.charge_list_detail_id, 			
	patient_charge_list_details.approvedAmt, 
	patient_charge_list_details.patient_id, 
	patient_charge_list_details.paidForProc+patient_charge_list_details.overPaymentForProc as 'paidForProc', 
	patient_charge_list_details.write_off, 
	patient_charge_list_details.totalAmount, 
	patient_charge_list_details.write_off_code_id, 
	patient_charge_list_details.write_off_opr_id, 
	patient_charge_list_details.procCharges * patient_charge_list_details.units as procCharges,
	patient_charge_list_details.write_off_dot,
	patient_charge_list_details.newBalance,
	patient_charge_list_details.procCode
	
	FROM patient_charge_list 
	JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id 
	JOIN patient_data pd ON pd.id=patient_charge_list.patient_id 
	WHERE 1=1";
    if (empty($strDoePats) == false) {
        //$sel_qry.=" AND patient_charge_list.patient_id NOT IN(".$strDoePats.")";
    }
    if ($dtRangeFor == "dos") {
        $sel_qry .= " AND patient_charge_list_details.del_status='0'";
    } else {
        $sel_qry .= " AND (patient_charge_list_details.del_status='0' OR (patient_charge_list_details.del_status='1' AND DATE_FORMAT(patient_charge_list_details.trans_del_date, '%Y-%m-%d')>'$EndDate'))";
    }
	/*if ($StartDate != '' && $EndDate != '') {
        if (sizeof($arrEncounters) > 0) {
            $sel_qry .= " AND (
						(patient_charge_list.date_of_service between '$StartDate' and '$EndDate') 
						OR 
						patient_charge_list.encounter_id IN(" . $strEncounters . ")
							)";
        } else {
            $sel_qry .= " AND (patient_charge_list.date_of_service between '$StartDate' and '$EndDate')";
        }
    }*/
	if (sizeof($arrEncounters) > 0) {
		$sel_qry.=" AND patient_charge_list.encounter_id IN(" . $strEncounters . ")";
	}	
	/*if (sizeof($arrBatchIds) > 0) {
        $sel_qry .= " and (
		patient_charge_list_details.batch_id IN ($strBatchIds) 
		OR 
		patient_charge_list_details.charge_list_detail_id IN ($chld_imp))";
    }*/
    if (empty($grp_id) === false) {
        $sel_qry .= " and patient_charge_list.gro_id IN ($grp_id)";
    }
    if (empty($sc_name) === false) {
		if($billing_location=='1'){
			$sel_qry.= " and patient_charge_list.billing_facility_id IN ($sc_name)";
		}else{	
			$sel_qry.= " and patient_charge_list.facility_id IN ($sc_name)";
		}
    }
    if (empty($phyId) === false) {
        $sel_qry .= " and patient_charge_list.primary_provider_id_for_reports IN ($phyId)";
    }
/*    if (empty($operator) === false) {
        $sel_qry .= " and  patient_charge_list_details.write_off_opr_id IN ($operator)";
    }*/
    $sel_qry .= " ORDER BY patient_charge_list.date_of_service,
							patient_charge_list.encounter_id, patient_charge_list.del_status ASC";
   //echo $sel_qry;
    $chargesQryRs = imw_query($sel_qry);
    //for($i=0;$i<count($chargesQryRes);$i++){
    while ($chargesQryRes = imw_fetch_array($chargesQryRs)) {
        $flag_data_exist = 1;
        $encId = $chargesQryRes['encounter_id'];

        if (!($tempArrEncSts[$encId]) || $tempArrEncSts[$encId] == $chargesQryRes['del_status']) { //CHECK ADDED TO AVOID DUPLICATE ENCOUNTER IN CASE ONE IS DELETED.
            $tempArrEncSts[$encId] = $chargesQryRes['del_status'];

            $writeOffAmt = 0;
            $writeOffBy = $userNameTwoCharArr[$chargesQryRes['write_off_opr_id']];
            $providerId = $chargesQryRes['primaryProviderId'];
            $facilityId = ($billing_location=='1') ? $chargesQryRes['billing_facility_id'] : $chargesQryRes['facility_id'];				
            $patientId = $chargesQryRes['patient_id'];

            $writeOperatorId = $chargesQryRes['write_off_opr_id'];
            $procId = $chargesQryRes['procCode'];
            $writeCodeId = $chargesQryRes['write_off_code_id'];
            $chld = $chargesQryRes['charge_list_detail_id'];
            $dos = $chargesQryRes['date_of_service'];

			$writeCodeId = $arrDefaultWriteOff[$chld]['code'];
			$writeOffAmt = $arrDefaultWriteOff[$chld]['amt'];
/*          if ($dtRangeFor == "dot") {
                $writeCodeId = $arrDefaultWriteOff[$chld]['code'];
                $writeOffAmt = $arrDefaultWriteOff[$chld]['amt'];
            } else {
                $writeOffAmt = $chargesQryRes['write_off'];
            }
*/
            //---- BEGIN WRITE OFF ARRAYS ---------------------------
            if($arrDefaultWriteOff[$chld]['amt']>0 || $arrWriteOffCode['charge_list_detail_id'][$chld]){

				if($arrDefaultWriteOff[$chld]>0 && $disOrAdjSel==false){
					$writeOperatorId=$arrDefaultWriteOff['opr_info'][$chld][$writeCodeId];				
//if($encId==260903){ echo $writeCodeId.', '; }
					//echo "entered in third condition";
					$arrWriteOffCode[$writeCodeId][$writeOperatorId][$providerId][$facilityId][$patientId][$encId][$chld]['auto'] = $chargesQryRes;
					$arrWriteOffCode[$writeCodeId][$writeOperatorId][$providerId][$facilityId][$patientId][$encId]['dos'] = $chargesQryRes['date_of_service'];
					//$arrWriteOffCode[$writeCodeId][$writeOperatorId][$providerId][$facilityId][$patientId][$encId]['write_off'] += $writeOffAmt;
					//$arrWriteOffCode[$writeCodeId][$writeOperatorId][$providerId][$facilityId]['write_off'] += $writeOffAmt;
					$arrWriteOffCode['charge_list_detail_id'][$chld]['charges'] = $chargesQryRes['totalAmount'];
					$arrWriteOffCode['charge_list_detail_id'][$chld]['balance'] = $chargesQryRes['newBalance'];
					$arrWriteOffCode['charge_list_detail_id'][$chld]['dos'] = $chargesQryRes['date_of_service'];
					//$arrWriteOffCode['code'][$writeCodeId] = $writeCodeId;
					//$arrWriteOffCode['woff_operator'][$writeOperatorId] = $writeOperatorId;
				} else if ($arrWriteOffCode['charge_list_detail_id'][$chld]) {
					$arrWriteOffCode['charge_list_detail_id'][$chld]['charges'] = $chargesQryRes['totalAmount'];
					$arrWriteOffCode['charge_list_detail_id'][$chld]['balance'] = $chargesQryRes['newBalance'];
					$arrWriteOffCode['charge_list_detail_id'][$chld]['dos'] = $chargesQryRes['date_of_service'];
				}
				
				$arrPatientId[$patientId] = $patientId;
				$arrProcedureId[] = $procId;
				$arrProviderId[] = $providerId;
				$arrFacilityId[$facilityId] = $facilityId;

            } else if ($arrWriteOffCode['charge_list_detail_id'][$chld]) {
                $arrWriteOffCode['charge_list_detail_id'][$chld]['charges'] = $chargesQryRes['totalAmount'];
                $arrWriteOffCode['charge_list_detail_id'][$chld]['balance'] = $chargesQryRes['newBalance'];
                $arrWriteOffCode['charge_list_detail_id'][$chld]['dos'] = $chargesQryRes['date_of_service'];
                $arrPatientId[$patientId] = $patientId;
                $arrProcedureId[] = $procId;
                $arrProviderId[] = $providerId;
                $arrFacilityId[$facilityId] = $facilityId;
            }
            //---- END WRITE OFF ARRAYS ---------------------------
            //---- BEGIN ADJUSTMENT ARRAYS ---------------------------
            if ($arrAdjData['charge_list_detail_id'][$chld]) {
                $arrAdjCodeChld = $arrAdjData['charge_list_detail_id'][$chld]['code'];
                foreach ($arrAdjCodeChld as $code) {
                    if ($code == 'extra_adj_refund') {
						$writeOperatorId=$arrAdjData['opr_info'][$chld][0];
						
                        //$arrAdjData[0][$providerId][$facilityId][$patientId][$encId][$chld]['spl'][] = $adj_off_res_new;
                        $arrAdjData[0][$writeOperatorId][$providerId][$facilityId][$patientId][$encId]['refund'] += $arrOtherAdj[$chld][$writeOperatorId];
                        $arrAdjData[0][$writeOperatorId][$providerId][$facilityId]['refund'] += $arrOtherAdj[$chld][$writeOperatorId];
                        $arrAdjData['code'][0] = 0;
                        $arrAdjData['woff_operator'][$writeOperatorId] = $writeOperatorId;

                        $arrAdjData[0][$writeOperatorId][$providerId][$facilityId][$patientId][$encId][$chld]['chld_detail'] = $chargesQryRes;
                        $arrAdjData[0][$writeOperatorId][$providerId][$facilityId][$patientId][$encId]['dos'] = $chargesQryRes['date_of_service'];
                        $arrPatientId[$patientId] = $patientId;
                    } else {
						$writeOperatorId=$arrAdjData['opr_info'][$chld][$code];
                        $arrAdjData[$code][$writeOperatorId][$providerId][$facilityId][$patientId][$encId][$chld]['chld_detail'] = $chargesQryRes;
                        $arrAdjData[$code][$writeOperatorId][$providerId][$facilityId][$patientId][$encId]['dos'] = $chargesQryRes['date_of_service'];
                    }
                }
                $arrAdjData['charge_list_detail_id'][$chld]['charges'] = $chargesQryRes['totalAmount'];
                $arrAdjData['charge_list_detail_id'][$chld]['balance'] = $chargesQryRes['newBalance'];
                $arrAdjData['charge_list_detail_id'][$chld]['dos'] = $chargesQryRes['date_of_service'];
            }
            //---- END ADJUSTMENT ARRAYS ---------------------------
            //---- BEGIN DISCOUNT ARRAYS ---------------------------
            if ($arrDisData['charge_list_detail_id'][$chld]) {
                $arrDisCodeChld = $arrDisData['charge_list_detail_id'][$chld]['code'];
                foreach ($arrDisCodeChld as $code) {
					$writeOperatorId=$arrDisData['opr_info'][$chld][$code];
                    $arrDisData[$code][$writeOperatorId][$providerId][$facilityId][$patientId][$encId][$chld]['chld_detail'][] = $chargesQryRes;
                    $arrDisData[$code][$writeOperatorId][$providerId][$facilityId][$patientId][$encId]['dos'] = $chargesQryRes['date_of_service'];
                }
                $arrDisData['charge_list_detail_id'][$chld]['charges'] = $chargesQryRes['totalAmount'];
                $arrDisData['charge_list_detail_id'][$chld]['balance'] = $chargesQryRes['newBalance'];
                $arrDisData['charge_list_detail_id'][$chld]['dos'] = $chargesQryRes['date_of_service'];
            }

            //---- END DISCOUNT ARRAYS ---------------------------
            $arrEncounters[$encId] = $encId;
            $arrChld[$chld] = $chld;
            $writeoff_data[] = $chld;
        }
    }
    unset($chargesQryRs);
}


//---BEGIN GET PAYMENTS------------
$arrPaymentChld = $arrPaymentWrite = $arrPaymentAdj = $arrPaymentDis = array();

if (count($arrWriteOffCode['charge_list_detail_id']) > 0)
    $arrPaymentWriteTemp = array_keys($arrWriteOffCode['charge_list_detail_id']);
$arrPaymentWrite = array_combine($arrPaymentWriteTemp, $arrPaymentWriteTemp);
unset($arrPaymentWriteTemp);

if (count($arrAdjData['charge_list_detail_id']) > 0)
    $arrPaymentAdjTemp = array_keys($arrAdjData['charge_list_detail_id']);
$arrPaymentAdj = array_combine($arrPaymentAdjTemp, $arrPaymentAdjTemp);
unset($arrPaymentAdjTemp);

if (count($arrDisData['charge_list_detail_id']) > 0)
    $arrPaymentDisTemp = array_keys($arrDisData['charge_list_detail_id']);
$arrPaymentDis = array_combine($arrPaymentDisTemp, $arrPaymentDisTemp);
unset($arrPaymentDisTemp);

//REMOVING ERROS OF ARRAY MERGE FUNCTION
if (sizeof($arrPaymentWrite) <= 0)
    $arrPaymentWrite = array();
if (sizeof($arrPaymentAdj) <= 0)
    $arrPaymentAdj = array();
if (sizeof($arrPaymentDis) <= 0)
    $arrPaymentDis = array();

$arrPaymentChld = array_merge($arrPaymentWrite, $arrPaymentAdj, $arrPaymentDis);
$arrPaymentChld = implode(",", $arrPaymentChld);
$qry = "SELECT 
				pcdpi.paidForProc,
				pcdpi.charge_list_detail_id 	
				FROM patient_chargesheet_payment_info pcpi 
				JOIN patient_charges_detail_payment_info pcdpi ON pcpi.payment_id = pcdpi.payment_id
				WHERE pcdpi.charge_list_detail_id IN(" . $arrPaymentChld . ")
					AND pcdpi.deletePayment = 0
					AND pcpi.markPaymentDelete = 0
				";
$res = imw_query($qry);
while ($row = imw_fetch_array($res)) {
    $chld = $row['charge_list_detail_id'];

    if ($arrPaymentWrite[$chld])
        $arrWriteOffCode['charge_list_detail_id'][$chld]['payment'] += $row['paidForProc'];

    if ($arrPaymentAdj[$chld])
        $arrAdjData['charge_list_detail_id'][$chld]['payment'] += $row['paidForProc'];

    if ($arrPaymentDis[$chld])
        $arrDisData['charge_list_detail_id'][$chld]['payment'] += $row['paidForProc'];
}unset($res);
//---END GET PAYMENTS--------------
//---BEGIN GET PATIENT ARRAY-----------------------------------------------------
$qry = "SELECT 
				id,
				CONCAT(patient_data.lname,', ',patient_data.fname, ' - ',id) AS name
				FROM patient_data 
				WHERE patient_data.id IN(" . implode(",", array_unique($arrPatientId)) . ")";
$res = imw_query($qry);
$arrPatient = array();
while ($row = imw_fetch_array($res)) {
    $arrPatient[$row['id']] = $row['name'];
}unset($res);
//---END GET PATIENT ARRAY-----------------------------------------------------
//---BEGIN GET PROCEDURE ARRAY-----------------------------------------------------
$qry = "SELECT 
				cpt_fee_id,
				cpt4_code 
				FROM cpt_fee_tbl 
				WHERE cpt_fee_id IN(" . implode(",", array_unique($arrProcedureId)) . ")";
$res = imw_query($qry);
$arrProcedure = array();
while ($row = imw_fetch_array($res)) {
    $arrProcedure[$row['cpt_fee_id']] = $row['cpt4_code'];
}unset($res);
//---END GET PROCEDURE ARRAY-----------------------------------------------------
//---BEGIN GET PROVIDER ARRAY-----------------------------------------------------
$qry = "SELECT 
				id,
				TRIM(CONCAT(users.lname,', ',users.fname,' ',users.mname)) as physician_name
				FROM users";
$res = imw_query($qry);
$arrProvider = array();
while ($row = imw_fetch_array($res)) {
    $arrProvider[$row['id']] = $row['physician_name'];
}unset($res);
//---END GET PROVIDER ARRAY-----------------------------------------------------
//---BEGIN GET OPERATOR ARRAY-----------------------------------------------------
$qry = "SELECT 
				id,
				TRIM(CONCAT(users.lname,', ',users.fname,' ',users.mname)) as user_name
				FROM users 
				WHERE id IN(" . implode(",", array_unique($arrOprId)) . ")";
$res = imw_query($qry);
$arrOpr = array();
while ($row = imw_fetch_array($res)) {
    $arrOpr[$row['id']] = $row['user_name'];
}unset($res);
//---END GET OPERATOR ARRAY-----------------------------------------------------
//---BEGIN GET FACILITY ARRAY-----------------------------------------------------
$qry = "SELECT 
				pos_facility_id,
				CONCAT(facility_name,' - ',facilityPracCode) AS facility_name
				FROM pos_facilityies_tbl";
$res = imw_query($qry);
$arrFacility = array();
while ($row = imw_fetch_array($res)) {
    $arrFacility[$row['pos_facility_id']] = $row['facility_name'];
}unset($res);
//---END GET FACILITY ARRAY-----------------------------------------------------
// CREDIT DEBIT ------------------------------
$strAllEncounters = implode(',', $arrEncounters);
$strAllChargeIds = implode(',', $chld_arr);
$debit_amt_arr = array();
$pay_crd_deb_arr = array();
$pay_crd_deb_chld_arr = array();

//	GET DEBIT AMOUNT FOR ADJUSTMENTS
/* 	if($dtRangeFor=="dos"){
  $debit_qry_res = $objManageData->__getDebitappliedData($strAllEncounters, $mode='ENCOUNTERID');
  for($i=0;$i<count($debit_qry_res);$i++){
  $charge_id = $debit_qry_res[$i]['charge_list_detail_id'];
  $eid = $debit_qry_res[$i]['crAppliedToEncId'];
  if($debit_qry_res[$i]['crAppliedTo']=="payment"){
  $debit_amt_arr[$eid]+= $debit_qry_res[$i]['amountApplied'];
  }
  }
  } */

//GET CREDIT AMOUNT
$credit_qry_rs = $CLSReports->__getCreditappliedRept($strAllChargeIds, $mode = 'CHARGELISTID', $StartDate, $EndDate, '', $operator, '', '', '', '', '', '', '', $reptByForFun);
while ($credit_qry_res = imw_fetch_assoc($credit_qry_rs)) {
    $encounter_id_adjust = $credit_qry_res['crAppliedToEncId_adjust'];
    $chld = $credit_qry_res['charge_list_detail_id_adjust'];
    if ($credit_qry_res['crAppliedTo'] == "adjustment") {
        $pay_crd_deb_arr[$encounter_id_adjust][] = $credit_qry_res['amountApplied'];
        $pay_crd_deb_chld_arr[$chld][] = $credit_qry_res['amountApplied'];
    }
}

//GET DEBIT AMOUNT
$debit_qry_rs = $CLSReports->__getDebitappliedData($strAllChargeIds, $mode = 'CHARGELISTID', $StartDate, $EndDate, '', $operator, '', '', '', '', '', '', '', $reptByForFun);
while ($debit_qry_res = imw_fetch_assoc($debit_qry_rs)) {
    $encounter_id = $debit_qry_res['crAppliedToEncId'];
    $chld = $debit_qry_res['charge_list_detail_id'];
    if ($debit_qry_res['crAppliedTo'] == "adjustment") {
        $pay_crd_deb_arr[$encounter_id][] = '-' . $debit_qry_res['amountApplied'];
        $pay_crd_deb_chld_arr[$chld][] = '-' . $debit_qry_res['amountApplied'];
    }
}


//pre($pay_crd_deb_chld_arr);
if (count($writeoff_data) > 0) {
    $selWriteoffDisp = 'All';
    if (sizeof($wrt_code) == 1) {
        $arrSelAdjCode = explode("_", $str_wrt_code);
        switch ($arrSelAdjCode[0]) {
            case "DC":
                $selWriteoffDisp = $dis_off_code_arr[$arrSelAdjCode[1]];
                break;
            case "AC":
                $selWriteoffDisp = $adj_off_code_arr[$arrSelAdjCode[1]];
                break;
            case "WC":
                $selWriteoffDisp = $write_off_code_arr[$arrSelAdjCode[1]];
                break;
			case "RC":
                $selWriteoffDisp = $reason_off_code_arr['res_'.$arrSelAdjCode[1]];
                break;	
        }
    } else if (count($wrt_code) == $allWriteoffCount) {
        $selWriteoffDisp = "All";
    } else if (count($wrt_code) > 1) {
        $selWriteoffDisp = "Multi";
    }

    if ($processReport == 'summary') {
        include_once(dirname(__FILE__) . '/adjustment_report_summary.php');
    } else {
        include_once(dirname(__FILE__) . '/adjustment_report_detail.php');
    }
    $conditionChk = true;
}


if ($printFile == true && $page_data != "") {
    $op = 'l';
    $styleHTML = '<style>' . file_get_contents('css/reports_html.css') . '</style>';
    $csv_file_data = $styleHTML . $page_data;

    $stylePDF = '<style>' . file_get_contents('css/reports_pdf.css') . '</style>';
    $strHTML = $stylePDF . $pdf_file_content;

    $file_location = write_html($strHTML, 'adjustment_report.html');


    $HTMLCreated = 0;
    if($callFrom!='scheduled'){	
		if ($printFile == true) {
			$HTMLCreated = 1;
			if ($output_option == 'view' || $output_option == 'output_csv') {
				echo $csv_file_data;
			} else {
				echo '<div class="text-center alert alert-info">Please Check PDF.</div>';
			}
		}
	}
} else {
	if($callFrom!='scheduled'){	
		echo '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}
?>
