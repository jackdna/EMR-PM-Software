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
$phpDateFormat = phpDateFormat();
$curDate = date($phpDateFormat.'_h:i');

$page_data = '';	$printFile= false;

if($start_date == ""){
	$start_date = $curDate;
	$end_date = $curDate;
}
$arrAllSelProcIds=array();
$curDate.='&nbsp;'.date(" h:i A");

if($_POST['form_submitted']){

	//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
	$arrDateRange= $CLSCommonFunction->changeDateSelection();

	if($dayReport=='Daily'){
		$start_date = $End_date= date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$start_date = $arrDateRange['WEEK_DATE'];
		$end_date= date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$start_date = $arrDateRange['MONTH_DATE'];
		$end_date= date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$start_date = $arrDateRange['QUARTER_DATE_START'];
		$end_date = $arrDateRange['QUARTER_DATE_END'];
	}

	$st_date = getDateFormatDB($start_date);
	$en_date = getDateFormatDB($end_date);
	$primaryProviderId = join(",",$providerID);
	$facility_name_str = join(",",$facility_name);

	//getting report generator name
	$report_generator_name = NULL;
	if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
		$arr_report_generator_name = explode(" ", $_SESSION["authProviderName"]);
		$report_generator_name = substr($arr_report_generator_name[1], 0, 1).substr($arr_report_generator_name[0], 0, 1);
		$report_generator_name = strtoupper($report_generator_name);
	}
	
	//GET ALL USERS
	$providerRs = imw_query("Select id,fname,mname,lname from users");
	$providerNameArr = array();
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$uLname = trim($providerResArr['lname']);
		$uFname = trim($providerResArr['fname']);
		$uMname = trim($providerResArr['mname']);
		$providerNameArr[$id] = core_name_format($uLname,$uFname,$uMname);
	}
	
	$qry="Select sa.id, sa.sa_doctor_id, sa.sa_patient_id, pd.lname, pd.fname, sa.sa_app_start_date, sa.procedureid, sa.sec_procedureid, sa.tertiary_procedureid, pd.DOB  as 'dob', pd.primary_care 
	FROM schedule_appointments sa JOIN patient_data pd 
	ON pd.id=sa.sa_patient_id WHERE (sa.sa_app_start_date BETWEEN '$st_date' AND '$en_date')";
	if(empty($primaryProviderId)==false){
		$qry.=" AND sa.sa_doctor_id IN(".$primaryProviderId.")";
	}
	if(empty($facility_name_str)==false){
		$qry.=" AND sa.sa_facility_id IN(".$facility_name_str.")";
	}
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$arr_main_result[$res['id']]=$res;
		$date_strtotime=strtotime($res['sa_app_start_date']);
		$arrTemp[$res['sa_patient_id']][$date_strtotime]=$res['id'];
	
		if($res['procedureid']>0)$arrAllSelProcIds[$res['procedureid']]=$res['procedureid'];
		if($res['sec_procedureid']>0)$arrAllSelProcIds[$res['sec_procedureid']]=$res['sec_procedureid'];
		if($res['tertiary_procedureid']>0)$arrAllSelProcIds[$res['tertiary_procedureid']]=$res['tertiary_procedureid'];
	}

	if(sizeof($arrTemp)>0){
		$printFile=true;
		$arr_new_established=array();
		$str_patients=implode(',', array_keys($arrTemp));
		$qry="Select id, patient_id, date_of_service FROM chart_master_table WHERE patient_id IN(".$str_patients.")";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$chart_dos=strtotime($res['date_of_service']);
			$arr_appt_dos= array_keys($arrTemp[$res['patient_id']]);
			
			foreach($arr_appt_dos as $appt_dos){
				if($chart_dos<$appt_dos){
					$sch_id=$arrTemp[$res['patient_id']][$appt_dos];
					$arr_new_established[$sch_id]='Established';
				}
			}
		}
		
		//GETTING NAME OF SELECTED PROCEDURES
		if(sizeof($arrAllSelProcIds)>0){
			$strAllSelProcIds=implode(',', $arrAllSelProcIds);
			$qry="Select id, proc FROM slot_procedures WHERE id IN(".$strAllSelProcIds.")";
			$rs=imw_query($qry);
			while($res=imw_fetch_assoc($rs)){
				$arrProcNames[$res['id']]=$res['proc'];
			}
		}
		
		//MAKING OUTPUT DATA
		$csvFileName = 'patient_referral.csv';
		if(in_array(strtolower($billing_global_server_name), array('bennett'))){
			$date_now = date("Y-m-d"); 
			$date = str_replace("-","",$date_now);
			$csvFileName = 'bb_imedicware_referral_export_'.$date.'.csv';
		}
		$pfx="|";
		$file_name= $csvFileName;
		$csv_file_name= write_html("", $file_name);
	
		//CSV FILE NAME
		//$csv_file_name = "../../data/".PRACTICE_PATH."/UserId_".$_SESSION['authId']."/tmp/eid_status_".time().'.csv';
		if(file_exists($csv_file_name)){
			unlink($csv_file_name);
		}
		$fp = fopen ($csv_file_name, 'a+');
		//$strData.="INTERNAL_ID".$pfx;
		$strData.="PATIENT_ID".$pfx;
		$strData.="PATIENT_NAME".$pfx;
		$strData.="DATE_OF_BIRTH".$pfx;
		$strData.="DATE_OF_SERVICE".$pfx;
		$strData.="APPOINTMENT_DATE".$pfx;
		$strData.="APPOINTMENT_TYPE".$pfx;
		$strData.="APPOINTMENT_REASON".$pfx;
		$strData.="REFERRAL_SOURCE".$pfx;
		$strData.="ATTENDING_DOCTOR".$pfx;
		$strData.="SOURCE_SYSTEM";//.$pfx;
		//$strData.="EXTRACTED_ON";
		$strData.= "\n";
		$fp=fopen($csv_file_name,'w');
		@fwrite($fp,$strData);
		@fclose($fp);
		
		foreach($arr_main_result as $sch_id => $apptData){
			$tempArrProc=array();
			
			if($apptData['procedureid']>0)$tempArrProc[]=$arrProcNames[$apptData['procedureid']];
			if($apptData['sec_procedureid']>0)$tempArrProc[]=$arrProcNames[$apptData['sec_procedureid']];
			if($apptData['tertiary_procedureid']>0)$tempArrProc[]=$arrProcNames[$apptData['tertiary_procedureid']];
			$strProcNames=implode(', ', $tempArrProc);
			
			$establishedVal = ($arr_new_established[$sch_id]=='Established') ? 'Established' : 'New';
			
			$lName = $apptData['lname'];
			$fName = $apptData['fname'];
			$ptName = core_name_format($lName, $fName);

			$page_content.='
			<tr>
				<td class="text alignLeft white" style="width:10%;">&nbsp;'.$apptData['sa_patient_id'].'</td>
				<td class="text alignLeft white" style="width:15%;">&nbsp;'.trim($ptName).'</td>
				<td class="text alignLeft white" style="width:8%;">&nbsp;'.$apptData['dob'].'</td>
				<td class="text alignLeft white" style="width:8%;">&nbsp;'.$apptData['sa_app_start_date'].'</td>
				<td class="text alignLeft white" style="width:8%;">&nbsp;'.$apptData['sa_app_start_date'].'</td>
				<td class="text alignLeft white" style="width:10%;">&nbsp;'.trim($establishedVal).'</td>
				<td class="text alignLeft white" style="width:15%;">&nbsp;'.trim($strProcNames).'</td>
				<td class="text alignLeft white" style="width:15%;">&nbsp;'.trim($apptData['primary_care']).'</td>
				<td class="text alignLeft white" style="width:15%;">&nbsp;'.$providerNameArr[$apptData['sa_doctor_id']].'</td>
			</tr>';

			//$strData.= "".$pfx; //$sch_id;
			$strData.= $apptData['sa_patient_id'].$pfx;
			$strData.= trim($ptName).$pfx;
			$strData.= $apptData['dob'].$pfx;
			$strData.= $apptData['sa_app_start_date'].$pfx;
			$strData.= $apptData['sa_app_start_date'].$pfx;
			$strData.= trim($establishedVal).$pfx;
			$strData.= trim($strProcNames).$pfx;
			$strData.= trim($apptData['primary_care']).$pfx;
			$strData.= $providerNameArr[$apptData['sa_doctor_id']].$pfx;
			$strData.="BB_IMEDICWARE";//.$pfx;
		//	$strData.=$extracted_on;
			$strData.= "\n";
			$fp=fopen($csv_file_name,"w");
			@fwrite($fp,$strData);
		}

		$page_data='
		<table class="rpt_table rpt_table-bordered rpt_padding" style="width:100%">
		<tr class="rpt_headers">
			<td class="rptbx1" style="width:33%">Patient Referral</td>	
			<td class="rptbx2" style="width:34%">From : '.$start_date.' To : '.$end_date.'</td>					
			<td class="rptbx3" style="width:33%">Created By: '.$report_generator_name.' on '.date($phpDateFormat)." ".date("h:i A").'</td>
		</tr>
		</table>
		<table class="rpt_table rpt_table-bordered" style="width:100%">
		<tr>
			<td class="text_b_w alignCenter" style="width:10%;">Patient Id</td>				
			<td class="text_b_w alignCenter" style="width:15%;">Patient Name</td>
			<td class="text_b_w alignCenter" style="width:8%;">DOB</td>
			<td class="text_b_w alignCenter" style="width:8%;">Date of Service</td>
			<td class="text_b_w alignCenter" style="width:8%;">Appt. Date</td>
			<td class="text_b_w alignCenter" style="width:10%;">Appt. Type</td>
			<td class="text_b_w alignCenter" style="width:15%;">Appt. Reason</td>
			<td class="text_b_w alignCenter" style="width:15%;">Referral Source</td>
			<td class="text_b_w alignCenter" style="width:15%;">Attending Doctor</td>
		</tr>
		</table>
		<table class="rpt_table rpt_table-bordered" style="width:100%">
		'.$page_content.'
		</table>';
		
	}
	fclose($fp);

	//DECIDING OUTPUT
	$HTMLCreated=0;
	$hasData=0;
	if($printFile == true and $page_data != ''){
		$hasData=1;
		$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
		$csv_file_data= $styleHTML.$page_data;
	}else{
		$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
	}
	echo $csv_file_data;;
}
?>