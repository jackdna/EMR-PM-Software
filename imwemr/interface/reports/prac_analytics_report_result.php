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
set_time_limit(180);
$FCName= $_SESSION['authId'];
//$dispChart = (int)$_REQUEST['disp_chart'];
$dateFormat= get_sql_date_format();
$phpDateFormat = phpDateFormat();

$strHTML = $pdfFooter = $strHeader = '';
$strHTML_Label_St = $strHTML_Label_Footer = $strHTML_Label = '';
//--- GET Groups DETAIL ----
$group_query = "Select  gro_id,name from groups_new order by name";
$group_query_res = imw_query($group_query);
while ($group_res = imw_fetch_array($group_query_res)) {
    $group_arr[$group_res['gro_id']]=$group_res['name'];
}

//GET USERS DETAIL
$q = "Select  id, fname,mname,lname from users";
$res = imw_query($q);
while ($user_res = imw_fetch_array($res)) {
    $user_arr[$user_res['id']]=core_name_format($user_res['lname'], $user_res['fname'], $user_res['mname']);
}

//GET REPORT TEMPLATE NAME
if($_REQUEST['sch_temp_id']){
	$sql_query = imw_query("SELECT template_name FROM `custom_reports` WHERE id='$_REQUEST[sch_temp_id]' and `delete_status` = 0");
	if(imw_num_rows($sql_query) > 0){
		$row = imw_fetch_assoc($sql_query);
		$page_title  = $row['template_name'];
	}
}

//GET Group ids
$strGroupIds = '';
if(empty($_REQUEST['groups']) == false) {
	$strGroupIds = implode(",",$_REQUEST['groups']);
}
// GET Physicians Ids
$physicianIds = '';
if(empty($_REQUEST['phyId']) == false) {
	$physicianIds = implode(",",$_REQUEST['phyId']);
}
// GET Operator Ids
$operatorIds = '';
if(empty($_REQUEST['operator_id']) == false) {
	$operatorIds = implode(",",$_REQUEST['operator_id']);
}

//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
$arrDateRange= $CLSCommonFunction->changeDateSelection();
if($dayReport=='Daily'){
	$_REQUEST['Start_date'] = $_REQUEST['End_date']= date($phpDateFormat);
}else if($dayReport=='Weekly'){
	$_REQUEST['Start_date'] = $arrDateRange['WEEK_DATE'];
	$_REQUEST['End_date'] = date($phpDateFormat);
}else if($dayReport=='Monthly'){
	$_REQUEST['Start_date'] = $arrDateRange['MONTH_DATE'];
	$_REQUEST['End_date'] = date($phpDateFormat);
}else if($dayReport=='Quarterly'){
	$_REQUEST['Start_date'] = $arrDateRange['QUARTER_DATE_START'];
	$_REQUEST['End_date'] = $arrDateRange['QUARTER_DATE_END'];
}
	
// GET Status Ids
$heardAbtIds = '';
if(empty($_REQUEST['heardAbtUs']) == false) {
	$heardAbtIds = $_REQUEST['heardAbtUs'];
}

$heardResArr = array();
$heardIdArr = array();
$query = "select heard_id,heard_options from heard_about_us order by heard_options";
$rsQuery = imw_query($query);
while($rowQuery = imw_fetch_array($rsQuery)){
	$heard_id = 0;
	$heard_options = "";
	$heard_id = $rowQuery['heard_id'];
	$heard_options = $rowQuery['heard_options'];
	$heardResArr[$heard_id] = $heard_options;
	$heardIdArr[] =	$heard_id;
	$arrHeardAbtUsAll[$heard_id] = $heard_id;
}
$allHeardCount=sizeof($heardResArr);

$strSelType = "";
$arrHeardAbtUs = $_REQUEST['heardAbtUs'];

$_REQUEST['heardAbtUsValue']=ucfirst(strtolower($_REQUEST['heardAbtUsValue']));

