<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
set_time_limit(900);
include(dirname(__FILE__)."/common/conDb.php"); 
if(!isset($its_demo_msg_show_only)) $its_demo_msg_show_only = false;

if(!function_exists("newMessageUniqueId")){
	function newMessageUniqueId(){
		$res1 = imw_query("SELECT if(MAX(id) IS NULL,0,MAX(id))+1  as NewMsgId FROM hl7_sent");
		if($res1 && imw_num_rows($res1)==1){
			$rs1 = imw_fetch_assoc($res1);
			$NewMsgId = $rs1['NewMsgId'];
			$set_number = $NewMsgId * 1000;
			$set_number = substr($set_number,0,7);
			$set_number = $set_number + $NewMsgId;
			return $set_number;
		}
		return false;
	}
}

if(!function_exists("get_confId_fac_name")){
	function get_confId_fac_name($ptConfID){
		$res = imw_query("SELECT ft.fac_name,ft.fac_city FROM stub_tbl st JOIN facility_tbl ft ON(st.iasc_facility_id = ft.fac_idoc_link_id) WHERE st.patient_confirmation_id = '".$ptConfID."' ORDER BY st.stub_id LIMIT 1");
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return $rs;			
		}
		return false;
	}
}

if(!function_exists("mapping_vocabulary")){
	function mapping_vocabulary($section,$key){
		
		$mapping_array = array();
		$mapping_array['RACE']['ASIAN'] 								= "5";
		$mapping_array['RACE']['BLACK'] 								= "2";
		$mapping_array['RACE']['BLACK OR AFRICAN AMERICAN'] 			= "2";
		$mapping_array['RACE']['CAUCASIAN'] 							= "1";
		$mapping_array['RACE']['HAWAIIAN/PAC ISLAND'] 					= "18";
		$mapping_array['RACE']['NATIVE HAWAIIAN OR OTHER PACIFIC ISLANDER'] = "18";
		$mapping_array['RACE']['HISPANIC'] 								= "4";
		$mapping_array['RACE']['INDIAN'] 								= "7";
		$mapping_array['RACE']['NATIVE AMERICAN'] 						= "3";
		$mapping_array['RACE']['OTHER'] 								= "6";	
		$mapping_array['RACE']['WHITE'] 								= "1";	
		$mapping_array['RACE']['UNKNOWN'] 								= "19";
	
		$mapping_array['RACE2']['AMERICAN INDIAN OR ALASKA NATIVE']				= "1002-5";
		$mapping_array['RACE2']['ASIAN'] 										= "2028-9";
		$mapping_array['RACE2']['BLACK OR AFRICAN AMERICAN'] 					= "2054-5";
		$mapping_array['RACE2']['NATIVE HAWAIIAN OR OTHER PACIFIC ISLANDER'] 	= "2076-8";
		$mapping_array['RACE2']['WHITE']										= "2106-3";	
		$mapping_array['RACE2']['OTHER'] 										= "2131-1";	
		$mapping_array['RACE2']['UNKNOWN'] 										= "2131-1";
		$mapping_array['RACE2']['DECLINED TO SPECIFY'] 							= "2131-1";
		
		
		$mapping_array['ETHNICITY']['AFRICAN AMERICANS']				= "AFA";
		$mapping_array['ETHNICITY']['AFGANISTANI']						= "AFG";
		$mapping_array['ETHNICITY']['AFRICAN']							= "AFR";
		$mapping_array['ETHNICITY']['ALBANIAN']							= "ALB";
		$mapping_array['ETHNICITY']['AMERICAN']							= "AME";
		$mapping_array['ETHNICITY']['ARGENTINEAN']						= "ARG";
		$mapping_array['ETHNICITY']['ARMENIAN']							= "ARM";
		$mapping_array['ETHNICITY']['ASIAN INDIAN']						= "ASI";
		$mapping_array['ETHNICITY']['ASIAN']							= "ASN";
		$mapping_array['ETHNICITY']['ASSYRIAN']							= "ASR";
		$mapping_array['ETHNICITY']['BARBADIAN']						= "BAR";
		$mapping_array['ETHNICITY']['BANGLADESHI']						= "BGL";
		$mapping_array['ETHNICITY']['BHUTANESE']						= "BHU";
		$mapping_array['ETHNICITY']['BELIZE']							= "BLZ";
		$mapping_array['ETHNICITY']['BOLIVIAN']							= "BOL";
		$mapping_array['ETHNICITY']['BOSNIAN']							= "BOS";
		$mapping_array['ETHNICITY']['BRAZILIAN']						= "BRZ";
		$mapping_array['ETHNICITY']['BURMESE']							= "BUR";
		$mapping_array['ETHNICITY']['CENTRAL AMERICAN INDIAN']			= "CAI";
		$mapping_array['ETHNICITY']['CAMBODIAN']						= "CBD";
		$mapping_array['ETHNICITY']['CARIBBEAN ISLAND']					= "CBI";
		$mapping_array['ETHNICITY']['CHINESE']							= "CHI";
		$mapping_array['ETHNICITY']['CHILEAN']							= "CHL";
		$mapping_array['ETHNICITY']['CENTRAL AMERICAN']					= "CNA";
		$mapping_array['ETHNICITY']['COLUMBIAN']						= "COL";
		$mapping_array['ETHNICITY']['COSTA RICAN']						= "COS";
		$mapping_array['ETHNICITY']['CRIOLLO']							= "CRI";
		$mapping_array['ETHNICITY']['CROATIAN']							= "CRO";
		$mapping_array['ETHNICITY']['CUBAN']							= "CUB";
		$mapping_array['ETHNICITY']['CAPE VERDEAN']						= "CVD";
		$mapping_array['ETHNICITY']['DOMINICA ISLANDER']				= "DMI";
		$mapping_array['ETHNICITY']['DOMINICAN']						= "DOM";
		$mapping_array['ETHNICITY']['ECUADORIAN']						= "ECU";
		$mapping_array['ETHNICITY']['EGYPTIAN']							= "EGY";
		$mapping_array['ETHNICITY']['ENGLISH']							= "ENG";
		$mapping_array['ETHNICITY']['EASTERN EUROPEAN']					= "EST";
		$mapping_array['ETHNICITY']['ETHIOPIAN']						= "ETH";
		$mapping_array['ETHNICITY']['EUROPEAN']							= "EUR";
		$mapping_array['ETHNICITY']['FILIPINO']							= "FIL";
		$mapping_array['ETHNICITY']['FRENCH']							= "FRE";
		$mapping_array['ETHNICITY']['GERMAN']							= "GER";
		$mapping_array['ETHNICITY']['GHANA']							= "GHA";
		$mapping_array['ETHNICITY']['GREEK']							= "GRK";
		$mapping_array['ETHNICITY']['GUATEMALAN']						= "GTM";
		$mapping_array['ETHNICITY']['GUYANA']							= "GUY";
		$mapping_array['ETHNICITY']['HMONG']							= "HMO";
		$mapping_array['ETHNICITY']['HONDURAN']							= "HON";
		$mapping_array['ETHNICITY']['HAITIAN']							= "HTN";
		$mapping_array['ETHNICITY']['INDONESIAN']						= "IDO";
		$mapping_array['ETHNICITY']['IRANIAN']							= "IRA";
		$mapping_array['ETHNICITY']['IRISH']							= "IRE";
		$mapping_array['ETHNICITY']['IRAQI']							= "IRQ";
		$mapping_array['ETHNICITY']['ISRAELI']							= "ISR";
		$mapping_array['ETHNICITY']['ITALIAN']							= "ITA";
		$mapping_array['ETHNICITY']['IWO JIMAN']						= "IWO";
		$mapping_array['ETHNICITY']['JAMAICAN']							= "JAM";
		$mapping_array['ETHNICITY']['JAPANESE']							= "JAP";
		$mapping_array['ETHNICITY']['KOREAN']							= "KOR";
		$mapping_array['ETHNICITY']['LAOTIAN']							= "LAO";
		$mapping_array['ETHNICITY']['LEBANESE']							= "LEB";
		$mapping_array['ETHNICITY']['LIBERIAN']							= "LIB";
		$mapping_array['ETHNICITY']['MADAGASCAR']						= "MAD";
		$mapping_array['ETHNICITY']['MALAYSIAN']						= "MAL";
		$mapping_array['ETHNICITY']['MEXICAN,MEX AMER,CHICANO']			= "MEX";
		$mapping_array['ETHNICITY']['MIDDLE EASTERN']					= "MID";
		$mapping_array['ETHNICITY']['MALDIVIAN']						= "MLD";
		$mapping_array['ETHNICITY']['NEPALESE']							= "NEP";
		$mapping_array['ETHNICITY']['NICARAGUAN']						= "NIC";
		$mapping_array['ETHNICITY']['NIGERIAN']							= "NIG";
		$mapping_array['ETHNICITY']['OKINAWAN']							= "OKI";
		$mapping_array['ETHNICITY']['OTHER ETHNICITY']					= "OTH";
		$mapping_array['ETHNICITY']['PAKISTANI']						= "PAK";
		$mapping_array['ETHNICITY']['PALESTINIAN']						= "PAL";
		$mapping_array['ETHNICITY']['PANAMANIAN']						= "PAN";
		$mapping_array['ETHNICITY']['POLISH']							= "POL";
		$mapping_array['ETHNICITY']['PORTUGUESE']						= "POR";
		$mapping_array['ETHNICITY']['PUERTO RICAN']						= "PRC";
		$mapping_array['ETHNICITY']['PARAGUAYAN']						= "PRG";
		$mapping_array['ETHNICITY']['PERUVIAN']							= "PRV";
		$mapping_array['ETHNICITY']['RUSSIAN']							= "RUS";
		$mapping_array['ETHNICITY']['SOUTH AMERICAN INDIAN']			= "SAI";
		$mapping_array['ETHNICITY']['SALVADORAN']						= "SAL";
		$mapping_array['ETHNICITY']['SOUTH AMERICAN']					= "SAM";
		$mapping_array['ETHNICITY']['SCOTTISH']							= "SCO";
		$mapping_array['ETHNICITY']['SINGAPOREAN']						= "SIN";
		$mapping_array['ETHNICITY']['SIERRA LEONIAN']					= "SLE";
		$mapping_array['ETHNICITY']['SOMALI']							= "SOM";
		$mapping_array['ETHNICITY']['SPANISH']							= "SPA";
		$mapping_array['ETHNICITY']['SRI LANKAN']						= "SRI";
		$mapping_array['ETHNICITY']['SYRIAN']							= "SYR";
		$mapping_array['ETHNICITY']['TAIWANESE']						= "TAI";
		$mapping_array['ETHNICITY']['THAI']								= "THA";
		$mapping_array['ETHNICITY']['TOBAGOAN']							= "TOB";
		$mapping_array['ETHNICITY']['TRINIDADIAN']						= "TRN";
		$mapping_array['ETHNICITY']['UKRANIAN']							= "UKR";
		$mapping_array['ETHNICITY']['UNKNOWN/NOT SPECIFIED']			= "UNK";
		$mapping_array['ETHNICITY']['URUGUAYAN']						= "URG";
		$mapping_array['ETHNICITY']['VENEZUELAN']						= "VEN";
		$mapping_array['ETHNICITY']['VIETNAMESE']						= "VTN";
		$mapping_array['ETHNICITY']['WEST INDIAN']						= "WST";
			
		$mapping_array['LANGUAGE']['ALBANIAN']							= "ALB";
		$mapping_array['LANGUAGE']['AMHARIC']							= "AMH";
		$mapping_array['LANGUAGE']['ARABIC']							= "ARB";
		$mapping_array['LANGUAGE']['ARMENIAN']							= "ARM";
		$mapping_array['LANGUAGE']['ASL-SIGN LANGUAGE']					= "SIG";
		$mapping_array['LANGUAGE']['BENGALI']							= "BEN";
		$mapping_array['LANGUAGE']['BOSNIAN']							= "BOS";
		$mapping_array['LANGUAGE']['BULGARIAN']							= "BUL";
		$mapping_array['LANGUAGE']['BURMESE']							= "BUR";
		$mapping_array['LANGUAGE']['CAMBODIAN']							= "CAM";
		$mapping_array['LANGUAGE']['CANTONESE']							= "CHC";
		$mapping_array['LANGUAGE']['CAPE VERDEAN']						= "CVD";
		$mapping_array['LANGUAGE']['CZECH']								= "CZE";
		$mapping_array['LANGUAGE']['DUTCH']								= "DUT";
		$mapping_array['LANGUAGE']['ENGLISH']							= "ENG";
		$mapping_array['LANGUAGE']['ESTONIAN']							= "EST";
		$mapping_array['LANGUAGE']['FARSI']								= "FAR";
		$mapping_array['LANGUAGE']['FRENCH']							= "FRE";
		$mapping_array['LANGUAGE']['FUKANESE']							= "CHF";
		$mapping_array['LANGUAGE']['GAELIC']							= "GAE";
		$mapping_array['LANGUAGE']['GERMAN']							= "GER";
		$mapping_array['LANGUAGE']['GREEK']								= "GRK";
		$mapping_array['LANGUAGE']['GUJARATI']							= "GUJ";
		$mapping_array['LANGUAGE']['HAITIAN CREOLE']					= "CRE";
		$mapping_array['LANGUAGE']['HAKKA']								= "HAK";
		$mapping_array['LANGUAGE']['HEBREW']							= "HEB";
		$mapping_array['LANGUAGE']['HINDI']								= "IND";
		$mapping_array['LANGUAGE']['HUNGARIAN']							= "HUN";
		$mapping_array['LANGUAGE']['IBOL']								= "IBO";
		$mapping_array['LANGUAGE']['IRANIAN']							= "IRA";
		$mapping_array['LANGUAGE']['ITALIAN']							= "ITL";
		$mapping_array['LANGUAGE']['JAPANESE']							= "JAP";
		$mapping_array['LANGUAGE']['KHMER']								= "KHM";
		$mapping_array['LANGUAGE']['KOREAN']							= "KOR";
		$mapping_array['LANGUAGE']['LAOTIAN']							= "LAO";
		$mapping_array['LANGUAGE']['LATVIAN']							= "LAT";
		$mapping_array['LANGUAGE']['LEBANESE']							= "LEB";
		$mapping_array['LANGUAGE']['LITHUANIAN']						= "LIT";
		$mapping_array['LANGUAGE']['MALAY']								= "MAL";
		$mapping_array['LANGUAGE']['MANDARIN']							= "CHM";
		$mapping_array['LANGUAGE']['MARACI']							= "MAR";
		$mapping_array['LANGUAGE']['MARATHI']							= "MAT";
		$mapping_array['LANGUAGE']['OTHER']								= "ZZZ";
		$mapping_array['LANGUAGE']['POLISH']							= "POL";
		$mapping_array['LANGUAGE']['PORTUGUESE']						= "POR";
		$mapping_array['LANGUAGE']['RUMANIAN']							= "RUM";
		$mapping_array['LANGUAGE']['RUSSIAN']							= "RUS";
		$mapping_array['LANGUAGE']['SERBO-CROAT']						= "SEC";
		$mapping_array['LANGUAGE']['SLOVAK']							= "SLO";
		$mapping_array['LANGUAGE']['SOMALI']							= "SOM";
		$mapping_array['LANGUAGE']['SPANISH']							= "SPA";
		$mapping_array['LANGUAGE']['SWEDISH']							= "SWE";
		$mapping_array['LANGUAGE']['TAGALOG']							= "TAG";
		$mapping_array['LANGUAGE']['TAIWANESE']							= "TAI";
		$mapping_array['LANGUAGE']['THAI']								= "THA";
		$mapping_array['LANGUAGE']['TIGRINIAN']							= "TIG";
		$mapping_array['LANGUAGE']['TOISANESE']							= "CHT";
		$mapping_array['LANGUAGE']['TURKISH']							= "TUR";
		$mapping_array['LANGUAGE']['UKRAINIAN']							= "UKR";
		$mapping_array['LANGUAGE']['VIETNAMESE']						= "VIE";
		
		$mapping_array['EMP_STATUS']['EMPLOYED']						= "EMP";
		$mapping_array['EMP_STATUS']['HOMEMAKER']						= "HOM";
		$mapping_array['EMP_STATUS']['OTHER']							= "OTH";
		$mapping_array['EMP_STATUS']['RETIRED']							= "RET";
		$mapping_array['EMP_STATUS']['UNEMPLOYED']						= "UNEMP";
	
		$mapping_array['REL_GT1']['UNCLE']								= "22";
		$mapping_array['REL_GT1']['AUNT']								= "22";
		$mapping_array['REL_GT1']['AUNT/UNCLE']							= "22";
		$mapping_array['REL_GT1']['BROTHER/SISTER']						= "24";
		$mapping_array['REL_GT1']['CHILD:NO FIN RESPONSIBILITY']		= "6";
		$mapping_array['REL_GT1']['DEP CHILD:FIN RESPONSIBILITY']		= "4";
	//		$mapping_array['REL_GT1']['DAUGHTER']							= "";//mISSING at idx
		$mapping_array['REL_GT1']['DONOR LIVE']							= "13";
		$mapping_array['REL_GT1']['DONOR-DCEASED']						= "14";
		$mapping_array['REL_GT1']['EMPLOYEE']							= "10";
		$mapping_array['REL_GT1']['FATHER']								= "20";
		$mapping_array['REL_GT1']['FOSTER CHILD']						= "7";
		$mapping_array['REL_GT1']['GRAND CHILD']						= "15";
		$mapping_array['REL_GT1']['GRANDPARENT']						= "21";
		$mapping_array['REL_GT1']['HANDICAPPED DEPENDANT']				= "12";	
		$mapping_array['REL_GT1']['INJURED PLANTIFF']					= "17";	
		$mapping_array['REL_GT1']['INLAW']								= "25";	
		$mapping_array['REL_GT1']['LEGAL GUARDIAN']						= "23";	
		$mapping_array['REL_GT1']['MINOR DEPENDENT OF A DEPENDENT']		= "19";
		$mapping_array['REL_GT1']['MOTHER']								= "20";
		$mapping_array['REL_GT1']['NIECE\NEPHEW']						= "16";
		$mapping_array['REL_GT1']['OTHER']								= "5";
		$mapping_array['REL_GT1']['PARENT']								= "20";
	//		$mapping_array['REL_GT1']['POA']								= ""; //missing at idx
		$mapping_array['REL_GT1']['RELATIVE']							= "26";
		$mapping_array['REL_GT1']['SELF']								= "1";
		$mapping_array['REL_GT1']['SPONSORED DEPENDENT']				= "18";
		$mapping_array['REL_GT1']['SPOUSE']								= "2";
		$mapping_array['REL_GT1']['STEP CHILD']							= "8";
		$mapping_array['REL_GT1']['STUDENT']							= "3";		
		$mapping_array['REL_GT1']['UNKNOWN']							= "11";
		$mapping_array['REL_GT1']['WARD OF THE COURT']					= "9";
	
		$mapping_array['REL_NK1']['AUNT/UNCLE']							= "22";
		$mapping_array['REL_NK1']['AUNT']								= "22";
		$mapping_array['REL_NK1']['UNCLE']								= "22";
		$mapping_array['REL_NK1']['BROTHER/SISTER']						= "24";
		$mapping_array['REL_NK1']['BROTHER']							= "24";
		$mapping_array['REL_NK1']['SISTER']								= "24";
		$mapping_array['REL_NK1']['INLAW']								= "25";
		$mapping_array['REL_NK1']['LEGAL GUARDIAN']						= "23";
		$mapping_array['REL_NK1']['RELATIVE']							= "26";
		$mapping_array['REL_NK1']['CHILD:NO FIN RESPONSIBILITY']		= "6";
		$mapping_array['REL_NK1']['DEP CHILD:FIN RESPONSIBILITY']		= "4";
		$mapping_array['REL_NK1']['FOSTER CHILD']						= "7";
		$mapping_array['REL_NK1']['GRAND CHILD']						= "15";
		$mapping_array['REL_NK1']['GRANDPARENT']						= "21";
		$mapping_array['REL_NK1']['GRANDFATHER']						= "21";
		$mapping_array['REL_NK1']['GRANDMOTHER']						= "21";
		$mapping_array['REL_NK1']['NIECE\NEPHEW']						= "16";
		$mapping_array['REL_NK1']['OTHER']								= "5";
		$mapping_array['REL_NK1']['PARENT']								= "20";
		$mapping_array['REL_NK1']['MOTHER']								= "20";
		$mapping_array['REL_NK1']['FATHER']								= "20";
		$mapping_array['REL_NK1']['SPOUSE']								= "2";
		$mapping_array['REL_NK1']['HUSBAND']							= "2";
		$mapping_array['REL_NK1']['WIFE']								= "2";
		$mapping_array['REL_NK1']['STEP CHILD']							= "8";
		$mapping_array['REL_NK1']['UNKNOWN']							= "11";	
		
		$mapping_array['REL_IN1']['BROTHER/SISTER']						= "24";
		$mapping_array['REL_IN1']['CHILD:NO FIN RESPONSIBILITY']		= "6";
		$mapping_array['REL_IN1']['DEP CHILD:FIN RESPONSIBILITY']		= "4";
		$mapping_array['REL_IN1']['DONOR LIVE']							= "13";
		$mapping_array['REL_IN1']['DONOR-DECEASED']						= "14";
		$mapping_array['REL_IN1']['EMPLOYEE']							= "10";
		$mapping_array['REL_IN1']['FOSTER CHILD']						= "7";
		$mapping_array['REL_IN1']['GRAND CHILD']						= "15";
		$mapping_array['REL_IN1']['GRANDPARENT']						= "21";
		$mapping_array['REL_IN1']['HANDICAPPED DEPENDANT']				= "12";
		$mapping_array['REL_IN1']['INJURED PLANTIFF']					= "17";
		$mapping_array['REL_IN1']['MINOR DEPENDENT OF A DEPENDENT']		= "19";
		$mapping_array['REL_IN1']['NIECE\NEPHEW']						= "16";
		$mapping_array['REL_IN1']['OTHER']								= "5";
		$mapping_array['REL_IN1']['PARENT']								= "20";
		$mapping_array['REL_IN1']['SELF']								= "1";
		$mapping_array['REL_IN1']['SPONSORED DEPENDENT']				= "18";
		$mapping_array['REL_IN1']['SPOUSE']								= "2";
		$mapping_array['REL_IN1']['STEP CHILD']							= "8";
		$mapping_array['REL_IN1']['STUDENT']							= "3";
		$mapping_array['REL_IN1']['UNKNOWN']							= "11";
		$mapping_array['REL_IN1']['WARD OF THE COURT']					= "9";
		
		$mapping_array['ACNT_ZIN']['AUTO ACCIDENT']						= "A";
		$mapping_array['ACNT_ZIN']['ACCIDENT FOLLOW UP']				= "B";
		$mapping_array['ACNT_ZIN']['CRIME VICTIM']						= "C";
		$mapping_array['ACNT_ZIN']['MEDICAL VISIT']						= "D";
		$mapping_array['ACNT_ZIN']['MEDICAL FOLLOW UP']					= "E";
		$mapping_array['ACNT_ZIN']['DIAG.TESTING']						= "F";
		$mapping_array['ACNT_ZIN']['SURGERY']							= "G";
		$mapping_array['ACNT_ZIN']['HOME ACCIDENT']						= "H";
		$mapping_array['ACNT_ZIN']['PREADMISSION TESTING']				= "I";
		$mapping_array['ACNT_ZIN']['JOB RELATED ACCIDENT']				= "J";
		$mapping_array['ACNT_ZIN']['CARDIAC REHAB']						= "K";
		$mapping_array['ACNT_ZIN']['PHYSICAL THERAPY']					= "L";
		$mapping_array['ACNT_ZIN']['OCCUPATIONAL THERAPY']				= "M";
		$mapping_array['ACNT_ZIN']['NO FAULT INS INVOLVED']				= "N";
		$mapping_array['ACNT_ZIN']['OTHER']								= "O";
		$mapping_array['ACNT_ZIN']['SPEECH THERAPY']					= "P";
		$mapping_array['ACNT_ZIN']['OTHER ACCIDENT']					= "S";
		$mapping_array['ACNT_ZIN']['TORT LIABILITY ACCIDENT']			= "T";
		$mapping_array['ACNT_ZIN']['NEWBORN BABY']						= "W";
	
		$mapping_array['DECEASED_ZP1']['DECEASED']						= "Y";
		$mapping_array['DECEASED_ZP1']['ACTIVE']						= "N";
		$mapping_array['DECEASED_ZP1']['INACTIVE']						= "N";
		
		return $mapping_array[$section][$key];		
	}
}
/*------getting all procedure (surgerycenter)------*/
$arr_procs_mapping	= array();
$procs_query = "SELECT procedureId, name, code FROM procedures";
$procs_res	 = imw_query($procs_query);
if($procs_res && imw_num_rows($procs_res)>0){
	while($procs_rs = imw_fetch_assoc($procs_res)){
		$procs_rs_id							 = $procs_rs['procedureId'];
		$arr_procs_mapping[$procs_rs_id]['name'] = $procs_rs['name'];
		$arr_procs_mapping[$procs_rs_id]['cpt']  = $procs_rs['code'];
	}
}

