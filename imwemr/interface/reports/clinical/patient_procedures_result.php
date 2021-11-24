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
	
	$start_time='00:00:00';
	$end_time='23:59:59';
	
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

	//MASTER PROCEDURES ARRAY
	$rs = imw_query("Select procedure_id,procedure_name from operative_procedures");
	$arrAllProcedures = array();
	while($res = imw_fetch_assoc($rs)){
		$arrAllProcedures[$res['procedure_id']]=$res['procedure_name'];
	}
	
	// GET REPORT DATA
	$columnsArr = array();
	$none_charge_list = array();
	$get_charge_list_id = array();
	$printFile = false;
	$sortByPayment = NULL;
	$curDate = date(phpDateFormat().' h:i A');
	
	//GET ALL RESULTS
	$qry = "Select proc.patient_id, proc.proc_id, proc.pre_op_meds, proc.intravit_meds, proc.post_op_meds,
	DATE_FORMAT(proc.exam_date, '$getSqlDateFormat') as 'exam_date', 
	pd.fname, pd.mname, pd.lname 
	FROM chart_procedures proc
	JOIN patient_data pd ON pd.id = proc.patient_id
	WHERE (proc.exam_date BETWEEN '$start_date $start_time' AND '$end_date $end_time') AND deleted_by='0' 
	ORDER BY pd.lname, pd.fname";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$pid=$res['patient_id'];
		
		$arrMainResults[$pid][] = $res;
		$arrPatIds[$pid]=$pid;
	}

	//GETTING APPT AND INSURNACE INFO
	$arrApptMadeDate=array();
	$arrApptSchid=array();
	if(sizeof($arrMainResults)>0){
		$arr_chunks=array_chunk($arrPatIds, 2000);
		
		foreach($arr_chunks as $arr){
			$str_pat_ids=implode(',', $arr);
			
			$qry="Select old_date, patient_id, sch_id, DATE_FORMAT(status_date, '$getSqlDateFormat') as 'status_date',
			DATE_FORMAT(new_appt_date, '$getSqlDateFormat') as 'new_appt_date'
			FROM previous_status 
			WHERE patient_id IN(".$str_pat_ids.") AND status!='203' ORDER BY id";
			$rs=imw_query($qry);
			while($res=imw_fetch_assoc($rs)){
				$pid=$res['patientId'];
				$appt_date=$res['new_appt_date'];
				
				//ONLY THIS ARRAY MADE HERE SO THAT ONLY LAST RECORD WILL GET.
				if(!$tempArr[$res['sch_id']]){
					$arrApptMadeDate[$res['sch_id']]=$res['status_date']; 
					$tempArr[$res['sch_id']]=$res['sch_id'];
				}
				
				$arrApptSchid[$pid][$appt_date]=$res['sch_id']; 
				$arrApptIds[$res['sch_id']]=$res['sch_id'];
			}
		}
		
		$arrCaseIds=array();
		if(sizeof($arrApptIds)>0){
			$strApptIds=implode(',',$arrApptIds);
			$qry="Select id, case_type_id FROM schedule_appointments WHERE id IN(".$strApptIds.")";
			$rs=imw_query($qry);
			while($res=imw_fetch_assoc($rs)){
				$arrApptCaseIds[$res['id']]=$res['case_type_id'];
				$arrCaseIds[$res['case_type_id']]=$res['case_type_id'];
			}			
		}

		if(sizeof($arrCaseIds)>0){
			$strCaseIds=implode(',', $arrCaseIds);
			$qry="Select data.ins_caseid, data.auth_required, ins.name, DATE_FORMAT(auth.auth_date, '$getSqlDateFormat') as 'auth_date',
			DATE_FORMAT(auth.end_date, '$getSqlDateFormat') as 'end_date' 
			FROM insurance_data data  
			JOIN insurance_companies ins ON ins.id=data.provider 
			LEFT JOIN patient_auth auth ON auth.ins_data_id=data.id 
			WHERE data.ins_caseid IN(".$strCaseIds.") AND LOWER(data.type)='primary'";
			$rs=imw_query($qry);
			while($res=imw_fetch_assoc($rs)){
				$case_id=$res['ins_caseid'];
				
				$arrInsDetail[$case_id]['company']=$res['name'];
				$arrInsDetail[$case_id]['auth_start']=$res['auth_date'];
				$arrInsDetail[$case_id]['auth_end']=$res['end_date'];
			}
		}
	}

	
	$op = 'l';

	//MAKING OUTPUT DATA
	$file_name="patient_procedures_".time().".csv";
	$csv_file_name= write_html("", $file_name);

	if(file_exists($csv_file_name)){
		unlink($csv_file_name);
	}
	$fp = fopen ($csv_file_name, 'a+');

	$arr=array();
	$arr[]='Patient Procedures Report';
	$arr[]="Dos From ".$Start_date." To ".$End_date;
	$arr[]="Created by: $op_name on $curDate";
	fputcsv($fp,$arr, ",","\"");

	$arr=array();
	$arr[]="Patient Name-ID";
	$arr[]="Appt. Made Date";
	$arr[]="Insurance Provider";
	$arr[]="Authorization Date";
	$arr[]="Injection Date";
	$arr[]="Drug Used";
	$arr[]="Atuhorization Validation";
	fputcsv($fp,$arr, ",","\"");
	
	$page_header_val = '
	<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding">
			<tr>
				<td class="rptbx1" style="width:33%;">Patient Procedures Report</td>
				<td class="rptbx2" style="width:33%;">Dos From '.$Start_date.' To '.$End_date.'</td>
				<td class="rptbx3" style="width:34%;">Created by: '.$op_name.' on '.$curDate.'</td>
			</tr>
	</table>';
	
	$countRes=$countSuperbills=$countNoSuperbills=0;	
	$cnt=1;
	foreach($arrMainResults as $pat_id => $pat_data){
		foreach($pat_data as $detail_data){
			$printFile=true;	
			$ins_company=$auth_start=$auth_end='';
			$exam_date=$detail_data['exam_date'];
			$pat_name = core_name_format($detail_data['lname'], $detail_data['fname'], $detail_data['mname']).' - '.$detail_data['patient_id'];
			
			$appt_id=($arrApptSchid[$pid][$exam_date]) ? $arrApptSchid[$pid][$exam_date]: 0; 
			$appt_made_date= ($appt_id>0)? $arrApptMadeDate[$appt_id] :'';
			
			$case_id=($appt_id>0) ? $arrApptCaseIds[$appt_id]: 0;
			if($case_id>0){
				$ins_company=$arrInsDetail[$case_id]['company'];
				$auth_start=$arrInsDetail[$case_id]['auth_start'];
				$auth_end=$arrInsDetail[$case_id]['auth_end'];
			}
			
			//MEDICINES
			$str_medicines='';
			$medicines=$detail_data['pre_op_meds'].'|~|'.$detail_data['intravit_meds'].'|~|'.$detail_data['post_op_meds'];
			$arrMedicines=explode('|~|', $medicines);
			$arrMedicines=array_unique(array_filter($arrMedicines));
			$str_medicines= (sizeof($arrMedicines)>0) ? implode(', ', $arrMedicines) : $arrAllProcedures[$detail_data['proc_id']];
			
			//--- PDF FILE DATA ----
			$pdfData2 .='<tr>
				<td class="text_12" bgcolor="#FFFFFF" width="5%">'.$cnt.'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="20%">'.$pat_name.'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="8%" align="center">'.$appt_made_date.'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="20%" align="left">'.$ins_company.'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="8%" align="center">'.$auth_start.'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="8%" align="center">'.$exam_date.'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="23%" align="left">'.$str_medicines.'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="8%" align="center">'.$auth_end.'</td>
			</tr>';
			
			$csvFileData2 .='<tr>
				<td class="text_12" bgcolor="#FFFFFF" style="width:5%">'.$cnt.'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="width:20%">'.$pat_name.'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="width:8%" align="center">'.$appt_made_date.'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="width:20%" align="left">'.$ins_company.'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="width:8%" align="center">'.$auth_start.'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="width:8%" align="center">'.$exam_date.'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="width:23%" align="left">'.$str_medicines.'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="width:8%" align="center">'.$auth_end.'</td>
			</tr>';	

			//FOR CSV
			$arr=array();
			$arr[]=$pat_name;
			$arr[]=$appt_made_date;
			$arr[]=$ins_company;
			$arr[]=$auth_start;
			$arr[]=$exam_date;
			$arr[]=$str_medicines;
			$arr[]=$auth_end;
			fputcsv($fp,$arr, ",","\"");				
			
			$cnt++;
		}
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
				<td class="text_b_w" align="center" style="width:20%">Patient Name-ID</td>
				<td class="text_b_w" align="center" style="width:8%">Appt. Made Date</td>
				<td class="text_b_w" align="center" style="width:20%">Insurance Provider</td>
				<td class="text_b_w" align="center" style="width:8%">Authorization Date</td>
				<td class="text_b_w" align="center" style="width:8%">Injection Date</td>
				<td class="text_b_w" align="center" style="width:23%">Drug Used</td>
				<td class="text_b_w" align="center" style="width:8%">Atuhorization Validation</td>
			</tr>
		</table>
	</page_header>
	<table style="width:100%;" class="rpt_table rpt_table-bordered">	
		'.$csvFileData2.'
	</table>
	</page>';

	$csvFileData = $page_header_val.'
		<table style="width:100%" class="rpt_table rpt_table-bordered">			
		<tr>
			<td class="text_b_w" align="center" style="width:5%">#</td>
			<td class="text_b_w" align="center" style="width:20%">Patient Name-ID</td>
			<td class="text_b_w" align="center" style="width:8%">Appt. Made Date</td>
			<td class="text_b_w" align="center" style="width:20%">Insurance Provider</td>
			<td class="text_b_w" align="center" style="width:8%">Authorization Date</td>
			<td class="text_b_w" align="center" style="width:8%">Injection Date</td>
			<td class="text_b_w" align="center" style="width:23%">Drug Used</td>
			<td class="text_b_w" align="center" style="width:8%">Atuhorization Validation</td>
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