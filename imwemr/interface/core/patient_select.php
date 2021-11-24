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

?><?php 
require_once(dirname(__FILE__)."/../../config/globals.php");
require_once(dirname(__FILE__).'/../../library/classes/common_function.php');
require_once(dirname(__FILE__).'/../../library/classes/class.app_base.php');
$app_base			= new app_base();
$mysql_error = '';

/*Save Patient Data from All Scripts after confirmation*/
if( is_allscripts() && isset($_POST['asSavePatient']) && isset($_POST['asPatientId']) &&  $_POST['asPatientId'] !='' ){
	
	$GLOBALS['rethrow'] = true;
	include_once( $GLOBALS['srcdir'].'/allscripts/as_patient.php' );
	
	try
	{
		$asPatientId = trim($_POST['asPatientId']);
		if( $asPatientId === '' )
			throw new asException( 'Alert', 'Blank patient Id provided.' );
		
		/*Fetch Patient Details and Save in DB*/
		$patientObj = new as_patient();
		$patientData = $patientObj->patient( $asPatientId );
		if( is_null( $patientData ) )
			throw new asException( 'Alert', 'No data returned from All Scripts.' );
		
		$patientData = get_object_vars( $patientData );
		$patientData = (object) array_map('trim', $patientData);
		
		$dob = strtotime($patientData->dateofbirth);
		$dob = date( 'Y-m-d', $dob );
		
		$asPhyUserName = trim($patientData->PhysUserName);
		
		$pcpId = 0;
		$pcpName = '';
		
		/*Get PCP ID - Create if does not exists*/
		if( $asPhyUserName != '' )
		{
			require_once( $GLOBALS['srcdir'].'/classes/cls_common_function.php' );
			$OBJCommonFunction = new CLSCommonFunction;
			
			/*Check if physician already exists in DB*/
			$sqlPhyCheck = "SELECT `physician_Reffer_id` FROM `refferphysician` WHERE `external_id` = '".$asPhyUserName."'";
			$respPhyCheck = imw_query( $sqlPhyCheck );
			
			if( $respPhyCheck && imw_num_rows($respPhyCheck) > 0 )
			{
				$respPhyCheck = imw_fetch_assoc($respPhyCheck);
				
				$pcpId = $respPhyCheck['physician_Reffer_id'];
				$pcpName = trim( $OBJCommonFunction->getRefPhyName($pcpId) );
			}
			
			if( $pcpId == 0 )
			{
				include_once( $GLOBALS['srcdir'].'/allscripts/as_dataValues.php' );
				
				$asDataObj = new as_dataValues();
				$asPhyData = $asDataObj->getProvider( $asPhyUserName );
				
				$pcpMNameQry = ' ';
				if( trim($asPhyData->MiddleName) != '' )
				{
					$pcpMNameQry = " and MiddleName = '".addslashes(trim($asPhyData->MiddleName))."' ";
				}
				
				$sqlPhyCheck = "SELECT physician_Reffer_id FROM refferphysician WHERE LastName = '".addslashes(trim($asPhyData->LastName))."' AND FirstName = '".addslashes(trim($asPhyData->FirstName))."'".$pcpMNameQry."AND (delete_status = 0 OR delete_status = 2 ) ORDER BY delete_status ASC LIMIT 1";
				$respPhyCheck = imw_query( $sqlPhyCheck );
			
				if( $respPhyCheck && imw_num_rows($respPhyCheck) > 0 )
				{
					$respPhyCheck = imw_fetch_assoc($respPhyCheck);

					$pcpId = $respPhyCheck['physician_Reffer_id'];
					$pcpName = trim( $OBJCommonFunction->getRefPhyName($pcpId) );
				}
				else
				{
					$sqlPhyInsert = "INSERT INTO `refferphysician` SET
									`Title`='".core_refine_user_input( trim($asPhyData->TitleName) )."',
									`FirstName`='".core_refine_user_input( trim($asPhyData->FirstName) )."',
									`LastName`='".core_refine_user_input( trim($asPhyData->LastName) )."',
									`MiddleName`='".core_refine_user_input( trim($asPhyData->MiddleName) )."',
									`created_date`='".date('Y-m-d')."',
									`source`='TW',
									`delete_status`=0,
									`Address1`='".core_refine_user_input( trim($asPhyData->AddressLine1) )."',
									`Address2`='".core_refine_user_input( trim($asPhyData->AddressLine2) )."',
									`ZipCode`='".core_refine_user_input( trim($asPhyData->ZipCode) )."',
									`City`='".core_refine_user_input( trim($asPhyData->City) )."',
									`State`='".core_refine_user_input( trim($asPhyData->State) )."',
									`physician_phone`='".getNumber( trim($asPhyData->Phone) )."',
									`NPI`='".getNumber( trim($asPhyData->NPI) )."',
									`external_id`='".$asPhyUserName."'";
					
					$respPhyInsert = imw_query($sqlPhyInsert);
					$pcpId = imw_insert_id();
					$pcpName = trim( $OBJCommonFunction->getRefPhyName($pcpId) );
					if($pcpId)
					{
						$OBJCommonFunction->create_ref_phy_xml( strtolower(substr(trim($asPhyData->LastName),0,2)) );
					}
				}
			}
		}
		/*End Get PCP ID - Create if does not exists*/
		
		$sqlPatient = 'INSERT INTO ';
		$otherFields = ', `created_by`=\''.imw_real_escape_string($_SESSION["authId"]).'\'';
		$whereCondition = '';
		
		/*Check if patient with same allscripts ID and MRN already exists in IMW Database*/
		$asId = '`as_id`=\''.imw_real_escape_string($patientData->ID).'\',';
		$where = '';
		if( !empty($patientData->mrn) ) {
			$where .= ' `External_MRN_4`=\''.$patientData->mrn.'\' AND ';
		}
		if( !empty($patientData->ID) ) {
			$where .= ' `as_id`=\''.$patientData->ID.'\' ';
		}

		if( (!empty($patientData->mrn) || !empty($patientData->ID)) && $where != '' ) {

		$sqlPatientCheck = 'SELECT `id` FROM `patient_data` WHERE'.$where;
		// $sqlPatientCheck = 'SELECT `id` FROM `patient_data` WHERE `External_MRN_4`=\''.$patientData->mrn.'\' || `as_id`=\''.$patientData->ID.'\'';
		$respPatientCheck = imw_query( $sqlPatientCheck );
		
		if( $respPatientCheck && imw_num_rows( $respPatientCheck ) > 0 )
		{
			$pid = imw_fetch_assoc( $respPatientCheck );
			$pid = $pid['id'];
			
			$whereCondition = ' WHERE `id`='.$pid;
			$otherFields = '';
			
			$sqlPatient = 'UPDATE ';
			$asId = '';
		}
		
		}
		
		/*Add / Update Query*/
		$sqlPatient .= '`patient_data` SET
							'.$asId.'
							`External_MRN_4`=\''.imw_real_escape_string($patientData->mrn).'\',
							`lname`=\''.imw_real_escape_string($patientData->LastName).'\',
							`fname`=\''.imw_real_escape_string($patientData->Firstname).'\',
							`mname`=\''.imw_real_escape_string($patientData->middlename).'\',
							`sex`=\''.imw_real_escape_string($patientData->gender).'\',
							`DOB`=\''.imw_real_escape_string($dob).'\',
							`ss`=\''.imw_real_escape_string($patientData->ssn).'\',
							`street`=\''.imw_real_escape_string($patientData->Addressline1).'\',
							`street2`=\''.imw_real_escape_string($patientData->AddressLine2).'\',
							`city`=\''.imw_real_escape_string($patientData->City).'\',
							`state`=\''.imw_real_escape_string($patientData->State).'\',
							`postal_code`=\''.imw_real_escape_string($patientData->ZipCode).'\',
							`phone_home`=\''.imw_real_escape_string(core_phone_format(substr(getNumber($patientData->HomePhone),0,10))).'\',
							`phone_biz`=\''.imw_real_escape_string(core_phone_format(substr(getNumber($patientData->WorkPhone),0,10))).'\',
							`phone_cell`=\''.imw_real_escape_string(core_phone_format(substr(getNumber($patientData->CellPhone),0,10))).'\',
							`email`=\''.imw_real_escape_string($patientData->Email).'\',
							`race`=\''.imw_real_escape_string($patientData->Race).'\',
							`ethnicity`=\''.imw_real_escape_string($patientData->Ethnicity).'\',
							`language`=\''.imw_real_escape_string($patientData->Language).'\',
							`status`=\''.imw_real_escape_string($patientData->MaritalStatus).'\',
							`primary_care_phy_id`=\''.imw_real_escape_string($pcpId).'\',
							`primary_care_phy_name`=\''.imw_real_escape_string($pcpName).'\'
							'.$otherFields.$whereCondition;
		
		$resp = imw_query( $sqlPatient );
		
		if( $whereCondition === '' )
		{
			$pid  = imw_insert_id();
			imw_query("UPDATE `patient_data` SET `pid`='".$pid."' WHERE `id`='".$pid."'");
		}
		
		$_REQUEST['patient'] = $pid;
	}
	catch( asException $e)
	{
	?>
		<script>
			top.fAlert("Unable to save patient from All Scripts.<br /><?php echo $e->getErrorText(); ?>","","window.top.core_redirect_to(\""+window.top.document.getElementById('curr_main_tab').value+"\",\"\")");
			top.document.getElementById("findBy").value = "Active";
			top.document.getElementById("findByShow").value = "Active";
			top.show_loading_image('hide');
		</script>
	<?php	
	}
	/*Unset the object*/
	if( isset($patientObj) && is_object($patientObj) )
		unset($patientObj);
}

