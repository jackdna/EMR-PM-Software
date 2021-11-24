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
use DOMDocument;
use SimpleXMLElement;

include_once($GLOBALS['srcdir'].'/classes/SaveFile.php');
use SaveFile;
/**
 * CCDA
 *
 * CCDA Generation Class
 */
class CCDA{
	public $dbh_obj = '';
	public $patientId = '';
	public $operator_id = '';
	public $dos = '';
	public $startDate = false;
	public $endDate = false;
	public $form_id = '';
	public $currentDate = '';
	public $umls_db = 'umls_2013';
	public $token = '';
	
	public function __construct($db_obj = '', $patient_id = '', $dos = '', $start_date = '', $end_date = '', $token_val = ''){
		if(empty($db_obj) == false){
			$this->dbh_obj = $db_obj;
		}
		
		if(empty($patient_id) == false){
			$this->patientId = $patient_id;
		}
		
		if(empty($dos) == false){
			$this->dos = $dos;
		}
		
		if(empty($start_date) == false){
			$this->startDate = $start_date;
		}
		
		if(empty($end_date) == false){
			$this->endDate = $end_date;
		}
		
		if(empty($token_val) == false){
			$this->token = $token_val;
		}
		
		$this->operator_id = $this->get_operator_id($this->token);
		$this->form_id = $this->get_form_id();
		$this->currentDate = date("YmdHis");
	}
	
	public function get_operator_id($token){
		if(empty($token)) return false;
		$return_token = false;
		$sql = 'SELECT user_id FROM fmh_api_token_log where token = "'.$token.'"';
		$res = $this->dbh_obj->imw_query($sql);
		if($res && $this->dbh_obj->imw_num_rows($res) > 0){
			$row = $this->dbh_obj->imw_fetch_assoc($res);
			$return_token = (empty($row['user_id']) == false) ? $row['user_id'] : false;
		}
		return $return_token;
	}
	
	public function get_form_id(){
		$return_data = '';
		if(empty($this->dos) == false){	//If DOS exist check DOS is valid
			$return_data = 'no_dos';
		}
		$qry = $this->dbh_obj->imw_query("SELECT id FROM chart_master_table where date_of_service = '".$this->dos."' AND patient_id = ".$this->patientId." ORDER BY date_of_service DESC LIMIT 1");
		if($this->dbh_obj->imw_num_rows($qry) > 0){
			$row = $this->dbh_obj->imw_fetch_assoc($qry);
			$return_data = $row['id'];	
		}
		return $return_data;
	}
	
