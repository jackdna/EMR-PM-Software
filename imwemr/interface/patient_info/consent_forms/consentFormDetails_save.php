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
File: consentFormDetails_save.php
Purpose: Save consent forms
Access Type: Direct 
*/
include_once(dirname(__FILE__)."/../../../config/globals.php");
$library_path = $GLOBALS['webroot'].'/library';
$web_root= $GLOBALS['webroot'];
$patient_id = $_SESSION['patient'];
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
$OBJhold_sign = new CLSHoldDocument;
$obj_print_pt_key=new print_pt_key;
$OBJCommonFunction = new CLSCommonFunction;

$oSaveFile = new SaveFile($patient_id);
$oSaveFile->ptDir("consent_forms");

$oSaveFileUser = new SaveFile();

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

//function
function getSigImage($postDataWit,$pth="",$rootPath){
	global $webServerRootDirectoryName;
	$rootPath = $webServerRootDirectoryName;//THIS PARAMETER HOLD LOCAL ADDRESS OF SERVER i.e. "/var/www/html/";
	$output = shell_exec("java -cp .:".$rootPath."SigPlusLinuxHSB/SigPlus.jar:".$rootPath."SigPlusLinuxHSB/RXTXcomm.jar:".$rootPath."SigPlusLinuxHSB SigPlusImgDemoV2 ".$postDataWit." ".$rootPath."SigPlusLinuxHSB/sig.jpg 2>&1");
	@copy($rootPath."SigPlusLinuxHSB/sig.jpg",$pth);
	echo $output;
}
//functions--
function cfd_getProIdFromChart(){
	$pro_id=0;
	$sql = "SELECT providerId FROM chart_master_table where patient_id = '".$_SESSION['patient']."' AND delete_status='0' AND purge_status='0' AND date_of_service=CURDATE() ORDER BY create_dt DESC, id DESC  LIMIT 0, 1 ";
	$row=get_row_record_query($sql);
	if($row!=false){	$pro_id=$row["providerId"];}
	return $pro_id;
}
//functions--


//START CODE TO SET NEW CLASS TO CONSENT FORMS
if($_SESSION['patient']){
	$upcoming_appt=$obj_print_pt_key->getApptFuture($_SESSION['patient'],'','','n');
}
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

/*--IF PHY SIG IS AVAILABLE --*/

	//--
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
	//--	
	
/*--END OF PHY SIG IS AVAILABLE--*/


//echo $_SERVER['HTTP_USER_AGENT'];
if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true) {
	$blClientBrowserIpad = true;
}
//var_dump($blClientBrowserIpad);

/*$qryGetAuditPolicies = "select policy_status as polStSig from audit_policies where policy_id = 10";
$rsGetAuditPolicies = mysql_query($qryGetAuditPolicies);
if($rsGetAuditPolicies){
	if(mysql_num_rows($rsGetAuditPolicies)){
		extract(mysql_fetch_array($rsGetAuditPolicies));
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
1 � CHART NOTE (if exists on same date)
2 � APPOINTMENT (if exists on same date)
3 � Pt INFO
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
$qry = "select concat(LastName,', ',FirstName) as name , MiddleName, FirstName, LastName, Title, specialty, physician_phone, Address1, Address2, ZipCode, City, State from refferphysician
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
	$qryGetInsPriProvider ="SELCET 
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
$qryGetApptData = "select sa.sa_app_start_date,DATE_FORMAT(sa.sa_app_start_date, '%a ".get_sql_date_format('','y','-')."') as appDate,
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

//--- save form informations -------
$proc_site = $_REQUEST['procedure_site']; //Site value here posting from consentformdetails page for printing the worview procedure site value in consent.
$data['form_information_id'] = $information_id_2;
$data['consent_form_id'] = $show_td;
$data['patient_id'] = $patient_id;
$data['chart_procedure_id'] = $chart_procedure_id;
//if(isset($chart_procedure_timestamp) && !empty($chart_procedure_timestamp)){ $data['form_created_date'] = $chart_procedure_timestamp; }

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
	if($information_id_2){
		$qry = "select consent_form_content_data,consent_form_id from patient_consent_form_information 
			where form_information_id = '".$information_id_2."'";			
		$form_content = get_array_records_query($qry);
		$consent_form_id = stripslashes($form_content[0]['consent_form_id']);
		if($consent_form_id==$show_td){
			$consent_form_content_data = stripslashes($form_content[0]['consent_form_content_data']);
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
	if($patientDetails[0]['postal_code']) {
		$consent_form_content_data = str_ireplace('{STATE ZIP CODE}',ucwords($patientDetails[0]['state'].' '.$patientDetails[0]['postal_code']),$consent_form_content_data);
	}
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN TITLE}',		trim(ucwords($reffPhyDetail[0]['Title'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN FIRST NAME}',	trim(ucwords($reffPhyDetail[0]['FirstName'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN LAST NAME}',	trim(ucwords($reffPhyDetail[0]['LastName'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY SPECIALITY}',		trim(ucwords($reffPhyDetail[0]['specialty'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY PHONE}',			trim(ucwords($reffPhyDetail[0]['physician_phone'])),$consent_form_content_data);
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
	//
	//This site variable is replacing with procedure site value.
	$consent_form_content_data = str_ireplace('{SITE}',$proc_site,$consent_form_content_data);
		
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
	
	$data['consent_form_content_data'] = addslashes($consent_form_content_data);
	$data['form_created_date'] = date("Y-m-d H:i:s");
	
	if(empty($data['form_information_id'])){ 
		$insert_id = AddRecords($data,'patient_consent_form_information','false');
	}else{
		$insert_id = UpdateRecords($data['form_information_id'], "form_information_id", $data, 'patient_consent_form_information','false');			 
		$qry =imw_query("UPDATE consent_hold_sign set del_status=1  WHERE consent_id='".$data['form_information_id']."' AND signed='0' ORDER BY id asc");
	}
	
	/*-----SAVING HOLD SIGNATURE INFO-----*/
	 $OBJhold_sign->section_col = "consent_id";
	 $OBJhold_sign->section_col_value = $insert_id;
	 $OBJhold_sign->save_hold_sign();
	 /*-----end of HOLD SIGNATURE INFO-----------*/
	 
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
			
##audit signature save##	
$table = array("consent_form_signature");
$error = array($signatureError);
$mergedArray = merging_array($table,$error);	
if($polStSig == 1){
	auditTrail($arrAuditTrail,$mergedArray,0,0,0);
}		


/*
//checkprocedurenote --

if(!empty($pop_procedure)){	
	exit();
}

//checkprocedurenote --
*/

}

?>