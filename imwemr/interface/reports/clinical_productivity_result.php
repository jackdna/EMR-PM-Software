<?php 
$dateFormat= get_sql_date_format();
$report_generator_name = "";
if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
	$op_name_arr = preg_split("/, /",$_SESSION["authProviderName"]);
	$report_generator_name = $op_name_arr[1][0];
	$report_generator_name .= $op_name_arr[0][0];
}

if(empty($_POST['form_submitted']) === false){
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
	
	$start_date = getDateFormatDB($Start_date);
	$end_date = getDateFormatDB($End_date);
	//---------------------
	$phpDateFormat 		= phpDateFormat();
	$provider_id = implode(',',$phy_name);
	$selPhyArr=array_combine($phy_name, $phy_name);
	
	//USERS
	$qry="Select id, fname, mname, lname FROM users";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$nameArr = array();
		$nameArr["LAST_NAME"] = $res['lname'];
		$nameArr["FIRST_NAME"] = $res['fname'];
		$nameArr["MIDDLE_NAME"] = $res['mname'];
		$arrAllUsers[$res['id']] = changeNameFormat($nameArr);
	}
	
	//PROCEDURES
	$qry="Select id, proc FROM slot_procedures";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$arrAllProcedures[$res['id']] = $res['proc'];
	}
	
	//GET PROCEDURE ID WHEN CLINICAL OPTION IS AFTER APPOINTMENT DATE
	function getProcId($pid, $dos, $temp_info, $phpDateFormat){
		$proc_id=0;
		$check_date=$dos;
		$i=0;
		while($i<=180){
			$strtotime= strtotime($check_date);
			$check_date= date('Y-m-d', mktime(0,0,0, date('m',$strtotime),date('d',$strtotime)-1,date('Y',$strtotime)));
			$compare_date= date($phpDateFormat, strtotime($check_date));
			
			if($temp_info[$pid][$compare_date]){
				$proc_id= $temp_info[$pid][$compare_date]['proc_id'];
				break;
			}
			$i++;
		}
		return $proc_id;
	}
	
	//GET PROCEDURE IDS
	$qry="Select sa.sa_patient_id, sa.procedureid, DATE_FORMAT(sa.sa_app_start_date, '".$dateFormat."') as sa_app_start_date FROM schedule_appointments sa 
	WHERE (sa.sa_app_start_date BETWEEN '$start_date' AND '$end_date')";
	$qry.=" ORDER BY sa.sa_app_start_date";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$pid=$res['sa_patient_id'];
		$dos=$res['sa_app_start_date'];
		$temp_info[$pid][$dos]['proc_id']=$res['procedureid'];
	}
	
	//MR SELECTION	
	if($mr1==1 || $mr2==1 || $mr3==1){ 
		/*
		$qry="Select cv.patient_id, cv.provider_id, cv.providerIdOther, cv.providerIdOther_3, 
			DATE_FORMAT(cm.date_of_service, '%Y-%m-%d') as date_of_service, DATE_FORMAT(date_of_service, '".$dateFormat."') as date_of_service_new,
		cv.vis_statusElements, vis_mr_desc, vis_mr_desc_other, vis_mr_desc_3      
		FROM chart_vision cv JOIN chart_master_table cm ON cm.id = cv.form_id
		WHERE (DATE_FORMAT(cm.date_of_service, '%Y-%m-%d') BETWEEN '$start_date' AND '$end_date')";
		if(trim($provider_id) != ''){
			$qry.=" AND (";
			$OR='';
			if($mr1==1){ $qry.= " cv.provider_id IN($provider_id)"; $OR=' OR ';	}
			if($mr2==1){ $qry.=$OR." cv.providerIdOther IN($provider_id)"; $OR=' OR ';	}
			if($mr3==1){ $qry.=$OR." cv.providerIdOther_3 IN($provider_id)"; $OR=' OR ';	}
			$qry.=") ";
		}
		if($allmr==1){
			$qry.=" AND cv.vis_statusElements LIKE '%elem_visMr%'";
		}else{
			$qry.=" AND ("; $OR="";
			
			if($mr1==1){
				$qry.= $OR." (cv.vis_statusElements LIKE '%elem_visMrOd%' OR cv.vis_statusElements LIKE '%elem_visMrOs%')"; 
				$OR=" OR ";
			}
			if($mr2==1){
				$qry.= $OR." (cv.vis_statusElements LIKE '%elem_visMrOtherOd%' OR cv.vis_statusElements LIKE '%elem_visMrOtherOs%')"; 
				$OR=" OR ";
			}
			if($mr3==1){
				$qry.= $OR." (cv.vis_statusElements LIKE '%elem_visMrOther%' AND vis_statusElements LIKE '%_3%')";
			}
			$qry.=") ";
		}
		$qry.=" ORDER BY cm.date_of_service DESC";
		*/
		
		$qry = "
			Select
			cv.patient_id, c1.provider_id,
			DATE_FORMAT(cm.date_of_service, '%Y-%m-%d') as date_of_service, DATE_FORMAT(date_of_service, '".$dateFormat."') as date_of_service_new,
			cv.status_elements, c1.ex_desc, c1.ex_number 
			FROM chart_vis_master cv JOIN chart_master_table cm ON cm.id = cv.form_id
			LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = cv.id			
			WHERE (DATE_FORMAT(cm.date_of_service, '%Y-%m-%d') BETWEEN '$start_date' AND '$end_date')
			AND c1.ex_type = 'MR' AND c1.ex_number IN (1,2,3)";
		
		if(trim($provider_id) != ''){
			$qry.=" AND (";
			$OR='';
			if($mr1==1 || $mr2==1 || $mr3==1){ $qry.= " c1.provider_id IN($provider_id)"; }			
			$qry.=") ";
		}
		
		if($allmr==1){
			$qry.=" AND cv.status_elements LIKE '%elem_visMr%'";
		}else{
			$qry.=" AND ("; $OR="";
			
			if($mr1==1){
				$qry.= $OR." (cv.status_elements LIKE '%elem_visMrOd%' OR cv.status_elements LIKE '%elem_visMrOs%')"; 
				$OR=" OR ";
			}
			if($mr2==1){
				$qry.= $OR." (cv.status_elements LIKE '%elem_visMrOtherOd%' OR cv.status_elements LIKE '%elem_visMrOtherOs%')"; 
				$OR=" OR ";
			}
			if($mr3==1){
				$qry.= $OR." (cv.status_elements LIKE '%elem_visMrOther%' AND status_elements LIKE '%_3%')";
			}
			$qry.=") ";
		}
		
		$qry.=" ORDER BY cm.date_of_service DESC";		
		
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$tempArr=array();
			$pid=$res['patient_id'];
			$dos=$res['date_of_service_new'];
			
			$mrcmt1=$res['ex_desc'];
			$proc_id= $temp_info[$pid][$dos]['proc_id'];
			if($proc_id<=0){ //FIND DOS LESS THAN EXAM DATE, IF EXAM IS DONE AFTER APPT DATE
				$proc_id = getProcId($pid, $res['date_of_service'], $temp_info, $phpDateFormat);
			}
			
			$cc = $res["ex_number"];
			
			$flg=0;
			if($cc==1 && $mr1==1){
				if((preg_match('/elem_visMrOd|elem_visMrOs/', $res['status_elements']))){$flg=1;}
			}else if ($cc==2 && $mr2==1){
				if((preg_match('/elem_visMrOtherOd|elem_visMrOtherOs/', $res['status_elements']))){$flg=1;}
			}else if ($cc==3 && $mr3==1){
				if(preg_match('/elem_visMrOtherOd|elem_visMrOtherOs/', $res['status_elements']) && preg_match('/_3/', $res['status_elements'])){$flg=1;}
			}else{
				continue;
			}
			
			if(!empty($flg)){
				if(trim($provider_id)!=''){ //TO FETCH DATA BASED ON PARTICULAR USER ID
					if($selPhyArr[$res['provider_id']]){
						$given='';
						if(preg_match('/elem_mrNoneGiven'.$cc.'=1/', $res['status_elements'])){
							$given='(Given)';
							$arr_mr_given['MR'.$cc][]=1;
						}
						$tempArr[$res['provider_id']]['MR'.$cc]['given']='MR'.$cc.$given;
						$tempArr[$res['provider_id']]['MR'.$cc]['Comments']=$mrcmt1;
						$arr_mr_breakdown['MR'.$cc][]=1;
					}
				}else{
					$given='';
					if(preg_match('/elem_mrNoneGiven'.$cc.'=1/', $res['status_elements'])){
						$given='(Given)';
						$arr_mr_given['MR'.$cc][]=1;
					}
					$tempArr[$res['provider_id']]['MR'.$cc]['given']='MR'.$cc.$given;
					$tempArr[$res['provider_id']]['MR'.$cc]['Comments']=$mrcmt1;
					$arr_mr_breakdown['MR'.$cc][]=1;
				}
			}

			foreach($tempArr as $uid => $dataArr){
				$mr = '';
				$tmpMrArr = array();
				if(count($dataArr) > 0){
					foreach($dataArr as $mrKey => $mrValues){
						if($mrValues['given']) $tmpMrArr['given'][] = $mrValues['given'];
						if($mrValues['Comments']) $tmpMrArr['Comments'][$mrKey] = $mrKey.': '.$mrValues['Comments'];
					}
				}
				$mr=implode(', ', $tmpMrArr['given']);
				if($report_view=='summary'){
					$arr_result_data[$uid][$proc_id][$pid]=$pid;
				}else{
					$arr_result_data[$uid][$pid][$dos]['proc_id']=$proc_id;
					$arr_clinical_options[$uid][$pid][$dos][]= $mr;
					if($tmpMrArr['Comments']) $arr_clinical_options[$uid][$pid][$dos]['Comments'] = implode('; ', $tmpMrArr['Comments']);
				}
				$arrPatIds[$pid]=$pid;
			}
			unset($tempArr);
		}
	}
	
	//IOP SELECTION
	if($iop==1){
		$qry="Select ci.patient_id, ci.uid, DATE_FORMAT(cm.date_of_service, '%Y-%m-%d') as date_of_service, DATE_FORMAT(cm.date_of_service, '".$dateFormat."') as date_of_service_new  
		FROM chart_iop ci JOIN chart_master_table cm ON cm.id = ci.form_id
		WHERE (DATE_FORMAT(cm.date_of_service, '%Y-%m-%d') BETWEEN '$start_date' AND '$end_date')";
		if(trim($provider_id) != ''){
			$qry.= " AND ci.uid IN($provider_id)";
		}
		$qry.=" ORDER BY cm.date_of_service DESC";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$uid=$res['uid'];
			$pid=$res['patient_id'];
			$dos=$res['date_of_service_new'];
			
			$proc_id= $temp_info[$pid][$dos]['proc_id'];
			if($proc_id<=0){ //FIND DOS LESS THAN EXAM DATE, IF EXAM IS DONE AFTER APPT DATE
				$proc_id = getProcId($pid, $res['date_of_service'], $temp_info, $phpDateFormat);
			}
			
			if($report_view=='summary'){
				$arr_result_data[$uid][$proc_id][$pid]=$pid;
				$arrPatIds[$pid]=$pid;
			}else{
				$arr_result_data[$uid][$pid][$dos]['proc_id']=$proc_id;
				$arr_clinical_options[$uid][$pid][$dos][]='IOP';
				$arrPatIds[$pid]=$pid;
			}
		}
	}
	
	//INTERMEDIATE/COMPREHENSIVE SELECTION
	if($intermediate==1 || $comprehensive==1){
		$qry="Select sb.patientId, cm.date_of_service,  DATE_FORMAT(cm.date_of_service, '".$dateFormat."') as date_of_service_new,
		sb.procOrder, cap.uid  
		FROM superbill sb JOIN chart_master_table cm ON cm.id = sb.formId 
		JOIN chart_assessment_plans cap ON cap.form_id = cm.id 
		WHERE (cm.date_of_service BETWEEN '$start_date' AND '$end_date')";
		if(trim($provider_id) != ''){
			$qry.= " AND cap.uid IN($provider_id)";
		}
		$qry.= " AND ("; $OR='';
		if($intermediate==1){
			$qry.= " (sb.procOrder LIKE '%92002%' OR sb.procOrder LIKE '%92012%')";
			$OR=' OR ';
		}
		if($comprehensive==1){
			$qry.= $OR." (sb.procOrder LIKE '%92004%' OR sb.procOrder LIKE '%92014%')";
		}
		$qry.= ") ";
		$qry.=" ORDER BY cm.date_of_service DESC";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$uid=$res['uid'];
			$pid=$res['patientId'];
			$dos=$res['date_of_service_new'];

			$proc_id= $temp_info[$pid][$dos]['proc_id'];
			if($proc_id<=0){ //FIND DOS LESS THAN EXAM DATE, IF EXAM IS DONE AFTER APPT DATE
				$proc_id = getProcId($pid, $res['date_of_service'], $temp_info, $phpDateFormat);
			}
			
			if($report_view=='summary'){
				$arr_result_data[$uid][$proc_id][$pid]=$pid;
				$arrPatIds[$pid]=$pid;
			}else{
				$arr_result_data[$uid][$pid][$dos]['proc_id']=$proc_id;
				if(preg_match('/92002|92012/', $res['procOrder'])){ 
					$arr_exam_options[$uid][$pid][$dos][]='Intermediate';
					$arrPatIds[$pid]=$pid;
				}
				if(preg_match('/92004|92014/', $res['procOrder'])){ 
					$arr_exam_options[$uid][$pid][$dos][]='Comprehensive';
					$arrPatIds[$pid]=$pid;
				}
			}
			$arrPatIds[$pid]=$pid;
		}
	}
	
	//GET Patient Names
	if(sizeof($arrPatIds)>0){
		$strPatIds=implode(',', $arrPatIds);
		$qry="Select pd.id, pd.fname, pd.mname, pd.lname FROM patient_data pd WHERE pd.id IN(".$strPatIds.")";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$patient_name_arr = array();
			$patient_name_arr["LAST_NAME"] = $res['lname'];
			$patient_name_arr["FIRST_NAME"] = $res['fname'];
			$patient_name_arr["MIDDLE_NAME"] = $res['mname'];		
			$patient_name = changeNameFormat($patient_name_arr);
			$arr_patient_info[$res['id']]=$patient_name;
		}
	}
	
		if(count($arr_result_data)> 0){	
		$curDate = get_date_format(date('m-d-Y'),'mm-dd-yyyy');
		$curDate.=	'&nbsp;'.date('h:i A');	
		$phyName = $_SESSION['authProviderName'];
		
		//SELECTION CRITERIA
		$selUsers = $CLSReports->report_display_selected($provider_id,'physician',1, $allPhyCount);
		$arrSelClinicalOpts=array();
		$selClinicalOpts='';
		if($allmr==1)$arrSelClinicalOpts[]='MR (All)';
		if($allmr==0){
			if($mr1==1)$arrSelClinicalOpts[]='MR 1';
			if($mr2==1)$arrSelClinicalOpts[]='MR 2';
			if($mr3==1)$arrSelClinicalOpts[]='MR 3';
		}
		if($iop==1)$arrSelClinicalOpts[]='IOP';
		if($comprehensive==1)$arrSelClinicalOpts[]='Comprehensive';
		if($intermediate==1)$arrSelClinicalOpts[]='Intermediate';
		$selClinicalOpts = implode(', ', $arrSelClinicalOpts);
		

		if($report_view=='detail'){
			$header_part='
			<table class="rpt rpt_table rpt_table-bordered rpt_padding">
				<tr>
					<td class="text_b_w" style="width:30px; text-align:center">#</td>
					<td class="text_b_w" style="width:200px;">Patient Name</td>
					<td class="text_b_w" style="width:100px; text-align:center">DOS</td>
					<td class="text_b_w" style="width:150px;">Visit Type</td>
					<td class="text_b_w" style="width:150px;">Exam</td>
					<td class="text_b_w" style="width:200px;">Clinical Options</td>
					<td class="text_b_w" style="width:220px;">MR Comments</td>
				</tr>
			</table>';	

			foreach($arr_result_data as $doc_id => $patData){	
				$phy_name=$arrAllUsers[$doc_id];
				$cnt=1;
				$page_content.='
				<tr>
					<td class="text_b_w" colspan="7">User : '.$phy_name.'</td>
				</tr>';

				foreach($patData as $pat_id => $dosData){
					foreach($dosData as $dos => $dosDetail){
						$exam_options=$clinical_options='';
						$proc_name= $arrAllProcedures[$dosDetail['proc_id']];
						$patient_name= $arr_patient_info[$pat_id];
						$mrComments = '';
						if($arr_clinical_options[$doc_id][$pat_id][$dos]['Comments']){
							$mrComments = $arr_clinical_options[$doc_id][$pat_id][$dos]['Comments'];
							unset($arr_clinical_options[$doc_id][$pat_id][$dos]['Comments']);
						}
						$clinical_options=implode(', ', $arr_clinical_options[$doc_id][$pat_id][$dos]);
						$exam_options = implode(", ", $arr_exam_options[$doc_id][$pat_id][$dos]);
						
						
						$page_content.='
						<tr>
							<td class="text_10" style="width:30px; text-align:center" bgcolor="#ffffff">'.$cnt.'</td>
							<td class="text_10" style="width:200px; text-align:left" bgcolor="#ffffff">'.$patient_name.' - '.$pat_id.'</td>
							<td class="text_10" style="width:100px; text-align:center" bgcolor="#ffffff">'.$dos.'</td>
							<td class="text_10" style="width:150px; text-align:left" bgcolor="#ffffff">'.$proc_name.'</td>
							<td class="text_10" style="width:150px; text-align:left" bgcolor="#ffffff">'.$exam_options.'</td>
							<td class="text_10" style="width:200px; text-align:left" bgcolor="#ffffff">'.$clinical_options.'</td>
							<td class="text_10" style="width:220px; text-align:left" bgcolor="#ffffff">'.$mrComments.'</td>
						</tr>';
						$cnt++;
					}
				}
			}
		}else{
			//summary
			$total_count=0;
			$header_part='
			<table class="rpt rpt_table rpt_table-bordered rpt_padding">
				<tr>
					<td class="text_b_w" style="width:300px;">Procedure</td>
					<td class="text_b_w" style="width:150px;">Total Count</td>
					<td class="text_b_w" style="width:600px;"></td>
				</tr>
			</table>';
					
			foreach($arr_result_data as $doc_id => $procData){	
				$cnt=0;
				$phy_name=$arrAllUsers[$doc_id];
				$page_content.='
				<tr>
					<td class="text_b_w" colspan="3">User : '.$phy_name.'</td>
				</tr>';
	
				foreach($procData as $proc_id => $procDetail){
					$proc_name= $arrAllProcedures[$proc_id];
					$count=count($procDetail);
					$cnt+=$count;
					
					$page_content.='
					<tr>
						<td class="text_10" style="width:305px; text-align:left" bgcolor="#ffffff">'.$proc_name.'</td>
						<td class="text_10" style="width:155px; text-align:center" bgcolor="#ffffff">'.$count.'</td>
						<td class="text_10" style="width:600px; text-align:left" bgcolor="#ffffff">&nbsp;</td>
					</tr>';
				}
				$total_count+=$cnt;
				$page_content.='
				<tr><td colspan="3" style="height: 2px; padding: 0px; background: #009933;"></td></tr>
				<tr>
					<td class="text_10" style="width:305px; text-align:left" bgcolor="#ffffff"><strong>Total: </strong></td>
					<td class="text_10" style="width:155px; text-align:center" bgcolor="#ffffff"><strong>'.$cnt.'</strong></td>
					<td class="text_10" style="width:600px; text-align:left" bgcolor="#ffffff">&nbsp;</td>
				</tr>
				<tr><td colspan="3" style="height: 2px; padding: 0px; background: #009933;"></td></tr>';

			}
			$page_content.='
			<tr><td colspan="3" style="height: 2px; padding: 0px; background: #009933;"></td></tr>
			<tr>
				<td class="text_10" style="text-align:left" bgcolor="#ffffff" colspan="3"><strong>Total Patients: '.$total_count.'</strong></td>
			</tr>
			<tr><td colspan="3" style="height: 2px; padding: 0px; background: #009933;"></td></tr>';
		}
	}
	
	//MR BREAKDOWN COMMON FOR SUMMARY AND DETAILS
	$mr_breakdown_html='';
	if(sizeof($arr_mr_breakdown)>0){
		$total_mr= count($arr_mr_breakdown['MR1']) + count($arr_mr_breakdown['MR2']) + count($arr_mr_breakdown['MR3']);
		$total_given= count($arr_mr_given['MR1']) + count($arr_mr_given['MR2']) + count($arr_mr_given['MR3']);
		$mr_breakdown_html='
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" style="width:100%">
			<tr><td class="text_10" style="width:100%;" colspan="6" bgcolor="#ffffff">&nbsp;</td></tr>
			<tr><td class="text_b_w" style="width:100%;" colspan="6">MR Breakdown</td></tr>
			<tr>
				<td class="text_b_w" style="width:8%;">&nbsp;</td>
				<td class="text_b_w" style="width:8%; text-align:center">MR1</td>
				<td class="text_b_w" style="width:8%; text-align:center">MR2</td>
				<td class="text_b_w" style="width:8%; text-align:center">MR3</td>
				<td class="text_b_w" style="width:8%; text-align:center">Total</td>
				<td class="text_b_w" style="width:60%;">&nbsp;</td>
			</tr>
			<tr>
				<td class="text_10" style="text-align:right" bgcolor="#ffffff">Count:&nbsp;</td>
				<td class="text_10" style="text-align:right" bgcolor="#ffffff">'.count($arr_mr_breakdown['MR1']).'&nbsp;</td>
				<td class="text_10" style="text-align:right" bgcolor="#ffffff">'.count($arr_mr_breakdown['MR2']).'&nbsp;</td>
				<td class="text_10" style="text-align:right" bgcolor="#ffffff">'.count($arr_mr_breakdown['MR3']).'&nbsp;</td>
				<td class="text_10" style="text-align:right" bgcolor="#ffffff"><strong>'.$total_mr.'</strong>&nbsp;</td>
				<td class="text_10" style="text-align:left" bgcolor="#ffffff">&nbsp;</td>
			</tr>
			<tr>
				<td class="text_10" style="text-align:right" bgcolor="#ffffff">Given Count:&nbsp;</td>
				<td class="text_10" style="text-align:right" bgcolor="#ffffff">'.count($arr_mr_given['MR1']).'&nbsp;</td>
				<td class="text_10" style="text-align:right" bgcolor="#ffffff">'.count($arr_mr_given['MR2']).'&nbsp;</td>
				<td class="text_10" style="text-align:right" bgcolor="#ffffff">'.count($arr_mr_given['MR3']).'&nbsp;</td>
				<td class="text_10" style="text-align:right" bgcolor="#ffffff"><strong>'.$total_given.'</strong>&nbsp;</td>
				<td class="text_10" style="text-align:left" bgcolor="#ffffff">&nbsp;</td>
			</tr>
			<tr><td colspan="6" style="height: 2px; padding: 0px; background: #009933;"></td></tr>
		</table>';		
	}
}

