<?php
/**
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 * Use this software under MIT License
 *
 * Purpose : Contains functions for several modules of application
 * Access Type: Included
 *
 */

//Function files
//require_once(dirname(__FILE__).'/database.php');

include_once($GLOBALS['fileroot']."/library/classes/functions_ptInfo.php");
//require_once(dirname(__FILE__)."/../../library/smarty/libs/Smarty.class.php");
$GLOBALS['REPORTS_PDF_FOLDER'] = $GLOBALS['rootdir'].'/reports/new_html2pdf';
$DirPath = substr($_SERVER['PATH_TRANSLATED'],0,strrpos($_SERVER['PATH_TRANSLATED'],'/'));
define(mainDir,$DirPath);

class ManageData {
	public $QUERY_STRING;
	protected $result_data;
	protected $default_file_name;
	public $adoDbObj;
	// Tabs display Order	
	//---- iMedicR4 main tabs Array ---------
	public function __getFormsVocab($formName) {
		$allFormsArr = $ptInfoArr = $cnsntFrmArr = $cnsltLtrArr = $commonPtInfoArr1 = $commonPtInfoArr2 = $commonPtInfoArr3 = $commonPtInfoArr4 = $commonPtInfoArr5 = $commonPtInfoArr6 = $commonPtInfoArr7 = $commonPtInfoArr8 = $commonPtInfoArr9 = $commonPtInfoArr10 = $commonPtInfoArr11 = $commonPtInfoArr12 = $commonPtInfoArr13 = $commonPtInfoArr14 = $commonPtInfoArr15 = $commonPtInfoArr16 = $commonPtInfoArr17 = $commonPtInfoArr18 = array();

		$commonPtInfoArr1	 = array(
							 		 "{SEX}"						=>"Patient Gender information : ", 	
									 "{PATIENT SS}"					=>"Patient SS Number : ",			
									 "{MARITAL STATUS}"				=>"Patient Marital Status : ",	   	
									 "{EMERGENCY CONTACT}"			=>"Patient Emergency Contact : ",	"{EMERGENCY CONTACT PH}"	=>"Patient Emergency Contact Ph. : ",
									 "{REGISTRATION DATE}"			=>"Registration Date : ",			"{POS FACILITY}"			=>"Facility : ",
									 "{DRIVING LICENSE}"			=>"Driving License : ",			
									 
									 "{RES.PARTY TITLE}"			=>"Res. Party Title : ",
									 "{RES.PARTY MIDDLE NAME}"		=>"Res. Party Middle Name : ",
									 "{RES.PARTY DOB}"				=>"Res. Party DOB : ",
									 "{RES.PARTY SS}"				=>"Res. Party SS : ",				"{RES.PARTY SEX}"			=>"Res. Party Gender Info. : ",
									 "{RES.PARTY RELATION}"			=>"Res. Party Relation : ",			
									 /*"{RES.PARTY CITY}"				=>"Res. Party City : ",				"{RES.PARTY STATE}"			=>"Res. Party State : ",
									 "{RES.PARTY ZIP}"				=>"Res. Party Zip : ",				*/
									 "{RES.PARTY MARITAL STATUS}"=>"Res. Party Marital Status : ",
									 "{RES.PARTY DD NUMBER}"		=>"Res. Party DD Number : ",
									 
									 "{PATIENT OCCUPATION}"			=>"Patient Occupation : ",
									 "{PATIENT EMPLOYER}"			=>"Patient Employer : ",			"{OCCUPATION ADDRESS1}"		=>"Patient Occupation Address1 : ",
									 "{OCCUPATION ADDRESS2}"		=>"Patient Occupation Address2 : ",	"{OCCUPATION CITY}"			=>"Patient Occupation City : ",
									 "{OCCUPATION STATE}"			=>"Patient Occupation State : ",	"{OCCUPATION ZIP}"			=>"Patient Occupation Zip : ",
									 "{MONTHLY INCOME}"				=>"Monthly Income : ",				"{PATIENT INITIAL}"			=>"Patient Initial : ",
									 "{TIME}"						=>"Current Time : ",
									 "{OPERATOR NAME}"				=>"Operator Name : ",				"{OPERATOR INITIAL}"		=>"Operator Initial : ",
									 "{HEARD ABOUT US}"				=>"Heard About Us : ",				"{HEARD ABOUT US DETAIL}"	=>"Heard About Us Detail : ",
									 "{EMAIL ADDRESS}"				=>"Patient E-mail : ",			/*"{USER DEFINE 1}"			=>"User Defined 1 : ",
									 "{USER DEFINE 2}"				=>"User Defined 2 : ",*/

		
									 "{PRIMARY INSURANCE COMPANY}"			=>"Primary Insurance company : ",		"{PRIMARY POLICY #}"					=>"Primary Policy # : ",
									 "{PRIMARY GROUP #}"					=>"Primary Group # : ",					"{PRIMARY SUBSCRIBER NAME}"				=>"Primary Subscriber Name : ",
									 "{PRIMARY SUBSCRIBER RELATIONSHIP}"	=>"Primary Subscriber Relationship : ",	"{PRIMARY BIRTHDATE}"					=>"Primary Birthdate : ",
									 "{PRIMARY SOCIAL SECURITY}"			=>"Primary Social Security : ",			"{PRIMARY PHONE}"						=>"Primary Phone : ",
									 "{PRIMARY ADDRESS}"					=>"Primary Address : ",					"{PRIMARY CITY}"						=>"Primary City : ",
									 "{PRIMARY STATE}"						=>"Primary State : ",					"{PRIMARY ZIP}"							=>"Primary Zip : ",
									 "{PRIMARY EMPLOYER}"					=>"Primary Employer : ",				"{SECONDARY INSURANCE COMPANY}"			=>"Secondary Insurance company : ",
									 "{SECONDARY POLICY #}"					=>"Secondary Policy # : ",				"{SECONDARY GROUP #}"					=>"Secondary Group # : ",
									 "{SECONDARY SUBSCRIBER NAME}"			=>"Secondary Subscriber Name : ",		"{SECONDARY SUBSCRIBER RELATIONSHIP}"	=>"Secondary Subscriber Relationship : ",
									 "{SECONDARY BIRTHDATE}"				=>"Secondary Birthdate : ",				"{SECONDARY SOCIAL SECURITY}"			=>"Secondary Social Security : ",
									 "{SECONDARY PHONE}"					=>"Secondary Phone : ",					"{SECONDARY ADDRESS}"					=>"Secondary Address : ",
									 "{SECONDARY CITY}"						=>"Secondary City : ",					"{SECONDARY STATE}"						=>"Secondary State : ",
									 "{SECONDARY ZIP}"						=>"Secondary Zip : ",					"{SECONDARY EMPLOYER}"					=>"Secondary Employer : "
		
									);			
		

		$commonPtInfoArr2	 = array("{PATIENT NAME TITLE}"					=>"Patient Name Title : ",				"{PATIENT FIRST NAME}"					=>"Patient First Name : ",
									 "{MIDDLE NAME}"						=>"Patient Middle Name : ",				"{LAST NAME}"							=>"Patient Last Name : ",
									 "{DOB}"								=>"Patient Date of Birth : ",			"{PatientID}"							=>"Patient ID : ",
									 "{ADDRESS1}"							=>"Patient Address1 : ",				"{ADDRESS2}"							=>"Patient Address2 : ",			
									 "{PATIENT CITY}"						=>"Patient City : ", 					"{STATE ZIP CODE}"						=>"Pt. State Zip Code : ",	
									 "{HOME PHONE}"							=>"Patient Home Phone : ",				"{WORK PHONE}"							=>"Patient Work Phone : ",
									 "{MOBILE PHONE}"						=>"Patient Mobile Phone : ", 			"{PATIENT MRN}"							=>"Patient MRN : ",
									 "{PATIENT MRN2}"						=>"Patient MRN2 : ",					"{DATE}"								=>"Date : ",
									 "{LANGUAGE}"							=>"Language : ",	    				"{RACE}"								=>"Race : ",	
									 "{ETHNICITY}"							=>"Ethnicity : "
									
									
									);

		$commonPtInfoArr3	 = array("{RES.PARTY FIRST NAME}"				=>"Res. Party First Name : ",			
									 "{RES.PARTY LAST NAME}"				=>"Res. Party Last Name : ",			"{RES.PARTY ADDRESS1}"					=>"Res. Party Address1 : ",
									 "{RES.PARTY ADDRESS2}"					=>"Res. Party Address2 : ",				"{RES.PARTY HOME PH.}"					=>"Res. Party Home Ph. : ",
									 "{RES.PARTY WORK PH.}"					=>"Res. Party Work Ph. : ",				"{RES.PARTY MOBILE PH.}"				=>"Res. Party Mobile Ph. : "
									);
		
		$commonPtInfoArr4	 = array("{REFFERING PHY.}"						=>"Referring Phy. : "			
									);

		$commonPtInfoArr5	 = array("{REF PHYSICIAN FIRST NAME}"	=>"Ref. Physician First Name : ",	"{REF PHYSICIAN LAST NAME}"	=>"Ref. Physician Last Name : ", 
									 "{REF PHY SPECIALITY}"			=>"Ref. Phy Speciality : ",			"{REF PHY PHONE}"			=>"Ref. Phy Phone : ",
									 "{REF PHY STREET ADDR}"		=>"Ref. Phy Address : ",			"{REF PHY CITY}"			=>"Ref. Phy City : ",
									 "{REF PHY STATE}"				=>"Ref. Phy State : ",				"{REF PHY ZIP}"				=>"Ref. Phy Zip : ",
									 "{REF PHY FAX}"				=>"Ref. Phy Fax : "
									);		

		$commonPtInfoArr6	 = array("{PCP STREET ADDR}"			=>"PCP Address : ",					"{PCP City}"				=>"PCP City : ",
									 "{PCP State}"					=>"PCP State : ",					"{PCP ZIP}"					=>"PCP ZIP : ",
									 "{STATE ZIP CODE}"				=>"Pt. State Zip Code : ",			"{REF PHYSICIAN TITLE}"		=>"Ref. Physician Title : ",
									 "{DATE_F}"						=>"Date Format : ",					
									); 
		
		$commonPtInfoArr7 	 = array("{AGE}"						=>"Patient Age : "				
									);
		
		$commonPtInfoArr8	=  array("{FULL ADDRESS}"				=>"Patient Full Address : ", 		"{PATIENT NAME}"			=>"Patient Name : "
									);
		$commonPtInfoArr9 = array("{PHYSICIAN SIGNATURE}"			=>"Physician Signature : ",
								);
		
		$commonPtInfoArr10 	 = array("{PHYSICIAN NAME}"				=>"Physician Name : ", 				"{PHYSICIAN FIRST NAME}"	=>"Physician First Name : ",				
									 "{PHYSICIAN MIDDLE NAME}"		=>"Physician Middle Name : ",		"{PHYSICIAN LAST NAME}"		=>"Physician Last Name : ",
									 "{PHYSICIAN NAME SUFFIX}"		=>"Physician Name Suffix : " 	
									);
		$commonPtInfoArr11 	 = array("{PATIENT PHOTO}"				=>"Patient Photo : ",  			    "{LAST NAME LEN~20~}"		=>"Last Name Len~20~ : ",
									 "{PATIENT FIRST NAME LEN~20~}" =>"Patient First Name Len~20~ : ",  "{PRIMARY INSURANCE COMPANY LEN~20~}"=>"Primary Insurance Company Len~20~",
									 "{SECONDARY INSURANCE COMPANY LEN~20~}" =>"Secondary Insurance Company Len~20~ : ",
									 "{PRIMARY INSURANCE SCAN CARD}"		=> "Primary Insurance Scan Card", 		"{SECONDARY INSURANCE SCAN CARD}"	=> "Secondary Insurance Scan Card"
									 
									);
									
		$commonPtInfoArr12 = array("{REL INFO}"						=>"Release Information : "
								);
		
		$commonPtInfoArr13	= array("{PATIENT STATE}"				=>"Patient State : ");

		$commonPtInfoArr14 	 = array("{PRI INS ADDR}"				=>"Pri. Insurance Address : ",		"{SEC INS ADDR}"				=>"Sec. Insurance Address : "
									);

		$commonPtInfoArr15	 = array("{PATIENT ZIP}"				=>"Patient Zip : "			
									);
		$commonPtInfoArr16	 = array("{RES.PARTY CITY}"				=>"Res. Party City : ",				"{RES.PARTY STATE}"			=>"Res. Party State : ",
									 "{RES.PARTY ZIP}"				=>"Res. Party Zip : ");
									 
		$commonPtInfoArr17	 = array("{PCP NAME}"					=>"PCP Name : ",					"{ALL_INS_CASE}"			=>	"ALL INS CASE : "
									);
		$commonPtInfoArr18	 = array("{PATIENT_SS4}"		    	=>"Patient Last 4 Digit SS Number : ");						
		
		$commonEncounter1 	= array("{DOS}"							=>"Date Of Service : "
									);
		$commonEncounter2	= array(			
									"{DISTANCE}"					=>"Distance : ",					"{MEDICAL DOCTOR}"			=>"PCP : ",
									"{PUPIL OU}"					=>"Pupil OU : ",					"{PUPIL OD}"				=>"Pupil OD : ",
									"{PUPIL OS}"					=>"Pupil OS : ",					"{EXTERNAL OU}"				=>"External OU : ",
									"{EXTERNAL OD}"					=>"External OD : ",					"{EXTERNAL OS}"				=>"External OS : ",
									"{L&A OU}"						=>"L&A OU : ",						"{L&A OD}"					=>"L&A OD : ",
									"{L&A OS}"						=>"L&A OS : ",						"{IOP}"						=>"IOP : ",
									"{IOP OD}"						=>"IOP OD : ",						"{IOP OS}"					=>"IOP OS : ",
									"{GONIO OU}"					=>"Gonio OU : ",					"{GONIO OD}"				=>"Gonio OD : ",
									"{GONIO OS}"					=>"Gonio OS : ",					"{SLE OU}"					=>"SLE OU : ",
									"{SLE OD}"						=>"SLE OD : ",						"{SLE OS}"					=>"SLE OS : ",
									"{FUNDUS EXAM OU}"				=>"Fundus Exam OU : ",				"{FUNDUS EXAM OD}"			=>"Fundus Exam OD : ",
									"{FUNDUS EXAM OS}"				=>"Fundus Exam OS : ",				"{FOLLOW-UP}"				=>"FOLLOW-UP : ",					
									"{CVF OD}"						=>"CVF OD : ",						"{CVF OS}"					=>"CVF OS : ",		
									"{EOM}"							=>"EOM : ",							"{IOP OD WITHOUT PACHY}"	=>"IOP OD WITHOUT PACHY : ",
									"{IOP OS WITHOUT PACHY}"		=>"IOP OS WITHOUT PACHY: ",			"{C:D_OD}"					=>"CUP DISC OD: ",
									"{C:D_OS}"						=>"CUP DISC OS: ", 					"{IRIS_OD}"					=>"IRIS OD: ",
									"{IRIS_OS}"						=>"IRIS OS: "
									);
		
		
		$commonEncounter3= array("{PRIMARY PHYSICIAN}"			=>"Primary Physician : ",			"{PINHOLE OD}"				=>"Pinhole OD : ",
								 "{PINHOLE OS}"					=>"Pinhole OS : ",					"{SLE}"						=>"SLE : ",
								 "{CONJ_OD}"					=>"CONJ OD : ",						"{CONJ_OS}"					=>"CONJ OS : ",
								 "{CORNEA_OD}"					=>"CORNEA OD : ",					"{CORNEA_OS}"				=>"CORNEA OS : ",
								 "{ANTCHAMBER_OD}"				=>"ANTCHAMBER OD : ",				"{ANTCHAMBER_OS}"			=>"ANTCHAMBER OS : ",
								 "{LENS_OD}"					=>"LENS OD : ",						"{LENS_OS}"					=>"LENS OS : ",
								 "{FUNDUS}"						=>"FUNDUS : ",						"{OPTICNERVE_OD}"			=>"OPTICNERVE OD  : ",
								 "{OPTICNERVE_OS}"				=>"OPTICNERVE OS : ",				"{MACULA_OD}"				=>"MACULA OD : ",
								 "{MACULA_OS}"					=>"MACULA OS : ",					"{VITREOUS_OD}"				=>"VITREOUS OD : ",
								 "{VITREOUS_OS}"				=>"VITREOUS OS : ",					"{PERIPHERY_OD}"			=>"PERIPHERY OD : ",
								 "{PERIPHERY_OS}"				=>"PERIPHERY OS : ",				"{BV_OD}"					=>"BLOOD VESSELS OD : ",
								 "{RETINAL_EX_OD}"				=>"RETINAL EX OD : ",				"{RETINAL_EX_OS}"			=>"RETINAL EX OS : ",
								 "{BV_OS}"						=>"BLOOD VESSELS OS : "
								);		
		

		$commonEncounter4 = array("{A & P}"						=>"A & P : "
								);
		
		$commonEncounter5	= array( "{SIGNATURE}"					=>"Signature : ",						
									 "{TEXTBOX_XSMALL}"				=>"Very Small Textbox : ",		"{TEXTBOX_SMALL}"			=>"Small Textbox : ",
									 "{TEXTBOX_MEDIUM}"				=>"Medium Textbox : ",				
									);
		
		$commonEncounter6	= array("{OD SPHERICAL}"	=>"OD SPHERICAL : ",	"{OD CYLINDER}"		=>"OD CYLINDER : ",
									"{OD AXIS}"			=>"OD AXIS : ",			"{OD BASE CURVE}"	=>"OD BASE CURVE : ",		
								    "{OD ADD}"			=>"OD ADD : ",			"{OD COLOR}"		=>"OD COLOR : ",			
									"{OS SPHERICAL}"	=>"OS SPHERICAL : ",	"{OS CYLINDER}"		=>"OS CYLINDER : ",
									"{OS AXIS}"			=>"OS AXIS : ",			"{OS BASE CURVE}"	=>"OS BASE CURVE : ",
									"{OS ADD}"			=>"OS ADD : ",			"{OS COLOR}"		=>"OS COLOR : ",
									"{OU SPHERICAL}"	=>"OU SPHERICAL : ",	"{OU CYLINDER}"		=>"OU CYLINDER : ",
									"{OU AXIS}"			=>"OU AXIS : ",			"{OU BASE CURVE}"	=>"OU BASE CURVE : ",
									"{OU ADD}"			=>"OU ADD : ",			"{OU COLOR}"		=>"OU COLOR : ",
									"{EXPIRATION DATE}"	=>"Expiration Date : ", "{PHYSICIAN NPI}"	=>"PHYSICIAN NPI : "
							);
		$commonEncounter7	= array("{NOTES}"			=>"Print Notes  : "	
							);
		
		
		$commonEncounter8 = array("{V-SC-OD}"				=>"V-SC-OD : ",					"{V-SC-OS}"				=>"V-SC-OS : ",
								  "{INITIAL IOP}"			=>"Initial IOP : ",				"{INITIAL IOP TIME}"	=>"Initial IOP Time : ",
								  "{POST IOP}"				=>"Post IOP : ",				"{POST IOP TIME}"		=>"Post IOP Time : ",
								  "{OCULAR HISTORY}"		=>"Ocular History : ",			"{SITE}"				=>"Patient Site : ",
								  "{VITAL SIGN}"			=>"Vital Sign : ",				"{SCRIBED BY}"			=>"Scribed By : ",
							      "{NEAR_OD}"				=>"NEAR OD : ",					"{NEAR_OS}"				=>"NEAR OS : ",					  "{PACHYMETRY OD}"			=>"PACHYMETRY OD : ",				"{PACHYMETRY OS}"						=>"PACHYMETRY OS : ",				"{KERATOMETRY OD}"				=>"KERATOMETRY OD : ",				"{KERATOMETRY OS}"							=>"KERATOMETRY OS : "
							);
		
		$commonEncounter9 = array("{MR1}"					=>"MR1 : ",						"{MR2}"					=>"MR2 : ",
								  "{MR3}"					=>"MR3 : ",						"{DIABETES}"			=>"Diabetes : ",
								  "{FLASHES}"				=>"Flashes : ",					"{FLOATERS}"			=>"Floaters : ",
								  "{SMOKER}"				=>"Smokers : ",					"{FAMILY HX SMOKE}"		=>"Family Hx Smoke : ",
								  "{EMP ID}"				=>"EMP ID : ",					"{MR1 GLARE OD}"		=>"MR1 GLARE OD : ",
								  "{MR1 GLARE OS}"			=>"MR1 GLARE OS : ",			"{MR2 GLARE OD}"		=>"MR2 GLARE OD : ",
								  "{MR2 GLARE OS}"			=>"MR2 GLARE OS : ",			"{MR3 GLARE OD}"		=>"MR3 GLARE OD : ",
								  "{MR3 GLARE OS}"			=>"MR3 GLARE OS : "
							);
		$commonEncounter10	= array("{APPT_HX}"				=>"Appointment History  : ",	"{ASSESSMENT OD}"		=>"Assessment OD : ",
									"{ASSESSMENT OS}"		=>"Assessment OS : ",			"{PLAN OS}"				=>"Plan OS : ",
									"{PLAN OD}"				=>"Plan OD : ",					"{OCULAR OD}"			=>"Ocular OD : ",
									"{OCULAR OS}"		 	=>"Ocular OS : ",				"{OCULAR}"		 		=>"Ocular : ",
									"{NEAR OS}"				=>"Near OS : ",					"{NEAR OD}"				=>"Near OD : ",
									"{CYL OS}"				=>"CYL OS : ",					"{CYL OD}"				=>"CYL OD : ",
									"{DISTANCE OS}"			=>"Distance OS : ",				"{DISTANCE OD}"			=>"Distance OD : ",
									"{MR1 OD}"				=>"MR1 OD : ",					"{MR1 OS}"				=>"MR1 OS : ",
									"{BCVA OD}"				=>"BCVA OD : ",					"{BCVA OS}"				=>"BCVA OS : ");
									
		$commonEncounter11	= array("{GLARE}"				=>"Glare  : ",					
									"{PATIENT ALLERGIES}"	=>"Patient Allergies : ",		"{MEDICAL PROBLEM}"		=>"Medical Problem  : "
							);

		$commonEncounter12	= array("{GLARE_K_OD}"			=>"Glare K OD  : ",				"{GLARE_K_OS}"			=>"Glare K OS : ",	
									"{MR GIVEN}"			=>"MR Given : ",	
									"{OPERATIVE EYE}"		=>"Operative Eye  : ",			"{UNOPERATIVE EYE}"		=>"Unoperative Eye  : ",
									"{OCULAR PATHOLOGY}"	=>"Ocular Pathology  : ",		"{DOMINANT EYE}"		=>"Dominant Eye  : ",
									"{LRI}"					=>"LRI  : ",					"{LENS SELECTION}"		=>"Lens Selection  : ",
									"{APPT SITE}"			=>"Appt Site : ",
									"{PCP TITLE NAME}"		=>"PCP Title Name : ",			"{PCP FIRST NAME}"		=>"PCP First Name : ",
									"{PCP MIDDLE NAME}"		=>"PCP Middle Name : ",			"{PCP LAST NAME}"		=>"PCP Last Name : ",
									"{PCP CREDENTIAL}"		=>"PCP Credential : ",			"{PCP FULL NAME}"		=>"PCP Full Name : "
									
							);

		$commonEncounter13	= array("{PC1}"					=>"PC1  : ",					"{PC2}"					=>"PC2 : ",	
									"{PC3}"					=>"PC3  : "
							);
		$commonEncounter14	= array("{APPT DATE}"			=>"Appt Date : ",				"{APPT TIME}"			=>"Appt Time : ",
									"{APPT PROC}"			=>"Appt Procedure : "
							);
		$commonEncounter15	= array("{APPT COMMENTS}"		=>"Appt Comments : ",			"{PT-KEY}"			=>"PT-Key : ",
									"{PT_DUE}"				=>"PT-Due : ",					"{PT_COPAY}"		=>"PT-Copay : ",					"{TOTAL_DUE}"			=>"Total-Due : ",		"{PHYSICIAN NPI}" =>"PHYSICIAN NPI:"
							);
		
		$commonEncounter16	= array("{APPT PROVIDER}"		=>"Appt Provider : ",			"{APPT PROVIDER LAST NAME}"	=>"Appt Provider Last Name : ",
									"{APPT DATE_F}"			=>"Appt Date Format : ",		"{APPT FACILITY}"			=>"Appt Facility : ",	
									"{APPT FACILITY PHONE}"	=>"Appt Facility Phone : "
									);
									
		$commonEncounter17 = array("{ASSESSMENT & PLAN}"	=>"Assessment & Plan : ",		"{ASSESSMENT 1}"			=>"Assessment 1 : ",
								   "{ASSESSMENT 2}"			=>"Assessment 2 : ",			"{ASSESSMENT 3}"			=>"Assessment 3 : ",
								   "{ASSESSMENT 4}"			=>"Assessment 4 : ",			"{ASSESSMENT 5}"			=>"Assessment 5 : ",
								   "{PLAN 1}"				=>"Plan 1 : ",					"{PLAN 2}"					=>"Plan 2 : ",
								   "{PLAN 3}"				=>"Plan 3 : ",					"{PLAN 4}"					=>"Plan 4 : ",
								   "{ASSESSMENT ALL}"		=>"ASSESSMENT ALL : ",			"{PLAN ALL}"				=>"PLAN ALL : "					
								 
								);
		$commonEncounter18	= array("{PRIMARY LICENCE NUMBER}"=>"Primary Licence Number : ");
		$commonEncounter19	= array("{A & P_V}"=>"A & P_V : ");
		
		$commonEncounter20	= array("{MED HX}"				=>"Medical History : ", "{OCULAR MEDICATION}"	=>"Ocular Medication : ",
									"{SYSTEMIC MEDICATION}"	=>"Systemic Medication : ");
		$commonEncounter21	= array("{CC}"=>"CC : ","{HISTORY}" => "HISTORY : ");
		$commonEncounter22	= array("{APPT_FUTURE}"			=>"Appointment Future  : ");
		$commonEncounter23	= array("{V-CC-OD}"				=>"V-CC-OD : ",	"{V-CC-OS}"	=>	"V-CC-OS : ");
		$commonEncounter24	= array("{APPT FACILITY NAME}" =>"APPT FACILITY NAME :", "{APPT FACILITY ADDRESS}" =>"APPT FACILITY ADDRESS :");
		$commonEncounter25	= array("{APPT DATE}"			=>"Appt Date : ", "{APPT DATE_F}"			=>"Appt Date Format : ",	"{APPT PROVIDER}"		=>"Appt Provider : " );
		$commonEncounter26	= array("{LOGGED_IN_FACILITY_NAME}" =>"Logged In Facility Name :", "{LOGGED_IN_FACILITY_ADDRESS}" =>"Logged In Facility Address :");
		$commonEncounter27	= array("{V-CC-OU}" => "V-CC-OU :", "{V-SC-OU}" => "V-SC-OU :", "{NEAR_OU}" => "NEAR OU :");
		$commonEncounter28	= array("{PAM_OD}" => "PAM OD :", "{PAM_OS}" => "PAM OS :", "{PAM_OU}" => "PAM OU :", "{BAT_OD}" => "BAT OD :", "{BAT_OS}" => "BAT OS :", "{BAT_OU}" => "BAT OU :", "{AR_OD}" => "AUTOREFRACTION OD :", "{AR_OS}" => "AUTOREFRACTION OS :", "{CYCLOPLEGIC_OD}" => "CYCLOPLEGIC OD :", "{CYCLOPLEGIC_OS}" => "CYCLOPLEGIC OS :");
	  //$replace_data_arr['GLARE_K_OD'] = $glareInfoArr[1];
		//$replace_data_arr['GLARE_K_OS'] = $glareInfoArr[2];

		if($formName == "consent_form") {
			$ptInfoArr	= array_merge($commonPtInfoArr1,$commonPtInfoArr2,$commonPtInfoArr3,$commonPtInfoArr4,$commonPtInfoArr5,$commonPtInfoArr6,$commonPtInfoArr7,$commonPtInfoArr9,$commonPtInfoArr10,$commonPtInfoArr12,$commonPtInfoArr13,$commonPtInfoArr14,$commonPtInfoArr15,$commonPtInfoArr16,$commonPtInfoArr17,$commonPtInfoArr18,$commonEncounter20,$commonEncounter21,$commonEncounter26);
			
			$cnsntFrmArr = array("{START APPLET ROW}"					=>"Multiple Applet in row : ",	"{TEXTBOX_LARGE}"		=>"Large Textbox : ",			
								 "{WITNESS SIGNATURE}"					=>"Witness Signature : ",		"{SITE}"				=>"Patient Site : ",				 "{PATIENT ALLERGIES}"					=>"Patient Allergies : ",	    "{OCULAR MEDICATION}"	=>"Ocular Medication : ",			 "{SYSTEMIC MEDICATION}"				=>"Systemic Medication :"	
							  );
			$cnsntFrmArr	= array_merge($cnsntFrmArr,$commonEncounter5,$commonEncounter14);
		} else if($formName == "consult_letter") {
							   
			$ptInfoArr	= array_merge($commonPtInfoArr1,$commonPtInfoArr2,$commonPtInfoArr3,$commonPtInfoArr4,$commonPtInfoArr5,$commonPtInfoArr6,$commonPtInfoArr7,$commonPtInfoArr9,$commonPtInfoArr10,$commonPtInfoArr13,$commonPtInfoArr14,$commonPtInfoArr15,$commonPtInfoArr16,$commonPtInfoArr18,$commonEncounter26);
							   
			$cnsltLtrArr = array( "{ADDRESSEE}"					=>"Addressee : ",
								 "{ADDRESSEE_ADDRESS}"			=>"Addressee Address : ",			"{CC1}"						=>"Cc1 : ",
								 "{CC1_ADDRESS}"				=>"Cc1 Address : ",					"{CC2}"						=>"Cc2 : ",
								 "{CC2_ADDRESS}"				=>"Cc2 Address : ",					"{CC3}"						=>"Cc3 : ",
								 "{CC3_ADDRESS}"				=>"Cc3 Address : ", 				"{L&A ALL}"			   	    =>"L&A ALL:",
								 "{SLE ALL}"			 		=>"SLE ALL :",					    "{FUNDUS ALL}"		   	    =>"FUNDUS ALL :",					 "{GENERAL HEALTH}"		   	    =>"GENERAL HEALTH :",					"{CO MANAGED PHY.}"		=>"CO MANAGED PHYSICIAN :"								
							  );
			$cnsltLtrArr	= array_merge($cnsltLtrArr,$commonEncounter1,$commonEncounter2,$commonEncounter3,$commonEncounter4,$commonEncounter8,$commonEncounter9,$commonEncounter11,$commonEncounter13,$commonEncounter17,$commonEncounter19,$commonEncounter20,$commonEncounter21,$commonEncounter22,$commonEncounter23,$commonEncounter27,$commonEncounter28);
		
			
			
		} else if($formName == "collection_letter") {
			
			$ptInfoArr = array("{FULL NAME}"				=>"Pt. Full Name : ",				"{SUFFIX}"			=>"Pt. Suffix : ",			
							 "{RES FULL NAME}"				=>"Res. Full Name : ",				"{RES SUFFIX}"		=>"Res. Suffix : "
								);
			$ptInfoArr	= array_merge($ptInfoArr,$commonPtInfoArr2,$commonPtInfoArr3,$commonPtInfoArr16);
			
			$collectLtrArr = array("{TOTAL OUTSTANDING CHARGES}"	=>"Total Outstanding Charges : ",	"{CHARGES}"		=>"Charges : ",
								 "{DOS & CHARGES}"					=>"DOS & Charges : "				
								);
			$collectLtrArr	= array_merge($collectLtrArr,$commonEncounter1,$commonEncounter2,$commonEncounter17,$commonEncounter21);
		} else if($formName == "recall_letter") {
			
			$ptInfoArr	= $commonPtInfoArr2;
			$ptInfoArr	= array_merge($commonPtInfoArr2,$commonPtInfoArr13,$commonPtInfoArr15);
			$recallLtrArr = array("{RECALL DESCRIPTION}"	=>"Recall Description : ",		"{RECALL PROCEDURE}" 		=>"Recall Procedure :",
								  "{LAST DOS}" 				=>"Last DOS :", 				"{APPT PROVIDER SIGNATURE}" =>"Appt Provider Signature:",
								  "​{PT-KEY}" 				=>"PT-KEY :"
								 );
			$recallLtrArr	= array_merge($recallLtrArr,$commonEncounter14,$commonEncounter16);
		} else if($formName == "op_notes") {
			
			$ptInfoArr	= array_merge($commonPtInfoArr1,$commonPtInfoArr2,$commonPtInfoArr3,$commonPtInfoArr4,$commonPtInfoArr5,$commonPtInfoArr6,$commonPtInfoArr7,$commonPtInfoArr9,$commonPtInfoArr10,$commonPtInfoArr13,$commonPtInfoArr14,$commonPtInfoArr15,$commonPtInfoArr16,$commonPtInfoArr18,$commonEncounter26);
			$opNotesArr = array("{DX_CODE}"	=> "DX CODE");
			$opNotesArr	= array_merge($opNotesArr,$commonEncounter1,$commonEncounter2,$commonEncounter3,$commonEncounter4,$commonEncounter8,$commonEncounter9,$commonEncounter11,$commonEncounter13,$commonEncounter17,$commonEncounter20,$commonEncounter21,$commonEncounter23);
		
		} else if($formName == "education" || $formName == "instruction") {
			
			$ptInfoArr	= array_merge($commonPtInfoArr2,$commonPtInfoArr4,$commonPtInfoArr5,$commonPtInfoArr6,$commonPtInfoArr7,$commonPtInfoArr9,$commonPtInfoArr10,$commonPtInfoArr14,$commonEncounter26);

			$educatArr	= array_merge($commonEncounter1,$commonEncounter2,$commonEncounter3,$commonEncounter4,$commonEncounter9,$commonEncounter11,$commonEncounter13,$commonEncounter17,$commonEncounter20,$commonEncounter21,$commonEncounter23,$commonEncounter25);
		
		} else if($formName == "prescription_contact_lens") {
			
			$ptInfoArr	= array_merge($commonPtInfoArr2,$commonPtInfoArr7,$commonPtInfoArr8,$commonPtInfoArr10);
			
			
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
								   "{WEAR SCHEDULER}"	=>"WEAR SCHEDULER : ",	"{DISINFECTING}" 	=>"DISINFECTING : "
								   	
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
			
			
			$presCntctLnsArr	= array_merge($presCntctLnsArr,$commonEncounter1,$commonEncounter5,$commonEncounter6,$commonEncounter7,$commonEncounter18,$commonEncounter24);
			
			
		} else if($formName == "prescription_glasses") {
			
			$ptInfoArr	= array_merge($commonPtInfoArr2,$commonPtInfoArr7,$commonPtInfoArr8,$commonPtInfoArr10,$commonEncounter18,$commonEncounter26);
			
			
			$presGlassesArr	= array("{OD PRISM}"			=>"OD PRISM : ",				"{OS PRISM}"			=>	"OS PRISM : ",
									"{OD HORIZONTAL PRISM}" =>"OD HORIZONTAL PRISM : ",		"{OS HORIZONTAL PRISM}" =>	"OS HORIZONTAL PRISM : ",
									"{OD VERTICAL PRISM}"	=>"OD VERTICAL PRISM : ",		"{OS VERTICAL PRISM}"	=>	"OS VERTICAL PRISM : ",
								);
			
			$presGlassesArr	= array_merge($presGlassesArr,$commonEncounter1,$commonEncounter5,$commonEncounter6,$commonEncounter7,$commonEncounter24);
			
			
		} else if($formName == "prescription_medical_rx") {
			
			$ptInfoArr	= array_merge($commonPtInfoArr2,$commonPtInfoArr7,$commonPtInfoArr8);
			$presMedRxArr	= array("{MEDICATION NAME}"			=>"Name of the Medication : ",		"{STRENGTH}"	=>"Strength : ",
								   "{QUANTITY}"					=>"Quantity : ",					"{DIRECTION}"	=>"Complete direction : ",	
								   "{SUBSITUTION}"				=>"SUBSITUTION : ",					"{REFILL}"		=>"Number associated with Refill : ",					
								   	
								);
			
			$presMedRxArr	= array_merge($presMedRxArr,$commonEncounter1,$commonEncounter5,$commonEncounter7);
		}		
		else if ($formName == "pt_docs_template"){
			
			$ptInfoArr	= array_merge($commonPtInfoArr1,$commonPtInfoArr2,$commonPtInfoArr3,$commonPtInfoArr4,$commonPtInfoArr5,$commonPtInfoArr6,$commonPtInfoArr7,$commonPtInfoArr10,$commonPtInfoArr11,$commonPtInfoArr12,$commonPtInfoArr13,$commonPtInfoArr14,$commonPtInfoArr15,$commonPtInfoArr16,$commonPtInfoArr18,$commonEncounter26);
			//$cnsntFrmArr = array_merge($cnsltLtrArr,$commonEncounter1,$commonEncounter2,$commonEncounter4,$commonEncounter10);
			$cnsntFrmArr = array_merge($commonEncounter4,$commonEncounter10,$commonEncounter11,$commonEncounter12,$commonEncounter13,$commonEncounter14,$commonEncounter15,$commonEncounter16,$commonEncounter17,$commonEncounter20,$commonEncounter22,$commonEncounter23,$commonEncounter24, array("{NO SHOW APPOINTMENT}"=>"No Show Appointment:"));
		}

		asort($ptInfoArr);
		asort($cnsntFrmArr);
		asort($cnsltLtrArr);
		asort($collectLtrArr);
		asort($recallLtrArr);
		asort($opNotesArr);
		asort($educatArr);
		asort($presCntctLnsArr);
		asort($presGlassesArr);
		asort($presMedRxArr);
		$allFormsArr = array($ptInfoArr,$cnsntFrmArr,$cnsltLtrArr,$collectLtrArr,$recallLtrArr,$opNotesArr,$educatArr,$presCntctLnsArr,$presGlassesArr,$presMedRxArr);
		return $allFormsArr;
	}
	
