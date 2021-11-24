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
$ignoreAuth = true;
$skip_file="skipthisfile";
/*Set Practice Name - for dynamically including config file*/
if($argv[1]){
	$practicePath = trim($argv[1]);
	$_SERVER['HTTP_HOST'] = $practicePath;
}
$cron_job="yes";
$skip_file_process="yes";

//Function Files
$without_pat = "yes";
include_once(dirname(__FILE__)."/../../config/globals.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once($GLOBALS['fileroot'] .'/library/classes/class.reports.php');

require_once($GLOBALS['fileroot'].'/library/classes/cls_common_function.php');

set_time_limit(0);

unset($_SESSION['scheduledIdUpdated']);
$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;


$callFrom = 'scheduled';
$html_file_name ='';
$thisDate= date('Y-m-d');
$thisTime= date('H:i:s');

$qry="Select * FROM reports_crone_jobs WHERE next_execution_date='".$thisDate."' 
AND DATE_FORMAT(executed, '%Y-%m-%d')<= '".$thisDate."' 
AND next_execution_time<= '".$thisTime."'
AND executed!= CONCAT(next_execution_date, ' ', next_execution_time) 
AND status='active' ORDER BY next_execution_date, next_execution_time";

$rs= imw_query($qry) or die(imw_error());
while($res =imw_fetch_array($rs)){
	$id=  $res['id'];
	$arrRptNamesTemp  = explode(',', $res['report']);
	
	//create js array to call ajax
	foreach($arrRptNamesTemp as $rptName){
		$arrRptNames[$id][] = $rptName;
	}
}

foreach($arrRptNames as $called_sch_id => $sch_Arr_Data){
	foreach($sch_Arr_Data as $rpt_to_execute){
		$output_option='';
		$_REQUEST['id']=$called_sch_id;
		$_REQUEST['rpt_name'] = $rpt_to_execute;
		
		//Main Code
		$qry="Select * FROM reports_crone_jobs WHERE id=$_REQUEST[id]";
		$rs= imw_query($qry);
		$res =imw_fetch_array($rs);
		
		$printFile=true;
		$SCH_rptName=$_REQUEST['rpt_name'];
		$SCH_SCHEDULE_ID  = $res['id'];
		$SCH_RPT_EXE_PERIOD = $res['executionPeriod'];
		$HOUR_OPTIONS_SEL = $res['hour_options'];
		$output_option = ($res['output_option']=='output_csv' || $res['output_option']=='') ? 'output_csv' : 'output_pdf';
		$SFTP_ADDRESS = $res['sftp_address'];
		$SFTP_USER = $res['sftp_user'];
		$SFTP_PASSWORD = $res['sftp_password'];
		$SFTP_DIRECTORY = $res['sftp_directory'];
		$SFTP_PORT = $res['sftp_port'];
		
		$arrWEEKDAYS = explode(',', $res['weekday_options']);
		$arrMONTHS = explode(',', $res['month_options']);
		$arrQUARTERLY = explode(',', $res['quarterly']);
		$nextExecutionDate = $res['next_execution_date'];
		$nextExecutionTime = $res['next_execution_time'];
		
		$allArr= explode('~~', $res['searched_criteria']);
		
		$SCH_STR_GROUPS 		= strToArr($allArr[0]);
		$SCH_STR_FACILITY 		= strToArr($allArr[1]);
		$SCH_STR_PHYSICIAN 		= strToArr($allArr[2]);
		$SCH_RPT_VIEW 			= $allArr[3];
		if(empty($allArr[4])==false){
			$SCH_STR_OPERAOTR 	= explode(",",$allArr[4]);
		}
		$SCH_STR_CPTCODES 		= strToArr($allArr[5]);
		$SCH_STR_DXCODES 		= strToArr($allArr[6]);
		$SCH_STR_INSGROUPS 		= strToArr($allArr[7]);
		$SCH_STR_INSCOMPANIES 	= strToArr($allArr[8]);
		$SCH_STR_RVU 			= $allArr[9];
				
		$period = $res['executionPeriod'];

		if(strstr($period,'~')){
			$arr=explode('~', $period);
			
			$arrExecutionDate['start'] = date('m-d-Y', strtotime($arr[0]));
			$arrExecutionDate['end'] = date('m-d-Y', strtotime($arr[1]));
		}else{
			$arrExecutionDate = getExecutionDates($period);
		}

		//$arrExecutionDate['start']='01-01-2014';
		//$arrExecutionDate['end']='01-31-2014';
		
		$arrExecutedData=array();
		$reportExecuted=0;
		$e=0;
		//REPORTS TO BE EXEUCTE
	
		$html_file_name = '';
		$fileNAME = '';
		$filePath = '';
		$SCH_STR_PHYSICIAN;	
		switch($SCH_rptName){
			case 'patients_csv_export':
				$_REQUEST['reg_from'] = $arrExecutionDate['start'];
				$_REQUEST['reg_to'] = $arrExecutionDate['end'];
				$Submit = 1;
				$_REQUEST['Submit'] = 1;
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				$fileNAME = 'patients_csv_export_'.time(); 
				include_once('detail_patient_report_print.php');
			break;
			
			case 'scheduler_report':
				$groups = $SCH_STR_GROUPS;
				$facility_name = $SCH_STR_FACILITY;
				$phyId = $SCH_STR_PHYSICIAN;
				$operator_id = $SCH_STR_OPERAOTR;
				$insId = $SCH_STR_INSCOMPANIES;
				$Dxcode10 = $SCH_STR_DXCODES;
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$Submit = 1;
				$_REQUEST['Submit'] = 1;
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				$fileNAME = 'scheduler_report_'.time(); 
				include_once('scheduler_report_default_result.php');
				
			break;

			case 'day_sheet':
				$groupId = $SCH_STR_GROUPS;
				$facility_name = $SCH_STR_FACILITY;
				$phyId = $SCH_STR_PHYSICIAN;
				$sel_date = $arrExecutionDate['start'];
				$printsummary = 'Yes';
				$newpage = 'No';
				$getReport = 'getReport';
				$Submit = 1;
				$_REQUEST['Submit'] = 1;
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				$fileNAME = 'day_sheet_'.time(); 
				include_once('billing_report_result.php');
				
			break;
			case 'payments':
				$groupId = $SCH_STR_GROUPS;
				$facility_name = $SCH_STR_FACILITY;
				$rqArrPhyId = $SCH_STR_PHYSICIAN;
				$rqArrOprId = $SCH_STR_OPERAOTR;
				//$department = '';
				$summary_detail = $SCH_RPT_VIEW;
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$DateRangeFor='dot';
				$groupBy = 'physician';
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				$Submit = 1;
				$_REQUEST['Submit'] = 1;
				$fileNAME = 'payments_'.time(); 
				include_once('payments_result.php');
				
			break;	
			case 'front_desk':
				$grp_id = $SCH_STR_GROUPS;
				$sc_name = $SCH_STR_FACILITY;
				$Physician = $SCH_STR_PHYSICIAN;
				$operatorId = $SCH_STR_OPERAOTR;
				$groupBy = 'physician';
				$sortBy = 'patientName';
				
				$processReport = $SCH_RPT_VIEW;
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$fileNAME = 'front_desk_'.time(); 
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				$Submit = 1;
				$_REQUEST['Submit'] = 1;
				include_once('front_desk_report.php');
				
			break;
			case 'copay_recon':
				$operator_id = $SCH_STR_OPERAOTR;
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				//$fileNAME = 'copay_recon_'.time(); 
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				$Submit = 1;
				$_REQUEST['Submit'] = 1;
				$fileNAME = 'copay_reconciliation_'.time(); 
				include_once('copay_reconciliation_result.php');
				
			break;
			case 'unapplied_superbills':
				$core_grp_id = $SCH_STR_GROUPS;
				$facility_name = $SCH_STR_FACILITY;
				$phyId = $SCH_STR_PHYSICIAN;
				$summary_detail = strtolower($SCH_RPT_VIEW);
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				$fileNAME = 'unapplied_superbills_'.time(); 
				include_once('missing_encounters_report.php');
				
			break;	
			case 'unfinalized_encs':
				$phyId = $SCH_STR_PHYSICIAN;
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				$fileNAME = 'unfinalized_encounters_'.time(); 
				include_once('unfinalized_encounters_result.php');
				
			break;						
			case 'unapplied_payments':
				$groups = $SCH_STR_GROUPS;
				$facility_name = $SCH_STR_FACILITY;
				$phyId = $SCH_STR_PHYSICIAN;
				$operator_id = $SCH_STR_OPERAOTR;
				$Process = strtolower($SCH_RPT_VIEW);
				$start_date = $arrExecutionDate['start'];
				$end_date = $arrExecutionDate['end'];
				$fileNAME = 'unapplied_payments_'.time();
				$Submit = 'submitted';
				include_once('unapplied_amounts_result.php');
				
			break;						
			case 'adjustment':
				$groups = $SCH_STR_GROUPS;
				$facility_name = $SCH_STR_FACILITY;
				$phyId = $SCH_STR_PHYSICIAN;
				$operator_id = $SCH_STR_OPERAOTR;
				//wrt_code
				//batchFiles
				//dtRangeFor (transaction, dos)
				$onePage = FALSE;
				$summary_detail = $SCH_RPT_VIEW;
				$dtRangeFor = 'dot';
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$Submit = 'submitted';
				$fileNAME = 'adjustments_report_'.time(); 
				
				include_once('adjustment_report_result.php');
				
			break;	
			case 'refund':
				$groups = $SCH_STR_GROUPS;
				$facility_name = $SCH_STR_FACILITY;
				$phyId = $SCH_STR_PHYSICIAN;
				$operator_id = $SCH_STR_OPERAOTR;
				$startLname='a';
				$endLname='z';
				//patientId
				$summary_detail = strtolower($SCH_RPT_VIEW);
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				$fileNAME = 'refund_report_'.time(); 
				include_once('refund_report_result.php');
				
			break;	
			case 'daily_balance':
				$groupId = $SCH_STR_GROUPS;
				$facility_name = $SCH_STR_FACILITY;
				$rqArrPhyId = $SCH_STR_PHYSICIAN;
				$rqArrOprId = $SCH_STR_OPERAOTR;
				$summary_detail = strtolower($SCH_RPT_VIEW);
				$summary_detail = ($summary_detail=='detail') ? 'details' : $summary_detail;
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$Submit = '1';
				$_POST['Submit'] = '1';
				$_POST['form_submitted'] = '1';
				$fileNAME = 'daily_balance_'.time(); 
				include_once('daily_balance_result.php');
				
			break;	
			case 'fd_collection':
				$grp_id = $SCH_STR_GROUPS;
				$sc_name = $SCH_STR_FACILITY;
				$Physician = $SCH_STR_PHYSICIAN;
				$operatorId = $SCH_STR_OPERAOTR;
				$processReport = $SCH_RPT_VIEW;
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$sortBy = 'patientName';
				$Submit = 'submitted';
				$fileNAME = 'fd_collection_'.time(); 
				include_once('fd_collection_report.php');
				
			break;	
			case 'practice_analytics':
				$grp_id = $SCH_STR_GROUPS;
				$facility_id = $SCH_STR_FACILITY;
				$filing_provider = $SCH_STR_PHYSICIAN;
				$viewBy = 'physician';
				$insuranceGrp = $SCH_STR_INSGROUPS;
				$ins_carriers = $SCH_STR_INSCOMPANIES;
				$cpt_code_id = $SCH_STR_CPTCODES;
				$icd10_codes = $SCH_STR_DXCODES;
				$operator_id = $SCH_STR_OPERAOTR;
				$processReport = $SCH_RPT_VIEW;
				$DateRangeFor = 'transaction_date';
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				$fileNAME = 'practice_analytics_'.time(); 

				include_once('productivity_result.php');
				
			break;	
		
			case 'cpt_analysis':
				$grp_id = $SCH_STR_GROUPS;
				$sc_name = $SCH_STR_FACILITY;
				$Physician = $SCH_STR_PHYSICIAN;
				$cpt_cat_id = $SCH_STR_CPTCODES;
				$ins_group = $SCH_STR_INSGROUPS;
				$ins_carriers = $SCH_STR_INSCOMPANIES;
				$DateRangeFor = 'date_of_service';
				$total_method = 'total_charges';
				$rvu = $SCH_STR_RVU;
				$groupby = 'Physician';
				$Process = $SCH_RPT_VIEW;
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				$fileNAME = 'cpt_analysis_'.time();
				include_once('ProceduralResult.php');
				
			break;
			
			case 'yearly':
				$grp_id = $SCH_STR_GROUPS;
				$sc_name = $SCH_STR_FACILITY;
				$Physician = $SCH_STR_PHYSICIAN;
				$groupby = 'Physician';
				$dd = explode('-', $arrExecutionDate['start']);
				$start = $dd[1].'-'.$dd[0].'-'.$dd[2];
				$Start_year = date('Y', strtotime($start));
	
				$dd = explode('-', $arrExecutionDate['end']);
				$end = $dd[1].'-'.$dd[0].'-'.$dd[2];
				$End_year = date('Y', strtotime($end));
				$fileNAME = 'yearly_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				include_once('yearly_report_result.php');
			break;		
			
			case 'ledger':
				$grp_id = $SCH_STR_GROUPS;
				$facility_id = $SCH_STR_FACILITY;
				$filing_provider = $SCH_STR_PHYSICIAN;
				$ins_carriers = $SCH_STR_INSCOMPANIES;
				$insuranceGrp= $SCH_STR_INSGROUPS;
				$operator_id = $SCH_STR_OPERAOTR;
				$reportType='';
				$reportWise='';
				$_REQUEST['groupBy'] = 'physician';
				$DateRangeFor = 'dot';
				$processReport = $SCH_RPT_VIEW;
				$_REQUEST['searchDFrom']= $Start_date = $arrExecutionDate['start'];
				$_REQUEST['searchDTo'] = $End_date = $arrExecutionDate['end'];
				$fileNAME = 'ledger_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				include_once('ledger_result.php');
			break;	
					
			case 'provider_revenue':
				$grp_id = $SCH_STR_GROUPS;
				$facility_id = $SCH_STR_FACILITY;
				$filing_provider = $SCH_STR_PHYSICIAN;
				$processReport = $SCH_RPT_VIEW;
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$fileNAME = 'provider_revenue_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				include_once('provider_revenue_result.php');
			break;			
	
			case 'facility_revenue':
				$grp_id = $SCH_STR_GROUPS;
				$facility_id = $SCH_STR_FACILITY;
				$filing_provider = $SCH_STR_PHYSICIAN;
				$processReport = $SCH_RPT_VIEW;
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$fileNAME = 'facility_revenue_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				include_once('facility_revenue_result.php');
			break;			
	
			case 'referring_revenue':
				$grp_id = $SCH_STR_GROUPS;
				$facility_id = $SCH_STR_FACILITY;
				$filing_provider = $SCH_STR_PHYSICIAN;
				$cpt_code_id = $SCH_STR_CPTCODES;
				$dx_code10 = $SCH_STR_DXCODES;
				$insuranceGrp = $SCH_STR_INSGROUPS;
				$insuranceName = $SCH_STR_INSCOMPANIES;
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$groupby = 'Physician';
				$encounter_type = 'all';
				$fileNAME = 'ref_physician_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				include_once('referring_physician_revenue_result.php');
			break;	
	
			case 'provider_analytics':
				$grp_id = $SCH_STR_GROUPS;
				$filing_provider = $SCH_STR_PHYSICIAN;
				$rvu = $SCH_STR_RVU;
				$DateRangeFor = 'transaction_date';
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$fileNAME = 'provider_analytics_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				include_once('provider_analytics_result.php');
			break;
	
			case 'credit_analysis':
				$grp_id = $SCH_STR_GROUPS;
				$sc_name = $SCH_STR_FACILITY;
				$Physician = $SCH_STR_PHYSICIAN;
				$process = $SCH_RPT_VIEW;
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$fileNAME = 'credit_analysis_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				include_once('creditResult.php');
			break;		

			case 'insurance_cases':
				$grp_id = $SCH_STR_GROUPS;
				$facility_id = $SCH_STR_FACILITY;
				$filing_provider = $SCH_STR_PHYSICIAN;
				$insuranceGrp = $SCH_STR_INSGROUPS;
				$ins_carriers = $SCH_STR_INSCOMPANIES;
				$cpt_code_id = $SCH_STR_CPTCODES;
				$processReport = $SCH_RPT_VIEW;
				$showCPT = '';
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$fileNAME = 'insurance_analytics_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				include_once('ins_analytics_result.php');
			break;				
	
			case 'eid_status':
				$grp_id = $SCH_STR_GROUPS;
				$comboFac = $SCH_STR_FACILITY;
				$comboProvider = $SCH_STR_PHYSICIAN;
				$process = $SCH_RPT_VIEW;
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				$fileNAME = 'eid_status_'.time(); 
				include_once('eid_status_result.php');
			break;	
			
			case 'allowable_verify':
				$grp_id = $SCH_STR_GROUPS;
				$sc_name = $SCH_STR_FACILITY;
				$Physician = $SCH_STR_PHYSICIAN;
				$process = $SCH_RPT_VIEW;
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$fileNAME = 'allowable_verify_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				include_once('allowVerifyResult.php');
			break;			
			
			case 'deferred_vip':
				$grp_id = $SCH_STR_GROUPS;
				$facility_id = $SCH_STR_FACILITY;
				$filing_provider = $SCH_STR_PHYSICIAN;
				$acc_status ='';
				$pt_status ='';
				$def_ins_chg=1;
				$def_pat_chg=1;
				$vip_pat=1;
				$process = $SCH_RPT_VIEW;
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$fileNAME = 'deferred_vip_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
	
				include_once('deferred_vip_result.php');
			break;	
			
			case 'rvu_report':
				$grp_id = $SCH_STR_GROUPS;
				$sc_name = $SCH_STR_FACILITY;
				$Physician = $SCH_STR_PHYSICIAN;
				$process = $SCH_RPT_VIEW;
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$submit_btn = 'submitted';
	
				include_once('rvu_result.php');
			break;			
	
			case 'sx_payment':
				$group_id = $SCH_STR_GROUPS;
				$sc_name = $SCH_STR_FACILITY;
				$report_search = 'DOP';
				$reportProcess = strtolower($SCH_RPT_VIEW);
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$Submit = 'submitted';
	
				include_once('surgical_report_result.php');
			break;	
					
			// A/R REPORTS
			case 'provider_ar':
				$groups = $SCH_STR_GROUPS;
				$facility_name = $SCH_STR_FACILITY;
				$phyId = $SCH_STR_PHYSICIAN;
				$groupBy = 'physician';
				$Start_date = $arrExecutionDate['start'];
				$End_date =  $arrExecutionDate['end'];
				$DateRangeFor='dos';
				$fileNAME = 'provider_ar_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
		
				include_once('dr_total_report_result.php');
			break;	
	
			case 'net_gross':
				$grp_id = $SCH_STR_GROUPS;
				$sc_name = $SCH_STR_FACILITY;
				$Physician = $SCH_STR_PHYSICIAN;
				$Start_date = $arrExecutionDate['start'];
				$arrStart = explode("-",$Start_date);
				$startYear = $arrStart[2];
				$startMonth = $arrStart[0];
				$End_date = $arrExecutionDate['end'];
				$arrEnd = explode("-",$End_date);
				$endYear = $arrEnd[2];
				$endMonth = $arrEnd[0];
				$Submit = 'submitted';
	
				include_once('net_gross_ratio_result.php');
			break;	
	
			case 'ar_reports':
				$_REQUEST['grp_id'] = $SCH_STR_GROUPS;
				$_REQUEST['sc_name'] = $SCH_STR_FACILITY;
				$_REQUEST['phyId'] = $SCH_STR_PHYSICIAN;
				$_REQUEST['insId'] = $SCH_STR_INSCOMPANIES;
				$_REQUEST['rep_proc'] = $SCH_STR_CPTCODES;
				$_REQUEST['icd9'] = $SCH_STR_DXCODES;
				$_REQUEST['run_qry'] = 'yes';
				$_REQUEST['Start_date'] = $arrExecutionDate['start'];
				$_REQUEST['End_date'] = $arrExecutionDate['end'];
				$_REQUEST['processReport'] = $SCH_RPT_VIEW;
				$srh_report='phy';
				
				include_once('acc_rec_insurance_result.php');
			break;	
	
			case 'days_in_ar':
				$grp_id = $SCH_STR_GROUPS;
				$sc_name = $SCH_STR_FACILITY;
				$Physician = $SCH_STR_PHYSICIAN;
				$ins_carriers = $SCH_STR_INSCOMPANIES;
				$fileNAME = 'days_in_ar_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
	
				include_once('days_in_ar_result.php');
			break;			
	
			case 'days_in_patient':
				$groups = $SCH_STR_GROUPS;
				$facility_name = $SCH_STR_FACILITY;
				$phyId = $SCH_STR_PHYSICIAN;
				$startLname = 'a'; // DEFAULT a
				$endLname = 'z'; // DEFAULT z
				$grt_bal = '0'; // DEFAULT 0
				$aging_start = '00'; // DEFAULT 00
				$aging_to = 'All'; // DEFAULT 181
				$patientId = '';
				$dayReport = '';
				$summary_detail = strtolower($SCH_RPT_VIEW);
				//$Start_date = $arrExecutionDate['start'];
				//$End_date = $arrExecutionDate['end'];
				$fileNAME = 'days_in_patient_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				include_once('patient_ar_aging.php');
				
			break;	
	
			case 'days_in_insurance':
				$groups = $SCH_STR_GROUPS;
				$facility_name = $SCH_STR_FACILITY;
				$phyId = $SCH_STR_PHYSICIAN;
				$insuranceGrp = $SCH_STR_INSGROUPS;
				$ins_carriers = $SCH_STR_INSCOMPANIES;
				$DateRangeFor='dos';
				$aging_start = '00'; // DEFAULT 00
				$aging_to = 'All'; // DEFAULT 181
				$BalanceAmount = '';
				//$Start_date = $arrExecutionDate['start'];
				//$End_date = $arrExecutionDate['end'];
				$summary_detail = strtolower($SCH_RPT_VIEW);
				$fileNAME = 'days_in_insurance_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				
				include_once('insuarnce_ar_aging.php');
				
			break;	
			
			case 'ar_trial_balance':
				$grp_id = $SCH_STR_GROUPS;
				$Facility = $SCH_STR_FACILITY;
				$aging_start = '00'; // DEFAULT 00
				$aging_to = 'All'; // DEFAULT 181
				//$Start_date = $arrExecutionDate['start'];
				//$End_date = $arrExecutionDate['end'];
				$Process = $SCH_RPT_VIEW;
				$Submit = 'submitted';
				include_once('trialBalanceResult.php');
				
			break;	
			
			case 'receivables':
				$groups = $SCH_STR_GROUPS;
				$facility_name = $SCH_STR_FACILITY;
				$phyId = $SCH_STR_PHYSICIAN;
				$insuranceGrp = $SCH_STR_INSGROUPS;
				$insuranceName = $SCH_STR_INSCOMPANIES;
				$insAmt =0;
				$startLname='a';
				$endLname='z';
				$aging_from = 'All';
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$process = $SCH_RPT_VIEW;
				$receivable_type='ins_rec';
				$fileNAME = 'receivables_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				
				include_once('account_receivable_result.php');
				
			break;
			
			case 'unworked_ar':
				$groups = $SCH_STR_GROUPS;
				$facility_name = $SCH_STR_FACILITY;
				$phyId = $SCH_STR_PHYSICIAN;
				$insuranceGrp = $SCH_STR_INSGROUPS;
				$ins_carriers = $SCH_STR_INSCOMPANIES;
				$groupBy = 'physician'; //facility
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$summary_detail = strtolower($SCH_RPT_VIEW);
				$fileNAME = 'unworked_ar_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
	
				include_once('unworked_ar_result.php');
				
			break;	
				
			case 'unbilled_claims':
				$groups = $SCH_STR_GROUPS;
				$facility_name = $SCH_STR_FACILITY;
				$Physician = $SCH_STR_PHYSICIAN;
				$insuranceGrp = $SCH_STR_INSGROUPS;
				$ins_carriers = $SCH_STR_INSCOMPANIES;
				$groupBy = 'physician'; //facility
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$process = $SCH_RPT_VIEW;
				$fileNAME = 'unbilled_claims_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				
				include_once('unbilled_claims_result.php');
				
			break;
			
			case 'top_rej_reasons':
				$operator_id = $SCH_STR_OPERAOTR;
				$insuranceGrp = $SCH_STR_INSGROUPS;
				$ins_carriers = $SCH_STR_INSCOMPANIES;
				$reason_code = '';
				$cpt = $SCH_STR_CPTCODES;
				$DateRangeFor = 'date_of_service';  // transaction_date
				$Start_date = $arrExecutionDate['start'];
				$End_date = $arrExecutionDate['end'];
				$process = $SCH_RPT_VIEW;
				$fileNAME = 'top_rej_reasons_'.time();
				$form_submitted = '1';
				$_POST['form_submitted'] = '1';
				
				include_once('top_rej_reasons_result.php');
				
			break;				
		}
		
		$filePath = data_path().'executed_reports/';
		if(!is_dir($filePath)) mkdir($filePath, 0777, true);

		//MAKE PDF/ZIP
		$pdfFileName = 'No Record Exists';
		
		//FOR PDF
		if($output_option!='output_csv'){
			if(file_exists($file_location)){
				$size_bytes = filesize($file_location);
				$size_mb = ($size_bytes/1024)/1024;
				
				if($size_mb > 5){ //MOVING TO CSV BLOCK
					$output_option='output_csv';
					$file_names=$html_file_name;
					$pdfFileName = $filePath.$html_file_name.'.html';
				}else{
					$fileInfo = pathinfo($file_location);
					$html_file_name = $fileInfo['filename'];
					$filePath = $filePath.$fileNAME.'.pdf';

					createSavePDF($filePath, $file_location, $op);
					$pdfFileName = $filePath;
				}
			}
		}
	
		//FOR CSV AND HTML
		if($output_option=='output_csv'){
			if(file_exists($csv_file_name)){ //WHERE SCRIPT ALREADY MADE CSV
				$new_file_path=$filePath.$fileNAME.'.csv';
				//COPYING FILE TO SAFE DIRECTORY
				copy($csv_file_name, $new_file_path);
				$pdfFileName = $new_file_path;
				
			}elseif(file_exists($file_location)){ //SCRIPT ONLY PROVIDING HTML FILE AND ONLY AT TIME OF DISPLAY IT WILL BE CONVERTED TO CSV
				$new_file_path=$filePath.$fileNAME.'.html';
				//COPYING FILE TO SAFE DIRECTORY
				copy($file_location, $new_file_path);
				$pdfFileName = $new_file_path;
				$csv_file_name=$new_file_path;
			}
		}
		
		
		if($SCH_rptName!='provider_monthly'){	
			$arrExecutedData[$e]['sch_id'] = $SCH_SCHEDULE_ID;
			$arrExecutedData[$e]['report_name'] = $SCH_rptName;
			$arrExecutedData[$e]['file_name'] = $pdfFileName;
			$arrExecutedData[$e]['html_file_name'] = ($output_option=='output_csv')? $csv_file_name : $html_file_name;
			$arrExecutedData[$e]['output_option']=$output_option;
			$arrExecutedData[$e]['exe_period'] = $SCH_RPT_EXE_PERIOD;
			$arrExecutedData[$e]['exe_date'] = date('Y-m-d H:i:s');
			
			$arrExecutedData[$e]['sftp_address'] = $SFTP_ADDRESS;
			$arrExecutedData[$e]['sftp_user'] = $SFTP_USER;
			$arrExecutedData[$e]['sftp_password'] = $SFTP_PASSWORD;
			$arrExecutedData[$e]['sftp_directory'] = $SFTP_DIRECTORY;
			$arrExecutedData[$e]['sftp_port'] = $SFTP_PORT;
		}
	
		unset($strHTML,$output_option,$html_file_name,$pdfFileName,$csv_file_name,$csv_data,$page_data,$data,$html_page_content,$page_content,$pdf_file_content,$page_html_script);
	

		foreach($arrExecutedData as $key => $data){
			
			$pdfFileName = 'No Record Exists';
			//$ext = pathinfo($data['file_name'], PATHINFO_EXTENSION);
			
			if(file_exists($data['file_name'])){
				$pdfFileName = $data['file_name'];
			}

			$rs=imw_query("Insert into reports_schedules_executed SET schedule_id='".$data['sch_id']."', executed_reports='".$data['report_name']."', file_path='".$pdfFileName."', executionPeriod='".$data['exe_period']."', executed_on='".$data['exe_date']."'")or die(imw_error());
			if($rs){ $reportExecuted=1; }
		
			if($_SESSION['reportExecuted']=='1'){
				$reportExecuted = 1;
				$_SESSION['reportExecuted']='';
			}
			
			//UPDATE NEXT EXECUTION DATE/TIME AND EXECUTED DATE/TIME
			 if($_SESSION['scheduledIdUpdated']!=$_REQUEST['id']){
			 
				$arrNextExeTime= $CLSReports->getNextRunTime($HOUR_OPTIONS_SEL, $arrWEEKDAYS, $arrMONTHS, $arrQUARTERLY);
				$next_execution_date_new = $arrNextExeTime['0']['year'].'-'.$arrNextExeTime['0']['month'].'-'.$arrNextExeTime['0']['day'];
				$next_execution_time_new = $arrNextExeTime['0']['hour'].':00:00';
	
				$up_qry="Update reports_crone_jobs SET executed='".$nextExecutionDate." ".$nextExecutionTime."', 
				next_execution_date='".$next_execution_date_new."', next_execution_time='".$next_execution_time_new."'	WHERE id='".$_REQUEST['id']."'";
				$rs=imw_query($up_qry);
				$_SESSION['scheduledIdUpdated']=$_REQUEST['id'];
				
			} 
		}

		//SFTP EXECUTION
		foreach($arrExecutedData as $key => $data){
			if($data['sftp_address']!='' && $data['sftp_user']!='' && $data['sftp_password']!=''){
				$dirName='';
				$sftp_credentials_set='1';
				$sftp_strServerIP = $data['sftp_address'];
				$sftp_strServerPort = ($data['sftp_port']!='')? $data['sftp_port'] : "22";
				$sftp_strServerUsername = $data['sftp_user'];
				$sftp_strServerPassword = $data['sftp_password'];
				$remote_directory=$data['sftp_directory'];
				$html_file_name=$data['html_file_name'];
				
				$path_info=pathinfo($html_file_name);
				
				if($path_info['extension']!='html' && $path_info['extension']!='htm'){

					if($data['output_option']=='output_csv'){
						$t_arr= explode('/', $html_file_name);
						$fileNAME=end($t_arr);
						array_pop($t_arr);
						$dirName= implode('/', $t_arr).'/';
					}else{
						$dirName = data_path().'executed_reports/';
					}
					
					include_once('Net/SFTP.php');
					/* Change the following directory path to your specification */
					$local_directory = $dirName;
					$remote_directory1 = $remote_directory.'/';//providing physical(full) path
					$file = $fileNAME;

					/* Add the correct FTP credentials below */
					$sftp = new Net_SFTP($sftp_strServerIP,$sftp_strServerPort,'1000');
					if (!$sftp->login($sftp_strServerUsername,$sftp_strServerPassword)){
						//exit('Login Failed');
					} else{
						//echo 'Login Successful';
					}

					if(file_exists($local_directory.$file))	{
						/* Upload the local file to the remote server put('remote file', 'local file'); */
						$success = $sftp->put($remote_directory1 . $file,$local_directory . $file, NET_SFTP_LOCAL_FILE);
						//echo "upload physical :".$success;
					}else{
						//echo 'file not found';
					}
				}

			}
		}
	}
				//End Main Code
}

function getExecutionDates($period){
	$arrExecutionDate =array();
	$today_dt = date("Y-m-d");
	switch($period){
		case "today":
			$Start_date = date('Y-m-d');
			$End_date = date('Y-m-d');
		break;
		case "last_day":
			$yesterday_dt = strtotime($today_dt)-24*60*60;
			$Start_date = date('Y-m-d', $yesterday_dt);
			$End_date = date('Y-m-d', $yesterday_dt);
		break;
		case "last_week":
			$previous_week = strtotime("-1 week +1 day");
			$start_week = strtotime("last sunday midnight",$previous_week);
			$end_week = strtotime("next saturday",$start_week);
			$Start_date = date("Y-m-d",$start_week);
			$End_date = date("Y-m-d",$end_week);
		break;
		case "last_month":
			$Start_date = date("Y-m-1", strtotime("last month"));
			$End_date = date("Y-m-t", strtotime("last month"));
		break;
		case "last_quarter":
			 $current_month = date('m');
			  $current_year = date('Y');
	
			  if($current_month>=1 && $current_month<=3)
			  {
				$Start_date = strtotime('1-October-'.($current_year-1));  // timestamp or 1-October Last Year 12:00:00 AM
				$End_date = strtotime('1-December-'.($current_year-1));  // // timestamp or 1-January  12:00:00 AM means end of 31 December Last year
			  } 
			  else if($current_month>=4 && $current_month<=6)
			  {
				$Start_date = strtotime('1-January-'.$current_year);  // timestamp or 1-Janauray 12:00:00 AM
				$End_date = strtotime('1-March-'.$current_year);  // timestamp or 1-April 12:00:00 AM means end of 31 March
			  }
			  else  if($current_month>=7 && $current_month<=9)
			  {
				$Start_date = strtotime('1-April-'.$current_year);  // timestamp or 1-April 12:00:00 AM
				$End_date = strtotime('1-June-'.$current_year);  // timestamp or 1-July 12:00:00 AM means end of 30 June
			  }
			  else  if($current_month>=10 && $current_month<=12)
			  {
				$Start_date = strtotime('1-July-'.$current_year);  // timestamp or 1-July 12:00:00 AM
				$End_date = strtotime('1-October-'.$current_year);  // timestamp or 1-October 12:00:00 AM means end of 30 September
			  }
			 $Start_date = date("Y-m-1", $Start_date);
			 $End_date = date("Y-m-t", $End_date);
		break;
		case "year_to_date":
			$Start_date = date('Y-01-01');
			$End_date = date("Y-m-d");
		break;		
	}
	$arrExecutionDate['start'] = date('m-d-Y', strtotime($Start_date));
	$arrExecutionDate['end'] = date('m-d-Y', strtotime($End_date));
	
	return $arrExecutionDate;
}	

function strToArr($val=""){
	$returnVal = '';
	if(empty($val)) return $returnVal;
	
	$tmpArr = explode(',', $val);
	if(is_array($tmpArr) && count($tmpArr) > 0) $returnVal = $tmpArr;

	return $returnVal;
}

//CREATE PDF FROM HTML
function createSavePDF($filePath='', $html_file_name, $op='l'){
	global $GLOBALS;
	$webadd = $GLOBALS['php_server'].'/library/html_to_pdf/createPdf.php';
	$pdf_path = $filePath;
	$urlPdfFile	= $webadd."?setIgnoreAuth=true&op=l&saveOption=fax&file_location=".$html_file_name."&pdf_name=".$pdf_path;
	$curNew = curl_init();
	curl_setopt($curNew,CURLOPT_URL,$urlPdfFile);
	curl_setopt ($curNew, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($curNew, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
	$data = curl_exec($curNew);
	curl_close($curNew);
	if(file_exists($filePath.$html_file_name)){
		unlink($filePath.$html_file_name);
	}
}
?>