<?php 
	require_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');
	require_once($GLOBALS['fileroot'].'/library/classes/ccd_xml_parser.php');
	
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
			
			$this->saveCodeSystemValues($setSnomedValueSet_arr);
			
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
			$measureNQF = array('9a032d9c-3d9b-11e1-8634-00237d5bf174' => '0419', 'd90bdab4-b9d2-4329-9993-5c34e2c0dc66' => '0055', '9a0339c2-3d9b-11e1-8634-00237d5bf174' => '0564', '39e0424a-1727-4629-89e2-c46c2fbb3f5f' => '0565', 'e35791df-5b25-41bb-b260-673337bc44a8' => '0028', '53d6d7c3-43fb-4d24-8099-17e74c022c05' => '0089', 'db9d9f09-6b6a-4749-a8b2-8c1fdb018823' => '0086', 'a3837ff8-1abc-4ba9-800e-fd4e7953adbd' => '0022', 'abdc37cc-bac6-4156-9b91-d1be2c8b7268' => '0018', '50164228-9d64-4efc-af67-da0547ff61f1' => '0088');
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
											
											$refusal = $refusal_snomedCode = '';
											$refusal = (bool) $procData['negationInd'];  
											$refusal_snomedCode = $procData->entryRelationship->observation->value['code'];
											
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
											
											//Description
											$description = '';
											if(isset($procData->code->originalText)){
												$description = (string) $procData->code->originalText;
												$description = (empty($description) == false) ? $this->getTitle($description) : '';
											}
											
											
											//Code
											$code = '';
											if(isset($procData->code['code'])){
												$code = (string) $procData->code['code'];
												$code = (empty($code) == false) ? $code : '';
											}
											
											//Value set
											$valueSet = '';
											if(isset($procData->code['sdtcvalueSet'])){
												$valueSet = (string) $procData->code['sdtcvalueSet'];
												$valueSet = (empty($valueSet) == false) ? $valueSet : '';
											}
											
											//Code System
											$codeSystem = '';
											if(isset($procData->code['codeSystem'])){
												$codeSystem = (string) $procData->code['codeSystem'];
												$codeSystem = (empty($codeSystem) == false) ? $codeSystem : '';
											}
											
											//Code System Name
											$codeSystemName = '';
											if(isset($procData->code['codeSystemName'])){
												$codeSystemName = (string) $procData->code['codeSystemName'];
												$codeSystemName = (empty($codeSystemName) == false) ? $codeSystemName : '';
											}
											$procedure_status = $proc_type = '';
											if($templateId == '2.16.840.1.113883.10.20.24.3.64'){
												$procedure_status="completed";
												$proc_type="procedure";
											}
											
											//Translation Code
											$tranCode = '';
											if(isset($procData->code->translation['code'])){
												$tranCode = (string) $procData->code->translation['code'];
												$tranCode = (empty($tranCode) == false) ? $tranCode : '';
											}
											
											
											$tmpProcedure['startObj'] = $startIntervention;
											$tmpProcedure['endObj'] = $endIntervention;
											$tmpProcedure['description'] = $description;
											$tmpProcedure['code'] = $code;
											$tmpProcedure['valueSet'] = $valueSet;
											$tmpProcedure['valueset_text'] = $description['title'];
											$tmpProcedure['codeSystem'] = $codeSystem;
											$tmpProcedure['codeSystemName'] = $codeSystemName;
											$tmpProcedure['status'] = $procedure_status;
											$tmpProcedure['type'] = $proc_type;
											
											$tmpProcedure['refusal'] = $refusal;
											$tmpProcedure['refusal_snomedCode'] = $refusal_snomedCode;
											$tmpProcedure['translation_code'] = $tranCode;
											$returnArr['Procedure'][] = $tmpProcedure;
											
											$setSnomedValueSet_arr=array();
											$setSnomedValueSet_arr['code'] = (string)$procData->code['code'];
											$setSnomedValueSet_arr['valueSet'] = (string)$procData->code['sdtcvalueSet'];
											$setSnomedValueSet_arr['valueset_text'] = (string)$procData->code->originalText;
											$setSnomedValueSet_arr['codeSystem'] = (string)$procData->code['codeSystem'];
											//$setSnomedValueSet_arr['codeSystemName'] = $codeSystemName;
											$setSnomedValueSet_arr['type'] = $proc_type;
											
											$this->saveCodeSystemValues($setSnomedValueSet_arr, $ptId);
                                            
                                            if(strtolower(trim($procData['negationInd']))=='true')
                                            {
                                                $setSnomedValueSet_arr=array();
                                                $setSnomedValueSet_arr['code'] = (string)$procData->entryRelationship->observation->value['code'];
                                                $setSnomedValueSet_arr['valueSet'] = (string)$procData->entryRelationship->observation->value['sdtcvalueSet'];
                                                $setSnomedValueSet_arr['valueset_text'] = '';
                                                $setSnomedValueSet_arr['codeSystem'] = (string)$procData->entryRelationship->observation->value['codeSystem'];
                                                //$setSnomedValueSet_arr['codeSystemName'] = $codeSystemName;
                                                $setSnomedValueSet_arr['type'] = $proc_type;

                                                $this->saveCodeSystemValues($setSnomedValueSet_arr, $ptId); 
                                            }
									}
									//pre($entryParent->substanceAdministration);
									//Medications Data
									if(isset($entryParent->substanceAdministration)){
										
										//Medication Array
										$medData = &$entryParent->substanceAdministration;
										
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
										
										
										if(empty($templateId) == false && ($templateId == '2.16.840.1.113883.10.20.24.3.144' || $templateId == '2.16.840.1.113883.10.20.24.3.59')){
											
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
													
													$tmpVsArr['date_vital'] = $StartTs;
													
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
											}else{
												//Assessment / Diagnosis / Exan Data --> Health Concerns
												//Effective Time
												if(isset($observations->effectiveTime)){
													$StartTs = $EndTs = $startTime = $endTime = '';
													//Start Date Time
													if(isset($observations->effectiveTime->low)){
														$startTime = $this->formatXMLDt($observations->effectiveTime->low);	
													}
													
													//End Date Time
													if(isset($observations->effectiveTime->high)){
														$endTime = $this->formatXMLDt($observations->effectiveTime->high);	
													}
												}
												
												//Description
												$description = '';
												if(isset($observations->code->originalText)){
													$description = (string) $observations->code->originalText;
													$description = (empty($description) == false) ? $this->getTitle($description) : '';
												}
												
												//LoincCode
												$LoincCode = '';
												if(isset($observations->code['code'])){
													$LoincCode = (string) $observations->code['code'];
													$LoincCode = (empty($LoincCode) == false) ? $LoincCode : '';
												}
												
												//Code
												$code = '';
												if(isset($observations->value['code'])){
													$code = (string) $observations->value['code'];
													$code = (empty($code) == false) ? $code : '';
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
												
												//Null value
												$nullValue = '';
												if(isset($observations->value['nullFlavor'])){
													$nullValue = (string) $observations->value['nullFlavor'];
												}
												
												//Refusal Value
												$refusalObserv = '';
												if(isset($observations->value['nullFlavor'])){
													$refusalObserv = (string) $observations['negationInd'];
													$refusalObserv = (empty($refusalObserv) == false && $refusalObserv == 'true') ? 1 : 0;
												}
												
												//Refusal Code
												$refusalCode = '';
												if(isset($observations->entryRelationship->observation->value['code'])){
													$refusalCode = (string) $observations->entryRelationship->observation->value['code'];
												}
												
												$tmpObservation['startObj'] = $startTime;
												$tmpObservation['endObj'] = $endTime;
												$tmpObservation['description'] = $description;
												$tmpObservation['loincCode'] = $LoincCode;
												$tmpObservation['nullValue'] = $nullValue;
												$tmpObservation['refusal'] = $refusalObserv;
												$tmpObservation['refusalCode'] = $refusalCode;
												$tmpObservation['code'] = $code;
												if($templateId == '2.16.840.1.113883.10.20.24.3.144'){
													$tmpObservation['type'] = '1';
												}else if($templateId == '2.16.840.1.113883.10.20.24.3.59'){
													$tmpObservation['type'] = '0';
												}
												
												$returnArr['Observation'][] = $tmpObservation;
												
												
												//Lionic
												$setSnomedValueSet_arr=array();
												$setSnomedValueSet_arr['code'] = $LoincCode;
												$setSnomedValueSet_arr['valueSet'] = $observations->code['sdtcvalueSet'];
												$setSnomedValueSet_arr['valueset_text'] = $observations->code->originalText;
												$setSnomedValueSet_arr['codeSystem'] = $observations->code['codeSystem'];
												$setSnomedValueSet_arr['type'] = 'Observation';
												
												$this->saveCodeSystemValues($setSnomedValueSet_arr, $ptId);
												
												
												
												
												//EntryRelative -> value
												$setSnomedValueSet_arr=array();
												$setSnomedValueSet_arr['code'] = (string)$observations->value['code'];
												$setSnomedValueSet_arr['valueSet'] = (string)$observations->value['sdtcvalueSet'];
												$setSnomedValueSet_arr['valueset_text'] = '';
												$setSnomedValueSet_arr['codeSystem'] = (string)$observations->value['codeSystem'];
												//$setSnomedValueSet_arr['codeSystemName'] = $codeSystemName;
												$setSnomedValueSet_arr['type'] = 'Observation';

												$this->saveCodeSystemValues($setSnomedValueSet_arr, $ptId); 
												
												
												
												if(strtolower(trim($observations['negationInd']))=='true')
												{
													$setSnomedValueSet_arr=array();
													$setSnomedValueSet_arr['code'] = (string)$observations->entryRelationship->observation->value['code'];
													$setSnomedValueSet_arr['valueSet'] = (string)$observations->entryRelationship->observation->value['sdtcvalueSet'];
													$setSnomedValueSet_arr['valueset_text'] = '';
													$setSnomedValueSet_arr['codeSystem'] = (string)$observations->entryRelationship->observation->value['codeSystem'];
													//$setSnomedValueSet_arr['codeSystemName'] = $codeSystemName;
													$setSnomedValueSet_arr['type'] = 'Observation';

													$this->saveCodeSystemValues($setSnomedValueSet_arr, $ptId); 
												}
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
										if(empty($templateId) == false && ($templateId == '2.16.840.1.113883.10.20.24.3.31' || $templateId == '2.16.840.1.113883.10.20.24.3.32'  || $templateId == '2.16.840.1.113883.10.20.24.3.64')){
											$startIntervention = $endIntervention = '';
											$tmpIntervention = array();
											
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
											
											//Description
											$description = '';
											if(isset($entryParent->act->code->originalText)){
												$description = (string) $entryParent->act->code->originalText;
												$description = (empty($description) == false) ? $this->getTitle($description) : '';
											}
											
											//Code
											$code = '';
											if(isset($entryParent->act->code['code'])){
												$code = (string) $entryParent->act->code['code'];
												$code = (empty($code) == false) ? $code : '';
											}
											
											//Value set
											$valueSet = '';
											if(isset($entryParent->act->code['sdtcvalueSet'])){
												$valueSet = (string) $entryParent->act->code['sdtcvalueSet'];
												$valueSet = (empty($valueSet) == false) ? $valueSet : '';
											}
											
											//Code System
											$codeSystem = '';
											if(isset($entryParent->act->code['codeSystem'])){
												$codeSystem = (string) $entryParent->act->code['codeSystem'];
												$codeSystem = (empty($codeSystem) == false) ? $codeSystem : '';
											}
											
											//Code System Name
											$codeSystemName = '';
											if(isset($entryParent->act->code['codeSystemName'])){
												$codeSystemName = (string) $entryParent->act->code['codeSystemName'];
												$codeSystemName = (empty($codeSystemName) == false) ? $codeSystemName : '';
											}
											
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
											
											$refusal = $refusal_snomedCode = '';
											$refusal = (bool) $entryParent->act['negationInd'];  
											$refusal_snomedCode = $entryParent->act->entryRelationship->observation->value['code'];
											
											$tmpIntervention['startObj'] = $startIntervention;
											$tmpIntervention['endObj'] = $endIntervention;
											$tmpIntervention['description'] = $description;
											$tmpIntervention['code'] = $code;
											$tmpIntervention['valueSet'] = $valueSet;
											$tmpIntervention['status'] = $procedure_status;
											$tmpIntervention['type'] = $proc_type;
											$tmpIntervention['refusal'] = $refusal;
											$tmpIntervention['refusal_snomedCode'] = $refusal_snomedCode;
											
											$returnArr['Intervention'][] = $tmpIntervention;
											
											
											$setSnomedValueSet_arr=array();
											$setSnomedValueSet_arr['code'] = $code;
											$setSnomedValueSet_arr['valueSet'] = $valueSet;
											$setSnomedValueSet_arr['valueset_text'] = $description['title'];
											$setSnomedValueSet_arr['codeSystem'] = $codeSystem;
											$setSnomedValueSet_arr['codeSystemName'] = $codeSystemName;
											$setSnomedValueSet_arr['type'] = $proc_type;
											
											$this->saveCodeSystemValues($setSnomedValueSet_arr, $ptId);
                                            
                                            if(strtolower(trim($entryParent->act['negationInd']))=='true')
                                            {
                                                $setSnomedValueSet_arr=array();
                                                $setSnomedValueSet_arr['code'] = (string)$entryParent->act->entryRelationship->observation->value['code'];
                                                $setSnomedValueSet_arr['valueSet'] = (string)$entryParent->act->entryRelationship->observation->value['sdtcvalueSet'];
                                                $setSnomedValueSet_arr['valueset_text'] = '';
                                                $setSnomedValueSet_arr['codeSystem'] = (string)$entryParent->act->entryRelationship->observation->value['codeSystem'];
                                                //$setSnomedValueSet_arr['codeSystemName'] = $codeSystemName;
                                                $setSnomedValueSet_arr['type'] = $proc_type;

                                                $this->saveCodeSystemValues($setSnomedValueSet_arr, $ptId); 
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
														
														$tmpEncounterArr['code'] = array('valueSet' => $valueSet, 'code' => $code, 'description' => $description);
														$tmpEncounterArr['text'] = (isset($obj->text)) ? (string) $obj->text : '';
														$tmpEncounterArr['statusCode'] = (isset($obj->statusCode['code'])) ? (string) $obj->statusCode['code'] : '';
														$tmpEncounterArr['effectiveTime'] = array('startObj' => $StartTs, 'endObj' => $EndTs);
														
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
												if(isset($actData->observation)){
													$tmpDiagnosisArr = array();
													$diagArr = &$actData->observation;
													
													//Value set
													$valueSet = '';
													if(isset($diagArr->value['sdtcvalueSet'])){
														$valueSet = (string) $diagArr->value['sdtcvalueSet'];
														$valueSet = (empty($valueSet) == false) ? $valueSet : '';
													}
													
													//Code
													$code = '';
													if(isset($diagArr->value['code'])){
														$code = (string) $diagArr->value['code'];
														$code = (empty($code) == false) ? $code : '';
													}
													
													//Code System
													$codeSystem = '';
													if(isset($diagArr->value['codeSystem'])){
														$codeSystem = (string) $diagArr->value['codeSystem'];
														$codeSystem = (empty($codeSystem) == false) ? $codeSystem : '';
													}
													
													//Code System Name
													$codeSystemName = '';
													if(isset($diagArr->value['codeSystemName'])){
														$codeSystemName = (string) $diagArr->value['codeSystemName'];
														$codeSystemName = (empty($codeSystemName) == false) ? $codeSystemName : '';
													}
													
													//Description
													$description = '';
													if(isset($diagArr->value->originalText)){
														$description = (string) $diagArr->value->originalText;
														$description = (empty($description) == false) ? $this->getTitle($description) : '';
													}
													
													//ICD Code
													$icd10Code = '';
													
													if( isset($diagArr->value->translation) ){
													    
													    foreach($diagArr->value->translation as $translation){
														$codeSystem1 = (string) $translation['codeSystem'];
														if($codeSystem1 == '2.16.840.1.113883.6.90'){		//No snowmed code
															$icd10Code = (string) $translation['code'];
														}
													    }
													    
													}
													
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
													
													$tmpDiagnosisArr['code'] = array('valueSet' => $valueSet, 'code' => $code, 'description' => $description, 'icd10' => $icd10Code);
													$tmpDiagnosisArr['effectiveTime'] = array('startObj' => $DiagStartTs, 'endObj' => $DiagEndTs);
													
													$returnArr['Diagnosis'][] = $tmpDiagnosisArr;	/*Container for Problems and Diagnosis*/
													//pre($actData->observation);
													
													$setSnomedValueSet_arr=array();
													$setSnomedValueSet_arr['code'] = $code;
													$setSnomedValueSet_arr['valueSet'] = $valueSet;
													$setSnomedValueSet_arr['valueset_text'] = $description['title'];
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
			
			//Loop will be executed for section in the array i.e -> Encounter, Observations etc
			if(isset($xmlArr['Encounter'])){
				foreach($xmlArr['Encounter'] as $obj){
					$encounterDt = array();
					
					//Creating Encounter
					$encounterDt = $this->createEncounter($obj, $provId, $facId, $ptId, $ptNm);
					$returnArr['Encounter'][] = $encounterDt;
					
					//If encounter is created
					if(count($encounterDt) > 0){
						$returnArr['PtData'][] = $this->insertPtData($encounterDt, $xmlArr, $provId, $ptId);
						foreach($returnArr['PtData'] as $tmp)
							$this->setGlobalStatus($tmp);
					}
				}
			}else{
				$returnArr['PtData'][] = $this->insertPtData('',$xmlArr, $provId, $ptId);
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
			$returnArr['title'] = (isset($arr[1]) && empty($arr[1]) == false) ? trim($arr[1]) : '';			//Operation title like Opthamologic Services
			
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
				
				$medStatus = (empty($med_dosage) == false) ? 'Active' : 'Order';
				
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
		
		
		//Creating Interventions
		function createInterventions($arrData = array(), $provId = '', $ptId = ''){
			if(empty($ptId) || empty($provId)  || count($arrData) == 0) return false;
			
			$cur_dat=date('Y-m-d H:i:s');
			$counter = 0;
			foreach($arrData as $obj){
				//Encounter Start Date/Time
				$EncStDt = (isset($obj['startObj']['date']) && empty($obj['startObj']['date']) == false) ? $obj['startObj']['date'] : '';
				$EncStTm = (isset($obj['startObj']['time']) && empty($obj['startObj']['time']) == false) ? $obj['startObj']['time'] : '';
				
				//Encounter Start Date/Time
				$EncEnDt = (isset($obj['endObj']['date']) && empty($obj['endObj']['date']) == false) ? $obj['endObj']['date'] : '';
				$EncEnTm = (isset($obj['endObj']['time']) && empty($obj['endObj']['time']) == false) ? $obj['endObj']['time'] : '';
				
				$description = (isset($obj['description']['title']) && empty($obj['description']['title']) == false) ? $obj['description']['title'] : '';
				$valueSet = (isset($obj['valueSet']) && empty($obj['valueSet']) == false) ? $obj['valueSet'] : '';
				$code = (isset($obj['code']) && empty($obj['code']) == false) ? $obj['code'] : '';
				$procedure_status = (isset($obj['status']) && empty($obj['status']) == false) ? $obj['status'] : '';
				$proc_type = (isset($obj['type']) && empty($obj['type']) == false) ? $obj['type'] : '';
				
				//Refusal Reason
				$refusalCode = (isset($obj['refusal_snomedCode']) && empty($obj['refusal_snomedCode']) == false) ? $obj['refusal_snomedCode'] : '';
				$refusal = (isset($obj['refusal']) && empty($obj['refusal']) == false && $obj['refusal'] === true) ? $obj['refusal'] : '';
				
				//Translation Code
				$translationCode = (isset($obj['translation_code']) && empty($obj['translation_code']) == false) ? $obj['translation_code'] : '';
				//$translationCode = (isset($obj['translation_code']) && empty($obj['translation_code']) == false && $obj['translation_code'] === true) ? $obj['translation_code'] : '';
				
				if(empty($refusal)){
					$refusalCode = '';
					$refusal = 0;
				}
				
				//Inserting in DB
				//Checikng if patient Intervention exists or not already
				
				$descriptionChk = (strtolower($proc_type) == 'intervention') ? 'title="'.$description.'" and' : '';
				$checkDos = imw_query('SELECT id FROM lists where proc_type="'.$proc_type.'" and '.$descriptionChk.' begdate = "'.$EncStDt.'" and begtime = "'.$EncStTm.'" AND pid = "'.$ptId.'" AND allergy_status = "Active" AND ccda_code = "'.$code.'"');
				if(imw_num_rows($checkDos) > 0){
					$row = imw_fetch_assoc($checkDos);
					//return array('tblId' => $row['id']);
					$list_id = $row['id'];
					$counter++;
				}else{
					$insertDos = imw_query('INSERT INTO lists SET date="'.$cur_dat.'",type = "5",title = "'.$description.'",begdate = "'.$EncStDt.'",begtime = "'.$EncStTm.'",enddate = "'.$EncEnDt.'",endtime = "'.$EncEnTm.'", 
					pid = "'.$ptId.'",allergy_status = "Active", proc_type = "'.$proc_type.'",ccda_code="'.$code.'",procedure_status="'.$procedure_status.'", refusal = "'.$refusal.'", refusal_snomed = "'.$refusalCode.'", translation_code = "'.$translationCode.'"') or imw_error();
					
					if($insertDos){
						$counter++;
					}else{
						//return false;
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
				$date_of_vital=$time_of_vital=$BP_systolic_value=$BP_diastolic_value="";
				
				$date_of_vital = (isset($vitalFieldValue["date_vital"]['date']) && empty($vitalFieldValue["date_vital"]['date']) == false) ? $vitalFieldValue["date_vital"]['date'] : '';
				$time_of_vital = (isset($vitalFieldValue["date_vital"]['time']) && empty($vitalFieldValue["date_vital"]['time']) == false) ? $vitalFieldValue["date_vital"]['time'] : '';
				
				$range_vital=$vitalFieldValue["range_vital"];
				$vital_unit=$vitalFieldValue["unit"];
				
				//$vital_unit = str_replace(array('[', ']'), array('', ''), $vital_unit);
				//$unitKey = array_search($vital_unit, $vsUnitArr);
				//$vital_sign_id = (isset($vsUnitArr[$unitKey]) && empty($vsUnitArr[$unitKey]) == false) ? $unitKey : '';
				
				$vitalSignType = $vitalFieldValue["type"]['title'];
				
				$vital_sign_id = "";
				if($vitalSignType == "Systolic Blood Pressure"){
					$BP_systolic_value=$range_vital;
					$vital_sign_id = 1;
				}else if($vitalSignType == "Diastolic Blood Pressure"){
					$BP_diastolic_value=$range_vital;
					$vital_sign_id = 2;
				}
				$qryVitalSignMaster="SELECT id FROM vital_sign_master WHERE patient_id='".$ptId."' AND date_vital='".$date_of_vital."'";
				$resVitalSignMaster=imw_query($qryVitalSignMaster);
				if(imw_num_rows($resVitalSignMaster)>0){
					$rowVitalSignMaster= imw_fetch_assoc($resVitalSignMaster);
					$vitalMasterId=$rowVitalSignMaster["id"];
				}else if(imw_num_rows($resVitalSignMaster)==0){
					$qryInsertVitalSignMaster="INSERT INTO vital_sign_master set patient_id='".$ptId."', date_vital='".$date_of_vital."', time_vital='".$time_of_vital."', created_on='".$current_date."', created_by='".$provId."', phy_reviewed ='".$provId."', phy_reviewed_date='".$date_of_vital." ".$time_of_vital."', timestamp='".$current_date."'";
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
		//Creating Interventions
		function createObservations($arrData = array(), $provId = '', $ptId = '',$enArr = array()){
			if(empty($ptId) || empty($provId)  || count($arrData) == 0 || count($enArr) == 0) return false;
			$cur_dat=date('Y-m-d H:i:s');
			$form_id=$enArr['DosStatus']['formId'];
			$counter = 0;
			foreach($arrData as $obj){
				//Encounter Start Date/Time
				$EncStDt = (isset($obj['startObj']['date']) && empty($obj['startObj']['date']) == false) ? $obj['startObj']['date'] : '';
				$EncStTm = (isset($obj['startObj']['time']) && empty($obj['startObj']['time']) == false) ? $obj['startObj']['time'] : '';
				
				//Encounter Start Date/Time
				$EncEnDt = (isset($obj['endObj']['date']) && empty($obj['endObj']['date']) == false) ? $obj['endObj']['date'] : '';
				$EncEnTm = (isset($obj['endObj']['time']) && empty($obj['endObj']['time']) == false) ? $obj['endObj']['time'] : '';
				
				$description = (isset($obj['description']['title']) && empty($obj['description']['title']) == false) ? $obj['description']['title'] : '';
				$valueSet = (isset($obj['valueSet']) && empty($obj['valueSet']) == false) ? $obj['valueSet'] : '';
				$code = (isset($obj['code']) && empty($obj['code']) == false) ? $obj['code'] : '';
				$loincCode = (isset($obj['loincCode']) && empty($obj['loincCode']) == false) ? $obj['loincCode'] : '';
				$procedure_status = (isset($obj['status']) && empty($obj['status']) == false) ? $obj['status'] : '';
				$obs_type = (isset($obj['type']) && empty($obj['type']) == false) ? $obj['type'] : '';
				$nullValue = (isset($obj['nullValue']) && empty($obj['nullValue']) == false) ? $obj['nullValue'] : '';
				
				$refusalVal = (isset($obj['refusal']) && empty($obj['refusal']) == false) ? $obj['refusal'] : '';
				$refusalCode = (isset($obj['refusalCode']) && empty($obj['refusalCode']) == false) ? $obj['refusalCode'] : '';
				
				//Inserting in DB
				//Checikng if patient Intervention exists or not already
				$checkDos = imw_query('SELECT id FROM hc_observations where type ="'.$obs_type.'" and observation="'.$description.'" and observation_date  = "'.$EncStDt.'" and observation_time = "'.$EncStTm.'" AND pt_id = "'.$ptId.'" AND status = 0');
				if(imw_num_rows($checkDos) > 0){
					$row = imw_fetch_assoc($checkDos);
					$counter++;
				}else{
					$insertDos = imw_query('INSERT INTO hc_observations SET form_id="'.$form_id.'",pt_id = "'.$ptId.'",snomed_code = "'.$loincCode.'",observation = "'.$description.'",observation_date = "'.$EncStDt.'",status = "completed",entry_date_time = "'.$cur_dat.'",operator_id = "'.$provId.'", 
					observation_time  = "'.$EncStTm.'",type  = "'.$obs_type.'", refusal = "'.$refusalVal.'", refusal_snomed = "'.$refusalCode.'"') or imw_error();
					$hc_id=imw_insert_id();
					$insertrel = imw_query('INSERT INTO hc_rel_observations SET rel_observation_date="'.$EncStDt.'",rel_observation  = "'.$description.'",snomed_code  = "'.$code.'",entry_date_time = "'.$cur_dat.'",operator_id = "'.$provId.'",observation_id  = "'.$hc_id.'", nullflavor = "'.$nullValue.'"') or imw_error();
					if($insertDos){
						$counter++;
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
				$returrnArr['Intervention'] = $this->createInterventions($arrData['Intervention'], $provId, $ptId);
				//pre($interventionStatus);exit;
			}
			
			if(isset($arrData['Procedure']) && count($arrData['Procedure']) > 0){
				$returrnArr['Procedure'] = $this->createInterventions($arrData['Procedure'], $provId, $ptId);
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
			
			foreach($arrData as $obj){
				//Getting Date/Time from XML Array
				
				$EncStDt = (isset($obj['effectiveTime']['startObj']['date']) && empty($obj['effectiveTime']['startObj']['date']) == false) ? $obj['effectiveTime']['startObj']['date'] : '';
				
				$EncStTm = (isset($obj['effectiveTime']['startObj']['time']) && empty($obj['effectiveTime']['startObj']['time']) == false) ? $obj['effectiveTime']['startObj']['time'] : '';
				
				
				$EncEndDt = (isset($obj['effectiveTime']['endObj']['date']) && empty($obj['effectiveTime']['endObj']['date']) == false) ? $obj['effectiveTime']['endObj']['date'] : '';
				$EncEndTm = (isset($obj['effectiveTime']['endObj']['time']) && empty($obj['effectiveTime']['endObj']['time']) == false) ? $obj['effectiveTime']['endObj']['time'] : '';
				
				
				$description = (isset($obj['code']['description']['title']) && empty($obj['code']['description']['title']) == false) ? $obj['code']['description']['title'] : '';
				$valueSet = (isset($obj['code']['valueSet']) && empty($obj['code']['valueSet']) == false) ? $obj['code']['valueSet'] : '';
				$code = (isset($obj['code']['code']) && empty($obj['code']['code']) == false) ? $obj['code']['code'] : '';
				$icd10Code = (isset($obj['code']['icd10']) && empty($obj['code']['icd10']) == false) ? $obj['code']['icd10'] : '';
				
				if(empty($description) == false && empty($icd10Code) == false) $description = $description.' - '.$icd10Code;
				
				//if(empty($templateId) == false){
					//Checikng if patient Intervention exists or not already
					$checkDos = imw_query('SELECT * FROM pt_problem_list where prob_type="Diagnosis" and problem_name="'.$description.'" and onset_date = "'.$EncStDt.'" and OnsetTime = "'.$EncStTm.'" AND pt_id = "'.$ptId.'" AND ccda_code = "'.$code.'"');
					if(imw_num_rows($checkDos) > 0){
						$row = imw_fetch_assoc($checkDos);
						$prob_id=$row['id'];
						
						//Updating Problem Data
						/* $updateDos = imw_query('UPDATE pt_problem_list SET user_id="'.$provId.'",problem_name="'.$description.'",
						onset_date = "'.$EncStDt.'",OnsetTime = "'.$EncStTm.'",prob_type = "Diagnosis",status="Active",ccda_code="'.$code.'" WHERE id = "'.$prob_id.'"') or imw_error();
						
						if($updateDos){
							//Inserting Previous data in log 
							$q = "INSERT INTO pt_problem_list_log 
									SET problem_id = '".$row['id']."', 
										pt_id = '".$row['pt_id']."', 
										user_id = '".$provId."', 
										problem_name = '".$row['problem_name']."',
										comments = '".$row['comments']."', 
										onset_date = '".$row['onset_date']."', 
										status = '".$row['status']."', 
										signerId = '".$row['signerId']."',
										coSignerId = '".$row['coSignerId']."', 
										OnsetTime = '".$row['OnsetTime']."',
										statusDateTime = '".date('Y-m-d H:i:s')."', 
										prob_type = '".$row['prob_type']."'
							";	
							imw_query($q);
						}*/
						$counter++;
						
					}else{
						$insertDos = imw_query('INSERT INTO pt_problem_list SET pt_id = "'.$ptId.'",user_id="'.$provId.'",problem_name="'.$description.'",
						onset_date = "'.$EncStDt.'",OnsetTime = "'.$EncStTm.'",prob_type = "Diagnosis",status="Active",ccda_code="'.$code.'", end_datetime = "'.$EncEndDt.' '.$EncEndTm.'"') or imw_error();
						if($insertDos){
							$counter++;
						}
					}
				//}
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
		
	}
?>