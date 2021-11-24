<?php
//Function Files
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
require_once('CLSSchedulerReports.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

if($_REQUEST['form_submitted']){
	$printDate = $_REQUEST['Start_date'];
	list($m,$d,$y) = explode('-',$_REQUEST['Start_date']);	
	$selected_date = $y.'-'.$m.'-'.$d;
	$sort_by = 'sa_doctor_id';
	
	$strFacIds = implode(",", $arrFacIds);
	$rep_fac_str= implode(",",$_REQUEST['facility_name']);
	
	if($rep_fac_str =='')
	{
		$rep_fac_str = $strFacIds;
	}
	
	$rep_doc_str= implode(",",$_REQUEST['phyId']);
	if($rep_doc_str =='')
	{
	$rep_doc_arr = $CLSCommonFunction->provider_Ids('','1','');
		$rep_doc_str = implode(",", $rep_doc_arr);
	}
	
	$curDate = date($phpDateFormat.' h:i A');
	//getting report generator name
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$createdBy = ucfirst(trim($op_name_arr[1][0]));
	$createdBy .= ucfirst(trim($op_name_arr[0][0]));
	
	$resAppts = sr_get_day_report_records($rep_fac_str, $selected_date, $sort_by, $rep_doc_str);
	$intTotRec = count($resAppts);	 	//total appointments


	//MAKING OUTPUT DATA
	$file_name="Day_Report.csv";
	$csv_file_name= write_html("", $file_name);
	if(file_exists($csv_file_name)){
		unlink($csv_file_name);
	}
	$fp = fopen ($csv_file_name, 'a+');
	$arr=array();
	$arr[]="Facility";
	$arr[]="Provider Name";
	$arr[]="Check in";
	$arr[]="Check out";
	$arr[]="Patient Name";
	$arr[]="Co-Pay";
	$arr[]="Ref. Physician";
	$arr[]="Comments";
	$arr[]="Operator";
	$arr[]="Status";
	fputcsv($fp,$arr, ",","\"");
	
	//html begin
	$intTempProviderId = 0;		//to carry forward last provider id in loop
	$intTempFacilityId = 0;		//to carry forward last facility id in loop
	$intSerialNo = 0;			//to show serial number per record per provider
	$totCopy = (int)$totCopy;
	if($intTotRec > 0){
		$strHTML='<style>'.file_get_contents('css/reports_pdf.css').'</style>';
		$strHTML .= '
			<page backtop="17mm" backbottom="10mm">			
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>';
		for($i = 0; $i < $intTotRec; $i++){	
			
			//appCheckIn
			$appCheckIn = "N/A";
			$arrCI = sr_get_appt_prev_status_time($resAppts[$i]['appt_id'], 13, $selected_date);
			if(count($arrCI) > 0){
				$appCheckIn = $arrCI[0]['appCheckTime'];
			}
			
			//appCheckedOut	
			$appCheckedOut = "N/A";
			$arrCO = sr_get_appt_prev_status_time($resAppts[$i]['appt_id'], 11, $selected_date);
			if(count($arrCO) > 0){
				$appCheckedOut = $arrCO[0]['appCheckTime'];	
			}
			
			//co pay
			$copay = $resAppts[$i]['copay'];
			
			// CHANGE Currency Format
			$copay_val = numberFormat($copay,2,'yes','','yes');
			//if($copay_val =='') { $copay_val ='$0.00'; }
			//patient name
			$patientName = $CLSReports->re_change_name_format($resAppts[$i]['patientFName'], $resAppts[$i]['patientLName']);			
			
			//operator
			$strOperatorNm = $CLSReports->re_change_name_format($resAppts[$i]['operatorFName'], $resAppts[$i]['operatorLName']);	
							
			//status
			$strAppStatus = sr_get_appt_status_name($resAppts[$i]['sa_patient_app_status_id']);
			
			//new page for every provider
			if($i == 0 || ($intTempProviderId != $resAppts[$i]['sa_doctor_id'] || $intTempFacilityId != $resAppts[$i]['sa_facility_id'])){			
				if($intSerialNo>0){
					$intTempSerialNo = $intSerialNo;
					$newTotCopy = $totCopy;
				}else{
					$intTempSerialNo = 0;
					$newTotCopy = 0;
				}
				$intSerialNo = 0;			//resetting record serial no counter
				$totCopy = 0;
				if(!$newTotCopy)
					$newTotCopy = 0;
				
				$newTotCopy = numberFormat($newTotCopy,2,'yes','','yes');
				//if($newTotCopy=='') { $newTotCopy = '$0.00'; }
				if($i>0){
							
					$strHTML .= '<tr>
									<td colspan="9">&nbsp;</td>
								 </tr>
								 <tr>
									<td align="left" class="text" colspan="3"> <b>Total Appointment(s): '.$intTempSerialNo.'</b> </td>
									<td align="right" class="text" colspan="2"><b>Total Copay: '.$newTotCopy.'</b></td>
									<td align="right" class="text" colspan="4">&nbsp;</td>
								 </tr>										 
								</table>
							</page>
							<page pageset="old">
						';

					$page_data .= '<tr class="white">
									<td colspan="9" >&nbsp;</td>
								 </tr>
								 <tr class="white">
									<td align="left " class="text" colspan="3"> <b>Total Appointment(s): '.$intTempSerialNo.'</b> </td>
									<td align="right" class="text" colspan="2"><b>Total Copay: '.$newTotCopy.'</b></td>
									<td align="right" class="text" colspan="4">&nbsp;</td>
								 </tr>										 
								</table>
						';

					$pageHeaderStart='<page_header>';
					$pageHeaderEnd='</page_header>';										
				}
				//provider(physician) name
				$physicianName = $CLSReports->re_change_name_format($resAppts[$i]['physicianFName'], $resAppts[$i]['physicianLName']);
				if($i==0) {
					$page_data .= '<table class="rpt_table rpt rpt_table-bordered rpt_padding">
						<tr>	
							<td class="rptbx1" style="width:33%">'.$firstFacName.'</td>
							<td  class="rptbx2" style="text-align:left; width:33%">Day Reports for Date : '.$_REQUEST['Start_date'].'</td>
							<td class="rptbx3" style="text-align:left; width:33%">Created By: '.$createdBy.' on '.$curDate.'&nbsp;</td>
						</tr>
					</table>';
				}
				
				$page_data.='<table class="rpt_table rpt rpt_table-bordered rpt_padding">
							<tr>
								<td class="text_b_w" style="width:33%">'.$resAppts[$i]['facilityName'].'</td>
								<td class="text_b_w" style="width:66%">Provider Name: '.$physicianName.'</td>						
							</tr>
							</table>';
				
				$page_data.='<table class="rpt_table rpt rpt_table-bordered rpt_padding">
							<tr>
								<td class="text_b_w" style="text-align:center;" width="30">Appt.</td>
								<td class="text_b_w" style="text-align:center;" width="60">Check in</td>				
								<td class="text_b_w" style="text-align:center;"  width="75">Check out</td>
								<td class="text_b_w" style="text-align:center;" width="140">Patient Name</td>
								<td class="text_b_w" style="text-align:center;" width="55">Co-Pay</td>
								<td class="text_b_w" style="text-align:center;" width="100">Ref. Physician</td>
								<td class="text_b_w" style="text-align:center;" width="150">Comments</td>
								<td class="text_b_w" style="text-align:center;" width="120">Operator</td>
								<td class="text_b_w" style="text-align:center;" width="120">Status</td>
							</tr>
							';
				
				$strHTML .= '
					<page_header>
					<table class="rpt_table rpt rpt_table-bordered rpt_padding">
						<tr>	
							<td class="rptbx1" style="width:320px;">'.$firstFacName.'</td>
							<td  class="rptbx2" style="text-align:left; width:400px;">Day Reports for Date : '.$_REQUEST['Start_date'].'</td>
							<td class="rptbx3" style="text-align:left; width:320px;">Created By: '.$createdBy.' on '.$curDate.'&nbsp;</td>
						</tr>
					</table>
					<table class="rpt_table rpt rpt_table-bordered rpt_padding">
						<tr>
							<td class="text_b_w" align="left" colspan="4">Facility: '.$resAppts[$i]['facilityName'].'</td>							
							<td class="text_b_w" align="center" colspan="3" >Provider Name: '.$physicianName.'</td>							
							<td class="text_b_w" align="right" colspan="2" >Date: '.$_REQUEST['Start_date'].'</td>
						</tr>						
						<tr>
							<td class="text_b_w" width="30" align="left">Appt.</td>
							<td class="text_b_w" width="65" align="center">Check in</td>				
							<td class="text_b_w" width="80" align="center">Check out</td>
							<td class="text_b_w" width="120" align="center">Patient Name</td>
							<td class="text_b_w" width="55" align="center">Co-Pay</td>
							<td class="text_b_w" width="160" align="center">Ref. Physician</td>
							<td class="text_b_w" width="165" align="center">Comments</td>
							<td class="text_b_w" width="170" align="center">Operator</td>
							<td class="text_b_w" width="120" align="center">Status</td>
							
						</tr>						
					</table>
					</page_header>
					<table class="rpt_table rpt rpt_table-bordered rpt_padding">
				';
			}		
							
			$intSerialNo++;
			$arr=array();
			$arr[]=$resAppts[$i]['facilityName'];
			$arr[]=$physicianName;
			$arr[]=$appCheckIn;
			$arr[]=$appCheckedOut;
			$arr[]=$patientName;
			$arr[]=$copay_val;
			$arr[]=$resAppts[$i]['refPhysician'];
			$arr[]=addslashes($resAppts[$i]['comments']);
			$arr[]=ucwords($strOperatorNm);
			$arr[]=$strAppStatus;
			fputcsv($fp,$arr, ",","\"");	
			
			$page_data .= '			
						<tr valign="top">
							<td style="text-align:left; width="30">&nbsp;'.($intSerialNo).'</td>
							<td style="text-align:center; width="65">'.$appCheckIn.'</td>				
							<td style="text-align:center; width="78">'.$appCheckedOut.'</td>
							<td class="text alignLeft white" width="150">&nbsp;'.$patientName.'</td>
							<td class="text alignCenter white" width="54">'.$copay_val.'</td>		
							<td class="text alignLeft white" width="108">&nbsp;'.$resAppts[$i]['refPhysician'].'</td>
							<td class="text alignLeft white" width="150">&nbsp;'.addslashes($resAppts[$i]['comments']).'</td>							
							<td class="text alignLeft white" width="120">&nbsp;'.ucwords($strOperatorNm).'</td>
							<td class="text alignLeft white" width="125">&nbsp;'.$strAppStatus.'</td>
						</tr>						
						';

			$strHTML .= '			
						<tr valign="top">
							<td class="text" width="30" align="left">&nbsp;'.($intSerialNo).'</td>
							<td class="text" width="65" align="center">'.$appCheckIn.'</td>				
							<td class="text" width="80" align="center">'.$appCheckedOut.'</td>
							<td class="text" width="120" align="left">'.$patientName.'</td>
							<td class="text" width="55" align="left">'.$copay_val.'</td>		
							<td class="text" width="160" align="left">'.$resAppts[$i]['refPhysician'].'</td>
							<td class="text" width="165" align="left">'.addslashes($resAppts[$i]['comments']).'</td>							
							<td class="text" width="170" align="left">'.ucwords($strOperatorNm).'</td>
							<td class="text" width="120" align="center">'.$strAppStatus.'</td>
						</tr>						
						';


			$intTempProviderId = $resAppts[$i]['sa_doctor_id'];
			$intTempFacilityId = $resAppts[$i]['sa_facility_id'];
			$copay = (int)$copay;
			
			$totCopy = $totCopy+$copay;
		}
		fclose($fp);

		if($intSerialNo>0){
			$intTempSerialNo=$intSerialNo;
			$newTotCopy = $totCopy;
		}else{
			$intTempSerialNo = 0;
			$newTotCopy = 0;
		}
		if(!$newTotCopy)
			$newTotCopy = 0;
		
		$newTotCopy = numberFormat($newTotCopy,2,'yes','','yes');
		//if($newTotCopy=='') { $newTotCopy = '$0.00'; }
		
		$strHTML .= '<tr>
						<td colspan="9" class="white">&nbsp;</td>
					 </tr>
					 <tr class="white">
						<td align="left" class="text" colspan="3"> <b>Total Appointment(s): '.$intTempSerialNo.'</b> </td>
						<td align="right" class="text" colspan="2"><b>Total Copay: '.$newTotCopy.'</b></td>
						<td align="right" class="text" colspan="4">&nbsp;</td>
					 </tr>
					 ';
					
		$strHTML .= '</table></page>';
	
		$printPdFBtn = 1;
		$file_location = write_html($strHTML);
		
		$page_data .= '<tr class="white">
						<td colspan="9">&nbsp;</td>
					 </tr>
					 <tr class="white">
						<td align="left" class="text" colspan="3"> <b>Total Appointment(s): '.$intTempSerialNo.'</b> </td>
						<td align="right" class="text" colspan="2"><b>Total Copay: '.$newTotCopy.'</b></td>
						<td align="left" class="text" colspan="4">&nbsp;</td>
					 </tr></table>
					 ';		
	}
}
$showbtn  = 0;
if($page_data){
	echo $page_data;
	$showbtn  = 1;
	
}else{
	echo '<div class="text-center alert alert-info">No Record Found.</div>';
}
?>