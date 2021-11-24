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
File: consentFormDetails.php
Purpose: Get consent form details
Access Type: Include 
*/
require_once(dirname(__FILE__)."/../../config/globals.php");

require_once($GLOBALS['srcdir']."/classes/Mobile_Detect.php");

// Exclude tablets.
$detect = new Mobile_Detect;

if(!isset($_REQUEST['authId'])){
	if(isset($_REQUEST['phyId']) && empty($_REQUEST['phyId']) == false){
		$_REQUEST['authId'] = $_REQUEST['phyId'];
		$_REQUEST['op_id'] = $_REQUEST['phyId'];
	}
}

if(!isset($_REQUEST['patient'])){
	if(isset($_REQUEST['patId']) && empty($_REQUEST['patId']) == false){
		$_REQUEST['patient'] = $_REQUEST['patId'];
	}else{
		$_REQUEST['patient'] = $_REQUEST['pt_id'];
	}
}

$this_device = "frontend";
if( $detect->isMobile() && !$detect->isTablet() ){
//if( $detect->isMobile()  || $detect->isTablet() ){
$this_device = "mobile";
}
if(isset($_GET["mode"]) && $_GET["mode"] == "mobile"){
$this_device = "mobile"; //testing to be commented later on
}

function get_smartTags_array($id=0){
	$query = "SELECT id, tagname FROM smart_tags WHERE under=".intval($id)." AND status=1";
	$result = imw_query($query);
	if($result && imw_num_rows($result)>0){
		$arrResult = array();
		while($rs = imw_fetch_assoc($result)){
			$id = $rs['id'];
			$tagname = $rs['tagname'];
			$arrResult[$id] = $tagname;
		}
		return $arrResult;		
	}else{
		return false;
	}
}

function getPtReleaseInfoNames($ptid){
	$html = '';
	$q = "SELECT relInfoName1, relInfoPhone1, relInfoReletion1, relInfoName2, relInfoPhone2, relInfoReletion2, relInfoName3, relInfoPhone3, relInfoReletion3, relInfoName4, relInfoPhone4, relInfoReletion4 FROM patient_data WHERE pid='$ptid' LIMIT 0,1";
	$res= imw_query($q);
	if($res && imw_num_rows($res)>0){
		$rs = imw_fetch_assoc($res);
		$innerhtml = '';
		for($i=1; $i<5; $i++){
			if(trim($rs['relInfoName'.$i])!=''){
				$innerhtml .= '<tr><td>'.$rs['relInfoName'.$i].'</td><td>'.$rs['relInfoPhone'.$i].'</td><td>'.$rs['relInfoReletion'.$i].'</td></tr>';
			}
		}
		if($innerhtml != ''){
			$html = '<table width="100%">'.$innerhtml.'</table>';
		}
	}
	return $html;
}

/* function logged_in_facility_info($loggedinFacId){
	$loggedinFacInfo="";
	$qry = imw_query("SELECT name, street, city, state, postal_code, zip_ext FROM `facility` WHERE id='".$loggedinFacId."'");
	if(imw_num_rows($qry)>0){
		$qryRes 		=  imw_fetch_assoc($qry);
		$facName		=  $qryRes['name'];
		$facStreet  	=  $qryRes['street'];
		$facCity 		=  $qryRes['city'];
		$facState		=  $qryRes['state'];
		$facPostal_code =  $qryRes['postal_code'];
		$facZip_ext 	=  $qryRes['zip_ext'];
	}
	$loggedinFacInfo = array($facName,$facStreet,$facCity,$facState,$facPostal_code,$facZip_ext);
	return $loggedinFacInfo;
} */


//-----  Get data from remote server -------------------

/* $zRemotePageName = "patient_info/consent_forms/consentFormDetails";
if(constant("REMOTE_SYNC") == 1) {
	require(dirname(__FILE__)."/../../chart_notes/get_chart_from_remote_server.inc.php");
} */

//-----  Get data from remote server -------------------
/*
require_once(dirname(__FILE__)."/../../main/Functions.php");
require_once(dirname(__FILE__)."/../../common/CLSCommonFunction.php");
require_once(dirname(__FILE__)."/../../common/audit_common_function.php");
require_once(dirname(__FILE__)."/../../main/main_functions.php");
include_once(dirname(__FILE__)."/../../admin/documents/smart_tags/functions.smart_tags.php");
include_once(dirname(__FILE__)."/../../common/CLSHoldDocument.php");
include(dirname(__FILE__).'/../../../library/bar_code/code128/code128.class.php');


$OBJsmart_tags = new SmartTags;
$OBJCommonFunction = new CLSCommonFunction;
$OBJhold_sign = new CLSHoldDocument;
*/

include_once(dirname(__FILE__)."/../../library/work_view/PnTempParser.php");
include_once(dirname(__FILE__)."/../../library/classes/cls_common_function.php");


$blClientBrowserIpad = false;
$new_manageData = new CLSCommonFunction;

$package_category_id = $_REQUEST["package_category_id"];


$objParser = new PnTempParser;
$medHx=$objParser->getMedHx_public($_REQUEST['patient']);
$ocularMed=$objParser->get_med_list($_REQUEST['patient'],4);
$systemicMed=$objParser->get_med_list($_REQUEST['patient'],1);
$allergyList=$objParser->get_med_list($_SESSION['patient'],7);
$form_id=$_REQUEST['form_id'];
$ins_cases=$objParser->all_ins_case($_REQUEST['patient']);
if(!$form_id){
	$qry_form_id="Select id from chart_master_table where patient_id='".$_REQUEST['patient']."'  order by id desc";
	$res_form_id=imw_query($qry_form_id)or die(imw_error().' 62');
	$row_form_id=imw_fetch_assoc($res_form_id);
	$form_id=$row_form_id['id'];
}
//-------------signature blank img creation starts here------------




//delete tmp folder for target operator
/* $filePath = dirname(__FILE__)."/../signature/tmp/".$_REQUEST['op_id'].$addition_path;
if (is_dir($filePath)) {
	delete($filePath);
}//clear dir for that operator
mkdir($filePath, 0777); */

//delete tmp folder for target operator
$filePath = data_path()."app_services/signature/tmp/".$_REQUEST['op_id'].$addition_path;
if (is_dir($filePath)) {
	delete($filePath);
}//clear dir for that operator
mkdir($filePath, 0777, true);


//===============LOGGED IN FACILITY INFO WORKS WORKS STARTS HERE=========================
$loggedfacCity = $loggedfacState = $loggedfacCountry = $loggedfacPostalcode = $loggedfacExt = "";
$loggedfacilityInfoArr 	= logged_in_facility_info($_SESSION['login_facility']);
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



//-------------signature blank img creation ends here------------

$cc_hx=$objParser->get_cc_hx($_REQUEST['patient'],$form_id);
$cc_val=$cc_hx[1];
$hx_val=$cc_hx[2];
//START CODE TO SET NEW CLASS TO CONSENT FORMS
if($_REQUEST['patient']){
	$upcoming_appt=$new_manageData->__getApptFuture($_REQUEST['patient'],'','','n');
}
//START CODE TO SET NEW CLASS TO CONSENT FORMS
$htmlFolder = "html2pdf";
$htmlV2Class = false;
$htmlFilePth = "html2pdf/index.php";
if(constant("CONSENT_FORM_VERSION")=="consent_v2") {
	$htmlFolder = "new_html2pdf";
	$htmlV2Class=true;	
	$htmlFilePth = "new_html2pdf/createPdf.php";
}
//END CODE TO SET NEW CLASS TO CONSENT FORMS

/*--IF PHY SIG IS AVAILABLE --*/

	//--
	$id = $_REQUEST['authId'];
	$chkSignQry = "SELECT sign, sign_path FROM users WHERE id='".$_REQUEST['authId']."'";
	$chkSignRes = imw_query($chkSignQry)or die(imw_error().' 89');
	$chkSignRow = imw_fetch_array($chkSignRes);
	$elem_sign = $chkSignRow["sign"];
	$elem_sign_path = $chkSignRow["sign_path"];
	$updir=$GLOBALS['incdir']."/main/uploaddir";
	if((!empty($elem_sign_path) && file_exists($updir.$elem_sign_path))) {		
		
		$sigDivDataURL_tmp = "common/".$htmlFolder."/user_id_".$id.".jpg";		
	
		if(copy($updir.$elem_sign_path, $GLOBALS['incdir']."/".$sigDivDataURL_tmp)){		
			$sigDivDataURL=$sigDivDataURL_tmp;
			$sigDivData = '<img src="'.$web_root.'/interface/'.$sigDivDataURL.'" height="45" width="225">';
		}else{
			//exit("Copy Failed! ".$updir.$elem_sign_path." --- ".$GLOBALS['incdir']."/".$sigDivDataURL_tmp);
		}
		
	}else if((!empty($elem_sign) && $elem_sign != "0-0-0:;")) {		
		
		$tblName = "users";
		$pixelFieldName = "sign";
		$idFieldName = "id";
		$imgPath = "";
		$saveImg = dirname(__FILE__)."/../../common/".$htmlFolder."/user_id_".$id.".jpg";
		$imgNme = dirname(__FILE__)."/../../common/new_html2pdf/user_id_".$id.".jpg";
		include(dirname(__FILE__)."/../../main/imgGd.php");
		$sigDivDataURL = "common/".$htmlFolder."/user_id_".$id.".jpg";
		$sigDivData = '<img src="'.$web_root.'/interface/'.$sigDivDataURL.'" height="45" width="225">';				
	}
	
	//--	
	
