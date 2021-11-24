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

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");

header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");

include_once("../../../config/globals.php");

$library_path = $GLOBALS['webroot'].'/library';
$patient_id = $_SESSION['patient'];
$browserIpad = 'no';

if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true) {
	$browserIpad = 'yes';
}


//include_once('../../main/Functions.php');
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
include_once($GLOBALS['fileroot']."/library/classes/common_function.php");
include_once($GLOBALS['fileroot']."/library/classes/print_pt_key.php");
include_once($GLOBALS['fileroot']."/library/classes/functions_ptInfo.php");
include_once($GLOBALS['fileroot'].'/library/bar_code/code128/code128.class.php');
include_once($GLOBALS['fileroot']."/library/classes/work_view/ChartAP.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/MedHx.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/Patient.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/CcHx.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/PnTempParser.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/wv_functions.php");
$oSaveFile = new SaveFile($patient_id);
$obj_print_pt_key=new print_pt_key;
if($_SESSION['patient']){
	$upcoming_appt=$obj_print_pt_key->getApptFuture($_SESSION['patient'],'','','n');
}
$objParser = new PnTempParser;
$medHx=$objParser->getMedHx_public($_SESSION['patient']);
$ocularMed=$objParser->get_med_list($_SESSION['patient'],4);
$systemicMed=$objParser->get_med_list($_SESSION['patient'],1);
$allergyList=$objParser->get_med_list($_SESSION['patient'],7);
$ins_cases=$objParser->all_ins_case($_SESSION['patient']);

$form_id = (isset($_SESSION['form_id']) && $_SESSION['form_id'] != "") ? $_SESSION['form_id'] : '' ;
if(!$form_id){
	$qry_form_id="Select id from chart_master_table where patient_id='".$_SESSION['patient']."'  order by id desc";
	$res_form_id=imw_query($qry_form_id);
	$row_form_id=imw_fetch_assoc($res_form_id);
	$form_id=$row_form_id['id'];
}
if($_SESSION['patient'] && $form_id){
	$cc_hx=$objParser->get_cc_hx($_SESSION['patient'],$form_id);
	$cc_val=$cc_hx[1];
	$hx_val=$cc_hx[2];
}

//------NEW APPOINTMENT VARIABLE INFORMATION---------------------------
$apptInfoArr2 = $obj_print_pt_key->getApptInfo($patient_id,'','','',1);

//======LOGGED IN FACILITY INFO WORKS WORKS STARTS HERE================
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
//======ENDS HERE======================================================
//======GET PHYSICIAN ID WORKS STARTS HERE============================= 
if(!function_exists("cfd_getProIdFromChart")){
	function cfd_getProIdFromChart(){
		$pro_id=0;
		$sql = "SELECT providerId FROM chart_master_table where patient_id = '".$_SESSION['patient']."' AND delete_status='0' AND purge_status='0' AND date_of_service=CURDATE() ORDER BY create_dt DESC,  id DESC  LIMIT 0, 1 ";
		$row=get_row_record_query($sql);
		if($row!=false){	$pro_id=$row["providerId"];}
		return $pro_id;
	}
}
$pro_id = cfd_getProIdFromChart();//1
if(empty($pro_id)){
	$qry_pro_id="select sa_doctor_id from schedule_appointments
				where sa_patient_app_status_id not in (201, 18, 203, 19, 20)
				and sa_patient_id = '".$patient_id."' and sa_app_start_date <= now()
				order by sa_app_start_date desc, sa_app_starttime desc limit 0, 1";
	$res_pro_id=imw_query($qry_pro_id);
	$row_pro_id=imw_fetch_assoc($res_pro_id);	
	$pro_id=$row_pro_id['sa_doctor_id'];
	if($pro_id=="" || $pro_id==0){
		$pro_id = $patientDetails[0]['providerID'];	
	}
}  
//==========================ENDS HERE=====================================================
//---- get Patient details ---------
$qry = "select *,date_format(DOB,'".get_sql_date_format()."') as pat_dob,date_format(date,'".get_sql_date_format()."') as reg_date
		from patient_data where id = '$patient_id'";