if(trim($page_content) != ""){
	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$PdfText = $stylePDF.'
		<page backtop="16mm" backbottom="5mm">
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
		<page_header>
			<table class="rpt rpt_table rpt_table-bordered rpt_padding">
				<tr class="rpt_headers">
					<td class="rptbx1" style="width:350px;">Clinical Productivity Report ('.ucfirst($report_view).')</td>
					<td class="rptbx2" style="width:350px;">From : '.$Start_date.'  To : '.$End_date.'</td>
					<td class="rptbx3" style="width:350px;">Created by '.$report_generator_name.'  on  '.$curDate.'</td>
				</tr>
				<tr class="rpt_headers">
					<td class="rptbx1" style="width:350px;">Selected Users : '.$selUsers.'</td>
					<td class="rptbx2" style="width:350px;">Selected Options : '.$selClinicalOpts.'</td>
					<td class="rptbx3" style="width:350px;"></td>
				</tr>
			</table>
			'.$header_part.'
		</page_header>		
		<table class="rpt rpt_table rpt_table-bordered rpt_padding">'
			.$page_content.
		'</table>
		'.$mr_breakdown_html.'
		</page>';
		
		//PAGE DATA
		$page_data=
		'<table class="rpt rpt_table rpt_table-bordered rpt_padding">
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:350px;">Clinical Productivity Report ('.ucfirst($report_view).')</td>
				<td class="rptbx2" style="width:350px;">From : '.$Start_date.'  To : '.$End_date.'</td>
				<td class="rptbx3" style="width:350px;">Created by '.$report_generator_name.'  on  '.$curDate.'</td>
			</tr>
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:350px;">Selected Users : '.$selUsers.'</td>
				<td class="rptbx2" style="width:350px;">Selected Options : '.$selClinicalOpts.'</td>
				<td class="rptbx3" style="width:350px;"></td>
			</tr>	
		</table>
		'.$header_part.'
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">'
			.$page_content.
		'</table>
		'.$mr_breakdown_html;
		$printFile = 1;
		$file_location = write_html($PdfText);
	echo $page_data;
} else{
	echo '<div class="text-center alert alert-info">No Record Found.</div>';
}
?>