/*---getting patient location (surgerycenter location)---*/
$location_result = imw_query("SELECT npi,name FROM surgerycenter LIMIT 0,1");
$location_rs	 = imw_fetch_assoc($location_result);
$patient_location= '^^^^'.preg_replace("/[^0-9]/","",$location_rs['npi']).'^^^'.$location_rs['name'];

/*---getting diagnosis codes------*/
$arr_diags_mapping = array();
$diags_query = "SELECT diag_id,diag_code FROM diagnosis_tbl";
$diags_result= imw_query($diags_query);
if($diags_result && imw_num_rows($diags_result)>0){
	while($diags_rs = imw_fetch_assoc($diags_result)){
		$arr_diags_mapping[$diags_rs['diag_id']] = explode(', ',$diags_rs['diag_code']);
	}
}

//GETTING PATIENT DEMOGRAPHICS FROM iASC
if(isset($unfinalize_confirmation_id) && $unfinalize_confirmation_id!=''){$SC_pConfId = $unfinalize_confirmation_id;}
else{$SC_pConfId = $pConfId;}



$HL7MSG_TEXT = '';
$HL7MSG_TEXT_PRL = '';
$HL7MSG_TEXT_ADT = '';
$HL7MSG_TEXT_ADT_PRL = '';
$HL7MSG_ANES_TEXT = '';
$EVN_Segment = '';
$HL7MSG_ERROR = array();
//MSH SEGMENT (MESSAGE HEADER)

