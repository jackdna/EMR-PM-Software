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
 * 
 * File: consentFormDetails.php
 * Purpose: Get consent form details  
 * Access Type: Direct  
 */
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");

header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

include_once("../../../config/globals.php");
$library_path = $GLOBALS['webroot'].'/library';
$patient_id = $_SESSION['patient'];
require_once("../../../library/classes/Mobile_Detect.php");
// Exclude tablets.
$detect = new Mobile_Detect;
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
$this_device = "frontend";
if( $detect->isMobile() && !$detect->isTablet() ){
$this_device = "mobile";
}
if(isset($_GET["mode"]) && $_GET["mode"] == "mobile"){
$this_device = "mobile"; //testing to be commented later on
}
if($detect->isMobile()){
	$test_device = "mobile";
}else if($detect->isTablet()){
	$test_device = "Tablet";
}

$enable_sig_web = enable_web_sig_pad();



//-----  Get data from remote server -------------------

$zRemotePageName = "patient_info/consent_forms/consentFormDetails";
if(constant("REMOTE_SYNC") == 1) {
	//require(dirname(__FILE__)."/../../chart_notes/get_chart_from_remote_server.inc.php");
}

//-----  Get data from remote server -------------------

//require_once(dirname(__FILE__)."/../../main/Functions.php");
require_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
require_once(dirname(__FILE__)."/../../../library/classes/cls_common_function.php");
require_once(dirname(__FILE__)."/../../../library/classes/audit_common_function.php");
//require_once(dirname(__FILE__)."/../../main/main_functions.php");
require_once(dirname(__FILE__)."/../../../library/classes/functions_ptInfo.php");
$blClientBrowserIpad = false; 
include_once(dirname(__FILE__)."/../../../library/classes/functions.smart_tags.php");
$OBJsmart_tags = new SmartTags;

include_once(dirname(__FILE__)."/../../../library/classes/class.cls_hold_document.php");
include_once(dirname(__FILE__)."/../../../library/classes/print_pt_key.php");
include(dirname(__FILE__).'/../../../library/bar_code/code128/code128.class.php');
$oSaveFile = new SaveFile($patient_id);
$oSaveFile->ptDir("consent_forms");

$oSaveFileUser = new SaveFile();


$OBJhold_sign = new CLSHoldDocument;
$obj_print_pt_key=new print_pt_key;
$OBJCommonFunction = new CLSCommonFunction;
$package_category_id = $_REQUEST["package_category_id"];
$proc_site = $_REQUEST["site"]; //This site value is coming from workview procedures sites
function getSigImage($postDataWit,$pth="",$rootPath){
	global $webServerRootDirectoryName;
	$rootPath = $webServerRootDirectoryName;//THIS PARAMETER HOLD LOCAL ADDRESS OF SERVER i.e. "/var/www/html/";
	$output = shell_exec("java -cp .:".$rootPath."SigPlusLinuxHSB/SigPlus.jar:".$rootPath."SigPlusLinuxHSB/RXTXcomm.jar:".$rootPath."SigPlusLinuxHSB SigPlusImgDemoV2 ".$postDataWit." ".$rootPath."SigPlusLinuxHSB/sig.jpg 2>&1");
	@copy($rootPath."SigPlusLinuxHSB/sig.jpg",$pth);
	echo $output;
}

include_once(dirname(__FILE__)."/../../../interface/chart_notes/chart_globals.php");
include_once(dirname(__FILE__)."/../../../library/classes/work_view/wv_functions.php");
include_once(dirname(__FILE__)."/../../../library/classes/work_view/ChartAP.php");
include_once(dirname(__FILE__)."/../../../library/classes/work_view/MedHx.php");
include_once(dirname(__FILE__)."/../../../library/classes/work_view/Patient.php");
include_once(dirname(__FILE__)."/../../../library/classes/work_view/CcHx.php");
include_once(dirname(__FILE__)."/../../../library/classes/work_view/pnTempParser.php");
$objParser = new PnTempParser;
$medHx=$objParser->getMedHx_public($_SESSION['patient']);
$ocularMed=$objParser->get_med_list($_SESSION['patient'],4);
$systemicMed=$objParser->get_med_list($_SESSION['patient'],1);
$allergyList=$objParser->get_med_list($_SESSION['patient'],7);
$form_id=$_SESSION['form_id'];
$ins_cases=$objParser->all_ins_case($_SESSION['patient']);
$signature_type_select = $_REQUEST['signature_type_select'];
if(!$form_id){
	$qry_form_id="Select id from chart_master_table where patient_id='".$_SESSION['patient']."'  order by id desc";
	$res_form_id=imw_query($qry_form_id);
	$row_form_id=imw_fetch_assoc($res_form_id);
	$form_id=$row_form_id['id'];
}
$cc_hx=$objParser->get_cc_hx($_SESSION['patient'],$form_id);
$cc_val=$cc_hx[1];
$hx_val=$cc_hx[2];
//START CODE TO SET NEW CLASS TO CONSENT FORMS
if($_SESSION['patient']){
	$upcoming_appt=$obj_print_pt_key->getApptFuture($_SESSION['patient'],'','','n');
}

$arrival_time=$objParser->__pt_actual_arrival_time('',$_SESSION['patient'],$form_id,'consent');
$pt_arrival_time = $arrival_time[0];
//START CODE TO SET NEW CLASS TO CONSENT FORMS
/*
$htmlFolder = "html2pdf";
$htmlV2Class = false;
$htmlFilePth = "html2pdf/index.php";
if(constant("CONSENT_FORM_VERSION")=="consent_v2") {
	$htmlFolder = "html_to_pdf";
	$htmlV2Class=true;	
	$htmlFilePth = "html_to_pdf/createPdf.php";
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
$loggedfacPhone	   		= $loggedfacilityInfoArr[6];
if($loggedfacPostalcode && $loggedfacExt){
	$loggedzipcodext = $loggedfacPostalcode.'-'.$loggedfacExt;
}else{
	$loggedzipcodext = $loggedfacPostalcode;
}
$loggedfacAddress = $loggedfacCity.', '.$loggedfacState.',&nbsp;'.$loggedfacCountry.'&nbsp;'.$loggedzipcodext.'&nbsp;'.$loggedfacPhone;
//=======================ENDS HERE======================================================
//functions--
function cfd_getProIdFromChart(){
	$pro_id=0;
	$sql = "SELECT providerId FROM chart_master_table where patient_id = '".$_SESSION['patient']."' AND delete_status='0' AND purge_status='0' AND date_of_service=CURDATE() ORDER BY create_dt DESC, id DESC  LIMIT 0, 1 ";
	$row=get_row_record_query($sql);
	if($row!=false){	$pro_id=$row["providerId"];}
	return $pro_id;
}
//functions--


/*--IF PHY SIG IS AVAILABLE --*/

	//--
	$id = $_SESSION['authId'];
	$chkSignQry = "SELECT sign, sign_path FROM users WHERE id='".$_SESSION['authId']."'";
	$chkSignRes = imw_query($chkSignQry);
	$chkSignRow = imw_fetch_array($chkSignRes);
	$elem_sign = $chkSignRow["sign"];
	$elem_sign_path = trim($chkSignRow["sign_path"]);
	$updir=substr(data_path(), 0, -1);
	$srcDir = substr(data_path(1), 0, -1);
	$oSaveFileUser->ptDir("UserId_".$id."/sign");
	if((!empty($elem_sign_path) && file_exists(trim($updir.$elem_sign_path)))) {		
		$sigDivDataURL = trim($srcDir.$elem_sign_path);
		$sigDivData = '<img src="'.$sigDivDataURL.'" height="45" width="225">';
	}else if((!empty($elem_sign) && $elem_sign != "0-0-0:;")) {		
		$tblName = "users";
		$pixelFieldName = "sign";
		$idFieldName = "id";
		$imgPath = "";
		$saveImg = dirname(__FILE__)."/../../../data/".PRACTICE_PATH."/UserId_".$id."/user_id_".$id.".jpg";
		include(dirname(__FILE__)."/../../patient_info/complete_pt_rec/imgGd.php");
		$sigDivDataURL = $srcDir.$sigDivDataURL;
		$sigDivData = '<img src="'.$sigDivDataURL.'" height="45" width="225">';				
	}
	//--	
	
/*--END OF PHY SIG IS AVAILABLE--*/
$deviceSelect = $_REQUEST['deviceSelect'];
//echo $_SERVER['HTTP_USER_AGENT'];
if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true || $signature_type_select=='touch_screen'   || ($detect->isTablet()) || ($detect->isMobile()) || $deviceSelect == 'touch_screen') {/* || stristr($_SERVER['HTTP_USER_AGENT'], 'touch') == true*/
if(!isset($_REQUEST['chartId'])){
	$blClientBrowserIpad = true;
	echo "<script>var win_op=1;</script>";
}
}
if($blClientBrowserIpad==true && $fopen_file==false){
	echo "<script>window.open('consent_form_details_ipad.php?signature_type_select=".$signature_type_select."&consent_form_id=".$_REQUEST['consent_form_id']."&package_category_id=".$package_category_id."','Consent Form','width=800,height=700,top=50,left=300,resizable=yes,scrollbars=yes');</script>";	
	
	exit();
}
//var_dump($blClientBrowserIpad);

/*$qryGetAuditPolicies = "select policy_status as polStSig from audit_policies where policy_id = 10";
$rsGetAuditPolicies = imw_query($qryGetAuditPolicies);
if($rsGetAuditPolicies){
	if(imw_num_rows($rsGetAuditPolicies)){
		extract(imw_fetch_array($rsGetAuditPolicies));
	}
}
*/
$polStSig = 0;
$polStSig = (int)$_SESSION['AUDIT_POLICIES']['Signature_Created_Validated'];

if($polStSig == 1){
	$signatureDataFields = array(); 
	$signatureDataFields = make_field_type_array("consent_form_signature");
	if($signatureDataFields == 1146){
		$signatureError = "Error : Table 'consent_form_signature' doesn't exist";
	}
}

$patient_id = $_SESSION['patient'];
$opt_id = $_SESSION['authId'];
if($ver_option){
	$consent_form_id = $ver_option;
}
//--- get operator details --------
$qry = "select * from users where id = '$opt_id'";
$operatorDetails = get_array_records_query($qry);
$operator_name = $operatorDetails[0]['lname'].', ';
$operator_name .= $operatorDetails[0]['fname'].' ';
$operator_name .= $operatorDetails[0]['mname'];
$operator_initial = substr($operatorDetails[0]['fname'],0,1);
$operator_initial .= substr($operatorDetails[0]['lname'],0,1);
//---- get Patient details ---------
$qry = "select *,date_format(DOB,'".get_sql_date_format()."') as pat_dob,date_format(date,'".get_sql_date_format()."') as reg_date
		from patient_data where id = '$patient_id'";
$patientDetails = get_array_records_query($qry);
$patient_initial = substr($patientDetails[0]['fname'],0,1);
$patient_initial .= substr($patientDetails[0]['lname'],0,1);
list($year, $month, $day) = explode('-',$patientDetails[0]['DOB']);
$pat_date = $year."-".$month."-".$day;
$patient_age = get_age($pat_date);	
//--- get physician name --------
/*//RE: Ticket 10002 - URGENT
order of reading physician will be following:
1 ï¿½ CHART NOTE (if exists on same date)
2 ï¿½ APPOINTMENT (if exists on same date)
3 ï¿½ Pt INFO
*/
$pro_id = cfd_getProIdFromChart();//1
if(empty($pro_id)){
	//2
	$qry_pro_id="select sa_doctor_id from schedule_appointments
				where sa_patient_app_status_id not in (201, 18, 203, 19, 20)
				and sa_patient_id = '".$patient_id."' and sa_app_start_date <= now()
				order by sa_app_start_date desc, sa_app_starttime desc limit 0, 1";
	$res_pro_id=imw_query($qry_pro_id);
	$row_pro_id=imw_fetch_assoc($res_pro_id);	
	$pro_id=$row_pro_id['sa_doctor_id'];
	//3
	if($pro_id=="" || $pro_id==0){
		$pro_id = $patientDetails[0]['providerID'];	
	}
}
$qry = "select concat(lname,', ',fname) as name,mname,fname,lname,pro_title,pro_suffix from users where id = '$pro_id'";
$phyDetail = get_array_records_query($qry);
$phy_name = ucwords(trim($phyDetail[0]['name'].' '.$phyDetail[0]['mname']));
$phy_fname = ucwords(trim($phyDetail[0]['fname']));
$phy_mname = ucwords(trim($phyDetail[0]['mname']));
$phy_lname = ucwords(trim($phyDetail[0]['lname']));
$phy_name_suffix = ucwords(trim($phyDetail[0]['pro_suffix']));
//--- get reffering physician name --------
$primary_care_id = $patientDetails[0]['primary_care_id'];
$qry = "select concat(LastName,', ',FirstName) as name , MiddleName, FirstName, LastName, Title, specialty, physician_phone, physician_fax, Address1, Address2, ZipCode, City, State from refferphysician
		where physician_Reffer_id = '$primary_care_id'";
$reffPhyDetail = get_array_records_query($qry);
$reffer_name = ucwords(trim($reffPhyDetail[0]['name'].' '.$reffPhyDetail[0]['MiddleName']));
$refPhyAddress="";
$refPhyAddress .= (!empty($reffPhyDetail[0]['Address1'])) ? trim($reffPhyDetail[0]['Address1']) : "";
$refPhyAddress .= (!empty($reffPhyDetail[0]['Address2'])) ? "<br>".trim($reffPhyDetail[0]['Address2']) : "";


//--- get primary care physician detail --------
$pcp_id = $patientDetails[0]['primary_care_phy_id'];
$qry = "select pcp.Title as pcpTitle,pcp.FirstName as pcpFName,pcp.MiddleName as pcpMName,pcp.LastName as pcpLName, 
		pcp.Address1 as pcpAddress1,pcp.Address2 as pcpAddress2,pcp.City as pcpCity,pcp.State as pcpState,pcp.ZipCode as pcpZipCode
		 from refferphysician pcp
		 where pcp.physician_Reffer_id = '".$pcp_id."'";
$pcpPhyDetail = get_array_records_query($qry);
$pcpAddress=$pcpName="";
$pcpName=$pcpPhyDetail[0]['pcpLName'].", ".$pcpPhyDetail[0]['pcpFName']." ".$pcpPhyDetail[0]['pcpMName'];
$pcpAddress .= (!empty($pcpPhyDetail[0]['pcpAddress1'])) ? trim($pcpPhyDetail[0]['pcpAddress1']) : "";
$pcpAddress .= (!empty($pcpPhyDetail[0]['pcpAddress2'])) ? "<br>".trim($pcpPhyDetail[0]['pcpAddress2']) : "";

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
//get Primary insurence data//

$qryGetInsPriData ="SELECT 
						ind.provider as priInsComp,
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
					FROM
						insurance_data as ind INNER JOIN insurance_case as ic
						on (ind.ins_caseid=ic.ins_caseid AND ic.case_status = 'Open')
						JOIN insurance_case_types as ict  
						on ict.case_id=ic.ins_case_type
					WHERE 
						ind.pid = '".$_SESSION['patient']."' 
					AND 
						ind.ins_caseid > 0 
					AND
						ind.type = 'primary' 
					AND 
						ind.actInsComp = 1
					AND 
						ind.provider > 0 
					ORDER BY 
						ict.normal DESC LIMIT 0,1
					";
$rsGetInsPriData = imw_query($qryGetInsPriData);
$numRowGetInsPriData = imw_num_rows($rsGetInsPriData);
if($numRowGetInsPriData>0){
	extract(imw_fetch_array($rsGetInsPriData));
	$qryGetInsPriProvider ="SELECT 
								name as priInsCompName, 
								CONCAT(contact_address,if(TRIM(city)!='',CONCAT(' ',city),''),if(TRIM(State)!='',CONCAT(', ',State),''),if(TRIM(Zip)!='',CONCAT(' ',Zip),''),if(TRIM(zip_ext)!='',CONCAT('-',zip_ext),'')) AS priInsCompAddress 
							FROM 
								insurance_companies 
							WHERE
								id = $priInsComp
							";
								
	$rsGetInsPriProvider = imw_query($qryGetInsPriProvider);
	$numRowGetInsPriProvider = imw_num_rows($rsGetInsPriProvider);
	if($numRowGetInsPriProvider>0){
		extract(imw_fetch_array($rsGetInsPriProvider));
	}
}
//end get Primary insurence data//
//get Secondary insurence data//
$qryGetInsSecData ="SELECT 
						ind.provider as secInsComp,
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
					FROM 
						insurance_data as ind INNER JOIN insurance_case as ic
						on (ind.ins_caseid=ic.ins_caseid AND ic.case_status = 'Open')
						JOIN insurance_case_types as ict  
						on ict.case_id=ic.ins_case_type
					WHERE 
						ind.pid = '".$_SESSION['patient']."' 
					AND 
						ind.ins_caseid > 0 
					AND
						ind.type = 'secondary' 
					AND 
						ind.actInsComp = 1
					AND 
						ind.provider > 0 
					ORDER BY 
						ict.normal DESC LIMIT 0,1
					 ";
$rsGetInsSecData = imw_query($qryGetInsSecData);
$numRowGetInsSecData = imw_num_rows($rsGetInsSecData);
if($numRowGetInsSecData>0){
	extract(imw_fetch_array($rsGetInsSecData));
	$qryGetInsSecProvider ="SELECT 
								name as secInsCompName,
								CONCAT(contact_address,if(TRIM(city)!='',CONCAT(' ',city),''),if(TRIM(State)!='',CONCAT(', ',State),''),if(TRIM(Zip)!='',CONCAT(' ',Zip),''),if(TRIM(zip_ext)!='',CONCAT('-',zip_ext),'')) AS secInsCompAddress  
							FROM 
								insurance_companies 
							WHERE 
								id = $secInsComp
							";
	$rsGetInsSecProvider = imw_query($qryGetInsSecProvider);
	$numRowGetInsSecProvider = imw_num_rows($rsGetInsSecProvider);
	if($numRowGetInsSecProvider>0){
		extract(imw_fetch_array($rsGetInsSecProvider));
	}
}
//end get Secondary insurence data//

//get patient Appointment data//
/* $qryGetApptData = "select sa.sa_app_start_date,DATE_FORMAT(sa.sa_app_start_date, '%a ".get_sql_date_format('','y','-')."') as appDate,
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
} */
$appDate = $appTime = "";
$apptInfoArr= $obj_print_pt_key->getApptInfo($patient_id,'','','','');
$appDate	= $apptInfoArr[0];
$appTime	= $apptInfoArr[8];

//------NEW APPOINTMENT VARIABLE INFORMATION-------------------------
$apptInfoArr2 = $obj_print_pt_key->getApptInfo($patient_id,'','','',1);