/**
 * Save DSS Patient Demographics
 */
if( isDssEnable() && isset($_POST['dssSavePatient']) && 
	( isset($_POST['dssPatientDfn']) && !empty($_POST['dssPatientDfn']) && $_POST['dssPatientDfn'] != 0 )
	) {

	require_once(dirname(__FILE__) . "/../../library/dss_api/dss_demographics.php");
	try 
	{
		$patientDFN = $_POST['dssPatientDfn'];
        $objDss = new Dss_demographics();
        $data = $objDss->PT_GetDemographics($patientDFN);

        if( is_null( $data ) )
			throw new Exception( 'Alert', 'No data returned from server.' );

		$sqlPatient = 'INSERT INTO ';
		$otherFields = ', `created_by`=\''.imw_real_escape_string($_SESSION["authId"]).'\'';
		$whereCondition = '';

		// Check if patient with same patient DFN / MRN already exists in IMW Database
		$sqlPatientCheck = 'SELECT `id` FROM `patient_data` WHERE `External_MRN_5`=\''.$patientDFN.'\'';
		$respPatientCheck = imw_query( $sqlPatientCheck );
		
		if( $respPatientCheck && imw_num_rows( $respPatientCheck ) > 0 )
		{
			$pid = imw_fetch_assoc( $respPatientCheck );
			$pid = $pid['id'];
			
			$whereCondition = ' WHERE `id`='.$pid;
			$otherFields = '';
			
			$sqlPatient = 'UPDATE ';
		}

		// Patient Name
		$patient_name = explode(',', $data[0]['name']);

		if ( preg_match('/\s/',$patient_name[1]) ) {
			$patient_fm_name = explode(' ', $patient_name[1]);

			$patient_first_name = !empty($patient_fm_name) ? $patient_fm_name[0] : '';
			$patient_middle_name = !empty($patient_fm_name) ? $patient_fm_name[1] : '';
		} else {
			$patient_first_name = $patient_name[1];
			$patient_middle_name = '';
		} 

		// Patient Gender
		$gender = '';
		if($data[0]['sex'] == 'M') $gender = 'Male';
		if($data[0]['sex'] == 'F') $gender = 'Female';

		// Patient DOB
		$dob = explode(';', $data[0]['dateOfBirth']);
		$dob = date( 'Y-m-d', strtotime($dob[1]) );

		// SSN
		$ssn = explode(';', $data[0]['ssn']);
		$ssn = $ssn[1];

		// Race / Ethnicity
		$raceData = explode(',',$data[0]['race']);
		$race = $raceData[0];
		$ethnicity = $raceData[1];

		// zipcode / zip_ext
		$zip = explode('-', $data[0]['zipCode']);
		$postal_code = $zip[0];
		$zip_ext = $zip[1];

		/*Add / Update Query*/
		$sqlPatient .= '`patient_data` SET
						`External_MRN_5`=\''.imw_real_escape_string($patientDFN).'\',
						`lname`=\''.imw_real_escape_string($patient_name[0]).'\',
						`fname`=\''.imw_real_escape_string($patient_first_name).'\',
						`mname`=\''.imw_real_escape_string($patient_middle_name).'\',
						`sex`=\''.imw_real_escape_string($gender).'\',
						`DOB`=\''.imw_real_escape_string($dob).'\',
						`ss`=\''.imw_real_escape_string($ssn).'\',
						`street`=\''.imw_real_escape_string($data[0]['street1']).'\',
						`street2`=\''.imw_real_escape_string($data[0]['street2']).'\',
						`city`=\''.imw_real_escape_string($data[0]['city']).'\',
						`state`=\''.imw_real_escape_string($data[0]['state']).'\',
						`postal_code`=\''.imw_real_escape_string($postal_code).'\',
						`zip_ext`=\''.imw_real_escape_string($zip_ext).'\',
						`phone_home`=\''.imw_real_escape_string(core_phone_format(getNumber($data[0]['homePhone']))).'\',
						`phone_biz`=\''.imw_real_escape_string(core_phone_format(getNumber($data[0]['workPhone']))).'\',
						`race`=\''.imw_real_escape_string($race).'\',
						`otherEthnicity`=\''.imw_real_escape_string($ethnicity).'\',
						`status`=\''.imw_real_escape_string($data[0]['maritalStatus']).'\',
						`dod_patient`=\''.imw_real_escape_string($data[0]['dateOfDeath']).'\'
						'.$otherFields.$whereCondition;
        // pre($sqlPatient);
		
		$resp = imw_query( $sqlPatient );

		if( $whereCondition === '' )
		{
			$pid  = imw_insert_id();
			imw_query("UPDATE `patient_data` SET `pid`='".$pid."' WHERE `id`='".$pid."'");
		}
		
		$_SESSION['patient']=$_REQUEST['patient'] = $pid;
		if($pid) { ?>
			<script>
				window.location.href=top.JS_WEB_ROOT_PATH+'/interface/patient_info/demographics/index.php';
			</script>
			<?php	
	}
		
	}
	catch(Exception $e){
	?>
		<script>
			top.fAlert("Unable to save patient from DSS.<br /><?php echo $e->getMessage(); ?>","","window.top.core_redirect_to(\""+window.top.document.getElementById('curr_main_tab').value+"\",\"\")");
			top.document.getElementById("findBy").value = "Active";
			top.document.getElementById("findByShow").value = "Active";
			top.show_loading_image('hide');
		</script>
	<?php
	}
}

//require_once(getcwd()."/../../../library/patient.php");
$allScriptPtArr = array();
$allScriptMapArr = array('#', 'First Name', 'Last Name', 'Middle Name', 'Patient ID', 'MRN', 'SSN', 'Gender', 'Street Address', 'Phone', 'Date of birth');

// DSS
$dssPtArr = array();
// $DssMapArr = array('patientDFN', 'firstName', 'middleName', 'lastName', 'patientSex', 'patientDob', 'patientSsn');
$DssMapArr = array('recordCount', 'firstName', 'lastName', 'patientSex', 'patientDob', 'patientSsn');

$findBy = $_REQUEST["findBy"];
if(empty($patient)){
	$findBy = "Nothing";      
}
else{
	if(($findBy != "Resp.LN") && ($findBy != "Ins.Policy") && ($findBy != "External MRN") && ($findBy != "Address")){
		if((strtolower($patient[0])) == "e" && ((int)substr($patient, 1) > 0)){
			$_REQUEST['patient'] = trim($_REQUEST['patient']);
			$_REQUEST['patient'] = substr($_REQUEST['patient'], 1);
			$elem_status = "Active";
			$_REQUEST["findBy"] = "External MRN";
			$findBy = "External MRN";
		}
		else{
			$elem_status = $findBy;
			$findBy = $app_base->getFindBy($patient);
		}
	}
	elseif($findBy == "External MRN"){
		if((!in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('raleighophthalmology'))) && (strtolower($patient[0]) == "e") && ((int)substr($patient, 1) > 0)){
			$_REQUEST['patient'] = trim($_REQUEST['patient']);
			$_REQUEST['patient'] = substr($_REQUEST['patient'], 1);
		}
		$elem_status = "Active";
	}
}

// GET ID OF ACCOUNT STATUS 'ACTIVE'
$acctStsId = $app_base->get_account_status_id('Active');

//break glass privilege chekc
$isBGPriv = $app_base->core_check_privilege("priv_break_glass");
$bgPriv = ($isBGPriv == true) ? "y" : "n";

//auditing search query
if(isset($_REQUEST["patient"]) && !empty($_REQUEST["patient"])){
	if($_SESSION["AUDIT_POLICIES"]["Search_Query"] == 1){
		$opreaterId = $_SESSION["authId"];
		$ip = getRealIpAddr();
		$URL = $_SERVER['PHP_SELF'];													 
		$os = getOS();
		$browserInfoArr = array();
		$browserInfoArr = _browser();
		$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
		$browserName = str_replace(";","",$browserInfo);													 
		$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		$arrAuditTrail [] = 
				array(
						"Action"=> "query_search",
						"Operater_Id"=> $opreaterId,
						"Operater_Type"=> getOperaterType($opreaterId) ,
						"IP"=> $ip,
						"MAC_Address"=> $_REQUEST['macaddrs'],
						"URL"=> $URL,
						"Browser_Type"=> $browserName,
						"OS"=> $os,
						"Machine_Name"=> $machineName,
						"Category"=> "search",
						"Category_Desc"=> $_REQUEST['onSearchTab'],		
						"New_Value"=> $_REQUEST['patient'],					
					);																		
		auditTrail($arrAuditTrail,$mergedArray);		
	}		
}

