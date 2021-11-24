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
File: print_consent_form.php
Purpose: Print consent forms
Access Type: Direct 
*/
	

//require_once(dirname(__FILE__).'/../../main/Functions.php');
//include_once(dirname(__FILE__)."/../../chart_notes/progress_notes/pnTempParser.php");

include_once(dirname(__FILE__)."/../../config/globals.php");
include_once($GLOBALS['srcdir'].'/bar_code/code128/code128.class.php');
//include_once($GLOBALS['srcdir'].'/classes/work_view/ChartAP.php');
//include_once($GLOBALS['srcdir'].'/classes/work_view/pnTempParser.php');
//include_once($GLOBALS['srcdir'].'/classes/cls_common_function.php');

$browserIpad = 'no';
if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true) {
	$browserIpad = 'yes';
}

//delete tmp folder for target operator
$filePath = data_path()."app_services/signature/tmp/".$_REQUEST['phyId']."/admin_consent/";
if (is_dir($filePath)) {
	delete($filePath);
}//clear dir for that operator
mkdir($filePath, 0777, true);

if(isset($_REQUEST['patId']) && empty($_REQUEST['patId']) == false) $_REQUEST['patient_id'] = $_REQUEST['patId'];

$patient_id = $_REQUEST['patient_id'];
$new_manageData = new CLSCommonFunction;
if($_REQUEST['patient_id']){
	$upcoming_appt=$new_manageData->__getApptFuture($_REQUEST['patient_id'],'','','n');
}
$objParser = new PnTempParser;
$medHx=$objParser->getMedHx_public($_REQUEST['patient_id']);
$ocularMed=$objParser->get_med_list($_REQUEST['patient_id'],4);
$systemicMed=$objParser->get_med_list($_REQUEST['patient_id'],1);
$allergyList=$objParser->get_med_list($_REQUEST['patient_id'],7);
$ins_cases=$objParser->all_ins_case($_REQUEST['patient_id']);
$form_id=$_REQUEST['form_id'];
if(!$form_id){
	$qry_form_id="Select id from chart_master_table where patient_id='".$_REQUEST['patient_id']."'  order by id desc";
	$res_form_id=imw_query($qry_form_id);
	$row_form_id=imw_fetch_assoc($res_form_id);
	$form_id=$row_form_id['id'];
}
if($_REQUEST['patient_id'] && $form_id){
	$cc_hx=$objParser->get_cc_hx($_REQUEST['patient_id'],$form_id);
	$cc_val=$cc_hx[1];
	$hx_val=$cc_hx[2];
}

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

