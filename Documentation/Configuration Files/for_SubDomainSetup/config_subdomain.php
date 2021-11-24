<?php
//Error reporting settings
error_reporting(0);
ini_set("display_errors",0);
date_default_timezone_set('America/New_York');

//Internal and External IP
$myInternalIP = "sample.imwemr.com";
$myExternalIP = "sample.imwemr.com";
$phpServerIP = "sample.imwemr.com"; //24.225.173.41 //127.0.0.1
$phpServerPort = "";
$protocol = 'https://';
$phpHTTPProtocol = $protocol;

//---- imwemr main tabs Array ---------
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

define("DEFAULT_OFFICE_CLOSED_COLOR",'#CC0000');
define("DEFAULT_PRODUCT",'imwemr'); //iMWCare //imwemr
define("OPTICAL_DISPLAY_PROVIDER","YES");
$enableIolink = true; //To make availability of IOlink tab in admin module

$GLOBALS['phone_format'] = "###-###-####" ; //(###) ###-####

/*Enable/disable chart notes patient lock functionality. Single user access pt chart at a time. enable - 1, disable - 0*/
define("SEM_CHART_LOCK","1");

//Valid UserType of chart Notes Physicians and Technicians --
$GLOBALS['arrValidCNPhy'] = array(1,2,7,8,9,10,11,12,14);
$GLOBALS['arrValidCNTech'] = array(3,6);

//PAYMENT METHODS
$arr_global_payment_opts = array("AE","DISCOVER","CareCredit");

define("DEFAULT_TIME_SLOT", 10); //(Possible Values 5/10). One time insatallation setting. DO NOT CHANGE this setting if you have appt records
define("CLAIM_STATUS_REQUEST","YES"); //"YES" TO ENABLE REALTIME CLAIM STATUS REQUESTS, "NO" TO NO disable.
define("HASH_METHOD","SHA2"); //it can be SHA2 or MD5
define("APP_DEBUG_MODE", 1); //1 - on, 0 - off
define("PASS_EXPIRY_NOTICE_DAYS", 7); // 7 days of password expiry warning.
define('SCHEDULER_VERSION', 'scheduler_v1_1_1'); // scheduler_v1_0_0, scheduler_v1_1_0 on your system.
define('SCHEDULER_GANTT_CHART', 'enable'); // enable, disable - please note it only works for scheduler_v1_1_0 or higher
define('CONSENT_FORM_VERSION', 'consent_v2'); // consent_v1, consent_v2
define("ENABLE_REAL_ELIGILIBILITY", "YES"); //this constant Will Enable or disable the real time elegilibility. Please use Caps for ENABLE - YES and DISABLE - NO
define("IPORTAL","1");//IF IPORTAL VALUE IS 1. THEN IPORTAL TAB ENABLE in ADMIN.
define("V_CHART_LOCK", "1");//Chart Notes Lock
$GLOBALS["max_recent_search_cache"] = 5;
//$GLOBALS["LOCAL_SERVER"] = 'Boston';
define("SHOW_SERVER_LOCATION","1");//IF SHOW_SERVER_LOCATION VALUE IS 1 THEN SERVER LOCATION DROP-DOWN ENABLE in ADMIN=>FACILITY.
define("IPORTAL_SERVER","");
//Enable :--   if PQRI checking for Commercial claims is enabled
$GLOBALS["PQRIforAllClaims"] = '1';
define("stop_notes_alert","");//IF stop_notes_alert VALUE IS 1 THEN ACCOUNTING NOTES ALERT WILL BE DISABLED.
$_GLOBALS["show_facility_in_ub04"] = "1";
define("DISABLE_CREDITCARD", ""); //used in statements
$GLOBALS['SHA_FILE_VAL'] = true; //	True/False -- To show SHA256 key value of a file/attachment
//define("connect_optical","1");//IF connect_optical VALUE IS 1 THEN Optical Functionality WILL BE Work in iDOC.
define("IDOC_IASC_SAME", ""); //IF YES IT MEANS IDOC AND IASC IS SAME. This is used in interface\scheduler_v1_1_1\common\iolink_sync.php

