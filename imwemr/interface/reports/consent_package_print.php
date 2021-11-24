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
FILE : surgery_appointment_facesheet_print.php
PURPOSE : Display results of Surgery Appointments report
ACCESS TYPE : Direct
*/

//Function files
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");

header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");
set_time_limit(900);
include_once($GLOBALS['fileroot'].'/library/bar_code/code128/code128.class.php');
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
$oSaveFile = new SaveFile($patient_id);
$oSaveFile->ptDir("consent_forms");
$dir = explode('/',$_SERVER['HTTP_REFERER']);
$httpPro = $dir[0];
$myHTTPAddress = $httpPro.'//'.$myInternalIP.'/'.$web_RootDirectoryName.'/interface/main/print_fun_patient_package.php';

//--------------	FUNCTION TO GET K HEADING NAMES	---------------//
$getKreadingIdStr = "SELECT * FROM kheadingnames ORDER BY kheadingId";
$getKreadingIdQry = imw_query($getKreadingIdStr);
$kReadingHeadingNameArr = array();
while($getKreadingIdRow = imw_fetch_array($getKreadingIdQry)) {		
	if(strpos($getKreadingIdRow['kheadingName'], "K[")===false){$getKreadingIdRow['kheadingName'] = 'K['.$getKreadingIdRow['kheadingName'];}
	if(strpos($getKreadingIdRow['kheadingName'], "]")===false){$getKreadingIdRow['kheadingName'] = $getKreadingIdRow['kheadingName']."]";}		
	$kheadingId = $getKreadingIdRow['kheadingId'];
	$kReadingHeadingNameArr[$kheadingId] = $getKreadingIdRow['kheadingName'];		
}
//--------------	FUNCTION TO GET K HEADING NAMES	---------------//

//--------------	FUNCTION TO GET LENSES FORMULA HEADING NAME	---------------//
$formula_heading_name_arr = array();
$getFormulaheadingsStr = "SELECT * FROM formulaheadings ORDER BY formula_id";
$getFormulaheadingsQry = imw_query($getFormulaheadingsStr);
while($getFormulaheadingsRow = imw_fetch_array($getFormulaheadingsQry)) {
	$get_formula_id = $getFormulaheadingsRow['formula_id'];
	$formula_heading_name_arr[$get_formula_id] = $getFormulaheadingsRow['formula_heading_name'];
}

function getFormulaHeadNameIolMaster($id){
	global $formula_heading_name_arr;
	$formula_heading_name = $formula_heading_name_arr[$id];
	return $formula_heading_name;
}
//--------------	FUNCTION TO GET LENSES FORMULA HEADING NAME	---------------//

//================= FUNCTION TO GET LENSE TYPE
$lenses_iol_type_arr = array();
$getLenseTypeStr = "SELECT * FROM lenses_iol_type ORDER BY iol_type_id";
$getLenseTypeQry = imw_query($getLenseTypeStr);
while($getLenseTypeRow = imw_fetch_array($getLenseTypeQry)) {
	$get_iol_type_id = $getLenseTypeRow['iol_type_id'];
	$lenses_iol_type = $getLenseTypeRow['lenses_iol_type'];
	$lenses_iol_type_arr[$get_iol_type_id] = $getLenseTypeRow['lenses_iol_type'];
}
//================= FUNCTION TO GET LENSE TYPE

//================= START FUNCTION FOR LENSES DEFINED TO PROVIDER OD
function getLensesIolMaster($provider_id){
	$getLensesForProviderStr = "SELECT * FROM lensesdefined a,
								lenses_iol_type b
								WHERE a.physician_id = '$provider_id'
								AND a.iol_type_id = b.iol_type_id";
	$getLensesForProviderQry = imw_query($getLensesForProviderStr);
	if(imw_num_rows($getLensesForProviderQry)>0){
		while($getLensesForProviderRows = imw_fetch_array($getLensesForProviderQry)){
			$iol_type_id = $getLensesForProviderRows['iol_type_id'];
			$providersLenses = $getLensesForProviderRows['lenses_iol_type'];
			$lensesProviderArray[$iol_type_id] = $providersLenses;
		}
	}
	return $lensesProviderArray;
}
//================= START FUNCTION FOR LENSES DEFINED TO PROVIDER OD

