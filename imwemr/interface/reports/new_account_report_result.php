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
/*
FILE : new_account_report_result.php
PURPOSE :  SCHEDULER NEW REPORT RESULT
ACCESS TYPE : INCLUDED
*/
set_time_limit (300);
$page_data = NULL;
$curDate = date('Y-m-d');
if($Start_date == ""){
	$Start_date = $curDate;
	$End_date = $curDate;
}
$processReport = Ucfirst($summary_detail);
$opr_ins=array();
$sel_opr=imw_query("select id,fname,lname from users");
while($row_opr=imw_fetch_array($sel_opr)){
	$opr_name="";
	$opr_name = ucfirst(trim($row_opr['fname'][0]));
	$opr_name .= ucfirst(trim($row_opr['lname'][0]));
	$opr_ins[$row_opr['id']]=$opr_name;
}

$heard_arr=array();
$sel_heard=imw_query("select heard_options,heard_id from heard_about_us");
while($row_heard=imw_fetch_array($sel_heard)){
	$heard_arr[$row_heard['heard_id']]=$row_heard['heard_options'];
}

$fac_ids_arr=array();
$fac_id_data_arr=array();
$fac_query = imw_query("select pos_facilityies_tbl.pos_facility_id, pos_facilityies_tbl.facilityPracCode, 
			pos_tbl.pos_prac_code from pos_facilityies_tbl join pos_tbl 
			on pos_facilityies_tbl.pos_id = pos_tbl.pos_id order by pos_facilityies_tbl.facilityPracCode");
while($row_fac=imw_fetch_array($fac_query)){
	$pos_facility_id = $row_fac['pos_facility_id'];
	$facilityPracCode = $row_fac['facilityPracCode'];
	$pos_prac_code = $row_fac['pos_prac_code'];
	$fac_id_data_arr[$pos_facility_id] = $facilityPracCode.' - '.$pos_prac_code;
	$fac_ids_arr[]=$pos_facility_id;
}
$fac_ids_arr[]=0;
$fac_id_data_arr[0]="None";
if($_POST){
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$createdBy = ucfirst(trim($op_name_arr[1][0]));
	$createdBy .= ucfirst(trim($op_name_arr[0][0]));

	$facility_name_str = join(',',$facility_name);
	$phyId_str = join(',',$phyId);
	$heard_str = join(',',$heard);
	$operator_id_str = join(',',$operator_id);
	$app_type_str = join(',',$app_type);
	
	
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
	
	$age_start_date_whr = getDateFormatDB($age_start_date);
	$age_end_date_whr = getDateFormatDB($age_end_date);
	
	if($app_type_str || $date_criteria == "Sch Appt"){
		$sch_join=" join schedule_appointments appt on appt.sa_patient_id=patient_data.id";
		if($app_type_str)
		$sch_join_whr=" and appt.procedureid in($app_type_str) ";
		
		if($date_criteria == "Sch Appt")
			$sch_join_whr.=" and (appt.sa_app_start_date BETWEEN '$start_date_whr' and  '$end_date_whr')";
			
		$sch_join_whr.=" and appt.sa_patient_app_status_id not in (18,3)";	
	}
	$strQry = "select  
		patient_data.id,patient_data.lname,patient_data.fname,patient_data.mname,patient_data.email,
		patient_data.created_by,patient_data.heard_abt_us,patient_data.heard_abt_desc,
		DATE_FORMAT(patient_data.date,'%m-%d-%Y %h:%i %p') as pat_date,patient_data.default_facility,patient_data.primary_care_id 
		from patient_data $sch_join 
		LEFT JOIN pos_facilityies_tbl pos ON pos.pos_facility_id = patient_data.default_facility 
		LEFT JOIN users on users.id=patient_data.primary_care_id  
		where  patient_data.lname!='doe' ";
		if($date_criteria == "Patient Regis"){
			$strQry .= "AND DATE_FORMAT(patient_data.date,'%Y-%m-%d') BETWEEN '$start_date_whr' and  '$end_date_whr'";
		}
		
		
	 	$strQry .=" $sch_join_whr ";
		if($_REQUEST['facility_name']!=""){
			$strQry .= " and patient_data.default_facility IN ($facility_name_str)";
		}
		if($_REQUEST['phyId']!=""){
			$strQry .= " and patient_data.providerID IN ($phyId_str)";
		}
		if($_REQUEST['heard']!=""){
			if($heard_str == "0")
			$strQry .= " and patient_data.heard_abt_us =''";
			else
			$strQry .= " and patient_data.heard_abt_us IN ($heard_str)";
		}
		if($_REQUEST['operator_id']!=""){
			$strQry .= " and patient_data.created_by IN ($operator_id_str)";
		}
		if($age_start_date!=""){
			$strQry .= " and patient_data.DOB >='$age_start_date_whr'";
		}
		if($age_start_date!=""){
			$strQry .= " and patient_data.DOB <= '$age_end_date_whr'";
		}
		if($postal_code!=""){
			$strQry .= " and patient_data.postal_code = '$postal_code'";
		}
		if($city!=""){
			$strQry .= " and patient_data.city = '$city'";
		}
		if($state!=""){
			$strQry .= " and patient_data.state ='$state'";
		}
		
		if($id_reff_physician!=""){
			$strQry .= " and patient_data.primary_care_id IN ($id_reff_physician)";
		}
		if(sizeof($reminder_choices)>0){
			if($reminder_choices['postal_mail']){
				$strQry .= " and patient_data.hipaa_mail='1'";	
			}
			if($reminder_choices['email']){
				$strQry .= " and patient_data.hipaa_email='1'";	
			}
			if($reminder_choices['voice']){
				$strQry .= " and patient_data.hipaa_voice='1'";	
			}
			if($reminder_choices['text']){
				$strQry .= " and patient_data.hipaa_text='1'";	
			}
		}
		$strQry .= " group by patient_data.id ";
		if($_REQUEST['grpby_block'] == "grpby_physician"){
			$strQry .= " ORDER BY pos.facilityPracCode, patient_data.lname, patient_data.fname";
		}else{
			$strQry .= " ORDER BY users.lname, users.fname, patient_data.lname, patient_data.fname";
		}
		//echo $strQry ;echo "<br><br>";
		
		
	$mainQryRes = get_array_records_query($strQry);
	$pat_for_sch_arr=array();	
	$arrDataRefPhy = array();
	for($h=0;$h<count($mainQryRes);$h++){	
		$mainQryRes[$h]['heard_abt_us'].'<br>';
		$fac_id=$mainQryRes[$h]['default_facility'];
		$ref_phy_id = $mainQryRes[$h]['primary_care_id'];
		$pat_id = $mainQryRes[$h]['id'];
		$pat_for_sch_arr[]=$mainQryRes[$h]['id'];
		$arr_facility_order[$fac_id]=$fac_id;
		if($fac_id==""){
			$fac_id=0;
		}
		$fac_based_data_arr[$fac_id][]=$mainQryRes[$h];
		//--------- NEW PATIENT SEARCH FOR REFERRING PHYSICAN-----------//
		if($_REQUEST['grpby_block'] == "grpby_physician"){
			//$arrDataRefPhy[$ref_phy_id][$pat_id] = $pat_id;
			$arrDataRefPhy[$ref_phy_id][] = $mainQryRes[$h];
		}
		
	}

	if(sizeof($pat_for_sch_arr)>0){
		$pat_for_sch_imp=implode(',',array_unique($pat_for_sch_arr));
		
		//echo "<pre>";
		//print_r($fac_based_data_arr);
		if($report_type=="Address Labels"){
			if(count($pat_for_sch_arr)>0){
				include_once "new_account_recall_letter.php";
			}
		}else if($report_type=="Recall letter"){
			$recallPrintFrom = "newAccountReport";
			if(count($pat_for_sch_arr)>0){
				include_once "new_account_recall_letter.php";
			}
		}else if($report_type== "Emails"){
			$top_pad="15mm";
			$st_no=0;
			for($r=0;$r<count($mainQryRes);$r++){
				if($mainQryRes[$r]['email']){
					$st_no=$st_no+1;	
					if($st_no==1){	
						$page_data ='
							<tr>
								<td style="width:20px; text-align:center;" class="text_b_w">#</td>
								<td style="width:120px; text-align:left;" class="text_b_w">Patient Name</td>
								<td style="width:150px; text-align:left;" class="text_b_w">Email Address</td>
							</tr>';
						$page_title_header='<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8">						
								<tr>
									<td style="width:50px; height:20px; text-align:center;" class="text_b_w">#</td>
									<td style="width:250px; text-align:left;" class="text_b_w">Patient Name</td>
									<td style="width:750px; text-align:left;" class="text_b_w">Email Address</td>
								</tr>
						</table>';
					}
					$pat_name = "";
					$pat_email="";
					$pat_name_arr = array();
					$pat_name_arr['LAST_NAME'] = $mainQryRes[$r]['lname'];
					$pat_name_arr['FIRST_NAME'] = $mainQryRes[$r]['fname'];
					$pat_name_arr['MIDDLE_NAME'] = $mainQryRes[$r]['mname'];		
					$pat_name = changeNameFormat($pat_name_arr);
					$pat_email= $mainQryRes[$r]['email'];
							
					$page_data .='<tr style="background-color:#ffffff;">
						<td style="text-align:left;" class="text_10">'.$st_no.'</td>
						<td style="text-align:left;" class="text_10">'.$pat_name.'</td>
						<td style="text-align:left;" class="text_10">'.$pat_email.'</td>
						
					</tr>';	
					$page_data2 .='<tr style="background-color:#ffffff;">
						<td style="text-align:center;width:50px;" class="text_10">'.$st_no.'</td>
						<td style="text-align:left;width:220px" class="text_10">'.$pat_name.'</td>
						<td style="text-align:left;width:150px;" class="text_10">'.$pat_email.'</td>
					</tr>';	
				}
			}
		}else{
			$sch_query = "SELECT appt.id as 'sch_id', appt.sa_doctor_id as sdocid, appt.sa_patient_id as spid, facility.name,
					  appt.sa_doctor_id,appt.procedureid as procId,sp.acronym as proc_name,
					  DATE_FORMAT(appt.sa_app_start_date, '".get_sql_date_format()."') as apptDate, 
					  TIME_FORMAT(appt.sa_app_starttime, '%h:%i %p') as starttime,
					  u.lname as ulname,u.fname as ufname, u.mname as umname,
					  sp.proc as procedurename
					  FROM schedule_appointments appt 
					  JOIN users u ON u.id = appt.sa_doctor_id
					  LEFT JOIN facility ON facility.id = appt.sa_facility_id
					  LEFT JOIN slot_procedures sp ON sp.id = appt.procedureid
					  where appt.sa_patient_id in ($pat_for_sch_imp)
					  $sch_join_whr
					  group by appt.sa_patient_id
					  order by appt.sa_app_start_date asc, appt.sa_app_starttime asc";
				$sch_run_qry=imw_query($sch_query);
				while($row_sch=imw_fetch_array($sch_run_qry)){
					$spid=$row_sch['spid'];
					$provider_name = "";
					$pro_name_arr=array();
					$pro_name_arr['LAST_NAME'] = $row_sch['ulname'];
					$pro_name_arr['FIRST_NAME'] = $row_sch['ufname'];
					$pro_name_arr['MIDDLE_NAME'] = "";			
					$provider_name = changeNameFormat($pro_name_arr);					
					
					$sch_data_arr[$spid]['appt_date']=$row_sch['apptDate'];
					$sch_data_arr[$spid]['appt_proc']=$row_sch['proc_name'];
					$sch_data_arr[$spid]['physician']=$provider_name;
					$sch_data_arr[$spid]['facility']=$row_sch['name'];
					
					$temp_arr[$row_sch['sch_id']]=$row_sch['sch_id'];
				}
				
				$arr_appt_created_date=array();
				if(sizeof($temp_arr)>0){
					$temp_str=implode(',', $temp_arr);
					$qry="Select patient_id, DATE_FORMAT(dateTime, '".get_sql_date_format()."') as 'appt_created_date' FROM previous_status 
					WHERE sch_id IN(".$temp_str.") AND status='0' ORDER BY id DESC";
					$rs=imw_query($qry);
					while($res=imw_fetch_array($rs)){
						$arr_appt_created_date[$res['patient_id']]= $res['appt_created_date'];
					}
				}
				
				if(count($mainQryRes)>0){
				if($_REQUEST['grpby_block'] == "grpby_facility")
					include_once('new_account_report_result_facility.php');
				else if($_REQUEST['grpby_block'] == "grpby_physician"){
					if($_REQUEST['heard']!="" && $heard_str == "0"){
						include_once('new_account_report_result_refphysician.php');
					}
					else
					include_once('new_account_report_result_refphysician.php');
				}
			}
		}
	}
if($page_data!=""){	
	$pdfData='<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$pdfData .= '
	<page backtop="13mm" backbottom="5mm">
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>		
	<page_header>
		<table class="rpt_table rpt rpt_table-bordered rpt_padding">	
			<tr class="rpt_headers">
				<td class="rptbx1" width="345">New Account Report ('.$processReport.')</td>
				<td class="rptbx2" width="345">Report Period : '.$Start_date.' to '.$End_date.'</td>
				<td class="rptbx3" width="345">Created By '.$createdBy.' on '.date('Y-m-d')." ".date(' h:i A').'</td>
			</tr>
		</table>
		'.$page_title_header.'
	</page_header>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			'.$page_data2.'
		</table>
	</page>';
	$hasData =1;
	$file_location = write_html($pdfData);	
}
}
?>

<table cellpadding="1" cellspacing="1" class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%">
	<tr class="rpt_headers">
		<td class="rptbx1" width="350">New Account Report <?php echo $processReport; ?></td>
		<td class="rptbx2" width="350">Report Period: <?php echo $Start_date.' to '.$End_date ?></td>
		<td class="rptbx3" width="350">Created by: <?php echo $report_generator_name.' on '.date("".$phpDateFormat." h:i A") ?> </td>
	</tr>
</table>
<table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="#FFF3E8" class="rpt rpt_table rpt_table-bordered rpt_padding">
<?php 
	if($page_data){
		 echo $page_data;
	}else {
		echo "<tr><td align=\"center\" >No record exists.</td></tr>";
	}	

?>
</table>