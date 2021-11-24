<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
/************************************************/
/* REPORT BY PRABH ON 4.13 PM 9th OCT 2014
/************************************************/
set_time_limit (0);
$without_pat = "yes";
include_once("../../../config/globals.php");
include_once($GLOBALS['fileroot']."/interface/chart_notes/chart_globals.php");
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
$library_path = $GLOBALS['webroot'].'/library';
include_once($GLOBALS['fileroot']."/library/classes/functions.smart_tags.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/pnTempParser.php");
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

//getting detail of seleted patients
$appt_qry_res = array();
$query = imw_query($schQry);
while($appt_row = imw_fetch_assoc($query)){
	$appt_qry_res[] = $appt_row;
}

for($i=0;$i<count($appt_qry_res);$i++){
	$apptId = $appt_qry_res[$i]['id'];
	$pid = $appt_qry_res[$i]['sa_patient_id'];
	$phyId  = ($appt_qry_res[$i]['sa_doctor_id']=='') ? 0 : $appt_qry_res[$i]['sa_doctor_id'];
	$arrPatIds[$pid] = $pid;

	$patient_name_arr = array();
	$patient_name_arr["LAST_NAME"] = $appt_qry_res[$i]['pLname'];
	$patient_name_arr["FIRST_NAME"] = $appt_qry_res[$i]['pFname'];
	$patient_name_arr["MIDDLE_NAME"] = $appt_qry_res[$i]['pMname'];	
		
	$patient_name = changeNameFormat($patient_name_arr);
	
	$arrPatData[$phyId][$apptId]['patId'] = $pid;
	$arrPatData[$phyId][$apptId]['patName'] = $patient_name;
	$arrPatData[$phyId][$apptId]['apptDate'] = $appt_qry_res[$i]['sa_app_start_date'];
	$arrPatData[$phyId][$apptId]['tempKey'] = $appt_qry_res[$i]['temp_key'];
	$arrPatData[$phyId][$apptId]['facility'] = $arrAllFacilities[$appt_qry_res[$i]['sa_facility_id']];
}

$qryPatPart='';
//making search string from finalized patient ids
if(sizeof($arrPatIds)>0){
	$strPatIds = implode(',', $arrPatIds);
	$qryPatPart = " AND patient_data.id IN(".$strPatIds.")";
}


//preparing style for pdf letter content
$pdfFileData .='
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
	</style>';

//query to get letter format
$templQry = "select pt_docs_template_name, pt_docs_template_content, pt_docs_template_enable_facesheet, 
		 enable_footer, pt_docs_template_id
		 from pt_docs_template 
		 where pt_docs_template_status = '0'";
		 