//code for barcode search
$enableBarcodeSearch = $scanConsentCategoryId = $consentSubFolderId = '';
$ptIdScanArr = explode("-",trim($_REQUEST['patient']));
if(is_numeric(trim($ptIdScanArr[0])) && is_numeric(trim($ptIdScanArr[1])) && count($ptIdScanArr)==2) {
	$enableBarcodeSearch 		= 'yes';
	$patient 					= $ptIdScanArr[0];	
	$scanConsentCategoryId 		= $ptIdScanArr[1];
	$fieldName					= "category_name";
	$tblName 					= "consent_category";
	$condtId 					= "cat_id";
	$parentFolderName 			= "Signed Consents";
	if(substr($ptIdScanArr[1],0,1)=='1') {
		$fieldName				= "package_category_name";
		$tblName 				= "consent_package";
		$condtId 				= "package_category_id";
		$parentFolderName 		= "Signed Package";
		$scanConsentCategoryId	= substr($scanConsentCategoryId,1);	
	}
	if($scanConsentCategoryId) {
		$getConsentCategoryNameQry 	= "SELECT ".$fieldName." FROM ".$tblName." WHERE ".$condtId." = '".$scanConsentCategoryId."'";
		$getConsentCategoryNameRes 	= imw_query($getConsentCategoryNameQry) or die(imw_error());
		$scanConsentCategoryName 	= 'Default';
		if(imw_num_rows($getConsentCategoryNameRes)>0) {
			$getConsentCategoryNameRow 	= imw_fetch_array($getConsentCategoryNameRes);
			$scanConsentCategoryName 	= $getConsentCategoryNameRow[$fieldName];
		}
		$chkQry = "SELECT folder_categories_id FROM ".constant("IMEDIC_SCAN_DB").".folder_categories  WHERE folder_name='".$parentFolderName."' AND parent_id=0 AND folder_status ='active' AND patient_id=0 ORDER BY folder_categories_id LIMIT 0,1";
		$chkRes = imw_query($chkQry);
		if(imw_num_rows($chkRes)>0) {
			$chkRow = imw_fetch_array($chkRes);
			$scanConsentFolderId 	= $chkRow['folder_categories_id'];
		}else {
			$insScnConsentRes 		= imw_query("INSERT INTO ".constant("IMEDIC_SCAN_DB").".folder_categories SET folder_name='".$parentFolderName."', folder_status='active',created_by='".$_SESSION["authId"]."', date_created='".date('Y-m-d H:i:s')."', modified_section='adminConMF'");	
			$scanConsentFolderId 	= imw_insert_id();
		}
		$chkSubFolderQry 			= "SELECT folder_categories_id FROM ".constant("IMEDIC_SCAN_DB").".folder_categories  WHERE folder_name='".$scanConsentCategoryName."' AND parent_id='".$scanConsentFolderId."' AND folder_status ='active' AND patient_id=0 ORDER BY folder_categories_id LIMIT 0,1";
		$chkSubFolderRes 			= imw_query($chkSubFolderQry);
		if(imw_num_rows($chkSubFolderRes)>0) {
			$chkSubFolderRow 		= imw_fetch_array($chkSubFolderRes);
			$consentSubFolderId 	= $chkSubFolderRow['folder_categories_id'];
		}else {
			$insSubFolderRow 		= imw_query("INSERT INTO ".constant("IMEDIC_SCAN_DB").".folder_categories SET folder_name='".$scanConsentCategoryName."',parent_id='".$scanConsentFolderId."', folder_status='active',created_by='".$_SESSION["authId"]."', date_created='".date('Y-m-d H:i:s')."', modified_section='adminConMF'");	
			$consentSubFolderId 	= imw_insert_id();
		}
	}
}

//id based search
if((is_numeric(trim($_REQUEST['patient'])) || $enableBarcodeSearch == 'yes') && trim($_REQUEST['findBy']) != "Ins.Policy" && trim($_REQUEST['findBy']) != "Address"){
	//Declared for TW search conditions as we need to show details of patient from TW if nothing is found in iDoc
	//If record is found in either iDoc or TW this variable will be converted into true so that no record popup condition can be bypassed
	$validateSearch = false;
	
	$patient = trim($_REQUEST['patient']);
	if($enableBarcodeSearch == 'yes') {$patient = $ptIdScanArr[0];}
	list($result, $num_all, $prevDataPtIdArr, $Total_Records) = $app_base->core_search($findBy, $elem_status, $_REQUEST['patient'], false);
	if(count($result) > 0){
		$result_first = reset($result);
		$askForReason = $app_base->core_get_restricted_status($result_first["id"]);
		?>
		<script>
			//top.tb_popup(top.document.getElementById('scan_doc_img_id'));
			var consentSubFolderId = '<?php echo $consentSubFolderId;?>';
			var srh_findBy = '<?php echo trim($_REQUEST['findBy']);?>';
		</script>        
        <?php
		if($askForReason == true){
			?>
		<html>
			<body>
				<script>
					var patId = '<?php echo $result_first["id"];?>';
					var bgPriv = '<?php echo $bgPriv;?>';
					top.core_restricted_prov_alert(patId, bgPriv,'',consentSubFolderId);
					top.show_loading_image('hide');
				</script>
			</body>
		</html>
			<?php
			exit();
		//}else if(isset($_SESSION["logged_user_type"]) && ($_SESSION["logged_user_type"] == 2 || $_SESSION["logged_user_type"] == 4)){
		}/*
		#######33 code commented as we havn't work on to-do alert yet
		else if(isset($_SESSION["logged_user_type"]) && ($_SESSION["logged_user_type"] != 1 && $_SESSION["logged_user_type"] != 3)){
			$arr_pt_todo = array();
			$sql_todo = "SELECT id FROM schedule_appointments where sa_patient_app_status_id IN(201) AND sa_patient_id = '".$result_first["id"]."'";
			$res_todo = imw_query($sql_todo);
			if(imw_num_rows($res_todo) > 0){
				
				?>
		<html>
			<body>
				<script>
					var patId = '<?php echo $result_first["id"];?>';
					top.document.getElementById("findBy").value = patId;
					top.core_to_do_alert(patId,consentSubFolderId);
					top.show_loading_image('hide');
				</script>
			</body>
		</html>
				<?php
				exit();
			}			
		}*/
		?>
		<html>
			<body>
				<script>
					var patId = '<?php echo $result_first["id"];?>';
					var req_patient = '<?php echo $_REQUEST['patient'];?>';
					var enc_postedStatus = '<?php echo $result_first["postedStatus"];?>';
					var curr_tab = top.document.getElementById("curr_main_tab").value;
					var redirect_url = top.core_get_tab_path(curr_tab);
					if(srh_findBy=="EID" && (enc_postedStatus=="" || enc_postedStatus>0)){
						top.document.getElementById("curr_main_tab").value = 'AccountingEC';
						redirect_url[0] = "../accounting/accounting_view.php?encounter_id="+req_patient+"&tabvalue=Enter_Charges&show_load=yes";
					}
					top.core_set_pt_session(top.fmain, patId, redirect_url[0],'',consentSubFolderId);
					top.document.getElementById("findBy").value = "Active";
					top.show_loading_image('hide');
				</script>
			</body>
		</html>
		<?php
		exit();
		
		$validateSearch = true; 
		
	}elseif( is_allscripts() ){
		//Allscripts Patient ID based search
		$GLOBALS['rethrow'] = true;
		include_once( $GLOBALS['srcdir'].'/allscripts/as_patient.php' );
		
		try{
			$mrnNumber = trim($_REQUEST['patient']);
			
			if( $mrnNumber == '')
				throw new asException( 'Call Error', 'Blank ID/MRN number supplied.' );
			
			/*Load Patient - If already exists with the same MRN number*/
			$sqlMRN = 'SELECT  `id` FROM `patient_data` WHERE `External_MRN_4`=\''.$mrnNumber.'\' ';
			$sqlMRN = imw_query( $sqlMRN );
			if( $sqlMRN && imw_num_rows($sqlMRN) > 0){
				$result = imw_fetch_assoc( $sqlMRN );
				?>
					<html>
						<body>
							<script>
								var patId = '<?php echo $result['id'];?>';
								var curr_tab = top.document.getElementById("curr_main_tab").value;
								var redirect_url = top.core_get_tab_path(curr_tab);
								top.core_set_pt_session(top.fmain, patId, redirect_url,'',consentSubFolderId);
								top.document.getElementById("findBy").value = "Active";
								top.document.getElementById("findByShow").value = "Active";
								top.show_loading_image('hide');
							</script>
						</body>
					</html>
				<?php
				exit;
			}else{
				//If no patient found get results from TW and confirm from user 
				/*Find details of new patient and present for confirmation*/
				$patientObj = new as_patient();
				$patientData = $patientObj->mrn( $mrnNumber );
				
				if( is_null( $patientData ) )
					throw new asException( 'Alert', 'No data returned from All Scripts.' );
				
				$ptDob = strtotime($patientData->dateofbirth);
				$ptDob = core_date_format( date('Y-m-d', $ptDob) );
				$ptAddress = core_address_format( trim($patientData->AddressLine1), trim($patientData->AddressLine2), trim($patientData->City), trim($patientData->State), trim($patientData->ZipCode) );
				
				$allScriptPtArr['Title'] = 'All Scripts Patient Search By MRN';
				$allScriptPtArr['Msg'] = 'Id not found in IMW database, result fetched from Touch Works';
				$allScriptPtArr['PtData'] = array();
				
				$tmpArr = array();
				$tmpArr['First Name'] = $patientData->firstname;
				$tmpArr['Last Name'] = $patientData->lastname;
				$tmpArr['Middle Name'] = $patientData->middlename;
				$tmpArr['Patient ID'] = $patientData->ID;
				$tmpArr['MRN'] = $patientData->MRN;
				$tmpArr['SSN'] = $patientData->ssn;
				$tmpArr['Gender'] = $patientData->gender;
				$tmpArr['Street Address'] = $ptAddress;
				$tmpArr['Phone'] = core_phone_format($patientData->HomePhone);
				$tmpArr['Date of birth'] = $ptDob;
				
				array_push($allScriptPtArr['PtData'], $tmpArr);
			}
		}
		catch( asException $e){
		?>
			<script>
				top.fAlert("No Record Found.<br /><?php echo $e->getErrorText(); ?>","","window.top.core_redirect_to(\""+window.top.document.getElementById('curr_main_tab').value+"\",\"\")");
				top.document.getElementById("findBy").value = "Active";
				top.document.getElementById("findByShow").value = "Active";
				top.show_loading_image('hide');
			</script>
		<?php	
			/*Unset the object*/
			if( isset($patientObj) && is_object($patientObj) )
				unset($patientObj);
			//exit;
			die;
			$validateSearch = true;
		}
		
	}elseif(isDssEnable() && $_REQUEST['findBy']=='External MRN') {
        //$validateSearch = true;
        $GLOBALS['rethrow'] = true;
		include_once( $GLOBALS['srcdir'].'/dss_api/dss_demographics.php');
		
		try{
			$mrnNumber = trim($_REQUEST['patient']);
			
			if( $mrnNumber == '')
				throw new asException( 'Call Error', 'Blank ID/MRN number supplied.' );
			
			/*Load Patient - If already exists with the same MRN number*/
			$sqlMRN = 'SELECT  `id` FROM `patient_data` WHERE `External_MRN_5`=\''.$mrnNumber.'\' ';
			$sqlMRN = imw_query( $sqlMRN );
			if( $sqlMRN && imw_num_rows($sqlMRN) > 0){
				$result = imw_fetch_assoc( $sqlMRN );
				?>
					<html>
						<body>
							<script>
                                var consentSubFolderId = '<?php echo $consentSubFolderId;?>';
								var patId = '<?php echo $result['id'];?>';
								var curr_tab = top.document.getElementById("curr_main_tab").value;
								var redirect_url = top.core_get_tab_path(curr_tab);
								top.core_set_pt_session(top.fmain, patId, redirect_url[0],'',consentSubFolderId);
								top.document.getElementById("findBy").value = "Active";
								top.document.getElementById("findByShow").value = "Active";
								top.show_loading_image('hide');
							</script>
						</body>
					</html>
				<?php
				exit;
			}else{
				//If no patient found get results from DSS and confirm from user 
				/*Find details of new patient and present for confirmation*/
				$objDss_demog = new Dss_demographics();
				$patientData = $objDss_demog->PT_GetPatientInfo( $mrnNumber );
                $patientData = $patientData[0];

				if( is_null( $patientData ) )
					throw new asException( 'Alert', 'No data returned from DSS.' );

				$ptDob = strtotime($patientData['patientDob']);
				$ptDob = core_date_format( date('Y-m-d', $ptDob) );
				$dssPtArr['Title'] = 'DSS Patient Search By MRN';
				$dssPtArr['Msg'] = 'Id not found in IMW database, result fetched from DSS';
				$dssPtArr['PtData'] = array();

				$tmpArr = array();
                if(isset($patientData['patientName']) && $patientData['patientName']!='' && $patientData['patientName']!='-1') {
					$tmpArr['patientDFN'] = $mrnNumber;
					
					$patient_name = explode(',', $patientData['patientName']);
					$patient_last_name = $patient_name[0];
					if ( preg_match('/\s/',$patient_name[1]) ) {
						$patient_fm_name = explode(' ', $patient_name[1]);
						$patient_first_name = !empty($patient_fm_name) ? $patient_fm_name[0] : '';
						// $patient_middle_name = !empty($patient_fm_name) ? $patient_fm_name[1] : '';
					} else {
						$patient_first_name = $patient_name[1];
						// $patient_middle_name = '';
					} 

					$tmpArr['firstName'] = $patient_first_name;
					// $tmpArr['middleName'] = $patient_middle_name;
					$tmpArr['lastName'] = $patient_last_name;

					$tmpArr['patientSex'] = $patientData['patientSex'];
					$tmpArr['patientDob'] = $patientData['patientDob'];
					$tmpArr['patientSsn'] = $patientData['patientSsn'];
                }
				array_push($dssPtArr['PtData'], $tmpArr);
                if(empty($dssPtArr['PtData'][0])==false && count($dssPtArr)>0)$validateSearch = true;
			}
		}
		catch( Exception $e){
		?>
			<script>
				top.fAlert("No Record Found.<br /><?php echo $e->getErrorText(); ?>","","window.top.core_redirect_to(\""+window.top.document.getElementById('curr_main_tab').value+"\",\"\")");
				top.document.getElementById("findBy").value = "Active";
				top.document.getElementById("findByShow").value = "Active";
				top.show_loading_image('hide');
			</script>
		<?php	
			/*Unset the object*/
			if( isset($objDss_demog) && is_object($objDss_demog) )
				unset($objDss_demog);
			//exit;
			die;
			$validateSearch = true;
		}
    }
    
		//no record found
		//This condition is implemented to show TW patient data on the search page rather than just showing popup of no record found
		if($validateSearch === false){
			?>
			<html>
				<body>
					<script>
						top.fAlert("No Record Found.","","window.top.core_redirect_to(\""+window.top.document.getElementById('curr_main_tab').value+"\",\"\")");
						top.document.getElementById("findBy").value = "Active";
						top.show_loading_image('hide');
					</script>
				</body>
			</html>
			<?php
			die();
		}
}

