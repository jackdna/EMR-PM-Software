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
File: demographics_save.php
Purpose: Saves patient's demographics information
Access Type: Direct 
*/
require_once("../../../config/globals.php");	
require_once("../../../library/classes/cls_common_function.php");	
require_once("../../../library/classes/SaveFile.php");	

if(isset($_REQUEST['edit_patient_id']) && $_REQUEST['edit_patient_id']!='' && $_REQUEST['edit_patient_id'] > 0 && $_REQUEST['edit_patient_id']!=$_SESSION['patient']) {
    $_SESSION['patient'] = $_REQUEST['edit_patient_id'];
}
$pid = $_SESSION['patient'];

$OBJCommonFunction = new CLSCommonFunction;
$save_file = new SaveFile($pid);

$status_updated = false;
if ((isset($_REQUEST["elem_patientStatus"]) && $_REQUEST["elem_patientStatus"] != '') ) {
    $pt_sql = "SELECT id,patientStatus FROM patient_data where id='$pid'";
    $pt_rs = imw_query($pt_sql);
    if ($pt_rs && imw_num_rows($pt_rs) == 1) {
        $row = imw_fetch_assoc($pt_rs);
        
        if (trim($row['patientStatus']) != trim($_REQUEST["elem_patientStatus"])) {
            $status_updated = true;
        }
	}
	

	//Cancel all future appointments if patient status changed to deceased
	if( $status_updated && $_REQUEST["elem_patientStatus"] === 'Deceased' ) {
		cancel_future_appointments($pid);
	}
}

//----------BEGIN ADD MULTIPLE ADDRESSES--------------
	$address_cnt = max(array_keys($_REQUEST['street']));
	$all_communication_index = (isset($_REQUEST['all_communication']))? $_REQUEST['all_communication']:"0";
	for($i=0; $i<=$address_cnt; $i++)
	{
		$flag_add = false;
		if($_POST['postal_code'][$i]!=""
			|| $_POST['street'][$i]
			|| $_POST['street2'][$i]
			|| $_POST['postal_code'][$i]
			|| $_POST['zip_ext'][$i]
			|| $_POST['city'][$i]
			|| $_POST['state'][$i]
			|| $_POST['county'][$i]
			//|| $_POST['country_code'][$i]
			){
				$flag_add = true;
			}
		if(($_POST['id_address'][$i] == "" || $_POST['id_address'][$i] == "0") && ($i ==0 || ($i!=0 && ($_POST['postal_code'][$i]!="" || $flag_add)))){	
			$qry = "INSERT INTO patient_multi_address 
				  SET street = '".imw_real_escape_string($_POST['street'][$i])."',
				  street2 = '".imw_real_escape_string($_POST['street2'][$i])."',
				  postal_code = '".imw_real_escape_string($_POST['postal_code'][$i])."',
				  zip_ext = '".imw_real_escape_string($_POST['zip_ext'][$i])."',
				  city = '".imw_real_escape_string($_POST['city'][$i])."',
				  state = '".imw_real_escape_string($_POST['state'][$i])."',
				  country_code = '".imw_real_escape_string($_POST['country_code'][$i])."',
				  patient_id  = '".$pid."',
				  county = '".imw_real_escape_string($_POST['county'][$i])."',
					del_status = '0'
				";
			imw_query($qry) or die(imw_error());
			$address_id = imw_insert_id();
			if($i == $all_communication_index)
			$default_address_id = $address_id;
		}else if($_POST['postal_code'][$i] != "" || $flag_add){
			$qry = "UPDATE patient_multi_address 
			  SET street = '".imw_real_escape_string($_POST['street'][$i])."',
			  street2 = '".imw_real_escape_string($_POST['street2'][$i])."',
			  postal_code = '".imw_real_escape_string($_POST['postal_code'][$i])."',
			  zip_ext = '".imw_real_escape_string($_POST['zip_ext'][$i])."',
			  city = '".imw_real_escape_string($_POST['city'][$i])."',
			  state = '".imw_real_escape_string($_POST['state'][$i])."',
			  country_code = '".imw_real_escape_string($_POST['country_code'][$i])."',
			  patient_id  = '".$pid."',
			  county = '".imw_real_escape_string($_POST['county'][$i])."'
			  WHERE id = '".$_POST['id_address'][$i]."'
			";
			imw_query($qry) or die(imw_error());
			$address_id = $_POST['id_address'][$i];
		}
		//echo "i ==".$i."  all_communication=";echo $_REQUEST['all_communication']."<br>";
		if($i == $all_communication_index){
			$street = $_POST['street'][$i];
			$street2 = $_POST['street2'][$i];
			$postal_code = $_POST['postal_code'][$i];
			$zip_ext = $_POST['zip_ext'][$i];
			$city = $_POST['city'][$i];
			$state = $_POST['state'][$i];
			$country_code = $_POST['country_code'][$i];
			$county = $_POST['county'][$i];
			if($_POST['id_address'][$i] != "" && $_POST['id_address'][$i] != "0")
			$default_address_id = $_POST['id_address'][$i];
		}
	}
	$address_del_id = trim($_REQUEST['address_del_id'],",");
	if($address_del_id != ""){
		imw_query("UPDATE patient_multi_address SET del_status = 1 WHERE id IN (".$address_del_id.")");
	}
//----------END ADD MULTIPLE ADDRESSES--------------
	

$strProvidersToRestrictDemographics = @implode(',',$_REQUEST["providersToRestrictDemographics"]);

if(empty($_REQUEST["chk_mobile"])){
	$_REQUEST["chk_mobile"] = 0;
}

$_REQUEST['oldpatient'] = NULL;	

$add_new_patient = false;
if(empty($pid) == true){
	$add_new_patient = true;
}
$main = $_POST['main'];
$careName = $_POST['pcare'];
//////////

$arrAuditTrail = array();
$arrAuditTrail = unserialize(urldecode($_REQUEST['hidData']));

//saving referring  phy.

//--- CHANGE DATE FORMAT ----


$dod_patient = getDateFormatDB($dod_patient);
$dod_patient = empty($dod_patient) ? '0000-00-00' : $dod_patient;

$refPhyNameFullNew = NULL;
if($_REQUEST['pcare'] == "")
{
	if($_REQUEST['pcare2']){
		$arrTemp = explode(";", $_REQUEST['pcare2']);
		$_REQUEST['pcare2'] = trim($arrTemp[0]);
		$intRefPhyId = 0;
		$strRefPhyName = "";
		list($intRefPhyId, $strRefPhyName) = $OBJCommonFunction->chk_create_ref_phy($_REQUEST['pcare2'],2);
		if((empty($intRefPhyId) == false) && (empty($strRefPhyName) == false)){
			$pcare = $intRefPhyId;
			$_REQUEST['pcare'] = $pcare;
			$_REQUEST['pcare2'] = $strRefPhyName;
			$refPhyNameFullNew = trim($strRefPhyName);
		}	
	}
}	
else{
	$pcare = $_REQUEST['pcare'];
	$arrTemp = explode(";", $_REQUEST['pcare2']);
	$_REQUEST['pcare2'] = trim($arrTemp[0]);
}

//saving Primary Care Phy. 
$priCarePhyNameFullNew = NULL;

if($_REQUEST['pCarePhy'] == 0 || $_REQUEST['pCarePhy'] == ''){
	if($_REQUEST['primaryCarePhy']){
		$arrTemp = explode(";", $_REQUEST['primaryCarePhy']);
		$_REQUEST['primaryCarePhy'] = trim($arrTemp[0]);
		$intPCPId = 0;
		$strPCPName = "";
		list($intPCPId, $strPCPName) = $OBJCommonFunction->chk_create_ref_phy($_REQUEST['primaryCarePhy']);
		if((empty($intPCPId) == false) && (empty($strPCPName) == false)){
			$pCarePhy = $intPCPId;
			$_REQUEST['pCarePhy'] = $pCarePhy;
			$priCarePhyNameFullNew = trim($strPCPName);	
		}
	}
}	
else{
	$pCarePhy = $_REQUEST['pCarePhy'];
	$arrTemp = explode(";", $_REQUEST['primaryCarePhy']);
	$_REQUEST['primaryCarePhy'] = trim($arrTemp[0]);
	$priCarePhyNameFullNew = trim($_REQUEST['primaryCarePhy']);	
}

$CoManPhyNameFullNew = NULL;

if($_REQUEST['co_man_phy_id'] == 0 || $_REQUEST['co_man_phy_id'] == ''){
	if($_REQUEST['co_man_phy']){
		$arrTemp = explode(";", $_REQUEST['co_man_phy']);
		$_REQUEST['co_man_phy'] = trim($arrTemp[0]);
		
		$CoManPhyNameArr = explode(',',$_REQUEST['co_man_phy']);
		$strCoManPhyFName = trim($CoManPhyNameArr[1]);
		
		$arrCoManPhyFirstName = array();
		$arrCoManPhyFirstName = explode(' ',$strCoManPhyFName);
		$strCoManPhyFName = trim($arrCoManPhyFirstName[0]);
		
		$strCoManPhyLName = preg_split('/,/',trim($CoManPhyNameArr[0]));
		$strCoManPhyLName = preg_split('/ /',trim($CoManPhyNameArr[0]));
		$strCoManPhyLName = trim(end($strCoManPhyLName));
		
		$rowCoManPhy = get_reffer_physician_id('FirstName',$strCoManPhyFName,'LastName',$strCoManPhyLName);
		
		$CoManPhyNameFullNew = $rowCoManPhy['Title']." ".$rowCoManPhy['LastName'].", ".$rowCoManPhy['FirstName'];	
		$CoManPhyNameFullNew = trim($CoManPhyNameFullNew);	
		
		$co_man_phy_id = $rowCoManPhy['physician_Reffer_id'];
		if($co_man_phy_id){
			$_REQUEST['co_man_phy_id'] = $co_man_phy_id;
		}		
	}
}	
else{
	$co_man_phy_id = $_REQUEST['co_man_phy_id'];
	$arrTemp = explode(";", $_REQUEST['co_man_phy']);
	$_REQUEST['co_man_phy'] = trim($arrTemp[0]);
	$CoManPhyNameFullNew = trim($_REQUEST['co_man_phy']);	
}
//--- CHANGE DATE FORMAT -----
$date_convert = getDateFormatDB($dob);
$date_conv = getDateFormatDB($financial_review);
$date_conv = empty($date_conv) ? '0000-00-00 00:00:00' :	$date_conv ;

$vip = $_REQUEST['vip'];
$h_statement = $_REQUEST['h_statement'];
$noBalBill = $_REQUEST['noBalBill'];
$erx_entry = $_REQUEST['erx_entry'];
$chkErxAsk = $_REQUEST['chkErxAsk'];

$preferred_phone = 0;
if(trim($_REQUEST['pf_contact']) != "")
{
	$preferred_phone = $_REQUEST['pf_contact'];
}

if($emr == "1"){
	$EMR = "EMR='1' ,";
	$emr_val = 1;
}else{
	$EMR = "EMR='0' ,";
	$emr_val = 0;
}

$patientStatus = $_POST["elem_patientStatus"];
$ptInfoCollapseStatus = $_REQUEST["ptInfoCollapseStatus"];
$resPartyCollapseStatus = $_REQUEST["resPartyCollapseStatus"];
$ptOccCollapseStatus = $_REQUEST["ptOccCollapseStatus"];
$miscCollapseStatus = $_REQUEST["miscCollapseStatus"];
$zipCodeStatus = $_REQUEST["zipCodeStatus"];

if(inter_country() != "UK") 
$state = correct_state_name($state);

if($zipCodeStatus == 'NA'){
	$zipDataArr = array();
	$zipDataArr["zip_code"] = $_REQUEST["postal_code"];
	$zipDataArr["zip_ext"] = $_REQUEST["zip_ext"];
	$zipDataArr["city"] = $_REQUEST["city"];
	$zipDataArr["state_abb"] = $state;
	AddRecords($zipDataArr,"zip_codes");	
	
}else if($zipCodeStatus == 'NotOk'){
	$postal_code = NULL;
	$zip_ext = NULL;
	$city = NULL;
	$state = NULL;
}
if(inter_country() != "UK")
$postal_code = core_padd_char($postal_code,5);

$arrElemHeardAbtUs = explode("-",$_REQUEST['elem_heardAbtUs']);
$elem_heardAbtUs = addslashes($arrElemHeardAbtUs[0]);
$elem_heardAbtUsValue = addslashes($arrElemHeardAbtUs[1]);

if($_REQUEST['heardAbtOther'] != ''){
	$chkqryHeardMaster = "SELECT heard_id FROM heard_about_us WHERE heard_options = '".addslashes($_REQUEST['heardAbtOther'])."' limit 1";
	$rschkqryHeardMaster = imw_query($chkqryHeardMaster);
	if($rschkqryHeardMaster){
		if(imw_num_rows($rschkqryHeardMaster) == 0){
			$priv_query_part = ", for_all=0 ";
			$qryHeard = "Insert into heard_about_us set heard_options = '".addslashes($_REQUEST['heardAbtOther'])."'".$priv_query_part;
			$resHeard = imw_query($qryHeard);
			$elem_heardAbtUs = imw_insert_id();
		}
		else{
			$rowChkQryHeardMaster = imw_fetch_array($rschkqryHeardMaster);
			$elem_heardAbtUs = $rowChkQryHeardMaster['heard_id'];		
			imw_free_result($rschkqryHeardMaster);
		}	
	}
}

