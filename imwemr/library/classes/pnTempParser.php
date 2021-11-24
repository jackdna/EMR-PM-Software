<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

File: pnTempParser.php
Coded in PHP7
Purpose: This class provides Processing functions so that template tags are replaced with correct Pt data.
Access Type : Include file
*/

include_once($GLOBALS['fileroot']."/library/classes/ChartApXml.php");
include_once($GLOBALS['fileroot']."/library/classes/functions_ptInfo.php");
//require_once(dirname(__FILE__)."/../../Medical_history/common/common_functions.php");
//require_once(dirname(__FILE__)."/../common/functions.php");
class PnTempParser extends ChartApXml{	
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
	private $kPachy;

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
	private $tPachy;
			
	function __construct(){
		
		$this->db = $GLOBALS['adodb']['db'];
		
		$this->kPtInfo = array("{PatientID}","{PATIENT NAME TITLE}","{LAST NAME}","{MIDDLE NAME}","{PATIENT FIRST NAME}","{ADDRESS1}","{ADDRESS2}","{PATIENT CITY}","{STATE ZIP CODE}","{DOB}",
								"{REFFERING PHY.}","{MEDICAL DOCTOR}","{REF PHYSICIAN FIRST NAME}","{REF PHYSICIAN LAST NAME}","{PRIMARY PHYSICIAN}","{REF PHY SPECIALITY}",
								"{REF PHY PHONE}","{REF PHY STREET ADDR}","{REF PHY CITY}","{REF PHY STATE}","{REF PHY ZIP}","{REF PHY FAX}","{AGE}","{PCP City}","{PCP State}","{PCP ZIP}","{REF PHYSICIAN TITLE}","{PCP STREET ADDR}","{ADDRESSEE}","{ADDRESSEE_ADDRESS}","{CC1}","{CC1_ADDRESS}","{CC2}","{CC2_ADDRESS}","{CC3}","{CC3_ADDRESS}",
								"{SEX}","{DOB}","{PATIENT SS}","{MARITAL STATUS}","{HOME PHONE}","{EMERGENCY CONTACT}","{EMERGENCY CONTACT PH}","{MOBILE PHONE}","{WORK PHONE}","{PATIENT STATE}","{PATIENT ZIP}","{REGISTRATION DATE}","{POS FACILITY}","{DRIVING LICENSE}",
								"{RES.PARTY TITLE}","{RES.PARTY FIRST NAME}","{RES.PARTY MIDDLE NAME}","{RES.PARTY LAST NAME}","{RES.PARTY DOB}",
								"{RES.PARTY SS}","{RES.PARTY SEX}","{RES.PARTY RELATION}","{RES.PARTY ADDRESS1}","{RES.PARTY ADDRESS2}",
								"{RES.PARTY HOME PH.}","{RES.PARTY WORK PH.}","{RES.PARTY MOBILE PH.}","{RES.PARTY CITY}","{RES.PARTY STATE}",
								"{RES.PARTY ZIP}","{RES.PARTY MARITAL STATUS}","{RES.PARTY DD NUMBER}","{PATIENT OCCUPATION}",
								"{PATIENT EMPLOYER}","{OCCUPATION ADDRESS1}","{OCCUPATION ADDRESS2}","{OCCUPATION CITY}","{OCCUPATION STATE}",
								"{OCCUPATION ZIP}","{MONTHLY INCOME}","{PATIENT INITIAL}","{TIME}","{OPERATOR NAME}","{OPERATOR INITIAL}",
								"{HEARD ABOUT US}","{HEARD ABOUT US DETAIL}","{EMAIL ADDRESS}","{USER DEFINE 1}","{USER DEFINE 2}",
								"{PRIMARY INSURANCE COMPANY}","{PRIMARY POLICY #}","{PRIMARY GROUP #}","{PRIMARY SUBSCRIBER NAME}",
								"{PRIMARY SUBSCRIBER RELATIONSHIP}","{PRIMARY BIRTHDATE}","{PRIMARY SOCIAL SECURITY}","{PRIMARY PHONE}",
								"{PRIMARY ADDRESS}","{PRIMARY CITY}","{PRIMARY STATE}","{PRIMARY ZIP}",
								"{PRIMARY EMPLOYER}","{SECONDARY INSURANCE COMPANY}","{SECONDARY POLICY #}","{SECONDARY GROUP #}",
								"{SECONDARY SUBSCRIBER NAME}","{SECONDARY SUBSCRIBER RELATIONSHIP}","{SECONDARY BIRTHDATE}","{SECONDARY SOCIAL SECURITY}",
								"{SECONDARY PHONE}","{SECONDARY ADDRESS}","{SECONDARY CITY}","{SECONDARY STATE}",
								"{SECONDARY ZIP}","{SECONDARY EMPLOYER}","{PATIENT MRN}","{PATIENT MRN2}","{RACE}","{LANGUAGE}","{ETHNICITY}","{PRI INS ADDR}","{SEC INS ADDR}",
								"{PCP PHY PHONE}", "{PCP PHY FAX}","{PATIENT_SS4}","{LOGGED_IN_FACILITY_NAME}","{LOGGED_IN_FACILITY_ADDRESS}","{PT-KEY}","{CO MANAGED PHY.}"
							  
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
							  	"Heard About Us : ","Heard About Us Detail : ","Patient E-mail : ","User Defined 1 : ","User Defined 2 : ",
								"Primary Insurance company : ","Primary Policy # : ","Primary Group # : ","Primary Subscriber Name : ",
								"Primary Subscriber Relationship : ","Primary Birthdate : ","Primary Social Securtiy : ","Primary Phone : ",
								"Primary Address : ","Primary City : ","Primary State : ","Primary Zip : ",
								"Primary Employer : ","Secondary Insurance company : ","Secondary Policy : ","Secondary Group # : ",
								"Secondary Subscriber Name : ","Secondary Subscriber Relationship : ","Secondary Birthdate : ","Secondary Social Securtiy : ",
								"Secondary Phone : ","Secondary Address : ","Secondary City : ","Secondary State : ",
								"Secondary Zip : ","Secondary Employer : ","Patient MRN : ","Patient MRN2 : ","Race : ","Language : ","Ethnicity : ","Pri. Ins Address : ","Sec. Ins Address : ",
								"PCP Phy Phone :", "PCP Phy Fax :", "Patient Last 4 Digit SS", "","", "Patient Key", "CO MANAGED PHYSICIAN :"
															  
							  
							  );
		$this->kChartLeftCcHx = array("{DOS}","{CC}","{HISTORY}","{PHYSICIAN NAME}","{PHYSICIAN SIGNATURE}","{SITE}","{PHYSICIAN FIRST NAME}","{PHYSICIAN MIDDLE NAME}","{PHYSICIAN LAST NAME}","{PHYSICIAN NAME SUFFIX}","{PHYSICIAN NPI}");
		$this->tChartLeftCcHx = array("Date Of Service : ","CC : ","HISTORY : ","Physician : ","Physician Signature : ","Patient Site : ","Physician First Name : ","Physician Middle Name : ","Physician Last Name : ","Physician Name Suffix : ","PHYSICIAN NPI");
		
		$this->kVision = array("{DISTANCE}","{V-CC-OD}","{V-CC-OS}","{V-SC-OD}","{V-SC-OS}","{NEAR_OD}","{NEAR_OS}","{KERATOMETRY OD}",	"{KERATOMETRY OS}","{V-CC-OU}","{V-SC-OU}","{NEAR_OU}","{PAM_OD}","{PAM_OS}","{PAM_OU}","{BAT_OD}","{BAT_OS}","{BAT_OU}","{AR_OD}","{AR_OS}","{CYCLOPLEGIC_OD}","{CYCLOPLEGIC_OS}");
		$this->tVision = array("Distance : ","Visual Acuity - OD : ","Visual Acuity - OS : ","V-SC-OD :","V-SC-OS :","NEAR OD :","NEAR OS :","KERATOMETRY OD :","KERATOMETRY OS :","VA Corrected- OU : ","VA Uncorrected - OU : ","NEAR OU : ","PAM OD :","PAM OS :","PAM OU :","BAT OD :","BAT OS :","BAT OU :","AUTOREFRACTION OD :","AUTOREFRACTION OS :","CYCLOPLEGIC OD :","CYCLOPLEGIC OS :");	

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
				
		$this->kIop = array("{IOP}", "{IOP OD}","{IOP OS}","{INITIAL IOP}","{INITIAL IOP TIME}","{POST IOP}","{POST IOP TIME}","{IOP OD WITHOUT PACHY}","{IOP OS WITHOUT PACHY}");
		$this->tIop = array("IOP:","IOP OD : ","IOP OS : ","Initial IOP : ","Initial IOP Time : ","Post IOP : ","Post IOP Time : ","IOP OD WITHOUT PACHY : ","IOP OS WITHOUT PACHY: ");
		
		$this->kGonio = array("{GONIO OU}","{GONIO OD}","{GONIO OS}");
		$this->tGonio = array("Gonio OU : ","Gonio OD : ","Gonio OS : ");		

		$this->kSle = array("{SLE OU}","{SLE OD}","{SLE OS}","{SLE}","{CONJ_OD}","{CONJ_OS}","{CORNEA_OD}","{CORNEA_OS}","{ANTCHAMBER_OD}","{ANTCHAMBER_OS}","{LENS_OD}","{LENS_OS}","{IRIS_OD}","{IRIS_OS}");
		$this->tSle = array("SLE OU : ","SLE OD : ","SLE OS : ","SLE : ","CONJ OD : ","CONJ OS : ","CORNEA OD : ","CONRNEA OS : ","ANTCHAMBER OD : ","ANTCHAMBER OS : ","LENS OD : ","LENS OS : ","IRIS OD","IRIS OS");
		
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
		
		$this->kMedHx = array("{OCULAR HISTORY}","{VITAL SIGN}","{OCULAR MEDICATION}","{SYSTEMIC MEDICATION}","{PATIENT ALLERGIES}","{MEDICAL PROBLEM}","{MED HX}","{DIABETES}","{FLASHES}","{FLOATERS}","{SMOKER}","{FAMILY HX SMOKE}","{EMP ID}","{GENERAL HEALTH}");
		$this->tMedHx = array("Ocular History : ","Vital Sign : ","Ocular Medication : ","Systemic Medication : ","Patient Allergies : ","Medical Problem : ","Medical History : ","Diabetes : ","Flashes : ","Floaters : ","Smoker : ","Family Hx Smoke : ","EMP ID : ","GENERAL HEALTH");	
		
		$this->kPachy = array("{PACHYMETRY OD}","{PACHYMETRY OS}");
		$this->tPachy = array("PACHYMETRY OD : ","PACHYMETRY OS : ");	
	
	}
	
	//Check for Key that exits
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
	