//Clear any finalized_id of Chart notes if exists
if(isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"])){
	$_SESSION["finalize_id"] = "";
	$_SESSION["finalize_id"] = NULL;
	unset($_SESSION["finalize_id"]);	
}

//Clear any form_id of Chart notes if exists
if(isset($_SESSION["form_id"]) && !empty($_SESSION["form_id"])){
	$_SESSION["form_id"] = "";
	$_SESSION["form_id"] = NULL;
	unset($_SESSION["form_id"]);
}

///clear current_caseid session variable
if(isset($_SESSION["current_caseid"]) && !empty($_SESSION["current_caseid"])){
	$_SESSION["current_caseid"] = "";
	$_SESSION["current_caseid"] = NULL;
	unset($_SESSION["current_caseid"]);
}

///clear current_caseid session variable
if(isset($_SESSION["new_casetype"]) && !empty($_SESSION["new_casetype"])){
	$_SESSION["new_casetype"] = "";
	$_SESSION["new_casetype"] = NULL;
	unset($_SESSION["new_casetype"]);		
}

if(empty($elem_status)){                           
	$elem_status = "Active";
}

//search begins
$arr_patient = array();
$arr_prev_patient = array();

$arr_pt = array();
$arr_prev_pt = array();

$int_total = 0;
$int_prev_total = 0;
if($findBy != ""){
	list($arr_patient, $int_total, $arr_prev_patient, $int_prev_total) = $app_base->core_search($findBy, $elem_status, $patient, true);
}

if(count($arr_prev_patient) > 0){
	//For Pt. Hx Name case --------------
	foreach($arr_prev_patient as $obj){
		$prev_key = $obj['id'];
		if(!$arr_patient[$prev_key]){
			$arr_patient[$prev_key] = $obj;
		}
	}
}

if($elem_status == 'Pt. Hx Name'){
	foreach($arr_patient as $key => $val){
		if(!isset($arr_prev_patient[$key])){
			unset($arr_patient[$key]);
		}
	}
}

$int_total = count($arr_patient);
$int_prev_total = count($arr_prev_patient);

//main search
$arr_balance = array();
$arr_pt_appt = array();
$arr_pt_todo = array();
$arr_pt_rp = array();
$str_patient = "";

if(count($arr_patient) > 0){
	foreach($arr_patient as $obj){
		$arr_pt[] = $obj["id"];
	}
	$str_patient = implode(",", $arr_pt);
}else if(is_allscripts()){
	$GLOBALS['rethrow'] = true;
		include_once( $GLOBALS['srcdir'].'/allscripts/as_patient.php' );
		try{
			$search_name_value = trim($patient);
			
			if( $search_name_value == '')
				throw new asException( 'Call Error', 'Blank value supplied to search.' );
			
			
			/*Find details of new patient and present for confirmation*/
			$patientObj = new as_patient();
			$patientDataSet = $patientObj->PtName( $search_name_value );
			
			if( is_null( $patientDataSet ) && count($patientDataSet) > 0 ) throw new asException( 'Alert', 'No data returned from All Scripts.' );
			
			$allScriptPtArr['Title'] = 'All Scripts Patient Search';
			$allScriptPtArr['Msg'] = 'Id not found in IMW database, result fetched from Touch Works';
			$allScriptPtArr['PtData'] = array();
			
			foreach( $patientDataSet as $patientData ){
			$ptDob = strtotime($patientData->dateofbirth);
				$ptDob = core_date_format( date('Y-m-d', $ptDob) );
				$ptAddress = core_address_format( trim($patientData->AddressLine1), trim($patientData->AddressLine2), trim($patientData->City), trim($patientData->State), trim($patientData->ZipCode) );
			
				$tmpArr = array();
				$tmpArr['First Name'] = $patientData->firstname;
				$tmpArr['Last Name'] = $patientData->lastname;
				$tmpArr['Middle Name'] = $patientData->middlename;
				$tmpArr['Patient ID'] = $patientData->ID;
				$tmpArr['MRN'] = $patientData->MRN;
				$tmpArr['SSN'] = $patientData->ssn;
				$tmpArr['Gender'] = $patientData->gender;
				$tmpArr['Street Address'] = $ptAddress;
				$tmpArr['Phone'] = core_phone_format($patientData->HomePhone);
				$tmpArr['Date of birth'] = $ptDob;
				
				array_push($allScriptPtArr['PtData'], $tmpArr);
			}
		}
		catch( asException $e){
		?>
			<script>
				top.fAlert("No Record Found.<br /><?php echo $e->getErrorText(); ?>","","window.top.core_redirect_to(\""+window.top.document.getElementById('curr_main_tab').value+"\",\"\")");
				top.document.getElementById("findBy").value = "Active";
				top.document.getElementById("findByShow").value = "Active";
				top.show_loading_image('hide');
			</script>
		<?php	
			/*Unset the object*/
			if( isset($patientObj) && is_object($patientObj) )
				unset($patientObj);
			//exit;
			die;
		}
		
                } 
                
