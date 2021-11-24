<?php 
$getSqlDateFormat = get_sql_date_format();
$phpDateFormat = phpDateFormat();
$curDate = date($phpDateFormat.' h:i A');	
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
	
	//--- CHANGE DATE FORMAT -----------
	$StartDate = getDateFormatDB($Start_date);
	$EndDate = getDateFormatDB($End_date);
	
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$createdBy = ucfirst(trim($op_name_arr[1][0]));
	$createdBy .= ucfirst(trim($op_name_arr[0][0]));

	//--- GET ALL PROVIDER NAME ----
	$providerRs = imw_query("Select id,fname,mname,lname from users");
	$providerNameArr = array();
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
	}	

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facId)<=0 && isPosFacGroupEnabled()){
		$facId = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facId)<=0){
			$facId[0]='NULL';
		}
	}

	$oprId = join(',',$oprId);
	$facId = join(',',$facId);
	$phyId = join(',',$phyId);
	$credit_physician= (sizeof($crediting_provider)>0) ? implode(',',$crediting_provider) : '';

	$oprSelected = $CLSReports->report_display_selected($oprId,'operator',1, $opr_cnt);
	$facilitySelected = $CLSReports->report_display_selected($facId,'practice',1, $posfac_cnt);
	$doctorSelected = $CLSReports->report_display_selected($phyId,'physician',1, $allPhyCount);
	$selCrPhy = $CLSReports->report_display_selected($credit_physician,'physician',1, $allCrPhyCount);	
	
	$phyWhr='';
	if($oprId){ $oprWhr	= " AND pclm.modifier_by IN ($oprId)"; }
	if($facId){	$facWhr = " AND pcl.facility_id IN ($facId)"; }
	if($phyId){ $phyWhr	= " AND pcl.primary_provider_id_for_reports IN ($phyId)"; }
	if(empty($credit_physician) === false){
		$phyWhr.= " and pcl.secondaryProviderId IN ($credit_physician)";
	}
	if($chksamebillingcredittingproviders==1){
		$phyWhr.= " and pcl.primary_provider_id_for_reports!=pcl.secondaryProviderId";							
	}	
	$dateWhr = " AND (date_format(pclm.modifier_on, '%Y-%m-%d') BETWEEN '".$StartDate."' AND '".$EndDate."') ";
	
	$query = "SELECT DATE_FORMAT(pcl.date_of_service, '".get_sql_date_format()."') as date_of_service, CONCAT(p.lname, ', ', p.fname, '-' , 
	pcl.patient_id) as ptName, CONCAT(u.lname, ', ', u.fname) as oprName, CONCAT(u1.lname, ', ', u1.fname) as phyName, pclm.modifier_by, 
	DATE_FORMAT(pclm.modifier_on, '".get_sql_date_format()."') as modifier_on , pcl.encounter_id, pcl.patient_id, pcl.facility_id, 
	CONCAT(pft.facilityPracCode, '-' , pos_tbl.pos_prac_code) as facName, pcl.primary_provider_id_for_reports,
	pcl.secondaryProviderId 	
	FROM patient_charge_list as pcl 
	JOIN patient_charge_list_modifiy as pclm on pcl.encounter_id=pclm.enc_id 
	JOIN (SELECT MAX(tpclm.id) AS pclm_id, tpclm.enc_id 
	FROM patient_charge_list_modifiy as tpclm group by tpclm.enc_id)F ON F.enc_id = pclm.enc_id AND F.pclm_id = pclm.id 
	JOIN patient_data p ON p.id = pcl.patient_id JOIN users u ON u.id = pclm.modifier_by 
	JOIN users u1 ON u1.id = pcl.primary_provider_id_for_reports 
	JOIN pos_facilityies_tbl pft on pft.pos_facility_id = pcl.facility_id 
	JOIN pos_tbl on pos_tbl.pos_id = pft.pos_id 
	Where 1=1 $dateWhr $oprWhr $facWhr $phyWhr ORDER BY p.lname";
	$qryRs = imw_query($query);
	$num_row = imw_num_rows($qryRs);
	while($qryRes = imw_fetch_array($qryRs)){
		$ptName = $qryRes['ptName'];
		$encounter_id = $qryRes['encounter_id'];
		$dos = $qryRes['date_of_service'];
		$modifier_on = $qryRes['modifier_on'];
		$doctor_id=$qryRes['primary_provider_id_for_reports'];
		$oprName = $qryRes['oprName'];
		$phyName = $qryRes['phyName'];
		$facName = $qryRes['facName'];

		if(empty($phyId)==true && empty($credit_physician) === false){
			$doctor_id=$qryRes['secondaryProviderId'];
		}
	
		$data .= '
		<tr bgcolor="#FFFFFF">
			<td class="text_10" valign="top">'.$ptName.'</td>
			<td class="text_10" valign="top">'.$encounter_id.'</td>
			<td class="text_10" valign="top">'.$dos.'</td>
			<td class="text_10" valign="top">'.$providerNameArr[$doctor_id].'</td>
			<td class="text_10" valign="top">'.$facName.'</td>
			<td class="text_10" valign="top">'.$oprName.'</td>
			<td class="text_10" valign="top">'.$modifier_on.'</td>
		</tr>';
	}
	
	$html = '
		<table  class="rpt rpt_table rpt_table-bordered rpt_padding" cellpadding="0" cellspacing="1" border="0" bgcolor="#FFF3E8">
			<tr>
				<td style="text-align:left; width:200px" class="text_b_w">Patient Name - ID</td>
				<td style="text-align:left; width:150px" class="text_b_w">Encounter ID</td>
				<td style="text-align:left; width:140px" class="text_b_w">DOS</td>
				<td style="text-align:left; width:140px" class="text_b_w">Physican</td>
				<td style="text-align:left; width:140px" class="text_b_w">Facility</td>
				<td style="text-align:left; width:140px" class="text_b_w">Modified By</td>
				<td style="text-align:left; width:140px" class="text_b_w">Modified On</td>
			</tr>
			'.$data.'
		</table>';
	$header = '
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:350px;">Modified Encounters</td>
				<td class="rptbx2" style="width:350px;">Selected Date: '.$Start_date.' to '.$End_date.' </td>
				<td class="rptbx3" style="width:350px;">Created by: '.$createdBy.' on '.$curDate.'</td>
			</tr>
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:342px;">Physican: '.$doctorSelected.'&nbsp;&nbsp;&nbsp;Cr. Phy.: '.$selCrPhy.'</td>
				<td class="rptbx2" style="width:350px;">Selected Facility: '.$facilitySelected.'</td>
				<td class="rptbx3" style="width:350px;">Selected Operator: '.$oprSelected.'</td>
			</tr>
		</table>';
	
	if($num_row > 0) {
		$HTMLCreated = 1;
		$stylePDF = '<style>' . file_get_contents('css/reports_pdf.css') . '</style>';
			$pdf_file_data = <<<DATA
			<page backtop="14mm" backbottom="4mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center; width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>		
				$header
				<table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="#FFF3E8">
					<tr>
						<td style="text-align:left; width:200px" class="text_b_w">Patient Name - ID</td>
						<td style="text-align:left; width:150px" class="text_b_w">Encounter ID</td>
						<td style="text-align:left; width:141px" class="text_b_w">DOS</td>
						<td style="text-align:left; width:141px" class="text_b_w">Physican</td>
						<td style="text-align:left; width:141px" class="text_b_w">Facility</td>
						<td style="text-align:left; width:141px" class="text_b_w">Modified By</td>
						<td style="text-align:left; width:141px" class="text_b_w">Modified On</td>
					</tr>
				</table>
			</page_header>
			<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8">
				<tr bgcolor="#FFFFFF" height="0px">
					<td style="text-align:left; width:200px"></td>
					<td style="text-align:left; width:150px"></td>
					<td style="text-align:left; width:141px"></td>
					<td style="text-align:left; width:141px"></td>
					<td style="text-align:left; width:141px"></td>
					<td style="text-align:left; width:141px"></td>
					<td style="text-align:left; width:141px"></td>
				</tr>				
				$data
			</table>
			</page>
DATA;
		
		$strHTML = $stylePDF . $pdf_file_data;
		$file_location = write_html($strHTML);
		$styleHTML = '<style>' . file_get_contents('css/reports_html.css') . '</style>';
		echo $csv_file_data = $styleHTML . $header.$html;
	} else{
		 echo '<div class="text-center alert alert-info">No record exit.</div>';
	}
}
?>
