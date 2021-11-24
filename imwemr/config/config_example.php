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

 
 Purpose: practice specific settings controller page example.
 Access Type: Indirect Access.
*/
//Error reporting settings
error_reporting(1);
ini_set("display_errors",1);
date_default_timezone_set('America/New_York');//date_default_timezone_set('America/Chicago');  //US CST

define("HASH_METHOD","SHA1"); //it can be SHA1 or MD5
define("PASS_EXPIRY_NOTICE_DAYS",15);
$GLOBALS['phone_format'] = "###-###-####" ; //(###) ###-####

//---- IMWEMR main tabs Array ---------
$button_arr = array();
$button_arr[] = 'Work View';
$button_arr[] = 'Tests';
$button_arr[] = 'Patient Info';
$button_arr[] = 'Medical Hx';
$button_arr[] = 'Accounting';
$button_arr[] = 'Billing';
$button_arr[] = 'Optical';
$button_arr[] = 'Reports';
$button_arr[] = 'Admin';

// LOCAL SERVER NAME
$GLOBALS["LOCAL_SERVER"] = 'Boston';

ini_set("session.bug_compat_warn","off");//turn off PHP compatibility warnings
define("REMOTE_SYNC",0);//Possible values 0 [off] or 1 [on].
define("DEFAULT_PRODUCT",'imwemr'); //imwemr //imwemr
//To make availability of IOlink tab in admin module
define("OPTICAL_DISPLAY_PROVIDER","YES");
$enableIolink = true;

//Chart notes global settings
//include_once($GLOBALS['incdir']."/chart_globals.php");
//Valid UserType of chart Notes Physicians and Technicians --

//Chart Notes Lock
define("V_CHART_LOCK", "1");
//Valid UserType of chart Notes Physicians and Technicians --
define("APP_DEBUG_MODE" , 1); //1 - on, 0 - off" 

//PAYMENT METHODS
$arr_global_payment_opts = array("AE","DISCOVER","CareCredit");//,"MPAY"

define("ENABLE_REAL_ELIGILIBILITY", "YES"); //this constant Will Enable or disable the real time elegilibility. Please use Caps for ENABLE - YES and DISABLE - NO

define('CONSENT_FORM_VERSION', 'consent_v2'); // consent_v1, consent_v2 on your system.
$GLOBALS["max_recent_search_cache"] = 5; //number to show recent patient
define("ACTIVE_X_CLIENT_INFO","YES"); //this constant Will Enable or disable the Active-X functionality from the Application. Please use Caps for ENABLE - YES and DISABLE - NO
define("IDOC_IASC_SAME", ""); // possible value - "", "YES" //IF YES IT MEANS IDOC AND IASC IS SAME. This is used in interface\scheduler_v1_1_1\common\iolink_sync.php
define("DISABLE_CREDITCARD", "YES"); //used in statements

define("fax_username","admin"); //USER NAME TO SEND FAX THROUGH CONSULT LETTER
define("fax_password","admin123"); //PASSSWORD TO SEND FAX THROUGH CONSULT LETTER
define("APP_FACILITY_INCLUDE_EXPORT",""); //this constant will used in Admin->Facility as check box for to inclue those facility in patient appointment export which are associate with this.

/*---DIRECT PATIENT REGISTRATION VARIABLE--*/
define("DIRECT_PT_REGISTRATION","YES"); //SET IT TO "YES" FOR SAVING PATIENT IN LOCAL DB DIRECTLY. IF SET TO "NO", PT. WILL REGISTER (IF NOT EXITS) IN REMOTE SERVER (URL GIVEN IN NEXT VARIABLE BELOW), THEN REGISTER IN LOCAL DB WITH ID RETURNED FROM REMOTE SERVER.
define("DIRECT_PT_REGISTRATION_URL",""); //STATIC URL REQUIRED IF "DIRECT_PT_REGISTRATION" IS SET TO "NO".
define("DIRECT_PT_REGISTRATION_SERVER","NO"); //YES/NO :: "YES" = THIS SERVER WILL ACT AS MAIN SERVER AND RETURN PT.ID TO GUEST SERVER

// LOCAL SERVER NAME
$GLOBALS["LOCAL_SERVER"] = '';

define("REMOTE_SCH",1); //IF YES THEN CAN ACCESS TO REMOTE PC AND SOME CODES WILL BE IN ACTIVE.
// GLOBAL SERVER NAMES ARRAY
// NOTE : Name of server like 'boston' here should always be in lower case letters.
$GLOBALS["REMOTE_SCH_SERVER"]['boston'] = 'https://yourdomain/imwemr/interface/core/login.index.php'; // LOCAL SERVER
$GLOBALS["REMOTE_SCH_SERVER"]['brookline'] = '';
$GLOBALS["REMOTE_SCH_SERVER"]['framingham'] = '';
$GLOBALS["REMOTE_SCH_SERVER"]['leominster'] = 'https://yourdomain/TUFTS/interface/core/login.index.php'; // REMOTE SERVER
$GLOBALS["REMOTE_SCH_SERVER"]['mount auburn'] = '';
$GLOBALS["REMOTE_SCH_SERVER"]['st. elizabeth'] = '';
$GLOBALS["REMOTE_SCH_SERVER"]['wellesley'] = '';
define("ENABLE_ELIGIBILITY_5010", "YES"); //this constant Will Enable or disable the real time Eligibility for HIPAA 5010 . Please use Caps for ENABLE - YES (5010) and DISABLE - NO (4010)
//Enable :--  Resident's hx reviewed, pt interviewed, examined. 
$GLOBALS["showResHxRevwd"] = '1'; 

