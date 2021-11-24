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
$page_data = NULL;
$curDate = date('Y-m-d');
if($Start_date == ""){
	$Start_date = $curDate;
	$End_date = $curDate;
}

if($_POST){
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$createdBy = ucfirst(trim($op_name_arr[1][0]));
	$createdBy .= ucfirst(trim($op_name_arr[0][0]));

	
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
	
		//--- CHANGE DATE FORMAT -------
	if($Start_date != '' && $End_date != ''){
		$start_date_whr = getDateFormatDB($Start_date);
		$end_date_whr = getDateFormatDB($End_date);	
	}

	$facility_name_str = join(',',$facility_name);
	$phyId_str = join(',',$phyId);	

	$sel_opr=imw_query("select id, fname, lname, mname from users");
	$arrAllPhysicians=array();
	while($row_opr=imw_fetch_array($sel_opr)){
		$opr_name="";
		$opr_name = ucfirst(trim($row_opr['fname'][0]));
		$opr_name .= ucfirst(trim($row_opr['lname'][0]));
		$opr_ins[$row_opr['id']]=$opr_name;
		$arrAllPhysicians[$row_opr['id']]=core_name_format($row_opr['lname'], $row_opr['fname'], $row_opr['mname']);
	}
	// GET FACILITIES DETAILS
	$qry = "Select id, name from facility";
	$rs=imw_query($qry)or die(imw_error().'_44');
	$arrAllFacilities = array();
	while($res = imw_fetch_array($rs)){
		$arrAllFacilities[$res['id']] = $res['name'];
	}	


	//FETCHING MAIN DATA
	$qry="Select ch.id as 'form_id', ch.patient_id, DATE_FORMAT(ch.date_of_service, '".$date_format_SQL."') as 'dos', ch.providerId, ch.facilityid, 
	plan.followup, pd.fname, pd.mname, pd.lname 
	FROM chart_master_table ch JOIN chart_assessment_plans plan ON plan.form_id=ch.id 
	JOIN patient_data pd ON pd.id=ch.patient_id 
	WHERE plan.followup!='' AND (ch.date_of_service BETWEEN '".$start_date_whr."' AND '".$end_date_whr."')";
	if(empty($facility_name_str)==false){
		$qry.=" AND ch.facilityid IN(".$facility_name_str.")";
	}
	if(empty($phyId_str)==false){
		$qry.=" AND ch.providerId IN(".$phyId_str.")";
	}	
	$qry.=" ORDER BY ch.date_of_service DESC";
	$rs=imw_query($qry) or die(imw_error());
	$arrMainData=array();
	
	while($res=imw_fetch_array($rs)){
		$form_id=$res['form_id'];
		$arrtemppatid[$res['patient_id']][$res['dos']]=$res['dos'];
		
		$ox = simplexml_load_string($res['followup']);
		$len = count($ox->fu);
		
		$arr=array();
		if($len > 0){
			foreach($ox->fu as $fux){
				if(!empty($fux->number)){
					$arrTmp = array();
					$arrTmp["number"] = "".$fux->number;
					$arrTmp["time"] = "".$fux->time;
					$arrTmp["visit_type"] = "".$fux->visit_type;
					$arrTmp["provider"] = $arrAllPhysicians[trim($fux->provider)];
					$arrTmp["chk_str"] = (!empty($arrTmp["number"])|| !empty($arrTmp["time"]) || !empty($arrTmp["visit_type"]))? $arrTmp["number"]."-".$arrTmp["time"]."-".$arrTmp["visit_type"] : '';					
					
					$arr[]= $arrTmp["chk_str"].', <strong>Provider:</strong> '.$arrTmp["provider"];
				}
			}
		}
		
		if(sizeof($arr)>0){
			$arrMainData[$form_id]['pat_id']= $res['patient_id'];
			$arrMainData[$form_id]['pat_name']=core_name_format($res['lname'], $res['fname'], $res['mname']);
			$arrMainData[$form_id]['provider']= $arrAllPhysicians[$res['providerId']];
			$arrMainData[$form_id]['facility']= $arrAllFacilities[$res['facilityid']];
			
			$arrMainData[$form_id]['dos']= $res['dos'];
			$arrMainData[$form_id]['followup']= implode(",<br>", $arr); 
		}
	}
	
	//GET NEXT APPT DATE
	if(sizeof($arrtemppatid)>0){
		$strtemppatid=implode(',', array_keys($arrtemppatid));
		
		$qry="Select sa_patient_id, DATE_FORMAT(sa_app_start_date, '".$date_format_SQL."') as 'sa_app_start_date' FROM schedule_appointments 
		WHERE sa_patient_id IN(".$strtemppatid.") AND sa_app_start_date>'".$start_date_whr."' and sa_patient_app_status_id not in(18,203) 
		ORDER BY sa_app_start_date";
		$rs=imw_query($qry);
		$arrApptInfo=array();
		
		while($res=imw_fetch_array($rs)){
			$arrApptInfo[$res['sa_patient_id']]=$res['sa_app_start_date'];
		}
	}unset($arrtemppatid);

	$htmlpart='';
	if(sizeof($arrMainData)>0){
		foreach($arrMainData as $form_id => $detdata){
			
			$pat_id=$detdata['pat_id'];
			$pat_name=$detdata['pat_name'].' - '.$pat_id;
			
			//if appt date is bigger than chart note date only then appt date will display
			$next_appt_date= ($arrApptInfo[$pat_id]>$detdata['dos'])? $arrApptInfo[$pat_id] : '';

			$htmlpart.='
			<tr>
				<td class="text_10" style="background:#FFFFFF; width:15%">'.$pat_name.'</td>
				<td class="text_10" style="text-align:center; background:#FFFFFF; width:10%">'.$detdata['dos'].'</td>
				<td class="text_10" style="text-align:left; background:#FFFFFF; width:15%">'.$detdata['provider'].'</td>
				<td class="text_10" style="text-align:left; background:#FFFFFF; width:15%">'.$detdata['facility'].'</td>
				<td class="text_10" style="text-align:left; background:#FFFFFF; width:35%">'.$detdata['followup'].'</td>
				<td class="text_10" style="text-align:left; background:#FFFFFF; width:10%">'.$next_appt_date.'</td>
			</tr>';
		}
	}
}

