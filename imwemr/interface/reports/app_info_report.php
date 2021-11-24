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
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot']."/library/classes/scheduler/appt_schedule_functions.php");
require_once($GLOBALS['fileroot']."/library/classes/scheduler/appt_ac_functions.php");
require_once($GLOBALS['fileroot']."/library/classes/acc_functions.php");
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');//to get html save location
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');//function to write html

if(count($_REQUEST['insuranceGrp']) > 1){
	$displayInsGrp = "Multiple";
}else{
	$displayInsGrp = $_REQUEST['slectedIns'];
}
$qryDateFormat = get_sql_date_format();
//scheduler object
$obj_scheduler = new appt_scheduler();
ob_start();
//getting report generator name
$report_generator_name = "";
$mor_appt_flag = $eve_appt_flag = 0;
// SITE ARRAY
$arr_site = $obj_scheduler->eye_site();
$dtEffectiveDate = getDateFormatDB($Start_date);

$strProviderIds = join(',',$phyId);
$form_submit = $_REQUEST['form_submitted'];

if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
	$op_name_arr = preg_split("/, /",$_SESSION["authProviderName"]);
	$report_generator_name = $op_name_arr[1][0];
	$report_generator_name .= $op_name_arr[0][0];
}

function calculate_age_from_dob($yyyymmdd_dob){
	list($year, $month, $day) = explode ('-', $yyyymmdd_dob);
	$year_diff = date('Y') - $year;
	
	if(date("m") < $month || (date("m") == $month && date("d") < $day)){
		$year_diff--;
	}
	
	return $year_diff;
}
//changing date format
	$dtDBEffectDate=$dtEffectiveDate;

//changing date format
//changing date format
$bl_dt_range_search = false;
if(isset($End_date) && $End_date != ""){
	$dtEffectiveDate2 = getDateFormatDB($End_date);	
	$dtDBEffectDate2=$dtEffectiveDate2;
	$intTimeStamp2 = mktime(0, 0, 0, $m2, $d2, $y2);
	$dtShowEffectDate2 = date("m/d/Y", $intTimeStamp2);

	$bl_dt_range_search = true;
}else{
	$dtDBEffectDate2 = $dtDBEffectDate;
	$intTimeStamp2 = $intTimeStamp;
	$dtEffectiveDate2 = $dtEffectiveDate;
}

//getting selected facility ids
$strFacilityIds = join(',',$facility_name);

$facQry = "select pos_facilityies_tbl.facilityPracCode as name,
			pos_facilityies_tbl.pos_facility_id as id,
			pos_tbl.pos_prac_code
			from pos_facilityies_tbl
			left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
			order by pos_facilityies_tbl.headquarter desc,
			pos_facilityies_tbl.facilityPracCode";
$facSql = imw_query($facQry);
while($facRt = imw_fetch_array($facSql)){
	$fac_arr[] = $facRt['id'];
	$fac_name_arr[$facRt['id']]=$facRt['name'] .' - '. $facRt['pos_prac_code'];
}

//getting selected report time period

$strMidDay = $_REQUEST['day'];
//getting selected provider ids
if(empty($strProviderIds)){
	if($all_prov != ""){
		$strProviderIds = $all_prov;
	}else{
		$provQry = "select id from users where Enable_Scheduler = 1 and delete_status='0'";
		$provSql = imw_query($provQry);
		while($provRt = imw_fetch_array($provSql)){
			$provid[] = $provRt['id'];
		}
		$strProviderIds = implode(", ",$provid);
	}
}

$blIncludePatientAddress = false;	
if(isset($_REQUEST['inc_demographics']) && $_REQUEST['inc_demographics'] == 1){
	$blIncludePatientAddress = true;
}
$blSortPatientLName = false;
if(isset($_REQUEST['sort_patient_last_name']) && $_REQUEST['sort_patient_last_name'] == 1){
	$blSortPatientLName = true;
}
$blIncludePatientKey = false;
if(isset($_REQUEST['inc_portal_key']) && $_REQUEST['inc_portal_key'] == 1){
	$blIncludePatientKey = true;
}
$start_head_page = "<page_header>";
$end_head_page = "</page_header>";
$start_page = "</page>";
$end_page = "<page pageset=\"old\"><page_footer>
				<table style=\"width: 100%;\">
					<tr>
						<td style=\"text-align: center;	width: 100%\">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>";