if(sizeof($arrHeardAbtUs)<=0){
	if(empty($_REQUEST['heardAbtUsValue'])==false){ //IN CASE VALUE SELECTED BUT HEARD TYPE NOT SELECTED THEN O IS COMPULSARY TO GET THOSE RECORDS.
		//ADDING IN MASTER ARRAY FOR FURTHER RESULT LOOP  
		$heardIdArr[$_REQUEST['heardAbtUsValue']]=$_REQUEST['heardAbtUsValue']; 
		$heardResArr[$_REQUEST['heardAbtUsValue']]=$_REQUEST['heardAbtUsValue'];
	}
	//$arrHeardAbtUs = $arrHeardAbtUsAll;
}
$strSelType =  implode(",", $arrHeardAbtUs);

if(empty($strSelType) == true){
	$strSelType = $_REQUEST['heardAbtAll'];
	$arrHeardAbtUs = explode(",", $strSelType);
}

$facilityIds = implode(",", $_REQUEST['facility_name']);


$strShowSelType = $CLSReports->report_display_selected(implode(',',$_REQUEST['heardAbtUs']),'heard_abt_us',1, $allHeardCount);
if($strShowSelType=='All' && empty($_REQUEST['heardAbtUsValue'])==false){
	$strShowSelType='Other ('.$_REQUEST['heardAbtUsValue'].')';
}else if($strShowSelType!='All' && empty($_REQUEST['heardAbtUsValue'])==false){
	$strShowSelType.=' ('.$_REQUEST['heardAbtUsValue'].')';
}

$strLayout = $_REQUEST['summary_detail'];

//changing date format
if((isset($_REQUEST['Start_date'])) && (empty($_REQUEST['Start_date']) == false)){
	$dtEffectiveDate1 = $_REQUEST['Start_date'];
	$start_date = getDateFormatDB($dtEffectiveDate1);
}

//changing date format
if((isset($_REQUEST['End_date'])) && (empty($_REQUEST['End_date']) == false)){
	$dtEffectiveDate2 = $_REQUEST['End_date'];
	$end_date = getDateFormatDB($dtEffectiveDate2);
}

$_REQUEST['daterangefor']='appointment';
//getting operator name is session

$ArrC= getUserDetails($_SESSION['authId']);
$createdBy = strtoupper(substr($ArrC['fname'],0,1).substr($ArrC['lname'],0,1));
$createdOn = get_date_format(date('Y-m-d'));
$createdOn.= date(' H:i A');

		
$strCSSHtml= '<style>'.file_get_contents('css/reports_html.css').'</style>';
$strCSSPdf= '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
//get list of appointments on behalf of appt date range
$patientIDArr = $patApptDetArr = $grandCountArr = $patientIDArr_lab = array();

//GET PATIENT IDS IF SEARCHED FOR PAT CREATED DATE
$arrTempPatIds=array();
$strTempPatIds='';

/* *********************************************************************** */
/* FILTER CODE STARTS HERE
/* *********************************************************************** */

//FILTER::GETTING APPOINTMENT OF PATIENT BETWEEN SELECTED DATE RANGE
if($_REQUEST['daterangefor']=='appointment'){
	$appt_qry="SELECT sa.id, sa.sa_patient_id FROM schedule_appointments sa";
	if(empty($_REQUEST['groups']) == false)
	{
		$appt_qry.=" LEFT JOIN facility fac ON sa.sa_facility_id=fac.id";
	}
		
	$appt_qry .=" WHERE (sa.sa_app_start_date BETWEEN '".$start_date."' AND '".$end_date."')";
	
	//FILTER::add groups filter
	if(empty($_REQUEST['groups']) == false)
	{
		$appt_qry .= " AND fac.default_group IN(".$strGroupIds.")";
	}
	//FILTER::add operator filter
	if(empty($operatorIds) == false){
		$appt_qry .= " AND sa.status_update_operator_id IN(".$operatorIds.")";
	}
	//FILTER::add facility filter
	if(empty($facilityIds) == false){
		$appt_qry .=" AND sa.sa_facility_id IN(".$facilityIds.")";
	}
	//FILTER::add physician filter
	if(empty($physicianIds) == false){
		$appt_qry .=" AND sa.sa_doctor_id IN(".$physicianIds.")";
	}
	$appt_qry .=" ORDER BY sa.sa_app_start_date ASC";
	
	$appt_rs=imw_query($appt_qry);
	while($appt_res=imw_fetch_array($appt_rs)){
		if(!$arrTempPatIds[$appt_res['sa_patient_id']]){
			$arrTempPatIds[$appt_res['sa_patient_id']]=$appt_res['sa_patient_id'];
			$arrApptIds[$appt_res['sa_patient_id']]=$appt_res['id'];
		}
	}
	//free up memory
	unset($rs,$appt_qry, $appt_rs, $appt_res);
}