if(empty($pid) == true){
	$heardDate = date('Y-m-d');
}

if(in_array($elem_heardAbtUsValue,array('Family','Friends','Doctor','Previous Patient.','Previous Patient')) ) {
	$heardAbtDesc = '';
	
}
else {
	$heardAbtSearch = $heardAbtSearchId = ''; 
	
	$query_string = "SELECT id FROM heard_about_us_desc 
					WHERE heard_id = '$elem_heardAbtUs' and heard_desc = '$heardAbtDesc'";
	$sql = imw_query($query_string);
	$heardQryRes = imw_fetch_assoc($sql);
	$heard_id = $heardQryRes['id'];
	if(empty($heard_id) == true){
		$heardDataArr = array();
		$heardDataArr["heard_desc"] = $heardAbtDesc;
		$heardDataArr["heard_id"] = $elem_heardAbtUs;
		//$heardDataArr["timestamp"] = '0000-00-00 00:00:00';

		AddRecords($heardDataArr,"heard_about_us_desc");
	}
}
$previous_record=false;
if($pid != ""){
	////////ravi
	$vquery_c = "select street,street2,city,state,postal_code,ss, phone_home,phone_cell, erx_patient_id, title, fname, mname, lname, suffix, DOB, sex from patient_data where id = '$pid'";		
	$vsql = imw_query($vquery_c);
	$queryResult = imw_fetch_assoc($vsql);
    if(imw_num_rows($vsql)==1) {
        $previous_record=true;
    }
									
	$exitingStreet = $queryResult['street'];
	$exitingStreet2 = $queryResult['street2'];
	$exitingCity = $queryResult['city'];
	$exitingState = $queryResult['state'];
	$exitingZip = $queryResult['postal_code'];
	$exitingSsn = $queryResult['ss'];
	$erx_patient_id = $queryResult['erx_patient_id'];
	$exitingPhoneHome = $queryResult['phone_home'];
	$existingPhoneCell = $queryResult['phone_cell'];
	
	$exitingTitleName = $queryResult['title'];
	$exitingFirstName = $queryResult['fname'];
	$exitingMiddleName = $queryResult['mname'];
	$exitingLastName = $queryResult['lname'];
	$exitingSfxName = $queryResult['suffix'];
	$exitingMstatus = $queryResult['status'];
	
	$exitingDOB = $queryResult['DOB'];
	$exitingSex = $queryResult['sex'];
	
	//---------To update schedule appointments table
	$str_upd_appt = "UPDATE schedule_appointments SET ";
	$str_upd_appt_clause = "";
	if((trim($fname) != "" && (trim($fname) != trim($exitingFirstName))) || (trim($mname) != "" && (trim($mname) != trim($exitingMiddleName))) || (trim($lname) != "" && (trim($lname) != trim($exitingLastName)))){
		$str_sa_patient_name = "";
		$str_sa_patient_name = $lname != "" ? $lname.", " : "";
		$str_sa_patient_name .= $fname != "" ? $fname : "";
		$str_sa_patient_name .= $mname != "" ? ' '.strtoupper(substr($mname,0,1)) : "";
		if($str_sa_patient_name != ""){
			$str_upd_appt_clause .= " sa_patient_name = '".$str_sa_patient_name."', ";
		}
	}
	if(trim($hid_emr) != "" && (trim($hid_emr) != trim($emr_val))){
		$str_upd_appt_clause .= " EMR = '".$emr_val."', ";
	}
	if($str_upd_appt_clause != ""){
		$str_upd_appt_clause = substr($str_upd_appt_clause, 0 ,-2);
		$str_upd_appt .= $str_upd_appt_clause;
		$str_upd_appt .= " WHERE sa_patient_id = '".$pid."'";
		imw_query($str_upd_appt);
	}
							
}else{
	
	$dpr_pt_data = array();
	$dpr_pt_data['ptfname'] 	= convertUcfirst($fname);
	$dpr_pt_data['ptlname'] 	= convertUcfirst($lname);
	$dpr_pt_data['ptdob'] 		= $date_convert;
	$dpr_pt_data['ptgender'] 	= $sex;
	$dpr_pt_data['ptzip'] 		= $postal_code;
	$dpr_pt_data['ptzip_ext'] 	= $zip_ext;
	
	$arr_patient_next_id = get_Next_PatientID($dpr_pt_data);
	if($arr_patient_next_id['error']==''){
		$patient_next_id = $arr_patient_next_id['patient_id'];
		$patient_src_server = $arr_patient_next_id['src_server'];
	}else{
		echo $arr_patient_next_id['error'];exit;
	}
	
	$tdate = date("H:i:s");
	$create_date = getDateFormatDB($reg_date);
	$date = $create_date." ".$tdate;
	
	$patDataArr = array();
	$patDataArr["title"] = convertUcfirst($title);
	$patDataArr["fname"] = convertUcfirst($fname);
	$patDataArr["mname"] = convertUcfirst($mname);
	$patDataArr["lname"] = convertUcfirst($lname);
	$patDataArr["id"] = $patient_next_id;
	$patDataArr["pid"] = $patient_next_id;
	$patDataArr["date"] = date('Y-m-d H:i:s');
	$patDataArr["created_by"] = $_SESSION['authId'];
	if($patient_src_server>0){$patDataArr["src_server"] = $patient_src_server;}
	//Clear --
	clean_patient_session();

	//--- ADD NEW PATIENT ----
	$pid = AddRecords($patDataArr,"patient_data");
	$_SESSION['patient'] = $pid;
}

$tdate = date("H:i:s");
$create_date = getDateFormatDB($reg_date);
$date = $create_date." ".$tdate;

$lic_img = $_SESSION['scan_image'];
if($language=='Other' && $_REQUEST['otherLanguage']!=""){
	$language = "Other -- ".$_REQUEST['otherLanguage'];
}

// New Fields Sexual Orientation and Gender Identity
$arrSOR = sexual_orientation();
$arrGI = gender_identity();
$other_sor= ('Other' == $_REQUEST['sexual_orientation']) 	? addslashes(trim($_REQUEST['otherSOR'])) : '';
$other_gi = ('Other' == $_REQUEST['gender_identity']) 		? addslashes(trim($_REQUEST['otherGI'])) : '';
$sor_code	= $arrSOR[$_REQUEST['sexual_orientation']]['code'];
$gi_code 	= $arrGI[$_REQUEST['gender_identity']]['code'];

$vip=0;
if($_REQUEST['vip']==1){ $vip=1; }

$h_statement = 0;
if($_REQUEST['h_statement']==1){ $h_statement=1; }

$heardDate = date('Y-m-d');

$patDataArr = array();
$patDataArr["title"] = convertUcfirst($title);
$patDataArr["fname"] = convertUcfirst($fname);
$patDataArr["mname"] = convertUcfirst($mname);
$patDataArr["lname"] = convertUcfirst($lname);
$patDataArr["suffix"] = convertUcfirst($suffix);
$patDataArr["mname_br"] = convertUcfirst($birth_name);
$patDataArr["maiden_fname"] = convertUcfirst($maiden_fname);
$patDataArr["maiden_mname"] = convertUcfirst($maiden_mname);
$patDataArr["maiden_lname"] = convertUcfirst($maiden_lname);
$patDataArr["nick_name"] = addslashes(convertUcfirst($nick_name));
$patDataArr["phonetic_name"] = addslashes($phonetic_name);
$patDataArr["DOB"] = $date_convert;
$patDataArr["sex"] = $sex;
$patDataArr["primary_care"] = addslashes($_REQUEST['pcare2']);
$patDataArr["ss"] = $ss;
$patDataArr["EMR"] = $emr_val;
$patDataArr["street"] = convertUcfirst($street);
$patDataArr["street2"] = convertUcfirst($street2);
$patDataArr["city"] = convertUcfirst($city);
$patDataArr["state"] = ucwords($state);
$patDataArr["status"] = $status;
$patDataArr["postal_code"] = $postal_code;
$patDataArr["zip_ext"] = $zip_ext;
$patDataArr["country_code"] = $country_code;
$patDataArr["county"] = $county;
$patDataArr["contact_relationship"] = convertUcfirst($contact_relationship);
$patDataArr["phone_contact"] = core_phone_unformat($phone_contact);
$patDataArr["phone_home"] = core_phone_unformat($phone_home);
$patDataArr["phone_biz"] = core_phone_unformat($phone_biz);
$patDataArr["phone_biz_ext"] = $phone_biz_ext;
$patDataArr["phone_cell"] = core_phone_unformat($phone_cell);
$patDataArr["email"] = $ptDemoEmail;
$patDataArr["driving_licence"] = convertUcfirst($dlicence);
//$patDataArr["username"] = $usernm;
//$patDataArr["password"] = $pass1;
$patDataArr["providerId"] = $providerID;
//$patDataArr["assigned_nurse"] = $assigned_nurse;
$patDataArr["genericval1"] = $genericval1;
$patDataArr["genericval2"] = $genericval2;
$patDataArr["occupation"] = $occupation;
$patDataArr["financial_review"] = $date_conv;
$patDataArr["family_size"] = $family_size;
$patDataArr["monthly_income"] = $monthly_income;
$patDataArr["homeless"] = $homeless;
$patDataArr["interpreter_type"] = $interpreter_type;
$patDataArr["interpretter"] = $interpretter;
$patDataArr["migrantseasonal"] = $migrantseasonal;
$patDataArr["hipaa_mail"] = $hipaa_mail;
$patDataArr["hipaa_email"] = $hipaa_email;
$patDataArr["hipaa_voice"] = $hipaa_voice;
$patDataArr["hipaa_text"] = $hipaa_text;
$patDataArr["default_facility"] = $default_facility;
$patDataArr["language"] = $language;
$patDataArr["lang_code"] = $lang_code;
$patDataArr["ethnoracial"] = $ethnoracial;

// To filter out invalid characters from notes
if($patient_notes){
	$patient_notes = preg_replace( '/[^[:print:]]/', '', nl2br(html_entity_decode($patient_notes)));
	//$patient_notes = str_replace('<br />', PHP_EOL, $patient_notes);
	$patient_notes = str_replace('<br />', "\r\n", $patient_notes);
}

$patDataArr["patient_notes"] = $patient_notes;

// 3 - RMEOTE SYNC CODE REMOVED
$patDataArr["primary_care_id"] = $pcare;
$patDataArr["Sec_HCFA"] = $secHCFA;
$patDataArr["vip"] = $vip;
$patDataArr["hold_statement"] = $h_statement;
$patDataArr["preferr_contact"] = $preferred_phone;
$patDataArr["noBalanceBill"] = $noBalBill;
$patDataArr["erx_entry"] = $erx_entry;
$patDataArr["patientStatus"] = $elem_patientStatus != '' ? $elem_patientStatus : 'Active';
$patDataArr["otherPatientStatus"] = $otherPatientStatus;
$patDataArr["ptInfoCollapseStatus"] = $ptInfoCollapseStatus;
$patDataArr["resPartyCollapseStatus"] = $resPartyCollapseStatus;
$patDataArr["ptOccCollapseStatus"] = $ptOccCollapseStatus;
$patDataArr["miscCollapseStatus"] = $miscCollapseStatus;
$patDataArr["heard_abt_us"] = $elem_heardAbtUs;
$patDataArr["heard_abt_desc"] = $heardAbtDesc;
$patDataArr["heard_about_us_date"] = $heardDate;
$patDataArr["heard_abt_search"] = $heardAbtSearch;
$patDataArr["heard_abt_search_id"] = $heardAbtSearchId;
$patDataArr["relInfoName1"] = $relInfoName1;
$patDataArr["relInfoPhone1"] = core_phone_unformat($relInfoPhone1);
$patDataArr["relInfoReletion1"] = $relInfoReletion1;
$patDataArr["otherRelInfoReletion1"] = $otherRelInfoReletion1;
$patDataArr["relInfoComment1"] = $relInfoComment1;
$patDataArr["relInfoName2"] = $relInfoName2;
$patDataArr["relInfoPhone2"] = core_phone_unformat($relInfoPhone2);
$patDataArr["relInfoReletion2"] = $relInfoReletion2;
$patDataArr["otherRelInfoReletion2"] = $otherRelInfoReletion2;
$patDataArr["relInfoComment2"] = $relInfoComment2;
$patDataArr["relInfoName3"] = $relInfoName3;
$patDataArr["relInfoPhone3"] = core_phone_unformat($relInfoPhone3);
$patDataArr["relInfoReletion3"] = $relInfoReletion3;
$patDataArr["otherRelInfoReletion3"] = $otherRelInfoReletion3;
$patDataArr["relInfoComment3"] = $relInfoComment3;
$patDataArr["relInfoName4"] = $relInfoName4;
$patDataArr["relInfoPhone4"] = core_phone_unformat($relInfoPhone4);
$patDataArr["relInfoReletion4"] = $relInfoReletion4;
$patDataArr["otherRelInfoReletion4"] = $otherRelInfoReletion4;
$patDataArr["relInfoComment4"] = $relInfoComment4;
$patDataArr["emergencyRelationship"] = $emerRelation;
$patDataArr["emergencyRelationship_other"] = $relation_other_textbox;
$patDataArr["reportExemption"] = $reportExemption;
$patDataArr["race"]=(is_array($race))?implode(",",$race):$race;
$patDataArr["otherRace"]=(is_array($race)&& in_array("Other",$race))?$otherRace:"";
$patDataArr["ethnicity"]=(is_array($ethnicity))?implode(",",$ethnicity):$ethnicity;
$patDataArr["otherEthnicity"]=(is_array($ethnicity) && in_array("Other",$ethnicity))?$otherEthnicity:"";
$patDataArr["chk_mobile"] = $_REQUEST["chk_mobile"];
$patDataArr["ado_option"] = $_REQUEST["ado_option"];
$patDataArr["sor_txt"] = $_REQUEST['sexual_orientation'];
$patDataArr["other_sor"] = $other_sor;
$patDataArr["sor_code"] = $sor_code;
$patDataArr["gi_txt"] = $_REQUEST["gender_identity"];
$patDataArr["other_gi"] = $other_gi;
$patDataArr["gi_code"] = $gi_code;
$patDataArr["desc_ado_other_txt"] = $_REQUEST["ado_other_txt"];
$patDataArr["chk_notes_scheduler"] = $chkNotesScheduler;
$patDataArr["chk_notes_chart_notes"] = $chkNotesChartNotes;
$patDataArr["chk_notes_accounting"] = $chkNotesAccounting;
$patDataArr["chk_notes_optical"] = $chkNotesOptical;
$patDataArr["primary_care_phy_name"] = $priCarePhyNameFullNew;
$patDataArr["primary_care_phy_id"] = $pCarePhy;
$patDataArr["dod_patient"] = $dod_patient;
$patDataArr["locked"] = ($status_updated && $_REQUEST["elem_patientStatus"] === 'Deceased') ? 1 : $_REQUEST["lockPatient"];
$patDataArr["co_man_phy"] = $CoManPhyNameFullNew;
$patDataArr["co_man_phy_id"] = $co_man_phy_id;
$patDataArr["view_portal"] = $view_portal;
$patDataArr["update_portal"] = $update_portal;
$patDataArr["temp_key"] = isset($_REQUEST['temp_key']) ? $_REQUEST['temp_key'] : '';
$patDataArr["vip"] = $vip;
$patDataArr["default_address"] = $default_address_id;
$patDataArr["pt_disable"] = (isset($pt_disable) && $pt_disable == 1)?1:0;