if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye','southwest')))
{
	imw_close($link);
	include('connect_imwemr.php');
	$newMsgId		= newMessageUniqueId();
	
	imw_close($link_imwemr);
	include('common/conDb.php');
}
else
	$newMsgId		= newMessageUniqueId();

if(strtolower($GLOBALS["LOCAL_SERVER"])=='keywhitman'){
	$HL7MSG_TEXT 	.= 'MSH|^~\&|IMW|IMW_KEYWHITMAN|MEDEVOLVE ECENO|MEDEVOLVE ECENO KEYWHITMAN|'.date('YmdHis').'||DFT^P03|'.$newMsgId.'|P|2.3.1||||||ASCII||||
';
}
elseif(in_array(strtolower($GLOBALS["LOCAL_SERVER"]), array('gnysx','mackool'))){
	if(strtolower($GLOBALS["LOCAL_SERVER"])==='mackool'){
		$HL7MSG_TEXT 	.= 'MSH|^~\&|IMW|IMW_'.strtoupper($GLOBALS["LOCAL_SERVER"]).'|MACKOOL|MACKOOL|'.date('YmdHis').'||DFT^P03|'.$newMsgId.'|P|2.3.1||||||ASCII|||
';
		$HL7MSG_ANES_TEXT 	.= 'MSH|^~\&|IMW|IMW_ANESTHESIA_MSG|MACKOOL|MACKOOL|'.date('YmdHis').'||DFT^P03|'.$newMsgId.'|P|2.3.1||||||ASCII|||
';
	}
	else{
		$HL7MSG_TEXT 	.= 'MSH|^~\&|IMW|IMW_FACILITY_MSG|Ntierprise|Ambulatory Surgery Center of Greater NY|'.date('YmdHis').'||DFT^P03|'.$newMsgId.'|P|2.3.1||||||ASCII|||
';
		$HL7MSG_ANES_TEXT 	.= 'MSH|^~\&|IMW|IMW_ANESTHESIA_MSG|Ntierprise|Ambulatory Surgery Center of Greater NY|'.date('YmdHis').'||DFT^P03|'.$newMsgId.'|P|2.3.1||||||ASCII|||
';
	}

	$EVN_Segment 	.= 'EVN|P03|'.date('YmdHis').'|||'.$_SESSION['loginUserName'].'||
';
	$HL7MSG_TEXT 	.= $EVN_Segment;
	$HL7MSG_ANES_TEXT 	.= $EVN_Segment;
}
else if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham','palisades','islandeye','valleyeye','southwest'))){
	$MSG_ORIGIN = get_confId_fac_name($SC_pConfId);
	
	if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('waltham'))){
		$HL7MSG_TEXT 	.= 'MSH|^~\&|IMW|IMW_'.strtoupper($MSG_ORIGIN['fac_city']).'|TEST|TEST|'.date('YmdHis').'||DFT^P03|'.$newMsgId.'|P|2.3.1||||||ASCII|||
';	
	}else if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye'))){
		$HL7MSG_TEXT 	.= 'MSH|^~\&|imwemr|IES^0002|Nextgen|IES|'.date('YmdHis').'||DFT^P03|'.$newMsgId.'d|P|2.3.1||||||ASCII|||
';
		$IES_ADT_MSH 	= 'MSH|^~\&|imwemr|IES^0002|Nextgen|IES|'.date('YmdHis').'||ADT^A08|'.$newMsgId.'a|P|2.4||||||ASCII|||
';
		$IES_ADT_MSH 	.= 'EVN|A08|'.date('YmdHis').'|||'.$_SESSION['loginUserName'].'|||||
';	
	}else if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('valleyeye'))){
		$HL7MSG_TEXT 	.= 'MSH|^~\&|imwemr|0011^VALLEYEYE|Nextgen|VALLEYEYE|'.date('YmdHis').'||DFT^P03|'.$newMsgId.'d|P|2.3.1
';
		$IES_ADT_MSH 	= 'MSH|^~\&|imwemr|0011^VALLEYEYE|Nextgen|VALLEYEYE|'.date('YmdHis').'||ADT^A08|'.$newMsgId.'a|P|2.4
';
		$IES_ADT_MSH 	.= 'EVN|A08|'.date('YmdHis').'|||'.$_SESSION['loginUserName'].'|||||
';	
	}else{
		$HL7MSG_TEXT 	.= 'MSH|^~\&|IMW|IMW_'.strtoupper($GLOBALS["LOCAL_SERVER"]).'|TEST|TEST|'.date('YmdHis').'||DFT^P03|'.$newMsgId.'|P|2.3.1||||||ASCII|||
';
	}
	$HL7MSG_TEXT 	.= 'EVN|P03|'.date('YmdHis').'|||'.$_SESSION['loginUserName'].'||
';
	if( strtolower($GLOBALS["LOCAL_SERVER"]) === 'waltham' )
	{
		$HL7MSG_TEXT_PRL 	.= 'EVN|P03|'.date('YmdHis').'|||'.$_SESSION['loginUserName'].'||
';
	}
}

if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham','palisades','mackool'))){
	$HL7MSG_TEXT_ADT 	.= 'EVN|A08|'.date('YmdHis').'|||'.$_SESSION['loginUserName'].'||
';
	if( strtolower($GLOBALS["LOCAL_SERVER"]) === 'waltham' )
	{
		$HL7MSG_TEXT_ADT_PRL	.= 'EVN|A08|'.date('YmdHis').'|||'.$_SESSION['loginUserName'].'||
';
	}
}

/*List anesthesia cpt ids*/
$anesthesia_cpt = array();
if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]), array('gnysx','mackool'))){
	$anesthesia_cpt_q = "SELECT `cpt_id` FROM `superbill_tbl` WHERE `confirmation_id`='$SC_pConfId' AND `bill_user_type`=1";
	if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]), array('mackool'))){
		$anesthesia_cpt_q = "SELECT `cpt_id` FROM `superbill_tbl` WHERE `confirmation_id`='$SC_pConfId' AND `bill_user_type` IN ('1','2')";
	}
	$anesthesia_cpt_res = imw_query($anesthesia_cpt_q);
	if($anesthesia_cpt_res && imw_num_rows($anesthesia_cpt_res)>0){
		while($anestheisa_row = imw_fetch_assoc($anesthesia_cpt_res)){
			array_push($anesthesia_cpt, $anestheisa_row['cpt_id']);
		}
	}
}
/*End List anesthesia cpt ids*/

/*LIST All procedured ids with the modifiers and user types - PRL*/
if( strtolower($GLOBALS["LOCAL_SERVER"]) == 'waltham' )
{
	$cptModifiers = array();
	$proc_mod_sql = "SELECT `cpt_id`, `bill_user_type`, TRIM(BOTH '~' FROM CONCAT(`modifier1`, '~', `modifier2`, '~', `modifier3`)) AS 'modifiers' FROM `superbill_tbl` WHERE `confirmation_id`='$SC_pConfId' AND deleted=0";
	$proc_mod_resp = imw_query($proc_mod_sql);
	if( $proc_mod_resp && imw_num_rows($proc_mod_resp) > 0)
	{
		while( $mod_row = imw_fetch_assoc($proc_mod_resp) )
		{
			$cptModifiers[$mod_row['bill_user_type']][$mod_row['cpt_id']] = $mod_row['modifiers'];
		}
	}
}

$SC_patient_q = "SELECT sc_pdt.imwPatientId AS patient_id, sc_pdt.patient_id AS sc_patient_id, sc_pc.anesthesiologist_id, sc_pc.ascId AS iASCID, sc_pc.dos as date_of_service FROM patient_data_tbl sc_pdt 
				 JOIN patientconfirmation sc_pc ON (sc_pc.patientId = sc_pdt.patient_id) WHERE sc_pc.patientConfirmationId = '$SC_pConfId'";
