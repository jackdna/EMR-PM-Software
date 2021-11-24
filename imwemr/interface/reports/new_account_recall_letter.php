<?php
require_once '../../config/globals.php';
require_once '../../library/classes/pnTempParser.php';
require_once("../../library/classes/Functions.php");
include_once($GLOBALS['fileroot'] . '/library/classes/scheduler/appt_page_functions.php');

$objParser = new PnTempParser;
$objManageData=new ManageData;
$pat_id_imp=$pat_for_sch_imp;
$recallTemplatesListId=$_REQUEST['recallTemplatesListId'];
$pgPL = "p";
$marginType = "recall";
$is_json = ( isset($_REQUEST['json']) && $_REQUEST['json'] == 'true' ) ? true : false;

//show Page numbers on pdf or not
$showPageInfo = "" ;
//$showPageInfo = "Page [[page_cu]]/[[page_nb]]" ;


//pre($pat_for_sch_appt_arr);die();
function getApptRecall($patientID,$searchDate){
	$arrAppRecall=array();
	if($patientID){
		$qryApptRecall="select procedure_id,descriptions from patient_app_recall where patient_id='".$patientID."' AND recalldate >='".$searchDate."' ORDER BY recalldate asc  LIMIT 0,1";
		$resApptRecall=imw_query($qryApptRecall)or die(imw_error().$qryApptRecall);
		$rowApptRecall=imw_fetch_assoc($resApptRecall);
		if($rowApptRecall['procedure_id']){
			$procedureName=getProcedureName($rowApptRecall['procedure_id']);
		}
		$arrAppRecall[0]=$procedureName;
		$arrAppRecall[1]=$rowApptRecall['descriptions'];
	}
	return $arrAppRecall;
}

if($report_type=="Post Card"){
	$paddingTop = "80";
	$paddingLeft= "90";
	$marginType = "post_card";
	$backTop 	= "0mm";
	$backBottom = "0mm";
	$backLeft 	= "0mm";
	$backRight 	= "2mm";
	$fontSize 	= "12px";
	$centerHorizontalGap= "0px";
	$centerVerticalGap 	= "0px";
	$pgPL = "l";
	$templQry = "select pt_docs_template_name, pt_docs_template_content, pt_docs_template_enable_facesheet from pt_docs_template 
				where pt_docs_template_status = '0' and pt_docs_template_enable_facesheet='postcard'";
	$templRes = @imw_query($templQry) or die(imw_error());
	$templNum = @imw_num_rows($templRes);
	if($templNum>0) {
		$templRow = imw_fetch_array($templRes);
		$template_content = $templRow["pt_docs_template_content"];	
	}
}

//setting margins
$sql_margin=imw_query("select * from create_margins where margin_type='".$marginType."'");
$row_margin=imw_fetch_array($sql_margin);
$top_margin = $row_margin['top_margin'];
$bottom_margin = $row_margin['bottom_margin'];
$line_margin = $row_margin['line_margin'];
$coloumn_margin = $row_margin['column_margin'];
$left_margin = $row_margin['left_margin'];

if($report_type=="Post Card"){
	if(trim($top_margin)) 	{ $paddingTop 	= $top_margin;}
	if(trim($left_margin)) 	{ $paddingLeft 	= $left_margin;}	
}

//flag to set create label/house call on off (by default its off)
if($recallTemplatesListId) {
	$recallTemplateData		= '';
	$recallTemplateQry 		= "SELECT * FROM recalltemplate WHERE recallLeter_id='".$recallTemplatesListId."'";
	$recallTemplateRes 		= @imw_query($recallTemplateQry);
	$recallTemplateNumRow 	= @imw_num_rows($recallTemplateRes);
	if($recallTemplateNumRow>0) {
		$recallTemplateRow 	= @imw_fetch_array($recallTemplateRes);
		$recallTemplateData = stripslashes($recallTemplateRow['recallTemplateData']);	
	}
}