	function logged_in_facility_info($loggedinFacId){
		$loggedinFacInfo="";
		$qry = imw_query("SELECT name, street, city, state, postal_code, zip_ext, phone FROM `facility` WHERE id='".$loggedinFacId."'");
		if(imw_num_rows($qry)>0){
			$qryRes 		=  imw_fetch_assoc($qry);
			$facName		=  $qryRes['name'];
			$facStreet  	=  $qryRes['street'];
			$facCity 		=  $qryRes['city'];
			$facState		=  $qryRes['state'];
			$facPostal_code =  $qryRes['postal_code'];
			$facZip_ext 	=  $qryRes['zip_ext'];
			$facilityPhone  = trim($qryRes['phone']);
			if(strlen($facilityPhone) > 0){
			    $facilityPhone = "Ph:&nbsp; ".$facilityPhone;
			}else{
			    $facilityPhone = "";
			}
		}
		$loggedinFacInfo = array($facName,$facStreet,$facCity,$facState,$facPostal_code,$facZip_ext,$facilityPhone);
		return $loggedinFacInfo;
	}
	public function __getReffPhysicianDetails($refPhysicianId='', $read_from_database=0){
		$refPhysicianIdArr = array();
		$refPhyDataArr = array();
		if(trim($refPhysicianId) != ''){
			$refPhysicianIdArr = preg_split('/,/',$refPhysicianId);
			$refPhysicianIdArr=array_combine($refPhysicianIdArr,$refPhysicianIdArr);
		}

		$xml_file_name = realpath(dirname(__FILE__)."/../../xml/Referring_Physicians.xml");
		if(empty($xml_file_name) === true || $read_from_database==1){
			$qry = "select physician_Reffer_id as refphyId, Title as refPhyTitle,
						FirstName as refphyFName,LastName as refphyLName, MiddleName as refPhyMname,physician_fax,physician_fax as refFax,Address1 as refPhyAdd1,Address2 as refPhyAdd2,City as refPhyCity,State as refPhyState,ZipCode as refPhyZip, physician_phone as refPhone from refferphysician";
			if($refPhysicianId != ''){
				$qry .= " where physician_Reffer_id = '$refPhysicianId'";
			}
			$refPhyDataArr = get_array_records_query($qry);
		}
		else{
			$fileContent = file_get_contents($xml_file_name);
			$insFileData = new SimpleXMLElement($fileContent);
			//print'<pre>';print_r($insFileData);
			foreach($insFileData->refPhyInfo as $refPhysicianDataObj){
				$refPhysicianArr = (array)$refPhysicianDataObj;
				$refPhysicianDataArr = $refPhysicianArr['@attributes'];
				if(count($refPhysicianIdArr) > 0){					
					if(in_array($refPhysicianDataArr['refphyId'],$refPhysicianIdArr) === true){
						$refPhyDataArr[] = $refPhysicianDataArr; 
					}
				}else{
					$refPhyDataArr[] = $refPhysicianDataArr; 
				}
			}
		}
		return $refPhyDataArr;
	}

	public function __getPatientAge($ptDOB){
		$PtAge = "";
		if($ptDOB!="0000-00-00" && $ptDOB!="" && $ptDOB!=0) {
			$qry = "SELECT year( from_days( datediff( now() ,'".$ptDOB."'))) as PtYear, month( from_days( datediff( now() ,'".$ptDOB."')))-1 as PtMonth";
			$res = imw_query($qry) or die(imw_error());
			if(imw_num_rows($res)>0) {
				$row = imw_fetch_array($res);
				$PtYear = $row['PtYear'];
				$PtMonth = $row['PtMonth'];
				if($PtYear && $PtMonth) {$commaSpace = ",&nbsp;"; }
				if($PtYear) { $PtAge .= $PtYear."&nbsp;Yr"; }
				if($PtMonth) { $PtAge .= $commaSpace.$PtMonth."&nbsp;Mon."; }
				$PtAge = trim($PtAge);
			}
		}
		return $PtAge;
	}

	public function imageResize($width, $height, $target) {
		if ($width > $height) {
			$percentage = ($target / $width);
		} else {
			$percentage = ($target / $height);
		}
		$width = round($width * $percentage);
		$height = round($height * $percentage);
		return "width=\"$width\" height=\"$height\"";
	}

	public function __changeNameFormat($nameArr){
		$return_name = $nameArr['TITLE'];
		//$return_name .= $nameArr['LAST_NAME'].', ';
		$return_name .= $nameArr['LAST_NAME'];
		if(stristr($nameArr['LAST_NAME'],',')===false) {
			$return_name .= ', ';	
		}else {
			$return_name .= ' ';	
		}
		$return_name .= $nameArr['FIRST_NAME'].' ';
		if(trim($nameArr['MIDDLE_NAME']) != ''){
			$return_name .= substr(trim($nameArr['MIDDLE_NAME']),0,1).".";
		}
		$return_name = ucfirst(trim($return_name));
		if($return_name[0] == ','){
			$return_name = substr($return_name,1);
		}
		return $return_name;
	}

