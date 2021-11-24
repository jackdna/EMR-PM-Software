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
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
require_once('common/report_logic_info.php');
require_once(dirname(__FILE__).'/../../library/classes/class.sparcs.php');
$objSparcs		= new SPARCS;
$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

$dateFormat= get_sql_date_format();
$curDate = date($phpDateFormat.' h:i A');

$page_data = '';	$printFile= false;

if($start_date == ""){
	$start_date = $curDate;
	$end_date = $curDate;
}
$curDate.='&nbsp;'.date(" h:i A");

$_POST['form_submitted']='1';

if($_POST['form_submitted']){

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


	$st_date = getDateFormatDB($Start_date);
	$en_date = getDateFormatDB($End_date);
	//$primaryProviderId = join(",",$providerID);
	//$facility_name_str = join(",",$facility_name);

	//getting report generator name
	$report_generator_name = NULL;
	if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
		$arr_report_generator_name = explode(" ", $_SESSION["authProviderName"]);
		$report_generator_name = substr($arr_report_generator_name[1], 0, 1).substr($arr_report_generator_name[0], 0, 1);
		$report_generator_name = strtoupper($report_generator_name);
	}

	$qry = "SELECT a.charge_list_id, a.encounter_id, a.patient_id, a.date_of_service,DATE_FORMAT(a.date_of_service, '".$date_format_SQL."') as 'date_of_service2', b.procCode, a.primary_provider_id_for_reports, SUM(b.units) as 'units',a.case_type_id, 
    b.diagnosis_id1, b.diagnosis_id2, b.diagnosis_id3, b.diagnosis_id4, b.modifier_id1,  GROUP_CONCAT(DISTINCT(cpt_fee_tbl.cpt_prac_code) SEPARATOR ', ') as 'cpt_code',
	pd.fname, pd.mname, pd.lname, pd.DOB 
	FROM patient_charge_list a 
    JOIN patient_data pd ON pd.id=a.patient_id 
	LEFT JOIN patient_charge_list_details b ON a.charge_list_id = b.charge_list_id 
	LEFT JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = b.procCode 
	WHERE b.procCharges !=0 
	AND b.del_status != '1'
	AND (date_of_service BETWEEN '".$st_date."' AND '".$en_date."')
	GROUP BY a.charge_list_id 
    ORDER BY a.patient_id, a.date_of_service, b.procCharges";
	
	$rs=imw_query($qry);
	
	while($res = imw_fetch_assoc($rs)){
		
		/*****skip if basic validation failed for record**/
		$all_dx_codes_array = $objSparcs->get_all_dx_codes($res['charge_list_id']);
		$sc_data_array = $objSparcs->get_sc_details($res['patient_id'],$res['date_of_service']);
		$subscriber_details = $objSparcs->get_patient_insurance($res['case_type_id'],$res['patient_id'],'primary',$res['date_of_service']);
		
		$discharge_hour = $admission_hour = "";
		if($sc_data_array && is_array($sc_data_array)){
			extract($sc_data_array);
			if(!empty($checked_out_time)){
				// reformate parameters for SPARCS format
				$discharge_hour = str_replace(array(":"," AM", " PM"), "", $checked_out_time);
				$discharge_hour = $objSparcs->getHourCode($discharge_time);
				$discharge_hour = str_replace(" ", "0", $discharge_hour);
			}
			if(!empty($checked_in_time)){
				// Admission
				$admission_hour = $objSparcs->getHourCode($checked_in_time);
				$admission_hour = str_replace(" ", "0", $admission_hour);
			}
		}

		$error = false;
		if(!$subscriber_details || !is_array($subscriber_details)) $error = true;
		else{
			if(empty($subscriber_details['policy_number'])) $error = true;
			if(empty($subscriber_details['InsCompPayerIdPro'])) $error = true;
		}
		if (empty($res['primary_provider_id_for_reports'])) $error = true;
		if (empty($res['DOB']) || $res['DOB']=='0000-00-00') $error = true;
		if (empty($res['patient_id'])) $error = true;
		if (empty($res['procCode'])) $error = true;
		if(!$all_dx_codes_array || !is_array($all_dx_codes_array) || count($all_dx_codes_array)==0) $error = true;
		if (empty($admission_hour)) $error = true;
		if (empty($discharge_hour)) $error = true;
		/******skip rule end here**********************/
		if($error) continue;
		
		$printFile = true;
		$patient_name = core_name_format($res['lname'], $res['fname'], $res['mname']);
		$patient_name.=' - '.$res['patient_id'];
		
		$arr=array();
		$arr[$res['diagnosis_id1']]=$res['diagnosis_id1'];
		$arr[$res['diagnosis_id2']]=$res['diagnosis_id2'];
		$arr[$res['diagnosis_id3']]=$res['diagnosis_id3'];
		$arr[$res['diagnosis_id4']]=$res['diagnosis_id4'];

		$arr=array_filter($arr);
		$arr=array_unique($arr);
		$str_dx_codes=implode(', ', $arr);

        $page_content.='
        <tr>
			<td class="text-center white" style="width:5%;">
				<div class="checkbox">
					<input type="checkbox" name="chk_box_charges" id="chk_'.$res['charge_list_id'].'" value="'.$res['charge_list_id'].'" class="chk_box_charges" checked="CHECKED"/> 
					<label for="chk_'.$res['charge_list_id'].'"> </div>
				</div>
			</td>
			<td class="text alignLeft white" style="width:20%;">'.$patient_name.'</td>
			<td class="text white" style="width:10%; text-align:center">'.$res['encounter_id'].'</td>
            <td class="text white" style="width:10%; text-align:center">'.$res['date_of_service2'].'</td>
			<td class="text alignLeft white" style="width:25%;">'.$res['cpt_code'].'</td>
			<td class="text alignLeft white" style="width:20%;">'.$str_dx_codes.'</td>
            <td class="text alignLeft white" style="width:10%; text-align:right">&nbsp;'.round($res['units']).'</td>							
        </tr>';

	}unset($rs);
		
	$HTMLCreated=0;
	if($printFile ==true){
		$HTMLCreated=1;
		$record_no-=1;
	
		$strHTML = '<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
		<tr>	
			<td class="rptbx1" width="350">SPARCS Report</td>
			<td class="rptbx2" width="350">DOS From '.$Start_date.' to '.$End_date.'</td>
			<td class="rptbx3" width="350">Created by '.strtoupper($createdBy).' on '.get_date_format(date("Y-m-d"))." ".date("h:i A").'&nbsp;</td>
		</tr>
		</table>';	
		$strHTML .= '<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="100" bgcolor="#FFF3E8">
		<tr>
			<td class="text_b_w" style="text-align:center;" width="5%">
				<div class="checkbox">
					<input type="checkbox" name="chk_box_all" id="chk_box_all" value="all" checked="CHECKED" onclick="selAllChkbox(this)" /> 
					<label for="chk_box_all"> </div>
				</div>
			</td>
			<td class="text_b_w" style="text-align:center;" width="20%">Patient Name-ID</td>
			<td class="text_b_w" style="text-align:center;" width="10%">EID</td>
			<td class="text_b_w" style="text-align:center;" width="10%">DOS</td>
			<td class="text_b_w" style="text-align:center;" width="25%">CPT</td>
			<td class="text_b_w" style="text-align:center;" width="20%">DX Codes</td>
			<td class="text_b_w" style="text-align:center;" width="10%">Units</td>
		</tr>'
		.$page_content
		.'</table>';
		
		echo $strHTML;
		
	}else{
		echo '<div class="text-center alert alert-info">No Record Found.</div><script>top.btn_show("PPR", Array());</script>';
	}
}
?>


 