if(trim($patDataArr["fname"]) != '' && trim($patDataArr["lname"]) != ''){
	$sql_pd = "select src_server,temp_key_chk_val from patient_data where id = '".$pid."'";
	$res_pd = imw_query($sql_pd);
	$row_pd = imw_fetch_assoc($res_pd);
	if(isset($temp_key_chk_val) && $temp_key_chk_val == 1 && $row_pd['temp_key_chk_val'] == 0){
		$patDataArr["temp_key_chk_val"] = $temp_key_chk_val;
		$patDataArr["temp_key_chk_opr_id"] = $_SESSION['authId'];
		$patDataArr["temp_key_chk_datetime"] = date('Y-m-d H:i:s');
	}
	//pre($patDataArr); exit;
	//echo UpdateRecords($pid,'id',$patDataArr,'patient_data');  exit;
	UpdateRecords($pid,'id',$patDataArr,'patient_data');
	
	// **** 1 - REMOTE SYNC Code Removed FROM THIS PLACE
}
else
{
	log_patient_update($patDataArr);
}

if ($previous_record) {
    //-----------START INSERT CHANGED ENTRY IN patient_previous_data TABLE
    if ((trim($hidd_prev_title) != "" && (trim($hidd_prev_title) != trim($title))) || (trim($hidd_prev_fname) != "" && (trim($hidd_prev_fname) != trim($fname))) || (trim($hidd_prev_mname) != "" && (trim($hidd_prev_mname) != trim($mname))) || (trim($hidd_prev_lname) != "" && (trim($hidd_prev_lname) != trim($lname))) || (trim($hidd_prev_suffix) != "" && (trim($hidd_prev_suffix) != trim($suffix)))) {
        //---------SAVE PATIENT-NAME
        $query_string = "INSERT INTO patient_previous_data SET
							patient_id = '$pid', save_date_time = '" . date('Y-m-d H:i:s') . "',
							operator_id	= '" . $_SESSION['authId'] . "',
							patient_section_name = 'patientName',
							prev_title = '" . convertUcfirst($hidd_prev_title) . "',
							prev_fname = '" . addslashes(convertUcfirst($hidd_prev_fname)) . "',
							prev_lname = '" . addslashes(convertUcfirst($hidd_prev_lname)) . "',
							prev_mname = '" . addslashes(convertUcfirst($hidd_prev_mname)) . "',
							prev_suffix = '" . addslashes(convertUcfirst($hidd_prev_suffix)) . "',
							new_title = '" . convertUcfirst($title) . "',
							new_fname = '" . addslashes(convertUcfirst($fname)) . "',
							new_lname = '" . addslashes(convertUcfirst($lname)) . "',
							new_mname = '" . addslashes(convertUcfirst($mname)) . "',
							new_suffix = '" . addslashes(convertUcfirst($suffix)) . "'";
        $saveChangedNameRes = imw_query($query_string);
    }
    /* -----START SAVING UPDATED MARITAL STATUS---Code---
      ----- Inserting changed data in patient_previous_data TABLE -- */

    if (trim($hidd_prev_mstatus) != "" && trim($hidd_prev_mstatus) != trim($status)) {
        $query_string = "INSERT INTO patient_previous_data SET patient_id = '$pid',
						save_date_time	= '" . date('Y-m-d H:i:s') . "', operator_id = '" . $_SESSION['authId'] . "',
						patient_section_name= 'patientMstatus', 
						prev_mstatus = '" . addslashes(convertUcfirst($hidd_prev_mstatus)) . "',
						new_mstatus	= '" . addslashes(convertUcfirst($status)) . "'";
        $saveChangedNameRes = imw_query($query_string);
    }
    /* ----end saving MARITAL STATUS--------- */

    /* -----START SAVING UPDATE Gender---Code--- */

    if (trim($hidd_prev_sex) != "" && trim($hidd_prev_sex) != trim($sex)) {
        $query_string = "INSERT INTO patient_previous_data SET patient_id = '$pid',
						save_date_time	= '" . date('Y-m-d H:i:s') . "', operator_id = '" . $_SESSION['authId'] . "',
						patient_section_name= 'patientGender',
						prev_sex = '" . addslashes(convertUcfirst($hidd_prev_sex)) . "',
						new_sex = '" . addslashes(convertUcfirst($sex)) . "'";
        $saveChangedNameRes = imw_query($query_string);
    }
    /* ----end saving Gender--------- */

    /* -----START SAVING SS code---Code--- */
    if (trim($hidd_prev_ss) != "" && trim($hidd_prev_ss) != trim($ss)) {
        $query_string = "INSERT INTO patient_previous_data SET
						patient_id = '" . $pid . "', save_date_time	= '" . date('Y-m-d H:i:s') . "',
						operator_id = '" . $_SESSION['authId'] . "',
						patient_section_name= 'patientSS', prev_ss = '" . addslashes(convertUcfirst($hidd_prev_ss)) . "',
						new_ss = '" . addslashes(convertUcfirst($ss)) . "'";
        $saveChangedNameRes = imw_query($query_string);
    }
    /* ----end saving SS code--------- */

    /* -----START SAVING DOB code---Code--- */
    $dob = getDateFormatDB($dob);

    if (trim($hidd_prev_dob) != "" && trim($hidd_prev_dob) != trim($dob)) {
        $query_string = "INSERT INTO patient_previous_data SET
						patient_id = '$pid',
						save_date_time = '" . date('Y-m-d H:i:s') . "',
						operator_id = '" . $_SESSION['authId'] . "',
						patient_section_name = 'patientDOB',
						prev_dob = '$hidd_prev_dob',new_dob = '$dob'";
        $saveChangedNameRes = imw_query($query_string);
    }
    /* ----end saving DOB code--------- */

    /* -----START SAVING ADO OPTION code---Code--- */
    if (trim($hidd_prev_ado_option) != "" && trim($hidd_prev_ado_option) != trim($ado_option)) {
        $query_string = "INSERT INTO patient_previous_data SET
						patient_id = '$pid',
						save_date_time = '" . date('Y-m-d H:i:s') . "',
						operator_id	= '" . $_SESSION['authId'] . "',
						patient_section_name = 'patientADOopt',
						prev_ado_option = '$hidd_prev_ado_option',
						new_ado_option = '$ado_option'";
        $saveChangedNameRes = imw_query($query_string);
    }
    /* ----end saving ADO OPTION code--------- */


    if ((trim($hidd_prev_street) != "" && (trim($hidd_prev_street) != trim($street))) || (trim($hidd_prev_street2) != "" && (trim($hidd_prev_street2) != trim($street2))) || (trim($hidd_prev_postal_code) != "" && (trim($hidd_prev_postal_code) != trim($postal_code))) || (trim($hidd_prev_city) != "" && (trim($hidd_prev_city) != trim($city))) || (trim($hidd_prev_state) != "" && (trim($hidd_prev_state) != trim($state)))) {
        //SAVE PATIENT-ADDRESS
        $query_string = "INSERT INTO patient_previous_data SET
								patient_id = '" . $pid . "', save_date_time = '" . date('Y-m-d H:i:s') . "',
								operator_id = '" . $_SESSION['authId'] . "',
								patient_section_name = 'patientAddress',
								new_street = '" . addslashes(convertUcfirst($street)) . "',
								new_street2 = '" . addslashes(convertUcfirst($street2)) . "',
								new_postal_code = '" . $postal_code . "',
								new_city = '" . addslashes(trim(convertUcfirst($city))) . "',
								new_state = '" . addslashes(trim(ucwords($state))) . "',
								prev_street = '" . addslashes(convertUcfirst($hidd_prev_street)) . "', 
								prev_street2 = '" . addslashes(convertUcfirst($hidd_prev_street2)) . "',
								prev_postal_code = '" . $hidd_prev_postal_code . "',
								prev_city = '" . addslashes(trim(convertUcfirst($hidd_prev_city))) . "',
								prev_state = '" . addslashes(trim(ucwords($hidd_prev_state))) . "'";
        $saveChangedAddressRes = imw_query($query_string);
    }

    $phone_home = core_phone_unformat($phone_home);
    $phone_biz = core_phone_unformat($phone_biz);
    $phone_cell = core_phone_unformat($phone_cell);
    $hidd_prev_phone_home = core_phone_unformat($hidd_prev_phone_home);
    $hidd_prev_phone_biz = core_phone_unformat($hidd_prev_phone_biz);
    $hidd_prev_phone_cell = core_phone_unformat($hidd_prev_phone_cell);
    
    if ((trim($hidd_prev_phone_home) != "" && (trim($hidd_prev_phone_home) != trim($phone_home))) || (trim($hidd_prev_phone_biz) != "" && (trim($hidd_prev_phone_biz) != trim($phone_biz))) || (trim($hidd_prev_phone_cell) != "" && (trim($hidd_prev_phone_cell) != trim($phone_cell))) || (trim($hidd_prev_email) != "" && (trim($hidd_prev_email) != trim($ptDemoEmail)))) {
        //SAVE PATIENT-CONTACT
        $query_string = "INSERT INTO patient_previous_data SET
						patient_id = '$pid', save_date_time = '" . date('Y-m-d H:i:s') . "',
						operator_id = '" . $_SESSION['authId'] . "',";
        if (trim($hidd_prev_phone_home) != "" && trim($hidd_prev_phone_home) != trim($phone_home)) {
            $query_string .= "prev_phone_home = '" . core_phone_unformat($hidd_prev_phone_home) . "',
											new_phone_home = '" . core_phone_unformat($phone_home) . "',";
        }

        if (trim($hidd_prev_phone_biz) != "" && trim($hidd_prev_phone_biz) != trim($phone_biz)) {
            $query_string .= " prev_phone_biz = '" . core_phone_unformat($hidd_prev_phone_biz) . "',
											new_phone_biz = '" . core_phone_unformat($phone_biz) . "',";
        }

        if (trim($hidd_prev_phone_cell) != "" && trim($hidd_prev_phone_cell) != trim($phone_cell)) {
            $query_string .= "prev_phone_cell 	= '" . core_phone_unformat($hidd_prev_phone_cell) . "',
											new_phone_cell 		= '" . core_phone_unformat($phone_cell) . "',";
        }

        if (trim($hidd_prev_email) != "" && trim($hidd_prev_email) != trim($ptDemoEmail)) {
            $query_string .= " prev_email = '" . addslashes(trim($hidd_prev_email)) . "',
											new_email 			= '" . addslashes($ptDemoEmail) . "',";
        }
        $query_string .= "patient_section_name= 'patientContact'";
        $chbxChangedContactRes = imw_query($query_string);
    }
    //END INSERT CHANGED ENTRY IN patient_previous_data	TABLE
}