//Enable :--   if PQRI checking for Commercial claims is enabled
//$GLOBALS["PQRIforAllClaims"] = '1'; 
$GLOBALS["PQRIforAllClaims"] = '1'; 
$GLOBALS["REF_PHY_FORMAT"] = 'Boston'; // 'BOSTON' for boston and all satelite servers '' for others //IF 'BOSTON' THEN FORMAT IS L,F M T ELSE T L,F M 

//Stop  check  for PQRI  code given in same calender year.
//$GLOBALS["STOP_PQRI1YRFILTER"] = '1';  //1 for stop
$GLOBALS["STOP_PQRI1YRFILTER"] = '1'; 

define("SURGERY_CONSENT_VER_DATE","2013-01-02");//use in surgery censent form
define("GENERAL_ELIGIBILITY_5010", "YES");//Set this constant to "YES", it will create common EDI 270 code if payerDB is not set.
$GLOBALS["DEFAULT_PVC_SET"] = 1; // 0 | 1 // IF 0 Patient Verbal Communication in physician console will be unchecked else checked 

//CONTACT LENS CL EXAM CPT CODES VARIABLES
$GLOBALS['EVALUATION'] = '92310';
$GLOBALS['FIT'] = '92310';
$GLOBALS['REFIT'] = '92310';
$GLOBALS['CL_CHECK'] = '92310';

//Marco: Use Flat Values/Steep Values
//$GLOBALS["marcoUseFlat"] = 1;  //1 for Flat values
$GLOBALS["marcoUseFlat"] = 1; 

/***********boston network specific code (remote sync, scheduler)******/
if(isset($ignoreAuth) && $ignoreAuth == true && isset($_REQUEST['rem_sessions_arr'])){
	foreach($_REQUEST['rem_sessions_arr'] as $rms_key => $rms_val){
		$_SESSION[$rms_key] = $rms_val;	
	}
}
/*************boston code end*****************************************/
define("USERS_TYPE_AHEAD","0"); //Possible values 0=off,1=on; Toggle to show drop-dwon or Type-ahead.
define("CLAIM_STATUS_REQUEST","NO"); //"YES" TO ENABLE REALTIME CLAIM STATUS REQUESTS, "NO" TO DISABLE 
define("DISABLE_DXCODE", "YES"); //Hide DX code in statement.
define("IPORTAL","0");//IF IPORTAL VALUE IS 1. THEN IPORTAL TAB ENABLE in ADMIN.
define("MUR_VERSION","STAGE2"); // STAGE1 or STAGE2
define("AAO_IRIS_REG","Yes");//used in MUR report. Value can be 'Yes' or 'No'.
define("DEFAULT_PDF_PASSWORD", "1Med1cWare");
define("ZEISS_FORUM","NO"); //(YES/NO) To show zeiss forum buttons/links (integration).
define("ZEISS_USER","");
define("ZEISS_PASS","");
define("HIE_FORUM","NO");// (YES/NO) to configure NYU HIE forum (mackool)

/*****CUBIXX XML SUBMISSION****/
define("GENERATE_CUBIXX_CHARGES_XML","NO");
$GLOBALS["CUBIXX_CHARGES_XML_CONF"] = array('domainIP'=>'111.111.111.111','port'=>'22','path'=>'DFT','user'=>'imw','pass'=>'*Agm7X');
/*****CUBIXX XML SUBMISSION END*/

// TABS SETTINGS FOR imwemr
$GLOBALS['IMW_TAB_SETTINGS'] = array('WORKVIEW', 'MEDICAL_HX', 'SPECIFIC_REPORTS');
define("IPORTAL_SERVER","PRODUCTION");// For iPortal Define Server.
define("TESTUPLOAD_DIR", "C:\Imedic\apache\htdocs\Testimages");//define for test images auto upload.
//------BEGIN INTERNATIONALISATION VARIABLES---------------
$GLOBALS['phone_format'] = ""; // ###-###-#### for USA | (###) ###-#### | (##) ###-#### | (###) ###-### | (####) ###### | (####) ##### | (#####) ##### | (#####) #### 
$GLOBALS['phone_reg_exp_js'] = "[^0-9 +]"; // [^0-9\-+] for USA | [^0-9 +] for UK  // used in set_phone_format() function defined in IMWEMR/js/common.js
$GLOBALS['phone_length'] = "15"; // 15 | 10
//$GLOBALS['phone_min_length'] = "8"; // Comment for USA

$GLOBALS['ssn_format'] = ""; // ###-###-#### for USA | empty i.e "" for UK
$GLOBALS['ssn_reg_exp_js'] = "[^0-9A-Za-z +]"; // [^0-9\-+] for USA | [^0-9A-Za-z +] for UK  // used in validate_ssn() function defined in IMWEMR/js/common.js
$GLOBALS['ssn_length'] = "15"; // 10 for USA | 15 for UK