function get_consent(){
	$patient_id = $_REQUEST['patient_id'];
	$consent_form_id = $_REQUEST['consent_id'];
	//---- get Patient details ---------
	$qry = imw_query("select *,date_format(DOB,'".get_sql_date_format()."') as pat_dob,date_format(date,'".get_sql_date_format()."') as reg_date
			from patient_data where id = '".$_REQUEST['patient_id']."'");
	$patientDetails = '';
	if($qry && imw_num_rows($qry) > 0){
		$patientDetails = imw_fetch_assoc($qry);
	}
	
	$patient_initial = substr($patientDetails['fname'],0,1);
	$patient_initial .= substr($patientDetails['lname'],0,1);
	//--- get physician name --------
	$pro_id = $patientDetails['providerID'];
	$qry = imw_query("select concat(lname,', ',fname) as name,mname from users where id = '$pro_id'");
	
	$phyDetail = '';
	if($qry && imw_num_rows($qry) > 0){
		$phyDetail = imw_fetch_assoc($qry);
	}
	
	$phy_name = ucwords(trim($phyDetail['name'].' '.$phyDetail['mname']));
	//--- get reffering physician name --------
	$primary_care_id = $patientDetails['primary_care_id'];
	$qry = imw_query("select concat(LastName,', ',FirstName) as name , MiddleName,Title, specialty, physician_phone, Address1, Address2, ZipCode, City, State from refferphysician
			where physician_Reffer_id = '$primary_care_id'");
	$reffPhyDetail = '';
	if($qry && imw_num_rows($qry) > 0){
		$reffPhyDetail = imw_fetch_assoc($qry);
	}
	
	//$reffPhyDetail = ManageData::getQryRes($qry);
	$reffer_name = ucwords(trim($reffPhyDetail['name'].' '.$reffPhyDetail['MiddleName']));
	$refPhyAddress="";
	$refPhyAddress .= (!empty($reffPhyDetail['Address1'])) ? trim($reffPhyDetail['Address1']) : "";
	$refPhyAddress .= (!empty($reffPhyDetail['Address2'])) ? "<br>".trim($reffPhyDetail['Address2']) : "";
	
	//--- get pos facility name -------
	$default_facility = $patientDetails['default_facility'];
	$qry = imw_query("select facilityPracCode from pos_facilityies_tbl 
			where pos_facility_id = '$default_facility'");
	$posFacilityDetail = '';
	if($qry && imw_num_rows($qry) > 0){
		$posFacilityDetail = imw_fetch_assoc($qry);
	}
	//$posFacilityDetail = ManageData::getQryRes($qry);
	$pos_facility_name = $posFacilityDetail['facilityPracCode'];
	
	//--- get responsible party information ------
	$qry = imw_query("select *,date_format(dob,'".get_sql_date_format()."') as res_dob 
			from resp_party where patient_id = '$patient_id'");
	$resDetails = '';
	if($qry && imw_num_rows($qry) > 0){
		$resDetails = imw_fetch_assoc($qry);
	}
	
	//$resDetails = ManageData::getQryRes($qry);
	//--- get epmoyee detail of patient ---
	$qry = imw_query("select * from employer_data where pid = '$patient_id'");
	$empDetails = '';
	if($qry && imw_num_rows($qry) > 0){
		$empDetails = imw_fetch_assoc($qry);
	}
	//$empDetails = ManageData::getQryRes($qry);
	if(isset($_REQUEST['callFromApp']) && empty($_REQUEST['callFromApp']) == false){
		//If call is from Consent form then use this block
		/* echo "SELECT pcf.consent_form_content_data
										FROM patient_consent_form_information  pcf
										WHERE pcf.patient_id='".$_REQUEST['patient_id']."' 
											AND pcf.form_information_id  = '".$_REQUEST['consent_id']."'"; */
		$qry = imw_query("SELECT pcf.consent_form_content_data
										FROM patient_consent_form_information  pcf
										WHERE pcf.patient_id='".$_REQUEST['patient_id']."' 
											AND pcf.form_information_id  = '".$_REQUEST['consent_id']."'");
		$consentDetail = '';
		if($qry && imw_num_rows($qry) > 0){
			$consentDetail = imw_fetch_assoc($qry);
		}
		
		$chk = count($consentDetail);
		if(count($consentDetail) == 0){
			$qry = imw_query("select *,consent_form_content as consent_form_content_data, cat_id as consent_cat_id 
					from consent_form where consent_form_id = '".$_REQUEST['consent_id']."'");
			$consentDetail = '';
			if($qry && imw_num_rows($qry) > 0){$consentDetail = imw_fetch_assoc($qry);}
			if($_REQUEST["package_category_id"]!="") {
				$consentDetail["consent_package_category_id"] = $_REQUEST["package_category_id"];
			}
			//echo "<pre>";
		//echo $consentDetail[0]['consent_form_content_data'];
		//exit;
		}
	}else{
		$qry = imw_query("select consent_form_name from consent_form where consent_form_id = '".$_REQUEST['consent_id']."'");
		$res = '';
		if($qry && imw_num_rows($qry) > 0){
			$res = imw_fetch_assoc($qry);
		}
		//$res = ManageData::getQryRes($qry);
		$consent_form_name = $res['consent_form_name'];
		$qry = imw_query("SELECT consent_form_content FROM consent_form WHERE consent_form_id  = '".$_REQUEST['consent_id']."'");
		$consentDetail = '';
		if($qry && imw_num_rows($qry) > 0){
			$consentDetail = imw_fetch_assoc($qry);
		}
		//$consentDetail = ManageData::getQryRes($qry);
		$chk = count($consentDetail);
		if(count($consentDetail) == 0){
			$qry = imw_query("select *,consent_form_content as consent_form_content_data, cat_id as consent_cat_id 
					from consent_form where consent_form_id = '$consent_form_id'");
			$consentDetail = '';
			if($qry && imw_num_rows($qry) > 0){
				$consentDetail = imw_fetch_assoc($qry);
			}
			
			if($_REQUEST["package_category_id"]!="") {
				$consentDetail["consent_package_category_id"] = $_REQUEST["package_category_id"];
			}
			//echo "<pre>";
		//echo $consentDetail['consent_form_content_data'];
		//exit;
		}
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
						 where ind.pid = '".$_REQUEST['patient_id']."' and 
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
						 where ind.pid = '".$_REQUEST['patient_id']."' and 
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
	
	$pcp_id = $patientDetails['primary_care_phy_id'];
	$qry = imw_query("select pcp.Title as pcpTitle,pcp.FirstName as pcpFName,pcp.MiddleName as pcpMName,pcp.LastName as pcpLName, 
			pcp.Address1 as pcpAddress1,pcp.Address2 as pcpAddress2,pcp.City as pcpCity,pcp.State as pcpState,pcp.ZipCode as pcpZipCode
			 from refferphysician pcp
			 where pcp.physician_Reffer_id = '".$pcp_id."'");
	$pcpPhyDetail = '';
	if($qry && imw_num_rows($qry) > 0){
		$pcpPhyDetail = imw_fetch_assoc($qry);
	}
	
	//$pcpPhyDetail = ManageData::getQryRes($qry);
	$pcpName=$pcpPhyDetail['pcpLName'].", ".$pcpPhyDetail['pcpFName']." ".$pcpPhyDetail['pcpMName'];
	$pcpAddress .= (!empty($pcpPhyDetail['pcpAddress1'])) ? trim($pcpPhyDetail['pcpAddress1']) : "";
	$pcpAddress .= (!empty($pcpPhyDetail['pcpAddress2'])) ? "<br>".trim($pcpPhyDetail['pcpAddress2']) : "";
	
	//end get Secondary insurence data//
	//get patient Appointment data//
	$qryGetApptData = "select sa.sa_app_start_date,DATE_FORMAT(sa.sa_app_start_date, '%a ".get_sql_date_format('','y','/')."')  as appDate,
						 TIME_FORMAT(sa.sa_app_starttime,'%h:%i %p') as appTime,
						 sp.proc as ptProc
						 from schedule_appointments sa 
						 INNER JOIN slot_procedures sp ON sp.id = sa.procedureid  
						 where sa.sa_patient_id = '".$_REQUEST['patient_id']."' and 
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
	
	$htmlFolder = "new_html2pdf";
		$htmlV2Class=true;	
		$htmlFilePth = "new_html2pdf/createPdf.php";
	//http host & protocol for replace sign path
	$http_host=$_SERVER['HTTP_HOST'];
	if($protocol==''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }
	//pre($consentDetail);die;
	//END CODE TO SET NEW CLASS TO CONSENT FORMS - 
	//for($i=0;$i<count($consentDetail);$i++){	
		$consent_cat_id = $consentDetail['consent_cat_id'];//$patient_id
		$consent_package_category_id = $consentDetail['consent_package_category_id'];//$patient_id
		if(isset($consentDetail['consent_form_content']) && empty($consentDetail['consent_form_content']) == false){
			$consentDetail['consent_form_content_data'] = stripslashes($consentDetail['consent_form_content']);
		}
		$consentDetail['consent_form_content_data'] = htmlspecialchars_decode($consentDetail['consent_form_content_data']);
		//--- change value between curly brackets -------	
		
		
		$consent_form_content = str_ireplace('{PATIENT NAME TITLE}',ucwords($patientDetails['title']),$consentDetail['consent_form_content_data']);
	
		/*$consent_form_content = str_ireplace($protocol.$http_host.$web_root.'/interface/common/new_html2pdf/','',$consent_form_content);
		$consent_form_content = str_ireplace($web_root.'/interface/common/'.$htmlFolder.'/','',$consent_form_content);
		$consent_form_content = str_ireplace($web_root.'/interface/common/html2pdf/','',$consent_form_content);
		$consent_form_content = str_ireplace($web_root.'/interface/common/new_html2pdf/','',$consent_form_content);
	
		
		$consent_form_content = str_ireplace('interface/common/html2pdf/','',$consent_form_content);
		$consent_form_content = str_ireplace('interface/common/new_html2pdf/','',$consent_form_content);
		$consent_form_content = str_ireplace($protocol.$http_host.$web_root.'/interface/main/uploaddir/','../../main/uploaddir/',$consent_form_content);
		$consent_form_content = str_ireplace($protocol.$myExternalIP.$web_root.'/interface/main/uploaddir/','../../main/uploaddir/',$consent_form_content);
		$consent_form_content = str_ireplace($web_root.'/interface/main/uploaddir/','../../main/uploaddir/',$consent_form_content);
		$consent_form_content = str_ireplace($protocol.$http_host.$web_root.'/redactor/images/','../../../redactor/images/',$consent_form_content);
		$consent_form_content = str_ireplace('/'.$web_RootDirectoryName,'',$consent_form_content);*/
		$consent_form_content = str_ireplace('%20',' ',$consent_form_content);
		$consent_form_content = str_ireplace('{PATIENT FIRST NAME}',ucwords($patientDetails['fname']),$consent_form_content);
		$consent_form_content = str_ireplace('{MIDDLE NAME}',ucwords($patientDetails['mname']),$consent_form_content);
		$consent_form_content = str_ireplace('{LAST NAME}',ucwords($patientDetails['lname']),$consent_form_content);
		$consent_form_content = str_ireplace('{SEX}',ucwords($patientDetails['sex']),$consent_form_content);
		$consent_form_content = str_ireplace('{DOB}',ucwords($patientDetails['pat_dob']),$consent_form_content);
		$consent_form_content = str_ireplace('{PATIENT SS}',ucwords($patientDetails['ss']),$consent_form_content);
		//=============START WORK TO SHOW THE LAST 4 DIGIT PATIENT SS==========================
		if(trim($patientDetails['ss'])!=''){
			$consent_form_content = str_ireplace('{PATIENT_SS4}',ucwords(substr_replace($patientDetails['ss'],'XXX-XX',0,6)),$consent_form_content);
		}else{
			$consent_form_content = str_ireplace('{PATIENT_SS4}','',$consent_form_content);
		}
		//===========================END WORK===================================================
		$consent_form_content = str_ireplace('{PHYSICIAN NAME}',ucwords($phy_name),$consent_form_content);
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
	
		//=============RESPONSIBLE PARTY DATA REPLACEMENT-I======================================================
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
		//=====================================THE END REPONSIBLE PARTY DATA-I========================================
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
		$consent_form_content = str_ireplace('{PatientID}',$_REQUEST['patient_id'],$consent_form_content);
		$consent_form_content = str_ireplace('{Appt Date}',$appDate,$consent_form_content);
		$consent_form_content = str_ireplace('{Appt Time}',$appTime,$consent_form_content);
		$consent_form_content = str_ireplace('{Appt Proc}',$ptProc,$consent_form_content);
		
		$consent_form_content = str_ireplace('{OPERATOR INITIALS}',"",$consent_form_content);
		
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
		
		$consent_form_content = str_ireplace('{PCP NAME}',$pcpName,$consent_form_content);
		$consent_form_content = str_ireplace('{PCP STREET ADDR}',$pcpAddress,$consent_form_content);
		$consent_form_content = str_ireplace('{PCP City}',	$pcpPhyDetail['pcpCity'],$consent_form_content);
		$consent_form_content = str_ireplace('{PCP State}',$pcpPhyDetail['pcpState'],$consent_form_content);
		$consent_form_content = str_ireplace('{PCP ZIP}',	$pcpPhyDetail['pcpZipCode'],$consent_form_content);
		
		$consent_form_content = str_ireplace('{REF PHYSICIAN TITLE}',		trim(ucwords($reffPhyDetail['Title'])),$consent_form_content);
		$consent_form_content = str_ireplace('{REF PHYSICIAN FIRST NAME}',	trim(ucwords($reffPhyDetail['FirstName'])),$consent_form_content);
		$consent_form_content = str_ireplace('{REF PHYSICIAN LAST NAME}',	trim(ucwords($reffPhyDetail['LastName'])),$consent_form_content);
		$consent_form_content = str_ireplace('{REF PHY SPECIALITY}',		trim(ucwords($reffPhyDetail['specialty'])),$consent_form_content);
		$consent_form_content = str_ireplace('{REF PHY PHONE}',			trim(ucwords($reffPhyDetail['physician_phone'])),$consent_form_content);
		$consent_form_content = str_ireplace('{REF PHY STREET ADDR}',		$refPhyAddress,$consent_form_content);
		$consent_form_content = str_ireplace('{REF PHY CITY}',				trim(ucwords($reffPhyDetail['City'])),$consent_form_content);
		$consent_form_content = str_ireplace('{REF PHY STATE}',			trim(ucwords($reffPhyDetail['State'])),$consent_form_content);
		$consent_form_content = str_ireplace('{REF PHY ZIP}',				trim(ucwords($reffPhyDetail['ZipCode'])),$consent_form_content);
		
		
		
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
		if ($chk == 0)
		{
			$conArr='';
			$conArr=explode('{SIGNATURE}',$consent_form_content);
			for($r=1;$r<count($conArr);$r++)
			{
				$fileName=createBlankImg($_REQUEST['patient_id'],$_REQUEST['phyId']);
				$sigPats[$fileName[1]]=$fileName[0];
				$sig_data = '<span width="145" >
								<a name="typ_sig" href="'.$fileName[1].'" id="'.$fileName[1].'">
								<div style="HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;border:solid 1px; border-color:#FF9900;">
								<img src="'.$fileName[0].'"  /></div></a>
							</span>';
				$str_data = $conArr[$s];
				$conArr[$r] = $sig_data;
				$conArr[$r] .= $str_data;
				//$consent_form_content = str_ireplace('{SIGNATURE}',$sig_data,$consent_form_content);
			}
			if($conArr)$consent_form_content=implode(' ',$conArr);
		}
	//}
		//-- get signature value -------
		$qry = imw_query("select form_information_id from patient_consent_form_information 
				where consent_form_id = '$consent_form_id' and patient_id = '$patient_id'");
		$consentDetailsInfo = '';
		if($qry && imw_num_rows($qry) > 0) $consentDetailsInfo = imw_fetch_assoc($qry);
		
		$form_information_id = $consentDetailsInfo['form_information_id'];
		/*$qry = "select signature_content from consent_form_signature 
				where form_information_id = '$form_information_id' and patient_id = '$patient_id'
				and consent_form_id = '$consent_form_id' and signature_status = 'Active' order by signature_count";*/
		
		$qry = imw_query("select signature_image_path,signature_count from consent_form_signature 
				where form_information_id = '$form_information_id' and patient_id = '$patient_id'
				and consent_form_id = '$consent_form_id' and signature_status = 'Active' order by signature_count");
		/*$qry = "select signature_content from consent_form_signature 
			where patient_id = '$patient_id'
			and consent_form_id = '$consent_form_id' and signature_status = 'Active'";*/
		 $sigDetail = false;
		 if($qry && imw_num_rows($qry) > 0){
			while($row112 = imw_fetch_assoc($qry)){
				$sigDetail[] = $row112;
			}
		 }
		 //$sigDetail = ManageData::getQryRes($qry);
	
		 if ($sigDetail){
	
		$sig_con = array();
	//echo	count($sigDetail).'<br>';
	
	//echo $sigDetail[0]['signature_content'].'<br>';
	//exit;
		for($s=0;$s<count($sigDetail);$s++){
			$sig_con[$s] = $sigDetail[$s]['signature_image_path'];
			$signature_count[$s] = $sigDetail[$s]['signature_count'];
		}
		//echo count($signature_count).'<br>';
		//exit;
	//echo $sig_con[1].'<br>';
	//exit;
	//echo count($sig_con).'<br>';
	
		 
		 
		//$sig_con = $sigDetail[$s]['signature_content'];
		//////////////////
		
	$deletePath=array();
		for($ps=0;$ps<count($sig_con);$ps++){
		//$postData = $sig_con[$ps];
		////////////new pad
		$row_arr = explode('{START APPLET ROW}',$consent_form_content);
		$sig_arr = explode('{SIGNATURE}',$row_arr[0]);	
		//print_r($sig_arr);
		//exit;
		
		$sig_data = '';
		//$path1 = split("/",$postData);
		$ds=0;
		$coun=0;
		for($s=1;$s<count($sig_arr);$s++){
				if($s==$signature_count[$ds]){
					$postData = $sig_con[$coun];
					$path1 = split("/",$postData);
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
	
		
		}
	}
	else
	{
		
		$conArr=explode('{SIGNATURE}',$consent_form_content);
		for($r=1;$r<count($conArr);$r++)
		{
			$fileName=createBlankImg($_REQUEST['patient_id'],$_REQUEST['phyId']);
			$sigPats[$fileName[1]]=$fileName[0];
			$sig_data = '<span width="145" >
							<a name="typ_sig" href="'.$fileName[1].'" id="'.$fileName[1].'">
							<div style="HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;border:solid 1px; border-color:#FF9900;">
							<img src="'.$fileName[0].'"  /></div></a>
						</span>';
		$str_data = $conArr[$s];
		$conArr[$r] = $sig_data;
		$conArr[$r] .= $str_data;
		//$consent_form_content = str_ireplace('{SIGNATURE}',$sig_data,$consent_form_content);
		}
		if($conArr)$consent_form_content=implode(' ',$conArr);
	}
	//echo $consent_form_content;die;	
		//--- get all content of consent forms -------	
		$consent_content .= '
			<table id="content_'.$consent_form_id.'" style="display:'.$display.'" width="100%" align="center" cellpadding="1" cellspacing="1" border="0">
				<tr>
					<td align="center" >'.$consent_form_content.'</td>
				</tr>
			</table>
		';
	
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
	//echo $consent_form_content;die;
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
	//$barCodeImgPath = '../../common/'.$htmlFolder.'/bar_code_images/'.$img_name.'.png';
	$barCodeImgPath = data_path().'PatientId_'.$patient_id.'/consent_forms/bar_code_images/'.$img_name.'.png';
	if(!is_dir(data_path().'PatientId_'.$patient_id.'/consent_forms/bar_code_images/')) {
		mkdir(data_path().'PatientId_'.$patient_id.'/consent_forms/bar_code_images/', 0777, true);	
	}
	/* if(!is_dir('../../common/'.$htmlFolder.'/bar_code_images/patient_id_'.$patient_id)) {
		mkdir('../../common/'.$htmlFolder.'/bar_code_images/patient_id_'.$patient_id, 0777);	
	} */
	
	generate_barcode($Barcode_Text,$img_name,$barCodeImgPath); 
	$barCodeImgPath = data_path(1).'PatientId_'.$patient_id.'/consent_forms/bar_code_images/'.$img_name.'.png';
	$barCodeContent = '<img src="'.$barCodeImgPath.'">';
	
	$page_bar_code='<table style="width:700px;" cellpadding="0" cellspacing="0"><tr>
					<td style="text-align:right;width:700px;">'.	$barCodeContent.'</td></tr><tr>
					<td style="text-align:right;width:700px;">'.$img_name.'</td></tr></table>';
	$backtop="10mm";
	$page_bar_code_v1='<p align="right" style="margin-top:-15px;">'.$barCodeContent.'</p><p align="right">'.$img_name.'</p>';
	//if(constant("BAR_CODE_DISABLE")=='YES'){
		$page_bar_code='';$backtop="0mm";$page_bar_code_v1='';
	//}
	
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
	
	if($htmlV2Class==true){
		$consent_form_content='<page backtop="'.$backtop.'" backbottom="5mm"><page_header>
		'.$page_bar_code.'</page_header>
		<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%" class="text_value">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
		</page_footer>
		
		<table border="0" cellpadding="0" cellspacing="0" width="100%;">
			
			<tr>
				<td style="width:100%;font-size:15px;" class="text_value">
					'.$consent_form_content.'
				</td>
			</tr>
		</table>
		'."</page>";
		$consent_form_content=str_ireplace("<div st</page>","</page>",$consent_form_content);
		return $consent_form_content;	
	}
}
?>