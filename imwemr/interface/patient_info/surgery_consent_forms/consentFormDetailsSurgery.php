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

require_once("../../../library/classes/Mobile_Detect.php");
require_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
require_once(dirname(__FILE__)."/../../../library/classes/cls_common_function.php");
require_once(dirname(__FILE__)."/../../../library/classes/functions_ptInfo.php");
require_once(dirname(__FILE__)."/../../../library/classes/audit_common_function.php");


include_once(dirname(__FILE__)."/../../../library/classes/functions.smart_tags.php");
include_once(dirname(__FILE__)."/../../../library/classes/class.cls_hold_document.php");
include_once(dirname(__FILE__)."/../../../library/classes/print_pt_key.php");
include(dirname(__FILE__).'/../../../library/bar_code/code128/code128.class.php');

$oSaveFile = new SaveFile($patient_id);
$oSaveFile->ptDir("consent_forms");
$oSaveFileUser = new SaveFile();

$OBJCommonFunction = new CLSCommonFunction;

$OBJsmart_tags = new SmartTags;
$OBJhold_sign = new CLSHoldDocument;
$obj_print_pt_key=new print_pt_key;

$blClientBrowserIpad = false; 
$enable_sig_web = enable_web_sig_pad();
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
function getSigImage($postDataWit,$pth="",$rootPath){
	global $webServerRootDirectoryName;
	$rootPath = $webServerRootDirectoryName;//THIS PARAMETER HOLD LOCAL ADDRESS OF SERVER i.e. "/var/www/html/";
	$output = shell_exec("java -cp .:".$rootPath."SigPlusLinuxHSB/SigPlus.jar:".$rootPath."SigPlusLinuxHSB/RXTXcomm.jar:".$rootPath."SigPlusLinuxHSB SigPlusImgDemoV2 ".$postDataWit." ".$rootPath."SigPlusLinuxHSB/sig.jpg 2>&1");
	@copy($rootPath."SigPlusLinuxHSB/sig.jpg",$pth);
	
}

//START CODE TO SET NEW CLASS TO CONSENT FORMS
$upcoming_appt='';
if($_SESSION['patient']){
	$obj_print_pt_key->getApptFuture($_SESSION['patient'],'','','n');
}

//------NEW APPOINTMENT VARIABLE INFORMATION-------------------------
$apptInfoArr2 = $obj_print_pt_key->getApptInfo($patient_id,'','','',1);

//START CODE TO SET NEW CLASS TO CONSENT FORMS
$htmlFolder = "html_to_pdf";
$htmlV2Class=true;	
$htmlFilePth = "html_to_pdf/createPdf.php";
$htmlFilename="file_location";

if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true) {
	$blClientBrowserIpad = true;
}
//var_dump($blClientBrowserIpad);

