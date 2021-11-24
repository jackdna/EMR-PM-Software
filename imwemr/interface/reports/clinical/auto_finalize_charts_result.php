<?php
$dateFormat=get_sql_date_format();
$currency = showCurrency();
$curDate = date(phpDateFormat());
if(empty($Start_date) === true){
	$Start_date = $curDate;
	$End_date = $curDate;
}
$printFile = true;
$csvFileData = NULL;
if ($_POST['form_submitted']) {
	$pdfData = NULL;
	$printFile = false;

	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = ucfirst(trim($op_name_arr[1][0]));
	$op_name .= ucfirst(trim($op_name_arr[0][0]));
	$curDate = date(phpDateFormat().' h:i A');	


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
	
	//--- CHANGE DATE FORMAT ---
	$startDate = getDateFormatDB($Start_date);
	$endDate = getDateFormatDB($End_date);
	
	$Physician = implode(',',$filing_provider);
	$sc_name = implode(',',$facility_id);
	
	//GET ALL SCHEDULE FACILITIES
	$fac_query = "select id,name from facility order by name";
	$fac_query_res = imw_query($fac_query);
	$arr_all_sch_fac_arr = array();
	while ($fac_res = imw_fetch_array($fac_query_res)) {
		$fac_id = $fac_res['id'];
		$arr_all_sch_fac_arr[$fac_id]=addslashes($fac_res['name']);
	}

	//--- GET ALL PROVIDER NAME ----
	$providerRs = imw_query("Select id,fname,mname,lname from users");
	$providerNameArr = array();
	$providerNameArr[0] = 'No Provider';
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
	}

	if(empty($Physician) == true){
		$Physician = join(',',$phyIdArr);
	}

	// GET REPORT DATA
	$columnsArr = array();
	$none_charge_list = array();
	$get_charge_list_id = array();
	$printFile = false;
	$sortByPayment = NULL;
	$curDate = date(phpDateFormat().' h:i A');
	
	$practice_name = $CLSReports->report_display_selected($sc_name,'facility_tbl',1, $allFacCount);
	$physician_name = $CLSReports->report_display_selected($Physician,'physician',1, $allPhyCount);
	
	//--- GET ALL RESULTS FROM PATIENT CHARGE LIST TABLE ---------
	$ptChrgQry = "Select chm.id, chm.patient_id, chm.providerId, DATE_FORMAT(chm.date_of_service, '$dateFormat') as 'date_of_service', chm.facilityid, pd.fname, pd.mname, pd.lname  
	FROM chart_master_table chm JOIN patient_data pd ON pd.id=chm.patient_id WHERE chm.autoFinalize='1' 
	AND (chm.date_of_service BETWEEN '$startDate' AND '$endDate')";
	if(empty($sc_name) == false){
		$ptChrgQry .= " and chm.facilityid IN ($sc_name)";
	}
	if(empty($Physician) == false){
		$ptChrgQry .= " and chm.providerId IN ($Physician)";
	}
	$ptChrgQry .= " ORDER BY pd.lname,pd.fname";
	$qry_res = imw_query($ptChrgQry);

	$mainQryRes = array();
	$ovr_pay_arr=array();
	while($res=imw_fetch_array($qry_res)){
		$phy_id=$res['providerId'];
		$mainQryRes[$phy_id][] = $res;
	}
	unset($qry_res);

	
	$op = 'p';
	$main_provider_arr=array();
	//--- GET ADJUSTMENT AMOUNT ---

	//MAKING OUTPUT DATA
	$file_name="auto_finalize_charts_".time().".csv";
	$csv_file_name= write_html("", $file_name);

	if(file_exists($csv_file_name)){
		unlink($csv_file_name);
	}
	$fp = fopen ($csv_file_name, 'a+');

	$arr=array();
	$arr[]='Auto Finalize Charts Report';
	$arr[]="DOS From ".$Start_date." To ".$End_date;
	$arr[]="Created by: $op_name on $curDate";
	fputcsv($fp,$arr, ",","\"");
	$arr=array();
	$arr[]="Physician :".$physician_name;
	$arr[]="Facility :".$practice_name;
	fputcsv($fp,$arr, ",","\"");

	$arr=array();
	$arr[]="Patient Name-ID";
	$arr[]="Chart DOS";
	$arr[]="Physician";
	$arr[]="Facility";
	fputcsv($fp,$arr, ",","\"");
	
	$page_header_val = '
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
			<tr>
				<td class="rptbx1" style="width:230px;">Auto Finalize Charts Report</td>
				<td class="rptbx2" style="width:240px;">DOS From '.$Start_date.' To '.$End_date.'</td>
				<td class="rptbx3" style="width:240px;">Created by: '.$op_name.' on '.$curDate.'</td>
			</tr>
			<tr>
				<td class="rptbx1" style="width:230px;">Sel Phy.: '.$physician_name.'</td>
				<td class="rptbx2" style="width:240px;">Sel Fac.: '.$practice_name.'</td>
				<td class="rptbx3" style="width:240px;"></td>
			</tr>
	</table>';
		
	foreach($mainQryRes as $phy_id => $data_arr){
		$printFile=true;	
		$countRes=0;
		$pdfData2 .='<tr><td class="text_b_w" colspan="5" align="left">Physician Name : '.$providerNameArr[$phy_id].'</td></tr>';
		$csvFileData2 .='<tr><td class="text_b_w" colspan="6" align="left">Physician Name : '.$providerNameArr[$phy_id].'</td></tr>';			

		foreach($data_arr as $detail_data){	
			$countRes++;

			$pat_name = core_name_format($detail_data['lname'], $detail_data['fname'], $detail_data['mname']).' - '.$detail_data['patient_id'];
			
			//--- PDF FILE DATA ----
			$pdfData2 .='<tr>
				<td class="text_12" bgcolor="#FFFFFF" width="20" align="center">'.$countRes.'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="230">'.$pat_name.'</td>
				<td class="text_12" bgcolor="#FFFFFF" align="left" width="90">'.$detail_data['date_of_service'].'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="190" align="left">'.$providerNameArr[$phy_id].'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="190" align="left">'.$arr_all_sch_fac_arr[$detail_data['facilityid']].'</td>
			</tr>';
			$csvFileData2 .='<tr>
				<td class="text_12" bgcolor="#FFFFFF" width="20" align="center">'.$countRes.'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="190">'.$pat_name.'</td>
				<td class="text_12" bgcolor="#FFFFFF" align="left" width="118">'.$detail_data['date_of_service'].'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="118">'.$providerNameArr[$phy_id].'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="118" align="left">'.$arr_all_sch_fac_arr[$detail_data['facilityid']].'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="118" style="text-align:right;"></td>
			</tr>';	

			//FOR CSV
			$arr=array();
			
			$arr[]=$pat_name;
			$arr[]=$detail_data['date_of_service'];
			$arr[]=$providerNameArr[$phy_id];
			$arr[]=$arr_all_sch_fac_arr[$detail_data['facilityid']];
			fputcsv($fp,$arr, ",","\"");				
		}
	}
	fclose($fp);

	$pdfData = '
	<page backtop="16mm" backbottom="5mm">
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>		
	<page_header>
		'.$page_header_val.'
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" bgcolor="#FFF3E8">						
			<tr>
				<td class="text_b_w" width="20" align="center">S.No</td>
				<td class="text_b_w" width="230" align="center">Patient Name-ID</td>
				<td class="text_b_w" width="90" align="center">Chart DOS</td>
				<td class="text_b_w" width="190" align="center">Physician</td>
				<td class="text_b_w" width="190" align="center">Facility</td>
			</tr>
		</table>
	</page_header>
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" bgcolor="#FFF3E8">
		'.$pdfData2.'
	</table>
	</page>';

	$csvFileData = $page_header_val.'
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" bgcolor="#FFF3E8">					
		<tr>
			<td class="text_b_w" width="20" align="center">S.No</td>
			<td class="text_b_w" width="190" align="center">Patient Name-ID</td>
			<td class="text_b_w" width="118" align="center">Chart DOS</td>
			<td class="text_b_w" width="118" align="center">Physician</td>
			<td class="text_b_w" width="118" align="center">Facility</td>
			<td class="text_b_w" width="118" align="center"></td>
		</tr>
		'.$csvFileData2.'
	</table>';


	$HTMLCreated='0';
	if($printFile == true){
		$HTMLCreated='1';
		$pdf_css= '<style>'.file_get_contents("../css/reports_pdf.css").'</style>';
		$strHTML = <<<DATA
			$pdf_css
			$pdfData
DATA;
		$file_location = write_html($strHTML);


		if($output_option=='output_csv'){
			echo '<div class="text-center alert alert-info">Please click on link near application bottom to download CSV file.</div>';
		}elseif($output_option=='output_pdf'){
			echo '<div class="text-center alert alert-info">PDF generated in separate window.</div>';
		}elseif($output_option=='view'){
			echo $csvFileData;	
		}
	}else{
		echo '<div class="text-center alert alert-info">No record exists.</div>';
	}
}
?>