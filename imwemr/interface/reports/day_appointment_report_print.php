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
FILE : DAY_APPOINTMENT_REPORT_PRINT.PHP
PURPOSE :  DAILY APPOINTMENT PRINTING
ACCESS TYPE : INCLUDED
*/
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot']."/library/classes/scheduler/appt_schedule_functions.php");
require_once($GLOBALS['fileroot']."/library/classes/scheduler/appt_ac_functions.php");
require_once($GLOBALS['fileroot']."/library/classes/acc_functions.php");
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');//to get html save location
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');//function to write html
require_once($GLOBALS['fileroot'].'/library/classes/cls_common_function.php');
require_once('CLSSchedulerReports.php');

//require_once(getcwd().'/../common/functions.inc.php');
$qryDateFormat = get_sql_date_format();
//scheduler object
$obj_scheduler = new appt_scheduler();
ob_start();
//getting report generator name
$report_generator_name = "";
$mor_appt_flag = $eve_appt_flag = 0;

$CLSCommonFunction = new CLSCommonFunction;


//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
$arrDateRange= $CLSCommonFunction->changeDateSelection();

if($dayReport=='Daily'){
	$_REQUEST['Start_date'] = $_REQUEST['End_date']= $Start_date = $End_date= date($phpDateFormat);
}else if($dayReport=='Weekly'){
	$_REQUEST['Start_date']=$Start_date = $arrDateRange['WEEK_DATE'];
	$_REQUEST['End_date']=$End_date= date($phpDateFormat);
}else if($dayReport=='Monthly'){
	$_REQUEST['Start_date']=$Start_date = $arrDateRange['MONTH_DATE'];
	$_REQUEST['End_date']=$End_date= date($phpDateFormat);
}else if($dayReport=='Quarterly'){
	$_REQUEST['Start_date']=$Start_date = $arrDateRange['QUARTER_DATE_START'];
	$_REQUEST['End_date']=$End_date = $arrDateRange['QUARTER_DATE_END'];
}


// SITE ARRAY
$arr_site = $obj_scheduler->eye_site();
$dtEffectiveDate = getDateFormatDB($_REQUEST['Start_date']);

$strProviderIds = join(',',$phyId);
$form_submit = $_REQUEST['form_submitted'];
//getting selected report time period
$strMidDay = $_REQUEST['day'];
$facility_name = $_REQUEST['facility_name'];
if($_REQUEST['submitted_from_scheduler']=='yes'){
	$facility_name = explode(',',$comboFac);
	$dtEffectiveDate = get_date_format($_REQUEST['from_date'], 'mm-dd-yyyy', 'yyyy-mm-dd');
	$strProviderIds = join(',',$comboProvider);
	$form_submit = $_REQUEST['submitted'];
	//getting selected report time period
	$strMidDay = $_REQUEST['selMidDay'];
}else{
	$_REQUEST['excusion_chkbox'][]='Patient DOB';
	$_REQUEST['excusion_chkbox'][]='Phone';
	$_REQUEST['excusion_chkbox'][]='Procedure';
	$_REQUEST['excusion_chkbox'][]='Comments';
	$_REQUEST['excusion_chkbox'][]='Appt Made';
	$_REQUEST['excusion_chkbox'][]='CoPay';
	$_REQUEST['excusion_chkbox'][]='Pt. Prv Bal';
}

if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
	//$arr_report_generator_name = explode(" ", $_SESSION["authProviderName"]);
	//$report_generator_name = substr($arr_report_generator_name[1], 0, 1).substr($arr_report_generator_name[0], 0, 1);
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
$bl_dt_range_search = false;
if(isset($_REQUEST['End_date']) && $_REQUEST['End_date'] != ""){
	$dtEffectiveDate2 = getDateFormatDB($_REQUEST['End_date']);	
	$dtDBEffectDate2=$dtEffectiveDate2;
	$intTimeStamp2 = mktime(0, 0, 0, $m2, $d2, $y2);
	$dtShowEffectDate2 = date("m/d/Y", $intTimeStamp2);

	$bl_dt_range_search = true;
}else{
	$dtDBEffectDate2 = $dtDBEffectDate;
	$intTimeStamp2 = $intTimeStamp;
	$dtEffectiveDate2 = $dtEffectiveDate;
}

function patient_mrn($ext_1,$ext_2,$athena_id){
	$patient_MRN="";	
	if(((empty($ext_1) == false) || (empty($ext_2) == false)) && (constant("EXTERNAL_MRN_SEARCH") == "YES")){
		
		if(DISP_EXTERNAL_MRN || DISP_EXTERNAL_MRN=='1'){ 
			if(empty($ext_1) == false){
				if(strlen($ext_1) == 6){
					$patient_MRN=$ext_1;	
				}
				else{
					$patient_MRN=$ext_1;
				}
			}
			elseif(empty($ext_2) == false){
				if(strlen($ext_2) == 6){
					$patient_MRN="0".$ext_2;	
				}
				else{
					$patient_MRN=$ext_2;
				}
			}
		}
		
		//====Show ExternalMRN2 value of first preference ===============// 
		if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('marr','mage')) || constant('DISP_EXTERNAL_MRN')=='2') {
			if(empty($ext_2) == false){
				if(strlen($ext_2) == 6){
					$patient_MRN="0".$ext_2;	
				}
				else{
					$patient_MRN=$ext_2;
				}
			}
			elseif(empty($ext_1) == false){
				if(strlen($ext_1) == 6){
					$patient_MRN="0".$ext_1;	
				}
				else{
					$patient_MRN=$ext_1;
				}
			}	
		}
	}
	else { $patient_MRN=$athena_id; }
	
	return $patient_MRN;
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
$blIncludeInsurance = false;	
if(isset($_REQUEST['inc_insurance']) && $_REQUEST['inc_insurance'] == 1){
	$blIncludeInsurance = true;
}
$blSortPatientLName = false;
if(isset($_REQUEST['sort_patient_last_name']) && $_REQUEST['sort_patient_last_name'] == 1){
	$blSortPatientLName = true;
}
$blIncludePatientKey = false;
if(isset($_REQUEST['inc_portal_key']) && $_REQUEST['inc_portal_key'] == 1){
	$blIncludePatientKey = true;
}