	//Refine Data
	private function refineData($str, $arrSrch,$arrRep=array(),$arrTitle,$defRep="Not Performed"){
		
		foreach($arrSrch as $key => $val){
			if(stripos($str,$val) !== false){
				$tmp = (!empty($arrRep[$key])) ? $arrRep[$key] : $defRep;
				
				/*Fix for breaking html tags due to < or > signs in data*/
				$find = array("<OD", "<od", ">OD", ">od", "<OS", "<os", ">OS", ">os");
				$replace = array("< OD", "< od", "> OD", "> od", "< OS", "< os", "> OS", "> os");
				$tmp = str_replace($find,$replace,$tmp);
				/*End Fix for breaking html tags due to < ot > signs in data*/
				
				if(!empty($arrRep[$key])){ //if Values exists then replace
					//==To Remove the Extra </br> in Last Line==// 
					if(substr($tmp,-5)=="</br>" || substr($tmp,-5)=="<br/>"){
						$StrLen=strlen($tmp);
						$tmp=substr($tmp,0,($StrLen-5));
					}
					if(substr($tmp,-6)=="<br />"){
						$StrLen=strlen($tmp);
						$tmp=substr($tmp,0,($StrLen-6));
					}
					//===========================//
					if(strpos($tmp, "&lt;")!==false && strpos($tmp, "&lt; ")===false){ /*Fix Breaking html tags in editor*/
						$tmp = str_replace("&lt;", " &lt; ", $tmp);
					}
					$str = str_ireplace($val,$tmp,$str);
				}else{
					//Check For label and key word to remove both
					$pattern = '/\d.\s/';
					//print_r($arrTitle[$key]);
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
		$resg=imw_query($sql)or die(imw_error());		
		if(imw_num_rows($resg)>0){
			$rowg=imw_fetch_assoc($resg);
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
	private function getPtInfo($str, $ptId,$patientRefTo,$addresseRefToId,$cc1RefToId,$cc2RefToId,$cc3RefToId){
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
			 "patient_data.External_MRN_1, patient_data.External_MRN_2, patient_data.race, patient_data.otherRace, patient_data.language, patient_data.ethnicity, patient_data.otherEthnicity, patient_data.temp_key as pt_key,  patient_data.co_man_phy  as co_managed_phy, ".
			 
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
			 
			 " pos_fac.facilityPracCode AS pos_facility_name ".				 
			 
			 "FROM patient_data ".
			 "LEFT JOIN refferphysician rf ON rf.physician_Reffer_id = patient_data.primary_care_id ".
			 "LEFT JOIN refferphysician pcp ON pcp.physician_Reffer_id = patient_data.primary_care_phy_id ".
			 "LEFT JOIN general_medicine ON general_medicine.patient_id = patient_data.id ".
			 "LEFT JOIN employer_data empdata ON empdata.pid = patient_data.id ".
			 "LEFT JOIN resp_party resp ON resp.patient_id = patient_data.id ".
			 
			 "LEFT JOIN insurance_data priIns ON (priIns.pid = patient_data.id AND priIns.ins_caseid > 0 AND priIns.type = 'primary' AND priIns.actInsComp = 1 AND priIns.provider > 0) ".
			 "LEFT JOIN insurance_companies priInCmp ON (priInCmp.id = priIns.provider AND priIns.pid = patient_data.id) ".

			 "LEFT JOIN insurance_data secIns ON secIns.pid = patient_data.id AND secIns.ins_caseid > 0 AND secIns.type = 'secondary' AND secIns.actInsComp = 1 AND secIns.provider > 0 ".
			 "LEFT JOIN insurance_companies secInCmp ON (secInCmp.id = secIns.provider AND secIns.pid = patient_data.id) ".
			 
			 "LEFT JOIN pos_facilityies_tbl pos_fac ON pos_fac.pos_facility_id = patient_data.default_facility ".
			 "LEFT JOIN users us ON us.id = patient_data.providerID ".			 
			 "WHERE patient_data.id='".$ptId."' ";
		$res = imw_fetch_object($sql);
		if($res !== false){
			//Pt Info			
			$ptAge = "";
			$patientId = $res->fields["id"];
			$title = $res->fields["title"];
			$fname = $res->fields["fname"];
			$lname = $res->fields["lname"];
			$mname = $res->fields["mname"];
			$pt_key= $res->fields["pt_key"];
			$coManagedPhy= $res->fields["co_managed_phy"]; 
			//$dob = $res->UserDate($res->fields["DOB"],'m-d-Y');
			$dob = get_date_format($res->fields["DOB"]);
			$street = $res->fields["street"];
			$street2 = $res->fields["street2"];
			$zip = $res->fields["postal_code"];
			$city = $res->fields["city"];
			$state = $res->fields["state"];
			if(strlen($res->fields['ptAge']) != 4){
				$ptAge = $res->fields["ptAge"];
				if($ptAge != ""){
					$ptAge .= "&nbsp;Yr.";
				}
			}
			$physicianName = $res->fields["physicianName"];
			$ptProID = $res->fields["ptProID"];
			
			//new variable added
			$ptSex 					= $res->fields["sex"];	
			//$dob;
			$ptSS 					= $res->fields["ss"];	
			if(trim($ptSS)!==""){
				$ptss_last4digit	= substr_replace($ptSS,'XXX-XX',0,6); 
			}else{
				$ptss_last4digit="";
			}
			$ptMaritalStatus 		= $res->fields["status"];	
			$ptHomePhone 			= $res->fields["phone_home"];	
			$ptEmergencyContact 	= $res->fields["contact_relationship"];	
			$ptEmergencyContactPhone= $res->fields["phone_contact"];	
			$ptMobilePhone 			= $res->fields["phone_cell"];	
			$ptWorkPhone 			= $res->fields["phone_biz"];
			//$state;	
			//$zip;
			$ptRegDate 				= $res->fields["reg_date"];	
			$pos_facility_name 		= $res->fields["pos_facility_name"];	
			$ptDrivingLicence 		= $res->fields["driving_licence"];
			$pt_occupation 			= $res->fields["pt_occupation"];
			
			$empdata_name 			= $res->fields["empdata_name"];
			$empdata_street 		= $res->fields["empdata_street"];
			$empdata_street2 		= $res->fields["empdata_street2"];
			$empdata_city 			= $res->fields["empdata_city"];
			$empdata_state 			= $res->fields["empdata_state"];
			$empdata_postal_code 	= $res->fields["empdata_postal_code"];

			$monthly_income 		= $res->fields["monthly_income"];
			$patient_initial 		= $res->fields["patient_initial"];
			$currTm					= date('h:i A');
			$heard_abt_us 			= $res->fields["heard_abt_us"];
			$heard_abt_desc 		= $res->fields["heard_abt_desc"];
			$pt_email 				= $res->fields["pt_email"];
			$genericval1 			= $res->fields["genericval1"];
			$genericval2 			= $res->fields["genericval2"];
			$External_MRN_1			= $res->fields["External_MRN_1"];
			$External_MRN_2			= $res->fields["External_MRN_2"];
			
			$raceShow				= trim($res->fields["race"]);
			$otherRace				= trim($res->fields["otherRace"]);
			if($otherRace) { 
				$raceShow			= $otherRace;
			}
			$language				= str_ireplace("Other -- ","",$res->fields["language"]);
			$ethnicityShow			= trim($res->fields["ethnicity"]);			
			$otherEthnicity			= trim($res->fields["otherEthnicity"]);
			if($otherEthnicity) { 
				$ethnicityShow		= $otherEthnicity;
			}
			//==============RESPONSIBLE PARTY DATA REPLACEMENT=============================
			//IF PATIENT HAVE NO RESPONSIBLE PERSON THEN PATIENT DATA WILL BE REPLACED WITH RESPONSIBLE VARIABLES.
			
			if(!empty($res->fields["resp_title"]) || !empty($res->fields["resp_fname"]) || !empty($res->fields["resp_mname"]) || !empty($res->fields["resp_lname"]) || !empty($res->fields["resp_dob"]) || !empty($res->fields["resp_ss"]) || !empty($res->fields["resp_sex"]) || !empty($res->fields[0]['resp_relation']) || !empty($res->fields["resp_address"]) || !empty($res->fields["resp_address2"]) || !empty($res->fields["resp_home_ph"]) || !empty($res->fields["resp_mobile"]) || !empty($res->fields["resp_city"]) || !empty($res->fields["resp_state"]) || !empty($res->fields["resp_zip"]) || !empty($res->fields["resp_marital"]) || !empty($res->fields["resp_licence"])){
				
			  $resp_title 			= $res->fields["resp_title"];
			  $resp_fname 			= $res->fields["resp_fname"];
			  $resp_mname 			= $res->fields["resp_mname"];
			  $resp_lname 			= $res->fields["resp_lname"];
			  $resp_dob 			= $res->fields["resp_dob"];
			  $resp_ss 				= $res->fields["resp_ss"];
			  $resp_sex 			= $res->fields["resp_sex"];
			  $resp_relation 		= $res->fields[0]['resp_relation'];
			  if(strtolower($resp_relation) == "doughter"){
				  $resp_relation = "Daughter";
			  }
			  $resp_address 			= $res->fields["resp_address"];
			  $resp_address2 			= $res->fields["resp_address2"];
			  $resp_home_ph 			= $res->fields["resp_home_ph"];
			  $resp_work_ph 			= $res->fields["resp_work_ph"];
			  $resp_mobile 				= $res->fields["resp_mobile"];
			  $resp_city 				= $res->fields["resp_city"];
			  $resp_state 				= $res->fields["resp_state"];
			  $resp_zip 				= $res->fields["resp_zip"];
			  $resp_marital 			= $res->fields["resp_marital"];
			  $resp_licence 			= $res->fields["resp_licence"];
			
			}else{
			
			  $resp_title 				= $res->fields["title"];
			  $resp_fname 				= $res->fields["fname"];
			  $resp_mname 				= $res->fields["mname"];
			  $resp_lname 				= $res->fields["lname"];
			  $resp_dob 				= get_date_format($res->fields["DOB"]);
			  $resp_ss 					= $res->fields["ss"];
			  $resp_sex 				= $res->fields["sex"];
			  $resp_relation 			= 'Self';
			  $resp_address 			= $res->fields["street"];
			  $resp_address2 			= $res->fields["street2"];
			  $resp_home_ph 			= $res->fields["phone_home"];
			  $resp_work_ph 			= $res->fields["phone_biz"];
			  $resp_mobile 				= $res->fields["phone_cell"];
			  $resp_city 				= $res->fields["city"];
			  $resp_state 				= $res->fields["state"];
			  $resp_zip 				= $res->fields["postal_code"];
			  $resp_marital 			= $res->fields["status"];
			  $resp_licence 			= $res->fields["driving_licence"];
			}
			//============================THE END OF RESPONSIBLE PARTY WORK=======================================
			
			$priInsComp 			= $res->fields["priInsComp"];
			$priPolicyNumber 		= $res->fields["priPolicyNumber"];
			$priGroupNumber 		= $res->fields["priGroupNumber"];
			$priSubscriberName 		= $res->fields["priSubscriberName"];
			$priSubscriberRelation 	= $res->fields["priSubscriberRelation"];
			$priSubscriberDOB 		= $res->fields["priSubscriberDOB"];
			$priSubscriberSS 		= $res->fields["priSubscriberSS"];
			$priSubscriberPhone 	= $res->fields["priSubscriberPhone"];
			$priSubscriberStreet 	= $res->fields["priSubscriberStreet"];
			$priSubscriberCity 		= $res->fields["priSubscriberCity"];
			$priSubscriberState 	= $res->fields["priSubscriberState"];
			$priSubscriberZip 		= $res->fields["priSubscriberZip"];
			$priSubscriberEmployer 	= $res->fields["priSubscriberEmployer"];
			$priInsCompName 		= $res->fields["priInsCompName"];
			$priInsCompAddress 		= $res->fields["priInsCompAddress"];
			$secInsCompAddress 		= $res->fields["secInsCompAddress"];
			
			$secPolicyNumber 		= $res->fields["secPolicyNumber"];
			$secGroupNumber 		= $res->fields["secGroupNumber"];
			$secSubscriberName 		= $res->fields["secSubscriberName"];
			$secSubscriberRelation 	= $res->fields["secSubscriberRelation"];
			$secSubscriberDOB 		= $res->fields["secSubscriberDOB"];
			$secSubscriberSS 		= $res->fields["secSubscriberSS"];
			$secSubscriberPhone 	= $res->fields["secSubscriberPhone"];
			$secSubscriberStreet 	= $res->fields["secSubscriberStreet"];
			$secSubscriberCity 		= $res->fields["secSubscriberCity"];
			$secSubscriberState 	= $res->fields["secSubscriberState"];
			$secSubscriberZip 		= $res->fields["secSubscriberZip"];
			$secSubscriberEmployer 	= $res->fields["secSubscriberEmployer"];
			$secInsCompName 		= $res->fields["secInsCompName"];
			
			
			//--- get operator details --------
			$qryGetUsrData = "select fname,mname,lname from users where id = '".$_SESSION['authId']."'";
			$rsGetUsrData = imw_fetch_object($qryGetUsrData);
			$operator_name = $rsGetUsrData->fields['lname'].', ';
			$operator_name .= $rsGetUsrData->fields['fname'].' ';
			$operator_name .= $rsGetUsrData->fields['mname'];
			$operator_initial = substr($rsGetUsrData->fields['fname'],0,1);
			$operator_initial .= substr($rsGetUsrData->fields['lname'],0,1);
			
			
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
			
			$rp_ptRfPhyId = $res->fields["ptRfPhyId"];
			$rp_ptPCPPhyId = $res->fields["ptPCPPhyId"];
			$rp_title = $res->fields["rp_title"];
			$rf_credential= $res->fields["rf_credential"];
			$rp_fname = $res->fields["FirstName"];
			$rp_mname = $res->fields["MiddleName"];
			$rp_lname = $res->fields["LastName"];
			$rp_add1 = $res->fields["Address1"];
			$rp_add2 = $res->fields["Address2"];
			$rp_zip = $res->fields["ZipCode"];
			$rp_city = $res->fields["rp_city"];
			$rp_fax = $res->fields["rp_fax"];
			$rp_state = $res->fields["rp_state"];
			$rp_specialty = $res->fields["rp_specialty"];
			$rp_physician_phone = core_phone_format($res->fields["rp_physician_phone"]);
			
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
			$addresseeName = trim($rp_name);
			
			$addresseeAddress .= $rp_street_add;
			$addresseeAddress .= (!empty($rp_city)) ? "<br>".$rp_csz : "";
			
			$cc1Title 		= $res->fields["pcp_title"];
			$cc1FName 		= $res->fields["pcp_firstName"];
			$cc1MName 		= $res->fields["pcp_middleName"];
			$cc1LName 		= $res->fields["pcp_lastName"];
			$cc1Address1	= $res->fields["pcp_address1"];
			$cc1Address2 	= $res->fields["pcp_address2"];
			$cc1City 		= $res->fields["pcp_city"];
			$cc1State 		= $res->fields["pcp_state"];
			$cc1ZipCode 	= $res->fields["pcp_zipCode"];
			
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
			$medDoc = $res->fields["med_doctor"];
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
				$rsGetMDData = imw_fetch_object($qryGetMDData);
					$PCP_title = $rsGetMDData->fields["Title"];
					$PCP_fname = $rsGetMDData->fields["FirstName"];
					$PCP_mname = $rsGetMDData->fields["MiddleName"];
					$PCP_lname = $rsGetMDData->fields["LastName"];
					$PCP_add1 = $rsGetMDData->fields["Address1"];
					$PCP_add2 = $rsGetMDData->fields["Address2"];
					$PCP_zip = $rsGetMDData->fields["ZipCode"];
					$PCP_city = $rsGetMDData->fields["City"];
					$PCP_state = $rsGetMDData->fields["State"];
					$PCP_specialty = $rsGetMDData->fields["specialty"];
					$PCP_physician_phone = core_phone_format($rsGetMDData->fields["physician_phone"]);
					$PCP_name ="";
					$PCP_name .= (!empty($PCP_title)) ? "".$PCP_title." " : "" ;
					$PCP_name .= (!empty($PCP_fname)) ? "".$PCP_fname." " : "" ;
					$PCP_name .= (!empty($PCP_mname)) ? "".$PCP_mname." " : "" ;
					$PCP_name .= (!empty($PCP_lname)) ? "".$PCP_lname : "" ;
					if(trim($PCP_fname) && trim($PCP_lname)) {  
						$medDoc = $PCP_fname." ".$PCP_lname." ".$PCP_title;
					}
					$PCP_fax = $rsGetMDData->fields["physician_fax"];
					
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
				$rsGetRefPhyData = imw_fetch_object($qryGetRefPhyData);
				if(($rsGetRefPhyData !== false) && ($rsGetRefPhyData->_numOfRows > 0) ){				
					//Refer Phy
					$rp_title = $rsGetRefPhyData->fields["Title"];
					$rp_fname = $rsGetRefPhyData->fields["FirstName"];
					$rp_mname = $rsGetRefPhyData->fields["MiddleName"];
					$rp_lname = $rsGetRefPhyData->fields["LastName"];
					$rp_add1 = $rsGetRefPhyData->fields["Address1"];
					$rp_add2 = $rsGetRefPhyData->fields["Address2"];
					$rp_zip = $rsGetRefPhyData->fields["ZipCode"];
					$rp_city = $rsGetRefPhyData->fields["City"];
					$rp_fax = $rsGetRefPhyData->fields["physician_fax"];
					$rp_state = $rsGetRefPhyData->fields["State"];
					$rp_specialty = $rsGetRefPhyData->fields["specialty"];
					$rp_credential=$rsGetRefPhyData->fields["credential"];
					$rp_physician_phone = core_phone_format($rsGetRefPhyData->fields["physician_phone"]);
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
					$rp_info .= (!empty($rp_name)) ? $rp_name : "";
					
					$rp_street_add = "";			
					$rp_street_add .= (!empty($rp_add1)) ? $rp_add1 : "";
					$rp_street_add .= (!empty($rp_add2)) ? "<br>".$rp_add2 : "";	
				}
				
			}
			// "rf.Title AS rp_title, rf.FirstName , rf.MiddleName, rf.LastName, ".
			if($addresseRefToId && $addresseRefToId!=$rp_ptRfPhyId) {
				list($addresseTitle,$addresseFName,$addresseMName,$addresseLName,$addresseAddress1,$addresseAddress2,$addresseCity,$addresseState,$addresseZipCode) = $this->getRefPcpInfo($addresseRefToId);
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
			$qry = imw_query("SELECT name, street, city, state, postal_code, zip_ext, phone FROM `facility` WHERE id='".$_SESSION['login_facility']."'");
			if(imw_num_rows($qry)>0){
				  $qryRes = imw_fetch_assoc($qry);
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
			  $loggedfacAddress = $facStreet.', '.$facCity.',&nbsp;'.$facState.'&nbsp;'.$loggedzipcodext.$loggedfacPhone;
			  //=======================ENDS HERE======================================================

			
			//Replace Array
			
			$arrRep = array($patientId,$title,$lname,$mname,$fname,$street,$street2,$city,$ptSt_zip,$dob,
						$rp_info,$medDoc,$rp_fname,$rp_lname,$physicianNameNew,$rp_specialty,$rp_physician_phone,$rp_street_add,$rp_city,
						$rp_state,$rp_zip,$rp_fax,$ptAge,$PCP_city,$PCP_state,$PCP_zip,$rp_title,$PCP_street_add,$addresseeName,$addresseeAddress,$cc1Name,$cc1Address,$cc2Name,$cc2Address,$cc3Name,$cc3Address,
						$ptSex,$dob,$ptSS,$ptMaritalStatus,$ptHomePhone,$ptEmergencyContact,$ptEmergencyContactPhone,$ptMobilePhone,$ptWorkPhone,$state,$zip,$ptRegDate,$pos_facility_name,$ptDrivingLicence,
						$resp_title,$resp_fname,$resp_mname,$resp_lname,$resp_dob,$resp_ss,$resp_sex,$resp_relation,$resp_address,$resp_address2,
						$resp_home_ph,$resp_work_ph,$resp_mobile,$resp_city,$resp_state,$resp_zip,$resp_marital,$resp_licence,$pt_occupation,
						$empdata_name,$empdata_street,$empdata_street2,$empdata_city,$empdata_state,$empdata_postal_code,$monthly_income,
						$patient_initial,$currTm,$operator_name,$operator_initial,$heard_abt_us,$heard_abt_desc,$pt_email,$genericval1,$genericval2,
						$priInsCompName,$priPolicyNumber,$priGroupNumber,$priSubscriberName,$priSubscriberRelation,$priSubscriberDOB,$priSubscriberSS,$priSubscriberPhone,
						$priSubscriberStreet,$priSubscriberCity,$priSubscriberState,$priSubscriberZip,$priSubscriberEmployer,$secInsCompName,$secPolicyNumber,$secGroupNumber,$secSubscriberName,
						$secSubscriberRelation,$secSubscriberDOB,$secSubscriberSS,$secSubscriberPhone,$secSubscriberStreet,$secSubscriberCity,$secSubscriberState,$secSubscriberZip,
						$secSubscriberEmployer,$External_MRN_1,$External_MRN_2,$raceShow,$language,$ethnicityShow,$priInsCompAddress,$secInsCompAddress,
						$PCP_physician_phone, $PCP_fax,$ptss_last4digit,$loggedfacilityname,$loggedfacAddress,$pt_key,$coManagedPhy
						);			
			//echo '<pre>';			
			//print_r($rsGetRefPhyData);
			
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
		$res = imw_fetch_object($sql);
		if($res !== false){
			$reasonText = $reasonOri = $reasonTextCC = "";
			//$dos = $res->UserDate($res->fields["date_of_service"],"m-d-Y");
			$dos = get_date_format($res->fields["date_of_service"]);
			$site = $res->fields["dominant"];
			$finalizeStatus = $res->fields["finalize"];
			$finalizer = $res->fields["finalizerId"];
			$provId = $res->fields["providerId"];
			$site = strtoupper($site);
			if($site=="OD") 	{ $siteShow = "Right"; 
			}elseif($site=="OS"){ $siteShow = "Left"; 
			}elseif($site=="OU"){ $siteShow = "Both"; 
			}
			
			//$pattern = '/\d.\s/';			
			$pattern = '/[\d]+[\.]+[\s]/'; 
			$reasonOri = preg_replace($pattern,"",$res->fields["ccompliant"]);
			
			if(!trim($reasonOri)) { //check if current field is blank show previous field
				$reasonOri = preg_replace($pattern,"",$res->fields["reason"]);
			}
			if($reasonOri == ""){
				$text_data = $this->getCcHx1stLine($ptId);
				list($eyeProbs, $chronicProbs) = $this->getOcularEyeInfo($ptId);
				$rvs = $this->getGenMedInfo($ptId);
				
				$text_data .= $chronicProbs;
				$elem_acuteProbs = ($this->isEyeProbChanged($ptId, $eyeProbs, $elem_acuteProbsPrev)) ? addslashes("".$eyeProbs) : "";
				$text_data .= $elem_acuteProbs; // Add Eye Problems
				
				$text_data .= "".$rvs; // diebates				
				
				$reasonOri = preg_replace("/\s\s/", " ", trim($text_data)); // remove double spaces;
				$reasonOri = preg_replace($pattern,"",$reasonOri);
			}
			$arrReason 		= explode("-",$reasonOri);
			foreach($arrReason as $value){
				   $reasonText .= "-".wordwrap($value, 110);
				   //$reasonText .= wordwrap($value, 100);
			}
			if($reasonText != ""){
				$reasonText =  substr_replace($reasonText,'',0,1);
				//$reason = str_ireplace("-","</br>",$reasonText);
				$reason = $reasonText;
			}
			else{
				//$reason = str_replace("-","</br>",$res->fields["ccompliant"]);
				//$reason = str_ireplace("-","</br>",$reasonOri);
				$reason = $reasonOri;
			}
			
			//reason cc (new field added)
			$reasonCC = preg_replace($pattern,"",$res->fields["reason"]);
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
		$user_type_arr = array(1,7,12);
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
		
		$sqlGetPhysician ="SELECT  CONCAT_WS(' ',us.fname,us.lname) as physicianName,us.fname as usr_fname,us.mname as usr_mname,us.lname as usr_lname,us.pro_title as pro_title,us.pro_suffix as pro_suffix,sign_path,us.user_npi as user_npi 
		from users as us where id='".$provId."'";					
		$rsGetPhysician = imw_fetch_object($sqlGetPhysician);
		if($rsGetPhysician !== false){			
			$physicianName = $rsGetPhysician->fields["physicianName"];
			$usr_fname = $rsGetPhysician->fields["usr_fname"];
			$usr_mname = $rsGetPhysician->fields["usr_mname"];
			$usr_lname = $rsGetPhysician->fields["usr_lname"];
			$pro_title = $rsGetPhysician->fields["pro_title"];
			$pro_suffix = $rsGetPhysician->fields["pro_suffix"];
			$sign_path =  $rsGetPhysician->fields["sign_path"];
			$user_npi = $rsGetPhysician->fields["user_npi"];
			$physicianNameNew = $physicianName;
			//if($pro_title) { $physicianNameNew = $physicianName."&nbsp;".$pro_title; }
			if($pro_suffix) { $physicianNameNew = $physicianName." ".$pro_suffix; }
			//Sign 
			global $gdFilename;
			global $web_RootDirectoryName;
			global $webserver_root;
			$img_path="";
			if($sign_path_remote) {
				$sign_path_remote_decode = urldecode($sign_path_remote);
				$img_path="../../interface/main/uploaddir".$sign_path_remote_decode;
			}else if($sign_path){
				$img_path="../../interface/main/uploaddir".$sign_path;
				if($document_from=='education_instruction' || $document_from=='opnotes' || $document_from=='pt_docs'){
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

			if($img_path){$TData="<img src='".$img_path."' alt=\"alt image\" style=\"width:240px;height:80px;\">";}
			if($_SESSION["logged_user_type"] && !in_array($_SESSION["logged_user_type"],$user_type_arr) && ($document_from=='opnotes' || $document_from=='procedures' || $document_from=='pt_docs')) {
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
		$sql = imw_query("SELECT ".
			 "chart_master_table.date_of_service, ".
			 "chart_master_table.finalize, ".
			 "chart_master_table.finalizerId, ".
			  "chart_master_table.providerId, ".
			 "chart_left_cc_history.ccompliant , ".
			 "chart_left_cc_history.reason , ".
			 "chart_left_cc_history.dominant ".	
			 "FROM chart_master_table ".
			 "LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id ".
			 "WHERE chart_master_table.id='".$formId."' AND chart_master_table.patient_id ='".$ptId."' ");
		
		if($sql && imw_num_rows($sql) > 0){
			$res = imw_fetch_object($sql);
			$reasonText = $reasonOri = $reasonTextCC = "";
			//$dos = $res->UserDate($res->fields["date_of_service"],"m-d-Y");
			$dos = get_date_format($res->fields["date_of_service"],"m-d-Y");
			$site = $res->fields["dominant"];
			$finalizeStatus = $res->fields["finalize"];
			$finalizer = $res->fields["finalizerId"];
			$provId = $res->fields["providerId"];
			$site = strtoupper($site);
			if($site=="OD") 	{ $siteShow = "Right"; 
			}elseif($site=="OS"){ $siteShow = "Left"; 
			}elseif($site=="OU"){ $siteShow = "Both"; 
			}
			
			//$pattern = '/\d.\s/';			
			$pattern = '/[\d]+[\.]+[\s]/'; 
			$reasonOri = preg_replace($pattern,"",$res->fields["ccompliant"]);
			
			if(!trim($reasonOri)) { //check if current field is blank show previous field
				$reasonOri = preg_replace($pattern,"",$res->fields["reason"]);
			}
			if($reasonOri == ""){
				$text_data = $this->getCcHx1stLine($ptId);
				list($eyeProbs, $chronicProbs) = $this->getOcularEyeInfo($ptId);
				$rvs = $this->getGenMedInfo($ptId);
				
				$text_data .= $chronicProbs;
				$elem_acuteProbs = ($this->isEyeProbChanged($ptId, $eyeProbs, $elem_acuteProbsPrev)) ? addslashes("".$eyeProbs) : "";
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
				//$reason = str_replace("-","</br>",$res->fields["ccompliant"]);
				$reason = str_ireplace("-","</br>",$reasonOri);
			}
			
			//reason cc (new field added)
			$reasonCC = preg_replace($pattern,"",$res->fields["reason"]);
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
		$sqlGetPhysician ="SELECT  CONCAT_WS(' ',us.fname,us.lname) as physicianName,us.fname as usr_fname,us.mname as usr_mname,us.lname as usr_lname,us.pro_title as pro_title,us.pro_suffix as pro_suffix 
		from users as us where id='".$provId."'";					
		$rsGetPhysician = imw_fetch_object($sqlGetPhysician);
		if($rsGetPhysician !== false){			
			$physicianName = $rsGetPhysician->fields["physicianName"];
			$usr_fname = $rsGetPhysician->fields["usr_fname"];
			$usr_mname = $rsGetPhysician->fields["usr_mname"];
			$usr_lname = $rsGetPhysician->fields["usr_lname"];
			$pro_title = $rsGetPhysician->fields["pro_title"];
			$pro_suffix = $rsGetPhysician->fields["pro_suffix"];
			
			$physicianNameNew = $physicianName;
			//if($pro_title) { $physicianNameNew = $physicianName."&nbsp;".$pro_title; }
			if($pro_suffix) { $physicianNameNew = $physicianName."&nbsp;".$pro_suffix; }
			//Sign 
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
				c2.k_od, c2.slash_od, c2.x_od, c2.k_os, c2.slash_os, c2.x_os, c2.k_type,c2.ex_desc AS k_desc,
				c3.txt1_od, c3.txt2_od, c3.txt1_os, c3.txt2_os, c3.txt1_ou, c3.txt2_ou,  
				c4.nl_od, c4.l_od, c4.m_od, c4.h_od, c4.nl_os, c4.l_os, c4.m_os, c4.h_os, c4.nl_ou, c4.l_ou, c4.m_ou, c4.h_ou
			FROM chart_vis_master c1
			LEFT JOIN chart_ak c2 ON c2.id_chart_vis_master = c1.id
			LEFT JOIN chart_pam c3 ON c3.id_chart_vis_master = c1.id
			LEFT JOIN chart_bat c4 ON c4.id_chart_vis_master = c1.id
			WHERE c1.form_id = '".$formId."' AND c1.patient_id = '".$ptId."' 
		";
		$row = sqlQuery($sql);
		if($row!=false){
			$statusElements = $row["status_elements"];
			//================PAM OD/OS/OU DATA===============
			$pam_od_txt_1 = $res["txt1_od"];
			$pam_od_txt_2 = $res["txt2_od"];
			
			$pam_os_txt_1 = $res["txt1_os"];
			$pam_os_txt_2 = $res["txt2_os"];
			
			$pam_ou_txt_1 = $res["txt1_ou"];
			$pam_ou_txt_2 = $res["txt2_ou"];
			//================BAT OD/OS/OU DATA===============
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
			//===========AUTOREFRACTION OD/OS DATA============
			
			//===========KERATOMETRY READINGS WORKS START HERE========================
			
			//====RIGHT EYE(OD) DATA WORK STARTS FROM HERE=============
			$vis_ak_od_k = trim($res["k_od"]);
			$vis_ak_od_slash = trim($res["slash_od"]);
			$vis_ak_od_x = trim($res["x_od"]);
			
			if(!empty($vis_ak_od_k) || !empty($vis_ak_od_slash) || !empty($vis_ak_od_x)){
				
				$KRTOD.= "<b style='color:blue;'>OD:&nbsp;</b>"; 
				
				if($vis_ak_od_k){ $KRTOD.= $vis_ak_od_k; }
				if($vis_ak_od_slash){ $KRTOD.= '/'.$vis_ak_od_slash; }
				if($vis_ak_od_x){ $KRTOD.= '&nbsp;<b>X</b>'.$vis_ak_od_x; }
			}
			//=====RIGHT EYE WORKS ENDS HERE=====
			
			//LEFT EYE(OS) DATA			
			$vis_ak_os_k = trim($res["k_os"]);
			$vis_ak_os_slash = trim($res["slash_os"]);
			$vis_ak_os_x = trim($res["x_os"]);
			
			if(!empty($vis_ak_os_k) || !empty($vis_ak_os_slash) || !empty($vis_ak_os_x)){
				
				$KRTOS.= "<b style='color:green;'>OS:&nbsp;</b>"; 
				
				if($vis_ak_os_k){ $KRTOS.= $vis_ak_os_k; }
				if($vis_ak_os_slash){ $KRTOS.= '/'.$vis_ak_os_slash; }
				if($vis_ak_os_x){ $KRTOS.= '&nbsp;<b>X</b>'.$vis_ak_os_x; }
			}
			//LEFT EYE WORKS ENDS HERE
			
			
			//===========BAT OD/OS/OU WORKS=========================
			$batODTxt = $batOSTxt = $batOUTxt = "";
			//================BAT OD WORKS==========================
			if((!empty($bat_nl_od) && ($bat_nl_od != "20/")) && (strpos($statusElements, "elem_visBatNlOd=1") !== false) || (!empty($bat_low_od) && ($bat_low_od != "20/") && (strpos($statusElements, "elem_visBatLowOd=1") !== false)) || (!empty($bat_med_od) && ($bat_med_od != "20/") && (strpos($statusElements, "elem_visBatMedOd=1") !== false)) || (!empty($bat_high_od) && ($bat_high_od != "20/") && (strpos($statusElements, "elem_visBatHighOd=1") !== false))){
				
			  //$batODTxt.= "<b style='color:blue;'>OD:&nbsp;</b>"; 
			  if($bat_nl_od){  $batODTxt.= '<b>&nbsp;NL&nbsp;</b>'.$bat_nl_od;}
			  if($bat_low_od){ $batODTxt.= '<b>&nbsp;L&nbsp;</b>'.$bat_low_od;}
			  if($bat_med_od){ $batODTxt.= '<b>&nbsp;M&nbsp;</b>'.$bat_med_od;}
			  if($bat_high_od){ $batODTxt.= '<b>&nbsp;H&nbsp;</b>'.$bat_high_od;}
			}
			
			//================BAT OS WORKS==========================
			if((!empty($bat_nl_os) && ($bat_nl_os != "20/")) && (strpos($statusElements, "elem_visBatNlOs=1") !== false) || (!empty($bat_low_os) && ($bat_low_os != "20/") && (strpos($statusElements, "elem_visBatLowOs=1") !== false)) || (!empty($bat_med_os) && ($bat_med_os != "20/") && (strpos($statusElements, "elem_visBatMedOs=1") !== false)) || (!empty($bat_high_os) && ($bat_high_os != "20/") && (strpos($statusElements, "elem_visBatHighOs=1") !== false))){
			  //$batOSTxt.= "<b style='color:green;'>OS:&nbsp;</b>"; 
			  if($bat_nl_os){  $batOSTxt.= '<b>&nbsp;NL&nbsp;</b>'.$bat_nl_os;}
			  if($bat_low_os){ $batOSTxt.= '<b>&nbsp;L&nbsp;</b>'.$bat_low_os;}
			  if($bat_med_os){ $batOSTxt.= '<b>&nbsp;M&nbsp;</b>'.$bat_med_os;}
			  if($bat_high_os){ $batOSTxt.= '<b>&nbsp;H&nbsp;</b>'.$bat_high_os;}
			}
			//================BAT OU WORKS==========================
			if((!empty($bat_nl_ou) && ($bat_nl_ou != "20/")) && (strpos($statusElements, "elem_visBatNlOu=1") !== false) || (!empty($bat_low_ou) && ($bat_low_ou != "20/") && (strpos($statusElements, "elem_visBatLowOu=1") !== false)) || (!empty($bat_med_ou) && ($bat_med_ou != "20/") && (strpos($statusElements, "elem_visBatMedOu=1") !== false)) || (!empty($bat_high_ou) && ($bat_high_ou != "20/") && (strpos($statusElements, "elem_visBatHighOu=1") !== false))){
			 // $batOUTxt.= "<b style='color:#9900cc;'>OU:&nbsp;</b>"; 
			  if($bat_nl_ou){  $batOUTxt.= '<b>&nbsp;NL&nbsp;</b>'.$bat_nl_ou;}
			  if($bat_low_ou){ $batOUTxt.= '<b>&nbsp;L&nbsp;</b>'.$bat_low_ou;}
			  if($bat_med_ou){ $batOUTxt.= '<b>&nbsp;M&nbsp;</b>'.$bat_med_ou;}
			  if($bat_high_ou){ $batOUTxt.= '<b>&nbsp;H&nbsp;</b>'.$bat_high_ou;}
			}
			
			//===========PAM OD/OS/OU WORKS=========================
			$pamODTxt = $pamOSTxt = $pamOUTxt = "";
			//===========PAM OD WORKS=========================
			if((!empty($pam_od_txt_1) && ($pam_od_txt_1 != "20/")) && (strpos($statusElements, "elem_visPamOdTxt1=1") !== false) || (!empty($pam_od_txt_2 ) && ($pam_od_txt_2 != "20/") && (strpos($statusElements, "elem_visPamOdTxt2=1") !== false))){
				
			  //$pamODTxt.= "<b style='color:blue;'>OD:&nbsp;</b>"; 
			  if($pam_od_txt_1 && $pam_od_txt_2){ $pamODTxt.=  $pam_od_txt_1.'&nbsp;-&nbsp;'. $pam_od_txt_2; }
			  else if($pam_od_txt_1){ $pamODTxt.= $pam_od_txt_1; } 
			  else if($pam_od_txt_2){ $pamODTxt.= $pam_od_txt_2; }
			}
			//===========PAM OS WORKS=========================
			if((!empty($pam_os_txt_1) && ($pam_os_txt_1 != "20/")) && (strpos($statusElements, "elem_visPamOsTxt1=1") !== false) || (!empty($pam_os_txt_2 ) && ($pam_os_txt_2 != "20/") && (strpos($statusElements, "elem_visPamOsTxt2=1") !== false))){
				
			  //$pamOSTxt.= "<b style='color:green;'>OS:&nbsp;</b>"; 
			  if($pam_os_txt_1 && $pam_os_txt_2){ $pamOSTxt.=  $pam_os_txt_1.'&nbsp;-&nbsp;'. $pam_os_txt_2;} 
			  else if($pam_os_txt_1){ $pamOSTxt.= $pam_os_txt_1; }
			  else if($pam_os_txt_2){ $pamOSTxt.= $pam_os_txt_2; } 
			}
			//===========PAM OU WORKS=========================
			if((!empty($pam_ou_txt_1) && ($pam_ou_txt_1 != "20/")) && (strpos($statusElements, "elem_visPamOuTxt1=1") !== false) || (!empty($pam_ou_txt_2) && ($pam_ou_txt_2 != "20/") && (strpos($statusElements, "elem_visPamOuTxt2=1") !== false))){
				
			 //$pamOUTxt.= "<b style='color:#9900cc;'>OU:&nbsp;</b>"; 
			  if($pam_ou_txt_1 && $pam_ou_txt_2){ $pamOUTxt.=  $pam_ou_txt_1.'&nbsp;-&nbsp;'. $pam_ou_txt_1; }
			  else if($pam_ou_txt_1){ $pamOUTxt.= $pam_ou_txt_1; }
			  else if($pam_ou_txt_2){ $pamOUTxt.= $pam_ou_txt_2; }
			}		
		}
		
		//--
		$flg_acuity=0;
		$sql = "
			SELECT sec_name, sec_indx,
				sel_od, sel_os, txt_od, txt_os, sel_ou, txt_ou
			FROM `chart_vis_master` c1
			LEFT JOIN chart_acuity c2 ON c1.id = c2.id_chart_vis_master
			WHERE c1.form_id = '".$formId."' AND c1.patient_id = '".$ptId."'
			AND (sec_name = 'Distance' OR sec_name = 'Near')
			AND sec_indx IN (1,2)
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
			}else if($row["sec_name"] == "Near" && $row["sec_indx"]=="1"){				
				$NearOdSel1 = $row["sel_od"];
				$NearOsSel1 = $row["sel_os"];
				$NearOdTxt1 = $row["txt_od"];
				$NearOsTxt1 = $row["txt_os"]; 
				$NearOuSel1 = $row["sel_ou"];
				$NearOuTxt1 = $row["txt_ou"];
			}
		}
		
		if(!empty($flg_acuity)){			
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
			   //===============================================
				
				//Replace Array
				//print_r($VCCOD);
				$CCOD=$CCOS=$SCOD=$SCOS= $CCOU = $SCOU ='';
				if((strpos($VCCOD, 'SC')!==false && strpos($VCCOS, 'SC')!==false) &&  (strpos($VSCOD, 'CC')!==false && strpos($VSCOS, 'CC')!==false)){
					$SCOD=$VCCOD; $SCOS=$VCCOS;
					$CCOD=$VSCOD; $CCOS=$VSCOS;
				}else if((strpos($VCCOD, 'CC')!==false && strpos($VCCOS, 'CC')!==false) &&  (strpos($VSCOD, 'SC')!==false && strpos($VSCOS, 'SC')!==false)){
					$CCOD=$VCCOD; $CCOS=$VCCOS;
					$SCOD=$VSCOD; $SCOS=$VSCOS;
				}else{
					$CCOD=$VCCOD; $CCOS=$VCCOS;
					$SCOD=$VSCOD; $SCOS=$VSCOS;
				}
			
				//==========OU DATA ARRANGEMENT============
				
				if(strpos($VCCOU, 'CC')!==false){
					$CCOU=$VCCOU; $SCOU=$VSCOU; 
				}else if(strpos($VSCOU, 'CC')!==false){
					$CCOU=$VSCOU; $SCOU=$VCCOU; 
				}else if(strpos($VSCOU, 'CC')!==false){
					$CCOU=$VSCOU; $SCOU=$VCCOU; 
				}else if(strpos($VSCOU, 'CC')!==false){
					$CCOU=$VSCOU; $SCOU=$VCCOU; 
				}
				
				
				/*if((strpos($CCOD, 'PH')!==false) || (strpos($CCOD, 'GL')!==false)){ $CCOD = "";}
				if((strpos($CCOS, 'PH')!==false) || (strpos($CCOS, 'GL')!==false)){ $CCOS = "";}
				if((strpos($CCOU, 'PH')!==false) || (strpos($CCOU, 'GL')!==false) || (strpos($CCOU, 'GPCL')!==false)){ $CCOU = "";}
				if((strpos($SCOD, 'PH')!==false) || (strpos($SCOD, 'GL')!==false)){ $SCOD = "";}
				if((strpos($SCOS, 'PH')!==false) || (strpos($SCOS, 'GL')!==false)){ $SCOS = "";}
				if((strpos($SCOU, 'PH')!==false) || (strpos($SCOU, 'GL')!==false) || (strpos($SCOU, 'GPCL')!==false)){ $SCOU = "";}*/
				
			}
			
			//================NEAR SECTION VALUE WORK STARTS HERE=====================
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
				
			}
			//=========================ENDS HERE======================================
		}
		
		$flg_sca=0;
		$sql = "
			SELECT sec_name,
				s_od, c_od, a_od, sel_od, s_os, c_os, a_os, sel_os 
			FROM `chart_vis_master` c1
			LEFT JOIN chart_sca c2 ON c1.id = c2.id_chart_vis_master
			WHERE c1.form_id = '".$formId."' AND c1.patient_id = '".$ptId."'
			AND (sec_name = \"AR\" OR sec_name = \"ARC\")			
		";
		$rez = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($rez);$i++){
			$flg_sca=1;
			if($row["sec_name"] == "AR"){
				//===========AUTOREFRACTION OD/OS DATA============
				$ar_od_s = $res["s_od"];
				$ar_od_c = $res["c_od"];
				$ar_od_a = $res["a_od"];
				$ar_od_sel_1 = $res["sel_od"];
				
				$ar_os_s = $res["s_os"];
				$ar_os_c = $res["c_os"];
				$ar_os_a = $res["a_os"];
				$ar_os_sel_1 = $res["sel_os"];
			}else if($row["sec_name"] == "ARC"){
				//============CYCLOPLEGIC OD/OS DATA==============
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
			
			//==============AUTOREFRACTION OD/OS WORKS=========================
			$arOD = $arOS = "";
			//================AUTOREFRACTION OD WORKS==========================
			if((!empty($ar_od_s)) && (strpos($statusElements, "elem_visArOdS=1") !== false) || (!empty($ar_od_c) && (strpos($statusElements, "elem_visArOdC=1") !== false)) || (!empty($ar_od_a) && (strpos($statusElements, "elem_visArOdA=1") !== false)) || (!empty($ar_od_sel_1) && (strpos($statusElements, "elem_visArOdSel1=1") !== false))){
				
			  //$arOD.= "<b style='color:blue;'>OD:&nbsp;</b>"; 
			  if($ar_od_s){ $arOD.= '<b>&nbsp;S&nbsp;</b>'.$ar_od_s;}
			  if($ar_od_c){ $arOD.= '<b>&nbsp;C&nbsp;</b>'.$ar_od_c;}
			  if($ar_od_a){ $arOD.= '<b>&nbsp;A&nbsp;</b>'.$ar_od_a;}
			  if($ar_od_sel_1){ $arOD.= '<b>&nbsp;</b>'.$ar_od_sel_1;}
			}
			//================AUTOREFRACTION OS WORKS==========================
			if((!empty($ar_os_s)) && (strpos($statusElements, "elem_visArOsS=1") !== false) || (!empty($ar_os_c) && (strpos($statusElements, "elem_visArOsC=1") !== false)) || (!empty($ar_os_a) && (strpos($statusElements, "elem_visArOsA=1") !== false)) || (!empty($ar_os_sel_1) && (strpos($statusElements, "elem_visArOsSel1=1") !== false))){
			 // $arOS.= "<b style='color:green;'>OS:&nbsp;</b>"; 
			  if($ar_os_s){ $arOS.= '<b>&nbsp;S&nbsp;</b>'.$ar_os_s;}
			  if($ar_os_c){ $arOS.= '<b>&nbsp;C&nbsp;</b>'.$ar_os_c;}
			  if($ar_os_a){ $arOS.= '<b>&nbsp;A&nbsp;</b>'.$ar_os_a;}
			  if($ar_os_sel_1){ $arOS.= '<b>&nbsp;</b>'.$ar_os_sel_1;}
			}
			
			//=================CYCLOPLEGIC OD/OS WORKS=========================
			$CycAROD = $CycAROS = "";
			//==================CYCLOPLEGIC OD WORKS===========================
			if((!empty($CycArOdS)) && (strpos($statusElements, "elem_visCycArOdS=1") !== false) || (!empty($CycArOdC) && (strpos($statusElements, "elem_visCycArOdC=1") !== false)) || (!empty($CycArOdA) && (strpos($statusElements, "elem_visCycArOdA=1") !== false)) || (!empty($CycArOdSel1) && (strpos($statusElements, "elem_visCycArOdSel1=1") !== false))){
				
			 // $CycAROD.= "<b style='color:blue;'>OD:&nbsp;</b>"; 
			  if($CycArOdS){ $CycAROD.= '<b>&nbsp;S&nbsp;</b>'.$CycArOdS;}
			  if($CycArOdC){ $CycAROD.= '<b>&nbsp;C&nbsp;</b>'.$CycArOdC;}
			  if($CycArOdA){ $CycAROD.= '<b>&nbsp;A&nbsp;</b>'.$CycArOdA;}
			  if($CycArOdSel1){ $CycAROD.= '<b>&nbsp;</b>'.$CycArOdSel1;}
			}
			
			//==================CYCLOPLEGIC OS WORKS===========================
			if((!empty($CycArOsS)) && (strpos($statusElements, "elem_visCycArOsS=1") !== false) || (!empty($CycArOsC) && (strpos($statusElements, "elem_visCycArOsC=1") !== false)) || (!empty($CycArOsA) && (strpos($statusElements, "elem_visCycArOsA=1") !== false)) || (!empty($CycArOsSel1) && (strpos($statusElements, "elem_visCycArOsSel1=1") !== false))){
			 // $CycAROS.= "<b style='color:green;'>OS:&nbsp;</b>"; 
			  if($CycArOsS){ $CycAROS.= '<b>&nbsp;S&nbsp;</b>'.$CycArOsS;}
			  if($CycArOsC){ $CycAROS.= '<b>&nbsp;C&nbsp;</b>'.$CycArOsC;}
			  if($CycArOsA){ $CycAROS.= '<b>&nbsp;A&nbsp;</b>'.$CycArOsA;}
			  if($CycArOsSel1){ $CycAROS.= '<b>&nbsp;</b>'.$CycArOsSel1;}
			}
			//=================================================================
			
		}else{
			$arOD = $arOS = "";
			$CycAROD = $CycAROS = "";
		}
		
		if(!empty($flg_sca) && !empty($flg_acuity)){
			$arrRep = array($disOD.$disOS,$CCOD,$CCOS,$SCOD,$SCOS,$NearOD,$NearOS,$KRTOD,$KRTOS,$CCOU,$SCOU,$NearOU,$pamODTxt,$pamOSTxt,$pamOUTxt,$batODTxt,$batOSTxt,$batOUTxt,$arOD,$arOS,$CycAROD,$CycAROS);
		}
		
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
		$res = imw_fetch_object($sql);
		if($res !== false){
			$wnlString = !empty($res->fields["wnl_value"]) ? $res->fields["wnl_value"] : getExamWnlStr("Pupil", $ptId, $formId);
			$pupilOd = ($res->fields["wnlPupilOd"] == 0) ? $res->fields["sumOdPupil"] : $wnlString;
			$pupilOs = ($res->fields["wnlPupilOs"] == 0) ? $res->fields["sumOsPupil"] : $wnlString;
			
			$pupilOd = (!empty($pupilOd)) ? $this->tOd.$pupilOd : "";
			$pupilOs = (!empty($pupilOs)) ? $this->tOs.$pupilOs : "";
			$pupilOu = "";
			$pupilOu .= (!empty($pupilOd)) ? $pupilOd : ""; //$this->tOd."Not Performed";
			$pupilOu .= (!empty($pupilOd)) ? $pupilOd : ""; //$this->tOs."Not Performed";
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
		$res = imw_fetch_object($sql);
		if($res !== false){
			$wnlString = !empty($res->fields["wnl_value"]) ? $res->fields["wnl_value"] : getExamWnlStr("External", $ptId, $formId);
			$sumOd = ($res->fields["wnlEeOd"] == 0) ? $res->fields["external_exam_summary"] : $wnlString;
			$sumOs = ($res->fields["wnlEeOs"] == 0) ? $res->fields["sumOsEE"] : $wnlString;
			
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
			 
		$res = imw_fetch_object($sql);
		if($res !== false){
			$oCn = new ChartNote($ptId, $formId);
			$wnlStringLids = !empty($res->fields["wnl_value_Lids"]) ? $res->fields["wnl_value_Lids"] : getExamWnlStr("Lids", $ptId, $formId);
			$wnlStringLidPos = !empty($res->fields["wnl_value_LidPos"]) ? $res->fields["wnl_value_LidPos"] :  getExamWnlStr("Lid Position", $ptId, $formId);
			$wnlStringLesion = !empty($res->fields["wnl_value_Lesion"]) ? $res->fields["wnl_value_Lesion"] :  getExamWnlStr("Lesion", $ptId, $formId);
			$wnlStringLac = !empty($res->fields["wnl_value_LacSys"]) ? $res->fields["wnl_value_LacSys"] :  getExamWnlStr("Lacrimal System", $ptId, $formId);
			
			$sumLidsOd = ($res->fields["wnlLidsOd"] == 0) ? $res->fields["lid_conjunctiva_summary"] : $wnlStringLids;
			$sumLidPosOd = ($res->fields["wnlLidPosOd"] == 0) ? $res->fields["lid_deformity_position_summary"] : $wnlStringLidPos;
			$sumLesionOd = ($res->fields["wnlLesionOd"] == 0) ? $res->fields["lesion_summary"] : $wnlStringLesion;
			$sumLacOd = ($res->fields["wnlLacSysOd"] == 0) ? $res->fields["lacrimal_system_summary"] : $wnlStringLac;
			
			$sumLidsOs = ($res->fields["wnlLidsOs"] == 0) ? $res->fields["sumLidsOs"] : $wnlStringLids;
			$sumLidPosOs = ($res->fields["wnlLidPosOs"] == 0) ? $res->fields["sumLidPosOs"] : $wnlStringLidPos ;
			$sumLesionOs = ($res->fields["wnlLesionOs"] == 0) ? $res->fields["sumLesionOs"] : $wnlStringLesion;
			$sumLacOs = ($res->fields["wnlLacSysOs"] == 0) ? $res->fields["sumLacOs"] : $wnlStringLac;
			
			$arrOd = array();
			$arrOs = array();			
			if(!empty($sumLidsOd)){
				$arrOd[] = "<b>Lids</b>&nbsp;".$sumLidsOd."</br>";
			}
			
			if(!empty($sumLidsOs)){
				$arrOs[] = "<b>Lids</b>&nbsp;".$sumLidsOs."</br>";
			}
			
			if(!empty($sumLesionOd)){
				$arrOd[] = "<b>Lesion</b>&nbsp;".$sumLesionOd."</br>";
			}			
			
			if(!empty($sumLesionOs)){
				$arrOs[] = "<b>Lesion</b>&nbsp;".$sumLesionOs."</br>";
			}			
			
			if(!empty($sumLidPosOd)){
				$arrOd[] = "<b>Lid Position</b>&nbsp;".$sumLidPosOd."</br>";
			}			
			
			if(!empty($sumLidPosOs)){
				$arrOs[] = "<b>Lid Position</b>&nbsp;".$sumLidPosOs."</br>";
			}
			
			if(!empty($sumLacOd)){
				$arrOd[] = "<b>Lacrimal</b>&nbsp;".$sumLacOd."</br>";
			}
			
			if(!empty($sumLacOs)){
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
				  c5.wnl_value_LacSys 
				  c5.lacrimal_system_summary, 
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
			 
		$res = imw_fetch_object($sql);
		if($res !== false){
			$oCn = new ChartNote($ptId, $formId);
			
			$wnlStringLids = !empty($res->fields["wnl_value_Lids"]) ? $res->fields["wnl_value_Lids"] : getExamWnlStr("Lids", $ptId, $formId);
			$wnlStringLidPos = !empty($res->fields["wnl_value_LidPos"]) ? $res->fields["wnl_value_LidPos"] :  getExamWnlStr("Lid Position", $ptId, $formId);
			$wnlStringLesion = !empty($res->fields["wnl_value_Lesion"]) ? $res->fields["wnl_value_Lesion"] :  getExamWnlStr("Lesion", $ptId, $formId);
			$wnlStringLac = !empty($res->fields["wnl_value_LacSys"]) ? $res->fields["wnl_value_LacSys"] :  getExamWnlStr("Lacrimal System", $ptId, $formId);
			
			$sumLidsOd = ($res->fields["wnlLidsOd"] == 0) ? $res->fields["lid_conjunctiva_summary"] : $wnlStringLids;
			$sumLidPosOd = ($res->fields["wnlLidPosOd"] == 0) ? $res->fields["lid_deformity_position_summary"] : $wnlStringLidPos;
			$sumLesionOd = ($res->fields["wnlLesionOd"] == 0) ? $res->fields["lesion_summary"] : $wnlStringLesion;
			$sumLacOd = ($res->fields["wnlLacSysOd"] == 0) ? $res->fields["lacrimal_system_summary"] : $wnlStringLac;
			
			$sumLidsOs = ($res->fields["wnlLidsOs"] == 0) ? $res->fields["sumLidsOs"] : $wnlStringLids;
			$sumLidPosOs = ($res->fields["wnlLidPosOs"] == 0) ? $res->fields["sumLidPosOs"] : $wnlStringLidPos ;
			$sumLesionOs = ($res->fields["wnlLesionOs"] == 0) ? $res->fields["sumLesionOs"] : $wnlStringLesion;
			$sumLacOs = ($res->fields["wnlLacSysOs"] == 0) ? $res->fields["sumLacOs"] : $wnlStringLac;
			
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
		$res = imw_fetch_object($sql);
		if($res !== false){
			$sumOd 				= $res->fields["sumOdIop"];
			$sumOs 				= $res->fields["sumOsIop"];
			$trtOd 				= $res->fields["trgtOd"];
			$trtOs 				= $res->fields["trgtOs"];
			$multiple_pressure 	= $res->fields["multiple_pressure"];
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
				
				//initial iop
				$initial_iop = '<table cellpadding="0" border="0" cellspacing="0">';
				if($elem_appOd || $elem_appOs) {		
					$initial_iop .= '<tr>
										<td class="text_10b">T<sub>A</sub></td>
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
										<td class="text_10b">T<sub>p</sub></td>
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
										<td class="text_10b">T<sub>x</sub></td>
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
											<td class="text_10b">T<sub>A:</sub></td>
											<td class="text_10">'.$elem_appTime.'</td>
										</tr>';
				}
				if($elem_puffTime) {		
					$initial_iop_time .= '<tr>
											<td class="text_10b">T<sub>p:</sub></td>
											<td class="text_10">'.$elem_puffTime.'</td>
										</tr>';
				}
				if($elem_xTime) {		
					$initial_iop_time .= '<tr>
											<td class="text_10b">T<sub>x:</sub></td>
											<td class="text_10">'.$elem_xTime.'</td>
										</tr>';
				}
				$initial_iop_time .= '</table>';
			
				//post iop
				$post_iop = '<table cellpadding="0" border="0" cellspacing="0">';
				if($post_elem_appOd || $post_elem_appOs) {		
					$post_iop .= '<tr>
										<td class="text_10b">T<sub>A</sub></td>
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
										<td class="text_10b">T<sub>p</sub></td>
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
										<td class="text_10b">T<sub>x</sub></td>
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
											<td class="text_10b">T<sub>A:</sub></td>
											<td class="text_10">'.$post_elem_appTime.'</td>
										</tr>';
				}
				if($post_elem_puffTime) {		
					$post_iop_time .= '<tr>
											<td class="text_10b">T<sub>p:</sub></td>
											<td class="text_10">'.$post_elem_puffTime.'</td>
										</tr>';
				}
				if($post_elem_xTime) {		
					$post_iop_time .= '<tr>
											<td class="text_10b">T<sub>x:</sub></td>
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
			
			//Replace Array
			$arrRep = array(trim($sum),trim($sumOd),trim($sumOs),$initial_iop,$initial_iop_time,$post_iop,$post_iop_time,trim($IOPOdWithoutPachy),trim($IOPOsWithoutPachy));
		}
		//Replace
		$str = $this->refineData($str, $this->kIop, $arrRep, $this->tIop,"");
		return $str;
	}
	
	//Get Gonio
	private function getGonio($str, $ptId, $formId){
		$sql = "SELECT gonio_od_summary, gonio_os_summary FROM chart_gonio WHERE form_id = '".$formId."' AND patient_id = '".$ptId."' AND purged='0'  ";
		$res = imw_fetch_object($sql);
		if($res !== false){
			$sumOd = $res->fields["gonio_od_summary"];
			$sumOs = $res->fields["gonio_os_summary"];
			
			$sumOd = (!empty($sumOd)) ? $this->tOd." ".$sumOd : "";
			$sumOs = (!empty($sumOs)) ? $this->tOs." ".$sumOs : "";
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
		$res = imw_fetch_object($sql);
		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv For Merged NSE Server
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
			if($res == false){
				$sql = str_replace("chart_slit_lamp_exam","chart_slit_lamp_exam_archive",$sql);
				$res=imw_fetch_object($sql);	
			}
		}
		//-------- End ----------------------------------------------------------------------------------	
		if($res !== false){
			$strCONJOD = $strCONJOS = $strCORNEAOD = $strCORNEAOS = $strANTCHAMBEROD = $strANTCHAMBEROS = $strLENSOD = $strLENSOS = $strIRISOD = $strIRISOS ="";
			$sumConOd = wordwrap($res->fields["conjunctiva_od_summary"], 20, "<br />\n");
			$strCONJOD = $sumConOd;
			
			$sumConOs = wordwrap($res->fields["conjunctiva_os_summary"], 20, "<br />\n");			
			$strCONJOS = $sumConOs;
			
			$sumCorOd = wordwrap($res->fields["cornea_od_summary"], 20, "<br />\n");
			$strCORNEAOD = $sumCorOd;
			$sumCorOs = wordwrap($res->fields["cornea_os_summary"], 20, "<br />\n");
			$strCORNEAOS = $sumCorOs;
			
			$sumAnfOd = wordwrap($res->fields["anf_chamber_od_summary"], 20, "<br />\n");
			$strANTCHAMBEROD = $sumAnfOd;
			$sumAnfOs = wordwrap($res->fields["anf_chamber_os_summary"], 20, "<br />\n");					
			$strANTCHAMBEROS = $sumAnfOs;
			
			$sumIrisOd = wordwrap($res->fields["iris_pupil_od_summary"], 20, "<br />\n");
			$sumIrisOs = wordwrap($res->fields["iris_pupil_os_summary"], 20, "<br />\n");						
			
			$sumLensOd = wordwrap($res->fields["lens_od_summary"], 20, "<br />\n");
			$strLENSOD = $sumLensOd;
			$sumLensOs = wordwrap($res->fields["lens_os_summary"], 20, "<br />\n");
			$strLENSOS = $sumLensOs;
			
			if($res->fields["wnlConjOd"] == "1" && strpos($res->fields["statusElem"],"elem_chng_div1_Od=1") !== false){
				$wnlConjOd =  !empty($res->fields["wnl_value_Conjunctiva"]) ? $res->fields["wnl_value_Conjunctiva"] : getExamWnlStr("Conjunctiva", $ptId, $formId);
			}
			if($res->fields["wnlConjOs"] == "1" && strpos($res->fields["statusElem"],"elem_chng_div1_Os=1") !== false){
				$wnlConjOs =  !empty($res->fields["wnl_value_Conjunctiva"]) ? $res->fields["wnl_value_Conjunctiva"] : getExamWnlStr("Conjunctiva", $ptId, $formId);
			}
			
			if($res->fields["wnlCornOd"] == "1" && strpos($res->fields["statusElem"],"elem_chng_div2_Od=1") !== false){
				$wnlCornOd =  !empty($res->fields["wnl_value_Cornea"]) ? $res->fields["wnl_value_Cornea"] : getExamWnlStr("Cornea", $ptId, $formId);
			}
			if($res->fields["wnlCornOs"] == "1" && strpos($res->fields["statusElem"],"elem_chng_div2_Os=1") !== false){
				$wnlCornOs =  !empty($res->fields["wnl_value_Cornea"]) ? $res->fields["wnl_value_Cornea"] : getExamWnlStr("Cornea", $ptId, $formId);
			}
			
			if($res->fields["wnlAntOd"] == "1" && strpos($res->fields["statusElem"],"elem_chng_div3_Od=1") !== false){
				$wnlAntOd =  !empty($res->fields["wnl_value_Ant"]) ? $res->fields["wnl_value_Ant"] : getExamWnlStr("Ant. Chamber", $ptId, $formId);
			}
			if($res->fields["wnlAntOs"] == "1" && strpos($res->fields["statusElem"],"elem_chng_div3_Os=1") !== false){
				$wnlAntOs =  !empty($res->fields["wnl_value_Ant"]) ? $res->fields["wnl_value_Ant"] : getExamWnlStr("Ant. Chamber", $ptId, $formId); 
			}
			
			if($res->fields["wnlIrisOd"] == "1" && strpos($res->fields["statusElem"],"elem_chng_div4_Od=1") !== false){
				$wnlIrisOd =  !empty($res->fields["wnl_value_Iris"]) ? $res->fields["wnl_value_Iris"] : getExamWnlStr("Iris & Pupil", $ptId, $formId);
			}
			if($res->fields["wnlIrisOs"] == "1" && strpos($res->fields["statusElem"],"elem_chng_div4_Os=1") !== false){
				$wnlIrisOs =  !empty($res->fields["wnl_value_Iris"]) ? $res->fields["wnl_value_Iris"] : getExamWnlStr("Iris & Pupil", $ptId, $formId);
			}
			if($res->fields["wnlLensOd"] == "1" && strpos($res->fields["statusElem"],"elem_chng_div5_Od=1") !== false){
				$wnlLensOd =  !empty($res->fields["wnl_value_Lens"]) ? $res->fields["wnl_value_Lens"] : getExamWnlStr("Lens", $ptId, $formId);
			}
			if($res->fields["wnlLensOs"] == "1" && strpos($res->fields["statusElem"],"elem_chng_div5_Os=1") !== false){
				$wnlLensOs =  !empty($res->fields["wnl_value_Lens"]) ? $res->fields["wnl_value_Lens"] : getExamWnlStr("Lens", $ptId, $formId);
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
		$res = imw_fetch_object($sql);
		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv For Merged NSE Server
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
			if($res == false){
				$sql = str_replace("chart_slit_lamp_exam","chart_slit_lamp_exam_archive",$sql);
				$res=imw_fetch_object($sql);	
			}
		}
		//-------- End ----------------------------------------------------------------------------------	
		if($res !== false){
			$strCONJOD = $strCONJOS = $strCORNEAOD = $strCORNEAOS = $strANTCHAMBEROD = $strANTCHAMBEROS = $strLENSOD = $strLENSOS = "";
			$sumConOd = wordwrap($res->fields["conjunctiva_od_summary"], 20, "<br />\n");
			$strCONJOD = $sumConOd;
			
			$sumConOs = wordwrap($res->fields["conjunctiva_os_summary"], 20, "<br />\n");			
			$strCONJOS = $sumConOs;
			
			$sumCorOd = wordwrap($res->fields["cornea_od_summary"], 20, "<br />\n");
			$strCORNEAOD = $sumCorOd;
			$sumCorOs = wordwrap($res->fields["cornea_os_summary"], 20, "<br />\n");
			$strCORNEAOS = $sumCorOs;
			
			$sumAnfOd = wordwrap($res->fields["anf_chamber_od_summary"], 20, "<br />\n");
			$strANTCHAMBEROD = $sumAnfOd;
			$sumAnfOs = wordwrap($res->fields["anf_chamber_os_summary"], 20, "<br />\n");					
			$strANTCHAMBEROS = $sumAnfOs;
			
			$sumIrisOd = wordwrap($res->fields["iris_pupil_od_summary"], 20, "<br />\n");
			$sumIrisOs = wordwrap($res->fields["iris_pupil_os_summary"], 20, "<br />\n");						
			
			$sumLensOd = wordwrap($res->fields["lens_od_summary"], 20, "<br />\n");
			$strLENSOD = $sumLensOd;
			$sumLensOs = wordwrap($res->fields["lens_os_summary"], 20, "<br />\n");
			$strLENSOS = $sumLensOs;
			
			if($res->fields["wnlConjOd"] == "1"){
				$wnlConjOd =  !empty($res->fields["wnl_value_Conjunctiva"]) ? $res->fields["wnl_value_Conjunctiva"] : getExamWnlStr("Conjunctiva", $ptId, $formId);
			}
			if($res->fields["wnlConjOs"] == "1"){
				$wnlConjOs =  !empty($res->fields["wnl_value_Conjunctiva"]) ? $res->fields["wnl_value_Conjunctiva"] : getExamWnlStr("Conjunctiva", $ptId, $formId);
			}
			
			if($res->fields["wnlCornOd"] == "1"){
				$wnlCornOd =  !empty($res->fields["wnl_value_Cornea"]) ? $res->fields["wnl_value_Cornea"] : getExamWnlStr("Cornea", $ptId, $formId);
			}
			if($res->fields["wnlCornOs"] == "1"){
				$wnlCornOs =  !empty($res->fields["wnl_value_Cornea"]) ? $res->fields["wnl_value_Cornea"] : getExamWnlStr("Cornea", $ptId, $formId);
			}
			
			if($res->fields["wnlAntOd"] == "1"){
				$wnlAntOd =  !empty($res->fields["wnl_value_Ant"]) ? $res->fields["wnl_value_Ant"] : getExamWnlStr("Ant. Chamber", $ptId, $formId);
			}
			if($res->fields["wnlAntOs"] == "1"){
				$wnlAntOs =  !empty($res->fields["wnl_value_Ant"]) ? $res->fields["wnl_value_Ant"] : getExamWnlStr("Ant. Chamber", $ptId, $formId); 
			}
			
			if($res->fields["wnlIrisOd"] == "1"){
				$wnlIrisOd =  !empty($res->fields["wnl_value_Iris"]) ? $res->fields["wnl_value_Iris"] : getExamWnlStr("Iris & Pupil", $ptId, $formId);
			}
			if($res->fields["wnlIrisOs"] == "1"){
				$wnlIrisOs =  !empty($res->fields["wnl_value_Iris"]) ? $res->fields["wnl_value_Iris"] : getExamWnlStr("Iris & Pupil", $ptId, $formId);
			}
			if($res->fields["wnlLensOd"] == "1"){
				$wnlLensOd =  !empty($res->fields["wnl_value_Lens"]) ? $res->fields["wnl_value_Lens"] : getExamWnlStr("Lens", $ptId, $formId);
			}
			if($res->fields["wnlLensOs"] == "1"){
				$wnlLensOs =  !empty($res->fields["wnl_value_Lens"]) ? $res->fields["wnl_value_Lens"] : getExamWnlStr("Lens", $ptId, $formId);
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
	
	//Get Fundus
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
					c2.statusElem,
					c3.statusElem,
					c4.statusElem,
					c5.statusElem,
					c6.statusElem
				  ) AS statusElem
				FROM
				  chart_master_table c1
				  INNER JOIN chart_retinal_exam c2 ON c1.id = c2.form_id
				  INNER JOIN chart_vitreous c3 ON c1.id = c3.form_id
				  INNER JOIN chart_blood_vessels c4 ON c1.id = c4.form_id
				  INNER JOIN chart_macula c5 ON c1.id = c5.form_id
				  INNER JOIN chart_periphery c6 ON c1.id = c6.form_id
				  LEFT JOIN chart_optic ON chart_optic.form_id = c1.id AND chart_optic.purged = '0'
 			      LEFT JOIN disc ON disc.formId = c1.id AND disc.purged='0' AND disc.del_status = '0' AND disc.purged = '0'
				WHERE
				  c1.id = '".$formId."'
				  AND c1.patient_id = '".$ptId."'
				  AND c1.purge_status = '0'
				  AND c1.delete_status = '0'
				  AND c2.purged = '0'
				  AND c3.purged = '0'
				  AND c4.purged = '0'
				  AND c5.purged = '0'
				  AND c6.purged = '0'";
		$res = imw_fetch_object($sql);
		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv For Merged NSE Server
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
			if($res == false){
				$sql = str_replace("chart_rv","chart_rv_archive",$sql);
				$res=imw_fetch_object($sql);	
			}
		}
		//-------- End ----------------------------------------------------------------------------------	
				
		if($res !== false){			
			$strOPTICNERVEOD = $strOPTICNERVEOS = $strMACULAOD = $strMACULAOS = $strVITREOUSOD = $strVITREOUSOS = $strPERIPHERYOD = $strPERIPHERYOS = $strBVOD = $strBVOS = $strRETINALOD= $strRETINALOS="";
			
			if($res->fields["wnlVitreousOd"] == 0){
				$sumVitOd = wordwrap($res->fields["vitreous_od_summary"], 20, "<br />\n");
				$strVITREOUSOD = $sumVitOd;
			}
			else{
				$sumVitOd = !empty($res->fields["wnl_value_Vitreous"]) ? $res->fields["wnl_value_Vitreous"] : getExamWnlStr("Vitreous", $ptId, $formId);
				$strVITREOUSOD = $sumVitOd;
			}
			
			if($res->fields["wnlVitreousOs"] == 0){
				$sumVitOs = wordwrap($res->fields["vitreous_os_summary"], 20, "<br />\n");
				$strVITREOUSOS = $sumVitOs;
			}
			else{
				$sumVitOs = !empty($res->fields["wnl_value_Vitreous"]) ? $res->fields["wnl_value_Vitreous"] : getExamWnlStr("Vitreous", $ptId, $formId);
				$strVITREOUSOS = $sumVitOs;
			}
			
			if($res->fields["wnlPeriOd"] == 0){
				$sumPeriOd = wordwrap($res->fields["periphery_od_summary"], 20, "<br />\n");
				$strPERIPHERYOD = $sumPeriOd;
			}
			else{
				$sumPeriOd = !empty($res->fields["wnl_value_Peri"]) ? $res->fields["wnl_value_Peri"] :  getExamWnlStr("Periphery", $ptId, $formId);
				$strPERIPHERYOD = $sumPeriOd;
			}
			
			if($res->fields["wnlPeriOs"] == 0){
				$sumPeriOs = wordwrap($res->fields["periphery_os_summary"], 20, "<br />\n");			
				$strPERIPHERYOS = $sumPeriOs;
			}
			else{
				$sumPeriOs = !empty($res->fields["wnl_value_Peri"]) ? $res->fields["wnl_value_Peri"] :  getExamWnlStr("Periphery", $ptId, $formId);			
				$strPERIPHERYOS = $sumPeriOs;
			}
			
			if($res->fields["wnlBVOd"] == 0){
				$sumBvOd = wordwrap($res->fields["blood_vessels_od_summary"], 20, "<br />\n");
				$strBVOD = $sumBvOd;
			}
			else{
				$sumBvOd = !empty($res->fields["wnl_value_BV"]) ? $res->fields["wnl_value_BV"] :  getExamWnlStr("Blood Vessels", $ptId, $formId);
				$strBVOD = $sumBvOd;
			}
			
			if($res->fields["wnlBVOs"] == 0){
				$sumBvOs = wordwrap($res->fields["blood_vessels_os_summary"], 20, "<br />\n");
				$strBVOS = $sumBvOs;
			}
			else{
				$sumBvOs = !empty($res->fields["wnl_value_BV"]) ? $res->fields["wnl_value_BV"] :  getExamWnlStr("Blood Vessels", $ptId, $formId);
				$strBVOS = $sumBvOs;
			}
			
			if($res->fields["wnlMaculaOd"] == 0){
				$sumMacOd = wordwrap($res->fields["macula_od_summary"], 20, "<br />\n");			
				$strMACULAOD = $sumMacOd;
			}	
			else{
				$sumMacOd = !empty($res->fields["wnl_value_Macula"]) ? $res->fields["wnl_value_Macula"] :  getExamWnlStr("Macula", $ptId, $formId);
				$strMACULAOD = $sumMacOd;
			}		
			
			if($res->fields["wnlMaculaOs"] == 0){
				$sumMacOs = wordwrap($res->fields["macula_os_summary"], 20, "<br />\n");
				$strMACULAOS = $sumMacOs;
			}
			else{
				$sumMacOs = !empty($res->fields["wnl_value_Macula"]) ? $res->fields["wnl_value_Macula"] :  getExamWnlStr("Macula", $ptId, $formId);
				$strMACULAOS = $sumMacOs;
			}
			
			///Retinal
			if(isset($GLOBALS["ADD_PERI_WNL_2_RETINAL"])&&!empty($GLOBALS["ADD_PERI_WNL_2_RETINAL"])){
				if($res->fields["periNotExamined"]!=1){$wnlPeri="periphery normal";}else{$wnlPeri="periphery not examined";}
			}
			if($res->fields["wnlRetinalOd"] == 0){
				if($res->fields["periNotExamined"]==1 && ($res->fields["peri_ne_eye"]=="OU" || $res->fields["peri_ne_eye"]=="OD")){ 
					if(!empty($res->fields["retinal_od_summary"])){ $res->fields["retinal_od_summary"].=";";  }
					$res->fields["retinal_od_summary"] = $res->fields["retinal_od_summary"]." periphery not examined";
				}
				$sumRetiOd = wordwrap($res->fields["retinal_od_summary"], 20, "<br />\n");			
				$strRETINALOD = $sumRetiOd;
			}	
			else{
				$wnlRetExm = !empty($row["wnl_value_RetinalExam"]) ? $row["wnl_value_RetinalExam"] : getExamWnlStr("Retinal Exam", $ptId, $formId);
				$sumRetiOd = "".$wnlRetExm." ".$wnlPeri;
				$strRETINALOD = $sumRetiOd;
			}		
			
			if($res->fields["wnlRetinalOs"] == 0){
				if($res->fields["periNotExamined"]==1 && ($res->fields["peri_ne_eye"]=="OU" || $res->fields["peri_ne_eye"]=="OS")){ 
					if(!empty($res->fields["retinal_os_summary"])){ $res->fields["retinal_os_summary"].=";";  }
					$res->fields["retinal_os_summary"] = $res->fields["retinal_os_summary"]." periphery not examined";
				}
				$sumRetiOs = wordwrap($res->fields["retinal_os_summary"], 20, "<br />\n");			
				$strRETINALOS = $sumRetiOs;
			}	
			else{
				$wnlRetExm = !empty($row["wnl_value_RetinalExam"]) ? $row["wnl_value_RetinalExam"] : getExamWnlStr("Retinal Exam", $ptId, $formId);
				$sumRetiOs = "".$wnlRetExm." ".$wnlPeri;
				$strRETINALOS = $sumRetiOs;
			}	
			///Retinal
			
			if($res->fields["wnlOpticOd"] == 0){
				$sumOptOd = wordwrap($res->fields["optic_nerve_od_summary"], 20, "<br />\n");
				$strOPTICNERVEOD = $sumOptOd;
			}
			else{
				$wnlON = !empty($row["wnl_value_Optic"]) ? $row["wnl_value_Optic"] : getExamWnlStr("Optic Nerve", $ptId, $formId);
				$sumOptOd = $wnlON;//"Pink & Sharp";
				$strOPTICNERVEOD = $sumOptOd;
			}
			
			if($res->fields["wnlOpticOs"] == 0){
				$sumOptOs = wordwrap($res->fields["optic_nerve_os_summary"], 20, "<br />\n");
				$strOPTICNERVEOS = $sumOptOs;
			}
			else{
				$wnlON = !empty($row["wnl_value_Optic"]) ? $row["wnl_value_Optic"] : getExamWnlStr("Optic Nerve", $ptId, $formId);
				$sumOptOs = $wnlON;//"Pink & Sharp";
				$strOPTICNERVEOS = $sumOptOs;
			}
			if($res->fields["cd_val_od"] != ""){
				$CDOd = wordwrap($res->fields["cd_val_od"], 20, "<br />\n");
			}
			else if($res->fields["od_text"] != ""){
				$CDOd = wordwrap($res->fields["od_text"], 20, "<br />\n");
				$strCDOD = $CDOd;
			}
			if($res->fields["cd_val_os"] != ""){
				$CDOs = wordwrap($res->fields["cd_val_os"], 20, "<br />\n");
			}
			else if($res->fields["os_text"] != ""){
				$CDOs = wordwrap($res->fields["os_text"], 20, "<br />\n");
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
			
			if(!empty($sumOptOd)){
				$arrOd[] = "<b>Opt.Nev</b>&nbsp;".$sumOptOd.'</br>';
				$strOPTICNERVEOD = "<b>Opt.Nev</b>&nbsp;".$sumOptOd.'</br>';
			}
			if(!empty($sumOptOs)){
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
			if(!empty($sumRetiOd)){
				$arrOd[] = "<b>Retinal Exam</b>&nbsp;".$sumRetiOd.'</br>';
				$strRETINALOD = "<b>Retinal Exam</b>&nbsp;".$sumRetiOd.'</br>';
			}
			if(!empty($sumRetiOs)){
				$arrOs[] = "<b>Retinal Exam</b>&nbsp;".$sumRetiOs.'</br>';
				$strRETINALOS = "<b>Retinal Exam</b>&nbsp;".$sumRetiOs.'</br>';
			}
			//Retinal
			if(!empty($sumVitOd)){
				$arrOd[] = "<b>Vitreous</b>&nbsp;".$sumVitOd.'</br>';
				$strVITREOUSOD = "<b>Vitreous</b>&nbsp;".$sumVitOd.'</br>';
			}
			if(!empty($sumVitOs)){
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
				  INNER JOIN chart_retinal_exam c2 ON c1.id = c2.form_id
				  INNER JOIN chart_vitreous c3 ON c1.id = c3.form_id
				  INNER JOIN chart_blood_vessels c4 ON c1.id = c4.form_id
				  INNER JOIN chart_macula c5 ON c1.id = c5.form_id
				  INNER JOIN chart_periphery c6 ON c1.id = c6.form_id
				  LEFT JOIN chart_optic ON chart_optic.form_id = c1.id AND chart_optic.purged = '0'
				  LEFT JOIN disc ON disc.formId = c1.id  AND disc.del_status = '0' AND disc.purged = '0'
				 
				WHERE
				  c1.id = '".$formId."'
				  AND c1.patient_id = '".$ptId."'
				  AND c1.purge_status = '0'
				  AND c1.delete_status = '0'
				  AND c2.purged = '0'
				  AND c3.purged = '0'
				  AND c4.purged = '0'
				  AND c5.purged = '0'
				  AND c6.purged = '0'";
		
		$res = imw_fetch_object($sql);
		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv For Merged NSE Server
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
			if($res == false){
				$sql = str_replace("chart_rv","chart_rv_archive",$sql);
				$res=imw_fetch_object($sql);	
			}
		}
		//-------- End ----------------------------------------------------------------------------------	
		if($res !== false){			
			$strOPTICNERVEOD = $strOPTICNERVEOS = $strMACULAOD = $strMACULAOS = $strVITREOUSOD = $strVITREOUSOS = $strPERIPHERYOD = $strPERIPHERYOS = $strBVOD = $strBVOS = $strRETINALOD= $strRETINALOS="";
			
			if($res->fields["wnlVitreousOd"] == 0){
				$sumVitOd = wordwrap($res->fields["vitreous_od_summary"], 20, "<br />\n");
				$strVITREOUSOD = $sumVitOd;
			}
			else{
				$sumVitOd = !empty($res->fields["wnl_value_Vitreous"]) ? $res->fields["wnl_value_Vitreous"] : getExamWnlStr("Vitreous", $ptId, $formId);
				$strVITREOUSOD = $sumVitOd;
			}
			
			if($res->fields["wnlVitreousOs"] == 0){
				$sumVitOs = wordwrap($res->fields["vitreous_os_summary"], 20, "<br />\n");
				$strVITREOUSOS = $sumVitOs;
			}
			else{
				$sumVitOs = !empty($res->fields["wnl_value_Vitreous"]) ? $res->fields["wnl_value_Vitreous"] : getExamWnlStr("Vitreous", $ptId, $formId);
				$strVITREOUSOS = $sumVitOs;
			}
			
			if($res->fields["wnlPeriOd"] == 0){
				$sumPeriOd = wordwrap($res->fields["periphery_od_summary"], 20, "<br />\n");
				$strPERIPHERYOD = $sumPeriOd;
			}
			else{
				$sumPeriOd = !empty($res->fields["wnl_value_Peri"]) ? $res->fields["wnl_value_Peri"] :  getExamWnlStr("Periphery", $ptId, $formId);
				$strPERIPHERYOD = $sumPeriOd;
			}
			
			if($res->fields["wnlPeriOs"] == 0){
				$sumPeriOs = wordwrap($res->fields["periphery_os_summary"], 20, "<br />\n");			
				$strPERIPHERYOS = $sumPeriOs;
			}
			else{
				$sumPeriOs = !empty($res->fields["wnl_value_Peri"]) ? $res->fields["wnl_value_Peri"] :  getExamWnlStr("Periphery", $ptId, $formId);			
				$strPERIPHERYOS = $sumPeriOs;
			}
			
			if($res->fields["wnlBVOd"] == 0){
				$sumBvOd = wordwrap($res->fields["blood_vessels_od_summary"], 20, "<br />\n");
				$strBVOD = $sumBvOd;
			}
			else{
				$sumBvOd = !empty($res->fields["wnl_value_BV"]) ? $res->fields["wnl_value_BV"] :  getExamWnlStr("Blood Vessels", $ptId, $formId);
				$strBVOD = $sumBvOd;
			}
			
			if($res->fields["wnlBVOs"] == 0){
				$sumBvOs = wordwrap($res->fields["blood_vessels_os_summary"], 20, "<br />\n");
				$strBVOS = $sumBvOs;
			}
			else{
				$sumBvOs = !empty($res->fields["wnl_value_BV"]) ? $res->fields["wnl_value_BV"] :  getExamWnlStr("Blood Vessels", $ptId, $formId);
				$strBVOS = $sumBvOs;
			}
			
			if($res->fields["wnlMaculaOd"] == 0){
				$sumMacOd = wordwrap($res->fields["macula_od_summary"], 20, "<br />\n");			
				$strMACULAOD = $sumMacOd;
			}	
			else{
				$sumMacOd = !empty($res->fields["wnl_value_Macula"]) ? $res->fields["wnl_value_Macula"] :  getExamWnlStr("Macula", $ptId, $formId);
				$strMACULAOD = $sumMacOd;
			}		
			
			if($res->fields["wnlMaculaOs"] == 0){
				$sumMacOs = wordwrap($res->fields["macula_os_summary"], 20, "<br />\n");
				$strMACULAOS = $sumMacOs;
			}
			else{
				$sumMacOs = !empty($res->fields["wnl_value_Macula"]) ? $res->fields["wnl_value_Macula"] :  getExamWnlStr("Macula", $ptId, $formId);
				$strMACULAOS = $sumMacOs;
			}
			
			///Retinal
			if(isset($GLOBALS["ADD_PERI_WNL_2_RETINAL"])&&!empty($GLOBALS["ADD_PERI_WNL_2_RETINAL"])){
				if($res->fields["periNotExamined"]!=1){$wnlPeri="periphery normal";}else{$wnlPeri="periphery not examined";}
			}
			if($res->fields["wnlRetinalOd"] == 0){
				if($res->fields["periNotExamined"]==1 && ($res->fields["peri_ne_eye"]=="OU" || $res->fields["peri_ne_eye"]=="OD")){ 
					if(!empty($res->fields["retinal_od_summary"])){ $res->fields["retinal_od_summary"].=";";  }
					$res->fields["retinal_od_summary"] = $res->fields["retinal_od_summary"]." periphery not examined";
				}
				$sumRetiOd = wordwrap($res->fields["retinal_od_summary"], 20, "<br />\n");					
				$strRETINALOD = $sumRetiOd;
			}	
			else{
				$wnlRetExm = !empty($row["wnl_value_RetinalExam"]) ? $row["wnl_value_RetinalExam"] : getExamWnlStr("Retinal Exam", $ptId, $formId);
				$sumRetiOd = "".$wnlRetExm." ".$wnlPeri;
				$strRETINALOD = $sumRetiOd;
			}		
			
			if($res->fields["wnlRetinalOs"] == 0){
				if($res->fields["periNotExamined"]==1 && ($res->fields["peri_ne_eye"]=="OU" || $res->fields["peri_ne_eye"]=="OS")){ 
					if(!empty($res->fields["retinal_os_summary"])){ $res->fields["retinal_os_summary"].=";";  }
					$res->fields["retinal_os_summary"] = $res->fields["retinal_os_summary"]." periphery not examined";
				}
				$sumRetiOs = wordwrap($res->fields["retinal_os_summary"], 20, "<br />\n");			
				$strRETINALOS = $sumRetiOs;
			}	
			else{
				$wnlRetExm = !empty($row["wnl_value_RetinalExam"]) ? $row["wnl_value_RetinalExam"] : getExamWnlStr("Retinal Exam", $ptId, $formId);
				$sumRetiOs = "".$wnlRetExm." ".$wnlPeri;
				$strRETINALOS = $sumRetiOs;
			}	
			
			if($res->fields["wnlOpticOd"] == 0){
				$sumOptOd = wordwrap($res->fields["optic_nerve_od_summary"], 20, "<br />\n");
				$strOPTICNERVEOD = $sumOptOd;
			}
			else{
				$wnlON = !empty($row["wnl_value_Optic"]) ? $row["wnl_value_Optic"] : getExamWnlStr("Optic Nerve", $ptId, $formId);
				$sumOptOd = $wnlON;//"Pink & Sharp";
				$strOPTICNERVEOD = $sumOptOd;
			}
			
			if($res->fields["wnlOpticOs"] == 0){
				$sumOptOs = wordwrap($res->fields["optic_nerve_os_summary"], 20, "<br />\n");
				$strOPTICNERVEOS = $sumOptOs;
			}
			else{
				$wnlON = !empty($row["wnl_value_Optic"]) ? $row["wnl_value_Optic"] : getExamWnlStr("Optic Nerve", $ptId, $formId);
				$sumOptOs = $wnlON;//"Pink & Sharp";
				$strOPTICNERVEOS = $sumOptOs;
			}
			if($res->fields["cdOd"] != ""){
				$CDOd = wordwrap($res->fields["cdOd"], 20, "<br />\n");
			}
			else if($res->fields["od_text"] != ""){
				$CDOd = wordwrap($res->fields["od_text"], 20, "<br />\n");
				$strCDOD = $CDOd;
			}
			if($res->fields["cdOs"] != ""){
				$CDOs = wordwrap($res->fields["cdOs"], 20, "<br />\n");
			}
			else if($res->fields["os_text"] != ""){
				$CDOs = wordwrap($res->fields["os_text"], 20, "<br />\n");
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
		$res = imw_fetch_object($sql);
		if($res !== false){
			$scribed_by_opr = $res->fields["scribed_by_opr"];
			$a1 = $res->fields["assessment_1"];
			$a2 = $res->fields["assessment_2"];
			$a3 = $res->fields["assessment_3"];
			$assess = $res->fields["assessment_4"];
			
			$p1 = $res->fields["plan_notes_1"];
			$p2 = $res->fields["plan_notes_2"];
			$p3 = $res->fields["plan_notes_3"];
			$plan = $res->fields["plan_notes_4"];
			$strXml = $res->fields["assess_plan"];
			$aAll = $pAll = $FUDatta = $folllowUp = $eyeAll = "";
			//--- Set Assess Plan Resolve NE Value FROM xml// Start					 
			$arrApVals = $this->getVal_Str($strXml);
			$arrAp = $arrApVals["data"]["ap"];
			//echo '<pre>';
			//print_r($arrAp);
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
			if($res->fields["follow_up_numeric_value"]){				
				$followUpNumber = $res->fields["follow_up_numeric_value"];
				if($followUpNumber == "13"){
					$followUpNumber = "PRN";
				}
				if($followUpNumber == "14"){
					$followUpNumber = "PMD";
				}
				$followUpTmp = $res->fields["follow_up"];
				$followUpVistType = $res->fields["followUpVistType"];
				
				$folllowUp .= (!empty($followUpNumber)) ? $followUpNumber." " : "";
				$folllowUp .= (!empty($followUpTmp)) ? $followUpTmp." " : "";
				$folllowUp .= (!empty($followUpVistType)) ? $followUpVistType." " : "";			
			}elseif($res->fields["followup"]){
				$followup = $res->fields["followup"];
				$arrFollowup = $this->fuGetXmlValsArr($followup);				
				for($a=0;$a<count($arrFollowup[1]);$a++){					
					if($a==0){
						//$FUDatta.='F/U ';
					}
					else{
						$FUDatta.=' ';
					}
					$number= $arrFollowup[1][$a]['number'][0];				
					$FUDatta.=' '.$number;		
					$time= $arrFollowup[1][$a]['time'][0];
					$FUDatta.=' '.$time;					
					$visit_type= $arrFollowup[1][$a]['visit_type'][0];
					if(trim($visit_type)) {
						$FUDatta.=' for '.$visit_type;					
					}
					//if(trim($FUDatta) != "F/U")
					if(count($arrFollowup[1])>1 && (count($arrFollowup[1])-1)>$a && (trim($arrFollowup[1][$a+1]['number'][0])!="" || trim($arrFollowup[1][$a+1]['time'][0])!="" || trim($arrFollowup[1][$a+1]['visit_type'][0])!="")){
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
					$arrRep[] = wordwrap($FUDatta, 60, "<br />\n");
			//	}	
			}
			else {
				$arrRep[]='';
			}
			
			$ap = "</p>Assessment: ".$aAllNew."<br />Plan: ".$aPllNew;
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
		$res = imw_fetch_object($sql);
		if($res !== false){
			
			$strXml = stripslashes($res->fields["assess_plan"]);
			//--- Set Assess Plan Resolve NE Value FROM xml// Start					 
			$arrApVals = $this->getVal_Str($strXml);
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
		$res = imw_fetch_object($sql);
		if($res !== false){
			$wnl =$res->fields["wnl"];
			if($wnl==1){
				$eom = $res->fields["wnl_value"];
			}else{
				$eom = $res->fields["sumEom"];			
			}
		}
		
		//glare
		$sqlGlare = "SELECT glareOD, glareOS FROM surgical_tbl WHERE patient_id = '".$ptId."' ORDER BY `surgical_id` DESC ";
		$resGlare = imw_fetch_object($sqlGlare);
		if($resGlare !== false){
			$glareOD = $resGlare->fields["glareOD"];
			$glareOS = $resGlare->fields["glareOS"];

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
		$res = imw_fetch_object($sql);
		if($res !== false){
			$sumCVFod = $res->fields["summaryOd"];			
			$sumCVFos = $res->fields["summaryOs"];			
		}
		$arrRep = array($sumCVFod,$sumCVFos);
		//Replace
		$str = $this->refineData($str, $this->kCVF, $arrRep, $this->tCVF,"");
		return $str;
	}
	//Get Date
	private function getDate($str){
		$currentDate = get_date_format(date("Y-m-d"));
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
	public function getDataParsed($str="",$ptId=0,$formId=0,$patientRefTo="",$addresseRefToId="",$cc1RefToId="",$cc2RefToId="",$cc3RefToId="",$sign_path_remote="",$document_from=""){
		
		if(!empty($str) && !empty($ptId)){
			
			//ptInfo
			if($this->isKeyExist($str, $this->kPtInfo)){				
				$str = $this->getPtInfo($str, $ptId,$patientRefTo,$addresseRefToId,$cc1RefToId,$cc2RefToId,$cc3RefToId);
			}
		}
		if(!empty($str) && !empty($ptId) && !empty($formId)){	
		
			//Chart Left CC History
			if($this->isKeyExist($str, $this->kChartLeftCcHx)){		
				$str = $this->getChartLeftCcHx($str, $ptId, $formId,$sign_path_remote,$document_from);
			}
			
			//Vision
			if($this->isKeyExist($str, $this->kVision)){		
				$str = $this->getVision($str, $ptId, $formId);
			}
			
			//MR1, MR2, MR3
			if($this->isKeyExist($str, $this->kMr1Mr2Mr3)){		
				$str = $this->getMr1Mr2Mr3($str, $ptId, $formId);
			}
			
			//PC1, PC2, PC3
			if($this->isKeyExist($str, $this->kPc1Pc2Pc3)){		
				$str = $this->getPc1Pc2Pc3($str, $ptId, $formId);
			}
			
			//Pupil
			if($this->isKeyExist($str, $this->kPupil)){		
				$str = $this->getPupil($str, $ptId, $formId);
			}
			
			//Exter
			if($this->isKeyExist($str, $this->kExter)){		
				$str = $this->getExter($str, $ptId, $formId);
			}
			
			//LA
			if($this->isKeyExist($str, $this->kLa)){		
				$str = $this->getLa($str, $ptId, $formId);
			}
			//LA in Table Format
			if($this->isKeyExist($str, $this->kLna)){		
				$str = $this->getLnA($str, $ptId, $formId);
			}
			//IOP
			if($this->isKeyExist($str, $this->kIop)){		
				$str = $this->getIop($str, $ptId, $formId);
			}
			
			//Gonio
			if($this->isKeyExist($str, $this->kGonio)){		
				$str = $this->getGonio($str, $ptId, $formId);
			}
			
			//Sle
			if($this->isKeyExist($str, $this->kSle)){		
				$str = $this->getSle($str, $ptId, $formId);
			}
			//SLE in Table Format
			if($this->isKeyExist($str, $this->kSli)){		
				$str = $this->getSli($str, $ptId, $formId);
			}
			
			//Fundus
			if($this->isKeyExist($str, $this->kFundus)){		
				$str = $this->getFundus($str, $ptId, $formId);
			}
			//Fundus in Table Format
			if($this->isKeyExist($str, $this->kFundusAll)){		
				$str = $this->getFundusAll($str, $ptId, $formId);
			}
			
			//Eom
			if($this->isKeyExist($str, $this->kEom)){		
				$str = $this->getEom($str, $ptId, $formId);
			}
			//CVF
			if($this->isKeyExist($str, $this->kCVF)){		
				$str = $this->getCVF($str, $ptId, $formId);
			}
			
			//Assess and plan
			if($this->isKeyExist($str, $this->kAP)){
				$str = $this->getAP($str, $ptId, $formId);
			}
			//Assess and plan Od/os/ou vals
			if($this->isKeyExist($str, $this->kAPODOSOUs)){
				$str = $this->getAPoDoSoUVal($str, $ptId, $formId);
			}
		
			//Extra variable
			if($this->isKeyExist($str, $this->kDate)){
				//echo 'hlo';
				$str = $this->getDate($str);
			}
			//Pinhole
			if($this->isKeyExist($str, $this->kPinhole)){
				$str = $this->getPinhole($str, $ptId, $formId);
			}
			//Text Box
			if($this->isKeyExist($str, $this->kTextBox)){
				$str = $this->getTextBox($str);
			}
			
			
			//START Medical History
			if($this->isKeyExist($str, $this->kMedHx)){		
				$str = $this->getMedHx($str, $ptId, $formId);
			}
			//END Medical History
			//====GET PACHY DATA==============
			if($this->isKeyExist($str, $this->kPachy)){		
				$str = $this->getPachy($str, $ptId, $formId);
			}
			//====END PACHY DATA==============
			
		}
		
		$str = $this->getGroupInfo($str);
		
		return $str;
	}
	
	//START get MedHx
	private function getMedHx($str, $ptId, $formId) {
		$arrReturn = array();
		if($ptId != 0){
			//ocular history
			$ocularHist 		= $this->getOcularHist($ptId);	
			
			//latest vital sign
			$latestVitalSign 	= $this->getLatestVitalSign($ptId);	
			
			//ocular and systemic medication
			$ocularMed 			= $this->getMedList($ptId,4);	
			$systemicMed 		= $this->getMedList($ptId,1);	
			$allergyList 		= $this->getMedList($ptId,7);
			$medProbList 		= $this->getMedProbList($ptId);
			
			//MedHx
			$medHx 				= $this->getMedHxSummary($ptId);
			
			
			 
			$diabMedArr			= $this->getDiabMedInfo($ptId);
			$Diabetes 			= $diabMedArr[0];
			
			$oclrArr			= $this->getOcularEyeInfo($ptId);
			$flashYesNo 		= $oclrArr[3];
			$floatersYesNo 		= $oclrArr[2];
			
			$socialHistArr 		= $this->getSocialHist($ptId);
			$smokeSmry 			= $socialHistArr[0];
			$fmlyHxSmokSmry		= $socialHistArr[1];
			
			$empId 				= $_SESSION['authId'];
			$genHealth 			= $this->getMedHxSummary($ptId,"General Health");

			$arrMedHx = array($ocularHist,$latestVitalSign,$ocularMed,$systemicMed,$allergyList,$medProbList,$medHx,$Diabetes,$flashYesNo,$floatersYesNo,$smokeSmry,$fmlyHxSmokSmry,$empId,$genHealth);
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
	private function getMedHxSummary($ptId,$defaultData = NULL) {
		$ocData = $ghData = $MSAData = $immData = $medHxData = "";
		$arrPtOcular = $arrPtInfo = array();
		$history = "";
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
			imw_free_result($rsGetBS);
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
			imw_free_result($rsGetCH);
		}
		$ghData_gen .= "</td></tr>";
		
		$ghData .= "<tr><td style=\"width:310;border-bottom:1px solid #CCC; font-weight:bold; padding-left:5px;padding-top:3px;background:#CCC;\">General Health</td>
						<td style=\"width:310;border-bottom:1px solid #CCC;  font-weight:bold; padding-left:5px;padding-top:3px;background:#CCC;\">Review of Systems</td></tr>";
		$arrPtInfo = getPtGenHealthInfo($ptId);
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
		$strROS = ""; $strROS_neg="";	$chk_rev=false;
		if(count($arrPtInfo["ROS"]) > 0 ){
			foreach($arrPtInfo["ROS"] as $key => $val){			
				if(count($val) > 0){
					//$key = (strlen($key)>$max_chars)?substr($key,0,$max_showc)."...":$key;
					if($key != "negChkBx"){$chk_rev=true;
						$strROS .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"border-bottom:1px solid #CCC;width:350px; padding-left:5px;\">".$key."</td></tr></table>";
					}
					elseif($key == "negChkBx"){
						foreach($arrPtInfo["ROS"]["negChkBx"] as $negChkBxKey => $negChkBxVal){$chk_rev=true;
							//$negChkBxVal = (strlen($negChkBxVal)>$max_chars)?substr($negChkBxVal,0,$max_showc)."...":$negChkBxVal;
							$strROS_neg .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"border-bottom:1px solid #CCC;width:350px; padding-left:5px;\"> Negative&nbsp;".$negChkBxVal."</td></tr></table>";						
						}
					}
				}
			}
			
			if(!empty($strROS)){
				if(!empty($strROS_neg)){
					$strROS .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"border-bottom:1px solid #CCC;width:350px; padding-left:5px;\">All recorded systems are negative except as noted above.</td></tr></table>";
				}
			}else if(!empty($strROS_neg)){
				$strROS = $strROS_neg;
			}	
		}
		if($chk_gen_rev==false){$strMedicalCond.="&nbsp;None";  $genHealth="&nbsp;None";}if($chk_rev==false){$strROS.="&nbsp;None";}
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
			
			if($type == 7 && $ag_occular_drug != 'fdbATDrugName') {//SHOW ONLY DRUG ALLEGIES
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
			if(getNumber($administered_date) == '00000000'){
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
		if($defaultData=="General Health"){
			$medHxData = "";
			$medHxData = $genHealth;
		}
		return $medHxData;
	}
	public function getMedHx_public($pt_id){
		$medhx=$this->getMedHxSummary($pt_id);	
		return $medhx;
	}
	//get patient problem list
	private function getMedProbList($pid) {	
		$medProbName = "";
		$problemNameArr = array();
		if($pid){
			$medProbQry = "SELECT problem_name FROM pt_problem_list WHERE pt_id='".$pid."' AND  
						status = 'Active' ORDER BY id";
			
			$medProbRes = imw_query($medProbQry) or die(imw_error());
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
	private function getMedList($pid,$listType) {	
		$medList = "";
		$medListArr = array();
		if($pid && $listType){
			$medQry = "SELECT title,comments,sig,qty,refills,referredby,destination, DATE_FORMAT(begdate,'".get_sql_date_format()."') as begdateNew ,
						DATE_FORMAT(enddate,'".get_sql_date_format()."') as enddateNew FROM lists WHERE pid='".$pid."' AND  
						allergy_status = 'Active' AND type in (".$listType.") ORDER BY id";
			$medRes = imw_query($medQry) or die(imw_error());
			if(imw_num_rows($medRes)>0) {
				while($medRow = imw_fetch_array($medRes)) {
					$dosage 		= trim($medRow["destination"]);
					$sig 			= trim($medRow["sig"]);
					$dosageSig 		= "";
					$comments 		= trim($medRow["comments"]);
					$commentsVal 	= "";
					
					if(($dosage || $sig) && $listType=='4') { $dosageSig=trim('('.$dosage.''.$sig.')');}
					if(($comments) && $listType=='7') { $commentsVal=trim('('.$comments.')');}
					$medListArr[] 	= $medRow["title"].$dosageSig.$commentsVal;
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
			$res = imw_fetch_object($sql);
			
			if($res !== false){
				$rfTitle	 = $res->fields["rfTitle"];	
				$rfFName	 = $res->fields["rfFName"];		
				$rfMName	 = $res->fields["rfMName"];	
				$rfLName	 = $res->fields["rfLName"];	
				$gmPCP		 = $res->fields["gmPCP"];	
				$rfFax		 = $res->fields["rfFax"];
				$rfPcpFax	 = $res->fields["rfPcpFax"];
				$rfId		 = $res->fields["rfId"];
				$rfPcpId	 = $res->fields["rfPcpId"];
				$coManPhy	 = $res->fields["coManPhy"];
				$coManPhyId	 = $res->fields["coManPhyId"];
				$rfCoManFName= $res->fields["rfCoManFName"];	
				$rfCoManLName= $res->fields["rfCoManLName"];	
				$rfCoManFax= $res->fields["rfCoManFax"];
				
				$rfEmail= $res->fields["rfEmail"];
				$rfPcpEmail= $res->fields["rfPcpEmail"];
				$rfCoManEmail= $res->fields["rfCoManEmail"];	
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
					rf.Address1 as rfAddress1,rf.Address2 as rfAddress2,rf.City as rfCity,rf.State as rfState,rf.ZipCode as rfZipCode,rf.physician_fax as rfPhysicianFax
					 from refferphysician rf
					 where rf.physician_Reffer_id = '".$refId."'";
			$res = imw_fetch_object($sql);
			
			if($res !== false){
				$rfTitle	 		= $res->fields["rfTitle"];	
				$rfFName	 		= $res->fields["rfFName"];		
				$rfMName	 		= $res->fields["rfMName"];	
				$rfLName	 		= $res->fields["rfLName"];	
				$rfAddress1	 		= $res->fields["rfAddress1"];	
				$rfAddress2	 		= $res->fields["rfAddress2"];	
				$rfCity	 	 		= $res->fields["rfCity"];	
				$rfState	 		= $res->fields["rfState"];	
				$rfZipCode	 		= $res->fields["rfZipCode"];	
				$rfPhysicianFax		= $res->fields["rfPhysicianFax"];	
				//Returning Array
				$arrReturn = array($rfTitle,$rfFName,$rfMName,$rfLName,$rfAddress1,$rfAddress2,$rfCity,$rfState,$rfZipCode,$rfPhysicianFax);
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
			$res = imw_fetch_object($sql);
			if($res !== false){
				$rf_physician_Reffer_id	 = $res->fields["physician_Reffer_id"];	
					
				//Returning Array
				$arrReturn = array($rf_physician_Reffer_id);
			}
		}
		return $arrReturn;
			
	}
		
	//get CC History of Patient	first line as it appear in chart note cc history textarea
	public function getCcHx1stLine($pid){
		$text_data = $age = "";
		// Patient_data
		$sql = "select sex,DOB,providerID,DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(patient_data.dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(patient_data.dob, '00-%m-%d')) AS ptAge from patient_data where id=".$pid;		
		$res = imw_fetch_object($sql);
		if($res != false){
			$sex=$res->fields['sex'];
			$dob=$res->fields['DOB'];
			$defaul_provider = $res->fields['providerID'];
			if(strlen($res->fields['ptAge']) != 4){
				$age = $res->fields['ptAge'];
				if($age != ""){
					$age .= "&nbsp;Yrs.";
				}
			}
			
			$text_data="A $age $sex $ocular_medi_List with history of \r";//$eyeProbs$rvs";
		}			
		return $text_data;
	}
	
	//get CC History of Patient	oculer eye info line as it appear in chart note cc history textarea
	public function getOcularEyeInfo($pid){	
		$eyeProbs="";
		$chronicProbs = "";
		$floatersYesNo = "No";$flashYesNo = "No";
		//Ocular 
		$sql = "select any_conditions_you, chronicDesc, any_conditions_others_you, OtherDesc, eye_problems from ocular where patient_id='".$pid."' ";
		$res = imw_fetch_object($sql);
		if($res != false){		
			//desc
			$strSep="~!!~~";
			$strSep2=":*:";
			$strDesc = $res->fields["chronicDesc"];	
			$strDesc = $this->get_set_pat_rel_values_retrive($strDesc,"pat");		
			$arrChronicDesc=array();
			if(!empty($strDesc)){
				$arrDescTmp = explode($strSep, $strDesc);
				if(count($arrDescTmp) > 0){
					foreach($arrDescTmp as $key => $val){
						$arrTmp = explode($strSep2,$val);
						$arrChronicDesc[$arrTmp[0]] = $arrTmp[1]; 					
					}
				}				
			}
			
			$strAnyConditionsYou = $res->fields["any_conditions_you"];
			$strAnyConditionsYou = $this->get_set_pat_rel_values_retrive($strAnyConditionsYou,"pat");		
		
		
			$any_conditions_u1_arr=explode(" ",trim(str_replace(","," ",$strAnyConditionsYou)));
			//for($epr=0;$epr<=sizeof($any_conditions_u1_arr);$epr++){
				if(in_array("1", $any_conditions_u1_arr)){
					$sTmp = (!empty($arrChronicDesc[1])) ? " - ".$arrChronicDesc[1] : "";
					$chronicProbs.="\nDry Eyes".$sTmp;
					//$elem_glucoma = "1";	
				}
				if(in_array("2", $any_conditions_u1_arr)){
					$sTmp = (!empty($arrChronicDesc[2])) ? " - ".$arrChronicDesc[2] : "";
					$chronicProbs.="\nMacula Degeneration".$sTmp;
					//$elem_macDeg = "1";	
				}
				if(in_array("3", $any_conditions_u1_arr)){
					$sTmp = (!empty($arrChronicDesc[3])) ? " - ".$arrChronicDesc[3] : "";
					$chronicProbs.="\nGlaucoma".$sTmp;
					//$elem_glucoma = "1";	
				}
				if(in_array("4", $any_conditions_u1_arr)){
					$sTmp = (!empty($arrChronicDesc[4])) ? " - ".$arrChronicDesc[4] : "";
					$chronicProbs.="\nRetinal Detachment".$sTmp;
					//$elem_rDetach = "1";
				}
				if(in_array("5", $any_conditions_u1_arr)){
					$sTmp = (!empty($arrChronicDesc[5])) ? " - ".$arrChronicDesc[5] : "";
					$chronicProbs.="\nCataracts".$sTmp;
					//$elem_cataracts = "1";
				}			
			//}
			$strOtherDesc = "";
			$strOtherDesc = $res->fields["OtherDesc"];
			$strOtherDesc = $this->get_set_pat_rel_values_retrive($strOtherDesc,"pat");
		
			if(($res->fields["any_conditions_others_you"] == "1") && !empty($strOtherDesc)){
				$sTmp = (!empty($arrChronicDesc[$arrTmp["other"]])) ? " - ".$arrChronicDesc[$arrTmp["other"]] : "";
				$chronicProbs.="\n".$strOtherDesc.$sTmp;
			}
		
			$eye_problems_arr=explode(" ",trim(str_replace(","," ",$res->fields["eye_problems"])));
			//for($epr=0;$epr<=sizeof($eye_problems_arr);$epr++){
				if(in_array("1",$eye_problems_arr)){
					//$eyeProbs.="\nBlurred";
					$eyeProbs.= "\nBlurred or Poor Vision";
				}
				if(in_array("2",$eye_problems_arr)){
					$eyeProbs.="\nPoor night vision";
				}
				if(in_array("3",$eye_problems_arr)){
					$eyeProbs.="\nGritty Sensation";
				}
				if(in_array("4",$eye_problems_arr)){
					$eyeProbs.="\nTrouble Reading Signs";
				}
				if(in_array("5",$eye_problems_arr)){
					$eyeProbs.="\nGlare From Lights";
				}
				if(in_array("6",$eye_problems_arr)){
					$eyeProbs.="\nTearing";
				}
				if(in_array("7",$eye_problems_arr)){
					$eyeProbs.="\nPoor Depth Perception";
				}
				if(in_array("8",$eye_problems_arr)){
					$eyeProbs.="\nHalos Around Lights";
				}			
				if(in_array("9",$eye_problems_arr)){
					$eyeProbs.="\nItching/Burning";
				}
				if(in_array("10",$eye_problems_arr)){
					$eyeProbs.="\nTrouble Identifying Colors";
				}
				if(in_array("11",$eye_problems_arr)){
					$eyeProbs.="\nSpots/Floaters";
					$floatersYesNo = "Yes";
				}
				if(in_array("12",$eye_problems_arr)){
					$eyeProbs.="\nEye Pain";
				}
				if(in_array("13",$eye_problems_arr)){
					$eyeProbs.="\nDouble Vision";
				}
				if(in_array("14",$eye_problems_arr)){
					$eyeProbs.="\nSee Light Flashes";
					$flashYesNo = "Yes";
				}			
				if(in_array("15",$eye_problems_arr)){
					$eyeProbs.="\nRed eyes";
				}
				if(!empty($res->fields["eye_problems_other"])){
					$eyeProbs.="\n".$res->fields["eye_problems_other"];	
				}
			//}
		}	
		$eyeProbs = nl2br($eyeProbs);
		$chronicProbs = nl2br($chronicProbs);		
		return array($eyeProbs,$chronicProbs,$floatersYesNo,$flashYesNo);
	}
	//get CC History of Patient	general health info line as it appear in chart note cc history textarea
	public function getGenMedInfo($pid, $flagDiab=false){
		$delimiter = '~|~';
		$rvs = "";		
		//General Medicine
		$sql = "select any_conditions_you,any_conditions_relative,desc_u,desc_r,diabetes_values from general_medicine where patient_id='".$pid."' ";
		$res = imw_fetch_object($sql);
		if($res != false){
			$any_conditions_u1_arr1=explode(" ",trim(str_replace(","," ",$res->fields["any_conditions_you"])));
			//for($epr=0;$epr<=sizeof($any_conditions_u1_arr1);$epr++){				
			//Diabeties
			if(in_array("3",$any_conditions_u1_arr1)){
				//$rvs.="\nDiet ".date("m-Y");
				$strDiabetesIdTxtPat =  get_set_pat_rel_values_retrive($res->fields["diabetes_values"],'pat',$delimiter);				
				$rvs.="\n Diabetes ".date("m-Y")." ".$strDiabetesIdTxtPat;
				//$rvs.="\nDiabetes ";				
				//$ptInfoDiaDesc="Diabetes";
				$strDiabetesTxtPat = "";
				$strDiabetesTxtPat = $this->get_set_pat_rel_values_retrive($res->fields["desc_u"],"pat");										
				$ptInfoDiaDesc=(!empty($strDiabetesTxtPat))? $ptInfoDiaDesc." ".stripslashes($strDiabetesTxtPat) : "";
				$rvs = ($flagDiab) ? "\n".$ptInfoDiaDesc : $rvs."\n".$ptInfoDiaDesc;				
			}
		}
		$rvs = nl2br($rvs);
		return $rvs;
	}
	
	//get daibetic info
	public function getDiabMedInfo($pid, $flagDiab=false){
		$delimiter = '~|~';
		$rvs = "";		
		//General Medicine
		$sql = "select any_conditions_you,any_conditions_relative,desc_u,desc_r,diabetes_values from general_medicine where patient_id='".$pid."' ";
		$res = imw_fetch_object($sql);
		if($res != false){
			$any_conditions_u1_arr1=explode(" ",trim(str_replace(","," ",$res->fields["any_conditions_you"])));
			//for($epr=0;$epr<=sizeof($any_conditions_u1_arr1);$epr++){				
			//Diabeties
			if(in_array("3",$any_conditions_u1_arr1)){
				//$rvs.="\nDiet ".date("m-Y");
				$strDiabetesIdTxtPat =  get_set_pat_rel_values_retrive($res->fields["diabetes_values"],'pat',$delimiter);				
				$rvs.="Yes ".date("m-Y")." ".$strDiabetesIdTxtPat;
				//$rvs.="\nDiabetes ";				
				//$ptInfoDiaDesc="Diabetes";
				$strDiabetesTxtPat = "";
				$strDiabetesTxtPat = $this->get_set_pat_rel_values_retrive($res->fields["desc_u"],"pat");										
				$ptInfoDiaDesc=(!empty($strDiabetesTxtPat))? $ptInfoDiaDesc." ".stripslashes($strDiabetesTxtPat) : "";
				$rvs = ($flagDiab) ? "\n".$ptInfoDiaDesc : $rvs."\n".$ptInfoDiaDesc;				
			}else {
				$rvs = "No";
			}
		}
		$rvs = nl2br($rvs);
		
		$allArr = array($rvs);
		return $allArr;
	}

	//get CC History of Patient as it appear in chart note cc history textarea
	public function isEyeProbChanged($pid, $eyeProbsCur, $elem_acutePrev){	
		$ret = true;
		$elem_acutePrev = str_replace(array("\n","\r"), "", $elem_acutePrev);
		$eyeProbsCur = str_replace(array("\n","\r"), "", $eyeProbsCur);	
		if(!empty($elem_acutePrev) ){	
			$ret = ($elem_acutePrev == $eyeProbsCur) ? false : true;		
		}else{
			//check last entered
			$sql = "SELECT acuteEyeProbs FROM chart_left_cc_history 
					WHERE patient_id='".$pid."' 
					AND acuteEyeProbs != '' 
					ORDER BY form_id DESC LIMIT 0,1 ";
			$res = imw_fetch_object($sql);
			if($res != false){
				$elem_acutePrev = stripslashes($res->fields["acuteEyeProbs"]);
				$elem_acutePrev = str_replace(array("\n","\r"), "", $elem_acutePrev);
				$ret = ($elem_acutePrev == $eyeProbsCur) ? false : true;
			}
		}	
		return $ret;
	}
	//this function is copied from medical history common file
	function get_set_pat_rel_values_retrive($dbValue,$methodFor,$delimiter = "~|~",$hifenOptional= ""){	
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
		}  	
	   
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
		
			$result = imw_fetch_assoc($medSql);
			
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
		$vsRes = imw_query($vsQry) or die(imw_error());
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
				if($Respiration) {
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
				while($row_ins_case=imw_fetch_assoc($res_ins_case)){
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
	//===========PACHYMETRY DATA WORK ENDS HERE==========================================
}
?>