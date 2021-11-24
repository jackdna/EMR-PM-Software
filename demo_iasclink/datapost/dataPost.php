<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
set_time_limit(180);
include_once("../common/conDb.php");
include_once("../common/commonFunctions.php");
include_once("../admin/classObjectFunction.php");

$objManageData 		= new manageData;
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
//start get head_quarter facility_id and associated iASC facility_id from facility_tbl table
$getFacilityDetails = $objManageData->getRowRecord('facility_tbl', 'fac_head_quater', '1');
if($getFacilityDetails) {
	$headQuarterIascFacId 	= $getFacilityDetails->fac_idoc_link_id;
}
//end get head_quarter facility_id and associated iASC facility_id from facility_tbl table
//START FUNCTION FOR INS-SCAN
function putInsImgContent($scan_card,$insurenceDataScanContent,$demoPtId) {
	$scanCardPath='';
	if($scan_card) {
		$scan_cardExplode = explode('/',$scan_card);
		$httpfolder =  realpath(dirname(__FILE__));
		$iolinkInsScanFolder = $httpfolder."/../imedic_uploaddir/PatientId_".$demoPtId;
		if(!is_dir($iolinkInsScanFolder)){		
			mkdir($iolinkInsScanFolder);
		}
		$scanCardPath = "/PatientId_".$demoPtId.'/'.$scan_cardExplode[2];
		$inScanPutPdfFileName = $iolinkInsScanFolder.'/'.$scan_cardExplode[2];
		file_put_contents($inScanPutPdfFileName,$insurenceDataScanContent);
	}
	return $scanCardPath;
}	
//END FUNCTION FOR INS-SCAN

//START FUNCTION FOR INS-SCAN
function putPtImgContent($ptImageName,$ptImageContent,$demoPtId) {
	global $surgeryCenterDirectoryName;
	global $iolinkDirectoryName;
	global $rootServerPath;
	
	$ptImagePath='';
	if($ptImageName) {
		$ptImageNameExplode = explode('/',$ptImageName);
		$ptImagePath 		= 'pdfFiles/patient_images/patient_id_'.$demoPtId.'_'.$ptImageNameExplode[2];
		$ptImageDirName 	= 'admin/pdfFiles/patient_images';
		$ptImageNameNew 	= 'patient_id_'.$demoPtId.'_'.$ptImageNameExplode[2];
		
		//START ADD PHOTO IN SURGERYCENTER
		$ptScemrImageFolder = $rootServerPath.'/'.$surgeryCenterDirectoryName.'/'.$ptImageDirName;
		if(!is_dir($ptScemrImageFolder)){		
			mkdir($ptScemrImageFolder);
		}
		$ptImageScemrPutPdfFileName = $ptScemrImageFolder.'/'.$ptImageNameNew;
		file_put_contents($ptImageScemrPutPdfFileName,$ptImageContent);
		//END ADD PHOTO IN SURGERYCENTER
		
		//START ADD PHOTO IN IOLINK
		$ptiOLinkImageFolder = $rootServerPath.'/'.$iolinkDirectoryName.'/'.$ptImageDirName;
		if(!is_dir($ptiOLinkImageFolder)){		
			mkdir($ptiOLinkImageFolder);
		}
		$ptImageIolinkPutPdfFileName = $ptiOLinkImageFolder.'/'.$ptImageNameNew;
		file_put_contents($ptImageIolinkPutPdfFileName,$ptImageContent);
		//END ADD PHOTO IN IOLINK
		
	}
	return $ptImagePath;
}	
//END FUNCTION FOR INS-SCAN

extract($_REQUEST);

$userName = $_REQUEST['userName'];
$password = $_REQUEST['password'];
$downloadForm = $_REQUEST['downloadForm'];
$iolinkSync = $_REQUEST['iolinkSync'];
$mode = $_REQUEST['mode'];
$schedule_id = $_REQUEST['schedule_id'];
$sa_date = $_REQUEST['sa_date'];
$ioPtId = $_REQUEST['ioPtId'];
$ioPtWtId = $_REQUEST['ioPtWtId'];
$iAscSyncWtId = $_REQUEST['iAscSyncWtId'];
$myAddress = $_REQUEST['myAddress'];
$myAddressNew = $_REQUEST['myAddressNew'];
$idocIascSame = $_REQUEST['idocIascSame'];
$chkDtTime = $_REQUEST['chk_dt_time'];
//if(!$constantImwFacilityId) {$constantImwFacilityId='1';}
if(!trim($ioPtWtId) && trim($iAscSyncWtId)) {
	$ioPtWtId = trim($iAscSyncWtId);	
}
if(trim($_SESSION['iolink_iasc_facility_id'])) {
	$imwFacilityIdNew = $_SESSION['iolink_iasc_facility_id'];
}
if(!$imwFacilityIdNew) {
	$imwFacilityIdNew = $headQuarterIascFacId;
}
if(!$imwFacilityIdNew) {
	$imwFacilityIdNew = '1';
}
	
if(!$constantImwSlotMinute) { $constantImwSlotMinute='5';}
function getiAscSlotCount($usrFname,$usrMname,$usrLname,$dos,$defaultSlotTime,$imwFacilityId,$syncStatus='',$usrNpi) {
	$slotCount = "";
	$imedicFileName = '../connect_imwemr.php';
	if($syncStatus=='iosync') {
		include_once($imedicFileName);
	}else {
		include($imedicFileName);
	}
	$andUsrQry = " AND  fname='".addslashes($usrFname)."' AND mname='".addslashes($usrMname)."' AND lname='".addslashes($usrLname)."' ";
	if(constant('CHECK_USER_NPI')=='YES') {
		$andUsrQry 			= " AND user_npi = '".$usrNpi."' AND user_npi != '' AND user_npi != '0' ";
	}
	$usrQry = "SELECT id FROM users WHERE delete_status = '0' AND user_type = '1' ".$andUsrQry." ORDER BY id DESC LIMIT 0,1";
	$usrRes = imw_query($usrQry) or $msgInfo[] = $usrQry.imw_error();
	if(imw_num_rows($usrRes)>0) {
		$usrRow = imw_fetch_array($usrRes);
		$usr_id = $usrRow["id"];
		$slotCount = getiAscSlotCountNew($dos,$defaultSlotTime,$usr_id,$imwFacilityId=0);
	}
	imw_close($link_imwemr);
	$scEMRFileName = '../common/conDb.php';
	if($syncStatus=='iosync') {
		include_once($scEMRFileName);
	}else {
		include($scEMRFileName);
	}
	return $slotCount;
}
function getiAscSlotCountNew($dos,$defaultSlotTime,$usr_id,$imwFacilityId=0) {
	// $dos format should be YYYY-MM-DD
	if($dos == "" || $defaultSlotTime == "" || $usr_id == "" || $usr_id == 0)
	{
		return 0;	
		// for incorrect data or user not match
	}
	
	$arr_prov_sch = get_provider_schedules($dos, array(0 => $usr_id));	
	
	$arr_final_tmp = array();
	$arr_sch_tmp_id = array();
	$sch_tmp_avail_ids_us_h = array();
	$tmp_max_appointments_arr_h = array();
				
	for($i = 0; $i < count($arr_prov_sch); $i++){						
		if($imwFacilityId != 0 && $imwFacilityId != "")
		{
			// Facility is available
			if($arr_prov_sch[$i]["facility"] == $imwFacilityId)
			{
				$arr_sch_tmp_id[] = $arr_prov_sch[$i]["sch_tmp_id"];
			}
		}
		else
		{
			// All facility is choosen
			$arr_sch_tmp_id[] = $arr_prov_sch[$i]["sch_tmp_id"];
		}				
	}
	
	if(count($arr_sch_tmp_id) == 0)
	{
		return 0;
		// Provider has no schedule in the requested facility	
	}
	
	$arr_sch_tmp_ids_data = implode(',',$arr_sch_tmp_id);
	
	//$sch_template_timing_qry = "SELECT sum( TIMESTAMPDIFF(MINUTE , `morning_start_time` , `morning_end_time` ) )  - sum( TIMESTAMPDIFF(MINUTE , `fldLunchStTm` , `fldLunchEdTm` ) ) AS tmp_min FROM `schedule_templates` WHERE id IN(".$arr_sch_tmp_ids_data.")";
	$sch_template_timing_qry = "SELECT sum( TIMESTAMPDIFF(MINUTE , concat(UTC_DATE(),' ',morning_start_time) , concat(UTC_DATE(),' ',morning_end_time)) )  - sum( TIMESTAMPDIFF(MINUTE , concat(UTC_DATE(),' ',fldLunchStTm) , concat(UTC_DATE(),' ',fldLunchEdTm))) AS tmp_min FROM `schedule_templates` WHERE id IN(".$arr_sch_tmp_ids_data.")";	
	$sch_template_timing_qry_obj = imw_query($sch_template_timing_qry) or $msgInfo[] = $sch_template_timing_qry.imw_error();
	$sch_template_timing_qry_data = imw_fetch_assoc($sch_template_timing_qry_obj);		
	$total_available_time_min = $sch_template_timing_qry_data["tmp_min"];
	$total_slots_count = $total_available_time_min/$defaultSlotTime;
	$total_slots = intval($total_slots_count,10);
	return $total_slots;
}
function get_provider_schedules($wd, $ap = array()){
	//variable declarations
	$pr = false;	$wno = $dno = 0;	$ar_wd = $arr_sch = $arr_del_sch = $arr_sch_tmp = $arr_sch2 = array();	$q = $r = $str_sch = "";

	//selected provider
	if(count($ap) > 0){	$pr = "(".implode("','", $ap).")";	}

	//calculating week day no and week no
	$ar_wd = explode("-", $wd);	$wno = ceil($ar_wd[2] / 7);	$dno = date("w", mktime(0, 0, 0, $ar_wd[1], $ar_wd[2], $ar_wd[0]));	if($dno == 0) $dno = 7;
	
	//quering provider schedules
	$q = "select id, del_status, delete_row, status, provider, facility, today_date, sch_tmp_id from provider_schedule_tmp where today_date <= '".$wd."' and week".$wno." = '".$dno."' ";
	if($pr != false){	$q .= "and provider_schedule_tmp.provider IN ".$pr." ";	}
	$q .= "order by provider, facility, sch_tmp_id, today_date";
	$r = imw_query($q) or $msgInfo[] = $q.imw_error();
	if(imw_num_rows($r) > 0){
		while($arr_record = imw_fetch_assoc($r))
		{
			$arr_sch[] = $arr_record;
		}			
		$arr_sch_tmp = $arr_sch;
		for($i = 0; $i < count($arr_sch_tmp); $i++){
			//removing deleted schedules
			if($arr_sch_tmp[$i]["del_status"] == 1){
				$arr_del_sch[] = $arr_sch_tmp[$i];
				unset($arr_sch[$i]);
			}
		}
	}
	if(count($arr_sch)>0) $arr_sch = array_values($arr_sch);
	if(count($arr_del_sch)>0) $arr_del_sch = array_values($arr_del_sch);
	
	//removing shcedules which have been deleted for future
	$arr_sch_tmp = $arr_sch;
	if(count($arr_del_sch)>0){
		for($j = 0; $j < count($arr_del_sch); $j++){
			for($k = 0; $k < count($arr_sch_tmp); $k++){
				if(strtolower($arr_del_sch[$j]["delete_row"]) == "all"){
					if($arr_del_sch[$j]["provider"] == $arr_sch_tmp[$k]["provider"] && $arr_del_sch[$j]["facility"] == $arr_sch_tmp[$k]["facility"] && $arr_del_sch[$j]["sch_tmp_id"] == $arr_sch_tmp[$k]["sch_tmp_id"] && strtotime($arr_del_sch[$j]["today_date"]) >= strtotime($arr_sch_tmp[$k]["today_date"])){							
						unset($arr_sch[$k]);
					}
				}
				if(strtolower($arr_del_sch[$j]["delete_row"]) == "no"){
					if($arr_del_sch[$j]["provider"] == $arr_sch_tmp[$k]["provider"] && $arr_del_sch[$j]["facility"] == $arr_sch_tmp[$k]["facility"] && $arr_del_sch[$j]["sch_tmp_id"] == $arr_sch_tmp[$k]["sch_tmp_id"] && strtotime($arr_del_sch[$j]["today_date"]) == strtotime($wd)){							
						unset($arr_sch[$k]);
					}
				}
			}
		}
	}
	if(count($arr_sch)>0) $arr_sch = array_values($arr_sch);
	if(count($arr_del_sch)>0) $arr_del_sch = array_values($arr_del_sch);

	//removing schedules which were created for a single day earlier than the sought date
	$arr_sch_tmp = $arr_sch;
	if(count($arr_sch_tmp)>0){	
		for($i = 0; $i < count($arr_sch_tmp); $i++){
			if(strtotime($arr_sch_tmp[$i]["today_date"]) < strtotime($wd) && strtolower($arr_sch_tmp[$i]["status"]) == "no"){
				$arr_del_sch[] = $arr_sch_tmp[$i];					
				unset($arr_sch[$i]);
			}
		}
	}
	if(count($arr_sch)>0) $arr_sch = array_values($arr_sch);
	if(count($arr_del_sch)>0) $arr_del_sch = array_values($arr_del_sch);
	
	//removing duplicate records if any
	if(count($arr_sch)>0){
		$arr_sch_tmp = array();	//resetting array
		for($i = 0; $i < count($arr_sch); $i++){
			$arr_sch_tmp[] = $arr_sch[$i]["id"];
		}
		$str_sch = join(',', $arr_sch_tmp);
		$q = "select id, facility , provider, sch_tmp_id, today_date from provider_schedule_tmp where id in (".$str_sch.") ";
		if($pr != false){	$q .= "and provider_schedule_tmp.provider IN ".$pr." ";	}
		$q .= "order by provider, facility, sch_tmp_id, today_date";
		$r = imw_query($q) or $msgInfo[] = $q.imw_error();
		if(imw_num_rows($r) > 0){
			while($arr_record = imw_fetch_assoc($r))
			{
				$arr_sch2[] = $arr_record;
			}				
			//$arr_sch2 = $r->GetArray();
			$arr_sch3 = $arr_sch2;
			for($n = 0; $n < count($arr_sch2); $n++){
				if($arr_sch2[$n]['sch_tmp_id'] == $arr_sch2[$n+1]['sch_tmp_id'] && $arr_sch2[$n]['facility'] == $arr_sch2[$n+1]['facility'] && $arr_sch2[$n]['provider'] == $arr_sch2[$n+1]['provider']){
					$arr_del_sch[] = $arr_sch2[$n];

					unset($arr_sch3[$n]);
				}
			}
		}			
	}
	$arr_sch = $arr_sch3;
	if(count($arr_sch)>0) $arr_sch = array_values($arr_sch);
	if(count($arr_del_sch)>0) $arr_del_sch = array_values($arr_del_sch);

	//unsetting variables
	unset($pr, $wno, $dno, $ar_wd, $arr_del_sch, $arr_sch_tmp, $arr_sch2, $q, $r, $str_sch);

	return $arr_sch;
}	
function getDocumentsNotExpire($document_save_date_time,$dos,$maximumDaysToExpire) {
	$documentsNotExpire 	= "yes";
	$dos_days				= strtotime($dos);
	$documentSaveDays 		= strtotime($document_save_date_time);
	if($documentSaveDays!="" && intval($documentSaveDays)<intval($dos_days)){
		$documentDaysDiffrence=ceil((intval($dos_days - $documentSaveDays ))/(60*60*24));
		if(intval($documentDaysDiffrence)>$maximumDaysToExpire){
			$documentsNotExpire = "";
		}
	}
	return $documentsNotExpire;
}
	