$GLOBALS['currency'] = "&pound;";  // &pound; for ý | $
$GLOBALS['date_format'] = "dd-mm-yyyy"; // dd-mm-yyyy | dd/mm/yyyy   | mm-dd-yyyy | mm/dd/yyyy 
$GLOBALS['zip_type'] = "alphanumeric";// numeric for USA | alphanumeric for UK
$GLOBALS['zip_length'] = "8"; // 8 for UK | 5 for USA
$GLOBALS['zip_ext'] = false; // false for UK | true for USA
$GLOBALS['state_label'] = "County"; // County for UK | State for USA
$GLOBALS['state_val'] = "full"; // full | abb 
//------END INTERNATIONALISATION VARIABLES---------------

$GLOBALS["direct_soap_file"] = "PRODUCTION"; // 'PRODUCTION' OR 'TEST'

/*====================== HL7 Config===========================*/
define("OUTBOUND_HL7_DIR","");// directory path to save HL7 message flat file.
define("INBOUND_HL7_DIR","");// directory path to read incoming hl7 flat file.
$GLOBALS["SSH_OUTBOUND"] 	= false;// or array('domainIP'=>'','port'=>'','path'=>'','user'=>'','pass'=>''); //To write hl7 msg to ssh.
$GLOBALS["SSH_INBOUND"] 	= false; //or array('domainIP'=>'','port'=>'','path'=>'','user'=>'','pass'=>'','post2url'=>''); //To READ hl7 msg FROM ssh.
$GLOBALS["SSH_OUTBOUND_DIR_MAPPING"] = false; //array('ZMS'=>'HL7_Rx_In','*'=>'HL7_ADT_In');
define("HL7_READ_EXTENSION", '');

// Parser Version Number.
define("HL7_READER_VERSION", "");

// ERROR_LOG
define("HL7_READER_ERROR_LOG", 0); //1 - on , 0 - off
// ERROR_LOG_PATH
define("HL7_READER_ERROR_LOG_PATH", $webserver_root.'/data/'.PRACTICE_PATH.'/HL7_READ_ERRORS');

// RESPONSE_LOG
define("HL7_RESPONSE_LOG", 0); //1 - on , 0 - off
// RESPONSE_LOG_PATH
define("HL7_RESPONSE_LOG_PATH", $webserver_root.'/data/'.PRACTICE_PATH.'/HL7_RESPONSE');

define("HL7_ADT_GENERATION",false);//VALUE: true/false; to generate ADT hl7 message, store in table hl7_sent;
define("HL7_ADT_GENERATION_OLD",false);//VALUE: true/false; to generate hl7 with R7 script;
define("HL7_SIU_GENERATION",false);//VALUE: true/false; to generate ADT hl7 message, store in table hl7_sent;
define("SUP_DFT_GENERATION",false);//VALUE: true/false; To turn ON HL7 DFT message generation upon Superbill changes in WorkView 
define("TEST_SUP_DFT_GENERATION",false);//VALUE: true/false; To turn ON HL7 DFT message generation upon Superbill changes in Tests. 
define("ACC_DFT_GENERATION",false);//VALUE: true/false; To turn ON HL7 DFT message generation upon ENTER CHARGES saving in Accounting
define("HL7_ZMS_GENERATION",false);//VALUE: true/false; 
//define("PTVISIT_ORU_GENERATION",true);//VALUE: true/false; To turn ON HL7 message generation Patient visit note (Soarian Interface - tufts)
//define("PTVISIT_ORU_GENERATION_FOR",'SORIAN');//STRING VALUE, NAME OF DESTINATION.
//define("PTVISIT_ORU_GENERATION_FILEPATH",'/var/tmp/sorian/');//STRING VALUE, PATH OF DESTINATION.

define("HL7_MFN_REF_PHY",false);//VALUE: true/false; to generate MFN messages for Referring Physicians;
define('HL7_SENT_TABLE', 'hl7_sent');	/*Table for Logging the Messages - hl7_sent/hl7_sent_forum*/
define('HL7_RECEIVED_TABLE', 'hl7_received');	/*Table for Logging the Messages - hl7_sent/hl7_sent_forum*/
define('HL7_INCLUDE_ZIN', false);	/*Include Additional Insurance Segment*/

//$GLOBALS['HL_RECEIVING'] = array('APPLICATION'=>'IMW', 'FACILITY'=>'IMW');
//$GLOBALS['HL_SENDING'] = array('APPLICATION'=>'', 'FACILITY'=>'');

$GLOBALS['HL7_ADT_SEGMENTS'] = array('PID', 'GT1', 'NK1EMC', 'NK1', 'IN1', 'PV1');	/*Segments to be included in ADT Messages*/
$GLOBALS['HL7_SIU_SEGMENTS'] = array('PID', 'SCH', 'PV1');	/*Segments to be included in SIU Messages*/
$GLOBALS['HL7_MFN_SEGMENTS'] = array('MFI', 'MFE', 'STF', 'PRA');	/*Segments to be included in MFN Messages*/
$GLOBALS["HL7_SIU_EVENTS"] = array(0, 11, 13, 18, 202, 203);	//Events to Log HL7 SIU Messages

