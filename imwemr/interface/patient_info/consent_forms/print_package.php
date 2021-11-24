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
/*	
File: index.php
Purpose: Print consent package
Access Type: Direct 
*/

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");

header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");
include_once("../../../config/globals.php");
$library_path = $GLOBALS['webroot'].'/library';
$patient_id = $_SESSION['patient'];

//require_once('../../main/Functions.php');
require_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
include_once(dirname(__FILE__)."/../../../library/classes/print_pt_key.php");
include(dirname(__FILE__).'/../../../library/bar_code/code128/code128.class.php');
$oSaveFile = new SaveFile($patient_id);
$obj_print_pt_key=new print_pt_key;
$getSqlDateFormat = get_sql_date_format();
$getSqlDateFormatSmall = str_replace("Y","y",get_sql_date_format());
$browserIpad = 'no';
if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true) {
	$browserIpad = 'yes';
}
//START CODE TO SET NEW CLASS TO CONSENT FORMS
/*
$htmlFolder = "html2pdf";
$htmlV2Class = false;
$htmlFilePth = "html2pdf/index.php";
if(constant("CONSENT_FORM_VERSION")=="consent_v2") {
	$htmlFolder = "new_html2pdf";
	$htmlV2Class=true;	
	$htmlFilePth = "new_html2pdf/createPdf.php";
}
*/
$htmlFolder = "html_to_pdf";
$htmlV2Class=true;	
$htmlFilePth = "html_to_pdf/createPdf.php";

//END CODE TO SET NEW CLASS TO CONSENT FORMS

//===============LOGGED IN FACILITY INFO WORKS WORKS STARTS HERE=========================
$loggedfacCity = $loggedfacState = $loggedfacCountry = $loggedfacPostalcode = $loggedfacExt = "";
$loggedfacilityInfoArr 	= $obj_print_pt_key->logged_in_facility_info($_SESSION['login_facility']);
$loggedfacName 			= $loggedfacilityInfoArr[0];
$loggedfacCity 			= $loggedfacilityInfoArr[1];
$loggedfacState 		= $loggedfacilityInfoArr[2];
$loggedfacCountry 		= $loggedfacilityInfoArr[3];
$loggedfacPostalcode	= $loggedfacilityInfoArr[4];
$loggedfacExt	   		= $loggedfacilityInfoArr[5];
if($loggedfacPostalcode && $loggedfacExt){
	$loggedzipcodext = $loggedfacPostalcode.'-'.$loggedfacExt;
}else{
	$loggedzipcodext = $loggedfacPostalcode;
}
$loggedfacAddress = $loggedfacCity.', '.$loggedfacState.',&nbsp;'.$loggedfacCountry.'&nbsp;'.$loggedzipcodext;
//=======================ENDS HERE======================================================


$patient_id = $_SESSION['patient'];
$packageCategoryId = $_REQUEST['package_category_id'];
//---- get Patient details ---------
$qry = "select *,date_format(DOB,'$getSqlDateFormat') as pat_dob,date_format(date,'$getSqlDateFormat') as reg_date
		from patient_data where id = '$patient_id'";
$patientDetails = get_array_records_query($qry);
$patient_initial = substr($patientDetails[0]['fname'],0,1);
$patient_initial .= substr($patientDetails[0]['lname'],0,1);
list($year, $month, $day) = explode('-',$patientDetails[0]['DOB']);
$pat_date = $year."-".$month."-".$day;
$patient_age = get_age($pat_date);
//--- get physician name --------
$pro_id = $patientDetails[0]['providerID'];
$qry = "select concat(lname,', ',fname) as name,mname from users where id = '$pro_id'";
$phyDetail = get_array_records_query($qry);
$phy_name = ucwords(trim($phyDetail[0]['name'].' '.$phyDetail[0]['mname']));
//--- get reffering physician name --------
$primary_care_id = $patientDetails[0]['primary_care_id'];
$qry = "select concat(LastName,', ',FirstName) as name , MiddleName,Title, specialty, physician_phone, Address1, Address2, ZipCode, City, State from refferphysician
		where physician_Reffer_id = '$primary_care_id'";
$reffPhyDetail = get_array_records_query($qry);
$reffer_name = ucwords(trim($reffPhyDetail[0]['name'].' '.$reffPhyDetail[0]['MiddleName']));
$refPhyAddress="";
$refPhyAddress .= (!empty($reffPhyDetail[0]['Address1'])) ? trim($reffPhyDetail[0]['Address1']) : "";
$refPhyAddress .= (!empty($reffPhyDetail[0]['Address2'])) ? "<br>".trim($reffPhyDetail[0]['Address2']) : "";

//--- get pos facility name -------
$default_facility = $patientDetails[0]['default_facility'];
$qry = "select facilityPracCode from pos_facilityies_tbl 
		where pos_facility_id = '$default_facility'";
$posFacilityDetail = get_array_records_query($qry);
$pos_facility_name = $posFacilityDetail[0]['facilityPracCode'];
//--- get responsible party information ------
$qry = "select *,date_format(dob,'$getSqlDateFormat') as res_dob 
		from resp_party where patient_id = '$patient_id'";