if(isset($_REQUEST['printWithoutPageBreak']) && $_REQUEST['printWithoutPageBreak'] == 1){
	$start_page = "";
	$end_page = "";
}
if($form_submit){
	//COPAY POLICIES
	$rs=imw_query("Select vip_copay_not_collect FROM copay_policies LIMIT 0,1");
	$res=imw_fetch_array($rs);
	$vipCopayCollect= $res['vip_copay_not_collect'];
	
	//getting facilities
	$arr_facilities =$comboFacArr= array();
	$strFacQry = "SELECT id, name FROM facility";
	$rsFacData = imw_query($strFacQry);
	while($tmpData=imw_fetch_assoc($rsFacData))
	{
		$arrFacData[] = $tmpData;
	}
	for($f = 0; $f < count($arrFacData); $f++){
		$arr_facilities[$arrFacData[$f]["id"]] = $arrFacData[$f]["name"];
		$comboFacArr[]=$arrFacData[$f]["id"];
	}
	if(trim($strFacilityIds) == ""){
		$strFacilityIds = join(',',(array)$comboFacArr);
	}
	
	$strInsuranceIds='';
	if(sizeof($insuranceGrp)>0){
		$str=implode(',',$insuranceGrp);
		$arr=explode(',',$str);
		$arr=array_unique($arr);
		$strInsuranceIds=implode(',',$arr);
	}


	//getting schedule appointments
	$strQry = "SELECT DISTINCT(sa.id) as saAppId, sa.case_type_id, sa.sa_facility_id, p.ss, p.sex, p.EMR, p.erx_patient_id, p.erx_entry, CONCAT(p.street, p.street2, '<br>',
	p.city, ', ', p.state, ' ', p.postal_code) as patientAddress, p.temp_key as patientkey, sa.sa_madeby,
	pt.test_name ,CONCAT_WS(',',sp1.acronym,sp2.acronym,sp3.acronym) as acronym, sa.sa_app_duration, 
	sa.sa_comments, sa.procedure_site, p.DOB, CONCAT(p.lname,', ',p.fname,' ',p.mname) as pname, sa.sa_patient_id,
	DATE_FORMAT(p.DOB,'".$qryDateFormat."') as date_of_birth, p.phone_home, p.phone_biz, p.phone_cell, p.vip,
	u.lname, u.fname, u.mname, CONCAT(SUBSTRING(o.fname,1,1),SUBSTRING(o.lname,1,1)) as oname,
	sa.sa_doctor_id, TIME_FORMAT(sa.sa_app_starttime,'%h:%i %p') selStartTime, sa.sa_app_start_date,
	DATE_FORMAT(st.fldLunchStTm,'%H:%i:%s') as fldLunchStTm, DATE_FORMAT(st.fldLunchEdTm,'%H:%i:%s') as fldLunchEdTm,
	(SELECT SUM(pat_due) FROM patient_charge_list_details pcld WHERE pcld.patient_id = sa.sa_patient_id AND pcld.del_status='0' ) as  pt_due,
	if(st.fldLunchStTm,if(TIME_FORMAT(sa.sa_app_starttime,'%H:%i:%s') < DATE_FORMAT(st.fldLunchStTm,'%H:%i:%s'),'Mor','Eve'),if(TIME_FORMAT(sa.sa_app_starttime,'%H:%i:%s') < '12:00:00', 'Mor', 'Eve')) as tm_status, p.primary_care as refPhysician, p.primary_care_id, p.primary_care_phy_name as priCarePhysician,sa.sa_patient_app_status_id,p.primary_care_phy_id,
	u.user_type, sa.arrival_time
	FROM schedule_appointments sa 
	LEFT JOIN facility f ON f.id = sa.sa_facility_id 
	LEFT JOIN users u ON u.id = sa.sa_doctor_id 
	LEFT JOIN users o ON (o.username = sa.sa_madeby AND o.username != '') 
	LEFT JOIN patient_data p ON p.id = sa.sa_patient_id  
	LEFT JOIN slot_procedures sp1 ON sp1.id = sa.procedureid 
	LEFT JOIN slot_procedures sp2 ON sp2.id = sa.sec_procedureid 
	LEFT JOIN slot_procedures sp3 ON sp3.id = sa.tertiary_procedureid 
	LEFT JOIN patient_tests pt ON (pt.id = sa.sa_test_id AND pt.facility = sa.sa_facility_id) 
	LEFT JOIN schedule_templates st ON st.id = sa.sch_template_id 
	WHERE 
	sa_facility_id IN (".$strFacilityIds.") 
	AND (sa_app_start_date BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate2."') 
	AND sa_doctor_id IN (".$strProviderIds.") 
	AND sa.sch_template_id != 0 ";
	$strReportFullOrHalf = "Full Day";
	if($strMidDay == "morning"){
		$strReportFullOrHalf = "Morning";
		$strQry .= " AND IF(st.fldLunchStTm, (TIME_FORMAT(sa.sa_app_starttime,'%H:%i:%s') < DATE_FORMAT(st.fldLunchStTm,'%H:%i:%s')), (TIME_FORMAT(sa.sa_app_starttime,'%H:%i:%s') < '12:00:00') ) ";
	}
	if($strMidDay == "afternoon"){
		$strReportFullOrHalf = "Afternoon";
		$strQry .= " AND IF(st.fldLunchStTm, (TIME_FORMAT(sa.sa_app_starttime,'%H:%i:%s') >= DATE_FORMAT(st.fldLunchStTm,'%H:%i:%s')), (TIME_FORMAT(sa.sa_app_starttime,'%H:%i:%s') >= '12:00:00') ) ";
	}
	$strQry .= " GROUP BY sa.id ";
	if($blSortPatientLName == true){
		$strQry .= " ORDER BY sa_doctor_id ASC,p.lname ASC, sa_app_start_date ASC, sa_app_starttime ASC";	
	}else{
		// sa_facility_id ASC, 
		$strQry .= " ORDER BY sa_doctor_id ASC, sa_facility_id ASC, sa_app_start_date ASC, sa_app_starttime ASC";	
	}
	
	//echo $strQry;
	$resAppts = array();
	$resApptsRes = imw_query($strQry);
	while($tmpData=imw_fetch_assoc($resApptsRes))
	{
		$resAppts[] = $tmpData;
	}
	
	$intTotAppts = count($resAppts);

	//GETTING PAT IDS
	$arr_pt_id = array();
	$patientCopay=array();
	foreach($resAppts as $thisPtId){
		$isVip=0;
		$schId=$thisPtId["saAppId"];
		$pid= $thisPtId["sa_patient_id"];
		
		$priInsId=0;
		$arr_pt_id[$thisPtId["sa_patient_id"]] = $thisPtId["sa_patient_id"];
		$arr_pt_apptdate[$thisPtId["sa_patient_id"]][$thisPtId["sa_app_start_date"]][]=$thisPtId["sa_app_start_date"];
		$arr_pt_apptid[$thisPtId["sa_patient_id"]][$thisPtId["sa_app_start_date"]][]=$schId;
		$arrAllSchIds[$schId]=$schId;
		
		if($vipCopayCollect==1 && $thisPtId["vip"]==1){
			$arrPatCopay[$pid][$schId]['COPAY_AMT']='VIP';
			$isVip=1;
		}
		
		if($isVip==0){
			//PRIMARY COPAY
			$qry="Select provider, type, copay, ins_caseid, copay_type, co_ins FROM insurance_data WHERE pid='".$thisPtId["sa_patient_id"]."' 
			AND type='primary'";
			if($thisPtId["case_type_id"]!='0'){
				$qry.=" AND ins_caseid='".$thisPtId["case_type_id"]."'";
			}
			$qry.=" ORDER BY actInsComp DESC LIMIT 0,1";
			$rs=imw_query($qry);
			$res=imw_fetch_array($rs);
			$priInsId=$res['provider'];
			$copayVisit=$copayTest=0;
			if($thisPtId["case_type_id"]!='0' && $res['ins_caseid']==$thisPtId["case_type_id"]){
				// user type=1=Physician / user type=5=Test / copay type=2=office/test
				$copayVisit=$res['copay'];
				if($res['co_ins']!='' && $res['co_ins']!='00/00'){
					$coParts=explode('/', $res['co_ins']);
					$copayVisit=$coParts[0];
					$copayTest=$coParts[1];
				}
				if($thisPtId["user_type"]=='1'){
					$arrPatCopay[$pid][$schId]['COPAY_AMT']=$copayVisit;
				}
				if($thisPtId["user_type"]!='1'){
					if($res['copay_type']!='2' || $copayTest<=0){ $copayTest=$copayVisit; }
					$arrPatCopay[$pid][$schId]['COPAY_AMT']=$copayTest;
				}
				
			}else if($thisPtId["case_type_id"]=='0'){
				//set default
				$rsCase=imw_query("Select ins_caseid FROM insurance_case WHERE patient_id ='".$thisPtId["sa_patient_id"]."'");
				if(imw_num_rows($rsCase)>1){
					$rsDefault=imw_query("Select insurance_data.copay, insurance_data.copay_type, insurance_data.co_ins FROM insurance_case_types JOIN insurance_case 
					ON insurance_case.ins_case_type=insurance_case_types.case_id 
					JOIN insurance_data ON insurance_data.ins_caseid=insurance_case.ins_caseid 
					WHERE insurance_data.pid=".$thisPtId["sa_patient_id"]." AND insurance_data.type='primary' ORDER BY insurance_case_types.normal DESC, insurance_data.actInsComp DESC LIMIT 0,1");
					$resDefault=imw_fetch_array($rsDefault);
					// user type=1=Physician / user type=5=Test / copay type=2=office/test
					$copayVisit=$resDefault['copay'];
					if($resDefault['co_ins']!='' && $resDefault['co_ins']!='00/00'){
						$coParts=explode('/', $resDefault['co_ins']);
						$copayVisit=$coParts[0];
						$copayTest=$coParts[1];
					}
					if($thisPtId["user_type"]=='1'){
						$arrPatCopay[$pid][$schId]['COPAY_AMT']=$copayVisit;
					}
					if($thisPtId["user_type"]!='1'){
						if($res['copay_type']!='2' || $copayTest<=0){ $copayTest=$copayVisit; }
						$arrPatCopay[$pid][$schId]['COPAY_AMT']=$copayTest;
					}
				}
				unset($rsDefault);
			}
	
			if($priInsId>0){
				$copay_policies = ChkSecCopay_collect($priInsId);
				$copayVisit=$copayTest=0;
				if($copay_policies=='Yes'){
					//SECONDAY COPAY
					$qry="Select provider, type, copay, ins_caseid, copay_type, co_ins FROM insurance_data WHERE pid='".$thisPtId["sa_patient_id"]."' 
					AND type='secondary'"; 
					if($thisPtId["case_type_id"]!='0'){
						$qry.=" AND ins_caseid='".$thisPtId["case_type_id"]."'";
					}else{
						$qry.=" ORDER BY actInsComp DESC LIMIT 0,1";
					}
					$rs=imw_query($qry);
					$res=imw_fetch_array($rs);
					if($thisPtId["case_type_id"]!='0' && $res['ins_caseid']==$thisPtId["case_type_id"]){
						// user type=1=Physician / user type=5=Test / copay type=2=office/test
						$copayVisit=$res['copay'];
						if($res['co_ins']!='' && $res['co_ins']!='00/00'){
							$coParts=explode('/', $res['co_ins']);
							$copayVisit=$coParts[0];
							$copayTest=$coParts[1];
						}
						if($thisPtId["user_type"]=='1'){
							$arrPatCopay[$pid][$schId]['COPAY_AMT']+=$copayVisit;
						}
						if($thisPtId["user_type"]!='1'){
							if($res['copay_type']!='2' || $copayTest<=0){ $copayTest=$copayVisit; }
							$arrPatCopay[$pid][$schId]['COPAY_AMT']+=$copayTest;
						}					
					}else if($thisPtId["case_type_id"]=='0'){
						//set default
						$rsCase=imw_query("Select ins_caseid 	FROM insurance_case WHERE patient_id ='".$thisPtId["sa_patient_id"]."'");
						if(imw_num_rows($rsCase)>1){
							$rsDefault=imw_query("Select insurance_data.copay, insurance_data.copay_type, insurance_data.co_ins FROM insurance_case_types JOIN insurance_case 
							ON insurance_case.ins_case_type=insurance_case_types.case_id 
							JOIN insurance_data ON insurance_data.ins_caseid=insurance_case.ins_caseid 
							WHERE insurance_data.pid=".$thisPtId["sa_patient_id"]." AND insurance_data.type='secondary' ORDER BY insurance_case_types.normal DESC, insurance_data.actInsComp DESC LIMIT 0,1");
							$resDefault=imw_fetch_array($rsDefault);
							// user type=1=Physician / user type=5=Test / copay type=2=office/test
							$copayVisit=$resDefault['copay'];
							if($resDefault['co_ins']!='' && $resDefault['co_ins']!='00/00'){
								$coParts=explode('/', $resDefault['co_ins']);
								$copayVisit=$coParts[0];
								$copayTest=$coParts[1];
							}
							if($thisPtId["user_type"]=='1'){
								$arrPatCopay[$pid][$schId]['COPAY_AMT']+=$copayVisit;
							}
							if($thisPtId["user_type"]!='1'){
								if($res['copay_type']!='2' || $copayTest<=0){ $copayTest=$copayVisit; }
								$arrPatCopay[$pid][$schId]['COPAY_AMT']+=$copayTest;
							}						
						}
					}
					unset($rsDefault);
				}
			}
			if($arrPatCopay[$pid][$schId]['COPAY_AMT']>0){
				//GETTING PAID COPAY
				$rs=imw_query("Select SUM(payChgDet.paidForProc) as 'copayPaid' FROM patient_charge_list patChg 
				LEFT JOIN patient_chargesheet_payment_info payChg ON payChg.encounter_id=patChg.encounter_id 
				LEFT JOIN patient_charges_detail_payment_info payChgDet ON payChgDet.payment_id=payChg.payment_id 
				WHERE patChg.patient_id='".$pid."' AND patChg.date_of_service='".$thisPtId["sa_app_start_date"]."' 
				AND payChgDet.charge_list_detail_id='0' AND payChgDet.deletePayment='0' 
				GROUP BY payChgDet.payment_id");
				while($res=imw_fetch_array($rs)){
					$arrPatCopay[$pid][$schId]['COPAY_PAID']+=$res['copayPaid'];
				}
				unset($rs);		
			}
		}
	}
	$str_pt_id = implode(",",$arr_pt_id);
	
	//GETTING EXACT APPOINTMENT MADE DATE
	if(sizeof($arrAllSchIds)>0){
		$strAllSchIds=implode(',', $arrAllSchIds);
		$qry="Select sch_id, MIN(status_date), DATE_FORMAT(status_date, '".$qryDateFormat."') as appt_made_date FROM previous_status WHERE status ='0' AND sch_id IN(".$strAllSchIds.") GROUP BY sch_id";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$arrApptMadeDate[$res['sch_id']] = $res['appt_made_date'];
		}unset($rs);
	}
	
	if($intTotAppts > 0){
			
			//getting refferal# s 
			$strReffQry = "SELECT patient_id, ins_data_id, no_of_reffs, reff_used FROM `patient_reff` 
							WHERE patient_reff.patient_id IN (".$str_pt_id.") 
							and (
									(patient_reff.end_date >= current_date() and effective_date <= current_date()) 
									or 
									(no_of_reffs > 0)
								) 					
							order by patient_id, ins_data_id";
			$resReffRes = imw_query($strReffQry);
			$arr_reff = array();
			if($resReffRes){
				while($tmpData=imw_fetch_assoc($resReffRes))
				{$resReff[] = $tmpData;}
				if(count($resReff) > 0){
					foreach($resReff as $reff_details){
						$arr_reff[$reff_details["patient_id"]]["ins_data_id"] = $reff_details["ins_data_id"];
						$arr_reff[$reff_details["patient_id"]]["no_of_reffs"] = $reff_details["no_of_reffs"];
						$arr_reff[$reff_details["patient_id"]]["no_of_rused"] = $reff_details["reff_used"];
					}
				}
			}

			//getting ins details
			$strInsQry = "
			SELECT 
				insurance_data.provider, ict.normal, insurance_data.id, ics.ins_caseid,  insurance_data.pid, ict.case_name, ics.ins_case_type, insurance_data.type, ic.in_house_code, insurance_data.policy_number, insurance_data.copay, insurance_data.subscriber_relationship, 
				insurance_data.subscriber_lname, insurance_data.subscriber_mname, insurance_data.subscriber_fname,   insurance_data.subscriber_ss, DATE_FORMAT(insurance_data.subscriber_DOB, '".get_sql_date_format('','Y','/')."') as subscriber_DOB
			FROM 
				insurance_data
			LEFT JOIN
				insurance_companies AS ic ON insurance_data.provider = ic.id 
			LEFT JOIN 
				insurance_case ics ON ics.ins_caseid = insurance_data.ins_caseid 
			LEFT JOIN 
				insurance_case_types ict ON ics.ins_case_type = ict.case_id 
			WHERE 
				insurance_data.actInsComp = 1 
				AND insurance_data.pid IN (".$str_pt_id.") 
				AND ics.ins_case_type IS NOT NULL";
			if(empty($strInsuranceIds)==false){
				$strInsQry.=" AND insurance_data.provider IN(".$strInsuranceIds.")";
			}
			$strInsQry.= " ORDER BY insurance_data.pid, ics.ins_case_type, insurance_data.type ASC";
			$resInsRes = imw_query($strInsQry);
			$arr_ins = array();
			$arr_ins_case = array();
			if($resInsRes){
				while($tmpData=imw_fetch_assoc($resInsRes))
				{$resIns[] = $tmpData;}
				
				if(count($resIns) > 0){
					$abc = 0;
					foreach($resIns as $ins_details){
						$arr_ins_case[$ins_details["ins_caseid"]] = $ins_details["case_name"];
						
						$arr_ins[$ins_details["pid"]]["ins_details"][$ins_details["type"]][$ins_details["case_name"]]["ins_case"] = $ins_details["case_name"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$ins_details["type"]][$ins_details["case_name"]]["casetype"] = $ins_details["normal"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$ins_details["type"]][$ins_details["case_name"]]["ins_prid"] = $ins_details["id"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$ins_details["type"]][$ins_details["case_name"]]["ins_comp"] = $ins_details["in_house_code"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$ins_details["type"]][$ins_details["case_name"]]["policyno"] = $ins_details["policy_number"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$ins_details["type"]][$ins_details["case_name"]]["copayamt"] = $ins_details["copay"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$ins_details["type"]][$ins_details["case_name"]]["type"] = $ins_details["type"];
						
						//embedding ref count in insurance details
						$arr_ins[$ins_details["pid"]]["ins_details"][$ins_details["type"]][$ins_details["case_name"]][$ins_details["type"]][$ins_details["case_name"]]["refcount"] = 0;
						$arr_ins[$ins_details["pid"]]["ins_details"][$ins_details["type"]][$ins_details["case_name"]][$ins_details["type"]][$ins_details["case_name"]]["reffused"] = 0;
						if(isset($arr_reff[$ins_details["pid"]]["no_of_reffs"]) && $arr_reff[$ins_details["pid"]]["no_of_reffs"] > 0){
							if($arr_reff[$ins_details["pid"]]["ins_data_id"] == $ins_details["id"]){
								$arr_ins[$ins_details["pid"]]["ins_details"][$ins_details["type"]][$ins_details["case_name"]]["refcount"] = $arr_reff[$ins_details["pid"]]["no_of_reffs"];
								$arr_ins[$ins_details["pid"]]["ins_details"][$ins_details["type"]][$ins_details["case_name"]]["reffused"] = intval($arr_reff[$ins_details["pid"]]["no_of_rused"]);
							}
						}
						
						$arr_ins[$ins_details["pid"]]["ins_details"][$ins_details["type"]][$ins_details["case_name"]]["relat"] = $ins_details["subscriber_relationship"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$ins_details["type"]][$ins_details["case_name"]]["fname"] = $ins_details["subscriber_fname"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$ins_details["type"]][$ins_details["case_name"]]["mname"] = $ins_details["subscriber_mname"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$ins_details["type"]][$ins_details["case_name"]]["lname"] = $ins_details["subscriber_lname"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$ins_details["type"]][$ins_details["case_name"]]["rpdob"] = $ins_details["subscriber_DOB"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$ins_details["type"]][$ins_details["case_name"]]["rpssn"] = $ins_details["subscriber_ss"];

						$abc++;
					}
				}
			}
			
		//MAKING OUTPUT DATA
	$file_name="Appointment_Information_Report.csv";
	$csv_file_name= write_html("", $file_name);
	if(file_exists($csv_file_name)){
		unlink($csv_file_name);
	}
	$fp = fopen ($csv_file_name, 'a+');
	$arr=array();
	$arr[]="Appointment Information Report";
	$arr[]="Ins. Group: $displayInsGrp";
	$arr[]= 'From :' .get_date_format($dtEffectiveDate) .' To : '. get_date_format($dtEffectiveDate2);				
	$arr[]= 'Created by '.strtoupper($report_generator_name).' on '.get_date_format(date("Y-m-d"))." ".date("h:i A");
	fputcsv($fp,$arr, ",","\"");
	$fp = fopen ($csv_file_name, 'a+');	
		$cssHTML = '
			<style>
				.text_b_w{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#BCD5E1;
					border-style:solid;
					border-color:#FFFFFF;
					border-width: 1px; 
				}
				.tb_heading{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#FE8944;
				}
				.text_b{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#FFFFFF;
					background-color:#4684AB;
				}
				.text_b_date{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#F3F3F3;
				}				
				.text{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#FFFFFF;
				}
				.report_head_text{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#FFFFFF;
					color:#4684ab;
					font-weight:bold;
				}
				.valign{
					vertical-align:top;
				}
			</style>
			<page backtop="10mm" backbottom="10mm">			
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align: center;	width: 100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>';
			//<td style="text-align:left;" class="text_b_w" width="200">'.$first_fac_name.'</td>
		$strHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';
		$pdfHTML='<style>'.file_get_contents('css/reports_pdf.css').'</style>';	
		
		$strHTML .= '
			<table class="rpt_table rpt rpt_table-bordered rpt_padding">
				<tr >	
					<td class="rptbx1" style="width:25%">Appointment Information Report</td>
					<td class="rptbx2" style="width:25%">Ins. Group: '.$displayInsGrp.' </td>
					<td  class="rptbx3" style="text-align:center; width:25%">From : '.get_date_format($dtEffectiveDate) .' To : '. get_date_format($dtEffectiveDate2).'</td>
					<td class="rptbx1" style="text-align:center; width:25%">Created by '.strtoupper($report_generator_name).' on '.get_date_format(date("Y-m-d"))." ".date("h:i A").'&nbsp;</td>
				</tr>
			</table>';
		$pdfHTML .= '
			<table class="rpt_table rpt rpt_table-bordered rpt_padding" >
				<tr class="rpt_headers">	
					<td class="rptbx1" style="width:25%">Appointment Information Report</td>
					<td class="rptbx2" style="width:25%">Ins. Group: '.$displayInsGrp.'</td>
					<td class="rptbx3" style="width:25%">From : '.get_date_format($dtEffectiveDate) .' To : '. get_date_format($dtEffectiveDate2).'</td>
					<td class="rptbx1" style="width:25%">Created by '.strtoupper($report_generator_name).' on '.get_date_format(date("Y-m-d"))." ".date("h:i A").'&nbsp;</td>
				</tr>
			</table>';
		
		$arr=array();
		$arr[]="Appt Date";
		$arr[]="Appt ID";
		$arr[]="Patient ID";
		$arr[]="Appt Location";
		$arr[]="Rendering Physician";
		$arr[]="Ref. Phy.";
		$arr[]="Ref. Fax";
		$arr[]="Appt Notes";
		$arr[]="Appt Reason";
		$arr[]="Appt Disposition";
		$arr[]="Arrival Time";
		$arr[]="PCP";
		$arr[]="PCP Phone";
		$arr[]="Pri Coverage";
		$arr[]="Sec Coverage";
		$arr[]="Pri Ins. ID";
		$arr[]="Full Name";
		$arr[]="DOB";
		$arr[]="SSN";
		$arr[]="Home Phone";
		fputcsv($fp,$arr, ",","\"");
		$fp = fopen ($csv_file_name, 'a+');	
		
		$strHTML .= '
						<table class="rpt_table rpt rpt_table-bordered" width="100%">
						<tr>
							<td class="text_b_w" align="left" width="58">Appt Date</td>
							<td class="text_b_w" align="left" width="58">Appt ID</td>
							<td class="text_b_w" align="left" width="58">Patient ID</td>
							<td class="text_b_w" align="left" width="58">Appt Location</td>
							<td class="text_b_w" align="left" width="58">Rendering Physician</td>
							<td class="text_b_w" align="left" width="58">Ref. Phy.</td>
							<td class="text_b_w" align="left" width="58">Ref. Fax</td>
							<td class="text_b_w" align="left" width="58">Appt Notes</td>
							<td class="text_b_w" align="left" width="58">Appt Reason</td>
							<td class="text_b_w" align="left" width="58">Appt Disposition</td>
							<td class="text_b_w" align="left" width="58">Arrival Time</td>
							<td class="text_b_w" align="left" width="58">PCP</td>
							<td class="text_b_w" align="left" width="58">PCP Phone</td>
							<td class="text_b_w" align="left" width="58">Pri Coverage</td>
							<td class="text_b_w" align="left" width="58">Sec Coverage</td>
							<td class="text_b_w" align="left" width="58">Pri Ins. ID</td>
							<td class="text_b_w" align="left" width="58">Full Name</td>
							<td class="text_b_w" align="left" width="58">DOB</td>
							<td class="text_b_w" align="left" width="58">SSN</td>
							<td class="text_b_w" align="left" width="58">Home Phone</td></tr>
							';	
		$pdfHTML .= '
						<table class="rpt_table rpt rpt_table-bordered" width="100%">
						<tr>
							<td class="text_b_w" align="left" width="55">Appt Date</td>
							<td class="text_b_w" align="left" width="55">Appt ID</td>
							<td class="text_b_w" align="left" width="65">Patient ID</td>
							<td class="text_b_w" align="left" width="115">Appt Reason</td>
							<td class="text_b_w" align="left" width="105">PCP</td>
							<td class="text_b_w" align="left" width="115">Pri Coverage</td>
							<td class="text_b_w" align="left" width="105">Pri Ins. ID</td>
							<td class="text_b_w" align="left" width="115">Full Name</td>
							<td class="text_b_w" align="left" width="55">DOB</td>
							<td class="text_b_w" align="left" width="70">SSN</td>
							<td class="text_b_w" align="left" width="70">Home Phone</td></tr>
							';					
		
		$intTempProviderId = 0;
		$intTempFacilityId = 0;
		$dtTempApptDate = "";
		$intDateApptNo = 0;
		
				
		for($i = 0; $i < $intTotAppts; $i++){
			
			if(empty($strInsuranceIds)==true || (empty($strInsuranceIds)==false && $arr_ins[$resAppts[$i]['sa_patient_id']])){
				
				//appt date
				$dtApptDate = $resAppts[$i]['sa_app_start_date'];

				$saAppId = $resAppts[$i]['saAppId'];
				$ps_qry = "SELECT CONCAT(SUBSTRING(o.fname,1,1),SUBSTRING(o.lname,1,1)) as uname FROM previous_status ps LEFT JOIN users o ON o.username = ps.oldMadeBy  WHERE ps.sch_id = '".$saAppId."' order by ps.id ASC LIMIT 1";
				$ps_qry_obj = imw_query($ps_qry);
				$ps_qry_obj_data = imw_fetch_assoc($ps_qry_obj);		
				$ps_user = $ps_qry_obj_data['uname'];

				//appt case #, if any
				$insApptCaseId = $resAppts[$i]['case_type_id'];
				$insApptCaseId = $resAppts[$i]['case_type_id'];

				//provider
				$intProviderId = $resAppts[$i]['sa_doctor_id'];
				if($resAppts[$i]['lname'] != ""){
					$strProviderNm = $resAppts[$i]['lname'].", ".$resAppts[$i]['fname']." ".$resAppts[$i]['mname'];
				}else{
					$strProviderNm = $resAppts[$i]['fname'];
				}
				
				//facility
				$intFacilityId = $resAppts[$i]['sa_facility_id'];
				$strFacilityNm = $arr_facilities[$intFacilityId];
				
				//patient
				$strPatientNm = $resAppts[$i]['pname'];
				$strPatientSS = $resAppts[$i]['ss'];
				$strPatientRCP = $resAppts[$i]['refPhysician'];
				$strPatientRCPID = $resAppts[$i]['primary_care_id'];
				
				$title = $arr_priCarePhyDetail[$strPatientRCPID]['Title'];
				$RCPFirstName = $arr_priCarePhyDetail[$strPatientRCPID]['FirstName'];
				$RCPLastName = $arr_priCarePhyDetail[$strPatientRCPID]['LastName'];
				$RCPfax = $arr_priCarePhyDetail[$strPatientRCPID]['physician_fax'];
				$RCPTitle="";
				if($title){
					$RCPTitle = $title;
				}
				$RCPName = $RCPTitle.' '.$RCPLastName.' '.$RCPFirstName; 
				
				$strPatientPCP = $resAppts[$i]['priCarePhysician'];
				
				$intprimaryCAREPHYId = $resAppts[$i]['primary_care_phy_id'];
				$strPhyCarePhyPh = $arr_priCarePhy[$intprimaryCAREPHYId];
				
				$dtPatientDOB = ($resAppts[$i]['DOB'] != "0000-00-00") ? $resAppts[$i]['date_of_birth'] : "N/A";
				$intAge = ($resAppts[$i]['DOB'] != "0000-00-00") ? calculate_age_from_dob($resAppts[$i]['DOB']) : "";
				$intPhoneNo = (trim($resAppts[$i]['phone_home']) != "") ? core_phone_format($resAppts[$i]['phone_home']) : ((trim($resAppts[$i]['phone_cell']) != "") ? core_phone_format($resAppts[$i]['phone_cell']) : core_phone_format($resAppts[$i]['phone_biz']));
				
				//including patient's address
				$intPatientId = $resAppts[$i]['sa_patient_id'];
				
				//whether patient is on EMR or not
				$strEMR = "";
				if($resAppts[$i]['EMR'] == 1){
					$strEMR = "e";
				}
				
				if($resAppts[$i]['erx_entry'] == 1 && $resAppts[$i]['erx_patient_id'] != ''){
					$strEMR .= $strEMR != '' ? '-' : '';
					$strEMR .= "e/Rx";
				}

				// SET SITE
				$str_site = '';
				$site = $resAppts[$i]['procedure_site'];
				$str_site = ($site) ? $arr_site[$site] : "";				 
				
				//procedure and test
				$strProcedureNm = (trim($resAppts[$i]['acronym']) != "") ? $resAppts[$i]['acronym'].' '.$str_site: $resAppts[$i]['test_name'];
				
				//operator
				$strOperatorNm = $resAppts[$i]['oname'];
				
				//other fields
				//By Karan
				$CommENTS = str_replace("$","".show_currency()."",$resAppts[$i]['sa_comments']);
				$strComments = $CommENTS;
				
				//By Jaswant Sir $strComments = $resAppts[$i]['sa_comments'];
				
				if(strlen(trim($strComments))>500){$strComments =substr($strComments,0,500);}
				
				$strComments = wordwrap($strComments, 50, "<br>\n", true);
			
				$pt_due = $resAppts[$i]['pt_due'];
				list($sdy, $sdm, $sdd) = explode("-",$resAppts[$i]['sa_app_start_date']);
				$tsApptStartDate = mktime(0, 0, 0, $sdm, $sdd, $sdy);
				$strApptStartDate = $sdm."-".$sdd."-".$sdy;
				$strApptStartTime = $resAppts[$i]['selStartTime'];
				$strApptDuration = number_format($resAppts[$i]['sa_app_duration']/60);	
				$stApptMadeDate = $arrApptMadeDate[$saAppId];
				$ptkey = $resAppts[$i]['patientkey'];
				$arrival_time = $resAppts[$i]['arrival_time'];
				
				//status			
				$intAppStatus = $resAppts[$i]['sa_patient_app_status_id'];
				$strAppStatus = $appt_opts_arr[$intAppStatus];
				
				$caseTypeArr = array('primary', 'secondary');
				
				$primaryInsName = $primaryPolicyNo = $secondaryInsName = $secondaryPolicyNo = array();
				foreach($caseTypeArr as $typeKey => $typeName){
					if(isset($arr_ins[$intPatientId]["ins_details"][$typeName]) && count($arr_ins[$intPatientId]["ins_details"][$typeName]) > 0){
						$obj = &$arr_ins[$intPatientId]["ins_details"][$typeName];
						foreach($obj as $key => &$val){
							switch($typeName){
								case 'primary':
									$primaryInsName[] = $val['ins_comp'].' ('.$key.')';
									$primaryPolicyNo[] = $val['policyno'];
								break;
								
								case 'secondary':
									$secondaryInsName[] = $val['ins_comp'].' ('.$key.')';
									$secondaryPolicyNo[] = $val['policyno'];
								break;
								
							}
						}
					}
				}
				$arr=array();
				$arr[]=get_date_format(date("Y-m-d",$tsApptStartDate));
				$arr[]=$saAppId;
				$arr[]=$intPatientId;
				$arr[]=$strFacilityNm;
				$arr[]=$strProviderNm;
				$arr[]=$RCPName;
				$arr[]=$RCPfax;
				$arr[]=$CommENTS;
				$arr[]=$strProcedureNm;
				$arr[]=$strAppStatus;
				$arr[]=$arrival_time;
				$arr[]=$strPatientPCP;
				$arr[]=$strPhyCarePhyPh;
				$arr[]=implode(', <br />', $primaryInsName);
				$arr[]=implode(', <br />', $secondaryInsName);
				$arr[]=implode(', <br />', $primaryPolicyNo);
				$arr[]=$strPatientNm;
				$arr[]=$dtPatientDOB;
				$arr[]=$strPatientSS;
				$arr[]=core_phone_format($intPhoneNo);
				fputcsv($fp,$arr, ",","\"");
				//concatinating records
				$strHTML .= '<tr valign="top">
							<td class="text" bgcolor="#ffffff" align="left"> '.get_date_format(date("Y-m-d",$tsApptStartDate)).'</td>
							<td class="text" bgcolor="#ffffff" align="left"> '.$saAppId.'</td>
							<td class="text" bgcolor="#ffffff" align="left"> '.$intPatientId.'</td>
							<td class="text" bgcolor="#ffffff" align="left"> '.$strFacilityNm.'</td>
							<td class="text" bgcolor="#ffffff" align="left">'.$strProviderNm.'</td>
							<td class="text" bgcolor="#ffffff" align="left">'.$RCPName.'</td>
							<td class="text" bgcolor="#ffffff" align="left">'.$RCPfax.'</td>
							<td class="text" bgcolor="#ffffff" align="left">'.$strComments.'</td>
							<td class="text" bgcolor="#ffffff" align="left">'.$strProcedureNm.'</td>
							<td class="text" bgcolor="#ffffff" align="left">'.$strAppStatus.'</td>
							<td class="text" bgcolor="#ffffff" align="left">'.$arrival_time.'</td>
							<td class="text" bgcolor="#ffffff" align="left">'.$strPatientPCP.'</td>
							<td class="text" bgcolor="#ffffff" align="left">'.$strPhyCarePhyPh.'</td>
							<td class="text" bgcolor="#ffffff" align="left">'.implode(', <br />', $primaryInsName).'</td>
							<td class="text" bgcolor="#ffffff" align="left">'.implode(', <br />', $secondaryInsName).'</td>
							<td class="text" bgcolor="#ffffff" align="left">'.implode(', <br />', $primaryPolicyNo).'</td>
							<td class="text" bgcolor="#ffffff" align="left"> '.$strPatientNm.'</td>
							<td class="text" bgcolor="#ffffff" align="left"> '.$dtPatientDOB.'</td>
							<td class="text" bgcolor="#ffffff" align="left"> '.$strPatientSS.'</td>
							<td class="text" bgcolor="#ffffff" align="left"> '.core_phone_format($intPhoneNo).'</td>';
						
				$pdfHTML .= '<tr valign="top">
							<td class="text" bgcolor="#ffffff" align="left" width="55"> '.get_date_format(date("Y-m-d",$tsApptStartDate)).'</td>
							<td class="text" bgcolor="#ffffff" align="left" width="55"> '.$saAppId.'</td>
							<td class="text" bgcolor="#ffffff" align="left" width="65"> '.$intPatientId.'</td>
							<td class="text" bgcolor="#ffffff" align="left" width="115">'.$strProcedureNm.'</td>
							<td class="text" bgcolor="#ffffff" align="left" width="105">'.$strPatientPCP.'</td>
							<td class="text" bgcolor="#ffffff" align="left" width="115">'.implode(', <br />', $primaryInsName).'</td>
							<td class="text" bgcolor="#ffffff" align="left" width="105">'.implode(', <br />', $primaryPolicyNo).'</td>
							<td class="text" bgcolor="#ffffff" align="left" width="115"> '.$strPatientNm.'</td>
							<td class="text" bgcolor="#ffffff" align="left" width="55"> '.$dtPatientDOB.'</td>
							<td class="text" bgcolor="#ffffff" align="left" width="70"> '.$strPatientSS.'</td>
							<td class="text" bgcolor="#ffffff" align="left" width="70"> '.core_phone_format($intPhoneNo).'</td></tr>';
			}
		}
		$strHTML .= '</tr></table>';
		$pdfHTML .= '</table>';
	}
}
fclose($fp);
$printPdFBtn = 1;
$printFile = 0;
$file_location = write_html($pdfHTML);
if(trim($strHTML) != ""){
	echo $strHTML;
	$printFile = 1;
} else{
	echo '<div class="text-center alert alert-info">No Record Found.</div>';
}
?>