	public function get_ccda(){
		$dbh = $this->dbh_obj;
		$return_data = array();
		$usr_operator_id = ($this->operator_id && empty($this->operator_id) == false) ? $this->operator_id : '';	
			/* Patient Block */
			$XMLpatient_data = '';
				$qry = "select patient_data.*,users.fname as ptProviderFName,users.mname as ptProviderMName,users.lname as ptProviderLName,users.user_npi as ptProviderNPI,
					refferphysician.Title as ptRefferPhyTitle,refferphysician.FirstName as ptRefferPhyFName,refferphysician.MiddleName as ptRefferPhyMName,
					refferphysician.LastName as ptRefferPhyLName,refferphysician.physician_phone as ptRefferPhyPhone
					from patient_data LEFT JOIN users on users.id = patient_data.providerID
					LEFT JOIN refferphysician ON refferphysician.physician_Reffer_id = patient_data.primary_care_id 
					where patient_data.id = '".$this->patientId."'";
					
				$rsPatient = $dbh->imw_query($qry);
				$rowPatient = $dbh->imw_fetch_assoc($rsPatient);
					
					$XMLpatient_data = '<recordTarget>';
					$XMLpatient_data .= '<patientRole>';
					if($rowPatient['ss'] != "")
						$XMLpatient_data .= '<id extension="'.$rowPatient['ss'].'" root="2.16.840.1.113883.4.1"/>';
					else
						$XMLpatient_data .= '<id root="2.16.840.1.113883.4.6"/>';
				  
					$XMLpatient_data .= '<addr use="HP">';
				
					if($rowPatient['street'] != "")
						$XMLpatient_data .= '<streetAddressLine>'.$rowPatient['street'].'</streetAddressLine>';
					else
						$XMLpatient_data .= '<streetAddressLine nullFlavor="NI"/>';
					
					if($rowPatient['street2'] != "")
						$XMLpatient_data .= '<streetAddressLine>'.$rowPatient['street2'].'</streetAddressLine>';
					else
						$XMLpatient_data .= '<streetAddressLine nullFlavor="NI"/>';
					
					if($rowPatient['city'] != "")
						$XMLpatient_data .= '<city>'.$rowPatient['city'].'</city>';
					else
						$XMLpatient_data .= '<city nullFlavor="NI"/>';
					
					if($rowPatient['state'] != "")
						$XMLpatient_data .= '<state>'.$rowPatient['state'].'</state>';
					else
						$XMLpatient_data .= '<state nullFlavor="NI"/>';
					
					if($rowPatient['postal_code'] != "")
						$XMLpatient_data .= '<postalCode>'.$rowPatient['postal_code'].'</postalCode>';
					else
						$XMLpatient_data .= '<postalCode nullFlavor="NI"/>';
					
					$XMLpatient_data .= '<country>US</country>';
					$XMLpatient_data .= '</addr>';
					 
					if($rowPatient['phone_home'] != "")
						$XMLpatient_data .= '<telecom value="tel:+1-'.$this->core_phone_format($rowPatient['phone_home']).'" use="HP"/>';
					else
						$XMLpatient_data .= '<telecom nullFlavor="NI" use="HP"/>';
					 
					 if($rowPatient['phone_biz'] != "")
						$XMLpatient_data .= '<telecom value="tel:+1-'.$this->core_phone_format($rowPatient['phone_biz']).'" use="WP"/>';

					 if($rowPatient['phone_cell'] != "")
						 $XMLpatient_data .= '<telecom value="tel:+1-'.$this->core_phone_format($rowPatient['phone_cell']).'" use="MC"/>';
				
					 if($rowPatient['email'] != "")
						 $XMLpatient_data .= '<telecom use="HP" value="mailto:'.$rowPatient['email'].'"/>';
					 
					 $XMLpatient_data .= '<patient>';
					 $XMLpatient_data .= '<name>';
					 
					 if($rowPatient['suffix'] != ""){
						 $XMLpatient_data .= '<prefix>'.$rowPatient['title'].'</prefix>';
					 }
					 $XMLpatient_data .= '<given qualifier="CL">'.$rowPatient['fname'].'</given>';
					 
					 if($rowPatient['mname']!=""){
						$XMLpatient_data .= '<given>'.$rowPatient['mname'].'</given>';
					 }
					 if($rowPatient['mname_br']!=""){
						 $XMLpatient_data .= '<given qualifier="BR">'.$rowPatient['mname_br'].'</given>';
					 }
					 $XMLpatient_data .= '<family>'.$rowPatient['lname'].'</family>';
						 
					 if($rowPatient['suffix'] != ""){
						 $XMLpatient_data .= '<suffix>'.$rowPatient['suffix'].'</suffix>';
					 }
					
					$XMLpatient_data .= '</name>';
					
					$arrGender = array();
					$arrGender = $this->gender_srh(strtolower($rowPatient['sex']));
					if($arrGender['code']!="" && $arrGender['display_name']!=""){	
					$XMLpatient_data .= '<administrativeGenderCode code="'.$arrGender['code'].'" codeSystem="2.16.840.1.113883.5.1"
														displayName="'.$arrGender['display_name'].'" codeSystemName="AdministrativeGender"/>';
					}else{
					$XMLpatient_data .= '<administrativeGenderCode nullFlavor="NI"/>';	
					}
					$dob = str_replace("-","",$rowPatient['DOB']);
					if($dob != "00000000"){
					$XMLpatient_data .= '<birthTime value="'.$dob.'"/>';
					}else{
						$XMLpatient_data .= '<birthTime nullFlavor="NI"/>';
					}
					
					$arrMarried = array();
					$arrMarried = $this->marr_status_srh(strtolower($rowPatient['status']));
					if($arrMarried['code']!="" && $arrMarried['display_name']!=""){
					$XMLpatient_data .= '<maritalStatusCode code="'.$arrMarried['code'].'" displayName="'.$arrMarried['display_name'].'"
												codeSystem="2.16.840.1.113883.5.2"
												codeSystemName="MaritalStatus"/>';
					}
					
					//PATIENT RACE, NEW LOGIC FOR RACE-EXTENION----
					$PT_race_heirarcy = $this->get_race_heirarcy($rowPatient['race'],$rowPatient['race_code']);
					$PT_race_name_joined = '';
					if(count($PT_race_heirarcy)>0){
						for($i=0; $i < count($PT_race_heirarcy); $i++){
							if($i==0){
								if( empty($PT_race_heirarcy[$i]['cdc_code']) || $PT_race_heirarcy[$i]['cdc_code']==='ASKU')
								{
									// $XMLpatient_data .= '<raceCode nullFlavor="ASKU"/>';		//~~~//
									$XMLpatient_data .= '<raceCode nullFlavor="ASKU" displayName="Unknown" />';
								}
								else
								{
									$XMLpatient_data .= '<raceCode code="'.$PT_race_heirarcy[$i]['cdc_code'].'" displayName="'.$PT_race_heirarcy[$i]['race_name'].'" codeSystem="2.16.840.1.113883.6.238" codeSystemName="Race and Ethnicity - CDC"/>';
									$PT_race_name_joined = $PT_race_heirarcy[$i]['race_name'];
								}
							}else{
								$PT_race_name_joined .= ' '.$PT_race_heirarcy[$i]['race_name'];
								$XMLpatient_data .= '<sdtc:raceCode code="'.$PT_race_heirarcy[$i]['cdc_code'].'" displayName="'.$PT_race_name_joined.'" codeSystem="2.16.840.1.113883.6.238" codeSystemName="Race and Ethnicity - CDC"/>';

							}
						}
					}else{
						$XMLpatient_data .= '<raceCode nullFlavor="NI"/>';
					}		

					$arrEthnicity = array();
					$arrEthnicity = $this->ethnicity_srh(strtolower($rowPatient['ethnicity']));
					if($arrEthnicity['code']!="" && $arrEthnicity['display_name']!=""){		
						if( strtolower($arrEthnicity['display_name']) == "unknown" ){		
							$XMLpatient_data .= '<ethnicGroupCode nullFlavor="UNK" displayName="Unknown" />';
						}
						else
						{
							$XMLpatient_data .= '<ethnicGroupCode code="'.$arrEthnicity['code'].'" displayName="'.$arrEthnicity['display_name'].'" codeSystem="2.16.840.1.113883.6.238" codeSystemName="Race and Ethnicity - CDC"/>';
						}
					}else{
						$XMLpatient_data .= '<ethnicGroupCode nullFlavor="NI"/>';
					}
					$arrLanguage = array();
					if(trim($rowPatient['lang_code'])==''){
						$arrLanguage = $this->language_srh(strtolower($rowPatient['language']));
					}else{
						if(trim($rowPatient['lang_code'])=='eng') $rowPatient['lang_code']='en';
						$arrLanguage['display_name'] = trim($rowPatient['language']);
						$arrLanguage['code'] 		 = trim($rowPatient['lang_code']);
					}
					if($arrLanguage['code']!="" && $arrLanguage['display_name']!=""){					
					$XMLpatient_data .= '<languageCommunication>
											<languageCode code="'.$arrLanguage['code'].'"/>
											<modeCode code="ESP" displayName="Expressed spoken" codeSystem="2.16.840.1.113883.5.60" codeSystemName="LanguageAbilityMode"/>
											<preferenceInd value="true"/>
										</languageCommunication>
										';
					}
					$XMLpatient_data .= '</patient>';
					$XMLpatient_data .= '</patientRole>';
					$XMLpatient_data .= '</recordTarget>';
			
				//$xml_string .= $XMLpatient_data;
				
			/* Patient Block End*/
			
			/* Care Team Members */
				$XML_documentationof_data = '';
				/*Get Providers IDS for the Chart note - $form_id*/
				$providerIds = array();
				$providersGroups = array();

				$where = " `id`='".$this->form_id."'";
				
				if( empty($this->form_id) && $this->startDate && $this->endDate)
				{
					$where = " `date_of_service` BETWEEN '".$this->startDate."' AND '".$this->endDate."'";
				}

				$sql = "SELECT `provIds` FROM `chart_master_table` WHERE ".$where." AND `patient_id`='".$this->patientId."'";
				$resp = $dbh->imw_query($sql);
				if( $resp && $dbh->imw_num_rows($resp) === 1 )
				{
					$resp = $dbh->imw_fetch_assoc($resp);
					$providerIds = $resp['provIds'];
					$providerIds = explode(',', $providerIds);
					
					$providerIds = array_map(array($this,'convertToInt'), $providerIds);
					if(count($providerIds) > 0){
						$providerIds = array_filter($providerIds);	
						$providerIds = array_unique($providerIds);
					}
					
					if( is_array($providerIds) && count($providerIds) > 0 )
					{
						$sql = 'SELECT `user_group_id`, `id` FROM `users` WHERE `id` IN('.implode(',', $providerIds).')';
						$resp = $dbh->imw_query($sql);
						if( $resp && $dbh->imw_num_rows($resp)>0 )
						{
							while( $row = $dbh->imw_fetch_assoc($resp) )
							{
								$providersGroups[$row['id']] = (int)$row['user_group_id'];
							}
						}
					}
				}
				
					$sql_patient = "SELECT * FROM patient_data WHERE id = '".$this->patientId."' LIMIT 0,1";
					$result_patient = $dbh->imw_query($sql_patient);
					$row_patient 	= $dbh->imw_fetch_assoc($result_patient);
					$providerID = $row_patient['providerID'];
					
					$tempProviderID = false;
					$tempTechnicianID = false;
					
					if( count($providerIds) > 0)
					{
						foreach($providersGroups as $groupKey=>$groupVal)
						{
							if( $groupVal === 2 && $tempProviderID === false )
							{
								$tempProviderID = (int)$groupKey;
							}
							elseif( $groupVal === 5 && $tempTechnicianID === false )
							{
								$tempTechnicianID = (int)$groupKey;
							}
						}
						
						if( $tempProviderID !== false && $tempProviderID > 0)
						{
							$providerID = $tempProviderID;
						}
					}
					
					$XML_documentationof_data = '<documentationOf>';
					$XML_documentationof_data .= '<serviceEvent classCode="PCPR">';
					$XML_documentationof_data .= '<effectiveTime>
												 <low value="'.$this->currentDate.'"/>
												 <high value="'.$this->currentDate.'"/>
												 </effectiveTime>';
												 
						$qry_provider = "SELECT * FROM users WHERE id = '".$providerID."'";  // PRIMARY PHYSICIAN
						$res_provider = $dbh->imw_query($qry_provider);
						if($dbh->imw_num_rows($res_provider) > 0){
							$row_provider = $dbh->imw_fetch_assoc($res_provider);
							
							$XML_documentationof_data .= '<performer typeCode="PRF">
							<functionCode code="PCP" displayName="Primary Care Provider" codeSystem="2.16.840.1.113883.5.88" codeSystemName="participationFunction">
								<originalText>Primary Care Provider</originalText>
							</functionCode>
							<assignedEntity>
							<!-- NPI 12345 -->
							';
							if($row_provider['user_npi'] != ""){
								$XML_documentationof_data .= '<id extension="'.$row_provider['user_npi'].'" root="2.16.840.1.113883.4.6"/>
							';
							}else{
								$XML_documentationof_data .= '<id nullFlavor="NI"/>
								';
							}
							
							if($row_provider['default_facility'] > 0){
								$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$row_provider['default_facility']."'";
							}
							else{
								$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
							}
							$res_facility = $dbh->imw_query($qry_facility);
							$row_facility = $dbh->imw_fetch_assoc($res_facility);
							
							$PCP_represented_location = '';
							$XML_documentationof_data .= '<addr use="WP">
							';
							if($row_facility['street'] != ""){
								$PCP_represented_location .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>
								';
							}
							if($row_facility['city'] != ""){
								$PCP_represented_location .= '<city>'.$row_facility['city'].'</city>
								';
							}
							if($row_facility['state'] != ""){
								$PCP_represented_location .= '<state>'.$row_facility['state'].'</state>
								';
							}
							if($row_facility['postal_code'] != ""){
								$PCP_represented_location .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>
								';
							}
							$PCP_represented_location .= '<country>US</country>';
							
							$XML_documentationof_data .= $PCP_represented_location.'
														</addr>';
							if($row_facility['phone'] != ""){
								$XML_documentationof_data .= '<telecom use="WP" value="tel:+1-'.$this->core_phone_format($row_facility['phone']).'"/>
								';
								$PCP_represented_location = '<telecom use="WP" value="tel:+1-'.$this->core_phone_format($row_facility['phone']).'"/><addr>'.$PCP_represented_location.'</addr>';
							}else{
								$XML_documentationof_data .= '<telecom nullFlavor="NI"/>
								';
								$PCP_represented_location = '<telecom nullFlavor="NI"/><addr>'.$PCP_represented_location.'</addr>';
							}
							$XML_documentationof_data .= '<assignedPerson>
															<name>
																<given>'.$row_provider['fname'].'</given>
																<family>'.$row_provider['lname'].'</family>
															</name>
															</assignedPerson>
															<representedOrganization>
															<id nullFlavor="NI"/>
															<name>'.$row_facility['name'].'</name>
															'.$PCP_represented_location.'
															</representedOrganization>';
							
							$XML_documentationof_data .= '</assignedEntity>';
							$XML_documentationof_data .= '</performer>';
						}
						else{
							$XML_documentationof_data .= '<performer typeCode="PRF">';
							$XML_documentationof_data .= '<functionCode code="PCP" displayName="Provider Care Provider" codeSystem="2.16.840.1.113883.5.88" codeSystemName="participationFunction"/>';
							$XML_documentationof_data .= '<assignedEntity>';
							$XML_documentationof_data .= '<!-- NPI 12345 -->';
							$XML_documentationof_data .= '<id nullFlavor="NI"/>';
							$XML_documentationof_data .= '<addr>';
							$XML_documentationof_data .= '<streetAddressLine nullFlavor="NI"/>';
							$XML_documentationof_data .= '<city nullFlavor="NI"/>';
							$XML_documentationof_data .= '<state nullFlavor="NI"/>';
							$XML_documentationof_data .= '<postalCode nullFlavor="NI"/>';
							$XML_documentationof_data .= '<country nullFlavor="NI"/>';
							$XML_documentationof_data .= '</addr>';
							$XML_documentationof_data .= '<telecom nullFlavor="NI"/>';								
							$XML_documentationof_data .= '<assignedPerson>';
							$XML_documentationof_data .= '<name>';
							$XML_documentationof_data .= '<given nullFlavor="NI"/>';
							$XML_documentationof_data .= '<family nullFlavor="NI"/>';
							$XML_documentationof_data .= '</name>';
							$XML_documentationof_data .= '</assignedPerson>';
							$XML_documentationof_data .= '</assignedEntity>';
							$XML_documentationof_data .= '</performer>';
						}
					
						$qry_reff = "SELECT * FROM refferphysician WHERE physician_Reffer_id = '".$row_patient['primary_care_id']."'";  			
						// REFERRING PHYSICIAN
						$res_reff = $dbh->imw_query($qry_reff);
						if($dbh->imw_num_rows($res_reff) > 0){
							$row_reff = $dbh->imw_fetch_assoc($res_reff);
							
							$XML_documentationof_data .= '<performer typeCode="PRF">
															<time><low nullFlavor="UNK"/></time>
														<assignedEntity>
														<!-- NPI 12345 -->
														';
							if($row_reff['NPI'] != "")
								$XML_documentationof_data .= '<id extension="'.$row_reff['NPI'].'" root="2.16.840.1.113883.4.6"/>';
							else
								$XML_documentationof_data .= '<id extension="'.$row_reff['physician_Reffer_id'].'" root="1.3.6.1.4.1.22812.4.99930.4"/>';
							
							if($row_reff['default_facility'] > 0){
								$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$row_reff['default_facility']."'";
							}
							else{
								$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
							}
							$res_facility = $dbh->imw_query($qry_facility);
							$row_facility = $dbh->imw_fetch_assoc($res_facility);
							
							$XML_documentationof_data .= '<addr use="WP">';
							
							if($row_facility['street'] != "")
								$XML_documentationof_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>
								';
					
							if($row_facility['city'] != "")
								$XML_documentationof_data .= '<city>'.$row_facility['city'].'</city>
								';
						
							if($row_facility['state'] != "")
								$XML_documentationof_data .= '<state>'.$row_facility['state'].'</state>
								';
							
							if($row_facility['postal_code'] != "")
								$XML_documentationof_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>
								';
						
							$XML_documentationof_data .= '<country>US</country>
							';
							$XML_documentationof_data .= '</addr>
							';
							
							if($row_facility['phone'] != ""){
								$XML_documentationof_data .= '<telecom use="WP" value="tel:+1-'.$this->core_phone_format($row_facility['phone']).'"/>
								';
							}else{
								$XML_documentationof_data .= '<telecom nullFlavor="NI"/>';
							}
							
							$XML_documentationof_data .= '<assignedPerson>';
							$XML_documentationof_data .= '<name>';
							
							if($row_reff['Title']!="")
								$XML_documentationof_data .= '<prefix>'.$row_reff['Title'].'</prefix>';
					
							$XML_documentationof_data .= '<given>'.$row_reff['FirstName'].'</given>';
							$XML_documentationof_data .= '<family>'.$row_reff['LastName'].'</family>';
							$XML_documentationof_data .= '</name>';
							
							$XML_documentationof_data .= '</assignedPerson>';
							$XML_documentationof_data .= '</assignedEntity>';
							$XML_documentationof_data .= '</performer>';
						}
						
						$qry_reff = "SELECT * FROM refferphysician WHERE physician_Reffer_id = '".$row_patient['primary_care_phy_id']."'"; // PCP PHYSICIAN
						$res_reff = $dbh->imw_query($qry_reff);
						if($dbh->imw_num_rows($res_reff) > 0){
							$row_reff = $dbh->imw_fetch_assoc($res_reff);
							
							$XML_documentationof_data .= '<performer typeCode="PRF">';
							$XML_documentationof_data .= '<functionCode code="PCP" displayName="Primary Care Physician" codeSystem="2.16.840.1.113883.5.88" 	  																	
															codeSystemName="participationFunction"/>';
							$XML_documentationof_data .= '<assignedEntity>';
							$XML_documentationof_data .= '<!-- NPI 12345 -->';
							if($row_reff['NPI'] != "")
							$XML_documentationof_data .= '<id extension="'.$row_reff['NPI'].'" root="2.16.840.1.113883.4.6"/>';
							else
							$XML_documentationof_data .= '<id nullFlavor="NI"/>';
							
							if($row_reff['default_facility'] > 0){
								$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$row_reff['default_facility']."'";
							}
							else{
								$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
							}
							$res_facility = $dbh->imw_query($qry_facility);
							$row_facility = $dbh->imw_fetch_assoc($res_facility);
							
							$XML_documentationof_data .= '<addr use="WP">';
							
							if($row_facility['street'] != "")
								$XML_documentationof_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';

							if($row_facility['city'] != "")
								$XML_documentationof_data .= '<city>'.$row_facility['city'].'</city>';
					
							if($row_facility['state'] != "")
								$XML_documentationof_data .= '<state>'.$row_facility['state'].'</state>';
						
							if($row_facility['postal_code'] != "")
								$XML_documentationof_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
							
							$XML_documentationof_data .= '<country>US</country>';
							$XML_documentationof_data .= '</addr>';
							
							if($row_facility['phone'] != "")
								$XML_documentationof_data .= '<telecom use="WP" value="tel:+1-'.$this->core_phone_format($row_facility['phone']).'"/>';
							$XML_documentationof_data .= '<assignedPerson>';
							$XML_documentationof_data .= '<name>';
							
							if($row_reff['Title']!="")
								$XML_documentationof_data .= '<prefix>'.$row_reff['Title'].'</prefix>';
							
							$XML_documentationof_data .= '<given>'.$row_reff['FirstName'].'</given>';
							$XML_documentationof_data .= '<family>'.$row_reff['LastName'].'</family>';
							$XML_documentationof_data .= '</name>';
							
							$XML_documentationof_data .= '</assignedPerson>';
					
							$XML_documentationof_data .= '</assignedEntity>';
							$XML_documentationof_data .= '</performer>';
						}
					
					
						$qry_provider = "SELECT * FROM users WHERE id = '".(($tempTechnicianID!==false)?$tempTechnicianID:$row_patient['assigned_nurse'])."'";  // PRIMARY PHYSICIAN
						
						$res_provider = $dbh->imw_query($qry_provider);
						if($dbh->imw_num_rows($res_provider) > 0){
							$row_provider = $dbh->imw_fetch_assoc($res_provider);
							
							$XML_documentationof_data .= '<performer typeCode="PRF">';
							$XML_documentationof_data .= '<functionCode code="NASST" displayName="nurse assistant" codeSystem="2.16.840.1.113883.5.88" 	  																	
															codeSystemName="participationFunction"/>';
							$XML_documentationof_data .= '<assignedEntity>';
							$XML_documentationof_data .= '<!-- NPI 12345 -->';
							if($row_provider['user_npi'] != "")
							$XML_documentationof_data .= '<id extension="'.$row_provider['user_npi'].'" root="2.16.840.1.113883.4.6"/>';
							else
							$XML_documentationof_data .= '<id nullFlavor="NI"/>';
							
							
							if($row_provider['facility'] > 0){
								$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$row_provider['facility']."'";
							}
							else{
								$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
							}
							$res_facility = $dbh->imw_query($qry_facility);
							$row_facility = $dbh->imw_fetch_assoc($res_facility);
							
							$XML_documentationof_data .= '<addr use="WP">';
							if($row_facility['street'] != "")
							$XML_documentationof_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
							if($row_facility['city'] != "")
							$XML_documentationof_data .= '<city>'.$row_facility['city'].'</city>';
							if($row_facility['state'] != "")
							$XML_documentationof_data .= '<state>'.$row_facility['state'].'</state>';
							if($row_facility['postal_code'] != "")
							$XML_documentationof_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
							$XML_documentationof_data .= '<country>US</country>';
							$XML_documentationof_data .= '</addr>';
							if($row_facility['phone'] != "")
							$XML_documentationof_data .= '<telecom use="WP" value="tel:+1-'.$this->core_phone_format($row_facility['phone']).'"/>';
							$XML_documentationof_data .= '<assignedPerson>';
							$XML_documentationof_data .= '<name>';
							$XML_documentationof_data .= '<given>'.$row_provider['fname'].'</given>';
							$XML_documentationof_data .= '<family>'.$row_provider['lname'].'</family>';
							$XML_documentationof_data .= '</name>';
							$XML_documentationof_data .= '</assignedPerson>';
							$XML_documentationof_data .= '</assignedEntity>';
							$XML_documentationof_data .= '</performer>';
						}	
					
					$XML_documentationof_data .= '</serviceEvent>';
					$XML_documentationof_data .= '</documentationOf>';
	
			/* Care Team Members End */
			
			/* Refferals To Others */
				$XML_referral_to_providers = '';
				if(empty($this->form_id) == false){
					$form_id = $this->form_id;
					$dos = $this->dos;
					
					$sql_cmt = "SELECT date_of_service FROM chart_master_table WHERE id = '".$form_id."'";
					$res_cmt = $dbh->imw_query($sql_cmt);
					$row_cmt = $dbh->imw_fetch_assoc($res_cmt);
					$dos = $row_cmt['date_of_service'];
					
					$sql = "SELECT * FROM chart_schedule_test_external WHERE patient_id = '".$this->patientId."'";
					if($form_id != ""){
						$sql .= " AND schedule_date >= '".$dos."'";
					}else{
						$sql .= " AND schedule_date >= '".date('Y-m-d')."'";
					}
					$sql .= " AND deleted_by = '0'";
					$sql .= " AND appoint_test = 'Referral'";
					$res = $dbh->imw_query($sql);
					$row = $dbh->imw_fetch_assoc($res);
					
					if($row['reff_phy'] !=""){
					$arr = explode(",",$row['reff_phy']);
					$arrFirst = explode(" ",trim($arr[0]));
					$arrSecond = explode(" ",trim($arr[1]));
					if(count($arrFirst)>1){
						$title = $arrFirst[0];
						$fname = $arrFirst[1];
					}else{
						$fname = $arrFirst[0];
					}
					
					if(count($arrSecond)>1){
						$lname = $arrSecond[0];
						$mname = $arrSecond[1];
					}else{
						$lname = $arrSecond[0];
					}
					$XML_referral_to_providers = '<componentOf>
													<encompassingEncounter>
														<id extension="'.$form_id.'" root="2.16.840.1.113883.4.6"/>
														<effectiveTime value="'.str_replace("-","",$row['schedule_date']).'" />
														<encounterParticipant typeCode="ATND">
															<assignedEntity>
																<id root="2.16.840.1.113883.4.6"/>
																<assignedPerson>
																	<name>';
																	if(isset($title) && $title!="")
																		$XML_referral_to_providers .= '<prefix>'.$title.'</prefix>';
																	if(isset($fname) && $fname!="")
																		$XML_referral_to_providers .= '<given>'.$fname.'</given>';
																	if(isset($lname) && $lname!="")
																		$XML_referral_to_providers .= '<family>'.$lname.'</family>';
											
											$XML_referral_to_providers .='</name>
																</assignedPerson>
															</assignedEntity>
														</encounterParticipant>
													</encompassingEncounter>
												</componentOf>';
					}
				}
			/* Refferals To Others End */
			
			/* Autor Data */
				$XML_author_data = '';
				$qry_user = "select * from users where id = ".$usr_operator_id.""; //Static
				$res_user = $dbh->imw_query($qry_user);
				$row_user = $dbh->imw_fetch_assoc($res_user);
				$XML_author_data = '<author>';
				$XML_author_data .= '<time value="'.$this->currentDate.'"/>';
				$XML_author_data .= '<assignedAuthor>';
				if($row_user['user_npi'] != "")
				$XML_author_data .= '<id extension="'.$row_user['user_npi'].'" root="2.16.840.1.113883.4.6"/>';
				else
				$XML_author_data .= '<id root="2.16.840.1.113883.4.6"/>';
				
				if($row_user['facility'] > 0){
					$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$row_user['facility']."'";
				}
				else{
					$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
				}
				$res_facility = $dbh->imw_query($qry_facility);
				$row_facility = $dbh->imw_fetch_assoc($res_facility);
				
				$XML_author_data .= '<addr use="WP">';
				if($row_facility['street'] != "")
				$XML_author_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
				if($row_facility['city'] != "")
				$XML_author_data .= '<city>'.$row_facility['city'].'</city>';
				if($row_facility['state'] != "")
				$XML_author_data .= '<state>'.$row_facility['state'].'</state>';
				if($row_facility['postal_code'] != "")
				$XML_author_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
				$XML_author_data .= '<country>US</country>';
				$XML_author_data .= '</addr>';
				
				if($row_facility['phone'] != "")
					$XML_author_data .= '<telecom use="WP" value="tel:+1-'.$this->core_phone_format($row_facility['phone']).'"/>';
				else
					$XML_author_data .= '<telecom nullFlavor="NI" use="WP"/>';
				
				$XML_author_data .= '<assignedPerson>';
				$XML_author_data .= '<name>';
				if($row_user['mname'] != "")
				$XML_author_data .= '<given>'.$row_user['mname'].'</given>';
				$XML_author_data .= '<given qualifier="CL">'.$row_user['fname'].'</given>';
				$XML_author_data .= '<family>'.$row_user['lname'].'</family>';
				$XML_author_data .= '</name>';
				
				$XML_author_data .= '</assignedPerson>';
				$XML_author_data .= '</assignedAuthor>';
				$XML_author_data .= '</author>';
				
			/* Author Data End */
			
			/* Data Enterer */
			$XML_data_enterer_data ='';
				$qry_user = "select * from users where id = ".$usr_operator_id."";
				$res_user = $dbh->imw_query($qry_user);
				$row_user = $dbh->imw_fetch_assoc($res_user);
				$XML_data_enterer_data ='<dataEnterer>';
				$XML_data_enterer_data .='<assignedEntity>';
				if($row_user['user_npi'] != "")
				$XML_data_enterer_data .='<id root="2.16.840.1.113883.19.5" extension="'.$row_user['user_npi'].'"/>';
				else
				$XML_data_enterer_data .= '<id nullFlavor="NAV"/>';
				
				if($row_user['facility'] > 0){
					$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$row_user['facility']."'";
				}
				else{
					$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
				}
				$res_facility = $dbh->imw_query($qry_facility);
				$row_facility = $dbh->imw_fetch_assoc($res_facility);
				
				$XML_data_enterer_data .= '<addr use="WP">';
				if($row_facility['street'] != "")
				$XML_data_enterer_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
				if($row_facility['city'] != "")
				$XML_data_enterer_data .= '<city>'.$row_facility['city'].'</city>';
				if($row_facility['state'] != "")
				$XML_data_enterer_data .= '<state>'.$row_facility['state'].'</state>';
				if($row_facility['postal_code'] != "")
				$XML_data_enterer_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
				$XML_data_enterer_data .= '<country>US</country>';
				$XML_data_enterer_data .= '</addr>';
				if($row_facility['phone'] != "")
				$XML_data_enterer_data .= '<telecom use="WP" value="tel:+1-'.$this->core_phone_format($row_facility['phone']).'"/>';
				else
				$XML_data_enterer_data .= '<telecom nullFlavor="NI" use="WP"/>';
				$XML_data_enterer_data .='<assignedPerson>';
				$XML_data_enterer_data .='<name>';
				$XML_data_enterer_data .='<given>'.$row_user['fname'].'</given>';
				$XML_data_enterer_data .='<family>'.$row_user['lname'].'</family>';
				$XML_data_enterer_data .='</name>';
				
				$XML_data_enterer_data .='</assignedPerson>';
				$XML_data_enterer_data .='</assignedEntity>';
				$XML_data_enterer_data .='</dataEnterer>';
			/* Data Enterer End */
			
			/* Custodian Data */
			$facility = $XML_custodian_data = "";
				if(isset($this->form_id) && $this->form_id!=""){
					$qry = "SELECT sa.sa_facility_id  as facility
							FROM schedule_appointments sa
							JOIN chart_master_table cmt ON cmt.date_of_service = sa.sa_app_start_date
							WHERE sa.sa_patient_id ='".$this->patientId."' 
								AND cmt.id = '".$this->form_id."'";
					$res = $dbh->imw_query($qry);						
					$row = $dbh->imw_fetch_assoc($res);
					$facility = $row['facility'];
					
				}
				if($facility == "" || $facility == "0"){
					$qry = "select default_facility as pos_facility from patient_data where id = '".$this->patientId."'";
					$res = $dbh->imw_query($qry);
					$row = $dbh->imw_fetch_assoc($res);
					$pos_facility = $row['pos_facility'];
					$qry = "SELECT id as facility 
							FROM facility 
							WHERE fac_prac_code = '".$pos_facility."'
							";
					$res = $dbh->imw_query($qry);		
					$row = $dbh->imw_fetch_assoc($res);
					$facility = $row['facility'];
				}
				if($facility > 0){
					$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$facility."'";
				}
				else{
					$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
				}
				$res_facility = $dbh->imw_query($qry_facility);
				$row_facility = $dbh->imw_fetch_assoc($res_facility);
				
				$XML_custodian_data = '<custodian>';
				$XML_custodian_data .= '<assignedCustodian>';
				$XML_custodian_data .= '<representedCustodianOrganization>';
				$XML_custodian_data .= '<id root="1.1.1.1.1.1.1.1.2"/>';
				if($row_facility['name'] != "")
				$XML_custodian_data .= '<name>'.htmlentities($row_facility['name']).'</name>';
				
				if($row_facility['phone'] != "")
				$XML_custodian_data .= '<telecom use="WP" value="tel:+1-'.$this->core_phone_format($row_facility['phone']).'"/>';
				else
				$XML_custodian_data .= '<telecom nullFlavor="NI"/>';
				$XML_custodian_data .= '<addr>';
				if($row_facility['street'] != "")
				$XML_custodian_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
				if($row_facility['city'] != "")
				$XML_custodian_data .= '<city>'.$row_facility['city'].'</city>';
				if($row_facility['state'] != "")
				$XML_custodian_data .= '<state>'.$row_facility['state'].'</state>';
				if($row_facility['postal_code'] != "")
				$XML_custodian_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
				$XML_custodian_data .= '<country>US</country>';
				$XML_custodian_data .= '</addr>';
				
				$XML_custodian_data .= '</representedCustodianOrganization>';
				$XML_custodian_data .= '</assignedCustodian>';
				$XML_custodian_data .= '</custodian>';	
			/* Custodian Data End */
			
			/* Social History */
				$pt_birth_sex_rs = $this->getBirthSexInfo($this->patientId);
				$XML_social_history_section = $XML_smoking_status_entry = $smoking_modified_on = '';
				$smoking_start_dt = $smoking_end_dt = $smoking_modified_on_view = $smoking_start_dt_view = $smoking_end_dt_view = '';
				$qry = "SELECT smoking_status,
						DATE_FORMAT(modified_on,'%m/%d/%Y') as smoking_modified_dt_view, 
						DATE_FORMAT(smoke_start_date,'%m/%d/%Y') as smoking_start_dt_view, 
						DATE_FORMAT(smoke_end_date,'%m/%d/%Y') as smoking_end_dt_view,
						DATE_FORMAT(modified_on,'%Y%m%d') as smoking_modified_dt, 
						DATE_FORMAT(smoke_start_date,'%Y%m%d') as smoking_start_dt, 
						DATE_FORMAT(smoke_end_date,'%Y%m%d') as smoking_end_dt  
						FROM social_history WHERE patient_id = '".$this->patientId."'";		
				$row_social = $dbh->imw_fetch_assoc($dbh->imw_query($qry));	
				$arrTmp = explode('/',$row_social['smoking_status']);
				$smoking_status = trim($arrTmp[1]);
				
				if($smoking_status){
					$smoking_modified_on 	= $row_social['smoking_modified_dt'] != '00000000' ? $row_social['smoking_modified_dt'] : '';
					$smoking_start_dt 		= $row_social['smoking_start_dt'] != '00000000' ? $row_social['smoking_start_dt'] : '';
					$smoking_end_dt 		= $row_social['smoking_end_dt'] != '00000000' ? $row_social['smoking_end_dt'] : '';
					
					$smoking_modified_on_view 	= $row_social['smoking_modified_dt'] != '00000000' ? $row_social['smoking_modified_dt_view'] : '';
					$smoking_start_dt_view 		= $row_social['smoking_start_dt'] != '00000000' ? $row_social['smoking_start_dt_view'] : '';
					$smoking_end_dt_view		= $row_social['smoking_end_dt'] != '00000000' ? $row_social['smoking_end_dt_view'] : '';
				}
				
				$arrSmoking = array();
				$arrSmoking = $this->smoking_status_srh(strtolower($smoking_status));
				$XML_social_history_section = '<component>
												<section>
												<templateId root="2.16.840.1.113883.10.20.22.2.17" extension="2015-08-01"/>
												<templateId root="2.16.840.1.113883.10.20.22.2.17"/>
												<code code="29762-2" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Social History"/>
												<title>SOCIAL HISTORY</title>
												<text>
												<table border = "1" width = "100%">
												<thead>
													<tr>
														<th>Social History Observation</th>
														<th>Description</th>
														<th>Dates Observed</th>
													</tr>
												</thead>
												';
				if($arrSmoking['code']!="" && $arrSmoking['display_name']!=""){						
				$XML_social_history_section .='
										<tbody>
										<tr>
											<td>Smoking Status</td>
											<td>'.$arrSmoking['display_name'].' (SNOMED-CT: '.$arrSmoking['code'].')</td>
											<td>';
				if($smoking_start_dt_view != '') $XML_social_history_section .= $smoking_start_dt_view;
				if($smoking_end_dt_view != '') $XML_social_history_section .= ' - '.$smoking_end_dt_view;
				if($smoking_start_dt_view == '' && $smoking_end_dt_view == '' && $smoking_modified_on_view != '') $XML_social_history_section .= $smoking_modified_on_view;
				$XML_social_history_section .='
											</td>
										</tr>';
				
				
					if($pt_birth_sex_rs){						
						$XML_social_history_section .='		
											<tr>
												<td ID="BirthSexInfo">Birth Sex</td>
												<td>'.$pt_birth_sex_rs['birth_sex'].'</td>
												<td>'.date('F d,Y',strtotime($pt_birth_sex_rs['birth_sex_date'])).'</td>
											</tr>';
					}	
					$XML_social_history_section .= '</tbody>
												';
				}
				$XML_social_history_section .= '</table>
												</text>
												';
						
						$XML_smoking_status_entry .=	'<entry typeCode="DRIV">
															<observation classCode="OBS" moodCode="EVN">
																<!-- ** Smoking Status - Meaningful Use (V2) ** -->
																<templateId root="2.16.840.1.113883.10.20.22.4.78" extension="2014-06-09"/>
																<templateId root="2.16.840.1.113883.10.20.22.4.78"/>
																<id nullFlavor = "NI"/>
																<!-- code SHALL be 72166-2 for Smoking Status - Meaningful Use (V2) -db -->
																<code code="72166-2" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Tobacco smoking status NHIS"/>
																<statusCode code="completed"/>
																<!-- The effectiveTime reflects when the current smoking status was observed. -->';
					if($smoking_start_dt != '' && $smoking_end_dt != ''){
						$XML_smoking_status_entry .= '<effectiveTime>
														<low value="'.$smoking_start_dt.'"/>
														<high value="'.$smoking_end_dt.'"/>
													</effectiveTime>
						';
					}else if($smoking_start_dt != '' && $smoking_end_dt == ''){
						$XML_smoking_status_entry .= '<effectiveTime value="'.$smoking_start_dt.'"/>
						';
					}else if($smoking_start_dt == '' && $smoking_end_dt == '' && $smoking_modified_on != ''){
						$XML_smoking_status_entry .= '<effectiveTime value="'.$smoking_modified_dt.'"/>
						';
					}else if($smoking_start_dt == '' && $smoking_end_dt == '' && $smoking_modified_on == ''){
						$XML_smoking_status_entry .= '<effectiveTime nullFlavor="NI"/>
						';
					}
					
					$XML_smoking_status_entry .= '<!-- The value represents the patient\'s smoking status currently observed. -->
																<!-- Consol Smoking Status Meaningful Use2 SHALL contain exactly one [1..1] value (CONF:1098-14810), which SHALL be selected from ValueSet Current Smoking Status 2.16.840.1.113883.11.20.9.38 STATIC 2014-09-01 (CONF:1098-14817) -db -->
																<value xsi:type="CD" code="'.$arrSmoking['code'].'" displayName="'.$arrSmoking['display_name'].'" codeSystem="2.16.840.1.113883.6.96"/>
															</observation>
													</entry>';
					if($arrGender['code']!="" && $arrGender['display_name']!=""){	// //~~~//
						$XML_smoking_status_entry .= '
												<!-- Add Birth Sex entry -->
												 <entry>
												  <observation classCode="OBS" moodCode="EVN">
													<templateId root="2.16.840.1.113883.10.20.22.4.200" extension="2016-06-01"/>
													<code code="76689-9" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Sex Assigned At Birth"/>
													<text>
												   <reference value="#BirthSexInfo"/>
													</text>
													<statusCode code="completed"/>
													<effectiveTime value="20150622"/>
													<value code="'.$arrGender['code'].'" codeSystem="2.16.840.1.113883.5.1" xsi:type="CD" displayName="'.$arrGender['display_name'].'"/>
												  </observation>
												 </entry>';
					}
					/* END SMOKING STATUS ENTRY */
					
					/* Birth Sex Entry */
					$birth_sex_entry = '';
					
					if($pt_birth_sex_rs){
						
						$arrgender = $this->gender();
						$pt_birth_status_date = date("Ymd",strtotime($pt_birth_sex_rs['birth_sex_date']));
						$gender_code = $arrgender[$pt_birth_sex_rs['birth_sex']];
						$display_name_pt_birth_sex = $pt_birth_sex_rs['birth_sex'];
						
						$birth_sex_entry .=	'
							<entry>
								<observation classCode="OBS" moodCode="EVN">
								<templateId root="2.16.840.1.113883.10.20.22.4.200" extension="2016-06-01"/>
								<code code="76689-9" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Sex Assigned At Birth"/>
								<text>
									<reference value="#BirthSexInfo"/>
								</text>
								<statusCode code="completed"/>
								<effectiveTime value="'.$pt_birth_status_date.'"/>
								<value code="'.$gender_code.'" codeSystem="2.16.840.1.113883.5.1" xsi:type="CD" displayName="'.$display_name_pt_birth_sex.'"/>
								</observation>
							</entry>
							';
					}else{
						$birth_sex_entry .=	'
								<entry>
								<observation classCode="OBS" moodCode="EVN">
								  <templateId root="2.16.840.1.113883.10.20.22.4.200" extension="2016-06-01"/>
								  <code code="76689-9" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Sex Assigned At Birth"/>
								  <text>
									<reference value="#BirthSexInfo"/>
								  </text>
								  <statusCode code="completed"/>
								  <effectiveTime nullFlavor="NI" />
								  <value nullFlavor="NI" />
								</observation>
							</entry>';
					}
					
					if(empty($birth_sex_entry) === false){
						$XML_smoking_status_entry .= $birth_sex_entry;
					}
				/* Birth Sex $End */
					
					
					
					
				$XML_social_history_section .= $XML_smoking_status_entry;
				$XML_social_history_section .= '</section>
											</component>';	
			/* Social History End */
			
			/* Medication Section */
				$XML_medication_section = $XML_medication_activity_entry = '';
				$XML_medication_section = '<component>';
				$XML_medication_section .= '<section>';
				$XML_medication_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.1.1" extension="2014-06-09"/>';
				$XML_medication_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.1.1"/>';
				$XML_medication_section .= '<code code="10160-0" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="History of medications"/>';
				$XML_medication_section .= '<title>MEDICATIONS</title>';
				$XML_medication_section .= '<text>';
				$XML_medication_section .= '<table border = "1" width = "100%">';
				$XML_medication_section .= '<thead>
										<tr>
											<th>Medication</th>
											<th>Start Date</th>
											<th>End Date</th>
											<th>Route</th>
											<th>Dose</th>
											<th>Frequency</th>                                    
										</tr>
									</thead>';
				
				$arrType = array("1","4");
				$arrMedication = $this->get_medical_data($this->form_id,$arrType ,$this->patientId, $this->startDate, $this->endDate);
				$flag = 0;
				if(count($arrMedication)>0){
					$XML_medication_section .= ' <tbody>';
					//$XML_medication_section .= '<list listType="ordered">';
					foreach($arrMedication as $medication){	
						if($medication['ccda_code'] == ""){
							$arrCCDA = $this->getRXNormCode($medication['title']);
							$ccda_code = $arrCCDA['ccda_code'];
						}else{
							$ccda_code = $medication['ccda_code'];
						}
						if(!in_array($medication['title'],$arrMedHxMedi)){
							$flag = 1;	
							$XML_medication_section .= '<tr>
														<td>
															<content ID = "Med'.$medication['id'].'">'.htmlentities($medication['title']).'    [RxNorm: '.$ccda_code.']</content>
														</td>
														<td>';
							$XML_medication_section .= ($medication['begdate']!="" && $medication['begdate']!='0000-00-00')?date('M d,Y',strtotime($medication['begdate'])):"";
							$XML_medication_section .='</td>
															 <td>';
							$XML_medication_section .=($medication['enddate']!="" && $medication['enddate']!='0000-00-00')?date('M d,Y',strtotime($medication['enddate'])):"";
							$XML_medication_section .='</td>
															<td ID = "MEDROUTE'.$medication['id'].'">'.$medication['med_route'].'</td>
															<td ID = "MEDFORM'.$medication['id'].'">'.$medication['destination'].'</td>
															<td ID = "Instruct'.$medication['id'].'">'.htmlentities($medication['sig']).'</td>
														</tr>
													';	
						}
					}
					$XML_medication_section .= ' </tbody>';
				}
				if($flag == 0)
				{
					$XML_medication_section .= ' <tbody><tr><td colspan="7">No known Medications</td></tr></tbody>';
				}
				
				$XML_medication_section .= '</table>';
				$XML_medication_section .= '</text>';
						$arrType = array("1","4");
						$arrMedication = $this->get_medical_data($this->form_id,$arrType ,$this->patientId, $this->startDate, $this->endDate);
						$flag = 0;
						if(count($arrMedication)>0){
							foreach($arrMedication as $medication){
								if(!in_array($medication['title'],$arrMedHxMedi)){
									$flag = 1;
									
									if($medication['ccda_code']!=""){
										$arrCCDA = $this->getRXNorm_by_code($medication['ccda_code']);
										//if(count($arrCCDA)>0){
										$ccda_code_med = $medication['ccda_code'];
										$ccda_display_name_med = $medication['title'];
										//}					
									}else{
										$arrCCDA = $this->getRXNormCode($medication['title']);
										if(count($arrCCDA)>0){
										$ccda_code_med = $arrCCDA['ccda_code'];
										$ccda_display_name_med = $arrCCDA['ccda_display_name'];
										}
										
									}
									
								/*  BEGIN MEDICATION ENTRY  */
									$XML_medication_activity_entry = '<entry typeCode="DRIV">';
									$XML_medication_activity_entry .= '<substanceAdministration classCode="SBADM" moodCode="EVN">';
									$XML_medication_activity_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.16" extension="2014-06-09"/>';
									$XML_medication_activity_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.16"/>';
									$XML_medication_activity_entry .= '<id nullFlavor="NI"/>';
									$XML_medication_activity_entry .= '<text>';
									
									$XML_medication_activity_entry .= '<reference value="#Med'.$medication['id'].'"/>'.htmlentities($medication['title']).'    [RxNorm: '.$ccda_code_med.']';
									$XML_medication_activity_entry .= '</text>';
									$XML_medication_activity_entry .= '<statusCode code="completed"/>';
									$XML_medication_activity_entry .= '<effectiveTime xsi:type="IVL_TS">';
									if($medication['begdate'] !="" && $medication['begdate']!="0000-00-00")
									$XML_medication_activity_entry .= '<low value="'.str_replace("-","",$medication['begdate']).'"/>';
									else 
									$XML_medication_activity_entry .= '<low nullFlavor="NI"/>';
									if($medication['enddate'] !="" && $medication['enddate']!="0000-00-00")
									$XML_medication_activity_entry .= '<high value="'.str_replace("-","",$medication['enddate']).'"/>';
									else 
									$XML_medication_activity_entry .= '<high nullFlavor="NI"/>';
									$XML_medication_activity_entry .= '</effectiveTime>';
									
									$medDosage = trim($medication['destination']);
									$arrMedDosage = preg_split("/(?<=\d)(?=[a-zA-Z])|(?<=[a-zA-Z])(?=\d)/",preg_replace('/\s/','',$medDosage));
									$medDosageVal = $arrMedDosage[0];
									$medDosageUnit = $arrMedDosage[1];
									/* DYNAMIC ROUTE Medication Route FDA Value Set :: Code System(s): National Cancer Institute (NCI) Thesaurus*/
									if($medication['sites'] == 1 || $medication['sites'] == 2 || $medication['sites'] == 3 ){
										$XML_medication_activity_entry .= '<routeCode code="C38287" codeSystem="2.16.840.1.113883.3.26.1.1"
																		codeSystemName="National Cancer Institute (NCI) Thesaurus"
																		displayName="OPHTHALMIC"/>';
									}else if($medication['sites'] == 4){
										$XML_medication_activity_entry .= '<routeCode code="C38288" codeSystem="2.16.840.1.113883.3.26.1.1"
																		codeSystemName="National Cancer Institute (NCI) Thesaurus"
																		displayName="ORAL"/>';
									}else{
										$routeCode 		= $this->get_med_route_val_code($medication['med_route'],'code');
										$XML_medication_activity_entry .= '<routeCode code="'.$routeCode.'" codeSystem="2.16.840.1.113883.3.26.1.1"
												codeSystemName="National Cancer Institute (NCI) Thesaurus"
												displayName="'.$medication['med_route'].'"/>';
										
									}
									
									if($medication['sites'] == 1){ // LEFT EYE OS
									$XML_medication_activity_entry .= '<approachSiteCode code="362503005" codeSystem="2.16.840.1.113883.6.96"
																		codeSystemName="SNOMED CT"
																		displayName="Entire left eye (body structure)"/>';
									}else if($medication['sites'] == 2){ // RIGHT EYE OD
									$XML_medication_activity_entry .= '<approachSiteCode code="362502000" codeSystem="2.16.840.1.113883.6.96"
																		codeSystemName="SNOMED CT"
																		displayName="Entire right eye (body structure)"/>';
									}
									else if($medication['sites'] == 3){ // BOTH EYES OU
									$XML_medication_activity_entry .= '<approachSiteCode code="244486005" codeSystem="2.16.840.1.113883.6.96"
																		codeSystemName="SNOMED CT"
																		displayName="Entire eye (body structure)"/>';
									}
									else if($medication['sites'] == 4){ // ORAL PO
									$XML_medication_activity_entry .= '<approachSiteCode code="26643006" codeSystem="2.16.840.1.113883.6.96"
																		codeSystemName="SNOMED CT"
																		displayName="taking by mouth"/>';
									}
									if($medDosageVal > 0 && $medDosageUnit != "")
									$XML_medication_activity_entry .= '<doseQuantity value="'.trim($medDosageVal).'"/>';
									else
									$XML_medication_activity_entry .= '<doseQuantity nullFlavor="NI"/>';
									$XML_medication_activity_entry .= '<consumable>';
									$XML_medication_activity_entry .= '<manufacturedProduct classCode="MANU">';
									$XML_medication_activity_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.23" extension="2014-06-09"/>';
									$XML_medication_activity_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.23"/>';
									$XML_medication_activity_entry .= '<manufacturedMaterial>';
																	/* DYNAMIC MEDICATION CODE FROM RXNORM Medication Clinical Drug  */
									
									$XML_medication_activity_entry .= '<code code="'.$ccda_code_med.'"
																		codeSystem="2.16.840.1.113883.6.88"
																		codeSystemName="RxNorm"
																		displayName="'.$ccda_display_name_med.'">';
									$XML_medication_activity_entry .= '</code>';
									$XML_medication_activity_entry .= '</manufacturedMaterial>';
									$XML_medication_activity_entry .= '</manufacturedProduct>';
									$XML_medication_activity_entry .= '</consumable>';
									
										$XML_medication_activity_entry .= '</substanceAdministration>';
										$XML_medication_activity_entry .= '</entry>';
										$XML_medication_section .= $XML_medication_activity_entry;
								}
							}
						}
							
						if($flag == 0){
						/*  BEGIN MEDICATION ENTRY  */
							$XML_medication_activity_entry .= '
							<entry>
								<substanceAdministration moodCode="EVN" classCode="SBADM" negationInd="true">
									<!-- ** Medication Activity (V2) ** -->
									<templateId root="2.16.840.1.113883.10.20.22.4.16" extension="2014-06-09"/>
									<templateId root="2.16.840.1.113883.10.20.22.4.16"/>
									<id nullFlavor="NI"/>
									<statusCode code="completed"/>
									<effectiveTime nullFlavor="NA"/>
									<doseQuantity nullFlavor="NA"/>
									<consumable>
										<manufacturedProduct classCode="MANU">
											<templateId root="2.16.840.1.113883.10.20.22.4.23" extension="2014-06-09"/>
											<templateId root="2.16.840.1.113883.10.20.22.4.23"/>
											<manufacturedMaterial>
												<code nullFlavor="OTH" codeSystem="2.16.840.1.113883.6.88"> 
													<translation code="410942007" displayName="drug or medication"
														codeSystem="2.16.840.1.113883.6.96"            
														codeSystemName="SNOMED CT"/>
												</code>
											</manufacturedMaterial>
										</manufacturedProduct>
									</consumable>
								</substanceAdministration>
							</entry>
							';
							
							
							
							$XML_medication_section .= $XML_medication_activity_entry;
						
						}
						/*  END MEDICATION ENTRY*/
				$XML_medication_section .= '</section>';
				$XML_medication_section .= '</component>';
				
			/* Medication Section End */
			
			/* Allergies */
				$XML_allergies_section = $XML_allergies_problem_act = '';
				$XML_allergies_section = '<component>';
				$XML_allergies_section .= '<section>';
				$XML_allergies_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.6.1" extension="2015-08-01"/>';
				$XML_allergies_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.6.1"/>';
				$XML_allergies_section .= '<code code="48765-2" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="History of allergies"/>';
				$XML_allergies_section .= '<title>ALLERGIES &amp; REACTIONS</title>';
				$XML_allergies_section .= '<text>';
				$XML_allergies_section .= '<table border = "1" width = "100%">';
				$XML_allergies_section .= '<thead>
											<tr>
												<th>Type</th>
												<th>Substance</th>
												<th>Begin Date</th>
												<th>Reactions</th>
												<th>Severity</th>
												<th>Status</th>
											</tr>
										</thead>';
				$XML_allergies_section .= '<tbody>';
				$arrType = array("3","7");
				$arrAllergies = $this->get_medical_data($this->form_id, $arrType,$this->patientId);
				$flag = 0;
				if(count($arrAllergies)>0){
					foreach($arrAllergies as $allergy){		
						if(!in_array($allergy['title'],$arrMedHxAller)){
							
						$arrAllerType = $this->allergy_type_srh($allergy['ag_occular_drug']);
						$strAllerType = '';
						if(count($arrAllerType)>0)
						$strAllerType = '  - '.$arrAllerType['display_name'];
						$flag = 1;
						$XML_allergies_section .= '<tr ID = "ALGSUMMARY_'.$allergy['id'].'">
												<td ID = "ALGTYPE_'.$allergy['id'].'">'.$arrAllerType['display_name'].'</td>
												<td ID = "ALGSUB_'.$allergy['id'].'">'.htmlentities($allergy['title']).'</td>
												<td ID = "ALGBEGIN_'.$allergy['id'].'">
												';
						$XML_allergies_section .=(preg_replace("/-/",'',$allergy['begdate'])>0)?date('M d,Y',strtotime($allergy['begdate'])):"";
						$XML_allergies_section .='</td>
												<td ID = "ALGREACT_'.$allergy['id'].'">'.htmlentities($allergy['comments']).'</td>
												<td ID = "ALGRESEV_'.$allergy['id'].'">'.htmlentities(ucwords(strtolower($allergy['severity']))).'</td>
												<td ID = "ALGSTATUS_'.$allergy['id'].'">'.$allergy['allergy_status'].'</td>
											</tr>';
						}
					}
				}
				if($flag == 0)
				{
					$XML_allergies_section .= ' <tr><td colspan="5">No allergy data.</td></tr>';
				}
				$sql = "SELECT no_value FROM commonnomedicalhistory WHERE patient_id = '".$this->patientId."' AND module_name = 'Allergy'";
				$res = $dbh->imw_query($sql);
				$row = $dbh->imw_fetch_assoc($res);
				$negationInd = '';
				if($row['no_value'] == "NoAllergies"){
					$XML_allergies_section .= ' <tr><td colspan="5">No Known Drug Allergy (NKDA)</td></tr>';
				}
				
				$XML_allergies_section .= '</tbody>';
				$XML_allergies_section .= '</table>';
				$XML_allergies_section .= '</text>';
						/* BEGIN ALLERGIES PROBLEM ACT */
						$arrType = array("3","7");
						$arrAllergies = $this->get_medical_data($this->form_id, $arrType,$this->patientId);
						$flag = 0;
						if(count($arrAllergies)>0){
							foreach($arrAllergies as $allergy){
								
								if(!in_array($allergy['title'],$arrMedHxAller)){
								$flag = 1;	
								$XML_allergies_problem_act = '<entry typeCode="DRIV">';
								$XML_allergies_problem_act .= '<act classCode="ACT" moodCode="EVN">';
								$XML_allergies_problem_act .= '<templateId root="2.16.840.1.113883.10.20.22.4.30" extension="2015-08-01"/>';
								$XML_allergies_problem_act .= '<templateId root="2.16.840.1.113883.10.20.22.4.30"/>';
								$XML_allergies_problem_act .= '<id nullFlavor="NI"/>';
								$XML_allergies_problem_act .= '<!-- Allergy Problem Act template -->';
								$XML_allergies_problem_act .= '<code code="CONC" codeSystem="2.16.840.1.113883.5.6"/>';
								$XML_allergies_problem_act .= '<statusCode code="active"/>';
								$XML_allergies_problem_act .= '<effectiveTime>';
								if($allergy['begdate']!= ""){
								$XML_allergies_problem_act .= '<low value="'.str_replace('-','',$allergy['begdate']).'"/>';
								}else{
								$XML_allergies_problem_act .= '<low nullFlavor="NI"/>';	
								}
								$XML_allergies_problem_act .= '</effectiveTime>';
								$XML_allergies_problem_act .= '<entryRelationship typeCode="SUBJ">';
								
								$XML_allergies_problem_act .= '<observation classCode="OBS" moodCode="EVN">';
								$XML_allergies_problem_act .= '<templateId root="2.16.840.1.113883.10.20.22.4.7" extension="2014-06-09"/>';
								$XML_allergies_problem_act .= '<templateId root="2.16.840.1.113883.10.20.22.4.7"/>';
								$XML_allergies_problem_act .= '<id nullFlavor="NI"/>';
								$XML_allergies_problem_act .= '<!-- Allergy - intolerance observation template -->';
								$XML_allergies_problem_act .= '<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4"/>';
								$XML_allergies_problem_act .= '<statusCode code="completed"/>';
								if($allergy['begdate']!= ""){
								$XML_allergies_problem_act .= '<effectiveTime>';
								$XML_allergies_problem_act .= '<low value="'.str_replace('-','',$allergy['begdate']).'"/>';
								$XML_allergies_problem_act .= '</effectiveTime>';
								}else{
								$XML_allergies_problem_act .= '<effectiveTime>';
								$XML_allergies_problem_act .= '<low nullFlavor="NI"/>';
								$XML_allergies_problem_act .= '</effectiveTime>';
								}
								$arrAllerType = $this->allergy_type_srh($allergy['ag_occular_drug']);
								if($arrAllerType['code'] != "" && $arrAllerType['display_name'] != ""){				
								$XML_allergies_problem_act .= '<value xsi:type="CD" code="'.$arrAllerType['code'].'"
																displayName="'.$arrAllerType['display_name'].'"
																codeSystem="2.16.840.1.113883.6.96"
																codeSystemName="SNOMED CT">';
								$XML_allergies_problem_act .= '<originalText>';
								$XML_allergies_problem_act .= '<reference value="#ALGSUB_'.$allergy['id'].'"/>';
								$XML_allergies_problem_act .= htmlentities($allergy['title']);
								$XML_allergies_problem_act .= '</originalText>';
								$XML_allergies_problem_act .= '</value>';
								}
								$XML_allergies_problem_act .= '<participant typeCode="CSM">';
								$XML_allergies_problem_act .= '<participantRole classCode="MANU">';
								$XML_allergies_problem_act .= '<playingEntity classCode="MMAT">';
								/* */
								if($allergy['ag_occular_drug'] == "fdbATIngredient" || $allergy['ag_occular_drug'] == "fdbATAllergenGroup"){ // Food Allergy
																
									if($allergy['ccda_code'] != "")	{
									$ccda_code_aller = $allergy['ccda_code'];
									$ccda_display_name_aller = $allergy['title'];
									}
									else{
									/* DYNAMIC CODE FROM Ingredient Name Value Set (Unique Ingredient Identifier (UNII) Code System)*/		
									$ccda_code_aller = $allergy['ccda_code'];
									$ccda_display_name_aller = $allergy['title'];	
									}
									$XML_allergies_problem_act .= '<code code="'.$ccda_code_aller.'" displayName="'.$ccda_display_name_aller.'"
																	codeSystem="2.16.840.1.113883.4.9" codeSystemName="UNII">';
									$XML_allergies_problem_act .= '<originalText>';
									$XML_allergies_problem_act .= '<reference value="#ALGSUB_'.$allergy['id'].'"/>';
									$XML_allergies_problem_act .= '</originalText>';
									$XML_allergies_problem_act .= '</code>';
								}
								else if($allergy['ag_occular_drug'] == "fdbATDrugName"){ // Drug Allergy
									if($allergy['ccda_code'] != "")	{
										//$arrCCDA = getRXNorm_by_code($allergy['ccda_code']);
										//if(count($arrCCDA)>0){
										$ccda_code_aller = $allergy['ccda_code'];
										$ccda_display_name_aller = $allergy['title'];
										//}
									}
									else{
										/* DYNAMIC CODE FROM Medication Clinical Drug Value Set (RxNorm Code System)*/
										$arrCCDA = $this->getRXNormCode($allergy['title']);
										if(count($arrCCDA)>0){
										$ccda_code_aller = $arrCCDA['ccda_code'];
										$ccda_display_name_aller = $arrCCDA['ccda_display_name'];
										}
									}
									$XML_allergies_problem_act .= '<code code="'.$ccda_code_aller.'" displayName="'.$ccda_display_name_aller.'"
																	codeSystem="2.16.840.1.113883.6.88" codeSystemName="RxNorm">';
									$XML_allergies_problem_act .= '<originalText>';
									$XML_allergies_problem_act .= '<reference value="#ALGSUB_'.$allergy['id'].'"/>';
									$XML_allergies_problem_act .= '</originalText>';
									$XML_allergies_problem_act .= '</code>';
								}
								
								$XML_allergies_problem_act .= '</playingEntity>';
								$XML_allergies_problem_act .= '</participantRole>';
								$XML_allergies_problem_act .= '</participant>';
								$XML_allergies_problem_act .= '<entryRelationship typeCode = "SUBJ" inversionInd = "true">
													<observation classCode = "OBS" moodCode = "EVN">
														<templateId root = "2.16.840.1.113883.10.20.22.4.28" extension="2014-06-09"/>
														<templateId root = "2.16.840.1.113883.10.20.22.4.28"/>
														<code code = "33999-4" codeSystem = "2.16.840.1.113883.6.1" codeSystemName = "LOINC" displayName = "Status"/>
														<statusCode code = "completed"/>
														<value xsi:type = "CE" code = "55561003" codeSystem = "2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT" displayName = "'.$allergy['allergy_status'].'"/>
													</observation>
												</entryRelationship>';
							   $XML_allergies_problem_act .= '<entryRelationship typeCode = "MFST" inversionInd = "true">
													<observation classCode = "OBS" moodCode = "EVN">
														<templateId root = "2.16.840.1.113883.10.20.22.4.9" extension="2014-06-09"/>
														<templateId root = "2.16.840.1.113883.10.20.22.4.9"/>
														<id nullFlavor="NI"/>
														<code code = "ASSERTION" codeSystem = "2.16.840.1.113883.5.4"/>
														<text>
															<reference value = "#ALGREACT_'.$allergy['id'].'"/>'.
															htmlentities($allergy['comments'])
															.'
														</text>
														<statusCode code = "completed"/>';
								$arrAllReaction = $this->getProblemCode($allergy['comments']);
												// DYNAMIC REACTION CODE //
								if($arrAllReaction['ccda_code']!="" && $arrAllReaction['ccda_display_name']){					
									$XML_allergies_problem_act .= '<value xsi:type="CD"
															code="'.$arrAllReaction['ccda_code'].'"
															codeSystem="2.16.840.1.113883.6.96"
															codeSystemName="SNOMED CT"
															displayName="'.$arrAllReaction['ccda_display_name'].'"/>';
								}else if(trim($allergy['comments'])!="" && trim($allergy['reaction_code'])!=''){					
									$XML_allergies_problem_act .= '<value xsi:type="CD"
															code="'.trim($allergy['reaction_code']).'"
															codeSystem="2.16.840.1.113883.6.96"
															codeSystemName="SNOMED CT"
															displayName="'.trim($allergy['comments']).'"/>';
								}else{
									$XML_allergies_problem_act .= '<value xsi:type="CD" nullFlavor="NI"/>';
								} 
								//----if severity available--new code below -----
								if(trim($allergy['severity'])!=''){
									$XML_allergies_problem_act .= '
									<entryRelationship typeCode="SUBJ" inversionInd="true">
										<observation classCode="OBS" moodCode="EVN">
											<!--Severity Observation (V2)-->
											<templateId root="2.16.840.1.113883.10.20.22.4.8" extension="2014-06-09"/>
											<templateId root="2.16.840.1.113883.10.20.22.4.8"/>
											<code code="SEV" displayName="Severity Observation" codeSystem="2.16.840.1.113883.5.4" codeSystemName="ActCode"/>
											<text>
												<reference value="#ALGRESEV_'.$allergy['id'].'"/>
											</text>
											<statusCode code="completed"/>';
									if(strtolower(trim($allergy['severity']))=='fatal')					$severity_value_code = '399166001';
									else if(strtolower(trim($allergy['severity']))=='mild') 			$severity_value_code = '255604002';
									else if(strtolower(trim($allergy['severity']))=='mild to moderate')	$severity_value_code = '371923003';
									else if(strtolower(trim($allergy['severity']))=='moderate') 		$severity_value_code = '6736007';
									else if(strtolower(trim($allergy['severity']))=='moderate to severe')$severity_value_code= '371924009';
									else if(strtolower(trim($allergy['severity']))=='severe') 			$severity_value_code = '24484000';
									
									$XML_allergies_problem_act .= '							
											<value xsi:type="CD" code="'.$severity_value_code.'"
												displayName="'.trim(ucwords(strtolower($allergy['severity']))).'"
												codeSystem="2.16.840.1.113883.6.96"
												codeSystemName="SNOMED-CT"/>
										</observation>
									</entryRelationship>
									';
								}
								//------severity code end------------------------
								
								$XML_allergies_problem_act .='</observation>
												</entryRelationship>';
								$XML_allergies_problem_act .= '</observation>';
								$XML_allergies_problem_act .= '</entryRelationship>';
								$XML_allergies_problem_act .= '</act>';
								$XML_allergies_problem_act .= '</entry>';
								$XML_allergies_section .= $XML_allergies_problem_act;
								}
							
							}
						}
						
						if($flag == 0){
							
							$XML_allergies_problem_act = '<entry typeCode="DRIV">';
							$XML_allergies_problem_act .= '	<act classCode="ACT" moodCode="EVN">';
							$XML_allergies_problem_act .= ' <templateId root="2.16.840.1.113883.10.20.22.4.30" extension="2015-08-01"/>';
							$XML_allergies_problem_act .= '	<templateId root="2.16.840.1.113883.10.20.22.4.30"/>';
							$XML_allergies_problem_act .= '	<id nullFlavor="NI"/>';
							$XML_allergies_problem_act .= '	<!-- Allergy Problem Act template -->';
							$XML_allergies_problem_act .= '	<code code="48765-2" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Allergies, adverse reactions, alerts"/>';
							$XML_allergies_problem_act .= '	<statusCode code="active"/>';
							$XML_allergies_problem_act .= '	<effectiveTime>';
							$XML_allergies_problem_act .= '		<low nullFlavor="NI"/>';
							$XML_allergies_problem_act .= '	</effectiveTime>';
							$XML_allergies_problem_act .= '	<entryRelationship typeCode="SUBJ">';
							
							$sql = "SELECT no_value FROM commonnomedicalhistory WHERE patient_id = '".$this->patientId."' AND module_name = 'Allergy'";
							$res = $dbh->imw_query($sql);
							$row = $dbh->imw_fetch_assoc($res);
							$negationInd = '';
							if($row['no_value'] == "NoAllergies")
							$negationInd = "negationInd='true'";
							$XML_allergies_problem_act .= '		<observation classCode="OBS" moodCode="EVN" '.$negationInd.'>';
							$XML_allergies_problem_act .= '			<templateId root="2.16.840.1.113883.10.20.22.4.7" extension="2014-06-09"/>';
							$XML_allergies_problem_act .= '			<templateId root="2.16.840.1.113883.10.20.22.4.7"/>';
							$XML_allergies_problem_act .= '			<id nullFlavor="NI"/>';
							$XML_allergies_problem_act .= '			<!-- Allergy - intolerance observation template -->';
							$XML_allergies_problem_act .= '			<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4"/>';
							$XML_allergies_problem_act .= '			<statusCode code="completed"/>';
							$XML_allergies_problem_act .= '			<effectiveTime>';
							$XML_allergies_problem_act .= '				<low nullFlavor="UNK"/>';
							$XML_allergies_problem_act .= '			</effectiveTime>';
							$XML_allergies_problem_act .= '			';
							$XML_allergies_problem_act .= '<value xsi:type="CD" nullFlavor="NI"/>';
							$XML_allergies_problem_act .= '			<participant typeCode="CSM">';
							$XML_allergies_problem_act .= '				<participantRole classCode="MANU">';
							$XML_allergies_problem_act .= '					<playingEntity classCode="MMAT">';
							$XML_allergies_problem_act .= '						<code nullFlavor="NI"/>';
							$XML_allergies_problem_act .= '					</playingEntity>';
							$XML_allergies_problem_act .= '				</participantRole>';
							$XML_allergies_problem_act .= '			</participant>';
							$XML_allergies_problem_act .= '		</observation>';
							$XML_allergies_problem_act .= '</entryRelationship>';
							$XML_allergies_problem_act .= '</act>';
							$XML_allergies_problem_act .= '</entry>';
							$XML_allergies_section .= $XML_allergies_problem_act;
						
						}
						/* END ALLERGIES PROBLEM ACT */
				$XML_allergies_section .= '</section>';
				$XML_allergies_section .= '</component>';

			/* Allergies End */
			
			/* Immunization */
				$qry_immu = $XML_immunization_section = '';
				$qry_immu = "SELECT * FROM immunizations immu WHERE patient_id = '".$this->patientId."'";
				
				$XML_immunization_section = '<component>';
				$XML_immunization_section .= '<section>';
				$XML_immunization_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.2.1"/>';
				$XML_immunization_section .= '<!-- ******** Immunizations section template ******** -->';
				$XML_immunization_section .= '<code code="11369-6"
											codeSystem="2.16.840.1.113883.6.1"
											codeSystemName="LOINC"
											displayName="History of immunizations"/>';
				$XML_immunization_section .= '<title>IMMUNIZATIONS</title>';
				$XML_immunization_section .= '<text>';

				$res_immu = $dbh->imw_query($qry_immu);
				if($dbh->imw_num_rows($res_immu)){
					$XML_immunization_section .= ' <table border = "1" width = "100%">';
					$XML_immunization_section .= '<thead>
											<tr>
												<th>Vaccine Code</th>
												<th>Vaccine Name</th>
												<th>Date</th>
												<th>Status</th>
												<th>Lot#</th>
												<th>Manufacturer</th>
												<th>Additional Notes</th>
											</tr>
										</thead>
										<tbody>
										';
					while($row_immu = $dbh->imw_fetch_assoc($res_immu)){
						$admi_date = $admi_route = $dosage = $manu = $admn_by ="";
						if(str_replace('-','',$row_immu['administered_date']) != '00000000') $admi_date = $row_immu['administered_date'];
						
						if($row_immu['immzn_route_site'] != '') $admi_route = $row_immu['immzn_route_site'];
						
						if($row_immu['immzn_dose_unit'] != '' && $row_immu['immzn_dose'] != "") $dosage = $row_immu['immzn_dose']. " ".$row_immu['immzn_dose_unit'];
						
						if($row_immu['manufacturer'] != "") $manu = $row_immu['manufacturer'];
						
						if($row_immu['administered_by_id']!="" && $row_immu['administered_by_id']>0 ){
							$qry_admin_by = "SELECT * FROM users WHERE id = '".$row_immu['administered_by_id']."'";
							$res_admin_by = $dbh->imw_query($qry_admin_by);
							if($dbh->imw_num_rows($res_admin_by) > 0){
								$row_admin_by = $dbh->imw_fetch_assoc($res_admin_by);
								$admn_by = $row_admin_by['fname']." ".$row_admin_by['lname'];
							}
						}	
						
						$temp_immunization_id 		 = explode(' - ',$row_immu['immunization_id']);
						$row_immu['immunization_id'] = $temp_immunization_id[1];
						
						$XML_immunization_section .= '<tr>
											<td><content ID = "immun'.$row_immu['id'].'"/>CVX: '.$row_immu['immunization_cvx_code'].'</td>
											<td>'.htmlentities($row_immu['immunization_id']).'</td>
											<td>';
						$XML_immunization_section .=(preg_replace("/-/",'',$admi_date)>0)?date('M d,Y',strtotime($admi_date)):"";
						$XML_immunization_section .='</td>
											<td>'.$row_immu['scpStatus'].'</td>
											<td>'.$row_immu['lot_number'].'</td>
											<td>'.htmlentities($manu).'</td>
											<td>'.$row_immu['note'].'<br/>'.$row_immu['refusal_reason'].'</td>
										</tr>
										';
					}
					$XML_immunization_section .= '</tbody>
												</table>
												';
				}

				$XML_immunization_section .= '</text>';

				$res_immu = $dbh->imw_query($qry_immu);
				if($dbh->imw_num_rows($res_immu)){
					while($row_immu = $dbh->imw_fetch_assoc($res_immu)){
						$ccda_code_route = $ccda_display_name_route = "";
						$XML_immunization_entry = '<entry typeCode="DRIV">';
						$XML_immunization_entry .= '<substanceAdministration classCode="SBADM" moodCode="EVN"
														negationInd="false">';
						$XML_immunization_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.52" extension="2015-08-01"/>';
						$XML_immunization_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.52"/>';
						$XML_immunization_entry .= '<id nullFlavor="NI"/>';
						$XML_immunization_entry .= '<!-- **** Immunization activity template **** -->';
						$XML_immunization_entry .= '<text>';
						$XML_immunization_entry .= '<reference value="#immun'.$row_immu['id'].'"/>';
						$XML_immunization_entry .= '</text>';
						$XML_immunization_entry .= '<statusCode code="completed"/>';
						if($row_immu['administered_date']!="")
						$XML_immunization_entry .= '<effectiveTime xsi:type="IVL_TS" value="'.str_replace('-','',$row_immu['administered_date']).'"/>';
						else
						$XML_immunization_entry .= '<effectiveTime xsi:type="IVL_TS" nullFlavor="NI"/>';
						/* DYNAMIC VALUE FOR ROUTE (EX ORAL) Medication Route FDA Value Set Code System(s):National Cancer Institute (NCI) Thesaurus 2.16.840.1.113883.3.26.1.1 */
						if($row_immu['immzn_route_site']!=""){
							$arrCCDA = $this->getRouteCode($row_immu['immzn_route_site']);
							$ccda_code_route = $arrCCDA['ccda_code'];
							$ccda_display_name_route = $arrCCDA['ccda_display_name'];
						}
						if($ccda_code_route != "" && $ccda_display_name_route!=""){
							$XML_immunization_entry .= '<routeCode code="'.$ccda_code_route.'" codeSystem="2.16.840.1.113883.3.26.1.1"
													codeSystemName="NCI Thesaurus"
													displayName="'.$ccda_display_name_route.'"/>';
						}else{
							$XML_immunization_entry .= '<routeCode nullFlavor="NI"/>';
						}
						
						if($row_immu['immzn_dose'] != "" && $row_immu['immzn_dose_unit'] != "")							
						$XML_immunization_entry .= '<doseQuantity value="'.trim($row_immu['immzn_dose']).'" unit="'.trim($row_immu['immzn_dose_unit']).'"/>';
						else
						$XML_immunization_entry .= '<doseQuantity nullFlavor="NI"/>';
						$XML_immunization_entry .= '<consumable>';
						$XML_immunization_entry .= '<manufacturedProduct classCode="MANU">';
						$XML_immunization_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.54" extension="2014-06-09"/>';
						$XML_immunization_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.54"/>';
						$XML_immunization_entry .= '<!-- **** Immunization Medication Information **** -->';
						$XML_immunization_entry .= '<manufacturedMaterial>';
						
						/* DYNAMIC VALUE Vaccine Administered Value Set Code System(s):Vaccines administered (CVX) 2.16.840.1.113883.12.292 */
						$XML_immunization_entry .= '<code code="'.$row_immu['immunization_cvx_code'].'"
													codeSystem="2.16.840.1.113883.12.292"
													codeSystemName="CVX"
													displayName="'.$row_immu['immunization_id'].'">';							
						$XML_immunization_entry .= '<originalText><reference value = "#immun'.$row_immu['id'].'"/>'.$row_immu['immunization_id'].'</originalText>';
						$XML_immunization_entry .= '</code>';
						$XML_immunization_entry .= '</manufacturedMaterial>';
						if($row_immu['manufacturer'] != ""){
						$XML_immunization_entry .= '<manufacturerOrganization>
													  <name>'.htmlentities($row_immu['manufacturer']).'</name>
												   </manufacturerOrganization>';
						}
						$XML_immunization_entry .= '</manufacturedProduct>';
						$XML_immunization_entry .= '</consumable>';
						
							if($row_immu['administered_by_id']!="" && $row_immu['administered_by_id']>0 ){
							$qry_admin_by = "SELECT * FROM users WHERE id = '".$row_immu['administered_by_id']."'";
							$res_admin_by = $dbh->imw_query($qry_admin_by);
							if($dbh->imw_num_rows($res_admin_by) > 0){
							$row_admin_by = $dbh->imw_fetch_assoc($res_admin_by);
							
							$XML_immunization_entry .= '<performer typeCode="PRF">';
							$XML_immunization_entry .= '<assignedEntity>';
							$XML_immunization_entry .= '<!-- NPI 12345 -->';
							if($row_admin_by['user_npi'] != "")
							$XML_immunization_entry .= '<id extension="'.$row_admin_by['user_npi'].'" root="2.16.840.1.113883.4.6"/>';
							else
							$XML_immunization_entry .= '<id nullFlavor="NI"/>';
							//
							if($row_admin_by['facility'] > 0){
							$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$row_admin_by['facility']."'";
							}
							else{
							$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
							}
							$res_facility = $dbh->imw_query($qry_facility);
							$row_facility = $dbh->imw_fetch_assoc($res_facility);
							
							$XML_immunization_entry .= '<addr use="WP">';
							if($row_facility['street'] != "")
							$XML_immunization_entry .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
							if($row_facility['city'] != "")
							$XML_immunization_entry .= '<city>'.$row_facility['city'].'</city>';
							if($row_facility['state'] != "")
							$XML_immunization_entry .= '<state>'.$row_facility['state'].'</state>';
							if($row_facility['postal_code'] != "")
							$XML_immunization_entry .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
							$XML_immunization_entry .= '<country>US</country>';
							$XML_immunization_entry .= '</addr>';
							
							if($row_facility['phone'] != "")
							$XML_immunization_entry .= '<telecom use="WP" value="tel:+1-'.$this->core_phone_format($row_facility['phone']).'"/>';
							$XML_immunization_entry .= '<assignedPerson>';
							$XML_immunization_entry .= '<name>';
							$XML_immunization_entry .= '<given>'.$row_admin_by['fname'].'</given>';
							$XML_immunization_entry .= '<family>'.$row_admin_by['lname'].'</family>';
							$XML_immunization_entry .= '</name>';
							$XML_immunization_entry .= '</assignedPerson>';
							$XML_immunization_entry .= '</assignedEntity>';
							$XML_immunization_entry .= '</performer>';
							}
							}
							if($row_immu['adverse_reaction']!=""){
							$XML_immunization_entry .= '<entryRelationship typeCode="CAUS">';
							$XML_immunization_entry .= '<observation classCode="OBS" moodCode="EVN">';
							$XML_immunization_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.9"/>';
							$XML_immunization_entry .= '<!-- Reaction observation template -->';
							$XML_immunization_entry .= '<id nullFlavor="NI"/>';
							$XML_immunization_entry .= '<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4"/>';
							$XML_immunization_entry .= '<statusCode code="completed"/>';
							$XML_immunization_entry .= '<effectiveTime xsi:type="IVL_TS">';
							if($row_immu['adverse_reaction_date'] != "" && $row_immu['adverse_reaction_date'] != '0000-00-00 00:00:00'){
							$date = date('Ymd',strtotime($row_immu['adverse_reaction_date']));
							$XML_immunization_entry .= '<low value="'.$date.'"/>';
							}else{
							$XML_immunization_entry .= '<low nullFlavor="NI"/>';
							}
							$XML_immunization_entry .= '</effectiveTime>';
							$arrReaction = $this->getProblemCode($row_immu['adverse_reaction']);
													// DYNAMIC REACTION CODE //
							if($arrReaction['ccda_code']!="" && $arrReaction['ccda_display_name']){					
							$XML_immunization_entry .= '<value xsi:type="CD"
														code="'.$arrReaction['ccda_code'].'"
														codeSystem="2.16.840.1.113883.6.96"
														codeSystemName="SNOMED CT"
														displayName="'.$arrReaction['ccda_display_name'].'"/>';
							}else{
								$XML_immunization_entry .= '<value xsi:type="CD" nullFlavor="NI"/>';
							}
							$XML_immunization_entry .= '</observation>';
							$XML_immunization_entry .= '</entryRelationship>';
							}
						$XML_immunization_entry .= '</substanceAdministration>';
						$XML_immunization_entry .= '</entry>';
						$XML_immunization_section .= $XML_immunization_entry;
					}
				}else{
					
				$XML_immunization_entry = '<entry typeCode="DRIV">
											<substanceAdministration classCode="SBADM" moodCode="EVN" negationInd="false">
											<templateId root="2.16.840.1.113883.10.20.22.4.52" extension="2015-08-01"/>
											<templateId root="2.16.840.1.113883.10.20.22.4.52"/>
											<id nullFlavor="NI"/>
											<!-- **** Immunization activity template **** -->
											<statusCode code="completed"/>
											<effectiveTime nullFlavor="NI"/>
											<consumable>
											<manufacturedProduct classCode="MANU">
											<templateId root="2.16.840.1.113883.10.20.22.4.54" extension="2014-06-09"/>
											<templateId root="2.16.840.1.113883.10.20.22.4.54"/>
											<!-- **** Immunization Medication Information **** -->
											<manufacturedMaterial>
											<code nullFlavor="NI"/>
											</manufacturedMaterial>
											</manufacturedProduct>
											</consumable>';

				$XML_immunization_entry .= '</substanceAdministration>';
				$XML_immunization_entry .= '</entry>';
				$XML_immunization_section .= $XML_immunization_entry;
				}
				$XML_immunization_section .= '</section>';
				$XML_immunization_section .= '</component>';
			
			/* Immunization End */
			
			/* Vital Signs */
			$XML_vital_section = '';
				if($this->form_id == ""){
					$sql_vital = "SELECT vsp.*,vsl.vital_sign,vsm.date_vital FROM vital_sign_master vsm 
							JOIN vital_sign_patient vsp ON vsm.id = vsp.vital_master_id 
							JOIN  vital_sign_limits vsl ON vsl.id = vsp.vital_sign_id 
							WHERE vsm.patient_id = '".$this->patientId."' AND  vsm.status = 0 ORDER BY vsp.id ASC";
				}else{
					$sql_vital = "SELECT vsp.*,vsl.vital_sign,vsm.date_vital FROM vital_sign_master vsm 
							JOIN vital_sign_patient vsp ON vsm.id = vsp.vital_master_id 
							JOIN  vital_sign_limits vsl ON vsl.id = vsp.vital_sign_id 
							WHERE vsm.patient_id = '".$this->patientId."' AND  vsm.status = 0 
								AND vsm.date_vital = '".$this->dos."'
							ORDER BY vsp.id ASC";
				}
				$result_vital = $dbh->imw_query($sql_vital);
				if($dbh->imw_num_rows($result_vital)>0){
					$XML_vital_section = '<component>';
					$XML_vital_section .= '<section>';
					$XML_vital_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.4.1" extension="2015-08-01"/>';
					$XML_vital_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.4.1"/>';
					$XML_vital_section .= '<code code="8716-3"
											codeSystem="2.16.840.1.113883.6.1"
											codeSystemName="LOINC"
											displayName="VITAL SIGNS" />';
					$XML_vital_section .= '<title>VITAL SIGNS</title>';
					$XML_vital_section .= '<text>';			
					$XML_vital_section .= '<table border = "1" width = "100%">';	
					$XML_vital_section .= '<thead>
											<tr>
												<th>Vital Sign</th>
												<th >Value</th>
												<th>Date Time</th>
											</tr>
										</thead>';
					$XML_vital_section .= '<tbody>';					
					while($row_vital = $dbh->imw_fetch_assoc($result_vital)){	
						$arr_vs_result_type = $this->vs_result_type_srh($row_vital['vital_sign']);
						if($arr_vs_result_type['code'] != "" && $arr_vs_result_type['display_name'] != "" && $row_vital['range_vital']!=""){						
						if(strtolower($row_vital['unit'])=='mmhg') $row_vital['unit'] = 'mm[Hg]';
						else if(strtolower($row_vital['unit'])=='beats/minute') $row_vital['unit'] = '/min';
						else if(strtolower($row_vital['unit'])=='breaths/minute') $row_vital['unit'] = '/min';
						else if(strtolower(trim($row_vital['unit']))=='kg/sqr. m' || strtolower(trim($row_vital['unit']))=='kg/sqr.m') $row_vital['unit'] = 'kg/m2';
						else if(strtolower($row_vital['unit'])=='°c' || strtolower($row_vital['unit'])=='&deg;c') $row_vital['unit'] = 'Cel';
						
						$XML_vital_section .= '
											<tr>
												<td>'.$row_vital['vital_sign'].'</td>
												<td ID = "VS_Val_'.$row_vital['id'].'">'.$row_vital['range_vital']." ".html_entity_decode($row_vital['unit']).'</td>
												<td ID = "VS_'.$row_vital['id'].'">';
						$XML_vital_section .=(preg_replace("/-/",'',$row_vital['date_vital'])>0)?date('M d,Y',strtotime($row_vital['date_vital'])):"";
						$XML_vital_section .='</td>
											</tr>
											';
						}
					}
					$XML_vital_section .= '</tbody>';
					$XML_vital_section .= '</table>';
					$XML_vital_section .= '</text>';
				
				
					$XML_vital_entry = '';
					$result_vital = $dbh->imw_query($sql_vital);
					if($dbh->imw_num_rows($result_vital) > 0){
						$XML_vital_entry = '';
						while($row_vital = $dbh->imw_fetch_assoc($result_vital)){
							$arr_vs_result_type = $this->vs_result_type_srh($row_vital['vital_sign']);
							if($arr_vs_result_type['code'] != "" && $arr_vs_result_type['display_name'] != "" && $row_vital['range_vital']!=""){
								
								if($XML_vital_entry==''){
								$XML_vital_entry = '<entry typeCode="DRIV">
													<organizer classCode="CLUSTER" moodCode="EVN">
													<!-- Vital Signs Organizer template -->
													<templateId root="2.16.840.1.113883.10.20.22.4.26" extension="2015-08-01"/>
													<templateId root="2.16.840.1.113883.10.20.22.4.26"/>
													<id nullFlavor="NI"/>
													<code code="46680005" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="Vital Signs">
														<translation code="74728-7" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Vital Signs"/>
													</code>
													<statusCode code="completed"/>
													<effectiveTime>
														<low value="'.str_replace('-','',$row_vital['date_vital']).'"/>
														<high value="'.str_replace('-','',$row_vital['date_vital']).'"/>
													</effectiveTime>';
								}
								$XML_vital_entry .= '
													<component>
														<!-- VITAL SIGN OBSERVATIONS -->
														<observation classCode="OBS" moodCode="EVN">
															<templateId root="2.16.840.1.113883.10.20.22.4.27" extension="2014-06-09"/>
															<templateId root="2.16.840.1.113883.10.20.22.4.27"/>
															<id nullFlavor="NI"/>
															<code code="'.$arr_vs_result_type['code'].'" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="'.$arr_vs_result_type['display_name'].'"/>
															<statusCode code="completed"/>
															<effectiveTime value="'.str_replace('-','',$row_vital['date_vital']).'"/>';
								if($row_vital['range_vital']!=""){
									if(strtolower($row_vital['unit'])=='mmhg') $row_vital['unit'] = 'mm[Hg]';
									else if(strtolower($row_vital['unit'])=='beats/minute') $row_vital['unit'] = '/min';
									else if(strtolower($row_vital['unit'])=='breaths/minute') $row_vital['unit'] = '/min';
									else if(strtolower(trim($row_vital['unit']))=='kg/sqr. m' || strtolower(trim($row_vital['unit']))=='kg/sqr.m') $row_vital['unit'] = 'kg/m2';
									else if(strtolower($row_vital['unit'])=='°c' || strtolower($row_vital['unit'])=='&deg;c') $row_vital['unit'] = 'Cel';
									
									$XML_vital_entry .= '<value xsi:type="PQ" value="'.trim($row_vital['range_vital']).'" unit="'.html_entity_decode(preg_replace('/\s/','',trim($row_vital['unit']))).'"/>
									';
								}else{
									$XML_vital_entry .= '<value xsi:type="PQ" nullFlavor="NI"/>';
								}
								$XML_vital_entry .= '</observation>
													</component>';
							}
						}
						$XML_vital_entry .= '</organizer>
											</entry>';
						$XML_vital_section .= $XML_vital_entry;				
					}
					$XML_vital_section .= '</section>';
					$XML_vital_section .= '</component>
					';
				}else{
					$XML_vital_section .= '
					<component>
						<section nullFlavor="NI">
							<!-- Vitals Section (entries required) (V3) nullflavor -->
							<templateId root="2.16.840.1.113883.10.20.22.2.4.1" extension="2015-08-01"/>
							<templateId root="2.16.840.1.113883.10.20.22.2.4.1"/>
							<code code="8716-3" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="VITAL SIGNS"/>
							<title>VITAL SIGNS</title>
							<text>No Vital Signs Information</text>
						</section>
					</component>
					';
				}
				
			/* Vital Signs End */
			
			/* Problem List */
			$XML_problem_section = '';	
				$arrProblemList = $this->get_pt_problem_list($this->form_id, $this->patientId, $this->startDate, $this->endDate);
				$XML_problem_section = '<component>
								<section>
								<!-- Problem Section with Coded Entries Required templateID -->
								<templateId root="2.16.840.1.113883.10.20.22.2.5.1" extension="2015-08-01"/>
								<templateId root="2.16.840.1.113883.10.20.22.2.5.1"/>
								<code code="11450-4" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="PROBLEM LIST"/>
								<title>PROBLEMS</title>
								<text>
								<table border = "1" width = "100%">
								<thead>
                                <tr>
                                    <th>Problem</th>
                                    <th>Effective Dates</th>
									<th>Problem Type</th>
                                    <th>Problem Status</th>
                                </tr>
                            </thead>
							<tbody>';
				$flag = 0;		
				if(count($arrProblemList)>0){					
					foreach($arrProblemList as $problemList){
						$flag = 1;
						$XML_problem_section .= '<tr ID = "PROBSUMMARY_'.$problemList['id'].'">
												<td ID = "PROBKIND_'.$problemList['id'].'">'.htmlentities($problemList['problem_name']).' [SNOMED-CT: '.$problemList['ccda_code'].']</td>
												<td>'.date('M d,Y',strtotime($problemList['onset_date'])).'</td>
												<td ID = "PROBTYPE_'.$problemList['id'].'">'.$problemList['prob_type'].'</td>
												<td ID = "PROBSTATUS_'.$problemList['id'].'">'.$problemList['status'].'</td>
											</tr>
										';
					}
				}
				
				if($flag == 0){
					$XML_problem_section .= '<tr><td colspan="4">No known health problems</td></tr>
					';
				}
				
				$XML_problem_section .= '</tbody>
								</table>
								</text>
								<!-- Problem Concern Act -->
								';
				$flag = 0;
				if(count($arrProblemList)>0){
					foreach($arrProblemList as $problemList){
						$flag = 1;
						$XML_problem_entry = '<entry>
												<act classCode="ACT" moodCode="EVN">
												<!-- Problem Concern Act template -->
												<templateId root="2.16.840.1.113883.10.20.22.4.3" extension="2015-08-01"/>
												<templateId root="2.16.840.1.113883.10.20.22.4.3"/>
												<id nullFlavor="NI"/>
												<code code="CONC" codeSystem="2.16.840.1.113883.5.6" displayName="Concern"/>
												<statusCode code="active"/>
												<effectiveTime>
													<low value="'.str_replace('-','',$problemList['onset_date']).'"/>
												</effectiveTime>
												<entryRelationship typeCode="SUBJ">
													<observation classCode="OBS" moodCode="EVN">
														<!-- Problem Observation template -->
														<templateId root="2.16.840.1.113883.10.20.22.4.4" extension="2015-08-01"/>
														<templateId root="2.16.840.1.113883.10.20.22.4.4"/>
														<id nullFlavor="NI"/>
														';
						$arrProbType = $this->problem_type_srh($problemList['prob_type']);
						if(isset($arrProbType['code']) && empty($arrProbType['code']) == false && isset($arrProbType['display_name']) && empty($arrProbType['display_name']) == false){
							$XML_problem_entry .= '<code code="'.$arrProbType['code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.$arrProbType['display_name'].'">
							';
							$translation_code = '';						  
							if($arrProbType['code']=='409586006'){//COMPLAINT.
								$translation_code = '10154-3';
							}else if($arrProbType['code']=='282291009'){//DIAGNOSIS.
								$translation_code = '29308-4';
							}else if($arrProbType['code']=='64572001'){//CONDITION.
								$translation_code = '75323-6';
							}else if($arrProbType['code']=='55607006'){//PROBLEM.
								$translation_code = '75326-9';
							}else if($arrProbType['code']=='404684003'){//FINDING
								$translation_code = '75321-0';
							}
							if($translation_code!=''){
								$XML_problem_entry .= '<translation code="'.$translation_code.'" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="'.$arrProbType['display_name'].'" />
								';
							}
							$XML_problem_entry .= '</code>';
						}
						else{
							$XML_problem_entry .= '<code nullFlavor="NI"/>';
						}
						
						$XML_problem_entry .= '<statusCode code="completed"/>';
						if($problemList['onset_date'] != ""){
							$XML_problem_entry .= '<effectiveTime>
														<low value="'.str_replace('-','',$problemList['onset_date']).'"/>
												   </effectiveTime>';
						}else{
							$XML_problem_entry .= '<effectiveTime nullFlavor="NI"/>';
						}
						// DYNAMIC PROBLEM VALUE //
						if($problemList['ccda_code']!=""){									
							$XML_problem_entry .= '<value xsi:type="CD" code="'.$problemList['ccda_code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.$problemList['problem_name'].'"/>
							';
						}else{
							$arrProblem = $this->getProblemCode($problemList['problem_name']);
							// DYNAMIC REACTION CODE //
							if(isset($arrProblem['ccda_code']) && $arrProblem['ccda_code']!="" && $arrProblem['ccda_display_name']){					
								$XML_problem_entry .= '<value xsi:type="CD" code="'.$arrProblem['ccda_code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.$arrProblem['ccda_display_name'].'"/>
								';
							}else{
								$XML_problem_entry .= '<value xsi:type="CD" code="" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.$problemList['problem_name'].'"/>
								';
							}  
						}
						$XML_problem_entry .= '</observation>
											</entryRelationship>
										</act>
									</entry>
									';
						$XML_problem_section .= $XML_problem_entry;
					}
				}
				if($flag == 0){
					$XML_problem_entry = '
							<entry typeCode="DRIV">
								<!-- Problem Concern Act -->	
								<act classCode="ACT" moodCode="EVN">
									<!-- ** Problem Concern Act (V3) ** -->
									<templateId root="2.16.840.1.113883.10.20.22.4.3" extension="2015-08-01" />
									<templateId root="2.16.840.1.113883.10.20.22.4.3" />
									<id root="36e3e930-7b14-11db-9fe1-0835200c9a66"/>
									<code code="CONC" codeSystem="2.16.840.1.113883.5.6"/>
									<text><reference value="#Concern_1"></reference></text>
									<statusCode code="active"/>
									<!-- The concern is not active, in terms of there being an active condition to be managed.-->
									<effectiveTime>
										<low value="20150722"/> <!-- Time at which THIS “concern” began being tracked.-->
									</effectiveTime> <!-- status is active so high is not applicable. If high is present it should have nullFlavor of NA-->
									<entryRelationship typeCode="SUBJ">
										<!-- Model of Meaning for No Problems -->
										<!-- The use of negationInd corresponds with the newer Observation.ValueNegationInd -->
										<!-- The negationInd = true negates the value element. --> 
										<!-- problem observation template -->
										<observation classCode="OBS" moodCode="EVN" negationInd="true">
											<!-- ** Problem observation  (V3)** -->
											<templateId root="2.16.840.1.113883.10.20.22.4.4" extension="2015-08-01"/>
											<templateId root="2.16.840.1.113883.10.20.22.4.4"/>
											<id root="4adc1021-7b14-11db-9fe1-0836200c9a67"/>
											<!-- updated for R2.1 -db -->
											<code code="55607006" displayName="Problem" codeSystemName="SNOMED-CT" codeSystem="2.16.840.1.113883.6.96">
												<!-- This code SHALL contain at least one [1..*] translation, which SHOULD be selected from ValueSet Problem Type (LOINC) -->
												<translation code="75326-9" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Problem"/>
											</code>									
											<text><reference value="#problems1"></reference></text>
											<statusCode code="completed"/>
											<!-- The time when this was biologically relevant ie True for the patient. -->
											<!-- As a minimum time interval over which this is true, populate the effectiveTime/low with the current time. -->
											<!-- It would be equally valid to have a longer range of time over which this statement was represented as being true. -->
											<!-- As a maximum, you would never indicate an effectiveTime/high that was greater than the current point in time. -->
											<effectiveTime>
												<low value="20150722"/>
											</effectiveTime>
											<!-- This idea assumes that the value element could come from the Problem value set, or-->
											<!-- when negationInd was true, is could also come from the ProblemType value set (and code would be ASSERTION). -->
											<value xsi:type="CD" code="55607006"
												displayName="Problem"
												codeSystem="2.16.840.1.113883.6.96"
												codeSystemName="SNOMED CT">
												<originalText><reference value="#problems1"></reference></originalText>
											</value>
										</observation>
									</entryRelationship>
								</act>
							</entry>';
					$XML_problem_section .= $XML_problem_entry;
				}
				$XML_problem_section .= '</section>';
				$XML_problem_section .= '</component>';	
				
			/* Problem List End */
			
			/* Lab Orders */
			$XML_results_section = '';	
				$lab_test_ordered = $this->getPatientLabOrdered($this->patientId);	
				
				$no_results_lab_tests_array = array();
				if($lab_test_ordered){
					$XML_results_section = '<component>
											<section>
											<templateId root="2.16.840.1.113883.10.20.22.2.3.1"  extension="2015-08-01"/>
											<templateId root="2.16.840.1.113883.10.20.22.2.3.1"/>
											<code code="30954-2" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="RESULTS" />
											<title>RESULTS</title>
											<text>
											<table border = "1" width = "100%">
												<thead>
													<tr>
														<th>Test Code</th>
														<th>Code System</th>
														<th colspan="3">Lab Test Name</th>
														<th>Date</th>
													</tr>
												</thead>
												<tbody>
												';
						//--- LAB REQUESTS FOUND
						$j = 1;
						foreach($lab_test_ordered as $lab_test_rs){
							if($j>1){
								$XML_results_section .= '
													<tr><td colspan="6" align="center">----</td></tr>
													<tr>
														<th>Test Code</th>
														<th>Code System</th>
														<th colspan="3">Lab Test Name</th>
														<th>Date</th>
													</tr>
								';	
							}
							$XML_results_section .= '<tr>
														<td>'.$lab_test_rs['loinc'].'</td>
														<td>LOINC</td>
														<td colspan="3">'.$lab_test_rs['service'].'</td>
														<td>'.$lab_test_rs['lab_test_date_html'].'</td>
													</tr>';
						
							$lab_test_results = $this->getPatientLabResults($lab_test_rs['lab_test_id'],$this->patientId);
							if($lab_test_results){
								$XML_results_section .= '
														<tr>
															<th colspan="7">LABORATORY TEST RESULTS</th>
														</tr>
														<tr>
															<th>Result Code</th>
															<th>Code System</th>
															<th>Type</th>
															<th>Value &amp; Units</th>
															<th>Date</th>
															<th>Ref. Range</th>
														</tr>
														
														';
								//--- LAB RESULTS FOUND				
								foreach($lab_test_results as $lab_result_rs){
									$XML_results_section .= '<tr>
																<td>'.$lab_result_rs['result_loinc'].'</td>
																<td>LOINC</td>
																<td>'.$lab_result_rs['observation'].'</td>
																<td>'.$lab_result_rs['result'].' '.$lab_result_rs['uom'].'</td>
																<td>'.$lab_result_rs['lab_result_date_html'].'</td>
																<td>'.$lab_result_rs['result_range'].'</td>
															</tr>
															';
								}
								if($lab_test_rs['lab_destination']){
									$temp_lab_contact_name_arr 	= explode('-',$lab_test_rs['lab_destination']['lab_contact_name']);
									$lab_contact_name 			= trim($temp_lab_contact_name_arr['0']);
									$lab_contact_id 			= trim($temp_lab_contact_name_arr['1']);
									$lab_address_arr = array();
									if($lab_contact_id) 	$lab_address_arr['ID'] = $lab_contact_id;
									if($lab_contact_name) 	$lab_address_arr['Lab Name'] = $lab_contact_name;
									$lab_address_arr['Address']	= $lab_test_rs['lab_destination']['lab_radiology_address'];
									$lab_address_arr['City']	= $lab_test_rs['lab_destination']['lab_radiology_city'];
									$lab_address_arr['State']	= $lab_test_rs['lab_destination']['lab_radiology_state'];
									$lab_address_arr['Zip']		= $lab_test_rs['lab_destination']['lab_radiology_zip'];
									$lab_address_arr['Phone']	= $lab_test_rs['lab_destination']['lab_radiology_phone'];
									
									$XML_results_section .= '<tr>
											<td colspan="6">';
									foreach($lab_address_arr as $lab_ad_key=>$lab_ad_val){
										if(trim($lab_ad_val)=='') continue;
										$XML_results_section .= ''.$lab_ad_key.': '.$lab_ad_val.'<br/>
										';
									}
									
									$XML_results_section .=	'
											</td>
										</tr>
										';
								}
							}else{
								$no_results_lab_tests_array[] = $lab_test_rs['lab_test_id'];
								$XML_results_section .= '<tr>
														<td colspan="6">No result information.</td>
													</tr>';	
							}
							$j++;
						}
						$XML_results_section .= '</tbody>
											</table>
											</text>
											';
				}else{
				//--- LAB REQUESTS NOT FOUND
						$XML_results_section .= '
						<component>
							<section nullFlavor="NI">
								<!-- Results Section (entries required) (V3) -->
								<templateId root="2.16.840.1.113883.10.20.22.2.3.1" extension="2015-08-01"/>
								<templateId root="2.16.840.1.113883.10.20.22.2.3.1"/>
								<code code="30954-2" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="RESULTS"/>
								<title>RESULTS</title>
								<text>Laboratory Tests: No Lab Tests Information. Laboratory Values/Results: No Lab Results Information</text>
							</section>
						</component>	
					';
				}
				
				if($lab_test_ordered){
					//--- LAB REQUESTS FOUND
					foreach($lab_test_ordered as $lab_test_rs){
						$current_result_lab_test_id = $lab_test_rs['lab_test_id'];			
						if(in_array($current_result_lab_test_id,$no_results_lab_tests_array)) continue; //skip this loop.
						
						$XML_results_section .= '<entry typeCode="DRIV">
												 <!-- Result organizer template  -->
												 <organizer classCode="BATTERY" moodCode="EVN">
													<templateId root="2.16.840.1.113883.10.20.22.4.1" extension="2015-08-01"/>
													<templateId root="2.16.840.1.113883.10.20.22.4.1"/>
													<id nullFlavor="NI"/>
													';
						if($lab_test_rs['loinc']!= "" && $lab_test_rs['service'] != ""){					
							$XML_results_section .= '<code code="'.$lab_test_rs['loinc'].'" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="'.$lab_test_rs['service'].'"/>';
						}else{
							$XML_results_section .= '<code nullFlavor="NI"/>';
						}
						//$XML_results_section .= '<code code="22032-7" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Lab tests Narrative"/>';
						
						$arrResultStatus = $this->result_status_srh($lab_test_rs['lab_status']);	
						if($arrResultStatus['code']!="" && $arrResultStatus['display_name']!="")					
							$XML_results_section .= '<statusCode code="'.$arrResultStatus['code'].'"/>';
						else
							$XML_results_section .= '<statusCode nullFlavor="NI"/>';
						
						if($lab_test_rs['lab_test_date_ccd']!='00000000'){
							$XML_results_section .= '<effectiveTime>
														<low value="'.$lab_test_rs['lab_test_date_ccd'].'"/>
														<high value="'.$lab_test_rs['lab_test_date_ccd'].'"/>
													 </effectiveTime>
							';
						}else{
							$XML_results_section .= '<effectiveTime nullFlavor="NI"/>
							';	
						}
						$lab_test_results = $this->getPatientLabResults($lab_test_rs['lab_test_id'],$this->patientId);
						if($lab_test_results){
							//-----------STARTING LAB RESULT COMPONENT------
							$i = 1;
							foreach($lab_test_results as $lab_result_rs){
								$current_result_lab_test_id = $lab_result_rs['lab_test_id'];
								if(in_array($current_result_lab_test_id,$no_results_lab_tests_array)) continue; //skip this loop.
								$XML_results_section .= '
											<component>
												<observation classCode="OBS" moodCode="EVN">
												<!-- Result observation template -->
												<templateId root="2.16.840.1.113883.10.20.22.4.2" extension="2015-08-01"/>
												<templateId root="2.16.840.1.113883.10.20.22.4.2"/>
												<id nullFlavor="NI"/>';
								/* DYNAMIC CODE FROM LOINC ResultTypeCode  */
								if($lab_result_rs['observation'] != "")						
									$XML_results_section .= '<code code="'.$lab_result_rs['result_loinc'].'" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="'.$lab_result_rs['observation'].'"/>';
								else
									$XML_results_section .= '<code nullFlavor="NI"/>
									';
								$XML_results_section .= '<text>
															<reference value="#result'.$i.'"/>
														 </text>
														 <statusCode code="completed"/>
														 ';
								
								if($lab_result_rs['lab_result_date_ccd'] != "00000000")
									$XML_results_section .= '<effectiveTime value="'.$lab_result_rs['lab_result_date_ccd'].'"/>
									';
								else
									$XML_results_section .= '<effectiveTime nullFlavor="NI"/>
									';
								
								$place_observation_range = false;	
								if($lab_result_rs['result'] != "" && $lab_result_rs['result_range']!="" && $lab_result_rs['result']==$lab_result_rs['result_range']){
									$XML_results_section .= '<value xsi:type="ST">'.trim($lab_result_rs['result']).'</value>
									';
								}else if($lab_result_rs['result'] != "" && $lab_result_rs['result_range']!="" && $lab_result_rs['uom']!="" && $lab_result_rs['result']!=$lab_result_rs['result_range']){
									$XML_results_section .= '<value xsi:type="PQ" value="'.trim($lab_result_rs['result']).'" unit="'.trim($lab_result_rs['uom']).'"/>
									';
								}else if($lab_result_rs['result'] != "" && $lab_result_rs['result_range']!="" && $lab_result_rs['uom']=="" && $lab_result_rs['result']!=$lab_result_rs['result_range']){
									$XML_results_section .= '<value xsi:type="PQ" value="'.trim($lab_result_rs['result']).'"/>
									';
									$place_observation_range = true;
								}else{
									$XML_results_section .= '<value xsi:type="PQ" nullFlavor="NI"/>
									';
								}
								if($lab_result_rs['abnormal_flag']=='') $lab_result_rs['abnormal_flag'] = 'N';
								if($lab_result_rs['abnormal_flag'] != "")
									$XML_results_section .= '<interpretationCode code="'.$lab_result_rs['abnormal_flag'].'" codeSystem="2.16.840.1.113883.5.83"/>
									';
								else
									$XML_results_section .= '<interpretationCode nullFlavor="NI"/>';
								$XML_results_section .= '
														</observation>
														</component>';
								
									
								$i++;
							}
							//-------END OF LAB RESULT COMPONENT-----------
						}else{
						//---code here IF NO RESULT FOUND----
							$XML_results_section .= '<component>
													<observation classCode="OBS" moodCode="EVN">
													<!-- Result observation template -->
													<templateId root="2.16.840.1.113883.10.20.22.4.2" extension="2015-08-01"/>
													<templateId root="2.16.840.1.113883.10.20.22.4.2"/>
													<id nullFlavor="NI"/>
													<code nullFlavor="NI"/>
													<statusCode code="completed"/>
													<effectiveTime nullFlavor="NI"/>
													<value xsi:type="PQ" nullFlavor="NI"/>
													</observation>
												  </component>';				
						}
						$XML_results_section .= '	</organizer>
												</entry>';
					}
				}
				if($lab_test_ordered){
					$XML_results_section .= '</section>';
					$XML_results_section .= '</component>';
				}
			/* Lab Orders End */
			
			/* Assessment */
				$arrApVals = array();
				$row = $this->valuesNewRecordsAssess($this->patientId);
				
				if($row != false){
					$strXml = stripslashes($row["assess_plan"]);
					$arrApVals = $this->getXmlArr($strXml);
					$arrApVals = $arrApVals['data']['ap'];
				}
				
				$XML_assessment_section = '
				<!-- ASSESSMENT SECTION -->
				<!-- Tere is no R2.1 (R2.0) version of assessment section, using R1.1 templateId only -->
											<component>
											<section>
											<templateId root="2.16.840.1.113883.10.20.22.2.8"/>
											<code codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" code="51848-0" displayName="ASSESSMENTS"/>
											<title>ASSESSMENTS &amp; PLAN OF TREATMENT</title>
											<text>
												<table border = "1" width = "100%">
													<thead>
														<tr>
															<th>Assessment</th>
														</tr>
													</thead>
													 <tbody>';
				$flag = 0;
				foreach($arrApVals as $apVals){
					if($apVals['assessment'] != ""){
						$split_by_colon = explode(';',$apVals['assessment']);
						if(isset($split_by_colon[1])){
							$temp_assess_text_part = explode('(',trim($split_by_colon[1]));
						}else{
							$temp_assess_text_part = explode('(',trim($split_by_colon[0]));	
						}
						$assess_text_part	= $temp_assess_text_part[0];
						$flag = 1;
						$XML_assessment_section .= '<tr>
													<td>'.htmlentities($assess_text_part).'</td>
												</tr>
												<tr>
													<th>Plan of Treatment</th>
												</tr>
												<tr>
													<td>'.nl2br($apVals['plan']).'</td>
												</tr>
												';
					}
				}
				if($flag == 0){
					$XML_assessment_section .= '<tr><td>No data.</td></tr>
						';
				}
				$XML_assessment_section .= 	'
									</tbody>
									</table>
									</text>
									</section>
									</component>';
			
			/* Assessment End */
			
			/***************/
			
			
			/* Patient Goals */
			$qry_form_id='';if(empty($this->form_id) == false){$qry_form_id=' AND form_id ="'.$this->form_id.'"';}
			$goal_qry = 'SELECT `id`, `patient_id`, `form_id`, `goal_set`, `loinc_code`, `goal_data`, `goal_data_type`, `gloal_data_type_unit`, `operator_id`,  DATE_FORMAT(`goal_date`, \'%b %d, %Y\') AS \'goal_date\',DATE_FORMAT(`goal_date`, \'%Y%m%d\') AS \'goal_date_show\' from patient_goals where  patient_id="'.$this->patientId.'" AND delete_status = 0 '.$qry_form_id;
			$row_arr = array();
			$res = $dbh->imw_query($goal_qry);
			if($res && $dbh->imw_num_rows($res) > 0){
				while($row = $dbh->imw_fetch_assoc($res)){
					$row_arr[] = $row;
				}
			}
			$XML_goals_section = '
				<!-- GOAL SECTION -->
					<component>
							<section '.((is_array($row_arr) && count($row_arr) <= 0) ? 'nullFlavor="NI"' : '').'>
							<templateId root="2.16.840.1.113883.10.20.22.2.60"/>
							<code code="61146-7" displayName="Goals" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"/>
							<title>GOALS</title>
							<text>
								<table border = "1" width = "100%">
									<thead>
										<tr>
											<th>Goal</th>
											<th>Value</th>
											<th>Date</th>
										</tr>
									</thead>
									<tbody>
									';
			if(is_array($row_arr) && count($row_arr)>0){
				$xml_goals_entry = '';
				foreach($row_arr as $res_arr_goal){
					$goal_set=trim(addslashes($res_arr_goal["goal_set"]));
					$loinc_code=trim(addslashes($res_arr_goal["loinc_code"]));
					$goal_data=trim(addslashes($res_arr_goal["goal_data"]));
					$goal_data_type=trim(addslashes($res_arr_goal["goal_data_type"]));
					$gloal_data_type_unit=trim(addslashes($res_arr_goal["gloal_data_type_unit"]));
					$goal_date=trim(addslashes($res_arr_goal["goal_date"]));
					$goal_date_show=trim(addslashes($res_arr_goal["goal_date_show"]));
					$operator_id=trim($res_arr_goal["operator_id"]);
					
					$qry_user = $dbh->imw_query("Select fname,lname,mname,pro_suffix from users where id='".$operator_id."'");
					$fname = $lname = $mname = $pro_suffix = '';
					if($qry_user && $dbh->imw_num_rows($qry_user) > 0){
						$res_user = $dbh->imw_fetch_assoc($qry_user);
						$fname=$res_user["fname"];
						$lname=$res_user["lname"];
						$mname=$res_user["mname"];
						$pro_suffix=$res_user["pro_suffix"];
					}
					$XML_goals_section.='<!-- the following two do not need to be coded in entries and can be combined together -->';
					$XML_goals_section.='<tr>';
					$XML_goals_section.='<td>'.$goal_set.'</td>';
					$XML_goals_section.='<td>'.$goal_data.'</td>';
					$XML_goals_section.='<td>'.$goal_date.'</td>';
					$XML_goals_section.='</tr>';
					
					
					
					$xml_goals_entry .= '<entry>';
					$xml_goals_entry.='<!-- Goal Observation -->';
					$xml_goals_entry.='<observation classCode="OBS" moodCode="GOL">';
					$xml_goals_entry.='<templateId root="2.16.840.1.113883.10.20.22.4.121"/>';
					$xml_goals_entry.='<id root="3700b3b0-fbed-11e2-b778-0800200c9a66"/>';
					$xml_goals_entry.='<!-- TODO (min - not required for test data): find a more suitable LOINC code for generic fever or for Visual Inspection -db -->';
					$xml_goals_entry.='<code code="'.$loinc_code.'" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="'.$goal_set.'"/>';
					$xml_goals_entry.='<statusCode code="active"/>';
					$xml_goals_entry.='<effectiveTime value="'.$goal_date_show.'"/>';
					$xml_goals_entry.='<!-- this may not be the recommended way to record a visual inspection -db -->';
					if(empty($goal_data)){
						$xml_goals_entry.='<value xsi:type="ST" nullFlavor="NP" />';
					}else{
						$xml_goals_entry.='<value xsi:type="ST">'.$goal_data.'</value>';
					}
					$xml_goals_entry.='<author>';
					$xml_goals_entry.='<templateId root="2.16.840.1.113883.10.20.22.4.119"/>';
					$xml_goals_entry.='<time value="'.$goal_date_show.'"/>';
					$xml_goals_entry.='<assignedAuthor>';
					$xml_goals_entry.='<id root="d839038b-7171-4165-a760-467925b43857"/>';
					$xml_goals_entry.='<code code="163W00000X" displayName="Registered nurse" codeSystem="2.16.840.1.113883.6.101" codeSystemName="Healthcare Provider Taxonomy (HIPAA)"/>';
					$xml_goals_entry.='<assignedPerson>';
					$xml_goals_entry.='<name>';
					$xml_goals_entry.='<given>'.$fname.'</given>';
					$xml_goals_entry.='<family>'.$lname.'</family>';
					if(empty($pro_suffix)){
						$xml_goals_entry.='<suffix nullFlavor="NP"></suffix>';
					}else{
						$xml_goals_entry.='<suffix>'.$pro_suffix.'</suffix>';
					}
					$xml_goals_entry.='</name>';
					$xml_goals_entry.='</assignedPerson>';
					$xml_goals_entry.='</assignedAuthor>';
					$xml_goals_entry.='</author>';
					$xml_goals_entry.='<!-- Patient Author -->';
					$xml_goals_entry.='<author typeCode="AUT">';
					$xml_goals_entry.='<templateId root="2.16.840.1.113883.10.20.22.4.119"/>';
					$xml_goals_entry.='<time/>';
					$xml_goals_entry.='<assignedAuthor>';
					$xml_goals_entry.='<!-- This id can point back to the record target already described in the CDA header (or someone else can be described here) -->';
					$xml_goals_entry.='<!-- This particular example points back to the record target -->';
					$xml_goals_entry.='<id extension="996-756-495" root="2.16.840.1.113883.19.5"/>';
					$xml_goals_entry.='</assignedAuthor>';
					$xml_goals_entry.='</author>';
					$xml_goals_entry.='</observation>';
					$xml_goals_entry .= '</entry>';
					
				}
			}else{
				$XML_goals_section .= '<tr><td colspan="3">No Goals Data</td></tr>';
			}

			$XML_goals_section.='</tbody></table></text>';
			$XML_goals_section .= $xml_goals_entry;
			$XML_goals_section.='</section></component>';

		/* Patient Goals End */


		/* Health Concerns */
		$qry_form_id='';if(!empty($this->form_id)){$qry_form_id=' AND form_id ="'.$this->form_id.'"';}
		$observatin_qry = "SELECT
						`id`,
						`observation`,
						DATE_FORMAT(`observation_date`, '%Y%m%d') AS 'observation_date_raw', 
						DATE_FORMAT(`observation_date`, '%b %d, %Y') AS 'observation_date',
						`snomed_code`,
						`status` 
					FROM 
						`hc_observations` 
					WHERE 
						`pt_id` = '".$this->patientId."' 
						AND `del_status` = 0 ".$qry_form_id;
						
		$res_sql = $dbh->imw_query($observatin_qry);
		$resp_observation = array();
		if($res_sql && $dbh->imw_num_rows($res_sql) > 0){
			while($row = $dbh->imw_fetch_assoc($res_sql)){
				$resp_observation[] = $row;
			}
		}
		unset($res_sql);

		$qry_form_id = '';
		$qry_form_id='';if(!empty($this->form_id)){$qry_form_id=' AND `obs`.form_id ="'.$this->form_id.'"';}
		$res_qry = "SELECT
						`c`.`id`,
						`c`.`concern`,
						DATE_FORMAT(`c`.`concern_date`, '%Y%m%d') AS 'concern_date_raw', 
						DATE_FORMAT(`c`.`concern_date`, '%b %d, %Y') AS 'concern_date', 
						`c`.`status` 
					FROM 
						`hc_concerns` `c`
						INNER JOIN `hc_observations` `obs` ON(`c`.`observation_id`=`obs`.`id`)
					WHERE 
						`obs`.`pt_id` = '".$this->patientId."' 
						AND `c`.`del_status` = 0 ".$qry_form_id;
		$res_sql = $dbh->imw_query($res_qry);
		$resp_concern = array();
		if($res_sql && $dbh->imw_num_rows($res_sql) > 0){
			while($row = $dbh->imw_fetch_assoc($res_sql)){
				$resp_concern[] = $row;
			}
		}
		unset($res_sql);

		$rel_observaton_qry = "SELECT
								`rel`.`id`,
								`rel`.`rel_observation`,
								DATE_FORMAT(`rel`.`rel_observation_date`, '%Y%m%d') AS 'rel_observation_date_raw', 
								DATE_FORMAT(`rel`.`rel_observation_date`, '%b %d, %Y') AS 'rel_observation_date',
								`rel`.`snomed_code`
							FROM 
								`hc_rel_observations` `rel`
								INNER JOIN `hc_observations` `obs` ON(`rel`.`observation_id`=`obs`.`id`)
							WHERE 
								`obs`.`pt_id` = '".$this->patientId."' 
								AND `rel`.`del_status` = 0 ".$qry_form_id;
		$res_sql = $dbh->imw_query($rel_observaton_qry);
		$resp_rel_observation = array();
		if($res_sql && $dbh->imw_num_rows($res_sql) > 0){
			while($row = $dbh->imw_fetch_assoc($res_sql)){
				$resp_rel_observation[] = $row;
			}
		}
		unset($res_sql);

		$component = new SimpleXMLElement('<component/>');

			$section = $component->addChild('section');
			$section->addAttribute('nullFlavor', 'NI');
			
			/*Start static Data for the Section*/
			$templateId = $section->addChild('templateId');
			$templateId->addAttribute('root', '2.16.840.1.113883.10.20.22.2.58');
			$templateId->addAttribute('extension', '2015-08-01');
			
			$templateId = $section->addChild('code');
			$templateId->addAttribute('code', '75310-3');
			$templateId->addAttribute('displayName', 'Health Concerns Document');
			$templateId->addAttribute('codeSystem', '2.16.840.1.113883.6.1');
			$templateId->addAttribute('codeSystemName', 'LOINC');
			
			$section->addChild('title', 'Health Concerns Section');
			/*End static Data for the Section*/
			
			/*Dynamic Data - Starting <text> element*/
			$text = $section->addChild('text');
			
			/*Observation table*/
				$observation = $text->addChild('table');
				$text->addChild('br');
				
				$observation->addAttribute('border', '1');
				$observation->addAttribute('width', '100%');
				
				/*Static table head*/
				$observationThead = $observation->addChild('thead');
				$observationTheadTr = $observationThead->addChild('tr');
				
				$observationTheadTr->addChild('th', 'Observations');
				$observationTheadTr->addChild('th', 'Status');
				$observationTheadTr->addChild('th', 'Date');
				/*End Static table head*/
				
				/*Dynamic Table Rows*/
				$observationTbody = $observation->addChild('tbody');
				
				
				if( $resp_observation && count($resp_observation) > 0 && is_array($resp_observation))
				{
					foreach($resp_observation as $row)
					{
						$observationTbodyTr = $observationTbody->addChild('tr');;
						$observationTbodyTr->addChild('td', addslashes($row['observation']));
						$observationTbodyTr->addChild('td', addslashes($row['status']));
						$observationTbodyTr->addChild('td', addslashes($row['observation_date']));
						
						/*Entry Tag*/
						$entry1 = $section->addChild('entry');
						$entryObervation = $entry1->addChild('observation');
							$entryObervation->addAttribute('classCode', 'OBS');
							$entryObervation->addAttribute('moodCode', 'EVN');
						
						$template1 = $entryObervation->addChild('templateId');
						$template1->addAttribute('root', '2.16.840.1.113883.10.20.22.4.5');
						$template1->addAttribute('extension', '2014-06-09');
						$entryObervation->addChild('templateId')->addAttribute('root', '2.16.840.1.113883.10.20.22.4.5');
						
						$entryObervation->addChild('id')->addAttribute('root', $this->createGUID((int)$row['id'], 'observation'));
						
						$code = $entryObervation->addChild('code');
						$code->addAttribute('code', '11323-3');
						$code->addAttribute('codeSystem', '2.16.840.1.113883.6.1');
						$code->addAttribute('codeSystemName', 'LOINC');
						$code->addAttribute('displayName', 'Health status');
						
						//$entryObervation->addChild('statusCode')->addAttribute('code', addslashes(strtolower($row['status'])));
						$entryObervation->addChild('statusCode')->addAttribute('code', 'completed');
						
						$value = $entryObervation->addChild('value');
						$value->addAttribute('xmlns:xsi:type', 'CD');
						$value->addAttribute('code', addslashes($row['snomed_code']));
						$value->addAttribute('codeSystem', '2.16.840.1.113883.6.96');
						$value->addAttribute('codeSystemName', 'SNOMED-CT');
						$value->addAttribute('displayName', addslashes($row['observation']));
					}
				}
				else
				{
					$observationTbodyTr = $observationTbody->addChild('tr');
					$observationTbodyTrTd = $observationTbodyTr->addChild('td', 'No Health Observation');
					$observationTbodyTrTd->addAttribute('colspan', '3');
				}
				/*End Dynamic Table Rows*/
			/*End Observation table*/
			
			/*Concern table*/
				$concern = $text->addChild('table');
				$text->addChild('br');
				
				$concern->addAttribute('border', '1');
				$concern->addAttribute('width', '100%');
				
				/*Static table head*/
				$concernThead = $concern->addChild('thead');
				$concernTheadTr = $concernThead->addChild('tr');
				
				$concernTheadTr->addChild('th', 'Concern - HealthCare Concerns refer to underlying clinical facts');
				$concernTheadTr->addChild('th', 'Status');
				$concernTheadTr->addChild('th', 'Date');
				/*End Static table head*/
				
				/*Dynamic Table Rows*/
				$concernTbody = $concern->addChild('tbody');
				
				if( $resp_concern && count($resp_concern) > 0 && is_array($resp_concern))
				{
					/*Entry Tag*/
					$entry2 = $section->addChild('entry');
					$actConcern = $entry2->addChild('act');
					$actConcern->addAttribute('classCode', 'ACT');
					$actConcern->addAttribute('moodCode', 'EVN');
					
					$template = $actConcern->addChild('templateId');
					$template->addAttribute('root', '2.16.840.1.113883.10.20.22.4.132');
					$template->addAttribute('extension', '2015-08-01');
					
					$actConcern->addChild('id')->addAttribute('root', $this->createGUID((int)$row['id'], 'concernOuter'));
					
					$code = $actConcern->addChild('code');
					$code->addAttribute('code', '75310-3');
					$code->addAttribute('codeSystem', '2.16.840.1.113883.6.1');
					$code->addAttribute('codeSystemName', 'LOINC');
					$code->addAttribute('displayName', 'Health Concern');
					
					//$actConcern->addChild('statusCode')->addAttribute('code', addslashes(strtolower($row['status'])));
					$actConcern->addChild('statusCode')->addAttribute('code', 'completed');
						
					foreach($resp_concern as $row)
					{
						$concernTbodyTr = $concernTbody->addChild('tr');;
						$concernTbodyTr->addChild('td', addslashes($row['concern']));
						$concernTbodyTr->addChild('td', addslashes($row['status']));
						$concernTbodyTr->addChild('td', addslashes($row['concern_date']));
						
						$concern = $actConcern->addChild('entryRelationship');
						$concern->addAttribute('typeCode', 'REFR');
						
						$act = $concern->addChild('act');
						$act->addAttribute('classCode', 'ACT');
						$act->addAttribute('moodCode', 'EVN');
						
						$act->addChild('templateId')->addAttribute('root', '2.16.840.1.113883.10.20.22.4.122');
						
						$act->addChild('id')->addAttribute('root', $this->createGUID((int)$row['id'], 'concern'));
						
						$act->addChild('code')->addAttribute('nullFlavor', 'NP');
						
						$act->addChild('statusCode')->addAttribute('code', addslashes(strtolower($row['status'])));
					}
				}
				else
				{
					$concernTbodyTr = $concernTbody->addChild('tr');
					$concernTbodyTrTd = $concernTbodyTr->addChild('td', 'No Health Concern.');
					$concernTbodyTrTd->addAttribute('colspan', '3');
				}
				/*End Dynamic Table Rows*/
			/*End Concern table*/
			
			
			/*Related Observations table*/
				$relObservation = $text->addChild('table');
				$text->addChild('br');
				
				$relObservation->addAttribute('border', '1');
				$relObservation->addAttribute('width', '100%');
				
				/*Static table head*/
				$relObservationThead = $relObservation->addChild('thead');
				$relObservationTheadTr = $relObservationThead->addChild('tr');
				
				$relObservationTheadTr->addChild('th', 'Related observation');
				$relObservationTheadTr->addChild('th', 'Date');
				/*End Static table head*/
				
				/*Dynamic Table Rows*/
				$relObservationTbody = $relObservation->addChild('tbody');
				
				if( $resp_rel_observation && count($resp_rel_observation) > 0 && is_array($resp_rel_observation))
				{
					foreach($resp_rel_observation as $row)
					{
						$relObservationTbodyTr = $relObservationTbody->addChild('tr');;
						$relObservationTbodyTr->addChild('td', addslashes($row['rel_observation']));
						$relObservationTbodyTr->addChild('td', addslashes($row['rel_observation_date']));
						
						
						/*Entry Tag*/
						if( !isset($actConcern) && !is_object($actConcern) )
						{
							$entry2 = $section->addChild('entry');
							$actConcern = $entry2->addChild('act');
							$actConcern->addAttribute('classCode', 'ACT');
							$actConcern->addAttribute('moodCode', 'EVN');
						}
						
						$entryRel = $actConcern->addChild('entryRelationship');
						$entryRel->addAttribute('typeCode', 'REFR');
						$entryRel->addAttribute('inversionInd', 'true');
						
						$obsRel = $entryRel->addChild('observation');
						$obsRel->addAttribute('classCode', 'OBS');
						$obsRel->addAttribute('moodCode', 'EVN');
						
						$template = $obsRel->addChild('templateId');
						$template->addAttribute('root', '2.16.840.1.113883.10.20.22.4.4');
						$template->addAttribute('extension', '2014-06-09');
						
						$obsRel->addChild('templateId')->addAttribute('root', '2.16.840.1.113883.10.20.22.4.4');
						
						$obsRel->addChild('id')->addAttribute('root', $this->createGUID((int)$row['id'], 'obsRel'));
						
						$code = $obsRel->addChild('code');
						$code->addAttribute('code', '29308-4');
						$code->addAttribute('codeSystem', '2.16.840.1.113883.6.1');
						$code->addAttribute('codeSystemName', 'LOINC');
						$code->addAttribute('displayName', 'Diagnosis');
						
						$obsRel->addChild('statusCode')->addAttribute('code', 'completed');
						
						$obsRel->addChild('effectiveTime')->addChild('low')->addAttribute('value', $row['rel_observation_date_raw']);
						
						$value = $obsRel->addChild('value');
						$value->addAttribute('xmlns:xsi:type', 'CD');
						$value->addAttribute('code', addslashes($row['snomed_code']));
						$value->addAttribute('codeSystem', '2.16.840.1.113883.6.96');
						$value->addAttribute('codeSystemName', 'SNOMED-CT');
						$value->addAttribute('displayName', addslashes($row['rel_observation']));
					}
				}
				else
				{
					$relObservationTbodyTr = $relObservationTbody->addChild('tr');
					$relObservationTbodyTrTd = $relObservationTbodyTr->addChild('td', 'No Related Observation.');
					$relObservationTbodyTrTd->addAttribute('colspan', '2');
				}
				/*End Dynamic Table Rows*/
			/*End Concern table*/
			
			
			
			/*End Dynamic Data - <text>*/
			

		$finalHealthConcern = $component->saveXML();
		$finalHealthConcern = str_replace("<?xml version=\"1.0\"?>\n", "", $finalHealthConcern);


		/* Health Concerns End */
		
		/* Encounters */
				$XML_encouters_section = $XML_encouter_entry = '';
				$encounter_diagnosis = $this->getEncounterDiagnosis($this->form_id, $this->patientId);
				
				if($encounter_diagnosis){
					$XML_encouters_section = '<component>
												<section>
												<templateId root="2.16.840.1.113883.10.20.22.2.22.1" extension="2015-08-01"/>
												<templateId root="2.16.840.1.113883.10.20.22.2.22.1"/>
												<code code="46240-8" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="History of encounters"/>
												<title>ENCOUNTERS</title>
												<text>
												<table border = "1" width = "100%">
												<thead>
												<tr>
													<th>Encounter Diagnosis</th>
													<th>Location</th>
													<th>Date</th>
												</tr>
												</thead>
												<tbody>
												';
					$flag = 0;
					foreach($encounter_diagnosis as $problemList){	
						//if(!in_array($problemList['problem_name'],$arrMedHxProbList)){
						$flag = 1;			
						$XML_encouters_section .= '
							<tr>
								<td ID="enc_problem'.$problemList['id'].'">'.htmlentities($problemList['problem_name']).'</td>';

							$encounter_diag_location = $this->getEncounterFacility($problemList['form_id'],$problemList['pt_id']);
							$XML_encouters_section .= '
								<td ID="enc_problem_location'.$problemList['id'].'">'.htmlentities($encounter_diag_location['name']). " - ".$encounter_diag_location['street'].",".$encounter_diag_location['city']." ".$encounter_diag_location['state'].' - '.$encounter_diag_location['postal_code'].'</td>';
								
						$XML_encouters_section .= '
								<td ID="enc_problem_date'.$problemList['id'].'">'.date('M d,Y',strtotime($problemList['onset_date'])).'</td>
							</tr>
							';
						//}
					}
					
					if($flag == 0){
						$XML_encouters_section .= '<tr><td>No Data.</td></tr>
						';
					}
					$XML_encouters_section .= '</tbody>
											</table>
										</text>
										';
								
					/* BEGIN ENCOUNTER ACTIVITIES */
					foreach($encounter_diagnosis as $problemList){
					
						$XML_encouter_entry = '<entry typeCode="DRIV">
												<encounter classCode="ENC" moodCode="EVN">
												<!-- Encounter Activities -->
												<templateId root="2.16.840.1.113883.10.20.22.4.49" extension="2015-08-01"/>
												<templateId root="2.16.840.1.113883.10.20.22.4.49"/>
												<id nullFlavor="NI"/>
												';
						/* DYNAMIC ENCOUNTER TYPE CODE TO GET FROM CHART ENTRY*/ 
						$sql = "SELECT ct.ccda_cpt_code 
								FROM chart_master_table cmt
								JOIN chart_template ct ON cmt.templateId = ct.id
								WHERE id='".$problemList['form_id']."' AND patient_id = '".$problemList['pt_id']."'";
						$res = $dbh->imw_query($sql);
						$row = $dbh->imw_fetch_assoc($res);	
						if($row['ccda_cpt_code'] != "" && $row['ccda_cpt_code']!=0){				
							$XML_encouter_entry .= '<code code="'.$row['ccda_cpt_code'].'" displayName="'.$row['temp_name'].'" codeSystem="2.16.840.1.113883.6.12" codeSystemVersion="4">
														<translation code="AMB" codeSystem="2.16.840.1.113883.5.4" displayName="Ambulatory" codeSystemName="HL7 ActEncounterCode"/>
													</code>
													';
						}else if($row['ccda_cpt_code']==0){				
							$XML_encouter_entry .= '<code code="0123" displayName="comprehensive" codeSystem="2.16.840.1.113883.6.12" codeSystemVersion="4">
														<translation code="AMB" codeSystem="2.16.840.1.113883.5.4" displayName="Ambulatory" codeSystemName="HL7 ActEncounterCode"/>
													</code>
													';
						}else{
							$XML_encouter_entry .= '<code nullFlavor="NI"/>
								';
						}
						
						$sql = "SELECT cmt.date_of_service as date_of_service,ut.user_type_name as user_type
							FROM chart_master_table cmt 
							JOIN users usr ON usr.id = cmt.providerId
							JOIN user_type ut ON usr.user_type = ut.user_type_id
							WHERE cmt.id = '".$problemList['form_id']."'";
						$res = $dbh->imw_query($sql);
						$row = $dbh->imw_fetch_assoc($res);
					
						if($row['date_of_service'] != ""){
							$XML_encouter_entry .= '<effectiveTime value="'.str_replace("-","",$row['date_of_service']).'"/>';
						}else{
							$XML_encouter_entry .= '<effectiveTime nullFlavor="NI"/>';
						}
					
						$arrProviderType = $this->get_provider_code($row['user_type']);
					
						//------BEGIN CHART PROVIDER INFO --------
						if($arrProviderType['code'] != "" && $arrProviderType['display_name'] != ""){
						$XML_encouter_entry .= '<performer>
												<assignedEntity>
												<id nullFlavor="NI"/>
												<code code="'.$arrProviderType['code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.$arrProviderType['display_name'].'"/>
												</assignedEntity>
												</performer>
												';
						}
					
						//------END CHART PROVIDER INFO --------						
						$encounter_diag_location = $this->getEncounterFacility($problemList['form_id'],$problemList['pt_id']);
						if($encounter_diag_location){
							//---------BEGIN LOCATION ----------
							$XML_encouter_entry .= '<participant typeCode = "LOC">
														<participantRole classCode = "SDLOC">
														<templateId root = "2.16.840.1.113883.10.20.22.4.32"/>
														<!--Service Delivery Location template -->
														<code nullFlavor="NI"/>
														<addr>
														';
							if($encounter_diag_location['street'] != "")
								$XML_encouter_entry .= '<streetAddressLine>'.$encounter_diag_location['street'].'</streetAddressLine>';
							if($encounter_diag_location['city'] != "")
								$XML_encouter_entry .= '<city>'.$encounter_diag_location['city'].'</city>';
							if($encounter_diag_location['state'] != "")
								$XML_encouter_entry .= '<state>'.$encounter_diag_location['state'].'</state>';
							if($encounter_diag_location['postal_code'] != "")
								$XML_encouter_entry .= '<postalCode>'.$encounter_diag_location['postal_code'].'</postalCode>';
							
							$XML_encouter_entry .= '<country>US</country>
												</addr>
												';	
							if($encounter_diag_location['phone'] != "")
								$XML_encouter_entry .= '<telecom use="WP" value="tel:+1-'.$this->core_phone_format($encounter_diag_location['phone']).'"/>
								';
							else
								$XML_encouter_entry .= '<telecom nullFlavor="NI"/>
								';
							$XML_encouter_entry .='
											<playingEntity classCode = "PLC">
												<name>'.htmlentities($encounter_diag_location['name']).'</name>
											</playingEntity>
										</participantRole>
									</participant>
									';
						}
						//---------END LOCATION ----------			
							$XML_encouter_entry .= '<entryRelationship typeCode="SUBJ" >';
							/* BEGIN ENCOUNTER DIAGNOSIS ACT*/
							$XML_encouter_entry .= '<act classCode="ACT" moodCode="EVN">
													<!-- Encounter diagnosis act -->
													<templateId root="2.16.840.1.113883.10.20.22.4.80" extension="2015-08-01"/>
													<templateId root="2.16.840.1.113883.10.20.22.4.19"/>
													<id nullFlavor="NI"/>
													<code code="29308-4" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="ENCOUNTER DIAGNOSIS"/>
													<!-- <statusCode code="active"/> -->
													<effectiveTime><low value="'.str_replace("-","",$row['date_of_service']).'"/></effectiveTime>
													<entryRelationship typeCode="SUBJ" inversionInd="false">
													<!-- Problem Observation (V3) -->
													<observation classCode="OBS" moodCode="EVN">
													<templateId root="2.16.840.1.113883.10.20.22.4.4" extension="2015-08-01"/>
													<templateId root="2.16.840.1.113883.10.20.22.4.4"/>
													<id nullFlavor="NI"/>
													';
							$arrProbListType = $this->problem_type_srh(strtolower($problemList['prob_type']));
							if( isset($arrProbListType['code']) && $arrProbListType['code']!="" && isset($arrProbListType['display_name']) && $arrProbListType['display_name']!=""){
								$XML_encouter_entry .= '<code code="'.$arrProbListType['code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.$arrProbListType['display_name'].'">
															<translation code="75321-0" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Clinical Finding"/>
														</code>
								';
							}else{
								$XML_encouter_entry .= '<code nullFlavor="NI"/>
								';
							}
							/* BEGIN ENCOUNTER ENTRY */
							$XML_encouter_entry .= '<!-- Problem Observation template -->
							';
							$arrProbList = array();
							
							$XML_encouter_entry .= '<statusCode code="completed"/>
													<effectiveTime><low value="'.str_replace("-","",$row['date_of_service']).'"/></effectiveTime>
													';
													/* DYNAMIC SNOMED CT CODE FROM PROBLEM VALUE SET */
							if($problemList['ccda_code']!=""){						
								$XML_encouter_entry .= '<value xsi:type="CD" code="'.$problemList['ccda_code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.$problemList['problem_name'].'"/>';
							}else{
								$arrProblem = $this->getProblemCode($problemList['problem_name']);
								// DYNAMIC REACTION CODE //
								if(isset($arrProblem['ccda_code']) && $arrProblem['ccda_code']!="" && isset($arrProblem['ccda_display_name']) && $arrProblem['ccda_display_name']){					
									$XML_encouter_entry .= '<value xsi:type="CD"
																code="'.$arrProblem['ccda_code'].'"
																codeSystem="2.16.840.1.113883.6.96"
																codeSystemName="SNOMED CT"
																displayName="'.$arrProblem['ccda_display_name'].'"/>';
								}else{
									$XML_encouter_entry .= '<value xsi:type="CD" nullFlavor="NI"/>';
								} 
							}
							/* END ENCOUNTER ENTRY */							
							$XML_encouter_entry .= '</observation>';
							$XML_encouter_entry .= '</entryRelationship>';
							$XML_encouter_entry .= '</act>';
							/* END ENCOUNTER DIAGNOSIS ACT */
							$XML_encouter_entry .= '</entryRelationship>';
						
						/* END ENCOUNTER ACTIVITIES */
						$XML_encouter_entry .= '</encounter>';
						$XML_encouter_entry .= '</entry>';
					}
					
					/* END PROBLEM OBSERVATION */
					$XML_encouters_section .= $XML_encouter_entry;
					$XML_encouters_section .= '</section>';
					$XML_encouters_section .= '</component>';
				}
			/* Encounters End */
			
			/* Plan of Care */
				$XML_plan_of_care_section = '';
				$XML_plan_of_care_section .= '<component>';
				$XML_plan_of_care_section .= '<section>';
				$XML_plan_of_care_section .= '<!-- ** Plan of Care Section Template -->';
				$XML_plan_of_care_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.10"/>';
				$XML_plan_of_care_section .= '<!-- CCDA Plan of Care Section definition requires this code -->';
				$XML_plan_of_care_section .= '<code code="18776-5" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"
												displayName="Treatment plan"/>';
				$XML_plan_of_care_section .= '<title>PLAN OF CARE</title>';
				$XML_plan_of_care_section .= ' <text>';
				$XML_plan_of_care_section .= '<table border = "1" width = "100%">
										<thead>
											<tr>
												<th>Name</th>
												<th>Result</th>
												<th>Status</th>
												<th>Date / Reason</th>
											</tr>
										</thead>
										<tbody>';
				$flag = 0;						
				//----BEGIN GOALS/ INSTRUCTION ENTRY-----------------
				$sql_goal = "SELECT osacnd.inform ,od.name, od.snowmed, osacn.form_id, osacnd.order_set_associate_details_id 
							FROM order_set_associate_chart_notes_details osacnd 
							JOIN order_set_associate_chart_notes osacn ON osacnd.order_set_associate_id = osacn.order_set_associate_id 
							JOIN order_details od ON od.id = osacnd.order_id	
							WHERE osacn.form_id = '".$this->form_id."' AND patient_id = '".$this->patientId."'
									AND osacnd.delete_status = 0 AND osacn.delete_status = 0
							";
				$res_goal = $dbh->imw_query($sql_goal);				
				
				while($row_goal = $dbh->imw_fetch_assoc($res_goal)){
					$sql_cmt = "SELECT date_of_service FROM chart_master_table WHERE id = '".$row_goal['form_id']."'";
					$res_cmt = $dbh->imw_query($sql_cmt);
					$row_cmt = $dbh->imw_fetch_assoc($res_cmt);
					
					if($row_goal['name'] != ""){
					$flag = 1;	
					$XML_plan_of_care_section .= '<tr><td ID="goal_'.$row_goal['order_set_associate_details_id'].'">'.htmlentities($row_goal['name']).'</td>
													  <td>Goal</td>
													  <td></td>
													  <td>'.date('M d,Y',strtotime($row_cmt['date_of_service'])).'</td>
													</tr>';
					}
					
					if($row_goal['inform'] != ""){
					$flag = 1;	
					$XML_plan_of_care_section .= '<tr><td ID="instructions_'.$row_goal['order_set_associate_details_id'].'">Instruction : '.htmlentities($row_goal['inform']).'</td>
										<td>Instruction</td>
										<td>'.date('M d,Y',strtotime($row_cmt['date_of_service'])).'</td>
										</tr>';
					}
				}
				//----END GOALS/ INSTRUCTION ENTRY-----------------	
				
				//------- BEGIN FUTURE APPOINTMENTS AND TESTS-------
				$current_date = date("Y-m-d");
				$current_time = date("H:i:s");
				$sql = "SELECT date_of_service FROM chart_master_table WHERE id = '".$this->form_id."'";
				$res = $dbh->imw_query($sql);
				$row = $dbh->imw_fetch_assoc($res);
				$dos = $row['date_of_service'];
				
				$sql = "SELECT * FROM chart_schedule_test_external WHERE patient_id = '".$this->patientId."'";
				if($this->form_id != ""){
					$sql .= " AND schedule_date >= '".$this->dos."'";
				}else{
					$sql .= " AND schedule_date >= '".date('Y-m-d')."'";
				}
				$sql .= " AND deleted_by = '0'";
				$res = $dbh->imw_query($sql);
				
				while($row = $dbh->imw_fetch_assoc($res)){
					if($row['appoint_test'] == "Test"){
						$flag = 1;
						$XML_plan_of_care_section .= '<tr><td>'.htmlentities($row['test_type'])." : ".htmlentities($row['test_name']).'</td>
															<td>Future Sch Test</td>
															<td>';
						$XML_plan_of_care_section .=(preg_replace("/-/",'',$row['schedule_date'])>0)?date('M d,Y',strtotime($row['schedule_date'])):"";									
						$XML_plan_of_care_section .=" ".htmlentities($row['variation']).'</td>
															</tr>';
					}
					if($row['appoint_test'] == "Appointment"){
						$flag = 1;
						$XML_plan_of_care_section .= '<tr><td>'.htmlentities($row['reff_phy']).'</td>
															<td>Future Scheduled Appointment</td>
															<td>'.htmlentities($row['phy_address'])." ON ";
						$XML_plan_of_care_section .=(preg_replace("/-/",'',$row['schedule_date'])>0)?date('M d,Y',strtotime($row['schedule_date'])):"";									
						$XML_plan_of_care_section .=" ".htmlentities($row['variation']).'</td></tr>';
					}
					if($row['appoint_test'] == "Referral"){
						$flag = 1;
						$XML_plan_of_care_section .= '<tr><td>'.htmlentities($row['reff_phy']).'</td>
															<td>Referral to other providers</td>
															<td>'.htmlentities($row['phy_address'])." ON ";
						$XML_plan_of_care_section .=(preg_replace("/-/",'',$row['schedule_date'])>0)?date('M d,Y',strtotime($row['schedule_date'])):"";									
						$XML_plan_of_care_section .=" ".$row['variation']." FOR ".htmlentities($row['reason']).'</td></tr>';
					}
				}
				//------- END FUTURE APPOINTMENTS AND TESTS----------
				
				//-------BEGIN DIAGNOSTICS TESTS PENDING --------------//
				$qry = "SELECT * FROM rad_test_data WHERE rad_patient_id = '".$this->patientId."' AND rad_status != 3";									
				$res = $dbh->imw_query($qry);
				while($row = $dbh->imw_fetch_assoc($res)){
					$flag = 1;
					$status = ($row['rad_status'] == 1) ? 'Pending' : 'Completed';
					$rad_results=trim(addslashes($row['rad_results']));
					$XML_plan_of_care_section .= '<tr><td ID="RAD_Result_'.$row['rad_test_data_id'].'"> RAD : '.htmlentities($row['rad_name']).' [LOINC:'.$row['rad_loinc'].']</td>
														<td>'.$rad_results.'</td>
														<td>'.$status.'</td>
														<td>';
						$XML_plan_of_care_section .=(preg_replace("/-/",'',$row['rad_order_date'])>0)?date('M d,Y',strtotime($row['rad_order_date'])):"";									
						$XML_plan_of_care_section .='</td>
														</tr>';
				}
				
				$qry = "SELECT lor.*,lore.id as result_id FROM lab_test_data ltd 
						LEFT JOIN lab_observation_requested lor ON lor.lab_test_id = ltd.lab_test_data_id 
						LEFT JOIN lab_observation_result lore ON lore.lab_test_id = ltd.lab_test_data_id
						WHERE ltd.lab_patient_id = '".$this->patientId."' AND ltd.lab_status !=3
						";									
				$res1 = $dbh->imw_query($qry);
				while($row = $dbh->imw_fetch_assoc($res1)){
					if($row['result_id'] == "" || $row['result_id'] == NULL){	
					$flag = 1;
					$XML_plan_of_care_section .= '<tr><td>LAB: '.htmlentities($row['service']).' [LOINC:'.$row['loinc'].']</td>
													<td></td>
													<td>Diagnostic Test pending</td>
													<td></td>
													</tr>';
					}
				}
				//-------END DIAGNOSTICS TESTS PENDING --------------//
				
				//-------BEGIN RECOMMENDED PATIENT DECISION AIDS --------------//
				$sql = "SELECT dpr.name, doc.ccda_code 
						FROM document_patient_rel dpr 
						JOIN document doc ON dpr.doc_id = doc.id
						WHERE  dpr.p_id  = '".$this->patientId."' AND dpr.form_id = '".$this->form_id."'
						";
				$res = $dbh->imw_query($sql);
				while($row = $dbh->imw_fetch_assoc($res)){
					$flag = 1;
					$XML_plan_of_care_section .= '<tr><td>'.htmlentities($row['name']).' SNOMED CT :'.$row['ccda_code'].'</td><td>Recommended Patient Decision Aids</td><td></td></tr>';
				}
				//-------END RECOMMENDED PATIENT DECISION AIDS --------------//
				
				if($flag == 0)
				$XML_plan_of_care_section .= '<tr><td colspan="3"></td></tr>';
				$XML_plan_of_care_section .= '</tbody>';							
				$XML_plan_of_care_section .= '</table>';
				$XML_plan_of_care_section .= '</text>';
				
				//----BEGIN GOALS/ INSTRUCTION ENTRY-----------------
				$sql = "SELECT osacnd.inform ,od.name, od.snowmed, osacn.form_id, osacnd.order_set_associate_details_id 
						FROM order_set_associate_chart_notes_details osacnd 
						JOIN order_set_associate_chart_notes osacn ON osacnd.order_set_associate_id = osacn.order_set_associate_id 
						JOIN order_details od ON od.id = osacnd.order_id	
						WHERE osacn.form_id = '".$this->form_id."' AND patient_id = '".$this->patientId."'
						AND osacnd.delete_status = 0 AND osacn.delete_status = 0
						";
				$res = $dbh->imw_query($sql);
				while($row = $dbh->imw_fetch_assoc($res)){
					$sql_cmt = "SELECT date_of_service FROM chart_master_table WHERE id = '".$row['form_id']."'";
					$res_cmt = $dbh->imw_query($sql_cmt);
					$row_cmt = $dbh->imw_fetch_assoc($res_cmt);
					if($row['name'] != "" && $row['snowmed'] != ""){
					$XML_plan_of_care_entry ='<entry>
											<observation classCode = "OBS" moodCode = "GOL">
												<templateId root = "2.16.840.1.113883.10.20.22.4.44"/>
												<id nullFlavor="NI"/>
												<code code = "'.$row['snowmed'].'" codeSystem = "2.16.840.1.113883.6.96" displayName = "'.$row['name'].'"/>
												<statusCode code = "new"/>
												 <effectiveTime>
													<center value = "'.str_replace("-","",$row_cmt['date_of_service']).'"/>
												</effectiveTime>
											</observation>
										</entry>';
					$XML_plan_of_care_section .= $XML_plan_of_care_entry;	
					}
					if($row['inform'] != ""){
					$XML_plan_of_care_entry ='<entry>
												<act classCode = "ACT" moodCode = "INT">
													<templateId root = "2.16.840.1.113883.10.20.22.4.20"/>
													<code nullFlavor="NI"/>
													<text>
														<reference value = "#instructions_'.$row['order_set_associate_details_id'].'"/>
														'.htmlentities($row['inform']).'
													</text>
													<statusCode code = "completed"/>
												</act>
											</entry>';
					$XML_plan_of_care_section .= $XML_plan_of_care_entry;	
					}
				}
				//----END GOALS/ INSTRUCTION ENTRY-----------------	
				
				//-------BEGIN FUTURE APPOINTMENT ENTRY-------------
				$current_date = date("Y-m-d");
				$current_time = date("H:i:s");
				$sql = "SELECT * FROM chart_schedule_test_external WHERE patient_id = '".$this->patientId."'";
				if($this->form_id != ""){
					$sql .= " AND schedule_date >= '".$this->dos."'";
				}else{
					$sql .= " AND schedule_date >= '".date('Y-m-d')."'";
				}
				$sql .= " AND deleted_by = '0'";
				$res = $dbh->imw_query($sql);
				while($row = $dbh->imw_fetch_assoc($res)){
					
					switch($row['test_type']){
						case "Imaging":
							$ccda_code = $row['snomed'];
							$codeSystem = "2.16.840.1.113883.6.96";
							$codeSystemName = "SNOMED -CT";
						break; 
						
						case "Lab":
							$ccda_code = $row['loinc'];
							$codeSystem = "2.16.840.1.113883.6.1";
							$codeSystemName = "LOINC";
						break;
						
						case "Procedure":
							$ccda_code = $row['cpt'];
							$codeSystem = "2.16.840.1.113883.6.12";
							$codeSystemName = "CPT";
						break;
					}
					
					if($row['appoint_test'] == "Test"){
					$XML_plan_of_care_entry ='<entry typeCode="DRIV">
													<act moodCode = "RQO" classCode = "ACT">
													<templateId root = "2.16.840.1.113883.10.20.22.4.39"/>
													<id nullFlavor="NI"/>
													<code code = "'.$ccda_code.'" codeSystem = "'.$codeSystem.'" codeSystemName = "'.$codeSystemName.'" displayName = "'.$row['test_name'].'"/>
													<statusCode code = "new"/>
													<effectiveTime>
														<center value = "'.str_replace("-","",$row['schedule_date']).'"/>
													</effectiveTime>
													</act>
												</entry>';	
					}else if($row['user_type'] == "Appointment"){
						$XML_plan_of_care_entry ='<entry typeCode="DRIV">
													<act moodCode = "RQO" classCode = "ACT">
													<templateId root = "2.16.840.1.113883.10.20.22.4.39"/>
													<id nullFlavor="NI"/>
													<code nullFlavor="NI"/>
													<statusCode code = "new"/>
													<effectiveTime>
														<center value = "'.str_replace("-","",$row['schedule_date']).'"/>
													</effectiveTime>
													</act>
												</entry>';	
					}else if($row['user_type'] == "Referral"){
						$XML_plan_of_care_entry ='<entry typeCode="DRIV">
													<act moodCode = "RQO" classCode = "ACT">
													<templateId root = "2.16.840.1.113883.10.20.22.4.39"/>
													<id nullFlavor="NI"/>
													<code nullFlavor="NI"/>
													<statusCode code = "new"/>
													<effectiveTime>
														<center value = "'.str_replace("-","",$row['schedule_date']).'"/>
													</effectiveTime>
													</act>
												</entry>';	
					}
					$XML_plan_of_care_section .= $XML_plan_of_care_entry;					
				}
				//-------END FUTURE APPOINTMENT ENTRY-------------
				
				//-------BEGIN DIAGNOSTICS RAD TESTS PENDING --------------//
				$qry = "SELECT * FROM rad_test_data WHERE rad_patient_id = '".$this->patientId."' AND rad_status != 3";									
				$res = $dbh->imw_query($qry);
				while($row = $dbh->imw_fetch_assoc($res)){
					$XML_plan_of_care_entry ='<entry typeCode="DRIV">
														<observation classCode="OBS" moodCode="RQO">
														<templateId root="2.16.840.1.113883.10.20.22.4.44"/>
														<!-- Plan of Care Activity Observation template -->
														<id nullFlavor="NI"/>
														<code code="'.$row['rad_loinc'].'" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"
														displayName="'.$row['rad_name'].'"/>
														<text>
															<reference value = "#RAD_Result_'.$row['rad_test_data_id'].'"/>
															'.htmlentities($row['rad_results']).'
														</text>
														<statusCode code="new"/>
														<effectiveTime nullFlavor="NI"/>
														</observation>
													</entry>';
					$XML_plan_of_care_section .= $XML_plan_of_care_entry;									
				}
				//-------END DIAGNOSTICS RAD TESTS PENDING --------------//
				
				//-------BEGIN DIAGNOSTICS LAB TESTS PENDING --------------//
				$qry = "SELECT lor.*,lore.id as result_id FROM lab_test_data ltd 
						LEFT JOIN lab_observation_requested lor ON lor.lab_test_id = ltd.lab_test_data_id 
						LEFT JOIN lab_observation_result lore ON lore.lab_test_id = ltd.lab_test_data_id
						WHERE ltd.lab_patient_id = '".$this->patientId."' AND ltd.lab_status !=3
						";									
				$res = $dbh->imw_query($qry);
				while($row = $dbh->imw_fetch_assoc($res)){
					if($row['result_id'] == "" || $row['result_id'] == NULL){
					$XML_plan_of_care_entry ='<entry typeCode="DRIV">
														<observation classCode="OBS" moodCode="RQO">
														<templateId root="2.16.840.1.113883.10.20.22.4.44"/>
														<!-- Plan of Care Activity Observation template -->
														<id nullFlavor="NI"/>
														<code code="'.$row['loinc'].'" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"
														displayName="'.$row['service'].'"/>
														<statusCode code="new"/>
														<effectiveTime nullFlavor="NI"/>
														</observation>
													</entry>';
					$XML_plan_of_care_section .= $XML_plan_of_care_entry;	
					}
				}
				//-------END DIAGNOSTICS RAD TESTS PENDING --------------//
				
				//----BEGIN RECOMMENDED PATIENT DECISION AIDS-----------//
				$sql = "SELECT dpr.name, doc.ccda_code 
						FROM document_patient_rel dpr 
						JOIN document doc ON dpr.doc_id = doc.id
						WHERE  dpr.p_id  = '".$this->patientId."' AND dpr.form_id = '".$this->form_id."'
						";
				$res = $dbh->imw_query($sql);
				while($row = $dbh->imw_fetch_assoc($res)){
				$XML_plan_of_care_entry ='<entry typeCode="DRIV">
											<supply moodCode="INT" classCode="SPLY">
											<templateId root="2.16.840.1.113883.10.20.22.4.43"/>
											<!-- ** Plan of Care Activity Supply ** -->
											<id nullFlavor="NI"/>
											<code xsi:type="CE" code="'.$row['ccda_code'].'" codeSystem="2.16.840.1.113883.6.96"
												displayName="'.$row['name'].'"/>
											</supply>
										</entry>';
				}
				$XML_plan_of_care_section .= $XML_plan_of_care_entry;										
				//----END RECOMMENDED PATIENT DECISION AIDS-----------//
				
				$XML_plan_of_care_section .= '</section>';
				$XML_plan_of_care_section .= '</component>';
				
			/* Plan of Care End */
			
			/* Instructions */
				$sql = "SELECT osacnd.inform ,od.name, od.snowmed, osacn.form_id, osacnd.order_set_associate_details_id 
							FROM order_set_associate_chart_notes_details osacnd 
							JOIN order_set_associate_chart_notes osacn ON osacnd.order_set_associate_id = osacn.order_set_associate_id 
							JOIN order_details od ON od.id = osacnd.order_id	
							WHERE osacn.form_id = '".$this->form_id."' AND patient_id = '".$this->patientId."'
							AND osacnd.delete_status = 0 AND osacn.delete_status = 0
							";
				$res = $dbh->imw_query($sql);		
				$XML_instructions_section = '<component>';
				$XML_instructions_section .= '<section>';
				$XML_instructions_section .= '<!-- Instructions template ID -->';
				$XML_instructions_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.45"/>';
				$XML_instructions_section .= '<id nullFlavor="NI"/>';
				$XML_instructions_section .= '<code code="69730-0" codeSystem="2.16.840.1.113883.6.1" codeSystemVersion="LOINC"
								  displayName="Instructions"/>';
				$XML_instructions_section .= '<title>INSTRUCTIONS</title>';
				$XML_instructions_section .= '<text>';
				while($row = $dbh->imw_fetch_assoc($res)){
					if($row['inform'] != ""){
					$XML_instructions_section .= '<paragraph>'.htmlentities($row['inform']).'</paragraph>';
					}
				}
				
				$sql = "SELECT * 
						FROM document_patient_rel
						WHERE p_id = '".$this->patientId."'
						";
				$res = $dbh->imw_query($sql);
				while($row = $dbh->imw_fetch_assoc($res)){
					if($row['name'] != ""){
					$XML_instructions_section .= '<paragraph>'.htmlentities($row['name']).'</paragraph>';
					}
				}	
				$XML_instructions_section .= '</text>';
				
				$XML_instructions_section .= '</section>';
				$XML_instructions_section .= '</component>';
			/* Instructions End */
			
			/* Functional Status */
				$sql = "SELECT neuroPsych, func_status FROM chart_left_cc_history WHERE patient_id = '".$this->patientId."' AND form_id = '".$this->form_id."'";
				$res = imw_query($sql);
				$XML_functional_status_section = '<component>';
				$XML_functional_status_section .= '<section>';
				$XML_functional_status_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.14"/>';
				$XML_functional_status_section .= '<!--  ******** Functional status section template   ******** -->';
				$XML_functional_status_section .= '<code code="47420-5" codeSystem="2.16.840.1.113883.6.1"/>';
				$XML_functional_status_section .= '<title>FUNCTIONAL AND CONGNITIVE STATUS</title>';
				$XML_functional_status_section .= ' <text> ';
				if($dbh->imw_num_rows($res)>0){	
					$XML_functional_status_section .= '<table border = "1" width = "100%">
									<thead>
										<tr>
											<th>Functional Status</th>
											<th>Congnitive Status</th>
										</tr>
									</thead>
									<tbody>';
					$row = $dbh->imw_fetch_assoc($res);	
					$XML_functional_status_section .= '<tr>';			
					$arrFuncStatus = $this->get_functional_status($row['func_status']);
					$XML_functional_status_section .= '<td >'.$arrFuncStatus['display_name'].'</td>';
					$XML_functional_status_section .= '<td >'.htmlentities($row['neuroPsych']).'</td>';
					$XML_functional_status_section .= '</tr>';
					$XML_functional_status_section .= '</tbody> </table>';
				}						
				$XML_functional_status_section .= '</text>';
				$XML_functional_status_section .= ' <entry typeCode="DRIV">
													<templateId root="2.16.840.1.113883.10.20.22.4.74"/>
													<!-- **** Cognitive Status Result Observation template **** -->';
				$XML_functional_status_section .= '<organizer classCode="CLUSTER" moodCode="EVN">
													<templateId root="2.16.840.1.113883.10.20.22.4.66"/>
													<!-- Cognitive Status Result Organizer template -->
													<id nullFlavor="NI"/>';
				$arrCongStatus = $this->get_cognitive_status($row['neuroPsych']);
				if(isset($arrCongStatus['code']) && $arrCongStatus['code']!="" && isset($arrCongStatus['display_name']) && $arrCongStatus['display_name']!=""){									
				$XML_functional_status_section .= '<code code="'.$arrCongStatus['code'].'" displayName="'.$arrCongStatus['display_name'].'"
													codeSystem="2.16.840.1.113883.6.96"
													codeSystemName="SNOMED CT"/>';
				}
				else 
				$XML_functional_status_section .= '<code nullFlavor="NI"/>';
				$XML_functional_status_section .= '<statusCode code="completed"/>
													<component>
													<observation classCode="OBS" moodCode="EVN">
													<!-- Functional Status Result observation(such as toileting) -->
													<templateId root="2.16.840.1.113883.10.20.22.4.67"/>
													<id nullFlavor="NI"/>';
				$arrFuncStatus = $this->get_functional_status($row['func_status']);
				if($arrFuncStatus['code']!="" && $arrFuncStatus['display_name']!=""){
				$XML_functional_status_section .= '<code code="'.$arrFuncStatus['code'].'"
													displayName="'.$arrFuncStatus['display_name'].'"
													codeSystem="2.16.840.1.113883.6.96"
													codeSystemName="SNOMED CT"/>';
				}
				else 
				$XML_functional_status_section .= '<code nullFlavor="NI"/>';									
				$XML_functional_status_section .= '<statusCode code="completed"/>
													<effectiveTime nullFlavor="NI"/>
													<value xsi:type = "CD" nullFlavor="NI"/>
													</observation>
													</component>
													</organizer>';									
				$XML_functional_status_section .= '</entry>';
				$XML_functional_status_section .= '</section>';
				$XML_functional_status_section .= '</component>';
				
			/* Functional Status End */
			
			/* Procedures */
				$flag = 0;
				$XML_procedures_section = '<component>
											<section>
												<!-- Procedures section template -->
												<templateId root="2.16.840.1.113883.10.20.22.2.7.1" extension="2014-06-09"/>
												<templateId root="2.16.840.1.113883.10.20.22.2.7.1"/>
												<code code="47519-4" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="HISTORY OF PROCEDURES"/>
												<title>PROCEDURES</title>
												<text>
												<table border = "1" width = "100%">
													<thead>
														<tr>
															<th>Name</th>
															
															<th>Date</th>
															<th>Provider</th>
														</tr>
													</thead>
													 <tbody>
													 ';
				
				$sql_sx = "SELECT * FROM lists WHERE type IN (5,6) AND allergy_status = 'Active' AND pid = '".$this->patientId."'";
				$res_sx = $dbh->imw_query($sql_sx);
				$procudure_sx_id = '';
				while($row_sx = $dbh->imw_fetch_assoc($res_sx)){
					$flag = 1;
					$procudure_sx_id = 'procedure_sx_'.$row_sx['id'];
					$XML_procedures_section .= '<tr>
												<td ID = "'.$procudure_sx_id.'">'.htmlentities($row_sx['title']).'</td>
												<td ID = "date_sx_'.$row_sx['id'].'">';
					$XML_procedures_section .=(preg_replace("/-/",'',$row_sx['begdate'])>0)?date('M d,Y',strtotime($row_sx['begdate'])):"";
					$XML_procedures_section .='</td>
												<td >'.$row_sx['referredby'].'</td>
												</tr>';
				}
				
				if($flag == 0){
					$XML_procedures_section .= '<tr><td>No Procedures.</td></tr>';	
				}
				$XML_procedures_section .= '		</tbody>';
				$XML_procedures_section .= '		</table>';
				$XML_procedures_section .= '		</text>';
				

				$res_sx = $dbh->imw_query($sql_sx);
				while($row_sx = $dbh->imw_fetch_assoc($res_sx)){
					$flag = 1;
				$XML_procedures_entry = '<entry typeCode="DRIV">
											<procedure classCode="PROC" moodCode="EVN">
											<!-- Procedure  Activity Procedure Template -->
											<templateId root="2.16.840.1.113883.10.20.22.4.14" extension="2014-06-09"/>
											<templateId root="2.16.840.1.113883.10.20.22.4.14"/>
											<id nullFlavor="NI"/>
											<code code="'.$row_sx['ccda_code'].'" codeSystem="2.16.840.1.113883.6.96"	displayName="'.$row_sx['title'].'" codeSystemName="SNOMED CT"></code>
											<statusCode code="completed"/>
											';
				if($row_sx['begdate'] !="" && preg_replace("/-/","",$row_sx['begdate'])>0){
					$XML_procedures_entry .= '		<effectiveTime value="'.preg_replace("/-/","",$row_sx['begdate']).'"/>
					';
				}else
					$XML_procedures_entry .= '		<effectiveTime nullFlavor="NI"/>
				';
				
				$qry_provider = "SELECT * FROM refferphysician WHERE physician_Reffer_id = '".$row_sx['referredby_id']."'";  // PRIMARY PHYSICIAN
				$res_provider = $dbh->imw_query($qry_provider);
				if($dbh->imw_num_rows($res_provider) > 0){
					$row_provider = $dbh->imw_fetch_assoc($res_provider);
					$XML_procedures_entry .= '		<performer>
														<assignedEntity>
													<!-- NPI 34567 -->
													';
					if($row_provider['NPI']!=""){
						$XML_procedures_entry .= '<id extension="'.$row_provider['NPI'].'" root="2.16.840.1.113883.4.6"/>
						';
					}else{
						$XML_procedures_entry .= '				<id nullFlavor="NI"/>
						';
					}
					$XML_procedures_entry .= '<addr>
					';
					if($row_provider['Address1'] != "")
						$XML_procedures_entry .= '					<streetAddressLine>'.$row_provider['Address1'].'</streetAddressLine>
						';
					if($row_provider['City'] != "")
						$XML_procedures_entry .= '					<city>'.$row_provider['City'].'</city>
						';
					if($row_provider['State'] != "")
						$XML_procedures_entry .= '					<state>'.$row_provider['State'].'</state>
						';
					if($row_provider['ZipCode'] != "")
						$XML_procedures_entry .= '					<postalCode>'.$row_provider['ZipCode'].'</postalCode>
						';
					
					$XML_procedures_entry .= '					<country>US</country>';
					$XML_procedures_entry .= '				</addr>';
				
					if($row_provider['physician_phone'] != ""){
						$XML_procedures_entry .= '	<telecom use="WP" value="tel:+1-'.$this->core_phone_format($row_provider['physician_phone']).'"/>
						';
					}else{
						$XML_procedures_entry .= '	<telecom nullFlavor="NI"/>
						';
					}
					$XML_procedures_entry .= '				<assignedPerson>';
					$XML_procedures_entry .= '					<name>';
					$XML_procedures_entry .= '						<given>'.$row_provider['FirstName'].'</given>';
					$XML_procedures_entry .= '						<family>'.$row_provider['LastName'].'</family>';
					$XML_procedures_entry .= '					</name>';
					$XML_procedures_entry .= '				</assignedPerson>';
					$XML_procedures_entry .= '			</assignedEntity>';
					$XML_procedures_entry .= '		</performer>';
				}
				/********UDI data new block ************/
				$udi_for_this_proc = $this->getUDIprocWise($row_sx['id']);
				if($udi_for_this_proc && count($udi_for_this_proc) > 0 && is_array($udi_for_this_proc)){
					foreach($udi_for_this_proc as $UDI_rs){
						$XML_procedures_entry .= '
						<participant typeCode="DEV">
							<participantRole classCode="MANU">
								<!-- ** Product instance ** -->
								<templateId root="2.16.840.1.113883.10.20.22.4.37"/>
								<id assigningAuthorityName="FDA" extension="'.$UDI_rs['title'].'" root="2.16.840.1.113883.3.3719"/>
								<playingDevice>
									<!-- the actual UDI device -db -->
									<code nullFlavor="UNK">
										<originalText>
											<reference value="#'.$procudure_sx_id.'"/>
										</originalText>
								   </code>
								</playingDevice>
								<!-- FDA Scoping Entity OID for UDI-db -->
								<scopingEntity>
									<id root="2.16.840.1.113883.3.3719"/>
								</scopingEntity>
							</participantRole>
						</participant>
						';
					}			
				}
				
				/********END OF UDI block **************/
					$XML_procedures_entry .= '	</procedure>';
					$XML_procedures_entry .= '	</entry>';
					$XML_procedures_section .= $XML_procedures_entry;
				}
				
				if($flag == 0){
				$XML_procedures_entry = '	<entry>';
				$XML_procedures_entry .= '	<procedure classCode="PROC" moodCode="EVN">';
				$XML_procedures_entry .= '		<!-- Procedure  Activity Procedure Template -->';
				$XML_procedures_entry .= '		<templateId root="2.16.840.1.113883.10.20.22.4.14"/>';
				$XML_procedures_entry .= '		<id nullFlavor="NI"/>';
				$XML_procedures_entry .= '		<code nullFlavor="NI"/>';
				$XML_procedures_entry .= '		<statusCode code="completed"/>';
				$XML_procedures_entry .= '		<effectiveTime nullFlavor="NI"/>';
				$XML_procedures_entry .= '	</procedure>';
				$XML_procedures_entry .= '	</entry>';
				$XML_procedures_section .= $XML_procedures_entry;
				}
				
				$XML_procedures_section .= '</section>';
				$XML_procedures_section .= '</component>';
			/* Procedures End */
			
			/* UDI */
				$row_sx = array();
				$sql_sx = $dbh->imw_query("SELECT * FROM lists WHERE type IN (5,6) AND allergy_status = 'Active' AND pid = '".$this->patientId."'");
				if($sql_sx && $dbh->imw_num_rows($sql_sx) > 0){
					while($row = $dbh->imw_fetch_assoc($sql_sx)){
						$row_sx[] = $row;
					}
				}
				$XML_udi_section = '<component>
										<section nullFlavor="NI">
											<!-- Procedures section template -->
											<templateId root="2.16.840.1.113883.10.20.22.2.7.1" extension="2014-06-09"/>
											<templateId root="2.16.840.1.113883.10.20.22.2.7.1"/>
											<code code="47519-4" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="IMPLANTABLE DEVICES"/>
											<title>Implantable Devices</title>
											<text>
											<table border = "1" width = "100%">
												<thead>
													<tr>
														<th>UDI</th>
														<th>Assigning Authority</th>
													</tr>
												</thead>
												 <tbody>
												 ';
												 
												 if(count($row_sx) > 0 && is_array($row_sx)){
													foreach($row_sx as $row){
														$procedure_id = $row['id'];
														$udi_details = $this->getUDIprocWise($row['id']);
														
														foreach($udi_details as $obj){
															$XML_udi_section .= '
															<tr>
																<td>'.$obj['title'].'</td>
																<td>'.$obj['assigning_authority_UDI'].'</td>
															</tr>';
														}
													}
												 }else{
													$XML_udi_section .= '<tr><td colspan="2">No UDI Information</td></tr>'; 
												 }
												 
												
				$XML_udi_section .= '				</tbody>
												</table>
											</text>
										</section>
									</component>';
			/* UDI End */
			
			/* Chief Complaint */
				$sql = "SELECT * FROM chart_left_cc_history WHERE patient_id = '".$this->patientId."' AND form_id = '".$this->form_id."' ";
				$row = $dbh->imw_fetch_assoc($dbh->imw_query($sql));
				$XML_chief_complaint_section = '<component>
						<section>
							<templateId root = "2.16.840.1.113883.10.20.22.2.13"/>
							<code code = "46239-0" codeSystem = "2.16.840.1.113883.6.1" codeSystemName = "LOINC" displayName = "CHIEF COMPLAINT AND REASON FOR VISIT"/>
							<title>CHIEF COMPLAINT</title>
							<text>';
			if($row['ccompliant'] != ""){				
			   $XML_chief_complaint_section .= '<table border = "1" width = "100%">
									<thead>
										<tr>
											<th>Reason for Visit/Chief Complaint</th>
										</tr>
									</thead>';
								
				$XML_chief_complaint_section .= '<tbody>
										<tr>
											<td>'.htmlentities($row['ccompliant']).'</td>
										</tr>
									</tbody>';
				
				 $XML_chief_complaint_section .= '</table>';
				 }
				$XML_chief_complaint_section .= '</text>
						</section>
					</component>';
			/* Chief Complaint End */
			
			/* Reason for Referral */
				$sql = "SELECT date_of_service FROM chart_master_table WHERE id = '".$this->form_id."'";
				$res = $dbh->imw_query($sql);
				$row = $dbh->imw_fetch_assoc($res);
				$dos = $row['date_of_service'];
				
				$sql = "SELECT * FROM chart_assessment_plans WHERE patient_id = '".$this->patientId."' Order By form_id DESC Limit 0, 1";
				if($this->form_id != ""){
					$sql .= " AND schedule_date >= '".$this->dos."'";
				}else{
					$sql .= " AND schedule_date >= '".date('Y-m-d')."'";
				}
				$sql .= " AND deleted_by = '0'";
				$sql .= " ORDER BY id DESC LIMIT 0,1";
				$res = $dbh->imw_query($sql);
				
				if($res && $dbh->imw_num_rows($res) >0 ){
					$row = $dbh->imw_fetch_assoc($res);
				}
				else
					$row['reason'] = '';
				$XML_reason_for_referral = '<component>
											<section>
												<templateId root="1.3.6.1.4.1.19376.1.5.3.1.3.1"/>
												<!-- ** Reason for Referral Section Template ** -->
												<code codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" code="42349-1"
													displayName="REASON FOR REFERRAL"/>
												<title>REASON FOR REFERRAL</title>
												<text>
													<paragraph>'.htmlentities($row['consult_reason']).'</paragraph>
												</text>
											</section>
										</component>';
			/* Reason for Referral End */
			
			/* XML Body */
				$XML_cda_body = '<component>';
				$XML_cda_body .= '<structuredBody>';
					
				$XML_cda_body .= '<!-- SOCIAL HISTORY SECTION -->';
				$XML_cda_body .= $XML_social_history_section;		   // INCLUDES SMOKING STATUS
				
				$XML_cda_body .= '<!-- MEDICATIONS SECTION -->';
				$XML_cda_body .= $XML_medication_section;
				
				$XML_cda_body .= '<!-- ALLERGIES SECTION -->';
				$XML_cda_body .= $XML_allergies_section;
				
				$XML_cda_body .= '<!-- IMMUNIZATION SECTION -->';
				$XML_cda_body .= $XML_immunization_section;
				
				$XML_cda_body .= '<!-- VITAL SIGN SECTION -->';
				$XML_cda_body .= $XML_vital_section;
				
				$XML_cda_body .= '<!-- PROBLEM SECTION -->';
				$XML_cda_body .= $XML_problem_section;

				$XML_cda_body .= '<!-- LAB TESTS SECTION -->';
				$XML_cda_body .= $XML_results_section;				   // INCLUDES LAB RESULTS
			
			
				$XML_cda_body .= '<!-- ASSESSMENT SECTION -->';	
				$XML_cda_body .= $XML_assessment_section;
				
				if(isset($XML_encouters_section) && empty($XML_encouters_section) == false){	
					$XML_cda_body .= '<!-- ENCOUNTERS SECTION -->';
					$XML_cda_body .= $XML_encouters_section;				// INCLUDES PROBLEMS
				}
				
				$XML_cda_body .= '<!-- PLAN OF CARE SECTION -->';
				$XML_cda_body .= $XML_plan_of_care_section;	
				
				$XML_cda_body .= '<!-- INSTRICTIONS SECTION -->';      // INCLUDED STATIC
				$XML_cda_body .= $XML_instructions_section;
				
				$XML_cda_body .= '<!-- FUNCTIONAL STATUS SECTION -->';  // INCLUDED STATIC
				$XML_cda_body .= $XML_functional_status_section;
				
				$XML_cda_body .= '<!-- CHIEF COMPLAINT AND REASON FOR VISIT SECTION -->';  // INCLUDED STATIC
				$XML_cda_body .= $XML_chief_complaint_section;
				
				//$XML_cda_body .= '<!-- MEDICATIONS ADMINISTERED SECTION -->';  // INCLUDED STATIC
				//$XML_cda_body .= $XML_medication_admin_section;
				
				$XML_cda_body .= '<!-- PROCEDURES SECTION -->';   // INCLUDED STATIC
				$XML_cda_body .= $XML_procedures_section;
				
				$XML_cda_body .= '<!-- REASON FOR REFERRAL SECTION -->';   // INCLUDED STATIC
				$XML_cda_body .= $XML_reason_for_referral;
				
				$XML_cda_body .= '<!-- GOALS SECTION -->';   // DYNAMIC PATIENT GOALS
				$XML_cda_body .= $XML_goals_section;
				
				
				$XML_cda_body .= '<!-- UDI SECTION -->';   // DYNAMIC PATIENT GOALS
				$XML_cda_body .= $XML_udi_section;
				
				$XML_cda_body .= '<!-- Health Concern Section -->';   // DYNAMIC PATIENT GOALS
				$XML_cda_body .= $finalHealthConcern;
				
				$XML_cda_body .= '</structuredBody>';
				$XML_cda_body .= '</component>';
			/* XML Body End */
			
			$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<?xml-stylesheet type="text/xsl" href="'.$GLOBALS['php_server'].'/interface/reports/ccd/CDA_IMW.xsl'.'"?>
				<!--Title: Continuity of Care Document (CCD).-->
				<ClinicalDocument xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
					xmlns="urn:hl7-org:v3" 
					xmlns:voc="urn:hl7-org:v3/voc" 
					xmlns:sdtc="urn:hl7-org:sdtc">
				  <realmCode code="US"/>
				  <typeId extension="POCD_HD000040" root="2.16.840.1.113883.1.3"/>
				  <!-- indicates conformance with US Realm Clinical Document Header template -->
				  <templateId root="2.16.840.1.113883.10.20.22.1.1" extension="2015-08-01"/>
				  <templateId root="2.16.840.1.113883.10.20.22.1.1"/>
				  <!-- conforms to CCD Template ID requirements -->
				  <templateId root="2.16.840.1.113883.10.20.22.1.2" extension="2015-08-01"/>
				  <templateId root="2.16.840.1.113883.10.20.22.1.2"/>
				  <!-- UNIQUE DOCUMENT IDENTIFIER -->
				  <id extension="Test CCDA" root="1.1.1.1.1.1.1.1.1"/>
				  <code code="34133-9" displayName="Summarization of patient data" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"/>
				  <title>HEALTH HISTORY &amp; PHYSICAL</title>
				  <effectiveTime value="'.$this->currentDate.'"/>
				  <confidentialityCode code="N" codeSystem="2.16.840.1.113883.5.25"/>
				  <languageCode code="en-US"/>';
				  
			$xml .= $XMLpatient_data;
			$xml .= $XML_author_data;
			$xml .= $XML_data_enterer_data;	  
			$xml .= $XML_custodian_data;
			$xml .= $XML_documentationof_data; // CARE TEAM MEMBERS
			$xml .= $XML_referral_to_providers;
			$xml .= $XML_cda_body;
			$xml .= '</ClinicalDocument>';	  
			if(empty($xml) == false){
				$file_path = $GLOBALS['fileroot'].'';
				$saveFile = new SaveFile($this->patientId);
				$file_name = "tmp/imedic-api-".$this->patientId.".xml";
				$file_pointer = $saveFile->cr_file($file_name,$xml);
				$return_data['xml'] = $file_pointer;
			}
		return $return_data;
	}
	
	public function convertToInt($val){
		return (int)trim($val);
	}
	
	function core_phone_format($phone_number,$format=''){
		$return = "";
		$default_format = $format!=''? $format : "###-###-####";
		$refined_phone = $default_format == '' ? $phone_number : preg_replace('/[^0-9]/','',$phone_number);
		

		switch($default_format){
			case "###-###-####"://-------1
				$return = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $refined_phone);
				break;
			case "(###) ###-####"://-------2
				$return = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $refined_phone);
				break;
			case "(##) ###-####"://-------3
				$return = preg_replace("/([0-9]{2})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $refined_phone);
				break;	
			case "(###) ###-###"://-------4
				$return = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{3})/", "($1) $2-$3", $refined_phone);
				break;	
			case "(####) ######"://-------5
				$return = preg_replace("/([0-9]{4})([0-9]{6})/", "($1) $2", $refined_phone);
				break;
			case "(####) #####"://-------6
				$return = preg_replace("/([0-9]{4})([0-9]{5})/", "($1) $2", $refined_phone);
				break;	
			case "(#####) #####"://-------7
				$return = preg_replace("/([0-9]{5})([0-9]{5})/", "($1) $2", $refined_phone);
				break;	
			case "(#####) ####"://-------8
				$return = preg_replace("/([0-9]{5})([0-9]{4})/", "($1) $2", $refined_phone);
				break;		
			default:
				$return = $refined_phone;
				break;
		}
		return $return;
	}
	
	public function gender_srh($val,$map="code_to_imw"){
		$val = trim($val);
		$arrGender = array(
							array("imw"=>'male',"code"=>"M","display_name"=> "Male"),
							array("imw"=>'female',"code"=>"F","display_name"=> "Female"),
							array("imw"=>'unknown',"code"=>"UNK","display_name"=> "Unknown")
						  );
		$arr = array();
		if($val != ""){
			foreach($arrGender as $row){
				if(in_array($val, $row)){
					if($map == "code_to_imw"){
						$arr['code'] = $row['code'];
						$arr['display_name'] = $row['display_name'];
					}else{
						$arr['imw'] = $row['imw'];
					}
					break;
				}else{
					if($map == "code_to_imw"){
					$arr['code'] = "UN";
					$arr['display_name'] = "Undifferentiated";
					}else{
						$arr['imw'] = "";
					}
				}
			}
		}
		return $arr;
	}
	
	public function marr_status_srh($val){
		$val = trim($val);
		$arrMartitalStatus = array(
							array("imw"=>'married',"code"=>"M","display_name"=> "Married"),
							array("imw"=>'single',"code"=>"S","display_name"=> "Never Married"),								  
							array("imw"=>'divorced',"code"=>"D","display_name"=> "Divorced"),
							array("imw"=>'widowed,widow',"code"=>"W","display_name"=> "Widowed"),
							array("imw"=>'separated',"code"=>"L","display_name"=> "Legally Separated"),
							array("imw"=>'domestic partner',"code"=>"T","display_name"=> "Domestic Partner")
						  );
		$arr = array();
		if($val != ""){
			foreach($arrMartitalStatus as $row){
				$arr = explode(',',$row['imw']);
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}
			}
		}
		return $arr;
	}
	
	function get_race_heirarcy($race,$race_code=''){
		$RACE_DATA_AR = array();
		if(!empty($race_code)){
			$q = "SELECT race_name,cdc_code,parent_id,h_code FROM `race` WHERE is_deleted = '0' AND cdc_code LIKE '$race_code' LIMIT 1";
		}else if(!empty($race)){
			$q = "SELECT race_name,cdc_code,parent_id,h_code FROM `race` WHERE is_deleted = '0' AND race_name LIKE '$race' LIMIT 1";
		}
		if($q != ''){
			$res = $this->dbh_obj->imw_query($q);
			$rs = $this->dbh_obj->imw_fetch_assoc($res);
			$h_code		= $rs['h_code'];
			$RACE_DATA_AR[] = $rs;
			if($h_code!=''){
				$arr_h_code = explode('.',$h_code);
				if(count($arr_h_code)>1){// Its a child node, lookup for parent.
					for($i = 0; $i < count($arr_h_code); $i++){
						$removed_h_code = array_pop($arr_h_code);
						$remaining_h_code = implode('.',$arr_h_code);
						$q2 = "SELECT race_name,cdc_code,parent_id,h_code FROM race WHERE h_code LIKE '$remaining_h_code' LIMIT 1";
						$res2 = $this->dbh_obj->imw_query($q2);
						if($res2 && $this->dbh_obj->imw_num_rows($res2)>0){
							$rs2 = $this->dbh_obj->imw_fetch_assoc($res2);
							array_unshift($RACE_DATA_AR,$rs2);
						}
					}
				}
			}
		}
		return $RACE_DATA_AR;
	}
	
	public function ethnicity_srh($val){
		$val = str_replace(',','',$val);
		$val = trim($val);
		$arrRace = $arrTemp = array();
		$q = "SELECT ethnicity_name,cdc_code FROM `ethnicity` WHERE is_deleted = '0'";
		$res = $this->dbh_obj->imw_query($q);
		while($rs = $this->dbh_obj->imw_fetch_assoc($res)){
			$arrTemp['imw'] 			= strtolower($rs['ethnicity_name']);
			$arrTemp['code'] 			= $rs['cdc_code'];
			$arrTemp['display_name'] 	= $rs['ethnicity_name'];
			$arrRace[] = $arrTemp;
		}
		
		$arr = array();
		if($val != ""){
			foreach($arrRace as $row){
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}else{
					$arr['code'] = "2186-5";
					$arr['display_name'] = "Not Hispanic or Latino";
				}
			}
		}
		return $arr;
	}
	
	public function language_srh($val){
		$val = trim($val);
		
		$arrLang = array();
		$q = "SELECT lang_name,iso_639_1_code,iso_639_2_B_code FROM `languages` WHERE is_deleted = '0' AND lang_name LIKE '$val' LIMIT 1";
		$res = $this->dbh_obj->imw_query($q);
		while($rs = $this->dbh_obj->imw_fetch_assoc($res)){
			$arrLang['imw'] 			= strtolower($rs['lang_name']);
			$lcode = trim($rs['iso_639_1_code']);
			if($lcode==''){
				$lcode = trim($rs['iso_639_2_B_code']);
			}
			$arrLang['code'] 			= strtolower($lcode);
			$arrLang['display_name'] 	= $rs['lang_name'];
		}
		return $arrLang;
	}
	
	public function smoking_status_srh($val){
		$val = trim($val);
		$arrSmoking = array(
							array("imw"=>'current every day smoker',"code"=>"449868002","display_name"=> "Current every day smoker"),
							array("imw"=>'current some day smoker',"code"=>"428041000124106","display_name"=> "Current some day smoker"),
							array("imw"=>'former smoker',"code"=>"8517006","display_name"=> "Former smoker"),
							array("imw"=>'never smoked',"code"=>"266919005","display_name"=> "Never smoker"),
							array("imw"=>'smoker, current status unknown',"code"=>"77176002","display_name"=> "Smoker, current status unknown"),
							array("imw"=>'unknown if ever smoked',"code"=>"266927001","display_name"=> "Unknown if ever smoked"),
							array("imw"=>'heavy tobacco smoke',"code"=>"428071000124103","display_name"=> "Heavy tobacco smoker"),
							array("imw"=>'light tobacco smoker',"code"=>"428061000124105","display_name"=> "Light tobacco smoker")
						  );
		$arr = array();
			foreach($arrSmoking as $row){
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}else{
					$arr['code'] = "266927001";
					$arr['display_name'] = "Unknown if ever smoked";
				}
			}
		return $arr;
	}
	
