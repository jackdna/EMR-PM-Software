<?php
/*
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 * Use this software under MIT License
 */
require_once($GLOBALS['srcdir'].'/classes/cls_common_function.php');
require_once($GLOBALS['srcdir'].'/classes/SaveFile.php');
$cls_common = new CLSCommonFunction();

class Documents extends SaveFile{
	
	public $current_tab = '';
	public $cur_tab_table = '';
	public $upload_path = '';
	public $doc_web_root = '';
	public $cur_tab_prnt_table = '';
	public $unique_doc_msg = 'Template name already exists';
	public $logo_data = array();
	public $common_array = array();
	
	public function __construct($request_from = 'collection',$sub_section=''){
		parent::__construct();
		
		//Changing Webroot if it does not exists or empty [ For New HCCS Server ]
		$this->doc_web_root = $GLOBALS['webroot'];
		
		if(empty($this->doc_web_root)) $this->doc_web_root = $GLOBALS['php_server'];
		
		$this->upload_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."";
		//Returns data and value based on requested section
		
		$load_data = $this->request_data($request_from,$sub_section);
		
		$this->current_tab = $load_data['tab'];
		$this->cur_tab_table = $load_data['table'];
		
		if($load_data['prnt_table'])
		{
			$this->cur_tab_prnt_table = $load_data['prnt_table'];
		}
		
		if($load_data['unique_msg'])
		{
			$this->unique_doc_msg = $load_data['unique_msg'];
		}
		
		//To generate logo related data on loading
		if(empty($this->logo_url_arr))
		{
			$this->logo_data = $this->get_logo_variables(); 
		}
		
		//Generating commom arrays on load
		$this->common_array = $this->get_common_dropdown_arr();
	}
	
	//Returns required data based on section
	public function request_data($section,$sub_sec = false){
		
		$return_arr = array();
		
		$return_arr['tab'] = (empty($section) == false) ? $section : 'collection';
		
		if($sub_sec)
		{
			$return_arr['tab'] = $sub_sec;
		}
		
		switch($section)
		{
			case 'collection':
				$return_arr['table'] = 'collection_letter_template';
			break;
			
			case 'consent':
				$return_arr['table'] = 'consent_form';
				$return_arr['prnt_table'] = 'consent_category';
				
			break;
			
			case 'package':
				$return_arr['table'] = 'consent_package';
				$return_arr['unique_msg'] = 'Package name already exists';
			break;
			
			case 'consult':
				$return_arr['table'] = 'consultTemplate';
			break;
			
			case 'education':
			case 'instructions':
				$return_arr['table'] = 'document';
			break;
			
			case 'op_notes':
				$return_arr['table'] = 'pn_template';
			break;
			
			case 'recall':
				$return_arr['table'] = 'recalltemplate';
			break;
			
			case 'pt_docs':
				$return_arr['table'] = 'pt_docs_template';
				$return_arr['prnt_table'] = 'main_template_category';
			break;
			
			case 'statements':
				$return_arr['table'] = 'statement_template';
			break;
			
			case 'order_temp':
				$return_arr['table'] = 'order_template';
			break;
			
			case 'smart_tags':
				$return_arr['table'] = 'smart_tags';
			break;
			
			case 'logos':
				$return_arr['table'] = 'document_logos';
			break;
			
			case 'panels':
				$return_arr['table'] = 'document_panels';
			break;
			
			case 'surgery_consent':
				$return_arr['table'] = 'surgery_center_consent_forms_template';
				$return_arr['prnt_table'] = 'surgery_center_consent_category';
			break;
			
			case 'prescriptions':
				$return_arr['table'] = 'prescription_template';
				//$return_arr['prnt_table'] = 'surgery_center_consent_category';
			break;
			
			default:
				$return_arr['table'] = 'collection_letter_template';
			break;
		}
		return $return_arr;
	}
	