$GLOBALS['HL7_SENDER'] = array('IP'=>'0.0.0.0', 'PORT'=>'0000', 'ACK_WAIT'=>10);
//$GLOBALS['HL7_SENDER_ARRAY'] = array('DFT'=>array('IP'=>'', 'PORT'=>'', 'ACK_WAIT'=>15),'ADT'=>array('IP'=>'', 'PORT'=>'', 'ACK_WAIT'=>15));
//$GLOBALS['HL7_SENDER_ZEISS'] = array('IP'=>'0.0.0.0', 'PORT'=>'0000', 'ACK_WAIT'=>10);
//$GLOBALS['HL7_RECEIVER_ZEISS'] = array('IP'=>'0.0.0.0', 'PORT'=>'5001', 'URL4POST'=>'http://100.250.25.21/imwemr/hl7sys/old/data_receiver/HL7_to_DB_zeiss.php');

// DEBUG_MODE
define("HL7_READER_DEBUG_MODE", 0); //1 - on , 0 - off
define("HL7_DEFAULT_INSURANCE_CASE_TYPE", "1");
define("HL7_DEFAULT_RESCHEDULE_STATUS_ID", "202");
define("HL7_DEFAULT_CHECKIN_STATUS_ID", "13");
define("HL7_DEFAULT_CHECKOUT_STATUS_ID", "11");
define("HL7_DEFAULT_CANCEL_STATUS_ID", "18");


define('LISTENING_IP','0.0.0.0');
define('WRITE_DEBUGGING_LOG', false);
define('URL4POST','{practice url}/hl7sys/receiver/HL7_to_DB.php');

/*R8 : Type of messages we are expecting on this listener*/
$GLOBALS['HL7_LISTENING'][0]['MESSAGES'] = array('');        
$GLOBALS['HL7_LISTENING'][0]['PORT'] = ''; //Used in OLD code also.


/*R7 : Type of messages we are expecting on this listener*/
define('LOCAL_PRACTICE_NAME','IMW');//will be used in ACK header.
define('HL7READER','{practice url}/hl7sys/HL7Reader/index.php'); //URL where HL7 parsing done

/*====================== End HL7 Config===========================*/

define("SHOW_SERVER_LOCATION","1");//IF SHOW_SERVER_LOCATION VALUE IS 1 THEN SERVER LOCATION DROP-DOWN ENABLE in ADMIN=>FACILITY.
define("stop_notes_alert","1");//IF stop_notes_alert VALUE IS 1 THEN ACCOUNTING NOTES ALERT WILL BE DISABLED.
define("connect_optical","1");//IF connect_optical VALUE IS 1 THEN Optical Functionality WILL BE Work in iDOC.
define("BAR_CODE_DISABLE","YES");//IF BAR CODE DISABLE VALUE IS "YES" THEN BAR CODE Functionality WILL NOT Work in iDOC.
//$_GLOBALS["SB_POS_Default"] = ""; //Set Default POS facility in Superbill

define("ZEISS_API_PATH", ""); // directory path of "ZEISS Forum Viewer" API

//if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false){define("AV_MODULE","YES");/*Audio/Video Module turned ON for Chrome.*/}
define("ERX_DATA_DOWNLOAD","NO"); //(YES/NO) To enable/disable erx data download practice wise. Enabling this may lead to data overwrite for Medication and Allergies.
define("STOP_CONVERT_COMMAND", "NO"); //(YES/"") TO STOP CONVERT COMMAND IN SCAN DOCS
define('STOP_ZIPCODE_VALIDATION', "YES"); // "YES" | ""  TO STOP ZIPCODE VALIDATION

$GLOBALS['CHART_APP_VERSION'] = "0"; //any numeric, always increase by  1, update js, css resources
define("DIRECT_EMAIL_NOTIFICATION_TIMER",0);//Time in MINUTES; Interval To recheck direct email. 0=OFF; positive number (integer) sets Interval.
define("DISABLE_DEFAULT_FOLDER",1);// TO STOP BY DEFAULT ADD "MEDICATION" FOLDER IN SCAN DOCS IF NOT EXIST
$GLOBALS['optical_directory_location'] 	= "";// OPTIONAL -  FOR EXAMPLE - http://192.168.0.5
$GLOBALS['optical_directory_name'] 		= "Optical";// Optical Directory Name
$GLOBALS['iasclink_directory_location'] = "";// OPTIONAL -  FOR EXAMPLE - http://192.168.0.5
$GLOBALS['iasclink_directory_name'] 	= "iOLink";// iASCLink Directory Name

/*------------- Updox Related Configurations -------------*/
define("UPDOX_APP_ID", "imwemr");	/*Updox Application ID*/
define("UPDOX_APP_PASSWORD", "yourpassword");	/*Updox Application Password - QA*/
define("UPDOX_FAX_STATUS", false);	/*Update sent fax status, by callback from updox*/
define("UPDOX_PRODUCTION", false);	/*Use Updox Production API end point*/
define("UPDOX_DIRECT", false);
define("REPLY_AS_PHYSICIAN", false);/*reply other physicians direct messages from phy console on there behalf. Possible values true,false*/
define("UPDOX_USE_WEBHOOKS", true);	/** Weather to use  webhook or pull mechanism for updatting direct status in IMW */
define('UPDOX_IP_RANGE_VALIDATION', true); // TRUE || FALSE - Enable/Disable IP range validation for Inbound communication for Updox
/*------------- Updox Configurations Ends -------------*/