$hasData=0;
$op='l';
if($htmlpart){
	$hasData=1;
	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	
		
	$headerdata='<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding">
	<tr class="rpt_headers">
		<td class="rptbx1"  style="width:33%">Followups Report</td>
		<td class="rptbx2"  style="width:33%">Report Period: '.$Start_date.' to '.$End_date.'</td>
		<td class="rptbx3"  style="width:34%">Created by: '.$report_generator_name.' on '.date("".$phpDateFormat." h:i A").'</td>
	</tr>
	</table>';
	
	$htmldata=$headerdata.
	'<table style="width:100%" class="rpt_table rpt_table-bordered">
	<tr>
		<td class="text_b_w" style="width:15%">Pat Name-ID</td>
		<td class="text_b_w" style="width:10%; text-align:center">Chart DOS</td>
		<td class="text_b_w" style="width:15%; text-align:center">Physician</td>
		<td class="text_b_w" style="width:15%; text-align:center">Facility</td>
		<td class="text_b_w" style="width:35%; text-align:center">Followup Details</td>
		<td class="text_b_w" style="width:10%; text-align:center">Next Appt. Date</td>
	</tr>'
	.$htmlpart.
	'</table>';
	
	
	$pdfdata=$stylePDF.
	'<page backtop="10mm" backbottom="5mm">
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>'
	.$headerdata.
	'<table style="width:100%;" class="rpt_table rpt_table-bordered">
	<tr>
		<td class="text_b_w" style="width:15%">Pat Name-ID</td>
		<td class="text_b_w" style="width:10%; text-align:center">Chart DOS</td>
		<td class="text_b_w" style="width:15%; text-align:center">Physician</td>
		<td class="text_b_w" style="width:15%; text-align:center">Facility</td>
		<td class="text_b_w" style="width:35%; text-align:center">Followup Details</td>
		<td class="text_b_w" style="width:10%; text-align:center">Next Appt. Date</td>
	</tr>
	</table>
	</page_header>
	<table  class="rpt_table rpt_table-bordered" style="width:100%;>'
	.$htmlpart.
	'</table>
	</page>';
	
	
	
	$file_location = write_html($pdfdata, 'followups_report.html');
	
	echo $htmldata;
}else{
	echo '<div class="text-center alert alert-info">No Record Found.</div>';
}
?>

