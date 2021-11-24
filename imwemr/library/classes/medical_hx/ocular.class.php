<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Class File Related to Ocular
 Access Type: Indirect Access.
 
*/
include_once 'medical_history.class.php';
class Ocular extends MedicalHistory
{
	public $ocular_vocabulary = false;
	public $ocular_data = false;
	public $as_exception_msg = false;
		
	public function __construct($tab = 'ocular')
	{
		parent::__construct($tab);
		$this->ocular_vocabulary = $this->get_vocabulary("medical_hx", "ocular");
		$this->ocular_data = $this->load_ocular_data();
		$this->all_script_ocular();
	}
	
	public function load_ocular_data()
	{
		$query = "select * from ocular where patient_id='".$this->patient_id."' LIMIT 1";
		$sql = imw_query($query);
		$row = imw_fetch_assoc($sql);
		
		if($row == false)
		{
			imw_query("INSERT INTO ocular SET patient_id = '".$this->patient_id."'");
		}

		$arrPtRel = get_relationship_array('general_health');
		$row['arrPtRel'] = $arrPtRel;
		$row['strPtRel'] = implode(',', $arrPtRel);
		
		//Do you wear
		if($row["you_wear"] == 1) 		$row['uwear1'] = "checked";
		elseif($row["you_wear"] == 2)	$row['uwear2'] = "checked";
		elseif($row["you_wear"] == 3)	$row['uwear3'] = "checked";
		else	$row['uwear0'] = "checked";
		
		//Last eye exam date
		if($row['last_exam_date'] != "0000-00-00" && empty($row['last_exam_date'])==false)
		{
			$row['last_eye_exam_date'] = get_date_format($row["last_exam_date"]);
		}
		else
		{
			$row['last_eye_exam_date'] = get_date_format(date("Y-m-d"));
		}
		
		//Eye Problems - Please check any of the problems you have 
		//$eye_problems_arr = explode(",",$row["eye_problems"]);
		//for($epr = 1; $epr < count($eye_problems_arr)-1; $epr++){
		//	$row['eye_p'][$eye_problems_arr[$epr]]="checked";
		//}
		
		//Please mark any condition you or blood relative have presently or have had in the past
		$strAnyConditionsYou = $row["any_conditions_you"];
		$strAnyConditionsYou = get_set_pat_rel_values_retrive($strAnyConditionsYou,"pat",$this->delimiter);
		$any_conditions_you_arr = explode(",",$strAnyConditionsYou);
		for($epr1 = 1; $epr1 < count($any_conditions_you_arr)-1; $epr1++){
			$row['acya_p'][$any_conditions_you_arr[$epr1]]="checked";
		}
		
		$row['aco_u_checked'] = ($row["any_conditions_others_you"] == 1) ? "checked" : ''; 
		
		//Other Value (checking if string seprator present then extracting value for patient)-----
		$OtherDesc = $row["OtherDesc"];
		$OtherDesc = get_set_pat_rel_values_retrive($OtherDesc,"pat",$this->delimiter);
		
		/*----separating chronicDesc of patient and relative--*/
		$strSep="~!!~~";
		$strSep2=":*:";
		$strDesc = $row["chronicDesc"];
		$strDesc = get_set_pat_rel_values_retrive($strDesc,"pat",$this->delimiter);
		if(!empty($strDesc))
		{
			$arrDescTmp = explode($strSep, $strDesc);
			if(count($arrDescTmp) > 0)
			{
				foreach($arrDescTmp as $key => $val)
				{
					$arrTmp = explode($strSep2,$val);
					$fId = "elem_chronicDesc_".$arrTmp[0];
					$row[$fId] = $arrTmp[1];
				}
			}
		}
		
		/*---separation end----------*/
		
		//setting form elements
		$any_conditions_relative_arr = explode(",",$row["any_conditions_relative"]);
		for($epr2 = 1; $epr2 < count($any_conditions_relative_arr)-1; $epr2++){
			$row['acra_p'][$any_conditions_relative_arr[$epr2]]="checked";
		}
		
		//getting value of other checkbox
		if($row["any_conditions_other_relative"] == 1)
		{
			$row['aco_relative_checked'] = "checked";
		}
		
		//Other Descirption (checking if string seprator present then extracting value for patient)-----
		$row['OtherRelDesc'] = get_set_pat_rel_values_retrive($row["OtherDesc"],"rel",$this->delimiter);
		
		
		/*----separating chronicDesc of relative--*/
		$strSep="~!!~~";
		$strSep2=":*:";
		$strDesc = $row["chronicDesc"];
		$strDesc = get_set_pat_rel_values_retrive($strDesc,"rel",$this->delimiter);
		if(!empty($strDesc))
		{
			$arrDescTmp = explode($strSep, $strDesc);
			if(count($arrDescTmp) > 0)
			{
				foreach($arrDescTmp as $key => $val)
				{
					$arrTmp = explode($strSep2,$val);
					$fId = "rel_elem_chronicDesc_".$arrTmp[0];
					$row[$fId] = $arrTmp[1];
				}
			}
		}
		
		/*----separating chronicDesc of relative--*/
		$strRelative = $row["chronicRelative"];
		if( !empty($strRelative) )
		{
			$arrRelTmp = explode($strSep, $strRelative);
			if( count($arrRelTmp) > 0 )
			{
				foreach( $arrRelTmp as $key => $val )
				{
					$arrTmp = explode($strSep2, $val);
					if(trim($arrTmp[0]) == '' ) continue; 
					$fId = "elem_chronicRelative_".$arrTmp[0];
					$fId2= "other_elem_chronicRelative_".$arrTmp[0];
					$row[$fId] = $arrTmp[1];
					$row[$fId2] = $this->get_other_field_val($arrTmp[1],$arrPtRel);
					
				}
			}
		}

		// SET STRING FOR JAVASCRIPT CALL
		$row['strAllTxtRelValues']	= $elem_chronicRelative_1.'~'.$elem_chronicRelative_2.'~'.$elem_chronicRelative_3.'~'.
																	$elem_chronicRelative_4.'~'.$elem_chronicRelative_5.'~'.$elem_chronicRelative_6;
		
		
		return $row;
		
	}