if(empty($letterTempId) == false){
	$templQry .= " and pt_docs_template_id = $letterTempId";
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
//sorting array by key value
ksort($templDataArr);
ksort($enableTypeArr);
ksort($enableFooterArr);
ksort($ptDocsTemplateIdArr);


$arr_patients_temp_key_given = array(); //ARRAY TO HOLD PATIENT IDs WHICH NEED TO MARK AS TEMP-KEY GIVEN;


foreach($arrPatIds as $patint_id){
	$aa++;
	$bb++;
	//--- GET PATIENT DETAILS ----
	$patQry = "select patient_data.*, 
			pos_facilityies_tbl.facilityPracCode,
			heard_about_us.heard_options , 
			employer_data.name emp_name, 
			employer_data.street as emp_street, 
			employer_data.street2 as emp_street2, 
			employer_data.state as emp_state,
			employer_data.postal_code as emp_postal_code, 
			employer_data.city as emp_city,
			users.lname as users_lname, 
			users.fname as users_fname, 
			users.mname as users_mname,
			date_format(patient_data.date, '".$dateFormat."') as reg_date,
			date_format(patient_data.DOB, '".$dateFormat."') as patient_dob				
			
			FROM patient_data 
			
			LEFT JOIN pos_facilityies_tbl 
			ON pos_facilityies_tbl.pos_facility_id = default_facility
			
			LEFT JOIN heard_about_us 
			ON patient_data.heard_abt_us = heard_about_us.heard_id
			
			LEFT JOIN employer_data 
			ON employer_data.pid = patient_data.id
			
			LEFT JOIN users 
			ON users.id = patient_data.providerID
			
			WHERE patient_data.id = '$patint_id'";
			
	//RUNNING query
	$patQryRes = array();
	$query = imw_query($patQry);
	while($pat_row = imw_fetch_assoc($query)){
		$patQryRes[] = $pat_row;
	}
	
	$template_data_arr = array_values($templDataArr);
	$enable_type_arr = array_values($enableTypeArr);
	$enable_footer_arr = array_values($enableFooterArr);
	$pt_docs_template_id_arr = array_values($ptDocsTemplateIdArr);
	
	if(count($template_data_arr)>=1)$showBtn=1;
	
	$enable_type="";
	for($t=0;$t<count($template_data_arr);$t++){
		
		$template_content 	= $template_data_arr[$t];
		$enable_type 		= $enable_type_arr[$t];
		$enable_footer 		= $enable_footer_arr[$t];
		$pt_docs_template_id= $pt_docs_template_id_arr[$t];
		
		/*--IF temp_key VARIABLE FOUND, THEN PICKING PATIENT id TO UPDATE--*/
		if(strpos($template_content,'{PT-KEY}') > -1){
			$arr_patients_temp_key_given[] = $patint_id;
		}
		
		$templateData = $objManageData->__loadTemplateData($template_content,$patQryRes[0],$provider_id_str,$start_date,$end_date);
		
		//--- CREATE PDF FILE DATA ----
		if($enable_type=="postcard") {
			$paddingTopShow = $paddingTop;
			$paddingLeftShow = $paddingLeft;
			if($aa!=1) {
				if($bb==3) { $paddingTopShow = $paddingTopShow+55; }
				if($bb==4) { $paddingTopShow = $paddingTopShow+55; }
				//if($bb==2 || $bb==4) { $paddingLeftShow = $paddingLeftShow+10; }
				$pdfFileData .= "
						<td style=\"text-align:left; width:50%;font-size:$fontSize;padding-right:$centerHorizontalGap;padding-bottom:$centerVerticalGap;padding-left:$paddingLeftShow;padding-top:$paddingTopShow; \">$templateData</td>";
			}
			
			if(($aa%4)==0) {
				$bb=0;
				$pdfFileData .= "
						</tr>
					</table>
					</page>";
			}
			
			if((($aa%4)==0 && $aa!=count($arrPatIds)) || ($aa==1)) {
				$pdfFileData .="
					<page backtop=\"$backTop\" backbottom=\"$backBottom\" backleft=\"$backLeft\" backRight=\"$backRight\">
					<table style=\"width: 100%;\" cellpadding=\"0\" cellspacing=\"0\">
					<tr>";
			}
			
			if($aa==1) {
			$pdfFileData .= "
					<td style=\"text-align:left; width:50%;font-size:$fontSize;padding-right:$centerHorizontalGap;padding-bottom:$centerVerticalGap;padding-left:$paddingLeft;padding-top:$paddingTopShow; \">
						$templateData
					</td>";
			}
			
			if(($aa%2)==0 && ($aa%4)!=0) {
				$pdfFileData .= "</tr>
					<tr>";
			}
			
			if($aa==count($arrPatIds) && ($aa%4)!=0) {
				$pdfFileData .= "</tr>
					</table>
					</page>";
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
			
			$pdfFileData .= "
				<page backtop=\"-3mm\" backbottom=\"1mm\"  backleft=\"-2mm\"  backright=\"0mm\">
				<page_footer>
					<table style=\"width: 100%;\" cellpadding=\"0\" cellspacing=\"0\">
						$footerAdd 
						$footerPageNum
					</table>
				</page_footer>
					<table style=\"width: 100%;\" cellpadding=\"0\" cellspacing=\"0\">
						<tr>
							<td style=\"text-align:left;width:100%\">
							$templateData
							</td>
						</tr>
					</table>
				</page>";
			
		}
	}
	
}//end of loop for getting selected patient data

//putting this code in comment for varification
/*--UPDATING TEMP-KEY GIVEN STATUS---*/
/*if(count($arr_patients_temp_key_given)>0){
	$str_patients_temp_key_given = implode(',',$arr_patients_temp_key_given);
	$q_update_temp_given = "UPDATE patient_data SET temp_key_chk_val = 1, temp_key_chk_opr_id = '".$_SESSION['authId']."', temp_key_chk_datetime='".date('Y-m-d H:i:s')."' WHERE id IN($str_patients_temp_key_given) AND temp_key!='' AND temp_key_chk_val = 0";
	$res_update_temp_given = mysql_query($q_update_temp_given);
}*/
	
/*--GENERATING PDF---*/

$pdfFileData = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/gn_images/','../../data/'.PRACTICE_PATH.'/gn_images/',$pdfFileData);
$boolPdf = true;
if(count($template_data_arr) > 0){
	$boolPdf = false;
	$pdfFileData = html_entity_decode($pdfFileData);
	$file_location = write_html($pdfFileData);
	if($file_location){
		echo '<div class="text-center alert alert-info">Please Check PDF.</div>';
	}
	$page_op = "p";	
	if($enable_type=="postcard") {
		$page_op = "l";	
	}
	$boolPdf=true;	
	$page_data="<strong></strong>";
}
?>