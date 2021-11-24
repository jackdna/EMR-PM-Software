<?php
/**
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 * Use this software under MIT License
 * 
 * File: pnTempParser.php
 * Coded in PHP7
 * Purpose: This class provides Processing functions so that template tags are replaced with correct Pt data.
 * Access Type : Include file
 */

require_once(dirname(__FILE__)."/../functions_ptInfo.php");
class PnTempParser extends ChartAP{	
	private $db;
	private $kPtInfo;
	private $kChartLeftCcHx;
	private $kVision;
	private $kMr1Mr2Mr3;
	private $kPc1Pc2Pc3;
	private $kPupil;
	private $kExter;
	private $kLa;
	private $kLna;
	private $kIop;
	private $kGonio;
	private $kSle;
	private $kSli;
	private $kFundus;
	private $kFundusAll;
	private $kAP;
	private $kAPODOSOU;
	private $kEom;
	private $kCVF;	
	private $kDate;
	private $kPinhole;
	private $kTextBox;
	private $kMedHx;
	private $kVisPriIns;
	private $kVisSecIns;
	private $kPachy;
	private $kApptInfo;
	private $kCptMod;
	private $kArrTym;
	
	private $tPtInfo;
	private $tChartLeftCcHx;
	private $tVision;
	private $tMr1Mr2Mr3;
	private $tPc1Pc2Pc3;
	private $tPupil;
	private $tExter;
	private $tLa;
	private $tLna;
	private $tIop;
	private $tGonio;
	private $tSle;
	private $tSli;
	private $tkFundus;
	private $tFundusAll;
	private $tAP;
	private $tEom;	
	private $tCVF;	
	private $tOd;
	private $tOs;			
	private $tDate;			
	private $tPinhole;
	private $tTextBox;
	private $tMedHx;			
	private $tVisPriIns;
	private $tVisSecIns;
	private $tPachy;
	private $tApptInfo;
	private $tCptMod;
	private $tArrTym;
	
	function __construct(){
		
		//$this->db = $GLOBALS['adodb']['db'];
		
		$this->kPtInfo = array("{PatientID}","{PATIENT NAME TITLE}","{LAST NAME}","{MIDDLE NAME}","{PATIENT FIRST NAME}","{ADDRESS1}","{ADDRESS2}","{PATIENT CITY}","{STATE ZIP CODE}","{DOB}",
								"{REFFERING PHY.}","{MEDICAL DOCTOR}","{REF PHYSICIAN FIRST NAME}","{REF PHYSICIAN LAST NAME}","{PRIMARY PHYSICIAN}","{REF PHY SPECIALITY}",
								"{REF PHY PHONE}","{REF PHY STREET ADDR}","{REF PHY CITY}","{REF PHY STATE}","{REF PHY ZIP}","{REF PHY FAX}","{AGE}","{PCP City}","{PCP State}","{PCP ZIP}","{REF PHYSICIAN TITLE}","{PCP STREET ADDR}","{ADDRESSEE}","{ADDRESSEE_ADDRESS}","{ADDRESSEE_PHONE}","{ADDRESSEE_FAX}","{ADDRESSEE_SPECIALTY}","{CC1}","{CC1_ADDRESS}","{CC2}","{CC2_ADDRESS}","{CC3}","{CC3_ADDRESS}",
								"{SEX}","{DOB}","{PATIENT SS}","{MARITAL STATUS}","{HOME PHONE}","{EMERGENCY CONTACT}","{EMERGENCY CONTACT PH}","{MOBILE PHONE}","{WORK PHONE}","{PATIENT STATE}","{PATIENT ZIP}","{REGISTRATION DATE}","{POS FACILITY}","{DRIVING LICENSE}",
								"{RES.PARTY TITLE}","{RES.PARTY FIRST NAME}","{RES.PARTY MIDDLE NAME}","{RES.PARTY LAST NAME}","{RES.PARTY DOB}",
								"{RES.PARTY SS}","{RES.PARTY SEX}","{RES.PARTY RELATION}","{RES.PARTY ADDRESS1}","{RES.PARTY ADDRESS2}",
								"{RES.PARTY HOME PH.}","{RES.PARTY WORK PH.}","{RES.PARTY MOBILE PH.}","{RES.PARTY CITY}","{RES.PARTY STATE}",
								"{RES.PARTY ZIP}","{RES.PARTY MARITAL STATUS}","{RES.PARTY DD NUMBER}","{PATIENT OCCUPATION}",
								"{PATIENT EMPLOYER}","{OCCUPATION ADDRESS1}","{OCCUPATION ADDRESS2}","{OCCUPATION CITY}","{OCCUPATION STATE}",
								"{OCCUPATION ZIP}","{MONTHLY INCOME}","{PATIENT INITIAL}","{TIME}","{OPERATOR NAME}","{OPERATOR INITIAL}",
								"{HEARD ABOUT US}","{HEARD ABOUT US DETAIL}","{EMAIL ADDRESS}",/*"{USER DEFINE 1}","{USER DEFINE 2}",*/
								"{PRIMARY INSURANCE COMPANY}","{PRIMARY POLICY #}","{PRIMARY GROUP #}","{PRIMARY SUBSCRIBER NAME}",
								"{PRIMARY SUBSCRIBER RELATIONSHIP}","{PRIMARY BIRTHDATE}","{PRIMARY SOCIAL SECURITY}","{PRIMARY PHONE}",
								"{PRIMARY ADDRESS}","{PRIMARY CITY}","{PRIMARY STATE}","{PRIMARY ZIP}",
								"{PRIMARY EMPLOYER}","{SECONDARY INSURANCE COMPANY}","{SECONDARY POLICY #}","{SECONDARY GROUP #}",
								"{SECONDARY SUBSCRIBER NAME}","{SECONDARY SUBSCRIBER RELATIONSHIP}","{SECONDARY BIRTHDATE}","{SECONDARY SOCIAL SECURITY}",
								"{SECONDARY PHONE}","{SECONDARY ADDRESS}","{SECONDARY CITY}","{SECONDARY STATE}",
								"{SECONDARY ZIP}","{SECONDARY EMPLOYER}","{PATIENT MRN}","{PATIENT MRN2}","{RACE}","{LANGUAGE}","{ETHNICITY}","{PRI INS ADDR}","{SEC INS ADDR}",
								"{PCP PHY PHONE}", "{PCP PHY FAX}","{PATIENT_SS4}","{LOGGED_IN_FACILITY_NAME}","{LOGGED_IN_FACILITY_ADDRESS}","{PT-KEY}","{CO MANAGED PHY.}", "{SEX_S}", "{LOGGED_IN_FACILITY_FAX}", "{PCP FULL NAME}", "{PATIENT NAME SUFFIX}", "{PATIENT_NICK_NAME}"
							  
							  );

		$this->tPtInfo = array("Patient ID : ","Pt. Name Title : ","Pt. Last Name : ","Pt. Middle Initial : ","Pt. First Name : ","Pt. Address1 : ","Pt. Address2 : ","Pt. City : ",
								"Pt. State Zip Code : ","Pt. DOB : ","Referring Physician : ","PCP : ","Ref. Physician First Name : ","Ref. Physician Last Name : ",
								"Primary Physician : ","Ref. Phy Speciality :","Ref. Phy Phone :","Ref. Phy Address :","Ref. Phy City :","Ref. Phy State :","Ref. Phy Zip :","Ref. Phy Fax :","Pt. Age :","PCP City :","PCP State :","PCP Zip :","Ref. Physician Title : ","PCP Address :","Addressee :","Addressee Address :","Cc1 :","Cc1 Address :","Cc2 :","Cc2 Address :","Cc3 :","Cc3 Address :",
								"Patient Gender information : ","Patient Date of Birth : ","Patient SS Number : ","Patient Marital Status : ","Patient Home Phone : ","Patient Emergency Contact : ","Patient Emergency Contact Ph. : ","Patient Mobile Phone : ","Patient Work Phone : ","Patient State : ","Patient Zip : ","Registration Date : ","Facility : ","Driving Linciense : ",
								"Res. Party Title : ","Res. Party First Name : ","Res. Party Middle Name : ","Res. Party Last Name : ","Res. Party DOB : ",
								"Res. Party SS : ","Res. Party Gender Info. : ","Res. Party Relation : ","Res. Party Address1 : ","Res. Party Address2 : ",
								"Res. Party Home Ph. : ","Res. Party Work Ph. : ","Res. Party Mobile Ph. : ","Res. Party City : ","Res. Party State : ",
								"Res. Party Zip : ","Res. Party Marital Status : ","Res. Party DD Number","Patient Occupation : ",
								"Patient Employer : ","Patient Occupation Address1 : ","Patient Occupation Address2 : ","Patient Occupation City : ","Patient Occupation State : ",
								"Patient Occupation Zip : ","Monthly Income : ","Patient Initial : ","Current Time : ","Operator Name : ","Operator Initial",
							  	"Heard About Us : ","Heard About Us Detail : ","Patient E-mail : ",/*"User Defined 1 : ","User Defined 2 : ",*/
								"Primary Insurance company : ","Primary Policy # : ","Primary Group # : ","Primary Subscriber Name : ",
								"Primary Subscriber Relationship : ","Primary Birthdate : ","Primary Social Securtiy : ","Primary Phone : ",
								"Primary Address : ","Primary City : ","Primary State : ","Primary Zip : ",
								"Primary Employer : ","Secondary Insurance company : ","Secondary Policy : ","Secondary Group # : ",
								"Secondary Subscriber Name : ","Secondary Subscriber Relationship : ","Secondary Birthdate : ","Secondary Social Securtiy : ",
								"Secondary Phone : ","Secondary Address : ","Secondary City : ","Secondary State : ",
								"Secondary Zip : ","Secondary Employer : ","Patient MRN : ","Patient MRN2 : ","Race : ","Language : ","Ethnicity : ","Pri. Ins Address : ","Sec. Ins Address : ",
								"PCP Phy Phone :", "PCP Phy Fax :", "Patient Last 4 Digit SS", "", "", "Patient Key", "CO MANAGED PHYSICIAN :",  "Pt. Gender in Small letters :", "LOGGED IN FACILITY FAX", "PCP FULL NAME", "PATIENT NAME SUFFIX", "PATIENT NICK NAME"
							  
							  );
		$this->kChartLeftCcHx = array("{DOS}","{CC}","{HISTORY}","{PHYSICIAN NAME}","{PHYSICIAN SIGNATURE}","{SITE}","{PHYSICIAN FIRST NAME}","{PHYSICIAN MIDDLE NAME}","{PHYSICIAN LAST NAME}","{PHYSICIAN NAME SUFFIX}","{PHYSICIAN NPI}");
		$this->tChartLeftCcHx = array("Date Of Service : ","CC : ","HISTORY : ","Physician : ","Physician Signature : ","Patient Site : ","Physician First Name : ","Physician Middle Name : ","Physician Last Name : ","Physician Name Suffix : ","Physician NPI: ");
		
		$this->kVision = array("{DISTANCE}","{V-CC-OD}","{V-CC-OS}","{V-SC-OD}","{V-SC-OS}","{NEAR_OD}","{NEAR_OS}","{KERATOMETRY OD}",	"{KERATOMETRY OS}","{V-CC-OU}","{V-SC-OU}","{NEAR_OU}","{PAM_OD}","{PAM_OS}","{PAM_OU}","{BAT_OD}","{BAT_OS}","{BAT_OU}","{AR_OD}","{AR_OS}","{CYCLOPLEGIC_OD}","{CYCLOPLEGIC_OS}","{SC_DVA_OD}", "{SC_DVA_OS}", "{CC_DVA_OD}", "{CC_DVA_OS}", "{SC_NVA_OD}", "{SC_NVA_OS}", "{CC_NVA_OD}","{CC_NVA_OS}","{PINHOLE_OD_1}","{PINHOLE_OS_1}");
		$this->tVision = array("Distance : ","Visual Acuity - OD : ","Visual Acuity - OS : ","V-SC-OD :","V-SC-OS :","NEAR OD :","NEAR OS :","KERATOMETRY OD :","KERATOMETRY OS :","VA Corrected- OU : ","VA Uncorrected - OU : ","NEAR OU : ","PAM OD :","PAM OS :","PAM OU :","BAT OD :","BAT OS :","BAT OU :","AUTOREFRACTION OD :","AUTOREFRACTION OS :","CYCLOPLEGIC OD :","CYCLOPLEGIC OS :","SC DVA OD :","SC DVA OS :", "CC DVA OD :","CC DVA OS :", "SC NVA OD :", "SC NVA OS :", "CC NVA OD :", "CC NVA OS :", "PINHOLE OD 1", "PINHOLE OS 1");	

		$this->kMr1Mr2Mr3 = array("{MR1}","{MR2}","{MR3}","{MR1 GLARE OD}","{MR1 GLARE OS}","{MR2 GLARE OD}","{MR2 GLARE OS}","{MR3 GLARE OD}","{MR3 GLARE OS}");
		$this->tMr1Mr2Mr3 = array("MR1 : ","MR2 : ","MR3 : ","MR1 GLARE OD : ","MR1 GLARE OS : ","MR2 GLARE OD : ","MR2 GLARE OS : ","MR3 GLARE OD : ","MR3 GLARE OS : ");	
		
		$this->kPc1Pc2Pc3 = array("{PC1}","{PC2}","{PC3}");
		$this->tPc1Pc2Pc3 = array("PC1 : ","PC2 : ","PC3 : ");	
		
		
		$this->kPupil = array("{PUPIL OU}","{PUPIL OD}","{PUPIL OS}");
		$this->tPupil = array("Pupil OU : ","Pupil OD : ","Pupil OS : ");
		
		$this->kExter = array("{EXTERNAL OU}","{EXTERNAL OD}","{EXTERNAL OS}");
		$this->tExter = array("External OU : ","External OD : ","External OS : ");
		
 		$this->kLa = array("{L&amp;A OU}","{L&amp;A OD}","{L&amp;A OS}");
		$this->tLa = array("L &amp; A OU : ","L &amp; A OD : ","L &amp; A OS : ");
		
		$this->kLna = array("{L&amp;A ALL}");
		$this->tLna = array("L &amp; A ALL: ");
				
		$this->kIop = array("{IOP}", "{IOP OD}","{IOP OS}","{INITIAL IOP}","{INITIAL IOP TIME}","{POST IOP}","{POST IOP TIME}","{IOP OD WITHOUT PACHY}","{IOP OS WITHOUT PACHY}","{GFS_TARGET_IOP_OD}","{GFS_TARGET_IOP_OS}");
		$this->tIop = array("IOP:","IOP OD : ","IOP OS : ","Initial IOP : ","Initial IOP Time : ","Post IOP : ","Post IOP Time : ","IOP OD WITHOUT PACHY : ","IOP OS WITHOUT PACHY: ","GFS TARGET IOP OD:","GFS TARGET IOP OS:");
		
		$this->kGonio = array("{GONIO OU}","{GONIO OD}","{GONIO OS}");
		$this->tGonio = array("Gonio OU : ","Gonio OD : ","Gonio OS : ");		

		$this->kSle = array("{SLE OU}","{SLE OD}","{SLE OS}","{SLE}","{CONJ_OD}","{CONJ_OS}","{CORNEA_OD}","{CORNEA_OS}","{ANTCHAMBER_OD}","{ANTCHAMBER_OS}","{LENS_OD}","{LENS_OS}","{IRIS_OD}","{IRIS_OS}");
		$this->tSle = array("SLE OU : ","SLE OD : ","SLE OS : ","SLE : ","CONJ OD : ","CONJ OS : ","CORNEA OD : ","CONRNEA OS : ","ANTCHAMBER OD : ","ANTCHAMBER OS : ","LENS OD : ","LENS OS : ","IRIS OD :","IRIS OS :");
		
		$this->kSli = array("{SLE ALL}");
		$this->tSli = array("SLE ALL : ");
		
		
		$this->kFundus = array("{FUNDUS EXAM OU}","{FUNDUS EXAM OD}","{FUNDUS EXAM OS}","{FUNDUS}","{OPTICNERVE_OD}","{OPTICNERVE_OS}","{MACULA_OD}","{MACULA_OS}","{RETINAL_EX_OD}","{RETINAL_EX_OS}","{VITREOUS_OD}","{VITREOUS_OS}","{PERIPHERY_OD}","{PERIPHERY_OS}","{BV_OD}","{BV_OS}","{C:D_OD}","{C:D_OS}");
		$this->tFundus = array("Fundus Exam OU : ","Fundus Exam OD : ","Fundus Exam OS : ","Fundus : ","OPTICNERVE OD : ","OPTICNERVE OS : ","MACULA OD : ","MACULA OS : ","RETINAL EX OD : ","RETINAL EX OS : ","VITREOUS OD : ","VITREOUS OS : ","PERIPHERY OD : ","PERIPHERY OS : ","BV OD : ","BV OS : ","CUP DISC OD :","CUP DISC OS :");
		$this->kFundusAll = array("{FUNDUS ALL}");
		$this->tFundusAll = array("FUNDUS ALL :");		
		
		$this->kAP = array(	
						"{ASSESSMENT ALL}","{PLAN ALL}","{FOLLOW-UP}","{ASSESSMENT &amp; PLAN}",
						"{ASSESSMENT 1}","{ASSESSMENT 2}","{ASSESSMENT 3}","{ASSESSMENT 4}","{ASSESSMENT 5}",
						"{PLAN 1}","{PLAN 2}","{PLAN 3}","{PLAN 4}","{PLAN 5}","{A &amp; P}","{SCRIBED BY}","{A &amp; P_V}"
					);
		$this->kAPODOSOU = array(	
						"{ASSESSMENT OD}","{ASSESSMENT OS}","{PLAN OD}","{PLAN OS}");
		
		
		$this->tAP = array(
						"ASSESSMENT ALL : ","PLAN ALL : ","FOLLOW - UP : ","Assessment &amp; Plan : ",
						"Assessment 1 : ","Assessment 2 : ","Assessment 3 : ","Assessment 4 : ","Assessment 5 : ",
						"Plan 1 : ","Plan 2 : ","Plan 3 : ","Plan 4 : ","Plan 5 : ","A &amp; P : ","Scribed By : ","A &amp; P_V : "
					);
					
		$this->kEom = array("{EOM}","{GLARE}");
		$this->tEom = array("EOM : ","Glare : ");
		
		$this->kCVF = array("{CVF OD}","{CVF OS}");
		$this->tCVF = array("CVF OD :","CVF OS :");
		
		$this->tOd = "<span style=\"font-weight:bold;color:#0000CC;\">OD&nbsp;</span>";
		$this->tOs = "<span style=\"font-weight:bold;color:#006600;\">OS&nbsp;</span>";
		
		$this->kDate = array("{Date}","{DATE_F}");
		$this->tDate = array("Date : ","Date Format : ");
		
		$this->kPinhole = array("{PINHOLE OD}","{PINHOLE OS}");
		$this->tPinhole = array("Pinhole OD : ","Pinhole OS : ");		
		
		$this->kTextBox = array("{TEXTBOX_SMALL}","{TEXTBOX_MEDIUM}");
		$this->tTextBox = array("","");	
		
		$this->kMedHx = array("{OCULAR HISTORY}","{VITAL SIGN}","{OCULAR MEDICATION}","{SYSTEMIC MEDICATION}","{PATIENT ALLERGIES}","{MEDICAL PROBLEM}","{MED HX}","{DIABETES}","{FLASHES}","{FLOATERS}","{SMOKER}","{FAMILY HX SMOKE}","{EMP ID}","{GENERAL HEALTH}","{OCULAR_OTHER}","{ROS_SUMMARY}","{ROS_DETAIL}","{ROS_ALLERGICIMMUNOLOGIC}","{ROS_CARDIOVASCULAR}","{ROS_CONSTITUTIONAL}","{ROS_EARNOSEMOUTHTHROAT}","{ROS_ENDOCRINE}","{ROS_EYES}","{ROS_GASTROINTESTINAL}","{ROS_GENITOURINARY}","{ROS_HEMOTOLOGICLYMPHATIC}","{ROS_INTEGUMENTARY}","{ROS_MUSCULOSKELETAL}","{ROS_NEUROLOGICAL}","{ROS_PSYCHIATRIC}","{ROS_RESPIRATORY}","{OCULAR_MED_WITH_LOT}","{SYSTEMIC_MED_WITH_LOT}");
		$this->tMedHx = array("Ocular History : ","Vital Sign : ","Ocular Medication : ","Systemic Medication : ","Patient Allergies : ","Medical Problem : ","Medical History : ","Diabetes : ","Flashes : ","Floaters : ","Smoker : ","Family Hx Smoke : ","EMP ID : ","GENERAL HEALTH","OCULAR OTHER","ROS SUMMARY","ROS DETAIL","ROS ALLERGIC/IMMUNOLOGIC","ROS CARDIOVASCULAR","ROS CONSTITUTIONAL","ROS EAR,NOSE,MOUTH,THROAT","ROS ENDOCRINE","ROS EYES","ROS GASTROINTESTINAL","ROS GENITOURINARY","ROS HEMOTOLOGIC/LYMPHATIC","ROS INTEGUMENTARY","ROS MUSCULOSKELETAL","ROS NEUROLOGICAL","ROS PSYCHIATRIC","ROS RESPIRATORY","Ocular Medication With Lot","Systemic Medication With Lot");	
		
		$this->kVisPriIns = array(
								"{VISION PRIMARY INSURANCE COMPANY}", "{VISION PRIMARY POLICY #}", "{VISION PRIMARY GROUP #}","{VISION PRIMARY SUBSCRIBER NAME}","{VISION PRIMARY SUBSCRIBER RELATIONSHIP}","{VISION PRIMARY BIRTHDATE}","{VISION PRIMARY SOCIAL SECURITY}","{VISION PRIMARY PHONE}","{VISION PRIMARY ADDRESS}","{VISION PRIMARY CITY}","{VISION PRIMARY STATE}","{VISION PRIMARY ZIP}","{VISION PRIMARY EMPLOYER}"
								);
		
		$this->tVisPriIns = array(
								"VISION PRIMARY INSURANCE COMPANY:","VISION PRIMARY POLICY #:","VISION PRIMARY GROUP #:","VISION PRIMARY SUBSCRIBER NAME:","VISION PRIMARY SUBSCRIBER RELATIONSHIP:","VISION PRIMARY BIRTHDATE:","VISION PRIMARY SOCIAL SECURITY:","VISION PRIMARY PHONE:","VISION PRIMARY ADDRESS:","VISION PRIMARY CITY:","VISION PRIMARY STATE:","VISION PRIMARY ZIP:","VISION PRIMARY EMPLOYER:"
								);
		$this->kVisSecIns = array(
								"{VISION SECONDARY INSURANCE COMPANY}","{VISION SECONDARY POLICY #}","{VISION SECONDARY GROUP #}","{VISION SECONDARY SUBSCRIBER NAME}","{VISION SECONDARY SUBSCRIBER RELATIONSHIP}","{VISION SECONDARY BIRTHDATE}","{VISION SECONDARY SOCIAL SECURITY}","{VISION SECONDARY PHONE}","{VISION SECONDARY ADDRESS}","{VISION SECONDARY CITY}","{VISION SECONDARY STATE}","{VISION SECONDARY ZIP}","{VISION SECONDARY EMPLOYER}","{VISION PRI INS ADDR}","{VISION SEC INS ADDR}"
								);
		
		$this->tVisSecIns = array(
								"VISION SECONDARY INSURANCE COMPANY:","VISION SECONDARY POLICY #:","VISION SECONDARY GROUP #:","VISION SECONDARY SUBSCRIBER NAME:","VISION SECONDARY SUBSCRIBER RELATIONSHIP:","VISION SECONDARY BIRTHDATE:","VISION SECONDARY SOCIAL SECURITY:","VISION SECONDARY PHONE:","VISION SECONDARY ADDRESS:","VISION SECONDARY CITY:","VISION SECONDARY STATE:","VISION SECONDARY ZIP:","VISION SECONDARY EMPLOYER:","VISION PRI INS ADDR:","VISION SEC INS ADDR:"
								);	
		$this->kPachy = array("{PACHYMETRY OD}","{PACHYMETRY OS}");
		$this->tPachy = array("PACHYMETRY OD : ","PACHYMETRY OS : ");						
		
		$this->kApptInfo = array(
								"{PATIENT_NEXT_APPOINTMENT_DATE}","{PATIENT_NEXT_APPOINTMENT_TIME}","{PATIENT_NEXT_APPOINTMENT_PROVIDER}",
								"{PATIENT_NEXT_APPOINTMENT_LOCATION}","{PATIENT_NEXT_APPOINTMENT_PRIREASON}","{PATIENT_NEXT_APPOINTMENT_SECREASON}",
								"{PATIENT_NEXT_APPOINTMENT_TERREASON}"
								);
		$this->tApptInfo = array(
								"Patient Next Appointment Date : "," Patient Next Appointment Time : "," Patient Next Appointment Provider : ",
								"Patient Next Appointment Location : ","Patient Next Appointment Primary Reason : ",
								"Patient Next Appointment Secondary Reason : ","Patient Next Appointment Secondary Reason : ","Patient Next Appointment Tertiary Reason : "
								);						
		$this->kCptMod = array("{CPT_MOD}");
		$this->tCptMod = array("CPT MOD ");	
		
		$this->kArrTym = array("{ARRIVAL_TIME}");
		$this->tArrTym = array("Arrival Time ");		
		
	}
	
	//-----CHECK FOR KEY THAT EXITS---------------
	private function isKeyExist($str, $arr){
		if(count($arr) > 0){
			foreach($arr as $key => $var){
				if(stripos($str, $var) !== false){
					return 1;
					break;
				}
			}
		}
		return 0;
	}
	
	//-----REFINE DATA---------------------------
	private function refineData($str, $arrSrch,$arrRep=array(),$arrTitle,$defRep="Not Performed"){
		foreach($arrSrch as $key => $val){
			if(stripos($str,$val) !== false){
				$tmp = (!empty($arrRep[$key])) ? $arrRep[$key] : $defRep;
				
				//-----FIX FOR BREAKING HTML TAGS DUE TO < OR  > SIGNS IN DATA-------------------
				$find = array("<OD", "<od", ">OD", ">od", "<OS", "<os", ">OS", ">os");
				$replace = array("< OD", "< od", "> OD", "> od", "< OS", "< os", "> OS", "> os");
				$tmp = str_replace($find,$replace,$tmp);
				//-----END FIX FOR BREAKING HTML TAGS DUE TO LT GT ARROWS------------------------
				
				if(!empty($arrRep[$key])){ 
					
					//-IF VALUES EXISTS THEN REPLACE---------------------------------------------
					//-TO REMOVE THE EXTRA </BR> IN LAST LINE------------------------------------
					if(substr($tmp,-5)=="</br>" || substr($tmp,-5)=="<br/>"){
						$StrLen=strlen($tmp);
						$tmp=substr($tmp,0,($StrLen-5));
					}
					if(substr($tmp,-6)=="<br />"){
						$StrLen=strlen($tmp);
						$tmp=substr($tmp,0,($StrLen-6));
					}
					
					if(strpos($tmp, "&lt;")!==false && strpos($tmp, "&lt; ")===false){ /*Fix Breaking html tags in editor*/
						$tmp = str_replace("&lt;", " &lt; ", $tmp);
					}
					$str = str_ireplace($val,$tmp,$str);
				}else{
					//Check For label and key word to remove both
					$pattern = '/\d.\s/';
					$ptrn = "/".$arrTitle[$key]."\s*".$val."/i";
					$ptrn = str_ireplace(" ","\s*",$ptrn);
					if(preg_match($ptrn,$str,$matches)){
						//Replace with ""
						$str = preg_replace($ptrn,"",$str);
					}else{
						
						//if($val == "{ASSESSMENT ALL}"){							
							//$str = str_replace($arrTitle[$key],"",$str);
						//}
						if(strpos($tmp, "&lt;")!==false && strpos($tmp, " &lt; ")===false){ /*Fix Breaking html tags in editor*/
							$tmp = str_replace("&lt;", " &lt; ", $tmp);
						}
						$str = str_ireplace($val,$tmp,$str);
					}
				}
				
			}else{
				//echo "<xmp>NO FOUND ".$val."</xmp>";
			}
		}
		return $str;
	}
	
	private function getGroupInfo($s){
		//Group--
		$sql = "select name, group_Address1, group_Address2, group_Zip, group_City, group_State, group_Telephone, group_Email from groups_new where del_status='0' order by gro_id limit 0,1 ";
		$rowg=sqlQuery($sql);		
		if($rowg!=false){			
			$grp_info["name"] = $rowg["name"];
			if(!empty($rowg["group_Address1"])) { $grp_info["address"] .= $rowg["group_Address1"].", "; }
			if(!empty($rowg["group_Address2"])) { $grp_info["address"] .= $rowg["group_Address2"].", "; }
			if(!empty($rowg["group_Zip"])) { $grp_info["address"] .= $rowg["group_Zip"].", "; }
			if(!empty($rowg["group_City"])) { $grp_info["address"] .= $rowg["group_City"].", "; }
			if(!empty($rowg["group_State"])) { $grp_info["address"] .= $rowg["group_State"]." "; }
			if(!empty($rowg["group_Telephone"])) { $grp_info["address"] .= "<br/>Ph: ".$rowg["group_Telephone"]; }
			if(!empty($rowg["group_Email"])) { $grp_info["address"] .= "<br/>Email: ".$rowg["group_Email"]; }
		}
		
		$s = str_replace("{GROUP NAME}", $grp_info["name"], $s);
		$s = str_replace("{GROUP ADDRESS}", $grp_info["address"], $s);
		
		return $s;
	}
	
	//Get PtInfo
	private function getPtInfo($str, $ptId,$patientRefTo,$addresseRefToId,$cc1RefToId,$cc2RefToId,$cc3RefToId,$ptDocReport="",$insCaseTypeId){
		
		$priInsCaseType= $secInsCaseType="";
		
		//$insCaseTypeId IS COMING FROM SCHEDULER FACESHEET, DOCS TAB=>PT-DOC AND REPORTS =>PATIENT DOCUMENT REPORT BASED ON PT. APPOINTMENT
		if((!empty($ptDocReport) && ($ptDocReport=="fromReports" || $ptDocReport=="fromDocsTabPTDoc"))
			&& ($insCaseTypeId>0))
		{ 
			$priInsCaseType = "AND priIns.ins_caseid= '".$insCaseTypeId."'";  //$_SESSION['currentCaseid'] => OLD CODE
			$secInsCaseType = "AND secIns.ins_caseid= '".$insCaseTypeId."'"; //$_SESSION['currentCaseid']  => OLD CODE
		}
		else
		{ 
			//ELSE CASE IS USED FOR DOCS TAB => PT-DOC TEMPLATES.
			//IF PATIENT HAVE NO APPOINTMENT THEN DEFAULT IT WILL DISPLAY DEFAULT ACTIVE 'MEDICAL' INSURANCE	
			$insCaseQry = "SELECT ins_caseid AS insCaseTypeId FROM `insurance_case` WHERE patient_id='".$ptId."' 
							AND ins_case_type='9' AND case_status='Open'";  
			$insCaseRes = imw_query($insCaseQry);
			if(imw_num_rows($insCaseRes)>0)
			{
				$insCaseRow =imw_fetch_assoc($insCaseRes);
				$insCaseTypeId = $insCaseRow['insCaseTypeId'];
			}
			if(!empty($insCaseTypeId))
			{
				$priInsCaseType = "AND priIns.ins_caseid= '".$insCaseTypeId."'";
				$secInsCaseType = "AND secIns.ins_caseid= '".$insCaseTypeId."'";
			}	
		}
		
		$sql = "SELECT ".
			 "patient_data.id,patient_data.title, patient_data.fname, patient_data.lname, ".
			 "patient_data.mname, patient_data.DOB, patient_data.street, ".
			 "patient_data.street2, patient_data.postal_code, patient_data.city, patient_data.state, ".
			 "DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(patient_data.dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(patient_data.dob, '00-%m-%d')) AS ptAge,". 
			 "patient_data.primary_care_id as ptRfPhyId, patient_data.primary_care_phy_id as ptPCPPhyId, ".
			 "patient_data.sex, patient_data.ss, patient_data.status, patient_data.phone_home, ".
			 "patient_data.contact_relationship, patient_data.phone_contact, patient_data.phone_cell, patient_data.phone_biz, ".
			 "date_format(patient_data.date,'".get_sql_date_format()."') as reg_date, patient_data.driving_licence,patient_data.occupation as pt_occupation, ".
			 "patient_data.monthly_income,CONCAT_WS('',SUBSTRING(patient_data.fname,1,1),SUBSTRING(patient_data.lname,1,1)) as patient_initial, ".
			 "patient_data.heard_abt_us, patient_data.heard_abt_desc, patient_data.email as pt_email, patient_data.genericval1, patient_data.genericval2,". 
			 "patient_data.External_MRN_1, patient_data.External_MRN_2, patient_data.race, patient_data.otherRace, patient_data.language, patient_data.ethnicity, patient_data.otherEthnicity, patient_data.temp_key as pt_key,  patient_data.co_man_phy  as co_managed_phy, patient_data.suffix  as patient_suffix, patient_data.nick_name  as patient_nick_name, ".
			 "heard_about_us.heard_options AS heard_about_name,".
			 
			 "rf.Title AS rp_title,rf.credential AS rf_credential, rf.FirstName , rf.MiddleName, rf.LastName, ".
			 "rf.Address1, rf.Address2, rf.ZipCode, rf.City AS rp_city, rf.physician_fax AS rp_fax, ".			 
			 "rf.State AS rp_state,rf.specialty AS rp_specialty,rf.physician_phone AS rp_physician_phone, ".
			 "CONCAT_WS(' ',us.fname,us.lname) as physicianName,patient_data.providerID as ptProID, ".
			 "general_medicine.med_doctor, ".			 
			 
			 "pcp.Title AS pcp_title, pcp.FirstName AS pcp_firstName , pcp.MiddleName AS pcp_middleName, pcp.LastName AS pcp_lastName, ".
			 "pcp.Address1 AS pcp_address1, pcp.Address2 AS pcp_address2, pcp.ZipCode AS pcp_zipCode, pcp.City AS pcp_city, ".			 
			 "pcp.State AS pcp_state,pcp.specialty AS pcp_specialty,pcp.physician_phone AS pcp_physician_phone, ".

			 "resp.title as resp_title,resp.fname as resp_fname,resp.mname as resp_mname,resp.lname as resp_lname, ".
			 "date_format(resp.dob,'".get_sql_date_format()."') as resp_dob,resp.ss as resp_ss,resp.sex as resp_sex, ".
			 "resp.relation as resp_relation, resp.address as resp_address,resp.address2 as resp_address2, ".
			 "resp.home_ph as resp_home_ph,resp.work_ph as resp_work_ph,resp.mobile as resp_mobile,resp.city as resp_city, ".
			 "resp.state as resp_state,resp.zip as resp_zip,resp.marital as resp_marital,resp.licence as resp_licence, ".
			 
			 "empdata.name as empdata_name,empdata.street as empdata_street,empdata.street2 as empdata_street2,empdata.city as empdata_city, ".
			 "empdata.state as empdata_state,empdata.postal_code as empdata_postal_code, ".
			 
			 "priIns.provider as priInsComp,priIns.policy_number as priPolicyNumber,priIns.group_number as priGroupNumber, ".
			 "CONCAT(priIns.subscriber_fname,'&nbsp;',priIns.subscriber_lname)as priSubscriberName,priIns.subscriber_relationship as priSubscriberRelation, ".
			 "date_format(priIns.subscriber_DOB,'".get_sql_date_format()."') as priSubscriberDOB,priIns.subscriber_ss as priSubscriberSS,priIns.subscriber_phone as priSubscriberPhone, ".
			 "priIns.subscriber_street as priSubscriberStreet,priIns.subscriber_city as priSubscriberCity,priIns.subscriber_state as priSubscriberState, ".
			 "priIns.subscriber_postal_code as priSubscriberZip,priIns.subscriber_employer as priSubscriberEmployer, ".
			 
			 "secIns.provider as secInsComp, secIns.policy_number as secPolicyNumber,secIns.group_number as secGroupNumber, ".
			 "CONCAT(secIns.subscriber_fname,'&nbsp;',secIns.subscriber_lname)as secSubscriberName,secIns.subscriber_relationship as secSubscriberRelation, ".
			 "date_format(secIns.subscriber_DOB,'".get_sql_date_format()."') as secSubscriberDOB,secIns.subscriber_ss as secSubscriberSS,secIns.subscriber_phone as secSubscriberPhone, ".
			 "secIns.subscriber_street as secSubscriberStreet,secIns.subscriber_city as secSubscriberCity,secIns.subscriber_state as secSubscriberState, ".
			 "secIns.subscriber_postal_code as secSubscriberZip,secIns.subscriber_employer as secSubscriberEmployer, ".
			
			 "priInCmp.name as priInsCompName, CONCAT(priInCmp.contact_address,if(TRIM(priInCmp.city)!='',CONCAT(' ',priInCmp.city),''),if(TRIM(priInCmp.State)!='',CONCAT(', ',priInCmp.State),''),if(TRIM(priInCmp.Zip)!='',CONCAT(' ',priInCmp.Zip),''),if(TRIM(priInCmp.zip_ext)!='',CONCAT('-',priInCmp.zip_ext),'')) AS priInsCompAddress, ".
			 "secInCmp.name as secInsCompName, CONCAT(secInCmp.contact_address,if(TRIM(secInCmp.city)!='',CONCAT(' ',secInCmp.city),''),if(TRIM(secInCmp.State)!='',CONCAT(', ',secInCmp.State),''),if(TRIM(secInCmp.Zip)!='',CONCAT(' ',secInCmp.Zip),''),if(TRIM(secInCmp.zip_ext)!='',CONCAT('-',secInCmp.zip_ext),'')) AS secInsCompAddress, ".
			 
			 " pos_fac.facilityPracCode AS pos_facility_name, ".
			 
			 " pos_t.pos_code as pos_code ".
			 
			 "FROM patient_data ".
			 "LEFT JOIN heard_about_us ON patient_data.heard_abt_us = heard_about_us.heard_id ".
			 "LEFT JOIN refferphysician rf ON rf.physician_Reffer_id = patient_data.primary_care_id ".
			 "LEFT JOIN refferphysician pcp ON pcp.physician_Reffer_id = patient_data.primary_care_phy_id ".
			 "LEFT JOIN general_medicine ON general_medicine.patient_id = patient_data.id ".
			 "LEFT JOIN employer_data empdata ON empdata.pid = patient_data.id ".
			 "LEFT JOIN resp_party resp ON resp.patient_id = patient_data.id ".
			 
			 "LEFT JOIN insurance_data priIns ON (priIns.pid = patient_data.id AND priIns.ins_caseid > 0 AND priIns.type = 'primary' AND priIns.actInsComp = 1 AND priIns.provider > 0 $priInsCaseType) ".
			 "LEFT JOIN insurance_companies priInCmp ON (priInCmp.id = priIns.provider AND priIns.pid = patient_data.id) ".

			 "LEFT JOIN insurance_data secIns ON (secIns.pid = patient_data.id AND secIns.ins_caseid > 0 AND secIns.type = 'secondary' AND secIns.actInsComp = 1 AND secIns.provider > 0 $secInsCaseType)".
			 "LEFT JOIN insurance_companies secInCmp ON (secInCmp.id = secIns.provider AND secIns.pid = patient_data.id) ".
			 
			 "LEFT JOIN pos_facilityies_tbl pos_fac ON pos_fac.pos_facility_id = patient_data.default_facility ".
			 "LEFT JOIN pos_tbl pos_t ON pos_fac.pos_id = pos_t.pos_id ".
			 "LEFT JOIN users us ON us.id = patient_data.providerID ".			 
			 "WHERE patient_data.id='".$ptId."' ";
		$res = sqlQuery($sql) ;		
		if($res !== false){
			//Pt Info			
			$ptAge = "";
			$patientId = $res["id"];
			$title = $res["title"];
			$fname = $res["fname"];
			$lname = $res["lname"];
			$mname = $res["mname"];
			$pt_key= $res["pt_key"];
			$coManagedPhy= $res["co_managed_phy"]; 
			//$dob = $res->UserDate($res["DOB"],'m-d-Y');
			$dob = wv_formatDate($res["DOB"]);
			$street = $res["street"];
			$street2 = $res["street2"];
			$zip = $res["postal_code"];
			$city = $res["city"];
			$state = $res["state"];
			$patient_suffix = $res["patient_suffix"];
			if(strlen($res['ptAge']) != 4){
				$ptAge = $res["ptAge"];
				if($ptAge != ""){
					$ptAge .= "&nbsp;Yr.";
				}
			}
			$physicianName = $res["physicianName"];
			$ptProID = $res["ptProID"];
			
			//new variable added
			$ptSex 					= $res["sex"];	
			$ptSex_s 				= strtolower($res["sex"]);	//gender in small letter
			//$dob;
			$ptSS 					= $res["ss"];	
			if(trim($ptSS)!==""){
				$ptss_last4digit	= substr_replace($ptSS,'XXX-XX',0,6); 
			}else{
				$ptss_last4digit="";
			}
			$ptMaritalStatus 		= $res["status"];	
			$ptHomePhone 			= core_phone_format($res["phone_home"]);
			$ptEmergencyContact 	= $res["contact_relationship"];	
			$ptEmergencyContactPhone= $res["phone_contact"];	
			$ptMobilePhone 			= $res["phone_cell"];	
			$ptWorkPhone 			= $res["phone_biz"];
			$patient_nick_name 		= $res["patient_nick_name"];
			//$state;	
			//$zip;
			$ptRegDate 				= $res["reg_date"];	
			$pos_facility_name 		= $res["pos_facility_name"]."-".$res["pos_code"];
			$ptDrivingLicence 		= $res["driving_licence"];
			$pt_occupation 			= $res["pt_occupation"];
			
			$empdata_name 			= $res["empdata_name"];
			$empdata_street 		= $res["empdata_street"];
			$empdata_street2 		= $res["empdata_street2"];
			$empdata_city 			= $res["empdata_city"];
			$empdata_state 			= $res["empdata_state"];
			$empdata_postal_code 	= $res["empdata_postal_code"];

			$monthly_income 		= $res["monthly_income"];
			$patient_initial 		= $res["patient_initial"];
			$currTm					= date('h:i A');
			$heard_abt_us 			= $res["heard_about_name"];
			$heard_abt_desc 		= $res["heard_abt_desc"];
			$pt_email 				= $res["pt_email"];
			$pt_email				= str_ireplace("&lt;","[",$pt_email);
			$pt_email				= str_ireplace("&gt;","]",$pt_email);
			
			$genericval1 			= $res["genericval1"];
			$genericval2 			= $res["genericval2"];
			$External_MRN_1			= $res["External_MRN_1"];
			$External_MRN_2			= $res["External_MRN_2"];
			
			$raceShow				= trim($res["race"]);
			$otherRace				= trim($res["otherRace"]);
			if($otherRace) { 
				$raceShow			= $otherRace;
			}
			$language				= str_ireplace("Other -- ","",$res["language"]);
			$ethnicityShow			= trim($res["ethnicity"]);			
			$otherEthnicity			= trim($res["otherEthnicity"]);
			if($otherEthnicity) { 
				$ethnicityShow		= $otherEthnicity;
			}
			//==============RESPONSIBLE PARTY DATA REPLACEMENT=============================
			//IF PATIENT HAVE NO RESPONSIBLE PERSON THEN PATIENT DATA WILL BE REPLACED WITH RESPONSIBLE VARIABLES.
			
			if(!empty($res["resp_title"]) || !empty($res["resp_fname"]) || !empty($res["resp_mname"]) || !empty($res["resp_lname"]) || !empty($res["resp_dob"]) || !empty($res["resp_ss"]) || !empty($res["resp_sex"]) || !empty($res[0]['resp_relation']) || !empty($res["resp_address"]) || !empty($res["resp_address2"]) || !empty($res["resp_home_ph"]) || !empty($res["resp_mobile"]) || !empty($res["resp_city"]) || !empty($res["resp_state"]) || !empty($res["resp_zip"]) || !empty($res["resp_marital"]) || !empty($res["resp_licence"])){
				
			  $resp_title 			= $res["resp_title"];
			  $resp_fname 			= $res["resp_fname"];
			  $resp_mname 			= $res["resp_mname"];
			  $resp_lname 			= $res["resp_lname"];
			  $resp_dob 			= $res["resp_dob"];
			  $resp_ss 				= $res["resp_ss"];
			  $resp_sex 			= $res["resp_sex"];
			  $resp_relation 		= $res[0]['resp_relation'];
			  if(strtolower($resp_relation) == "doughter"){
				  $resp_relation = "Daughter";
			  }
			  $resp_address 			= $res["resp_address"];
			  $resp_address2 			= $res["resp_address2"];
			  $resp_home_ph 			= $res["resp_home_ph"];
			  $resp_work_ph 			= $res["resp_work_ph"];
			  $resp_mobile 				= $res["resp_mobile"];
			  $resp_city 				= $res["resp_city"];
			  $resp_state 				= $res["resp_state"];
			  $resp_zip 				= $res["resp_zip"];
			  $resp_marital 			= $res["resp_marital"];
			  $resp_licence 			= $res["resp_licence"];
			
			}else{
			
			  $resp_title 				= $res["title"];
			  $resp_fname 				= $res["fname"];
			  $resp_mname 				= $res["mname"];
			  $resp_lname 				= $res["lname"];
			  $resp_dob 				= wv_formatDate($res["DOB"]);
			  $resp_ss 					= $res["ss"];
			  $resp_sex 				= $res["sex"];
			  $resp_relation 			= 'Self';
			  $resp_address 			= $res["street"];
			  $resp_address2 			= $res["street2"];
			  $resp_home_ph 			= $res["phone_home"];
			  $resp_work_ph 			= $res["phone_biz"];
			  $resp_mobile 				= $res["phone_cell"];
			  $resp_city 				= $res["city"];
			  $resp_state 				= $res["state"];
			  $resp_zip 				= $res["postal_code"];
			  $resp_marital 			= $res["status"];
			  $resp_licence 			= $res["driving_licence"];
			}
			//============================THE END OF RESPONSIBLE PARTY WORK=======================================
			
			$priInsComp 			= $res["priInsComp"];
			$priPolicyNumber 		= $res["priPolicyNumber"];
			$priGroupNumber 		= $res["priGroupNumber"];
			$priSubscriberName 		= $res["priSubscriberName"];
			$priSubscriberRelation 	= $res["priSubscriberRelation"];
			$priSubscriberDOB 		= $res["priSubscriberDOB"];
			$priSubscriberSS 		= $res["priSubscriberSS"];
			$priSubscriberPhone 	= $res["priSubscriberPhone"];
			$priSubscriberStreet 	= $res["priSubscriberStreet"];
			$priSubscriberCity 		= $res["priSubscriberCity"];
			$priSubscriberState 	= $res["priSubscriberState"];
			$priSubscriberZip 		= $res["priSubscriberZip"];
			$priSubscriberEmployer 	= $res["priSubscriberEmployer"];
			//$priInsCompName 		= "";
			//if($_SESSION['currentCaseid']){
			$priInsCompName 		= $res["priInsCompName"];
			//}
			/*else{
			    $priInsCompName         = $insuranceCompanyName;
			}*/
			$priInsCompAddress 		= $res["priInsCompAddress"];
			$secInsCompAddress 		= $res["secInsCompAddress"];
			
			$secPolicyNumber 		= $res["secPolicyNumber"];
			$secGroupNumber 		= $res["secGroupNumber"];
			$secSubscriberName 		= $res["secSubscriberName"];
			$secSubscriberRelation 	= $res["secSubscriberRelation"];
			$secSubscriberDOB 		= $res["secSubscriberDOB"];
			$secSubscriberSS 		= $res["secSubscriberSS"];
			$secSubscriberPhone 	= $res["secSubscriberPhone"];
			$secSubscriberStreet 	= $res["secSubscriberStreet"];
			$secSubscriberCity 		= $res["secSubscriberCity"];
			$secSubscriberState 	= $res["secSubscriberState"];
			$secSubscriberZip 		= $res["secSubscriberZip"];
			$secSubscriberEmployer 	= $res["secSubscriberEmployer"];
			$secInsCompName 		= $res["secInsCompName"];
			
			
			//--- get operator details --------
			$qryGetUsrData = "select fname,mname,lname from users where id = '".$_SESSION['authId']."'";
			$rsGetUsrData = sqlQuery($qryGetUsrData);
			$operator_name = $rsGetUsrData['lname'].', ';
			$operator_name .= $rsGetUsrData['fname'].' ';
			$operator_name .= $rsGetUsrData['mname'];
			$operator_initial = substr($rsGetUsrData['fname'],0,1);
			$operator_initial .= substr($rsGetUsrData['lname'],0,1);
			
			
			//Sign*/ 
			$title = (!empty($title)) ? $title : "";
			$fname = (!empty($fname)) ? $fname : "";
			$lname = (!empty($lname)) ? $lname : "";
			$mname = (!empty($mname)) ? $mname : "";
			
			$ptSt_zip = "";
			$ptSt_zip .= (!empty($state)) ? "".$state : "";
			$ptSt_zip .= (!empty($ptSt_zip)) ? ", " : "";
			$ptSt_zip .= (!empty($zip)) ? " ".$zip : "";
			//Refer Phy
			
			$rp_ptRfPhyId = $res["ptRfPhyId"];
			$rp_ptPCPPhyId = $res["ptPCPPhyId"];
			$rp_title = $res["rp_title"];
			$rf_credential= $res["rf_credential"];
			$rp_fname = $res["FirstName"];
			$rp_mname = $res["MiddleName"];
			$rp_lname = $res["LastName"];
			$rp_add1 = $res["Address1"];
			$rp_add2 = $res["Address2"];
			$rp_zip = $res["ZipCode"];
			$rp_city = $res["rp_city"];
			$rp_fax = $res["rp_fax"];
			$rp_state = $res["rp_state"];
			$rp_specialty = $res["rp_specialty"];
			$rp_physician_phone = core_phone_format($res["rp_physician_phone"]);
			
			$physicianNameNew = $physicianName;
			if(trim($rp_title)) { $physicianNameNew = $physicianName." ".$rp_title; }
			$rp_name ="";
			/*
			$rp_name .= (!empty($rp_title)) ? "".$rp_title." " : "" ;
			$rp_name .= (!empty($rp_fname)) ? "".$rp_fname." " : "" ;
			$rp_name .= (!empty($rp_mname)) ? "".$rp_mname." " : "" ;
			$rp_name .= (!empty($rp_lname)) ? "".$rp_lname : "" ;
			*/
			$arr_right_title=array("DO","MD","OD");
			$arr_left_title=array("Miss","Dr.","Mr.","Mrs.","Ms");
			$rp_name .= (in_array($rp_title,$arr_left_title) && trim($rp_title)!='' && trim($rf_credential)=='')?"".$rp_title." ": "" ;
			$rp_name .= (!empty($rp_fname)) ? "".$rp_fname." " : "" ;
			$rp_name .= (!empty($rp_mname)) ? "".$rp_mname." " : "" ;
			$rp_name .= (!empty($rp_lname)) ? "".$rp_lname." " : "" ;
			$rp_name .= (in_array($rp_title,$arr_right_title) && trim($rp_title)!='')?"".$rp_title: "" ;
			$rp_name .= (in_array($rp_title,$arr_left_title) && trim($rf_credential)!='') ? "".$rf_credential."" : "" ;
			
			
			$rp_csz = "";
			$rp_csz .= (!empty($rp_city)) ? "".$rp_city : "";
			$rp_csz .= (!empty($rp_csz) && (!empty($rp_zip) || !empty($rp_state))) ? ", " : "";
			$rp_csz .= (!empty($rp_state)) ? "".$rp_state." " : "";
			$rp_csz .= (!empty($rp_zip)) ? "".$rp_zip." " : "";
			/*
			$rp_info = "";
			$rp_info .= (!empty($rp_name)) ? $rp_name."<br>" : "";
			$rp_info .= (!empty($rp_add1)) ? $rp_add1."<br>" : "";
			$rp_info .= (!empty($rp_add2)) ? $rp_add2."<br>" : "";
			$rp_info .= $rp_csz;
			*/
			$rp_info = "";
			$rp_info .= (!empty($rp_name)) ? trim($rp_name) : "";
			
			$rp_street_add = "";			
			$rp_street_add .= (!empty($rp_add1)) ? $rp_add1 : "";
			$rp_street_add .= (!empty($rp_add2)) ? "<br>".$rp_add2 : "";		
			
			$addresseeName = $addresseeAddress = "";
			$addresseeName = $rp_name;
			
			$addresseeAddress .= $rp_street_add;
			$addresseeAddress .= (!empty($rp_city)) ? "<br>".$rp_csz : "";
			
			$addresseFax 		= $res["rp_fax"];
			$addressePhone		= $res["rp_physician_phone"];
			$addresseSpecialty	= $res["rp_specialty"];
				
			$cc1Title 		= $res["pcp_title"];
			$cc1FName 		= $res["pcp_firstName"];
			$cc1MName 		= $res["pcp_middleName"];
			$cc1LName 		= $res["pcp_lastName"];
			$cc1Address1	= $res["pcp_address1"];
			$cc1Address2 	= $res["pcp_address2"];
			$cc1City 		= $res["pcp_city"];
			$cc1State 		= $res["pcp_state"];
			$cc1ZipCode 	= $res["pcp_zipCode"];
			
			//CODE COMMENTED TO STOP REPLACE PCP NAME WITH {CC1} VARIABLE
			/*$cc1Name = $cc1Address = "";
			if(trim($cc1FName) && trim($cc1LName)) {
				if(!empty($cc1Title)){
					$cc1LName = $cc1LName.',';
				}
				
				$cc1Name = $cc1FName." ".$cc1LName." ".$cc1Title;
				
				$cc1Address .= (!empty($cc1Address1)) ? $cc1Address1 : "";
				$cc1Address .= (!empty($cc1Address2)) ? "<br>".$cc1Address2 : "";
				$cc1Address .= (!empty($cc1City)) 	  ? "<br>".$cc1City.", ".$cc1State." ".$cc1ZipCode : "";		
			}*/
			//Medical Doctor (PCP)
			$medDoc = $res["med_doctor"];
			$arrMedDoc = explode(",",$medDoc);				
				$MD_lname = trim($arrMedDoc[0]);
				$arrMDLname = explode(" ",$MD_lname);
				$MD_lname = ($arrMDLname[1] != "") ? $arrMDLname[1] : $arrMDLname[0];
				$MD_fname = trim($arrMedDoc[1]);
				$arrMDFname = explode(" ",$MD_fname);
				$MD_fname = $arrMDFname[0];
				
				
				$qryGetMDData = "select Title , FirstName , MiddleName, LastName, ".
			 							"Address1, Address2, ZipCode, City , ".			 
			 							"State ,specialty ,physician_phone, physician_fax ".
										"from refferphysician where physician_Reffer_id = '".$rp_ptPCPPhyId."'";	
				$rsGetMDData = sqlQuery($qryGetMDData);
					$PCP_title = $rsGetMDData["Title"];
					$PCP_fname = $rsGetMDData["FirstName"];
					$PCP_mname = $rsGetMDData["MiddleName"];
					$PCP_lname = $rsGetMDData["LastName"];
					$PCP_add1 = $rsGetMDData["Address1"];
					$PCP_add2 = $rsGetMDData["Address2"];
					$PCP_zip = $rsGetMDData["ZipCode"];
					$PCP_city = $rsGetMDData["City"];
					$PCP_state = $rsGetMDData["State"];
					$PCP_specialty = $rsGetMDData["specialty"];
					$PCP_physician_phone = core_phone_format($rsGetMDData["physician_phone"]);
					$PCP_name ="";
					$PCP_name .= (!empty($PCP_title)) ? "".$PCP_title." " : "" ;
					$PCP_name .= (!empty($PCP_fname)) ? "".$PCP_fname." " : "" ;
					$PCP_name .= (!empty($PCP_mname)) ? "".$PCP_mname." " : "" ;
					$PCP_name .= (!empty($PCP_lname)) ? "".$PCP_lname : "" ;
					if(trim($PCP_fname) && trim($PCP_lname)) {  
						$medDoc = $PCP_fname." ".$PCP_lname." ".$PCP_title;
					}
					$PCP_fax = $rsGetMDData["physician_fax"];
					
					$PCP_csz = "";
					$PCP_csz .= (!empty($PCP_city)) ? "".$PCP_city : "";
					$PCP_csz .= (!empty($PCP_csz) && (!empty($PCP_zip) || !empty($PCP_state))) ? ", " : "";
					$PCP_csz .= (!empty($PCP_state)) ? "".$PCP_state." " : "";
					$PCP_csz .= (!empty($PCP_zip)) ? "".$PCP_zip." " : "";
					
					$PCP_info = "";
					$PCP_info .= (!empty($PCP_name)) ? $PCP_name."<br>" : "";
					$PCP_info .= (!empty($PCP_add1)) ? $PCP_add1."<br>" : "";
					$PCP_info .= (!empty($PCP_add2)) ? $PCP_add2."<br>" : "";
					$PCP_info .= $PCP_csz;
					
					$PCP_street_add = "";			
					$PCP_street_add .= (!empty($PCP_add1)) ? $PCP_add1 : "";
					$PCP_street_add .= (!empty($PCP_add2)) ? "<br>".$PCP_add2 : "";	
					
			//---MD end (PCP)--------
			
			
			
			//refering phy start			
			if($patientRefTo!= ""){
				$rp_info = $patientRefTo;
				$arrPatientRefTo = explode(",",$patientRefTo);				
				$rp_lname = trim($arrPatientRefTo[0]);
				$arrRpLname = explode(" ",$rp_lname);
				$rp_lname = ($arrRpLname[1] != "") ? $arrRpLname[1] : $arrRpLname[0];
				$rp_fname = trim($arrPatientRefTo[1]);
				$arrRpFname = explode(" ",$rp_fname);
				$rp_fname = $arrRpFname[0];
				$qryCond=" FirstName = '".addslashes($rp_fname)."' and LastName = '".addslashes($rp_lname)."'";
				if(is_numeric($patientRefTo)){
					$qryCond=" physician_Reffer_id = '".$patientRefTo."'";	
				}
				
				$qryGetRefPhyData = "select Title , FirstName , MiddleName, LastName, ".
			 							"Address1, Address2, ZipCode, City , physician_fax,  ".			 
			 							"State ,specialty ,physician_phone,credential ".
										"from refferphysician where ".$qryCond;
				$rsGetRefPhyData = sqlQuery($qryGetRefPhyData);	
				if(($rsGetRefPhyData !== false)){				
					//Refer Phy
					$rp_title = $rsGetRefPhyData["Title"];
					$rp_fname = $rsGetRefPhyData["FirstName"];
					$rp_mname = $rsGetRefPhyData["MiddleName"];
					$rp_lname = $rsGetRefPhyData["LastName"];
					$rp_add1 = $rsGetRefPhyData["Address1"];
					$rp_add2 = $rsGetRefPhyData["Address2"];
					$rp_zip = $rsGetRefPhyData["ZipCode"];
					$rp_city = $rsGetRefPhyData["City"];
					$rp_fax = $rsGetRefPhyData["physician_fax"];
					$rp_state = $rsGetRefPhyData["State"];
					$rp_specialty = $rsGetRefPhyData["specialty"];
					$rp_credential=$rsGetRefPhyData["credential"];
					$rp_physician_phone = core_phone_format($rsGetRefPhyData["physician_phone"]);
					$rp_name ="";
					
					$rp_name .= (in_array($rp_title,$arr_left_title) && trim($rp_title)!='' && trim($rp_credential)=='')?"".$rp_title." ": "" ;
					$rp_name .= (!empty($rp_fname)) ? "".$rp_fname." " : "" ;
					$rp_name .= (!empty($rp_mname)) ? "".$rp_mname." " : "" ;
					$rp_name .= (!empty($rp_lname)) ? "".$rp_lname." " : "" ;
					$rp_name .= (in_array($rp_title,$arr_right_title) && trim($rp_title)!='')?"".$rp_title: "" ;
					$rp_name .= (in_array($rp_title,$arr_left_title) && trim($rp_credential)!='') ? "".$rp_credential."" : "" ;

					
					$rp_csz = "";
					$rp_csz .= (!empty($rp_city)) ? "".$rp_city : "";
					$rp_csz .= (!empty($rp_csz) && (!empty($rp_zip) || !empty($rp_state))) ? ", " : "";
					$rp_csz .= (!empty($rp_state)) ? "".$rp_state." " : "";
					$rp_csz .= (!empty($rp_zip)) ? "".$rp_zip." " : "";
					/*
					$rp_info = "";
					$rp_info .= (!empty($rp_add1)) ? $rp_add1."<br>" : "";
					$rp_info .= (!empty($rp_add2)) ? $rp_add2."<br>" : "";
					$rp_info .= $rp_csz;
					*/
					$rp_info = "";
					$rp_info .= (!empty($rp_name)) ? trim($rp_name) : "";
					
					$rp_street_add = "";			
					$rp_street_add .= (!empty($rp_add1)) ? $rp_add1 : "";
					$rp_street_add .= (!empty($rp_add2)) ? "<br>".$rp_add2 : "";	
				}
				
			}
			// "rf.Title AS rp_title, rf.FirstName , rf.MiddleName, rf.LastName, ".
			if($addresseRefToId && $addresseRefToId!=$rp_ptRfPhyId) {
				
				list($addresseTitle, $addresseFName, $addresseMName, $addresseLName, $addresseAddress1, $addresseAddress2, $addresseCity, $addresseState, $addresseZipCode, $addresseFax, $addresseEmail, $addressePhone, $addresseSpecialty) = $this->getRefPcpInfo($addresseRefToId);
				
				$addresseeName = $addresseFName." ".$addresseLName." ".$addresseTitle;
				$addresseeAddress="";
				$addresseeAddress .= (!empty($addresseAddress1)) ? $addresseAddress1 : "";
				$addresseeAddress .= (!empty($addresseAddress2)) ? "<br>".$addresseAddress2 : "";
				$addresseeAddress .= (!empty($addresseCity)) ? "<br>".$addresseCity.", ".$addresseState." ".$addresseZipCode : "";		
				
			}
			$cc2Name = $cc3Name = $cc2Address = $cc3Address = "";
			// && $cc1RefToId!=$rp_ptPCPPhyId
			if($cc1RefToId) {
				list($cc1Title,$cc1FName,$cc1MName,$cc1LName,$cc1Address1,$cc1Address2,$cc1City,$cc1State,$cc1ZipCode) = $this->getRefPcpInfo($cc1RefToId);
				if(!empty($cc1Title)){
					$cc1LName = $cc1LName.',';
				}
				$cc1Name 	 = $cc1FName." ".$cc1LName." ".$cc1Title;
				$cc1Address  = "";
				$cc1Address .= (!empty($cc1Address1)) ? $cc1Address1 : "";
				$cc1Address .= (!empty($cc1Address2)) ? "<br>".$cc1Address2 : "";
				$cc1Address .= (!empty($cc1City)) 	  ? "<br>".$cc1City.", ".$cc1State." ".$cc1ZipCode : "";		
			}
			
			if($cc2RefToId) {
				list($cc2Title,$cc2FName,$cc2MName,$cc2LName,$cc2Address1,$cc2Address2,$cc2City,$cc2State,$cc2ZipCode) = $this->getRefPcpInfo($cc2RefToId);
				if(!empty($cc2Title)){
					$cc2LName = $cc2LName.',';
				}
				$cc2Name = $cc2FName." ".$cc2LName." ".$cc2Title;
				$cc2Address .= (!empty($cc2Address1)) ? $cc2Address1 : "";
				$cc2Address .= (!empty($cc2Address2)) ? "<br>".$cc2Address2 : "";
				$cc2Address .= (!empty($cc2City)) 	  ? "<br>".$cc2City.", ".$cc2State." ".$cc2ZipCode : "";		
			}
			if($cc3RefToId) {
				list($cc3Title,$cc3FName,$cc3MName,$cc3LName,$cc3Address1,$cc3Address2,$cc3City,$cc3State,$cc3ZipCode) = $this->getRefPcpInfo($cc3RefToId);
				if(!empty($cc3Title)){
					$cc3LName = $cc3LName.',';
				}
				$cc3Name = $cc3FName." ".$cc3LName." ".$cc3Title;
				$cc3Address .= (!empty($cc3Address1)) ? $cc3Address1 : "";
				$cc3Address .= (!empty($cc3Address2)) ? "<br>".$cc3Address2 : "";
				$cc3Address .= (!empty($cc3City)) 	  ? "<br>".$cc3City.", ".$cc3State." ".$cc3ZipCode : "";		
			}
			
			//===============LOGGED IN FACILITY INFO WORKS WORKS STARTS HERE=========================
			$qryRes = sqlQuery("SELECT name, street, city, state, postal_code, zip_ext, phone, fax FROM `facility` WHERE id='".$_SESSION['login_facility']."'");
			if($qryRes!=false){				  
				  $loggedfacilityname	= $qryRes['name'];
				  $facStreet  			= $qryRes['street'];
				  $facCity 				=  $qryRes['city'];
				  $facState				=  $qryRes['state'];
				  $facPostal_code		=  $qryRes['postal_code'];
				  $facZip_ext 			=  $qryRes['zip_ext'];
				  $facPhone 			=  trim($qryRes['phone']);
			}
			  if(!empty($facPhone) && ($facPhone!=='-')){
			   	   $loggedfacPhone	= ', Ph:&nbsp;'.$facPhone;
			   }	
			  if($facPostal_code && $facZip_ext){
				  $loggedzipcodext = $facPostal_code.'-'.$facZip_ext;
			  }else{
				  $loggedzipcodext = $facPostal_code;
			  }
			  
			 if(!empty($facFax) && ($facFax!=='-')){
			   	   $loggedfacFax	= '&nbsp;'.$facFax;
			  }	
			  
			  $loggedfacAddress = $facStreet.', '.$facCity.',&nbsp;'.$facState.'&nbsp;'.$loggedzipcodext.$loggedfacPhone;
			  //=======================ENDS HERE======================================================

			
			//Replace Array
			
			$arrRep = array($patientId,$title,$lname,$mname,$fname,$street,$street2,$city,$ptSt_zip,$dob,
						$rp_info,$medDoc,$rp_fname,$rp_lname,$physicianNameNew,$rp_specialty,$rp_physician_phone,$rp_street_add,$rp_city,
						$rp_state,$rp_zip,$rp_fax,$ptAge,$PCP_city,$PCP_state,$PCP_zip,$rp_title,$PCP_street_add,$addresseeName,$addresseeAddress,$addressePhone, $addresseFax, $addresseSpecialty,$cc1Name,$cc1Address,$cc2Name,$cc2Address,$cc3Name,$cc3Address,
						$ptSex,$dob,$ptSS,$ptMaritalStatus,$ptHomePhone,$ptEmergencyContact,$ptEmergencyContactPhone,$ptMobilePhone,$ptWorkPhone,$state,$zip,$ptRegDate,$pos_facility_name,$ptDrivingLicence,
						$resp_title,$resp_fname,$resp_mname,$resp_lname,$resp_dob,$resp_ss,$resp_sex,$resp_relation,$resp_address,$resp_address2,
						$resp_home_ph,$resp_work_ph,$resp_mobile,$resp_city,$resp_state,$resp_zip,$resp_marital,$resp_licence,$pt_occupation,
						$empdata_name,$empdata_street,$empdata_street2,$empdata_city,$empdata_state,$empdata_postal_code,$monthly_income,
						$patient_initial,$currTm,$operator_name,$operator_initial,$heard_abt_us,$heard_abt_desc,$pt_email,
						$priInsCompName,$priPolicyNumber,$priGroupNumber,$priSubscriberName,$priSubscriberRelation,$priSubscriberDOB,$priSubscriberSS,$priSubscriberPhone,
						$priSubscriberStreet,$priSubscriberCity,$priSubscriberState,$priSubscriberZip,$priSubscriberEmployer,$secInsCompName,$secPolicyNumber,$secGroupNumber,$secSubscriberName,
						$secSubscriberRelation,$secSubscriberDOB,$secSubscriberSS,$secSubscriberPhone,$secSubscriberStreet,$secSubscriberCity,$secSubscriberState,$secSubscriberZip,
						$secSubscriberEmployer,$External_MRN_1,$External_MRN_2,$raceShow,$language,$ethnicityShow,$priInsCompAddress,$secInsCompAddress,
						$PCP_physician_phone, $PCP_fax,$ptss_last4digit,$loggedfacilityname,$loggedfacAddress,$pt_key,$coManagedPhy,$ptSex_s,$loggedfacFax,$PCP_name,$patient_suffix, $patient_nick_name
						);			
			//echo '<pre>';			
			//print_r($rsGetRefPhyData);
			//$genericval1,$genericval2,
			
		}
		
		//Replace
		$str = $this->refineData($str, $this->kPtInfo,$arrRep,$this->tPtInfo,"");
		
		return $str;
	}
	
	//Get Chart Left CC Hx
	private function getChartLeftCcHx($str, $ptId, $formId,$sign_path_remote,$document_from){
		$sql = "SELECT ".
			 "chart_master_table.date_of_service, ".
			 "chart_master_table.finalize, ".
			 "chart_master_table.finalizerId, ".
			  "chart_master_table.providerId, ".
			 "chart_left_cc_history.ccompliant , ".
			 "chart_left_cc_history.reason , ".
			 "chart_left_cc_history.dominant ".	
			 "FROM chart_master_table ".
			 "LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id ".
			 "WHERE chart_master_table.id='".$formId."' AND chart_master_table.patient_id ='".$ptId."' ";
		$res = sqlQuery($sql);
		if($res !== false){
			$reasonText = $reasonOri = $reasonTextCC = "";
			//$dos = $res->UserDate($res["date_of_service"],"m-d-Y");
			$dos = wv_formatDate($res["date_of_service"]);
			$site = $res["dominant"];
			$finalizeStatus = $res["finalize"];
			$finalizer = $res["finalizerId"];
			$provId = $res["providerId"];
			$site = strtoupper($site);
			if($site=="OD") 	{ $siteShow = "Right"; 
			}elseif($site=="OS"){ $siteShow = "Left"; 
			}elseif($site=="OU"){ $siteShow = "Both"; 
			}
			
			//$pattern = '/\d.\s/';			
			//$pattern = '/[\d]+[\.]+[\s]/'; 
			$pattern = '/[\d]+[\.]+[\s]+[\/]/'; 
			$reasonOri = preg_replace($pattern,"",$res["ccompliant"]);
			
			if(!trim($reasonOri)) { //check if current field is blank show previous field
				$reasonOri = preg_replace($pattern,"",$res["reason"]);
			}
			if($reasonOri == ""){
				$oMedHx = new MedHx($ptId);
				$oCcHx = new CcHx($ptId,0);
				$text_data = $oCcHx->getCcHx1stLine();
				
				/*list($eyeProbs, $chronicProbs) = $oMedHx->getOcularEyeInfo();
				$rvs = $oMedHx->getGenMedInfo();
				
				$text_data .= $chronicProbs;
				$elem_acuteProbs = ($oCcHx->isEyeProbChanged($eyeProbs, $elem_acuteProbsPrev)) ? addslashes("".$eyeProbs) : "";
				$text_data .= $elem_acuteProbs; // Add Eye Problems
				
				$text_data .= "".$rvs; // diebates*/
				
				$reasonOri = preg_replace("/\s\s/", " ", trim($text_data[1])); // remove double spaces;
				$reasonOri = preg_replace($pattern,"",$reasonOri);
			}
			$arrReason 		= explode("-",$reasonOri);
			foreach($arrReason as $value){
				// $reasonText .= "-".wordwrap($value, 110);
				   $reasonText .= "-".wordwrap($value, 100);
			}
			if($reasonText != ""){
				//$reason = str_ireplace("-","</br>",$reasonText);
				$reason = ltrim($reasonText,'-');
			}
			else{
				//$reason = str_replace("-","</br>",$res["ccompliant"]);
				//$reason = str_ireplace("-","</br>",$reasonOri);
				$reason = $reasonOri;
			}
			
			//reason cc (new field added)
			$reasonCC = preg_replace($pattern,"",$res["reason"]);
			$arrReasonCC 	= explode("-",$reasonCC);
			foreach($arrReasonCC as $valueKeyCC => $valueCC){
				 if($valueKeyCC >0) { $reasonTextCC .= "-";}
				 $reasonTextCC .= wordwrap($valueCC, 110);
			}
			//$reasonNewCC = str_ireplace("-","</br>",$reasonTextCC);
			$reasonNewCC = $reasonTextCC;
			if(!trim($reasonTextCC)) {
				//$reasonNewCC = str_ireplace("-","</br>",$reasonCC);	
				$reasonNewCC = $reasonCC;	
			}
			//reason cc (new field added)
			
		}
		/*echo $sqlGetPhysician = "SELECT cap.id as capId, CONCAT_WS(' ',us.fname,us.lname) as physicianName,us.fname as usr_fname,us.mname as usr_mname,us.lname as usr_lname,us.pro_title as pro_title,us.pro_suffix as pro_suffix, cap.doctorId as docId 
							from chart_assessment_plans cap 
							LEFT JOIN users us ON us.id = cap.doctorId
							WHERE cap.form_id ='".$formId."' AND cap.patient_id ='".$ptId."' 
							".$qrySigFinlz;*/
		$user_type_arr = array(1,3,7,12);
		if($finalizeStatus=="1" && $finalizer){				
			$qryChkPhy="SELECT id from users WHERE user_type IN (1,7,12) AND delete_status!='1' and id='".$finalizer."' LIMIT 0,1";
			$resChkPhy=imw_query($qryChkPhy);
			if(imw_num_rows($resChkPhy)>0){
				$provId=$finalizer;
			}
		}else{
			//       Logged in as a scribe the consult letter populates the resident or fellows signature that saw the patient.  Should populate the attendings signature that the scribe is following.
			if(isset($_SESSION['res_fellow_sess']) && !empty($_SESSION['res_fellow_sess'])){			
				$provId=$_SESSION['res_fellow_sess'];
			}
		}		
		
		$sqlGetPhysician ="SELECT  CONCAT_WS(' ',us.fname,us.lname) as physicianName,us.fname as usr_fname,us.mname as usr_mname,us.lname as usr_lname,us.pro_title as pro_title,us.pro_suffix as pro_suffix,sign_path, us.user_npi as user_npi  
		from users as us where id='".$provId."'";					
		$rsGetPhysician = sqlQuery($sqlGetPhysician) ;	
		if($rsGetPhysician !== false){			
			$physicianName = $rsGetPhysician["physicianName"];
			$usr_fname = $rsGetPhysician["usr_fname"];
			$usr_mname = $rsGetPhysician["usr_mname"];
			$usr_lname = $rsGetPhysician["usr_lname"];
			$pro_title = $rsGetPhysician["pro_title"];
			$pro_suffix = $rsGetPhysician["pro_suffix"];
			$sign_path =  $rsGetPhysician["sign_path"];
			$user_npi =  $rsGetPhysician["user_npi"];
			$physicianNameNew = $physicianName;
			//if($pro_title) { $physicianNameNew = $physicianName."&nbsp;".$pro_title; }
			if($pro_suffix) { $physicianNameNew = $physicianName." ".$pro_suffix; }
			//Sign 
			global $gdFilename;
			global $web_RootDirectoryName;
			global $webserver_root;
			$img_path="";
			/*//TODOLATER//
			if($sign_path_remote) {
				$sign_path_remote_decode = urldecode($sign_path_remote);
				$img_path="../../interface/main/uploaddir".$sign_path_remote_decode;
			}else if($sign_path){
				$img_path="../../interface/main/uploaddir".$sign_path;
				if($document_from=='education_instruction' || $document_from=='opnotes'){
					$img_path="../../main/uploaddir".$sign_path;
				}else if($document_from=='procedures'){
					$img_path="/".$web_RootDirectoryName."/interface/main/uploaddir".$sign_path;
				}
			}else{
				$id = $provId;
				$tblName = "users";
				$pixelFieldName = "sign";
				$idFieldName = "id";
				$imgPath = "";
				$saveImg = "3";
				
				include($webserver_root."/interface/main/imgGd.php");			
				$img_path="/".$web_RootDirectoryName."/interface/common/new_html2pdf/tmp/".$gdFilename;
			}
			*/
			if($sign_path){ $img_path=$GLOBALS['webroot'].'/data/'.PRACTICE_PATH.$sign_path; }
			if(strpos($img_path,"/var/www/html")!== false){ 
				$img_path = str_ireplace('/var/www/html','',$img_path);
			}
			if($img_path){$TData="<img src='".$img_path."' alt=\"alt image\" style=\"width: 225px; height: 85px;\">";}
			if($_SESSION["logged_user_type"] && !in_array($_SESSION["logged_user_type"],$user_type_arr) && ($document_from=='opnotes' || $document_from=='procedures')) {
				$TData="{PHYSICIAN SIGNATURE}";
			}
			//Sign
		}	
			$RepArr = array("<",">","&lt;/br&gt;","&lt;br /&gt;","&lt;br/&gt;","&lt;br&gt;","OD&lt;OS");
			$arr_rep= array("&lt;","&gt;","<br>","<br />","<br/>","<br>","OD &lt; OS");
			$reason = str_ireplace($RepArr,$arr_rep,$reason);
			$reasonNewCC = str_ireplace($RepArr,$arr_rep,$reasonNewCC);
			$reasonNewCC = str_ireplace($RepArr,$arr_rep,$reasonNewCC);
			
			//Replace Array
			$arrRep = array($dos,$reason,$reasonNewCC,$physicianNameNew,$TData,$siteShow,$usr_fname,$usr_mname,$usr_lname,$pro_suffix,$user_npi);
			
		//Replace
		$str = $this->refineData($str, $this->kChartLeftCcHx, $arrRep, $this->tChartLeftCcHx,"");
		return $str;
	}
	public function get_cc_hx($ptId, $formId){
		$sql = "SELECT ".
			 "chart_master_table.date_of_service, ".
			 "chart_master_table.finalize, ".
			 "chart_master_table.finalizerId, ".
			  "chart_master_table.providerId, ".
			 "chart_left_cc_history.ccompliant , ".
			 "chart_left_cc_history.reason , ".
			 "chart_left_cc_history.dominant ".	
			 "FROM chart_master_table ".
			 "LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id ".
			 "WHERE chart_master_table.id='".$formId."' AND chart_master_table.patient_id ='".$ptId."' ";
		$res = sqlQuery($sql);
		if($res !== false){
			$reasonText = $reasonOri = $reasonTextCC = "";
			$dos = get_date_format($res["date_of_service"]);
			$site = $res["dominant"];
			$finalizeStatus = $res["finalize"];
			$finalizer = $res["finalizerId"];
			$provId = $res["providerId"];
			$site = strtoupper($site);
			if($site=="OD") 	{ $siteShow = "Right"; 
			}elseif($site=="OS"){ $siteShow = "Left"; 
			}elseif($site=="OU"){ $siteShow = "Both"; 
			}
			
			//$pattern = '/\d.\s/';			
			$pattern = '/[\d]+[\.]+[\s]/'; 
			$reasonOri = preg_replace($pattern,"",$res["ccompliant"]);
			
			if(!trim($reasonOri)) { //check if current field is blank show previous field
				$reasonOri = preg_replace($pattern,"",$res["reason"]);
			}
			if($reasonOri == ""){
				$oMedHx = new MedHx($ptId);
				$oCcHx = new CcHx($ptId,0);
				$text_data = $oCcHx->getCcHx1stLine();
				list($eyeProbs, $chronicProbs) = $oMedHx->getOcularEyeInfo();
				$rvs = $oMedHx->getGenMedInfo();
				
				$text_data .= $chronicProbs;
				$elem_acuteProbs = ($oCcHx->isEyeProbChanged($eyeProbs, $elem_acuteProbsPrev)) ? addslashes("".$eyeProbs) : "";
				$text_data .= $elem_acuteProbs; // Add Eye Problems
				
				$text_data .= "".$rvs; // diebates				
				
				$reasonOri = preg_replace("/\s\s/", " ", trim($text_data)); // remove double spaces;
				$reasonOri = preg_replace($pattern,"",$reasonOri);
			}
			$arrReason 		= explode("-",$reasonOri);
			foreach($arrReason as $value){
				 $reasonText .= "-".wordwrap($value, 100, "<br />\n");
			}
			if($reasonText != ""){
				$reason = str_ireplace("-","</br>",$reasonText);
			}
			else{
				//$reason = str_replace("-","</br>",$res["ccompliant"]);
				$reason = str_ireplace("-","</br>",$reasonOri);
			}
			
			//reason cc (new field added)
			$reasonCC = preg_replace($pattern,"",$res["reason"]);
			$arrReasonCC 	= explode("-",$reasonCC);
			foreach($arrReasonCC as $valueKeyCC => $valueCC){
				 if($valueKeyCC >0) { $reasonTextCC .= "-";}
				 $reasonTextCC .= wordwrap($valueCC, 100, "<br />\n");
			}
			$reasonNewCC = str_ireplace("-","</br>",$reasonTextCC);
			if(!trim($reasonTextCC)) {
				$reasonNewCC = str_ireplace("-","</br>",$reasonCC);	
			}
			//reason cc (new field added)
			
		}
		
		/*echo $sqlGetPhysician = "SELECT cap.id as capId, CONCAT_WS(' ',us.fname,us.lname) as physicianName,us.fname as usr_fname,us.mname as usr_mname,us.lname as usr_lname,us.pro_title as pro_title,us.pro_suffix as pro_suffix, cap.doctorId as docId 
							from chart_assessment_plans cap 
							LEFT JOIN users us ON us.id = cap.doctorId
							WHERE cap.form_id ='".$formId."' AND cap.patient_id ='".$ptId."' 
							".$qrySigFinlz;*/
		if($finalizeStatus=="1" && $finalizer){				
			$qryChkPhy="SELECT id from users WHERE user_type IN (1,7,12) AND delete_status!='1' and id='".$finalizer."' LIMIT 0,1";
			$resChkPhy=imw_query($qryChkPhy);
			if(imw_num_rows($resChkPhy)>0){
				$provId=$finalizer;
			}
		}else{
			//       Logged in as a scribe the consult letter populates the resident or fellows signature that saw the patient.  Should populate the attendings signature that the scribe is following.
			if(isset($_SESSION['res_fellow_sess']) && !empty($_SESSION['res_fellow_sess'])){			
				$provId=$_SESSION['res_fellow_sess'];
			}
		}
		$sqlGetPhysician ="SELECT  CONCAT_WS(' ',us.fname,us.lname) as physicianName,us.fname as usr_fname,us.mname as usr_mname,
						us.lname as usr_lname,us.pro_title as pro_title,us.pro_suffix as pro_suffix 
						from users as us where id='".$provId."'";					
		$rsGetPhysician = sqlQuery($sqlGetPhysician);	
		if($rsGetPhysician !== false){			
			$physicianName = $rsGetPhysician["physicianName"];
			$usr_fname = $rsGetPhysician["usr_fname"];
			$usr_mname = $rsGetPhysician["usr_mname"];
			$usr_lname = $rsGetPhysician["usr_lname"];
			$pro_title = $rsGetPhysician["pro_title"];
			$pro_suffix = $rsGetPhysician["pro_suffix"];
			
			$physicianNameNew = $physicianName;
			//if($pro_title) { $physicianNameNew = $physicianName."&nbsp;".$pro_title; }
			if($pro_suffix) { $physicianNameNew = $physicianName."&nbsp;".$pro_suffix; }
			//Sign
			/*TODO	
			global $gdFilename;
			global $web_RootDirectoryName;
			global $webserver_root;
			$id = $provId;
			$tblName = "users";
			$pixelFieldName = "sign";
			$idFieldName = "id";
			$imgPath = "";
			$saveImg = "3";
			
			include($webserver_root."/interface/main/imgGd.php");			

			$TData = "<img src=\"/$web_RootDirectoryName/interface/common/new_html2pdf/tmp/".$gdFilename."\" alt=\"alt image\">";
			*/	
			//Sign
		}	
			$RepArr = array("<",">","&lt;/br&gt;","&lt;br /&gt;","&lt;br/&gt;","&lt;br&gt;","OD&lt;OS");
			$arr_rep= array("&lt;","&gt;","<br>","<br />","<br/>","<br>","OD &lt; OS");
			$reason = str_ireplace($RepArr,$arr_rep,$reason);
			$reasonNewCC = str_ireplace($RepArr,$arr_rep,$reasonNewCC);
			$reasonNewCC = str_ireplace($RepArr,$arr_rep,$reasonNewCC);
			
			//Replace Array
			$arrRep = array($dos,$reason,$reasonNewCC,$physicianNameNew,$TData,$siteShow,$usr_fname,$usr_mname,$usr_lname,$pro_suffix);
			return $arrRep;
	}
	//Get Vision
	private function getVision($str, $ptId, $formId){
		$VCCOD = $VCCOS = $VCCOU = "";
		$VSCOD = $VSCOS = $VSCOU ="";
		$KRTOD = $KRTOS = "";
		$NEAROU = "";
		
		$sql = "
			SELECT
				c1.status_elements,
				
				c2.k_od, 
				c2.x_od,
				c2.k_os, 
				c2.slash_od, 
				c2.slash_os, 
				c2.x_os, 
				c2.k_type,
				c2.ex_desc AS k_desc,
				
				c3.txt1_od, 
				c3.txt2_od,
				c3.txt1_os,
				c3.txt2_os, 
				c3.txt1_ou,
				c3.txt2_ou,  
				
				c4.nl_od, 
				c4.l_od, 
				c4.m_od, 
				c4.h_od, 
				c4.nl_os, 
				c4.l_os, 
				c4.m_os, 
				c4.h_os, 
				c4.nl_ou, 
				c4.l_ou, 
				c4.m_ou, 
				c4.h_ou
			FROM 
				chart_vis_master c1
				LEFT JOIN chart_ak c2 ON c2.id_chart_vis_master = c1.id
				LEFT JOIN chart_pam c3 ON c3.id_chart_vis_master = c1.id
				LEFT JOIN chart_bat c4 ON c4.id_chart_vis_master = c1.id
			WHERE 
				c1.form_id = '".$formId."' AND c1.patient_id = '".$ptId."' 
			";
		$row = sqlQuery($sql);
		if($row!=false)
		{
			$statusElements = $row["status_elements"];
			
			//------PAM OD/OS/OU DATA---------------
			$pam_od_txt_1 = $res["txt1_od"];
			$pam_od_txt_2 = $res["txt2_od"];
			
			$pam_os_txt_1 = $res["txt1_os"];
			$pam_os_txt_2 = $res["txt2_os"];
			
			$pam_ou_txt_1 = $res["txt1_ou"];
			$pam_ou_txt_2 = $res["txt2_ou"];
			
			//------BAT OD/OS/OU DATA---------------
			$bat_nl_od = $res["nl_od"];
			$bat_low_od = $res["l_od"];
			$bat_med_od = $res["m_od"];
			$bat_high_od = $res["h_od"];
			
			$bat_nl_os = $res["nl_os"];
			$bat_low_os = $res["l_os"];
			$bat_med_os = $res["m_os"];
			$bat_high_os = $res["h_os"];
			
			$bat_nl_ou = $res["nl_ou"];
			$bat_low_ou = $res["l_ou"];
			$bat_med_ou = $res["m_ou"];
			$bat_high_ou = $res["h_ou"]; 	
			
			
			//------AUTOREFRACTION OD/OS DATA----------
			//------KERATOMETRY READINGS WORKS START HERE
			
			//------RIGHT EYE(OD) DATA WORK STARTS FROM HERE
			$vis_ak_od_k = trim($res["k_od"]);
			$vis_ak_od_slash = trim($res["slash_od"]);
			$vis_ak_od_x = trim($res["x_od"]);
			
			if(!empty($vis_ak_od_k) || !empty($vis_ak_od_slash) || !empty($vis_ak_od_x)){
				
				$KRTOD.= "<b style='color:blue;'>OD:&nbsp;</b>"; 
				
				if($vis_ak_od_k){ $KRTOD.= $vis_ak_od_k; }
				if($vis_ak_od_slash){ $KRTOD.= '/'.$vis_ak_od_slash; }
				if($vis_ak_od_x){ $KRTOD.= '&nbsp;<b>X</b>'.$vis_ak_od_x; }
			}
			//------RIGHT EYE WORKS ENDS HERE=====
			
			//------LEFT EYE(OS) DATA-------------			
			$vis_ak_os_k = trim($res["k_os"]);
			$vis_ak_os_slash = trim($res["slash_os"]);
			$vis_ak_os_x = trim($res["x_os"]);
			
			if(!empty($vis_ak_os_k) || !empty($vis_ak_os_slash) || !empty($vis_ak_os_x)){
				
				$KRTOS.= "<b style='color:green;'>OS:&nbsp;</b>"; 
				
				if($vis_ak_os_k){ $KRTOS.= $vis_ak_os_k; }
				if($vis_ak_os_slash){ $KRTOS.= '/'.$vis_ak_os_slash; }
				if($vis_ak_os_x){ $KRTOS.= '&nbsp;<b>X</b>'.$vis_ak_os_x; }
			}
			//------LEFT EYE WORKS ENDS HERE------
			
			
			//------BAT OD/OS/OU WORKS------------
			$batODTxt = $batOSTxt = $batOUTxt = "";
			//------BAT OD WORKS------------------
			if((!empty($bat_nl_od) && ($bat_nl_od != "20/")) && (strpos($statusElements, "elem_visBatNlOd=1") !== false) || (!empty($bat_low_od) && ($bat_low_od != "20/") && (strpos($statusElements, "elem_visBatLowOd=1") !== false)) || (!empty($bat_med_od) && ($bat_med_od != "20/") && (strpos($statusElements, "elem_visBatMedOd=1") !== false)) || (!empty($bat_high_od) && ($bat_high_od != "20/") && (strpos($statusElements, "elem_visBatHighOd=1") !== false))){
				
			  //$batODTxt.= "<b style='color:blue;'>OD:&nbsp;</b>"; 
			  if($bat_nl_od){  $batODTxt.= '<b>&nbsp;NL&nbsp;</b>'.$bat_nl_od;}
			  if($bat_low_od){ $batODTxt.= '<b>&nbsp;L&nbsp;</b>'.$bat_low_od;}
			  if($bat_med_od){ $batODTxt.= '<b>&nbsp;M&nbsp;</b>'.$bat_med_od;}
			  if($bat_high_od){ $batODTxt.= '<b>&nbsp;H&nbsp;</b>'.$bat_high_od;}
			}
			
			//------BAT OS WORKS------------------
			if((!empty($bat_nl_os) && ($bat_nl_os != "20/")) && (strpos($statusElements, "elem_visBatNlOs=1") !== false) || (!empty($bat_low_os) && ($bat_low_os != "20/") && (strpos($statusElements, "elem_visBatLowOs=1") !== false)) || (!empty($bat_med_os) && ($bat_med_os != "20/") && (strpos($statusElements, "elem_visBatMedOs=1") !== false)) || (!empty($bat_high_os) && ($bat_high_os != "20/") && (strpos($statusElements, "elem_visBatHighOs=1") !== false))){
			  //$batOSTxt.= "<b style='color:green;'>OS:&nbsp;</b>"; 
			  if($bat_nl_os){  $batOSTxt.= '<b>&nbsp;NL&nbsp;</b>'.$bat_nl_os;}
			  if($bat_low_os){ $batOSTxt.= '<b>&nbsp;L&nbsp;</b>'.$bat_low_os;}
			  if($bat_med_os){ $batOSTxt.= '<b>&nbsp;M&nbsp;</b>'.$bat_med_os;}
			  if($bat_high_os){ $batOSTxt.= '<b>&nbsp;H&nbsp;</b>'.$bat_high_os;}
			}
			//------BAT OU WORKS------------------
			if((!empty($bat_nl_ou) && ($bat_nl_ou != "20/")) && (strpos($statusElements, "elem_visBatNlOu=1") !== false) || (!empty($bat_low_ou) && ($bat_low_ou != "20/") && (strpos($statusElements, "elem_visBatLowOu=1") !== false)) || (!empty($bat_med_ou) && ($bat_med_ou != "20/") && (strpos($statusElements, "elem_visBatMedOu=1") !== false)) || (!empty($bat_high_ou) && ($bat_high_ou != "20/") && (strpos($statusElements, "elem_visBatHighOu=1") !== false))){
			 // $batOUTxt.= "<b style='color:#9900cc;'>OU:&nbsp;</b>"; 
			  if($bat_nl_ou){  $batOUTxt.= '<b>&nbsp;NL&nbsp;</b>'.$bat_nl_ou;}
			  if($bat_low_ou){ $batOUTxt.= '<b>&nbsp;L&nbsp;</b>'.$bat_low_ou;}
			  if($bat_med_ou){ $batOUTxt.= '<b>&nbsp;M&nbsp;</b>'.$bat_med_ou;}
			  if($bat_high_ou){ $batOUTxt.= '<b>&nbsp;H&nbsp;</b>'.$bat_high_ou;}
			}
			
			//------PAM OD/OS/OU WORKS------------
			$pamODTxt = $pamOSTxt = $pamOUTxt = "";
			//------PAM OD WORKS------------------
			if((!empty($pam_od_txt_1) && ($pam_od_txt_1 != "20/")) && (strpos($statusElements, "elem_visPamOdTxt1=1") !== false) || (!empty($pam_od_txt_2 ) && ($pam_od_txt_2 != "20/") && (strpos($statusElements, "elem_visPamOdTxt2=1") !== false))){
				
			  //$pamODTxt.= "<b style='color:blue;'>OD:&nbsp;</b>"; 
			  if($pam_od_txt_1 && $pam_od_txt_2){ $pamODTxt.=  $pam_od_txt_1.'&nbsp;-&nbsp;'. $pam_od_txt_2; }
			  else if($pam_od_txt_1){ $pamODTxt.= $pam_od_txt_1; } 
			  else if($pam_od_txt_2){ $pamODTxt.= $pam_od_txt_2; }
			}
			//------PAM OS WORKS------------------
			if((!empty($pam_os_txt_1) && ($pam_os_txt_1 != "20/")) && (strpos($statusElements, "elem_visPamOsTxt1=1") !== false) || (!empty($pam_os_txt_2 ) && ($pam_os_txt_2 != "20/") && (strpos($statusElements, "elem_visPamOsTxt2=1") !== false))){
				
			  //$pamOSTxt.= "<b style='color:green;'>OS:&nbsp;</b>"; 
			  if($pam_os_txt_1 && $pam_os_txt_2){ $pamOSTxt.=  $pam_os_txt_1.'&nbsp;-&nbsp;'. $pam_os_txt_2;} 
			  else if($pam_os_txt_1){ $pamOSTxt.= $pam_os_txt_1; }
			  else if($pam_os_txt_2){ $pamOSTxt.= $pam_os_txt_2; } 
			}
			//------PAM OU WORKS------------------
			if((!empty($pam_ou_txt_1) && ($pam_ou_txt_1 != "20/")) && (strpos($statusElements, "elem_visPamOuTxt1=1") !== false) || (!empty($pam_ou_txt_2) && ($pam_ou_txt_2 != "20/") && (strpos($statusElements, "elem_visPamOuTxt2=1") !== false))){
				
			//------$pamOUTxt.= "<b style='color:#9900cc;'>OU:&nbsp;</b>"; 
			  if($pam_ou_txt_1 && $pam_ou_txt_2){ $pamOUTxt.=  $pam_ou_txt_1.'&nbsp;-&nbsp;'. $pam_ou_txt_1; }
			  else if($pam_ou_txt_1){ $pamOUTxt.= $pam_ou_txt_1; }
			  else if($pam_ou_txt_2){ $pamOUTxt.= $pam_ou_txt_2; }
			}		
		}
		
		//--
		$flg_acuity=0;
		$sql = "
			SELECT 
				sec_name, 
				sec_indx,
				sel_od, 
				sel_os,
				txt_od, 
				txt_os, 
				sel_ou, 
				txt_ou
			FROM 
				`chart_vis_master` c1
				LEFT JOIN chart_acuity c2 ON c1.id = c2.id_chart_vis_master
			WHERE 
				c1.form_id = '".$formId."' 
			AND 
				c1.patient_id = '".$ptId."'
			AND 
				(sec_name = 'Distance' OR sec_name = 'Near')
			AND 
				sec_indx IN (1,2)
		";
		
		$rez = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($rez);$i++){
			$flg_acuity=1;
			$j = $row["sec_indx"];
			if($row["sec_name"] == "Distance"){				
				${"disOdSel" . $j} = $row["sel_od"];
				${"disOsSel" . $j} = $row["sel_os"];
				${"disOdTxt" . $j} = $row["txt_od"];
				${"disOsTxt" . $j} = $row["txt_os"];	
				${"disOuSel" . $j} = $row["sel_ou"];
				${"disOuTxt" . $j} = $row["txt_ou"];				
			}else if($row["sec_name"] == "Near"){
				${"NearOdSel" . $j} = $row["sel_od"];
				${"NearOsSel" . $j} = $row["sel_os"];
				${"NearOdTxt" . $j} = $row["txt_od"];
				${"NearOsTxt" . $j} = $row["txt_os"]; 
				${"NearOuSel" . $j} = $row["sel_ou"];
				${"NearOuTxt" . $j} = $row["txt_ou"];
			}
		}
		
		if(!empty($flg_acuity))
		{			
			//Check for background
			if( (empty($disOdSel1) || (strpos($statusElements, "elem_visDisOdSel1=0") !== false)) &&
			    (empty($disOsSel1) || (strpos($statusElements, "elem_visDisOsSel1=0") !== false)) &&  
			    (empty($disOdTxt1) || ($disOdTxt1 == "20/") || (strpos($statusElements, "elem_visDisOdTxt1=0") !== false)) &&  
			    (empty($disOsTxt1) || ($disOsTxt1 == "20/") || (strpos($statusElements, "elem_visDisOsTxt1=0") !== false)) &&  
			    (empty($disOdSel2) || (strpos($statusElements, "elem_visDisOdSel2=0") !== false)) &&
			    (empty($disOsSel2) || (strpos($statusElements, "elem_visDisOsSel2=0") !== false)) &&  
			    (empty($disOdTxt2) || ($disOdTxt2 == "20/") || (strpos($statusElements, "elem_visDisOdTxt2=0") !== false)) &&  
			    (empty($disOsTxt2) || ($disOsTxt2 == "20/") || (strpos($statusElements, "elem_visDisOsTxt2=0") !== false))
			  )
			{
				// Not Performed
			}
			else
			{
				
				$disOD = (!empty($disOdSel1) || (!empty($disOdTxt1) && ($disOdTxt1 != "20/")) || !empty($disOdSel2) || (!empty($disOdTxt2) && ($disOdTxt2 != "20/")) ) ? "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$this->tOd."&nbsp;&nbsp;".$disOdSel1." - ".$disOdTxt1."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$disOdSel2." - ".$disOdTxt2 : "";//"Not Performed";
				$disOS = (!empty($disOsSel1) || (!empty($disOsTxt1) && ($disOsTxt1 != "20/")) || !empty($disOsSel2) || (!empty($disOsTxt2) && ($disOsTxt2 != "20/")) ) ? "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$this->tOs."&nbsp;&nbsp;".$disOsSel1." - ".$disOsTxt1."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$disOsSel2." - ".$disOsTxt2 : "";//"Not Performed";
				
				//$VCCOD = (!empty($disOdSel1) || (!empty($disOdTxt1) && ($disOdTxt1 != "20/")) || !empty($disOdSel2) || (!empty($disOdTxt2) && ($disOdTxt2 != "20/")) ) ? $disOdSel1." - ".$disOdTxt1."<br/>".$disOdSel2." - ".$disOdTxt2 : "";
				$VCCOD = (!empty($disOdSel1) && (strpos($statusElements, "elem_visDisOdSel1=1") !== false) || (!empty($disOdTxt1) && ($disOdTxt1 != "20/") && (strpos($statusElements, "elem_visDisOdTxt1=1") !== false))) ? $disOdSel1." - ".$disOdTxt1 : "";
				//$VCCOD_Br_Tag = (!empty($VCCOD)) ? "<br/>" : "";
				$VSCOD = (!empty($disOdSel2) && (strpos($statusElements, "elem_visDisOdSel2=1") !== false) || (!empty($disOdTxt2) && ($disOdTxt2 != "20/") && (strpos($statusElements, "elem_visDisOdTxt2=1") !== false))) ? $disOdSel2." - ".$disOdTxt2 : "";
				$VCCOS = (!empty($disOsSel1) && (strpos($statusElements, "elem_visDisOsSel1=1") !== false) || (!empty($disOsTxt1) && ($disOsTxt1 != "20/") && (strpos($statusElements, "elem_visDisOsTxt1=1") !== false))) ? $disOsSel1." - ".$disOsTxt1 : "";
				//$VCCOS_Br_Tag = (!empty($VCCOS)) ? "<br/>" : "";
				$VSCOS = (!empty($disOsSel2) && (strpos($statusElements, "elem_visDisOdSel2=1") !== false) || (!empty($disOsTxt2) && ($disOsTxt2 != "20/") && (strpos($statusElements, "elem_visDisOsTxt2=1") !== false))) ? $disOsSel2." - ".$disOsTxt2 : "";
				
				//=======DISTANCE/VISION OU FIELD WORK==========
				$VCCOU = (!empty($disOuSel1) && (strpos($statusElements, "elem_visDisOuSel1=1") !== false) || (!empty($disOuTxt1) && ($disOuTxt1 != "20/") && (strpos($statusElements, "elem_visDisOuTxt1=1") !== false))) ? $disOuSel1." - ".$disOuTxt1 : "";
				
				$VSCOU = (!empty($disOuSel2) && (strpos($statusElements, "elem_visDisOuSel2=1") !== false) || (!empty($disOuTxt2) && ($disOuTxt2 != "20/") && (strpos($statusElements, "elem_visDisOuTxt2=1") !== false))) ? $disOuSel2." - ".$disOuTxt2 : "";
			   
				
				//Replace Array
				//print_r($VCCOD);
				$CCOD=$CCOS=$SCOD=$SCOS= $CCOU = $SCOU = $PHOD = $PHOS = '';
				$SCDVAOD=$SCDVAOS=$CCDVAOD=$CCDVAOS= $SCNVAOD=$SCNVAOS=$CCNVAOD=$CCNVAOS ='';
				
				if((strpos($VCCOD, 'SC')!==false && strpos($VCCOS, 'SC')!==false) &&  (strpos($VSCOD, 'CC')!==false && strpos($VSCOS, 'CC')!==false))
				{
					$SCOD=$VCCOD; $SCOS=$VCCOS;
					$CCOD=$VSCOD; $CCOS=$VSCOS;
					//------NEW VARIABLES CREATED REQUEST BY MIRAMAR SERVER
					$SCDVAOD=$VCCOD; $SCDVAOS=$VCCOS;
					$CCDVAOD=$VSCOD; $CCDVAOS=$VSCOS;
					//------------------------------------------------------
				
				}
				else if((strpos($VCCOD, 'CC')!==false && strpos($VCCOS, 'CC')!==false) &&  (strpos($VSCOD, 'SC')!==false && strpos($VSCOS, 'SC')!==false))
				{
					$CCOD=$VCCOD; $CCOS=$VCCOS;
					$SCOD=$VSCOD; $SCOS=$VSCOS;
					//------NEW VARIABLES CREATED REQUEST BY MIRAMAR SERVER
					$CCDVAOD=$VCCOD; $CCDVAOS=$VCCOS;
					$SCDVAOD=$VSCOD; $SCDVAOS=$VSCOS;
					//------------------------------------------------------
				}
				else if((strpos($VCCOD, 'CC')!==false && strpos($VCCOS, 'CC')!==false) &&  (empty($VSCOD) ==true && empty($VSCOS)==true))
				{
					$CCOD=$VCCOD; $CCOS=$VCCOS;
					$SCOD=$VSCOD; $SCOS=$VSCOS;
					
					//------NEW VARIABLES CREATED REQUEST BY MIRAMAR SERVER
					$CCDVAOD=$VCCOD; $CCDVAOS=$VCCOS;
					$SCDVAOD=$VSCOD; $SCDVAOS=$VSCOS;
				}
				else if((strpos($VCCOD, 'SC')!==false && strpos($VCCOS, 'SC')!==false) &&  (empty($VSCOD) ==true && empty($VSCOS)==true))
				{
					$SCOD=$VCCOD; $SCOS=$VCCOS;
					$CCOD=$VSCOD; $CCOS=$VSCOS;
					
					//------NEW VARIABLES CREATED REQUEST BY MIRAMAR SERVER
					$SCDVAOD=$VCCOD; $SCDVAOS=$VCCOS;
					$CCDVAOD=$VSCOD; $CCDVAOS=$VSCOS;
				}
				else if((strpos($VCCOD, 'SC')!==false && strpos($VCCOS, 'SC')!==false) &&  (strpos($VSCOD, 'PH')!==false && strpos($VSCOS, 'PH')!==false))
				{  
					$SCOD=$VCCOD; $SCOS=$VCCOS;
					$CCOD=$VSCOD; $CCOS=$VSCOS;
					
					//------NEW VARIABLES CREATED REQUEST BY MIRAMAR SERVER
					$SCDVAOD=$VCCOD; $SCDVAOS=$VCCOS;
					$CCDVAOD=$VSCOD; $CCDVAOS=$VSCOS;
				}
				else
				{
					$CCOD=$VCCOD; $CCOS=$VCCOS;
					$SCOD=$VSCOD; $SCOS=$VSCOS;
					//------NEW VARIABLES CREATED REQUEST BY MIRAMAR SERVER
					$CCDVAOD=$VCCOD; $CCDVAOS=$VCCOS;
					$SCDVAOD=$VSCOD; $SCDVAOS=$VSCOS;
					//------------------------------------------------------
				}
			
				//----------OU DATA ARRANGEMENT------------------------------
				
				if(strpos($VCCOU, 'CC')!==false){
					$CCOU=$VCCOU; $SCOU=$VSCOU; 
				}else if(strpos($VSCOU, 'CC')!==false){
					$CCOU=$VSCOU; $SCOU=$VCCOU; 
				}else if(strpos($VSCOU, 'CC')!==false){
					$CCOU=$VSCOU; $SCOU=$VCCOU; 
				}else if(strpos($VSCOU, 'CC')!==false){
					$CCOU=$VSCOU; $SCOU=$VCCOU; 
				}
				
				//----------PINHOLE OD/OS DATA FOR NEW PINHOLE VARIABLES(PINHOLE_OD_1, PINHOLE_OS_1)
				if(strpos($CCOD, 'PH')!==false)
				{
					$PHOD = $CCOD;
					$CCOD = "";
				}else if(strpos($SCOD, 'PH')!==false)
				{ 
					$PHOD = $SCOD;
					$SCOD = "";
				}
				if(strpos($CCOS, 'PH')!==false)
				{ 
					$PHOS = $CCOS;
					$CCOS = "";
				}else if(strpos($SCOS, 'PH')!==false)
				{ 
					$PHOS = $SCOS;
					$SCOS = "";
				}
				
				/*if((strpos($CCOD, 'PH')!==false) || (strpos($CCOD, 'GL')!==false)){ $CCOD = "";}
				if((strpos($CCOS, 'PH')!==false) || (strpos($CCOS, 'GL')!==false)){ $CCOS = "";}
				if((strpos($CCOU, 'PH')!==false) || (strpos($CCOU, 'GL')!==false) || (strpos($CCOU, 'GPCL')!==false)){ $CCOU = "";}
				if((strpos($SCOD, 'PH')!==false) || (strpos($SCOD, 'GL')!==false)){ $SCOD = "";}
				if((strpos($SCOS, 'PH')!==false) || (strpos($SCOS, 'GL')!==false)){ $SCOS = "";}
				if((strpos($SCOU, 'PH')!==false) || (strpos($SCOU, 'GL')!==false) || (strpos($SCOU, 'GPCL')!==false)){ $SCOU = "";}*/
				
			}
			
			//-------------NEAR SECTION VALUE WORK STARTS HERE------------
			if( (empty($NearOdSel1) || (strpos($statusElements, "elem_visNearOdSel1=0") !== false)) &&
			    (empty($NearOsSel1) || (strpos($statusElements, "elem_visNearOsSel1=0") !== false)) &&  
			    (empty($NearOdTxt1) || ($NearOdTxt1 == "20/") || (strpos($statusElements, "elem_visNearOdTxt1=0") !== false)) &&  
			    (empty($NearOsTxt1) || ($NearOsTxt1 == "20/") || (strpos($statusElements, "elem_visNearOsTxt1=0") !== false)) 
			  ){
				// Not Performed
			}else{
				$NearOD = (!empty($NearOdSel1) && (strpos($statusElements, "elem_visNearOdSel1=1") !== false) || (!empty($NearOdTxt1) && ($NearOdTxt1 != "20/") && (strpos($statusElements, "elem_visNearOdTxt1=1") !== false))) ? $NearOdSel1." - ".$NearOdTxt1 : "";
				
				$NearOS = (!empty($NearOsSel1) && (strpos($statusElements, "elem_visDisOsSel1=1") !== false) || (!empty($NearOsTxt1) && ($NearOsTxt1 != "20/") && (strpos($statusElements, "elem_visNearOsTxt1=1") !== false))) ? $NearOsSel1." - ".$NearOsTxt1 : "";
				
				$NearOU = (!empty($NearOuSel1) && (strpos($statusElements, "elem_visNearOuSel1=1") !== false) || (!empty($NearOuTxt1) && ($NearOuTxt1 != "20/") && (strpos($statusElements, "elem_visNearOuTxt1=1") !== false))) ? $NearOuSel1." - ".$NearOuTxt1 : "";
				
				//----------NEAR SECTION SECOND SELL TEXT DISPLAYED---------
				
				$NearOD2 = (!empty($NearOdSel1) && (strpos($statusElements, "elem_visNearOdSel2=1") !== false) || (!empty($NearOdTxt2) && ($NearOdTxt2 != "20/") && (strpos($statusElements, "elem_visNearOdTxt2=1") !== false))) ? $NearOdSel2." - ".$NearOdTxt2 : "";
				
				$NearOS2 = (!empty($NearOsSel1) && (strpos($statusElements, "elem_visDisOsSel2=1") !== false) || (!empty($NearOsTxt2) && ($NearOsTxt2 != "20/") && (strpos($statusElements, "elem_visNearOsTxt2=1") !== false))) ? $NearOsSel2." - ".$NearOsTxt2 : "";
				
				$NearOU2 = (!empty($NearOuSel1) && (strpos($statusElements, "elem_visNearOuSel2=1") !== false) || (!empty($NearOuTxt2) && ($NearOuTxt2 != "20/") && (strpos($statusElements, "elem_visNearOuTxt2=1") !== false))) ? $NearOuSel2." - ".$NearOuTxt2 : "";
				
			    //----------NEAR SECTION CROSS SELECTION SC/CC DATA REPLACE ACCORDING TO VARIABLE NAME
				if((strpos($NearOD2, 'SC')!==false && strpos($NearOS2, 'SC')!==false) &&  (strpos($NearOD, 'CC')!==false && strpos($NearOS, 'CC')!==false)){
					$SCNVAOD=$NearOD2; $SCNVAOS=$NearOS2;
					$CCNVAOD=$NearOD; $CCNVAOS=$NearOS;
					
				
				}else if((strpos($NearOD2, 'CC')!==false && strpos($NearOS2, 'CC')!==false) &&  (strpos($NearOD, 'SC')!==false && strpos($NearOS, 'SC')!==false)){
					$CCNVAOD=$NearOD2; $CCNVAOS=$NearOS2;
					$SCNVAOD=$NearOD; $SCNVAOS=$NearOS;
					
				}else{
					
					$CCNVAOD=$NearOD2; $CCNVAOS=$NearOS2;
					$SCNVAOD=$NearOD; $SCNVAOS=$NearOS;
					
				}
			}
			//------------ENDS HERE------------------------------
		}
		
		$flg_sca=0;
		$sql = "
			SELECT 
				sec_name,
				s_od, 
				c_od, 
				a_od, 
				sel_od, 
				s_os, 
				c_os, 
				a_os, 
				sel_os 
			FROM 
				`chart_vis_master` c1
				LEFT JOIN chart_sca c2 ON c1.id = c2.id_chart_vis_master
			WHERE 
				c1.form_id = '".$formId."' 
			AND 
				c1.patient_id = '".$ptId."'
			AND 
				(sec_name = \"AR\" OR sec_name = \"ARC\")			
		";
		$rez = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($rez);$i++){
			$flg_sca=1;
			if($row["sec_name"] == "AR"){
				
				//------AUTOREFRACTION OD/OS DATA------------
				$ar_od_s = $res["s_od"];
				$ar_od_c = $res["c_od"];
				$ar_od_a = $res["a_od"];
				$ar_od_sel_1 = $res["sel_od"];
				
				$ar_os_s = $res["s_os"];
				$ar_os_c = $res["c_os"];
				$ar_os_a = $res["a_os"];
				$ar_os_sel_1 = $res["sel_os"];
			}
			else if($row["sec_name"] == "ARC")
			{
				
				//------CYCLOPLEGIC OD/OS DATA----------------
				$CycArOdS = $res["s_od"];
				$CycArOdC = $res["c_od"];
				$CycArOdA = $res["a_od"];
				$CycArOdSel1 = $res["sel_od"];
				
				$CycArOsS = $res["s_os"];
				$CycArOsC = $res["c_os"];
				$CycArOsA = $res["a_os"];
				$CycArOsSel1 = $res["sel_os"];
			}			
		}
		
		if(!empty($flg_sca)){
			
			//------AUTOREFRACTION OD/OS WORKS------------------
			$arOD = $arOS = "";
			//------AUTOREFRACTION OD WORKS---------------------
			if((!empty($ar_od_s)) && (strpos($statusElements, "elem_visArOdS=1") !== false) || (!empty($ar_od_c) && (strpos($statusElements, "elem_visArOdC=1") !== false)) || (!empty($ar_od_a) && (strpos($statusElements, "elem_visArOdA=1") !== false)) || (!empty($ar_od_sel_1) && (strpos($statusElements, "elem_visArOdSel1=1") !== false))){
				
			  //$arOD.= "<b style='color:blue;'>OD:&nbsp;</b>"; 
			  if($ar_od_s){ $arOD.= '<b>&nbsp;S&nbsp;</b>'.$ar_od_s;}
			  if($ar_od_c){ $arOD.= '<b>&nbsp;C&nbsp;</b>'.$ar_od_c;}
			  if($ar_od_a){ $arOD.= '<b>&nbsp;A&nbsp;</b>'.$ar_od_a;}
			  if($ar_od_sel_1){ $arOD.= '<b>&nbsp;</b>'.$ar_od_sel_1;}
			}
			//------AUTOREFRACTION OS WORKS----------------------
			if((!empty($ar_os_s)) && (strpos($statusElements, "elem_visArOsS=1") !== false) || (!empty($ar_os_c) && (strpos($statusElements, "elem_visArOsC=1") !== false)) || (!empty($ar_os_a) && (strpos($statusElements, "elem_visArOsA=1") !== false)) || (!empty($ar_os_sel_1) && (strpos($statusElements, "elem_visArOsSel1=1") !== false))){
			 // $arOS.= "<b style='color:green;'>OS:&nbsp;</b>"; 
			  if($ar_os_s){ $arOS.= '<b>&nbsp;S&nbsp;</b>'.$ar_os_s;}
			  if($ar_os_c){ $arOS.= '<b>&nbsp;C&nbsp;</b>'.$ar_os_c;}
			  if($ar_os_a){ $arOS.= '<b>&nbsp;A&nbsp;</b>'.$ar_os_a;}
			  if($ar_os_sel_1){ $arOS.= '<b>&nbsp;</b>'.$ar_os_sel_1;}
			}
			
			//------CYCLOPLEGIC OD/OS WORKS-----------------------
			$CycAROD = $CycAROS = "";
			//------CYCLOPLEGIC OD WORKS--------------------------
			if((!empty($CycArOdS)) && (strpos($statusElements, "elem_visCycArOdS=1") !== false) || (!empty($CycArOdC) && (strpos($statusElements, "elem_visCycArOdC=1") !== false)) || (!empty($CycArOdA) && (strpos($statusElements, "elem_visCycArOdA=1") !== false)) || (!empty($CycArOdSel1) && (strpos($statusElements, "elem_visCycArOdSel1=1") !== false))){
				
			 // $CycAROD.= "<b style='color:blue;'>OD:&nbsp;</b>"; 
			  if($CycArOdS){ $CycAROD.= '<b>&nbsp;S&nbsp;</b>'.$CycArOdS;}
			  if($CycArOdC){ $CycAROD.= '<b>&nbsp;C&nbsp;</b>'.$CycArOdC;}
			  if($CycArOdA){ $CycAROD.= '<b>&nbsp;A&nbsp;</b>'.$CycArOdA;}
			  if($CycArOdSel1){ $CycAROD.= '<b>&nbsp;</b>'.$CycArOdSel1;}
			}
			
			//------CYCLOPLEGIC OS WORKS----------------------------
			if((!empty($CycArOsS)) && (strpos($statusElements, "elem_visCycArOsS=1") !== false) || (!empty($CycArOsC) && (strpos($statusElements, "elem_visCycArOsC=1") !== false)) || (!empty($CycArOsA) && (strpos($statusElements, "elem_visCycArOsA=1") !== false)) || (!empty($CycArOsSel1) && (strpos($statusElements, "elem_visCycArOsSel1=1") !== false))){
			 // $CycAROS.= "<b style='color:green;'>OS:&nbsp;</b>"; 
			  if($CycArOsS){ $CycAROS.= '<b>&nbsp;S&nbsp;</b>'.$CycArOsS;}
			  if($CycArOsC){ $CycAROS.= '<b>&nbsp;C&nbsp;</b>'.$CycArOsC;}
			  if($CycArOsA){ $CycAROS.= '<b>&nbsp;A&nbsp;</b>'.$CycArOsA;}
			  if($CycArOsSel1){ $CycAROS.= '<b>&nbsp;</b>'.$CycArOsSel1;}
			}
			//------------------------------------------------------
			
		}else{
			$arOD = $arOS = "";
			$CycAROD = $CycAROS = "";
		}
		//BELOW CONDITION IS COMMENTED BECAUSE ALL THE VISION VARIABLES STOPPED WORKING UNLESS THIS TRUE.
		//if(!empty($flg_sca) && !empty($flg_acuity)){
			$arrRep = array($disOD.$disOS,$CCOD,$CCOS,$SCOD,$SCOS,$NearOD,$NearOS,$KRTOD,$KRTOS,$CCOU,$SCOU,$NearOU,$pamODTxt,$pamOSTxt,$pamOUTxt,$batODTxt,$batOSTxt,$batOUTxt,$arOD,$arOS,$CycAROD,$CycAROS,$SCDVAOD,$SCDVAOS,$CCDVAOD,$CCDVAOS,$SCNVAOD,$SCNVAOS,$CCNVAOD,$CCNVAOS,$PHOD,$PHOS);
		//}
		
		//Replace
		$str = $this->refineData($str, $this->kVision, $arrRep, $this->tVision,"");
		return $str;
	}
	
	//Get MR1, MR2, MR3
	private function getMr1Mr2Mr3($str, $ptId, $formId){
		global $web_RootDirectoryName;
		$MR1 = $MR2 = $MR3 = "";
		
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
			
			// MR1 GLARE VALUE
			if(!empty($Mr1_vis_mr_od_sel_2) && strpos($Mr1_vis_mr_od_sel_2, 'GL')!==false && !empty($Mr1_visMrOdSel2Vision) && $Mr1_visMrOdSel2Vision !== "20/" && $Mr1_visMrOdSel2Vision!= ""){
				${"MR".$exnum."_GLAREOD"} = $Mr1_vis_mr_od_sel_2.' - '.$Mr1_visMrOdSel2Vision;
			}
			if(!empty($Mr1_vis_mr_os_sel_2) && strpos($Mr1_vis_mr_os_sel_2, 'GL')!==false && !empty($Mr1_visMrOsSel2Vision) && $Mr1_visMrOsSel2Vision !== "20/" && $Mr1_visMrOsSel2Vision!= ""){
				${"MR".$exnum."_GLAREOS"} = $Mr1_vis_mr_os_sel_2.' - '.$Mr1_visMrOsSel2Vision;
			}
		}
		
		$arrRep = array($MR1,$MR2,$MR3,$MR1_GLAREOD,$MR1_GLAREOS,$MR2_GLAREOD,$MR2_GLAREOS,$MR3_GLAREOD,$MR3_GLAREOS);
		//Replace
		$str = $this->refineData($str, $this->kMr1Mr2Mr3, $arrRep, $this->tVision,"");
		return $str;
	}
	
	//get PC1,PC2,PC3
		private function getPc1Pc2Pc3($str, $ptId, $formId){
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
			//Replace
			$str = $this->refineData($str, $this->kPc1Pc2Pc3, $arrRep, $this->tVision,"");
			return $str;
		
		}	
	
	//Get Pupil
	private function getPupil($str, $ptId, $formId){
		$sql = "SELECT sumOdPupil,sumOsPupil,wnlPupilOd,wnlPupilOs,wnl_value FROM chart_pupil WHERE formId = '".$formId."' AND patientId = '".$ptId."'  AND purged='0'  ";
		$res = sqlQuery($sql);
		if($res !== false){
			$oCn = new ChartNote($ptId, $formId);
			$wnlString = !empty($res["wnl_value"]) ? $res["wnl_value"] : $oCn->getExamWnlStr("Pupil");
			$pupilOd = ($res["wnlPupilOd"] == 0) ? $res["sumOdPupil"] : $wnlString;
			$pupilOs = ($res["wnlPupilOs"] == 0) ? $res["sumOsPupil"] : $wnlString;
			
			$pupilOd = (!empty($pupilOd)) ? $this->tOd.$this->clearSumm(
$pupilOd) : "";
			$pupilOs = (!empty($pupilOs)) ? $this->tOs.$this->clearSumm(
$pupilOs) : "";
			$pupilOu = "";
			$pupilOu .= (!empty($pupilOd)) ? $pupilOd : ""; //$this->tOd."Not Performed";
			$pupilOu .= (!empty($pupilOs)) ? $pupilOs : ""; //$this->tOs."Not Performed";
			if(empty($pupilOd) || empty($pupilOs)){
				$pupilOu = "";
			}			
			
			//Replace Array
			$arrRep = array($pupilOu, $pupilOd, $pupilOs);
		}
		
		//Replace
		$str = $this->refineData($str, $this->kPupil, $arrRep, $this->tPupil,"");
		return $str;
	}
	
	//Get Exter
	private function getExter($str, $ptId, $formId){
		$sql = "SELECT external_exam_summary, sumOsEE,wnlEeOd,wnlEeOs, wnl_value FROM chart_external_exam WHERE form_id = '".$formId."' AND patient_id = '".$ptId."'  AND purged='0' ";
		$res = sqlQuery($sql);
		if($res !== false){
			$oCn = new ChartNote($ptId, $formId);
			$wnlString = !empty($res["wnl_value"]) ? $res["wnl_value"] : $oCn->getExamWnlStr("External");
			$sumOd = ($res["wnlEeOd"] == 0) ? $this->clearSumm($res["external_exam_summary"]) : $wnlString;
			$sumOs = ($res["wnlEeOs"] == 0) ? $this->clearSumm($res["sumOsEE"]) : $wnlString;
			
			$sumOd = (!empty($sumOd)) ? $this->tOd.$sumOd : "";
			$sumOs = (!empty($sumOs)) ? $this->tOs.$sumOs : "" ;
			$sumOu = "";
			$sumOu .= (!empty($sumOd)) ? $sumOd : "";//$this->tOd."Not Performed";
			$sumOu .= (!empty($sumOs)) ? $sumOs : "";//$this->tOs."Not Performed";
			if(empty($sumOd) && empty($sumOd)){
				$sumOu = "";
			}
			
			//Replace Array
			$arrRep = array($sumOu, $sumOd, $sumOs);
		}
		
		//Replace
		$str = $this->refineData($str, $this->kExter, $arrRep, $this->tExter,"");
		return $str;
	}

	//Get LA
	private function getLA($str, $ptId, $formId){
		$sql = "SELECT 
					c2.sumLidsOs,
					c2.wnlLidsOd, 
					c2.wnlLidsOs, 
					c2.wnl_value_Lids, 
					c2.lid_conjunctiva_summary, 
					
					c3.sumLesionOs,
					c3.wnlLesionOd, 
					c3.wnlLesionOs,
					c3.wnl_value_Lesion,
					c3.lesion_summary,
					
					c4.wnlLidPosOd, 
					c4.wnlLidPosOs,
					c4.sumLidPosOs, 
					c4.wnl_value_LidPos, 
					c4.lid_deformity_position_summary,

					c5.sumLacOs, 
					c5.wnlLacSysOd,
					c5.wnlLacSysOs,	
					c5.wnl_value_LacSys, 
					c5.lacrimal_system_summary,
					
				CONCAT(
					c2.statusElem,
					c3.statusElem,
					c4.statusElem,
					c5.statusElem
				) AS statusElem 

				FROM 
					chart_master_table c1 
					INNER JOIN chart_lids c2 ON c1.id = c2.form_id 
					INNER JOIN chart_lesion c3 ON c1.id = c3.form_id 
					INNER JOIN chart_lid_pos c4 ON c1.id = c4.form_id 
					INNER JOIN chart_lac_sys c5 ON c1.id = c5.form_id 
				WHERE 
					c1.id = '".$formId."' 
					AND c1.patient_id = '".$ptId."'  
					AND c1.purge_status='0' 
					AND c1.delete_status='0' 
					AND c2.purged='0' 
					AND c3.purged='0' 
					AND c4.purged='0' 
					AND c5.purged='0'";
		$res = sqlQuery($sql);
		if($res !== false){
			$oCn = new ChartNote($ptId, $formId);
			$wnlStringLids = !empty($res["wnl_value_Lids"]) ? $res["wnl_value_Lids"] : $oCn->getExamWnlStr("Lids");
			$wnlStringLidPos = !empty($res["wnl_value_LidPos"]) ? $res["wnl_value_LidPos"] :  $oCn->getExamWnlStr("Lid Position");
			$wnlStringLesion = !empty($res["wnl_value_Lesion"]) ? $res["wnl_value_Lesion"] :  $oCn->getExamWnlStr("Lesion");
			$wnlStringLac = !empty($res["wnl_value_LacSys"]) ? $res["wnl_value_LacSys"] :  $oCn->getExamWnlStr("Lacrimal System");
			
			$sumLidsOd = ($res["wnlLidsOd"] == 0) ? $this->clearSumm($res["lid_conjunctiva_summary"]) : $wnlStringLids;
			$sumLidPosOd = ($res["wnlLidPosOd"] == 0) ? $this->clearSumm($res["lid_deformity_position_summary"]) : $wnlStringLidPos;
			$sumLesionOd = ($res["wnlLesionOd"] == 0) ? $this->clearSumm($res["lesion_summary"]) : $wnlStringLesion;
			$sumLacOd = ($res["wnlLacSysOd"] == 0) ? $this->clearSumm($res["lacrimal_system_summary"]) : $wnlStringLac;
			
			$sumLidsOs = ($res["wnlLidsOs"] == 0) ? $this->clearSumm($res["sumLidsOs"]) : $wnlStringLids;
			$sumLidPosOs = ($res["wnlLidPosOs"] == 0) ? $this->clearSumm($res["sumLidPosOs"]) : $wnlStringLidPos ;
			$sumLesionOs = ($res["wnlLesionOs"] == 0) ? $this->clearSumm($res["sumLesionOs"]) : $wnlStringLesion;
			$sumLacOs = ($res["wnlLacSysOs"] == 0) ? $this->clearSumm($res["sumLacOs"]) : $wnlStringLac;
			
			$arrOd = array();
			$arrOs = array();			
			if(!empty($sumLidsOd) && strpos($res["statusElem"],"elem_chng_div1_Od=1") !== false){
				$arrOd[] = "<b>Lids</b>&nbsp;".$sumLidsOd."</br>";
			}
			
			if(!empty($sumLidsOs) && strpos($res["statusElem"],"elem_chng_div1_Os=1") !== false){
				$arrOs[] = "<b>Lids</b>&nbsp;".$sumLidsOs."</br>";
			}
			
			if(!empty($sumLesionOd) && strpos($res["statusElem"],"elem_chng_div2_Od=1") !== false){
				$arrOd[] = "<b>Lesion</b>&nbsp;".$sumLesionOd."</br>";
			}			
			
			if(!empty($sumLesionOs) && strpos($res["statusElem"],"elem_chng_div2_Os=1") !== false){
				$arrOs[] = "<b>Lesion</b>&nbsp;".$sumLesionOs."</br>";
			}			
			
			if(!empty($sumLidPosOd) && strpos($res["statusElem"],"elem_chng_div3_Od=1") !== false){
				$arrOd[] = "<b>Lid Position</b>&nbsp;".$sumLidPosOd."</br>";
			}			
			
			if(!empty($sumLidPosOs) && strpos($res["statusElem"],"elem_chng_div3_Os=1") !== false){
				$arrOs[] = "<b>Lid Position</b>&nbsp;".$sumLidPosOs."</br>";
			}
			
			if(!empty($sumLacOd) && strpos($res["statusElem"],"elem_chng_div4_Od=1") !== false){
				$arrOd[] = "<b>Lacrimal</b>&nbsp;".$sumLacOd."</br>";
			}
			
			if(!empty($sumLacOs) && strpos($res["statusElem"],"elem_chng_div4_Os=1") !== false){
				$arrOs[] = "<b>Lacrimal</b>&nbsp;".$sumLacOs."</br>";
			}
			
			$sumOd = "";
			if(count($arrOd) > 0){
				foreach($arrOd as $key => $val){
					$sumOd .= (!empty($val)) ? $val : "";
				}				
				//Last comma removal
				$sumOd = preg_replace("/,\s*$/", "", $sumOd);				
			}
			
			$sumOs = "";
			if(count($arrOs) > 0){
				foreach($arrOs as $key => $val){
					$sumOs .= (!empty($val)) ? $val : "";
				}				
				//Last comma removal
				$sumOs = preg_replace("/,\s*$/", "", $sumOs);
			}
			
			$sumOu = trim($sumOd." ".$sumOs);
			
			//Replace Array
			$arrRep = array($sumOu, $sumOd, $sumOs);
		}
		
		//Replace
		$str = $this->refineData($str, $this->kLa, $arrRep, $this->tLa,"");
		return $str;
	}
	
	//L&A in Table Format
	private function getLnA($str, $ptId, $formId){
		$sql = "SELECT 
				  c2.sumLidsOs, 
				  c2.wnlLidsOd,
				  c2.wnlLidsOs,
				  c2.wnl_value_Lids,
				  c2.lid_conjunctiva_summary, 
				  
				  c3.sumLesionOs,
				  c3.wnlLesionOd,
				  c3.wnlLesionOs,
				  c3.lesion_summary,
				  c3.wnl_value_Lesion,
				  
				  c4.wnlLidPosOd, 
				  c4.wnlLidPosOs, 
				  c4.sumLidPosOs,
				  c4.wnl_value_LidPos,
				  c4.lid_deformity_position_summary, 

				  c5.sumLacOs,
				  c5.wnlLacSysOd,
				  c5.wnlLacSysOs,
				  c5.wnl_value_LacSys, 
				  c5.lacrimal_system_summary 
				FROM 
				  chart_master_table c1  
				  INNER JOIN chart_lids c2 ON c1.id = c2.form_id 
				  INNER JOIN chart_lesion c3 ON c1.id = c3.form_id 
				  INNER JOIN chart_lid_pos c4 ON c1.id = c4.form_id 
				  INNER JOIN chart_lac_sys c5 ON c1.id = c5.form_id 
				WHERE 
				  c1.id = '".$formId."' 
				  AND c1.patient_id = '".$ptId."'  
				  AND c1.purge_status='0' 
				  AND c1.delete_status='0' 
				  AND c2.purged='0' 
				  AND c3.purged='0' 
				  AND c4.purged='0' 
				  AND c5.purged='0'";
			 
		$res = sqlQuery($sql);
		if($res !== false){
			$oCn = new ChartNote($ptId, $formId);
			$wnlStringLids = !empty($res["wnl_value_Lids"]) ? $res["wnl_value_Lids"] : $oCn->getExamWnlStr("Lids");
			$wnlStringLidPos = !empty($res["wnl_value_LidPos"]) ? $res["wnl_value_LidPos"] :  $oCn->getExamWnlStr("Lid Position");
			$wnlStringLesion = !empty($res["wnl_value_Lesion"]) ? $res["wnl_value_Lesion"] :  $oCn->getExamWnlStr("Lesion");
			$wnlStringLac = !empty($res["wnl_value_LacSys"]) ? $res["wnl_value_LacSys"] :  $oCn->getExamWnlStr("Lacrimal System");
			
			$sumLidsOd = ($res["wnlLidsOd"] == 0) ? $this->clearSumm($res["lid_conjunctiva_summary"]) : $wnlStringLids;
			$sumLidPosOd = ($res["wnlLidPosOd"] == 0) ? $this->clearSumm($res["lid_deformity_position_summary"]) : $wnlStringLidPos;
			$sumLesionOd = ($res["wnlLesionOd"] == 0) ? $this->clearSumm($res["lesion_summary"]) : $wnlStringLesion;
			$sumLacOd = ($res["wnlLacSysOd"] == 0) ? $this->clearSumm($res["lacrimal_system_summary"]) : $wnlStringLac;
			
			$sumLidsOs = ($res["wnlLidsOs"] == 0) ? $this->clearSumm($res["sumLidsOs"]) : $wnlStringLids;
			$sumLidPosOs = ($res["wnlLidPosOs"] == 0) ? $this->clearSumm($res["sumLidPosOs"]) : $wnlStringLidPos ;
			$sumLesionOs = ($res["wnlLesionOs"] == 0) ? $this->clearSumm($res["sumLesionOs"]) : $wnlStringLesion;
			$sumLacOs = ($res["wnlLacSysOs"] == 0) ? $this->clearSumm($res["sumLacOs"]) : $wnlStringLac;
			
			$LnA_val = 0;		
			$table = "";
			
			if($sumLidsOd || $sumLidsOs){
				$LnA_val = 1;		
				$table.= '<tr>
							<td style="width:45%;vertical-align:top;"><b>Lids</b>&nbsp;'.$sumLidsOd.'</td>
							<td style="width:45%;vertical-align:top;"><b>Lids</b>&nbsp;'.$sumLidsOs.'</td>
						 </tr>';
			}
			if($sumLesionOd || $sumLesionOs){
				$LnA_val = 1;
				$table.= '<tr>
							<td style="width:45%;vertical-align:top;"><b>Lesion</b>&nbsp;'.$sumLesionOd.'</td>
							<td style="width:45%;vertical-align:top;"><b>Lesion</b>&nbsp;'.$sumLesionOs.'</td>
						 </tr>';			
			}
			if($sumLidPosOd || $sumLidPosOs){
				$LnA_val = 1;
				$table.= '<tr>
							<td style="width:45%;vertical-align:top;"><b>Lid Position</b>&nbsp;'.$sumLidPosOd.'</td>
							<td style="width:45%;vertical-align:top;"><b>Lid Position</b>&nbsp;'.$sumLidPosOs.'</td>
						 </tr>';		
			}
			if($sumLacOd || $sumLacOs){
				$LnA_val = 1;
				$table.= '<tr>
							<td style="width:45%;vertical-align:top;"><b>Lacrimal</b>&nbsp;'.$sumLacOd.'</td>
							<td style="width:45%;vertical-align:top;"><b>Lacrimal</b>&nbsp;'.$sumLacOs.'</td>
						 </tr>';		
			}
			$table_h ='<table cellpadding="0" border="0" cellspacing="0" style="width:100%">';
			$table_f ='</table>';
			if($LnA_val == 1){
				$table = $table_h.$table.$table_f;	
			}
			//Replace Array
			$arrRep = array($table);
		}
		
		//Replace
		$str = $this->refineData($str, $this->kLna, $arrRep, $this->tLna,"");
		return $str;
	}
	
	
	//Get IOP
	private function getIOP($str, $ptId, $formId){
		global $web_RootDirectoryName;
		
		$sql = "SELECT sumOdIop, sumOsIop,trgtOd,trgtOs,multiple_pressure FROM chart_iop WHERE form_id = '".$formId."' AND patient_id = '".$ptId."' AND purged='0'  ";
		$res = sqlQuery($sql) ;
		
		//---GFS Target IOP OD/OS Variables Replacement Work Starts HERE---
		$gfs_sql = "SELECT iopTrgtOd, iopTrgtOs FROM `glucoma_main` WHERE patientId = '".$ptId."' AND activate='1' ";
		$gfs_res = sqlQuery($gfs_sql) ;
	
		if($res !== false || $gfs_res !== false){
			$sumOd 				= $res["sumOdIop"];
			$sumOs 				= $res["sumOsIop"];
			$trtOd 				= $res["trgtOd"];
			$trtOs 				= $res["trgtOs"];
			$multiple_pressure 	= $res["multiple_pressure"];
			$arrReplace = array("/Trgt:","/ Trgt:",$trtOd."/ ",$trtOs."/ ","Trgt:",$trtOd,$trtOs);
			$sumOd = str_ireplace($arrReplace,"",$sumOd);
			$sumOs = str_ireplace($arrReplace,"",$sumOs);
			$sum = (!empty($sumOd)) ? "OD: ".$sumOd : "";			
			$sum .= (!empty($sumOs) && !empty($sumOd)) ? "&nbsp;" : "";			
			$sum .= (!empty($sumOs)) ? "OS: ".$sumOs : "";
			
			//Pachy data split from IOP data by Kaushal
			preg_match_all ("/Pachy:(.*)<br>/U", $sumOd, $sumOd_arr);
			preg_match_all ("/Pachy:(.*)<br>/U", $sumOs, $sumOs_arr);
			$pachyOd = $sumOd_arr[0][0]; 
			$pachyOs = $sumOs_arr[0][0]; 
			$IOPOdWithoutPachy = str_ireplace($pachyOd,'',$sumOd);
			$IOPOsWithoutPachy = str_ireplace($pachyOs,'',$sumOs);
		
			//START
			$initial_iop = '';
			if($multiple_pressure) {
				$multiple_pressure = unserialize($multiple_pressure);	
				$last_count = count($multiple_pressure);
				$last_sub_count = (count($multiple_pressure)-1);


				$elem_appOd 	= $multiple_pressure['multiplePressuer']['elem_appOd'];
				$elem_appOs 	= $multiple_pressure['multiplePressuer']['elem_appOs'];
				$elem_puffOd 	= $multiple_pressure['multiplePressuer']['elem_puffOd'];
				$elem_puffOs 	= $multiple_pressure['multiplePressuer']['elem_puffOs'];
				$elem_appTrgtOd = $multiple_pressure['multiplePressuer']['elem_appTrgtOd'];
				$elem_appTrgtOs = $multiple_pressure['multiplePressuer']['elem_appTrgtOs'];
				
				$elem_descTa 	= $multiple_pressure['multiplePressuer']['elem_descTa'];
				$elem_descTp 	= $multiple_pressure['multiplePressuer']['elem_descTp'];
				$elem_descTx 	= $multiple_pressure['multiplePressuer']['elem_descTx'];
				
				$elem_appTime 	= $multiple_pressure['multiplePressuer']['elem_appTime'];
				$elem_puffTime 	= $multiple_pressure['multiplePressuer']['elem_puffTime'];
				$elem_xTime 	= $multiple_pressure['multiplePressuer']['elem_xTime'];



				$post_elem_appOd 		= $multiple_pressure['multiplePressuer'.$last_count]['elem_appOd'.$last_sub_count];
				$post_elem_appOs 		= $multiple_pressure['multiplePressuer'.$last_count]['elem_appOs'.$last_sub_count];
				$post_elem_puffOd 		= $multiple_pressure['multiplePressuer'.$last_count]['elem_puffOd'.$last_sub_count];
				$post_elem_puffOs 		= $multiple_pressure['multiplePressuer'.$last_count]['elem_puffOs'.$last_sub_count];
				$post_elem_appTrgtOd 	= $multiple_pressure['multiplePressuer'.$last_count]['elem_appTrgtOd'.$last_sub_count];
				$post_elem_appTrgtOs 	= $multiple_pressure['multiplePressuer'.$last_count]['elem_appTrgtOs'.$last_sub_count];
				
				$post_elem_descTa 		= $multiple_pressure['multiplePressuer'.$last_count]['elem_descTa'.$last_sub_count];
				$post_elem_descTp 		= $multiple_pressure['multiplePressuer'.$last_count]['elem_descTp'.$last_sub_count];
				$post_elem_descTx 		= $multiple_pressure['multiplePressuer'.$last_count]['elem_descTx'.$last_sub_count];
				
				$post_elem_appTime 		= $multiple_pressure['multiplePressuer'.$last_count]['elem_appTime'.$last_sub_count];
				$post_elem_puffTime 	= $multiple_pressure['multiplePressuer'.$last_count]['elem_puffTime'.$last_sub_count];
				$post_elem_xTime 		= $multiple_pressure['multiplePressuer'.$last_count]['elem_xTime'.$last_sub_count];
				
				
				$elem_appMethod 		= $multiple_pressure['multiplePressuer']['elem_appMethod'];
				$elem_puffMethod 		= $multiple_pressure['multiplePressuer']['elem_puffMethod'];
				$elem_tactMethod		= $multiple_pressure['multiplePressuer']['elem_tactMethod'];
				$elem_ttMethod			= $multiple_pressure['multiplePressuer']['elem_ttMethod'];
				
				(!empty($elem_appMethod)) ? $elem_appMethod : "T<sub>A</sub>" ;
				(!empty($elem_puffMethod)) ? $elem_puffMethod : "T<sub>p</sub>" ;
				(!empty($elem_tactMethod)) ? $elem_tactMethod : "T<sub>x</sub>" ;
				
				//initial iop
				$initial_iop = '<table cellpadding="0" border="0" cellspacing="0">';
				if($elem_appOd || $elem_appOs) {		
					$initial_iop .= '<tr>
										<td class="text_10b">'.$elem_appMethod.'</td>
										<td class="text_10b"><b>OD: </b></td>
										<td class="text_10">'.$elem_appOd.'</td>
										<td class="text_10b"><b>OS: </b></td>
										<td class="text_10">'.$elem_appOs.'</td>				
										<td class="text_10b"><b>Description: </b></td>
										<td class="text_10">'.$elem_descTa.'</td>
									</tr>';
				}
				if($elem_puffOd || $elem_puffOs) {		
					$initial_iop .= '<tr>
										<td class="text_10b">'.$elem_puffMethod.'</td>
										<td class="text_10b"><b>OD: </b></td>
										<td class="text_10">'.$elem_puffOd.'</td>
										<td class="text_10b"><b>OS: </b></td>
										<td class="text_10">'.$elem_puffOs.'</td>				
										<td class="text_10b"><b>Description: </b></td>
										<td class="text_10">'.$elem_descTp.'</td>
									</tr>';
				}
				if($elem_appTrgtOd || $elem_appTrgtOs) {		
					$initial_iop .= '<tr>
										<td class="text_10b">'.$elem_tactMethod.'</td>
										<td class="text_10b"><b>OD: </b></td>
										<td class="text_10">'.$elem_appTrgtOd.'</td>
										<td class="text_10b"><b>OS: </b></td>
										<td class="text_10">'.$elem_appTrgtOs.'</td>				
										<td class="text_10b"><b>Description: </b></td>
										<td class="text_10">'.$elem_descTx.'</td>
									</tr>';
				}
				$initial_iop .= '</table>';
				
				$initial_iop_time = '<table cellpadding="0" border="0" cellspacing="0">';
				if($elem_appTime) {		
					$initial_iop_time .= '<tr>
											<td class="text_10b">'.$elem_appMethod.'</td>
											<td class="text_10">'.$elem_appTime.'</td>
										</tr>';
				}
				if($elem_puffTime) {		
					$initial_iop_time .= '<tr>
											<td class="text_10b">'.$elem_puffMethod.'</td>
											<td class="text_10">'.$elem_puffTime.'</td>
										</tr>';
				}
				if($elem_xTime) {		
					$initial_iop_time .= '<tr>
											<td class="text_10b">'.$elem_tactMethod.'</td>
											<td class="text_10">'.$elem_xTime.'</td>
										</tr>';
				}
				$initial_iop_time .= '</table>';
			
				//post iop
				$post_iop = '<table cellpadding="0" border="0" cellspacing="0">';
				if($post_elem_appOd || $post_elem_appOs) {		
					$post_iop .= '<tr>
										<td class="text_10b">'.$elem_appMethod.'</td>
										<td class="text_10b"><b>OD: </b></td>
										<td class="text_10">'.$post_elem_appOd.'</td>
										<td class="text_10b"><b>OS: </b></td>
										<td class="text_10">'.$post_elem_appOs.'</td>				
										<td class="text_10b"><b>Description: </b></td>
										<td class="text_10">'.$post_elem_descTa.'</td>
									</tr>';
				}
				if($post_elem_puffOd || $post_elem_puffOs) {		
					$post_iop .= '<tr>
										<td class="text_10b">'.$elem_puffMethod.'</td>
										<td class="text_10b"><b>OD: </b></td>
										<td class="text_10">'.$post_elem_puffOd.'</td>
										<td class="text_10b"><b>OS: </b></td>
										<td class="text_10">'.$post_elem_puffOs.'</td>				
										<td class="text_10b"><b>Description: </b></td>
										<td class="text_10">'.$post_elem_descTp.'</td>
									</tr>';
				}
				if($post_elem_appTrgtOd || $post_elem_appTrgtOs) {		
					$post_iop .= '<tr>
										<td class="text_10b">'.$elem_tactMethod.'</td>
										<td class="text_10b"><b>OD: </b></td>
										<td class="text_10">'.$post_elem_appTrgtOd.'</td>
										<td class="text_10b"><b>OS: </b></td>
										<td class="text_10">'.$post_elem_appTrgtOs.'</td>				
										<td class="text_10b"><b>Description: </b></td>
										<td class="text_10">'.$post_elem_descTx.'</td>
									</tr>';
				}
				$post_iop .= '</table>';
				
				$post_iop_time = '<table cellpadding="0" border="0" cellspacing="0">';
				if($post_elem_appTime) {		
					$post_iop_time .= '<tr>
											<td class="text_10b">'.$elem_appMethod.'</td>
											<td class="text_10">'.$post_elem_appTime.'</td>
										</tr>';
				}
				if($post_elem_puffTime) {		
					$post_iop_time .= '<tr>
											<td class="text_10b">'.$elem_puffMethod.'</td>
											<td class="text_10">'.$post_elem_puffTime.'</td>
										</tr>';
				}
				if($post_elem_xTime) {		
					$post_iop_time .= '<tr>
											<td class="text_10b">'.$elem_tactMethod.'</td>
											<td class="text_10">'.$post_elem_xTime.'</td>
										</tr>';
				}
				$post_iop_time .= '</table>';

			
			}
			//===============IOP OD/OS DATA WITHOUT TIME VALUE ON SIO REQUEST===============
			if($web_RootDirectoryName=="SIO" || $web_RootDirectoryName=="AEC"){
				$sumOd = $sumOs="";
				//==========================UNSERIALIZE MULTIPLE PRESSURE DATA==============
				$elem_appOd 	= $multiple_pressure['multiplePressuer']['elem_appOd'];
				$elem_appOs 	= $multiple_pressure['multiplePressuer']['elem_appOs'];
				$elem_puffOd 	= $multiple_pressure['multiplePressuer']['elem_puffOd'];
				$elem_puffOs 	= $multiple_pressure['multiplePressuer']['elem_puffOs'];
				$elem_appTrgtOd = $multiple_pressure['multiplePressuer']['elem_appTrgtOd'];
				$elem_appTrgtOs = $multiple_pressure['multiplePressuer']['elem_appTrgtOs'];
				$elem_descTa 	= $multiple_pressure['multiplePressuer']['elem_descTa'];
				$elem_descTp 	= $multiple_pressure['multiplePressuer']['elem_descTp'];
				$elem_descTx 	= $multiple_pressure['multiplePressuer']['elem_descTx'];
				$tactTrgtOd     = $multiple_pressure['multiplePressuer']['elem_tactTrgtOd'];
				$tactTrgtOs     = $multiple_pressure['multiplePressuer']['elem_tactTrgtOs'];
				$elem_descTt    = $multiple_pressure['multiplePressuer']['elem_descTt'];
								
				//==============================OD DATA==================================
				if($elem_appOd){  	$sumOd .= "T<sub>A</sub>: ".$elem_appOd;            }
				if($elem_descTa){ 	$sumOd .= "&nbsp;Desc T<sub>A</sub>: ".$elem_descTa; }	
				if($elem_puffOd){ 	$sumOd .= "&nbsp;T<sub>p</sub>: ".$elem_puffOd; }
				if($elem_descTp){ 	$sumOd .= "&nbsp;Desc T<sub>p</sub>: ".$elem_descTp; }
				if($elem_appTrgtOd){$sumOd .= "&nbsp;T<sub>x</sub>: ".$elem_appTrgtOd;   } 
				if($elem_descTx){	$sumOd .= "&nbsp;Desc T<sub>x</sub>: ".$elem_descTx; }
				if($tactTrgtOd){	$sumOd .= "&nbsp;T<sub>t</sub>: ".$tactTrgtOd;		}
				if($elem_descTt){	$sumOd .= "&nbsp;Desc T<sub>t</sub>: ".$elem_descTt; }
				//==============================OS DATA==================================
				if($elem_appOs){	$sumOs .= "T<sub>A</sub>: ".$elem_appOs;			}
				if($elem_descTa){ 	$sumOs .= "&nbsp;Desc T<sub>A</sub>: ".$elem_descTa; }	
				if($elem_puffOs){	$sumOs .= "&nbsp;T<sub>p</sub>: ".$elem_puffOs;		}
				if($elem_descTp){ 	$sumOs .= "&nbsp;Desc T<sub>p</sub>: ".$elem_descTp; }
				if($elem_appTrgtOs){$sumOs .= "&nbsp;T<sub>x</sub>: ".$elem_appTrgtOs;	}
				if($elem_descTx){	$sumOs .= "&nbsp;Desc T<sub>x</sub>: ".$elem_descTx; }
				if($tactTrgtOs){	$sumOs .= "&nbsp;T<sub>t</sub>: ".$tactTrgtOs;		}
				if($elem_descTt){	$sumOs .= "&nbsp;Desc T<sub>t</sub>: ".$elem_descTt; }
			}
			//==============================END TASK======================================
			
			//---GFS Target IOP OD/OS Variables Replacement Work Starts HERE---
			$gfsiopTrgtOd = $gfsiopTrgtOs = "";
			$gfsiopTrgtOd  = $gfs_res["iopTrgtOd"];
			$gfsiopTrgtOs  = $gfs_res["iopTrgtOs"];
			
			//Replace Array
			$arrRep = array(trim($sum),trim($sumOd),trim($sumOs),$initial_iop,$initial_iop_time,$post_iop,$post_iop_time,trim($IOPOdWithoutPachy),trim($IOPOsWithoutPachy),$gfsiopTrgtOd,$gfsiopTrgtOs);
		}
		//Replace
		$str = $this->refineData($str, $this->kIop, $arrRep, $this->tIop,"");
		return $str;
	}
	
	//Get Gonio
	private function getGonio($str, $ptId, $formId){
		$sql = "SELECT gonio_od_summary, gonio_os_summary FROM chart_gonio WHERE form_id = '".$formId."' AND patient_id = '".$ptId."' AND purged='0'  ";
		$res = sqlQuery($sql);
		if($res !== false){
			$sumOd = $res["gonio_od_summary"];
			$sumOs = $res["gonio_os_summary"];
			
			$sumOd = (!empty($sumOd)) ? $this->tOd." ".$this->clearSumm($sumOd) : "";
			$sumOs = (!empty($sumOs)) ? $this->tOs." ".$this->clearSumm($sumOs) : "";
			$sumOu = "";
			$sumOu .= (!empty($sumOd)) ? $sumOd : "";//$this->tOd." Not Performed";
			$sumOu .= (!empty($sumOs)) ? $sumOs : "";//$this->tOs." Not Performed";			
			if(empty($sumOd) && empty($sumOs)){
				$sumOu = "";
			}			
			
			//Replace Array
			$arrRep = array($sumOu, $sumOd, $sumOs);
			
		}
		//Replace
		$str = $this->refineData($str, $this->kGonio, $arrRep, $this->tGonio,"");
		return $str;
	}
	
	//Get SLE
	private function getSle($str, $ptId, $formId){
		/**
		 * chart_slit_lamp_exam TABLE SPLITTING TABLES WORK - NOW EVERY SECTION OF SLE EXAM HAS OWN INDEPENDENT TABLES
		 * 
		 */
		$sql = "SELECT
			  CON.wnlConjOd, 
			  CON.wnlConjOs,	
			  CON.conjunctiva_od_summary,
			  CON.conjunctiva_os_summary,
			  CON.wnl_value_Conjunctiva,
			  
			  COR.cornea_od_summary,
			  COR.cornea_os_summary,
			  COR.wnl_value_Cornea,
			  COR.wnlCornOd,
			  COR.wnlCornOs, 
			  
			  CAC.wnl_value_Ant,
			  CAC.anf_chamber_od_summary,
			  CAC.anf_chamber_os_summary,
			  CAC.wnlAntOd,
			  CAC.wnlAntOs,
			  
			  CLE.lens_od_summary, 
			  CLE.lens_os_summary, 
			  CLE.wnl_value_Lens, 
			  CLE.wnlLensOd,
			  CLE.wnlLensOs,
			  
			  CIR.wnl_value_Iris,
			  CIR.iris_pupil_od_summary,
			  CIR.iris_pupil_os_summary,
			  CIR.wnlIrisOd,
			  CIR.wnlIrisOs		  
			FROM
			  chart_master_table CMT
			  INNER JOIN chart_conjunctiva CON ON CMT.id = CON.form_id
			  INNER JOIN chart_cornea COR ON CMT.id = COR.form_id
			  INNER JOIN chart_ant_chamber CAC ON CMT.id = CAC.form_id
			  INNER JOIN chart_iris CIR ON CMT.id = CIR.form_id
			  INNER JOIN chart_lens CLE ON CMT.id = CLE.form_id
			  INNER JOIN chart_drawings CLD ON CMT.id = CLD.form_id
			WHERE
			  CMT.patient_id = '".$ptId."'
			  AND CMT.id = '".$formId."'
			  AND CMT.purge_status = '0'
			  AND CMT.delete_status = '0'
			  AND CON.purged = '0'
			  AND COR.purged = '0'
			  AND CAC.purged = '0'
			  AND CIR.purged = '0'
			  AND CLE.purged = '0'
			  AND CLD.purged = '0'";
		$res = sqlQuery($sql);
		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv For Merged NSE Server
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
			if($res == false){
				$sql = str_replace("chart_slit_lamp_exam","chart_slit_lamp_exam_archive",$sql);
				$res=sqlQuery($sql);	
			}
		}
		//-------- End ----------------------------------------------------------------------------------	
		if($res !== false){
			$oCn = new ChartNote($ptId, $formId);
			$strCONJOD = $strCONJOS = $strCORNEAOD = $strCORNEAOS = $strANTCHAMBEROD = $strANTCHAMBEROS = $strLENSOD = $strLENSOS = $strIRISOD = $strIRISOS = "";
			$sumConOd = wordwrap($this->clearSumm($res["conjunctiva_od_summary"]), 20, "<br />\n");
			$strCONJOD = $sumConOd;
			
			$sumConOs = wordwrap($this->clearSumm($res["conjunctiva_os_summary"]), 20, "<br />\n");			
			$strCONJOS = $sumConOs;
			
			$sumCorOd = wordwrap($this->clearSumm($res["cornea_od_summary"]), 20, "<br />\n");
			$strCORNEAOD = $sumCorOd;
			$sumCorOs = wordwrap($this->clearSumm($res["cornea_os_summary"]), 20, "<br />\n");
			$strCORNEAOS = $sumCorOs;
			
			$sumAnfOd = wordwrap($this->clearSumm($res["anf_chamber_od_summary"]), 20, "<br />\n");
			$strANTCHAMBEROD = $sumAnfOd;
			$sumAnfOs = wordwrap($this->clearSumm($res["anf_chamber_os_summary"]), 20, "<br />\n");					
			$strANTCHAMBEROS = $sumAnfOs;
			
			$sumIrisOd = wordwrap($this->clearSumm($res["iris_pupil_od_summary"]), 20, "<br />\n");
			$sumIrisOs = wordwrap($this->clearSumm($res["iris_pupil_os_summary"]), 20, "<br />\n");
			
			$sumLensOd = wordwrap($this->clearSumm($res["lens_od_summary"]), 20, "<br />\n");
			$strLENSOD = $sumLensOd;
			$sumLensOs = wordwrap($this->clearSumm($res["lens_os_summary"]), 20, "<br />\n");
			$strLENSOS = $sumLensOs;
			
			if($res["wnlConjOd"] == "1"){
				$wnlConjOd =  !empty($res["wnl_value_Conjunctiva"]) ? $res["wnl_value_Conjunctiva"] : $oCn->getExamWnlStr("Conjunctiva");
			}
			if($res["wnlConjOs"] == "1"){
				$wnlConjOs =  !empty($res["wnl_value_Conjunctiva"]) ? $res["wnl_value_Conjunctiva"] : $oCn->getExamWnlStr("Conjunctiva");
			}
			
			if($res["wnlCornOd"] == "1"){
				$wnlCornOd =  !empty($res["wnl_value_Cornea"]) ? $res["wnl_value_Cornea"] : $oCn->getExamWnlStr("Cornea");
			}
			if($res["wnlCornOs"] == "1"){
				$wnlCornOs =  !empty($res["wnl_value_Cornea"]) ? $res["wnl_value_Cornea"] : $oCn->getExamWnlStr("Cornea");
			}
			
			if($res["wnlAntOd"] == "1"){
				$wnlAntOd =  !empty($res["wnl_value_Ant"]) ? $res["wnl_value_Ant"] : $oCn->getExamWnlStr("Ant. Chamber");
			}
			if($res["wnlAntOs"] == "1"){
				$wnlAntOs =  !empty($res["wnl_value_Ant"]) ? $res["wnl_value_Ant"] : $oCn->getExamWnlStr("Ant. Chamber"); 
			}
			
			if($res["wnlIrisOd"] == "1"){
				$wnlIrisOd =  !empty($res["wnl_value_Iris"]) ? $res["wnl_value_Iris"] : $oCn->getExamWnlStr("Iris & Pupil");
			}
			if($res["wnlIrisOs"] == "1"){
				$wnlIrisOs =  !empty($res["wnl_value_Iris"]) ? $res["wnl_value_Iris"] : $oCn->getExamWnlStr("Iris & Pupil");
			}
			if($res["wnlLensOd"] == "1"){
				$wnlLensOd =  !empty($res["wnl_value_Lens"]) ? $res["wnl_value_Lens"] : $oCn->getExamWnlStr("Lens");
			}
			if($res["wnlLensOs"] == "1"){
				$wnlLensOs =  !empty($res["wnl_value_Lens"]) ? $res["wnl_value_Lens"] : $oCn->getExamWnlStr("Lens");
			}
			
			$arrOd = array();
			$arrOs = array();
			if(!empty($sumConOd)){
				$arrOd[] = "<b>Conjunctiva</b>&nbsp;".$sumConOd.'</br>';
			}
			elseif(!empty($wnlConjOd)){//$wnlCornOd
				$arrOd[] = "<b>Conjunctiva</b>&nbsp;".$wnlConjOd.'</br>';
				$strCONJOD = "<b>Conjunctiva</b>&nbsp;".$wnlConjOd.'</br>';
			}
					
			if(!empty($sumConOs)){
				$arrOs[] = "<b>Conjunctiva</b>&nbsp;".$sumConOs.'</br>';
			}
			elseif(!empty($wnlConjOs)){
				$arrOs[] = "<b>Conjunctiva</b>&nbsp;".$wnlConjOs.'</br>';
				$strCONJOS = "<b>Conjunctiva</b>&nbsp;".$wnlConjOs.'</br>';
			}
			
			if(!empty($sumCorOd)){
				$arrOd[] = "<b>Cornea</b>&nbsp;".$sumCorOd.'</br>';
			}
			elseif(!empty($wnlCornOd)){
				$arrOd[] = "<b>Cornea</b>&nbsp;".$wnlCornOd.'</br>';
				$strCORNEAOD = "<b>Cornea</b>&nbsp;".$wnlCornOd.'</br>';
			}
			
			if(!empty($sumCorOs)){
				$arrOs[] = "<b>Cornea</b>&nbsp;".$sumCorOs.'</br>';
			}
			elseif(!empty($wnlCornOs)){
				$arrOs[] = "<b>Cornea</b>&nbsp;".$wnlCornOs.'</br>';
				$strCORNEAOS = "<b>Cornea</b>&nbsp;".$wnlCornOs.'</br>';
			}
			
			if(!empty($sumAnfOd)){
				$arrOd[] = "<b>Chamber</b>&nbsp;".$sumAnfOd.'</br>';
			}
			elseif(!empty($wnlAntOd)){
				$arrOd[] = "<b>Chamber</b>&nbsp;".$wnlAntOd.'</br>';
				$strANTCHAMBEROD = "<b>Chamber</b>&nbsp;".$wnlAntOd.'</br>';
			}
			
			if(!empty($sumAnfOs)){
				$arrOs[] = "<b>Chamber</b>&nbsp;".$sumAnfOs.'</br>';
			}
			elseif(!empty($wnlAntOs)){
				$arrOs[] = "<b>Chamber</b>&nbsp;".$wnlAntOs.'</br>';
				$strANTCHAMBEROS = "<b>Chamber</b>&nbsp;".$wnlAntOs.'</br>';
			}
			
			if(!empty($sumIrisOd)){
				$arrOd[] = "<b>Iris</b>&nbsp;".$sumIrisOd.'</br>';
				$strIRISOD = "<b>Iris</b>&nbsp;".$sumIrisOd.'</br>';
			}
			elseif(!empty($wnlIrisOd)){
				$arrOd[] = "<b>Iris</b>&nbsp;".$wnlIrisOd.'</br>';
				$strIRISOD = "<b>Iris</b>&nbsp;".$wnlIrisOd.'</br>';
			}
			if(!empty($sumIrisOs)){
				$arrOs[] = "<b>Iris</b>&nbsp;".$sumIrisOs.'</br>';
				$strIRISOS = "<b>Iris</b>&nbsp;".$sumIrisOs.'</br>';
			}
			elseif(!empty($wnlIrisOs)){
				$arrOs[] = "<b>Iris</b>&nbsp;".$wnlIrisOs.'</br>';
				$strIRISOS = "<b>Iris</b>&nbsp;".$wnlIrisOs.'</br>';
			}
			if(!empty($sumLensOd)){
				$arrOd[] = "<b>Lens</b>&nbsp;".$sumLensOd.'</br>';
			}
			elseif(!empty($wnlLensOd)){
				$arrOd[] = "<b>Lens</b>&nbsp;".$wnlLensOd.'</br>';
				$strLENSOD = "<b>Lens</b>&nbsp;".$wnlLensOd.'</br>';
			}
			
			if(!empty($sumLensOs)){
				$arrOs[] = "<b>Lens</b>&nbsp;".$sumLensOs.'</br>';
			}
			elseif(!empty($wnlLensOs)){
				$arrOs[] = "<b>Lens</b>&nbsp;".$wnlLensOs.'</br>';
				$strLENSOS = "<b>Lens</b>&nbsp;".$wnlLensOs.'</br>';
			}
			$sumOd = "";
			if(count($arrOd) > 0){
				foreach($arrOd as $key => $val){
					$sumOd .= (!empty($val)) ? $val : "";
				}				
				//Last comma removal
				$sumOd = preg_replace("/,\s*$/", "", $sumOd);				
			}
			
			$sumOs = "";
			if(count($arrOs) > 0){
				foreach($arrOs as $key => $val){
					$sumOs .= (!empty($val)) ? $val : "";
				}				
				//Last comma removal
				$sumOs = preg_replace("/,\s*$/", "", $sumOs);
			}
			
			$sumOu 	= trim($sumOd." ".$sumOs);
			$SLE 	= $sumOu;
			
			//Replace Array
			$arrRep = array($sumOu, $sumOd, $sumOs, $SLE, $strCONJOD, $strCONJOS, $strCORNEAOD, $strCORNEAOS, $strANTCHAMBEROD, $strANTCHAMBEROS, $strLENSOD, $strLENSOS, $strIRISOD, $strIRISOS);
		}
		
		//Replace
		$str = $this->refineData($str, $this->kSle, $arrRep, $this->tSle,"");		
		return $str;	
	}
	
	//Get SLE Data in Table Format By Kaushal
	private function getSli($str, $ptId, $formId){
		/**
		 * chart_slit_lamp_exam TABLE SPLITTING TABLES WORK - NOW EVERY SECTION OF SLE EXAM HAS OWN INDEPENDENT TABLES
		 * 
		 */
		$sql = "SELECT
				  CON.wnlConjOd, 
				  CON.wnlConjOs,	
				  CON.conjunctiva_od_summary,
				  CON.conjunctiva_os_summary,
				  CON.wnl_value_Conjunctiva,
				  
				  COR.cornea_od_summary,
				  COR.cornea_os_summary,
				  COR.wnl_value_Cornea,
				  COR.wnlCornOd,
				  COR.wnlCornOs, 
				  
				  CAC.wnl_value_Ant,
				  CAC.anf_chamber_od_summary,
				  CAC.anf_chamber_os_summary,
				  CAC.wnlAntOd,
				  CAC.wnlAntOs,
				  
				  CLE.lens_od_summary, 
				  CLE.lens_os_summary, 
				  CLE.wnl_value_Lens, 
				  CLE.wnlLensOd,
				  CLE.wnlLensOs,
				  
				  CIR.wnl_value_Iris,
				  CIR.iris_pupil_od_summary,
				  CIR.iris_pupil_os_summary,
				  CIR.wnlIrisOd,
				  CIR.wnlIrisOs		  
				FROM
				  chart_master_table CMT
				  INNER JOIN chart_conjunctiva CON ON CMT.id = CON.form_id
				  INNER JOIN chart_cornea COR ON CMT.id = COR.form_id
				  INNER JOIN chart_ant_chamber CAC ON CMT.id = CAC.form_id
				  INNER JOIN chart_iris CIR ON CMT.id = CIR.form_id
				  INNER JOIN chart_lens CLE ON CMT.id = CLE.form_id
				  INNER JOIN chart_drawings CLD ON CMT.id = CLD.form_id
				WHERE
				  CMT.patient_id = '".$ptId."'
				  AND CMT.id = '".$formId."'
				  AND CMT.purge_status = '0'
				  AND CMT.delete_status = '0'
				  AND CON.purged = '0'
				  AND COR.purged = '0'
				  AND CAC.purged = '0'
				  AND CIR.purged = '0'
				  AND CLE.purged = '0'
				  AND CLD.purged = '0'";
		$res = sqlQuery($sql);
		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv For Merged NSE Server
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
			if($res == false){
				$sql = str_replace("chart_slit_lamp_exam","chart_slit_lamp_exam_archive",$sql);
				$res=sqlQuery($sql);	
			}
		}
		//-------- End ----------------------------------------------------------------------------------	
		if($res !== false){
			$oCn = new ChartNote($ptId, $formId);
			$strCONJOD = $strCONJOS = $strCORNEAOD = $strCORNEAOS = $strANTCHAMBEROD = $strANTCHAMBEROS = $strLENSOD = $strLENSOS = "";
			$sumConOd = wordwrap($this->clearSumm($res["conjunctiva_od_summary"]), 20, "<br />\n");
			$strCONJOD = $sumConOd;
			
			$sumConOs = wordwrap($this->clearSumm($res["conjunctiva_os_summary"]), 20, "<br />\n");			
			$strCONJOS = $sumConOs;
			
			$sumCorOd = wordwrap($this->clearSumm($res["cornea_od_summary"]), 20, "<br />\n");
			$strCORNEAOD = $sumCorOd;
			$sumCorOs = wordwrap($this->clearSumm($res["cornea_os_summary"]), 20, "<br />\n");
			$strCORNEAOS = $sumCorOs;
			
			$sumAnfOd = wordwrap($this->clearSumm($res["anf_chamber_od_summary"]), 20, "<br />\n");
			$strANTCHAMBEROD = $sumAnfOd;
			$sumAnfOs = wordwrap($this->clearSumm($res["anf_chamber_os_summary"]), 20, "<br />\n");					
			$strANTCHAMBEROS = $sumAnfOs;
			
			$sumIrisOd = wordwrap($this->clearSumm($res["iris_pupil_od_summary"]), 20, "<br />\n");
			$sumIrisOs = wordwrap($this->clearSumm($res["iris_pupil_os_summary"]), 20, "<br />\n");						
			
			$sumLensOd = wordwrap($this->clearSumm($res["lens_od_summary"]), 20, "<br />\n");
			$strLENSOD = $sumLensOd;
			$sumLensOs = wordwrap($this->clearSumm($res["lens_os_summary"]), 20, "<br />\n");
			$strLENSOS = $sumLensOs;
			
			if($res["wnlConjOd"] == "1"){
				$wnlConjOd =  !empty($res["wnl_value_Conjunctiva"]) ? $res["wnl_value_Conjunctiva"] : $oCn->getExamWnlStr("Conjunctiva");
			}
			if($res["wnlConjOs"] == "1"){
				$wnlConjOs =  !empty($res["wnl_value_Conjunctiva"]) ? $res["wnl_value_Conjunctiva"] : $oCn->getExamWnlStr("Conjunctiva");
			}
			
			if($res["wnlCornOd"] == "1"){
				$wnlCornOd =  !empty($res["wnl_value_Cornea"]) ? $res["wnl_value_Cornea"] : $oCn->getExamWnlStr("Cornea");
			}
			if($res["wnlCornOs"] == "1"){
				$wnlCornOs =  !empty($res["wnl_value_Cornea"]) ? $res["wnl_value_Cornea"] : $oCn->getExamWnlStr("Cornea");
			}
			
			if($res["wnlAntOd"] == "1"){
				$wnlAntOd =  !empty($res["wnl_value_Ant"]) ? $res["wnl_value_Ant"] : $oCn->getExamWnlStr("Ant. Chamber");
			}
			if($res["wnlAntOs"] == "1"){
				$wnlAntOs =  !empty($res["wnl_value_Ant"]) ? $res["wnl_value_Ant"] : $oCn->getExamWnlStr("Ant. Chamber"); 
			}
			
			if($res["wnlIrisOd"] == "1"){
				$wnlIrisOd =  !empty($res["wnl_value_Iris"]) ? $res["wnl_value_Iris"] : $oCn->getExamWnlStr("Iris & Pupil");
			}
			if($res["wnlIrisOs"] == "1"){
				$wnlIrisOs =  !empty($res["wnl_value_Iris"]) ? $res["wnl_value_Iris"] : $oCn->getExamWnlStr("Iris & Pupil");
			}
			if($res["wnlLensOd"] == "1"){
				$wnlLensOd =  !empty($res["wnl_value_Lens"]) ? $res["wnl_value_Lens"] : $oCn->getExamWnlStr("Lens");
			}
			if($res["wnlLensOs"] == "1"){
				$wnlLensOs =  !empty($res["wnl_value_Lens"]) ? $res["wnl_value_Lens"] : $oCn->getExamWnlStr("Lens");
			}
			
			$SLE_val = 0;
			$table = "";
			
			if (!empty($sumConOd) ||  !empty($wnlConjOd) || !empty($sumConOs) || !empty($wnlConjOs)){
				
				$table.= '<tr><td style="width:45%;vertical-align:top;"><b>Conjunctiva</b>&nbsp;';			 
					if(!empty($sumConOd)){
						$SLE_val = 1;
						$table.=$sumConOd;
					}
					elseif(!empty($wnlConjOd)){
						$SLE_val = 1;
						$table.= $wnlConjOd;
					}
					$table.='</td><td style="width:45%;vertical-align:top;"><b>Conjunctiva</b>&nbsp;';		
					if(!empty($sumConOs)){
						$SLE_val = 1;
						$table.=$sumConOs;
					}
					elseif(!empty($wnlConjOs)){
						$SLE_val = 1;
						$table.=$wnlConjOs;
					}
					$table.= '</td></tr>';
			}
			if(!empty($sumCorOd) || !empty($wnlCornOd) || !empty($sumCorOs) || !empty($wnlCornOs)){
				$table.= '<tr><td style="width:45%;vertical-align:top;"><b>Cornea</b>&nbsp;';		
				if(!empty($sumCorOd)){
					$SLE_val = 1;
					$table.= $sumCorOd;		
				}
				elseif(!empty($wnlCornOd)){
					$SLE_val = 1;
					$table.= $wnlCornOd;		
				}
				$table.='</td><td style="width:45%;vertical-align:top;"><b>Cornea</b>&nbsp;';
				if(!empty($sumCorOs)){
					$SLE_val = 1;
					$table.= $sumCorOs;		
				}
				elseif(!empty($wnlCornOs)){
					$SLE_val = 1;
					$table.= $wnlCornOs;		
				}
				$table.= '</td></tr>';
			}
			if(!empty($sumAnfOd) || !empty($wnlAntOd) || !empty($sumAnfOs) || !empty($wnlAntOs)){
				$table.= '<tr><td style="width:45%;vertical-align:top;"><b>Chamber</b>&nbsp;';
				if(!empty($sumAnfOd)){
					$SLE_val = 1;
					$table.= $sumAnfOd;		
				}
				elseif(!empty($wnlAntOd)){
					$SLE_val = 1;
					$table.= $wnlAntOd;		
				}
				$table.='</td><td style="width:45%;vertical-align:top;"><b>Chamber</b>&nbsp;';
				
				if(!empty($sumAnfOs)){
					$SLE_val = 1;
					$table.= $sumAnfOs;		
				}
				elseif(!empty($wnlAntOs)){
					$SLE_val = 1;
					$table.= $wnlAntOs;		
				}
				$table.= '</td></tr>';
			}
			if(!empty($sumIrisOd) || !empty($wnlIrisOd) || !empty($sumIrisOs) || !empty($wnlIrisOs)){
				$table.= '<tr><td style="width:45%;vertical-align:top;"><b>Iris</b>&nbsp;';
				if(!empty($sumIrisOd)){
					$SLE_val = 1;
					$table.= $sumIrisOd;		
				}
				elseif(!empty($wnlIrisOd)){
					$SLE_val = 1;
					$table.= $wnlIrisOd;		
				}
				$table.='</td><td style="width:45%;vertical-align:top;"><b>Iris</b>&nbsp;';
				if(!empty($sumIrisOs)){
					$SLE_val = 1;
					$table.= $sumIrisOs;		
				}
				elseif(!empty($wnlIrisOs)){
					$SLE_val = 1;
					$table.= $wnlIrisOs;		
				}
				$table.= '</td></tr>';
			}
			if(!empty($sumLensOd) || !empty($wnlLensOd) || !empty($sumLensOs) || !empty($wnlLensOs)){
				$table.= '<tr><td style="width:45%;vertical-align:top;"><b>Lens</b>&nbsp;';
				if(!empty($sumLensOd)){
					$SLE_val = 1;
					$table.= $sumLensOd;		
				}
				elseif(!empty($wnlLensOd)){
					$SLE_val = 1;
					$table.= $wnlLensOd;		
				}
				$table.='</td><td style="width:45%;vertical-align:top;"><b>Lens</b>&nbsp;';				
				if(!empty($sumLensOs)){
					$SLE_val = 1;
					$table.= $sumLensOs;		
				}
				elseif(!empty($wnlLensOs)){
					$SLE_val = 1;
					$table.= $wnlLensOs;		
				}
				$table.='</td></tr>';
			}
			$table_h = '<table cellpadding="0" border="0" cellspacing="0" style="width:100%">';
			$table_f = '</table>';
			if($SLE_val == 1){
				$table = $table_h.$table.$table_f; 
			}
			//Replace Array
			$arrRep = array($table);
		}
		
		//Replace
		$str = $this->refineData($str, $this->kSli, $arrRep, $this->tSli,"");		
		return $str;	
	}
	
	//Get Fundus Data
	private function getFundus($str, $ptId, $formId){
		/**
		 * chart_rv TABLE SPLITTING TABLES WORK - NOW EVERY SECTION OF FUNDUS EXAM HAS OWN INDEPENDENT TABLES
		 * 
		 */
		$sql = "SELECT 
				  c2.retinal_od_summary,
				  c2.retinal_os_summary,
				  c2.wnlRetinalOd,
				  c2.wnlRetinalOs,
				  c2.periNotExamined,
				  c2.peri_ne_eye, 
				  c2.wnl_value_RetinalExam,
				  
				  c3.vitreous_od_summary,
				  c3.vitreous_os_summary,
				  c3.wnlVitreousOd,
				  c3.wnlVitreousOs,
				  c3.wnl_value_Vitreous,
				 
				  c4.blood_vessels_od_summary,
				  c4.blood_vessels_os_summary,
				  c4.wnlBVOd,
				  c4.wnlBVOs,
				  c4.wnl_value_BV,
				  
				  c5.macula_od_summary,
				  c5.macula_os_summary,
				  c5.wnlMaculaOd,
				  c5.wnlMaculaOs,
				  c5.wnl_value_Macula,
				  
				  c6.periphery_od_summary,
				  c6.periphery_os_summary,
				  c6.wnlPeriOd,
				  c6.wnlPeriOs,
				  c6.wnl_value_Peri,
				  
				  chart_optic.optic_nerve_od_summary,
				  chart_optic.optic_nerve_os_summary,
				  chart_optic.wnlOpticOd,
				  chart_optic.wnlOpticOs,
				  chart_optic.od_text,
				  chart_optic.os_text,
				  chart_optic.wnl_value_Optic,
				  chart_optic.cd_val_od,
				  chart_optic.cd_val_os,
				  disc.cdOd,
				  disc.cdOs,
				   CONCAT(
					IFNULL(c2.statusElem,''),
					IFNULL(c3.statusElem,''),
					IFNULL(c4.statusElem,''),
					IFNULL(c5.statusElem,''),
					IFNULL(c6.statusElem,''),
					IFNULL(chart_optic.statusElem,'')
				  ) AS statusElem
				FROM
				  chart_master_table c1
				  LEFT JOIN chart_retinal_exam c2 ON c1.id = c2.form_id  AND c2.purged = '0'
				  LEFT JOIN chart_vitreous c3 ON c1.id = c3.form_id  AND c3.purged = '0'
				  LEFT JOIN chart_blood_vessels c4 ON c1.id = c4.form_id  AND c4.purged = '0'
				  LEFT JOIN chart_macula c5 ON c1.id = c5.form_id  AND c5.purged = '0'
				  LEFT JOIN chart_periphery c6 ON c1.id = c6.form_id AND c6.purged = '0'
				  LEFT JOIN chart_optic ON chart_optic.form_id = c1.id AND chart_optic.purged = '0'
 			      LEFT JOIN disc ON disc.formId = c1.id AND disc.purged='0' AND disc.del_status = '0' AND disc.purged = '0'
				WHERE
				  c1.id = '".$formId."'
				  AND c1.patient_id = '".$ptId."'
				  AND c1.purge_status = '0'
				  AND c1.delete_status = '0'";
	
		$res = sqlQuery($sql);
		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv For Merged NSE Server
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
			if($res == false){
				$sql = str_replace("chart_rv","chart_rv_archive",$sql);
				$res=sqlQuery($sql);	
			}
		}
		//-------- End ----------------------------------------------------------------------------------
		if($res !== false){	
		
			$strOPTICNERVEOD = $strOPTICNERVEOS = $strMACULAOD = $strMACULAOS = $strVITREOUSOD = $strVITREOUSOS = $strPERIPHERYOD = $strPERIPHERYOS = $strBVOD = $strBVOS = $strRETINALOD= $strRETINALOS="";
			$oCn = new ChartNote($ptId, $formId);
			if($res["wnlVitreousOd"] == 0){
				$sumVitOd = wordwrap($this->clearSumm($res["vitreous_od_summary"]), 20, "<br />\n");
				$strVITREOUSOD = $sumVitOd;
			}
			else{
				$sumVitOd = !empty($res["wnl_value_Vitreous"]) ? $res["wnl_value_Vitreous"] : $oCn->getExamWnlStr("Vitreous");
				$strVITREOUSOD = $sumVitOd;
			}
			
			if($res["wnlVitreousOs"] == 0){
				$sumVitOs = wordwrap($this->clearSumm($res["vitreous_os_summary"]), 20, "<br />\n");
				$strVITREOUSOS = $sumVitOs;
			}
			else{
				$sumVitOs = !empty($res["wnl_value_Vitreous"]) ? $res["wnl_value_Vitreous"] : $oCn->getExamWnlStr("Vitreous");
				$strVITREOUSOS = $sumVitOs;
			}
			
			if($res["wnlPeriOd"] == 0){
				$sumPeriOd = wordwrap($this->clearSumm($res["periphery_od_summary"]), 20, "<br />\n");
				$strPERIPHERYOD = $sumPeriOd;
			}
			else{
				$sumPeriOd = !empty($res["wnl_value_Peri"]) ? $res["wnl_value_Peri"] :  $oCn->getExamWnlStr("Periphery");
				$strPERIPHERYOD = $sumPeriOd;
			}
			
			if($res["wnlPeriOs"] == 0){
				$sumPeriOs = wordwrap($this->clearSumm($res["periphery_os_summary"]), 20, "<br />\n");			
				$strPERIPHERYOS = $sumPeriOs;
			}
			else{
				$sumPeriOs = !empty($res["wnl_value_Peri"]) ? $res["wnl_value_Peri"] :  $oCn->getExamWnlStr("Periphery");			
				$strPERIPHERYOS = $sumPeriOs;
			}
			
			if($res["wnlBVOd"] == 0){
				$sumBvOd = wordwrap($this->clearSumm($res["blood_vessels_od_summary"]), 20, "<br />\n");
				$strBVOD = $sumBvOd;
			}
			else{
				$sumBvOd = !empty($res["wnl_value_BV"]) ? $res["wnl_value_BV"] :  $oCn->getExamWnlStr("Blood Vessels");
				$strBVOD = $sumBvOd;
			}
			
			if($res["wnlBVOs"] == 0){
				$sumBvOs = wordwrap($this->clearSumm($res["blood_vessels_os_summary"]), 20, "<br />\n");
				$strBVOS = $sumBvOs;
			}
			else{
				$sumBvOs = !empty($res["wnl_value_BV"]) ? $res["wnl_value_BV"] :  $oCn->getExamWnlStr("Blood Vessels");
				$strBVOS = $sumBvOs;
			}
			
			if($res["wnlMaculaOd"] == 0){
				$sumMacOd = wordwrap($this->clearSumm($res["macula_od_summary"]), 20, "<br />\n");			
				$strMACULAOD = $sumMacOd;
			}	
			else{
				$sumMacOd = !empty($res["wnl_value_Macula"]) ? $res["wnl_value_Macula"] :  $oCn->getExamWnlStr("Macula");
				$strMACULAOD = $sumMacOd;
			}		
			
			if($res["wnlMaculaOs"] == 0){
				$sumMacOs = wordwrap($this->clearSumm($res["macula_os_summary"]), 20, "<br />\n");
				$strMACULAOS = $sumMacOs;
			}
			else{
				$sumMacOs = !empty($res["wnl_value_Macula"]) ? $res["wnl_value_Macula"] :  $oCn->getExamWnlStr("Macula");
				$strMACULAOS = $sumMacOs;
			}
			
			///Retinal
			if(isset($GLOBALS["ADD_PERI_WNL_2_RETINAL"])&&!empty($GLOBALS["ADD_PERI_WNL_2_RETINAL"])){
				if($res["periNotExamined"]!=1){$wnlPeri="periphery normal";}else{$wnlPeri="periphery not examined";}
			}
			if($res["wnlRetinalOd"] == 0){
				if($res["periNotExamined"]==1 && ($res["peri_ne_eye"]=="OU" || $res["peri_ne_eye"]=="OD")){ 
					if(!empty($res["retinal_od_summary"])){ $res["retinal_od_summary"].=";";  }
					$res["retinal_od_summary"] = $res["retinal_od_summary"]." periphery not examined";
				}
				$sumRetiOd = wordwrap($this->clearSumm($res["retinal_od_summary"]), 20, "<br />\n");			
				$strRETINALOD = $sumRetiOd;
			}	
			else{
				$wnlRetExm = !empty($row["wnl_value_RetinalExam"]) ? $row["wnl_value_RetinalExam"] : $oCn->getExamWnlStr("Retinal Exam");
				$sumRetiOd = "".$wnlRetExm." ".$wnlPeri;
				$strRETINALOD = $sumRetiOd;
			}		
			
			if($res["wnlRetinalOs"] == 0){
				if($res["periNotExamined"]==1 && ($res["peri_ne_eye"]=="OU" || $res["peri_ne_eye"]=="OS")){ 
					if(!empty($res["retinal_os_summary"])){ $res["retinal_os_summary"].=";";  }
					$res["retinal_os_summary"] = $res["retinal_os_summary"]." periphery not examined";
				}
				$sumRetiOs = wordwrap($this->clearSumm($res["retinal_os_summary"]), 20, "<br />\n");			
				$strRETINALOS = $sumRetiOs;
			}	
			else{
				$wnlRetExm = !empty($row["wnl_value_RetinalExam"]) ? $row["wnl_value_RetinalExam"] : $oCn->getExamWnlStr("Retinal Exam");
				$sumRetiOs = "".$wnlRetExm." ".$wnlPeri;
				$strRETINALOS = $sumRetiOs;
			}	
			///Retinal
			
			if($res["wnlOpticOd"] == 0){
				$sumOptOd = wordwrap($this->clearSumm($res["optic_nerve_od_summary"]), 20, "<br />\n");
				$strOPTICNERVEOD = $sumOptOd;
			}
			else{
				$wnlON = !empty($row["wnl_value_Optic"]) ? $row["wnl_value_Optic"] : $oCn->getExamWnlStr("Optic Nerve");
				$sumOptOd = $wnlON;//"Pink & Sharp";
				$strOPTICNERVEOD = $sumOptOd;
			}
			
			if($res["wnlOpticOs"] == 0){
				$sumOptOs = wordwrap($this->clearSumm($res["optic_nerve_os_summary"]), 20, "<br />\n");
				$strOPTICNERVEOS = $sumOptOs;
			}
			else{
				$wnlON = !empty($row["wnl_value_Optic"]) ? $row["wnl_value_Optic"] : $oCn->getExamWnlStr("Optic Nerve");
				$sumOptOs = $wnlON;//"Pink & Sharp";
				$strOPTICNERVEOS = $sumOptOs;
			}
			if($res["cd_val_od"] != ""){
				$CDOd = wordwrap($res["cd_val_od"], 20, "<br />\n");
			}
			else if($res["od_text"] != ""){
				$CDOd = wordwrap($res["od_text"], 20, "<br />\n");
				$strCDOD = $CDOd;
			}
			if($res["cd_val_os"] != ""){
				$CDOs = wordwrap($res["cd_val_os"], 20, "<br />\n");
			}
			else if($res["os_text"] != ""){
				$CDOs = wordwrap($res["os_text"], 20, "<br />\n");
				$strCDOS = $CDOs;
			}
			
			$arrOd = array();
			$arrOs = array();
			
			if(!empty($CDOd)){
				$arrOd[] = "<b>C:D</b>&nbsp;".$CDOd.'</br>';
				$strCDOD = "<b>C:D</b>&nbsp;".$CDOd.'</br>';
			}
			if(!empty($CDOs)){
				$arrOs[] = "<b>C:D</b>&nbsp;".$CDOs.'</br>';
				$strCDOS = "<b>C:D</b>&nbsp;".$CDOs.'</br>';
			}
			
			if(!empty($sumOptOd) && strpos($res["statusElem"],"elem_chng_div1_Od=1") !== false){
				$arrOd[] = "<b>Opt.Nev</b>&nbsp;".$sumOptOd.'</br>';
				$strOPTICNERVEOD = "<b>Opt.Nev</b>&nbsp;".$sumOptOd.'</br>';
			}
			if(!empty($sumOptOs) && strpos($res["statusElem"],"elem_chng_div1_Os=1") !== false){
				$arrOs[] = "<b>Opt.Nev</b>&nbsp;".$sumOptOs.'</br>';
				$strOPTICNERVEOS = "<b>Opt.Nev</b>&nbsp;".$sumOptOs.'</br>';
			}
			if(!empty($sumMacOd)){
				$arrOd[] = "<b>Macula</b>&nbsp;".$sumMacOd.'</br>';
				$strMACULAOD = "<b>Macula</b>&nbsp;".$sumMacOd.'</br>';
			}
			if(!empty($sumMacOs)){
				$arrOs[] = "<b>Macula</b>&nbsp;".$sumMacOs.'</br>';
				$strMACULAOS = "<b>Macula</b>&nbsp;".$sumMacOs.'</br>';
			}
			//Retinal
			
			if(!empty($sumRetiOd) && strpos($res["statusElem"],"elem_chng_div7_Od=1") !== false){
				$arrOd[] = "<b>Retinal Exam</b>&nbsp;".$sumRetiOd.'</br>';
				$strRETINALOD = "<b>Retinal Exam</b>&nbsp;".$sumRetiOd.'</br>';
			}
			if(!empty($sumRetiOs) && strpos($res["statusElem"],"elem_chng_div7_Os=1") !== false){
				$arrOs[] = "<b>Retinal Exam</b>&nbsp;".$sumRetiOs.'</br>';
				$strRETINALOS = "<b>Retinal Exam</b>&nbsp;".$sumRetiOs.'</br>';
			}
			//Retinal
			if(!empty($sumVitOd) && strpos($res["statusElem"],"elem_chng_div1_Od=1") !== false){
				$arrOd[] = "<b>Vitreous</b>&nbsp;".$sumVitOd.'</br>';
				$strVITREOUSOD = "<b>Vitreous</b>&nbsp;".$sumVitOd.'</br>';
			}
			if(!empty($sumVitOs) && strpos($res["statusElem"],"elem_chng_div1_Os=1") !== false){
				$arrOs[] = "<b>Vitreous</b>&nbsp;".$sumVitOs.'</br>';
				$strVITREOUSOS = "<b>Vitreous</b>&nbsp;".$sumVitOs.'</br>';
			}
			if(!empty($sumPeriOd)){
				$arrOd[] = "<b>Periphery</b>&nbsp;".$sumPeriOd.'</br>';
				$strPERIPHERYOD = "<b>Periphery</b>&nbsp;".$sumPeriOd.'</br>';
			}
			if(!empty($sumPeriOs)){
				$arrOs[] = "<b>Periphery</b>&nbsp;".$sumPeriOs.'</br>';
				$strPERIPHERYOS = "<b>Periphery</b>&nbsp;".$sumPeriOs.'</br>';
			}
			if(!empty($sumBvOd)){
				$arrOd[] = "<b>Vessels</b>&nbsp;".$sumBvOd.'</br>';
				$strBVOD = "<b>Vessels</b>&nbsp;".$sumBvOd.'</br>';
			}
			if(!empty($sumBvOs)){
				$arrOs[] = "<b>Vessels</b>&nbsp;".$sumBvOs.'</br>';
				$strBVOS = "<b>Vessels</b>&nbsp;".$sumBvOs.'</br>';
			}			
			
			$sumOd = "";
			if(count($arrOd) > 0){
				foreach($arrOd as $key => $val){
					$sumOd .= (!empty($val)) ? $val : "";
				}				
				//Last comma removal
				$sumOd = preg_replace("/,\s*$/", "", $sumOd);				
			}
			
			$sumOs = "";
			if(count($arrOs) > 0){
				foreach($arrOs as $key => $val){
					$sumOs .= (!empty($val)) ? $val : "";
				}				
				//Last comma removal
				$sumOs = preg_replace("/,\s*$/", "", $sumOs);
			}
			
			$sumOu = trim($sumOd." ".$sumOs);
			$FUNDUS = trim($sumOd." ".$sumOs);
			//Replace Array
			$arrRep = array($sumOu, $sumOd, $sumOs, $FUNDUS,$strOPTICNERVEOD, $strOPTICNERVEOS, $strMACULAOD, $strMACULAOS, $strRETINALOD, $strRETINALOS, $strVITREOUSOD, $strVITREOUSOS, $strPERIPHERYOD, $strPERIPHERYOS, $strBVOD, $strBVOS,$strCDOD,$strCDOS);
			
		}
		//Replace
		$str = $this->refineData($str, $this->kFundus, $arrRep, $this->tFundus,"");
		return $str;
	}
	
	//Get Fundus Data in Table Format
	private function getFundusAll($str, $ptId, $formId){
		/**
		 * chart_rv TABLE SPLITTING TABLES WORK - EVERY SECTION OF FUNDUS EXAM HAS OWN INDEPENDENT TABLE
		 * 
		 */
		$sql = "SELECT 
				  c2.wnlRetinalOd,
				  c2.wnlRetinalOs,
				  c2.peri_ne_eye,
				  c2.periNotExamined,
				  c2.retinal_od_summary,
				  c2.retinal_os_summary,
				  c2.wnl_value_RetinalExam,
				  
				  c3.wnlVitreousOd,
				  c3.wnlVitreousOs,
				  c3.vitreous_od_summary,
				  c3.vitreous_os_summary,
				  c3.wnl_value_Vitreous,
				  
				  c4.wnlBVOd,
				  c4.wnlBVOs,
				  c4.wnl_value_BV,
				  c4.blood_vessels_od_summary,
				  c4.blood_vessels_os_summary,
				  
				  c5.macula_od_summary,
				  c5.macula_os_summary,
				  c5.wnlMaculaOd,
				  c5.wnlMaculaOs,
				  c5.wnl_value_Macula,
				  
				  c6.periphery_os_summary,
				  c6.periphery_od_summary,
				  c6.wnlPeriOd,
				  c6.wnlPeriOs,
				  c6.wnl_value_Peri,
				  
				  chart_optic.optic_nerve_od_summary,
				  chart_optic.optic_nerve_os_summary,
				  chart_optic.wnlOpticOd,
				  chart_optic.wnlOpticOs,
				  chart_optic.od_text,
				  chart_optic.os_text,
				  chart_optic.wnl_value_Optic,
				  
				  disc.cdOd,
				  disc.cdOs

				FROM
				  chart_master_table c1
				  LEFT JOIN chart_retinal_exam c2 ON c1.id = c2.form_id AND c2.purged = '0'
				  LEFT JOIN chart_vitreous c3 ON c1.id = c3.form_id  AND c3.purged = '0'
				  LEFT JOIN chart_blood_vessels c4 ON c1.id = c4.form_id  AND c4.purged = '0'
				  LEFT JOIN chart_macula c5 ON c1.id = c5.form_id  AND c5.purged = '0'
				  LEFT JOIN chart_periphery c6 ON c1.id = c6.form_id  AND c6.purged = '0'
				  LEFT JOIN chart_optic ON chart_optic.form_id = c1.id AND chart_optic.purged = '0'
				  LEFT JOIN disc ON disc.formId = c1.id  AND disc.del_status = '0' AND disc.purged = '0'
				 
				WHERE
				  c1.id = '".$formId."'
				  AND c1.patient_id = '".$ptId."'
				  AND c1.purge_status = '0'
				  AND c1.delete_status = '0'";
		
		$res = sqlQuery($sql);
		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv For Merged NSE Server
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
			if($res == false){
				$sql = str_replace("chart_rv","chart_rv_archive",$sql);
				$res=sqlQuery($sql);	
			}
		}
		//-------- End ----------------------------------------------------------------------------------	
		if($res !== false){			
			$strOPTICNERVEOD = $strOPTICNERVEOS = $strMACULAOD = $strMACULAOS = $strVITREOUSOD = $strVITREOUSOS = $strPERIPHERYOD = $strPERIPHERYOS = $strBVOD = $strBVOS = $strRETINALOD= $strRETINALOS="";
			$oCn = new ChartNote($ptId, $formId);
			if($res["wnlVitreousOd"] == 0){
				$sumVitOd = wordwrap($this->clearSumm($res["vitreous_od_summary"]), 20, "<br />\n");
				$strVITREOUSOD = $sumVitOd;
			}
			else{
				$sumVitOd = !empty($res["wnl_value_Vitreous"]) ? $res["wnl_value_Vitreous"] : $oCn->getExamWnlStr("Vitreous");
				$strVITREOUSOD = $sumVitOd;
			}
			
			if($res["wnlVitreousOs"] == 0){
				$sumVitOs = wordwrap($this->clearSumm($res["vitreous_os_summary"]), 20, "<br />\n");
				$strVITREOUSOS = $sumVitOs;
			}
			else{
				$sumVitOs = !empty($res["wnl_value_Vitreous"]) ? $res["wnl_value_Vitreous"] : $oCn->getExamWnlStr("Vitreous");
				$strVITREOUSOS = $sumVitOs;
			}
			
			if($res["wnlPeriOd"] == 0){
				$sumPeriOd = wordwrap($this->clearSumm($res["periphery_od_summary"]), 20, "<br />\n");
				$strPERIPHERYOD = $sumPeriOd;
			}
			else{
				$sumPeriOd = !empty($res["wnl_value_Peri"]) ? $res["wnl_value_Peri"] :  $oCn->getExamWnlStr("Periphery");
				$strPERIPHERYOD = $sumPeriOd;
			}
			
			if($res["wnlPeriOs"] == 0){
				$sumPeriOs = wordwrap($this->clearSumm($res["periphery_os_summary"]), 20, "<br />\n");			
				$strPERIPHERYOS = $sumPeriOs;
			}
			else{
				$sumPeriOs = !empty($res["wnl_value_Peri"]) ? $res["wnl_value_Peri"] :  $oCn->getExamWnlStr("Periphery");			
				$strPERIPHERYOS = $sumPeriOs;
			}
			
			if($res["wnlBVOd"] == 0){
				$sumBvOd = wordwrap($this->clearSumm($res["blood_vessels_od_summary"]), 20, "<br />\n");
				$strBVOD = $sumBvOd;
			}
			else{
				$sumBvOd = !empty($res["wnl_value_BV"]) ? $res["wnl_value_BV"] :  $oCn->getExamWnlStr("Blood Vessels");
				$strBVOD = $sumBvOd;
			}
			
			if($res["wnlBVOs"] == 0){
				$sumBvOs = wordwrap($this->clearSumm($res["blood_vessels_os_summary"]), 20, "<br />\n");
				$strBVOS = $sumBvOs;
			}
			else{
				$sumBvOs = !empty($res["wnl_value_BV"]) ? $res["wnl_value_BV"] :  $oCn->getExamWnlStr("Blood Vessels");
				$strBVOS = $sumBvOs;
			}
			
			if($res["wnlMaculaOd"] == 0){
				$sumMacOd = wordwrap($this->clearSumm($res["macula_od_summary"]), 20, "<br />\n");			
				$strMACULAOD = $sumMacOd;
			}	
			else{
				$sumMacOd = !empty($res["wnl_value_Macula"]) ? $res["wnl_value_Macula"] :  $oCn->getExamWnlStr("Macula");
				$strMACULAOD = $sumMacOd;
			}		
			
			if($res["wnlMaculaOs"] == 0){
				$sumMacOs = wordwrap($this->clearSumm($res["macula_os_summary"]), 20, "<br />\n");
				$strMACULAOS = $sumMacOs;
			}
			else{
				$sumMacOs = !empty($res["wnl_value_Macula"]) ? $res["wnl_value_Macula"] :  $oCn->getExamWnlStr("Macula");
				$strMACULAOS = $sumMacOs;
			}
			
			///Retinal
			if(isset($GLOBALS["ADD_PERI_WNL_2_RETINAL"])&&!empty($GLOBALS["ADD_PERI_WNL_2_RETINAL"])){
				if($res["periNotExamined"]!=1){$wnlPeri="periphery normal";}else{$wnlPeri="periphery not examined";}
			}
			if($res["wnlRetinalOd"] == 0){
				if($res["periNotExamined"]==1 && ($res["peri_ne_eye"]=="OU" || $res["peri_ne_eye"]=="OD")){ 
					if(!empty($res["retinal_od_summary"])){ $res["retinal_od_summary"].=";";  }
					$res["retinal_od_summary"] = $res["retinal_od_summary"]." periphery not examined";
				}
				$sumRetiOd = wordwrap($this->clearSumm($res["retinal_od_summary"]), 20, "<br />\n");					
				$strRETINALOD = $sumRetiOd;
			}	
			else{
				$wnlRetExm = !empty($row["wnl_value_RetinalExam"]) ? $row["wnl_value_RetinalExam"] : $oCn->getExamWnlStr("Retinal Exam");
				$sumRetiOd = "".$wnlRetExm." ".$wnlPeri;
				$strRETINALOD = $sumRetiOd;
			}		
			
			if($res["wnlRetinalOs"] == 0){
				if($res["periNotExamined"]==1 && ($res["peri_ne_eye"]=="OU" || $res["peri_ne_eye"]=="OS")){ 
					if(!empty($res["retinal_os_summary"])){ $res["retinal_os_summary"].=";";  }
					$res["retinal_os_summary"] = $res["retinal_os_summary"]." periphery not examined";
				}
				$sumRetiOs = wordwrap($this->clearSumm($res["retinal_os_summary"]), 20, "<br />\n");			
				$strRETINALOS = $sumRetiOs;
			}	
			else{
				$wnlRetExm = !empty($row["wnl_value_RetinalExam"]) ? $row["wnl_value_RetinalExam"] : $oCn->getExamWnlStr("Retinal Exam");
				$sumRetiOs = "".$wnlRetExm." ".$wnlPeri;
				$strRETINALOS = $sumRetiOs;
			}	
			
			if($res["wnlOpticOd"] == 0){
				$sumOptOd = wordwrap($this->clearSumm($res["optic_nerve_od_summary"]), 20, "<br />\n");
				$strOPTICNERVEOD = $sumOptOd;
			}
			else{
				$wnlON = !empty($row["wnl_value_Optic"]) ? $row["wnl_value_Optic"] : $oCn->getExamWnlStr("Optic Nerve");
				$sumOptOd = $wnlON;//"Pink & Sharp";
				$strOPTICNERVEOD = $sumOptOd;
			}
			
			if($res["wnlOpticOs"] == 0){
				$sumOptOs = wordwrap($this->clearSumm($res["optic_nerve_os_summary"]), 20, "<br />\n");
				$strOPTICNERVEOS = $sumOptOs;
			}
			else{
				$wnlON = !empty($row["wnl_value_Optic"]) ? $row["wnl_value_Optic"] : $oCn->getExamWnlStr("Optic Nerve");
				$sumOptOs = $wnlON;//"Pink & Sharp";
				$strOPTICNERVEOS = $sumOptOs;
			}
			if($res["cdOd"] != ""){
				$CDOd = wordwrap($res["cdOd"], 20, "<br />\n");
			}
			else if($res["od_text"] != ""){
				$CDOd = wordwrap($res["od_text"], 20, "<br />\n");
				$strCDOD = $CDOd;
			}
			if($res["cdOs"] != ""){
				$CDOs = wordwrap($res["cdOs"], 20, "<br />\n");
			}
			else if($res["os_text"] != ""){
				$CDOs = wordwrap($res["os_text"], 20, "<br />\n");
				$strCDOS = $CDOs;
			}
			$fundus_val =0;
			$table="";
			if(!empty($CDOd) || !empty($CDOs)){
				$fundus_val = 1;
				$table.= '<tr>
							<td style="width:45%;vertical-align:top;"><b>C:D</b>&nbsp;'.$CDOd.'</td>
							<td style="width:45%;vertical-align:top;"><b>C:D</b>&nbsp;'.$CDOs.'</td>
						 </tr>';
			}
			
			if(!empty($sumOptOd) ||  !empty($sumOptOs)){
				$fundus_val = 1;
				$table.= '<tr>
							<td style="width:45%;vertical-align:top;"><b>Opt.Nev</b>&nbsp;'.$sumOptOd.'</td>
							<td style="width:45%;vertical-align:top;"><b>Opt.Nev</b>&nbsp;'.$sumOptOs.'</td>
						  </tr>';
			}
			if(!empty($sumMacOd) ||  !empty($sumMacOs)){
				$fundus_val = 1;
				$table.= '<tr>
							<td style="width:45%;vertical-align:top;"><b>Macula</b>&nbsp;'.$sumMacOd.'</td>
							<td style="width:45%;vertical-align:top;"><b>Macula</b>&nbsp;'.$sumMacOs.'</td>
						  </tr>';
			}
			if(!empty($sumRetiOd) ||  !empty($sumRetiOs)){
				$fundus_val = 1;
				$table.= '<tr>
							<td style="width:45%;vertical-align:top;"><b>Retinal Exam</b>&nbsp;'.$sumRetiOd.'</td>
							<td style="width:45%;vertical-align:top;"><b>Retinal Exam</b>&nbsp;'.$sumRetiOs.'</td>
						  </tr>';
			}
			if(!empty($sumVitOd) ||  !empty($sumVitOs)){
				$fundus_val = 1;
				$table.= '<tr>
							<td style="width:45%;vertical-align:top;"><b>Vitreous</b>&nbsp;'.$sumVitOd.'</td>
							<td style="width:45%;vertical-align:top;"><b>Vitreous</b>&nbsp;'.$sumVitOs.'</td>
						  </tr>';
			}
			if(!empty($sumPeriOd) ||  !empty($sumPeriOs)){
				$fundus_val = 1;
				$table.= '<tr>
							<td style="width:45%;vertical-align:top;"><b>Periphery</b>&nbsp;'.$sumPeriOd.'</td>
							<td style="width:45%;vertical-align:top;"><b>Periphery</b>&nbsp;'.$sumPeriOs.'</td>
						  </tr>';
			}
			if(!empty($sumBvOd) ||  !empty($sumBvOs)){
				$fundus_val = 1;
				$table.= '<tr>
							<td style="width:45%;vertical-align:top;"><b>Vessels</b>&nbsp;'.$sumBvOd.'</td>
							<td style="width:45%;vertical-align:top;"><b>Vessels</b>&nbsp;'.$sumBvOs.'</td>
						  </tr>';
			}
			$table_h= '<table cellpadding="0" border="0" cellspacing="0" style="width:100%">';
			$table_f ='</table>';		 
			if($fundus_val==1){
				$table = $table_h.$table.$table_f;
			}
			//Replace Array
			$arrRep = array($table);
			
		}
		//Replace
		$str = $this->refineData($str, $this->kFundusAll, $arrRep, $this->tFundusAll,"");
		return $str;
	}
	
	//Get AP
	private function getAP($str, $ptId, $formId){
		global $web_RootDirectoryName;
		$strAssessment1 = $strAssessment2 = $strAssessment3 = $strAssessment4 = $strAssessment5 = "";
		$strPlan1 = $strPlan2 = $strPlan3 = $strPlan4 = $strPlan5 = "";
		 $sql = "SELECT ".
			 "cap.assessment_1, cap.assessment_2, cap.assessment_3, cap.assessment_4, ".
			 "cap.plan_notes_1, cap.plan_notes_2, cap.plan_notes_3, cap.plan_notes_4, ".
			 "cap.follow_up, cap.follow_up_numeric_value, cap.followUpVistType, ".
			 "cap.doctorId, cap.sign_coords, cap.id,cap.assess_plan,cap.followup,  ".
			 "CONCAT_WS(', ',us.lname,us.fname) as scribed_by_opr  ".
			 
			 " FROM chart_assessment_plans cap  
			 LEFT JOIN users us ON (us.id = cap.scribedBy AND cap.scribedBy!='0')
			 WHERE form_id = '".$formId."' AND patient_id = '".$ptId."' ";
		$res = sqlQuery($sql);
		if($res !== false){
			$scribed_by_opr = $res["scribed_by_opr"];
			$a1 = $res["assessment_1"];
			$a2 = $res["assessment_2"];
			$a3 = $res["assessment_3"];
			$assess = $res["assessment_4"];
			
			$p1 = $res["plan_notes_1"];
			$p2 = $res["plan_notes_2"];
			$p3 = $res["plan_notes_3"];
			$plan = $res["plan_notes_4"];
			$strXml = $res["assess_plan"];
			$aAll = $pAll = $FUDatta = $folllowUp = $eyeAll = "";
			//--- Set Assess Plan Resolve NE Value FROM xml// Start					 
			//$arrApVals = $this->getVal_Str($strXml);
			$oChartAP  = new ChartAP($ptId, $formId);
			$arrApVals = $oChartAP->getVal();
			
			$arrAp = $arrApVals["data"]["ap"];
			//pre($arrAp);
			//echo '<pre>';
			//print_r($arrAp);
			//echo count($arrAp);
			for($i=0;$i<count($arrAp);$i++){
				$j=$i+1;	
				$elem_assessment = "assessment_".$j;
				$$elem_assessment = $arrAp[$i]["assessment"];
				$elem_plan = "plan_notes_".$j;
				$$elem_plan = $arrAp[$i]["plan"];
				$$elem_plan = preg_replace('/[^`~!<>@$?a-zA-Z0-9_{}:; ,"#%\[\]\.\(\)%&-\/\\r\\n\\\\]/s','',$$elem_plan);
				$elem_resolve = "elem_resolve".$j;
				$$elem_resolve = $arrAp[$i]["resolve"];
				$elem_ne = "elem_ne".$j;
				$$elem_ne = $arrAp[$i]["ne"];
				
				$elem_eye = "eye_".$j;
				$$elem_eye = $arrAp[$i]["eye"];
				
				$no_change_Assess = "no_change_Assess".$j;
				/*$$no_change_Assess = $arrAp[$i]["ne"];
				if($$no_change_Assess) $$no_change_Assess = '<b>NE '.$j.'</b>';
				if($$elem_resolve){
					if($$no_change_Assess){
					$$no_change_Assess.= ', <b>RES '.$j.'</b>';
					}else{
					$$no_change_Assess = '<b>RES '.$j.'</b>';
					}
				}
				if(!$$no_change_Assess){
					$$no_change_Assess = '<b>'.$j.'</b>';
				}*/
				if($$elem_resolve=="1"){
					$$elem_assessment=$$elem_assessment." - Resolved";
				}
				//========BELOW CODE USED TO REMOVED DX CODES FROM ASSESSMENTS FOR SIO SERVER ONLY===========
				if($web_RootDirectoryName=="SIO" || $web_RootDirectoryName=="AEC"){
					$$elem_assessment = preg_replace("/\([^)]+\)/","",$$elem_assessment);
				}
				//==========================END TASK=========================================================
				if($$elem_assessment && $$elem_ne!="1"){					
					switch ($i):
						case 0:
							$strAssessment1 = $$elem_assessment;
							break;
						case 1:
							$strAssessment2 = $$elem_assessment;
							break;
						case 2:
							$strAssessment3 = $$elem_assessment;
							break;
						case 3:
							$strAssessment4 = $$elem_assessment;
							break;
						case 4:
							$strAssessment5 = $$elem_assessment;
							break;	
					endswitch;

					$aAll .= $$no_change_Assess.' '.$$elem_assessment.'</br>';
					$eyeAll .= $$elem_eye.'</br>'; 	
					
				}
				//echo $$elem_plan.'<br>';
				//if($$elem_plan && $$elem_ne!="1")
				if($$elem_ne!="1"){
					switch ($i):
						case 0:
							$strPlan1 = nl2br($$elem_plan);
							break;
						case 1:
							$strPlan2 = nl2br($$elem_plan);
							break;
						case 2:
							$strPlan3 = nl2br($$elem_plan);
							break;
						case 3:
							$strPlan4 = nl2br($$elem_plan);
							break;
						case 4:
							$strPlan5 = nl2br($$elem_plan);
							break;	
					endswitch;
					$pAll .= $$no_change_Assess.' '.$$elem_plan; 
					//if($$elem_plan) {$pAll .= '.'; }
					$pAll .= '</br>'; 				
				}				
			}
			//--- Set Assess Plan Resolve NE Value FROM xml// End					
						
			//function ClearAssessPlan()
			/*$arrAssess = $arrPlan = $arrResolve = $arrNe = array();
			$arrAssessTmp = explode("^A|^",$assess);
			$len = count($arrAssessTmp);
			for($i=1;$i<$len;$i++)
			{			
				$tmp = explode("^R|^",$arrAssessTmp[$i]);
				$arrAssess[] = $tmp[0];			
				$tmpCheck = $tmp[1];
				
				$arrCheckTemp = explode("^NE|^",$tmpCheck);
				$arrResolve[]=$arrCheckTemp[0];
				$arrNe[]=$arrCheckTemp[1];
			}
			$arrPlan = explode("^P|^",$plan);
			$arrPlan = array_slice($arrPlan,1);			
			
			//$a1 = (!empty($a1)) ? $a1."<br>" : "";
			//$a2 = (!empty($a2)) ? $a2."<br>" : "";
			//$a3 = (!empty($a3)) ? $a3."<br>" : "";
			
			$aAll ="";
			$aAll .= (!empty($a1)) ? $a1."<br>" : "";//"Not Performed<br>";
			$aAll .= (!empty($a2)) ? $a2."<br>" : "";//"Not Performed<br>";
			$aAll .= (!empty($a3)) ? $a3."<br>" : "";//"Not Performed<br>";
			
			$arrOtherAsss=array();
			$lenAssess = count($arrAssess);
			if($lenAssess > 0){
				for( $i=0;$i<$lenAssess;$i++ ){
					
					if(($i+4) > 5){
						$arrOtherAsss["{ASSESSMENT ".($i+4)."}"] = "";
					}
					
					if(!empty($arrAssess[$i])){
						$tmp = "a".($i+4);
						$$tmp = $arrAssess[$i];
						$aAll .= $arrAssess[$i]."<br>";
						
						if(($i+4) > 5){
							$arrOtherAsss["{ASSESSMENT ".($i+4)."}"] = $arrAssess[$i];
						}
					}else{
						$aAll .= "";//"Not Performed<br>";
					}
					
				}
			}
			
			//$p1 = (!empty($p1)) ? $p1."<br>" : "";
			//$p2 = (!empty($p2)) ? $p2."<br>" : "";
			//$p3 = (!empty($p3)) ? $p3."<br>" : "";
			
			$pAll = "";
			$pAll .= (!empty($p1)) ? $p1."<br>" : "";//"Not Performed<br>";
			$pAll .= (!empty($p2)) ? $p2."<br>" : "";//"Not Performed<br>";
			$pAll .= (!empty($p3)) ? $p3."<br>" : "";//"Not Performed<br>";
			
			$arrOtherPlan=array();
			$lenPlan = count($arrPlan);
			if($lenPlan > 0){
				for($i=0;$i<$lenPlan;$i++){
					
					if(($i+4) > 5){
						$arrOtherPlan["{PLAN ".($i+4)."}"] = "";
					}
					
					if(!empty($arrPlan[$i])){
						$tmp = "p".($i+4);
						$$tmp = $arrPlan[$i];
						$pAll .= $arrPlan[$i]."<br>";
						
						if(($i+4) > 5){
							$arrOtherPlan["{PLAN ".($i+4)."}"] = $arrPlan[$i];
						}						
					}else{
						$pAll .= "";//"Not Performed<br>";
					}
				}
			}
			
			//Assess & Plan
			$ap = "";
			$len = ($lenPlan > $lenAssess) ? $lenPlan : $lenAssess;
			$len = $len + 3;
			for($i=0;$i<$len;$i++){
				$tmpA = "a".($i+1);
				$tmpP = "p".($i+1);
				
				if(!empty($$tmpA) || !empty($$tmpP)){
					$tA = (!empty($$tmpA)) ? "<b>Assessment ".($i+1).":</b> ".$$tmpA : "";
					$tP = (!empty($$tmpP)) ? "<b>Plan ".($i+1).":</b> ".$$tmpP : "";
					//$ap .= "<tr><td style=\"border:0px;\">".$tA."</td><td style=\"border:0px;\">".$tP."</td></tr>";
					$ap .= "<br /><span>".$tA."</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>".$tP."</span>";
				}
			}
			
			//if(!empty($ap)){
				//$ap = "<table style=\"border:0px;\">".$ap."</table>";
			//}
			
			
			/*
			#Debugg
				
				echo "<br>A-".($lenAssess+3);
				
				for($i=0;($i<$lenAssess+3);$i++){
					$tmp = "a".($i+1);
					echo "<br>".$$tmp;
				}
				
				echo "Plan<br>";
				echo "<br>P-".($lenPlan+3);
				
				for($i=0;$i<($lenPlan+3);$i++){
					$tmp = "p".($i+1);
					echo "<br>".$$tmp;
				}
					
			#Debugg
			*/
			
			//"follow_up, follow_up_numeric_value, followUpVistType ".
			//Follow-up
			if($res["follow_up_numeric_value"]){				
				$followUpNumber = $res["follow_up_numeric_value"];
				if($followUpNumber == "13"){
					$followUpNumber = "PRN";
				}
				if($followUpNumber == "14"){
					$followUpNumber = "PMD";
				}
				$followUpTmp = $res["follow_up"];
				$followUpVistType = $res["followUpVistType"];
				
				$folllowUp .= (!empty($followUpNumber)) ? $followUpNumber." " : "";
				$folllowUp .= (!empty($followUpTmp)) ? $followUpTmp." " : "";
				$folllowUp .= (!empty($followUpVistType)) ? $followUpVistType." " : "";			
			}elseif($res["followup"]){
				$followup = $res["followup"];
				$oFu =  new Fu($ptId, $formId);				
				$arrFollowup = $oFu->fu_getXmlValsArr($followup);				
				for($a=0;$a<count($arrFollowup[1]);$a++){					
					if($a==0){
						//$FUDatta.='F/U ';
					}
					else{
						$FUDatta.=' ';
					}
					//[0] removed from below codes because it was displaying only first characters of follow up...
					$number= $arrFollowup[1][$a]['number'];				
					$FUDatta.=' '.$number;		
					$time= $arrFollowup[1][$a]['time'];
					$FUDatta.=' '.$time;					
					$visit_type= $arrFollowup[1][$a]['visit_type'];
					if(trim($visit_type)) {
						$FUDatta.=' for '.$visit_type;					
					}
					//if(trim($FUDatta) != "F/U")
					if(count($arrFollowup[1])>1 && (count($arrFollowup[1])-1)>$a && (trim($arrFollowup[1][$a+1]['number'])!="" || trim($arrFollowup[1][$a+1]['time'])!="" || trim($arrFollowup[1][$a+1]['visit_type'])!="")){
						$FUDatta .= "</br>";
					}
					
				}					
			}
			/*//Check of only 'Not Perfoemed' in aAll and pAll
			$tmp = trim(str_replace("Not Performed<br>","",$aAll));
			$ptrn = "/{ASSESSMENT \d+}/";	
			$tmp = preg_replace($ptrn,"",$tmp);			
			if(trim($tmp) == ""){
				$aAll = "";//"Not Performed<br>";
			}
			
			$tmp = trim(str_replace("Not Performed<br>","",$pAll));
			$ptrn = "/{PLAN \d+}/";
			$tmp = preg_replace($ptrn,"",$tmp);
			if(trim($tmp) == ""){
				$pAll = ""; //"Not Performed<br>";
			}
			*/
			//Replace Array
			$arrRep = array();
			$aAll = trim($aAll);
			$eyeAll	 = trim($eyeAll);					
			$pattern = '/\d{3}.\d{2}/';						
			$aAll = preg_replace($pattern,"",$aAll);			
			$aAll = str_replace("()","",$aAll);
			//echo htmlentities($aAll);
			$arrAllExplode = array();
			$arrAllExplode = explode("</br>",$aAll);
			
			$aAllNew = "";
			
			//print_r($arrAllExplode);
			if(count($arrAllExplode)>0){
				$aAllNew ='<table cellpadding="2" cellspacing="0">';
				//$aAllNew .=	'<table style="border-collapse:collapse; ">'; 		
				$arrAllCnt = 0;
				foreach($arrAllExplode as $arrAllKey=> $value){
					if($value){
						$arrAllCnt=$arrAllKey+1;  
						if(strlen($value) > 50){
							$aAllNew .= '<tr><td style="vertical-align:top;text-align:center;width:3%;padding-top:5px;">'.$arrAllCnt.'.&nbsp;</td><td style="width:95%;padding-top:5px;">'.$value.'</td></tr>';
						}
						else{
							$aAllNew .= '<tr><td style="vertical-align:top;padding-top:5px;text-align:center;width:3%;">'.$arrAllCnt.'.&nbsp;</td><td style="width:95%;padding-top:5px;">'.$value.'</td></tr>';
						}
						//$aAllNew .=	'<tr>';
						//$aAllNew .=		'<td style=" vertical-align:top; width: 270px;" >'.$value.'</td>';
						//$aAllNew .=	'</tr>';
						
					}
				}
				//$aPllNew .=	'</table>'; 		
				$aAllNew .='</table>';
			}
			
			//echo $aAllNew;
			$aAllNew = str_ireplace("Not Examined","",$aAllNew);
			$arrRep[] = $aAllNew;
			
			//echo htmlentities($aAll);
			$arrPllExplode = array();
			$arrPllExplode = explode("</br>",$pAll);
			$aPllNew = "";
			//print_r($arrPllExplode);
			if(count($arrPllExplode)>0){
				$aPllCnt = 0;
				foreach($arrPllExplode as $arrPllKey => $value){
					if($value){
						$aPllCnt=$arrPllKey+1;
						/*if(strlen($value) > 50){
							$aPllNew .= $aPllCnt.'.&nbsp;'.wordwrap($value, 50, "<br />\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;").'<br />';
						}
						else{*/
							$aPllNew .= $aPllCnt.'.&nbsp;'.$value.'<br />';
						//}
					}
				}
			}
			//echo $aPllNew;
			$aPllNew = nl2br($aPllNew);
			$aPllNew = str_ireplace("Not Examined","",$aPllNew);
			$arrRep[] = $aPllNew;
			if($folllowUp){
				$arrRep[] = $folllowUp;
			}
			elseif(trim($FUDatta) != "F/U"){
			//	if(trim($FUDatta) != "F/U"){					
					$arrRep[] = wordwrap($FUDatta, 100, "<br />\n");
			//	}	
			}
			else {
				$arrRep[]='';
			}
			
			$ap = "Assessment: ".$aAllNew."<br />Plan: ".$aPllNew;
			$ap = str_ireplace("Not Examined","",$ap);
			$arrRep[] = $ap;
			
			for($z=1;$z<=5;$z++) {
				$ass = "strAssessment".$z;
				$pln = "strPlan".$z;
				$$ass = str_ireplace("Not Examined","",$$ass);
				$$pln = str_ireplace("Not Examined","",$$pln);
			}
			
			$arrRep[] = $strAssessment1;
			$arrRep[] = $strAssessment2;
			$arrRep[] = $strAssessment3;
			$arrRep[] = $strAssessment4;
			$arrRep[] = $strAssessment5;
			
			$arrRep[] = $strPlan1;
			$arrRep[] = $strPlan2;
			$arrRep[] = $strPlan3;
			$arrRep[] = $strPlan4;
			$arrRep[] = $strPlan5;
						
			/*for($i=1;$i<=5;$i++){
				$tmp = "a".$i;
				$arrRep[] = (!empty($$tmp)) ? $$tmp."<br>" : "";
			}
			for($i=1;$i<=5;$i++){
				$tmp = "p".$i;
				$arrRep[] = (!empty($$tmp)) ? $$tmp."<br>" : "";
			}
			
			if(count($arrOtherAsss) > 0){
				foreach($arrOtherAsss as $key => $var ){
					$this->kAP[] = $key;
					$arrRep[] = $var."<br>";
				}
			}
			
			if(count($arrOtherPlan) > 0){
				foreach($arrOtherPlan as $key => $var ){
					$this->kAP[] = $key;
					$arrRep[] = $var."<br>";
				}
			}
			*/
			//echo "<br>HELO";
			//print_r($arrRep);
			
			$arrEyeAllExplode = explode("</br>",$eyeAll);
			$apAllTblContent = "";$apAllTblContent_V = "";
			if(count($arrAllExplode)>0 || count($arrPllExplode)>0){
				$apAllCnt = count($arrAllExplode);
				
				if(count($arrPllExplode) > count($arrAllExplode)) {
					$apAllCnt = count($arrPllExplode);		
				}
				$apAllTblContent .=	'<table style="border-collapse:collapse; " border="0">'; 		
				$apAllTblContent .=		'<tr>';
				$apAllTblContent .=			'<td colspan="2" style=" vertical-align:top; width: 225px;" ><strong>Assessment</strong></td>';
				$apAllTblContent .=			'<td style=" vertical-align:top; width: 80px;text-align:left;padding-left:2px;" ><strong>Site</strong></td>';
				$apAllTblContent .=			'<td colspan="2" style=" vertical-align:top; width: 225px;" ><strong>Plan</strong></td>';
				$apAllTblContent .=		'</tr>';
				//===========================A &amp; P Vertical==============================================//			
				$apAllTblContent_V.='<table style="width:99%;" cellpadding="0" cellspacing="0">';
				$apAllTblContent_V.='<tr>';
				$apAllTblContent_V.='<td colspan="2" style="vertical-align:top;width:98%; font-weight:bold;padding-bottom:10px;">';
				$apAllTblContent_V.='Assessment &amp; Plan</td>';
				//$apAllTblContent_V.='<td style="width:5%; padding-bottom:10px;font-weight:bold;">Site</td>';
				$apAllTblContent_V.='</tr>';
				//===========================A &amp; P Vertical==============================================//
				$m=0;
				$n=0;
				$RepArr = array("<",">","&lt;/br&gt;","&lt;br /&gt;","&lt;br/&gt;","&lt;br&gt;");
				$arr_rep= array("&lt;","&gt;","<br>","<br />","<br/>","<br>");
				$arr_site=array("OD"=>"Right eye","OS"=>"Left eye","OU"=>"Both eyes");
				for($w=0;$w<$apAllCnt;$w++) {
					$m++;
					$n++;
					$y=$m.'.';
					$z=$n.'.';
					if(!trim($arrAllExplode[$w])) { $y=""; }
					if(!trim($arrPllExplode[$w])) { $z=""; }
					$arrAllExplodeNew = str_ireplace($RepArr,$arr_rep,$arrAllExplode[$w]);
					$arrPllExplodeNew = str_ireplace($RepArr,$arr_rep,$arrPllExplode[$w]);		
					
					
					$arrPllExplodeNew = preg_replace('/[^`~!<>@$?a-zA-Z0-9_{}:; ,"#%\[\]\.\(\)%&-\/\\r\\n\\\\]/s','',$arrPllExplodeNew);
					
					
					$apAllTblContent .=	'<tr>';
					$apAllTblContent .=		'<td style=" vertical-align:top; width: 2px; padding-right:2px;" >'.$y.'</td>';
					$apAllTblContent .=		'<td style=" vertical-align:top; width: 225px;" >'.$arrAllExplodeNew.'</td>';
					$apAllTblContent .=		'<td style=" vertical-align:top; width: 80px;text-align:left;padding-left:2px;" >'.$arr_site[$arrEyeAllExplode[$w]].'</td>';
					$apAllTblContent .=		'<td style=" vertical-align:top; width: 2px; padding-right:2px;" >'.$z.'</td>';
					$apAllTblContent .=		'<td style=" vertical-align:top; width: 225px;" >'.nl2br($arrPllExplodeNew).'</td>';
					$apAllTblContent .=	'</tr>';
					
					//================================A &amp; P Vertical=====================================//
					if(trim($arrAllExplodeNew) || trim($arrPllExplodeNew)){
						$removedxcodes = explode('(',$arrAllExplodeNew);
						$removedxcodesfrmassesments='';
						for($ii=0;$ii<sizeof($removedxcodes)-1;$ii++)
						{
							$removedxcodesfrmassesments.=	($removedxcodesfrmassesments)?"(".$removedxcodes[$ii]:$removedxcodes[$ii];
						}
						$removedxcodesfrmassesments= str_ireplace(';',',',$removedxcodesfrmassesments); 
						$apAllTblContent_V.='<tr>';
						$apAllTblContent_V.='<td rowspan="2" class="bdrtop" style="vertical-align:top; width: 2px; padding-right:2px;" >'.$y.'</td>';
						$apAllTblContent_V.='<td style="vertical-align:top;width:98%;">'.str_ireplace(' ,',',',$removedxcodesfrmassesments).'</td>';
						//$apAllTblContent_V.='<td style="vertical-align:top;width:5%;">'.$arrEyeAllExplode[$w].'</td>';
						$apAllTblContent_V.='</tr>';
						$apAllTblContent_V.='<tr><td  style="vertical-align:top;width:95%;padding-bottom:10px;" >'.nl2br($arrPllExplodeNew).'</td>';
						$apAllTblContent_V.='</tr>';
					}
					//================================A &amp; P Vertical=====================================//
				}
				$apAllTblContent .=	'</table>';
				$apAllTblContent_V.='</table>';
			}
			$apAllTblContent = str_ireplace("Not Examined","",$apAllTblContent);
			$arrRep[] = $apAllTblContent;
			//pre($arrRep);
			
			$arrRep[] = $scribed_by_opr;	
			$arrRep[] =$apAllTblContent_V;
		}
		//print'<pre>'.print_r($arrRep);
		
		//Replace
		$str = $this->refineData($str, $this->kAP, $arrRep,$this->tAP,"");
		
		//Check if More Assessment and plan tags exits
		$ptrn = "/{ASSESSMENT \d+}/";
		if(preg_match($ptrn, $str)){
			$str = preg_replace($ptrn,"",$str);
		}
		$ptrn = "/{PLAN \d+}/";
		if(preg_match($ptrn, $str)){
			$str = preg_replace($ptrn,"",$str);
		}
		
		return $str;
	}

	//Get Aseessment & Plan OD/OS val
	private function getAPoDoSoUVal($str, $ptId, $formId){
		 $sql = "SELECT ".
			 "cap.assessment_1, cap.assessment_2, cap.assessment_3, cap.assessment_4, ".
			 "cap.plan_notes_1, cap.plan_notes_2, cap.plan_notes_3, cap.plan_notes_4, ".
			 "cap.follow_up, cap.follow_up_numeric_value, cap.followUpVistType, ".
			 "cap.doctorId, cap.sign_coords, cap.id,cap.assess_plan,cap.followup,  ".
			 "CONCAT_WS(', ',us.lname,us.fname) as scribed_by_opr  ".
			 
			 " FROM chart_assessment_plans cap  
			 LEFT JOIN users us ON (us.id = cap.scribedBy AND cap.scribedBy!='0')
			 WHERE form_id = '".$formId."' AND patient_id = '".$ptId."' ";
		$res = sqlQuery($sql);
		if($res !== false){
			
			$strXml = stripslashes($res["assess_plan"]);
			//--- Set Assess Plan Resolve NE Value FROM xml// Start					 
			//$arrApVals = $this->getVal_Str($strXml);
			$oChartAP  = new ChartAP($ptId, $formId);
			$arrApVals = $oChartAP->getVal();
			$arrAp = $arrApVals["data"]["ap"];
			//echo '<pre>';
			$arr_assment_od=$arr_assment_os=$plan_od=$plan_os="";
			$arr_assment_od_val=$arr_assment_os_val=$plan_od_val=$plan_os_val="";
			
			foreach($arrAp as $key=> $ap_val){
				if($ap_val['eye']=="OU" || $ap_val['eye']=="OD"){
					$arr_assment_od.=(strlen($ap_val['assessment'])>1)?"<tr><td style='width:99%;vertical-align:top;border-bottom:1px solid #ccc;' >".$ap_val['assessment']."</td></tr>":"";
					$plan_od.=(strlen($ap_val['plan'])>1)?"<tr><td style='width:99%;vertical-align:top;border-bottom:1px solid #ccc;' >".$ap_val['plan']."</td></tr>":"";
				}	
				if($ap_val['eye']=="OU" || $ap_val['eye']=="OS"){
					$arr_assment_os.=(strlen($ap_val['assessment'])>=1)?"<tr><td style='width:99%;vertical-align:top;border-bottom:1px solid #ccc;'>".$ap_val['assessment']."</td></tr>":"";
					
					$plan_os.=(strlen($ap_val['plan'])>1)?"<tr><td style='width:99%;vertical-align:top;border-bottom:1px solid #ccc;'>".$ap_val['plan']."</td></tr>":"";
				}
			}
			
			$arr_assment_od_val="<table cellpadding='0' cellspacing='0' style='width:99%;border-top:1px solid #ccc;border-left:1px solid #ccc;border-right:1px solid #ccc;'>".$arr_assment_od."</table>";
			$arr_assment_os_val="<table cellpadding='0' cellspacing='0' style='width:99%;border-top:1px solid #ccc;border-left:1px solid #ccc;border-right:1px solid #ccc;'>".$arr_assment_os."</table>";
			$plan_od_val="<table cellpadding='0' cellspacing='0' style='width:99%;border-top:1px solid #ccc;border-left:1px solid #ccc;border-right:1px solid #ccc;'>".$plan_od."</table>";
			$plan_os_val="<table cellpadding='0' cellspacing='0' style='width:99%;border-top:1px solid #ccc;border-left:1px solid #ccc;border-right:1px solid #ccc;'>".$plan_os."</table>";
		}
		$arr_return=array($arr_assment_od_val,$arr_assment_os_val,$plan_od_val,$plan_os_val);
		return $arr_return;
	}

	//Get Eom
	private function getEom($str, $ptId, $formId){
		$sumEom = $vpGlare = "";
		$sql = "SELECT ".
			 "sumEom, wnl, wnl_value ".
			 "FROM chart_eom WHERE form_id='".$formId."' AND patient_id ='".$ptId."'  AND purged='0'  ";
		$res = sqlQuery($sql);
		if($res !== false){
			$wnl =$res["wnl"];
			if($wnl==1){
				$eom = $res["wnl_value"];
			}else{
				$eom = $res["sumEom"];			
			}
		}
		
		//glare
		$sqlGlare = "SELECT glareOD, glareOS FROM surgical_tbl WHERE patient_id = '".$ptId."' ORDER BY `surgical_id` DESC ";
		$resGlare = sqlQuery($sqlGlare);
		if($resGlare !== false){
			$glareOD = $resGlare["glareOD"];
			$glareOS = $resGlare["glareOS"];

			$glareVal = '<table cellpadding="0" border="0" cellspacing="0">';			
			if($glareOD || $glareOS) {
				$glareVal .= '<tr>
								<td><b>OD : </b></td>
								<td>'.$glareOD.'</td>				
							 </tr>
							 <tr>
								<td><b>OS : </b></td>
								<td>'.$glareOS.'</td>				
							 </tr>
							 ';			
			}
			$glareVal .= '</table>';	
		}
		
		$arrRep = array($eom,$glareVal);
		//Replace
		$str = $this->refineData($str, $this->kEom, $arrRep, $this->tEom,"");
		return $str;
	}
	//Get CVF
	private function getCVF($str, $ptId, $formId){
		$sumCVFod = "";
		$sumCVFos = "";
		$sql = "SELECT ".
			 "summaryOd, summaryOs ".
			 "FROM chart_cvf WHERE formId='".$formId."' AND patientId ='".$ptId."'";
		$res = sqlQuery($sql);
		if($res !== false){
			$sumCVFod = $res["summaryOd"];			
			$sumCVFos = $res["summaryOs"];			
		}
		$arrRep = array($sumCVFod,$sumCVFos);
		//Replace
		$str = $this->refineData($str, $this->kCVF, $arrRep, $this->tCVF,"");
		return $str;
	}
	//Get Date
	private function getDate($str){
		$currentDate = wv_formatDate(date("Y-m-d"));
		$newCurrentDate = date("F d, Y");
		$arrRep = array($currentDate,$newCurrentDate);
		//Replace
		$str = $this->refineData($str, $this->kDate, $arrRep, $this->tDate,"");
		return $str;
	}
	//Get Pinhole (Given MR or latest)
	private function getPinhole($str, $ptId, $formId){
		$givenMrValue  = $odSpherical = $odCylinder = $odAxis = $odAxisTxt = $odAdd = $odAddTxt = $osSpherical = $osCylinder = $osAxis = $osAxisTxt = $osAdd = $osAddTxt = "";			
		
		$sql = "
				SELECT
				c0.status_elements,
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
				
				WHERE c1.ex_type='MR' AND c0.form_id = '".$formId."' AND c0.patient_id = '".$ptId."'
				AND c1.mr_none_given!=''
				ORDER BY c1.ex_number DESC
			";
		$rez = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($rez);$i++){
		
			$ex_num = $row["ex_number"];
			$sttsElm = $row["status_elements"];
			$givenMrValue = $row["mr_none_given"];
			if(strpos($givenMrValue, "MR ".$ex_num)!==false && strpos($sttsElm,"elem_mrNoneGiven".$ex_num."=1") !== false ){
			
				$odSpherical = $row["sph_r"];	
				$odCylinder = $row["cyl_r"];	
				$odAxis = $row["axs_r"];	
				$odAxisTxt = $row["txt_1_r"];	
				$odAdd = $row["ad_r"];	
				$odAddTxt = $row["txt_2_r"];
					
				$osSpherical = $row["sph_l"];	
				$osCylinder = $row["cyl_l"];	
				$osAxis = $row["axs_l"];	
				$osAxisTxt = $row["txt_1_l"];	
				$osAdd = $row["ad_l"];	
				$osAddTxt = $row["txt_2_l"];	
				$uiControlStstus = $sttsElm;
				
				$odSphericalVal = $odCylinderVal = $odAxisVal = $odAxisTxtVal = $odAddVal = $odAddTxtVal = "";
				$osSphericalVal = $osCylinderVal = $osAxisVal = $osAxisTxtVal = $osAddVal = $osAddTxtVal = "";
				
				$odSphericalVal = (trim($odSpherical)) ? "<b>S</b>"." ".$odSpherical." " : "";
				$odCylinderVal  = (trim($odCylinder)) ? "<b>C</b>"." ".$odCylinder." " : "";
				$odAxisVal  = (trim($odAxis)) ? "<b>A</b>"." ".$odAxis." " : "";
				$odAxisTxtVal  = ($odAxisTxt != "20/" && trim($odAxisTxt)) ? $odAxisTxt." " : "";
				$odAddVal  = ($odAdd !="+" && trim($odAdd) !="") ? "<b>Add</b>"." ".$odAdd." " : "";
				$odAddTxtVal  = ($odAddTxt != "20/" && trim($odAddTxt)) ? $odAddTxt." " : "";
				
				$osSphericalVal = (trim($osSpherical)) ? "<b>S</b>"." ".$osSpherical." " : "";
				$osCylinderVal  = (trim($osCylinder)) ? "<b>C</b>"." ".$osCylinder." " : "";
				$osAxisVal  = (trim($osAxis)) ? "<b>A</b>"." ".$osAxis." " : "";
				$osAxisTxtVal  = ($osAxisTxt != "20/" && trim($osAxisTxt)) ? $osAxisTxt." " : "";
				$osAddVal  = ($osAdd !="+" && trim($osAdd) !="") ? "<b>Add</b>"." ".$osAdd." " : "";
				$osAddTxtVal  = ($osAddTxt != "20/" && trim($osAddTxt)) ? $osAddTxt." " : "";
				if(trim($odSphericalVal) || trim($odCylinderVal) || trim($odAxisVal) || trim($odAxisTxtVal) || trim($odAddVal) || trim($odAddTxtVal)){
					$pinholeOD = $odSphericalVal.$odCylinderVal."<br/>".$odAxisVal.$odAxisTxtVal."<br/>".$odAddVal.$odAddTxtVal;
				}
				if(trim($osSphericalVal) || trim($osCylinderVal) || trim($osAxisVal) || trim($osAxisTxtVal) || trim($osAddVal) || trim($osAddTxtVal)){				
					$pinholeOS = $osSphericalVal.$osCylinderVal."<br/>".$osAxisVal.$osAxisTxtVal."<br/>".$osAddVal.$osAddTxtVal;
					
				}
				$perform = false;
				
				$indx1=$indx2="";
				if($ex_num>1){
					$indx1="Other";
					if($ex_num>2){
						$indx2="_".$ex_num;
					}
				}
				
				//Check for background for mr 3
				if( ((strpos($uiControlStstus, "elem_visMr".$indx1."OdS".$indx2."=0") == true)) &&
					((strpos($uiControlStstus, "elem_visMr".$indx1."OdC".$indx2."=0") == true)) &&  
					((strpos($uiControlStstus, "elem_visMr".$indx1."OdA".$indx2."=0") == true)) &&  
					((strpos($uiControlStstus, "elem_visMr".$indx1."OdTxt1".$indx2."=0") == true)) &&  
					((strpos($uiControlStstus, "elem_visMr".$indx1."OdAdd".$indx2."=0") == true)) &&
					((strpos($uiControlStstus, "elem_visMr".$indx1."OdTxt2".$indx2."=0") == true)) && 
					
					((strpos($uiControlStstus, "elem_visMr".$indx1."OsS".$indx2."=0") == true)) &&
					((strpos($uiControlStstus, "elem_visMr".$indx1."OsC".$indx2."=0") == true)) &&  
					((strpos($uiControlStstus, "elem_visMr".$indx1."OsA".$indx2."=0") == true)) &&  
					((strpos($uiControlStstus, "elem_visMr".$indx1."OsTxt1".$indx2."=0") == true)) &&  
					((strpos($uiControlStstus, "elem_visMr".$indx1."OsAdd".$indx2."=0") == true)) &&
					((strpos($uiControlStstus, "elem_visMr".$indx1."OsTxt2".$indx2."=0") == true)) 
				  ){
					$perform = false;
				}else{
					$perform = true;	
				}
			
				if($perform == true){					
					//Replace Array
					$arrRep = array($pinholeOD,$pinholeOS);
				}
			}
		}
		
		$str = $this->refineData($str, $this->kPinhole, $arrRep, $this->tPinhole,"");
		return $str;		
	}
	
	//get assesment & plan for ptdocs
	public function getPtDocsAssPlan($str="",$ptId=0,$formId=0){
		//if(!empty($str) && !empty($ptId) && !empty($formId)){	
			if($this->isKeyExist($str, $this->kAP)){
				$str = $this->getAP($str, $ptId, $formId);
			}
		//}
		return $str;
	}
	
	//Get assessment & plan od/os/ou vals
	public function getPtDocsAssPlanODOSOUVals($str="",$ptId=0,$formId=0){
		//if(!empty($str) && !empty($ptId) && !empty($formId)){	
			if($this->isKeyExist($str, $this->kAPODOSOU)){
				$str = $this->getAPoDoSoUVal($str, $ptId, $formId);
			}
		//}
		return $str;
	}
	
	//Get data to parse
	public function getDataParsed($str="",$ptId=0,$formId=0,$patientRefTo="",$addresseRefToId="",$cc1RefToId="",$cc2RefToId="",$cc3RefToId="",$sign_path_remote="",$document_from="",$ptDocReport="",$insCaseTypeId="",$section=""){
	    if(empty($ptId) == false || empty($formId) == false)
		{
	        parent::__construct($ptId, $formId);
	    }
		if(!empty($str) && !empty($ptId))
		{
			//--------PTINFO------------------------------
			if($this->isKeyExist($str, $this->kPtInfo))
			{				
				$str = $this->getPtInfo($str, $ptId,$patientRefTo,$addresseRefToId,$cc1RefToId,$cc2RefToId,$cc3RefToId,$ptDocReport,$insCaseTypeId);
			}
			
			//--------GET VISION PRIMARY INSURANCE--------
			if($this->isKeyExist($str, $this->kVisPriIns))
			{				
				$str = $this->getVisionPriIns($str,$ptId,$type='primary');
			}
			
			//--------GET VISION SECONDARY INSURANCE------
			if($this->isKeyExist($str, $this->kVisSecIns))
			{				
				$str = $this->getVisionSecIns($str,$ptId,$type='secondary');
			}
			
			//--------GET PATIENT UPCOMING APPOINTMENT INFORMATION
			if($this->isKeyExist($str, $this->kApptInfo))
			{		
				$str = $this->__getApptInfo($str, $ptId);
			}
			
		}
		
		if(empty($formId) && ($_SESSION['finalize_id'])){ $formId=$_SESSION['finalize_id']; }
		
		if(!empty($str) && !empty($ptId) && !empty($formId))
		{	
		
			//Chart Left CC History
			if($this->isKeyExist($str, $this->kChartLeftCcHx))
			{		
				$str = $this->getChartLeftCcHx($str, $ptId, $formId,$sign_path_remote,$document_from);
			}
			
			//Vision
			if($this->isKeyExist($str, $this->kVision))
			{		
				$str = $this->getVision($str, $ptId, $formId);
			}
			
			//MR1, MR2, MR3
			if($this->isKeyExist($str, $this->kMr1Mr2Mr3))
			{		
				$str = $this->getMr1Mr2Mr3($str, $ptId, $formId);
			}
			
			//PC1, PC2, PC3
			if($this->isKeyExist($str, $this->kPc1Pc2Pc3))
			{		
				$str = $this->getPc1Pc2Pc3($str, $ptId, $formId);
			}
			
			//Pupil
			if($this->isKeyExist($str, $this->kPupil))
			{		
				$str = $this->getPupil($str, $ptId, $formId);
			}
			
			//Exter
			if($this->isKeyExist($str, $this->kExter))
			{		
				$str = $this->getExter($str, $ptId, $formId);
			}
			
			//LA
			if($this->isKeyExist($str, $this->kLa))
			{		
				$str = $this->getLa($str, $ptId, $formId);
			}
			//LA in Table Format
			if($this->isKeyExist($str, $this->kLna))
			{		
				$str = $this->getLnA($str, $ptId, $formId);
			}
			//IOP
			if($this->isKeyExist($str, $this->kIop))
			{		
				$str = $this->getIop($str, $ptId, $formId);
			}
			
			//Gonio
			if($this->isKeyExist($str, $this->kGonio))
			{		
				$str = $this->getGonio($str, $ptId, $formId);
			}
			
			//Sle
			if($this->isKeyExist($str, $this->kSle))
			{		
				$str = $this->getSle($str, $ptId, $formId);
			}
			//SLE in Table Format
			if($this->isKeyExist($str, $this->kSli))
			{		
				$str = $this->getSli($str, $ptId, $formId);
			}
			
			//Fundus
			if($this->isKeyExist($str, $this->kFundus))
			{		
				$str = $this->getFundus($str, $ptId, $formId);
			}
			//Fundus in Table Format
			if($this->isKeyExist($str, $this->kFundusAll))
			{		
				$str = $this->getFundusAll($str, $ptId, $formId);
			}
			
			//Eom
			if($this->isKeyExist($str, $this->kEom))
			{		
				$str = $this->getEom($str, $ptId, $formId);
			}
			//CVF
			if($this->isKeyExist($str, $this->kCVF))
			{		
				$str = $this->getCVF($str, $ptId, $formId);
			}
			
			//Assess and plan
			if($this->isKeyExist($str, $this->kAP))
			{
				$str = $this->getAP($str, $ptId, $formId);
			}
			//Assess and plan Od/os/ou vals
			if($this->isKeyExist($str, $this->kAPODOSOUs))
			{
				$str = $this->getAPoDoSoUVal($str, $ptId, $formId);
			}
		
			//Extra variable
			if($this->isKeyExist($str, $this->kDate))
			{
				//echo 'hlo';
				$str = $this->getDate($str);
			}
			//Pinhole
			if($this->isKeyExist($str, $this->kPinhole))
			{
				$str = $this->getPinhole($str, $ptId, $formId);
			}
			//Text Box
			if($this->isKeyExist($str, $this->kTextBox))
			{
				$str = $this->getTextBox($str);
			}
			
			
			//START Medical History
			if($this->isKeyExist($str, $this->kMedHx))
			{		
				$str = $this->getMedHx($str, $ptId, $formId);
			}
			//END Medical History
			//====GET PACHY DATA==============
			if($this->isKeyExist($str, $this->kPachy))
			{		
				$str = $this->getPachy($str, $ptId, $formId);
			}
			//====END PACHY DATA==============
			
			//====GET CPT MODIFIERS DATA======
			if($this->isKeyExist($str, $this->kCptMod))
			{		
				$str = $this->__getCptModifiers($str, $ptId, $formId);
			}
			
			//===GET PT ACTUAL ARRIVAL TIME===
			if($this->isKeyExist($str, $this->kArrTym))
			{		
				$str = $this->__pt_actual_arrival_time($str, $ptId, $formId, $section);
			}
		}
		
		$str = $this->getGroupInfo($str);
		
		return $str;
	}
	
	//START get MedHx
	private function getMedHx($str, $ptId, $formId) {
		$oMedHx = new MedHx($ptId);
		$arrReturn = array();
		if($ptId != 0){
			//ocular history
			$ocularHist 		= $this->getOcularHist($ptId);	
			
			//latest vital sign
			$latestVitalSign 	= $this->getLatestVitalSign($ptId);	
			
			//ocular and systemic medication
			$ocularMed 			= $this->getMedList($ptId,4);	
			$systemicMed 		= $this->getMedList($ptId,1);	
			$ocularMedWithLot	= $this->getMedList($ptId,4,1);	
			$systemicMedWithLot = $this->getMedList($ptId,1,1);	
			$allergyList 		= $this->getMedList($ptId,7);
			$medProbList 		= $this->getMedProbList($ptId);
			
			//MedHx
			$medHx 				= $this->getMedHxSummary($ptId);
			
			
			 
			$diabMedArr			= $oMedHx->getDiabMedInfo($ptId);
			$Diabetes 			= $diabMedArr[0];
			
			$oclrArr			= $oMedHx->getOcularEyeInfo($ptId);
			$flashYesNo 		= $oclrArr[3];
			$floatersYesNo 		= $oclrArr[2];
			
			$socialHistArr 		= $this->getSocialHist($ptId);
			$smokeSmry 			= $socialHistArr[0];
			$fmlyHxSmokSmry		= $socialHistArr[1];
			
			$empId 					= $_SESSION['authId'];
			$genHealth 				= $this->getMedHxSummary($ptId,"General Health");
			$ocuOther 				= $this->getMedHxSummary($ptId,"","Ocular_Other"); //OCULAR OTHER VARIABLE REPLACEMENT
			//---REVIEW OF SYSTEM VARIABLES WORKS STARTS HERE---
			$ros_summary 			= $this->getMedHxSummary($ptId,"","","ROS_SUMMARY");
			$ros_detail 			= $this->getMedHxSummary($ptId,"","","ROS_DETAIL");
			$ros_allergicimmunologic = $this->getMedHxSummary($ptId,"","","ROS_ALLERGICIMMUNOLOGIC");
			$ros_cardiovascular 	= $this->getMedHxSummary($ptId,"","","ROS_CARDIOVASCULAR");
			$ros_constitutional 	= $this->getMedHxSummary($ptId,"","","ROS_CONSTITUTIONAL");
			$ros_earnosemouththroat = $this->getMedHxSummary($ptId,"","","ROS_EARNOSEMOUTHTHROAT");
			$ros_endocrine 			= $this->getMedHxSummary($ptId,"","","ROS_ENDOCRINE");
			$ros_eyes 				= $this->getMedHxSummary($ptId,"","","ROS_EYES");
			$ros_gastrointestinal   = $this->getMedHxSummary($ptId,"","","ROS_GASTROINTESTINAL");
			$ros_genitourinary		= $this->getMedHxSummary($ptId,"","","ROS_GENITOURINARY");
			$ros_hemotologiclymphatic= $this->getMedHxSummary($ptId,"","","ROS_HEMOTOLOGICLYMPHATIC");
			$ros_integumentary 		= $this->getMedHxSummary($ptId,"","","ROS_INTEGUMENTARY");
			$ros_musculoskeletal	= $this->getMedHxSummary($ptId,"","","ROS_MUSCULOSKELETAL");
			$ros_neurological 		= $this->getMedHxSummary($ptId,"","","ROS_NEUROLOGICAL");
			$ros_psychiatric 		= $this->getMedHxSummary($ptId,"","","ROS_PSYCHIATRIC");
			$ros_respiratory		= $this->getMedHxSummary($ptId,"","","ROS_RESPIRATORY");
			
			$arrMedHx = array($ocularHist,$latestVitalSign,$ocularMed,$systemicMed,$allergyList,$medProbList,$medHx,$Diabetes,$flashYesNo,$floatersYesNo,$smokeSmry,$fmlyHxSmokSmry,$empId,$genHealth,$ocuOther,$ros_summary,$ros_detail,$ros_allergicimmunologic,$ros_cardiovascular,$ros_constitutional,$ros_earnosemouththroat,$ros_endocrine,$ros_eyes,$ros_gastrointestinal,$ros_genitourinary,$ros_hemotologiclymphatic,$ros_integumentary,$ros_musculoskeletal,$ros_neurological,$ros_psychiatric,$ros_respiratory,$ocularMedWithLot,$systemicMedWithLot);
		}
			
		//Replace
		$str = $this->refineData($str, $this->kMedHx, $arrMedHx, $this->tMedHx,"");
		return $str;
	}
	
	
		
	private function getSocialHist($ptId) {
		$SmokeSumray = "";
		$familyHxSmokeSumray = "";
		$qrySmoking = "select smoking_status, source_of_smoke, source_of_smoke_other, smoke_perday, alcohal, source_of_alcohal_other, list_drugs, otherSocial,family_smoke,smokers_in_relatives,smoke_description from social_history where patient_id = '$ptId'";
		$rsSmoking = imw_query($qrySmoking);
		if($rsSmoking){
			$rowSmoking = imw_fetch_array($rsSmoking);
			$SmokeSumray .= '<table cellpadding="0" border="0" cellspacing="0">';
			if((trim($rowSmoking["smoking_status"]) != "Never smoker" && trim($rowSmoking["smoking_status"]) != "") || (trim($rowSmoking["smoking_status"]) != "Never Smoked" && trim($rowSmoking["smoking_status"]) != "")){
				$smoke_source = $rowSmoking["source_of_smoke"];
				if($smoke_source == "Other"){
					$smoke_source = $rowSmoking["source_of_smoke_other"]; 
					/*
					if(strlen($smoke_source)>10){
						$smoke_source = substr($smoke_source,0,10)."...";
					}*/
				}
				
				$smoke_per_day = $rowSmoking["smoke_perday"];
				if($smoke_per_day=="" || empty($smoke_per_day) || $smoke_per_day==NULL){
					$smoke_per_day = "";
				}
				$smoke_str = (($rowSmoking["smoking_status"]) ? $rowSmoking["smoking_status"] : "").(($smoke_source) ? "&nbsp;of&nbsp;".$smoke_source : "")."&nbsp;".(($smoke_per_day) ? $smoke_per_day."&nbsp;Per Day" : "");
				$SmokeSumray .= "<tr><td>Yes ".(($smoke_str) ? "&nbsp;&nbsp;".$smoke_str : '')."</td></tr>";
			}
			else{
				$SmokeSumray .= "<tr><td>&nbsp;&nbsp;Never Smoked</td></tr>";
			}
			$SmokeSumray .= '</table>';
			
			$familyHxSmokeSumray .= '<table cellpadding="0" border="0" cellspacing="0">';
			if((trim($rowSmoking["family_smoke"]) == "1")) {
				$familyHxSmokeSumray .= "<tr><td>".trim("Yes <b>".$rowSmoking["smokers_in_relatives"]."</b> ".$rowSmoking["smoke_description"])."</td></tr>";
			}else {
				$familyHxSmokeSumray .= "<tr><td>No</td></tr>";
				
			}
			$familyHxSmokeSumray .= '</table>';
		}
		return $socialHxArr = array($SmokeSumray,$familyHxSmokeSumray);
	}
	
	//get medhx summary	
	private function getMedHxSummary($ptId,$defaultData = NULL,$ocuOther = NULL,$rosData = NULL) {
		$ocData = $ghData = $MSAData = $immData = $medHxData = "";
		$arrPtOcular = $arrPtInfo = array();
		$history = "";
		$ocularOther="";
		$max_chars = 50;
		$max_showc = 50;
		//Ocular Start
		$ocData = "<tr><td colspan='2' style='width:700px;font-weight:bold;background:#CCC; height:20px;'>Ocular</td></tr>";
		$arrPtOcular = getPtOcularInfo($ptId);
		$history = $arrPtOcular["eye_history"];		
		$val = (strlen($history)>$max_chars)?substr($history,0,$max_showc)."...":$history;
		//$historyEye = "<tr><td colspan='2' style=\"border-bottom:1px solid #CCC;padding:3px\">".((trim($val) != "") ? "<b>Patient Wears:&nbsp;</b>".$val : '')."</td></tr>";		
		//$ocData .= $historyEye;
		$ocData.= "<tr><td style=\"width:350px;border-bottom:1px solid #CCC;padding-left:5px\"><b>Patient:</b></td>
						<td style=\"width:350px;border-bottom:1px solid #CCC;padding-left:5px\"><b>Blood Relative:</b></td></tr>";	
		if(count($arrPtOcular["eye_problem"]) > 0 || strlen($arrPtOcular["eye_problems_other"])>0){	
			$problem = "";$ck_var=false;
			$problem.="<table style=\"width:360px;border-right:1px solid #CCC;\" cellpadding='0' cellspacing='0'>";
			foreach($arrPtOcular["eye_problem"] as $key => $val){
				if(count($val) > 0){$ck_var=true;
					$val = (strlen($val)>$max_chars)?substr($val,0,$max_showc)."...":$val;
					$problem .= "<tr><td style=\"width:360px;border-bottom:1px solid #CCC; padding-left:5px;\">".((trim($val) != "") ? $val : '')."</td></tr>";				
				}
			}
			
			//-------eye problem other--------------
			/*$eye_problem_other = $arrPtOcular["eye_problems_other"];
			if($eye_problem_other!="" || !empty($eye_problem_other) || $eye_problem_other!=NULL){
				if(strlen($eye_problem_other)>20)
				{
					$eye_problem_other = substr($eye_problem_other,0,20)."...";
				}
				//$problem.="<tr><td id=\"tdOcEyeProbOther\" class=\"text_10\">&nbsp;&nbsp;".$eye_problem_other."</td></tr>";
				$problem.="<tr><td
				 style=\"border-bottom:1px solid #CCC; padding-left:5px;\">".((trim($eye_problem_other) != "") ? $eye_problem_other : '')."</td></tr>";			
			}*/
			/*$any_condition_other = $arrPtOcular["OtherDesc"];
			if($any_condition_other != "" || !empty($any_condition_other) || $any_condition_other != NULL){
				if(strlen($any_condition_other)>20){
					//$any_condition_other = substr($any_condition_other,0,20)."...";
				}$ck_var=true;
				$problem.="<tr><td style=\"border-bottom:1px solid #CCC; padding-left:5px;\">".((trim($any_condition_other) != "") ? stripslashes($any_condition_other) : '')."</td></tr>";			
			}*/
			//-------eye problem other--------------
			$problem.="</table>";
		}
		if($ck_var==false){$problem="&nbsp;None";}
		$chk_val_rel=false;
		$chk_val_bld_rel=false;
		if( count($arrPtOcular["you_rel"]) > 0 || strlen($arrPtOcular["OtherDesc"])>0){
			$condition = "<table style=\"width:310px;\" cellpadding=\"0\" cellspacing=\"0\">";
			foreach($arrPtOcular["you_rel"] as $key => $val){
				if(count($val) > 0){$chk_val_rel=true;
					$val = (strlen($val)>$max_chars)?substr($val,0,$max_showc)."...":$val;
					$condition .= "<tr><td style=\"border-bottom:1px solid #CCC; width:355px; padding-left:5px;\">".((trim($val) != "") ? stripslashes($val) : '')."</td></tr>";
				}
			}	
			$any_condition_other = $arrPtOcular["OtherDesc"];
			if($any_condition_other != "" || !empty($any_condition_other) || $any_condition_other != NULL){
				if(strlen($any_condition_other)>20){
					//$any_condition_other = substr($any_condition_other,0,20)."...";
				}$chk_val_rel=true;
				$condition.="<tr><td style=\"width:350px;border-bottom:1px solid #CCC; padding-left:5px;\">".((trim($any_condition_other) != "") ? stripslashes($any_condition_other) : '')."</td></tr>";			
				if(trim($any_condition_other)!=""){ $ocularOther = $any_condition_other; }
			}	
			
			//-------Any Condition other--------------
			/*$any_condition_other = $arrPtOcular["OtherDesc"];
			if($any_condition_other != "" || !empty($any_condition_other) || $any_condition_other != NULL){
				if(strlen($any_condition_other)>20){
					$any_condition_other = substr($any_condition_other,0,20)."...";
				}
				$condition.="<tr><td style=\"border-bottom:1px solid #CCC; padding-left:5px;\">".((trim($any_condition_other) != "") ? stripslashes($any_condition_other) : '')."</td></tr>";			
			}	*/	
			//------- Any Condition other --------------		
		
		$condition .="</table>";
		}
		
		//=========MED HX BLOOD RELATIVE GET DATA WITH OTHER DATA=============================
		if( count($arrPtOcular["blood_rel"]) > 0 || strlen($arrPtOcular["relOtherDesc"])>0){
			$any_condition_rel = $arrPtOcular["relOtherDesc"];
			$condition_rel = "<table style=\"width:310px;\" cellpadding=\"0\" cellspacing=\"0\">";
			foreach($arrPtOcular["blood_rel"] as $key => $val){
				if(count($val) > 0){$chk_val_bld_rel=true;
					$val = (strlen($val)>$max_chars)?substr($val,0,$max_showc)."...":$val;
					$condition_rel .= "<tr><td style=\"border-bottom:1px solid #CCC; width:310px; padding-left:5px;\">".((trim($val) != "") ? stripslashes($val) : '')."</td></tr>";
				}
			}
			if(strlen($arrPtOcular["relOtherDesc"])>0){
				$chk_val_bld_rel=true;
				$condition_rel .= "<tr><td style=\"border-bottom:1px solid #CCC; width:310px; padding-left:5px;\">".((trim($any_condition_rel) != "") ? stripslashes($any_condition_rel) : '')."</td></tr>";	
			}
		$condition_rel .="</table>";
		}
		
		//======================================================================================
		
		if($chk_val_rel==false){$condition="&nbsp;None";}
		if($chk_val_bld_rel==false){$condition_rel="&nbsp;None";}
		$ocData .= "<tr><td style=\"vertical-align:top;\">".$condition."</td><td style=\"vertical-align:top;border-left:1px solid #CCC;\">".$condition_rel."</td></tr>";
		//Ocular End
		
		//General Health Start
		$ghData_gen ="<tr> <td style=\"border-bottom:1px solid #CCC;border-left:1px solid #CCC;padding-left:5px;padding-top:5px;\"><b>Blood&nbsp;Sugar:&nbsp;</b>";
		$qryGetBS = "select id, sugar_value, date_format(creation_date,'".get_sql_date_format()."') as createdDate
							from patient_blood_sugar where patient_id = '$ptId' ORDER BY creation_date DESC LIMIT 1;";
		$rsGetBS = imw_query($qryGetBS);
		if($rsGetBS){
			$rowGetBS = imw_fetch_array($rsGetBS);
			$blood_sugar_date = $rowGetBS["createdDate"];
			$blood_sugar_value = $rowGetBS["sugar_value"];
			$ghData_gen .= "".((trim($blood_sugar_date) != "" && trim($blood_sugar_value) != "") ? $blood_sugar_date." - ".$blood_sugar_value." mg/dl" : '')."";
			
		}
		
		$ghData_gen .= "</td><td style=\"border-bottom:1px solid #CCC;padding-left:5px;padding-top:5px;\"><b>Cholesterol:&nbsp;</b>";
		$qryGetCH = "Select id, cholesterol_total, cholesterol_triglycerides, 
				cholesterol_LDL, cholesterol_HDL, date_format(creation_date,'".get_sql_date_format()."') as date
				from patient_cholesterol where patient_id = '$ptId' order by creation_date desc LIMIT 1;";
		$rsGetCH = imw_query($qryGetCH);
		if($rsGetCH){
			$rowGetCH = imw_fetch_array($rsGetCH);
			$cholesterol_date = $rowGetCH["date"];
			$cholesterol_total = $rowGetCH["cholesterol_total"];
			$cholesterol_tri = $rowGetCH["cholesterol_triglycerides"];
			$cholesterol_ldl = $rowGetCH["cholesterol_LDL"];
			$cholesterol_hdl = $rowGetCH["cholesterol_HDL"];
			//$ghData .= "<tr><td><b>Cholesterol</b></td></tr>";
			$ghData_gen .= "".((trim($cholesterol_date) != "" || trim($cholesterol_total) != "" || trim($cholesterol_tri) != "" || trim($cholesterol_ldl) != "" || trim($cholesterol_hdl) != "") ? $cholesterol_date." ".$cholesterol_total." ".$cholesterol_tri." ".$cholesterol_ldl." ".$cholesterol_hdl : '')."";
			
		}
		$ghData_gen .= "</td></tr>";
		
		//
		$oMedHx = new MedHx($ptId);
		$ghData .= "<tr><td style=\"border-bottom:1px solid #CCC; font-weight:bold; padding-left:5px;padding-top:3px;background:#CCC;\">General Health</td>
						<td style=\"border-bottom:1px solid #CCC;  font-weight:bold; padding-left:5px;padding-top:3px;background:#CCC;\">Review of Systems</td></tr>";
		$arrPtInfo = $oMedHx->getPtGenHealthInfo($ptId);
		$arrShowGH = array_merge((array)$arrPtInfo["AnyCond"]["You"],(array)$arrPtInfo["AnyCond"]["Relatives"]);
		//	Removes duplicate values from an array
		$arrShowGH = array_unique($arrShowGH);	
		sort($arrShowGH);
		$strMedicalCond = "";$chk_gen_rev=false;	
		$genHealth = "";
		if(count($arrShowGH) > 0 ){
			
			foreach($arrShowGH as $key => $val){$chk_gen_rev=true;
				$val = (strlen($val)>$max_chars)?substr($val,0,$max_showc)."...":$val;
				if($val=="Diabetes" && trim($arrPtInfo["diabetes_values"])!=""){
					$diabetes_values = $arrPtInfo["diabetes_values"];
					if(strlen($diabetes_values)>9){
						//$diabetes_values = substr($diabetes_values,0,9)."...";
					}$chk_gen_rev=true;
					$val.= " - ".$diabetes_values;
				}
				$strMedicalCond .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"border-bottom:1px solid #CCC; width:355px; padding-left:5px;\">".$val."</td></tr></table>";
				$genHealth .= "<table cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%\"><tr><td style=\"width:100%; padding-left:5px;\">".$val."</td></tr></table>";
			}
		}
		//$ghData .= "<tr><td>".$strMedicalCond."</td></tr>";
		//ROS
		$strROS = ""; $strROS_neg=""; $strROS_summary=""; $strROS_detail="";  $chk_rev=false;
		if(count($arrPtInfo["ROS"]) > 0 ){
			foreach($arrPtInfo["ROS"] as $key => $val){			
				if(count($val) > 0){
					//$key = (strlen($key)>$max_chars)?substr($key,0,$max_showc)."...":$key;
					$rev_show_val="";
					if($key != "negChkBx"){$chk_rev=true;
						$rev_show_val = trim(implode(", ",$val));
						$strROS .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"border-bottom:1px solid #CCC;width:350px; padding-left:5px;\"><b>".$key."</b>  ".$rev_show_val."</td></tr></table>";
						
						$strROS_summary .= "<b>".$key."</b><br> ";
						//print_r($key).'<br>';
						//---DATA SET FOR REVIEW OF SYSTEMS VARIABLES HERE, IF FINDINGS ADDED FOR SECTIONS---
						if($key == "Allergic/Immunologic"){ $allergicimmunologic = "<b>".$key."</b>  "; $allergicimmunologic_finding = $rev_show_val; }
						if($key == "Cardiovascular"){	$cardiovascular = "<b>".$key."</b>  ";		$cardiovascular_finding = $rev_show_val; }
						if($key == "Constitutional"){	$constitutional = "<b>".$key."</b>  ";		$constitutional_finding = $rev_show_val; }
						if($key == "Ear, Nose, Mouth & Throat"){ $earnosemouththroat = "<b>".$key."</b>  "; $earnosemouththroat_finding = $rev_show_val; }
						if($key == "Endocrine"){	$endocrine = "<b>".$key."</b>  "; 		$endocrine_finding = $rev_show_val; }
						if($key == "Eyes"){			$eyes = "<b>".$key."</b>  ";			$eyes_finding = $rev_show_val; }
						if($key == "Gastrointestinal"){	$gastrointestinal = "<b>".$key."</b>  ";	$gastrointestinal_finding = $rev_show_val; }
						if($key == "Genitourinary"){ 	$genitourinary = "<b>".$key."</b>  ";		$genitourinary_finding = $rev_show_val; }
						if($key == "Hemotologic/Lymphatic"){ $hemotologiclymphatic = "<b>".$key."</b>  "; $hemotologiclymphatic_finding = $rev_show_val; }
						if($key == "Integumentary"){	$integumentary = "<b>".$key."</b>  ";		$integumentary_finding = $rev_show_val; }
						if($key == "Musculoskeletal"){ 	$musculoskeletal = "<b>".$key."</b>  ";		$musculoskeletal_finding = $rev_show_val; }
						if($key == "Neurological"){	$neurological = "<b>".$key."</b>  "; 	$neurological_finding = $rev_show_val; }
						if($key == "Psychiatry"){ 	$psychiatric = "<b>".$key."</b>  "; 	$psychiatric_finding = $rev_show_val; }
						if($key == "Respiratory"){ 	$respiratory = "<b>".$key."</b>  "; 	$respiratory_finding = $rev_show_val; }
					}
					elseif($key == "negChkBx")
					{
						foreach($arrPtInfo["ROS"]["negChkBx"] as $negChkBxKey => $negChkBxVal)
						{
							//print_r($negChkBxVal).'<br>';
							$chk_rev=true;
							//$negChkBxVal = (strlen($negChkBxVal)>$max_chars)?substr($negChkBxVal,0,$max_showc)."...":$negChkBxVal;
							$strROS_neg .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"border-bottom:1px solid #CCC;width:350px; padding-left:5px;\">Negative&nbsp;".$negChkBxVal."</td></tr></table>";						
							
							//---DATA SET FOR REVIEW OF SYSTEMS VARIABLES HERE, IF NEGATIVE OPTION IS CHECKED---
							if($negChkBxVal == "Allergic/Immunologic"){ $allergicimmunologic = $negChkBxVal;   $allergicimmunologic_finding  = "-ve"; }
							if($negChkBxVal == "Cardiovascular"){	$cardiovascular = $negChkBxVal; $cardiovascular_finding = "-ve";    }
							if($negChkBxVal == "Constitutional"){	$constitutional = $negChkBxVal;   $constitutional_finding = "-ve"; }
							if($negChkBxVal == "Ear, Nose, Mouth & Throat"){ $earnosemouththroat = $negChkBxVal; $earnosemouththroat_finding= "-ve";}
							if($negChkBxVal == "Endocrine"){	$endocrine = $negChkBxVal;    $endocrine_finding = "-ve"; }
						 	if($negChkBxVal == "Eyes"){	$eyes = $negChkBxVal; $eyes_finding = "-ve";	 }
							if($negChkBxVal == "Gastrointestinal"){	$gastrointestinal = $negChkBxVal; $gastrointestinal_finding = "-ve";}
							if($negChkBxVal == "Genitourinary"){ 	$genitourinary = $negChkBxVal;  $genitourinary_finding  = "-ve";}
							if($negChkBxVal == "Hemotologic/Lymphatic"){ $hemotologiclymphatic = $negChkBxVal; $hemotologiclymphatic_finding = "-ve";}
							if($negChkBxVal == "Integumentary"){	$integumentary = $negChkBxVal;  $integumentary_finding = "-ve"; }
							if($negChkBxVal == "Musculoskeletal"){ 	$musculoskeletal = $negChkBxVal;  $musculoskeletal_finding = "-ve";}
							if($negChkBxVal == "Neurological"){	$neurological = $negChkBxVal; $neurological_finding = "-ve"; }
							if($negChkBxVal == "Psychiatry"){ 	$psychiatric = $negChkBxVal;  $psychiatric_finding = "-ve"; }
							if($negChkBxVal == "Respiratory"){ 	$respiratory = $negChkBxVal;  $respiratory_finding = "-ve";}
						}
					}
				}
			}
			
			if(!empty($strROS))
			{
				if(!empty($strROS_neg))
				{
					//---USE FOR INDEPENDENT VARIABLES OF REVIEW SYSTEMS---
					$strROS_detail = $strROS; 
					//---END---
					$strROS .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"border-bottom:1px solid #CCC;width:350px; padding-left:5px;\">All recorded systems are negative except as noted above.</td></tr></table>";
				}
			}
			else if(!empty($strROS_neg))
			{
				$strROS = $strROS_neg;
			}
		}
		
		//---MESSAGES, IF NO FINDINGS OR WITH NEGATIVE FINDINGS---
		$negative_findings = "This is noted with Negative finding.<br>";
		$no_findings = "There are no findings (Positive/Negative) noted.<br>";
		
		//---ALLERGIC/IMMUNOLOGIC FINDINGS---
		if(!empty($allergicimmunologic_finding) && $allergicimmunologic_finding== '-ve')
		{ 
			$allergicimmunologic = $negative_findings;
		}
		else if (!empty($allergicimmunologic) && $allergicimmunologic_finding!= '-ve')
		{
			$allergicimmunologic = $allergicimmunologic_finding; 
		}
		else if (empty($allergicimmunologic) && empty($allergicimmunologic_finding))
		{
			$allergicimmunologic = $no_findings;
		}
		
		//---CARDIOVASCULAR FINDINGS---
		if(!empty($cardiovascular_finding) && $cardiovascular_finding== '-ve')
		{ 
			$cardiovascular = $negative_findings;
		}
		else if (!empty($cardiovascular) && $cardiovascular_finding!= '-ve')
		{
			$cardiovascular = $cardiovascular_finding; 
		}
		else if (empty($cardiovascular) && empty($cardiovascular_finding))
		{
			$cardiovascular = $no_findings;
		}
		
		//---CONSTITUTIONAL FINDINGS---
		if(!empty($constitutional_finding) && $constitutional_finding== '-ve')
		{ 
			$constitutional = $negative_findings;
		}
		else if (!empty($constitutional) && $constitutional_finding!= '-ve')
		{
			$constitutional = $constitutional_finding; 
		}
		else if (empty($constitutional) && empty($constitutional_finding))
		{
			$constitutional = $no_findings;
		}
		
		//---EARNOSEMOUTHTHROAT FINDINGS---
		if(!empty($earnosemouththroat_finding) && $earnosemouththroat_finding== '-ve')
		{ 
			$earnosemouththroat = $negative_findings;
		}
		else if (!empty($earnosemouththroat) && $earnosemouththroat_finding!= '-ve')
		{
			$earnosemouththroat = $earnosemouththroat_finding; 
		}
		else if (empty($earnosemouththroat) && empty($earnosemouththroat_finding))
		{
			$earnosemouththroat = $no_findings;
		}
		
		//---ENDOCRINE FINDINGS---
		if(!empty($endocrine_finding) && $endocrine_finding== '-ve')
		{ 
			$endocrine = $negative_findings;
		}
		else if (!empty($endocrine) && $endocrine_finding!= '-ve')
		{
			$endocrine = $endocrine_finding; 
		}
		else if (empty($endocrine) && empty($endocrine_finding))
		{
			$endocrine = $no_findings;
		}
		
		//---EYES FINDINGS---
		if(!empty($eyes_finding) && $eyes_finding== '-ve')
		{ 
			$eyes = $negative_findings;
		}
		else if (!empty($eyes) && $eyes_finding!= '-ve')
		{
			$eyes = $eyes_finding; 
		}
		else if (empty($eyes) && empty($eyes_finding))
		{
			$eyes = $no_findings;
		}
		
		//---GASTROINTESTINAL FINDINGS---
		if(!empty($gastrointestinal_finding) && $gastrointestinal_finding== '-ve')
		{ 
			$gastrointestinal = $negative_findings;
		}
		else if (!empty($gastrointestinal) && $gastrointestinal_finding!= '-ve')
		{
			$gastrointestinal = $gastrointestinal_finding; 
		}
		else if (empty($gastrointestinal) && empty($gastrointestinal_finding))
		{
			$gastrointestinal = $no_findings;
		}
		
		//---GENITOURINARY FINDINGS---
		if(!empty($genitourinary_finding) && $genitourinary_finding== '-ve')
		{ 
			$genitourinary = $negative_findings;
		}
		else if (!empty($genitourinary) && $genitourinary_finding!= '-ve')
		{
			$genitourinary = $genitourinary_finding; 
		}
		else if (empty($genitourinary) && empty($genitourinary_finding))
		{
			$genitourinary = $no_findings;
		}
		
		//---HEMOTOLOGIC/LYMPHATIC FINDINGS---
		if(!empty($hemotologiclymphatic_finding) && $hemotologiclymphatic_finding== '-ve')
		{ 
			$hemotologiclymphatic = $negative_findings;
		}
		else if (!empty($hemotologiclymphatic) && $hemotologiclymphatic_finding!= '-ve')
		{
			$hemotologiclymphatic = $hemotologiclymphatic_finding; 
		}
		else if (empty($hemotologiclymphatic) && empty($hemotologiclymphatic_finding))
		{
			$hemotologiclymphatic = $no_findings;
		}
		
		//---INTEGUMENTARY FINDINGS---
		if(!empty($integumentary_finding) && $integumentary_finding== '-ve')
		{ 
			$integumentary = $negative_findings;
		}
		else if (!empty($integumentary) && $integumentary_finding!= '-ve')
		{
			$integumentary = $integumentary_finding; 
		}
		else if (empty($integumentary) && empty($integumentary_finding))
		{
			$integumentary = $no_findings;
		}
		
		//---MUSCULOSKELETAL FINDINGS---
		if(!empty($musculoskeletal_finding) && $musculoskeletal_finding== '-ve')
		{ 
			$musculoskeletal = $negative_findings;
		}
		else if (!empty($musculoskeletal) && $musculoskeletal_finding!= '-ve')
		{
			$musculoskeletal = $musculoskeletal_finding; 
		}
		else if (empty($musculoskeletal) && empty($musculoskeletal_finding))
		{
			$musculoskeletal = $no_findings;
		}
		
		//--- NEUROLOGICAL FINDINGS---
		if(!empty($neurological_finding) && $neurological_finding== '-ve')
		{ 
			$neurological = $negative_findings;
		}
		else if (!empty($neurological) && $neurological_finding!= '-ve')
		{
			$neurological = $neurological_finding; 
		}
		else if (empty($neurological) && empty($neurological_finding))
		{
			$neurological = $no_findings;
		}
		
		//--- PSYCHIATRIC FINDINGS---
		if(!empty($psychiatric_finding) && $psychiatric_finding== '-ve')
		{ 
			$psychiatric = $negative_findings;
		}
		else if (!empty($psychiatric) && $psychiatric_finding!= '-ve')
		{
			$psychiatric = $psychiatric_finding; 
		}
		else if (empty($psychiatric) && empty($psychiatric_finding))
		{
			$psychiatric = $no_findings;
		}
		
		//--- RESPIRATORY FINDINGS---
		if(!empty($respiratory_finding) && $respiratory_finding== '-ve')
		{ 
			$respiratory = $negative_findings;
		}
		else if (!empty($respiratory) && $respiratory_finding!= '-ve')
		{
			$respiratory = $respiratory_finding; 
		}
		else if (empty($respiratory) && empty($respiratory_finding))
		{
			$respiratory = $no_findings;
		}
		
		if($chk_gen_rev==false){$strMedicalCond.="&nbsp;None";  $genHealth="&nbsp;None";}if($chk_rev==false){$strROS.="&nbsp;None"; $strROS_detail.="&nbsp;None";}
		$ghData .= "<tr><td style=\"width:350px;vertical-align:top;\"><table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"border-bottom:1px solid #CCC;border-right:1px solid #CCC; width:350px;padding-left:5px;\">".$strMedicalCond."</td></tr></table></td><td style=\"width:350px;vertical-align:top;border-bottom:1px solid #CCC;border-left:1px solid #CCC;\"><table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"border-bottom:1px solid #CCC; width:350px; padding-left:5px;\">".$strROS."</td></tr></table></td></tr>";
		//$ghData .=$ghData_gen;
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
						//$smoke_source = substr($smoke_source,0,10)."...";
					}
				}
				
				$smoke_per_day = $rowSmoking["smoke_perday"];
				if($smoke_per_day=="" || empty($smoke_per_day) || $smoke_per_day==NULL){
					$smoke_per_day = "";
				}
				$smoke_str = (($rowSmoking["smoking_status"]) ? $rowSmoking["smoking_status"] : "").(($smoke_source) ? "&nbsp;of&nbsp;".$smoke_source : "")."&nbsp;".(($smoke_per_day) ? $smoke_per_day."&nbsp;Per Day" : "");
				$SmokeSumray .= "<tr><td colspan='2' style=\"padding-left:5px; border-bottom:1px solid #CCC;\">".(($smoke_str) ? "&nbsp;&nbsp;".$smoke_str : '')."</td></tr>";
			}
			else{
				$SmokeSumray .= "<tr><td colspan='2' style=\"padding-left:5px; border-bottom:1px solid #CCC;\">&nbsp;&nbsp;Never Smoked</td></tr>";
			}
			//-------alcohal-------
			$alcohal = $rowSmoking["alcohal"];
			if($alcohal == "Other"){
				$alcohal = $rowSmoking["source_of_alcohal_other"];
				if(strlen($alcohal)>13){
					//$alcohal = substr($alcohal,0,13)."...";
				}
			}
			$SmokeSumray .="<tr><td colspan='2' style=\"padding-left:5px; border-bottom:1px solid #CCC;\">".((trim($alcohal) != "") ? "&nbsp;&nbsp;Alcohol ".$alcohal : '')."</td></tr>";
			//-------alcohal-------
	
			//-------List Drugs-------
			$list_drugs = $rowSmoking["list_drugs"];
			if(strlen($list_drugs)>21){
				//$list_drugs = substr($list_drugs,0,21)."...";
			}
			$SmokeSumray .= "<tr><td colspan='2' style=\"padding-left:5px; border-bottom:1px solid #CCC;\">".((trim($list_drugs) != "") ? "&nbsp;&nbsp;".$list_drugs : '')."</td></tr>";
			//-------List Drugs-------			
	
			//-------More Information-------
			$other_social = $rowSmoking["otherSocial"];
			if(strlen($other_social)>20){
				//$other_social = substr($other_social,0,20)."...";
			}
			$SmokeSumray .= "<tr><td colspan='2' style=\"padding-left:5px; border-bottom:1px solid #CCC;\">".((trim($other_social) != "") ? "&nbsp;&nbsp;".$other_social : '')."</td></tr>";
			//-------More Information-------
		}
		$ghData .= "<tr><td colspan='2' style=\"border-bottom:1px solid #CCC;padding-left:5px;padding-top:3px;background:#CCC;\"><b>Social</b></td></tr>";	
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
			$title = $rowGetListData['title'].',&nbsp;';
			$title = ucfirst($title);
			
			if(strlen($title) > $max_chars){
				//$title = substr($title,0,$max_showc).'...';
			}
			$type = $rowGetListData['type'];
			$ag_occular_drug = $rowGetListData['ag_occular_drug'];
			
			if($type == 7 && $ag_occular_drug == 'fdbATIngredient') {//SHOW DRUG AND Allergen ALLEGIES
				$type="";
			}
			//--- DELETED MEDICATION DATA --
			$rowStyle = '';
			switch ($type):
					case 1:
						//--- GET MEDICATION DATA -----
						$medicationData .= $title;
					break;
					case 3:
						//--- GET OCULAR ALLERGY DATA -----
						$ocularAllData .= $title;
					break;
					case 4:
						//--- GET OCULAR MEDICATION DATA -----
						$ocularData .= $title;
					break;
					case 5:
						//--- GET SX/PROCEDURE DATA -----
						$sxProcData .= $title;
					break;
					case 6:
						//--- GET OCULAR SX DATA -----
						$ocularSxData .= $title;
					break;		
					case 7:
						//--- GET DRUG ALLERGY DATA -----
						$drugAllData .= $title;
					break;
			endswitch;
		}
		if(trim($ocularData)==""){$ocularData="&nbsp;None";}
		if(trim($medicationData)==""){$medicationData="&nbsp;None";}
		if(trim($ocularSxData)==""){$ocularSxData="&nbsp;None";}
		if(trim($sxProcData)==""){$sxProcData="&nbsp;None";}
		if(trim($drugAllData)==""){$drugAllData="&nbsp;NKDA";}
		
		$MSAData.= "<tr><td colspan='2' style='padding-left:5px;padding-top:3px;border-bottom:1px solid #CCC;background:#CCC;'><b>Medication</b></td></tr>";
		$MSAData.= "<tr><td style='padding-left:5px;border-bottom:1px solid #CCC;'><b>Ocular</b></td><td style='border-bottom:1px solid #CCC;padding-left:5px;'><b>Systemic</b></td></tr>";
		
		$MSAData.= "<tr><td style='border-right:1px solid #CCC;width:360px;padding-left:5px;vertical-align:top;'><table cellspacing='0' cellpadding='0' style='border-right:1px solid #CCC;'><tr><td style=\"border-bottom:1px solid #CCC; width:310px; padding-left:5px;\">".$ocularData."</td></tr></table></td><td style='border-bottom:1px solid #CCC;width:50%;vertical-align:top;'><table cellspacing='0' cellpadding='0'><tr><td style=\"border-bottom:1px solid #CCC; width:310px; padding-left:5px;\">".$medicationData."</td></tr></table></td></tr>";
		
		/*$MSAData.= "<tr><td><b>Medication</b></td></tr>";
		$MSAData.= "<tr><td>".$medicationData."</td></tr>";
		*/	
		$MSAData.= "<tr><td colspan='2' style='padding-left:5px;padding-top:3px;border-bottom:1px solid #CCC;background:#CCC;'><b>Sx/Procedures</b></td></tr>";
		$MSAData.= "<tr><td style='padding-left:5px;border-bottom:1px solid #CCC;'><b>Ocular</b></td><td style='border-bottom:1px solid #CCC;padding-left:5px;'><b>Other</b></td></tr>";
		$MSAData.= "<tr><td style='border-bottom:1px solid #CCC;border-right:1px solid #CCC;width:355px;padding-left:5px;vertical-align:top;'><table cellspacing='0' cellpadding='0' style='border-right:1px solid #CCC;'><tr><td style=\"border-bottom:1px solid #CCC; width:310px; padding-left:5px;\">".$ocularSxData."</td></tr></table></td><td style='border-bottom:1px solid #CCC;width:50%;vertical-align:top;'><table cellspacing='0' cellpadding='0'><tr><td style=\"border-bottom:1px solid #CCC; width:310px; padding-left:5px;\">".$sxProcData."</td></tr></table></td></tr>";
		
		$MSAData.= "<tr><td style='padding-left:5px;padding-top:3px;border-bottom:1px solid #CCC;background:#CCC;'><b>Drug Allergies</b></td><td style='padding-left:5px;padding-top:3px;border-bottom:1px solid #CCC;background:#CCC;'><!--<b>Immunizations</b>--></td></tr>";
		//$MSAData.= "<tr><td>".$drugAllData."</td></tr>";
		
		/*
		$MSAData.= "<tr><td><b>Ocular Sx/Procedures</b></td></tr>";
		$MSAData.= "<tr><td>".$ocularSxData."</td></tr>";
		
		$MSAData.= "<tr><td><b>Sx/Procedures</b></td></tr>";
		$MSAData.= "<tr><td>".$sxProcData."</td></tr>";*/
		//Medications, Sx/Procedures, Allergies End
		
		
		//$immData .= "<tr><td><b>Immunizations</b></td></tr>";
		$immDiv = "";
		$qryGetImmu = "select date_format(administered_date,'".get_sql_date_format()."') as administered_date,
						immunization_id,id from immunizations  
						where patient_id = '$ptId' and status = 'Given'";
		$rsGetImmu = imw_query($qryGetImmu);
		while($rowGetImmu = imw_fetch_array($rsGetImmu)){
			$administered_date = $rowGetImmu['administered_date'];
			if(get_number($administered_date) == '00000000'){
				$administered_date = '';
			}
			$name = $rowGetImmu['immunization_id'];
			$im_id = $rowGetImmu["id"];
			if(strlen($name) > $max_chars){
				$name = substr($name,0,$max_chars).'...';
			}		
			$immDiv .=  "<tr><td style=\"width:355px; padding-left:5px;\">".$administered_date.' '.$name."</td></tr>"; 		
		}
		$immData .= "<tr><td style=\"vertical-align:top;width:310px;border-right:1px solid #CCC;border-bottom:1px solid #CCC;\"><table cellspacing='0' cellpadding='0' style='border-right:1px solid #CCC;width:100%;'><tr><td style=\"border-bottom:1px solid #CCC; width:310px; padding-left:5px;\">".$drugAllData."</td></tr></table></td><td style=\"vertical-align:top;border-bottom:1px solid #CCC;\"><!--<table cellspacing='0' cellpadding='0'>".$immDiv."</table>--></td></tr>";
		
		$medHxData = "<table style=\"width:650px; font-size:14px; border-top:1px solid #CCC;border-left:1px solid #CCC;border-right:1px solid #CCC;\" cellpadding=\"0\" cellspacing=\"0\">";
		$medHxData .= $ocData.$ghData.$MSAData.$immData;
		$medHxData .= "</table>";
		
		$neg_string_list ="";
		$neg_string_list = "All recorded systems are negative except as noted below.<br>";
		
		$ros_detail = str_ireplace('</td>','<br></td>',$strROS_detail);
		$ros_detail =  strip_tags($ros_detail,'<b><br>');
		
		//---ROS DETAIL VARIABLE CONDITIONAL DATA---
		if(empty($ros_detail)) { $neg_string_list = "There are no negative findings"; }
		if(empty($strROS_summary)) { $neg_string_list = "There are no negative findings"; }
		
		//---BELOW SWITCH CASE STARTED TO RENDER AS PER VARIABLE---
		switch($rosData)
		{
			case "ROS_SUMMARY":
				$medHxData = "";
				$medHxData .= $neg_string_list;
				$medHxData .=$strROS_summary;
			break;
			case "ROS_DETAIL":
				$medHxData = "";
				$medHxData .= $neg_string_list;
				$medHxData .=$ros_detail;
			break;
			case "ROS_ALLERGICIMMUNOLOGIC":
				$medHxData = "";
				$medHxData = $allergicimmunologic;
			break;
			case "ROS_CARDIOVASCULAR":
				$medHxData = "";
				$medHxData = $cardiovascular;
			break;
			case "ROS_CONSTITUTIONAL":
				$medHxData = "";
				$medHxData = $constitutional;
			break;
			case "ROS_EARNOSEMOUTHTHROAT":
				$medHxData = "";
				$medHxData = $earnosemouththroat;
			break;
			case "ROS_ENDOCRINE":
				$medHxData = "";
				$medHxData = $endocrine;
			break;
			case "ROS_EYES":
				$medHxData = "";
				$medHxData = $eyes;
			break;
			case "ROS_GASTROINTESTINAL":
				$medHxData = "";
				$medHxData = $gastrointestinal;
			break;
			case "ROS_GENITOURINARY":
				$medHxData = "";
				$medHxData = $genitourinary;
			break;
			case "ROS_HEMOTOLOGICLYMPHATIC":
				$medHxData = "";
				$medHxData = $hemotologiclymphatic;
			break;
			case "ROS_INTEGUMENTARY":
				$medHxData = "";
				$medHxData = $integumentary;
			break;
			case "ROS_MUSCULOSKELETAL":
				$medHxData = "";
				$medHxData = $musculoskeletal;
			break;
			case "ROS_NEUROLOGICAL":
				$medHxData = "";
				$medHxData = $neurological;
			break;
			case "ROS_PSYCHIATRIC":
				$medHxData = "";
				$medHxData = $psychiatric; 
			break;
			case "ROS_RESPIRATORY":
				$medHxData = "";
				$medHxData = $respiratory;
			break;
			default:
				$medHxData;
		}
		
		if($defaultData=="General Health")
		{
			$medHxData = "";
			$medHxData = $genHealth;
		}
		
		if($ocuOther=="Ocular_Other")
		{
			$medHxData = "";
			$medHxData = $ocularOther;
		}
		return $medHxData;
	}
	public function getMedHx_public($pt_id,$defaultData = NULL,$Ocular_Other = NULL){
		$medhx=$this->getMedHxSummary($pt_id,$defaultData,$Ocular_Other);	
		return $medhx;
	}
	//get patient problem list
	private function getMedProbList($pid) {	
		$medProbName = "";
		$problemNameArr = array();
		if($pid){
			$medProbQry = "SELECT problem_name FROM pt_problem_list WHERE pt_id='".$pid."' AND  
						status = 'Active' ORDER BY id";
			
			$medProbRes = imw_query($medProbQry);
			if(imw_num_rows($medProbRes)>0) {
				while($medProbRow = imw_fetch_array($medProbRes)) {
					$problemNameArr[] 	= trim($medProbRow["problem_name"]);
				}
				$medProbName = implode(', ',$problemNameArr);
			}
		}
		return $medProbName;
	}
	
	//get Medication
	private function getMedList($pid,$listType,$lot='')
	{	
		$medList = "";
		$medListArr = array();
		$siteArr = array("1"=>"OS","2"=>"OD","3"=>"OU","4"=>"PO");
		$lot = trim($lot);
		
		if($pid && $listType)
		{
			$medQry="SELECT 
						title, sig, qty, refills, comments, referredby, destination, sites, 
						DATE_FORMAT(begdate,'".get_sql_date_format()."') as begdateNew, 
						DATE_FORMAT(enddate,'".get_sql_date_format()."') as enddateNew 
					FROM 
						lists 
					WHERE 
						pid='".$pid."' 
					AND 
						allergy_status = 'Active' 
					AND 
						type in (".$listType.") 
					ORDER BY id";
			
			$medRes = imw_query($medQry) or die(imw_error());
			if(imw_num_rows($medRes)>0) {
				while($medRow = imw_fetch_array($medRes)) {
					$dosage 		= trim($medRow["destination"]);
					$sig 			= trim($medRow["sig"]);
					$dosageSig 		= "";
					$comments 		= trim($medRow["comments"]);
					$commentsVal 	= "";
					$ocuMedSite 	= trim($medRow["sites"]);
					
					if(($dosage || $sig) && $listType=='4') { $dosageSig=trim('('.$dosage.' '.$sig.')').' - '.$siteArr[$ocuMedSite];}
					if(($comments) && $listType=='7') { $commentsVal=trim('('.$comments.')');}
					
					if( !empty( $medRow["title"] ) && !empty( $lot ) && $lot == 1 )	
					{
						$medQryWithLot="SELECT lot_number FROM `chart_procedures_med_lot` WHERE med_name LIKE '".$medRow["title"]."%'";
						$medResWithLot = imw_query($medQryWithLot) or die(imw_error());
						if(imw_num_rows($medResWithLot)>0) 
						{	
							$medRowWithLot  = imw_fetch_array($medResWithLot);
							$medListArr[] 	= $medRow["title"].$dosageSig.$commentsVal.' - '.$medRowWithLot['lot_number'];	
						}
					}
					else
					{
						$medListArr[] 	= $medRow["title"].$dosageSig.$commentsVal;
					}
				}
				$medName = implode(', ',$medListArr);
				
				$medList = '<table style="width:100%;"  cellpadding="0" cellspacing="0">	
								<tr>
									<td style="width:100%;" valign="top" >'.$medName.'</td>				
								</tr>';	
				$medList .= '</table>';
				
				if( empty( $medName ) ) { $medList = ''; }
			}
			else
			{
				$medList = '<table style="width:100%;"  cellpadding="0" cellspacing="0">	
								<tr>
									<td style="width:100%;" valign="top" >NKDA</td>				
								</tr>';	
				$medList .= '</table>';
			}	
		}
		return $medList;
	}
	public function get_med_list($pid,$listType){
		$medList=$this->getMedList($pid,$listType);
		return $medList;
	}
	//get Ref. Phy and PCP of Patient	
	public function getRefPcpRef($patientId=0){
		$arrReturn = array();
		if($patientId != 0){
			$sql = "select pd.primary_care_phy_name as gmPCP, rf.Title as rfTitle,rf.FirstName as rfFName,rf.MiddleName as rfMName, 
					rf.LastName as rfLName, rf.physician_fax as rfFax, rf.physician_email as rfEmail, rfPCP.physician_email as rfPcpEmail, rfCoMan.physician_email as rfCoManEmail, rfPCP.physician_fax as rfPcpFax, rfCoMan.physician_fax as rfCoManFax, pd.primary_care_id as rfId,
					pd.primary_care_phy_id as rfPcpId, pd.co_man_phy as coManPhy, pd.co_man_phy_id as coManPhyId,
					rfCoMan.FirstName as rfCoManFName,rfCoMan.LastName as rfCoManLName
					 from patient_data pd
					 left join refferphysician rf on rf.physician_Reffer_id = pd.primary_care_id 
					 left join refferphysician rfPCP on rfPCP.physician_Reffer_id = pd.primary_care_phy_id 
					 left join refferphysician rfCoMan on rfCoMan.physician_Reffer_id = pd.co_man_phy_id  
					 where pd.id = '$patientId'";
			$res = sqlQuery($sql);
			
			if($res !== false){
				$rfTitle	 = $res["rfTitle"];	
				$rfFName	 = $res["rfFName"];		
				$rfMName	 = $res["rfMName"];	
				$rfLName	 = $res["rfLName"];	
				$gmPCP		 = $res["gmPCP"];	
				$rfFax		 = $res["rfFax"];
				$rfPcpFax	 = $res["rfPcpFax"];
				$rfId		 = $res["rfId"];
				$rfPcpId	 = $res["rfPcpId"];
				$coManPhy	 = $res["coManPhy"];
				$coManPhyId	 = $res["coManPhyId"];
				$rfCoManFName= $res["rfCoManFName"];	
				$rfCoManLName= $res["rfCoManLName"];	
				$rfCoManFax= $res["rfCoManFax"];
				
				$rfEmail= $res["rfEmail"];
				$rfPcpEmail= $res["rfPcpEmail"];
				$rfCoManEmail= $res["rfCoManEmail"];	
				//Returning Array
				$arrReturn = array($rfTitle,$rfFName,$rfMName,$rfLName,$gmPCP,$rfFax,$rfPcpFax,$rfId,$rfPcpId,$coManPhy,$coManPhyId,$rfCoManFName,$rfCoManLName,$rfCoManFax,$rfEmail,$rfPcpEmail,$rfCoManEmail);
			}
		}
		return $arrReturn;
	}
	
	//get Ref. Phy and PCP of Patient	
	public function getRefPcpinfo($refId=0){
		$arrReturn = array();
		if($refId != 0){
			$sql = "select rf.Title as rfTitle,rf.FirstName as rfFName,rf.MiddleName as rfMName,rf.LastName as rfLName, 
					rf.Address1 as rfAddress1,rf.Address2 as rfAddress2,rf.City as rfCity,rf.State as rfState,rf.ZipCode as rfZipCode,rf.physician_fax as rfPhysicianFax, rf.direct_email as rfDirectEmail ,rf.physician_phone, rf.specialty
					 from refferphysician rf
					 where rf.physician_Reffer_id = '".$refId."'";
			$res = sqlQuery($sql);
			
			if($res !== false){
				$rfTitle	 		= $res["rfTitle"];	
				$rfFName	 		= $res["rfFName"];		
				$rfMName	 		= $res["rfMName"];	
				$rfLName	 		= $res["rfLName"];	
				$rfAddress1	 		= $res["rfAddress1"];	
				$rfAddress2	 		= $res["rfAddress2"];	
				$rfCity	 	 		= $res["rfCity"];	
				$rfState	 		= $res["rfState"];	
				$rfZipCode	 		= $res["rfZipCode"];	
				$rfPhysicianFax		= $res["rfPhysicianFax"];
				$rfPhysicianPhone	= $res["physician_phone"];	
				$rfDirectSpecialty	= $res["specialty"];	
				$rfDirectEmail		= $res["rfDirectEmail"];	
				//Returning Array
				$arrReturn = array($rfTitle,$rfFName,$rfMName,$rfLName,$rfAddress1,$rfAddress2,$rfCity,$rfState,$rfZipCode,$rfPhysicianFax,$rfDirectEmail, $rfPhysicianPhone, $rfDirectSpecialty);
			}
		}
		return $arrReturn;
	}
	
	
	//get Ref. Phy-id and PCP-id of Patient	
	public function getRefPcpId($refPcpName){
		$arrReturn = array();
		if(trim($refPcpName)){
			$arrRefPcpDoc = explode(",",$refPcpName);				
			$refPcp_lname = trim($arrRefPcpDoc[0]);
			$arrRefPcpLname = explode(" ",$refPcp_lname);
			$refPcp_lname = ($arrRefPcpLname[1] != "") ? $arrRefPcpLname[1] : $arrRefPcpLname[0];
			$refPcp_fname = trim($arrRefPcpDoc[1]);
			$arrRefPcpFname = explode(" ",$refPcp_fname);
			$refPcp_fname = $arrRefPcpFname[0];
			
			$sql = "select physician_Reffer_id from refferphysician where FirstName = '".addslashes($refPcp_fname)."' and LastName = '".addslashes($refPcp_lname)."'";	
			$res = sqlQuery($sql);
			if($res !== false){
				$rf_physician_Reffer_id	 = $res["physician_Reffer_id"];	
					
				//Returning Array
				$arrReturn = array($rf_physician_Reffer_id);
			}
		}
		return $arrReturn;
			
	}
	

	//Get Text Box	
	private function getTextBox($str){
		#################
		$repValMain = "";
		$arrStr = array("{TEXTBOX_SMALL}","{TEXTBOX_MEDIUM}");
		for($j = 0;$j<count($arrStr);$j++){
			if($arrStr[$j] == '{TEXTBOX_XSMALL}'){
				$name = 'xsmall';
				$size = 1;
			}
			else if($arrStr[$j] == '{TEXTBOX_SMALL}'){
				$name = 'small';
				$size = 30;
			}
			else if($arrStr[$j] == '{TEXTBOX_MEDIUM}'){
				$name = 'medium';
				$size = 60;
			}
			else if($arrStr[$j] == '{TEXTBOX_LARGE}'){
				$name = 'large';
				$size = 120;
			}
			$repVal = '';
			if(substr_count($str,$arrStr[$j]) > 1){
				if($arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}'){
					$c = 1;
					$arrExp = explode($arrStr[$j],$str);					
					for($p = 0;$p<count($arrExp)-1;$p++){
						$repVal = str_replace($arrStr[$j],'<input type="text" name="'.$name.$c.'" value="'.$_POST[$name.$c].'" size="'.$size.'" maxlength="'.$size.'">',$str);
						$c++;
					}				
					$str = $repVal;
				}
				else if($arrStr[$j] == '{TEXTBOX_LARGE}'){
					$c = 1;
					$arrExp = explode($arrStr[$j],$str);
					for($p = 0;$p<count($arrExp)-1;$p++){
						$repVal = str_replace($arrStr[$j],'<textarea rows="2" cols="100" name="'.$name.$c.'"> '.$_POST[$name.$c].' </textarea>',$str);
						$c++;
					}	
					$str = $repVal;
				}			
			}
			else{
				if($arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}'){
					$repVal = str_replace($arrStr[$j],'<input type="text" name="'.$name.'" value="'.$_POST[$name].'" size="'.$size.'" maxlength="'.$size.'">',$str);
					$str = $repVal;
				}
				else if($arrStr[$j] == '{TEXTBOX_LARGE}'){
					$repVal = str_replace($arrStr[$j],'<textarea rows="2" cols="100" name="'.$name.'"> '.$_POST[$name].' </textarea>',$str);
					$str = $repVal;
				}
			}
		}		
		return $str;
	}
	
	//START FUNCTIONS FOR OCULAR HISTORY
	
	private function getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label){
		$OldValueOrignal = "";
		$arrOldVal = explode(',',$Old_Value);										
		foreach ($arrOldVal as $key => $value) {
			if ($arrOldVal[$key] == "") {unset($arrOldVal[$key]);}
		}										
		foreach ($arrMEDHXGenHealth as $key => $value) {
			if($arrMEDHXGenHealth[$key]['Filed_Label'] == $Field_Label){
				//echo $arrMEDHXOculer[$key]['Filed_Label_Og_Val'].'--'.$Field_Label.'<br>';												
				foreach ($arrOldVal as $keyOld => $valueOld) {
					if ($arrOldVal[$keyOld] == $arrMEDHXGenHealth[$key]['Filed_Label_Val']){														
						$OldValueOrignal .= $arrMEDHXGenHealth[$key]['Filed_Label_Og_Val']."<br>";														
						unset($arrOldVal[$keyOld]);																																							
						break;
					}
				}																								
			}
		}
		$Old_Value = $OldValueOrignal;
		
		$newValueOrignal = "";
		$arrNewVal = explode(',',$New_Value);										
		foreach ($arrNewVal as $key => $value) {
			if ($arrNewVal[$key] == "") {unset($arrNewVal[$key]);}
		}										
		foreach ($arrMEDHXGenHealth as $key => $value) {
			if($arrMEDHXGenHealth[$key]['Filed_Label'] == $Field_Label){
				//echo $arrMEDHXOculer[$key]['Filed_Label_Og_Val'].'--'.$Field_Label.'<br>';												
				foreach ($arrNewVal as $keyNew => $valueNew) {
					if ($arrNewVal[$keyNew] == $arrMEDHXGenHealth[$key]['Filed_Label_Val']){														
						$newValueOrignal .= $arrMEDHXGenHealth[$key]['Filed_Label_Og_Val']."<br>";														
						unset($arrNewVal[$keyNew]);																																							
						break;
					}
				}																								
			}
		}										
		$New_Value = $newValueOrignal;
		return $Old_Value."~~~~".$New_Value;
	}

	private function getOrignalValWt2Sep($Old_Value,$New_Value,$arrMEDHXOculer,$Field_Label){
		$arrTmp = array();								
		$OldValueOrignal = "";
		$strSep="~!!~~";
		$strSep2=":*:";
		$arrOldVal = explode($strSep, $Old_Value);
		foreach($arrOldVal as $keyOld => $valueOld){
			$arrTmp[] = explode($strSep2,$valueOld);											
		}										
		foreach ($arrTmp as $key => $value) {											
			foreach ($value as $keyInner => $valueInner) {																					
				if ($value[$keyInner] == "") {unset($arrTmp[$key]);}																							
			}																					
		}	
		foreach ($arrMEDHXOculer as $key => $value) {
			if($arrMEDHXOculer[$key]['Filed_Label'] == $Field_Label){												
				foreach ($arrTmp as $keyOld => $valueOld) {
					foreach ($valueOld as $keyInner => $valueInner) {																					
						if ($valueOld[$keyInner] == $arrMEDHXOculer[$key]['Filed_Label_Val']){																												
							$OldValueOrignal .= $arrMEDHXOculer[$key]['Filed_Label_Og_Val']."&nbsp;".$valueOld[$keyInner+1]."<br>";											
							unset($arrTmp[$keyOld]);																																							
							break;
						} 																							
					}													
				}																								
			}
		}

		$Old_Value = $OldValueOrignal;
		
		$newValueOrignal = "";
		$arrTmp = array();																		
		$strSep="~!!~~";
		$strSep2=":*:";
		$arrOldVal = explode($strSep, $New_Value);
		foreach($arrOldVal as $keyOld => $valueOld){
			$arrTmp[] = explode($strSep2,$valueOld);											
		}										
		foreach ($arrTmp as $key => $value) {											
			foreach ($value as $keyInner => $valueInner) {																					
				if ($value[$keyInner] == "") {unset($arrTmp[$key]);}																							
			}																					
		}	

		foreach ($arrMEDHXOculer as $key => $value) {
			if($arrMEDHXOculer[$key]['Filed_Label'] == $Field_Label){												
				foreach ($arrTmp as $keyOld => $valueOld) {
					foreach ($valueOld as $keyInner => $valueInner) {																					
						if ($valueOld[$keyInner] == $arrMEDHXOculer[$key]['Filed_Label_Val']){																												
							$newValueOrignal .= $arrMEDHXOculer[$key]['Filed_Label_Og_Val']."&nbsp;".$valueOld[$keyInner+1]."<br>";											
							unset($arrTmp[$keyOld]);																																							
							break;
						} 																							
					}													
				}																								
			}
		}
		$New_Value = $newValueOrignal;
		return $Old_Value."~~~~".$New_Value;
	}
	
	private function isDate($i_sDate){
		return wv_formatDate($i_sDate);
		/*
		if(ereg ("^[0-9]{4}-[0-9]{2}-[0-9]{2}$", $i_sDate)){		
			//ereg ("^[0-9]{2}/[0-9]{2}/[0-9]{4}$", $i_sDate)	    
			$arrDate = explode("-", $i_sDate); 		
			$intYear = $arrDate[0]; 
			$intMonth = $arrDate[1];
			$intDay = $arrDate[2];		
			$intIsDate = checkdate($intMonth, $intDay, $intYear);
			if($intIsDate){
				$date = $intMonth."-".$intDay."-".$intYear;
			}
			return ($date);
		}
		else{
			return $i_sDate;
		} */ 	
	   
	} //end function isDate

	private function getOcularHist($pid) {
		$ocHx="";
		$arrMEDHX = array();
		$arrMEDHX [] = array("Filed_Label"=> "you_wear","Filed_Label_Val"=> '0',"Filed_Label_Og_Val"=> "None");
		$arrMEDHX [] = array("Filed_Label"=> "you_wear","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Glasses");
		$arrMEDHX [] = array("Filed_Label"=> "you_wear","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Contact Lenses");
		$arrMEDHX [] = array("Filed_Label"=> "you_wear","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Glasses And Contact Lenses");										
		$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Blurred or Poor Vision");
		$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Poor Night Vision");
		$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Gritty Sensation");
		$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Trouble Reading Signs");
		$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Glare From Lights");
		$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Tearing");
		$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Poor Depth Perception");
		$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Halos Around Lights");
		$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '9',"Filed_Label_Og_Val"=> "Itching or Burning");
		$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '10',"Filed_Label_Og_Val"=> "Trouble Identifying Colors");
		$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '11',"Filed_Label_Og_Val"=> "See Spots or Floaters");
		$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '12',"Filed_Label_Og_Val"=> "Eye Pain");
		$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "Double Vision");
		$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "See Light Flashes");
		$arrMEDHX [] = array("Filed_Label"=> "eye_problems","Filed_Label_Val"=> '15',"Filed_Label_Og_Val"=> "Redness or Bloodshot");
		$arrMEDHX [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "You Dry Eyes :");
		$arrMEDHX [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "You Macula Degeneration :");
		$arrMEDHX [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "You Glaucoma :");
		$arrMEDHX [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "You Retinal Detachment :");
		$arrMEDHX [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "You Cataracts :");
		$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Relative Dry Eyes :");
		$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Macula Degeneration :");
		$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Relative Glaucoma :");
		$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Relative Retinal Detachment :");
		$arrMEDHX [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Relative Cataracts :");
		$arrMEDHX [] = array("Filed_Label"=> "chronicDesc","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "You Dry Eyes :");
		$arrMEDHX [] = array("Filed_Label"=> "chronicDesc","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "You Macula Degeneration :");
		$arrMEDHX [] = array("Filed_Label"=> "chronicDesc","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "You Glaucoma :");
		$arrMEDHX [] = array("Filed_Label"=> "chronicDesc","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "You Retinal Detachment :");
		$arrMEDHX [] = array("Filed_Label"=> "chronicDesc","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "You Cataracts :");
		$arrMEDHX [] = array("Filed_Label"=> "chronicDesc","Filed_Label_Val"=> 'other',"Filed_Label_Og_Val"=> "You other :");
		$arrMEDHX [] = array("Filed_Label"=> "chronicRelative","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Relative Dry Eyes :");
		$arrMEDHX [] = array("Filed_Label"=> "chronicRelative","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Macula Degeneration :");
		$arrMEDHX [] = array("Filed_Label"=> "chronicRelative","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Relative Glaucoma :");
		$arrMEDHX [] = array("Filed_Label"=> "chronicRelative","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Relative Retinal Detachment :");
		$arrMEDHX [] = array("Filed_Label"=> "chronicRelative","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Relative Cataracts :");
		$arrMEDHX [] = array("Filed_Label"=> "chronicRelative","Filed_Label_Val"=> 'other',"Filed_Label_Og_Val"=> "Relative other :");
		
		
		$medQry = "select * from ocular where patient_id='".$pid."'";
		$medSql = imw_query($medQry);
		if(imw_num_rows($medSql)>0) {
		
			$result = imw_fetch_array($medSql);
			
			foreach ((array)$result as $key => $value) {	
				switch ($key):
					case "you_wear":														
						$orignalValue = $this->getOrignalValComa($value,$value,$arrMEDHX,$key);										
						$orignalValue = explode("~~~~",$orignalValue);										
						$youWearValue = $orignalValue[0];														
					break;
					case "last_exam_date":														
						$lastExamDateValue = $this->isDate($value);
					break;		
					case "eye_problems":										
						$orignalValue = $this->getOrignalValComa($value,$value,$arrMEDHX,$key);										
						$orignalValue = explode("~~~~",$orignalValue);										
						$eyeProblemValue = $orignalValue[0];	
						
					break;	
					case "any_conditions_you":										
						$orignalValue = $this->getOrignalValComa($value,$value,$arrMEDHX,$key);										
						$orignalValue = explode("~~~~",$orignalValue);										
						$anyConditionYouValue = $orignalValue[0];																	
						
					break;		
					case "any_conditions_relative":										
						$orignalValue = $this->getOrignalValComa($value,$value,$arrMEDHX,$key);										
						$orignalValue = explode("~~~~",$orignalValue);										
						$anyConditionRelativeValue = $orignalValue[0];
						break;									
					case "chronicDesc":												
						$orignalValue = $this->getOrignalValWt2Sep($value,$value,$arrMEDHX,$key);										
						$orignalValue = explode("~~~~",$orignalValue);										
						$chronicDescValue = $orignalValue[0];															
					break;
					case "chronicRelative":																								
						$orignalValue = $this->getOrignalValWt2Sep($value,$value,$arrMEDHX,$key);										
						$orignalValue = explode("~~~~",$orignalValue);										
						$chronicRelativeValue = $orignalValue[0];									
					break;	
					case "eye_problems_other":																																			
						$eyeProbOtherValue = $this->isDate($value);									
					break;		
					case "OtherDesc":																																				
						$otherDescValue = $this->isDate($value);									
					break;		
							
				endswitch;
			}	
		
			$ocHx = '
			
				<table width="100%" cellpadding="0" border="0" cellspacing="0">
					<tr>
						<td colspan="4" class="text_10b">
							<b>Ocular</b>
						</td>				
					</tr>	
					<tr><td height="7" colspan="4"></td></tr>
					<tr>
						<td class="text_10" colspan="4" valign="top">
							<i>Eye History :-</i>
						</td>
					</tr>	
					<tr>
						<td width="13%"  valign="top" class="text_10">
							<i>Do you wear:</i>
						</td>
						<td width="29%"  valign="top" class="text_10">
							'.$youWearValue.'
						</td>
						<td width="17%"  valign="top" class="text_10">
							<i>Last eye exam date:</i>
						</td>
						<td width="41%" valign="top" class="text_10">
							'.$lastExamDateValue.'
						</td>
					</tr>
					<tr><td height="7" colspan="4"></td></tr>
					<tr>
						<td class="text_10" valign="top">';
							if($eyeProblemValue){
								$ocHx .= '<i>Eye problems :-</i>';
							}
			$ocHx .='			
						</td>	
						<td  class="text_10" valign="top">
							'.$eyeProblemValue.'
						</td>	
						<td  class="text_10" valign="top">';
							if($eyeProbOtherValue){
								$ocHx .= '<i>Other eye problems :-</i>';
							}
			$ocHx .='        
						</td>		
						<td class="text_10" valign="top">
							'.$eyeProbOtherValue.'
						</td>	
					</tr>
					<tr><td height="7" colspan="4"></td></tr>
					<tr>
						<td class="text_10" colspan="4" valign="top">';
						if($chronicDescValue || $chronicRelativeValue){
							$ocHx .= 'Any condition you or blood relative :- ';
						}
			$ocHx .=' 		
						</td>			
					</tr>
					<tr><td height="7" colspan="4"></td></tr>
					<tr>
						<td colspan="2" class="text_10"  valign="top">';
						if($chronicDescValue){
							$ocHx .= '<i>Any condition you :-</i>';
						}
			$ocHx .=' 
						</td>		
						<td colspan="2" class="text_10"  valign="top">';
						if($chronicRelativeValue){
							$ocHx .= '<i>Any condition blood relative :-</i>';
						}
			$ocHx .='
						</td>		
					</tr>
					<tr><td height="7" colspan="4"></td></tr>
					<tr>
						<td colspan="2" class="text_10">			
							'.$chronicDescValue.'		
						</td>	
						<td colspan="2" class="text_10" align="left" valign="top">
							'.$chronicRelativeValue.'
						</td>	
					</tr>
					<tr><td height="7" colspan="4"></td></tr>
				</table>';
		}
		return $ocHx;
	}	
	//END FUNCTIONS FOR OCULAR HISTORY	
	
	function getLatestVitalSign($pid) {
	
		//vital sign
		$vsQry = "SELECT vsp.* FROM `vital_sign_master` vsm INNER JOIN vital_sign_patient vsp ON vsp.vital_master_id = vsm.id
				  WHERE `patient_id`='".$pid."' ORDER BY vsm.date_vital DESC, vsp.vital_master_id DESC,vsp.vital_sign_id ASC LIMIT 0,10";			
		$vsRes = imw_query($vsQry);
		$latestVtSign='';
		if(imw_num_rows($vsRes)>0) {
			while($vsRow = imw_fetch_array($vsRes)) {
				$vital_sign_id 	= $vsRow["vital_sign_id"];
				$range_vital 	= $vsRow["range_vital"];
				switch($vital_sign_id) {
					case '1':
						$BP_Systolic = $range_vital;
					break;
					case '2':
						$BP_Diastolic = $range_vital;
					break;
					case '3':
						$pulse = $range_vital;
					break;
					case '4':
						$Respiration = $range_vital;
					break;
					case '5':
						$O2Sat = $range_vital;
					break;
					case '6':
						$Temperature = $range_vital;
					break;
				}
				//<td>'.$BP_Systolic.'/'.$BP_Diastolic.' '.$pulse.' '.$Respiration.' '.$O2Sat.' '.$Temperature.'</td>				
				$latestVtSign = '<table cellpadding="0" border="0" cellspacing="0">';			
				if($BP_Systolic && $BP_Diastolic) {
					$latestVtSign .= '<tr>
										<td><b>BP-S/BP-D : </b></td>
										<td>'.$BP_Systolic.'/'.$BP_Diastolic.'</td>				
									 </tr>';			
				}
				if($pulse) {
					$latestVtSign .= '<tr>
										<td><b>PULSE : </b></td>
										<td>'.$pulse.'</td>				
									 </tr>';			
				}
				if($Respiration) {
					$latestVtSign .= '<tr>
										<td><b>RESP : </b></td>
										<td>'.$Respiration.'</td>				
									 </tr>';			
				}
				if($O2Sat) {
					$latestVtSign .= '<tr>
										<td><b>O2SAT : </b></td>
										<td>'.$O2Sat.'</td>				
									 </tr>';			
				}
				if($Temperature) {
					$latestVtSign .= '<tr>
										<td><b>TEMP : </b></td>
										<td>'.$Temperature.'</td>				
									 </tr>';			
				}
				$latestVtSign .= '</table>';				
			}
	
		}
		return $latestVtSign;
	}
	public function all_ins_case($pid){
		if($pid){
			 $qry_ins_case="select ind.provider as priInsComp,
						 ict.case_name as case_name,
						 inc.name as insurance_company,
						 ind.type as type,
						 ind.policy_number as policyNumber,
						 ind.group_number as groupNumber,
						 CONCAT(ind.subscriber_fname,'&nbsp;',ind.subscriber_lname)as subscriberName,
						 ind.subscriber_relationship as relation,
						 date_format(ind.subscriber_DOB,'%m-%d-%Y') as priSubscriberDOB,
						 ind.subscriber_ss as priSubscriberSS,
						 ind.subscriber_phone as priSubscriberPhone,
						 ind.subscriber_street as priSubscriberStreet,
						 ind.subscriber_city as priSubscriberCity,
						 ind.subscriber_state as priSubscriberState,
						 ind.subscriber_postal_code as priSubscriberZip,
						 ind.subscriber_employer as priSubscriberEmployer
						 from insurance_data as ind INNER JOIN insurance_case as ic
						 on ind.ins_caseid=ic.ins_caseid
						 JOIN insurance_case_types as ict  
						 on ict.case_id=ic.ins_case_type
						 JOIN insurance_companies as inc
						 on ind.provider=inc.id
						 where ind.pid = '".$pid."' and 
						 ind.ins_caseid > 0 
						 and ind.actInsComp = 1
						 and ind.provider > 0 ORDER BY ict.case_name,ind.type";
			$res_ins_case=imw_query($qry_ins_case);
			$ins_all_case='';
			if(imw_num_rows($res_ins_case)>0){
				$ins_all_case.='<table style="width:700px;font-size:14px;border:1px solid #CCC;" cellpadding="0" cellspacing="0">';
				$ins_all_case.='	<tr>';
				$ins_all_case.='		<td style="width:60px;font-weight:bold;padding:3px;border-bottom:1px solid #CCC;border-right:1px solid #CCC; background:#CCC;">Case</td>';
				$ins_all_case.='		<td style="width:130px;font-weight:bold;padding:3px;border-bottom:1px solid #CCC;border-right:1px solid #CCC;background:#CCC;">Ins. Company</td>';
				$ins_all_case.='		<td style="width:70px;font-weight:bold;padding:3px;border-bottom:1px solid #CCC;border-right:1px solid #CCC;background:#CCC;">Type</td>';
				$ins_all_case.='		<td style="width:110px;font-weight:bold;padding:3px;border-bottom:1px solid #CCC;border-right:1px solid #CCC;background:#CCC;">Policy No.</td>';
				$ins_all_case.='		<td style="width:110px;font-weight:bold;padding:3px;border-bottom:1px solid #CCC;border-right:1px solid #CCC;background:#CCC;">Group No.</td>';
				$ins_all_case.='		<td style="width:100px;font-weight:bold;padding:3px;border-bottom:1px solid #CCC;border-right:1px solid #CCC;background:#CCC;">Sub. Name</td>';
				$ins_all_case.='		<td style="width:60px;font-weight:bold;padding:3px;border-bottom:1px solid #CCC;background:#CCC;">Relation</td>';
				$ins_all_case.='	</tr>';
				while($row_ins_case=imw_fetch_array($res_ins_case)){
					$ins_all_case.='<tr>';
					$ins_all_case.='	<td style="width:60px;vertical-align:top;padding:3px; border-bottom:1px solid #CCC;border-right:1px solid #CCC;">'.$row_ins_case['case_name'].'</td>';
					$ins_all_case.='	<td style="width:130px;vertical-align:top;padding:3px;border-bottom:1px solid #CCC;border-right:1px solid #CCC;">'.$row_ins_case['insurance_company'].'</td>';
					$ins_all_case.='	<td style="width:70px;vertical-align:top;padding:3px;border-bottom:1px solid #CCC;border-right:1px solid #CCC;">'.ucwords($row_ins_case['type']).'</td>';
					$ins_all_case.='	<td style="width:110px;vertical-align:top;padding:3px;border-bottom:1px solid #CCC;border-right:1px solid #CCC;">'.$row_ins_case['policyNumber'].'</td>';
					$ins_all_case.='	<td style="width:110px;vertical-align:top;padding:3px;border-bottom:1px solid #CCC;border-right:1px solid #CCC;">'.$row_ins_case['groupNumber'].'</td>';
					$ins_all_case.='	<td style="width:100px;vertical-align:top;padding:3px;border-bottom:1px solid #CCC;border-right:1px solid #CCC;">'.$row_ins_case['subscriberName'].'</td>';
					$ins_all_case.='	<td style="width:60px;vertical-align:top;padding:3px;border-bottom:1px solid #CCC;">'.ucwords($row_ins_case['relation']).'</td>';
					$ins_all_case.='</tr>';
				}
				$ins_all_case.='</table>';
			}
			return $ins_all_case;			 	
		}
	}
	
	//GET VISION PRIMARY INSURANCE CASE ONLY
	public function getVisionPriIns($str,$ptId,$type='primary') {
		$currentCaseId = $_SESSION['currentCaseid'];
		$currentCaseVision = false;
		if( $currentCaseId ) {
			$q = "Select ict.vision From insurance_case ic 
             INNER JOIN insurance_case_types ict on (ic.ins_case_type=ict.case_id )
             WHERE ic.ins_caseid = ".$currentCaseId." And ict.vision = 1 Limit 0,1";
	
		$s = imw_query($q);
			$c = imw_num_rows($s);
			if( $c ) $currentCaseVision = true;
		}
		
		if( $currentCaseVision) {
			//Replace
			$str = $this->refineData($str, $this->kVisPriIns, array(), $this->tVisPriIns,"");  
			return $str;
		}
  
		$q = "SELECT id.provider as priInsComp,id.policy_number as priPolicyNumber,id.group_number as priGroupNumber, ".
			 "CONCAT(id.subscriber_fname,'&nbsp;',id.subscriber_lname)as priSubscriberName,id.subscriber_relationship as priSubscriberRelation, ".
			 "date_format(id.subscriber_DOB,'".get_sql_date_format()."') as priSubscriberDOB,id.subscriber_ss as priSubscriberSS,id.subscriber_phone as priSubscriberPhone, ".
			 "id.subscriber_street as priSubscriberStreet,id.subscriber_city as priSubscriberCity,id.subscriber_state as priSubscriberState, ".
			 "id.subscriber_postal_code as priSubscriberZip,id.subscriber_employer as priSubscriberEmployer, ".
			"priInCmp.name as priInsCompName, CONCAT(priInCmp.contact_address,if(TRIM(priInCmp.city)!='',CONCAT(' ',priInCmp.city),''),if(TRIM(priInCmp.State)!='',CONCAT(', ',priInCmp.State),''),if(TRIM(priInCmp.Zip)!='',CONCAT(' ',priInCmp.Zip),''),if(TRIM(priInCmp.zip_ext)!='',CONCAT('-',priInCmp.zip_ext),'')) AS priInsCompAddress
		
		FROM insurance_data id ".
		"JOIN insurance_case ic on (id.ins_caseid = ic.ins_caseid and ic.case_status = 'Open') ".
		"left join insurance_case_types ict on (ict.case_id = ic.ins_case_type) ".
		"LEFT JOIN insurance_companies priInCmp ON (priInCmp.id = id.provider AND id.pid = '".$ptId."' ) ".
		" where id.actInsComp = '1' and id.pid = '".$ptId."' and ".($currentCaseVision ? " ict.default_selected = 1 " : "ict.vision = 1" )." and id.type='".strtolower($type)."' ";
		
		$res = sqlQuery($q);
		if($res!=false){
			
			$visPriInsComp 				= $res["priInsComp"];
			$visPriPolicyNumber 		= $res["priPolicyNumber"];
			$visPriGroupNumber 			= $res["priGroupNumber"];
			$visPriSubscriberName 		= $res["priSubscriberName"];
			$visPriSubscriberRelation 	= $res["priSubscriberRelation"];
			$visPriSubscriberDOB 		= $res["priSubscriberDOB"];
			$visPriSubscriberSS 		= $res["priSubscriberSS"];
			$visPriSubscriberPhone 		= $res["priSubscriberPhone"];
			$visPriSubscriberStreet 	= $res["priSubscriberStreet"];
			$visPriSubscriberCity 		= $res["priSubscriberCity"];
			$visPriSubscriberState 		= $res["priSubscriberState"];
			$visPriSubscriberZip 		= $res["priSubscriberZip"];
			$visPriSubscriberEmployer 	= $res["priSubscriberEmployer"];
			$visPriInsCompName 			= $res["priInsCompName"];
			$visPriInsCompAddress 		= $res["priInsCompAddress"];
			
			
			$arrRep = array($visPriInsCompName,$visPriPolicyNumber,$visPriGroupNumber,$visPriSubscriberName,$visPriSubscriberRelation,$visPriSubscriberDOB,$visPriSubscriberSS,$visPriSubscriberPhone,$visPriSubscriberStreet,$visPriSubscriberCity,$visPriSubscriberState,$visPriSubscriberZip,$visPriSubscriberEmployer,$visPriInsCompAddress);			
					
		}	
		//Replace
		$str = $this->refineData($str, $this->kVisPriIns, $arrRep, $this->tVisPriIns,"");		
		return $str;	
	}
	
	//GET VISION SECONDARY INSURANCE CASE ONLY
	public function getVisionSecIns($str,$ptId,$type='secondary') {
		$currentCaseId = $_SESSION['currentCaseid'];
		$currentCaseVision = false;
		if( $currentCaseId ) {
			$q = "SELECT ict.vision From insurance_case ic 
             INNER JOIN insurance_case_types ict on (ic.ins_case_type=ict.case_id )
             WHERE ic.ins_caseid = ".$currentCaseId." And ict.vision = 1 Limit 0,1";
			$s = imw_query($q);
			$c = imw_num_rows($s);
			if( $c ) $currentCaseVision = true;
		}
		
		if( $currentCaseVision) {
			//Replace
			$str = $this->refineData($str, $this->kVisSecIns, array(), $this->tVisSecIns,"");  
			return $str;
		}
		
		$q = "SELECT 
			id.provider as secInsComp,id.policy_number as secPolicyNumber,id.group_number as secGroupNumber, ".
			 "CONCAT(id.subscriber_fname,'&nbsp;',id.subscriber_lname)as secSubscriberName,id.subscriber_relationship as secSubscriberRelation, ".
			 "date_format(id.subscriber_DOB,'".get_sql_date_format()."') as secSubscriberDOB,id.subscriber_ss as secSubscriberSS,id.subscriber_phone as secSubscriberPhone, ".
			 "id.subscriber_street as secSubscriberStreet,id.subscriber_city as secSubscriberCity,id.subscriber_state as secSubscriberState, ".
			 "id.subscriber_postal_code as secSubscriberZip,id.subscriber_employer as secSubscriberEmployer, ".
			"secInCmp.name as secInsCompName, CONCAT(secInCmp.contact_address,if(TRIM(secInCmp.city)!='',CONCAT(' ',secInCmp.city),''),if(TRIM(secInCmp.State)!='',CONCAT(', ',secInCmp.State),''),if(TRIM(secInCmp.Zip)!='',CONCAT(' ',secInCmp.Zip),''),if(TRIM(secInCmp.zip_ext)!='',CONCAT('-',secInCmp.zip_ext),'')) AS secInsCompAddress
		
			FROM insurance_data id ".
		" JOIN insurance_case ic on (id.ins_caseid = ic.ins_caseid and ic.case_status = 'Open') ".
		"LEFT JOIN insurance_case_types ict on (ict.case_id = ic.ins_case_type) ".
		"LEFT JOIN insurance_companies secInCmp ON (secInCmp.id = id.provider AND id.pid = '".$ptId."' ) ".
		" WHERE id.actInsComp = '1' and id.pid = '".$ptId."' and ".($currentCaseVision ? " ict.default_selected = 1 " : "ict.vision = 1" )." and id.type='".strtolower($type)."' ";
		$res = sqlQuery($q);
		if($res!=false){
			
			$visSecInsComp 				= $res["secInsComp"];
			$visSecPolicyNumber 		= $res["secPolicyNumber"];
			$visSecGroupNumber 			= $res["secGroupNumber"];
			$visSecSubscriberName 		= $res["secSubscriberName"];
			$visSecSubscriberRelation 	= $res["secSubscriberRelation"];
			$visSecSubscriberDOB 		= $res["secSubscriberDOB"];
			$visSecSubscriberSS 		= $res["secSubscriberSS"];
			$visSecSubscriberPhone 		= $res["secSubscriberPhone"];
			$visSecSubscriberStreet 	= $res["secSubscriberStreet"];
			$visSecSubscriberCity 		= $res["secSubscriberCity"];
			$visSecSubscriberState 		= $res["secSubscriberState"];
			$visSecSubscriberZip 		= $res["secSubscriberZip"];
			$visSecSubscriberEmployer 	= $res["secSubscriberEmployer"];
			$visSecInsCompName 			= $res["secInsCompName"];
			$visSecInsCompAddress 		= $res["secInsCompAddress"];
			
			$arrRep = array($visSecInsCompName,$visSecPolicyNumber,$visSecGroupNumber,$visSecSubscriberName,$visSecSubscriberRelation,$visSecSubscriberDOB,$visSecSubscriberSS,$visSecSubscriberPhone,$visSecSubscriberStreet,$visSecSubscriberCity,$visSecSubscriberState,$visSecSubscriberZip,$visSecSubscriberEmployer,$secInsCompName,$secPolicyNumber,$secGroupNumber,$secSubscriberName, $secSubscriberRelation,$secSubscriberDOB,$secSubscriberSS,$secSubscriberPhone,$secSubscriberStreet,$secSubscriberCity,$secSubscriberState,$secSubscriberZip,$secSubscriberEmployer);			
		}	
		//Replace
		$str = $this->refineData($str, $this->kVisSecIns, $arrRep, $this->tVisSecIns,"");		
		return $str;	
	} 

	public function clearSumm($str){	
	//Remove last semi colon
	$str = trim($str);
	$ptrn = '/(\s)*\;(\s)*$/i';
	$str = preg_replace($ptrn, "", $str);
	$str=str_replace(array("Comments:"),array(""),$str);
	return $str;
	}	
	
	//============GET PACHYMETRY DATA BASED ON PATIENT DATA AND FORMID============
	private function getPachy($str, $ptId, $formId) {
	
		$pachyOd = $pachyOs = "";	
		if(trim(!empty($ptId)) && trim(!empty($ptId))){
		
			$reading_od = $avg_od = $cor_val_od =  $reading_os = $avg_os = $cor_val_os ="";
			//GET PACHY DATA 
			$sql = "SELECT reading_od, avg_od, cor_val_od, reading_os, avg_os, cor_val_os FROM `chart_correction_values` WHERE patient_id='".$ptId."' AND form_id='".$formId."'";
			//echo $sql;die;		
			$res = imw_query($sql);
			if(imw_num_rows($res)>0){
				$row = imw_fetch_assoc($res);
				
				//FETCH RIGHT EYE(OD) DATA 
				$reading_od = trim($row['reading_od']);
				$avg_od = trim($row['avg_od']);
				$cor_val_od = trim($row['cor_val_od']);
				
				//FETCH LEFT EYE (OS) DATA
				$reading_os = trim($row['reading_os']);
				$avg_os = trim($row['avg_os']);
				$cor_val_os = trim($row['cor_val_os']);
				
				//RIGHT EYE (OD) DATA 
				if(!empty($reading_od) || !empty($avg_od) || !empty($cor_val_od)){ 
					
					$pachyOd.= "<b style='color:blue;'>OD:&nbsp;</b>"; 
						
					if(!empty($reading_od)){ $pachyOd.= $reading_od; }
					if(!empty($avg_od)){ $pachyOd.= '&nbsp;'.$avg_od; }
					if(!empty($cor_val_od)){ $pachyOd.= '&nbsp;'.$cor_val_od; }	
					//echo $pachyOd;
				}
				//LEFT EYE (OS) DATA
				if(!empty($reading_os) || !empty($avg_os) || !empty($cor_val_os)){ 
					
					$pachyOs.= "<b style='color:green;'>OS:&nbsp;</b>"; 
						
					if(!empty($reading_os)){ $pachyOs.= $reading_os; }
					if(!empty($avg_os)){ $pachyOs.= '&nbsp;'.$avg_os; }
					if(!empty($cor_val_od)){ $pachyOs.= '&nbsp;'.$cor_val_os; }	
					//echo $pachyOs;
				}
			}
		
		}
		$arrPachyData = array($pachyOd,$pachyOs);	
		//Replace
		$str = $this->refineData($str, $this->kPachy, $arrPachyData, $this->tPachy,"");
		return $str;
	}	
	//-------------PACHYMETRY DATA WORK ENDS HERE---------------------------
	//-------------GET PATIENT UPCOMING APPOINTMENT INFORMATION-------------
	public function __getApptInfo($str="",$ptId="")
	{	
		if(trim(!empty($ptId)) && trim(!empty($ptId)))
		{
			$doctorName = $doctorLastName = "";
			$facName = $facStreet = $facCity = $facState = $facPostal_code = $faczip_ext = $facPhoneFormat = "";
			$appStrtDate = $appStrtDate_FORMAT = $appSiteShow = $appStrtTime = $appComments = $appcasetypeid = $priProcName = $secProcName = $terProcName = "";
			
			//------GET PATIENT UPCOMING APPOINTMENT-----------------------
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
							sc.sa_patient_id = '".$ptId."'
						AND 
							sc.sa_app_start_date >= CURRENT_DATE( )
						AND 
							sc.sa_app_starttime >= CURRENT_TIME( )
						AND 
							sc.sa_patient_app_status_id NOT IN('18','203')
						ORDER BY 
							sc.sa_app_start_date, sc.sa_app_starttime ASC
						LIMIT 0,1";
			$schDataRow = sqlQuery($schDataQry);
			if($schDataRow!=false)
			{ 	
				$appStrtDate 			= $schDataRow['appStrtDate'];
				$appStrtDate_FORMAT 	= $schDataRow['appStrtDate_FORMAT'];
				$facName 				= $schDataRow['facName'];
				$facStreet 				= $schDataRow['facStreet'];
				$facCity 				= $schDataRow['facCity'];
				$facState 				= $schDataRow['facState'];
				$facPostal_code			= $schDataRow['facPostal_code'];
				$faczip_ext				= $schDataRow['faczip_ext'];
				$facPhone 				= $schDataRow['facPhone'];
				$facPhoneFormat			= $facPhone;
				if(trim($facPhoneFormat)) {
					$facPhoneFormat = str_ireplace("-","",$facPhoneFormat);
					$facPhoneFormat = "(".substr($facPhoneFormat,0,3).") ".substr($facPhoneFormat,3,3)."-".substr($facPhoneFormat,6);
				}
				
				$priProcName 			= $schDataRow['procName'];
				$secProcName 			= $schDataRow['secProcName'];
				$terProcName 			= $schDataRow['terProcName'];
				
				$doctorName 			= $schDataRow['doctorName'];
				$doctorLastName 		= $schDataRow['doctorLastName'];
				
				$appSite 				= ucfirst($schDataRow['appSite']);
				$appSiteShow 			= $appSite;
				if($appSite == "Bilateral") {$appSiteShow="Both"; }
				
				$appStrtTime 			= $schDataRow['appStrtTime'];
				if($appStrtTime[0]=="0") { $appStrtTime = substr($appStrtTime, 1); }

				$appComments 			= $schDataRow['sa_comments'];
				$appComments 			= htmlentities($appComments);
				$appcasetypeid			= $schDataRow['casetypeid'];
				
				//-----------FACILITY ADDRESS VARIABLE CONCATENATION-----------
				if(!empty($facName) && !empty($facCity))
				{
					$facAddress .= $facName.',&nbsp;'.$facCity.',&nbsp;'.$facState.'&nbsp;'.$facPostal_code.'&nbsp;'.$faczip_ext;	
				}
				else if(!empty($facName)&& empty($facCity))
				{
					$facAddress .= $facName.',&nbsp;'.$facState.'&nbsp;'.$facPostal_code.'&nbsp;'.$faczip_ext;	
				}
				else if(!empty($facCity)&& empty($facName))
				{
					$facAddress .= $facCity.',&nbsp;'.$facState.'&nbsp;'.$facPostal_code.'&nbsp;'.$faczip_ext;
				}
				
				$arrApptInfo = array($appStrtDate,$appStrtTime,$doctorName,$facAddress,$priProcName,$secProcName,$terProcName);
				
				/*$arrApptInfo = array($appStrtDate,$appStrtDate_FORMAT,$facName,$facPhoneFormat,$priProcName,$doctorName,$doctorLastName,$appSiteShow,$appStrtTime,$appComments,$facStreet,$facCity,$facState,$facPostal_code,$faczip_ext,$appcasetypeid,$secProcName,$terProcName);*/
			}	
		}
		
		
		//-------REPLACE WORK------------------------------------------------------------
		$str = $this->refineData($str, $this->kApptInfo, $arrApptInfo, $this->tApptInfo,"");
		return $str;
	}	
	
	/*
	|--------------------------------------------------------------------
	| Superbill CPT Modifiers Variable Replacement Work
	|--------------------------------------------------------------------
	|
	| First it will get records based on Workview Superbill CPT Modifiers
	| Secondly it will verify patient charge list table to display cpt 
	| modifiers records.
	| 
	| Finally pass data to refineData function to replace the variable 
	|
	*/
	public function __getCptModifiers($str="",$ptId="",$formId="")
	{	
		if(!empty($ptId) && !empty($formId))
		{
			$date_of_service = $cpt_mod = "";
			//---GET DOS---
			$sql_dos = "SELECT 
						date_of_service
					FROM 
						chart_master_table
					WHERE 
						id='".$formId."' 
					AND 
						patient_id ='".$ptId."' 
					";
			$sql_dos_row = sqlQuery($sql_dos);
			if($sql_dos_row!=false)
			{ 
				$date_of_service = $sql_dos_row['date_of_service'];
			}
			
			//---Get record from superbill table---
			$sql_cpt_mod = "SELECT 
								sb.idSuperBill,
								sb.encounterId,
								pi.cptCode,
								pi.modifier1,
								pi.modifier2,
								pi.modifier3,
								pi.modifier4
							FROM 
								superbill sb
								LEFT JOIN procedureinfo pi ON pi.idSuperBill = sb.idSuperBill 
							WHERE 
								sb.formId='".$formId."' 
							AND 
								sb.patientId ='".$ptId."' 
							AND 
								sb.del_status ='0' 
							AND 
								pi.delete_status ='0' 	
					";
			
			$exe_sql = imw_query($sql_cpt_mod);
			$cpt_mod  = '<table cellpadding="0" border="0" cellspacing="0">';
			$cpt_mod .= '<tr>
									<td style="width:10%"><b>CPT </b></td>
									<td style="width:10%"><b>MOD 1</b></td>
									<td style="width:10%"><b>MOD 2</b></td>
									<td style="width:10%"><b>MOD 3</b></td>
									<td style="width:10%"><b>MOD 4</b></td>
							 </tr>';
			if(imw_num_rows($exe_sql) > 0)
			{ 
				while($res = imw_fetch_assoc($exe_sql))
				{	
				$cpt_mod .= '<tr>
									<td>'.$res['cptCode'].' </td>
									<td>'.$res['modifier1'].' </td>
									<td>'.$res['modifier2'].' </td>
									<td>'.$res['modifier3'].' </td>
									<td>'.$res['modifier4'].' </td>
								</tr>';		
				}
				$cpt_mod .= '</table>'; 
			}
			else
			{
				//---Get record from patient charge list table---
				$sql_cpt_mod = "
							SELECT 
								pcld.modifier_id1,
								pcld.modifier_id2,
								pcld.modifier_id3,
								pcld.modifier_id4,
								cft.cpt_prac_code
							FROM 
								patient_charge_list pcl
								LEFT JOIN patient_charge_list_details pcld ON pcld.charge_list_id = pcl.charge_list_id
								LEFT JOIN cpt_fee_tbl cft ON cft.cpt_fee_id = pcld.procCode								
							WHERE 
								pcl.date_of_service='".$date_of_service."' 
							AND 
								pcl.patient_id ='".$ptId."' 
							AND 
								pcl.del_status ='0' 
							AND 
								pcld.del_status ='0' 								
							";
				$exe_sql = imw_query($sql_cpt_mod);
				if(imw_num_rows($exe_sql) > 0)
				{ 
					while($res = imw_fetch_assoc($exe_sql))
					{	
					$cpt_mod .= '<tr>
										<td>'.$res['cpt_prac_code'].' </td>
										<td>'.$res['modifier_id1'].' </td>
										<td>'.$res['modifier_id2'].' </td>
										<td>'.$res['modifier_id3'].' </td>
										<td>'.$res['modifier_id4'].' </td>
									</tr>';		
					}
					
				}
				else
				{
					$cpt_mod .= '<tr>
									<td colspan="5" style="text-align:center;">No record found </td>
								 </tr>';	
				}	
			}	
			$cpt_mod .= '</table>'; 
			
			$arrCptMod = array($cpt_mod);	
		}
		//-------REPLACE WORK---------------------------------------------------------
		$str = $this->refineData($str, $this->kCptMod, $arrCptMod, $this->tCptMod,"");
		return $str;
	}	
	
	/*
	|--------------------------------------------------------------------
	| Get Patient Actual Arrival Time Information 
	|--------------------------------------------------------------------
	|
	| This will be get through scheduler Id from Previous Status Table
	| It wil be displayed for current appointment only.. neither for 
	| history or for future appointments
	| 
	*/
	public function __pt_actual_arrival_time($str="",$ptId="",$formId="",$section="")
	{
		$date_of_service = $pt_arrival_time = "";
		if(!empty($ptId) && !empty($formId))
		{
			//---GET DOS---
			$sql_dos = "SELECT 
						date_of_service
					FROM 
						chart_master_table
					WHERE 
						id='".$formId."' 
					AND 
						patient_id ='".$ptId."' 
					";
			$sql_dos_row = sqlQuery($sql_dos);
			if($sql_dos_row!=false)
			{ 
				$date_of_service = $sql_dos_row['date_of_service'];
			}
			
			//---GET get appt time based on ---
			$sql_appt_time="SELECT 
								DATE_FORMAT(ps.dateTime,'%h:%i %p') as app_arrival_time
							FROM 
								previous_status ps 
								LEFT JOIN schedule_appointments sa ON sa.id = ps.sch_id 
							WHERE 
								ps.patient_id = '".$ptId."'
							AND 
								sa.sa_app_start_date = '".$date_of_service."'
							AND 
								ps.status = 4
							ORDER BY 
								ps.dateTime ASC
							LIMIT 0,1";
			$row_appt_time = sqlQuery($sql_appt_time);
			if($row_appt_time!=false)
			{ 
				$app_arrival_time = $row_appt_time['app_arrival_time'];
			}
			$pt_arrival_time = array($app_arrival_time);
		}	
		if(!empty($section) && $section == "consent")
		{	
			return $pt_arrival_time;	
		}
		else
		{
			//-------REPLACE WORK---------------------------------------------------------
			$str = $this->refineData($str, $this->kArrTym, $pt_arrival_time, $this->tArrTym,"");	
			return $str;
		}
	}
}
?>