$blIncludePatientDob = false;
if(isset($_REQUEST['inc_pt_dob']) && $_REQUEST['inc_pt_dob'] == 1){
	$blIncludePatientDob = true;
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
	
$file_name="day_appointments_report.csv";
$csv_file_name= write_html("", $file_name);
if(file_exists($csv_file_name)){
unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');
$arr=array();
$arr[]='Day Appointments Report';
$arr[]='Selected Date :'.get_date_format($dtEffectiveDate) .' To : '. get_date_format($dtEffectiveDate2);
$arr[]='Created by :'.$report_generator_name.' on '.get_date_format(date("Y-m-d"))." ".date("h:i A");
fputcsv($fp,$arr, ",","\"");

	$arr=array();
	$arr[]='Provider Name';
	$arr[]='Facility';
	$arr[]='Appt Date';
	$arr[]='Appt Time';
	$arr[]='Duration';
	$arr[]='Patient Name-ID';
	if($blIncludePatientKey == true){
	$arr[]='PT-Key';
	}
	if($blIncludePatientDob == true){	
	$arr[]='Patient DOB';
	}
	if($blIncludePatientAddress == true){		
		$arr[]='Demographics';
	}else{
		$arr[]='Phone #';
	}
	if($blIncludeInsurance == true){		
		$arr[]='Insurance';
		$arr[]='Referrals';
		$arr[]='Subscriber Info';
	}
	$arr[]='Proc./Appt. Ins. Case';
	$arr[]='Comments';
	$arr[]='Appt. Made';
	$arr[]='CoPay Due';
	$arr[]='Pt. Prv Bal';
	fputcsv($fp,$arr, ",","\"");
	
	
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


	//getting schedule appointments
	$strQry = "SELECT DISTINCT(sa.id) as saAppId, sa.case_type_id, sa.sa_facility_id, p.ss, p.sex, p.EMR, p.erx_patient_id, p.erx_entry, CONCAT(p.street, p.street2, '<br>',
	p.city, ', ', p.state, ' ', p.postal_code) as patientAddress, p.temp_key as patientkey, p.temp_key_chk_val, sa.sa_madeby,
	pt.test_name ,CONCAT_WS(',',sp1.acronym,sp2.acronym,sp3.acronym) as acronym, sa.sa_app_duration, 
	sa.sa_comments, sa.procedure_site, p.DOB, CONCAT(p.lname,', ',p.fname,' ',p.mname,' - ',sa.sa_patient_id) as pname, sa.sa_patient_id,
	DATE_FORMAT(p.DOB,'".$qryDateFormat."') as date_of_birth, p.phone_home, p.phone_biz, p.phone_cell, p.vip,p.preferr_contact, 
	p.athenaID, p.External_MRN_1, p.External_MRN_2, 
	u.lname, u.fname, u.mname, CONCAT(SUBSTRING(o.fname,1,1),SUBSTRING(o.lname,1,1)) as oname,
	sa.sa_doctor_id, TIME_FORMAT(sa.sa_app_starttime,'%h:%i %p') selStartTime, sa.sa_app_start_date,
	DATE_FORMAT(st.fldLunchStTm,'%H:%i:%s') as fldLunchStTm, DATE_FORMAT(st.fldLunchEdTm,'%H:%i:%s') as fldLunchEdTm,
	(SELECT SUM(pat_due) FROM patient_charge_list_details pcld WHERE pcld.patient_id = sa.sa_patient_id AND pcld.del_status='0' ) as  pt_due,
	if(st.fldLunchStTm,if(TIME_FORMAT(sa.sa_app_starttime,'%H:%i:%s') < DATE_FORMAT(st.fldLunchStTm,'%H:%i:%s'),'Mor','Eve'),if(TIME_FORMAT(sa.sa_app_starttime,'%H:%i:%s') < '12:00:00', 'Mor', 'Eve')) as tm_status, sa_patient_app_status_id,
	u.user_type 
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
	AND sa_patient_app_status_id  NOT IN(201,18,19,20,203) AND 
	IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) 
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
	
	$resApptsRes = imw_query($strQry);
	while($tmpData=imw_fetch_assoc($resApptsRes))
	{
		//SETTING MRN NUMBER
		$mrn='';
		$mrn=patient_mrn($tmpData['External_MRN_1'],$tmpData['External_MRN_2'],$tmpData['athenaID']);
		$tmpData['pname']= (empty($mrn)===false) ? $tmpData['pname'].'/'.$mrn : $tmpData['pname'];
		
		$resAppts[] = $tmpData;
	}
	
	$intTotAppts = count($resAppts);

	//GETTING PAT IDS
	$arr_pt_id = array();
	$patientCopay=array();
	$status_countArr =array();
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
		
		if($blIncludeInsurance	 == true){
			
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
							ict.normal, insurance_data.id, ics.ins_caseid,  insurance_data.pid, ict.case_name, ics.ins_case_type, insurance_data.type, ic.in_house_code, insurance_data.policy_number, insurance_data.copay, insurance_data.subscriber_relationship, 
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
							AND ics.ins_case_type IS NOT NULL 
						ORDER BY insurance_data.pid, ics.ins_case_type, insurance_data.type ASC";
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
						
						$arr_ins[$ins_details["pid"]]["ins_details"][$abc]["ins_case"] = $ins_details["case_name"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$abc]["casetype"] = $ins_details["normal"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$abc]["ins_prid"] = $ins_details["id"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$abc]["ins_comp"] = $ins_details["in_house_code"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$abc]["policyno"] = $ins_details["policy_number"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$abc]["copayamt"] = $ins_details["copay"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$abc]["ins_type"] = ucfirst($ins_details["type"]);

						//embedding ref count in insurance details
						$arr_ins[$ins_details["pid"]]["ins_details"][$abc]["refcount"] = 0;
						$arr_ins[$ins_details["pid"]]["ins_details"][$abc]["reffused"] = 0;
						if(isset($arr_reff[$ins_details["pid"]]["no_of_reffs"]) && $arr_reff[$ins_details["pid"]]["no_of_reffs"] > 0){
							if($arr_reff[$ins_details["pid"]]["ins_data_id"] == $ins_details["id"]){
								$arr_ins[$ins_details["pid"]]["ins_details"][$abc]["refcount"] = $arr_reff[$ins_details["pid"]]["no_of_reffs"];
								$arr_ins[$ins_details["pid"]]["ins_details"][$abc]["reffused"] = intval($arr_reff[$ins_details["pid"]]["no_of_rused"]);
							}
						}
						
						$arr_ins[$ins_details["pid"]]["ins_details"][$abc]["relat"] = $ins_details["subscriber_relationship"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$abc]["fname"] = $ins_details["subscriber_fname"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$abc]["mname"] = $ins_details["subscriber_mname"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$abc]["lname"] = $ins_details["subscriber_lname"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$abc]["rpdob"] = $ins_details["subscriber_DOB"];
						$arr_ins[$ins_details["pid"]]["ins_details"][$abc]["rpssn"] = $ins_details["subscriber_ss"];

						$abc++;
					}
				}
			}
		}

		$strHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';
		$pdfHTML='<style>'.file_get_contents('css/reports_pdf.css').'</style>';	
			
		$strHTML .= '
			<table class="rpt_table rpt rpt_table-bordered rpt_padding">
				<tr>	
					<td class="rptbx1" style="width:33%">Day Appointments Report </td>
					<td  class="rptbx2" style="text-align:center; width:33%">From : '.get_date_format($dtEffectiveDate) .' To : '. get_date_format($dtEffectiveDate2).'</td>
					<td class="rptbx3" style="text-align:center; width:33%">Created by '.$report_generator_name .' on '.get_date_format(date("Y-m-d"))." ".date("h:i A").'&nbsp;</td>
				</tr>
			</table>';
		$pdfHTML .= '
			<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="100%">
				<tr>	
					<td class="rptbx1" width="320">Day Appointments Report </td>
					<td class="rptbx2" width="400">From : '.get_date_format($dtEffectiveDate) .' To : '. get_date_format($dtEffectiveDate2).'</td>
					<td class="rptbx3" width="320">Created by '.$report_generator_name .' on '.get_date_format(date("Y-m-d"))." ".date("h:i A").'&nbsp;</td>
				</tr>
			</table>';
			
		//PAGE SETTINGS
		$page_style = ($blIncludePatientAddress == true) ? "l" : "l"; //client wants landscape format in both cases now - 19 aug 2010
		
		$date_time_sep = ($blIncludePatientAddress == true) ? "<br>" : " ";
		
		$totalcol = 10;
		$leftcol = 5;
		$rightcol = 5;

		if($blIncludePatientDob == true){
			$totalcol+=1;
			$leftcol+= 1;
		}
		if($blIncludePatientKey == true){
			$totalcol+=1;
			$leftcol+= 1;
		}	
		if($blIncludeInsurance == true){
			$totalcol+=1;
			$leftcol+= 1;			
		}				
		
		if($_REQUEST['submitted_from_scheduler']=='yes'){
			$totalcol = 11;
			$leftcol = 6;
			$rightcol = 5;
			$serial_no_width_h = 20;
			$serial_no_width_d = 35;
			$date_time_width_h = 35;
			$date_time_width_d = 70;
			$duration_width_h = 35;
			$duration_width_d = 70;
			$patient_name_width_h = 130;
			$patient_name_width_d = 130;
			$patient_dob_width_h  = 100;
			$patient_dob_width_h  = 100;
			$pt_copay_width = 90;
			$pt_due_width = 90;
			$procedure_width_h = 100;
			$procedure_width_d = 100;	
			$comments_width_h = 120;
			$comments_width_d = 120;
		}
		
		if($blIncludePatientDob == true){
			//$totalcol = 11;
			//$leftcol = 6;
			//$rightcol = 5;
			
			$serial_no_width_h = 20;
			$serial_no_width_d = 35;
			$date_time_width_h = 70;
			$date_time_width_d = 70;
			$duration_width_h = 70;
			$duration_width_d = 70;
			$patient_name_width_h = 110;
			$patient_name_width_d = 110;
			$patient_dob_width_h = 100;
			$patient_dob_width_d = 100;
			
			$patient_portalkey_h  = 70;
			$patient_portalkey_d  = 70;
			$phone_no_width_h = 80;
			$phone_no_width_d = 90;
			$procedure_width_h = 110;
			$procedure_width_d = 110;
			$comments_width_h =  110;
			$comments_width_d = 110;
			$made_by_width_h = 100;
			$made_by_width_d = 100;
			$pt_copay_width = 70;
			$pt_due_width = 70;	
			
		}
		
		if($blIncludePatientKey == true){
			//$totalcol = 11;
			//$leftcol = 6;
			//$rightcol = 5;
			$serial_no_width_h = 20;
			$serial_no_width_d = 35;
			$date_time_width_h = 70;
			$date_time_width_d = 70;
			$duration_width_h = 70;
			$duration_width_d = 70;
			$patient_name_width_h = 150;
			$patient_name_width_d = 150;
			$patient_portalkey_h  = 70;
			$patient_portalkey_d  = 70;
			$phone_no_width_h = 100;
			$phone_no_width_d = 100;
			$procedure_width_h = 130;
			$procedure_width_d = 130;
			$comments_width_h =  150;
			$comments_width_d = 150;
			$made_by_width_h = 100;
			$made_by_width_d = 100;
			$pt_copay_width = 70;
			$pt_due_width = 70;	
		}
		
		if($blIncludePatientKey == true && $blIncludePatientDob == true){
			//$totalcol = 12;
			//$leftcol = 6;
			//$rightcol = 6;
			$serial_no_width_h = 20;
			$serial_no_width_d = 25;
			$date_time_width_h = 60;
			$date_time_width_d = 60;
			$duration_width_h =  60;
			$duration_width_d =  60;
			$patient_name_width_h = 125;
			$patient_name_width_d = 125;
			$patient_portalkey_h  = 65;
			$patient_portalkey_d  = 65;
			$patient_dob_width_h = 90;
			$patient_dob_width_d = 90;
			$phone_no_width_h = 80;
			$phone_no_width_d = 80;
			$procedure_width_h = 120;
			$procedure_width_d = 120;
			$comments_width_h = 130;
			$comments_width_d = 130;
			$made_by_width_h = 100;
			$made_by_width_d = 100;
			$pt_copay_width =  75;
			$pt_due_width = 75; 
		}
		
	
		if($blIncludePatientAddress == true){
			//$totalcol = 10;
			//$leftcol = 5;
			//$rightcol = 5;
			$serial_no_width_h =  25;
			$serial_no_width_d = 25;
			$date_time_width_h = 55;
			$date_time_width_d = 55;
			$duration_width_h = 55;
			$duration_width_d = 55;
			$patient_name_width_h = 110;
			$patient_name_width_d = 110;
			$patient_portalkey_h  = 70;
			$patient_portalkey_d  = 70;
			$demographics_width_h = 120;
			$demographics_width_d = 120;
			$insurance_details_width_h = 100;
			$insurance_details_width_d = 100;
			$referral_details_width_h = 45;
			$referral_details_width_d = 45;
			$subscriber_details_width_h = 85;
			$subscriber_details_width_d = 85;
			$phone_no_width_h = 75;
			$phone_no_width_d = 75;
			$procedure_width_h = 90;
			$procedure_width_d = 90;
			$comments_width_h = 70;
			$comments_width_d = 70;
			$made_by_width_h = 60;
			$made_by_width_d = 60;
			$pt_copay_width = 60;
			$pt_due_width = 60;
		}
		
		
		if($blIncludePatientAddress == false && $blIncludePatientKey == false && $blIncludePatientDob == false && $_REQUEST['submitted_from_scheduler'] !='yes'){
			$serial_no_width_h = 20;
			$serial_no_width_d = 20;
			$date_time_width_h = 80;
			$date_time_width_d = 80;
			$duration_width_h = 80;
			$duration_width_d = 80;
			$patient_name_width_h = 160;
			$patient_name_width_d = 160;
			$phone_no_width_h = 100;
			$phone_no_width_d = 100;
			$procedure_width_h = 100;
			$procedure_width_d = 100;
			$comments_width_h = 100;
			$comments_width_d = 100;
			$made_by_width_h = 100;
			$made_by_width_d = 100;
			$pt_copay_width = 100;
			$pt_due_width = 100;
		}
		
		if($blIncludeInsurance == true && $blIncludePatientKey == true && $blIncludePatientDob == false){
			//$totalcol = 14;
			//$leftcol = 7;
			//$rightcol = 7;
			
			$serial_no_width_h = 25;
			$serial_no_width_d = 25;
			$date_time_width_h = 50;
			$date_time_width_d = 50;
			$duration_width_h = 50;
			$duration_width_d = 50;
			$patient_name_width_h = 100;
			$patient_name_width_d = 100;
			$patient_portalkey_h = 60;
			$patient_portalkey_d = 60;
			$demographics_width_h = 120;
			$demographics_width_d = 120;
			$insurance_details_width_h = 100;
			$insurance_details_width_d = 100;
			$referral_details_width_h = 50;
			$referral_details_width_d = 50;
			$subscriber_details_width_h = 100;
			$subscriber_details_width_d = 100;
			$phone_no_width_h = 70;
			$phone_no_width_d = 70;
			$procedure_width_h = 80;
			$procedure_width_d = 80;
			$comments_width_h = 70;
			$comments_width_d = 70;
			$made_by_width_h = 50;
			$made_by_width_d = 50;
			$pt_copay_width = 40;
			$pt_due_width = 40;
		}
		if($blIncludePatientAddress == true && $blIncludePatientKey == false && $blIncludePatientDob == true){
			//$totalcol = 14;
			//$leftcol = 7;
			//$rightcol = 7;
			$serial_no_width_h =  25;
			$serial_no_width_d = 25;
			$date_time_width_h = 50;
			$date_time_width_d = 50;
			$duration_width_h = 50;
			$duration_width_d = 50;
			$patient_name_width_h = 100;
			$patient_name_width_d = 100;
			$patient_dob_width_h  = 60;
			$patient_dob_width_d  = 60;
			$demographics_width_h = 120;
			$demographics_width_d = 120;
			$insurance_details_width_h = 100;
			$insurance_details_width_d = 100;
			$referral_details_width_h = 50;
			$referral_details_width_d = 50;
			$subscriber_details_width_h = 100;
			$subscriber_details_width_d = 100;
			$phone_no_width_h = 70;
			$phone_no_width_d = 70;
			$procedure_width_h = 80;
			$procedure_width_d = 80;
			$comments_width_h = 70;
			$comments_width_d = 70;
			$made_by_width_h = 50;
			$made_by_width_d = 50;
			$pt_copay_width = 40;
			$pt_due_width = 40;
			
		}
		if($blIncludePatientAddress == true && $blIncludePatientKey == true && $blIncludePatientDob == true){
			//$totalcol = 15;
			//$leftcol = 8;
			//$rightcol = 7;
			
			$serial_no_width_h = 25;
			$serial_no_width_d = 25;
			$date_time_width_h = 50;
			$date_time_width_d = 50;
			$duration_width_h = 50;
			$duration_width_d = 50;
			$patient_name_width_h = 80;
			$patient_name_width_d = 80;
			$patient_dob_width_h = 60;
			$patient_dob_width_d = 60;
			$patient_portalkey_h = 60;
			$patient_portalkey_d = 60;
			$demographics_width_h = 100;
			$demographics_width_d = 100;
			$insurance_details_width_h = 90;
			$insurance_details_width_d = 90;
			$referral_details_width_h = 50;
			$referral_details_width_d = 50;
			$subscriber_details_width_h = 90;
			$subscriber_details_width_d = 90;
			$phone_no_width_h = 70;
			$phone_no_width_d = 70;
			$procedure_width_h = 70;
			$procedure_width_d = 70;
			$comments_width_h = 70;
			$comments_width_d = 70;
			$made_by_width_h = 50;
			$made_by_width_d = 50;
			$pt_copay_width = 40;
			$pt_due_width = 40;	
		}
		
		$intTempProviderId = 0;
		$intTempFacilityId = 0;
		$dtTempApptDate = "";
		$intDateApptNo = 0;
		for($i = 0; $i < $intTotAppts; $i++){
			//appt date
			$ptkey1 = $ptkeyChkVal = "";
			$dtApptDate = $resAppts[$i]['sa_app_start_date'];
			
			$sa_patient_app_status_id= $resAppts[$i]["sa_patient_app_status_id"];
			$status_countArr[$sa_patient_app_status_id][] = $resAppts[$i]['sa_patient_id'];
			
			$saAppId = $resAppts[$i]['saAppId'];
			$ps_qry = "SELECT CONCAT(SUBSTRING(o.fname,1,1),SUBSTRING(o.lname,1,1)) as uname FROM previous_status ps LEFT JOIN users o ON o.username = ps.oldMadeBy  WHERE ps.sch_id = '".$saAppId."' order by ps.id ASC LIMIT 1";
			$ps_qry_obj = imw_query($ps_qry);
			$ps_qry_obj_data = imw_fetch_assoc($ps_qry_obj);		
			$ps_user = $ps_qry_obj_data['uname'];

			//appt case #, if any
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
			$strPatient_Nm = $resAppts[$i]['pname'];
			
			$strPatientNm =	wordwrap($strPatient_Nm, 18, "<br>\n", true);
			
			$dtPatientDOB = ($resAppts[$i]['DOB'] != "0000-00-00") ? $resAppts[$i]['date_of_birth'] : "N/A";
			$intAge = ($resAppts[$i]['DOB'] != "0000-00-00") ? calculate_age_from_dob($resAppts[$i]['DOB']) : "";
			
			$intPhoneNo = $resAppts[$i]["phone_home"];
			$prefer_contact = $resAppts[$i]["preferr_contact"];
			if($prefer_contact == 0)
			{
				if(trim($resAppts[$i]["phone_home"]) != ""){$intPhoneNo = core_phone_format($resAppts[$i]["phone_home"]); }
			}
			else if($prefer_contact == 1)
			{
				if(trim($resAppts[$i]["phone_biz"]) != ""){$intPhoneNo = core_phone_format($resAppts[$i]["phone_biz"]); }				
			}
			else if($prefer_contact == 2)
			{
				if(trim($resAppts[$i]["phone_cell"]) != ""){$intPhoneNo = core_phone_format($resAppts[$i]["phone_cell"]); }				
			}
			
			//$intPhoneNo = (trim($resAppts[$i]['phone_home']) != "") ? core_phone_format($resAppts[$i]['phone_home']) : ((trim($resAppts[$i]['phone_cell']) != "") ? core_phone_format($resAppts[$i]['phone_cell']) : core_phone_format($resAppts[$i]['phone_biz']));
			
			//including patient's address
			$intPatientId = $resAppts[$i]['sa_patient_id'];
			if($blIncludePatientAddress == true){
				$strPatientAddress = $resAppts[$i]['patientAddress'];
				$strSSN = $resAppts[$i]['ss'];
				$strSex = $resAppts[$i]['sex'];
			}
			
			//whether patient is on EMR or not
			$strEMR = "";
			if($resAppts[$i]['EMR'] == 1){
				$strEMR = "e";
			}
			
			if($resAppts[$i]['erx_entry'] == 1 and $resAppts[$i]['erx_patient_id'] != ''){
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
			$strComments = str_ireplace('<','&lt;',$strComments);
			$strComments_str = str_ireplace('>','&gt;',$strComments);
			$strComments = wordwrap($strComments_str, 20, "<br>\n", true);
			//if(strlen(trim($strComments))>500){$strComments =substr($strComments,0,500);}
			$pt_due = $resAppts[$i]['pt_due'];
			list($sdy, $sdm, $sdd) = explode("-",$resAppts[$i]['sa_app_start_date']);
			$tsApptStartDate = mktime(0, 0, 0, $sdm, $sdd, $sdy);
			$strApptStartDate = $sdm."-".$sdd."-".$sdy;
			$strApptStartTime = $resAppts[$i]['selStartTime'];
			$strApptDuration = number_format($resAppts[$i]['sa_app_duration']/60);	
			$stApptMadeDate = $arrApptMadeDate[$saAppId];
			$ptkey1 = $resAppts[$i]['patientkey'];
			$ptkeyChkVal = $resAppts[$i]['temp_key_chk_val'];
			if(($ptkey1 == "" || $ptkey1 == '0') && $ptkeyChkVal==1){
				$new_pt_tempKey = temp_key_gen();
				$qry = "UPDATE patient_data SET temp_key = '".$new_pt_tempKey."' WHERE id = '".$intPatientId."' ";
				imw_query($qry);
			}
			if(($ptkey1 == "" || $ptkey1 == '0') && $ptkeyChkVal==0){
				$new_pt_tempKey1 = temp_key_gen();
				$qry1 = "UPDATE patient_data SET temp_key = '".$new_pt_tempKey1."', temp_key_chk_val=1 WHERE id = '".$intPatientId."' ";
				imw_query($qry1);
			}
			if(($ptkey1 !="" || $ptkey1 != '0') && $ptkeyChkVal==0){
				$qry2 = "UPDATE patient_data SET temp_key_chk_val=1 WHERE id = '".$intPatientId."' ";
				imw_query($qry2);			
			}
			
			$rs = imw_query("select temp_key from patient_data where id = '".$intPatientId."'");
			$res = imw_fetch_array($rs);
			$ptkey= $res['temp_key'];
			//new page for every provider
			$bl_provider_header_changed = false;
			if($intTempProviderId != $intProviderId || $intTempFacilityId != $intFacilityId){
			//if($intTempProviderId != $intProviderId){	
				if($i > 0){
					if($blIncludePatientKey!==false){
					$strHTML .= "
							<tr>
								<td colspan=".$totalcol." bgcolor=\"#ffffff\"> </td>
							 </tr>
							 <tr>
								<td align=\"right\" bgcolor=\"#ffffff\" class=\"text\" colspan=".$totalcol."> <b>Total Appointment(s): ".$intSerialNo."</b> </td>
							 </tr>	
							 <tr>
								<td colspan=".$totalcol." bgcolor=\"#ffffff\"> </td>
							 </tr>
						</table>
					";
					$pdfHTML .= "
							<tr>
								<td colspan=".$totalcol." bgcolor=\"#ffffff\"> </td>
							 </tr>
							 <tr>
								<td align=\"right\" bgcolor=\"#ffffff\" class=\"text\" colspan=".$totalcol."> <b>Total Appointment(s): ".$intSerialNo."</b> </td>
							 </tr>	
							 <tr>
								<td colspan=".$totalcol." bgcolor=\"#ffffff\"> </td>
							 </tr>
						</table>
						$end_page
						$start_page
					";
					}else{
					$strHTML .= "
							<tr>
								<td colspan=".$totalcol." bgcolor=\"#ffffff\"> </td>
							 </tr>
							 <tr>
								<td align=\"right\" bgcolor=\"#ffffff\" class=\"text\" colspan=".$totalcol."> <b>Total Appointment(s): ".$intSerialNo."</b> </td>
							 </tr>	
							 <tr>
								<td colspan=".$totalcol." bgcolor=\"#ffffff\"> </td>
							 </tr>
						</table>
					";
					$pdfHTML .= "
							<tr>
								<td colspan=".$totalcol." bgcolor=\"#ffffff\"> </td>
							 </tr>
							 <tr>
								<td align=\"right\" bgcolor=\"#ffffff\" class=\"text\" colspan=".$totalcol."> <b>Total Appointment(s): ".$intSerialNo."</b> </td>
							 </tr>	
							 <tr>
								<td colspan=".$totalcol." bgcolor=\"#ffffff\"> </td>
							 </tr>
						</table>
						$end_page
						$start_page
					";
					}
				}
				$intSerialNo = 0;

				$strHTML .= '					
					<table class="rpt_table rpt rpt_table-bordered" width="100%">';
				
				$pdfHTML .= '					
					<table class="rpt_table rpt rpt_table-bordered" width="100%">';
				//adding header for first page
				
				$strHTML .= '
						<tr>
							<td class="text_b_w" align="left" colspan='.$leftcol.'>Provider Name: '.$strProviderNm.' ('.$strReportFullOrHalf.') </td>
							<td class="text_b_w" align="left" colspan='.$rightcol.'>'.$strFacilityNm.'</td>
						</tr>
						<tr>
							<td class="text_b_w" width="'.$serial_no_width_h.'" align="left">Appt.</td>
							<td class="text_b_w" width="'.$date_time_width_h.'" align="left">Time</td>				
							<td class="text_b_w" width="'.$duration_width_h.'" align="left">Duration</td>
							<td class="text_b_w" width="'.$patient_name_width_h.'" align="left">Patient Name-ID</td>
							';
				$pdfHTML .= '
						<tr>
							<td class="text_b_w" align="left" colspan='.$leftcol.'>Provider Name: '.$strProviderNm.' ('.$strReportFullOrHalf.')</td>
							<td class="text_b_w" align="left" colspan='.$rightcol.'>'.$strFacilityNm.'</td>
						</tr>
						<tr>
							<td class="text_b_w" width="'.$serial_no_width_h.'" align="left">Appt.</td>
							<td class="text_b_w" width="'.$date_time_width_h.'" align="left">Time</td>				
							<td class="text_b_w" width="'.$duration_width_h.'" align="left">Duration</td>
							<td class="text_b_w" width="'.$patient_name_width_h.'" align="left">Patient Name-ID</td>
							';			
					if($blIncludePatientKey == true){						
						$strHTML .= '
							<td class="text_b_w" width="'.$patient_portalkey_h.'" align="left">PT-Key</td>';
						$pdfHTML .= '
							<td class="text_b_w" width="'.$patient_portalkey_h.'" align="left">PT-Key</td>';
					}
					
					
					
					if($blIncludePatientDob == true){						
						$strHTML .= '
							<td class="text_b_w" width="'.$patient_dob_width_h.'" align="left">Patient DOB</td>';
						$pdfHTML .= '
							<td class="text_b_w" width="'.$patient_dob_width_h.'" align="left">Patient DOB</td>';
					}
					if($_REQUEST['submitted_from_scheduler']=='yes'){
						if(in_array('Patient DOB',$_REQUEST['excusion_chkbox'])){
							$pdfHTML .= '<td class="text_b_w" width="'.$patient_dob_width_h.'" align="left">Patient DOB</td>';
						}
					}
					if($blIncludePatientAddress == true){						
						$strHTML .= '<td class="text_b_w" width="'.$demographics_width_h.'" align="left">Demographics</td>';
						$pdfHTML .= '<td class="text_b_w" width="'.$demographics_width_h.'" align="left">Demographics</td>';
					}else{
						$strHTML .= '
							<td class="text_b_w" width="'.$phone_no_width_h.'" align="left">Phone #</td>
						';
						$pdfHTML .= '';
						if(in_array('Phone',$_REQUEST['excusion_chkbox'])){
						$pdfHTML .= '<td class="text_b_w" width="'.$phone_no_width_h.'" align="left">Phone #</td>';
						}else{
							$pdfHTML .= '<td class="text_b_w" width="'.$phone_no_width_h.'" align="left">&nbsp;</td>';
						}
					}

					if($blIncludeInsurance == true){						
						$strHTML .= '
						<td class="text_b_w" align="left">
							<table class="rpt_table rpt rpt_table-bordered" width="100%">
								<tr>
									<td class="text_b_w" style="border:none;" width="'.$insurance_details_width_h.'" align="left">Insurance</td>
									<td class="text_b_w" style="border:none;" width="'.$referral_details_width_h.'" align="left">Referrals</td>
									<td class="text_b_w" style="border:none;" width="'.$insurance_details_width_h.'" align="left">Subscriber Info</td>
								</tr>
							</table>
						</td>';

						$pdfHTML .= '
						<td class="text_b_w" align="left">
							<table class="rpt_table rpt rpt_table-bordered" width="100%">
								<tr>
									<td class="text_b_w" style="border:none;" width="'.$insurance_details_width_h.'" align="left">Insurance</td>
									<td class="text_b_w" style="border:none;" width="'.$referral_details_width_h.'" align="left">Referrals</td>
									<td class="text_b_w" style="border:none;" width="'.$insurance_details_width_h.'" align="left">Subscriber Info</td>
								</tr>
							</table>
						</td>';
					}

					if($blIncludePatientAddress == true){
						$strHTML .= '
								<td class="text_b_w" width="'.$procedure_width_h.'" align="left">Proc./Appt. Ins. Case</td>
								<td class="text_b_w" width="'.$comments_width_h.'" align="left">Comments</td>							
								<td class="text_b_w" width="'.$made_by_width_h.'" align="left">Appt. Made</td>
								<td class="text_b_w" width="'.$pt_due_width.'" align="left">CoPay Due</td>
								<td class="text_b_w" width="'.$pt_copay_width.'" align="left">Pt. Prv Bal</td>																
							</tr>';
						$pdfHTML .= '<td class="text_b_w" width="'.$procedure_width_h.'" align="left">Proc./Appt. Ins. Case</td>';
						$pdfHTML .= '<td class="text_b_w" width="'.$comments_width_h.'" align="left">Comments</td>';
						$pdfHTML .= '<td class="text_b_w" width="'.$made_by_width_h.'" align="left">Appt. Made</td>';
						$pdfHTML .= '<td class="text_b_w" width="'.$pt_copay_width.'" align="left">CoPay Due</td>';
						$pdfHTML .= '<td class="text_b_w" width="'.$pt_due_width.'" align="left">Pt. Prv Bal</td>';
						$pdfHTML .= '</tr>';
					}else{
						$strHTML .= '
								<td class="text_b_w" width="'.$procedure_width_h.'" align="left">Procedure</td>
								<td class="text_b_w" width="'.$comments_width_h.'" align="left">Comments</td>							
								<td class="text_b_w" width="'.$made_by_width_h.'" align="left">Appt. Made</td>
								<td class="text_b_w" width="'.$pt_copay_width.'" align="left">CoPay Due</td>
								<td class="text_b_w" width="'.$pt_due_width.'" align="left">Pt. Prv Bal</td>
							</tr>';	
						if(in_array('Procedure',$_REQUEST['excusion_chkbox'])){
						$pdfHTML .= '<td class="text_b_w" width="'.$procedure_width_h.'" align="left">Procedure</td>';
						}else{
							$pdfHTML .= '<td class="text_b_w" width="'.$procedure_width_h.'" align="left">&nbsp;</td>';
						}
						if(in_array('Comments',$_REQUEST['excusion_chkbox'])){
						$pdfHTML .= '<td class="text_b_w" width="'.$comments_width_h.'" align="left">Comments</td>';
						}else{
							$pdfHTML .= '<td class="text_b_w" width="'.$comments_width_h.'" align="left">&nbsp;</td>';
						}
						if(in_array('Appt Made',$_REQUEST['excusion_chkbox'])){
						$pdfHTML .= '<td class="text_b_w" width="'.$made_by_width_h.'" align="left">Appt. Made</td>';
						}else{
							$pdfHTML .= '<td class="text_b_w" width="'.$made_by_width_h.'" align="left">&nbsp;</td>';
						}
						if(in_array('CoPay',$_REQUEST['excusion_chkbox'])){
						$pdfHTML .= '<td class="text_b_w" width="'.$pt_copay_width.'" align="left">CoPay Due</td>';
						}else{
							$pdfHTML .= '<td class="text_b_w" width="'.$pt_copay_width.'" align="left">&nbsp;</td>';
						}
						if(in_array('Pt. Prv Bal',$_REQUEST['excusion_chkbox'])){
						$pdfHTML .= '<td class="text_b_w" width="'.$pt_due_width.'" align="left">Pt. Prv Bal</td>';								
						}else{
							$pdfHTML .= '<td class="text_b_w" width="'.$pt_due_width.'" align="left">&nbsp;</td>';
						}
						$pdfHTML .='</tr>';	
					}
				$bl_provider_header_changed = true;
			}


			
			$intSerialNo++;
			
			//new date header
			if(($dtTempApptDate != $dtApptDate) || $dtTempApptDate == "" || $bl_provider_header_changed == true){

				if($dtTempApptDate != ""){					
					$strHTML = str_replace("[__APPOINTMENT_CNT__]", $intDateApptNo, $strHTML);
					$pdfHTML = str_replace("[__APPOINTMENT_CNT__]", $intDateApptNo, $pdfHTML);
					$intDateApptNo = 0;
				}

				if($dtTempApptDate != "" && $bl_provider_header_changed == false){
				
				}
				$strHTML .= "
							<tr>								
								<td align=\"left\" class=\"text_b_w\" colspan=".$totalcol."><b>".date('l',$tsApptStartDate).", ".get_date_format(date("Y-m-d",$tsApptStartDate))."</b> - [__APPOINTMENT_CNT__] appointment(s)</td>
							</tr>";	
				$pdfHTML .= "
							<tr>								
								<td align=\"left\" class=\"text_b_w\" colspan=".$totalcol."><b>".date('l',$tsApptStartDate).", ".get_date_format(date("Y-m-d",$tsApptStartDate))."</b> - [__APPOINTMENT_CNT__] appointment(s)</td>
							</tr>";	
							
				$mor_appt_flag = 0; $eve_appt_flag = 0;									
				
			}
			
			if($mor_appt_flag == 0 && $resAppts[$i]['tm_status']=='Mor')
			{
				$strHTML .= "
							<tr>								
								<td align=\"left\" class=\"text_b_w\" colspan=".$totalcol."><b>Morning Appointments</b> </td>
							</tr>";	
				$pdfHTML .= "
							<tr>								
								<td align=\"left\" class=\"text_b_w\" colspan=".$totalcol."><b>Morning Appointments</b> </td>
							</tr>";	
				$mor_appt_flag = 1;
			}
			
			if($eve_appt_flag == 0 && $resAppts[$i]['tm_status']=='Eve')
			{
				$strHTML .= "
							<tr>								
								<td align=\"left\" class=\"text_b_w\" colspan=".$totalcol."><b>Afternoon Appointments</b> </td>
							</tr>";	
				$pdfHTML .= "
							<tr>								
								<td align=\"left\" class=\"text_b_w\" colspan=".$totalcol."><b>Afternoon Appointments</b> </td>
							</tr>";	
				$eve_appt_flag = 1;
			}			
			
			$intDateApptNo++;

			//concatinating records 
			$strHTML .= '			
					<tr valign="top">
						<td class="text" bgcolor="#ffffff" width="'.$serial_no_width_d.'" align="left"> '.($intSerialNo).'</td>
						<td class="text valign" bgcolor="#ffffff" width="'.$date_time_width_d.'" align="center">'.$date_time_sep.$strApptStartTime.'</td>				
						<td class="text" bgcolor="#ffffff" width="'.$duration_width_d.'" align="center">'.$strApptDuration.' Min.</td>';
			$strHTML .= '<td class="text" bgcolor="#ffffff" width="'.$patient_name_width_d.'" align="left">'.$strPatientNm;
			$pdfHTML .= '			
					<tr valign="top">
						<td class="text" bgcolor="#ffffff" width="'.$serial_no_width_d.'" align="left"> '.($intSerialNo).'</td>
						<td class="text valign" bgcolor="#ffffff" width="'.$date_time_width_d.'" align="center">'.$date_time_sep.$strApptStartTime.'</td>				
						<td class="text" bgcolor="#ffffff" width="'.$duration_width_d.'" align="center">'.$strApptDuration.' Min.</td>';
			$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$patient_name_width_d.'" align="left">'.$strPatientNm;
			if(trim($strEMR) != ""){
				$strHTML .= ' ('.$strEMR.')';
				$pdfHTML .= ' ('.$strEMR.')';
			}
			
			$strHTML .= '</td>';
			$pdfHTML .= '</td>';
			
			$ptportalkey='';
			if($ptkey!=""){
				$ptportalkey = $ptkey;			
			}

			if($blIncludePatientKey == true){	
			$strHTML .= '<td class="text" bgcolor="#ffffff" width="'.$patient_portalkey_d.'" align="left">'.$ptportalkey;
			$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$patient_portalkey_d.'" align="left">'.$ptportalkey;
			$strHTML .= '</td>';
			$pdfHTML .= '</td>';
			}
			
			if($blIncludePatientDob == true){	
				if($intAge != ""){
					$strHTML .= '<td class="text" bgcolor="#ffffff" width="'.$patient_dob_width_d.'" align="left">'.$dtPatientDOB.' ('.$intAge.')</td>';
					$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$patient_dob_width_d.'" align="left">'.$dtPatientDOB.' ('.$intAge.')</td>';
				} else{
					$strHTML .= '<td class="text" bgcolor="#ffffff" width="'.$patient_dob_width_d.'" align="left">'.$dtPatientDOB.'</td>';
					$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$patient_dob_width_d.'" align="left">'.$dtPatientDOB.'</td>';
				}
			}
			if($_REQUEST['submitted_from_scheduler']=='yes'){
				if($intAge != ""){
					if(in_array('Patient DOB',$_REQUEST['excusion_chkbox'])){
						$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$patient_dob_width_d.'" align="left">'.$dtPatientDOB.' ('.$intAge.')</td>';
					}
				} else {
					if(in_array('Patient DOB',$_REQUEST['excusion_chkbox'])){
						$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$patient_dob_width_d.'" align="left">'.$dtPatientDOB.'</td>';
					}
				}
			}
			if($blIncludePatientAddress == true){					
				//demographics
				$strHTML .= '<td class="text" bgcolor="#ffffff" width="'.$demographics_width_d.'" align="left">';
				$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$demographics_width_d.'" align="left">';
				
				if(str_replace(",","",trim(strip_tags($strPatientAddress))) != ""){
					$strHTML .= $strPatientAddress;
					$pdfHTML .= $strPatientAddress;
					$csvDemo = $strPatientAddress;
				}
				$strHTML .= '<BR>'.$strSSN." ".$strSex;
				$pdfHTML .= '<BR>'.$strSSN." ".$strSex;
				$csvDemo .= $strSSN." ".$strSex;
				if($intAge != ""){
					$strHTML .= '<BR>'.$dtPatientDOB.' ('.$intAge.')';
					$pdfHTML .= '<BR>'.$dtPatientDOB.' ('.$intAge.')';
				}else{
					$strHTML .= '<BR>'.$dtPatientDOB;
					$pdfHTML .= '<BR>'.$dtPatientDOB;
				}
				$csvDemo .= $dtPatientDOB;
				$strHTML .= '<BR>'.core_phone_format($intPhoneNo);
				$pdfHTML .= '<BR>'.core_phone_format($intPhoneNo);
				$csvDemo .= core_phone_format($intPhoneNo);
				$strHTML .= '</td>';
				$pdfHTML .= '</td>';

			}else{
				$strHTML .= '<td class="text" bgcolor="#ffffff" width="'.$phone_no_width_d.'" align="left">'.core_phone_format($intPhoneNo).'</td>';
				if(in_array('Phone',$_REQUEST['excusion_chkbox'])){
				$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$phone_no_width_d.'" align="left">'.core_phone_format($intPhoneNo).'</td>';
				}else{
					$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$phone_no_width_d.'" align="left">&nbsp;</td>';
						}
			}

			//insurance
			if($blIncludeInsurance == true){					
				
				$strHTML .= '<td class="text" align="center" bgcolor="#ffffff">
								<table class="rpt_table rpt rpt_table-bordered" width="100%">';
				$pdfHTML .= '<td class="text" align="center" bgcolor="#ffffff">
								<table class="rpt_table rpt rpt_table-bordered" width="100%">';
				if(isset($arr_ins[$intPatientId]) && is_array($arr_ins[$intPatientId]["ins_details"])){
					$int_ins_details = count($arr_ins[$intPatientId]["ins_details"]);
					if($int_ins_details > 0){
						$insCnt = 1;
						foreach($arr_ins[$intPatientId]["ins_details"] as $thisInsDetail){
							
							$strHTML .= '<tr>';
							$pdfHTML .= '<tr>';
								$strHTML .= '<td class="text" bgcolor="#ffffff" align="left" width="'.$insurance_details_width_d.'">'.$thisInsDetail["ins_type"].' - '.$thisInsDetail["ins_comp"].' ('.$thisInsDetail["ins_case"].')<br>'.$thisInsDetail["policyno"].' '.numberFormat((float)$thisInsDetail["copayamt"],2).'</td>';
								$pdfHTML .= '<td class="text" bgcolor="#ffffff" align="left" width="'.$insurance_details_width_d.'">'.$thisInsDetail["ins_type"].' - '.$thisInsDetail["ins_comp"].' ('.$thisInsDetail["ins_case"].')<br>'.$thisInsDetail["policyno"].' '.numberFormat((float)$thisInsDetail["copayamt"],2).'</td>';
								if($thisInsDetail["casetype"] == 1){
									$strHTML .= '<td class="text" bgcolor="#ffffff" align="center" width="'.$referral_details_width_d.'">'.$thisInsDetail["refcount"].' / '.$thisInsDetail["reffused"].'</td>';
									$pdfHTML .= '<td class="text" bgcolor="#ffffff" align="center" width="'.$referral_details_width_d.'">'.$thisInsDetail["refcount"].' / '.$thisInsDetail["reffused"].'</td>';
								}else{
									$strHTML .= '<td class="text" bgcolor="#ffffff" align="center" width="'.$referral_details_width_d.'">N/A</td>';
									$pdfHTML .= '<td class="text" bgcolor="#ffffff" align="center" width="'.$referral_details_width_d.'">N/A</td>';
								}
								$strHTML .= '<td class="text" bgcolor="#ffffff" align="left" width="'.$subscriber_details_width_d.'">'.$thisInsDetail["fname"].' '.$thisInsDetail["lname"].'<br>'.$thisInsDetail["rpssn"].' '.$thisInsDetail["rpdob"].'</td>';
								$pdfHTML .= '<td class="text" bgcolor="#ffffff" align="left" width="'.$subscriber_details_width_d.'">'.$thisInsDetail["fname"].' '.$thisInsDetail["lname"].'<br>'.$thisInsDetail["rpssn"].' '.$thisInsDetail["rpdob"].'</td>';
							$strHTML .= '</tr>';
							$pdfHTML .= '</tr>';

							if($insCnt < $int_ins_details){
								$strHTML .= '<tr>
											<td colspan="3"><hr style="height:.5px;color:#c9c9c9"></td>
										</tr>';
								$pdfHTML .= '<tr>
											<td colspan="3"><hr style="height:.5px;color:#c9c9c9"></td>
										</tr>';
							}
							$insCnt++;
						}
					}else{
						$strHTML .= '<tr>';
						$strHTML .= '<td class="text" bgcolor="#ffffff" align="left" width="'.$insurance_details_width_d.'"> </td>';
						$strHTML .= '<td class="text" bgcolor="#ffffff" align="center" width="'.$referral_details_width_d.'"> </td>';
						$strHTML .= '<td class="text" bgcolor="#ffffff" align="left" width="'.$subscriber_details_width_d.'"> </td>';
						$strHTML .= '</tr>';
						
						$pdfHTML .= '<tr>';
						$pdfHTML .= '<td class="text" bgcolor="#ffffff" align="left" width="'.$insurance_details_width_d.'"> </td>';
						$pdfHTML .= '<td class="text" bgcolor="#ffffff" align="center" width="'.$referral_details_width_d.'"> </td>';
						$pdfHTML .= '<td class="text" bgcolor="#ffffff" align="left" width="'.$subscriber_details_width_d.'"> </td>';
						$pdfHTML .= '</tr>';
					}
				}else{
					$strHTML .= '<tr>';
					$strHTML .= '<td class="text" bgcolor="#ffffff" align="left" width="'.$insurance_details_width_d.'"> </td>';
					$strHTML .= '<td class="text" bgcolor="#ffffff" align="center" width="'.$referral_details_width_d.'"> </td>';
					$strHTML .= '<td class="text" bgcolor="#ffffff" align="left" width="'.$subscriber_details_width_d.'"> </td>';
					$strHTML .= '</tr>';
					
					$pdfHTML .= '<tr>';
					$pdfHTML .= '<td class="text" bgcolor="#ffffff" align="left" width="'.$insurance_details_width_d.'"> </td>';
					$pdfHTML .= '<td class="text" bgcolor="#ffffff" align="center" width="'.$referral_details_width_d.'"> </td>';
					$pdfHTML .= '<td class="text" bgcolor="#ffffff" align="left" width="'.$subscriber_details_width_d.'"> </td>';
					$pdfHTML .= '</tr>';
				}
				$strHTML .= '</table></td>';	
				$pdfHTML .= '</table></td>';				
			}

			//-- SET AMOUNTS
			$textAlign='right';
			$pendingCopay=trim($arrPatCopay[$intPatientId][$saAppId]['COPAY_AMT']);
			if($pendingCopay=='VIP'){ $textAlign='center';}
							
			if($pendingCopay!='VIP'){
				if($arrPatCopay[$intPatientId][$saAppId]['COPAY_AMT']>0){
					$pendingCopay=$arrPatCopay[$intPatientId][$saAppId]['COPAY_AMT']-$arrPatCopay[$intPatientId][$saAppId]['COPAY_PAID'];
				}
				if($pendingCopay==0 || $pendingCopay==''){ 
					$pendingCopay='-'; $textAlign='center';
				}else{
					$pendingCopay=numberFormat($pendingCopay,2);
				}
			}
			
			$textAlignPDue='right';
			if($pt_due==0 || $pt_due==''){
				$pt_due='-'; $textAlignPDue='center';
			}else{
				$pt_due=numberFormat($pt_due,2);				
			}
			// ---------------
			
			if($blIncludePatientAddress == true){
				$strHTML .= '
						<td class="text" bgcolor="#ffffff" width="'.$procedure_width_d.'" style="text-align:left;">'.$strProcedureNm.'<br><br>'.((!empty($insApptCaseId) && isset($arr_ins_case[$insApptCaseId]) && !empty($arr_ins_case[$insApptCaseId])) ? $arr_ins_case[$insApptCaseId]." - ".$insApptCaseId : "N/A" ).'</td>
						<td class="text" bgcolor="#ffffff" width="'.$comments_width_d.'" align="left">'.$strComments.'</td>							
						<td class="text" bgcolor="#ffffff" width="'.$made_by_width_d.'" align="left">'.$ps_user.' '.$stApptMadeDate.'</td>
						<td class="text" bgcolor="#ffffff" width="'.$pt_copay_width.'" style="text-align:'.$textAlign.'">'.$pendingCopay.'&nbsp;</td>
						<td class="text" bgcolor="#ffffff" width="'.$pt_due_width.'" style="text-align:'.$textAlignPDue.'" >'.$pt_due.'&nbsp;</td>
					</tr>';
				$pdfHTML .= '
						
						<td class="text" bgcolor="#ffffff" width="'.$procedure_width_d.'" style="text-align:left;">'.$strProcedureNm.'<br><br>'.((!empty($insApptCaseId) && isset($arr_ins_case[$insApptCaseId]) && !empty($arr_ins_case[$insApptCaseId])) ? $arr_ins_case[$insApptCaseId]." - ".$insApptCaseId : "N/A" ).'</td>
						<td class="text" bgcolor="#ffffff" width="'.$comments_width_d.'" align="left">'.$strComments.'</td>							
						<td class="text" bgcolor="#ffffff" width="'.$made_by_width_d.'" align="left">'.$ps_user.' '.$stApptMadeDate.'</td>
						<td class="text" bgcolor="#ffffff" width="'.$pt_copay_width.'" style="text-align:'.$textAlign.'">'.$pendingCopay.'&nbsp;</td>
						<td class="text" bgcolor="#ffffff" width="'.$pt_due_width.'" style="text-align:'.$textAlignPDue.'">'.$pt_due.'&nbsp;</td>					
					</tr>';	
			
			}else{
				$strHTML .= '
						
						<td class="text" bgcolor="#ffffff" width="'.$procedure_width_d.'" style="text-align:left;">'.$strProcedureNm.'</td>
						<td class="text" bgcolor="#ffffff" width="'.$comments_width_d.'" align="left">'.$strComments.'</td>							
						<td class="text" bgcolor="#ffffff" width="'.$made_by_width_d.'" align="left">'.$ps_user.' '.$stApptMadeDate.'</td>
						<td class="text" bgcolor="#ffffff" width="'.$pt_copay_width.'" style="text-align:'.$textAlign.'">'.$pendingCopay.'&nbsp;</td>
						<td class="text" bgcolor="#ffffff" width="'.$pt_due_width.'" style="text-align:'.$textAlignPDue.'">'.$pt_due.'&nbsp;</td>					
					</tr>';
					if(in_array('Procedure',$_REQUEST['excusion_chkbox'])){
						$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$procedure_width_d.'" style="text-align:left;">
							'.$strProcedureNm.'</td>';
					}else{
						$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$procedure_width_d.'" align="left">&nbsp;</td>';
						}
					if(in_array('Comments',$_REQUEST['excusion_chkbox'])){
							$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$comments_width_d.'" align="left">'.$strComments.'</td>';							
					}else{
						$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$comments_width_d.'" align="left">&nbsp;</td>';
						}
					if(in_array('Appt Made',$_REQUEST['excusion_chkbox'])){
				$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$made_by_width_d.'" align="left">'.$ps_user.' '.$stApptMadeDate.'</td>';
					}else{
						$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$made_by_width_d.'" align="left">&nbsp;</td>';
					}
					if(in_array('CoPay',$_REQUEST['excusion_chkbox'])){
				$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$pt_copay_width.'" style="text-align:'.$textAlign.'">'.$pendingCopay.'&nbsp;</td>';
					}else{
						$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$pt_copay_width.'" align="left">&nbsp;</td>';
					}
					if(in_array('Pt. Prv Bal',$_REQUEST['excusion_chkbox'])){
				$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$pt_due_width.'" style="text-align:'.$textAlignPDue.'">'.$pt_due.'&nbsp;</td>';					
					}else{
						$pdfHTML .= '<td class="text" bgcolor="#ffffff" width="'.$pt_due_width.'" align="left">&nbsp;</td>';
					}
				$pdfHTML .= '	</tr>';		
			}
						
			$intTempProviderId = $intProviderId;
			$intTempFacilityId = $intFacilityId;
			$dtTempApptDate = $dtApptDate;
			
		$arr=array();
		$arr[]= $strProviderNm;
		$arr[]= $strFacilityNm;
		$arr[]= get_date_format(date("Y-m-d",$tsApptStartDate));
		$arr[]= $strApptStartTime;
		$arr[]= $strApptDuration.' Min';
		$arr[]= $strPatient_Nm;
		if($blIncludePatientKey == true){ 
			$arr[]= $ptkey;
		}
		if($blIncludePatientDob == true){	
			$arr[]= $dtPatientDOB; 
		}
		
		if($blIncludeInsurance == true)
		{		
			if(isset($arr_ins[$intPatientId]) && is_array($arr_ins[$intPatientId]["ins_details"]))
			{ 
				$int_ins_details = count($arr_ins[$intPatientId]["ins_details"]);
				if($int_ins_details > 0){
					$insCnt = 1;$csv_counter =0;
						
					foreach($arr_ins[$intPatientId]["ins_details"] as $thisInsDetail){
						
						$csv_counter++;
						if($csv_counter == 1){
							$arr=array();
							$arr[]= $strProviderNm;
							$arr[]= $strFacilityNm;
							$arr[]= get_date_format(date("Y-m-d",$tsApptStartDate));
							$arr[]= $strApptStartTime;
							$arr[]= $strApptDuration.' Min';
							$arr[]= $strPatient_Nm;
							if($blIncludePatientKey == true){ 
								$arr[]= $ptkey;
							}
							if($blIncludePatientDob == true){	
								$arr[]= $dtPatientDOB; 
							}
						
							if($blIncludePatientAddress == true){	
								$arr[]=$csvDemo;
							}else{
								$arr[]=core_phone_format($intPhoneNo);
							}
							$arr[]=$thisInsDetail["ins_comp"].' ('.$thisInsDetail["ins_case"].')<br>'.$thisInsDetail["policyno"].' '.numberFormat((float)$thisInsDetail["copayamt"],2);
							if($thisInsDetail["casetype"] == 1){
								$arr[]=$thisInsDetail["refcount"].' / '.$thisInsDetail["reffused"];
							}else{
								$arr[]='N/A';
							}
							$arr[]=$thisInsDetail["fname"].' '.$thisInsDetail["lname"].' '.$thisInsDetail["rpssn"].' '.$thisInsDetail["rpdob"];
							$arr[]=$strProcedureNm.' '.((!empty($insApptCaseId) && isset($arr_ins_case[$insApptCaseId]) && !empty($arr_ins_case[$insApptCaseId])) ? $arr_ins_case[$insApptCaseId]." - ".$insApptCaseId : "N/A" );
							$arr[]=$strComments_str;
							$arr[]=$ps_user.' '.$stApptMadeDate;
							$arr[]=$pendingCopay;
							$arr[]=$pt_due;
							fputcsv($fp,$arr, ",","\"");
						}else{
							$arr=array();
							$arr[]= $strProviderNm;
							$arr[]= $strFacilityNm;
							$arr[]= get_date_format(date("Y-m-d",$tsApptStartDate));
							$arr[]= $strApptStartTime;
							$arr[]= $strApptDuration.' Min';
							$arr[]= $strPatient_Nm;
							if($blIncludePatientKey == true){ 
								$arr[]= $ptkey;
							}
							if($blIncludePatientDob == true){	
								$arr[]= $dtPatientDOB; 
							}
							if($blIncludePatientAddress == true){	
								$arr[]=$csvDemo;
							}else{
								$arr[]=core_phone_format($intPhoneNo);
							}

							$arr[]=$thisInsDetail["ins_comp"].' ('.$thisInsDetail["ins_case"].')<br>'.$thisInsDetail["policyno"].' '.numberFormat((float)$thisInsDetail["copayamt"],2);
							if($thisInsDetail["casetype"] == 1){
								$arr[]=$thisInsDetail["refcount"].' / '.$thisInsDetail["reffused"];
							}else{
								$arr[]='N/A';
							}
							$arr[]=$thisInsDetail["fname"].' '.$thisInsDetail["lname"].' '.$thisInsDetail["rpssn"].' '.$thisInsDetail["rpdob"];
							$arr[]=$strProcedureNm.' '.((!empty($insApptCaseId) && isset($arr_ins_case[$insApptCaseId]) && !empty($arr_ins_case[$insApptCaseId])) ? $arr_ins_case[$insApptCaseId]." - ".$insApptCaseId : "N/A" );
							$arr[]=$strComments_str;
							$arr[]=$ps_user.' '.$stApptMadeDate;
							$arr[]=$pendingCopay;
							$arr[]=$pt_due;
							fputcsv($fp,$arr, ",","\"");
						}
					}
				}	
			}else{
				$arr=array();
				$arr[]= $strProviderNm;
				$arr[]= $strFacilityNm;
				$arr[]= get_date_format(date("Y-m-d",$tsApptStartDate));
				$arr[]= $strApptStartTime;
				$arr[]= $strApptDuration.' Min';
				$arr[]= $strPatient_Nm;
				if($blIncludePatientKey == true){ 
					$arr[]= $ptkey;
				}
				if($blIncludePatientDob == true){	
					$arr[]= $dtPatientDOB; 
				}
				if($blIncludePatientAddress == true){	
					$arr[]=$csvDemo;
				}	
				$arr[]='';
				$arr[]='';
				$arr[]='';			
				$arr[]=$strProcedureNm;
				$arr[]=$strComments_str;
				$arr[]=$ps_user.' '.$stApptMadeDate;
				$arr[]=$pendingCopay;
				$arr[]=$pt_due;
				fputcsv($fp,$arr, ",","\"");				
			}
		}else{
			$arr=array();
			$arr[]= $strProviderNm;
			$arr[]= $strFacilityNm;
			$arr[]= get_date_format(date("Y-m-d",$tsApptStartDate));
			$arr[]= $strApptStartTime;
			$arr[]= $strApptDuration.' Min';
			$arr[]= $strPatient_Nm;
			if($blIncludePatientKey == true){ 
				$arr[]= $ptkey;
			}
			if($blIncludePatientDob == true){	
				$arr[]= $dtPatientDOB; 
			}
			if($blIncludePatientAddress == true){	
				$arr[]=$csvDemo;
			}else{			
				$arr[]=core_phone_format($intPhoneNo);
			}
			$arr[]=$strProcedureNm;
			$arr[]=$strComments_str;
			$arr[]=$ps_user.' '.$stApptMadeDate;
			$arr[]=$pendingCopay;
			$arr[]=$pt_due;
			fputcsv($fp,$arr, ",","\"");
		}
	}
		
		
		//pre($status_countArr);
		
		$status_table .= "<table class=\"rpt_table rpt rpt_table-bordered\" width=\"1050\"><tr><td class=\"text_b_w\" colspan=\"2\">Status Breakdown</td></tr>";
		$status_table .="<tr><td width=\"250\" class=\"text_b_w\"><b>Status</b></td><td width=\"800\" class=\"text_b_w\"><b>Count</b></td></tr>";
		foreach($status_countArr as $id => $val) {
			$status_name = sr_get_appt_status_name_new($id);
			$status_count =  count($val); 
			$status_total += $status_count;
			$status_table .="<tr><td><b>$status_name</b></td>";
			$status_table .="<td>$status_count</td></tr>";
		}
		$status_table .="<tr><td class=\"text_b_w\"><b>Total</b></td><td class=\"text_b_w\"><b>$status_total</b></td></tr>";
		$status_table .= "</table>";
		
		$strHTML = str_replace("[__APPOINTMENT_CNT__]", $intDateApptNo, $strHTML);
		$pdfHTML = str_replace("[__APPOINTMENT_CNT__]", $intDateApptNo, $pdfHTML);
		if($blIncludePatientKey!==false){
			$strHTML .= '<tr>
						<td colspan='.$totalcol.' bgcolor="#ffffff"> </td>
					</tr>
					 <tr>
						<td align="right" bgcolor="#ffffff" class="text" colspan='.$totalcol.'> <b>Total Appointment(s): '.$intSerialNo.'</b> </td>
					 </tr>
				</table>
				'.$status_table.'
			</page>';
		$pdfHTML .= '<tr>
						<td class="text" colspan='.$totalcol.' bgcolor="#ffffff"> </td>
					</tr>
					 <tr>
						<td align="right" bgcolor="#ffffff" class="text" colspan='.$totalcol.'> <b>Total Appointment(s): '.$intSerialNo.'</b> </td>
					 </tr>
				</table>
				'.$status_table.'
				';
		}else{
		$strHTML .= '<tr>
						<td colspan='.$totalcol.' bgcolor="#ffffff"> </td>
					</tr>
					 <tr>
						<td align="right" bgcolor="#ffffff" class="text" colspan='.$totalcol.'> <b>Total Appointment(s): '.$intSerialNo.'</b> </td>
					 </tr>
				</table>
				'.$status_table.'
			</page>';
		$pdfHTML .= '<tr>
						<td class="text" colspan='.$totalcol.' bgcolor="#ffffff"> </td>
					</tr>
					 <tr>
						<td align="right" bgcolor="#ffffff" class="text" colspan='.$totalcol.'> <b>Total Appointment(s): '.$intSerialNo.'</b> </td>
					 </tr>
				</table>
				'.$status_table.'';	
		}
	}
}
$printPdFBtn = 1;
$showbtn = 0;
$file_location = write_html($pdfHTML);
if($_REQUEST['submitted_from_scheduler']=='yes' && trim($strHTML) != ""){ ?>
<form name="printFrmALLPDF" action="<?php echo $GLOBALS['webroot'] ?>/library/html_to_pdf/createPdf.php" method="POST">
	<input type="hidden" name="page" value="1.3" >
	<input type="hidden" name="onePage" value="false">
	<input type="hidden" name="op" value="l" >
	<input type="hidden" name="font_size" value="7.5">
	<input type="hidden" name="file_location" value="<?php echo $file_location; ?>">
</form>
<script type="text/javascript">
	document.printFrmALLPDF.submit();
</script>
<?php
}else{
	if(trim($strHTML) != ""){
		echo $strHTML;
		$showbtn = 1;
	} else{
		echo '<div class="text-center alert alert-info" align="center">No Record Found.</div>';
	}
}
?>