<?php 

if ($_POST['form_submitted']) {
	
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
	
	//--- CHANGE DATE FORMAT ----
	$date_format_SQL = get_sql_date_format();
	$startDate = getDateFormatDB($Start_date);
	$endDate = getDateFormatDB($End_date);
	
	$phpDateFormat 		= phpDateFormat();
	$curDate 			= date($phpDateFormat.' h:i A');
	$op_name_arr 		= preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$createdBy 			= ucfirst(trim($op_name_arr[1][0]));
	$createdBy 		   .= ucfirst(trim($op_name_arr[0][0]));
}


	//SELECT * FROM `tm_rules_list` 
	
	
	$qryRL = imw_query("SELECT tm_rules_list.id, tm_rules_list.rule_id, tm_rules.tm_rule_name FROM tm_rules_list JOIN tm_rules ON (tm_rules.id = tm_rules_list.rule_id) WHERE tm_rules_list.cat_id=3 ");
	
	$rlArray = array();
	while($res=imw_fetch_assoc($qryRL)){
		$id = $res['id'];
		$rule_id = $res['tm_rule_name'];
		$rlArray[$id] = $rule_id;
	}
		
	//SELECT * FROM `tm_rules` ORDER BY `tm_rules`.`tm_rule_name` ASC 
	
	$qry="SELECT * FROM tm_assigned_rules WHERE status = 0 AND (DATE_FORMAT(added_on, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate') AND section_name='ar_aging' order by added_on desc";
	$mainQryRes = array();
	$printFile = false;
	$query = imw_query($qry);
	if(imw_num_rows($query)>0) $printFile = true;
	while($res=imw_fetch_assoc($query)){
		$mainQryRes[] = $res;
	}
	
	$pageDate ='<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" bgcolor="#FFF3E8">
				<tr class="rpt_headers">
					<td class="rptbx1" style="width:350px;">A/R Aging Rules</td>
					<td class="rptbx2" style="width:350px;">Date From '.$Start_date.' To '.$End_date.'</td>
					<td class="rptbx3" style="width:350px;">Created by: '.$createdBy.' on '.$curDate.'</td>
				</tr>
		</table>';
	$pageDate .= '<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050"><tr>
				<td class="text_b_w" style="text-align:center;width:131px;">Patient Id</td>
				<td class="text_b_w" style="text-align:center;width:131px;">Patient Name</td>
				<td class="text_b_w" style="text-align:center;width:131px;">Encounter ID</td>
				<td class="text_b_w" style="text-align:center;width:131px;">DOS</td>
				<td class="text_b_w" style="text-align:center;width:131px;">Due Amount</td>
				<td class="text_b_w" style="text-align:center;width:131px;">Aged Day</td>
				<td class="text_b_w" style="text-align:center;width:131px;">A/R From</td>
				<td class="text_b_w" style="text-align:center;width:131px;">Created Date</td>
			</tr>';
	for($i=0;$i<count($mainQryRes);$i++){
		$patinet_id = $mainQryRes[$i]['patientid'];
		$patient_name = $mainQryRes[$i]['patient_name'];
		$encounter_id = $mainQryRes[$i]['encounter_id'];
		$date_of_service = date('m-d-Y',strtotime($mainQryRes[$i]['date_of_service']));
		$amount_due = $mainQryRes[$i]['amount_due'];
		$days_aged = $mainQryRes[$i]['days_aged'];
		$rule_list_id = $rlArray[$mainQryRes[$i]['rule_list_id']];
		$added_on = date('m-d-Y', strtotime($mainQryRes[$i]['added_on']));
		
	$pageDate .='<tr>
				<td class="text_10" style="text-align:center;width:131px;">'.$patinet_id.'</td>
				<td class="text_10" style="text-align:left;width:131px;">'.$patient_name.'</td>
				<td class="text_10" style="text-align:center;width:131px;">'.$encounter_id.'</td>
				<td class="text_10" style="text-align:center;width:131px;">'.$date_of_service.'</td>
				<td class="text_10" style="text-align:center;width:131px;">'.$amount_due.'</td>
				<td class="text_10" style="text-align:center;width:131px;">'.$days_aged.'</td>
				<td class="text_10" style="text-align:center;width:131px;">'.$rule_list_id.'</td>
				<td class="text_10" style="text-align:center;width:131px;">'.$added_on.'</td>
			</tr>';
	$pdfDate .='<tr>
				<td class="text_10" style="text-align:center;width:131px;">'.$patinet_id.'</td>
				<td class="text_10" style="text-align:left;width:131px;">'.$patient_name.'</td>
				<td class="text_10" style="text-align:center;width:131px;">'.$encounter_id.'</td>
				<td class="text_10" style="text-align:center;width:131px;">'.$date_of_service.'</td>
				<td class="text_10" style="text-align:center;width:131px;">'.$amount_due.'</td>
				<td class="text_10" style="text-align:center;width:131px;">'.$days_aged.'</td>
				<td class="text_10" style="text-align:center;width:131px;">'.$rule_list_id.'</td>
				<td class="text_10" style="text-align:center;width:131px;">'.$added_on.'</td>
			</tr>';	
		
			
	}
	$pageDate .='</table>';

$page_header_val = '
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050" bgcolor="#FFF3E8">
				<tr class="rpt_headers">
					<td class="rptbx1" style="width:350px;">A/R Aging Rules</td>
					<td class="rptbx2" style="width:350px;">Date From '.$Start_date.' To '.$End_date.'</td>
					<td class="rptbx3" style="width:350px;">Created by: '.$createdBy.' on '.$curDate.'</td>
				</tr>
		</table>';	
	
$pdfData = '
	<page backtop="10mm" backbottom="5mm">
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>		
	<page_header>
	'.$page_header_val.'
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050" bgcolor="#FFF3E8">						
			<tr>
				<td class="text_b_w" style="text-align:center;width:131px;">Patient Id</td>
				<td class="text_b_w" style="text-align:center;width:131px;">Patient Name</td>
				<td class="text_b_w" style="text-align:center;width:131px;">Encounter ID</td>
				<td class="text_b_w" style="text-align:center;width:131px;">DOS</td>
				<td class="text_b_w" style="text-align:center;width:131px;">Due Amount</td>
				<td class="text_b_w" style="text-align:center;width:131px;">Aged Day</td>
				<td class="text_b_w" style="text-align:center;width:131px;">A/R From</td>
				<td class="text_b_w" style="text-align:center;width:131px;">Created Date</td>
			</tr>
		</table>
	</page_header>
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" bgcolor="#FFF3E8">
		'.$pdfDate.'
	</table>
	</page>';
	
if($printFile == true){
	$pdf_css= '<style>'.file_get_contents("css/reports_pdf.css").'</style>';
	$strHTML = <<<DATA
		$pdf_css
		$pdfData
DATA;
	$file_location = write_html($strHTML);
	}
	
	if($printFile == true){
		echo $pageDate;
	} else {
		 echo '<div class="text-center alert alert-info">No record exists.</div>';
	}

?>