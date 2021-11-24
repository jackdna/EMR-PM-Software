<?php
set_time_limit (0);
$without_pat = "yes";
include_once("../../../config/globals.php");
include_once($GLOBALS['fileroot']."/interface/chart_notes/chart_globals.php");
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
$library_path = $GLOBALS['webroot'].'/library';
include_once($GLOBALS['fileroot']."/library/classes/functions.smart_tags.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/PnTempParser.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/wv_functions.php");
include_once($GLOBALS['fileroot']."/library/classes/Functions.php");
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');

$objParser = new PnTempParser;
$objManageData 	= new ManageData;

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

if(empty($form_submitted) === false){

	//--- GET ALL PATIENT ID WITH IN SREARCH CERTARIA ---
	$facility_name_str = join(",", $facility_name);
	$provider_id_str = join(",", $phyId);
	$template_id_str = $_REQUEST['template_id'];

	//--- CHANGE DATE FORMAT ---
	$startDate = getDateFormatDB($Start_date);
	$endDate = getDateFormatDB($End_date);
	
	//--- GET FACILITY IDS ----
$fac_query = "select id from facility";
$fac_query_res = imw_query($fac_query);
$fac_id_arr = array();
while ($fac_res = imw_fetch_array($fac_query_res)) {
  $fac_id_arr[] = trim($fac_res['id']);
}
$facIds = "'".implode("','", $fac_id_arr)."'";
	//--- GET MAIN APPOINTMENT DATA -----
	// `case_type_id` GET TO DISPLAY THE INSURANCE DATA BASED ON PT. APPOINTMENT
	$appt_qry = "select sa_patient_id as sa_patient_id, id, case_type_id as insCaseTypeId  from schedule_appointments where sa_doctor_id > 0
				and sa_app_start_date between '$startDate' and '$endDate'
				and sa_patient_app_status_id NOT IN(203,201,18,19,20,3) 
				and sa_facility_id IN($facIds)
				and sch_template_id != '0'";
	if(empty($facility_name_str) === false){
		$appt_qry .= " and sa_facility_id in ($facility_name_str)";
	}
	if(empty($provider_id_str) === false){
		$appt_qry .= " and sa_doctor_id in ($provider_id_str)";
	}
	$appt_qry .= " ORDER BY sa_app_start_date ASC, sa_app_starttime ASC ";
	$appt_qry_res = array();
	$query = imw_query($appt_qry);
	while($appt_row = imw_fetch_assoc($query)){
		$appt_qry_res[] = $appt_row;
	}
	//--- FETCH PT DOCS TEMPLATE ----
	$templQry = "select pt_docs_template_name, pt_docs_template_content, pt_docs_template_enable_facesheet, 
				 enable_footer, pt_docs_template_id
				 from pt_docs_template 
				 where pt_docs_template_status = '0'";
	if(empty($template_id_str) === false){
		$templQry .= " and pt_docs_template_id in ($template_id_str)";
	}

	$templQryRes = array();
	$query = imw_query($templQry);
	while($temp_row = imw_fetch_assoc($query)){
		$templQryRes[] = $temp_row;
	}

	$templDataArr = $enableTypeArr = $enableFooterArr = array();
	for($i=0;$i<count($templQryRes);$i++){
		$template_name = $templQryRes[$i]['pt_docs_template_name'];
		$templDataArr[$template_name] 		= $templQryRes[$i]['pt_docs_template_content'];
		$enableTypeArr[$template_name] 		= $templQryRes[$i]['pt_docs_template_enable_facesheet'];
		$enableFooterArr[$template_name] 	= $templQryRes[$i]['enable_footer'];
		$ptDocsTemplateIdArr[$template_name]= $templQryRes[$i]['pt_docs_template_id'];
	}
	ksort($templDataArr);
	ksort($enableTypeArr);
	ksort($enableFooterArr);
	ksort($ptDocsTemplateIdArr);

	//--- CREATE HTML CONTENT FOR PDF PRINTING ----
	$pdfFileData = NULL;
	$pdfFileData .= <<<DATA
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

//---- REPORT GENERATE BY ---
	$phpDateFormat 		= phpDateFormat();
	$curDate 			= date($phpDateFormat.' h:i A');
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = ucfirst(trim($op_name_arr[1][0]));
	$op_name .= ucfirst(trim($op_name_arr[0][0]));
	$aa=0;
	$bb=0;
	$enable_type="";
	for($i=0;$i<count($appt_qry_res);$i++){
		$aa++;
		$bb++;
		//--- GET PATIENT DETAILS ----
		$insCaseTypeId="";
		$patint_id = $appt_qry_res[$i]['sa_patient_id'];
		$appt_id = $appt_qry_res[$i]['id'];
		$insCaseTypeId = $appt_qry_res[$i]['insCaseTypeId'];
		$patQry = "select patient_data.*, pos_facilityies_tbl.facilityPracCode,heard_about_us.heard_options , 
				employer_data.name emp_name, employer_data.street as emp_street, 
				employer_data.street2 as emp_street2, employer_data.state as emp_state,
				employer_data.postal_code as emp_postal_code, employer_data.city as emp_city,
				users.lname as users_lname, users.fname as users_fname, users.mname as users_mname,
				chart_master_table.id as form_id,date_format(patient_data.date, '".get_sql_date_format()."') as reg_date,
				date_format(patient_data.DOB, '".get_sql_date_format()."') as patient_dob				
				from patient_data left join pos_facilityies_tbl 
				on pos_facilityies_tbl.pos_facility_id = default_facility
				left join heard_about_us on patient_data.heard_abt_us = heard_about_us.heard_id
				left join employer_data on employer_data.pid = patient_data.id
				left join users on users.id = patient_data.providerID
				left join chart_master_table on chart_master_table.patient_id = patient_data.id 
				where patient_data.id = '$patint_id' ORDER BY chart_master_table.id DESC LIMIT 0,1";
		
		$patQryRes = array();
		$query = imw_query($patQry);
		while($pat_row = imw_fetch_assoc($query)){
			$patQryRes[] = $pat_row;
		}
		
		$template_data_arr = array_values($templDataArr);
		$enable_type_arr = array_values($enableTypeArr);
		$enable_footer_arr = array_values($enableFooterArr);
		$pt_docs_template_id_arr = array_values($ptDocsTemplateIdArr);
			
		$enable_type="";
		for($t=0;$t<count($template_data_arr);$t++){
			
			$template_content 	= $template_data_arr[$t];
			$enable_type 		= $enable_type_arr[$t];
			$enable_footer 		= $enable_footer_arr[$t];
			$pt_docs_template_id= $pt_docs_template_id_arr[$t];
			//fromReports IDENTIFIER TO IDENTIFY THAT REQUEST IS SENDING FROM REPORT TO library/classes/work_view/PnTempParser.php
			//$insCaseTypeId FROM SCHEDULER TABLE TO DISPLAY INSURANCE BASED ON PATIENT APPOINTMENT
			$templateData	= $objParser->getDataParsed($template_content,$patQryRes[0]['id'],$patQryRes[0]['form_id'],'','','','','','','','fromReports',$insCaseTypeId);
			$templateData 	= $objManageData->__loadTemplateData($templateData,$patQryRes[0],$provider_id_str,$startDate,$endDate, $read_from_database=1,$appt_id);
			$templateData = html_entity_decode($templateData);
			//--- CREATE PDF FILE DATA ----
			if($enable_type=="postcard") {
				$paddingTopShow = $paddingTop;
				$paddingLeftShow = $paddingLeft;
				if($aa!=1) {
					if($bb==3) { $paddingTopShow = $paddingTopShow+55; }
					if($bb==4) { $paddingTopShow = $paddingTopShow+55; }
					//if($bb==2 || $bb==4) { $paddingLeftShow = $paddingLeftShow+10; }
					$pdfFileData .= <<<DATA
							<td style="text-align:left; width:50%;font-size:$fontSize;padding-right:$centerHorizontalGap;padding-bottom:$centerVerticalGap;padding-left:$paddingLeftShow;padding-top:$paddingTopShow; ">
								$templateData
							</td>
					
DATA;
				}
				
				if(($aa%4)==0) {
					$bb=0;
					$pdfFileData .= <<<DATA
							</tr>
						</table>
						</page>
DATA;
				}
				
				if((($aa%4)==0 && $aa!=count($appt_qry_res)) || ($aa==1)) {
					$pdfFileData .= <<<DATA
						<page backtop="$backTop" backbottom="$backBottom" backleft="$backLeft" backRight="$backRight">
						<table style="width: 100%;" cellpadding="0" cellspacing="0">
							<tr>
DATA;
				}
				
				if($aa==1) {
				$pdfFileData .= <<<DATA
						<td style="text-align:left; width:50%;font-size:$fontSize;padding-right:$centerHorizontalGap;padding-bottom:$centerVerticalGap;padding-left:$paddingLeft;padding-top:$paddingTopShow; ">
							$templateData
						</td>
					
DATA;
				}
				
				if(($aa%2)==0 && ($aa%4)!=0) {
					$pdfFileData .= <<<DATA
						</tr>
						<tr>
DATA;
				}
				
				if($aa==count($appt_qry_res) && ($aa%4)!=0) {
					$pdfFileData .= <<<DATA
							</tr>
						</table>
						</page>
DATA;
				}
				
			}else {
				
				//start code to add record in ptdocs
				$templateDataArr = array();
				
				$templateDataArr['patient_id'] = $patint_id;
				$templateDataArr['pt_doc_primary_template_id'] = $pt_docs_template_id;
				$templateDataArr['pt_enable_footer'] = $enable_footer;
				$templateDataArr['template_content'] = $templateData;
				//	REPLACING SMART TAG OPTONS WITH NON-ANCHOR STRING. 
				$regpattern='|<a class=\"cls_smart_tags_link\" id=(.*) href=(.*)>(.*)<\/a>|U'; 
				$templateDataArr['template_content'] = preg_replace($regpattern, "\\3", $templateData);
				$regpattern='|<a id=(.*) class=\"cls_smart_tags_link\" href=(.*)>(.*)<\/a>|U'; 
				$templateDataArr['template_content'] = preg_replace($regpattern, "\\3", $templateData);
				//--SMART TAG REPLACEMENT END--
				$templateDataArr['created_date'] = date('Y-m-d h:i:s');
				$templateDataArr['operator_id'] = $_SESSION['authId'];
				$templateDataArr['template_delete_status'] = 0;
				AddRecords($templateDataArr, 'pt_docs_patient_templates');
				//end code to add recod in ptdocs
				
				$footerAdd='';
				$footerPageNum = '<tr><td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td></tr>';
				if($enable_footer=="yes") {
					//Static code for CEC server
					$footerAdd ='<tr><td style="text-align:center;width:100%" class="text_value">7001 S Edgerton Rd, Suite B&nbsp;&nbsp;&nbsp; Brecksville, OH&nbsp; 44141&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 440-526-1974&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 800-875-0300</td></tr>';
					$footerPageNum = '';
				}else if($enable_footer=="disable_page_no"){
					$footerPageNum = '';
				}
				
				$pdfFileData .= <<<DATA
					<page backtop="-3mm" backbottom="1mm"  backleft="-2mm"  backright="0mm">
					<page_footer>
						<table style="width: 100%;" cellpadding="0" cellspacing="0">
							$footerAdd 
							$footerPageNum
						</table>
					</page_footer>
						<table style="width: 100%;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="text-align:left;width:100%">
								$templateData
								</td>
							</tr>
						</table>
					</page>
DATA;
				
			}
		}
		
	}
	if(strtoupper(substr(PHP_OS, 0, 3))=='LIN'){ 
		$pdfFileData= mb_convert_encoding($pdfFileData, "HTML-ENTITIES", 'UTF-8');
	}	
	//-----FIX FOR BREAKING HTML TAGS DUE TO SPECIAL SYMBOLS < OR  > IN DATA----
	$find = array("<OD", "<od", ">OD", ">od", "<OS", "<os", ">OS", ">os","OD>","OD>>","OS >>");
	$replace = array("< OD", "< od", "> OD", "> od", "< OS", "< os", "> OS", "> os","OD &gt;","OD &gt;&gt;","OS &gt;&gt;");
	$pdfFileData = str_replace($find,$replace,$pdfFileData);
	
	$pdfFileData = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/gn_images/','../../data/'.PRACTICE_PATH.'/gn_images/',$pdfFileData);
	$pdfFileData = str_ireplace($GLOBALS['webroot'].'/library/images/',$GLOBALS['php_server'].'/library/images/',$pdfFileData);
	$pdfFileData = str_ireplace('/'.$GLOBALS['php_server'].'/library/images/',$GLOBALS['php_server'].'/library/images/',$pdfFileData);
	
	if(count($appt_qry_res) > 0 and count($template_data_arr) > 0){
		$file_location = write_html($pdfFileData);
		if($file_location){
			echo '<div class="text-center alert alert-info">Please Check PDF.</div>';
		}
	} else {
		echo '<div class="text-center alert alert-info">No record exists.</div>';
	}
}
?>