if($downloadForm==""){
	$userName = $_REQUEST['userName'];
	$password = $_REQUEST['password'];
	
	//Encrypted Password	
	$imw_passQry = imw_query("select PASSWORD('$password')") or $msgInfo[] = imw_error();
	$imw_passRow = imw_fetch_array($imw_passQry);
	$password = $imw_passRow['0'];
	//Encrypted Password
	
	// 
	//$maxRecentlyUsedPass = getData("maxRecentlyUsedPass", "surgerycenter", "surgeryCenterId", '1');
	//$maxLoginAttempts = getData("maxLoginAttempts", "surgerycenter", "surgeryCenterId", '1');
	//
	
	$getLoginDetailsStr = "SELECT * FROM users WHERE loginName = '$userName' AND user_password = '$password' AND deleteStatus <> 'Yes'";
	$getLoginDetailsQry = imw_query($getLoginDetailsStr) or $msgInfo[] = $getLoginDetailsStr.imw_error();
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
	$imw_passQry = imw_query("select PASSWORD('$password')") or $msgInfo[] = imw_error();
	$imw_passRow = imw_fetch_array($imw_passQry);
	$password = $imw_passRow['0'];
	//Encrypted Password
	$getLoginDetailsStr = "SELECT * FROM users WHERE loginName = '$userName' AND user_password = '$password' AND deleteStatus <> 'Yes'";
	$getLoginDetailsQry = imw_query($getLoginDetailsStr) or $msgInfo[] = $getLoginDetailsStr.imw_error();
	$getLoginRowCount = imw_num_rows($getLoginDetailsQry);
	if($getLoginRowCount>0){	
		if($wishToDownLoad == 1){			
			//export start
			$querygGetAllConsentCotegory = "SELECT * FROM consent_category";
			$rsQuerygGetAllConsentCotegory = imw_query($querygGetAllConsentCotegory) or $msgInfo[] = $querygGetAllConsentCotegory.imw_error();
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
						$cat_name123 = $row['category_name'];

						$myXml .= "<categoryname><![CDATA[$cat_name123]]></categoryname>";

						//$myXml .= "<categoryname><![CDATA[$cat_name123]></categoryname>";						
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
			$rsQuerygGetAllConsentForm = imw_query($querygGetAllConsentForm) or $msgInfo[] = $querygGetAllConsentForm.imw_error();
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
			$rsQuerygGetHealthQuestioner = imw_query($querygGetHealthQuestioner) or $msgInfo[] = $querygGetHealthQuestioner.imw_error();
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
				else $healthQuestionerXml = "No Record found";
			}			
			echo $healthQuestionerXml;	
		}
		elseif($wishToDownLoad == 3){						
			//export start
			$hpQuesXml = "";
			$queryGet = "SELECT * FROM predefine_history_physical";
			$rsQueryGet = imw_query($queryGet) or $msgInfo[] = $queryGet.imw_error();
			if(!$rsQueryGet){
				echo ("Error : ". imw_error()."<br>".$queryGet);
			}
			else{
				$hpQues = array();
				$a = 0;			
				if(imw_num_rows($rsQueryGet)>0){		
					$aa = "?>";
					$hpQuesXml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"$aa";
					$hpQuesXml .= "<historyphysical>";
					while ($row = imw_fetch_array($rsQueryGet)) {
						//echo '<pre>';
						//print_r($row); 
						$hpQuesXml .= "<historyphysicalchild>";						
						$hpQuesXml .= "<historyPhysicalId>".$row['id']."</historyPhysicalId>";						
						$question = addslashes($row['name']);
						$hpQuesXml .= "<historyPhysicalQues><![CDATA[$question]]></historyPhysicalQues>";					
						$hpQuesXml .= "<historyPhysicalStatus>".$row['deleted']."</historyPhysicalStatus>";					
						$hpQuesXml .= "</historyphysicalchild>";
						$a++;						
					}
					$hpQuesXml .= "</historyPhysical>";
				}
				else $hpQuesXml = "No Record found";
			}			
			echo $hpQuesXml;	
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
	$imw_passQry = imw_query("select PASSWORD('$password')") or $msgInfo[] = imw_error();
	$imw_passRow = imw_fetch_array($imw_passQry);
	$password = $imw_passRow['0'];
	//Encrypted Password
	$getLoginDetailsStr = "SELECT * FROM users WHERE loginName = '$userName' AND user_password = '$password' AND deleteStatus <> 'Yes'";
	$getLoginDetailsQry = imw_query($getLoginDetailsStr) or $msgInfo[] = $getLoginDetailsStr.imw_error();
	$getLoginRowCount = imw_num_rows($getLoginDetailsQry);
	if($getLoginRowCount>0){				
	/////////////
		if($mode == "send" || $mode == "resync" || $mode == "ioBookSheet"){		
			$cur = curl_init();
			//$url = $myAddress."/addons/iOLink/getIolinkXMLData.php?login=$login&pass=$pass&dbase=$dbase&mode=$mode&schedule_id=$schedule_id&sa_date=$sa_date";
			$url = $myAddress."/addons/iOLink/getIolinkXMLData.php";
			if($myAddressNew) {
				$url = $myAddressNew."/getIolinkXMLData.php";	
			}
			
			$postArr=array();
			$postArr['mode']		= $mode;
			$postArr['schedule_id']	= $schedule_id;
			$postArr['sa_date']		= $sa_date;//print_r($postArr);echo $url;die;
			
			curl_setopt($cur,CURLOPT_URL,$url);
			curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
			curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($cur, CURLOPT_POSTFIELDS,$postArr);  
			$data = curl_exec($cur);
			if (curl_errno($cur)){
				$msgInfo[] =  "Curl Error iOLink to iDOC: " . curl_error($cur);
			} 
			curl_close($cur);	
			$iolinkMainData = array();
			$iolinkMainData = explode('~~~~~~~',$data);
			//echo $iolinkMainData[14];
			//echo $data;die;
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
			$patientAllergyNoValueDataSaveFlag = false;
			$patientHealthQuesMedDataSaveFlag = false;
			$oculerHxPdfDataSaveFlag = false;
			$historyPhysicalPdfDataSaveFlag = false;
			$patientHistoryPhysicalDataSaveFlag=false;
			$patientHPQuesDataSaveFlag = false;
			$patientSxAlertDataSaveFlag = false;
			$aScanPdfDataSaveFlag = false;
			$iolMasterPdfDataSaveFlag = false;
			$iolMasterDataSaveFlag = false;
			$genHealthPdfDataSaveFlag = false;
			$sxPdfDataSaveFlag = false;
			$boolBookAppt = true;
			
			$procNameArr = $procAliasArr = $procCatArr = $procInjMiscArr = array();
			$procQry = "SELECT pr.`name` AS procName,pr.procedureAlias,pr.catId AS procCatId,pc.* 
						FROM procedures pr 
						LEFT JOIN procedurescategory pc ON(pc.proceduresCategoryId = pr.catId) 
						WHERE pr.del_status != 'yes'
						ORDER BY pr.`name` ";
			$procRes = imw_query($procQry) or $msgInfo[] = $procQry.imw_error();			
			if(imw_num_rows($procRes)>0) {
				while($procRow = imw_fetch_array($procRes)) {
					$procName = trim($procRow["procName"]);
					$procAlias = trim($procRow["procedureAlias"]);
					$procCatId = $procRow["procCatId"];
					$procNameArr[$procName] = $procName;
					$procAliasArr[$procName] = $procAlias;
					$procCatArr[$procName] 					= $procCatId;
					$procInjMiscArr[$procCatId]["Misc"] 	= $procRow["isMisc"];
					$procInjMiscArr[$procCatId]["Inj"] 		= $procRow["isInj"];
				}
			}
			
			//START CODE TO GET NPI OF SURGEON
			$surgeonArr = $npiArr = array();
			$surgeonQry 				= "SELECT usersId,npi,fname,mname,lname FROM users WHERE deleteStatus!='Yes' AND user_type='Surgeon' ORDER BY usersId";
			$surgeonRes 				= imw_query($surgeonQry) or die($surgeonQry.imw_error());
			$surgeonNumRow 				= imw_num_rows($surgeonRes);
			$sa_doctor_id				= "";
			if($surgeonNumRow>0) {
				while($surgeonRow 		= imw_fetch_array($surgeonRes)) {
					$surgnNpi			= trim($surgeonRow['npi']);
					if($surgnNpi) {
						$npiArr[]		= $surgnNpi;
						$surgeonArr[$surgnNpi]['fname'] = $surgeonRow['fname'];
						$surgeonArr[$surgnNpi]['mname'] = $surgeonRow['mname'];
						$surgeonArr[$surgnNpi]['lname'] = $surgeonRow['lname'];
					}
				}
				
			}
			//END CODE TO GET NPI OF SURGEON			
			
			//GET EXPIRY DAYS OF OLD DOCUMENTS
			$old_days_count = intval(90);
			$expireDaysQry = "SELECT * FROM surgerycenter WHERE surgeryCenterId = '1' LIMIT 0,1";
			$expireDaysRes = imw_query($expireDaysQry) or die(imw_error());
			if(imw_num_rows($expireDaysRes)>0) {
				$expireDaysRow = imw_fetch_assoc($expireDaysRes);
				if(trim($expireDaysRow["documentsExpireDays"])) {
					$old_days_count = intval($expireDaysRow["documentsExpireDays"]);	
				}
			}
			//GET EXPIRY DAYS OF OLD DOCUMENTS
			
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
				$language				= $demographicdata[$a]['language'];
				$race					= $demographicdata[$a]['race'];
				$ethnicity				= $demographicdata[$a]['ethnicity'];
				$religion				= $demographicdata[$a]['religion'];
				$phone_cell		 		= $demographicdata[$a]['phone_cell'];
				$preferr_contact		= $demographicdata[$a]['preferr_contact'];
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
				$p_imagename_content	= $demographicdata[$a]['p_imagename_content'];
				$p_imagename_content	= base64_decode($p_imagename_content);
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
				
				//START CODE TO REPLACE PREFERRED CONTACT IN HOME PHONE
				if($preferr_contact == '1' && trim($phone_biz)) {
					$phone_home = $phone_biz;	
				}else if($preferr_contact == '2' && trim($phone_cell)) {
					$phone_home = $phone_cell;	
				}
				//END CODE TO REPLACE PREFERRED CONTACT IN HOME PHONE
				
				if($sex=="Male"){
					$sex="m";
				}
				elseif($sex=="Female"){
					$sex="f";
				}
				$selctNumRow = 0;
				$addImwptIdQry = "";
				if($idocIascSame=="yes") {
					$pid = trim($pid);
					if($pid) {
						$selctQuery = "SELECT patient_id FROM patient_data_tbl WHERE imwPatientId = '".$pid."' and imwPatientId!=''";
						$selctRes = imw_query($selctQuery) or $msgInfo[] = $selctQuery.imw_error();	
						$selctNumRow = imw_num_rows($selctRes);
						$addImwptIdQry = " imwPatientId	= '".$pid."', ";
					}
				}
				if($selctNumRow<=0) {
					$selctQuery = "select patient_id from patient_data_tbl where 
									patient_fname 		= '".addslashes($fname)."'
									and patient_lname 	= '".addslashes($lname)."'
									and date_of_birth 	= '".$DOB."'
									and zip 			= '".$postal_code."'
									ORDER BY patient_id
									";
					$selctRes = imw_query($selctQuery) or $msgInfo[] = $selctQuery.imw_error();			
				}
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
												workPhone 		= '$phone_biz',
												language 		= '".addslashes($language)."',
												race 			= '".addslashes($race)."',
												religion 		= '".addslashes($religion)."',
												".$addImwptIdQry."
												ethnicity 		= '".addslashes($ethnicity)."'
												
											";
					$insertQueryDemograpic1 .= $whereQueryDemograpic1;		
								
					$insertQueryDemograpicRsId1 = imw_query($insertQueryDemograpic1) or $msgInfo[] = $insertQueryDemograpic1.imw_error();	
					if(!$demoPatientInstId){
						$demoPatientInstId = imw_insert_id();
					}
					//START ADD PATIENT IMAGE
						$patient_image_path_new = putPtImgContent($p_imagename,$p_imagename_content,$demoPatientInstId); 
						if(trim($patient_image_path_new)) {
							$updtPtImgQry = "UPDATE patient_data_tbl SET patient_image_path = '".$patient_image_path_new."' WHERE patient_id =".$demoPatientInstId;
							$updtPtImgRes = imw_query($updtPtImgQry) or $msgInfo[] = $updtPtImgQry.imw_error();	
						}
					//END ADD PATIENT IMAGE
				$a++;
				$b++;
			}
			if ($b == count($demographicdata)){
				$demographicDataSaveFlag = true;
			}
			else{
				//break;
			}
			$proc_cat_id  = "";
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
					$surgeon_npi				= $patientInWatingData[$a]['surgeon_npi'];
					$surgery_time				= $patientInWatingData[$a]['surgery_time'];
					$iasc_facility_id	 		= $patientInWatingData[$a]['iasc_facility_id'];	
					if($idocIascSame != "yes") {
						$iasc_facility_id 		= $imwFacilityIdNew;	
					}
					$pickup_time		 		= $patientInWatingData[$a]['pickup_time'];
					$arrival_time				= $patientInWatingData[$a]['arrival_time'];	
					$patient_primary_procedure 	= trim($patientInWatingData[$a]['patient_primary_procedure']);
					$patient_primary_acroynm 	= trim($patientInWatingData[$a]['patient_primary_acroynm']);
					$patient_secondary_procedure= trim($patientInWatingData[$a]['patient_secondary_procedure']);
					$patient_secondary_acroynm 	= trim($patientInWatingData[$a]['patient_secondary_acroynm']);
					$patient_tertiary_procedure = trim($patientInWatingData[$a]['patient_tertiary_procedure']);
					$patient_tertiary_acroynm 	= trim($patientInWatingData[$a]['patient_tertiary_acroynm']);
					$site						= $patientInWatingData[$a]['site'];
					$idoc_sch_athena_id	 		= $patientInWatingData[$a]['idoc_sch_athena_id'];	
					$comment			 		= $patientInWatingData[$a]['comment'];	
					$patient_status				= $patientInWatingData[$a]['patient_status'];	
					
					if(constant('CHECK_USER_NPI')=='YES' && in_array($surgeon_npi,$npiArr)) {
						$surgeon_fname 			= $surgeonArr[$surgeon_npi]['fname'];
						$surgeon_mname 			= $surgeonArr[$surgeon_npi]['mname'];
						$surgeon_lname 			= $surgeonArr[$surgeon_npi]['lname'];
					}
					//START CODE TO SET PROCEDURE NAME
					/*
					$chkProcNmeQry = "SELECT * FROM procedures WHERE `name`='".addslashes($patient_primary_procedure)."' OR `procedureAlias`='".addslashes($patient_primary_procedure)."' ORDER BY `procedureId`";
					$chkProcNmeRes = imw_query($chkProcNmeQry);			
					$chkProcNmeNumRow = imw_num_rows($chkProcNmeRes);
					if($chkProcNmeNumRow>0) {
						$chkProcNmeRow = imw_fetch_array($chkProcNmeRes);
						$patient_primary_procedure = $chkProcNmeRow['name'];
					}else if($chkProcNmeNumRow<=0) {
						$chkProcAcronymQry = "SELECT * FROM procedures WHERE `name`='".addslashes($patient_primary_acroynm)."' OR `procedureAlias`='".addslashes($patient_primary_acroynm)."' ORDER BY `procedureId`";
						$chkProcAcronymRes = imw_query($chkProcAcronymQry);			
						$chkProcAcronymNumRow = imw_num_rows($chkProcAcronymRes);
						if($chkProcAcronymNumRow>0) {
							$chkProcAcronymRow = imw_fetch_array($chkProcAcronymRes);
							$patient_primary_procedure = $chkProcAcronymRow['name'];
						}
					}
					*/
					$primProcFound= false;
					$secProcFound = false;
					$terProcFound = false;
					$proc_cat_id  = "";
					foreach($procNameArr as $procNameKey => $procNameVal) {
						if(trim($patient_primary_procedure) && strtolower($patient_primary_procedure)==strtolower($procNameVal)) {
							$patient_primary_procedure=$procNameVal;
							$primProcFound = true;
							$proc_cat_id = $procCatArr[$procNameKey];
						}
						if(trim($patient_primary_procedure) && $primProcFound==false && strtolower($patient_primary_procedure)==strtolower($procAliasArr[$procNameKey])) {
							$patient_primary_procedure=$procNameVal;
							$proc_cat_id = $procCatArr[$procNameKey];
						}
						if(trim($patient_secondary_procedure) && strtolower($patient_secondary_procedure)==strtolower($procNameVal)) {
							$patient_secondary_procedure=$procNameVal;
							$secProcFound = true;
						}
						if(trim($patient_secondary_procedure) && $secProcFound==false && strtolower($patient_secondary_procedure)==strtolower($procAliasArr[$procNameKey])) {
							$patient_secondary_procedure=$procNameVal;
						}
						if(trim($patient_tertiary_procedure) && strtolower($patient_tertiary_procedure)==strtolower($procNameVal)) {
							$patient_tertiary_procedure=$procNameVal;
							$terProcFound = true;
						}
						if(trim($patient_tertiary_procedure) && $terProcFound==false && strtolower($patient_tertiary_procedure)==strtolower($procAliasArr[$procNameKey])) {
							$patient_tertiary_procedure=$procNameVal;
						}
					}				
					
					//END CODE TO SET PROCEDURE NAME
					
					$iOLinkApptCountQry = "SELECT patient_in_waiting_id FROM patient_in_waiting_tbl 
											WHERE dos 			= '".addslashes($dos)."'  
											AND surgeon_fname 	= '".addslashes($surgeon_fname)."'
											AND surgeon_mname 	= '".addslashes($surgeon_mname)."'
											AND surgeon_lname 	= '".addslashes($surgeon_lname)."'
											AND patient_status != 'Canceled'";
					$iOLinkApptCountRes = imw_query($iOLinkApptCountQry) or $msgInfo[] = $iOLinkApptCountQry.imw_error();
					$iOLinkApptCountNumRow = imw_num_rows($iOLinkApptCountRes);
					
					/*$selctQuery = "select patient_in_waiting_id,iAscSyncroStatus from patient_in_waiting_tbl where 
									dos = '".addslashes($dos)."' and 
									patient_id = '".$demoPatientInstId."'
									and patient_status != 'Canceled'";*/
					
									
					//if($ioPtWtId!=0) { 
						$selctQuery = "select patient_in_waiting_id,iAscSyncroStatus from patient_in_waiting_tbl where patient_in_waiting_id = '".$ioPtWtId."'";
					//}
					$selctRes = imw_query($selctQuery) or $msgInfo[] = $selctQuery.imw_error();			
					$selctNumRow = imw_num_rows($selctRes);
					if($selctNumRow>0){
						$selctRow = imw_fetch_array($selctRes);
						$savedPatientInWtId = $selctRow['patient_in_waiting_id'];
						$demoPatientInWtId = $selctRow['patient_in_waiting_id'];
						$demoIAscSyncroStatus = $selctRow['iAscSyncroStatus'];
						/*$patientInWatingDataSaveFlag = true;
						$insurenceDataSaveFlag = true;
						$consentDataSaveFlag = true;
						$consentSigDataSaveFlag = true;*/
						//$a = count($patientInWatingData);
						//$b = count($patientInWatingData);	
						$insertQueryDemograpic2 = "update patient_in_waiting_tbl set ";
						if($demoIAscSyncroStatus=="Syncronized") {
							$insertQueryDemograpic2 .= " iAscReSyncroStatus = 'yes', reSyncroVia = 'iDOC', ";
						}
						
						$whereQueryDemograpic2 = " where patient_in_waiting_id =".$savedPatientInWtId;
					}
					else{
						$insertQueryDemograpic2 = "insert into patient_in_waiting_tbl set ";
						$iAscTotalApptSlot = getiAscSlotCount($surgeon_fname,$surgeon_mname,$surgeon_lname,$dos,$constantImwSlotMinute,$iasc_facility_id,'',$surgeon_npi);
						//echo $iOLinkApptCountNumRow.' - '.$iAscTotalApptSlot.' -'.$iasc_facility_id;die();	
						if($iOLinkApptCountNumRow>=$iAscTotalApptSlot && $chkDtTime=='yes') {
							$boolBookAppt = false;	
						}
					}
					$surgryTmQry="";
					if($idocIascSame=="yes") {
						$surgryTmQry = " surgery_time = '".addslashes($surgery_time)."', ";	
					}
					$insertQueryDemograpic2 .=" dos 							= '".addslashes($dos)."',
												surgeon_fname 					= '".addslashes($surgeon_fname)."',
												surgeon_mname 					= '".addslashes($surgeon_mname)."',
												surgeon_lname 					= '".addslashes($surgeon_lname)."',
												".$surgryTmQry."
												patient_primary_procedure		= '".addslashes($patient_primary_procedure)."',
												patient_secondary_procedure		= '".addslashes($patient_secondary_procedure)."',
												patient_tertiary_procedure		= '".addslashes($patient_tertiary_procedure)."',
												site 							= '".addslashes($site)."',
												idoc_sch_athena_id				= '".$idoc_sch_athena_id."',
												comment 						= '".addslashes($comment)."',
												patient_status 					= '".addslashes($patient_status)."',
												patient_id 						= '".$demoPatientInstId."',
												iasc_facility_id 				= '".$iasc_facility_id."', 
												arrival_time 					= '".addslashes($arrival_time)."',
												drOfficePatientId 				= '".$pid.'-'.$demoPatientInstId."'
											";
												/*
												pickup_time 					= '".addslashes($pickup_time)."',
												arrival_time 					= '".addslashes($arrival_time)."',
												*/

					$surgeonName = addslashes($surgeon_fname).'_'.addslashes($surgeon_lname);						
					$insertQueryDemograpic2 .=	$whereQueryDemograpic2;					
					
					//echo 'hi '.$boolBookAppt;
					
					if($boolBookAppt==true) {
						$insertQueryDemograpicRsId2 = imw_query($insertQueryDemograpic2) or $msgInfo[] = $insertQueryDemograpic2.imw_error();	
						if(!$savedPatientInWtId){
							$demoPatientInWtId = imw_insert_id();
						}
						$b++;
					}
					$a++;
				}
				if ($b == count($patientInWatingData)){
					$patientInWatingDataSaveFlag = true;
				}elseif($boolBookAppt==false) {
					//Do Nothing	
				}
				else{
					//break;
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
						
						$expire_pri_effective_date	= $insurenceCase[$a]['expire_pri_effective_date'];
						$expire_pri_expiration_date	= $insurenceCase[$a]['expire_pri_expiration_date'];
						$expire_sec_effective_date	= $insurenceCase[$a]['expire_sec_effective_date'];
						$expire_sec_expiration_date	= $insurenceCase[$a]['expire_sec_expiration_date'];
						$expire_ter_effective_date	= $insurenceCase[$a]['expire_ter_effective_date'];
						$expire_ter_expiration_date	= $insurenceCase[$a]['expire_ter_expiration_date'];
						
						$insId='';
						$whereQueryInsurenceCase = "";
						$selectCaseQry = "select * from iolink_insurance_case where patient_id = '".$demoPatientInstId."' AND athenaID='".$insCaseId."'";
						//exit;
						$rsSelectCaseQry = imw_query($selectCaseQry) or $msgInfo[] = $selectCaseQry.imw_error();
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
						$insertQueryInsurenceCaseRsId = imw_query($insertQueryInsurenceCase) or $msgInfo[] = $insertQueryInsurenceCase.imw_error();
						if(!$insId){
							$insId =imw_insert_id();						
						}	
						$arrInsCaseId[$a] = array("iolinkInsCaseId" => $insId,"imedicWareInsCaseId" => $insCaseId, 
												  "expire_pri_effective_date" 	=> $expire_pri_effective_date,
												  "expire_pri_expiration_date" 	=> $expire_pri_expiration_date,
												  "expire_sec_effective_date" 	=> $expire_sec_effective_date,
												  "expire_sec_expiration_date" 	=> $expire_sec_expiration_date,
												  "expire_ter_effective_date" 	=> $expire_ter_effective_date,
												  "expire_ter_expiration_date" 	=> $expire_ter_expiration_date
												  );					
						
						$a++;
						$b++;
					}
	
					if ($b == count($insurenceCase)){
						$insurenceCaseSaveFlag = true;
					}
					else{
						//break;
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
					$insTypeArr = array();
					while ($a < count($insurenceData)) {		
						
						$type 					= $insurenceData[$a]['type'];
						$provider		 		= $insurenceData[$a]['ins_provider'];
						$in_house_code		 	= $insurenceData[$a]['ins_in_house_code'];
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
						
						$scan_card = putInsImgContent($scan_card,$insurenceDataScanCard1,$demoPatientInstId); 
						$scan_card2 = putInsImgContent($scan_card2,$insurenceDataScanCard2,$demoPatientInstId); 
						//SET INSURANCE-SCAN INFO
						
						$authorization_number	= $insurenceData[$a]['authorization_number'];
							
						$iolinkInsCaseIdToInsert = "";
						foreach($arrInsCaseId as $key => $val) {
							foreach($val as $key1 => $val1) {
								if($val1 == $ins_caseid){
									//echo $val1.'=='.$insCaseId.'=>'.$val['iolinkInsCaseId'].'<br>';
									$iolinkInsCaseIdToInsert = $val['iolinkInsCaseId'];
								}
							}
						}
						//START CODE TO DEACTIVATE FUTURE ACTIVATED DATE OR EXPIRED DATE OF INSURANCE(IF EXIST)
						$curDate = date('Y-m-d');
						$updateInsActiveInsCompQry = "UPDATE insurance_data SET actInsComp='0' 
														WHERE patient_id = '".$demoPatientInstId."' 
														AND((active_date!='0000-00-00' AND active_date >  '".$curDate."')
															OR(active_date!='0000-00-00' AND active_date <= '".$curDate."' AND expiry_Date !='0000-00-00' AND expiry_Date <= '".$curDate."')
														   )"; 
						$updateInsActiveInsCompRes = imw_query($updateInsActiveInsCompQry) or die($updateInsActiveInsCompQry.imw_error());
						//END CODE TO DEACTIVATE FUTURE ACTIVATED DATE OR EXPIRED DATE OF INSURANCE(IF EXIST)
						
						$whereQueryInsurence = "";
						$selectQry = "select * from insurance_data where patient_id = '".$demoPatientInstId."' and type = '".$type."' and ins_caseid = '".$iolinkInsCaseIdToInsert."' AND actInsComp = '".$actInsComp."'";
						$rsSelectQry = imw_query($selectQry) or $msgInfo[] = $selectQry.imw_error();
						$numRowSelectQry = imw_num_rows($rsSelectQry);
						//echo $numRowSelectQry;
						if($numRowSelectQry==0){					
							$insertQueryInsurence = "insert into insurance_data set ";					
						}
						else{					
							$insertQueryInsurence = "update insurance_data set ";
							$whereQueryInsurence  = "where patient_id = '".$demoPatientInstId."' and type = '".$type."' and ins_caseid = '".$iolinkInsCaseIdToInsert."' AND actInsComp = '".$actInsComp."'";
						}
						$insTypeArr[] = $type;
						if($provider){			
							//START CODE TO CLOSE PREVIOUS INSURANCE CASE STATUS
							$closeInsCaseStatusQry ="UPDATE iolink_insurance_case,insurance_data  
														SET iolink_insurance_case.case_status	=	'Close' 
														WHERE iolink_insurance_case.ins_caseid	=	insurance_data.ins_caseid 
														AND insurance_data.type					=	'".addslashes($type)."' 
														AND insurance_data.ins_provider			=	'".addslashes($provider)."' 
														AND insurance_data.ins_in_house_code	=	'".addslashes($in_house_code)."' 
														
														AND insurance_data.patient_id			=	'".$demoPatientInstId."' 
														AND insurance_data.active_date			<	'".$effective_date."'"; 
							
							$closeInsCaseStatusRes =imw_query($closeInsCaseStatusQry) or die(imw_error().$closeInsCaseStatusQry);
							//END CODE TO CLOSE PREVIOUS INSURANCE CASE STATUS
							
							$insertQueryInsurence .= "	type 				= '".addslashes($type)."',
														ins_provider 		= '".addslashes($provider)."',												
														ins_in_house_code	= '".addslashes($in_house_code)."',												
														policy 				= '".addslashes($policy_number)."',
														group_name 			= '".addslashes($group_number)."',
														plan_name 			= '".addslashes($plan_name)."',
														copay 				= '".addslashes($copay)."',
														refer_req 			= '".addslashes($referal_required)."',
														authorization_number= '".addslashes($authorization_number)."',
														active_date 		= '".addslashes($effective_date)."',
														expiry_Date 		= '".addslashes($expiration_date)."',	
														fname 				= '".addslashes($subscriber_fname)."',
														mname 				= '".addslashes($subscriber_mname)."',
														lname 				= '".addslashes($subscriber_lname)."',
														sub_relation 		= '".addslashes($subscriber_relationship)."',						
														ssn 				= '".addslashes($subscriber_ss)."',
														dob 				= '".addslashes($subscriber_DOB)."',
														gender 				= '".addslashes($subscriber_sex)."',
														address1 			= '".addslashes($subscriber_street)."',
														address2 			= '".addslashes($subscriber_street_2)."',
														zip_code 			= '".addslashes($subscriber_postal_code)."',
														city 				= '".addslashes($subscriber_city)."',
														state 				= '".addslashes($subscriber_state)."',
														home_phone 			= '".addslashes($subscriber_phone)."',
														work_phone 			= '".addslashes($subscriber_biz_phone)."',
														mbl_phone 			= '".addslashes($subscriber_mobile)."',
														scan_card			= '".$scan_card."',
														scan_label			= '".addslashes($scan_label)."',
														ins_caseid			= '".addslashes($iolinkInsCaseIdToInsert)."',	
														claims_adjustername	= '".addslashes($claims_adjustername)."',
														claims_adjusterphone= '".addslashes($claims_adjusterphone)."',
														Sec_HCFA			= '".addslashes($Sec_HCFA)."',
														newComDate			= '".addslashes($newComDate)."',
														actInsComp			= '".addslashes($actInsComp)."',
														actInsCompDate		= '".addslashes($actInsCompDate)."',
														scan_card2			= '".$scan_card2."',
														scan_label2			= '".addslashes($scan_label2)."',
														cardscan_date		= '".addslashes($cardscan_date)."',
														cardscan_comments	= '".addslashes($cardscan_comments)."',
														cardscan1_datetime	= '".addslashes($cardscan1_datetime)."',
														self_pay_provider	= '".addslashes($self_pay_provider)."',
														patient_id 			= '".addslashes($demoPatientInstId)."',
														waiting_id 			= '".addslashes($demoPatientInWtId)."'
													";
							$insertQueryInsurence .= $whereQueryInsurence;
							$insertQueryInsurenceRsId = imw_query($insertQueryInsurence) or die(imw_error().$insertQueryInsurence);
						}	
						$a++;
						$b++;
					}
					//START DE-ACTIVATE INSURANCE IF NOT SYNCED TO IASCLINK
					$staticInsTypeArr = array("primary","secondary","tertiary");
					foreach($staticInsTypeArr as $staticInsType) {
						if(!in_array($staticInsType,$insTypeArr) && $demoPatientInstId) {
							$iolinkInsCaseIdExp = $arrInsCaseId[0]["iolinkInsCaseId"];
							$imwInsCaseIdExp = $arrInsCaseId[0]["imedicWareInsCaseId"];
							$imwExpInsEffectiveDt = "";
							$imwExpInsExpirationDt = "";
							if($staticInsType == "primary") {
								$imwExpInsEffectiveDt 	= $arrInsCaseId[0]["expire_pri_effective_date"];
								$imwExpInsExpirationDt 	= $arrInsCaseId[0]["expire_pri_expiration_date"];
							}else if($staticInsType == "secondary") {
								$imwExpInsEffectiveDt 	= $arrInsCaseId[0]["expire_sec_effective_date"];
								$imwExpInsExpirationDt 	= $arrInsCaseId[0]["expire_sec_expiration_date"];
							}else if($staticInsType == "tertiary") {
								$imwExpInsEffectiveDt 	= $arrInsCaseId[0]["expire_ter_effective_date"];
								$imwExpInsExpirationDt 	= $arrInsCaseId[0]["expire_ter_expiration_date"];
							}
							$efftExpireDtQry = "";
							if($imwExpInsEffectiveDt && $imwExpInsEffectiveDt !='0000-00-00' && $imwExpInsEffectiveDt !='0') {
								$efftExpireDtQry .= ", active_date = '".$imwExpInsEffectiveDt."'";
							}
							if($imwExpInsExpirationDt && $imwExpInsExpirationDt !='0000-00-00' && $imwExpInsExpirationDt !='0') {
								$efftExpireDtQry .= ", expiry_Date = '".$imwExpInsExpirationDt."'";
							}
							$updtDeactiveInsuranceQry = "UPDATE insurance_data SET actInsComp = '0' ".$efftExpireDtQry." WHERE patient_id = '".$demoPatientInstId."' AND type = '".$staticInsType."' AND ins_caseid = '".$iolinkInsCaseIdExp."' AND ins_caseid != '0' ";
							$updtDeactiveInsuranceRes = imw_query($updtDeactiveInsuranceQry) or die(imw_error().$updtDeactiveInsuranceQry);
						}
					}
					//END DE-ACTIVATE INSURANCE IF NOT SYNCED TO IASCLINK
					
					if ($b == count($insurenceData)){
						$insurenceDataSaveFlag = true;
					}
					else{
						//break;
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
						
						if(!$insScanDocDocumentStatus) {
							$insScanDocDocumentStatus='0';
						}	
						//SET INSURANCE-SCAN INFO
						
						$insurenceDocScanCard1		= $InsurenceScanDoc[$a]['insurenceDocScanCard1'];
						$insurenceDocScanCard2		= $InsurenceScanDoc[$a]['insurenceDocScanCard2'];
						
						$insurenceDocScanCard1 		= base64_decode($insurenceDocScanCard1);
						$insurenceDocScanCard2 		= base64_decode($insurenceDocScanCard2);
						
						$insScanDocScanCard 		= putInsImgContent($insScanDocScanCard,$insurenceDocScanCard1,$demoPatientInstId); 
						$insScanDocScanCard2 		= putInsImgContent($insScanDocScanCard2,$insurenceDocScanCard2,$demoPatientInstId); 
						
						//SET INSURANCE-SCAN INFO
						
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
						$selectQryInsurenceScanDoc = "select * from iolink_insurance_scan_documents where patient_id = $demoPatientInstId and type = '$insScanDocType' and ins_caseid = '$iolinkScanDocInsCaseIdToInsert'";
						$rsSelectQryInsurenceScanDoc = imw_query($selectQryInsurenceScanDoc) or $msgInfo[] = $selectQryInsurenceScanDoc.imw_error();
						$numRowSelectQryInsurenceScanDoc = imw_num_rows($rsSelectQryInsurenceScanDoc);
						//echo $numRowSelectQryInsurenceScanDoc;
						if($numRowSelectQryInsurenceScanDoc==0){					
							$insertQueryInsurenceScanDoc = "insert into iolink_insurance_scan_documents set ";					
						}
						else{					
							$insertQueryInsurenceScanDoc = "update iolink_insurance_scan_documents set ";
							$whereQueryInsurenceScanDoc = "where patient_id = $demoPatientInstId and type = '$insScanDocType' and ins_caseid = '$iolinkScanDocInsCaseIdToInsert'";
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
						//echo $insertQueryInsurenceScanDoc;
						$insertQueryInsurenceScanDocRsId = imw_query($insertQueryInsurenceScanDoc) or die($insertQueryInsurenceScanDoc.imw_error());
						$a++;
						$b++;
					}
					if ($b == count($InsurenceScanDoc)){
						$InsurenceScanDocSaveFlag = true;
					}
					else{
						//break;
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
							$rsSelectQry = imw_query($selectQry) or $msgInfo[] = $selectQry.imw_error();
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
						//break;
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
								/*
								$aConn = new COM("SIGPLUS.SigPlusCtrl.1");
								$aConn->InitSigPlus();
								$aConn->SigCompressionMode = 2;
								$aConn->SigString=$postData;
								$aConn->ImageFileFormat = 4; //4=jpg, 0=bmp, 6=tif
								$aConn->ImageXSize = 500; //width of resuting image in pixels
								$aConn->ImageYSize =165; //height of resulting image in pixels
								$aConn->ImagePenWidth = 11; //thickness of ink in pixels
								$aConn->JustifyMode = 5;  //center and fit signature to size
								//ALSO SAVE IMAGE FOR PDF
								$pathPDF =  realpath(dirname(__FILE__).'/../html2pdfnew').'/'.$signature_image_name;
			
								//$pathPDF =  realpath(dirname(__FILE__).'/html2pdfnew').'\sign_iolink_'.$_REQUEST["intPatientWaitingId"].'_'.date('d_m_y_h_i_s').'_'.$ps.'.jpg';
								$patientSignArr[] = $pathPDF;
								$aConn->WriteImageFile("$pathPDF");
								*/
							}	
						//}
					//end save sig.	
						if($pathPDF){
							$whereQueryConsentSig = "";
							$selectQry = "select * from iolink_consent_form_signature where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and consent_template_id = $consent_template_id and signature_count = $signature_count";
							//exit;
							$rsSelectQry = imw_query($selectQry) or $msgInfo[] = $selectQry.imw_error();
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
						//break;
					}
					/////////////////////
					//Saving scan data Information
					$sacnDataXML = $iolinkMainData[5];				
					$tag = 'scanDocDataChild';
					$scanData = array();
					$scanData = XMLtoArray($sacnDataXML,$tag);
					//print_r($scanData);
					//exit;
					$httpfolder =  realpath(dirname(__FILE__));					
					$iolinkPdfPath = $httpfolder."/../admin/pdfFiles/";
					$imedicIolinkPdfPath = $iolinkPdfPath.$surgeonName;
					if(!is_dir($imedicIolinkPdfPath)){		
						mkdir($imedicIolinkPdfPath);
					}
					$a = 0;
					$b = 0;
					while ($a < count($scanData)) {			
						$patient_id 				= $scanData[$a]['patient_id'];
						$scan_doc_add				= $scanData[$a]['scan_doc_add'];
						$iolink_scan_folder_name	= $scanData[$a]['scan_type_folder'];	
						$scan_save_date_time 		= $scanData[$a]['created_date'];
						$scan1Upload	 			= $scanData[$a]['doc_data'];
						$scan_upload_data	 		= $scanData[$a]['scan_upload_data'];
						
						$mask	 					= addslashes(urldecode($scanData[$a]['mask']));					
						$scanDocAdd = $scan_doc_add;					
						$scanDocAdd = explode("/",$scanDocAdd);									
						$document_name = $scanDocAdd[2];
						$patient_in_waiting_id = $demoPatientInWtId;
						$iolink_scan_folder_name = (int)$iolink_scan_folder_name;
						switch ($iolink_scan_folder_name):
							case 0:
								$iolink_scan_folder_name = "ptInfo";
								break;
							case 1:
								$iolink_scan_folder_name = "clinical";
								break;
							case 2:
								$iolink_scan_folder_name = "healthQuest";
								break;						
							case 3:
								$iolink_scan_folder_name = "h&p";
								break;
							case 4:
								$iolink_scan_folder_name = "ekg";
								break;	
							case 5:
								$iolink_scan_folder_name = "ocularHx";
								break;									
						endswitch;
						
						$scan1Upload = base64_decode($scan1Upload);					
						//$scan_upload_data = base64_decode($scan_upload_data);
						$putUploadPdfData = base64_decode($scan_upload_data);
						
						//$httpfolder =  realpath(dirname(__FILE__));
						//$iolinkPdfPath = $httpfolder."/../admin/pdfFiles/";
						//$imedicIolinkPdfPath = $iolinkPdfPath.$surgeonName;
						$patientCreatedDateTime = str_replace(" ","_",$scan_save_date_time);
						$patientCreatedDateTime = str_replace(":","-",$patientCreatedDateTime);
						$pdfUploadFileName = $document_name;
						if($document_name) {
							$document_nameExplode = explode('.',$document_name);
							$pdfUploadFileName =$document_nameExplode[0].'_'.$demoPatientInstId.'_'.$patientCreatedDateTime.'.'.$document_nameExplode[1];
						}
						$pdfUploadFilePath = "pdfFiles".'/'.$surgeonName.'/'.$pdfUploadFileName;
						//$pdfFileName = $document_name.'_'.$demoPatientInstId.'_'.$patientCreatedDateTime.'.pdf';					
						
						$putPdfUploadFileName = $imedicIolinkPdfPath.'/'.$pdfUploadFileName;
						$whereQueryScan = "";
						$selectQry = "select * from iolink_scan_consent where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and document_name = '".addslashes($document_name)."'";
						//exit;
						$rsSelectQry = imw_query($selectQry) or $msgInfo[] = $selectQry.imw_error();
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
											 scan_save_date_time = '".addslashes($scan_save_date_time)."',
											 document_name = '".addslashes($document_name)."',
											 iolink_scan_folder_name = '".addslashes($iolink_scan_folder_name)."',
											";
						
						if(stristr($document_name,'.pdf')!==false) {
							$insertQueryScan .= "image_type = 'application/pdf',		
												 pdfFilePath = '".addslashes($pdfUploadFilePath)."',											 								 
												";
							if($putUploadPdfData){
								//die($putPdfUploadFileName);
								file_put_contents($putPdfUploadFileName,$putUploadPdfData);	
							}
						}else {
							$insertQueryScan .= "image_type = 'image/".$document_nameExplode[1]."',		
												 pdfFilePath = '".addslashes($pdfUploadFilePath)."',											 								 
												";
							if($scan1Upload){
								file_put_contents($putPdfUploadFileName,$scan1Upload);	
							}
							//$insertQueryScan .= "scan1Upload = '".addslashes($scan1Upload)."',";
						}
						$insertQueryScan .= "mask = '".addslashes($mask)."'";
						$insertQueryScan .= $whereQueryScan;
						
						//START CODE NOT TO SAVE DOCUMENTS OLDER THAN 90 DAYS(days may vary)
						$documentsNotExpire = getDocumentsNotExpire($scan_save_date_time,$sa_date,$old_days_count);
						$insertQueryScanRsId = "";
						if(trim($documentsNotExpire)=='yes') {//SAVE IF DOCUMENT IS NOT OLDER THAN 90 DAYS(days may vary)
							$insertQueryScanRsId = imw_query($insertQueryScan) or $msgInfo[] = $insertQueryScan.imw_error();					
						}
						//END CODE NOT TO SAVE DOCUMENTS OLDER THAN 90 DAYS(days may vary)
						$a++;
						$b++;
					}
					if ($b == count($scanData)){
						$scanDataSaveFlag = true;
					}
					else{
						//break;
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
					
					$iolinkPdfPath = $httpfolder."/../admin/pdfFiles/";
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
						$patient_id 				= $pdfData[$a]['patient_id'];
						$surgery_consent_name		= $pdfData[$a]['surgery_consent_name'];
						$form_created_date			= $pdfData[$a]['form_created_date'];	
						$pdf_doc_data 				= $pdfData[$a]['pdf_doc_data'];						
						$idoc_consent_template_id 	= $pdfData[$a]['consent_template_id'];						
						
						//exit;
						$patientCreatedDateTime = str_replace(" ","_",$form_created_date);
						$patientCreatedDateTime = str_replace(":","-",$patientCreatedDateTime);
						$pdfFileName = $surgery_consent_name.'_'.$demoPatientInstId.'_'.$patientCreatedDateTime.'.pdf';
						//$putPdfFileName = $imedicIolinkPdfPath.$patientDir.'\\'.$pdfFileName;				
						//$putPdfFileName = $imedicIolinkPdfPath.'\\'.$pdfFileName;
						$putPdfFileName = $imedicIolinkPdfPath.'/'.str_ireplace("/","_",$pdfFileName);				
						//$putPdfDocdata = base64_decode($pdf_doc_data);	
						$putPdfDocdata = base64_decode($pdf_doc_data);			
						//file_put_contents($putPdfFileName,$putPdfDocdata);			
						$pdfFilePath = "pdfFiles".'/'.$surgeonName.'/'.str_ireplace("/","_",$pdfFileName);
						$iolink_scan_folder_name = "ptInfo";
						$whereQueryScan = "";
						$selectQry = "select * from iolink_scan_consent where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and document_name = '".addslashes($pdfFileName)."'";
						//exit;
						$rsSelectQry = imw_query($selectQry) or $msgInfo[] = $selectQry.imw_error();
						$numRowSelectQry = imw_num_rows($rsSelectQry);
						//echo $numRowSelectQry.'qwqwqw';
						if($numRowSelectQry==0){					
							$insertQueryScan = "insert into iolink_scan_consent set ";					
						}
						else{					
							$insertQueryScan = "update iolink_scan_consent set ";
							$whereQueryScan = "where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and document_name = '".addslashes($pdfFileName)."'";
						}
						$insertQueryScan .= "patient_id 				= '".addslashes($demoPatientInstId)."',
											 patient_in_waiting_id 		= '".addslashes($demoPatientInWtId)."',										 
											 scan_save_date_time 		= '".addslashes($form_created_date)."',
											 document_name 				= '".addslashes($pdfFileName)."',
											 image_type 				= 'application/pdf',		
											 pdfFilePath 				= '".addslashes($pdfFilePath)."',
											 idoc_consent_template_id 	= '".addslashes($idoc_consent_template_id)."',
											 iolink_scan_folder_name 	= '".addslashes($iolink_scan_folder_name)."'									
											";
						$insertQueryScan .= $whereQueryScan;
						
						//START CODE NOT TO SAVE DOCUMENTS OLDER THAN 90 DAYS(days may vary)
						$documentsNotExpire = getDocumentsNotExpire($form_created_date,$sa_date,$old_days_count);
						$insertQueryScanRsId = "";
						if(trim($documentsNotExpire)=='yes') {//SAVE IF DOCUMENT IS NOT OLDER THAN 90 DAYS(days may vary)
							$insertQueryScanRsId = imw_query($insertQueryScan) or $msgInfo[] = $insertQueryScan.imw_error();			
						}
						//END CODE NOT TO SAVE DOCUMENTS OLDER THAN 90 DAYS(days may vary)
						
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
						//break;
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
						$iolinkPdfPath = $httpfolder."/../admin/pdfFiles/";
						$imedicIolinkPdfPath = $iolinkPdfPath.$surgeonName;
						
						if(!is_dir($imedicIolinkPdfPath)){		
							mkdir($imedicIolinkPdfPath);
						}
						//echo $imedicIolinkPdfPath;
						//die;
					}
					while ($a < count($faceSheetDocData)) {			
						$patient_id 			= $faceSheetDocData[$a]['patient_id'];
						$file_name 				= $faceSheetDocData[$a]['file_name'];						
						$face_sheet_doc_data	= $faceSheetDocData[$a]['face_sheet_doc_data'];						
											
						$faceSheetPdfFileNameOld = 'faceSheet'.$demoPatientInstId.$demoPatientInWtId.'.pdf';
						$faceSheetPdfFileName = $file_name.'_'.$demoPatientInstId.'_'.$demoPatientInWtId.'.pdf';
	
						$faceSheetPutPdfFileName = $imedicIolinkPdfPath.'/'.$faceSheetPdfFileName;				
						
						$faceSheetPutPdfDocdata = base64_decode($face_sheet_doc_data);			
						
						$pdfFilePath = "pdfFiles".'/'.$surgeonName.'/'.$faceSheetPdfFileName;
						$iolink_scan_folder_name = "ptInfo";
						$whereQueryScan = "";
						$numRowSelectQryOld=0;
						$numRowSelectQryNew=0;
						$selectQryOld = "select * from iolink_scan_consent where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and document_name = '".addslashes($faceSheetPdfFileNameOld)."'";
						$rsSelectQryOld = imw_query($selectQryOld) or $msgInfo[] = $selectQryOld.imw_error();
						$numRowSelectQryOld = imw_num_rows($rsSelectQryOld);
						if($numRowSelectQryOld==0){	
							$selectQryNew = "select * from iolink_scan_consent where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and document_name = '".addslashes($faceSheetPdfFileName)."'";
							$rsSelectQryNew = imw_query($selectQryNew) or $msgInfo[] = $selectQryNew.imw_error();
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
											 scan_save_date_time = IF(scan_save_date_time='0000-00-00 00:00:00','".date("Y-m-d H:i:s")."',scan_save_date_time),
											 iolink_scan_folder_name = '".addslashes($iolink_scan_folder_name)."'									
											";
						$insertQueryScan .= $whereQueryScan;
						$insertQueryScanRsId = imw_query($insertQueryScan) or $msgInfo[] = $insertQueryScan.imw_error();			
						if($insertQueryScanRsId){
							if($faceSheetPutPdfDocdata){							
								/*
								if(@file_exists($imedicIolinkPdfPath.'\\'.$faceSheetPdfFileNameOld)){
									unlink($imedicIolinkPdfPath.'\\'.$faceSheetPdfFileNameOld);
								}*/
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
						//break;
					}
					////////////////////
					//End Saving face sheet pdf
					
					//Saving Pre-op Health Questionnaire Patient Allergy & Medication Status
					$patientAllergyNoValueDataXML = $iolinkMainData[22];
					$tag = 'patientAllergyNoValueDataChild';
					$patientAllergyNoValueData = array();
					$patientAllergyNoValueData = XMLtoArray($patientAllergyNoValueDataXML,$tag);
					//print_r($patientAllergyNoValueData);				
					$a = 0;
					$b = 0;
					$allergies_nkda_status = "";
					while ($a < count($patientAllergyNoValueData)) {
						$allergies_nkda_status = "";
						$allergies_no_value		= $patientAllergyNoValueData[$a]['allergies_no_value'];
						$medication_no_value	= $patientAllergyNoValueData[$a]['medication_no_value'];
						$medication_comments	= $patientAllergyNoValueData[$a]['medication_comments'];
						if($allergies_no_value == "NoAllergies") {
							$allergies_nkda_status = "Yes";	
							//DELETE ALLERGIES IF NKA STATUS=YES
							$objManageData->delRecord('iolink_patient_allergy', 'patient_in_waiting_id', $demoPatientInWtId);
						}
						$iolinkNoMedicationStatus = "";
						$iolinkNoMedicationComments = "";
						if($medication_no_value == "NoMedications") {
							$iolinkNoMedicationStatus = "Yes";
							$iolinkNoMedicationComments = $medication_comments;
							//DELETE MEDICATION IF NO MEDICATION STATUS=YES
							$objManageData->delRecord('iolink_patient_prescription_medication', 'patient_in_waiting_id', $demoPatientInWtId);
						}
						$updtNkaQry = "UPDATE patient_in_waiting_tbl SET 
										iolink_allergiesNKDA_status		= '".addslashes($allergies_nkda_status)."',
										iolink_no_medication_status		= '".addslashes($iolinkNoMedicationStatus)."',
										iolink_no_medication_comments	= '".addslashes($iolinkNoMedicationComments)."' 
										WHERE patient_in_waiting_id 	= '".$demoPatientInWtId."'";
						$updtNkaRes = imw_query($updtNkaQry) or $msgInfo[] = $updtNkaQry.imw_error();
						if($updtNkaRes){
							$b++;
						}
						$a++;
					}
					if ($b == count($patientAllergyNoValueData)){
						$patientAllergyNoValueDataSaveFlag = true;
					}
					else{
						//break;
					}
					//end Saving Pre-op Health Questionnaire Patient Allergy					
					
					//Saving Pre-op Health Questionnaire
					$PreOpHealthQuesDataXML = $iolinkMainData[8];
					
					$tag 								= 'patientPreOpDataChild';
					$patientPreOpHealthQuestionnaire 	= array();
					$patientPreOpHealthQuestionnaire 	= XMLtoArray($PreOpHealthQuesDataXML,$tag);
					//print_r($patientPreOpHealthQuestionnaire);				
					
					$a = 0;
					$b = 0;
					//$allergies_status = '';
					while ($a < count($patientPreOpHealthQuestionnaire)) {	
						$pathJpg 						= "";		
						$heartTrouble 					= $patientPreOpHealthQuestionnaire[$a]['heartTrouble'];
						$stroke							= $patientPreOpHealthQuestionnaire[$a]['stroke'];
						$HighBP							= $patientPreOpHealthQuestionnaire[$a]['HighBP'];	
						$anticoagulationTherapy 		= $patientPreOpHealthQuestionnaire[$a]['anticoagulationTherapy'];		
						$asthma							= $patientPreOpHealthQuestionnaire[$a]['asthma'];	
						$diabetes 						= $patientPreOpHealthQuestionnaire[$a]['diabetes'];
						$insulinDependence				= $patientPreOpHealthQuestionnaire[$a]['insulinDependence'];
						$epilepsy						= $patientPreOpHealthQuestionnaire[$a]['epilepsy'];						
						$restlessLegSyndrome			= $patientPreOpHealthQuestionnaire[$a]['restlessLegSyndrome'];	
						$hepatitis						= $patientPreOpHealthQuestionnaire[$a]['hepatitis'];	
						$hepatitisA						= $patientPreOpHealthQuestionnaire[$a]['hepatitisA'];	
						$hepatitisB						= $patientPreOpHealthQuestionnaire[$a]['hepatitisB'];	
						$hepatitisC						= $patientPreOpHealthQuestionnaire[$a]['hepatitisC'];	
						$kidneyDisease					= $patientPreOpHealthQuestionnaire[$a]['kidneyDisease'];	
						$shunt							= $patientPreOpHealthQuestionnaire[$a]['shunt'];	
						$fistula						= $patientPreOpHealthQuestionnaire[$a]['fistula'];	
						$hivAutoimmuneDiseases			= $patientPreOpHealthQuestionnaire[$a]['hivAutoimmuneDiseases'];	
						$hivTextArea					= $patientPreOpHealthQuestionnaire[$a]['hivTextArea'];	
						$cancerHistory					= $patientPreOpHealthQuestionnaire[$a]['cancerHistory'];	
						$cancerHistoryDesc				= $patientPreOpHealthQuestionnaire[$a]['cancerHistoryDesc'];	
						$organTransplant				= $patientPreOpHealthQuestionnaire[$a]['organTransplant'];	
						$organTransplantDesc			= $patientPreOpHealthQuestionnaire[$a]['organTransplantDesc'];	
						$anesthesiaBadReaction			= $patientPreOpHealthQuestionnaire[$a]['anesthesiaBadReaction'];						
						$tuberculosis					= $patientPreOpHealthQuestionnaire[$a]['tuberculosis'];	
						$otherTroubles					= $patientPreOpHealthQuestionnaire[$a]['otherTroubles'];	
						$walker							= $patientPreOpHealthQuestionnaire[$a]['walker'];	
						$contactLenses					= $patientPreOpHealthQuestionnaire[$a]['contactLenses'];	
						$smoke							= $patientPreOpHealthQuestionnaire[$a]['smoke'];	
						$smokeHowMuch					= $patientPreOpHealthQuestionnaire[$a]['smokeHowMuch'];	
						$smokeAdvise					= $patientPreOpHealthQuestionnaire[$a]['smokeAdvise'];	
						$alchohol						= $patientPreOpHealthQuestionnaire[$a]['alchohol'];	
						$alchoholHowMuch				= $patientPreOpHealthQuestionnaire[$a]['alchoholHowMuch'];	
						$alchoholAdvise					= $patientPreOpHealthQuestionnaire[$a]['alchoholAdvise'];	
						$autoInternalDefibrillator		= $patientPreOpHealthQuestionnaire[$a]['autoInternalDefibrillator'];	
						$metalProsthetics				= $patientPreOpHealthQuestionnaire[$a]['metalProsthetics'];	
						$notes							= $patientPreOpHealthQuestionnaire[$a]['notes'];						
						$patientSign					= $patientPreOpHealthQuestionnaire[$a]['patientSign'];
						$patient_sign_image_path		= $patientPreOpHealthQuestionnaire[$a]['patient_sign_image_path'];
						$witness_sign_image_path		= $patientPreOpHealthQuestionnaire[$a]['witness_sign_image_path'];
						$dateQuestionnaire				= $patientPreOpHealthQuestionnaire[$a]['dateQuestionnaire'];
						$emergencyContactPerson			= $patientPreOpHealthQuestionnaire[$a]['emergencyContactPerson'];
						$emergencyContactPhone			= $patientPreOpHealthQuestionnaire[$a]['emergencyContactPhone'];
						$witnessname					= $patientPreOpHealthQuestionnaire[$a]['witnessname'];
						$witnessSign					= $patientPreOpHealthQuestionnaire[$a]['witnessSign'];
						$healthQstFormStatus			= $patientPreOpHealthQuestionnaire[$a]['form_status'];
						//$allergies_status				= $patientPreOpHealthQuestionnaire[$a]['allergies_status'];
						$allergies_status_reviewed		= $patientPreOpHealthQuestionnaire[$a]['allergies_status_reviewed'];					
						
						$heartTroubleDesc				= $patientPreOpHealthQuestionnaire[$a]['heartTroubleDesc'];
						$strokeDesc						= $patientPreOpHealthQuestionnaire[$a]['strokeDesc'];
						$HighBPDesc						= $patientPreOpHealthQuestionnaire[$a]['HighBPDesc'];
						$anticoagulationTherapyDesc		= $patientPreOpHealthQuestionnaire[$a]['anticoagulationTherapyDesc'];
						$asthmaDesc						= $patientPreOpHealthQuestionnaire[$a]['asthmaDesc'];
						$tuberculosisDesc				= $patientPreOpHealthQuestionnaire[$a]['tuberculosisDesc'];
						$diabetesDesc					= $patientPreOpHealthQuestionnaire[$a]['diabetesDesc'];
						$epilepsyDesc					= $patientPreOpHealthQuestionnaire[$a]['epilepsyDesc'];
						$restlessLegSyndromeDesc		= $patientPreOpHealthQuestionnaire[$a]['restlessLegSyndromeDesc'];
						$hepatitisDesc					= $patientPreOpHealthQuestionnaire[$a]['hepatitisDesc'];
						$kidneyDiseaseDesc				= $patientPreOpHealthQuestionnaire[$a]['kidneyDiseaseDesc'];
						$anesthesiaBadReactionDesc		= $patientPreOpHealthQuestionnaire[$a]['anesthesiaBadReactionDesc'];
						$walkerDesc						= $patientPreOpHealthQuestionnaire[$a]['walkerDesc'];
						$contactLensesDesc				= $patientPreOpHealthQuestionnaire[$a]['contactLensesDesc'];
						$autoInternalDefibrillatorDesc	= $patientPreOpHealthQuestionnaire[$a]['autoInternalDefibrillatorDesc'];

						$patient_sign_image_path		= explode("/",$patient_sign_image_path);
						$patient_image_name				= $patient_sign_image_path[4];
						if( !$patient_image_name) $patient_image_name = end($patient_sign_image_path);

						$witness_sign_image_path		= explode("/",$witness_sign_image_path);
						$witness_image_name				= $witness_sign_image_path[4];
						if( !$witness_image_name) $witness_image_name = end($witness_sign_image_path);
						
						$patient_sign_image_data		= $patientPreOpHealthQuestionnaire[$a]['patient_sign_image_data'];
						$witness_sign_image_data		= $patientPreOpHealthQuestionnaire[$a]['witness_sign_image_data'];
						
						//SAVE SIGNATURE
						$patientSignArr = array();
						$signQry = "";
						for($ps=1;$ps<=2;$ps++){
							$imageName='';
							$signImageData='';
							if($ps==1){
								$imageName 						= $patient_image_name;
								$signImageData 					= $patient_sign_image_data;
							}
							if($ps==2){
								$imageName 						= $witness_image_name;
								$signImageData 					= $witness_sign_image_data;
							}
							$savePath = "";
							if($imageName) {
								$pathJpg =  realpath(dirname(__FILE__).'/../html2pdfnew').'/'.$imageName;
								$signPutJpgData = base64_decode($signImageData);
								file_put_contents($pathJpg,$signPutJpgData);
								$savePath = "html2pdfnew"."/".$imageName;
								//$patientSignArr[] = $savePath;
								if($ps==1){
									$signQry .= " patient_sign_image_path = '".addslashes($savePath)."', ";	
								}
								if($ps==2){
									$signQry .= " witness_sign_image_path = '".addslashes($savePath)."', ";	
								}
										
							}
						}
						//print_r($patient_sign_image_path);
						//exit;
					//end save sig.	
						$andPreOpHealthQry = "";
						if(trim($healthQstFormStatus)=="") {
							$andPreOpHealthQry = " AND form_status = '' ";		
						}
						$whereQueryPreOpHealth = "";
						$selectQry = "select * from iolink_preophealthquestionnaire where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId";
						$rsSelectQry = imw_query($selectQry) or $msgInfo[] = $selectQry.imw_error();
						$numRowSelectQry = imw_num_rows($rsSelectQry);						
						if($numRowSelectQry==0){					
							$insertQueryPreOpHealth = "insert into iolink_preophealthquestionnaire set ";					
						}
						else{					
							$insertQueryPreOpHealth = "update iolink_preophealthquestionnaire set ";
							$whereQueryPreOpHealth = "where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId ".$andPreOpHealthQry;
						}
						//allergies_status = '".addslashes($allergies_nkda_status)."', //now NKDA status will set in patient_in_waitig table and not health quest table
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
												smokeAdvise = '".addslashes($smokeAdvise)."',
												alchohol = '".addslashes($alchohol)."',
												alchoholHowMuch = '".addslashes($alchoholHowMuch)."',												
												alchoholAdvise = '".addslashes($alchoholAdvise)."',
												autoInternalDefibrillator = '".addslashes($autoInternalDefibrillator)."',
												metalProsthetics = '".addslashes($metalProsthetics)."',
												notes = '".addslashes($notes)."',
												patientSign = '".addslashes($patientSign)."',
												".$signQry."
												dateQuestionnaire = '".addslashes($dateQuestionnaire)."',
												emergencyContactPerson = '".addslashes($emergencyContactPerson)."',
												emergencyContactPhone = '".addslashes($emergencyContactPhone)."',
												witnessname = '".addslashes($witnessname)."',
												witnessSign = '".addslashes($witnessSign)."',
												
												allergies_status_reviewed = '".addslashes($allergies_status_reviewed)."',
												
												heartTroubleDesc = '".addslashes($heartTroubleDesc)."',
												strokeDesc = '".addslashes($strokeDesc)."',
												HighBPDesc = '".addslashes($HighBPDesc)."',
												anticoagulationTherapyDesc = '".addslashes($anticoagulationTherapyDesc)."',
												asthmaDesc = '".addslashes($asthmaDesc)."',
												tuberculosisDesc = '".addslashes($tuberculosisDesc)."',
												diabetesDesc = '".addslashes($diabetesDesc)."',
												epilepsyDesc = '".addslashes($epilepsyDesc)."',
												restlessLegSyndromeDesc = '".addslashes($restlessLegSyndromeDesc)."',
												hepatitisDesc = '".addslashes($hepatitisDesc)."',
												kidneyDiseaseDesc = '".addslashes($kidneyDiseaseDesc)."',
												anesthesiaBadReactionDesc = '".addslashes($anesthesiaBadReactionDesc)."',
												walkerDesc = '".addslashes($walkerDesc)."',
												contactLensesDesc = '".addslashes($contactLensesDesc)."',
												autoInternalDefibrillatorDesc = '".addslashes($autoInternalDefibrillatorDesc)."',
												
												patient_in_waiting_id = '".$demoPatientInWtId."',
												form_status = '".addslashes($healthQstFormStatus)."',																								
												patient_id = '$demoPatientInstId'										
											";		
						$insertQueryPreOpHealth .= $whereQueryPreOpHealth;
						$insertQueryPreOpHealth;
						
						$insertQueryPreOpHealthRsId = imw_query($insertQueryPreOpHealth) or $msgInfo[] = $insertQueryPreOpHealth.imw_error();
						if($insertQueryPreOpHealthRsId){
							$b++;
						}
						$a++;
						
					}
					if ($b == count($patientPreOpHealthQuestionnaire)){
						$patientPreOpHealthQuesDataSaveFlag = true;
					}
					else{
						//break;
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
						$adminQuestionDesc		= $patientAdminHealthQues[$a]['adminQuestionDesc'];			
						$whereQueryAdminHealthQues = "";
						$selectQry = "select * from iolink_healthquestionadmin where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and adminQuestion = '$adminQuestion'";
						$rsSelectQry = imw_query($selectQry) or $msgInfo[] = $selectQry.imw_error();
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
														adminQuestionDesc = '".addslashes($adminQuestionDesc)."',
														patient_in_waiting_id = '".$demoPatientInWtId."',																								
														patient_id = '$demoPatientInstId'										
														";			
						$insertQueryAdminHealthQues .= $whereQueryAdminHealthQues;
						$insertQueryAdminHealthQuesRsId = imw_query($insertQueryAdminHealthQues) or $msgInfo[] = $insertQueryAdminHealthQues.imw_error();						
						if($insertQueryAdminHealthQuesRsId){
							$b++;
						}
						$a++;
						
					}
					if ($b == count($patientAdminHealthQues)){
						$patientAdminHealthQuesDataSaveFlag = true;
					}
					else{
						//break;
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
					if($allergies_nkda_status=='Yes') {
						//RESETTING ARRAY
						$patientHealthQuesAllergy = array();
					}
					while ($a < count($patientHealthQuesAllergy)) {
						$allergy_name 			= $patientHealthQuesAllergy[$a]['allergy_name'];
						$reaction_name			= $patientHealthQuesAllergy[$a]['reaction_name'];
									
						$whereQueryHealthQuesAllergy = "";
						$selectQry = "select * from iolink_patient_allergy where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and allergy_name='".addslashes($allergy_name)."' and reaction_name='".addslashes($reaction_name)."'";
						
						$rsSelectQry = imw_query($selectQry) or $msgInfo[] = $selectQry.imw_error();
						$numRowSelectQry = imw_num_rows($rsSelectQry);						
						if($numRowSelectQry==0){					
							$insertQueryHealthQuesAllergy = "insert into iolink_patient_allergy set ";					
						}
						else{					
							$insertQueryHealthQuesAllergy = "update iolink_patient_allergy set ";
							$whereQueryHealthQuesAllergy = "where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and allergy_name='".addslashes($allergy_name)."' and reaction_name='".addslashes($reaction_name)."'";
						}
						$insertQueryHealthQuesAllergy .= "allergy_name = '".addslashes($allergy_name)."',
														reaction_name = '".addslashes($reaction_name)."',	
														patient_in_waiting_id = '".$demoPatientInWtId."',																								
														patient_id = '$demoPatientInstId'										
														";			
						$insertQueryHealthQuesAllergy .= $whereQueryHealthQuesAllergy;
						$insertQueryHealthQuesAllergyRsId = imw_query($insertQueryHealthQuesAllergy) or $msgInfo[] = $insertQueryHealthQuesAllergy.imw_error();						
						if($insertQueryHealthQuesAllergyRsId){
							$b++;
						}
						$a++;
						
					}
					if ($b == count($patientHealthQuesAllergy)){
						$patientHealthQuesAllergyDataSaveFlag = true;
					}
					else{
						//break;
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
						$prescription_medication_sig			= $patientHealthQuesMed[$a]['prescription_medication_sig'];
									
						$whereQueryHealthQuesMed = "";
						$selectQry = "select * from iolink_patient_prescription_medication where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and prescription_medication_name='".addslashes($prescription_medication_name)."' and prescription_medication_desc='".addslashes($prescription_medication_desc)."' and prescription_medication_sig='".addslashes($prescription_medication_sig)."'";
						
						$rsSelectQry = imw_query($selectQry) or $msgInfo[] = $selectQry.imw_error();
						$numRowSelectQry = imw_num_rows($rsSelectQry);						
						if($numRowSelectQry==0){					
							$insertQueryHealthQuesMed = "insert into iolink_patient_prescription_medication set ";					
						}
						else{					
							$insertQueryHealthQuesMed = "update iolink_patient_prescription_medication set ";
							$whereQueryHealthQuesMed = "where patient_id = '".$demoPatientInstId."' and patient_in_waiting_id = '".$demoPatientInWtId."' and prescription_medication_name='".addslashes($prescription_medication_name)."' and prescription_medication_desc='".addslashes($prescription_medication_desc)."' and prescription_medication_sig='".addslashes($prescription_medication_sig)."'";
						}
						$insertQueryHealthQuesMed .= "prescription_medication_name = '".addslashes($prescription_medication_name)."',
														prescription_medication_desc = '".addslashes($prescription_medication_desc)."',	
														prescription_medication_sig = '".addslashes($prescription_medication_sig)."',	
														patient_in_waiting_id = '".$demoPatientInWtId."',																								
														patient_id = '$demoPatientInstId'										
														";			
						$insertQueryHealthQuesMed .= $whereQueryHealthQuesMed;
						$insertQueryHealthQuesMedRsId = imw_query($insertQueryHealthQuesMed) or $msgInfo[] = $insertQueryHealthQuesMed.imw_error();						
						if($insertQueryHealthQuesMedRsId){
							$b++;
						}
						$a++;
						
					}
					if ($b == count($patientHealthQuesMed)){
						$patientHealthQuesMedDataSaveFlag = true;
					}
					else{
						//break;
					}
					//end Saving Pre-op Health Questionnaire Patient Medication*/
					//aaaaaaaaaaa
					//Saving Pre-op Health Questionnaire Patient Allergy if data not containt in iolink (it is prectice allergy data)
					
					$selectQry = "select * from iolink_patient_allergy where patient_id = '$demoPatientInstId' and patient_in_waiting_id = '$demoPatientInWtId'";
					$rsSelectQry = imw_query($selectQry) or $msgInfo[] = $selectQry.imw_error();
					$numRowSelectQry = imw_num_rows($rsSelectQry);						
					if($numRowSelectQry==0){					
						$patientPracAllergyDataXML = $iolinkMainData[12];
						$tag = 'pracPatientAlleryDataChild';
						$pracPatientAlleryData = array();
						$pracPatientAlleryData = XMLtoArray($patientPracAllergyDataXML,$tag);
						//print_r($patientAdminHealthQues);				
						$a = 0;
						$b = 0;
						$pracPatientAlleryData = array();
						/*
						if($allergies_status=='Yes') {
							//RESETTING ARRAY
							$pracPatientAlleryData = array();
						}
					
						while ($a < count($pracPatientAlleryData)) {						
							$allergy_name 			= $pracPatientAlleryData[$a]['title'];													
							$whereQueryHealthQuesAllergy = "";
							$selectQry = "select * from iolink_patient_allergy where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and allergy_name='$allergy_name'";
							$rsSelectQry = imw_query($selectQry) or $msgInfo[] = $selectQry.imw_error();
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
							$insertQueryHealthQuesAllergyRsId = imw_query($insertQueryHealthQuesAllergy) or $msgInfo[] = $insertQueryHealthQuesAllergy.imw_error();						
							if($insertQueryHealthQuesAllergyRsId){
								$b++;
							}
							$a++;
							
						}*/
						if ($b == count($pracPatientAlleryData)){
							$patientHealthQuesAllergyDataSaveFlag = true;
						}
						else{
							//break;
						}
					}
					
					//end Saving Pre-op Health Questionnaire Patient Allergy if data not containt in iolink (it is prectice allergy data)
					//aaaaaaaaaaaaaaaa
					//bbbbbbbbbbbbb
					//Saving Pre-op Health Questionnaire Patient Medication if data not containt in iolink (it is prectice allergy data)
					$selectQry = "select * from iolink_patient_prescription_medication where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId";
					$rsSelectQry = imw_query($selectQry) or $msgInfo[] = $selectQry.imw_error();
					$numRowSelectQry = imw_num_rows($rsSelectQry);						
					if($numRowSelectQry==0){					
						$patientPracMedDataXML = $iolinkMainData[13];
						$tag = 'pracPatientMedDataChild';
						$pracPatientMedData = array();
						$pracPatientMedData = XMLtoArray($patientPracMedDataXML,$tag);
						//print_r($patientAdminHealthQues);				
						$a = 0;
						$b = 0;
						$pracPatientMedData = array();
						/*
						while ($a < count($pracPatientMedData)) {						
							$prescription_medication_name = $pracPatientMedData[$a]['title'];
							$prescription_medication_desc = $pracPatientMedData[$a]['destination'];
										
							$whereQueryHealthQuesMed = "";
							$selectQry = "select * from iolink_patient_prescription_medication where patient_id = $demoPatientInstId and patient_in_waiting_id = $demoPatientInWtId and prescription_medication_name='$prescription_medication_name' and prescription_medication_desc='$prescription_medication_desc'";
							
							$rsSelectQry = imw_query($selectQry) or $msgInfo[] = $selectQry.imw_error();
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
							$insertQueryHealthQuesMedRsId = imw_query($insertQueryHealthQuesMed) or $msgInfo[] = $insertQueryHealthQuesMed.imw_error();						
							if($insertQueryHealthQuesMedRsId){
								$b++;
							}
							$a++;						
						}*/					
						if ($b == count($pracPatientMedData)){
							$patientHealthQuesMedDataSaveFlag = true;
						}
						else{
							//break;
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
					$httpfolderOculer =  realpath(dirname(__FILE__));					
					$iolinkPdfPathOculer = $httpfolderOculer."/../admin/pdfFiles/";
					$imedicIolinkPdfPathOculer = $iolinkPdfPathOculer.$surgeonName;
					if(!is_dir($imedicIolinkPdfPathOculer)){		
						mkdir($imedicIolinkPdfPathOculer);
					}				
					while ($a < count($oculerHxDocData)) {			
						$pdfFilePath = "";
						$patient_id = "";
						$file_name = "";
						$oculer_doc_doc_data = "";
						$patient_id 			= $oculerHxDocData[$a]['patient_id'];
						$file_name 				= $oculerHxDocData[$a]['file_name'];						
						$oculer_doc_doc_data	= $oculerHxDocData[$a]['oculer_doc_doc_data'];						
						
						$oculerHxPdfFileName = $file_name.'_'.$demoPatientInstId.'_'.$demoPatientInWtId.'.pdf';
	
						$oculerHxPutPdfFileName = $imedicIolinkPdfPathOculer.'/'.$oculerHxPdfFileName;				
						
						$oculerHxPutPdfDocdata = base64_decode($oculer_doc_doc_data);			
						$insertQueryScanOculer = "";
						$pdfFilePath = "pdfFiles".'/'.$surgeonName.'/'.$oculerHxPdfFileName;
						$iolink_scan_folder_name = "ocularHx";
						$whereQueryScanOculer = "";					
						$numRowSelectQryOculer=0;					
						$selectQryNewOculer = "select * from iolink_scan_consent where patient_id = '$demoPatientInstId' and patient_in_waiting_id = '$demoPatientInWtId' and document_name = '".addslashes($oculerHxPdfFileName)."'";						
						$rsSelectQryNewOculer = imw_query($selectQryNewOculer) or $msgInfo[] = $selectQryNewOculer.imw_error();
						$numRowSelectQryOculer = imw_num_rows($rsSelectQryNewOculer);						
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
											 scan_save_date_time = IF(scan_save_date_time='0000-00-00 00:00:00','".date("Y-m-d H:i:s")."',scan_save_date_time),
											 iolink_scan_folder_name = 'ocularHx'									
											";
						$insertQueryScanOculer .= $whereQueryScanOculer;					
						$insertQueryScanRsIdOculer = imw_query($insertQueryScanOculer) or $msgInfo[] = $insertQueryScanOculer.imw_error();			
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
						//break;
					}
					////////////////////
					//Saving Oculer HX pdf --- End
					//bbbbbbbbbbbbbb 
					
					//Saving AScan HX pdf --- Start
					$aScanPdfDataXML = $iolinkMainData[18];				
					//exit;
					$tag = 'aScanDocDataChild';
					$aScanDocData = array();
					$aScanDocData = XMLtoArray($aScanPdfDataXML,$tag);
					//print_r($aScanDocData);
					//exit;
					$a = 0;
					$b = 0;
					$httpfolderAScan =  realpath(dirname(__FILE__));					
					$iolinkPdfPathAScan = $httpfolderAScan."/../admin/pdfFiles/";
					$imedicIolinkPdfPathAScan = $iolinkPdfPathAScan.$surgeonName;
					if(!is_dir($imedicIolinkPdfPathAScan)){		
						mkdir($imedicIolinkPdfPathAScan);
					}				
					while ($a < count($aScanDocData)) {			
						$pdfFilePath 			= "";
						$patient_id 			= "";
						$file_name 				= "";
						$ascan_doc_doc_data 	= "";
						$patient_id 			= $aScanDocData[$a]['patient_id'];
						$file_name 				= $aScanDocData[$a]['file_name'];						
						$ascan_doc_doc_data		= $aScanDocData[$a]['ascan_doc_data'];						
						
						$aScanPdfFileName 		= $file_name.'_'.$demoPatientInstId.'_'.$demoPatientInWtId.'.pdf';
	
						$aScanPutPdfFileName 	= $imedicIolinkPdfPathAScan.'/'.$aScanPdfFileName;				
						
						$aScanPutPdfDocdata 	= base64_decode($ascan_doc_doc_data);			
						$insertQueryScanAScan 	= "";
						$pdfFilePath 			= "pdfFiles".'/'.$surgeonName.'/'.$aScanPdfFileName;
						$iolink_scan_folder_name= "iol";
						$whereQueryScanAScan 	= "";					
						$numRowSelectQryAScan	=0;					
						$selectQryNewAScan 		= "select * from iolink_scan_consent where patient_id = '$demoPatientInstId' and patient_in_waiting_id = '$demoPatientInWtId' and document_name = '".addslashes($aScanPdfFileName)."'";						
						$rsSelectQryNewAScan 	= imw_query($selectQryNewAScan) or $msgInfo[] = $selectQryNewAScan.imw_error();
						$numRowSelectQryAScan 	= imw_num_rows($rsSelectQryNewAScan);						
						if($numRowSelectQryAScan==0){					
							$insertQueryScanAScan = "insert into iolink_scan_consent set ";					
						}
						else{
							$insertQueryScanAScan = "update iolink_scan_consent set ";
							if($numRowSelectQryAScan>0){
								$whereQueryScanAScan = "where patient_id = '$demoPatientInstId' and patient_in_waiting_id = '$demoPatientInWtId' and document_name = '".addslashes($aScanPdfFileName)."'";
							}
							
						}
						$insertQueryScanAScan .= "patient_id = '".addslashes($demoPatientInstId)."',
											 patient_in_waiting_id = '".addslashes($demoPatientInWtId)."',										 										 
											 document_name = '".addslashes($aScanPdfFileName)."',
											 image_type = 'application/pdf',		
											 pdfFilePath = '".addslashes($pdfFilePath)."',											 								 
											 scan_save_date_time = IF(scan_save_date_time='0000-00-00 00:00:00','".date("Y-m-d H:i:s")."',scan_save_date_time),
											 iolink_scan_folder_name = '".addslashes($iolink_scan_folder_name)."'									
											";
						$insertQueryScanAScan .= $whereQueryScanAScan;					
						$insertQueryScanRsIdAScan = imw_query($insertQueryScanAScan) or $msgInfo[] = $insertQueryScanAScan.imw_error();			
						if($insertQueryScanRsIdAScan){
							if($aScanPutPdfDocdata){														
								file_put_contents($aScanPutPdfFileName,$aScanPutPdfDocdata);							
							}
						}
						$a++;
						$b++;
					}
					if ($b == count($aScanDocData)){
						$aScanPdfDataSaveFlag = true;
					}
					else{
						//break;
					}
					////////////////////
					//Saving AScan pdf --- End
					//bbbbbbbbbbbbbb 				
				
					//Saving IOL_Master HX pdf --- Start
					$iolMasterPdfDataXML = $iolinkMainData[19];				
					//exit;
					$tag = 'iolMasterDocDataChild';
					$iolMasterDocData = array();
					$iolMasterDocData = XMLtoArray($iolMasterPdfDataXML,$tag);
					//print_r($iolMasterDocData);
					//exit;
					$a = 0;
					$b = 0;
					$httpfolderIOL_Master =  realpath(dirname(__FILE__));					
					$iolinkPdfPathIOL_Master = $httpfolderIOL_Master."/../admin/pdfFiles/";
					$imedicIolinkPdfPathIOL_Master = $iolinkPdfPathIOL_Master.$surgeonName;
					if(!is_dir($imedicIolinkPdfPathIOL_Master)){		
						mkdir($imedicIolinkPdfPathIOL_Master);
					}				
					while ($a < count($iolMasterDocData)) {			
						$pdfFilePath 				= "";
						$patient_id 				= "";
						$file_name 					= "";
						$iol_master_doc_doc_data 	= "";
						$patient_id 				= $iolMasterDocData[$a]['patient_id'];
						$file_name 					= $iolMasterDocData[$a]['file_name'];						
						$iol_master_doc_doc_data	= $iolMasterDocData[$a]['iol_master_doc_data'];						
						
						$iolMasterPdfFileName 		= $file_name.'_'.$demoPatientInstId.'_'.$demoPatientInWtId.'.pdf';
	
						$iolMasterPutPdfFileName 	= $imedicIolinkPdfPathIOL_Master.'/'.$iolMasterPdfFileName;				
						
						$iolMasterPutPdfDocdata 	= base64_decode($iol_master_doc_doc_data);			
						$insertQueryScanIOL_Master 	= "";
						$pdfFilePath 				= "pdfFiles".'/'.$surgeonName.'/'.$iolMasterPdfFileName;
						$iolink_scan_folder_name	= "iol";
						$whereQueryScanIOL_Master 	= "";					
						$numRowSelectQryIOL_Master	= 0;					
						$selectQryNewIOL_Master 	= "select * from iolink_scan_consent where patient_id = '$demoPatientInstId' and patient_in_waiting_id = '$demoPatientInWtId' and document_name = '".addslashes($iolMasterPdfFileName)."'";						
						$rsSelectQryNewIOL_Master 	= imw_query($selectQryNewIOL_Master) or $msgInfo[] = $selectQryNewIOL_Master.imw_error();
						$numRowSelectQryIOL_Master 	= imw_num_rows($rsSelectQryNewIOL_Master);						
						if($numRowSelectQryIOL_Master==0){					
							$insertQueryScanIOL_Master = "insert into iolink_scan_consent set ";					
						}
						else{
							$insertQueryScanIOL_Master = "update iolink_scan_consent set ";
							if($numRowSelectQryIOL_Master>0){
								$whereQueryScanIOL_Master = "where patient_id = '$demoPatientInstId' and patient_in_waiting_id = '$demoPatientInWtId' and document_name = '".addslashes($iolMasterPdfFileName)."'";
							}
							
						}
						$insertQueryScanIOL_Master .= "patient_id = '".addslashes($demoPatientInstId)."',
											 patient_in_waiting_id = '".addslashes($demoPatientInWtId)."',										 										 
											 document_name = '".addslashes($iolMasterPdfFileName)."',
											 image_type = 'application/pdf',		
											 pdfFilePath = '".addslashes($pdfFilePath)."',											 								 
											 scan_save_date_time = IF(scan_save_date_time='0000-00-00 00:00:00','".date("Y-m-d H:i:s")."',scan_save_date_time),
											 iolink_scan_folder_name = '".addslashes($iolink_scan_folder_name)."'									
											";
						$insertQueryScanIOL_Master .= $whereQueryScanIOL_Master;					
						$insertQueryScanRsIdIOL_Master = imw_query($insertQueryScanIOL_Master) or $msgInfo[] = $insertQueryScanIOL_Master.imw_error();			
						if($insertQueryScanRsIdIOL_Master){
							if($iolMasterPutPdfDocdata){														
								file_put_contents($iolMasterPutPdfFileName,$iolMasterPutPdfDocdata);							
							}
						}
						$a++;
						$b++;
					}
					if ($b == count($iolMasterDocData)){
						$iolMasterPdfDataSaveFlag = true;
					}
					else{
						//break;
					}
					////////////////////
					//Saving IOL_Master pdf --- End
					//bbbbbbbbbbbbbb
					
					
					//Saving IOL_Master Data --- Start
					$iolMasterDataXML = $iolinkMainData[20];
					$tag = 'iolinkIolMasterDataChild';
					$iolMasterData = array();
					$iolMasterData = XMLtoArray($iolMasterDataXML,$tag);
					$a = 0;
					$b = 0;
					
					$tempSiteStr	=	strtoupper($site);
					$tempSiteStr	=	($tempSiteStr == 'RIGHT')	? 	'OD'  :  ($tempSiteStr == 'LEFT' ? 'OS' : $tempSiteStr);
					$tempSiteArray=	array($tempSiteStr);
					if($tempSiteStr == 'BOTH' )
						$tempSiteArray=	 array('OD','OS') ;
					
					while ($a < count($iolMasterData)) {			
						//ADD IOL LENSES ONLY WHEN CATEGORY OF PROCEDURE IS NOT LASER, INJECTION AND MISCELLANEOUS
						if($proc_cat_id != '2' && (!$procInjMiscArr[$proc_cat_id]["Misc"]) && (!$procInjMiscArr[$proc_cat_id]["Inj"])) {
							foreach($tempSiteArray as $tempSite)
							{
								$opRoomDefault	=	$iolMasterData[$a]['iolinkOpRoomDefault'.$tempSite];
								for ($loop = 1; $loop <= 4; $loop++)
								{
									$manufacturer = $brand = $mode = $diopter = $insertUpdateQuery = $whereQuery = ''; 
									
									$manufacturer	=	$iolMasterData[$a]['iolinkManufacturer'.$tempSite.'_'.$loop];
									$brand				=	$iolMasterData[$a]['iolinkLensBrand'.$tempSite.'_'.$loop];
									$model			=	$iolMasterData[$a]['iolinkModel'.$tempSite.'_'.$loop];
									$diopter			=	$iolMasterData[$a]['iolinkDiopter'.$tempSite.'_'.$loop];
									
									
									$query	= "Select iol_manufacturer_id From iolink_iol_manufacturer Where patient_id = '".$demoPatientInstId."' And patient_in_waiting_id = '".$demoPatientInWtId."' And model= '".addslashes($model)."'";
									
									$sql		=	imw_query($query);
									$cnt		=	imw_num_rows($sql);
									if( $cnt == 0 )
									{
										$insertUpdateQuery = "Insert Into ";	
									}
									else
									{
										$res	=	imw_fetch_object($sql);
										$insertUpdateQuery = "Update ";	
										$whereQuery	= "  Where iol_manufacturer_id = ".(int) $res->iol_manufacturer_id." ";
									}
									
									$insertUpdateQuery .=	" iolink_iol_manufacturer Set 
																				patient_id ='".addslashes($demoPatientInstId)."',
																				patient_in_waiting_id = '".addslashes($demoPatientInWtId)."',
																				manufacture = '".addslashes($manufacturer)."',
																				lensBrand = '".addslashes($brand)."',
																				model = '".addslashes($model)."',
																				Diopter = '".addslashes($diopter)."'  
																			";
														  
									$insertUpdateQuery	.=	$whereQuery ;					  
									//echo $insertUpdateQuery.'<br>' ;
									imw_query($insertUpdateQuery) or $msgInfo[] = $insertUpdateQuery.imw_error();
										
								}
								
							}
							//print_r($msgInfo);	
							$query2	=	"Update iolink_iol_manufacturer Set opRoomDefault = 0  Where patient_id = '".$demoPatientInstId."' And patient_in_waiting_id = '".$demoPatientInWtId."' ";
							$sql2	=	imw_query($query2) ;
							
							$query3	=	"Update iolink_iol_manufacturer Set opRoomDefault = 1  Where patient_id = '".$demoPatientInstId."' And patient_in_waiting_id = '".$demoPatientInWtId."' And model= '".addslashes($opRoomDefault)."'";
							$sql3	=	imw_query($query3) ;	
						}
						$a++;
						$b++;
					}
					if ($b == count($iolMasterData)){
						$iolMasterDataSaveFlag = true;
					}
					else{
						//break;
					}
					////////////////////
					//Saving IOL_Master Data --- End
					 	

					//Saving GenHealth HX pdf --- Start
					$genHealthPdfDataXML = $iolinkMainData[21];				
					//exit;
					$tag = 'genHealthDocDataChild';
					$genHealthDocData = array();
					$genHealthDocData = XMLtoArray($genHealthPdfDataXML,$tag);
					//print_r($genHealthDocData);
					//exit;
					$a = 0;
					$b = 0;
					$httpfolderGenHealth =  realpath(dirname(__FILE__));					
					$iolinkPdfPathGenHealth = $httpfolderGenHealth."/../admin/pdfFiles/";
					$imedicIolinkPdfPathGenHealth = $iolinkPdfPathGenHealth.$surgeonName;
					if(!is_dir($imedicIolinkPdfPathGenHealth)){		
						mkdir($imedicIolinkPdfPathGenHealth);
					}				
					while ($a < count($genHealthDocData)) {			
						$pdfFilePath = "";
						$patient_id = "";
						$file_name = "";
						$gen_health_doc_doc_data = "";
						$patient_id 			= $genHealthDocData[$a]['patient_id'];
						$file_name 				= $genHealthDocData[$a]['file_name'];						
						$gen_health_doc_doc_data	= $genHealthDocData[$a]['gen_health_doc_data'];						
						
						$genHealthPdfFileName = $file_name.'_'.$demoPatientInstId.'_'.$demoPatientInWtId.'.pdf';
	
						$genHealthPutPdfFileName = $imedicIolinkPdfPathGenHealth.'/'.$genHealthPdfFileName;				
						
						$genHealthPutPdfDocdata = base64_decode($gen_health_doc_doc_data);			
						$insertQueryScanGenHealth = "";
						$pdfFilePath = "pdfFiles".'/'.$surgeonName.'/'.$genHealthPdfFileName;
						$iolink_scan_folder_name = "clinical";
						$whereQueryScanGenHealth = "";					
						$numRowSelectQryGenHealth=0;					
						$selectQryNewGenHealth = "select * from iolink_scan_consent where patient_id = '$demoPatientInstId' and patient_in_waiting_id = '$demoPatientInWtId' and document_name = '".addslashes($genHealthPdfFileName)."'";						
						$rsSelectQryNewGenHealth = imw_query($selectQryNewGenHealth) or $msgInfo[] = $selectQryNewGenHealth.imw_error();
						$numRowSelectQryGenHealth = imw_num_rows($rsSelectQryNewGenHealth);						
						if($numRowSelectQryGenHealth==0){					
							$insertQueryScanGenHealth = "insert into iolink_scan_consent set ";					
						}
						else{
							$insertQueryScanGenHealth = "update iolink_scan_consent set ";
							if($numRowSelectQryGenHealth>0){
								$whereQueryScanGenHealth = "where patient_id = '$demoPatientInstId' and patient_in_waiting_id = '$demoPatientInWtId' and document_name = '".addslashes($genHealthPdfFileName)."'";
							}
							
						}
						$insertQueryScanGenHealth .= "patient_id = '".addslashes($demoPatientInstId)."',
											 patient_in_waiting_id = '".addslashes($demoPatientInWtId)."',										 										 
											 document_name = '".addslashes($genHealthPdfFileName)."',
											 image_type = 'application/pdf',		
											 pdfFilePath = '".addslashes($pdfFilePath)."',											 								 
											 scan_save_date_time = IF(scan_save_date_time='0000-00-00 00:00:00','".date("Y-m-d H:i:s")."',scan_save_date_time),
											 iolink_scan_folder_name = 'clinical'									
											";
						$insertQueryScanGenHealth .= $whereQueryScanGenHealth;					
						$insertQueryScanRsIdGenHealth = imw_query($insertQueryScanGenHealth) or $msgInfo[] = $insertQueryScanGenHealth.imw_error();			
						if($insertQueryScanRsIdGenHealth){
							if($genHealthPutPdfDocdata){														
								file_put_contents($genHealthPutPdfFileName,$genHealthPutPdfDocdata);							
							}
						}
						$a++;
						$b++;
					}
					if ($b == count($genHealthDocData)){
						$genHealthPdfDataSaveFlag = true;
					}
					else{
						//break;
					}
					////////////////////
					//Saving GenHealth HX pdf --- End
					//bbbbbbbbbbbbbb 
					
					
					
					//Saving SX planing pdf --- Start
					$sxPdfDataXML = $iolinkMainData[23];
					
					//exit;
					$tag = 'sxDocDataChild';
					$sxDocData = array();
					$sxDocData = XMLtoArray($sxPdfDataXML,$tag);
					//print_r($sxDocData);
					//exit;
					$a = 0;
					$b = 0;
					$httpfoldersx =  realpath(dirname(__FILE__));					
					$iolinkPdfPathsx = $httpfoldersx."/../admin/pdfFiles/";
					$imedicIolinkPdfPathsx = $iolinkPdfPathsx.$surgeonName;
					if(!is_dir($imedicIolinkPdfPathsx)){		
						mkdir($imedicIolinkPdfPathsx);
					}				
					while ($a < count($sxDocData)) {			
						$pdfFilePath = "";
						$patient_id = "";
						$file_name = "";
						$sx_doc_doc_data = "";
						$patient_id 			= $sxDocData[$a]['patient_id'];
						$file_name 				= $sxDocData[$a]['file_name'];						
						$sx_doc_doc_data		= $sxDocData[$a]['sx_doc_data'];						
						
						$sxPdfFileName = $file_name.'_'.$demoPatientInstId.'_'.$demoPatientInWtId.'.pdf';
	
						$sxPutPdfFileName = $imedicIolinkPdfPathsx.'/'.$sxPdfFileName;
						$sxPutPdfDocdata = base64_decode($sx_doc_doc_data);			
						$insertQueryScansx = "";
						$pdfFilePath = "pdfFiles".'/'.$surgeonName.'/'.$sxPdfFileName;
						$iolink_scan_folder_name = "clinical";
						$whereQueryScansx = "";					
						$numRowSelectQrysx=0;					
						$selectQryNewsx = "select * from iolink_scan_consent where patient_id = '$demoPatientInstId' and patient_in_waiting_id = '$demoPatientInWtId' and document_name = '".addslashes($sxPdfFileName)."'";						
						$rsSelectQryNewsx = imw_query($selectQryNewsx) or $msgInfo[] = $selectQryNewsx.imw_error();
						$numRowSelectQrysx = imw_num_rows($rsSelectQryNewsx);						
						if($numRowSelectQrysx==0){					
							$insertQueryScansx = "insert into iolink_scan_consent set ";					
						}
						else{
							$insertQueryScansx = "update iolink_scan_consent set ";
							if($numRowSelectQrysx>0){
								$whereQueryScansx = "where patient_id = '$demoPatientInstId' and patient_in_waiting_id = '$demoPatientInWtId' and document_name = '".addslashes($sxPdfFileName)."'";
							}
							
						}
						$insertQueryScansx .= "patient_id = '".addslashes($demoPatientInstId)."',
											 patient_in_waiting_id = '".addslashes($demoPatientInWtId)."',										 										 
											 document_name = '".addslashes($sxPdfFileName)."',
											 image_type = 'application/pdf',		
											 pdfFilePath = '".addslashes($pdfFilePath)."',											 								 
											 scan_save_date_time = IF(scan_save_date_time='0000-00-00 00:00:00','".date("Y-m-d H:i:s")."',scan_save_date_time),
											 iolink_scan_folder_name = 'clinical'									
											";
						$insertQueryScansx .= $whereQueryScansx;					
						$insertQueryScanRsIdsx = imw_query($insertQueryScansx) or $msgInfo[] = $insertQueryScansx.imw_error();			
						if($insertQueryScanRsIdsx){
							if($sxPutPdfDocdata){														
								file_put_contents($sxPutPdfFileName,$sxPutPdfDocdata);							
							}
						}
						$a++;
						$b++;
					}
					if ($b == count($sxDocData)){
						$sxPdfDataSaveFlag = true;
					}
					else{
						//break;
					}
					////////////////////
					//Saving sx planning pdf --- End
									
				
				}
				
				//Saving H&P pdf --- Start
					//$historyPhysicalPdfDataXML = $iolinkMainData[24];				
					//exit;
					/*$tag = 'historyPhysicalDocDataChild';
					$historyPhysicalDocData = array();
					$historyPhysicalDocData = XMLtoArray($historyPhysicalPdfDataXML,$tag);
					//print_r($historyPhysicalDocData);
					//exit;
					$a = 0;
					$b = 0;
					$httpfolderHP =  realpath(dirname(__FILE__));					
					$iolinkPdfPathHP = $httpfolderHP."/../admin/pdfFiles/";
					$imedicIolinkPdfPathHP = $iolinkPdfPathHP.$surgeonName;
					if(!is_dir($imedicIolinkPdfPathHP)){		
						mkdir($imedicIolinkPdfPathHP);
					}				
					while ($a < count($historyPhysicalDocData)) {			
						$pdfFilePath = "";
						$patient_id = "";
						$file_name = "";
						$history_physical_doc_data = "";
						$patient_id 			= $historyPhysicalDocData[$a]['patient_id'];
						$file_name 				= $historyPhysicalDocData[$a]['file_name'];						
						$history_physical_doc_data	= $historyPhysicalDocData[$a]['history_physical_doc_data'];						
						
						$historyPhysicalPdfFileName = $file_name.'_'.$demoPatientInstId.'_'.$demoPatientInWtId.'.pdf';
	
						$historyPhysicalPutPdfFileName = $imedicIolinkPdfPathHP.'/'.$historyPhysicalPdfFileName;				
						
						$historyPhysicalPutPdfDocdata = base64_decode($history_physical_doc_data);			
						$insertQueryScanHP = "";
						$pdfFilePath = "pdfFiles".'/'.$surgeonName.'/'.$historyPhysicalPdfFileName;
						$iolink_scan_folder_name = "H&P";
						$whereQueryScanHP = "";					
						$numRowSelectQryHP=0;					
						$selectQryNewHP = "select * from iolink_scan_consent where patient_id = '$demoPatientInstId' and patient_in_waiting_id = '$demoPatientInWtId' and document_name = '".addslashes($historyPhysicalPdfFileName)."'";						
						$rsSelectQryNewHP = imw_query($selectQryNewHP) or $msgInfo[] = $selectQryNewHP.imw_error();
						$numRowSelectQryHP = imw_num_rows($rsSelectQryNewHP);						
						if($numRowSelectQryHP==0){					
							$insertQueryScanHP = "insert into iolink_scan_consent set ";					
						}
						else{
							$insertQueryScanHP = "update iolink_scan_consent set ";
							if($numRowSelectQryHP>0){
								$whereQueryScanHP = "where patient_id = '$demoPatientInstId' and patient_in_waiting_id = '$demoPatientInWtId' and document_name = '".addslashes($historyPhysicalPdfFileName)."'";
							}
							
						}
						$insertQueryScanHP .= "patient_id = '".addslashes($demoPatientInstId)."',
											 patient_in_waiting_id = '".addslashes($demoPatientInWtId)."',										 										 
											 document_name = '".addslashes($historyPhysicalPdfFileName)."',
											 image_type = 'application/pdf',		
											 pdfFilePath = '".addslashes($pdfFilePath)."',											 								 
											 scan_save_date_time = IF(scan_save_date_time='0000-00-00 00:00:00','".date("Y-m-d H:i:s")."',scan_save_date_time),
											 iolink_scan_folder_name = 'H&P'									
											";
						$insertQueryScanHP .= $whereQueryScanHP;					
						$insertQueryScanRsIdHP = imw_query($insertQueryScanHP) or $msgInfo[] = $insertQueryScanHP.imw_error();			
						if($insertQueryScanRsIdHP){
							if($historyPhysicalPutPdfDocdata){														
								file_put_contents($historyPhysicalPutPdfFileName,$historyPhysicalPutPdfDocdata);							
							}
						}
						$a++;
						$b++;
					}
					if ($b == count($historyPhysicalDocData)){
						$historyPhysicalPdfDataSaveFlag = true;
					}
					else{
						break;
					}*/
					////////////////////
					//Saving H&P pdf --- End
				//Saving History Physical
					$HistoryPhysicalDataXML = $iolinkMainData[25];
					
					$tag 								= 'patientHPDataChild';
					$patientHistoryPhysical 	= array();
					$patientHistoryPhysical 	= XMLtoArray($HistoryPhysicalDataXML,$tag);
					//print_r($patientHistoryPhysical);				
					
					$a = 0;
					$b = 0;
					//$allergies_status = '';
					while ($a < count($patientHistoryPhysical)) {	
						$cadMI = $patientHistoryPhysical[$a]['cadMI'];
						$cadMIDesc = $patientHistoryPhysical[$a]['cadMIDesc'];
						$cvaTIA = $patientHistoryPhysical[$a]['cvaTIA'];	
						$cvaTIADesc = $patientHistoryPhysical[$a]['cvaTIADesc'];		
						$htnCP = $patientHistoryPhysical[$a]['htnCP'];	
						$htnCPDesc = $patientHistoryPhysical[$a]['htnCPDesc'];
						$anticoagulationTherapy = $patientHistoryPhysical[$a]['anticoagulationTherapy'];
						$anticoagulationTherapyDesc = $patientHistoryPhysical[$a]['anticoagulationTherapyDesc'];						
						$respiratoryAsthma = $patientHistoryPhysical[$a]['respiratoryAsthma'];	
						$respiratoryAsthmaDesc = $patientHistoryPhysical[$a]['respiratoryAsthmaDesc'];	
						$arthritis = $patientHistoryPhysical[$a]['arthritis'];	
						$arthritisDesc = $patientHistoryPhysical[$a]['arthritisDesc'];	
						$diabetes	= $patientHistoryPhysical[$a]['diabetes'];	
						$diabetesDesc	= $patientHistoryPhysical[$a]['diabetesDesc'];	
						$recreationalDrug	= $patientHistoryPhysical[$a]['recreationalDrug'];	
						$recreationalDrugDesc = $patientHistoryPhysical[$a]['recreationalDrugDesc'];
						$giGerd	= $patientHistoryPhysical[$a]['giGerd'];
						$giGerdDesc = $patientHistoryPhysical[$a]['giGerdDesc'];	
						$ocular = $patientHistoryPhysical[$a]['ocular'];		
						$ocularDesc = $patientHistoryPhysical[$a]['ocularDesc'];	
						$kidneyDisease = $patientHistoryPhysical[$a]['kidneyDisease'];
						$kidneyDiseaseDesc = $patientHistoryPhysical[$a]['kidneyDiseaseDesc'];
						$hivAutoimmune = $patientHistoryPhysical[$a]['hivAutoimmune'];						
						$hivAutoimmuneDesc = $patientHistoryPhysical[$a]['hivAutoimmuneDesc'];	
						$historyCancer = $patientHistoryPhysical[$a]['historyCancer'];	
						$historyCancerDesc = $patientHistoryPhysical[$a]['historyCancerDesc'];	
						$organTransplant = $patientHistoryPhysical[$a]['organTransplant'];	
						$organTransplantDesc = $patientHistoryPhysical[$a]['organTransplantDesc'];	
						$badReaction = $patientHistoryPhysical[$a]['badReaction'];	
						$badReactionDesc = $patientHistoryPhysical[$a]['badReactionDesc'];	
						$otherHistoryPhysical = $patientHistoryPhysical[$a]['otherHistoryPhysical'];
						$heartExam = $patientHistoryPhysical[$a]['heartExam'];
						$heartExamDesc = $patientHistoryPhysical[$a]['heartExamDesc'];	
						$lungExam	= $patientHistoryPhysical[$a]['lungExam'];		
						$lungExamDesc = $patientHistoryPhysical[$a]['lungExamDesc'];
						$discussedAdvancedDirective = $patientHistoryPhysical[$a]['discussedAdvancedDirective'];
						$highCholesterol = $patientHistoryPhysical[$a]['highCholesterol'];		
						$highCholesterolDesc = $patientHistoryPhysical[$a]['highCholesterolDesc'];
						$thyroid	= $patientHistoryPhysical[$a]['thyroid'];		
						$thyroidDesc = $patientHistoryPhysical[$a]['thyroidDesc'];
						$ulcer	= $patientHistoryPhysical[$a]['ulcer'];		
						$ulcerDesc = $patientHistoryPhysical[$a]['ulcerDesc'];
						$createDateTime = $patientHistoryPhysical[$a]['createDateTime'];
						$saveDateTime = $patientHistoryPhysical[$a]['saveDateTime'];
							
						$whereQueryHistoryPhysical = "";
						$selectQry = "select * from iolink_history_physical where patient_id = $demoPatientInstId and pt_waiting_id = $demoPatientInWtId";
						$rsSelectQry = imw_query($selectQry) or $msgInfo[] = $selectQry.imw_error();
						$numRowSelectQry = imw_num_rows($rsSelectQry);						
						if($numRowSelectQry==0){					
							$insertQueryHistoryPhysical = "insert into iolink_history_physical set ";					
						}
						else{					
							$insertQueryHistoryPhysical = "update iolink_history_physical set ";
							$whereQueryHistoryPhysical = "where patient_id = $demoPatientInstId and pt_waiting_id = $demoPatientInWtId";
						}
						
						$insertQueryHistoryPhysical .= "cadMI = '".addslashes($cadMI)."',
												cadMIDesc = '".addslashes($cadMIDesc)."',
												cvaTIA = '".addslashes($cvaTIA)."',
												cvaTIADesc = '".addslashes($cvaTIADesc)."',
												htnCP = '".addslashes($htnCP)."',
												htnCPDesc = '".addslashes($htnCPDesc)."',												
												anticoagulationTherapy = '".addslashes($anticoagulationTherapy)."',
												anticoagulationTherapyDesc = '".addslashes($anticoagulationTherapyDesc)."',
												respiratoryAsthma = '".addslashes($respiratoryAsthma)."',
												respiratoryAsthmaDesc = '".addslashes($respiratoryAsthmaDesc)."',
												arthritis = '".addslashes($arthritis)."',
												arthritisDesc = '".addslashes($arthritisDesc)."',
												diabetes = '".addslashes($diabetes)."',
												diabetesDesc = '".addslashes($diabetesDesc)."',
												recreationalDrug = '".addslashes($recreationalDrug)."',
												recreationalDrugDesc = '".addslashes($recreationalDrugDesc)."',
												giGerd = '".addslashes($giGerd)."',
												giGerdDesc = '".addslashes($giGerdDesc)."',
												ocular = '".addslashes($ocular)."',
												ocularDesc = '".addslashes($ocularDesc)."',
												kidneyDisease = '".addslashes($kidneyDisease)."',
												kidneyDiseaseDesc = '".addslashes($kidneyDiseaseDesc)."',
												hivAutoimmune = '".addslashes($hivAutoimmune)."',
												hivAutoimmuneDesc = '".addslashes($hivAutoimmuneDesc)."',
												historyCancer = '".addslashes($historyCancer)."',
												historyCancerDesc = '".addslashes($historyCancerDesc)."',
												organTransplant = '".addslashes($organTransplant)."',
												organTransplantDesc = '".addslashes($organTransplantDesc)."',
												badReaction = '".addslashes($badReaction)."',
												badReactionDesc = '".addslashes($badReactionDesc)."',
												otherHistoryPhysical = '".addslashes($otherHistoryPhysical)."',												
												heartExam = '".addslashes($heartExam)."',
												heartExamDesc = '".addslashes($heartExamDesc)."',
												lungExam = '".addslashes($lungExam)."',
												lungExamDesc = '".addslashes($lungExamDesc)."',
												highCholesterol = '".addslashes($highCholesterol)."',
												highCholesterolDesc = '".addslashes($highCholesterolDesc)."',
												thyroid = '".addslashes($thyroid)."',
												thyroidDesc = '".addslashes($thyroidDesc)."',
												ulcer = '".addslashes($ulcer)."',
												ulcerDesc = '".addslashes($ulcerDesc)."',
												discussedAdvancedDirective = '".addslashes($discussedAdvancedDirective)."',
												form_status = 'not completed',
												version_num = 4,
												version_date_time = '".addslashes(date('Y-m-d H:i:s'))."',
												create_date_time = '".addslashes($createDateTime)."',
												save_date_time = '".addslashes($saveDateTime)."',
												pt_waiting_id = '".addslashes($demoPatientInWtId)."',
												patient_id = '".$demoPatientInstId."'
											";	
						$insertQueryHistoryPhysical .= $whereQueryHistoryPhysical;
						
						$insertQueryHistoryPhysicalId = imw_query($insertQueryHistoryPhysical) or $msgInfo[] = $insertQueryHistoryPhysical.imw_error();
						if($insertQueryHistoryPhysicalId){
							$b++;
						}
						$a++;
						
					}
					if ($b == count($patientHistoryPhysical)){
						$patientHistoryPhysicalDataSaveFlag = true;
					}
					else{
						//break;
					}
					//end Saving History Physical
					
					//Saving History Physical Custom Questions
					$patientHPQuesDataXML = $iolinkMainData[26];
					$tag = 'patientHPQuesDataChild';
					$patientHPQues = array();
					$patientHPQues = XMLtoArray($patientHPQuesDataXML,$tag);
					//print_r($patientHPQues);				
					$a = 0;
					$b = 0;
					while ($a < count($patientHPQues)) {						
						$adminQuestionSid		= $patientHPQues[$a]['HPQuestionSid'];
						$adminQuestion 			= $patientHPQues[$a]['HPQuestion'];
						$adminQuestionStatus	= $patientHPQues[$a]['HPQuestionStatus'];
						$adminQuestionDesc		= $patientHPQues[$a]['HPQuestionDesc'];			
						$whereQueryHPQues = "";
						$selectQry = "select * from iolink_history_physical_ques where patient_id = $demoPatientInstId and pt_waiting_id = $demoPatientInWtId and ques = '$adminQuestion'";
						$rsSelectQry = imw_query($selectQry) or $msgInfo[] = $selectQry.imw_error();
						$numRowSelectQry = imw_num_rows($rsSelectQry);						
						if($numRowSelectQry==0){					
							$insertQueryHPQues = "insert into iolink_history_physical_ques set ";					
						}
						else{					
							$insertQueryHPQues = "update iolink_history_physical_ques set ";
							$whereQueryHPQues = "where patient_id = $demoPatientInstId and pt_waiting_id = $demoPatientInWtId and ques = '$adminQuestion'";
						}
						$insertQueryHPQues .= "ques = '".addslashes($adminQuestion)."',
														ques_status = '".addslashes($adminQuestionStatus)."',	
														ques_desc = '".addslashes($adminQuestionDesc)."',
														source = 'imw',
														pt_waiting_id = '".$demoPatientInWtId."',
														patient_id = '$demoPatientInstId'										
														";			
						$insertQueryHPQues .= $whereQueryHPQues;
						$insertQueryHPQuesRsId = imw_query($insertQueryHPQues) or $msgInfo[] = $insertQueryHPQues.imw_error();						
						if($insertQueryHPQuesRsId){
							$b++;
						}
						$a++;
						
					}
					if ($b == count($patientHPQues)){
						$patientHPQuesDataSaveFlag = true;
					}
					else{
						//break;
					}
				
				//START
				//saving Sx Alerts for patient --- Start
				$patientSxAlertDataXML = $iolinkMainData[17];
				$tag = 'sxAlertDataChild';
				$pracSxAlertData = array();
				$pracSxAlertData = XMLtoArray($patientSxAlertDataXML,$tag);
				//print_r($pracSxAlertData);
				//exit;				
				$a = 0;
				$b = 0;
				while ($a < count($pracSxAlertData)) {						
					$imw_alert_id 	= $pracSxAlertData[$a]['alertId'];
					$alert_content 		= $pracSxAlertData[$a]['alertContent'];
					$save_date_time 	= $pracSxAlertData[$a]['saveDateTime'];
					
					
					$whereQuerySxAlert = "";
					$selectQry = "select * from iolink_patient_alert_tbl where patient_id = '".$demoPatientInstId."' and imw_alert_id = '".$imw_alert_id."'";
					$rsSelectQry = imw_query($selectQry) or $msgInfo[] = $insertQuerySxAlert.imw_error();
					$numRowSelectQry = imw_num_rows($rsSelectQry);						
					if($numRowSelectQry==0){					
						$insertQuerySxAlert = " insert into iolink_patient_alert_tbl set ";					
					}
					else{					
						$insertQuerySxAlert = " update iolink_patient_alert_tbl set ";
						$whereQuerySxAlert = " where patient_id = '".$demoPatientInstId."' and imw_alert_id = '".$imw_alert_id."' ";
					}
					$insertQuerySxAlert .= "imw_alert_id = '".$imw_alert_id."',													
													patient_id = '".$demoPatientInstId."',
													alert_content = '".addslashes($alert_content)."',																								
													save_date_time = '".$save_date_time."' 										
													";			
					$insertQuerySxAlert .= $whereQuerySxAlert;
					$insertQuerySxAlertRsId = imw_query($insertQuerySxAlert) or $msgInfo[] = $insertQuerySxAlert.imw_error();						
					if($insertQuerySxAlertRsId){
						$b++;
					}
					$a++;
					
				}
				if ($b == count($pracSxAlertData)){
					$patientSxAlertDataSaveFlag = true;
					
				}
				else{
					//break;
				}
				/*
				echo '<br>'.$demographicDataSaveFlag.'<br>'.$patientInWatingDataSaveFlag.'<br>'.$insurenceDataSaveFlag.'<br>'.$insurenceCaseSaveFlag.'<br>'.$InsurenceScanDocSaveFlag;
				echo '<br>'.$consentDataSaveFlag.'<br>'.$consentSigDataSaveFlag.'<br>'.$scanDataSaveFlag.'<br>'.$pdfDataSaveFlag.'<br>'.$pdfDataSaveFlag;
				echo '<br>'.$faceSheetDataSaveFlag.'<br>'.$patientPreOpHealthQuesDataSaveFlag.'<br>'.$patientAdminHealthQuesDataSaveFlag.'<br>'.$patientHealthQuesAllergyDataSaveFlag.'<br>'.$patientAllergyNoValueDataSaveFlag.'<br>'.$patientHealthQuesMedDataSaveFlag;
				echo '<br>'.$oculerHxPdfDataSaveFlag.'<br>'.$patientSxAlertDataSaveFlag.'<br>'.$.'<br>'.$aScanPdfDataSaveFlag.'<br>'.$iolMasterPdfDataSaveFlag.'<br>'.$genHealthPdfDataSaveFlag;
				die();
				*/
				//saving Sx Alerts for patient --- End
				//END
				
				if($demographicDataSaveFlag === true && $patientInWatingDataSaveFlag === true
					&& $insurenceDataSaveFlag === true && $insurenceCaseSaveFlag === true && $InsurenceScanDocSaveFlag === true 
					&& $consentDataSaveFlag === true
					&& 	$consentSigDataSaveFlag === true && $scanDataSaveFlag === true
					&& 	$pdfDataSaveFlag === true && $faceSheetDataSaveFlag === true
					&& $patientPreOpHealthQuesDataSaveFlag === true
					&& $patientAdminHealthQuesDataSaveFlag === true
					&& $patientHealthQuesAllergyDataSaveFlag === true
					&& $patientAllergyNoValueDataSaveFlag === true
					&& $patientHealthQuesMedDataSaveFlag === true
					&& $oculerHxPdfDataSaveFlag === true
					&& $patientSxAlertDataSaveFlag === true
					&& $aScanPdfDataSaveFlag === true
					&& $iolMasterPdfDataSaveFlag === true
					&& $iolMasterDataSaveFlag === true
					&& $genHealthPdfDataSaveFlag === true
				  && $sxPdfDataSaveFlag === true
					//&& $historyPhysicalPdfDataSaveFlag = true 
					&& $patientHistoryPhysicalDataSaveFlag === true
					&& $patientHPQuesDataSaveFlag === true
				){
					$response = $demoPatientInstId.'@@@@@'.$demoPatientInWtId.'@@@@@'.'1';
				}elseif($boolBookAppt===false) {
					//MSG = SCHEDULE NOT AVAILABLE
					$response = $demoPatientInstId.'@@@@@'.$demoPatientInWtId.'@@@@@'.'2';
				}else{
					$response = $demoPatientInstId.'@@@@@'.$demoPatientInWtId.'@@@@@'.'0';
				}
				echo $response;
				if(count($msgInfo)>0) {
					echo(implode("@@@@@",$msgInfo));
				}
			}
			else{
				/*
				echo '<br>'.$demographicDataSaveFlag.'<br>'.$patientInWatingDataSaveFlag.'<br>'.$insurenceDataSaveFlag.'<br>'.$insurenceCaseSaveFlag.'<br>'.$InsurenceScanDocSaveFlag;
				echo '<br>'.$consentDataSaveFlag.'<br>'.$consentSigDataSaveFlag.'<br>'.$scanDataSaveFlag.'<br>'.$pdfDataSaveFlag.'<br>'.$pdfDataSaveFlag;
				echo '<br>'.$faceSheetDataSaveFlag.'<br>'.$patientPreOpHealthQuesDataSaveFlag.'<br>'.$patientAdminHealthQuesDataSaveFlag.'<br>'.$patientHealthQuesAllergyDataSaveFlag.'<br>'.$patientAllergyNoValueDataSaveFlag.'<br>'.$patientHealthQuesMedDataSaveFlag;
				echo '<br>'.$oculerHxPdfDataSaveFlag.'<br>'.$patientSxAlertDataSaveFlag.'<br>'.$aScanPdfDataSaveFlag.'<br>'.$iolMasterPdfDataSaveFlag.'<br>'.$genHealthPdfDataSaveFlag;
				die();
				*/
				
				if($demographicDataSaveFlag === true && $patientInWatingDataSaveFlag === true
				&& $insurenceDataSaveFlag === true && $insurenceCaseSaveFlag === true && $InsurenceScanDocSaveFlag === true
				&& $consentDataSaveFlag === true
				&& 	$consentSigDataSaveFlag === true && $scanDataSaveFlag === true
				&& 	$pdfDataSaveFlag === true && $faceSheetDataSaveFlag === true
				&& $patientPreOpHealthQuesDataSaveFlag === true
				&& $patientAdminHealthQuesDataSaveFlag === true
				&& $patientHealthQuesAllergyDataSaveFlag === true
				&& $patientAllergyNoValueDataSaveFlag === true
				&& $patientHealthQuesMedDataSaveFlag === true
				&& $oculerHxPdfDataSaveFlag === true
				&& $patientSxAlertDataSaveFlag === true
				&& $aScanPdfDataSaveFlag === true
				&& $iolMasterPdfDataSaveFlag === true
				&& $iolMasterDataSaveFlag === true
				&& $genHealthPdfDataSaveFlag === true
				&& $sxPdfDataSaveFlag === true
				//&& $historyPhysicalPdfDataSaveFlag = true	 
				&& $patientHistoryPhysicalDataSaveFlag === true
				&& $patientHPQuesDataSaveFlag === true	 
				){
					$response = $demoPatientInstId.'@@@@@'.$demoPatientInWtId.'@@@@@'.'1';
				}else if($boolBookAppt==false) {
					//MSG = SCHEDULE NOT AVAILABLE
					$response = $demoPatientInstId.'@@@@@'.$demoPatientInWtId.'@@@@@'.'2';
				}else{
					$response = $demoPatientInstId.'@@@@@'.$demoPatientInWtId.'@@@@@'.'0';
					
				}
				echo $response;
				if(count($msgInfo)>0) {
					echo(implode("@@@@@",$msgInfo));
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
					
					
					//START CODE TO DELETE INSURANCE-SCAN FROM INSURANCE-DATA
					$qryGetIOlinkInsScanData = "select scan_card,scan_card2 from insurance_data where patient_id = '".$ioPtId."'";
					$rsGetIOlinkInsScanData = imw_query($qryGetIOlinkInsScanData);
					while($rowGetIOlinkInsScanData = imw_fetch_array($rsGetIOlinkInsScanData)){						
						$httpfolder =  realpath(dirname(__FILE__));				
						$deletePathScanData1 = $httpfolder."/../imedic_uploaddir".$rowGetIOlinkInsScanData['scan_card'];
						$deletePathScanData2 = $httpfolder."/../imedic_uploaddir".$rowGetIOlinkInsScanData['scan_card2'];
						if(file_exists($deletePathScanData1)) {unlink($deletePathScanData1);}
						if(file_exists($deletePathScanData2)) {unlink($deletePathScanData2);}	
					}					
					
					$qryDelIOlinkPatientInsurenceDetail = "delete from insurance_data where patient_id = ".$ioPtId;
					$rsDelIOlinkPatientInsurenceDetail = imw_query($qryDelIOlinkPatientInsurenceDetail);
					//END CODE TO DELETE INSURANCE-SCAN FROM INSURANCE-DATA
					

					$qryDelIOlinkPatientInsurenceCaseDetail = "delete from iolink_insurance_case where patient_id = ".$ioPtId;
					$rsDelIOlinkPatientInsurenceCaseDetail = imw_query($qryDelIOlinkPatientInsurenceCaseDetail);

					//START CODE TO DELETE INSURANCE-SCAN FROM INSURANCE-SCAN-DOCS
					$qryGetIOlinkInsScanDoc = "select scan_card,scan_card2 from iolink_insurance_scan_documents where patient_id = '".$ioPtId."'";
					$rsGetIOlinkInsScanDoc = imw_query($qryGetIOlinkInsScanDoc);
					while($rowGetIOlinkInsScanDoc = imw_fetch_array($rsGetIOlinkInsScanDoc)){						
						$httpfolder =  realpath(dirname(__FILE__));				
						$deletePathScanDoc1 = $httpfolder."/../imedic_uploaddir".$rowGetIOlinkInsScanDoc['scan_card'];
						$deletePathScanDoc2 = $httpfolder."/../imedic_uploaddir".$rowGetIOlinkInsScanDoc['scan_card2'];
						if(file_exists($deletePathScanDoc1)) {unlink($deletePathScanDoc1);}
						if(file_exists($deletePathScanDoc2)) {unlink($deletePathScanDoc2);}	
					}					
					$qryDelIOlinkPatientInsurenceScanDetail = "delete from iolink_insurance_scan_documents where patient_id = '".$ioPtId."'";
					$rsDelIOlinkPatientInsurenceScnaDetail = imw_query($qryDelIOlinkPatientInsurenceScanDetail);
					//END CODE TO DELETE INSURANCE-SCAN FROM INSURANCE-SCAN-DOCS
					
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
						$deletePath = $httpfolder."/../admin/".$rowGetIOlinkPatientPdfpath[pdfFilePath];
						unlink($deletePath);
					}					
					$qryDelIOlinkScanDetail = "delete from iolink_scan_consent where patient_id = ".$ioPtId." and patient_in_waiting_id = ".$ioPtWtId;
					$rsDelIOlinkScanDetail = imw_query($qryDelIOlinkScanDetail);
					
					$qryGetIOlinkPatientPreOpSigPath = "select patient_sign_image_path,witness_sign_image_path from iolink_preophealthquestionnaire where patient_id = ".$ioPtId." and patient_in_waiting_id = ".$ioPtWtId;
					$rsGetIOlinkPatientPreOpSigPath = imw_query($qryGetIOlinkPatientPreOpSigPath);
					while($rowGetIOlinkPatientPreOpSigPath = imw_fetch_array($rsGetIOlinkPatientPreOpSigPath)){						
						$httpfolder =  realpath(dirname(__FILE__));				
						$patientPreOpSigPath = $rowGetIOlinkPatientPreOpSigPath['patient_sign_image_path'];
						$witnessPreOpSigPath = $rowGetIOlinkPatientPreOpSigPath['witness_sign_image_path'];
						if(trim($patientPreOpSigPath) != ""){
							$patientdeletePath = $httpfolder."/../".trim($patientPreOpSigPath);
							unlink($patientdeletePath);
						}	
						if(trim($witnessPreOpSigPath) != ""){
							$witnessdeletePath = $httpfolder."/../".trim($witnessPreOpSigPath);
							unlink($witnessdeletePath);
						}						
					}	

					$qryDelPatientPreOpDetail = "delete from iolink_preophealthquestionnaire where patient_id = ".$ioPtId." and patient_in_waiting_id = ".$ioPtWtId;
					$rsDelIPatientPreOpDetail = imw_query($qryDelPatientPreOpDetail);
					
					$qryDelPatientPreOpAdminDetail = "delete from iolink_healthquestionadmin where patient_id = ".$ioPtId." and patient_in_waiting_id = ".$ioPtWtId;
					$rsDelIPatientPreOpAdminDetail = imw_query($qryDelPatientPreOpAdminDetail);
					
					$qryDelPatientAllergDetail	= "delete from iolink_patient_allergy where patient_id = ".$ioPtId." and patient_in_waiting_id = ".$ioPtWtId;
					$rsDelPatientAllergDetail		= imw_query($qryDelPatientAllergDetail);
					
					$qryDelPatientMedicationDetail = "delete from iolink_patient_prescription_medication where patient_id = ".$ioPtId." and patient_in_waiting_id = ".$ioPtWtId;
					$rsDelPatientMedicationDetail = imw_query($qryDelPatientMedicationDetail);
					
					$qryDelIolManufacturerDetail	=	"delete from iolink_iol_manufacturer where patient_id = ".$ioPtId." and patient_in_waiting_id = ".$ioPtWtId;
					$rsDelIolManufacturerDetail	=	imw_query($qryDelIolManufacturerDetail);
					
					echo $rsDelIOlinkPatientdetail.'@@@@@'.$rsDelIOlinkPatientWtdetail;
				}
			}
		}
	}
}
else if($iolinkSync=="get_db_name"){
	$qryDbName="select database() as dbname from dual";
	$resDbName=imw_query($qryDbName);	
	$rowDbName=imw_fetch_assoc($resDbName);
	echo $rowDbName['dbname'];

}

function XMLtoArray($xml,$tag){
	$parser = xml_parser_create('ISO-8859-1'); // For Latin-1 charset
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); // Dont mess with my cAsE sEtTings
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); // Dont bother with empty info
	//xml_parse_into_struct($parser, $xml, $values);
	$ok = xml_parse_into_struct($parser, $xml, $values);
	if (!$ok) {
		$errmsg = sprintf("XML parse error %d '%s' at line %d, column %d (byte index %d)",
		xml_get_error_code($parser),
		xml_error_string(xml_get_error_code($parser)),
		xml_get_current_line_number($parser),
		xml_get_current_column_number($parser),
		xml_get_current_byte_index($parser));
		//echo 'XML ERROR - '.$errmsg;
	}
	
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