//END FUNCTIONS FOR IOL INFORMATION

//START CODE TO SET NEW CLASS TO CONSENT FORMS
$htmlFolder = "html_to_pdf";
$htmlV2Class=true;	
$htmlFilePth = "html_to_pdf/createPdf.php";




//END CODE TO SET NEW CLASS TO CONSENT FORMS

/*
$consent_package_template = $_REQUEST["consent_package_template"];
$consent_package_template_explode = explode("@@",$consent_package_template);
$packageCategoryId = $consent_package_template_explode[0];
$package_consent_form = $consent_package_template_explode[1];
*/
$hidd_selected_patient_id 	= $_REQUEST["hidd_selected_patient_id"];
$packageCategoryId 			= $_REQUEST["consent_package_template"];
$chk_latest_chart 			= $_REQUEST["chk_latest_chart"];
$chk_iol 					= $_REQUEST["chk_iol"];
$packageCategoryId = implode(',',$packageCategoryId);

//in case all facilities are selected
$str_rep_fac = implode(",", $rep_fac);
if($rep_fac == ''){
	$qry = imw_query("select id from facility");
	$res = array();
	$fac_arr = array();
	while ($fac_res = imw_fetch_assoc($qry)) {
		$res[] = $fac_res;
	}
	for($i=0;$i<count($res);$i++){
		$fac_arr[] = $res[$i]['id'];
	}
	$str_rep_fac = implode(',',$fac_arr);
}

//getting selected provider ids
$strProviderIds = implode(",",$providerID);
if(trim($strProviderIds) == ""){
	$provQry = imw_query("select id from users where Enable_Scheduler = 1");
	$provQryRes = array();
	$$provid = array();
	while ($pro_res = imw_fetch_assoc($provQry)) {
		$provQryRes[] = $pro_res;
	}
	for($i=0;$i<count($provQryRes);$i++){
		$provid[] = $provQryRes[$i]['id'];
	}
	$strProviderIds = implode(',',$provid);
}

$blIncludePatientAddress = false;
if(isset($_REQUEST['include_pat_Add']) && $_REQUEST['include_pat_Add'] == 1){
	$blIncludePatientAddress = true;
}

//changing date format
$dtEffectiveDate = $_REQUEST['eff_date'];
list($m,$d,$y) = preg_split('/-/', $dtEffectiveDate);
$dtDBEffectDate = $y.'-'.$m.'-'.$d;

$intTimeStamp = mktime(0, 0, 0, $m, $d, $y);
$dtShowEffectDate = date("m/d/Y", $intTimeStamp);
$strDayName = date('l', $intTimeStamp);

//getting week
$week = ceil($d/7);

//getting week day
$weekDay = date("N",$intTimeStamp);

$andQry = "";
if(trim($str_rep_fac)) {
	$andQry .= "AND sa.sa_facility_id IN (".$str_rep_fac.") ";	
}
if(trim($strProviderIds)) {
	$andQry .= "AND sa.sa_doctor_id IN (".$strProviderIds.") ";	
}
if(trim($dtDBEffectDate)) {
	$andQry .= "AND ('".$dtDBEffectDate."' BETWEEN sa.sa_app_start_date AND sa.sa_app_end_date) ";	
}

$hidd_selected_patient_id_arr = array();
if(trim($hidd_selected_patient_id)) {
	$hidd_selected_patient_id_arr = explode(",",$hidd_selected_patient_id);
}

$pckgQry = "SELECT distinct(sa.sa_patient_id) as patient_id,sa_app_start_date,
			CONCAT(p.lname,', ',p.fname,' ',p.mname,' - ',sa.sa_patient_id) as pname,
			CONCAT(p.lname,', ',p.fname,' ',p.mname) as patientName,p.DOB,
			DATE_FORMAT(p.DOB,'".get_sql_date_format()."') as date_of_birth
			FROM schedule_appointments sa
			LEFT JOIN patient_data p ON p.id = sa.sa_patient_id  
			WHERE sa.sa_patient_app_status_id NOT IN(201,18,203) ".$andQry." ORDER BY sa.sa_patient_id";//die($pckgQry);