//For TW Data
if(is_allscripts() && isset($allScriptPtArr['PtData']) && count($allScriptPtArr['PtData']) > 0) $int_total = count($allScriptPtArr['PtData']) + $int_total;
// ----------------------------------
// $int_search_total = $int_prev_total + $int_total;

// Temporary fix for the DSS to override the popup for no patient found in iDoc
// Need an approval for those coditions
if( isDssEnable() ) {
$int_search_total = ( ($int_prev_total + $int_total) == 0 ) ? 1 : $int_prev_total + $int_total;
$int_total = ( $int_total == 0 ) ? 1 : $int_total;
} else {
	$int_search_total = $int_prev_total + $int_total;	
}


//prev patient search
$str_prev_patient = "";
if(count($arr_prev_patient) > 0){
	foreach($arr_prev_patient as $obj){
		if(count($obj) > 0){
			$arr_prev_pt[] = $obj["id"];
		}
	}
	$str_prev_patient = implode(",", $arr_prev_pt);
}

$str_all_pt = "";
if($str_prev_patient != "" && $str_patient != ""){
	$str_all_pt = $str_prev_patient.",".$str_patient;
}else if($str_patient != ""){
	$str_all_pt = $str_patient;
}else if($str_prev_patient != ""){
	$str_all_pt = $str_prev_patient;
}


if($str_all_pt != ""){
	//check total ids length
	$total_ids_arr=explode(',',$str_all_pt);
	$total_ids_arr=array_unique($total_ids_arr);
	$total_ids=count($total_ids_arr);
	$str_all_pt=implode(',',$total_ids_arr);
	
	$where=$join="";
	if(  $total_ids>10)
	{
		$tmp_table="PatientIdList_".time().'_'.$_SESSION["authId"];
		//add temporary table here
		imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);$mysql_error .= imw_error();
		imw_query("CREATE TEMPORARY TABLE ".$tmp_table." (id INT)");$mysql_error .= imw_error();
		$insertValues=str_replace(',','),(',$str_all_pt);
		
		imw_query("INSERT INTO $tmp_table (id) VALUES($insertValues)");$mysql_error .= imw_error();
		$join=" INNER JOIN ".$tmp_table." pl ON pcl.patient_id  = pl.id ";
	}
	else{
		//add IN clause in query
		$where="pcl.patient_id IN (".$str_all_pt.") AND ";
	}
	
	$sql = "select sum(pcl.totalBalance) as totalBalance, pcl.patient_id from patient_charge_list pcl $join where $where pcl.del_status='0' GROUP BY pcl.patient_id";
	/*$sql = "select sum(totalBalance) as totalBalance, patient_id from patient_charge_list where del_status='0' and patient_id IN (".$str_all_pt.") GROUP BY patient_id";*/
	$res = imw_query($sql);	$mysql_error .= imw_error();
	if(imw_num_rows($res) > 0){
		while($arr = imw_fetch_assoc($res)){
			$temp = $arr["patient_id"];
			$arr_balance[$temp] = $arr["totalBalance"];
		}
	}
	$where=$join="";
	if(  $total_ids>10)
	{
		$join=" INNER JOIN ".$tmp_table." pl ON sa.sa_patient_id  = pl.id ";
	}
	else{
		//add IN clause in query
		$where="sa.sa_patient_id IN (".$str_all_pt.") AND ";
	}
	$sql_appt = "SELECT sa.sa_patient_id, date_format(sa.sa_app_start_date,'%m-%d-%Y') as ap_start_date, sa.sa_app_starttime, sa.procedureid, sp.proc, sp.acronym FROM schedule_appointments sa
			INNER JOIN slot_procedures sp ON (sp.id=sa.procedureid) 
			$join
			WHERE $where sa.sa_patient_app_status_id NOT IN(203,201,18,19,20,3) 
			AND CONCAT(sa.sa_app_start_date, ' ', sa.sa_app_starttime) > NOW()";
	/*$sql_appt = "SELECT sa.sa_patient_id, date_format(sa.sa_app_start_date,'%m-%d-%Y') as ap_start_date, sa.sa_app_starttime, sa.procedureid, sp.proc, sp.acronym FROM schedule_appointments sa
			JOIN slot_procedures sp ON (sp.id=sa.procedureid) 
			WHERE sa.sa_patient_id IN (".$str_all_pt.") AND sa.sa_patient_app_status_id NOT IN(203,201,18,19,20,3) 
			AND CONCAT(sa.sa_app_start_date, ' ', sa.sa_app_starttime) > NOW()";*/
	$res_appt = imw_query($sql_appt);$mysql_error .= imw_error();
	if(imw_num_rows($res_appt) > 0){
		while($arr_appt = imw_fetch_assoc($res_appt)){
			$str_apt_details = "";
			$str_apt_details .= $arr_appt['ap_start_date'].' '.core_time_format($arr_appt['sa_app_starttime']).' ';
			$str_apt_details .= $arr_appt['acronym']=='' ? $arr_appt['proc'] : $arr_appt['acronym'];
			$arr_pt_appt[$arr_appt["sa_patient_id"]] = $str_apt_details;
		}
	}
	
	$where=$join="";
	if(  $total_ids>10){
		$join=" INNER JOIN ".$tmp_table." pl ON sa.sa_patient_id  = pl.id ";
	}
	else{
		$where=" and sa.sa_patient_id IN (".$str_all_pt.") ";
	}
	$sql_todo = "select sa.id, sa.sa_patient_id from schedule_appointments as sa $join where sa.sa_patient_app_status_id IN (201) $where";
	$res_todo = imw_query($sql_todo);
	if(imw_num_rows($res_todo) > 0){
		while($arr_todo = imw_fetch_assoc($res_todo)){
			$arr_pt_todo[$arr_todo["sa_patient_id"]] = $arr_todo["sa_patient_id"];
		}
	}
	
	$where=$join="";
	if(  $total_ids>10){
		$join=" INNER JOIN ".$tmp_table." pl ON rp.patient_id  = pl.id ";
	}
	else{
		$where=" rp.patient_id IN (".$str_all_pt.") and ";
	}
	$sql_rp = "SELECT  rp.restrict_providers,  rp.patient_id FROM restricted_providers rp $join where $where rp.restrict_providers != ''";
	/*$sql_rp = "SELECT restrict_providers, patient_id FROM restricted_providers where patient_id IN (".$str_all_pt.") and restrict_providers != ''";*/
	$res_rp = imw_query($sql_rp);
	if(imw_num_rows($res_rp) > 0){
		while($arr_rp = imw_fetch_assoc($res_rp)){
			if(isset($_SESSION["glassBreaked_ptId"]) && $_SESSION["glassBreaked_ptId"] == $arr_rp["patient_id"]){
				continue;
			}
			$explodeArray = explode(",", $arr_rp["restrict_providers"]);
			if(in_array($_SESSION["authId"], $explodeArray)){
				$arr_pt_rp[$arr_rp["patient_id"]] = $arr_rp["patient_id"];
			}
		}
	}
}