//SAVE VOICE TIMMINGS OF PATIENT
if($_REQUEST['hipaa_voice']=='1'){

	for($i=1; $i<=4; $i++){
		if($_REQUEST['hourFrom'.$i]!='' || $_REQUEST['hourTo'.$i]!=''){
			$fromTime='00:00:00';
			$toTime='00:00:00';
			if($_REQUEST['hourFrom'.$i]!=''){
				if($_REQUEST['ampmFrom'.$i]=='PM'){
					$_REQUEST['hourFrom'.$i]=12 + $_REQUEST['hourFrom'.$i];
				}
				$_REQUEST['minFrom'.$i]= ($_REQUEST['minFrom'.$i]!='') ? $_REQUEST['minFrom'.$i] : '00';
				$fromTime = $_REQUEST['hourFrom'.$i].':'.$_REQUEST['minFrom'.$i].':00';
			}
			if($_REQUEST['hourTo'.$i]!=''){
				if($_REQUEST['ampmTo'.$i]=='PM'){
					$_REQUEST['hourTo'.$i]=12 + $_REQUEST['hourTo'.$i];
				}
				$_REQUEST['minTo'.$i]= ($_REQUEST['minTo'.$i]!='') ? $_REQUEST['minTo'.$i] : '00';
				$toTime = $_REQUEST['hourTo'.$i].':'.$_REQUEST['minTo'.$i].':00';
			}
			
			$qryPre="Insert INTO ";
			$timeWhere='';
			if($_REQUEST['timeId'.$i]!='' && $_REQUEST['timeId'.$i]>0){ $qryPre="UPDATE "; $timeWhere=" WHERE id='".$_REQUEST['timeId'.$i]."'"; }
			
			$qryPre." patient_call_timmings SET patient_id='".$pid."', time_from='".$fromTime."', time_to='".$toTime."', del_status='0' ".$timeWhere;
		$rs=imw_query($qryPre." patient_call_timmings SET patient_id='".$pid."', time_from='".$fromTime."', time_to='".$toTime."', del_status='0' ".$timeWhere);
		
		}else if($_REQUEST['timeId'.$i]>0 && $_REQUEST['hourFrom'.$i]=='' && $_REQUEST['hourTo'.$i]==''){
			$rs=imw_query("Update patient_call_timmings SET del_status='1' WHERE id='".$_REQUEST['timeId'.$i]."'");
		}
	}
}

$newPatiendId = $pid;
$opreaterId = $_SESSION['authId'];

if((int)$pid > 0){
	if(((int)$pcare > 0) && (empty($_REQUEST['pcare2']) == false)){	
			$selPatRefPhyMulti = "select id, status from patient_multi_ref_phy where patient_id = '".$pid."' and phy_type = 1 and status = 0 ORDER BY id limit 1";
		$rsPatRefPhyMulti = imw_query($selPatRefPhyMulti);
		
		if(imw_num_rows($rsPatRefPhyMulti) > 0){
			$rowPatRefPhyMulti = imw_fetch_row($rsPatRefPhyMulti);
			$intPatRefPhyMultiID = $intPatRefPhyMultiStatus = 0;
			$intPatRefPhyMultiID = $rowPatRefPhyMulti[0];
			$intPatRefPhyMultiStatus = $rowPatRefPhyMulti[1];
			$qryUp = "update patient_multi_ref_phy set ref_phy_id = '".$pcare."', ref_phy_name = '".addslashes($_REQUEST['pcare2'])."' where id = '".$intPatRefPhyMultiID."' LIMIT 1 ";
			imw_query($qryUp);
		}
		else{
			$qryInsertCoPhy = "insert into patient_multi_ref_phy (patient_id, ref_phy_id, ref_phy_name, phy_type, created_by, created_by_date_time) Values ('".$pid."', '".$pcare."', '".addslashes($_REQUEST['pcare2'])."', '1','".$_SESSION['authId']."', '".date('Y-m-d H:i:s')."')";
			$rsInsertCoPhy = imw_query($qryInsertCoPhy);
		}
	}else if( (empty($_REQUEST['pcare2']) == true)){
		// DELETE REF. PHYSICIAN IN CASE OF ONLY ONE PHYSICIAN AND REF PHY TEXTBOX EMPTIED
		$selPatRefPhyMulti = "select id, status from patient_multi_ref_phy where patient_id = '".$pid."' and phy_type = 1 and status = 0 ORDER BY id";
		$rsPatRefPhyMulti = imw_query($selPatRefPhyMulti);
		if(imw_num_rows($rsPatRefPhyMulti) == 1 ){
			$qryUp = "update patient_data set primary_care_id = '' where id = '".$pid."'";
			imw_query($qryUp);
			$rowPatRefPhyMulti = imw_fetch_row($rsPatRefPhyMulti);
			$intPatRefPhyMultiID = $rowPatRefPhyMulti[0];
			$qryUp = "update patient_multi_ref_phy set status = '1' where id = '".$intPatRefPhyMultiID."' LIMIT 1 ";
			imw_query($qryUp);
		}
	
	}
	if(((int)$co_man_phy_id > 0) && (empty($CoManPhyNameFullNew) == false)){
		$selPatRefPhyMulti = "select id, status from patient_multi_ref_phy where patient_id = '".$pid."' and phy_type = 2 and status = 0 limit 1";
		$rsPatRefPhyMulti = imw_query($selPatRefPhyMulti);
		if(imw_num_rows($rsPatRefPhyMulti) > 0){
			$rowPatRefPhyMulti = imw_fetch_row($rsPatRefPhyMulti);
			$intPatRefPhyMultiID = $intPatRefPhyMultiStatus = 0;
			$intPatRefPhyMultiID = $rowPatRefPhyMulti[0];
			$intPatRefPhyMultiStatus = $rowPatRefPhyMulti[1];
			$qryUp = "update patient_multi_ref_phy set ref_phy_id = '".$co_man_phy_id."',ref_phy_name = '".$CoManPhyNameFullNew."' where id = '".$intPatRefPhyMultiID."' LIMIT 1 ";
			imw_query($qryUp);
		}
		else{
			$qryInsertCoPhy = "insert into patient_multi_ref_phy 
										(patient_id, ref_phy_id, ref_phy_name, phy_type, created_by, created_by_date_time) Values ('".$pid."', '".$co_man_phy_id."', '".$CoManPhyNameFullNew."', '2','".$_SESSION['authId']."', '".date('Y-m-d H:i:s')."')";
			$rsInsertCoPhy = imw_query($qryInsertCoPhy);
		}
	}
	else if( (empty($CoManPhyNameFullNew) == true)){
		// DELETE COMAN. PHYSICIAN IN CASE OF ONLY ONE PHYSICIAN AND COMAN PHY TEXTBOX EMPTIED
		$selPatRefPhyMulti = "select id, status from patient_multi_ref_phy where patient_id = '".$pid."' and phy_type = 2 and status = 0 ORDER BY id";
		$rsPatRefPhyMulti = imw_query($selPatRefPhyMulti);
		if(imw_num_rows($rsPatRefPhyMulti) == 1 ){
			$qryUp = "update patient_data set co_man_phy_id = '' where id = '".$pid."'";
			imw_query($qryUp);
			$rowPatRefPhyMulti = imw_fetch_row($rsPatRefPhyMulti);
			$intPatRefPhyMultiID = $rowPatRefPhyMulti[0];
			$qryUp = "update patient_multi_ref_phy set status = '1' where id = '".$intPatRefPhyMultiID."' LIMIT 1 ";
			imw_query($qryUp);
		}
	
	}
	if(((int)$pCarePhy > 0) && (empty($priCarePhyNameFullNew) == false)){
		$selPatRefPhyMulti = "select id, status from patient_multi_ref_phy where patient_id = '".$pid."' and status = 0 and phy_type = 4 limit 1";
		$rsPatRefPhyMulti = imw_query($selPatRefPhyMulti);
		if(imw_num_rows($rsPatRefPhyMulti) > 0){
			$rowPatRefPhyMulti = imw_fetch_row($rsPatRefPhyMulti);
			$intPatRefPhyMultiID = $intPatRefPhyMultiStatus = 0;
			$intPatRefPhyMultiID = $rowPatRefPhyMulti[0];
			$intPatRefPhyMultiStatus = $rowPatRefPhyMulti[1];
			$qryUp = "update patient_multi_ref_phy set ref_phy_id = '".$pCarePhy."',ref_phy_name = '".$priCarePhyNameFullNew."' where id = '".$intPatRefPhyMultiID."' LIMIT 1 ";
			imw_query($qryUp);
		}
		else{
			$qryInsertCoPhy = "insert into patient_multi_ref_phy (patient_id, ref_phy_id, ref_phy_name, phy_type, created_by, created_by_date_time) Values ('".$pid."', '".$pCarePhy."', '".$priCarePhyNameFullNew."', '4','".$_SESSION['authId']."', '".date('Y-m-d H:i:s')."')";
			$rsInsertCoPhy = imw_query($qryInsertCoPhy);
		}
	}
	else if( (empty($priCarePhyNameFullNew) == true)){
		// DELETE PCP. PHYSICIAN IN CASE OF ONLY ONE PHYSICIAN AND PCP PHY TEXTBOX EMPTIED
		$selPatRefPhyMulti = "select id, status from patient_multi_ref_phy where patient_id = '".$pid."' and phy_type = 4 and status = 0 ORDER BY id";
		$rsPatRefPhyMulti = imw_query($selPatRefPhyMulti);
		if(imw_num_rows($rsPatRefPhyMulti) == 1 ){
			$qryUp = "update patient_data set primary_care_phy_id = '' where id = '".$pid."'";
			imw_query($qryUp);
			$rowPatRefPhyMulti = imw_fetch_row($rsPatRefPhyMulti);
			$intPatRefPhyMultiID = $rowPatRefPhyMulti[0];
			$qryUp = "update patient_multi_ref_phy set status = '1' where id = '".$intPatRefPhyMultiID."' LIMIT 1 ";
			imw_query($qryUp);
		}
	
	}
}
			
//--------------- UPDATE INS. DATA
$query_string = "select id as insPK_ID, type as instype, subscriber_fname as insSubFName, 
				subscriber_mname as insSubMName, subscriber_lname as insSubLName, subscriber_ss as insSubSS,
				subscriber_DOB as insSubDOB, subscriber_street as insSubStreet,
				subscriber_postal_code as insSubZipCode, subscriber_city as insSubCity,
				subscriber_state as insSubState, subscriber_country as insSubCountry,
				subscriber_phone as insSubPhone, subscriber_sex as insSubSex from insurance_data 
				WHERE pid = '$pid' AND subscriber_relationship = 'self' AND actInsComp = '1'";				
