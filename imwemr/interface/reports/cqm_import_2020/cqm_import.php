<?php 
	// require_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');
	// require_once($GLOBALS['fileroot'].'/library/classes/ccd_xml_parser.php');
	
	include_once(__DIR__.'/../../../library/classes/ccda_functions.php');

	class CQIMPORT{
		
		/* Upload related variables */
		public $operatorId = '';
		public $dirPath = '';
		public $task = '';
		public $zipName = false;
		public $statusVal = '';
		public $errorVal = '';
		public $title = 'Please select file :';
		
		/* Patient related variables */
		public $ptXmlArr = array();
		public $ptXmlArrFile = '';
		public $ptXmlMapArr = '';
		
		/* Import related variables */
		public $codeEntered = false;
		public $enCreated = false;
		public $dosCreated = false;
		public $apptCreated = false;
		public $spCreated = false;
		public $globalStatus = true;
		/* Patient data insertion related variables */
		public $ptDataInserted = false;
		public $medicationInserted = false;
		public $interventionInserted = false;
		public $diagnosisInserted = false;
		public $communicationInserted = false;

		/* CMS ID for the measure file being processed */
		private $measurecmsid = NULL;
		private $valuesets = [];
		
		/* Appointment related variables */
		public $procCreated = false;
		
		/* Superbill related variables */
		public $superBillProcCreated = false;
		
		/* Static In Patient Valueset array */
		public $staticValueSetArr = array();
		
		function __construct($task = false, $oprId = '', &$request = '', &$files = ''){
			$this->task = $task;
			$this->operatorId = (empty($oprId) == false) ? $oprId : $_SESSION['authId'];
			$this->dirPath = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH').'/users/UserId_'.$this->operatorId.'/CQM/';
			if(!is_dir($this->dirPath)){
				mkdir($this->dirPath,0777,true);
			}
			
			//Empty patient unique table 
			imw_query('truncate table uniptidentify');
			
			$this->ptXmlMapArr = array(
				'ptId' => 'ID',
				'fname' => 'First Name',
				'mname' => 'Middle Name',
				'lname' => 'Last Name',
				'mname_br' => 'Birth Name',
				'DOB' => 'Date of Birth',
				'sex' => 'Gender',
				'postal_code' => 'Postal Code',
				'race_code' => 'Race Code',
				'race' => 'Race',
				'language' => 'Language',
				'lang_code' => 'Language Code',
				'ethnicity_code' => 'Ethnicity Code',
				'ethnicity' => 'Ethnicity',
				'street' => 'Street',
				'street2' => 'Street 2',
				'city' => 'City',
				'state' => 'State',
				'country_code' => 'Country',
				'phone_home' => 'Phone Home',
				'phone_biz' => 'Phone Work',
				'phone_cell' => 'Phone Mobile'
			);
			$this->doTask($this->task, $request, $files);
		}
		
		function doTask($task = false, $request = '', $files = ''){
			if($task == false) return ;
			switch($task){
				case 'uploadZip':
					$this->uploadZip($files['file']);
				break;
			}
		}
		
		function uploadZip(&$files = ''){
			if(empty($files)) return false;
			
			$f_name = $files["name"];
			$f_type = $files["type"];
			$f_size = $files["size"];
			$f_tmp  = $files["tmp_name"];
			
			if (strpos($f_type, 'xml') !== false) {
				$this->zipName = date('Ymd_hms').'_CQM.xml';
			}elseif(strpos($f_type, 'zip') !== false){
				$this->zipName = date('Ymd_hms').'_CQM.zip';
			}
			
			if(move_uploaded_file($f_tmp, $this->dirPath.$this->zipName)){
				//$this->statusVal = 'Upload Successfull';
				$this->title = 'Uploaded File : - '.$f_name;
			}else{
				$this->errorVal = 'Upload failed !';
			}
		}
		
		function getZipContent($path = false){
			if($path === false) return ;
			
			$fileInfo = pathinfo($path);
			$patientArr = array();
			
			//Checking if folder exists
			$folderName = str_replace(array('.zip','.xml'),array('',''),$path);
			if(is_dir($folderName) == false){
				mkdir($folderName, 0770, true);
				
				//Getting content from Zip
				if(file_exists($path)){
					if($fileInfo['extension'] == 'zip'){
						$zip = new ZipArchive;
						if($zip->open($path) == TRUE){
							for($i=0; $i<$zip->numFiles; $i++){
								$name = $zip->getNameIndex($i);
								if(strpos(strtolower($name),".xml") !== false || strpos(strtolower($name),".txt") !== false){
									//Getting Patient details
									$filename =  $zip->getNameIndex($i);
									$check_xml_file = $this->getPatientDetails($zip->getFromIndex($i));
									if($check_xml_file['fname']!="" || $check_xml_file['lname']!=""){
										if(isset($check_xml_file['ptId']) && empty($check_xml_file['ptId']) == false){
											$check_xml_file['fileName'] = $filename;
											$patientArr['Patients__Found'][$check_xml_file['External_MRN_1']][] = $check_xml_file;
											//$zip->renameIndex($i,$check_xml_file['ptId'].'_'.$fileInfo['filename'].'.xml');
											$zip->extractTo($folderName, $zip->getNameIndex($i));
										}
									}
								}
							}
							$zip->close();
						}
					}else{
						$xmlData = file_get_contents($path);
						$fInfo = pathinfo($path);
						$check_xml_file = $this->getPatientDetails($xmlData);
						if($check_xml_file['fname']!="" || $check_xml_file['lname']!=""){
							if(isset($check_xml_file['ptId']) && empty($check_xml_file['ptId']) == false){
								$check_xml_file['fileName'] = $fInfo['basename'];
								$patientArr['Patients__Found'][$check_xml_file['External_MRN_1']][] = $check_xml_file;
								$fileName = $fInfo['basename'];
								rename($path,$folderName.'/'.$fileName);
							}
						}
					}
				}
			}else{
				//Getting content from folder
				$files = array_values(array_filter(scandir($folderName), function($file) {
					return !is_dir($file);
				}));
				
				foreach($files as $file){
					if(strpos(strtolower($file),".xml") !== false || strpos(strtolower($file),".txt") !== false){
						//Getting Patient details
						$xml = file_get_contents($folderName.'/'.$file);
						$check_xml_file = $this->getPatientDetails($xml);
						if($check_xml_file['fname']!="" || $check_xml_file['lname']!=""){
							if(isset($check_xml_file['ptId']) && empty($check_xml_file['ptId']) == false){
								$patientArr['Patients__Found'][$check_xml_file['External_MRN_1']][] = $check_xml_file;
							}
						}
					}
				}
			}
			
			return $patientArr;
		}
		
		//Fetching Pt. details from XML
		function getPatientDetails($xml = false){
			if($xml === false) return ;
			
			$arrPatientData = $reffPhysicianArr = array();
			
			$xml_data = str_ireplace(array('sdtc:valueSet', 'xsi:type', 'sdtc:dischargeDispositionCode','sdtc:raceCode'), array('sdtcvalueSet', 'xsitype', 'sdtcdischargeDispositionCode', 'sdtcraceCode'), $xml);
			$xml	= simplexml_load_string($xml_data);
			
			//Getting Assigned Person Details
			$reffData = &$xml->documentationOf->serviceEvent->performer->assignedEntity;
			
			//Phy. Taxenomy
			$reffPhysicianArr['Texonomy'] = (string) $reffData->code['code'];
			
			$setSnomedValueSet_arr=array();
			$setSnomedValueSet_arr['code'] = (string) $reffData->code['code'];
			$setSnomedValueSet_arr['valueset_text'] = (string)$reffData->code['codeSystemName'];
			$setSnomedValueSet_arr['codeSystem'] = (string)$reffData->code['codeSystem'];
			
			$this->saveCodeSystemValues($setSnomedValueSet_arr, '');
			
			//Phy. Name
			$reffPhysicianArr['FirstName'] = (string) $reffData->assignedPerson->name->given;
			$reffPhysicianArr['LastName'] = (string) $reffData->assignedPerson->name->family;
			
			//Phy. Address
			$reffPhysicianArr['Address1'] = (string) $reffData->addr->streetAddressLine;
			$reffPhysicianArr['City'] = (string) $reffData->addr->city;
			$reffPhysicianArr['state'] = (string) $reffData->addr->state;
			$reffPhysicianArr['ZipCode'] = (string) $reffData->addr->postalCode;
			
			//Phy. TIN
			$tinRoot = (string) $reffData->representedOrganization->id['root'];
			if(empty($tinRoot) == false && $tinRoot == '2.16.840.1.113883.4.2') $reffPhysicianArr['MDCD'] = (string) $reffData->representedOrganization->id['extension'];
			
			foreach($reffData->id as $obj){
				
				$root = (string) $obj['root'];
				$extention = (string) $obj['extension'];
				
				//NPI
				if($root == '2.16.840.1.113883.4.6'){
					$reffPhysicianArr['NPI'] = $extention;
				}
				
				//CCN
				if($root == '2.16.840.1.113883.4.336'){
					$reffPhysicianArr['MDCR'] = $extention;
				}
			}
			
			$ptUniqueAr = array();
			foreach($xml->recordTarget->patientRole->id as $arrID){
				if($arrID['root'] == "2.16.840.1.113883.4.1")
				$arrPatientData['ssn'] = $arrID['extension'];
				
				if($arrID['root'] == "2.16.840.1.113883.4.572" || $arrID['root'] == "1.3.6.1.4.1.115")
				$ptUniqueAr[] = (string)$arrID['extension'];	
			}
			
			//Patient names
			$pt_Fname = $pt_Mname = $pt_Mname_BR = false;
			$arrPatientData['lname'] = trim($xml->recordTarget->patientRole->patient->name->family);
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
				$arrPatientData['fname'] = $pt_Fname;
			}
			if($pt_Mname){
				$arrPatientData['mname'] = $pt_Mname;
			}
			if($pt_Mname_BR){
				$arrPatientData['mname_br'] = $pt_Mname_BR;
			}
			
			//Date of birth
			if($xml->recordTarget->patientRole->patient->birthTime['value']!="" && $xml->recordTarget->patientRole->patient->birthTime['value']!="00000000")
				$arrPatientData['DOB'] =  date('Y-m-d', strtotime($xml->recordTarget->patientRole->patient->birthTime['value']));
			else
				$arrPatientData['DOB'] =  '';
			
			//Gender
			$arr = gender_srh($xml->recordTarget->patientRole->patient->administrativeGenderCode['code'], "imw_to_code");
			$arrPatientData['sex'] = trim(ucfirst($arr['imw']));
			
			//Zip code
			$arrPatientData['postal_code'] = trim($xml->recordTarget->patientRole->addr->postalCode);
			
			//Race
			if(empty($xml->recordTarget->patientRole->patient->raceCode['disaplyName']) == false){
				$arrPatientData['race'] = (string)$xml->recordTarget->patientRole->patient->raceCode['disaplyName'];
			}	
			
			//Race Code
			$rcCode = '';
			if(empty($xml->recordTarget->patientRole->patient->raceCode['code']) == false){
				$rcCode = (string)$xml->recordTarget->patientRole->patient->raceCode['code'];
			}
			
			//Race Array from DB
			$arrRace = $this->getRaceHierarcy($arrPatientData['race'], $rcCode);
			
			//Changing Xml race name to DB race name
			if(isset($arrRace['race_name']) && empty($arrRace['race_name']) == false){
				$arrPatientData['race'] = $arrRace['race_name'];
			}
			
			//Changing Xml race name to DB race name
			if(isset($arrRace['cdc_code']) && empty($arrRace['cdc_code']) == false){
				$arrPatientData['race_code'] = $arrRace['cdc_code'];
			}
			
			//Checking Race - Other race
			if(!isset($arrPatientData['race']) || empty($arrPatientData['race']) && $rcCode == '2131-1'){
				$arrPatientData['race'] = "Other Race";
			}
			
			if(!isset($arrPatientData['race_code']) || empty($arrPatientData['race_code'])){
				$arrPatientData['race_code'] = $rcCode;
			}
			
			//Race Extension
			if(empty($xml->recordTarget->patientRole->patient->sdtcraceCode['displayName']) == false){
				$arrPatientData['raceExtension'] = (string)$xml->recordTarget->patientRole->patient->sdtcraceCode['displayName'];
			}
			
			if(empty($xml->recordTarget->patientRole->patient->sdtcraceCode['code']) == false){
				$arrPatientData['raceExtension_code'] = (string)$xml->recordTarget->patientRole->patient->sdtcraceCode['code'];
				$race_temp_arr = race_srh('',$arrPatientData['raceExtension_code']);
				$arrPatientData['raceExtension'] = $race_temp_arr['display_name'];
			}
			
			//Language Code
			if(empty($xml->recordTarget->patientRole->patient->languageCommunication->languageCode['code']) == false){
				$arrPatientData['language'] = code_to_language((string)$xml->recordTarget->patientRole->patient->languageCommunication->languageCode['code']);
				$arrPatientData['lang_code']= (string)$xml->recordTarget->patientRole->patient->languageCommunication->languageCode['code'];
			}
			
			//Ethnicity Code
			if(empty($xml->recordTarget->patientRole->patient->ethnicGroupCode['code']) == false){
				$arrPatientData['ethnicity_code'] = (string)$xml->recordTarget->patientRole->patient->ethnicGroupCode['code'];
			}
			
			$arrEthnicity = $this->ethnictySearch($arrPatientData['ethnicity_code']);
			//Adding Ethinicity name to array from DB ethnicity code
			if(isset($arrEthnicity['display_name']) && empty($arrEthnicity['display_name']) == false){
				$arrPatientData['ethnicity'] = $arrEthnicity['display_name'];
			}
			
			//Patient Address
			if(empty($xml->recordTarget->patientRole->addr->streetAddressLine[0]) == false){
				$arrPatientData['street'] = (string)trim($xml->recordTarget->patientRole->addr->streetAddressLine[0]);
			}
			
			if(empty($xml->recordTarget->patientRole->addr->streetAddressLine[1]) == false){
				$arrPatientData['street2'] = (string)trim($xml->recordTarget->patientRole->addr->streetAddressLine[1]);
			}
			
			if(empty($xml->recordTarget->patientRole->addr->city) == false){
				$arrPatientData['city'] = (string)trim($xml->recordTarget->patientRole->addr->city);
			}
			
			if(empty($xml->recordTarget->patientRole->addr->state) == false){
				$arrPatientData['state'] = (string)trim($xml->recordTarget->patientRole->addr->state);
			}
			
			if(empty($xml->recordTarget->patientRole->addr->country) == false){
				$arrPatientData['country_code'] = (string)trim($xml->recordTarget->patientRole->addr->country);
			}
			
			//Telphone Numbers
			$tel_phn_arr = array('HP' => 'phone_home', 'WP' => 'phone_biz', 'MP' => 'phone_cell');
			//foreach($tel_phn_arr as $key => $val){
				if($xml->recordTarget->patientRole->telecom['value'] && empty($xml->recordTarget->patientRole->telecom['value']) == false){
					$tel_no = (string)$xml->recordTarget->patientRole->telecom['value'];
					$output = preg_replace( '/[^0-9]/', '', $tel_no );
					$telecomUse = (string) $xml->recordTarget->patientRole->telecom['use'];
					$arrPatientData[$tel_phn_arr[$telecomUse]] = core_phone_format(substr($output,1));
				}
			//}
			
			//Patient Xml Unique ID
			if(count($ptUniqueAr) > 0) $arrPatientData['External_MRN_1'] = $ptUniqueAr[0];
			
			
			//Measure Unique ID
			$setId = '';
			foreach($xml->component->structuredBody as $components){
				foreach($components as $sections){
					foreach($sections as $section){
						$tmpEncounterArr = array();
						switch($section->code['code']){
							//Measure Section
							case '55186-1':
								//If data exists in Entry Tag of Reporting tag
								foreach($section->entry as $obj){
									$organizer = &$obj->organizer;
									$refArr = &$organizer->reference->externalDocument;
									$setId = (string) $refArr->setId['root'];
								}
							break;
						}
					}
				}
			}
			$measureNQF = [
				'abdc37cc-bac6-4156-9b91-d1be2c8b7268' => 'CMS165v8',
				'39e0424a-1727-4629-89e2-c46c2fbb3f5f' => 'CMS133v8',
				'9a0339c2-3d9b-11e1-8634-00237d5bf174' => 'CMS132v8',
				'9a032d9c-3d9b-11e1-8634-00237d5bf174' => 'CMS68v9',
				'a3837ff8-1abc-4ba9-800e-fd4e7953adbd' => 'CMS156v8',
				'e35791df-5b25-41bb-b260-673337bc44a8' => 'CMS138v8',
				'f58fc0d6-edf5-416a-8d29-79afbfd24dea' => 'CMS50v8',
				'db9d9f09-6b6a-4749-a8b2-8c1fdb018823' => 'CMS143v8',
				'53d6d7c3-43fb-4d24-8099-17e74c022c05' => 'CMS142v8',
				'd90bdab4-b9d2-4329-9993-5c34e2c0dc66' => 'CMS131v8'
			];

			$arrPatientData['External_MRN_4'] = $measureNQF[strtolower($setId)];
			
			
			//Checking if patient exists or not
			$ptExists = array();
			$ptExists = $this->get_patient_suggestions($arrPatientData);
			if(count($ptExists) > 0 && isset($ptExists[0]['id']) && empty($ptExists[0]['id']) == false){
				$arrPatientData['ptId'] = $ptExists[0]['id'];
				$this->ptXmlArr[$ptExists[0]['id']] = $xml;
			}
			
			$addRefPhy = $this->addRefPhysician($reffPhysicianArr, $arrPatientData);
			//$arrPatientData = $this->managePtData($arrPatientData);
			return $arrPatientData;
		}
		
		//Checking if patient exists in our system or not
		public function get_patient_suggestions($xml_obj = array()){
			if(count($xml_obj) == 0) return ;
			
			//Checking Its External Unique ID
			//$checkUnique = imw_query('SELECT id FROM patient_data where External_MRN_1 = "'.$xml_obj['External_MRN_1'].'"');
			//if(imw_num_rows($checkUnique) > 0){
			//	while($row11 = imw_fetch_assoc($checkUnique)){
			//		$tmp_arr[] = $row11;
			//		$updateRec = false;
			//		$updateRec = UpdateRecords($row11['id'], 'id', $xml_obj, 'patient_data');
			//		if($updateRec !== false){
			//			$tmp_arr[] = $row11;
			//		}
			//	}
			//}else{
				
				$sql = imw_query('SELECT id from patient_data where fname = "'.$xml_obj['fname'].'" AND lname = "'.$xml_obj['lname'].'" AND DOB = "'.$xml_obj['DOB'].'" AND sex = "'.$xml_obj['sex'].'" AND postal_code = "'.$xml_obj['postal_code'].'" ');
				if(imw_num_rows($sql) > 0 && $sql){
					while($row = imw_fetch_assoc($sql)){
						$updateRec = false;
						$updateRec = UpdateRecords($row['id'], 'id', $xml_obj, 'patient_data');
						if($updateRec !== false){
							$tmp_arr[] = $row;
						}
					}
				}else{
					if(empty($xml_obj['fname']) == false && empty($xml_obj['lname']) == false){
						$tmpArr = array();
						$xml_obj['DOB'] = getDateFormatDB($xml_obj['DOB']);
						$xml_obj['date'] = date('Y-m-d  H:i:s');
						$xml_obj['created_by'] = $this->operatorId;
						$xml_obj['hipaa_mail'] = 1;
						$xml_obj['hipaa_email'] = 1;
						$xml_obj['hipaa_voice'] = 1;
						
						if($xml_obj['lang_code']=='eng') $xml_obj['lang_code']='en';
						
						$insert_id = AddRecords($xml_obj,'patient_data');
						if(empty($insert_id) == false){
							$update_id = imw_query('UPDATE `patient_data` SET pid = '.$insert_id.' where id = '.$insert_id.'');
							if($update_id){
								$sql = imw_query('SELECT id from patient_data where id = '.$insert_id.'');
								if(imw_num_rows($sql) > 0 && $sql){
									while($row = imw_fetch_assoc($sql)){
										$tmp_arr[] = $row;
									}
								}
							}
						}
					}
				}
			//}
			
			if(count($tmp_arr) > 0){
				$returnVal = $tmp_arr;
			}
			return $returnVal; 
		}
		
		//Return Hierarcy regarding race or race code
		public function getRaceHierarcy($race,$race_code=''){
			$arr_race = explode(',',$race);
			$RACE_DATA_AR = array();
			
			if(count($arr_race) > 0){
				foreach($arr_race as $key => $val){
					if(!empty($race_code)){
						$q = "SELECT race_name,cdc_code,parent_id,h_code FROM `race` WHERE is_deleted = '0' AND cdc_code LIKE '$race_code' LIMIT 1";
					}else if(!empty($val)){
						$q = "SELECT race_name,cdc_code,parent_id,h_code FROM `race` WHERE is_deleted = '0' AND race_name LIKE '$val' LIMIT 1";
					}
					if($q != ''){
						$res = imw_query($q);
						$rs = imw_fetch_assoc($res);
						$h_code		= $rs['h_code'];
						//$RACE_DATA_AR[] = $rs;
						if($h_code!=''){
							$arr_h_code = explode('.',$h_code);
							if(count($arr_h_code)>1){// Its a child node, lookup for parent.
								for($i = 0; $i < count($arr_h_code); $i++){
									$removed_h_code = array_pop($arr_h_code);
									$remaining_h_code = implode('.',$arr_h_code);
									$q2 = imw_query("SELECT race_name,cdc_code,parent_id,h_code FROM race WHERE h_code LIKE '$remaining_h_code' LIMIT 1");
									if(imw_num_rows($q2) > 0 ){
										$res2 = imw_fetch_assoc($q2);
										array_unshift($rs,$res2);
									}
								}
							}
						}
						$RACE_DATA_AR = $rs;
					}
				}
			}
			
			return $RACE_DATA_AR;
		}
		
		//Return ethnicity hierarchy based on code 
		function ethnictySearch($val){
			$val = str_replace(',','',$val);
			$val = trim($val);
			$arrRace = array();
			$q = imw_query("SELECT ethnicity_name,cdc_code FROM `ethnicity` WHERE ".($val ? "cdc_code = '".$val."' And " : '')." is_deleted = '0'");
			if(imw_num_rows($q) > 0){
				while($rorow12w = imw_fetch_assoc($q)){
					$tmpEth = array();
					$tmpEth['imw'] 			= strtolower($rorow12w['ethnicity_name']);
					$tmpEth['code'] 			= $rorow12w['cdc_code'];
					$tmpEth['display_name'] 	= $rorow12w['ethnicity_name'];
					$arrRace[]  = $tmpEth;
				}
			}
			$arr = array();
			if($val != ""){
				$match = false;
				foreach($arrRace as $row){
					if(in_array($val, $row)){
						$match = true;
						$arr['code'] = $row['code'];
						$arr['display_name'] = $row['display_name'];
						break;
					}else{
						$arr['code'] = "2186-5";
						$arr['display_name'] = "Not Hispanic or Latino";
					}
					if( $match ) break;
				}
			}
			return $arr;
		}
		
		//Get provider array
		function get_provider_ar($proId = 0){
			if($proId == 0){
				$qry_providers = "select id, fname, lname, mname from users where user_type IN (1,12) AND delete_status = 0 order by lname";
			}
			else{
				$qry_providers = "select id, fname, lname, mname from users where id='$proId' LIMIT 0,1";	
			}
			$result = imw_query($qry_providers);
			if($result && imw_num_rows($result)>=1){
				while($rs = imw_fetch_assoc($result)){
					$id = $rs['id'];
					$arr_proName['LAST_NAME']=$rs['lname'];
					$arr_proName['FIRST_NAME']=$rs['fname'];
					$arr_proName['MIDDLE_NAME']=$rs['mname'];
					$pro_name[$id] = changeNameFormat($arr_proName);
				}//end of while.
				return $pro_name;
			}
			else
				return false;
		}
		
		//Get facilities
		function get_facility_arr($fac_id = false){
			if($fac_id == false){
				$qry_fac = "select id,name from facility order by name";
			}
			else{
				$qry_fac = "select id,name from facility where id='$fac_id'";
			}
			$result = imw_query($qry_fac);
			if($result && imw_num_rows($result)>=1){
				while($rs = imw_fetch_assoc($result)){
					$id = $rs['id'];
					$pro_name[$id] = $rs['name'];
				}//end of while.
				return $pro_name;
			}
			else
				return false;
		}
		
		//Get Facility Encounter
		function getFacEncounter(){
			$facId = $encounterId = '';
			
			//Checikng HQ Facility
			$checkHQ = "SELECT id FROM facility WHERE facility_type = '1' LIMIT 0,1 ";
			$res = imw_exec($checkHQ);
			if($res !== false){
				$facId = $res["id"];
			}else{
				// Fix if No Hq. is selected
				$sql = "SELECT id FROM facility LIMIT 0,1 ";
				$res = imw_exec($sql);
				if($row != false){
					$facId = $res["id"];
				}
			}
			
			//If HQ facility ID is not empty
			if(empty($facId) == false){
				$sql = "SELECT encounterId FROM facility WHERE id='".$facId."' ";
				$res = imw_exec($sql);
				if($res != false){
					$encounterId = $res["encounterId"];
				}
				
				//get from policies
				$sql = imw_query("select Encounter_ID from copay_policies WHERE policies_id = '1' ");
				if(imw_num_rows($sql) > 0){
					$rowEnc = imw_fetch_assoc($sql);
					$encounterId_2 = $rowEnc["Encounter_ID"];	
				}
				
				//Compare Ids
				if($encounterId < $encounterId_2){
					$encounterId = $encounterId_2;
				}
				
				$flgbreak = 0;
				$counter = 0; //check only 100 times
				do{
					//check in superbill
					if($flgbreak == 0){
						$sql = imw_query("select count(*) as num FROM superbill WHERE encounterId='".$encounterId."' ");
						if(imw_num_rows($sql) > 0){
							$row = imw_fetch_assoc($sql);
							if($row["num"] == 0){
								$flgbreak = 1;
							}	
						}
					}
					
					//check in chart_master_table--
					if($flgbreak == 0){
						$sql = imw_query("select count(*) as num FROM chart_master_table WHERE encounterId='".$encounterId."' ");
						if(imw_num_rows($sql) > 0){
							$row = imw_fetch_assoc($sql);
							if($row["num"] == 0){
								$flgbreak = 1;
							}	
						}
					}
					
					$encounterId = $encounterId+1;
					$counter++;
				}while($flgbreak == 0 && $counter < 100);
				
				
				$sql = "UPDATE copay_policies SET Encounter_ID = '".($encounterId+1)."' WHERE policies_id='1' ";
				$row = imw_query($sql);		
				
				//Update
				$sql = "UPDATE facility SET encounterId = '".($encounterId+1)."' WHERE id='".$facId."' ";
				$res = imw_query($sql);	
				
				return $encounterId;
			}
		}
		
		//Handling Patient Import
		function handlePtImport($ptId = '', $zipName = '', $provId = '', $facId = '', $ptNm = '', $fileName = ''){
			$returnArr = array();	
			if(empty($ptId) || empty($zipName) || empty($provId) || empty($facId) || empty($fileName)) return $returnArr['error'] = 'Invalid Input. Please try again';
			//If valid inputs are provided than proceed
			$path = $this->dirPath.$zipName;
			$fileInfo = pathinfo($path);
			
			//If file is a single XML
			if(strtolower($fileInfo['extension']) == 'xml'){
				//Adding folder name to the path if file is a single upload
				$path = $this->dirPath.$fileInfo['filename'].'/'.$fileInfo['basename'];
			}
			if(file_exists($path)){
				$folderPath = $this->dirPath.$fileInfo['filename'].'/'.$fileName;
				if(file_exists($folderPath)){
					$xml = file_get_contents($folderPath);
					$xml = str_ireplace(array('sdtc:valueSet', 'xsi:type', 'sdtc:dischargeDispositionCode'), array('sdtcvalueSet', 'xsitype', 'sdtcdischargeDispositionCode'), $xml);
					$xmlArr = simplexml_load_string($xml);
					if($xmlArr){
						return $returnArr['status'] = $this->parseXML($xmlArr, $provId, $facId, $ptId, $ptNm);
					}else{
						return $returnArr['error'] = 'Invalid XML format.';
					}
				}
			}else{
				return $returnArr['error'] = 'Unable to find the uploaded docuemnt. Please try again';
			}
			
		}
		
		//Filtering XML
		function filterXml(&$xmlArr = array(), &$ptId = ''){
			$returnArr = array();
			if(count($xmlArr) == 0) return false;
			
			//pre($xmlArr);
			
			//Accessing Structure body
				
				//Encounter Details ==> Root -> component -> structuredBody -> component -> section -> entry -> act -> entryRelationship -> encounter
				//Medications Data 	==> Root -> component -> structuredBody -> component -> section -> substanceAdministration
				//Intervention Data 	==> Root -> component -> structuredBody -> component -> section -> entry -> act 
				
			foreach($xmlArr->component->structuredBody as $components){
				foreach($components as $sections){
					foreach($sections as $section){
						$tmpEncounterArr = array();
						switch($section->code['code']){
							//Measure Section
							case '55186-1':
								/**
								 * Measure CMS ID
								 */
								$measureIds = [
									'abdc37cc-bac6-4156-9b91-d1be2c8b7268' => 'CMS165v8',
									'39e0424a-1727-4629-89e2-c46c2fbb3f5f' => 'CMS133v8',
									'9a0339c2-3d9b-11e1-8634-00237d5bf174' => 'CMS132v8',
									'9a032d9c-3d9b-11e1-8634-00237d5bf174' => 'CMS68v9',
									'a3837ff8-1abc-4ba9-800e-fd4e7953adbd' => 'CMS156v8',
									'e35791df-5b25-41bb-b260-673337bc44a8' => 'CMS138v8',
									'f58fc0d6-edf5-416a-8d29-79afbfd24dea' => 'CMS50v8',
									'db9d9f09-6b6a-4749-a8b2-8c1fdb018823' => 'CMS143v8',
									'53d6d7c3-43fb-4d24-8099-17e74c022c05' => 'CMS142v8',
									'd90bdab4-b9d2-4329-9993-5c34e2c0dc66' => 'CMS131v8',
								];

								$measureGUID = (string) $section->entry->organizer->reference->externalDocument->setId['root'];
								$measureGUID = strtolower($measureGUID);

								$returnArr['cmsid'] = $measureIds[$measureGUID];
							break;
							
							//Reporting Section
							case '55187-9':
								
							break;
							
							//Patient Data
							case '55188-7':
								//If data exists in Entry Tag of PatientData tag
								foreach($section->entry as $entryParent){
									$tmpMedicationsArr = $tmpProcedure = array();
									
									//Procedure Data
									if(isset($entryParent->procedure)){
										$procData = &$entryParent->procedure;
										$startIntervention = $endIntervention = '';
										
										$templateId = (string) $procData->templateId['root'];
										if(is_array($procData->templateId)){
											$templateId = (string) $procData->templateId[1]['root'];
										}

                                        if($templateId == '2.16.840.1.113883.10.20.24.3.64'){
                                            $tmpProcArr = [
                                                'snomedct' => [],
                                                'cpt' => [],
                                                'primaryCodeSystem' => []
                                            ];

                                            /** Diagnosis Data from QDM Attribute */
                                            $code = '';
                                            $codeSystem = '';
                                            $codeSystemName = '';
                                            if( 
                                                isset($procData->code['code']) &&
                                                isset($procData->code['codeSystem']) &&
                                                isset($procData->code['codeSystemName'])
                                            )
                                            {

                                                $code = (string) $procData->code['code'];
                                                $codeSystem = (string) $procData->code['codeSystem'];
                                                $codeSystemName = (string) $procData->code['codeSystemName'];
                                                $codeSystemName = strtolower($codeSystemName);

                                                $tmpProcArr[$codeSystemName]['code'] = $code;
                                                $tmpProcArr[$codeSystemName]['codeSystem'] = $codeSystem;
                                                $tmpProcArr['primaryCodeSystem'] = $codeSystemName;

                                            }

                                            /** Additional Data from Translation */
                                            if( isset($procData->code->translation) ){

                                                foreach($procData->code->translation as $translation){

                                                    $translationCodeSystemName = (string) $translation['codeSystemName'];
                                                    $translationCodeSystemName = strtolower($translationCodeSystemName);

                                                    $tmpProcArr[$translationCodeSystemName]['code'] = (string) $translation['code'];
                                                    $tmpProcArr[$translationCodeSystemName]['codeSystem'] = (string) $translation['codeSystem'];
                                                }
                                            }

                                            $refusal = $refusal_snomedCode = '';
                                            $refusal = (bool) $procData['negationInd'];  
                                            $refusal_snomedCode = (string) $procData->entryRelationship->observation->value['code'];

                                            /** Add refusal data to the main response array */
                                            $tmpProcArr['is_refusal'] = $refusal;
                                            $tmpProcArr['refusal_code'] = $refusal_snomedCode;


                                            //Effective Time
                                            $StartTs = $EndTs = '';
                                            if(isset($procData->effectiveTime)){

                                                //Start Date Time
                                                if(isset($procData->effectiveTime->low)){
                                                    $startIntervention = $this->formatXMLDt($procData->effectiveTime->low);	
                                                }

                                                //End Date Time
                                                if(isset($procData->effectiveTime->high)){
                                                    $endIntervention = $this->formatXMLDt($procData->effectiveTime->high);	
                                                }
                                            }


                                            /** Date */
                                            $tmpProcArr['start_date'] = $startIntervention;
                                            $tmpProcArr['end_date'] = $endIntervention;


                                            /** Procedure Type (Procedure/Intervention) */
                                            $procedure_status = $proc_type = '';
                                            if($templateId == '2.16.840.1.113883.10.20.24.3.64'){
                                                $procedure_status="completed";
                                                $proc_type="procedure";
                                            }

                                            $tmpProcArr['status'] = $procedure_status;
                                            $tmpProcArr['type'] = $proc_type;


                                            $returnArr['Procedure'][] = $tmpProcArr;
                                        }
                                        
                                        if($templateId == '2.16.840.1.113883.10.20.22.4.14'){
                                            $tmpImplantArr = [
                                                'hcpcs' => [],
                                                'primaryCodeSystem' => [],
                                            ];

                                            /** Diagnosis Data from QDM Attribute */
                                            $hcpcscode = '';
                                            $hcpcscodeSystem = '';
                                            $hcpcscodeSystemName = '';
                                            if(isset($procData->participant->participantRole)) {
                                                if(isset($procData->participant->participantRole->playingDevice->code)) {
                                                    $hcpcscode = (string) $procData->participant->participantRole->playingDevice->code['code'];
                                                    $hcpcscodeSystem = (string) $procData->participant->participantRole->playingDevice->code['codeSystem'];
                                                    $hcpcscodeSystemName = (string) $procData->participant->participantRole->playingDevice->code['codeSystemName'];
                                                    $hcpcscodeSystemName = strtolower($hcpcscodeSystemName);

                                                    $tmpImplantArr[$hcpcscodeSystemName]['code'] = $hcpcscode;
                                                    $tmpImplantArr[$hcpcscodeSystemName]['codeSystem'] = $hcpcscodeSystem;
                                                    $tmpImplantArr['primaryCodeSystem'] = $hcpcscodeSystemName;
                                                    
                                                }
                                            }

                                            $refusal = $refusal_snomedCode = '';
                                            $refusal = (bool) $procData['negationInd'];  
                                            $refusal_snomedCode = $procData->entryRelationship->observation->value['code'];

                                            /** Add refusal data to the main response array */
                                            $tmpImplantArr['is_refusal'] = $refusal;
                                            $tmpImplantArr['refusal_code'] = $refusal_snomedCode;


                                            //Effective Time
                                            $StartTs = $EndTs = '';
                                            if(isset($procData->effectiveTime)){

                                                //Start Date Time
                                                if(isset($procData->effectiveTime->low)){
                                                    $startIntervention = $this->formatXMLDt($procData->effectiveTime->low);	
                                                }

                                                //End Date Time
                                                if(isset($procData->effectiveTime->high)){
                                                    $endIntervention = $this->formatXMLDt($procData->effectiveTime->high);	
                                                }
                                            }
                                            

                                            /** Date */
                                            $tmpImplantArr['start_date'] = $startIntervention;
                                            $tmpImplantArr['end_date'] = $endIntervention;


                                            //Description
                                            $description = '';
                                            if(isset($procData->text)){
                                                $description = (string) $procData->text;
                                            }
                                            $procedure_status = '';
                                            if(isset($procData->statusCode)){
                                                $procedure_status = (string) $procData->statusCode["code"];
                                            }

                                            $implant_status="";
                                            if(!empty($procedure_status) && $procedure_status=='active') {
                                                $implant_status="order";
                                            }
                                            if(!empty($procedure_status) && $procedure_status=='completed') {
                                                $implant_status="applied";
                                            }

                                            $tmpImplantArr['status'] = $implant_status;
                                            $tmpImplantArr['description'] = $description;

                                            $returnArr['Implantable'][] = $tmpImplantArr;
                                        }
                                        
									}
									//pre($entryParent->substanceAdministration);
									//Medications Data
									if(isset($entryParent->substanceAdministration)){
										
										//Medication Array
										$medData = &$entryParent->substanceAdministration;
                                        
                                        $medtemplateId = (string) $medData->templateId['root'];
										if(is_object($medData->templateId)){
											if($medData->templateId[1]['root']!=""){
												$medtemplateId = (string) $medData->templateId[1]['root'];
											}else{
												$medtemplateId = (string) $medData->templateId['root'];
											}
										}
										
										$medText = (isset($medData->text)) ? (string) $medData->text : '';
										$medText = (empty($medText) == false) ? $medText : '';	//Med Name
										
										$medStatus = (isset($medData->statusCode)) ? (string) $medData->statusCode['code'] : '';
										$medStatus = (empty($medStatus) == false) ? $medStatus : '';	//Med Status
										
										$medComment = (isset($medData->comment)) ? (string) $medData->comment : '';
										$medComment = (empty($medComment) == false) ? $medComment : '';	//Med Status
										
										$tmpTimeArr = array();
										$StartTs = $EndTs  = $medSig = $medDos = $doseQuantity = '';
										if(isset($medData->effectiveTime)){
											//Getting Date Time
											$dateTime = $medData->effectiveTime[0];
											
											//Getting Value
											$valueObj = $medData->effectiveTime[1];
                                            
											//doseQuantity 
                                            $doseQuantity = (isset($medData->doseQuantity['value'])) ? (string)$medData->doseQuantity['value'] : '';
											$doseQuantity = (empty($doseQuantity) == false) ? $doseQuantity : '';
                                            
												//Start Date Time
												if(isset($dateTime->low)){
													$StartTs = $this->formatXMLDt($dateTime->low);	
												}
												
												//End Date Time
												if(isset($dateTime->high)){
													$EndTs = $this->formatXMLDt($dateTime->high);	
												}
												
												//Dose value
												$medDos = (isset($valueObj->period['value'])) ? (string)$valueObj->period['value'] : '';
												$medDos = (empty($medDos) == false) ? $medDos : '';
												
												//Sig value
												$medSig = (isset($valueObj->period['unit'])) ? (string)$valueObj->period['unit'] : '';
												$medSig = (empty($medSig) == false) ? $medSig : '';
											
											$tmpTimeArr['StartTs'] = $StartTs['date'];
											$tmpTimeArr['EndTs'] = $EndTs['date'];
											$tmpTimeArr['StartTime'] = $StartTs['time'];
											$tmpTimeArr['EndTime'] = $EndTs['time'];
											//$tmpTimeArr['Dose'] = $medDos;
											$tmpTimeArr['Dose'] = $doseQuantity;
											$tmpTimeArr['Sig'] = $medDos.';'.$medSig;
										}
										
										//Description
										$description = '';
										if($medText!=""){
											$description = (string) $medText;
											$description = (empty($description) == false) ? $this->getTitle($medText) : '';
										}
										
										//Code
										$code = '';
										if(isset($medData->code['code'])){
											$code = (string) $medData->code['code'];
											$code = (empty($code) == false) ? $code : '';
										}

										//Value set
										$valueSet = '';
										if(isset($medData->code['sdtcvalueSet'])){
											$valueSet = (string) $medData->code['sdtcvalueSet'];
											$valueSet = (empty($valueSet) == false) ? $valueSet : '';
										}
										
										//Code System
										$codeSystem = '';
										if(isset($medData->code['codeSystem'])){
											$codeSystem = (string) $medData->code['codeSystem'];
											$codeSystem = (empty($codeSystem) == false) ? $codeSystem : '';
										}
										
										//Code System Name
										$codeSystemName = '';
										if(isset($medData->code['codeSystemName'])){
											$codeSystemName = (string) $medData->code['codeSystemName'];
											$codeSystemName = (empty($codeSystemName) == false) ? $codeSystemName : '';
										}
										
										$medRxNorm=(string)$medData->consumable->manufacturedProduct->manufacturedMaterial->code['code'];
										$medRxNorm = (empty($medRxNorm) == false) ? $medRxNorm : '';
                                        
                                        $medRxNormSystem=(string)$medData->consumable->manufacturedProduct->manufacturedMaterial->code['codeSystem'];
										$medRxNormSystem = (empty($medRxNormSystem) == false) ? $medRxNormSystem : '';
                                        
                                        $medRxNormValueSet=(string)$medData->consumable->manufacturedProduct->manufacturedMaterial->code['sdtcvalueSet'];
										$medRxNormValueSet = (empty($medRxNormValueSet) == false) ? $medRxNormValueSet : '';
                                       
                                        $medRxNormText=(string)$medData->consumable->manufacturedProduct->manufacturedMaterial->code->originalText;
										$medRxNormText = (empty($medRxNormText) == false) ? $medRxNormText : '';
										
										//Refusal Value
										$refusalMed = '';
										if(isset($medData['negationInd'])){
											$refusalMed = (string) $medData['negationInd'];
											$refusalMed = (empty($refusalMed) == false && $refusalMed == 'true') ? 1 : 0;
										}
										
										//Refusal Code
										$refusalMedCode = '';
										if(isset($medData->entryRelationship->observation->value['code'])){
											$refusalMedCode = (string) $medData->entryRelationship->observation->value['code'];
										}
                                        
                                        if($medtemplateId == '2.16.840.1.113883.10.20.24.3.47' || $medtemplateId == '2.16.840.1.113883.10.20.22.4.42'){
                                            $medStatus="order";
                                        }
                                        if($medtemplateId == '2.16.840.1.113883.10.20.24.3.41' || $medtemplateId == '2.16.840.1.113883.10.20.22.4.16'){
                                            $medStatus="active";
                                        }
										
										$tmpMedicationsArr['Name'] = $medText;
										$tmpMedicationsArr['Status'] = $medStatus;
										$tmpMedicationsArr['Comments'] = $medComment;
										$tmpMedicationsArr['Comments'] = $medComment;
										$tmpMedicationsArr['Time'] = $tmpTimeArr;
										$tmpMedicationsArr['RxNorm'] = $medRxNorm;
										$tmpMedicationsArr['refusal'] = $refusalMed;
										$tmpMedicationsArr['refusalCode'] = $refusalMedCode;
										
										
										$tmpMedicationsArr['code'] = $medRxNorm;
										$tmpMedicationsArr['valueSet'] = $medRxNormValueSet;
										$tmpMedicationsArr['valueset_text'] = $medRxNormText;
										$tmpMedicationsArr['codeSystem'] = $medRxNormSystem;
										$tmpMedicationsArr['codeSystemName'] = $codeSystemName;
										$tmpMedicationsArr['type'] = 'Medication';

										
										$returnArr['Medications'][] = $tmpMedicationsArr;
										$this->saveCodeSystemValues($tmpMedicationsArr, $ptId);
										
										if(strtolower(trim($medData['negationInd']))=='true')
										{
											$tmpMedicationsArr['code'] = (string)$medData->entryRelationship->observation->value['code'];
											$tmpMedicationsArr['valueSet'] = (string)$medData->entryRelationship->observation->value['sdtcvalueSet'];
											$tmpMedicationsArr['valueset_text'] = '';
											$tmpMedicationsArr['codeSystem'] = (string)$medData->entryRelationship->observation->value['codeSystem'];
											//$tmpMedicationsArr['codeSystemName'] = $codeSystemName;
											$tmpMedicationsArr['type'] = 'Medication';
											$this->saveCodeSystemValues($tmpMedicationsArr, $ptId); 
										}
									}
									
									//Observation Values
									if(isset($entryParent->observation)){
										$observations=&$entryParent->observation;
										
										$templateId = (string) $observations->templateId['root'];
										if(is_object($observations->templateId)){
											if($observations->templateId[1]['root']!=""){
												$templateId = (string) $observations->templateId[1]['root'];
											}else{
												$templateId = (string) $observations->templateId['root'];
											}
										}
										
										// Value Type
											// PQ --> Vital Signs
											// CD --> Exam Data [ Physicial Exam ]
										
										$valueType = (isset($observations->value['xsitype']) && empty($observations->value['xsitype']) == false) ? $observations->value['xsitype'] : '';
										
										/**
										 * Unidentified section - Temporary comment 
										 * $templateId == '2.16.840.1.113883.10.20.24.3.144' ||
										 * **/
										if(empty($templateId) == false && ( $templateId == '2.16.840.1.113883.10.20.24.3.59' || $templateId == '2.16.840.1.113883.10.20.24.3.144'))
										{
											
											if(empty($valueType) == false && $valueType == 'PQ'){
												//Vital Signs	
												$tmpVsArr = array();
												
												//Vs Name
												$vsText = (isset($observations->text)) ? (string) $observations->text : '';
												$vsText = (empty($vsText) == false) ? $this->getTitle($vsText) : '';
												
												//Vs Code
												$vsCode = (isset($observations->statusCode['code'])) ? (string) $observations->statusCode['code'] : '';
												$vsCode = (empty($vsCode) == false) ? $vsCode : '';
												
												//Vs Value
												$vsValue = (isset($observations->value['value'])) ? (string) $observations->value['value'] : '';
												$vsValue = (empty($vsValue) == false) ? $vsValue : '';
												
												//Vs Unit
												$vsUnit = (isset($observations->value['unit'])) ? (string) $observations->value['unit'] : '';
												$vsUnit = (empty($vsUnit) == false) ? $vsUnit : '';
												
												if(empty($vsText) == false){
													$tmpVsArr['type'] = $vsText;
													$tmpVsArr['status'] = $vsCode;
													$tmpVsArr['range_vital'] = $vsValue;
													$tmpVsArr['unit'] = $vsUnit;

													//Effective Time
													$StartTs = $EndTs = '';
													if(isset($observations->effectiveTime)){
														//Start Date Time
														if(isset($observations->effectiveTime->low)){
															$StartTs = $this->formatXMLDt($observations->effectiveTime->low);	
														}
														
														//End Date Time
														if(isset($observations->effectiveTime->high)){
															$EndTs = $this->formatXMLDt($observations->effectiveTime->high);	
														}
													}
                                                    
													//Effective Time
													$authorTime = '';
													if(isset($observations->author)){
														//Start Date Time
														if(isset($observations->author->time)){
															$authorTime = $this->formatXMLDt($observations->author->time);	
														}

													}
													
													$tmpVsArr['date_vital'] = $StartTs;
													$tmpVsArr['authorTime'] = $authorTime;
													
													$returnArr['vital_sign'][] = $tmpVsArr;
													
													//Code
													$code = '';
													if(isset($observations->code['code'])){
														$code = (string) $observations->code['code'];
														$code = (empty($code) == false) ? $code : '';
													}
													
													//Value set
													$valueSet = '';
													if(isset($observations->code['sdtcvalueSet'])){
														$valueSet = (string) $observations->code['sdtcvalueSet'];
														$valueSet = (empty($valueSet) == false) ? $valueSet : '';
													}
													
													//Code System
													$codeSystem = '';
													if(isset($observations->code['codeSystem'])){
														$codeSystem = (string) $observations->code['codeSystem'];
														$codeSystem = (empty($codeSystem) == false) ? $codeSystem : '';
													}
													
													//Code System Name
													$codeSystemName = '';
													if(isset($observations->code['codeSystemName'])){
														$codeSystemName = (string) $observations->code['codeSystemName'];
														$codeSystemName = (empty($codeSystemName) == false) ? $codeSystemName : '';
													}
													
													$setSnomedValueSet_arr=array();
													$setSnomedValueSet_arr['code'] = $code;
													$setSnomedValueSet_arr['valueSet'] = $valueSet;
													$setSnomedValueSet_arr['valueset_text'] = $observations->code->originalText;
													$setSnomedValueSet_arr['codeSystem'] = $codeSystem;
													$setSnomedValueSet_arr['codeSystemName'] = $codeSystemName;
													$setSnomedValueSet_arr['type'] = 'vital sign';
													
													$this->saveCodeSystemValues($setSnomedValueSet_arr, $ptId);
												}
											}
											else
											{
												/**
												 * Physical Exam performed
												 */

												/* Temparory container for observation data */
												$tmpObservation = [];
												
												/** Effective Date and Time */
												$startTime = $endTime = '';
												if(isset($observations->effectiveTime)){
													
													//Start Date Time
													if(isset($observations->effectiveTime->low)){
														$startTime = $this->formatXMLDt($observations->effectiveTime->low);	
													}
													
													//End Date Time
													if(isset($observations->effectiveTime->high)){
														$endTime = $this->formatXMLDt($observations->effectiveTime->high);	
													}
												}
                                                
                                                //Effective Time
                                                $authorTime = '';
                                                if(isset($observations->author)){
                                                    //Start Date Time
                                                    if(isset($observations->author->time)){
                                                        $authorTime = $this->formatXMLDt($observations->author->time);	
                                                    }

                                                }

												$tmpObservation['start']	= $startTime;
												$tmpObservation['end']		= $endTime;
                                                $tmpObservation['authorTime'] = $authorTime;
                                                if($startTime=='')$tmpObservation['start']	= $authorTime;
                                                
												$tmpObservation['observation'] = [];
												$observation = &$tmpObservation['observation'];

												$observation['codetype'] 	= (string) $observations->code['codeSystemName'];
												$observation['code']		= (string) $observations->code['code'];
												$observation['description']	= (string) $observations->text;
												unset($observation);

												$tmpObservation['status'] = (string) $observations->statusCode['code'];

												/**
												 * Observation result from <value> tag
												 */
												$tmpObservation['result'] = [];
												$observationResult = &$tmpObservation['result'];

												$observationResult['codetype']	= (string) $observations->value['codeSystemName'];
												$observationResult['code']		= (string) $observations->value['code'];
												unset($observationResult);


												//Null value
												$nullValue = '';
												if(isset($observations->value['nullFlavor']) || isset($observations->code['nullFlavor'])){
													$nullValue = (string) $observations->value['nullFlavor'];
												}
												$tmpObservation['null_value'] = $nullValue;
												
												//Refusal Value
												$refusalObserv = '';
												if(isset($observations->value['nullFlavor']) || isset($observations->code['nullFlavor'])){
													$refusalObserv = (string) $observations['negationInd'];
													$refusalObserv = (empty($refusalObserv) == false && $refusalObserv == 'true') ? 1 : 0;
												}
												$tmpObservation['is_refused'] = $refusalObserv;
												
												//Refusal Code
												$refusalCode = '';
												if(isset($observations->entryRelationship->observation->value['code'])){
													$refusalCode = (string) $observations->entryRelationship->observation->value['code'];
												}
												$tmpObservation['resufal_code'] = $refusalCode;
												if($templateId == '2.16.840.1.113883.10.20.24.3.144'){
													$tmpObservation['type'] = '1';
												}else if($templateId == '2.16.840.1.113883.10.20.24.3.59'){
													$tmpObservation['type'] = '0';
												}
												/* $tmpObservation['type'] = '0'; */

												$returnArr['Observation'][] = $tmpObservation;
											}
										}
										elseif(empty($templateId) == false && ($templateId == '2.16.840.1.113883.10.20.24.3.18'))
										{
											//rad test name
											if(isset($observations->text)){
												$vsText = (isset($observations->text)) ? (string) $observations->text : '';
												$vsText=(empty($vsText) == false) ? $this->getTitle($vsText) : '';
												$rad['testName']=$vsText['title'];
											}
											
											//test status
											if(isset($observations->statusCode)){
												$rad['testStatus']=(string)$observations->statusCode['code'];
											}
											
											//rad order date
											if(isset($observations->effectiveTime->low)){
												$dt_obj='';
												$dt_obj=$this->formatXMLDt($observations->effectiveTime->low);
												//Appointment Start Date/Time
												$rad['orderDate'] = $dt_obj['date'];
												$rad['orderTime'] = $dt_obj['time'];
												
											}
											//rad result date
											if(isset($observations->effectiveTime->high)){
												$dt_obj='';
												$dt_obj=$this->formatXMLDt($observations->effectiveTime->high);
												//Appointment Start Date/Time
												$rad['resultDate'] = $dt_obj['date'];
												$rad['resultTime'] = $dt_obj['time'];
											}
											
											//lonic code
											if(isset($observations->code['code']))
											$rad['loinc']=(string)$observations->code['code'];
											
											
											#################################
											# GET VALUES IN CASE NEGATION
											#################################
											
											//BY default negation/refusel status is zero
											$rad['refusal']=0;
											//checking for negation
											if(isset($observations['negationInd']))
											{
												//rad loinc code in case of negation
												if(isset($observations->entryRelationship->observation->code)){
													$rad['loinc']=(string)$observations->entryRelationship->observation->code['code'];
												}
											
												if(strtolower(trim($observations['negationInd']))=='true')
												{
													//set refusel true
													$rad['refusal']=1;
													//save refusel snowmed code
													if(isset($observations->entryRelationship->observation->value['code'])){
														$rad['refusal_snomed']=(string)$observations->entryRelationship->observation->value['code'];
														//result snowmed must be empty
														$rad['snowmedCode']='';
													}
												}
												elseif(strtolower(trim($observations['negationInd']))=='false')
												{
													//save it as it is
												}
											}
											
											$rad['refusal_reason']='';
											
											
											//test result
											if(isset($observations->value['value']))
											{
												$rad['result']=(string)$observations->value['value'];
												if(isset($observations->value['unit']))
												$rad['result'].="; Unit:".$observations->value['unit'];
											}
											elseif(isset($observations->value->originalText))
											{
												$vsText = $observations->value->originalText;
												$vsText=(empty($vsText) == false) ? $this->getTitle($vsText) : '';
												$rad['result']=(string)$vsText['title'];
											}
											else
											{
												$rad['result']=(string)$observations->value;
											}
											
											//test result snowmed code
											if(isset($observations->value['code'])){
												$rad['snowmedCode']=(string)$observations->value['code'];
											}
											
											//Value set
											$valueSet = '';
											if(isset($observations->value['sdtcvalueSet'])){
												$valueSet = (string) $observations->value['sdtcvalueSet'];
												$valueSet = (empty($valueSet) == false) ? $valueSet : '';
											}
											
											//Code System
											$codeSystem = '';
											if(isset($observations->value['codeSystem'])){
												$codeSystem = (string) $observations->value['codeSystem'];
												$codeSystem = (empty($codeSystem) == false) ? $codeSystem : '';
											}
											
											//Code System Name
											$codeSystemName = '';
											if(isset($observations->value['codeSystemName'])){
												$codeSystemName = (string) $observations->value['codeSystemName'];
												$codeSystemName = (empty($codeSystemName) == false) ? $codeSystemName : '';
											}
											
                                            // Result section for macular exam
                                            $resultCodeArr=array();
                                            $resultCodeArr['resultSnomed']='';
                                            $resultCodeArr['component']=array();
                                            if(isset($observations->entryRelationship)){
                                                foreach($observations->entryRelationship as $result_obs) {
                                                    if(isset($result_obs->observation)){
                                                        $resultsObs = &$result_obs->observation;
                                                        $result_templateId = (string) $resultsObs->templateId['root'];
                                                        if($result_templateId=='2.16.840.1.113883.10.20.22.4.2') {
                                                            $resultCodeArr['resultSnomed'] = (string) $resultsObs->value['code'];
                                                        }
                                                        if($result_templateId=='2.16.840.1.113883.10.20.24.3.149') {
                                                            $resultCodeArr['component'][] = (string) $resultsObs->value['code'];
                                                        }
                                                    }
                                                }
                                            }
                                            
                                            if($rad['snowmedCode']=='' && $resultCodeArr['resultSnomed']!='') {
                                                $rad['snowmedCode']=$resultCodeArr['resultSnomed'];
                                            }
                                            
											//pre($rad);
											$returnArr['radiology'][] = $rad;
											
											
                                            
                                            
                                            
                                            $setSnomedValueSet_arr=array();
											$setSnomedValueSet_arr['code'] = (string)$observations->code['code'];
											$setSnomedValueSet_arr['valueSet'] = (string)$observations->code['sdtcvalueSet'];
											$setSnomedValueSet_arr['valueset_text'] = (string)$observations->code->originalText;
											$setSnomedValueSet_arr['codeSystem'] = (string)$observations->code['codeSystem'];
											//$setSnomedValueSet_arr['codeSystemName'] = $codeSystemName;
											$setSnomedValueSet_arr['type'] = 'Radiology';
											
											$this->saveCodeSystemValues($setSnomedValueSet_arr, $ptId);
                                            
                                            if(strtolower(trim($observations['negationInd']))=='true')
                                            {
                                                $setSnomedValueSet_arr=array();
                                                $setSnomedValueSet_arr['code'] = (string)$observations->entryRelationship->observation->value['code'];
                                                $setSnomedValueSet_arr['valueSet'] = (string)$observations->entryRelationship->observation->value['sdtcvalueSet'];
                                                $setSnomedValueSet_arr['valueset_text'] = '';
                                                $setSnomedValueSet_arr['codeSystem'] = (string)$observations->entryRelationship->observation->value['codeSystem'];
                                                //$setSnomedValueSet_arr['codeSystemName'] = $codeSystemName;
                                                $setSnomedValueSet_arr['type'] = 'Radiology';

                                                $this->saveCodeSystemValues($setSnomedValueSet_arr, $ptId); 
                                            }
											
										}else if(empty($templateId) == false && $templateId == '2.16.840.1.113883.10.20.24.3.55'){
											
											//Patient Payer
											$tmpPayerArr = array();
											$tmpPayerArr['code'] = (string)$observations->code['code'];
											$tmpPayerArr['valueCode'] = (string)$observations->value['code'];
											$tmpPayerArr['valueCodeSys'] = (string)$observations->value['codeSystem'];
											$tmpPayerArr['valueValueSet'] = (string)$observations->value['sdtcvalueSet'];
											$tmpPayerArr['text'] = (string)$observations->originalText;
											
											
											//Start & End Dates
											$tmpPayerArr['startDt'] = $this->formatXMLDt($observations->effectiveTime->low);
											$tmpPayerArr['endDt'] = $this->formatXMLDt($observations->effectiveTime->high);
											
											$returnArr['PtPayer'][] = $tmpPayerArr;
										}
									}
									
									//Entry Act tag
									if(isset($entryParent->act)){
										$templateId = (string) $entryParent->act->templateId['root'];
										if(is_object($entryParent->act->templateId)){
											$templateId = (string) $entryParent->act->templateId[1]['root'];
										}
										
										//Intervention Array
										if( ($templateId == '2.16.840.1.113883.10.20.24.3.31' || $templateId == '2.16.840.1.113883.10.20.24.3.32'  || $templateId == '2.16.840.1.113883.10.20.24.3.64')){
											$startIntervention = $endIntervention = '';
											$tmpIntervention = array();
											
                                            $tmpInterventionArr = [
                                                'snomedct' => [],
                                                'cpt' => [],
                                                'primaryCodeSystem' => []
                                            ];
                                            
                                            
                                            /** Diagnosis Data from QDM Attribute */
                                            $code = '';
                                            $codeSystem = '';
                                            $codeSystemName = '';
                                            if( 
                                                isset($entryParent->act->code['code']) &&
                                                isset($entryParent->act->code['codeSystem']) &&
                                                isset($entryParent->act->code['codeSystemName'])
                                            )
                                            {

                                                $code = (string) $entryParent->act->code['code'];
                                                $codeSystem = (string) $entryParent->act->code['codeSystem'];
                                                $codeSystemName = (string) $entryParent->act->code['codeSystemName'];
                                                $codeSystemName = strtolower($codeSystemName);

                                                $tmpInterventionArr[$codeSystemName]['code'] = $code;
                                                $tmpInterventionArr[$codeSystemName]['codeSystem'] = $codeSystem;
                                                $tmpInterventionArr['primaryCodeSystem'] = $codeSystemName;

                                            }
                                            
                                            $refusal = $refusal_snomedCode = '';
											$refusal = (bool) $entryParent->act['negationInd'];  
											$refusal_snomedCode = $entryParent->act->entryRelationship->observation->value['code'];
                                            
                                            /** Add refusal data to the main response array */
                                            $tmpInterventionArr['is_refusal'] = $refusal;
                                            $tmpInterventionArr['refusal_code'] = $refusal_snomedCode;
                                            
											//Effective Time
											if(isset($entryParent->act->effectiveTime)){
												$StartTs = $EndTs = '';
												//Start Date Time
												if(isset($entryParent->act->effectiveTime->low)){
													$startIntervention = $this->formatXMLDt($entryParent->act->effectiveTime->low);	
												}
												
												//End Date Time
												if(isset($entryParent->act->effectiveTime->high)){
													$endIntervention = $this->formatXMLDt($entryParent->act->effectiveTime->high);	
												}
											}
                                            if(empty($startIntervention) && empty($endIntervention) && isset($entryParent->act->author->time)){
                                                $startIntervention = $this->formatXMLDt($entryParent->act->author->time);	
                                            }
                                            
                                            /** Date */
                                            $tmpInterventionArr['start_date'] = $startIntervention;
                                            $tmpInterventionArr['end_date'] = $endIntervention;
											
                                            
											//Description
											$description = '';
											if(isset($entryParent->act->code->originalText)){
												$description = (string) $entryParent->act->code->originalText;
												$description = (empty($description) == false) ? $this->getTitle($description) : '';
											}
											if(empty($description) && isset($entryParent->act->text)){
												$description = (string) $entryParent->act->text;
												$description = (empty($description) == false) ? $this->getTitle($description) : '';
											}
                                            $tmpInterventionArr['description'] = $description;
											
											$proc_type = $procedure_status = '';
											if($templateId == '2.16.840.1.113883.10.20.24.3.31'){
												$procedure_status="pending";
												$proc_type="intervention";
											}
											if($templateId == '2.16.840.1.113883.10.20.24.3.32'){
												$procedure_status="completed";
												$proc_type="intervention";
											}
											if($templateId == '2.16.840.1.113883.10.20.24.3.64'){
												$procedure_status="completed";
												$proc_type="procedure";
											}
                                            
                                            
                                            $tmpInterventionArr['status'] = $procedure_status;
                                            $tmpInterventionArr['type'] = $proc_type;
                                            
                                            $returnArr['Intervention'][] = $tmpInterventionArr;
											
                                       
                                        }
                                        
                                        $templateId1 = (string) $entryParent->act->templateId['root'];
										if(empty($templateId1) && is_object($entryParent->act->templateId)){
											$templateId1 = (string) $entryParent->act->templateId[1]['root'];
										}
                                        //Implantable Devices Array
										if( ($templateId1 == '2.16.840.1.113883.10.20.24.3.130') ){
                                            $tmpImplantArr = array();
                                            //device order
                                            if(isset($entryParent->act->entryRelationship)){
                                                if(isset($entryParent->act->entryRelationship->supply)){
                                                    
                                                    $code='';$codeSystem = '';$codeSystemName = '';$valueSet='';
                                                    if(isset($entryParent->act->entryRelationship->supply->participant)){
                                                        if(isset($entryParent->act->entryRelationship->supply->participant->participantRole)){
                                                            if(isset($entryParent->act->entryRelationship->supply->participant->participantRole->playingDevice)){
                                                                if(isset($entryParent->act->entryRelationship->supply->participant->participantRole->playingDevice->code)){
                                                                    $code = (string) $entryParent->act->entryRelationship->supply->participant->participantRole->playingDevice->code['code'];
                                                                    $code = (empty($code) == false) ? $code : '';
                                                                    
                                                                    //Code System
                                                                    if(isset($entryParent->act->entryRelationship->supply->participant->participantRole->playingDevice->code['codeSystem'])){
                                                                        $codeSystem = (string) $entryParent->act->entryRelationship->supply->participant->participantRole->playingDevice->code['codeSystem'];
                                                                        $codeSystem = (empty($codeSystem) == false) ? $codeSystem : '';
                                                                    }

                                                                    //Code System Name
                                                                    if(isset($entryParent->act->entryRelationship->supply->participant->participantRole->playingDevice->code['codeSystemName'])){
                                                                        $codeSystemName = (string) $entryParent->act->entryRelationship->supply->participant->participantRole->playingDevice->code['codeSystemName'];
                                                                        $codeSystemName = (empty($codeSystemName) == false) ? $codeSystemName : '';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    
                                                    
                                                    $tmpImplantArr = [
                                                        'hcpcs' => [],
                                                        'primaryCodeSystem' => []
                                                    ];


                                                    /** Diagnosis Data from QDM Attribute */
                                                    if( empty($code)==false && empty($codeSystem)==false && empty($codeSystemName)==false )
                                                    {

                                                        $code = (string) $code;
                                                        $codeSystem = (string) $codeSystem;
                                                        $codeSystemName = (string) $codeSystemName;
                                                        $codeSystemName = strtolower($codeSystemName);

                                                        $tmpImplantArr[$codeSystemName]['code'] = $code;
                                                        $tmpImplantArr[$codeSystemName]['codeSystem'] = $codeSystem;
                                                        $tmpImplantArr['primaryCodeSystem'] = $codeSystemName;

                                                    }
                                                
                                                    $startIntervention='';$endIntervention='';
                                                    
                                                    //Effective Time
                                                    if(isset($entryParent->act->entryRelationship->supply->effectiveTime)){
                                                        $StartTs = $EndTs = '';
                                                        //Start Date Time
                                                        if(isset($entryParent->act->entryRelationship->supply->effectiveTime->low)){
                                                            $startIntervention = $this->formatXMLDt($entryParent->act->entryRelationship->supply->effectiveTime->low);	
                                                        }

                                                        //End Date Time
                                                        if(isset($entryParent->act->entryRelationship->supply->effectiveTime->high)){
                                                            $endIntervention = $this->formatXMLDt($entryParent->act->entryRelationship->supply->effectiveTime->high);	
                                                        }
                                                    }
                                            
                                                    if(empty($startIntervention) && empty($endIntervention) && isset($entryParent->act->entryRelationship->supply->author->time)){
                                                        $startIntervention = $this->formatXMLDt($entryParent->act->entryRelationship->supply->author->time);	
                                                    }
                                                    
                                                    /** Date */
                                                    $tmpImplantArr['start_date'] = $startIntervention;
                                                    $tmpImplantArr['end_date'] = $endIntervention;

                                                    //Description
                                                    $description = '';
                                                    if(isset($entryParent->act->entryRelationship->supply->text)){
                                                        $description = (string) $entryParent->act->entryRelationship->supply->text;
                                                    }
                                                    $procedure_status = '';
                                                    if(isset($entryParent->act->entryRelationship->supply->statusCode)){
                                                        $procedure_status = (string) $entryParent->act->entryRelationship->supply->statusCode["code"];
                                                    }
                                                    
                                                    $implant_status="";
                                                    if(!empty($procedure_status) && $procedure_status=='active') {
                                                        $implant_status="order";
                                                    }
                                                    if(!empty($procedure_status) && $procedure_status=='completed') {
                                                        $implant_status="applied";
                                                    }
                                                    
                                                    $tmpImplantArr['status'] = $implant_status;
                                                    $tmpImplantArr['description'] = $description;
                                                    
                                                    $returnArr['Implantable'][] = $tmpImplantArr;

                                                }
                                            }
                                        }
                                        
										
										
										
										//Entry relationship	
										if(isset($entryParent->act->entryRelationship)){
											
											//Entry relationship Array
											$actData = &$entryParent->act->entryRelationship;
											if(count($actData) > 0){
												
												//Getting Encounter tag
												if(isset($actData->encounter)){
													
													//In Patient Static Array
													$this->staticValueSetArr = array(
														'2.16.840.1.113883.17.4077.3.2056',
														'2.16.840.1.113883.17.4077.2.2079',
														'2.16.840.1.113883.3.666.5.2289',
														'2.16.840.1.113883.3.117.1.7.1.294',
														'2.16.840.1.113883.3.117.1.7.1.295',
														'2.16.840.1.113883.3.666.5.2599',
														'2.16.840.1.113883.13.190.5.6',
														'2.16.840.1.113883.3.666.5.307',
														'2.16.840.1.113883.3.666.5.3001',
														'2.16.840.1.113883.13.190.5.27',
														'2.16.840.1.113762.1.4.1111.70',
														'2.16.840.1.113883.3.666.5.3007',
														'2.16.840.1.113883.3.666.5.625',
														'2.16.840.1.113883.3.464.1003.101.12.1060',
														'2.16.840.1.113883.3.117.1.7.1.23',
														'2.16.840.1.113883.3.464.1003.101.11.1224',
														'2.16.840.1.113883.3.666.5.647',
														'2.16.840.1.113883.3.2066.2068',
														'2.16.840.1.113883.3.2066.2060',
														'2.16.840.1.113883.3.2066.2062',
														'2.16.840.1.113762.1.4.1098.3',
														'2.16.840.1.113762.1.4.1098.4',
														'2.16.840.1.113883.3.2066.2061',
														'2.16.840.1.113762.1.4.1104.8',
														'2.16.840.1.113883.3.117.1.7.1.424',
														'2.16.840.1.113883.3.666.5.3013'
													);
													
													
													foreach($actData->encounter as $obj){
														
														//Get Template ID 
														$templateId = (string) $obj->templateId['root'];
														if(is_object($obj->templateId)){
															$templateId = (string) $obj->templateId[1]['root'];
														}
														$tmpEncounterArr['templateId'] = $templateId;
														
														//Filtering Elements
														//Code
															//Value set
															$valueSet = '';
															if(isset($obj->code['sdtcvalueSet'])){
																$valueSet = (string) $obj->code['sdtcvalueSet'];
																$valueSet = (empty($valueSet) == false) ? $valueSet : '';
															}
															
															//Code
															$code = '';
															if(isset($obj->code['code'])){
																$code = (string) $obj->code['code'];
																$code = (empty($code) == false) ? $code : '';
															}
															
															//Description
															$description = '';
															if(isset($obj->code->originalText)){
																$description = (string) $obj->code->originalText;
																$description = (empty($description) == false) ? $description : '';
															}
															
															//Code System
															$codeSystem = '';
															if(isset($obj->code['codeSystem'])){
																$codeSystem = (string) $obj->code['codeSystem'];
																$codeSystem = (empty($codeSystem) == false) ? $codeSystem : '';
															}
															
															
															//Code ValueSet
															$codeValueSet = '';
															if(isset($obj->code['sdtcvalueSet'])){
																$codeValueSet = (string) $obj->code['sdtcvalueSet'];
																$codeValueSet = (empty($codeValueSet) == false) ? $codeValueSet : '';
															}
															
															//Discharge Values Array
															$dischargeSet = $prinDiag = array();
															
															//Dischage Details
															$dischargeType = 'DischargeCode';
															$dischargeCode  = (string) $obj->sdtcdischargeDispositionCode['code'];
															$dischargeCodeSys  = (string) $obj->sdtcdischargeDispositionCode['codeSystem'];
															
															
															//Principal Diagnosis
															if($obj->entryRelationship->observation){
																$inPtObj = &$obj->entryRelationship->observation;
																$disCCode = (string)$inPtObj->code['code'];
																
																if($disCCode == '8319008'){
																	$prinDiag['field_code']  = (string) $inPtObj->value['code'];
																	$prinDiag['field_codesystem']  = (string) $inPtObj->value['codeSystem'];
																	$prinDiag['field_type'] = 'PrincipalDiag';
																}
															}
															
															if(empty($dischargeCode) == false){
																$dischargeSet['field_type'] = $dischargeType;
																$dischargeSet['field_code'] = $dischargeCode;
																$dischargeSet['field_codesystem'] = $dischargeCodeSys;
															}
															
															
														$tmpEncounterArr['inPtArr'] = array($dischargeSet, $prinDiag);
														//If code valueset is in static in patient array
														$tmpEncounterArr['isInPt'] = 0;
														if(empty($codeValueSet) == false && in_array($codeValueSet, $this->staticValueSetArr)){
															$tmpEncounterArr['isInPt'] = 1;	
														}
														
														//Effective Time
														$StartTs = $EndTs = '';
														if(isset($obj->effectiveTime) && is_object($obj->effectiveTime)){
															
															//Start Date Time
															if(isset($obj->effectiveTime->low)){
																$StartTs = $this->formatXMLDt($obj->effectiveTime->low);	
															}
															
															//End Date Time
															if(isset($obj->effectiveTime->high)){
																$EndTs = $this->formatXMLDt($obj->effectiveTime->high);	
															}
														}
                                                        
                                                        // encounter contains diagnosis section 
                                                        $encDiagArr=array();
                                                        if( isset($obj->entryRelationship->act) ) {
                                                            $diagAct = &$obj->entryRelationship->act;
                                                            
                                                            //Get Template ID 
                                                            $templateId2 = (string) $diagAct->templateId['root'];

                                                            if( $templateId2 == '2.16.840.1.113883.10.20.22.4.80' && isset($diagAct->entryRelationship->observation) ) {
                                                                $diagObj = &$diagAct->entryRelationship->observation;
                                                                
                                                                $tmpDiagnosisArr = [
                                                                    'snomedct' => [],
                                                                    'icd10cm' => [],
                                                                    'icd9cm' => [],
                                                                    'primaryCodeSystem' => [],
                                                                ];
                                                                
                                                                /** Diagnosis Data from QDM Attribute */
                                                                $diagCode = '';
                                                                $diagCodeSystem = '';
                                                                $diagCodeSystemName = '';
                                                                if( 
                                                                    isset($diagObj->value['code']) &&
                                                                    isset($diagObj->value['codeSystem']) &&
                                                                    isset($diagObj->value['codeSystemName'])
                                                                )
                                                                {

                                                                    $diagCode = (string) $diagObj->value['code'];
                                                                    $diagCodeSystem = (string) $diagObj->value['codeSystem'];
                                                                    $diagCodeSystemName = (string) $diagObj->value['codeSystemName'];
                                                                    $diagCodeSystemName = strtolower($diagCodeSystemName);
                                                                    
                                                                    $encDiagArr['codeType']='encounter';
                                                                    $encDiagArr[$diagCodeSystemName]['code'] = $diagCode;
                                                                    $encDiagArr[$diagCodeSystemName]['codeSystem'] = $diagCodeSystem;
                                                                    $encDiagArr['primaryCodeSystem'] = $diagCodeSystemName;

                                                                }
                                                                
                                                                //Effective Time
                                                                $StartDiagTs = $EndDiagTs = '';
                                                                if(isset($diagObj->effectiveTime) && is_object($diagObj->effectiveTime)){

                                                                    //Start Date Time
                                                                    if(isset($diagObj->effectiveTime->low)){
                                                                        $StartDiagTs = $this->formatXMLDt($diagObj->effectiveTime->low);	
                                                                    }

                                                                    //End Date Time
                                                                    if(isset($diagObj->effectiveTime->high)){
                                                                        $EndDiagTs = $this->formatXMLDt($diagObj->effectiveTime->high);	
                                                                    }
                                                                }

                                                                $encDiagArr['effectiveTime'] = array('startObj' => $StartDiagTs, 'endObj' => $EndDiagTs);
                                                                
                                                            }
                                                        }
                                                        
														
														$tmpEncounterArr['code'] = array('valueSet' => $valueSet, 'code' => $code, 'description' => $description);
														$tmpEncounterArr['text'] = (isset($obj->text)) ? (string) $obj->text : '';
														$tmpEncounterArr['statusCode'] = (isset($obj->statusCode['code'])) ? (string) $obj->statusCode['code'] : '';
														$tmpEncounterArr['effectiveTime'] = array('startObj' => $StartTs, 'endObj' => $EndTs);
														$tmpEncounterArr['encDiagnosis'][] = $encDiagArr;

														$returnArr['Encounter'][] = $tmpEncounterArr;
														
														$setSnomedValueSet_arr=array();
														$setSnomedValueSet_arr['code'] = $code;
														$setSnomedValueSet_arr['valueSet'] = $valueSet;
														$setSnomedValueSet_arr['valueset_text'] = $description;
														$setSnomedValueSet_arr['codeSystem'] = $codeSystem;
														$setSnomedValueSet_arr['type'] = 'Encounter';
														
														$this->saveCodeSystemValues($setSnomedValueSet_arr, $ptId);
														
														
													}
												}
												//Gettin Observations
												if(isset($actData->observation) && (string)$actData['typeCode']!=='REFR'){

													$tmpDiagnosisArr = [
														'snomedct' => [],
														'icd10cm' => [],
														'icd9cm' => [],
														'primaryCodeSystem' => [],
													];
													$diagArr = &$actData->observation;


													/** Diagnosis Data from QDM Attribute */
													$code = '';
													$codeSystem = '';
													$codeSystemName = '';
													if( 
														isset($diagArr->value['code']) &&
														isset($diagArr->value['codeSystem']) &&
														isset($diagArr->value['codeSystemName'])
													)
													{
														
														$code = (string) $diagArr->value['code'];
														$codeSystem = (string) $diagArr->value['codeSystem'];
														$codeSystemName = (string) $diagArr->value['codeSystemName'];
														$codeSystemName = strtolower($codeSystemName);

														$tmpDiagnosisArr[$codeSystemName]['code'] = $code;
														$tmpDiagnosisArr[$codeSystemName]['codeSystem'] = $codeSystem;
														$tmpDiagnosisArr['primaryCodeSystem'] = $codeSystemName;

													}

													/** Additional Data from Translation */
													if( isset($diagArr->value->translation) ){
													    
													    foreach($diagArr->value->translation as $translation){

															$translationCodeSystemName = (string) $translation['codeSystemName'];
															$translationCodeSystemName = strtolower($translationCodeSystemName);
															
															$tmpDiagnosisArr[$translationCodeSystemName]['code'] = (string) $translation['code'];
															$tmpDiagnosisArr[$translationCodeSystemName]['codeSystem'] = (string) $translation['codeSystem'];
													    }
													}
                                                    
                                                    
                                                    $problem_type='Diagnosis';
                                                    $type_code = '';
													$trans_type_code = '';
													if( 
														isset($diagArr->code['code']) &&
														isset($diagArr->code->translation['code']) 
													)
													{
														$type_code = (string) $diagArr->code['code'];
														$trans_type_code = (string) $diagArr->code->translation['code'];
                                                        $problem_type = $this->problem_type_srh($trans_type_code);
													}
                                                    $tmpDiagnosisArr['type'] = $problem_type;
                                                    
													//Effective Time
													$DiagStartTs = $DiagEndTs = '';
													if(isset($diagArr->effectiveTime)){
														//Start Date Time
														if(isset($diagArr->effectiveTime->low)){
															$DiagStartTs = $this->formatXMLDt($diagArr->effectiveTime->low);	
														}
														
														//End Date Time
														if(isset($diagArr->effectiveTime->high)){
															$DiagEndTs = $this->formatXMLDt($diagArr->effectiveTime->high);	
														}
													}
                                                    
													$tmpDiagnosisArr['effectiveTime'] = array('startObj' => $DiagStartTs, 'endObj' => $DiagEndTs);
													$returnArr['Diagnosis'][] = $tmpDiagnosisArr;	/*Container for Problems and Diagnosis*/
													//pre($actData->observation);
													
													$setSnomedValueSet_arr=array();
													$setSnomedValueSet_arr['code'] = $code;
													$setSnomedValueSet_arr['valueSet'] = '';
													$setSnomedValueSet_arr['valueset_text'] = '';
													$setSnomedValueSet_arr['codeSystem'] = $codeSystem;
													$setSnomedValueSet_arr['codeSystemName'] = $codeSystemName;
													$setSnomedValueSet_arr['type'] = 'Diagnosis';
													
													$this->saveCodeSystemValues($setSnomedValueSet_arr, $ptId);
												}
											}
										
										}
										//===========Communication From Provider To Provider===========================//
										
										if($entryParent->act->templateId['root']=="2.16.840.1.113883.10.20.24.3.4"){
											
											$tmpCommnincationArr=array();
											$tmpCommnincationArr['XML_val']=$entryParent->asXML();
											foreach($entryParent->act as $communication){
												$tmpCommnincationArr['text']=(string)$communication->text;
												if(isset($communication->effectiveTime)){
													if(isset($communication->effectiveTime->low)){
														$CommStartTs = $this->formatXMLDt($communication->effectiveTime->low);	
													}
													if(isset($communication->effectiveTime->high)){
														$CommEndTs = $this->formatXMLDt($communication->effectiveTime->high);	
													}
												}
												$tmpCommnincationArr['start_date_time']=$CommStartTs;
												$tmpCommnincationArr['end_date_time']=$CommEndTs;
											}
											$returnArr['communication'][] = $tmpCommnincationArr;
										}
                                        
                                        
                                        
                                        if($entryParent->act->templateId['root']=="2.16.840.1.113883.10.20.24.3.156"){
                                            $tmpCommnincationArr=array();
											//$tmpCommnincationArr['XML_val']=$entryParent->asXML();
											foreach($entryParent->act as $communication){
                                                
                                                $refusal = $refusal_snomedCode = $snomedCode = '';
                                                //Refusal Value
                                                if(isset($communication['negationInd'])){
                                                    $refusal = (string) $communication['negationInd'];
                                                    $refusal = (empty($refusal) == false && $refusal == 'true') ? 1 : 0;
                                                }
                                                $tmpCommnincationArr['refusal']=$refusal;
                                                
                                                $typeCode=(string) $communication['typeCode'];
												$tmpCommnincationArr['text']=(string)$communication->text;
												if(isset($communication->effectiveTime)){
													if(isset($communication->effectiveTime->low)){
														$CommStartTs = $this->formatXMLDt($communication->effectiveTime->low);	
													}
													if(isset($communication->effectiveTime->high)){
														$CommEndTs = $this->formatXMLDt($communication->effectiveTime->high);	
													}
												}
												$tmpCommnincationArr['start_date_time']=$CommStartTs;
												$tmpCommnincationArr['end_date_time']=$CommEndTs;
                                                
                                                //participant
                                                if(isset($communication->participant)){
                                                    //$typeCode=(string) $communication->participant['typeCode'];
                                                    foreach($communication->participant as $participant){
                                                        $typeCode=(string) $participant['typeCode'];
                                                        if(isset($participant->participantRole->code)){
                                                            $tmpCommnincationArr[$typeCode]=(string) $participant->participantRole->code['code'];
                                                        }
                                                    }
                                                }
                                                
                                                //participant
                                                if(isset($communication->entryRelationship)){
                                                    //$typeCode=(string) $communication->participant['typeCode'];
                                                    foreach($communication->entryRelationship as $item){
                                                        $typeCode=(string) $item['typeCode'];
                                                        
                                                        if(isset($item->observation->value)) {
                                                            if($typeCode=='RSON'){
                                                                $refusal_snomedCode=(string) $item->observation->value['code'];
                                                            } else {
                                                                $snomedCode=(string) $item->observation->value['code'];
                                                            }
                                                        }
                                                    }
                                                    $tmpCommnincationArr['refusal_snomedCode']=$refusal_snomedCode;
                                                    $tmpCommnincationArr['snomedCode']=$snomedCode;
                                                }
											}
											$returnArr['ptCommunication'][] = $tmpCommnincationArr;
                                            
                                        }
                                        
										//====================================================================================//
										
										
									}
									
								}
							break;
						}
					}
				}	
			}
			return $returnArr;
		}
		
		//Importing XML 
		function parseXML($xmlArr = array(), $provId = false, $facId = false, $ptId = false, $ptNm = ''){
			$returnArr = array();
			if(count($xmlArr) == 0) return $returnArr['error'] = 'Invalid XML format.';
			if($provId === false) return $returnArr['error'] = 'Please select a provider to continue !';
			if($facId === false) return $returnArr['error'] = 'Please select a facility to continue !';
			if($ptId === false) return $returnArr['error'] = 'Please select a patient to continue !';
			
			$xmlArr = $this->filterXml($xmlArr, $ptId);

			$this->measurecmsid = $xmlArr['cmsid'] ?? NULL;
			if( is_null($this->measurecmsid) )
			{
				return $returnArr['error'] = 'Measure no supported !';
			}

			/**
			 * List value sets for measures
			 */
			$sql = "SELECT `CMS_ID`, `Value_Set_OID`, `Code`, `Description`, `Code_System` FROM `cqm_v8_valueset` WHERE `Expansion_ID`='20190510'";
			$resp = imw_query($sql);

			if( $resp && imw_num_rows($resp) > 0 )
			{
				while( $row = imw_fetch_assoc($resp) )
				{
					$key = $row['CMS_ID'].'_'.$row['Value_Set_OID'].'_'.$row['Code_System'].'_'.$row['Code'];
					$key = strtolower($key);
					$this->valuesets[$key] = $row['Description'];
				}
			}
			
			//Loop will be executed for section in the array i.e -> Encounter, Observations etc
			if(isset($xmlArr['Encounter'])){
                $diagCounter=0;
				foreach($xmlArr['Encounter'] as $obj){
					$encounterDt = array();
					
					//Creating Encounter
					$encounterDt = $this->createEncounter($obj, $provId, $facId, $ptId, $ptNm);
					$returnArr['Encounter'][] = $encounterDt;
					
                    if(isset($obj['encDiagnosis']) && empty($obj['encDiagnosis'])==false && isset($obj['encDiagnosis'][$diagCounter]) && count($obj['encDiagnosis'][$diagCounter]) > 0) {
                        $this->createDiagnosis($obj['encDiagnosis'], $provId, $ptId);
                    }
                    
					//If encounter is created
					if(count($encounterDt) > 0){
						$returnArr['PtData'][] = $this->insertPtData($encounterDt, $xmlArr, $provId, $ptId);
						foreach($returnArr['PtData'] as $tmp)
							$this->setGlobalStatus($tmp);
					}
                    $diagCounter++;
				}
			}
			else
			{
				$encounterDt = array();
				$encounterData = array();
				if( 
					array_key_exists('Observation', $xmlArr) && array_key_exists('start', $xmlArr['Observation'][0])
				)
				{
					$encounterData['effectiveTime']['startObj']['date'] = $xmlArr['Observation'][0]['start']['date'];
					$encounterData['effectiveTime']['startObj']['time'] = $xmlArr['Observation'][0]['start']['time'];
				}
				elseif( 
					array_key_exists('PtPayer', $xmlArr) && array_key_exists('startDt', $xmlArr['PtPayer'][0])
				)
				{
					
					$encounterData['effectiveTime']['startObj']['date'] = $xmlArr['PtPayer'][0]['startDt']['date'];
					$encounterData['effectiveTime']['startObj']['time'] = $xmlArr['PtPayer'][0]['startDt']['time'];
				}

				if( 
					array_key_exists('effectiveTime', $encounterData)
				)
				{
					//Creating Chart note for the patient
					$encounterDt['DosStatus'] = $this->createChartNote($ptId, $provId, $facId, $encounterData);
				}

				$returnArr['PtData'][] = $this->insertPtData($encounterDt, $xmlArr, $provId, $ptId);
			}
			
			$returnArr['globalStatus'] = $this->globalStatus;
			
			return $returnArr;
		}
		
		//Returns Title details from array
		function getTitle($str = ''){
			if(empty($str)) return ;
			
			$returnArr = array();
			
			//Explode String with ':' to get operation and title for it
			$arr = explode(':', $str);
			
			$returnArr['operation'] = (isset($arr[0]) && empty($arr[0]) == false) ? trim($arr[0]) : '';		//Operation details like Encounter performed
			$returnArr['title'] = $returnArr['operation'];			//Operation title like Opthamologic Services
			
			return $returnArr;
		}
		
		
		//Returns Date timestamp Based on XML String or object
		function formatXMLDt($dt = ''){
			if(empty($dt)) return $dt;
			$returnArr = array();
			$YY = $MM = $DD = $HH = $MN = $timeZone = $date = $time = '';
			
			if(is_object($dt)){
				if(isset($dt['value']) && empty($dt['value']) == false && !isset($dt['nullFlavor'])){
					$dt = (string) $dt['value'];
				}
			}
			
			if(is_string($dt)){
				$YY = substr($dt,0,4);
				$MM = substr($dt,4,2);
				$DD = substr($dt,6,2);
				
				$HH = substr($dt,8,2);
				$MN = substr($dt,10,2);
				
				$timeStamp = $YY.'-'.$MM.'-'.$DD.' '.$HH.':'.$MN;
				$dtime = New DateTime($timeStamp);
				$dtime->createFromFormat("y-m-d G:i", $timeStamp);
				$timestamp = $dtime->getTimestamp();
				
				$dateDt =  date('Y-m-d H:i', $timestamp);
				
				$datDT = New DateTime($dateDt);
				$returnArr['date'] = $datDT->format('Y-m-d');
				$returnArr['time'] = $datDT->format('H:i:s');
			}
			return $returnArr;
		}
		
		function getTemplateId($startDate, $startTime, $docId, $facId){
			list($y, $m, $d) = explode("-", $startDate);
			$week = ceil($d/7);
			$intTimeStamp = mktime(0, 0, 0, $m, $d, $y);
			$weekDay = date("N", $intTimeStamp);
			$strQryCheck = "SELECT st.id 
							FROM schedule_templates st 
							INNER JOIN provider_schedule_tmp pst ON st.id = pst.sch_tmp_id 
							WHERE pst.provider = '".$docId."'  
							AND '".$startDate."' >= pst.today_date
							AND pst.del_status = 0
							AND ((pst.status  = 'yes') OR (pst.status  = 'no' AND pst.today_date = '".$startDate."')) 
							AND pst.week$week = '".$weekDay."'  
							AND pst.facility = '".$facId."' ";
			$resQryCheck = imw_query($strQryCheck);
			
			$templateId = 0;
			
			while($arrQryCheck = imw_fetch_array($resQryCheck)){		
				$strQryTemp = "SELECT morning_start_time, morning_end_time FROM schedule_templates WHERE id = '".$arrQryCheck["id"]."'";
				$resQryTemp = imw_query($strQryTemp) or $msg_info[] = imw_error();
				$arrQryTemp = imw_fetch_array($resQryTemp);
		
				if($arrQryCheck["id"] > 0 && strtotime($startTime) >= strtotime($arrQryTemp["morning_start_time"]) && strtotime($startTime) <= strtotime($arrQryTemp["morning_end_time"])){
					$templateId = $arrQryCheck["id"];
					break;
				}
			}
			
			return $templateId;
		}
		
		/* ------------------- ENCONUTER CREATION ------------------- */
		
		//Creating Patients Encounters
		function createEncounter($arrData = array(), $provId = '', $facId = '', $ptId = '', $ptNm = ''){
			
			//Inserting System codes
			$codeArr = (isset($arrData['code']) && count($arrData['code']) > 0) ? $arrData['code'] : false;	
			if($codeArr){
				$this->getSystemCode($codeArr);
			}
			
			//Creating Appointment
			$this->apptCreated = $this->createAppointment($arrData, $provId, $facId, $ptId, $ptNm);
			
			//Creating Chart note for the patient
			$this->dosCreated = $this->createChartNote($ptId, $provId, $facId, $arrData);
			
			//Creating Superbill
			$this->spCreated = $this->createSuperBill($this->apptCreated, $this->dosCreated, $arrData, $provId, $facId, $ptId, $ptNm);
			
			if($this->apptCreated !== false && $this->dosCreated !== false){
				$this->enCreated = true;
			}
			
			$this->setGlobalStatus($this->enCreated);
			
			$returnArr = array();
			$returnArr['ApptStatus'] = $this->apptCreated;
			$returnArr['DosStatus'] = $this->dosCreated;
			$returnArr['SpStatus'] = $this->spCreated;
			$returnArr['EnStatus'] = $this->enCreated;
			
			//Reset all status variables
			$this->procCreated = false;
			$this->apptCreated = false;
			$this->dosCreated = false;
			$this->spCreated = false;
			$this->enCreated = false;
			
			//Finish
			return $returnArr;
		}
		
		//Get system code and their value sets
		function getSystemCode($arrData = array()){
			if(count($arrData) == 0) return ;
			
			//only run the code if its the first call 
			if($this->codeEntered === false){
				$catId = '';
			
				//Check if Other category exists for CPT or not 
				$qry = imw_query('select id from cpt_category_tbl where LOWER(cpt_category) = "other"');
				if(imw_num_rows($qry) > 0){
					$row = imw_fetch_assoc($qry);
					$catId = $row['id'];
				}else{
					//Inserting Other category in CPT category table
					$insertQry = imw_query('INSERT INTO cpt_category_tbl SET cpt_category = "Other"');
					if($insertQry){
						$catId = imw_insert_id();
					}
				}	
				
				//After receiving CPT Category ID
				if(empty($catId) == false){
					
					$valueSet = (isset($arrData['valueSet']) && empty($arrData['valueSet']) == false) ? $arrData['valueSet'] : '';		//Value Set
					$code = (isset($arrData['code']) && empty($arrData['code']) == false) ? $arrData['code'] : '';						//Code
					$description = (isset($arrData['description']) && empty($arrData['description']) == false) ? $arrData['description'] : '';		//Description
					
					//Matching code in the CPT fee table
					$counter = 0;
					$matchCode = imw_query('SELECT cpt_fee_id from cpt_fee_tbl where (cpt4_code = "'.$code.'") OR (cpt_prac_code = "'.$code.'")');
					if(imw_num_rows($matchCode) > 0){
						while($row = imw_fetch_assoc($matchCode)){
							//Updating Code in the matched code
							$updateQry = imw_query('UPDATE cpt_fee_tbl SET valueSet = "'.$valueSet.'" where cpt_fee_id = '.$row['cpt_fee_id'].'');
							if($updateQry) $counter++;
						}
					}else{
						//Insert new code field with valueset
						$insertQry = imw_query('INSERT INTO cpt_fee_tbl SET cpt4_code = "'.$code.'", cpt_prac_code = "'.$code.'", valueSet = "'.$valueSet.'", cpt_desc = "'.$description.'", Status = "Active"') or imw_error();
						if($insertQry) $counter++;
					}
					
					if($counter > 0){
						$this->codeEntered = true;
					}
				}
			}
		}
		
		//Creating Patient Appointment
		function createAppointment($arrData = array(), $provId = false, $facId = false, $ptId = false, $ptNm = ''){
			if(count($arrData) == 0) return false;
			if($provId === false) return false;
			if($facId === false) return false;
			if($ptId === false) return false;
			
			//Variables Needed 
			$procId = $procCode = $proc = $procTime = $procStatus = $apptStDt = $apptEnDt = $apptStTm = $apptEnTm = $isInPt = '';
			
			//Getting Procedure Title
			if(isset($arrData['code'])){
				$proc = (string) $arrData['text'];
				$proc = $this->getTitle($proc);
			}
			//if($proc['title'] != 'Encounter Inpatient'){
				//Setting Appointment Date Time variables
				if(isset($arrData['effectiveTime'])){
					//Start Date Time
					$StartTs = (isset($arrData['effectiveTime']['startObj'])) ? $arrData['effectiveTime']['startObj'] : '';
					
					//End Date Time
					$EndTs = (isset($arrData['effectiveTime']['endObj'])) ? $arrData['effectiveTime']['endObj'] : '';
					
					//Appointment Start Date/Time
					$apptStDt = (isset($StartTs['date']) && empty($StartTs['date']) == false) ? $StartTs['date'] : '';
					$apptStTm = (isset($StartTs['time']) && empty($StartTs['time']) == false) ? $StartTs['time'] : '';
					
					
					//Appointment Start Date/Time
					$apptEnDt = (isset($EndTs['date']) && empty($EndTs['date']) == false) ? $EndTs['date'] : '';
					$apptEnTm = (isset($EndTs['time']) && empty($EndTs['time']) == false) ? $EndTs['time'] : '';
					
					//Calculating Procedure Total time
					$interval  = abs(strtotime($apptEnTm) - strtotime($apptStTm));		//Getting procedure duration (minutes)
					$procTime   = round($interval / 60);	//Time gets multiplied by 60 as time is based on how many minutes, procedure will take
				}
				$procStatus = $this->manageApptProc($proc['title'], $procTime);
				
				//In Patient Status
				$isInPt = (isset($arrData['isInPt']) && empty($arrData['isInPt']) == false) ? $arrData['isInPt'] : '';
				
				//If procedure is created, than continue to appointments
				if($procStatus !== false){
					$this->procCreated = true;
					
					//Appointment Fields array
					$arrApptFields = array();
					
					$arrApptFields['sa_aid'] = 0;
					$arrApptFields['sa_doctor_id'] = $provId;
					$arrApptFields['sa_test_id'] = 0;
					$arrApptFields['sa_patient_id'] = $ptId;
					$arrApptFields['sa_patient_name'] = $ptNm;
					$arrApptFields['sa_patient_app_status_id'] = 0;
					$arrApptFields['status_date'] = "0000-00-00";
					$arrApptFields['sa_app_time'] = date("Y-m-d H:i:s");
					$arrApptFields['sa_app_starttime'] = $apptStTm;
					$arrApptFields['sa_app_endtime'] = $apptEnTm;
					$arrApptFields['sa_app_duration'] = $procTime;
					$arrApptFields['sa_facility_id'] = $facId;
					$arrApptFields['sa_app_start_date'] = $apptStDt;
					$arrApptFields['sa_app_end_date'] = $apptEnDt;
					$arrApptFields['procedureid'] = $procStatus;
					$arrApptFields['sa_comments'] = addslashes($proc['title']);
					$arrApptFields['sch_template_id'] = $this->getTemplateId($apptStDt, $apptStTm, $provId, $facId);
					
					$arrProvider = $this->get_provider_ar($this->operatorId);
					$arrApptFields['sa_madeby'] = addslashes($arrProvider[$this->operatorId]);
					$arrApptFields['status_update_operator_id'] = $this->operatorId;
					$arrApptFields['is_inpatient'] = $isInPt;
					
					$strQry = '';
					foreach ($arrApptFields as $key => $val){
						$strQry .= " $key = '".$val."', ";
					}
					$strQry = substr($strQry,0,-2);
					
					//Checking If any appointment exist or not
					$checkAppointment = imw_query("SELECT id, sa_app_start_date, sa_app_starttime FROM schedule_appointments WHERE 
						  sa_doctor_id = '".$provId."' AND 
						  sa_app_start_date = '".$apptStDt."' AND sa_app_end_date = '".$apptEnDt."' AND 
						  sa_app_starttime = '".$apptStTm."' AND sa_app_endtime = '".$apptEnTm."' AND 
						  sa_patient_id = '".$ptId."' AND 
						  sa_patient_app_status_id NOT IN ('18') ORDER BY id DESC LIMIT 0,1");
					
					$apptCounter = 0;
					$appointmentID = '';
					if(imw_num_rows($checkAppointment) > 0){
						//Updating matched appointment
						$apptDetails = imw_fetch_assoc($checkAppointment);
						$strQry = "UPDATE schedule_appointments SET ".$strQry;						
						$strQry .= " WHERE id = '".$apptDetails['id']."' AND sa_patient_app_status_id NOT IN ('18')";		
						$updateAppt = imw_query($strQry);
						if($updateAppt){
							$appointmentID = $apptDetails['id'];
							$apptCounter++;
						}
					}else{
						$insertQry = imw_query("INSERT INTO schedule_appointments SET ".$strQry) or imw_error();					
						if($insertQry){
							$appointmentID = imw_insert_id();
							$apptCounter++;
						}
					}
					
					//Inseting Into inPatient table
					$inPaitentStatus = $this->insertingPatient($arrData['inPtArr'], $appointmentID);
					
					if($apptCounter > 0){
						return array('apptId' => $appointmentID, 'procId' => $procStatus, 'procName' => $proc);
					}else{
						return false;
					}
					
				}else{
					$this->procCreated = false;
					return false;
				}
			//}
			
		}
		
		//Manage Appointment related procedures
		function manageApptProc($proc = '', $procTime = ''){
			if(empty($proc)) return false;
			$procId = '';
			$timeArr = $usrArr = array();
			
			//User group Arr
			$getUsrGroup = imw_query('select id FROM user_groups where status = 1');
			if(imw_num_rows($getUsrGroup) > 0){
				while($rowUsrGrp = imw_fetch_assoc($getUsrGroup)){
					$usrArr[] = $rowUsrGrp['id'];
				}
			}
			
			//Usr Group Id's String 
			$usrGrpStr = implode(',',$usrArr); 
			
			//Available Time slots for procedures (in minutes)
			$checkTime = imw_query('SELECT id,times from slot_procedures where lower(proc) = "" && times != ""');
			if(imw_num_rows($checkTime) > 0){
				while($row1 = imw_fetch_assoc($checkTime)){
					$timeArr[$row1['times']] = $row1['id'];
				}
			}
			
			//If time period exits in timeArr
			if(isset($timeArr[$procTime]) && empty($timeArr[$procTime]) == false) $procTime = $timeArr[$procTime];
			else{
				//Inserting new time slot for procedure
				$insertTmSlot = imw_query('INSERT INTO slot_procedures SET proc = "", times = "'.$procTime.'", active_status = "yes", user_group = "'.$usrGrpStr.'"') or imw_error();;
				if($insertTmSlot) $procTime = imw_insert_id();
			}
			
			
			//Matching Procedures from DB
			$matchProc = imw_query('SELECT id FROM slot_procedures WHERE lower(proc) = "'.addslashes(strtolower($proc)).'" AND doctor_id = 0');
			if(imw_num_rows($matchProc) > 0){
				$row = imw_fetch_assoc($matchProc);
				$procId = $row['id'];
			}else{
				//Inserting New Procedures
				//Procedure Acronym
				$procNmArr = preg_split("/\s+/", strtoupper($proc));
				foreach($procNmArr as $word){
					$procAcr .= substr($word,0,1);
				}
				
				//Procedure Color
				$procColor = "#".strtoupper(dechex(rand(0,10000000)));
				
				
				//Check if procedure for same time exists
				// --> If procedure with same time exits than procedureId value will be replaced with the id of the matched procedure
				$procedureTimeId = '';
				$matchtimeProc = imw_query('SELECT id from slot_procedures WHERE proc_time = '.$procTime.' AND doctor_id = 0');
				if(imw_num_rows($matchtimeProc) > 0){
					$rowTime = imw_fetch_assoc($matchtimeProc);
					$procedureTimeId = $rowTime['id'];
				}
				
				$insertProc = imw_query('INSERT INTO slot_procedures SET proc = "'.addslashes(strtolower($proc)).'", times = "", proc_time = "'.$procTime.'", acronym = "'.$procAcr.'", proc_color = "'.$procColor.'", doctor_id = 0, procedureId = "'.$procedureTimeId.'", active_status = "yes", user_group = "'.$usrGrpStr.'"') or imw_error();
				
				if($insertProc){
					$procId = imw_insert_id();
				}
			}
			
			return $procId;
		}
		
		//Creating Chart Note
		function createChartNote($ptId = '', $provId = '', $facId = '', $arrData = array()){
			if(empty($ptId) || empty($provId) || empty($facId) || count($arrData) == 0) return false;

			//Variables Needed
			$EncStDt = $EncStTm = $EncEnDt = $EncEnTm = '';
			
			//Getting HQ facility details
			
			/* $qryFac = imw_query('SELECT encounterId FROM facility where facility_type = 1 LIMIT 0,1');
			if(imw_num_rows($qryFac) > 0){
				$row = imw_fetch_assoc($qryFac);
				$facEncounter = $row['encounterId'];
			} */
			
			//Getting Date/Time from Array
			if(isset($arrData['effectiveTime'])){
				//Start Date Time
				$StartTs = (isset($arrData['effectiveTime']['startObj'])) ? $arrData['effectiveTime']['startObj'] : '';
				
				//End Date Time
				$EndTs = (isset($arrData['effectiveTime']['endObj'])) ? $arrData['effectiveTime']['endObj'] : '';
				
				//Appointment Start Date/Time
				$EncStDt = (isset($StartTs['date']) && empty($StartTs['date']) == false) ? $StartTs['date'] : '';
				$EncStTm = (isset($StartTs['time']) && empty($StartTs['time']) == false) ? $StartTs['time'] : '';
				
				//Appointment Start Date/Time
				$EncEnDt = (isset($EndTs['date']) && empty($EndTs['date']) == false) ? $EndTs['date'] : '';
				$EncEnTm = (isset($EndTs['time']) && empty($EndTs['time']) == false) ? $EndTs['time'] : '';
			}
			
				$checkDos = imw_query('SELECT id, date_of_service, encounterId FROM chart_master_table where date_of_service = "'.$EncStDt.'" AND time_of_service = "'.$EncStTm.'" AND patient_id = "'.$ptId.'" AND delete_status = 0');
				if(imw_num_rows($checkDos) > 0){
					$row = imw_fetch_assoc($checkDos);
				return array('formId' => $row['id'], 'dateOfService' => $row['date_of_service'], 'encounterFac' => $row['encounterId']);
				}else{
					$facEncounter = $this->getFacEncounter();
					$qry = 'INSERT INTO chart_master_table SET patient_id = "'.$ptId.'", providerId = "'.$provId.'", encounterId = "'.$facEncounter.'", create_dt = "'.date('Y-m-d H:i:s').'", create_by = "'.$this->operatorId.'", date_of_service = "'.$EncStDt.'", time_of_service = "'.$EncStTm.'", finalize = 0, releaseNumber = 1';
					$insertDos = imw_query($qry) or imw_error();
					
					if($insertDos){
						return array('formId' => imw_insert_id(), 'dateOfService' => $EncStDt, 'encounterFac' => $facEncounter);
					}else{
						return false;
					}
				}
			}
		
		//Creating Chart Note
		function createSuperBill($apptArr = array(), $cNArr = array(), $arrData = array(), $provId = '', $facId = '', $ptId = '', $ptNm = ''){
			if(count($cNArr) == 0 || count($arrData) == 0 || empty($ptId) || empty($provId)) return false;
			
			if(isset($cNArr['dateOfService']) && empty($cNArr['dateOfService']) == false){
				//Variables Needed
				$cptCode = $superBillId = '';
				$fieldsArr = array();
				
				//Fields needed in Superbill
				$fieldsArr['physicianId'] = $provId;
				$fieldsArr['patientId'] = $ptId;
				$fieldsArr['encounterId'] = (isset($cNArr['encounterFac']) && empty($cNArr['encounterFac']) == false) ? $cNArr['encounterFac'] : '';
				$fieldsArr['formId'] = (isset($cNArr['formId']) && empty($cNArr['formId']) == false) ? $cNArr['formId'] : '';
				$fieldsArr['timeSuperBill'] = date('H:i:s');
				$fieldsArr['dateOfService'] = (isset($cNArr['dateOfService']) && empty($cNArr['dateOfService']) == false) ? $cNArr['dateOfService'] : '';
				$fieldsArr['patientStatus'] = 'Active';
				$fieldsArr['procOrder'] = (isset($apptArr['procId']) && empty($apptArr['procId']) == false) ? $apptArr['procId'] : '';
				$fieldsArr['sch_app_id'] = (isset($apptArr['apptId']) && empty($apptArr['apptId']) == false) ? $apptArr['apptId'] : '';
				$fieldsArr['primary_provider_id_for_reports'] = $provId;
				$fieldsArr['gro_id'] = 0;
				
				
				//Checking if superbill exists or not
				$qry = 'SELECT idSuperBill from superbill WHERE patientId = "'.$fieldsArr['patientId'].'" AND dateOfService = "'.$fieldsArr['dateOfService'].'" AND patientStatus = "Active" AND formId = "'.$fieldsArr['formId'].'"';
				$checkSp = imw_query($qry) or imw_error($qry);
				
				//Return superBill Id if found
				if(imw_num_rows($checkSp) > 0){
					$rowSp = imw_fetch_assoc($checkSp);
					$superBillId = $rowSp['idSuperBill'];
				}else{
					//Create New Superbill
					$superBillId = AddRecords($fieldsArr,'superbill');
				}
				
				//After creating superbill
				if(empty($superBillId) == false){
					$this->spCreated = true;
					//If code exists, enter it in Procedure info table
					if(isset($arrData['code'])){
						$cptCode = (isset($arrData['code']['code'])) ? $arrData['code']['code'] : '';
					}
					//Check code in procedure info table if exists or not
					$checkPro = imw_query('SELECT id,idSuperBill,cptCode FROM procedureinfo WHERE cptCode = "'.$cptCode.'" AND idSuperBill = "'.$superBillId.'"');
					if(imw_num_rows($checkPro) > 0){
						//Return superbill related data
						$rowProcSp = imw_fetch_assoc($checkPro);
						$this->superBillProcCreated = true;
						return array('procId' => $rowProcSp['id'], 'superBillId' => $rowProcSp['idSuperBill'], 'cptCode' => $rowProcSp['cptCode']);
					}else{
						//Insert code into procedure info table
						$procedureName = (isset($apptArr['procName']['title']) && empty($apptArr['procName']['title']) == false) ? $apptArr['procName']['title'] : '';
						
						$description = '';
						if(isset($apptArr['procName']['operation']) && empty($apptArr['procName']['operation']) == false && empty($procedureName) == false){
							$description = $apptArr['procName']['operation'].' : '.$procedureName;
						}
						
						$insertProcQry = imw_query('INSERT INTO procedureinfo SET cptCode = "'.$cptCode.'", description = "'.$description.'", procedureName = "'.$procedureName.'", units = 1, porder = 1, idSuperBill = "'.$superBillId.'"');
						
						if($insertProcQry){
							$this->superBillProcCreated = true;
							return array('procId' => imw_insert_id(), 'superBillId' => $superBillId, 'cptCode' => $cptCode);
						}else{
							$this->superBillProcCreated = false;
							return false;
						}
					}
				}else{
					$this->spCreated = false;
					return false;
				}
			}else{
				$this->spCreated = false;	
				return false;
			}
		}
		
		/* ------------------- PATIENT DATA INSERTION ------------------- */
		
		//Inserting Patient Data into our DB
		
		//Inserting Medications
		function createMedications($array_medication_XML = array(),$provId = '',$ptId = ''){
			if(count($array_medication_XML) ==0 || empty($ptId)) return false;
			
			$med_name = $med_name_explode = $medication_comments = $med_status = $med_start_date = $med_end_date = $med_sig = $med_dosage = $medRxNorm= "";
			$return_cnt = 0;
			
			foreach($array_medication_XML as $arr_medication_object){
				$med_name_explode = explode(":",$arr_medication_object['Name']);
				if(trim(end($med_name_explode))){
					$med_name=trim(end($med_name_explode));
				}
				$medication_comments=$arr_medication_object['Comments'];
				$med_status=$arr_medication_object['Status'];
				
				$med_start_date = $arr_medication_object['Time']['StartTs'];
				$med_end_date=$arr_medication_object['Time']['EndTs'];
				
				$med_start_time = $arr_medication_object['Time']['StartTime'];
				$med_end_time = $arr_medication_object['Time']['EndTime'];
				
				$med_dosage=$arr_medication_object['Time']['Dose'];
				$med_sig=$arr_medication_object['Time']['Sig'];
				$medRxNorm=$arr_medication_object['RxNorm'];
				
				$refusal = $arr_medication_object['refusal'];
				$refusalCode = $arr_medication_object['refusalCode'];
				
				//$medStatus = (empty($med_dosage) == false) ? 'Active' : 'Order';
                $medStatus = (strtolower($med_status)=='active')?'Active':'Order';
				
				if($med_name){
					$qry_intial="INSERT INTO "; $whr="";
					$qry_med='SELECT id FROM lists where type= "1" AND pid="'.$ptId.'" AND title="'.$med_name.'" AND begdate="'.$med_start_date.'"  ';
					$res_med = imw_query($qry_med);
					if(imw_num_rows($res_med)> 0){
						$row_med = imw_fetch_assoc($res_med);
						$med_id  = $row_med['id'];
						$qry_intial="UPDATE ";
						$whr=" WHERE id='".$med_id."' ";
					}
					if($qry_intial){
						$qry_action_med=$qry_intial.' lists SET type= "1", user="'.$provId.'", pid="'.$ptId.'", title="'.$med_name.'", begdate="'.$med_start_date.'", enddate="'.$med_end_date.'", comments="'.$medication_comments.'",sig="'.$med_sig.'",destination="'.$med_dosage.'",ccda_code="'.$medRxNorm.'",allergy_status="'.$medStatus.'",compliant="2",sites="0",timestamp="'.date("Y-m-d H:i:s").'", refusal = "'.$refusal.'", refusal_snomed = "'.$refusalCode.'", begtime = "'.$med_start_time.'", endtime = "'.$med_end_time.'" '.$whr;
						$res_action_med=imw_query($qry_action_med);
						if($res_action_med){
							$return_cnt++;
						}
					}
				}
			}
			$return = true;
			if($return_cnt==0){$return = false;}
			return $return;
		}
		
        //Creating Referral Loop
		function createReferralLoop($obj = array(), $provId = '', $ptId = '', $description='', $enArr=array() ){
            
            $dos = (isset($enArr['DosStatus']['dateOfService']) && empty($enArr['DosStatus']['dateOfService']) == false) ? $enArr['DosStatus']['dateOfService'] : '';
            
			if(empty($ptId) || empty($provId)  || count($obj) == 0 || $obj['start_date']['date']!=$dos) return false;

            $form_id = (isset($enArr['DosStatus']['formId']) && empty($enArr['DosStatus']['formId']) == false) ? $enArr['DosStatus']['formId'] : '';
            
            
            /** Check if Intervention already exists */
            $sql = 'select `id` FROM `chart_assessment_plans`
                    WHERE `form_id`="'.$form_id.'" AND 
                    `patient_id`="'.$ptId.'" AND
                    `refer_to_code` = "'.imw_real_escape_string($description).'"';

            $resp = imw_query($sql);
            if( imw_num_rows($resp) > 0 )
            {
                $row = imw_fetch_assoc($resp);

                $sql = 'update `chart_assessment_plans` 
                        SET `refer_to_code` = "'.imw_real_escape_string($description).'"
                        WHERE `form_id`="'.$form_id.'" AND 
                        `patient_id`="'.$ptId.'" ';

                $insertDos = imw_query($sql) or imw_error();
                $counter++;
            }
            else
            {
                $sql = 'insert into `chart_assessment_plans` 
                        SET `refer_to_code` = "'.imw_real_escape_string($description).'",
                        `form_id`="'.$form_id.'",
                        `exam_date`="'.$dos.'",
                        `patient_id`="'.$ptId.'" ';

                $insertDos = imw_query($sql) or imw_error();

                if($insertDos)
                {
                    $counter++;
                }
            }	
			
			$returnVal = ($counter > 0) ? true : false;
			return $returnVal;
		}
		
		//Creating Interventions
		function createInterventions($arrData = array(), $provId = '', $ptId = '', $enArr=array()){
			if(empty($ptId) || empty($provId)  || count($arrData) == 0) return false;

			$currentMeasureId = strtolower($this->measurecmsid);
            $ar=array();
			if($currentMeasureId == 'cms50v8') {
                $sql = "SELECT `Code`, `Description`
                    FROM `cqm_v8_valueset`
                    WHERE `Value_Set_OID` = '2.16.840.1.113883.3.464.1003.101.12.1046'
                    AND `CMS_ID` = 'CMS50v8'
                    AND `Code_System` = 'SNOMEDCT'";
                $resp = imw_query($sql);
                if( $resp && imw_num_rows($resp) > 0 )
                {
                    while( $row = imw_fetch_assoc($resp) )
                    {
                        $ar[$row['Code']]=$row['Description'].' -('.$row['Code'].')';
                    }
                }
            }
            
			$cur_dat=date('Y-m-d H:i:s');
			$counter = 0;
			
			foreach($arrData as $obj)
			{
                $snomedCode = $obj['snomedct']['code'];
                
                if(array_key_exists($snomedCode, $ar)) {
                    $description=$ar[$snomedCode];
                    $this->createReferralLoop($obj, $provId, $ptId, $description, $enArr);
                    $counter++;
                    continue;
                }
                
				// Encounter Start Date/Time
				$EncStDt = $obj['start_date']['date'] ?? '';
				$EncStTm = $obj['start_date']['time'] ?? '';
				
				//Encounter End Date/Time
				$EncEnDt = $obj['end_date']['date'] ?? '';
				$EncEnTm = $obj['end_date']['time'] ?? '';


				/** Pull the data from the XML object and get the Code Description/Procedure name from the latest value set definition */
				$description = '';

				/** Possbile Code Sytem names */
				$codesystems = [
					'snomedct' => '',
					'cpt' => 'CPT-CM'
				];

				$tempCodesystems = array_keys($codesystems);

				/** First search based on Primary code */
				$codeSystemName = $obj['primaryCodeSystem'];

				do
				{
					$codeSystemName = strtolower($codeSystemName);

					$code = strtolower($obj[$codeSystemName]['code']);
					$codeSystem = strtolower($obj[$codeSystemName]['codeSystem']);

					$sqlName = 'SELECT `Value_Set_Name` FROM `cqm_v8_valueset`
								WHERE
								LOWER(`Code`) = "'.imw_real_escape_string($code).'" AND 
								LOWER(`Code_System`) = "'.imw_real_escape_string($codeSystemName).'" AND
								LOWER(`Code_System_OID`) = "'.imw_real_escape_string($codeSystem).'" AND
								LOWER(`CMS_ID`) = "'.imw_real_escape_string($currentMeasureId).'"';
					
					$respName = imw_query($sqlName);

					if( $respName && imw_num_rows($respName) > 0 )
					{
						$respName = imw_fetch_assoc($respName);
						$description = trim($respName['Value_Set_Name']);
					}

					unset($tempCodesystems[$codeSystemName]);
					$codeSystemName = array_shift($tempCodesystems);
				}
				while( empty($description) && count($tempCodesystems) > 0 );

				/** Append ICD-10 and ICD-9 codes to the problem description */
				$descCodes = '';
				if( array_key_exists('code', $obj['cpt']) && !empty($obj['cpt']['code']) )
				{
					$descCodes .= 'CPT-CM '.$obj['cpt']['code'];
				}

				$descCodes = trim($descCodes);
				$descCodes = ( !empty($descCodes) ) ? '('.$descCodes.')' : '';

                //if empty Description 
				$description = (empty($description) && isset($obj['description']) && isset($obj['description']['title']) && empty($obj['description']['title']) == false) ? trim($obj['description']['title']) : $description;
                
				/** Final Problem Descriptoin */
				$description = $description.' '.$descCodes;
				$description = trim($description);
				
				//Refusal Reason
				$refusal  = $obj['is_refusal'] ?? '';
				$refusalCode = $obj['refusal_code'] ?? '';

				//Translation Code
				$translationCode = (isset($obj['translation_code']) && empty($obj['translation_code']) == false) ? $obj['translation_code'] : '';
                
				
				
				if(empty($refusal)){
					$refusalCode = '';
					$refusal = 0;
				}
			

				/** Check if Procedure/Intervention already exists */
				$sql = 'SELECT id FROM lists 
						WHERE 
							`proc_type` = "'.imw_real_escape_string($obj['type']).'" AND
							`title` = "'.imw_real_escape_string($description).'" AND
							`begdate` = "'.$EncStDt.'" AND
							`begtime` = "'.$EncStTm.'" AND
							`pid` = "'.$ptId.'" AND
							`allergy_status` = "Active" AND
							`ccda_code` = "'.imw_real_escape_string($snomedCode).'"';

				$resp = imw_query($sql);
				if( imw_num_rows($resp) > 0 )
				{
					$row = imw_fetch_assoc($resp);
					$list_id = $row['id'];
					$counter++;
				}
				else
				{
					$sql = 'INSERT INTO lists 
							SET
								`date` = "'.imw_real_escape_string($cur_dat).'",
								`type` = "5",
								`title` = "'.imw_real_escape_string($description).'",
								`begdate` = "'.imw_real_escape_string($EncStDt).'",
								`begtime` = "'.imw_real_escape_string($EncStTm).'",
								`enddate` = "'.imw_real_escape_string($EncEnDt).'",
								`endtime` = "'.imw_real_escape_string($EncEnTm).'",
								`pid` = "'.imw_real_escape_string($ptId).'",
								`allergy_status` = "Active",
								`proc_type` = "'.imw_real_escape_string($obj['type']).'",
								`ccda_code` = "'.imw_real_escape_string($snomedCode).'",
								`procedure_status` = "'.imw_real_escape_string($obj['status']).'",
								`refusal` = "'.imw_real_escape_string($refusal).'",
								`refusal_snomed` = "'.imw_real_escape_string($refusalCode).'"';

					$insertDos = imw_query($sql) or imw_error();
					
					if($insertDos)
					{
						$counter++;
					}
				}	
			}
			$returnVal = ($counter > 0) ? true : false;
			return $returnVal;
		}
		
		//Creating Implantable devices
		function createImplantable($arrData = array(), $provId = '', $ptId = ''){
			if(empty($ptId) || empty($provId)  || count($arrData) == 0) return false;

			$currentMeasureId = strtolower($this->measurecmsid);
			
			$cur_dat=date('Y-m-d H:i:s');
			$counter = 0;
			
			foreach($arrData as $obj)
			{
				// Implant Start Date/Time
				$EncStDt = $obj['start_date']['date'] ?? '';
				$EncStTm = $obj['start_date']['time'] ?? '';
				
				//Implant End Date/Time
				$EncEnDt = $obj['end_date']['date'] ?? '';
				$EncEnTm = $obj['end_date']['time'] ?? '';


				/** Pull the data from the XML object and get the Code Description/Procedure name from the latest value set definition */
				$description = '';
                $snomedCode = '';
				/** Possible Code System names */
				$codesystems = [
					'hcpcs' => ''
				];

                $hcpcsCode = $obj['hcpcs']['code'];
                
				$tempCodesystems = array_keys($codesystems);

				/** First search based on Primary code */
				$codeSystemName = $obj['primaryCodeSystem'];

				do
				{
					$codeSystemName = strtolower($codeSystemName);

					$code = strtolower($obj[$codeSystemName]['code']);
					$codeSystem = strtolower($obj[$codeSystemName]['codeSystem']);

					$sqlName = 'SELECT `Value_Set_Name` FROM `cqm_v8_valueset`
								WHERE
								LOWER(`Code`) = "'.imw_real_escape_string($code).'" AND 
								LOWER(`Code_System`) = "'.imw_real_escape_string($codeSystemName).'" AND
								LOWER(`Code_System_OID`) = "'.imw_real_escape_string($codeSystem).'" AND
								LOWER(`CMS_ID`) = "'.imw_real_escape_string($currentMeasureId).'"';
					
					$respName = imw_query($sqlName);

					if( $respName && imw_num_rows($respName) > 0 )
					{
						$respName = imw_fetch_assoc($respName);
						$description = trim($respName['Value_Set_Name']);
					}

					unset($tempCodesystems[$codeSystemName]);
					$codeSystemName = array_shift($tempCodesystems);
				}
				while( empty($description) && count($tempCodesystems) > 0 );

                $descIcd = '';
				if( array_key_exists('code', $obj['hcpcs']) && !empty($obj['hcpcs']['code']) )
				{
					$descIcd =$obj['hcpcs']['code'];
				}

				$descIcd = trim($descIcd);
				$descIcd = ( !empty($descIcd) ) ? '-('.$descIcd.')' : '';

				/** Final IMplantable device Description */
				$description = trim($description);
				$description = $description.' '.$descIcd;

				//Refusal Reason
				$resusal  = $obj['is_refusal'] ?? '';
				$refusalCode = $obj['refusal_code'] ?? '';
				if(empty($refusal)){
					$refusalCode = '';
					$refusal = 0;
				}
                
                $status = $obj['status'] ?? '';
			

				/** Check if Implantable deivce already exists */
				$sql = 'SELECT id FROM lists 
						WHERE 
							`type` = "9" AND
							`comments` = "'.imw_real_escape_string($description).'" AND
							`begdate` = "'.$EncStDt.'" AND
							`begtime` = "'.$EncStTm.'" AND
							`pid` = "'.$ptId.'" AND
							`allergy_status` = "Active" AND
							`implant_status` = "'.imw_real_escape_string($status).'" ';

				$resp = imw_query($sql);
				if( imw_num_rows($resp) > 0 )
				{
					$row = imw_fetch_assoc($resp);
					$list_id = $row['id'];
					$counter++;
				}
				else
				{
					$sql = 'INSERT INTO lists 
							SET
								`date` = "'.imw_real_escape_string($cur_dat).'",
								`type` = "9",
								`comments` = "'.imw_real_escape_string($description).'",
								`begdate` = "'.imw_real_escape_string($EncStDt).'",
								`begtime` = "'.imw_real_escape_string($EncStTm).'",
								`enddate` = "'.imw_real_escape_string($EncEnDt).'",
								`endtime` = "'.imw_real_escape_string($EncEnTm).'",
								`pid` = "'.imw_real_escape_string($ptId).'",
								`allergy_status` = "Active",
								`ccda_code` = "'.imw_real_escape_string($snomedCode).'",
								`implant_status` = "'.imw_real_escape_string($status).'",
								`refusal` = "'.imw_real_escape_string($refusal).'",
								`refusal_snomed` = "'.imw_real_escape_string($refusalCode).'"';

					$insertDos = imw_query($sql) or imw_error();
					
					if($insertDos)
					{
						$counter++;
					}
				}	
			}
			$returnVal = ($counter > 0) ? true : false;
			return $returnVal;
		}
		
		function createVitalSign($arrVitalSign,$provId,$ptId){
			if(!$ptId){return false;}
			
			$chk_counter="0";
			$current_date=date("Y-m-d H:i:s");
			
			//Vital Sign unit array
			$vsUnitArr = array();
			$arrQry = imw_query('SELECT id,vital_sign_unit FROM vital_sign_limits') or imw_error();
			if(imw_num_rows($arrQry) > 0){
				while($unitRow = imw_fetch_assoc($arrQry)){
					$vsUnitArr[$unitRow['id']] = $unitRow['vital_sign_unit'];
				}
			}
			
			foreach($arrVitalSign as $vitalFieldValue){
				$authorDate=$authorTime=$date_of_vital=$time_of_vital=$BP_systolic_value=$BP_diastolic_value="";
				
				$date_of_vital = (isset($vitalFieldValue["date_vital"]['date']) && empty($vitalFieldValue["date_vital"]['date']) == false) ? $vitalFieldValue["date_vital"]['date'] : '';
				$time_of_vital = (isset($vitalFieldValue["date_vital"]['time']) && empty($vitalFieldValue["date_vital"]['time']) == false) ? $vitalFieldValue["date_vital"]['time'] : '';
                
				$authorDate = (isset($vitalFieldValue["authorTime"]['date']) && empty($vitalFieldValue["authorTime"]['date']) == false) ? $vitalFieldValue["authorTime"]['date'] : '';
				$authorTime = (isset($vitalFieldValue["authorTime"]['time']) && empty($vitalFieldValue["authorTime"]['time']) == false) ? $vitalFieldValue["authorTime"]['time'] : '';
				$phy_reviewed_date='0000-00-00 00:00:00';
                $phy_reviewed=0;
                if($authorDate!='' && $authorTime!='') {
                    $phy_reviewed_date=$authorDate.' '.$authorTime;
                    $phy_reviewed=$provId;
                }
				$range_vital=$vitalFieldValue["range_vital"];
				$vital_unit=$vitalFieldValue["unit"];
				
				//$vital_unit = str_replace(array('[', ']'), array('', ''), $vital_unit);
				//$unitKey = array_search($vital_unit, $vsUnitArr);
				//$vital_sign_id = (isset($vsUnitArr[$unitKey]) && empty($vsUnitArr[$unitKey]) == false) ? $unitKey : '';
				
				$vitalSignType = $vitalFieldValue["type"]['title'];
				
				$vital_sign_id = "";
				if(strtolower($vitalSignType) == "systolic blood pressure"){
					$BP_systolic_value=$range_vital;
					$vital_sign_id = 1;
				}else if(strtolower($vitalSignType) == "diastolic blood pressure"){
					$BP_diastolic_value=$range_vital;
					$vital_sign_id = 2;
				}
				$qryVitalSignMaster="SELECT id FROM vital_sign_master WHERE patient_id='".$ptId."' AND date_vital='".$date_of_vital."'";
				$resVitalSignMaster=imw_query($qryVitalSignMaster);
				if(imw_num_rows($resVitalSignMaster)>0){
					$rowVitalSignMaster= imw_fetch_assoc($resVitalSignMaster);
					$vitalMasterId=$rowVitalSignMaster["id"];
				}else if(imw_num_rows($resVitalSignMaster)==0){
					$qryInsertVitalSignMaster="INSERT INTO vital_sign_master set patient_id='".$ptId."', date_vital='".$date_of_vital."', time_vital='".$time_of_vital."', created_on='".$current_date."', created_by='".$provId."', phy_reviewed ='".$phy_reviewed."', phy_reviewed_date='".$phy_reviewed_date."', timestamp='".$current_date."'";
					$resInsertVitalSignMaster=imw_query($qryInsertVitalSignMaster);
					$vitalMasterId=imw_insert_id();
				}
				if($vitalMasterId){
					if($range_vital){
						$qryVitalSignPatient="SELECT id FROM vital_sign_patient WHERE vital_master_id='".$vitalMasterId."' AND vital_sign_id='".$vital_sign_id."' AND range_vital='".$range_vital."'";
						$resVitalSignPatient=imw_query($qryVitalSignPatient);
						$qry_init="INSERT INTO ";$whr_cond="";
						if(imw_num_rows($resVitalSignPatient)>0){
							$rowVitalSignPatient=imw_fetch_assoc($resVitalSignPatient);
							$id=$rowVitalSignPatient["id"];
							$qry_init="UPDATE ";	
							$whr_cond="where id='".$id."'";
							$chk_counter++;
						}
						if($qry_init){
							$qryInsertVitalSignPatient=$qry_init." vital_sign_patient set vital_master_id='".$vitalMasterId."', vital_sign_id='".$vital_sign_id."', range_vital='".$range_vital."', unit='".$vital_unit."', inhale_O2 = '".strtotime($time_of_vital)."' ".$whr_cond;
							$resInsertVitalSignPatient = imw_query($qryInsertVitalSignPatient);
							if($resInsertVitalSignPatient){
								$chk_counter++;
							}
							//echo $qryInsertVitalSignPatient;echo "<br>";
						}
					}
				} 
			}
			$return_status=false;
			if($chk_counter>0){
				$return_status=true;
			}
			return $return_status;
		}
		
		//Creating Interventions
		function createRadiology($arrData = array(), $provId = '', $ptId = '',$enArr = array()){
			
			foreach($arrData as $obj){
				
				if(strtolower($obj['testStatus'])=='completed')$rad_status=2;
				else $rad_status=1;
				
				//add condition to check where
				if($obj['snowmedCode']){
					$where="AND snowmedCode='". imw_real_escape_string($obj['snowmedCode']) ."'";
				}elseif($obj['refusal_snomed']){
					$where="AND refusal_snomed='". imw_real_escape_string($obj['refusal_snomed']) ."'";
				}
				//check is data already exist
				$ifExist=imw_query("select rad_test_data_id from rad_test_data where
					rad_patient_id='$ptId' 
					AND rad_name='". imw_real_escape_string($obj['testName']) ."'
					AND rad_order_date='$obj[orderDate]'
					AND rad_order_time='$obj[orderTime]'
					AND rad_results_date='$obj[resultDate]'
					AND rad_results_time='$obj[resultTime]'
					$where
					");
				
				if(imw_num_rows($ifExist)>0)
				{
					$existingID=0;
					$res=imw_fetch_object($ifExist);
					$existingID=$res->rad_test_data_id;
					//update existing
					imw_query("update rad_test_data set rad_name='". imw_real_escape_string($obj['testName']) ."',
					rad_patient_id='$ptId',
					rad_order_date='$obj[orderDate]',
					rad_order_time='$obj[orderTime]',
					rad_results_date='$obj[resultDate]',
					rad_results_time='$obj[resultTime]',
					rad_loinc='$obj[loinc]',
					rad_results='". imw_real_escape_string($obj['result']) ."',
					snowmedCode='". imw_real_escape_string($obj['snowmedCode']) ."',
					rad_status=$rad_status, 
					refusal=$obj[refusal],
					refusal_reason='". imw_real_escape_string($obj['refusal_reason']) ."',
					refusal_snomed='". imw_real_escape_string($obj['refusal_snomed']) ."'
					
					where rad_test_data_id=$existingID");
					$counter++;
				}
				else
				{
					//add new
					imw_query("insert into rad_test_data set rad_name='". imw_real_escape_string($obj['testName']) ."',
					rad_patient_id='$ptId',
					rad_order_date='$obj[orderDate]',
					rad_order_time='$obj[orderTime]',
					rad_results_date='$obj[resultDate]',
					rad_results_time='$obj[resultTime]',
					rad_loinc='$obj[loinc]',
					rad_results='". imw_real_escape_string($obj['result']) ."',
					snowmedCode='". imw_real_escape_string($obj['snowmedCode']) ."',
					rad_status=$rad_status, 
					refusal=$obj[refusal],
					refusal_reason='". imw_real_escape_string($obj['refusal_reason']) ."',
					refusal_snomed='". imw_real_escape_string($obj['refusal_snomed']) ."'");
					$counter++;
				}
			}
			
			$returnVal = ($counter > 0) ? true : false;
			return $returnVal;
		}

		//Creating Observation / Health Concerns / Physical Exam
		function createObservations($arrData = array(), $provId = '', $ptId = '',$enArr = array()){
			if(empty($ptId) || empty($provId)  || count($arrData) == 0 || count($enArr) == 0) return false;
			$cur_dat=date('Y-m-d H:i:s');
			$form_id=$enArr['DosStatus']['formId'];
			$counter = 0;
			foreach($arrData as $obj){

				if( 
					array_key_exists('observation', $obj) &&
					(array_key_exists('code', $obj['observation']) && !empty($obj['observation']['code']) || 
                            (array_key_exists('is_refused', $obj) && !empty($obj['is_refused']) && 
                                array_key_exists('resufal_code', $obj) && !empty($obj['resufal_code'])) ) &&
					array_key_exists('description', $obj['observation']) && !empty($obj['observation']['description'])
				)
				{
					/* Encounter Start Date/Time */
					$EncStDt = (isset($obj['start']['date']) && empty($obj['start']['date']) == false) ? $obj['start']['date'] : '';
					$EncStTm = (isset($obj['start']['time']) && empty($obj['start']['time']) == false) ? $obj['start']['time'] : '';
					
					/* Encounter Start Date/Time */
					$EncEnDt = (isset($obj['end']['date']) && empty($obj['end']['date']) == false) ? $obj['end']['date'] : '';
					$EncEnTm = (isset($obj['end']['time']) && empty($obj['end']['time']) == false) ? $obj['end']['time'] : '';
					
					$obs = &$obj['observation']; /* Observation procedure/action performed */

                    $type=(isset($obj['type']) && empty($obj['type']) == false) ? $obj['type'] : '0';
					/* Check if Observation record already exists */
					$query = 'SELECT id FROM hc_observations
							WHERE
								`type` = "'.$type.'" AND
								`observation` = "'.imw_real_escape_string($obs['description']).'" AND
								`observation_date`  = "'.imw_real_escape_string($EncStDt).'" AND
								`observation_time` = "'.imw_real_escape_string($EncStTm).'" AND 
								`pt_id` = "'.imw_real_escape_string($ptId).'" AND
								`status` = 0';
					$resp = imw_query($query);

					if( $resp && imw_num_rows($resp) > 0 )
					{
						$row = imw_fetch_assoc($checkDos);
						$counter++;
					}
					else
					{
						$query = 'INSERT INTO `hc_observations` 
								SET 
									`form_id` = "'.$form_id.'",
									`pt_id` = "'.$ptId.'",
									`snomed_code` = "'.imw_real_escape_string($obs['code']).'",
									`observation` = "'.imw_real_escape_string($obs['description']).'",
									`observation_date` = "'.$EncStDt.'",
									`status` = "completed",
									`entry_date_time` = "'.$cur_dat.'",
									`operator_id` = "'.$provId.'", 
									`observation_time` = "'.$EncStTm.'",
									`type`  = "'.$type.'",
									`refusal` = "'.imw_real_escape_string($obj['is_refused']).'",
									`refusal_snomed` = "'.imw_real_escape_string($obj['resufal_code']).'"';
						$resp = imw_query($query);
						$hc_id=imw_insert_id();	/* Health Concern Id */


						/** Get Observatin value description based on code supplied */
						$result = &$obj['result']; /* Observation procedure/action performed */
						$result['description'] = $obs['description'];

						$query = 'SELECT `Value_Set_Name` FROM `cqm_v8_valueset`
								WHERE
									`Code` = "'.imw_real_escape_string($result['code']).'" AND
									`Code_System` = "'.imw_real_escape_string($result['codetype']).'" AND
									LOWER(`CMS_ID`) = "'.imw_real_escape_string($this->measurecmsid).'"';
						$resp = imw_query($query);
						if( $resp && imw_num_rows($resp) > 0 )
						{
							$resp = imw_fetch_assoc($resp);

							$result['description'] = trim($resp['Value_Set_Name']);
						}

						/* Insert Observation result entry */
						$query = 'INSERT INTO `hc_rel_observations`
								SET
									`rel_observation_date` = "'.$EncStDt.'",
									`rel_observation` = "'.imw_real_escape_string($result['description']).'",
									`snomed_code` = "'.imw_real_escape_string($result['code']).'",
									`entry_date_time` = "'.$cur_dat.'",
									`operator_id` = "'.$provId.'",
									`observation_id` = "'.$hc_id.'",
									`nullflavor` = "'.imw_real_escape_string($obj['is_refused']).'"';
						
						if( imw_query($query) )
						{
							$counter++;
						}
					}
				}
			}
			$returnVal = ($counter > 0) ? true : false;
			return $returnVal;
		}
		
		function insertPtData($enArr = array(), $arrData = array(), $provId, $ptId){
			if(count($arrData) == 0) return false;
			$returrnArr = array();
			
			//Importing Medications
			if(isset($arrData['Medications']) && count($arrData['Medications']) > 0){
				$returrnArr['Medications'] = $this->createMedications($arrData['Medications'], $provId, $ptId);
			}
			
			//Importing Interventions / Sx Procedures / UDI
			if(isset($arrData['Intervention']) && count($arrData['Intervention']) > 0){
				$returrnArr['Intervention'] = $this->createInterventions($arrData['Intervention'], $provId, $ptId, $enArr);
				//pre($interventionStatus);exit;
			}
			
			if(isset($arrData['Procedure']) && count($arrData['Procedure']) > 0){
				$returrnArr['Procedure'] = $this->createInterventions($arrData['Procedure'], $provId, $ptId);
				//pre($procedureStatus);
			}
            
			if(isset($arrData['Implantable']) && count($arrData['Implantable']) > 0){
				$returrnArr['Implantable'] = $this->createImplantable($arrData['Implantable'], $provId, $ptId);
				//pre($procedureStatus);
			}
			
			if(isset($arrData['Diagnosis']) && count($arrData['Diagnosis']) > 0){
				$returrnArr['Diagnosis'] = $this->createDiagnosis($arrData['Diagnosis'], $provId, $ptId);
				//pre($procedureStatus);
			}
			
			if(isset($arrData['radiology']) && count($arrData['radiology']) > 0){
				$returrnArr['radiology'] = $this->createRadiology($arrData['radiology'], $provId, $ptId,$enArr);
				//pre($procedureStatus);
			}
			
			if(count($arrData['vital_sign'])>0){
				$returrnArr['vitalSign']=$this->createVitalSign($arrData['vital_sign'],$provId,$ptId);
			}
			
			if(isset($arrData['Observation']) && count($arrData['Observation']) > 0){
				$returrnArr['Observation'] = $this->createObservations($arrData['Observation'], $provId, $ptId,$enArr);
				//pre($procedureStatus);
			}
			
			if(count($arrData['communication'])>0){
				$form_id = $enArr['DosStatus']['formId'];
				$returrnArr['communication']=$this->createCommunication($arrData['communication'],$provId,$ptId,$form_id);
			}
            
			if(count($arrData['ptCommunication'])>0){
				$form_id = $enArr['DosStatus']['formId'];
				$returrnArr['ptCommunication']=$this->createPtCommunication($arrData['ptCommunication'],$provId,$ptId,$form_id);
			}
			
			if(count($arrData['PtPayer']) > 0){
				$form_id = (isset($enArr['DosStatus']['formId']) && empty($enArr['DosStatus']['formId']) == false) ? $enArr['DosStatus']['formId'] : '';
				$dos = (isset($enArr['DosStatus']['dateOfService']) && empty($enArr['DosStatus']['dateOfService']) == false) ? $enArr['DosStatus']['dateOfService'] : '';
				$returrnArr['PatientPayer'] = $this->createPtPayer($arrData['PtPayer'], '', $ptId, $form_id, $dos);
			}
			
			//Finializing Charts -- This step will be always done after inserting all patient data
			if(isset($enArr) && count($enArr) > 0){
				$formId = (isset($enArr['DosStatus']['formId']) && empty($enArr['DosStatus']['formId']) == false) ? $enArr['DosStatus']['formId'] : '';
				$dateOfService = (isset($enArr['DosStatus']['dateOfService']) && empty($enArr['DosStatus']['dateOfService']) == false) ? $enArr['DosStatus']['dateOfService'] : '';
				
				if(empty($formId) == false && empty($dateOfService) == false){
					$checkDos = imw_query("SELECT id from chart_master_table where date_of_service = '".$dateOfService."' AND patient_id = '".$ptId."' AND id = '".$formId."' AND finalize = 0");
					
					if(imw_num_rows($checkDos) > 0){
						$rowChart = imw_fetch_assoc($checkDos);
						$updateChart = imw_query('UPDATE chart_master_table SET finalize = 1, finalizerId = "'.$provId.'", finalizeDate = "'.$dateOfService.'" WHERE id = "'.$rowChart['id'].'"');
						
						if($updateChart){
							$returrnArr['chartFinalized'] = true;
						}
					}
				}
				
				//$returnArr['ChartFinalized'] = $this->finalizeChart($enArr, $ptId, $provId);
			}
			
			return $returrnArr;
			
		}
		
		// Creating Diagnosis / Assessments
		function createDiagnosis($arrData = array(), $provId = '', $ptId = ''){
			if(empty($ptId) || empty($provId)  || count($arrData) == 0) return false;
			$cur_dat=date('Y-m-d  H:i:s');
			$returnArr = array();
			$counter = 0;

			$currentMeasureId = strtolower($this->measurecmsid);
			
			foreach($arrData as $obj){
				//Getting Date/Time from XML Array
				
				$EncStDt = (isset($obj['effectiveTime']['startObj']['date']) && empty($obj['effectiveTime']['startObj']['date']) == false) ? $obj['effectiveTime']['startObj']['date'] : '';
				
				$EncStTm = (isset($obj['effectiveTime']['startObj']['time']) && empty($obj['effectiveTime']['startObj']['time']) == false) ? $obj['effectiveTime']['startObj']['time'] : '';
				
				
				$EncEndDt = (isset($obj['effectiveTime']['endObj']['date']) && empty($obj['effectiveTime']['endObj']['date']) == false) ? $obj['effectiveTime']['endObj']['date'] : '';
				$EncEndTm = (isset($obj['effectiveTime']['endObj']['time']) && empty($obj['effectiveTime']['endObj']['time']) == false) ? $obj['effectiveTime']['endObj']['time'] : '';


				/** Pull the data from the XML object and get the Code Description/Problem name from the latest value set definition */
				$description = '';

				/** Possbile Code Sytem names */
				$codesystems = [
					'snomedct' => '',
					'icd10cm' => 'ICD-10-CM',
					'icd9cm' => 'ICD-9-CM'
				];

				$tempCodesystems = array_keys($codesystems);

				/** First search based on Primary code */
				$codeSystemName = $obj['primaryCodeSystem'];
				
				do
				{
					$codeSystemName = strtolower($codeSystemName);

					$code = strtolower($obj[$codeSystemName]['code']);
					$codeSystem = strtolower($obj[$codeSystemName]['codeSystem']);

					$sqlProblemName = 'SELECT `Value_Set_Name` FROM `cqm_v8_valueset`
								WHERE
								LOWER(`Code`) = "'.imw_real_escape_string($code).'" AND 
								LOWER(`Code_System`) = "'.imw_real_escape_string($codeSystemName).'" AND
								LOWER(`Code_System_OID`) = "'.imw_real_escape_string($codeSystem).'" AND
								LOWER(`CMS_ID`) = "'.imw_real_escape_string($currentMeasureId).'"';
					
					$respProblemName = imw_query($sqlProblemName);

					if( $respProblemName && imw_num_rows($respProblemName) > 0 )
					{
						$respProblemName = imw_fetch_assoc($respProblemName);
						$description = trim($respProblemName['Value_Set_Name']);
					}

					unset($tempCodesystems[$codeSystemName]);
					$codeSystemName = array_shift($tempCodesystems);
				}
				while( empty($description) && count($tempCodesystems) > 0 );

				/** Append ICD-10 and ICD-9 codes to the problem description */
				$descIcd = '';
				if( array_key_exists('code', $obj['icd10cm']) && !empty($obj['icd10cm']['code']) )
				{
					$descIcd .= 'ICD-10-CM '.$obj['icd10cm']['code'].', ';
				}

				if( array_key_exists('code', $obj['icd9cm']) && !empty($obj['icd9cm']['code']) )
				{
					$descIcd .= 'ICD-9-CM '.$obj['icd9cm']['code'];
				}

				$descIcd = trim($descIcd);
				$descIcd = rtrim($descIcd, ',');
				$descIcd = ( !empty($descIcd) ) ? '('.$descIcd.')' : '';

				/** Final Problem Description */
				$description = $description.' '.$descIcd;
				$snomedCode = $obj['snomedct']['code'];

                $probCheck = $description!='' && $EncStDt!='' && $EncStTm!='' ;
                $prob_type = ($obj['type']!='') ? trim($obj['type']): 'Diagnosis';
                if(isset($obj['codeType']) && $obj['codeType']=='encounter') {
                    $prob_type='Condition';
                    $probCheck = $description!='' && $snomedCode!='' && $EncStDt!='' && $EncStTm!='' ;
                }

				//Checikng if diagnosis already exists
				$sqlDiagnosis = 'SELECT `id` FROM `pt_problem_list`
								where 
								prob_type = "'.$prob_type.'" and problem_name="'.$description.'" and onset_date = "'.$EncStDt.'" 
								and OnsetTime = "'.$EncStTm.'" AND pt_id = "'.$ptId.'" AND ccda_code = "'.$snomedCode.'"';
				$checkDos = imw_query($sqlDiagnosis);

				if( imw_num_rows($checkDos) > 0)
				{
					$counter++;
				}
				else
				{
                    if($probCheck) {
                        $insertDiagnosisSql = 'INSERT INTO `pt_problem_list`
                                                SET pt_id = "'.$ptId.'",user_id="'.$provId.'", problem_name="'.$description.'", 
                                                onset_date = "'.$EncStDt.'", OnsetTime = "'.$EncStTm.'", prob_type = "'.$prob_type.'",status="Active", ccda_code="'.$snomedCode.'", end_datetime = "'.trim($EncEndDt.' '.$EncEndTm).'"';
                        $insertDiagnosis = imw_query($insertDiagnosisSql) or imw_error();
                    }
					if($insertDiagnosis)
					{
						$counter++;
					}
				}
			}
			
			$returnVal = false;
			if($counter > 0){
				$returnVal = true;
			}
			return $returnVal;
		}
		function createCommunication($arrDataCommunication,$provId,$ptId,$formId){
			if(!$ptId){return false;}
			$cur_dat=date('Y-m-d  H:i:s');
			$return=false;
			
			$counter = 0;
			if(count($arrDataCommunication)>0){
				foreach($arrDataCommunication as $communicationDataVal){
					$templateData=$DOS=$text="";
					$DOS=$communicationDataVal['start_date_time']['date'];
					$text=$communicationDataVal['text'];
					$XML_value=$communicationDataVal['XML_val'];
					$arrText=array();
					$arrText=explode(":",$text);
					$templateData=trim(end($arrText));
					$qrySelect="SELECT patient_consult_id FROM patient_consult_letter_tbl WHERE patient_id='".$ptId."' AND date='".$DOS."' AND templateData='".$templateData."' " ;
					$resSelect=imw_query($qrySelect) or imw_error();
					if(imw_num_rows($resSelect) == 0){
						$qryInsertConsult="INSERT INTO patient_consult_letter_tbl set patient_id='".$ptId."',patient_form_id='".$formId."',templateData='".$templateData."',date='".$DOS."',templateId=0,templateName='Other',status='0',operator_id='".$provId."',cur_date='".$cur_dat."', provider_signature_id=1,provider_signature_exist='yes'";
						$resInsertConsult=imw_query($qryInsertConsult) or imw_error();
						if($resInsertConsult){
							$qry_insert_communication="INSERT INTO communication set patient_id='".$ptId."',form_id='".$formId."',description='".$XML_value."'";
							$res_insert_communication=imw_query($qry_insert_communication) or imw_error();
							$counter++;
							$return=true;
						}
					}else{
						$counter++;
					}
				}
			}
			if($counter > 0 ) $return = true;
			return $return;
		}
        
		function createPtCommunication($arrDataCommunication,$provId,$ptId,$formId){
			if(!$ptId){return false;}
			$cur_dat=date('Y-m-d  H:i:s');
			$return=false;
			
            $currentMeasureId = strtolower($this->measurecmsid);
            
			$counter = 0;
			if(count($arrDataCommunication)>0){
				foreach($arrDataCommunication as $obj){
                    $commStDt = (isset($obj['start_date_time']['date']) && empty($obj['start_date_time']['date']) == false) ? $obj['start_date_time']['date'] : '';
                    $commStTm = (isset($obj['start_date_time']['time']) && empty($obj['start_date_time']['time']) == false) ? $obj['start_date_time']['time'] : '';

                    $commEndDt = (isset($obj['end_date_time']['date']) && empty($obj['end_date_time']['date']) == false) ? $obj['end_date_time']['date'] : '';
                    $commEndTm = (isset($obj['end_date_time']['time']) && empty($obj['end_date_time']['time']) == false) ? $obj['end_date_time']['time'] : '';
                    
                    $IRCPcode = (isset($obj['IRCP']) && empty($obj['IRCP']) == false) ? $obj['IRCP'] : '';
                    $AUTcode = (isset($obj['AUT']) && empty($obj['AUT']) == false) ? $obj['AUT'] : '';
                    
                    $refusal = (isset($obj['refusal']) && empty($obj['refusal']) == false) ? $obj['refusal'] : 0;
                    $refusal_snomedCode = (isset($obj['refusal_snomedCode']) && empty($obj['refusal_snomedCode']) == false) ? $obj['refusal_snomedCode'] : '';
                    
                    $snomedCode = (isset($obj['snomedCode']) && empty($obj['snomedCode']) == false) ? $obj['snomedCode'] : '';
                    
                    $approved='accept';
                    if($refusal==1)$approved='decline';
                    
                    $message_text='(';
                    if($snomedCode!='')$message_text.='Communication-CM '.$snomedCode;
                    if($IRCPcode!='')$message_text.=',IRCP-CM '.$IRCPcode;
                    if($AUTcode!='')$message_text.=',AUT-CM '.$AUTcode;
                    if($refusal==1 && $refusal_snomedCode!='')$message_text.=',Refusal-CM '.$refusal_snomedCode;
                    if($message_text!='(')$message_text.=')';else$message_text='';
                    
                    $message_send_date=date('Y-m-d  H:i:s', strtotime($commStDt.' '.$commStTm));
                    $message_subject='Patient Communication Message';
                    
                    //Checikng if Communication message already exists
                    $sqlComm = 'SELECT `user_message_id` FROM `user_messages`
                                    where 
                                    message_text = "'.$message_text.'" and message_send_date="'.$message_send_date.'" and Pt_Communication = 1 
                                    AND patientId = "'.$ptId.'" ';
                    $checkComm = imw_query($sqlComm);

                    if( imw_num_rows($checkComm) > 0)
                    {
                        $counter++;
                    }
                    else
                    {
                        $qry_insert="INSERT INTO user_messages 
                            SET patientId='".$ptId."', 
                            message_subject='".$message_subject."', 
                            message_text='".$message_text."', 
                            message_to='".$provId."', 
                            message_sender_id='".$provId."', 
                            approved='".$approved."', 
                            message_send_date='".$message_send_date."', 
                            message_status='0', 
                            message_read_status='0', 
                            Pt_Communication='1' ";
                    
                        $insertComm=imw_query($qry_insert);
                        if($insertComm)
                        {
                            $counter++;
                        }
                    }
                }
			}
			if($counter > 0 ) $return = true;
			return $return;
		}
		
		//Managing Patient Payer codes
		function createPtPayer($arrData = array(),$provId = '',$ptId = '',$form_id = '',$dos = ''){
			if(count($arrData) == 0 || empty($ptId)) return false;
			$counter = 0;
			$qry_dos="";
			if($dos!='' && $dos!='0000-00-00'){ $qry_dos="  AND dos = '".$dos."' ";}
			$qry_formid="";
			if($form_id!=''){ $qry_formid=" AND formId = '".$form_id."' ";}
			
			foreach($arrData as $obj){
				$code = (isset($obj['code']) && empty($obj['code']) == false) ? $obj['code'] : '';
				$valueCode = (isset($obj['valueCode']) && empty($obj['valueCode']) == false) ? $obj['valueCode'] : '';
				$valueCodeSys = (isset($obj['valueCodeSys']) && empty($obj['valueCodeSys']) == false) ? $obj['valueCodeSys'] : '';
				$valueValueSet = (isset($obj['valueValueSet']) && empty($obj['valueValueSet']) == false) ? $obj['valueValueSet'] : '';
				$startDt = (isset($obj['startDt']) && empty($obj['startDt']) == false) ? $obj['startDt'] : '';
				$endDt = (isset($obj['endDt']) && empty($obj['endDt']) == false) ? $obj['endDt'] : '';
				
				$startTimeStamp = $startDt['date'].' '.$startDt['time'];
				$endTimeStamp = $endDt['date'].' '.$endDt['time'];
				
				$endTimeStamp = (empty($endTimeStamp) == false) ? $endTimeStamp : '';
				$startTimeStamp = (empty($startTimeStamp) == false) ? $startTimeStamp : '';
				
				$payerText = (isset($obj['text']) && empty($obj['text']) == false) ? $obj['text'] : '';
				
				if(empty($payerText)){
					$checkText = imw_query('SELECT name FROM payerdt WHERE code = "'.$valueCode.'"');
					if(imw_num_rows($checkText) > 0){
						$fetchDt = imw_fetch_assoc($checkText);
						$payerText = $fetchDt['name'];
					}
				}
				
				//Checikng Database for Payer code
				$checkPayer = imw_query('SELECT * FROM patientPayer WHERE pid = "'.$ptId.'" '.$qry_dos.$qry_formid.'  AND payer = "'.$code.'"');
				if(imw_num_rows($checkPayer) > 0){
					$counter++;
				}else{
					//Inserting Into table
					$insertQry = imw_query('INSERT INTO patientPayer SET pid = "'.$ptId.'", dos = "'.$dos.'", formId = "'.$form_id.'", payer = "'.$code.'", valueCode = "'.$valueCode.'", valCodeSet = "'.$valueCodeSys.'", valValueSet = "'.$valueValueSet.'", EffStart = "'.$startTimeStamp.'", EffEnd = "'.$endTimeStamp.'", displayText = "'.$payerText.'" ');

					if($insertQry){
						$counter++;
					}
				}
				
				
				//Inserting Patient Payer Code
				$chkPtData = imw_query('SELECT * FROM patient_data where id = '.$ptId.' and patient_payer = ""');
				if(imw_num_rows($chkPtData) > 0){
					$updateQry = imw_query('UPDATE patient_data SET patient_payer = "'.$valueCode.'" WHERE id = '.$ptId.' ');
					
					if($updateQry) $counter++;
					
				}
				
			}
			$returnVal = ($counter > 0) ? true : false;
			return $returnVal;
		}
		
		function finalizeChart($enArr = array(), $ptId = '', $provId = ''){
			if(count($enArr) == 0 || empty($ptId)) return false;
			
			pre($enArr);
			
			//Check if encounter / chart exist or not
			
			
			
			
			exit;
		}
		
		function saveCodeSystemValues($data_arr, $pid){
            
			if($data_arr['code']!=""){
				$code=$data_arr['code'];
				$valueSet=$data_arr['valueSet'];
				$valueset_text=$data_arr['valueset_text'];
				$type=$data_arr['codeSystemName'];
				$code_system=$data_arr['codeSystem'];
				$page=$data_arr['type'];
				$checkPayer = imw_query('SELECT * FROM snomed_valueset WHERE code = "'.$code.'" AND value_set = "'.$valueSet.'" AND code_system = "'.$code_system.'" AND  page = "'.$page.'" AND pid = "'.$pid.'"');
				if(imw_num_rows($checkPayer) == 0){
					$insertQry = imw_query('INSERT INTO snomed_valueset SET code = "'.$code.'", value_set = "'.$valueSet.'", valueset_text = "'.$valueset_text.'", type = "'.$type.'", code_system = "'.$code_system.'", page = "'.$page.'", pid = "'.$pid.'" ');
				}
			}
            
            
		}
		
		function setGlobalStatus($status)
		{
			if( $this->globalStatus )
				$this->globalStatus = ($status !== false) ? $this->globalStatus : false; 		
		}
		
		function managePtData($arrData = array()){
			if(count($arrData) == 0) return $arrData;
			
			$ptFields = array_keys($arrData);
			$ptValues = array_values($arrData);
			
			$ptArr = array_combine($ptFields,$ptValues);
			
			$uniqueTag = $arrData['uniqueId'];
			$ptId = $arrData['ptId'];
			 unset($arrData['uniqueId']);
			 unset($arrData['ptId']);
			
			//Check if the current array unique Tag already exist or not
			$checkQry = imw_query('SELECT * FROM uniptidentify WHERE uniqueTag = "'.$uniqueTag.'" AND LOWER(fname) = "'.strtolower($arrData['fname']).'" AND LOWER(lname) = "'.strtolower($arrData['lname']).'" AND delStatus = 0');
			
			if(imw_num_rows($checkQry) > 0){
				$row = imw_fetch_assoc($checkQry);
				$patientId = $row['ptId'];
				
				//Getting Patient Record
				$getPt = imw_query('SELECT * FROM patient_data where id = "'.$patientId.'"');
				if(imw_num_rows($getPt) > 0){
					$rowPt = imw_fetch_assoc($getPt);
					$updateRec = UpdateRecords($patientId, 'id', $arrData, 'patient_data');
					if($updateRec){
						$arrData['uniqueId']  = $uniqueTag;
						$arrData['ptId']  = $patientId;
						return $arrData;
					}
				}
			}else{
				$insertQry = imw_query('INSERT INTO uniptidentify SET uniqueTag = "'.$uniqueTag.'", fname = "'.$arrData['fname'].'", lname = "'.$arrData['lname'].'", delStatus = 0, ptId = "'.$ptId.'"');
				
				if($insertQry){
					$arrData['uniqueId']  = $uniqueTag;
					$arrData['ptId']  = $ptId;
					return $arrData;
				}
				
			}
		}
		
		function insertingPatient($arrData = array(), $apptId = ''){
			if(count($arrData) == 0 || empty($apptId)) return false;
			
			
			foreach($arrData as $obj){
				$obj['appt_id'] = $apptId;
				$checkPt = imw_query('SELECT * FROM inpatient_fields WHERE appt_id = "'.$obj['appt_id'].'" AND field_type = "'.$obj['field_type'].'" AND field_code = "'.$obj['field_code'].'"');
				$counter = 0 ;
				if(imw_num_rows($checkPt) > 0){
					$row = imw_fetch_assoc($checkPt);
					$id = $row['id'];
					
					$counter = UpdateRecords($id,'id',$obj,'inpatient_fields');
				}else{
					if(empty($obj['field_code']) == false && isset($obj['field_code'])){
						$counter = AddRecords($obj,'inpatient_fields');
					}
				}
			}
		}
		
		function addRefPhysician($arrData = array(), $ptData = array()){
			if(count($arrData) == 0 || count($ptData) == 0) return false;
			
			$ptId = $ptData['ptId'];
			$refId = '';
			
			$counter = 0;
			$qryGetRefPhy = imw_query("select physician_Reffer_id from refferphysician where LOWER(LastName) = '".addslashes(strtolower($arrData['LastName']))."' and LOWER(FirstName) = '".addslashes(strtolower($arrData['FirstName']))."' and delete_status = 0 and NPI = '".$arrData['NPI']."' ORDER BY LastName ASC limit 1");
			
			if(imw_num_rows($qryGetRefPhy) > 0){
				$row = imw_fetch_assoc($qryGetRefPhy);
				$phyId = $row['physician_Reffer_id'];
				$update = false;
				$update = UpdateRecords($phyId, 'physician_Reffer_id', $arrData, 'refferphysician');
				if($update !== false){
					$refId = $phyId;
				}
			}else{
				$arrData['created_date'] = date('Y-m-d');
				$refId = AddRecords($arrData,'refferphysician');
			}
			
			if(empty($refId) == false){
				$phyName = $arrData['LastName'].', '.$arrData['FirstName'];
				$chkPtRef = imw_query('SELECT * FROM patient_multi_ref_phy WHERE ref_phy_id = "'.$refId.'" AND status = 0 AND type = 1 AND patient_id = "'.$ptId.'"');
				if(imw_num_rows($chkPtRef) > 0){
					$counter++;
				}else{
					$insertQry = imw_query('INSERT INTO patient_multi_ref_phy SET patient_id = "'.$ptId.'", ref_phy_id = "'.$refId.'", phy_type = 1, created_by = 1, created_by_date_time = "'.date('Y-m-d H:i:s').'", status = 0, 	deleted_by = 0, ref_phy_name = "'.$phyName.'"');
					if($insertQry){
						$counter++;
					}
				}
			}
			
			$return = ($counter > 0) ? true : false;
			return $return;
		}
		
		function createSelBlock($uniqueRec = 0, $arrData = array()){
			if($uniqueRec !== 0 || count($arrData) == 0) return false;
			
			$str = '';
			
			$ptId = $arrData['ptId'];
			$uniqueId = $arrData['External_MRN_1'];
			
			if(empty($ptId) == false && empty($uniqueId) == false){
				$str = '<button type="button" id="'.$ptId.'_btn" class="uniquePt btn btn-primary" data-id="'.$ptId.'" data-unique="'.$uniqueId.'" onclick="setPtDemo(this);">Use This</button>';	
			}
			return $str;
		}
        
        
        public function problem_type_srh($val) {   //SNOMED CT
            $val = trim(strtolower($val));
            $arrProbType = array(
                array("imw" => 'finding', "code" => "404684003", "display_name" => "Finding", "loinic" => "29308-4"),
                array("imw" => 'complaint', "code" => "409586006", "display_name" => "Complaint", "loinic" => "29308-4"),
                array("imw" => 'diagnosis', "code" => "282291009", "display_name" => "Diagnosis", "loinic" => "29308-4"),
                array("imw" => 'condition', "code" => "64572001", "display_name" => "Disorder", "loinic" => "29308-4"),
                array("imw" => 'smoker, current status unknown', "code" => "248536006", "display_name" => "Diagnosis", "loinic" => "29308-4"),
                array("imw" => 'symptom', "code" => "418799008", "display_name" => "Symptom", "loinic" => "75325-1"),
                array("imw" => 'problem', "code" => "55607006", "display_name" => "Problem", "loinic" => "29308-4"),
                array("imw" => 'cognitive function finding', "code" => "373930000", "display_name" => "Diagnosis", "loinic" => "29308-4")
            );
            $return = 'Diagnosis';
            if ($val != "") {
                foreach ($arrProbType as $row) {
                    if (in_array($val, $row)) {
                        $return = $row['display_name'];
                        break;
                    }
                }
            }
            return $return;
        }
		
	}
?>