$resDetails = get_array_records_query($qry);
//--- get epmoyee detail of patient ---
$qry = "select * from employer_data where pid = '$patient_id'";
$empDetails = get_array_records_query($qry);

if($packageCategoryId) {
	$pckQry = "SELECT package_category_id, package_category_name, package_consent_form FROM consent_package WHERE package_category_id='".$packageCategoryId."' ORDER BY package_category_name";	
	$pckDetail = get_array_records_query($pckQry);
	if(count($pckDetail) > 0){
		for($i_pck=0;$i_pck<count($pckDetail);$i_pck++){	
			$package_category_id = $pckDetail[$i_pck]['package_category_id'];
			$package_consent_form = $pckDetail[$i_pck]['package_consent_form'];
		}
	}

	//start creating barcode image
	$barCodePatientId = $patient_id;
	//$barCodeFolderId = $consent_cat_id;
	$barCodeFolderId = $package_category_id;
	$lim=9;
	$lenPtId = strlen($barCodePatientId);
	$lenCnt = $lim-$lenPtId;
	for($q=0;$q<$lenCnt;$q++) {
		$barCodePatientId =	'0'.$barCodePatientId;
	}
	$limFolder=3;
	$lenFolder = strlen($barCodeFolderId);
	$lenCntFolder = $limFolder-$lenFolder;
	for($r=0;$r<$lenCntFolder;$r++) {
		$barCodeFolderId =	'0'.$barCodeFolderId;
	}
	$barCodeFolderId =	'1'.$barCodeFolderId;
	$Barcode_Text = $barCodePatientId.'-'.$barCodeFolderId;
	$img_name = $barCodePatientId.'-'.$barCodeFolderId;
	$oSaveFile->ptDir("consent_forms/bar_code_images");
	$barCodeImgPath = '../../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/bar_code_images/'.$img_name.'.png';
	generate_barcode($Barcode_Text,$img_name,$barCodeImgPath); 
	$barCodeImgPath = $web_root.'/data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/bar_code_images/'.$img_name.'.png';
	$barCodeContent = '<img src="'.$barCodeImgPath.'">';
	$backtop="10mm";
	$page_bar_code='<page backtop="'.$backtop.'"" backbottom="5mm">
						<page_header>
						  <table style="width:700px;" cellpadding="0" cellspacing="0"><tr>
						  <td style="text-align:right;width:700px;">'.$barCodeContent.'</td></tr><tr>
						  <td style="text-align:right;width:700px;">'.$img_name.'</td></tr></table>
					  </page_header>
				    </page>';
	$page_bar_code_v1='<p align="right" style="margin-top:-15px;">'.$barCodeContent.'</p><p align="right">'.$img_name.'</p>';
	if(constant("BAR_CODE_DISABLE")=='YES'){
		
		$page_bar_code='<page></page>';$backtop="0mm";$page_bar_code_v1='';
	}
}

$qry = "select pcfi.consent_form_content_data,pcfi.consent_form_id, cf.cat_id as consent_cat_id from patient_consent_form_information pcfi
		LEFT JOIN consent_form cf ON (cf.consent_form_id = pcfi.consent_form_id)
		WHERE pcfi.consent_form_id = '$consent_form_id' and pcfi.patient_id = '$patient_id' and pcfi.form_information_id  = '$form_information_id'";
$consentDetail = get_array_records_query($qry);
$chk = count($consentDetail);

$consent_form_content='';
if(count($consentDetail) == 0){
	$qry = "SELECT cf.*,cf.consent_form_content AS consent_form_content_data, cf.cat_id AS consent_cat_id FROM consent_form cf 
			INNER JOIN consent_category cc ON (cc.cat_id=cf.cat_id)
			WHERE cf.consent_form_id IN(0,".$package_consent_form.")
			ORDER BY cf.consent_form_name ";
	$consentDetail = get_array_records_query($qry);
	$pageTag="";
	for($i_csn=0;$i_csn<count($consentDetail);$i_csn++){
		//if($i_csn>0) { $pageTag = "<page></page>";}
		//$consent_form_content .= $pageTag.$consentDetail[$i_csn]['consent_form_content'];
		
		if($htmlV2Class==true){
			$pageTag = $page_bar_code;
		}else {
			//$pageTag = $consentDetail[$i_csn]['consent_form_content'];	
		}
		$consent_form_content .= $pageTag.$consentDetail[$i_csn]['consent_form_content'];
		//$consent_form_content .= $pageTag;
		
	}
}
//get Primary insurence data//
$qryGetInsPriData = "select provider as priInsComp,
					 policy_number as priPolicyNumber,
					 group_number as priGroupNumber,
					 CONCAT(subscriber_fname,'&nbsp;',subscriber_lname)as priSubscriberName,
					 subscriber_relationship as priSubscriberRelation,
					 date_format(subscriber_DOB,'$getSqlDateFormat') as priSubscriberDOB,
					 subscriber_ss as priSubscriberSS,
					 subscriber_phone as priSubscriberPhone,
					 subscriber_street as priSubscriberStreet,
					 subscriber_city as priSubscriberCity,
					 subscriber_state as priSubscriberState,
					 subscriber_postal_code as priSubscriberZip,
					 subscriber_employer as priSubscriberEmployer
					 from insurance_data
					 where pid = '".$_SESSION['patient']."' and 
					 ins_caseid > 0 and
					 type = 'primary' 
					 and actInsComp = 1
					 and provider > 0   
					 ";