$rsInsDataQry = imw_query($query_string);
if($rsInsDataQry){
	if(imw_num_rows($rsInsDataQry) > 0){
		$totInsurenceDataFields = imw_num_fields($rsInsDataQry);
		global $insurenceDataFields; 
		$insurenceDataFields = array();
		for($i=0;$i<$totInsurenceDataFields;$i++){
			$row_meta = imw_fetch_field_direct($rsInsDataQry, $i);
			
			$mysql_data_type_hash = array(
				1=>'tinyint',
				2=>'smallint',
				3=>'int',
				4=>'float',
				5=>'double',
				7=>'timestamp',
				8=>'bigint',
				9=>'mediumint',
				10=>'date',
				11=>'time',
				12=>'datetime',
				13=>'year',
				16=>'bit',
				//252=>'text',
				252=>'blob', //text and blob returns same value 252
				253=>'string', //varchar
				254=>'char',
				246=>'decimal'
			);
			
			$type  = $row_meta->type;
			foreach ($mysql_data_type_hash as $key => $value) {
				if($key == $row_meta->type){
					$row_meta->type = $value;
					$type = $value;
				}
			}
			$name  = $row_meta->name;
			$insurenceDataFields[$i]["DB_Field_Name"] = $name;
			$insurenceDataFields[$i]["DB_Field_Type"] = $type;	
		}
	
		while($row = imw_fetch_array($rsInsDataQry)){
			if($row['instype']=="primary"){
				$typeCounter = "1";
				$typeCounterLName = "";
				$insType = "Primary";
			}
			elseif($row['instype']=="secondary"){
				$typeCounter = "2";
				$typeCounterLName = "1";
				$insType = "Secondary";
			}
			elseif($row['instype']=="tertiary"){
				$typeCounter = "3";
				$typeCounterLName = "2";
				$insType = "Tertiary";
			}					

			$arrAuditTrail [] = 
				array(
						"Pk_Id"=> $row['insPK_ID'],
						"pid"=> $pid,
						"Table_Name"=>"insurance_data",
						"Data_Base_Field_Name"=> "subscriber_fname" ,
						"Filed_Label"=> "i".$typeCounter."subscriber_fname",
						"Filed_Text"=> "Patient ".$insType." Insurance First Name",
						"Old_Value"=> addcslashes(addslashes($row['insSubFName']),"\0..\37!@\177..\377"),
						"New_Value"=> addcslashes(addslashes(trim($fname)),"\0..\37!@\177..\377"),
						"Opreater_Id"=> $opreaterId,
						"IP"=> $ip,
						"URL"=> $URL,
						"Category"=> "patient_info",
						"Category_Desc"=> "demographics",
						"Data_Base_Field_Type"=> fun_get_field_type($insurenceDataFields,"insSubFName"),
						"Action"=> "update  "																								
					);
			$arrAuditTrail [] = 
				array(
						"Pk_Id"=> $row['insPK_ID'],
						"pid"=> $pid,
						"Table_Name"=>"insurance_data",
						"Data_Base_Field_Name"=> "subscriber_mname" ,
						"Filed_Label"=> "i".$typeCounter."subscriber_mname",
						"Filed_Text"=> "Patient ".$insType." Insurance Middle Name",
						"Old_Value"=> addcslashes(addslashes(trim($row['insSubMName'])),"\0..\37!@\177..\377"),
						"New_Value"=> addcslashes(addslashes(trim($mname)),"\0..\37!@\177..\377"),
						"Opreater_Id"=> $opreaterId,
						"IP"=> $ip,
						"URL"=> $URL,
						"Category"=> "patient_info",
						"Category_Desc"=> "demographics",
						"Data_Base_Field_Type"=> fun_get_field_type($insurenceDataFields,"insSubMName"),
						"Action"=> "update  "																								
					);	
			$arrAuditTrail [] = 
				array(
						"Pk_Id"=> $row['insPK_ID'],
						"pid"=> $pid,
						"Table_Name"=>"insurance_data",
						"Data_Base_Field_Name"=> "subscriber_lname" ,
						"Filed_Label"=> "lastName".$typeCounterLName,
						"Filed_Text"=> "Patient ".$insType." Insurance Last Name",
						"Old_Value"=> addcslashes(addslashes($row['insSubLName']),"\0..\37!@\177..\377"),
						"New_Value"=> addcslashes(addslashes(trim($lname)),"\0..\37!@\177..\377"),
						"Opreater_Id"=> $opreaterId,
						"IP"=> $ip,
						"URL"=> $URL,
						"Category"=> "patient_info",
						"Category_Desc"=> "demographics",
						//"Data_Base_Field_Type"=> imw_fetch_field_direct($insurenceDataFields,"insSubLName")->type ,
						"Data_Base_Field_Type"=> fun_get_field_type($insurenceDataFields,"insSubLName"),
						"Action"=> "update  "																								
					);
			$arrAuditTrail [] = 
				array(
						"Pk_Id"=> $row['insPK_ID'],
						"pid"=> $pid,
						"Table_Name"=>"insurance_data",
						"Data_Base_Field_Name"=> "subscriber_ss" ,
						"Filed_Label"=> "i".$typeCounter."subscriber_ss",
						"Filed_Text"=> "Patient ".$insType." Insurance S.S",
						"Old_Value"=> addcslashes(addslashes($row['insSubSS']),"\0..\37!@\177..\377"),
						"New_Value"=> addcslashes(addslashes($ss),"\0..\37!@\177..\377"),
						"Opreater_Id"=> $opreaterId,
						"IP"=> $ip,
						"URL"=> $URL,
						"Category"=> "patient_info",
						"Category_Desc"=> "demographics",
						//"Data_Base_Field_Type"=> imw_fetch_field_direct($insurenceDataFields,"insSubSS")->type ,
						"Data_Base_Field_Type"=> fun_get_field_type($insurenceDataFields,"insSubSS"),
						"Action"=> "update  "																								
					);
			$arrAuditTrail [] = 
				array(
						"Pk_Id"=> $row['insPK_ID'],
						"pid"=> $pid,
						"Table_Name"=>"insurance_data",
						"Data_Base_Field_Name"=> "subscriber_DOB" ,
						"Filed_Label"=> "i".$typeCounter."subscriber_DOB",
						"Filed_Text"=> "Patient ".$insType." Insurance DOB",
						"Old_Value"=> (addcslashes(addslashes($row['insSubDOB']),"\0..\37!@\177..\377")!="0000-00-00") ? addcslashes(addslashes($row['insSubDOB']),"\0..\37!@\177..\377") : "",																
						"New_Value"=> (addcslashes(addslashes($date_convert),"\0..\37!@\177..\377")!="0000-00-00" && addcslashes(addslashes($date_convert),"\0..\37!@\177..\377")!="--") ? addcslashes(addslashes($date_convert),"\0..\37!@\177..\377") : "",																																
						"Opreater_Id"=> $opreaterId,
						"IP"=> $ip,
						"URL"=> $URL,
						"Category"=> "patient_info",
						"Category_Desc"=> "demographics",
						//"Data_Base_Field_Type"=> imw_fetch_field_direct($insurenceDataFields,"insSubDOB")->type ,
						"Data_Base_Field_Type"=> fun_get_field_type($insurenceDataFields,"insSubDOB"),
						"Action"=> "update  "																								
					);		
			$arrAuditTrail [] = 
				array(
						"Pk_Id"=> $row['insPK_ID'],
						"pid"=> $pid,
						"Table_Name"=>"insurance_data",
						"Data_Base_Field_Name"=> "subscriber_street" ,
						"Filed_Label"=> "i".$typeCounter."subscriber_street",
						"Filed_Text"=> "Patient ".$insType." Insurance Address 1",
						"Old_Value"=> addcslashes(addslashes($row['insSubStreet']),"\0..\37!@\177..\377"),
						"New_Value"=> addcslashes(addslashes(trim($street)),"\0..\37!@\177..\377"),
						"Opreater_Id"=> $opreaterId,
						"IP"=> $ip,
						"URL"=> $URL,
						"Category"=> "patient_info",
						"Category_Desc"=> "demographics",
						//"Data_Base_Field_Type"=> imw_fetch_field_direct($insurenceDataFields,"insSubStreet")->type ,
						"Data_Base_Field_Type"=> fun_get_field_type($insurenceDataFields,"insSubStreet"),
						"Action"=> "update  "																								
					);		
			$arrAuditTrail [] = 
				array(
						"Pk_Id"=> $row['insPK_ID'],
						"pid"=> $pid,
						"Table_Name"=>"insurance_data",
						"Data_Base_Field_Name"=> "subscriber_postal_code" ,
						"Filed_Label"=> "i".$typeCounter."subscriber_postal_code",
						"Filed_Text"=> "Patient ".$insType." Insurance Zip",
						"Old_Value"=> addcslashes(addslashes($row['insSubZipCode']),"\0..\37!@\177..\377"),
						"New_Value"=> addcslashes(addslashes($postal_code),"\0..\37!@\177..\377"),
						"Opreater_Id"=> $opreaterId,
						"IP"=> $ip,
						"URL"=> $URL,
						"Category"=> "patient_info",
						"Category_Desc"=> "demographics",
						//"Data_Base_Field_Type"=> imw_fetch_field_direct($insurenceDataFields,"insSubZipCode")->type ,
						"Data_Base_Field_Type"=> fun_get_field_type($insurenceDataFields,"insSubZipCode"),
						"Action"=> "update  "																								
					);	
			$arrAuditTrail [] = 
				array(
						"Pk_Id"=> $row['insPK_ID'],
						"pid"=> $pid,
						"Table_Name"=>"insurance_data",
						"Data_Base_Field_Name"=> "subscriber_city" ,
						"Filed_Label"=> "i".$typeCounter."subscriber_city",
						"Filed_Text"=> "Patient ".$insType." Insurance City",
						"Old_Value"=> addcslashes(addslashes($row['insSubCity']),"\0..\37!@\177..\377"),
						"New_Value"=> addcslashes(addslashes(convertUcfirst($city)),"\0..\37!@\177..\377"),
						"Opreater_Id"=> $opreaterId,
						"IP"=> $ip,
						"URL"=> $URL,
						"Category"=> "patient_info",
						"Category_Desc"=> "demographics",
						//"Data_Base_Field_Type"=> imw_fetch_field_direct($insurenceDataFields,"insSubCity")->type ,
						"Data_Base_Field_Type"=> fun_get_field_type($insurenceDataFields,"insSubCity"),
						"Action"=> "update  "																								
					);		
			$arrAuditTrail [] = 
				array(
						"Pk_Id"=> $row['insPK_ID'],
						"pid"=> $pid,
						"Table_Name"=>"insurance_data",
						"Data_Base_Field_Name"=> "subscriber_state" ,
						"Filed_Label"=> "i".$typeCounter."subscriber_state",
						"Filed_Text"=> "Patient ".$insType." Insurance State",
						"Old_Value"=> addcslashes(addslashes($row['insSubState']),"\0..\37!@\177..\377"),
						"New_Value"=> addcslashes(addslashes(trim($state)),"\0..\37!@\177..\377"),
						"Opreater_Id"=> $opreaterId,
						"IP"=> $ip,
						"URL"=> $URL,
						"Category"=> "patient_info",
						"Category_Desc"=> "demographics",
						//"Data_Base_Field_Type"=> imw_fetch_field_direct($insurenceDataFields,"insSubState")->type ,
						"Data_Base_Field_Type"=> fun_get_field_type($insurenceDataFields,"insSubState"),
						"Action"=> "update  "																								
					);	
			$arrAuditTrail [] = 
				array(
						"Pk_Id"=> $row['insPK_ID'],
						"pid"=> $pid,
						"Table_Name"=>"insurance_data",
						"Data_Base_Field_Name"=> "subscriber_phone" ,
						"Filed_Label"=> "i".$typeCounter."subscriber_phone",
						"Filed_Text"=> "Patient ".$insType." Insurance Home Tel.",
						"Old_Value"=> addcslashes(addslashes($row['insSubPhone']),"\0..\37!@\177..\377"),
						"New_Value"=> addcslashes(addslashes($phone_home),"\0..\37!@\177..\377"),
						"Opreater_Id"=> $opreaterId,
						"IP"=> $ip,
						"URL"=> $URL,
						"Category"=> "patient_info",
						"Category_Desc"=> "demographics",
						//"Data_Base_Field_Type"=> imw_fetch_field_direct($insurenceDataFields,"insSubPhone")->type ,
						"Data_Base_Field_Type"=> fun_get_field_type($insurenceDataFields,"insSubPhone"),
						"Action"=> "update  "																								
					);
			$arrAuditTrail [] = 
				array(
						"Pk_Id"=> $row['insPK_ID'],
						"pid"=> $pid,
						"Table_Name"=>"insurance_data",
						"Data_Base_Field_Name"=> "subscriber_sex" ,
						"Filed_Label"=> "i".$typeCounter."subscriber_sex",
						"Filed_Text"=> "Patient ".$insType." Insurance Gender",
						"Old_Value"=> addcslashes(addslashes($row['insSubSex']),"\0..\37!@\177..\377"),
						"New_Value"=> addcslashes(addslashes($sex),"\0..\37!@\177..\377"),
						"Opreater_Id"=> $opreaterId,
						"IP"=> $ip,
						"URL"=> $URL,
						"Category"=> "patient_info",
						"Category_Desc"=> "demographics",
						//"Data_Base_Field_Type"=> imw_fetch_field_direct($insurenceDataFields,"insSubSex")->type ,
						"Data_Base_Field_Type"=> fun_get_field_type($insurenceDataFields,"insSubSex"),
						"Action"=> "update  "																								
					);
			
		}
		
		//--- UPDATE PATIENT SUBSCRIBER RELATION IF SELF ---
		$ins_data_arr = array();
		$ins_data_arr["subscriber_lname"] = convertUcfirst($lname);
		$ins_data_arr["subscriber_mname"] = convertUcfirst($mname);
		$ins_data_arr["subscriber_fname"] = convertUcfirst($fname);
		$ins_data_arr["subscriber_suffix"] = convertUcfirst($suffix);
		$ins_data_arr["subscriber_ss"] = $ss;
		$ins_data_arr["subscriber_DOB"] = $date_convert;
		$ins_data_arr["subscriber_street"] = convertUcfirst($street);
		$ins_data_arr["subscriber_street_2"] = convertUcfirst($street2);
		$ins_data_arr["subscriber_postal_code"] = $postal_code;
		$ins_data_arr["subscriber_city"] = convertUcfirst($city);
		$ins_data_arr["subscriber_state"] = $state;
		$ins_data_arr["subscriber_country"] = $country_code;
		$ins_data_arr["subscriber_phone"] = preg_replace('/[^0-9]/','',$phone_home);
		$ins_data_arr["subscriber_mobile"] = preg_replace('/[^0-9]/','',$phone_cell);
		$ins_data_arr["subscriber_biz_phone"] = preg_replace('/[^0-9]/','',$phone_biz);
		if($sex!='')
		$ins_data_arr["subscriber_sex"] = $sex;
		
		
		$query_string = "select id from insurance_data
						where pid = '$pid' and subscriber_relationship = 'self'
						and actInsComp = '1'";
		$sql	=	imw_query($query_string);
		while( $insQryRes = imw_fetch_assoc($sql))
		{
			$ins_data_id = $insQryRes['id'];			
			if($ins_data_id > 0){
				UpdateRecords($ins_data_id,'id',$ins_data_arr,'insurance_data');
			}
		}
	}																		
}

//--------------- UPDATE INS. DATA

#setting or inserting general health PCP------------start
if($priCarePhyNameFullNew){
	$query_string = "select general_id from general_medicine where patient_id = '$pid'";
	$sql = imw_query($query_string);
	$genQryRes = imw_fetch_assoc($sql);
	$pat_general_id = $genQryRes['general_id'];
	$gen_data_arr = array();
	$gen_data_arr["patient_id"] = $pid;
	$gen_data_arr["med_doctor"] = $priCarePhyNameFullNew;

	if(empty($pat_general_id) === true){
		AddRecords($gen_data_arr,"general_medicine");
	}
	else{
		UpdateRecords($pat_general_id,"general_id",$gen_data_arr,"general_medicine");
	}
}

#setting or inserting general health PCP------------end		
$chkForExit = false;
if($_REQUEST['isNewPatient'] == 'yes'){
	$chkForExit = true;
}

