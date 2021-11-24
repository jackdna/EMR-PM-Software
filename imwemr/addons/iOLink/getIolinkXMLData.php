<?php
$ignoreAuth = true;
require_once(dirname(__FILE__).'/../../config/globals.php');
//include_once("../../library/classes/admin/documents/encoding.php");
//include_once("../../library/html_to_pdf/html2pdf.class.php");
//$link=imw_connect($host_name,$login,$pass) or die(imw_error());
//imw_select_db($dbase);
extract($_REQUEST);
//print "<pre>";
//print_r($_REQUEST);
//exit;
$schedule_id = $_REQUEST['schedule_id'];
$mode = $_REQUEST['mode'];
$httpfolder =  data_path().'iOLink';//realpath(dirname(__FILE__));
//$httpfolder = str_ireplace("\\","/",$httpfolder);
$sa_date_pattern = date('Ymd',strtotime($sa_date));
if($mode == "send" || $mode == "resync"){
//START FUNCTION TO GET SCAN CONTENT
function getInsScanContent($insurenceDataScanCardPath) {
	$insurenceDataScanFolder = data_path();
	$insurenceDataScanCard='';
	if($insurenceDataScanCardPath) {
		$insurenceDataScanCardGetPath = $insurenceDataScanFolder.$insurenceDataScanCardPath;
		$insurenceDataScanCardGetPath = realpath($insurenceDataScanCardGetPath);
		clearstatcache();
		if (file_exists($insurenceDataScanCardGetPath)) {	
			$insurenceDataScanCardContents = file_get_contents($insurenceDataScanCardGetPath);	
			$insurenceDataScanCard = addslashes(base64_encode($insurenceDataScanCardContents));
		}
	}
	return $insurenceDataScanCard;
}
//END FUNCTION TO GET SCAN CONTENT

function getFilesFromFolder($path,$type,$nameMatch){
	$arr=array();
	foreach(glob($path."/*.".$type) as $pdf_file){
		$filename="";
		$filename=basename($pdf_file);
		$strLenCnt = strlen($nameMatch);
		if(substr($filename,0,$strLenCnt)==$nameMatch){
			$arr[]=$filename;
		}
	}	
	return $arr;
}

function getLensesData($lenseTypeId,$field)
{
	global $lenses_iol_type_arr;
	return ($lenses_iol_type_arr[$lenseTypeId][$field]) ? $lenses_iol_type_arr[$lenseTypeId][$field] : '' ;	
}

//START CODE TO GET DEFAULT CASE
$defaultCaseId	='';
$defaultCaseName='';
$defaultCaseTypeQry 	= "SELECT case_id FROM insurance_case_types WHERE normal = '1'";
$defaultCaseTypeRes 	= imw_query($defaultCaseTypeQry)or die(imw_error());
if($defaultCaseTypeRes){
	if(imw_num_rows($defaultCaseTypeRes)>0){
		$defaultCaseTypeNumRow 	= imw_num_rows($defaultCaseTypeRes);
		$defaultCaseTypeRow = imw_fetch_array($defaultCaseTypeRes);
		$defaultCaseId 		= $defaultCaseTypeRow['case_id'];
		$defaultCaseName 	= $defaultCaseTypeRow['case_name'];
	}
}
//END CODE TO GET DEFAULT CASE

$strQryGetPatientDemographicData="SELECT pd.* FROM schedule_appointments sa
									INNER JOIN patient_data pd ON pd.pid = sa.sa_patient_id											
									WHERE sa.id = ".$schedule_id;
									
$rsQryGetPatientDemographicData = imw_query($strQryGetPatientDemographicData);
/*if($rsQryGetPatientDemographicData){
$demographicDataRow = imw_fetch_array($rsQryGetPatientDemographicData);
extract($demographicDataRow);
}*/
/////////////////////////////////////////////////////////////////
if(!$rsQryGetPatientDemographicData){
	echo ("Error : ". imw_error()."<br>".$strQryGetPatientDemographicData);
}
else{
	$demographicdata = array();
	$a = 0;
	if(imw_num_rows($rsQryGetPatientDemographicData)>0){		
		$aa = "?>";
		$demographicDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
		$demographicDataXML .= "<demographicData>";
		while ($row = imw_fetch_array($rsQryGetPatientDemographicData)) {
			//echo '<pre>';
			//print_r($row); 
			$patientId = $row['pid'];
			$patientName = $row['fname'].'_'.$row['lname'];
			$patientfname = $row['fname'];
			$patientlname = $row['lname'];
			$patientmname = $row['mname'];

			//get patient religion field value from misc. table if exists
			$pt_religion = '';
			$qryR = "select id From custom_fields where control_lable = 'religion' "; 
			$sqlR = imw_query($qryR) or die(imw_error());
			if( imw_num_rows($sqlR) > 0 ) {
				$resR = imw_fetch_assoc($sqlR);
				$qryR2 = "select TRIM(patient_control_value) as religion_val  From patient_custom_field where patient_id = ".(int)$patientId." and admin_control_id = ".(int)$resR['id']." ";
				$sqlR2 = imw_query($qryR2) or die(imw_error());
				if( imw_num_rows($sqlR2) > 0 ) {
					$resR2 = imw_fetch_assoc($sqlR2);
					$pt_religion = $resR2['religion_val'];
				}
			}

			$HPAdvanceDirective = (empty($row['ado_option']) || $row['ado_option'] == 'No')  ? 'No' : 'Yes';
			$demographicDataXML .= "<demographicDataChild>";
			$demographicDataXML .= "<title>".$row['title']."</title>";
			$demographicDataXML .= "<fname><![CDATA[$patientfname]]></fname>";
			$demographicDataXML .= "<lname><![CDATA[$patientlname]]></lname>";
			$demographicDataXML .= "<mname><![CDATA[$patientmname]]></mname>";
			$demographicDataXML .= "<suffix>".$row['suffix']."</suffix>";
			$demographicDataXML .= "<DOB>".$row['DOB']."</DOB>";
			$demographicDataXML .= "<street>".$row['street']."</street>";
			$demographicDataXML .= "<street2>".$row['street2']."</street2>";
			$demographicDataXML .= "<postal_code>".$row['postal_code']."</postal_code>";
			$demographicDataXML .= "<city>".$row['city']."</city>";
			$demographicDataXML .= "<state>".$row['state']."</state>";
			$demographicDataXML .= "<ss>".$row['ss']."</ss>";
			$demographicDataXML .= "<occupation>".$row['occupation']."</occupation>";
			$demographicDataXML .= "<phone_home>".$row['phone_home']."</phone_home>";
			$demographicDataXML .= "<phone_biz>".$row['phone_biz']."</phone_biz>";
			$demographicDataXML .= "<language>".$row['language']."</language>";
			$demographicDataXML .= "<race>".$row['race']."</race>";
			$demographicDataXML .= "<ethnicity>".$row['ethnicity']."</ethnicity>";
			$demographicDataXML .= "<religion><![CDATA[$pt_religion]]></religion>";
			$demographicDataXML .= "<phone_contact>".$row['phone_contact']."</phone_contact>";
			$demographicDataXML .= "<phone_cell>".$row['phone_cell']."</phone_cell>";
			$demographicDataXML .= "<preferr_contact>".$row['preferr_contact']."</preferr_contact>";
			$demographicDataXML .= "<status>".$row['status']."</status>";
			$demographicDataXML .= "<contact_relationship>".$row['contact_relationship']."</contact_relationship>";
			$demographicDataXML .= "<date>".$row['date']."</date>";
			$demographicDataXML .= "<sex>".$row['sex']."</sex>";
			$demographicDataXML .= "<referrer>".$row['referrer']."</referrer>";
			$demographicDataXML .= "<referrerID>".$row['referrerID']."</referrerID>";
			$demographicDataXML .= "<providerID>".$row['providerID']."</providerID>";
			$demographicDataXML .= "<email>".$row['email']."</email>";
			$demographicDataXML .= "<ethnoracial>".$row['ethnoracial']."</ethnoracial>";
			$demographicDataXML .= "<interpretter>".$row['interpretter']."</interpretter>";
			$demographicDataXML .= "<migrantseasonal>".$row['migrantseasonal']."</migrantseasonal>";
			$demographicDataXML .= "<family_size>".$row['family_size']."</family_size>";
			$demographicDataXML .= "<monthly_income>".$row['monthly_income']."</monthly_income>";
			$demographicDataXML .= "<homeless>".$row['homeless']."</homeless>";
			$demographicDataXML .= "<financial_review>".$row['financial_review']."</financial_review>";
			$demographicDataXML .= "<pubpid>".$row['pubpid']."</pubpid>";
			$demographicDataXML .= "<pid>".$row['pid']."</pid>";
			$demographicDataXML .= "<genericname1>".$row['genericname1']."</genericname1>";
			$demographicDataXML .= "<genericval1>".$row['genericval1']."</genericval1>";
			$demographicDataXML .= "<genericname2>".$row['genericname2']."</genericname2>";
			$demographicDataXML .= "<genericval2>".$row['genericval2']."</genericval2>";
			$demographicDataXML .= "<hipaa_mail>".$row['hipaa_mail']."</hipaa_mail>";
			$demographicDataXML .= "<hipaa_voice>".$row['hipaa_voice']."</hipaa_voice>";
			$demographicDataXML .= "<squad>".$row['squad']."</squad>";
			$demographicDataXML .= "<fitness>".$row['fitness']."</fitness>";
			$demographicDataXML .= "<username>".$row['username']."</username>";
			$demographicDataXML .= "<password>".$row['password']."</password>";
			$demographicDataXML .= "<p_imagename>".$row['p_imagename']."</p_imagename>";
			$p_imagename_content = getInsScanContent($row['p_imagename']);
			$demographicDataXML .= "<p_imagename_content><![CDATA[$p_imagename_content]]></p_imagename_content>";
			$demographicDataXML .= "<driving_licence>".$row['driving_licence']."</driving_licence>";
			$demographicDataXML .= "<licence_photo>".$row['licence_photo']."</licence_photo>";
			$demographicDataXML .= "<providerColor>".$row['providerColor']."</providerColor>";
			$demographicDataXML .= "<testColor>".$row['testColor']."</testColor>";
			$demographicDataXML .= "<financial_date>".$row['financial_date']."</financial_date>";
			$demographicDataXML .= "<financial_photo>".$row['financial_photo']."</financial_photo>";
			$demographicDataXML .= "<financial_applicant>".$row['financial_applicant']."</financial_applicant>";
			$primary_care		 = $row['primary_care'];
			$demographicDataXML .= "<primary_care><![CDATA[$primary_care]]></primary_care>";
			$demographicDataXML .= "<default_facility>".$row['default_facility']."</default_facility>";
			$demographicDataXML .= "<created_by>".$row['created_by']."</created_by>";
			$pNotes 			 = $row['patient_notes'];
			$demographicDataXML .= "<patient_notes><![CDATA[$pNotes]]></patient_notes>";
			$demographicDataXML .= "<patientStatus>".$row['patientStatus']."</patientStatus>";
			$demographicDataXML .= "<primary_care_id>".$row['primary_care_id']."</primary_care_id>";
			$demographicDataXML .= "<Sec_HCFA>".$row['Sec_HCFA']."</Sec_HCFA>";
			$demographicDataXML .= "<noBalanceBill>".$row['noBalanceBill']."</noBalanceBill>";
			$demographicDataXML .= "<EMR>".$row['EMR']."</EMR>";
			$demographicDataXML .= "<erx_entry>".$row['erx_entry']."</erx_entry>";
			$demographicDataXML .= "<erx_patient_id>".$row['erx_patient_id']."</erx_patient_id>";
			$demographicDataXML .= "<athenaID>".$row['athenaID']."</athenaID>";
			$demographicDataXML .= "</demographicDataChild>";
			$a++;
			//echo $row[category_id].' '.$row[category_id].' '.$row[category_status].'<br>';
		}
		$demographicDataXML .= "</demographicData>";
	}
}	
//echo $demographicDataXML;

//echo '~~~~~~~';
/////////////////////////////////////////////////////////////////
/*echo $demographicDataRow['fname'].' '.$demographicDataRow['lname'];
while($row = imw_fetch_array($rsQryGetPatientDemographicData)){
	echo $row['fname'].''.$row['lname'];
}*/

$strQryGetPatientInsurenceData="SELECT insData. * 
								FROM schedule_appointments sa
								INNER JOIN insurance_data insData ON insData.pid = sa.sa_patient_id
								AND (
								insData.type = 'primary'
								OR insData.type = 'secondary'
								OR insData.type = 'tertiary'
								)
								AND insData.actInsComp = '1'
								INNER JOIN insurance_case insCase ON insCase.ins_caseid = insData.ins_caseid
								AND insCase.case_status = 'Open'
								AND insCase.ins_caseid = sa.case_type_id
								LEFT JOIN patient_auth ptAuth ON ptAuth.ins_data_id = insData.id
								AND ptAuth.patient_id = insData.pid
								WHERE sa.id = ".$schedule_id;

$rsQryGetPatientInsurenceData = imw_query($strQryGetPatientInsurenceData);
if(!$rsQryGetPatientInsurenceData){
	echo ("Error : ". imw_error()."<br>".$strQryGetPatientInsurenceData);
}
if(imw_num_rows($rsQryGetPatientInsurenceData)<=0){	
	//GET RECORD FROM DEFAULT CASE
	$strQryGetPatientInsurenceData="SELECT insData. * 
									FROM schedule_appointments sa
									INNER JOIN insurance_data insData ON insData.pid = sa.sa_patient_id
									AND (
									insData.type = 'primary'
									OR insData.type = 'secondary'
									OR insData.type = 'tertiary'
									)
									AND insData.actInsComp = '1'
									INNER JOIN insurance_case insCase ON insCase.ins_caseid = insData.ins_caseid
									AND insCase.case_status = 'Open'
									AND insCase.ins_case_type = '".$defaultCaseId."'
									LEFT JOIN patient_auth ptAuth ON ptAuth.ins_data_id = insData.id
									AND ptAuth.patient_id = insData.pid
									WHERE sa.id = ".$schedule_id;
	
	$rsQryGetPatientInsurenceData = imw_query($strQryGetPatientInsurenceData);
}
if(!$rsQryGetPatientInsurenceData){
	echo ("Error : ". imw_error()."<br>".$strQryGetPatientInsurenceData);
}
else{
	$insurenceData = array();
	$a = 0;
	if(imw_num_rows($rsQryGetPatientInsurenceData)>0){		
		$aa = "?>";
		$insurenceDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
		$insurenceDataXML .= "<insurenceData>";
		while ($row = imw_fetch_array($rsQryGetPatientInsurenceData)) {
			//echo '<pre>';
			//print_r($row); 
			$provider = $row['provider'];
			$insCompQry = "select name,in_house_code from insurance_companies 
							where id = '$provider'";
			$rsInsCompQry = imw_query($insCompQry);	
			$numRowrInsCompQry = imw_num_rows($rsInsCompQry);				
			if($numRowrInsCompQry > 0){
				$insDetails = imw_fetch_array($rsInsCompQry);
				$providerName = $insDetails['name'];
				$inHouseCode = $insDetails['in_house_code'];
			}
			
			
			$ptAuthQry = "select auth_name from patient_auth 
							where ins_data_id = '".$row['id']."' AND auth_status='0'";
			$ptAuthRes = imw_query($ptAuthQry);	
			$ptAuthNumRow = imw_num_rows($ptAuthRes);				
			$authorization_number='';
			$authNumberArr=array();
			if($ptAuthNumRow > 0){
				while($ptAuthRow = imw_fetch_array($ptAuthRes)) {
					$authNumberArr[] = $ptAuthRow['auth_name'];
				}
				$authorization_number = implode(", ",$authNumberArr);
			}

			//INSURANCE SCAN-IMAGES
			$insurenceDataScanCardPath1 = $row['scan_card'];
			$insurenceDataScanCardPath2 = $row['scan_card2'];
			
			$insurenceDataScanCard1 = getInsScanContent($insurenceDataScanCardPath1);
			$insurenceDataScanCard2 = getInsScanContent($insurenceDataScanCardPath2);
			//INSURANCE SCAN IMAGES
			
			
			$insurenceDataXML .= "<insurenceDataChild>";
			$insurenceDataXML .= "<type>".$row['type']."</type>";
			$insurenceDataXML .= "<ins_provider><![CDATA[$providerName]]></ins_provider>";
			$insurenceDataXML .= "<ins_in_house_code><![CDATA[$inHouseCode]]></ins_in_house_code>";
			$insurenceDataXML .= "<plan_name>".$row['plan_name']."</plan_name>";
			$insurenceDataXML .= "<policy>".$row['policy_number']."</policy>";
			$insurenceDataXML .= "<group_name>".$row['group_number']."</group_name>";
			$insurenceDataXML .= "<lname>".$row['subscriber_lname']."</lname>";
			$insurenceDataXML .= "<mname>".$row['subscriber_mname']."</mname>";
			$insurenceDataXML .= "<fname>".$row['subscriber_fname']."</fname>";
			$insurenceDataXML .= "<sub_relation>".$row['subscriber_relationship']."</sub_relation>";
			$insurenceDataXML .= "<ssn>".$row['subscriber_ss']."</ssn>";
			$insurenceDataXML .= "<dob>".$row['subscriber_DOB']."</dob>";
			$insurenceDataXML .= "<address1>".$row['subscriber_street']."</address1>";
			$insurenceDataXML .= "<address2>".$row['subscriber_street_2']."</address2>";
			$insurenceDataXML .= "<zip_code>".$row['subscriber_postal_code']."</zip_code>";
			$insurenceDataXML .= "<city>".$row['subscriber_city']."</city>";
			$insurenceDataXML .= "<state>".$row['subscriber_state']."</state>";
			$insurenceDataXML .= "<subscriber_country>".$row['subscriber_country']."</subscriber_country>";
			$insurenceDataXML .= "<home_phone>".$row['subscriber_phone']."</home_phone>";
			$insurenceDataXML .= "<work_phone>".$row['subscriber_biz_phone']."</work_phone>";
			$insurenceDataXML .= "<mbl_phone>".$row['subscriber_mobile']."</mbl_phone>";
			$insurenceDataXML .= "<subscriber_employer>".$row['subscriber_employer']."</subscriber_employer>";
			$insurenceDataXML .= "<subscriber_employer_street>".$row['subscriber_employer_street']."</subscriber_employer_street>";
			$insurenceDataXML .= "<subscriber_employer_postal_code>".$row['subscriber_employer_postal_code']."</subscriber_employer_postal_code>";
			$insurenceDataXML .= "<subscriber_employer_state>".$row['subscriber_employer_state']."</subscriber_employer_state>";
			$insurenceDataXML .= "<subscriber_employer_country>".$row['subscriber_employer_country']."</subscriber_employer_country>";
			$insurenceDataXML .= "<subscriber_employer_city>".$row['subscriber_employer_city']."</subscriber_employer_city>";
			$insurenceDataXML .= "<copay>".$row['copay']."</copay>";
			$insurenceDataXML .= "<date>".$row['date']."</date>";
			$insurenceDataXML .= "<pid>".$row['pid']."</pid>";
			$insurenceDataXML .= "<gender>".$row['subscriber_sex']."</gender>";
			$insurenceDataXML .= "<copay_fixed>".$row['copay_fixed']."</copay_fixed>";
			$insurenceDataXML .= "<refer_req>".$row['referal_required']."</refer_req>";
			$insurenceDataXML .= "<scan_card>".$row['scan_card']."</scan_card>";
			$insurenceDataXML .= "<scan_label>".$row['scan_label']."</scan_label>";
			$insurenceDataXML .= "<active_date>".$row['effective_date']."</active_date>";
			$insurenceDataXML .= "<expiry_Date>".$row['expiration_date']."</expiry_Date>";
			$insurenceDataXML .= "<ins_caseid>".$row['ins_caseid']."</ins_caseid>";
			$insurenceDataXML .= "<claims_adjustername>".$row['claims_adjustername']."</claims_adjustername>";
			$insurenceDataXML .= "<claims_adjusterphone>".$row['claims_adjusterphone']."</claims_adjusterphone>";
			$insurenceDataXML .= "<responsible_party>".$row['responsible_party']."</responsible_party>";
			$insurenceDataXML .= "<Sec_HCFA>".$row['Sec_HCFA']."</Sec_HCFA>";
			$insurenceDataXML .= "<newComDate>".$row['newComDate']."</newComDate>";
			$insurenceDataXML .= "<actInsComp>".$row['actInsComp']."</actInsComp>";
			$insurenceDataXML .= "<actInsCompDate>".$row['actInsCompDate']."</actInsCompDate>";
			$insurenceDataXML .= "<scan_card2>".$row['scan_card2']."</scan_card2>";
			$insurenceDataXML .= "<scan_label2>".$row['scan_label2']."</scan_label2>";
			$insurenceDataXML .= "<cardscan_operator>".$row['cardscan_operator']."</cardscan_operator>";
			$insurenceDataXML .= "<cardscan_date>".$row['cardscan_date']."</cardscan_date>";
			$insurenceDataXML .= "<cardscan_comments>".$row['cardscan_comments']."</cardscan_comments>";
			$insurenceDataXML .= "<auth_required>".$row['auth_required']."</auth_required>";
			$insurenceDataXML .= "<cardscan1_datetime>".$row['cardscan1_datetime']."</cardscan1_datetime>";
			$insurenceDataXML .= "<self_pay_provider>".$row['self_pay_provider']."</self_pay_provider>";

			$insurenceDataXML .= "<insurenceDataScanCard1><![CDATA[$insurenceDataScanCard1]]></insurenceDataScanCard1>";
			$insurenceDataXML .= "<insurenceDataScanCard2><![CDATA[$insurenceDataScanCard2]]></insurenceDataScanCard2>";
			$insurenceDataXML .= "<authorization_number>".$authorization_number."</authorization_number>";
			$insurenceDataXML .= "</insurenceDataChild>";
			$a++;
			//echo $row[category_id].' '.$row[category_id].' '.$row[category_status].'<br>';
		}
		$insurenceDataXML .= "</insurenceData>";
	}
}

//START INSURANCE CASES
$strQryGetPatientInsurenceCase="SELECT insCase. * , insCaseTypes. * 
								FROM schedule_appointments sa
								INNER JOIN insurance_case insCase ON insCase.patient_id = sa.sa_patient_id
								AND insCase.case_status = 'Open'
								AND insCase.ins_caseid = sa.case_type_id
								INNER JOIN insurance_case_types insCaseTypes ON insCaseTypes.case_id = insCase.ins_case_type
								WHERE sa.id = ".$schedule_id;
								

$rsQryGetPatientInsurenceCase = imw_query($strQryGetPatientInsurenceCase);
if(imw_num_rows($rsQryGetPatientInsurenceCase)<=0){	
	//GET DEFAULT INSURANCE CASE
	$strQryGetPatientInsurenceCase="SELECT insCase. * , insCaseTypes. * 
									FROM schedule_appointments sa
									INNER JOIN insurance_case insCase ON insCase.patient_id = sa.sa_patient_id
									AND insCase.case_status = 'Open'
									AND insCase.ins_case_type = '".$defaultCaseId."'
									INNER JOIN insurance_case_types insCaseTypes ON insCaseTypes.case_id = insCase.ins_case_type
									WHERE sa.id = ".$schedule_id;
									
	
	$rsQryGetPatientInsurenceCase = imw_query($strQryGetPatientInsurenceCase);
}
if(!$rsQryGetPatientInsurenceCase){
	echo ("Error : ". imw_error()."<br>".$strQryGetPatientInsurenceCase);
}else {
	$insurenceCase = array();
	$a = 0;
	if(imw_num_rows($rsQryGetPatientInsurenceCase)>0){		
		$aa = "?>";
		$insurenceCaseXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
		$insurenceCaseXML .= "<insurenceCase>";
		while ($row = imw_fetch_array($rsQryGetPatientInsurenceCase)) {
			$insurenceCaseXML .= "<insurenceCaseChild>";
			$insurenceCaseXML .= "<ins_caseid>".$row['ins_caseid']."</ins_caseid>";
			$insurenceCaseXML .= "<ins_case_name>".$row['ins_case_name']."</ins_case_name>";
			$insurenceCaseXML .= "<ins_case_type>".$row['ins_case_type']."</ins_case_type>";
			$insurenceCaseXML .= "<patient_id>".$row['patient_id']."</patient_id>";
			$insurenceCaseXML .= "<start_date>".$row['start_date']."</start_date>";
			$insurenceCaseXML .= "<end_date>".$row['end_date']."</end_date>";
			$insurenceCaseXML .= "<case_status>".$row['case_status']."</case_status>";
			$insurenceCaseXML .= "<athenaID>".$row['athenaID']."</athenaID>";
			
			$insurenceCaseXML .= "<case_name>".$row['case_name']."</case_name>";
			$insurenceCaseXML .= "<vision>".$row['vision']."</vision>";
			$insurenceCaseXML .= "<normal>".$row['normal']."</normal>";

			//START CODE TO GET EXPIRED INFORMATION OF INSURANCE
			$expireInsQry = "SELECT date_format(effective_date,'%Y-%m-%d') AS effective_date_format, date_format(expiration_date,'%Y-%m-%d') AS expiration_date_format, type, pid FROM insurance_data WHERE actInsComp = '0' AND pid = '".$row['patient_id']."' AND ins_caseid = '".$row['ins_caseid']."' ORDER BY id";				
			$expireInsRes = imw_query($expireInsQry);
			$expireInsArr = array();
			if(imw_num_rows($expireInsRes)>0){
				while ($expireInsRow = imw_fetch_array($expireInsRes)) {
					$insTypeExp = $expireInsRow["type"];
					$expireInsArr[$insTypeExp]["effective_date"] 	= $expireInsRow["effective_date_format"];
					$expireInsArr[$insTypeExp]["expiration_date"] 	= $expireInsRow["expiration_date_format"];
				}
			}
			
			$insurenceCaseXML .= "<expire_pri_effective_date>".$expireInsArr["primary"]["effective_date"]."</expire_pri_effective_date>";
			$insurenceCaseXML .= "<expire_pri_expiration_date>".$expireInsArr["primary"]["expiration_date"]."</expire_pri_expiration_date>";
			$insurenceCaseXML .= "<expire_sec_effective_date>".$expireInsArr["secondary"]["effective_date"]."</expire_sec_effective_date>";
			$insurenceCaseXML .= "<expire_sec_expiration_date>".$expireInsArr["secondary"]["expiration_date"]."</expire_sec_expiration_date>";
			$insurenceCaseXML .= "<expire_ter_effective_date>".$expireInsArr["tertiary"]["effective_date"]."</expire_ter_effective_date>";
			$insurenceCaseXML .= "<expire_ter_expiration_date>".$expireInsArr["tertiary"]["expiration_date"]."</expire_ter_expiration_date>";
			//END CODE TO GET EXPIRED INFORMATION OF INSURANCE
			
			$insurenceCaseXML .= "</insurenceCaseChild>";
			$a++;
		}
		$insurenceCaseXML .= "</insurenceCase>";
	}	
}
//END INSURANCES CASES

//START INSURANCE SCANNED DOCUMENT
//AND insScanDoc.document_status = '0'
$strQryGetPatientInsurenceScanDoc="SELECT insScanDoc. * 
									FROM schedule_appointments sa
									INNER JOIN insurance_scan_documents insScanDoc ON insScanDoc.patient_id = sa.sa_patient_id
									AND (
									insScanDoc.type = 'primary'
									OR insScanDoc.type = 'secondary'
									OR insScanDoc.type = 'tertiary'
									)
									INNER JOIN insurance_case insCase ON insCase.ins_caseid = insScanDoc.ins_caseid
									AND insCase.case_status = 'Open'
									AND insCase.ins_caseid = sa.case_type_id
									WHERE sa.id = ".$schedule_id;

$rsQryGetPatientInsurenceScanDoc = imw_query($strQryGetPatientInsurenceScanDoc);

if(imw_num_rows($rsQryGetPatientInsurenceScanDoc)<=0){		
	//GET DEFAULT INSURANCE SCANNED DOCUMENT
	$strQryGetPatientInsurenceScanDoc="SELECT insScanDoc. * 
										FROM schedule_appointments sa
										INNER JOIN insurance_scan_documents insScanDoc ON insScanDoc.patient_id = sa.sa_patient_id
										AND (
										insScanDoc.type = 'primary'
										OR insScanDoc.type = 'secondary'
										OR insScanDoc.type = 'tertiary'
										)
										INNER JOIN insurance_case insCase ON insCase.ins_caseid = insScanDoc.ins_caseid
										AND insCase.case_status = 'Open'
										AND insCase.ins_case_type = '".$defaultCaseId."'
										WHERE sa.id = ".$schedule_id;
	
	$rsQryGetPatientInsurenceScanDoc = imw_query($strQryGetPatientInsurenceScanDoc);

}
if(!$rsQryGetPatientInsurenceScanDoc){
	echo ("Error : ". imw_error()."<br>".$strQryGetPatientInsurenceScanDoc);
}
else{
	$InsurenceScanDoc = array();
	$a = 0;
	if(imw_num_rows($rsQryGetPatientInsurenceScanDoc)>0){		
		$aa = "?>";
		$InsurenceScanDocXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
		$InsurenceScanDocXML .= "<InsurenceScanDoc>";
		while ($row = imw_fetch_array($rsQryGetPatientInsurenceScanDoc)) {
			
			//INSURANCE SCAN DOC- IMAGES
			$insurenceScanDocScanCardPath1 = $row['scan_card'];
			$insurenceScanDocScanCardPath2 = $row['scan_card2'];
			
			$insurenceDocScanCard1 = getInsScanContent($insurenceScanDocScanCardPath1);
			$insurenceDocScanCard2 = getInsScanContent($insurenceScanDocScanCardPath2);
			//INSURANCE SCAN DOC- IMAGES
			
			$InsurenceScanDocXML .= "<InsurenceScanDocChild>";
			$InsurenceScanDocXML .= "<type>".$row['type']."</type>";
			$InsurenceScanDocXML .= "<ins_caseid>".$row['ins_caseid']."</ins_caseid>";
			$InsurenceScanDocXML .= "<patient_id>".$row['patient_id']."</patient_id>";
			$InsurenceScanDocXML .= "<scan_card>".$row['scan_card']."</scan_card>";
			$InsurenceScanDocXML .= "<scan_label>".$row['scan_label']."</scan_label>";
			$InsurenceScanDocXML .= "<scan_card2>".$row['scan_card2']."</scan_card2>";
			$InsurenceScanDocXML .= "<scan_label2>".$row['scan_label2']."</scan_label2>";
			$InsurenceScanDocXML .= "<created_date>".$row['created_date']."</created_date>";
			$InsurenceScanDocXML .= "<document_status>".$row['document_status']."</document_status>";
			$InsurenceScanDocXML .= "<cardscan_date>".$row['cardscan_date']."</cardscan_date>";
			$InsurenceScanDocXML .= "<cardscan_comments>".$row['cardscan_comments']."</cardscan_comments>";
			$InsurenceScanDocXML .= "<cardscan1_date>".$row['cardscan1_date']."</cardscan1_date>";
			
			$InsurenceScanDocXML .= "<insurenceDocScanCard1><![CDATA[$insurenceDocScanCard1]]></insurenceDocScanCard1>";
			$InsurenceScanDocXML .= "<insurenceDocScanCard2><![CDATA[$insurenceDocScanCard2]]></insurenceDocScanCard2>";
			
			$InsurenceScanDocXML .= "</InsurenceScanDocChild>";
			$a++;
		}
		$InsurenceScanDocXML .= "</InsurenceScanDoc>";
	}
}
//END INSURANCE SCAN DOCUMENT

//echo $insurenceDataXML;
//echo '~~~~~~~';
$strQryGetPatientSurgeryConsentData="SELECT scff.* FROM schedule_appointments sa									
									INNER JOIN surgery_consent_filled_form scff ON (scff.patient_id  = sa.sa_patient_id AND scff.appt_id  = sa.id AND scff.movedToTrash = '0')
									WHERE sa.id = ".$schedule_id;
$rsQryGetPatientSurgeryConsentData = imw_query($strQryGetPatientSurgeryConsentData);	
/*while($row = imw_fetch_array($rsQryGetPatientSurgeryConsentData)){
	echo $row['surgery_consent_name'].' '.$row['surgery_consent_alias'];
}*/
////////////////////////////////////
if(!$rsQryGetPatientSurgeryConsentData){
	echo ("Error : ". imw_error()."<br>".$strQryGetPatientSurgeryConsentData);
}
else{
	$consentData = array();
	$a = 0;
	if(imw_num_rows($rsQryGetPatientSurgeryConsentData)>0){		
		$aa = "?>";
		$consentDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
		$consentDataXML .= "<consentData>";
		while ($row = imw_fetch_array($rsQryGetPatientSurgeryConsentData)) {
			//echo '<pre>';
			//print_r($row); 
			$consentDataXML .= "<consentDataChild>";

			$consentNameData = addslashes($row['surgery_consent_name']);
			$consentDataXML .= "<surgery_consent_name><![CDATA[$consentNameData]]></surgery_consent_name>";
			
//			$consentDataXML .= "<surgery_consent_name>".$row['surgery_consent_name']."</surgery_consent_name>";
			$consentAliasData = addslashes($row['surgery_consent_alias']);
			$consentDataXML .= "<surgery_consent_alias><![CDATA[$consentAliasData]]></surgery_consent_alias>";

//			$consentDataXML .= "<surgery_consent_alias>".$row['surgery_consent_alias']."</surgery_consent_alias>";
			
			$surgeryConsentData = addslashes($row['surgery_consent_data']);
//			$myXmlNew .= "<consent_name><![CDATA[$consentNameData]]></consent_name>";
			$consentDataXML .= "<surgery_consent_data><![CDATA[$surgeryConsentData]]></surgery_consent_data>";
			
//			$consentDataXML .= "<surgery_consent_data><![CDATA[$surgeryConsentData]]></surgery_consent_data>";
			$consentDataXML .= "<surgery_consent_sign>".$row['surgery_consent_sign']."</surgery_consent_sign>";
			$consentDataXML .= "<ascId>".$row['ascId']."</ascId>";
			$consentDataXML .= "<form_status>".$row['form_status']."</form_status>";
			$consentDataXML .= "<eposted>".$row['eposted']."</eposted>";
			$consentDataXML .= "<consent_template_id>".$row['consent_template_id']."</consent_template_id>";
			$consentDataXML .= "<consent_category_id>".$row['consent_category_id']."</consent_category_id>";
			$consentDataXML .= "<left_navi_status>".$row['left_navi_status']."</left_navi_status>";
			$consentDataXML .= "<consent_purge_status>".$row['consent_purge_status']."</consent_purge_status>";
			$consentDataXML .= "<sigStatus>".$row['sigStatus']."</sigStatus>";
			$consentDataXML .= "<signSurgeon1Activate>".$row['signSurgeon1Activate']."</signSurgeon1Activate>";
			$consentDataXML .= "<signSurgeon1Id>".$row['signSurgeon1Id']."</signSurgeon1Id>";
			$consentDataXML .= "<signSurgeon1FirstName>".$row['signSurgeon1FirstName']."</signSurgeon1FirstName>";
			$consentDataXML .= "<signSurgeon1MiddleName>".$row['signSurgeon1MiddleName']."</signSurgeon1MiddleName>";
			$consentDataXML .= "<signSurgeon1LastName>".$row['signSurgeon1LastName']."</signSurgeon1LastName>";
			$consentDataXML .= "<signSurgeon1Status>".$row['signSurgeon1Status']."</signSurgeon1Status>";
			$consentDataXML .= "<signSurgeon1DateTime>".$row['signSurgeon1DateTime']."</signSurgeon1DateTime>";
			$consentDataXML .= "<signNurseActivate>".$row['signNurseActivate']."</signNurseActivate>";
			$consentDataXML .= "<signNurseId>".$row['signNurseId']."</signNurseId>";
			$consentDataXML .= "<signNurseFirstName>".$row['signNurseFirstName']."</signNurseFirstName>";
			$consentDataXML .= "<signNurseMiddleName>".$row['signNurseMiddleName']."</signNurseMiddleName>";
			$consentDataXML .= "<signNurseLastName>".$row['signNurseLastName']."</signNurseLastName>";
			$consentDataXML .= "<signNurseStatus>".$row['signNurseStatus']."</signNurseStatus>";
			$consentDataXML .= "<signNurseDateTime>".$row['signNurseDateTime']."</signNurseDateTime>";
			$consentDataXML .= "<signAnesthesia1Activate>".$row['signAnesthesia1Activate']."</signAnesthesia1Activate>";
			$consentDataXML .= "<signAnesthesia1Id>".$row['signAnesthesia1Id']."</signAnesthesia1Id>";
			$consentDataXML .= "<signAnesthesia1FirstName>".$row['signAnesthesia1FirstName']."</signAnesthesia1FirstName>";
			$consentDataXML .= "<signAnesthesia1MiddleName>".$row['signAnesthesia1MiddleName']."</signAnesthesia1MiddleName>";
			$consentDataXML .= "<signAnesthesia1LastName>".$row['signAnesthesia1LastName']."</signAnesthesia1LastName>";
			$consentDataXML .= "<signAnesthesia1Status>".$row['signAnesthesia1Status']."</signAnesthesia1Status>";
			$consentDataXML .= "<signAnesthesia1DateTime>".$row['signAnesthesia1DateTime']."</signAnesthesia1DateTime>";
			$consentDataXML .= "<signWitness1Activate>".$row['signWitness1Activate']."</signWitness1Activate>";
			$consentDataXML .= "<signWitness1Id>".$row['signWitness1Id']."</signWitness1Id>";
			$consentDataXML .= "<signWitness1FirstName>".$row['signWitness1FirstName']."</signWitness1FirstName>";
			$consentDataXML .= "<signWitness1MiddleName>".$row['signWitness1MiddleName']."</signWitness1MiddleName>";
			$consentDataXML .= "<signWitness1LastName>".$row['signWitness1LastName']."</signWitness1LastName>";
			$consentDataXML .= "<signWitness1Status>".$row['signWitness1Status']."</signWitness1Status>";
			$consentDataXML .= "<signWitness1DateTime>".$row['signWitness1DateTime']."</signWitness1DateTime>";
			$consentDataXML .= "<fldPatientWaitingId>".$row['fldPatientWaitingId']."</fldPatientWaitingId>";
			$consentDataXML .= "<patient_id>".$row['patient_id']."</patient_id>";
			$consentDataXML .= "<consentSignedStatus>".$row['consentSignedStatus']."</consentSignedStatus>";
			$consentDataXML .= "<form_created_date>".$row['form_created_date']."</form_created_date>";			
			$consentDataXML .= "</consentDataChild>";
			$a++;
			//echo $row[category_id].' '.$row[category_id].' '.$row[category_status].'<br>';
		}
		$consentDataXML .= "</consentData>";
	}
}
//echo $consentDataXML;
//echo '~~~~~~~';
$strQryGetPatientSurgeryConsentSigData="SELECT scfs.*
										FROM schedule_appointments sa									
										INNER JOIN surgery_consent_form_signature scfs ON scfs.patient_id  = sa.sa_patient_id 								
										WHERE sa.id = ".$schedule_id;
$rsQryGetPatientSurgeryConsentSigData = imw_query($strQryGetPatientSurgeryConsentSigData);	
/*while($row = imw_fetch_array($rsQryGetPatientSurgeryConsentSigData)){
	echo $row['surgery_consent_name'].' '.$row['surgery_consent_alias'];
}*/
////////////////////////////////////
if(!$rsQryGetPatientSurgeryConsentSigData){
	echo ("Error : ". imw_error()."<br>".$strQryGetPatientSurgeryConsentData);
}
else{
	$consentSigData = array();
	$a = 0;
	if(imw_num_rows($rsQryGetPatientSurgeryConsentSigData)>0){		
		$aa = "?>";
		$consentSigDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
		$consentSigDataXML .= "<consentDataSig>";
		while ($row = imw_fetch_array($rsQryGetPatientSurgeryConsentSigData)) {
			//echo '<pre>';
			//print_r($row); 
			$consentSigDataXML .= "<consentDataSigChild>";
			$consentSigDataXML .= "<signature_content>".$row['signature_content']."</signature_content>";
			$consentSigDataXML .= "<consent_template_id>".$row['consent_template_id']."</consent_template_id>";
			$consentSigDataXML .= "<confirmation_id>".$row['confirmation_id']."</confirmation_id>";
			$consentSigDataXML .= "<signature_count>".$row['signature_count']."</signature_count>";
			$consentSigDataXML .= "<signature_image_path>".$row['signature_image_path']."</signature_image_path>";
			$consentSigDataXML .= "<patient_in_waiting_id>".$row['patient_in_waiting_id']."</patient_in_waiting_id>";
			$consentSigDataXML .= "<patient_id>".$row['patient_id']."</patient_id>";
			$consentSigDataXML .= "<surgery_consent_auto_id>".$row['surgery_consent_auto_id']."</surgery_consent_auto_id>";
			$consentSigDataXML .= "</consentDataSigChild>";
			$a++;
			//echo $row[category_id].' '.$row[category_id].' '.$row[category_status].'<br>';
		}
		$consentSigDataXML .= "</consentDataSig>";
	}
}
/////////////////
$sqlAppointmentsQry="SELECT * FROM schedule_appointments 
					WHERE sa_app_start_date = '".$sa_date."'
					AND sa_patient_app_status_id NOT IN(201)
					and id = '".$schedule_id."'
					ORDER BY sa_app_start_date";
					
$sqlAppointmentsRes = imw_query($sqlAppointmentsQry) or die(imw_error());
$sqlAppointmentsNumRow = imw_num_rows($sqlAppointmentsRes);
if($sqlAppointmentsNumRow>0) {
	$aa = "?>";
	$patientInWatingDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
	$patientInWatingDataXML .= "<patientInWating>";
	while($sqlAppointmentsRow = imw_fetch_array($sqlAppointmentsRes)) {
		$appt_date_of_surgery	   	= $sqlAppointmentsRow['sa_app_start_date'];
		$sa_doctor_id	   			= ((int)$sqlAppointmentsRow['facility_type_provider']!=0)?$sqlAppointmentsRow['facility_type_provider']:$sqlAppointmentsRow['sa_doctor_id']; //Surgeon ID
		$surgeonNameQry = "select * from users where id='".$sa_doctor_id."'";
		$surgeonNameRes = imw_query($surgeonNameQry) or die($surgeonNameQry.imw_error());
		$surgeonNameNumRow = imw_num_rows($surgeonNameRes);
				
		//START INITIALIZE VARIABLES OF SURGEON
		$surgeonFirstName    = '';
		$surgeonMiddleName   = '';
		$surgeonLastName     = '';
		
		//END INITIALIZE VARIABLES OF SURGEON
		if($surgeonNameNumRow>0) {
			$surgeonNameRow = imw_fetch_array($surgeonNameRes);
			$surgeonFirstName    = $surgeonNameRow['fname']; //Surgeon First Name
			$surgeonMiddleName   = $surgeonNameRow['mname']; //Surgeon Middle Name
			$surgeonLastName     = $surgeonNameRow['lname']; //Surgeon Last Name
			$surgeonNpi     	 = $surgeonNameRow['user_npi']; //Surgeon Last Name
			
		}
		$sa_app_starttime  			= $sqlAppointmentsRow['sa_app_starttime']; //Surgery Time
		$sa_facility_id  			= $sqlAppointmentsRow['sa_facility_id']; //Facility ID
		//added by amit on 13-06-09 for synch with imwemr
		$pickup_time = "";
		if(isset($sqlAppointmentsRow['pick_up_time'])){
			$pickup_time = addslashes($sqlAppointmentsRow['pick_up_time']);
		}
		$arrival_time = "";
		if(isset($sqlAppointmentsRow['arrival_time'])){
			$arrival_time = addslashes($sqlAppointmentsRow['arrival_time']);
		}			
		$procedureid	   			= $sqlAppointmentsRow['procedureid']; //ProcedureId
		$sec_procedureid	=	'';
		if(isset($sqlAppointmentsRow['sec_procedureid'])){
			$sec_procedureid = addslashes($sqlAppointmentsRow['sec_procedureid']);
		}
		$ter_procedureid	=	'';
		if(isset($sqlAppointmentsRow['tertiary_procedureid'])){
			$ter_procedureid = addslashes($sqlAppointmentsRow['tertiary_procedureid']);
		}
		//$procedureNameQry = "select * from slot_procedures where id='".$procedureid."'";
		$procedureNameQry = "SELECT prim_proc.proc AS pri_proc,prim_acronym.acronym AS pri_acronym, 
								secd_proc.proc AS sec_proc,secd_acronym.acronym AS sec_acronym,
								tert_proc.proc AS ter_proc,tert_acronym.acronym AS ter_acronym
								FROM (
								
								SELECT proc
								FROM slot_procedures
								WHERE id ='".$procedureid."'
								) AS prim_proc, (
								
								SELECT IF(count(id)=1,acronym,'') as acronym
								FROM slot_procedures
								WHERE id = '".$procedureid."'
								) AS prim_acronym, (
								
								
								SELECT IF(count(id)=1,proc,'') as proc
								FROM slot_procedures
								WHERE id = '".$sec_procedureid."'
								) AS secd_proc, (
								
								SELECT IF(count(id)=1,acronym,'') as acronym
								FROM slot_procedures
								WHERE id = '".$sec_procedureid."'
								) AS secd_acronym, ( 
								
								SELECT IF(count(id)=1,proc,'') as proc
								FROM slot_procedures
								WHERE id = '".$ter_procedureid."'
								) AS tert_proc, (
								
								SELECT IF(count(id)=1,acronym,'') as acronym
								FROM slot_procedures
								WHERE id = '".$ter_procedureid."'
								) AS tert_acronym
								
								";
		
		
		$procedureNameRes = imw_query($procedureNameQry) or die(imw_error());
		$procedureNameNumRow = imw_num_rows($procedureNameRes);
		
		$procedureName   = "";
		$site = "";
		$confSiteNo='';
		//START CODE FOR SITE(SET THIS ON PRIORITY)
		if(isset($sqlAppointmentsRow['procedure_site'])){
			$site = strtolower(addslashes($sqlAppointmentsRow['procedure_site']));
			if($site=='bilateral') 	{ $site='both';}
			
			if($site=='left') 		{ $confSiteNo=1;
			}else if($site=='right'){ $confSiteNo=2;
			}else if($site=='both') { $confSiteNo=3;
			}
			
		}
		//END CODE FOR SITE(SET THIS ON PRIORITY)
		
		$procedureBasedSite='';
		$procedureBasedConfSiteNo='';
		if($procedureNameNumRow>0) {
			$procedureNameRow = imw_fetch_array($procedureNameRes);
			
			$procedureName    		= $procedureNameRow['pri_proc']; //Procedure Name
			$procedureAcronym   	= addslashes($procedureNameRow['pri_acronym']); //Procedure Name Acronym
			$secProcedureName    	= $procedureNameRow['sec_proc']; //Secondary Procedure Name
			$secProcedureAcronym 	= addslashes($procedureNameRow['sec_acronym']); //Secondary Procedure Acronym
			$terProcedureName    	= $procedureNameRow['ter_proc']; //Tertiary Procedure Name
			$terProcedureAcronym 	= addslashes($procedureNameRow['ter_acronym']); //Tertiary Procedure Acronym
			//$site = "";
			$siteTemp = substr(trim($procedureName),-2,2); //READ LAST TWO CHARACTERS OF PRIMARY PROCEDURE EXCLUDING SPACE
			if($siteTemp=='OS') {
				$procedureBasedSite = 'left';
				$procedureBasedConfSiteNo=1;
				$procedureName = trim(str_replace($siteTemp,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
			}else if($siteTemp=='OD') {
				$procedureBasedSite = 'right';
				$procedureBasedConfSiteNo=2;
				$procedureName = trim(str_replace($siteTemp,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
			}else if($siteTemp=='OU') {
				$procedureBasedSite = 'both';
				$procedureBasedConfSiteNo=3;
				$procedureName 	= trim(str_replace($siteTemp,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
			}
			$procedureName 		= addslashes($procedureName);
			
		}
		
		if($site=='') { //IF SITE IS STILL BLANK THEN SET IT ON PROCEDURE BASED	
			$site 		= $procedureBasedSite;
			$confSiteNo = $procedureBasedConfSiteNo;
		} 
		
		$patient_status_id		= $sqlAppointmentsRow['sa_patient_app_status_id']; //PATIENT STATUS ID
		
		//SET PATIENT STATUS
		$patient_status='Scheduled';
		if($patient_status_id=='18') {
			$patient_status='Canceled';
		}
		//END SET PATIENT STATUS
		$comment					= stripslashes($sqlAppointmentsRow['sa_comments']); //Comment
		$iDocSchAthenaId			= stripslashes($sqlAppointmentsRow['athenaID']); //Comment
				
		$patientInWatingDataXML .= "<patientInWatingChild>";
		$patientInWatingDataXML .= "<dos>".$appt_date_of_surgery."</dos>";
		$patientInWatingDataXML .= "<surgeon_fname>".$surgeonFirstName."</surgeon_fname>";
		$patientInWatingDataXML .= "<surgeon_mname>".$surgeonMiddleName."</surgeon_mname>";
		$patientInWatingDataXML .= "<surgeon_lname>".$surgeonLastName."</surgeon_lname>";
		$patientInWatingDataXML .= "<surgeon_npi>".$surgeonNpi."</surgeon_npi>";
		$patientInWatingDataXML .= "<surgery_time>".$sa_app_starttime."</surgery_time>";
		$patientInWatingDataXML .= "<iasc_facility_id>".$sa_facility_id."</iasc_facility_id>";
		$patientInWatingDataXML .= "<pickup_time>".$pickup_time."</pickup_time>";
		$patientInWatingDataXML .= "<arrival_time>".$arrival_time."</arrival_time>";
		$patientInWatingDataXML .= "<patient_primary_procedure><![CDATA[$procedureName]]></patient_primary_procedure>";
		$patientInWatingDataXML .= "<patient_primary_acroynm><![CDATA[$procedureAcronym]]></patient_primary_acroynm>";
		$patientInWatingDataXML .= "<patient_secondary_procedure><![CDATA[$secProcedureName]]></patient_secondary_procedure>";
		$patientInWatingDataXML .= "<patient_secondary_acroynm><![CDATA[$secProcedureAcronym]]></patient_secondary_acroynm>";
		$patientInWatingDataXML .= "<patient_tertiary_procedure><![CDATA[$terProcedureName]]></patient_tertiary_procedure>";
		$patientInWatingDataXML .= "<patient_tertiary_acroynm><![CDATA[$terProcedureAcronym]]></patient_tertiary_acroynm>";
		$patientInWatingDataXML .= "<patient_status>".$patient_status."</patient_status>";
		$patientInWatingDataXML .= "<site>".$site."</site>";
		$patientInWatingDataXML .= "<idoc_sch_athena_id>".$iDocSchAthenaId."</idoc_sch_athena_id>";
		$patientInWatingDataXML .= "<comment><![CDATA[$comment]]></comment>";
		$patientInWatingDataXML .= "</patientInWatingChild>";
		$a++;
		
		
	}
}						
/////////////////////////			
$strQryGetPatientScanDocsData="SELECT scpsc.*
										FROM schedule_appointments sa									
										INNER JOIN surgery_center_patient_scan_docs scpsc ON scpsc.patient_id = sa.sa_patient_id 								
										WHERE sa.id = ".$schedule_id;
$rsQryGetPatientScanDocsData = imw_query($strQryGetPatientScanDocsData);	

if(!$rsQryGetPatientScanDocsData){
	echo ("Error : ". imw_error()."<br>".$strQryGetPatientScanDocsData);
}
else{
	$scanDocData = array();
	$a = 0;

	if(imw_num_rows($rsQryGetPatientScanDocsData)>0){		
		$aa = "?>";
		$scanDocDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
		$scanDocDataXML .= "<scanDocData>";
		while ($row = imw_fetch_array($rsQryGetPatientScanDocsData)) {
			//echo '<pre>';
			//print_r($row);			
			$mainDir = substr(data_path(), 0, -1);			
			$dataBaseDocAddress = urldecode($row['scan_doc_add']);			
			if($dataBaseDocAddress){
				$pathParts = pathinfo($dataBaseDocAddress);				
				if(trim(strtolower($pathParts['extension'])) != "pdf"){
					$docAddress = $mainDir.$dataBaseDocAddress;
					$file = $docAddress;
					if(file_exists($file)){
						
						$image = imagecreatefromjpeg($file);						
						$contents = "";
						ob_start();
						imagejpeg($image);
						$contents = ob_get_contents();
						ob_end_clean();							
						$ImageData = addslashes(base64_encode($contents));
						imagedestroy($image);							
						$mask = addslashes(urldecode($row['mask']));		
						$scanDocDataXML .= "<scanDocDataChild>";
						$scanDocDataXML .= "<patient_id >".$row['patient_id']."</patient_id >";
						$scanDocDataXML .= "<scan_doc_add>".$row['scan_doc_add']."</scan_doc_add>";
						$scanDocDataXML .= "<scan_type_folder>".$row['scan_type_folder']."</scan_type_folder>";
						$scanDocDataXML .= "<created_date>".$row['created_date']."</created_date>";
						$scanDocDataXML .= "<mask><![CDATA[$mask]]></mask>";
						$scanDocDataXML .= "<doc_data><![CDATA[$ImageData]]></doc_data>";							
						$scanDocDataXML .= "<scan_upload_data></scan_upload_data>";							
						$scanDocDataXML .= "</scanDocDataChild>";
						
						//die($scanDocDataXML);
						
					}
				}
				elseif(trim(strtolower($pathParts['extension'])) == "pdf"){
					$docAddress = $mainDir.$dataBaseDocAddress;
					$file = $docAddress;
					if(file_exists($file)){
						//geting pdf of patient under surgery consent forms	folder					
						$scanUploadData = $scanUploadContent = "";
						$mask = addslashes(urldecode($row['mask']));		
						$scanUploadContent 	= file_get_contents($file);					
						$scanUploadData 	= addslashes(base64_encode($scanUploadContent));
						$scanDocDataXML .= "<scanDocDataChild>";
						$scanDocDataXML .= "<patient_id >".$row['patient_id']."</patient_id >";
						$scanDocDataXML .= "<scan_doc_add>".$row['scan_doc_add']."</scan_doc_add>";
						$scanDocDataXML .= "<scan_type_folder>".$row['scan_type_folder']."</scan_type_folder>";
						$scanDocDataXML .= "<created_date>".$row['created_date']."</created_date>";
						$scanDocDataXML .= "<mask><![CDATA[$mask]]></mask>";
						$scanDocDataXML .= "<doc_data></doc_data>";
						$scanDocDataXML .= "<scan_upload_data><![CDATA[$scanUploadData]]></scan_upload_data>";							
						$scanDocDataXML .= "</scanDocDataChild>";						
						//geting pdf of patient under surgery consent forms	folder
					}
				}							
			}
			$a++;
		}
		$scanDocDataXML .= "</scanDocData>";
	}
}

/////////////////////////			
$strQryGetPatientConsentPdfData="SELECT scff.*
										FROM schedule_appointments sa									
										INNER JOIN surgery_consent_filled_form scff ON (scff.patient_id  = sa.sa_patient_id AND scff.appt_id  = sa.id AND scff.movedToTrash = '0')
										WHERE sa.id = ".$schedule_id;
$rsQryGetPatientConsentPdfData = imw_query($strQryGetPatientConsentPdfData);	

if(!$rsQryGetPatientConsentPdfData){
	echo ("Error : ". imw_error()."<br>".$strQryGetPatientConsentPdfData);
}
else{
	$pdfDocData = array();
	$a = 0;
	if(imw_num_rows($rsQryGetPatientConsentPdfData)>0){		
		$aa = "?>";
		$pdfDocDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
		$pdfDocDataXML .= "<pdfDocData>";
		while ($row = imw_fetch_array($rsQryGetPatientConsentPdfData)) {
			//echo '<pre>';
			//print_r($row);			
			$contents = "";
			$docAddress = urldecode($row['iolink_pdf_path']);	
			$surgeryConsentDataDB = ($row['surgery_consent_data']);
			//$docAddress = html_entity_decode($docAddress);
			if(trim($docAddress) && file_exists($docAddress)){
				
			}
			if(file_exists($docAddress)){
				$contents = file_get_contents($docAddress);					
				$pdfData = addslashes(base64_encode($contents));				
				//$pdfData = addslashes($contents);							
				$pdfDocDataXML .= "<pdfDocDataChild>";
				$pdfDocDataXML .= "<patient_id >".$row['patient_id']."</patient_id >";
				$pdfDocDataXML .= "<consent_template_id >".$row['consent_template_id']."</consent_template_id >";
				$surgeryConsentName = stripslashes($row['surgery_consent_name']);			
				$pdfDocDataXML .= "<surgery_consent_name><![CDATA[$surgeryConsentName]]></surgery_consent_name>";
				
				$pdfDocDataXML .= "<form_created_date>".$row['form_created_date']."</form_created_date>";			
				
				//$scanDocDataXML .= "<doc_data>".$data."</doc_data>";			
				$pdfDocDataXML .= "<pdf_doc_data><![CDATA[$pdfData]]></pdf_doc_data>";					
				$pdfDocDataXML .= "</pdfDocDataChild>";
				$a++;
			}
		}
		$pdfDocDataXML .= "</pdfDocData>";
	}
}
/////////////////////////			

$faceSheetDocData = array();
$a = 0;

$getPathFaceSheet = $httpfolder.'/PatientId_'.$patientId.'/faceSheet.pdf';
clearstatcache();
if (file_exists($getPathFaceSheet)) {	
	$aa = "?>";
	$faceSheetDocDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
	$faceSheetDocDataXML .= "<faceSheetDocData>";
	$faceSheetpdfData = "";
	$faceSheetcontents = "";
	$faceSheetcontents = file_get_contents($getPathFaceSheet);	
	$faceSheetPdfData = addslashes(base64_encode($faceSheetcontents));
	$faceSheetDocDataXML .= "<faceSheetDocDataChild>";
	$faceSheetDocDataXML .= "<file_name><![CDATA[$patientName]]></file_name>";
	$faceSheetDocDataXML .= "<patient_id>".$patientId."</patient_id >";
	$faceSheetDocDataXML .= "<face_sheet_doc_data><![CDATA[$faceSheetPdfData]]></face_sheet_doc_data>";
	$faceSheetDocDataXML .= "</faceSheetDocDataChild>";	
	$faceSheetDocDataXML .= "</faceSheetDocData>";
}
		
/////////////////////////Pre-op Health Questionnaire
$strQryGetPreOpHealthQuestionnaire="SELECT scpohq.*
										FROM schedule_appointments sa									
										INNER JOIN surgery_center_pre_op_health_ques scpohq ON scpohq.patient_id = sa.sa_patient_id 								
										WHERE sa.id = ".$schedule_id;
										
$rsQryGetPreOpHealthQuestionnaire = imw_query($strQryGetPreOpHealthQuestionnaire);	

if(!$rsQryGetPreOpHealthQuestionnaire){
	echo ("Error : ". imw_error()."<br>".$strQryGetPreOpHealthQuestionnaire);
}
else{
	$patientPreOpHealthQuestionnaire = array();
	$a = 0;
	if(imw_num_rows($rsQryGetPreOpHealthQuestionnaire)>0){		
		$aa = "?>";
		$patientPreOpDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
		$patientPreOpDataXML .= "<patientPreOpData>";
		while ($row = imw_fetch_array($rsQryGetPreOpHealthQuestionnaire)) {			
			$patientPreOpDataXML .= "<patientPreOpDataChild>";
			$patientPreOpDataXML .= "<patient_id>".$row['patient_id']."</patient_id>";
			$patientPreOpDataXML .= "<heartTrouble>".$row['heartTrouble']."</heartTrouble>";
			$patientPreOpDataXML .= "<stroke>".$row['stroke']."</stroke>";
			$patientPreOpDataXML .= "<HighBP>".$row['HighBP']."</HighBP>";
			$patientPreOpDataXML .= "<anticoagulationTherapy>".$row['anticoagulationTherapy']."</anticoagulationTherapy>";
			$patientPreOpDataXML .= "<asthma>".$row['asthma']."</asthma>";
			$patientPreOpDataXML .= "<diabetes>".$row['diabetes']."</diabetes>";
			$patientPreOpDataXML .= "<insulinDependence>".$row['insulinDependence']."</insulinDependence>";
			$patientPreOpDataXML .= "<epilepsy>".$row['epilepsy']."</epilepsy>";			
			$patientPreOpDataXML .= "<restlessLegSyndrome>".$row['restlessLegSyndrome']."</restlessLegSyndrome>";
			$patientPreOpDataXML .= "<hepatitis >".$row['hepatitis']."</hepatitis >";
			$patientPreOpDataXML .= "<hepatitisA>".$row['hepatitisA']."</hepatitisA>";
			$patientPreOpDataXML .= "<hepatitisB>".$row['hepatitisB']."</hepatitisB>";
			$patientPreOpDataXML .= "<hepatitisC>".$row['hepatitisC']."</hepatitisC>";
			$patientPreOpDataXML .= "<kidneyDisease>".$row['kidneyDisease']."</kidneyDisease>";
			$patientPreOpDataXML .= "<shunt>".$row['shunt']."</shunt>";
			$patientPreOpDataXML .= "<fistula>".$row['fistula']."</fistula>";
			$patientPreOpDataXML .= "<hivAutoimmuneDiseases>".$row['hivAutoimmuneDiseases']."</hivAutoimmuneDiseases>";
			$patientPreOpDataXML .= "<hivTextArea>".stripslashes($row['hivTextArea'])."</hivTextArea>";
			$patientPreOpDataXML .= "<cancerHistory>".$row['cancerHistory']."</cancerHistory>";			
			$patientPreOpDataXML .= "<cancerHistoryDesc>".stripslashes($row['cancerHistoryDesc'])."</cancerHistoryDesc>";
			$patientPreOpDataXML .= "<organTransplant>".stripslashes($row['organTransplant'])."</organTransplant>";
			$patientPreOpDataXML .= "<organTransplantDesc >".$row['organTransplantDesc']."</organTransplantDesc >";
			$patientPreOpDataXML .= "<anesthesiaBadReaction>".$row['anesthesiaBadReaction']."</anesthesiaBadReaction>";
			$patientPreOpDataXML .= "<tuberculosis>".$row['tuberculosis']."</tuberculosis>";
			$patientPreOpDataXML .= "<otherTroubles >".$row['otherTroubles']."</otherTroubles >";
			$patientPreOpDataXML .= "<walker>".$row['walker']."</walker>";
			$patientPreOpDataXML .= "<contactLenses>".$row['contactLenses']."</contactLenses>";
			$patientPreOpDataXML .= "<smoke>".$row['smoke']."</smoke>";
			$patientPreOpDataXML .= "<smokeHowMuch>".stripslashes($row['smokeHowMuch'])."</smokeHowMuch>";
			$patientPreOpDataXML .= "<smokeAdvise>".stripslashes($row['smokeAdvise'])."</smokeAdvise>";
			$patientPreOpDataXML .= "<alchohol>".$row['alchohol']."</alchohol>";
			$patientPreOpDataXML .= "<alchoholHowMuch>".stripslashes($row['alchoholHowMuch'])."</alchoholHowMuch>";
			$patientPreOpDataXML .= "<alchoholAdvise>".stripslashes($row['alchoholAdvise'])."</alchoholAdvise>";
			$patientPreOpDataXML .= "<autoInternalDefibrillator>".$row['autoInternalDefibrillator']."</autoInternalDefibrillator>";
			$patientPreOpDataXML .= "<metalProsthetics>".$row['metalProsthetics']."</metalProsthetics>";
			$patientPreOpDataXML .= "<notes>".stripslashes($row['notes'])."</notes >";			
			$patientPreOpDataXML .= "<patientSign>".$row['patientSign']."</patientSign>";
			$patientPreOpDataXML .= "<patient_sign_image_path>".stripslashes($row['patient_sign_image_path'])."</patient_sign_image_path>";
			$patientPreOpDataXML .= "<witness_sign_image_path>".stripslashes($row['witness_sign_image_path'])."</witness_sign_image_path>";
			$patientPreOpDataXML .= "<dateQuestionnaire>".stripslashes($row['dateQuestionnaire'])."</dateQuestionnaire>";			
			$patientPreOpDataXML .= "<emergencyContactPerson>".stripslashes($row['emergencyContactPerson'])."</emergencyContactPerson>";
			$patientPreOpDataXML .= "<emergencyContactPhone>".stripslashes($row['emergencyContactPhone'])."</emergencyContactPhone>";
			$patientPreOpDataXML .= "<witnessname>".stripslashes($row['witnessname'])."</witnessname>";
			$patientPreOpDataXML .= "<witnessSign >".stripslashes($row['witnessSign'])."</witnessSign >";
			$patientPreOpDataXML .= "<form_status>".stripslashes($row['form_status'])."</form_status>";
			$patientPreOpDataXML .= "<allergies_status>".stripslashes($row['allergies_status'])."</allergies_status>";		
			$patientPreOpDataXML .= "<allergies_status_reviewed>".stripslashes($row['allergies_status_reviewed'])."</allergies_status_reviewed>";											
			
			$patientPreOpDataXML .= "<heartTroubleDesc><![CDATA[".stripslashes($row['heartTroubleDesc'])."]]></heartTroubleDesc>";
			$patientPreOpDataXML .= "<strokeDesc><![CDATA[".stripslashes($row['strokeDesc'])."]]></strokeDesc>";
			$patientPreOpDataXML .= "<HighBPDesc><![CDATA[".stripslashes($row['HighBPDesc'])."]]></HighBPDesc>";
			$patientPreOpDataXML .= "<anticoagulationTherapyDesc><![CDATA[".stripslashes($row['anticoagulationTherapyDesc'])."]]></anticoagulationTherapyDesc>";
			$patientPreOpDataXML .= "<asthmaDesc><![CDATA[".stripslashes($row['asthmaDesc'])."]]></asthmaDesc>";
			$patientPreOpDataXML .= "<tuberculosisDesc><![CDATA[".stripslashes($row['tuberculosisDesc'])."]]></tuberculosisDesc>";
			$patientPreOpDataXML .= "<diabetesDesc><![CDATA[".stripslashes($row['diabetesDesc'])."]]></diabetesDesc>";
			$patientPreOpDataXML .= "<epilepsyDesc><![CDATA[".stripslashes($row['epilepsyDesc'])."]]></epilepsyDesc>";
			$patientPreOpDataXML .= "<restlessLegSyndromeDesc><![CDATA[".stripslashes($row['restlessLegSyndromeDesc'])."]]></restlessLegSyndromeDesc>";
			$patientPreOpDataXML .= "<hepatitisDesc><![CDATA[".stripslashes($row['hepatitisDesc'])."]]></hepatitisDesc>";
			$patientPreOpDataXML .= "<kidneyDiseaseDesc><![CDATA[".stripslashes($row['kidneyDiseaseDesc'])."]]></kidneyDiseaseDesc>";
			$patientPreOpDataXML .= "<anesthesiaBadReactionDesc><![CDATA[".stripslashes($row['anesthesiaBadReactionDesc'])."]]></anesthesiaBadReactionDesc>";
			$patientPreOpDataXML .= "<walkerDesc><![CDATA[".stripslashes($row['walkerDesc'])."]]></walkerDesc>";
			$patientPreOpDataXML .= "<contactLensesDesc><![CDATA[".stripslashes($row['contactLensesDesc'])."]]></contactLensesDesc>";
			$patientPreOpDataXML .= "<autoInternalDefibrillatorDesc><![CDATA[".stripslashes($row['autoInternalDefibrillatorDesc'])."]]></autoInternalDefibrillatorDesc>";

			//$httpNewFolder =  realpath(dirname(__FILE__));
			
			$patient_sign_image_path = $row['patient_sign_image_path'];
			if($patient_sign_image_path) {
				$patientTmpPth = str_ireplace("../../","",$patient_sign_image_path);
				$patientsignFullPath = data_path().$patientTmpPth;
				$patient_sign_image_content = file_get_contents($patientsignFullPath);	
				$patientSignImageData = addslashes(base64_encode($patient_sign_image_content));
				$patientPreOpDataXML .= "<patient_sign_image_data><![CDATA[$patientSignImageData]]></patient_sign_image_data>";
			}
			
			$witness_sign_image_path = $row['witness_sign_image_path'];
			if($witness_sign_image_path) {
				$witnessTmpPth = str_ireplace("../../","",$witness_sign_image_path);
				$witnesssignFullPath =  data_path().$witnessTmpPth;
				$witness_sign_image_content = file_get_contents($witnesssignFullPath);	
				$witnessSignImageData = addslashes(base64_encode($witness_sign_image_content));
				$patientPreOpDataXML .= "<witness_sign_image_data><![CDATA[$witnessSignImageData]]></witness_sign_image_data>";
				
			}
			$patientPreOpDataXML .= "</patientPreOpDataChild>";
			
			/////////////////////////////////////////////////////////////////////////
			$strQryGetPatientAdminHealthQues = "SELECT * FROM surgery_center_health_question_admin where preOpHealthQuesId = ".$row['preOpHealthQuesId']." AND preOpHealthQuesId != '0'";
			$rsQryGetPatientAdminHealthQues = imw_query($strQryGetPatientAdminHealthQues);	
			if($rsQryGetPatientAdminHealthQues){				
				$patientAdminHealthQues = array();			
					if(imw_num_rows($rsQryGetPatientAdminHealthQues)>0){		
						$aa = "?>";
						$patientAdminHealthDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
						$patientAdminHealthDataXML .= "<patientAdminHealthData>";
						while ($rowAdminHealthQues = imw_fetch_array($rsQryGetPatientAdminHealthQues)) {			
							$patientAdminHealthDataXML .= "<patientAdminHealthDataChild>";
							$patientAdminHealthDataXML .= "<adminQuestion>".$rowAdminHealthQues['adminQuestion']."</adminQuestion>";
							$patientAdminHealthDataXML .= "<adminQuestionStatus>".$rowAdminHealthQues['adminQuestionStatus']."</adminQuestionStatus>";											
							$patientAdminHealthDataXML .= "<adminQuestionDesc>".stripslashes($rowAdminHealthQues['adminQuestionDesc'])."</adminQuestionDesc>";
							$patientAdminHealthDataXML .= "</patientAdminHealthDataChild>";
						}
						$patientAdminHealthDataXML .= "</patientAdminHealthData>";
					}			
			}
			
			$a++;
		}
		$patientPreOpDataXML .= "</patientPreOpData>";
	}
}

/////////////////////////////////////////////////////////////////////
//GET ALLEGIES 
$strQryGetPatientHealthQuesAllergy="SELECT ls.title AS allergy_name, ls.comments AS reaction_name FROM schedule_appointments sa 
									INNER JOIN lists ls ON (ls.pid = sa.sa_patient_id  AND ls.type in(3,7) AND ls.allergy_status = 'Active')
									WHERE sa.id = '".$schedule_id."'";
$rsQryGetPatientHealthQuesAllergy = imw_query($strQryGetPatientHealthQuesAllergy);
$patientHealthQuesAllergy = array();			
if(imw_num_rows($rsQryGetPatientHealthQuesAllergy)>0){		
	$aa = "?>";
	$patientHealthQuesAllergyDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
	$patientHealthQuesAllergyDataXML .= "<patientHealthQuesAllergyData>";
	while ($rowPatientHealthQuesAllergy = imw_fetch_array($rsQryGetPatientHealthQuesAllergy)) {			
		$patientHealthQuesAllergyDataXML .= "<patientHealthQuesAllergyDataChild>";
		$patientHealthQuesAllergyDataXML .= "<allergy_name>".$rowPatientHealthQuesAllergy['allergy_name']."</allergy_name>";
		$patientHealthQuesAllergyDataXML .= "<reaction_name>".$rowPatientHealthQuesAllergy['reaction_name']."</reaction_name>";											
		$patientHealthQuesAllergyDataXML .= "</patientHealthQuesAllergyDataChild>";
	}
	$patientHealthQuesAllergyDataXML .= "</patientHealthQuesAllergyData>";
}
//GET NKDA STATUS AND NO MEDICATION STATUS
$strQryAllergyNoValue="SELECT cm.module_name, cm.no_value AS allergies_med_no_value, cm.comments AS allergies_med_comments  FROM schedule_appointments sa 
									INNER JOIN commonNoMedicalHistory cm ON (cm.patient_id = sa.sa_patient_id  AND (cm.module_name = 'Allergy' || cm.module_name = 'Medication'))
									WHERE sa.id = '".$schedule_id."'";
$rsQryAllergyNoValue = imw_query($strQryAllergyNoValue);
$patientAllergyNoValue = array();			
if(imw_num_rows($rsQryAllergyNoValue)>0){		
	$aa = "?>";
	$patientAllergyNoValueDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
	$patientAllergyNoValueDataXML .= "<patientAllergyNoValueData>";
	$patientAllergyNoValueDataXML .= "<patientAllergyNoValueDataChild>";
	while($rowQryAllergyNoValue = imw_fetch_array($rsQryAllergyNoValue)) {
		if($rowQryAllergyNoValue['module_name'] == 'Allergy') {
			$patientAllergyNoValueDataXML .= "<allergies_no_value>".stripslashes($rowQryAllergyNoValue['allergies_med_no_value'])."</allergies_no_value>";
		}
		if($rowQryAllergyNoValue['module_name'] == 'Medication') {
			$patientAllergyNoValueDataXML .= "<medication_no_value>".stripslashes($rowQryAllergyNoValue['allergies_med_no_value'])."</medication_no_value>";
			$patientAllergyNoValueDataXML .= "<medication_comments>".stripslashes($rowQryAllergyNoValue['allergies_med_comments'])."</medication_comments>";
		}
	}
	$patientAllergyNoValueDataXML .= "</patientAllergyNoValueDataChild>";
	$patientAllergyNoValueDataXML .= "</patientAllergyNoValueData>";
}
/////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////
//GET MEDICATION
$strQryGetPatientHealthQuesMed="SELECT li.title AS prescription_medication_name, li.destination AS prescription_medication_desc, li.sig AS prescription_medication_sig
								FROM schedule_appointments sa									
								INNER JOIN lists li ON (li.pid = sa.sa_patient_id and li.type in(1,4) AND li.allergy_status = 'Active') 																			
								WHERE sa.id = ".$schedule_id;

$rsQryGetPatientHealthQuesMed = imw_query($strQryGetPatientHealthQuesMed);	
if($rsQryGetPatientHealthQuesMed){				
	$patientHealthQuesMed = array();			
	if(imw_num_rows($rsQryGetPatientHealthQuesMed)>0){		
		$aa = "?>";
		$patientHealthQuesMedDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
		$patientHealthQuesMedDataXML .= "<patientHealthQuesMedData>";
		while ($rowPatientHealthQuesMed = imw_fetch_array($rsQryGetPatientHealthQuesMed)) {			
			$patientHealthQuesMedDataXML .= "<patientHealthQuesMedDataChild>";
			$patientHealthQuesMedDataXML .= "<prescription_medication_name>".$rowPatientHealthQuesMed['prescription_medication_name']."</prescription_medication_name>";
			$patientHealthQuesMedDataXML .= "<prescription_medication_desc>".$rowPatientHealthQuesMed['prescription_medication_desc']."</prescription_medication_desc>";
			$patientHealthQuesMedDataXML .= "<prescription_medication_sig>".$rowPatientHealthQuesMed['prescription_medication_sig']."</prescription_medication_sig>";
			$patientHealthQuesMedDataXML .= "</patientHealthQuesMedDataChild>";
		}
		$patientHealthQuesMedDataXML .= "</patientHealthQuesMedData>";
	}			
}			
/////////////////////////////////////////////////////////////////////			


/////////////////////////	
/*
$strQryGetPracPatientAllery="SELECT scpa.*
										FROM schedule_appointments sa									
										INNER JOIN surgery_center_patient_allergy scpa ON scpa.patient_id = sa.sa_patient_id 								
										WHERE sa.id = '".$schedule_id."'";	
$queryGetAllergyRes = @imw_query($strQryGetPracPatientAllery);
if(@imw_num_rows($queryGetAllergyRes) == 0){
	*/
	$strQryGetPracPatientAllery="SELECT li.*
										FROM schedule_appointments sa									
										INNER JOIN lists li ON (li.pid = sa.sa_patient_id and li.type in(3,7)) 																			
										WHERE sa.id = ".$schedule_id;
	$rsQryGetPracPatientAllery = @imw_query($strQryGetPracPatientAllery);	
	if($rsQryGetPracPatientAllery){		
		$pracPatientAlleryData = array();
		$a = 0;		
		if(@imw_num_rows($rsQryGetPracPatientAllery)>0){		
			$aa = "?>";
			$pracPatientAlleryDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
			$pracPatientAlleryDataXML .= "<pracPatientAlleryData>";
			while ($row = @imw_fetch_array($rsQryGetPracPatientAllery)) {
				$pracPatientAlleryDataXML .= "<pracPatientAlleryDataChild>";
				/*
				$pracPatientAlleryDataXML .= "<title>".$row['title']."</title>";				
				$pracPatientAlleryDataXML .= "</pracPatientAlleryDataChild>";
				*/
				$a++;			
			}
			$pracPatientAlleryDataXML .= "</pracPatientAlleryData>";
		}
	
	}
	
//}
/////////////////////////	
/*
$strQryGetPracPatientMed="SELECT scpm.*
										FROM schedule_appointments sa									
										INNER JOIN surgery_center_patient_medication scpm ON scpm.patient_id = sa.sa_patient_id 								
										WHERE sa.id = ".$schedule_id;	
$queryGetPracPatientMed = @imw_query($strQryGetPracPatientMed);
if(@imw_num_rows($queryGetPracPatientMed) == 0){
	*/
	$strQryGetPracPatientMed="SELECT li.*
										FROM schedule_appointments sa									
										INNER JOIN lists li ON (li.pid = sa.sa_patient_id and li.type in(1,4)) 																			
										WHERE sa.id = ".$schedule_id;
	$rsQryGetPracPatientMed = @imw_query($strQryGetPracPatientMed);	
	if($rsQryGetPracPatientMed){		
		$pracPatientMedData = array();
		$a = 0;		
		if(@imw_num_rows($rsQryGetPracPatientMed)>0){		
			$aa = "?>";
			$pracPatientMedDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
			$pracPatientMedDataXML .= "<pracPatientMedData>";
			while ($row = imw_fetch_array($rsQryGetPracPatientMed)) {
				$pracPatientMedDataXML .= "<pracPatientMedDataChild>";
				/*
				$pracPatientMedDataXML .= "<title>".$row['title']."</title>";
				$pracPatientMedDataXML .= "<destination>".$row['destination']."</destination>";				
				*/
				$pracPatientMedDataXML .= "</pracPatientMedDataChild>";
				$a++;			
			}
			$pracPatientMedDataXML .= "</pracPatientMedData>";
		}
	
	}
	
//}

/////////////////////////			
//getting Oculer PDF Data------satrt
$oculerHxDocData = array();
$oculerHxDocDataXML = "";
$getPathOcularHx = $httpfolder.'/PatientId_'.$patientId.'/OcularHx.pdf';
clearstatcache();
if (file_exists($getPathOcularHx)) {	
	$aa = "?>";
	$oculerHxDocDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
	$oculerHxDocDataXML .= "<oculerHxDocData>";
	$oculerHxPdfData = "";
	$oculerHxContents = "";
	$oculerHxContents = file_get_contents($getPathOcularHx);	
	$oculerHxPdfData = addslashes(base64_encode($oculerHxContents));
	$oculerHxDocDataXML .= "<oculerHxDocDataChild>";
	$oculerHxDocDataXML .= "<file_name>OcularHx</file_name >";
	$oculerHxDocDataXML .= "<patient_id>".$patientId."</patient_id >";
	$oculerHxDocDataXML .= "<oculer_doc_doc_data><![CDATA[$oculerHxPdfData]]></oculer_doc_doc_data>";
	$oculerHxDocDataXML .= "</oculerHxDocDataChild>";	
	$oculerHxDocDataXML .= "</oculerHxDocData>";
}
//getting Oculer PDF Data------end										
/////////////////////////					
//echo $oculerHxDocDataXML;  
//exit;
//getting Sx alert------start

$strQryGetSxAlert="SELECT *  FROM alert_tbl WHERE patient_id='".$patientId."' AND patient_id!='0' AND substr(alert_to_show_under, 7, 1) = '1' AND is_deleted  = '0'";
$rsQryGetSxAlert = @imw_query($strQryGetSxAlert);	
if($rsQryGetSxAlert){		
	$sxAlertData = array();
	$a = 0;		
	if(@imw_num_rows($rsQryGetSxAlert)>0){		
		$aa = "?>";
		$sxAlertDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
		$sxAlertDataXML .= "<sxAlertData>";
		while ($row = imw_fetch_array($rsQryGetSxAlert)) {
			$sxAlertDataXML .= "<sxAlertDataChild>";
			$sxAlertDataXML .= "<alertId>".$row['alertId']."</alertId>";
			$sxAlertDataXML .= "<alertContent>".$row['alertContent']."</alertContent>";
			$sxAlertDataXML .= "<saveDateTime>".$row['saveDateTime']."</saveDateTime>";				
			$sxAlertDataXML .= "</sxAlertDataChild>";
			$a++;			
		}
		$sxAlertDataXML .= "</sxAlertData>";
	}
}

//getting Sx alert------end

/////////////////////////	
//getting AScan PDF Data------satrt
$aScanDocData = array();
$aScanDocDataXML = "";
$patientIdFolder = $httpfolder.'/PatientId_'.$patientId;
$getPathAScan = $patientIdFolder.'/AScan.pdf';
clearstatcache();
if (file_exists($getPathAScan)) {	
	$aa = "?>";
	$aScanDocDataXML 	 = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
	$aScanDocDataXML 	.= "<aScanDocData>";
	$aScanPdfData 		 = "";
	$aScanContents 		 = "";
	$aScanContents 		 = file_get_contents($getPathAScan);	
	$aScanPdfData 		 = addslashes(base64_encode($aScanContents));
	$aScanDocDataXML 	.= "<aScanDocDataChild>";
	$aScanDocDataXML 	.= "<file_name>AScan</file_name >";
	$aScanDocDataXML 	.= "<patient_id>".$patientId."</patient_id >";
	$aScanDocDataXML 	.= "<ascan_doc_data><![CDATA[$aScanPdfData]]></ascan_doc_data>";
	$aScanDocDataXML 	.= "</aScanDocDataChild>";	
	
	//START GET ASCAN UPLOADED PDF
	$aScanFilesArr		= array();
	$aScanPattern		= 'AScan_'.$sa_date_pattern;
	$aScanFilesArr		= getFilesFromFolder($patientIdFolder,"pdf",$aScanPattern);	
	foreach($aScanFilesArr as $aScanFiles) {
		$getPathAScanPdfFiles = $httpfolder.'/PatientId_'.$patientId.'/'.$aScanFiles;
		if(file_exists($getPathAScanPdfFiles)) {
			$aScanFileName		 = $aScanFiles;
			$aScanFileName		 = str_ireplace($sa_date_pattern.'_','',$aScanFileName);
			$aScanFileName		 = str_ireplace('.pdf','',$aScanFileName);
			$aScanPdfData 		 = "";
			$aScanContents 		 = "";
			$aScanContents 		 = file_get_contents($getPathAScanPdfFiles);	
			$aScanPdfData 		 = addslashes(base64_encode($aScanContents));
			$aScanDocDataXML 	.= "<aScanDocDataChild>";
			$aScanDocDataXML 	.= "<file_name>".$aScanFileName."</file_name >";
			$aScanDocDataXML 	.= "<patient_id>".$patientId."</patient_id >";
			$aScanDocDataXML 	.= "<ascan_doc_data><![CDATA[$aScanPdfData]]></ascan_doc_data>";
			$aScanDocDataXML 	.= "</aScanDocDataChild>";	
		}
	}
	//END GET ASCAN UPLOADED PDF
	
	$aScanDocDataXML 	.= "</aScanDocData>";
}
//getting AScan PDF Data------end										
/////////////////////////					
//echo $aScanDocDataXML;  
//exit;

/////////////////////////	
//getting IOL_Master PDF Data------satrt
$iolMasterDocData = array();
$iolMasterDocDataXML = "";
$patientIdFolder = $httpfolder.'/PatientId_'.$patientId;
$getPathIOL_Master = $httpfolder.'/PatientId_'.$patientId.'/IOL_Master.pdf';
clearstatcache();
if (file_exists($getPathIOL_Master)) {	
	$aa = "?>";
	$iolMasterDocDataXML 	 = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
	$iolMasterDocDataXML 	.= "<iolMasterDocData>";
	$iolMasterPdfData 		 = "";
	$iolMasterContents 		 = "";
	$iolMasterContents 		 = file_get_contents($getPathIOL_Master);	
	$iolMasterPdfData 		 = addslashes(base64_encode($iolMasterContents));
	$iolMasterDocDataXML 	.= "<iolMasterDocDataChild>";
	$iolMasterDocDataXML 	.= "<file_name>IOL_Master</file_name >";
	$iolMasterDocDataXML 	.= "<patient_id>".$patientId."</patient_id >";
	$iolMasterDocDataXML 	.= "<iol_master_doc_data><![CDATA[$iolMasterPdfData]]></iol_master_doc_data>";
	$iolMasterDocDataXML 	.= "</iolMasterDocDataChild>";
	
	//START GET IOL_MASTER UPLOADED PDF
	$iolMasterFilesArr		= array();
	$iolMasterPattern		= 'IOL_Master_'.$sa_date_pattern;
	$iolMasterFilesArr		= getFilesFromFolder($patientIdFolder,"pdf",$iolMasterPattern);	
	foreach($iolMasterFilesArr as $iolMasterFiles) {
		$getPathIOL_MasterPdfFiles = $httpfolder.'/PatientId_'.$patientId.'/'.$iolMasterFiles;
		if(file_exists($getPathIOL_MasterPdfFiles)) {
			$iolMasterFileName		 = $iolMasterFiles;
			$iolMasterFileName		 = str_ireplace($sa_date_pattern.'_','',$iolMasterFileName);
			$iolMasterFileName		 = str_ireplace('.pdf','',$iolMasterFileName);
			$iolMasterPdfData 		 = "";
			$iolMasterContents 		 = "";
			$iolMasterContents 		 = file_get_contents($getPathIOL_MasterPdfFiles);	
			$iolMasterPdfData 		 = addslashes(base64_encode($iolMasterContents));
			$iolMasterDocDataXML 	.= "<iolMasterDocDataChild>";
			$iolMasterDocDataXML 	.= "<file_name>".$iolMasterFileName."</file_name >";
			$iolMasterDocDataXML 	.= "<patient_id>".$patientId."</patient_id >";
			$iolMasterDocDataXML 	.= "<iol_master_doc_data><![CDATA[$iolMasterPdfData]]></iol_master_doc_data>";
			$iolMasterDocDataXML 	.= "</iolMasterDocDataChild>";	
		}
	}
	//END GET IOL_MASTER UPLOADED PDF
		
	$iolMasterDocDataXML 	.= "</iolMasterDocData>";
}
//getting IOL_Master PDF Data------end										
/////////////////////////					
//echo $iolMasterDocDataXML;  
//exit;

/////////////////////////	
// Get IOL Data

//================= Getting Lense Type to store in array===========
$lenses_iol_type_arr = array();
$getLenseTypeStr = "SELECT * FROM lenses_iol_type ORDER BY iol_type_id";
$getLenseTypeQry = imw_query($getLenseTypeStr);
while($getLenseTypeRow = imw_fetch_array($getLenseTypeQry)) {
	$lenses_iol_type_arr[$getLenseTypeRow['iol_type_id']] = array(
																	'lenses_iol_type'=>$getLenseTypeRow['lenses_iol_type'],
																	'lenses_category'=>$getLenseTypeRow['lenses_category'],
																	'lenses_manufacturer'=>$getLenseTypeRow['lenses_manufacturer'],
																	'lenses_brand'=>$getLenseTypeRow['lenses_brand']
																);
}

//get data from sx planing sheet to get first record date only
$sxQry="select sx.sx_plan_dos, sx.surgeon_id from chart_sx_plan_sheet as sx 
RIGHT JOIN chart_sps_ast_plan_tpa as tpa ON sx.id=tpa.id_chart_sx_plan_sheet 
WHERE sx.sx_plan_dos<='".$sa_date."' AND sx.del_status = 0 AND sx.patient_id='".$patientId."'
ORDER BY sx.sx_plan_dos DESC LIMIT 0,1";
$sxRes		= imw_query($sxQry);
$sxNum 		= imw_num_rows($sxRes);
$sxRow 		= imw_fetch_array($sxRes);
	
//==================================================
$iolinkIolMasterQry 	= "SELECT * FROM `iol_master_tbl` WHERE patient_id='".$patientId."' AND examDate<='".$sa_date."' AND purged = '0' ORDER BY iol_master_id DESC LIMIT 0,1";	
$iolinkIolMasterRes		= imw_query($iolinkIolMasterQry);
$iolinkIolMasterNumRow 	= imw_num_rows($iolinkIolMasterRes);
$iolinkIolMasterRow 	= imw_fetch_array($iolinkIolMasterRes);
	
if($sxNum>0 && $iolinkIolMasterNumRow>0)
{
	if($sxRow['sx_plan_dos']>$iolinkIolMasterRow['examDate'])
	$sxData=true;
	else
	$iolinkData=true;
}
else
{
	if($sxNum>0)$sxData=true;
	elseif($iolinkIolMasterNumRow>0)$iolinkData=true;
}
	//
if($iolinkIolMasterNumRow>0 && $iolinkData==true)
{
	extract($iolinkIolMasterRow);
	
	if($iolinkIolMasterRow['selecedIOLsOD'])
		$opRoomDefaultOD	=	getLensesData($iolinkIolMasterRow['selecedIOLsOD'],'lenses_iol_type');
	else
		$opRoomDefaultOD	=	getLensesData($iolinkIolMasterRow['iol1OD'],'lenses_iol_type');
		
	
	if($iolinkIolMasterRow['selecedIOLsOS'])
		$opRoomDefaultOS	=	getLensesData($iolinkIolMasterRow['selecedIOLsOS'],'lenses_iol_type');
	else
		$opRoomDefaultOS	=	getLensesData($iolinkIolMasterRow['iol1OS'],'lenses_iol_type');	
		
	
	$iolinkIolMasterDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
	$iolinkIolMasterDataXML .= "<iolinkIolMasterData>";
	$iolinkIolMasterDataXML .= "<iolinkIolMasterDataChild>";
	$iolinkIolMasterDataXML .= "<patient_id>".$patientId."</patient_id>";
	
	for($loop = 1; $loop <=4 ; $loop++)
	{
		$iolModelID_OD			=	$iolinkIolMasterRow['iol'.$loop.'OD'];
		$iolModelID_OS			=	$iolinkIolMasterRow['iol'.$loop.'OS'];
		$iolPower_OD			=	$iolinkIolMasterRow['iol'.$loop.'PowerOD'];
		$iolPower_OS			=	$iolinkIolMasterRow['iol'.$loop.'PowerOS'];
		
		$iolinkManufacturerOD	= 	getLensesData($iolModelID_OD,'lenses_manufacturer'); 
		$iolinkLensBrandOD		=	getLensesData($iolModelID_OD,'lenses_brand');
		$iolinkModelOD			=	getLensesData($iolModelID_OD,'lenses_iol_type');
		$iolinkDiopterOD		=	$iolPower_OD;
		
		$iolinkManufacturerOS	=	getLensesData($iolModelID_OS,'lenses_manufacturer');
		$iolinkLensBrandOS		=	getLensesData($iolModelID_OS,'lenses_brand');
		$iolinkModelOS			=	getLensesData($iolModelID_OS,'lenses_iol_type');
		$iolinkDiopterOS		=	$iolPower_OS;
		
		
		$iolinkIolMasterDataXML .= "<iolinkManufacturerOD_".$loop."><![CDATA[$iolinkManufacturerOD]]></iolinkManufacturerOD_".$loop.">";
		$iolinkIolMasterDataXML .= "<iolinkLensBrandOD_".$loop."><![CDATA[$iolinkLensBrandOD]]></iolinkLensBrandOD_".$loop.">";
		$iolinkIolMasterDataXML .= "<iolinkModelOD_".$loop."><![CDATA[$iolinkModelOD]]></iolinkModelOD_".$loop.">";
		$iolinkIolMasterDataXML .= "<iolinkDiopterOD_".$loop."><![CDATA[$iolinkDiopterOD]]></iolinkDiopterOD_".$loop.">";
		
		$iolinkIolMasterDataXML .= "<iolinkManufacturerOS_".$loop."><![CDATA[$iolinkManufacturerOS]]></iolinkManufacturerOS_".$loop.">";
		$iolinkIolMasterDataXML .= "<iolinkLensBrandOS_".$loop."><![CDATA[$iolinkLensBrandOS]]></iolinkLensBrandOS_".$loop.">";
		$iolinkIolMasterDataXML .= "<iolinkModelOS_".$loop."><![CDATA[$iolinkModelOS]]></iolinkModelOS_".$loop.">";
		$iolinkIolMasterDataXML .= "<iolinkDiopterOS_".$loop."><![CDATA[$iolinkDiopterOS]]></iolinkDiopterOS_".$loop.">";
		
	}
	
	$iolinkIolMasterDataXML .= "<iolinkOpRoomDefaultOD><![CDATA[$opRoomDefaultOD]]></iolinkOpRoomDefaultOD>";
	$iolinkIolMasterDataXML .= "<iolinkOpRoomDefaultOS><![CDATA[$opRoomDefaultOS]]></iolinkOpRoomDefaultOS>";
	
	$iolinkIolMasterDataXML .= "</iolinkIolMasterDataChild>";
	$iolinkIolMasterDataXML .= "</iolinkIolMasterData>";
}elseif($sxNum>0 && $sxData==true)
{
	//get data from sx planing sheet
	$sxQry="select tpa.*, sx.sx_plan_dos, sx.mank_eye from chart_sx_plan_sheet as sx 
	RIGHT JOIN chart_sps_ast_plan_tpa as tpa ON sx.id=tpa.id_chart_sx_plan_sheet 
	WHERE sx.sx_plan_dos='".$sxRow['sx_plan_dos']."' AND sx.del_status = 0 AND sx.patient_id='".$patientId."'
	AND prov_id='".$sxRow['surgeon_id']."'
	ORDER BY tpa.indx asc LIMIT 0,4";
	$sxRes		= imw_query($sxQry);
	$sxNum 		= imw_num_rows($sxRes);
	
	
	
	$iolinkIolMasterDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
	$iolinkIolMasterDataXML .= "<iolinkIolMasterData>";
	$iolinkIolMasterDataXML .= "<iolinkIolMasterDataChild>";
	$iolinkIolMasterDataXML .= "<patient_id>".$patientId."</patient_id>";
	
	while($sxRow = imw_fetch_array($sxRes))
	{
		$loop=$sxRow['indx']+1;
		
		if($sxRow['mank_eye']=='OD'){
			
			$iolModelID_OD			=	$sxRow['lens_type'];
			$iolPower_OD			=	$sxRow['power'];
			$iolinkManufacturerOD	= 	getLensesData($iolModelID_OD,'lenses_manufacturer'); 
			$iolinkLensBrandOD		=	getLensesData($iolModelID_OD,'lenses_brand');
			$iolinkModelOD			=	getLensesData($iolModelID_OD,'lenses_iol_type');
			$iolinkDiopterOD		=	$iolPower_OD;
			
			if($sxRow['indx']=='0')
			$opRoomDefaultOD	=	getLensesData($sxRow['lens_type'],'lenses_iol_type');
		}


		if($sxRow['mank_eye']=='OS'){
			
			$iolModelID_OS			=	$sxRow['lens_type'];
			$iolPower_OS			=	$sxRow['power'];
			$iolinkManufacturerOS	=	getLensesData($iolModelID_OS,'lenses_manufacturer');
			$iolinkLensBrandOS		=	getLensesData($iolModelID_OS,'lenses_brand');
			$iolinkModelOS			=	getLensesData($iolModelID_OS,'lenses_iol_type');
			$iolinkDiopterOS		=	$iolPower_OS;
			
			if($sxRow['indx']=='0')
			$opRoomDefaultOS	=	getLensesData($sxRow['lens_type'],'lenses_iol_type');	
		}
		
		$iolinkIolMasterDataXML .= "<iolinkManufacturerOD_".$loop."><![CDATA[$iolinkManufacturerOD]]></iolinkManufacturerOD_".$loop.">";
		$iolinkIolMasterDataXML .= "<iolinkLensBrandOD_".$loop."><![CDATA[$iolinkLensBrandOD]]></iolinkLensBrandOD_".$loop.">";
		$iolinkIolMasterDataXML .= "<iolinkModelOD_".$loop."><![CDATA[$iolinkModelOD]]></iolinkModelOD_".$loop.">";
		$iolinkIolMasterDataXML .= "<iolinkDiopterOD_".$loop."><![CDATA[$iolinkDiopterOD]]></iolinkDiopterOD_".$loop.">";
		
		$iolinkIolMasterDataXML .= "<iolinkManufacturerOS_".$loop."><![CDATA[$iolinkManufacturerOS]]></iolinkManufacturerOS_".$loop.">";
		$iolinkIolMasterDataXML .= "<iolinkLensBrandOS_".$loop."><![CDATA[$iolinkLensBrandOS]]></iolinkLensBrandOS_".$loop.">";
		$iolinkIolMasterDataXML .= "<iolinkModelOS_".$loop."><![CDATA[$iolinkModelOS]]></iolinkModelOS_".$loop.">";
		$iolinkIolMasterDataXML .= "<iolinkDiopterOS_".$loop."><![CDATA[$iolinkDiopterOS]]></iolinkDiopterOS_".$loop.">";
		
	}
	
	$iolinkIolMasterDataXML .= "<iolinkOpRoomDefaultOD><![CDATA[$opRoomDefaultOD]]></iolinkOpRoomDefaultOD>";
	$iolinkIolMasterDataXML .= "<iolinkOpRoomDefaultOS><![CDATA[$opRoomDefaultOS]]></iolinkOpRoomDefaultOS>";
	
	$iolinkIolMasterDataXML .= "</iolinkIolMasterDataChild>";
	$iolinkIolMasterDataXML .= "</iolinkIolMasterData>";
}
/////////////////////////			
//getting General Health PDF Data------satrt
$genHealthDocData = array();
$genHealthDocDataXML = "";
$getPathGenHealth = $httpfolder.'/PatientId_'.$patientId.'/General_Health.pdf';
clearstatcache();
if (file_exists($getPathGenHealth)) {	
	$aa = "?>";
	$genHealthDocDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
	$genHealthDocDataXML .= "<genHealthDocData>";
	$genHealthPdfData = "";
	$genHealthContents = "";
	$genHealthContents = file_get_contents($getPathGenHealth);	
	$genHealthPdfData = addslashes(base64_encode($genHealthContents));
	$genHealthDocDataXML .= "<genHealthDocDataChild>";
	$genHealthDocDataXML .= "<file_name>GenHealth</file_name >";
	$genHealthDocDataXML .= "<patient_id>".$patientId."</patient_id >";
	$genHealthDocDataXML .= "<gen_health_doc_data><![CDATA[$genHealthPdfData]]></gen_health_doc_data>";
	$genHealthDocDataXML .= "</genHealthDocDataChild>";	
	$genHealthDocDataXML .= "</genHealthDocData>";
}
//getting General Health PDF Data------end										
/////////////////////////					
//echo $genHealthDocDataXML;  
//exit;
	
/////////////////////////			
//getting SX Planning Sheet PDF Data------satrt
$sxDocDataXML = "";
$SxPlanSheet = $httpfolder.'/PatientId_'.$patientId.'/Sx_Plan_Sheet.pdf';
clearstatcache();
if (file_exists($SxPlanSheet)) {	
	$aa = "?>";
	$sxDocDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
	$sxDocDataXML .= "<sxDocData>";
	$sxPdfData = "";
	$sxContents = "";
	$sxContents = file_get_contents($SxPlanSheet);	
	$sxPdfData = addslashes(base64_encode($sxContents));
	$sxDocDataXML .= "<sxDocDataChild>";
	$sxDocDataXML .= "<file_name>Sx_Planing_Sheet</file_name >";
	$sxDocDataXML .= "<patient_id>".$patientId."</patient_id >";
	$sxDocDataXML .= "<sx_doc_data><![CDATA[$sxPdfData]]></sx_doc_data>";
	$sxDocDataXML .= "</sxDocDataChild>";	
	$sxDocDataXML .= "</sxDocData>";
}
//getting General Health PDF Data------end										
/////////////////////////					
//echo $sxDocDataXML;  
//exit;

/////////////////////////			
//getting H&P PDF Data------satrt
$historyPhysicalDocData = array();
$historyPhysicalDocDataXML = "";
/*$getPathHistoryPhysical = $httpfolder.'/PatientId_'.$patientId.'/History_Physical.pdf';
clearstatcache();
if (file_exists($getPathHistoryPhysical)) {	
	$aa = "?>";
	$historyPhysicalDocDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
	$historyPhysicalDocDataXML .= "<historyPhysicalDocData>";
	$historyPhysicalPdfData = "";
	$historyPhysicalContents = "";
	$historyPhysicalContents = file_get_contents($getPathHistoryPhysical);	
	$historyPhysicalPdfData = addslashes(base64_encode($historyPhysicalContents));
	$historyPhysicalDocDataXML .= "<historyPhysicalDocDataChild>";
	$historyPhysicalDocDataXML .= "<file_name>HistoryPhysical</file_name >";
	$historyPhysicalDocDataXML .= "<patient_id>".$patientId."</patient_id >";
	$historyPhysicalDocDataXML .= "<history_physical_doc_data><![CDATA[$historyPhysicalPdfData]]></history_physical_doc_data>";
	$historyPhysicalDocDataXML .= "</historyPhysicalDocDataChild>";	
	$historyPhysicalDocDataXML .= "</historyPhysicalDocData>";
}*/
//getting H&P PDF Data------end										
/////////////////////////					
//echo $historyPhysicalDocDataXML;  
//exit;

/////////////////////////History & Physical 
	
$facArr = get_facility_details();
$enable_hp = $facArr['enable_hp'];
if( $enable_hp) {
		
$strQryGetHP="SELECT schp.* FROM schedule_appointments sa INNER JOIN surgerycenter_pt_history_physical schp ON schp.patient_id = sa.sa_patient_id WHERE sa.id = ".$schedule_id;
$rsQryGetHP = imw_query($strQryGetHP);	
	
$patientHPDataXML = "";
if(!$rsQryGetHP){
	echo ("Error : ". imw_error()."<br>".$strQryGetHP);
}
else{
	if(imw_num_rows($rsQryGetHP)>0){		
		$aa = "?>";
		$patientHPDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
		$patientHPDataXML .= "<patientHPData>";
		while ($row = imw_fetch_assoc($rsQryGetHP)) {
			
			$otherHistoryPhysical = $row['otherHistoryPhysical'];
			$cadMIDesc = $row['cadMIDesc'];
			$cvaTIADesc = $row['cvaTIADesc'];
			$htnCPDesc = $row['htnCPDesc'];
			$anticoagulationTherapyDesc = $row['anticoagulationTherapyDesc'];
			$respiratoryAsthmaDesc = $row['respiratoryAsthmaDesc'];
			$diabetesDesc = $row['diabetesDesc'];
			$arthritisDesc = $row['arthritisDesc'];
			$recreationalDrugDesc = $row['recreationalDrugDesc'];
			$giGerdDesc = $row['giGerdDesc'];
			$ocularDesc = $row['ocularDesc'];
			$kidneyDiseaseDesc = $row['kidneyDiseaseDesc'];
			$hivAutoimmuneDesc = $row['hivAutoimmuneDesc'];
			$historyCancerDesc = $row['historyCancerDesc'];
			$organTransplantDesc = $row['organTransplantDesc'];
			$badReactionDesc = $row['badReactionDesc'];
			$heartExamDesc = $row['heartExamDesc'];
			$lungExamDesc = $row['lungExamDesc'];
			$highCholesterolDesc = $row['highCholesterolDesc'];
			$thyroidDesc = $row['thyroidDesc'];
			$ulcerDesc = $row['ulcerDesc'];
			
			$patientHPDataXML .= "<patientHPDataChild>";
			$patientHPDataXML .= "<patient_id>".$row['patient_id']."</patient_id>";
			$patientHPDataXML .= "<cadMI>".$row['cadMI']."</cadMI>";
			$patientHPDataXML .= "<cadMIDesc><![CDATA[$cadMIDesc]]></cadMIDesc>";
			$patientHPDataXML .= "<cvaTIA>".$row['cvaTIA']."</cvaTIA>";
			$patientHPDataXML .= "<cvaTIADesc><![CDATA[$cvaTIADesc]]></cvaTIADesc>";
			$patientHPDataXML .= "<htnCP>".$row['htnCP']."</htnCP>";
			$patientHPDataXML .= "<htnCPDesc><![CDATA[$htnCPDesc]]></htnCPDesc>";
			$patientHPDataXML .= "<anticoagulationTherapy>".$row['anticoagulationTherapy']."</anticoagulationTherapy>";
			$patientHPDataXML .= "<anticoagulationTherapyDesc><![CDATA[$anticoagulationTherapyDesc]]></anticoagulationTherapyDesc>";
			$patientHPDataXML .= "<respiratoryAsthma>".$row['respiratoryAsthma']."</respiratoryAsthma>";
			$patientHPDataXML .= "<respiratoryAsthmaDesc><![CDATA[$respiratoryAsthmaDesc]]></respiratoryAsthmaDesc>";
			$patientHPDataXML .= "<diabetes>".$row['diabetes']."</diabetes>";
			$patientHPDataXML .= "<diabetesDesc><![CDATA[$diabetesDesc]]></diabetesDesc>";
			$patientHPDataXML .= "<arthritis>".$row['arthritis']."</arthritis>";
			$patientHPDataXML .= "<arthritisDesc><![CDATA[$arthritisDesc]]></arthritisDesc>";
			$patientHPDataXML .= "<recreationalDrug>".$row['recreationalDrug']."</recreationalDrug>";
			$patientHPDataXML .= "<recreationalDrugDesc><![CDATA[$recreationalDrugDesc]]></recreationalDrugDesc>";
			$patientHPDataXML .= "<giGerd>".$row['giGerd']."</giGerd>";
			$patientHPDataXML .= "<giGerdDesc><![CDATA[$giGerdDesc]]></giGerdDesc>";
			$patientHPDataXML .= "<ocular>".$row['ocular']."</ocular >";
			$patientHPDataXML .= "<ocularDesc><![CDATA[$ocularDesc]]></ocularDesc>";
			$patientHPDataXML .= "<kidneyDisease>".$row['kidneyDisease']."</kidneyDisease>";
			$patientHPDataXML .= "<kidneyDiseaseDesc><![CDATA[$kidneyDiseaseDesc]]></kidneyDiseaseDesc>";
			$patientHPDataXML .= "<hivAutoimmune>".$row['hivAutoimmune']."</hivAutoimmune>";
			$patientHPDataXML .= "<hivAutoimmuneDesc><![CDATA[$hivAutoimmuneDesc]]></hivAutoimmuneDesc>";
			$patientHPDataXML .= "<historyCancer>".$row['historyCancer']."</historyCancer>";
			$patientHPDataXML .= "<historyCancerDesc><![CDATA[$historyCancerDesc]]></historyCancerDesc>";
			$patientHPDataXML .= "<organTransplant>".$row['organTransplant']."</organTransplant>";
			$patientHPDataXML .= "<organTransplantDesc><![CDATA[$organTransplantDesc]]></organTransplantDesc>";
			$patientHPDataXML .= "<badReaction>".$row['badReaction']."</badReaction>";
			$patientHPDataXML .= "<badReactionDesc><![CDATA[$badReactionDesc]]></badReactionDesc>";
			$patientHPDataXML .= "<otherHistoryPhysical><![CDATA[$otherHistoryPhysical]]></otherHistoryPhysical>";
			$patientHPDataXML .= "<heartExam>".$row['heartExam']."</heartExam>";
			$patientHPDataXML .= "<heartExamDesc><![CDATA[$heartExamDesc]]></heartExamDesc>";
			$patientHPDataXML .= "<lungExam>".$row['lungExam']."</lungExam>";
			$patientHPDataXML .= "<lungExamDesc><![CDATA[$lungExamDesc]]></lungExamDesc>";
			$patientHPDataXML .= "<highCholesterol>".$row['highCholesterol']."</highCholesterol>";
			$patientHPDataXML .= "<highCholesterolDesc><![CDATA[$highCholesterolDesc]]></highCholesterolDesc>";
			$patientHPDataXML .= "<thyroid>".$row['thyroid']."</thyroid>";
			$patientHPDataXML .= "<thyroidDesc><![CDATA[$thyroidDesc]]></thyroidDesc>";
			$patientHPDataXML .= "<ulcer>".$row['ulcer']."</ulcer>";
			$patientHPDataXML .= "<ulcerDesc><![CDATA[$ulcerDesc]]></ulcerDesc>";
			$patientHPDataXML .= "<discussedAdvancedDirective>".$HPAdvanceDirective."</discussedAdvancedDirective>";
			$patientHPDataXML .= "<createDateTime>".$row['create_date_time']."</createDateTime>";
			$patientHPDataXML .= "<saveDateTime>".$row['save_date_time']."</saveDateTime>";
			$patientHPDataXML .= "</patientHPDataChild>";
			
			// Start to get and send Pre Define Question of H&P
			$strQryGetPatientHPQues = "SELECT shpq.surgerycenter_id as sid, shpq.name, sphpq.ques_status, sphpq.ques_desc FROM surgerycenter_history_physical_ques shpq LEFT JOIN surgerycenter_pt_history_physical_ques sphpq on (shpq.id = sphpq.ques_id) Where shpq.deleted = '0' and sphpq.patient_id = '".$row['patient_id']."' ";
			$rsQryGetPatientHPQues = imw_query($strQryGetPatientHPQues);	
			if($rsQryGetPatientHPQues){				
				$patientHPQues = array();			
					if(imw_num_rows($rsQryGetPatientHPQues)>0){		
						$aa = "?>";
						$patientHPQuesDataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
						$patientHPQuesDataXML .= "<patientHPQuesData>";
						while ($rowHPQues = imw_fetch_array($rsQryGetPatientHPQues)) {	
							$ques_name = $rowHPQues['name'];
							$ques_desc = $rowHPQues['ques_desc'];
							$patientHPQuesDataXML .= "<patientHPQuesDataChild>";
							$patientHPQuesDataXML .= "<HPQuestion><![CDATA[$ques_name]]></HPQuestion>";
							$patientHPQuesDataXML .= "<HPQuestionSid>".$rowHPQues['sid']."</HPQuestionSid>";
							$patientHPQuesDataXML .= "<HPQuestionStatus>".$rowHPQues['ques_status']."</HPQuestionStatus>";											
							$patientHPQuesDataXML .= "<HPQuestionDesc><![CDATA[$ques_desc]]></HPQuestionDesc>";
							$patientHPQuesDataXML .= "</patientHPQuesDataChild>";
						}
						$patientHPQuesDataXML .= "</patientHPQuesData>";
					}			
			}
		}
		$patientHPDataXML .= "</patientHPData>";
	}
}
}
	
echo $iolinkMainData = $demographicDataXML."~~~~~~~".$insurenceDataXML."~~~~~~~".$consentDataXML."~~~~~~~".$consentSigDataXML."~~~~~~~".$patientInWatingDataXML."~~~~~~~".$scanDocDataXML."~~~~~~~".$pdfDocDataXML."~~~~~~~".$faceSheetDocDataXML."~~~~~~~".$patientPreOpDataXML."~~~~~~~".$patientAdminHealthDataXML."~~~~~~~".$patientHealthQuesAllergyDataXML."~~~~~~~".$patientHealthQuesMedDataXML."~~~~~~~".$pracPatientAlleryDataXML."~~~~~~~".$pracPatientMedDataXML."~~~~~~~".$oculerHxDocDataXML."~~~~~~~".$insurenceCaseXML."~~~~~~~".$InsurenceScanDocXML."~~~~~~~".$sxAlertDataXML."~~~~~~~".$aScanDocDataXML."~~~~~~~".$iolMasterDocDataXML."~~~~~~~".$iolinkIolMasterDataXML."~~~~~~~".$genHealthDocDataXML."~~~~~~~".$patientAllergyNoValueDataXML."~~~~~~~".$sxDocDataXML."~~~~~~~".$historyPhysicalDocDataXML."~~~~~~~".$patientHPDataXML."~~~~~~~".$patientHPQuesDataXML;
}
?>