	//Return base path
	public function get_base_path($path='')
	{
		$return_str = $this->doc_web_root.'/interface/admin/documents';
		
		if($this->current_tab == 'order_temp')
		{
			$return_str = $GLOBALS['webroot'].'/interface/admin/order_sets/order_templates';
		}
		
		if($this->current_tab == 'prescription')
		{
			$return_str = $GLOBALS['webroot'].'/interface/admin/console/prescriptions';
		}
		
		if($path == 'file')
		{
			$return_str = str_replace($this->doc_web_root,$GLOBALS['fileroot'],$return_str);
		}
		
		return $return_str;
	}
	
	
	//Returns array of variable available for this section
	public function get_variable_tags($sec_name)
	{
		$return_arr = array();
		
		$commonPtInfoArr1	 = array(
							 		 "{SEX}"						=>"Patient Gender information  ", 	
									 "{PATIENT SS}"					=>"Patient SS Number  ",			
									 "{MARITAL STATUS}"				=>"Patient Marital Status  ",	   	
									 "{EMERGENCY CONTACT}"			=>"Patient Emergency Contact  ",	"{EMERGENCY CONTACT PH}"	=>"Patient Emergency Contact Ph.  ",
									 "{REGISTRATION DATE}"			=>"Registration Date  ",			"{POS FACILITY}"			=>"Facility  ",
									 "{DRIVING LICENSE}"			=>"Driving License  ",			
									 
									 "{RES.PARTY TITLE}"			=>"Res. Party Title  ",
									 "{RES.PARTY MIDDLE NAME}"		=>"Res. Party Middle Name  ",
									 "{RES.PARTY DOB}"				=>"Res. Party DOB  ",
									 "{RES.PARTY SS}"				=>"Res. Party SS  ",				"{RES.PARTY SEX}"			=>"Res. Party Gender Info.  ",
									 "{RES.PARTY RELATION}"			=>"Res. Party Relation  ",			
									 /*"{RES.PARTY CITY}"				=>"Res. Party City  ",				"{RES.PARTY STATE}"			=>"Res. Party State  ",
									 "{RES.PARTY ZIP}"				=>"Res. Party Zip  ",				*/
									 "{RES.PARTY MARITAL STATUS}"=>"Res. Party Marital Status  ",
									 "{RES.PARTY DD NUMBER}"		=>"Res. Party DD Number  ",
									 
									 "{PATIENT OCCUPATION}"			=>"Patient Occupation  ",
									 "{PATIENT EMPLOYER}"			=>"Patient Employer  ",			"{OCCUPATION ADDRESS1}"		=>"Patient Occupation Address1  ",
									 "{OCCUPATION ADDRESS2}"		=>"Patient Occupation Address2  ",	"{OCCUPATION CITY}"			=>"Patient Occupation City  ",
									 "{OCCUPATION STATE}"			=>"Patient Occupation State  ",	"{OCCUPATION ZIP}"			=>"Patient Occupation Zip  ",
									 "{MONTHLY INCOME}"				=>"Monthly Income  ",				"{PATIENT INITIAL}"			=>"Patient Initial  ",
									 "{TIME}"						=>"Current Time  ",
									 "{OPERATOR NAME}"				=>"Operator Name  ",				"{OPERATOR INITIAL}"		=>"Operator Initial  ",
									 "{HEARD ABOUT US}"				=>"Heard About Us  ",				"{HEARD ABOUT US DETAIL}"	=>"Heard About Us Detail  ",
									 "{EMAIL ADDRESS}"				=>"Patient E-mail  ",				/*"{USER DEFINE 1}"			=>"User Defined 1  ",
									 "{USER DEFINE 2}"				=>"User Defined 2  ",*/

		
									 "{PRIMARY INSURANCE COMPANY}"			=>"Primary Insurance company  ",		"{PRIMARY POLICY #}"					=>"Primary Policy #  ",
									 "{PRIMARY GROUP #}"					=>"Primary Group #  ",					"{PRIMARY SUBSCRIBER NAME}"				=>"Primary Subscriber Name  ",
									 "{PRIMARY SUBSCRIBER RELATIONSHIP}"	=>"Primary Subscriber Relationship  ",	"{PRIMARY BIRTHDATE}"					=>"Primary Birthdate  ",
									 "{PRIMARY SOCIAL SECURITY}"			=>"Primary Social Security  ",			"{PRIMARY PHONE}"						=>"Primary Phone  ",
									 "{PRIMARY ADDRESS}"					=>"Primary Address  ",					"{PRIMARY CITY}"						=>"Primary City  ",
									 "{PRIMARY STATE}"						=>"Primary State  ",					"{PRIMARY ZIP}"							=>"Primary Zip  ",
									 "{PRIMARY EMPLOYER}"					=>"Primary Employer  ",				"{SECONDARY INSURANCE COMPANY}"			=>"Secondary Insurance company  ",
									 "{SECONDARY POLICY #}"					=>"Secondary Policy #  ",				"{SECONDARY GROUP #}"					=>"Secondary Group #  ",
									 "{SECONDARY SUBSCRIBER NAME}"			=>"Secondary Subscriber Name  ",		"{SECONDARY SUBSCRIBER RELATIONSHIP}"	=>"Secondary Subscriber Relationship  ",
									 "{SECONDARY BIRTHDATE}"				=>"Secondary Birthdate  ",				"{SECONDARY SOCIAL SECURITY}"			=>"Secondary Social Security  ",
									 "{SECONDARY PHONE}"					=>"Secondary Phone  ",					"{SECONDARY ADDRESS}"					=>"Secondary Address  ",
									 "{SECONDARY CITY}"						=>"Secondary City  ",					"{SECONDARY STATE}"						=>"Secondary State  ",
									 "{SECONDARY ZIP}"						=>"Secondary Zip  ",					"{SECONDARY EMPLOYER}"					=>"Secondary Employer  "
		
									);			
		

		$commonPtInfoArr2	 = array("{PATIENT NAME TITLE}"					=>"Patient Name Title  ",				"{PATIENT FIRST NAME}"					=>"Patient First Name  ",
									 "{MIDDLE NAME}"						=>"Patient Middle Name  ",				"{LAST NAME}"							=>"Patient Last Name  ",
									 "{DOB}"								=>"Patient Date of Birth  ",			"{PatientID}"							=>"Patient ID  ",
									 "{ADDRESS1}"							=>"Patient Address1  ",				"{ADDRESS2}"							=>"Patient Address2  ",			
									 "{PATIENT CITY}"						=>"Patient City  ", 					"{STATE ZIP CODE}"						=>"Pt. State Zip Code  ",	
									 "{HOME PHONE}"							=>"Patient Home Phone  ",				"{WORK PHONE}"							=>"Patient Work Phone  ",
									 "{MOBILE PHONE}"						=>"Patient Mobile Phone  ", 			"{PATIENT MRN}"							=>"Patient MRN  ",
									 "{PATIENT MRN2}"						=>"Patient MRN2  ",					"{DATE}"								=>"Date  ",
									 "{LANGUAGE}"							=>"Language  ",	    				"{RACE}"								=>"Race  ",	
									 "{ETHNICITY}"							=>"Ethnicity  ",                    "{PATIENT NAME SUFFIX}"                 =>"Patient Name Suffix   "
									
									
									);

		$commonPtInfoArr3	 = array("{RES.PARTY FIRST NAME}"				=>"Res. Party First Name  ",			
									 "{RES.PARTY LAST NAME}"				=>"Res. Party Last Name  ",			"{RES.PARTY ADDRESS1}"					=>"Res. Party Address1  ",
									 "{RES.PARTY ADDRESS2}"					=>"Res. Party Address2  ",				"{RES.PARTY HOME PH.}"					=>"Res. Party Home Ph.  ",
									 "{RES.PARTY WORK PH.}"					=>"Res. Party Work Ph.  ",				"{RES.PARTY MOBILE PH.}"				=>"Res. Party Mobile Ph.  "
									);
		
		$commonPtInfoArr4	 = array("{REFFERING PHY.}"						=>"Referring Phy.  "			
									);

		$commonPtInfoArr5	 = array("{REF PHYSICIAN FIRST NAME}"	=>"Ref. Physician First Name  ",	"{REF PHYSICIAN LAST NAME}"	=>"Ref. Physician Last Name  ", 
									 "{REF PHY SPECIALITY}"			=>"Ref. Phy Speciality  ",			"{REF PHY PHONE}"			=>"Ref. Phy Phone  ",
									 "{REF PHY STREET ADDR}"		=>"Ref. Phy Address  ",			"{REF PHY CITY}"			=>"Ref. Phy City  ",
									 "{REF PHY STATE}"				=>"Ref. Phy State  ",				"{REF PHY ZIP}"				=>"Ref. Phy Zip  ",
									 "{REF PHY FAX}"				=>"Ref. Phy Fax  "
									);		

		$commonPtInfoArr6	 = array("{PCP STREET ADDR}"			=>"PCP Address  ",					"{PCP City}"				=>"PCP City  ",
									 "{PCP State}"					=>"PCP State  ",					"{PCP ZIP}"					=>"PCP ZIP  ",
									 "{STATE ZIP CODE}"				=>"Pt. State Zip Code  ",			"{REF PHYSICIAN TITLE}"		=>"Ref. Physician Title  ",
									 "{DATE_F}"						=>"Date Format  ",					
									); 
		
		$commonPtInfoArr7 	 = array("{AGE}"						=>"Patient Age  "				
									);
		
		$commonPtInfoArr8	=  array("{FULL ADDRESS}"				=>"Patient Full Address  ", 		"{PATIENT NAME}"			=>"Patient Name  "
									);
		$commonPtInfoArr9 = array("{PHYSICIAN SIGNATURE}"			=>"Physician Signature  ",
								);
		
		$commonPtInfoArr10 	 = array("{PHYSICIAN NAME}"				=>"Physician Name  ", 				"{PHYSICIAN FIRST NAME}"	=>"Physician First Name  ",				
									 "{PHYSICIAN MIDDLE NAME}"		=>"Physician Middle Name  ",		"{PHYSICIAN LAST NAME}"		=>"Physician Last Name  ",
									 "{PHYSICIAN NAME SUFFIX}"		=>"Physician Name Suffix  " 	
									);
		$commonPtInfoArr11 	 = array("{PATIENT PHOTO}"				=>"Patient Photo  ",  			    "{LAST NAME LEN~20~}"		=>"Last Name Len~20~  ",
									 "{PATIENT FIRST NAME LEN~20~}" =>"Patient First Name Len~20~  ",  "{PRIMARY INSURANCE COMPANY LEN~20~}"=>"Primary Insurance Company Len~20~",
									 "{SECONDARY INSURANCE COMPANY LEN~20~}" =>"Secondary Insurance Company Len~20~  ",
									 "{PRIMARY INSURANCE SCAN CARD}"		=> "Primary Insurance Scan Card", 		"{SECONDARY INSURANCE SCAN CARD}"	=> "Secondary Insurance Scan Card"
									 
									);
									
		$commonPtInfoArr12 = array("{REL INFO}"						=>"Release Information  "
								);
		
		$commonPtInfoArr13	= array("{PATIENT STATE}"				=>"Patient State  ");

		$commonPtInfoArr14 	 = array("{PRI INS ADDR}"				=>"Pri. Insurance Address  ",		"{SEC INS ADDR}"				=>"Sec. Insurance Address  "
									);

		$commonPtInfoArr15	 = array("{PATIENT ZIP}"				=>"Patient Zip  "			
									);
		$commonPtInfoArr16	 = array("{RES.PARTY CITY}"				=>"Res. Party City  ",				"{RES.PARTY STATE}"			=>"Res. Party State  ",
									 "{RES.PARTY ZIP}"				=>"Res. Party Zip  ");
									 
		$commonPtInfoArr17	 = array("{PCP NAME}"					=>"PCP Name  ",					"{ALL_INS_CASE}"			=>	"ALL INS CASE  "
									);
		$commonPtInfoArr18	 = array("{PATIENT_SS4}"		    	=>"Patient Last 4 Digit SS Number  ");						
		
		$commonEncounter1 	= array("{DOS}"							=>"Date Of Service  "
									);
		$commonEncounter2	= array(			
									"{DISTANCE}"					=>"Distance  ",					"{MEDICAL DOCTOR}"			=>"PCP  ",
									"{PUPIL OU}"					=>"Pupil OU  ",					"{PUPIL OD}"				=>"Pupil OD  ",
									"{PUPIL OS}"					=>"Pupil OS  ",					"{EXTERNAL OU}"				=>"External OU  ",
									"{EXTERNAL OD}"					=>"External OD  ",					"{EXTERNAL OS}"				=>"External OS  ",
									"{L&A OU}"						=>"L&A OU  ",						"{L&A OD}"					=>"L&A OD  ",
									"{L&A OS}"						=>"L&A OS  ",						"{IOP}"						=>"IOP  ",
									"{IOP OD}"						=>"IOP OD  ",						"{IOP OS}"					=>"IOP OS  ",
									"{GONIO OU}"					=>"Gonio OU  ",					"{GONIO OD}"				=>"Gonio OD  ",
									"{GONIO OS}"					=>"Gonio OS  ",					"{SLE OU}"					=>"SLE OU  ",
									"{SLE OD}"						=>"SLE OD  ",						"{SLE OS}"					=>"SLE OS  ",
									"{FUNDUS EXAM OU}"				=>"Fundus Exam OU  ",				"{FUNDUS EXAM OD}"			=>"Fundus Exam OD  ",
									"{FUNDUS EXAM OS}"				=>"Fundus Exam OS  ",				"{FOLLOW-UP}"				=>"FOLLOW-UP  ",					
									"{CVF OD}"						=>"CVF OD  ",						"{CVF OS}"					=>"CVF OS  ",		
									"{EOM}"							=>"EOM  ",							"{IOP OD WITHOUT PACHY}"	=>"IOP OD WITHOUT PACHY  ",
									"{IOP OS WITHOUT PACHY}"		=>"IOP OS WITHOUT PACHY ",			"{C:D_OD}"					=>"CUP DISC OD ",
									"{C:D_OS}"						=>"CUP DISC OS ", 					"{IRIS_OD}"					=>"IRIS OD ",
									"{IRIS_OS}"						=>"IRIS OS "
									);
		
		
		$commonEncounter3= array("{PRIMARY PHYSICIAN}"			=>"Primary Physician  ",			"{PINHOLE OD}"				=>"Pinhole OD  ",
								 "{PINHOLE OS}"					=>"Pinhole OS  ",					"{SLE}"						=>"SLE  ",
								 "{CONJ_OD}"					=>"CONJ OD  ",						"{CONJ_OS}"					=>"CONJ OS  ",
								 "{CORNEA_OD}"					=>"CORNEA OD  ",					"{CORNEA_OS}"				=>"CORNEA OS  ",
								 "{ANTCHAMBER_OD}"				=>"ANTCHAMBER OD  ",				"{ANTCHAMBER_OS}"			=>"ANTCHAMBER OS  ",
								 "{LENS_OD}"					=>"LENS OD  ",						"{LENS_OS}"					=>"LENS OS  ",
								 "{FUNDUS}"						=>"FUNDUS  ",						"{OPTICNERVE_OD}"			=>"OPTICNERVE OD   ",
								 "{OPTICNERVE_OS}"				=>"OPTICNERVE OS  ",				"{MACULA_OD}"				=>"MACULA OD  ",
								 "{MACULA_OS}"					=>"MACULA OS  ",					"{VITREOUS_OD}"				=>"VITREOUS OD  ",
								 "{VITREOUS_OS}"				=>"VITREOUS OS  ",					"{PERIPHERY_OD}"			=>"PERIPHERY OD  ",
								 "{PERIPHERY_OS}"				=>"PERIPHERY OS  ",				"{BV_OD}"					=>"BLOOD VESSELS OD  ",
								 "{RETINAL_EX_OD}"				=>"RETINAL EX OD  ",				"{RETINAL_EX_OS}"			=>"RETINAL EX OS  ",
								 "{BV_OS}"						=>"BLOOD VESSELS OS  "
								);		
		

		$commonEncounter4 = array("{A & P}"						=>"A & P  "
								);
		
		$commonEncounter5	= array( "{SIGNATURE}"					=>"Signature  ",						
									 "{TEXTBOX_XSMALL}"				=>"Very Small Textbox  ",		"{TEXTBOX_SMALL}"			=>"Small Textbox  ",
									 "{TEXTBOX_MEDIUM}"				=>"Medium Textbox  ",				
									);
		
		$commonEncounter6	= array("{OD SPHERICAL}"	=>"OD SPHERICAL  ",	"{OD CYLINDER}"		=>"OD CYLINDER  ",
									"{OD AXIS}"			=>"OD AXIS  ",			"{OD BASE CURVE}"	=>"OD BASE CURVE  ",		
								    "{OD ADD}"			=>"OD ADD  ",			"{OD COLOR}"		=>"OD COLOR  ",			
									"{OS SPHERICAL}"	=>"OS SPHERICAL  ",	"{OS CYLINDER}"		=>"OS CYLINDER  ",
									"{OS AXIS}"			=>"OS AXIS  ",			"{OS BASE CURVE}"	=>"OS BASE CURVE  ",
									"{OS ADD}"			=>"OS ADD  ",			"{OS COLOR}"		=>"OS COLOR  ",
									"{OU SPHERICAL}"	=>"OU SPHERICAL  ",	"{OU CYLINDER}"		=>"OU CYLINDER  ",
									"{OU AXIS}"			=>"OU AXIS  ",			"{OU BASE CURVE}"	=>"OU BASE CURVE  ",
									"{OU ADD}"			=>"OU ADD  ",			"{OU COLOR}"		=>"OU COLOR  ",
									"{EXPIRATION DATE}"	=>"Expiration Date  ", "{PHYSICIAN NPI}"	=>"PHYSICIAN NPI  "
							);
		$commonEncounter7	= array("{NOTES}"			=>"Print Notes   "	
							);
		
		
		$commonEncounter8 = array("{V-SC-OD}"				=>"V-SC-OD  ",					"{V-SC-OS}"				=>"V-SC-OS  ",
								  "{INITIAL IOP}"			=>"Initial IOP  ",				"{INITIAL IOP TIME}"	=>"Initial IOP Time  ",
								  "{POST IOP}"				=>"Post IOP  ",				"{POST IOP TIME}"		=>"Post IOP Time  ",
								  "{OCULAR HISTORY}"		=>"Ocular History  ",			"{SITE}"				=>"Patient Site  ",
								  "{VITAL SIGN}"			=>"Vital Sign  ",				"{SCRIBED BY}"			=>"Scribed By  ",
							      "{NEAR_OD}"				=>"NEAR OD  ",					"{NEAR_OS}"				=>"NEAR OS  ",					  "{PACHYMETRY OD}"			=>"PACHYMETRY OD  ",				"{PACHYMETRY OS}"						=>"PACHYMETRY OS  ",				"{KERATOMETRY OD}"				=>"KERATOMETRY OD  ",				"{KERATOMETRY OS}"							=>"KERATOMETRY OS  "
							);
		
		$commonEncounter9 = array("{MR1}"					=>"MR1  ",						"{MR2}"					=>"MR2  ",
								  "{MR3}"					=>"MR3  ",						"{DIABETES}"			=>"Diabetes  ",
								  "{FLASHES}"				=>"Flashes  ",					"{FLOATERS}"			=>"Floaters  ",
								  "{SMOKER}"				=>"Smokers  ",					"{FAMILY HX SMOKE}"		=>"Family Hx Smoke  ",
								  "{EMP ID}"				=>"EMP ID  ",					"{MR1 GLARE OD}"		=>"MR1 GLARE OD  ",
								  "{MR1 GLARE OS}"			=>"MR1 GLARE OS  ",			"{MR2 GLARE OD}"		=>"MR2 GLARE OD  ",
								  "{MR2 GLARE OS}"			=>"MR2 GLARE OS  ",			"{MR3 GLARE OD}"		=>"MR3 GLARE OD  ",
								  "{MR3 GLARE OS}"			=>"MR3 GLARE OS  "
							);
		$commonEncounter10	= array("{APPT_HX}"				=>"Appointment History   ",	"{ASSESSMENT OD}"		=>"Assessment OD  ",
									"{ASSESSMENT OS}"		=>"Assessment OS  ",			"{PLAN OS}"				=>"Plan OS  ",
									"{PLAN OD}"				=>"Plan OD  ",					"{OCULAR OD}"			=>"Ocular OD  ",
									"{OCULAR OS}"		 	=>"Ocular OS  ",				"{OCULAR}"		 		=>"Ocular  ",
									"{NEAR OS}"				=>"Near OS  ",					"{NEAR OD}"				=>"Near OD  ",
									"{CYL OS}"				=>"CYL OS  ",					"{CYL OD}"				=>"CYL OD  ",
									"{DISTANCE OS}"			=>"Distance OS  ",				"{DISTANCE OD}"			=>"Distance OD  ",
									"{MR1 OD}"				=>"MR1 OD  ",					"{MR1 OS}"				=>"MR1 OS  ",
									"{BCVA OD}"				=>"BCVA OD  ",					"{BCVA OS}"				=>"BCVA OS  ");
									
		$commonEncounter11	= array("{GLARE}"				=>"Glare   ",					
									"{PATIENT ALLERGIES}"	=>"Patient Allergies  ",		"{MEDICAL PROBLEM}"		=>"Medical Problem   "
							);

		$commonEncounter12	= array("{GLARE_K_OD}"			=>"Glare K OD   ",				"{GLARE_K_OS}"			=>"Glare K OS  ",	
									"{MR GIVEN}"			=>"MR Given  ",	
									"{OPERATIVE EYE}"		=>"Operative Eye   ",			"{UNOPERATIVE EYE}"		=>"Unoperative Eye   ",
									"{OCULAR PATHOLOGY}"	=>"Ocular Pathology   ",		"{DOMINANT EYE}"		=>"Dominant Eye   ",
									"{LRI}"					=>"LRI   ",					"{LENS SELECTION}"		=>"Lens Selection   ",
									"{APPT SITE}"			=>"Appt Site  ",
									"{PCP TITLE NAME}"		=>"PCP Title Name  ",			"{PCP FIRST NAME}"		=>"PCP First Name  ",
									"{PCP MIDDLE NAME}"		=>"PCP Middle Name  ",			"{PCP LAST NAME}"		=>"PCP Last Name  ",
									"{PCP CREDENTIAL}"		=>"PCP Credential  ",			"{PCP FULL NAME}"		=>"PCP Full Name  "
									
							);

		$commonEncounter13	= array("{PC1}"					=>"PC1   ",					"{PC2}"					=>"PC2  ",	
									"{PC3}"					=>"PC3   "
							);
		$commonEncounter14	= array("{APPT DATE}"			=>"Appt Date  ",				"{APPT TIME}"			=>"Appt Time  ",
									"{APPT PROC}"			=>"Appt Procedure  "
							);
		$commonEncounter15	= array("{APPT COMMENTS}"		=>"Appt Comments  ",			"{PT-KEY}"			=>"PT-Key  ",
									"{PT_DUE}"				=>"PT-Due  ",					"{PT_COPAY}"		=>"PT-Copay  ",					"{TOTAL_DUE}"			=>"Total-Due  ",		"{PHYSICIAN NPI}" =>"PHYSICIAN NPI:"
							);
		
		$commonEncounter16	= array("{APPT PROVIDER}"		=>"Appt Provider  ",			"{APPT PROVIDER LAST NAME}"	=>"Appt Provider Last Name  ",
									"{APPT DATE_F}"			=>"Appt Date Format  ",		"{APPT FACILITY}"			=>"Appt Facility  ",	
									"{APPT FACILITY PHONE}"	=>"Appt Facility Phone  "
									);
									
		$commonEncounter17 = array("{ASSESSMENT & PLAN}"	=>"Assessment & Plan  ",		"{ASSESSMENT 1}"			=>"Assessment 1  ",
								   "{ASSESSMENT 2}"			=>"Assessment 2  ",			"{ASSESSMENT 3}"			=>"Assessment 3  ",
								   "{ASSESSMENT 4}"			=>"Assessment 4  ",			"{ASSESSMENT 5}"			=>"Assessment 5  ",
								   "{PLAN 1}"				=>"Plan 1  ",					"{PLAN 2}"					=>"Plan 2  ",
								   "{PLAN 3}"				=>"Plan 3  ",					"{PLAN 4}"					=>"Plan 4  ",
								   "{ASSESSMENT ALL}"		=>"ASSESSMENT ALL  ",			"{PLAN ALL}"				=>"PLAN ALL  "					
								 
								);
		$commonEncounter18	= array("{PRIMARY LICENCE NUMBER}"=>"Primary Licence Number  ");
		$commonEncounter19	= array("{A & P_V}"=>"A & P_V  ");
		
		$commonEncounter20	= array("{MED HX}"				=>"Medical History  ", "{OCULAR MEDICATION}"	=>"Ocular Medication  ",
									"{SYSTEMIC MEDICATION}"	=>"Systemic Medication  ");
		$commonEncounter21	= array("{CC}"=>"CC  ","{HISTORY}" => "HISTORY  ");
		$commonEncounter22	= array("{APPT_FUTURE}"			=>"Appointment Future   ");
		$commonEncounter23	= array("{V-CC-OD}"				=>"V-CC-OD  ",	"{V-CC-OS}"	=>	"V-CC-OS  ");
		$commonEncounter24	= array("{APPT FACILITY NAME}" =>"APPT FACILITY NAME", "{APPT FACILITY ADDRESS}" =>"APPT FACILITY ADDRESS", 						   "{APPT FACILITY PHONE}" =>"APPT FACILITY PHONE :");
		$commonEncounter25	= array("{APPT DATE}"		=>"Appt Date  ",	"{APPT DATE_F}"		=>	"Appt Date Format  ",							"{APPT PROVIDER}"	 =>"Appt Provider ", "{PT_DISCUSSION}"	 =>" Patient Discussion "
								);
		$commonEncounter26	= array("{LOGGED_IN_FACILITY_NAME}" =>"Logged In Facility Name", "{LOGGED_IN_FACILITY_ADDRESS}" =>"Logged In Facility Address");
		$commonEncounter27	= array("{V-CC-OU}" => "V-CC-OU", "{V-SC-OU}" => "V-SC-OU", "{NEAR_OU}" => "NEAR OU");
		$commonEncounter28	= array("{PAM_OD}" => "PAM OD", "{PAM_OS}" => "PAM OS", "{PAM_OU}" => "PAM OU", "{BAT_OD}" => "BAT OD", "{BAT_OS}" => "BAT OS", "{BAT_OU}" => "BAT OU", "{AR_OD}" => "AUTOREFRACTION OD", "{AR_OS}" => "AUTOREFRACTION OS", "{CYCLOPLEGIC_OD}" => "CYCLOPLEGIC OD", "{CYCLOPLEGIC_OS}" => "CYCLOPLEGIC OS","{SC_DVA_OD}" => "SC DVA OD :", "{SC_DVA_OS}" => "SC DVA OS :", "{CC_DVA_OD}" => "CC DVA OD :", "{CC_DVA_OS}" => "CC DVA OS :", "{SC_NVA_OD}" => "SC NVA OD :", "{SC_NVA_OS}" => "SC NVA OS :", "{CC_NVA_OD}" => "CC NVA OD :", "{CC_NVA_OS}" => "CC NVA OS :", "{PINHOLE_OD_1}" => "PINHOLE OD :", "{PINHOLE_OS_1}" => "PINHOLE OS :");
		
		$commonEncounter29	= array("{VISION PRIMARY INSURANCE COMPANY}" => "VISION PRIMARY INSURANCE COMPANY:", "{VISION PRIMARY POLICY #}" => "VISION PRIMARY POLICY #:", "{VISION PRIMARY GROUP #}" => "VISION PRIMARY GROUP #:", "{VISION PRIMARY SUBSCRIBER NAME}" => "VISION PRIMARY SUBSCRIBER NAME:", "{VISION PRIMARY SUBSCRIBER RELATIONSHIP}" => "VISION PRIMARY SUBSCRIBER RELATIONSHIP:", "{VISION PRIMARY BIRTHDATE}" => "VISION PRIMARY BIRTHDATE:", "{VISION PRIMARY SOCIAL SECURITY}" => "VISION PRIMARY SOCIAL SECURITY:", "{VISION PRIMARY PHONE}" => "VISION PRIMARY PHONE:", "{VISION PRIMARY ADDRESS}" => "VISION PRIMARY ADDRESS:", "{VISION PRIMARY CITY}" => "VISION PRIMARY CITY:", "{VISION PRIMARY STATE}" => "VISION PRIMARY STATE:", "{VISION PRIMARY ZIP}" => "VISION PRIMARY ZIP:", "{VISION PRIMARY EMPLOYER}" => "VISION PRIMARY EMPLOYER:", "{VISION SECONDARY INSURANCE COMPANY}" => "VISION SECONDARY INSURANCE COMPANY:", "{VISION SECONDARY POLICY #}"  => "VISION SECONDARY POLICY #:","{VISION SECONDARY GROUP #}" => "VISION SECONDARY GROUP #:", "{VISION SECONDARY SUBSCRIBER NAME}" => "VISION SECONDARY SUBSCRIBER NAME:", "{VISION SECONDARY SUBSCRIBER RELATIONSHIP}" => "VISION SECONDARY SUBSCRIBER RELATIONSHIP:", "{VISION SECONDARY BIRTHDATE}" => "VISION SECONDARY BIRTHDATE:", "{VISION SECONDARY SOCIAL SECURITY}" => "VISION SECONDARY SOCIAL SECURITY:", "{VISION SECONDARY PHONE}" => "VISION SECONDARY PHONE:", "{VISION SECONDARY ADDRESS}" => "VISION SECONDARY ADDRESS:", "{VISION SECONDARY CITY}" => "VISION SECONDARY CITY:", "{VISION SECONDARY STATE}" => "VISION SECONDARY STATE:", "{VISION SECONDARY ZIP}" => "VISION SECONDARY ZIP:", "{VISION SECONDARY EMPLOYER}" => "VISION SECONDARY EMPLOYER:", "{VISION PRI INS ADDR}" => "VISION PRI INS ADDR:", "{VISION SEC INS ADDR}" => "VISION SEC INS ADDR:", "{CO MANAGED PHY.}" => "CO MANAGED PHY.:","{FOLLOW-UP}"		=>"FOLLOW-UP");	
		
		$commonEncounter30	= array("{PATIENT_NEXT_APPOINTMENT_DATE}" =>"Patient Next Appointment Date", 
									"{PATIENT_NEXT_APPOINTMENT_TIME}" =>"Patient Next Appointment Time",
									"{PATIENT_NEXT_APPOINTMENT_PROVIDER}" =>"Patient Next Appointment Provider",
									"{PATIENT_NEXT_APPOINTMENT_LOCATION}" =>"Patient Next Appointment Location",
									"{PATIENT_NEXT_APPOINTMENT_PRIREASON}" =>"Patient Next Appointment PriReason",
									"{PATIENT_NEXT_APPOINTMENT_SECREASON}" =>"Patient Next Appointment SecReason", 
									"{PATIENT_NEXT_APPOINTMENT_TERREASON}" =>"Patient Next Appointment TerReason",
									"{PATIENT_NICK_NAME}" =>"Patient Nick Name"
									);
		
		$commonEncounter31	= array("{ROS_SUMMARY}" =>"ROS SUMMARY", 
									"{ROS_DETAIL}" =>"ROS DETAIL",
									"{ROS_EYES}" =>"ROS EYES",
									"{ROS_CONSTITUTIONAL}" =>"ROS CONSTITUTIONAL",
									"{ROS_INTEGUMENTARY}" =>"ROS INTEGUMENTARY",
									"{ROS_EARNOSEMOUTHTHROAT}" =>"ROS EAR,NOSE,MOUTH,THROAT", 
									"{ROS_CARDIOVASCULAR}" =>"ROS CARDIOVASCULAR",
									"{ROS_RESPIRATORY}" =>"ROS RESPIRATORY",
									"{ROS_GASTROINTESTINAL}" =>"ROS GASTROINTESTINAL",
									"{ROS_GENITOURINARY}" =>"ROS GENITOURINARY",
									"{ROS_NEUROLOGICAL}" =>"ROS NEUROLOGICAL",
									"{ROS_MUSCULOSKELETAL}" =>"ROS MUSCULOSKELETAL",
									"{ROS_PSYCHIATRIC}" =>"ROS PSYCHIATRIC",
									"{ROS_HEMOTOLOGICLYMPHATIC}" =>"ROS HEMOTOLOGIC/LYMPHATIC",
									"{ROS_ALLERGICIMMUNOLOGIC}" =>"ROS ALLERGIC/IMMUNOLOGIC",
									"{ROS_ENDOCRINE}" =>"ROS ENDOCRINE",
									"{GFS_TARGET_IOP_OD}" =>"GFS TARGET IOP OD",
									"{GFS_TARGET_IOP_OS}" =>"GFS TARGET IOP OS"
									);	
		
		$commonEncounter32	= array("{OCULAR_MED_WITH_LOT}"	=>"Ocular Medication With Lot", 
									"{SYSTEMIC_MED_WITH_LOT}"=>"Systemic Medication With Lot"
									);	
		$commonEncounter33	= array("{ARRIVAL_TIME}" => "Arrival Time");
		//Statement variables
		$stInfoArr = array(
			"{Patient ID}" => "Patient ID",
			"{PATIENT FIRST NAME}" => "Patient First Name",
			"{PATIENT MIDDLE NAME}" => "Patient Middle Name",
			"{PATIENT LAST NAME}" => "Patient Last Name",
			"{PATIENT ADDRESS}" => "Patient Address",
			"{PATIENT CITY}" => "Patient City",
			"{PATIENT STATE}" => "Patient State",
			"{PATIENT ZIP}" => "Patient Zip",
			"{GROUP NAME}" => "Group Name",
			"{GROUP ADDRESS}" => "Group Address",
			"{GROUP CITY}" => "Group City",
			"{GROUP STATE}" => "Group State",
			"{GROUP ZIP}" => "Group Zip",
			"{GROUP PHONE}" => "Group Phone",
			"{GROUP FAX}" => "Group Fax",
			"{STATEMENT REQUEST}" => "Statement Request",
			"{STATEMENT DATE}" => "Statement Date",
			"{DOS}" => "DOS",
			"{CPT}" => "CPT",
			"{CPT DESCRIPTION}" => "CPT Description",
			"{UNIT}" => "Unit",
			"{CHARGES}" => "Charges",
			"{INSURANCE PAID}" => "Insurance Paid",
			"{PT PAID}" => "Pt Paid",
			"{ADJUSTMENT}" => "Adjustment",
			"{BALANCE}" => "Balance",
			"{TOTAL UNIT}" => "Total Unit",
			"{TOTAL CHARGES}" => "Total Charges",
			"{TOTAL INSURANCE PAID}" => "Total Insurance Paid",
			"{TOTAL PT PAID}" => "Total Pt Paid",
			"{TOTAL ADJUSTMENT}" => "Total Adjustment",
			"{TOTAL BALANCE}" => "Total Balance",
			"{COMMENT}" => "Comment",
			"{NEW BALANCE SINCE LAST BILL}" => "New Balance Since Last Bill",
			"{NEW PAYMENT SINCE LAST BILL}" => "New Payment Since Last Bill",
			"{INSURANCE PAID SINCE LAST BILL}" => "Insurance Paid Since Last Bill",
			"{RESPONSIBLE NAME}" => "Responsible Name",
			"{RESPONSIBLE ADDRESS}" => "Responsible Address",
			"{RESPONSIBLE CITY}" => "Responsible City",
			"{RESPONSIBLE STATE}" => "Responsible State",
			"{RESPONSIBLE ZIP}" => "Responsible Zip",
			"{MIN PAY DUE IN STATEMENT}" => "Min Pay Due in Statement",
			"{PHYSICIAN NAME TITLE}" => "Physician Name Title",
			"{PHYSICIAN FIRST NAME}" => "Physician First Name",
			"{PHYSICIAN MIDDLE NAME}" => "Physician Middle Name",
			"{PHYSICIAN LAST NAME}" => "Physician Last Name",
			"{PHYSICIAN NAME SUFFIX}" => "Physician Name Suffix",
		);
		
		// Surgery Consent Varibales 
		$surgery_con_arr = array(
			"{PATIENT NAME TITLE}" => "Patient Name Title",
			"{PATIENT FIRST NAME}" => "Patient First Name",
			"{MIDDLE NAME}" => "Middle Name",
			"{LAST NAME}" => "Last Name",
			"{SEX}" => "Gender information",
			"{DOB}" => "Date of Birth",
			"{PATIENT SS}" => "SS Number",
			"{PHYSICIAN NAME}" => "Provider Name",
			"{MARITAL STATUS}" => "Marital Status",
			"{ADDRESS1}" => "Address1",
			"{ADDRESS2}" => "Address2",
			"{HOME PHONE}" => "Home Phone",
			"{EMERGENCY CONTACT}" => "Emergency Contact",
			"{EMERGENCY CONTACT PH}" => "Emergency Contact Ph.",
			"{MOBILE PHONE}" => "Mobile Phone",
			"{WORK PHONE}" => "Work Phone",
			"{PATIENT CITY}" => "Patient City",
			"{PATIENT STATE}" => "Patient State",
			"{PATIENT ZIP}" => "Patient Zip",
			"{REGISTRATION DATE}" => "Registration Date",
			"{REFFERING PHY.}" => "Referring Phy.",
			"{POS FACILITY}" => "Facility",
			"{DRIVING LICIENSE}" => "Driving Linciense",
			"{RES.PARTY TITLE}" => "Res. Party Title",
			"{RES.PARTY FIRST NAME}" => "Res. Party First Name",
			"{RES.PARTY MIDDLE NAME}" => "Res. Party Middle Name",
			"{RES.PARTY LAST NAME}" => "Res. Party Last Name",
			"{RES.PARTY DOB}" => "Res. Party DOB",
			"{RES.PARTY SS}" => "Res. Party SS",
			"{RES.PARTY SEX}" => "Res. Party Gender",
			"{RES.PARTY RELATION}" => "Res. Party Relation",
			"{RES.PARTY ADDRESS1}" => "Res. Party Address1",
			"{RES.PARTY ADDRESS2}" => "Res. Party Address2",
			"{RES.PARTY HOME PH.}" => "Res. Party Home Ph.",
			"{RES.PARTY WORK PH.}" => "Res. Party Work Ph.",
			"{RES.PARTY MOBILE PH.}" => "Res. Party Mobile Ph.",
			"{RES.PARTY CITY}" => "Res. Party City",
			"{RES.PARTY STATE}" => "Res. Party State",
			"{RES.PARTY ZIP}" => "Res. Party Zip",
			"{RES.PARTY MARITAL STATUS}" => "Res. Party Marital Status",
			"{RES.PARTY DD NUMBER}" => "Res. Party DD Number",
			"{PATIENT OCCUPATION}" => "Patient Occupation",
			"{PATIENT EMPLOYER}" => "Patient Employer",
			"{OCCUPATION ADDRESS1}" => "Occupation Address1",
			"{OCCUPATION ADDRESS2}" => "Occupation Address2",
			"{OCCUPATION CITY}" => "Occupation City",
			"{OCCUPATION STATE}" => "Occupation State",
			"{OCCUPATION ZIP}" => "Occupation Zip",
			"{MONTHLY INCOME}" => "Monthly Income",
			"{SIGNATURE}" => "Signature",
			"{ASSISTANT_SURGEON_SIGNATURE}" => "Assistant Surgeon Signature",
			"{START APPLET ROW}" => "Multiple Applet in row",
			"{DATE}" => "Date (mm-dd-yyyy)",
			"{TIME}" => "Current time",
			"{OPERATOR NAME}" => "Operator Name",
			"{OPERATOR INITIAL}" => "Operator Initial",
			"{PATIENT INITIAL}" => "Patient Initial",
			"{TEXTBOX_SMALL}" => "Small Textbox",
			"{TEXTBOX_LARGE}" => "Large Textbox",
			"{TEXTBOX_MEDIUM}" => "Medium Textbox",
			"{TEXTBOX_XSMALL}" => "Xsmall Textbox",
			"{PROCEDURE}" => "Procedure",
			"{SITE}" => "Site",
			"{SURGEON NAME}" => "Surgeon Name",
			"{SURGEON SIGNATURE}" => "Surgeon Signature",
			"{PHYSICIAN SIGNATURE}" => "Physician Signature",
			"{WITNESS SIGNATURE}" => "Witness Signature",
			"{APPT_FUTURE}" => "FUTURE APPOINTMENT",
			"{PATIENT_SS4}" => "Patient SS Last 4 digit",
			"{PATIENT_NEXT_APPOINTMENT_DATE}" => "Patient Next Appointment Date",
			"{PATIENT_NEXT_APPOINTMENT_TIME}" => "Patient Next Appointment Time",
			"{PATIENT_NEXT_APPOINTMENT_PROVIDER}" => "Patient Next Appointment Provider",
			"{PATIENT_NEXT_APPOINTMENT_LOCATION}" => "Patient Next Appointment Location",
			"{PATIENT_NEXT_APPOINTMENT_PRIREASON}" => "Patient Next Appointment PriReason",
			"{PATIENT_NEXT_APPOINTMENT_SECREASON}" => "Patient Next Appointment SecReason",
			"{PATIENT_NEXT_APPOINTMENT_TERREASON}" => "Patient Next Appointment TerReason",
			"{PATIENT_NICK_NAME}" =>"Patient Nick Name"
		);
		
		switch($sec_name){
			//Consent Forms
			case 'consent':
				//Patient variables
				$return_arr['Smart Tag'] = $this->get_smart_tags();
				$return_arr['Patient'] = array_merge($commonPtInfoArr1,$commonPtInfoArr2,$commonPtInfoArr3,$commonPtInfoArr4,$commonPtInfoArr5,$commonPtInfoArr6,$commonPtInfoArr7,$commonPtInfoArr9,$commonPtInfoArr10,$commonPtInfoArr12,$commonPtInfoArr13,$commonPtInfoArr14,$commonPtInfoArr15,$commonPtInfoArr16,$commonPtInfoArr17,$commonPtInfoArr18,$commonEncounter20,$commonEncounter21,$commonEncounter26,$commonEncounter30);
				
				//Consent form variables
				$return_arr['Consent Form'] = array(
									"{START APPLET ROW}"	=>	"Multiple Applet in row  ",
									"{TEXTBOX_LARGE}"		=>"Large Textbox  ",			
									"{WITNESS SIGNATURE}"	=>"Witness Signature  ",
									"{SITE}"				=>"Patient Site  ",
									"{PATIENT ALLERGIES}"	=>"Patient Allergies  ",
									"{OCULAR MEDICATION}"	=>"Ocular Medication  ",
									"{SYSTEMIC MEDICATION}"	=>"Systemic Medication"	
								);
				$return_arr['Consent Form']	= array_merge($return_arr['Consent Form'],$commonEncounter5,$commonEncounter14,$commonEncounter33);
				$return_arr['Logo'] = $this->logo_data['logo'];
				return $return_arr;
			break;
			
			case 'consult_letter':
				$return_arr['Smart Tag'] = $this->get_smart_tags();
				//Patient variables
				$return_arr['Patient'] = array_merge($commonPtInfoArr1,$commonPtInfoArr2,$commonPtInfoArr3,$commonPtInfoArr4,$commonPtInfoArr5,$commonPtInfoArr6,$commonPtInfoArr7,$commonPtInfoArr9,$commonPtInfoArr10,$commonPtInfoArr13,$commonPtInfoArr14,$commonPtInfoArr15,$commonPtInfoArr16,$commonPtInfoArr18,$commonEncounter26,$commonEncounter30);
				
				//Consult letter variables
				$return_arr['Consult Letter'] = array( 
									"{ADDRESSEE}"			=>	"Addressee  ",
									"{ADDRESSEE_ADDRESS}"	=>	"Addressee Address  ",
									"{ADDRESSEE_PHONE}"	=>	"Addressee Phone  ",
									"{ADDRESSEE_FAX}"	=>	"Addressee Fax  ",
									"{ADDRESSEE_SPECIALTY}"	=>	"Addressee Specialty  ",
									"{CC1}"					=>	"Cc1  ",
									"{CC1_ADDRESS}"			=>	"Cc1 Address  ",
									"{CC2}"					=>	"Cc2  ",
									"{CC2_ADDRESS}"			=>	"Cc2 Address  ",
									"{CC3}"					=>	"Cc3  ",
									"{CC3_ADDRESS}"			=>	"Cc3 Address  ",
									"{L&A ALL}"				=>	"L&A ALL",
									"{SLE ALL}"				=>	"SLE ALL",
									"{FUNDUS ALL}"			=>	"FUNDUS ALL",
									"{GENERAL HEALTH}"		=>	"GENERAL HEALTH",
									"{CO MANAGED PHY.}"		=>	"CO MANAGED PHYSICIAN",
									"{CPT_MOD}"				=>	"CPT MOD"
								);
				$return_arr['Consult Letter'] = array_merge($return_arr['Consult Letter'],$commonEncounter1,$commonEncounter2,$commonEncounter3,$commonEncounter4,$commonEncounter8,$commonEncounter9,$commonEncounter11,$commonEncounter13,$commonEncounter17,$commonEncounter19,$commonEncounter20,$commonEncounter21,$commonEncounter22,$commonEncounter23,$commonEncounter27,$commonEncounter28,$commonEncounter31,$commonEncounter32,$commonEncounter33);
				
				$return_arr['Logo'] = $this->logo_data['logo'];
				return $return_arr;
			break;
			
			case 'order_temp':
			$return_arr['Smart Tag'] = $this->get_smart_tags();
				//Patient variables
				$return_arr['Patient'] = array_merge($commonPtInfoArr1,$commonPtInfoArr2,$commonPtInfoArr3,$commonPtInfoArr4,$commonPtInfoArr5,$commonPtInfoArr6,$commonPtInfoArr7,$commonPtInfoArr9,$commonPtInfoArr10,$commonPtInfoArr13,$commonPtInfoArr14,$commonPtInfoArr15,$commonPtInfoArr16,$commonPtInfoArr18,$commonEncounter26);
				
				//Consult letter variables
				$return_arr['Order Template'] = array( 
									"{ADDRESSEE}"			=>	"Addressee  ",
									"{ADDRESSEE_ADDRESS}"	=>	"Addressee Address  ",
									"{CC1}"					=>	"Cc1  ",
									"{CC1_ADDRESS}"			=>	"Cc1 Address  ",
									"{CC2}"					=>	"Cc2  ",
									"{CC2_ADDRESS}"			=>	"Cc2 Address  ",
									"{CC3}"					=>	"Cc3  ",
									"{CC3_ADDRESS}"			=>	"Cc3 Address  ",
									"{L&A ALL}"				=>	"L&A ALL",
									"{SLE ALL}"				=>	"SLE ALL",
									"{FUNDUS ALL}"			=>	"FUNDUS ALL",
									"{GENERAL HEALTH}"		=>	"GENERAL HEALTH",
									"{CO MANAGED PHY.}"		=>	"CO MANAGED PHYSICIAN"								
								);
				$return_arr['Order Template'] = array_merge($return_arr['Order Template'],$commonEncounter1,$commonEncounter2,$commonEncounter3,$commonEncounter4,$commonEncounter8,$commonEncounter9,$commonEncounter11,$commonEncounter13,$commonEncounter17,$commonEncounter19,$commonEncounter20,$commonEncounter21,$commonEncounter22,$commonEncounter23,$commonEncounter27,$commonEncounter28);
				
				return $return_arr;
			break;
			
			case 'collection':
				//Patient variables
				$return_arr['Patient'] = array(
							"{FULL NAME}"		=>	"Pt. Full Name  ",
							"{SUFFIX}"			=>	"Pt. Suffix  ",			
							"{RES FULL NAME}"	=>	"Res. Full Name  ",
							"{RES SUFFIX}"		=>	"Res. Suffix  "
						);
				$return_arr['Patient']	= array_merge($return_arr['Patient'],$commonPtInfoArr2,$commonPtInfoArr3,$commonPtInfoArr16);
				
				//Collection letter variables
				$return_arr['Collection Letter'] = array(
							"{TOTAL OUTSTANDING CHARGES}"	=>	"Total Outstanding Charges  ",
							"{CHARGES}"						=>	"Charges  ",
							"{DOS & CHARGES}"				=>	"DOS & Charges  "				
						);
				$return_arr['Collection Letter'] = array_merge($return_arr['Collection Letter'],$commonEncounter1,$commonEncounter2,$commonEncounter17,$commonEncounter21);
				$return_arr['Logo'] = $this->logo_data['logo'];
				return $return_arr;
			break;
			
			case 'education':
				$return_arr['Smart Tag'] = $this->get_smart_tags();
				$return_arr['Patient'] = array_merge($commonPtInfoArr2,$commonPtInfoArr4,$commonPtInfoArr5,$commonPtInfoArr6,$commonPtInfoArr7,$commonPtInfoArr9,$commonPtInfoArr10,$commonPtInfoArr14,$commonEncounter26,$commonEncounter30);

				$return_arr[$this->get_header_title($this->current_tab)] = array_merge($commonEncounter1,$commonEncounter2,$commonEncounter3,$commonEncounter4,$commonEncounter9,$commonEncounter11,$commonEncounter13,$commonEncounter17,$commonEncounter20,$commonEncounter21,$commonEncounter23,$commonEncounter25);
				$return_arr['Logo'] = $this->logo_data['logo'];
			break;
			
			case 'prescription_contact_lens':
				$return_arr['Smart Tag'] = $this->get_smart_tags();
				$return_arr['Patient'] = array_merge($commonPtInfoArr2,$commonPtInfoArr7,$commonPtInfoArr8,$commonPtInfoArr10);
				$return_arr['Contact Lens'] = array(
									   "{OD DIAMETER}"		=>"OD DIAMETER : ",		"{OS DIAMETER}"		=>"OS DIAMETER : ",	
									   "{MAKE}"				=>"MAKE : ",			"{MAKE OS}"			=>"MAKE OS : ",	
									   "{MAKE OD}"			=>"MAKE OD : ",			"{MAKE OU}"			=>"MAKE OU : ",
									   "{DVA OD}"			=>"DVA OD : ",			"{DVA OS}"			=>"DVA OS : ",
									   "{DVA OU}"			=>"DVA OU : ",
									   "{NVA OD}"			=>"NVA OD : ",			"{NVA OS}"			=>"NVA OS : ",
									   "{NVA OU}"			=>"NVA OU : ",
									   "{2 Degree/W OD}"	=>"2 Degree/W OD : ",	"{2 Degree/W OS}"	=>"2 Degree/W OS : ",
									   "{3 Degree/W OD}"	=>"3 Degree/W OD : ",	"{3 Degree/W OS}"	=>"3 Degree/W OS : ",
									   "{PC/W OD}"			=>"PC/W OD : ",			"{PC/W OS}"			=>"PC/W OS : ",
									   "{BLEND OD}"			=>"BLEND OD : ",		"{BLEND OS}"		=>"BLEND OS : ",
									   "{EDGE OD}"			=>"EDGE OD : ",			"{EDGE OS}"			=>"EDGE OS : ",
									   "{POWER OD}"			=>"POWER OD : ",		"{POWER OS}"		=>"POWER OS",
									   "{CL COMMENT}"		=>"CL COMMENT : ",		"{REPLENISHMENT}"	=>"REPLENISHMENT : ",
									   "{WEAR SCHEDULER}"	=>"WEAR SCHEDULER : ",	"{DISINFECTING}" 	=>"DISINFECTING : "
								);	
				$return_arr['Glasses'] = array(
									"{OD PRISM}"			=>"OD PRISM : ",				"{OS PRISM}"			=>	"OS PRISM : ",
									"{OD HORIZONTAL PRISM}" =>"OD HORIZONTAL PRISM : ",		"{OS HORIZONTAL PRISM}" =>	"OS HORIZONTAL PRISM : ",
									"{OD VERTICAL PRISM}"	=>"OD VERTICAL PRISM : ",		"{OS VERTICAL PRISM}"	=>	"OS VERTICAL PRISM : ",
									"{V-CC-OD}"				=>"V-CC-OD : ",					"{V-CC-OS}"				=>	"V-CC-OS : ",
									"{MR1}"					=>"MR1 ",						"{MR2}"					=>	"MR2 : "
								);
				$return_arr['Medical Rx'] = array(
									"{MEDICATION NAME}"		=>"Name of the Medication : ",	"{STRENGTH}"		=>	"Strength : ",
								   	"{QUANTITY}"			=>"Quantity : ",				"{DIRECTION}"		=>	"Complete direction : ",	
								   	"{SUBSITUTION}"			=>"SUBSITUTION : ",				"{REFILL}"			=>	"Number associated with Refill : "				
								);
				$return_arr['Contact Lens'] = array_merge($return_arr['Contact Lens'],$commonEncounter1,$commonEncounter5,$commonEncounter6,$commonEncounter7,$commonEncounter18,$commonEncounter24);
				$return_arr['Logo'] = $this->logo_data['logo'];
			break;
			
			case 'prescription_glasses':
				$return_arr['Smart Tag'] = $this->get_smart_tags();
				$return_arr['Patient'] = array_merge($commonPtInfoArr2,$commonPtInfoArr7,$commonPtInfoArr8,$commonPtInfoArr10,$commonEncounter18,$commonEncounter26);
				$return_arr['Glasses'] = array(
									"{OD PRISM}"			=>"OD PRISM : ",				"{OS PRISM}"			=>	"OS PRISM : ",
									"{OD HORIZONTAL PRISM}" =>"OD HORIZONTAL PRISM : ",		"{OS HORIZONTAL PRISM}" =>	"OS HORIZONTAL PRISM : ",
									"{OD VERTICAL PRISM}"	=>"OD VERTICAL PRISM : ",		"{OS VERTICAL PRISM}"	=>	"OS VERTICAL PRISM : ",
									"{V-CC-OD}"				=>"V-CC-OD : ",					"{V-CC-OS}"				=>	"V-CC-OS : ",
									"{MR1}"					=>"MR1 ",						"{MR2}"					=>	"MR2 : "
								);

				$return_arr['Glasses'] = array_merge($return_arr['Glasses'],$commonEncounter1,$commonEncounter5,$commonEncounter6,$commonEncounter7,$commonEncounter24);
				$return_arr['Logo'] = $this->logo_data['logo'];
			break;
			
			case 'prescription_medical_rx':
				$return_arr['Smart Tag'] = $this->get_smart_tags();
				$return_arr['Patient'] = array_merge($commonPtInfoArr2,$commonPtInfoArr7,$commonPtInfoArr8);
				$return_arr['Medical Rx'] = array(
									"{MEDICATION NAME}"		=>"Name of the Medication : ",	"{STRENGTH}"		=>	"Strength : ",
								   	"{QUANTITY}"			=>"Quantity : ",				"{DIRECTION}"		=>	"Complete direction : ",	
								   	"{SUBSITUTION}"			=>"SUBSITUTION : ",				"{REFILL}"			=>	"Number associated with Refill : "				
								);

				$return_arr['Medical Rx'] = array_merge($return_arr['Medical Rx'],$commonEncounter1,$commonEncounter5,$commonEncounter7);
				$return_arr['Logo'] = $this->logo_data['logo'];
			break;
			
			case 'op_notes':
				$return_arr['Smart Tag'] = $this->get_smart_tags();
				$return_arr['Patient']	= array_merge($commonPtInfoArr1,$commonPtInfoArr2,$commonPtInfoArr3,$commonPtInfoArr4,$commonPtInfoArr5,$commonPtInfoArr6,$commonPtInfoArr7,$commonPtInfoArr9,$commonPtInfoArr10,$commonPtInfoArr13,$commonPtInfoArr14,$commonPtInfoArr15,$commonPtInfoArr16,$commonPtInfoArr18,$commonEncounter26,$commonEncounter30);
				$opNotesArr = array("{DX_CODE}"	=> "DX CODE", "{LASER_PROC_NOTES}"	=> "LASER PROCEDURE NOTES");
				$opNotesArr	= array_merge($opNotesArr,$commonEncounter1,$commonEncounter2,$commonEncounter3,$commonEncounter4,$commonEncounter8,$commonEncounter9,$commonEncounter11,$commonEncounter13,$commonEncounter17,$commonEncounter20,$commonEncounter21,$commonEncounter23,$commonEncounter32);
				$return_arr['Operative Note'] = $opNotesArr;
				$return_arr['Logo'] = $this->logo_data['logo'];
			break;
			
			case 'recall':
				$return_arr['Smart Tag'] = $this->get_smart_tags();
				$ptInfoArr	= $commonPtInfoArr2;
				$ptInfoArr	= array_merge($commonPtInfoArr2,$commonPtInfoArr13,$commonPtInfoArr15,$commonEncounter30);
				$return_arr['Patient'] = $ptInfoArr;
				$recallLtrArr = array("{RECALL DESCRIPTION}"	=>"Recall Description : ",		"{RECALL PROCEDURE}" 		=>"Recall Procedure :",
									  "{LAST DOS}" 				=>"Last DOS :", 				"{APPT PROVIDER SIGNATURE}" =>"Appt Provider Signature:",
									  "​{PT-KEY}" 				=>"PT-KEY :",					"{NO SHOW APPOINTMENT}" 				=>"No Show Appointment:"
									 );
				$recallLtrArr	= array_merge($recallLtrArr,$commonEncounter14,$commonEncounter16);
				$return_arr['Recall'] = $recallLtrArr;
				$return_arr['Logo'] = $this->logo_data['logo'];
			break;
			
			case 'pt_docs':
				$return_arr['Smart Tag'] = $this->get_smart_tags();
				$return_arr['Patient']	= array_merge($commonPtInfoArr1,$commonPtInfoArr2,$commonPtInfoArr3,$commonPtInfoArr4,$commonPtInfoArr5,$commonPtInfoArr6,$commonPtInfoArr7,$commonPtInfoArr10,$commonPtInfoArr11,$commonPtInfoArr12,$commonPtInfoArr13,$commonPtInfoArr14,$commonPtInfoArr15,$commonPtInfoArr16,$commonPtInfoArr18,$commonEncounter26,$commonEncounter30);
				$cnsntFrmArr = array_merge($commonEncounter4,$commonEncounter10,$commonEncounter11,$commonEncounter12,$commonEncounter13,$commonEncounter14,$commonEncounter15,$commonEncounter16,$commonEncounter17,$commonEncounter20,$commonEncounter22,$commonEncounter23,$commonEncounter24,$commonEncounter29, array("{NO SHOW APPOINTMENT}"=>"No Show Appointment:"),$commonEncounter33);
				$return_arr['Pt. Docs'] = $cnsntFrmArr; 
				$return_arr['Logo'] = $this->logo_data['logo'];
			break;
			
			case 'statements':
				$return_arr['Statement'] = $stInfoArr;
				$return_arr['Logo'] = $this->logo_data['logo'];
			break;
			
			case 'panels':
				$return_arr['Patient']	= array_merge($commonPtInfoArr1,$commonPtInfoArr2,$commonPtInfoArr3,$commonPtInfoArr4,$commonPtInfoArr5,$commonPtInfoArr6,$commonPtInfoArr7,$commonPtInfoArr10,$commonPtInfoArr11,$commonPtInfoArr12,$commonPtInfoArr13,$commonPtInfoArr14,$commonPtInfoArr15,$commonPtInfoArr16,$commonPtInfoArr18,$commonEncounter26);
				$return_arr['Logo'] = $this->logo_data['logo'];
			break;
			
			case 'surger_consent':
				$return_arr['Smart Tag'] = $this->get_smart_tags();
				$return_arr[''] = $surgery_con_arr;
				$return_arr['Logo'] = $this->logo_data['logo'];
			break;
			
			case 'prescriptions':
				$return_arr['Smart Tag'] = $this->get_smart_tags();
				
				$return_arr['Patient']	= array_merge($commonPtInfoArr2,$commonPtInfoArr7,$commonPtInfoArr8,$commonPtInfoArr10,$commonEncounter18,$commonEncounter26);
			
			
				$presCntctLnsArr = array("{OD DIAMETER}"	=>"OD DIAMETER : ",		"{OS DIAMETER}"		=>"OS DIAMETER : ",	
								   "{MAKE}"				=>"MAKE : ",			"{MAKE OS}"			=>"MAKE OS : ",	
								   "{MAKE OD}"			=>"MAKE OD : ",			"{MAKE OU}"			=>"MAKE OU : ",
								   "{DVA OD}"			=>"DVA OD : ",			"{DVA OS}"			=>"DVA OS : ",
								   "{DVA OU}"			=>"DVA OU : ",
								   "{NVA OD}"			=>"NVA OD : ",			"{NVA OS}"			=>"NVA OS : ",
								   "{NVA OU}"			=>"NVA OU : ",
								   "{2 Degree/W OD}"	=>"2 Degree/W OD : ",	"{2 Degree/W OS}"	=>"2 Degree/W OS : ",
								   "{3 Degree/W OD}"	=>"3 Degree/W OD : ",	"{3 Degree/W OS}"	=>"3 Degree/W OS : ",
								   "{PC/W OD}"			=>"PC/W OD : ",			"{PC/W OS}"			=>"PC/W OS : ",
								   "{BLEND OD}"			=>"BLEND OD : ",		"{BLEND OS}"		=>"BLEND OS : ",
								   "{EDGE OD}"			=>"EDGE OD : ",			"{EDGE OS}"			=>"EDGE OS : ",
								   "{POWER OD}"			=>"POWER OD : ",		"{POWER OS}"		=>"POWER OS",
								   "{CL COMMENT}"		=>"CL COMMENT : ",		"{REPLENISHMENT}"	=>"REPLENISHMENT : ",
								   "{WEAR SCHEDULER}"	=>"WEAR SCHEDULER : ",	"{DISINFECTING}" 	=>"DISINFECTING : ",
								   "{OZ_OD}"			=>"OZ OD : ",			"{OZ_OS}"			=>"OZ OS : ",
								   "{CT_OD}"			=>"CT OD : ",			"{CT_OS}" 			=>"CT OS : ",			
                                   "{PT_EMAIL_ADDRESS}" =>"Patient Email : ",
				                   "{OVER_REFR_DVA_SPHERE_OD}" =>"DVA SPHERE : ",
				                   "{OVER_REFR_DVA_CYLINDER_OD}" =>"DVA CYLINDER : ",
            				       "{OVER_REFR_DVA_AXIS_OD}" => "DVA AXIS : ",
            				       "{OVER_REFR_DVA_OD}" => "DVA : ",
            				       "{OVER_REFR_DVA_OU_OD}" => "DVA OU : ",
            				       "{OVER_REFR_DVA_SPHERE_OS}" =>"DVA SPHERE : ",
            				       "{OVER_REFR_DVA_CYLINDER_OS}" =>"DVA CYLINDER : ",
            				       "{OVER_REFR_DVA_AXIS_OS}" => "DVA AXIS : ",
            				       "{OVER_REFR_DVA_OS}" => "DVA : ",
            				       "{OVER_REFR_NVA_SPHERE_OD}" => "NVA SPHERE : ",
				                   "{OVER_REFR_NVA_CYLINDER_OD}" => "NVA CYLINDER : ",
            				       "{OVER_REFR_NVA_AXIS_OD}" => "NVA AXIS : ",
				                   "{OVER_REFR_NVA_OD}" => "NVA : ",
				                   "{OVER_REFR_NVA_OU_OD}" => "NVA OU : ",
                                   "{OVER_REFR_NVA_SPHERE_OS}" => "NVA SPHERE : ",
                                   "{OVER_REFR_NVA_CYLINDER_OS}" => "NVA CYLINDER : ",
                                   "{OVER_REFR_NVA_AXIS_OS}" => "NVA AXIS : ",
                                   "{OVER_REFR_NVA_OS}" => "NVA : ",
                                   "{MAKE TYPE OD}" => "TYPE OD : ",
                                   "{MAKE TYPE OS}" => "TYPE OS : "
								   	
								  /* "{REF. DVA SPHERE OD}"	=>"REF. DVA SPHERE OD : ",	"{REF. DVA SPHERE OS}"	=>"REF. DVA SPHERE OS : ",
								   "{REF. DVA CYLINDER OD}"	=>"REF. DVA CYLINDER OD : ","{REF. DVA CYLINDER OS}"=>"REF. DVA CYLINDER OS : ",
								   "{REF. DVA AXIS OD}"		=>"REF. DVA AXIS OD : ",	"{REF. DVA AXIS OS}"	=>"REF. DVA AXIS OS : ",
								   "{REF. DVA OD}"		=>"REF. DVA OD : ",		"{REF. DVA OS}"		=>"REF. DVA OS : ",
								   "{REF. DVA OU}"		=>"REF. DVA OU : ",
								   "{REF. NVA SPHERE OD}"	=>"REF. NVA SPHERE OD : ",	"{REF. NVA SPHERE OS}"	=>"REF. NVA SPHERE OS : ",
								   "{REF. NVA CYLINDER OD}"	=>"REF. NVA CYLINDER OD : ","{REF. NVA CYLINDER OS}"=>"REF. NVA CYLINDER OS : ",
								   "{REF. NVA AXIS OD}"		=>"REF. NVA AXIS OD : ",	"{REF. NVA AXIS OS}"	=>"REF. NVA AXIS OS : ",
								   "{REF. NVA OD}"		=>"REF. NVA OD : ",		"{REF. NVA OS}"		=>"REF. NVA OS : ",
								   "{REF. NVA OU}"		=>"REF. NVA OU : ",
								   "{REF. COMFORT OD}"	=>"REF. COMFORT OD : ",	"{REF. COMFORT OS}"	=>"REF. COMFORT OS : ",
								   "{REF. MOVEMENT OD}"	=>"REF. MOVEMENT OD : ","{REF. MOVEMENT OS}"=>"REF. MOVEMENT OS : ",
								   "{REF. ROTATION OD}"	=>"REF. ROTATION OD : ","{REF. ROTATION OS}"=>"REF. ROTATION OS : ",
								   "{REF. CONDITION OD}"	=>"REF. CONDITION OD : ","{REF. CONDITION OS}"=>"REF. CONDITION OS : ",
								   "{REF. POSITION OD}"	=>"REF. POSITION OD : ","{REF. POSITION OS}"=>"REF. POSITION OS : "*/
								);
			
			
				$return_arr['Contact Lens']	= array_merge($presCntctLnsArr,$commonEncounter1,$commonEncounter5,$commonEncounter6,$commonEncounter7,$commonEncounter18,$commonEncounter24);
				
				$presGlassesArr	= array("{OD PRISM}"			=>"OD PRISM : ",				"{OS PRISM}"			=>	"OS PRISM : ",
									"{OD HORIZONTAL PRISM}" =>"OD HORIZONTAL PRISM : ",		"{OS HORIZONTAL PRISM}" =>	"OS HORIZONTAL PRISM : ",
									"{OD VERTICAL PRISM}"	=>"OD VERTICAL PRISM : ",		"{OS VERTICAL PRISM}"	=>	"OS VERTICAL PRISM : ",
									"{V-CC-OD}"				=>"V-CC-OD : ",					"{V-CC-OS}"				=>	"V-CC-OS : ",
									"{MR1}"					=>"MR1 ",						"{MR2}"					=>	"MR2 : ",
									"{GIVEN_DATE}"			=>"MR GIVEN DATE "
								);
			
				$return_arr['Glasses']	= array_merge($presGlassesArr,$commonEncounter1,$commonEncounter5,$commonEncounter6,$commonEncounter7,$commonEncounter24);
				
				$presMedRxArr	= array("{MEDICATION NAME}"			=>"Name of the Medication : ",		"{STRENGTH}"	=>"Strength : ",
								   "{QUANTITY}"					=>"Quantity : ",					"{DIRECTION}"	=>"Complete direction : ",	
								   "{SUBSITUTION}"				=>"SUBSITUTION : ",					"{REFILL}"		=>"Number associated with Refill : ",					
								   	
								);
			
				$return_arr['Medical Rx']	= array_merge($presMedRxArr,$commonEncounter1,$commonEncounter5,$commonEncounter7);
				
				$return_arr['Logo'] = $this->logo_data['logo'];
			break;	
		}
		
		return $return_arr;
	}
	