if(strtolower($_REQUEST['isNewPatient']) != 'yes' or empty($_REQUEST['isNewPatient'])){
	if (strtolower($exitingStreet) != strtolower($street) 
	|| strtolower($exitingStreet2) != strtolower($street2) 
	|| strtolower($exitingCity) != strtolower($city) 
	|| strtolower($exitingState) != strtolower($state) 
	|| trim($exitingZip) != trim($postal_code) 
	|| trim($exitingSsn) != trim($ss) 
	|| $exitingPhoneHome != $phone_home 
	|| $existingPhoneCell != $phone_cell 
	|| strtolower($exitingFirstName) != strtolower(addslashes($fname)) 
	|| strtolower($exitingMiddleName) != strtolower(addslashes($mname)) 
	|| strtolower($exitingLastName) != strtolower(addslashes($lname))  
	|| $exitingDOB != $date_convert 
	|| $exitingSex != $sex){
		$chkForExit = true;
	}
	
	if($chkErxAsk==1 or (empty($chkErxAsk) and $erx_patient_id == '')){
		$chkForExit = true;
	}
}
else if(strtolower($_REQUEST['isNewPatient']) == 'yes' and !empty($_REQUEST['isNewPatient']) and $chkErxAsk==1){
	$chkForExit = true;
}else{
	$chkForExit = false;
}

$cookie_file = $save_file->upDir.'/'.'cookie_'.$_SESSION['authId'].'.txt';

//registering patient in case of updating record and patient not registered and the operator has confirmed to register
if($chkErxAsk == 1){
	$erx_entry = 1;
}
	
//stop patient registration with erx if DOB and sex not entered
if($dob == "" || $sex == "" || $fname == "" || $lname == ""){
	$erx_entry = 0;
}

if($chkForExit == true && $erx_entry == 1){

	$query_string = "select Allow_erx_medicare, EmdeonUrl from copay_policies";
	$sql = imw_query($query_string);
	$copay_policies_res = imw_fetch_assoc($sql);
	$Allow_erx_medicare = $copay_policies_res['Allow_erx_medicare'];	
	$EmdeonUrl = $copay_policies_res['EmdeonUrl'];	
	
	$userId = $_SESSION['authId'];	
	$query_string = "select eRx_user_name, erx_password,eRx_facility_id from users where id = '$userId'";
	$sql = imw_query($query_string);
	$userRes = imw_fetch_assoc($sql);
	$eRx_user_name = $userRes['eRx_user_name'];
	$erx_password = $userRes['erx_password'];
	$eRx_facility_id = trim($_SESSION['login_facility_erx_id']);

	if(strtolower($Allow_erx_medicare) == 'yes' && $eRx_user_name != '' && $erx_password != '' && trim($EmdeonUrl) != '' && $patientStatus != 'Deceased'){
		//--- PROVIDER LOGIN WITH ERX ----
		$cur = curl_init();
		$url = "$EmdeonUrl/servlet/DxLogin?userid=$eRx_user_name&PW=$erx_password&hdnBusiness=$eRx_facility_id&target=html/LoginSuccess.html&testLogin=true";

		curl_setopt($cur, CURLOPT_URL,$url);
		curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
		
		$data = curl_exec($cur);
		curl_close($cur);
		preg_match('/Login Error/',$data,$login_error);
		if(count($login_error) == 0){
			//--- ERX UPLOAD PATIENT DATA  ----------
			$phone_home = preg_replace('/-/','',$phone_home);
			$phone_cell = preg_replace('/-/','',$phone_cell);
			$ss = preg_replace('/-/','',$ss);
			//--- DOB FORMAT MM/DD/YYYY
			if(empty($_POST['dob']) == false){
				$date_convert2 = preg_replace('/-/','/',$_POST['dob']);
			}
			else{
				list($y,$m,$d) = preg_split('/-/',$_POST['patientDob']);
				$date_convert2 = $m.'/'.$d.'/'.$y;
			}
			$sex_erx = substr($sex,0,1);											
			$url = "$EmdeonUrl/servlet/servlets.apiPersonServlet?apiuserid=$eRx_user_name&actionCommand=upload&P_ACT=$pid&P_LNM=$lname&P_FNM=$fname&P_MID=$mname&P_ADR=$street&P_CIT=$city&P_STA=$state&P_ZIP=$postal_code&P_SEX=$sex_erx&P_DOB=$date_convert2&P_SSN=$ss&P_PHN=$phone_home&P_AD2=$street2&P_REL=1&P_CELLPHONE=$phone_cell";
			
			$url = preg_replace('/ /','%20',$url);
			$cur = curl_init();
			curl_setopt($cur, CURLOPT_URL,$url);
			curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, false); 
			curl_setopt($cur, CURLOPT_COOKIEFILE,$cookie_file);
			curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
			$erx_data = curl_exec($cur);
			
			$erx_status_arr = preg_split('/ /',$erx_data);
			$erx_status = $erx_status_arr[1];
			$erx_status_msg = strtolower($erx_status) == 'null' ? false : true;

			if($erx_status_msg === true){
				$msg = 'Patient Registered With eRx.';
				$erxDataArr = array();
				$erxDataArr["erx_patient_id"] = $erx_status;
				UpdateRecords($pid,"id",$erxDataArr,"patient_data");
			}
			else{
			//	$msg = 'Patient Not Registered With eRx.';
			}

			curl_close($cur);
			
			//--- LOG OUT FROM ERX --------
			$cur = curl_init();
			$url = "$EmdeonUrl/servlet/lab.security.DxLogout?userid=$eRx_user_name&BaseUrl=$EmdeonUrl&LogoutPath=/html/AutoPrintFinished.html";
			curl_setopt($cur,CURLOPT_URL,$url);
			curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
			curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
			curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
			$data = curl_exec($cur);
			curl_close($cur);
			
		}
		else{
			$msg = "There is an error with your eRx login";
		}
	}
	else if($patientStatus != 'Deceased'){
		$msg = 'Patient Not Registered With eRx.';
	}
}

//-------- delete erx cookie file -------
@unlink($cookie_file);

if(strtoupper($_POST["isNewPatient"]) == "YES"){
	$upload_dir = data_path();
	$patientDir = "PatientId_".$newPatiendId;
	
	//Check
	if(!is_dir($upload_dir.$patientDir)){
		//Create patient directory
		mkdir($upload_dir.$patientDir, 0700);																	
	}
	//Patient Image
	if($_SESSION["scan_patient_image"]){
		$filename = $upload_dir.$_SESSION["scan_patient_image"];
		$newname = str_replace("/tmp",$patientDir,$_SESSION["scan_patient_image"]);											
		$newfilename = $upload_dir.$newname;
		if(file_exists($filename))
		{											
			if(rename($filename, $newfilename))
			{									
				$sql = "UPDATE patient_data SET p_imagename = '$newname' WHERE id = '$newPatiendId' ";
				sqlStatement($sql);
				$_SESSION["scan_patient_image"] = "";
				$_SESSION["scan_patient_image"] = NULL;
				unset($_SESSION["scan_patient_image"]);
			}
		}
	}

	//Licence Image
	if($_SESSION["scan_image"])
	{
		$filename = $upload_dir.$_SESSION["scan_image"];
		$newname = str_replace("/temp",$patientDir,$_SESSION["scan_image"]);											
		$newfilename = $upload_dir.$newname;
												
		if(file_exists($filename))
		{											
			if(rename ($filename, $newfilename))
			{									
				$sql = "UPDATE patient_data SET licence_photo='$newname' WHERE id = '$newPatiendId' ";
				sqlStatement($sql);
				//
				$_SESSION["scan_image"] = "";
				$_SESSION["scan_image"] = NULL;
				unset($_SESSION["scan_image"]);
			}
		}										
	}								
}

//--- SAVE EMPLOYER DATA ----
{
	$query_string = "select id from employer_data where pid = '$pid'";
	$sql = imw_query($query_string);
	$empQryRes = imw_fetch_assoc($sql);
	$newEmpDataId = $empQryRes['id'];
	
	$ename = trim($ename);
	if( $ename || ($newEmpDataId && !$ename) )
	{
		$empDataArr = array();
		$empDataArr["name"] = convertUcfirst($ename);
		$empDataArr["street"] = convertUcfirst($estreet);
		$empDataArr["street2"] = convertUcfirst($estreet2);
		$empDataArr["city"] = convertUcfirst($ecity);
		$empDataArr["state"] = convertUcfirst($estate);
		$empDataArr["postal_code"] = $epostal_code;
		$empDataArr["zip_ext"] = $ezip_ext;
		$empDataArr["country"] = convertUcfirst($ecountry);
		$empDataArr["pid"] = $pid;
		
		if($newEmpDataId > 0){
			UpdateRecords($newEmpDataId,'id',$empDataArr,'employer_data');
		}
		else{
			$newEmpDataId = AddRecords($empDataArr,'employer_data');
		}
	}
}

//--- RESPONSIBLE PARTY -----
if(trim($lname1) != '' and trim($fname1) != ''){
	$date_convert1 = getDateFormatDB($dob1);
	$ocularSaveDataArr = array();
	$ocularSaveDataArr["patient_id"] = $pid;
	$ocularSaveDataArr["title"] = convertUcfirst($title1);
	$ocularSaveDataArr["fname"] = convertUcfirst($fname1);
	$ocularSaveDataArr["mname"] = convertUcfirst($mname1);
	$ocularSaveDataArr["lname"] = convertUcfirst($lname1);
	$ocularSaveDataArr["suffix"] = convertUcfirst($suffix1);
	$ocularSaveDataArr["dob"] = $date_convert1;
	$ocularSaveDataArr["sex"] = $sex1;
	$ocularSaveDataArr["ss"] = $ss1;
	$ocularSaveDataArr["address"] = convertUcfirst($street1);
	$ocularSaveDataArr["address2"] = convertUcfirst($street_emp);
	$ocularSaveDataArr["city"] = convertUcfirst($city1);
	$ocularSaveDataArr["state"] = ucwords($state2);
	$ocularSaveDataArr["zip"] = $postal_code1;
	$ocularSaveDataArr["zip_ext"] = $rzip_ext;
	$ocularSaveDataArr["country"] = $country_code1;
	$ocularSaveDataArr["marital"] = $status1;
	$ocularSaveDataArr["relation"] = $relation1;
	$ocularSaveDataArr["other1"] = $other1;
	$ocularSaveDataArr["emergency_contact"] = convertUcfirst($emergency_contact1);
	$ocularSaveDataArr["phone_contact"] = core_phone_unformat($phone_contact1);
	$ocularSaveDataArr["home_ph"] = core_phone_unformat($phone_home1);
	$ocularSaveDataArr["work_ph"] = core_phone_unformat($phone_biz1);
	$ocularSaveDataArr["mobile"] = core_phone_unformat($phone_cell1);
	$ocularSaveDataArr["email"] = $email1;
	$ocularSaveDataArr["licence"] = $dlicence1;
	$ocularSaveDataArr["hippa_release_status"] = $chkHippaRelResp;
	$ocularSaveDataArr["licence_image"] = addslashes($_POST['resp_license_image']);

	if( isset($_SESSION['rpscan_license_comment']) ) {
		$ocularSaveDataArr["licenseComments"] = addslashes($_SESSION['rpscan_license_comment']);
		unset($_SESSION['rpscan_license_comment']);
	}
	
	if( isset($_SESSION['rpscan_license_opr']) ) {
		$ocularSaveDataArr["licenseOperator"] = $_SESSION['rpscan_license_opr'];
		unset($_SESSION['rpscan_license_opr']);
	}
	
    $ocularSaveDataArr["erp_resp_username"] = "";
    $ocularSaveDataArr["erp_resp_imw_password"] = "";
    if(isERPPortalEnabled()) {
		$erp_resp_password="";
        $ocularSaveDataArr["erp_resp_username"] = $erp_resp_username;
        if($erp_resp_passwd!='' && $erp_resp_cpasswd!='') {
            $erp_resp_password = $erp_resp_passwd;
        }else if($erp_resp_passwd=='' && $erp_resp_cpasswd=='') {
            $erp_resp_password = $erp_resp_passwd;
        }
		
		$ocularSaveDataArr["erp_resp_imw_password"] = $erp_resp_password;
    }

	$query_string = "select id from resp_party where patient_id = '$pid'";
	$sql = imw_query($query_string);
	$resQryRes = imw_fetch_assoc($sql);
	
	$resp_party_id = $resQryRes['id'];
	
	if($resp_party_id > 0){
		UpdateRecords($resp_party_id,'id',$ocularSaveDataArr,'resp_party');		
	}
	else{
		$resp_party_id = AddRecords($ocularSaveDataArr,'resp_party');echo imw_error();
		if($_REQUEST['hid_create_acc_resp_party'] == "yes"){
			
			$dpr_pt_data1 = array();
			$dpr_pt_data1['ptfname'] 	= convertUcfirst($fname1);
			$dpr_pt_data1['ptlname'] 	= convertUcfirst($lname1);
			$dpr_pt_data1['ptdob'] 		= $date_convert1;
			$dpr_pt_data1['ptgender'] 	= $sex1;
			$dpr_pt_data1['ptzip'] 		= (inter_country() != "UK")?core_padd_char($postal_code1,5):$postal_code1;
			$dpr_pt_data1['ptzip_ext'] 	= $rzip_ext;
			
			$arr_patient_next_id = get_Next_PatientID($dpr_pt_data1);
			if($arr_patient_next_id['error']==''){
				$resp_patient_next_id = $arr_patient_next_id['patient_id'];
				$resp_patient_src_server = $arr_patient_next_id['src_server'];
			}else{
				echo $arr_patient_next_id['error'];exit;
			}
			
			$arrRespNewSepAcc = array();
			$arrRespNewSepAcc["title"] = convertUcfirst($title1);
			$arrRespNewSepAcc["fname"] = convertUcfirst($fname1);
			$arrRespNewSepAcc["mname"] = convertUcfirst($mname1);
			$arrRespNewSepAcc["lname"] = convertUcfirst($lname1);
			$arrRespNewSepAcc["street"] = convertUcfirst($street1);
			$arrRespNewSepAcc["street2"] = convertUcfirst($street_emp);
			$arrRespNewSepAcc["city"] = convertUcfirst($city1);
			$arrRespNewSepAcc["state"] = ucwords($state2);			
			$arrRespNewSepAcc["postal_code"] = $postal_code1;
			$arrRespNewSepAcc["zip_ext"] = $rzip_ext;
			$arrRespNewSepAcc["country_code"] = $country_code;
			$arrRespNewSepAcc["ss"] = $ss1;
			$arrRespNewSepAcc["DOB"] = $date_convert1;
			$arrRespNewSepAcc["patientStatus"] = "Active";
			$arrRespNewSepAcc["sex"] = $sex1;			 			 
			$arrRespNewSepAcc["pid"] = $resp_patient_next_id;	
			$arrRespNewSepAcc["id"] = $resp_patient_next_id;
			$arrRespNewSepAcc["date"] = date('Y-m-d H:i:s');
			$arrRespNewSepAcc["created_by"] = $_SESSION['authId'];
			if($resp_patient_src_server>0){$arrRespNewSepAcc["src_server"] = $resp_patient_src_server;} 		
			//--- ADD NEW PATIENT (ability to create an account whenever a new Grantor/Responsible Party person is added)----
			AddRecords($arrRespNewSepAcc,"patient_data");
		}
	}	
	
	//--- UPLOAD RESPONSIBLE PARTY IMAGE ---

	// validate file content type
	if( check_img_mime($_FILES['userlic12']['tmp_name']) ) {
		if( file_exists($_FILES['userlic12']['tmp_name'])) {
			$image_name = $save_file->copyfile($_FILES['userlic12']);
		}
	} 
	
	if($image_name)
	{		
		$query_string = "select licence_image from resp_party where patient_id = '$pid'";
		$sql = imw_query($query_string);
		$imgQryRes = imw_fetch_assoc($sql);
		$oldimagename = $imgQryRes['licence_image'];
		$oldpath = ($oldimagename) ? $save_file->upDir.$oldimagename : '';
		@unlink($oldpath);
		
		imw_query("update resp_party set licence_image = '$image_name' where patient_id = '$pid'");
	}
}