	//START
	public function __getApptInfo($patient_id,$providerIds=0,$report_start_date='',$report_end_date='',$appt_id=0,$newApptVars=0){
		/**
		 * GET PATIENT APPOINTMENT AND ASSOCIATED INFORMATION
		 */
		$appStrtDate = $appStrtTime = $doctorName = $facName = $procName = $andSchProvQry = $secProcName = $terProcName = "";
		$schDataQryRes=array();		
		if($providerIds) { $andSchProvQry = "AND sc.sa_doctor_id IN($providerIds)";}
		if($appt_id){ $andSchProvQry.= " AND sc.id='".$appt_id."'";}
		
		$ns_q = imw_query("select CONCAT(DATE_FORMAT(sa_app_start_date, '".get_sql_date_format()."'),' ',TIME_FORMAT(sa_app_starttime, '%h:%i %p')) as no_show from schedule_appointments 
		  WHERE sa_patient_app_status_id=3 and sa_patient_id =$patient_id order by sa_app_start_date DESC LIMIT 1") or die(imw_error());
		$ns_res=imw_fetch_assoc($ns_q);
		$no_show_appointment=$ns_res['no_show'];
		
		if(!empty($newApptVars) && ($newApptVars) > 0)
		{
			$schDataQry = "SELECT 
							DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."') as appStrtDate, 
							DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, 
							sc.procedure_site as appSite,
							DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
							sc.sa_patient_app_status_id as appStatus, 
							sc.case_type_id as casetypeid, 
							CONCAT_WS(', ', us.lname, us.fname) as doctorName, 
							us.lname as doctorLastName, 
							fac.name as facName,
							fac.street as facStreet,
							fac.city as facCity,
							fac.state as facState,
							fac.postal_code as facPostal_code,
							fac.zip_ext as faczip_ext, 
							fac.phone as facPhone,
							slp.proc as procName, 
							slp_sec.proc as secProcName, 
							slp_ter.proc as terProcName, 
							sc.sa_comments  
						FROM 
							schedule_appointments sc 
							LEFT JOIN users us ON us.id = sc.sa_doctor_id 
							LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
							LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
							LEFT JOIN slot_procedures slp_sec ON slp_sec.id = sc.sec_procedureid 
							LEFT JOIN slot_procedures slp_ter ON slp_ter.id = sc.tertiary_procedureid 
						WHERE 
							sc.sa_patient_id = '".$patient_id."'
						AND 
							sc.sa_app_start_date >= CURRENT_DATE( )
						AND 
							sc.sa_app_starttime >= CURRENT_TIME( )
						AND 
							sc.sa_patient_app_status_id NOT IN('18','203')
						ORDER BY 
							sc.sa_app_start_date, sc.sa_app_starttime ASC
						LIMIT 0,1";
			$schDataQryRes = get_array_records_query($schDataQry);
		}
		else
		{
			
			if($report_start_date || $report_end_date){
				$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
							sc.sa_patient_app_status_id as appStatus, sc.case_type_id as casetypeid, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.street as facStreet,fac.city as facCity,fac.state as facState,fac.postal_code as facPostal_code,fac.zip_ext as faczip_ext, fac.phone as facPhone,slp.proc as procName, sc.sa_comments  
							FROM schedule_appointments sc 
							LEFT JOIN users us ON us.id = sc.sa_doctor_id 
							LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
							LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
							WHERE sc.sa_patient_id = '".$patient_id."'
							AND sc.sa_app_start_date BETWEEN '".$report_start_date."' AND '".$report_end_date."'
							AND sc.sa_patient_app_status_id NOT IN('18','203')
							$andSchProvQry
							ORDER BY sc.sa_app_start_date DESC
							LIMIT 0,1";
				$schDataQryRes = get_array_records_query($schDataQry);
			}
			
			if(count($schDataQryRes)<=0) {
				$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
								sc.sa_patient_app_status_id as appStatus, sc.case_type_id as casetypeid, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.street as facStreet,fac.city as facCity,fac.state as facState,fac.postal_code as facPostal_code,fac.zip_ext as faczip_ext,fac.phone as facPhone,slp.proc as procName, sc.sa_comments  
								FROM schedule_appointments sc 
								LEFT JOIN users us ON us.id = if(sc.facility_type_provider!='0',sc.facility_type_provider,sc.sa_doctor_id)  
								LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
								LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
								WHERE sc.sa_patient_id = '".$patient_id."'
								AND sc.sa_app_start_date >= current_date()
								AND sc.sa_patient_app_status_id NOT IN('18','203')
								$andSchProvQry
								ORDER BY sc.sa_app_start_date ASC
								LIMIT 0,1";
				$schDataQryRes = get_array_records_query($schDataQry);
			}		
			if(count($schDataQryRes)<=0) {
				$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
								sc.sa_patient_app_status_id as appStatus, sc.case_type_id as casetypeid, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.street as facStreet,fac.city as facCity,fac.state as facState,fac.postal_code as facPostal_code,fac.zip_ext as faczip_ext ,fac.phone as facPhone,slp.proc as procName, sc.sa_comments  
								FROM schedule_appointments sc 
								LEFT JOIN users us ON us.id = if(sc.facility_type_provider!='0',sc.facility_type_provider,sc.sa_doctor_id)  
								LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
								LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
								WHERE sc.sa_patient_id = '".$patient_id."'
								AND sc.sa_app_start_date <= current_date() 
								AND sc.sa_patient_app_status_id NOT IN('18','203')
								$andSchProvQry
								ORDER BY sc.sa_app_start_date DESC
								LIMIT 0,1";
				$schDataQryRes = get_array_records_query($schDataQry);		
			}
			if(count($schDataQryRes)<=0) {
				$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
								sc.sa_patient_app_status_id as appStatus, sc.case_type_id as casetypeid, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.street as facStreet,fac.city as facCity,fac.state as facState,fac.postal_code as facPostal_code,fac.zip_ext as faczip_ext ,fac.phone as facPhone,slp.proc as procName, sc.sa_comments  
								FROM schedule_appointments sc 
								LEFT JOIN users us ON us.id = sc.sa_doctor_id 
								LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
								LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
								WHERE sc.sa_patient_id = '".$patient_id."'
								AND sc.sa_app_start_date <= current_date() 
								$andSchProvQry
								ORDER BY sc.sa_app_start_date DESC
								LIMIT 0,1";
				$schDataQryRes = get_array_records_query($schDataQry);		
			}
		}	
		if(count($schDataQryRes)>0) {
			for($i=0;$i<count($schDataQryRes);$i++){
				$appStrtDate 			= $schDataQryRes[$i]['appStrtDate'];
				$appStrtDate_FORMAT 	= $schDataQryRes[$i]['appStrtDate_FORMAT'];
				$facName 				= $schDataQryRes[$i]['facName'];
				$facStreet 				= $schDataQryRes[$i]['facStreet'];
				$facCity 				= $schDataQryRes[$i]['facCity'];
				$facState 				= $schDataQryRes[$i]['facState'];
				$facPostal_code			= $schDataQryRes[$i]['facPostal_code'];
				$faczip_ext				= $schDataQryRes[$i]['faczip_ext'];
				$facPhone 				= $schDataQryRes[$i]['facPhone'];
				$facPhoneFormat			= $facPhone;
				if(trim($facPhoneFormat)) {
					$facPhoneFormat = str_ireplace("-","",$facPhoneFormat);
					$facPhoneFormat = "(".substr($facPhoneFormat,0,3).") ".substr($facPhoneFormat,3,3)."-".substr($facPhoneFormat,6);
				}
				
				$priProcName 			= $schDataQryRes[$i]['procName'];
				if(!empty($newApptVars) && ($newApptVars) > 0)
				{	
					$secProcName 		= $schDataQryRes[$i]['secProcName'];
					$terProcName 		= $schDataQryRes[$i]['terProcName'];
				}
				$doctorName 			= $schDataQryRes[$i]['doctorName'];
				$doctorLastName 		= $schDataQryRes[$i]['doctorLastName'];
				
				$appSite 				= ucfirst($schDataQryRes[$i]['appSite']);
				$appSiteShow 			= $appSite;
				if($appSite == "Bilateral") {$appSiteShow="Both"; }
				
				$appStrtTime 			= $schDataQryRes[$i]['appStrtTime'];
				if($appStrtTime[0]=="0") { $appStrtTime = substr($appStrtTime, 1); }

				$appComments 			= $schDataQryRes[$i]['sa_comments'];
				$appComments 			= htmlentities($appComments);
				$appcasetypeid			= $schDataQryRes[$i]['casetypeid'];
			}
		}
		$appInfo = array($appStrtDate,$appStrtDate_FORMAT,$facName,$facPhoneFormat,$priProcName,$doctorName,$doctorLastName,$appSiteShow,$appStrtTime,$appComments,$facStreet,$facCity,$facState,$facPostal_code,$faczip_ext,$appcasetypeid,$secProcName,$terProcName,$no_show_appointment);
		return $appInfo;
	}
	//END

	public function __getInsuranceComDetails($patient_id, $ins_case_type='', $ins_provider_type='',$appt_id){
	
	//FROM NOW, INSURANCE VARIABLE WILL BE REPLACED WITH PATIENT APPOINTMENT ASSOCIATED INSURANCE INFORMATION	
	$insCaseTypeId = $insQueryCondition ='';
	if($appt_id){
		$appt_qry = "SELECT case_type_id as insCaseTypeId FROM `schedule_appointments` WHERE id='".$appt_id."' AND sa_patient_app_status_id NOT IN(203,201,18,19,20,3)";
		$appt_qry_res = imw_query($appt_qry);
		if(imw_num_rows($appt_qry_res)>0){
			$appt_qry_row = imw_fetch_assoc($appt_qry_res);
			$insCaseTypeId = $appt_qry_row['insCaseTypeId'];
			$insQueryCondition= "and insurance_data.ins_caseid = '$insCaseTypeId'";
		}
	}	
		//CODE COMMENTED TO STOP TO DISPLAY THE DEFAULT ACTIVE INSURANCE
		/*if(empty($ins_case_type) === true){
			$ins_case_type = (int)$_SESSION['new_casetype'];
		}
		if(empty($ins_case_type) === true){
			$caseTypeQry = "select case_id from insurance_case_types order by normal desc limit 0, 1";
			$caseTypeQryRes = get_array_records_query($caseTypeQry);
			$ins_case_type = $caseTypeQryRes[0]['case_id'];
		}*/
		
	$insDataQry = "select insurance_data.id, insurance_data.provider, insurance_companies.name as ins_name,
					insurance_companies.in_house_code,insurance_companies.phone,
					insurance_data.policy_number,
					insurance_data.group_number, insurance_data.subscriber_lname,
					insurance_data.subscriber_mname, insurance_data.subscriber_fname,
					insurance_data.subscriber_relationship, insurance_data.subscriber_ss,
					date_format(insurance_data.subscriber_DOB,'".get_sql_date_format()."') as subscriber_DOB,
					concat(insurance_data.subscriber_street,' ',insurance_data.subscriber_street_2) as ins_address,
					insurance_data.subscriber_postal_code, insurance_data.subscriber_city,
					insurance_data.scan_card, insurance_data.scan_label,insurance_data.scan_card2,
					insurance_data.subscriber_state, insurance_data.subscriber_country,
					insurance_data.subscriber_phone, insurance_data.subscriber_biz_phone,
					insurance_data.subscriber_mobile, insurance_data.copay, 
					insurance_data.subscriber_sex, insurance_data.type,
					insurance_data.subscriber_employer, insurance_case.ins_case_type, 
					CONCAT(insurance_companies.contact_address,if(TRIM(insurance_companies.city)!='',CONCAT(' ',insurance_companies.city),''),if(TRIM(insurance_companies.State)!='',CONCAT(', ',insurance_companies.State),''),if(TRIM(insurance_companies.Zip)!='',CONCAT(' ',insurance_companies.Zip),''),if(TRIM(insurance_companies.zip_ext)!='',CONCAT('-',insurance_companies.zip_ext),'')) AS ins_comp_address
					from insurance_data join insurance_case 
					on insurance_data.ins_caseid = insurance_case.ins_caseid and insurance_data.pid=insurance_case.patient_id
					join insurance_companies on insurance_companies.id = insurance_data.provider					 
					where insurance_case.case_status = 'open' and insurance_data.pid = '$patient_id' 
					$insQueryCondition
					and insurance_companies.ins_del_status = '0' and insurance_data.actInsComp = '1'";
					//and insurance_case.ins_case_type = '$ins_case_type'
		
		if(empty($ins_provider_type) === false){
			$insDataQry .= " and insurance_data.type in ($ins_provider_type)";
		}
		$insDataQryRes = get_array_records_query($insDataQry);
		$insDataDetailsArr = array();
		for($i=0;$i<count($insDataQryRes);$i++){
			$type = $insDataQryRes[$i]['type'];
			$insDataDetailsArr[$type] = $insDataQryRes[$i];
		}
		return $insDataDetailsArr;
	}

	public function newImageResize($imageUrl,$new_width,$new_height=''){
		//--- Get Image Size -----
		if(file_exists($imageUrl) && is_dir($imageUrl) == ''){
			$image_size = getimagesize($imageUrl);
			$image_width = $image_size[0];
			$image_height = $image_size[1];
		}
		if (($new_width!=0) && ($new_width<$image_width))
		{
			$image_height=(int)($image_height*($new_width/$image_width));
			$image_width=$new_width;
		}

		if (($new_height!=0) && ($new_height<$image_height))
		{
			$image_width=(int)($image_width*($new_height/$image_height));
			$image_height=$new_height;
		}
		return "width=\"$image_width\" height=\"$image_height\"";
	}

	function getPtReleaseInfoNames($ptid){
		$html = '';
		$q = "SELECT relInfoName1, relInfoPhone1, relInfoReletion1, relInfoName2, relInfoPhone2, relInfoReletion2, relInfoName3, relInfoPhone3, relInfoReletion3, relInfoName4, relInfoPhone4, relInfoReletion4 FROM patient_data WHERE pid='$ptid' LIMIT 0,1";
		$res= imw_query($q);
		if($res && imw_num_rows($res)>0){
			$rs = imw_fetch_assoc($res);
			$innerhtml = '';
			for($i=1; $i<5; $i++){
				if(trim($rs['relInfoName'.$i])!=''){
					$innerhtml .= '<tr><td>'.$rs['relInfoName'.$i].'</td><td>'.$rs['relInfoPhone'.$i].'</td><td>'.$rs['relInfoReletion'.$i].'</td></tr>';
				}
			}
			if($innerhtml != ''){
				$html = '<table width="100%">'.$innerhtml.'</table>';
			}
		}
		return $html;
	}
	
	public function __getLatestDosFormId($ptId){
		$formId = "";
		$qry = "SELECT c1.id as formId FROM chart_master_table c1,chart_left_cc_history c2
				WHERE c1.id=c2.form_id AND c1.patient_id = '".$ptId."'
				ORDER BY c1.date_of_service DESC LIMIT 0,1
				";
	
		$res = imw_query($qry) or die(imw_error());
		if(imw_num_rows($res)>0) {
			$row = imw_fetch_array($res);
			$formId = $row['formId'];
		}
		return $formId;
	}

	public function __getApptFuture($patient_id,$report_start_date="",$report_end_date="",$time_inc=""){
		$appFu = '';
		$dateRange=" AND sc.sa_app_start_date > current_date() ";
		if($report_end_date){
			$dateRange=" AND sc.sa_app_start_date >='".$report_end_date."' ";	
		}
		$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'%m-%d-%Y') as appStrtDate,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
						sc.sa_patient_app_status_id as appStatus, CONCAT_WS(', ', us.lname, us.fname) as doctorName,fac.name as facName,slp.proc as procName 
						FROM schedule_appointments sc 
						LEFT JOIN users us ON us.id = sc.sa_doctor_id 
						LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
						LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
						WHERE sc.sa_patient_id = '".$patient_id."'
						AND sc.sa_patient_app_status_id != '18'
						$dateRange
						ORDER BY sc.sa_app_start_date ASC
						";
		
		$schDataQryRes = get_array_records_query($schDataQry);
		$schDataDetailsArr = array();
		if(count($schDataQryRes)>0) {
			$appStatusArr = array(18=>"Cancel",13=>"Check-in",11=>"Check-out",2=>"Chart Pulled",202=>"Reschedule",6=>"Left Without Visit",3=>"No-Show",
									201=>"To-Do",0=>"New",200=>"Room # assignment",7=>"Insurance/Financial Issue");
			
			$appStatus='';
			foreach($appStatusArr as $key=>$val) {
				if($key==$schDataQryRes[$i]['appStatus']) {
					$appStatus = $val;	
				}
			}
			$appFu.='<table  border="0" cellpadding="2" cellspacing="2" width="100%">
						<tr align="left">
							<td class="text_10b" valign="top" style="width:10%; height:20px;"><strong>Date</strong></td>';
			if($time_inc==''){
				$appFu.='	<td class="text_10b" valign="top" style="width:10%; height:20px;"><strong>Time</strong></td>';
			}
				$appFu.='	<td class="text_10b" valign="top" style="width:20%; padding-left:5px;height:20px;"><strong>Doctor</strong></td>
							<td class="text_10b" valign="top" style="width:30%; padding-left:5px; height:20px;"><strong>Facility</strong></td>
							<td class="text_10b" valign="top" style="width:20%; height:20px;"><strong>Procedure</strong></td>';
			if($time_inc==''){				
				$appFu.='	<td class="text_10b" valign="top" style="width:10%;"><strong>Status</strong></td>';
			}
				$appFu.='</tr>';
				
			for($i=0;$i<count($schDataQryRes);$i++){
				$appFu.='
						<tr align="left">
							<td class="text_10" valign="top" style="width:10%; height:20px;">'.$schDataQryRes[$i]['appStrtDate'].'</td>';
				if($time_inc==''){			
					$appFu.='<td class="text_10" valign="top" style="width:10%; height:20px;">'.$schDataQryRes[$i]['appStrtTime'].'</td>';
				}
				$appFu.='	<td class="text_10" valign="top" style="width:20%;padding-left:5px;height:20px;">'.$schDataQryRes[$i]['doctorName'].'</td>
							<td class="text_10" valign="top" style="width:30%;  height:20px;padding-left:5px;">'.$schDataQryRes[$i]['facName'].'</td>
							<td class="text_10" valign="top" style="width:20%;  height:20px;">'.$schDataQryRes[$i]['procName'].'</td>';
				if($time_inc==''){
					$appFu.='	<td class="text_10" valign="top" style="width:10%; height:20px;">'.$appStatus.'</td>';
				}
					$appFu.='</tr>';
			}
			$appFu.='</table>';
		}else{$appFu.='&nbsp;&nbsp;No appointments';}
		return $appFu;
	}

	public function __getApptHx($patient_id,$report_start_date,$report_end_date){
		$appHx = '';
		$dateRange=" AND sc.sa_app_start_date <= current_date() ";
		if($report_end_date){
			$dateRange=" AND sc.sa_app_start_date <='".$report_end_date."' ";	
		}
		$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'%m-%d-%Y') as appStrtDate,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
						IF(sc.sa_patient_app_status_id = 0, 'New', sst.status_name) as appStatus, CONCAT_WS(', ', us.lname, us.fname) as doctorName,fac.name as facName,slp.proc as procName 
						FROM schedule_appointments sc 
						LEFT JOIN users us ON us.id = sc.sa_doctor_id 
						LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
						LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
						LEFT JOIN schedule_status sst ON sst.id = sc.sa_patient_app_status_id
						WHERE sc.sa_patient_id = '".$patient_id."'
						AND sc.sa_patient_app_status_id != '18'
						$dateRange
						ORDER BY sc.sa_app_start_date DESC
						LIMIT 0,3";