//FILTER::CHECKING HEARD ABOUT US 
if(trim($strSelType))
{
	$qryHAU = "Select id  
	  FROM patient_data  
	  WHERE heard_abt_us IN (".$strSelType.") ";
	
	if(sizeof($arrTempPatIds)>0){
		$strTempPatIds=implode(',', $arrTempPatIds);
		$qryHAU .= " AND id IN(".$strTempPatIds.")";
	}
	
	if(empty($_REQUEST['heardAbtUsValue'])==false){
		$qryHAU .= " AND heard_abt_desc='".trim($_REQUEST['heardAbtUsValue'])."'";
	}
	
	$hau_rs=imw_query($qryHAU);
	while($hau_res=imw_fetch_array($hau_rs))
	{
		$new_temp_pt_arr[$hau_res['id']]=$hau_res['id'];
	}
	//over write pt ids arr
	$arrTempPatIds=$new_temp_pt_arr;
	//free up memory
	unset($qryHAU, $hau_rs, $hau_res, $new_temp_pt_arr);
}

//FILTER::CHECK FOR NEW PATIENT
if($_REQUEST['new_patient'])
{
	//create temporary array for pt list
	$new_temp_pt_arr=$arrTempPatIds;
	
	$appt_qry="Select sa_patient_id FROM schedule_appointments WHERE sa_app_start_date < '".$start_date."' and sa_patient_id<>''";
	if(sizeof($arrTempPatIds)>0){
		$strTempPatIds=implode(',', $arrTempPatIds);
		$appt_qry .= " AND sa_patient_id IN(".$strTempPatIds.")";
	}
	$appt_rs=imw_query($appt_qry);
	while($appt_res=imw_fetch_array($appt_rs))
	{
		//remove patients that have prior appt then start date
		unset($new_temp_pt_arr[$appt_res['sa_patient_id']]);
	}
	//over write pt ids arr
	$arrTempPatIds=$new_temp_pt_arr;
	//free up memory
	unset($appt_qry, $appt_rs, $appt_res, $new_temp_pt_arr, $strTempPatIds);
}

//FILTER::CHECK FOR LOST PATIENT
if($_REQUEST['lost_patient'])
{
	//create temporary array for pt list
	$new_temp_pt_arr=$arrTempPatIds;
	$strTempPatIds=implode(',', $arrTempPatIds);
	$pat_qry = "Select sa_patient_id FROM schedule_appointments WHERE sa_patient_id IN(".$strTempPatIds.") AND sa_app_start_date > '".$end_date."'";
	$pat_rs = imw_query($pat_qry);
	while($pat_res = mysql_fetch_array($pat_rs)){
		//remove those ids which have future appointment
		unset($new_temp_pt_arr[$pat_res['sa_patient_id']]);
	}
	//over write pt ids arr
	$arrTempPatIds=$new_temp_pt_arr;
	//free up memory
	unset($pat_qry, $pat_rs, $pat_res, $new_temp_pt_arr, $strTempPatIds);
}

