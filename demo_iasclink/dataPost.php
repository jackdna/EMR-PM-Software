<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
set_time_limit(180);
include_once("common/conDb.php");
include_once("common/commonFunctions.php");
include_once("admin/classObjectFunction.php");

//START FUNCTION FOR INS-SCAN
function putInsImgContent($scan_card,$insurenceDataScanContent) {
	$scanCardPath='';
	if($scan_card) {
		$scan_cardExplode = explode('/',$scan_card);
		$scan_cardPatientDir = $scan_cardExplode[1];
		$httpfolder =  realpath(dirname(__FILE__));
		$iolinkInsScanFolder = $httpfolder."/imedic_uploaddir/PatientId_".$demoPatientInstId;
		if(!is_dir($iolinkInsScanFolder)){		
			mkdir($iolinkInsScanFolder);
		}
		$scanCardPath = "/PatientId_".$demoPatientInstId.'/'.$scan_cardExplode[2];
		$inScanPutPdfFileName = $iolinkInsScanFolder.'/'.$scan_cardExplode[2];
		file_put_contents($inScanPutPdfFileName,$insurenceDataScanContent);
	}
	return $scanCardPath;
}	
//END FUNCTION FOR INS-SCAN
extract($_REQUEST);
if($downloadForm==""){
	$userName = $_REQUEST['userName'];
	$password = $_REQUEST['password'];
	
	//Encrypted Password	
	$imw_passQry = imw_query("select PASSWORD('$password')");
	$imw_passRow = imw_fetch_array($imw_passQry);
	$password = $imw_passRow['0'];
	//Encrypted Password
	
	// 
	//$maxRecentlyUsedPass = getData("maxRecentlyUsedPass", "surgerycenter", "surgeryCenterId", '1');
	//$maxLoginAttempts = getData("maxLoginAttempts", "surgerycenter", "surgeryCenterId", '1');
	//
	
	$getLoginDetailsStr = "SELECT * FROM users WHERE loginName = '$userName' AND user_password = '$password' AND deleteStatus <> 'Yes'";
	$getLoginDetailsQry = imw_query($getLoginDetailsStr);
	$getLoginRowCount = imw_num_rows($getLoginDetailsQry);
	if($getLoginRowCount>0){				
		echo 1;
	}else{		
		echo 0;
	}
}
else if($downloadForm=="yes"){
	$userName = $_REQUEST['userName'];
	$password = $_REQUEST['password'];
	$wishToDownLoad = $_REQUEST['wishToDownLoad'];
	//$wishToDownLoad(1 == Consent Data, 2 == Health Questionnaire)
	//Encrypted Password	
	$imw_passQry = imw_query("select PASSWORD('$password')");
	$imw_passRow = imw_fetch_array($imw_passQry);
	$password = $imw_passRow['0'];
	//Encrypted Password
	$getLoginDetailsStr = "SELECT * FROM users WHERE loginName = '$userName' AND user_password = '$password' AND deleteStatus <> 'Yes'";
	$getLoginDetailsQry = imw_query($getLoginDetailsStr);
	$getLoginRowCount = imw_num_rows($getLoginDetailsQry);
	if($getLoginRowCount>0){	
		if($wishToDownLoad == 1){			
			//export start
			$querygGetAllConsentCotegory = "SELECT * FROM consent_category";
			$rsQuerygGetAllConsentCotegory = imw_query($querygGetAllConsentCotegory);
			if(!$rsQuerygGetAllConsentCotegory){
				echo ("Error : ". imw_error()."<br>".$querygGetAllConsentCotegory);
			}
			else{
				$consentCategory = array();
				$a = 0;
			
				if(imw_num_rows($rsQuerygGetAllConsentCotegory)>0){		
				$aa = "?>";
					$myXml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
					$myXml .= "<consentforms>";
					while ($row = imw_fetch_array($rsQuerygGetAllConsentCotegory)) {
						//echo '<pre>';
						//print_r($row); 
						$myXml .= "<consentform>";						
						$myXml .= "<categoryid>".$row['category_id']."</categoryid>";						
						$myXml .= "<categoryname>".$row['category_name']."</categoryname>";						
						$myXml .= "<categorystatus>".$row['category_status']."</categorystatus>";
						$myXml .= "</consentform>";
						$a++;
						//echo $row[category_id].' '.$row[category_id].' '.$row[category_status].'<br>';
					}
					$myXml .= "</consentforms>";
				}
			}			
			echo $myXml;
			
			echo '~~~~~~~';
	
			$querygGetAllConsentForm = "SELECT * FROM consent_forms_template";
			$rsQuerygGetAllConsentForm = imw_query($querygGetAllConsentForm);
			if(!$rsQuerygGetAllConsentForm){
				echo ("Error : ". imw_error()."<br>".$querygGetAllConsentForm);
			}
			else{
				$consentTemplate = array();
				$a = 0;
			
				if(imw_num_rows($rsQuerygGetAllConsentForm)>0){
					$aa = "?>";
					$myXmlNew = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
					$myXmlNew .= "<consentformstemplate>";
					while ($row = imw_fetch_array($rsQuerygGetAllConsentForm)) {					
						$myXmlNew .= "<consentformstemplatechild>";
						//$consentTemplate[$a][0] = $row[consent_id];
						$myXmlNew .= "<consent_id>".$row['consent_id']."</consent_id>";
						//$consentTemplate[$a][1] = $row[category_name];
						$consentNameData = addslashes($row['consent_name']);
						$myXmlNew .= "<consent_name><![CDATA[$consentNameData]]></consent_name>";
						//$consentTemplate[$a][2] = $row[consent_alias];
						$consentAliasData = addslashes($row['consent_alias']);
						$myXmlNew .= "<consent_alias><![CDATA[$consentAliasData]]></consent_alias>";
						//$consentTemplate[$a][3] = $row[consent_category_id];
						$myXmlNew .= "<consent_category_id>".$row['consent_category_id']."</consent_category_id>";
						//$consentTemplate[$a][4] = $row[consent_data];
						$consentMainData = addslashes($row['consent_data']);
						$myXmlNew .= "<consent_data><![CDATA[$consentMainData]]></consent_data>";
						//$consentTemplate[$a][5] = $row[consent_delete_status];
						$myXmlNew .= "<consent_delete_status>".$row['consent_delete_status']."</consent_delete_status>";
						$myXmlNew .= "</consentformstemplatechild>";
						$a++;
						//echo $row[category_id].' '.$row[category_id].' '.$row[category_status].'<br>';
					}
					$myXmlNew .= "</consentformstemplate>";
				}	
			}
			echo $myXmlNew;
		}
		elseif($wishToDownLoad == 2){						
			//export start
			$healthQuestionerXml = "";
			$querygGetHealthQuestioner = "SELECT * FROM healthquestioner";
			$rsQuerygGetHealthQuestioner = imw_query($querygGetHealthQuestioner);
			if(!$rsQuerygGetHealthQuestioner){
				echo ("Error : ". imw_error()."<br>".$querygGetHealthQuestioner);
			}
			else{
				$healthQuestioner = array();
				$a = 0;			
				if(imw_num_rows($rsQuerygGetHealthQuestioner)>0){		
					$aa = "?>";
					$healthQuestionerXml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
					$healthQuestionerXml .= "<healthquestioner>";
					while ($row = imw_fetch_array($rsQuerygGetHealthQuestioner)) {
						//echo '<pre>';
						//print_r($row); 
						$healthQuestionerXml .= "<healthquestionerchild>";						
						$healthQuestionerXml .= "<healthQuestioner>".$row['healthQuestioner']."</healthQuestioner>";						
						$question = addslashes($row['question']);
						$healthQuestionerXml .= "<adminquestion><![CDATA[$question]]></adminquestion>";					
						$healthQuestionerXml .= "</healthquestionerchild>";
						$a++;						
					}
					$healthQuestionerXml .= "</healthquestioner>";
				}
			}			
			echo $healthQuestionerXml;	
		}	 
	}else{		
		//echo 0;
	}
}
else if($iolinkSync=="yes"){
	//echo 'ravi';
	//echo urldecode($iolinkMainData);
	//echo $iolinkMainData;
	//$iolinkMain = explode('~~~~~~~',$iolinkMainData);
	//echo $iolinkMain[0];
	//$rowGetIOlinkSettings = imw_fetch_array($rsGetIOlinkSettings);			
	//extract($rowGetIOlinkSettings);
	//echo $mode;
	//exit;
	$userName = $_REQUEST['userName'];
	$password = $_REQUEST['password'];	
	//Encrypted Password	
	$imw_passQry = imw_query("select PASSWORD('$password')");
	$imw_passRow = imw_fetch_array($imw_passQry);
	$password = $imw_passRow['0'];
	//Encrypted Password
	$getLoginDetailsStr = "SELECT * FROM users WHERE loginName = '$userName' AND user_password = '$password' AND deleteStatus <> 'Yes'";
	$getLoginDetailsQry = imw_query($getLoginDetailsStr);
	$getLoginRowCount = imw_num_rows($getLoginDetailsQry);
	if($getLoginRowCount>0){				
	/////////////
		if($mode == "send" || $mode == "resync" || $mode == "ioBookSheet"){		
			$cur = curl_init();
			//$url = $iolinkUrl."?userName=$iolinkUrlUsername&password=$iolinkUrlPassword&downloadForm='NO'&iolinkSync=yes&iolinkMainData='$iolinkMainData'";
			//$url = "http://192.168.0.35/R3/getIolinkXMLData.php?login=$login&pass=$pass&dbase=$dbase&schedule_id=$schedule_id&sa_date=$sa_date";
			$url = $myAddress."/addons/iOLink/getIolinkXMLData.php?login=$login&pass=$pass&dbase=$dbase&mode=$mode&schedule_id=$schedule_id&sa_date=$sa_date";
			curl_setopt($cur,CURLOPT_URL,$url);
			curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
			curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
			$data = curl_exec($cur);
			curl_close($cur);	
			$iolinkMainData = array();
			$iolinkMainData = explode('~~~~~~~',$data);
			//echo $iolinkMainData[14];
			//die;
			//Saving Demographics data
			$demographicDataSaveFlag = false;
			$patientInWatingDataSaveFlag = false;
			$insurenceDataSaveFlag = false;
			$insurenceCaseSaveFlag = false;
			$InsurenceScanDocSaveFlag=false;
			$consentDataSaveFlag = false;
			$consentSigDataSaveFlag = false;		
			$scanDataSaveFlag = false;
			$pdfDataSaveFlag = false;
			$faceSheetDataSaveFlag = false;			
			$patientPreOpHealthQuesDataSaveFlag = false;
			$patientAdminHealthQuesDataSaveFlag = false;
			$patientHealthQuesAllergyDataSaveFlag = false;
			$patientHealthQuesMedDataSaveFlag = false;
			$oculerHxPdfDataSaveFlag = false;
			$demographicDataXML = $iolinkMainData[0];
			
			$tag = 'demographicDataChild';
			$demographicdata = array();
			$demographicdata = XMLtoArray($demographicDataXML,$tag);
			//print_r($demographicdata); exit;
			$a = 0;
			$b = 0;
			while ($a < count($demographicdata)){		
				$title 					= $demographicdata[$a]['title'];
				$fname		 			= $demographicdata[$a]['fname'];
				$lname			 		= $demographicdata[$a]['lname'];	
				$mname 					= $demographicdata[$a]['mname'];
				$suffix 				= $demographicdata[$a]['suffix'];
				$DOB		 			= $demographicdata[$a]['DOB'];
				$street			 		= $demographicdata[$a]['street'];	
				$street2 				= $demographicdata[$a]['street2'];
				$postal_code			= $demographicdata[$a]['postal_code'];
				$city			 		= $demographicdata[$a]['city'];	
				$state 					= $demographicdata[$a]['state'];
				$postal_code 			= $demographicdata[$a]['postal_code'];
				$ss		 				= $demographicdata[$a]['ss'];
				$occupation				= $demographicdata[$a]['occupation'];
				$phone_home				= $demographicdata[$a]['phone_home'];	
				$phone_biz 				= $demographicdata[$a]['phone_biz'];
				$phone_cell		 		= $demographicdata[$a]['phone_cell'];
				$contact_relationship	= $demographicdata[$a]['contact_relationship'];	
				$status			 		= $demographicdata[$a]['status'];	
				$date 					= $demographicdata[$a]['date'];
				$sex		 			= $demographicdata[$a]['sex'];
				$providerID				= $demographicdata[$a]['providerID'];
				$email					= $demographicdata[$a]['email'];	
				$ethnoracial			= $demographicdata[$a]['ethnoracial'];	
				$interpretter			= $demographicdata[$a]['interpretter'];	
				$monthly_income			= $demographicdata[$a]['monthly_income'];	
				$financial_review 		= $demographicdata[$a]['financial_review'];
				$pid		 			= $demographicdata[$a]['pid'];
				$genericval1			= $demographicdata[$a]['genericval1'];
				$genericval2			= $demographicdata[$a]['genericval2'];	
				$hipaa_mail				= $demographicdata[$a]['hipaa_mail'];	
				$hipaa_voice 			= $demographicdata[$a]['hipaa_voice'];
				$username		 		= $demographicdata[$a]['username'];
				$password		 		= $demographicdata[$a]['password'];
				$p_imagename			= $demographicdata[$a]['p_imagename'];
				$licence_photo			= $demographicdata[$a]['licence_photo'];	
				$financial_date 		= $demographicdata[$a]['financial_date'];
				$default_facility		= $demographicdata[$a]['default_facility'];
				$created_by				= $demographicdata[$a]['created_by'];	
				$patient_notes			= $demographicdata[$a]['patient_notes'];	
				$patientStatus 			= $demographicdata[$a]['patientStatus'];
				$primary_care_id		= $demographicdata[$a]['primary_care_id'];
				$noBalanceBill			= $demographicdata[$a]['noBalanceBill'];
				$EMR		 			= $demographicdata[$a]['EMR'];
				$erx_entry		 		= $demographicdata[$a]['erx_entry'];
				$erx_patient_id			= $demographicdata[$a]['erx_patient_id'];	
				if($sex=="Male"){
					$sex="m";
				}
				elseif($sex=="Female"){
					$sex="f";
				}
				$selctQuery = "select * from patient_data_tbl where 
								patient_fname 	= '".addslashes($fname)."'
								and patient_lname 	= '".addslashes($lname)."'
								and date_of_birth 	= '$DOB'
								and zip 			= '$postal_code'";

				$selctRes = imw_query($selctQuery);			
				$selctNumRow = imw_num_rows($selctRes);
				$whereQueryDemograpic1 = "";
				if($selctNumRow>0){
					$selctRow = imw_fetch_array($selctRes);
					$demoPatientInstId = $selctRow['patient_id'];
					//$b = count($demographicdata);
					//$a = count($demographicdata);
					$insertQueryDemograpic1 = "update patient_data_tbl set "; 
					$whereQueryDemograpic1 = "where patient_id =".$demoPatientInstId;


				}else {
					$insertQueryDemograpic1 = "insert into patient_data_tbl set ";
				}
					$insertQueryDemograpic1 .= "title 			= '".addslashes($title)."',
												patient_fname 	= '".addslashes($fname)."',
												patient_mname 	= '".addslashes($mname)."',
												patient_lname 	= '".addslashes($lname)."',
												patient_suffix 	= '".addslashes($suffix)."',
												street1 		= '".addslashes($street)."',
												street2 		= '".addslashes($street2)."',
												city 			= '".addslashes($city)."',
												state 			= '".addslashes($state)."',
												zip 			= '$postal_code',
												date_of_birth 	= '$DOB',
												sex 			= '$sex',
												homePhone 		= '$phone_home',
												workPhone 		= '$phone_biz'
											";
					$insertQueryDemograpic1 .= $whereQueryDemograpic1;		
								
					$insertQueryDemograpicRsId1 = imw_query($insertQueryDemograpic1);
					if(!$demoPatientInstId){
						$demoPatientInstId = imw_insert_id();
					}
				$a++;
				$b++;
			}
			if ($b == count($demographicdata)){
				$demographicDataSaveFlag = true;
			}
			else{
				break;
			}
			if($demoPatientInstId){
			//Saving Demographics(patientInWating) data
			$patientInWatingDataXML = $iolinkMainData[4];
			$tag = 'patientInWatingChild';
			$patientInWatingData = array();
			$patientInWatingData = XMLtoArray($patientInWatingDataXML,$tag);
			//print_r($patientInWatingData);die;
			$a = 0;
			$b = 0;
			while ($a < count($patientInWatingData)) {		
				$dos 						= $patientInWatingData[$a]['dos'];
				$surgeon_fname 				= $patientInWatingData[$a]['surgeon_fname'];
				$surgeon_mname	 			= $patientInWatingData[$a]['surgeon_mname'];	
				$surgeon_lname				= $patientInWatingData[$a]['surgeon_lname'];
				$surgery_time				= $patientInWatingData[$a]['surgery_time'];
				$pickup_time		 		= $patientInWatingData[$a]['pickup_time'];
				$arrival_time				= $patientInWatingData[$a]['arrival_time'];	
				$patient_primary_procedure 	= $patientInWatingData[$a]['patient_primary_procedure'];
				$site						= $patientInWatingData[$a]['site'];
				$comment			 		= $patientInWatingData[$a]['comment'];	
				$patient_status				= $patientInWatingData[$a]['patient_status'];	
				$selctQuery = "select patient_in_waiting_id from patient_in_waiting_tbl where 
								dos = '".addslashes($dos)."' and 
								patient_id = '".$demoPatientInstId."'
								and patient_status != 'Canceled'
								";
				$selctRes = imw_query($selctQuery);			
				$selctNumRow = imw_num_rows($selctRes);
				if($selctNumRow>0){
					$selctRow = imw_fetch_array($selctRes);
					$savedPatientInWtId = $selctRow['patient_in_waiting_id'];
					$demoPatientInWtId = $selctRow['patient_in_waiting_id'];
					/*$patientInWatingDataSaveFlag = true;
					$insurenceDataSaveFlag = true;
					$consentDataSaveFlag = true;
					$consentSigDataSaveFlag = true;*/
					//$a = count($patientInWatingData);
					//$b = count($patientInWatingData);	
					$insertQueryDemograpic2 = "update patient_in_waiting_tbl set ";
					$whereQueryDemograpic2 = "where patient_in_waiting_id =".$savedPatientInWtId;
				}
				else{
					$insertQueryDemograpic2 = "insert into patient_in_waiting_tbl set ";				
				}
				$insertQueryDemograpic2 .=" dos 						= '".addslashes($dos)."',
											surgeon_fname 				= '".addslashes($surgeon_fname)."',
											surgeon_mname 				= '".addslashes($surgeon_mname)."',
											surgeon_lname 				= '".addslashes($surgeon_lname)."',
											surgery_time 				= '".addslashes($surgery_time)."',
											pickup_time 				= '".addslashes($pickup_time)."',
											arrival_time 				= '".addslashes($arrival_time)."',
											patient_primary_procedure	= '".addslashes($patient_primary_procedure)."',
											site 						= '".addslashes($site)."',
											comment 					= '".addslashes($comment)."',
											patient_status 				= '".addslashes($patient_status)."',
											patient_id 					= '".$demoPatientInstId."',
											drOfficePatientId 			= '".$pid."'
										";
				$surgeonName = addslashes($surgeon_fname).'_'.addslashes($surgeon_lname);						
				$insertQueryDemograpic2 .=	$whereQueryDemograpic2;					
				$insertQueryDemograpicRsId2 = imw_query($insertQueryDemograpic2);
				if(!$savedPatientInWtId){
					$demoPatientInWtId = imw_insert_id();
				}
				$a++;
				$b++;
			}
			if ($b == count($patientInWatingData)){
				$patientInWatingDataSaveFlag = true;
			}
			else{
				break;
			}
			/////////
			if($demoPatientInWtId > 0){						
				//START SAVING INS CASES
				$insurenceCaseXML = $iolinkMainData[15];
				$tag = 'insurenceCaseChild';
				$insurenceCase = array();
				$insurenceCase = XMLtoArray($insurenceCaseXML,$tag);
				//print_r($insurenceCase);
				$a = 0;
				$b = 0;
				$arrInsCaseId=array();
				while ($a < count($insurenceCase)) {		
				$insId = "";
					$insCaseId	 			= $insurenceCase[$a]['ins_caseid'];
					$ins_case_name		 	= $insurenceCase[$a]['ins_case_name'];
					$ins_case_type			= $insurenceCase[$a]['ins_case_type'];	
					$start_date		 		= $insurenceCase[$a]['start_date'];
					$end_date				= $insurenceCase[$a]['end_date'];	
					$case_id 				= $insurenceCase[$a]['case_id'];
					$case_status			= $insurenceCase[$a]['case_status'];
					$insCaseAthenaID		= $insurenceCase[$a]['athenaID'];	
					$case_name 				= $insurenceCase[$a]['case_name'];
					$vision		 			= $insurenceCase[$a]['vision'];
					$normal					= $insurenceCase[$a]['normal'];
					
					$insId='';
					$whereQueryInsurenceCase = "";
					$selectCaseQry = "select * from iolink_insurance_case where patient_id = '".$demoPatientInstId."' AND athenaID='".$insCaseId."'";
					//exit;
					$rsSelectCaseQry = imw_query($selectCaseQry);
					$numRowSelectCaseQry = imw_num_rows($rsSelectCaseQry);
					//echo $numRowSelectCaseQry.'qwqwqw';
					if($numRowSelectCaseQry==0){					
						$insertQueryInsurenceCase = "insert into iolink_insurance_case set ";					
					}
					else{					
						$rowSelectCaseQry = imw_fetch_array($rsSelectCaseQry);
						$insId = $rowSelectCaseQry['ins_caseid'];
						
						$insertQueryInsurenceCase = "update iolink_insurance_case set ";
						$whereQueryInsurenceCase = "where patient_id = '".$demoPatientInstId."' AND athenaID='".$insCaseId."'";
					}
					$insertQueryInsurenceCase .= "	ins_case_name	= '".$ins_case_name."',
												ins_case_type 		= '".$ins_case_type."',												
												start_date 			= '".$start_date."',
												end_date 			= '".$end_date."',
												case_id 			= '".$case_id."',
												case_status 		= '".$case_status."',
												athenaID 			= '".$insCaseId."',
												case_name 			= '".$case_name."',	
												vision 				= '".$vision."',
												normal 				= '".$normal."',
												patient_id 			= '".$demoPatientInstId."',
												waiting_id 			= '".$demoPatientInWtId."'
											";
					$insertQueryInsurenceCase .= $whereQueryInsurenceCase;
					$insertQueryInsurenceCaseRsId = imw_query($insertQueryInsurenceCase);
					if(!$insId){
						$insId =imw_insert_id();						
					}	
					$arrInsCaseId[$a] = array("iolinkInsCaseId" => $insId,"imedicWareInsCaseId" => $insCaseId);					
					
					$a++;
					$b++;
				}

				if ($b == count($insurenceCase)){
					$insurenceCaseSaveFlag = true;
				}
				else{
					break;
				}
				//END SAVING INS CASES
				
				//Saving Insurence data
				$insurenceDataXML = $iolinkMainData[1];
				$tag = 'insurenceDataChild';
				$insurenceData = array();
				$insurenceData = XMLtoArray($insurenceDataXML,$tag);
				//print_r($insurenceData);
				$a = 0;
				$b = 0;
				
				while ($a < count($insurenceData)) {		
					
					$type 					= $insurenceData[$a]['type'];
					$provider		 		= $insurenceData[$a]['ins_provider'];
					$plan_name			 	= $insurenceData[$a]['plan_name'];	
					$policy_number 			= $insurenceData[$a]['policy'];
					$group_number		 	= $insurenceData[$a]['group_name'];
					$subscriber_lname		= $insurenceData[$a]['lname'];	
					$subscriber_mname 		= $insurenceData[$a]['mname'];
					$subscriber_fname		= $insurenceData[$a]['fname'];
					$subscriber_relationship= $insurenceData[$a]['sub_relation'];	
					$subscriber_ss 			= $insurenceData[$a]['ssn'];
					$subscriber_DOB		 	= $insurenceData[$a]['dob'];
					$subscriber_street		= $insurenceData[$a]['address1'];
					$subscriber_street_2	= $insurenceData[$a]['address2'];	
					$subscriber_postal_code	= $insurenceData[$a]['zip_code'];
					$subscriber_city 		= $insurenceData[$a]['city'];
					$subscriber_state		= $insurenceData[$a]['state'];	
					$subscriber_phone 		= $insurenceData[$a]['home_phone'];	
					$subscriber_biz_phone	= $insurenceData[$a]['work_phone'];
					$subscriber_mobile		= $insurenceData[$a]['mbl_phone'];
					$copay					= $insurenceData[$a]['copay'];
					$subscriber_sex			= $insurenceData[$a]['gender'];	
					$referal_required		= $insurenceData[$a]['refer_req'];	
					$effective_date			= $insurenceData[$a]['active_date'];
					$expiration_date		= $insurenceData[$a]['expiry_Date'];
					$ins_caseid				= $insurenceData[$a]['ins_caseid'];	
					
					$scan_card				= $insurenceData[$a]['scan_card'];
					$scan_label				= $insurenceData[$a]['scan_label'];
					$claims_adjustername	= $insurenceData[$a]['claims_adjustername'];
					$claims_adjusterphone	= $insurenceData[$a]['claims_adjusterphone'];
					$Sec_HCFA				= $insurenceData[$a]['Sec_HCFA'];
					$newComDate				= $insurenceData[$a]['newComDate'];
					$actInsComp				= $insurenceData[$a]['actInsComp'];
					$actInsCompDate			= $insurenceData[$a]['actInsCompDate'];
					$scan_card2				= $insurenceData[$a]['scan_card2'];
					$scan_label2			= $insurenceData[$a]['scan_label2'];
					$cardscan_date			= $insurenceData[$a]['cardscan_date'];
					$cardscan_comments		= $insurenceData[$a]['cardscan_comments'];
					$cardscan1_datetime		= $insurenceData[$a]['cardscan1_datetime'];
					$self_pay_provider		= $insurenceData[$a]['self_pay_provider'];
					
					//SET INSURANCE-SCAN INFO
					$insurenceDataScanCard1		= $insurenceData[$a]['insurenceDataScanCard1'];
					$insurenceDataScanCard2		= $insurenceData[$a]['insurenceDataScanCard2'];
					
					$insurenceDataScanCard1 = base64_decode($insurenceDataScanCard1);
					$insurenceDataScanCard2 = base64_decode($insurenceDataScanCard2);
					
					$scan_card = putInsImgContent($scan_card,$insurenceDataScanCard1); 
					$scan_card2 = putInsImgContent($scan_card2,$insurenceDataScanCard2); 
					//SET INSURANCE-SCAN INFO
						
					$iolinkInsCaseIdToInsert = "";
					foreach($arrInsCaseId as $key => $val) {
						foreach($val as $key1 => $val1) {
							if($val1 == $ins_caseid){
								//echo $val1.'=='.$insCaseId.'=>'.$val['iolinkInsCaseId'].'<br>';
								$iolinkInsCaseIdToInsert = $val['iolinkInsCaseId'];
							}
						}
					}
					$whereQueryInsurence = "";
					$selectQry = "select * from insurance_data where patient_id = $demoPatientInstId and waiting_id = $demoPatientInWtId and type = '$type' and ins_caseid = '$iolinkInsCaseIdToInsert'";
					$rsSelectQry = imw_query($selectQry);
					$numRowSelectQry = imw_num_rows($rsSelectQry);
					//echo $numRowSelectQry;
					if($numRowSelectQry==0){					
						$insertQueryInsurence = "insert into insurance_data set ";					
					}
					else{					
						$insertQueryInsurence = "update insurance_data set ";
						$whereQueryInsurence = "where patient_id = $demoPatientInstId and waiting_id = $demoPatientInWtId and type = '$type' and ins_caseid = '$iolinkInsCaseIdToInsert'";
					}
					if($provider){			
						$insertQueryInsurence .= "	type 				= '$type',
													ins_provider 		= '$provider',												
													policy 				= '$policy_number',
													group_name 			= '$group_number',
													plan_name 			= '$plan_name',
													copay 				= '$copay',
													refer_req 			= '$referal_required',
													active_date 		= '$effective_date',
													expiry_Date 		= '$expiration_date',	
													fname 				= '$subscriber_fname',
													mname 				= '$subscriber_mname',
													lname 				= '$subscriber_lname',
													sub_relation 		= '$subscriber_relationship',						
													ssn 				= '$subscriber_ss',
													dob 				= '$subscriber_DOB',
													gender 				= '$subscriber_sex',
													address1 			= '$subscriber_street',
													address2 			= '$subscriber_street_2',
													zip_code 			= '$subscriber_postal_code',
													city 				= '$subscriber_city',
													state 				= '$subscriber_state',
													home_phone 			= '$subscriber_phone',
													work_phone 			= '$subscriber_biz_phone',
													mbl_phone 			= '$subscriber_mobile',
													scan_card			= '$scan_card',
													scan_label			= '$scan_label',
													ins_caseid			= '$iolinkInsCaseIdToInsert',	
													claims_adjustername	= '$claims_adjustername',
													claims_adjusterphone= '$claims_adjusterphone',
													Sec_HCFA			= '$Sec_HCFA',
													newComDate			= '$newComDate',
													actInsComp			= '$actInsComp',
													actInsCompDate		= '$actInsCompDate',
													scan_card2			= '$scan_card2',
													scan_label2			= '$scan_label2',
													cardscan_date		= '$cardscan_date',
													cardscan_comments	= '$cardscan_comments',
													cardscan1_datetime	= '$cardscan1_datetime',
													self_pay_provider	= '$self_pay_provider',
													patient_id 			= '$demoPatientInstId',
													waiting_id 			= '$demoPatientInWtId'
												";
						$insertQueryInsurence .= $whereQueryInsurence;
						$insertQueryInsurenceRsId = imw_query($insertQueryInsurence);
					}	
					$a++;
					$b++;
				}
				if ($b == count($insurenceData)){
					$insurenceDataSaveFlag = true;
				}
				else{
					break;
				}
				
				//START SAVING INSURANCE SCAN DOCUMENTS
				$InsurenceScanDocXML = $iolinkMainData[16];
				$tag = 'InsurenceScanDocChild';
				$InsurenceScanDoc = array();
				$InsurenceScanDoc = XMLtoArray($InsurenceScanDocXML,$tag);
				//print_r($InsurenceScanDoc);
				$a = 0;
				$b = 0;
				
				while ($a < count($InsurenceScanDoc)) {		
					
					$insScanDocType				= $InsurenceScanDoc[$a]['type'];
					$insScanDocInsCaseId		= $InsurenceScanDoc[$a]['ins_caseid'];
					//$insScanDocPatientId		= $InsurenceScanDoc[$a]['patient_id'];	
					$insScanDocScanCard 		= $InsurenceScanDoc[$a]['scan_card'];
					$insScanDocScanLabel		= $InsurenceScanDoc[$a]['scan_label'];
					$insScanDocScanCard2		= $InsurenceScanDoc[$a]['scan_card2'];	
					$insScanDocScanLabe2 		= $InsurenceScanDoc[$a]['scan_label2'];
					$insScanDocCreatedDate		= $InsurenceScanDoc[$a]['created_date'];
					$insScanDocDocumentStatus	= $InsurenceScanDoc[$a]['document_status'];	
					$insScanDocCardscanDate 	= $InsurenceScanDoc[$a]['cardscan_date'];
					$insScanDocCardscanComments	= $InsurenceScanDoc[$a]['cardscan_comments'];
					$insScanDocCardscan1Date	= $InsurenceScanDoc[$a]['cardscan1_date'];
					
						
					$iolinkScanDocInsCaseIdToInsert = "";
					foreach($arrInsCaseId as $key => $val) {
						foreach($val as $key1 => $val1) {
							if($val1 == $insScanDocInsCaseId){
								//echo $val1.'=='.$insScanDocInsCaseId.'=>'.$val['iolinkInsCaseId'].'<br>';
								$iolinkScanDocInsCaseIdToInsert = $val['iolinkInsCaseId'];
							}
						}
					}
					$whereQueryInsurenceScanDoc = "";
					$selectQryInsurenceScanDoc = "select * from iolink_insurance_scan_documents where patient_id = $demoPatientInstId and waiting_id = $demoPatientInWtId and type = '$insScanDocType' and ins_caseid = '$iolinkScanDocInsCaseIdToInsert'";
					$rsSelectQryInsurenceScanDoc = imw_query($selectQryInsurenceScanDoc);
					$numRowSelectQryInsurenceScanDoc = imw_num_rows($rsSelectQryInsurenceScanDoc);
					//echo $numRowSelectQryInsurenceScanDoc;
					if($numRowSelectQryInsurenceScanDoc==0){					
						$insertQueryInsurenceScanDoc = "insert into iolink_insurance_scan_documents set ";					
					}
					else{					
						$insertQueryInsurenceScanDoc = "update iolink_insurance_scan_documents set ";
						$whereQueryInsurenceScanDoc = "where patient_id = $demoPatientInstId and waiting_id = $demoPatientInWtId and type = '$insScanDocType' and ins_caseid = '$iolinkScanDocInsCaseIdToInsert'";
					}
					$insertQueryInsurenceScanDoc .= "	type 			= '$insScanDocType',
													ins_caseid 			= '$iolinkScanDocInsCaseIdToInsert',												
													scan_card 			= '$insScanDocScanCard',
													scan_label 			= '$insScanDocScanLabel',
													scan_card2 			= '$insScanDocScanCard2',
													scan_label2 		= '$insScanDocScanLabe2',
													created_date 		= '$insScanDocCreatedDate',
													document_status 	= '$insScanDocDocumentStatus',
													cardscan_date 		= '$insScanDocCardscanDate',	
													cardscan_comments 	= '$insScanDocCardscanComments',
													cardscan1_date 		= '$insScanDocCardscan1Date',
													patient_id 			= '$demoPatientInstId',
													waiting_id 			= '$demoPatientInWtId'
											";
					$insertQueryInsurenceScanDoc .= $whereQueryInsurenceScanDoc;
					$insertQueryInsurenceScanDocRsId = imw_query($insertQueryInsurenceScanDoc);
					$a++;
					$b++;
				}
				if ($b == count($InsurenceScanDoc)){
					$InsurenceScanDocSaveFlag = true;
				}
				else{
					break;
				}
				//END SAVING INSURANCE SCAN DOCUMENTS
				
				//Saving Consent data Information
				$consentDataXML = $iolinkMainData[2];
				$tag = 'consentDataChild';
				$consentData = array();
				$consentData = XMLtoArray($consentDataXML,$tag);
				//print_r($consentData);
				$a = 0;
				$b = 0;
				while ($a < count($consentData)) {			
					$surgery_consent_name 		= $consentData[$a]['surgery_consent_name'];
					$surgery_consent_alias		= $consentData[$a]['surgery_consent_alias'];
					$surgery_consent_data		= $consentData[$a]['surgery_consent_data'];	
					$surgery_consent_sign 		= $consentData[$a]['surgery_consent_sign'];
					$ascId		 				= $consentData[$a]['ascId'];
					$form_status				= $consentData[$a]['form_status'];	
					$eposted 					= $consentData[$a]['eposted'];
					$consent_template_id		= $consentData[$a]['consent_template_id'];
					$consent_category_id		= $consentData[$a]['consent_category_id'];	
					$left_navi_status 			= $consentData[$a]['left_navi_status'];
					$consent_purge_status		= $consentData[$a]['consent_purge_status'];
					$sigStatus					= $consentData[$a]['sigStatus'];
					$signSurgeon1Activate		= $consentData[$a]['signSurgeon1Activate'];	
					$signSurgeon1Id				= $consentData[$a]['signSurgeon1Id'];
					$signSurgeon1FirstName 		= $consentData[$a]['signSurgeon1FirstName'];
					$signSurgeon1MiddleName		= $consentData[$a]['signSurgeon1MiddleName'];	
					$signSurgeon1LastName 		= $consentData[$a]['signSurgeon1LastName'];	
					$signSurgeon1Status			= $consentData[$a]['signSurgeon1Status'];
					$signSurgeon1DateTime		= $consentData[$a]['signSurgeon1DateTime'];
					$signNurseActivate			= $consentData[$a]['signNurseActivate'];
					$signNurseId				= $consentData[$a]['signNurseId'];	
					$signNurseFirstName			= $consentData[$a]['signNurseFirstName'];	
					$signNurseMiddleName		= $consentData[$a]['signNurseMiddleName'];	
					$signNurseLastName			= $consentData[$a]['signNurseLastName'];	
					$signNurseStatus		 	= $consentData[$a]['signNurseStatus'];
					$signNurseDateTime			= $consentData[$a]['signNurseDateTime'];
					$signAnesthesia1Activate	= $consentData[$a]['signAnesthesia1Activate'];
					$signAnesthesia1Id			= $consentData[$a]['signAnesthesia1Id'];	
					$signAnesthesia1FirstName	= $consentData[$a]['signAnesthesia1FirstName'];	
					$signAnesthesia1MiddleName 	= $consentData[$a]['signAnesthesia1MiddleName'];
					$signAnesthesia1LastName	= $consentData[$a]['signAnesthesia1LastName'];
					$signAnesthesia1Status		= $consentData[$a]['signAnesthesia1Status'];
					$signAnesthesia1DateTime	= $consentData[$a]['signAnesthesia1DateTime'];
					$signWitness1Activate		= $consentData[$a]['signWitness1Activate'];
					$signWitness1Id				= $consentData[$a]['signWitness1Id'];	
					$signWitness1FirstName		= $consentData[$a]['signWitness1FirstName'];	
					$signWitness1MiddleName		= $consentData[$a]['signWitness1MiddleName'];	
					$signWitness1LastName		= $consentData[$a]['signWitness1LastName'];	
					$signWitness1Status		 	= $consentData[$a]['signWitness1Status'];
					$signWitness1DateTime		= $consentData[$a]['signWitness1DateTime'];
					$fldPatientWaitingId		= $consentData[$a]['fldPatientWaitingId'];
					$patient_id					= $consentData[$a]['patient_id'];	
					$consentSignedStatus		= $consentData[$a]['consentSignedStatus'];	
					$form_created_date 			= $consentData[$a]['form_created_date'];
					$consentGroupName 			= 'BRIAN';
					if($surgery_consent_name || $surgery_consent_alias){			
						$whereQueryConsent = "";
						$selectQry = "select * from iolink_consent_filled_form where patient_id = $demoPatientInstId and fldPatientWaitingId = $demoPatientInWtId and consent_template_id = $consent_template_id";
						//exit;
						$rsSelectQry = imw_query($selectQry);
						$numRowSelectQry = imw_num_rows($rsSelectQry);
						//echo $numRowSelectQry.'qwqwqw';
						if($numRowSelectQry==0){					
							$insertQueryConsent = "insert into iolink_consent_filled_form set ";					
						}
						else{					
							$insertQueryConsent = "update iolink_consent_filled_form set ";
							$whereQueryConsent = "where patient_id = $demoPatientInstId and fldPatientWaitingId = $demoPatientInWtId and consent_template_id = $consent_template_id";
						}
						$insertQueryConsent .= "surgery_consent_name = '".addslashes($surgery_consent_name)."',
												surgery_consent_alias = '".addslashes($surgery_consent_alias)."',
												surgery_consent_data = '".addslashes($surgery_consent_data)."',
												surgery_consent_sign = '".addslashes($surgery_consent_sign)."',
												ascId = '".addslashes($ascId)."',
												form_status = '".addslashes($form_status)."',
												eposted = '".addslashes($eposted)."',
												consent_template_id = '".addslashes($consent_template_id)."',
												consent_category_id = '".addslashes($consent_category_id)."',
												left_navi_status = '".addslashes($left_navi_status)."',
												consent_purge_status = '".addslashes($consent_purge_status)."',
												sigStatus = '".addslashes($sigStatus)."',
												signSurgeon1Activate = '".addslashes($signSurgeon1Activate)."',
												signSurgeon1Id = '".addslashes($signSurgeon1Id)."',
												signSurgeon1FirstName = '".addslashes($signSurgeon1FirstName)."',
												signSurgeon1MiddleName = '".addslashes($signSurgeon1MiddleName)."',
												signSurgeon1LastName = '".addslashes($signSurgeon1LastName)."',
												signSurgeon1Status = '".addslashes($signSurgeon1Status)."',						
												signSurgeon1DateTime = '".addslashes($signSurgeon1DateTime)."',
												signNurseActivate = '".addslashes($signNurseActivate)."',
												signNurseId = '".addslashes($signNurseId)."',
												signNurseFirstName = '".addslashes($signNurseFirstName)."',
												signNurseMiddleName = '".addslashes($signNurseMiddleName)."',
												signNurseLastName = '".addslashes($signNurseLastName)."',
												signNurseStatus = '".addslashes($signNurseStatus)."',						
												signNurseDateTime = '".addslashes($signNurseDateTime)."',
												signAnesthesia1Activate = '".addslashes($signAnesthesia1Activate)."',
												signAnesthesia1Id = '".addslashes($signAnesthesia1Id)."',
												signAnesthesia1FirstName = '".addslashes($signAnesthesia1FirstName)."',
												signAnesthesia1MiddleName = '".addslashes($signAnesthesia1MiddleName)."',
												signAnesthesia1LastName = '".addslashes($signAnesthesia1LastName)."',
												signAnesthesia1Status = '".addslashes($signAnesthesia1Status)."',
												signAnesthesia1DateTime = '".addslashes($signAnesthesia1DateTime)."',
												signWitness1Activate = '".addslashes($signWitness1Activate)."',						
												signWitness1Id = '".addslashes($signWitness1Id)."',
												signWitness1FirstName = '".addslashes($signWitness1FirstName)."',
												signWitness1MiddleName = '".addslashes($signWitness1MiddleName)."',
												signWitness1LastName = '".addslashes($signWitness1LastName)."',
												signWitness1Status = '".addslashes($signWitness1Status)."',
												signWitness1DateTime = '".addslashes($signWitness1DateTime)."',
												fldPatientWaitingId = '".addslashes($demoPatientInWtId)."',
												patient_id = '$demoPatientInstId',												
												consentSignedStatus = '".addslashes($consentSignedStatus)."'								
											";
						$insertQueryConsent .= $whereQueryConsent;
						//$insertQueryConsentRsId = imw_query($insertQueryConsent);
					}
					$a++;
					$b++;
				}
				if ($b == count($consentData)){
					$consentDataSaveFlag = true;
				}
				else{
					break;
				}
				//Saving Consent Signature Information
				$consentSigDataXML = $iolinkMainData[3];
				$tag = 'consentDataSigChild';
				$consentSigData = array();
				$consentSigData = XMLtoArray($consentSigDataXML,$tag);
				//print_r($consentSigData);
				
				$a = 0;
				$b = 0;
				while ($a < count($consentSigData)) {	
					$pathPDF = "";		
					$signature_content 			= $consentSigData[$a]['signature_content'];
					$consent_template_id		= $consentSigData[$a]['consent_template_id'];
					$confirmation_id			= $consentSigData[$a]['confirmation_id'];	
					$signature_count 			= $consentSigData[$a]['signature_count'];		
					$signature_image_path		= $consentSigData[$a]['signature_image_path'];	
					$patient_in_waiting_id 		= $consentSigData[$a]['patient_in_waiting_id'];
					$patient_id					= $consentSigData[$a]['patient_id'];
					$surgery_consent_auto_id	= $consentSigData[$a]['surgery_consent_auto_id'];	
					$signature_image_path		= explode("/",$signature_image_path);
					$signature_image_name		= $signature_image_path[1];
					$sigComeFrom				= '1';
					//SAVE SIGNATURE
					//for($ps=1;$ps<=$sig_count;$ps++){
						$postData = "";
						$postData = $signature_content;
						if($postData) {
							$aConn = new COM("SIGPLUS.SigPlusCtrl.1");
							$aConn->InitSigPlus();
							$aConn->SigCompressionMode = 2;
							$aConn->SigString=$postData;
							$aConn->ImageFileFormat = 4; //4=jpg, 0=bmp, 6=tif
							$aConn->ImageXSize = 500; //width of resuting image in pixels
							$aConn->ImageYSize =165; //height of resulting image in pixels
							$aConn->ImagePenWidth = 11; //thickness of ink in pixels
							$aConn->JustifyMode = 5;  //center and fit signature to size
							/*
							$path =  realpath(dirname(__FILE__).'/SigPlus_images').'\sign_iolink_'.$_REQUEST["intPatientWaitingId"].'_'.date('d_m_y_h_i_s').'_'.$ps.'.jpg';
							$patientSignArr[] = $path;
							$aConn->WriteImageFile("$path");
							*/
							//ALSO SAVE IMAGE FOR PDF
							$pathPDF =  realpath(dirname(__FILE__).'/html2pdfnew').'\\'.$signature_image_name;
		
							//$pathPDF =  realpath(dirname(__FILE__).'/html2pdfnew').'\sign_iolink_'.$_REQUEST["intPatientWaitingId"].'_'.date('d_m_y_h_i_s').'_'.$ps.'.jpg';
							$patientSignArr[] = $pathPDF;
							$aConn->WriteImageFile("$pathPDF");
							
						}	
					//}
				//end save sig.	
					if($pathPDF){
						$whereQueryConsentSig = "";
						$selectQry = "select * from iolink_consent_form_signature where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and consent_template_id = $consent_template_id and signature_count = $signature_count";
						//exit;
						$rsSelectQry = imw_query($selectQry);
						$numRowSelectQry = imw_num_rows($rsSelectQry);
						//echo $numRowSelectQry.'qwqwqw';
						if($numRowSelectQry==0){					
							$insertQueryConsentSig = "insert into iolink_consent_form_signature set ";					
						}
						else{					
							$insertQueryConsentSig = "update iolink_consent_form_signature set ";
							$whereQueryConsentSig = "where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and consent_template_id = $consent_template_id and signature_count = $signature_count";
						}
						$insertQueryConsentSig .= "signature_content = '".addslashes($signature_content)."',
												consent_template_id = '".addslashes($consent_template_id)."',
												confirmation_id = '".addslashes($confirmation_id)."',
												signature_count = '".addslashes($signature_count)."',
												signature_image_path = '".addslashes($pathPDF)."',
												patient_in_waiting_id = '".addslashes($demoPatientInWtId)."',												
												patient_id = '$demoPatientInstId'										
											";			
						$insertQueryConsentSig .= $whereQueryConsentSig;
						//$insertQueryConsentSigRsId = imw_query($insertQueryConsentSig);						
					}
					$a++;
					$b++;
				}
				if ($b == count($consentSigData)){
					$consentSigDataSaveFlag = true;
				}
				else{
					break;
				}
				/////////////////////
				//Saving scan data Information
				$sacnDataXML = $iolinkMainData[5];				
				$tag = 'scanDocDataChild';
				$scanData = array();
				$scanData = XMLtoArray($sacnDataXML,$tag);
				//print_r($scanData);
				//exit;
				$a = 0;
				$b = 0;
				while ($a < count($scanData)) {			
					$patient_id 				= $scanData[$a]['patient_id'];
					$scan_doc_add				= $scanData[$a]['scan_doc_add'];
					$iolink_scan_folder_name	= $scanData[$a]['scan_type_folder'];	
					$scan_save_date_time 		= $scanData[$a]['created_date'];
					$scan1Upload	 			= $scanData[$a]['doc_data'];
					$mask	 					= $scanData[$a]['mask'];					
					$scanDocAdd = $scan_doc_add;					
					$scanDocAdd = explode("/",$scanDocAdd);									
					$document_name = $scanDocAdd[2];
					$patient_in_waiting_id = $demoPatientInWtId;
					if($iolink_scan_folder_name == 0){
						$iolink_scan_folder_name = "ptInfo";
					}
					if($iolink_scan_folder_name == 1){
						$iolink_scan_folder_name = "clinical";
					}
					if($iolink_scan_folder_name == 2){
						$iolink_scan_folder_name = "healthQuest";
					}
					if($iolink_scan_folder_name == 3){
						$iolink_scan_folder_name = "h&p";
					}
					if($iolink_scan_folder_name == 4){
						$iolink_scan_folder_name = "ekg";
					}
					
					$scan1Upload = base64_decode($scan1Upload);
					
					$whereQueryScan = "";
					$selectQry = "select * from iolink_scan_consent where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and document_name = '".addslashes($document_name)."'";
					//exit;
					$rsSelectQry = imw_query($selectQry);
					$numRowSelectQry = imw_num_rows($rsSelectQry);
					//echo $numRowSelectQry.'qwqwqw';
					if($numRowSelectQry==0){					
						$insertQueryScan = "insert into iolink_scan_consent set ";					
					}
					else{					
						$insertQueryScan = "update iolink_scan_consent set ";
						$whereQueryScan = "where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and document_name = '".addslashes($document_name)."'";
					}
					$insertQueryScan .= "patient_id = '".addslashes($demoPatientInstId)."',
 										 patient_in_waiting_id = '".addslashes($demoPatientInWtId)."',
										 scan1Upload = '".addslashes($scan1Upload)."',
										 scan_save_date_time = '".addslashes($scan_save_date_time)."',
										 document_name = '".addslashes($document_name)."',
										 mask = '".addslashes($mask)."',
										 iolink_scan_folder_name = '".addslashes($iolink_scan_folder_name)."'									
										";
					$insertQueryScan .= $whereQueryScan;
					$insertQueryScanRsId = imw_query($insertQueryScan);					
					$a++;
					$b++;
				}
				if ($b == count($scanData)){
					$scanDataSaveFlag = true;
				}
				else{
					break;
				}
				////////////////////
				//End Saving scan data Information
				//Saving consent pdf
				$pdfDataXML = $iolinkMainData[6];				
				//exit;
				$tag = 'pdfDocDataChild';
				$pdfData = array();
				$pdfData = XMLtoArray($pdfDataXML,$tag);
				//print_r($pdfData);
				//exit;
				$a = 0;
				$b = 0;
				$httpfolder =  realpath(dirname(__FILE__));
				
				$iolinkPdfPath = $httpfolder."/admin/pdfFiles/";
				$imedicIolinkPdfPath = $iolinkPdfPath.$surgeonName;
				if(!is_dir($imedicIolinkPdfPath)){		
					mkdir($imedicIolinkPdfPath);
				}
						
				//$patientDir = "\PatientId_".$demoPatientInstId;
								
				//Create patient directory
				/*if(!is_dir($imedicIolinkPdfPath.$patientDir)){		
					mkdir($imedicIolinkPdfPath.$patientDir);
				}*/
				
				
				while ($a < count($pdfData)) {			
					$patient_id 			= $pdfData[$a]['patient_id'];
					$surgery_consent_name	= $pdfData[$a]['surgery_consent_name'];
					$form_created_date		= $pdfData[$a]['form_created_date'];	
					$pdf_doc_data 			= $pdfData[$a]['pdf_doc_data'];						
					//exit;
					$patientCreatedDateTime = str_replace(" ","_",$form_created_date);
					$patientCreatedDateTime = str_replace(":","-",$patientCreatedDateTime);
					$pdfFileName = $surgery_consent_name.'_'.$demoPatientInstId.'_'.$patientCreatedDateTime.'.pdf';
					//$putPdfFileName = $imedicIolinkPdfPath.$patientDir.'\\'.$pdfFileName;				
					$putPdfFileName = $imedicIolinkPdfPath.'\\'.$pdfFileName;				
					//$putPdfDocdata = base64_decode($pdf_doc_data);	
					$putPdfDocdata = base64_decode($pdf_doc_data);			
					//file_put_contents($putPdfFileName,$putPdfDocdata);			
					$pdfFilePath = "pdfFiles".'/'.$surgeonName.'/'.$pdfFileName;
					$iolink_scan_folder_name = "ptInfo";
					$whereQueryScan = "";
					$selectQry = "select * from iolink_scan_consent where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and document_name = '".addslashes($pdfFileName)."'";
					//exit;
					$rsSelectQry = imw_query($selectQry);
					$numRowSelectQry = imw_num_rows($rsSelectQry);
					//echo $numRowSelectQry.'qwqwqw';
					if($numRowSelectQry==0){					
						$insertQueryScan = "insert into iolink_scan_consent set ";					
					}
					else{					
						$insertQueryScan = "update iolink_scan_consent set ";
						$whereQueryScan = "where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and document_name = '".addslashes($pdfFileName)."'";
					}
					$insertQueryScan .= "patient_id = '".addslashes($demoPatientInstId)."',
 										 patient_in_waiting_id = '".addslashes($demoPatientInWtId)."',										 
										 scan_save_date_time = '".addslashes($form_created_date)."',
										 document_name = '".addslashes($pdfFileName)."',
										 image_type = 'application/pdf',		
										 pdfFilePath = '".addslashes($pdfFilePath)."',											 								 
										 iolink_scan_folder_name = '".addslashes($iolink_scan_folder_name)."'									
										";
					$insertQueryScan .= $whereQueryScan;
					$insertQueryScanRsId = imw_query($insertQueryScan);			
					if($insertQueryScanRsId){
						if($putPdfDocdata){
							file_put_contents($putPdfFileName,$putPdfDocdata);	
						}
					}
					$a++;
					$b++;
				}
				if ($b == count($pdfData)){
					$pdfDataSaveFlag = true;
				}
				else{
					break;
				}
				////////////////////
				//End Saving consent pdf
				//Saving face sheet pdf
				$faceSheetPdfDataXML = $iolinkMainData[7];				
				//exit;
				$tag = 'faceSheetDocDataChild';
				$faceSheetDocData = array();
				$faceSheetDocData = XMLtoArray($faceSheetPdfDataXML,$tag);
				//print_r($faceSheetDocData);
				//exit;
				$a = 0;
				$b = 0;
				if(count($faceSheetDocData)){
					$httpfolder =  realpath(dirname(__FILE__));				
					$iolinkPdfPath = $httpfolder."/admin/pdfFiles/";
					$imedicIolinkPdfPath = $iolinkPdfPath.$surgeonName;
					if(!is_dir($imedicIolinkPdfPath)){		
						mkdir($imedicIolinkPdfPath);
					}
				}
				while ($a < count($faceSheetDocData)) {			
					$patient_id 			= $faceSheetDocData[$a]['patient_id'];
					$file_name 				= $faceSheetDocData[$a]['file_name'];						
					$face_sheet_doc_data	= $faceSheetDocData[$a]['face_sheet_doc_data'];						
										
					$faceSheetPdfFileNameOld = 'faceSheet'.$demoPatientInstId.$demoPatientInWtId.'.pdf';
					$faceSheetPdfFileName = $file_name.'_'.$demoPatientInstId.'_'.$demoPatientInWtId.'.pdf';

					$faceSheetPutPdfFileName = $imedicIolinkPdfPath.'\\'.$faceSheetPdfFileName;				
					
					$faceSheetPutPdfDocdata = base64_decode($face_sheet_doc_data);			
					
					$pdfFilePath = "pdfFiles".'/'.$surgeonName.'/'.$faceSheetPdfFileName;
					$iolink_scan_folder_name = "ptInfo";
					$whereQueryScan = "";
					$numRowSelectQryOld=0;
					$numRowSelectQryNew=0;
					$selectQryOld = "select * from iolink_scan_consent where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and document_name = '".addslashes($faceSheetPdfFileNameOld)."'";
					$rsSelectQryOld = imw_query($selectQryOld);
					$numRowSelectQryOld = imw_num_rows($rsSelectQryOld);
					if($numRowSelectQryOld==0){	
						$selectQryNew = "select * from iolink_scan_consent where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and document_name = '".addslashes($faceSheetPdfFileName)."'";
						$rsSelectQryNew = imw_query($selectQryNew);
						$numRowSelectQryNew = imw_num_rows($rsSelectQryNew);						
					}
					if($numRowSelectQryOld==0 && $numRowSelectQryNew==0){					
						$insertQueryScan = "insert into iolink_scan_consent set ";					
					}
					else{					
					
						$insertQueryScan = "update iolink_scan_consent set ";
						if($numRowSelectQryOld>0 && $numRowSelectQryNew==0){
							$whereQueryScan = "where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and document_name = '".addslashes($faceSheetPdfFileNameOld)."'";
						}
						elseif($numRowSelectQryOld==0 && $numRowSelectQryNew>0){
							$whereQueryScan = "where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and document_name = '".addslashes($faceSheetPdfFileName)."'";
						}
						
					}
					$insertQueryScan .= "patient_id = '".addslashes($demoPatientInstId)."',
 										 patient_in_waiting_id = '".addslashes($demoPatientInWtId)."',										 										 
										 document_name = '".addslashes($faceSheetPdfFileName)."',
										 image_type = 'application/pdf',		
										 pdfFilePath = '".addslashes($pdfFilePath)."',											 								 
										 iolink_scan_folder_name = '".addslashes($iolink_scan_folder_name)."'									
										";
					$insertQueryScan .= $whereQueryScan;
					$insertQueryScanRsId = imw_query($insertQueryScan);			
					if($insertQueryScanRsId){
						if($faceSheetPutPdfDocdata){							
							if(@file_exists($imedicIolinkPdfPath.'\\'.$faceSheetPdfFileNameOld)){
								unlink($imedicIolinkPdfPath.'\\'.$faceSheetPdfFileNameOld);
							}
							file_put_contents($faceSheetPutPdfFileName,$faceSheetPutPdfDocdata);							
						}
					}

					$a++;
					$b++;
				}
				if ($b == count($faceSheetDocData)){
					$faceSheetDataSaveFlag = true;
				}
				else{
					break;
				}
				////////////////////
				//End Saving face sheet pdf
				//Saving Pre-op Health Questionnaire
				$PreOpHealthQuesDataXML = $iolinkMainData[8];
				
				$tag = 'patientPreOpDataChild';
				$patientPreOpHealthQuestionnaire = array();
				$patientPreOpHealthQuestionnaire = XMLtoArray($PreOpHealthQuesDataXML,$tag);
				//print_r($patientPreOpHealthQuestionnaire);				
				
				$a = 0;
				$b = 0;
				while ($a < count($patientPreOpHealthQuestionnaire)) {	
					$pathPDF = "";		
					$heartTrouble 			= $patientPreOpHealthQuestionnaire[$a]['heartTrouble'];
					$stroke					= $patientPreOpHealthQuestionnaire[$a]['stroke'];
					$HighBP					= $patientPreOpHealthQuestionnaire[$a]['HighBP'];	
					$anticoagulationTherapy = $patientPreOpHealthQuestionnaire[$a]['anticoagulationTherapy'];		
					$asthma					= $patientPreOpHealthQuestionnaire[$a]['asthma'];	
					$diabetes 				= $patientPreOpHealthQuestionnaire[$a]['diabetes'];
					$insulinDependence		= $patientPreOpHealthQuestionnaire[$a]['insulinDependence'];
					$epilepsy				= $patientPreOpHealthQuestionnaire[$a]['epilepsy'];						
					$restlessLegSyndrome	= $patientPreOpHealthQuestionnaire[$a]['restlessLegSyndrome'];	
					$hepatitis				= $patientPreOpHealthQuestionnaire[$a]['hepatitis'];	
					$hepatitisA				= $patientPreOpHealthQuestionnaire[$a]['hepatitisA'];	
					$hepatitisB				= $patientPreOpHealthQuestionnaire[$a]['hepatitisB'];	
					$hepatitisC				= $patientPreOpHealthQuestionnaire[$a]['hepatitisC'];	
					$kidneyDisease			= $patientPreOpHealthQuestionnaire[$a]['kidneyDisease'];	
					$shunt					= $patientPreOpHealthQuestionnaire[$a]['shunt'];	
					$fistula				= $patientPreOpHealthQuestionnaire[$a]['fistula'];	
					$hivAutoimmuneDiseases	= $patientPreOpHealthQuestionnaire[$a]['hivAutoimmuneDiseases'];	
					$hivTextArea			= $patientPreOpHealthQuestionnaire[$a]['hivTextArea'];	
					$cancerHistory			= $patientPreOpHealthQuestionnaire[$a]['cancerHistory'];	
					$cancerHistoryDesc		= $patientPreOpHealthQuestionnaire[$a]['cancerHistoryDesc'];	
					$organTransplant		= $patientPreOpHealthQuestionnaire[$a]['organTransplant'];	
					$organTransplantDesc	= $patientPreOpHealthQuestionnaire[$a]['organTransplantDesc'];	
					$anesthesiaBadReaction	= $patientPreOpHealthQuestionnaire[$a]['anesthesiaBadReaction'];						
					$tuberculosis			= $patientPreOpHealthQuestionnaire[$a]['tuberculosis'];	
					$otherTroubles			= $patientPreOpHealthQuestionnaire[$a]['otherTroubles'];	
					$walker					= $patientPreOpHealthQuestionnaire[$a]['walker'];	
					$contactLenses			= $patientPreOpHealthQuestionnaire[$a]['contactLenses'];	
					$smoke					= $patientPreOpHealthQuestionnaire[$a]['smoke'];	
					$smokeHowMuch			= $patientPreOpHealthQuestionnaire[$a]['smokeHowMuch'];	
					$alchohol				= $patientPreOpHealthQuestionnaire[$a]['alchohol'];	
					$alchoholHowMuch		= $patientPreOpHealthQuestionnaire[$a]['alchoholHowMuch'];	
					$autoInternalDefibrillator= $patientPreOpHealthQuestionnaire[$a]['autoInternalDefibrillator'];	
					$metalProsthetics		= $patientPreOpHealthQuestionnaire[$a]['metalProsthetics'];	
					$notes					= $patientPreOpHealthQuestionnaire[$a]['notes'];						
					$patientSign			= $patientPreOpHealthQuestionnaire[$a]['patientSign'];
					$patient_sign_image_path= $patientPreOpHealthQuestionnaire[$a]['patient_sign_image_path'];
					$witness_sign_image_path= $patientPreOpHealthQuestionnaire[$a]['witness_sign_image_path'];
					$dateQuestionnaire		= $patientPreOpHealthQuestionnaire[$a]['dateQuestionnaire'];
					$emergencyContactPerson	= $patientPreOpHealthQuestionnaire[$a]['emergencyContactPerson'];
					$emergencyContactPhone	= $patientPreOpHealthQuestionnaire[$a]['emergencyContactPhone'];
					$witnessname			= $patientPreOpHealthQuestionnaire[$a]['witnessname'];
					$witnessSign			= $patientPreOpHealthQuestionnaire[$a]['witnessSign'];
					$allergies_status		= $patientPreOpHealthQuestionnaire[$a]['allergies_status'];
					$allergies_status_reviewed= $patientPreOpHealthQuestionnaire[$a]['allergies_status_reviewed'];					
					
					$patient_sign_image_path	= explode("/",$patient_sign_image_path);
					$patient_image_name		= $patient_sign_image_path[3];
					$witness_sign_image_path	= explode("/",$witness_sign_image_path);
					$witness_image_name		= $witness_sign_image_path[3];
					//print_r($patient_sign_image_path);
					//exit;
					//SAVE SIGNATURE
					$patientSignArr = array();
					for($ps=1;$ps<=2;$ps++){
						$postData = "";
						if($ps==1){
							$postData = $patientSign;
							$imageName = $patient_image_name;
						}
						elseif($ps==2){
							$postData = $witnessSign;
							$imageName = $witness_image_name;
						}
						if($postData) {
							$aConn = new COM("SIGPLUS.SigPlusCtrl.1");
							$aConn->InitSigPlus();
							$aConn->SigCompressionMode = 2;
							$aConn->SigString=$postData;
							$aConn->ImageFileFormat = 4; //4=jpg, 0=bmp, 6=tif
							$aConn->ImageXSize = 500; //width of resuting image in pixels
							$aConn->ImageYSize =165; //height of resulting image in pixels
							$aConn->ImagePenWidth = 11; //thickness of ink in pixels
							$aConn->JustifyMode = 5;  //center and fit signature to size
							$pathPDF =  realpath(dirname(__FILE__).'/html2pdfnew').'\\'.$imageName;	
							$savePath ="";
							//$pathPDF =  realpath(dirname(__FILE__).'/html2pdfnew').'\\'.$signature_image_name;						
							$savePath = "html2pdfnew"."/".$imageName;		
							$patientSignArr[] = $savePath;
							$aConn->WriteImageFile("$pathPDF");
						}
					}
					//print_r($patientSignArr);
					//exit;
				//end save sig.	
					
					$whereQueryPreOpHealth = "";
					$selectQry = "select * from iolink_preophealthquestionnaire where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId";
					$rsSelectQry = imw_query($selectQry);
					$numRowSelectQry = imw_num_rows($rsSelectQry);						
					if($numRowSelectQry==0){					
						$insertQueryPreOpHealth = "insert into iolink_preophealthquestionnaire set ";					
					}
					else{					
						$insertQueryPreOpHealth = "update iolink_preophealthquestionnaire set ";
						$whereQueryPreOpHealth = "where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId";
					}
					$insertQueryPreOpHealth .= "heartTrouble = '".addslashes($heartTrouble)."',
											stroke = '".addslashes($stroke)."',
											HighBP = '".addslashes($HighBP)."',
											anticoagulationTherapy = '".addslashes($anticoagulationTherapy)."',
											asthma = '".addslashes($asthma)."',
											diabetes = '".addslashes($diabetes)."',												
											insulinDependence = '".addslashes($insulinDependence)."',
											epilepsy = '".addslashes($epilepsy)."',
											restlessLegSyndrome = '".addslashes($restlessLegSyndrome)."',
											hepatitis = '".addslashes($hepatitis)."',
											hepatitisA = '".addslashes($hepatitisA)."',
											hepatitisB = '".addslashes($hepatitisB)."',
											hepatitisC = '".addslashes($hepatitisC)."',
											kidneyDisease = '".addslashes($kidneyDisease)."',
											shunt = '".addslashes($shunt)."',
											fistula = '".addslashes($fistula)."',
											hivAutoimmuneDiseases = '".addslashes($hivAutoimmuneDiseases)."',
											hivTextArea = '".addslashes($hivTextArea)."',
											cancerHistory = '".addslashes($cancerHistory)."',
											cancerHistoryDesc = '".addslashes($cancerHistoryDesc)."',
											organTransplant = '".addslashes($organTransplant)."',
											organTransplantDesc = '".addslashes($organTransplantDesc)."',
											anesthesiaBadReaction = '".addslashes($anesthesiaBadReaction)."',
											tuberculosis = '".addslashes($tuberculosis)."',
											otherTroubles = '".addslashes($otherTroubles)."',
											walker = '".addslashes($walker)."',
											contactLenses = '".addslashes($contactLenses)."',
											smoke = '".addslashes($smoke)."',
											smokeHowMuch = '".addslashes($smokeHowMuch)."',
											alchohol = '".addslashes($alchohol)."',
											alchoholHowMuch = '".addslashes($alchoholHowMuch)."',												
											autoInternalDefibrillator = '".addslashes($autoInternalDefibrillator)."',
											metalProsthetics = '".addslashes($metalProsthetics)."',
											notes = '".addslashes($notes)."',
											patientSign = '".addslashes($patientSign)."',
											patient_sign_image_path = '".addslashes($patientSignArr[0])."',
											witness_sign_image_path = '".addslashes($patientSignArr[1])."',
											dateQuestionnaire = '".addslashes($dateQuestionnaire)."',
											emergencyContactPerson = '".addslashes($emergencyContactPerson)."',
											emergencyContactPhone = '".addslashes($emergencyContactPhone)."',
											witnessname = '".addslashes($witnessname)."',
											witnessSign = '".addslashes($witnessSign)."',												
											allergies_status = '".addslashes($allergies_status)."',
											allergies_status_reviewed = '".addslashes($allergies_status_reviewed)."',
											patient_in_waiting_id = '".$demoPatientInWtId."',																								
											patient_id = '$demoPatientInstId'										
										";		
					$insertQueryPreOpHealth .= $whereQueryPreOpHealth;
					$insertQueryPreOpHealth.'asdasdasd';
					
					$insertQueryPreOpHealthRsId = imw_query($insertQueryPreOpHealth);						
					if($insertQueryPreOpHealthRsId){
						$b++;
					}
					$a++;
					
				}
				if ($b == count($patientPreOpHealthQuestionnaire)){
					$patientPreOpHealthQuesDataSaveFlag = true;
				}
				else{
					break;
				}
				//end Saving Pre-op Health Questionnaire
				//Saving Pre-op Health Questionnaire Admin
				$patientAdminHealthQuesDataXML = $iolinkMainData[9];
				$tag = 'patientAdminHealthDataChild';
				$patientAdminHealthQues = array();
				$patientAdminHealthQues = XMLtoArray($patientAdminHealthQuesDataXML,$tag);
				//print_r($patientAdminHealthQues);				
				$a = 0;
				$b = 0;
				while ($a < count($patientAdminHealthQues)) {						
					$adminQuestion 			= $patientAdminHealthQues[$a]['adminQuestion'];
					$adminQuestionStatus	= $patientAdminHealthQues[$a]['adminQuestionStatus'];
								
					$whereQueryAdminHealthQues = "";
					$selectQry = "select * from iolink_healthquestionadmin where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and adminQuestion = '$adminQuestion'";
					$rsSelectQry = imw_query($selectQry);
					$numRowSelectQry = imw_num_rows($rsSelectQry);						
					if($numRowSelectQry==0){					
						$insertQueryAdminHealthQues = "insert into iolink_healthquestionadmin set ";					
					}
					else{					
						$insertQueryAdminHealthQues = "update iolink_healthquestionadmin set ";
						$whereQueryAdminHealthQues = "where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and adminQuestion = '$adminQuestion'";
					}
					$insertQueryAdminHealthQues .= "adminQuestion = '".addslashes($adminQuestion)."',
													adminQuestionStatus = '".addslashes($adminQuestionStatus)."',	
													patient_in_waiting_id = '".$demoPatientInWtId."',																								
													patient_id = '$demoPatientInstId'										
													";			
					$insertQueryAdminHealthQues .= $whereQueryAdminHealthQues;
					$insertQueryAdminHealthQuesRsId = imw_query($insertQueryAdminHealthQues);						
					if($insertQueryAdminHealthQuesRsId){
						$b++;
					}
					$a++;
					
				}
				if ($b == count($patientAdminHealthQues)){
					$patientAdminHealthQuesDataSaveFlag = true;
				}
				else{
					break;
				}
				//end Saving Pre-op Health Questionnaire Admin
				//Saving Pre-op Health Questionnaire Patient Allergy
				$patientHealthQuesAllergyDataXML = $iolinkMainData[10];
				$tag = 'patientHealthQuesAllergyDataChild';
				$patientHealthQuesAllergy = array();
				$patientHealthQuesAllergy = XMLtoArray($patientHealthQuesAllergyDataXML,$tag);
				//print_r($patientAdminHealthQues);				
				$a = 0;
				$b = 0;
				while ($a < count($patientHealthQuesAllergy)) {						
					$allergy_name 			= $patientHealthQuesAllergy[$a]['allergy_name'];
					$reaction_name			= $patientHealthQuesAllergy[$a]['reaction_name'];
								
					$whereQueryHealthQuesAllergy = "";
					$selectQry = "select * from iolink_patient_allergy where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and allergy_name='$allergy_name' and reaction_name='$reaction_name'";
					
					$rsSelectQry = imw_query($selectQry);
					$numRowSelectQry = imw_num_rows($rsSelectQry);						
					if($numRowSelectQry==0){					
						$insertQueryHealthQuesAllergy = "insert into iolink_patient_allergy set ";					
					}
					else{					
						$insertQueryHealthQuesAllergy = "update iolink_patient_allergy set ";
						$whereQueryHealthQuesAllergy = "where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and allergy_name='$allergy_name' and reaction_name='$reaction_name'";
					}
					$insertQueryHealthQuesAllergy .= "allergy_name = '".addslashes($allergy_name)."',
													reaction_name = '".addslashes($reaction_name)."',	
													patient_in_waiting_id = '".$demoPatientInWtId."',																								
													patient_id = '$demoPatientInstId'										
													";			
					$insertQueryHealthQuesAllergy .= $whereQueryHealthQuesAllergy;
					$insertQueryHealthQuesAllergyRsId = imw_query($insertQueryHealthQuesAllergy);						
					if($insertQueryHealthQuesAllergyRsId){
						$b++;
					}
					$a++;
					
				}
				if ($b == count($patientHealthQuesAllergy)){
					$patientHealthQuesAllergyDataSaveFlag = true;
				}
				else{
					break;
				}
				//end Saving Pre-op Health Questionnaire Patient Allergy
				//Saving Pre-op Health Questionnaire Patient Medication
				$patientHealthQuesMedDataXML = $iolinkMainData[11];
				$tag = 'patientHealthQuesMedDataChild';
				$patientHealthQuesMed = array();
				$patientHealthQuesMed = XMLtoArray($patientHealthQuesMedDataXML,$tag);
				//print_r($patientHealthQuesMed);				
				$a = 0;
				$b = 0;
				while ($a < count($patientHealthQuesMed)) {						
					$prescription_medication_name 			= $patientHealthQuesMed[$a]['prescription_medication_name'];
					$prescription_medication_desc			= $patientHealthQuesMed[$a]['prescription_medication_desc'];
								
					$whereQueryHealthQuesMed = "";
					$selectQry = "select * from iolink_patient_prescription_medication where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and prescription_medication_name='$prescription_medication_name' and prescription_medication_desc='$prescription_medication_desc'";
					
					$rsSelectQry = imw_query($selectQry);
					$numRowSelectQry = imw_num_rows($rsSelectQry);						
					if($numRowSelectQry==0){					
						$insertQueryHealthQuesMed = "insert into iolink_patient_prescription_medication set ";					
					}
					else{					
						$insertQueryHealthQuesMed = "update iolink_patient_prescription_medication set ";
						$whereQueryHealthQuesMed = "where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and prescription_medication_name='$prescription_medication_name' and prescription_medication_desc='$prescription_medication_desc'";
					}
					$insertQueryHealthQuesMed .= "prescription_medication_name = '".addslashes($prescription_medication_name)."',
													prescription_medication_desc = '".addslashes($prescription_medication_desc)."',	
													patient_in_waiting_id = '".$demoPatientInWtId."',																								
													patient_id = '$demoPatientInstId'										
													";			
					$insertQueryHealthQuesMed .= $whereQueryHealthQuesMed;
					$insertQueryHealthQuesMedRsId = imw_query($insertQueryHealthQuesMed);						
					if($insertQueryHealthQuesMedRsId){
						$b++;
					}
					$a++;
					
				}
				if ($b == count($patientHealthQuesMed)){
					$patientHealthQuesMedDataSaveFlag = true;
				}
				else{
					break;
				}
				//end Saving Pre-op Health Questionnaire Patient Medication*/
				//aaaaaaaaaaa
				//Saving Pre-op Health Questionnaire Patient Allergy if data not containt in iolink (it is prectice allergy data)
				$selectQry = "select * from iolink_patient_allergy where patient_id = '$demoPatientInstId' and patient_in_waiting_id = '$demoPatientInWtId'";
				$rsSelectQry = imw_query($selectQry);
				$numRowSelectQry = imw_num_rows($rsSelectQry);						
				if($numRowSelectQry==0){					
					$patientPracAllergyDataXML = $iolinkMainData[12];
					$tag = 'pracPatientAlleryDataChild';
					$pracPatientAlleryData = array();
					$pracPatientAlleryData = XMLtoArray($patientPracAllergyDataXML,$tag);
					//print_r($patientAdminHealthQues);				
					$a = 0;
					$b = 0;
					while ($a < count($pracPatientAlleryData)) {						
						$allergy_name 			= $pracPatientAlleryData[$a]['title'];													
						$whereQueryHealthQuesAllergy = "";
						$selectQry = "select * from iolink_patient_allergy where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and allergy_name='$allergy_name'";
						$rsSelectQry = imw_query($selectQry);
						$numRowSelectQry = imw_num_rows($rsSelectQry);						
						if($numRowSelectQry==0){					
							$insertQueryHealthQuesAllergy = "insert into iolink_patient_allergy set ";					
						}
						else{					
							$insertQueryHealthQuesAllergy = "update iolink_patient_allergy set ";
							$whereQueryHealthQuesAllergy = "where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and allergy_name='$allergy_name'";
						}
						$insertQueryHealthQuesAllergy .= "allergy_name = '".addslashes($allergy_name)."',													
														patient_in_waiting_id = '".$demoPatientInWtId."',																								
														patient_id = '$demoPatientInstId'										
														";			
						$insertQueryHealthQuesAllergy .= $whereQueryHealthQuesAllergy;
						$insertQueryHealthQuesAllergyRsId = imw_query($insertQueryHealthQuesAllergy);						
						if($insertQueryHealthQuesAllergyRsId){
							$b++;
						}
						$a++;
						
					}
					if ($b == count($pracPatientAlleryData)){
						$patientHealthQuesAllergyDataSaveFlag = true;
					}
					else{
						break;
					}
				}
				//end Saving Pre-op Health Questionnaire Patient Allergy if data not containt in iolink (it is prectice allergy data)
				//aaaaaaaaaaaaaaaa
				//bbbbbbbbbbbbb
				//Saving Pre-op Health Questionnaire Patient Medication if data not containt in iolink (it is prectice allergy data)
				$selectQry = "select * from iolink_patient_prescription_medication where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId";
				$rsSelectQry = imw_query($selectQry);
				$numRowSelectQry = imw_num_rows($rsSelectQry);						
				if($numRowSelectQry==0){					
					$patientPracMedDataXML = $iolinkMainData[13];
					$tag = 'pracPatientMedDataChild';
					$pracPatientMedData = array();
					$pracPatientMedData = XMLtoArray($patientPracMedDataXML,$tag);
					//print_r($patientAdminHealthQues);				
					$a = 0;
					$b = 0;
					while ($a < count($pracPatientMedData)) {						
						$prescription_medication_name = $pracPatientMedData[$a]['title'];
						$prescription_medication_desc = $pracPatientMedData[$a]['destination'];
									
						$whereQueryHealthQuesMed = "";
						$selectQry = "select * from iolink_patient_prescription_medication where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and prescription_medication_name='$prescription_medication_name' and prescription_medication_desc='$prescription_medication_desc'";
						
						$rsSelectQry = imw_query($selectQry);
						$numRowSelectQry = imw_num_rows($rsSelectQry);						
						if($numRowSelectQry==0){					
							$insertQueryHealthQuesMed = "insert into iolink_patient_prescription_medication set ";					
						}
						else{					
							$insertQueryHealthQuesMed = "update iolink_patient_prescription_medication set ";
							$whereQueryHealthQuesMed = "where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and prescription_medication_name='$prescription_medication_name' and prescription_medication_desc='$prescription_medication_desc'";
						}
						$insertQueryHealthQuesMed .= "prescription_medication_name = '".addslashes($prescription_medication_name)."',
														prescription_medication_desc = '".addslashes($prescription_medication_desc)."',	
														patient_in_waiting_id = '".$demoPatientInWtId."',																								
														patient_id = '$demoPatientInstId'										
														";			
						$insertQueryHealthQuesMed .= $whereQueryHealthQuesMed;
						$insertQueryHealthQuesMedRsId = imw_query($insertQueryHealthQuesMed);						
						if($insertQueryHealthQuesMedRsId){
							$b++;
						}
						$a++;						
					}					
					if ($b == count($pracPatientMedData)){
						$patientHealthQuesMedDataSaveFlag = true;
					}
					else{
						break;
					}
				}
				//end Saving Pre-op Health Questionnaire Patient Allergy if data not containt in iolink (it is prectice allergy data)
				//Saving Oculer HX pdf --- Start
				$oculerHxPdfDataXML = $iolinkMainData[14];				
				//exit;
				$tag = 'oculerHxDocDataChild';
				$oculerHxDocData = array();
				$oculerHxDocData = XMLtoArray($oculerHxPdfDataXML,$tag);
				//print_r($oculerHxDocData);
				//exit;
				$a = 0;
				$b = 0;
				if(count($oculerHxDocData)){
					$httpfolderOculer =  realpath(dirname(__FILE__));				
					$iolinkPdfPathOculer = $httpfolderOculer."/admin/pdfFiles/";
					$imedicIolinkPdfPathOculer = $iolinkPdfPathOculer.$surgeonName;
					if(!is_dir($imedicIolinkPdfPathOculer)){		
						mkdir($imedicIolinkPdfPathOculer);
					}
				}
				while ($a < count($oculerHxDocData)) {			
					$pdfFilePath = "";
					$patient_id = "";
					$file_name = "";
					$oculer_doc_doc_data = "";
					$patient_id 			= $oculerHxDocData[$a]['patient_id'];
					$file_name 				= $oculerHxDocData[$a]['file_name'];						
					$oculer_doc_doc_data	= $oculerHxDocData[$a]['oculer_doc_doc_data'];						
										
					//$faceSheetPdfFileNameOld = 'faceSheet'.$demoPatientInstId.$demoPatientInWtId.'.pdf';
					$oculerHxPdfFileName = $file_name.'_'.$demoPatientInstId.'_'.$demoPatientInWtId.'.pdf';

					$oculerHxPutPdfFileName = $imedicIolinkPdfPath.'\\'.$oculerHxPdfFileName;				
					
					$oculerHxPutPdfDocdata = base64_decode($oculer_doc_doc_data);			
					
					$pdfFilePath = "pdfFiles".'/'.$surgeonName.'/'.$oculerHxPdfFileName;
					$iolink_scan_folder_name = "ocularHx";
					$whereQueryScanOculer = "";					
					$numRowSelectQryOculer=0;
					//$selectQryOld = "select * from iolink_scan_consent where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and document_name = '".addslashes($faceSheetPdfFileNameOld)."'";
					//$rsSelectQryOld = imw_query($selectQryOld);
					//$numRowSelectQryOld = imw_num_rows($rsSelectQryOld);
					//if($numRowSelectQryOld==0){	
						$selectQryNewOculer = "select * from iolink_scan_consent where patient_id = '$demoPatientInstId' and patient_in_waiting_id = '$demoPatientInWtId' and document_name = '".addslashes($oculerHxPdfFileName)."'";						
						$rsSelectQryNewOculer = imw_query($selectQryNewOculer);
						$numRowSelectQryOculer = imw_num_rows($rsSelectQryNewOculer);						
					//}
					if($numRowSelectQryOculer==0){					
						$insertQueryScanOculer = "insert into iolink_scan_consent set ";					
					}
					else{
						$insertQueryScanOculer = "update iolink_scan_consent set ";
						if($numRowSelectQryOculer>0){
							$whereQueryScanOculer = "where patient_id = '$demoPatientInstId' and patient_in_waiting_id = '$demoPatientInWtId' and document_name = '".addslashes($oculerHxPdfFileName)."'";
						}
						
					}
					$insertQueryScanOculer .= "patient_id = '".addslashes($demoPatientInstId)."',
 										 patient_in_waiting_id = '".addslashes($demoPatientInWtId)."',										 										 
										 document_name = '".addslashes($oculerHxPdfFileName)."',
										 image_type = 'application/pdf',		
										 pdfFilePath = '".addslashes($pdfFilePath)."',											 								 
										 iolink_scan_folder_name = '".addslashes($iolink_scan_folder_name)."'									
										";
					$insertQueryScanOculer .= $whereQueryScanOculer;					
					$insertQueryScanRsIdOculer = imw_query($insertQueryScanOculer);			
					if($insertQueryScanRsIdOculer){
						if($oculerHxPutPdfDocdata){														
							file_put_contents($oculerHxPutPdfFileName,$oculerHxPutPdfDocdata);							
						}
					}
					$a++;
					$b++;
				}
				if ($b == count($oculerHxDocData)){
					$oculerHxPdfDataSaveFlag = true;
				}
				else{
					break;
				}
				////////////////////
				//Saving Oculer HX pdf --- End
				//bbbbbbbbbbbbbb 
			}
			if($demographicDataSaveFlag === true && $patientInWatingDataSaveFlag === true
				&& $insurenceDataSaveFlag === true && $insurenceCaseSaveFlag === true && $InsurenceScanDocSaveFlag === true 
				&& $consentDataSaveFlag === true
				&& 	$consentSigDataSaveFlag === true && $scanDataSaveFlag === true
				&& 	$pdfDataSaveFlag === true && $faceSheetDataSaveFlag === true
				&& $patientPreOpHealthQuesDataSaveFlag === true
				&& $patientAdminHealthQuesDataSaveFlag === true
				&& $patientHealthQuesAllergyDataSaveFlag === true
				&& $patientHealthQuesMedDataSaveFlag === true
				&& $oculerHxPdfDataSaveFlag === true
				
			){
				//$demoPatientInWtId = $savedPatientInWtId;
				$response = $demoPatientInstId.'@@@@@'.$demoPatientInWtId.'@@@@@'.'1';
				//$msg = "Patient successfully sends to IOlink";
				//echo $response.$msg;
				echo $response;
			}
			else{
				$response = $demoPatientInstId.'@@@@@'.$demoPatientInWtId.'@@@@@'.'0';
				//$msg = "Error in sending patient to IOlink";
				//echo $response.$msg;
				echo $response;
				//echo "Error in sending patient to IOlink";
			}
			}
			else{
				if($demographicDataSaveFlag === true && $patientInWatingDataSaveFlag === true
				&& $insurenceDataSaveFlag === true && $insurenceCaseSaveFlag === true && $InsurenceScanDocSaveFlag === true
				&& $consentDataSaveFlag === true
				&& 	$consentSigDataSaveFlag === true && $scanDataSaveFlag === true
				&& 	$pdfDataSaveFlag === true && $faceSheetDataSaveFlag === true
				&& $patientPreOpHealthQuesDataSaveFlag === true
				&& $patientAdminHealthQuesDataSaveFlag === true
				&& $patientHealthQuesAllergyDataSaveFlag === true
				&& $patientHealthQuesMedDataSaveFlag === true
				&& $oculerHxPdfDataSaveFlag === true
				){
					//$demoPatientInWtId = $savedPatientInWtId;
					$response = $demoPatientInstId.'@@@@@'.$demoPatientInWtId.'@@@@@'.'1';
					//$msg = "Patient successfully sends to IOlink";
					//echo $response.$msg;
					echo $response;
				}
				else{
					$response = $demoPatientInstId.'@@@@@'.$demoPatientInWtId.'@@@@@'.'0';
					//$msg = "Error in sending patient to IOlink";
					//echo $response.$msg;
					echo $response;
					//echo "Error in sending patient to IOlink";
				}
			}
		}
		elseif($mode == "remove"){
			$qryDelIOlinkPatientdetail = "delete from patient_data_tbl where patient_id = ".$ioPtId;
			//$rsDelIOlinkPatientdetail = imw_query($qryDelIOlinkPatientdetail);
			$rsDelIOlinkPatientdetail = 1;
			if($rsDelIOlinkPatientdetail){
				$qryDelIOlinkPatientWtdetail = "delete from patient_in_waiting_tbl where patient_id = ".$ioPtId." and patient_in_waiting_id = ".$ioPtWtId;
				$rsDelIOlinkPatientWtdetail = imw_query($qryDelIOlinkPatientWtdetail);
				if($rsDelIOlinkPatientWtdetail){
					$qryDelIOlinkPatientConsentDetail = "delete from iolink_consent_filled_form where patient_id = ".$ioPtId." and fldPatientWaitingId = ".$ioPtWtId;
					$rsDelIOlinkPatientConsentDetail = imw_query($qryDelIOlinkPatientConsentDetail);
					
					
					$qryDelIOlinkPatientInsurenceDetail = "delete from insurance_data where patient_id = ".$ioPtId." and waiting_id = ".$ioPtWtId;
					$rsDelIOlinkPatientInsurenceDetail = imw_query($qryDelIOlinkPatientInsurenceDetail);

					$qryDelIOlinkPatientInsurenceCaseDetail = "delete from iolink_insurance_case where patient_id = ".$ioPtId." and waiting_id = ".$ioPtWtId;
					$rsDelIOlinkPatientInsurenceCaseDetail = imw_query($qryDelIOlinkPatientInsurenceCaseDetail);

					//get sig image path
					$qryGetIOlinkPatientConsentSigDetail = "select signature_image_path from iolink_consent_form_signature where patient_id = ".$ioPtId." and patient_in_waiting_id = ".$ioPtWtId;
					$rsGetIOlinkPatientConsentSigDetail = imw_query($qryGetIOlinkPatientConsentSigDetail);
					while($rowGetIOlinkPatientConsentSigDetail = imw_fetch_array($rsGetIOlinkPatientConsentSigDetail)){
						unlink($rowGetIOlinkPatientConsentSigDetail[signature_image_path]);
					}
					//
					$qryDelIOlinkPatientConsentSigDetail = "delete from iolink_consent_form_signature where patient_id = ".$ioPtId." and patient_in_waiting_id = ".$ioPtWtId;
					$rsDelIOlinkPatientConsentSigDetail = imw_query($qryDelIOlinkPatientConsentSigDetail);
					
					$qryGetIOlinkPatientPdfpath = "select pdfFilePath from iolink_scan_consent where patient_id = ".$ioPtId." and patient_in_waiting_id = ".$ioPtWtId." and image_type = 'application/pdf'";
					$rsGetIOlinkPatientPdfpath = imw_query($qryGetIOlinkPatientPdfpath);
					while($rowGetIOlinkPatientPdfpath = imw_fetch_array($rsGetIOlinkPatientPdfpath)){						
						$httpfolder =  realpath(dirname(__FILE__));				
						$deletePath = $httpfolder."/admin/".$rowGetIOlinkPatientPdfpath[pdfFilePath];
						unlink($deletePath);
					}					
					$qryDelIOlinkScanDetail = "delete from iolink_scan_consent where patient_id = ".$ioPtId." and patient_in_waiting_id = ".$ioPtWtId;
					$rsDelIOlinkScanDetail = imw_query($qryDelIOlinkScanDetail);
					
					$qryGetIOlinkPatientPreOpSigPath = "select patient_sign_image_path,witness_sign_image_path from iolink_preophealthquestionnaire where patient_id = ".$ioPtId." and patient_in_waiting_id = ".$ioPtWtId;
					$rsGetIOlinkPatientPreOpSigPath = imw_query($qryGetIOlinkPatientPreOpSigPath);
					while($rowGetIOlinkPatientPreOpSigPath = imw_fetch_array($rsGetIOlinkPatientPreOpSigPath)){						
						$httpfolder =  realpath(dirname(__FILE__));				
						$patientPreOpSigPath = $rowGetIOlinkPatientPreOpSigPath[patient_sign_image_path];
						$witnessPreOpSigPath = $rowGetIOlinkPatientPreOpSigPath[witness_sign_image_path];
						if(trim($patientPreOpSigPath) != ""){
							$patientdeletePath = $httpfolder."/".trim($patientPreOpSigPath);
							unlink($patientdeletePath);
						}	
						if(trim($witnessPreOpSigPath) != ""){
							$witnessdeletePath = $httpfolder."/".trim($witnessPreOpSigPath);
							unlink($witnessdeletePath);
						}						
					}	

					$qryDelPatientPreOpDetail = "delete from iolink_preophealthquestionnaire where patient_id = ".$ioPtId." and patient_in_waiting_id = ".$ioPtWtId;
					$rsDelIPatientPreOpDetail = imw_query($qryDelPatientPreOpDetail);
					
					$qryDelPatientPreOpAdminDetail = "delete from iolink_healthquestionadmin where patient_id = ".$ioPtId." and patient_in_waiting_id = ".$ioPtWtId;
					$rsDelIPatientPreOpAdminDetail = imw_query($qryDelPatientPreOpAdminDetail);
					
					$qryDelPatientAllergDetail = "delete from iolink_patient_allergy where patient_id = ".$ioPtId." and patient_in_waiting_id = ".$ioPtWtId;
					$rsDelPatientAllergDetail = imw_query($qryDelPatientAllergDetail);
					
					$qryDelPatientMedicationDetail = "delete from iolink_patient_prescription_medication where patient_id = ".$ioPtId." and patient_in_waiting_id = ".$ioPtWtId;
					$rsDelPatientMedicationDetail = imw_query($qryDelPatientMedicationDetail);
					
					
					echo $rsDelIOlinkPatientdetail.'@@@@@'.$rsDelIOlinkPatientWtdetail;
				}
			}
		}
	}
}

function XMLtoArray($xml,$tag){
	$parser = xml_parser_create('ISO-8859-1'); // For Latin-1 charset
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); // Dont mess with my cAsE sEtTings
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); // Dont bother with empty info
	xml_parse_into_struct($parser, $xml, $values);
	xml_parser_free($parser);
  
	$return = array(); // The returned array
	$stack = array(); // tmp array used for stacking
	$arr = array();
	$arrMain = array();
	$c=0;
	$flag = false;

	foreach($values as $key => $val){
	////////////Code To Make Rates Found Array 
	if(($val["tag"] ==$tag)){
		if($val["type"] == "open"){ 
			$flag = true;
			$arr = array();
		}
		if($val["type"] == "close"){ 
			$flag = false;
			$arrMain[$c++] = $arr;
			unset($arr); 
		}
	}

	if(($flag == true)&&($val["type"]=="complete")){
		if(isset($val["value"]) && !empty($val["value"])){
			$arr[$val["tag"]] = $val["value"];
		}
	}	
	//echo("<textarea>$imageAndTop $theOutput</textarea>");
	}
	return $arrMain;
}

?>