define("UPDOX_APP_ID", "imwemr"); /*Updox Application ID*/
define("UPDOX_APP_PASSWORD", "imY50sGrHUrz)8RkQ02Bm-9gnjbEZ.0c"); /*Updox Application ID*/
define("UPDOX_FAX_STATUS", true); /*Update sent fax status, by callback from updox*/
define("UPDOX_PRODUCTION", true); /*Use Updox Production API end point*/
define("UPDOX_DIRECT", true);

$GLOBALS['optical_directory_location'] 	= "";// OPTIONAL -  FOR EXAMPLE - http://192.168.0.5
$GLOBALS['optical_directory_name']	= "";// Optical Directory Name
$GLOBALS['iasclink_directory_location'] = "";// OPTIONAL -  FOR EXAMPLE - http://192.168.0.5
$GLOBALS['iasclink_directory_name'] 	= "";// iASCLink Directory Name

/**** Chart Globals *****/
$GLOBALS['Timeout_chart']="10"; //Used in Pt Chart Lock

/*
Following are the user_type ids for different types of users in imwemr. 
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

/*
//Default Cylinder Sign
*/
$GLOBALS["def_cylinder_sign"]="-";

/*CYLINDER SIGN FOR CONTACT LENS */
$GLOBALS["def_cylinder_sign_cl"]="-";

//CYLINDER SIGN FOR CONTACT LENS OVER-REFRACTION
$GLOBALS["def_cylinder_sign_cl_ref"]="-";

/*
Check Archive Table for Drawings : Enter Archive Database Name
*/
$GLOBALS["imwemr_SCAN_DB_ARCHIVE"]="";

/*
//stop exam label with icon: 1 to stop, 0 to not
*/
$GLOBALS["no_exm_label_w_icon"]="0";

/*
Add Preiphery WNL into retinal exam or not
*/
$GLOBALS["ADD_PERI_WNL_2_RETINAL"]="1";

/*
Rx Print � Please make this as practice specific
*/
$GLOBALS["PrintBtnRx"]="1";

/**** Chart Globals *****/


/****************BILLING VARIABLES************/

        /*Transaction Set Unique code*/
        $billing_global_tsuc_separator = '_';

        /*To control server specific changes in claim files*/
        $billing_global_server_name = 'sample'; /*gewirtz, brian, kung*/

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

/**************END OF BILLING VARIABLES*******/

#################################################################################
######################### SQL RELATED DEFININGS #########################
#################################################################################

$sqlconf = array();
$sqlconf["host"]= '10.1.25.173';
$sqlconf["port"] = '3306';
$sqlconf["login"] = 'imw_app';
$sqlconf["pass"] = 'imw@A23$534';
$sqlconf['idoc_db_name'] = 'sample_imwemr';
$sqlconf['scan_db_name'] = 'sample_imwemr_scan_db';

define("imwemr_IDOC",$sqlconf['idoc_db_name']);//imwemr iDOC 
define("imwemr_SCAN_DB",$sqlconf['scan_db_name']);//scan db NAME

//For iASCEMR Only
/*
$sqlconf['sc_db_name'] = 'dev_scemr';
define("imwemr_SC",$sqlconf['sc_db_name']);//surgery center
global $gbl_imw_connect;
global $gbl_sc_connect;
$gbl_imw_connect = array($sqlconf["host"],$sqlconf["port"],$sqlconf["login"],$sqlconf["pass"],$sqlconf['idoc_db_name']);
$gbl_sc_connect = array($sqlconf["host"],$sqlconf["port"],$sqlconf["login"],$sqlconf["pass"],$sqlconf['sc_db_name']);
*/

?>