	public function get_medical_data($form_id='', $arrType, $pid, $start_date = false, $end_date = false){
		$strType = implode(',',$arrType);
		$dataFinal = array();
		
		$date_filter = '';
		if($start_date){
			$date_filter .= 'begdate >= \''.$start_date.'\''; 
		}
		
		if($end_date){
			if(empty($date_filter) == false){
				$date_filter .= ' AND ';
			}
			$date_filter .= 'begdate <= \''.$end_date.'\''; 
		}
		
		if(empty($date_filter) == false){
			$date_filter .= ' AND ';
		}
		
		$sql_list = $sql_arc = '';
		if(isset($form_id) && $form_id != ''){
			$sql_arc  = "select lists 
					from  
					chart_genhealth_archive 
					where patient_id='".$pid."' and
					form_id = '".$form_id."'";
		}else{
			$sql_list  = "select * ,
							date_format(begdate,'%m/%d/%y') as DateStart from lists where ".$date_filter." pid='".$pid."' and
							allergy_status = 'Active' and type in($strType) order by id";
		}
		if(empty($sql_list) == false){
			$res_list = $this->dbh_obj->imw_query($sql_list);	
			while($row_list = $this->dbh_obj->imw_fetch_assoc($res_list))	{
				$dataFinal[] = $row_list;
			}
		}
		if($sql_arc != ""){
			$res_arc = $this->dbh_obj->imw_query($sql_arc);
	
			$dataFinal = array();
			while($row_arc = $this->dbh_obj->imw_fetch_assoc($res_arc)){
				$arrList = unserialize($row_arc['lists']);
				foreach($arrList as $arrData){
					foreach($arrData as $data){
						if(in_array($data['type'],$arrType)){
							if($data['allergy_status'] == 'Active'){
								$dataFinal[] = $data;
							}
						}
					}
				}
			}
		}
		return $dataFinal;
	}
	
	
	function getRXNormCode($str){
		$arr = array();
		$sql = "select RXCUI,STR from ".$this->umls_db.".rxnconso where STR = '".$str."' and SAB='RXNORM'";
		$res = $this->dbh_obj->imw_query($sql);
		if($this->dbh_obj->imw_num_rows($res)>0){
			$row = $this->dbh_obj->imw_fetch_assoc($res);
			$arr['ccda_code'] = $row['RXCUI'];	
			$arr['ccda_display_name'] = $str;		
		}else{
			$medNameTemp = "";
			$medNameTemp = trim($str);
			$medNameTemp = str_replace("-"," ",$medNameTemp);						
			
			$arrMedictionName = explode(" ",$medNameTemp);
			$qryMore = "";
			if(count($arrMedictionName) > 1){
				foreach($arrMedictionName as $val){
					$qryMore .= " `STR` LIKE '%$val%' and";
				}
			}
			$qryMore = substr(trim($qryMore), 0, -3); 
			$sql = "select RXCUI,STR from ".$this->umls_db.".rxnconso where '".$qryMore."' and SAB='RXNORM'  LIMIT 1";
			$res = $this->dbh_obj->imw_query($sql);
			$row = $this->dbh_obj->imw_fetch_assoc($res);
			$arr['ccda_code'] = $row['RXCUI'];	
			$arr['ccda_display_name'] = $str;	
		}
		return $arr;
	}
	