/*--END OF PHY SIG IS AVAILABLE--*/

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
	
$patient_id = $_REQUEST['patient'];
$opt_id = $_REQUEST['authId'];
if($ver_option){
	$consent_form_id = $ver_option;
}
//--- get operator details --------
$qry = "select * from users where id = '$opt_id'";
$operatorDetails = sqlQuery($qry);
$operator_name = $operatorDetails['lname'].', ';
$operator_name .= $operatorDetails['fname'].' ';
$operator_name .= $operatorDetails['mname'];
$operator_initial = substr($operatorDetails['fname'],0,1);
$operator_initial .= substr($operatorDetails['lname'],0,1);

//---- get Patient details ---------
$qry = "select *,date_format(DOB,'".get_sql_date_format()."') as pat_dob,date_format(date,'".get_sql_date_format()."') as reg_date
		from patient_data where id = '$patient_id'";
$patientDetails = sqlQuery($qry);
$patient_initial = substr($patientDetails['fname'],0,1);
$patient_initial .= substr($patientDetails['lname'],0,1);
list($year, $month, $day) = explode('-',$patientDetails['DOB']);
$pat_date = $year."-".$month."-".$day;
$patient_age = get_age($pat_date);	

//--- get physician name --------
$pro_id = $patientDetails['providerID'];
if($pro_id=="" || $pro_id==0){
	$qry_pro_id="select sa_doctor_id from schedule_appointments
				where sa_patient_app_status_id not in (201, 18, 203, 19, 20)
				and sa_patient_id = '".$patient_id."' and sa_app_start_date <= now()
				order by sa_app_start_date desc, sa_app_starttime desc limit 0, 1";
	$res_pro_id=imw_query($qry_pro_id)or die(imw_error().' 183');
	$row_pro_id=imw_fetch_assoc($res_pro_id);	
	$pro_id=$row_pro_id['sa_doctor_id'];
}
$qry = "select concat(lname,', ',fname) as name,mname,fname,lname,pro_title,pro_suffix from users where id = '$pro_id'";
$phyDetail = sqlQuery($qry);
$phy_name = ucwords(trim($phyDetail['name'].' '.$phyDetail['mname']));
$phy_fname = ucwords(trim($phyDetail['fname']));
$phy_mname = ucwords(trim($phyDetail['mname']));
$phy_lname = ucwords(trim($phyDetail['lname']));
$phy_name_suffix = ucwords(trim($phyDetail['pro_suffix']));


//--- get reffering physician name --------
$primary_care_id = $patientDetails['primary_care_id'];
$qry = "select concat(LastName,', ',FirstName) as name , MiddleName, FirstName, LastName, Title, specialty, physician_phone, Address1, Address2, ZipCode, City, State from refferphysician
		where physician_Reffer_id = '$primary_care_id'";
$reffPhyDetail = sqlQuery($qry);
$reffer_name = ucwords(trim($reffPhyDetail['name'].' '.$reffPhyDetail['MiddleName']));
$refPhyAddress="";
$refPhyAddress .= (!empty($reffPhyDetail['Address1'])) ? trim($reffPhyDetail['Address1']) : "";
$refPhyAddress .= (!empty($reffPhyDetail['Address2'])) ? "<br>".trim($reffPhyDetail['Address2']) : "";

//--- get primary care physician detail --------
$pcp_id = $patientDetails['primary_care_phy_id'];
$qry = "select pcp.Title as pcpTitle,pcp.FirstName as pcpFName,pcp.MiddleName as pcpMName,pcp.LastName as pcpLName, 
		pcp.Address1 as pcpAddress1,pcp.Address2 as pcpAddress2,pcp.City as pcpCity,pcp.State as pcpState,pcp.ZipCode as pcpZipCode
		 from refferphysician pcp
		 where pcp.physician_Reffer_id = '".$pcp_id."'";
$pcpPhyDetail = sqlQuery($qry);
$pcpAddress=$pcpName="";
$pcpName=$pcpPhyDetail['pcpLName'].", ".$pcpPhyDetail['pcpFName']." ".$pcpPhyDetail['pcpMName'];
$pcpAddress .= (!empty($pcpPhyDetail['pcpAddress1'])) ? trim($pcpPhyDetail['pcpAddress1']) : "";
$pcpAddress .= (!empty($pcpPhyDetail['pcpAddress2'])) ? "<br>".trim($pcpPhyDetail['pcpAddress2']) : "";

//--- get pos facility name -------
$default_facility = $patientDetails['default_facility'];

$qry = "select facilityPracCode from pos_facilityies_tbl 
		where pos_facility_id = '$default_facility'";
$posFacilityDetail = sqlQuery($qry);
$pos_facility_name = $posFacilityDetail['facilityPracCode'];
//--- get responsible party information ------
$qry = "select *,date_format(dob,'".get_sql_date_format()."') as res_dob 
		from resp_party where patient_id = '$patient_id'";
$resDetails = sqlQuery($qry);
//--- get epmoyee detail of patient ---
$qry = "select * from employer_data where pid = '$patient_id'";
$empDetails = sqlQuery($qry);
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
					 where ind.pid = '".$_REQUEST['patient']."' and 
					 ind.ins_caseid > 0 and
					 ind.type = 'primary' 
					 and ind.actInsComp = 1
					 and ind.provider > 0 ORDER BY ict.normal DESC LIMIT 0,1
					 ";
$rsGetInsPriData = imw_query($qryGetInsPriData)or die(imw_error().' 256');
$numRowGetInsPriData = imw_num_rows($rsGetInsPriData);
if($numRowGetInsPriData>0){
	extract(imw_fetch_array($rsGetInsPriData));
	$qryGetInsPriProvider = "select name as priInsCompName, 
							 CONCAT(contact_address,if(TRIM(city)!='',CONCAT(' ',city),''),if(TRIM(State)!='',CONCAT(', ',State),''),if(TRIM(Zip)!='',CONCAT(' ',Zip),''),if(TRIM(zip_ext)!='',CONCAT('-',zip_ext),'')) AS priInsCompAddress 
							 from insurance_companies where id = $priInsComp";
	$rsGetInsPriProvider = imw_query($qryGetInsPriProvider)or die(imw_error().' 263');
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
					 where ind.pid = '".$_REQUEST['patient']."' and 
					 ind.ins_caseid > 0 and
					 ind.type = 'secondary' 
					 and ind.actInsComp = 1
					 and ind.provider > 0 ORDER BY ict.normal DESC LIMIT 0,1
					 ";
$rsGetInsSecData = imw_query($qryGetInsSecData)or die(imw_error().' 294');
$numRowGetInsSecData = imw_num_rows($rsGetInsSecData);
if($numRowGetInsSecData>0){
	extract(imw_fetch_array($rsGetInsSecData));
	$qryGetInsSecProvider = "select name as secInsCompName,
							 CONCAT(contact_address,if(TRIM(city)!='',CONCAT(' ',city),''),if(TRIM(State)!='',CONCAT(', ',State),''),if(TRIM(Zip)!='',CONCAT(' ',Zip),''),if(TRIM(zip_ext)!='',CONCAT('-',zip_ext),'')) AS secInsCompAddress  
							 from insurance_companies where id = $secInsComp";
	$rsGetInsSecProvider = imw_query($qryGetInsSecProvider)or die(imw_error().' 301');
	$numRowGetInsSecProvider = imw_num_rows($rsGetInsSecProvider);
	if($numRowGetInsSecProvider>0){
		extract(imw_fetch_array($rsGetInsSecProvider));
	}
}
//end get Secondary insurence data//

//get patient Appointment data//
$qryGetApptData = "select sa.sa_app_start_date,DATE_FORMAT(sa.sa_app_start_date, '%a ".get_sql_date_format('','y','/')."') as appDate,
					 TIME_FORMAT(sa.sa_app_starttime,'%h:%i %p') as appTime,
					 sp.proc as ptProc
					 from schedule_appointments sa 
					 INNER JOIN slot_procedures sp ON sp.id = sa.procedureid  
					 where sa.sa_patient_id = '".$_REQUEST['patient']."' and 
					 sa.sa_app_start_date <= current_date() 
					 order by sa.sa_app_start_date DESC 
					 LIMIT 1 
					 ";
$rsGetApptData = imw_query($qryGetApptData)or die(imw_error().' 320');
$numRowGetApptData = imw_num_rows($rsGetApptData);
if($numRowGetApptData>0){
	extract(imw_fetch_array($rsGetApptData));	
}
//end get patient Appointment data//

//--- save form informations -------
$data['consent_form_id'] = $_REQUEST['consent_id'];
$data['patient_id'] = $patient_id;
$data['patient_signature'] = $_POST['patient_signature_0_'.$_REQUEST['consent_id']];
$data['witness_signature'] = $_POST['witness_signature_1_'.$_REQUEST['consent_id']];
$data['operator_id'] = $_REQUEST['authId'];
$qry = "select consent_form_name from consent_form where consent_form_id = '$_REQUEST[consent_id]'";
$res = sqlQuery($qry);
$consent_form_name = $res[0]['consent_form_name'];
$data['consent_form_name'] = $consent_form_name;

//echo 'after save this code will run and save code';
	$qry = "select consent_form_content from consent_form
			where consent_form_id = '$_REQUEST[consent_id]'";			
	$form_content = sqlQuery($qry);
	$consent_form_content_data = stripslashes($form_content['consent_form_content']);
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
					$repVal .= $arrExp[$p].inputBox($arrStr[$j],$name.$c,htmlentities($_POST[$name.$c]),$size);
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
					$repVal .= $arrExp[$p].inputBox($arrStr[$j],$name.$c,htmlentities($_POST[$name.$c]),$size);
					$c++;
				}
				$repVal .= end($arrExp);
				$consent_form_content_data = $repVal;
			}
		}
		else
		{
			if($arrStr[$j] == '{TEXTBOX_XSMALL}') {
				$repVal = str_ireplace($arrStr[$j],inputBox($arrStr[$j],$name,htmlentities($_POST[$name])),$consent_form_content_data,$size);
				$consent_form_content_data = $repVal;
			}else if($arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}')
			{
				$repVal = str_ireplace($arrStr[$j],inputBox($arrStr[$j],$name,htmlentities($_POST[$name])),$consent_form_content_data,$size);
				$consent_form_content_data = $repVal;
			}
			else if($arrStr[$j] == '{TEXTBOX_LARGE}')
			{
				$repVal = str_ireplace($arrStr[$j],inputBox($arrStr[$j],$name,htmlentities($_POST[$name])),$consent_form_content_data,$size);
				$consent_form_content_data = $repVal;
			}
		}
		 
	}
	
