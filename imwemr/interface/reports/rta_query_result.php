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

$flotDECAmtTot = $flotCopayAmtTot = 0;
$flotAllTot = 0;
$intTotPatient = 0;
//$rqLayout = $_REQUEST['rdLayout']; // for getting to know the request is summary or detail
$blDataHave = false;
if($_POST['form_submitted']){
	
	$op_name_arr = explode(', ',strtoupper($_SESSION['authProviderName']));
	$createdBy = ucfirst(trim($op_name_arr[1][0]));
	$createdBy .= ucfirst(trim($op_name_arr[0][0]));
	
	$searchDateFrom = $searchDateTo = "";
	$dispDateFrom = $_REQUEST['Start_date'];
	$dispDateTo = $_REQUEST['End_date'];
	
	$arrDateRange= $CLSCommonFunction->changeDateSelection();
	if($dayReport=='Daily'){
		$Start_date = $End_date = date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$Start_date = $arrDateRange['WEEK_DATE'];
		$End_date = date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$Start_date = $arrDateRange['MONTH_DATE'];
		$End_date = date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$Start_date = $arrDateRange['QUARTER_DATE_START'];
		$End_date = $arrDateRange['QUARTER_DATE_END'];
	}

	//--- CHANGE DATE FORMAT ----
	$date_format_SQL = get_sql_date_format();
	$searchDateFrom = getDateFormatDB($Start_date);
	$searchDateTo = getDateFormatDB($End_date);
	
	$rqSelectedFac = implode("," ,$_REQUEST["comboFac"]);
	$rqSelectedPro = implode("," ,$_REQUEST["comboProvider"]);
	
	$createdOn = date(''.$phpDateFormat.' H:i A');
	
	function leap_year_check($req_year)
	{
		$return = 0;
		if($req_year % 400 == 0){ $return = 1; }
		elseif ($req_year % 100 == 0){ $return = 0; }
		elseif ($req_year % 4 == 0){ $return = 1; }
		else { $return = 0; }
		return $return;
	}
		
	$month_data = array();
	$month_data[1] = array("mn"=>"January","no_days"=>31);
	$month_data[2] = array("mn"=>"February","no_days"=>28);
	$month_data[3] = array("mn"=>"March","no_days"=>31);
	$month_data[4] = array("mn"=>"April","no_days"=>30);
	$month_data[5] = array("mn"=>"May","no_days"=>31);
	$month_data[6] = array("mn"=>"June","no_days"=>30);
	$month_data[7] = array("mn"=>"July","no_days"=>31);
	$month_data[8] = array("mn"=>"August","no_days"=>31);
	$month_data[9] = array("mn"=>"September","no_days"=>30);
	$month_data[10] = array("mn"=>"October","no_days"=>31);
	$month_data[11] = array("mn"=>"November","no_days"=>30);
	$month_data[12] = array("mn"=>"December","no_days"=>31);
	
	$searchDateToArr = explode('-',$searchDateTo);
	$searchDateFromArr = explode('-',$searchDateFrom);
	
	$syr = $searchDateFromArr[0];
	$smn = $searchDateFromArr[1];
	$sday = $searchDateFromArr[2];
	
	$eyr = $searchDateToArr[0];
	$emn = $searchDateToArr[1];
	$eday = $searchDateToArr[2];
	
	$start_mn = $end_mn = 1;
	$hdrhtml = '<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050">
					<tr class=\"rpt_headers\">
						<td class="rptbx1" style="width:350px;">&nbsp;RTA Query Report</td>	
						<td class="rptbx2" style="width:350px;">&nbsp;From : '.$dispDateFrom.' To : '.$dispDateTo.'</td>
						<td class="rptbx3" style="width:350px;">&nbsp;Created by '.$createdBy.' on '.$createdOn.'</td>			
					</tr>
				</table>';
	$result_html = '<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050" background-color:#FFF3E8;" cellpadding="5" cellspacing="2">
					<tr>
						<td class="text_b_w" style="width:210px;">&nbsp;Month - Year </td>
						<td class="text_b_w" style="width:210px;">&nbsp;# of Appointments </td>
						<td class="text_b_w" style="width:210px;">&nbsp;# of Queries </td>
						<td class="text_b_w" style="width:210px;">&nbsp;# of Successful Queries</td>
						<td class="text_b_w" style="width:210px;">&nbsp;# of Unsuccessful Queries</td>
					</tr>';
	
	for($yr = $syr; $yr <= $eyr; $yr++)
	{
		$leap_status = leap_year_check($yr);
		if($leap_status == 1)
		{
			$month_data[2]["no_days"] = 29;
		}
		else
		{
			$month_data[2]["no_days"] = 28;			
		}
		
		if($yr == $syr){ $start_mn = (int)$smn;} else { $start_mn = 1;}
		if($yr == $eyr){ $end_mn = (int)$emn; } else { $end_mn = 12; }
		
		for($mn=$start_mn;$mn<=$end_mn;$mn++)
		{
			$rmn = $mn;			
			$start_day = "01";
			$end_day = $month_data[$mn]["no_days"];
			if($yr == $syr && $mn == $smn){ $start_day = $sday; }
			if($yr == $eyr && $mn == $emn){ $end_day = $eday; }
			if($rmn < 10){ $rmn = "0".$rmn; }	
			$req_start_date = $yr.'-'.$rmn.'-'.$start_day;
			$req_end_date = $yr.'-'.$rmn.'-'.$end_day;	

	$rqComboOperator = implode(",",$_REQUEST["comboOperator"]);

	// Query on the behalf of the Date of Query
	$qryGetElRep = "Select rtme.id from real_time_medicare_eligibility rtme  
					INNER JOIN patient_data patData ON patData.id = rtme.patient_id 
					WHERE patData.lname != 'doe' ";
				
	if(empty($searchDateFrom) == false && empty($searchDateTo) == false){
		$qryGetElRep .= " AND (date_format(rtme.request_date_time,'%Y-%m-%d') BETWEEN '$req_start_date' AND '$req_end_date')";
	}
	if(empty($rqComboOperator) == false){
		$qryGetElRep .= " AND request_operator IN(".$rqComboOperator.") ";
	}
	if(empty($rqSelectedFac) == false){
		$qryGetElRep .=" AND rtme.facility IN(".$rqSelectedFac.") ";
	}
	if(empty($rqSelectedPro) == false){
		$qryGetElRep .=" AND rtme.doctor_id IN(".$rqSelectedPro.") ";
	}								
	
	// Appointments count
	$qryGetPatient = "Select count(sa.id) as total_appts from schedule_appointments sa WHERE sa.rte_id IN(".$qryGetElRep.")";
	if(empty($rqSelectedFac) == false){
		$qryGetPatient .=" AND sa.sa_facility_id IN(".$rqSelectedFac.") ";
	}
	if(empty($rqSelectedPro) == false){
		$qryGetPatient .=" AND sa.sa_doctor_id IN(".$rqSelectedPro.") ";
	}	
	
	$rsGetPatient = imw_query($qryGetPatient);
	$appts_resultSet = imw_fetch_assoc($rsGetPatient);
	$total_appts = $appts_resultSet['total_appts'];
	
	
	// Total no. of Success Queries
	$qryGetElRep_successCount = "Select count(rtme.id) as success_queries from real_time_medicare_eligibility rtme  
					INNER JOIN patient_data patData ON patData.id = rtme.patient_id 
					WHERE patData.lname != 'doe' and transection_error = '' ";
				
	if(empty($searchDateFrom) == false && empty($searchDateTo) == false){
		$qryGetElRep_successCount .= " AND (date_format(rtme.request_date_time,'%Y-%m-%d') BETWEEN '$req_start_date' AND '$req_end_date')";
	}
	if(empty($rqComboOperator) == false){
		$qryGetElRep_successCount .= " AND request_operator IN(".$rqComboOperator.") ";
	}	
	if(empty($rqSelectedFac) == false){
		$qryGetElRep_successCount .=" AND rtme.facility IN(".$rqSelectedFac.") ";
	}
	if(empty($rqSelectedPro) == false){
		$qryGetElRep_successCount .=" AND rtme.doctor_id IN(".$rqSelectedPro.") ";
	}	
	
	$success_queries_obj = imw_query($qryGetElRep_successCount);
	$success_queries_result_set = imw_fetch_assoc($success_queries_obj);
	$success_queries = $success_queries_result_set['success_queries'];
	
	// Total no. of Failure Queries
	$qryGetElRep_failureCount = "Select count(rtme.id) as failure_queries from real_time_medicare_eligibility rtme  
					INNER JOIN patient_data patData ON patData.id = rtme.patient_id 
					WHERE patData.lname != 'doe' and transection_error != '' ";
				
	if(empty($searchDateFrom) == false && empty($searchDateTo) == false){
		$qryGetElRep_failureCount .= " AND (date_format(rtme.request_date_time,'%Y-%m-%d') BETWEEN '$req_start_date' AND '$req_end_date')";
	}
	if(empty($rqComboOperator) == false){
		$qryGetElRep_failureCount .= " AND request_operator IN($rqComboOperator)";
	}	
	if(empty($rqSelectedFac) == false){
		$qryGetElRep_failureCount .=" AND rtme.facility IN(".$rqSelectedFac.") ";
	}
	if(empty($rqSelectedPro) == false){
		$qryGetElRep_failureCount .=" AND rtme.doctor_id IN(".$rqSelectedPro.") ";
	}		
	
	$failure_queries_obj = imw_query($qryGetElRep_failureCount);
	$failure_queries_result_set = imw_fetch_assoc($failure_queries_obj);
	$failure_queries = $failure_queries_result_set['failure_queries'];
	
	$total_queries = $success_queries + $failure_queries;
	
	$result_html .= '
        <tr>
			<td style="background-color:#eeeeee;">'.$month_data[$mn]["mn"].' - '.$yr.'</td>
            <td>&nbsp;'.$total_appts.'</td>
			<td>&nbsp;'.$total_queries.'</td>
			<td>&nbsp;'.$success_queries.'</td>
			<td>&nbsp;'.$failure_queries.'</td>
        </tr>';
		}
	}
	
	$result_html .= '</table>';
	$strHTML = "<page backtop=\"4mm\" backbottom=\"1mm\">
			<page_footer>
				<table style=\"width:100%;\">								
					<tr>
						<td style=\"text-align:center;width:100%\">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
				<table class=\"rpt rpt_table rpt_table-bordered rpt_padding\" width=\"1050\" >
					<tr class=\"rpt_headers\">
						<td class=\"rptbx1\" style=\"width:347px;\">&nbsp;RTA Query Report</td>	
						<td class=\"rptbx2\" style=\"width:347px;\">&nbsp;From : $dispDateFrom To : $dispDateTo</td>
						<td class=\"rptbx3\" style=\"width:347px;\">&nbsp;Created by ".$createdBy." on ".$createdOn."</td>
					</tr>
				</table>
			</page_header>";	
	$strHTML .= $result_html.'</page>';
	$strCSS = '<style>' . file_get_contents('css/reports_pdf.css') . '</style>';
	$strHTML = $strCSS.$strHTML;
	
	echo $hdrhtml.$result_html;
	$hasData = 1;
	$file_location = write_html($strHTML);	
}
?>