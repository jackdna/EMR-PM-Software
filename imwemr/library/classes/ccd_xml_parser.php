<?php 
/*CCD XML Parser*/
include_once(dirname(__FILE__).'/../../config/globals.php');
include_once(dirname(__FILE__).'/ccda_functions.php');
class CDAXMLParser{
		public $file_name = "";
		public $pid = "";
		public $arrMedications = array();
		public $arrProblemList = array();
		public $arrAllergies = array();
		public $arrSxProc = array();
		public $arrXMLData = array();
		public $arrPatientData = array();
		public function __construct($file_name){
			$this->file_name = $file_name;
			$arrFileInfo = pathinfo($this->file_name);
			if(strtolower($arrFileInfo['extension']) == "xml"){
				$this->XML_to_array();
			}else if($arrFileInfo['extension'] == "csv"){
				$this->CSV_to_array();
			}
		}
		public function XML_to_array(){
			$xml_data = file_get_contents($this->file_name);
			
			//--FIXING RACE EXTENSION CODE.			
			$xml_data = str_replace('sdtc:raceCode','sdtcraceCode',$xml_data);
			//file_put_contents($this->file_name,$xml_data);
			//$xml = simplexml_load_file($this->file_name);
			$xml	= simplexml_load_string($xml_data);

			// BEGIN XML DETAILS //
			$this->arrXMLData['date_time'] = $xml->effectiveTime['value'];
			// END XML DETAILS //
			
			// BEGIN PATIENT DEMOGRAPHIC DATA 
			foreach($xml->recordTarget->patientRole->id as $arrID){
				if($arrID['root'] == "2.16.840.1.113883.4.1")
				$this->arrPatientData['ssn'] = $arrID['extension'];
			}
			$pt_Fname = $pt_Mname = $pt_Mname_BR = false;
			$this->arrPatientData['lname'] = trim($xml->recordTarget->patientRole->patient->name->family);
			for($i=0; $i<sizeof($xml->recordTarget->patientRole->patient->name->given); $i++){
				if($xml->recordTarget->patientRole->patient->name->given[$i]['qualifier'] == "CL"){
					$pt_Fname = trim($xml->recordTarget->patientRole->patient->name->given->$i);
				}else if($xml->recordTarget->patientRole->patient->name->given[$i]['qualifier'] == "BR"){
					$pt_Mname_BR = trim($xml->recordTarget->patientRole->patient->name->given->$i);
				}
				else{
					if(!$pt_Fname) $pt_Fname = trim($xml->recordTarget->patientRole->patient->name->given->$i);
					else if(!$pt_Mname) $pt_Mname = trim($xml->recordTarget->patientRole->patient->name->given->$i);
				}
			}
			if($pt_Fname){
				$this->arrPatientData['fname'] = $pt_Fname;
			}
			if($pt_Mname){
				$this->arrPatientData['mname'] = $pt_Mname;
			}
			if($pt_Mname_BR){
				$this->arrPatientData['mname_br'] = $pt_Mname_BR;
			}
			
			if($xml->recordTarget->patientRole->patient->birthTime['value']!="" && $xml->recordTarget->patientRole->patient->birthTime['value']!="00000000")
				$this->arrPatientData['dob'] =  date('Y-m-d', strtotime($xml->recordTarget->patientRole->patient->birthTime['value']));
			else
				$this->arrPatientData['dob'] =  '';
			$arr = gender_srh($xml->recordTarget->patientRole->patient->administrativeGenderCode['code'], "imw_to_code");
			$this->arrPatientData['gender'] = trim($arr['imw']);
			$this->arrPatientData['zip'] = trim($xml->recordTarget->patientRole->addr->postalCode);
			
			if(empty($xml->recordTarget->patientRole->patient->raceCode['displayName']) == false){
				$this->arrPatientData['race'] = (string)$xml->recordTarget->patientRole->patient->raceCode['displayName'];
			}
			if(empty($xml->recordTarget->patientRole->patient->sdtcraceCode['displayName']) == false){
				$this->arrPatientData['raceExtension'] = (string)$xml->recordTarget->patientRole->patient->sdtcraceCode['displayName'];
			}
			if(empty($xml->recordTarget->patientRole->patient->sdtcraceCode['code']) == false){
				$this->arrPatientData['raceExtension_code'] = (string)$xml->recordTarget->patientRole->patient->sdtcraceCode['code'];
				$race_temp_arr = race_srh('',$this->arrPatientData['raceExtension_code']);
				$this->arrPatientData['raceExtension'] = $race_temp_arr['display_name'];
			}
			if(empty($xml->recordTarget->patientRole->patient->languageCommunication->languageCode['code']) == false){
				$this->arrPatientData['language'] = code_to_language((string)$xml->recordTarget->patientRole->patient->languageCommunication->languageCode['code']);
				$this->arrPatientData['lang_code']= (string)$xml->recordTarget->patientRole->patient->languageCommunication->languageCode['code'];
			}
			
			if(empty($xml->recordTarget->patientRole->patient->ethnicGroupCode['displayName']) == false){
				$this->arrPatientData['ethnicity'] = (string)$xml->recordTarget->patientRole->patient->ethnicGroupCode['displayName'];
			}
			
			//Patient Address
			if(empty($xml->recordTarget->patientRole->addr->streetAddressLine[0]) == false){
				$this->arrPatientData['street_1'] = (string)trim($xml->recordTarget->patientRole->addr->streetAddressLine[0]);
			}
			
			if(empty($xml->recordTarget->patientRole->addr->streetAddressLine[1]) == false){
				$this->arrPatientData['street_2'] = (string)trim($xml->recordTarget->patientRole->addr->streetAddressLine[1]);
			}
			
			if(empty($xml->recordTarget->patientRole->addr->city) == false){
				$this->arrPatientData['city'] = (string)trim($xml->recordTarget->patientRole->addr->city);
			}
			
			if(empty($xml->recordTarget->patientRole->addr->state) == false){
				$this->arrPatientData['state'] = (string)trim($xml->recordTarget->patientRole->addr->state);
			}
			
			if(empty($xml->recordTarget->patientRole->addr->country) == false){
				$this->arrPatientData['country'] = (string)trim($xml->recordTarget->patientRole->addr->country);
			}
			
			//Telphone Numbers
			$tel_phn_arr = array(0 => 'home_phone', 1 => 'work_phone', 2 => 'mobile_contact');
			foreach($tel_phn_arr as $key => $val){
				if($xml->recordTarget->patientRole->telecom[$key] && empty($xml->recordTarget->patientRole->telecom[$key]['value']) == false){
					$tel_no = (string)$xml->recordTarget->patientRole->telecom[$key]['value'];
					$output = preg_replace( '/[^0-9]/', '', $tel_no );
					$this->arrPatientData[$val] = core_phone_format(substr($output,1));
				}
			}
			
			// END PATIENT DEMOGRAPHIC DATA
			foreach($xml->component->structuredBody as $components){
				foreach($components as $sections){
					foreach($sections as $section){
						// BEGIN MEDICATIONS SECTION //
						//Few CCDs don't have this section, but they have Human Readable <text> tag section. Those can't be imported.
						if($section->code['code'] == "10160-0"){	
							foreach($section->entry as $entries){
								foreach($entries->substanceAdministration as $entry){//pre( $entry);
									$index = count($this->arrMedications);
									$this->arrMedications[$index]['ccda_code'] = $entry->consumable->manufacturedProduct->manufacturedMaterial->code['code'];
									$this->arrMedications[$index]['ccda_code_system'] = $entry->consumable->manufacturedProduct->manufacturedMaterial->code['codeSystem'];
									$this->arrMedications[$index]['ccda_code_system_name'] = $entry->consumable->manufacturedProduct->manufacturedMaterial->code['codeSystemName'];
									$this->arrMedications[$index]['title'] = $entry->consumable->manufacturedProduct->manufacturedMaterial->code['displayName'];
									if($entry->effectiveTime->low['value']>0){
										$this->arrMedications[$index]['begdate'] = date('Y-m-d',strtotime($entry->effectiveTime->low['value']));
									}else{
										$this->arrMedications[$index]['begdate'] = "";
									}
									
									if($entry->effectiveTime->high['value']>0){
										$this->arrMedications[$index]['enddate'] = date('Y-m-d',strtotime($entry->effectiveTime->high['value']));								}else{
										$this->arrMedications[$index]['enddate'] = "";
									}
									$this->arrMedications[$index]['destination'] = $entry->doseQuantity['value']." ".$entry->doseQuantity['unit'];
									$med_route_val = get_med_route_val_code($entry->routeCode['code'],'value');
									$this->arrMedications[$index]['route'] = $med_route_val ? ucwords(strtolower($med_route_val)) : '';
									$this->arrMedications[$index]['sites'] = getIMWSite($entry->approachSiteCode['displayName']);
									$this->arrMedications[$index]['pid'] = $this->pid;
									$this->arrMedications[$index]['type'] = 1;
								}
							}
						}
						// END MEDICATIONS SECTION //
						
						// BEGIN PROBLEM LIST SECTION //
						if($section->code['code'] == "11450-4"){
							foreach($section->entry as $entries){
								foreach($entries->act as $entry){
									$index = count($this->arrProblemList);
									if(!isset($entry->entryRelationship->observation->value['nullFlavor'])){
									$this->arrProblemList[$index]['ccda_code'] = $entry->entryRelationship->observation->value['code'];
									$this->arrProblemList[$index]['ccda_code_system'] = $entry->entryRelationship->observation->value['codeSystem'];
									$this->arrProblemList[$index]['ccda_code_system_name'] = $entry->entryRelationship->observation->value['codeSystemName'];
									$this->arrProblemList[$index]['problem_name'] = $entry->entryRelationship->observation->value['displayName'];
									if($entry->effectiveTime->low['value']>0)
									$this->arrProblemList[$index]['onset_date'] = date('Y-m-d',strtotime($entry->effectiveTime->low['value']));							else
									$this->arrProblemList[$index]['onset_date'] = '';
									$this->arrProblemList[$index]['OnsetTime'] = date('H:i:s',strtotime($entry->effectiveTime->low['value']));
									$this->arrProblemList[$index]['prob_type'] = $entry->entryRelationship->observation->code['displayName'];
									$this->arrProblemList[$index]['pt_id'] = $this->pid;
									$this->arrProblemList[$index]['user_id'] = $_SESSION['authId'];
									$this->arrProblemList[$index]['status'] = 'Active';
										foreach($entry->entryRelationship->observation->entryRelationship as $entryRelation){
											if($entryRelation->observation->templateId['root'] == "2.16.840.1.113883.10.20.22.4.6"){
											$this->arrProblemList[$index]['status'] = $entryRelation->observation->value['displayName'];
											}
										
										}
									}else if(!isset($entry->entryRelationship->observation->value->translation['nullFlavor'])){
									$this->arrProblemList[$index]['ccda_code'] = $entry->entryRelationship->observation->value->translation['code'];
									$this->arrProblemList[$index]['ccda_code_system'] = $entry->entryRelationship->observation->value->translation['codeSystem'];
									$this->arrProblemList[$index]['ccda_code_system_name'] = $entry->entryRelationship->observation->value->translation['codeSystemName'];
									$this->arrProblemList[$index]['problem_name'] = $entry->entryRelationship->observation->value->translation['displayName'];
									if($entry->effectiveTime->low['value'])
									$this->arrProblemList[$index]['onset_date'] = date('Y-m-d',strtotime($entry->effectiveTime->low['value']));							else
									$this->arrProblemList[$index]['onset_date'] = '';
									$this->arrProblemList[$index]['OnsetTime'] = date('H:i:s',strtotime($entry->effectiveTime->low['value']));
									$this->arrProblemList[$index]['prob_type'] = $entry->entryRelationship->observation->code->translation['displayName'];
									$this->arrProblemList[$index]['pt_id'] = $this->pid;
									$this->arrProblemList[$index]['user_id'] = $_SESSION['authId'];
									}
									
								}
							}
						}
						// END PROBLEM LIST SECTION //
						
						// BEGIN ALLERGIES SECTION //
						if($section->code['code'] == "48765-2"){	
							foreach($section->entry as $entries){
								foreach($entries->act as $entry){//pre($entry);
									$index = count($this->arrAllergies);
									$this->arrAllergies[$index]['ccda_code'] = $entry->entryRelationship->observation->participant->participantRole->playingEntity->code['code'];
									$this->arrAllergies[$index]['ccda_code_system'] = $entry->entryRelationship->observation->participant->participantRole->playingEntity->code['codeSystem'];
									$this->arrAllergies[$index]['ccda_code_system_name'] = $entry->entryRelationship->observation->participant->participantRole->playingEntity->code['codeSystemName'];
									$this->arrAllergies[$index]['title'] = $entry->entryRelationship->observation->participant->participantRole->playingEntity->code['displayName'];
									if($entry->entryRelationship->observation->effectiveTime->low['value']>0)
									$this->arrAllergies[$index]['begdate'] = date('Y-m-d',strtotime($entry->entryRelationship->observation->effectiveTime->low['value']));						else
									$this->arrAllergies[$index]['begdate'] = '';
									$arrAllType = allergy_type_srh($entry->entryRelationship->observation->value['code'],"imw_to_code");
									foreach($entry->entryRelationship->observation->entryRelationship as $entryRelation){
										if($entryRelation->observation->templateId['root'] == "2.16.840.1.113883.10.20.22.4.9"){
											if(isset($entryRelation->observation->value['displayName']) && !empty($entryRelation->observation->value['displayName'])){
												$this->arrAllergies[$index]['comments'] 	 = $entryRelation->observation->value['displayName'];
												$this->arrAllergies[$index]['reaction_code'] = $entryRelation->observation->value['code'];
												foreach($entryRelation->observation->entryRelationship->observation as $reactionDetails){
													if($reactionDetails->templateId['root'] == "2.16.840.1.113883.10.20.22.4.8"){
														$this->arrAllergies[$index]['severity'] = $reactionDetails->value['displayName'];
													}
												}
												
												
											}else if($entryRelation->observation->text->reference!=""){
												$this->arrAllergies[$index]['comments'] = $entryRelation->observation->text->reference;
											}
										
										}
										if($entryRelation->observation->templateId['root'] == "2.16.840.1.113883.10.20.22.4.28"){
											$this->arrAllergies[$index]['status'] = $entryRelation->observation->value['displayName'];
										}
										if(!isset($this->arrAllergies[$index]['severity'])){
											if($entryRelation->observation->templateId['root'] == "2.16.840.1.113883.10.20.22.4.8"){
												$this->arrAllergies[$index]['severity'] = $entryRelation->observation->value['displayName'];
											}
										}
									}
									$this->arrAllergies[$index]['status'] = ucfirst(strtolower(trim($entry->statusCode['code'])));
									$this->arrAllergies[$index]['ag_occular_drug'] = $arrAllType['imw'];
									$this->arrAllergies[$index]['pid'] = $this->pid;
									$this->arrAllergies[$index]['type'] = 7;
								}
							}
						}
						//Sx Procedures
						if($section->code['code'] == "47519-4"){
							foreach($section->entry as $entries){
								foreach($entries->procedure as $entry){
									$index = count($this->arrSxProc);
									$this->arrSxProc[$index]['ccda_code'] = (string)$entry->code['code'];
									$this->arrSxProc[$index]['ccda_code_system'] = (string)$entry->code['codeSystem'];
									$this->arrSxProc[$index]['ccda_code_system_name'] = (string)$entry->code['codeSystemName'];
									$this->arrSxProc[$index]['name'] = (string)$entry->code['displayName'];
									if($entry->effectiveTime->low['value']>0){
										$this->arrSxProc[$index]['date'] = date('Y-m-d',strtotime($entry->effectiveTime->low['value']));
									}else{
										$this->arrSxProc[$index]['date'] = "";
									}
									$prov_first_name = (string)$entry->performer->assignedEntity->assignedPerson->name->given;
									$prov_last_name = (string)$entry->performer->assignedEntity->assignedPerson->name->family;
									$this->arrSxProc[$index]['provider'] = $prov_first_name.' '.$prov_last_name;
								}
							}							
						}	
					}
				}
			}
		}
		public function CSV_to_array(){
			if($handle = fopen($this->file_name,'r')){
				$index = 0;
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE){
					if($data[0] == 1)
					$this->arrMedications[$index]['type'] = 4;
					else
					$this->arrMedications[$index]['type'] = 1;
					$this->arrMedications[$index]['title'] = $data[1];
					$this->arrMedications[$index]['destination'] = $data[2];
					$this->arrMedications[$index]['sites'] = $data[3];
					$this->arrMedications[$index]['sig'] = $data[4];
					$this->arrMedications[$index]['compliant'] = $data[5];
					$this->arrMedications[$index]['begdate'] = $data[6];
					$this->arrMedications[$index]['enddate'] = $data[7];
					$index++;
				}
			}
		}
}
?>