/*--REPLACING SMART TAGS (IF FOUND) WITH LINKS--*/
	$arr_smartTags = get_smartTags_array();
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
	if($blClientBrowserIpad == true) {//IPAD SIGNATURE
		
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
				$hidd_signReplacedWit = "interface/".$hidd_signReplacedWit;
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
				$hidd_signReplacedPhy = "interface/".$hidd_signReplacedPhy;
				$consent_form_content_data .= '<img src="'.$hidd_signReplacedPhy.'" width="150" height="83">';	
			}else {
				$consent_form_content_data .= '{PHYSICIAN SIGNATURE}';
				
			}
			
			$consent_form_content_data .= $row_arr[$c];
		}
		
	
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
	}
	
	//--- change value between curly brackets -------	
	$consent_form_content_data = str_ireplace('{PATIENT NAME TITLE}',ucwords($patientDetails['title']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT FIRST NAME}',ucwords($patientDetails['fname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{MIDDLE NAME}',ucwords($patientDetails['mname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{LAST NAME}',ucwords($patientDetails['lname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SEX}',ucwords($patientDetails['sex']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{DOB}',ucwords($patientDetails['pat_dob']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{AGE}',$patient_age,$consent_form_content_data);	
	$consent_form_content_data = str_ireplace('{PATIENT SS}',ucwords($patientDetails['ss']),$consent_form_content_data);
	//=============START WORK TO SHOW THE LAST 4 DIGIT PATIENT SS==========================
	if($patientDetails['ss']!=''){
		$consent_form_content_data = str_ireplace('{PATIENT_SS4}',ucwords(substr_replace($patientDetails['ss'],'XXX-XX',0,6)),$consent_form_content_data);
	}else{
		$consent_form_content_data = str_ireplace('{PATIENT_SS4}','',$consent_form_content_data);
	}
	//===========================END WORK===================================================
	$consent_form_content_data = str_ireplace('{PHYSICIAN NAME}',ucwords($phy_name),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PHYSICIAN FIRST NAME}',ucwords($phy_fname),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PHYSICIAN MIDDLE NAME}',ucwords($phy_mname),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PHYSICIAN LAST NAME}',ucwords($phy_lname),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PHYSICIAN NAME SUFFIX}',ucwords($phy_name_suffix),$consent_form_content_data);
	
	$consent_form_content_data = str_ireplace('{MARITAL STATUS}',ucwords($patientDetails['status']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{ADDRESS1}',ucwords($patientDetails['street']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{ADDRESS2}',ucwords($patientDetails['street2']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{HOME PHONE}',ucwords($patientDetails['phone_home']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{EMERGENCY CONTACT}',ucwords($patientDetails['contact_relationship']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{EMERGENCY CONTACT PH}',ucwords($patientDetails['phone_contact']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{MOBILE PHONE}',ucwords($patientDetails['phone_cell']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{WORK PHONE}',ucwords($patientDetails['phone_biz']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT CITY}',ucwords($patientDetails['city']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT STATE}',ucwords($patientDetails['state']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT ZIP}',ucwords($patientDetails['postal_code']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REGISTRATION DATE}',ucwords($patientDetails['reg_date']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REFFERING PHY.}',ucwords($reffer_name),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{POS FACILITY}',ucwords($pos_facility_name),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{DRIVING LICENSE}',ucwords($patientDetails['driving_licence']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{HEARD ABOUT US}',ucwords($patientDetails['heard_abt_us']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{HEARD ABOUT US DETAIL}',$patientDetails['heard_abt_desc'],$consent_form_content_data);
	
	$consent_form_content_data = str_ireplace('{EMAIL ADDRESS}',$patientDetails['email'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{USER DEFINE 1}',$patientDetails['genericval1'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{USER DEFINE 2}',$patientDetails['genericval2'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT MRN}',$patientDetails['External_MRN_1'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT MRN2}',$patientDetails['External_MRN_2'],$consent_form_content_data);
	
	$languageShow 			   = str_ireplace("Other -- ","",$patientDetails['language']);
	$raceShow				   = trim($patientDetails["race"]);
	$otherRace				   = trim($patientDetails["otherRace"]);
	if($otherRace) { 
		$raceShow			   = $otherRace;
	}
	$ethnicityShow			   = trim($patientDetails["ethnicity"]);			
	$otherEthnicity			   = trim($patientDetails["otherEthnicity"]);
	if($otherEthnicity) { 
		$ethnicityShow		   = $otherEthnicity;
	}

	$consent_form_content_data = str_ireplace('{RACE}',$raceShow,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{LANGUAGE}',$languageShow,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{ETHNICITY}',$ethnicityShow,$consent_form_content_data);
	
	
	$consent_form_content_data = str_ireplace('{REL INFO}',getPtReleaseInfoNames($patient_id),$consent_form_content_data);
	//new variable added
	if($patientDetails['postal_code']) {
		$consent_form_content_data = str_ireplace('{STATE ZIP CODE}',ucwords($patientDetails['state'].' '.$patientDetails['postal_code']),$consent_form_content_data);
	}
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN TITLE}',		trim(ucwords($reffPhyDetail['Title'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN FIRST NAME}',	trim(ucwords($reffPhyDetail['FirstName'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN LAST NAME}',	trim(ucwords($reffPhyDetail['LastName'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY SPECIALITY}',		trim(ucwords($reffPhyDetail['specialty'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY PHONE}',			trim(ucwords($reffPhyDetail['physician_phone'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY STREET ADDR}',		$refPhyAddress,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY CITY}',				trim(ucwords($reffPhyDetail['City'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY STATE}',			trim(ucwords($reffPhyDetail['State'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY ZIP}',				trim(ucwords($reffPhyDetail['ZipCode'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP NAME}',$pcpName,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP STREET ADDR}',$pcpAddress,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP City}',	$pcpPhyDetail['pcpCity'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP State}',$pcpPhyDetail['pcpState'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP ZIP}',	$pcpPhyDetail['pcpZipCode'],$consent_form_content_data);	
	
	//=============RESPONSIBLE PARTY DATA REPLACEMENT-I======================================================
	//=============NOW IF PATIENT HAVE NO RESPONSILE PERSON THEN PATIENT DATA WILL BE REPLACED.=============
	if(count($resDetails)>0){
	$consent_form_content_data = str_ireplace('{RES.PARTY TITLE}',ucwords($resDetails['title']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($resDetails['fname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{RES.PARTY MIDDLE NAME}',ucwords($resDetails['mname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{RES.PARTY LAST NAME}',ucwords($resDetails['lname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{RES.PARTY DOB}',ucwords($resDetails['res_dob']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{RES.PARTY SS}',ucwords($resDetails['ss']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{RES.PARTY SEX}',ucwords($resDetails['sex']),$consent_form_content_data);
	$strToShowRelation = $resDetails['relation'];
	if(strtolower($resDetails['relation']) == "doughter"){
		$strToShowRelation = "Daughter";
	}
	$consent_form_content_data = str_ireplace('{RES.PARTY RELATION}',ucwords($strToShowRelation),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS1}',ucwords($resDetails['address']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS2}',ucwords($resDetails['address2']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{RES.PARTY HOME PH.}',ucwords($resDetails['home_ph']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{RES.PARTY WORK PH.}',ucwords($resDetails['work_ph']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{RES.PARTY MOBILE PH.}',ucwords($resDetails['mobile']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{RES.PARTY CITY}',ucwords($resDetails['city']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{RES.PARTY STATE}',ucwords($resDetails['state']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{RES.PARTY ZIP}',ucwords($resDetails['zip']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{RES.PARTY MARITAL STATUS}',ucwords($resDetails['marital']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{RES.PARTY DD NUMBER}',ucwords($resDetails['licence']),$consent_form_content_data);
	}else{
	
		$consent_form_content_data = str_ireplace('{RES.PARTY TITLE}',ucwords($patientDetails['title']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($patientDetails['fname']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY MIDDLE NAME}',ucwords($patientDetails['mname']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY LAST NAME}',ucwords($patientDetails['lname']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY DOB}',ucwords($patientDetails['pat_dob']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY SS}',ucwords($patientDetails['ss']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY SEX}',ucwords($patientDetails['sex']),$consent_form_content_data);
	    $consent_form_content_data = str_ireplace('{RES.PARTY RELATION}','Self',$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS1}',ucwords($patientDetails['street']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS2}',ucwords($patientDetails['street2']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY HOME PH.}',ucwords($patientDetails['phone_home']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY WORK PH.}',ucwords($patientDetails['phone_biz']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY MOBILE PH.}',ucwords($patientDetails['phone_cell']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY CITY}',ucwords($patientDetails['city']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY STATE}',ucwords($patientDetails['state']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY ZIP}',ucwords($patientDetails['postal_code']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY MARITAL STATUS}',ucwords($patientDetails['status']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY DD NUMBER}',ucwords($patientDetails['driving_licence'])	,$consent_form_content_data);
	}	  
	//=====================================THE END REPONSIBLE PARTY DATA-I========================================	
	//--- change epmoyee detail of patient ---
	$consent_form_content_data = str_ireplace('{PATIENT OCCUPATION}',ucwords($patientDetails['occupation']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT EMPLOYER}',ucwords($empDetails['name']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION ADDRESS1}',ucwords($empDetails['street']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION ADDRESS2}',ucwords($empDetails['street2']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION CITY}',ucwords($empDetails['city']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION STATE}',ucwords($empDetails['state']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION ZIP}',ucwords($empDetails['postal_code']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{MONTHLY INCOME}','$'.number_format($patientDetails['monthly_income'],2),$consent_form_content_data);
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
	$consent_form_content_data = str_ireplace('{PatientID}',$_REQUEST['patient'],$consent_form_content_data);
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
	//
	
	
	$data['consent_form_content_data'] 	= addslashes($consent_form_content_data);	
	$data['package_category_id'] 		= addslashes($package_category_id);	
	$data['form_created_date']=date("Y-m-d H:i:s");
	// $insert_id = ManageData::addRecords($data,'patient_consent_form_information');

		
//---- get consent forms -------
$qry = "select consent_form_content_data as consent_form_content,
		form_information_id 
		from patient_consent_form_information
		where consent_form_id = '$_REQUEST[consent_id]' and patient_id = '$patient_id' ORDER BY form_created_date desc LIMIT 1";
$consentDetail = sqlQuery($qry);
if(count($consentDetail) == 0){
	$qry = "select *,consent_form_id as form_information_id 
			from consent_form where consent_form_id = '$_REQUEST[consent_id]'";
	$consentDetail = sqlQuery($qry);
	$patientSign = false;
}
else{
	$patientSign = true;
}

//for($i=0;$i<count($consentDetail);$i++){
	$consentDetail['consent_form_content'] = stripslashes($consentDetail['consent_form_content']);
	//--- change value between curly brackets -------	
	$consent_form_content = str_ireplace('{PATIENT NAME TITLE}',ucwords($patientDetails['title']),$consentDetail['consent_form_content']);
	$consent_form_content = str_ireplace('{PATIENT FIRST NAME}',ucwords($patientDetails['fname']),$consent_form_content);
	$consent_form_content = str_ireplace('{MIDDLE NAME}',ucwords($patientDetails['mname']),$consent_form_content);
	$consent_form_content = str_ireplace('{LAST NAME}',ucwords($patientDetails['lname']),$consent_form_content);
	$consent_form_content = str_ireplace('{SEX}',ucwords($patientDetails['sex']),$consent_form_content);
	$consent_form_content = str_ireplace('{DOB}',ucwords($patientDetails['pat_dob']),$consent_form_content);
	$consent_form_content = str_ireplace('{AGE}',$patient_age,$consent_form_content);	
	$consent_form_content = str_ireplace('{PATIENT SS}',ucwords($patientDetails['ss']),$consent_form_content);
	//=============START WORK TO SHOW THE LAST 4 DIGIT PATIENT SS==========================
	if($patientDetails['ss']!=''){
		$consent_form_content = str_ireplace('{PATIENT_SS4}',ucwords(substr_replace($patientDetails['ss'],'XXX-XX',0,6)),$consent_form_content);
	}else{
		$consent_form_content = str_ireplace('{PATIENT_SS4}','',$consent_form_content);
	}
	//===========================END WORK===================================================
	$consent_form_content = str_ireplace('{PHYSICIAN NAME}',ucwords($phy_name),$consent_form_content);
	$consent_form_content = str_ireplace('{PHYSICIAN FIRST NAME}',ucwords($phy_fname),$consent_form_content);
	$consent_form_content = str_ireplace('{PHYSICIAN MIDDLE NAME}',ucwords($phy_mname),$consent_form_content);
	$consent_form_content = str_ireplace('{PHYSICIAN LAST NAME}',ucwords($phy_lname),$consent_form_content);
	$consent_form_content = str_ireplace('{PHYSICIAN NAME SUFFIX}',ucwords($phy_name_suffix),$consent_form_content);
	$consent_form_content = str_ireplace('{MARITAL STATUS}',ucwords($patientDetails['status']),$consent_form_content);
	$consent_form_content = str_ireplace('{ADDRESS1}',ucwords($patientDetails['street']),$consent_form_content);
	$consent_form_content = str_ireplace('{ADDRESS2}',ucwords($patientDetails['street2']),$consent_form_content);
	$consent_form_content = str_ireplace('{HOME PHONE}',ucwords($patientDetails['phone_home']),$consent_form_content);
	$consent_form_content = str_ireplace('{EMERGENCY CONTACT}',ucwords($patientDetails['contact_relationship']),$consent_form_content);
	$consent_form_content = str_ireplace('{EMERGENCY CONTACT PH}',ucwords($patientDetails['phone_contact']),$consent_form_content);
	$consent_form_content = str_ireplace('{MOBILE PHONE}',ucwords($patientDetails['phone_cell']),$consent_form_content);
	$consent_form_content = str_ireplace('{WORK PHONE}',ucwords($patientDetails['phone_biz']),$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT CITY}',ucwords($patientDetails['city']),$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT STATE}',ucwords($patientDetails['state']),$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT ZIP}',ucwords($patientDetails['postal_code']),$consent_form_content);
	$consent_form_content = str_ireplace('{REGISTRATION DATE}',ucwords($patientDetails['reg_date']),$consent_form_content);
	$consent_form_content = str_ireplace('{REFFERING PHY.}',ucwords($reffer_name),$consent_form_content);
	$consent_form_content = str_ireplace('{POS FACILITY}',ucwords($pos_facility_name),$consent_form_content);
	$consent_form_content = str_ireplace('{DRIVING LICENSE}',ucwords($patientDetails['driving_licence']),$consent_form_content);
	$consent_form_content = str_ireplace('{HEARD ABOUT US}',ucwords($patientDetails['heard_abt_us']),$consent_form_content);
	$consent_form_content = str_ireplace('{HEARD ABOUT US DETAIL}',$patientDetails['heard_abt_desc'],$consent_form_content);
	
	$consent_form_content = str_ireplace('{EMAIL ADDRESS}',$patientDetails['email'],$consent_form_content);
	$consent_form_content = str_ireplace('{USER DEFINE 1}',$patientDetails['genericval1'],$consent_form_content);
	$consent_form_content = str_ireplace('{USER DEFINE 2}',$patientDetails['genericval2'],$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT MRN}',$patientDetails['External_MRN_1'],$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT MRN2}',$patientDetails['External_MRN_2'],$consent_form_content);
	
	$languageShow 		  = str_ireplace("Other -- ","",$patientDetails['language']);
	$raceShow			  = trim($patientDetails["race"]);
	$otherRace			  = trim($patientDetails["otherRace"]);
	if($otherRace) { 
		$raceShow		  = $otherRace;
	}
	$ethnicityShow		  = trim($patientDetails["ethnicity"]);			
	$otherEthnicity		  = trim($patientDetails["otherEthnicity"]);
	if($otherEthnicity) { 
		$ethnicityShow	  = $otherEthnicity;
	}
	$consent_form_content = str_ireplace('{RACE}',$raceShow,$consent_form_content);
	$consent_form_content = str_ireplace('{LANGUAGE}',$languageShow,$consent_form_content);
	$consent_form_content = str_ireplace('{ETHNICITY}',$ethnicityShow,$consent_form_content);
	

	//new variable added
	if($patientDetails['postal_code']) {
		$consent_form_content = str_ireplace('{STATE ZIP CODE}',ucwords($patientDetails['state'].' '.$patientDetails['postal_code']),$consent_form_content);
	}
	$consent_form_content = str_ireplace('{REF PHYSICIAN TITLE}',		trim(ucwords($reffPhyDetail['Title'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHYSICIAN FIRST NAME}',	trim(ucwords($reffPhyDetail['FirstName'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHYSICIAN LAST NAME}',	trim(ucwords($reffPhyDetail['LastName'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY SPECIALITY}',		trim(ucwords($reffPhyDetail['specialty'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY PHONE}',			trim(ucwords($reffPhyDetail['physician_phone'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY STREET ADDR}',		$refPhyAddress,$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY CITY}',				trim(ucwords($reffPhyDetail['City'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY STATE}',			trim(ucwords($reffPhyDetail['State'])),$consent_form_content);
	$consent_form_content = str_ireplace('{REF PHY ZIP}',				trim(ucwords($reffPhyDetail['ZipCode'])),$consent_form_content);
	$consent_form_content = str_ireplace('{PCP NAME}',$pcpName,$consent_form_content);
	$consent_form_content = str_ireplace('{PCP STREET ADDR}',$pcpAddress,$consent_form_content);
	$consent_form_content = str_ireplace('{PCP City}',	$pcpPhyDetail['pcpCity'],$consent_form_content);
	$consent_form_content = str_ireplace('{PCP State}',$pcpPhyDetail['pcpState'],$consent_form_content);
	$consent_form_content = str_ireplace('{PCP ZIP}',	$pcpPhyDetail['pcpZipCode'],$consent_form_content);	
	
	//=============RESPONSIBLE PARTY DATA REPLACEMENT-II======================================================
	//=============NOW IF PATIENT HAVE NO RESPONSILE PERSON THEN PATIENT DATA WILL BE REPLACED.=============
	if(count($resDetails)>0){	
		$consent_form_content = str_ireplace('{RES.PARTY TITLE}',ucwords($resDetails['title']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($resDetails['fname']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY MIDDLE NAME}',ucwords($resDetails['mname']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY LAST NAME}',ucwords($resDetails['lname']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY DOB}',ucwords($resDetails['res_dob']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY SS}',ucwords($resDetails['ss']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY SEX}',ucwords($resDetails['sex']),$consent_form_content);
		$strToShowRelation = $resDetails['relation'];
		if(strtolower($resDetails['relation']) == "doughter"){
			$strToShowRelation = "Daughter";
		}
		$consent_form_content = str_ireplace('{RES.PARTY RELATION}',ucwords($strToShowRelation),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY ADDRESS1}',ucwords($resDetails['address']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY ADDRESS2}',ucwords($resDetails['address2']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY HOME PH.}',ucwords($resDetails['home_ph']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY WORK PH.}',ucwords($resDetails['work_ph']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY MOBILE PH.}',ucwords($resDetails['mobile']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY CITY}',ucwords($resDetails['city']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY STATE}',ucwords($resDetails['state']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY ZIP}',ucwords($resDetails['zip']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY MARITAL STATUS}',ucwords($resDetails['marital']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY DD NUMBER}',ucwords($resDetails['licence']),$consent_form_content);
	}else{
	
		$consent_form_content = str_ireplace('{RES.PARTY TITLE}',ucwords($patientDetails['title']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($patientDetails['fname']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY MIDDLE NAME}',ucwords($patientDetails['mname']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY LAST NAME}',ucwords($patientDetails['lname']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY DOB}',ucwords($patientDetails['pat_dob']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY SS}',ucwords($patientDetails['ss']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY SEX}',ucwords($patientDetails['sex']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY RELATION}','Self',$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY ADDRESS1}',ucwords($patientDetails['street']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY ADDRESS2}',ucwords($patientDetails['street2']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY HOME PH.}',ucwords($patientDetails['phone_home']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY WORK PH.}',ucwords($patientDetails['phone_biz']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY MOBILE PH.}',ucwords($patientDetails['phone_cell']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY CITY}',ucwords($patientDetails['city']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY STATE}',ucwords($patientDetails['state']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY ZIP}',ucwords($patientDetails['postal_code']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY MARITAL STATUS}',ucwords($patientDetails['status']),$consent_form_content);
		$consent_form_content = str_ireplace('{RES.PARTY DD NUMBER}',ucwords($patientDetails['driving_licence'])	,$consent_form_content);	
	
	}
	
	//--- change epmoyee detail of patient ---
	$consent_form_content = str_ireplace('{PATIENT OCCUPATION}',ucwords($patientDetails['occupation']),$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT EMPLOYER}',ucwords($empDetails['name']),$consent_form_content);
	$consent_form_content = str_ireplace('{OCCUPATION ADDRESS1}',ucwords($empDetails['street']),$consent_form_content);
	$consent_form_content = str_ireplace('{OCCUPATION ADDRESS2}',ucwords($empDetails['street2']),$consent_form_content);
	$consent_form_content = str_ireplace('{OCCUPATION CITY}',ucwords($empDetails['city']),$consent_form_content);
	$consent_form_content = str_ireplace('{OCCUPATION STATE}',ucwords($empDetails['state']),$consent_form_content);
	$consent_form_content = str_ireplace('{OCCUPATION ZIP}',ucwords($empDetails['postal_code']),$consent_form_content);
	$consent_form_content = str_ireplace('{MONTHLY INCOME}','$'.number_format($patientDetails['monthly_income'],2),$consent_form_content);
	$consent_form_content = str_ireplace('{DATE}',get_date_format(date('Y-m-d')),$consent_form_content);
	$consent_form_content = str_ireplace('{DATE_F}',date("F d, Y"),$consent_form_content);
	$consent_form_content = str_ireplace('{TIME}',date('h:i A'),$consent_form_content);
	$consent_form_content = str_ireplace('{OPERATOR NAME}',ucwords(trim($operator_name)),$consent_form_content);
	$consent_form_content = str_ireplace('{OPERATOR INITIAL}',ucwords($operator_initial),$consent_form_content);
	$consent_form_content = str_ireplace('{PATIENT INITIAL}',ucwords($patient_initial),$consent_form_content);
	$consent_form_content = str_ireplace('{TEXTBOX_XSMALL}',inputBox('{TEXTBOX_XSMALL}','xsmall',''),$consent_form_content,$size);
	$consent_form_content = str_ireplace('{TEXTBOX_SMALL}',inputBox('{TEXTBOX_SMALL}','small',''),$consent_form_content,$size);
	$consent_form_content = str_ireplace('{TEXTBOX_MEDIUM}',inputBox('{TEXTBOX_MEDIUM}','medium',''),$consent_form_content,$size);
	$consent_form_content = str_ireplace('{TEXTBOX_LARGE}',inputBox('{TEXTBOX_LARGE}','large',''),$consent_form_content,$size);
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
	$consent_form_content = str_ireplace('{PatientID}',$_REQUEST['patient'],$consent_form_content);
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
	//-- get signature value -------
	$qry = "select form_information_id from patient_consent_form_information 
			where consent_form_id = '$_REQUEST[consent_id]' and patient_id = '$patient_id'";
	$consentDetailsInfo = sqlQuery($qry);
	$form_information_id = $consentDetailsInfo[0]['form_information_id'];
	$qry = "select signature_content from consent_form_signature 
			where form_information_id = '$form_information_id' and patient_id = '$patient_id'
			and consent_form_id = '$_REQUEST[consent_id]' and signature_status = 'Active'";
	$sigDetail = sqlQuery($qry);
	//-- get signature applets ----
	$row_arr = explode('{START APPLET ROW}',$consent_form_content);
	$sig_arr = explode('{SIGNATURE}',$row_arr[0]);	
	$sig_data = '';
	$ds = 1;
	for($s=1;$s<count($sig_arr);$s++,$ds++){/*
		
			$fileName=createBlankImg($_REQUEST['patient'],$_REQUEST['op_id']);
			$sigPats[$fileName[1]]=$fileName[0];
			file_put_contents('test.txt',"line:955= $fileName[1]",FILE_APPEND);
			$sig_data = '
						<span width="145" >
							<a name="typ_sig" href="'.$fileName[1].'" id="'.$fileName[1].'">
							<div style="HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;border:solid 1px; border-color:#FF9900;">
							<img src="'.$fileName[0].'"  /></div></a>
						</span>';
		$str_data = $sig_arr[$s];
		$sig_arr[$s] = $sig_data;
		$sig_arr[$s] .= $str_data;
		$hiddenFields[] = true;
	*/}
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
										
											$fileName=createBlankImg($_REQUEST['patient'],$_REQUEST['op_id'],$addition_path);
											$sigPats[$fileName[1]]=$fileName[0];
											$td_sign .= '<a name="typ_sig" href="'.$fileName[1].'" id="'.$fileName[1].'">
													<div style="HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;">
													<img src="'.$fileName[0].'" style="height:86px; width:318px;" /></div></a>';
											$td_sign .= '</td>';
												
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
	//--- get all content of consent forms -------	
	$consent_content .= '
		<table id="content_'.$_REQUEST['consent_id'].'" style="display:'.$display.'" width="100%" align="center" cellpadding="1" cellspacing="1" border="0">
			<tr>
				<td align="left" >'.$consent_form_content.'</td>
			</tr>
		</table>
	';
//}
//-- get new versions of consent forms ------
$qry = "select consent_form_name from consent_form where consent_form_id = '$_REQUEST[consent_id]'";
$res = sqlQuery($qry);
$consent_form_name = $res['consent_form_name'];
//-- get new versions of consent forms ------
$qry = "select consent_form_id from consent_form
		where consent_form_name = '$consent_form_name'
		order by consent_form_name , consent_form_version desc
		limit 0,1";
$formDetail = sqlQuery($qry);
$new_consent_form_id = array();
$new_consent_form_id = $formDetail[0]['consent_form_id'];
if($new_consent_form_id == $ver_option){
	$ver_option = '';
}
$qry = "select consent_form_name, consent_form_id, form_created_date
		from consent_form
		where consent_form_name = '$consent_form_name' 
		and consent_form_id != '$_REQUEST[consent_id]'";
$res = sqlQuery($qry);


$form_content=array();
if(isset($_GET["chart_procedures_id"]) && !empty($_GET["chart_procedures_id"])){
	$qry = "select consent_form_content_data AS consent_form_content, form_information_id from patient_consent_form_information 
			where consent_form_id = '$_REQUEST[consent_id]' AND chart_procedure_id = '".$_GET["chart_procedures_id"]."' ";	
	$form_content = sqlQuery($qry);		
}

if(count($form_content)<=0){
	$qry = "select consent_form_content from consent_form
			where consent_form_id = '$_REQUEST[consent_id]'";
	$form_content = sqlQuery($qry);
}

	$consent_form_content_data = stripslashes($form_content['consent_form_content']);
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
					$repVal .= $arrExp[$p].inputBox($arrStr[$j],$name.$c,htmlentities($_POST[$name.$c]),$size);
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
					$repVal .= $arrExp[$p].inputBox($arrStr[$j],$name.$c,htmlentities($_POST[$name.$c]),$size);
					$c++;
				}
				$repVal .= end($arrExp);
				$consent_form_content_data = $repVal;
			}
		}
		else
		{
			if($arrStr[$j] == '{TEXTBOX_XSMALL}') {
				$repVal = str_ireplace($arrStr[$j],inputBox($arrStr[$j],$name,htmlentities($_POST[$name])),$consent_form_content_data,$size);
				$consent_form_content_data = $repVal;
			}else if($arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}'){
				$repVal = str_ireplace($arrStr[$j],inputBox($arrStr[$j],$name,htmlentities($_POST[$name])),$consent_form_content_data,$size);
				$consent_form_content_data = $repVal;
			}else if($arrStr[$j] == '{TEXTBOX_LARGE}')
			{
				$repVal = str_ireplace($arrStr[$j],inputBox($arrStr[$j],$name,htmlentities($_POST[$name])),$consent_form_content_data,$size);
				$consent_form_content_data = $repVal;
			}
		}
		 
	}
	
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

/*--REPLACING SMART TAGS (IF FOUND) WITH LINKS--*/
	$arr_smartTags = get_smartTags_array();
	$tagconsent_form_content_data = '';
	foreach($arr_smartTags as $key=>$val){
//		$val = str_replace('&nbsp',' ',$val);
		$regpattern='/(\['.$val.'\])+/i';
		$row_arr = preg_split($regpattern,$consent_form_content_data);
//		$row_arr = explode("[".$val."]",$consent_form_content_data);
		$tagconsent_form_content_data = $row_arr[0];
		for($tagCount=1;$tagCount<count($row_arr);$tagCount++){
			//$tagconsent_form_content_data .= '<a id="'.$key.'_'.$tagCount.'tag" class="cls_smart_tags_link" href="javascript:;">'.$val.'</a><input type="hidden" class="'.$key.'_'.$tagCount.'tag" name="'.$key.'_'.$tagCount.'tag" value="">'.$row_arr[$tagCount];
			$tagconsent_form_content_data .= '<a id="'.$key.'" class="cls_smart_tags_link" href="javascript:">'.$val.'</a>'.$row_arr[$tagCount];
		}
		
		if($tagconsent_form_content_data != ''){//die($tagconsent_form_content_data);
			$consent_form_content_data = $tagconsent_form_content_data;
		}
	}
	/*--SMART TAG REPLACEMENT END--*/

	//--- change value between curly brackets -------	
	$consent_form_content_data = str_ireplace('{PATIENT NAME TITLE}',ucwords($patientDetails['title']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT FIRST NAME}',ucwords($patientDetails['fname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{MIDDLE NAME}',ucwords($patientDetails['mname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{LAST NAME}',ucwords($patientDetails['lname']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{SEX}',ucwords($patientDetails['sex']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{DOB}',ucwords($patientDetails['pat_dob']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{AGE}',$patient_age,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT SS}',ucwords($patientDetails['ss']),$consent_form_content_data);
	//=============START WORK TO SHOW THE LAST 4 DIGIT PATIENT SS==========================
	if($patientDetails['ss']!=''){
		$consent_form_content_data = str_ireplace('{PATIENT_SS4}',ucwords(substr_replace($patientDetails['ss'],'XXX-XX',0,6)),$consent_form_content_data);
	}else{
		$consent_form_content_data = str_ireplace('{PATIENT_SS4}','',$consent_form_content_data);
	}
	//===========================END WORK===================================================
	$consent_form_content_data = str_ireplace('{PHYSICIAN NAME}',ucwords($phy_name),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PHYSICIAN FIRST NAME}',ucwords($phy_fname),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PHYSICIAN MIDDLE NAME}',ucwords($phy_mname),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PHYSICIAN LAST NAME}',ucwords($phy_lname),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PHYSICIAN NAME SUFFIX}',ucwords($phy_name_suffix),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{MARITAL STATUS}',ucwords($patientDetails['status']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{ADDRESS1}',ucwords($patientDetails['street']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{ADDRESS2}',ucwords($patientDetails['street2']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{HOME PHONE}',ucwords($patientDetails['phone_home']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{EMERGENCY CONTACT}',ucwords($patientDetails['contact_relationship']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{EMERGENCY CONTACT PH}',ucwords($patientDetails['phone_contact']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{MOBILE PHONE}',ucwords($patientDetails['phone_cell']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{WORK PHONE}',ucwords($patientDetails['phone_biz']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT CITY}',ucwords($patientDetails['city']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT STATE}',ucwords($patientDetails['state']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT ZIP}',ucwords($patientDetails['postal_code']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REGISTRATION DATE}',ucwords($patientDetails['reg_date']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REFFERING PHY.}',ucwords($reffer_name),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{POS FACILITY}',ucwords($pos_facility_name),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{DRIVING LICENSE}',ucwords($patientDetails['driving_licence']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{HEARD ABOUT US}',ucwords($patientDetails['heard_abt_us']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{HEARD ABOUT US DETAIL}',$patientDetails['heard_abt_desc'],$consent_form_content_data);

	$consent_form_content_data = str_ireplace('{EMAIL ADDRESS}',$patientDetails['email'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{USER DEFINE 1}',$patientDetails['genericval1'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT MRN}',$patientDetails['External_MRN_1'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT MRN2}',$patientDetails['External_MRN_2'],$consent_form_content_data);
	
	$languageShow 			   = str_ireplace("Other -- ","",$patientDetails['language']);
	$raceShow				   = trim($patientDetails["race"]);
	$otherRace				   = trim($patientDetails["otherRace"]);
	if($otherRace) { 
		$raceShow			   = $otherRace;
	}
	$ethnicityShow			   = trim($patientDetails["ethnicity"]);			
	$otherEthnicity			   = trim($patientDetails["otherEthnicity"]);
	if($otherEthnicity) { 
		$ethnicityShow		   = $otherEthnicity;
	}

	$consent_form_content_data = str_ireplace('{RACE}',$raceShow,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{LANGUAGE}',$languageShow,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{ETHNICITY}',$ethnicityShow,$consent_form_content_data);
	
	$consent_form_content_data = str_ireplace('{REL INFO}',getPtReleaseInfoNames($patient_id),$consent_form_content_data);
	//new variable added
	if($patientDetails['postal_code']) {
		$consent_form_content_data = str_ireplace('{STATE ZIP CODE}',ucwords($patientDetails['state'].' '.$patientDetails['postal_code']),$consent_form_content_data);
	}
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN TITLE}',		trim(ucwords($reffPhyDetail['Title'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN FIRST NAME}',	trim(ucwords($reffPhyDetail['FirstName'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHYSICIAN LAST NAME}',	trim(ucwords($reffPhyDetail['LastName'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY SPECIALITY}',		trim(ucwords($reffPhyDetail['specialty'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY PHONE}',			trim(ucwords($reffPhyDetail['physician_phone'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY STREET ADDR}',		$refPhyAddress,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY CITY}',				trim(ucwords($reffPhyDetail['City'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY STATE}',			trim(ucwords($reffPhyDetail['State'])),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{REF PHY ZIP}',				trim(ucwords($reffPhyDetail['ZipCode'])),$consent_form_content_data);
	
	$consent_form_content_data = str_ireplace('{PCP NAME}',$pcpName,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP STREET ADDR}',$pcpAddress,$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP City}',	$pcpPhyDetail['pcpCity'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP State}',$pcpPhyDetail['pcpState'],$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PCP ZIP}',	$pcpPhyDetail['pcpZipCode'],$consent_form_content_data);	
	
	//=============RESPONSIBLE PARTY DATA REPLACEMENT-III======================================================
	//=============NOW IF PATIENT HAVE NO RESPONSILE PERSON THEN PATIENT DATA WILL BE REPLACED.=============
	if(count($resDetails)>0){
		$consent_form_content_data = str_ireplace('{RES.PARTY TITLE}',ucwords($resDetails['title']),$consent_form_content_data);
	
		$consent_form_content_data = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($resDetails['fname']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY MIDDLE NAME}',ucwords($resDetails['mname']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY LAST NAME}',ucwords($resDetails['lname']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY DOB}',ucwords($resDetails['res_dob']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY SS}',ucwords($resDetails['ss']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY SEX}',ucwords($resDetails['sex']),$consent_form_content_data);
		$strToShowRelation = $resDetails['relation'];
		if(strtolower($resDetails['relation']) == "doughter"){
			$strToShowRelation = "Daughter";
		}
		$consent_form_content_data = str_ireplace('{RES.PARTY RELATION}',ucwords($strToShowRelation),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS1}',ucwords($resDetails['address']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS2}',ucwords($resDetails['address2']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY HOME PH.}',ucwords($resDetails['home_ph']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY WORK PH.}',ucwords($resDetails['work_ph']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY MOBILE PH.}',ucwords($resDetails['mobile']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY CITY}',ucwords($resDetails['city']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY STATE}',ucwords($resDetails['state']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY ZIP}',ucwords($resDetails['zip']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY MARITAL STATUS}',ucwords($resDetails['marital']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY DD NUMBER}',ucwords($resDetails['licence']),$consent_form_content_data);
	}else{
		 $consent_form_content_data = str_ireplace('{RES.PARTY TITLE}',ucwords($patientDetails['title']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($patientDetails['fname']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY MIDDLE NAME}',ucwords($patientDetails['mname']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY LAST NAME}',ucwords($patientDetails['lname']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY DOB}',ucwords($patientDetails['pat_dob']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY SS}',ucwords($patientDetails['ss']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY SEX}',ucwords($patientDetails['sex']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY RELATION}','Self',$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS1}',ucwords($patientDetails['street']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS2}',ucwords($patientDetails['street2']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY HOME PH.}',ucwords($patientDetails['phone_home']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY WORK PH.}',ucwords($patientDetails['phone_biz']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY MOBILE PH.}',ucwords($patientDetails['phone_cell']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY CITY}',ucwords($patientDetails['city']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY STATE}',ucwords($patientDetails['state']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY ZIP}',ucwords($patientDetails['postal_code']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY MARITAL STATUS}',ucwords($patientDetails['status']),$consent_form_content_data);
		$consent_form_content_data = str_ireplace('{RES.PARTY DD NUMBER}',ucwords($patientDetails['driving_licence']),$consent_form_content_data);	
	}
	//=====================================THE END REPONSIBLE PARTY DATA-III========================================
	
	//--- change epmoyee detail of patient ---
	$consent_form_content_data = str_ireplace('{PATIENT OCCUPATION}',ucwords($patientDetails['occupation']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{PATIENT EMPLOYER}',ucwords($empDetails['name']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION ADDRESS1}',ucwords($empDetails['street']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION ADDRESS2}',ucwords($empDetails['street2']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION CITY}',ucwords($empDetails['city']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION STATE}',ucwords($empDetails['state']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{OCCUPATION ZIP}',ucwords($empDetails['postal_code']),$consent_form_content_data);
	$consent_form_content_data = str_ireplace('{MONTHLY INCOME}','$'.number_format($patientDetails['monthly_income'],2),$consent_form_content_data);
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
	$consent_form_content_data = str_ireplace('{PatientID}',$_REQUEST['patient'],$consent_form_content_data);
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
	//
	
	$row_arr = explode('{START APPLET ROW}',$consent_form_content_data);
	$sig_arr = explode('{SIGNATURE}',$row_arr[0]);	
	$sig_data = '';
	$ds = 1;
	for($s=1;$s<count($sig_arr);$s++,$ds++){
		
			$fileName=createBlankImg($_REQUEST['patient'],$_REQUEST['op_id'],$addition_path);
			$sigPats[$fileName[1]]=$fileName[0];
			$sig_data = '<span width="145">
							<a name="typ_sig" href="'.$fileName[1].'" id="'.$fileName[1].'">
							<div style="HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;border:solid 1px; border-color:#FF9900;">
							<img src="'.$fileName[0].'" style="height:86px; width:318px;" /></div></a>
						</span>
						
					';
		
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
		
			$fileName=createBlankImg($_REQUEST['patient'],$_REQUEST['op_id'],$addition_path);
			$sigPats[$fileName[1]]=$fileName[0];
			$sig_wit_data = '<span id="SpanWitSign'.$ds.'">
					<span width="145">
						<a name="typ_sig" href="'.$fileName[1].'" id="'.$fileName[1].'">
						<div style="HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;border:solid 1px; border-color:#FF9900;">
						<img src="'.$fileName[0].'" style="height:86px; width:318px;" /></div></a>
					</span>
					</span>';
		
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
	$ds=1;
	for($s=1;$s<count($sig_phy_arr);$s++,$ds++){
			$fileName=createBlankImg($_REQUEST['patient'],$_REQUEST['op_id'],$addition_path);
			$sigPats[$fileName[1]]=$fileName[0];
			$sig_phy_data = '<span>
					<span width="145" >
						<a name="typ_sig" href="'.$fileName[1].'" id="'.$fileName[1].'">
						<div style="HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;border:solid 1px; border-color:#FF9900;">
						<img src="'.$fileName[0].'" style="height:86px; width:318px;" /></div></a>
					</span>
					
				</span>';
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
				$td_sign .= '
							<td align="left">
								<table border="0">
									<tr><td>'.$sig_arr1[$t].'</td></tr>
									<tr>
										<td style="border:solid 1px;" bordercolor="#FF9900">';
											$fileName=createBlankImg($_REQUEST['patient'],$_REQUEST['op_id'],$addition_path);
											$sigPats[$fileName[1]]=$fileName[0];
											$td_sign .= '<a name="typ_sig" href="'.$fileName[1].'" id="'.$fileName[1].'">
											<div style="HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;">
											<img src="'.$fileName[0].'" style="height:86px; width:318px;" /></div></a>';
											$td_sign .= '</td>';			
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
				</table>';
		}
	}
	$jh = 1;
	$consent_form_content .= $content_row;
	//--- get all content of consent forms -------	
	$consent_content_new .= '
						<table id="content_'.$_REQUEST['consent_id'].'" width="100%" align="center" cellpadding="1" cellspacing="1" border="0">
							<tr>
								<td align="left" >'.$consent_form_content.'</td>
							</tr>
						</table>
					';
	$consent_form_content = $consent_content_new;
	$consent_form_content .="<endofcode></endofcode>";
//}
header("Content-Type: text/html; charset=utf-8");
ob_start();//rest of code is for smart tag which is on hold for now--------------------------------
//global $webServerRootDirectoryName;
//global $web_RootDirectoryName,$web_root;

?>
<style>
OBJECT{position: absolute; z-index:1;}
#div_smart_tags_options{z-index:2}
	.page_block_heading, .div_popup_heading{
	color:#333333; font-size:12px; text-align:left;
}
.div_popup{
	position:absolute; display:none; z-index:1000; background-color:#CCCCCC; width:auto; overflow:auto;
}
.div_popup_heading{
	background:#FFCC66 url(images/popup_heading.gif) repeat-x; height:15px; padding:5px 5px;
	border-bottom:1px solid #E6B24A;
}
</style>

<script type="text/javascript" src="<?php echo $GLOBALS['php_server'];?>/library/js/jquery.min.1.12.1.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['php_server'];?>/library/js/jquery-ui.min.1.12.0.js"></script>

<!--<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../interface/admin/menuIncludes_menu/js/jBoss.js"></script>
<script type="text/javascript" src="../interface/js/dragresize.js"></script>

<link rel="stylesheet" href="<?php echo $GLOBALS['php_server'].'/interface';?>/themes/default/common.css" type="text/css">
<link rel='stylesheet' href="<?php echo $css_header;?>" type="text/css">
<link rel='stylesheet' href="<?php echo $css_patient;?>" type="text/css">
<link rel="stylesheet" href="<?php echo $GLOBALS['php_server'].'/interface';?>/themes/tab-view.css" type="text/css">
-->
<div class="div_popup white border" id="div_smart_tags_options" style="top:200px;left:400px; width:300px; z-index:999;">
	<div class="section_header"><span class="closeBtn" onClick="$('#div_smart_tags_options').hide();"></span>Smart Tag Options</div>
	<img src="<?php echo $GLOBALS['php_server'];?>/images/ajax-loader.gif">
</div>
<input type="hidden" name="smartTag_parentId" id="smartTag_parentId" value="">
<script type="text/javascript">

var smart_tag_current_object = new Object;

	if(typeof(win_op)=='undefined'){
		var win_op='';
		if(win_op==''){
			$('.cls_smart_tags_link').mouseup(function(e){
				//if(e.button==2){
					$('#smartTag_parentId').val($(this).attr('id'));
					smart_tag_current_object = $(this);
					display_tag_options($(this)[0]);  		
				//}
				
			});
		}
	}else if(win_op==1){
		$('.cls_smart_tags_link').click(function(e){
				$('#smartTag_parentId').val($(this).attr('id'));
				smart_tag_current_object = $(this);
				display_tag_options($(this)[0]);
			}
		);
	}


function display_tag_options(_this){
	
	$("#div_smart_tags_options").css({position:'absolute'});
	$('#div_smart_tags_options').html('<div class="section_header"><span class="closeBtn" onClick="$(\'#div_smart_tags_options\').hide();"></span>Smart Tag Options</div><img src="<?php echo $GLOBALS['php_server'];?>/images/ajax-loader.gif">');
	
	var parentId = $('#smartTag_parentId').val();
	/*
	ArrtempParentId = parentId.split('_');
	parentId = ArrtempParentId[0];
	*/
	var sendData = 'current_tab=smart_tags&call_from=get_tags_app&id='+parentId+'&patId=<?php echo $_REQUEST['patient']; ?>';
	$.ajax({
		type: "GET",
		url: "<?php echo $GLOBALS['webroot']; ?>/interface/admin/documents/ajax_handler.php",
		data:sendData,
		complete:function(){
			$('#div_smart_tags_options').show();
			var elementPos = $(_this).offset();
			var finalTop = elementPos.top - $('#div_smart_tags_options').outerHeight(true) + $(_this).outerHeight();
			var finalLeft = elementPos.left + $(_this).outerWidth(true) + 10;

			if(finalTop > screen.availHeight) finalTop = finalTop - (finalTop - screen.availHeight);
			if(finalLeft > screen.availWidth) finalLeft = finalLeft - (finalLeft - screen.availWidth) + $(_this).outerHeight(true);

			if(finalLeft < 0 )  finalLeft = elementPos.left + $(_this).outerWidth() + 10;
			if(finalTop < 0 )  finalTop = elementPos.top;


			$('#div_smart_tags_options').css('left',finalLeft);
			$('#div_smart_tags_options').css('top',finalTop);
		},
		success: function(resp){
			$('#div_smart_tags_options').html(resp);
			
			/*var hit_list = $("object").overlaps("#div_smart_tags_options");	
			if(hit_list.hits.length>0){				
				
				
				var a = $("#div_smart_tags_options").offset();
				var b = $(_this).offset();
				
				var ll=a.left;
				var tt=a.top;
				var zz = b.top-$("#div_smart_tags_options").height();
				if(zz<=0){ zz=0; }
				$('#div_smart_tags_options').css('top',zz);				
				var hit_list = $("object").overlaps("#div_smart_tags_options");	
				if(hit_list.hits.length>0){
					var yy = b.left-$("#div_smart_tags_options").width();	
					if(yy<=0){ yy=0;}
					$('#div_smart_tags_options').css('left',yy);
					var hit_list = $("object").overlaps("#div_smart_tags_options");	
					if(hit_list.hits.length>0){
						$("#div_smart_tags_options").offset(a);
						var yy = b.left-$("#div_smart_tags_options").width();	
						if(yy<=0){ yy=0;}
						$('#div_smart_tags_options').css('left',yy);
						var hit_list = $("object").overlaps("#div_smart_tags_options");	
						if(hit_list.hits.length>0){
							var flag=5;
							if(yy<=100){
								flag=1;								
								do{								
								$("#div_smart_tags_options").offset(a);
								var yy = b.left+(($("#div_smart_tags_options").width()/2)*flag)+25;	
								$('#div_smart_tags_options').css('left',yy);
								var hit_list = $("object").overlaps("#div_smart_tags_options");	
								if(hit_list.hits.length>0){
									flag+=1;
								}else{
									flag=5;
								}
								}while(flag<=3);
							}else{
								flag=0;
							}
							
							if(flag!=5 && zz<=100){
								flag=1;
								do{								
								$("#div_smart_tags_options").offset(a);
								var zz = b.top+(($("#div_smart_tags_options").height()/2)*flag);
								$('#div_smart_tags_options').css('top',zz);
								var hit_list = $("object").overlaps("#div_smart_tags_options");	
								if(hit_list.hits.length>0){/*restore if not working* /
									flag+=1;
								}else{
									flag=5;
								}
								}while(flag<=3);
							}
							
							if(flag!=5){
								$("#div_smart_tags_options").offset(a);
								$("#div_smart_tags_options .closeBtn").bind("click", function(){ $("object").css({"visibility":"visible"}); });
								$("object").css({"visibility":"hidden"});
								/ *
			//top.window.status = "Please drag smart menu out of signature box!";						
							}
						}						
					}
				}				
				
			}*/
			
			/*//check position--*/
			
		}
	});
	
}

function replace_tag_with_options(){
	var strToReplace = '';
	var parentId = $('#smartTag_parentId').val();
	
	$('input[name=chkSmartTagOptions]').each(function(id, elem){
		//console.log($(elem));
		if($(elem).prop('checked') == true){
			if(strToReplace=='')
				strToReplace +=  $(elem).val();
			else
				strToReplace +=  ', '+$(elem).val();
		}
	});
	/*--//alert(strToReplace);
	
	GETTING FCK EDITOR TEXT--*/
	if(strToReplace!='' && smart_tag_current_object){
		$('.cls_smart_tags_link[id="'+parentId+'"]').html(strToReplace);
		/*//$(smart_tag_current_object).html(strToReplace);*/
		var hiddclass = $(smart_tag_current_object).attr('id');
		$('.'+hiddclass).val($(smart_tag_current_object).text());
/*		
		RemoveString = window.location.protocol+'//'+window.location.host; //.innerHTML BUG adds host url to relative urls.
		var strippedData = $('#hold_temp_smarttag_data').html();
		strippedData = strippedData.replace(new RegExp(RemoveString, 'g'),'');
*/		
		$('#div_smart_tags_options').hide();
		$("object").css({"visibility":"visible"});
	}else{
		alert('Select Options');
	}
}


$('.manageUserInput').keyup(function(){
if($(this).val()!=""){

var id_t = $(this).attr("id"); 
var val_t= $(this).val(); 
$("#"+id_t+"_span").html(val_t);
} 
});

$(document).ready(function(e) {
    reloadValues()
});

function reloadValues()
{
	/*$('.manageUserInput').function(){
	if($(this).val()!="") 
		{
			var id_t = $(this).attr("id"); 
			var val_t= $("#"+id_t+"_span").html();
			$(this).val(val_t); 
		} 
	};*/	
	$('.manageUserInput').each(function(index, element) {
		//console.log($(element).attr("id"));
		var id_t = $(element).attr("id");
		var val_t= $("#"+id_t+"_span").html();
		//console.log(val_t);
		$(element).val(val_t);
    });
	
}
</script>
<?php

$consent_form_content.= ob_get_clean();
?>