/****************BILLING VARIABLES************/
	/*Transaction Set Unique code*/
	$billing_global_tsuc_separator = '_';
	
	/*To control server specific changes in claim files*/
	$billing_global_server_name = ''; /*gewirtz, brian, kung*/
	
	/*Blue Cross Blue Shield (BCBS) Payers*/
	$arr_BL_payers = array('22099','SB800','SB803','00303','23738','BS001','54763','54721','HM007','940360000','94036','12011','12B35','SB865','00590','SB760','12B18','SB338','12B24');
	
	/*Medicare Payer IDs--*/
	$arr_Medicare_payers = array('SDMEA','SRRGA','13202','SMNY0','SMAZ0','13292','SMPA0','SMTX0','SMCA1');
	
	/*CLIA #*/
	$billing_global_clia_num = '';
	
	/*DME Payer IDs*/
	$arr_DME_payers = array('SDMEA','SDMEB','SDMEC','SDMED','SPRNT');
	
	/*Railroad Medicare Payer IDs*/
	$arr_RRM_payers = array('SRRGA','00882','C00882');
	
	/*NE Medicaid Payer IDs*/
	$arr_NE_Medicaid_payers = array('SKNE0','SKNY0','SKLA0');
	
	/*Taxonomy #*/
	$billing_global_taxonomy_number = '';
	
	/*Overwrite clm_type_indicator; Empty means not overwrite. Otherwise set the value to 1 or 7*/
	$billing_global_clm_type_indicator_medicare = '';
	$billing_global_clm_type_indicator_emdeon = '';
	
	//Stop ERA Negative Payment Post
	$stop_era_neg_pay_post='';

	//Show facility in UB04 box 76. Empty mean old functionality otherwise set the value to 1.
	$show_facility_in_ub04 = '1';

	/*G code procedures*/
	$arr_g_code_proc = array('G0008','G0009','G0101','G0117');
	
	//Show Default Claim Type 831 or 837
	$show_default_claim_type="";

	//Billing Global Practice Identification
	$billing_global_practice_dentifier = "";
	
	//Used in claim generation Rendering Provider loop.
	$FACILITY_BILLING_PROVIDERS = array();
	
	/*Save ERA files to be used by external application*/
	define("ERA_SHARE", false);	
	/*Dowload era files from external practice - IMWEMR installation*/
	define("ERA_URL", "");
	
	/**SET COPAY FROM RTE, BY DEFAULT; (TRUE/FALSE)**/
	define("SET_COPAY_FROM_RTE",false);
	
	/**CHC ERA DOWNLOAD & ERA STEP BY STEP**/
	define("ERA_DOWNLOAD_DELETE_IN_STEP","no");
/**************END OF BILLING VARIABLES*******/

/**** Chart Globals *****/
$GLOBALS['Timeout_chart']="10"; //Used in Pt Chart Lock

/*
Following are the user_type ids for different types of users in IMWEMR. 
these ids can be used in below arrays to enable or disable below functions for diffrent type of users.
user_type_id	 user_type_name
1		Physician
2		Nurse
3		Technician
4		Staff
5		Test
6		Surgical Coordinator
7		Physician Assistant
8		CFNP
9		CRNA
10		Consultant
11		Resident
12		Attending Physician
13		Scribe
14		Medical Assistant
15		Office Manager
16		Security Admin
17		Receptionist
18		Billing Administrator
19		Fellow
20		Medical Student
21		APRN
*/
/*
//Valid UserType of chart Notes Physicians and Technicians --
*/
$GLOBALS['arrValidCNPhy'] = array(1,10,11,12,19,21);
$GLOBALS['arrValidCNTech'] = array(2,3,6,7,8,9,13,20);
$GLOBALS['arrFollowPhyUserTypes'] = array(3,11,13,19,20); 

/*VF, VF-GL test options*/
$GLOBALS["vfTestOpts"]=array(); 

/*
//set doc id, it will be Always doctor in FU
*/
$GLOBALS["alwaysDocFU"]=""; 

/*
//POE CPT CODE
*/
$GLOBALS["cpt_code_poe"]="99024";

//Default Cylinder Sign
$GLOBALS["def_cylinder_sign"]="-";
//CYLINDER SIGN FOR CONTACT LENS
$GLOBALS["def_cylinder_sign_cl"]="-";
//CYLINDER SIGN FOR CONTACT LENS OVER-REFRACTION
$GLOBALS["def_cylinder_sign_cl_ref"]="-";