//START INSERT data to patient_custom_field TABLE
if(trim($pid) != ''){
	$createdBy = $_SESSION['authId'];
	$arrPatientControlId = $_REQUEST['hidPatientControlPId'];
	$arrcustomField = $_REQUEST['hidcustomField'];
	$control = explode("_",$Value);		 		
	$controlVal = $_REQUEST[$control[0]]; 		 		
	foreach((array)$arrPatientControlId as $patientControlKey => $patientControlValue){ 
		foreach($arrcustomField as $Key => $Value){		
			$control = explode("_",$Value);		 		
			$controlVal = $_REQUEST[$control[0]]; 
			$controlType = "";
			$controlType = $control[2]; 			 		
			if(!$arrPatientControlId[$patientControlKey]){
				$customQry = "insert into patient_custom_field set "; 
				$customQry .= "created_by = '".$createdBy."',";
				$customQry .= "created_date_time = NOW(),";	
			}
			elseif($arrPatientControlId[$patientControlKey]){
				$customQry = "update patient_custom_field set "; 
				$customQry .= "modified_by = '".$createdBy."',";
				$customQry .= "modified_date_time = NOW(),";	
				$queryUpdate = " where id = '".$arrPatientControlId[$patientControlKey]."'";
			}	
			$customQry .= "patient_id = '$pid',";
			$customQry .= "admin_control_id = '$control[1]',";
			if($controlType == "checkbox"){
				if($controlVal){
					$customQry .= "patient_cbk_control_value = 'checked',";
				}
				else{
					$customQry .= "patient_cbk_control_value = 'unChecked',";
				}
			}
			$customQry .= "patient_control_value = '".trim(addslashes($controlVal))."'";
			if($arrPatientControlId[$patientControlKey]){
				$customQry .= $queryUpdate;			
			}	
			$rsCustomQry = "";		
			$rsCustomQry = imw_query($customQry);
			if($rsCustomQry){									
				unset($arrPatientControlId[$patientControlKey]);	
				unset($arrcustomField[$Key]);						
				break ;						
			}
		}
	}
}

//END INSERT data to patient_custom_field TABLE						 
$date_convert = getDateFormatDB($dob);
if($date_convert != "--"){
	$_REQUEST['dob'] = $dob;
}
else{
	$_REQUEST['dob'] = "";
}

//--- CHANGE DATE FORMAT -----
$date_convert1 = getDateFormatDB($dob1);
$date_convert1 = ($date_convert1) ? $date_convert1 : '0000-00-00';
$_REQUEST['dob1'] = $date_convert1;

$create_date = getDateFormatDB($reg_date);
$create_date = ($create_date) ? $create_date : '0000-00-00';
$_REQUEST['reg_date'] = $create_date;
	
$date = $create_date." ".$tdate;

$table = array("patient_data","resp_party","employer_data","insurance_data","patient_family_info");
$error = array($demoError,$respPartyError,$empError,$insError);
$mergedArray = merging_array($table,$error);

$policyStatus = 0;
$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];

if(empty($arrAuditTrail[0]) != true){
	foreach ($arrAuditTrail as $key => $value) {
		if(trim($arrAuditTrail [$key]["Table_Name"]) == "patient_data"){
			if(!array_key_exists('Pk_Id', $arrAuditTrail[$key])) {
				$arrAuditTrail[$key]["Pk_Id"] = $newPatiendId;
			}
		}
		else if(trim($arrAuditTrail [$key]["Table_Name"]) == "resp_party"){
			if(!array_key_exists('Pk_Id', $arrAuditTrail[$key])) {
				$arrAuditTrail[$key]["Pk_Id"] = $newRespPartyId;
			}
		}
		else if(trim($arrAuditTrail [$key]["Table_Name"]) == "employer_data"){
			if(!array_key_exists('Pk_Id', $arrAuditTrail[$key])) {
				$arrAuditTrail[$key]["Pk_Id"] = $newEmpDataId;
			}
		}
		$arrAuditTrail[$key]["pid"] = $pid;		
	}
}

$_REQUEST["providersToRestrictDemographics"] = implode(",",(array)$_REQUEST["providersToRestrictDemographics"]);

if($_REQUEST["hipaa_mail"]!=1)
{
	$_REQUEST["hipaa_mail"] = '0';
}
if($_REQUEST["hipaa_email"]!=1)
{
	$_REQUEST["hipaa_email"] = 0;
}
if($_REQUEST["hipaa_voice"]!=1)
{
	$_REQUEST["hipaa_voice"] = 0;
}
if($_REQUEST["chkNotesScheduler"]!=1)
{
	$_REQUEST["chkNotesScheduler"] = 0;
}
if($_REQUEST["chkNotesChartNotes"]!=1)
{
	$_REQUEST["chkNotesChartNotes"] = 0;
}
if($_REQUEST["chkNotesAccounting"]!=1)
{
	$_REQUEST["chkNotesAccounting"] = 0;
}
if($_REQUEST["chkNotesOptical"]!=1)
{
	$_REQUEST["chkNotesOptical"] = 0;
}
if($_REQUEST["chkHippaRelResp"] != 1){
	$_REQUEST["chkHippaRelResp"] = 0;
}

$_REQUEST['elem_heardAbtUs'] = $elem_heardAbtUsValue;
//--- GET PATIENT FAMILY INFORMATION ID FOR EXISTS DATA ---
$family_inf_arr = array();
for($i=1;$i<=$last_family_inf_cnt;$i++){
	$family_relation_primary_key_id = $_REQUEST["family_info_primary_key_id".$i];
	if($family_relation_primary_key_id != ''){
		$family_inf_arr[] = $family_relation_primary_key_id;
	}
}
$family_inf_str = join(',',$family_inf_arr);
if(!$family_inf_str) $family_inf_str = 0;
$query_string = "select * from patient_family_info where patient_id = '".$pid."' and id in ($family_inf_str)";
$sql = imw_query($query_string);
$pat_family_info_data = array();
while($family_inf_res = imw_fetch_assoc($sql))
{
	$primary_id = $family_inf_res['id'];
	$pat_family_info_data[$primary_id] = $family_inf_res;
}


//---- GET TABLE COLUMN TYPE ---
$query_string = "show columns from patient_family_info";
$sql = imw_query($query_string);

$field_type_arr = array();
while($columnQryRes = imw_fetch_assoc($sql))
{
	$field_name = $columnQryRes['Field'];
	$field_type_arr[$field_name] = $columnQryRes;
}