$patientDetails = get_array_records_query($qry);
$patient_initial = substr($patientDetails[0]['fname'],0,1);
$patient_initial .= substr($patientDetails[0]['lname'],0,1);
list($year, $month, $day) = explode('-',$patientDetails[0]['DOB']);
$pat_date = $year."-".$month."-".$day;
$patient_age = get_age($pat_date);
//=============PHYSICIAN DETAILS==================================
//$pro_id = $patientDetails[0]['providerID'];
$qry = "select concat(lname,', ',fname) as name,mname,lname,fname from users where id = '$pro_id'";
$phyDetail = get_array_records_query($qry);
$phy_name = ucwords(trim($phyDetail[0]['name'].' '.$phyDetail[0]['mname']));
$phy_fname = ucwords(trim($phyDetail[0]['fname']));
$phy_mname = ucwords(trim($phyDetail[0]['mname']));
$phy_lname = ucwords(trim($phyDetail[0]['lname']));
//=========================ENDS HERE==============================
//--- get reffering physician name --------
$primary_care_id = $patientDetails[0]['primary_care_id'];
$qry = "select concat(LastName,', ',FirstName) as name , FirstName, LastName, MiddleName,Title, specialty, physician_phone, physician_fax, Address1, Address2, ZipCode, City, State from refferphysician
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
$qry = "select *,date_format(dob,'".get_sql_date_format()."') as res_dob 
		from resp_party where patient_id = '$patient_id'";
$resDetails = get_array_records_query($qry);
//--- get epmoyee detail of patient ---
$qry = "select * from employer_data where pid = '$patient_id'";
$empDetails = get_array_records_query($qry);

$qry = "select pcfi.consent_form_content_data,pcfi.form_created_date,pcfi.package_category_id as consent_package_category_id, pcfi.consent_form_id, cf.cat_id as consent_cat_id from patient_consent_form_information pcfi
		LEFT JOIN consent_form cf ON (cf.consent_form_id = pcfi.consent_form_id)
		WHERE pcfi.consent_form_id = '$consent_form_id' and pcfi.patient_id = '$patient_id' and pcfi.form_information_id  = '$form_information_id'";
$consentDetail = get_array_records_query($qry);
$chk = count($consentDetail);
if(count($consentDetail) == 0){
	$qry = "select *,consent_form_content as consent_form_content_data, cat_id as consent_cat_id 
			from consent_form where consent_form_id = '$consent_form_id'";
	$consentDetail = get_array_records_query($qry);
	if($_REQUEST["package_category_id"]!="") {
		$consentDetail[0]["consent_package_category_id"] = $_REQUEST["package_category_id"];
	}
	//echo "<pre>";
	//echo $consentDetail[0]['consent_form_content_data'];
	//exit;
}
//get Primary insurence data//
$qryGetInsPriData = "select ind.provider as priInsComp,
					 ind.policy_number as priPolicyNumber,
					 ind.group_number as priGroupNumber,
					 CONCAT(ind.subscriber_fname,'&nbsp;',ind.subscriber_lname)as priSubscriberName,
					 ind.subscriber_relationship as priSubscriberRelation,
					 date_format(ind.subscriber_DOB,'".get_sql_date_format()."') as priSubscriberDOB,
					 ind.subscriber_ss as priSubscriberSS,
					 ind.subscriber_phone as priSubscriberPhone,
					 ind.subscriber_street as priSubscriberStreet,
					 ind.subscriber_city as priSubscriberCity,
					 ind.subscriber_state as priSubscriberState,
					 ind.subscriber_postal_code as priSubscriberZip,
					 ind.subscriber_employer as priSubscriberEmployer
					 from insurance_data as ind INNER JOIN insurance_case as ic
					 on ind.ins_caseid=ic.ins_caseid
					 JOIN insurance_case_types as ict  
					 on ict.case_id=ic.ins_case_type
					 where ind.pid = '".$_SESSION['patient']."' and 
					 ind.ins_caseid > 0 and
					 ind.type = 'primary' 
					 and ind.actInsComp = 1
					 and ind.provider > 0 ORDER BY ict.normal DESC LIMIT 0,1
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
$qryGetInsSecData = "select ind.provider as secInsComp,
					 ind.policy_number as secPolicyNumber,
					 ind.group_number as secGroupNumber,
					 CONCAT(ind.subscriber_fname,'&nbsp;',ind.subscriber_lname)as secSubscriberName,
					 ind.subscriber_relationship as secSubscriberRelation,
					 date_format(ind.subscriber_DOB,'".get_sql_date_format()."') as secSubscriberDOB,
					 ind.subscriber_ss as secSubscriberSS,
					 ind.subscriber_phone as secSubscriberPhone,
					 ind.subscriber_street as secSubscriberStreet,
					 ind.subscriber_city as secSubscriberCity,
					 ind.subscriber_state as secSubscriberState,
					 ind.subscriber_postal_code as secSubscriberZip,
					 ind.subscriber_employer as secSubscriberEmployer
					 from insurance_data as ind INNER JOIN insurance_case as ic
					 on ind.ins_caseid=ic.ins_caseid
					 JOIN insurance_case_types as ict  
					 on ict.case_id=ic.ins_case_type
					 where ind.pid = '".$_SESSION['patient']."' and 
					 ind.ins_caseid > 0 and
					 ind.type = 'secondary' 
					 and ind.actInsComp = 1
					 and ind.provider > 0 ORDER BY ict.normal DESC LIMIT 0,1
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
$pcpAddress = '';
$pcpAddress .= (!empty($pcpPhyDetail[0]['pcpAddress1'])) ? trim($pcpPhyDetail[0]['pcpAddress1']) : "";
$pcpAddress .= (!empty($pcpPhyDetail[0]['pcpAddress2'])) ? "<br>".trim($pcpPhyDetail[0]['pcpAddress2']) : "";