$SC_patient_res = imw_query($SC_patient_q);
if($SC_patient_res && imw_num_rows($SC_patient_res)==1){
	$SC_patient_rs 			= imw_fetch_assoc($SC_patient_res);
	$iASC_patient_id 		= $SC_patient_rs['patient_id'];
	$SC_patient_id			= $SC_patient_rs['sc_patient_id'];
	$anesthesiologist_id 	= $SC_patient_rs['anesthesiologist_id'];
	$iASCID 				= $SC_patient_rs['iASCID'];
	$date_of_service		= $SC_patient_rs['date_of_service'];
	
	$pid_sql = "";
	if( in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('keywhitman','gnysx','mackool')) ){
		$pid_sql .= "select '1' AS set_id, id AS iMWID, 
				CONCAT(External_MRN_2,'^^^^PT~',External_MRN_1,'^^^^PI') AS pt_identifier_list, ";
		if(strtolower($GLOBALS["LOCAL_SERVER"])==='keywhitman')
			$pid_sql .= 'External_MRN_1 AS Alternate_id, ';
		elseif(strtolower($GLOBALS["LOCAL_SERVER"])==='gnysx')
			$pid_sql .= 'External_MRN_2 as External_id, ';
		elseif(strtolower($GLOBALS["LOCAL_SERVER"])==='mackool')
			$pid_sql .= "'' as External_id, ";
	}else if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham','palisades','islandeye','valleyeye','southwest'))){
		$pid_sql = "select '0001' AS set_id, 
				'' AS iMWID, 
				id AS pt_identifier_list, 
				'' AS Alternate_id, ";
		if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye','southwest'))){
			$pid_sql = "select '0001' AS set_id, 
				id AS iMWID, 
				External_MRN_2 AS pt_identifier_list, 
				'' AS Alternate_id, ";
		}
	}
	$pid_sql .=  " CONCAT(lname,'^',fname,'^',SUBSTR(mname,1,1)) AS ptName, 
				'' AS Mother_Maiden_Name, 
				DATE_FORMAT(DOB,'%Y%m%d') AS DOB, 
				UPPER(SUBSTR(sex,1,1)) AS sex, 
				'' AS Alias, 
				race ";
	
	$pid_where = "";
	if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]), array('keywhitman')))
		$pid_where = " AND External_MRN_2 != '' AND External_MRN_1 != ''";
	
	if( in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('keywhitman','gnysx')) ){
		$pid_sql .= ", '' AS eleventh, 
				'' AS twelve, 
				'' AS thirteen, 
				'' AS forteen, 
				'' AS fifteen, 
				'' AS sixteen,
				'' AS seventeen, 
				External_MRN_2 AS AccountNumber ";
	}else{
		if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye'))){
			$pid_sql .= " 
			, CONCAT(street,'^',street2,'^',city,'^',state,'^',postal_code,zip_ext,'^',country_code,'^^^',county) AS Address, 
			'' AS country_code,
			CONCAT('^PRN^PH^^^',SUBSTRING(TRIM(REPLACE(phone_home,'-','')),1,3),'^',SUBSTRING(TRIM(REPLACE(phone_home,'-','')),4),'^^Cell','~','^NET^Internet^',email,'~','^PRN^CP^^^',SUBSTRING(TRIM(REPLACE(phone_cell,'-','')),1,3),'^',SUBSTRING(TRIM(REPLACE(phone_cell,'-','')),4)) AS phone_home, ";
		}else{
			$pid_sql .= " 
			, CONCAT(street,'^',street2,'^',city,'^',state,'^',postal_code,zip_ext) AS Address, 
			'' AS country_code,
			REPLACE(phone_home,'-','') AS phone_home, ";
		}
		/*Different Phone format for PRL*/
		if( strtolower($GLOBALS["LOCAL_SERVER"]) == 'waltham' )
		{
			$pid_sql .= "
						CONCAT( 
							IF( TRIM(REPLACE(phone_home,'-','')) != '', TRIM(REPLACE(phone_home,'-','')), '' ),
							'^^PH^',
							IF( TRIM(REPLACE(email,'-','')) != '', TRIM(REPLACE(email,'-','')), '' ),
							'^^~',
							IF( TRIM(REPLACE(phone_cell,'-','')) != '', TRIM(REPLACE(phone_cell,'-','')), '' ),
							'^^CP'
						)  AS phone_home_prl, ";
		}
		
		if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye'))){
			$sql .= " 
				CONCAT('^WPN^PH^^^',SUBSTRING(TRIM(REPLACE(phone_biz,'-','')),1,3),'^',SUBSTRING(TRIM(REPLACE(phone_biz,'-','')),4)) AS phone_biz, ";
		}else{
			$pid_sql .= "
				REPLACE(phone_biz,'-','') AS phone_biz, ";
		}
		$pid_sql .= "
			language,
			UPPER(TRIM(status)) AS status, 
			'' AS Religion, 
			'' AS Account_Number, 
			REPLACE(ss,'-','') AS ss, 
			driving_licence, 
			'' AS Mother_Identifier, 
			ethnicity ";
		}
	$pid_sql .= "FROM patient_data 
				WHERE id='".$iASC_patient_id."' 
				AND (DOB != '0000-00-00' AND DOB != '' AND sex != ''".$pid_where.") 
				LIMIT 0,1";
	include(dirname(__FILE__)."/connect_imwemr.php");
	$pid_res = imw_query($pid_sql);
	$PID_Segment = '';
	if($pid_res && imw_num_rows($pid_res)==1){
		$pid_rs = imw_fetch_assoc($pid_res);
		//PID SEGMENT (PATIENT IDENTIFICATION)
		
		if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]), array('gnysx'))){
			$PID_Segment = 'PID|'.$pid_rs['set_id'].'|'.$pid_rs['External_id'].'|'.$pid_rs['pt_identifier_list'].'|'.$pid_rs['iMWID'].'|'.$pid_rs['ptName'].'||'.$pid_rs['DOB'].'|'.$pid_rs['sex'].'||||||||||'.$pid_rs['AccountNumber'].'|
';
			$HL7MSG_TEXT 	.= $PID_Segment;
			$HL7MSG_ANES_TEXT	.= $PID_Segment;
		}
		else{
			$PID_Segment	= 'PID|'.$pid_rs['set_id'].'|'.$pid_rs['iMWID'].'|'.$pid_rs['pt_identifier_list'].'|'.$pid_rs['Alternate_id'].'|'.$pid_rs['ptName'].'||'.$pid_rs['DOB'].'|'.$pid_rs['sex'].'||||||||||'.$pid_rs['AccountNumber'].'|
';
			if(strtolower($GLOBALS["LOCAL_SERVER"])==='mackool'){
				$HL7MSG_TEXT .= 'PID|'.implode('|',$pid_rs).'
';
				$HL7MSG_ANES_TEXT .= 'PID|'.implode('|',$pid_rs).'
';
			}
			else
				$HL7MSG_TEXT .= $PID_Segment;

			if( strtolower($GLOBALS["LOCAL_SERVER"]) == 'waltham' )
			{
				$PID_Segment_PRL	= 'PID|'.$pid_rs['set_id'].'|'.$pid_rs['iMWID'].'|'.$SC_patient_id.'|'.$pid_rs['Alternate_id'].'|'.$pid_rs['ptName'].'||'.$pid_rs['DOB'].'|'.$pid_rs['sex'].'||||||||||'.$pid_rs['AccountNumber'].'|
';
				$HL7MSG_TEXT_PRL .= $PID_Segment_PRL;
			}
		}

		if( strtolower($GLOBALS["LOCAL_SERVER"]) == 'waltham' )
		{
			$pid_rs_prl = $pid_rs;
			unset($pid_rs['phone_home_prl'], $pid_rs_prl['phone_home']);

			/*Send iOlink patient instead of Patient Id from iDoc/iASC*/
			$pid_rs_prl['pt_identifier_list'] = $SC_patient_id;

			$HL7MSG_TEXT_ADT_PRL .= 'PID|'.implode('|',$pid_rs_prl).'
';
			unset($pid_rs_prl);
		}

		$HL7MSG_TEXT_ADT .= 'PID|'.implode('|',$pid_rs).'
';
	}else{
		$HL7MSG_ERROR[] = 'Error in fetching iASC patient demographics. Data not sufficient for iDoc Pt.ID '.$iASC_patient_id.'.';
	}

	/*Referring Phyician - to be used in PV1 segment of DFT - PRL*/
	$referring_physician = '';
	if( strtolower($GLOBALS["LOCAL_SERVER"]) == 'waltham' )
	{
		$sql_ref_phy = "SELECT CONCAT(u.NPI, '^', u.LastName, '^', u.FirstName, '^', u.MiddleName) AS 'ref_phy' FROM patient_data pd INNER JOIN refferphysician u ON(pd.primary_care_id=u.physician_Reffer_id) WHERE pd.id='".$iASC_patient_id."' AND DOB != '0000-00-00' AND DOB != '' AND sex != ''";
		$resp_ref_php = imw_query($sql_ref_phy);
		if( $resp_ref_php && imw_num_rows($resp_ref_php) > 0)
		{
			$referring_physician = imw_fetch_assoc($resp_ref_php);
			$referring_physician = $referring_physician['ref_phy'];
		}
	}


	include(dirname(__FILE__)."/common/conDb.php"); 
	
	//FT1 SEGMENT (FINANCIAL TRANSCTION) {IT MAY REPEAT AS PER CPTs}
	$provider_id_field = "npi";
	if( in_array(strtolower($GLOBALS["LOCAL_SERVER"]), array('keywhitman','gnysx')) ){
		$provider_id_field = "external_id";
	}
	elseif( strtolower($GLOBALS["LOCAL_SERVER"]) == 'mackool' ){
		$provider_id_field = "usersId";
	}
	
	$ft1_q_left2 = ' LEFT ';
	if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham','palisades'))){
		$ft1_q_left2 = '';
	}
	
	$ft1_q = "SELECT dcs.dischargeSummarySheetId AS encounter_id, dcs.procedures_code, REPLACE(dcs.icd10_code,'@@',',') AS icd10_code, dcs.icd10_id, dcs.summarySaveDateTime, 
				CONCAT(u.".$provider_id_field.",'^',u.lname,'^',u.fname,'^',u.mname) AS surgeon_npi_name, 
				CONCAT(u2.".$provider_id_field.",'^',u2.lname,'^',u2.fname,'^',u2.mname) AS anes_npi_name, 
				u.external_id as surgeon_external_id 
				FROM dischargesummarysheet dcs 
				JOIN users u ON (u.usersId = dcs.surgeonId) 
				".$ft1_q_left2."JOIN users u2 ON (u2.usersId = '".$anesthesiologist_id."')
				WHERE dcs.confirmation_id = '".$SC_pConfId."' LIMIT 0,1";
	$ft1_res = imw_query($ft1_q);
	if($ft1_res && imw_num_rows($ft1_res)>0){
		$ft_i = 1;
		$ft1_rs = imw_fetch_assoc($ft1_res);
		$all_procs = explode(',',$ft1_rs['procedures_code']);
		$all_diags = str_replace(',','^',$ft1_rs['icd10_code']);
		if(strtolower($GLOBALS["LOCAL_SERVER"])=='keywhitman'){
			$all_diags = str_replace(',','~',$ft1_rs['icd10_code']);
		}
		elseif(strtolower($GLOBALS["LOCAL_SERVER"])=='waltham'){
			$all_diags_prl = str_replace(',','~',$ft1_rs['icd10_code']);
		}
		$all_diags_ids = $ft1_rs['icd10_id'];
		$arr_all_diags = explode(',',$ft1_rs['icd10_code']);
		//REPEAT FT1 SEGMENT AS MANY CPT AVAILABEL
		$facility_location = "";
		if(strtolower($GLOBALS["LOCAL_SERVER"])=='keywhitman'){
			$facility_location 	= "4^FREEHOLD";
			$iASCID				= ''; //FT1.2 empty for kwec.
		}

		/*Name of the Faiclity/Location - PRL*/
		$location = '';
		if( strtolower($GLOBALS["LOCAL_SERVER"]) == 'waltham' )
		{
			$location = get_confId_fac_name($SC_pConfId);
			$location = '^^^'.$location['fac_name'];

			$PV1_SEGMENT_PRL = 'PV1|0001||'.$location.'||||'.$ft1_rs['surgeon_npi_name'].'|'.$referring_physician.'|||||||||||'.$SC_pConfId.'|||||||||||||||||||||||||
';
		}
		
		$PV1_SEGMENT = 'PV1|0001||||||'.$ft1_rs['surgeon_npi_name'].'||||||||||||'.$SC_pConfId.'|||||||||||||||||||||||||
';
		if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]), array('gnysx','mackool')))
			$PV1_SEGMENT = 'PV1|1||||||'.$ft1_rs['surgeon_npi_name'].'||||||||||||'.$SC_pConfId.'|||||||||||||||||||||||||
';
		if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye'))){
			/*Get iDoc Appointment ID*/
			$sql = 'SELECT `appt_id` FROM `stub_tbl` WHERE `patient_confirmation_id`='.$SC_pConfId;
			$resp = imw_query($sql);
			$imwApptID = '0';
			if($resp && imw_num_rows($resp) > 0)
			{
				$imwApptID = imw_fetch_assoc($resp);
				$imwApptID = $imwApptID['appt_id'];
			}
			$pv1_7_NPI = '1629152053';
			if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('valleyeye'))){
				$pv1_7_NPI = '1437104718';
			}
			$PV1_SEGMENT = 'PV1|1||||||'.$pv1_7_NPI.'^||||||||||||'.$imwApptID.'|||||||||||||||||||||||||