//FILETER: CHECK FOR RECAL FULFILL MENT
if($_REQUEST['recal_month'] && $_REQUEST['recall_fulfilment'])
{
	list($month,$year)=explode('-',$_REQUEST['recal_month']);
	//create temporary array for pt list
	$new_temp_pt_arr=$arrTempPatIds;
	$strTempPatIds=implode(',', $arrTempPatIds);
	$recall_qry="select patient_id from patient_app_recall where patient_id IN($strTempPatIds) 
	AND descriptions != 'MUR_PATCH'";
	if($month){
		$recall_qry.=" AND date_format(recalldate,'%m') = $month";
	}
	if($year){
		$recall_qry.=" AND date_format(recalldate,'%Y') = $year";
	}
	$recall_qry.= " AND hipaa_voice=1";
	
	$recal_rs=imw_query($recall_qry);
	while($recal_res=imw_fetch_array($recal_rs))
	{
		//we are holding only those patients that have fulfilled recal condition
		$new_temp_pt_arr[$recal_res['patient_id']]=$recal_res['patient_id'];
	}	
	//over write pt ids arr
	$arrTempPatIds=$new_temp_pt_arr;
	//free up memory
	unset($recall_qry, $recal_rs, $recal_res, $new_temp_pt_arr, $strTempPatIds);
}

/* *********************************************************************** */
/* INCLUDE CODE STARTS HERE
/* *********************************************************************** */

$strTempPatIds=implode(',',$arrTempPatIds);
//INCLUDE:: get recal related data if selected
if($_REQUEST['inc_recalls'] && sizeof($arrTempPatIds)>0)
{
	$recall_qry="select rec.recalldate, rec.patient_id, sp.proc
	FROM patient_app_recall rec
	LEFT JOIN slot_procedures sp ON sp.id = rec.procedure_id 
	WHERE rec.recalldate >= '$start_date' and rec.patient_id IN($strTempPatIds) AND rec.descriptions != 'MUR_PATCH'
	ORDER BY rec.recalldate DESC";
	$recal_rs=imw_query($recall_qry);
	while($recal_res=imw_fetch_array($recal_rs))
	{
		$recallStrToTime=strtotime($recal_res['recalldate']);
		$recallArr[$recal_res['patient_id']]['date']=date($phpDateFormat, $recallStrToTime);
		$recallArr[$recal_res['patient_id']]['proc']=$recal_res['proc'];
	}
}
//free up memory
unset($recall_qry, $recal_rs, $recal_res);

//reset appt ids array as per pt id
foreach($arrTempPatIds as $id)
{
	$tempApptIdsArr[$id]=$arrApptIds[$id];
}
//swap values 
$arrApptIds=$tempApptIdsArr;
unset($tempApptIdsArr);

