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
$dateFormat= get_sql_date_format();
$phpDateFormat = phpDateFormat();
$curDate = date($phpDateFormat.' h:i A');		

//getting report generator name
$report_generator_name = "";
$mor_appt_flag = $eve_appt_flag = 0;
// SITE ARRAY

$strProviderIds = join(',',$phyId);
$form_submit = $_REQUEST['form_submitted'];

if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
	$op_name_arr = preg_split("/, /",$_SESSION["authProviderName"]);
	$report_generator_name = $op_name_arr[1][0];
	$report_generator_name .= $op_name_arr[0][0];
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

	$st_date = getDateFormatDB($Start_date);
	$en_date   = getDateFormatDB($End_date);
	
	//FACILITY
	$facility_name_str = join(',',$facility_name);
	//PHYSICIAN
	$physician_id_str = join(',',$phyId);

	//USERS
	$providerRs = imw_query("Select id,fname,mname,lname,username from users");
	$providerNameArr = array();
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$name= core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
		$providerNameArr[$id] = $name;
	}
	//FACILITIES
	$qry = "Select id, name FROM facility";
	$qryRs = imw_query($qry);
	$arrAllFacilities=array();
	while($qryRes  =imw_fetch_assoc($qryRs)){
		$arrAllFacilities[$qryRes['id']] = $qryRes['name'];
	}	
	//FETCH PROCEDURES
	$qry = "Select id, proc from slot_procedures WHERE source=''";	
	$rs=imw_query($qry);
	$arrAllProcedures=array();
	while($res=imw_fetch_assoc($rs)){
		$arrAllProcedures[$res['id']]=$res['proc'];
	}

	$sch_query ="Select sa.*, DATE_FORMAT(sa.sa_app_start_date, '".$dateFormat."') as 'sa_app_start_date',
	pd.fname as 'pfname', pd.mname as 'pmname', pd.lname as 'plname'
	FROM schedule_appointments sa 
	JOIN patient_data pd ON pd.id = sa.sa_patient_id
	where sa.sa_app_start_date between '$st_date' and '$en_date'
	and sa.sa_patient_app_status_id NOT IN(203,201,18,19,20)";
	if(empty($physician_id_str) === false){
		$sch_query .= " and sa.sa_doctor_id in($physician_id_str)";
	}
	if(empty($facility_name_str) === false){
		$sch_query .= " and sa.sa_facility_id in($facility_name_str)";
	}
	$sch_query .= " order by sa.sa_app_start_date, sa.sa_app_starttime";
	$sch_query_res = imw_query($sch_query);

	$totalAppts=0;
	$arrPatientData = array();
	$arrFacility = array();
	$j = 0; $k =  0;
	//for($i=0;$i<count($sch_query_res);$i++){
	while($res=imw_fetch_assoc($sch_query_res)){
		
		$facility='';	$docNameArr= array();	$patNameArr= array();
		$facility = $res['sa_facility_id'];
		$id = $res['id'];
		

		$doc_id = $res['sa_doctor_id'];
		$arrPatientData[$facility][$doc_id][$id]['DOCTORID'] = $doc_id;

		$patName= core_name_format($res['plname'], $res['pfname'], $res['pmname']);
		$arrPatientData[$facility][$doc_id][$id]['PATNAME'] = $patName."-".$res['sa_patient_id'];
		$arrPatientData[$facility][$doc_id][$id]['PROCEDURE'] = $arrAllProcedures[$res['procedureid']];
		
		$arrPatientData[$facility][$doc_id][$id]['APPDATE'] = $res['sa_app_start_date'];
		
		$app_time = getMainAmPmTime($res['sa_app_starttime']);
		$arrPatientData[$facility][$doc_id][$id]['APPTIME'] = $app_time;
		
		// Total Appointments
		$arrDocTotal[$facility][$doc_id][] = 1;
		$arrApptTotal[$facility][] = 1;
		$totalAppts+=1;
	}

	
	if(count($arrPatientData) > 0){
	
		$i = 0;
		foreach($arrPatientData as $fac_id =>$facData)
		{
			$facility_name=$arrAllFacilities[$fac_id];
				
			//PAGE DATA
			$page_data_part.='<tr><td class="text_b_w" colspan="6">Facility : '.$facility_name.' - Total Appointments ('.count($arrApptTotal[$fac_id]).')</td></tr>';

			$j=1;	$page_content='';
			foreach($facData as $docId => $arrPatData){
				$j=1;
				$page_data_part.='<tr><td class="text_b_w alignLeft"  colspan="6">Provider : '.$providerNameArr[$docId].' - Total Appointments ('.count($arrDocTotal[$fac_id][$docId]).')</td></tr>';	
				
				foreach($arrPatData as $patData){
					$page_data_part.='
					<tr  >
						<td class="text alignCenter white" style="width:10%;">'.$j.'</td>
						<td class="text alignLeft white" style="width:20%;">'.$providerNameArr[$docId].'</td>
						<td class="text alignLeft white" style="width:20%;">'.$patData['PATNAME'].'</td>
						<td class="text alignLeft white" style="width:20%;">'.$patData['PROCEDURE'].'</td>
						<td class="text alignLeft white" style="width:10%;">'.$patData['APPDATE'].'</td>
						<td class="text alignLeft white" style="width:20%;">'.$patData['APPTIME'].'</td>
					</tr>';
					$i++;	$j++;	
				}
			}
		}
		
		$sel_fac = $CLSReports->report_display_selected($facility_name_str,'facility_tbl',1,count($arrFacIds));
		$sel_phy = $CLSReports->report_display_selected($physician_id_str,'physician',1,$allPhyCount);
		
		//HEADER
		$header_html='<table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">	
		<tr>
			<td class="rptbx1" style="width:33%">Appointments on a certain day Report</td>	
			<td class="rptbx2" style="width:33%">From : '.$Start_date.' To : '.$End_date.'</td>					
			<td class="rptbx3" style="width:34%">Created By: '.$report_generator_name.' on '.$curDate." ".date("h:i A").'</td>
		</tr>
		<tr>
			<td class="rptbx1">Physician: '.$sel_phy.'</td>	
			<td class="rptbx2">Facility: '.$sel_fac.'</td>					
			<td class="rptbx3"></td>
		</tr>		
		</table>
		<table style="width:100%" class="rpt_table rpt_table-bordered">
		<tr>
			<td class="text_b_w alignCenter" style="width:10%;">Appt.</td>
			<td class="text_b_w alignCenter" style="width:20%;">Phy. Name</td>				
			<td class="text_b_w alignCenter" style="width:20%;">Patient Name-ID</td>
			<td class="text_b_w alignCenter" style="width:20%;">Procedure</td>
			<td class="text_b_w alignCenter" style="width:10%;">Appt. Date</td>
			<td class="text_b_w alignCenter" style="width:20%;">Appt. Time</td>
		</tr>
		</table>';
		
		$page_data=$header_html.
		'<table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">'
		.$page_data_part.'
		<tr><td class="text_10b alignLeft" colspan="6">Total Appointments : '.$totalAppts.'</td></tr>
		</table>';	

		//PDF
		$strHTML='
		<page backtop="15mm" backbottom="10mm">			
		<page_footer>
			<table style="width:100%;">
				<tr>
					<td style="text-align:center; width: 100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
		<page_header>
		'.$header_html.'
		</page_header>
		<table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">
		'.$page_data_part.'
		<tr><td class="text_10b alignLeft" colspan="6">Total Appointments : '.$totalAppts.'</td></tr>
		</table>
		</page>';	
	
	} // outermost IF	
}


$printPdFBtn = 1;
$printFile = 0;
if(trim($page_data) != ""){
	$HTMLCreated=1;
	$printFile = 1;
	$showbtn=1;
	
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$page_data= $styleHTML.$page_data;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$strHTML;

	$file_location = write_html($strHTML, 'appointments_on_certain_day.html');	
		
	echo $page_data;	
} else{
	echo '<div class="text-center alert alert-info">No Record Found.</div>';
}
?>