/*--IF PHY SIG IS AVAILABLE --*/
	$id = $_SESSION['authId'];
	$chkSignQry = "SELECT sign, sign_path FROM users WHERE id='".$_SESSION['authId']."'";
	$chkSignRes = imw_query($chkSignQry);
	$chkSignRow = imw_fetch_array($chkSignRes);
	$elem_sign = $chkSignRow["sign"];
	$elem_sign_path = $chkSignRow["sign_path"];
	$updir=substr(data_path(), 0, -1);
	$srcDir = substr(data_path(1), 0, -1);
	$oSaveFileUser->ptDir("UserId_".$id."/sign");
	if((!empty($elem_sign_path) && file_exists($updir.$elem_sign_path))) {		
		$sigDivDataURL = $srcDir.$elem_sign_path;
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
/*--END OF PHY SIG IS AVAILABLE--*/



$polStSig = 0;
$polStSig = (int)$_SESSION['AUDIT_POLICIES']['Signature_Created_Validated'];
if($polStSig == 1){
	$signatureDataFields = array(); 
	$signatureDataFields = make_field_type_array('surgery_consent_form_signature');
	if($signatureDataFields == 1146){
		$signatureError = "Error : Table 'surgery_consent_form_signature' doesn't exist";
	}
}

$patient_id = $_SESSION['patient'];
$consent_form_id = $_REQUEST['consent_form_id'];
$insert_id = $patientSite = "";
$curentDate = date("Y-m-d");
$hiddenApptIdList = $_REQUEST['hiddenApptIdList'];

$andSaApptQry=" AND sa.sa_app_start_date >= '".$curentDate."' ";
if($hiddenApptIdList) {  $andSaApptQry=" AND sa.id ='".$hiddenApptIdList."' ";}
//---- get Patient details ---------
$qryGetPatientSite  = "SELECT sa.id as ptApptId,sa.sa_app_start_date,sa.procedure_site,sp.proc as saProc, us.pro_title as proTitle, us.fname as proFName, us.mname as proMname, us.lname as proLName FROM schedule_appointments sa 
						inner join slot_procedures sp on sp.id = sa.procedureid 
						left join users us on us.id = if(sa.facility_type_provider!='0',sa.facility_type_provider,sa.sa_doctor_id) 
						WHERE 
						sa.sa_patient_id = '".$patient_id."' ".$andSaApptQry."  
						and sa_patient_app_status_id NOT IN (203,201,18,19,20) order by sa_app_start_date asc limit 1";
$patientSiteDetails = get_array_records_query($qryGetPatientSite);
$patientSite = $patientSiteDetails[0]['procedure_site'];
$patientProcedure = $patientSiteDetails[0]['saProc'];
$ptApptId = $patientSiteDetails[0]['ptApptId'];
if(!$hiddenApptIdList) { $hiddenApptIdList = $ptApptId; }
$Consent_patientConfirmDos = $patientSiteDetails[0]['sa_app_start_date'];
if($Consent_patientConfirmDos) { $Consent_patientConfirmDos=get_date_format(date('Y-m-d',strtotime($Consent_patientConfirmDos)));}
$phy_name = trim($patientSiteDetails[0]['proTitle']).' '.trim($patientSiteDetails[0]['proLName']).', '.trim($patientSiteDetails[0]['proFName']).' '.trim($patientSiteDetails[0]['proMname']);
$phy_name = trim($phy_name);
//---- get Patient site from appointment ---------
$qry = "select *,date_format(DOB,'".get_sql_date_format()."') as pat_dob,date_format(date,'".get_sql_date_format()."') as reg_date
		from patient_data where id = '$patient_id'";
$patientDetails = get_array_records_query($qry);
$patient_initial = substr($patientDetails[0]['fname'],0,1);
$patient_initial .= substr($patientDetails[0]['lname'],0,1);

//--- get reffering physician name --------
$primary_care_id = $patientDetails[0]['primary_care_id'];
$qry = "select concat(LastName,', ',FirstName) as name , MiddleName, FirstName, LastName, Title, specialty, physician_phone, Address1, Address2, ZipCode, City, State from refferphysician
		where physician_Reffer_id = '$primary_care_id'";
$reffPhyDetail = get_array_records_query($qry);
$reffer_name = ucwords(trim($reffPhyDetail[0]['name'].' '.$reffPhyDetail[0]['MiddleName']));
$refPhyAddress="";
$refPhyAddress .= (!empty($reffPhyDetail[0]['Address1'])) ? trim($reffPhyDetail[0]['Address1']) : "";
$refPhyAddress .= (!empty($reffPhyDetail[0]['Address2'])) ? "<br>".trim($reffPhyDetail[0]['Address2']) : "";
//--- get physician name -------- it is now geting from Scheduler 29-july-2010
$pro_id = $patientDetails[0]['providerID'];
$qry = "select concat(lname,', ',fname) as name,mname from users where id = '$pro_id'";
$phyDetail = get_array_records_query($qry);
$phy_name_demo = "";
$phy_name_demo = ucwords(trim($phyDetail[0]['name'].' '.$phyDetail[0]['mname']));

$qry = "select *,date_format(dob,'".get_sql_date_format()."') as res_dob 
		from resp_party where patient_id = '$patient_id'";
$resDetails = get_array_records_query($qry);
//--- save form informations -------
$data['consent_template_id'] = $show_td;
$data['patient_id'] = $patient_id;

if($content_save && $content_save!='changeAppt'){
	$apptIdList = $_REQUEST['apptIdList'];
	$sig_count = $_POST['sig_count'];
	$show_td = $_POST['show_td'];
	$qry = "select consent_name,consent_alias,consent_category_id,consent_id,consent_data from surgery_center_consent_forms_template
			where consent_id = '$show_td'";			
	$form_content = get_array_records_query($qry);
	$consent_form_content_data = $form_content[0]['consent_data'];
	
	//-- get signature applets ----
	$arrRepSig = array("{SURGEON SIGNATURE}","{WITNESS SIGNATURE}","{Surgeon's Signature}","{Witness's Signature}","{ASSISTANT_SURGEON_SIGNATURE}");
	$consent_form_content_data = str_ireplace('{SURGEON SIGNATURE}','{SIGNATURE}',$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{ASSISTANT_SURGEON_SIGNATURE}','{SIGNATURE}',$consent_form_content_data);
	//$consent_form_content_data = str_ireplace('{WITNESS SIGNATURE}','{SIGNATURE}',$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{Surgeon's Signature}",'{SIGNATURE}',$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{Witness's Signature}",'{SIGNATURE}',$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{Nurse's Signature}",'{SIGNATURE}',$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{AnesThesiologist's Signature}",'{SIGNATURE}',$consent_form_content_data);
	
	$consent_form_content_data = str_ireplace("{Surgeon&#39;s Signature}",'{SIGNATURE}',$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{Witness&#39;s Signature}",'{SIGNATURE}',$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{Nurse&#39;s Signature}",'{SIGNATURE}',$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{AnesThesiologist&#39;s Signature}",'{SIGNATURE}',$consent_form_content_data);

	$srgnSig = addslashes("{Surgeon's Signature}");
	$wtnsSig = addslashes("{Witness's Signature}");
	$nursSig = addslashes("{Nurse's Signature}");
	$anesSig = addslashes("{AnesThesiologist's Signature}");
	$consent_form_content_data= str_ireplace($srgnSig,"{SIGNATURE}",$consent_form_content_data);
	$consent_form_content_data= str_ireplace($wtnsSig,"{SIGNATURE}",$consent_form_content_data);
	$consent_form_content_data= str_ireplace($nursSig,"{SIGNATURE}",$consent_form_content_data);
	$consent_form_content_data= str_ireplace($anesSig,"{SIGNATURE}",$consent_form_content_data);
	// Signature Applet 
	
	$consent_form_content_data = htmlentities(addslashes($consent_form_content_data));
	$consent_name = $form_content[0]['consent_name'];
	$consent_alias = $form_content[0]['consent_alias'];
	$consent_category_id = $form_content[0]['consent_category_id'];
	$consent_id = $form_content[0]['consent_id'];
	
	$data['patient_id'] = $patient_id;
	$data['surgery_consent_name'] = $consent_name;
	$data['surgery_consent_alias'] = $consent_alias;
	$data['consent_category_id'] = $consent_category_id;
	$data['consent_template_id'] = $consent_id;
	//START MAKE VALUE IN {} AS CASE SENSITIVE	
	$consent_form_content_data= str_ireplace("{TEXTBOX_XSMALL}","{TEXTBOX_XSMALL}",$consent_form_content_data);
	$consent_form_content_data= str_ireplace("{TEXTBOX_SMALL}","{TEXTBOX_SMALL}",$consent_form_content_data);
	$consent_form_content_data= str_ireplace("{TEXTBOX_MEDIUM}","{TEXTBOX_MEDIUM}",$consent_form_content_data);
	$consent_form_content_data= str_ireplace("{TEXTBOX_LARGE}","{TEXTBOX_LARGE}",$consent_form_content_data);
	$consent_form_content_data= str_ireplace("{PHYSICIAN SIGNATURE}","{PHYSICIAN SIGNATURE}",$consent_form_content_data);
	$consent_form_content_data= str_ireplace("{WITNESS SIGNATURE}","{WITNESS SIGNATURE}",$consent_form_content_data);
	
	//END MAKE VALUE IN {} AS CASE SENSITIVE
	$arrStr = array("{TEXTBOX_XSMALL}","{TEXTBOX_SMALL}","{TEXTBOX_MEDIUM}","{TEXTBOX_LARGE}");
	for($j = 0;$j<count($arrStr);$j++){
		if($arrStr[$j] == '{TEXTBOX_XSMALL}'){
			$name = 'xsmall';
			$size = 1;
		}
		else if($arrStr[$j] == '{TEXTBOX_SMALL}'){
			$name = 'small';
			$size = 30;
		}
		else if($arrStr[$j] == '{TEXTBOX_MEDIUM}'){
			$name = 'medium';
			$size = 60;
		}
		else if($arrStr[$j] == '{TEXTBOX_LARGE}'){
			$name = 'large';
			$size = 120;
		}
		$repVal = '';
		if(substr_count($consent_form_content_data,$arrStr[$j]) > 1){
			if($arrStr[$j] == '{TEXTBOX_XSMALL}' || $arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}'){
				$c = 1;
				$arrExp = explode($arrStr[$j],$consent_form_content_data);
				for($p = 0;$p<count($arrExp)-1;$p++){
					$repVal .= $arrExp[$p].'<input type="text" class="form-control" name="'.$name.$c.'" value="'.htmlentities(addslashes($_POST[$name.$c])).'" size="'.$size.'" maxlength="'.$size.'">';
					$c++;
				}
				$repVal .= end($arrExp);
				$consent_form_content_data = $repVal;
			}
			else if($arrStr[$j] == '{TEXTBOX_LARGE}'){
				$c = 1;
				$arrExp = explode($arrStr[$j],$consent_form_content_data);
				for($p = 0;$p<count($arrExp)-1;$p++){
					$repVal .= $arrExp[$p].'<textarea rows="2" cols="100" name="'.$name.$c.'"> '.htmlentities(addslashes($_POST[$name.$c])).' </textarea>';
					$c++;
				}
				$repVal .= end($arrExp);
				$consent_form_content_data = $repVal;
			}
		}
		else{
			if($arrStr[$j] == '{TEXTBOX_XSMALL}' || $arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}'){
				$repVal = str_ireplace($arrStr[$j],'<input type="text" class="form-control" class="form-control" name="'.$name.'" value="'.htmlentities(addslashes($_POST[$name])).'" size="'.$size.'" maxlength="'.$size.'">',$consent_form_content_data);
				$consent_form_content_data = $repVal;
			}
			else if($arrStr[$j] == '{TEXTBOX_LARGE}'){
				$repVal = str_ireplace($arrStr[$j],'<textarea rows="2" cols="100" name="'.$name.'"> '.htmlentities(addslashes($_POST[$name])).' </textarea>',$consent_form_content_data);
				$consent_form_content_data = $repVal;
			}
		}
		 
	}
	
	//start code for iPad 
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
		$consent_form_content_data = $row_arr[0];//height="45" width="225"
		for($c=1;$c<count($row_arr);$c++){
			$hiddSigIpadIdWitImg 	= trim($_REQUEST['hiddSigIpadIdWit'.$c]);
			$hidd_signReplacedWit  	= trim($_REQUEST['hidd_signReplacedWit'.$c]);
			if($hiddSigIpadIdWitImg) {
				$consent_form_content_data .= '<img src="'.$hiddSigIpadIdWitImg.'" width="150" height="83">';	
			}else if($hidd_signReplacedWit) {
				$hidd_signReplacedWit = urldecode($hidd_signReplacedWit);
				$consent_form_content_data .= '<img src="'.$hidd_signReplacedWit.'" width="150" height="45">';	
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
				$consent_form_content_data .= '<img src="'.$hidd_signReplacedPhy.'" width="150" height="83">';	
			}else {
				$consent_form_content_data .= '{PHYSICIAN SIGNATURE}';
				
			}
			
			$consent_form_content_data .= $row_arr[$c];
		}
	
	//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 		
	$consent_form_content_data = str_ireplace('{PATIENT NAME TITLE}',ucwords($patientDetails[0]['title']),$consent_form_content_data);	
	$consent_form_content_data = str_ireplace('{PATIENT FIRST NAME}',ucwords($patientDetails[0]['fname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{MIDDLE NAME}',ucwords($patientDetails[0]['mname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{MIDDLE INITIAL}",ucwords($patientDetails[0]['mname']),$consent_form_content_data);
	
	$consent_form_content_data = str_ireplace('{LAST NAME}',ucwords($patientDetails[0]['lname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SEX}',ucwords($patientDetails[0]['sex']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{DOB}',ucwords($patientDetails[0]['pat_dob']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT_NICK_NAME}',ucwords($patientDetails[0]['nick_name']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT SS}',ucwords($patientDetails[0]['ss']),$consent_form_content_data);
	//=============START WORK TO SHOW THE LAST 4 DIGIT OF PATIENT SS==========================
	if(trim($patientDetails[0]['ss'])!=''){
	$consent_form_content_data = str_ireplace('{PATIENT_SS4}',ucwords(substr_replace($patientDetails[0]['ss'],'XXX-XX',0,6)),$consent_form_content_data);
	}else{
	$consent_form_content_data = str_ireplace('{PATIENT_SS4}','',$consent_form_content_data);
	}
	//===========================END WORK===================================================
	
	$consent_form_content_data = str_ireplace('{SURGEON NAME}',ucwords($phy_name),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PHYSICIAN NAME}',ucwords($phy_name_demo),$consent_form_content_data);
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
	$consent_form_content_data = str_ireplace('{PatientID}',$_SESSION['patient'],$consent_form_content_data);
	//--- Reffering physician vocabularies replaced-----
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN TITLE}',trim(ucwords($reffPhyDetail[0]['Title'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN FIRST NAME}',	trim(ucwords($reffPhyDetail[0]['FirstName'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN LAST NAME}',	trim(ucwords($reffPhyDetail[0]['LastName'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY SPECIALITY}',trim(ucwords($reffPhyDetail[0]['specialty'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY PHONE}',trim(ucwords($reffPhyDetail[0]['physician_phone'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY STREET ADDR}',$refPhyAddress,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY CITY}',trim(ucwords($reffPhyDetail[0]['City'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY STATE}',trim(ucwords($reffPhyDetail[0]['State'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY ZIP}',trim(ucwords($reffPhyDetail[0]['ZipCode'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REFFERING PHY.}',ucwords($reffer_name),$consent_form_content_data);
	
	//=============RESPONSIBLE PARTY DATA REPLACEMENT-I======================================================
	//=============NOW IF PATIENT HAVE NO RESPONSILE PERSON THEN PATIENT DATA WILL BE REPLACED.=============
	if(count($resDetails)>0)
	{
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
	
	}
	else
	{
		
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
	//=====================================THE END REPONSIBLE PARTY DATA-I========================================
	
	$consent_form_content_data= str_ireplace("{DOS}","<b>".$Consent_patientConfirmDos."</b>",$consent_form_content_data);
	$consent_form_content_data= str_ireplace("{SITE}","<b>".$patientSite."</b>",$consent_form_content_data);
	$consent_form_content_data= str_ireplace("{PROCEDURE}","<b>".$patientProcedure."</b>",$consent_form_content_data);
	$consent_form_content_data= str_ireplace("{SECONDARY PROCEDURE}","<b>".$Consent_patientConfirmSecProc."</b>",$consent_form_content_data);
	$consent_form_content_data= str_ireplace("{DATE}","<b>".get_date_format(date('Y-m-d'))."</b>",$consent_form_content_data);
	$consent_form_content_data = str_ireplace("{TIME}","<b>".date('h:i A')."</b>",$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{APPT_FUTURE}',$upcoming_appt,$consent_form_content_data);
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
	
	
	$data['left_navi_status'] = "false";
	
	
	//START
	$opreaterId = $_SESSION['authId'];			
	$ip = getRealIpAddr();
	$URL = $_SERVER['PHP_SELF'];													 
	$os = getOS();
	$browserInfoArr = array();
	$browserInfoArr = _browser();
	$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
	$browserName = str_ireplace(";","",$browserInfo);													 
	$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);			
	$arrAuditTrail = array();
	
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
										"Table_Name"=>"surgery_consent_form_signature",
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
										"pid"=> $patient_id,
										"Machine_Name"=> $machineName,
										"Category"=> "signature",
										"Category_Desc"=> "surgery_consent_form_signature",	
										"Filed_Label"=> "Form Name ".$consent_name."<br>Signaure Position ".$ps,
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
			//$imgNamePhyArr = explode('\\',$phySignArr[$c-1]);
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
										"Table_Name"=>"surgery_consent_form_signature",
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
										"pid"=> $patient_id,
										"Machine_Name"=> $machineName,
										"Category"=> "signature",
										"Category_Desc"=> "surgery_consent_form_signature",	
										"Filed_Label"=> "Form Name ".$consent_name."<br>Signaure Position ".$ws,
										"New_Value"=> $postDataWit																																										
									);
			}else if($postSignImgWit && $postSignImgWit != ''){
				$witSignArr[$ws] = urldecode($postSignImgWit);
			}
		}
		
		/*--REPLACING SMART TAGS (IF FOUND) WITH LINKS--*/
			$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
			$tagconsent_form_content_data = '';
			foreach($arr_smartTags as $key=>$val){
		//		$val = str_ireplace('&nbsp',' ',$val);
				$regpattern='/(\['.$val.'\])+/i';
				$row_arr = preg_split($regpattern,$consent_form_content_data);
		//		$row_arr = explode("[".$val."]",$consent_form_content_data);
				$tagconsent_form_content_data = $row_arr[0];
				for($tagCount=1;$tagCount<count($row_arr);$tagCount++){
					if($_POST[$key.'_'.$tagCount.'tag'] != ''){
						$tagconsent_form_content_data .= $_POST[$key.'_'.$tagCount.'tag'].$row_arr[$tagCount];
					}else{
						$tagconsent_form_content_data .= $val.$row_arr[$tagCount];
					}
				}
				
				if($tagconsent_form_content_data != ''){//die($tagconsent_form_content_data);
					$consent_form_content_data = $tagconsent_form_content_data;
				}
			}
		/*--SMART TAG REPLACEMENT END--*/
		
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
	//END
	
	
	// save signature ------
	if($blClientBrowserIpad == false){
		for($ps=1;$ps<=$sig_count;$ps++){
			$postData = $_REQUEST['SigData'.$ps];
			if($postData  && $postData != '000000000000000000000000000000000000000000000000000000000000000000000000'  && $postData !='undefined'){
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
				//ALSO SAVE IMAGE FOR PDF
				
				//--- Save Array Fields --------
				unset($sigDataArr);
				$sigDataArr['signature_content'] = $postData;
				$sigDataArr['consent_template_id'] = $show_td;
		
				$sigDataArr['patient_id'] = $patient_id;
				$sigDataArr['signature_count'] = $ps;
				$sigDataArr['signature_image_path'] = addslashes($newPath);
				$sigDataArr['surgery_consent_auto_id'] = $insert_id;
		
				$SaveConsentSigQry = "insert into `surgery_consent_form_signature` set 
									signature_content = '".addslashes($postData)."',									
									consent_template_id ='".addslashes($show_td)."',	
									patient_id='".addslashes($patient_id)."',
									signature_count='".addslashes($ps)."',
									signature_image_path='".addslashes($newPath)."',
									surgery_consent_auto_id	='".$insert_id."'
									";
				$insert_id_sig = imw_query($SaveConsentSigQry);
				$insert_id_sig = imw_insert_id();
				if(!$insert_id_sig){
					$signatureError = "Error : ". imw_errno() . ": " . imw_error();
				}
				
				$arrAuditTrail [] = 
								array(
										"Pk_Id"=> $insert_id_sig,
										"Table_Name"=>"surgery_consent_form_signature",
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
										"pid"=> $patient_id,
										"Machine_Name"=> $machineName,
										"Category"=> "signature",
										"Category_Desc"=> "surgery_consent_form_signature",	
										"Filed_Label"=> "Form Name ".$consent_name."<br>Signaure Position ".$ps,
										"New_Value"=> $postData																																										
									);
			}
		}
		//-- get signature applets ----
		$consent_form_content_data= str_ireplace("{SIGNATURE}","{SIGNATURE}",$consent_form_content_data);
		$row_pat_arr = explode('{SIGNATURE}',$consent_form_content_data);
		$consent_form_content_data = $row_pat_arr[0];
		for($c=1;$c<count($row_pat_arr);$c++){
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
			if($postData != "" && $postData != '000000000000000000000000000000000000000000000000000000000000000000000000'){				
				$path = $newPath = "";
				$patNewPath = realpath(dirname(__FILE__).'/../../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms/').'/sign_pat_'.$patient_id.'_'.date('d_m_y_h_i_s').'_'.$ps.'.jpeg';
				$patientSignArr[] = $patNewPath;
				$signatureData = str_ireplace("data:image/jpeg;base64,","",$postData);				
				file_put_contents($patNewPath,base64_decode($signatureData));				

				
				//--- Save Array Fields --------
				unset($sigDataArr);
				$sigDataArr['signature_content'] = $postData;
				$sigDataArr['consent_template_id'] = $show_td;
		
				$sigDataArr['patient_id'] = $patient_id;
				$sigDataArr['signature_count'] = $ps;
				$sigDataArr['signature_image_path'] = addslashes($newPath);
				$sigDataArr['surgery_consent_auto_id'] = $insert_id;
		
				$SaveConsentSigQry = "insert into `surgery_consent_form_signature` set 
									signature_content = '".addslashes($postData)."',									
									consent_template_id ='".addslashes($show_td)."',	
									patient_id='".addslashes($patient_id)."',
									signature_count='".addslashes($ps)."',
									signature_image_path='".addslashes($newPath)."',
									surgery_consent_auto_id	='".$insert_id."'
									";
				@copy($path,$newPath);					
				$insert_id_sig = imw_query($SaveConsentSigQry);
				$insert_id_sig = imw_insert_id();
				if(!$insert_id_sig){
					$signatureError = "Error : ". imw_errno() . ": " . imw_error();
				}
				
				##audit signature##
				$arrAuditTrail [] = 
								array(
										"Pk_Id"=> $insert_id_sig,
										"Table_Name"=>"surgery_consent_form_signature",
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
										"Category"=> "signature",
										"Category_Desc"=> "surgery_consent_form_signature",	
										"Filed_Label"=> "Form Name ".$consent_name."<br>Signaure Position ".$ps,
										"New_Value"=> $postData																																										
									);
			}
		$postData = "";
		}
	}

	$data['surgery_consent_data'] = htmlentities(addslashes($consent_form_content_data));
	$SaveConsentSurgeryQry = "insert into `surgery_consent_filled_form` set 
									surgery_consent_data 	= '".htmlentities(addslashes($consent_form_content_data))."',
									form_status 			= 'completed',
									surgery_consent_name	= '".addslashes($consent_name)."',
									surgery_consent_alias	= '".addslashes($consent_alias)."',
									consent_template_id		= '".$consent_id."',	
									consent_category_id		= '".$consent_category_id."',			
									left_navi_status		= 'false',	
									appt_id					= '".$apptIdList."',							
									patient_id 				= '".$patient_id."'
									";
	imw_query($SaveConsentSurgeryQry) or die($SaveConsentSurgeryQry);
	$insert_id = imw_insert_id();
	 /*-----SAVING HOLD SIGNATURE INFO-----*/
	 $OBJhold_sign->section_col = "sx_consent_id";
	 $OBJhold_sign->section_col_value = $insert_id;
	 $OBJhold_sign->save_hold_sign();
	 /*-----end of HOLD SIGNATURE INFO-----------*/
	 /*-----SURGERY CONSENT LOG TABLE ENTRY-----------*/
	$action="";
	//echo 'content_save => '.$content_save;
	//echo 'on_hold_save =>  '.$on_hold_save;
	//die;
	//pre($_REQUEST); die;
	if ($content_save == 'print_form')
	{
		$action = 'Save and Print';  
	}
	else if ($content_save == 'save_form' && $on_hold_save=='on_hold')
	{
		$action = 'On Hold';
	}
	else if ($content_save == 'save_form') 
	{
		$action = 'Save';
	}
	
	$SaveConsentSurgeryLogQry = "insert into `surgery_consent_log_tbl` set     
									surgery_consent_id = '".$insert_id."', 
									patient_id 		= '".$patient_id."', 
									template_id		= '".$consent_id."', 
									template_name	= '".addslashes($consent_name)."', 
									template_data 	= '".htmlentities(addslashes($consent_form_content_data))."', 
									operator_id 	= '".$_SESSION["authId"]."', 
									action			= '".$action."', 
									appt_id			= '".$apptIdList."' 	 						
									";
	imw_query($SaveConsentSurgeryLogQry) or die($SaveConsentSurgeryLogQry);
	 
	  /*-----THE END-----------*/
	##audit signature save##	
	$table = array("surgery_consent_form_signature");
	$error = array($signatureError);
	$mergedArray = merging_array($table,$error);	
	if($polStSig == 1){
		auditTrail($arrAuditTrail,$mergedArray,0,0,0);
	}
}
//END--- save form informations -------
$qry = "select consent_data from surgery_center_consent_forms_template
		where consent_id = '$consent_form_id'";			
//$form_content = get_array_records_query($qry);
$res_content = imw_query($qry);
$form_content = imw_fetch_array($res_content);
$consent_form_content_data = stripslashes($form_content['consent_data']);

//START MAKE VALUE IN {} AS CASE SENSITIVE	
$consent_form_content_data = str_ireplace("{TEXTBOX_XSMALL}","{TEXTBOX_XSMALL}",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{TEXTBOX_SMALL}","{TEXTBOX_SMALL}",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{TEXTBOX_MEDIUM}","{TEXTBOX_MEDIUM}",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{TEXTBOX_LARGE}","{TEXTBOX_LARGE}",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{PHYSICIAN SIGNATURE}","{PHYSICIAN SIGNATURE}",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{WITNESS SIGNATURE}","{WITNESS SIGNATURE}",$consent_form_content_data);
//END MAKE VALUE IN {} AS CASE SENSITIVE

$arrStr = array("{TEXTBOX_XSMALL}","{TEXTBOX_SMALL}","{TEXTBOX_MEDIUM}","{TEXTBOX_LARGE}");
for($j = 0;$j<count($arrStr);$j++){
	if($arrStr[$j] == '{TEXTBOX_XSMALL}'){
		$name = 'xsmall';
		$size = 1;
	}
	else if($arrStr[$j] == '{TEXTBOX_SMALL}'){
		$name = 'small';
		$size = 30;
	}
	else if($arrStr[$j] == '{TEXTBOX_MEDIUM}'){
		$name = 'medium';
		$size = 60;
	}
	else if($arrStr[$j] == '{TEXTBOX_LARGE}'){
		$name = 'large';
		$size = 120;
	}
	$repVal = '';
	if(substr_count($consent_form_content_data,$arrStr[$j]) > 1){
		if($arrStr[$j] == '{TEXTBOX_XSMALL}' || $arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}'){
			$c = 1;
			$arrExp = explode($arrStr[$j],$consent_form_content_data);
			for($p = 0;$p<count($arrExp)-1;$p++){
				$repVal .= $arrExp[$p].'<input type="text" class="form-control" name="'.$name.$c.'" value="'.$_POST[$name.$c].'" size="'.$size.'"  maxlength="'.$size.'">';
				$c++;
			}
			$repVal .= end($arrExp);
			$consent_form_content_data = $repVal;
		}
		else if($arrStr[$j] == '{TEXTBOX_LARGE}'){
			$c = 1;
			$arrExp = explode($arrStr[$j],$consent_form_content_data);
			for($p = 0;$p<count($arrExp)-1;$p++){
				$repVal .= $arrExp[$p].'<textarea rows="2" cols="100" name="'.$name.$c.'"> '.$_POST[$name.$c].' </textarea>';
				$c++;
			}
			$repVal .= end($arrExp);
			$consent_form_content_data = $repVal;
		}
	}
	else{
		if($arrStr[$j] == '{TEXTBOX_XSMALL}' || $arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}'){
			$repVal = str_ireplace($arrStr[$j],'<input type="text" class="form-control" name="'.$name.'" value="'.$_POST[$name].'" size="'.$size.'" maxlength="'.$size.'">',$consent_form_content_data);
			$consent_form_content_data = $repVal;
		}
		else if($arrStr[$j] == '{TEXTBOX_LARGE}'){
			$repVal = str_ireplace($arrStr[$j],'<textarea rows="2" cols="100" name="'.$name.'"> '.$_POST[$name].' </textarea>',$consent_form_content_data);
			$consent_form_content_data = $repVal;
		}
	}		 
}
//-- get signature applets ----
$consent_form_content_data = str_ireplace('{SURGEON SIGNATURE}','{SIGNATURE}',$consent_form_content_data);
$consent_form_content_data = str_ireplace('{ASSISTANT_SURGEON_SIGNATURE}','{SIGNATURE}',$consent_form_content_data);
//$consent_form_content_data = str_ireplace('{WITNESS SIGNATURE}','{SIGNATURE}',$consent_form_content_data);
$consent_form_content_data = str_ireplace("{Surgeon's Signature}",'{SIGNATURE}',$consent_form_content_data);
$consent_form_content_data = str_ireplace("{Witness's Signature}",'{SIGNATURE}',$consent_form_content_data);
$consent_form_content_data = str_ireplace("{Nurse's Signature}",'{SIGNATURE}',$consent_form_content_data);
$consent_form_content_data = str_ireplace("{AnesThesiologist's Signature}",'{SIGNATURE}',$consent_form_content_data);

$consent_form_content_data = str_ireplace("{Surgeon&#39;s Signature}",'{SIGNATURE}',$consent_form_content_data);
$consent_form_content_data = str_ireplace("{Witness&#39;s Signature}",'{SIGNATURE}',$consent_form_content_data);
$consent_form_content_data = str_ireplace("{Nurse&#39;s Signature}",'{SIGNATURE}',$consent_form_content_data);
$consent_form_content_data = str_ireplace("{AnesThesiologist&#39;s Signature}",'{SIGNATURE}',$consent_form_content_data);

$row_arr = explode('{SIGNATURE}',$consent_form_content_data);
/*
$consent_form_content_data = $row_arr[0];
for($c=1;$c<count($row_arr);$c++){
	$imgNameArr = explode('\\',$patientSignArr[$c-1]);
	$imgSrc = '../../'.end($imgNameArr);
	if(file_exists(dirname(__FILE__).'/'.$imgSrc) && is_dir(dirname(__FILE__).'/'.$imgSrc) == ''){
		$consent_form_content_data .= '<br><span style="text-align: left" ><img name="'.$imgSrc.'" id="sign_id[]" src="'.$imgSrc.'" width="250" height="83"></span>';
	}
	else{
		$consent_form_content_data .= '{SIGNATURE}';
	}
	$consent_form_content_data .= $row_arr[$c];
}
*/
//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 	
$consent_form_content_data = str_ireplace('{PATIENT NAME TITLE}',ucwords($patientDetails[0]['title']),$consent_form_content_data);		
$consent_form_content_data = str_ireplace('{PATIENT FIRST NAME}',ucwords($patientDetails[0]['fname']),$consent_form_content_data);
$consent_form_content_data = str_ireplace('{MIDDLE NAME}',ucwords($patientDetails[0]['mname']),$consent_form_content_data);
$consent_form_content_data = str_ireplace("{MIDDLE INITIAL}",ucwords($patientDetails[0]['mname']),$consent_form_content_data);

$consent_form_content_data = str_ireplace('{LAST NAME}',ucwords($patientDetails[0]['lname']),$consent_form_content_data);
$consent_form_content_data = str_ireplace('{SEX}',ucwords($patientDetails[0]['sex']),$consent_form_content_data);
$consent_form_content_data = str_ireplace('{DOB}',ucwords($patientDetails[0]['pat_dob']),$consent_form_content_data);
$consent_form_content_data = str_ireplace('{PATIENT_NICK_NAME}',ucwords($patientDetails[0]['nick_name']),$consent_form_content_data);
$consent_form_content_data = str_ireplace('{PATIENT SS}',ucwords($patientDetails[0]['ss']),$consent_form_content_data);
//=============START WORK TO SHOW THE LAST 4 DIGIT OF PATIENT SS==========================
if(trim($patientDetails[0]['ss'])!=''){
	$consent_form_content_data = str_ireplace('{PATIENT_SS4}',ucwords(substr_replace($patientDetails[0]['ss'],'XXX-XX',0,6)),$consent_form_content_data);
}else{
	$consent_form_content_data = str_ireplace('{PATIENT_SS4}','',$consent_form_content_data);
}
//===========================END WORK===================================================
$consent_form_content_data = str_ireplace('{SURGEON NAME}',ucwords($phy_name),$consent_form_content_data);
$consent_form_content_data = str_ireplace('{PHYSICIAN NAME}',ucwords($phy_name_demo),$consent_form_content_data);
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
$consent_form_content_data = str_ireplace('{PatientID}',$_SESSION['patient'],$consent_form_content_data);

	//--- Reffering physician vocabularies replaced-----
$consent_form_content_data = str_ireplace('{REF PHYSICIAN TITLE}',trim(ucwords($reffPhyDetail[0]['Title'])),$consent_form_content_data);
$consent_form_content_data = str_ireplace('{REF PHYSICIAN FIRST NAME}',	trim(ucwords($reffPhyDetail[0]['FirstName'])),$consent_form_content_data);
$consent_form_content_data = str_ireplace('{REF PHYSICIAN LAST NAME}',	trim(ucwords($reffPhyDetail[0]['LastName'])),$consent_form_content_data);
$consent_form_content_data = str_ireplace('{REF PHY SPECIALITY}',trim(ucwords($reffPhyDetail[0]['specialty'])),$consent_form_content_data);
$consent_form_content_data = str_ireplace('{REF PHY PHONE}',trim(ucwords($reffPhyDetail[0]['physician_phone'])),$consent_form_content_data);
$consent_form_content_data = str_ireplace('{REF PHY STREET ADDR}',$refPhyAddress,$consent_form_content_data);
$consent_form_content_data = str_ireplace('{REF PHY CITY}',trim(ucwords($reffPhyDetail[0]['City'])),$consent_form_content_data);
$consent_form_content_data = str_ireplace('{REF PHY STATE}',trim(ucwords($reffPhyDetail[0]['State'])),$consent_form_content_data);
$consent_form_content_data = str_ireplace('{REF PHY ZIP}',trim(ucwords($reffPhyDetail[0]['ZipCode'])),$consent_form_content_data);
$consent_form_content_data = str_ireplace('{REFFERING PHY.}',ucwords($reffer_name),$consent_form_content_data);

//=============RESPONSIBLE PARTY DATA REPLACEMENT-II======================================================
//=============NOW IF PATIENT HAVE NO RESPONSILE PERSON THEN PATIENT DATA WILL BE REPLACED.=============
if(count($resDetails)>0)
{
	  $consent_form_content_data = str_ireplace('{RES.PARTY TITLE}',ucwords($resDetails[0]['title']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($resDetails[0]['fname']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY MIDDLE NAME}',ucwords($resDetails[0]['mname']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY LAST NAME}',ucwords($resDetails[0]['lname']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY DOB}',ucwords($resDetails[0]['res_dob']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY SS}',ucwords($resDetails[0]['ss']),$consent_form_content_data);
	  $consent_form_content_data = str_ireplace('{RES.PARTY SEX}',ucwords($resDetails[0]['sex']),$consent_form_content_data);
	  $strToShowRelation = $resDetails[0]['relation'];
	  if(strtolower($resDetails[0]['relation']) == "doughter")
	  {
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

}
else
{
	
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
//=====================================THE END REPONSIBLE PARTY DATA-II========================================

$consent_form_content_data = str_ireplace("{DOS}","<b>".$Consent_patientConfirmDos."</b>",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{SITE}","<b>".$patientSite."</b>",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{PROCEDURE}","<b>".$patientProcedure."</b>",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{SECONDARY PROCEDURE}","<b>".$Consent_patientConfirmSecProc."</b>",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{DATE}","<b>".get_date_format(date('Y-m-d'))."</b>",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{TIME}","<b>".date('h:i A')."</b>",$consent_form_content_data);
$consent_form_content_data = str_ireplace('{APPT_FUTURE}',$upcoming_appt,$consent_form_content_data);

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

$consent_form_content_data = str_ireplace("{Signature}","{SIGNATURE}",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{Nurse's Signature}","{SIGNATURE}",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{Anesthesiologist's Signature}","{SIGNATURE}",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{Signature}","{SIGNATURE}",$consent_form_content_data);

$consent_form_content_data = str_ireplace("{Surgeon&#39;s Signature}",'{SIGNATURE}',$consent_form_content_data);
$consent_form_content_data = str_ireplace("{Witness&#39;s Signature}",'{SIGNATURE}',$consent_form_content_data);
$consent_form_content_data = str_ireplace("{Nurse&#39;s Signature}",'{SIGNATURE}',$consent_form_content_data);
$consent_form_content_data = str_ireplace("{AnesThesiologist&#39;s Signature}",'{SIGNATURE}',$consent_form_content_data);

$consent_form_content_data = str_ireplace("{Signature}","{SIGNATURE}",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{Surgeon's&nbsp;Signature}","{SIGNATURE}",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{Witness's&nbsp;Signature}","{SIGNATURE}",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{Nurse's&nbsp;Signature}","{SIGNATURE}",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{Anesthesiologist's&nbsp;Signature}","{SIGNATURE}",$consent_form_content_data);

$consent_form_content_data = str_ireplace("{Surgeon&#39;s&nbsp;Signature}",'{SIGNATURE}',$consent_form_content_data);
$consent_form_content_data = str_ireplace("{Witness&#39;s&nbsp;Signature}",'{SIGNATURE}',$consent_form_content_data);
$consent_form_content_data = str_ireplace("{Nurse&#39;s&nbsp;Signature}",'{SIGNATURE}',$consent_form_content_data);
$consent_form_content_data = str_ireplace("{AnesThesiologist&#39;s&nbsp;Signature}",'{SIGNATURE}',$consent_form_content_data);

$consent_form_content_data = str_ireplace("{Signature}","{SIGNATURE}",$consent_form_content_data);
$consent_form_content_data = str_ireplace("{Signature}","{SIGNATURE}",$consent_form_content_data);
$row_arr = explode('{START APPLET ROW}',$consent_form_content_data);
$sig_arr = explode('{SIGNATURE}',$row_arr[0]);	
$sig_data = '';
$ds = 1;
for($s=1;$s<count($sig_arr);$s++,$ds++){
	$sig_data = '<table class="alignLeft" style="border:none;" id="consentSigIpadId'.$ds.'">
					<tr>';
	
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
	
	
	if($blClientBrowserIpad == false){
		$sig_data .= '	<td>
							<span width="145" >
								<div class="consentObjectBeforSign" id="tdObject'.$ds.'" style="HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;">'.$sig_grid.'
								</div>
							</span>
							<span>
								<img title="Sig Pad Signature" style="cursor:pointer;" src="../../../library/images/pen.png" id="SignBtn'.$ds.'" name="SignBtn'.$ds.'" language="VBScript" onclick="'.$sig_onSignClick.'">
							</span>
							<span>
								<img title="Touch Signature" style="display:inline-block;position:relative;cursor:pointer;width:37px ; height:30px;" src="../../../library/images/touch.svg" id="SignBtnTouch'.$ds.'" name="SignBtnTouch'.$ds.'" onclick="OnSignIpadPhy(\''.$_SESSION['patient'].'\',\'ptConsentSurgery\',\'consentSigIpadId'.$ds.'\',\''.$ds.'\');">
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
		$sig_data .= '	<td style="width:320px;height:90px;" class="consentObjectBeforSign" >
							<img style="cursor:pointer; float:right; margin-top:50px;" src="../../../library/images/pen.png" id="SigPen'.$ds.'" onclick="OnSignIpadPhy(\''.$_SESSION['patient'].'\',\'ptConsentSurgery\',\'consentSigIpadId'.$ds.'\',\''.$ds.'\')">
						</td>
					';
				
	}
	$sig_data .= '	</tr>
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
	$ds=1;
	for($s=1;$s<count($sig_wit_arr);$s++,$ds++){
		$sig_wit_data = '<table class="alignLeft" style="border:none;" id="consentSigIpadIdWit'.$ds.'">
							<tr>';
		
		$sig_grid = $sig_onSignClick = $sig_onClearClick = '';	
		if( $enable_sig_web ) {
			$sig_grid = '<canvas id="SigPlusWit'.$ds.'" name="SigPlusWit'.$ds.'" width="310" height="87"></canvas>';
			$sig_onSignClick = 'OnSign('.$ds.',\'SigPlusWit'.$ds.'\',\'SigDataWit'.$ds.'\')';
			$sig_onClearClick = 'OnClear('.$ds.',\'SigPlusWit'.$ds.'\',\'SigDataWit'.$ds.'\')';
		}
		else {
			$sig_grid = '<OBJECT classid=clsid:69A40DA3-4D42-11D0-86B0-0000C025864A height=75
													id=SigPlusWit'.$ds.' name=SigPlusWit'.$ds.'
													style="HEIGHT: 87px; WIDTH: 310px; LEFT: 0px; TOP: 0px;" 
													VIEWASTEXT>
													<PARAM NAME="_Version" VALUE="131095">
													<PARAM NAME="_ExtentX" VALUE="4842">
													<PARAM NAME="_ExtentY" VALUE="1323">
													<PARAM NAME="_StockProps" VALUE="0">
											</OBJECT>';
			$sig_onSignClick = 'OnSignWit'.$ds.'()';
			$sig_onClearClick = 'OnClearWit'.$ds.'()';

		}

		if($blClientBrowserIpad == false){
				$sig_wit_data .= '<td>
									<span id="SpanWitSign'.$ds.'">
										<span >
											<div class="consentObjectBeforSign" id="tdObjectWit'.$ds.'" style="HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;">'.$sig_grid.'</div>
										</span>
										<span>
											<img title="Sig Pad Signature" style="cursor:pointer;" src="../../../library/images/pen.png" id="SignBtnWit'.$ds.'" name="SignBtnWit'.$ds.'" language="VBScript" onclick="'.$sig_onSignClick.'">
										</span>
										<span>
											<img title="Touch Signature" style="display:inline-block;position:relative;cursor:pointer;width:37px ; height:30px;" src="../../../library/images/touch.svg" id="SignBtnWitTouch'.$ds.'" name="SignBtnWitTouch'.$ds.'" onclick="OnSignIpadPhy(\''.$_SESSION['patient'].'\',\'witConsentSurgery\',\'consentSigIpadIdWit'.$ds.'\',\''.$ds.'\')">
										</span>
										<span>
											<img style="cursor:pointer;" src="../../../library/images/eraser.gif" id="buttonWit'.$ds.'" alt="Clear Sign" name="ClearBtnWit'.$ds.'" language="VBScript" onclick="'.$sig_onClearClick.'">
										</span>
										<img src="../../../library/images/text_signature.png" class="link_cursor" onclick="ReplacePadWithSign('.$ds.',\'witness\',\'SpanWitSign'.$ds.'\',\'\')" title="Click to Sign">
									</span>
								</td>	';
		}
		elseif($blClientBrowserIpad == true){
				$sig_wit_data .= '<td style="width:320px;height:90px;" class="consentObjectBeforSign" >
									<img style="cursor:pointer; float:right; margin-top:50px;" src="../../../library/images/pen.png" id="SigPenWit'.$ds.'" onclick="OnSignIpadPhy(\''.$_SESSION['patient'].'\',\'witConsentSurgery\',\'consentSigIpadIdWit'.$ds.'\',\''.$ds.'\')">
								 </td>
								 <td><img src="../../../library/images/text_signature.png" class="link_cursor" onclick="ReplacePadWithSign('.$ds.',\'witness\',\'consentSigIpadIdWit'.$ds.'\',\'ipad\')" title="Click to Sign"></td>
								';
		}
		$sig_wit_data .='	<tr>
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
			$sig_onSignClick = 'OnSignPhy'.$ps.'()';
			$sig_onClearClick = 'OnClearPhy'.$ps.'()';

		}
	if($blClientBrowserIpad == false){
			$sig_phy_data .= '<td>
								<span id="SpanPhySign'.$ps.'">
									<span>
										<div class="consentObjectBeforSign" id="tdObjectPhy'.$ps.'" style="HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;">'.$sig_grid.'</div>
									</span>
									<span>
										<img title="Sig Pad Signature" style="cursor:pointer;" src="../../../library/images/pen.png" id="SignBtnPhy'.$ps.'" name="SignBtnPhy'.$ps.'" language="VBScript" onclick="'.$sig_onSignClick.'">
									</span>
									<span>
										<img title="Touch Signature" style="display:inline-block;position:relative;cursor:pointer;width:37px ; height:30px;" src="../../../library/images/touch.svg" id="SignBtnPhyTouch'.$ps.'" name="SignBtnPhyTouch'.$ps.'" onclick="OnSignIpadPhy(\''.$_SESSION['patient'].'\',\'phyConsentSurgery\',\'consentSigIpadIdPhy'.$ps.'\',\''.$ps.'\')">
									</span>
									<span>
										<img style="cursor:pointer;" src="../../../library/images/eraser.gif" id="buttonPhy'.$ps.'" alt="Clear Sign" name="ClearBtnPhy'.$ps.'" language="VBScript" onclick="'.$sig_onClearClick.'">
									</span>
									<img src="../../../library/images/text_signature.png" class="link_cursor" onclick="ReplacePadWithSign('.$ps.',\'physician\',\'SpanPhySign'.$ps.'\',\'\')" title="Click to Sign">
								</span>
						  	<td>';
							
	}
	elseif($blClientBrowserIpad == true){
			$sig_phy_data .= '<td style="width:320px;height:90px;" class="consentObjectBeforSign" >
								<img style="cursor:pointer; float:right; margin-top:50px;" src="../../../library/images/pen.png" id="SigPenPhy'.$ps.'" onclick="OnSignIpadPhy(\''.$_SESSION['patient'].'\',\'phyConsentSurgery\',\'consentSigIpadIdPhy'.$ps.'\',\''.$ps.'\')">
							  </td>
							  <td><img src="../../../library/images/text_signature.png" class="link_cursor" onclick="ReplacePadWithSign('.$ps.',\'physician\',\'consentSigIpadIdPhy'.$ps.'\',\'ipad\')" title="Click to Sign"></td>
							';
	}
	$sig_phy_data .='	</tr>
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
				$sig_onSignClick = 'OnSign'.$ds.'()';
				$sig_onClearClick = 'OnClear'.$ds.'()';

			}
			
			$td_sign .= '
						<td align="left">
							<table border="0">
								<tr><td>'.$sig_arr1[$t].'</td></tr>
								<tr>
									<td class="consentObjectBeforSign" id="tdObject'.$ds.'">';
									if($blClientBrowserIpad == false){
										$td_sign .= $sig_grid;
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

/*--REPLACING SMART TAGS (IF FOUND) WITH LINKS--*/
	$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
	$tagconsent_form_content_data = '';
	foreach($arr_smartTags as $key=>$val){
//		$val = str_ireplace('&nbsp',' ',$val);
		$regpattern='/(\['.$val.'\])+/i';
		$row_arr = preg_split($regpattern,$consent_form_content);
//		$row_arr = explode("[".$val."]",$consent_form_content_data);
		$tagconsent_form_content_data = $row_arr[0];
		for($tagCount=1;$tagCount<count($row_arr);$tagCount++){
			$tagconsent_form_content_data .= '<a id="'.$key.'_'.$tagCount.'tag" class="cls_smart_tags_link" href="javascript:;">'.$val.'</a><input type="hidden" class="'.$key.'_'.$tagCount.'tag" name="'.$key.'_'.$tagCount.'tag" value="">'.$row_arr[$tagCount];
		}
		
		if($tagconsent_form_content_data != ''){//die($tagconsent_form_content_data);
			$consent_form_content = $tagconsent_form_content_data;
		}
	}
/*--SMART TAG REPLACEMENT END--*/

//START CODE TO GET ALL APPOINTMENTS OF PATIENT
$allApptPtQry  	= "SELECT sa.id,sa.sa_app_start_date FROM schedule_appointments sa 
					INNER JOIN slot_procedures sp on sp.id = sa.procedureid 
					LEFT JOIN users us on us.id = if(sa.facility_type_provider!='0',sa.facility_type_provider,sa.sa_doctor_id)  
				   WHERE 
				  	sa.sa_patient_id = '".$patient_id."' AND sa.sa_app_start_date >= '".$curentDate."'
					AND sa_patient_app_status_id NOT IN (203,201,18,19,20) ORDER BY sa_app_start_date ASC ";
$allApptPtRes 	= imw_query($allApptPtQry);
$apptSelectList ='Select Future Appointment Date <select name="apptIdList" id="apptIdList" style="width:100px;" onChange="changeApptFun(this);"><option value="">Select</option>';

if(imw_num_rows($allApptPtRes)>0) {
	$allAppStartDateShow='';
	while($allApptPtRow = imw_fetch_array($allApptPtRes)) {
		$sel			= '';
		$allApptId 		= $allApptPtRow['id'];
		$allAppStartDate= $allApptPtRow['sa_app_start_date'];
		$allAppStartDateShow = get_date_format(date('Y-m-d',strtotime($allAppStartDate)));
		if($allApptId==$ptApptId) { $sel='selected'; }
		$apptSelectList .= '<option value="'.$allApptId.'"  '.$sel.' >'.$allAppStartDateShow.'</option>';
	}
}
$apptSelectList .='</select>';
//END CODE TO GET ALL APPOINTMENTS OF PATIENT

//--- get all content of consent forms -------	

//By Karan

$currencyReplaceArray = array("$","£");
$consent_filter_data = str_replace($currencyReplaceArray,"".showCurrency()."",$consent_form_content); 

// $consent_form_content is changed with modified variable i.e $consent_filter_data in the below table

$consent_content .= '
					<table id="content_'.$consent_form_id.'" style="display:'.$display.'" width="100%" align="center" cellpadding="1" cellspacing="1" border="0">
						<tr>
							<td align="left" colspan="'.count($sig_arr).'">'.$consent_filter_data.'</td>
						<tr>
					</table>
				';
$consent_content = $consent_content;
?>
<html>
<head>
<style>
</style>
<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap.css" type="text/css">
<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-select.css" type="text/css">
<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
<link href="<?php echo $library_path; ?>/css/style.css" rel="stylesheet">
<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
<style>
.consentObjectAfterSign{ border:solid 1px; border-color:#99CC33; }
.consentObjectBeforSign{ border:solid 1px; border-color:#FF9900}
OBJECT{position: relative;}
#div_smart_tags_options{z-index:2}
.form-control { width:auto; display:inline-block; height:25px; max-width:175px;}
</style>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-select.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
<?php if( $enable_sig_web ){ include_once $GLOBALS['srcdir'].'/sigpad/SigWebTablet.php'; } ?>
<script type="text/javascript">
var isIPad = false;
var touch = "";
var insert_id = '<?php print $insert_id; ?>';
var printOption ="";
if(insert_id){
	var consent_form_id = "<?php echo $_REQUEST["consent_form_id"];?>";
	var content_save = "<?php echo $_REQUEST["content_save"];?>";
	var on_hold_save = "<?php echo $_REQUEST["on_hold_save"];?>";
	if(top.fmain.document.surgery_consent_main_frm) {
		top.fmain.document.surgery_consent_main_frm.consent_form_id.value = consent_form_id;
		if(content_save !='print_form') {
			//top.fmain.surgery_consent_main_frm.submit();
		}
	}
	top.alert_notification_show("Record Saved Successfully");
}

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
			document.getElementById("on_hold_save").value = 'on_hold';
			save_form('save_form');
			//$("#on_hold_save").val("on_hold");
			$('#hold_to_phy_div').modal("hide");
		}
	});
}

function hold_for_dr_sig(){
	if($('#hold_to_physician').val()==''){
			top.fAlert('Please select a physician');
	}else{
		$('#hidd_hold_to_physician').val($('#hold_to_physician').val());
		//save_form('save_form');
		document.getElementById("on_hold_save").value = 'on_hold';
		save_form('save_form');
		$('#hold_to_phy_div').hide();
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
if((!empty($elem_sign) && $elem_sign != "0-0-0:;") || (!empty($elem_sign_path) && file_exists($updir.$elem_sign_path))) {
	echo 'OperatorSign = true;';
}else{
	echo 'OperatorSign = false;';
}

?>
	obj = dgi(id);
	if(type=='witness'){
		//obj = dgi('SpanWitSign'+ds);	
		hidVar = 'hidd_signReplacedWit';
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
	/*
	if(type=='witness'){
		obj = dgi('SpanWitSign'+ds);	
		hidVar = 'hidd_signReplacedWit';
	}else if(type=='physician'){
		obj = dgi('SpanPhySign'+ds);
		hidVar = 'hidd_signReplaced';
	}*/
	
	imgData +='<?php echo $sigDivData;?><input type="hidden" name="'+hidVar+ds+'" value="<?php echo urlencode($sigDivDataURL);?>">';
	//var imgData = '<?php //echo $sigDivData;?><input type="hidden" name="'+hidVar+ds+'" value="<?php //echo urlencode($sigDivDataURL);?>">';
	imgData +=endTr;
	if(OperatorSign){
		obj.innerHTML =imgData;
	}else{
		top.fAlert("Operator Signature not saved. Kindly, save your signature to use this facility.");
	}
}

function changeBackImg(id,val){
	ab = document.getElementById(id);
	if(ab.className != 'TDTabSelected' && val == ''){
		ab.className = 'TDTabHover';
	}	
	else if(ab.className != 'TDTabSelected' && val != ''){
		ab.className = 'TDTab';
	}
}

function save_form(val){
	var flag=true;
	var msg='';
	if(document.getElementById("apptIdList").value=='') {
		flag=false;	
		msg = msg+'Please Select/Create Future Appointment Date';
		document.getElementById("apptIdList").focus();
	}
	

	document.getElementById("content_save").value = val;
	if( typeof saveSig == 'function') {
		var sign = saveSig();
	} else {
		var sign = SetSig();
		var signPhy = SetSigPhy();
		var signWit = SetSigWit();
	}
	if(sign == true){ }else {flag=false;}
	
	if(flag==true && isIPad == false) {			
		document.consent_frm.submit();
	}else {
		if(msg!='') {
			top.fAlert(msg);
		}
	}
	
	if(isIPad == true){
		setCanvasImage();
		if(flag==true) {			
			document.consent_frm.submit();
		}else {
			if(msg!='') {
				top.fAlert(msg);
			}
		}
	}
}
function changeApptFun(obj) {
	document.getElementById("content_save").value = 'changeAppt';
	document.getElementById("hiddenApptIdList").value = obj.value;
	document.consent_frm.submit();	
}
function SetSig() {
  if(document.getElementById('SigPlus1').NumberOfTabletPoints == 0){
	top.fAlert("Please sign to continue");
	 return false;
  }
  else{
	  document.getElementById('SigPlus1').SigCompressionMode = 1;
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
	window.open('../patient_info/surgery_consent_forms/print_consent_form_surgery.php?consent_form_id='+show_td+'&show_td='+show_td+'&form_information_id='+information_id,'print_form','');
}

function print_pdf_old(nme){
	//window.open('../../common/<?php echo $htmlFilePth; ?>?font_size=10&page=4&<?php echo $htmlFilename; ?>='+nme,'print_form','');
}
function print_pdf(htmlFilePth,src_type,htmlFileName){
	if(src_type=='ipad'){
		window.location.href='../../../library/'+htmlFilePth+'?font_size=10&page=4';
	}else{
		//alert(htmlFilePth+'@@'+src_type+'$$'+htmlFileName);
		window.open('../../../library/'+htmlFilePth+'?font_size=10&page=4&file_location='+htmlFileName,'print_form','');
	}
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

var signImgFolderName = '<?php echo $htmlFolder;?>';
function trimNew(val){ 
	return val.replace(/^\s+|\s+$/, ''); 
}

function OnSignIpadPhyAdmin(user_id,pConfId,sigFor,idInnerHTML,signSeqNum){
	window.open("../chartNoteSignatureIPad.php?user_id="+user_id+"&pConfId="+pConfId+"&sigFor="+sigFor+"&idInnerHTML="+idInnerHTML+"&signSeqNum="+signSeqNum+"&signImgFolderName="+signImgFolderName+"&formName=surgeryConsent","chartNoteSignature");
}

function OnSignIpadPhy(patient_id,sigFor,idInnerHTML,signSeqNum){
	window.open("../../common/chartNoteSignatureIPad.php?patient_id="+patient_id+"&sigFor="+sigFor+"&idInnerHTML="+idInnerHTML+"&signSeqNum="+signSeqNum+"&signImgFolderName="+signImgFolderName+"&formName=surgeryConsent","chartNoteSignature");
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
			if(div == "ptConsentSurgery"){
				if(typeof(document.getElementById('hiddSigIpadId'+seqNum)) != "undefined") {
					document.getElementById('hiddSigIpadId'+seqNum).value=imageSrc;
				}
				if(typeof(document.getElementById(id)) != "undefined") {
					$("#"+id).html("<tr><td style='width:160px;height:90px;'><img src='"+imageSrc+"' style='width:150px; height:83px;'></td></tr>");
				}
			}else if(div == "witConsentSurgery"){
				if(typeof(document.getElementById('hiddSigIpadIdWit'+seqNum)) != "undefined") {
					document.getElementById('hiddSigIpadIdWit'+seqNum).value=imageSrc;
				}
				if(typeof(document.getElementById(id)) != "undefined") {
					$("#"+id).html("<tr><td style='width:160px;height:90px;'><img src='"+imageSrc+"' style='width:150px; height:83px;'></td></tr>");
				}
			}else if(div == "phyConsentSurgery"){
				if(typeof(document.getElementById('hiddSigIpadIdPhy'+seqNum)) != "undefined") {
					document.getElementById('hiddSigIpadIdPhy'+seqNum).value=imageSrc;
				}
				if(typeof(document.getElementById(id)) != "undefined") {
					$("#"+id).html("<tr><td style='width:160px;height:90px;'><img src='"+imageSrc+"' style='width:150px; height:83px;'></td></tr>");
				}
			}
		}
	}
}
//ipad functions	
</script>
</head>
<body onLoad="load_image();<?php print $loadFun; ?> init();" oncontextmenu="return false;"><!--ontouchmove="BlockMove(event);"-->
<div class="col-xs-12">

  <form name="consent_frm" action="" method="post" oncontextmenu="return false;">
  <input type="hidden" name="show_td" id="show_td" value="<?php print $consent_form_id; ?>" >
  <input type="hidden" name="consentFormName" id="consentFormName" value="<?php print $consent_form_name; ?>" >
  <input type="hidden" name="content_save" id="content_save" value="" >
  <input type="hidden" name="on_hold_save" id="on_hold_save" value="" >
  <input type="hidden" name="hiddenApptIdList" id="hiddenApptIdList" value="<?php echo $hiddenApptIdList;?>" >
  <input type="hidden" name="information_id" id="information_id" value="<?php print $insert_id; ?>" > 
  <input type="hidden" name="sig_count" id="sig_count" value="<?php print count($hiddenFields); ?>" >
  <input type="hidden" name="sig_count_phy" id="sig_count_phy" value="<?php print count($hiddenPhyFields); ?>" >
  <input type="hidden" name="sig_count_wit" id="sig_count_wit" value="<?php print count($hiddenWitFields); ?>" >
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
    $qry = "select surgery_consent_id,form_created_date from surgery_consent_filled_form 
        where consent_template_id = '$consent_form_id' and patient_id = '$patient_id'";
    $consentFormInfoID = get_array_records_query($qry);
    $formInfoID = "";
    $formInfoID=$consentFormInfoID[0]['surgery_consent_id'];
    $form_created_date = $consentFormInfoID[0]['form_created_date'];
    
    $qry = "select signature_content from surgery_consent_form_signature 
        where consent_template_id = '$show_td' and patient_id = '$patient_id' and surgery_consent_auto_id = $formInfoID";
    $sigContentDetail = get_array_records_query($qry);
    $sig_con = array();
    for($s=0;$s<count($sigContentDetail);$s++){
      $sig_con[] = $sigContentDetail[$s]['signature_content'];
    }
    for($h=0;$h<count($hiddenFields);$h++,$jh++){
      $hiddenField .= '<input type="hidden" name="SigData'.$jh.'" id="SigData'.$jh.'" value="'.$sig_con[$h].'">';
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
    
    if($insert_id > 0){		
      $consent_form_id = $consent_form_id;
      $form_information_id = $insert_id;
      $comeFrom = 'consentformDetailSurgery';		
      $createdDate = date('m/d/Y');
      include_once('print_consent_form_surgery.php');
      if($content_save == 'print_form'){
        if(!$pdf_htm) {
          //$pdf_htm="pdfFile_PCFS";
        }
		if($file_path){
			$scrtype="";
			if($blClientBrowserIpad==true){
				$scrtype='ipad';	
			}
			?>
			<script type="text/javascript">
				//var htmlFilePth = "<?php echo $htmlFilePth;?>";
				var scrtype = '<?php echo $scrtype; ?>';
				//print_pdf(htmlFilePth,'<?php echo $scrtype; ?>','<?php echo $html_file_name; ?>');
				if(scrtype=='ipad'){
					html_to_pdf('<?php echo $file_path; ?>','p','',true);
				}else{
					html_to_pdf('<?php echo $file_path; ?>','p');
				}
				if(top.fmain.surgery_consent_main_frm) {
					top.fmain.surgery_consent_main_frm.submit()
				}
				</script>
			<?php
		}
      }
    }
  ?>
  <div style=" width:100%; height:100%; overflow:scroll; overflow-x:hidden;">
      <table width="100%" border="0" align="right" cellpadding="0" cellspacing="0">
          <tr>
              <td height="30" class="text_10b tblBg"  width="100%" ><?php print $apptSelectList; ?></td>
          </tr>
          <tr>
              <td width="100%" valign="top">
                   <!--<div style="width:100%;height:<?php //print $_SESSION['wn_height']-305;?>px; overflow:auto;">-->
                      <?php print $consent_content; ?>
                   <!--</div>   -->
              </td>
          </tr>
      </table>
  </div>
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

  <div id="div_smart_tags_options" class="modal" role="dialog">
    <div class="modal-dialog modal-sm">
      <!-- Modal content-->
      <div class="modal-content">
      
        <div class="modal-header bg-primary">
          <button type="button" class="close" onClick="$('#div_smart_tags_options').hide();">x</button>
          <h4 class="modal-title" id="modal_title">Smart Tag Options</h4>
        </div>
        
        <div class="modal-body pd0" style="min-height:250px; max-height:400px; overflow:hidden; overflow-y:auto;">
          <div class="loader"></div>
        </div>
          
        <div class="modal-footer pd5"></div>
        
      </div>
    </div>
  </div>

</div>
<script type="text/javascript">
var smart_tag_current_object = new Object;
$(document).ready(function(){
	$('.cls_smart_tags_link').mouseup(function(e){
		if(e.button==0 || e.button==2){
			$('#smartTag_parentId').val($(this).attr('id'));
			smart_tag_current_object = $(this);
			display_tag_options(e);
		//	document.oncontextmenu="return false;"    		
		}
		
	});
});

function display_tag_options(e){
	
	$('#div_smart_tags_options').show();
	
	/*var parentId = $('#smartTag_parentId').val();
	ArrtempParentId = parentId.split('_');
	parentId = ArrtempParentId[0];*/
	
	var parentId = $('#smartTag_parentId').val();
	$.ajax({
		type: "GET",
		url: top.JS_WEB_ROOT_PATH + "/interface/chart_notes/requestHandler.php?elem_formAction=getTagOptions&id="+parentId+'&is_return=1',
		dataType:"json",
		success: function(resp){
			$('#div_smart_tags_options .modal-title').html(resp.title);
			$('#div_smart_tags_options .modal-body').html(resp.data);
			$('#div_smart_tags_options .modal-footer').html(resp.footer_btn);
		}
	});
}

function replace_tag_with_options(){
	var strToReplace = '';
	var parentId = $('#smartTag_parentId').val();
	
	var arrSubTags = document.all.chkSmartTagOptions;
	$(arrSubTags).each(function (){
		if($(this).is(':checked')){
			if(strToReplace=='')
				strToReplace +=  $(this).val();
			else
				strToReplace +=  ', '+$(this).val();
		}
	});
	
	/*--GETTING FCK EDITOR TEXT--*/
	if(strToReplace!='' && smart_tag_current_object){
		$('.cls_smart_tags_link[id="'+parentId+'"]').html(strToReplace);	
		//$(smart_tag_current_object).html(strToReplace);
		var hiddclass = $(smart_tag_current_object).attr('id');
		$('.'+hiddclass).val($(smart_tag_current_object).text());
		$('#div_smart_tags_options').hide();
		$("object").css({"visibility":"visible"});
	}else{
		top.fAlert("Select Options");
		
	}
}
</script>
</body>
</html>
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
				for(var l=0;l<tagName.length;l++){
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
				function OnSign'.$jh.'(){
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
					document.getElementById("SigPlus'.$jh.'").ClearTablet();
				}
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
							document.getElementById("tdObject'.$jh.'").className="consentObjectBeforSign";		
						}
						if(document.getElementById("SigPlusPhy'.$jh.'")) {
							document.getElementById("SigPlusPhy'.$jh.'").TabletState = 0;
							document.getElementById("tdObjectPhy'.$jh.'").className="consentObjectBeforSign";		
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

function LoadSig() {
	<?php print $displaySign; ?>
}
var sign = '<?php print $sign; ?>';
if(!sign){
	if(top.fmain.document.getElementById("sign_id"))
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
	
//myFrame = top.fmain.front;

//Btn ---
top.btn_show("SCF");
//Btn ---

</script>