//CONTACT LENS SPHERE/POWER RANGE
$GLOBALS['CL_POWER_RANGE']['min']=-8.00; //DEFAULT -20 TO +20
$GLOBALS['CL_POWER_RANGE']['max']=8.00;
//CONTACT LENS CYLINDER SETTINGS
$GLOBALS['CL_CYLINDER_RANGE']['min']=-16; //NOTE: IF NEGATIVE THEN MIN=-16 AND MAX=0. IF POSITIVE THEN MIN=0 AND MAX=16. 
$GLOBALS['CL_CYLINDER_RANGE']['max']=16;
$GLOBALS['CL_CYLINDER_RANGE']['cl_cylinder_jump']=0.25; //DEFAULT IS 0.25
$GLOBALS['CL_CYLINDER_RANGE']['cl_cylinder_zero_pos']='';
$GLOBALS['CL_CYLINDER_RANGE']['cl_cylinder_val_order']='';

/*
Check Archive Table for Drawings : Enter Archive Database Name
*/
$GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"]="";

/*
//stop exam label with icon: 1 to stop, 0 to not
*/
$GLOBALS["no_exm_label_w_icon"]="0";

/*
Add Preiphery WNL into retinal exam or not
*/
$GLOBALS["ADD_PERI_WNL_2_RETINAL"]="1";

/*
Rx Print – Please make this as practice specific
*/
$GLOBALS["PrintBtnRx"]="1";

/*
Stop SUBJECTIVE Data to fill in PC
*/
$GLOBALS["STOP_SUBJECTIVE_DATA"]="1";

/**** Chart Globals *****/

/*HPI format:: 1 for Bullet format, otherwise line format .*/
$GLOBALS["HPI_FORMAT"]="1";

/* show MRN info in patient account: set field name like External_MRN_2 */
$GLOBALS["SHOW_PT_MRN"]="";

/* show multiple dx codes in separate line in assessmentplan: put 1 to enable */
$GLOBALS['asmt_sep_multi_dx_code']="";

/* show synergy link in tests: set value 1 to enable it */
$GLOBALS['Show_Synergy_Icon']="";

/* Stop Medication start date to enter in medication when med orders are added from chart finalize. 1 or any positive value will stop date to enter in medication.*/
$GLOBALS['NO_ADD_MED_START_DATE']=0;

/* Prism drop downs : values in quaters : possible values 1 or 0 */
$GLOBALS['PRISM_QTR_VALUES']=1;

/*Stop alert for multiple same visit code in a superbill*/
$GLOBALS['MULTIPLE_VISIT_CODES_NOALERT']=1;

/**************iMedicMonitor Related Settings*********/
define('IMM_ALWAYS_REFRESH','NO');//YES/NO.
define('DEFAULT_DILATION_TIMER', '15');
define('DEFAULT_REFRESH_INTERVAL', '30000');
/**************iMedicMonitor settings end*************/

define("SHOW_INS_STATE_PAYER_CODE",""); //Possible value "" | "PA" | "<OTHER_STATE_PAYER_CODE>" If "PA" then show payer codes of PA state.(BETZ server)
define("ASCEMR_IMW_PROVIDER_ID",""); //Possible value "" | "<id of billing user used for ascemr>" (HARTMAN server)

#################################################################################
######################### SQL RELATED DEFININGS #########################
#################################################################################

$sqlconf = array();
$sqlconf["host"]= 'localhost';
$sqlconf["port"] = '3306';
$sqlconf["login"] = 'root';
$sqlconf["pass"] = '123456';
$sqlconf['idoc_db_name'] = 'imw_dev';
$sqlconf['sc_db_name'] = 'surgerycenter';
$sqlconf['scan_db_name'] = 'imw_dev_scan';
define("IMEDIC_SCAN_DB",$sqlconf['scan_db_name']);//scan db NAME
define("IMEDIC_SC",$sqlconf['sc_db_name']);//surgery center
define("IMEDIC_IDOC",$sqlconf['idoc_db_name']);//iMedic iDOC 

global $gbl_imw_connect;
global $gbl_sc_connect;
$gbl_imw_connect = array($sqlconf["host"],$sqlconf["port"],$sqlconf["login"],$sqlconf["pass"],constant('IMEDIC_IDOC'));
$gbl_sc_connect = array('192.168.1.35','3306','root','123456',constant('IMEDIC_SC'));

$GLOBALS['SHA_FILE_VAL'] = true; //	True/False -- To show SHA256 key value of a file/attachment

$GLOBALS['r7_directory_name'] 	= "imwemr-Dev_old";//R7 Directory Name
$GLOBALS['r7_db_directory_name'] 	= "imwemr-Dev";//R7 Directory Name

//PAM2000 FORMAT ARRAY FOR FACILITY ONLY FOR REPORTS
//EXAMPLE: ARRAY KEY IS THE FACILITY ID AND VALUE IS FACILITY NAME.
$GLOBALS['PAM2000'] = array(); //array('01'=>'BRANDON EYE', '02'=>'PLANT CITY');

/******* VITAL INTERACTION **********/
//SFTP details for Vital Interactions
define("VITAL_HOST", "upload.vitalinteraction.com");
define("VITAL_USER", "SFTP user name");
define("VITAL_PASS", "SFTP password");
//for how many future days we do need to push data every day 1year/365 days is default range
define("VI_DATA_PUSH_RANGE", "+365 days");
//Readback program related variables
define("VITAL_IDOC_USER", "vital_dummy");//if its defined that means appt sts update via vital is active here (readback program). This must contain a valid username which is being further used to identify related user