$pckgRes = imw_query($pckgQry) or die($pckgQry.imw_error());
$patient_print_data = "";
$queryStaringString = "";
$pckgNumRow = imw_num_rows($pckgRes);
$patientIdArr = array();
$patientInfoArr = array();
if($pckgNumRow > 0){
	$k=0;
	while($pckgRow = imw_fetch_assoc($pckgRes)) {
		$getSqlDateFormat = get_sql_date_format();
		$getSqlDateFormatSmall = str_replace("Y","y",get_sql_date_format());
		$browserIpad = 'no';
		if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true) {
			$browserIpad = 'yes';
		}
		$patient_id = $pckgRow["patient_id"];
		
		$patient_name = $pckgRow["pname"];
		$patientName = $pckgRow["patientName"];
		$patient_date_of_birth = $pckgRow["date_of_birth"];
		$patient_age = show_age($pckgRow["DOB"]);
		
		//$patientIdArr[] = $patient_id;
		$patientInfoArr[$k]['patient_id'] 				= $patient_id;
		$patientInfoArr[$k]['patient_name'] 			= $patient_name;
		$patientInfoArr[$k]['patient_date_of_birth'] 	= $patient_date_of_birth;
		$patientInfoArr[$k]['patient_age'] 				= $patient_age;
		$patientInfoArr[$k]['patient_dob_age'] 			= $patient_date_of_birth.' ('.$patient_age.')';
		
		//---- get Patient details ---------
		$qry = "select *,date_format(DOB,'$getSqlDateFormat') as pat_dob,date_format(date,'$getSqlDateFormat') as reg_date,
				CONCAT(lname,', ',fname,' ',mname) as name,date_format(DOB,'$getSqlDateFormat') as DOB
				from patient_data where id = '$patient_id'";
		$qryRes = imw_query($qry);
		$patientDetails = array();
		while($qryRow = imw_fetch_assoc($qryRes)) {
			$patientDetails[] = $qryRow;
		}
		$patientDetails[0]["age"] = $patient_age;
		$patient_initial = substr($patientDetails[0]['fname'],0,1);
		$patient_initial .= substr($patientDetails[0]['lname'],0,1);
		
		//--- get physician name --------
		$pro_id = $patientDetails[0]['providerID'];
		$qry = imw_query("select concat(lname,', ',fname) as name,mname from users where id = '$pro_id'");
		$phyDetail = array();
		while($qryRow = imw_fetch_assoc($qry)) {
			$phyDetail[] = $qryRow;
		}
		$phy_name = ucwords(trim($phyDetail[0]['name'].' '.$phyDetail[0]['mname']));
		//--- get reffering physician name --------
		$primary_care_id = $patientDetails[0]['primary_care_id'];
		$qry = "select concat(LastName,', ',FirstName) as name , MiddleName,Title, specialty, physician_phone, Address1, Address2, ZipCode, City, State from refferphysician
				where physician_Reffer_id = '$primary_care_id'";
		$qryRes = imw_query($qry);
		$reffPhyDetail = array();
		while($qryRow = imw_fetch_assoc($qryRes)) {
			$reffPhyDetail[] = $qryRow;
		}
		$reffer_name = ucwords(trim($reffPhyDetail[0]['name'].' '.$reffPhyDetail[0]['MiddleName']));
		$refPhyAddress="";
		$refPhyAddress .= (!empty($reffPhyDetail[0]['Address1'])) ? trim($reffPhyDetail[0]['Address1']) : "";
		$refPhyAddress .= (!empty($reffPhyDetail[0]['Address2'])) ? "<br>".trim($reffPhyDetail[0]['Address2']) : "";
		
		//--- get pos facility name -------
		$default_facility = $patientDetails[0]['default_facility'];
		$qry = "select facilityPracCode from pos_facilityies_tbl 
				where pos_facility_id = '$default_facility'";
		$qryRes = imw_query($qry);
		$posFacilityDetail = array();
		while($qryRow = imw_fetch_assoc($qryRes)) {
			$posFacilityDetail[] = $qryRow;
		}
		$pos_facility_name = $posFacilityDetail[0]['facilityPracCode'];
		//--- get responsible party information ------
		$qry = "select *,date_format(dob,'$getSqlDateFormat') as res_dob 
				from resp_party where patient_id = '$patient_id'";
		$qryRes = imw_query($qry);
		$resDetails = array();
		while($qryRow = imw_fetch_assoc($qryRes)) {
			$resDetails[] = $qryRow;
		}		
		//--- get epmoyee detail of patient ---
		$qry = "select * from employer_data where pid = '$patient_id'";
		$qryRes = imw_query($qry);
		$empDetails = array();
		while($qryRow = imw_fetch_assoc($qryRes)) {
			$empDetails[] = $qryRow;
		}
		if($packageCategoryId) {
			
			$pckQry = "SELECT package_category_id, package_category_name, package_consent_form FROM consent_package WHERE package_category_id='".$packageCategoryId."' ORDER BY package_category_name";	
			$qryRes = imw_query($pckQry);
			$pckDetail = array();
			while($qryRow = imw_fetch_assoc($qryRes)) {
				$pckDetail[] = $qryRow;
			}
			if(count($pckDetail) > 0){
				for($i_pck=0;$i_pck<count($pckDetail);$i_pck++){	
					$package_category_id = $pckDetail[$i_pck]['package_category_id'];
					$package_consent_form = $pckDetail[$i_pck]['package_consent_form'];
				}
			}
			
			//start creating barcode image
			$barCodePatientId = $patient_id;
			$barCodeFolderId = $packageCategoryId;
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
			$page_bar_code='<page backtop="'.$backtop.'" backbottom="5mm" orientation="portrait">
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
		$consent_form_content='';
		//if(count($consentDetail) == 0){
		$qry = "SELECT cf.*,cf.consent_form_content AS consent_form_content_data, cf.cat_id AS consent_cat_id FROM consent_form cf 
				INNER JOIN consent_category cc ON (cc.cat_id=cf.cat_id)
				WHERE cf.consent_form_id IN(0,".$package_consent_form.")
				ORDER BY cf.consent_form_name ";
		$qryRes = imw_query($qry);
		$consentDetail = array();
		while($qryRow = imw_fetch_assoc($qryRes)) {
			$consentDetail[] = $qryRow;
		}		
		$pageTag="";
		for($i_csn=0;$i_csn<count($consentDetail);$i_csn++){
			
			if($htmlV2Class==true){
				$pageTag = $page_bar_code;
			}else {
				//$pageTag = $consentDetail[$i_csn]['consent_form_content'];	
			}
			$consent_form_content .= $pageTag.$consentDetail[$i_csn]['consent_form_content'];
			//$consent_form_content .= $pageTag;
			
		}
		//}
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
							 where pid = '".$patient_id."' and 
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
							 where pid = '".$patient_id."' and 
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
		$qryRes = imw_query($qry);
		$pcpPhyDetail = array();
		while($qryRow = imw_fetch_assoc($qryRes)) {
			$pcpPhyDetail[] = $qryRow;
		}
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
							 where sa.sa_patient_id = '".$patient_id."' and 
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
			$consent_cat_id = $consentDetail[$i]['consent_cat_id'];//$patient_id
			//--- change value between curly brackets -------	
			
			
			$consent_form_content = str_ireplace('{PATIENT NAME TITLE}',ucwords($patientDetails[0]['title']),$consent_form_content);
			$consent_form_content = str_ireplace($web_root.'/interface/common/'.$htmlFolder.'/','',$consent_form_content);
			$consent_form_content = str_ireplace($web_root.'/interface/common/html2pdf/','',$consent_form_content);
			$consent_form_content = str_ireplace($web_root.'/interface/common/new_html2pdf/','',$consent_form_content);
			$consent_form_content = str_ireplace('interface/common/html2pdf/','',$consent_form_content);
			$consent_form_content = str_ireplace('interface/common/new_html2pdf/','',$consent_form_content);
			$consent_form_content = str_ireplace('%20',' ',$consent_form_content);
			$consent_form_content = str_ireplace('{PATIENT FIRST NAME}',ucwords($patientDetails[0]['fname']),$consent_form_content);
			$consent_form_content = str_ireplace('{MIDDLE NAME}',ucwords($patientDetails[0]['mname']),$consent_form_content);
			$consent_form_content = str_ireplace('{LAST NAME}',ucwords($patientDetails[0]['lname']),$consent_form_content);
			$consent_form_content = str_ireplace('{SEX}',ucwords($patientDetails[0]['sex']),$consent_form_content);
			$consent_form_content = str_ireplace('{DOB}',ucwords($patientDetails[0]['pat_dob']),$consent_form_content);
			$consent_form_content = str_ireplace('{PATIENT SS}',ucwords($patientDetails[0]['ss']),$consent_form_content);
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
		
		//==============RESPONSIBLE PARTY VARIABLE REPLACEMENT STARTS HERE==============
		//==IF PATIENT HAVE NO RESPONSIBLE PERSON THEN PATIENT DETAILS WILL REPLACE WITH RESPONSIBLE PERSON DETAILS
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
			$consent_form_content = str_ireplace('{RES.PARTY DD NUMBER}',ucwords($patientDetails[0]['driving_licence']),$consent_form_content);
		
		}
		//=========================RESPONSIBLE PARTY DATA REPLACEMENT ENDS HERE==========================		
			
			//--- change epmoyee detail of patient ---
			$consent_form_content = str_ireplace('{PATIENT OCCUPATION}',ucwords($patientDetails[0]['occupation']),$consent_form_content);
			$consent_form_content = str_ireplace('{PATIENT EMPLOYER}',ucwords($empDetails[0]['name']),$consent_form_content);
			$consent_form_content = str_ireplace('{OCCUPATION ADDRESS1}',ucwords($empDetails[0]['street']),$consent_form_content);
			$consent_form_content = str_ireplace('{OCCUPATION ADDRESS2}',ucwords($empDetails[0]['street2']),$consent_form_content);
			$consent_form_content = str_ireplace('{OCCUPATION CITY}',ucwords($empDetails[0]['city']),$consent_form_content);
			$consent_form_content = str_ireplace('{OCCUPATION STATE}',ucwords($empDetails[0]['state']),$consent_form_content);
			$consent_form_content = str_ireplace('{OCCUPATION ZIP}',ucwords($empDetails[0]['postal_code']),$consent_form_content);
			$consent_form_content = str_ireplace('{MONTHLY INCOME}',''.showCurrency().''.number_format($patientDetails[0]['monthly_income'],2),$consent_form_content);
			$consent_form_content = str_ireplace('{DATE}',date(''.phpDateFormat().''),$consent_form_content);
			$consent_form_content = str_ireplace('{DATE_F}',date("F d, Y"),$consent_form_content);
			$consent_form_content = str_ireplace('{TIME}',date('h:i A'),$consent_form_content);
			$consent_form_content = str_ireplace('{OPERATOR NAME}',ucwords(trim($operator_name)),$consent_form_content);
			$consent_form_content = str_ireplace('{OPERATOR INITIAL}',ucwords($operator_initial),$consent_form_content);
			$consent_form_content = str_ireplace('{PATIENT INITIAL}',ucwords($patient_initial),$consent_form_content);
			$consent_form_content = str_ireplace('{TEXTBOX_XSMALL}',"_____",$consent_form_content);
			$consent_form_content = str_ireplace('{TEXTBOX_SMALL}',"__________",$consent_form_content);
			$consent_form_content = str_ireplace('{TEXTBOX_MEDIUM}',"_______________",$consent_form_content);
			$consent_form_content = str_ireplace('{TEXTBOX_LARGE}',"____________________",$consent_form_content);
			
			$consent_form_content = str_ireplace('{TEXTBOX_LARGE}',"____________________",$consent_form_content);
			//replacing Primary insurence data
			$consent_form_content = str_ireplace('{PRIMARY INSURANCE COMPANY}',ucwords($priInsCompName),$consent_form_content);
			$consent_form_content = str_ireplace('{PRIMARY POLICY #}',ucwords($priPolicyNumber),$consent_form_content);
			$consent_form_content = str_ireplace('{PRIMARY GROUP #}',ucwords($priGroupNumber),$consent_form_content);
			$consent_form_content = str_ireplace('{PRIMARY SUBSCRIBER NAME}',ucwords($priSubscriberName),$consent_form_content);
			$consent_form_content = str_ireplace('{PRIMARY SUBSCRIBER RELATIONSHIP}',ucwords($priSubscriberRelation),$consent_form_content);
			$consent_form_content = str_ireplace('{PRIMARY BIRTHDATE}',ucwords($priSubscriberDOB),$consent_form_content);
			$consent_form_content = str_ireplace('{PRIMARY SOCIAL SECURTIY}',ucwords($priSubscriberSS),$consent_form_content);
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
			$consent_form_content = str_ireplace('{SECONDARY SOCIAL SECURTIY}',ucwords($secSubscriberSS),$consent_form_content);
			$consent_form_content = str_ireplace('{SECONDARY PHONE}',ucwords($secSubscriberPhone),$consent_form_content);
			$consent_form_content = str_ireplace('{SECONDARY ADDRESS}',ucwords($secSubscriberStreet),$consent_form_content);
			$consent_form_content = str_ireplace('{SECONDARY CITY}',ucwords($secSubscriberCity),$consent_form_content);
			$consent_form_content = str_ireplace('{SECONDARY STATE}',ucwords($secSubscriberState),$consent_form_content);
			$consent_form_content = str_ireplace('{SECONDARY ZIP}',ucwords($secSubscriberZip),$consent_form_content);
			$consent_form_content = str_ireplace('{SECONDARY EMPLOYER}',ucwords($secSubscriberEmployer),$consent_form_content);
			$consent_form_content = str_ireplace('{PatientID}',$patient_id,$consent_form_content);
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
			
			
			
			//
			$consent_form_content = str_ireplace($web_root."/interface/SigPlus_images/","",$consent_form_content);
			$consent_form_content = str_ireplace("{PHYSICIAN SIGNATURE}","",$consent_form_content);	
			$consent_form_content = str_ireplace("{WITNESS SIGNATURE}","",$consent_form_content);	
			
			if ($chk == 0)
			{
				$consent_form_content = str_ireplace('{SIGNATURE}',"",$consent_form_content);
				
			}
		}
			
			$qry = "select signature_image_path,signature_count from consent_form_signature 
					where form_information_id = '$form_information_id' and patient_id = '$patient_id'
					and consent_form_id = '$consent_form_id' and signature_status = 'Active' order by signature_count";
			$qryRes = imw_query($pckQry);
			$sigDetail = array();
			while($qryRow = imw_fetch_assoc($qryRes)) {
				$sigDetail[] = $qryRow;
			}
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
					$content_row .= '<br>'.$td_sign;
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
				$consent_form_content = str_ireplace('<textarea rows="2" cols="100" name="large">','____________________',$consent_form_content);
				$consent_form_content = str_ireplace('<textarea rows="2" cols="100" name="large'.$i.'">','____________________',$consent_form_content);
				$consent_form_content = str_ireplace('</textarea>','____________________',$consent_form_content);
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
	
		if(trim($hidd_selected_patient_id)) {
			if(in_array($patient_id,$hidd_selected_patient_id_arr)) {
				$patient_print_data .= $consent_form_content;	
				if($chk_latest_chart=='1' || $chk_iol=='1') {
					if($chk_latest_chart == '1') {
						//get chart latest dos
						$chrtMstrQry = "SELECT id,date_of_service FROM chart_master_table WHERE delete_status='0' AND purge_status='0' AND date_of_service <= '".$dtDBEffectDate."' AND patient_id = '".$patient_id."' ORDER BY date_of_service DESC, id DESC LIMIT 0,1 ";
						$qryRes = imw_query($chrtMstrQry);
						$chrtMstrDetails = array();
						while($qryRow = imw_fetch_assoc($qryRes)) {
							$chrtMstrDetails[] = $qryRow;
						}
						$chrt_date_of_service = $chrtMstrDetails[0]["date_of_service"];
						$chrt_id = $chrtMstrDetails[0]["id"];
						
						$dataChartHTML = "";
						$data1 = "";
						$curNew = curl_init();
						$urlPdfFile = $myHTTPAddress;
						$urlPdfFile = $myHTTPAddress;
						
						$postArr=array();
						$postArr['patient'] 				= $patient_id;
						$postArr['form_id'] 				= $chrt_id;
						$postArr['chrt_date_of_service'] 	= $chrt_date_of_service;
						$postArr['chk_latest_chart'] 		= $chk_latest_chart;
						$postArr['chk_iol'] 				= $chk_iol;
						$postArr['authId'] 					= $_SESSION['authId'];
						curl_setopt($curNew, CURLOPT_URL,$GLOBALS['php_server'].'/interface/patient_info/complete_pt_rec/print_fun_patient_package.php');
						curl_setopt($curNew, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($curNew, CURLOPT_SSL_VERIFYPEER, false); 
						curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
						curl_setopt($curNew, CURLOPT_POSTFIELDS,$postArr); 
						$data1 = curl_exec($curNew);
						//$data1 = strip_tags('<table> <tr> <td> <tbody> <thead> <p> <img> <span> <page> <page_header> <page_footer> <br> <br/>',$data1);
						if (curl_errno($curNew)){
							echo  "Curl Error iDOC to iASC Link: " . curl_error($curNew). " ";
						}else {
							$file_path = $GLOBALS['fileroot']."/library/html_to_pdf/pdffile.html";
							$dataChartHTML = file_get_contents($file_path, true);
						}
						//print_r(curl_getinfo($curNew));
						curl_close($curNew);
					}
					
					
					$patient_print_data .=$dataChartHTML;
										
					//GET IOL
					
					if($chk_iol == '1') {
						$iol_package_html_file_name = $GLOBALS['fileroot']."/library/html_to_pdf/iol_package_".$_SESSION['authId'].".html";
						if(file_exists($iol_package_html_file_name)) {
							unlink($iol_package_html_file_name);	
						}
						
						include_once($GLOBALS['fileroot']."/interface/patient_info/complete_pt_rec/print_iol_package.php");
						$iolDataHTML = "";
						if(file_exists($iol_package_html_file_name)) {
							$iolDataHTML = file_get_contents($iol_package_html_file_name, true);
							$file_path = $GLOBALS['fileroot']."/library/html_to_pdf/pdffile.html";
							$dataChartHTML = file_get_contents($file_path, true);
							
							
							
							$iolDataHTML .=
							'<style>
							table{ font-size:10px;}
							td{ font-size:11px;}
							.tb_heading{
								font-size:11px;
								font-weight:bold;
								color:#000;
								background-color:#C0C0C0;
								margin-top:10;
								padding:3px 0px 3px 0px;
								vertical-align:middle;
								width:100%;
							}
							</style>';	
						}
						$patient_print_data .= $iolDataHTML;
					}
				}
				//end chart
				
			}
		}else {
			$patient_print_data .= '&nbsp;';	
		}

		
		$k++;
	}//die;
}

