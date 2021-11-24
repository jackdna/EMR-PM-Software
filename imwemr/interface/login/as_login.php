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

$ignoreAuth = true;
require_once("../../config/globals.php");

/*Clear TW Related values*/
$_SESSION['as_user_id'] = '';
/*$_SESSION['as_user_pass'] = '';*/
$_SESSION['as_user_entry_code'] = '';
$_SESSION['tw_encounter_id'] = '';

$token = false;

/*Allow Request only from Allowed Touch Works IP*/
/*if( is_allscripts('enabled') && isset($GLOBALS["TW_IP"]) && is_array($GLOBALS["TW_IP"]) && in_array($_SERVER['REMOTE_ADDR'], $GLOBALS["TW_IP"]) )
{*/
	$token = isset( $_REQUEST['SSOToken'] ) ? trim( $_REQUEST['SSOToken'] ) : false;	/*Get SSO token*/
/*}*/

$_POST = array();
$tokenLogId = false;

try
{
	$GLOBALS['rethrow'] = true;
	if( $token && $token !== '' )
	{
		/*Add Entry In Token Log*/
		$sqlLog = "INSERT INTO `as_token_log` SET `tokenId`='".imw_real_escape_string($token)."', `entry_date_time`='".date('Y-m-d- H:i:s')."'";
		imw_query($sqlLog);
		$tokenLogId = imw_insert_id();
		/*End Add Entry In Token Log*/
		
		/*Validate Token and Get Provider's username and Patient Id*/
		include_once( dirname(__FILE__).'/../../library/allscripts/as_dataValues.php' );
		$dataValsObj = new as_dataValues(false);
		
		$validationResponse = $dataValsObj->validateToken( $token );
		
		$dataValsObj->triggerDailyCalls();
		
		/*Log Token Response*/
		$sqlLOgUpdate = "UPDATE `as_token_log` SET `response`='".json_encode($validationResponse)."', `update_date_time`='".date('Y-m-d- H:i:s')."' WHERE `id`=".$tokenLogId;
		imw_query($sqlLOgUpdate);
		/*End Log Token Response*/
		
		$loginiDoc = false;
		
		$twUserId = trim($validationResponse->SSOUserName);
		$twEncounterId = trim($validationResponse->EncounterID);

		/*Get TW entryCode and numeric ID for the User Namer supplied*/
		$dataValsObj->setEhrUserID($twUserId);
		$twUserData =  $dataValsObj->getUserID();
		$twUserData->ID = (int)trim($twUserData->ID);

		if( !isset($twUserData->ID) || $twUserData->ID <= 0 )
			throw new asException( 'Data Error', 'Unable to retrieve TW numeric User ID.' );

		/*Get User's Logged IN Facility Name*/
		$twUserSite = $dataValsObj->getUserSiteInfo($twUserData->ID);

		if( !isset($twUserSite->SiteName) || empty($twUserSite->SiteName) )
			throw new asException( 'Data Error', 'Unable to TW Site Name for the logged in User.' );

		$twUserSite->SiteName = strtolower(trim($twUserSite->SiteName));
		$twUserSite->SiteName = imw_real_escape_string($twUserSite->SiteName);

		/*Get iDoc Facility ID for the matched Facility*/
		$idocFacility = false;
		$sql = "SELECT `id` FROM `facility` WHERE LOWER(`name`) = '".$twUserSite->SiteName."'";
		$resp = imw_query($sql);
		if( $resp && imw_num_rows($resp) > 0 )
		{
			$idocFacility = imw_fetch_assoc($resp);
			$idocFacility = (int)$idocFacility['id'];
		}

		if( $idocFacility === false )
			throw new asException( 'Data Error', 'Facility does not exists in iDoc.' );

		/*Login in imwemr by using SSOUsername and User's Logged in facility matching*/
		if( $twUserId !== '' )
		{
			/*Get Provider's iDoc Credentials*/
			/*, as_password*/
			$sqlCreds = "SELECT `id`, `password`, `username`, `as_username`, `as_entry_code` FROM `users` WHERE `as_username`='".$twUserId."'";
			$respCreds = imw_query( $sqlCreds );
			
			$iDocAuthId = 0;
			if( $respCreds && imw_num_rows($respCreds) > 0 )
			{
				$respCreds = imw_fetch_assoc($respCreds);
				
				$_POST['u_n'] = $respCreds['username'];
				$_POST['p_w'] = $respCreds['password'];
				$_POST['l_facility'] = $idocFacility;
				$_POST['as_login'] = true;
				$iDocAuthId = $respCreds['id'];
				
				/*Set The data in Session*/
				$_SESSION['as_user_id'] = $respCreds['as_username'];
				/*$_SESSION['as_user_pass'] = $respCreds['as_password'];*/
				$_SESSION['as_user_entry_code'] = $respCreds['as_entry_code'];
				$_SESSION['tw_encounter_id'] = $twEncounterId;
				/*Set The data in Session*/
				
				$loginiDoc = true;
			}
			else
				throw new asException( 'Data Error', 'Provider with supplied username, does not exists in iDoc.' );
		}
		
		/*Load/Save Patient in iDoc in Touch works Patient Id provided in API call response*/
		$twPatientId = trim($validationResponse->PatientID);
		if( $twPatientId != '' )
		{
			/*Check If patient Already exists in iDoc - Load data if yes*/
			/* $sqlPatientCheck = "SELECT `id`, `External_MRN_4`, `as_id` FROM `patient_data` WHERE `as_id`='".$twPatientId."'";
			$respPatientCheck = imw_query( $sqlPatientCheck );
			
			if( $respPatientCheck && imw_num_rows($respPatientCheck) > 0 )
			{
				$idocPtId = imw_fetch_assoc($respPatientCheck);
				$_POST['idoc_pt_id'] = $idocPtId['id'];
				
				$_POST['External_MRN_4'] = $idocPtId['External_MRN_4'];
				$_POST['as_id'] = $idocPtId['as_id'];
			} */
			
			/* Add New Patient to idocFacility
			 * Updarte if it already exists
			 */
			// else
			// {
			include_once( $GLOBALS['srcdir'].'/allscripts/as_patient.php' );

			/*Fetch Patient Details and Save in DB*/
			$patientObj = new as_patient();
			$patientData = $patientObj->patient( $twPatientId );

			if( !is_null( $patientData ) )
			{
				$patientData = get_object_vars( $patientData );
				$patientData = (object) array_map('trim', $patientData);

				$dob = strtotime($patientData->dateofbirth);
				$dob = date( 'Y-m-d', $dob );

				$asPhyUserName = trim($patientData->PhysUserName);
	
				$pcpId = 0;
				$pcpName = '';

				try
				{
					/*Get PCP ID - Create if does not exists*/
					if( $asPhyUserName != '' )
					{
						//require_once( $GLOBALS['incdir'].'/common/CLSCommonFunction.php' );
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
								$pcpMNameQry = " and MiddleName = '".imw_real_escape_string(trim($asPhyData->MiddleName))."' ";
							}

							$sqlPhyCheck = "SELECT physician_Reffer_id FROM refferphysician WHERE LastName = '".imw_real_escape_string(trim($asPhyData->LastName))."' AND FirstName = '".imw_real_escape_string(trim($asPhyData->FirstName))."'".$pcpMNameQry."AND (delete_status = 0 OR delete_status = 2 ) ORDER BY delete_status ASC LIMIT 1";
							$respPhyCheck = imw_query( $sqlPhyCheck );

							if( $respPhyCheck && imw_num_rows($respPhyCheck) > 0 )
							{
								$respPhyCheck = imw_fetch_assoc($respPhyCheck);

								$pcpId = $respPhyCheck['physician_Reffer_id'];
								$pcpName = trim( $OBJCommonFunction->getRefPhyName($pcpId) );

								/*Update external ID to ReferringPhysicians DB*/
								$sqlPhyUpd = "UPDATE `refferphysician` SET
												`external_id`='".imw_real_escape_string($asPhyUserName)."'
												WHERE
												`physician_Reffer_id`=".$pcpId;
								imw_query($sqlPhyUpd);
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
												`external_id`='".imw_real_escape_string($asPhyUserName)."'";

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
				}
				catch(Exception $e)
				{
					if( array_key_exists('as_error_msg', $_SESSION) )
						$_SESSION['as_error_msg'] .= ' '.$e->getMessage();
					else
						$_SESSION['as_error_msg'] = $e->getMessage();
					
					/*Log Error Message*/
					if( $tokenLogId )
					{
						$sqlLOgUpdate = "UPDATE `as_token_log` SET `error`=TRIM(CONCAT(error, ' ', '".$_SESSION['as_error_msg']."')), `update_date_time`='".date('Y-m-d- H:i:s')."' WHERE `id`=".$tokenLogId;
						imw_query($sqlLOgUpdate);
					}
					/*End Log Error Message*/
				}
				
				
				$sqlPatient = 'INSERT INTO ';
				$otherFields = ', `created_by`=\''.imw_real_escape_string($iDocAuthId).'\'';
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
				$_POST['idoc_pt_id'] = $pid;
				$_POST['External_MRN_4'] = trim($patientData->mrn);
				$_POST['as_id'] = trim($patientData->ID);
			}
			//}
			
			/**
			 * Sync Medical Hx data for the patient
			 */
			ob_start();
			include_once( $GLOBALS['srcdir'].'/allscripts/sync_medical_hx.php' );
			$sync = new as_medical_hx();
			$sync->sync_medical_hx();
			ob_end_clean();
		}
		
		if( $loginiDoc )
		{
			include_once( dirname(__FILE__).'/../login/index.php' );
		}
	}
}
catch(Exception $e)
{
	if( array_key_exists('as_error_msg', $_SESSION) )
		$_SESSION['as_error_msg'] .= $e->getMessage();
	else
		$_SESSION['as_error_msg'] = $e->getMessage();
	
	/*Log Error Message*/
	if( $tokenLogId )
	{
		$sqlLOgUpdate = "UPDATE `as_token_log` SET `error`=TRIM(CONCAT(error, ' ', ".$_SESSION['as_error_msg']."')), `update_date_time`='".date('Y-m-d- H:i:s')."' WHERE `id`=".$tokenLogId;
		imw_query($sqlLOgUpdate);
	}
	/*End Log Error Message*/
	
	if( count($_POST) == 0 )
		echo '<script>window.location=\''.$GLOBALS['php_server'].'/interface/login/index.php\';</script>';
	else
		include_once( dirname(__FILE__).'/../login/index.php' );
}

?>