/******* scheduler related settings *******/
define('MAX_SCHEDULE_PER_SLIDE', 5); //show max physician schedules per slide , 5 is max value
define("DISABLE_CLICK_SCHEDULER_RAIL",1);// TO STOP ADD APPOINTMENT ON CLICK TO RAIL.
define("ENABLE_SCHEDULER_RAIL_CHECK",1);// FOR MATCHING LABEL AND FROCEDURE FROM LEFT RAIL AND TO REMOVE MAX APPT LIMIT AS PER PROCEDURE.
define("RX_EXPIRY_DATE",12);// TO SET THE EXPIRATION DATE FOR PRESCRIPTION BASED ON SERVER REQUEST e.g. 12 or 24 months.
define("PT_SUMMARY_HEADER_INFO","FACILITY");// DEFAULT IT WILL BE DISPLAY THE GROUP INFORMATION, IF SET FACILITY THEN WILL DISPLAY THE FACILITY INFORMATIONS
define("ENABLE_SCHEDULER_TRACK_LOG", 1);//set txt log for cancel reschedule appt on or off
define("DEFAULT_TIME_SLOT", 10); //(Possible Values 5 or 10) - This is one time insatallation setting. Please DO NOT CHANGE this setting if you have appt records
define("DEFAULT_OFFICE_CLOSED_COLOR",'#CC0000');
define('SCHEDULER_VERSION', 'scheduler_v1_1_1'); // scheduler_v1_0_0, scheduler_v1_1_0
define('SCHEDULER_GANTT_CHART', 'enable'); // enable, disable - please note it only works for scheduler_v1_1_0 or higher
define('SCHEDULER_FAC_COLOR_ON_CAL', 1);//hold values 1,0. used to check does we show facility color on schedule calendar instead of purple one
define('SCHEDULER_FD_ICON', 1);//hold values 1,0. used to show icons instead of buttons
define('SCHEDULER_RESTORE_ON_BLOCK', 1);//while appointment restore shall we check for lock/block or not, 1=yes and 0=no, if not defined that means 0 = no 
define('SCHEDULER_CACHE_RANGE', 730);//value in days

define('MARK_AS_NO_SHOW',true);// Enable functionality on first logger to mark appts as no show, possible values true = ON and false = Off
define('MARK_AS_NO_SHOW_FAC','0');// lists of facilities ids (comma (,) seprated id field from table facility ) to exclude from no show operation if first setting is set to true
define('MARK_AS_NO_SHOW_DOC','0');// lists of provider/doctor (comma (,) seprated id field from table users) to exclude from no show operation if first setting is set to true
define('MARK_AS_NO_SHOW_PRO','0');// lists of procedure (comma (,) seprated id field from table slot_procedure) to exclude from no show operation if first setting is set to true

define('SCHEDULER_SHOW_STATUS_COLOR',true);// show appt status color infront of appt in right bar schedule, possible values true and false
define('SCHEDULER_SHOW_PROC_TYPE',true);// show appt procedure type infront of appt in right bar schedule, possible values true and false

define('SCHEDULER_SHOW_PROC_ONLY_FOR_AVAL_LIST',true);// show procedure only under available list. Possible values are true and false
//Unity Setup
define("ALLSCRIPTS", true); /*Enable Allscripts*/
define("UNITY_LOG_CALLS", true); /*Log Unity API calls*/
define("DISP_EXTERNAL_MRN", "2"); //IF VALUE IS "1" THEN FIRST MRN1 WILL DISPLAY. IF VALUE IS "2" THEN FIRST MRN2 WILL DISPLAY IN DEMOGRAPHIC SCREEN.
define("EXTERNAL_MRN_SEARCH","YES"); //this constant Will Enable or disable the External MRN Search. Please use Caps for ENABLE - YES and DISABLE - NO
define("EXTERNAL_INS_MAPPING","NO"); //this constant Will Enable or disable the External Insurance mapping. Please use Caps for ENABLE - YES and DISABLE - NO

//SET API CALLS VARIABLES
define('API_CUSTOMER_ID', '5266'); //CUSTOMER ID IS DIFFERENT FOR EVERY SERVER
define('API_SECRET_KEY', 'd2ae343dbc964f63800d69125ab18a88'); //THIS IS MD5 CREATED SECRET KEY
define('DOC_POPUP',false);// Enable doc tab to open in popup window - true|false; Default is false
$GLOBALS['show_reason_code_statment']	= "YES"; // If value is "YES" then reason code will show in statment.
$GLOBALS['bl_pt_home_facility']	= "YES"; // If value is "YES" then Patient Home facility will show in Billing Location.
define('PAG_HOVER_STOP',false);  //define constant to disable "Patient at a glance" mouse hover popup
define('CHECKIN_ON_DONE',1);  //define constant to change appt status in checkin popup window 

// Array added to show CL power range specificically in contact lens worksheet.
$GLOBALS['CL_POWER_RANGE'] = array('min' => -20, 'max' => 20);