//--- GET PDF FILE -----

if(empty($patient_print_data) == false){
	
	$printFile = 0;
	//--- CREATE HTML FILE FOR PDF PRINTING ---
	$patient_print_data = utf8_decode($patient_print_data);
	$patient_print_data = html_entity_decode($patient_print_data);
	$file_location = write_html($patient_print_data);
	
	$con_pac_print = 1;
	echo '<table class="table table-bordered table-hover table-striped" bgcolor="#FFF3E8">
		<tr>
			<th class="text_b_w valignTop" width="5" align="center" style="text-align:center;"><input type="checkbox" onclick="checkAllChkBoxConsent(this);"></th>
			<th class="text_b_w" width="50" align="Left">Patient Name-ID</th>
			<th class="text_b_w" width="320" align="Left">DOB (Age)</th>
		</tr>
	';
    $slected_pt = array();
	if(isset($_REQUEST['hidd_selected_patient_id']) && empty($_REQUEST['hidd_selected_patient_id']) == false){
		$slected_pt = explode(',',$_REQUEST['hidd_selected_patient_id']);
	}
	foreach($patientInfoArr as $key => $val){
		$checked = (in_array($patientInfoArr[$key]['patient_id'],$slected_pt)) ? "CHECKED" : "";
		echo '<tr>
        <td class="valignTop" align="center" bgcolor="#FFFFFF"><input class="chk_box_package" '.$checked.' type="checkbox" name="patient_id_arr[]" value='.$patientInfoArr[$key]['patient_id'].'></td>
        <td class="text_10 valignTop" bgcolor="#FFFFFF">'.$patientInfoArr[$key]['patient_name'].'</td>
        <td class="text_10 valignTop" bgcolor="#FFFFFF">'.$patientInfoArr[$key]['patient_dob_age'].'</td>
    </tr>';
	}
echo '</table>';
} else{
	echo '<div class="text-center alert alert-info">No Recod Exists.</div>';
	$con_pac_print = 0;
}