$rsGetInsPriData = imw_query($qryGetInsPriData);
$numRowGetInsPriData = imw_num_rows($rsGetInsPriData);
if($numRowGetInsPriData>0){
	extract(imw_fetch_array($rsGetInsPriData));
	$qryGetInsPriProvider = "select name as priInsCompName from insurance_companies where id = $priInsComp";
	$rsGetInsPriProvider = imw_query($qryGetInsPriProvider);
	$numRowGetInsPriProvider = imw_num_rows($rsGetInsPriProvider);
	if($numRowGetInsPriProvider>0){
		extract(imw_fetch_array($rsGetInsPriProvider));
	}
}
//end get Primary insurence data//
//get Secondary insurence data//
$qryGetInsSecData = "select provider as secInsComp,
					 policy_number as secPolicyNumber,
					 group_number as secGroupNumber,
					 CONCAT(subscriber_fname,'&nbsp;',subscriber_lname)as secSubscriberName,
					 subscriber_relationship as secSubscriberRelation,
					 subscriber_DOB as secSubscriberDOB,
					 subscriber_ss as secSubscriberSS,
					 subscriber_phone as secSubscriberPhone,
					 subscriber_street as secSubscriberStreet,
					 subscriber_city as secSubscriberCity,
					 subscriber_state as secSubscriberState,
					 subscriber_postal_code as secSubscriberZip,
					 subscriber_employer as secSubscriberEmployer
					 from insurance_data
					 where pid = '".$_SESSION['patient']."' and 
					 ins_caseid > 0 and
					 type = 'secondary' 
					 and actInsComp = 1
					 and provider > 0
					 ";
$rsGetInsSecData = imw_query($qryGetInsSecData);
$numRowGetInsSecData = imw_num_rows($rsGetInsSecData);
if($numRowGetInsSecData>0){
	extract(imw_fetch_array($rsGetInsSecData));
	$qryGetInsSecProvider = "select name as secInsCompName from insurance_companies where id = $secInsComp";
	$rsGetInsSecProvider = imw_query($qryGetInsSecProvider);
	$numRowGetInsSecProvider = imw_num_rows($rsGetInsSecProvider);
	if($numRowGetInsSecProvider>0){
		extract(imw_fetch_array($rsGetInsSecProvider));
	}
}

$pcp_id = $patientDetails[0]['primary_care_phy_id'];
$qry = "select pcp.Title as pcpTitle,pcp.FirstName as pcpFName,pcp.MiddleName as pcpMName,pcp.LastName as pcpLName, 
		pcp.Address1 as pcpAddress1,pcp.Address2 as pcpAddress2,pcp.City as pcpCity,pcp.State as pcpState,pcp.ZipCode as pcpZipCode
		 from refferphysician pcp
		 where pcp.physician_Reffer_id = '".$pcp_id."'";
$pcpPhyDetail = get_array_records_query($qry);
$pcpName=$pcpPhyDetail[0]['pcpLName'].", ".$pcpPhyDetail[0]['pcpFName']." ".$pcpPhyDetail[0]['pcpMName'];
$pcpAddress .= (!empty($pcpPhyDetail[0]['pcpAddress1'])) ? trim($pcpPhyDetail[0]['pcpAddress1']) : "";
$pcpAddress .= (!empty($pcpPhyDetail[0]['pcpAddress2'])) ? "<br>".trim($pcpPhyDetail[0]['pcpAddress2']) : "";

//end get Secondary insurence data//
//get patient Appointment data//
$qryGetApptData = "select sa.sa_app_start_date,DATE_FORMAT(sa.sa_app_start_date, '%a $getSqlDateFormatSmall') as appDate,
					 TIME_FORMAT(sa.sa_app_starttime,'%h:%i %p') as appTime,
					 sp.proc as ptProc
					 from schedule_appointments sa 
					 INNER JOIN slot_procedures sp ON sp.id = sa.procedureid  
					 where sa.sa_patient_id = '".$_SESSION['patient']."' and 
					 sa.sa_app_start_date <= current_date() 
					 order by sa.sa_app_start_date DESC 
					 LIMIT 1 
					 ";
					 
$rsGetApptData = imw_query($qryGetApptData);
$numRowGetApptData = imw_num_rows($rsGetApptData);
if($numRowGetApptData>0){
	extract(imw_fetch_array($rsGetApptData));	
}
//end get patient Appointment data//