//if($strLayout == "summary"){		
		
	$intTotAppts = $j = 0;
	$qryAppt = "Select sa.id, sa.sa_patient_app_status_id, sa.procedureid, sa.status_update_operator_id, 
	sp.proc, sa.sa_app_start_date, TIME_FORMAT(sa.sa_app_starttime, '%h:%i %p') as starttime, ss.status_name, pd.primary_care,
	pd.id as 'patient_id', pd.heard_abt_us, pd.heard_abt_desc, sa.sa_doctor_id, pd.lname as plname, pd.fname as pfname, pd.mname as pmname";
	
	if($grpby_block=='grpby_groups' || $grpby_block=='grpby_facility' || $grpby_block=='grpby_facility')
	{$qryAppt.=" ,fac.name as fac_name, fac.id as fac_id ,fac.default_group";}
	
	
	$qryAppt.=" FROM patient_data pd 
	  LEFT JOIN schedule_appointments sa ON sa.sa_patient_id = pd.id  
	  LEFT JOIN slot_procedures sp ON sp.id = sa.procedureid 
	  LEFT JOIN schedule_status ss ON ss.id = sa.sa_patient_app_status_id ";
	
	//GROUP BY:: GROUP OR FACILITY
	if($grpby_block=='grpby_groups' || $grpby_block=='grpby_facility' || $grpby_block=='grpby_facility')
	{
		$qryAppt.=" LEFT JOIN facility fac ON sa.sa_facility_id=fac.id";
	}
	$qryAppt .=" WHERE 1=1";
	
	//FILTER::add VIP Filter
	if($vip==1){
		$qryAppt.=" AND pd.vip=1";
	}

	if(trim($strSelType))
	$qryAppt .=" AND pd.heard_abt_us IN (".$strSelType.") ";
	
	if(sizeof($arrApptIds)>0){
		$strApptIds=implode(',', $arrApptIds);
		$qryAppt .= " AND sa.id IN(".$strApptIds.")";
	}	
	$qryAppt .= " ORDER BY pd.providerID,sa.status_update_operator_id, pd.default_facility,  pd.heard_abt_us, pd.heard_about_us_date DESC";
	
	$rsQryAppt = imw_query($qryAppt);
	$intTotAppts = imw_num_rows($rsQryAppt);
	if($intTotAppts > 0){
		while($data=imw_fetch_object($rsQryAppt))
		{
			if($grpby_block=='grpby_groups')
			{
				$firstGrpBy=$data->default_group;
				$firstTitle='Business Unit';
				$firstGrpByName = $group_arr[$firstGrpBy];
			}
			elseif($grpby_block=='grpby_facility')
			{
				$firstGrpBy=$data->fac_id;
				$firstTitle='Facility';
				$firstGrpByName = $data->fac_name;
			}
			elseif($grpby_block=='grpby_physician')
			{
				$firstGrpBy=$data->sa_doctor_id;
				$firstTitle='Physician';
				$firstGrpByName = $user_arr[$firstGrpBy];
			}
			elseif($grpby_block=='grpby_operators')
			{
				$firstGrpBy=$data->status_update_operator_id;
				$firstTitle='Operators';
				$firstGrpByName = $user_arr[$firstGrpBy];
			}
			$group_by_detail[$firstGrpBy]=$firstGrpByName;
			
			$page_data_arr['APPT_ID']=$data->id;
			$page_data_arr['APPT_STATUS']=$data->sa_patient_app_status_id;
			$page_data_arr['APPT_PROC']=$data->proc;
			$page_data_arr['APPT_OPERATOR']=$user_arr[$data->status_update_operator_id];
			
			if($data->sa_app_start_date){
				$apptTime=strtotime($data->sa_app_start_date);
				$page_data_arr['APPT_DATE'] = date($phpDateFormat, $apptTime);
			}
			
			$page_data_arr['APPT_TIME']=$data->starttime;
			$page_data_arr['APPT_PT_ID']=$data->patient_id;
			$page_data_arr['APPT_PT_NAME']=core_name_format($data->plname, $data->pfname, $data->pmname);
			$page_data_arr['APPT_HEARD']=$data->heard_abt_us;
			$page_data_arr['APPT_PHY_NAME']= $user_arr[$data->sa_doctor_id];
			$page_data_arr['APPT_CARE_PHY_NAME']= $data->primary_care;
			$page_data_arr['APPT_FAC_NAME']=$data->fac_name;
			
			$page_data_arr['APPT_RECAL_DATE']=$recallArr[$data->patient_id]['date'];
			$page_data_arr['APPT_RECAL_PROC']=$recallArr[$data->patient_id]['proc'];
			
			$dataArr[$firstGrpBy][] = $page_data_arr;
		}
	}