// Array added to show specific ranges for CL cylinder in contact lens worksheet
$GLOBALS['CL_CYLINDER_RANGE'] = array(
                                        'min' => -2.75,
                                        'max' => -0.75,
                                        'cl_cylinder_jump' => 0.50,
                                        'cl_cylinder_zero_pos' => "TOP",
                                        'cl_cylinder_val_order' => "ASC"
									 );
									 

/***** DSS *****/
  
define("DSSAPI", true); /*Enable DSS API*/

/***** DSS *****/

//Show resp. party in Payment Screen. // If value is "1" then it will show.
	$show_resp_party = '1';
define("SHOW_PR_FAC_ADDR","YES"); //Possible value "YES" | "" If "YES" then show facility address in Payment Receipt Print.								

/***** UGA Finance *****/
define("UGAAPI", true); // Enable UGA API
define("UGA_API_LOG", true); // Enable UGA API LOG
define("UGA_ENV","STAGING"); // STAGING or PRODUCTION
define("UGA_API_KEY", "THISISADEMOKEYNOTFORPROD"); // UGA API KEY
define('UGA_CONTACT_CUSTOMER_NUMBER', 'T5C73DXX67');
define('UGA_LOCATION_ID', 'T9679DF');
define('UGA_PROGRAM_NUMBER', 'TC2AX');
/***** UGA Finance *****/

/*****REVIEW INC SUBMISSION****/
define("REVIEWINC_SUBMIT", false);
$GLOBALS['REVIEWINC_CONF'] = array('mode'=>'T','corporateId'=>'','apiKey'=>'');
/*****REVIEW INC SUBMISSION END*/

/*****TESTIMONIAL TREE SUBMISSION****/
define("TESTIMONIALTREE_SUBMIT", false);
$GLOBALS['TESTIMONIALTREE_CONF'] = array('surveyId'=>'NNNNN','ftpdomain'=>'ftp.example.com','ftpuser'=>'exampleuser','ftppass'=>'examplepass');
/*****TESTEIMONIAL TREE SUBMISSION END*/

// Use this for Windsor Eye server
$GLOBALS['phone_country_code'] = 0; // 1 = Zip Code, 0 = Post Code

$GLOBALS["ADDON_SU_FIELD"] = true; /* Constant used to add username field in the switch user window. */

//Tsys API Payments using TSEP Set variable 1=Production and 0=Staging to set URL for staging or production.
$GLOBALS["TSEP_MANIFEST_URL"] = 0; //1=Production, 0=Staging to set the manifest URL

/*
PRS GL RX COMMENTS DISPLAY - STILL IT IS SERVER SPECIFIC - FOR PREMIER EYE..
BY DEFAULT IT SHOULD BE EMPTY.
WHEN SET IT '1', IT WILL BE ON
*/
$GLOBALS["SHOW_PRS_RX_COMMENTS"]="1";
define('SHOW_INS_SCAN_IN_MODAL_POPUP',1);  //define constant to show insurance scan in modal popup instead of seperate window

//Enable Disable POS FACILITY GROUP functionality
$GLOBALS["POS_FACILITY_GROUP"] = 1; //1=Enabled, 0=Disabled to set the POS FACILITY GROUP. 

//ENABLE CUBIXX XML CHARGES
define("GENERATE_CUBIXX_CHARGES_XML","NO"); //Set value to "YES" to generate XML.


$print_paper_from_claims = '1'; //Print paper claim based on 837 file

//Enable Disable default logged in user selected in accounting notes functionality
$GLOBALS["SELECT_DEFAULT_LOGGED_IN_USER"] = 1; //1=Enabled, 0=Disabled to select the logged in user in dropdown.

/*Enable Disable ERP API PATIENT PORTAL functionality EYE REACH PATIENT PORTAL*/
$GLOBALS["ERP_API_PATIENT_PORTAL"] = 1; //1=Enabled, 0=Disabled to set the ERP API PATIENT PORTAL. 
$GLOBALS["ERP_API_PATIENT_PORTAL_URL"] = 0; //1=Production, 0=Staging to set the ERP API PATIENT PORTAL URL. 
/*Rabbit Mq credentials for ERP PATIENT PORTAL */
define("RABBITMQ_HOST","host.yourdomain.com");   // For localhost "host.yourdomain.com" resolve IP in hosts file
define("RABBITMQ_PORT","5672");
define("RABBITMQ_USER","username");
define("RABBITMQ_PASS","password");
define("RABBITMQ_REQUEST_EXCHANGE","RequestsExchange");
define("RABBITMQ_RESPONSE_EXCHANGE","iMWExchange");
define("RABBITMQ_EXCHANGE_TYPE","topic");
define("RABBITMQ_DURABLE",true);
define("RABBITMQ_REQUEST_ROUTING_KEY","api.request");
define("RABBITMQ_RESPONSE_ROUTING_KEY","IMW.*");
define("RABBITMQ_REQUEST_QUEUE","api-requests");
define("RABBITMQ_RESPONSE_QUEUE","iMWQueue");
/*Rabbit Mq credentials for ERP PATIENT PORTAL */

/*RX Notification Consent Credentails and API URL*/
define("RX_NOTIFICATION_URL","https://host.your.com/link/rest/api");
define("RX_NOTIFICATION_USER","emr-stage");
define("RX_NOTIFICATION_PASS","password");

?>