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
FILE : patient_recall_result.php
PURPOSE : PATIENT APPOINTMENT RECALL REPORT
ACCESS TYPE : INCLUDED
*/
$dateFormat= get_sql_date_format();
$curDate = date($phpDateFormat.' h:i A');

$page_data = '';	$printFile= false;

if($start_date == ""){
	$start_date = $curDate;
	$end_date = $curDate;
}
$curDate.='&nbsp;'.date(" h:i A");

if($_POST['form_submitted']){

	$printFile = true;
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


	$st_date = getDateFormatDB($start_date);
	$en_date = getDateFormatDB($end_date);
	$primaryProviderId = join(",",$providerID);
	$facility_name_str = join(",",$facility_name);

	$firstFac = $facility_name[0];

	//getting report generator name
	$report_generator_name = NULL;
	if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
		$arr_report_generator_name = explode(" ", $_SESSION["authProviderName"]);
		$report_generator_name = substr($arr_report_generator_name[1], 0, 1).substr($arr_report_generator_name[0], 0, 1);
		$report_generator_name = strtoupper($report_generator_name);
	}
	

	//--- GET SCHEDULER First FACILITY NAME ----
	$qry = "Select id, name from facility ORDER BY name";
	$rs=imw_query($qry);
	$i=0;
	while($res=imw_fetch_assoc($rs)){
		$arrAllFacilities[$res['id']] = $res['name'];
		if($i==0){
			$firstFacName =$res['name'];
		}
		$i++;
	}
	
	$sch_query = "Select sa.id, sa.sa_patient_id, sa.sa_facility_id, sa.sa_doctor_id, DATE_FORMAT(sa.sa_app_start_date, '".get_sql_date_format()."') as 'sa_app_start_date',
	sa.sa_app_starttime, TIME_FORMAT(sa.sa_app_starttime, '%h:%i %p') as 'app_time', sa.hl7_appt_external_encounter_id, us.id as 'docID', 
	us.fname, us.lname, pd.fname as 'pfname', pd.mname as 'pmname', pd.lname as 'plname',
	pd.External_MRN_1, pd.External_MRN_2 
	from schedule_appointments sa 
	join users us on us.id = sa.sa_doctor_id JOIN patient_data pd ON pd.id = sa.sa_patient_id 
	where (sa.sa_app_start_date between '$st_date' and '$en_date') 
	and sa.sa_patient_app_status_id NOT IN(203,201,18,19,20,3)";
	if(empty($primaryProviderId) === false){
		$sch_query .= " and sa.sa_doctor_id in($primaryProviderId)";
	}
	if(empty($facility_name_str) === false){
		$sch_query .= " and sa.sa_facility_id in($facility_name_str)";
	}
	$sch_query .= " order by sa.sa_app_start_date, sa.sa_app_starttime";
	$rs=imw_query($sch_query) or die(imw_error());

	$totalAppts=0;
	$arrPatientData = array();
	$arrFacility = array();
	$j = 0; $k =  0;
	while($res=imw_fetch_assoc($rs)){
		
		$facility='';	$docNameArr= array();	$patNameArr= array();
		$facility = $res['sa_facility_id'];
		$sch_id= $res['id'];

		$doc_id = $res['docID'];
		$arrPatientData[$doc_id][$sch_id]['DOCTORID'] = $doc_id;
		$docName = core_name_format($res['lname'], $res['fname'], '');		
		$arrDocNames[$doc_id] = $docName;

		$patName = core_name_format($res['plname'],$res['pfname'],$res['pmname']);
		$arrPatientData[$doc_id][$sch_id]['PATNAME'] = $patName."-".$res['sa_patient_id'];
		
		$arrPatientData[$doc_id][$sch_id]['FACILITY'] = $facility;
		$arrPatientData[$doc_id][$sch_id]['APPDATE'] = $res['sa_app_start_date'];
		$app_time = $res['app_time'];
		$arrPatientData[$doc_id][$sch_id]['APPTIME'] = $app_time;
		$arrPatientData[$doc_id][$sch_id]['RCO'] = $res['hl7_appt_external_encounter_id'];
		
		$external_mrn_id='';
		if(empty($res["External_MRN_1"]) == false || empty($res["External_MRN_2"]) == false){
			if(empty($res["External_MRN_1"]) == false){
				if(strlen($res["External_MRN_1"]) == 6){
					$external_mrn_id="0".$res["External_MRN_1"];	
				}
				else{
					$external_mrn_id=$res["External_MRN_1"];
				}
			}
			elseif(empty($res["External_MRN_2"]) == false){
				if(strlen($res["External_MRN_2"]) == 6){
					$external_mrn_id="0".$res["External_MRN_2"];	
				}
				else{
					$external_mrn_id=$res["External_MRN_2"];
				}
			}
		}
		$arrPatientData[$doc_id][$sch_id]['EXTERNAL_MRN_ID'] = $external_mrn_id;
		
	}
	
	if(count($arrPatientData) > 0){
		//$strHTML = file_get_contents(dirname(_FILE__)."/../themes/default/pdf.css");
			
		foreach($arrPatientData as $docId => $phy_data){
			$j=1;
			$page_content.='<tr><td class="text_b_w alignLeft nowrap" colspan="7">Physician : '.$arrDocNames[$docId].'</td></tr>';
			
			foreach($phy_data as $sch_id => $patData){
				$page_content.='
				<tr>
					<td class="text alignCenter white" style="width:5%; height:20px;">'.$j.'</td>
					<td class="text alignLeft white" style="width:20%;">&nbsp;'.$arrAllFacilities[$patData['FACILITY']].'</td>
					<td class="text alignLeft white" style="width:25%;">&nbsp;'.$patData['PATNAME'].'</td>
					<td class="text alignLeft white" style="width:10%;">&nbsp;'.$patData['APPDATE'].'</td>
					<td class="text alignLeft white" style="width:10%;">&nbsp;'.$patData['APPTIME'].'</td>
					<td class="text alignLeft white" style="width:15%;">&nbsp;'.$patData['RCO'].'</td>
					<td class="text alignLeft white" style="width:15%;">&nbsp;'.$patData['EXTERNAL_MRN_ID'].'</td>
				</tr>';
				$j++;
			}
			
		}

		$page_data='
		<table class="rpt_table rpt_table-bordered rpt_padding">
		<tr class="rpt_headers">
			<td class="rptbx1" style="width:20%">PK Interface</td>	
			<td class="rptbx2" style="width:40%">From : '.$start_date.' To : '.$end_date.'</td>					
			<td class="rptbx3" style="width:40%">Created By: '.$report_generator_name.' on '.date($phpDateFormat)." ".date("h:i A").'</td>
		</tr>
		</table>
		<table class="rpt_table rpt_table-bordered">
		<tr>
			<td class="text_b_w alignCenter" style="width:5%;">#</td>
			<td class="text_b_w alignCenter" style="width:20%;">Facility</td>				
			<td class="text_b_w alignCenter" style="width:25%;">Patient Name-ID</td>
			<td class="text_b_w alignCenter" style="width:10%;">Appt. Date</td>
			<td class="text_b_w alignCenter" style="width:10%;">Appt. Time</td>
			<td class="text_b_w alignCenter" style="width:15%;">RCO AC#</td>
			<td class="text_b_w alignCenter" style="width:15%;">EXTERNAL MRN#</td>
		</tr>
		</table>
		<table class="rpt_table rpt_table-bordered">
		'.$page_content.'
		</table>';

		$pdf_data= '
			<page backtop="11mm" backbottom="10mm">			
				<page_footer>
					<table style="width:100%;">
						<tr>
							<td style="text-align:center; width: 100%">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				<page_header>
					<table class="rpt_table rpt_table-bordered rpt_padding" style="width:100%">
					<tr>
						<td class="rptbx1" style="width:20%">PK Interface</td>	
						<td class="rptbx2" style="width:40%">From : '.$start_date.' To : '.$end_date.'</td>					
						<td class="rptbx3" style="width:40%">Created By: '.$report_generator_name.' on '.date($phpDateFormat)." ".date("h:i A").'</td>
					</tr>
					</table>
					<table class="rpt_table rpt_table-bordered rpt_padding" style="width:100%">
					<tr>
						<td class="text_b_w alignCenter" style="width:5%;">#</td>
						<td class="text_b_w alignCenter" style="width:20%;">Facility</td>				
						<td class="text_b_w alignCenter" style="width:25%;">Patient Name-ID</td>
						<td class="text_b_w alignCenter" style="width:10%;">Appt. Date</td>
						<td class="text_b_w alignCenter" style="width:10%;">Appt. Time</td>
						<td class="text_b_w alignCenter" style="width:15%;">RCO AC#</td>
						<td class="text_b_w alignCenter" style="width:15%;">EXTERNAL MRN#</td>
					</tr></table>
				</page_header>
			<table style="width:100%" class="rpt_table rpt_table-bordered"  style="width:100%">'.
			$page_content
			.'</table>
			</page>';						
} // outermost IF	


//--- CREATE PDF FILE FOR PRINTING -----
$hasData=0;
if($printFile == true and $page_data != ''){
	$hasData=1;
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$page_data;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$pdf_data;

	$file_location = write_html($strHTML);
}else{
	$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
}

echo $csv_file_data;

}
?>