	//Returns Logo variables array
	public function get_logo_variables()
	{
		
		$return_arr = array();
		$logo_vacab = array();
		$logo_url_arr = array();
		
		$logo_vocab_res = imw_query("SELECT var_name,img_url FROM document_logos WHERE delete_status=0");
		
		if($logo_vocab_res)
		{
			while($logo_vocab_rs= imw_fetch_assoc($logo_vocab_res))
			{
				$logo_vacab['{'.$logo_vocab_rs['var_name'].'}'] = str_replace('_',' ',$logo_vocab_rs['var_name']);
				$logo_url_arr['{'.$logo_vocab_rs['var_name'].'}'] = str_ireplace($GLOBALS['fileroot'],$GLOBALS['webroot'],$logo_vocab_rs['img_url']);
			}
		}
		
		$return_arr['logo'] = $logo_vacab;
		$return_arr['logo_url'] = $logo_url_arr;
		
		return $return_arr;
	}
	
	//Returns Smart Tags array
	function get_smart_tags($id = 0,$return_type = 'all')
	{
		$return = false;
		
		$sQry = imw_query(" SELECT 
								id, 
								tagname, 
								under, 
								created_by, 
								status 
							FROM 
								`smart_tags` 
							WHERE 
								under='$id' 
							AND 
								status='1' 
							ORDER BY 
								tagname
						 ");
						 
		if(imw_num_rows($sQry)>0)
		{
			$return = array();
			
			while($sRes = imw_fetch_assoc($sQry))
			{
				if($return_type == 'all')
				{
					$return['['.$sRes['tagname'].']'] = $sRes['tagname'];
				}
				else
				{
					$return[$sRes['id']] = $sRes;
				}
			}
		}
		return $return;
	}
	
	//Returns array for common dropdowns
	public function get_common_dropdown_arr()
	{
		$return_arr = array();
		
		//Consent form arr
		$qry = 'SELECT 
					consent_form_id,
					consent_form_name 
				FROM 
					`consent_form` 
				WHERE 
					consent_form_status=\'Active\' 
				ORDER BY
					consent_form_name';
					
		$template_qry = imw_query($qry);
		$consent_form_arr = array();
		if(imw_num_rows($template_qry) > 0)
		{
			while($temp_row = imw_fetch_assoc($template_qry))
			{
				$consent_form_arr[$temp_row['consent_form_id']] = $temp_row;
			}
		}
		
		$return_arr['consent_form'] = $consent_form_arr;
		
		//ICD 10 arr
		$icd_10_qry = imw_query("SELECT 
									id,
									icd10,
									icd10_desc 
								FROM 
									`icd10_data`
								WHERE 
									deleted = 0 
								ORDER BY 
									icd10 asc, 
									icd10_desc asc
								");
		$icd_10_arr = array();
		if(imw_num_rows($icd_10_qry) > 0)
		{
			while($icd_row = imw_fetch_assoc($icd_10_qry))
			{
				$icd_10_arr[$icd_row['icd10']] = $icd_row['icd10_desc'];
			}
		}
		
		$return_arr['icd_10'] = $icd_10_arr;
		
		//LAB / RADIOLOGY arr
		$lab_qry=imw_query("SELECT 
								lab_radiology_name, 
								lab_radiology_tbl_id 
							FROM 
								lab_radiology_tbl 
							WHERE 
								lab_radiology_status = '0' 
							AND 
								lab_type='Lab'
							");
		$lab_arr = array();
		if(imw_num_rows($lab_qry) > 0)
		{
			while($lab_row = imw_fetch_assoc($lab_qry))
			{
				$lab_arr[$lab_row['lab_radiology_tbl_id']] = $lab_row['lab_radiology_name'];
			}
		}
		
		$return_arr['lab'] = $lab_arr;
		
		//Lab criteria arr
		$lab_criteria_arr = array('greater' => '>', 'greater_equal' => '>=', 'equalsto' => '=', 'less_equal' => '<=', 'less' => '<');
		ksort($lab_criteria_arr);
		$return_arr['lab_criteria'] = $lab_criteria_arr;
		
		//CPT code arr
		$cpt_code_qry=imw_query("SELECT 
									cpt_fee_id,
									cpt4_code,
									cpt_desc 
								FROM 
									`cpt_fee_tbl` 
								WHERE 
									delete_status = '0' 
								ORDER BY 
									cpt_desc asc, 
									cpt4_code asc
								");
		
		$cpt_code_arr = array();
		
		if(imw_num_rows($cpt_code_qry) > 0)
		{
			while($cpt_row = imw_fetch_assoc($cpt_code_qry))
			{
				$tmp_arr = array();
				$tmp_arr['cpt_code'] = $cpt_row['cpt4_code'];
				$tmp_arr['cpt_desc'] = $cpt_row['cpt_desc'];
				$cpt_code_arr[$cpt_row['cpt_fee_id']] = $tmp_arr;
			}
		}
		
		$return_arr['cpt'] = $cpt_code_arr;
		
		//Type visit arr
		$type_visit_qry=imw_query('	SELECT 
										ptVisit,
										tech_id  
									FROM 
										`tech_tbl` 
									WHERE 
										ptVisit<>"" 
									ORDER BY 
										ptVisit
								 ');
		
		$type_visit_arr = array();
		
		if(imw_num_rows($type_visit_qry) > 0)
		{
			while($type_visit_row = imw_fetch_assoc($type_visit_qry))
			{
				$type_visit_arr[$type_visit_row['tech_id']] = $type_visit_row['ptVisit'];
			}
		}
		
		$return_arr['type_visit'] = $type_visit_arr;
		
		//Medications arr
		$medication_qry = imw_query("SELECT 
										id,
										medicine_name 
									FROM 
										`medicine_data` 
									WHERE 
										del_status = '0' 
									ORDER BY 
										medicine_name asc
									");
		
		$medication_arr = array();
		
		if(imw_num_rows($medication_qry) > 0)
		{
			while($medication_row = imw_fetch_assoc($medication_qry))
			{
				$medication_arr[$medication_row['id']] = $medication_row['medicine_name'];
			}
		}
		
		$return_arr['medication'] = $medication_arr;
		
		//Test/Exam arr
		$test_exam_arr = array(1=>'HRT',2=>'OCT',3=>'AVF',4=>'IVFA',5=>'DFE',6=>'Photos',7=>'Gonio',8=>'Pachy',9=>'Ophthalmoscopy',10=>'Visual field',11=>'Topography',12=>'fundus',13=>'B-Scan',14=>'External',15=>'Anterior',16=>'Cell Count');
		$return_arr['test'] = $test_exam_arr;
		
		return $return_arr;
	}
	
	//Returns data object of the required section
	public function get_template_data($table, $conditionId=0, $value=0, $orderBy=0,$compareVal = '=',$sort_order = 'DESC'){
		if(strlen($value) > 0){
			if(strtolower(trim($compareVal)) == 'in'){
				$value = "(".$value.")";
			}else{
				$value = "'".$value."'";
			}
		}
		
		if($orderBy){
			if($conditionId){
				$qryStr = "	SELECT 
								* 
							FROM 
								$table 
							WHERE 
								$conditionId 
								$compareVal 
								$value 
							ORDER BY 
								$orderBy 
								$sort_order
						 ";
			}else{
				$qryStr = "	SELECT 
								* 
							FROM 
								$table 
							ORDER BY 
								$orderBy 
								$sort_order";
			}
		}else{
			if($conditionId){
				$qryStr = "	SELECT 
								* 
							FROM 
								$table 
							WHERE 
								$conditionId 
								$compareVal 
								$value";
			}else{
				$qryStr = "SELECT * FROM $table";
			}
		}
		
		$qryQry = imw_query($qryStr);
		
		if($qryQry)
		{
			while($qryRow = imw_fetch_assoc($qryQry))
			{
				$qryRows[] = $qryRow;
			}		
			return $qryRows;
		}
	}
	
	//Check for existing data
	public function check_existing($select_field = '*',$chk_field = 0,$chk_value = 0)
	{
		$return = false;
		$where_str = '';
		
		if(is_array($chk_field) == true)
		{
			$counter = 0;
			foreach($chk_field as $key => $val)
			{
				if($counter > 0)
				{
					$where_str .= " AND ";
				}
				$where_str .= "$key = '$val'";
				$counter++;
			}
		}
		else
		{
			$where_str = '$chk_field = $chk_value';
		}
		$qry = imw_query("	SELECT 
								$select_field 
							FROM 
								".$this->cur_tab_table." 
							WHERE 
								$where_str
						");

		if(imw_num_rows($qry) > 0)
		{
			$return = true;
		}
		return $return;
	}
	
	//Returns consent data array
	public function get_consent_data($return_type = 'all')
	{
		$return_arr = array();
		
		//Template Data
		$prnt_qry = imw_query("SELECT * from consent_category order by category_name ASC");
		if(imw_num_rows($prnt_qry) > 0)
		{
			while($prnt_row = imw_fetch_assoc($prnt_qry))
			{
				$cat_id = $prnt_row['cat_id'];
				$child_qry = imw_query('SELECT * from consent_form where cat_id = '.$cat_id.' order by consent_form_name ASC');
				$tmp_arr = array();
				if(imw_num_rows($child_qry) > 0)
				{
					while($child_row = imw_fetch_assoc($child_qry))
					{
						$dt_tmp_arr['consent_form_name'] = $child_row['consent_form_name'];
						$dt_tmp_arr['cat_id'] = $child_row['cat_id'];
						$dt_tmp_arr['consent_form_id'] = $child_row['consent_form_id'];
						$tmp_arr[] = $dt_tmp_arr;
					}
				}
				
				switch($return_type)
				{
					case 'all':
						$return_arr[$prnt_row['category_name']] = $tmp_arr;
					break;
					
					case 'dropdown':
						$rt_tmp_arr['category_name'] = $prnt_row['category_name'];
						$rt_tmp_arr['section'] = $prnt_row['section'];
						$rt_tmp_arr['iportal'] = $prnt_row['iportal'];
						$return_arr[$cat_id] = $rt_tmp_arr;
					break;
				}
			}
		}
		
		//Getting Old & New form data
		if($return_type == 'old_data')
		{
			$new_consent_form_id = array();
			$old_ver_str = '<option value="">Select</option>';
			$new_ver_qry = imw_query("	SELECT 
											consent_form_id,
											physician_id,
											consent_form_name 
										FROM 
											consent_form
										ORDER BY 
											consent_form_name , 
											consent_form_version desc
									");
			if(imw_num_rows($new_ver_qry) > 0)
			{
				while($new_ver_row = imw_fetch_array($new_ver_qry))
				{
					if($consent_form_name2 != $new_ver_row['consent_form_name'])
					{
						$consent_form_name2 = $new_ver_row['consent_form_name'];
						$formDetails[] = $new_ver_row;
						$new_consent_form_id[] = $new_ver_row['consent_form_id'];
					}
				}
			}
			
			if(count($new_consent_form_id) > 0)
			{
				$old_data = '';
				$new_ver_ids = implode(',',$new_consent_form_id);
				$old_ver_qry=imw_query("SELECT 
											consent_form_id,
											consent_form_name,
											date_format(consent_change_date,'%m-%d-%y') as consent_change_date 
										FROM 
											consent_form 
										WHERE 
											consent_form_id not in ($new_ver_ids) 
										ORDER BY 
											consent_form_name
									 ");
				if(imw_num_rows($old_ver_qry) > 0)
				{
					while($old_ver_row = imw_fetch_assoc($old_ver_qry))
					{
						$form_id = $old_ver_row['consent_form_id'];
						$form_name = ucwords($old_ver_row['consent_form_name']);
						$change_date = $old_ver_row['consent_change_date'];
						$sel = $old_ver_row['consent_form_id'] == $_REQUEST['old_version_id'] ? 'selected' :'';
						$old_ver_str .= "<option value='$form_id' $sel >$form_name / $change_date</option>";
					}
					$old_data = '<select name="old_version_id" class="selectpicker" data-width="100%" data-title="Select" onchange="document.template_form.submit();">'.$old_ver_str.'</select>';
				}
				else
				{
					$old_data = '<br />N/A';
				}
			}
			unset($return_arr);
			$return_arr['old_data'] = $old_data;
		}
		return $return_arr;
	}
	
	//Returns package data array
	public function get_package_data($order_asc_desc)
	{
		$return_arr = array();
		$consent_form_arr = $this->common_array['consent_form'];
		$parent_data = $this->get_template_data($this->cur_tab_table,'delete_status','yes','package_category_name','!=',$order_asc_desc);
		foreach($parent_data as &$obj)
		{
			$tmp_arr = array();
			$consent_ids = explode(',',$obj['package_consent_form']);
			foreach($consent_ids as $key => $val)
			{
				if($consent_form_arr[$val])
				{
					$tmp_arr[] = $consent_form_arr[$val];
				}
			}
			$obj['consent_data'] = $tmp_arr;
		}
		
		$return_arr['data'] = $parent_data;
		return $return_arr;
	}
	
	//Manages Education update and add values
	public function manage_education($request,$files)
	{
		$return_str = '';
		$valid = false;
		$value_arr = array();
		
		$doc_from = $request['doc_from'];
		//Managing documents
		$doc_status = $this->manage_upload_docs($doc_from,$request,$files);
		if(count($doc_status) > 0 && is_array($doc_status))
		{
			foreach($doc_status as $key => $val)
			{
				$value_arr[$key] = $val;
			}
		}
		
		//Values
		$eid = $request['eid'];
		$name = addslashes($request['name']);
		$ccda_code = addslashes($request['ccda_code']);
		$old_name = addslashes($request['old_name']);
		$visit = @implode(',',$request['visit']);
		$tests = @implode(',',$request['tests']);
		$txt_lab_name = addslashes($request['txt_lab_name']);
		$lab_criteria = addslashes($request['lab_criteria']);
		$lab_result = addslashes($request['lab_result']);
		
		$dx = '';
		$where_arr = array();
		$where_arr['name'] = $name;
		$where_arr['status'] = '0';
		if($this->current_tab == 'education')
		{
			$dx = str_replace("\n",",",$request['dx']);
			$where_arr['pt_edu'] = '1';
		}
		
		if($this->current_tab == 'instructions')
		{
			$dx = $request['dx'];
			if(is_array($request['dx']))
			{
				$dx = @implode(',',$request['dx']); 
			}
			$where_arr['pt_test'] = '1';
		}
		$cpt = @implode(',',$request['cpt']);
		$medications = @implode(',',$request['medications']);
		$scan_id = ($_SESSION['document_scan_id']<>'') ? $_SESSION['document_scan_id'] : $request['scan_id'];
		$pt_edu = ($this->current_tab == 'education') ? $request['pt_edu'] : '';
		$pt_test = ($this->current_tab == 'instructions') ? $request['pt_test'] : '';
		$andOrCondition = $request['andOrCondition'];
		
		//Add values
		if($request['eid'] == "")
		{
			$value_arr['name'] = $name;
			$value_arr['ccda_code'] = $ccda_code;
			$value_arr['visit'] = $visit;
			$value_arr['tests'] = $tests;
			$value_arr['txt_lab_name'] = $txt_lab_name;
			$value_arr['lab_criteria'] = $lab_criteria;
			$value_arr['lab_result'] = $lab_result;
			$value_arr['dx'] = $dx;
			$value_arr['cpt'] = $cpt;
			$value_arr['medications'] = $medications;
			$value_arr['doc_from'] = $doc_from;
			$value_arr['scan_id'] = $scan_id;
			$value_arr['pt_edu'] = $pt_edu;
			$value_arr['pt_test'] = $pt_test;
			$value_arr['andOrCondition'] = $andOrCondition;
			$_SESSION['document_scan_id']='';
			
			//Checking name ==> New Or Exist
			$check_unique = $this->check_existing('name',$where_arr);
			if($check_unique)
			{
				$return_str = $this->unique_doc_msg;
				$valid = false;
			}
			else
			{
				$valid = true;
			}
			
			if($valid)
			{
				$insert_id = $this->addRecords($value_arr,$this->cur_tab_table);
				if($insert_id)
				{
					$return_str = 'Document added successfully';
				}
			}
			
			//VALID FORMAT OF UPLOAD DATA VALIDATION 
			if($value_arr['error'])
			{
				$return_str = $value_arr['error'];
			}
			
		}
		else
		{
			$value_arr['name'] = $name;
			$value_arr['ccda_code'] = $ccda_code;
			$value_arr['visit'] = $visit;
			$value_arr['tests'] = $tests;
			$value_arr['txt_lab_name'] = $txt_lab_name;
			$value_arr['lab_criteria'] = $lab_criteria;
			$value_arr['lab_result'] = $lab_result;
			$value_arr['dx'] = $dx;
			$value_arr['cpt'] = $cpt;
			$value_arr['medications'] = $medications;
			$value_arr['doc_from'] = $doc_from;
			$value_arr['scan_id'] = $scan_id;
			$value_arr['pt_edu'] = $pt_edu;
			$value_arr['pt_test'] = $pt_test;
			$value_arr['andOrCondition'] = $andOrCondition;
			
			//Checking name ==> New Or Exist
			$check_unique = $this->check_existing('name',$where_arr);
			if($check_unique && $old_name != $name)
			{
				$return_str = "Document name already exist.";
				$valid = false;
			}
			else
			{
				$valid = true;
			}
			if($valid)
			{
				$status = $this->updateRecords($value_arr, $this->cur_tab_table, 'id', $eid);
				if($status > 0)
				{
					$rel_arr['name'] = $name;
					$rel_status = $this->updateRecords($rel_arr, 'document_patient_rel', 'doc_id', $eid);
					$return_str['status'] = "Document updated successfully.";
					$return_str['edit_id'] = $eid;
				}
			}
		}
		
		//VALID FORMAT OF UPLOAD DATA VALIDATION 
		if($value_arr['error'])
		{
			$return_str = $value_arr['error'];
		}
	
		$alert_msg = $ret_edit_id = '';
		if(is_array($return_str) && count($return_str) > 0)
		{
			if($return_str['edit_id'])
			{
				$ret_edit_id = $return_str['edit_id'];
			}
			
			if($return_str['status'])
			{
				$alert_msg = $return_str['status'];
			}
		}
		else
		{
			$alert_msg = $return_str;
		}
		
		$return_arr = array();
		$return_arr['msg'] = $alert_msg;
		$return_arr['url'] = $this->get_base_path().'/index.php?showpage='.$this->current_tab.'&edit_id='.$ret_edit_id;
		if($this->current_tab == 'instructions')
		{
			$return_arr['url'] = $this->get_base_path().'/index.php?showpage=education&sub=instructions&edit_id='.$ret_edit_id;
		}
		
		
		return $return_arr;
	}
	
	//Returns surgery consnet data
	public function get_surgery_consent_data()
	{
		$srgery_child_arr = $srgery_cat_arr = $return_arr = array();
		$srgy_consent = $this->get_template_data($this->cur_tab_prnt_table,'category_status','true','category_name','!=');
		if(count($srgy_consent) > 0){
			foreach($srgy_consent as $obj){
				$cat_id = $obj['category_id'];
				$child_row = $this->get_template_data($this->cur_tab_table,'consent_category_id = "'.$cat_id.'" and consent_delete_status','true','consent_name','!=');
				$srgery_child_arr[$obj['category_name']] = $child_row;
				$srgery_cat_arr[$obj['category_id']] = $obj['category_name'];
			}
		}
		$return_arr['data'] = $srgery_child_arr;
		$return_arr['cat_arr'] = $srgery_cat_arr;
		return $return_arr;
	}
	
	
	//Manage Upload/Scan documents and return arr containing db fields to be updated
	public function manage_upload_docs($call_from,$request,$files){
		$return_arr = array();
		
		switch($call_from){
			// Doc ==> Editor
			case 'writeDoc': 
				$content = addslashes($request['content']);
				$return_arr['content'] = $content;
				$return_arr['scan_doc_file_path'] = '';
				$return_arr['upload_doc_file_path'] = '';
			break;
			
			// Doc ==> Scan
			case 'scanDoc':
				$return_arr['upload_doc_file_path'] = '';
				if($_SESSION['scan_image_document']<>'' && $request['eid'] == "") { // in case of new document to add 
					$return_arr['scan_doc_file_path'] = $_SESSION['scan_image_document'];
					$return_arr['scan_doc_date'] = 'now()';
					$return_arr['upload_doc_file_path'] = '';
					
					$_SESSION['scan_image_document'] = NULL;
					$_SESSION['scan_image_document'] = '';
				}
			break;
			
			// Doc ==> Upload
			case 'uploadDoc':
				$adminMainDocDir = "/admin_documents";					
				if(!is_dir($this->upload_path.$adminMainDocDir)){mkdir($this->upload_path.$adminMainDocDir, 0700);}

				$adminDocDir = "/admin_documents/upload";					
				if(!is_dir($this->upload_path.$adminDocDir)){mkdir($this->upload_path.$adminDocDir, 0700);}
				
				$return_str = " scan_doc_file_path = '', ";
				$save_file_path = $file_extn = '';
				if($files['upld_doc']['tmp_name']) {	
					$image_name = $files['upld_doc']['name'];
					$image_name = urldecode($image_name);
					$image_name = str_ireplace(" ","-",$image_name);
					$image_name = str_ireplace(",","-",$image_name);
					
					$file_extn_arr = pathinfo($image_name);
					$file_extn = $file_extn_arr['extension'];
					$file_to_upload = $this->upload_path.$adminDocDir."/".$image_name;
					$save_file_path = $adminDocDir."/".$image_name;

					$allowed_ext = array("jpeg","jpg", "gif", "png","pdf");
					if( !in_array($file_extn,$allowed_ext) ) {
						$return_arr['error'] = $files['upld_doc']['name'] . " has invalid extension.<br>";
					}
					   
					  // validate file content type
					if( !wv_check_mime('img+pdf',$files['upld_doc']['tmp_name']) ) {
						$return_arr['error'] = $files['upld_doc']['name'] . " is an invalid image.<br>";
					}
					if( move_uploaded_file($files['upld_doc']['tmp_name'],$file_to_upload) ) {
						$return_arr['scan_doc_file_path'] = '';
						$return_arr['upload_doc_file_path'] = $save_file_path;
						$return_arr['upload_doc_date'] = 'now()';
						$return_arr['upload_doc_type'] = $file_extn;
					}
					else {
						$return_arr['error'] = "Error: File not moved. Please try again.";
					}
				}
			break;
		}
		return $return_arr;
	}	
	
	//Returns path acc to provided Scan/Uplaod values
	public function get_scan_upload_path($scan_doc_file_path = false,$upload_doc_file_path = false,$upload_doc_type = false){
		$return_arr = array();
		$upload_dir = $this->upload_path;
		$upload_dir_web = str_replace($GLOBALS['fileroot'],$GLOBALS['webroot'],$upload_dir);
		
		if($scan_doc_file_path) {									
			$thmb_path = $this->createThumbs($upload_dir.$scan_doc_file_path,"",150,150);
			if(is_array($thmb_path) == true){
				$tempImgWH = "width:".$thmb_path["imgWidth"]."px; height:".$thmb_path["imgHeight"]."px;";
				$thmb_path = $upload_dir_web.$scan_doc_file_path;
			}
			else{
				if(empty($thmb_path) == false){
					$thmb_path = $upload_dir_web.$thmb_path;
				}										 
			}
			
		}
		if($upload_doc_file_path && $upload_doc_type!='pdf') {
			$thmb_path_upload = $this->createThumbs($upload_dir.$upload_doc_file_path,"",150,150);									
			
			if(is_array($thmb_path_upload) == true){
				$tempImgWH = "width:".$thmb_path_upload["imgWidth"]."px; height:".$thmb_path_upload["imgHeight"]."px;";
				$thmb_path_upload = $upload_dir_web.$upload_doc_file_path;
			}
			else{
				if(empty($thmb_path_upload) == false){
					$thmb_path_upload = $upload_dir_web.$thmb_path_upload;
				}
			}
		}
		
		return array($thmb_path, $thmb_path_upload, $tempImgWH, $upload_dir_web);
	}
	
	//Returns data array for Pt docs
	public function get_pt_docs_data($return_type = 'all'){
		$return_arr = $data_arr = array();
		$category_arr = array();
		$prnt_child_qry = "
			SELECT 
			 `catg`.`cat_id`, 
			 `catg`.`category_name`, 
			 `temp`.`pt_docs_template_name`, 
			 `temp`.`pt_docs_template_id`, 
			 `temp`.`pt_docs_template_content`
			FROM 
			 `main_template_category` `catg`
			 LEFT JOIN `pt_docs_template` `temp`  ON (`catg`.cat_id = `temp`.`pt_docs_template_category_id`)
			WHERE
			 `catg`.`delete_status` = '0' 
			 AND `catg`.`template_name` = 'pt_docs'
			 AND (`temp`.`pt_docs_template_status` = '0' or `temp`.`pt_docs_template_status` is null) ORDER BY `catg`.`category_name`, temp.pt_docs_template_name  ASC";
		$res = imw_query($prnt_child_qry);
		if(imw_num_rows($res) > 0){
			while($res_row = imw_fetch_assoc($res)){
				$tmp_arr = array();
				$cat_id = $res_row['cat_id'];
				//If template id exists
				if(empty($res_row['pt_docs_template_id']) == false){
					$tmp_arr['cat_id'] = $cat_id;
					$tmp_arr['doc_name'] = $res_row['pt_docs_template_name'];
					$tmp_arr['doc_id'] = $res_row['pt_docs_template_id'];
					$tmp_arr['content'] = $res_row['pt_docs_template_content'];
				}
				
				//Template data array
				if(count($tmp_arr) > 0){
					$data_arr[$res_row['category_name']][] = $tmp_arr;
				}else{
					$data_arr[$res_row['category_name']] = $tmp_arr;
				}
				
				
				//Category array
				if(!$category_arr[$cat_id]){
					if(!in_array($res_row['category_name'],$category_arr)){
						$category_arr[$cat_id] = $res_row['category_name'];
					}
				}
			}
		}
		
		switch($return_type){
			case 'category':
				$return_arr['category'] = $category_arr;	
			break;
			
			default:
				$return_arr['data'] = $data_arr;
				$return_arr['category'] = $category_arr;
		}
		
		return $return_arr;
	}
	
	
	
	//Returns data array for Admin > Order Set > Order Template
	public function get_order_templates($return_type = 'all'){
		$return_arr = array();
		$order_type = array("1"=>"Meds", "2"=>"Labs", "3"=>"Imaging/Rad", "4"=>"Procedure/Sx",  "5"=>"Information/Instructions");
		
		//Template Data
		$tmp_arr = array();
		foreach($order_type as $key => $val){
			$data = $this->get_template_data($this->cur_tab_table,'status = 0 AND order_type_id',$key);
			$tmp_arr[$val] = $data;
		}
		$return_arr['data'] = $tmp_arr;
		$return_arr['type_arr'] = $order_type;
		
		return $return_arr;
	}	
	
	
	//Returns HTML for manage category
	public function get_section_categories($section){
		$cat_arr = $return_arr = array();
		$dp_str = '<table class="table table-bordered table-condensed">
			<tr>
				<th colspan="2" class="head">Manage Categories</th>	
			</tr>
		';
		switch($section){
			case 'consent':
				$cat_arr = $this->get_consent_data('dropdown');
				foreach($cat_arr as $key => $val){
					$check_in = (empty($val['section']) == true) ? 0 : 1;
					$dp_str .= '
						<tr>
							<td style="width:5%" class="pointer">
								<div class="checkbox">
									<input type="checkbox" id="cat_option_'.$key.'" name="cat_option[]" value="'.$key.'" onclick="check_delete_stat();">
									<label for="cat_option_'.$key.'"></label>	
								</div>
							</td>
							<td style="width:95%" class="pointer" onclick="set_category(this);">
								<span data-check="'.$check_in.'" data-iportal="'.$val['iportal'].'" data-id="'.$key.'" data-name="'.$val['category_name'].'">'.$val['category_name'].'</span>
							</td>
						</tr>';
				}
			break;
			
			case 'pt_docs':
				$cat_arr = $this->get_pt_docs_data('category');
				foreach($cat_arr['category'] as $key => $val){
					$dp_str .= '
					<tr>
						<td style="width:5%" class="pointer">
							<div class="checkbox">
								<input type="checkbox" id="cat_option_'.$key.'" name="cat_option[]" value="'.$key.'" onclick="check_delete_stat();">
								<label for="cat_option_'.$key.'"></label>	
							</div>
						</td>
						<td style="width:95%" class="pointer" onclick="set_category(this);">
							<span data-id="'.$key.'" data-name="'.$val.'">'.$val.'</span>
						</td>	
					</tr>';
				}
			break;
			
			case 'surgery_consent':
				$cat_arr = $this->get_surgery_consent_data();
				foreach($cat_arr['cat_arr'] as $key => $val){
					$dp_str .= '
					<tr>
						<td style="width:5%" class="pointer">
							<div class="checkbox">
								<input type="checkbox" id="cat_option_'.$key.'" name="cat_option[]" value="'.$key.'" onclick="check_delete_stat();">
								<label for="cat_option_'.$key.'"></label>	
							</div>
						</td>
						<td style="width:95%" class="pointer" onclick="set_category(this);">
							<span data-id="'.$key.'" data-name="'.$val.'">'.$val.'</span>
						</td>	
					</tr>';
				}
			break;
		}
		$dp_str .= '</table>';
		echo $dp_str;
	}
	
	
	//Data manipulation for categories
	public function manipulate_categories($request){
		$data_arr = array();
		$notification = 'No changes detected';
		$category_name = addslashes(trim($request['cat_name']));
		$section = isset($request['check_in']) ? 'checkin' : '';
		
		//Setting variable values acc. to current tab
		$update_field = '';
		switch($this->current_tab){
			case 'consent':
				$data_arr['section'] = $section;
				$data_arr['iportal'] = $request['iportal'];
				$data_arr['category_name'] = $category_name;
				$update_field = 'cat_id';
			break;
			
			case 'pt_docs':
				$data_arr['category_name'] = $category_name;
				$data_arr['template_name'] = 'pt_docs';
				$update_field = 'cat_id';
			break;
			
			case 'surgery_consent':
				$data_arr['category_name'] = $category_name;
				if(empty($request['category_edit_id']) == true){
					$data_arr['category_created_from'] = 'user';
				}
				$update_field = 'category_id';
			break;
		}
		
		//If not delete request
		if(!isset($request['modal_del'])){
			if(empty($request['category_edit_id']) == false){
				if($category_name != ''){
					$status = $this->updateRecords($data_arr, $this->cur_tab_prnt_table, $update_field, $request['category_edit_id']);
					if($status > 0){
						$notification = "Category Updated Successfully.";
					}
				}
			}else{
				if($category_name != ''){
					$status = $this->addRecords($data_arr, $this->cur_tab_prnt_table);
					if($status > 0){
						$notification = "Category Added Successfully.";
					}
				}
			}
		}else{
			$ids = explode(',',$request['ids']);
			$counter = 0;
			foreach($ids as $key => $val){
				$status = $this->delRecord($this->cur_tab_prnt_table,$update_field,$val);
				$counter = $counter + $status;
			}
			$notification = $counter;
		}
		return $notification;
	}
	
	//Managing smart tags
	public function manage_smart_tags($call_from,$request){
		$return_arr = array();
		$created_by = $_SESSION['authId'];
		$created_on = date('Y-m-d H:i:s');
		$msg = 'Some error occurred !';
		$counter = 0;
		switch($call_from){
			case 'sub_tag':
				$main_cat_id = $request['main_cat'];
				$input_data = json_decode($request['input_data']);
				
				foreach($input_data as $key => $input_val){
					$rec_arr = array();
					$cat_id = $input_val->cat;
					$tag_id = $input_val->id;
					$tag_name = $input_val->tag;
					
					$rec_arr['tagname'] = $tag_name;
					$rec_arr['modified_by'] = $created_by;
					$rec_arr['modified_on'] = $created_on;
					
					if(empty($tag_name) == false){
						if($cat_id == 0 && $tag_id == 0){	//Add new record
							$rec_arr['status'] = 1;
							$rec_arr['under'] = $main_cat_id;
							$status = $this->addRecords($rec_arr, $this->cur_tab_table);
						}else{								//Update existing record
							$status = $this->updateRecords($rec_arr, $this->cur_tab_table, "under = '$cat_id' AND id", $tag_id);
						}
						$counter = ($status + $counter);
					}
				}
				$return_arr['counter'] = $counter;
				$return_arr['main_cat_id'] = $main_cat_id;
			break;
			
			case 'main_tag':
				$tag_name = $request['tag_name'];
				$cat_id = (isset($request['cat_id']) && empty($request['cat_id']) == false) ? $request['cat_id'] : '';
				
				$rec_arr = array();
				$rec_arr['tagname'] = $tag_name;
				$rec_arr['under'] = 0;
				$rec_arr['modified_by'] = $created_by;
				$rec_arr['modified_on'] = $created_on;
				
				if($cat_id != ''){
					$status = $this->updateRecords($rec_arr, $this->cur_tab_table,'id', $cat_id);
				}else{
					$status = $this->addRecords($rec_arr, $this->cur_tab_table);
				}
				$counter = ($status + $counter);
				$return_arr['counter'] = $counter;
			break;
			
			case 'get_editor_tags':
				$smart_id = (isset($request['smart_id']) && empty($request['smart_id']) == false) ? $request['smart_id'] : '';
				$smart_tag_name = (isset($request['tag_name']) && empty($request['tag_name']) == false) ? ucwords($request['tag_name']) : '';
				
				$returnArr = false;
				if(empty($smart_id) == false && empty($smart_tag_name) == false){
					$returnArr['tagName'] = $smart_tag_name;
					$tagData = $this->get_smart_tags($smart_id, 'rec');
					if(is_array($tagData) && count($tagData) > 0){
						foreach ($tagData as $key => $value) {
							if(empty($value['tagname']) == false) $returnArr['tagValues'][] = $value['tagname'];
						}
					}
				}
				
				return $returnArr;
			break;
				
			case 'get_tags_app':
				$under = (isset($request['id']) &&  intval($request['id'])!= 0) ? intval($request['id']) : $request['id'];
				if((int)$under > 0){
					$tagname_rs = imw_fetch_assoc(imw_query("SELECT tagname FROM smart_tags WHERE id=".$under));
					$tagname = $tagname_rs['tagname'];
					$subTag_query = "SELECT id, tagname, under FROM smart_tags WHERE under = ".$under." AND status=1 ORDER BY tagname";
					$subTag_result = imw_query($subTag_query);
					$records = '
					<div class="section_header"><span class="closeBtn" onClick="$(\'#div_smart_tags_options\').hide();"></span>Option under '.core_extract_user_input($tagname).'</div>
					<div style="height:200px; overflow:auto; overflow-x:hidden;">
						<table class="cellBorder3 table_collapse" id="tableSubTags">';

					$subTag_found = imw_num_rows($subTag_result);
					$alt_class = ' class="alt"';

					if($subTag_result){			
						if($subTag_found>0){
							$nofound = 0;
							while($subTag_rs = imw_fetch_assoc($subTag_result)){
								$subTagname = trim(core_extract_user_input($subTag_rs['tagname']));
								$subTagid = stripslashes($subTag_rs['id']);
								$records .= '
								<tr'.$class.'>
									<td style="width:25px" class="alignCenter"><input value="'.$subTagname.'" type="checkbox" name="chkSmartTagOptions" id="txtTagOpt_'.$under.'_'.$subTagid.'"></td>
									<td style="width:auto;">'.$subTagname.'</td>
								</tr>
								';
								if($alt_class==' class="alt"'){$alt_class='';}else{$alt_class=' class="alt"';}	
								$counter++;
							}//end of while.			
						}
						else{
								$records .= '
								<tr'.$class.'>
									<td colspan="2" class="alignCenter warning text12b">No options defined under this SmartTag.</td>
								</tr>
								';
								$nofound = 1;
						}
					}			
					$records .= '
						</table>
					</div>';
				}
				elseif($under == "aPatRefPhy"){
					$records = '<div class="section_header"><span class="closeBtn" onClick="$(\'#div_smart_tags_options\').hide();"></span>Please Select Patient Referring Physician</div>
									<div style="height:200px; overflow:auto; overflow-x:hidden;">
										<table class="cellBorder3 table_collapse" id="tableSubTags">';
										
					$qrySelpatRefPhy = "select refPhy.physician_Reffer_id, TRIM(CONCAT(refPhy.Title, ' ', refPhy.LastName, ', ', refPhy.FirstName, ' ', refPhy.MiddleName)) as refName
										from patient_multi_ref_phy pmrf INNER JOIN 
										refferphysician refPhy ON pmrf.ref_phy_id = refPhy.physician_Reffer_id 
										where pmrf.patient_id = '".$request['patId']."' and pmrf.phy_type = '1' and pmrf.status = '0'";
					$rsSelpatRefPhy = imw_query($qrySelpatRefPhy);
					if(imw_num_rows($rsSelpatRefPhy) > 0){
						$nofound = 0;
						while($rowSelpatRefPhy = imw_fetch_array($rsSelpatRefPhy)){
							$subTagname = trim(core_extract_user_input($rowSelpatRefPhy['refName']));
							$subTagid = stripslashes($rowSelpatRefPhy['physician_Reffer_id']);
							$records .= '
							<tr'.$class.'>
								<td style="width:25px" class="alignCenter"><input value="'.$subTagname.'" type="checkbox" name="chkSmartTagOptions" id="txtTagOpt_'.$under.'_'.$subTagid.'"></td>
								<td style="width:auto;">'.$subTagname.'</td>
							</tr>
							';
							if($alt_class==' class="alt"'){$alt_class='';}else{$alt_class=' class="alt"';}	
						}//end of while.
					}
					else{
						$records .= '
						<tr'.$class.'>
							<td colspan="2" class="alignCenter warning text12b">Multiple Patient Referring Physician are not found!</td>
						</tr>
						';
						$nofound = 1;
					}
					$records .= '
						</table>
					</div>';
				}
				elseif($under == "aPatPCP"){
					$arrTemp = array();
					$records = '<div class="section_header"><span class="closeBtn" onClick="$(\'#div_smart_tags_options\').hide();"></span>Please Select Patient Primary Care Physician</div>
									<div style="height:200px; overflow:auto; overflow-x:hidden;">
										<table class="cellBorder3 table_collapse" id="tableSubTags">';
										
					$qrySelpatPCPMedHx = "select refPhy.physician_Reffer_id, TRIM(CONCAT(refPhy.Title, ' ', refPhy.LastName, ', ', refPhy.FirstName, ' ', refPhy.MiddleName)) as refName
										from patient_multi_ref_phy pmrf INNER JOIN 
										refferphysician refPhy ON pmrf.ref_phy_id = refPhy.physician_Reffer_id 
										where pmrf.patient_id = '".$_SESSION['patient']."' and pmrf.phy_type IN (3,4) and pmrf.status = '0'";
					$rsSelpatPCPMedHx = imw_query($qrySelpatPCPMedHx);
					if(imw_num_rows($rsSelpatPCPMedHx) > 0){
						$nofound = 0;
						while($rowSelpatPCPMedHx = imw_fetch_array($rsSelpatPCPMedHx)){
							if(in_array((int)$rowSelpatPCPMedHx['physician_Reffer_id'], $arrTemp) == false){
								$subTagname = trim(core_extract_user_input($rowSelpatPCPMedHx['refName']));
								$subTagid = stripslashes($rowSelpatPCPMedHx['physician_Reffer_id']);
								$records .= '
								<tr'.$class.'>
									<td style="width:25px" class="alignCenter"><input value="'.$subTagname.'" type="checkbox" name="chkSmartTagOptions" id="txtTagOpt_'.$under.'_'.$subTagid.'"></td>
									<td style="width:auto;">'.$subTagname.'</td>
								</tr>
								';
								if($alt_class==' class="alt"'){$alt_class='';}else{$alt_class=' class="alt"';}	
								$arrTemp[] = $rowSelpatPCPMedHx["physician_Reffer_id"];
							}
						}//end of while.
					}
					else{
						$records .= '
						<tr'.$class.'>
							<td colspan="2" class="alignCenter warning text12b">Multiple Patient Primary Care Physician are not found!</td>
						</tr>
						';
						$nofound = 1;
					}
					$records .= '
						</table>
					</div>';
				}
				elseif($under == "aPatCoManPhy"){
					$records = '<div class="section_header"><span class="closeBtn" onClick="$(\'#div_smart_tags_options\').hide();"></span>Please Select Patient Co-Managed Physician</div>
									<div style="height:200px; overflow:auto; overflow-x:hidden;">
										<table class="cellBorder3 table_collapse" id="tableSubTags">';
										
					$qrySelpatCoPhy = "select refPhy.physician_Reffer_id, TRIM(CONCAT(refPhy.Title, ' ', refPhy.LastName, ', ', refPhy.FirstName, ' ', refPhy.MiddleName)) as refName
										from patient_multi_ref_phy pmrf INNER JOIN 
										refferphysician refPhy ON pmrf.ref_phy_id = refPhy.physician_Reffer_id 
										where pmrf.patient_id = '".$_SESSION['patient']."' and pmrf.phy_type = '2' and pmrf.status = '0'";
					$rsSelpatCoPhy = imw_query($qrySelpatCoPhy);
					if(imw_num_rows($rsSelpatCoPhy) > 0){
						$nofound = 0;
						while($rowSelpatCoPhy = imw_fetch_array($rsSelpatCoPhy)){
							$subTagname = trim(core_extract_user_input($rowSelpatCoPhy['refName']));
							$subTagid = stripslashes($rowSelpatCoPhy['physician_Reffer_id']);
							$records .= '
							<tr'.$class.'>
								<td style="width:25px" class="alignCenter"><input value="'.$subTagname.'" type="checkbox" name="chkSmartTagOptions" id="txtTagOpt_'.$under.'_'.$subTagid.'"></td>
								<td style="width:auto;">'.$subTagname.'</td>
							</tr>
							';
							if($alt_class==' class="alt"'){$alt_class='';}else{$alt_class=' class="alt"';}
						}//end of while.
					}
					else{
						$records .= '
						<tr'.$class.'>
							<td colspan="2" class="alignCenter warning text12b">Multiple Patient Co-Managed Physician are not found!</td>
						</tr>
						';
						$nofound = 1;
					}
					$records .= '
						</table>
					</div>';
				}
				//echo $records;
				if(!$nofound){
					$records .= '
					<div class="mt10 alignCenter">
						<input type="button" id="btn_subtag_save" class="dff_button" value="DONE" style="width:120px;" onclick="replace_tag_with_options();">
					</div>';
				}else{
					$records .= '
					<div class="mt10 alignCenter">
						<input type="button" id="btn_subtag_save" class="dff_button" value="Close" style="width:120px;" onclick="$(\'#div_smart_tags_options\').hide();">
					</div>';		
				}
				if(empty($records) == false){
					$return_arr['txt'] = $records;
					$return_arr['counter'] = $counter;
				}
			break;	
		}
		
		if($counter > 0){
			$msg = 'Record saved successfully';
		}
		$return_arr['msg'] = $msg;
		return $return_arr;
	}
	
	
	//Returns html for template preview
	public function get_template_preview($id){
		$return_str = '<input type="button" class="btn btn-success btn-sm" value="Template Preview" onClick="get_template_preview();">';
		return $return_str;
	}
	
	//Performs data manipulation according to action
	public function manipulate_data($action = '',$request){
		$return_arr = $where_arr = array();
		$notification = 'Some error occured !';
		$unique_doc = false; //Used to check curent doc exists or not
		//pre("action: " . $action);
        $templateName = $update_field = $update_value = $content = $del_id = '';
        $arrayRecord=array();
		//pre("curr tab: ".$this->current_tab);die;
		switch($this->current_tab){
			case 'collection':
				$templateName = $request['templateName'];
				$update_value = $request['edit_id'];
				$content = $request['content'];
				$del_id =  $request['delId'];
				unset($arrayRecord);	
				
				if(empty($request['edit_id']) == true){
					$where_arr['collection_name'] = $request['templateName'];
					$unique_doc = $this->check_existing('collection_name',$where_arr);
					$arrayRecord['unique_doc'] = $unique_doc;
				}
				
				$arrayRecord['collection_name'] = $templateName;
				$arrayRecord['collection_data'] = $content;
				$update_field = 'id';
			break;
			
			case 'consent':
				unset($arrayRecord);
				$arrayRecord['consent_form_name'] = $request['consent_form_name'];
				$arrayRecord['consent_form_content'] = addslashes(trim($request['consentForm']));
				$arrayRecord['cat_id'] = $request['consent_cat'];
				$arrayRecord['operator_id'] = $_SESSION['authId'];
				$arrayRecord['yearly_review'] = isset($request['yreview']) ? 1 : 0;
				
				if(empty($request['consent_form_id']) == true){
					$where_arr['consent_form_name'] = $request['consent_form_name'];
					$where_arr['cat_id'] = $request['consent_cat'];
					$unique_doc = $this->check_existing('consent_form_name',$where_arr);
					
					$arrayRecord['unique_doc'] = $unique_doc;
					$action = 'add';
				}else{
					$action = 'update';
				}
				$update_field = 'consent_form_id';
				$update_value = $request['consent_form_id'];
			break;
			
			case 'package':
				unset($arrayRecord);
				if($action != 'delete'){
					$package_category_id = $request['package_category_id'];
					$operator_id = $_SESSION['authId'];
					$save_date_time = date("Y-m-d H:i:s");
					
					$arrayRecord['package_category_name'] = $request['package_category_name'];
					$arrayRecord['package_consent_form'] = $request['package_consent_form'];
					$arrayRecord['operator_id'] = $operator_id;
					$arrayRecord['save_date_time'] = $save_date_time;
					$update_field = 'package_category_id';
					$update_value = $package_category_id;
					if($package_category_id != ''){
						$action = 'update';
					}else{
						$where_arr['package_category_name'] = $request['package_category_name'];
                        $where_arr['delete_status'] = '';
						if($package_category_id){
							$where_arr['package_category_id'] = $package_category_id;
						}
						$unique_doc = $this->check_existing('package_category_id',$where_arr);
						
						$arrayRecord['unique_doc'] = $unique_doc;
						$action = 'add';
					}
				}else{
					$arrayRecord = $request['delIds'];
				}
			break;
			
			case 'consult':
				$doc = new DomDocument();
				$FCKeditor1 = mb_convert_encoding($request['content'], 'HTML-ENTITIES', "UTF-8");
				@$doc->loadHTML($FCKeditor1); 
				$FCKeditor1 = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>','&#9744;','&#9633;'), array('', '', '', '', '', ' '), $doc->saveHTML())); 
				
				$arrayRecord['consultTemplateData'] = $FCKeditor1;
				$arrayRecord['consultTemplateName'] = str_ireplace('"','/',$request['templateName']);
				$arrayRecord['consultTemplateType'] = $request['templateType'];
				$arrayRecord['top_margin'] 			= $request['top_margin'];
				$arrayRecord['left_margin']			= $request['left_margin'];
				$arrayRecord['complete_consult_report']= $request['complete_consult_report'];
				$arrayRecord['footer']				= $request['footer_chk'];
				$arrayRecord['patient_header']		= $request['header_chk'];
				$arrayRecord['leftpanel'] 			= $request['leftpanel_chk'];
				$consultLeter_id = (isset($request['consultLeter_id']) && $request['consultLeter_id'] != '') ? $request['consultLeter_id'] : '';
				$update_field = 'consultLeter_id';
				$update_value = $consultLeter_id;
				if($consultLeter_id != ''){
					$action = 'update';
				}else{
					$where_arr['consultTemplateName'] = $request['templateName'];
					$unique_doc = $this->check_existing('consultTemplateName',$where_arr);
                    $arrayRecord['unique_doc'] = $unique_doc;
					
					$action = 'add';	
				}
			break;
			
			case 'op_notes':
				unset($arrayRecord);
				$where_arr = array();
				
				$call_from = (isset($request['elem_formAction']) && empty($request['elem_formAction']) == false) ? $request['elem_formAction'] : 'pnAdmin';
				switch($call_from){
					case 'pnAdmin':
						$arrayRecord['temp_name'] = $request["templateName"];
						$arrayRecord['temp_data'] = $request["content"];
						if(isset($request['elem_edit_id']) && empty($request['elem_edit_id']) == false){
							$arrayRecord['temp_id'] = $request["elem_edit_id"];	
							$action = 'update';
						}else{
							$where_arr['LCASE(temp_name)'] = strtolower($request["templateName"]);
							$unique_doc = $this->check_existing('temp_id,temp_name',$where_arr);
							$arrayRecord['unique_doc'] = $unique_doc;
							$action = 'add';
						}
					break;
				}
				$update_field = 'temp_id';
				$update_value = $request["elem_edit_id"];
			break;
			
			case 'recall':
				$arrayRecord['recallTemplateData'] = $request['content'];
				$arrayRecord['recallTemplateName'] = $request['templateName'];
				
				if(isset($request["recallLeter_id"]) && empty($request["recallLeter_id"]) == false){
					$action = 'update';
				}else{
					$where_arr['recallTemplateName'] = $request['templateName'];
					$unique_doc = $this->check_existing('recallTemplateName',$where_arr);
					$arrayRecord['unique_doc'] = $unique_doc;
					$action = 'add';
				}
				
				$update_field = 'recallLeter_id';
				$update_value = $request["recallLeter_id"];
			break;
			
			case 'pt_docs':
				$pt_docs_template_content = trim($request['content']);
				$string = "<p>
					&nbsp;</p>";
				$ep = explode('</table>',$pt_docs_template_content);
				$aa = end($ep);
				$bb = str_ireplace($string,'',$aa);
				$StrLen = strlen($aa);
				$cc = substr($pt_docs_template_content,0,(strlen($pt_docs_template_content)-$StrLen));
				
				$pt_docs_template_content = $cc.$bb;
				
				if(empty($request['pt_docs_enable_facesheet']) == false){
					$update_arr['pt_docs_template_enable_facesheet'] = '';
					$status = $this->updateRecords($update_arr, $this->cur_tab_table, "pt_docs_template_id != '0' AND pt_docs_template_enable_facesheet", $request['pt_docs_enable_facesheet']);
				}
				
				$arrayRecord["pt_docs_template_name"] = $request['templateName'];
				$arrayRecord["pt_docs_template_content"] = trim($pt_docs_template_content);
				$arrayRecord["pt_docs_template_category_id"] = $request['pt_docs_category'];
				$arrayRecord["pt_docs_template_enable_facesheet"] = $request['pt_docs_enable_facesheet'];
				$arrayRecord["pt_docs_template_created_date"] = date('Y-m-d H:i:s');
				$arrayRecord["pt_docs_template_created_operator_id"] = $_SESSION['authId'];
				$arrayRecord["pt_docs_template_status"] = '0';
				
				if(empty($request['pt_docs_template_id']) == false){
					$action = 'update';
				}else{
					$where_arr['pt_docs_template_name'] = $request['templateName'];
					$where_arr['pt_docs_template_status'] = '0';
					$unique_doc = $this->check_existing('pt_docs_template_name',$where_arr);
					$arrayRecord['unique_doc'] = $unique_doc;
					$action = 'add';
				}
				$update_field = 'pt_docs_template_id';
				$update_value = $request["pt_docs_template_id"];
			break;
			
			case 'order_temp':
				$template_name = $request['template_name'];
				$update_value = $request['edit_id'];
				$content = $request['content'];
				$order_type_id =  $request['order_type_id'];
				unset($arrayRecord);	
				
				$arrayRecord['template_name'] = $template_name;
				$arrayRecord['template_content'] = addslashes(trim($content));
				$arrayRecord['order_type_id'] = $order_type_id;
				$arrayRecord['operator_id'] = $_SESSION['authId'];
				$arrayRecord['created_on'] = date('Y-m-d H:i:s');
				
				$action = 'update';
				if(empty($request['edit_id']) == true){
					$where_arr['template_name'] = $request['template_name'];
					$unique_doc = $this->check_existing('template_name',$where_arr);
					$arrayRecord['unique_doc'] = $unique_doc;
					$action = 'add';
				}
				$update_field = 'template_id';
			break;
			
			case 'panels':
				$panel_id	= $request['panel_id'];
				$leftpanel 	= $request['leftpanel'];
				$header 	= $request['header'];
				$footer 	= $request['footer'];
				
				unset($arrayRecord);	
				
				if(empty($leftpanel) == false){
					$arrayRecord['leftpanel'] = $request['leftpanel'];
				}
				
				if(empty($header) == false){
					$arrayRecord['header'] = $header;
				}
				
				if(empty($footer) == false){
					$arrayRecord['footer'] = $footer;
				}
				
				if($panel_id){
					$action = 'update';
					$update_value = $panel_id;
				}else{		
					$action = 'add';
				}
				$update_field = 'id'; 
			break;
			
			case 'surgery_consent':
			
				unset($arrayRecord);
				$arrayRecord['consent_name'] = $request['consent_form_name'];
				$arrayRecord['consent_data'] = addslashes(trim($request['consentForm']));
				$arrayRecord['consent_category_id'] = $request['consent_cat'];
				
				if(empty($request['consent_form_id']) == true){
					$arrayRecord['form_created_from'] = 'user';
					$where_arr['consent_name'] = $request['consent_form_name'];
					$where_arr['consent_category_id'] = $request['consent_cat'];
					$unique_doc = $this->check_existing('consent_name',$where_arr);
					
					$arrayRecord['unique_doc'] = $unique_doc;
					$action = 'add';
				}else{
					$action = 'update';
				}
				$update_field = 'consent_id';
				$update_value = $request['consent_form_id'];
			break;
			
			case 'prescriptions':
			
				unset($arrayRecord);
				$arrayRecord['prescription_template_name'] = $request['prescription_template_name'];
				$arrayRecord['prescription_template_content'] = addslashes(trim($request['content']));
				$arrayRecord['prescription_template_type'] = addslashes(trim($request['prescriptionType']));
				$arrayRecord['prescription_template_created_date_time'] = date('Y-m-d g:i:s A');
				$arrayRecord['prescription_template_created_by'] = $_SESSION['authId'];
				$arrayRecord['printOption'] = $request['printoption'];
				
				if(empty($request['edit_id']) == true){
					$action = 'add';
				}else{
					$action = 'update';
				}
				$update_field = 'prescription_template_type';
				$update_value = $request['edit_id'];
			break;	
		}
		
		//Checking if current document name exist or not
		if($arrayRecord['unique_doc'] == true && $action != "delete"){
			$notification = $this->unique_doc_msg;
			$action = '';
		}
		if(array_key_exists('unique_doc', $arrayRecord)){
		  unset($arrayRecord['unique_doc']);
		}
		switch($action){
			case 'update':
				$status = $this->updateRecords($arrayRecord, $this->cur_tab_table, $update_field, $update_value);
				if($status > 0){
					$notification = "Record Updated Successfully.";
				}else if($status == 0){
					$notification = "No changes detected";
				}
				$return_arr['edit_id'] = $update_value;
			break;
			
			case 'add':
				$status = $this->addRecords($arrayRecord, $this->cur_tab_table);
				if($status){
					$notification = "Record Added Successfully.";
				}
			break;	
			
			case 'delete':
				if($this->current_tab == 'package'){
				    //pre("UPDATE ".$this->cur_tab_table." set delete_status='yes' WHERE package_category_id IN (".$arrayRecord.")");
				    //die;
					$del_qry = imw_query("UPDATE ".$this->cur_tab_table." set delete_status='yes' WHERE package_category_id IN (".$arrayRecord.")");
					if($del_qry){
						$notification = "Record Deleted Successfully.";
					}
				}
			break;
		}
		
		$return_arr['status'] = $notification;
		$return_arr['action'] = $action;
		
		return $return_arr;
	}
	
	//Returns filtered name for creating toggles
	public function get_toggle_nm($name){
		 //Lower case everything
		$string = strtolower($name);
		//Make alphanumeric (removes all other characters)
		$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
		//Clean up multiple dashes or whitespaces
		$string = preg_replace("/[\s-]+/", " ", $string);
		//Convert whitespaces and underscore to dash
		$string = preg_replace("/[\s-]/", "_", $string);
		
		return $string;
	}
	
	public function addRecords($arrayRecord, $table){
		if(is_array($arrayRecord)){
			$countFields = count($arrayRecord);
			$insertStr = "INSERT INTO $table SET ";
			foreach($arrayRecord as $field => $value){
				++$seq;
				$insertStr .= "$field = '".addslashes($value)."'";
				if($seq<$countFields){
					$insertStr .= ", ";
				}
			}
			$insertQry = imw_query($insertStr);
			$insertId = imw_insert_id();
			return $insertId;
		}		
	}
	
	// UPDATE TABLE ROW
	public function updateRecords($arrayRecord, $table, $condId, $condValue){
		$counter = 0;
		if(is_array($arrayRecord)){
			$countFields = count($arrayRecord);
			$updateStr = "UPDATE $table SET ";
			foreach($arrayRecord as $field => $value){
				++$seq;
				$updateStr .= "$field = '".addslashes($value)."'";
				if($seq<$countFields){
					$updateStr .= ", ";
				}
			}
			$updateStr .= " WHERE $condId = '$condValue'";
			$updateQry = imw_query($updateStr);
			$counter = $counter + imw_affected_rows();
		}		
		return $counter;
	}
	// UPDATE TABLE ROW
	
	// DELETE RECORD
	public function delRecord($table, $field, $value){
		$return_str = "";
		$validate = true;
		$counter = 0;
		switch($this->current_tab){
			case 'logos':
				$target_dir = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/document_logos";
				$del_res = imw_query("SELECT img_url FROM $table WHERE $field = '$value' LIMIT 0,1");
					if($del_res && imw_num_rows($del_res)==1){
					$del_rs = imw_fetch_assoc($del_res);
					unlink($target_dir.'/'.basename($del_rs['img_url']));
					if(file_exists($target_dir.'/'.basename($del_rs['img_url']))){
						$value  = "";
					}
				
				}
			break;
			
			case 'smart_tags':
				$validate = false;
				$created_by = $_SESSION['authId'];
				$created_on = date('Y-m-d H:i:s');
				$del_result = imw_query("UPDATE $this->cur_tab_table SET status=0, modified_by='".$created_by."', modified_on='".$created_on."' WHERE $field = $value");
				if($del_result){ 
					$return_str = 'delete_success';
				}else{
					$return_str = 'Unable to delete tag';
				}
			break;
		}
		if($validate){
			$qryStr = "DELETE FROM $table WHERE $field = '$value'";
			$qryQry = imw_query($qryStr);
			$counter = $counter + imw_affected_rows();
			if($counter > 0){
				$return_str = "delete_success";
			}
		}
		return $return_str;
	}
	
	//Returns array of variables needed in js file
	public function get_js_arr($tab){
		$js_php_arr = $return_arr = array();
		$js_php_arr['cur_tab'] = $tab;
		$js_php_arr['header_title'] = $this->get_header_title($tab);
		$js_php_arr['logo_urls'] = $this->logo_data['logo_url'];
		
		return $js_php_arr;
	}
	
	//Returns header title for the current section
	public function get_header_title($section){
		$return_val = '';
		switch($section){
			case 'collection':
				$return_val = 'Collection';
			break;
			
			case 'consent':
				$return_val = 'Consents';
			break;
			
			case 'package':
				$return_val = 'Packages';
			break;
			
			case 'consult':
				$return_val = 'Consults';
			break;
			
			case 'education':
				$return_val = 'Education';
			break;

			case 'instructions':
				$return_val = 'Instructions';
			break;
			
			case 'op_notes':
				$return_val = 'Op Notes';
			break;

			case 'recall':
				$return_val = 'Recall';
			break;	
			
			case 'pt_docs':
				$return_val = 'Pt. Docs';
			break;	
			
			case 'statements':
				$return_val = 'Statements';
			break;
			
			case 'smart_tags':
				$return_val = 'Smart Tags';
			break;		
			
			case 'logos':
				$return_val = 'Logos';
			break;
			
			case 'panels':
				$return_val = 'Panels';
			break;
			
			case 'order_temp':
				$return_val = 'Order Templates';
			break;
			
			case 'prescriptions':
				$return_val = 'Prescriptions';
			break;
			
		}
		return $return_val;
	}
}
?>