	public function getXmlTAgValue($xml, $tag)
	{
		if( !is_object($xml) ) return '';
		
		$rtData = '';
		$xmlTag =  $xml->getElementsByTagName($tag);
		if( $xmlTag->length == 1 )
		{
			$xmlTag = $xmlTag->item(0);
			$rtData = trim( $xmlTag->getAttribute('value') );
		}
		return $rtData;
	}
	
	public function stripDualSpace($string)
	{
		do
		{
			$loop = true;
			$string = str_replace('  ', ' ', $string);
			if( strpos($string, '  ') === false)
				$loop = false;
		} while($loop);
		return $string;
	}
	
	public function all_script_ocular()
	{ 
		if( is_allscripts() && !isset($_SESSION['asMedicalHistorySync'])  && $this->patient_id !== '' )
		{
			$GLOBALS['rethrow'] = true;
			include_once( $GLOBALS['srcdir'].'/allscripts/as_patient.php' );
			
			/*List existing AS data*/
			$sqlList = "SELECT GROUP_CONCAT( `as_id` ) AS 'as_ids' , `type` FROM `lists` WHERE `pid` = ".( (int) $this->patient_id )." GROUP BY `type`";
			$respList = imw_query($sqlList);

			$asExisgingData = array(1=>array(), 7=>array());
			if( $respList && imw_num_rows($respList) > 0 )
			{
				while( $listRow = imw_fetch_assoc($respList) )
				{
					$asExisgingData[(int)$listRow['type']] = explode(',', $listRow['as_ids']);
					$asExisgingData[(int)$listRow['type']] = array_flip($asExisgingData[(int)$listRow['type']]);
				}
			}
			/*End List existing AS data*/

			try
			{
				$asPtId = false;
				$asIdSql = "SELECT `as_id` FROM `patient_data` WHERE `id`=".( (int) $this->patient_id )." AND `External_MRN_4`!=''";
				$asIdSql = imw_query( $asIdSql );
				if( $asIdSql && imw_num_rows($asIdSql) > 0 )
				{
					$asPtId = imw_fetch_assoc( $asIdSql );
					$asPtId = $asPtId['as_id'];
				}
				
				if( $asPtId )
				{
					/*Fetch Data from AllScripts*/
					$patientObj = new as_patient();
					$clinicalSummary = $patientObj->clinicalSummary($asPtId);
					
					//$clinicalSummary = $patientObj->clinicalSummary('39');
					//print_r($clinicalSummary);
					//print "\n\n*****************************************************\n\n";
					
					$ptMedHxData = array('problems'=>array(), 'cc_hx'=>array(), 'medications'=>array(), 'allergies'=>array(), 'history'=>array() );
					
					/*Group the Data in categories*/
					foreach( $clinicalSummary as $summary )
					{
						$summary->section = strtolower( trim($summary->section) );
						$summary->section = str_replace(' ', '_', $summary->section);
						
						if( in_array( $summary->section, array('problems', 'past_medical_history') ) )
						{
							
							$data = array();
							$data['asId'] =  trim( $summary->transid) ;
							$data['name'] =  trim( $summary->detail );
							
							$xmlData = trim( $summary->XMLDetail );
							
							$dom = new DOMDocument();
							$dom->loadXML( $xmlData );
							
							$data['status'] = $this->getXmlTAgValue($dom, 'status');
							
							$allowedStatus = array('active'=>'Active', 'inactive'=>'Inactive', 'resolved'=>'Resolved', 'reviewed'=>'Active');
							$data['asId'] =  trim( $summary->transid );
							$data['status'] =  trim( $summary->status );
							if( !isset( $allowedStatus[ strtolower($data['status']) ] ) )
							{
								if( $summary->section === 'past_medical_history' )
								{
									continue;
								}
								$data['status'] = 'Active';
							}
							else
								$data['status'] = $allowedStatus[ strtolower($data['status']) ];
							
							$data['snomed'] = $this->getXmlTAgValue($dom, 'Snomed');
							$data['saveDate'] = $this->getXmlTAgValue($dom, 'recorded');
							$data['saveDate'] = $this->stripDualSpace($data['saveDate']);
							$data['saveDate'] = strtotime($data['saveDate']);
							
							$data['modifyDate'] = $this->getXmlTAgValue($dom, 'lastedited');
							$data['modifyDate'] = $this->stripDualSpace($data['modifyDate']);
							$data['modifyDate'] = strtotime($data['modifyDate']);
							
							$additionalData = array();
							$additionalData['icd9'] = $this->getXmlTAgValue($dom, 'ICD9');
							$additionalData['icd10'] = $this->getXmlTAgValue($dom, 'ICD10');
							$additionalData['code'] = trim( $summary->code );
							$additionalData['entryCode'] = trim( $summary->entrycode );
							$additionalData['description'] = $this->getXmlTAgValue($dom, 'description');
							$additionalData['displayDate'] = $this->getXmlTAgValue($dom, 'displaydate');
							$additionalData['identifiedby'] = $this->getXmlTAgValue($dom, 'identifiedby');
							$data['additionalData'] = json_encode($additionalData);
							
							array_push( $ptMedHxData['problems'], $data );
						}
						elseif( $summary->section === 'allergies' )
						{
							$data = array();
							$data['asId']	=  trim( $summary->transid) ;
							$data['name']	=  trim( $summary->description );
							$data['reaction'] = '';
							if( $summary->detail != '')
							{
								$data['reaction'] = preg_replace('/[^\w, ]/', '', $summary->detail);
								$data['reaction'] = trim($data['reaction']);
							}
							
							$allergyStat	=  strtolower(trim( $summary->status ));
							
							/*Mapped allery Statuses*/
							$allowedStatus = array('active'=>'Active', 'inactive'=>'Aborted', 'resolved'=>'Aborted', 'reviewed'=>'Active', 'noknown'=>'Active');
							$data['asId'] =  trim( $summary->transid );
							
							if( array_key_exists($allergyStat, $allowedStatus) )
							{
								$data['status'] = $allowedStatus[$allergyStat];
							}
							else
								continue;	/*Ignore allergy entry if unable to map with any of the mapped statuses*/
							
							// if( !isset( $allowedStatus[ strtolower($data['status']) ] ) )
							// 	$data['status'] = 'Active';
							// else
							// 	$data['status'] = $allowedStatus[ strtolower($data['status']) ];
							
							$xmlData = trim( $summary->XMLDetail );
							$dom = new DOMDocument();
							$dom->loadXML( $xmlData );
							
							$data['beginDate']	= $this->getXmlTAgValue($dom, 'recordedon');
							$data['beginDate'] = $this->stripDualSpace($data['beginDate']);
							$data['beginDate'] = strtotime($data['beginDate']);
							
							$data['snomed']		= $this->getXmlTAgValue($dom, 'snomed');
							$data['as_code']	= $summary->code;
							
							$additionalData = array();
							$additionalData['code'] = trim( $summary->code );
							$additionalData['entryCode'] = trim( $summary->entrycode );
							$additionalData['uniicode'] = $this->getXmlTAgValue($dom, 'uniicode');
							$additionalData['recordedby'] = $this->getXmlTAgValue($dom, 'recordedby');
							$data['additionalData'] = json_encode($additionalData);
							
							array_push( $ptMedHxData['allergies'], $data );
						}
						elseif( $summary->section === 'medications' )
						{
							$data = array();
							$allowedStatus = array(
								'Active'=>'Active', 
								'Complete'=>'Stop', 
								'Discontinued'=>'Discontinue',
								'PermanentDeferral'=>'Stop',
								'TempDeferral'=>'Stop',
								'TemporaryDeferral'=>'Stop'
							);
							
							$data['asId'] =  trim( $summary->transid );
							$medStat =  str_replace(' ','',trim( $summary->status ));
							//$data['status'] =  trim( $summary->status );
							
							//Checking Med Status
							foreach($allowedStatus as $twStat => $iDocStat){
								$pos = strpos(strtolower(trim($medStat)), strtolower(trim($twStat)));
								
								if ($pos !== false) {
									$data['status'] = $iDocStat;
									break;
								}
							}
							
							if( !isset($data['status']) ) continue;		//If Status is set according to iDoc Status only then execute further else skip 
							/* $data['status'] = 'Active';
									else
								$data['status'] = $allowedStatus[ $data['status'] ]; 
							*/
							
							$data['comments']	= trim( $summary->detail );
							
							$xmlData = trim( $summary->XMLDetail );
							$dom = new DOMDocument();
							$dom->loadXML( $xmlData );
							
							$data['beginDate']	= $this->getXmlTAgValue($dom, 'startdate');
							$data['beginDate'] = $this->stripDualSpace($data['beginDate']);
							$data['beginDate'] = strtotime($data['beginDate']);
							
							$data['name']	= $this->getXmlTAgValue($dom, 'medication');
							$data['rxNorm']	= $this->getXmlTAgValue($dom, 'rxnormcode');
							$data['sig']	= $this->getXmlTAgValue($dom, 'sig');

							$data['dose'] = trim( $this->getXmlTAgValue($dom, 'dose') );
							if( $data['dose'] != '' )
							{
								$tempForm = trim( $this->getXmlTAgValue($dom, 'form') );
								if( $tempForm != '' )
									$data['dose'] .= ' ' . $tempForm;
								unset($tempForm);
							}
							//$data['dose']	= $this->getXmlTAgValue($dom, 'dosefrequency');
							
							$additionalData = array();
							$additionalData['code'] = trim( $summary->code );
							$additionalData['entryCode'] = trim( $summary->entrycode );
							$additionalData['prescribedby'] = $this->getXmlTAgValue($dom, 'prescribedby');
							$additionalData['ndc'] = $this->getXmlTAgValue($dom, 'NDC');
							$data['additionalData'] = json_encode($additionalData);
							
							array_push( $ptMedHxData['medications'], $data );
						}
						elseif( $summary->section === 'history' )
						{
							$historyData = array(
								'status'	=> trim( $summary->status ),
								'comments'	=> trim( $summary->detail )
							);
							array_push( $ptMedHxData['history'], $historyData );
						}
					}
					/*End Group the Data in categories*/
					
					/*Escape Characters for DB insertion*/
					array_walk_recursive($ptMedHxData, function(&$val){$val = imw_real_escape_string($val);});
					
					/*Save Data to IMW DB*/
					foreach( $ptMedHxData as $key=>$data)
					{
						/*Continue to next iteration if values for the option does not exitst*/
						if( count($data) == 0 )
							continue;
						
						/*Save Problems Data*/
						if( $key === 'problems' )
						{
							foreach( $data as $vals )
							{
								/*Check if Problems for the patient with same Allscripts transid already exists in imwemr*/
								$existingData = array();
								$sqlCheck = "SELECT `id`, `status` 
											FROM `pt_problem_list` 
											WHERE 
												`pt_id`=".( (int)$this->patient_id )." AND 
												`as_id` LIKE '%".$vals['asId']."%' AND 
												`status` != 'Deleted'";
								$sqlCheck = imw_query($sqlCheck);
								if( $sqlCheck && imw_num_rows($sqlCheck) > 0 )
									$existingData = imw_fetch_assoc($sqlCheck);
								/*End Check if Problems for the patient with same Allscripts transid already exists in imwemr*/
								
								$onsetDate = ( $vals['saveDate'] !== '' ) ? date('Y-m-d', $vals['saveDate']) :  ( $vals['modifyDate'] !== '' ) ? date('Y-m-d', $vals['modifyDate']) : '0000-00-00';
								$onsetTime = ( $vals['saveDate'] !== '' ) ? date('H:i:s', $vals['saveDate']) : ( $vals['modifyDate'] !== '' ) ? date('H:i:s', $vals['modifyDate']): '00:00:00';
								
								if( count($existingData) > 0 && $existingData['status'] !== $vals['status'] )
								{
									/*Update if Status no same as existing*/
									$sqPU = "UPDATE `pt_problem_list` SET `status`='".$vals['status']."' WHERE `id`=".$existingData['id'];
									if( imw_query($sqPU) )
									{
									$sqlLog = "INSERT INTO `pt_problem_list_log` SET
														`problem_id`='".$existingData['id']."',
														`pt_id`=".( (int)$this->patient_id ).",
														`user_id`=".( (int)$_SESSION['authUserID'] ).",
														`problem_name`='".$vals['name']."',
														`onset_date`='".$onsetDate."',
														`status`='".$vals['status']."',
														`OnsetTime`='".$onsetTime."',
														`ccda_code`='".$vals['snomed']."'
														";
										imw_query($sqlLog);
									}
								}
								elseif( count($existingData) === 0 )
								{
									$sqlPI = "INSERT INTO `pt_problem_list` SET
												`pt_id`=".( (int)$this->patient_id ).",
												`user_id`=".( (int)$_SESSION['authUserID'] ).",
												`problem_name`='".$vals['name']."',
												`onset_date`='".$onsetDate."',
												`status`='".$vals['status']."',
												`OnsetTime`='".$onsetTime."',
												`ccda_code`='".$vals['snomed']."',
												`as_id`='".$vals['asId']."',
												`as_data`='".$vals['additionalData']."'
												";
									
									if( imw_query($sqlPI) )
									{
										$problemId = imw_insert_id();
										$sqlLog = "INSERT INTO `pt_problem_list_log` SET
															`problem_id`='".$problemId."',
															`pt_id`=".( (int)$this->patient_id ).",
															`user_id`=".( (int)$_SESSION['authUserID'] ).",
															`problem_name`='".$vals['name']."',
															`onset_date`='".$onsetDate."',
															`status`='".$vals['status']."',
															`OnsetTime`='".$onsetTime."',
															`ccda_code`='".$vals['snomed']."'
															";
										imw_query($sqlLog);
									}
								}
							}
							
						}
						/*End Save Problems Data*/
						/*Save Allergies Data*/
						elseif( $key === 'allergies' )
						{
							include_once( $GLOBALS['srcdir'].'/allscripts/as_dataValues.php' );
							
							$noKnownWatch = array('med'=>0, 'nonMed'=>0, 'nkda'=>0, 'nka'=>0);

							foreach( $data as $vals )
							{
								/*IMW Data*/
								$allergyType = false;
								$allergyId = false;
								
								/*Check if allergy record already exists in imwDatabase. Else Add new*/
								$imwData = array();
								$sqlCheck = "SELECT `allergies_id`, as_type FROM `allergies_data` WHERE LOWER(`allergie_name`)='".strtolower($vals['name'])."'";

								$sqlCheck = imw_query( $sqlCheck );
								if( $sqlCheck && imw_num_rows($sqlCheck) > 0 )
								{
									$imwData = imw_fetch_assoc($sqlCheck);
									imw_free_result($sqlCheck);
								}
								
								if( count($imwData) > 0 )
								{
									$allergyType = ($imwData['as_type']!=='')?$imwData['as_type']:false;
									$allergyId = $imwData['allergies_id'];
								}
								
								/*Get Data from Unity API if not exists in IMW*/
								if( $allergyType === false )
								{
									$dataValues = new as_dataValues();
									$allergyData = $dataValues->query( $vals['name'], 'allergies', $asPtId );
									
									$sqlAllergy = '';
									$sqlAllergyWhere = '';
									$qryFlag = true;
									if( $allergyId === false )
										$sqlAllergy = "INSERT INTO `allergies_data` SET `allergie_name`='".$vals['name']."', ";
									else{
										$sqlAllergy = "UPDATE `allergies_data` SET ";
										$sqlAllergyWhere = 'WHERE `allergies_id`='.$allergyId;
										$qryFlag = false;
									}
									
									if( isset( $allergyData[$vals['as_code']] ) )
									{
										$sqlAllergy .= "`as_id`='".$vals['as_code']."', `as_type`='".$allergyData[$vals['as_code']]['type']."' ";
										$allergyType = $allergyData[$vals['as_code']]['type'];
										$qryFlag = true;
									}
									$sqlAllergy .= $sqlAllergyWhere;
									
									if( $qryFlag )
									{
										$sqlAllergy = trim($sqlAllergy);
										$sqlAllergy = rtrim($sqlAllergy, ',');
										imw_query($sqlAllergy);
									}
								}
								/*End Get Data from Unity API if not exists in IMW*/
								/*End Check if allerfy record already exists in imwDatabase. Else Add new*/
								
								/*Add/Update Allergy for the Patient*/
								$existingData = array();
								/*AND allergy_status != 'Deleted'*/
								$sqlCheck = "SELECT `id` FROM `lists` WHERE `type`=7 AND `as_id`='".$vals['asId']."'";
								$sqlCheck = imw_query($sqlCheck);
								if( $sqlCheck && imw_num_rows($sqlCheck) > 0 )
									$existingData = imw_fetch_assoc($sqlCheck);
								/*End Check if Allergy for the patient with same Allscripts transid already exists in imwemr*/
								// && $existingData['allergy_status'] !== $vals['status'] 
								
								$ag_occular_drug = ($allergyType === 'MED') ? 'fdbATDrugName' : 'fdbATAllergenGroup';

								if( count($existingData) > 0 )
								{
									/*Update if Status not same as existing*/
									$sqPU = "UPDATE `lists`
											SET 
												`date`='".date('Y-m-d H:i:s')."',
												`title`='".$vals['name']."',
												`begdate`='".date('Y-m-d', $vals['beginDate'])."',
												`pid`=".( (int)$this->patient_id ).",
												`user`=".( (int)$_SESSION['authUserID'] ).",
												`ag_occular_drug`='".imw_real_escape_string($ag_occular_drug)."',
												`allergy_status`='".imw_real_escape_string($vals['status'])."',
												`ccda_code`='".imw_real_escape_string($vals['snomed'])."',
												`reactions`='".imw_real_escape_string($vals['reaction'])."',
												`as_data`='".imw_real_escape_string($vals['additionalData'])."'
											WHERE
												`id`=".$existingData['id'];
									imw_query($sqPU);
								}
								elseif( count($existingData) === 0 )
								{
									
									$sqlPI = "INSERT INTO `lists` SET
												`date`='".date('Y-m-d H:i:s')."',
												`type`=7,
												`title`='".$vals['name']."',
												`begdate`='".date('Y-m-d', $vals['beginDate'])."',
												`pid`=".( (int)$this->patient_id ).",
												`user`=".( (int)$_SESSION['authUserID'] ).",
												`ag_occular_drug`='".imw_real_escape_string($ag_occular_drug)."',
												`allergy_status`='".imw_real_escape_string($vals['status'])."',
												`ccda_code`='".imw_real_escape_string($vals['snomed'])."',
												`reactions`='".imw_real_escape_string($vals['reaction'])."',
												`as_id`='".$vals['asId']."',
												`as_data`='".imw_real_escape_string($vals['additionalData'])."'
												";
									imw_query($sqlPI);
									$existingData['id'] = imw_insert_id();
								}
								/*End Add/Update Allergy for the Patient*/

								/*Array to be used for deactivating No Known Allergies*/
								$allergyString = strtolower(trim($vals['name']));

								if( $allergyString === 'no known drug allergies' )
									$noKnownWatch['nkda'] = $existingData['id'];
								elseif( $allergyString === 'no known allergies' )
									$noKnownWatch['nka'] = $existingData['id'];
								elseif( $allergyType === 'MED' && $vals['status'] === 'Active' )
									$noKnownWatch['med']++;
								elseif( $vals['status'] === 'Active' )
									$noKnownWatch['nonMed']++;
								
								if( isset($asExisgingData[7][$vals['asId']]) )
									unset($asExisgingData[7][$vals['asId']]);
							}

							/*Mark No know Allergies deleted*/
							$delNk = array();
							if( $noKnownWatch['med'] > 0 )
							{
								array_push($delNk, $noKnownWatch['nka'], $noKnownWatch['nkda']);
							}
							elseif( $noKnownWatch['nonMed'] > 0 )
							{
								array_push($delNk, $noKnownWatch['nka']);
							}

							$delNk = array_unique($delNk);
							$delNk = array_filter($delNk);

							if( count($delNk) > 0 )
							{
								$sqlDel = "UPDATE `lists` SET `allergy_status` = 'Aborted' WHERE `id` IN(".implode(',', $delNk).")";
								imw_query($sqlDel);

								unset($sqlDel, $delNk);
							}
						}
						/*End Save Allergies Data*/
						/*Save Medications*/
						elseif( $key === 'medications' )
						{
							foreach( $data as $vals )
							{
								$existingData = array();
								$sqlCheck = "SELECT `id`, `allergy_status` FROM `lists` WHERE `type` IN(1, 4) AND `as_id`='".$vals['asId']."'";
                                                                
                                                                $sqlCheck = imw_query($sqlCheck);
								if( $sqlCheck && imw_num_rows($sqlCheck) > 0 )
									$existingData = imw_fetch_assoc($sqlCheck);
								/*End Check if Medication for the patient with same Allscripts transid already exists in imwemr*/
								
								$medWhere = '';
								$sqMed = '';
								if( count($existingData) > 0  )
								{
									/*Update if Status not same as existing*/
									$sqMed = "UPDATE `lists` SET `allergy_status`='".$vals['status']."', ";
									$medWhere =  " WHERE `id`=".$existingData['id'];
								}
								elseif( count($existingData) === 0 )
								{
									$sqMed = "INSERT INTO `lists` SET 	`date`='".date('Y-m-d H:i:s')."',
												`type`=1,
												`title`='".$vals['name']."',
												`pid`=".( (int)$this->patient_id ).",
												`user`=".( (int)$_SESSION['authUserID'] ).",
												`allergy_status`='".$vals['status']."',
												`med_comments`='".imw_real_escape_string($vals['comments'])."',
												";
								}
								
								$sqMed .= "`destination`='".$vals['dose']."',
												`begdate`='".date('Y-m-d', ($vals['beginDate']))."',
												`ccda_code`='".$vals['rxNorm']."',
												`sig`='".$vals['sig']."',
												`as_id`='".$vals['asId']."',
												`as_data`='".$vals['additionalData']."'".$medWhere;
								
                                                                imw_query($sqMed);
                                                                
								if( isset($asExisgingData[1][$vals['asId']]) )
                                                                    unset($asExisgingData[1][$vals['asId']]);
                                                                elseif( isset($asExisgingData[4][$vals['asId']]) )
									unset($asExisgingData[4][$vals['asId']]);
							}
						}
						/*End Save Medications*/
						/*Save patient history (as more information in social history)*/
						elseif( $key === 'history' )
						{
							$temp = array();
							for ($i=0; $i < sizeof($data); $i++) { 
							    $temp[$i] = $data[$i]['status'];
							}
							$status = array_unique($temp);

							$comments = '-- HISTORY RECORDS --\r\n';
							foreach ($status as $key => $value) {
							    $comments .= '---'.ucwords($value).'---\r\n';

							    foreach ($data as $key => $val) {
							        if($value == $val['status']) {
							            $comments .= $val['comments'].'\r\n';
							        }
							    } 
							}

							$existingData = array();
							$patient_id = $this->patient_id;
							$sqlQuery = "SELECT social_id  FROM social_history WHERE patient_id = '".$patient_id."' ORDER BY social_id DESC LIMIT 1";
							$sqlCheck = imw_query($sqlQuery);
							$existingData = imw_fetch_assoc($sqlCheck);

							$historyQuery = '';
							if( $sqlCheck && imw_num_rows($sqlCheck) > 0 ) {
								$historyQuery = "UPDATE social_history SET otherSocial = '".$comments."' WHERE social_id = ".$existingData['social_id']."";
							} else {
								$historyQuery = "INSERT INTO social_history SET patient_id = ".$patient_id.", otherSocial = '".$comments."'";
							}
							$finalHistoryQuery = $historyQuery;

							if(!empty($finalHistoryQuery))
								imw_query($historyQuery);
						}
						/*End Save History*/
					}

					/*Delete records which are not retrived in recent API call*/
					$log_file_name=data_path().'log_all_script_delete_qry.log';
					foreach( $asExisgingData as $keyExisting=>$valsExisting )
					{
						switch($keyExisting)
						{
                            case 1 :
                            case 4 :
                            case 7 :
                                $asIds = array_keys($valsExisting);
                                $asIds = array_filter($asIds);
                                
                                if( $asIds && count($asIds) > 0  )
                				{
									$sqlDel = "UPDATE `lists` SET `allergy_status` = 'Deleted' WHERE `as_id` IN('".implode("', '", $asIds)."') AND type=".(int)$keyExisting;
									imw_query($sqlDel);

									$c = imw_affected_rows();
                                    $msg = date('Y-m-d H:i:s').': '.$c. ' Records affected by query- '.$sqlDel;
                                    file_put_contents($log_file_name, $msg.PHP_EOL,FILE_APPEND);
                                    unset($sqlDel);
								}
							break;
						}
					}
				}
			}
			catch( asException $e)
			{
				$this->as_exception_msg= $e->getErrorText();
			}
			
			/*Unset the object*/
			if( isset($patientObj) && is_object($patientObj) )
				unset($patientObj);
		}
		
	}
	
}


?>