for($i=0;$i<count($consentDetail);$i++){	
	$consent_cat_id = stripslashes(html_entity_decode($consentDetail[$i]['consent_cat_id']));//$patient_id
	//$consentDetail[$i]['consent_form_content_data'] = stripslashes($consentDetail[$i]['consent_form_content_data']);
	//--- change value between curly brackets -------	
	
	
	$consent_form_content = str_ireplace('{PATIENT NAME TITLE}',ucwords($patientDetails[0]['title']),$consent_form_content);
	$consent_form_content = str_ireplace($web_root.'/interface/common/'.$htmlFolder.'/','',$consent_form_content);
	$consent_form_content = str_ireplace($web_root.'/interface/common/html2pdf/','',$consent_form_content);
	$consent_form_content = str_ireplace($web_root.'/interface/common/new_html2pdf/','',$consent_form_content);
	$consent_form_content = str_ireplace('interface/common/html2pdf/','',$consent_form_content);
	$consent_form_content = str_ireplace('interface/common/new_html2pdf/','',$consent_form_content);
	$consent_form_content = str_ireplace($web_root.'/interface/main/uploaddir/document_logos/','../../main/uploaddir/document_logos/',$consent_form_content);
	$consent_form_content = str_ireplace('%20',' ',$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT FIRST NAME}',ucwords($patientDetails[0]['fname']),$consent_form_content);
	$consent_form_content = str_ireplace('{MIDDLE NAME}',ucwords($patientDetails[0]['mname']),$consent_form_content);
	$consent_form_content = str_ireplace('{LAST NAME}',ucwords($patientDetails[0]['lname']),$consent_form_content);
	$consent_form_content = str_ireplace('{SEX}',ucwords($patientDetails[0]['sex']),$consent_form_content);
	$consent_form_content = str_ireplace('{DOB}',ucwords($patientDetails[0]['pat_dob']),$consent_form_content);
	$consent_form_content = str_ireplace('{AGE}',$patient_age,$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT SS}',ucwords($patientDetails[0]['ss']),$consent_form_content);
	//=============START WORK TO SHOW THE LAST 4 DIGIT PATIENT SS==========================
	if(trim($patientDetails[0]['ss'])!=''){
		$consent_form_content = str_ireplace('{PATIENT_SS4}',ucwords(substr_replace($patientDetails[0]['ss'],'XXX-XX',0,6)),$consent_form_content);
	}else{
		$consent_form_content = str_ireplace('{PATIENT_SS4}','',$consent_form_content);
	}
	//===========================END WORK===================================================
	$consent_form_content = str_ireplace('{PHYSICIAN NAME}',ucwords($phy_name),$consent_form_content);
	$consent_form_content = str_ireplace('{MARITAL STATUS}',ucwords($patientDetails[0]['status']),$consent_form_content);
	$consent_form_content = str_ireplace('{ADDRESS1}',ucwords($patientDetails[0]['street']),$consent_form_content);
	$consent_form_content = str_ireplace('{ADDRESS2}',ucwords($patientDetails[0]['street2']),$consent_form_content);
	$consent_form_content = str_ireplace('{HOME PHONE}',ucwords($patientDetails[0]['phone_home']),$consent_form_content);
	$consent_form_content = str_ireplace('{EMERGENCY CONTACT}',ucwords($patientDetails[0]['contact_relationship']),$consent_form_content);
	$consent_form_content = str_ireplace('{EMERGENCY CONTACT PH}',ucwords($patientDetails[0]['phone_contact']),$consent_form_content);
	$consent_form_content = str_ireplace('{MOBILE PHONE}',ucwords($patientDetails[0]['phone_cell']),$consent_form_content);
	$consent_form_content = str_ireplace('{WORK PHONE}',ucwords($patientDetails[0]['phone_biz']),$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT CITY}',ucwords($patientDetails[0]['city']),$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT STATE}',ucwords($patientDetails[0]['state']),$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT ZIP}',ucwords($patientDetails[0]['postal_code']),$consent_form_content);
	$consent_form_content = str_ireplace('{REGISTRATION DATE}',ucwords($patientDetails[0]['reg_date']),$consent_form_content);
	$consent_form_content = str_ireplace('{REFFERING PHY.}',ucwords($reffer_name),$consent_form_content);
	$consent_form_content = str_ireplace('{POS FACILITY}',ucwords($pos_facility_name),$consent_form_content);
	$consent_form_content = str_ireplace('{DRIVING LICENSE}',ucwords($patientDetails[0]['driving_licence']),$consent_form_content);
	$consent_form_content = str_ireplace('{HEARD ABOUT US}',ucwords($patientDetails[0]['heard_abt_us']),$consent_form_content);
	$consent_form_content = str_ireplace('{HEARD ABOUT US DETAIL}',$patientDetails[0]['heard_abt_desc'],$consent_form_content);

	$consent_form_content = str_ireplace('{EMAIL ADDRESS}',$patientDetails[0]['email'],$consent_form_content);
	$consent_form_content = str_ireplace('{USER DEFINE 1}',$patientDetails[0]['genericval1'],$consent_form_content);
	$consent_form_content = str_ireplace('{USER DEFINE 2}',$patientDetails[0]['genericval2'],$consent_form_content);

	//=============RESPONSIBLE PARTY DATA REPLACEMENT-I======================================================
	//=============NOW IF PATIENT HAVE NO RESPONSILE PERSON THEN PATIENT DATA WILL BE REPLACED.=============
	if(count($resDetails)>0){
		$consent_form_content = str_ireplace('{RES.PARTY TITLE}',ucwords($resDetails[0]['title']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($resDetails[0]['fname']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY MIDDLE NAME}',ucwords($resDetails[0]['mname']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY LAST NAME}',ucwords($resDetails[0]['lname']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY DOB}',ucwords($resDetails[0]['res_dob']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY SS}',ucwords($resDetails[0]['ss']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY SEX}',ucwords($resDetails[0]['sex']),$consent_form_content);
		$strToShowRelation = $resDetails[0]['relation'];
		if(strtolower($resDetails[0]['relation']) == "doughter"){
			$strToShowRelation = "Daughter";
		}
		$consent_form_content = str_ireplace('{RES.PARTY RELATION}',ucwords($strToShowRelation),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY ADDRESS1}',ucwords($resDetails[0]['address']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY ADDRESS2}',ucwords($resDetails[0]['address2']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY HOME PH.}',ucwords($resDetails[0]['home_ph']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY WORK PH.}',ucwords($resDetails[0]['work_ph']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY MOBILE PH.}',ucwords($resDetails[0]['mobile']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY CITY}',ucwords($resDetails[0]['city']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY STATE}',ucwords($resDetails[0]['state']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY ZIP}',ucwords($resDetails[0]['zip']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY MARITAL STATUS}',ucwords($resDetails[0]['marital']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY DD NUMBER}',ucwords($resDetails[0]['licence']),$consent_form_content);
	}else{
		$consent_form_content = str_ireplace('{RES.PARTY TITLE}',ucwords($patientDetails[0]['title']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($patientDetails[0]['fname']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY MIDDLE NAME}',ucwords($patientDetails[0]['mname']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY LAST NAME}',ucwords($patientDetails[0]['lname']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY DOB}',ucwords($patientDetails[0]['pat_dob']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY SS}',ucwords($patientDetails[0]['ss']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY SEX}',ucwords($patientDetails[0]['sex']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY RELATION}','Self',$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY ADDRESS1}',ucwords($patientDetails[0]['street']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY ADDRESS2}',ucwords($patientDetails[0]['street2']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY HOME PH.}',ucwords($patientDetails[0]['phone_home']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY WORK PH.}',ucwords($patientDetails[0]['phone_biz']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY MOBILE PH.}',ucwords($patientDetails[0]['phone_cell']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY CITY}',ucwords($patientDetails[0]['city']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY STATE}',ucwords($patientDetails[0]['state']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY ZIP}',ucwords($patientDetails[0]['postal_code']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY MARITAL STATUS}',ucwords($patientDetails[0]['status']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY DD NUMBER}',ucwords($patientDetails[0]['driving_licence'])	,$consent_form_content);
	}
	//=====================================THE END REPONSIBLE PARTY DATA-I========================================
	
	//--- change epmoyee detail of patient ---
	$consent_form_content = str_ireplace('{PATIENT OCCUPATION}',ucwords($patientDetails[0]['occupation']),$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT EMPLOYER}',ucwords($empDetails[0]['name']),$consent_form_content);
	$consent_form_content = str_ireplace('{OCCUPATION ADDRESS1}',ucwords($empDetails[0]['street']),$consent_form_content);
	$consent_form_content = str_ireplace('{OCCUPATION ADDRESS2}',ucwords($empDetails[0]['street2']),$consent_form_content);
	$consent_form_content = str_ireplace('{OCCUPATION CITY}',ucwords($empDetails[0]['city']),$consent_form_content);
	$consent_form_content = str_ireplace('{OCCUPATION STATE}',ucwords($empDetails[0]['state']),$consent_form_content);
	$consent_form_content = str_ireplace('{OCCUPATION ZIP}',ucwords($empDetails[0]['postal_code']),$consent_form_content);
	$consent_form_content = str_ireplace('{MONTHLY INCOME}',''.show_currency().''.number_format($patientDetails[0]['monthly_income'],2),$consent_form_content);
	$consent_form_content = str_ireplace('{DATE}',get_date_format(date('Y-m-d')),$consent_form_content);
	$consent_form_content = str_ireplace('{DATE_F}',date("F d, Y"),$consent_form_content);
	$consent_form_content = str_ireplace('{TIME}',date('h:i A'),$consent_form_content);
	$consent_form_content = str_ireplace('{OPERATOR NAME}',ucwords(trim($operator_name)),$consent_form_content);
	$consent_form_content = str_ireplace('{OPERATOR INITIAL}',ucwords($operator_initial),$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT INITIAL}',ucwords($patient_initial),$consent_form_content);
	$consent_form_content = str_ireplace('{TEXTBOX_XSMALL}',"",$consent_form_content);
	$consent_form_content = str_ireplace('{TEXTBOX_SMALL}',"",$consent_form_content);
	$consent_form_content = str_ireplace('{TEXTBOX_MEDIUM}',"",$consent_form_content);
	$consent_form_content = str_ireplace('{TEXTBOX_LARGE}',"",$consent_form_content);
	
	$consent_form_content = str_ireplace('{TEXTBOX_LARGE}',"",$consent_form_content);
	//replacing Primary insurence data
	$consent_form_content = str_ireplace('{PRIMARY INSURANCE COMPANY}',ucwords($priInsCompName),$consent_form_content);
	$consent_form_content = str_ireplace('{PRIMARY POLICY #}',ucwords($priPolicyNumber),$consent_form_content);
	$consent_form_content = str_ireplace('{PRIMARY GROUP #}',ucwords($priGroupNumber),$consent_form_content);
	$consent_form_content = str_ireplace('{PRIMARY SUBSCRIBER NAME}',ucwords($priSubscriberName),$consent_form_content);
	$consent_form_content = str_ireplace('{PRIMARY SUBSCRIBER RELATIONSHIP}',ucwords($priSubscriberRelation),$consent_form_content);
	$consent_form_content = str_ireplace('{PRIMARY BIRTHDATE}',ucwords($priSubscriberDOB),$consent_form_content);
	$consent_form_content = str_ireplace('{PRIMARY SOCIAL SECURITY}',ucwords($priSubscriberSS),$consent_form_content);
	$consent_form_content = str_ireplace('{PRIMARY PHONE}',ucwords($priSubscriberPhone),$consent_form_content);
	$consent_form_content = str_ireplace('{PRIMARY ADDRESS}',ucwords($priSubscriberStreet),$consent_form_content);
	$consent_form_content = str_ireplace('{PRIMARY CITY}',ucwords($priSubscriberCity),$consent_form_content);
	$consent_form_content = str_ireplace('{PRIMARY STATE}',ucwords($priSubscriberState),$consent_form_content);
	$consent_form_content = str_ireplace('{PRIMARY ZIP}',ucwords($priSubscriberZip),$consent_form_content);
	$consent_form_content = str_ireplace('{PRIMARY EMPLOYER}',ucwords($priSubscriberEmployer),$consent_form_content);
	//
	//replacing Secondary insurence data
	$consent_form_content = str_ireplace('{SECONDARY INSURANCE COMPANY}',ucwords($secInsCompName),$consent_form_content);
	$consent_form_content = str_ireplace('{SECONDARY POLICY #}',ucwords($secPolicyNumber),$consent_form_content);
	$consent_form_content = str_ireplace('{SECONDARY GROUP #}',ucwords($secGroupNumber),$consent_form_content);
	$consent_form_content = str_ireplace('{SECONDARY SUBSCRIBER NAME}',ucwords($secSubscriberName),$consent_form_content);
	$consent_form_content = str_ireplace('{SECONDARY SUBSCRIBER RELATIONSHIP}',ucwords($secSubscriberRelation),$consent_form_content);
	$consent_form_content = str_ireplace('{SECONDARY BIRTHDATE}',ucwords($secSubscriberDOB),$consent_form_content);
	$consent_form_content = str_ireplace('{SECONDARY SOCIAL SECURITY}',ucwords($secSubscriberSS),$consent_form_content);
	$consent_form_content = str_ireplace('{SECONDARY PHONE}',ucwords($secSubscriberPhone),$consent_form_content);
	$consent_form_content = str_ireplace('{SECONDARY ADDRESS}',ucwords($secSubscriberStreet),$consent_form_content);
	$consent_form_content = str_ireplace('{SECONDARY CITY}',ucwords($secSubscriberCity),$consent_form_content);
	$consent_form_content = str_ireplace('{SECONDARY STATE}',ucwords($secSubscriberState),$consent_form_content);
	$consent_form_content = str_ireplace('{SECONDARY ZIP}',ucwords($secSubscriberZip),$consent_form_content);
	$consent_form_content = str_ireplace('{SECONDARY EMPLOYER}',ucwords($secSubscriberEmployer),$consent_form_content);
	$consent_form_content = str_ireplace('{PatientID}',$_SESSION['patient'],$consent_form_content);
	$consent_form_content = str_ireplace('{Appt Date}',$appDate,$consent_form_content);
	$consent_form_content = str_ireplace('{Appt Time}',$appTime,$consent_form_content);
	$consent_form_content = str_ireplace('{Appt Proc}',$ptProc,$consent_form_content);
	
	$consent_form_content = str_ireplace('{OPERATOR INITIALS}',"",$consent_form_content);
	
	$languageShow 		  = str_ireplace("Other -- ","",$patientDetails[0]['language']);
	$raceShow			  = trim($patientDetails[0]["race"]);
	$otherRace			  = trim($patientDetails[0]["otherRace"]);
	if($otherRace) { 
		$raceShow		  = $otherRace;
	}
	$ethnicityShow		  = trim($patientDetails[0]["ethnicity"]);			
	$otherEthnicity		  = trim($patientDetails[0]["otherEthnicity"]);
	if($otherEthnicity) { 
		$ethnicityShow	  = $otherEthnicity;
	}
	$consent_form_content = str_ireplace('{RACE}',$raceShow,$consent_form_content);
	$consent_form_content = str_ireplace('{LANGUAGE}',$languageShow,$consent_form_content);
	$consent_form_content = str_ireplace('{ETHNICITY}',$ethnicityShow,$consent_form_content);
	
	$consent_form_content = str_ireplace('{PCP NAME}',$pcpName,$consent_form_content);
	$consent_form_content = str_ireplace('{PCP STREET ADDR}',$pcpAddress,$consent_form_content);
	$consent_form_content = str_ireplace('{PCP City}',	$pcpPhyDetail[0]['pcpCity'],$consent_form_content);
	$consent_form_content = str_ireplace('{PCP State}',$pcpPhyDetail[0]['pcpState'],$consent_form_content);
	$consent_form_content = str_ireplace('{PCP ZIP}',	$pcpPhyDetail[0]['pcpZipCode'],$consent_form_content);
	
	$consent_form_content = str_ireplace('{REF PHYSICIAN TITLE}',		trim(ucwords($reffPhyDetail[0]['Title'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHYSICIAN FIRST NAME}',	trim(ucwords($reffPhyDetail[0]['FirstName'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHYSICIAN LAST NAME}',	trim(ucwords($reffPhyDetail[0]['LastName'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY SPECIALITY}',		trim(ucwords($reffPhyDetail[0]['specialty'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY PHONE}',			trim(ucwords($reffPhyDetail[0]['physician_phone'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY STREET ADDR}',		$refPhyAddress,$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY CITY}',				trim(ucwords($reffPhyDetail[0]['City'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY STATE}',			trim(ucwords($reffPhyDetail[0]['State'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY ZIP}',				trim(ucwords($reffPhyDetail[0]['ZipCode'])),$consent_form_content);
	$consent_form_content = str_ireplace('{LOGGED_IN_FACILITY_NAME}',$loggedfacName,$consent_form_content);
	$consent_form_content = str_ireplace('{LOGGED_IN_FACILITY_ADDRESS}',$loggedfacAddress,$consent_form_content);
	
	
	//
	$consent_form_content = str_ireplace($web_root."/interface/SigPlus_images/","",$consent_form_content);
	$consent_form_content = str_ireplace("{PHYSICIAN SIGNATURE}","",$consent_form_content);	
	$consent_form_content = str_ireplace("{WITNESS SIGNATURE}","",$consent_form_content);	
	
	if ($chk == 0)
	{
		$consent_form_content = str_ireplace('{SIGNATURE}',"",$consent_form_content);
		
	}
}
	//-- get signature value -------
	/*$qry = "select form_information_id from patient_consent_form_information 
			where consent_form_id = '$consent_form_id' and patient_id = '$patient_id'";
	$consentDetailsInfo = get_array_records_query($qry);*/
	
	//$form_information_id = $consentDetailsInfo[0]['form_information_id'];
	/*$qry = "select signature_content from consent_form_signature 
			where form_information_id = '$form_information_id' and patient_id = '$patient_id'
			and consent_form_id = '$consent_form_id' and signature_status = 'Active' order by signature_count";*/
	
	$qry = "select signature_image_path,signature_count from consent_form_signature 
			where form_information_id = '$form_information_id' and patient_id = '$patient_id'
			and consent_form_id = '$consent_form_id' and signature_status = 'Active' order by signature_count";
	/*$qry = "select signature_content from consent_form_signature 
		where patient_id = '$patient_id'
		and consent_form_id = '$consent_form_id' and signature_status = 'Active'";*/
	 $sigDetail = get_array_records_query($qry);

	 if ($sigDetail){

	$sig_con = array();
	for($s=0;$s<count($sigDetail);$s++){
		$sig_con[$s] = $sigDetail[$s]['signature_image_path'];
		$signature_count[$s] = $sigDetail[$s]['signature_count'];
	}
	
$deletePath=array();
	for($ps=0;$ps<count($sig_con);$ps++){
	$row_arr = explode('{START APPLET ROW}',$consent_form_content);
	$sig_arr = explode('{SIGNATURE}',$row_arr[0]);	
	$sig_data = '';
	$ds=0;
	$coun=0;
	for($s=1;$s<count($sig_arr);$s++){
			if($s==$signature_count[$ds]){
				$postData = $sig_con[$coun];
				$path1 = explode("/",$postData);
					if(isset($path1[1]) && !empty($path1[1])){
						if($htmlV2Class==true && file_exists($postData)) {
						$sig_data = '<table>
						<tr>
							<td>
								<img src="'.$postData.'" height="80" width="240">
							</td>
						</tr></table>';
						}else{
						$sig_data = '<table>
						<tr>
							<td>
								<img src="'.$path1[1].'" height="80" width="240">
							</td>
						</tr></table>';
						}
						$str_data = $sig_arr[$s];
						$sig_arr[$s] = $sig_data;
						$sig_arr[$s] .= $str_data;
						$hiddenFields[] = true;
						
					}
					$coun++;
				$ds++;
			}
	}
	$consent_form_content = implode(' ',$sig_arr);
	$content_row = '';
	for($ro=1;$ro<count($row_arr);$ro++){
		if($row_arr[$ro]){
			$sig_arr1 = explode('{SIGNATURE}',$row_arr[$ro]);
			$td_sign = '';
			for($t=0;$t<count($sig_arr1)-1;$t++,$ds++){
				$sig_arr1[$t] = str_ireplace('&nbsp;','',$sig_arr1[$t]);
				$td_sign .= '
					<td align="left">
						<table border="0">
							<tr><td>'.$sig_arr1[$t].'</td></tr>
							<tr>
								<td style="border:solid 1px" bordercolor="#FF9900">
									{SIGNATURE}
								</td>
							</tr>
						</table>
					</td>	
				';
				$s++;
				$hiddenFields[] = true;
			}
			$content_row .= '<br>'.$td_sign;
			/*
			$content_row .= '
				<table width="145" border="1" align="center">
					<tr>
						'.$td_sign.'						
					</tr>
				</table>
			';*/
		}
	}
	$jh = 1;
	$consent_form_content .= $content_row;
	}
}
else
{
	$consent_form_content = str_ireplace('{SIGNATURE}',"",$consent_form_content);
}
	/*
	$consent_content .= '
		<table id="content_'.$consent_form_id.'" style="display:'.$display.'" width="100%" align="center" cellpadding="1" cellspacing="1" border="0">
			<tr>
				<td align="center" colspan="'.count($sig_arr).'">'.$consent_form_content.'</td>
			<tr>
		</table>
	';*/

$consent_form_content = str_ireplace('&nbsp;',' ',$consent_form_content);
if($htmlV2Class==false) {
	$consent_form_content = str_ireplace('</div>','<br>',$consent_form_content);
}
$consent_form_content = str_ireplace("text' name='medium' size='60' maxlength='60'>",'',$consent_form_content);
$inputVal = explode('<input',$consent_form_content);

$consent_form_content = $inputVal[0];
for($i=1;$i<count($inputVal);$i++){
	$pos = strpos($inputVal[$i],'value="');
	$str = substr($inputVal[$i],$pos+7);
	$pos1 = strpos($str,'"');
	$inputVals = substr($str,0,$pos1);
	$pos2 = strpos($str,'>');
	$lastVal = substr($str,$pos2+1);
	$consent_form_content .= $inputVals.' '.$lastVal;
}
/// Code To Replace  {TEXTBOX_LARGE}  Textarea////
$inputValTextarea=explode('<textarea rows="2" cols="100" name="large',$consent_form_content);
if(is_array($inputValTextarea)){
	for($i=1;$i<count($inputValTextarea);$i++){
		$consent_form_content = str_ireplace('<textarea rows="2" cols="100" name="large">','',$consent_form_content);
		$consent_form_content = str_ireplace('<textarea rows="2" cols="100" name="large'.$i.'">','',$consent_form_content);
		$consent_form_content = str_ireplace('</textarea>','',$consent_form_content);
 }
}
/// Code To Replace  {TEXTBOX_LARGE}  Textarea////
$consent_form_content=str_ireplace('align="justify"','align="left"',$consent_form_content);
$consent_form_content=str_ireplace('"text-align: justify"','"text-align: left"',$consent_form_content);
if($htmlV2Class==false) {
	$consent_form_content = strip_tags($consent_form_content,'<strong> </strong><img> <p><page> <page_header> <br>');
}


//end creating barcode image

if($htmlV2Class==true){
	$consent_form_content=str_ireplace("<div st</page>","</page>",$consent_form_content);
}
$consent_form_content = str_ireplace($web_root.'/data/'.PRACTICE_PATH.'/','../../data/'.PRACTICE_PATH.'/',$consent_form_content);
$consent_form_content = str_ireplace($web_root.'/library/images/','../../library/images/',$consent_form_content);
$consent_form_content = str_ireplace('../../../library/images/','../../library/images/',$consent_form_content);

$fld_path='../../../library/'.$htmlFolder.'/';
$html_file_name='pdffile.html';
$path_set=$fld_path.$html_file_name;
//$fp = fopen($path_set,'w');
//$write_data = fwrite($fp,html_entity_decode($consent_form_content));
$file_path = '';
$file_path = write_html(utf8_decode(html_entity_decode(stripslashes($consent_form_content))));
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Surgery Consent Form</title>
        <meta name="viewport" content="width=device-width, maximum-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
        <script>
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
		</script>
    </head>
	<body class="bg-white">
		<?php
        if($file_path){
             $_REQUEST["consent"]='print';
             if ($_REQUEST["consent"]) {?>
                <script type="text/javascript">
                if(typeof(top.btn_show)=='function'){
                    top.btn_show();
                }
                var htmlFilePth = "<?php echo $htmlFilePth;?>";
                var browserIpad = "<?php echo $browserIpad;?>";
                var html_File_name="<?php echo $html_file_name; ?>";
                if(browserIpad=="yes") {
                    //window.open('../../../library/'+htmlFilePth+'?font_size=10&page=4&file_location='+html_File_name,'_blank','');	
                	html_to_pdf('<?php echo $file_path; ?>','p');
				}else {
                    //window.location = "../../../library/"+htmlFilePth+"?font_size=10&page=4&tree=yes&file_location="+html_File_name;
					html_to_pdf('<?php echo $file_path; ?>','p','',true);
				}
                </script>
                <?php } else{ ?>
                <script type="text/javascript">		
                if(typeof(top.btn_show)=="function")top.btn_show();
                var htmlFilePth = "<?php echo $htmlFilePth;?>";
                html_to_pdf('<?php echo $file_path; ?>','p','',true);
				//window.location = "../../../library/"+htmlFilePth+"?font_size=10&page=4&tree=yes&file_location="+html_File_name;
                <?php } ?>
            </script>
            <?php
        }
        ?>
    </body>
</html>