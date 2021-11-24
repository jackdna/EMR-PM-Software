<?php
$noRecord = false;
$report_generator_name = "";
$global_date_format = phpDateFormat();

if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
	$op_name_arr = preg_split("/, /",$_SESSION["authProviderName"]);
	$report_generator_name = $op_name_arr[1][0];
	$report_generator_name .= $op_name_arr[0][0];
}

if($_POST['form_submitted']<>""){
	$curDate = get_date_format(date('Y-m-d'))." ".date('h:i A');	
	//list($m,$d,$y) = explode('-',$_POST['rep_date_dy']);
	//$_POST['rep_date_dy'] = $y.'-'.$m.'-'.$d.'|'.$y.'-'.$m.'-'.$d;  
	switch($_POST['rep_type']){
		case "1":
			$_POST['rep_date_dy']=getDateFormatDB($_POST['rep_date_dy']);
			$_POST['rep_date_dy']=$_POST['rep_date_dy'].'|'.$_POST['rep_date_dy'];
			$dts_arr=explode("|",$_POST['rep_date_dy']);	
			$rep_type="Date";  	  
			break;    
		case "2":
			$dts_arr=explode("|",$_POST['rep_date_wk']);	  	  
			$rep_type="Weekly";
			break;
		case "3":
			$dts_arr=explode("|",$_POST['rep_date_mn']);	  	  
			$rep_type="Monthly";
			break;	
		case "4":
			$dts_arr=explode("|",$_POST['rep_date_qa']);
			$rep_type="Quarterly";
			break;	
		case "5":
			$dts_arr=explode("|",$_POST['rep_date_ya']);	  	  
			$rep_type="Yearly";
			break;
		default : break;
	}
	
	$dts_st=$dts_arr[0];
	$dts_en=$dts_arr[1];
	
	$pos_facility_id = implode(',',$facility_name);	
	$provider_id = implode(',',$phyId);

	//GET FROM MAIN DATA
	if($pos_facility_id){
		$fac_whr=" and sa.sa_facility_id IN (".$pos_facility_id.")";
	}
	if($provider_id){
		$prov_whr=" and sa.sa_doctor_id IN (".$provider_id.")";
	}
	$qry = "SELECT sa.id, sa.sa_patient_id,sa.sa_patient_name,sa.sa_patient_app_status_id AS sa_patient_app_status_id, sa.procedureid,
	DATE_FORMAT(sa.sa_app_start_date, '".get_sql_date_format()."') as 'sa_app_start_date', sa.case_type_id, sp.proc, sp.acronym,
	u.fname, u.lname , sa.sa_doctor_id, pd.primary_care  
	FROM schedule_appointments sa 
	LEFT JOIN slot_procedures sp ON sp.id = sa.procedureid 
	LEFT JOIN patient_data pd ON pd.id = sa.sa_patient_id 
	LEFT JOIN users u ON u.id = sa.sa_doctor_id 
	WHERE (sa.sa_app_start_date BETWEEN '".$dts_st."' AND '".$dts_en."') 
	AND sa.sa_patient_app_status_id NOT IN (203) 
	$fac_whr
	$prov_whr 
	ORDER BY u.lname,u.fname, sp.proc, sa.sa_patient_name"; //echo $qry;
	$rs=imw_query($qry);
	while($res = imw_fetch_array($rs)){
		$schId = $res['id'];
		$docId= $res['sa_doctor_id'];
		$arrSchData[$docId][$schId]['proc'] = $res['proc'];
		$arrSchData[$docId][$schId]['patient_id'] = $res['sa_patient_id'];
		$arrSchData[$docId][$schId]['patient_name'] = $res['sa_patient_name'];
		$arrSchData[$docId][$schId]['apptDate'] = $res['sa_app_start_date'];
		$arrSchData[$docId][$schId]['status'] = $res['sa_patient_app_status_id'];
		$arrSchData[$docId][$schId]['primary_care'] = $res['primary_care'];		
		$arrSchData[$docId][$schId]['case_type_id'] = $res['case_type_id'];
		$arrDocName[$docId]= $res['lname'].', '.$res['fname'];
		
			
		//SUMMARY
		$arrSchSummary[$docId]['all'][$schId]=$schId;	
		if($res['sa_patient_app_status_id']==18){
			$arrSchSummary[$docId]['cancel'][$schId]=$schId;
		}else if($res['sa_patient_app_status_id']==3){
			$arrSchSummary[$docId]['noshow'][$schId]=$schId;	
		}else if($res['sa_patient_app_status_id']==202 || $res['sa_patient_app_status_id']==201){
			$arrSchSummary[$docId]['resch'][$schId]=$schId;
			$arrTempIds[$schId]=$schId;
		}
		
	}unset($rs);

	//GET RESCHEDULED FOR FUTURE DATES
	$qryPart='';
	if(sizeof($arrTempIds)>0){
		$strTempIds = implode(',', $arrTempIds);
		$qryPart=" AND pr.sch_id NOT In(".$strTempIds.")";
	}
	$qry="Select pr.sch_id, pr.patient_id, DATE_FORMAT(pr.old_date, '".get_sql_date_format()."') as 'old_date', pr.old_provider,  DATE_FORMAT(pr.new_appt_date, '".get_sql_date_format()."') as 'new_appt_date',
	sp.proc, pd.fname,pd.mname,pd.lname, primary_care, u.fname as 'dFname', u.lname as 'dLname'    
	FROM previous_status pr LEFT JOIN slot_procedures sp ON sp.id = pr.old_procedure_id 
	LEFT JOIN patient_data pd ON pd.id = pr.patient_id 
	LEFT JOIN users u ON u.id = pr.old_provider 
	WHERE (old_date BETWEEN '".$dts_st."' AND '".$dts_en."') AND pr.status='202' AND new_appt_date > '".$dts_en."' 
	".$qryPart;
	if($pos_facility_id){
		$qry.=" and pr.old_facility IN (".$pos_facility_id.")";
	}
	if($provider_id){
		$qry.=" and pr.old_provider IN (".$provider_id.")";
	}
	$qry.=" GROUP BY pr.sch_id ORDER BY u.lname, u.fname,sp.proc, pd.lname, pd.fname";	
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$schId = $res['sch_id'];
		$docId= $res['old_provider'];
		
		$pat_name_arr = array();
		$pat_name_arr["LAST_NAME"] = $res['lname'];
		$pat_name_arr["FIRST_NAME"] = $res['fname'];
		$pat_name_arr["MIDDLE_NAME"] = $res['mname'];
		$pat_name = changeNameFormat($pat_name_arr);

		$arrSchData[$docId][$schId]['proc'] = $res['proc'];
		$arrSchData[$docId][$schId]['patient_id'] = $res['patient_id'];
		$arrSchData[$docId][$schId]['patient_name'] = $pat_name;
		$arrSchData[$docId][$schId]['apptDate'] = $res['old_date'];
		$arrSchData[$docId][$schId]['status'] = '202';
		$arrSchData[$docId][$schId]['status_date'] = $res['new_appt_date'];
		$arrSchData[$docId][$schId]['primary_care'] = $res['primary_care'];
		$arrDocName[$docId]= $res['dLname'].', '.$res['dFname'];

		//SUMMARY
		$arrSchSummary[$docId]['all'][$schId]=$schId;	
		$arrSchSummary[$docId]['resch'][$schId]=$schId;	
		
		$arrReSchIds[$schId]=$schId;
	}unset($rs);
	//---------------------	


	//GET RESCHEDULED APPTS CASE TYPE IDS
	if(sizeof($arrReSchIds)>0){
		$strReSchIds = implode(',', $arrReSchIds);
		$qry="Select id, sa_doctor_id, case_type_id FROM schedule_appointments WHERE id IN(".$strReSchIds.")";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$schId = $res['id'];
			$docId= $res['sa_doctor_id'];

			$arrSchData[$docId][$schId]['case_type_id'] = $res['case_type_id'];			
		}unset($rs);
	}
	

	$dispDateFrom= date($global_date_format, strtotime($dts_st));
	$dispDateTo=date($global_date_format, strtotime($dts_en));

	$cssHTML ='
	<style>'.file_get_contents('css/reports_pdf.css').'</style>
	
	<page backtop="5mm" backbottom="5mm">			
	<page_footer>
		<table style="width:100%">
			<tr>
				<td style="text-align:center;width:100%;">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>';
	$strHTML ='';
	if(count($arrSchSummary) > 0){	 

		if($process!="Detail"){ //SUMMARY
		$strHTML .='
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
            <tr class="rpt_headers">
				<td class="rptbx1" style="width:350px;" align="left">
					Provider Report Summary
				</td>
				<td class="rptbx2" style="width:350px;" align="left">
					Type of Report: '.$rep_type.' (From '.$dispDateFrom.' To '.$dispDateTo.')
				</td>
				<td class="rptbx3" style="width:350px;" align="left">
					Created by '.$report_generator_name.'  on  '.$curDate.'
				</td>	
			</tr>
		</table>	
		<table class="rpt rpt_table rpt_table-bordered" width="100%" bgcolor="#FFF3E8">	
			<tr height="20">
				<td align="left" width="175" class="text_b_w">Provider</td>				
				<td align="left" width="175" class="text_b_w">Total Count</td>
				<td align="left" width="175" class="text_b_w">No Shows</td>
				<td align="left" width="175" class="text_b_w">Re-Scheduled</td>
				<td align="left" width="175" class="text_b_w">Cancelled</td>
				<td align="left" width="175" class="text_b_w">Actual Seen</td>
			</tr>';			
		foreach($arrSchSummary as $docId => $arrDet){			

			$actual_seen= $totDocAppts = $resch = $noshow = $cancel ="";
			$totDocAppts = count($arrDet['all']);
			$resch = count($arrDet['resch']);
			$noshow = count($arrDet['noshow']);
			$cancel = count($arrDet['cancel']);

			$actual_seen=$totDocAppts - ($resch + $noshow + $cancel);
			$strHTML .='<tr>
				<td align="left" width="175" class="text_12" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$arrDocName[$docId].'</td>		
				<td align="left" width="175" class="text_12" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$totDocAppts.'</td>
				<td align="left" width="175" class="text_12" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$noshow.'</td>
				<td align="left" width="175" class="text_12" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$resch.'</td>
				<td align="left" width="175" class="text_12" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$cancel.'</td>
				<td align="left" width="175" class="text_12" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$actual_seen.'</td>
			</tr>';	  
		}
		$strHTML .='</table>
		</page>';
		
		}else{ //DETAIL
		$strHTML ='
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
            <tr class="rpt_headers">
				<td class="rptbx1" style="width:350px;" align="left">
					Provider Report Details
				</td>
				<td class="rptbx2" style="width:350px;" align="left">
					Type of Report: '.$rep_type.' (From '.$dispDateFrom.' To '.$dispDateTo.')
				</td>
				<td class="rptbx3" style="width:350px;" align="left">
					Created by '.$report_generator_name.'  on  '.$curDate.'
				</td>	
			</tr>
		</table>	
		<table class="rpt rpt_table rpt_table-bordered" width="100%" bgcolor="#FFF3E8">';

		foreach($arrSchSummary as $docId => $arrDet){
			$strHTML .='
			<tr>
				<td colspan="7" align="left" class="text_b_w">Provider : '.$arrDocName[$docId].'</td>				
			</tr>	
			<tr>
				<td align="left" style="width:150px;" class="text_b_w">Procedure Name</td>				
				<td align="left" style="width:150px;" class="text_b_w">Patient Name - ID</td>
				<td align="left" style="width:150px;" class="text_b_w">Date Sched.</td>
				<td align="left" style="width:150px;" class="text_b_w">Status</td>
				<td align="left" style="width:150px;" class="text_b_w">Date&nbsp;Re-Sched.</td>
				<td align="left" style="width:150px;" class="text_b_w">Co-Pay </td>
				<td align="left" style="width:150px;" class="text_b_w">Ref. Physician</td>
			</tr>';
			
			foreach($arrSchData[$docId] as $apptId => $apptData){

				$status_date = '';
				$status_name='';
				if($apptData['status'] == 202 || $apptData['status'] == 201){
					$status_name="Reschedule";
					$status_date = $apptData['status_date'];
				}else if($apptData['status'] == 3){
					$status_name="No Show";
				}else if($apptData['status'] == 18){
					$status_name="Cancelled";
				}

				//gettting copay
				$int_pt_copay = re_get_pt_copay($apptData['patient_id'], $apptData['case_type_id']);

				$strHTML .='<tr class="text_10" height="20" bgcolor="#FFFFFF">
					<td class="text_12" style="width:150px;" align="left" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$apptData['proc'].'</td>				
					<td class="text_12" style="width:150px;" align="left" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$apptData['patient_name'] .' - '.$apptData['patient_id'].'</td>
					<td class="text_12" style="width:150px;" align="left" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$apptData['apptDate'].'</td>
					<td class="text_12" style="width:150px;" align="left" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$status_name.'</td>
					<td class="text_12" style="width:150px;" align="left" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$apptData["status_date"].'</td>
					<td class="text_12" style="width:150px;" align="left" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.numberFormat($int_pt_copay,2,'yes').'</td>
					<td class="text_12" style="width:150px;" align="left" bgcolor="#FFFFFF">'.$apptData["primary_care"].'</td>
				</tr>';
			}

			$totDocAppts = count($arrDet['all']);
			$resch = count($arrDet['resch']);
			$noshow = count($arrDet['noshow']);
			$cancel = count($arrDet['cancel']);
			$actual_seen=$totDocAppts - ($resch + $noshow + $cancel);
			
			$strHTML .='									
			<tr class="text_10" height="20">
				<td colspan="7" align="left" class="text_10b" bgcolor="#FFFFFF">
					Total Patient: '.$totDocAppts.',&nbsp;
					Re-Sched : '.$resch.'
					,&nbsp;No Show : '.$noshow.'
					,&nbsp;Cancel : '.$cancel.'
					</td>				
			</tr>
			<tr class="text_10" height="20">
				<td colspan="7" align="left" class="text_10b" bgcolor="#FFFFFF">Actual Patient Seen : '.$actual_seen.'</td>				
			</tr>';
			}
			$strHTML .='</table></page>';
		}
	}
}


if(trim($strHTML) != "" && $arrSchSummary>0){
	$PdfText = $cssHTML.$strHTML;
	$printFile = 1;
	$file_location = write_html($PdfText);
	$styleHTML = '<style>' . file_get_contents('css/reports_html.css') . '</style>';
	echo $csv_file_data = $styleHTML . $strHTML;
} else{
	echo '<div class="text-center alert alert-info">No record found.</div>';
}
?>