';
			unset($sql, $resp, $imwApptID,$pv1_7_NPI);
		}else if( strtolower($GLOBALS["LOCAL_SERVER"]) == 'southwest' ){
			$PV1_SEGMENT = 'PV1|1||16||||'.$ft1_rs['surgeon_external_id'].'||||||||||||'.$SC_pConfId.'|||||||||||||||||||||||||
';
		}
		
		if( !in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('keywhitman')) ){
			$HL7MSG_TEXT	.= $PV1_SEGMENT;
			$HL7MSG_ANES_TEXT	.= $PV1_SEGMENT;
			if(!in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye'))){
				$HL7MSG_TEXT_ADT .= $PV1_SEGMENT;
			}
			if( strtolower($GLOBALS["LOCAL_SERVER"]) === 'waltham' )
			{
				$HL7MSG_TEXT_PRL	.= $PV1_SEGMENT_PRL;
				$HL7MSG_TEXT_ADT_PRL .= $PV1_SEGMENT_PRL;
			}
		}

		if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham','palisades','mackool'))){
			//GT1
			$gt1_q = "SELECT 
						'0001' AS set_id, 
						'' AS Guarantor_Number, 
						CONCAT(lname,'^',fname,'^',mname) AS GtName, 
						'' AS Spouse_Name, 
						CONCAT(address,'^',address2,'^',city,'^',state,'^',zip,zip_ext) AS address, 
						REPLACE(home_ph,'-','') AS phone_home, 
						REPLACE(work_ph,'-','') AS phone_biz, 
						DATE_FORMAT(dob,'%Y%m%d') AS dob, 
						UPPER(SUBSTR(sex,1,1)) AS sex, 
						'' AS Guarantor_Type, 
						relation, 
						'' AS ss, 
						'' AS Begin_Date, 
						'' AS End_Date, 
						'' AS Priority, 
						'UNKNOWN' AS Employer_Name,
					    CONCAT('','^','','^','','^','','^','') AS Employer_address 
					FROM resp_party WHERE patient_id = '".$iASC_patient_id."'";
			include(dirname(__FILE__)."/connect_imwemr.php");
			$gt1_res = imw_query($gt1_q);
			if($gt1_res && imw_num_rows($gt1_res)>0){
				$gt1_rs = imw_fetch_assoc($gt1_res);
				$HL7MSG_TEXT_ADT .= 'GT1|'.implode('|',$gt1_rs).'
';
				if( strtolower($GLOBALS["LOCAL_SERVER"]) === 'waltham' )
				{
					$HL7MSG_TEXT_ADT_PRL .= 'GT1|'.implode('|',$gt1_rs).'
';
				}
			}else{
				$gt1_q = "SELECT 
						'0001' AS set_id, 
						'' AS Guarantor_Number, 
						CONCAT(lname,'^',fname,'^',mname) AS GtName, 
						'' AS Spouse_Name, 
						CONCAT(street,'^',street2,'^',city,'^',state,'^',postal_code,zip_ext) AS address, 
						REPLACE(phone_home,'-','') AS home_ph, 
						REPLACE(phone_biz,'-','') AS work_ph, 
						DATE_FORMAT(DOB,'%Y%m%d') AS DOB, 
						UPPER(SUBSTR(sex,1,1)) AS sex, 
						'S' AS Guarantor_Type, 
						'' AS relation 						 
					FROM patient_data WHERE id = '".$iASC_patient_id."'";
				$gt1_res = imw_query($gt1_q);
				if($gt1_res && imw_num_rows($gt1_res)>0){
					$gt1_rs = imw_fetch_assoc($gt1_res);
					$HL7MSG_TEXT_ADT .= 'GT1|'.implode('|',$gt1_rs).'
';
					if( strtolower($GLOBALS["LOCAL_SERVER"]) === 'waltham' )
					{
						$HL7MSG_TEXT_ADT_PRL .= 'GT1|'.implode('|',$gt1_rs).'
';
					}
				}
			}
			include(dirname(__FILE__)."/common/conDb.php"); 		
			
			if( strtolower($GLOBALS["LOCAL_SERVER"]) !== 'mackool' ){
				//OBX for PPMC
				$local_anes_q = "SELECT *, date_format(signAnesthesia1DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia1DateTimeFormat , date_format(signAnesthesia2DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia2DateTimeFormat , date_format(signAnesthesia3DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia3DateTimeFormat FROM `localanesthesiarecord` WHERE `confirmation_id` ='".$SC_pConfId."' LIMIT 0,1";	
				$local_anes_res = imw_query($local_anes_q);
				if($local_anes_res && imw_num_rows($local_anes_res)==1){
					$OBX_TEXT = '';
					$local_anes_rs 			= imw_fetch_assoc($local_anes_res);

					$pre_op_evaluation 		= $local_anes_rs['evaluation2'];
					$pre_op_stable_cardio 	= $local_anes_rs['stableCardiPlumFunction'];
					$planAnesthesia 		= $local_anes_rs['planAnesthesia']; //Yes
					$asaPhysicalStatus		= $local_anes_rs['asaPhysicalStatus']; //1//2
					$routineMonitorApplied	= $local_anes_rs['routineMonitorApplied']; //Yes.
					$ivCatheter				= $local_anes_rs['ivCatheter'];
					$hand_right				= $local_anes_rs['hand_right'];
					$hand_left				= $local_anes_rs['hand_left'];
					$wrist_right			= $local_anes_rs['wrist_right'];
					$wrist_left				= $local_anes_rs['wrist_left'];
					$arm_right				= $local_anes_rs['arm_right'];
					$arm_left				= $local_anes_rs['arm_left'];
					$anti_right				= $local_anes_rs['anti_right'];
					$anti_left				= $local_anes_rs['anti_left'];
					$ivCatheterOther		= $local_anes_rs['ivCatheterOther'];

					$TopicalBlock1Block2	= $local_anes_rs['TopicalBlock1Block2'];
					$start_time_ar 			= explode(':',$local_anes_rs['startTime']);
					if($start_time_ar[0]=='24')$start_time_ar[0] = '12'; 				
					$start_time 			= implode(':',$start_time_ar);

					$stop_time_ar 			= explode(':',$local_anes_rs['stopTime']);;
					if($stop_time_ar[0]=='24')$stop_time_ar[0] = '12'; 
					$stop_time				= implode(':',$stop_time_ar);

					$newStartTime1 			= $local_anes_rs['newStartTime1'];
					$newStopTime1 			= $local_anes_rs['newStopTime1'];
					$newStartTime2  		= $local_anes_rs['newStartTime2 '];
					$newStopTime2 			= $local_anes_rs['newStopTime2'];
					$newStartTime3  		= $local_anes_rs['newStartTime3 '];
					$newStopTime3 			= $local_anes_rs['newStopTime3'];

					$Lidocaine4PER			= $local_anes_rs['topical4PercentLidocaine'];
					$Intracameral 			= $local_anes_rs['Intracameral'];
					$Intracameral1PER		= $local_anes_rs['Intracameral1percentLidocaine'];
					$Peribulbar				= $local_anes_rs['Peribulbar'];
					$Peribular2PER			= $local_anes_rs['Peribulbar2percentLidocaine'];
					$Retrobulbar			= $local_anes_rs['Retrobulbar'];
					$Retrobulbar3PER		= $local_anes_rs['Retrobulbar4percentLidocaine'];

					$Hyalauronidase4percentLidocaine	= $local_anes_rs['Hyalauronidase4percentLidocaine'];
					$VanLindr							= $local_anes_rs['VanLindr'];
					$VanLindrHalfPercentLidocaine		= $local_anes_rs['VanLindrHalfPercentLidocaine'];

					$lidTxt								= $local_anes_rs['lidTxt'];
					$lid								= $local_anes_rs['lid'];
					$lidEpi5ug							= $local_anes_rs['lidEpi5ug'];
					$otherRegionalAnesthesiaWydase15u	= $local_anes_rs['otherRegionalAnesthesiaWydase15u'];
					$otherRegionalAnesthesiaTxt1		= $local_anes_rs['otherRegionalAnesthesiaTxt1'];
					$ocular_pressure_na					= $local_anes_rs['ocular_pressure_na'];
					$none					= $local_anes_rs['none'];
					$digital				= $local_anes_rs['digital'];
					$honanballon 			= $local_anes_rs['honanballon'];
					$honanBallonAnother		= $local_anes_rs['honanBallonAnother'];
					$ansComment				= $local_anes_rs['ansComment'];

					$anesthesiologist2			= $local_anes_rs['signAnesthesia2LastName'].', '.$local_anes_rs['signAnesthesia2FirstName'];
					$anes_signAnesthesia2Status	= $local_anes_rs['signAnesthesia2Status'];
					$signAnesthesia2DateTime	= $local_anes_rs['signAnesthesia2DateTimeFormat'];


					$post_op_evaluation 			= $local_anes_rs['evaluation'];
					$anyKnowAnestheticComplication	= $local_anes_rs['anyKnowAnestheticComplication'];
					$stableCardiPlumFunction2		= $local_anes_rs['stableCardiPlumFunction2'];
					$satisfactoryCondition4Discharge= $local_anes_rs['satisfactoryCondition4Discharge'];
					$remarks						= str_replace(array("\r", "\n"),", ",$local_anes_rs['remarks']);
					$remarks						= str_replace(", , ",", ",$remarks);

					$anesthesiologist3				= $local_anes_rs['signAnesthesia3LastName'].', '.$local_anes_rs['signAnesthesia3FirstName'];
					$signAnesthesia3Status			= $local_anes_rs['signAnesthesia3Status'];
					$signAnesthesia3DateTime		= $local_anes_rs['signAnesthesia3DateTimeFormat'];

					$OBX_STRING = '';
					if($pre_op_evaluation != ''){
						$OBX_STRING .= 'PRE OP EVALUATION: '.$pre_op_evaluation.'~~';
					}
					if($pre_op_stable_cardio != ''){
						$OBX_STRING .= 'STABLE CARDIOVASCULAR AND PULMONARY FUNCTION: '.$pre_op_stable_cardio.'~~';
					}
					if($planAnesthesia != ''){
						$OBX_STRING .= 'PLAN REGIONAL ANESTHESIA WITH SEDATION. RISKS, BENEFITS AND ALTERNATIVES OF ANESTHESIA PLAN HAVE BEEN DISCUSSED: '.$planAnesthesia.'~~';
					}
					if($asaPhysicalStatus != ''){
						$OBX_STRING .= 'ASA PHYSICAL STATUS: '.$asaPhysicalStatus.'~~';
					}
					if($routineMonitorApplied != ''){
						$OBX_STRING .= 'ROUTINE MONITORS APPLIED: '.$routineMonitorApplied.'~~';
					}
					if($ivCatheter != ''){
						$OBX_STRING .= 'IV CATHETER~NO IV:'.$ivCatheter;
						if($hand_left != '' || $hand_right != ''){
							$OBX_STRING .= '~HAND RIGHT:'.$hand_right.'~HAND LEFT:'.$hand_left;
						}
						if($wrist_left != '' || $wrist_left != ''){
							$OBX_STRING .= '~WRIST RIGHT:'.$wrist_left.'~WRIST LEFT:'.$wrist_left;
						}
						if($arm_left != '' || $arm_right != ''){
							$OBX_STRING .= '~ARM RIGHT:'.$arm_right.'~ARM LEFT:'.$arm_left;
						}
						if($anti_left != '' || $anti_right != ''){
							$OBX_STRING .= '~ANTECUBITAL RIGHT:'.$anti_right.'~ANTECUBITAL LEFT:'.$anti_left;
						}
						if($ivCatheterOther != ''){
							$OBX_STRING .= '~OTHER:'.$ivCatheterOther;
						}
						$OBX_STRING .= '~~';
					}				


					$OBX_STRING .= 'LOCAL ANESTHESIA ';
						if($TopicalBlock1Block2 != ''){
							$OBX_STRING .= '~TOPICAL: YES';
						}
						if(($start_time != '' && $start_time != '00:00:00') || ($stop_time != '' && $stop_time != '00:00:00')){
							$OBX_STRING .= '~START TIME: '.$start_time.'~STOP TIME: '.$stop_time;
						}
						if(($newStartTime1 != '' && $newStartTime1 != '00:00:00') || ($newStopTime1 != '' && $newStopTime1 != '00:00:00')){
							$OBX_STRING .= '~START TIME: '.$newStartTime1.'~STOP TIME: '.$newStopTime1;
						}
						if(($newStartTime2 != '' && $newStartTime2 != '00:00:00') || ($newStopTime2 != '' && $newStopTime2 != '00:00:00')){
							$OBX_STRING .= '~START TIME: '.$newStartTime2.'~STOP TIME: '.$newStopTime2;
						}
						if(($newStartTime3 != '' && $newStartTime3 != '00:00:00') || ($newStopTime3 != '' && $newStopTime3 != '00:00:00')){
							$OBX_STRING .= '~START TIME: '.$newStartTime3.'~STOP TIME: '.$newStopTime3;
						}
						if($Lidocaine4PER != ''){
							$OBX_STRING .= '~4% LIDOCAINE: '.$Lidocaine4PER;
						}
						if($Intracameral != ''){
							$OBX_STRING .= '~INTRACAMERAL: '.$Intracameral;
							if($Intracameral1PER != ''){
								$OBX_STRING .= ' 2% LIDOCAINE: '.$Peribular2PER;
							}
						}
						if($Peribulbar != ''){
							$OBX_STRING .= '~PERIBULBAR: '.$Peribulbar;
							if($Peribular2PER != ''){
								$OBX_STRING .= ' 1% LIDOCAINE MPF: '.$Peribular2PER;
							}
						}
						if($Retrobulbar != ''){
							$OBX_STRING .= '~RETROBULBAR (Done by Surgeon): '.$Retrobulbar;
							if($Retrobulbar3PER != ''){
								$OBX_STRING .= ' 3% LIDOCAINE: '.$Retrobulbar3PER;
							}
						}
						if($Hyalauronidase4percentLidocaine != ''){
							$OBX_STRING .= '~4% LIDOCAINE: '.$Hyalauronidase4percentLidocaine;
						}
						if($VanLindr != ''){
							$OBX_STRING .= '~VAN LINDT: '.$VanLindr;
							if($$VanLindrHalfPercentLidocaine != ''){
								$OBX_STRING .= ' 0.5% BUPIVACAINE: '.$VanLindrHalfPercentLidocaine;
							}
						}
						if($lidEpi5ug == 'Yes'){
							$OBX_STRING .= '~EPI 5ug/ml: '.$VanLindrHalfPercentLidocaine;
						}
						if($lid != ''){
							$OBX_STRING .= '~LID: '.$lid;
						}
						if($lidTxt != ''){
							$OBX_STRING .= '~LID TEXT: '.$lidTxt;
						}
						if($otherRegionalAnesthesiaWydase15u=='Yes'){
							$OBX_STRING .= '~WYDASE 15u/ml: '.$otherRegionalAnesthesiaTxt1;
						}
					$OBX_STRING .= '~~';

					if($ocular_pressure_na != ''){
						$OBX_STRING .= 'OCULAR PRESSURE N/A: '.$ocular_pressure_na;
						if($none != ''){
							$OBX_STRING .= '~NONE: '.$none;
						}
						if($digital != ''){
							$OBX_STRING .= '~NONE: '.$digital;
						}
						if($none != ''){
							$OBX_STRING .= '~NONE: '.$none;
						}
						if($honanballon != ''){
							$OBX_STRING .= '~HANON BALLOON: '.$honanballon.' mm '.$honanBallonAnother.' min.';
						}
						if($ansComment != ''){
							$OBX_STRING .= '~COMMENTS: '.$ansComment;
						}
						if(strlen($anesthesiologist2)>3){
							$OBX_STRING .= '~ANESTHESIOLOGIST: '.$anesthesiologist2;
						}
						if($anes_signAnesthesia2Status != ''){
							$OBX_STRING .= '~SIGNATURE ON FILE: '.$anesthesiologist2;
						}
						if($signAnesthesia2DateTime != '' && substr($signAnesthesia2DateTime,0,2) != '00'){
							$OBX_STRING .= '~SIGNATURE DATE: '.$signAnesthesia2DateTime;
						}
					}
					$OBX_STRING .= '~~';

					$OBX_STRING .= 'POST OPERATIVE ';
					if($post_op_evaluation != ''){
						$OBX_STRING .= 'POST OP EVALUATION: '.$post_op_evaluation.'~~';
					}
					if($anyKnowAnestheticComplication){
						$OBX_STRING .= 'NO KNOWN ANESTHETIC COMPLICATION: '.$anyKnowAnestheticComplication.'~~';
					}
					if($stableCardiPlumFunction2){
						$OBX_STRING .= 'STABLE CARDIOVASCULAR AND PULMONARY FUNCTION: '.$stableCardiPlumFunction2.'~~';
					}
					if($satisfactoryCondition4Discharge){
						$OBX_STRING .= 'SATISFACTORY CONDITION FOR DISCHARGE: '.$satisfactoryCondition4Discharge.'~~';
					}
					if($remarks){
						$OBX_STRING .= 'REMARKS: '.$remarks.'~~';
					}
					if(strlen($anesthesiologist3)>3){
						$OBX_STRING .= '~ANESTHESIOLOGIST: '.$anesthesiologist3;
					}
					if($signAnesthesia3Status != ''){
						$OBX_STRING .= '~SIGNATURE ON FILE: '.$signAnesthesia3Status;
					}
					if($signAnesthesia3DateTime != '' && substr($signAnesthesia3DateTime,0,2) != '00'){
						$OBX_STRING .= '~SIGNATURE DATE: '.$signAnesthesia3DateTime;
					}
					$OBX_STRING .= '~~';

					$OBX_TEXT .= 'OBX|1|TX|||'.$OBX_STRING.'|||
';
				}
				$HL7MSG_TEXT 	.= $OBX_TEXT;
			}
		}

		$pr1_segment_str = '';
		$pr1_segment_str_PRL = '';
		$pr1_segment_anes_str = '';
		$j = 0;
		$practice_counter = 0;
		$anesthesia_counter = 0;
		$ZCC_custom_segment = '';
		for($i=0;$i<count($all_procs);$i++){
			
			if( in_array(strtolower($GLOBALS["LOCAL_SERVER"]), array('gnysx','mackool')) ){
				$j = (in_array($all_procs[$i], $anesthesia_cpt))? ++$anesthesia_counter : ++$practice_counter;
			}
			else
				$j = $i+1;
			
			$performed_by_code = $ft1_rs['surgeon_npi_name'];
			if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham','palisades'))){
				$performed_by_code = $ft1_rs['anes_npi_name'];
			}

			/*Modifiers for the charge row - PRL*/
			$modifiers = '';
			if(strtolower($GLOBALS["LOCAL_SERVER"])=='waltham')
			{
				if( array_key_exists(1, $cptModifiers) )
				{
					if( array_key_exists($all_procs[$i], $cptModifiers[1]) )
					{
						$modifiers = trim($cptModifiers[1][$all_procs[$i]]);
					}	
				}
			}

			/*GET POS Location*/
			$facility_location_prl = '';
			if(strtolower($GLOBALS["LOCAL_SERVER"])=='waltham')
			{
				$facDataTemp = imw_query("SELECT ft.external_id FROM stub_tbl st JOIN facility_tbl ft ON(st.iasc_facility_id = ft.fac_idoc_link_id) WHERE st.patient_confirmation_id = '".$SC_pConfId."' ORDER BY st.stub_id LIMIT 1");

				if($facDataTemp && imw_num_rows($facDataTemp)==1){

					$facDataTemp = imw_fetch_assoc($facDataTemp);
					$facility_location_prl = '^^^'.$facDataTemp['external_id'];
				}
			}
			
			$performed_by_code_ft20 = $performed_by_code;
			$ft1_2 = $iASCID;
			if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye'))){
				if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye'))){
					$facility_location = "75714C81-3C46-4E5A-999B-8FA70D519200^Island Eye Surgicenter";
					$performed_by_code_ft20 = '1629152053^';
					$ft1_2 = $iASCID.'_'.$j;
				}else if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('valleyeye'))){
					$facility_location = "97071F68-8BDB-4BD3-9C0B-781FC3CD123E^Valley Eye Surgical Center";
					$performed_by_code_ft20 = '1437104718^';
					$ft1_2 = $iASCID.'_'.$j;
				}
				if(empty($ZCC_custom_segment)) {
					$ZCC_custom_segment = 'ZCC||'.$performed_by_code.'
';
				}
			}else if(strtolower($GLOBALS["LOCAL_SERVER"])=='southwest'){
				$ft1_2 = $iASCID.'_'.$j;
			}
			
			$FT1 	= 'FT1|'.($j).'|'.$ft1_2.'||'.str_replace(array('-',' ',':'),'',$date_of_service.$start_time).'|'.str_replace(array('-',' ',':'),'',$date_of_service.$stop_time).'|CG|'.$arr_procs_mapping[$all_procs[$i]]['cpt'].'|||1||||||'.$facility_location.'||O|'.$all_diags.'|'.$performed_by_code_ft20.'|||DCS'.$ft1_rs['encounter_id'].'PC'.$SC_pConfId.'|'.$performed_by_code.'|'.$arr_procs_mapping[$all_procs[$i]]['cpt'].'||
