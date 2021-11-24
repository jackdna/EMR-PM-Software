<?php
$getSqlDateFormat=get_sql_date_format();
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
	$start_date = getDateFormatDB($Start_date);
	$end_date = getDateFormatDB($End_date);
	
	$arr_physicians=$physician;
	$physicians = implode(',',$physician);
	
	//GET ALL SCHEDULE FACILITIES
	$fac_query = "select id,name from facility order by name";
	$fac_query_res = imw_query($fac_query);
	$arr_all_sch_fac = array();
	while ($fac_res = imw_fetch_array($fac_query_res)) {
		$fac_id = $fac_res['id'];
		$arr_all_sch_fac[$fac_id]=addslashes($fac_res['name']);
	}

	//--- GET ALL PROVIDER NAME ----
	$providerRs = imw_query("Select id,fname,mname,lname from users");
	$providerNameArr = array();
	$providerNameArr[0] = 'No Provider';
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
	}

	
	// GET REPORT DATA
	$columnsArr = array();
	$none_charge_list = array();
	$get_charge_list_id = array();
	$printFile = false;
	$sortByPayment = NULL;
	$curDate = date(phpDateFormat().' h:i A');
	
	//GET ALL RESULTS
	$qry = "Select ch.id, ch.patient_id, DATE_FORMAT(ch.date_of_service, '$getSqlDateFormat') as 'dos', ch.providerId, ch.facilityid,
	pd.fname, pd.mname, pd.lname 
	FROM chart_master_table ch
	LEFT JOIN patient_data pd ON pd.id = ch.patient_id
	WHERE (ch.date_of_service BETWEEN '$start_date' AND '$end_date') AND delete_status='0'";
	if(sizeof($arr_physicians)>0){
		$qry.=" AND ch.providerId IN(".$physicians.")";
	}
	$qry.=" ORDER BY pd.lname";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$formid=$res['id'];
		$pid=$res['patient_id'];
		$dos=$res['dos'];
		$phyId = $res['physicianId'];			
		$facId = $res['facility_id'];
		$res['testType']='IVFA';
		$res['testType_sp'] = 'IVFA';
		
		$arrMainResults[$pid][$formid][$dos] = $res;
		$arrFromIds[$formid]=$formid;
	}


	//CHECK SUPERBILLS
	if(sizeof($arrMainResults)>0){
		$arr_tests_superbills=array();
		$arr_superbills_posted=array();
		$arr_pat_ids=array_keys($arrMainResults);
		$arr_chunks=array_chunk($arr_pat_ids, 2000);
		
		foreach($arr_chunks as $arr){
			
			$str_pat_ids=implode(',', $arr);
			
			$qry="Select idSuperBill, patientId, formId, test_id, DATE_FORMAT(dateOfService, '$getSqlDateFormat') as 'dos' FROM superbill WHERE patientId IN(".$str_pat_ids.") AND del_status<='0' ORDER BY formId DESC";
			$rs=imw_query($qry);
			while($res=imw_fetch_assoc($rs)){
				$pid=$res['patientId'];
				$dos=$res['dos'];
				$i=0;
						
				if($arrFromIds[$res['formId']]){
					unset($arrMainResults[$pid][$res['formId']]);
				}else{
					foreach($arrMainResults[$pid] as $formid =>$formData){
						foreach($formData[$dos] as $det_data){
							if($i==0){ //SO THAT ONLY ONE RECORD REMOVE AT CHECK OF ONE Superbill
								unset($arrMainResults[$pid][$formid][$dos]);
								$i++;
							}
						}
					}
				}

			}
		}
		
		//SIMPLIFY THE ARRAY
		$tempArr=$arrMainResults;
		$arrMainResults=array();
		foreach($tempArr as $pid =>$formData){
			foreach($formData as $formid =>$dosData){
				foreach($dosData as $dos =>$detData){
					$arrMainResults[$formid]=$detData;
				}
			}
		}
		unset($tempArr);
	}
	

	//OUTPUT CREATION
	$physician_name = $CLSReports->report_display_selected($physicians,'physician',1, $allPhyCount);
	
	$op = 'p';

	//MAKING OUTPUT DATA
	$file_name="un_superbilled_encounters_".time().".csv";
	$csv_file_name= write_html("", $file_name);

	if(file_exists($csv_file_name)){
		unlink($csv_file_name);
	}
	$fp = fopen ($csv_file_name, 'a+');

	$arr=array();
	$arr[]='Un Superbilled Encounters Report';
	$arr[]="Dos From ".$Start_date." To ".$End_date;
	$arr[]="Created by: $op_name on $curDate";
	fputcsv($fp,$arr, ",","\"");
	$arr=array();
	$arr[]="Sel Physician :".$physician_name;
	fputcsv($fp,$arr, ",","\"");

	$arr=array();
	$arr[]="Patient Name-ID";
	$arr[]="DOS";
	$arr[]="Physician";
	$arr[]="Facility";
	fputcsv($fp,$arr, ",","\"");
	
	$page_header_val = '
	<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding">
			<tr>
				<td class="rptbx1" style="width:33%;">Un Superbilled Encounters Report</td>
				<td class="rptbx2" style="width:33%;">Dos From '.$Start_date.' To '.$End_date.'</td>
				<td class="rptbx3" style="width:34%;">Created by: '.$op_name.' on '.$curDate.'</td>
			</tr>
			<tr>
				<td class="rptbx1">Sel Phy.: '.$physician_name.'</td>
				<td class="rptbx2"></td>
				<td class="rptbx3"></td>
			</tr>
	</table>';
	
	$countRes=$countSuperbills=$countNoSuperbills=0;	
	$cnt=1;
	foreach($arrMainResults as $formid => $detail_data){
		$printFile=true;	
		$pat_name = core_name_format($detail_data['lname'], $detail_data['fname'], $detail_data['mname']).' - '.$detail_data['patient_id'];
		
		//--- PDF FILE DATA ----
		$pdfData2 .='<tr>
			<td class="text_12" bgcolor="#FFFFFF" width="5%">'.$cnt.'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="25%">'.$pat_name.'</td>
			<td class="text_12" bgcolor="#FFFFFF" align="left" width="10%">'.$detail_data['dos'].'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="25%" align="left">'.$providerNameArr[$detail_data['providerId']].'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="35%" align="left">'.$arr_all_sch_fac[$detail_data['facilityid']].'</td>
		</tr>';
		
		$csvFileData2 .='<tr>
			<td class="text_12" bgcolor="#FFFFFF" style="width:5%">'.$cnt.'</td>
			<td class="text_12" bgcolor="#FFFFFF" style="width:25%">'.$pat_name.'</td>
			<td class="text_12" bgcolor="#FFFFFF" align="left" style="width:10%">'.$detail_data['dos'].'</td>
			<td class="text_12" bgcolor="#FFFFFF" style="width:25%; align:left">'.$providerNameArr[$detail_data['providerId']].'</td>
			<td class="text_12" bgcolor="#FFFFFF" style="width:35%" style="text-align:left;">'.$arr_all_sch_fac[$detail_data['facilityid']].'</td>
		</tr>';	

		//FOR CSV
		$arr=array();
		$arr[]=$pat_name;
		$arr[]=$detail_data['dos'];
		$arr[]=$providerNameArr[$detail_data['providerId']];
		$arr[]=$arr_all_sch_fac[$detail_data['facilityid']];
		fputcsv($fp,$arr, ",","\"");				
		
		$cnt++;
	}
	fclose($fp);
	
	$pdfData = '
	<page backtop="15mm" backbottom="5mm">
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>		
	<page_header>     
		'.$page_header_val.'
		<table style="width:100%;" class="rpt_table rpt_table-bordered">		
			<tr>
				<td class="text_b_w" align="center" style="width:5%">#</td>
				<td class="text_b_w" align="center" style="width:25%">Patient Name-ID</td>
				<td class="text_b_w" align="center" style="width:10%">DOS</td>
				<td class="text_b_w" align="center" style="width:25%">Physician</td>
				<td class="text_b_w" align="center" style="width:35%">Facility</td>
			</tr>
		</table>
	</page_header>
	<table style="width:100%" class="rpt_table rpt_table-bordered">
		'.$csvFileData2.'
	</table>
	</page>';

	$csvFileData = $page_header_val.'
		<table style="width:100%" class="rpt_table rpt_table-bordered">			
		<tr>
			<td class="text_b_w" align="center" style="width:5%">#</td>
			<td class="text_b_w" align="center" style="width:25%">Patient Name-ID</td>
			<td class="text_b_w" align="center" style="width:10%">DOS</td>
			<td class="text_b_w" align="center" style="width:25%">Physician</td>
			<td class="text_b_w" align="center" style="width:35%">Facility</td>
		</tr>
		'.$csvFileData2.'
	</table>';


	$HTMLCreated='0';
	if($printFile == true){
		$HTMLCreated='1';
		$styleHTML='<style>'.file_get_contents('../css/reports_html.css').'</style>';	
		$pdf_css= '<style>'.file_get_contents("../css/reports_pdf.css").'</style>';
		
		$csvFileData=$styleHTML.$csvFileData;
		
		$strHTML =$pdf_css.$pdfData;
		$file_location = write_html($strHTML, 'unbilled_tests.html');


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