//setting result div heights
$int_pt_height = (($_SESSION["wn_height"]-400)/2);
$int_prev_pt_height = (($_SESSION["wn_height"]-400)/2);
if($int_search_total != 0){
	if($int_total > 0 && $int_prev_total > 0){
		//inherit values set above
	}else if($int_total > 0){
		$int_pt_height = $_SESSION["wn_height"]-305;
		$int_prev_pt_height = 0;
	}else if($int_prev_total > 0){
		$int_pt_height = 0;
		$int_prev_pt_height = $_SESSION["wn_height"]-305;
	}
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>imwemr</title>
    
    <!-- Bootstrap -->
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-dropdownhover.min.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/html5shiv.min.js"></script>
      <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<?php

	if($int_search_total == 0){
		
		?>
	<script>
		curr_main_tab_val = window.top.document.getElementById('curr_main_tab').value;
		top.fAlert("No Record Found.","","window.top.core_redirect_to(\""+curr_main_tab_val+"\", \"\")");
		top.document.getElementById("findBy").value = "Active";
		top.show_loading_image("hide");
	</script>
		<?php
		exit();
	}else{?>
		<div class="table-responsive">
    <?php
    if($int_total > 0){
	?>
<div class="clearfix"></div>
<div class="whtbox">
	<form id="asPatientSearch" method="POST">
		<input type="hidden" name="asSavePatient" value="true" />
		<input type="hidden" name="asPatientId" id="asPatientId" value="" />
	</form>	

			<form id="dssPatientDemographics" method="POST">
				<input type="hidden" name="dssSavePatient" value="true" />
				<input type="hidden" name="dssPatientDfn" id="dssPatientDfn" value="" />
			</form>	

	<div id="currentRecordDivId" style="overflow:auto; overflow-x:hidden;">
		<div class="section m5">
			<table class=" table table-striped table-hover table-bordered">
				<tr class="purple_bar">
				<?php 
					if(constant("EXTERNAL_MRN_SEARCH") == "YES"){
						$colspan=6;
					}else{
						$colspan=5;
					}
				?>
					<td colspan="<?php echo $colspan; ?>" style="border-right:none!important"><b>Patient Search</b></td>
					<td colspan="<?php echo $colspan; ?>" style="border-left:none!important" class="text-right"><span id="currentResultSpanId"><b><?php if(count($arr_patient)>0){?>Total number of matching Patient found: <?php echo count($arr_patient); ?><?php }?></b></span></td>
				</tr>
				<tr class="grythead">
					<td class="text-center">#</td>
					<td>First Name</td>
					<td>Last Name</td>
					<td class="text-center">Patient ID</td>
					<td>Gender</td>
					<td>Street Address</td>
					<td>Phone</td>						
					<td class="text-center">Date of birth</td>					
					<td class="text-center" title="Outstanding Balance">Balance</td>
					<td class="text-center" title="First Future Appointment" nowrap>Future Appt</td>
					<?php
					if(constant("EXTERNAL_MRN_SEARCH") == "YES"){
					?>
						<td title="External ID 1">External ID 1</td>
						<td title="External ID 2">External ID 2</td>
					<?php 
					}
					?>
				</tr>
				<tbody class="result_data">
				<?php
				$c = 1;
				if(count($arr_patient) > 0){
					foreach($arr_patient as $iter){
						//pre($iter,1);
						//balance
						$balance = "";
						if(isset($arr_balance[$iter["id"]])){
							$bal = 	$arr_balance[$iter["id"]];
							$balance = core_currency_format($bal, 2);
						}
						$balance = ($balance == '') ? 'N/A' : $balance;
						
						//future appt details
						$apptDetails = "";
						if(isset($arr_pt_appt[$iter["id"]])){
							$apptDetails = 	$arr_pt_appt[$iter["id"]];
						}
						$apptDetails = ($apptDetails == '') ? 'N/A' : $apptDetails;
						
						//todo
						$onClick = "";
						if(isset($arr_pt_todo[$iter["id"]])){
							$onClick = 	"show_todo";
						}

						//restricted alert
						$onClickAskForReason = false;
						if(isset($arr_pt_rp[$iter["id"]])){
							$onClickAskForReason = true;
						}
						$rp_alert = (($onClickAskForReason == true) ? "y" : "n");

						//address
						$ptAddr = core_address_format(trim($iter['street']), trim($iter['street2']), trim($iter['city']), trim($iter['state']), trim($iter['postal_code']));
						
						//check account status
						$fontColor = '';
						if($iter['pat_account_status']>0 && $iter['pat_account_status']!=$acctStsId){
							$fontColor = 'style="color:#CC0000;"';
						}
						$high_lg_class = '';
						if($arr_prev_patient[$iter['id']]){
							$high_lg_class = "hg_light";
						}
						
						//To show history row
						$hx_row_onclick = '';
						$hx_row_class = '';
						if($arr_prev_patient[$iter['id']]){
							$hx_row_onclick = "onclick=\"show_hx_rw('".$iter['id']."')\"";
							$hx_row_class = 'text_purple';
						}
						$patAccStatusHTML = '';
						//($iter['patientStatus']=='Deceased'?'danger':'info');
						if( $elem_status == 'Active' && ($iter['patientStatus'] == 'Deceased' || $iter['patientStatus'] == 'Inactive') )
							$patAccStatusHTML = '<span class="pull-right btn-sm btn-danger" style="padding:1px 5px; border-radius:0px;">'.$iter['patientStatus'].'</span>';
						

						$phone_string = "";	
						$phone_string .= $iter["phone_home"] ? "(H) ".core_phone_format($iter["phone_home"])."<br>" : "" ;
						$phone_string .= $iter["phone_biz"] ? "(W) ".core_phone_format($iter["phone_biz"])."<br>" : "" ;
						$phone_string .= $iter["phone_cell"] ? "(M) ".core_phone_format($iter["phone_cell"])."<br>" : "" ;
						$phone_string = trim($phone_string);
						$phone_string = $phone_string ? substr($phone_string,0,-4) : "";
						?>
						<tr valign="top" class="link_cursor <?php echo $high_lg_class;?> prt_row_<?php echo $iter['id']; ?>" data-calling="no"  <?php echo $fontColor;?>>
							<td class="text-center <?php echo $hx_row_class; ?>" <?php echo $hx_row_onclick; ?>><?php echo $c; ?></td>		
							<td onClick="javascript:top.load_patient('<?=$iter["id"]?>', '<?php print $onClick; ?>', '<?php echo $bgPriv;?>', '<?php print $rp_alert; ?>');"><?php echo $iter["fname"];  if(!empty($iter{"mname"})){echo ' '.$iter{"mname"}.'.';} ?></td>
							<td onClick="javascript:top.load_patient('<?=$iter["id"]?>', '<?php print $onClick; ?>', '<?php echo $bgPriv;?>', '<?php print $rp_alert; ?>');"><?php echo $iter["lname"].$patAccStatusHTML;?></td>
							<td class="text-center" onClick="javascript:top.load_patient('<?=$iter["id"]?>', '<?php print $onClick; ?>', '<?php echo $bgPriv;?>', '<?php print $rp_alert; ?>');"><?php echo $iter["id"];?>&nbsp;</td>
							<td onClick="javascript:top.load_patient('<?=$iter["id"]?>', '<?php print $onClick; ?>', '<?php echo $bgPriv;?>', '<?php print $rp_alert; ?>');"><?php echo $iter["sex"];?></td>
							<td onClick="javascript:top.load_patient('<?=$iter["id"]?>', '<?php print $onClick; ?>', '<?php echo $bgPriv;?>', '<?php print $rp_alert; ?>');"><?php echo $ptAddr;?></td>
							<td onClick="javascript:top.load_patient('<?=$iter["id"]?>', '<?php print $onClick; ?>', '<?php echo $bgPriv;?>', '<?php print $rp_alert; ?>');"><?php echo $phone_string; ?></td>
						<?php 
						if($iter["DOB"] != "0000-00-00"){
							?>
							<td class="text-center" onClick="javascript:top.load_patient('<?=$iter["id"]?>', '<?php print $onClick; ?>', '<?php echo $bgPriv;?>', '<?php print $rp_alert; ?>');"><?php echo core_date_format($iter["DOB"]); ?></td>
							<?php 
						}else{
							?>
							<td onClick="javascript:top.load_patient('<?=$iter["id"]?>', '<?php print $onClick; ?>', '<?php echo $bgPriv;?>', '<?php print $rp_alert; ?>');" class="text-center">N/A</td>
							<?php 
						}
						?>										
							<td onClick="javascript:top.load_patient('<?=$iter["id"]?>', '<?php print $onClick; ?>', '<?php echo $bgPriv;?>', '<?php print $rp_alert; ?>');" class="text-center"><?php echo($balance);?>&nbsp;</td>	
							<td onClick="javascript:top.load_patient('<?=$iter["id"]?>', '<?php print $onClick; ?>', '<?php echo $bgPriv;?>', '<?php print $rp_alert; ?>');" class="text-center" nowrap="nowrap"><?php echo $apptDetails; ?>&nbsp;</td>
							<?php
							if(constant("EXTERNAL_MRN_SEARCH") == "YES"){
							?>
							<td class="text-right" onClick="javascript:top.load_patient('<?=$iter["id"]?>', '<?php print $onClick; ?>', '<?php echo $bgPriv;?>', '<?php print $rp_alert; ?>');">
							<?php 
								if(strlen($iter["External_MRN_1"]) == 6){
									echo $iter["External_MRN_1"]; 
								}
								else{
									echo $iter["External_MRN_1"];
								}
							?>
								&nbsp;
							</td>
							<td class="text-right" onClick="javascript:top.load_patient('<?=$iter["id"]?>', '<?php print $onClick; ?>', '<?php echo $bgPriv;?>', '<?php print $rp_alert; ?>');">
								<?php 
								if(strlen($iter["External_MRN_2"]) == 6){
									echo $iter["External_MRN_2"]; 
								}
								else{
									echo $iter["External_MRN_2"]; 
								}
								?>
								&nbsp;</td>
							<?php 
							}
							?>
						</tr>
						<?php
							//Prev. Pt data
							$prev_pt_dt = $arr_prev_patient[$iter['id']];
							if(is_array($prev_pt_dt) && count($prev_pt_dt) > 0){
								//Prev Pt balance
								$pv_pt_balance = "";
								if(isset($arr_balance[$prev_pt_dt["id"]])){
									$bal = 	$arr_balance[$prev_pt_dt["id"]];
									$pv_pt_balance = core_currency_format($bal, 2);
								}						
								$pv_pt_balance = ($pv_pt_balance == '') ? 'N/A' : $pv_pt_balance;
								
								//Prev pt appt 
								//future appt details
								$pv_pt_appt = "";
								if(isset($arr_pt_appt[$prev_pt_dt["id"]])){
									$pv_pt_appt = 	$arr_pt_appt[$prev_pt_dt["id"]];										
								}
								$pv_pt_appt = ($pv_pt_appt == '') ? 'N/A' : $pv_pt_appt;
								
								//todo
								$onClick = "";
								if(isset($arr_pt_todo[$prev_pt_dt["id"]])){
									$onClick = 	"show_todo";
								}
								
								//restricted alert
								$onClickAskForReason = false;
								if(isset($arr_pt_rp[$prev_pt_dt["id"]])){
									$onClickAskForReason = true;
								}
								$rp_alert = (($onClickAskForReason == true) ? "y" : "n");	
								
								//address
								$ptOriginalAddr = core_address_format(trim($prev_pt_dt['street']), trim($prev_pt_dt['street2']), trim($prev_pt_dt['city']), trim($prev_pt_dt['state']), trim($prev_pt_dt['postal_code']));
								
								//previ data to show
								$previousRecordQry = "SELECT prev_fname, prev_mname, prev_lname, prev_sex, prev_street, prev_street2, prev_city, prev_state, prev_postal_code, prev_phone_home FROM patient_previous_data WHERE patient_id = '".$prev_pt_dt['id']."' ORDER BY save_date_time DESC";
								$previousRecordRes = imw_query($previousRecordQry);
								$previousRecordNumRow = imw_num_rows($previousRecordRes);
								
								$prevFname = $prevLname = $prevAddr = $prevPhone = "";
								
								if($previousRecordNumRow>0){
									while($previousRecordRow = imw_fetch_array($previousRecordRes)) {
										$prevStreet2 = "";
										$prevCityStateZip =	"";
												
										if($previousRecordRow['prev_fname']) {
											$prevFname .= '<span><s class="text-danger"><span>'.stripslashes($previousRecordRow['prev_fname']);
											if(!empty($previousRecordRow['prev_nname'])){$prevFname .= ' '.$previousRecordRow['prev_nname'].'.';}
											$prevFname .= '</span></s></span><br>';
										}
										if($previousRecordRow['prev_lname']) {
											$prevLname .= '<span><s class="text-danger"><span>'.stripslashes($previousRecordRow['prev_lname']).'</span></s></span><br>';
										}
										if($previousRecordRow['prev_sex']) {
											$prevSex .=	'<span><s class="text-danger"><span>'.stripslashes($previousRecordRow['prev_sex']).'</span></s></span><br>';
										}
										
										$prevFullAddr = core_address_format(trim($previousRecordRow['prev_street']), trim($previousRecordRow['prev_street2']), trim($previousRecordRow['prev_city']), trim($previousRecordRow['prev_state']), trim($previousRecordRow['prev_postal_code']));
										if($prevFullAddr){
											$prevAddr .= '<span><s class="text-danger"><span>'.$prevFullAddr.'</span></s></span><br>';
										}
												
										if($previousRecordRow['prev_phone_home']) {
											$prevPhone .= '<span><s class="text-danger"><span>'.stripslashes(core_phone_format($previousRecordRow['prev_phone_home'])).'</span></s></span><br>';
										}
									}
								}
								?>
								<tr class=" hide hx_row_<?php echo $iter['id']; ?>">
									<td class="text-center" valign="top" ><?php echo $c2;?></td>		
									<td valign="top"><?php echo $prevFname;?></td>
									<td valign="top"><?php echo $prevLname;?></td>
									<td class="text-center" valign="top"><?php echo($prev_pt_dt["id"]);?>&nbsp;</td>
									<td valign="top"><?php echo $prevSex;?></td>
									<td valign="top"><?php echo $prevAddr;?></td>
									<td class="text-left" valign="top"><?php echo $prevPhone;?></td>
									<td class="text-center" valign="top">&nbsp;</td>
									<td class="text-center" valign="top">&nbsp;</td>
									<td class="text-center" valign="top">&nbsp;</td>
								</tr>
								<?php
							}
						$c++;
						$total++;
					}
				}else{
					$emptyCol = 10;
					if(constant("EXTERNAL_MRN_SEARCH") == "YES") $emptyCol = 12;
					echo '<tr><td colspan="'.$emptyCol.'" class="text-center">No Record Found</td></tr>';
				}
				if(count($arr_patient) > 0 && is_array($allScriptPtArr) && !isset($allScriptPtArr['PtData']) && is_allscripts()){
					$emptyCol = 10;
					if(constant("EXTERNAL_MRN_SEARCH") == "YES") $emptyCol = 12;
					
					echo '<tr><td colspan="'.$emptyCol.'" class="text-center"><button class="btn btn-primary" id="searchRemotePt" data-find="'.$_REQUEST["findBy"].'" data-string="'.$_REQUEST['patient'].'" onClick="getRemotePatients(this);">Search in Remote Patients</button></td></tr>';
				} 

				// pre($_SESSION);
				if(isDssEnable()){
					echo '<tr><td colspan="12" class="text-center"><button class="btn btn-primary" id="searchRemotePtDss" data-find="'.$_REQUEST["findBy"].'" data-string="'.$_REQUEST['patient'].'" onClick="getRemotePatientsDss(this);">Search in Remote Patients</button></td></tr>';
				}

				?>
				</tbody>
			</table>
		</div>
		<?php 
			//Allscripts Patient Data
			if(is_allscripts() && is_array($allScriptPtArr) && count($allScriptPtArr['PtData']) > 0){
				?>
				<div class="section m5">
					<table class=" table table-striped table-hover table-bordered">
						<tr class="purple_bar">
							<td colspan="5" style="border-right:none!important"><b><?php echo $allScriptPtArr['Title']; ?></b></td>
							<td colspan="4" style="border-right:none!important"><b><?php echo $allScriptPtArr['Msg']; ?></b></td>
							<td colspan="2" style="border-left:none!important" class="text-right"><span id="currentResultSpanId"><b><?php if(count($allScriptPtArr['PtData'])>0){?>Total number of matching Patient found: <?php echo count($allScriptPtArr['PtData']); ?><?php }?></b></span></td>
						</tr>
						<tr class="grythead">
							<?php 
								//Title Row
								foreach($allScriptMapArr as $mapField){
									if($mapField == '#') echo '<td class="text-center">'.$mapField.'</td>';
									else echo '<td>'.$mapField.'</td>';
								}
							?>
						</tr>
						<tbody class="result_data">
							<?php 
								if(isset($allScriptPtArr['PtData']) && count($allScriptPtArr['PtData']) > 0){
									$counterAllScript = 1;
									foreach($allScriptPtArr['PtData'] as $ptObj){
										$mapCounter = 1;
										foreach($allScriptMapArr as $mapField){
											if($mapCounter == 1) echo '<tr asPtId="'.$ptObj['Patient ID'].'" class="pointer">';
											if($mapField == '#') echo '<td class="text-center">'.$counterAllScript.'</td>';
											else echo '<td >'.$ptObj[$mapField].'</td>';
											if($mapCounter == count($allScriptMapArr)) echo '</tr>';
											$mapCounter++;
										}
										$counterAllScript++;
									}
								}else{
									echo '<tr><td colspan="'.count($allScriptPtArr['PtData']).'" class="text-center">No Record Found</td></tr>';
								}
							?>
						</tbody>
					</table>
				</div>
				<?php
			}
		?>
        <?php
			//Allscripts Patient Data
			if(isDssEnable() && is_array($dssPtArr) && count($dssPtArr['PtData']) > 0){
				?>
				<div class="section m5">
					<table class=" table table-striped table-hover table-bordered">
						<tr class="purple_bar">
							<td colspan="5" style="border-right:none!important"><b><?php echo $dssPtArr['Title']; ?></b></td>
							<td colspan="4" style="border-right:none!important"><b><?php echo $dssPtArr['Msg']; ?></b></td>
							<td colspan="2" style="border-left:none!important" class="text-right"><span id="currentResultSpanId"><b><?php if(count($dssPtArr['PtData'])>0){?>Total number of matching Patient found: <?php echo count($dssPtArr['PtData']); ?><?php }?></b></span></td>
						</tr>
                        </table><table class=" table table-striped table-hover table-bordered">
						<tr class="grythead">
							<?php 
								//Title Row
								foreach($DssMapArr as $mapField){
									if(!isset($mapField)) echo '<td></td>';
									elseif($mapField == 'recordCount') echo '<td>#</td>';
									// elseif($mapField == 'patientDFN') echo '<td>DFN</td>';
									elseif($mapField == 'firstName') echo '<td>First Name</td>';
									elseif($mapField == 'middleName') echo '<td>Middle Name</td>';
									elseif($mapField == 'lastName') echo '<td>Last Name</td>';
									elseif($mapField == 'patientSex') echo '<td>Gender</td>';
									elseif($mapField == 'patientDob') echo '<td>Date of Birth</td>';
									elseif($mapField == 'patientSsn') echo '<td>SSN</td>';
									else echo '<td>'.strtoupper($mapField).'</td>';
								}
							?>
						</tr>
						<tbody class="result_data">
							<?php 
								if(isset($dssPtArr['PtData']) && count($dssPtArr['PtData']) > 0){
									$counterDss = 1;
									foreach($dssPtArr['PtData'] as $ptObj){
										$mapCounter = 1;
										foreach($DssMapArr as $mapField){
											if($mapCounter == 1) echo '<tr dssPtDfn="'.$ptObj['patientDFN'].'" class="pointer">';
											if($mapField == 'recordCount') echo '<td>'.$counterDss.'</td>';
											else echo '<td >'.$ptObj[$mapField].'</td>';
											if($mapCounter == count($DssMapArr)) echo '</tr>';
											$mapCounter++;
										}
										$counterDss++;
									}
								}else{
									echo '<tr><td colspan="'.count($dssPtArr['PtData']).'" class="text-center">No Record Found</td></tr>';
								}
							?>
						</tbody>
					</table>
				</div>
				<?php
			}
		?>
        <!--<input type="hidden" name="query_errors" id="query_errors" value="<?php echo $mysql_error;?>">-->
 	</div>
	
	<!-- Show Remote patient search results -->
	<div id="remotePatientSearch" class="modal" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal_90">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Remote Patient Search Result</h4>
				</div>
				<div class="modal-body"></div>
				<div id="module_buttons" class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	
</div>
<?php } ?>
</div>

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
					<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
                    <!-- Include all compiled plugins (below), or include individual files as needed -->
                    <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
					<script type="text/javascript">
					
						//To show/hide hx rows
						function show_hx_rw(hx_row){
							var prt_elem = $('.prt_row_'+hx_row);
							var child_elem = $('.hx_row_'+hx_row);
							var calling = prt_elem.data('calling');
							
							if(calling == 'no'){
								if(child_elem.hasClass('hide') === true){
									child_elem.removeClass('hide');
									prt_elem.data('calling','yes');
								}
							}else{
								if(child_elem.hasClass('hide') === false){
									child_elem.addClass('hide');
									prt_elem.data('calling','no');
								}
							}
						}
						
						function getRemotePatients(obj){
							$('#currentRecordDivId .remotePt').remove();
							
							var findBy = $(obj).data('find');
							var searchStr = $(obj).data('string');
							
							$.ajax({
								url:top.JS_WEB_ROOT_PATH+'/interface/core/ajax_handler.php',
								type:'GET',
								data:{task:'remote_patient_search', findBy:findBy, searchStr:searchStr},
								dataType:'JSON',
								beforeSend:function(){
									top.show_loading_image('show');
								},
								success:function(response){
									if(response.Error){
										top.fAlert(response.Error);
										return false;
									}
									
									var htmlStr = '';
									var mapArr = <?php echo json_encode($allScriptMapArr); ?>;
									
									var fieldStr = '';
									
									//Title Row
									fieldStr += '<tr class="purple_bar">';
									fieldStr += 	'<td colspan="5" style="border-right:none!important"><b>'+response.title+'</b></td>';
									fieldStr += 	'<td colspan="4" style="border-right:none!important"><b>'+response.msg+'</b></td>';
									fieldStr += 	'<td colspan="2" style="border-left:none!important" class="text-right"><span><b>Total number of matching Patient found: <span id="currentResultSpanId11"></span></b></span></td>';
									fieldStr += '</tr>';
									
									var mapFieldss = '';
									$.each(mapArr, function(id, val){
										if(val == '#') mapFieldss += '<td class="text-center">'+val+'</td>';
										else mapFieldss += '<td>'+val+'</td>';
									});
									
									if(mapFieldss !== '') fieldStr += '<tr class="grythead">'+mapFieldss+'</tr>';
									
									var totalFields = 0;
									if(Object.keys(response).length){
										$.each(response, function(id,result){
											if($.isNumeric(id) === true){
												var counter11 = 1;
												$.each(mapArr, function(id, val){
													if(counter11 == 1) htmlStr += '<tr asPtId="'+result['Patient ID']+'" class="pointer">';
													
													if(val == '#') htmlStr += '<td class="text-center">'+result[val]+'</td>';
													else htmlStr += '<td>'+result[val]+'</td>';
													
													if(counter11 == Object.keys(mapArr).length) htmlStr+= '</tr>';
													counter11++;
												});
												totalFields++;
											}
										});
									}
									
									var modalStr = '<div class="section m5 remotePt"><table class="table table-striped table-hover table-bordered">'+fieldStr+htmlStr+'</table></div>';
									$('#currentRecordDivId').append(modalStr);
									$('#currentResultSpanId11').html(totalFields);
									
									setHeight();
									
									return false;
								},
								complete:function(){
									top.show_loading_image('hide');
								}
							});
						}
						
						function setHeight(){
							var totalHeight = parseInt(top.fmain.top.fmain.innerHeight - 50);
							var newHeight = parseInt(totalHeight / $('#currentRecordDivId .section').length);
							
							$('#currentRecordDivId .section').css({
								'height' : newHeight,
								'max-height' : newHeight,
								'overflowY' : 'auto'
							});
						}
						
						function getRemotePatientsDss(obj){
							$('#currentRecordDivId .remotePt').remove();
							
							var findBy = $(obj).data('find');
							var searchStr = $(obj).data('string');
							
							$.ajax({
								url:top.JS_WEB_ROOT_PATH+'/interface/core/ajax_handler.php',
								type:'GET',
								data:{task:'dss_remote_patient_search', findBy:findBy, searchStr:searchStr},
								dataType:'JSON',
								beforeSend:function(){
									top.show_loading_image('show');
								},
								success:function(response){
									if(response.Error){
										top.fAlert(response.Error);
										return false;
									}
									if(response.status == 'error') {
										top.fAlert(response.data);
										return false;	
									}

									var htmlStr = '';
									var mapArr = <?php echo json_encode($DssMapArr); ?>;
									var fieldStr = '';
									
									//Title Row
									fieldStr += '<tr class="purple_bar">';
									fieldStr += '<td colspan="11" style="border-left:none!important" class="text-center"><span><b>Total number of matching Patient found on DSS: <span id="currentResultSpanId11"></span></b></span></td>';
									fieldStr += '</tr>';
									
									var mapFieldss = '';
									$.each(mapArr, function(id, val){
										if(typeof val == 'undefined') mapFieldss += '<td></td>';
										else if(val == 'recordCount') mapFieldss += '<td>#</td>';
										// else if(val == 'patientDFN') mapFieldss += '<td>DFN</td>';
										else if(val == 'firstName') mapFieldss += '<td>First Name</td>';
										// else if(val == 'middleName') mapFieldss += '<td>Middle Name</td>';
										else if(val == 'lastName') mapFieldss += '<td>Last Name</td>';
										else if(val == 'patientSex') mapFieldss += '<td>Gender</td>';
										else if(val == 'patientDob') mapFieldss += '<td>Date of Birth</td>';
										else if(val == 'patientSsn') mapFieldss += '<td>SSN</td>';
										else mapFieldss += '<td>'+val.toUpperCase()+'</td>';
									});
									
									if(mapFieldss !== '') fieldStr += '<tr class="grythead">'+mapFieldss+'</tr>';
									
									var totalFields = 0;
									if(Object.keys(response.data).length){
										$.each(response.data, function(id,result){
											if(result['patientDFN'] == 0) {
												htmlStr += '<tr class="pointer">';
												htmlStr += '<td colspan="'+Object.keys(mapArr).length+'">No matching Patient found on DSS.</td>';
												htmlStr+= '</tr>';
											} else {
												if($.isNumeric(id) === true){
													var counter11 = 1;
													$.each(mapArr, function(id, val){
														if(counter11 == 1) htmlStr += '<tr dssPtDfn="'+result['patientDFN']+'" class="pointer">';
														if (typeof result[val] == "undefined") {
															htmlStr += '<td></td>';
														} else {
															htmlStr += '<td>'+result[val]+'</td>';
														}
														
														if(counter11 == Object.keys(mapArr).length) htmlStr+= '</tr>';
														counter11++;
													});
													totalFields++;
												}
											}
										});
									}
									
									var modalStr = '<div class="section m5 remotePt"><table class="table table-striped table-hover table-bordered">'+fieldStr+htmlStr+'</table></div>';
									$('#currentRecordDivId').append(modalStr);
									$('#currentResultSpanId11').html(totalFields);
									
									setHeight();
									
									return false;
								},
								complete:function(){
									top.show_loading_image('hide');
								}
							});
						}
						
						$(function(){
							$( 'body' ).on('click','tr[asPtId]', function(){
								var asPtId  = $(this).attr('asPtId');
								var form = $('#asPatientSearch');
								$( form ).find('#asPatientId').val(asPtId);
								$( form ).submit();
							});
							
							$( 'body' ).on('click','tr[dssPtDfn]', function(){
								var dfn = $(this).attr('dssPtDfn');
								var form = $('#dssPatientDemographics');
								$( form ).find('#dssPatientDfn').val(dfn);
								$( form ).submit();
							});
							
							$('#remotePatientSearch').on('shown.bs.modal', function(){
								var totalHeight = parseInt(top.fmain.top.fmain.innerHeight / 2);
								$('#remotePatientSearch .modal-body').css({
									'height' : totalHeight,
									'max-height' : totalHeight,
									'overflowY' : 'auto'
								});
							});
							
							$('#remotePatientSearch').on('hide.bs.modal', function(){
								$('#remotePatientSearch .modal-body').removeAttr('style');
							});
						});
						
						//Btn --
						top.btn_show("DEF");
						//Btn --
						$.ajax({
							url: '<?php echo $GLOBALS["web_root"]; ?>/interface/core/index.php?pg=clean-patient-session',
							success: function(resp){
								var tab = top.document.getElementById("curr_main_tab").value;
								//line below commented temporary.
								//top.refresh_control_panel(tab);
							}
						});
						
						$(document).ready(function(){
							setHeight();
						});
						
						top.show_loading_image("hide");
					</script>
			<?php
		}
		?>
	</body>
</html>