';
			if(strtolower($GLOBALS["LOCAL_SERVER"])=='waltham')
			{
				$FT1_PRL 	= 'FT1|'.($j).'|'.$iASCID.'||'.str_replace(array('-',' ',':'),'',$date_of_service.$start_time).'|'.str_replace(array('-',' ',':'),'',$date_of_service.$stop_time).'|CG|'.$arr_procs_mapping[$all_procs[$i]]['cpt'].'|||1||||||'.$facility_location_prl.'||O|'.$all_diags_prl.'|'.$ft1_rs['surgeon_npi_name'].'|||DCS'.$ft1_rs['encounter_id'].'PC'.$SC_pConfId.'|'.$ft1_rs['surgeon_npi_name'].'|'.$arr_procs_mapping[$all_procs[$i]]['cpt'].'|'.$modifiers.'|
';
			}
			
			$pr1_segment_str_temp = 'PR1|'.($j).'|CPT|'.$arr_procs_mapping[$all_procs[$i]]['cpt'].'||'.str_replace(array('-',' ',':'),'',$date_of_service.$start_time).'|||||||'.$performed_by_code.'||||
';
			$pr1_segment_str_temp_PRL = 'PR1|'.($j).'|CPT|'.$arr_procs_mapping[$all_procs[$i]]['cpt'].'||'.str_replace(array('-',' ',':'),'',$date_of_service.$start_time).'|||||||'.$ft1_rs['surgeon_npi_name'].'||||