	function getRXNorm_by_code($ccda_code){
		$arr = array();
		$sql = "select RXCUI,STR from  ".$this->umls_db.".rxnconso where RXCUI = '".$ccda_code."' and SAB='RXNORM'";
		$res = $this->dbh_obj->imw_query($sql);
		$row = $this->dbh_obj->imw_fetch_assoc($res);
		$arr['ccda_code'] = $ccda_code;	
		$arr['ccda_display_name'] = $row['STR'];
		return $arr;
	}
	
	function allergy_type_srh($val, $map="code_to_imw"){ 	// 	 SNOMED CT
		$val = trim($val);
		$arrAllerType = array(
							/*array("imw"=>'',"code"=>"420134006","display_name"=> "Propensity to adverse reactions (disorder)"),
							array("imw"=>'',"code"=>"418038007","display_name"=> "Propensity to adverse reactions to substance (disorder)"),
							array("imw"=>'',"code"=>"419511003","display_name"=> "Propensity to adverse reactions to drug (disorder)"),
							array("imw"=>'',"code"=>"418471000","display_name"=> "Propensity to adverse reactions to food (disorder)"),*/
							array("imw"=>'fdbATAllergenGroup',"code"=>"419199007","display_name"=> "Allergy to substance (disorder)"),
							array("imw"=>'fdbATDrugName',"code"=>"416098002","display_name"=> "Drug allergy (disorder)"),
							array("imw"=>'fdbATIngredient',"code"=>"414285001","display_name"=> "Food allergy (disorder)")
							/*array("imw"=>'',"code"=>"59037007","display_name"=> "Drug intolerance (disorder)"),
							array("imw"=>'',"code"=>"235719002","display_name"=> "Food intolerance (disorder)")*/
						  );
		$arr = array();
		if($val != ""){
			foreach($arrAllerType as $row){
				if(in_array($val, $row)){
					if($map == "code_to_imw"){
						$arr['code'] = $row['code'];
						$arr['display_name'] = $row['display_name'];
					}else{
						$arr['imw'] = $row['imw'];
					}
					break;
				}
			}
		}
		return $arr;
	}	
	
