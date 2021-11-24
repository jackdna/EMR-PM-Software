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

if( isset($_REQUEST['do']) && trim($_REQUEST['do']) == '1' ) $ignoreAuth = false;

$callFrom = (isset($_REQUEST['from']) && trim($_REQUEST['from'])!='') ? trim($_REQUEST['from']) : 'core';
if(isset($callFrom) && $callFrom=='core'){$ignoreAuth=true;}

require_once(dirname(__FILE__).'/../../config/globals.php');
$task = isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
switch($task){
	case 'get_icon_bar_status':
		require_once(dirname(__FILE__).'/../../library/classes/class.app_base.php');
		$app_base	= new app_base();
		$returnArray = $app_base->get_iconbar_status();	
		echo json_encode($returnArray);
		break;
	case 'clean_patient_session':
		require_once(dirname(__FILE__).'/../../library/classes/class.app_base.php');
		require_once(dirname(__FILE__).'/../../library/classes/work_view/User.php');
		require_once(dirname(__FILE__).'/../../library/classes/work_view/RoleAs.php');
		$oRoleAs = new RoleAs();
		$oRoleAs->reset_user_role_ptonly();
		$app_base	= new app_base();
		$app_base->clean_patient_session();
		break;
	case 'show_patient_info':
		require_once(dirname(__FILE__).'/../../library/classes/class.app_base.php');
		$app_base	= new app_base();
		$returnArray = $app_base->show_patient_info();
		echo json_encode($returnArray);
		break;
	case 'save_imon_settings':
		require_once(dirname(__FILE__).'/../../library/classes/class.imedicmonitor.php');
		$objiMonitor = new imedicmonitor();
		$objiMonitor->save_imonitor_settings();
		break;
	case 'set_su_opened':
		$_SESSION['sess_user_switched'] = "no";
		patient_monitor_daily("SWITCH_USER_FORM");
		break;
	case 'set_pt_monitor_pt_close':
		patient_monitor_daily("PATIENT_CLOSE");
		break;
    //IM-6581:- Default location
    case 'get_defaultfac_options':
		require_once(dirname(__FILE__).'/../../library/classes/class.security.php');
		$objSecurity = new security('login');
        $fac_options=$objSecurity->get_user_default_fac_options($_POST);
        echo json_encode($fac_options);
		break;
	case 'processSwitchUser': 
		require_once(dirname(__FILE__).'/../../library/classes/class.security.php');
		$objSecurity = new security();
		$_SESSION["switch_user_tab"] = $_POST["switch_user_tab"];

		if(isset($GLOBALS["ADDON_SU_FIELD"]) && $GLOBALS["ADDON_SU_FIELD"] === true) {
			if(isset($_POST["suP"]) && !empty($_POST["suP"]) && isset($_POST["suU"]) && !empty($_POST["suU"])){ //POST used for security reasons only, please use $this->query_string instead
				if($objSecurity->app_login_process($_POST["suU"], $_POST["suP"], false) !== false){
					$_SESSION["sess_user_switched"] = "";
					die("OK");
				}else{
					$_SESSION["switch_user_tab"] = $_POST["switch_user_tab"];
					die("Incorrect Password.");
				}
			}
		} else {
			if(isset($_POST["suP"]) && !empty($_POST["suP"])){ //POST used for security reasons only, please use $this->query_string instead
				if($objSecurity->app_login_process("", $_POST["suP"], true) !== false){
					$_SESSION["sess_user_switched"] = "";
					die("OK");
				}else{
					$_SESSION["switch_user_tab"] = $_POST["switch_user_tab"];
					die("Incorrect Password.");
				}
			}
		}
		break;
    case 'change_loggedin_facility':
        require_once(dirname(__FILE__).'/../../library/classes/class.security.php');
		$objSecurity	= new Security();
        
		$loggedInFacility=xss_rem($_POST['loggedInFacility']);
		$curr_tab=xss_rem($_POST['curr_tab']);
        unset($_SESSION["login_facility"]);
        unset($_SESSION["login_facility_erx_id"]);
        unset($_SESSION["switch_user_tab"]);
        $_SESSION["login_facility"]			= $loggedInFacility;
        $_SESSION["login_facility_erx_id"]	= $objSecurity->get_emdeon_facility_obj_id($loggedInFacility);
        $_SESSION["switch_user_tab"]        = $curr_tab;
        echo $_SESSION["login_facility"];
		break;
    case 'check_restrict_access':
		require_once(dirname(__FILE__).'/../../library/classes/class.app_base.php');
		$app_base	= new app_base();
        //break glass privilege check
        $isBGPriv = $app_base->core_check_privilege("priv_break_glass");
        $bgPriv = ($isBGPriv == true) ? "y" : "n";
        $askForReason = $app_base->core_get_restricted_status($_POST["ptid"]);
        
        $rp_alert = ($askForReason == true) ? "y" : "n";
        $response['patId'] = $_POST["ptid"];
        $response['bgPriv'] = $bgPriv;
        $response['rp_alert'] = $rp_alert;
        
		echo json_encode($response);
		break;
	case 'process_change_password':
		require_once(dirname(__FILE__).'/../../library/classes/class.security.php');
		$objSecurity	= new Security();
		$response		= $objSecurity->process_change_pw($_POST);
		echo json_encode($response);
		break;
	case "set_res_fellow_sess":
		require_once(dirname(__FILE__).'/../../library/classes/class.app_base.php');
		$app_base	= new app_base();
		$app_base->set_res_fellow_sess();
		break;
	case 'pat_acc_status':
		require_once(dirname(__FILE__).'/../../library/classes/demographics.class.php');
		$dem = new Demographics();
		$pid = (isset($_SESSION['patient'])) ? $_SESSION['patient'] : 0;
		$pid = (int) $pid;
		$returnArray['data'] = $dem->get_pat_all_next_action_status($pid);
		echo json_encode($returnArray);
		break;
		
	case 'remote_patient_search':
		//Get Touch Works patients from their server
		
		require_once(dirname(__FILE__).'/../../library/classes/class.app_base.php');
		$app_base	= new app_base();
		
		$findBy = (isset($_REQUEST['findBy']) && empty($_REQUEST['findBy']) == false) ? $_REQUEST['findBy'] : '';
		$string = (isset($_REQUEST['searchStr']) && empty($_REQUEST['searchStr']) == false) ? $_REQUEST['searchStr'] : '';
		
		$remoteData = $app_base->get_touchworks_patients($findBy, $string);
		
		echo json_encode($remoteData);
	break;

	case 'dss_remote_patient_search':
		// Get DSS patients
		require_once(dirname(__FILE__) . "/../../library/dss_api/dss_demographics.php");
		try {
	        $objDss_demog = new Dss_demographics();
	        // $findBy = (isset($_REQUEST['findBy']) && empty($_REQUEST['findBy']) == false) ? $_REQUEST['findBy'] : '';
			$string = (isset($_REQUEST['searchStr']) && empty($_REQUEST['searchStr']) == false) ? $_REQUEST['searchStr'] : '';
			// $string = 'DATABRIDGE'; // Demo patient lname
			$pt = $objDss_demog->PT_SearchForVistApatient($string);
			array_shift($pt); // remove first element of array to fix the wrong array reponse at first position.

			$count = 1;
			$pData = array();
			foreach($pt as $key => $pat):
				$pData[$count]['recordCount'] = $count;
				$pData[$count]['patientDFN'] = $pat['patientDFN'];

				$patient_name = explode(',', $pat['patientName']);
				$patient_last_name = $patient_name[0];
				if ( preg_match('/\s/',$patient_name[1]) ) {
					$patient_fm_name = explode(' ', $patient_name[1]);
					$patient_first_name = !empty($patient_fm_name) ? $patient_fm_name[0] : '';
					// $patient_middle_name = !empty($patient_fm_name) ? $patient_fm_name[1] : '';
				} else {
					$patient_first_name = $patient_name[1];
					// $patient_middle_name = '';
				} 

				$pData[$count]['firstName'] = $patient_first_name;
				// $pData[$count]['middleName'] = $patient_middle_name;
				$pData[$count]['lastName'] = $patient_last_name;

				// Patient extra information.
				$ptd = $objDss_demog->PT_GetPatientInfo($pat['patientDFN']);
				// pre($ptd['patientSex']);
				foreach ($ptd as $key => $patt):
					$pData[$count]['patientSex'] = $patt['patientSex'];
					$pData[$count]['patientDob'] = $patt['patientDob'];
					$pData[$count]['patientSsn'] = $patt['patientSsn'];
				endforeach;
				$count++;
				if($count == 20) break;
			endforeach;

			$return = array('status'=>'success', 'data'=>$pData);
    	    echo json_encode($return);
		}
		catch(Exception $e){
			$return = array('status'=>'error','data'=>$e->getMessage());
			echo json_encode($return);
		}
	break;
	
	//Periodic Notification cases
	case 'periodic_check':
		$returnData = array();
		
		$params = (isset($_REQUEST['params']) && empty($_REQUEST['params']) == false) ? $_REQUEST['params'] : 'sessheight';
		
		//Task to be checked periodically
		$paramArr = explode(',', $params);
		
		//Session Height
		//if(in_array('sessheight',$paramArr)) $_SESSION["wn_height"] = $_REQUEST["height"] + 140;
		
		//Physician Notifications
		if(in_array('notifier',$paramArr)){
			//MsgConsole Main class for Physician Console
			require_once(dirname(__FILE__).'/../../library/classes/msgConsole.php');
			
			$msgConsoleObj = new msgConsole();
			$msgConsoleObj->callFrom = $callFrom;
			$arr_flag_status1 = $msgConsoleObj->get_active_flags('unread_messages');
			if(!isset($ptcommfirstcall_result)){
				//$arr_flag_status2 = $msgConsoleObj->get_active_flags('unread_scan_docs');
				$arr_flag_status3 = $msgConsoleObj->get_active_flags('un_consent_forms');
				$arr_flag_status4 = $msgConsoleObj->get_active_flags('un_sx_consent_forms');
				$arr_flag_status5 = $msgConsoleObj->get_active_flags('un_op_notes');
				$arr_flag_status6 = $msgConsoleObj->get_active_flags('un_consult_letters');
				$arr_flag_status7 = $msgConsoleObj->get_active_flags('phy_notes');
				$arr_flag_status = array_merge($arr_flag_status1, $arr_flag_status3, $arr_flag_status4, $arr_flag_status5, $arr_flag_status6, $arr_flag_status7);
			}else{
				$arr_flag_status = array_merge($arr_flag_status1);
			}
		
			$returnData['notifier']= $arr_flag_status;
		}
		
		if(in_array('shownotifications',$paramArr)){ 
			$msgConsoleObj = new msgConsole();
			$msgConsoleObj->callFrom = $callFrom;
			//$arr_flag_status = $msgConsoleObj->get_active_flags();
			echo 'shownotifications';
		}
		
		echo json_encode($returnData);
	break;
	case 'task_alert_shown':
			$_SESSION['task_alert_shown'] = true;
		break;
	case 'some_other_case':
		
		break;
	case 'check_doc_exists':
		require_once(dirname(__FILE__).'/../../library/classes/class.app_base.php');
		$app_base	= new app_base();
		$docExistClass=$app_base->check_docs_exists();
        echo $docExistClass;
		break;

	case 'apply_uga_finance':
		$patient = $_SESSION['patient'];
		if(!empty($patient)) {
			$p_sql = "SELECT `fname`,`lname`,`mname`,`DOB`,`phone_home`,`street`,`street2`,`postal_code`,`city`,`state`,`country_code`,`email`, `ss`, `uas_account_number` FROM patient_data WHERE id = ".$patient;
			$p_sql_query = imw_query($p_sql);
			$p_data = imw_fetch_assoc($p_sql_query);
			$uas_account_number = $p_data['uas_account_number'];

			include_once( $GLOBALS['srcdir'].'/uga_finance/uga.php' );
			$uga = new UGA();

			if(empty($p_data['uas_account_number']) || $p_data['uas_account_number'] == ''){
				$ssn = '';
				$pattern = '/^\d{3}-\d{2}-\d{4}$/';
				if (preg_match($pattern, $p_data['ss'])) {
				    $ssn = $p_data['ss'];
				} else {
					if(strlen($p_data['ss']) == 9 ) {
						$ssn = substr($p_data['ss'], 0,3).'-'.substr($p_data['ss'], 3,2).'-'.substr($p_data['ss'], 5,4);
					} else {
						echo json_encode(array('status'=>'failed','data'=>'SSN number is not valid as per UGA'));
						exit;
					}
				}

				$postArray = array(
					'retrieve_decision' => 'false',
					'redirect_uri' => 'https://google.com',
					'client_external_id' => $patient,
					'ssn' => $ssn,
					'first_name' => $p_data['fname'],
					'middle_initial' => $p_data['mname'],
					'last_name' => $p_data['lname'],
					'phone_primary_number' => $p_data['phone_home'],
					'phone_primary_type' => 'Home',
					'include_coapplicant' => 'false',
					'street' => $p_data['street'],
					'street2' => $p_data['street2'],
					'city' => $p_data['city'],
					'state' => $p_data['state'],
					'zip' => $p_data['postal_code'],
					'country' => $p_data['country_code'],
					'email' => $p_data['email'],
					'date_of_birth' => date('m/d/Y',strtotime($p_data['DOB'])),
				);

				try{
					$d = $uga->creditApplication($postArray);
					$uas_account_number = $d['data']['uas_account_number'];
					$data_arr['uas_account_number'] = $uas_account_number;
					UpdateRecords($patient,'id',$data_arr,'patient_data');
				} catch (Exception $e) {
					echo json_encode(array('status'=>'failed','data'=>$e->getMessage()));
					exit;
				}
			}
			// Autologin
			$redirect = $uga->redirectUrl($uas_account_number);
			if(!empty($redirect) && !empty($uas_account_number)) {
				echo json_encode(array('status'=>'success','data'=>$redirect));
			} else {
				echo json_encode(array('status'=>'failed','data'=>'Auto Login URL not generated for this patient.'));
				exit;
			}

		} else {
			echo json_encode(array('status'=>'failed','data'=>'Please create new patient or loaded any existing patient.'));
			exit;
		}
	break;

	case 'get_uga_status':
		$patient = $_SESSION['patient'];
		$html = '';
		if(!empty($patient)) {
			$p_sql = "SELECT `uas_account_number` FROM patient_data WHERE id = ".$patient;
			$p_sql_query = imw_query($p_sql);
			$p_data = imw_fetch_assoc($p_sql_query);
			$uas_account_number = $p_data['uas_account_number'];

			if(!empty($p_data['uas_account_number']) || $p_data['uas_account_number'] != '')
			{
				$contracts = '';
				try {
					include_once( $GLOBALS['srcdir'].'/uga_finance/uga.php' );
					$uga = new UGA();
					// Get Customer Info
					$d = $uga->getCustomer($uas_account_number);
					$redirect = $uga->redirectUrl($uas_account_number);
					//$contracts = $d['data']['contracts'];
					$htmldata = $d['data']['html'];
				} catch(Exception $e) {
					echo json_encode(array('html'=>$e->getMessage(),'btn'=>''));
					exit;
				}

				if(isset($htmldata) && !empty($htmldata) && $htmldata != '') {
					$html = '<div class="uga_app_status">'.$htmldata.'</div>';
					$html .= '<style>.uga_app_status table{width:100%;}.uga_app_status table tr td{padding:5px 10px;}.uga_app_status table tr{border-bottom:1px solid #ccc;}.uga_app_status table tr:last-child{border-bottom:none;}</style>';
					$btn = '<button type="button" class="btn btn-success btn-sm" onclick="top.view_in_uportal360(\''.$redirect.'\')">Review in uPortal360</button>';

					echo json_encode(array('html'=>$html,'btn'=>$btn));
				} else {
					echo json_encode(array('html'=>'<p>No Record Found.</p>','btn'=>'<input type="button" class="btn btn-success btn-info" align="bottom" name="uga_register" id="uga_register" onclick="top.apply_uga_finance(\'demographics\');" value="Start Application">'));
				}

				// if(!empty($contracts) && sizeof($contracts) > 0){
				// 	$html .= '<table class="table"><tbody>';
				// 	foreach ($contracts as $key => $cont) {
				// 		$note_date = !empty($cont['note_date']) ? date('m/d/Y',strtotime($cont['note_date'])) : '';
				// 		$first_payment_date = !empty($cont['first_payment_date']) ? date('m/d/Y',strtotime($cont['first_payment_date'])) : '';
				// 		$active_date = !empty($cont['active_date']) ? date('m/d/Y',strtotime($cont['active_date'])) : '';
				// 		$next_payment_date = !empty($cont['next_payment_date']) ? date('m/d/Y',strtotime($cont['next_payment_date'])) : '';

				// 		$html .= '<tr>
				// 				<th>Contract Number</th><td>'.$cont['contract_number'].'</td>
				// 				<th>Status</th><td>'.$cont['status'].'</td>
				// 				<th>Sub Status</th><td>'.$cont['substatus'].'</td>
				// 				<th>Code</th><td>'.$cont['code'].'</td>
				// 			</tr><tr>
				// 				<th>Note Date</th><td>'.$note_date.'</td>
				// 				<th>Autopay Type</th><td>'.$cont['autopay_type'].'</td>
				// 				<th>Payment</th><td>'.$cont['payment'].'</td>
				// 				<th>Payment Override</th><td>'.$cont['payment_override'].'</td>
				// 			</tr><tr>
				// 				<th>Principal Balance</th><td>'.$cont['principal_balance'].'</td>
				// 				<th>Interest Balance</th><td>'.$cont['interest_bal'].'</td>
				// 				<th>Fees Balance</th><td>'.$cont['fees_balance'].'</td>
				// 				<th>Payoff Amount</th><td>'.$cont['payoff_amount'].'</td>
				// 			</tr><tr>
				// 				<th>Amount Financed</th><td>'.$cont['amount_financed'].'</td>
				// 				<th>Credit Available</th><td>'.$cont['credit_available'].'</td>
				// 				<th>Term</th><td>'.$cont['term'].'</td>
				// 				<th>APR</th><td>'.$cont['apr'].'</td>
				// 			</tr><tr>
				// 				<th>First Payment Date</th><td>'.$first_payment_date.'</td>
				// 				<th>Active Date</th><td>'.$active_date.'</td>
				// 				<th>Credit Line</th><td>'.$cont['credit_line'].'</td>
				// 				<th>Past Due Amount</th><td>'.$cont['past_due_amount'].'</td>
				// 			</tr><tr>
				// 				<th>Minimum Payment Due</th><td>'.$cont['minimum_payment_due'].'</td>
				// 				<th>Delinquent Days</th><td>'.$cont['delinquent_days'].'</td>
				// 				<th>Next Payment</th><td>'.$next_payment_date.'</td>
				// 				<th></th><td></td>
				// 			</tr>';
				// 	}
				// 	$html .= '</tbody></table>';

				// 	$btn = '<button type="button" class="btn btn-success btn-sm" onclick="top.view_in_uportal360(\''.$redirect.'\')">Review in uPortal360</button>';

				// 	echo json_encode(array('html'=>$html,'btn'=>$btn));
				// } else {
				// 	echo json_encode(array('html'=>'<p>No Record Found.</p>','btn'=>'<input type="button" class="btn btn-success btn-info" align="bottom" name="uga_register" id="uga_register" onclick="top.apply_uga_finance(\'demographics\');" value="Start Application">'));
				// }
			} else {
				echo json_encode(array('html'=>'<p>Patient is not registed on UGA. Please start an application to register in UGA.</p>','btn'=>'<input type="button" class="btn btn-success btn-info" align="bottom" name="uga_register" id="uga_register" onclick="top.apply_uga_finance(\'demographics\');" value="Start Application">'));
			}
		} else {
			echo json_encode(array('html'=>'<p>Please create new patient or load any existing patient.</p>','btn'=>''));
		}
	break;

	case 'dssValidateElectronicSignature':
		$elecSign = (isset($_REQUEST['electronicSignature']) && empty($_REQUEST['electronicSignature']) == false) ? $_REQUEST['electronicSignature'] : '';
		$reqFrom = (isset($_REQUEST['reqFrom']) && empty($_REQUEST['reqFrom']) == false) ? $_REQUEST['reqFrom'] : '';
		
		if(empty($elecSign) == false){
			// Validate ESignature on DSS
			include_once( $GLOBALS['srcdir'].'/dss_api/dss_core.php' );
			$objDss = new Dss_core();

			$sqlQuery = "SELECT sso_identifier FROM users WHERE id = ".$_SESSION['authId'];
			$sqlQueryRes = imw_query($sqlQuery);
			$row = imw_fetch_assoc($sqlQueryRes);
			$duz = $row['sso_identifier'];
			if(!empty($duz)) {
				$validated = $objDss->validateESignature($duz, $elecSign);
				if( $validated == 1) {
					$code = base64_encode($elecSign);
					if($reqFrom == 'saveChartNote'){
						$_SESSION['vcode'] = $code;
					}

					echo json_encode(array('status'=>'success','msg'=>'Signature validated','vcode'=>$code));
				} else {
					echo json_encode(array('status'=>'failed','msg'=>'Invalid Electronic Signature'));
				}
			} else {
				echo json_encode(array('status'=>'failed','msg'=>'User DUZ not found'));
			}
		}
	break;

	case 'dssLoadTiuTitles':
		include_once( $GLOBALS['srcdir'].'/dss_api/dss_enc_visit_notes.php' );
		$obj = new Dss_enc_visit_notes();
	
		$postArray = array(
		  "noteClass" => "3",
		  "searchString" => "PROGRESS NOTE",
		  "searchDirection" => "1"
		);
		$tiuData = $obj->tiu_TitleSearch($postArray);

		$patient = $_SESSION['patient'];
		$form_id = '';
		if(isset($_SESSION['form_id']) && $_SESSION['form_id'] != ''){
			$form_id = $_SESSION['form_id'];
		} elseif(isset($_SESSION['finalize_id']) && $_SESSION['finalize_id'] != ''){
			$form_id = $_SESSION['finalize_id'];
		}

		$sql = "SELECT tiu_ifn FROM chart_master_table WHERE patient_id = '".$patient."' AND id = '".$form_id."'";
		$currentTiu = imw_fetch_assoc(imw_query($sql));

		$options = '<option></option>';
		foreach ($tiuData as $key => $tiu) {
			if($tiu['ifn'] == $currentTiu['tiu_ifn']) {
				$options .= '<option selected="selected" value="'.$tiu['ifn'].'">'.htmlentities($tiu['name']).'</option>';
			} else {
				$options .= '<option value="'.$tiu['ifn'].'">'.htmlentities($tiu['name']).'</option>';
			}
		}
		echo $options;
	break;

	case 'dssLoadServiceConnectedOpt':
		$patientDFN = '';
		$patient_id = $_SESSION['patient'];
		$sqlDFN = "SELECT External_MRN_5 FROM `patient_data` WHERE `id` = ".$patient_id;
		$resultDFN = imw_query($sqlDFN);
		if( imw_num_rows($resultDFN) > 0 ) {
			$data = imw_fetch_assoc($resultDFN);
			$patientDFN = $data['External_MRN_5'];
		}

		if( empty($patientDFN) || $patientDFN == '' ){
			echo 'Patient not allowed to be empty.';
			exit;
		}
		
		$service_connected_opts = array(
			'SC' => 'Service Connected Condition',
			'CV' => 'Combat Vet (Combat Related)',
			'AO' => 'Agent Orange Exposure',
			'IR' => 'Ionizing Radiation Exposure',
			'SWAC' => 'Southwest Asia Conditions',
			'SHD' => 'Shipboard Hazard and Defence',
			'MST' => 'MST',
			'HNC' => 'Head and/or Neck Cancer',
			'EC' => 'Description Not Available (DSS ?)'
		);

		$html = '<div class="row">
					<div class="col-md-6">
						<p>Service Connected & Rated Disabilities</p>
						<textarea class="form-control" name="dss_sc_status" id="dss_sc_status" readonly="true" style="height:100px!important;">{STATUS}</textarea>
					</div>
					<div class="col-md-6">
						<p>Related To</p>
						<form method="post" name="dss_sc_form" id="dss_sc_form">
							<ul class="list-unstyled">{OPTIONS}</ul>
						</form>
					</div>
				</div>';

		// Get patient service eligibility status from db, then update into the modal
		if($_REQUEST['reqFrom'] !== 'header_master'){
			$sc_status_query = imw_query("SELECT `service_eligibility`, `service_eligibility_status` FROM `patient_data` WHERE id = ".$patient_id);
			$sc_status_result = imw_fetch_assoc($sc_status_query);
			$status = $sc_status_result['service_eligibility_status'];
			$service_eligibility = unserialize($sc_status_result['service_eligibility']);
			if(!empty($status) || $status != '') 
				$html = str_ireplace('{STATUS}', $status, $html);
		}

		switch ($_REQUEST['reqFrom']) {
			case 'header_master':
				try {
					include_once( $GLOBALS['srcdir'].'/dss_api/dss_medical_hx.php' );
					$obj = new Dss_medical_hx();

					$locationIen = isset($_SESSION['dss_location']) ? $_SESSION['dss_location'] : '';

					$args = array(
						'patientDFN' => $patientDFN,
						'apptDateTime' => $obj->convertToFileman(date('Y-m-d')),
						'locationIen' => $locationIen
					);
					$opts = $obj->getServiceConnectedPrompt($args);
					// pre($opts);

					$allowed = array();
					foreach ($opts as $key => $o) {
					    foreach ($o as $key => $val) {
					        if($key == 'type') $kVal = $val;
					        if($key == 'allow') $aVal = $val;
					        $allowed[$kVal] = $aVal;
					    }
					}
					// pre($allowed);

					$data = '';
					$patient_sc_allowd_types = array();
					foreach ($service_connected_opts as $key => $op) {
					    if(array_key_exists($key, $allowed)) {
					        if($allowed[$key] == 1) {
					        	$patient_sc_allowd_types[$key] = 1;
					            $data .= '<li><div class="checkbox">
										<input checked="checked" type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="1" onchange="top.fmain.dss_value_change(this);">&nbsp;'.$key.' - '.$op.'
										<label style="float: left;" for="dss_'.$key.'"></label>
									</div></li>';
					        } else {
					            $data .= '<li><div class="checkbox">
										<input disabled="disabled" type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="0" onchange="top.fmain.dss_value_change(this);">&nbsp;'.$key.' - '.$op.'
										<label style="float: left;" for="dss_'.$key.'"></label>
									</div></li>';
					        }
					    } else {
					    	$data .= '<li><div class="checkbox">
										<input disabled="disabled" type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="0" onchange="top.fmain.dss_value_change(this);">&nbsp;'.$key.' - '.$op.'
										<label style="float: left;" for="dss_'.$key.'"></label>
									</div></li>';
					    }
					}
					// pre($data,1);

					// Get Service Connected patient status
					$status = '';
					$opts = $obj->getServiceConnectedStatus($patientDFN);
					if(!empty($opts) && sizeof($opts) > 0) {
						foreach ($opts as $key => $opt){
							foreach($opt as $option){
								$status .= trim($option)."\n";
							}
						}
					} else {
						$status = 'No Status Available';
					}

					// Update SC allowed type into the patient data
					$service_eligibility = '\''.imw_real_escape_string(serialize($patient_sc_allowd_types)).'\'';
					$sc_sql = 'UPDATE `patient_data` SET `service_eligibility` = '.$service_eligibility.', `service_eligibility_status` = \''.imw_real_escape_string($status).'\' WHERE  id = '.$patient_id;
					imw_query($sc_sql);

				 	$html = str_ireplace('{OPTIONS}', $data, $html);
				 	$html = str_ireplace('{STATUS}', $status, $html);
				 	echo $html;

				} catch(Exception $e) {
					echo $e->getMessage();
					exit;
				}
			break;
			// case 'medication':
			// 	$medId = $_REQUEST['medId'];

			// 	$data = '<input type="hidden" name="req_from" value="'.$_REQUEST['reqFrom'].'"><input type="hidden" name="med_id" value="'.$medId.'">';
			// 	// $scData = '';
			// 	// $checked = '';
			// 	// $checkVal = '';
			// 	// $sql_query = imw_query("SELECT `service_eligibility` FROM lists WHERE id = ".$medId."");
			// 	// if(imw_num_rows($sql_query) > 0) {
			// 	// 	$result = imw_fetch_assoc($sql_query);
			// 	// 	$data = $result['service_eligibility'];
			// 	// 	$scData = !empty($data) ? unserialize($data) : '';
			// 	// }
			// 	foreach ($service_connected_opts as $key => $field) {
			// 		if(array_key_exists($key, $service_eligibility) && $service_eligibility[$key] == 1) {
			// 			$data .= '<li>
			// 					<div class="checkbox">
			// 						<input type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="0" onchange="top.fmain.dss_value_change(this);">&nbsp;'.$key.' - '.$field.'
			// 						<label style="float: left;" for="dss_'.$key.'"></label>
			// 					</div>
			// 				</li>';
			// 		} else {
			// 			$data .= '<li>
			// 					<div class="checkbox">
			// 						<input disabled="disabled" type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="0" onchange="top.fmain.dss_value_change(this);">&nbsp;'.$key.' - '.$field.'
			// 						<label style="float: left;" for="dss_'.$key.'"></label>
			// 					</div>
			// 				</li>';
			// 		}
			// 	 	// if(!empty($scData) && $scData != '') {
			// 	 	// 	if(array_key_exists($key, $scData)) {

			// 	 	// 		$data .= '<li>
			// 			// 		<div class="checkbox">
			// 			// 			<input type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="1" onchange="top.fmain.dss_value_change(this);" checked="checked">&nbsp;'.$key.' - '.$field.'
			// 			// 			<label style="float: left;" for="dss_'.$key.'"></label>
			// 			// 		</div>
			// 			// 	</li>';

			// 	 	// 	} else {

			// 	 	// 		$data .= '<li>
			// 			// 		<div class="checkbox">
			// 			// 			<input type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="0" onchange="top.fmain.dss_value_change(this);">&nbsp;'.$key.' - '.$field.'
			// 			// 			<label style="float: left;" for="dss_'.$key.'"></label>
			// 			// 		</div>
			// 			// 	</li>';

			// 	 	// 	}
			// 	 	// } else {
			// 	 	// 	$data .= '<li>
			// 			// 		<div class="checkbox">
			// 			// 			<input type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="0" onchange="top.fmain.dss_value_change(this);">&nbsp;'.$key.' - '.$field.'
			// 			// 			<label style="float: left;" for="dss_'.$key.'"></label>
			// 			// 		</div>
			// 			// 	</li>';
			// 	 	// }
			//  	}
			// 	$html = str_ireplace('{OPTIONS}', $data, $html);
			//  	echo $html;		
			// break;

			case 'work_view':
				$formId = $_REQUEST['medId'];
				$pt_sc = '';
				if($formId != '') {
					$sql = imw_query("SELECT `service_eligibility` FROM `chart_master_table` WHERE `id` = ".$formId);
					if(imw_num_rows($sql) > 0) {
						$result = imw_fetch_assoc($sql);
						if($result['service_eligibility'] != '')
							$pt_sc = unserialize($result['service_eligibility']);
					}
				}

				$data = '<input type="hidden" name="req_from" value="'.$_REQUEST['reqFrom'].'">';
				// $data .= '<input type="hidden" name="formId" value="'.$formId.'">';

				foreach ($service_connected_opts as $key => $field) {
					if(array_key_exists($key, $service_eligibility) && $service_eligibility[$key] == 1) {
						if(!empty($pt_sc) && sizeof($pt_sc) > 0) {
							if(array_key_exists($key, $pt_sc) && $pt_sc[$key] == 1){
						 		$data .= '<li><input type="hidden" name="dss['.$key.']" value="0"><div class="checkbox">
										<input checked="checked" type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="1" onchange="top.dss_value_change(this);">&nbsp;'.$key.' - '.$field.'
										<label style="float: left;" for="dss_'.$key.'"></label>
									</div></li>';
							} else {
						 		$data .= '<li><input type="hidden" name="dss['.$key.']" value="0"><div class="checkbox">
						 				<input type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="0" onchange="top.dss_value_change(this);">&nbsp;'.$key.' - '.$field.'
										<label style="float: left;" for="dss_'.$key.'"></label>
									</div></li>';
							}
						} else {
					 		$data .= '<li><input type="hidden" name="dss['.$key.']" value="0"><div class="checkbox">
									<input type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="0" onchange="top.dss_value_change(this);">&nbsp;'.$key.' - '.$field.'
									<label style="float: left;" for="dss_'.$key.'"></label>
								</div></li>';
						}
					} else {
				 		$data .= '<li><input type="hidden" name="dss['.$key.']" value="0"><div class="checkbox">
								<input disabled="disabled" type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="0" onchange="top.dss_value_change(this);">&nbsp;'.$key.' - '.$field.'
								<label style="float: left;" for="dss_'.$key.'"></label>
							</div></li>';
					}
			 	}
			 	$html = str_ireplace('{OPTIONS}', $data, $html);
			 	echo $html;
		
			break;

			case 'problem_list':

				$pt_problem_id = $_REQUEST['medId'];
				$pt_sc = '';
				if($pt_problem_id != '') {
					$sql = imw_query("SELECT `service_eligibility` FROM `pt_problem_list` WHERE id = ".$pt_problem_id);
					if(imw_num_rows($sql) > 0) {
						$result = imw_fetch_assoc($sql);
						if($result['service_eligibility'] != '')
							$pt_sc = unserialize($result['service_eligibility']);
					}
				}

				$data = '';
				foreach ($service_connected_opts as $key => $field) {
					if(array_key_exists($key, $service_eligibility) && $service_eligibility[$key] == 1) {
						if(!empty($pt_sc) && sizeof($pt_sc) > 0) {
							if(array_key_exists($key, $pt_sc) && $pt_sc[$key] == 1){
						 		$data .= '<li><input type="hidden" name="dss['.$key.']" value="0"><div class="checkbox">
										<input checked="checked" type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="1" onchange="top.dss_value_change(this);">&nbsp;'.$key.' - '.$field.'
										<label style="float: left;" for="dss_'.$key.'"></label>
									</div></li>';
							} else {
						 		$data .= '<li><input type="hidden" name="dss['.$key.']" value="0"><div class="checkbox">
										<input type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="0" onchange="top.dss_value_change(this);">&nbsp;'.$key.' - '.$field.'
										<label style="float: left;" for="dss_'.$key.'"></label>
									</div></li>';
							}
						} else {
					 		$data .= '<li><input type="hidden" name="dss['.$key.']" value="0"><div class="checkbox">
									<input type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="0" onchange="top.dss_value_change(this);">&nbsp;'.$key.' - '.$field.'
									<label style="float: left;" for="dss_'.$key.'"></label>
								</div></li>';
						}
					} else {
				 		$data .= '<li><input type="hidden" name="dss['.$key.']" value="0"><div class="checkbox">
								<input disabled="disabled" type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="0" onchange="top.dss_value_change(this);">&nbsp;'.$key.' - '.$field.'
								<label style="float: left;" for="dss_'.$key.'"></label>
							</div></li>';

					}
			 	}
			 	$html = str_ireplace('{OPTIONS}', $data, $html);
			 	echo $html;

			break;
			default:
				$data = '';
				foreach ($service_connected_opts as $key => $field) {
					if(array_key_exists($key, $service_eligibility) && $service_eligibility[$key] == 1) {
				 		$data .= '<li><div class="checkbox">
								<input type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="0" onchange="top.dss_value_change(this);">&nbsp;'.$key.' - '.$field.'
								<label style="float: left;" for="dss_'.$key.'"></label>
							</div></li>';
					} else {
				 		$data .= '<li><div class="checkbox">
								<input disabled="disabled" type="checkbox" id="dss_'.$key.'" name="dss['.$key.']" value="0" onchange="top.dss_value_change(this);">&nbsp;'.$key.' - '.$field.'
								<label style="float: left;" for="dss_'.$key.'"></label>
							</div></li>';

					}
			 	}
			 	$html = str_ireplace('{OPTIONS}', $data, $html);
			 	echo $html;
			break;
		}
	break;

	case 'dssUpdateServiceConnected':
		parse_str($_REQUEST['formdata'], $formdata);
		switch ($formdata['req_from']) {
			
			// case 'medication':
			// 	$med_id = $formdata['med_id'];
			// 	$dss_sc = serialize($formdata['dss']);
			// 	$sql = "UPDATE lists SET `service_eligibility` = '".$dss_sc."' WHERE id = ".$med_id;
			// 	$exec = imw_query($sql);
			// 	if($exec) {
			// 		echo 'Record successfully updated';
			// 	} else {
			// 		echo 'Updation failed. Please contact to the developer.';
			// 	}
			// break;

			case 'work_view':
				$formId = $formdata['formId'];
				$dss_sc = serialize($formdata['dss']);
				$sql = "UPDATE `chart_master_table` SET `service_eligibility` = '".$dss_sc."' WHERE id = ".$formId;
				$exec = imw_query($sql);
				if($exec) {
					echo 'Record successfully updated';
				} else {
					echo 'Updation failed. Please contact to the developer.';
				}
			break;
		}
	break;
	
	case 'rx_notification_consent' :
		$rx_notification_consent = $_REQUEST['rx_notification_consent'];
		$patient_id = $_SESSION['patient'];
		$operator_id = $_SESSION['authId'];
		$rx_date=date('Y-m-d H:i:s');
		$sqlrx = "SELECT * FROM `patient_rx_notification_consent` WHERE `patient_id` = ".$patient_id;
		$resultrx = imw_query($sqlrx);
		if( imw_num_rows($resultrx) == 1 ) {
			$row=imw_fetch_assoc($resultrx);
			$sql = "UPDATE `patient_rx_notification_consent` SET rx_notification_consent='".$rx_notification_consent."', `patient_id`='".$patient_id."', `updated_on`='".$rx_date."', `updated_by`='".$operator_id."'  WHERE id = '".$row['id']."' ";
		} else {
			$sql = "INSERT INTO `patient_rx_notification_consent` SET rx_notification_consent='".$rx_notification_consent."', `patient_id`='".$patient_id."', `operator_id`='".$operator_id."', `added_on`='".$rx_date."' ";
		}
		$exec = imw_query($sql);
		if($exec) {
			echo 'Rx Notification consent updated successfully.';
		} else {
			echo 'Rx Notification consent Updation failed.';
		}
	break;

	default:
		die('No task defined');
}


?>