//--- save form informations -------
$data['consent_form_id'] = $show_td;
$data['patient_id'] = $patient_id;
$data['patient_signature'] = $_POST['patient_signature_0_'.$show_td];
$data['witness_signature'] = $_POST['witness_signature_1_'.$show_td];
$data['operator_id'] = $_SESSION['authId'];
$qry = "select consent_form_name from consent_form where consent_form_id = '$show_td'";
$res = get_array_records_query($qry);
$consent_form_name = $res[0]['consent_form_name'];
$data['consent_form_name'] = $consent_form_name;
if($content_save){
//echo 'after save this code will run and save code';
	$qry = "select consent_form_content from consent_form
			where consent_form_id = '$show_td'";			
	$form_content = get_array_records_query($qry);
	$consent_form_content_data = stripslashes($form_content[0]['consent_form_content']);
	$arrStr = array("{TEXTBOX_XSMALL}","{TEXTBOX_SMALL}","{TEXTBOX_MEDIUM}","{TEXTBOX_LARGE}");
			
	for($j = 0;$j<count($arrStr);$j++)
	{
		if($arrStr[$j] == '{TEXTBOX_XSMALL}')
		{
			$name = 'xsmall';
			$size = 1;
		}
		else if($arrStr[$j] == '{TEXTBOX_SMALL}')
		{
			$name = 'small';
			$size = 30;
		}
		else if($arrStr[$j] == '{TEXTBOX_MEDIUM}')
		{
			$name = 'medium';
			$size = 60;
		}
		else if($arrStr[$j] == '{TEXTBOX_LARGE}')
		{
			$name = 'large';
			$size = 120;
			
		}
		$repVal = '';
		if(substr_count($consent_form_content_data,$arrStr[$j]) > 1)
		{
			
			if($arrStr[$j] == '{TEXTBOX_XSMALL}' || $arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}')
			{
				$c = 1;
				$arrExp = explode($arrStr[$j],$consent_form_content_data);
				
				for($p = 0;$p<count($arrExp)-1;$p++)
				{
					$repVal .= $arrExp[$p].'<input type="text" class="form-control " name="'.$name.$c.'" value="'.htmlentities($_POST[$name.$c]).'" size="'.$size.'"  maxlength="'.$size.'">';
					$c++;
				}
				$repVal .= end($arrExp);
				$consent_form_content_data = $repVal;
			}
			else if($arrStr[$j] == '{TEXTBOX_LARGE}')
			{
				$c = 1;
				$arrExp = explode($arrStr[$j],$consent_form_content_data);
				
				for($p = 0;$p<count($arrExp)-1;$p++)
				{
					$repVal .= $arrExp[$p].'<textarea rows="2" cols="100" name="'.$name.$c.'"> '.nl2br(htmlentities($_POST[$name.$c])).' </textarea>';
					$c++;
				}
				$repVal .= end($arrExp);
				$consent_form_content_data = $repVal;
			}
		}
		else
		{
			if($arrStr[$j] == '{TEXTBOX_XSMALL}') {
				$repVal = str_ireplace($arrStr[$j],'<input type="text" class="form-control " name="'.$name.'" value="'.htmlentities($_POST[$name]).'" size="'.$size.'" maxlength="'.$size.'">',$consent_form_content_data);
				$consent_form_content_data = $repVal;
			}else if($arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}')
			{
				$repVal = str_ireplace($arrStr[$j],'<input class="form-control " type="text" name="'.$name.'" value="'.htmlentities($_POST[$name]).'" size="'.$size.'" >',$consent_form_content_data);
				$consent_form_content_data = $repVal;
			}
			else if($arrStr[$j] == '{TEXTBOX_LARGE}')
			{
				$repVal = str_ireplace($arrStr[$j],'<textarea rows="2" cols="100" name="'.$name.'"> '.nl2br(htmlentities($_POST[$name])).' </textarea>',$consent_form_content_data);
				$consent_form_content_data = $repVal;
			}
		}
		 
	}
	
/*--REPLACING SMART TAGS (IF FOUND) WITH LINKS--*/
	$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
	$tagconsent_form_content_data = '';
	foreach($arr_smartTags as $key=>$val){
//		$val = str_replace('&nbsp',' ',$val);
		$regpattern='/(\['.$val.'\])+/i';
		$row_arr = preg_split($regpattern,$consent_form_content_data);
//		$row_arr = explode("[".$val."]",$consent_form_content_data);
		$tagconsent_form_content_data = $row_arr[0];
		for($tagCount=1;$tagCount<count($row_arr);$tagCount++){
			/*
			if($_POST[$key.'_'.$tagCount.'tag'] != ''){
				$tagconsent_form_content_data .= $_POST[$key.'_'.$tagCount.'tag'].$row_arr[$tagCount];
			}else{
				$tagconsent_form_content_data .= $val.$row_arr[$tagCount];
			}*/
			if($_POST[$key] != ''){
				//$tagconsent_form_content_data .= $_POST[$key.'_'.$tagCount.'tag'].$row_arr[$tagCount];
				$tagconsent_form_content_data .= $_POST[$key].$row_arr[$tagCount];
			}else{
				$tagconsent_form_content_data .= $val.$row_arr[$tagCount];
			}
		}
		
		if($tagconsent_form_content_data != ''){//die($tagconsent_form_content_data);
			$consent_form_content_data = $tagconsent_form_content_data;
		}
	}
/*--SMART TAG REPLACEMENT END--*/

	
	
	//start code for iPad 
	//if($blClientBrowserIpad == true) {//IPAD SIGNATURE
		
		//Patient iPad Signature
		$consent_form_content_data= str_ireplace("{SIGNATURE}","{SIGNATURE}",$consent_form_content_data);
		$row_arr = explode('{SIGNATURE}',$consent_form_content_data);
		$consent_form_content_data = $row_arr[0];
		
		for($c=1;$c<count($row_arr);$c++){
			$hiddSigIpadIdImg = trim($_REQUEST['hiddSigIpadId'.$c]);
			if($hiddSigIpadIdImg) {
				$consent_form_content_data .= '<img src="'.$hiddSigIpadIdImg.'" width="150" height="83">';	
			}else {
				$consent_form_content_data .= '{SIGNATURE}';
				
			}
			
			$consent_form_content_data .= $row_arr[$c];
		}
		
		//Witness iPad Signature
		$consent_form_content_data= str_ireplace("{WITNESS SIGNATURE}","{WITNESS SIGNATURE}",$consent_form_content_data);
		$row_arr = explode('{WITNESS SIGNATURE}',$consent_form_content_data);
		$consent_form_content_data = $row_arr[0];
		for($c=1;$c<count($row_arr);$c++){
			$hiddSigIpadIdWitImg 	= trim($_REQUEST['hiddSigIpadIdWit'.$c]);
			$hidd_signReplacedWit  	= trim($_REQUEST['hidd_signReplacedWit'.$c]);
			if($hiddSigIpadIdWitImg) {
				$consent_form_content_data .= '<img src="'.$hiddSigIpadIdWitImg.'" width="150" height="83">';	
			}else if($hidd_signReplacedWit) {
				$hidd_signReplacedWit = urldecode($hidd_signReplacedWit);
				$hidd_signReplacedWit = $hidd_signReplacedWit;
				$consent_form_content_data .= '<img src="'.$hidd_signReplacedWit.'" width="150" height="83">';	
			}else {
				$consent_form_content_data .= '{WITNESS SIGNATURE}';
				
			}
			
			$consent_form_content_data .= $row_arr[$c];
		}
		
		//Physician iPad Signature
		$consent_form_content_data= str_ireplace("{PHYSICIAN SIGNATURE}","{PHYSICIAN SIGNATURE}",$consent_form_content_data);
		$row_arr = explode('{PHYSICIAN SIGNATURE}',$consent_form_content_data);
		$consent_form_content_data = $row_arr[0];
		for($c=1;$c<count($row_arr);$c++){
			$hiddSigIpadIdPhyImg = trim($_REQUEST['hiddSigIpadIdPhy'.$c]);
			$hidd_signReplacedPhy  	= trim($_REQUEST['hidd_signReplaced'.$c]);
			if($hiddSigIpadIdPhyImg) {
				$consent_form_content_data .= '<img src="'.$hiddSigIpadIdPhyImg.'" width="150" height="83">';	
			}else if($hidd_signReplacedPhy) {//Replace with Admin user
				$hidd_signReplacedPhy = urldecode($hidd_signReplacedPhy);
				$hidd_signReplacedPhy = $hidd_signReplacedPhy;
				$consent_form_content_data .= '<img src="'.$hidd_signReplacedPhy.'" width="150" height="83">';	
			}else {
				$consent_form_content_data .= '{PHYSICIAN SIGNATURE}';
				
			}
			
			$consent_form_content_data .= $row_arr[$c];
		}
		
	
	/*
	}else {
		
		//-- get signature applets ----
		$row_arr = explode('{SIGNATURE}',$consent_form_content_data);
		$consent_form_content_data = $row_arr[0];
		for($c=1;$c<count($row_arr);$c++){
			$imgNameArr = explode('\\',$patientSignArr[$c-1]);
			$imgSrc = '../'.end($imgNameArr);
			if(file_exists(dirname(__FILE__).'/'.$imgSrc) && is_dir(dirname(__FILE__).'/'.$imgSrc) == ''){
				$consent_form_content_data .= '<br><span style="text-align: left" ><img name="'.$imgSrc.'" id="sign_id[]" src="'.$imgSrc.'" width="250" height="83"></span>';
			}
			else{
				$consent_form_content_data .= '{SIGNATURE}';
			}
			$consent_form_content_data .= $row_arr[$c];
		}
	}*/
	//--- change value between curly brackets -------	
	$consent_form_content_data = str_ireplace('{PATIENT NAME TITLE}',ucwords($patientDetails[0]['title']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT FIRST NAME}',ucwords($patientDetails[0]['fname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{MIDDLE NAME}',ucwords($patientDetails[0]['mname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{LAST NAME}',ucwords($patientDetails[0]['lname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SEX}',ucwords($patientDetails[0]['sex']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{DOB}',ucwords($patientDetails[0]['pat_dob']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{AGE}',$patient_age,$consent_form_content_data);	
	$consent_form_content_data = str_ireplace('{PATIENT SS}',ucwords($patientDetails[0]['ss']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT_NICK_NAME}',ucwords($patientDetails[0]['nick_name']),$consent_form_content_data);
	//=============START WORK TO SHOW THE LAST 4 DIGIT PATIENT SS==========================
	if(trim($patientDetails[0]['ss'])!==''){
	$consent_form_content_data = str_ireplace('{PATIENT_SS4}',ucwords(substr_replace($patientDetails[0]['ss'],'XXX-XX',0,6)),$consent_form_content_data);
	}else{
	$consent_form_content_data = str_ireplace('{PATIENT_SS4}','',$consent_form_content_data);
	}
	//===========================END WORK===================================================
	
	$consent_form_content_data = str_ireplace('{PHYSICIAN NAME}',ucwords($phy_name),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PHYSICIAN FIRST NAME}',ucwords($phy_fname),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PHYSICIAN MIDDLE NAME}',ucwords($phy_mname),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PHYSICIAN LAST NAME}',ucwords($phy_lname),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PHYSICIAN NAME SUFFIX}',ucwords($phy_name_suffix),$consent_form_content_data);
	
	$consent_form_content_data = str_ireplace('{MARITAL STATUS}',ucwords($patientDetails[0]['status']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{ADDRESS1}',ucwords($patientDetails[0]['street']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{ADDRESS2}',ucwords($patientDetails[0]['street2']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{HOME PHONE}',ucwords($patientDetails[0]['phone_home']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{EMERGENCY CONTACT}',ucwords($patientDetails[0]['contact_relationship']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{EMERGENCY CONTACT PH}',ucwords($patientDetails[0]['phone_contact']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{MOBILE PHONE}',ucwords($patientDetails[0]['phone_cell']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{WORK PHONE}',ucwords($patientDetails[0]['phone_biz']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT CITY}',ucwords($patientDetails[0]['city']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT STATE}',ucwords($patientDetails[0]['state']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT ZIP}',ucwords($patientDetails[0]['postal_code']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REGISTRATION DATE}',ucwords($patientDetails[0]['reg_date']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REFFERING PHY.}',ucwords($reffer_name),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{POS FACILITY}',ucwords($pos_facility_name),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{DRIVING LICENSE}',ucwords($patientDetails[0]['driving_licence']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{HEARD ABOUT US}',ucwords($patientDetails[0]['heard_abt_us']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{HEARD ABOUT US DETAIL}',$patientDetails[0]['heard_abt_desc'],$consent_form_content_data);
	
	$consent_form_content_data = str_ireplace('{EMAIL ADDRESS}',$patientDetails[0]['email'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{USER DEFINE 1}',$patientDetails[0]['genericval1'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{USER DEFINE 2}',$patientDetails[0]['genericval2'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT MRN}',$patientDetails[0]['External_MRN_1'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT MRN2}',$patientDetails[0]['External_MRN_2'],$consent_form_content_data);
	
	$languageShow 			   = str_ireplace("Other -- ","",$patientDetails[0]['language']);
	$raceShow				   = trim($patientDetails[0]["race"]);
	$otherRace				   = trim($patientDetails[0]["otherRace"]);
	if($otherRace) { 
		$raceShow			   = $otherRace;
	}
	$ethnicityShow			   = trim($patientDetails[0]["ethnicity"]);			
	$otherEthnicity			   = trim($patientDetails[0]["otherEthnicity"]);
	if($otherEthnicity) { 
		$ethnicityShow		   = $otherEthnicity;
	}

	$consent_form_content_data = str_ireplace('{RACE}',$raceShow,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{LANGUAGE}',$languageShow,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{ETHNICITY}',$ethnicityShow,$consent_form_content_data);
	
	
	$consent_form_content_data = str_ireplace('{REL INFO}',$obj_print_pt_key->getPtReleaseInfoNames($patient_id),$consent_form_content_data);
	//new variable added
	//if($patientDetails[0]['postal_code']) {
		$consent_form_content_data = str_ireplace('{STATE ZIP CODE}',ucwords($patientDetails[0]['state'].' '.$patientDetails[0]['postal_code']),$consent_form_content_data);
	//}
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN TITLE}',		trim(ucwords($reffPhyDetail[0]['Title'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN FIRST NAME}',	trim(ucwords($reffPhyDetail[0]['FirstName'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN LAST NAME}',	trim(ucwords($reffPhyDetail[0]['LastName'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY SPECIALITY}',		trim(ucwords($reffPhyDetail[0]['specialty'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY PHONE}',			trim(ucwords($reffPhyDetail[0]['physician_phone'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY FAX}',				trim($reffPhyDetail[0]['physician_fax']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY STREET ADDR}',		$refPhyAddress,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY CITY}',				trim(ucwords($reffPhyDetail[0]['City'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY STATE}',			trim(ucwords($reffPhyDetail[0]['State'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY ZIP}',				trim(ucwords($reffPhyDetail[0]['ZipCode'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP NAME}',$pcpName,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP STREET ADDR}',$pcpAddress,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP City}',	$pcpPhyDetail[0]['pcpCity'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP State}',$pcpPhyDetail[0]['pcpState'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP ZIP}',	$pcpPhyDetail[0]['pcpZipCode'],$consent_form_content_data);	
	$consent_form_content_data = str_ireplace($web_root.'/library/images/','../../../library/images/',$consent_form_content_data);
	//=============RESPONSIBLE PARTY DATA REPLACEMENT-I======================================================
	//=============NOW IF PATIENT HAVE NO RESPONSILE PERSON THEN PATIENT DATA WILL BE REPLACED.=============
	if(count($resDetails)>0){
		  $consent_form_content_data = str_ireplace('{RES.PARTY TITLE}',ucwords($resDetails[0]['title']),$consent_form_content_data);
		  $consent_form_content_data = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($resDetails[0]['fname']),$consent_form_content_data);
		  $consent_form_content_data = str_ireplace('{RES.PARTY MIDDLE NAME}',ucwords($resDetails[0]['mname']),$consent_form_content_data);
		  $consent_form_content_data = str_ireplace('{RES.PARTY LAST NAME}',ucwords($resDetails[0]['lname']),$consent_form_content_data);
		  $consent_form_content_data = str_ireplace('{RES.PARTY DOB}',ucwords($resDetails[0]['res_dob']),$consent_form_content_data);
		  $consent_form_content_data = str_ireplace('{RES.PARTY SS}',ucwords($resDetails[0]['ss']),$consent_form_content_data);
		  $consent_form_content_data = str_ireplace('{RES.PARTY SEX}',ucwords($resDetails[0]['sex']),$consent_form_content_data);
		  $strToShowRelation = $resDetails[0]['relation'];
		  if(strtolower($resDetails[0]['relation']) == "doughter"){
			  $strToShowRelation = "Daughter";
		  }
		  $consent_form_content_data = str_ireplace('{RES.PARTY RELATION}',ucwords($strToShowRelation),$consent_form_content_data);
		  $consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS1}',ucwords($resDetails[0]['address']),$consent_form_content_data);
		  $consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS2}',ucwords($resDetails[0]['address2']),$consent_form_content_data);
		  $consent_form_content_data = str_ireplace('{RES.PARTY HOME PH.}',ucwords($resDetails[0]['home_ph']),$consent_form_content_data);
		  $consent_form_content_data = str_ireplace('{RES.PARTY WORK PH.}',ucwords($resDetails[0]['work_ph']),$consent_form_content_data);
		  $consent_form_content_data = str_ireplace('{RES.PARTY MOBILE PH.}',ucwords($resDetails[0]['mobile']),$consent_form_content_data);
		  $consent_form_content_data = str_ireplace('{RES.PARTY CITY}',ucwords($resDetails[0]['city']),$consent_form_content_data);
		  $consent_form_content_data = str_ireplace('{RES.PARTY STATE}',ucwords($resDetails[0]['state']),$consent_form_content_data);
		  $consent_form_content_data = str_ireplace('{RES.PARTY ZIP}',ucwords($resDetails[0]['zip']),$consent_form_content_data);
		  $consent_form_content_data = str_ireplace('{RES.PARTY MARITAL STATUS}',ucwords($resDetails[0]['marital']),$consent_form_content_data);
		  $consent_form_content_data = str_ireplace('{RES.PARTY DD NUMBER}',ucwords($resDetails[0]['licence']),$consent_form_content_data);
	
	}else{
		
		$consent_form_content_data = str_ireplace('{RES.PARTY TITLE}',ucwords($patientDetails[0]['title']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($patientDetails[0]['fname']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY MIDDLE NAME}',ucwords($patientDetails[0]['mname']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY LAST NAME}',ucwords($patientDetails[0]['lname']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY DOB}',ucwords($patientDetails[0]['pat_dob']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY SS}',ucwords($patientDetails[0]['ss']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY SEX}',ucwords($patientDetails[0]['sex']),$consent_form_content_data);
	    $consent_form_content_data = str_ireplace('{RES.PARTY RELATION}','Self',$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS1}',ucwords($patientDetails[0]['street']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS2}',ucwords($patientDetails[0]['street2']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY HOME PH.}',ucwords($patientDetails[0]['phone_home']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY WORK PH.}',ucwords($patientDetails[0]['phone_biz']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY MOBILE PH.}',ucwords($patientDetails[0]['phone_cell']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY CITY}',ucwords($patientDetails[0]['city']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY STATE}',ucwords($patientDetails[0]['state']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY ZIP}',ucwords($patientDetails[0]['postal_code']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY MARITAL STATUS}',ucwords($patientDetails[0]['status']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY DD NUMBER}',ucwords($patientDetails[0]['driving_licence'])	,$consent_form_content_data);
	}	  
	//=====================================THE END REPONSIBLE PARTY DATA-I========================================

	//--- change epmoyee detail of patient ---
	$consent_form_content_data = str_ireplace('{PATIENT OCCUPATION}',ucwords($patientDetails[0]['occupation']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT EMPLOYER}',ucwords($empDetails[0]['name']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION ADDRESS1}',ucwords($empDetails[0]['street']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION ADDRESS2}',ucwords($empDetails[0]['street2']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION CITY}',ucwords($empDetails[0]['city']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION STATE}',ucwords($empDetails[0]['state']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION ZIP}',ucwords($empDetails[0]['postal_code']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{MONTHLY INCOME}',''.show_currency().''.number_format($patientDetails[0]['monthly_income'],2),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{DATE}',get_date_format(date('Y-m-d')),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{DATE_F}',date("F d, Y"),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{TIME}',date('h:i A'),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OPERATOR NAME}',ucwords(trim($operator_name)),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OPERATOR INITIAL}',ucwords($operator_initial),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT INITIAL}',ucwords($patient_initial),$consent_form_content_data);
	//replacing Primary insurence data
	$consent_form_content_data = str_ireplace('{PRIMARY INSURANCE COMPANY}',ucwords($priInsCompName),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRI INS ADDR}',ucwords($priInsCompAddress),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY POLICY #}',ucwords($priPolicyNumber),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY GROUP #}',ucwords($priGroupNumber),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY SUBSCRIBER NAME}',ucwords($priSubscriberName),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY SUBSCRIBER RELATIONSHIP}',ucwords($priSubscriberRelation),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY BIRTHDATE}',ucwords($priSubscriberDOB),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY SOCIAL SECURITY}',ucwords($priSubscriberSS),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY PHONE}',ucwords($priSubscriberPhone),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY ADDRESS}',ucwords($priSubscriberStreet),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY CITY}',ucwords($priSubscriberCity),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY STATE}',ucwords($priSubscriberState),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY ZIP}',ucwords($priSubscriberZip),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY EMPLOYER}',ucwords($priSubscriberEmployer),$consent_form_content_data);
	//
	//replacing Secondary insurence data
	$consent_form_content_data = str_ireplace('{SECONDARY INSURANCE COMPANY}',ucwords($secInsCompName),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SEC INS ADDR}',ucwords($secInsCompAddress),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY POLICY #}',ucwords($secPolicyNumber),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY GROUP #}',ucwords($secGroupNumber),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY SUBSCRIBER NAME}',ucwords($secSubscriberName),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY SUBSCRIBER RELATIONSHIP}',ucwords($secSubscriberRelation),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY BIRTHDATE}',ucwords($secSubscriberDOB),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY SOCIAL SECURITY}',ucwords($secSubscriberSS),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY PHONE}',ucwords($secSubscriberPhone),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY ADDRESS}',ucwords($secSubscriberStreet),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY CITY}',ucwords($secSubscriberCity),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY STATE}',ucwords($secSubscriberState),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY ZIP}',ucwords($secSubscriberZip),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY EMPLOYER}',ucwords($secSubscriberEmployer),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PatientID}',$_SESSION['patient'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{Appt Date}',$appDate,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{Appt Time}',$appTime,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{Appt Proc}',$ptProc,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{MED HX}',$medHx,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCULAR MEDICATION}',$ocularMed,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SYSTEMIC MEDICATION}',$systemicMed,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT ALLERGIES}',$allergyList,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{CC}',$cc_val,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{HISTORY}',$hx_val,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{ALL_INS_CASE}',$ins_cases,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{APPT_FUTURE}',$upcoming_appt,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{LOGGED_IN_FACILITY_NAME}',$loggedfacName,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{LOGGED_IN_FACILITY_ADDRESS}',$loggedfacAddress,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('<dir>',"",$consent_form_content_data);
	$consent_form_content_data = str_ireplace('</dir>',"",$consent_form_content_data);
	$consent_form_content_data = str_ireplace('<div', '<span', $consent_form_content_data);
	$consent_form_content_data = str_ireplace('</div>', '</span>', $consent_form_content_data);
	$consent_form_content_data = str_ireplace('<meta charset="utf-8" />', '', $consent_form_content_data);
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

	
	$consent_form_content_data = str_ireplace("{PATIENT_NEXT_APPOINTMENT_DATE}",$apptInfoArr2[0],$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{PATIENT_NEXT_APPOINTMENT_TIME}",$apptInfoArr2[8],$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{PATIENT_NEXT_APPOINTMENT_PROVIDER}",$apptInfoArr2[5],$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{PATIENT_NEXT_APPOINTMENT_LOCATION}",$facilityAddress,$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{PATIENT_NEXT_APPOINTMENT_PRIREASON}",$apptInfoArr2[4],$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{PATIENT_NEXT_APPOINTMENT_SECREASON}",$apptInfoArr2[16],$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{PATIENT_NEXT_APPOINTMENT_TERREASON}",$apptInfoArr2[17],$consent_form_content_data);
	
	$consent_form_content_data = str_ireplace("{ARRIVAL_TIME}",$pt_arrival_time,$consent_form_content_data);
	//
	
	$arrAuditTrail = array();	
	##Audit Int.
	$opreaterId = $_SESSION['authId'];			
	$ip = getRealIpAddr();
	$URL = $_SERVER['PHP_SELF'];													 
	$os = getOS();
	$browserInfoArr = array();
	$browserInfoArr = _browser();
	$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
	$browserName = str_ireplace(";","",$browserInfo);													 
	$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);	
	
	if($blClientBrowserIpad == false){
		//START save image for physician signature
		for($ps=1;$ps<=$sig_count_phy;$ps++){
			$postDataPhy = $_POST['SigDataPhy'.$ps];
			$postSignImgPhy = $_POST['hidd_signReplaced'.$ps];
			if($postDataPhy != '' && $postDataPhy != '000000000000000000000000000000000000000000000000000000000000000000000000'  && $postDataPhy !='undefined'){
				$phyNewPath = realpath(dirname(__FILE__).'/../../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/').'/sign_phy_'.$patient_id.'_'.date('d_m_y_h_i_s').'_'.$ps.'.jpeg';
				if(class_exists("COM") && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
					$aConn = new COM("SIGPLUS.SigPlusCtrl.1");
					$aConn->InitSigPlus();
					$aConn->SigCompressionMode = 2;
					$aConn->SigString=$postDataPhy;
					$aConn->ImageFileFormat = 4; //4=jpg, 0=bmp, 6=tif
					$aConn->ImageXSize = 500; //width of resuting image in pixels
					$aConn->ImageYSize =165; //height of resulting image in pixels
					$aConn->ImagePenWidth = 11; //thickness of ink in pixels
					$aConn->JustifyMode = 5;  //center and fit signature to size
					$aConn->WriteImageFile("$phyNewPath");
					$phySignArr[$ps] = $phyNewPath;
				}else {
					getSigImage($postDataPhy,$phyNewPath,$rootServerPath);	
					$phySignArr[$ps] = $phyNewPath;			
				}
				
				$arrAuditTrail [] = 
								array(
										"Pk_Id"=> "",
										"Table_Name"=>"consent_form_signature",
										"Data_Base_Field_Name"=> "signature_content" ,
										"Data_Base_Field_Type"=> fun_get_field_type($signatureDataFields,"signature_content") ,
										"Action"=> "sig_create",
										"Operater_Id"=> $opreaterId,
										"Operater_Type"=> getOperaterType($opreaterId) ,
										"IP"=> $ip,
										"MAC_Address"=> $_REQUEST['macaddrs'],
										"URL"=> $URL,
										"Browser_Type"=> $browserName,
										"OS"=> $os,
										"Machine_Name"=> $machineName,
										"pid"=> $patient_id,
										"Category"=> "signature",
										"Category_Desc"=> "consent_form_signature",	
										"Filed_Label"=> "Form Name ".$consent_form_name."<br>Physician Signaure Position ".$ps,
										"New_Value"=> $postDataPhy																																										
									);	
					
			}else if($postSignImgPhy && $postSignImgPhy != ''){
				$phySignArr[$ps] = urldecode($postSignImgPhy);
			}
		}
		//-- get signature applets ----
		$consent_form_content_data= str_ireplace("{PHYSICIAN SIGNATURE}","{PHYSICIAN SIGNATURE}",$consent_form_content_data);
		$row_phy_arr = explode('{PHYSICIAN SIGNATURE}',$consent_form_content_data);
		$consent_form_content_data = $row_phy_arr[0];
		for($c=1;$c<count($row_phy_arr);$c++){
			$imgNamePhyArr = explode('/',$phySignArr[$c]);
			$imgPhySrc = end($imgNamePhyArr);
			if(!$imgPhySrc) {
				$consent_form_content_data .= '{PHYSICIAN SIGNATURE}';
			}else if($imgPhySrc=='{PHYSICIAN SIGNATURE}') {
				$consent_form_content_data .= '{PHYSICIAN SIGNATURE}';
			}else {
				$imgPhySrc = str_ireplace('consent_forms/','',$imgPhySrc);
				$imgPhySrcNew = $web_root."/data/".PRACTICE_PATH."/PatientId_".$patient_id."/consent_forms/".$imgPhySrc;
				$consent_form_content_data .= '<img src="'.$imgPhySrcNew.'" width="225" height="45">';
			}
			$consent_form_content_data .= $row_phy_arr[$c];
		}
		//END save image for physician signature
	}
	
	if($blClientBrowserIpad == false){
		//START save image for witness signature
		for($ws=1;$ws<=$sig_count_wit;$ws++){
			$postDataWit = $_POST['SigDataWit'.$ws];
			$postSignImgWit = $_POST['hidd_signReplacedWit'.$ws];
			if($postDataWit != '' && $postDataWit != '000000000000000000000000000000000000000000000000000000000000000000000000'  && $postDataWit !='undefined'){
				$wnewPath = realpath(dirname(__FILE__).'/../../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/').'/sign_wit_'.$patient_id.'_'.date('d_m_y_h_i_s').'_'.$ws.'.jpeg';
				if(class_exists("COM") && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
					$wConn = new COM("SIGPLUS.SigPlusCtrl.1");
					$wConn->InitSigPlus();
					$wConn->SigCompressionMode = 2;
					$wConn->SigString=$postDataWit;
					$wConn->ImageFileFormat = 4; //4=jpg, 0=bmp, 6=tif
					$wConn->ImageXSize = 500; //width of resuting image in pixels
					$wConn->ImageYSize =165; //height of resulting image in pixels
					$wConn->ImagePenWidth = 11; //thickness of ink in pixels
					$wConn->JustifyMode = 5;  //center and fit signature to size
					$wConn->WriteImageFile("$wnewPath");
					$witSignArr[$ws] = $wnewPath;
				}else {
					getSigImage($postDataWit,$wnewPath,$rootServerPath);
					$witSignArr[$ws] = $wnewPath;
				}
				
				$arrAuditTrail [] = 
								array(
										"Pk_Id"=> "",
										"Table_Name"=>"consent_form_signature",
										"Data_Base_Field_Name"=> "signature_content" ,
										"Data_Base_Field_Type"=> fun_get_field_type($signatureDataFields,"signature_content") ,
										"Action"=> "sig_create",
										"Operater_Id"=> $opreaterId,
										"Operater_Type"=> getOperaterType($opreaterId) ,
										"IP"=> $ip,
										"MAC_Address"=> $_REQUEST['macaddrs'],
										"URL"=> $URL,
										"Browser_Type"=> $browserName,
										"OS"=> $os,
										"Machine_Name"=> $machineName,
										"pid"=> $patient_id,
										"Category"=> "signature",
										"Category_Desc"=> "consent_form_signature",	
										"Filed_Label"=> "Form Name ".$consent_form_name."<br>Witness Signaure Position ".$ws,
										"New_Value"=> $postDataWit																																										
									);
				
			}else if($postSignImgWit && $postSignImgWit != ''){
				$witSignArr[$ws] = urldecode($postSignImgWit);
			}
		
	
		}
		//-- get signature applets ----
		$consent_form_content_data= str_ireplace("{WITNESS SIGNATURE}","{WITNESS SIGNATURE}",$consent_form_content_data);
		$row_wit_arr = explode('{WITNESS SIGNATURE}',$consent_form_content_data);
		$consent_form_content_data = $row_wit_arr[0];
		for($w=1;$w<count($row_wit_arr);$w++){
			$imgNameWitArr = explode('/',$witSignArr[$w]);
			$imgWitSrc = end($imgNameWitArr);
			if(!$imgWitSrc) {
				$consent_form_content_data .= '{WITNESS SIGNATURE}';
			}else if($imgWitSrc=='{WITNESS SIGNATURE}') {
				$consent_form_content_data .= '{WITNESS SIGNATURE}';
			}else {
				$imgWitSrc = str_ireplace('consent_forms/','',$imgWitSrc);
				$imgWitSrcNew = $web_root."/data/".PRACTICE_PATH."/PatientId_".$patient_id."/consent_forms/".$imgWitSrc;
				$consent_form_content_data .= '<img src="'.$imgWitSrcNew.'" width="225" height="45">';
			}
			$consent_form_content_data .= $row_wit_arr[$w];
		}
		//END save image for witness signature
	}
	
	
	
		
	// save signature ------
	if($blClientBrowserIpad == false){
		for($ps=1;$ps<=$sig_count;$ps++){
			$postData = $_POST['SigData'.$ps];
			if($postData != '' && $postData != '000000000000000000000000000000000000000000000000000000000000000000000000'  && $postData !='undefined'){
				$patNewPath = realpath(dirname(__FILE__).'/../../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/').'/sign_pat_'.$patient_id.'_'.date('d_m_y_h_i_s').'_'.$ps.'.jpeg';
				if(class_exists("COM") && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
					$aConn = new COM("SIGPLUS.SigPlusCtrl.1");
					$aConn->InitSigPlus();
					$aConn->SigCompressionMode = 2;
					$aConn->SigString=$postData;
					$aConn->ImageFileFormat = 4; //4=jpg, 0=bmp, 6=tif
					$aConn->ImageXSize = 500; //width of resuting image in pixels
					$aConn->ImageYSize =165; //height of resulting image in pixels
					$aConn->ImagePenWidth = 11; //thickness of ink in pixels
					$aConn->JustifyMode = 5;  //center and fit signature to size
					$aConn->WriteImageFile("$patNewPath");
					$patientSignArr[$ps] = $patNewPath;
				}else {
					getSigImage($postData,$patNewPath,$rootServerPath);
					$patientSignArr[$ps] = $patNewPath;
				}
				//--- Save Array Fields --------
				$sig_data['signature_content'] = $postData;
				$sig_data['consent_form_id'] = $show_td;
				$sig_data['patient_id'] = $patient_id;
				$sig_data['operator_id'] = $_SESSION['authId'];
				$sig_data['form_information_id'] = $insert_id;
				$sig_data['signature_count'] = $ps;
				$sig_data['signature_image_path'] = addslashes($path);
				$qry = "select consent_form_signature_id from consent_form_signature 
						where form_information_id = '$insert_id' and patient_id = '$patient_id'
						and consent_form_id = '$show_td' and signature_status = 'Active'
						and signature_count = ".$ps."";
				$sigGetDetail = get_array_records_query($qry);
				if($sigGetDetail[0]['consent_form_signature_id']){
					//$sig_insert_id = ManageData::updateRecords($sigGetDetail[0]['consent_form_signature_id'],'consent_form_signature_id',$sig_data,'consent_form_signature');
				}
				else{
					$sig_insert_id = AddRecords($sig_data,'consent_form_signature');
				}
				
				##audit signature##
				$arrAuditTrail [] = 
								array(
										"Pk_Id"=> $sig_insert_id,
										"Table_Name"=>"consent_form_signature",
										"Data_Base_Field_Name"=> "signature_content" ,
										"Data_Base_Field_Type"=> fun_get_field_type($signatureDataFields,"signature_content") ,
										"Action"=> "sig_create",
										"Operater_Id"=> $opreaterId,
										"Operater_Type"=> getOperaterType($opreaterId) ,
										"IP"=> $ip,
										"MAC_Address"=> $_REQUEST['macaddrs'],
										"URL"=> $URL,
										"Browser_Type"=> $browserName,
										"OS"=> $os,
										"Machine_Name"=> $machineName,
										"pid"=> $patient_id,
										"Category"=> "signature",
										"Category_Desc"=> "consent_form_signature",	
										"Filed_Label"=> "Form Name ".$consent_form_name."<br>Signaure Position ".$ps,
										"New_Value"=> $postData																																										
									);
				
			}
		}
		//-- get signature applets ----
		$consent_form_content_data= str_ireplace("{SIGNATURE}","{SIGNATURE}",$consent_form_content_data);
		$row_pat_arr = explode('{SIGNATURE}',$consent_form_content_data);
		$consent_form_content_data = $row_pat_arr[0];
		for($c=1;$c<count($row_pat_arr);$c++){file_put_contents("test1.php",$patientSignArr[$c]);
			//$imgNamePhyArr = explode('\\',$phySignArr[$c-1]);
			$imgNamePatArr = explode('/',$patientSignArr[$c]);
			$imgPatSrc = end($imgNamePatArr);
			if(!$imgPatSrc) {
				$consent_form_content_data .= '{SIGNATURE}';
			}else if($imgPatSrc=='{SIGNATURE}') {
				$consent_form_content_data .= '{SIGNATURE}';
			}else {
				$imgPatSrc = str_ireplace('consent_forms/','',$imgPatSrc);
				$imgPatSrcNew = $web_root."/data/".PRACTICE_PATH."/PatientId_".$patient_id."/consent_forms/".$imgPatSrc;
				$consent_form_content_data .= '<img src="'.$imgPatSrcNew.'" width="225" height="45">';
			}
			$consent_form_content_data .= $row_pat_arr[$c];
		}			
	}
	elseif($blClientBrowserIpad == true){
		for($ps=1;$ps<=$sig_count;$ps++){
			$postData = "";
			$postData = $_POST['canvas_sig_data'.$ps];
			if($postData != "" && $postData != '000000000000000000000000000000000000000000000000000000000000000000000000'  && $postData !='undefined'){				
				$path = $newPath = "";
				$patNewPath = realpath(dirname(__FILE__).'/../../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/').'/sign_pat_'.$patient_id.'_'.date('d_m_y_h_i_s').'_'.$ps.'.jpeg';
				/*
				$path =  realpath(dirname(__FILE__).'/../../SigPlus_images').'/sign_'.$patient_id.'_'.date('d_m_y_h_i_s').'_'.$ps.'.jpeg';
				$patientSignArr[$ps] = $path;
				$newPath = realpath(dirname(__FILE__).'/../../common/'.$htmlFolder.'/').'/sign_'.$patient_id.'_'.date('d_m_y_h_i_s').'_'.$ps.'.jpeg';
				*/
				$signatureData = str_ireplace("data:image/jpeg;base64,","",$postData);				
				file_put_contents($patNewPath,base64_decode($signatureData));				
				
				//--- Save Array Fields --------
				$sig_data['signature_content'] = addslashes(base64_decode($postData));
				$sig_data['consent_form_id'] = $show_td;
				$sig_data['patient_id'] = $patient_id;
				$sig_data['operator_id'] = $_SESSION['authId'];
				$sig_data['form_information_id'] = $insert_id;
				$sig_data['signature_count'] = $ps;
				$sig_data['signature_image_path'] = addslashes($patNewPath);
				$qry = "select consent_form_signature_id from consent_form_signature 
						where form_information_id = '$insert_id' and patient_id = '$patient_id'
						and consent_form_id = '$show_td' and signature_status = 'Active'
						and signature_count = ".$ps."";
				$sigGetDetail = get_array_records_query($qry);
				if($sigGetDetail[0]['consent_form_signature_id']){
					//$sig_insert_id = ManageData::updateRecords($sigGetDetail[0]['consent_form_signature_id'],'consent_form_signature_id',$sig_data,'consent_form_signature');
				}
				else{
					$sig_insert_id = AddRecords($sig_data,'consent_form_signature');
				}
				
				$arrAuditTrail [] = 
								array(
										"Pk_Id"=> $sig_insert_id,
										"Table_Name"=>"consent_form_signature",
										"Data_Base_Field_Name"=> "signature_content" ,
										"Data_Base_Field_Type"=> fun_get_field_type($signatureDataFields,"signature_content") ,
										"Action"=> "sig_create",
										"Operater_Id"=> $opreaterId,
										"Operater_Type"=> getOperaterType($opreaterId) ,
										"IP"=> $ip,
										"MAC_Address"=> $_REQUEST['macaddrs'],
										"URL"=> $URL,
										"Browser_Type"=> $browserName,
										"OS"=> $os,
										"Machine_Name"=> $machineName,
										"pid"=> $patient_id,
										"Category"=> "signature",
										"Category_Desc"=> "consent_form_signature",	
										"Filed_Label"=> "Form Name ".$consent_form_name."<br>Signaure Position ".$ps,
										"New_Value"=> addslashes(base64_decode($postData))																																									
									);
				
			}
		$postData = "";
		}
	}
	$data['consent_form_content_data'] 	= addslashes($consent_form_content_data);	
	$data['package_category_id'] 		= addslashes($package_category_id);	
	$data['form_created_date']			= date("Y-m-d H:i:s");
	 $insert_id = AddRecords($data,'patient_consent_form_information');
	 
	 /*-----SAVING HOLD SIGNATURE INFO-----*/
	 $OBJhold_sign->section_col = "consent_id";
	 $OBJhold_sign->section_col_value = $insert_id;
	 $OBJhold_sign->save_hold_sign();
	 /*-----end of HOLD SIGNATURE INFO-----------*/
	 
##audit signature save##	
$table = array("consent_form_signature");
$error = array($signatureError);
$mergedArray = merging_array($table,$error);	
if($polStSig == 1){
	auditTrail($arrAuditTrail,$mergedArray,0,0,0);
}		
//---- get consent forms -------
$qry = "select consent_form_content_data as consent_form_content,
		form_information_id 
		from patient_consent_form_information
		where consent_form_id = '$consent_form_id' and patient_id = '$patient_id' ORDER BY form_created_date desc LIMIT 1";
$consentDetail = get_array_records_query($qry);
if(count($consentDetail) == 0){
	$qry = "select *,consent_form_id as form_information_id 
			from consent_form where consent_form_id = '$consent_form_id'";
	$consentDetail = get_array_records_query($qry);
	$patientSign = false;
}
else{
	$patientSign = true;
}

for($i=0;$i<count($consentDetail);$i++){
	$consentDetail[$i]['consent_form_content'] = stripslashes($consentDetail[$i]['consent_form_content']);
	//--- change value between curly brackets -------	
	$consent_form_content = str_ireplace('{PATIENT NAME TITLE}',ucwords($patientDetails[0]['title']),$consentDetail[$i]['consent_form_content']);
	$consent_form_content = str_ireplace('{PATIENT FIRST NAME}',ucwords($patientDetails[0]['fname']),$consent_form_content);
	$consent_form_content = str_ireplace('{MIDDLE NAME}',ucwords($patientDetails[0]['mname']),$consent_form_content);
	$consent_form_content = str_ireplace('{LAST NAME}',ucwords($patientDetails[0]['lname']),$consent_form_content);
	$consent_form_content = str_ireplace('{SEX}',ucwords($patientDetails[0]['sex']),$consent_form_content);
	$consent_form_content = str_ireplace('{DOB}',ucwords($patientDetails[0]['pat_dob']),$consent_form_content);
	$consent_form_content = str_ireplace('{AGE}',$patient_age,$consent_form_content);	
	$consent_form_content = str_ireplace('{PATIENT SS}',ucwords($patientDetails[0]['ss']),$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT_NICK_NAME}',ucwords($patientDetails[0]['nick_name']),$consent_form_content);
	//=============START WORK TO SHOW THE LAST 4 DIGIT PATIENT SS==========================
	if(trim($patientDetails[0]['ss'])!==''){
		$consent_form_content = str_ireplace('{PATIENT_SS4}',ucwords(substr_replace($patientDetails[0]['ss'],'XXX-XX',0,6)),$consent_form_content);
	}else{
		$consent_form_content = str_ireplace('{PATIENT_SS4}','',$consent_form_content);
	}
	//===========================END WORK===================================================
	
	$consent_form_content = str_ireplace('{PHYSICIAN NAME}',ucwords($phy_name),$consent_form_content);
	$consent_form_content = str_ireplace('{PHYSICIAN FIRST NAME}',ucwords($phy_fname),$consent_form_content);
	$consent_form_content = str_ireplace('{PHYSICIAN MIDDLE NAME}',ucwords($phy_mname),$consent_form_content);
	$consent_form_content = str_ireplace('{PHYSICIAN LAST NAME}',ucwords($phy_lname),$consent_form_content);
	$consent_form_content = str_ireplace('{PHYSICIAN NAME SUFFIX}',ucwords($phy_name_suffix),$consent_form_content);
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
	$consent_form_content = str_ireplace('{PATIENT MRN}',$patientDetails[0]['External_MRN_1'],$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT MRN2}',$patientDetails[0]['External_MRN_2'],$consent_form_content);
	
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
	

	//new variable added
	//if($patientDetails[0]['postal_code']) {
		$consent_form_content = str_ireplace('{STATE ZIP CODE}',ucwords($patientDetails[0]['state'].' '.$patientDetails[0]['postal_code']),$consent_form_content);
	//}
	$consent_form_content = str_ireplace('{REF PHYSICIAN TITLE}',		trim(ucwords($reffPhyDetail[0]['Title'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHYSICIAN FIRST NAME}',	trim(ucwords($reffPhyDetail[0]['FirstName'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHYSICIAN LAST NAME}',	trim(ucwords($reffPhyDetail[0]['LastName'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY SPECIALITY}',		trim(ucwords($reffPhyDetail[0]['specialty'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY PHONE}',			trim(ucwords($reffPhyDetail[0]['physician_phone'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY FAX}',			trim($reffPhyDetail[0]['physician_fax']),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY STREET ADDR}',		$refPhyAddress,$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY CITY}',				trim(ucwords($reffPhyDetail[0]['City'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY STATE}',			trim(ucwords($reffPhyDetail[0]['State'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY ZIP}',				trim(ucwords($reffPhyDetail[0]['ZipCode'])),$consent_form_content);
	$consent_form_content = str_ireplace('{PCP NAME}',$pcpName,$consent_form_content);
	$consent_form_content = str_ireplace('{PCP STREET ADDR}',$pcpAddress,$consent_form_content);
	$consent_form_content = str_ireplace('{PCP City}',	$pcpPhyDetail[0]['pcpCity'],$consent_form_content);
	$consent_form_content = str_ireplace('{PCP State}',$pcpPhyDetail[0]['pcpState'],$consent_form_content);
	$consent_form_content = str_ireplace('{PCP ZIP}',	$pcpPhyDetail[0]['pcpZipCode'],$consent_form_content);	
	$consent_form_content = str_ireplace($web_root.'/library/images/','../../../library/images/',$consent_form_content);
	
	//=============RESPONSIBLE PARTY DATA REPLACEMENT-II======================================================
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
	//=====================================THE END REPONSIBLE PARTY DATA-II========================================

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
	$consent_form_content = str_ireplace('{TEXTBOX_XSMALL}',"<input type='text' class='form-control ' name='xsmall' size='1' maxlength='1'>",$consent_form_content);
	$consent_form_content = str_ireplace('{TEXTBOX_SMALL}',"<input type='text' class='form-control ' name='small' size='30' maxlength='30'>",$consent_form_content);
	$consent_form_content = str_ireplace('{TEXTBOX_MEDIUM}',"<input type='text' name='medium' size='60' maxlength='60'>",$consent_form_content);
	$consent_form_content = str_ireplace('{TEXTBOX_LARGE}',"<textarea name='large' cols='100' rows='2'></textarea>",$consent_form_content);
	//replacing Primary insurence data
	$consent_form_content = str_ireplace('{PRIMARY INSURANCE COMPANY}',ucwords($priInsCompName),$consent_form_content);
	$consent_form_content = str_ireplace('{PRI INS ADDR}',ucwords($priInsCompAddress),$consent_form_content);
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
	$consent_form_content = str_ireplace('{SEC INS ADDR}',ucwords($secInsCompAddress),$consent_form_content);
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
	$consent_form_content = str_ireplace('<dir>',"",$consent_form_content);
	$consent_form_content = str_ireplace('</dir>',"",$consent_form_content);
	
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
	
	$consent_form_content = str_ireplace("{ARRIVAL_TIME}",$pt_arrival_time,$consent_form_content);
	
	//-- get signature value -------
	$qry = "select form_information_id from patient_consent_form_information 
			where consent_form_id = '$consent_form_id' and patient_id = '$patient_id'";
	$consentDetailsInfo = get_array_records_query($qry);
	$form_information_id = $consentDetailsInfo[0]['form_information_id'];
	$qry = "select signature_content from consent_form_signature 
			where form_information_id = '$form_information_id' and patient_id = '$patient_id'
			and consent_form_id = '$consent_form_id' and signature_status = 'Active'";
	$sigDetail = get_array_records_query($qry);
	//-- get signature applets ----
	$row_arr = explode('{START APPLET ROW}',$consent_form_content);
	$sig_arr = explode('{SIGNATURE}',$row_arr[0]);	
	$sig_data = '';
	$ds = 1;
	for($s=1;$s<count($sig_arr);$s++,$ds++){
		if($blClientBrowserIpad == false){
			$sig_data = '
						<span width="145" style="border:solid 1px; border-color:#FF9900;" bordercolor="#FF9900">
							<div style="HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;">
							<OBJECT classid=clsid:69A40DA3-4D42-11D0-86B0-0000C025864A height=75
									id=SigPlus'.$ds.' name=SigPlus'.$ds.'
									style="HEIGHT: 87px; WIDTH: 310px; LEFT: 0px; TOP: 0px;" 
									VIEWASTEXT>
									<PARAM NAME="_Version" VALUE="131095">
									<PARAM NAME="_ExtentX" VALUE="4842">
									<PARAM NAME="_ExtentY" VALUE="1323">
									<PARAM NAME="_StockProps" VALUE="0">
							</OBJECT></div>
						</span>
						<span>
							<img style="cursor:pointer;" src="../../../library/images/pen.png" id="SignBtn'.$ds.'" name="SignBtn'.$ds.'" language="VBScript" onclick="OnSign'.$ds.'()">
						</span>
						<span>
							<img style="cursor:pointer;" src="../../../library/images/eraser.gif" id="button'.$ds.'" alt="Clear Sign" name="ClearBtn'.$ds.'" language="VBScript" onclick="OnClear'.$ds.'()">
						</span>
					';
			
		}
		elseif($blClientBrowserIpad == true){
			$canvasId = $hidCanvasSigData = "";
			$canvasId = "canvas".$ds;	
			$hidCanvasSigData = "canvas_sig_data".$ds;			
			$sig_data = '<span width="320">
							<canvas id="'.$canvasId.'" name="'.$canvasId.'" width="320" height="100" style="border: 1px solid #F60;"></canvas>
							<input type="hidden" name="'.$hidCanvasSigData.'" id="'.$hidCanvasSigData.'"/>
						</span>	
						<span>
							<img style="cursor:pointer;" src="../../../library/images/pen.png" id="SignBtn'.$ds.'" name="SignBtn'.$ds.'" language="VBScript" onclick="OnSign'.$ds.'()">
						</span>					
					';
		}		
		$str_data = $sig_arr[$s];
		$sig_arr[$s] = $sig_data;
		$sig_arr[$s] .= $str_data;
		$hiddenFields[] = true;
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
										<td style="border:solid 1px;" bordercolor="#FF9900">';
										if($blClientBrowserIpad == false){
											$td_sign .= '<div style="HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;">
													<OBJECT classid=clsid:69A40DA3-4D42-11D0-86B0-0000C025864A height=75
																id="SigPlus'.$ds.'" name="SigPlus'.$ds.'"
																style="HEIGHT: 87px; WIDTH: 310px; LEFT: 0px; TOP: 0px;" 
																VIEWASTEXT>
																<PARAM NAME="_Version" VALUE="131095">
																<PARAM NAME="_ExtentX" VALUE="4842">
																<PARAM NAME="_ExtentY" VALUE="1323">
																<PARAM NAME="_StockProps" VALUE="0">
														</OBJECT></div>';
											$td_sign .= '</td>
														<td valign="bottom" id="Sign_icon_'.$ds.'">
															<img style="cursor:pointer;" src="../../../library/images/pen.png" id="SignBtn'.$ds.'" name="SignBtn'.$ds.'" language="VBScript" onclick="OnSign'.$ds.'()"><br>
															<img style="cursor:pointer;" src="../../../library/images/eraser.gif" id="button'.$ds.'" name="ClearBtn'.$ds.'" alt="Clear Sign" language="VBScript" onclick="OnClear'.$ds.'()">											
														</td>';
										}
										elseif($blClientBrowserIpad == true){
											// the client browser is Ipad Safari
											$canvasId = $hidCanvasSigData = "";
											$canvasId = "canvas".$ds;
											$hidCanvasSigData = "canvas_sig_data".$ds;	
											$td_sign .= '<canvas id="'.$canvasId.'" name="'.$canvasId.'" width="320" height="100" style="border: 1px solid #F60;"></canvas>';
											$td_sign .= '<input type="hidden" name="'.$hidCanvasSigData.'" id="'.$hidCanvasSigData.'"/>';
											$td_sign .= '</td>
														<td valign="bottom" id="Sign_icon_'.$ds.'();">
															<img style="cursor:pointer;" src="../../../library/images/pen.png" id="SignBtn'.$ds.'" name="SignBtn'.$ds.'" language="VBScript" onclick="OnSign'.$ds.'()">
														</td>';
										}			
									$td_sign .= '</tr>
											</table>
										</td>';
			$s++;
			$hiddenFields[] = true;
			}
			$content_row .= '
				<table border="1" align="center">
					<tr>
						'.$td_sign.'						
					</tr>
				</table>
			';
		}
	}
	$jh = 1;
	$consent_form_content .= $content_row;
	//--- get all content of consent forms -------	
	$consent_content .= '
		<table id="content_'.$consent_form_id.'" style="display:'.$display.'" width="100%" align="center" cellpadding="1" cellspacing="1" border="0">
			<tr>
				<td align="left" colspan="'.count($sig_arr).'">'.$consent_form_content.'</td>
			</tr>
		</table>
	';
}
//-- get new versions of consent forms ------
$qry = "select consent_form_name from consent_form where consent_form_id = '$consent_form_id'";
$res = get_array_records_query($qry);
$consent_form_name = $res[0]['consent_form_name'];
//-- get new versions of consent forms ------
$qry = "select consent_form_id from consent_form
		where consent_form_name = '$consent_form_name'
		order by consent_form_name , consent_form_version desc
		limit 0,1";
$formDetail = get_array_records_query($qry);
$new_consent_form_id = array();
$new_consent_form_id = $formDetail[0]['consent_form_id'];
if($new_consent_form_id == $ver_option){
	$ver_option = '';
}
$qry = "select consent_form_name, consent_form_id, form_created_date
		from consent_form
		where consent_form_name = '$consent_form_name' 
		and consent_form_id != '$consent_form_id'";
$res = get_array_records_query($qry);


//checkprocedurenote --

if(!empty($pop_procedure)){	
	exit();
}

//checkprocedurenote --


}

//else 
//{
//echo 'on load this code will run';

$form_content=array();
if(isset($_GET["chart_procedures_id"]) && !empty($_GET["chart_procedures_id"])){
	$qry = "select consent_form_content_data AS consent_form_content, form_information_id from patient_consent_form_information 
			where consent_form_id = '$consent_form_id' AND chart_procedure_id = '".$_GET["chart_procedures_id"]."' ";	
	$form_content = get_array_records_query($qry);		
	if(count($form_content)>0){
		$form_content[0]['consent_form_content'] = str_ireplace($web_root."/interface/common/","interface/common/",$form_content[0]['consent_form_content']);
		$form_content[0]['consent_form_content'] = str_ireplace("interface/common/",$web_root."/interface/common/",$form_content[0]['consent_form_content']);
	}
}

if(count($form_content)<=0){
	$qry = "select consent_form_content from consent_form
			where consent_form_id = '$consent_form_id'";
	$form_content = get_array_records_query($qry);
}

	$consent_form_content_data = stripslashes($form_content[0]['consent_form_content']);
	//$consent_form_content_data = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $consent_form_content_data);
	
	if(!empty($form_content[0]['form_information_id'])) { $elem_form_information_id = $form_content[0]['form_information_id']; }
	
	$arrStr = array("{TEXTBOX_XSMALL}","{TEXTBOX_SMALL}","{TEXTBOX_MEDIUM}","{TEXTBOX_LARGE}");
			
	for($j = 0;$j<count($arrStr);$j++)
	{
	
		if($arrStr[$j] == '{TEXTBOX_XSMALL}')
		{
			$name = 'xsmall';
			$size = 1;
		}
		else if($arrStr[$j] == '{TEXTBOX_SMALL}')
		{
			$name = 'small';
			$size = 30;
		}
		else if($arrStr[$j] == '{TEXTBOX_MEDIUM}')
		{
			$name = 'medium';
			$size = 60;
		}
		else if($arrStr[$j] == '{TEXTBOX_LARGE}')
		{
			$name = 'large';
			$size = 120;
			
		}
		$repVal = '';
		if(substr_count($consent_form_content_data,$arrStr[$j]) > 1)
		{
			
			if($arrStr[$j] == '{TEXTBOX_XSMALL}' || $arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}')
			{
				$c = 1;
				$arrExp = explode($arrStr[$j],$consent_form_content_data);
				
				for($p = 0;$p<count($arrExp)-1;$p++)
				{
					$repVal .= $arrExp[$p].'<input type="text" class="form-control " name="'.$name.$c.'" value="'.htmlentities($_POST[$name.$c]).'" size="'.$size.'"  maxlength="'.$size.'">';
					$c++;
				}
				$repVal .= end($arrExp);
				$consent_form_content_data = $repVal;
			}
			else if($arrStr[$j] == '{TEXTBOX_LARGE}')
			{
				$c = 1;
				$arrExp = explode($arrStr[$j],$consent_form_content_data);
				
				for($p = 0;$p<count($arrExp)-1;$p++)
				{
					$repVal .= $arrExp[$p].'<textarea rows="2" cols="100" name="'.$name.$c.'"> '.htmlentities($_POST[$name.$c]).' </textarea>';
					$c++;
				}
				$repVal .= end($arrExp);
				$consent_form_content_data = $repVal;
			}
		}
		else
		{
			if($arrStr[$j] == '{TEXTBOX_XSMALL}') {
				$repVal = str_ireplace($arrStr[$j],'<input type="text" class="form-control " name="'.$name.'" value="'.htmlentities($_POST[$name]).'" size="'.$size.'"  maxlength="'.$size.'">',$consent_form_content_data);
				$consent_form_content_data = $repVal;
			}else if($arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}'){
				$repVal = str_ireplace($arrStr[$j],'<input type="text" class="form-control " name="'.$name.'" value="'.htmlentities($_POST[$name]).'" size="'.$size.'" >',$consent_form_content_data);
				$consent_form_content_data = $repVal;
			}else if($arrStr[$j] == '{TEXTBOX_LARGE}')
			{
				$repVal = str_ireplace($arrStr[$j],'<textarea rows="2" cols="100" name="'.$name.'"> '.htmlentities($_POST[$name]).' </textarea>',$consent_form_content_data);
				$consent_form_content_data = $repVal;
			}
		}
		 
	}
	
	//-- get signature applets ----
	$row_arr = explode('{SIGNATURE}',$consent_form_content_data);
	$consent_form_content_data = $row_arr[0];
	for($c=1;$c<count($row_arr);$c++){
		$imgNameArr = explode('/',$patientSignArr[$c-1]);
		$imgSrc = end($imgNameArr);
		if(file_exists(dirname(__FILE__).'/'.$imgSrc) && is_dir(dirname(__FILE__).'/'.$imgSrc) == ''){
			$consent_form_content_data .= '<br><span style="text-align: left" ><img name="'.$imgSrc.'" id="sign_id[]" src="'.$imgSrc.'" width="250" height="83"></span>';
		}
		else{
			$consent_form_content_data .= '{SIGNATURE}';
		}
		$consent_form_content_data .= $row_arr[$c];
	}

/*--REPLACING SMART TAGS (IF FOUND) WITH LINKS--*/
	$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
	$tagconsent_form_content_data = '';
	foreach($arr_smartTags as $key=>$val){
//		$val = str_replace('&nbsp',' ',$val);
		$regpattern='/(\['.$val.'\])+/i';
		$row_arr = preg_split($regpattern,$consent_form_content_data);
//		$row_arr = explode("[".$val."]",$consent_form_content_data);
		$tagconsent_form_content_data = $row_arr[0];
		for($tagCount=1;$tagCount<count($row_arr);$tagCount++){
			//$tagconsent_form_content_data .= '<a id="'.$key.'_'.$tagCount.'tag" class="cls_smart_tags_link" href="javascript:;">'.$val.'</a><input type="hidden" class="'.$key.'_'.$tagCount.'tag" name="'.$key.'_'.$tagCount.'tag" value="">'.$row_arr[$tagCount];
			$tagconsent_form_content_data .= '<a id="'.$key.'" class="cls_smart_tags_link" href="javascript:;">'.$val.'</a><input type="hidden" class="'.$key.'" name="'.$key.'" value="">'.$row_arr[$tagCount];
		}
		
		if($tagconsent_form_content_data != ''){//die($tagconsent_form_content_data);
			$consent_form_content_data = $tagconsent_form_content_data;
		}
	}
/*--SMART TAG REPLACEMENT END--*/

	
	//--- change value between curly brackets -------	
	$consent_form_content_data = str_ireplace('{PATIENT NAME TITLE}',ucwords($patientDetails[0]['title']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT FIRST NAME}',ucwords($patientDetails[0]['fname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{MIDDLE NAME}',ucwords($patientDetails[0]['mname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{LAST NAME}',ucwords($patientDetails[0]['lname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SEX}',ucwords($patientDetails[0]['sex']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{DOB}',ucwords($patientDetails[0]['pat_dob']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{AGE}',$patient_age,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT SS}',ucwords($patientDetails[0]['ss']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT_NICK_NAME}',ucwords($patientDetails[0]['nick_name']),$consent_form_content_data	);
	//=============START WORK TO SHOW THE LAST 4 DIGIT PATIENT SS==========================
	if(trim($patientDetails[0]['ss'])!==''){
		$consent_form_content_data = str_ireplace('{PATIENT_SS4}',ucwords(substr_replace($patientDetails[0]['ss'],'XXX-XX',0,6)),$consent_form_content_data);
	}else{
		$consent_form_content_data = str_ireplace('{PATIENT_SS4}','',$consent_form_content_data);
	}
	//===========================END WORK===================================================
	
	$consent_form_content_data = str_ireplace('{PHYSICIAN NAME}',ucwords($phy_name),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PHYSICIAN FIRST NAME}',ucwords($phy_fname),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PHYSICIAN MIDDLE NAME}',ucwords($phy_mname),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PHYSICIAN LAST NAME}',ucwords($phy_lname),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PHYSICIAN NAME SUFFIX}',ucwords($phy_name_suffix),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{MARITAL STATUS}',ucwords($patientDetails[0]['status']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{ADDRESS1}',ucwords($patientDetails[0]['street']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{ADDRESS2}',ucwords($patientDetails[0]['street2']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{HOME PHONE}',ucwords($patientDetails[0]['phone_home']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{EMERGENCY CONTACT}',ucwords($patientDetails[0]['contact_relationship']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{EMERGENCY CONTACT PH}',ucwords($patientDetails[0]['phone_contact']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{MOBILE PHONE}',ucwords($patientDetails[0]['phone_cell']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{WORK PHONE}',ucwords($patientDetails[0]['phone_biz']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT CITY}',ucwords($patientDetails[0]['city']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT STATE}',ucwords($patientDetails[0]['state']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT ZIP}',ucwords($patientDetails[0]['postal_code']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REGISTRATION DATE}',ucwords($patientDetails[0]['reg_date']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REFFERING PHY.}',ucwords($reffer_name),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{POS FACILITY}',ucwords($pos_facility_name),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{DRIVING LICENSE}',ucwords($patientDetails[0]['driving_licence']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{HEARD ABOUT US}',ucwords($patientDetails[0]['heard_abt_us']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{HEARD ABOUT US DETAIL}',$patientDetails[0]['heard_abt_desc'],$consent_form_content_data);

	$consent_form_content_data = str_ireplace('{EMAIL ADDRESS}',$patientDetails[0]['email'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{USER DEFINE 1}',$patientDetails[0]['genericval1'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT MRN}',$patientDetails[0]['External_MRN_1'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT MRN2}',$patientDetails[0]['External_MRN_2'],$consent_form_content_data);
	
	$languageShow 			   = str_ireplace("Other -- ","",$patientDetails[0]['language']);
	$raceShow				   = trim($patientDetails[0]["race"]);
	$otherRace				   = trim($patientDetails[0]["otherRace"]);
	if($otherRace) { 
		$raceShow			   = $otherRace;
	}
	$ethnicityShow			   = trim($patientDetails[0]["ethnicity"]);			
	$otherEthnicity			   = trim($patientDetails[0]["otherEthnicity"]);
	if($otherEthnicity) { 
		$ethnicityShow		   = $otherEthnicity;
	}

	$consent_form_content_data = str_ireplace('{RACE}',$raceShow,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{LANGUAGE}',$languageShow,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{ETHNICITY}',$ethnicityShow,$consent_form_content_data);
	
	$consent_form_content_data = str_ireplace('{REL INFO}',$obj_print_pt_key->getPtReleaseInfoNames($patient_id),$consent_form_content_data);
	//new variable added
	//if($patientDetails[0]['postal_code']) {
		$consent_form_content_data = str_ireplace('{STATE ZIP CODE}',ucwords($patientDetails[0]['state'].' '.$patientDetails[0]['postal_code']),$consent_form_content_data);
	//}
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN TITLE}',		trim(ucwords($reffPhyDetail[0]['Title'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN FIRST NAME}',	trim(ucwords($reffPhyDetail[0]['FirstName'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN LAST NAME}',	trim(ucwords($reffPhyDetail[0]['LastName'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY SPECIALITY}',		trim(ucwords($reffPhyDetail[0]['specialty'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY PHONE}',			trim(ucwords($reffPhyDetail[0]['physician_phone'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY FAX}',				trim($reffPhyDetail[0]['physician_fax']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY STREET ADDR}',		$refPhyAddress,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY CITY}',				trim(ucwords($reffPhyDetail[0]['City'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY STATE}',			trim(ucwords($reffPhyDetail[0]['State'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY ZIP}',				trim(ucwords($reffPhyDetail[0]['ZipCode'])),$consent_form_content_data);
	
	$consent_form_content_data = str_ireplace('{PCP NAME}',$pcpName,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP STREET ADDR}',$pcpAddress,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP City}',	$pcpPhyDetail[0]['pcpCity'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP State}',$pcpPhyDetail[0]['pcpState'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP ZIP}',	$pcpPhyDetail[0]['pcpZipCode'],$consent_form_content_data);	
	$consent_form_content_data = str_ireplace($web_root.'/library/images/','../../../library/images/',$consent_form_content_data);
	//=============RESPONSIBLE PARTY DATA REPLACEMENT-III======================================================
	//=============NOW IF PATIENT HAVE NO RESPONSILE PERSON THEN PATIENT DATA WILL BE REPLACED.=============
	if(count($resDetails)>0){
	  $consent_form_content_data = str_ireplace('{RES.PARTY TITLE}',ucwords($resDetails[0]['title']),$consent_form_content_data);
  
	  $consent_form_content_data = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($resDetails[0]['fname']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY MIDDLE NAME}',ucwords($resDetails[0]['mname']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY LAST NAME}',ucwords($resDetails[0]['lname']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY DOB}',ucwords($resDetails[0]['res_dob']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY SS}',ucwords($resDetails[0]['ss']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY SEX}',ucwords($resDetails[0]['sex']),$consent_form_content_data);
	  $strToShowRelation = $resDetails[0]['relation'];
	  if(strtolower($resDetails[0]['relation']) == "doughter"){
		  $strToShowRelation = "Daughter";
	  }
	  $consent_form_content_data = str_ireplace('{RES.PARTY RELATION}',ucwords($strToShowRelation),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS1}',ucwords($resDetails[0]['address']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS2}',ucwords($resDetails[0]['address2']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY HOME PH.}',ucwords($resDetails[0]['home_ph']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY WORK PH.}',ucwords($resDetails[0]['work_ph']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY MOBILE PH.}',ucwords($resDetails[0]['mobile']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY CITY}',ucwords($resDetails[0]['city']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY STATE}',ucwords($resDetails[0]['state']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY ZIP}',ucwords($resDetails[0]['zip']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY MARITAL STATUS}',ucwords($resDetails[0]['marital']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY DD NUMBER}',ucwords($resDetails[0]['licence']),$consent_form_content_data);
	}else{
	  $consent_form_content_data = str_ireplace('{RES.PARTY TITLE}',ucwords($patientDetails[0]['title']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($patientDetails[0]['fname']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY MIDDLE NAME}',ucwords($patientDetails[0]['mname']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY LAST NAME}',ucwords($patientDetails[0]['lname']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY DOB}',ucwords($patientDetails[0]['pat_dob']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY SS}',ucwords($patientDetails[0]['ss']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY SEX}',ucwords($patientDetails[0]['sex']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY RELATION}','Self',$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS1}',ucwords($patientDetails[0]['street']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS2}',ucwords($patientDetails[0]['street2']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY HOME PH.}',ucwords($patientDetails[0]['phone_home']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY WORK PH.}',ucwords($patientDetails[0]['phone_biz']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY MOBILE PH.}',ucwords($patientDetails[0]['phone_cell']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY CITY}',ucwords($patientDetails[0]['city']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY STATE}',ucwords($patientDetails[0]['state']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY ZIP}',ucwords($patientDetails[0]['postal_code']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY MARITAL STATUS}',ucwords($patientDetails[0]['status']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY DD NUMBER}',ucwords($patientDetails[0]['driving_licence']),$consent_form_content_data);	
	}
	//=====================================THE END REPONSIBLE PARTY DATA-III========================================
	//--- change epmoyee detail of patient ---
	$consent_form_content_data = str_ireplace('{PATIENT OCCUPATION}',ucwords($patientDetails[0]['occupation']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT EMPLOYER}',ucwords($empDetails[0]['name']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION ADDRESS1}',ucwords($empDetails[0]['street']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION ADDRESS2}',ucwords($empDetails[0]['street2']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION CITY}',ucwords($empDetails[0]['city']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION STATE}',ucwords($empDetails[0]['state']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION ZIP}',ucwords($empDetails[0]['postal_code']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{MONTHLY INCOME}',''.show_currency().''.number_format($patientDetails[0]['monthly_income'],2),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{DATE}',get_date_format(date('Y-m-d')),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{DATE_F}',date("F d, Y"),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{TIME}',date('h:i A'),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OPERATOR NAME}',ucwords(trim($operator_name)),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OPERATOR INITIAL}',ucwords($operator_initial),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT INITIAL}',ucwords($patient_initial),$consent_form_content_data);
	//replacing Primary insurence data
	$consent_form_content_data = str_ireplace('{PRIMARY INSURANCE COMPANY}',ucwords($priInsCompName),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRI INS ADDR}',ucwords($priInsCompAddress),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY POLICY #}',ucwords($priPolicyNumber),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY GROUP #}',ucwords($priGroupNumber),$consent_form_content_data);
	$priSubscriberName = str_ireplace('&amp;nbsp;', '&nbsp;', $priSubscriberName);
	$priSubscriberName = str_ireplace('&nbsp;', ' ', $priSubscriberName);
	$consent_form_content_data = str_ireplace('{PRIMARY SUBSCRIBER NAME}',ucwords($priSubscriberName),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY SUBSCRIBER RELATIONSHIP}',ucwords($priSubscriberRelation),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY BIRTHDATE}',ucwords($priSubscriberDOB),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY SOCIAL SECURITY}',ucwords($priSubscriberSS),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY PHONE}',ucwords($priSubscriberPhone),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY ADDRESS}',ucwords($priSubscriberStreet),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY CITY}',ucwords($priSubscriberCity),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY STATE}',ucwords($priSubscriberState),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY ZIP}',ucwords($priSubscriberZip),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PRIMARY EMPLOYER}',ucwords($priSubscriberEmployer),$consent_form_content_data);
	//
	//replacing Secondary insurence data
	$consent_form_content_data = str_ireplace('{SECONDARY INSURANCE COMPANY}',ucwords($secInsCompName),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SEC INS ADDR}',ucwords($secInsCompAddress),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY POLICY #}',ucwords($secPolicyNumber),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY GROUP #}',ucwords($secGroupNumber),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY SUBSCRIBER NAME}',ucwords($secSubscriberName),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY SUBSCRIBER RELATIONSHIP}',ucwords($secSubscriberRelation),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY BIRTHDATE}',ucwords($secSubscriberDOB),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY SOCIAL SECURITY}',ucwords($secSubscriberSS),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY PHONE}',ucwords($secSubscriberPhone),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY ADDRESS}',ucwords($secSubscriberStreet),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY CITY}',ucwords($secSubscriberCity),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY STATE}',ucwords($secSubscriberState),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY ZIP}',ucwords($secSubscriberZip),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SECONDARY EMPLOYER}',ucwords($secSubscriberEmployer),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PatientID}',$_SESSION['patient'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{Appt Date}',$appDate,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{Appt Time}',$appTime,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{Appt Proc}',$ptProc,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{MED HX}',$medHx,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCULAR MEDICATION}',$ocularMed,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SYSTEMIC MEDICATION}',$systemicMed,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT ALLERGIES}',$allergyList,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{CC}',$cc_val,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{HISTORY}',$hx_val,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{ALL_INS_CASE}',$ins_cases,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{APPT_FUTURE}',$upcoming_appt,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{LOGGED_IN_FACILITY_NAME}',$loggedfacName,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{LOGGED_IN_FACILITY_ADDRESS}',$loggedfacAddress,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('<dir>',"",$consent_form_content_data);
	$consent_form_content_data = str_ireplace('</dir>',"",$consent_form_content_data);
	//Replacing the procedures site with consent site variable.
	$consent_form_content_data = str_ireplace('{SITE}',$proc_site,$consent_form_content_data);
	
	$consent_form_content_data = str_ireplace('&amp;nbsp;', '&nbsp;', $consent_form_content_data);
	$consent_form_content_data = str_ireplace('&nbsp;', ' ', $consent_form_content_data);
	$consent_form_content_data = str_ireplace('<div', '<span', $consent_form_content_data);
	$consent_form_content_data = str_ireplace('</div>', '</span>', $consent_form_content_data);
	$consent_form_content_data = str_ireplace('<meta charset="utf-8" />', '', $consent_form_content_data);
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

	
	$consent_form_content_data = str_ireplace("{PATIENT_NEXT_APPOINTMENT_DATE}",$apptInfoArr2[0],$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{PATIENT_NEXT_APPOINTMENT_TIME}",$apptInfoArr2[8],$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{PATIENT_NEXT_APPOINTMENT_PROVIDER}",$apptInfoArr2[5],$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{PATIENT_NEXT_APPOINTMENT_LOCATION}",$facilityAddress,$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{PATIENT_NEXT_APPOINTMENT_PRIREASON}",$apptInfoArr2[4],$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{PATIENT_NEXT_APPOINTMENT_SECREASON}",$apptInfoArr2[16],$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{PATIENT_NEXT_APPOINTMENT_TERREASON}",$apptInfoArr2[17],$consent_form_content_data);
	
	$consent_form_content_data = str_ireplace("{ARRIVAL_TIME}",$pt_arrival_time,$consent_form_content_data);
	
	$row_arr = explode('{START APPLET ROW}',$consent_form_content_data);
	$sig_arr = explode('{SIGNATURE}',$row_arr[0]);	
	$sig_data = '';
	$ds = 1;
	for($s=1;$s<count($sig_arr);$s++,$ds++){
		$sig_data = '<table class="alignLeft" style="border:none;" id="consentSigIpadId'.$ds.'">
						<tr>';
		
		if($blClientBrowserIpad == false){
			$sig_grid = $sig_onSignClick = $sig_onClearClick = '';	
			if( $enable_sig_web ) {
				$sig_grid = '<canvas id="SigPlus'.$ds.'" name="SigPlus'.$ds.'" width="310" height="87"></canvas>';
				$sig_onSignClick = 'OnSign('.$ds.',\'SigPlus'.$ds.'\',\'SigData'.$ds.'\')';
				$sig_onClearClick = 'OnClear('.$ds.',\'SigPlus'.$ds.'\',\'SigData'.$ds.'\')';
			}
			else {
				$sig_grid = '<OBJECT classid=clsid:69A40DA3-4D42-11D0-86B0-0000C025864A height=75
										id=SigPlus'.$ds.' name=SigPlus'.$ds.'
										style="HEIGHT: 87px; WIDTH: 310px; LEFT: 0px; TOP: 0px;" 
										VIEWASTEXT>
										<PARAM NAME="_Version" VALUE="131095">
										<PARAM NAME="_ExtentX" VALUE="4842">
										<PARAM NAME="_ExtentY" VALUE="1323">
										<PARAM NAME="_StockProps" VALUE="0">
								</OBJECT>';
				$sig_onSignClick = 'OnSign'.$ds.'()';
				$sig_onClearClick = 'OnClear'.$ds.'()';
				
			}
			$sig_data .= '<td>
							<span width="145" >
								<div class="consentObjectBeforSign" id="tdObject'.$ds.'" style="HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;">'.$sig_grid.'
								</div>
							</span>
							<span>
								<img title="Sig Pad Signature" style="cursor:pointer;" src="../../../library/images/pen.png" id="SignBtn'.$ds.'" name="SignBtn'.$ds.'" language="VBScript" onclick="'.$sig_onSignClick.'">
							</span>
							<span>
								<img title="Touch Signature" style="display:inline-block;position:relative;cursor:pointer;width:37px ; height:30px;" src="../../../library/images/touch.svg" id="SignBtnTouch'.$ds.'" name="SignBtnTouch'.$ds.'" onclick="OnSignIpadPhy(\''.$_SESSION['patient'].'\',\'ptConsent\',\'consentSigIpadId'.$ds.'\',\''.$ds.'\')"> 
							</span>
							<span>
								<img style="cursor:pointer;" src="../../../library/images/eraser.gif" id="button'.$ds.'"  name="ClearBtn'.$ds.'" language="VBScript" onclick="'.$sig_onClearClick.'">
							</span>
						</td>
					';
			
			

		}
		elseif($blClientBrowserIpad == true){
			$canvasId = $hidCanvasSigData = "";
			$canvasId = "canvas".$ds;	
			$hidCanvasSigData = "canvas_sig_data".$ds;		
			$sig_data .= '<td style="width:320px;height:90px;" class="consentObjectBeforSign" >
							<img style="cursor:pointer; float:right; margin-top:50px;" src="../../../library/images/pen.png" id="SigPen'.$ds.'" onclick="OnSignIpadPhy(\''.$_SESSION['patient'].'\',\'ptConsent\',\'consentSigIpadId'.$ds.'\',\''.$ds.'\')">
						  </td>			
						';
		}
		$sig_data .='	<tr>
					 </table>';
		$str_data = $sig_arr[$s];
		$sig_arr[$s] = $sig_data;
		$sig_arr[$s] .= $str_data;
		$hiddenFields[] = true;
	}
	$consent_form_content = implode(' ',$sig_arr);
	
/*---witness signature replacement---*/
	$sig_wit_arr = explode('{WITNESS SIGNATURE}',$consent_form_content);
	$sig_wit_data = '';
	$ws=1;
	for($s=1;$s<count($sig_wit_arr);$s++,$ws++){
		$sig_wit_data = '<table class="alignLeft" style="border:none;" id="consentSigIpadIdWit'.$ws.'">
							<tr>';
		
		if($blClientBrowserIpad == false){
			$sig_grid = $sig_onSignClick = $sig_onClearClick = '';	
			if( $enable_sig_web ) {
				$sig_grid = '<canvas id="SigPlusWit'.$ws.'" name="SigPlusWit'.$ws.'" width="310" height="87"></canvas>';
				$sig_onSignClick = 'OnSign('.$ws.',\'SigPlusWit'.$ws.'\',\'SigDataWit'.$ws.'\')';
				$sig_onClearClick = 'OnClear('.$ws.',\'SigPlusWit'.$ws.'\',\'SigDataWit'.$ws.'\')';
			}
			else {
				$sig_grid = '<OBJECT classid=clsid:69A40DA3-4D42-11D0-86B0-0000C025864A height=75
													id=SigPlusWit'.$ws.' name=SigPlusWit'.$ws.'
													style="HEIGHT: 87px; WIDTH: 310px; LEFT: 0px; TOP: 0px;" 
													VIEWASTEXT>
													<PARAM NAME="_Version" VALUE="131095">
													<PARAM NAME="_ExtentX" VALUE="4842">
													<PARAM NAME="_ExtentY" VALUE="1323">
													<PARAM NAME="_StockProps" VALUE="0">
											</OBJECT>';
				$sig_onSignClick = 'OnSignWit'.$ws.'();';
				$sig_onClearClick = 'ClearBtnWit'.$ws.'();';
				
			}
			$sig_wit_data .= '<td>
									<span id="SpanWitSign'.$ws.'">
										<span >
											<div class="consentObjectBeforSign" id="tdObjectWit'.$ws.'" style="HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;">'.$sig_grid.'</div>
										</span>
										<span>
											<img title="Sig Pad Signature" style="cursor:pointer;" src="../../../library/images/pen.png" id="SignBtnWit'.$ws.'" name="SignBtnWit'.$ws.'" language="VBScript" onclick="'.$sig_onSignClick.'">
										</span>
										<span>
											<img title="Touch Signature" style="display:inline-block;position:relative;cursor:pointer;width:37px ; height:30px;" src="../../../library/images/touch.svg" src="../../../library/images/touch.svg" id="SignBtnWitTouch'.$ws.'" name="SignBtnWitTouch'.$ws.'" onclick="OnSignIpadPhy(\''.$_SESSION['patient'].'\',\'witConsent\',\'consentSigIpadIdWit'.$ws.'\',\''.$ws.'\')">
										</span>
										<span>
											<img style="cursor:pointer;" src="../../../library/images/eraser.gif" id="buttonWit'.$ws.'" alt="Clear Sign" name="ClearBtnWit'.$ws.'" language="VBScript" onclick="'.$sig_onClearClick.'">
										</span>
										<img src="../../../library/images/text_signature.png" class="link_cursor" onclick="ReplacePadWithSign('.$ws.',\'witness\',\'SpanWitSign'.$ws.'\',\'\')" title="Click to Sign">
									</span>
								</td>';
		}
		elseif($blClientBrowserIpad == true){
			$sig_wit_data .= '	<td style="width:320px;height:90px;" class="consentObjectBeforSign" >
									<img style="cursor:pointer; float:right; margin-top:50px;" src="../../../library/images/pen.png" id="SigPenWit'.$ws.'" onclick="OnSignIpadPhy(\''.$_SESSION['patient'].'\',\'witConsent\',\'consentSigIpadIdWit'.$ws.'\',\''.$ws.'\')">
								</td>
								<td><img src="../../../library/images/text_signature.png" class="link_cursor" onclick="ReplacePadWithSign('.$ws.',\'witness\',\'consentSigIpadIdWit'.$ws.'\',\'ipad\')" title="Click to Sign"></td>
									
					';
		}
		$sig_wit_data .= '	</tr>
						  </table>';
		
		$str_wit_data = $sig_wit_arr[$s];
		$sig_wit_arr[$s] = $sig_wit_data;
		$sig_wit_arr[$s] .= $str_wit_data;
		$hiddenWitFields[] = true;
	}
	$consent_form_content = implode(' ',$sig_wit_arr);
/*----witness sign replacement end---*/

	//START
	$sig_phy_arr = explode('{PHYSICIAN SIGNATURE}',$consent_form_content);
	$sig_phy_data = '';
	//if($ds>1) { $ds = $ds+1; }else { $ds=1; }
	$ps=1;
	for($s=1;$s<count($sig_phy_arr);$s++,$ps++){
		$sig_phy_data = '<table class="alignLeft" style="border:none;" id="consentSigIpadIdPhy'.$ps.'">
							<tr>';
		
		if($blClientBrowserIpad == false){
			
			$sig_grid = $sig_onSignClick = $sig_onClearClick = '';	
			if( $enable_sig_web ) {
				$sig_grid = '<canvas id="SigPlusPhy'.$ps.'" name="SigPlusPhy'.$ps.'" width="310" height="87"></canvas>';
				$sig_onSignClick = 'OnSign('.$ps.',\'SigPlusPhy'.$ps.'\',\'SigDataPhy'.$ps.'\')';
				$sig_onClearClick = 'OnClear('.$ps.',\'SigPlusPhy'.$ps.'\',\'SigDataPhy'.$ps.'\')';
			}
			else {
				$sig_grid = '<OBJECT classid=clsid:69A40DA3-4D42-11D0-86B0-0000C025864A height=75
													id=SigPlusPhy'.$ps.' name=SigPlusPhy'.$ps.'
													style="HEIGHT: 87px; WIDTH: 310px; LEFT: 0px; TOP: 0px;" 
													VIEWASTEXT>
													<PARAM NAME="_Version" VALUE="131095">
													<PARAM NAME="_ExtentX" VALUE="4842">
													<PARAM NAME="_ExtentY" VALUE="1323">
													<PARAM NAME="_StockProps" VALUE="0">
											</OBJECT>';
				$sig_onSignClick = 'OnSignPhy'.$ps.'();';
				$sig_onClearClick = 'OnClearPhy'.$ps.'();';
				
			}
			
			$sig_phy_data .= '	<td>
									<span id="SpanPhySign'.$ps.'">
										<span>
											<div class="consentObjectBeforSign" id="tdObjectPhy'.$ps.'" style="HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;">'.$sig_grid.'</div>
										</span>
										<span>
											<img title="Sig Pad Signature" style="cursor:pointer;" src="../../../library/images/pen.png" id="SignBtnPhy'.$ps.'" name="SignBtnPhy'.$ps.'" language="VBScript" onclick="'.$sig_onSignClick.'">
										</span>
										<span>
											<img title="Touch Signature" style="display:inline-block;position:relative;cursor:pointer;width:37px ; height:30px;" src="../../../library/images/touch.svg" id="SignBtnPhyTouch'.$ps.'" name="SignBtnPhyTouch'.$ps.'" onclick="OnSignIpadPhy(\''.$_SESSION['patient'].'\',\'phyConsent\',\'consentSigIpadIdPhy'.$ps.'\',\''.$ps.'\')">
										</span>
										<span>
											<img style="cursor:pointer;" src="../../../library/images/eraser.gif" id="buttonPhy'.$ps.'" alt="Clear Sign" name="ClearBtnPhy'.$ps.'" language="VBScript" onclick="'.$sig_onClearClick.'">
										</span>
										<img src="../../../library/images/text_signature.png" class="link_cursor" onclick="ReplacePadWithSign('.$ps.',\'physician\',\'SpanPhySign'.$ps.'\',\'\')" title="Click to Sign">
									</span>
								</td><input type="hidden" name="hidd_phy_sign_var" id="hidd_phy_sign_var" value="hidd_phy_sign" />';//hidd_phy_sign_var HIDDEN SEND TO WORK VIEW=>PROCEDURES TO CHECK IF DOCUMENT HAVE {PHYSICIAN SIGNATURE} VARIABLE
		}else if($blClientBrowserIpad == true){
			$sig_phy_data .= '	<td style="width:320px;height:90px;" class="consentObjectBeforSign" >
									<img style="cursor:pointer; float:right; margin-top:50px;" src="../../../library/images/pen.png" id="SigPenPhy'.$ps.'" onclick="OnSignIpadPhy(\''.$_SESSION['patient'].'\',\'phyConsent\',\'consentSigIpadIdPhy'.$ps.'\',\''.$ps.'\')">
								</td>
								<td><img src="../../../library/images/text_signature.png" class="link_cursor" onclick="ReplacePadWithSign('.$ps.',\'physician\',\'consentSigIpadIdPhy'.$ps.'\',\'ipad\')" title="Click to Sign"></td>
							';
		}
		$sig_phy_data .= '	</tr>
						 </table>';
		
		$str_phy_data = $sig_phy_arr[$s];
		$sig_phy_arr[$s] = $sig_phy_data;
		$sig_phy_arr[$s] .= $str_phy_data;
		$hiddenPhyFields[] = true;
	}
	$consent_form_content = implode(' ',$sig_phy_arr);
	//END		
	
	$content_row = '';
	for($ro=1;$ro<count($row_arr);$ro++){
		if($row_arr[$ro]){
			$sig_arr1 = explode('{SIGNATURE}',$row_arr[$ro]);
			$td_sign = '';
			for($t=0;$t<count($sig_arr1)-1;$t++,$ds++){
				$sig_arr1[$t] = str_ireplace('&nbsp;','',$sig_arr1[$t]);
				
				$sig_grid = $sig_onSignClick = $sig_onClearClick = '';	
				if( $enable_sig_web ) {
					$sig_grid = '<canvas id="SigPlus'.$ds.'" name="SigPlus'.$ds.'" width="310" height="87"></canvas>';
					$sig_onSignClick = 'OnSign('.$ds.',\'SigPlus'.$ds.'\',\'SigData'.$ds.'\')';
					$sig_onClearClick = 'OnClear('.$ds.',\'SigPlus'.$ds.'\',\'SigData'.$ds.'\')';
				}
				else {
					$sig_grid = '<OBJECT classid=clsid:69A40DA3-4D42-11D0-86B0-0000C025864A height=75
																id="SigPlus'.$ds.'" name="SigPlus'.$ds.'"
																style="HEIGHT: 87px; WIDTH: 310px; LEFT: 0px; TOP: 0px;" 
																VIEWASTEXT>
																<PARAM NAME="_Version" VALUE="131095">
																<PARAM NAME="_ExtentX" VALUE="4842">
																<PARAM NAME="_ExtentY" VALUE="1323">
																<PARAM NAME="_StockProps" VALUE="0">
														</OBJECT>';
					$sig_onSignClick = 'OnSign'.$ds.'();';
					$sig_onClearClick = 'OnClear'.$ds.'();';

				}
				
				$td_sign .= '
							<td align="left">
								<table border="0">
									<tr><td>'.$sig_arr1[$t].'</td></tr>
									<tr>
										<td style="border:solid 1px;" bordercolor="#FF9900">';
										if($blClientBrowserIpad == false){
											$td_sign .= '<div style="HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;">'.$sig_grid.'</div>';
											$td_sign .= '</td>
														<td valign="bottom" id="Sign_icon_'.$ds.'();">
															<img style="cursor:pointer;" src="../../../library/images/pen.png" id="SignBtn'.$ds.'" name="SignBtn'.$ds.'" language="VBScript" onclick="'.$sig_onSignClick.'"><br>
															<img style="cursor:pointer;" src="../../../library/images/eraser.gif" id="button'.$ds.'" name="ClearBtn'.$ds.'" alt="Clear Sign" language="VBScript" onclick="'.$sig_onClearClick.'">											
														</td>';			
										}
										elseif($blClientBrowserIpad == true){
											// the client browser is Ipad Safari
											$canvasId = $hidCanvasSigData = "";
											$canvasId = "canvas".$ds;
											$hidCanvasSigData = "canvas_sig_data".$ds;
											$td_sign .= '<canvas id="'.$canvasId.'" name="'.$canvasId.'" width="320" height="100" style="border: 1px solid #F60;"></canvas>';
											$td_sign .= '<input type="hidden" name="'.$hidCanvasSigData.'" id="'.$hidCanvasSigData.'"/>';
											$td_sign .= '</td>
														<td valign="bottom" id="Sign_icon_'.$ds.'();">
															<img style="cursor:pointer;" src="../../../library/images/eraser.gif" id="button'.$ds.'" name="ClearBtn'.$ds.'" alt="Clear Sign" onclick="clearCanvas'.$ds.'()">											
														</td>';
										}
										$td_sign .= '</tr>
												</table>
											</td>';
				$s++;
				$hiddenFields[] = true;
			}
			$content_row .= '
				<table border="1" align="center">
					<tr>
						'.$td_sign.'						
					</tr>
				</table>
			';
		}
	}
	$jh = 1;
	$consent_form_content .= $content_row;
	//--- get all content of consent forms -------	
	$consent_content_new .= '
						<table id="content_'.$consent_form_id.'" style="display:'.$display.'" width="100%" align="center" cellpadding="1" cellspacing="1" border="0">
							<tr>
								<td align="left" colspan="'.count($sig_arr).'">'.html_entity_decode($consent_form_content).'</td>
							<tr>
						</table>
					';
	$consent_content = $consent_content_new;
	$consent_content = str_ireplace(PRACTICE_PATH."/interface/main/uploaddir", PRACTICE_PATH."/data/".PRACTICE_PATH, $consent_content);
//}
?>

<!DOCTYPE HTML>
<html>
<head>
<title>Consent Forms Detail</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--<link rel="stylesheet" href="<?php //echo $GLOBALS['webroot']; ?>/interface/themes/default/common.css" type="text/css">
<link rel='stylesheet' href="<?php //echo $css_header;?>" type="text/css">
<link rel='stylesheet' href="<?php //echo $css_patient;?>" type="text/css">
<link rel="stylesheet" href="../../themes/tab-view.css" type="text/css">
-->
<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap.css" type="text/css">
<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-select.css" type="text/css">
<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
<link href="<?php echo $library_path; ?>/css/style.css" rel="stylesheet">
<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">


<style>
.consentObjectAfterSign{ border:solid 1px; border-color:#99CC33; }
.consentObjectBeforSign{ border:solid 1px; border-color:#FF9900}
OBJECT{position: absolute; z-index:1;}
#div_smart_tags_options{z-index:2}
.form-control { width:auto; display:inline-block; height:25px; max-width:175px;}
</style>
<!--<script type="text/javascript" src="../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../js/jquery.overlaps.js"></script>
<script type="text/javascript" src="../../../js/common.js"></script>
<script type="text/javascript" src="../../admin/menuIncludes_menu/js/jBoss.js"></script>
<script type="text/javascript" src="../../js/dragresize.js"></script>-->
<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-select.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
<?php if( $enable_sig_web ){ include_once $GLOBALS['srcdir'].'/sigpad/SigWebTablet.php'; } ?>
<script type="text/javascript">	
	var isIPad = false;
	var touch = "";
	/*
	var btn_arr = new Array("consent_save","consent_print","consent_save_print","add_appt");
	for(i=0;i<btn_arr.length;i++){
		parent.parent.document.getElementById(btn_arr[i]).style.display = 'inline';
	}
	*/
	var insert_id = '<?php print $insert_id; ?>';
	
	if(insert_id){
		if(typeof(win_op)!='undefined' && win_op!=''){
			if(win_op==1){
				opener.top.fmain.location.reload();
			}
		}else{
			if(!top.fmain){
				top.document.getElementById('consent_tree_id').src=top.document.getElementById('consent_tree_id').src;	
				top.$('.loading').hide();
			}else{
				//top.fmain.src=top.fmain.src;
				//alert(top.fmain.location);
				var consent_form_id = "<?php echo $_REQUEST["consent_form_id"];?>";
				var content_save = "<?php echo $_REQUEST["content_save"];?>";
				if(top.fmain.document.consent_main_frm) {
					top.fmain.document.consent_main_frm.consent_form_id.value = consent_form_id;
					if(content_save !='print_form') {
						top.fmain.consent_main_frm.submit();
					}
				}
			}
			top.alert_notification_show("Record Saved Successfully");
		}
	}
	
	function ReplacePadWithSign(ds,type,id,browserPlatForm){
		//START CODE TO DEACTIVATE SIGPAD SIGNATURE IF ALREADY CLICKED WITH PEN BEFORE THIS SIGNATURE
		var sig_count = sig_count_phy = sig_count_wit = 0;
		if(document.getElementById("sig_count")) {
			sig_count 		= document.getElementById("sig_count").value;
		}
		if(document.getElementById("sig_count_phy")) {
			sig_count_phy 	= document.getElementById("sig_count_phy").value;
		}
		if(document.getElementById("sig_count_wit")) {
			sig_count_wit 	= document.getElementById("sig_count_wit").value;
		}
		for(var i=1;i<=sig_count;i++) {
			if(document.getElementById("SigPlus"+i)) {
				document.getElementById("SigPlus"+i).TabletState 	= 0;
				document.getElementById("tdObject"+i).className="consentObjectBeforSign";
			}
		}
		for(var j=1;j<=sig_count_phy;j++) {
			if(document.getElementById("SigPlusPhy"+j)) {
				document.getElementById("SigPlusPhy"+j).TabletState = 0;
				document.getElementById("tdObjectPhy"+j).className="consentObjectBeforSign";
			}
		}
		for(var k=1;k<=sig_count_wit;k++) {
			if(document.getElementById("SigPlusWit"+k)) {
				document.getElementById("SigPlusWit"+k).TabletState = 0;
				document.getElementById("tdObjectWit"+k).className="consentObjectBeforSign";
			}
		}
		//END CODE TO DEACTIVATE SIGPAD SIGNATURE IF ALREADY CLICKED WITH PEN BEFORE THIS SIGNATURE
		var OperatorSign = false;
    	<?php
        	if((!empty($elem_sign) && $elem_sign != "0-0-0:;") || (!empty($elem_sign_path) && file_exists(trim($updir.$elem_sign_path))))
        	{
        	    //if($_SESSION['logged_user_type'] == 1){
                    echo 'OperatorSign = true;';
        	    //}
        	}
        	else
        	{
        		echo 'OperatorSign = false;';
        	}
    	?>
		obj = dgi(id);
		if(type=='witness'){
			//obj = dgi('SpanWitSign'+ds);	
			hidVar = 'hidd_signReplacedWit';
			OperatorSign = true;
			//hidAdminVar = 'hidd_admin_signReplacedWit';
		}else if(type=='physician'){
			//obj = dgi('SpanPhySign'+ds);
			hidVar = 'hidd_signReplaced';
			//hidAdminVar = 'hidd_admin_signReplaced';
		}
		var imgData=beginTr=endTr='';
		if(browserPlatForm=="ipad") { 
			beginTr='<tr><td style="width:160px;height:90px;">';
			endTr='</td></tr>';
		}
		imgData +=beginTr;
		imgData +='<?php echo $sigDivData;?><input type="hidden" name="'+hidVar+ds+'" value="<?php echo urlencode($sigDivDataURL);?>">';
		//imgData +='<input type="hidden" name="'+hidAdminVar+ds+'" value="<?php //echo urlencode('interface/'.$sigDivData);?>">';
		imgData +=endTr;
		if(OperatorSign){
			obj.innerHTML =imgData;
		}else{
			top.fAlert("Operator Sign not saved. Kindly save you signature to use this facility.");
		}
	}

	function changeBackImg(id,val)
	{
		ab = document.getElementById(id);
		if(ab.className != 'TDTabSelected' && val == '')
		{
			ab.className = 'TDTabHover';
		}	
		else if(ab.className != 'TDTabSelected' && val != '')
		{
			ab.className = 'TDTab';
		}
	}
	
	function save_form(val,flgnosave,printform){
		document.getElementById("content_save").value = val;
		if( typeof saveSig == 'function') {
			var sign = saveSig();
		}
		else {
			var sign = SetSig();
			var signPhy = SetSigPhy();
			var signWit = SetSigWit();
			setCanvasImage();		
		}
		if(typeof(flgnosave) != "undefined" && flgnosave == 1){
			return 0;
		}
		if(printform){document.getElementById('content_save').value='print_form';}

		if(sign == true && isIPad == false){
			document.consent_frm.submit();
		}
		else if(isIPad == true){
			document.consent_frm.submit();
		}
		
	}
	
	function change_sig_typ(obj,consent_form_id,package_category_id) {
		var objVal = obj.value;
		var url = 'consentFormDetails.php?signature_type_select='+objVal;
		if(consent_form_id) {
			url+="&consent_form_id="+consent_form_id;	
		}
		if(package_category_id) {
			url+="&package_category_id="+package_category_id;	
		}
		if(objVal=='sigpad') {
			if(opener) {
				opener.location.href = url;
				window.close();	
			}
		}else {
			document.location.href=url;	
		}
	}
	
	function SetSig() {
	  if(document.getElementById('SigPlus1').NumberOfTabletPoints==0){
		 top.fAlert("Please sign to continue");
		 return false;
	  }
	  else{
		  document.getElementById('SigPlus1').SigCompressionMode=1;
		  document.consent_frm.SigData.value=SigPlus1.SigString;
		  return true;
	   }
	}
	
	function load_image(){
		var obj = document.getElementsByName("sign_id[]");
		for(i=0;i<obj.length;i++){
			var namesrc = obj[i].name;
		}
	}

	function print_form(){
		var information_id = document.getElementById("information_id").value;
		var show_td = document.getElementById("show_td").value;
		var objId = document.getElementById("SigData1");
		var package_category_id = document.getElementById("package_category_id").value;		
		window.open('../patient_info/consent_forms/print_consent_form.php?consent_form_id='+show_td+'&show_td='+show_td+'&package_category_id='+package_category_id,'print_form','');
	}

	function print_pdf(htmlFilePth,src_type,htmlFileName){
		if(src_type=='ipad'){
			window.location.href='../../../library/'+htmlFilePth+'?font_size=10&page=4';
		}else{
			//alert(htmlFilePth+'@@'+src_type+'$$'+htmlFileName);
			window.open('../../../library/'+htmlFilePth+'?font_size=10&page=4&file_location='+htmlFileName,'print_form','');
		}
	}
	function print_ipad_pdf(htmlFilePth){
		window.location.href='../../../library/'+htmlFilePth+'?font_size=10&page=4';
	}
	// To be called when there's a move event on the body itself:
	function BlockMove(event) {
		// Tell Safari not to move the window.
		event.preventDefault() ;
	}

	function setCanvasImage(){
		//window.location = canvas.toDataURL("image/png"); 
		var totConvas = document.getElementById('sig_count').value;
		for(var counter = 1; counter <= parseInt(totConvas); counter++){
			var hidSigElementName = "canvas_sig_data"+counter;
			if(document.getElementById(hidSigElementName)){
				var canvasElementName = "canvas"+counter;
				if(document.getElementById(canvasElementName)){
					var objCanvasElementName = document.getElementById(canvasElementName);
					var strCanVasData = objCanvasElementName.toDataURL("image/jpeg"); 
					document.getElementById(hidSigElementName).value = strCanVasData;							
					//alert(document.getElementById(hidSigElementName).value);
				}
			}
		}
	}
	
//ipad functions 
function trimNew(val){ 
	return val.replace(/^\s+|\s+$/, ''); 
}

function OnSignIpadPhyAdmin(user_id,pConfId,sigFor,idInnerHTML,signSeqNum){
	window.open("../chartNoteSignatureIPad.php?user_id="+user_id+"&pConfId="+pConfId+"&sigFor="+sigFor+"&idInnerHTML="+idInnerHTML+"&signSeqNum="+signSeqNum,"chartNoteSignature");
}
function OnSignIpadPhy(patient_id,sigFor,idInnerHTML,signSeqNum){
	window.open("../../common/chartNoteSignatureIPad.php?patient_id="+patient_id+"&sigFor="+sigFor+"&idInnerHTML="+idInnerHTML+"&signSeqNum="+signSeqNum,"chartNoteSignature");
}
function image_DIV(imageSrc,div,id,seqNum){
	//START CODE TO DEACTIVATE SIGPAD SIGNATURE IF ALREADY CLICKED WITH PEN BEFORE THIS SIGNATURE
	var sig_count = sig_count_phy = sig_count_wit = 0;
	if(document.getElementById("sig_count")) {
		sig_count 		= document.getElementById("sig_count").value;
	}
	if(document.getElementById("sig_count_phy")) {
		sig_count_phy 	= document.getElementById("sig_count_phy").value;
	}
	if(document.getElementById("sig_count_wit")) {
		sig_count_wit 	= document.getElementById("sig_count_wit").value;
	}
	for(var i=1;i<=sig_count;i++) {
		if(document.getElementById("SigPlus"+i)) {
			document.getElementById("SigPlus"+i).TabletState 	= 0;
			document.getElementById("tdObject"+i).className="consentObjectBeforSign";
		}
	}
	for(var j=1;j<=sig_count_phy;j++) {
		if(document.getElementById("SigPlusPhy"+j)) {
			document.getElementById("SigPlusPhy"+j).TabletState = 0;
			document.getElementById("tdObjectPhy"+j).className="consentObjectBeforSign";
		}
	}
	for(var k=1;k<=sig_count_wit;k++) {
		if(document.getElementById("SigPlusWit"+k)) {
			document.getElementById("SigPlusWit"+k).TabletState = 0;
			document.getElementById("tdObjectWit"+k).className="consentObjectBeforSign";
		}
	}
	//END CODE TO DEACTIVATE SIGPAD SIGNATURE IF ALREADY CLICKED WITH PEN BEFORE THIS SIGNATURE
	if(imageSrc){
		if(trimNew(imageSrc) != "") {			
			if(div == "ptConsent"){
				if(typeof(document.getElementById('hiddSigIpadId'+seqNum)) != "undefined") {
					document.getElementById('hiddSigIpadId'+seqNum).value=imageSrc;
				}
				if(typeof(document.getElementById(id)) != "undefined") {
					$("#"+id).html("<tr><td style='width:160px;height:90px;'><img src='"+imageSrc+"' style='width:150px; height:83px;'></td></tr>");					
				}
			}else if(div == "witConsent"){
				if(typeof(document.getElementById('hiddSigIpadIdWit'+seqNum)) != "undefined") {
					document.getElementById('hiddSigIpadIdWit'+seqNum).value=imageSrc;
				}
				if(typeof(document.getElementById(id)) != "undefined") {
					$("#"+id).html("<tr><td style='width:160px;height:90px;'><img src='"+imageSrc+"' style='width:150px; height:83px;'></td></tr>");		
				}
			}else if(div == "phyConsent"){
				if(typeof(document.getElementById('hiddSigIpadIdPhy'+seqNum)) != "undefined") {
					document.getElementById('hiddSigIpadIdPhy'+seqNum).value=imageSrc;
				}
				if(typeof(document.getElementById(id)) != "undefined") {
					$("#"+id).html("<tr><td style='width:160px;height:90px;'><img src='"+imageSrc+"' style='width:150px; height:83px;'><input type='hidden' name='hidd_phy_sign_var' id='hidd_phy_sign_var' value='hidd_phy_sign' /></td></tr>");		
				}
			}
			/*
			var con=confirm("Do you want to save the Document");
			if(con==true){
				save_form('save_form','','print_form');
			}else{return false;}*/
		}
	}
}
//ipad functions
	
</script>
</head>
<?php

if($content_save == 'print_form' && $patientSign == true){
	$id = $consentDetail[0]['form_information_id'];
	
	//http host & protocol used for replace sign path
	$http_host=$_SERVER['HTTP_HOST'];
	if($protocol==''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }
	
	$qry = "select max(form_information_id) from patient_consent_form_information";
	$maxFormInfoIDData = get_array_records_query($qry);
	$maxFormInfoID = $maxFormInfoIDData[0]['max(form_information_id)'];
	$id = $maxFormInfoID;
	$qry = "select pcfi.consent_form_content_data,pcfi.package_category_id as consent_package_category_id,pcfi.consent_form_id, cf.cat_id as consent_cat_id from patient_consent_form_information pcfi
			LEFT JOIN consent_form cf ON (cf.consent_form_id = pcfi.consent_form_id)
			WHERE pcfi.patient_id = '$patient_id' and pcfi.form_information_id  = '$id'";
	$result = get_array_records_query($qry);
	
	$consent_cat_id 	  			= $result[0]['consent_cat_id'];
	$consent_package_category_id	= $result[0]['consent_package_category_id'];
	$consent_form_content = str_ireplace('&nbsp;',' ',$result[0]['consent_form_content_data']);
	$consent_form_content = stripslashes(html_entity_decode(html_entity_decode($consent_form_content)));
	$consent_form_content = str_ireplace($protocol.$http_host.$web_root.'/interface/common/html_to_pdf/','',$consent_form_content);
	$consent_form_content = str_ireplace($web_root.'/interface/common/'.$htmlFolder.'/','../../data/'.PRACTICE_PATH.'/',$consent_form_content);
	$consent_form_content = str_ireplace($web_root.'/interface/common/html2pdf/','../../data/'.PRACTICE_PATH.'/',$consent_form_content);
	$consent_form_content = str_ireplace($web_root.'/interface/common/html_to_pdf/','../../data/'.PRACTICE_PATH.'/',$consent_form_content);
	$consent_form_content = str_ireplace('interface/common/html2pdf/','../../data/'.PRACTICE_PATH.'/',$consent_form_content);
	$consent_form_content = str_ireplace('interface/common/html_to_pdf/','../../data/'.PRACTICE_PATH.'/',$consent_form_content);
	$consent_form_content = str_ireplace($protocol.$http_host.$web_root.'/interface/main/uploaddir/',$web_root.'/interface/main/uploaddir/',$consent_form_content);
	$consent_form_content = str_ireplace($protocol.$myExternalIP.$web_root.'/interface/main/uploaddir/',$web_root.'/interface/main/uploaddir/',$consent_form_content);
	$consent_form_content = str_ireplace($web_root.'/interface/main/uploaddir/','../../main/uploaddir/',$consent_form_content);
	//$consent_form_content = str_ireplace($protocol.$http_host.$web_root.'/redactor/images/',$web_root."/redactor/images/",$consent_form_content);
	//$consent_form_content = str_ireplace($web_root."/redactor/images/","../../../redactor/images/",$consent_form_content);
	$consent_form_content = str_ireplace('%20',' ',$consent_form_content);
	$consent_form_content = str_ireplace('</div>','<br>',$consent_form_content);
	$consent_form_content = str_ireplace('../SigPlus_images/','',$consent_form_content);	
	$consent_form_content = str_ireplace($web_root."/interface/SigPlus_images/","",$consent_form_content);
	$consent_form_content = str_ireplace("{PHYSICIAN SIGNATURE}","",$consent_form_content);	
	$consent_form_content = str_ireplace("{WITNESS SIGNATURE}","",$consent_form_content);	
	//$consent_form_content = str_ireplace($web_root.'/data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/','../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/',$consent_form_content);
	//$consent_form_content = str_ireplace($web_root.'/data/'.PRACTICE_PATH.'/','../../data/'.PRACTICE_PATH.'/',$consent_form_content);
	$consent_form_content = str_ireplace('../../../library/images/','../../library/images/',$consent_form_content);
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
	$resultConsentFormId = $result[0]['consent_form_id'];
	$qrynew = "select signature_image_path,signature_count from consent_form_signature 
				where form_information_id = '$id' and patient_id = '$patient_id'
				and consent_form_id = '$resultConsentFormId' and signature_status = 'Active' order by signature_count";
		 $sigDetail = get_array_records_query($qrynew);
		 if ($sigDetail){
			$sig_con = array();
			for($s=0;$s<count($sigDetail);$s++){
				$sig_con[$s] = $sigDetail[$s]['signature_image_path'];
				$signature_count[$s] = $sigDetail[$s]['signature_count'];
			}
			
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
									$sig_data = '<br /><table>
												<tr>
													<td>
														<img src="'.$path1[1].'" height="80" width="240"><br />
													</td>
												</tr></table>';
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
							<table border="1" align="center">
								<tr>
									'.$td_sign.'						
								</tr>
							</table>
						';
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
	
	
	$inputValTextarea=explode('<textarea rows="2" cols="100" name="large',$consent_form_content);
	if(is_array($inputValTextarea)){
		for($i=1;$i<count($inputValTextarea);$i++){
			$consent_form_content = str_ireplace('<textarea rows="2" cols="100" name="large">','',$consent_form_content);
			$consent_form_content = str_ireplace('<textarea rows="2" cols="100" name="large'.$i.'">','',$consent_form_content);
			$consent_form_content = str_ireplace('</textarea>','',$consent_form_content);
	 	}
	}
	
	////////		
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
					<td style="text-align:right;width:700px;">'.	$barCodeContent.'</td></tr><tr>
					<td style="text-align:right;width:700px;">'.$img_name.'</td></tr></table>';
	$backtop="10mm";
	$page_bar_code_v1='<p align="right" style="margin-top:-15px;">'.$barCodeContent.'</p><p align="right">'.$img_name.'</p>';
	if(constant("BAR_CODE_DISABLE")=='YES' || $web_RootDirectoryName == "nse/imwemr"){
		$page_bar_code='';$backtop="0mm";$page_bar_code_v1='';
	}
	if($htmlV2Class==true){
	$consent_form_content='
		<page  backtop="'.$backtop.'" backbottom="5mm">
		<page_header>
		'.$page_bar_code.'
		</page_header>
		<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
			<tr><td style="width:100%;font-size:15px;" class="text_value">'.$consent_form_content.'</td></tr>
		</table>
		</page>';
	}else{
		$consent_form_content = '
		<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
			'.$page_bar_code_v1.'
			<tr><td style="width:100%;font-size:15px;" class="text_value">'.$consent_form_content.'</td></tr>
		</table>
	';
	
	}
	//end creating barcode image	
	$fld_path='';$html_file_name='pdffile.html';
	$fld_path='../../../library/'.$htmlFolder.'/';
	//$fld_path='../../common/'.$htmlFolder.'/';
	//if(constant("CONSENT_FORM_VERSION")=="consent_v2") {
		if(!file_exists($fld_path.'consent_form')){
			mkdir($fld_path.'consent_form');
		}
		$html_file_name='consent_form/pdffile_'.$_SESSION['authId'].'.html';
	//}
	$path_set=$fld_path.$html_file_name;
	$fp = fopen($path_set,'w');
	if($htmlV2Class==false) {
		$consent_form_content = strip_tags($consent_form_content,'<strong> </strong><img> <p> <br>');
	}
	$consent_form_content = str_ireplace($web_root.'/data/'.PRACTICE_PATH.'/','../../data/'.PRACTICE_PATH.'/',$consent_form_content);	
	$consent_form_content = stripslashes($consent_form_content);
	//$write_data = fwrite($fp,html_entity_decode(html_entity_decode($consent_form_content)));
	$consent_form_content = mb_convert_encoding($consent_form_content, "HTML-ENTITIES", 'UTF-8');
	$consent_form_content = str_ireplace('�',' ',$consent_form_content);
	$consent_form_content = str_ireplace('&nbsp;',' ',$consent_form_content);
	$consent_form_content = str_ireplace('<div', '<span', $consent_form_content);
	$consent_form_content = str_ireplace('</div>', '</span>', $consent_form_content);
	$consent_form_content = str_ireplace('<meta charset="utf-8" />', '', $consent_form_content);
	$file_path = write_html(html_entity_decode(html_entity_decode($consent_form_content)));
	if($file_path){
		$scrtype="";
		if($blClientBrowserIpad==true){
			$scrtype='ipad';	
		}
		?>
		<script type="text/javascript">
			var htmlFilePth = "<?php echo $htmlFilePth;?>";
			var scrtype = '<?php echo $scrtype; ?>';
			//print_pdf(htmlFilePth,'<?php echo $scrtype; ?>','<?php echo $html_file_name; ?>');
			if(scrtype=='ipad'){
				html_to_pdf('<?php echo $file_path; ?>','p','',true);
			}else{
				html_to_pdf('<?php echo $file_path; ?>','p');
			}
			if(top.fmain.consent_main_frm) {
				top.fmain.consent_main_frm.submit();
			}
			</script>
		<?php
	}
}
?>
<body onLoad="load_image();<?php print $loadFun; ?> init();" oncontextmenu="return false;"><!--ontouchmove="BlockMove(event);"-->
<form name="consent_frm" action="" method="post">
<input type="hidden" name="show_td" id="show_td" value="<?php print xss_rem($consent_form_id); ?>" >
<input type="hidden" name="consentFormName" id="consentFormName" value="<?php print $consent_form_name; ?>" >
<input type="hidden" name="content_save" id="content_save" value="" >
<input type="hidden" name="information_id" id="information_id" value="<?php if(!empty($elem_form_information_id) && !empty($_GET["chart_procedures_id"])){	echo $elem_form_information_id;	}else{	print $consentDetail[0]['form_information_id'];	} ?>" >
<input type="hidden" name="sig_count" id="sig_count" value="<?php print count($hiddenFields); ?>" >
<input type="hidden" name="sig_count_phy" id="sig_count_phy" value="<?php print count($hiddenPhyFields); ?>" >
<input type="hidden" name="sig_count_wit" id="sig_count_wit" value="<?php print count($hiddenWitFields); ?>" >
<input type="hidden" name="package_category_id" id="package_category_id" value="<?php echo xss_rem($package_category_id); ?>" >
<!-- Below site hidden field used for sending the procedure site value for posting to save page (consentformdetails_save file). -->
<input type="hidden" name="procedure_site" id="procedure_site" value="<?php echo $proc_site; ?>" >


<input type="hidden" name="pop_procedure" id="pop_procedure" value="<?php print $_GET["pop_procedure"]; ?>" >


<input type="hidden" name="smartTag_parentId" id="smartTag_parentId" value="">
<!--<input type="button" value="get source" onClick="get_source()"/>-->

<?php 
for($h=0;$h<count($hiddenFields);$h++){?>
    <input type="hidden" name="hiddSigIpadId<?php echo($h+1); ?>" id="hiddSigIpadId<?php echo($h+1); ?>" value="">
<?php
}
for($h=0;$h<count($hiddenPhyFields);$h++){?>
    <input type="hidden" name="hiddSigIpadIdPhy<?php echo($h+1); ?>" id="hiddSigIpadIdPhy<?php echo($h+1); ?>" value="">
<?php
}
for($h=0;$h<count($hiddenWitFields);$h++){?>
    <input type="hidden" name="hiddSigIpadIdWit<?php echo($h+1); ?>" id="hiddSigIpadIdWit<?php echo($h+1); ?>" value="">
<?php
}


	if(!$show_td) $show_td = $consent_form_id;
	$qry = "select form_information_id from patient_consent_form_information 
			where consent_form_id = '$consent_form_id' and patient_id = '$patient_id'";
	$consentFormInfoID = get_array_records_query($qry);
	$formInfoID=$consentFormInfoID[0]['form_information_id'];
	
	$qry = "select signature_content from consent_form_signature 
			where consent_form_id = '$show_td' and patient_id = '$patient_id' and form_information_id = $formInfoID";
	$sigContentDetail = get_array_records_query($qry);
	$sig_con = array();
	for($s=0;$s<count($sigContentDetail);$s++){
		$sig_con[] = $sigContentDetail[$s]['signature_content'];
	}
	for($h=0;$h<count($hiddenFields);$h++,$jh++){
		$hiddenField .= '<input type="hidden" name="SigData'.$jh.'" id="SigData'.$jh.'" value="">';
	}
	for($h=0,$jh=1;$h<count($hiddenPhyFields);$h++,$jh++){
		$hiddenPhyField .= '<input type="hidden" name="SigDataPhy'.$jh.'" id="SigDataPhy'.$jh.'" value="">';
	}
	for($h=0,$jh=1;$h<count($hiddenWitFields);$h++,$jh++){
		$hiddenWitField .= '<input type="hidden" name="SigDataWit'.$jh.'" id="SigDataWit'.$jh.'" value="">';
	}
	
	print $hiddenField;
	print $hiddenPhyField;
	print $hiddenWitField;
	//---- check patient all form signed or not -----
	//---- get consent forms -------
	$qry = "select consent_form_id,consent_form_name from consent_form where consent_form_status = 'Active'
			order by consent_form_name,consent_form_version desc";
	$consentDetail = get_array_records_query($qry);
	$consentTabs = '';
	$sign = false;
	for($i=0;$i<count($consentDetail);$i++){
		if($consent_form_name != $consentDetail[$i]['consent_form_name']){
			$consent_form_name = $consentDetail[$i]['consent_form_name'];
			$consentDetails[] = $consentDetail[$i];
		}
	}
	for($i=0;$i<count($consentDetails);$i++){
		$form_name = trim(ucwords($consentDetails[$i]['consent_form_name']));
		//--- get signature data ------
		$qry = "select form_information_id from patient_consent_form_information 
				where consent_form_id = '$consent_form_id' and patient_id = '$patient_id'";
		$sig_details = get_array_records_query($qry);
		$form_information_id = $sig_details[0]['form_information_id'];
		if(!$form_information_id) $sign = true;
	}
?>

<?php
if($blClientBrowserIpad==false){
?>
<div id="consent_content_div" class="bg-white pd10" style=" width:100%; height:100%; overflow:scroll; overflow-x:hidden;">
<?php } ?>
<table style="width:100%; border:none; vertical-align:top; padding:0px; border-collapse:collapse">
	
    <tr>
		<td style="vertical-align:top">
			<?php 
                                print html_entity_decode($consent_content);
				//print str_ireplace("<td>?</td>","<td></td>",utf8_decode($consent_content));
			?>
		</td>
	</tr>
</table>
<?php if($blClientBrowserIpad==false){?>
</div>
<?php } ?>
<div id="hold_to_phy_div" class="modal" role="dialog" >
	<div class="modal-dialog modal-sm">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal" >x</button>
        <h4 class="modal-title" id="modal_title">On Hold</h4>
     	</div>
      
        <div class="modal-body">
            <div class="row">
            	<div class="col-xs-12">
            		<label>Select Physician for Hold</label>
            		<br>
            		<select name="hold_to_physician" id="hold_to_physician" class="selectpicker" style="color:#333" data-style="btn-warning">
            			<option value="">--SELECT--</option>
           				<?php echo $OBJCommonFunction->dropDown_providers('','');?>
            		</select>
            		<input type="hidden" name="hidd_hold_to_physician" id="hidd_hold_to_physician">
                </div>
            </div>                                    
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info" data-dismiss="modal" onClick="hold_for_dr_sig()">Hold & Save</button> &nbsp;&nbsp;&nbsp;&nbsp;
        <button type="button" class="btn btn-danger" data-dismiss="modal" >Close</button>
      </div>
      
    </div>
  </div>
</div>

</form>
<script type="text/javascript">		
		var getArea = document.getElementsByTagName('textarea');
		var areaTag = new Array();
		var getTags = document.getElementsByTagName('input');
		var arrTag = new Array();
		var a = 0;
		for(var i=0;i<getTags.length;i++)
		{
			if(document.getElementsByTagName('input')[i].type != 'hidden')
			{
				arrTag[a] = document.getElementsByTagName('input')[i].name;
				a++;
			}
		}
		
		for(var l=0;l<getArea.length;l++)
		{
			areaTag[l] = getArea[l].name;
		}
		
		var remArea = removeDuplicateElement(areaTag);
		for(var p=0;p<remArea.length;p++)
		{
			var areaName = document.getElementsByName(remArea[p]);
			var d=1;
			for(var b=0;b<areaName.length;b++)
			{
				if(areaName.length > 1)
				{
					areaName[b].name = areaName[b].name + d;
					d++;
				}
			} 
		}
		var ad = removeDuplicateElement(arrTag);
		
		for(var j=0;j<ad.length;j++)
		{
			if(ad[j]){
				var tagName = document.getElementsByName(ad[j]);
				var s = 1;
				for(var l=0;l<tagName.length;l++)
				{
					
					if(tagName.length > 1)
					{
						tagName[l].name = tagName[l].name + s;
						s++
					}
					
				}
			}
		} 
	
function removeDuplicateElement(arrayName)
      {
        var newArray=new Array();
        label:for(var b=0; b<arrayName.length;b++)
        {  
          for(var c=0; c<newArray.length;c++ )
          {
            if(newArray[c]==arrayName[b]) 
            continue label;
          }
          newArray[newArray.length] = arrayName[b];
        }
        return newArray;
      }

<?php
$commonCount = count($hiddenFields);
if(count($hiddenPhyFields)>$commonCount) {$commonCount = count($hiddenPhyFields);}
if(count($hiddenWitFields)>$commonCount) {$commonCount = count($hiddenWitFields);}


$jh = 1;
$sigFunData = $intFunData = '';
for($h=0;$h<count($hiddenFields);$h++,$jh++){
	$str = '';
	if($blClientBrowserIpad == false){
		for($s=0;$s<$commonCount;$s++){
			if($s+1 != $jh){			
				$str .= 'if(document.getElementById("SigPlus'.($s+1).'")){
							document.getElementById("SigPlus'.($s+1).'").TabletState = 0;
						 }
						 if(document.getElementById("SigPlusPhy'.($s+1).'")){
							document.getElementById("SigPlusPhy'.($s+1).'").TabletState = 0;
						 }
						 if(document.getElementById("SigPlusWit'.($s+1).'")){
							document.getElementById("SigPlusWit'.($s+1).'").TabletState = 0;
						 }
						 ';
			}
		}
		print '
				function OnSign'.$jh.'(){//alert('.$jh.'+document.getElementById("SigPlus'.$jh.'"));
					if(document.getElementById("SigPlus'.$jh.'")){
						'.$str.'
						if(document.getElementById("SigPlusPhy'.$jh.'")) {
							document.getElementById("SigPlusPhy'.$jh.'").TabletState = 0;
							document.getElementById("tdObjectPhy'.$jh.'").className="consentObjectBeforSign";	
						}
						if(document.getElementById("SigPlusWit'.$jh.'")) {
							document.getElementById("SigPlusWit'.$jh.'").TabletState = 0;
							document.getElementById("tdObjectWit'.$jh.'").className="consentObjectBeforSign";	
						}
						document.getElementById("SigPlus'.$jh.'").TabletState = 1;
						document.getElementById("tdObject'.$jh.'").className="consentObjectAfterSign";
						
					}
				}
			';
		print '
				function OnClear'.$jh.'(){
						if(document.getElementById("SigPlus'.$jh.'")){
				   document.getElementById("SigPlus'.$jh.'").ClearTablet();}
				}
			';
		$saveStr .= 'if(document.getElementById("SigPlus'.$jh.'")){
					document.getElementById("SigPlus'.$jh.'").SigCompressionMode=1;
					document.consent_frm.SigData'.$jh.'.value=document.getElementById("SigPlus'.$jh.'").SigString;}
				';
	}
	elseif($blClientBrowserIpad == true){
		echo '
				var canvas'.$jh.';
				var pen'.$jh.';	
				var lastPenPoint'.$jh.';
							
				function clearCanvas'.$jh.'(){
					if(document.getElementById("canvas'.$jh.'")){
				   		canvas'.$jh.'.width = canvas'.$jh.'.width; // Clears the canvas
						canvas'.$jh.'.height = canvas'.$jh.'.height; // Clears the canvas
						pen'.$jh.'.clearRect(0,0,canvas'.$jh.'.width,canvas'.$jh.'.height) ;	
					}
				}
			';
		$sigFunData .=	'if(document.getElementById("canvas'.$jh.'")){
							//alert(document.getElementById("canvas'.$jh.'"));
							canvas'.$jh.' = document.getElementById("canvas'.$jh.'");
							//alert(canvas'.$jh.');
							pen'.$jh.' = canvas'.$jh.'.getContext(\'2d\');										
							lastPenPoint'.$jh.' = null;				 																			
						}';
		echo '
				function getCanvasLocalCoordinates'.$jh.'(pageX, pageY ) {				
					return({
						x: (pageX - canvas'.$jh.'.offsetLeft),
						y: (pageY - canvas'.$jh.'.offsetTop)
					});
				}';	
				
		echo '
				function onTouchStart'.$jh.'() {								
					var touch = getTouchEvent( event );     
					//alert(touch);				
					var localPosition = getCanvasLocalCoordinates'.$jh.'(touch.pageX,touch.pageY);					
					lastPenPoint'.$jh.' = {x: localPosition.x,y: localPosition.y};
					pen'.$jh.'.beginPath();
					pen'.$jh.'.moveTo( lastPenPoint'.$jh.'.x, lastPenPoint'.$jh.'.y );				
				}
			';	
		
		echo '
				function onTouchMove'.$jh.'() {				
				       touch = getTouchEvent( event );     
					var localPosition = getCanvasLocalCoordinates'.$jh.'(
						touch.pageX,
						touch.pageY
					);
					lastPenPoint'.$jh.' = {
						x: localPosition.x,
						y: localPosition.y
					};
					pen'.$jh.'.lineTo( lastPenPoint'.$jh.'.x, lastPenPoint'.$jh.'.y );
					pen'.$jh.'.stroke();		
				}
				
				';	
		
		$funNameStart = $funNameMove = '';		
		$funNameStart = 'onTouchStart'.$jh;
		$funNameMove = 'onTouchMove'.$jh;		
		$intFunData .=	'if(document.getElementById("canvas'.$jh.'")){
							canvas'.$jh.'.addEventListener(\'touchstart\','.$funNameStart.', false ); 
							canvas'.$jh.'.addEventListener(\'touchmove\','.$funNameMove.', false );				
						}';						
	}
}

//START
$jh = 1;
for($h=0;$h<count($hiddenPhyFields);$h++,$jh++){
	$strPhy = '';
	if($blClientBrowserIpad == false){
		for($s=0;$s<$commonCount;$s++){
			if($s+1 != $jh){			
				$strPhy .= 'if(document.getElementById("SigPlusPhy'.($s+1).'")){
								document.getElementById("SigPlusPhy'.($s+1).'").TabletState = 0;
							}
							if(document.getElementById("SigPlus'.($s+1).'")){
								document.getElementById("SigPlus'.($s+1).'").TabletState = 0;
							}
							if(document.getElementById("SigPlusWit'.($s+1).'")){
								document.getElementById("SigPlusWit'.($s+1).'").TabletState = 0;
							}
							';
			}
		}
		
		print '
				function OnSignPhy'.$jh.'(){
					if(document.getElementById("SigPlusPhy'.$jh.'")){
						'.$strPhy.'
						if(document.getElementById("SigPlus'.$jh.'")) {
							document.getElementById("SigPlus'.$jh.'").TabletState = 0;
							document.getElementById("tdObject'.$jh.'").className="consentObjectBeforSign";	
						}
						if(document.getElementById("SigPlusWit'.$jh.'")) {
							document.getElementById("SigPlusWit'.$jh.'").TabletState = 0;
							document.getElementById("tdObjectWit'.$jh.'").className="consentObjectBeforSign";	
						}
						document.getElementById("SigPlusPhy'.$jh.'").TabletState = 1;
						document.getElementById("tdObjectPhy'.$jh.'").className="consentObjectAfterSign";
					}
					
				}
			';
		print '
				function OnClearPhy'.$jh.'(){
						if(document.getElementById("SigPlusPhy'.$jh.'")){
				   document.getElementById("SigPlusPhy'.$jh.'").ClearTablet();}
				}
			';
		$saveStrPhy .= 'if(document.getElementById("SigPlusPhy'.$jh.'")){
					document.getElementById("SigPlusPhy'.$jh.'").SigCompressionMode=1;
					document.consent_frm.SigDataPhy'.$jh.'.value=document.getElementById("SigPlusPhy'.$jh.'").SigString;}
				';
	}
}
//END

//START WITNESS
$jh = 1;
for($h=0;$h<count($hiddenWitFields);$h++,$jh++){
	$strWit = '';
	if($blClientBrowserIpad == false){
		for($s=0;$s<$commonCount;$s++){
			if($s+1 != $jh){			
				$strWit .= 'if(document.getElementById("SigPlusPhy'.($s+1).'")){
								document.getElementById("SigPlusPhy'.($s+1).'").TabletState = 0;
							}
							if(document.getElementById("SigPlus'.($s+1).'")){
								document.getElementById("SigPlus'.($s+1).'").TabletState = 0;
							}
							if(document.getElementById("SigPlusWit'.($s+1).'")){
								document.getElementById("SigPlusWit'.($s+1).'").TabletState = 0;
							}
							';
			}
		}
		print '
				function OnSignWit'.$jh.'(){
					if(document.getElementById("SigPlusWit'.$jh.'")){
						'.$strWit.'
						if(document.getElementById("SigPlus'.$jh.'")) {
							document.getElementById("SigPlus'.$jh.'").TabletState = 0;
							document.getElementById("tdObject'.($jh).'").className="consentObjectBeforSign";	
						}
						if(document.getElementById("SigPlusPhy'.$jh.'")) {
							document.getElementById("SigPlusPhy'.$jh.'").TabletState = 0;
							document.getElementById("tdObjectPhy'.($jh).'").className="consentObjectBeforSign";	
						}
						document.getElementById("SigPlusWit'.$jh.'").TabletState = 1;
						document.getElementById("tdObjectWit'.$jh.'").className="consentObjectAfterSign";
					}
				}
			';
		print '
				function OnClearWit'.$jh.'(){
					if(document.getElementById("SigPlusWit'.$jh.'")){
				   		document.getElementById("SigPlusWit'.$jh.'").ClearTablet();
					}
				}
			';
		$saveStrWit .= 'if(document.getElementById("SigPlusWit'.$jh.'")){
					document.getElementById("SigPlusWit'.$jh.'").SigCompressionMode=1;
					document.consent_frm.SigDataWit'.$jh.'.value=document.getElementById("SigPlusWit'.$jh.'").SigString;}
				';
	}
}
//END WITNESS

for($dis=0;$dis<count($sig_con);$dis++){
	if($patientSign == false){
		print '
				if(document.getElementById("Sign_icon_'.($dis+1).'"))
				document.getElementById("Sign_icon_'.($dis+1).'").style.display="none";';
	}
}
?>
function SetSig(){
	<?php print $saveStr; ?>
	return true;
}
function SetSigPhy(){
	<?php print $saveStrPhy; ?>
	return true;
}
function SetSigWit(){
	<?php print $saveStrWit; ?>
	return true;
}
function LoadSig(){
	<?php print $displaySign; ?>
}
	var sign = '<?php print $sign; ?>';
	if(!sign){
		if(top.fmain && top.fmain.document.getElementById("sign_id"))
			top.fmain.document.getElementById("sign_id").style.display = 'none';		
	}
function sig() {			
	isIPad = (new RegExp( "iPad", "i" )).test(navigator.userAgent);
	//alert(isIPad)
	if(isIPad){
		<?php 
			echo $sigFunData;
		?>				 	
	}
}	
function init(){
	sig();
	<?php 
		echo $intFunData;
	?>		

}
function getTouchEvent() {
	return(isIPad ? window.event.targetTouches[ 0 ] : event);
}
			
function get_source(){
	javascript:(function(){var a=window.open("about:blank").document;a.write("<!DOCTYPE html><html><head><title>Source of "+location.href+'</title><meta name="viewport" content="width=device-width" /></head><body></body></html>');a.close();var b=a.body.appendChild(a.createElement("pre"));b.style.overflow="auto";b.style.whiteSpace="pre-wrap";b.appendChild(a.createTextNode(document.documentElement.innerHTML))})();
}

//Btn ---
function hold_dr_sig(){
	scrol = $(window).scrollTop();
	//$('#hold_to_phy_div').css('top',scrol+310);
	$('#hold_to_phy_div').modal("show");
	//$(window).scroll(function(){$('#hold_to_phy_div').css('top',$(window).scrollTop()+310);});
	$('#hold_to_phy_div .hold').click(function(){
		if($('#hold_to_physician').val()==''){
			top.fAlert('Please select a physician');
		}else{
			$('#hidd_hold_to_physician').val($('#hold_to_physician').val());
			save_form('save_form');
			$('#hold_to_phy_div').modal("hide");
		}
	});
}
function hold_for_dr_sig(){
	if($('#hold_to_physician').val()==''){
			top.fAlert('Please select a physician');
	}else{
		$('#hidd_hold_to_physician').val($('#hold_to_physician').val());
		save_form('save_form');
		$('#hold_to_phy_div').hide();
	}
}
if(typeof(top.btn_show)!="undefined"){
<?php if(constant('DEFAULT_PRODUCT')=='imwemr'){?>
	top.btn_show("CF2");
<?php }else{?>
	top.btn_show("CF2");
<?php }?>
}

//Btn ---

</script>
<div id="div_smart_tags_options" class="modal" role="dialog">
	<div class="modal-dialog modal-sm">
  	<!-- Modal content-->
    <div class="modal-content">
    
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" onclick="$('#div_smart_tags_options').hide();">X</button>
        <h4 class="modal-title" id="modal_title">Smart Tag Options</h4>
     	</div>
      
      <div class="modal-body pd0" style="min-height:250px; max-height:325px; overflow:hidden; overflow-y:auto;">
      	<div class="loader"></div>
      </div>
      	
      <div class="modal-footer pd5"></div>
      
  	</div>
  </div>
</div>

<script type="text/javascript">
var smart_tag_current_object = new Object;
$(document).ready(function(){
	if(typeof(win_op)=='undefined'){var win_op='';}
	if(win_op==''){
		$('.cls_smart_tags_link').mouseup(function(e){
			if(e.button==0 || e.button==2){ //click event.
				$('#smartTag_parentId').val($(this).attr('id'));
				smart_tag_current_object = $(this);
				display_tag_options(e);
			}			
		});
	}else if(win_op==1){
		$('.cls_smart_tags_link').click(function(e){
				$('#smartTag_parentId').val($(this).attr('id'));
				smart_tag_current_object = $(this);
				display_tag_options(e);
			}
		);
	}
	$("td, p").each(function(){
	    if($(this).html() == "?"){
	    	$(this).html("");
	    }
	    $(this).html().replace('\&nbsp;', '\ ');
	    $(this).html().replace('.?', '.');
	    $(this).html().replace(':?', ':');
	    $(this).html().replace('?Ph:', ' Ph:');
	});
});


function display_tag_options(e){
	//css({'left':e.pageX,'top':e.pageY})
	$('#div_smart_tags_options').show();
	
	var parentId = $('#smartTag_parentId').val();
	/*
	ArrtempParentId = parentId.split('_');
	parentId = ArrtempParentId[0];
	*/
	var x = (top.imgPath) ? top.imgPath : top.JS_WEB_ROOT_PATH;
	if(x == '' || typeof(x) == 'undefined') {
		x = "<?php echo $GLOBALS['php_server']; ?>";
	}
	$.ajax({
		type: "GET",
		url: x + "/interface/chart_notes/requestHandler.php?elem_formAction=getTagOptions&id="+parentId+'&is_return=1',
		dataType:"json",
		success: function(resp){
			$('#div_smart_tags_options .modal-title').html(resp.title);
			$('#div_smart_tags_options .modal-body').html(resp.data);
			$('#div_smart_tags_options .modal-footer').html(resp.footer_btn);
			$("object").hide();
		}
	});
	
	$('.close').on('click', function (e) {
		 $("object").show();
    });
	
}

function replace_tag_with_options(){
	var strToReplace = '';
	var parentId = $('#smartTag_parentId').val();
	
	var arrSubTags = document.all.chkSmartTagOptions;
	$(arrSubTags).each(function (){
		if( $(this).is(':checked') ){
			if(strToReplace=='')
				strToReplace +=  $(this).val();
			else
				strToReplace +=  ', '+$(this).val();
		}
	});
	//alert(strToReplace);
	
	/*--GETTING FCK EDITOR TEXT--*/
	if(strToReplace!='' && smart_tag_current_object){
		$('.cls_smart_tags_link[id="'+parentId+'"]').html(strToReplace);
		//$(smart_tag_current_object).html(strToReplace);
		var hiddclass = $(smart_tag_current_object).attr('id');
		$('.'+hiddclass).val($(smart_tag_current_object).text());
/*		
		RemoveString = window.location.protocol+'//'+window.location.host; //.innerHTML BUG adds host url to relative urls.
		var strippedData = $('#hold_temp_smarttag_data').html();
		strippedData = strippedData.replace(new RegExp(RemoveString, 'g'),'');
*/		
		$('#div_smart_tags_options').hide();
		
		//Enabling Object Signature
		$("object").show();
		
		
		$("object").css({"visibility":"visible"});
	}else{
		alert('Select Options');
	}
}

function setConsentDivHeight() {
	var consent_div			=	$('#consent_content_div');
	var consent_div_height	=	parent.$('#consent_data_id').outerHeight(true);
	var height_custom 		=	consent_div_height-15;
	consent_div.css({ 'min-height' : height_custom , 'max-height': height_custom });
}
setConsentDivHeight();

</script>
</body>
</html>