';
			
			if( in_array(strtolower($GLOBALS["LOCAL_SERVER"]), array('gnysx','mackool')) && in_array($all_procs[$i], $anesthesia_cpt)){
				$HL7MSG_ANES_TEXT	.= $FT1;
				$pr1_segment_anes_str .= $pr1_segment_str_temp;
			}
			else{
				//$HL7MSG_TEXT 	 .= $FT1;
				if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye'))){
					$HL7MSG_TEXT 	 .= $FT1.$ZCC_custom_segment;
				}else{
					$HL7MSG_TEXT 	 .= $FT1;
				}
				if(strtolower($GLOBALS["LOCAL_SERVER"])=='waltham'){
					$HL7MSG_TEXT_PRL .= $FT1_PRL;
					$pr1_segment_str_PRL .= $pr1_segment_str_temp_PRL;
				}

				$pr1_segment_str .= $pr1_segment_str_temp;
			}
			
			if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham','palisades'))){
				$HL7MSG_TEXT_ADT .= $FT1;
			}
		}
		if(!in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('southwest','islandeye','valleyeye'))){
			$HL7MSG_TEXT 	 .= $pr1_segment_str;
		}

		if(strtolower($GLOBALS["LOCAL_SERVER"])=='waltham')
			$HL7MSG_TEXT_PRL .= $pr1_segment_str_PRL;

		$HL7MSG_ANES_TEXT .= $pr1_segment_anes_str;
		//$HL7MSG_TEXT_ADT .= $pr1_segment_str;
		
		//if(strtolower($GLOBALS["LOCAL_SERVER"])=='keywhitman'){
			//GETTING ALL DX CODES DETAILS FOR DG1 SEGMENT
			$res_dg = imw_query("SELECT icd10_desc FROM icd10_data WHERE id IN ($all_diags_ids)");
			if($res_dg && imw_num_rows($res_dg)>0){
				$all_diags_descrips = array();
				while($rs_dg = imw_fetch_assoc($res_dg)){
					$all_diags_descrips[] = $rs_dg['icd10_desc'];
				}
				for($i=0; $i<count($arr_all_diags); $i++){
					$DG1 	='DG1|'.($i+1).'|ICD10|'.$arr_all_diags[$i].'^'.$all_diags_descrips[$i].'|
';//.$diag_descrip.'|'.$ft1_rs['summarySaveDateTime'].'|F||||||||||'.$ft1_rs['surgeon_npi_name'].'|D|'.chr(13);
					if(!in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('southwest','islandeye','valleyeye'))){
						$HL7MSG_TEXT 	.= $DG1;
					}

					if(strtolower($GLOBALS["LOCAL_SERVER"])=='waltham')
						$HL7MSG_TEXT_PRL 	.= $DG1;

					$HL7MSG_ANES_TEXT .= $DG1;
					if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham','palisades'))){
						$HL7MSG_TEXT_ADT .= $DG1;
					}
				}
				
			}
		//}
		
	}else{
		$HL7MSG_ERROR[] = 'Error in getting Discharge Summary Sheet details for Confirmation ID '.$SC_pConfId.'.';	
	}
	
	if( !in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('keywhitman','gnysx','islandeye','valleyeye')) ){
		//GETTING INSURANCE..
		$ins_type = array("primary","secondary","tertiary");
		$co_arr = count($ins_type);
		if(strtolower($GLOBALS["LOCAL_SERVER"]) == 'waltham'){
			//include(dirname(__FILE__)."/connect_imwemr.php");
			include(dirname(__FILE__)."/common/conDb.php");// making SC connection
			for($i=0;$i<$co_arr;$i++){	
				$in_type = $ins_type[$i];
				$in1_sql = "SELECT '000".($i+1)."' AS set_id, '' AS ID1, '' AS ID2,";
				$in1_sql .= "ins_in_house_code AS ID2_prl, ";
				$in1_sql .= "id.ins_provider AS provider, 
						'' AS address, 
						'' AS ins_data_comments, 
						'' AS Company_Cont_Person, 
						'' AS Company_Phone, 
						'' AS group_number, 
						id.group_name AS Group_Name, 
						'' AS Group_Employer_id, 
						'' AS Group_Employer_Name, 
						IF( DATE_FORMAT(id.active_date,'%Y%m%d') = '00000000', '', DATE_FORMAT(id.active_date,'%Y%m%d') ) AS effective_date, 
						IF( DATE_FORMAT(id.expiry_Date,'%Y%m%d') = '00000000', '', DATE_FORMAT(id.expiry_Date,'%Y%m%d') ) AS expiration_date, 
						id.authorization_number AS Authorization_Information, 
						'' AS Authorization_Information_prl, 
						'' AS Plan_Type, 
						CONCAT(id.lname,'^', id.fname, '^', SUBSTR(id.mname,1,1)) AS subscriber_names, 
						id.sub_relation, 
						DATE_FORMAT(id.dob,'%Y%m%d') AS subscriber_DOB, 
						CONCAT(id.address1,'^',id.address2,'^', id.city,'^', id.state,'^', id.zip_code) AS subscriber_address, 
						'' AS assign_of_benifit, 
						'' AS cob, 
						'' AS cobp, 
						'' AS noaf, 
						'' AS noad, 
						'' AS rpt_of_ef,
						'' AS rpt_of_ed, 
						'' AS ric, 
						'' AS pac, 
						'' AS veridate, 
						'' AS veryby, 
						'' AS typeOfAC, 
						'' AS billingSt, 
						'' AS lifeReseDays, 
						'' AS delayBefore, 
						id.plan_name AS compPlanCode, 
						REPLACE(id.policy,'-','') AS PolicyNumber, 
						'' AS PolicyDeductible, 
						'' AS PolLimAmount, 
						'' AS PolLimDays, 
						'' AS RoomRateSP, 
						'' AS RoomRateP, 
						'' AS InsuEmpStatus, 
						SUBSTR(id.gender,1,1) AS subscriber_sex  
						FROM insurance_data id WHERE patient_id = '".$SC_patient_id."' AND type = '".$in_type."' AND actInsComp='1' LIMIT 0,1";

				$in1_res = imw_query($in1_sql);	
				if($in1_res && imw_num_rows($in1_res)==1){
					$in1_rs = imw_fetch_assoc($in1_res);
					/***GET INSURNACE COMPANY DATA FROM IdOC BASED ON IN_HOUSE_CODE***/
					include(dirname(__FILE__)."/connect_imwemr.php");
					if(!empty($in1_rs['ID2_prl'])){
						$insurance_comp_res = imw_query("SELECT ic.id, ic.name AS provider, CONCAT(ic.contact_address,'^','','^',ic.City,'^',ic.State,'^',ic.Zip,ic.zip_ext) AS address,ic.contact_name AS Company_Cont_Person, REPLACE(ic.phone,'-','') AS Company_Phone FROM insurance_companies ic WHERE LOWER(ic.in_house_code) = '".strtolower($in1_rs['ID2_prl'])."' AND ins_del_status = '0'");
					}else{
						$insurance_comp_res = imw_query("SELECT ic.id, ic.name AS provider, CONCAT(ic.contact_address,'^','','^',ic.City,'^',ic.State,'^',ic.Zip,ic.zip_ext) AS address,ic.contact_name AS Company_Cont_Person, REPLACE(ic.phone,'-','') AS Company_Phone FROM insurance_companies ic WHERE LOWER(ic.name) = '".strtolower(trim($in1_rs['provider']))."' AND ins_del_status = '0'");
					}
					if($insurance_comp_res && imw_num_rows($insurance_comp_res)==1){
						$insurance_comp_rs 		= imw_fetch_assoc($insurance_comp_res);
						$in1_rs['ID2_prl'] 		= $insurance_comp_rs['id'];
						$in1_rs['address'] 		= $insurance_comp_rs['address'];
						$in1_rs['Company_Cont_Person'] 	= $insurance_comp_rs['Company_Cont_Person'];
						$in1_rs['Company_Phone'] 		= $insurance_comp_rs['Company_Phone'];
					}else{
						$in1_rs['ID2_prl'] = '';
					}
					include(dirname(__FILE__)."/common/conDb.php");// making SC connection
					
					foreach ($in1_rs as $key=>$val){
						if($key=='subscriber_relationship'){
							$in1_rs[$key] = mapping_vocabulary('REL_IN1',strtoupper($val));
							if($in1_rs[$key]==""){
								$in1_rs[$key] = mapping_vocabulary('REL_NK1',strtoupper($val));
							}
							if($in1_rs[$key]==""){
								$in1_rs[$key] = mapping_vocabulary('REL_GT1',strtoupper($val));
							}
						}
						if($key=='address'){//ins_data_comments
							if(strlen($in1_rs[$key])<=5 && trim($in1_rs['ins_data_comments'])!=''){
								$pt_ins_comments_arr = explode("\n",trim($in1_rs['ins_data_comments']));
								$pt_ins_comments_str = implode('^',$pt_ins_comments_arr);
								$in1_rs[$key] = str_replace("\n","",$pt_ins_comments_str);
								$in1_rs[$key] = str_replace("\r","",$pt_ins_comments_str);
							}else{
								$val = str_replace(array("\n","\r\n","\r"), ', ', $val);
								$in1_rs[$key] = $val;
							}
							unset($in1_rs['ins_data_comments']);
						}
					}
				
					if( strtolower($GLOBALS["LOCAL_SERVER"]) == 'waltham')
					{
						$in1_rs_prl = $in1_rs;
						unset($in1_rs['ID2_prl'], $in1_rs['Authorization_Information_prl'], $in1_rs_prl['ID2'], $in1_rs_prl['Authorization_Information']);

						$HL7MSG_TEXT_PRL 	.= 'IN1|'.implode('|',$in1_rs_prl).'
';
						$HL7MSG_TEXT_ADT_PRL 	.= 'IN1|'.implode('|',$in1_rs_prl).'
';
						unset($in1_rs_prl);
					}

					//IN1 SEGMENT (INSURANCE INFORMATION)
					$HL7MSG_TEXT 	.= 'IN1|'.implode('|',$in1_rs).'
';
					$HL7MSG_TEXT_ADT 	.= 'IN1|'.implode('|',$in1_rs).'
';
				}else if(!$in1_res){
					$HL7MSG_ERROR[] = 'Error in fetching iASC Patient Insurance.';
				}
			}
		}else{
			include(dirname(__FILE__)."/connect_imwemr.php");
			for($i=0;$i<$co_arr;$i++){	
				$in_type = $ins_type[$i];
				$in1_sql = "SELECT '000".($i+1)."' AS set_id, '' AS ID1, '' AS ID2,";
				$in1_sql .= "ic.name AS provider, 
						CONCAT(ic.contact_address,'^','','^',ic.City,'^',ic.State,'^',ic.Zip,ic.zip_ext) AS address, 
						id.comments AS ins_data_comments, 
						ic.contact_name AS Company_Cont_Person, 
						REPLACE(ic.phone,'-','') AS Company_Phone, 
						id.group_number, 
						'' AS Group_Name, 
						'' AS Group_Employer_id, 
						'' AS Group_Employer_Name, ";
				$in1_sql .= "DATE_FORMAT(id.effective_date,'%Y%m%d') AS effective_date, ";
				$in1_sql .= "'' AS expiration_date, 
						'' AS Authorization_Information, ";
				$in1_sql .= "'' AS Plan_Type, 
						CONCAT(id.subscriber_lname,'^', id.subscriber_fname, '^', SUBSTR(id.subscriber_mname,1,1)) AS subscriber_names, 
						id.subscriber_relationship, 
						DATE_FORMAT(id.subscriber_DOB,'%Y%m%d') AS subscriber_DOB, 
						CONCAT(id.subscriber_street,'^',id.subscriber_street_2,'^', id.subscriber_city,'^', id.subscriber_state,'^', id.subscriber_postal_code) AS subscriber_address, 
						'' AS assign_of_benifit, 
						'' AS cob, 
						'' AS cobp, 
						'' AS noaf, 
						'' AS noad, 
						'' AS rpt_of_ef,
						'' AS rpt_of_ed, 
						'' AS ric, 
						'' AS pac, 
						'' AS veridate, 
						'' AS veryby, 
						'' AS typeOfAC, 
						'' AS billingSt, 
						'' AS lifeReseDays, 
						'' AS delayBefore, 
						id.plan_name AS compPlanCode, 
						REPLACE(id.policy_number,'-','') AS PolicyNumber, 
						'' AS PolicyDeductible, 
						'' AS PolLimAmount, 
						'' AS PolLimDays, 
						'' AS RoomRateSP, 
						'' AS RoomRateP, 
						'' AS InsuEmpStatus, 
						SUBSTR(id.subscriber_sex,1,1) AS subscriber_sex 
						FROM insurance_data id ";
				$in1_sql .= "
						LEFT JOIN insurance_companies ic ON (id.provider=ic.id) 
						LEFT JOIN idx_invision_rco iir ON (iir.id=id.rco_code_id) 
						WHERE pid = '".$iASC_patient_id."' AND type = '".$in_type."' AND actInsComp='1' LIMIT 0,1";
		
				$in1_res = imw_query($in1_sql);	
				if($in1_res && imw_num_rows($in1_res)==1){
					$in1_rs = imw_fetch_assoc($in1_res);
					foreach ($in1_rs as $key=>$val){
						if($key=='subscriber_relationship'){
							$in1_rs[$key] = mapping_vocabulary('REL_IN1',strtoupper($val));
							if($in1_rs[$key]==""){
								$in1_rs[$key] = mapping_vocabulary('REL_NK1',strtoupper($val));
							}
							if($in1_rs[$key]==""){
								$in1_rs[$key] = mapping_vocabulary('REL_GT1',strtoupper($val));
							}
						}
						if($key=='address'){//ins_data_comments
							if(strlen($in1_rs[$key])<=5 && trim($in1_rs['ins_data_comments'])!=''){
								$pt_ins_comments_arr = explode("\n",trim($in1_rs['ins_data_comments']));
								$pt_ins_comments_str = implode('^',$pt_ins_comments_arr);
								$in1_rs[$key] = str_replace("\n","",$pt_ins_comments_str);
								$in1_rs[$key] = str_replace("\r","",$pt_ins_comments_str);
							}else{
								$val = str_replace(array("\n","\r\n","\r"), ', ', $val);
								$in1_rs[$key] = $val;
							}
							unset($in1_rs['ins_data_comments']);
						}
					}

					//IN1 SEGMENT (INSURANCE INFORMATION)
					$HL7MSG_TEXT 	.= 'IN1|'.implode('|',$in1_rs).'
';
					$HL7MSG_TEXT_ADT 	.= 'IN1|'.implode('|',$in1_rs).'
';
				}else if(!$in1_res){
					$HL7MSG_ERROR[] = 'Error in fetching iASC Patient Insurance.';
				}
			}
		}
		include(dirname(__FILE__)."/common/conDb.php"); 
		if(!in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye'))){
			$HL7MSG_TEXT_ADT .= 'NTE|0001|'.$iASCID.'
';
		}
		if( strtolower($GLOBALS["LOCAL_SERVER"]) == 'waltham' )
		{
			$HL7MSG_TEXT_ADT_PRL .= 'NTE|0001|'.$iASCID.'
';
		}
	}
}else{
	$HL7MSG_ERROR[] = 'Error in getting iASC Patient ID for Confirmatoin ID '.$SC_pConfId.'.';
}
if(count($HL7MSG_ERROR)>0){
	$err_log_file_path		= dirname(__DIR__)."/".$imwDirectoryName."/hl7sys/sender/err_logs/error_".date('Y-m-d').".txt";
	$fp = fopen($err_log_file_path,'a');
	fwrite($fp,implode("\n",$HL7MSG_ERROR));
	fwrite($fp,"\n");
	fclose($fp);
}else if(count($HL7MSG_ERROR)==0){
	if($its_demo_msg_show_only) die($IES_ADT_MSH.$HL7MSG_TEXT_ADT.'<hr>'.$HL7MSG_TEXT);
	
	//MAKE,SAVE MESSAGE.
	$flat_file_path		= dirname(__DIR__)."/".$imwDirectoryName."/hl7sys/sender/outbound";
	//file_put_contents($flat_file_path."/1.txt",$HL7MSG_TEXT);
	$dftID='';
	//create log
	$send_to_value = '';
	if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham','palisades'))){
		$send_to_value = 'PPMC';
		//DELETING IF ANY UNSENT MESSAGE FOUND FOR SAME PCONF_ID.
		imw_query("DELETE FROM hl7_sent WHERE sch_id='".$SC_pConfId."' AND send_to ='".$send_to_value."' AND sent=0");
	}
	elseif(strtolower($GLOBALS["LOCAL_SERVER"])==='mackool'){$send_to_value = 'KARNEY';}
	
	if( in_array(strtolower($GLOBALS["LOCAL_SERVER"]), array('valleyeye')) ){
		$HL7MSG_TEXT = str_replace(array("\n\r","\n"),chr(13),$HL7MSG_TEXT);
	}
	
	$query = "insert into hl7_sent set patient_id='".$iASC_patient_id."',
				msg='". imw_real_escape_string($HL7MSG_TEXT) ."',
				msg_type='DFT',
				saved_on='".date('Y-m-d H:i:s')."',
				operator='".$_SESSION[loginUserId]."',
				send_to='".$send_to_value."',
				sch_id='".$SC_pConfId."'";
	
	$hl7_log_resp_anes = false;
	if( in_array(strtolower($GLOBALS["LOCAL_SERVER"]), array('gnysx', 'mackool')) ){
		$query1 = "insert into hl7_sent set patient_id='".$iASC_patient_id."',
				msg='". imw_real_escape_string($HL7MSG_ANES_TEXT) ."',
				msg_type='DFT',
				saved_on='".date('Y-m-d H:i:s')."',
				operator='".$_SESSION[loginUserId]."',
				send_to='".$send_to_value."',
				sch_id='".$SC_pConfId."'";
		if( strtolower($GLOBALS["LOCAL_SERVER"]) === 'gnysx' )
			include(dirname(__FILE__)."/connect_imwemr.php");
		
		if(isset($anesthesia_cpt) && count($anesthesia_cpt)>0)
			$hl7_log_resp_anes = imw_query($query1);
		else
			$hl7_log_resp_anes = true;
	}
	
	if( strtolower($GLOBALS["LOCAL_SERVER"]) !== 'mackool' )
	{
		if( in_array(strtolower($GLOBALS["LOCAL_SERVER"]), array('islandeye','valleyeye')) ){
			imw_close($link);
			include('connect_imwemr.php');
			
			$IES_ADT_MSH = $IES_ADT_MSH.$HL7MSG_TEXT_ADT;
			
			if( in_array(strtolower($GLOBALS["LOCAL_SERVER"]), array('valleyeye')) ){
				$IES_ADT_MSH = str_replace(array("\n\r","\n"),chr(13),$IES_ADT_MSH);
			}
			
			$queryIESADT = "INSERT into hl7_sent set patient_id='".$iASC_patient_id."',
				msg='". imw_real_escape_string($IES_ADT_MSH) ."',
				msg_type='Update_Patient',
				saved_on='".date('Y-m-d H:i:s')."',
				operator='".$_SESSION['loginUserId']."',
				send_to='".$send_to_value."',
				sch_id='".$SC_pConfId."'";
			
		}else if( in_array(strtolower($GLOBALS["LOCAL_SERVER"]), array('islandeye','valleyeye', 'southwest')) ){
			imw_close($link);
			include('connect_imwemr.php');	
		}
		if( in_array(strtolower($GLOBALS["LOCAL_SERVER"]), array('islandeye','valleyeye')) ){
			$chkHl7SentRes = imw_query("SELECT id,sent FROM hl7_sent WHERE sch_id='".$SC_pConfId."' AND msg_type = 'DFT' LIMIT 0,1");
			if(imw_num_rows($chkHl7SentRes)==0){
				$hl7_log_resp = imw_query($query);
				imw_query($queryIESADT);
			}
		}else{
			$hl7_log_resp = imw_query($query);
		}
		
		if( in_array(strtolower($GLOBALS["LOCAL_SERVER"]), array('islandeye','valleyeye', 'southwest')) ){
			imw_close($link_imwemr);	
			file_put_contents($_SERVER['DOCUMENT_ROOT'].'/'.$imwDirectoryName."/data/".$imwPracName."/hl7Flags/senderCheckDB.log", 1);
			
			include(dirname(__FILE__)."/common/conDb.php"); 
		}
	}
	
	if($hl7_log_resp_anes && $hl7_log_resp && strtolower($GLOBALS["LOCAL_SERVER"])==='gnysx'){
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/'.$imwDirectoryName."/hl7sys/sender/db_check_flag.txt","1");
	}
	
	include(dirname(__FILE__)."/common/conDb.php"); 
	
	$dftID=imw_insert_id();
	
	$newMsgId		= newMessageUniqueId();

	if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham','palisades','mackool'))){
		if(strtolower($GLOBALS["LOCAL_SERVER"])==='mackool'){
			$HL7MSG_TEXT_ADT = 'MSH|^~\&|IMW|IMW_'.strtoupper($GLOBALS["LOCAL_SERVER"]).'|MACKOOL|MACKOOL|'.date('YmdHis').'||ADT^A08|'.$newMsgId.'|P|2.4||||||ASCII|||
'.$HL7MSG_TEXT_ADT;
		}else if(strtolower($GLOBALS["LOCAL_SERVER"])==='waltham'){
			$HL7MSG_TEXT_ADT = 'MSH|^~\&|IMW|IMW_'.strtoupper($MSG_ORIGIN['fac_city']).'|TEST|TEST|'.date('YmdHis').'||ADT^A08|'.$newMsgId.'|P|2.4||||||ASCII|||
'.$HL7MSG_TEXT_ADT;
		}else{
			$HL7MSG_TEXT_ADT = 'MSH|^~\&|IMW|IMW_'.strtoupper($GLOBALS["LOCAL_SERVER"]).'|TEST|TEST|'.date('YmdHis').'||ADT^A08|'.$newMsgId.'|P|2.4||||||ASCII|||
'.$HL7MSG_TEXT_ADT;
		}
		
		imw_query("insert into hl7_sent set patient_id='".$iASC_patient_id."',
				msg='". imw_real_escape_string($HL7MSG_TEXT_ADT) ."',
				msg_type='ADT',
				saved_on='".date('Y-m-d H:i:s')."',
				operator='".$_SESSION[loginUserId]."',
				send_to='".$send_to_value."',
				sch_id='".$SC_pConfId."'");

		if(strtolower($GLOBALS["LOCAL_SERVER"])==='waltham' && $log_prl_hl7 === true){
			
			$send_to_value = 'PRL';
			imw_query("DELETE FROM hl7_sent WHERE sch_id='".$SC_pConfId."' AND send_to ='".$send_to_value."' AND sent=0");

			$newMsgId		= newMessageUniqueId();
			$HL7MSG_TEXT_PRL 	= 'MSH|^~\&|IMW|IMW|PRL|PRL|'.date('YmdHis').'||DFT^P03|'.$newMsgId.'|P|2.3.1||||||ASCII|||
'.$HL7MSG_TEXT_PRL;
			imw_query("insert into hl7_sent set patient_id='".$iASC_patient_id."',
				msg='". imw_real_escape_string($HL7MSG_TEXT_PRL) ."',
				msg_type='DFT',
				saved_on='".date('Y-m-d H:i:s')."',
				operator='".$_SESSION[loginUserId]."',
				send_to='".$send_to_value."',
				sch_id='".$SC_pConfId."'");
			
			$newMsgId		= newMessageUniqueId();
			$HL7MSG_TEXT_ADT_PRL = 'MSH|^~\&|IMW|IMW|PRL|PRL|'.date('YmdHis').'||ADT^A08|'.$newMsgId.'|P|2.4||||||ASCII|||
'.$HL7MSG_TEXT_ADT_PRL;
			imw_query("insert into hl7_sent set patient_id='".$iASC_patient_id."',
				msg='". imw_real_escape_string($HL7MSG_TEXT_ADT_PRL) ."',
				msg_type='ADT',
				saved_on='".date('Y-m-d H:i:s')."',
				operator='".$_SESSION[loginUserId]."',
				send_to='".$send_to_value."',
				sch_id='".$SC_pConfId."'");
		}
	}
	
	if(strtolower($GLOBALS["LOCAL_SERVER"])=='keywhitman'){
		//WRITE FLAT FILE FOR EACH MESSAGE
		if(is_dir($flat_file_path)){
			$text_file_name_DFT = $flat_file_path."/HL7_DFT_".$dftID.".hl7";
			$fp = fopen($text_file_name_DFT,'w');
			if($fp){
				$fw = fwrite($fp,$HL7MSG_TEXT);
				if($fw){//MARK THE MESSAGE AS SENT.
					imw_query("UPDATE hl7_sent SET sent=1, sent_on = '".date('Y-m-d H:i:s')."', status_text='Sent to OUTBOUND_HL7_DIR' WHERE id = '".$dftID."'");
				}
			}
		}
	}
	
}else{
	if($its_demo_msg_show_only) {
		//LOG ERROR.
		echo '<pre>';
		print_r($HL7MSG_ERROR);
	}
}

?>