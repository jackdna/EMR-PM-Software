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
 Purpose: MySQLi Extension Functions
 Access Type: Indirect Access.
*/

namespace IMW;

/**
 * DB
 *
 * Main DB connection Class
 */
class Consent
{
	public function __construct($db_obj = '',$cur_sec = 1){
		if(empty($db_obj) == false){
			$this->dbh_obj = $db_obj;
		}
		if(empty($cur_sec) == false){
			$this->current_sec = $cur_sec;
		}
	}
	// Function To Get Consent and Insert Into Temp Table //
	public function getConsentForm($patientId,$consentId,$packageId=0){

		$get_mad_conent_qry_="SELECT * from consent_form WHERE consent_form_id='".$consentId."'  LIMIT 0,1";
		$get_mas_consent_qry=$this->dbh_obj->imw_query($get_mad_conent_qry_);
		$get_mas_consent_arr=$this->dbh_obj->imw_fetch_assoc($get_mas_consent_qry);
		
		$consent_form_name = $get_mas_consent_arr['consent_form_name'];
		$consent_form_content = $get_mas_consent_arr['consent_form_content'];
		$consent_form_content = $this->consentReplacement($consent_form_content,$patientId);
		
		$todayDate=explode('-',date('Y-m-d'));
		
			{
			//check do we have saved form for that date in temporary folder
			$qry_check_form="select consent_form_content_data from patient_consent_form_information_app where patient_id='$patientId' and chart_procedure_id = 0 and consent_form_id='$consentId' and YEAR(form_created_date)='".$todayDate[0]."' and MONTH(form_created_date)='".$todayDate[1]."' and DAY(form_created_date)='".$todayDate[2]."' and consent_form_content_data!='' And operator_id=0";
			$QUERY_CHECK=$this->dbh_obj->imw_query($qry_check_form);
			if($this->dbh_obj->imw_num_rows($QUERY_CHECK)>=1)
			{
				//get and send saved data
				$DATA=$this->dbh_obj->imw_fetch_assoc($QUERY_CHECK);
				$consent_form_content= ($DATA['consent_form_content_data']);
				
			}
			else
			{
				
				{
					if($consentId)
					{
						if($protocol==''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }
						if($protocol=='https://')$consent_form_content = str_ireplace('http://','https://',$consent_form_content);
					
						$ret=$this->dbh_obj->imw_query("insert into patient_consent_form_information_app set consent_form_id='$consentId',
								consent_form_name='".html_entity_decode(addslashes(trim($consent_form_name)))."',
								patient_id='$patientId',
								operator_id='0',
								package_category_id='$packageId',
								form_created_date='".date('Y-m-d H:i:s')."',
								consent_form_content_data='".html_entity_decode($this->dbh_obj->imw_escape_string(trim($consent_form_content)))."'");
					}
				}
			}
		}
		return $consent_form_content;
	}
	public function consentReplacement($consent_form_content_data='',$patientId=0){
		
		//---- get Patient details ---------
		$qryPatInfo = "select *,date_format(DOB,'%m-%d-%Y') as pat_dob,date_format(date,'%m-%d-%Y') as reg_date
				from patient_data where id = '".$patientId."'";
		$patRes = $this->dbh_obj->imw_query($qryPatInfo);
		$rowpatientRes=$this->dbh_obj->imw_fetch_assoc($patRes);
		
		$patientDetails[]=$rowpatientRes;
		$patient_initial= substr($patientDetails[0]['fname'],0,1);
		$patient_initial .= substr($patientDetails[0]['lname'],0,1);
		list($year, $month, $day) = explode('-',$patientDetails[0]['DOB']);
		$pat_date = $year."-".$month."-".$day;
		//$patient_age = ($pat_date-(date("Y-m-d")));
			
		//--- get physician name --------
		$pro_id = $patientDetails[0]['providerID'];
		if($pro_id){
			$qryProv = "select concat(lname,', ',fname) as name,mname,fname,lname,pro_title,pro_suffix from users where id = '$pro_id'";
			$resProv = $this->dbh_obj->imw_query($qryProv);
			$rowProv = $this->dbh_obj->imw_fetch_assoc($resProv);
			$phyDetail[]=$rowProv;
			$phy_name = ucwords(trim($phyDetail[0]['name'].' '.$phyDetail[0]['mname']));
			$phy_fname = ucwords(trim($phyDetail[0]['fname']));
			$phy_mname = ucwords(trim($phyDetail[0]['mname']));
			$phy_lname = ucwords(trim($phyDetail[0]['lname']));
			$phy_name_suffix = ucwords(trim($phyDetail[0]['pro_suffix']));
		}
		
		//--- get reffering physician name --------
		$primary_care_id = $patientDetails[0]['primary_care_id'];
		if($primary_care_id){
			$qryRef = "select concat(LastName,', ',FirstName) as name , MiddleName, FirstName, LastName, Title, specialty, physician_phone, Address1, Address2, ZipCode, City, State from refferphysician
					where physician_Reffer_id = '$primary_care_id'";
			$resRef=$this->dbh_obj->imw_query($qryRef);
			$rowRef=$this->dbh_obj->imw_fetch_assoc($resRef);		
			$reffPhyDetail[] = $rowRef;
			$reffer_name = ucwords(trim($reffPhyDetail[0]['name'].' '.$reffPhyDetail[0]['MiddleName']));
			$refPhyAddress="";
			$refPhyAddress .= (!empty($reffPhyDetail[0]['Address1'])) ? trim($reffPhyDetail[0]['Address1']) : "";
			$refPhyAddress .= (!empty($reffPhyDetail[0]['Address2'])) ? "<br>".trim($reffPhyDetail[0]['Address2']) : "";
		}
		
		//--- get primary care physician detail --------
		$pcp_id = $patientDetails[0]['primary_care_phy_id'];
		if($pcp_id){
			$qryPcp = "select pcp.Title as pcpTitle,pcp.FirstName as pcpFName,pcp.MiddleName as pcpMName,pcp.LastName as pcpLName, 
					pcp.Address1 as pcpAddress1,pcp.Address2 as pcpAddress2,pcp.City as pcpCity,pcp.State as pcpState,pcp.ZipCode as pcpZipCode
					 from refferphysician pcp
					 where pcp.physician_Reffer_id = '".$pcp_id."'";
			$resPcp=$this->dbh_obj->imw_query($qryPcp);
			$rowPcp=$this->dbh_obj->imw_fetch_assoc($resPcp);
			$pcpPhyDetail = $rowPcp;
			$pcpAddress="";
			$pcpAddress .= (!empty($pcpPhyDetail[0]['pcpAddress1'])) ? trim($pcpPhyDetail[0]['pcpAddress1']) : "";
			$pcpAddress .= (!empty($pcpPhyDetail[0]['pcpAddress2'])) ? "<br>".trim($pcpPhyDetail[0]['pcpAddress2']) : "";
		}
		//--- get pos facility name -------
		$default_facility=$patientDetails[0]['default_facility'];
		if($default_facility){
			$qryFac="select facilityPracCode from pos_facilityies_tbl 
					where pos_facility_id = '$default_facility'";
			$resFac=$this->dbh_obj->imw_query($qryFac);
			$rowFac=$this->dbh_obj->imw_fetch_assoc($resFac);
			$posFacilityDetail[]=$rowFac;
			$pos_facility_name=$posFacilityDetail[0]['facilityPracCode'];
		}
		//--- get responsible party information ------
		$qryResp="select *,date_format(dob,'%m-%d-%Y') as res_dob 
				from resp_party where patient_id = '$patientId'";
		$resResp=$this->dbh_obj->imw_query($qryResp);
		$rowResp=$this->dbh_obj->imw_fetch_assoc($resResp);
		$resDetails[]=$rowResp;
		//--- get epmoyee detail of patient ---
		$qryEmp="select * from employer_data where pid = '$patientId'";
		$resEmp=$this->dbh_obj->imw_query($qryEmp);
		$rowEmp=$this->dbh_obj->imw_fetch_assoc($resEmp);
		$empDetails[]=$rowEmp;
		//get Primary insurence data//
		
		$qryGetInsPriData = "select provider as priInsComp,
							 policy_number as priPolicyNumber,
							 group_number as priGroupNumber,
							 CONCAT(subscriber_fname,'&nbsp;',subscriber_lname)as priSubscriberName,
							 subscriber_relationship as priSubscriberRelation,
							 subscriber_DOB as priSubscriberDOB,
							 subscriber_ss as priSubscriberSS,
							 subscriber_phone as priSubscriberPhone,
							 subscriber_street as priSubscriberStreet,
							 subscriber_city as priSubscriberCity,
							 subscriber_state as priSubscriberState,
							 subscriber_postal_code as priSubscriberZip,
							 subscriber_employer as priSubscriberEmployer
							 from insurance_data
							 where pid = '".$patientId."' and 
							 ins_caseid > 0 and
							 type = 'primary' 
							 and actInsComp = 1
							 and provider > 0
							 ";
		$rsGetInsPri = $this->dbh_obj->imw_query($qryGetInsPriData);
		$numRowGetInsPriData = $this->dbh_obj->imw_num_rows($rsGetInsPri);
		if($numRowGetInsPriData>0){
			$rsGetInsPriData = $this->dbh_obj->imw_fetch_assoc($rsGetInsPri);
			extract(($rsGetInsPriData));
			$qryGetInsPriProvider = "select name as priInsCompName, 
									 CONCAT(contact_address,if(TRIM(city)!='',CONCAT(' ',city),''),if(TRIM(State)!='',CONCAT(', ',State),''),if(TRIM(Zip)!='',CONCAT(' ',Zip),''),if(TRIM(zip_ext)!='',CONCAT('-',zip_ext),'')) AS priInsCompAddress 
									 from insurance_companies where id = $priInsComp";
			$resInsPriProvider = $this->dbh_obj->imw_query($qryGetInsPriProvider);
			$numRowGetInsPriProvider = $this->dbh_obj->imw_num_rows($resInsPriProvider);
			if($numRowGetInsPriProvider>0){
				$rsGetInsPriProvider = $this->dbh_obj->imw_fetch_assoc($resInsPriProvider);
				extract(($rsGetInsPriProvider));
			}
		}
		//end get Primary insurence data//
		//get Secondary insurence data//
		$qryGetInsSecData = "select provider as secInsComp,
							 policy_number as secPolicyNumber,
							 group_number as secGroupNumber,
							 CONCAT(subscriber_fname,'&nbsp;',subscriber_lname)as secSubscriberName,
							 subscriber_relationship as secSubscriberRelation,
							 subscriber_DOB as secSubscriberDOB,
							 subscriber_ss as secSubscriberSS,
							 subscriber_phone as secSubscriberPhone,
							 subscriber_street as secSubscriberStreet,
							 subscriber_city as secSubscriberCity,
							 subscriber_state as secSubscriberState,
							 subscriber_postal_code as secSubscriberZip,
							 subscriber_employer as secSubscriberEmployer
							 from insurance_data
							 where pid = '".$patientId."' and 
							 ins_caseid > 0 and
							 type = 'secondary' 
							 and actInsComp = 1
							 and provider > 0
							 ";
		$rsInsSecData = $this->dbh_obj->imw_query($qryGetInsSecData);
		$numRowGetInsSecData = $this->dbh_obj->imw_num_rows($rsInsSecData);
		if($numRowGetInsSecData>0){
			$rsGetInsSecData = $this->dbh_obj->imw_fetch_assoc($rsInsSecData);
			extract(($rsGetInsSecData));
			$qryGetInsSecProvider = "select name as secInsCompName,
									 CONCAT(contact_address,if(TRIM(city)!='',CONCAT(' ',city),''),if(TRIM(State)!='',CONCAT(', ',State),''),if(TRIM(Zip)!='',CONCAT(' ',Zip),''),if(TRIM(zip_ext)!='',CONCAT('-',zip_ext),'')) AS secInsCompAddress  
									 from insurance_companies where id = $secInsComp";
			$rsInsSecProvider = $this->dbh_obj->imw_query($qryGetInsSecProvider);
			$numRowGetInsSecProvider = $this->dbh_obj->imw_num_rows($rsInsSecProvider);
			if($numRowGetInsSecProvider>0){
				$rsGetInsSecProvider = $this->dbh_obj->imw_fetch_assoc($rsInsSecProvider);
				extract(($rsGetInsSecProvider));
			}
		}
		//end get Secondary insurence data//
		
		//get patient Appointment data//
		$qryGetApptData = "select sa.sa_app_start_date,DATE_FORMAT(sa.sa_app_start_date, '%a %m/%d/%y') as appDate,
							 TIME_FORMAT(sa.sa_app_starttime,'%h:%i %p') as appTime,
							 sp.proc as ptProc
							 from schedule_appointments sa 
							 INNER JOIN slot_procedures sp ON sp.id = sa.procedureid  
							 where sa.sa_patient_id = '".$patientId."' and 
							 sa.sa_app_start_date <= current_date() 
							 order by sa.sa_app_start_date DESC 
							 LIMIT 1 
							 ";
		$rsApptData = $this->dbh_obj->imw_query($qryGetApptData);
		$numRowGetApptData = $this->dbh_obj->imw_num_rows($rsApptData);
		if($numRowGetApptData>0){
			$rsGetApptData = $this->dbh_obj->imw_fetch_assoc($rsApptData);
			extract(($rsGetApptData));	
		}
		
		
		$reffer_name = ucwords(trim($reffPhyDetail[0]['name'].' '.$reffPhyDetail[0]['MiddleName']));
		$refPhyAddress="";
		$refPhyAddress .= (!empty($reffPhyDetail[0]['Address1'])) ? trim($reffPhyDetail[0]['Address1']) : "";
		$refPhyAddress .= (!empty($reffPhyDetail[0]['Address2'])) ? "<br>".trim($reffPhyDetail[0]['Address2']) : "";
		
		
		//======================================================================================================//
			
		//--- change value between curly brackets -------	
			$consent_form_content_data = str_ireplace('{PATIENT NAME TITLE}',ucwords($patientDetails[0]['title']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PATIENT FIRST NAME}',ucwords($patientDetails[0]['fname']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{MIDDLE NAME}',ucwords($patientDetails[0]['mname']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{LAST NAME}',ucwords($patientDetails[0]['lname']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{SEX}',ucwords($patientDetails[0]['sex']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{DOB}',ucwords($patientDetails[0]['pat_dob']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{AGE}',$patient_age,$consent_form_content_data);	
			$consent_form_content_data = str_ireplace('{PATIENT SS}',ucwords($patientDetails[0]['ss']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PHYSICIAN NAME}',ucwords($phy_name),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PHYSICIAN FIRST NAME}',ucwords($phy_fname),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PHYSICIAN MIDDLE NAME}',ucwords($phy_mname),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PHYSICIAN LAST NAME}',ucwords($phy_lname),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PHYSICIAN NAME SUFFIX}',ucwords($phy_name_suffix),$consent_form_content_data);
			
			$consent_form_content_data = str_ireplace('{MARITAL STATUS}',ucwords($patientDetails[0]['status']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{ADDRESS1}',ucwords($patientDetails[0]['street']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{ADDRESS2}',ucwords($patientDetails[0]['street2']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{HOME PHONE}',ucwords($patientDetails[0]['phone_home']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{EMERGENCY CONTACT}',ucwords($patientDetails[0]['contact_relationship']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{EMERGENCY CONTACT PH}',ucwords($patientDetails[0]['phone_contact']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{MOBILE PHONE}',ucwords($patientDetails[0]['phone_cell']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{WORK PHONE}',ucwords($patientDetails[0]['phone_biz']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PATIENT CITY}',ucwords($patientDetails[0]['city']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PATIENT STATE}',ucwords($patientDetails[0]['state']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PATIENT ZIP}',ucwords($patientDetails[0]['postal_code']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{REGISTRATION DATE}',ucwords($patientDetails[0]['reg_date']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{REFFERING PHY.}',ucwords($reffer_name),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{POS FACILITY}',ucwords($pos_facility_name),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{DRIVING LICIENSE}',ucwords($patientDetails[0]['driving_licence']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{HEARD ABOUT US}',ucwords($patientDetails[0]['heard_abt_us']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{HEARD ABOUT US DETAIL}',$patientDetails[0]['heard_abt_desc'],$consent_form_content_data);
			
			$consent_form_content_data = str_ireplace('{EMAIL ADDRESS}',$patientDetails[0]['email'],$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{USER DEFINE 1}',$patientDetails[0]['genericval1'],$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{USER DEFINE 2}',$patientDetails[0]['genericval2'],$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PATIENT MRN}',$patientDetails[0]['External_MRN_1'],$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PATIENT MRN2}',$patientDetails[0]['External_MRN_2'],$consent_form_content_data);
			
			$languageShow 			   = str_ireplace("Other -- ","",$patientDetails[0]['language']);
			$raceShow				   = trim($patientDetails[0]["race"]);
			$otherRace				   = trim($patientDetails[0]["otherRace"]);
			if($otherRace) { 
				$raceShow			   = $otherRace;
			}
			$ethnicityShow			   = trim($patientDetails[0]["ethnicity"]);			
			$otherEthnicity			   = trim($patientDetails[0]["otherEthnicity"]);
			if($otherEthnicity) { 
				$ethnicityShow		   = $otherEthnicity;
			}
		
			$consent_form_content_data = str_ireplace('{RACE}',$raceShow,$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{LANGUAGE}',$languageShow,$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{ETHNICITY}',$ethnicityShow,$consent_form_content_data);
			
			
			//$consent_form_content_data = str_ireplace('{REL INFO}',getPtReleaseInfoNames($patientId),$consent_form_content_data);
			//new variable added
			if($patientDetails[0]['postal_code']) {
				$consent_form_content_data = str_ireplace('{STATE ZIP CODE}',ucwords($patientDetails[0]['state'].' '.$patientDetails[0]['postal_code']),$consent_form_content_data);
			}
			$consent_form_content_data = str_ireplace('{REF PHYSICIAN TITLE}',		trim(ucwords($reffPhyDetail[0]['Title'])),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{REF PHYSICIAN FIRST NAME}',	trim(ucwords($reffPhyDetail[0]['FirstName'])),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{REF PHYSICIAN LAST NAME}',	trim(ucwords($reffPhyDetail[0]['LastName'])),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{REF PHY SPECIALITY}',		trim(ucwords($reffPhyDetail[0]['specialty'])),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{REF PHY PHONE}',			trim(ucwords($reffPhyDetail[0]['physician_phone'])),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{REF PHY STREET ADDR}',		$refPhyAddress,$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{REF PHY CITY}',				trim(ucwords($reffPhyDetail[0]['City'])),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{REF PHY STATE}',			trim(ucwords($reffPhyDetail[0]['State'])),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{REF PHY ZIP}',				trim(ucwords($reffPhyDetail[0]['ZipCode'])),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PCP STREET ADDR}',$pcpAddress,$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PCP City}',	$pcpPhyDetail[0]['pcpCity'],$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PCP State}',$pcpPhyDetail[0]['pcpState'],$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PCP ZIP}',	$pcpPhyDetail[0]['pcpZipCode'],$consent_form_content_data);	
			
			
			
			//--- change res party value -----
			$consent_form_content_data = str_ireplace('{RES.PARTY TITLE}',ucwords($resDetails[0]['title']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($resDetails[0]['fname']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{RES.PARTY MIDDLE NAME}',ucwords($resDetails[0]['mname']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{RES.PARTY LAST NAME}',ucwords($resDetails[0]['lname']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{RES.PARTY DOB}',ucwords($resDetails[0]['res_dob']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{RES.PARTY SS}',ucwords($resDetails[0]['ss']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{RES.PARTY SEX}',ucwords($resDetails[0]['sex']),$consent_form_content_data);
			$strToShowRelation = $resDetails[0]['relation'];
			if(strtolower($resDetails[0]['relation']) == "doughter"){
				$strToShowRelation = "Daughter";
			}
			$consent_form_content_data = str_ireplace('{RES.PARTY RELATION}',ucwords($strToShowRelation),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS1}',ucwords($resDetails[0]['address']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{RES.PARTY ADDRESS2}',ucwords($resDetails[0]['address2']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{RES.PARTY HOME PH.}',ucwords($resDetails[0]['home_ph']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{RES.PARTY WORK PH.}',ucwords($resDetails[0]['work_ph']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{RES.PARTY MOBILE PH.}',ucwords($resDetails[0]['mobile']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{RES.PARTY CITY}',ucwords($resDetails[0]['city']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{RES.PARTY STATE}',ucwords($resDetails[0]['state']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{RES.PARTY ZIP}',ucwords($resDetails[0]['zip']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{RES.PARTY MARITAL STATUS}',ucwords($resDetails[0]['marital']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{RES.PARTY DD NUMBER}',ucwords($resDetails[0]['licence']),$consent_form_content_data);
			//--- change epmoyee detail of patient ---
			$consent_form_content_data = str_ireplace('{PATIENT OCCUPATION}',ucwords($patientDetails[0]['occupation']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PATIENT EMPLOYER}',ucwords($empDetails[0]['name']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{OCCUPATION ADDRESS1}',ucwords($empDetails[0]['street']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{OCCUPATION ADDRESS2}',ucwords($empDetails[0]['street2']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{OCCUPATION CITY}',ucwords($empDetails[0]['city']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{OCCUPATION STATE}',ucwords($empDetails[0]['state']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{OCCUPATION ZIP}',ucwords($empDetails[0]['postal_code']),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{MONTHLY INCOME}','$'.number_format($patientDetails[0]['monthly_income'],2),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{DATE}',date('m-d-Y'),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{DATE_F}',date("F d, Y"),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{TIME}',date('h:i A'),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{OPERATOR NAME}',ucwords(trim($operator_name)),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{OPERATOR INITIAL}',ucwords($operator_initial),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PATIENT INITIAL}',ucwords($patient_initial),$consent_form_content_data);
			//replacing Primary insurence data
			$consent_form_content_data = str_ireplace('{PRIMARY INSURANCE COMPANY}',ucwords($priInsCompName),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PRI INS ADDR}',ucwords($priInsCompAddress),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PRIMARY POLICY #}',ucwords($priPolicyNumber),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PRIMARY GROUP #}',ucwords($priGroupNumber),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PRIMARY SUBSCRIBER NAME}',ucwords($priSubscriberName),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PRIMARY SUBSCRIBER RELATIONSHIP}',ucwords($priSubscriberRelation),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PRIMARY BIRTHDATE}',ucwords($priSubscriberDOB),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PRIMARY SOCIAL SECURITY}',ucwords($priSubscriberSS),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PRIMARY PHONE}',ucwords($priSubscriberPhone),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PRIMARY ADDRESS}',ucwords($priSubscriberStreet),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PRIMARY CITY}',ucwords($priSubscriberCity),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PRIMARY STATE}',ucwords($priSubscriberState),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PRIMARY ZIP}',ucwords($priSubscriberZip),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PRIMARY EMPLOYER}',ucwords($priSubscriberEmployer),$consent_form_content_data);
			//
			//replacing Secondary insurence data
			$consent_form_content_data = str_ireplace('{SECONDARY INSURANCE COMPANY}',ucwords($secInsCompName),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{SEC INS ADDR}',ucwords($secInsCompAddress),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{SECONDARY POLICY #}',ucwords($secPolicyNumber),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{SECONDARY GROUP #}',ucwords($secGroupNumber),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{SECONDARY SUBSCRIBER NAME}',ucwords($secSubscriberName),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{SECONDARY SUBSCRIBER RELATIONSHIP}',ucwords($secSubscriberRelation),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{SECONDARY BIRTHDATE}',ucwords($secSubscriberDOB),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{SECONDARY SOCIAL SECURITY}',ucwords($secSubscriberSS),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{SECONDARY PHONE}',ucwords($secSubscriberPhone),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{SECONDARY ADDRESS}',ucwords($secSubscriberStreet),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{SECONDARY CITY}',ucwords($secSubscriberCity),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{SECONDARY STATE}',ucwords($secSubscriberState),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{SECONDARY ZIP}',ucwords($secSubscriberZip),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{SECONDARY EMPLOYER}',ucwords($secSubscriberEmployer),$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PatientID}',$patientId,$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{Appt Date}',$appDate,$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{Appt Time}',$appTime,$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{Appt Proc}',$ptProc,$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{PHYSICIAN SIGNATURE}',"______________",$consent_form_content_data);
			$consent_form_content_data = str_ireplace('{WITNESS SIGNATURE}',"{SIGNATURE}",$consent_form_content_data);
			//$consent_form_content_data = str_ireplace($GLOBALS['iDoc_from']."/",$GLOBALS['idoc_external_ip'],$consent_form_content_data);
			$consent_form_content_data = str_ireplace("https://www.imwcloud.net/","",$consent_form_content_data);
						
			$row_arr = explode('{START APPLET ROW}',$consent_form_content_data);
			
			$arrStr = array("{TEXTBOX_XSMALL}","{TEXTBOX_SMALL}","{TEXTBOX_MEDIUM}","{TEXTBOX_LARGE}");
					
			for($j = 0;$j<count($arrStr);$j++)
			{
				if($arrStr[$j] == '{TEXTBOX_XSMALL}')
				{
					$name = 'xsmall';
					$size = 1;
				}
				else if($arrStr[$j] == '{TEXTBOX_SMALL}')
				{
					$name = 'small';
					$size = 30;
				}
				else if($arrStr[$j] == '{TEXTBOX_MEDIUM}')
				{
					$name = 'medium';
					$size = 60;
				}
				else if($arrStr[$j] == '{TEXTBOX_LARGE}')
				{
					$name = 'large';
					$size = 120;
					
				}
				$repVal = '';
				if(substr_count($consent_form_content_data,$arrStr[$j]) > 1)
				{
					
					if($arrStr[$j] == '{TEXTBOX_XSMALL}' || $arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}')
					{
						$c = 1;
						$arrExp = explode($arrStr[$j],$consent_form_content_data);
						
						for($p = 0;$p<count($arrExp)-1;$p++)
						{
							$repVal .= $arrExp[$p].'<input type="text" class="form-control " name="'.$name.$c.'" value="" size="'.$size.'" maxlength="'.$size.'" id="'.$name.$c.'"><div><span id="span_'.$name.$c.'" style="display:none"> </span></div>';
							$c++;
						}
						$repVal .= end($arrExp);
						$consent_form_content_data = $repVal;
					}
					else if($arrStr[$j] == '{TEXTBOX_LARGE}')
					{
						$c = 1;
						$arrExp = explode($arrStr[$j],$consent_form_content_data);
						
						for($p = 0;$p<count($arrExp)-1;$p++)
						{
							$repVal .= $arrExp[$p].'<textarea rows="2" cols="100" name="'.$name.$c.'" id="'.$name.$c.'" class="form-control ">  </textarea>
										<div><span id="span_'.$name.$c.'" style="display:none"> </span></div>';
							$c++;
						}
						$repVal .= end($arrExp);
						$consent_form_content_data = $repVal;
					}
				}
				else
				{
					if($arrStr[$j] == '{TEXTBOX_XSMALL}' || $arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}') {
						$repVal = str_ireplace($arrStr[$j],'<input type="text" class="form-control " name="'.$name.'" value="" size="'.$size.'" maxlength="'.$size.'" id="'.$name.'"><div><span id="span_'.$name.'" style="display:none"> </span></div>',$consent_form_content_data);
						$consent_form_content_data = $repVal;
					}
					else if($arrStr[$j] == '{TEXTBOX_LARGE}')
					{
						$repVal = str_ireplace($arrStr[$j],'<textarea rows="2" cols="100" name="'.$name.'" id="'.$name.'" class="form-control ">  </textarea>
									<div><span id="span_'.$name.'" style="display:none"> </span></div>',$consent_form_content_data);
						$consent_form_content_data = $repVal;
					}
				}
				 
			}
			
			$sig_arr = explode('{SIGNATURE}',$consent_form_content_data);
			$sig_data = '';
			$ds = 1;
			//$phy_arr = explode('{PHYSICIAN SIGNATURE}',$sig_arr1[0]);	
			//$sig_arr=array_merge($sig_arr1,$phy_arr);
			//===============================//
			for($s=1;$s<count($sig_arr);$s++,$ds++){
				$i=$ds;
				$canvasId = $hidCanvasSigData = "";
				$canvasId = "canvas".$ds;	
				$hidCanvasSigData = "hiddCanvasSigDataId".$ds;		
				$sig_data = '<br><table class="alignLeft" style="border:none;" id="consentSigWinId'.$ds.'">
								<tr>
									<td style="width:254px;height:90px;" class="consentObjectBeforSign" >
									<a name="typ_sig" href="signIpadConsent_'.date("m_d_y_H_i_s").'_'.$patientId."_".$s.'">
										<div id="signIpadConsent_'.date("m_d_y_H_i_s").'_'.$patientId."_".$s.'" style="HEIGHT: 90px; WIDTH: 254px;border:solid 1px; border-color:#FF9900;"></div></a></td>
								</tr>
							</table>		
						';
							
				$str_data = $sig_arr[$s];
				$sig_arr[$s] = $sig_data;
				$sig_arr[$s] .= $str_data;
				$hiddenFields[] = true;
			}
			$consent_form_content = implode(' ',$sig_arr);
			
			//--- get all content of consent forms -------	
			
			$javascript='<endofcode></endofcode>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
						<script type="text/javascript">
						$(".form-control").keyup(function(){
							if($(this).val()!=""){
								var id_t = $(this).attr("id"); 
								var val_t= $(this).val();								
								$("#span_"+id_t).html(val_t);
							} 
						});
						$(document).ready(function(e) {
							reloadValues()
						});
						
						function reloadValues()
						{
							$(".form-control").each(function(index, element) {
								var id_t = $(this).attr("id");
								var val_t= $("#span_"+id_t).html();
								$(this).val(val_t);
							});
							
						}
						</script>';
			$consent_form_content.= $javascript;
			
		return $consent_form_content;
	}
	
	public function save_patient_consent_form($type,$patientId,$consentId,$signImg=''){
	
		$result = array();
		$todayDate=explode('-',date('Y-m-d'));
		
		//get image name
		$file_name = $signImg;
		$filePathOrg = data_path()."/PatientId_".$patientId."/consent_forms";
		//if dir not exist then create it
		if(!is_dir($filePathOrg)){
			$mk_dir=mkdir($filePathOrg,0777);
			if(!$mk_dir){
				//file_put_contents('errorLog.txt',"unable to create folder: ".$filePathOrg." \n", FILE_APPEND);
				echo "Unable To Create Folder: ".$filePathOrg; die();
			}
		}
		$filePathOrg.="/".$file_name;
		// To Update Sign In Consent //
		if($type=='sign'){
			$resultIns = false;
			// To Handle base64 encoded image //
			$image = file_get_contents('php://input');
			if($image!=''){
				$done = file_put_contents($filePathOrg,base64_decode($image));
				$resultIns = $this->update_patient_consent_form($patientId,$consentId,'sign','',$file_name);
			}
			$result['Sign']=$resultIns;
			//return $result;
			
		}
		// To Update Consent //
		else if($type=='content'){	
			$resultIns = false;
			// code to handle html response //
			$content = file_get_contents('php://input');
			$content = base64_decode($content);
			if($content!=''){
				$resultIns = $this->update_patient_consent_form($patientId,$consentId,'content',$content,'');
			}
			$result['Content']=$resultIns;
			//return $result;
		}
		// To Save Consent //
		else if($type=='save'){
			$resultIns = array();
			// code to handle html response from APP //
			$content = file_get_contents('php://input');
			$content = base64_decode($content);
			$resultIns = $this->update_patient_consent_form($patientId,$consentId,'save',$content,'');
			
			$result = $resultIns;
			//return $result;
		}
		return $result;
	}
	public function update_patient_consent_form($patientId,$consentId,$typ,$content,$signImg){
		$todayDate=explode('-',date('Y-m-d'));
		// check do we have saved form for today's date //
		$qry_ch="SELECT * FROM patient_consent_form_information_app WHERE patient_id='$patientId' AND consent_form_id='$consentId' AND YEAR(form_created_date)='".$todayDate[0]."' AND MONTH(form_created_date)='".$todayDate[1]."' AND DAY(form_created_date)='".$todayDate[2]."' AND operator_id='0'";
		$QUERY_CHECK=$this->dbh_obj->imw_query($qry_ch);
		$resultIns = false;
		if($this->dbh_obj->imw_num_rows($QUERY_CHECK)>=1){
			// get and send saved data //
			$DATA=$this->dbh_obj->imw_fetch_assoc($QUERY_CHECK);
			if($typ=='sign'){
				$signName = explode('.',$signImg);
				$path = $GLOBALS['webroot'].'/data/'.PRACTICE_PATH."/PatientId_".$patientId."/consent_forms/".$signImg;
				$consent_form_content=html_entity_decode($DATA['consent_form_content_data']);
				// Putting the sign in the consent forms //
				$consent_form_content = str_replace('<a name="typ_sig" href="'.$signName[0].'">','<img src="'.$path.'" height="94px;" width="176px;">',$consent_form_content);
				$consent_form_content = str_replace('<div id="'.$signName[0].'" style="HEIGHT: 90px; WIDTH: 254px;border:solid 1px; border-color:#FF9900;"></div></a>','',$consent_form_content);
				$consent_form_content=$this->dbh_obj->imw_escape_string(trim($consent_form_content));
				
				$updateQuery = "UPDATE patient_consent_form_information_app SET 
								consent_form_content_data='".$consent_form_content."'
						  		WHERE form_information_id=".$DATA['form_information_id'];
				$resultIns = $this->dbh_obj->imw_query($updateQuery);
				return $resultIns;
			}
			elseif($typ=='content'){
				$consent_form_content=$this->dbh_obj->imw_escape_string(trim($content));
				$resultIns = $this->dbh_obj->imw_query("UPDATE patient_consent_form_information_app SET consent_form_content_data='$consent_form_content' WHERE form_information_id=".$DATA['form_information_id']);
				return $resultIns;
			}
			elseif($typ=='save'){
				$datatemp=stripcslashes(html_entity_decode(trim($DATA['consent_form_content_data'])));
				// To get HTML FORM from APP //
				if($content!='' ){
					$datatemp = $content;
				}
				
				$htmlArr=explode("<endofcode></endofcode>",$datatemp);
				
				$htmlArr[0] = preg_replace('/<input type="text" class="form-control " name=\"(.*?)\" value=\"(.*?)\" size=\"(.*?)\" maxlength=\"(.*?)\" id=\"(.*?)\">/','',$htmlArr[0]);
				
				$htmlArr[0] = preg_replace('/<textarea rows="2" cols="100" name=\"(.*?)\" id=\"(.*?)\" class="form-control ">/','',$htmlArr[0]);
				
				$htmlArr[0] = preg_replace('/<\/textarea>/','',$htmlArr[0]);
				
				$htmlArr[0] = preg_replace('/<div><span id="(.*?)" style="display:none">/','',$htmlArr[0]);
				
				$htmlArr[0] = preg_replace('/<\/span><\/div>/','',$htmlArr[0]);
				
				$consentDATA = ($this->dbh_obj->imw_escape_string(trim($htmlArr[0])));
				
				$query="INSERT INTO patient_consent_form_information SET 
									consent_form_id='".$consentId."', 
									consent_form_name ='".$this->dbh_obj->imw_escape_string($DATA['consent_form_name'])."', 
									patient_id='".$patientId."',
									form_status='0',
									operator_id='0',
									consent_form_content_data='".($consentDATA)."',
									iportal_patient_id='".$patientId."',
									form_created_date='".date('Y-m-d H:i:s')."',
									package_category_id='".$DATA['package_category_id']."'";
				
				$resultIns = $this->dbh_obj->imw_query($query);
				$imw_insert_id = $this->dbh_obj->imw_insert_id();
				if($resultIns){
					$qtring="DELETE FROM patient_consent_form_information_app WHERE form_information_id=".$DATA['form_information_id'];
					$resultIns = $this->dbh_obj->imw_query($qtring);
				}
				return array('flag'=>$resultIns,'id'=>$imw_insert_id);
			}
		}
		return $resultIns;
	}
	
	public function printPackage($patientId,$packageId=0){
		
		$consentFormContentArr = '';
		if($packageId!=0 && $packageId!=''){
			$package_query = "SELECT package_consent_form from consent_package WHERE package_category_id='".$packageId."'  LIMIT 0,1";
			$packageResultSet = $this->dbh_obj->imw_query($package_query);
			$packageAssoc = $this->dbh_obj->imw_fetch_assoc($packageResultSet);
			
			$consentQuery = "SELECT * from consent_form WHERE consent_form_id IN (".$packageAssoc['package_consent_form'].")";
			$consentResultSet = $this->dbh_obj->imw_query($consentQuery);
			while($consentAssoc = $this->dbh_obj->imw_fetch_assoc($consentResultSet)){
				
				$consentFormContent = $this->consentReplacement($consentAssoc['consent_form_content'],$patientId);
				
				$htmlArr=explode("<endofcode></endofcode>",$consentFormContent);
				
				$htmlArr[0] = preg_replace('/<input type="text" class="form-control " name=\"(.*?)\" value=\"(.*?)\" size=\"(.*?)\" maxlength=\"(.*?)\" id=\"(.*?)\">/','',$htmlArr[0]);
				
				$htmlArr[0] = preg_replace('/<textarea rows="2" cols="100" name=\"(.*?)\" id=\"(.*?)\" class="form-control ">/','',$htmlArr[0]);
				
				$htmlArr[0] = preg_replace('/<\/textarea>/','',$htmlArr[0]);
				
				$htmlArr[0] = preg_replace('/<div><span id="(.*?)" style="display:none">/','',$htmlArr[0]);
				
				$htmlArr[0] = preg_replace('/<\/span><\/div>/','',$htmlArr[0]);
				
				$consentFormContentArr.= '<page>'.$htmlArr[0].'</page>';
				
			}
		}
		return $consentFormContentArr;
	}
}
?>