//--- SAVE PATIENT FAMILY INFORMATION -----
for($i=1;$i<=$last_family_inf_cnt;$i++)
{	
	$family_relation_primary_key_id = $_REQUEST["family_info_primary_key_id".$i];
	$family_relation_patient_id = $pid;
	$family_relation_title = $_REQUEST["title_table_family_information".$i];
	$family_relation_fname = $_REQUEST["fname_table_family_information".$i];
	$family_relation_mname = $_REQUEST["mname_table_family_information".$i];
	$family_relation_lname = $_REQUEST["lname_table_family_information".$i];
	$family_relation_suffix = $_REQUEST["suffix_table_family_information".$i];
	$family_relation_relation = $_REQUEST["family_information_relatives".$i];
	$family_relation_other_relative_name = $_REQUEST["family_information_relatives_other_txt".$i];

	$family_relation_street1 = $_REQUEST["street1_table_family_information".$i];
	$family_relation_street2 = $_REQUEST["street2_table_family_information".$i];
	$family_relation_postal_code = $_REQUEST["postal_code_table_family_information".$i];
	$family_relation_zip_ext = $_REQUEST["zip_ext_table_family_information".$i];
	$family_relation_city = $_REQUEST["city_table_family_information".$i];
	$family_relation_state = $_REQUEST["state_table_family_information".$i];

	//$family_relation_home_phone = preg_replace('/[^0-9]/','',$_REQUEST["phone_home_table_family_information".$i]);
	$family_relation_home_phone = core_phone_unformat($_REQUEST["phone_home_table_family_information".$i]);
	$_REQUEST["phone_home_table_family_information".$i] = $family_relation_home_phone;
	//$family_relation_work_phone = preg_replace('/[^0-9]/','',$_REQUEST["phone_work_table_family_information".$i]);
	$family_relation_work_phone = core_phone_unformat($_REQUEST["phone_work_table_family_information".$i]);
	$_REQUEST["phone_work_table_family_information".$i] = $family_relation_work_phone;
	//$family_relation_mobile_phone = preg_replace('/[^0-9]/','',$_REQUEST["phone_cell_table_family_information".$i]);
	$family_relation_mobile_phone = core_phone_unformat($_REQUEST["phone_cell_table_family_information".$i]);
	$_REQUEST["phone_cell_table_family_information".$i] = $family_relation_mobile_phone;
	
	$family_relation_email = $_REQUEST["email_table_family_information".$i];
	$family_relation_is_mobile_email = $_REQUEST["chkMobileTableFamilyInformation_".$i];
	$family_relation_is_hippa = $_REQUEST["chkHippaFamilyInformation_".$i];
	
	if(trim($family_relation_lname) != '' and trim($family_relation_fname) != '')
	{
		$pat_fam_data_arr = array();
		$pat_fam_data_arr["patient_id"] = $family_relation_patient_id;
		$pat_fam_data_arr["patient_relation"] = $family_relation_relation;
		$pat_fam_data_arr["name_of_other_relation"] = $family_relation_other_relative_name;
		$pat_fam_data_arr["title"] = $family_relation_title;
		$pat_fam_data_arr["fname"] = $family_relation_fname;
		$pat_fam_data_arr["mname"] = $family_relation_mname;
		$pat_fam_data_arr["lname"] = $family_relation_lname;
		$pat_fam_data_arr["suffix"] = $family_relation_suffix;
		$pat_fam_data_arr["street1"] = $family_relation_street1;
		$pat_fam_data_arr["street2"] = $family_relation_street2;
		$pat_fam_data_arr["postal_code"] = $family_relation_postal_code;
		$pat_fam_data_arr["zip_ext"] = $family_relation_zip_ext;
		$pat_fam_data_arr["city"] = convertUcfirst($family_relation_city);
		$pat_fam_data_arr["state"] = $family_relation_state;
		$pat_fam_data_arr["home_phone"] = $family_relation_home_phone;
		$pat_fam_data_arr["work_phone"] = $family_relation_work_phone;
		$pat_fam_data_arr["mobile_phone"] = $family_relation_mobile_phone;
		$pat_fam_data_arr["email_id"] = $family_relation_email;
		$pat_fam_data_arr["mobile_email"] = $family_relation_is_mobile_email;
		$pat_fam_data_arr["hippa_release_status"] = $family_relation_is_hippa;
		$pat_fam_data_arr["created_by"] = $_SESSION['authId'];
		$pat_fam_data_arr["created_on"] = date('Y-m-d H:i:s');
		
		if(empty($family_relation_primary_key_id) === true)
		{
			$faim_act = 'add';
			$family_relation_primary_key_id = AddRecords($pat_fam_data_arr,'patient_family_info');
		}
		else
		{
			$faim_act = 'update';
			$pat_fam_data_arr["modified_by"] = $_SESSION['authId'];
			$pat_fam_data_arr["modified_on"] = date('Y-m-d H:i:s');			
			UpdateRecords($family_relation_primary_key_id,'id',$pat_fam_data_arr,'patient_family_info');
		}
		
		$oldfamilyDataArr = $pat_family_info_data[$family_relation_primary_key_id];
		$famRelPersonName = "";
		$famRelPersonName = $family_relation_lname.", ".$family_relation_fname;
		//--- PATIENT FAMILY INFORMATION RELATION AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "patient_relation";
		$arrAuditTrailFamily["Filed_Label"] = "family_information_relatives".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. Relative ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["patient_relation"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['patient_relation']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;		
		
		//--- PATIENT FAMILY INFORMATION RELATION OTHER AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "name_of_other_relation";
		$arrAuditTrailFamily["Filed_Label"] = "family_information_relatives_other_txt".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. Relative other ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["name_of_other_relation"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['name_of_other_relation']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;
		
		//--- PATIENT FAMILY INFORMATION HIPPA REALEASE AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "hippa_release_status";
		$arrAuditTrailFamily["Filed_Label"] = "chkHippaFamilyInformation_".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. Release HIPPA Info ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["hippa_release_status"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['hippa_release_status']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;			
		$arrAuditTrail[] = $arrAuditTrailFamily;
		if($_REQUEST["chkHippaFamilyInformation_".$i] != 1){
			$_REQUEST["chkHippaFamilyInformation_".$i] = 0;
		}
		
		//--- PATIENT FAMILY INFORMATION TITLE AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "title";
		$arrAuditTrailFamily["Filed_Label"] = "title_table_family_information".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. Title ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["title"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['title']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;
		
		//--- PATIENT FAMILY INFORMATION FIRST NAME AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "fname";
		$arrAuditTrailFamily["Filed_Label"] = "fname_table_family_information".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. First Name ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["fname"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['fname']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;
		
		//--- PATIENT FAMILY INFORMATION MIDDLE NAME AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "mname";
		$arrAuditTrailFamily["Filed_Label"] = "mname_table_family_information".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. Middle Name ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["mname"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['mname']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;
		
		//--- PATIENT FAMILY INFORMATION LAST NAME AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "lname";
		$arrAuditTrailFamily["Filed_Label"] = "lname_table_family_information".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. Last Name ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["lname"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['lname']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;
		
		//--- PATIENT FAMILY INFORMATION NAME SUFFIX AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "suffix";
		$arrAuditTrailFamily["Filed_Label"] = "suffix_table_family_information".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. Suffix ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["suffix"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['suffix']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;
		
		//--- PATIENT FAMILY INFORMATION ADDRESS AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "street1";
		$arrAuditTrailFamily["Filed_Label"] = "street1_table_family_information".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. Street1 ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["street1"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['street1']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;
		
		//--- PATIENT FAMILY INFORMATION ADDRESS 2 AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "street2";
		$arrAuditTrailFamily["Filed_Label"] = "street2_table_family_information".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. Street2 ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["street2"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['street2']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;
		
		//--- PATIENT FAMILY INFORMATION ADDRESS 2 AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "postal_code";
		$arrAuditTrailFamily["Filed_Label"] = "postal_code_table_family_information".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. Zip Code ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["postal_code"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['postal_code']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;
		
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "zip_ext";
		$arrAuditTrailFamily["Filed_Label"] = "zip_ext_table_family_information".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. Zip Ext ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["zip_ext"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['zip_ext']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;
		
		//--- PATIENT FAMILY INFORMATION CITY AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "city";
		$arrAuditTrailFamily["Filed_Label"] = "city_table_family_information".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. City ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["city"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['city']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;
		
		//--- PATIENT FAMILY INFORMATION STATE AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "state";
		$arrAuditTrailFamily["Filed_Label"] = "state_table_family_information".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. State ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["state"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['state']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;
		
		//--- PATIENT FAMILY INFORMATION HOME PHONE AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "home_phone";
		$arrAuditTrailFamily["Filed_Label"] = "phone_home_table_family_information".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. Home Phone# ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["home_phone"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['home_phone']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;
		
		//--- PATIENT FAMILY INFORMATION WORK PHONE AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "work_phone";
		$arrAuditTrailFamily["Filed_Label"] = "phone_work_table_family_information".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. Work Phone# ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["work_phone"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['work_phone']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;
		
		//--- PATIENT FAMILY INFORMATION WORK PHONE AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "mobile_phone";
		$arrAuditTrailFamily["Filed_Label"] = "phone_cell_table_family_information".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. Mobile Phone# ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["mobile_phone"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['mobile_phone']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;
		
		//--- PATIENT FAMILY INFORMATION EMAIL AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "email_id";
		$arrAuditTrailFamily["Filed_Label"] = "email_table_family_information".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. Email-Id ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["email_id"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['email_id']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;
		
		//--- PATIENT FAMILY INFORMATION EMAIL MOBILE AUDIT CODE ----
		$arrAuditTrailFamily = array();
		$arrAuditTrailFamily["Pk_Id"] = $family_relation_primary_key_id;
		$arrAuditTrailFamily["Action"] =  $faim_act;
		$arrAuditTrailFamily["Table_Name"] = "patient_family_info";
		$arrAuditTrailFamily["Data_Base_Field_Name"] = "mobile_email";
		$arrAuditTrailFamily["Filed_Label"] = "chkMobileTableFamilyInformation_".$i;
		$arrAuditTrailFamily["Filed_Text"] = "Family Info. Mobile ".$famRelPersonName;
		$arrAuditTrailFamily["Data_Base_Field_Type"] = $field_type_arr["mobile_email"]["Type"];
		$arrAuditTrailFamily["Old_Value"] = addcslashes(addslashes($oldfamilyDataArr['mobile_email']),"\0..\37!@\177..\377");
		$arrAuditTrailFamily["pid"] = $pid;	
		$arrAuditTrail[] = $arrAuditTrailFamily;
	
		if($_REQUEST["chkMobileTableFamilyInformation_".$i] != 1){
			$_REQUEST["chkMobileTableFamilyInformation_".$i] = 0;
		}
	}
}
//-----------------------------Stop point of Demographics Family Information-----------------------------------------------//

/*********NEW HL7 ENGINE START************/
require_once(dirname(__FILE__)."/../../../hl7sys/api/class.HL7Engine.php");
$objHL7Engine = new HL7Engine();
$objHL7Engine->application_module = 'demographics';
if(isset($_REQUEST['isNewPatient']) && $_REQUEST['isNewPatient'] == 'yes')$objHL7Engine->msgSubType = 'add_patient'; else $objHL7Engine->msgSubType = 'update_patient';
$objHL7Engine->source_id = $pid;
$objHL7Engine->generateHL7();
/*********NEW HL7 ENGINE END*************/

if(defined('HL7_ADT_GENERATION') && constant('HL7_ADT_GENERATION') === true && (defined('HL7_ADT_GENERATION_OLD') && constant('HL7_ADT_GENERATION_OLD') === true)){
	if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('boston'))){
		$remote_Facs = check_remote_facility();
		if(is_array($remote_Facs) && $remote_Facs != false){
			require_once( dirname(__FILE__).'/../../../hl7sys/old/CLS_makeHL7.php');
			$makeHL7 = new makeHL7();
		}
	}else{
		require_once( dirname(__FILE__).'/../../../hl7sys/old/CLS_makeHL7.php');
		$makeHL7 = new makeHL7();
	}
	
	
	
	//logging HL7 messages to send to IDX & Forum.
	if($_REQUEST['isNewPatient'] == 'yes'){
		if($makeHL7){$makeHL7->log_HL7_message($pid,'Add_New_Patient');}
	}else{
		if($makeHL7){$makeHL7->log_HL7_message($pid,'Update_Patient');}	
	}
}else if( defined('HL7_ADT_GENERATION') && constant('HL7_ADT_GENERATION') === true){
	/* Purpose: Make ADT hl7 messages*/

	require_once( dirname(__FILE__).'/../../../hl7sys/hl7GP/hl7FeedData.php');
	$hl7 = new hl7FeedData();
	
	$hl7->PD['id'] = $pid;
	
	if( $_REQUEST['isNewPatient'] == 'yes' )
	{
		$hl7->msgtypes['ADT']['trigger_event'] = "A04";
		$hl7->msgtype = "ADD_NEW_PATIENT";
	}
	else{
		$hl7->msgtype = "UPDATE_PATIENT";
	}
	
	if( isset($GLOBALS['HL_RECEIVING']) && is_array($GLOBALS['HL_RECEIVING']) )
	{
		$hl7RecApp = ( isset($GLOBALS['HL_RECEIVING']['APPLICATION']) ) ? $GLOBALS['HL_RECEIVING']['APPLICATION'] : '';
		$hl7RecFac = ( isset($GLOBALS['HL_RECEIVING']['FACILITY']) ) ? $GLOBALS['HL_RECEIVING']['FACILITY'] : '';
		$hl7->setReceivingFacility($hl7RecApp, $hl7RecFac);
	}

	$hl7->addEVN($hl7->msgtypes['ADT']['trigger_event']);
	
	if( isset($GLOBALS['HL7_ADT_SEGMENTS']) && is_array($GLOBALS['HL7_ADT_SEGMENTS']) )
	{
		foreach( $GLOBALS['HL7_ADT_SEGMENTS'] as $segment )
		{
			$hl7->insertSegment($segment, 'ADT');
		}
	}
	
	$hl7->log_message();
}
/*End code*/

/* MVE PORTAL CREATE NEW PATIENT */

$erp_error=array();
if(isERPPortalEnabled()) {
	try {
		include_once($GLOBALS['srcdir']."/erp_portal/patients.php");
		$obj_patients = new Patients();
		$patientDetails = $obj_patients->addUpdatePatient($pid);
	} catch(Exception $e) {
		$erp_error[]='Unable to connect to ERP Portal';
	}
}



//--- SAVE RESTRICTED PROVIDERS ----
	$query_string = "select restrict_id from restricted_providers where `patient_id` = '$pid'";
	$sql = imw_query($query_string);
	$restProQryRes = imw_fetch_assoc($sql);
	$newRestrictedProviders = $restProQryRes['restrict_id'];
	
	$restProDataArr = array();
	$restProDataArr["operator_id"] = $_SESSION["authId"];
	$restProDataArr["form_id"] = $_SESSION["form_id"];
	$restProDataArr["restrict_providers"] = $strProvidersToRestrictDemographics;
	$restProDataArr["date_saved"] = date('Y-m-d H:i:s');
	$restProDataArr["patient_id"] = $pid;
	
	if($newRestrictedProviders > 0){
		UpdateRecords($newRestrictedProviders,'restrict_id',$restProDataArr,'restricted_providers');
	}
	else{
		if(trim($strProvidersToRestrictDemographics) != ''){
			$newRestrictedProviders = AddRecords($restProDataArr,'restricted_providers');
		}
	}
	
foreach ((array)$arrAuditTrail as $key => $value) {
	if($arrAuditTrail [$key]["Table_Name"] == "restricted_providers" && $arrAuditTrail [$key]["Pk_Id"] == ""){				
		$arrAuditTrail [$key]["Pk_Id"] = $newRestrictedProviders;	
		break;			
	}	
}
if($policyStatus == 1){
	auditTrail($arrAuditTrail,$mergedArray,0,0,0);
}


//***** 2 - SYNC CODE REMOVED 


//Enter Pt info into DICOM->WORKLIST -----

//<!-- updatePtInfoDicomWL(); -->

//Enter Pt info into DICOM->WORKLIST -----

?>
<script type="text/javascript">
	<?php
	if($_REQUEST['hidDemoChangeOption'] == "0"){
	?>
		var msg = '<?php echo $msg ?>';
		var new_pat = '<?php echo $add_new_patient; ?>';
		if(new_pat != ''){
			if(msg=="")
			{
				msg = "Records have been updated";
			}
		}
		else if(msg=="")
		{
			msg = "Records have been saved";
		}
		top.alert_notification_show(msg);
		<?php
		if($_REQUEST['isNewPatient'] == 'yes'){
		?>
		//parent.location.href = '../../main/patient_tabs.php?isNewPatient=yes&reload=true';
			<?php
		}else{ ?>
            
		top.fmain.location.href = 'index.php';
	<?php
		}
	}
	?>	
	top.chkConfirmSave("yes","set");
</script>
<?php if($status_updated) {
    $params=array();
    $params['patientid']=$pid;
    $params['operatorid']=$_SESSION['authId'];
    $params['section']='pt_status';
    $params['obj_value']=$_POST["elem_patientStatus"];
    $serialized_arr = serialize($params);
    include("../../../interface/common/assign_new_task.php");
} ?>