	function getProblemCode($str){
		$arr = array();
		$sql = "select Concept_Code,Preferred_Concept_Name from ".$this->umls_db.".problem_list where (Concept_Name = '".$str."' or Preferred_Concept_Name ='".$str."') and Code_System_OID = '2.16.840.1.113883.6.96'";
		$res = $this->dbh_obj->imw_query($sql);
		if($res && $this->dbh_obj->imw_num_rows($res) > 0){
			$row = $this->dbh_obj->imw_fetch_assoc($res);
			$arr['ccda_code'] = $row['Concept_Code'];	
			$arr['ccda_display_name'] = $row['Preferred_Concept_Name'];	
		}else{
			$tmp = trim($str);
			$tmp = str_replace("-"," ",$tmp);						
			
			$arrTmp = explode(" ",$tmp);
			$qryMore = "";
			if(count($arrTmp) > 1){
				foreach($arrTmp as $val){
					$qryMore .= "(`Concept_Name` LIKE '%$val%' or Preferred_Concept_Name LIKE '%$val%')";
				}
			}
			$qryMore = substr(trim($qryMore), 0, -3); 
			$sql = "select Concept_Code,Preferred_Concept_Name from ".$this->umls_db.".problem_list where '".$qryMore."' and and Code_System_OID = '2.16.840.1.113883.6.96' LIMIT 1";
			$res = $this->dbh_obj->imw_query($sql);
			if($res && $this->dbh_obj->imw_num_rows($res) > 0){
				$row = $this->dbh_obj->imw_fetch_assoc($res);
				$arr['ccda_code'] = $row['Concept_Code'];	
				$arr['ccda_display_name'] = $row['Preferred_Concept_Name'];
			}
			
		}
		return $arr;
	}
	
