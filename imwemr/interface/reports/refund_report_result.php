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
  FILE : refundResult.php
  PURPOSE : Dsiplaying result for Refund report.
  ACCESS TYPE : Direct
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

//--- ADMIN ACCESS CHECK ----
$adminAccess = false;
if(core_check_privilege(array("priv_admin")) == true){
	$adminAccess = true;
}

$printFile = true;
$csvFileData = NULL;
if($_POST['form_submitted']){
    $pdfData = NULL;
    $printFile = false;

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
    
    //--- CHANGE DATE FORMAT ----
    $StartDate = getDateFormatDB($Start_date);
    $EndDate = getDateFormatDB($End_date);

	$grp_id=$sc_name=$Physician=$operator='';

	$grp_id=(sizeof($groups)>0)? implode(',',$groups): '';
	$sc_name=(sizeof($facility_name)>0)? implode(',',$facility_name): '';
	$Physician=(sizeof($phyId)>0)? implode(',',$phyId): '';
	$operator=(sizeof($operator_id)>0)? implode(',',$operator_id): '';
    
    $op_name_arr = preg_split('/, /', strtoupper($_SESSION['authProviderName']));
    $op_name = ucfirst(trim($op_name_arr[1][0]));
    $op_name .= ucfirst(trim($op_name_arr[0][0]));

    //--- CHANGE DATE FORMAT ---
    $startDate = getDateFormatDB($Start_date);
    $endDate = getDateFormatDB($End_date);


    //---- GET OPERATOR DETAILS ----------
    $userNameArr = array();
	$userNameArr[0]='No Provider';
    $operator_arr = array();
    $phyIdArr = array();
    $query_rs = imw_query("select id,lname,fname,mname,pro_title,user_type,delete_status from users ORDER BY lname, fname");
    while ($usersQryRes = imw_fetch_assoc($query_rs)) {
        $id = $usersQryRes['id'];
        //--- PROVIDER TYPE CHECK ONLY FOR PHYSICIAN ---	
        $user_type = $usersQryRes['user_type'];
        if ($user_type == 1 || $user_type == 5) {
            $phyIdArr[] = $id;
        }
        //--- DELETE PROVIDER CHECK ---
        $delete_status = $usersQryRes['delete_status'];
        $operator_arr[] = $id;
        $user_name_arr = array();
        $user_name_arr['TITLE'] = $usersQryRes['pro_title'];
        $user_name_arr['LAST_NAME'] = $usersQryRes['lname'];
        $user_name_arr['FIRST_NAME'] = $usersQryRes['fname'];
        $user_name_arr['MIDDLE_NAME'] = $usersQryRes['mname'];
        $userNameArr[$id] = changeNameFormat($user_name_arr);
    }
    

	//SET GROUPINGS
	$view_by='physician';
	if(empty($sc_name) == false) {
		$view_by='facility';
	}
	if(empty($sc_name) == false && empty($operator) === false) {
		$view_by='fac_n_oper';
	}


    //---- GET ALL POS FACILITIES DETAILS ------
    $qry="SELECT pos_facilityies_tbl.facilityPracCode, 
	pos_facilityies_tbl.pos_facility_id,
	pos_tbl.pos_prac_code
	FROM	pos_facilityies_tbl 
	JOIN pos_tbl ON pos_tbl.pos_id = pos_facilityies_tbl.pos_id ORDER BY pos_facilityies_tbl.facilityPracCode, pos_tbl.pos_prac_code";
    $rs = imw_query($qry);
    $posFacilityArr = array();
	while($res=imw_fetch_assoc($rs))	{
        $facilityPracCode = $res['facilityPracCode'];
        $pos_facility_id = $res['pos_facility_id'];
        $pos_prac_code = $res['pos_prac_code'];
        $posFacilityArr[$pos_facility_id] = $facilityPracCode . ' - ' . $pos_prac_code;
    }
    $arr_sc_name = array_combine($facility_name, $facility_name);

    // ARRAY OF FACILITIES AND POS FACILITIES
    $strSelFacilities = '';
    $arrPosFacAtFac = array();
    $arrSelFacilities = array();
    $qry = "Select id, fac_prac_code FROM facility ORDER BY fac_prac_code";
    $rs = imw_query($qry);
    while ($res = imw_fetch_array($rs)) {
        if (empty($sc_name)==false && $arr_sc_name[$res['fac_prac_code']]) {
            $arrSelFacilities[$res['id']] = $res['id'];
        }
        $arrPosFacAtFac[$res['id']] = $res['fac_prac_code'];
    }
    if (sizeof($arrSelFacilities) > 0) {
        $strSelFacilities = implode(',', $arrSelFacilities);
    }
	//-------------------------------------------------------------------
	

    //-- GET EXACT REFUND ENCOUNTERS 
    $arrEncounterIds = array();
    $arrRefundAmt = array();
    $arrRefundCPT = array();
    $arrRefundDate = array();
    $qry = "SELECT amountApplied,
				crAppliedTo, 
				cpt_code,
				crAppliedToEncId, 
				charge_list_detail_id, 
				DATE_FORMAT(dateApplied, '" . get_sql_date_format() . "') as dateApplied, 
				payment_mode, operatorApplied 
			FROM creditapplied 
			WHERE delete_credit = '0' 
				AND LOWER(crAppliedTo) = 'payment' 
				AND (DATE_FORMAT(entered_date, '%Y-%m-%d') BETWEEN '$startDate' and '$endDate')
			";
    if (empty($operator) === false) {
        $qry .= " AND operatorApplied IN(" . $operator . ")";
    }

    $rs = imw_query($qry);
    while ($res = imw_fetch_assoc($rs)) {
        $eId = $res['crAppliedToEncId'];
		$chgdetid=$res['charge_list_detail_id'];
		$oprid = $res['operatorApplied'];
		$arrChargeListIds[$chgdetid] = $chgdetid;
	
		if($view_by=='fac_n_oper'){	
			$arrRefundAmt[$chgdetid][$oprid] += $res['amountApplied'];
			$arrRefundCPT[$chgdetid][$oprid] = $res['cpt_code'];
			$arrRefundDate[$chgdetid][$oprid] = $res['dateApplied'];
			$arrRefundMethod[$chgdetid][$oprid] = $res['payment_mode'];
		}else{
			$arrRefundAmt[$eId] += $res['amountApplied'];
			$arrRefundCPT[$eId][] = $res['cpt_code'];
			$arrRefundDate[$eId] = $res['dateApplied'];
			$arrRefundMethod[$eId] = $res['payment_mode'];
		}
    }

    //--- GET ALL RESULTS FROM PATIENT CHARGE LIST TABLE ---------
    if (sizeof($arrChargeListIds) > 0) {
        $strChargeListIds = implode(',', $arrChargeListIds);
        $arrCheckEncs = array();
        $sql = "SELECT patient_charge_list.encounter_id,
			   patient_charge_list.patient_id,
			   date_format(patient_charge_list.date_of_service , '" . get_sql_date_format() . "') as date_of_service, 
			   patient_data.lname,
			   patient_data.fname,
			   patient_data.mname, 
			   patient_charge_list_details.approvedAmt,
			   patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId',
			   patient_charge_list.facility_id, 
			   patient_charge_list_details.charge_list_detail_id,
			   patient_charge_list_details.totalAmount,
			   patient_charge_list_details.paidForProc,
			   users.fname as pro_fname,
			   users.lname as pro_lname
		FROM patient_charge_list 
		LEFT JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id 
		JOIN patient_data ON patient_data.id = patient_charge_list.patient_id
		JOIN users ON users.id = patient_charge_list.primary_provider_id_for_reports 
		LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = patient_charge_list.facility_id 
		LEFT JOIN pos_tbl ON pos_tbl.pos_id = pos_facilityies_tbl.pos_id 
		WHERE patient_charge_list_details.charge_list_detail_id IN(" . $strChargeListIds . ") 
		AND (patient_charge_list.del_status='0' 
					AND patient_charge_list_details.del_status='0'
				)
		";
        if (empty($grp_id) == false) {
            $sql .= " and patient_charge_list.gro_id IN ($grp_id)";
        }
        if (empty($sc_name) == false) {
            $sql .= " and patient_charge_list.facility_id IN ($sc_name)";
        }
        if (empty($Physician) == false) {
            $sql .= " and patient_charge_list.primary_provider_id_for_reports IN ($Physician)";
        }
        if (empty($patientId) === false) {
            $startLname = '';
            $sql .= " and patient_charge_list.patient_id in ($patientId)";
        } else if (trim($startLname) != '') {
            $sql .= " and (trim(patient_data.lname) between '$startLname' and '$endLname' 
					or trim(patient_data.lname) like '$endLname%')";
        }
		if($view_by=='physician'){	
        	$sql .= " ORDER BY users.lname,users.fname,patient_data.lname,patient_data.fname,patient_charge_list.encounter_id";
		}else if($view_by=='facility' || $view_by=='fac_n_oper'){	
			$sql .= " ORDER BY pos_facilityies_tbl.facilityPracCode,patient_data.lname,patient_data.fname,patient_charge_list.encounter_id";
		}

        //echo $CLSReports->QUERY_STRING;
        $pat_ch_rs=imw_query($sql); 
        //$mainQryRows = imw_num_rows($sql_rs);
        $ovr_pay_arr = array();
        $arrRefundData = array();
        $patientMainDataArr = array();
        //if($mainQryRows > 0) {
        while ($mainQryRes = imw_fetch_array($pat_ch_rs)) {

			$printFile = true;
			$patient_id = $mainQryRes['patient_id'];
			$primaryProviderId = $mainQryRes['primaryProviderId'];
			$facilityId = $mainQryRes['facility_id'];
			$encounter_id = $mainQryRes['encounter_id'];
			$charge_list_id = $mainQryRes['charge_list_id'];
			$chgdetid = $mainQryRes['charge_list_detail_id'];
			
			$firstGrpId=$primaryProviderId;
			if($view_by=='facility' || $view_by=='fac_n_oper'){
				$firstGrpId=$facilityId;
			}
			
			if($view_by=='fac_n_oper'){
				
				foreach($arrRefundAmt[$chgdetid] as $oprid => $ref_amt){
					if(strtolower($summary_detail) == 'summary') {
						$ovr_pay_arr[$firstGrpId][$oprid]['refundAmt'] += $ref_amt;
						$arrRefundData[$firstGrpId][$oprid]['refund'] += $ref_amt;
					} else {
						$ovr_pay_arr[$firstGrpId][$oprid][$encounter_id][] = $mainQryRes;
					}
	
					$arrRefundData[$firstGrpId][$oprid]['charges'] += $mainQryRes['totalAmount'];
					$arrRefundData[$firstGrpId][$oprid]['payment'] += $mainQryRes['paidForProc'];

					$patientMainDataArr[$patient_id]['patient_name'] =core_name_format($mainQryRes['lname'], $mainQryRes['fname'], $mainQryRes['mname']);
				}
			}else{
				if (strtolower($summary_detail) == 'summary') {
					if (!in_array($encounter_id, $arrCheckEncs)) {
						$ovr_pay_arr[$firstGrpId][$facilityId]['refundAmt'] += $arrRefundAmt[$encounter_id];
						$arrRefundData[$firstGrpId]['refund'] += $arrRefundAmt[$encounter_id];
						$arrCheckEncs[$encounter_id] = $encounter_id;
					}
				} else {
					$ovr_pay_arr[$firstGrpId][$encounter_id][] = $mainQryRes;
				}

				$prov_name=core_name_format($mainQryRes['pro_lname'], $mainQryRes['pro_fname'], '');
				$provider_name_arr[$primaryProviderId] = $prov_name;

				$arrRefundData[$firstGrpId]['charges'] += $mainQryRes['totalAmount'];
				$arrRefundData[$firstGrpId]['payment'] += $mainQryRes['paidForProc'];
				$arrRefundData[$firstGrpId]['name'] = $prov_name;
				$patientMainDataArr[$patient_id]['patient_name'] =core_name_format($mainQryRes['lname'], $mainQryRes['fname'], $mainQryRes['mname']);
			}
		}
    }


    // GET REFUND FROM CI/CO AND PRE PAYMENTS
    $qry = "Select ciRef.id, ciRef.ci_co_id, ciRef.pmt_id, ciRef.ref_amt, ciRef.payment_method, ciRef.patient_id, 
	DATE_FORMAT(ciRef.entered_date, '" . get_sql_date_format() . "') as 'entered_date', ciRef.entered_by  
	FROM ci_pmt_ref ciRef WHERE del_status='0' AND (entered_date BETWEEN '$startDate' AND '$endDate')";
    if (empty($operator) === false) {
        $qry .= " AND ciRef.entered_by IN(" . $operator . ")";
    }
    $rs = imw_query($qry);

    while ($res = imw_fetch_array($rs)) {
        $id = $res['id'];
		$oprid=$res['entered_by'];
        
		if($view_by=='fac_n_oper'){	
			if($res['ci_co_id'] > 0) {
				$tempCICOIds[$res['ci_co_id']] = $res['ci_co_id'];
				$arrCICORefundsDet[$res['ci_co_id']][$oprid][$id]['ref_amt'] = $res['ref_amt'];
				$arrCICORefundsDet[$res['ci_co_id']][$oprid][$id]['method'] = $res['payment_method'];
				$arrCICORefundsDet[$res['ci_co_id']][$oprid][$id]['ref_date'] = $res['entered_date'];
				$arrCICORefundsDet[$res['ci_co_id']][$oprid][$id]['opr_id'] = $oprid;
				$tempCICOForSummary[$res['ci_co_id']][$oprid] += $res['ref_amt'];
			} else if ($res['pmt_id'] > 0) {
				$tempPMTIds[$res['pmt_id']] = $res['pmt_id'];
				$arrPMTRefundsDet[$res['pmt_id']][$oprid][$id]['ref_amt'] = $res['ref_amt'];
				$arrPMTRefundsDet[$res['pmt_id']][$oprid][$id]['method'] = $res['payment_method'];
				$arrPMTRefundsDet[$res['pmt_id']][$oprid][$id]['ref_date'] = $res['entered_date'];
				$arrPMTRefundsDet[$res['pmt_id']][$oprid][$id]['opr_id'] = $oprid;
				$tempPMTForSummary[$res['pmt_id']][$oprid] += $res['ref_amt'];
			}
		}else{
			if($res['ci_co_id'] > 0) {
				$tempCICOIds[$res['ci_co_id']] = $res['ci_co_id'];
				$arrCICORefundsDet[$res['ci_co_id']][$id]['ref_amt'] = $res['ref_amt'];
				$arrCICORefundsDet[$res['ci_co_id']][$id]['method'] = $res['payment_method'];
				$arrCICORefundsDet[$res['ci_co_id']][$id]['ref_date'] = $res['entered_date'];
				$tempCICOForSummary[$res['ci_co_id']] += $res['ref_amt'];
			} else if ($res['pmt_id'] > 0) {
				$tempPMTIds[$res['pmt_id']] = $res['pmt_id'];
				$arrPMTRefundsDet[$res['pmt_id']][$id]['ref_amt'] = $res['ref_amt'];
				$arrPMTRefundsDet[$res['pmt_id']][$id]['method'] = $res['payment_method'];
				$arrPMTRefundsDet[$res['pmt_id']][$id]['ref_date'] = $res['entered_date'];
				$tempPMTForSummary[$res['pmt_id']] += $res['ref_amt'];
			}
		}
    }
    unset($rs);

    // CI/CO RECORDS
    if (sizeof($tempCICOIds) > 0) {
        $strCICOIds = implode(',', $tempCICOIds);
        unset($tempCICOIds);

        $qry = "Select cicoDet.id, cicoDet.item_payment, cicoPay.patient_id, DATE_FORMAT(cicoPay.created_on, '" . get_sql_date_format() . "') as 'created_on', schAppt.sa_doctor_id, sa_facility_id,
		pd.lname, pd.fname, pd.mname  
		FROM check_in_out_payment_details cicoDet 
		LEFT JOIN check_in_out_payment cicoPay ON cicoPay.payment_id = cicoDet.payment_id 
		LEFT JOIN schedule_appointments schAppt ON schAppt.id = cicoPay.sch_id 
		LEFT JOIN patient_data pd ON pd.id = cicoPay.patient_id  
		WHERE cicoDet.id IN(" . $strCICOIds . ")";
        if (empty($Physician) == false) {
            $qry .= " AND schAppt.sa_doctor_id IN ($Physician)";
        }
        if (empty($strSelFacilities) == false) {
            $qry .= " AND schAppt.sa_facility_id IN ($strSelFacilities)";
        }
        if (empty($patientId) === false) {
            $startLname = '';
            $qry .= " AND cicoPay.patient_id in ($patientId)";
        } else if (trim($startLname) != '') {
            $qry .= " AND (trim(pd.lname) between '$startLname' and '$endLname' 
					OR trim(pd.lname) like '$endLname%')";
        }
        $qry .= " ORDER BY pd.lname, pd.fname";
        $rs = imw_query($qry);

        while ($res = imw_fetch_array($rs)) {
            $printFile = true;
            $CICODetId = $res['id'];
            $pid = $res['patient_id'];
            $phyId = $res['sa_doctor_id'];
			$facId = ($arrPosFacAtFac[$res['sa_facility_id']]>0) ? $arrPosFacAtFac[$res['sa_facility_id']] : 0;

			$firstGrpId=$phyId;
			if($view_by=='facility' || $view_by=='fac_n_oper'){
				$firstGrpId=$facId;
			}
			
			if($view_by=='fac_n_oper'){
				foreach($arrCICORefundsDet[$CICODetId] as $ref_opr => $refundData){
					foreach($refundData as $ref_id => $refData){
						$arrMainOthers[$firstGrpId][$ref_opr][$pid] = $pid;
						$arrCICORefunds[$firstGrpId][$ref_opr][$pid][$CICODetId] = $facId;
						$arrPatDetail[$CICODetId]['fname'] = $res['fname'];
						$arrPatDetail[$CICODetId]['mname'] = $res['mname'];
						$arrPatDetail[$CICODetId]['lname'] = $res['lname'];
						$arrPatDetail[$CICODetId]['pay_date'] = $res['created_on'];
						$arrPatDetail[$CICODetId][$ref_opr]['pay_amt'] += $res['item_payment'];
			
						//FOR SUMMARY
						if(!$tempIdCheck[$CICODetId]){
							$arrRefundData[$firstGrpId][$ref_opr]['payment'] += $res['item_payment'];
							
							$tempIdCheck[$CICODetId]=$CICODetId;
						}
						$arrRefundData[$firstGrpId][$ref_opr]['refund'] += $tempCICOForSummary[$CICODetId][$ref_opr];
					}
				}
			}else{
				$arrMainOthers[$firstGrpId][$pid] = $pid;
				$arrCICORefunds[$firstGrpId][$pid][$CICODetId] = $facId;
				$arrPatDetail[$CICODetId]['fname'] = $res['fname'];
				$arrPatDetail[$CICODetId]['mname'] = $res['mname'];
				$arrPatDetail[$CICODetId]['lname'] = $res['lname'];
				$arrPatDetail[$CICODetId]['pay_date'] = $res['created_on'];
				$arrPatDetail[$CICODetId]['pay_amt'] += $res['item_payment'];
	
				//FOR SUMMARY
				$arrRefundData[$firstGrpId]['payment'] += $res['item_payment'];
				$arrRefundData[$firstGrpId]['refund'] += $tempCICOForSummary[$CICODetId];
			}
		}
        unset($rs);
    }

    // PRE-PAMENT RECORDS
    if (sizeof($tempPMTIds) > 0) {
        $tempIdCheck=array();
		$strPMTIds = implode(',', $tempPMTIds);
        unset($tempPMTIds);

        $qry = "Select patPrePay.id, patPrePay.patient_id, patPrePay.patient_id, patPrePay.facility_id, patPrePay.paid_amount,
		DATE_FORMAT(patPrePay.paid_date, '" . get_sql_date_format() . "') as 'paid_date', patPrePay.provider_id, 
		pd.lname, pd.fname, pd.mname FROM patient_pre_payment patPrePay 
		LEFT JOIN patient_data pd ON pd.id = patPrePay.patient_id 
		WHERE patPrePay.id IN(" . $strPMTIds . ")";
        if (empty($Physician) == false) {
            $qry .= " AND patPrePay.provider_id IN ($Physician)";
        }
        if (empty($strSelFacilities) == false) {
            $qry .= " AND patPrePay.facility_id IN ($strSelFacilities)";
        }
        if (empty($patientId) === false) {
            $startLname = '';
            $qry .= " and patPrePay.patient_id in ($patientId)";
        } else if (trim($startLname) != '') {
            $qry .= " and (trim(pd.lname) between '$startLname' and '$endLname' 
					or trim(pd.lname) like '$endLname%')";
        }
        $qry .= " ORDER BY pd.lname, pd.fname";
        $rs = imw_query($qry);

        while ($res = imw_fetch_array($rs)) {
            $printFile = true;
            $pmtId = $res['id'];
            $pid = $res['patient_id'];
            $phyId = $res['provider_id'];
			$facId = ($arrPosFacAtFac[$res['facility_id']]>0) ? $arrPosFacAtFac[$res['facility_id']] : 0;

			$firstGrpId=$phyId;
			if($view_by=='facility' || $view_by=='fac_n_oper'){
				$firstGrpId=$facId;
			}

			if($view_by=='fac_n_oper'){
				foreach($arrPMTRefundsDet[$pmtId] as $ref_opr => $refundData){
					foreach($refundData as $ref_id => $refData){
						$ref_opr=$refData['opr_id'];
						$arrMainOthers[$firstGrpId][$ref_opr][$pid] = $pid;
						$arrPMTRefunds[$firstGrpId][$ref_opr][$pid][$pmtId] = $facId;
						$arrPatDetail[$pmtId]['fname'] = $res['fname'];
						$arrPatDetail[$pmtId]['mname'] = $res['mname'];
						$arrPatDetail[$pmtId]['lname'] = $res['lname'];
						$arrPatDetail[$pmtId]['pay_date'] = $res['paid_date'];
						$arrPatDetail[$pmtId][$ref_opr]['pay_amt'] += $res['paid_amount'];							
			
						//FOR SUMMARY
						if(!$tempIdCheck[$pmtId]){
							$arrRefundData[$firstGrpId][$ref_opr]['payment'] += $res['paid_amount'];
							$tempIdCheck[$pmtId]=$pmtId;
						}
						$arrRefundData[$firstGrpId][$ref_opr]['refund'] += $tempPMTForSummary[$pmtId][$ref_opr];
					}
				}
			}else{
				$arrMainOthers[$firstGrpId][$pid] = $pid;
				$arrPMTRefunds[$firstGrpId][$pid][$pmtId] = $facId;
				$arrPatDetail[$pmtId]['fname'] = $res['fname'];
				$arrPatDetail[$pmtId]['mname'] = $res['mname'];
				$arrPatDetail[$pmtId]['lname'] = $res['lname'];
				$arrPatDetail[$pmtId]['pay_date'] = $res['paid_date'];
				$arrPatDetail[$pmtId]['pay_amt'] += $res['paid_amount'];
	
				//FOR SUMMARY
				$arrRefundData[$firstGrpId]['payment'] += $res['paid_amount'];
				$arrRefundData[$firstGrpId]['refund'] += $tempPMTForSummary[$pmtId];
				
			}
        }
        unset($rs);
    }
    //--------------------------------------------


	//SORTING OF FACILITY AND OPERATOR FOR SUMMARY VIEW
	if($view_by=='fac_n_oper' && sizeof($arrRefundData)>0){
		$tempData=$arrRefundData;
		unset($arrRefundData);
		
		foreach($posFacilityArr as $facId =>$fac_name){
			if(sizeof($tempData[$facId])>0){
				foreach($userNameArr as $oprid =>$opr_name){
					
					if($tempData[$facId][$oprid]){
						$arrRefundData[$facId][$oprid]=$tempData[$facId][$oprid];
					}
				}
			}
		}
	}

    $group_name = $CLSReports->report_display_selected($grp_id, 'group', 1, $grp_cnt);
    $practice_name = $CLSReports->report_display_selected($sc_name,'facility',1,$posfac_cnt);
	$physician_name = $CLSReports->report_display_selected($Physician, 'physician', 1, $phy_cnt);
	$operator_name = $CLSReports->report_display_selected($operator, 'operator', 1, $opr_cnt);

    if ($patientId > 0) {
        $pat_nam_srh = (isset($patientMainDataArr[$patientId]['patient_name'])) ? $patientMainDataArr[$patientId]['patient_name'] . ' - ' . $patientId : $patientId;
    } else {
        $pat_nam_srh = "Last Name From " . $startLname . " To " . $endLname;
    }


    $main_provider_arr = array_keys($ovr_pay_arr);
    $op = 'l';


    //Include files for Detail and Summary view
    if (strtolower($summary_detail) == 'summary') {
        $op = 'p';
        require_once(dirname(__FILE__) . '/refund_report_summary.php');
    } else {
        require_once(dirname(__FILE__) . '/refund_report_detail.php');
    }
    $conditionChk = true;
}


if ($printFile == true && $csvFileData != "") {
    $op='l';
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$csvFileData;

    $stylePDF = '<style>' . file_get_contents('css/reports_pdf.css') . '</style>';
    $strHTML = $stylePDF . $pdfData;

    $file_location = write_html($strHTML, 'refund_report.html');
    
	if($callFrom=='scheduled'){
		$csv_file_name = write_html($csv_file_data, 'refund_report_csv.html');
	}
    
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