$qry = "SELECT *  FROM patient_data
		where id in ($pat_id_imp)
		group by id 
		ORDER BY lname asc,fname asc ";
	
$res = @imw_query($qry) or die(imw_error());
$num = @imw_num_rows($res);

//qery to get patient last no show marked appointment
$last_no_show=array();
$ns_res = imw_query("select sa_patient_id, DATE_FORMAT(sa_app_start_date, '".get_sql_date_format()."') as apptDate, 
			  TIME_FORMAT(sa_app_starttime, '%h:%i %p') as starttime from schedule_appointments 
			  WHERE sa_patient_app_status_id=3 and sa_patient_id IN ($pat_id_imp) order by sa_app_start_date ASC") or die(imw_error());
while($ns_rec=imw_fetch_object($ns_res))
{
	$last_no_show[$ns_rec->sa_patient_id]=$ns_rec->apptDate.' '.$ns_rec->starttime;
}

ob_start();
if($num > 0){
	if($report_type=="Post Card"){
		if($templNum<=0) {
			$num=0;
			$msgDisp = "Please set template as Post Card in Admin";
		}
		if($templNum >0) {
			
			$strHTML = NULL;
			$strHTML .= <<<DATA
				<style type="text/css">
					.text_b_w{
						font-size:11px;
						font-family:Arial, Helvetica, sans-serif;
						font-weight:bold;
						color:#000000;
						background-color:#BCD5E1;
						border-style:solid;
						border-color:#FFFFFF;
						border-width: 1px; 
					}
				</style>
DATA;
			$aa=0;
			$bb=0;
			while($rw = @imw_fetch_array($res)){
				$aa++;
				$bb++;
				$patientid		= $rw['id'];
				$template_content = trim(html_entity_decode(stripslashes($template_content)));
				$templateData 	= $objManageData->__loadTemplateData($template_content,$rw);	
				//echo $templateData;
				$paddingTopShow = $paddingTop;
				$paddingLeftShow = $paddingLeft;
				if($aa!=1) {
					if($bb==3) { $paddingTopShow = $paddingTopShow+55; }
					if($bb==4) { $paddingTopShow = $paddingTopShow+55; }
					//if($bb==2 || $bb==4) { $paddingLeftShow = $paddingLeftShow+10; }
					$strHTML .= <<<DATA
							<td style="text-align:left; width:50%;font-size:$fontSize;padding-right:$centerHorizontalGap;padding-bottom:$centerVerticalGap;padding-left:$paddingLeftShow;padding-top:$paddingTopShow; ">
								$templateData
							</td>
					
DATA;
				}
				
				if(($aa%4)==0) {
					$bb=0;
					$strHTML .= <<<DATA
							</tr>
						</table>
						</page>
DATA;
				}
				
				if((($aa%4)==0 && $aa!=$num) || ($aa==1)) {
					$strHTML .= <<<DATA
						<page backtop="$backTop" backbottom="$backBottom" backleft="$backLeft" backRight="$backRight">
						<table style="width: 100%;" cellpadding="0" cellspacing="0">
							<tr>
DATA;
				}
				
				if($aa==1) {
				$strHTML .= <<<DATA
						<td style="text-align:left; width:50%;font-size:$fontSize;padding-right:$centerHorizontalGap;padding-bottom:$centerVerticalGap;padding-left:$paddingLeft;padding-top:$paddingTop; ">
							$templateData
						</td>
					
DATA;
				}
				
				if(($aa%2)==0 && ($aa%4)!=0) {
					$strHTML .= <<<DATA
						</tr>
						<tr>
DATA;
				}
				
				if($aa==$num && ($aa%4)!=0) {
					$strHTML .= <<<DATA
							</tr>
						</table>
						</page>
DATA;
				}
				
					
			}
		}
	}
	if($report_type=="Recall letter"){
		$strHTML = '
				<style>
					.tb_heading{
						font-size:12px;
						font-family:Arial, Helvetica, sans-serif;
						font-weight:bold;
						color:#000000;
						background-color:#FE8944;
					}
					.text_b{
						font-size:12px;
						font-family:Arial, Helvetica, sans-serif;
						font-weight:bold;
						color:#FFFFFF;
						background-color:#4684AB;
					}
					.text{
						font-size:11px;
						font-family:Arial, Helvetica, sans-serif;
						background-color:#FFFFFF;
					}
				</style>
				';
		$strHTML .= '<page backtop="0mm" backbottom="0mm">';
		$strHTML .= '<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align: center;	width: 100%">'.$showPageInfo.'</td>
						</tr>
					</table>
				</page_footer>';
	
	
		$i = 1;
		$j = 1;
		$t=0;
		while($rw = @imw_fetch_array($res)){
			$t++;
			$patientid				= $rw['id'];
			//$recallDesc				= $rw['descriptions'];
			//$recallProcedureId		= $rw['procedure_id'];
			$patient_deta			= patient_data($patientid);
			$qu=imw_query("select * from patient_data where id='".$patientid."'");
			$patient_deta			= imw_fetch_assoc($qu);
			
			//$recallProcedureName	= getProcedureName($recallProcedureId);
			//if($recallProcedureId<0) {$recallProcedureName	= $rw['procedure_name'];}
			$patientRefTo			= '';
			$recallData 			= $recallTemplateData;
			
			$PtDOB 					= $patient_deta['DOB'];
			$pat_phy_last_name=$pat_phy_first_name=$appt_date=$appt_time=$pat_phy_name=$facility_name=$proc_name=$pat_phy_sign="";
			list($pat_phy_last_name,$pat_phy_first_name,$appt_date,$appt_time,$facility_name,$proc_name,$pat_phy_sign)= explode("~~",$pat_for_sch_appt_arr[$patientid]);
			$pat_phy_name=$pat_phy_last_name.", ".$pat_phy_first_name;
			if($PtDOB && $PtDOB!='0000-00-00') { $PtDOB = getDateFormatDB(date('Y-m-d',strtotime($PtDOB)));}
			
			//Appointment provider signature works for Report->Scheduler-->Scheduler Report->Recall Type
			$SignPath = $pat_phy_sign;	
			if(file_exists($physical_path.$SignPath)){
				$ProviderSignPath=$physical_path.$SignPath;
				$ProviderSign =  "<img  src='".$ProviderSignPath."'>";
			}
	
			//--- CHANGE DATE FORMAT -----------
			if($_REQUEST['Start_date']){ $Startdate= getDateFormatDB($_REQUEST['Start_date']); }
			if($_REQUEST['End_date']){ $Enddate= getDateFormatDB($_REQUEST['End_date']); }
			//APPT VARIABLE
		
			$apptInfo = $objManageData->__getApptInfo($patientid,'',$Startdate,$Enddate);
			$recallLastDos = get_vocab_lastdos($patientid);
			//OLD VARIABLES
			//MODIFIED VARIABLES FOR CONSISTENCY
			$recallData = str_ireplace('/'.$web_RootDirectoryName.'/data/'.PRACTICE_PATH.'/gn_images/',$fileroot.'/data/'.PRACTICE_PATH.'/gn_images/',$recallData);
			$recallData = str_ireplace($GLOBALS['webroot'].'/library/images/',$fileroot.'/library/images/',$recallData);
			$recallData = str_ireplace('/'.$web_RootDirectoryName.'/interface/common/new_html2pdf/','',$recallData);
			
			$recallData = str_ireplace("{PATIENT NAME TITLE}",$patient_deta['title'],$recallData);
			$recallData = str_ireplace("{PATIENT FIRST NAME}",$patient_deta['fname'],$recallData);
			$recallData = str_ireplace("{MIDDLE NAME}",$patient_deta['mname'],$recallData);
			$recallData = str_ireplace("{LAST NAME}",$patient_deta['lname'],$recallData);
			$recallData = str_ireplace("{PATIENT CITY}",$patient_deta['city'],$recallData);
			$recallData = str_ireplace("{HOME PHONE}",$patient_deta['phone_home'],$recallData);
			$recallData = str_ireplace("{WORK PHONE}",$patient_deta['phone_biz'],$recallData);
			$recallData = str_ireplace("{MOBILE PHONE}",$patient_deta['phone_cell'],$recallData);

			//MODIFIED VARIABLES FOR CONSISTENCY
			$recallData = str_ireplace("{PT-KEY}",$patient_deta['temp_key'],$recallData); 
			$recallData = str_ireplace("{PatientID}",$patientid,$recallData);
			$recallData = str_ireplace("{DOB}",$PtDOB,$recallData);
			$recallData = str_ireplace("{ADDRESS1}",$patient_deta['street'],$recallData);
			$recallData = str_ireplace("{ADDRESS2}",$patient_deta['street2'],$recallData);
			
			$state_zip2 =  $patient_deta['state']."  ".$patient_deta['postal_code'];
			$state_zip = ($patient_deta['state'] != "" && $patient_deta['zip']) ? $patient_deta['state'].",  ".$patient_deta['postal_code'] : $state_zip2;
			
			$recallData = str_ireplace("{STATE, ZIP CODE}",$state_zip,$recallData);
			$recallData = str_ireplace("{STATE ZIP CODE}",$state_zip2,$recallData);
			$recallData = str_ireplace("{PATIENT STATE}",$patient_deta['state'],$recallData);
			$recallData = str_ireplace("{PATIENT ZIP}",$patient_deta['postal_code'],$recallData);
			$recallData = str_ireplace("{DATE}",date(''.phpDateFormat().''),$recallData);
			$recallData = str_ireplace("{RECALL DESCRIPTION}",$recallDesc,$recallData);
			$recallData = str_ireplace("{RECALL PROCEDURE}",$recallProcedureName,$recallData);
			$recallData = str_ireplace("{LAST DOS}",$recallLastDos,$recallData);
			$recallData = str_ireplace("{APPT FACILITY}",$facilityName,$recallData);
			$recallData = str_ireplace("{APPT DATE}",$apptInfo[0],$recallData);
			$recallData = str_ireplace("{APPT TIME}",$apptInfo[8],$recallData);
			$recallData = str_ireplace("{NO SHOW APPOINTMENT}",$last_no_show[$patientid],$recallData);
			$recallData = str_ireplace("{APPT PROVIDER}",$apptInfo[5],$recallData);
			$recallData = str_ireplace("{APPT PROVIDER SIGNATURE}",$ProviderSign,$recallData);
			
			$recallData = str_ireplace("{ETHNICITY}",$patient_deta['ethnicity'],$recallData);	
			$recallData = str_ireplace("{LANGUAGE}",$patient_deta['language'],$recallData);
			$recallData = str_ireplace("{PATIENT MRN}",$patient_deta['External_MRN_1'],$recallData);
			$recallData = str_ireplace("{PATIENT MRN2}",$patient_deta['External_MRN_2'],$recallData);
			$recallData = str_ireplace("{RACE}",$patient_deta['race'],$recallData);
			
			$recallData = str_ireplace("{APPT PROVIDER LAST NAME}",$apptInfo[6],$recallData);
			$recallData = str_ireplace("{APPT PROC}",'',$recallData);	
			$recallData = str_ireplace("{APPT DATE_F}",'',$recallData);
			$recallData = str_ireplace("{APPT FACILITY PHONE}",$apptInfo[3],$recallData);
					
			
			$strHTML .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td class="text" valign="top"  style="margion:0px;">
									'.$recallData.'
								</td>
							</tr>
						</table>
						</page>		
						';
			if($num>$t) {
				$strHTML .= '<page pageset="old">';
			}
		}
	}
	
	$json_data = array();
	
	if($report_type=="Address Labels"){
		$strHTML = '
				<style>
					.tb_heading{
						font-size:12px;
						font-family:Arial, Helvetica, sans-serif;
						font-weight:bold;
						color:#000000;
						background-color:#FE8944;
					}
					.text_b{
						font-size:12px;
						font-family:Arial, Helvetica, sans-serif;
						font-weight:bold;
						color:#FFFFFF;
						background-color:#4684AB;
					}
					.text{
						font-size:12px;
						font-family:Arial, Helvetica, sans-serif;
						background-color:#FFFFFF;
					}
				</style>
				';
		$strHTML .= '<page backtop="'.$top_margin.'mm" backbottom="5mm">';
	
		$strHTML .= "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr>";
		$i = 1;
		$j = 1;
		while($rw = @imw_fetch_array($res)){
			$patientid=$rw['id'];
			$patient_deta=patient_data($patientid);
					
			if($patient_deta[1] != "" && $patient_deta[2] != ""){
				$address = $patient_deta[1].", ".$patient_deta[2];
			}else{
				$address = $patient_deta[1];
			}
			if($num > 1){
				$width = "100%";
			}else{
				$width = "100%";
			}
			if($address==''){
				$address="&nbsp;";
			}
			if($patient_deta[3]==''){
				$patient_deta[3]="&nbsp;";
			}
			
			if($is_json){
			
				$temp_json = array();
				$temp_json['name'] = $patient_deta[9];
				$temp_json['address1'] = $address;
				$temp_json['address2'] = $patient_deta[3];
				
				array_push($json_data, $temp_json);
				continue;
			}
			
			$strHTML .= "
					<td valign=\"top\" width=\"".$coloumn_margin."\" style=\"margion:0px;\">
						<table align=\"left\"  border=\"0\" rules=\"rows\"  cellpadding=\"2\" cellspacing=\"0\" width=\"".$width."\">
							<tr>
								<td width=\"230\" align=\"left\" valign=\"middle\" class=\"text_13b\">".$patient_deta[9]."</TD>
							</tr>
							<tr><td height=\"".$line_margin."\"></td></tr>
							<tr>
								<td width=\"230\" valign=\"middle\" align=\"left\" class=\"text_13\">";
					
							if($address <> ""){ 
								$strHTML .= substr($address,0,30);
							}
							
							$strHTML .= "
							</TD>
							</tr>
							<tr><td height=\"".$line_margin."\"></td></tr>
							<TR>
								<td width=\"230\" valign=\"middle\" align=\"left\" class=\"text_13\">".$patient_deta[3]."</TD>
							</tr>
							<tr><td height=\"".$line_margin."\"></td></tr>
						</table>
					</td>";
			$break = '';
			if($i%3 == 0){
				if($i%30 == 0){
					$break = "</tr><tr><td></td></tr><tr>";
				}else{
					$break = "</tr><tr><td height=\"".$bottom_margin."\"></td></tr><tr>";
				}
			}
			if($j == $num){
				$break = "</tr>";
			}
			$strHTML .= $break;
			$i++;
			$j++; 			
		}
		$strHTML .= "</table></page>";
	}
}


if($is_json){
	echo '<script type="text/javascript" src="'.$GLOBALS['webroot'].'/js/jquery.js"></script>';
	echo '<script type="text/javascript" src="'.$GLOBALS['webroot'].'/library/dymo/DYMO.Label.Framework.latest.js"></script>';
	echo '<script type="text/javascript" src="'.$GLOBALS['webroot'].'/library/dymo/dymo_common.js"></script>';
	
	echo '<script type="text/javascript">';
		echo 'var labelData = $.parseJSON(\''.json_encode($json_data).'\');';
		echo 'printLabels("'.$_REQUEST['dymo_printer'].'",labelData);';
	echo '</script>';
	
	exit;
}
$bl_printed = true;
if(trim($strHTML) != ""){
	//getting contents of html page hence created and writing in a html file
	$file_location = write_html($strHTML);
}
?>	