	function getRouteCode($route){
		global $routeset_codes,$routeset_nci_codes;
		$arr = array();
		if($routeset_codes[$route] != ""){
			$arr['ccda_code'] = $routeset_nci_codes[$route];
			$arr['ccda_display_name'] = $routeset_codes[$route];
		}else{
		$sql = "select code,term from  ".$this->umls_db.".route_nci_thesaurus where LOWER(term) = '".strtolower($route)."' OR LOWER(code) = '".strtolower($route)."'";
		$res = $this->dbh_obj->imw_query($sql);
		$row = $this->dbh_obj->imw_fetch_assoc($res);
		$arr['ccda_code'] = $row['code'];
		$arr['ccda_display_name'] = $row['term'];
		}
		return $arr;
	}
	
	function vs_result_type_srh($val){
		$val = trim($val);
		$arrVSType = array(
							array("imw"=>'Respiration',"code"=>"9279-1","display_name"=> "Respiratory Rate"),
							array("imw"=>'O2Sat',"code"=>"59408-5","display_name"=> "O2 % BldC Oximetry"),
							array("imw"=>'B/P - Systolic',"code"=>"8480-6","display_name"=> "BP Systolic"),
							array("imw"=>'B/P - Diastolic',"code"=>"8462-4","display_name"=> "BP Diastolic"),
							array("imw"=>'Temperature',"code"=>"8310-5","display_name"=> "Body Temperature"),
							array("imw"=>'Height',"code"=>"8302-2","display_name"=> "Height"),
							array("imw"=>'Weight',"code"=>"29463-7","display_name"=> "Weight Measured"),
							array("imw"=>'BMI',"code"=>"39156-5","display_name"=> "BMI (Body Mass Index)"),
							array("imw"=>'InhaleO2',"code"=>"3150-0","display_name"=> "Inhaled Oxygen Concentration"),
							array("imw"=>'pulse',"code"=>"8867-4","display_name"=> "Heart Rate")
						  );
		$arr = array();
		if($val != ""){
			foreach($arrVSType as $row){
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}
			}
		}
		return $arr;
	}
	
	function get_pt_problem_list($form_id='', $pid, $start_date = false, $end_date = false){
		$date_filter = '';
			if($start_date){
				$date_filter .= 'onset_date >= \''.$start_date.'\''; 
			}
			
			if($end_date){
				if(empty($date_filter) == false){
					$date_filter .= ' AND ';
				}
				$date_filter .= 'onset_date <= \''.$end_date.'\''; 
			}
			
			if(empty($date_filter) == false){
				$date_filter .= ' AND ';
			}
		
		$dataFinal = array();
		$sql = $sql_arc = '';
		if(isset($form_id) && $form_id != ''){
			$sql_arc  = "select pt_problem_list 
					from  
					chart_genhealth_archive 
					where patient_id='".$pid."' AND
					form_id = '".$form_id."'";
		}else{
			//GETTING RECORDS WHERE PROB_TYPE IS OT EMPTY. bECAUSE ASSESSMENT RECORDS HAVE THIS FEILD EMPTY
			$sql  = "SELECT * FROM pt_problem_list WHERE ".$date_filter." prob_type != '' AND pt_id = '".$pid."' AND status IN ('Active','Completed')";
		}
		if($sql != ""){
			$res = $this->dbh_obj->imw_query($sql);	
			while($row = $this->dbh_obj->imw_fetch_assoc($res))	{
				$dataFinal[] = $row;
			}
		}
		if($sql_arc != ""){
			$res_arc = $this->dbh_obj->imw_query($sql_arc);

			$dataFinal = array();
			while($row_arc = $this->dbh_obj->imw_fetch_assoc($res_arc)){
				$arrList = unserialize($row_arc['pt_problem_list']);
				foreach($arrList as $arrData){
						if($arrData['status'] == 'Active' || $arrData['status'] == 'Completed'){
							$dataFinal[] = $arrData;
						}
				}
			}
		}
		return $dataFinal;
	}	
	
	function problem_type_srh($val){ 		// 	 SNOMED CT
		$val = trim(strtolower($val));
		$arrProbType = array(
							array("imw"=>'finding',"code"=>"404684003","display_name"=> "Finding"),
							array("imw"=>'complaint',"code"=>"409586006","display_name"=> "Complaint"),
							array("imw"=>'diagnosis',"code"=>"282291009","display_name"=> "Diagnosis"),
							array("imw"=>'condition',"code"=>"64572001","display_name"=> "Condition"),
							array("imw"=>'smoker, current status unknown',"code"=>"248536006","display_name"=> "Finding of functional performance and activity"),
							array("imw"=>'symptom',"code"=>"418799008","display_name"=> "Symptom"),
							array("imw"=>'problem',"code"=>"55607006","display_name"=> "Problem"),
							array("imw"=>'cognitive function finding',"code"=>"373930000","display_name"=> "Cognitive function finding")
						  );
		$arr = array();
		if($val != ""){
			foreach($arrProbType as $row){
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}
			}
		}
		return $arr;
	}	
	
	function getPatientLabOrdered($pid){
		$q = "SELECT ltd.lab_status,lor.*,DATE_FORMAT(start_date,'%m-%d-%Y') AS lab_test_date_html,DATE_FORMAT(start_date,'%Y%m%d') AS lab_test_date_ccd FROM lab_observation_requested lor 
				JOIN lab_test_data ltd ON (lor.lab_test_id = ltd.lab_test_data_id) 
				WHERE ltd.lab_patient_id = '".$pid."' AND ltd.lab_status IN(1,2)";
		$res = $this->dbh_obj->imw_query($q);
		if($this->dbh_obj->imw_num_rows($res)>0){
			$lab_ordered = array();
			while($rs = $this->dbh_obj->imw_fetch_assoc($res)){
				$rs['lab_destination'] = $this->getLabOrderDestination($rs['service']);
				$lab_ordered[] = $rs;
			}
			return $lab_ordered;
		}else{
			return false;
		}
	}	
	
	function getLabOrderDestination($labTestTitle){
		$q = "SELECT lab_contact_name, lab_radiology_phone, lab_radiology_address, lab_radiology_city, lab_radiology_state, 
			 lab_radiology_zip FROM `lab_radiology_tbl` WHERE `lab_radiology_name` LIKE '".$labTestTitle."' AND 
			 lab_radiology_status='0' LIMIT 1";
		$res = $this->dbh_obj->imw_query($q);
		if($res && $this->dbh_obj->imw_num_rows($res)>0){
			$rs = $this->dbh_obj->imw_fetch_assoc($res);
			return $rs;
		}
		return false;
	}

	function getPatientLabResults($labId,$pid){
		$q = "SELECT lor.*,DATE_FORMAT(result_date,'%m-%d-%Y') AS lab_result_date_html,DATE_FORMAT(result_date,'%Y%m%d') AS lab_result_date_ccd FROM lab_observation_result lor 
				JOIN lab_test_data ltd ON (lor.lab_test_id = ltd.lab_test_data_id) 
				WHERE lor.lab_test_id='".$labId."' AND ltd.lab_patient_id = '".$pid."' AND ltd.lab_status IN(1,2)";
		$res = $this->dbh_obj->imw_query($q);
		if($this->dbh_obj->imw_num_rows($res)>0){
			$lab_ordered = array();
			while($rs = $this->dbh_obj->imw_fetch_assoc($res)){
				$lab_ordered[] = $rs;
			}
			return $lab_ordered;
		}else{
			return false;
		}
	}	
	
	function result_status_srh($val){ 	// 	 SNOMED CT
		$val = trim($val);
		$arrResultStatus = array(
							//array("imw"=>'3',"code"=>"aborted","display_name"=> "aborted"),
							array("imw"=>'1',"code"=>"active","display_name"=> "active"),
							array("imw"=>'3',"code"=>"cancelled","display_name"=> "cancelled"),
							array("imw"=>'2',"code"=>"completed","display_name"=> "completed")
							//array("imw"=>'held',"code"=>"held","display_name"=> "held"),
							//array("imw"=>'suspended',"code"=>"suspended","display_name"=> "suspended")
							/*array("imw"=>'',"code"=>"59037007","display_name"=> "Drug intolerance (disorder)"),
							array("imw"=>'',"code"=>"235719002","display_name"=> "Food intolerance (disorder)")*/
						  );
		$arr = array();
		if($val != ""){
			foreach($arrResultStatus as $row){
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}
			}
		}
		return $arr;
	}
	
	public function valuesNewRecordsAssess($patient_id,$sel=" * ",$LF="0",$flgmemo=1){
		$return_arr = array();
		$strmemo = ($flgmemo==1) ? "AND chart_master_table.memo != '1' " : "" ;
		$LF = ($LF == "1") ? "AND chart_master_table.finalize = '1' " : "";
		$qry = "SELECT ".$sel." FROM chart_master_table ".
			  "INNER JOIN chart_assessment_plans ON chart_master_table.id = chart_assessment_plans.form_id ".
			  "WHERE chart_master_table.patient_id = '$patient_id' AND chart_master_table.delete_status='0' AND chart_master_table.purge_status='0' ".
			  "AND chart_master_table.record_validity = '1' ".
			  $strmemo. //do not get memo assessments and plans: 08-04-2014
			  $LF.
			  "ORDER BY chart_master_table.date_of_service DESC, chart_master_table.create_dt DESC, chart_master_table.id DESC LIMIT 0,1 ";
		$res = $this->dbh_obj->imw_query($qry);
		if($this->dbh_obj->imw_num_rows($res)>0){
			while($rs = $this->dbh_obj->imw_fetch_assoc($res)){
				$return_arr = $rs;
			}
		}
		return $return_arr;
	}
	
	public function getXmlArr($strXml){
		$return_arr = array();
		
		//XML Sections
		$arrTND = array("assessment", "plan", "ne", "resolve","eye","conmed","pbid");
		$arrTNU = array("dt_time", "usrId");

		$xml = new DOMDocument;
		$xml->loadXML( $strXml );
		
		$data = $xml->getElementsByTagName('data');
		
		$counter = 0;
		foreach($data as $dtval){
			foreach($dtval->childNodes as $nodename){
				if($nodename->nodeType == 1){
					foreach($arrTND as $key => $nmd){
						$oNmd = $nodename->getElementsByTagname($nmd);
						foreach($oNmd as $assess){
							$vNmd = $assess->nodeValue;
							$arrRet["data"]["ap"][$counter][$nmd]=$vNmd;
						}
					}
					$counter++;
				}
			}
		}
		
		$counter = 0;
		$updates = $xml->getElementsByTagName('updates');
		foreach($updates as $upVal){
			foreach($upVal->childNodes as $nodename){
				if($nodename->nodeType == 1){
					foreach($arrTNU as $key => $nmd){
						$oNmd = $nodename->getElementsByTagname($nmd);
						foreach($oNmd as $assess){
							$vNmu = $assess->nodeValue;
							$arrRet["updates"]["update"][$counter][$nmd]=$vNmu;
						}
					}
				}
			}
		}	
		
		$arrApVals = $response_arr = $tmp_arr = array();
		if(count($arrRet) > 0){
			$return_arr = $arrRet;
		}
		return $return_arr;
	}
	
	function getEncounterDiagnosis($form_id, $pid){
		$q = "SELECT * FROM pt_problem_list WHERE pt_id = '".$pid."' AND LOWER(status) != 'deleted'";
		if($form_id!='') $q .= " AND form_id = '".$form_id."'";
		else if($form_id=='') $q .= " AND (form_id != '' AND form_id != '0')";
		$res = $this->dbh_obj->imw_query($q);
		if($res && $this->dbh_obj->imw_num_rows($res)>0){
			$encounter_diagnosis = array();
			while($rs = $this->dbh_obj->imw_fetch_assoc($res)){
				$temp_title = $rs['problem_name'];
				$temp_title_ar = explode(';',$temp_title);
				$rs['problem_name'] = trim($temp_title_ar[0]);
				$encounter_diagnosis['problem_list_data'] = $rs;
			}
			return $encounter_diagnosis;
		}
		return false;
	}
	
	function get_functional_status($val){
		$arr = array();
		$val = trim($val);
		if($val == "NE"){
			$arr['code'] = "";
			$arr['display_name'] = "Not Evaluated";
		}else if($val == 0){
			$arr['code'] = "66557003";
			$arr['display_name'] = "No Disability";
		}else if($val >= 10 && $val<=30){
			$arr['code'] = "161043008";
			$arr['display_name'] = "Mild Disability";
		}else if($val >= 40 && $val<=70){
			$arr['code'] = "161044002";
			$arr['display_name'] = "Moderate Disability";
		}else if($val >= 80 && $val<=100){
			$arr['code'] = "161045001";
			$arr['display_name'] = "Severe Disability";
		}
		return $arr;
	}
	
	function get_cognitive_status($val){
		$val = trim($val);
		$arrResultStatus = array(
							array("imw"=>'Alert',"code"=>"248233002","display_name"=> "Alert"),
							array("imw"=>'Oriented X3',"code"=>"426224004","display_name"=> "No Disability"),
							array("imw"=>'Confused',"code"=>"162702000","display_name"=> "Slight Disability"),
							array("imw"=>'Agitated',"code"=>"162721008","display_name"=> "Moderate Disability"),
							array("imw"=>'Flat Affect',"code"=>"932006","display_name"=> "Severe Disability"),
							array("imw"=>'Uncooperative',"code"=>"248042003","display_name"=> "Severe Disability"),
							array("imw"=>'Mentally Retarded',"code"=>"419723007","display_name"=> "Severe Disability")
						  );
		$arr = array();
		if($val != ""){
			foreach($arrResultStatus as $row){
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}
			}
		}
		return $arr;
	}	
	
	function getUDIprocWise($proc_id){
		$proc_id = trim($proc_id);
		if($proc_id=='') return false;
		$q = "SELECT * FROM `lists` WHERE `parent_id` = '$proc_id'";
		$res = $this->dbh_obj->imw_query($q);
		if($res && $this->dbh_obj->imw_num_rows($res)>0){
			$implantable_array = array();
			while($rs = $this->dbh_obj->imw_fetch_assoc($res)){
				$comments = explode('||', $rs['comments']);
				//$parse_udi = 
				$jsonData2 				= stripslashes(html_entity_decode($comments[2]));
				$jsonData2 				= str_replace("\\", "",$jsonData2);
				$temp_device_detail 	= json_decode($jsonData2,true);
				$device_detail 			= $temp_device_detail['gmdnTerms']['gmdn'];
				$rs['device_name'] 		= $device_detail['gmdnPTName'];
				$rs['device_desc'] 		= $device_detail['gmdnPTDefinition'];
				$implantable_array[] = $rs;
			}
			return $implantable_array;
		}else return false;
	}
	
	function get_med_route_val_code($input,$return='code'){
		/***IF $return='code', it will return NCI code against input value;
			if $return='value', it will return route value against NCI code****/
		if($return=='code'){$q = "SELECT code as val1 FROM route_codes WHERE LOWER(route_name)='".strtolower($input)."' LIMIT 1";}
		else{$q = "SELECT route_name as val1 FROM route_codes WHERE UPPER(code)='".strtoupper($input)."' LIMIT 1";}
		$res = $this->dbh_obj->imw_query($q);
		if($res && $this->dbh_obj->imw_num_rows($res)==1){
			$rs = $this->dbh_obj->imw_fetch_assoc($res);
			return $rs['val1'];
		}
		return false;
	}
	
	function getEncounterFacility($form_id,$pid){
		$q = "SELECT sa.sa_facility_id  AS facility 
				FROM schedule_appointments sa 
				JOIN chart_master_table cmt ON (cmt.date_of_service = sa.sa_app_start_date) 
				WHERE sa.sa_patient_id ='".$pid."' 
					  AND cmt.id = '".$form_id."' LIMIT 1";
		$res = $this->dbh_obj->imw_query($q);
		$facility = false;
		if($res && $this->dbh_obj->imw_num_rows($res)>0){
			$rs = $this->dbh_obj->imw_fetch_assoc($res);
			$facility = $rs['facility'];
		}
		if($facility){
			$qry_facility = "select name,phone,street,city,state,postal_code from facility where id='".$facility."' LIMIT 1";
		}else{
			$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type='1' LIMIT 1";
		}
		$res_fac = $this->dbh_obj->imw_query($qry_facility);
		if($res_fac && $this->dbh_obj->imw_num_rows($res_fac)>0){
			$rs_fac = $this->dbh_obj->imw_fetch_assoc($res_fac);
			return $rs_fac;
		}
		return false;
	}
	
	function get_provider_code($val){
		$val = trim($val);
		$arrResultStatus = array(
							array("imw"=>'Attending Physician',"code"=>"405279007","display_name"=> "Attending physician"),
							array("imw"=>'Physician',"code"=>"309343006","display_name"=> "Physician"),
							array("imw"=>'Resident',"code"=>"405277009","display_name"=> "Resident physician"),
							array("imw"=>'Consultant',"code"=>"158967008","display_name"=> "Consultant physician")
						  );
		$arr = array();
		if($val != ""){
			foreach($arrResultStatus as $row){
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}
			}
		}
		return $arr;
	}
	
	/*Create GUID for the ID (string) passed*/
	function createGUID($id, $salt='')
	{
		if( defined('IPORTAL_SERVER') && IPORTAL_SERVER !== '' ){
			$hash = IPORTAL_SERVER;
			$hash = md5($hash.$salt);
		}
		else
			$hash = md5('iPortal'.$salt);
		
		$id = (string)$id;
		
		$hash = substr_replace($hash, $id, -strlen($id));
		
		$hash = substr_replace($hash, '-', 8, 0);
		$hash = substr_replace($hash, '-', 13, 0);
		$hash = substr_replace($hash, '-', 18, 0);
		$hash = substr_replace($hash, '-', 23, 0);
		
		
		return $hash;
	}
	
	function getBirthSexInfo($patient_id){
		$q = "select * from general_medicine where patient_id = '$patient_id' LIMIT 1";
		$res = $this->dbh_obj->imw_query($q);
		if($res && $this->dbh_obj->imw_num_rows($res)>0){
			$rs = $this->dbh_obj->imw_fetch_assoc($res);
			return $rs;
		}
		return false;
	}
	
	function gender() {
	
		$qry = "Select * From gender_code Where is_deleted = 0  Order By gender_id";
		$sql = $this->dbh_obj->imw_query($qry);
		$cnt = $this->dbh_obj->imw_num_rows($sql);
		
		$return = array();
		if( $cnt > 0 )
		{
			while($row = $this->dbh_obj->imw_fetch_assoc($sql) )
			{
				$return[$row['gender_name']] = $row['gender_code'];
			}
		}
		
		return $return;
	}
}