		$schDataQryRes = get_array_records_query($schDataQry);
		$schDataDetailsArr = array();
		if(count($schDataQryRes)>0) {
			
			/*
			// Update with schedule status table in version R8
			$appStatusArr = array(18=>"Cancel",13=>"Check-in",11=>"Check-out",2=>"Chart Pulled",202=>"Reschedule",6=>"Left Without Visit",3=>"No-Show",
									201=>"To-Do",0=>"New",200=>"Room # assignment",7=>"Insurance/Financial Issue");
			$appStatus='';
			foreach($appStatusArr as $key=>$val) {
				if($key==$schDataQryRes[$i]['appStatus']) {
					$appStatus = $val;	
				}
			}
			*/
			$appHx.='<table  border="0" cellpadding="5" cellspacing="5" style="width:650px;">
						<tr align="left">
							<td class="text_10b" valign="top" style="width:80px;"><strong>Date</strong></td>
							<td class="text_10b" valign="top" style="width:80px;"><strong>Time</strong></td>
							<td class="text_10b" valign="top" style="width:120px;"><strong>Doctor</strong></td>
							<td class="text_10b" valign="top" style="width:130px;"><strong>Facility</strong></td>
							<td class="text_10b" valign="top" style="width:140px;"><strong>Procedure</strong></td>
							<td class="text_10b" valign="top" style="width:100px;"><strong>Status</strong></td>
						</tr>
					';
			for($i=0;$i<count($schDataQryRes);$i++){
				$appHx.='
						<tr align="left">
							<td class="text_10" valign="top">'.$schDataQryRes[$i]['appStrtDate'].'</td>
							<td class="text_10" valign="top">'.$schDataQryRes[$i]['appStrtTime'].'</td>
							<td class="text_10" valign="top">'.$schDataQryRes[$i]['doctorName'].'</td>
							<td class="text_10" valign="top">'.$schDataQryRes[$i]['facName'].'</td>
							<td class="text_10" valign="top">'.$schDataQryRes[$i]['procName'].'</td>
							<td class="text_10" valign="top">'.$schDataQryRes[$i]['appStatus'].'</td>
						</tr>';
			}
			$appHx.='</table>';
		}
		return $appHx;
	}
	
	public function __getVision($ptId, $formId){
		$VCCOD = $VCCOS = "";
		$sql = "
			SELECT sec_name, sec_indx, c1.status_elements, ex_desc, 
				sel_od, sel_os, txt_od, txt_os, sel_ou, txt_ou
			FROM `chart_vis_master` c1
			LEFT JOIN chart_acuity c2 ON c1.id = c2.id_chart_vis_master
			WHERE c1.form_id = '".$formId."' AND c1.patient_id = '".$ptId."'
			AND (sec_name = 'Distance' OR sec_name = 'Near' )
			AND sec_indx IN (1,2)
		";
		
		$rez = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($rez);$i++){
			$statusElements = $row["status_elements"];
			$flg_acuity=1;
			$j = $row["sec_indx"];
			if($row["sec_name"] == "Distance"){				
				${"disOdSel" . $j} = $row["sel_od"];
				${"disOsSel" . $j} = $row["sel_os"];
				${"disOdTxt" . $j} = $row["txt_od"];
				${"disOsTxt" . $j} = $row["txt_os"];	
				${"disOuSel" . $j} = $row["sel_ou"];
				${"disOuTxt" . $j} = $row["txt_ou"];
				$vision_comments=$res["ex_desc"];	
			}else if($row["sec_name"] == "Near" && $row["sec_indx"]=="1"){				
				$NearOdSel = $row["sel_od"];
				$NearOsSel = $row["sel_os"];
				$NearOdTxt = $row["txt_od"];
				$NearOsTxt = $row["txt_os"];				
			}
		}
		
		if(!empty($flg_acuity)){			
			$nearOdSel=$nearOdTxt=$nearOsSel=$nearOsTxt=$near_os=$near_od=$dis_od=$dis_os;
			if(trim($disOdSel1)=="SC"){
				$dis_od=$disOdSel1."&nbsp;".$disOdTxt1;
			}
			
			if(trim($disOsSel1)=="SC"){
				$dis_os=$disOsSel1."&nbsp;".$disOsTxt1;
			}
			
			
			if(trim($nearOdSel)=="SC"){
				$near_os=$nearOdSel."&nbsp;".$nearOdTxt;
			}
			
			if(trim($nearOsSel)=="SC"){	
				$near_od=$nearOsSel."&nbsp;".$nearOsTxt;
			}			
			
			//Check for background
			if( (empty($disOdSel1) || (strpos($statusElements, "elem_visDisOdSel1=0") !== false)) &&
			    (empty($disOsSel1) || (strpos($statusElements, "elem_visDisOsSel1=0") !== false)) &&  
			    (empty($disOdTxt1) || ($disOdTxt1 == "20/") || (strpos($statusElements, "elem_visDisOdTxt1=0") !== false)) &&  
			    (empty($disOsTxt1) || ($disOsTxt1 == "20/") || (strpos($statusElements, "elem_visDisOsTxt1=0") !== false)) &&  
			    (empty($disOdSel2) || (strpos($statusElements, "elem_visDisOdSel2=0") !== false)) &&
			    (empty($disOsSel2) || (strpos($statusElements, "elem_visDisOsSel2=0") !== false)) &&  
			    (empty($disOdTxt2) || ($disOdTxt2 == "20/") || (strpos($statusElements, "elem_visDisOdTxt2=0") !== false)) &&  
			    (empty($disOsTxt2) || ($disOsTxt2 == "20/") || (strpos($statusElements, "elem_visDisOsTxt2=0") !== false))
			  ){
				// Not Performed
			}else{
				$disOD = (!empty($disOdSel1) || (!empty($disOdTxt1) && ($disOdTxt1 != "20/")) || !empty($disOdSel2) || (!empty($disOdTxt2) && ($disOdTxt2 != "20/")) ) ? "<br>&nbsp;&nbsp;OD&nbsp;&nbsp;".$disOdSel1." - ".$disOdTxt1."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$disOdSel2." - ".$disOdTxt2 : "";//"Not Performed";
				$disOS = (!empty($disOsSel1) || (!empty($disOsTxt1) && ($disOsTxt1 != "20/")) || !empty($disOsSel2) || (!empty($disOsTxt2) && ($disOsTxt2 != "20/")) ) ? "<br>&nbsp;&nbsp;OS&nbsp;&nbsp;".$disOsSel1." - ".$disOsTxt1."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$disOsSel2." - ".$disOsTxt2 : "";//"Not Performed";
				$disc="";if($vision_comments){$disc="<br>&nbsp;&nbsp;".$vision_comments;}
				//$VCCOD = (!empty($disOdSel1) || (!empty($disOdTxt1) && ($disOdTxt1 != "20/")) || !empty($disOdSel2) || (!empty($disOdTxt2) && ($disOdTxt2 != "20/")) ) ? $disOdSel1." - ".$disOdTxt1."<br/>".$disOdSel2." - ".$disOdTxt2 : "";
				$VCCOD = (!empty($disOdSel1) || (!empty($disOdTxt1) && ($disOdTxt1 != "20/"))) ? "&nbsp;OD&nbsp;".$disOdSel1." - ".$disOdTxt1 : "";
				$VCCOD_Br_Tag = (!empty($VCCOD)) ? "&nbsp;&nbsp;" : "";
				$VCCOD .= (!empty($disOdSel2) || (!empty($disOdTxt2) && ($disOdTxt2 != "20/"))) ? $VCCOD_Br_Tag.$disOdSel2." - ".$disOdTxt2 : "";
				$VCCOS = (!empty($disOsSel1) || (!empty($disOsTxt1) && ($disOsTxt1 != "20/"))) ? "&nbsp;OS&nbsp;".$disOsSel1." - ".$disOsTxt1 : "";
				$VCCOS_Br_Tag = (!empty($VCCOS)) ? "&nbsp;&nbsp;" : "";
				$VCCOS .= (!empty($disOsSel2) || (!empty($disOsTxt2) && ($disOsTxt2 != "20/")) ) ? $VCCOS_Br_Tag.$disOsSel2." - ".$disOsTxt2 : "";
				//$VCCOD .=(!empty($vision_comments))?"<br>".$vision_comments:"";
				//Replace Array
				
			}
		}
		
		//
		$sql = "
			SELECT
			
			c2.txt_2 as txt_2_r,  c3.txt_2 as txt_2_l
			
			FROM chart_vis_master c0
			LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'
			
			WHERE c1.ex_type='MR' AND c1.ex_number IN (1) AND c0.form_id = '".$formId."' AND c0.patient_id = '".$ptId."'
			ORDER BY c1.ex_number
		";
		$row = sqlQuery($sql);
		if($row!=false){
			$cylOdTxt=($res["txt_2_r"]!="20/")?$res["txt_2_r"]:"";
			$cylOsTxt=($res["txt_2_l"]!="20/")?$res["txt_2_l"]:"";
		}
		
		$arrRet = array($disOD.$disOS.$disc,$VCCOD,$VCCOS,$near_os,$near_od,$cylOdTxt,$cylOsTxt,$dis_od,$dis_os);
		return $arrRet;
	}

	public function __getmr1NbcvaOdOs($ptId, $formId){
		$sql = "
			SELECT			
			c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.txt_1 as txt_1_r, 
			c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.txt_1 as txt_1_l
			FROM chart_vis_master c0
			LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'			
			WHERE c1.ex_type='MR' AND c1.ex_number IN (1) AND c0.form_id = '".$formId."' AND c0.patient_id = '".$ptId."'
			ORDER BY c1.ex_number
		";
		$row = sqlQuery($sql);
		
		if($row!=false){
			
			if(!empty($row["sph_r"])){
				$vis_mr_od_s = "&nbsp;S&nbsp;".$row["sph_r"];
			}
			if(!empty($row["cyl_r"])){
				$vis_mr_od_c = "&nbsp;C&nbsp;".$row["cyl_r"];
			}
			if(!empty($row["axs_r"])){
				$vis_mr_od_a = "&nbsp;A&nbsp;".$row["axs_r"];
			}
			if(!empty($row["sph_l"])){
				$vis_mr_os_s = "&nbsp;S&nbsp;".$row["axs_r"];
			}
			if(!empty($row["cyl_l"])){
				$vis_mr_os_c = "&nbsp;C&nbsp;".$row["cyl_l"];
			}
			if(!empty($row["axs_l"])){
				$vis_mr_os_a = "&nbsp;A&nbsp;".$row["axs_l"];
			}
		
			 $vis_mr_od_vals = $vis_mr_od_s.$vis_mr_od_c.$vis_mr_od_a;
			 $vis_mr_os_vals = $vis_mr_os_s.$vis_mr_os_c.$vis_mr_os_a;
		
			$BcvaOdTxt1=($row["txt_1_r"]!="20/")?$row["txt_1_r"]:"";
			$BcvaOsTxt1=($row["txt_1_l"]!="20/")?$row["txt_1_l"]:"";
	
			$arrRet = array($vis_mr_od_vals,$vis_mr_os_vals,$BcvaOdTxt1,$BcvaOsTxt1);
		}
		return $arrRet;
	}

	public function __getChartLeftCcHx($ptId, $formId){
		$dominantShow = "";
		$qry = "SELECT c2.dominant FROM chart_master_table c1
				 LEFT JOIN chart_left_cc_history c2 ON c2.form_id = c1.id 
				 WHERE c1.id='".$formId."' AND c1.patient_id ='".$ptId."' ";
		$res = imw_query($qry) or die(imw_error());
		if(imw_num_rows($res)>0) {
			$row = imw_fetch_array($res);
			$dominant = strtoupper($row['dominant']);
			if($dominant=="OD") { $dominantShow = "Right"; 
			}elseif($dominant=="OS") { $dominantShow = "Left"; 
			}elseif($dominant=="OU") { $dominantShow = "Both"; 
			}
		}
		$chartArr = array($dominantShow);
		return $chartArr;
	}

	public function __getGlareInfo($ptId, $formId){
		$glareVal = $glare_K_OD = $glare_K_OS = $operativeEyeVal = $unOperativeEyeVal = $assementVal = $lriVal = $lenSelectionVal = "";

		if($ptId && $formId) {
			$sqlGlare = "SELECT st.*,
						knaOD.kheadingName AS autoSelectODName ,kniOD.kheadingName AS iolMasterSelectODName,kntOD.kheadingName AS topographerSelectODName, 
						knaOS.kheadingName AS autoSelectOSName ,kniOS.kheadingName AS iolMasterSelectOSName,kntOS.kheadingName AS topographerSelectOSName,
						litOD.lenses_iol_type AS lens_selectionOD, litOS.lenses_iol_type AS lens_selectionOS 
						
						FROM surgical_tbl st 
						LEFT JOIN kheadingnames knaOD ON knaOD.kheadingId = st.autoSelectOD	 
						LEFT JOIN kheadingnames kniOD ON kniOD.kheadingId = st.iolMasterSelectOD 	
						LEFT JOIN kheadingnames kntOD ON kntOD.kheadingId = st.topographerSelectOD	 
						LEFT JOIN kheadingnames knaOS ON knaOS.kheadingId = st.autoSelectOS	 
						LEFT JOIN kheadingnames kniOS ON kniOS.kheadingId = st.iolMasterSelectOS 	
						LEFT JOIN kheadingnames kntOS ON kntOS.kheadingId = st.topographerSelectOS 	
						LEFT JOIN lenses_iol_type litOD ON litOD.iol_type_id = st.selecedIOLsOD 	
						LEFT JOIN lenses_iol_type litOS ON litOS.iol_type_id = st.selecedIOLsOS 	
						WHERE form_id = '".$formId."' AND patient_id = '".$ptId."'";
			$resGlare = imw_query($sqlGlare) or die(imw_error());
			
			if(imw_num_rows($resGlare)>0) {
				$rowGlare = imw_fetch_array($resGlare);
				
				//start get lens selection
				$lens_selectionOD = trim(stripslashes($rowGlare["lens_selectionOD"]));
				$lens_selectionOS = trim(stripslashes($rowGlare["lens_selectionOS"]));
				$lenSelectionVal .= '<table cellpadding="0" border="0" cellspacing="0"><tr>';			
				if($lens_selectionOD) {$lenSelectionVal .= '<td style="vertical-align:top; padding-right:20px;"><b>OD&nbsp;:&nbsp;</b>'.$lens_selectionOD.'</td>';}
				if($lens_selectionOS) {$lenSelectionVal .= '<td style="vertical-align:top;"><b>OS&nbsp;:&nbsp;</b>'.$lens_selectionOS.'</td>';}
				$lenSelectionVal .= '</tr></table>';	
				//end get lens selection
				
				//start get operative eye
				$selecedIOLsOD = $rowGlare["selecedIOLsOD"];
				$selecedIOLsOS = $rowGlare["selecedIOLsOS"];
				$operativeEyeVal .= '<table cellpadding="0" border="0" cellspacing="0"><tr>';			
				if($selecedIOLsOD) {$operativeEyeVal .= '<td style="vertical-align:top;width:50px; font-size:24px;"><b>LT </b></td>';}
				if($selecedIOLsOS) {$operativeEyeVal .= '<td style="vertical-align:top;width:50px; font-size:24px;"><b>RT </b></td>';}
				$operativeEyeVal .= '</tr></table>';	
				//end get operative eye
				
				//start get unoperative eye
				if($selecedIOLsOD || $selecedIOLsOS) {
					$unOperativeEyeVal .= '<table cellpadding="0" border="0" cellspacing="0"><tr>';			
					if($selecedIOLsOD) {//opposite of operative eye		
						$unOperativeEyeVal .= '<td style="vertical-align:top;width:50px; font-size:24px;"><b>RT </b></td>';
					}else if($selecedIOLsOS) {	
						$unOperativeEyeVal .= '<td style="vertical-align:top;width:50px; font-size:24px;"><b>LT </b></td>';
					}
					$unOperativeEyeVal .= '</tr></table>';	
				}
				//end get unoperative eye
				
				//start get LRI
				$lriODArr = $lriOSArr = array();
				$lriOD 				= $rowGlare["lriOD"];
				$dlOD 				= $rowGlare["dlOD"];
				$synechiolysisOD	= $rowGlare["synechiolysisOD"];
				$irishooksOD 		= $rowGlare["irishooksOD"];
				$trypanblueOD 		= $rowGlare["trypanblueOD"];
				$flomaxOD 			= $rowGlare["flomaxOD"];
				$cutsOD 			= $rowGlare["cutsOD"];
				$lengthOD 			= $rowGlare["lengthOD"];
				$lengthTypeOD 		= $rowGlare["lengthTypeOD"];
				$axisOD 			= $rowGlare["axisOD"];
				$superiorOD 		= $rowGlare["superiorOD"];
				$inferiorOD 		= $rowGlare["inferiorOD"];
				$nasalOD 			= $rowGlare["nasalOD"];
				$temporalOD 		= $rowGlare["temporalOD"];
				$STOD 				= $rowGlare["STOD"];
				$SNOD 				= $rowGlare["SNOD"];
				$ITOD 				= $rowGlare["ITOD"];
				$INOD 				= $rowGlare["INOD"];
				$opts_od 			= $rowGlare["opts_od"];
				$opts_od_other 		= $rowGlare["opts_od_other"];
				
				$lriOSArr = $lriOSArr = array();
				$lriOS 				= $rowGlare["lriOS"];
				$dlOS 				= $rowGlare["dlOS"];
				$synechiolysisOS	= $rowGlare["synechiolysisOS"];
				$irishooksOS 		= $rowGlare["irishooksOS"];
				$trypanblueOS 		= $rowGlare["trypanblueOS"];
				$flomaxOS 			= $rowGlare["flomaxOS"];
				$cutsOS 			= $rowGlare["cutsOS"];
				$lengthOS 			= $rowGlare["lengthOS"];
				$lengthTypeOS 		= $rowGlare["lengthTypeOS"];
				$axisOS 			= $rowGlare["axisOS"];
				$superiorOS 		= $rowGlare["superiorOS"];
				$inferiorOS 		= $rowGlare["inferiorOS"];
				$nasalOS 			= $rowGlare["nasalOS"];
				$temporalOS 		= $rowGlare["temporalOS"];
				$STOS 				= $rowGlare["STOS"];
				$SNOS 				= $rowGlare["SNOS"];
				$ITOS 				= $rowGlare["ITOS"];
				$INOS 				= $rowGlare["INOS"];
				$opts_os 			= $rowGlare["opts_os"];
				$opts_os_other 		= $rowGlare["opts_os_other"];
				
				
				if($cutsOD) 		{ $lriODArr[] = "<strong>Cuts:</strong> ".$cutsOD;						}
				if($lengthOD) 		{ $lriODArr[] = "<strong>Length:</strong> ".$lengthOD.$lengthTypeOD;	}
				if($axisOD) 		{ $lriODArr[] = "<strong>Axis:</strong> ".$axisOD;						}

				$lriODKeyArr = array("LRI" => $lriOD,"DL" => $dlOD, "Synechiolysis" => $synechiolysisOD, "IRIS Hooks" => $irishooksOD, 
									 "Trypan Blue" => $trypanblueOD, "Pt. On Flomax" => $flomaxOD,"opts_od" => $opts_od, 
									 "opts_od_other" => $opts_od_other,"Superior" => $superiorOD, "Inferior" => $inferiorOD, 
									 "Nasal" => $nasalOD, "Temporal" => $temporalOD,"ST" => $STOD, "SN" => $SNOD, "IT" => $ITOD,
									 "IN" => $INOD
									 );
				foreach($lriODKeyArr as $keyOD => $valOD) {
					
					if($valOD=="1") 													{ $lriODArr[] = $keyOD; }	
					if($keyOD=="opts_od" && trim($valOD)) 								{ $lriODArr[] = $valOD; }	
					if($keyOD=="opts_od_other" && trim($valOD) && trim($valOD)!="Other"){ $lriODArr[] = $valOD; }	
				}
				if($opts_od)
				
				if($cutsOS) 		{ $lriOSArr[] = "<strong>Cuts:</strong> ".$cutsOS;						}
				if($lengthOS) 		{ $lriOSArr[] = "<strong>Length:</strong> ".$lengthOS.$lengthTypeOS;	}
				if($axisOS) 		{ $lriOSArr[] = "<strong>Axis:</strong> ".$axisOS;						}
				$lriOSKeyArr = array("LRI" => $lriOS,"DL" => $dlOS, "Synechiolysis" => $synechiolysisOS, "IRIS Hooks" => $irishooksOS, 
									 "Trypan Blue" => $trypanblueOS, "Pt. On Flomax" => $flomaxOS, "opts_os" => $opts_os, 
									 "opts_os_other" => $opts_os_other, "Superior" => $superiorOS, "Inferior" => $inferiorOS, 
									 "Nasal" => $nasalOS, "Temporal" => $temporalOS,"ST" => $STOS, "SN" => $SNOS, "IT" => $ITOS,
									 "IN" => $INOS
									 );
				foreach($lriOSKeyArr as $keyOS => $valOS) {
					if($valOS=="1") 													{ $lriOSArr[] = $keyOS; }
					if($keyOS=="opts_os" && $valOS) 									{ $lriOSArr[] = $valOS; }
					if($keyOS=="opts_os_other" && trim($valOS) && trim($valOS)!="Other"){ $lriOSArr[] = $valOS; }	
				}
				
				$lriVal	.= '<table cellpadding="0" border="0" cellspacing="0">';
				if($lriODArr) { 
					$lriVal	.='	<tr>
									<td style=" width:10px;vertical-align:top;"><b>OD&nbsp;: </b></td>'; 
					$lriVal	.='		<td style="padding-right:5px;">'.implode(", ",$lriODArr).'</td>
							   	</tr>'; 
				}
				if($lriOSArr) { 
					$lriVal	.='	<tr>
									<td style=" width:10px;vertical-align:top;"><b>OS&nbsp;: </b></td>'; 
					$lriVal	.='		<td style="padding-right:5px;">'.implode(", ",$lriOSArr).'</td>
							   	</tr>'; 
				}
				
				$lriVal .= '</table>';
				//end get LRI
				
				//start get OCULAR PATHOLOGY
				$cataractOD 	= $rowGlare["cataractOD"];
				$astigmatismOD 	= $rowGlare["astigmatismOD"];
				$myopiaOD 		= $rowGlare["myopiaOD"];
				$cataractOS 	= $rowGlare["cataractOS"];
				$astigmatismOS 	= $rowGlare["astigmatismOS"];
				$myopiaOS 		= $rowGlare["myopiaOS"];
				$AssementODArr = $AssementOSArr = array();
				if($cataractOD==1) 		{ $AssementODArr[] = "Cataract";	}
				if($astigmatismOD==1) 	{ $AssementODArr[] = "Astigmatism";	}
				if($myopiaOD==1) 		{ $AssementODArr[] = "Myopia";		}
				if($cataractOS==1) 		{ $AssementOSArr[] = "Cataract";	}
				if($astigmatismOS==1) 	{ $AssementOSArr[] = "Astigmatism";	}
				if($myopiaOS==1) 		{ $AssementOSArr[] = "Myopia";		}
				$assementVal	.= '<table cellpadding="0" border="0" cellspacing="0"><tr>';
				if($AssementODArr) { $assementVal	.='<td style="padding-right:30px;vertical-align:top;"><b>OD&nbsp;: </b>'.implode(", ",$AssementODArr).'</td>'; }
				if($AssementOSArr) { $assementVal	.='<td style="padding-left:10px;vertical-align:top;"><b>OS&nbsp;: </b>'.implode(", ",$AssementOSArr).'</td>'; }
				$assementVal 	.= '</tr></table>';
				//end get OCULAR PATHOLOGY
				
				$glareOD = $rowGlare["glareOD"];
				$glareOS = $rowGlare["glareOS"];
				$glareVal .= '<table cellpadding="0" border="0" cellspacing="0">';			
				if($glareOD || $glareOS) {
					$glareVal .= '<tr>
									<td style="vertical-align:top;"><b>OD : </b></td>
									<td style="vertical-align:top;">'.$glareOD.'</td>				
								 </tr>
								 <tr>
									<td style="vertical-align:top;"><b>OS : </b></td>
									<td style="vertical-align:top;">'.$glareOS.'</td>				
								 </tr>
								 ';			
				}
				$glareVal .= '</table>';	
				
				
				$autoSelectODName 		= $rowGlare["autoSelectODName"]; 
				$iolMasterSelectODName 	= $rowGlare["iolMasterSelectODName"]; 
				$topographerSelectODName= $rowGlare["topographerSelectODName"]; 			
				$k1Auto1OD 				= $rowGlare["k1Auto1OD"]; 
				$k1Auto2OD 				= $rowGlare["k1Auto2OD"]; 
				$k1IolMaster1OD 		= $rowGlare["k1IolMaster1OD"]; 
				$k1IolMaster2OD 		= $rowGlare["k1IolMaster2OD"]; 
				$k1Topographer1OD 		= $rowGlare["k1Topographer1OD"]; 
				$k1Topographer2OD 		= $rowGlare["k1Topographer2OD"]; 
				$k2Auto1OD 				= $rowGlare["k2Auto1OD"]; 
				$k2Auto2OD 				= $rowGlare["k2Auto2OD"]; 
				$k2IolMaster1OD 		= $rowGlare["k2IolMaster1OD"]; 
				$k2IolMaster2OD 		= $rowGlare["k2IolMaster2OD"]; 
				$k2Topographer1OD 		= $rowGlare["k2Topographer1OD"]; 
				$k2Topographer2OD 		= $rowGlare["k2Topographer2OD"]; 		
			
				$autoSelectOSName 		= $rowGlare["autoSelectOSName"]; 
				$iolMasterSelectOSName 	= $rowGlare["iolMasterSelectOSName"]; 
				$topographerSelectOSName= $rowGlare["topographerSelectOSName"]; 			
				$k1Auto1OS 				= $rowGlare["k1Auto1OS"]; 
				$k1Auto2OS 				= $rowGlare["k1Auto2OS"]; 
				$k1IolMaster1OS 		= $rowGlare["k1IolMaster1OS"]; 
				$k1IolMaster2OS 		= $rowGlare["k1IolMaster2OS"]; 
				$k1Topographer1OS 		= $rowGlare["k1Topographer1OS"]; 
				$k1Topographer2OS 		= $rowGlare["k1Topographer2OS"]; 
				$k2Auto1OS 				= $rowGlare["k2Auto1OS"]; 
				$k2Auto2OS 				= $rowGlare["k2Auto2OS"]; 
				$k2IolMaster1OS 		= $rowGlare["k2IolMaster1OS"]; 
				$k2IolMaster2OS 		= $rowGlare["k2IolMaster2OS"]; 
				$k2Topographer1OS 		= $rowGlare["k2Topographer1OS"]; 
				$k2Topographer2OS 		= $rowGlare["k2Topographer2OS"]; 		
	
				if($k1Auto1OD || $k1Auto2OD || $k1IolMaster1OD || $k1IolMaster2OD || $k1Topographer1OD || $k1Topographer2OD || $k2Auto1OD || $k2Auto2OD || $k2IolMaster1OD || $k2IolMaster2OD || $k2Topographer1OD || $k2Topographer2OD) {
					$glare_K_OD .= '<table cellpadding="0" border="0"  cellspacing="0" style="width:100%;border-top:1px solid #CCC;border-left:1px solid #CCC;border-right:1px solid #CCC;">';			
					$glare_K_OD .= '	<tr>
											<td style="vertical-align:top;border-right:1px solid #CCC;"></td>
											<td colspan="2" style="vertical-align:top;border-right:1px solid #CCC;text-align:center;"><strong>K['.$autoSelectODName.']</strong></td>
											<td colspan="2" style="vertical-align:top;border-right:1px solid #CCC;text-align:center;"><strong>K['.$iolMasterSelectODName.']</strong></td>
											<td colspan="2" style="vertical-align:top;text-align:center;"><strong>K['.$topographerSelectODName.']</strong></td>
											
										</tr>
										<tr>
											<td style="vertical-align:top;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;"><strong>K1 : </strong></td>
											<td style="vertical-align:top;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k1Auto1OD.'</td>
											<td style="vertical-align:top;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k1Auto2OD.'</td>
											<td style="vertical-align:top;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k1IolMaster1OD.'</td>
											<td style="vertical-align:top;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k1IolMaster2OD.'</td>
											<td style="vertical-align:top;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k1Topographer1OD.'</td>
											<td style="vertical-align:top;border-top:1px solid #CCC;text-align:center;">'.$k1Topographer2OD.'</td>
										</tr>
										<tr>
											<td style="vertical-align:top;border-bottom:1px solid #CCC;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;"><strong>K2 : </strong></td>
											<td style="vertical-align:top;border-bottom:1px solid #CCC;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k2Auto1OD.'</td>
											<td style="vertical-align:top;border-bottom:1px solid #CCC;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k2Auto2OD.'</td>
											<td style="vertical-align:top;border-bottom:1px solid #CCC;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k2IolMaster1OD.'</td>
											<td style="vertical-align:top;border-bottom:1px solid #CCC;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k2IolMaster2OD.'</td>
											<td style="vertical-align:top;border-bottom:1px solid #CCC;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k2Topographer1OD.'</td>
											<td style="vertical-align:top;border-bottom:1px solid #CCC;border-top:1px solid #CCC;text-align:center;">'.$k2Topographer2OD.'</td>
										</tr>
								 ';			
					$glare_K_OD .= '</table>';	
				
				}
				if($k1Auto1OS || $k1Auto2OS || $k1IolMaster1OS || $k1IolMaster2OS || $k1Topographer1OS || $k1Topographer2OS || $k2Auto1OS || $k2Auto2OS || $k2IolMaster1OS || $k2IolMaster2OS || $k2Topographer1OS || $k2Topographer2OS) {
					$glare_K_OS .= '<table cellpadding="0" border="0" cellspacing="0" style="width:100%;border-top:1px solid #CCC;border-left:1px solid #CCC;border-right:1px solid #CCC;">';			
					$glare_K_OS .= '	<tr>
											<td style="border-right:1px solid #CCC;text-align:center;"></td>
											<td colspan="2" style="vertical-align:top;border-right:1px solid #CCC;text-align:center;"><strong>K['.$autoSelectOSName.']</strong></td>
											<td colspan="2" style="vertical-align:top;border-right:1px solid #CCC;text-align:center;"><strong>K['.$iolMasterSelectOSName.']</strong></td>
											<td colspan="2" style="vertical-align:top;text-align:center;"><strong>K['.$topographerSelectOSName.']</strong></td>
											
										</tr>
										<tr>
											<td style="vertical-align:top;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;"><strong>K1 : </strong></td>
											<td style="vertical-align:top;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k1Auto1OS.'</td>
											<td style="vertical-align:top;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k1Auto2OS.'</td>
											<td style="vertical-align:top;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k1IolMaster1OS.'</td>
											<td style="vertical-align:top;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k1IolMaster2OS.'</td>
											<td style="vertical-align:top;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k1Topographer1OS.'</td>
											<td style="vertical-align:top;border-top:1px solid #CCC;text-align:center;">'.$k1Topographer2OS.'</td>
										</tr>
										<tr>
											<td style="vertical-align:top;border-bottom:1px solid #CCC;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;"><strong>K2 : </strong></td>
											<td style="vertical-align:top;border-bottom:1px solid #CCC;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k2Auto1OS.'</td>
											<td style="vertical-align:top;border-bottom:1px solid #CCC;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k2Auto2OS.'</td>
											<td style="vertical-align:top;border-bottom:1px solid #CCC;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k2IolMaster1OS.'</td>
											<td style="vertical-align:top;border-bottom:1px solid #CCC;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k2IolMaster2OS.'</td>
											<td style="vertical-align:top;border-bottom:1px solid #CCC;border-top:1px solid #CCC;border-right:1px solid #CCC;text-align:center;">'.$k2Topographer1OS.'</td>
											<td style="vertical-align:top;border-bottom:1px solid #CCC;border-top:1px solid #CCC;text-align:center;">'.$k2Topographer2OS.'</td>
										</tr>
								 ';			
					$glare_K_OS .= '</table>';	
				
				}
			}
		}
		$glareInfoArr = array($glareVal,$glare_K_OD,$glare_K_OS,$operativeEyeVal,$unOperativeEyeVal,$assementVal,$lriVal,$lenSelectionVal);
		return $glareInfoArr;
	}

	public function __getMr1Mr2Mr3($ptId, $formId){
		global $web_RootDirectoryName;
		$MR1 = $MR2 = $MR3 = $MrGiven = "";
		
		$sql = "
			SELECT
			
			c1.exam_date, c1.mr_none_given,  c1.provider_id, c1.ex_number, c1.ex_desc,
			c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, 
			c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, 
			c2.sel_2 as sel_2_r, c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
			c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, 
			c3.prsm_p as prsm_p_l, c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, 
			c3.sel_2 as sel_2_l, c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l,
			us1.fname as us1_fname,us1.mname as us1_mname,us1.lname as us1_lname
			
			FROM chart_vis_master c0
			LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'
			LEFT JOIN users us1 ON us1.id = c1.provider_id
			WHERE c1.ex_type='MR' AND c1.ex_number IN (1,2,3) AND c0.form_id = '".$formId."' AND c0.patient_id = '".$ptId."'
			ORDER BY c1.ex_number
		";
		$rez = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($rez);$i++){
			
			$exnum = $row["ex_number"];	
			$Mr1_vis_mr_none_given 			= $row["mr_none_given"]; 
			$Mr1_vis_mr_none_givenExpld = explode(",",$Mr1_vis_mr_none_given);

			$Mr1_provider			= "";
			$Mr1_us1_fname 			= $row["us1_fname"]; 
			$Mr1_us1_mname 			= $row["us1_mname"]; 
			$Mr1_us1_lname 			= $row["us1_lname"]; 
			
			if(in_array("MR ".$exnum,$Mr1_vis_mr_none_givenExpld)){
				 $Mr1_provider = '&nbsp;'.substr($Mr1_us1_fname,0,1).substr($Mr1_us1_mname,0,1).substr($Mr1_us1_lname,0,1);
			}else if(trim($Mr1_us1_fname)) { 
				 $Mr1_provider = $Mr1_us1_lname.", ".$Mr1_us1_fname;
			}
			
			$givenMr1 = (in_array("MR ".$exnum,$Mr1_vis_mr_none_givenExpld)) ? "(Given)" : "";
			
			$Mr1_vis_mr_od_s 				= $row["sph_r"]; 
			$Mr1_vis_mr_od_c 				= $row["cyl_r"]; 
			$Mr1_vis_mr_od_a 				= $row["axs_r"]; 
			$Mr1_vis_mr_od_p 				= $row["prsm_p_r"]; 
			$Mr1_vis_mr_od_prism 			= $row["prism_r"];
			$Mr1_vis_mr_od_slash 			= $row["slash_r"]; 
			$Mr1_vis_mr_od_sel_1 			= $row["sel_1_r"]; 
			$Mr1_vis_mr_od_txt_1 			= $row["txt_1_r"]; 
			$Mr1_vis_mr_desc 				= $row["ex_desc"]; 
			$Mr1_vis_mr_od_txt_2 			= $row["txt_2_r"]; 
			$Mr1_vis_mr_od_add 				= $row["ad_r"]; 
			$Mr1_vis_mr_od_sel_2 			= $row["sel_2_r"];
			$Mr1_visMrOdSel2Vision 			= $row["sel2v_r"];
			$Mr1_vis_mr_os_s 				= $row["sph_l"]; 
			$Mr1_vis_mr_os_c 				= $row["cyl_l"]; 
			$Mr1_vis_mr_os_a 				= $row["axs_l"]; 
			$Mr1_vis_mr_os_p 				= $row["prsm_p_l"]; 
			$Mr1_vis_mr_os_prism 			= $row["prism_l"]; 
			$Mr1_vis_mr_os_slash 			= $row["slash_l"]; 
			$Mr1_vis_mr_os_sel_1 			= $row["sel_1_l"]; 
			$Mr1_vis_mr_os_txt_1 			= $row["txt_1_l"]; 
			$Mr1_vis_mr_os_txt_2 			= $row["txt_2_l"];
			$Mr1_vis_mr_os_add 				= $row["ad_l"];
			$Mr1_vis_mr_os_sel_2 			= $row["sel_2_l"]; 
			$Mr1_visMrOsSel2Vision 			= $row["sel2v_l"];
			$Mr1_provider_id 				= $row["provider_id"];
			
			$MR1t = "";
			$MR1t .= '<table cellpadding="0" border="0" cellspacing="0">';
				if(trim($Mr1_provider)){
					$MR1t .= '<tr><td colspan="12" class="text_10b" valign="top"><strong>'.$givenMr1.stripslashes($Mr1_provider).'</strong></td></tr>';
				}
				$MR1t .= '<tr>
							<td class="text_10b" valign="top">
								<table cellpadding="0" border="0" cellspacing="0">';
										$MR1t .='<tr>
													<td class="text_10b"><strong>OD </strong></td>
													<td class="text_10b">';
												  if($Mr1_vis_mr_od_s) 	{$MR1t .= '<strong>S </strong>'.$Mr1_vis_mr_od_s;}
											$MR1t .='</td><td class="text_10b" style="padding-left:5px;">';
												  if($Mr1_vis_mr_od_c) 	{$MR1t .= '<strong>C </strong>'.$Mr1_vis_mr_od_c;}
											$MR1t .='</td><td class="text_10b" style="padding-left:5px;">';
												  if($Mr1_vis_mr_od_a) 	{$MR1t .= '<strong>A </strong>'.$Mr1_vis_mr_od_a;}
											$MR1t .='</td><td class="text_10b">';
												  if($Mr1_vis_mr_od_txt_1 && $Mr1_vis_mr_od_txt_1 != "20/") {$MR1t .= $Mr1_vis_mr_od_txt_1;}
											$MR1t .='</td><td class="text_10b" style="padding-left:10px;">';
												  if($Mr1_vis_mr_od_add) 	{$MR1t .= '<strong>Add </strong>'.$Mr1_vis_mr_od_add;}
											$MR1t .='</td><td class="text_10b" style="padding-left:5px;">';
												  if($Mr1_vis_mr_od_txt_2 && $Mr1_vis_mr_od_txt_2 != "20/") {$MR1t .= $Mr1_vis_mr_od_txt_2;}
											$MR1t .='</td><td class="text_10b" style="padding-left:10px;">';
												  if($Mr1_vis_mr_od_p) 	{$MR1t .= '<strong>P </strong>'.$Mr1_vis_mr_od_p;}
											$MR1t .='</td><td class="text_10b" style="padding-left:5px;">';
												  if($Mr1_vis_mr_od_prism){$MR1t .= '<img src="/'.$web_RootDirectoryName.'/interface/common/new_html2pdf/pic_vision_pc.jpg">'.$Mr1_vis_mr_od_prism;}
											$MR1t .='</td><td class="text_10b">';
												  if($Mr1_vis_mr_od_slash){$MR1t .= '<strong>/ </strong>'.$Mr1_vis_mr_od_slash;}
											$MR1t .='</td><td class="text_10b">';
												  if($Mr1_vis_mr_od_sel_1){$MR1t .= $Mr1_vis_mr_od_sel_1;}
											$MR1t .='</td><td class="text_10b" style="padding-left:10px;">';
												  if($Mr1_vis_mr_od_sel_2 ||($Mr1_visMrOdSel2Vision && $Mr1_visMrOdSel2Vision != "20/")){
													$MR1t .= '<strong>GL/PH: </strong>';  
													if($Mr1_vis_mr_od_sel_2) {$MR1t .= $Mr1_vis_mr_od_sel_2.' ';}
													if($Mr1_visMrOdSel2Vision && $Mr1_visMrOdSel2Vision != "20/") {$MR1t .= 'Vision '.$Mr1_visMrOdSel2Vision;}
												  }
											$MR1t .='</td>
												</tr>';

										$MR1t .='<tr>
													<td class="text_10b"><strong>OS </strong></td>
													<td class="text_10b">';
												  if($Mr1_vis_mr_os_s) 	{$MR1t .= '<strong>S </strong>'.$Mr1_vis_mr_os_s;}
											$MR1t .='</td><td class="text_10b" style="padding-left:5px;">';
												  if($Mr1_vis_mr_os_c) 	{$MR1t .= '<strong>C </strong>'.$Mr1_vis_mr_os_c;}
											$MR1t .='</td><td class="text_10b" style="padding-left:5px;">';
												  if($Mr1_vis_mr_os_a) 	{$MR1t .= '<strong>A </strong>'.$Mr1_vis_mr_os_a;}
											$MR1t .='</td><td class="text_10b">';
												  if($Mr1_vis_mr_os_txt_1 && $Mr1_vis_mr_os_txt_1 != "20/") {$MR1t .= $Mr1_vis_mr_os_txt_1;}
											$MR1t .='</td><td class="text_10b" style="padding-left:10px;">';
												  if($Mr1_vis_mr_os_add) 	{$MR1t .= '<strong>Add </strong>'.$Mr1_vis_mr_os_add;}
											$MR1t .='</td><td class="text_10b" style="padding-left:5px;">';
												  if($Mr1_vis_mr_os_txt_2 && $Mr1_vis_mr_os_txt_2 != "20/") {$MR1t .= $Mr1_vis_mr_os_txt_2;}
											$MR1t .='</td><td class="text_10b" style="padding-left:10px;">';
												  if($Mr1_vis_mr_os_p) 	{$MR1t .= '<strong>P </strong>'.$Mr1_vis_mr_os_p;}
											$MR1t .='</td><td class="text_10b" style="padding-left:5px;">';//' '&#9650;
												  if($Mr1_vis_mr_os_prism){$MR1t .= '<img src="/'.$web_RootDirectoryName.'/interface/common/new_html2pdf/pic_vision_pc.jpg">'.$Mr1_vis_mr_os_prism;}
											$MR1t .='</td><td class="text_10b">';
												  if($Mr1_vis_mr_os_slash){$MR1t .= '<strong>/ </strong>'.$Mr1_vis_mr_os_slash;}
											$MR1t .='</td><td class="text_10b">';
												  if($Mr1_vis_mr_os_sel_1){$MR1t .= $Mr1_vis_mr_os_sel_1;}
											$MR1t .='</td><td class="text_10b" style="padding-left:10px;">';
												  if($Mr1_vis_mr_os_sel_2 ||($Mr1_visMrOsSel2Vision && $Mr1_visMrOsSel2Vision != "20/")){
													$MR1t .= '<strong>GL/PH: </strong>';  
													if($Mr1_vis_mr_os_sel_2) {$MR1t .= $Mr1_vis_mr_os_sel_2.' ';}
													if($Mr1_visMrOsSel2Vision && $Mr1_visMrOsSel2Vision != "20/") {$MR1t .= 'Vision '.$Mr1_visMrOsSel2Vision;}
												  }
											$MR1t .='</td>
												</tr>';
				
										if(trim($Mr1_vis_mr_desc)){
											$MR1t .= '<tr><td colspan="12" class="text_10b" valign="top"><strong>Mr1 Comments:</strong> '.stripslashes($Mr1_vis_mr_desc).'</td></tr>';
										}
						$MR1t .= '</table></td></tr>';
			$MR1t .= '</table>';
			
			${"MR".$exnum} = $MR1t;
			
			//if given
			if(empty($MrGiven) && !empty($givenMr1)){
				$MrGiven = $MR1t;
			}
			
		}
		
		if(!empty($MR1)){	
			$MrGiven = $MR1;	
		}
		$arrRep = array($MR1,$MR2,$MR3,$MrGiven);
		return $arrRep;
	}

	public function __getOcularSxODOSOU($ptId) {	
		$ocuSXQry = "Select title,sites,begdate  from lists where pid='".$ptId."' AND type ='6' AND allergy_status ='Active' AND proc_type ='surgery' order by begdate desc";
		$ocuSXRes = imw_query($ocuSXQry) or die(imw_error());
		if(imw_num_rows($ocuSXRes)>0){
			$arr_occu_od=$arr_occu_os="";
			$arr_occu_od_val=$arr_occu_os_val="";
			$arr_occu_full="";
			$arr_site=array(1=>"OS",2=>"OD",3=>"OU");
			while($occSXShow=imw_fetch_assoc($ocuSXRes)){
				if($occSXShow['sites']=="3" || $occSXShow['sites']=="2"){
					$arr_occu_od.="<tr><td style='width:99%;vertical-align:top;border-bottom:1px solid #ccc;' >".$occSXShow["title"]."</td></tr>";
				}	
				if($occSXShow['sites']=="3" || $occSXShow['sites']=="1"){
					$arr_occu_os.="<tr><td style='width:99%;vertical-align:top;border-bottom:1px solid #ccc;'>".$occSXShow["title"]."</td></tr>";
				}
				$date_proc = ($occSXShow["begdate"]);
				list($yy,$mm,$dd) = explode("-",$date_proc);
				$mm_p=$dd_p="";if($mm!="00"){$mm_p=$mm."-";}if($dd!="00"){$dd_p=$dd."-";}
				$date_of_proc=$mm_p.$dd_p.$yy;
				 $arr_occu_full.="<tr><td style='width:50%;border-bottom:1px solid #ccc;border-right:1px solid #ccc;height:18px;padding-left:4px;'>".$occSXShow["title"]."</td><td style='width:10%;border-right:1px solid #ccc;text-align:center;border-bottom:1px solid #ccc;height:18px;'>".$arr_site[$occSXShow["sites"]]."</td><td style='width:38%;border-bottom:1px solid #ccc;height:18px;padding-left:4px;'>".$date_of_proc."</td></tr>";
			}  			
				$arr_occu_od_val="<table cellpadding='0' cellspacing='0' style='width:99%;border-top:1px solid #ccc;border-left:1px solid #ccc;border-right:1px solid #ccc;'>".$arr_occu_od."</table>";
				$arr_occu_os_val="<table cellpadding='0' cellspacing='0' style='width:99%;border-top:1px solid #ccc;border-left:1px solid #ccc;border-right:1px solid #ccc;'>".$arr_occu_os."</table>";
				$arr_occu_full="<table cellpadding='0' cellspacing='0' style='width:99%;border-top:1px solid #ccc;border-left:1px solid #ccc;border-right:1px solid #ccc;'>
				<tr><td style='width:50%;border-bottom:1px solid #ccc;background: rgb(204, 204, 204);font-weight: bold;height:18px;'>Ocular Sx/Procedures	</td><td style='width:10%;border-bottom:1px solid #ccc;background: rgb(204, 204, 204);font-weight: bold;text-align:center;height:18px;'>Site</td><td style='width:38%;border-bottom:1px solid #ccc;background: rgb(204, 204, 204);font-weight: bold;height:18px;'>Date of Procedure</td></tr>
				".$arr_occu_full."</table>";
		}
		$ocularSX=array(0=>$arr_occu_od_val,1=>$arr_occu_os_val,2=>$arr_occu_full);
		return $ocularSX;
	}

	public function __get_set_pat_rel_values_retrive($dbValue,$methodFor,$delimiter = "~|~",$hifenOptional= ""){	
		$dbValue 	= trim($dbValue);		
		$methodFor 	= trim($methodFor);
		$delimiter	= trim($delimiter);	
		if($methodFor == "pat"){
			if(stristr($dbValue,$delimiter)){			
				list($strTxtPat,$strTxtRel) = explode($delimiter,$dbValue);
				$valueToShow = $strTxtPat;
			}
			else{
				$valueToShow = $dbValue;
			}
		}
		elseif($methodFor == "rel"){
			if(stristr($dbValue,$delimiter)){
				list($strTxtPat,$strTxtRel) = explode($delimiter,$dbValue);
				$valueToShow = $strTxtRel;
			}
			else{				
				$valueToShow = "";
			}
		}
		
		if($valueToShow) { $valueToShow = $hifenOptional.$valueToShow; }//FOR FACESHEET PDF
		
		return $valueToShow;
	}
	
	public function __getPtOcularInfo($pid){
		$qry = "SELECT you_wear, eye_problems, any_conditions_you, any_conditions_relative, eye_problems_other, OtherDesc from ocular where patient_id = '$pid'";
		$row = sqlQuery($qry);
		$retArr = array();
		$arrEyeHistory = array('None','Glasses','Contact Lenses','Glasses And Contact Lenses');
		
		$eyeProblems = array('Blurred or Poor Vision','Poor Night Vision','Gritty Sensation','Trouble Reading Signs',
							   'Glare From Lights','Tearing','Poor Depth Perception','Halos Around Lights','Itching or Burning',
							   'Trouble Identifying Colors','See Spots or Floaters','Eye Pain','Double Vision','See Light Flashes',
							   'Redness or Bloodshot','Others');
							   
		$arrCondition = array('Dry Eyes','Macula Degeneration','Glaucoma','Retinal Detachment','Cataracts', 'Keratoconus');
		
		
		$retArr['eye_history'] = $arrEyeHistory[$row['you_wear']];
		
		$arrEyeProb = explode(',',$row['eye_problems']);		

		$strAnyConditionsYou = $row["any_conditions_you"];
		$strAnyConditionsYou = $this->__get_set_pat_rel_values_retrive($strAnyConditionsYou,"pat","~|~");

		$arrYou = explode(',',$strAnyConditionsYou);
		
		$arrRelative = explode(',',$row['any_conditions_relative']);
		
		//$arrYouRel = array_merge($arrYou,$arrRelative);	//	merge relative array into arrYou
		//this is done because of sepration of ocular and family hx tab
		$arrYouRel = $arrYou;
		$arrYouRel = array_unique($arrYouRel);			//	Removes duplicate values from an array
		sort($arrYouRel);
		$k = 0;
		
		if(count($arrEyeProb) > 0){	
			for($i = 0; $i<count($arrEyeProb); $i++)
			{
				$val = (int)$arrEyeProb[$i];
				$val = $val - 1;
				$retArr['eye_problem'][$i] = $eyeProblems[$val];
			}
		}
//print_r($arrYouRel);
		for($j = 0; $j<count($arrYouRel); $j++)
		{
			
			if($arrYouRel[$j] != ''){
				$arrVal = (int)$arrYouRel[$j];
				$arrVal = $arrVal - 1;
				$retArr['you_rel'][$k] = $arrCondition[$arrVal];
				$k++;
			}
		}

		$retArr["eye_problems_other"] = $row["eye_problems_other"];
		
		$strOtherDesc = $row["OtherDesc"];
		$strOtherDesc = $this->__get_set_pat_rel_values_retrive($strOtherDesc,"pat","~|~");		
		$retArr["OtherDesc"] = $strOtherDesc;
		return $retArr;
		
	}
    
	public function __getMedHxSummary($ptId) {
		$ocData = $ghData = $MSAData = $immData = $medHxData = "";
		$arrPtOcular = $arrPtInfo = array();
		$history = "";
		$max_chars = 23;
		$max_showc = 20;
		//Ocular Start
		$ocData = "<tr><td><b>Ocular</b></td></tr>";
		$arrPtOcular = $this->__getPtOcularInfo($ptId);
		$history = $arrPtOcular["eye_history"];		
		$val = (strlen($history)>$max_chars)?substr($history,0,$max_showc)."...":$history;
		$historyEye = "<tr><td>".((trim($val) != "") ? "&nbsp;&nbsp;".$val : '')."</td></tr>";		
		$ocData .= $historyEye;
		if(count($arrPtOcular["eye_problem"]) > 0 || strlen($arrPtOcular["eye_problems_other"])>0){	
			$problem = "";
			foreach($arrPtOcular["eye_problem"] as $key => $val){
				if(count($val) > 0){
					$val = (strlen($val)>$max_chars)?substr($val,0,$max_showc)."...":$val;
					$problem .= "<tr><td style=\"white-space:nowrap; padding-left:5px;\">".((trim($val) != "") ? $val : '')."</td></tr>";				
				}
			}
		
			//-------eye problem other--------------
			$eye_problem_other = $arrPtOcular["eye_problems_other"];
			if($eye_problem_other!="" || !empty($eye_problem_other) || $eye_problem_other!=NULL){
				if(strlen($eye_problem_other)>20)
				{
					$eye_problem_other = substr($eye_problem_other,0,20)."...";
				}
				//$problem.="<tr><td id=\"tdOcEyeProbOther\" class=\"text_10\">&nbsp;&nbsp;".$eye_problem_other."</td></tr>";
				$problem.="<tr><td style=\"white-space:nowrap; padding-left:5px;\">".((trim($eye_problem_other) != "") ? $eye_problem_other : '')."</td></tr>";			
			}
			//-------eye problem other--------------
			$ocData .= "<tr><td>&nbsp;&nbsp;<b>Eye Problems</b></td></tr>";	
			$ocData .= "<tr><td>".$problem."</td></tr>";
		}
		
		$condition = "";
		if( count($arrPtOcular["you_rel"]) > 0 || strlen($arrPtOcular["OtherDesc"])>0){
			foreach($arrPtOcular["you_rel"] as $key => $val){
				if(count($val) > 0){
					$val = (strlen($val)>$max_chars)?substr($val,0,$max_showc)."...":$val;
					$condition .= "<tr><td style=\"white-space:nowrap; padding-left:5px;\">".((trim($val) != "") ? stripslashes($val) : '')."</td></tr>";
				}
			}		
			
			//-------Any Condition other--------------
			$any_condition_other = $arrPtOcular["OtherDesc"];
			if($any_condition_other != "" || !empty($any_condition_other) || $any_condition_other != NULL){
				if(strlen($any_condition_other)>20){
					$any_condition_other = substr($any_condition_other,0,20)."...";
				}
				$condition.="<tr><td style=\"white-space:nowrap; padding-left:5px;\">".((trim($any_condition_other) != "") ? stripslashes($any_condition_other) : '')."</td></tr>";			
			}		
			//------- Any Condition other --------------		
		}
		$ocData .= "<tr><td>&nbsp;&nbsp;<b>Any Conditions</b></td></tr>";	
		$ocData .= "<tr><td>".$condition."</td></tr>";
		//Ocular End
		
		//General Health Start
		
		$qryGetBS = "select id, sugar_value, date_format(creation_date,'%m-%d-%Y') as createdDate
							from patient_blood_sugar where patient_id = '$ptId' ORDER BY creation_date DESC LIMIT 1;";
		$rsGetBS = imw_query($qryGetBS);
		if($rsGetBS){
			$rowGetBS = imw_fetch_array($rsGetBS);
			$blood_sugar_date = $rowGetBS["createdDate"];
			$blood_sugar_value = $rowGetBS["sugar_value"];
			$ghData = "<tr><td>&nbsp;</td></tr><tr><td><b>Blood&nbsp;Sugar</b></td></tr>";
			$ghData .= "<tr><td>".((trim($blood_sugar_date) != "" && trim($blood_sugar_value) != "") ? $blood_sugar_date." - ".$blood_sugar_value." mg/dl" : '')."</td></tr>";
			imw_free_result($rsGetBS);
		}
		
		
		$qryGetCH = "Select id, cholesterol_total, cholesterol_triglycerides, 
				cholesterol_LDL, cholesterol_HDL, date_format(creation_date,'%m-%d-%Y') as date
				from patient_cholesterol where patient_id = '$ptId' order by creation_date desc LIMIT 1;";
		$rsGetCH = imw_query($qryGetCH);
		if($rsGetCH){
			$rowGetCH = imw_fetch_array($rsGetCH);
			$cholesterol_date = $rowGetCH["date"];
			$cholesterol_total = $rowGetCH["cholesterol_total"];
			$cholesterol_tri = $rowGetCH["cholesterol_triglycerides"];
			$cholesterol_ldl = $rowGetCH["cholesterol_LDL"];
			$cholesterol_hdl = $rowGetCH["cholesterol_HDL"];
			$ghData .= "<tr><td>&nbsp;</td></tr><tr><td><b>Cholesterol</b></td></tr>";
			$ghData .= "<tr><td>".((trim($cholesterol_date) != "" || trim($cholesterol_total) != "" || trim($cholesterol_tri) != "" || trim($cholesterol_ldl) != "" || trim($cholesterol_hdl) != "") ? $cholesterol_date." ".$cholesterol_total." ".$cholesterol_tri." ".$cholesterol_ldl." ".$cholesterol_hdl : '')."</td></tr>";
			imw_free_result($rsGetCH);
		}

		$ghData .= "<tr><td>&nbsp;</td></tr><tr><td>General Health</td></tr>";
		//$arrPtInfo = $this->__getPtGenHealthInfo($ptId);
		$arrShowGH = array_merge((array)$arrPtInfo["AnyCond"]["You"],(array)$arrPtInfo["AnyCond"]["Relatives"]);
		//	Removes duplicate values from an array
		$arrShowGH = array_unique($arrShowGH);	
		sort($arrShowGH);
		$strMedicalCond = "";	
		if(count($arrShowGH) > 0 ){
			foreach($arrShowGH as $key => $val){
				$val = (strlen($val)>$max_chars)?substr($val,0,$max_showc)."...":$val;
				if($val=="Diabetes" && trim($arrPtInfo["diabetes_values"])!=""){
					$diabetes_values = $arrPtInfo["diabetes_values"];
					if(strlen($diabetes_values)>9){
						$diabetes_values = substr($diabetes_values,0,9)."...";
					}
					$val.= " - ".$diabetes_values;
				}
				$strMedicalCond .= "<tr><td style=\"white-space:nowrap; padding-left:5px;\">".$val."</td></tr>";
			}
		}
		$ghData .= "<tr><td>".$strMedicalCond."</td></tr>";
		//ROS
		$strROS = "";	
		if(count($arrPtInfo["ROS"]) > 0 ){
			foreach($arrPtInfo["ROS"] as $key => $val){			
				if(count($val) > 0){
					$key = (strlen($key)>$max_chars)?substr($key,0,$max_showc)."...":$key;
					if($key != "negChkBx"){
						$strROS .= "<tr><td style=\"white-space:nowrap; padding-left:5px;\">".$key."</td></tr>";
					}
					elseif($key == "negChkBx"){
						foreach($arrPtInfo["ROS"]["negChkBx"] as $negChkBxKey => $negChkBxVal){
							$negChkBxVal = (strlen($negChkBxVal)>$max_chars)?substr($negChkBxVal,0,$max_showc)."...":$negChkBxVal;
							$strROS .= "<tr><td style=\"white-space:nowrap; padding-left:5px;color:green;\">Negative&nbsp;".$negChkBxVal."</td></tr>";						
						}
					}
				}
			}		
		}
		$ghData .= "<tr><td><b>Review of Systems</b></td></tr>";	
		$ghData .= "<tr><td>".$strROS."</td></tr>";
		
		$SmokeSumray = "";
		$qrySmoking = "select smoking_status, source_of_smoke, source_of_smoke_other, smoke_perday, alcohal, source_of_alcohal_other, list_drugs, otherSocial from social_history where patient_id = '$ptId'";
		$rsSmoking = imw_query($qrySmoking);
		if($rsSmoking){
			$rowSmoking = imw_fetch_array($rsSmoking);
			if((trim($rowSmoking["smoking_status"]) != "Never smoker" && trim($rowSmoking["smoking_status"]) != "") || (trim($rowSmoking["smoking_status"]) != "Never Smoked" && trim($rowSmoking["smoking_status"]) != "")){
				$smoke_source = $rowSmoking["source_of_smoke"];
				if($smoke_source == "Other"){
					$smoke_source = $rowSmoking["source_of_smoke_other"]; 
					if(strlen($smoke_source)>10){
						$smoke_source = substr($smoke_source,0,10)."...";
					}
				}
				
				$smoke_per_day = $rowSmoking["smoke_perday"];
				if($smoke_per_day=="" || empty($smoke_per_day) || $smoke_per_day==NULL){
					$smoke_per_day = "";
				}
				$smoke_str = (($rowSmoking["smoking_status"]) ? $rowSmoking["smoking_status"] : "").(($smoke_source) ? "&nbsp;of&nbsp;".$smoke_source : "")."&nbsp;".(($smoke_per_day) ? $smoke_per_day."&nbsp;Per Day" : "");
				$SmokeSumray .= "<tr><td>".(($smoke_str) ? "&nbsp;&nbsp;".$smoke_str : '')."</td></tr>";
			}
			else{
				$SmokeSumray .= "<tr><td>&nbsp;&nbsp;Never Smoked</td></tr>";
			}
			//-------alcohal-------
			$alcohal = $rowSmoking["alcohal"];
			if($alcohal == "Other"){
				$alcohal = $rowSmoking["source_of_alcohal_other"];
				if(strlen($alcohal)>13){
					$alcohal = substr($alcohal,0,13)."...";
				}
			}
			$SmokeSumray .="<tr><td>".((trim($alcohal) != "") ? "&nbsp;&nbsp;Alcohol ".$alcohal : '')."</td></tr>";
			//-------alcohal-------
	
			//-------List Drugs-------
			$list_drugs = $rowSmoking["list_drugs"];
			if(strlen($list_drugs)>21){
				$list_drugs = substr($list_drugs,0,21)."...";
			}
			$SmokeSumray .= "<tr><td>".((trim($list_drugs) != "") ? "&nbsp;&nbsp;".$list_drugs : '')."</td></tr>";
			//-------List Drugs-------			
	
			//-------More Information-------
			$other_social = $rowSmoking["otherSocial"];
			if(strlen($other_social)>20){
				$other_social = substr($other_social,0,20)."...";
			}
			$SmokeSumray .= "<tr><td>".((trim($other_social) != "") ? "&nbsp;&nbsp;".$other_social : '')."</td></tr>";
			//-------More Information-------
		}
		$ghData .= "<tr><td>&nbsp;</td></tr>";
		$ghData .= "<tr><td><b>Social</b></td></tr>";	
		$ghData .= $SmokeSumray;
		//General Health End
		
		//Medications, Sx/Procedures, Allergies Start
		$ocularData = '';
		$ocularSxData = '';
		$ocularAllData = '';
		$drugAllData = '';
		$medicationData = '';
		$sxProcData = '';
		$qryGetListData = "select id, title, type, ag_occular_drug from lists where pid = '$ptId' and allergy_status = 'Active' order by(id)";
		$rsGetListData = imw_query($qryGetListData);
		while($rowGetListData = imw_fetch_array($rsGetListData)){
			$title = ucfirst($rowGetListData['title']);
			if(strlen($title) > $max_chars){
				$title = substr($title,0,$max_showc).'...';
			}
			$type = $rowGetListData['type'];
			$ag_occular_drug = $rowGetListData['ag_occular_drug'];
			
			if($type == 7 && $ag_occular_drug != 'fdbATDrugName') {//SHOW ONLY DRUG ALLEGIES
				$type="";
			}
			//--- DELETED MEDICATION DATA --
			$rowStyle = '';
			switch ($type):
					case 1:
						//--- GET MEDICATION DATA -----
						$medicationData .= "<tr><td style=\"white-space:nowrap; padding-left:5px;\">&nbsp;&nbsp;$title</td></tr>";
					break;
					case 3:
						//--- GET OCULAR ALLERGY DATA -----
						$ocularAllData .= "<tr><td style=\"white-space:nowrap; padding-left:5px;\">$title</td></tr>";
					break;
					case 4:
						//--- GET OCULAR MEDICATION DATA -----
						$ocularData .= "<tr><td style=\"white-space:nowrap; padding-left:5px;\">$title</td></tr>";
					break;
					case 5:
						//--- GET SX/PROCEDURE DATA -----
						$sxProcData .= "<tr><td style=\"white-space:nowrap; padding-left:5px;\">$title</td></tr>";
					break;
					case 6:
						//--- GET OCULAR SX DATA -----
						$ocularSxData .= "<tr><td style=\"white-space:nowrap; padding-left:5px;\">$title</td></tr>";
					break;		
					case 7:
						//--- GET DRUG ALLERGY DATA -----
						$drugAllData .= "<tr><td style=\"white-space:nowrap; padding-left:5px;\">$title</td></tr>";
					break;
			endswitch;
		}
		
		$MSAData .= "<tr><td>&nbsp;</td></tr>";
		$MSAData .= "<tr><td><b>Ocular&nbsp;Medication</b></td></tr>";
		$MSAData .= "<tr><td>".$ocularData."</td></tr>";
		$MSAData .= "<tr><td>&nbsp;</td></tr>";
		$MSAData .= "<tr><td><b>Ocular Sx/Procedures</b></td></tr>";
		$MSAData .= "<tr><td>".$ocularSxData."</td></tr>";
		$MSAData .= "<tr><td>&nbsp;</td></tr>";
		$MSAData .= "<tr><td><b>Drug Allergies</b></td></tr>";
		$MSAData .= "<tr><td>".$drugAllData."</td></tr>";
		$MSAData .= "<tr><td>&nbsp;</td></tr>";
		$MSAData .= "<tr><td><b>Medication</b></td></tr>";
		$MSAData .= "<tr><td>".$medicationData."</td></tr>";
		$MSAData .= "<tr><td>&nbsp;</td></tr>";
		$MSAData .= "<tr><td><b>Sx/Procedures</b></td></tr>";
		$MSAData .= "<tr><td>".$sxProcData."</td></tr>";
		//Medications, Sx/Procedures, Allergies End
		
		$immData = "<tr><td>&nbsp;</td></tr>";
		$immData .= "<tr><td><b>Immunizations</b></td></tr>";
		$immDiv = "";
		$qryGetImmu = "select date_format(administered_date,'%m-%d-%Y') as administered_date,
						immunization_id,id from immunizations  
						where patient_id = '$ptId' and status = 'Given'";
		$rsGetImmu = imw_query($qryGetImmu);
		while($rowGetImmu = imw_fetch_array($rsGetImmu)){
			$administered_date = $rowGetImmu['administered_date'];
			if($administered_date == '00-00-0000'){
				$administered_date = '';
			}
			$name = $rowGetImmu['immunization_id'];
			$im_id = $rowGetImmu["id"];
			if(strlen($name) > $max_chars){
				$name = substr($name,0,$max_chars).'...';
			}		
			$immDiv .=  "<tr><td style=\"white-space:nowrap; padding-left:5px;\">".$administered_date.' '.$name."</td></tr>"; 		
		}
		$immData .= "<tr><td>".$immDiv."</td></tr>";
		
		$medHxData = "<table style=\"width:100%;\">";
		$medHxData .= $ocData.$ghData.$MSAData.$immData;
		$medHxData .= "</table>";
		return $medHxData;
	}
	
	public function __getMedProbList($pid) {	
		$medProbName = "";
		$problemNameArr = array();
		if($pid){
			$medProbQry = "SELECT problem_name FROM pt_problem_list WHERE pt_id='".$pid."' AND  
						status = 'Active' ORDER BY id";
			
			$medProbRes = imw_query($medProbQry) or die(imw_error());
			if(imw_num_rows($medProbRes)>0) {
				while($medProbRow = imw_fetch_array($medProbRes)) {
					$problemNameArr[] 	= trim(stripslashes($medProbRow["problem_name"]));
				}
				$medProbName = implode(', ',$problemNameArr);
			}
		}

		return $medProbName;
	}

	public function __getMedList($pid,$listType) {	
		$medList = "";
		$medListArr = array();
		if($pid && $listType){
			$medQry = "SELECT title,sig,qty,refills,referredby,destination, DATE_FORMAT(begdate,'%m-%d-%Y') as begdateNew ,
						DATE_FORMAT(enddate,'%m-%d-%Y') as enddateNew FROM lists WHERE pid='".$pid."' AND  
						allergy_status = 'Active' AND type in (".$listType.") ORDER BY id";
			$medRes = imw_query($medQry) or die(imw_error());
			if(imw_num_rows($medRes)>0) {
				while($medRow = imw_fetch_array($medRes)) {
					$dosage 		= trim($medRow["destination"]);
					$sig 			= trim($medRow["sig"]);
					$dosageSig 		= "";
					if(($dosage || $sig) && $listType=='4') { $dosageSig=trim('('.$dosage.' '.$sig.')');}
					
					$medListArr[] 	= $medRow["title"].$dosageSig;
				}
				$medName = implode(', ',$medListArr);
				$medList = '<table style="width:100%;"  cellpadding="0" cellspacing="0">	
								<tr>
									<td style="width:100%;" valign="top" >'.$medName.'</td>				
								</tr>';	
				$medList .= '</table>';
			}
		}
		return $medList;
	}

	public function __getPc1Pc2Pc3($ptId, $formId){
		global $web_RootDirectoryName;
		$PC1 = $PC2 = $PC3 = "";
		
		$sql = "
			SELECT
			
			c1.exam_date, c1.mr_none_given,  c1.provider_id, c1.ex_number, c1.ex_desc, c1.pc_near,
			
			c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, 
			c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, 
			c2.sel_2 as sel_2_r, c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
			c2.ovr_s as ovr_s_r, c2.ovr_c as ovr_c_r, c2.ovr_v as ovr_v_r, c2.ovr_a as ovr_a_r,  
			
			c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, 
			c3.prsm_p as prsm_p_l, c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, 
			c3.sel_2 as sel_2_l, c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l,
			c3.ovr_s as ovr_s_l, c3.ovr_c as ovr_c_l, c3.ovr_v as ovr_v_l, c3.ovr_a as ovr_a_l
			
			FROM chart_vis_master c0
			LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'
			
			WHERE c1.ex_type='PC' AND c1.ex_number IN (1,2,3) AND c0.form_id = '".$formId."' AND c0.patient_id = '".$ptId."'
			ORDER BY c1.ex_number
		";
		$rez = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($rez);$i++){
			
			$exnum = $row["ex_number"];  
			$pc_near 					= $row["pc_near"]; 
			$vis_pc_od_sel_1 			= $row["sel_1_r"]; 
			$vis_pc_od_s 				= $row["sph_r"]; 
			$vis_pc_od_c 				= $row["cyl_r"]; 
			$vis_pc_od_a 				= $row["axs_r"]; 
			$vis_pc_od_p 				= $row["prsm_p_r"]; 
			$vis_pc_od_prism 			= $row["prism_r"]; 
			$vis_pc_od_slash 			= $row["slash_r"]; 
			$vis_pc_od_sel_2 			= $row["sel_2_r"]; 
			$vis_pc_os_sel_1 			= $row["sel_1_l"]; 
			$vis_pc_os_s 				= $row["sph_l"]; 
			$vis_pc_os_c 				= $row["cyl_l"]; 
			$vis_pc_os_a 				= $row["axs_l"]; 
			$vis_pc_os_p 				= $row["prsm_p_l"]; 
			$vis_pc_os_prism 			= $row["prism_l"]; 
			$vis_pc_os_slash 			= $row["slash_l"]; 
			$vis_pc_os_sel_2 			= $row["sel_2_l"]; 
			$vis_pc_od_near_txt 		= $row["txt_1_r"]; 
			$vis_pc_os_near_txt 		= $row["txt_1_l"]; 
			$vis_pc_od_overref_s 		= $row["ovr_s_r"]; 
			$vis_pc_od_overref_c 		= $row["ovr_c_r"]; 
			$vis_pc_od_overref_v 		= $row["ovr_v_r"]; 
			$vis_pc_od_overref_a 		= $row["ovr_a_r"]; 
			$vis_pc_os_overref_s 		= $row["ovr_s_l"]; 
			$vis_pc_os_overref_c 		= $row["ovr_c_l"]; 
			$vis_pc_os_overref_v 		= $row["ovr_v_l"]; 
			$vis_pc_os_overref_a 		= $row["ovr_a_l"]; 
			$vis_pc_desc 				= $row["ex_desc"]; 
			//$prism_pc_1 				= $row["prism_pc_1"]; 
			//$vis_pc_od_i 				= $row["vis_pc_od_i"]; 
			//$vis_pc_os_i 				= $row["vis_pc_os_i"];
			$vis_pc_od_add 				= $row["ad_r"]; 
			$vis_pc_os_add 				= $row["ad_l"];
			
			$PC1t="";
			$PC1t .= '<table cellpadding="0" border="0" cellspacing="0">';
				
				$PC1t .= '<tr>
							<td class="text_10b" valign="top">
								<table cellpadding="0" border="0" cellspacing="0">';
										$PC1t .='<tr>
													<td class="text_10b"><strong>OD </strong></td>
													<td class="text_10b">';
												  if($vis_pc_od_s) 	{$PC1t .= '<strong>S </strong>'.$vis_pc_od_s;}

											$PC1t .='</td><td class="text_10b" style="padding-left:5px;">';
												  if($vis_pc_od_c) 	{$PC1t .= '<strong>C </strong>'.$vis_pc_od_c;}
											$PC1t .='</td><td class="text_10b" style="padding-left:5px;">';
												  if($vis_pc_od_a) 	{$PC1t .= '<strong>A </strong>'.$vis_pc_od_a;}
											$PC1t .='</td><td class="text_10b" style="padding-left:10px;">';
												  if($vis_pc_od_add) 	{$PC1t .= '<strong>Add </strong>'.$vis_pc_od_add;}
											$PC1t .='</td><td class="text_10b" style="padding-left:5px;">';
												  if($vis_pc_od_sel_1) {$PC1t .= $vis_pc_od_sel_1;}
											$PC1t .='</td><td class="text_10b" style="padding-left:10px;">';
												  if($vis_pc_od_p) 	{$PC1t .= '<strong>P </strong>'.$vis_pc_od_p;}
											$PC1t .='</td><td class="text_10b" style="padding-left:5px;">';
												  if($vis_pc_od_prism){$PC1t .= '<img src="/'.$web_RootDirectoryName.'/interface/common/new_html2pdf/pic_vision_pc.jpg">'.$vis_pc_od_prism;}
											$PC1t .='</td><td class="text_10b">';
												  if($vis_pc_od_slash){$PC1t .= '<strong>/ </strong>'.$vis_pc_od_slash;}
											$PC1t .='</td><td class="text_10b">';
												  if($vis_pc_od_sel_2){$PC1t .= $vis_pc_od_sel_2;}
											$PC1t .='</td>
												</tr>';

										$PC1t .='<tr>
													<td class="text_10b"><strong>OS </strong></td>
													<td class="text_10b">';
												  if($vis_pc_os_s) 	{$PC1t .= '<strong>S </strong>'.$vis_pc_os_s;}
											$PC1t .='</td><td class="text_10b" style="padding-left:5px;">';
												  if($vis_pc_os_c) 	{$PC1t .= '<strong>C </strong>'.$vis_pc_os_c;}
											$PC1t .='</td><td class="text_10b" style="padding-left:5px;">';
												  if($vis_pc_os_a) 	{$PC1t .= '<strong>A </strong>'.$vis_pc_os_a;}
											$PC1t .='</td><td class="text_10b" style="padding-left:10px;">';
												  if($vis_pc_os_add) 	{$PC1t .= '<strong>Add </strong>'.$vis_pc_os_add;}
											$PC1t .='</td><td class="text_10b" style="padding-left:5px;">';
												  if($vis_pc_os_sel_1) {$PC1t .= $vis_pc_os_sel_1;}
											$PC1t .='</td><td class="text_10b" style="padding-left:10px;">';
												  if($vis_pc_os_p) 	{$PC1t .= '<strong>P </strong>'.$vis_pc_os_p;}
											$PC1t .='</td><td class="text_10b" style="padding-left:5px;">';
												  if($vis_pc_os_prism){$PC1t .= '<img src="/'.$web_RootDirectoryName.'/interface/common/new_html2pdf/pic_vision_pc.jpg">'.$vis_pc_os_prism;}
											$PC1t .='</td><td class="text_10b">';
												  if($vis_pc_os_slash){$PC1t .= '<strong>/ </strong>'.$vis_pc_os_slash;}
											$PC1t .='</td><td class="text_10b">';
												  if($vis_pc_os_sel_2){$PC1t .= $vis_pc_os_sel_2;}
											$PC1t .='</td>
												</tr>';
				
										if(trim($vis_pc_desc)){
											$PC1t .= '<tr><td colspan="12" class="text_10b" valign="top"><strong>Pc1 Comments:</strong> '.stripslashes($vis_pc_desc).'</td></tr>';
										}
						$PC1t .= '</table></td></tr>';
			$PC1t .= '</table>';
			
			${"PC".$exnum} = $PC1t;
			
			
		}
		$arrRep = array($PC1,$PC2,$PC3);
		return $arrRep;
	}

	public function __getPcpInfo($refPhyId){
			if($refPhyId) {
				$sql = "select physician_Reffer_id as pcpphyId, Title as pcpTitle,FirstName as pcpFName,LastName as pcpLName, MiddleName as pcpMname,physician_fax,credential as pcpCredential FROM refferphysician WHERE physician_Reffer_id = '".$refPhyId."'";
				$res = imw_query($sql) or die(imw_error());
				$num = imw_num_rows($res);
				$row = imw_fetch_array($res);
				
				$pcpCredential 	= $row["pcpCredential"];
				$pcpFName 		= $row["pcpFName"];
				$pcpLName 		= $row["pcpLName"];
				$pcpMname 		= $row["pcpMname"];
				$pcpTitle 		= $row["pcpTitle"];
				
				$pcpFullName 	= trim($pcpTitle." ".$pcpFName);
				if($pcpMname) 		{ $pcpFullName 		= trim($pcpFullName." ".$pcpMname); }
				$pcpFullName 	= trim($pcpFullName." ".$pcpLName);
				if($pcpCredential) 	{ $pcpFullName 	= trim($pcpFullName.", ".$pcpCredential); }
				$pcpFullName	= trim($pcpFullName);
				
				$pcpArr = array($pcpCredential,$pcpFName,$pcpLName,$pcpMname,$pcpTitle,$pcpFullName);
				return $pcpArr;
			}
		}
		

	function getUserNPI($userId){
	$userNPI="";
	if($userId){
		$qryUserNPI = "SELECT user_npi as npi FROM `users` WHERE id ='".$userId."' and user_npi!=''";
		$rowUserNPI = imw_query($qryUserNPI);
		if(imw_num_rows($rowUserNPI)>0){
			$resUserNPI = imw_fetch_assoc($rowUserNPI);
			$userNPI = $resUserNPI['npi'];
		}
	}
	return $userNPI;
}	

	function getPtcopay($pid,$futapptInscase){
	$PtInsCopay="";
	$copay = "SELECT `copay` FROM `insurance_data` WHERE ins_caseid='".$futapptInscase."' AND `type`='primary' AND actInsComp=1 limit 0,1";
	$row_copay = imw_query($copay);
	if(imw_num_rows($row_copay)>0){ 
		$res_copay = imw_fetch_assoc($row_copay);
		$PtInsCopay= $res_copay['copay'];
	}else{ 
		$ins_case_qry =imw_query("SELECT  `ins_caseid` FROM `insurance_case` WHERE `ins_case_type` in(SELECT `case_id` FROM `insurance_case_types` WHERE case_name ='Medical' AND `status`=0) AND patient_id='".$pid."'"); 
		if(imw_num_rows($ins_case_qry)>0){
			  $ins_caseid = imw_fetch_assoc($ins_case_qry);
			  $get_ins_caseid = $ins_caseid['ins_caseid'];
		}
		$Pri_ins_copay =imw_query("SELECT `copay` FROM  `insurance_data` WHERE ins_caseid='".$get_ins_caseid."' AND `type`='primary'");
			if(imw_num_rows($Pri_ins_copay)>0){
				$InsCopay = imw_fetch_assoc($Pri_ins_copay);
				$PtInsCopay = $InsCopay['copay'];
		    }
	   }
		return $PtInsCopay;
}

	function getPatientdue($pid){
		$ptDue="";
		$pt_due = "SELECT sum(`patientdue`) FROM `patient_charge_list` where patient_id='$pid' and del_status='0'";
		$get_ptdue = imw_query($pt_due);
		if(imw_num_rows($get_ptdue)>0){
			$row_ptdue = imw_fetch_array($get_ptdue);
			$ptDue = $row_ptdue['0'];
		}
		return $ptDue;
	}

	public function __getAllergList($pid,$listType) {	
		$allergList = "";
		$allergListArr = array();
		if($pid && $listType){
			$allergQry = "SELECT title,comments,qty,refills,referredby,destination, DATE_FORMAT(begdate,'%m-%d-%Y') as begdateNew ,
						DATE_FORMAT(enddate,'%m-%d-%Y') as enddateNew FROM lists WHERE pid='".$pid."' AND  
						allergy_status = 'Active' AND type in (".$listType.") ORDER BY id";
			$allergRes = imw_query($allergQry) or die(imw_error());
			if(imw_num_rows($allergRes)>0) {
				while($allergRow = imw_fetch_array($allergRes)) {
					$comments 		= trim($allergRow["comments"]);
					$commentsVal 	= "";
					if(($comments) && $listType=='7') { $commentsVal=trim('('.$comments.')');}
					
					$allergListArr[] 	= $allergRow["title"].$commentsVal;
				}
				$allergName = implode(', ',$allergListArr);
				$allergList = '<table style="width:100%;"  cellpadding="0" cellspacing="0">	
								<tr>
									<td style="width:100%;" valign="top" >'.$allergName.'</td>				
								</tr>';	
				$allergList .= '</table>';
			}
		}
		return $allergList;
	}

	function getPatientTotaldue($pid){
		$ptTotDue="";
		$pt_tot_due = "SELECT sum(`totalBalance`) as pat_tot_bal FROM `patient_charge_list` WHERE `patient_id` ='$pid' and `del_status`='0'";
		$get_ptTotdue = imw_query($pt_tot_due);
		if(imw_num_rows($get_ptTotdue)>0){
			$row_ptTotdue = imw_fetch_array($get_ptTotdue);
			$ptTotDue = $row_ptTotdue['0'];
		}
		return $ptTotDue;
	}

	
	public function __loadTemplateData($templateData, $patientDetails="",$providerIds=0,$reportStartDate="",$reportEndDate="", $read_from_database=0,$appt_id=""){
		global $webroot;
		global $webServerRootDirectoryName;
		global $web_RootDirectoryName;
		$templ_vacab_data = $this->__getFormsVocab('pt_docs_template');
		$pat_details_vacab_arr = array_keys($templ_vacab_data[0]);
		
		$pat_details_vacab_arr = array_merge($pat_details_vacab_arr, array_keys($templ_vacab_data[1]));
		//print'<pre>';print_r($pat_details_vacab_arr);
		//--- DISPLAY KEY ELEMENT FOR DEBUGING WITH VACOBLARY ARRAY ---
		$replace_data_arr = array();
		$replace_data_arr['TIME'] 						= date('h:i A');
		$replace_data_arr['DATE'] 						= get_date_format(date('Y-m-d'));
		$replace_data_arr['DATE_F'] 					= date('F d, Y');
		$replace_data_arr['DRIVING LICENSE'] 			= $patientDetails['driving_licence'];
		
		$ethnicityShow = trim($patientDetails['ethnicity']);
		if(trim($patientDetails['otherEthnicity'])) {
			$ethnicityShow = trim($patientDetails['otherEthnicity']);		
		}
		$replace_data_arr['ETHNICITY'] 					= $ethnicityShow;
		$replace_data_arr['POS FACILITY'] 				= $patientDetails['facilityPracCode'];
		$replace_data_arr['HEARD ABOUT US'] 			= $patientDetails['heard_options'];
		$replace_data_arr['HEARD ABOUT US DETAIL'] 		= $patientDetails['heard_abt_desc'];

		$laguageShow = str_ireplace('Other -- ','',trim($patientDetails['language']));
		$replace_data_arr['LANGUAGE'] = $laguageShow;
		if(stristr($templateData,"{LAST NAME LEN~")){
			$splitLenData 	   = explode("{LAST NAME LEN",$templateData);
			for($r=1;$r<count($splitLenData);$r++){//echo $s."   : ".$splitFLenData[$s]."<hr>abc<hr>";	
				$splitDataLastName = $splitLenData[$r];
				$charLen 		   = explode("~",$splitDataLastName);
				$ptLnameLen	       = $charLen[1];
				$templateData 	   = str_ireplace("{LAST NAME LEN~".$ptLnameLen."~", "{LAST NAME LEN~20~}" ,$templateData);		
			}	
			$replace_data_arr['LAST NAME LEN~20~'] 		= substr($patientDetails['lname'],0,$ptLnameLen);
		}else{
			$replace_data_arr['LAST NAME LEN~20~'] 		= $patientDetails['lname'];
		}
		//=========LOGGED IN FACILITY INFO VOCABULARY REPLACEMENTS STARTS HERE==========================
		$loggedfacCity = $loggedfacState = $loggedfacCountry = $loggedfacPostalcode = $loggedfacExt = "";
		$loggedfacilityInfoArr 	= $this->logged_in_facility_info($_SESSION['login_facility']);
		$loggedfacstreet 			= $loggedfacilityInfoArr[1];
		$loggedfacity 		= $loggedfacilityInfoArr[2];
		$loggedfacstate	= $loggedfacilityInfoArr[3];
		$loggedfacPostalcode	= $loggedfacilityInfoArr[4];
		$loggedfacExt	   		= $loggedfacilityInfoArr[5];
		if($loggedfacPostalcode && $loggedfacExt){
			$loggedzipcodext = $loggedfacPostalcode.'-'.$loggedfacExt;
		}else{
			$loggedzipcodext = $loggedfacPostalcode;
		}
		$replace_data_arr['LOGGED_IN_FACILITY_ADDRESS'] = $loggedfacstreet.', '.$loggedfacity.',&nbsp;'.$loggedfacstate.'&nbsp;'.$loggedzipcodext;
		$replace_data_arr['LOGGED_IN_FACILITY_NAME'] 	= $loggedfacilityInfoArr[0];
		
		//=============================ENDS HERE=============================================================
		
		$replace_data_arr['MONTHLY INCOME'] 			= $patientDetails['monthly_income'];
		
		$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
		$op_name = ucfirst(trim($op_name_arr[1][0]));
		$op_name .= ucfirst(trim($op_name_arr[0][0]));
		$replace_data_arr['OPERATOR INITIAL'] 			= $op_name;
		$replace_data_arr['OPERATOR NAME'] 				= $_SESSION['authProviderName'];
		
		//--- PRIMARY CARE PROVIDER DETAILS ---

		if(empty($patientDetails['primary_care_phy_id']) === false){
			$reffPhySicianDetails = $this->__getReffPhysicianDetails($patientDetails['primary_care_phy_id'], $read_from_database);
			$referringPhyAdd = $reffPhySicianDetails[0]['refPhyAdd1'].' ';
			$referringPhyAdd .= trim($reffPhySicianDetails[0]['refPhyAdd2']);
		}
		
		$replace_data_arr['PCP STREET ADDR'] 			= trim($referringPhyAdd);
		$replace_data_arr['PCP City'] 					= trim($reffPhySicianDetails[0]['refPhyCity']);
		$replace_data_arr['PCP State'] 					= trim($reffPhySicianDetails[0]['refPhyState']);
		$replace_data_arr['PCP ZIP'] 					= trim($reffPhySicianDetails[0]['refPhyZip']);
		$replace_data_arr['ADDRESS1'] 					= $patientDetails['street'];
		$replace_data_arr['ADDRESS2']					= $patientDetails['street2'];
		$replace_data_arr['AGE'] 						= $this->__getPatientAge($patientDetails['DOB']);
		$replace_data_arr['PATIENT CITY'] 				= $patientDetails['city'];
		$replace_data_arr['DOB'] 						= $patientDetails['patient_dob'];
		
		$patient_email_address                          = $patientDetails['email'];
		$patient_email_address                          = str_ireplace("<","[",$patient_email_address);
		$patient_email_address                          = str_ireplace(">","]",$patient_email_address);
		
		$replace_data_arr['EMAIL ADDRESS'] 				= $patient_email_address;
		$replace_data_arr['EMERGENCY CONTACT'] 			= $patientDetails['contact_relationship'];
		$replace_data_arr['EMERGENCY CONTACT PH'] 		= $patientDetails['phone_contact'];
		$replace_data_arr['PATIENT EMPLOYER'] 			= $patientDetails['emp_name'];
		$replace_data_arr['PATIENT FIRST NAME'] 		= $patientDetails['fname'];
		if(stristr($templateData,"{PATIENT FIRST NAME LEN~")){
			$splitFLenData = explode("{PATIENT FIRST NAME LEN",$templateData);
			for($d=1;$d<count($splitFLenData);$d++){//echo $s."   : ".$splitFLenData[$s]."<hr>abc<hr>";	
				$splitFlen     = explode("~",$splitFLenData[$d]);//echo "<pre>";print_r($splitFLenData);echo "</pre>";
				$ptFnameLen	   = $splitFlen[1];
				$templateData  = str_ireplace("{PATIENT FIRST NAME LEN~".$ptFnameLen."~}", "{PATIENT FIRST NAME LEN~20~}" ,$templateData);		
			}
			$replace_data_arr['PATIENT FIRST NAME LEN~20~'] = substr($patientDetails['fname'],0,$ptFnameLen);
		}else{
			$replace_data_arr['PATIENT FIRST NAME LEN~20~'] = $patientDetails['fname'];
		}
		$replace_data_arr['SEX'] 						= $patientDetails['sex'];
		$replace_data_arr['HOME PHONE'] 				= $patientDetails['phone_home'];
		$replace_data_arr['PatientID'] 					= $patientDetails['pid'];
		$replace_data_arr['PATIENT INITIAL'] 			= strtoupper($patientDetails['fname'].$patientDetails['lname']);
		if(trim($patientDetails['ss'])!==''){
			$replace_data_arr['PATIENT_SS4'] 			= substr_replace($patientDetails['ss'],'XXX-XX',0,6);
		}else{
			$replace_data_arr['PATIENT_SS4'] 			= '';
		}
		$replace_data_arr['LAST NAME'] 					= $patientDetails['lname'];
		$replace_data_arr['PATIENT MRN'] 				= $patientDetails['External_MRN_1'];
		$replace_data_arr['PATIENT MRN2'] 				= $patientDetails['External_MRN_2'];
		$replace_data_arr['MARITAL STATUS'] 			= ucfirst($patientDetails['status']);
		$replace_data_arr['MIDDLE NAME'] 				= $patientDetails['mname'];
		$replace_data_arr['MOBILE PHONE'] 				= $patientDetails['phone_cell'];
		$replace_data_arr['PATIENT NAME TITLE'] 		= $patientDetails['title'];
		$replace_data_arr['PATIENT OCCUPATION'] 		= $patientDetails['occupation'];
		$replace_data_arr['OCCUPATION ADDRESS1'] 		= $patientDetails['emp_street'];
		$replace_data_arr['OCCUPATION ADDRESS2'] 		= $patientDetails['emp_street2'];
		$replace_data_arr['OCCUPATION CITY'] 			= $patientDetails['emp_city'];
		$replace_data_arr['OCCUPATION STATE'] 			= $patientDetails['emp_state'];
		$replace_data_arr['OCCUPATION ZIP'] 			= $patientDetails['emp_postal_code'];
		
		$p_imagename = $patientDetails['p_imagename'];
		$patientImage = "";
		global $protocol;
		global $phpServerIP;
		global $RootDirectoryName;
		if($p_imagename){
			//$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
			//$dirPath = $rootServerPath.$webroot.'/interface/main/uploaddir'.$p_imagename;
			$imageNAmeTEMP = $GLOBALS['fileroot'].'/data/'.PRACTICE_PATH.$p_imagename;
			//$dir_real_path = realpath($dirPath);
			//$dir_real_path = $dirPath;
			
			$img_name = substr($p_imagename,strrpos($p_imagename,'/')+1);	
			//copy($dir_real_path,$rootServerPath.$webroot.'/interface/reports/new_html2pdf/'.$img_name);
			//$imageNAmeTEMP = $webroot.'/interface/reports/new_html2pdf/'.$img_name;
			//$dirPath = $img_name;
			
			if(file_exists($imageNAmeTEMP)){
				$patient_img['patient'] = $img_name;
				$fileSize = getimagesize($imageNAmeTEMP);
				
				$imageNAmeTEMP = str_ireplace($RootDirectoryName,$web_RootDirectoryName,$imageNAmeTEMP); 				
				$imageNAmeTEMP = str_ireplace('/var/www/html',$protocol.$phpServerIP,$imageNAmeTEMP);
				
				if($fileSize[0]>80 || $fileSize[0]>90){
					//$imageWidth2 = ManageData::imageResize($fileSize[0],$fileSize[1],90);
					$imageWidth2 = $this->imageResize($fileSize[0],$fileSize[1],90);
					
					$patientImage = "<img style=\"cursor:pointer\" src=\"".$imageNAmeTEMP."\" alt=\"patient Image\" ".$imageWidth2.">";
				}
				else{
					//$imageNAmeTEMP = str_ireplace('/var/www/html',$protocol.$myExternalIP,$imageNAmeTEMP);
					$patientImage = "<img style=\"cursor:pointer\" src=\"".$imageNAmeTEMP."\" alt=\"patient Image\">";
				}
				
			}
		}
		
		$replace_data_arr['PATIENT PHOTO'] 				= $patientImage;
		
		$replace_data_arr['PATIENT SS'] 				= $patientDetails['ss'];
		$replace_data_arr['PATIENT STATE'] 				= $patientDetails['state'];
		$replace_data_arr['WORK PHONE'] 				= $patientDetails['phone_biz'];
		$replace_data_arr['PATIENT ZIP'] 				= $patientDetails['postal_code'];
		$replace_data_arr['PHYSICIAN FIRST NAME'] 		= $patientDetails['users_fname'];
		$replace_data_arr['PHYSICIAN LAST NAME'] 		= $patientDetails['users_lname'];
		$replace_data_arr['PHYSICIAN MIDDLE NAME'] 		= $patientDetails['users_mname'];
		
		//--- PRIMARY PHYSICIAN NAME ----
		$pri_phy_name_arr = array();
		$pri_phy_name_arr["LAST_NAME"] 					= $patientDetails['users_lname'];
		$pri_phy_name_arr["FIRST_NAME"] 				= $patientDetails['users_fname'];
		$pri_phy_name_arr["MIDDLE_NAME"] 				= $patientDetails['users_mname'];
		$pri_phy_name = $this->__changeNameFormat($pri_phy_name_arr);
		
		$apptInfoArr = $this->__getApptInfo($patientDetails['pid'],$providerIds,$reportStartDate,$reportEndDate,$appt_id,$newAPPTVars);
		if(!trim($patientDetails['users_fname'])) {
			$pri_phy_name = 	$apptInfoArr[5];
		}
		$replace_data_arr['PHYSICIAN NAME'] 			= $pri_phy_name;
		$replace_data_arr['PHYSICIAN NAME SUFFIX'] 		= $patientDetails['users_suffix'];
		
		//--- GET PATIENT INSURANCE COMPANY DETAILS ---
		$insDataDetailsArr = $this->__getInsuranceComDetails($patientDetails['pid'],'','',$appt_id);
		$primaryInsDetails = $insDataDetailsArr['primary'];
		//print'<pre>';print_r($insDataDetailsArr);
		$replace_data_arr['PRI INS ADDR'] = trim($primaryInsDetails['ins_comp_address']);
		
		//--- PRIMARY INSURANCE COMPANY DETAILS ----
		$replace_data_arr['PRIMARY ADDRESS'] = trim($primaryInsDetails['ins_address']);
		$replace_data_arr['PRIMARY BIRTHDATE'] 			= $primaryInsDetails['subscriber_DOB'];
		$replace_data_arr['PRIMARY CITY'] 				= $primaryInsDetails['subscriber_city'];
		$replace_data_arr['PRIMARY EMPLOYER'] 			= $primaryInsDetails['subscriber_employer'];
		$replace_data_arr['PRIMARY GROUP'] 				= $primaryInsDetails['group_number'];
		
		$ins_name = trim($primaryInsDetails['ins_name']);
		if(empty($ins_name) === true){
			$ins_name = trim($primaryInsDetails['in_house_code']);
		}
		if(stristr($templateData,"{PRIMARY INSURANCE COMPANY LEN~")){
			$splitPri = explode("{PRIMARY INSURANCE COMPANY LEN",$templateData);
			for($r=1;$r<count($splitPri);$r++){
				$splitPriData = $splitPri[$r];
				$charPriLen	  = explode("~",$splitPriData);
				$ptPriLen     = $charPriLen[1];
				$templateData = str_ireplace("{PRIMARY INSURANCE COMPANY LEN~".$ptPriLen."~", "{PRIMARY INSURANCE COMPANY LEN~20~}" ,$templateData);		
			}	
			$replace_data_arr['PRIMARY INSURANCE COMPANY LEN~20~'] = substr($ins_name,0,$ptPriLen);
		}else{
			$replace_data_arr['PRIMARY INSURANCE COMPANY LEN~20~'] = $ins_name;
		}
		if($primaryInsDetails['scan_card'] || $primaryInsDetails['scan_card2']){
			$img_real_path=$webServerRootDirectoryName.$webroot.'/interface/main/uploaddir'.$primaryInsDetails['scan_card'];
			$priImageSize = getimagesize($img_real_path);
			$newSize = '';
			if($priImageSize[0] > 395 && $priImageSize[1] < 840){
				$newSize = $this->imageResize(680,400,710);						
				
				$priImageSize[0] = 710;
			}		
			elseif($priImageSize[0] > 700){
				//width > 700
				//$newSize = $this->imageResize($priImageSize[0],$priImageSize[1],840);						
				$newSize = $this->newImageResize($img_real_path,700);
				
				$priImageSize[1] =700;
				
			}					
			elseif($priImageSize[1] > 840){
				//hight > 700
				//$newSize = $this->imageResize($priImageSize[0],$priImageSize[1],840);						
				$newSize = $this->newImageResize($img_real_path,700,800);
													
				$priImageSize[1] = 800;
				
			}								
			else{					
				$newSize = $priImageSize[3];
				
				//echo $image_view; die;
			}	
			
			$primaryInsCard.='
			<table style="width:100%">';
			if($primaryInsDetails['scan_card']){
				$insLabel=explode("/",$primaryInsDetails['scan_card']);
				$insCardLabel=end($insLabel);
				$primaryInsCard.='<tr><td><b>PRIMARY SCAN DOCUMENT</b><br>'.$insCardLabel.'<br><i>scan_card</i><br><img '.$newSize.' src='.$webroot.'/interface/main/uploaddir'.$primaryInsDetails['scan_card'].'></td></tr>';
			}
			if($primaryInsDetails['scan_card2']){
				$insLabel1=explode("/",$primaryInsDetails['scan_card2']);
				$insCardLabel1=end($insLabel1);
				$primaryInsCard.='<tr><td>'.$insCardLabel.'<br><i>scan_card2</i><br><img '.$newSize.' src='.$webroot.'/interface/main/uploaddir'.$primaryInsDetails['scan_card2'].'></td></tr>';
			}
			$primaryInsCard.='</table>';
		}
		$replace_data_arr['PRIMARY INSURANCE SCAN CARD'] 	= $primaryInsCard;
		$replace_data_arr['PRIMARY INSURANCE COMPANY'] 	= $ins_name;
		$replace_data_arr['PRIMARY PHONE'] 				= $primaryInsDetails['phone'];
		$replace_data_arr['PRIMARY POLICY'] 			= $primaryInsDetails['policy_number'];
		$replace_data_arr['PRIMARY SOCIAL SECURITY'] 	= $primaryInsDetails['subscriber_ss'];
		$replace_data_arr['PRIMARY STATE'] 				= $primaryInsDetails['subscriber_state'];
		
		//--- INSURANCE SUBSCRIBER NAME FORMAT ---
		$subscriber_name_arr = array();
		$subscriber_name_arr["LAST_NAME"] 				= $primaryInsDetails['subscriber_lname'];
		$subscriber_name_arr["FIRST_NAME"] 				= $primaryInsDetails['subscriber_fname'];
		$subscriber_name_arr["MIDDLE_NAME"] 			= $primaryInsDetails['subscriber_mname'];
		$subscriber_name = $this->__changeNameFormat($subscriber_name_arr);
		$replace_data_arr['PRIMARY SUBSCRIBER NAME'] 	= $subscriber_name;
		$replace_data_arr['PRIMARY SUBSCRIBER RELATIONSHIP'] = $primaryInsDetails['subscriber_relationship'];
		$replace_data_arr['PRIMARY ZIP'] 				= $primaryInsDetails['subscriber_postal_code'];
		
		$replace_data_arr['STATE ZIP CODE'] 			= NULL;
		
		$raceShow = trim($patientDetails['race']);
		if(trim($patientDetails['otherRace'])) {
			$raceShow = trim($patientDetails['otherRace']);		
		}
		$replace_data_arr['RACE'] 						= $raceShow;
		
		//--- DEFAULTE REFERRING PHYSICIAN DETAILS ---
		if(empty($patientDetails['primary_care_id']) === false){
			$reffPhySicianDetails = $this->__getReffPhysicianDetails($patientDetails['primary_care_id'], $read_from_database);
			$ref_phy_lname 								= $reffPhySicianDetails[0]['refphyLName'];
			$ref_phy_fname 								= $reffPhySicianDetails[0]['refphyFName'];
			$ref_phy_mname 								= $reffPhySicianDetails[0]['refPhyMname'];
			$ref_phy_name_arr = array();
			$ref_phy_name_arr["LAST_NAME"] 		= $ref_phy_lname;
			$ref_phy_name_arr["FIRST_NAME"] 	= $ref_phy_fname;
			$ref_phy_name_arr["MIDDLE_NAME"] 	= $ref_phy_mname;
			$ref_phy_name 	= $this->__changeNameFormat($ref_phy_name_arr);
			
			$ref_phy_add 	= $reffPhySicianDetails[0]['refPhyAdd1'].' ';
			$ref_phy_add 	.= trim($reffPhySicianDetails[0]['refPhyAdd2']);
			$refPhyCity 	= $reffPhySicianDetails[0]['refPhyCity'];
			$refPhyState 	= $reffPhySicianDetails[0]['refPhyState'];
			$refPhyZip 		= $reffPhySicianDetails[0]['refPhyZip'];
			$refPhyFax 		= $reffPhySicianDetails[0]['refFax'];
			
			$refPhone 		= $reffPhySicianDetails[0]['refPhone'];
			$refPhyTitle 	= $reffPhySicianDetails[0]['refPhyTitle'];
		}
		$replace_data_arr['REF PHY STREET ADDR'] 		= trim($ref_phy_add);
		$replace_data_arr['REF PHY CITY'] 				= trim($refPhyCity);
		$replace_data_arr['REF PHY FAX'] 				= trim($refPhyFax);
		$replace_data_arr['REF PHY PHONE'] 				= trim($refPhone);
		$replace_data_arr['REF PHY SPECIALITY'] 		= NULL;
		$replace_data_arr['REF PHY STATE'] 				= $refPhyState;
		$replace_data_arr['REF PHY ZIP'] 				= $refPhyZip;
		$replace_data_arr['REF PHYSICIAN FIRST NAME'] 	= $ref_phy_fname;
		$replace_data_arr['REF PHYSICIAN LAST NAME'] 	= $ref_phy_lname;
		$replace_data_arr['REF PHYSICIAN TITLE'] 		= $refPhyTitle;
		$replace_data_arr['REFFERING PHY'] 				= $ref_phy_name;
		$replace_data_arr['REGISTRATION DATE'] 			= $patientDetails['reg_date'];
		$replace_data_arr['REL INFO'] 					= $this->getPtReleaseInfoNames($patientDetails['pid']);
		
		//--- GET RESPONSIBLE PARTY DETAILS ---
		$patient_id = $_SESSION['patient'];if($patientDetails['pid']){$patient_id = $patientDetails['pid'];}
		if($patientDetails['pid']){
			$patient_id = $patientDetails['pid'];
		}
		$respPartyQry = "select title, fname, lname, mname, date_format(dob, '".get_sql_date_format()."') as resp_dob,
					sex, ss, address, city, state, zip,relation, home_ph, work_ph, mobile, licence, address2,
					marital from resp_party where patient_id = '$patient_id'";
		$respPartyQryRes = get_array_records_query($respPartyQry);
		//==========================RESPONSIBLE PARTY DATA REPLACEMENT STARTS HERE==========================
		//IF PATIENT HAVE NO RESPONSIBLE PERSON THEN PATIENT SELF DATA WILL BE REPLACED WITH RESPONSIBLE VARIABLES.
		if(count($respPartyQryRes)>0){
			$replace_data_arr['RES.PARTY ADDRESS1'] 		= $respPartyQryRes[0]['address'];
			$replace_data_arr['RES.PARTY ADDRESS2'] 		= $respPartyQryRes[0]['address2'];
			$replace_data_arr['RES.PARTY CITY'] 			= $respPartyQryRes[0]['city'];
			$replace_data_arr['RES.PARTY DD NUMBER']		= $respPartyQryRes[0]['licence'];
			$replace_data_arr['RES.PARTY DOB'] 				= $respPartyQryRes[0]['resp_dob'];
			$replace_data_arr['RES.PARTY FIRST NAME']		= $respPartyQryRes[0]['fname'];
			$replace_data_arr['RES.PARTY SEX'] 				= $respPartyQryRes[0]['sex'];
			$replace_data_arr['RES.PARTY HOME PH'] 			= $respPartyQryRes[0]['home_ph'];
			$replace_data_arr['RES.PARTY LAST NAME']		= $respPartyQryRes[0]['lname'];
			$replace_data_arr['RES.PARTY MARITAL STATUS']	= $respPartyQryRes[0]['marital'];
			$replace_data_arr['RES.PARTY MIDDLE NAME'] 		= $respPartyQryRes[0]['mname'];
			$replace_data_arr['RES.PARTY MOBILE PH']		= $respPartyQryRes[0]['mobile'];
			$replace_data_arr['RES.PARTY RELATION'] 		= $respPartyQryRes[0]['relation'];
			$replace_data_arr['RES.PARTY SS'] 				= $respPartyQryRes[0]['ss'];
			$replace_data_arr['RES.PARTY STATE'] 			= $respPartyQryRes[0]['state'];
			$replace_data_arr['RES.PARTY TITLE'] 			= $respPartyQryRes[0]['title'];
			$replace_data_arr['RES.PARTY WORK PH'] 			= $respPartyQryRes[0]['work_ph'];
			$replace_data_arr['RES.PARTY ZIP'] 				= $respPartyQryRes[0]['zip'];
		
		}else{
			$replace_data_arr['RES.PARTY ADDRESS1'] 		= $patientDetails['street'];
			$replace_data_arr['RES.PARTY ADDRESS2'] 		= $patientDetails['street2'];
			$replace_data_arr['RES.PARTY CITY'] 			= $patientDetails['city'];
			$replace_data_arr['RES.PARTY DD NUMBER']		= $patientDetails['driving_licence'];
			$replace_data_arr['RES.PARTY DOB'] 				= $patientDetails['patient_dob'];
			$replace_data_arr['RES.PARTY FIRST NAME']		= $patientDetails['fname'];
			$replace_data_arr['RES.PARTY SEX'] 				= $patientDetails['sex'];
			$replace_data_arr['RES.PARTY HOME PH'] 			= $patientDetails['phone_home'];
			$replace_data_arr['RES.PARTY LAST NAME']		= $patientDetails['lname'];
			$replace_data_arr['RES.PARTY MARITAL STATUS'] 	= ucfirst($patientDetails['status']);
			$replace_data_arr['RES.PARTY MIDDLE NAME'] 		= $patientDetails['mname'];
			$replace_data_arr['RES.PARTY MOBILE PH']		= $patientDetails['phone_cell'];
			$replace_data_arr['RES.PARTY RELATION'] 		= 'Self';
			$replace_data_arr['RES.PARTY SS'] 				= $patientDetails['ss'];
			$replace_data_arr['RES.PARTY STATE'] 			= $patientDetails['state'];
			$replace_data_arr['RES.PARTY TITLE'] 			= $patientDetails['title'];
			$replace_data_arr['RES.PARTY WORK PH'] 			= $patientDetails['phone_biz'];
			$replace_data_arr['RES.PARTY ZIP'] 				= $patientDetails['postal_code'];
		}
		//--- SECONDARY INSURANCE COMPANIES DETAILS ---
		$secondaryInsDetails 							= $insDataDetailsArr['secondary'];
		$replace_data_arr['SEC INS ADDR'] 				= trim($secondaryInsDetails['ins_comp_address']);
		
		$replace_data_arr['SECONDARY ADDRESS'] 			= trim($secondaryInsDetails['ins_address']);
		$replace_data_arr['SECONDARY BIRTHDATE']		= $secondaryInsDetails['subscriber_DOB'];
		$replace_data_arr['SECONDARY CITY'] 			= $secondaryInsDetails['subscriber_city'];
		$replace_data_arr['SECONDARY EMPLOYER'] 		= $secondaryInsDetails['subscriber_employer'];
		$replace_data_arr['SECONDARY GROUP'] 			= $secondaryInsDetails['group_number'];
		
		$ins_name = trim($secondaryInsDetails['ins_name']);
		if(empty($ins_name) === true){
			$ins_name = trim($secondaryInsDetails['in_house_code']);
		}
		if(stristr($templateData,"{SECONDARY INSURANCE COMPANY LEN~")){
			$splitSecData 	   = explode("{SECONDARY INSURANCE COMPANY LEN",$templateData);
			for($e=1;$e<count($splitSecData);$e++){//echo $s."   : ".$splitFLenData[$s]."<hr>abc<hr>";	
				$splitSecDataLen = $splitSecData[$e];
				$charSecLen	 	 = explode("~",$splitSecDataLen);
				$ptSecLen    	 = $charSecLen[1];
				$templateData	 = str_ireplace("{SECONDARY INSURANCE COMPANY LEN~".$ptSecLen."~", "{SECONDARY INSURANCE COMPANY LEN~20~}" ,$templateData);		
			}	
			$replace_data_arr['SECONDARY INSURANCE COMPANY LEN~20~'] = substr($ins_name,0,$ptSecLen);
		}else{
			$replace_data_arr['SECONDARY INSURANCE COMPANY LEN~20~'] = $ins_name;
		}
		if($secondaryInsDetails['scan_card'] || $secondaryInsDetails['scan_card2']){
			$img_real_path=$webServerRootDirectoryName.$webroot.'/interface/main/uploaddir'.$primaryInsDetails['scan_card'];
			$priImageSize = getimagesize($img_real_path);
			$newSize = '';
			if($priImageSize[0] > 395 && $priImageSize[1] < 840){
				$newSize = $this->imageResize(680,400,710);						
				
				$priImageSize[0] = 710;
			}		
			elseif($priImageSize[0] > 700){
				//width > 700
				//$newSize = $this->imageResize($priImageSize[0],$priImageSize[1],840);						
				$newSize = $this->newImageResize($img_real_path,700);
				
				$priImageSize[1] =700;
				
			}					
			elseif($priImageSize[1] > 840){
				//hight > 700
				//$newSize = $this->imageResize($priImageSize[0],$priImageSize[1],840);						
				$newSize = $this->newImageResize($img_real_path,700,800);
													
				$priImageSize[1] = 800;
				
			}								
			else{					
				$newSize = $priImageSize[3];
				
				//echo $image_view; die;
			}
			$secondaryInsCard.='
			<table style="width:100%">';
			if($secondaryInsDetails['scan_card']){
				$secodaryLabel=explode("/",$secondaryInsDetails['scan_card']);
				$secodaryCardLabel=end($secodaryLabel);
				$secondaryInsCard.='<tr><td><b>SECONDARY SCAN DOCUMENT</b><br>'.$secodaryCardLabel.'<br><i>scan_card</i><br><img '.$newSize.' src='.$webroot.'/interface/main/uploaddir'.$secondaryInsDetails['scan_card'].'></td></tr>';
			}
			if($secondaryInsDetails['scan_card2']){
				$secodaryLabel2=explode("/",$secondaryInsDetails['scan_card2']);
				$secodaryCardLabel2=end($secodaryLabel2);
				$secondaryInsCard.='<tr><td>'.$secodaryLabel2.'<br><i>scan_card</i><img '.$newSize.' src='.$webroot.'/interface/main/uploaddir'.$secondaryInsDetails['scan_card2'].'></td></tr>';}
			$secondaryInsCard.='</table>';
		}
		$replace_data_arr['SECONDARY INSURANCE SCAN CARD']	= $secondaryInsCard;
		$replace_data_arr['SECONDARY INSURANCE COMPANY']= $ins_name;
		$replace_data_arr['SECONDARY PHONE'] 			= $secondaryInsDetails['phone'];
		$replace_data_arr['SECONDARY POLICY'] 			= $secondaryInsDetails['policy_number'];
		$replace_data_arr['SECONDARY SOCIAL SECURITY'] 	= $secondaryInsDetails['subscriber_ss'];
		$replace_data_arr['SECONDARY STATE'] 			= $secondaryInsDetails['subscriber_state'];
		
		//--- INSURANCE SUBSCRIBER NAME FORMAT ---
		$subscriber_name_arr = array();
		$subscriber_name_arr["LAST_NAME"] 				= $secondaryInsDetails['subscriber_lname'];
		$subscriber_name_arr["FIRST_NAME"] 				= $secondaryInsDetails['subscriber_fname'];
		$subscriber_name_arr["MIDDLE_NAME"] 			= $secondaryInsDetails['subscriber_mname'];
		$subscriber_name = $this->__changeNameFormat($subscriber_name_arr);
		$replace_data_arr['SECONDARY SUBSCRIBER NAME'] = $subscriber_name;
		$replace_data_arr['SECONDARY SUBSCRIBER RELATIONSHIP'] = $secondaryInsDetails['subscriber_relationship'];
		$replace_data_arr['SECONDARY ZIP'] 				= $secondaryInsDetails['subscriber_postal_code'];
		$secondaryInsCard="";
		//$replace_data_arr['USER DEFINE 1'] 				= $patientDetails['genericval1'];
		//$replace_data_arr['USER DEFINE 2'] 				= $patientDetails['genericval2'];
		$formId 										= $this->__getLatestDosFormId($patientDetails['pid']);

		//Start - Following vocabularies already replaced in interface\chart_notes\scan_docs\load_pt_docs.php
		//For array matching we need to replace them here also, however they are already replaced
		$replace_data_arr['A &amp; P']					= "";
		
		//===============APPT FACILITY NAME & ADDRESS VARIABLES REPLACEMENT WORK START HERE=============
		
		$Facility_address = "";
		$Zip_code_ext	  = "";
		
				//===========ZIP CODE + EXTENSION CONCATENATION=====================
		if($apptInfoArr[13] && $apptInfoArr[14]){ 
			$Zip_code_ext = $apptInfoArr[13].'-'.$apptInfoArr[14]; 
		}else{
			$Zip_code_ext = $apptInfoArr[13];
		}
			  //===========FACILITY ADDRESS VARIABLE CONCATENATION==================
		if($apptInfoArr[10] && $apptInfoArr[11]){
			$Facility_address .= $apptInfoArr[10].',&nbsp;'.$apptInfoArr[11].',&nbsp;'.$apptInfoArr[12].'&nbsp;'.$Zip_code_ext.'&nbsp;'.$apptInfoArr[3];	
	   }else if($apptInfoArr[10]){
			$Facility_address .= $apptInfoArr[10].',&nbsp;'.$apptInfoArr[12].'&nbsp;'.$Zip_code_ext.'&nbsp;'.$apptInfoArr[3];	
		}else if($apptInfoArr[11]){
			$Facility_address .= $apptInfoArr[11].',&nbsp;'.$apptInfoArr[12].'&nbsp;'.$Zip_code_ext.'&nbsp;'.$apptInfoArr[3];
		}
		//============10 ==> STREET/ 11 ==> CITY/ 12 ==> STATE/ 13-14 ==> ZIP CODE - EXT/ 3 ==> PHONE===	
		$replace_data_arr['APPT FACILITY ADDRESS']		= $Facility_address;
		$replace_data_arr['APPT FACILITY NAME']			= $apptInfoArr[2];	
		
		//======================================THE END==================================================
		
		$replace_data_arr['ASSESSMENT ALL']				= "";
		$replace_data_arr['APPT_FUTURE']		 		= $this->__getApptFuture($patientDetails['pid'],$reportStartDate,$reportEndDate);
		//End - Following vocabularies already replaced in interface\chart_notes\scan_docs\load_pt_docs.php

		$replace_data_arr['APPT_HX'] 					= $this->__getApptHx($patientDetails['pid'],$reportStartDate,$reportEndDate);
		$replace_data_arr['APPT COMMENTS']				= $apptInfoArr[9];
		$replace_data_arr['APPT DATE']					= $apptInfoArr[0];
		$replace_data_arr['APPT DATE_F']				= $apptInfoArr[1];
		$replace_data_arr['APPT FACILITY']				= $apptInfoArr[2];
		$replace_data_arr['APPT FACILITY PHONE']		= $apptInfoArr[3];
		$replace_data_arr['APPT PROC']					= $apptInfoArr[4];
		$replace_data_arr['APPT PROVIDER']				= $apptInfoArr[5];
		$replace_data_arr['APPT PROVIDER LAST NAME']	= $apptInfoArr[6];
		$replace_data_arr['APPT SITE']					= $apptInfoArr[7];
		$replace_data_arr['APPT TIME']					= $apptInfoArr[8];
		
		//Start - Following vocabularies already replaced in interface\chart_notes\scan_docs\load_pt_docs.php
		//For array matching we need to replace them here also, however they are already replaced
		
		
		$replace_data_arr['ASSESSMENT &amp; PLAN']		= "";
		$replace_data_arr['ASSESSMENT 1']				= "";
		$replace_data_arr['ASSESSMENT 2']				= "";
		$replace_data_arr['ASSESSMENT 3']				= "";
		$replace_data_arr['ASSESSMENT 4']				= "";
		$replace_data_arr['ASSESSMENT 5']				= "";
		$replace_data_arr['ASSESSMENT OD']				= "{ASSESSMENT OD}";
		$replace_data_arr['ASSESSMENT OS']				= "{ASSESSMENT OS}";
		
		//End - Following vocabularies already replaced in interface\chart_notes\scan_docs\load_pt_docs.php
		$arr_vision										= $this->__getVision($patientDetails['pid'], $formId);
		$arr_mr1NbcvaOdOs								= $this->__getmr1NbcvaOdOs($patientDetails['pid'], $formId);
		$replace_data_arr['BCVA OD']					= $arr_mr1NbcvaOdOs[2];
		$replace_data_arr['BCVA OS']					= $arr_mr1NbcvaOdOs[3];
		
		$replace_data_arr['CYL OD']						= $arr_vision[5];
		$replace_data_arr['CYL OS']						= $arr_vision[6];
		
		$replace_data_arr['DISTANCE OD']				= $arr_vision[7];
		$replace_data_arr['DISTANCE OS']				= $arr_vision[8];
		
		
		$chartLeftVal 									= $this->__getChartLeftCcHx($patientDetails['pid'], $formId);
		$replace_data_arr['DOMINANT EYE']				= $chartLeftVal[0];

		$glareInfoArr 									= $this->__getGlareInfo($patientDetails['pid'], $formId);
		$replace_data_arr['GLARE'] 						= $glareInfoArr[0];
		$replace_data_arr['GLARE_K_OD'] 				= $glareInfoArr[1];
		$replace_data_arr['GLARE_K_OS'] 				= $glareInfoArr[2];
		$replace_data_arr['LRI'] 						= $glareInfoArr[6];
		$replace_data_arr['LENS SELECTION'] 			= $glareInfoArr[7];
		$MrArr 											= $this->__getMr1Mr2Mr3($patientDetails['pid'], $formId);
		$replace_data_arr['MR GIVEN'] 					= $MrArr[3];
		$replace_data_arr['MR1 OD'] 					= $arr_mr1NbcvaOdOs[0];
		$replace_data_arr['MR1 OS'] 					= $arr_mr1NbcvaOdOs[1];
		$arr_ocular_values								= $this->__getOcularSxODOSOU($patientDetails['pid']);
		$replace_data_arr['MED HX']						= $this->__getMedHxSummary($patientDetails['pid']);
		$replace_data_arr['MEDICAL PROBLEM']			= $this->__getMedProbList($patientDetails['pid']);
		
		$replace_data_arr['NEAR OD']					= $arr_vision[3];
		$replace_data_arr['NEAR OS']					= $arr_vision[4];
		$replace_data_arr['NO SHOW APPOINTMENT']		= $apptInfoArr[18];
		
		$replace_data_arr['OCULAR'] 					= $arr_ocular_values[2];
		$replace_data_arr['OCULAR MEDICATION'] 			= $this->__getMedList($patientDetails['pid'],4);
	
		$replace_data_arr['OCULAR OD'] 					= $arr_ocular_values[0];
		$replace_data_arr['OCULAR OS'] 					= $arr_ocular_values[1];
		$replace_data_arr['OCULAR PATHOLOGY']			= $glareInfoArr[5];
		$replace_data_arr['OPERATIVE EYE']				= $glareInfoArr[3];
		$PcArr 											= $this->__getPc1Pc2Pc3($patientDetails['pid'], $formId);
		$replace_data_arr['PC1'] 						= $PcArr[0];
		$replace_data_arr['PC2'] 						= $PcArr[1];
		$replace_data_arr['PC3'] 						= $PcArr[2];
		
		$pcpInfoArr 									= $this->__getPcpInfo($patientDetails['primary_care_phy_id']);
		$replace_data_arr['PCP CREDENTIAL'] 			= $pcpInfoArr[0];
		$replace_data_arr['PCP FIRST NAME'] 			= $pcpInfoArr[1];
		$replace_data_arr['PCP FULL NAME'] 				= $pcpInfoArr[5];
		$replace_data_arr['PCP LAST NAME'] 				= $pcpInfoArr[2];
		$replace_data_arr['PCP MIDDLE NAME'] 			= $pcpInfoArr[3];
		$replace_data_arr['PCP TITLE NAME'] 			= $pcpInfoArr[4];
		$replace_data_arr['PHYSICIAN NPI'] 				= $this->getUserNPI($_SESSION['authId']);

		$replace_data_arr['PLAN ALL']					= "";//already replaced in interface\chart_notes\scan_docs\load_pt_docs.php

		$replace_data_arr['PT_COPAY']					= $this->getPtcopay($patientDetails['pid'],$apptInfoArr[15]);
		$replace_data_arr['PT_DUE']						= $this->getPatientdue($patientDetails['pid']);
		$replace_data_arr['PT-KEY']						= $patientDetails['temp_key'];
		$replace_data_arr['PATIENT ALLERGIES'] 			= $this->__getAllergList($patientDetails['pid'],'7');

		//Start - Following vocabularies already replaced in interface\chart_notes\scan_docs\load_pt_docs.php
		//For array matching we need to replace them here also, however they are already replaced
		$replace_data_arr['PLAN 1']						= "";
		$replace_data_arr['PLAN 2']						= "";
		$replace_data_arr['PLAN 3']						= "";
		$replace_data_arr['PLAN 4']						= "";
		$replace_data_arr['PLAN OD']					= "{PLAN OD}";
		$replace_data_arr['PLAN OS']					= "{PLAN OS}";
		//End - Following vocabularies already replaced in interface\chart_notes\scan_docs\load_pt_docs.php
		
		
		$replace_data_arr['SYSTEMIC MEDICATION']		= $this->__getMedList($patientDetails['pid'],1);
		$replace_data_arr['TOTAL_DUE']					= $this->getPatientTotaldue($patientDetails['pid']);
		$replace_data_arr['UNOPERATIVE EYE']			= $glareInfoArr[4];
		
		$vision_od										= $arr_vision[1];
		$vision_os										= $arr_vision[2];
		$replace_data_arr['V-CC-OD']					= $vision_od;
		$replace_data_arr['V-CC-OS']					= $vision_os;
		
		
		//$pat_details_vacab_arr[] = '{PLNAME}';
		
		//--- GET VALUES FOR REPLACE VARIABLES ----
		$replace_data_arr = array_values($replace_data_arr);
		//print'<pre>';print_r($pat_details_vacab_arr);
		//print'<pre>';print_r($replace_data_arr);
		$templateData = str_ireplace($pat_details_vacab_arr, $replace_data_arr ,$templateData);
		
		
		$templateData = preg_replace("/[{}]/", "" ,$templateData);
		//<p>&#160;</p>
		if(substr($templateData,-13)=="<p>&#160;</p>"){
			$StrLen=strlen($templateData);
			$templateData=substr($templateData,0,($StrLen-13));
		}
		return $templateData;
	}
	public function mysqlifetchdata($query=''){
	    $mysqli = $this->mysqliConnect();
	    if(trim($query) == ''){
	        $query = $this->QUERY_STRING;
	    }
	    $result = $mysqli->query($query);
	    if($result){
	        $return = array();
	        if($result->num_rows){
	            while($this->result_data = $result->fetch_assoc()){
	                $return[] = $this->changeFormat($this->result_data);
	            }
	        }
	    }
	    return $return;
	}
	public function patient_proc_bal_update($encounter){
	    if(empty($encounter) === false){
	        $chld_cpt_self_arr=array();
	        $getProcedureDetailsStr = "SELECT b.cpt_prac_code,b.cpt_desc,b.not_covered,b.cpt_fee_id
										FROM cpt_fee_tbl as b
										WHERE b.not_covered=1";
	        $getProcedureDetailsQry = imw_query($getProcedureDetailsStr);
	        while($getProcedureDetailsRows = imw_fetch_array($getProcedureDetailsQry)){
	            $chld_cpt_ref_arr[$getProcedureDetailsRows['cpt_fee_id']]=$getProcedureDetailsRows['cpt_prac_code'];
	        }
	        
	        $this->QUERY_STRING = "select * from patient_charge_list where del_status='0' and encounter_id='$encounter'";
	        $qryRes = $this->mysqlifetchdata();
	        mysqli_fetch_data();
	        $enc_id_arr=array();
	        for($i=0;$i<count($qryRes);$i++){
	            $patient_id = $qryRes[$i]['patient_id'];
	            $encounter_id = $qryRes[$i]['encounter_id'];
	            $charge_list_id = $qryRes[$i]['charge_list_id'];
	            $primary_paid = $qryRes[$i]['primary_paid'];
	            $secondary_paid = $qryRes[$i]['secondary_paid'];
	            $tertiary_paid = $qryRes[$i]['tertiary_paid'];
	            $primaryInsuranceCoId = $qryRes[$i]['primaryInsuranceCoId'];
	            $secondaryInsuranceCoId = $qryRes[$i]['secondaryInsuranceCoId'];
	            $tertiaryInsuranceCoId = $qryRes[$i]['tertiaryInsuranceCoId'];
	            $copay = $qryRes[$i]['copay'];
	            
	            $getPaidByStr = "SELECT a.paid_by,a.insProviderId,
								 a.insCompany,b.overPayment,
								 a.paymentClaims,a.encounter_id,
								 b.paidForProc,b.charge_list_detail_id
								 FROM patient_chargesheet_payment_info as a,
								 patient_charges_detail_payment_info b
								 WHERE
								 a.encounter_id in($encounter_id)
								 AND a.payment_id = b.payment_id
								 AND b.deletePayment='0'";
	            $getPaidByQry = imw_query($getPaidByStr);
	            while($getPaidByRows = imw_fetch_array($getPaidByQry)){
	                $enc_id=$getPaidByRows['encounter_id'];
	                if($getPaidByRows['paymentClaims']=='Negative Payment'){
	                    $tot_enc_neg_paid_amt_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
	                    $tot_enc_neg_paid_amt_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
	                    
	                    $tot_chld_neg_paid_amt_arr[$getPaidByRows['charge_list_detail_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
	                    $tot_chld_neg_paid_amt_arr[$getPaidByRows['charge_list_detail_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
	                    
	                    $final_tot_neg_paid_amt_arr[$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
	                    $final_tot_neg_paid_amt_arr[$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
	                }else{
	                    $tot_enc_paid_amt_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
	                    $tot_enc_paid_amt_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
	                    
	                    $tot_chld_paid_amt_arr[$getPaidByRows['charge_list_detail_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
	                    $tot_chld_paid_amt_arr[$getPaidByRows['charge_list_detail_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
	                    
	                    if($getPaidByRows['charge_list_detail_id']==0){
	                        $tot_chld_paid_amt_copay_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
	                        $tot_chld_paid_amt_copay_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
	                    }
	                    $final_tot_paid_amt_arr[$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
	                    $final_tot_paid_amt_arr[$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
	                }
	                
	                if($getPaidByRows['paymentClaims']=='Deposit'){
	                    $paid_proc_arr[$enc_id]['paidForProc'][] = $getPaidByRows['paidForProc'];
	                    $deposit_proc_arr[$enc_id][$getPaidByRows['charge_list_detail_id']][] = $getPaidByRows['paidForProc'];
	                }
	            }
	            
	            $pri_bor_paid_col="";
	            $sec_bor_paid_col="";
	            $tri_bor_paid_col="";
	            $pat_bor_paid_col="";
	            $pat_heard_ins="false";
	            if($primary_paid=="false" && $primaryInsuranceCoId>0){
	                $pri_bor_paid_col="border";
	            }else if($secondary_paid=="false" && $secondaryInsuranceCoId>0){
	                $sec_bor_paid_col="border";
	            }else if($tertiary_paid=="false" && $tertiaryInsuranceCoId>0){
	                $tri_bor_paid_col="border";
	            }else{
	                if($newBal>0){
	                    $pat_bor_paid_col="border";
	                }
	                $pat_heard_ins="true";
	            }
	            $qry=imw_query("select * from patient_charge_list_details where del_status='0' and charge_list_id='$charge_list_id'");
	            while($row=imw_fetch_array($qry)){
	                $proc_approvedAmt=$row['approvedAmt'];
	                $charge_list_detail_id=$row['charge_list_detail_id'];
	                $chld_id=$row['charge_list_detail_id'];
	                $proc_newBalance=$row['newBalance'];
	                $proc_coPayAdjustedAmount = $row['coPayAdjustedAmount'];
	                $proc_selfpay = $row['proc_selfpay'];
	                $proc_deductAmt = $row['deductAmt'];
	                $procCode  = $row['procCode'];
	                
	                $chld_deduct_arr=array();
	                $getDeductDetailsStr = "SELECT deduct_ins_id,deduct_amount FROM payment_deductible WHERE delete_deduct=0 and
											deductible_by='Insurance' and charge_list_detail_id='$charge_list_detail_id'";
	                $getDeductDetailsQry = imw_query($getDeductDetailsStr);
	                while($getDeductDetailsRows = imw_fetch_array($getDeductDetailsQry)){
	                    $chld_deduct_arr[$getDeductDetailsRows['deduct_ins_id']][]=$getDeductDetailsRows['deduct_amount'];
	                }
	                $pri_deduct=0;
	                $sec_deduct=0;
	                $tri_deduct=0;
	                $pri_deduct=array_sum($chld_deduct_arr[$primaryInsuranceCoId]);
	                $sec_deduct=array_sum($chld_deduct_arr[$secondaryInsuranceCoId]);
	                $tri_deduct=array_sum($chld_deduct_arr[$tertiaryInsuranceCoId]);
	                $for_pri_deduct=true;
	                $for_sec_deduct=false;
	                $for_tri_deduct=false;
	                $for_pat_deduct=false;
	                
	                $chld_denied_arr=array();
	                $getDeniedDetailsStr = "SELECT deniedById,deniedAmount FROM deniedpayment WHERE denialDelStatus=0 and
											deniedBy='Insurance' and charge_list_detail_id='$charge_list_detail_id'";
	                $getDeniedDetailsQry = imw_query($getDeniedDetailsStr);
	                while($getDeniedDetailsRows = imw_fetch_array($getDeniedDetailsQry)){
	                    $chld_denied_arr[$getDeniedDetailsRows['deniedById']][]=$getDeniedDetailsRows['deniedAmount'];
	                }
	                $pri_denied=0;
	                $sec_denied=0;
	                $tri_denied=0;
	                $pri_denied=array_sum($chld_denied_arr[$primaryInsuranceCoId]);
	                $sec_denied=array_sum($chld_denied_arr[$secondaryInsuranceCoId]);
	                $tri_denied=array_sum($chld_denied_arr[$tertiaryInsuranceCoId]);
	                
	                $tot_chld_pri_paid_amt_all_num=count($tot_chld_paid_amt_arr[$chld_id][1]);
	                $tot_chld_sec_paid_amt_all_num=count($tot_chld_paid_amt_arr[$chld_id][2]);
	                $tot_chld_tri_paid_amt_all_num=count($tot_chld_paid_amt_arr[$chld_id][3]);
	                $tot_chld_pat_paid_amt_all_num=count($tot_chld_paid_amt_arr[$chld_id][0]);
	                
	                $tot_chld_pri_paid_amt_all=array_sum($tot_chld_paid_amt_arr[$chld_id][1])-array_sum($tot_chld_neg_paid_amt_arr[$chld_id][1]);
	                $tot_chld_sec_paid_amt_all=array_sum($tot_chld_paid_amt_arr[$chld_id][2])-array_sum($tot_chld_neg_paid_amt_arr[$chld_id][2]);
	                $tot_chld_tri_paid_amt_all=array_sum($tot_chld_paid_amt_arr[$chld_id][3])-array_sum($tot_chld_neg_paid_amt_arr[$chld_id][3]);
	                $tot_chld_pat_paid_amt_all=array_sum($tot_chld_paid_amt_arr[$chld_id][0])-array_sum($tot_chld_neg_paid_amt_arr[$chld_id][0]);
	                
	                if($proc_coPayAdjustedAmount>0){
	                    $tot_chld_pat_paid_amt_all=$tot_chld_pat_paid_amt_all+array_sum($tot_chld_paid_amt_copay_arr[$encounter_id][0]);
	                }
	                if($primary_paid=="true"){
	                    $for_pri_deduct=false;
	                }
	                if($secondary_paid=="true"){
	                    $for_sec_deduct=false;
	                }
	                if($secondaryInsuranceCoId>0 && ($pri_deduct>0 || $primary_paid=="true" || $tot_chld_pri_paid_amt_all>0 || $tot_chld_pri_paid_amt_all_num>0) && $sec_deduct<=0 && $secondary_paid=="false"){
	                    $for_sec_deduct=true;
	                    $for_pri_deduct=false;
	                }
	                if($secondaryInsuranceCoId>0 && ($pri_denied>0 || $primary_paid=="true" || $tot_chld_pri_paid_amt_all>0 || $tot_chld_pri_paid_amt_all_num>0) && $sec_denied<=0  && $secondary_paid=="false"){
	                    $for_sec_deduct=true;
	                    $for_pri_deduct=false;
	                }
	                
	                if($for_sec_deduct==true && ($sec_deduct>0 || $sec_denied>0 || $tot_chld_sec_paid_amt_all>0 || $tot_chld_sec_paid_amt_all_num>0) && ($pri_denied>0 || $pri_deduct>0 || $tot_chld_pri_paid_amt_all>0 || $tot_chld_pri_paid_amt_all_num>0)){
	                    $for_sec_deduct=false;
	                    $for_pri_deduct=false;
	                    $for_tri_deduct=true;
	                }
	                if($for_pri_deduct==true && ($pri_deduct>0 || $pri_denied>0 || $primaryInsuranceCoId==0 || $tot_chld_pri_paid_amt_all>0 || $tot_chld_pri_paid_amt_all_num>0)){
	                    $for_pri_deduct=false;
	                }
	                if($for_pri_deduct==false && $for_sec_deduct==false){
	                    $for_tri_deduct=true;
	                }
	                if($for_tri_deduct==true && ($tri_deduct>0 || $tri_denied>0 || $tertiaryInsuranceCoId==0 || $tot_chld_tri_paid_amt_all>0 || $tot_chld_tri_paid_amt_all_num>0)){
	                    $for_pri_deduct=false;
	                }
	                
	                if(($for_sec_deduct==false || $sec_deduct>0 || $sec_denied>0 || $secondaryInsuranceCoId==0 || $tot_chld_sec_paid_amt_all>0 || $tot_chld_sec_paid_amt_all_num>0)
	                        && ($for_tri_deduct==false || $tri_deduct>0 || $tri_denied>0 || $tertiaryInsuranceCoId==0 || $tot_chld_tri_paid_amt_all>0 || $tot_chld_tri_paid_amt_all_num>0)
	                        && $for_pri_deduct==false){
	                            $for_pat_deduct=true;
	                }
	                
	                //if($for_sec_deduct==false && $for_pri_deduct==false && ($sec_deduct>0 || $secondaryInsuranceCoId==0)){
	                 //$for_pat_deduct=true;
	                 //}
	                 
	                 //if($for_sec_deduct==false && $for_pri_deduct==false && ($sec_denied>0 || $secondaryInsuranceCoId==0)){
	                 //$for_pat_deduct=true;
	                 //}
	                $ref_amt="";
	                $ref_amt=$chld_cpt_ref_arr[$procCode];
	                $tot_pt_balance_proc=0;
	                $pri_due=0;
	                $sec_due=0;
	                $tri_due=0;
	                if($pat_heard_ins == "true"){
	                    $pri_due = 0;
	                    $sec_due = 0;
	                    $tri_due = 0;
	                    //$patientDueRes = $proc_approvedAmt-($tot_chld_pri_paid_amt_all+$tot_chld_sec_paid_amt_all);
	                    $tot_pt_balance_proc=$proc_newBalance;
	                }else{
	                    if($pat_bor_paid_col!=""){
	                        $tot_pt_balance_proc=$proc_newBalance;
	                        $pri_due = 0;
	                        $sec_due = 0;
	                        $tri_due = 0;
	                    }else{
	                        if($proc_coPayAdjustedAmount>0){
	                            if($proc_selfpay>0){
	                                $tot_pt_balance_proc=$proc_newBalance;
	                            }else{
	                                if($for_pri_deduct == true){
	                                    $pri_due=$proc_newBalance;
	                                }else{
	                                    if($for_sec_deduct == true || $for_tri_deduct == true){
	                                        //$tot_pt_balance_proc=$copay;
	                                    }else{
	                                        $tot_pt_balance_proc=$proc_newBalance;
	                                    }
	                                }
	                            }
	                        }else{
	                            if($for_pri_deduct == true){
	                                $pri_due=$proc_newBalance;
	                            }else{
	                                if($for_sec_deduct == true || $for_tri_deduct == true){
	                                }else{
	                                    $tot_pt_balance_proc=$proc_newBalance;
	                                }
	                            }
	                        }
	                        if($proc_selfpay>0 || $ref_amt>0){
	                            $tot_pt_balance_proc=$proc_newBalance;
	                        }
	                        //$tot_pt_balance_proc=$tot_pt_balance_proc-$tot_chld_pat_paid_amt_all;
	                    }
	                }
	                if($tot_pt_balance_proc<0){ $tot_pt_balance_proc=0;}
	                
	                if($for_sec_deduct == true){
	                    $sec_due=$proc_deductAmt;
	                }
	                if($for_sec_deduct == false && $for_tri_deduct == true){
	                    $tri_due=$proc_deductAmt;
	                }
	                if($for_pat_deduct == true){
	                    $tot_pt_balance_proc=$proc_newBalance;
	                }
	                if(($tot_chld_pri_paid_amt_all>0 || $tot_chld_pri_paid_amt_all_num>0) && $primaryInsuranceCoId>0){
	                    if(($tot_chld_sec_paid_amt_all>0 || $tot_chld_sec_paid_amt_all_num>0) && $secondaryInsuranceCoId>0){
	                        if(($tot_chld_tri_paid_amt_all>0 || $tot_chld_tri_paid_amt_all_num>0) && $tertiaryInsuranceCoId>0){
	                            $tot_pt_balance_proc=$proc_newBalance;
	                        }else{
	                            if($tertiaryInsuranceCoId>0 && $for_tri_deduct==true){
	                                $tri_due=$proc_newBalance-$tot_pt_balance_proc;
	                            }else{
	                                $tot_pt_balance_proc=$proc_newBalance;
	                            }
	                        }
	                    }else{
	                        if($secondaryInsuranceCoId>0 && $for_sec_deduct==true){
	                            $sec_due=$proc_newBalance-$tot_pt_balance_proc;
	                        }else{
	                            if(($tot_chld_tri_paid_amt_all>0 || $tot_chld_tri_paid_amt_all_num>0) && $tertiaryInsuranceCoId>0){
	                                $tot_pt_balance_proc=$proc_newBalance;
	                            }else{
	                                if($tertiaryInsuranceCoId>0 && $for_tri_deduct==true){
	                                    $tri_due=$proc_newBalance-$tot_pt_balance_proc;
	                                }else{
	                                    $tot_pt_balance_proc=$proc_newBalance;
	                                }
	                            }
	                        }
	                    }
	                }else{
	                    if($primaryInsuranceCoId>0){
	                        $pri_due=$proc_newBalance-$tot_pt_balance_proc;
	                    }
	                    if($for_sec_deduct == true){
	                        $pri_due=0;
	                        $sec_due=$proc_newBalance;
	                    }
	                    if($for_sec_deduct == false && $for_tri_deduct == true){
	                        $pri_due=0;
	                        $sec_due=0;
	                        $tri_due=$proc_newBalance;
	                    }
	                    if($for_pat_deduct == true){
	                        $tot_pt_balance_proc=$proc_newBalance;
	                        $pri_due=0;
	                        $sec_due=0;
	                        $tri_due=0;
	                    }
	                    
	                }
	                if($proc_selfpay>0 || $ref_amt>0){
	                    $tot_pt_balance_proc=$proc_newBalance;
	                    $pri_due=0;
	                    $sec_due=0;
	                    $tri_due=0;
	                }
	                if($pri_due<0){ $pri_due=0;}
	                if($sec_due<0){ $sec_due=0;}
	                if($tri_due<0){ $tri_due=0;}
	                if($proc_newBalance<=0){ $tot_pt_balance_proc=0;}
	                
	                if($proc_newBalance>0){
	                    $tot_due_amt=$pri_due+$sec_due+$tri_due+$tot_pt_balance_proc;
	                    $diff_due_amt=$tot_due_amt-$proc_newBalance;
	                    //echo $newBalance.'-'.$tot_due_amt.'-'.$pri_due.'-'.$sec_due.'-'.$pat_due;
	                    if($diff_due_amt!=0){
	                        if($pri_due>0){
	                            $pri_due=$pri_due-$diff_due_amt;
	                        }else if($sec_due>0){
	                            $sec_due=$sec_due-$diff_due_amt;
	                        }else if($tri_due>0){
	                            $tri_due=$tri_due-$diff_due_amt;
	                        }else if($tot_pt_balance_proc>0){
	                            $tot_pt_balance_proc=$tot_pt_balance_proc-$diff_due_amt;
	                        }
	                        if($pri_due<0){$pri_due=0;}
	                        if($sec_due<0){$sec_due=0;}
	                        if($tri_due<0){$tri_due=0;}
	                        if($tot_pt_balance_proc<0){$tot_pt_balance_proc=0;}
	                        if($pri_due==0 && $sec_due==0 && $tri_due==0){
	                            $tot_pt_balance_proc=$proc_newBalance;
	                        }
	                    }else{
	                        if($tot_pt_balance_proc>$proc_newBalance){
	                            $tot_pt_balance_proc=$proc_newBalance;
	                        }
	                        if($pri_due==0 && $sec_due==0 && $tri_due==0){
	                            $tot_pt_balance_proc=$proc_newBalance;
	                        }
	                    }
	                }else{
	                    $pri_due=0;
	                    $sec_due=0;
	                    $tri_due=0;
	                    $tot_pt_balance_proc=0;
	                }
	                
	                $len_TX=0;
	                $this->QUERY_STRING = "select charge_list_detail_id from tx_payments where charge_list_detail_id='$charge_list_detail_id' and del_status='0' limit 0,1";
	                $qryResTx = $this->mysqlifetchdata();
	                $len_TX = count($qryResTx);
	                if($len_TX==0){
	                    $up_qry="update patient_charge_list_details set pri_due=$pri_due,sec_due=$sec_due,tri_due=$tri_due,pat_due=$tot_pt_balance_proc where charge_list_detail_id ='$charge_list_detail_id'";
	                    imw_query($up_qry);
	                }
	                //echo $patient_id.'-'.$encounter_id.'<br>';
	            }
	        }
	    }
	}
}	

?>