//end get Secondary insurence data//
//get patient Appointment data//
$qryGetApptData = "select sa.sa_app_start_date,DATE_FORMAT(sa.sa_app_start_date, '%a ".get_sql_date_format('','y','/')."')  as appDate,
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

//START CODE TO SET NEW CLASS TO CONSENT FORMS
$htmlFolder = "html_to_pdf";
$htmlV2Class = true;
$htmlFilePth = "html_to_pdf/createPdf.php";

//http host & protocol for replace sign path
$http_host=$_SERVER['HTTP_HOST'];
if($protocol==''){ $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://'; }


//END CODE TO SET NEW CLASS TO CONSENT FORMS
for($i=0;$i<count($consentDetail);$i++){	
	$consent_cat_id = $consentDetail[$i]['consent_cat_id'];//$patient_id
	$consent_package_category_id = $consentDetail[$i]['consent_package_category_id'];//$patient_id
	$consentDetail[$i]['consent_form_content_data'] = stripslashes(html_entity_decode($consentDetail[$i]['consent_form_content_data']));
	//--- change value between curly brackets -------	
	
	
	$consent_form_content = str_ireplace('{PATIENT NAME TITLE}',ucwords($patientDetails[0]['title']),$consentDetail[$i]['consent_form_content_data']);

	//$consent_form_content = str_ireplace($GLOBALS['webroot']."/interface/common/new_html2pdf/","",$consent_form_content);
	//$consent_form_content = str_ireplace("interface/common/html2pdf/","",$consent_form_content);
	//$consent_form_content = str_ireplace($protocol.$http_host.$GLOBALS['webroot'].'/interface/common/new_html2pdf/','',$consent_form_content);
	//$consent_form_content = str_ireplace($GLOBALS['webroot'].'/interface/common/'.$htmlFolder.'/','',$consent_form_content);
	//$consent_form_content = str_ireplace($GLOBALS['webroot'].'/interface/common/html2pdf/','',$consent_form_content);
	//$consent_form_content = str_ireplace($GLOBALS['webroot'].'/interface/common/new_html2pdf/','',$consent_form_content);
	//$consent_form_content = str_ireplace('interface/common/html2pdf/','',$consent_form_content);
	//$consent_form_content = str_ireplace('interface/common/new_html2pdf/','',$consent_form_content);
	//$consent_form_content = str_ireplace("/iMedicR4/interface/common/new_html2pdf/","",$consent_form_content);

    $consent_form_content = str_ireplace($GLOBALS['webroot'].'/interface/common/new_html2pdf/','../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/',$consent_form_content);
	$consent_form_content = str_ireplace($protocol.$http_host.$GLOBALS['webroot'].'/interface/common/new_html2pdf/','../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/',$consent_form_content);
	$consent_form_content = str_ireplace($GLOBALS['webroot'].'/interface/common/'.$htmlFolder.'/','../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/',$consent_form_content);
	$consent_form_content = str_ireplace($GLOBALS['webroot'].'/interface/common/html2pdf/','../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/',$consent_form_content);
	$consent_form_content = str_ireplace($GLOBALS['webroot'].'/interface/common/new_html2pdf/','../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/',$consent_form_content);
	$consent_form_content = str_ireplace('interface/common/html2pdf/','../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/',$consent_form_content);
	$consent_form_content = str_ireplace('interface/common/new_html2pdf/','../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/',$consent_form_content);
	$consent_form_content = str_ireplace('/iMedicR4/interface/common/new_html2pdf/','../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/',$consent_form_content);
	$consent_form_content = str_ireplace($GLOBALS['webroot']."/library/common/html_to_pdf/","",$consent_form_content); 
	if($GLOBALS['webroot']!=''){
		$consent_form_content = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/','../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/',$consent_form_content);
    }
	$consent_form_content = str_ireplace($protocol.$http_host.$GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$consent_form_content);
	$consent_form_content = str_ireplace($protocol.$myExternalIP.$GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$consent_form_content);
	$consent_form_content = str_ireplace($webServerRootDirectoryName.$GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$consent_form_content);
	//$consent_form_content = str_ireplace($GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$consent_form_content);
	//$consent_form_content = str_ireplace($protocol.$http_host.$GLOBALS['webroot'].'/redactor/images/','',$consent_form_content);
	//$consent_form_content = str_ireplace($GLOBALS['webroot'].'/redactor/images/','',$consent_form_content);
	$consent_form_content = str_ireplace('%20',' ',$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT FIRST NAME}',ucwords($patientDetails[0]['fname']),$consent_form_content);
	$consent_form_content = str_ireplace('{MIDDLE NAME}',ucwords($patientDetails[0]['mname']),$consent_form_content);
	$consent_form_content = str_ireplace('{LAST NAME}',ucwords($patientDetails[0]['lname']),$consent_form_content);
	$consent_form_content = str_ireplace('{SEX}',ucwords($patientDetails[0]['sex']),$consent_form_content);
	$consent_form_content = str_ireplace('{DOB}',ucwords($patientDetails[0]['pat_dob']),$consent_form_content);
	$consent_form_content = str_ireplace('{AGE}',$patient_age,$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT SS}',ucwords($patientDetails[0]['ss']),$consent_form_content);
	$consent_form_content = str_ireplace('{STATE ZIP CODE}',ucwords($patientDetails[0]['state'].' '.$patientDetails[0]['postal_code']),$consent_form_content);
	$consent_form_content = str_ireplace('{REL INFO}',$obj_print_pt_key->getPtReleaseInfoNames($patient_id),$consent_form_content);	
	$consent_form_content = str_ireplace('{PATIENT_NICK_NAME}',ucwords($patientDetails[0]['nick_name']),$consent_form_content);
	//=============START WORK TO SHOW THE LAST 4 DIGIT PATIENT SS==========================
	if(trim($patientDetails[0]['ss'])!=''){
		$consent_form_content = str_ireplace('{PATIENT_SS4}',ucwords(substr_replace($patientDetails[0]['ss'],'XXX-XX',0,6)),$consent_form_content);
	}else{
		$consent_form_content = str_ireplace('{PATIENT_SS4}','',$consent_form_content);
	}
	//===========================END WORK===================================================
	$consent_form_content = str_ireplace('{PHYSICIAN NAME}',ucwords($phy_name),$consent_form_content);
	$consent_form_content = str_ireplace('{PHYSICIAN FIRST NAME}',ucwords($phy_fname),$consent_form_content);
	$consent_form_content = str_ireplace('{PHYSICIAN MIDDLE NAME}',ucwords($phy_mname),$consent_form_content);
	$consent_form_content = str_ireplace('{PHYSICIAN LAST NAME}',ucwords($phy_lname),$consent_form_content);
	
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
	$consent_form_content = str_ireplace('{MONTHLY INCOME}','$'.number_format($patientDetails[0]['monthly_income'],2),$consent_form_content);
	$consent_form_content = str_ireplace('{DATE}',get_date_format(date('Y-m-d')),$consent_form_content);
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
	$consent_form_content = str_ireplace('{REF PHY FAX}',			trim($reffPhyDetail[0]['physician_fax']),$consent_form_content);
	
	
	//
	$consent_form_content = str_ireplace($web_root."/interface/SigPlus_images/","",$consent_form_content);
	$consent_form_content = str_ireplace("{PHYSICIAN SIGNATURE}","",$consent_form_content);	
	$consent_form_content = str_ireplace("{WITNESS SIGNATURE}","",$consent_form_content);	
	$consent_form_content = str_ireplace('{MED HX}',$medHx,$consent_form_content);
	$consent_form_content = str_ireplace('{OCULAR MEDICATION}',$ocularMed,$consent_form_content);
	$consent_form_content = str_ireplace('{SYSTEMIC MEDICATION}',$systemicMed,$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT ALLERGIES}',$allergyList,$consent_form_content);
	$consent_form_content = str_ireplace('{CC}',$cc_val,$consent_form_content);
	$consent_form_content = str_ireplace('{HISTORY}',$hx_val,$consent_form_content);
	$consent_form_content = str_ireplace('{ALL_INS_CASE}',$ins_cases,$consent_form_content);
	$consent_form_content = str_ireplace('{APPT_FUTURE}',$upcoming_appt,$consent_form_content);
	$consent_form_content = str_ireplace('{LOGGED_IN_FACILITY_NAME}',$loggedfacName,$consent_form_content);
	$consent_form_content = str_ireplace('{LOGGED_IN_FACILITY_ADDRESS}',$loggedfacAddress,$consent_form_content);
	
	//----------NEW APPOINTMENT VARIABLES REPLACEMENT WORK--------------------
	//----------FACILITY ADDRESS VARIABLE CONCATENATION-----------------------
	if($apptInfoArr2[10] && $apptInfoArr2[11])
	{
		$facilityAddress .= $apptInfoArr2[10].',&nbsp;'.$apptInfoArr2[11].',&nbsp;'.$apptInfoArr2[12].'&nbsp;'.$Zip_code_ext.'&nbsp;'.$apptInfoArr2[3];	
	}
	else if($apptInfoArr2[10])
	{
		$facilityAddress .= $apptInfoArr2[10].',&nbsp;'.$apptInfoArr2[12].'&nbsp;'.$Zip_code_ext.'&nbsp;'.$apptInfoArr2[3];	
	}
	else if($apptInfoArr2[11])
	{
		$facilityAddress .= $apptInfoArr[11].',&nbsp;'.$apptInfoArr2[12].'&nbsp;'.$Zip_code_ext.'&nbsp;'.$apptInfoArr2[3];
	}

	$consent_form_content = str_ireplace("{PATIENT_NEXT_APPOINTMENT_DATE}",$apptInfoArr2[0],$consent_form_content);
	$consent_form_content = str_ireplace("{PATIENT_NEXT_APPOINTMENT_TIME}",$apptInfoArr2[8],$consent_form_content);
	$consent_form_content = str_ireplace("{PATIENT_NEXT_APPOINTMENT_PROVIDER}",$apptInfoArr2[5],$consent_form_content);
	$consent_form_content = str_ireplace("{PATIENT_NEXT_APPOINTMENT_LOCATION}",$facilityAddress,$consent_form_content);
	$consent_form_content = str_ireplace("{PATIENT_NEXT_APPOINTMENT_PRIREASON}",$apptInfoArr2[4],$consent_form_content);
	$consent_form_content = str_ireplace("{PATIENT_NEXT_APPOINTMENT_SECREASON}",$apptInfoArr2[16],$consent_form_content);
	$consent_form_content = str_ireplace("{PATIENT_NEXT_APPOINTMENT_TERREASON}",$apptInfoArr2[17],$consent_form_content);
	
	$consent_form_content = str_ireplace('&amp;nbsp;',' ', $consent_form_content);
	$consent_form_content = str_ireplace('&nbsp;', ' ', $consent_form_content);
	$consent_form_content = str_ireplace('<div', '<span', $consent_form_content);
	$consent_form_content = str_ireplace('</div>', '</span>', $consent_form_content);
	$consent_form_content = str_ireplace('<meta charset="utf-8" />', '', $consent_form_content);
	
	//SPECIAL CHARACTER REPLACEMENT USING PRINT BUTTON
	$consent_form_content = mb_convert_encoding($consent_form_content,'HTML-ENTITIES','UTF-8');
	
	if ($chk == 0)
	{
		$consent_form_content = str_ireplace('{SIGNATURE}',"",$consent_form_content);
		
	}
}
	$qry = "select signature_image_path,signature_count from consent_form_signature 
					where form_information_id = '$form_information_id' and patient_id = '$patient_id'
					and consent_form_id = '$consent_form_id' and signature_status = 'Active' order by signature_count";
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
			$ds=0;$coun=0;
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
						}
						else{
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
			$content_row .= '
				<table width="145" border="1" align="center">
					<tr>
						'.$td_sign.'						
					</tr>
				</table>
			';
		}
	}
			$jh = 1;
			$consent_form_content .= $content_row;
			$tmpPath = str_ireplace("/","\\",$webserver_root."/interface/SigPlus_images");
			//$consent_form_content = str_ireplace($tmpPath.'/','../../data/'.PRACTICE_PATH.'/',$consent_form_content);
			$consent_form_content = str_ireplace($tmpPath.'/','',$consent_form_content);
		}
	}
	else
	{
		$consent_form_content = str_ireplace('{SIGNATURE}',"",$consent_form_content);
	}
	
	//--- get all content of consent forms -------	
	$consent_content .= '
		<table id="content_'.$consent_form_id.'" style="display:'.$display.'" width="100%" align="center" cellpadding="1" cellspacing="1" border="0">
			<tr>
				<td align="center" colspan="'.is_countable($sig_arr) ?? count($sig_arr).'">'.$consent_form_content.'</td>
			<tr>
		</table>';
		
	$consent_form_content = str_ireplace('&nbsp;',' ',$consent_form_content);
	
	if($htmlV2Class==false) {
		$consent_form_content = str_ireplace('</div>','<br>',$consent_form_content);
		$destin_path = $webServerRootDirectoryName.$web_RootDirectoryName.'/interface/common/html2pdf/';
		$src_path = $webServerRootDirectoryName.$web_RootDirectoryName.'/interface/common/new_html2pdf/';
		
		preg_match_all('/(src)=("[^"]*")/i',$consent_form_content,$matches); 
			foreach($matches[2] as $key){
				$file_Name=str_ireplace('"','',$key);
				$destin_path_file = $destin_path.$file_Name;
				$src_path_file=$src_path.$file_Name;
				if(!file_exists($destin_path_file) && file_exists($src_path_file)){
					@copy($src_path_file,$destin_path_file);
				}
			}
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

	// Code To Replace  {TEXTBOX_LARGE}  Textarea////
	$inputValTextarea=explode('<textarea rows="2" cols="100" name="large',$consent_form_content);
	if(is_array($inputValTextarea)){
		for($i=1;$i<count($inputValTextarea);$i++){
			$consent_form_content = str_ireplace('<textarea rows="2" cols="100" name="large">','',$consent_form_content);
			$consent_form_content = str_ireplace('<textarea rows="2" cols="100" name="large'.$i.'">','',$consent_form_content);
			$consent_form_content = str_ireplace('</textarea>','',$consent_form_content);
		}
	}
	// Code To Replace  {TEXTBOX_LARGE}  Textarea////
	
	$consent_form_content=str_ireplace('align="justify"','align="left"',$consent_form_content);
	$consent_form_content=str_ireplace('"text-align: justify"','"text-align: left"',$consent_form_content);
	
	if($htmlV2Class==false) {
		$consent_form_content = strip_tags($consent_form_content,'<strong> </strong><img> <p><page> <page_header> <br>');
	}
	//start creating barcode image
	
$barCodePatientId = $patient_id;
$barCodeFolderId = $consent_cat_id;
$lim=9;
$limFolder=4;
if($consent_package_category_id) {
	$limFolder=3;
	$barCodeFolderId = $consent_package_category_id;
}
$lenPtId = strlen($barCodePatientId);
$lenCnt = $lim-$lenPtId;
for($q=0;$q<$lenCnt;$q++) {
	$barCodePatientId =	'0'.$barCodePatientId;
}

$lenFolder = strlen($barCodeFolderId);
$lenCntFolder = $limFolder-$lenFolder;
for($r=0;$r<$lenCntFolder;$r++) {
	$barCodeFolderId =	'0'.$barCodeFolderId;
}
if($consent_package_category_id) {
	$barCodeFolderId =	'1'.$barCodeFolderId;
}
$Barcode_Text = $barCodePatientId.'-'.$barCodeFolderId;
$img_name = $barCodePatientId.'-'.$barCodeFolderId;
$oSaveFile->ptDir("consent_forms/bar_code_images");
$barCodeImgPath = '../../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/bar_code_images/'.$img_name.'.png';
generate_barcode($Barcode_Text,$img_name,$barCodeImgPath); 
$barCodeImgPath = $web_root.'/data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/bar_code_images/'.$img_name.'.png';
$barCodeContent = '<img src="'.$barCodeImgPath.'">';

$page_bar_code='<table style="width:700px;" cellpadding="0" cellspacing="0"><tr>
				<td style="text-align:right;width:700px;">'.$barCodeContent.'</td></tr><tr>
				<td style="text-align:right;width:700px;">'.$img_name.'</td></tr></table>';
$backtop="9mm";
$page_bar_code_v1='<p align="right" style="margin-top:-15px;">'.$barCodeContent.'</p><p align="right">'.$img_name.'</p>';
if(constant("BAR_CODE_DISABLE")=='YES'){
	$page_bar_code='';$backtop="0mm";$page_bar_code_v1='';
}
if($htmlV2Class!=true){
	
	$consent_form_content=$page_bar_code_v1.'
	
	
		'.$consent_form_content;		
	
}else{
$consent_form_content = '
	<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
		
		<tr><td style="width:100%;font-size:15px;" class="text_value">'.$consent_form_content.'</td></tr>
	</table>
';
}
//end creating barcode image

//inline style code commented from below div => height:'.($_SESSION['wn_height']-275).'px; overflow:scroll; 
if($htmlV2Class==true){
	$consent_form_content='<page backtop="'.$backtop.'" backbottom="0mm"><page_header>
	'.$page_bar_code.'</page_header>
	<page_footer>
	<table style="width: 100%;">
		<tr>
			<td style="text-align:center;width:100%" class="text_value">Page [[page_cu]]/[[page_nb]]</td>
		</tr>
	</table>
	</page_footer>
	<div style=" width:100%;">
	<table border="0" cellpadding="0" cellspacing="0" width="100%;">
		
		<tr>
			<td style="width:100%;font-size:15px;" class="text_value">
				'.$consent_form_content.'
			</td>
		</tr>
	</table>
	</div>
	'."</page>";
	$consent_form_content=str_ireplace("<div st</page>","</page>",$consent_form_content);
}
$consent_form_content = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/','../../data/'.PRACTICE_PATH.'/',$consent_form_content);
$consent_form_content = str_ireplace($GLOBALS['webroot'].'/library/images/','../../library/images/',$consent_form_content);
$consent_form_content = str_ireplace('../../../library/images/','../../library/images/',$consent_form_content);
$consent_form_content = str_ireplace($GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$consent_form_content);
//BELOW iportal_sig IMAGES ARE OF SIGN IMAGES SAVED FROM IPORTAL_R8
$consent_form_content = str_ireplace('iportal_sig/','../../data/'.PRACTICE_PATH.'/'.'iportal_sig/',$consent_form_content);
$rmslwebServerRootDirectoryName = rtrim($webServerRootDirectoryName,'/');
$consent_form_content = str_ireplace($rmslwebServerRootDirectoryName,'',$consent_form_content);

if(strtoupper(substr(PHP_OS, 0, 3))=='LIN')
{ 
	$consent_form_content= mb_convert_encoding($consent_form_content, "HTML-ENTITIES", 'UTF-8');
}	
$consent_form_content = str_ireplace('&nbsp;', ' ', $consent_form_content);

$matchesArr = array();
preg_match_all('@font-family(\s*):(.*?)(\s?)("|;|$)@i', $consent_form_content2, $matchesArr);
if (count($matchesArr[2])>0) {
	foreach($matchesArr[0] as $matchesKey=> $matches ) {
		$matchesVal=str_ireplace('"','',$matches);
		$consent_form_content2=str_ireplace($matchesVal,'',$consent_form_content2);	
	}
}
$html_file_name='';
$fld_path='../../../library/'.$htmlFolder.'/';

//$html_file_name='pdffile.html';
//if(constant("CONSENT_FORM_VERSION")=="consent_v2" && $htmlFolder == "html_to_pdf") {
if(!file_exists($fld_path.'consent_form')){
	mkdir($fld_path.'consent_form');
}
$html_file_name='consent_form/pdffile_'.$_SESSION['authId'].'.html';
//}
$html_file_name='consent_form/pdffile_'.$_SESSION['authId'].'.html';
$path_set=$fld_path.$html_file_name;
$file_path = '';
if($send_fax !='yes') {
	$file_path = write_html(html_entity_decode($consent_form_content));
}
if($send_fax !='yes') {
?>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
<?php
}
if($file_path && $send_fax !='yes'){ //FROM consent_send_fax.php
	 
	 if ($_REQUEST["consent"]) {  ?>
		<script type="text/javascript">
		if(typeof(top.btn_show)=='function'){
			//START CODE TO SEND FAX
			var show_button = '';
			var form_information_id = '<?php echo $form_information_id;?>';
			if(parent.document.getElementById("form_information_id")) {
				parent.document.getElementById("form_information_id").value = form_information_id;
			}
			<?php
			if((is_updox('fax') || is_interfax()) && !isset($_REQUEST['hidebtn'])) {?>
				show_button = 'CF3';<?php
			}?>
			//END CODE TO SEND FAX
			top.btn_show(show_button);
		}
		var htmlFilePth = "<?php echo $htmlFilePth;?>";
		var browserIpad = "<?php echo $browserIpad;?>";
		var html_File_name="<?php echo $html_file_name; ?>";
		top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
		
		if(browserIpad=="yes") {
			//window.open('../../../library/'+htmlFilePth+'?font_size=10&page=4&file_location='+html_File_name,'_blank','');
			html_to_pdf('<?php echo $file_path; ?>','p');
		}else {
			//window.location = "../../../library/"+htmlFilePth+"?font_size=10&page=4&tree=yes&file_location="+html_File_name;
			var file_name = '<?php echo $print_file_name; ?>';
			html_to_pdf('<?php echo $file_path; ?>','p','',true);
		
		}
		</script>
		<?php } else{  ?>
		<script type="text/javascript">	
		top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
		var html_File_name="<?php echo $html_file_name; ?>";	
		if(typeof(top.btn_show)=="function")top.btn_show();
		var htmlFilePth = "<?php echo $htmlFilePth;?>";
		html_to_pdf('<?php echo $file_path; ?>','p','',true);
		//window.open('../../../library/'+htmlFilePth+'?font_size=10&page=4&file_location='+html_File_name,'_parent','');
		<?php } ?>
	</script>
<?php
}
?>