//pre($dataArr);
	if(sizeof($dataArr)>0){
		$pdfHTML = '<page backtop="11mm" backbottom="10mm">   <page_footer>
					<table width="100%">
						<tr>
							<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>';
		$pdfHTML .= '<page_header>
						<table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="#FFF3E8" class="rpt_padding">
							<tr class="rpt_headers">
								<td width="530" class="rpt_headers rptbx1">&nbsp;<b>'.$page_title.'</b></td>
								<td width="530" class="rpt_headers rptbx2">Created by '.$createdBy.' on '.$createdOn.'</td>
							</tr>	
							<tr class="rpt_headers">
								<td class="rpt_headers rptbx1"><!-- Heard Type: '.str_replace("'","",$strShowSelType).' --></td>
								<td class="rpt_headers rptbx2">Report Period: '.$start_date.' to '.$end_date.'</td>
							</tr>
						</table>
					</page_header>';
		$strHeader = '<table class="rpt rpt_table rpt_table-bordered" width="100%">
						<tr class="rpt_headers">
							<td width="50%" class="rptbx1"><b>'.$page_title.'</b></td>
							<td width="50%" nowrap="nowrap" class="rptbx2 text-right" >Created by '.$createdBy.' on '.$createdOn.'</td>
						</tr>
						<tr class="rpt_headers">
							<td class="rptbx1"><!-- Heard Type: '.str_replace("'","",$strShowSelType).' --></td>
							<td class="rptbx2 text-right">Report Period: '.$start_date.' to '.$end_date.'</td>
						</tr>			
					</table>';
		//count column span
		
		$columns=7;
		if(!$inc_appt_detail)$columns=$columns-3;
		if(!$inc_ref_physician)$columns=$columns-1;
		if(!$inc_recalls)$columns=$columns-2;
		
		
		$column_width = 1060;
		$no_of_column = 1;
		if($inc_appt_detail==1){
			$no_of_column = $no_of_column + 3;
		}	
		
		if($inc_ref_physician==1){
			$no_of_column = $no_of_column + 1;
		}
		
		if($inc_recalls==1){
			$no_of_column = $no_of_column + 2;
		}	
		$widthTD =  $column_width / $no_of_column;
		$widthTD = $widthTD.'px';	
		
		$pdfHTML .= '<table width="1080" cellpadding="1" cellspacing="1" border="0"  bgcolor="#FFF3E8">
					<tr><td class="text_b_w" width="1080">Summary Detail</td></tr></table>';
		$pdfHTML .= '<table><tr><td style="width:'.$widthTD.';" class="text_b_w">Pt. Name-ID</td>';
		
		if($inc_appt_detail==1){
			$pdfHTML .= '<td style="width:'.$widthTD.';" class="text_b_w">Appt Date</td>';
			$pdfHTML .= '<td style="width:'.$widthTD.';" class="text_b_w">Appt Time</td>';
			$pdfHTML .= '<td style="width:'.$widthTD.';" class="text_b_w">Appt Procedure</td>';
		}
		if($inc_ref_physician==1){
			$pdfHTML .= '<td style="width:'.$widthTD.';" class="text_b_w">Ref. Physician</td>';
		}
		if($inc_recalls==1){
			$pdfHTML .= '<td style="width:'.$widthTD.';" class="text_b_w">Recall Date</td>';
			$pdfHTML .= '<td style="width:'.$widthTD.';" class="text_b_w">Recall Proc.</td>';
		}
		$pdfHTML .= '</tr>';
		
		$strHTML = '<table class="rpt rpt_table rpt_table-bordered " >
					<tr> <td colspan="'.$columns.'" class="text_b_w ">Summary Detail</td></tr>';
		$strHTML .= '<tr><td style="width:310px;" class="text_b_w">Pat Name-ID</td>';
		if($inc_appt_detail==1){
			$strHTML .='<td style="width:100px;" class="text_b_w text-center">Appt Date</td>';
			$strHTML .='<td style="width:100px;" class="text_b_w text-center">Appt Time</td>';
			$strHTML .='<td style="width:340px;" class="text_b_w text-center">Appt Procedure</td>';
		}
		if($inc_ref_physician==1){
			$strHTML .='<td style="width:180px;" class="text_b_w text-center">Ref. Physician</td>';
			}
		if($inc_recalls==1){
			$strHTML .='<td style="width:100px;" class="text_b_w text-center">Recall Date</td>';
			$strHTML .='<td style="width:260px;" class="text_b_w text-center">Recall Proc.</td>';
		}
		$strHTML .='</tr>';				
		
		$totCount = $totIns = $totPat = $totAdu = $totAmt = 0;			
		foreach($dataArr as $group_id => $subArr) {	
			$pdfHTML.='<tr><td colspan="'.$columns.'">'.ucfirst($group_by_detail[$group_id]).'</td></tr>';
			$strHTML.='<tr><td colspan="'.$columns.'" class="text_b_w">'.ucfirst($group_by_detail[$group_id]).'</td></tr>';
			foreach($subArr as $dataArr){
			$strHTML.='<tr>';
			$pdfHTML.='<tr>';
			$pdfHTML.='<td style="width:'.$widthTD.';">'.$dataArr['APPT_PT_NAME'].'-'.$dataArr['APPT_PT_ID'].'</td>';
			if($inc_appt_detail==1){
				$pdfHTML.='<td style="width:'.$widthTD.';" class="text-center" >'.$dataArr['APPT_DATE'].'</td>';
				$pdfHTML.='<td style="width:'.$widthTD.';" class="text-center" >'.$dataArr['APPT_TIME'].'</td>';
				$pdfHTML.='<td style="width:'.$widthTD.';">'.$dataArr['APPT_PROC'].'</td>';
			}
			if($inc_ref_physician==1){
				$pdfHTML.='<td style="width:'.$widthTD.';">'.$dataArr['APPT_CARE_PHY_NAME'].'</td>';
			}
			if($inc_recalls==1){
				$pdfHTML.='<td style="width:'.$widthTD.';" class="text-center" >'.$dataArr['APPT_RECAL_DATE'].'</td>';
				$pdfHTML.='<td style="width:'.$widthTD.';">'.$dataArr['APPT_RECAL_PROC'].'</td>';
			}
			$pdfHTML.='</tr>';
			$strHTML.='<tr>';
			$strHTML.='<td>'.$dataArr['APPT_PT_NAME'].'-'.$dataArr['APPT_PT_ID'].'</td>';
			if($inc_appt_detail==1){
				$strHTML.='<td class="text-center" >'.$dataArr['APPT_DATE'].'</td>';
				$strHTML.='<td class="text-center" >'.$dataArr['APPT_TIME'].'</td>';
				$strHTML.='<td>'.$dataArr['APPT_PROC'].'</td>';
			}
			if($inc_ref_physician==1){
				$strHTML.='<td>'.$dataArr['APPT_CARE_PHY_NAME'].'</td>';
			}
			if($inc_recalls==1){
				$strHTML.='<td class="text-center" >'.$dataArr['APPT_RECAL_DATE'].'</td>';
				$strHTML.='<td>'.$dataArr['APPT_RECAL_PROC'].'</td>';
			}
			$strHTML.='</tr>';
			}
		}
		$pdfHTML.='</table></page>';
		$strHTML.='</table>';
	}
//}
	

if(sizeof($dataArr) > 0){
	$showBtn = true;
	$csvData = $strCSSHtml.$strHeader.$strHTML;
	$page_data = $strCSSHtml.$strHeader.$strHTML."<br>".$strHTML_Label_st.$strHTML_Label;
	$strHTML1  = $strCSSPdf.$pdfHeader.$pdfFooter.$pdfHTML.$chartImg.$strHTML_Label_St.$strHTML_Label_Footer.$strHTML_Label;
}
else{
	$page_data = ''; $strHTML1 ='';
}

$file_location = write_html($strHTML1);	
/* $html_file_name = 'prac_analytic_custom';
file_put_contents('new_html2pdf/'.$html_file_name.'.html',$strHTML1); */

$hasData=0;
if( $page_data ){
	$hasData=1;	
	echo $page_data;
}else{
	echo '<div class="text-center alert alert-danger">No Records Exists.</div>';
}
?>
<div id="fcexpDiv" align="center"></div>